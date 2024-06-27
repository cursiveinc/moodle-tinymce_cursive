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
 * @module     tiny_cursive/filter_writing_report
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/templates"], function (
  $,
  AJAX,
  templates
) {
  return {
    init: function (page) {
      $("#id_coursename").change(function () {
        var promise1 = AJAX.call([
          {
            methodname: "cursive_filtered_writing",
            args: {
              id: $("#id_coursename").val(),
            },
          },
        ]);
        promise1[0].done(function (json) {
          var data = JSON.parse(json);
        
          var context = {
            data: data.data,
            page: page,
          };
          templates
            .render("tiny_cursive/user_table", context)
            .then(function (html) {
              var filtered_user = $("#id_username");
              filtered_user.html(html);
            });
        });
      });
      $(document).ready(function ($) {
        $(window).on('click', function (e) {
          var mid = $(e.target.parentNode).data("id");
          $("#score" + mid).show();
          $("#" + mid).show();
          if ($(e.target).hasClass('modal-close')) {
            $(".modal").hide();
          }
        });
      });
    },
  };
});
