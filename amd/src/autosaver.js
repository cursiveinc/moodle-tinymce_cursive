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
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

import { call } from 'core/ajax';
import { create } from 'core/modal_factory';
import { get_string as getString } from 'core/str';
import { save, cancel, hidden } from 'core/modal_events';
import jQuery from 'jquery';
import user from 'tiny_cursive/user';

export const register = (editor) => {
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
    
    var is_student = !(jQuery('#body').hasClass('teacher_admin'));
    var intervention = jQuery('#body').hasClass('intervention');
    var userid = null;
    var filename = "";
    var quizSubmit = jQuery('#mod_quiz-next-nav');
    var ed = "";
    var event = "";
    var recourceId = 0;
    var modulename = "";
    var editorid = editor?.id;
    let bodyid = jQuery('body').attr('class');
    var classes = bodyid.split(' ');
    var cmid = 0;
    var questionid = 0;
    let assignSubmit = jQuery('#id_submitbutton');
    
    assignSubmit.on('click', async function (e) {
        e.preventDefault();
        if (filename) {
            let data = localStorage.getItem(filename);
            localStorage.removeItem(filename);
    
            try {
                await postOne('cursive_write_local_to_json', {
                    key: ed.key,
                    event: event,
                    keyCode: ed.keyCode,
                    resourceId: recourceId,
                    cmid: cmid,
                    modulename: modulename,
                    editorid: editorid,
                    json_data: data,
                });
                // Custom logic completed, re-enable default action or other logic
                assignSubmit.off('click').click();
            } catch (error) {
                console.error('Error submitting data:', error);
                // Handle the error, if needed
            }
        } else {
            // No filename, proceed with any other default actions
            assignSubmit.off('click').click();
        }
    });
    
    quizSubmit.on('click', async function (e) {
        e.preventDefault();
        if (filename) {
            let data = localStorage.getItem(filename);
            localStorage.removeItem(filename);
    
            try {
                await postOne('cursive_write_local_to_json', {
                    key: ed.key,
                    event: event,
                    keyCode: ed.keyCode,
                    resourceId: recourceId,
                    cmid: cmid,
                    modulename: modulename,
                    editorid: editorid,
                    json_data: data,
                });
                // Custom logic completed, re-enable default action or other logic
                quizSubmit.off('click').click();
            } catch (error) {
                console.error('Error submitting data:', error);
                // Handle the error, if needed
            }
        } else {
            // No filename, proceed with any other default actions
            quizSubmit.off('click').click();
        }
    });
    

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
                    let bodyid = jQuery('body').attr('class');
                    let classes = bodyid.split(' ');
                    let courseid = parseInt(classes.find((classname) => { return classname.startsWith('course-') }).split('-')[1]); // Getting cmid from body classlist.
                    let cmid = parseInt(classes.find((classname) => { return classname.startsWith('cmid-') }).split('-')[1]); // Getting cmid from body classlist.


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
                        timemodified: "1121232",
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
        cmid = parseInt(classes.find((classname) => { return classname.startsWith('cmid-') }).split('-')[1]); // Getting cmid from body classlist.


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
            // console.log(editorid);
        }
        // console.log(filename,cmid,classes);
        if (localStorage.getItem(filename)) {

            let data = JSON.parse(localStorage.getItem(filename));
            data.push({
                resourceId: recourceId,
                key: ed.key,
                keyCode: ed.keyCode,
                event: event,
            });
            localStorage.setItem(filename, JSON.stringify(data));
        } else {
            let data = [];
            data.push({
                resourceId: recourceId,
                key: ed.key,
                keyCode: ed.keyCode,
                event: event,
            });
            localStorage.setItem(filename, JSON.stringify(data));
        }

        // postOne('cursive_json', {
        //     key: ed.key,
        //     event: event,
        //     keyCode: ed.keyCode,
        //     resourceId: recourceId,
        //     cmid: cmid,
        //     modulename: modulename,
        //     editorid: editorid ? editorid : ""
        // });
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
        userid = user.getUserId();
    });
};
