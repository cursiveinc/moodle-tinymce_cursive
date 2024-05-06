define(["jquery", "core/ajax", "core/str", "core/templates", "./replay"], function(
    $,
    AJAX,
    str,
    templates,
    Replay
) {
    window.myFunction = function() {
        let mid = $(this).data('id');
        $("#typeid" + mid).show();
    };

    window.video_playback = function(mid, filepath) {
        if (filepath !== ''){
            $("#playback"+mid).show();
            new Replay(
                elementId = 'output_playback_'+mid,
                filePath = filepath,
                speed = 10,
                loop = false,
                controllerId = 'player_'+mid
            );
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
                console.log(entry.id);
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
                    if (data.usercomment != 'comments') {
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
                        var filepath = M.cfg.wwwroot+'/lib/editor/tiny/plugins/cursive/userdata/'+ data.data.filename;
                    }
                    if (filepath){
                        var score = data.data.score;
                        var icon = 'fa fa-circle-o';
                        var color = 'font-size:24px;color:black';
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
                            }).catch(e => window.console.log(e));
                    }

                });
                $(window).on('click', function (e) {
                    if (e.target.id == 'modal-close' + ids) {
                        $("#" + ids).hide();
                    }
                    if (e.target.id == 'modal-close-playback' + ids) {
                        $("#playback" + ids).hide();
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