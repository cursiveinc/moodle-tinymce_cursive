
/**
 * @module     tiny_cursive/plugin
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str","core/config"], function (
    $,
    AJAX,
    str,
  ) {
    var usersTable = {
      init: function () {
        str
          .get_strings([
            { key: "field_require", component: "tiny_cursive" },
          ])
          .done(function () {
            usersTable.appendSubmissionDetail();
          });
      },
      appendSubmissionDetail: function () {
       let sub_url= window.location.href;
          let parm = new URL(sub_url);
          let userid=parm.searchParams.get('userid');
          let cmid=parm.searchParams.get('id');
          let args={id: userid,modulename:"assign",'cmid':cmid} ;
          let methodname='cursive_get_assign_grade_comment';
          let com=AJAX.call([{ methodname ,args }]);
          com[0].done(function (json) {
            var data = JSON.parse(json);
            if (data[0].usercomment!='comments') {$('.activity-header').append('<div class="dropdown">');
            var tt='';
            data.forEach(element => {
              tt+= element.usercomment ;
            });
            var head="<summary>Content Sources Provided by Student</summary>";
            $('.fullwidth').append('<div class="border alert alert-warning"><details>'+head+' '+tt+'</details></div></div>');
          }
          });
          return com.usercomment;
      },
    };
   return usersTable;
  });