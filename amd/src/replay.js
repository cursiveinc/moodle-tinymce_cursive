export default class Replay {
    constructor(elementId, filePath, speed = 1, loop = false, controllerId) {
        console.log(filePath,elementId,controllerId);
        this.replayInProgress = false;
        this.speed = speed;
        this.loop = loop;
        const element = document.getElementById(elementId);
        if (element) {
            this.outputElement = element;
        } else {
            throw new Error(`Element with id '${elementId}' not found`);
        }
        if (controllerId) {
            console.log("made it here");
            this.constructController(controllerId);
        }
        this.loadJSON(filePath)
            .then((data) => {
                this.logData = data;
                // support for Cursive Recorder extension files (and outdated Curisve file formats)
                // logData should be a list of dictionaries for this to work properly
                if ("data" in this.logData) {
                    this.logData = this.logData['data'];
                }
                ;
                if ("payload" in this.logData) {
                    this.logData = this.logData['payload'];
                }
                ;
                this.startReplay();
            })
            .catch(error => {
                throw new Error('Error loading JSON file: ' + error.message);
            });
    }

    constructController(controllerId) {
        const controller = document.getElementById(controllerId);
        console.log(controller);
        if (controller) {
            // this.buttonElement = document.createElement('button');
            // this.buttonElement.id = 'playerButton';
            // this.buttonElement.textContent = 'Play';
            this.scrubberElement = document.createElement('input');
            this.scrubberElement.type = 'range';
            this.scrubberElement.id = 'timelineScrubber';
            this.scrubberElement.min = '0';
            this.scrubberElement.max = '100';
            this.scrubberElement.addEventListener('input', () => {
                const scrubberValue = this.scrubberElement.value;
                this.skipToTime(scrubberValue);
            });
            controller.appendChild(this.scrubberElement);
        }
    }

    setScrubberVal(value) {
        if (this.scrubberElement) {
            this.scrubberElement.value = String(value);
        }
    }

    loadJSON(filePath) {
        return fetch(filePath)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch JSON file');
                }
                if (response.headers.get('content-length') === '0') {
                    throw new Error('Empty JSON response');
                }
                let response_json = response.json();
                return response_json;
            })
            .catch(error => {
                throw new Error('Error loading JSON file: ' + error.message);
            });
    }

    // call this to make a "start" or "start over" function
    startReplay() {
        // clear previous instances of timeout to prevent multiple running at once
        if (this.replayInProgress) {
            clearTimeout(this.replayTimeout);
        };
        this.replayInProgress = true;
        this.outputElement.innerHTML = '';
        this.replayLog();
    }

    // called by startReplay() to recursively call through keydown events
    replayLog() {
        let textOutput = "";
        let index = 0;
        const processEvent = () => {
            console.log(11);
            if (this.replayInProgress) {
                if (index < this.logData.length) {
                    let event = this.logData[index++];
                    if (event.event.toLowerCase() === 'keydown') { // can sometimes be keydown or keyDown
                        textOutput = this.applyKey(event.key, textOutput);
                    }
                    this.outputElement.innerHTML = textOutput;
                    this.setScrubberVal(index / this.logData.length * 100);
                    this.replayTimeout = setTimeout(processEvent, 1 / this.speed * 100);
                } else {
                    this.replayInProgress = false;
                    if (this.loop) {
                        this.startReplay();
                    }
                    ;
                }
            }
        };
        processEvent();
    }

    skipToEnd() {
        if (this.replayInProgress) {
            this.replayInProgress = false;
        }
        let textOutput = "";
        this.logData.forEach(event => {
            if (event.event.toLowerCase() === 'keydown') {
                textOutput = this.applyKey(event.key, textOutput);
            }
        });
        this.outputElement.innerHTML = textOutput.slice(0, -1);
        this.setScrubberVal(100);
    }

    // used by the scrubber to skip to a certain percentage of data
    skipToTime(percentage) {
        if (this.replayInProgress) {
            this.replayInProgress = false;
        }
        // only go through certain % of log data
        let textOutput = "";
        const numElementsToProcess = Math.ceil(this.logData.length * percentage / 100);
        for (let i = 0; i < numElementsToProcess; i++) {
            const event = this.logData[i];
            if (event.event.toLowerCase() === 'keydown') {
                textOutput = this.applyKey(event.key, textOutput);
            }
        }
        this.outputElement.innerHTML = textOutput.slice(0, -1);
        this.setScrubberVal(percentage);
    }

    // used in various places to add a keydown, backspace, etc. to the output
    applyKey(key, textOutput) {
        switch (key) {
            case "Enter":
                return textOutput + "\n";
            case "Backspace":
                return textOutput.slice(0, -1);
            case "ControlBackspace":
                let lastSpace = textOutput.lastIndexOf(' ');
                return textOutput.slice(0, lastSpace);
            default:
                return !["Shift", "Ctrl", "Alt", "ArrowDown", "ArrowUp", "Control", "ArrowRight", "ArrowLeft"].includes(key) ? textOutput + key : textOutput;
        }
    }
}
