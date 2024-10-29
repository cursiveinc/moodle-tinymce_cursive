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
 * @module     tiny_cursive/token_approve
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str"], function (
  $,
  AJAX,
  str,
) {
  var usersTable = {
    init: function (page) {
      str
        .get_strings([
          { key: "field_require", component: "tiny_cursive" },
        ])
        .done(function () {
          usersTable.getToken(page);
          usersTable.generateToken();
        });
    },
    getToken: function () {
      $("#approve_token").click(function () {
        var token = $('#id_s_tiny_cursive_secretkey').val();
        var promise1 = AJAX.call([
          {
            methodname: "cursive_approve_token",
            args: {
              token: token,
            },
          },
        ]);
        promise1[0].done(function (json) {
          var data = JSON.parse(json);
          var message_alert = '';
          if (data.status == true) {
            message_alert = "<span class='alert alert-success' role='alert'>" + data.message + "</span>";
          } else {
            message_alert = "<span class='alert alert-danger' role='alert'>" + data.message + "</span>";
          }
          $("#token_message").html(message_alert);
        });

      });
    },

    generateToken() {
      var generateToken = $('#generate_cursivetoken')
      generateToken.on('click', function (e) {
        e.preventDefault();
        var promise1 = AJAX.call([
          {
            methodname: "cursive_generate_webtoken",
            args: []
          },
        ]);
        promise1[0].done(function (data) {
          var message_alert = '';
          if (data.token) {
            $('#id_s_tiny_cursive_cursivetoken').val(data.token);
            message_alert = "<span class='text-success' role='alert'>Webservice Token Generation Success</span>";
          } else {
            message_alert = "<span class='text-danger' role='alert'>Webservice Token Generation Failed</span>";
          }
          $("#cursivetoken_").html(message_alert);
          setTimeout(() => {
            $("#cursivetoken_").empty();
          }, 3000);

        });
      });

    }
  };
  return usersTable;
});