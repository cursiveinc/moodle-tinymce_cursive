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
        //$("#fgroup_id_buttonar").hide();
        $('#page-mod-forum-discuss').find( "article" ).get().forEach(function(entry) {
          var ids=$("#"+entry.id).data("post-id");
          let args={id: ids,modulename:"forum"} ;
          let methodname='cursive_get_comment_link';
          let com=AJAX.call([{ methodname ,args }]);
          com[0].done(function (json) {
            var data = JSON.parse(json);
            if (data[0].usercomment!='comments') {
                $("#"+entry.id).find('#post-content-'+ids).append('<div class="dropdown">');
              var tt='';
              data.forEach(element => {
                tt+='<li>'+element.usercomment +'</li>';
            });
            var p1='<div class="border alert alert-warning"><details><summary>Content Sources Provided by Student</summary>';
            $("#"+entry.id).find('#post-content-'+ids).append(p1+' '+tt+'</details></div></div>');
          }
          });
          return com.usercomment;
      });
      $('#page-mod-forum-view').find( "article" ).get().forEach(function(entry) {
        var ids=$("#"+entry.id).data("post-id");
        let args={id: ids } ;
        let methodname='cursive_get_comment_link';
        let com=AJAX.call([{ methodname ,args }]);
        com[0].done(function (json) {
          var data = JSON.parse(json);
          var p1='<div class="border alert alert-warning"><summary>Content Sources Provided by Student</summary>';
          $("#"+entry.id).find('#post-content-'+ids).append( p1+" <p>"+data.usercomment+ids+"</p></div>" );
        });
        return com.usercomment;
    });
      },
    };
    return usersTable;
  });