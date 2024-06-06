
/**
 * @module     tiny_cursive/show_url_in_submission_detail
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
      init: function () {
        str
          .get_strings([
            { key: "field_require", component: "tiny_keylogger" },
          ])
          .done(function () {
            usersTable.appendSubmissionDetail();
          });
      },
      appendSubmissionDetail: function () {
            let sub_url= window.location.href;
          let parm = new URL(sub_url);
          let sid=parm.searchParams.get('sid');
          let cmid=parm.searchParams.get('id');
          let args={id: sid,modulename:"assign",'cmid':cmid} ;
          let methodname='cursive_get_assign_comment_link';
          let com=AJAX.call([{ methodname ,args }]);
          com[0].done(function (json) {
            var data = JSON.parse(json);
            if (data[0].usercomment!='comments') {$('.submissionfull').append('<div class="dropdown">');
            var tt='';
            data.forEach(element => {
              tt+='<li>'+element.usercomment +'</li>';
            });
            var p1='<div class="border alert alert-warning"><details><summary>Content Sources Provided by Student</summary>';
            $('.submissionfull').append(p1+' '+tt+'</details></div></div>');
          }
          });
          return com.usercomment;
      },
    };
   return usersTable;
  });