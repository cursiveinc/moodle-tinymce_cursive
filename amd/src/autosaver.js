

/**
 * @module     tiny_cursive/plugin
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

import { call } from 'core/ajax';
import { create } from 'core/modal_factory';
import {get_string as getString} from 'core/str';
import {render} from 'core/templates';
import {save,cancel,hidden} from 'core/modal_events';//hidden
import jQuery from 'jquery';
export const register = (editor) => {
    jQuery(function () {
    });
    const postOne = (methodname, args) => call([{
        methodname,
        args,
    }])[0];
    const showLog=()=>{
        window.console.log(editor.id);
    };
    const getModal = (e) => {
        return create({
            type:'SAVE_CANCEL',
            title: getString('quizname','tiny_cursive'),
            body:  render("tiny_cursive/popup_form", ""),
            removeOnClose: true,
        },showLog())
        .done(modal => {
            modal.getRoot().append('<style>.close{ display: none ! important; }</style>');
              modal.show();
              modal.getRoot().on(save, function() {
                var number=document.getElementById("inputUrl").value;
                //var pastedtext=document.getElementById("pastetext").value;
                if (number === "" || number === null || number === undefined) {
                    editor.execCommand('Undo');
                    alert("You cannot paste text without providing source");
                } else {
                 editor.execCommand('Paste');
                }
                let ur = e.srcElement.baseURI;
                let parm = new URL(ur);
                let recourceId=0;
                let cmid=0;
                let modulename="";
                if (ur.includes("attempt.php")||ur.includes("forum")||ur.includes("assign")){
                    //return true;
                }else{
                    return false;
                }
                if (ur.includes("forum")||ur.includes("assign")) {
                    cmid=parm.searchParams.get('id');
                }else{
                    cmid=parm.searchParams.get('cmid');
                    recourceId=parm.searchParams.get('attempt');
                }
                if(recourceId===null){
                    recourceId=0;
                }
                if (ur.includes("forum")){
                    modulename="forum";
                }
                if (ur.includes("assign")){
                    modulename="assign";
                }
                if (ur.includes("attempt")){
                    modulename="quiz";
                }
                if(cmid===null){ cmid=0;}
                postOne('cursive_user_comments', {
                    modulename: modulename,
                    cmid: cmid,
                    resourceid: recourceId,
                    courseid: 0,
                    usercomment:number,
                    timemodified:"1121232"
                });
                modal.destroy();
              });
                modal.getRoot().on(cancel, function() {
                        editor.execCommand('Undo');
                        alert("You cannot paste text without providing source");
                    });
                modal.getRoot().on(hidden, function(e) {
                        window.console.log(e);
                    });
            return modal;
        });
    };
    const sendKeyEvent=(event, ed)=>{
         let ur = ed.srcElement.baseURI;
         let parm = new URL(ur);
         let recourceId=0;
         let modulename="";
        let cmid=0;
         if (ur.includes("attempt.php")||ur.includes("forum")||ur.includes("assign")){
            //return true;
         }else{
            return false;
         }
         if (ur.includes("forum")||ur.includes("assign")) {
            cmid=parm.searchParams.get('id');
        }else{
            cmid=parm.searchParams.get('cmid');
            recourceId=parm.searchParams.get('attempt');
        }
        if(recourceId===null){
            recourceId=0;
        }
        if(cmid===null){ cmid=0;}
        if (ur.includes("forum")){
            modulename="forum";
        }
        if (ur.includes("assign")){
            modulename="assign";
        }
        if (ur.includes("attempt")){
            modulename="quiz";
        }
        postOne('cursive_json', {
            key: ed.key,
            event: event,
            keyCode: ed.keyCode,
            resourceId: recourceId,
            cmid:cmid,
            modulename:modulename
        });
    };
    editor.on('keyUp', (editor) => {
        sendKeyEvent("keyUp", editor);
    });
    editor.on('Paste', async (e) => {
        //e.preventDefault();
        getModal(e);
    });
    editor.on('Redo', async (e) => {
        //e.preventDefault();
        getModal(e);
    });
    editor.on('keyDown', (editor) => {
        sendKeyEvent("keyDown", editor);
    });
    editor.on('init', () => {
        // Setup the Undo handler.
    });

};
