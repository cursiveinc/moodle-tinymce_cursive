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
      init: function (score_setting,showcomment) {
        str
          .get_strings([
            { key: "confidence_threshold", component: "tiny_cursive" },
          ]).done(function () {
            usersTable.appendTable(score_setting,showcomment);
          });
      },
      appendTable: function(score_setting){
        let sub_url= window.location.href;
        let parm = new URL(sub_url);
        let h_tr=$('thead').find('tr').get()[0];
        $(h_tr).find('th').eq(3).after('<th>TypeID</th><th>Stats</th>');
        $('tbody').find( "tr" ).get().forEach(function(tr) {
          let td_user=$(tr).find("td").get()[0];
         let userid=$(td_user).find("input[type='checkbox']").get()[0].value;
          let cmid=parm.searchParams.get('id');
          var chart="fa fa-area-chart popup_item";
          var st="font-size:24px;color:black;border:none";
          let thunder_icon='<td><button  data-id='+userid+' class="'+chart+'" style="'+st+'"></button></td>';
          $(tr).find('td').eq(3).after(thunder_icon);
          let args={id: userid,modulename:"assign",cmid:cmid} ;
          let methodname='cursive_user_list_submission_stats';
          let com=AJAX.call([{ methodname ,args }]);
          try {
          com[0].done(function (json) {
            var data = JSON.parse(json);
          var score=data.score;
         var icon='fa fa-circle-o';
         var color='font-size:24px;color:black';
          if(score>=score_setting){
              icon='fa fa-check-circle typeid';
              color='font-size:24px;color:green';
          }else if(score<score_setting){
              icon='fa fa-question-circle typeid';
              color='font-size:24px;color:#A9A9A9';
          }else{
              icon='fa fa-circle-o typeid';
              color='font-size:24px;color:black';
            }
          let close_icon='<td><button  data-id='+userid+' class=" '+icon+' " style="border:none; '+color+';"></button></td>';
          $(tr).find('td').eq(3).after(close_icon);
            var context = {
              tabledata: data,
              page: score_setting,
            };
            templates
              .render("tiny_cursive/pop_modal", context)
              .then(function (html) {
                $("body").append(html);
              }).catch(e=>window.console.log(e));
          });
        } catch (error) {
            window.console.log(error);
          }
          $(".popup_item").on('click',function () {
            $(".modal").hide();
            let mid =$(this).data('id');
          $("#"+mid).show();
          });
          $(".typeid").on('click',function () {
            $(".modal").hide();
            let mid =$(this).data('id');
          $("#typeid"+mid).show();
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