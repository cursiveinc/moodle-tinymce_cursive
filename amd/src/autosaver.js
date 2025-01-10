// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @module     tiny_cursive/autosaver
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author Brain Station 23 <elearning@brainstation-23.com>
 */

import { call } from 'core/ajax';
import { create } from 'core/modal_factory';
import { get_string as getString } from 'core/str';
import { save, cancel, hidden } from 'core/modal_events';

export const register = (editor, interval, userId) => {

    var is_student, intervention, quizSubmit, assignSubmit;

    quizSubmit = document.querySelector('#responseform');
    assignSubmit = document.querySelector('#id_submitbutton');

    var bodyElement = document.querySelector('#body');
    if (bodyElement) {
        is_student = !bodyElement.classList.contains('teacher_admin'); // true or false
        intervention = bodyElement.classList.contains('intervention'); // true or false
    } else {
        console.error('#body element not found');
    }


    var userid = userId;
    var host = M.cfg.wwwroot;
    var courseid = M.cfg.courseId;
    var filename = "";
    var ed = "";
    var event = "";
    var recourceId = 0;
    var modulename = "";
    var editorid = editor?.id;
    var cmid = M.cfg.contextInstanceId;
    var questionid = 0;
    var syncInterval = interval ? interval * 1000 : 10000; // Default: Sync Every 10s.

    const postOne = async (methodname, args) => {
        try {
            const response = await call([{
                methodname,
                args,
            }])[0];
            return response;
        } catch (error) {
            console.error('Error in postOne:', error);
            throw error;
        }
    };

    if (document.getElementById('page-mod-assign-editsubmission')) {
        if (assignSubmit) {
            assignSubmit.addEventListener('click', async function (e) {
                e.preventDefault();
                if (filename) {
                    await SyncData().then((res) => {
                        assignSubmit.removeEventListener('click', arguments.callee);
                        assignSubmit.click();
                        assignSubmit.removeEventListener('click', arguments.callee);
                    })
                } else {
                    assignSubmit.removeEventListener('click', arguments.callee);
                    assignSubmit.click();
                    assignSubmit.removeEventListener('click', arguments.callee);
                }
            });
        }
    }

    if (document.getElementById('page-mod-quiz-attempt')) {
        if (quizSubmit) {
            quizSubmit.addEventListener('click', async (e) => {
                if (filename) {
                    await SyncData().then(res => {
                        document.querySelector('#responseform').submit();
                    });
                }

            });
        }
    }

    const getModal = (e) => {
        return create({
            type: 'SAVE_CANCEL',
            title: getString('tiny_cursive', 'tiny_cursive'),
            body: '<textarea  class="form-control inputUrl" value="" id="inputUrl" placeholder="sourceurl"></textarea>',

            removeOnClose: true,
        })
            .done(modal => {
                modal.getRoot().append('<style>.close{ display: none ! important; }</style>');
                modal.show();
                var lastEvent = '';
                modal.getRoot().on(save, function () {
                    var number = document.getElementById("inputUrl").value;
                    if (number === "" || number === null || number === undefined) {
                        editor.execCommand('Undo');
                        alert("You cannot paste text without providing source");
                    } else {
                        editor.execCommand('Paste');
                    }
                    let ur = e.srcElement.baseURI;
                    let recourceId = 0;
                    let parm = new URL(ur);
                    let modulename = "";
                    let editorid = editor?.id;
                    let courseid = M.cfg.courseId;
                    let cmid = M.cfg.contextInstanceId;


                    if (ur.includes("attempt.php") || ur.includes("forum") || ur.includes("assign")) { } else {
                        return false;
                    }

                    if (!ur.includes("forum") && !ur.includes("assign")) {
                        recourceId = parm.searchParams.get('attempt');
                    }

                    if (recourceId === null) {
                        recourceId = 0;
                    }
                    if (ur.includes("forum")) {
                        modulename = "forum";
                    }
                    if (ur.includes("assign")) {
                        modulename = "assign";
                    }
                    if (ur.includes("attempt")) {
                        modulename = "quiz";
                    }
                    if (cmid === null) { cmid = 0; }

                    postOne('cursive_user_comments', {
                        modulename: modulename,
                        cmid: cmid,
                        resourceid: recourceId,
                        courseid: courseid,
                        usercomment: number,
                        timemodified: Date.now(),
                        editorid: editorid ? editorid : ""
                    });
                    lastEvent = 'save';
                    modal.destroy();
                });
                modal.getRoot().on(cancel, function () {

                    editor.execCommand('Undo');
                    lastEvent = 'cancel';
                });
                modal.getRoot().on(hidden, function () {
                    if (lastEvent != 'cancel' && lastEvent != 'save') { editor.execCommand('Undo'); }
                });
                return modal;
            });
    };
    const sendKeyEvent = (event, ed) => {
        let ur = ed.srcElement.baseURI;
        let parm = new URL(ur);
        ed = ed;
        event = event;

        if (ur.includes("attempt.php") || ur.includes("forum") || ur.includes("assign")) { } else {
            return false;
        }
        if (ur.includes("forum") || ur.includes("assign")) {

        } else {

            recourceId = parm.searchParams.get('attempt');
        }
        if (recourceId === null) {

            recourceId = 0;
        }

        if (ur.includes("forum")) {
            modulename = "forum";
        }
        if (ur.includes("assign")) {
            modulename = "assign";
        }
        if (ur.includes("attempt")) {
            modulename = "quiz";
        }

        filename = `${userid}_${recourceId}_${cmid}_${modulename}_attempt`;

        if (modulename === 'quiz') {
            questionid = editorid.split(':')[1].split('_')[0];
            filename = `${userid}_${recourceId}_${cmid}_${questionid}_${modulename}_attempt`;
        }
        if (ed.key !== "Process") {
            if (localStorage.getItem(filename)) {

                let data = JSON.parse(localStorage.getItem(filename));
                data.push({
                    resourceId: recourceId,
                    key: ed.key,
                    keyCode: ed.keyCode,
                    event: event,
                    courseId: courseid,
                    unixTimestamp: Date.now(),
                    clientId: host,
                    personId: userid
                });
                localStorage.setItem(filename, JSON.stringify(data));
            } else {
                let data = [];
                data.push({
                    resourceId: recourceId,
                    key: ed.key,
                    keyCode: ed.keyCode,
                    event: event,
                    courseId: courseid,
                    unixTimestamp: Date.now(),
                    clientId: host,
                    personId: userid
                });
                localStorage.setItem(filename, JSON.stringify(data));
            }
        }
    };
    editor.on('keyUp', (editor) => {
        sendKeyEvent("keyUp", editor);
    });
    editor.on('Paste', async (e) => {
        if (is_student && intervention) {
            getModal(e);
        }
    });
    editor.on('Redo', async (e) => {
        if (is_student && intervention) {
            getModal(e);
        }
    });
    editor.on('keyDown', (editor) => {
        sendKeyEvent("keyDown", editor);
    });
    editor.on('init', () => {

    });

    async function SyncData() {

        let data = localStorage.getItem(filename);

        if (!data || data.length === 0) {
            return;
        } else {
            localStorage.removeItem(filename);
            try {
                return await postOne('cursive_write_local_to_json', {
                    key: ed.key,
                    event: event,
                    keyCode: ed.keyCode,
                    resourceId: recourceId,
                    cmid: cmid,
                    modulename: modulename,
                    editorid: editorid,
                    json_data: data,
                });
            } catch (error) {
                console.error('Error submitting data:', error);
            }
        }
    }

    window.addEventListener('unload', (e) => {
        SyncData();
    });

    setInterval(SyncData, syncInterval);
};
