/**
 * @module     tiny_cursive/plugin
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str","core/templates", "./replay"], function (
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
        init: function (score_setting, showcomment) {
            str
                .get_strings([
                    {key: "field_require", component: "tiny_cursive"},
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
            if (!cmid){
                var firstHref = $('a[href*="/mod/quiz/review.php"]').first().attr('href');
                if (firstHref.length > 0 ) {
                    cmid = firstHref.match(/cmid=(\d+)/)[1];
                }
            }
            var userid = '';
            var tableRow = $('table.generaltable.generalbox.quizreviewsummary tbody tr');
            tableRow.each(function() {
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

            $('#page-mod-quiz-review .info').each(function() {

                var editQuestionLink = $(this).find('.editquestion a[href*="question/bank/editquestion/question.php"]');
                if (editQuestionLink.length > 0) {
                    var editQuestionLink = editQuestionLink.attr('href');
                    var questionid = editQuestionLink.match(/&id=(\d+)/)[1];
                }

                let args = {id: attempt_id, modulename: "quiz","cmid":cmid,"questionid":questionid,"userid":userid};
                let methodname = 'cursive_get_comment_link';
                let com = AJAX.call([{methodname, args}]);
                com[0].done(function (json) {
                    var data = JSON.parse(json);

                    if (data.data.filename){
                        var html= '';
                        var content = $('.que.essay .editquestion a[href*="question/bank/editquestion/question.php"][href*="&id='+data.data.questionid+'"]');
                        if (data.usercomment != 'comments') {
                            content.parent().parent().parent().find('.qtext').append('<div class="dropdown">');
                            var tt = '';
                            data.usercomment.forEach(element => {
                                tt += '<li>' + element.usercomment + '</li>';
                            });
                            var p1 = '<div class="border alert alert-warning"><details><summary>Content Sources Provided by Student</summary>';
                            content.parent().parent().parent().find('.qtext').append(p1 + ' ' + tt + '</details></div></div>');
                        }
                        var filepath ='';
                        if (data.data.filename){
                            var filepath = M.cfg.wwwroot+'/lib/editor/tiny/plugins/cursive/userdata/'+ data.data.filename;
                        }
                        var score = data.data.score;
                        var icon = 'fa fa-circle-o';
                        var color = 'font-size:24px;color:black';
                        console.log(data.data.first_file);
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
                        html= '<div class="justify-content-center d-flex">' +
                            '<button onclick="popup_item(\'' + userid + "-" + questionid + '\')" data-id=' + userid + ' class="mr-2 ' + chart + '" style="' + st + '"></button>' +
                            '<a href="#" onclick="video_playback(' + userid + ', \'' + filepath + '\')" data-filepath="' + filepath + '" data-id="playback_' + userid + '" class="mr-2 video_playback_icon ' + video + '" style="' + st + '"></a>' +
                            '<button onclick="myFunction()" data-id=' + userid + ' class="' + icon + ' " style="border:none; ' + color + ';"></button>' +
                            '</div>';
                        content.parent().parent().parent().find('.qtext').append(html);
                        var context = {
                            tabledata: data.data,
                            page: score_setting,
                            userid: userid,
                            quizid: questionid,
                        };
                        templates
                            .render("tiny_cursive/quiz_pop_modal", context)
                            .then(function (html) {
                                $("body").append(html);
                            }).catch(e => window.console.log(e));
                    }
                });
                $(window).on('click', function (e) {
                    if (e.target.id == 'modal-close' + userid+'-'+questionid) {
                        $("#" + userid+"-"+questionid).hide();
                    }
                    if (e.target.id == 'modal-close-playback' + userid) {
                        $("#playback" + userid).hide();
                    }
                });
                return com.usercomment;
            });
        },
    };
    return usersTable;
});
