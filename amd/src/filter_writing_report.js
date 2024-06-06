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
          window.console.log("data", data.data);
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
