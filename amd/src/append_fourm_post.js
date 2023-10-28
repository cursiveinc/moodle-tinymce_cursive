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
        window.console.log("Page fourm post");
        $('#page-mod-forum-discuss').find( "article" ).get().forEach(function(entry) {
          var ids=$("#"+entry.id).data("post-id");
          let args={id: ids,modulename:"forum"} ;
          let methodname='cursive_get_comment_link';
          let com=AJAX.call([{ methodname ,args }]);
          com[0].done(function (json) {
            var data = JSON.parse(json);
            if (data[0].usercomment!='comments') {
                $("#"+entry.id).find('#post-content-'+ids).append('<div class="dropdown">');
            //  var tx1='<button class="btn dropdown-toggle" type="button" data-toggle="dropdown">Content Sources</button>';
             // var tx2='<ul class="dropdown-menu">' ;
              var tt='';
              data.forEach(element => {
                tt+='<li>'+element.usercomment +'</li>';
             // $("#"+entry.id).find('#post-content-'+ids).append( '<li>'+element.usercomment +'</li>' );
              tt+='<li>'+element.usercomment +'</li>';
            });
            $("#"+entry.id).find('#post-content-'+ids).append('<details><summary>Content Sources</summary>'+tt+'</details>');
            //$("#"+entry.id).find('#post-content-'+ids).append(tx1+tx2+tt+'</ul></div>');
           // $("#"+entry.id).find('#post-content-'+ids).append( '</ul></div>');
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
          window.console.log(data);
          $("#"+entry.id).find('#post-content-'+ids).append( "<p><a class='post-link' href='#'>"+data.usercomment+ids+"</a></p>" );
        });
        return com.usercomment;
    });
      },
    };
    return usersTable;
  });