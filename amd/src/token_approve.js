
/**
 * @module     tiny_cursive/plugin
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
            var message_alert='';
            if(data.status==true){
              message_alert="<span class='alert alert-success' role='alert'>"+data.message+"</span>";
            }else{
              message_alert="<span class='alert alert-danger' role='alert'>"+data.message+"</span>";
            }
            $("#token_message").html(message_alert);
          });

        });
      },
    };
    return usersTable;
  });