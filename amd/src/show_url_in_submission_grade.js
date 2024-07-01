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
 * @module     tiny_cursive/show_url_in_submission_grade
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str", "core/templates", "./replay"], function (
    $,
    AJAX,
    str,
    templates,
    Replay
){
    const replayInstances = {};
    window.myFunction = function() {
        let mid = $(this).data('id');
        $("#typeid" + mid).show();
    };

    window.video_playback = function(mid, filepath) {
        if (filepath !== ''){
            $("#playback"+mid).show();
            const replay = new Replay(
                elementId = 'output_playback_'+mid,
                filePath = filepath,
                speed = 10,
                loop = false,
                controllerId = 'player_'+mid
            );
            replayInstances[mid] = replay;
        }
        else {
            alert('No submission');
        }
        return false;

    };

    window.popup_item = function(mid) {
        $("#" + mid).show();
    };

    var usersTable = {
        init: function(score_setting, showcomment) {
            str
                .get_strings([
                    {key: "field_require", component: "tiny_cursive"},
                ])
                .done(function() {
                    usersTable.appendSubmissionDetail(score_setting, showcomment);
                });
        },
        appendSubmissionDetail: function(score_setting, showcomment) {
            $(document).ready(function($) {

                var divElement = $('.path-mod-assign [data-region="grade-panel"]')[0];
                var previousContextId = window.location.href;
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {

                        var currentContextId = window.location.href;
                        if (currentContextId !== previousContextId) {
                            window.location.reload();
                            previousContextId = currentContextId;
                        }
                    });
                });

                // Configuration of the observer:
                var config = { childList: true, subtree: true };

                // Start observing the target node for configured mutations
                observer.observe(divElement, config);

                let sub_url = window.location.href;
                let parm = new URL(sub_url);
                let userid = parm.searchParams.get('userid');
                let cmid = parm.searchParams.get('id');
                var chart = "fa fa-area-chart popup_item";
                var video = "fa fa-play video_playback";
                var st = "font-size:24px;color:black;border:none";
              
                let args = {id: userid, modulename: "assign", 'cmid': cmid};
                let methodname = 'cursive_get_assign_grade_comment';
                let com = AJAX.call([{methodname, args}]);
                com[0].done(function(json) {
                    var data = JSON.parse(json);

                    $('.alert').remove();
                    if (data.usercomment != 'comments' && parseInt(showcomment)) {
                        $(document).ready(function(){
                            $('div[data-region="grade-panel"]').append('<div class="dropdown">');
                            var tt = '';
                            data.usercomment.forEach(element => {
                                tt += '<li>' + element.usercomment + '</li>';
                            });
                            var p1 = '<div class="border alert alert-warning"><details><summary>Content Sources Provided by Student</summary>';
                            $('div[data-region="grade-panel"]').append(p1 + ' ' + tt + '</details></div></div>');
                        });
                    }

                    var filepath ='';
                    if (data.data.filename){
                        var filepath =data.data.filename;
                    }
                    var score = parseFloat(data.data.score);
                    var icon = 'fa fa-circle-o';
                    var color = 'font-size:24px;color:black';
                    
                    if(data.data.first_file){
                        icon = 'fa  fa fa-solid fa-info-circle typeid';
                        color = 'font-size:24px;color:#000000';
                    }
                    else{
                        if (score >= score_setting) {
                            icon = 'fa fa-check-circle typeid';
                            color = 'font-size:24px;color:green';
                        } else if (score < score_setting) {
                            icon = 'fa fa-question-circle typeid';
                            color = 'font-size:24px;color:#A9A9A9';
                        } else {
                            icon = 'fa fa-circle-o typeid';
                            color = 'font-size:24px;color:black';
                        }
                    }
                    var html= '<div class="justify-content-center d-flex py-3">' +
                        '<button onclick="popup_item(' + userid + ')" data-id=' + userid + ' class="mr-2 ' + chart + '" style="' + st + '"></button>' +
                        '<a href="#" onclick="video_playback(' + userid + ', \'' + filepath + '\')" data-filepath="' +
                            filepath + '" data-id="playback_' + userid + '" class="mr-2 video_playback_icon ' + video + '" style="' + st + '"></a>' +
                        '<button onclick="myFunction()" data-id=' + userid + ' class="' + icon + ' " style="border:none; ' + color + ';"></button>' +
                        '</div>      ';

                    $('div[data-region="grade-actions"]').before(html);

                    $('div[data-region="review-panel"]').addClass('cursive_review_panel_path_mod_assign');

                    $('div[data-region="grading-navigation-panel"]').addClass('cursive_grading-navigation-panel_path_mod_assign');

                    $('div[data-region="grade-panel"]').addClass('cursive_grade-panel_path_mod_assign');

                    $('div[data-region="grade-actions-panel"]').addClass('cursive_grade-actions-panel_path_mod_assign');

                    var context = {
                        tabledata: data.data,
                        page: score_setting,
                        userid: userid,
                    };
                    templates
                        .render("tiny_cursive/pop_modal", context)
                        .then(function (html) {
                            $("body").append(html);
                        }).catch(e => window.console.error(e));

                });


                $(window).on('click', function (e) {
                    if (e.target.id == 'modal-close' + userid) {
                        $("#" + userid).hide();
                    }
                    if (e.target.id == 'modal-close-playback' + userid) {
                        $("#playback" + userid).hide();
                        if (replayInstances[userid]) {
                            replayInstances[userid].stopReplay();
                        }
                    }
                });

                return com.usercomment;
            });
        },
    };
    return usersTable;
});


