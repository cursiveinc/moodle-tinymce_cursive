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
        $(h_tr).find('th').eq(6).after('<th>Stats..</th>');
        $('tbody').find( "tr" ).get().forEach(function(tr) {
          let td_user=$(tr).find("td").get()[0];
          let userid=$(td_user).find("input").get()[0].id;
          userid=userid.slice(4);
          var color='font-size:36px;color:black';
          let link="/lib/editor/tiny/plugins/cursive/writing_report.php?userid="+userid;
          var icon="fa fa-area-chart";
          let thunder_icon='<td><a href="'+link+'" data-id='+userid+' class='+icon+' style="'+color+'"></a></td>';
          $(tr).find('td').eq(5).after(thunder_icon);
          let methodname='cursive_user_list_submission_stats';
          let com=0;//AJAX.call([{ methodname ,args }]);
          window.console.log("reached here"+methodname);
          try {
            var context = {
              page: page,
            };
            templates
              .render("tiny_cursive/pop_modal", context)
              .then(function (html,js) {
                window.console.log(js);
                $(tr).find('td').eq(5).after(html);
              }).catch(e=>window.console.log(e));
        } catch (error) {
            window.console.log(error);
          }
          return com.usercomment;
        });
      },
    };
    return usersTable;
  });