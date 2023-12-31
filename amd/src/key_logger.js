
/**
 * @module     tiny_cursive/plugin
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str", "core/templates"], function (
  $,
  AJAX,
  str,
  templates,
) {
  var usersTable = {
    init: function (page) {
      str
        .get_strings([
          { key: "field_require", component: "tiny_cursive" }
        ])
        .done(function (s) {
          window.console.log(s);
          $(document).ready(function($) {
            $(".popup_item").on('click',function () {
              var mid=$(this).data("id");
            $("#"+mid).show();
            });
            $(".link_icon").on('click',function () {
              var smid=$(this).data("id");
            $("#"+smid).show();
            window.console.log("#score"+smid);
            });
            $(".modal-close ").on('click',function () {$(".modal").hide();});
        } );
          usersTable.getusers(page);
        });
    },
    getusers: function (page) {
      $("#fgroup_id_buttonar").hide();
      $("#id_coursename").change(function () {
        var courseid = $(this).val();
        var promise1 = AJAX.call([
          {
            methodname: "cursive_get_user_list",
            args: {
              courseid: courseid,
            },
          },
        ]);
        promise1[0].done(function (json) {
          var data = JSON.parse(json);
          var context = {
            tabledata: data,
            page: page,
          };
          templates
            .render("tiny_cursive/user_list", context)
            .then(function (html, js) {
              window.console.log(js);
              var filtered_user = $("#id_username");
              filtered_user.html(html);
            });
        });

        var promise2 = AJAX.call([
          {
            methodname: "cursive_get_module_list",
            args: {
              courseid: courseid,
            },
          },
        ]);
        promise2[0].done(function (json) {
          var data = JSON.parse(json);
          var context = {
            tabledata: data,
            page: page,
          };
          templates
            .render("tiny_cursive/module_list", context)
            .then(function (html, js) {
              window.console.log(js);
              var filtered_user = $("#id_modulename");
              filtered_user.html(html);
            });
        });
      });
    },
  };
  return usersTable;
});
