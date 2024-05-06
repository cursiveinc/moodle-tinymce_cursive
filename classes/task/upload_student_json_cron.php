<?php
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
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

namespace tiny_cursive\task;

class upload_student_json_cron extends \core\task\scheduled_task {
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'tiny_cursive');
    }

    public function execute() {
        global $CFG, $DB;
        $table = 'tiny_cursive_files';
        $sql = "select * from {tiny_cursive_files} where timemodified > uploaded";
        $filerecords = $DB->get_records_sql($sql);
        $dirname = $CFG->dirroot . '/lib/editor/tiny/plugins/cursive/userdata/';
        require_once($CFG->dirroot . '/lib/editor/tiny/plugins/cursive/lib.php');
        foreach ($filerecords as $filerecord) {
            $filepath = $dirname . $filerecord->filename;
            $uploaded = upload_multipart_record($filerecord, $filepath);
            if ($uploaded) {
                $filerecord->uploaded = strtotime(date('Y-m-d H:i:s'));
                $DB->update_record($table, $filerecord);
                $uploaded = false;
            }
        }
    }
}
