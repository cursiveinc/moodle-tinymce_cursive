/**
 * @module     tiny_cursive/cursive_writing_reports
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
) {
    const replayInstances = {};
    var usersTable = {
        init: function (page) {
            str
                .get_strings([
                    {key: "field_require", component: "tiny_cursive"},
                ])
                .done(function () {
                    $(document).ready(function ($) {
                        $(".popup_item").on('click', function () {
                            var mid = $(this).data("id");
                            $("#" + mid).show();
                        });
                        $(".link_icon").on('click', function () {
                            var smid = $(this).data("id");
                            $("#" + smid).show();
                            window.console.log("#score" + smid);
                        });

                        $(".video_playback_icon").on('click', function () {

                            var mid = $(this).data("id");
                            var filepath = $(this).data("filepath");
                            if(filepath){
                            $("#" + mid).show();
                            const replay = new Replay(
                                elementId = 'output_'+mid,
                                filePath = decodeURIComponent(filepath),
                                speed = 10,
                                loop = false,
                                controllerId = 'player_' + mid
                            );
                            replayInstances[mid] = replay;
                            } else {
                                alert("File not found");
                            }
                        });
                        $(".modal-close ").on('click', function () {
                            $(".modal").hide();
                            var mid = $(this).data("id");
                            if (replayInstances[mid]) {
                                replayInstances[mid].stopReplay();
                                delete replayInstances[mid];  // Clean up the instance
                            }
                        });
                    });
                    usersTable.getusers(page);
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
                        .then(function (html, js) {
                            window.console.log(js);
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
                        .then(function (html, js) {
                            window.console.log(js);
                            var filtered_user = $("#id_modulename");
                            filtered_user.html(html);
                        });
                });
            });
        },
    };
    return usersTable;
});
