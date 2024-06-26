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
 * @module     tiny_cursive/append_submissions_table
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
        init: function (score_setting, showcomment) {
            str
                .get_strings([
                    {key: "confidence_threshold", component: "tiny_cursive"},
                ]).done(function () {
                usersTable.appendTable(score_setting, showcomment);
            });
        },
        appendTable: function (score_setting) {
            let sub_url = window.location.href;
            let parm = new URL(sub_url);
            let h_tr = $('thead').find('tr').get()[0];
            $(h_tr).find('th').eq(3).after('<th class="header c3 email">TypeID</th><th class="header c3 email">Playback</th><th class="header c3 email">Stats</th>');
            $('tbody').find("tr").get().forEach(function (tr) {
                let td_user = $(tr).find("td").get()[0];
                let userid = $(td_user).find("input[type='checkbox']").get()[0].value;
                let cmid = parm.searchParams.get('id');
                var chart = "fa fa-area-chart popup_item";
                var video = "fa fa-play video_playback";
                var st = "font-size:24px;color:black;border:none";
                let thunder_icon = '<td><button data-id=' + userid + ' class="' + chart + '" style="' + st + '"></button></td>';
                $(tr).find('td').eq(3).after(thunder_icon);

                let args = {id: userid, modulename: "assign", cmid: cmid};
                let methodname = 'cursive_user_list_submission_stats';
                let com = AJAX.call([{methodname, args}]);
                try {
                    com[0].done(function (json) {
                        var data = JSON.parse(json);
                        var filepath ='';
                        if (data.res.filename){
                            var filepath =data.res.filename;
                        }
                        var score = parseFloat(data.res.score);
                        var icon = 'fa fa-circle-o';
                        var color = 'font-size:24px;color:black';
                        if(data.res.first_file){
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

                        let video_icon = '<td><a href="#" onclick="video_playback(' + userid + ', \'' + filepath + '\')" data-filepath="' + filepath + '" data-id="playback_' + userid + '" class="video_playback_icon ' + video + '" style="' + st + '"></a></td>';
                        $(tr).find('td').eq(3).after(video_icon);
                        let typeid_icon = '<td><button onclick="myFunction()" data-id=' + userid + ' class=" ' + icon + ' " style="border:none; ' + color + ';"></button></td>';
                        $(tr).find('td').eq(3).after(typeid_icon);

                        // Get Module Name from element.
                        let element = document.querySelector('.page-header-headings h1'); // Selects the h1 element within the .page-header-headings class
                        let textContent = element.textContent; // Extracts the text content from the h1 element

                        // Calculate and format total time
                        let total_time_seconds = data.res.total_time_seconds;
                        let hours = Math.floor(total_time_seconds / 3600).toString().padStart(2, '0');
                        let minutes = Math.floor((total_time_seconds % 3600) / 60).toString().padStart(2, '0');
                        let seconds = (total_time_seconds % 60).toString().padStart(2, '0');
                        let formattedTime = `${hours}:${minutes}:${seconds}`;

                        var context = {
                            tabledata: data.res,
                            formattime: formattedTime,
                            moduletitle: textContent,
                            page: score_setting,
                            userid: userid,
                        };
                        templates
                            .render("tiny_cursive/pop_modal", context)
                            .then(function (html) {
                                $("body").append(html);
                            }).catch(e => window.console.error(e));
                    });
                } catch (error) {
                    window.console.error(error);
                }

                $(".popup_item").on('click', function () {
                    let mid = $(this).data('id');
                    $("#" + mid).show();
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
        }
    };

    return usersTable;
});