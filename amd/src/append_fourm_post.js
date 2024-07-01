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

define(["jquery", "core/ajax", "core/str", "core/templates", "./replay"], function(
    $,
    AJAX,
    str,
    templates,
    Replay
) {
    const replayInstances = {};
    window.myFunction = function() {
        let mid = $(this).data('id');
        $("#typeid" + mid).show();
    };

    window.video_playback = function(mid, filepath) {
        if (filepath !== '') {
            $("#playback"+mid).show();
            const replay = new Replay(
                elementId = 'output_playback_' + mid,
                filePath = filepath,
                speed = 10,
                loop = false,
                controllerId = 'player_' + mid
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
                    usersTable.getToken(score_setting, showcomment);
                });
        },
        getToken: function(score_setting,showcomment) {
            $('#page-mod-forum-discuss').find("article").get().forEach(function(entry) {
                $(document).ready(function() {
                    var replyButton = $('a[data-region="post-action"][title="Reply"]');
                    if (replyButton.length > 0) {
                        replyButton.on('click', function(event) {
                            event.preventDefault();
                            var url = $(this).attr('href');
                            window.location.href = url;
                        });
                    }
                });
               
                var ids = $("#" + entry.id).data("post-id");
                var anchorTag = $('a.nav-link.active.active_tree_node[href*="mod/forum/view.php?id="]');
                var cmid= 0;
                if (anchorTag.length > 0) {
                    var hrefValue = anchorTag.attr('href');
                    cmid = hrefValue.match(/id=(\d+)/)[1];
                }
                var chart = "fa fa-area-chart popup_item";
                var video = "fa fa-play video_playback";
                var st = "font-size:24px;color:black;border:none";

                let args = {id: ids, modulename: "forum",cmid:cmid};
                let methodname = 'cursive_get_forum_comment_link';
                let com = AJAX.call([{methodname, args}]);
                com[0].done(function(json) {
                    var data = JSON.parse(json);
                    if (data.usercomment != 'comments' && parseInt(showcomment)) {
                        $("#" + entry.id).find('#post-content-' + ids).append('<div class="dropdown">');
                        var tt = '';
                        data.usercomment.forEach(element => {
                            tt += '<li>' + element.usercomment + '</li>';
                        });
                        var p1 = '<div class="border alert alert-warning"><details><summary>Content Sources Provided by Student</summary>';
                        $("#" + entry.id).find('#post-content-' + ids).append(p1 + ' ' + tt + '</details></div></div>');
                    }
                    var filepath ='';
                    if (data.data.filename){
                        var filepath = data.data.filename;
                    }
                    if (filepath){
                        var score = parseFloat(data.data.score);
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
                        var html= '<div class="justify-content-center d-flex">' +
                            '<button onclick="popup_item(' + ids + ')" data-id=' + ids + ' class="mr-2 ' + chart + '" style="' + st + '"></button>' +
                            '<a href="#" onclick="video_playback(' + ids + ', \'' + filepath + '\')" data-filepath="' + filepath + '" data-id="playback_' + ids + '" class="mr-2 video_playback_icon ' + video + '" style="' + st + '"></a>' +
                            '<button onclick="myFunction()" data-id=' + ids + ' class="' + icon + ' " style="border:none; ' + color + ';"></button>' +
                            '</div>';
                        $("#" + entry.id).find('#post-content-' + ids).append(html);
                        var context = {
                            tabledata: data.data,
                            page: score_setting,
                            userid: ids,
                        };
                        templates
                            .render("tiny_cursive/pop_modal", context)
                            .then(function (html) {
                                $("body").append(html);
                            }).catch(e => window.console.error(e));
                    }

                });
                $(window).on('click', function (e) {
                    if (e.target.id == 'modal-close' + ids) {
                        $("#" + ids).hide();
                    }
                    if (e.target.id == 'modal-close-playback' + ids) {
                        $("#playback" + ids).hide();
                        if (replayInstances[ids]) {
                            replayInstances[ids].stopReplay();
                        }
                    }
                });
                return com.usercomment;
            });
            $('#page-mod-forum-view').find("article").get().forEach(function(entry) {

              

                var ids = $("#" + entry.id).data("post-id");
                var cmid = 0;
                var anchorTag = $('a.nav-link.active.active_tree_node[href*="mod/forum/view.php?id="]');
                if (anchorTag.length > 0) {
                    var hrefValue = anchorTag.attr('href');
                    cmid = parseInt(hrefValue.match(/id=(\d+)/)[1]);
                }
                let args = {id: ids, modulename: "forum", cmid:cmid};
                let methodname = 'cursive_get_comment_link';
                let com = AJAX.call([{methodname, args}]);
                com[0].done(function(json) {
                    var data = JSON.parse(json);
                    var p1 = '<div class="border alert alert-warning"><summary>Content Sources Provided by Student</summary>';
                    $("#" + entry.id).find('#post-content-' + ids).append(p1 + " <p>" + data.usercomment + ids + "</p></div>");
                });
                return com.usercomment;
            });
        },
    };
    return usersTable;


});