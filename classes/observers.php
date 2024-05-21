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
 * Tiny cursive plugin observer.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author eLearningstack
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_cursive;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/config.php');

require_login();

/**
 * Tiny cursive plugin observer class.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author eLearningstack
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observers {
    /**
     * Tiny cursive plugin update comment observer.
     *
     * @param $event
     * @return void
     * @throws \dml_exception
     */
    public static function update_comment($event) {
        global $DB;
        $eventdata = $event->get_data();
        $table = 'tiny_cursive_comments';
        $conditions = ["userid" => $eventdata['userid'], "modulename" => 'forum', 'resourceid' => 0];
        $recs = $DB->get_records($table, $conditions);
        if ($recs) {
            foreach ($recs as $rec) {
                $dataobj = new \stdClass();
                $dataobj->userid = $eventdata['userid'];
                $dataobj->id = $rec->id;
                $dataobj->cmid = $eventdata['contextinstanceid'];
                $dataobj->courseid = $eventdata['courseid'];
                $dataobj->resourceid = $eventdata['objectid'];
                $DB->update_record($table, $dataobj, true);
            }
        }
    }

    /**
     * Tiny cursive plugin update cursive files observer.
     *
     * @param $event
     * @return void
     * @throws \dml_exception
     */
    public static function update_cursive_files($event) {
        global $DB, $CFG;
        $eventdata = $event->get_data();
        $table = 'tiny_cursive_files';
        $conditions = [
            "userid" => $eventdata['userid'],
            "modulename" => 'forum',
            'resourceid' => 0,
        ];
        $recs = $DB->get_records($table, $conditions);
        if ($recs) {
            foreach ($recs as $rec) {
                $userid = $eventdata['userid'];
                $cmid = $eventdata['contextinstanceid'];
                $resourceid = $eventdata['objectid'];
                $dirname = $CFG->dirroot . '/lib/editor/tiny/plugins/cursive/userdata/';
                $fname = $userid . '_' . $resourceid . '_' . $cmid . '_attempt' . '.json';
                $sourcefile = $dirname . $rec->filename;
                $desfilename = $dirname . $fname;
                $inp = file_get_contents($desfilename);
                $temparray = null;
                if ($inp) {
                    $temparray = json_decode($inp, true);
                    $merged = json_encode(
                        array_merge($temparray, json_decode(file_get_contents($sourcefile))));
                    file_put_contents($desfilename, $merged);
                    unlink($sourcefile);
                    $DB->delete_records($table, ['id' => $rec->id]);
                } else {
                    rename($sourcefile, $desfilename);
                    $dataobj = new \stdClass();
                    $dataobj->userid = $userid;
                    $dataobj->id = $rec->id;
                    $dataobj->cmid = $cmid;
                    $dataobj->courseid = $eventdata['courseid'];
                    $dataobj->resourceid = $resourceid;
                    $dataobj->filename = $fname;
                    $DB->update_record($table, $dataobj, true);
                }
            }
        }
    }

    /**
     * Tiny cursive plugin login observer.
     *
     * @param \mod_forum\event\post_created $event
     * @return void
     * @throws \dml_exception
     */
    public static function observer_login(\mod_forum\event\post_created $event) {
        self::update_comment($event);
        self::update_cursive_files($event);
    }

    /**
     * Tiny cursive plugin post updated observer.
     *
     * @param \mod_forum\event\post_updated $event
     * @return void
     * @throws \dml_exception
     */
    public static function post_updated(\mod_forum\event\post_updated $event) {
        self::update_comment($event);
        self::update_cursive_files($event);
    }

    /**
     * Tiny cursive plugin discussion created observer.
     *
     * @param \mod_forum\event\discussion_created $event
     * @return void
     * @throws \dml_exception
     */
    public static function discussion_created(\mod_forum\event\discussion_created $event) {

        global $DB;
        $eventdata = $event->get_data();
        $objectid = $eventdata['objectid'];
        $discussionstable = 'forum_discussions';
        $discussionsrec = $DB->get_record($discussionstable, ['id' => $objectid]);
        $table = 'tiny_cursive_comments';
        $conditions = [
            "userid" => $eventdata['userid'],
            "modulename" => 'forum',
            'resourceid' => 0,
        ];
        $recs = $DB->get_records($table, $conditions);
        if ($recs) {
            foreach ($recs as $rec) {
                $dataobj = new \stdClass();
                $dataobj->userid = $eventdata['userid'];
                $dataobj->id = $rec->id;
                $dataobj->cmid = $eventdata['contextinstanceid'];
                $dataobj->courseid = $eventdata['courseid'];
                $dataobj->resourceid = $discussionsrec->firstpost;
                $DB->update_record($table, $dataobj, true);
            }
        }
    }

    /**
     * Tiny cursive plugin submission created observer.
     *
     * @param \mod_assign\event\submission_created $event
     * @return void
     */
    public static function submission_created(\mod_assign\event\submission_created $event) {
        global $DB, $CFG, $PAGE, $USER;
        $eventdata = $event->get_data();

    }

    /**
     * Tiny cursive plugin assessable observer.
     *
     * @param \mod_assign\event\assessable_submitted $event
     * @return void
     */
    public static function assessable_submitted(\mod_assign\event\assessable_submitted $event) {
        global $DB, $CFG, $PAGE, $USER;
        $eventdata = $event->get_data();
    }
}
