/**
 * @module     tiny_cursive/plugin
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str","core/templates"], function (
    $,
    AJAX,
    str,
    templates
  ) {
    var usersTable = {
      init: function (page) {
        str
          .get_strings([
            { key: "field_require", component: "tiny_cursive" },
          ])
          .done(function () {
            usersTable.appendTable(page);
          });
      },
      appendTable: function (page) {
        let h_tr=$('thead').find('tr').get()[0];
        $(h_tr).find('th').eq(8).after('<th>Stats</th><th>TypeID</th>');
        $('tbody').find( "tr" ).get().forEach(function(tr) {
          let td_a=$(tr).find("td").get()[8];
          let a_id=$(td_a).find("a").get()[0];
          let td_user=$(tr).find("td").get()[0];
          let userid=$(td_user).find("input").get()[0].value;
          let parm = new URL($(a_id).attr('href'));
          let cmid=parm.searchParams.get('id');
          let thunder_icon='<td><a href="#" data-id='+userid+' class="fa fa-bolt popup_item" style="font-size:24px"></a></td>';
          $(tr).find('td').eq(8).after(thunder_icon);
          let args={id: userid,modulename:"assign",cmid:cmid} ;
          let methodname='cursive_user_list_submission_stats';
          let com=AJAX.call([{ methodname ,args }]);
          window.console.log("reached here");
          try {
          com[0].done(function (json) {
            var data = JSON.parse(json);
          var score=data.score;
          var icon='fa fa-close';
          var color='font-size:36px;color:red';
          if(score>0.65){
              icon='fa fa-check-square';
              color='font-size:36px;color:green';
          }else if(score>=0.35){
              icon='fa fa-question';
              color='font-size:36px;color:orange';
          }else{
              icon='fa fa-close';
              color='font-size:36px;color:red';
            }
          let close_icon='<td><a href="#" class="'+icon+'" style="'+color+';"></a></td>';
          $(tr).find('td').eq(9).after(close_icon);
            var context = {
              tabledata: data,
              page: page,
            };
            templates
              .render("tiny_cursive/pop_modal", context)
              .then(function (html,js) {
                window.console.log(js);
                $(tr).find('td').eq(9).after(html);
              }).catch(e=>window.console.log(e));
          });
        } catch (error) {
            window.console.log(error);
          }
          $(".popup_item").on('click',function () {
            $(".modal").hide();
            let mid =$(this).data('id');
            window.console.log('userid'+mid);
          $("#"+mid).show();
          });
          $(window).on('click',function (e) {
            if(e.target.id=='modal-close'+userid){
              $("#"+userid).hide();
            }
          });
          return com.usercomment;
        });
      },
    };
    return usersTable;
  });