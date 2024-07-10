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
 * @module     tiny_cursive/show_url_in_quiz_detail
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str", "core/templates", "./replay", "./analytic_button", "./analytic_events"], function (
    $,
    AJAX,
    str,
    templates,
    Replay,
    analyticButton,
    AnalyticEvents
) {
    const replayInstances = {};

    window.myFunction = function () {
        let mid = $(this).data('id');
        $("#typeid" + mid).show();
    };

    window.video_playback = function (mid, filepath) {
        if (filepath !== '') {
            // $("#playback" + mid).show();
            const replay = new Replay(
                elementId = 'content' + mid,
                filePath = filepath,
                speed = 10,
                loop = false,
                controllerId = 'player_' + mid
            );
            replayInstances[mid] = replay;
        } else {
            alert('No submission');
        }
        return false;
    };

    window.popup_item = function (mid) {
        $("#" + mid).show();
    };

    var usersTable = {
        init: function (score_setting, showcomment) {
            str
                .get_strings([
                    { key: "field_require", component: "tiny_cursive" },
                ])
                .done(function () {
                    usersTable.appendSubmissionDetail(score_setting, showcomment);
                });
        },
        appendSubmissionDetail: function (score_setting, showcomment) {
            let sub_url = window.location.href;
            let parm = new URL(sub_url);
            let attempt_id = parm.searchParams.get('attempt');

            let cmid = parm.searchParams.get('cmid');
            if (!cmid) {
                var firstHref = $('a[href*="question/bank/editquestion/question.php"]').first().attr('href');
                if (firstHref && firstHref.length > 0) {
                    cmid = firstHref.match(/cmid=(\d+)/)[1];
                }
            }
            var userid = '';
            var tableRow = $('table.generaltable.generalbox.quizreviewsummary tbody tr');
            tableRow.each(function () {
                var href = $(this).find('a[href*="/user/view.php"]').attr('href');
                if (href) {
                    var id = href.match(/id=(\d+)/);
                    if (id) {
                        userid = id[1];
                    }
                }
            });
            var chart = "fa fa-area-chart popup_item";
            var video = "fa fa-play video_playback";
            var st = "font-size:24px;color:black;border:none";

            $('#page-mod-quiz-review .info').each(function () {

                var editQuestionLink = $(this).find('.editquestion a[href*="question/bank/editquestion/question.php"]');
                if (editQuestionLink.length > 0) {
                    var editQuestionLink = editQuestionLink.attr('href');
                    var questionid = editQuestionLink.match(/&id=(\d+)/)[1];
                }

                let args = { id: attempt_id, modulename: "quiz", "cmid": cmid, "questionid": questionid, "userid": userid };
                let methodname = 'cursive_get_comment_link';
                let com = AJAX.call([{ methodname, args }]);
                com[0].done(function (json) {
                    var data = JSON.parse(json);

                    if (data.data.filename) {
                        var html = '';
                        var content = $('.que.essay .editquestion a[href*="question/bank/editquestion/question.php"][href*="&id=' + data.data.questionid + '"]');
                        if (data.usercomment != 'comments' && parseInt(showcomment)) {
                            content.parent().parent().parent().find('.qtext').append('<div class="mb-2">');
                            var tt = '<h4>References</h4><div style="background-color:#FCEFDC;font-weight:500;" class = "border-bottom" >';
                            data.usercomment.forEach(element => {
                                tt += '<div class = "text-primary p-3" style="border-bottom:1px solid rgba(0, 0, 0, 0.1)">' + element.usercomment + '</div>';
                            });

                            content.parent().parent().parent().find('.qtext').append(tt + '</div></div>');
                        }
                        var filepath = '';
                        if (data.data.filename) {
                            filepath = data.data.filename;
                        }
                        var score = parseInt(data.data.score);
                        var icon = 'fa fa-circle-o';
                        var color = 'font-size:24px;color:black';
                        if (data.data.first_file) {
                            icon = 'fa  fa fa-solid fa-info-circle typeid';
                            color = 'font-size:24px;color:#000000';
                        } else {
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
                        // html = '<div class="justify-content-center d-flex">' +
                        //     '<button onclick="popup_item(\'' + userid + "-" + questionid + '\')" data-id=' + userid + ' class="mr-2 ' + chart + '" style="' + st + '"></button>' +
                        //     '<a href="#" onclick="video_playback(' + questionid + ', \'' + filepath + '\')" data-filepath="' + filepath + '" data-id="playback_' + questionid + '" class="mr-2 video_playback_icon ' + video + '" style="' + st + '"></a>' +
                        //     '<button onclick="myFunction()" data-id=' + userid + ' class="' + icon + ' " style="border:none; ' + color + ';"></button>' +
                        //     '</div>';
                        let analytic_button_div = document.createElement('div');
                        analytic_button_div.classList.add('text-center', 'mt-2');
                        analytic_button_div.append(analyticButton(userid, questionid));
                        content.parent().parent().parent().find('.qtext').append(analytic_button_div);

                        console.log("Quiz: ", data.data);
                        let myEvents = new AnalyticEvents();
                        var context = {
                            tabledata: data.data,
                            formattime: myEvents.formatedTime(data.data),
                            page: score_setting,
                            userid: userid,
                            quizid: questionid,
                        };


                        myEvents.createModal(userid, context, questionid);
                        myEvents.analytics(userid, templates, context, questionid, replayInstances);
                        myEvents.checkDiff(userid, data.data.file_id, questionid, replayInstances);
                        myEvents.replyWriting(userid, filepath, questionid, replayInstances);

                        templates
                            .render("tiny_cursive/quiz_pop_modal", context)
                            .then(function (html) {
                                $("body").append(html);
                            }).catch(e => window.console.error(e));
                    }
                });
                $(window).on('click', function (e) {
                    const targetId = e.target.id;
                    if (targetId.startsWith('modal-close' + userid + '-' + questionid)) {
                        $("#" + userid + "-" + questionid).hide();
                    }
                    if (targetId.startsWith('modal-close-playback' + questionid)) {
                        $("#playback" + questionid).hide();
                        if (replayInstances[questionid]) {
                            replayInstances[questionid].stopReplay();
                        }
                    }
                });
                return com.usercomment;
            });
        },
    };
    return usersTable;
});
