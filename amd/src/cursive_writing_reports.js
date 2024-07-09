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
 * @module     tiny_cursive/cursive_writing_reports
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str", "core/templates", "./replay", './analytic_button', "./analytic_events"], function (
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
            // $("#playback"+mid).show();
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
            alert('No submission');
        }
        return false;

    };
    var usersTable = {
        init: function (page) {
            str
                .get_strings([
                    { key: "field_require", component: "tiny_cursive" },
                ])
                .done(function () {
                    $(document).ready(function ($) {
                        // $(".popup_item").on('click', function () {
                        //     var mid = $(this).data("id");
                        //     $("#" + mid).show();
                        // });
                        //     $(".link_icon").on('click', function () {
                        //         var smid = $(this).data("id");
                        //         $("#" + smid).show();

                        //     });

                        //     $(".video_playback_icon").on('click', function () {

                        //         var mid = $(this).data("id");
                        //         var filepath = $(this).data("filepath");
                        //         if(filepath){
                        //         $("#" + mid).show();
                        //         const replay = new Replay(
                        //             elementId = 'output_'+mid,
                        //             filePath = decodeURIComponent(filepath),
                        //             speed = 10,
                        //             loop = false,
                        //             controllerId = 'player_' + mid
                        //         );
                        //         replayInstances[mid] = replay;
                        //         } else {
                        //             alert("File not found");
                        //         }
                        //     });
                        //     $(".modal-close ").on('click', function () {
                        //         $(".modal").hide();
                        //         var mid = $(this).data("id");
                        //         if (replayInstances[mid]) {
                        //             replayInstances[mid].stopReplay();
                        //             delete replayInstances[mid];  // Clean up the instance
                        //         }
                        //     });

                    });


                    usersTable.getusers(page);
                });

            let myEvents = new AnalyticEvents();
            $(".analytic-modal").each(function () {
                var mid = $(this).data("id");
                var filepath = $(this).data("filepath");
                let context = { userid: mid };
                let cmid = $(this).data("cmid");

                $(this).html(analyticButton($(this).data('id')));
                AJAX.call([{
                    methodname: 'cursive_get_writing_statistics',
                    args: {
                        cmid: cmid,
                        fileid: mid,
                    },
                }])[0].done(response => {
                    let data = JSON.parse(response.data);

                    context.formattime = myEvents.formatedTime(data);
                    context.tabledata = data;

                }).fail(error => {
                    throw new Error('Error: ' + error.message);
                });

                // Perform actions that require context.tabledata
                myEvents.createModal(mid, context);
                myEvents.analytics(mid, templates, context);
                myEvents.checkDiff(mid, mid);
                myEvents.replyWriting(mid, filepath);
            });
        },
        getusers: function (page) {
            $("#id_coursename").change(function () {
                var courseid = $(this).val();
                var promise1 = AJAX.call([
                    {
                        methodname: "cursive_get_user_list",
                        args: {
                            courseid: courseid,
                        },
                    },
                ]);
                promise1[0].done(function (json) {
                    var data = JSON.parse(json);
                    var context = {
                        tabledata: data,
                        page: page,
                    };
                    templates
                        .render("tiny_cursive/user_list", context)
                        .then(function (html) {

                            var filtered_user = $("#id_username");
                            filtered_user.html(html);
                        });
                });

                var promise2 = AJAX.call([
                    {
                        methodname: "cursive_get_module_list",
                        args: {
                            courseid: courseid,
                        },
                    },
                ]);
                promise2[0].done(function (json) {
                    var data = JSON.parse(json);
                    var context = {
                        tabledata: data,
                        page: page,
                    };
                    templates
                        .render("tiny_cursive/module_list", context)
                        .then(function (html) {

                            var filtered_user = $("#id_modulename");
                            filtered_user.html(html);
                        });
                });
            });
        },
    };
    return usersTable;
});
