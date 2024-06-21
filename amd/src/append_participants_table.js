// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @module     tiny_cursive/append_participants_table
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str","core/templates","core/config"], function($, ajax,str,
  templates,mdlcfg) {
    var usersTable = {
      init: function (page) {
            usersTable.appendTable(page);
      },
      appendTable: function(page) {
        $(document).ready(function($) {
          let h_tr=$('thead').find('tr').get()[0];
            $(h_tr).find('th').eq(6).after('<th>Stats</th>');
            $('tbody').find( "tr" ).get().forEach(function(tr){
              let td_user=$(tr).find("td").get()[0];
              let userid=$(td_user).find("input").get()[0]?.id;
              userid=userid?.slice(4);
              var color='font-size:24px;color:black ;text-decoration : none';
              let link=mdlcfg.wwwroot+"/lib/editor/tiny/plugins/cursive/writing_report.php?userid="+userid;
              var icon='fa fa-area-chart';
              let thunder_icon='<td><a href="'+link+'" data-id='+userid+' class="'+icon+'" style="'+color+'"></a></td>';
              $(tr).find('td').eq(5).after(thunder_icon);
              try {
                var context = {
                  page: page,
                };
                templates
                  .render("tiny_cursive/pop_modal", context)
                  .then(function (html) {
                    $(tr).find('td').eq(5).after(html);
                  }).catch(e=> window.console.error(e));
            } catch (error) {
                window.console.error(error);
              }
            });
            $(".page-item ,.header ").on('click',function () {
            setTimeout(() => {
               usersTable.init();}, 1800);
            });
          });
        }
    };
    return usersTable;
});