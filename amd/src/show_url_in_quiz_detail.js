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

    window.video_playback = function (mid, filepath, questionid) {
        if (filepath !== '') {
            const replay = new Replay(
                elementId = 'content' + mid,
                filePath = filepath,
                speed = 10,
                loop = false,
                controllerId = 'player_' + mid+questionid
            );
            replayInstances[mid] = replay;
        } else {
            templates.render('tiny_cursive/no_submission').then(html => {
                $('#content' + mid).html(html);
            }).catch(e => window.console.error(e));
        }
        return false;
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

                        var content = $('.que.essay .editquestion a[href*="question/bank/editquestion/question.php"][href*="&id='
                            + data.data.questionid + '"]');
                        if (data.usercomment != 'comments' && parseInt(showcomment)) {
                            content.parent().parent().parent().find('.qtext').append('<div class="mb-2">');
                            var tt = '<h4>References</h4><div class = "tiny_cursive-quiz-references rounded" >';
                            data.usercomment.forEach(element => {
                                tt += '<div class = "text-primary p-3" style="border-bottom:1px solid rgba(0, 0, 0, 0.1)">'
                                    + element.usercomment + '</div>';
                            });

                            content.parent().parent().parent().find('.qtext').append(tt + '</div></div>');
                        }
                        var filepath = '';
                        if (data.data.filename) {
                            filepath = data.data.filename;
                        }
                        let analytic_button_div = document.createElement('div');
                        analytic_button_div.classList.add('text-center', 'mt-2');
                        analytic_button_div.append(analyticButton(userid, questionid));
                        content.parent().parent().parent().find('.qtext').append(analytic_button_div);

                        let myEvents = new AnalyticEvents();
                        var context = {
                            tabledata: data.data,
                            formattime: myEvents.formatedTime(data.data),
                            page: score_setting,
                            userid: userid,
                            quizid: questionid,
                        };
                        let authIcon = myEvents.authorshipStatus(data.data.first_file, data.data.score, score_setting);
                        myEvents.createModal(userid, context, questionid, authIcon);
                        myEvents.analytics(userid, templates, context, questionid, replayInstances, authIcon);
                        myEvents.checkDiff(userid, data.data.file_id, questionid, replayInstances);
                        myEvents.replyWriting(userid, filepath, questionid, replayInstances);

                    }
                });
                return com.usercomment;
            });
        },
    };
    return usersTable;
});
