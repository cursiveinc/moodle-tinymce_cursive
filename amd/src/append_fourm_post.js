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
 * @module     tiny_cursive/append_fourm_post
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
    window.video_playback = function (mid, filepath) {
        if (filepath !== '') {
            const replay = new Replay(
                elementId = 'content' + mid,
                filePath = filepath,
                speed = 10,
                loop = false,
                controllerId = 'player_' + mid
            );
            replayInstances[mid] = replay;
        }
        else {
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
                    usersTable.getToken(score_setting, showcomment);
                });
        },
        getToken: function (score_setting, showcomment) {
            $('#page-mod-forum-discuss').find("article").get().forEach(function (entry) {
                $(document).ready(function () {
                    var replyButton = $('a[data-region="post-action"][title="Reply"]');
                    if (replyButton.length > 0) {
                        replyButton.on('click', function (event) {
                            event.preventDefault();
                            var url = $(this).attr('href');
                            window.location.href = url;
                        });
                    }
                });

                var ids = $("#" + entry.id).data("post-id");
                var anchorTag = $('a.nav-link.active.active_tree_node[href*="mod/forum/view.php?id="]');
                var cmid = 0;
                if (anchorTag.length > 0) {
                    var hrefValue = anchorTag.attr('href');
                    cmid = hrefValue.match(/id=(\d+)/)[1];
                }

                let args = { id: ids, modulename: "forum", cmid: cmid };
                let methodname = 'cursive_get_forum_comment_link';
                let com = AJAX.call([{ methodname, args }]);
                com[0].done(function (json) {
                    var data = JSON.parse(json);

                    var filepath = '';
                    if (data.data.filename) {
                        var filepath = data.data.filename;
                    }
                    if (filepath) {

                        let analytic_button_div = document.createElement('div');
                        analytic_button_div.append(analyticButton(ids));
                        analytic_button_div.classList.add('text-center', 'mt-2');
                        analytic_button_div.dataset.region = "analytic-div" + ids;

                        $("#" + entry.id).find('#post-content-' + ids).append(analytic_button_div);
                        if (data.usercomment != 'comments' && parseInt(showcomment)) {

                            let comments = "";
                            data.usercomment.forEach(element => {
                                // Create the anchor element
                                comments += '<div class="border-bottom p-3 text-primary" style="font-weight:600;">'
                                    + element.usercomment + '</div>';
                            });

                            $("#" + entry.id).find('#post-content-' + ids).prepend($('<div>')
                                .addClass('tiny_cursive-quiz-references rounded').append(comments));

                        }

                        let myEvents = new AnalyticEvents();
                        var context = {
                            tabledata: data.data,
                            formattime: myEvents.formatedTime(data.data),
                            page: score_setting,
                            userid: ids,
                        };

                        let authIcon = myEvents.authorshipStatus(data.data.first_file, data.data.score, score_setting);
                        myEvents.createModal(ids, context, '', authIcon);
                        myEvents.analytics(ids, templates, context, '', replayInstances, authIcon);
                        myEvents.checkDiff(ids, data.data.file_id, '', replayInstances);
                        myEvents.replyWriting(ids, filepath, '', replayInstances);
                    }

                });
                return com.usercomment;
            });
        },
    };
    return usersTable;


});