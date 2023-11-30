
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
            usersTable.appendSubmissionDetail(page);
          });
      },
      appendSubmissionDetail: function () {
       let sub_url= window.location.href;
          let parm = new URL(sub_url);
          let attempt_id=parm.searchParams.get('attempt');
          window.console.log("attempt_id"+attempt_id);
          let args={id: attempt_id,modulename:"quiz"} ;
          let methodname='cursive_get_comment_link';
          let com=AJAX.call([{ methodname ,args }]);
          com[0].done(function (json) {
            var data = JSON.parse(json);
            var p1='<div class="border alert alert-warning"><details><summary>Content Sources Provided by Student</summary>';
            if (data[0].usercomment!='comments') {$('.qtext').append( p1+' ' );
            data.forEach(element => {
              $('.qtext').append(element.usercomment);
            });
            $('.qtext').append('</details></div></div>');
          }
          });
          return com.usercomment;
      },
    };
   return usersTable;
  });