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
 * @module     tiny_cursive/append_participants_table
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/config", "core/str"], function ($, mdlcfg, Str) {
    var usersTable = {
        init: async function(page) {
            await usersTable.appendTable(page);
        },
        appendTable: async function () {
            $(document).ready(async function ($) {
                // Get the first row in the table header
                let h_tr = $('thead').find('tr').get()[0];
                let bodyid = $('body').attr('class');
                let classes = bodyid.split(' ');

                // Extract course ID
                let courseid = parseInt(classes.find((classname) => {
                    return classname.startsWith('course-');
                }).split('-')[1]);

                // Fetch string for stats header
                let statsString = await Str.get_string('stats', 'tiny_cursive');

                // Add the stats header if it doesn't already exist
                if (!$(h_tr).find('#stats').length) {
                    $(h_tr).find('th').eq(6).after('<th class="header c7" id="stats">' + statsString + '</th>');
                }

                // Iterate over each row in the table body
                $('tbody').find("tr").get().forEach(function (tr) {
                    let td_user = $(tr).find("td").get()[0];
                    let userid = $(td_user).find("input").get()[0]?.id;
                    userid = userid?.slice(4); // Extract userid from input id

                    if (userid) {
                        // Avoid duplicating the icon by checking if the icon is already added
                        if (!$(tr).find('td').eq(6).find('i').length) {
                            let color = 'font-size:24px;color:black;text-decoration:none';
                            let link = mdlcfg.wwwroot + "/lib/editor/tiny/plugins/cursive/writing_report.php?userid="
                                + userid + "&courseid=" + courseid;
                            let icon = 'fa fa-area-chart';

                            // Add the icon link to the 6th column
                            let thunder_icon = '<td><a href="' + link + '" data-id=' + userid + '>' +
                                '<i class="' + icon + '" aria-hidden="true" style="' + color + '"></i></a></td>';
                            $(tr).find('td').eq(5).after(thunder_icon); // Insert after the 5th column
                        }
                    }
                });

                // Add event listener for page change or other events triggering table update
                $(".page-item, .header").on('click', function () {
                    setTimeout(() => {
                        // Prevent multiple initializations by checking if already initialized
                        if (!$('#stats').length) {
                            usersTable.init(); // Initialize the table if needed
                        }
                    }, 1800); // Slight delay to ensure the DOM is fully updated
                });
            });
        }
    };
    return usersTable;
});
