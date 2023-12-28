
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
          let args={id: attempt_id,modulename:"quiz"} ;
          let methodname='cursive_get_comment_link';
          let com=AJAX.call([{ methodname ,args }]);
          com[0].done(function (json) {
            var data = JSON.parse(json);
            let key='questionid';
            let results= data.reduce((hash, obj) =>{
                if(obj[key] === undefined) {return hash;}else{
                return Object.assign(hash, { [obj[key]]:( hash[obj[key]] || [] ).concat(obj)});}
                }, {});
                window.console.log(results);
           Object.entries(results).forEach(result => {
            var p1='<div class="border alert alert-warning"><details><summary>Content Sources Provided by Student</summary>';
            let qid=result[0];
            let  ss =$("a[href*="+qid+"]");
            if((ss.length== 0)){
              ss =$("input[value*="+qid+"]");
            }
            ss=ss.parent().parent().parent().find('.qtext');
            window.console.log(ss);
            result[1].forEach(element =>{
              p1=p1+'<p>'+element.usercomment+'</p>';
            });
            p1=p1+'</details></div>';
            ss.append( p1);
        });
          });
          return com.usercomment;
      },
    };
   return usersTable;
  });