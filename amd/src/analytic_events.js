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
 * TODO describe module analytic_events
 *
 * @module     tiny_cursive/analytic_events
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import MyModal from "./analytic_modal";
import { call as getContent } from "core/ajax";
export default class analytic_events {

    createModal(userid, context, questionid = '') {
        $('#analytics' + userid + questionid).on('click', function (e) {
            e.preventDefault();

            // Create Moodle modal
            MyModal.create({ templateContext: context }).then(modal => {
                modal.show();
            }).catch(error => {
                console.error("Failed to create modal:", error);
            });

        });
    }

    analytics(userid, templates, context, questionid = '') {
        $('body').on('click', '#analytic' + userid + questionid, function (e) {
            e.preventDefault();
            $('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element

            templates.render('tiny_cursive/analytics_table', context).then(function (html) {
                $('#content' + userid).html(html);

            }).fail(function (error) {
                console.error("Failed to render template:", error);
            });
        });
    }

    checkDiff(userid, fileid, questionid = '', content = "diff") {
        // Event handler for '#diff' + userid
        const nodata = document.createElement('p');
        nodata.classList.add('text-center', 'p-5', 'bg-light', 'rounded', 'm-5', 'text-primary');
        nodata.style.verticalAlign = 'middle';
        nodata.style.textTransform = 'uppercase';
        nodata.style.fontWeight = '500';
        nodata.textContent = "no data received yet";
        
        $('body').on('click', '#diff' + userid + questionid, function (e) {
            e.preventDefault();
            $('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element
            if(!fileid) {
                $('#content' + userid).html(nodata);
                throw new Error('Missing file id or Difference Content not receive yet');
            } 
            getContent([{
                methodname: 'cursive_get_writing_differences',
                args: {
                    fileid: fileid,
                },
            }])[0].done(response => {
                let responsedata = JSON.parse(response.data);
                if (responsedata[0]) {
                    content = atob(responsedata[0].content)
                    $('#content' + userid).html(content); // Update content
                } else {
                    $('#content' + userid).html(nodata)
                }

            }).fail(error => { $('#content' + userid).html(nodata); throw new Error('Error loading JSON file: ' + error.message); });


        });
    }

    replyWriting(userid, filepath, questionid = '') {
        // Event handler for '#rep' + userid
        $('body').on('click', '#rep' + userid + questionid, function (e) {
            e.preventDefault();
            $('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element
            video_playback(userid, filepath);

        });
    }
}