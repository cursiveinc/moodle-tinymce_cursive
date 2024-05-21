export default class Replay {
    constructor(elementId, filePath, speed = 1, loop = false, controllerId) {
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
            this.constructController(controllerId);
        }

        this.loadJSON(filePath)
            .then((data) => {
                this.logData = this.parseLogData(data);
                this.startReplay();
            })
            .catch(error => {
                console.error('Error loading JSON file:', error.message);
            });
    }

    // Constructs the controller UI
    constructController(controllerId) {
        const controller = document.getElementById(controllerId);
        if (controller) {
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
        } else {
            console.error(`Controller element with id '${controllerId}' not found`);
        }
    }

    // Sets the scrubber value
    setScrubberVal(value) {
        if (this.scrubberElement) {
            this.scrubberElement.value = String(value);
        }
    }

    // Loads JSON from the provided file path
    async loadJSON(filePath) {
        const response = await fetch(filePath);
        if (!response.ok) {
            throw new Error('Failed to fetch JSON file');
        }
        const data = await response.json();
        if (!data || Object.keys(data).length === 0) {
            throw new Error('Empty JSON response');
        }
        return data;
    }

    // Parses the log data to handle different formats
    parseLogData(data) {
        if ("data" in data) {
            return data['data'];
        }
        if ("payload" in data) {
            return data['payload'];
        }
        return data;
    }

    // Starts or restarts the replay
    startReplay() {
        if (this.replayInProgress) {
            clearTimeout(this.replayTimeout);
        }
        this.replayInProgress = true;
        this.outputElement.innerHTML = '';
        this.replayLog();
    }

    // Processes each log event for replay
    replayLog() {
        let textOutput = "";
        let index = 0;
        const processEvent = () => {
            if (this.replayInProgress) {
                if (index < this.logData.length) {
                    const event = this.logData[index++];
                    if (event.event.toLowerCase() === 'keydown') {
                        textOutput = this.applyKey(event.key, textOutput);
                    }
                    this.outputElement.innerHTML = textOutput;
                    this.setScrubberVal(index / this.logData.length * 100);
                    this.replayTimeout = setTimeout(processEvent, 1000 / this.speed);
                } else {
                    this.replayInProgress = false;
                    if (this.loop) {
                        this.startReplay();
                    }
                }
            }
        };
        processEvent();
    }

    // Skips to the end of the replay
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

    // Skips to a specific time in the replay
    skipToTime(percentage) {
        percentage = Math.min(Math.max(percentage, 0), 100);
        if (this.replayInProgress) {
            this.replayInProgress = false;
        }
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

    stopReplay() {
        if (this.replayInProgress) {
            clearTimeout(this.replayTimeout);
            this.replayInProgress = false;
        }
    }

    // Applies a key event to the text output
    applyKey(key, textOutput) {
        switch (key) {
            case "Enter":
                return textOutput + "\n";
            case "Backspace":
                return textOutput.slice(0, -1);
            case "ControlBackspace":
                const lastSpace = textOutput.lastIndexOf(' ');
                return textOutput.slice(0, lastSpace);
            default:
                return !["Shift", "Ctrl", "Alt", "ArrowDown", "ArrowUp", "Control", "ArrowRight", "ArrowLeft"].includes(key) ? textOutput + key : textOutput;
        }
    }
}
