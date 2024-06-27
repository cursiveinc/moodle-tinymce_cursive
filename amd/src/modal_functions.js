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
 * @module     tiny_cursive/modal_functions
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "./replay"], function (
    $,
    Replay
) {
    var actions = {
        init: function () {

            $(".popup_item").on('click', function () {

                let mid = $(this).data('id');
                $("#" + mid).show();
            });
            $(".typeid").on('click', function () {
                let mid = $(this).data('id');
                $("#typeid" + mid).show();
            });
            $(".video_playback").on('click', function () {
                alert(1);
                let mid = $(this).data('id');
                var filepath = $(this).data("filepath");
                $("#playback" + mid).show();
                new Replay(
                    elementId = 'output',
                    filePath = filepath,
                    speed = 10,
                    loop = false,
                    controllerId = 'player'
                );
            });
        }
    };
    return actions;
});