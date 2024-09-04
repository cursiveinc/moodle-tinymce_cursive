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
 * @copyright  2024 CTI <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import MyModal from "./analytic_modal";
import { call as getContent } from "core/ajax";
import $ from 'jquery';
import * as Str from 'core/str';

export default class AnalyticEvents {

    createModal(userid, context, questionid = '', authIcon) {
        $('#analytics' + userid + questionid).on('click', function (e) {
            e.preventDefault();

            // Create Moodle modal
            MyModal.create({ templateContext: context }).then(modal => {
                $('#content' + userid + ' .table tbody tr:first-child td:nth-child(2)').html(authIcon);
                modal.show();
            }).catch(error => {
                console.error("Failed to create modal:", error);
            });
        });
    }

    analytics(userid, templates, context, questionid = '', replayInstances = null, authIcon) {
        $('body').on('click', '#analytic' + userid + questionid, function (e) {
            $('#rep' + userid + questionid).prop('disabled', false);
            e.preventDefault();
            $('#content' + userid).html($('<div>').addClass('d-flex justify-content-center my-5')
                .append($('<div>').addClass('tiny_cursive-loader')));
            if (replayInstances && replayInstances[userid]) {
                replayInstances[userid].stopReplay();
            }
            $('.tiny_cursive-nav-tab').find('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element

            templates.render('tiny_cursive/analytics_table', context).then(function (html) {
                $('#content' + userid).html(html);
                $('#content' + userid + ' .table tbody tr:first-child td:nth-child(2)').html(authIcon);

            }).fail(function (error) {
                console.error("Failed to render template:", error);
            });
        });
    }

    checkDiff(userid, fileid, questionid = '', replayInstances = null) {
        const nodata = document.createElement('p');
        nodata.classList.add('text-center', 'p-5', 'bg-light', 'rounded', 'm-5', 'text-primary');
        nodata.style.verticalAlign = 'middle';
        nodata.style.textTransform = 'uppercase';
        nodata.style.fontWeight = '500';
        nodata.textContent = "no data received yet";

        $('body').on('click', '#diff' + userid + questionid, function (e) {
            $('#rep' + userid + questionid).prop('disabled', false);
            e.preventDefault();
            $('#content' + userid).html($('<div>').addClass('d-flex justify-content-center my-5')
                .append($('<div>').addClass('tiny_cursive-loader')));
            $('.tiny_cursive-nav-tab').find('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element
            if (replayInstances && replayInstances[userid]) {
                replayInstances[userid].stopReplay();
            }
            if (!fileid) {
                $('#content' + userid).html(nodata);
                throw new Error('Missing file id or Difference Content not received yet');
            }
            getContent([{
                methodname: 'cursive_get_writing_differences',
                args: { fileid: fileid },
            }])[0].done(response => {
                let responsedata = JSON.parse(response.data);
                if (responsedata[0]) {
                    let submitted_text = atob(responsedata[0].submitted_text);

                    // Fetch the dynamic strings
                    Str.get_strings([
                        {key: 'original_text', component: 'tiny_cursive'},
                        {key: 'editspastesai', component: 'tiny_cursive'}
                    ]).done(strings => {
                        const originalTextString = strings[0];
                        const editsPastesAIString = strings[1];

                        const $legend = $('<div class="d-flex p-2 border rounded mb-2">');

                        // Create the first legend item
                        const $attributedItem = $('<div>', { class: 'tiny_cursive-legend-item' });
                        const $attributedBox = $('<div>', { class: 'tiny_cursive-box attributed' });
                        const $attributedText = $('<span>').text(originalTextString);
                        $attributedItem.append($attributedBox).append($attributedText);

                        // Create the second legend item
                        const $unattributedItem = $('<div>', { class: 'tiny_cursive-legend-item' });
                        const $unattributedBox = $('<div>', { class: 'tiny_cursive-box tiny_cursive_added' });
                        const $unattributedText = $('<span>').text(editsPastesAIString);
                        $unattributedItem.append($unattributedBox).append($unattributedText);

                        // Append the legend items to the legend container
                        $legend.append($attributedItem).append($unattributedItem);

                        let contents = $('<div>').addClass('tiny_cursive-comparison-content');
                        let textBlock2 = $('<div>').addClass('tiny_cursive-text-block').append(
                            $('<div>').attr('id', 'tiny_cursive-reconstructed_text').html(JSON.parse(submitted_text))
                        );

                        contents.append($legend, textBlock2);
                        $('#content' + userid).html(contents); // Update content
                    }).fail(error => {
                        console.error("Failed to load language strings:", error);
                        $('#content' + userid).html(nodata);
                    });
                } else {
                    $('#content' + userid).html(nodata);
                }
            }).fail(error => {
                $('#content' + userid).html(nodata);
                throw new Error('Error loading JSON file: ' + error.message);
            });
        });
    }

    replyWriting(userid, filepath, questionid = '', replayInstances = null) {
        $('body').on('click', '#rep' + userid + questionid, function (e) {
            $(this).prop('disabled', true);
            e.preventDefault();
            $('#content' + userid).html($('<div>').addClass('d-flex justify-content-center my-5')
                .append($('<div>').addClass('tiny_cursive-loader')));
            $('.tiny_cursive-nav-tab').find('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element
            if (replayInstances && replayInstances[userid]) {
                replayInstances[userid].stopReplay();
            }
            if (questionid) {
                video_playback(userid, filepath, questionid);
            } else {
                video_playback(userid, filepath);
            }
        });
    }

    formatedTime(data) {
        if (data.total_time_seconds) {
            let total_time_seconds = data.total_time_seconds;
            let hours = Math.floor(total_time_seconds / 3600).toString().padStart(2, 0);
            let minutes = Math.floor((total_time_seconds % 3600) / 60).toString().padStart(2, 0);
            let seconds = (total_time_seconds % 60).toString().padStart(2, 0);
            return `${hours}h ${minutes}m ${seconds}s`;
        } else {
            return "0h 0m 0s";
        }
    }

    authorshipStatus(firstFile, score, score_setting) {
        var icon = 'fa fa-circle-o';
        var color = 'font-size:32px;color:black';
        var score = parseFloat(score);

        if (firstFile) {
            icon = 'fa fa-solid fa-info-circle';
            color = 'font-size:32px;color:#000000';
        } else if (score >= score_setting) {
            icon = 'fa fa-check-circle';
            color = 'font-size:32px;color:green';
        } else if (score < score_setting) {
            icon = 'fa fa-question-circle';
            color = 'font-size:32px;color:#A9A9A9';
        }

        return $('<i>').addClass(icon).attr('style', color);
    }
}
