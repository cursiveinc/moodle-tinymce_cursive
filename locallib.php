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

function get_user_attempts_data($userid, $courseid, $moduleid, $orderby = 'id', $order = 'ASC', $perpage = 1, $limit = 5) {
    $attempts = [];
    global $DB;
    if ($orderby == 'id') {
        $odby = 'u.id';
    }
    if ($orderby == 'name') {
        $odby = 'u.firstname';
    }
    if ($orderby == 'email') {
        $odby = 'u.email';
    }
    if ($orderby == 'date') {
        $odby = 'uf.timemodified';
    }
    $attempts = " SELECT uf.id as fileid, u.id as usrid,uw.id as uniqueid, u.firstname,u.email,uf.courseid,
    uf.id as attemptid,uf.timemodified, uf.cmid as cmid,
    uf.filename,uf.id as fileid, uw.total_time_seconds as total_time_seconds,
    uw.key_count as key_count, uw.keys_per_minute as keys_per_minute,
    uw.character_count as character_count,
    uw.characters_per_minute as characters_per_minute,
    uw.word_count as word_count, uw.words_per_minute as words_per_minute,
    uw.backspace_percent as backspace_percent, uw.score as score,
    uw.copy_behavior as copy_behavior
      FROM  {tiny_cursive_files} uf
      INNER JOIN {user} u ON uf.userid =u.id
LEFT JOIN {tiny_cursive_user_writing} uw ON uw.file_id =uf.id
   WHERE uf.userid!=1 ";
    if ($userid != 0) {
        $attempts .= " AND  uf.userid = $userid";
    }
    if ($courseid != 0) {
        $attempts .= "  AND uf.courseid=$courseid";
    }

    if ($moduleid != 0) {
        $attempts .= "  AND uf.cmid=$moduleid";
    }

    $attempts .= " ORDER BY $odby ";
    $totalcount = 0;

    if ($limit) {
        $getdetailcount = $DB->get_records_sql($attempts);
        $totalcount = count($getdetailcount);
        $attempts .= " LIMIT $perpage , $limit ";
    }
    $attempts;
    $res = $DB->get_records_sql($attempts);
    $resncount = ['count' => $totalcount, 'data' => $res];
    return $resncount;
}

function get_user_writing_data($userid, $courseid, $moduleid, $orderby = 'id', $order = 'ASC', $perpage = '', $limit = '') {
    $attempts = [];
    global $DB;
    if ($orderby == 'id') {
        $odby = 'u.id';
    }
    if ($orderby == 'name') {
        $odby = 'u.firstname';
    }
    if ($orderby == 'email') {
        $odby = 'u.email';
    }
    if ($orderby == 'date') {
        $odby = 'uf.timemodified';
    }
    $attempts = "  SELECT uf.id as fileid, u.id as usrid,uw.id as uniqueid,
  u.firstname,u.email,uf.courseid,uf.resourceid as attemptid,uf.timemodified,
  uf.cmid as cmid,uf.filename,
  uw.total_time_seconds as total_time_seconds,
  uw.key_count as key_count,
  uw.keys_per_minute as keys_per_minute,
  uw.character_count as character_count,
  uw.characters_per_minute as characters_per_minute,
  uw.word_count as word_count,
  uw.words_per_minute as words_per_minute,
  uw.backspace_percent as backspace_percent,
  uw.score as score,
  uw.copy_behavior as copy_behavior
  FROM  {tiny_cursive_files} uf
  INNER JOIN {user} u ON uf.userid =u.id
  LEFT JOIN {tiny_cursive_user_writing} uw ON uw.file_id =uf.id
  WHERE uf.userid!=1";

    if ($userid != 0) {
        $attempts .= " AND  uf.userid = $userid";
    }
    if ($courseid != 0) {
        $attempts .= "  AND uf.courseid=$courseid";
    }

    if ($moduleid != 0) {
        $attempts .= "  AND uf.cmid=$moduleid";
    }

    $attempts .= " ORDER BY $odby $order";
    $totalcount = 0;
    if ($limit) {
        $getdetailcount = $DB->get_records_sql($attempts);
        $totalcount = count($getdetailcount);
        $attempts .= " LIMIT $perpage ,$limit ";
    }
    $res = $DB->get_records_sql($attempts);
    $resncount = ['count' => $totalcount, 'data' => $res];
    return $resncount;
}

function get_user_profile_data($userid, $courseid = 0) {
    $attempts = [];
    global $DB;
    $attempts = "SELECT  sum(uw.total_time_seconds) as total_time,sum(uw.word_count) as word_count
FROM {tiny_cursive_user_writing} uw
        INNER JOIN {tiny_cursive_files} uf
            ON uw.file_id =uf.id  where uf.userid =:userid";
    if ($courseid != 0) {
        $attempts .= "  AND uf.courseid=:courseid";
    }
    $res = $DB->get_record_sql($attempts, ['userid' => $userid, 'courseid' => $courseid]);
    return $res;

}

function get_user_submissions_data($resourceid, $modulename, $cmid, $courseid = 0) {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->dirroot."/lib/editor/tiny/plugins/cursive/lib.php");

    $attempts = [];
    $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
                         uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename
                 FROM {tiny_cursive_user_writing} uw
                      INNER JOIN {tiny_cursive_files} uf ON uw.file_id = uf.id
                 WHERE uf.userid = ". $resourceid ." AND uf.cmid = " . $cmid ." AND uf.modulename = '" . $modulename . "'";

    if ($courseid != 0) {
        $attempts .= "  AND uf.courseid=$courseid";
    }

    $res = $DB->get_record_sql($attempts);

    $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
                        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uw.file_id, uf.filename
                 FROM {tiny_cursive_user_writing} uw
                    INNER JOIN {tiny_cursive_files} uf ON uw.file_id =uf.id
                 WHERE uf.userid = ". $resourceid ." AND uf.cmid = ".$cmid. " AND uf.modulename='" . $modulename . "'";

    if ($courseid != 0) {
        $attempts .= "  AND uf.courseid = $courseid";
    }
    $data = $DB->get_record_sql($attempts);
    $data = (array)$data;
    if (!isset($data['filename'])) {
        $sql = 'SELECT id as fileid, userid, filename
                FROM {tiny_cursive_files} 
                WHERE userid = '. $resourceid .' AND cmid = :cmid AND modulename = :modulename';
        $filename = $DB->get_record_sql( $sql, ['userid' => $resourceid, 'cmid' => $cmid, 'modulename' => $modulename]);

        if ($filename){
            $context = context_system::instance();
            $data['filename'] = $filename->filename ?? '';
        }
    }
    if ($data['filename']){
        $sql = 'SELECT id as fileid
                FROM {tiny_cursive_files} 
                WHERE userid = :userid ORDER BY id ASC';
        $ffile = $DB->get_record_sql( $sql, ['userid' => $data->userid]);

        if ($ffile->fileid == $data->file_id){
            $data['first_file'] = 1;
        }
        else{
            $data['first_file'] = 0;
        }
    }
    $res = $data;

    $response = [
        'res' => $res,
    ];
    return $response;
}
