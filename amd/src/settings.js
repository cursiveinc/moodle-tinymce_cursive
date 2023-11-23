
/**
 * @module     tiny_cursive/plugin
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/str"], function (
    $,
    str
  ) {
    var usersTable = {
      init: function (showcomments,user_role) {
        str
          .get_strings([
            { key: "tiny_cursive", component: "tiny_cursive" },
          ])
          .done(function () {
            usersTable.getToken(showcomments,user_role);
          });
      },
      getToken: function (showcomments,user_role) {
        $(function () {
          var html="<div id='body' class='body'>";
          $("body").append(html);
          $('#body').prop("class", user_role);
         window.console.log("Settings showcomment",showcomments);
         window.console.log(" Settings user_role ",user_role);
          if(showcomments==1){
            $('#body').prop("class", 'intervention '+user_role);
          }
       });
      },
    };
    return usersTable;
  });