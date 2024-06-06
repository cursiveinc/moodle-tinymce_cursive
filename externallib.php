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
 * Tiny cursive plugin.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tiny_cursive\tiny_cursive_data;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once('locallib.php');

/**
 * Tiny cursive plugin.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cursive_json_func_data extends external_api
{

    /**
     * get_user_list_parameters
     *
     * @return external_function_parameters
     */
    public static function get_user_list_parameters() {
        return new external_function_parameters(
            [
                'page' => new external_value(PARAM_INT, '', false),
                'courseid' => new external_value(PARAM_INT, 'Course id', false, 'course_detail'),
            ]
        );
    }

    /**
     * get_user_list
     *
     * @param $page
     * @param $courseid
     * @return false|string
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function get_user_list($page, $courseid) {
        require_login();
        $params = self::validate_parameters(
            self::get_user_list_parameters(),
            [
                'page' => $page,
                'courseid' => $courseid,
            ]
        );
        return json_encode(tiny_cursive_data::get_courses_users($params));
    }

    /**
     * get_user_list_returns
     *
     * @return external_value
     */
    public static function get_user_list_returns() {
        return new external_value(PARAM_RAW, 'All quizzes');
    }

    // Service for quizzes list.

    /**
     * get_module_list_parameters
     *
     * @return external_function_parameters
     */
    public static function get_module_list_parameters() {
        return new external_function_parameters(
            [
                'page' => new external_value(PARAM_INT, '', false),
                'courseid' => new external_value(PARAM_INT, 'Course id', false, 'course_detail'),
            ]
        );
    }

    /**
     * get_module_list
     *
     * @param $page
     * @param $courseid
     * @return false|string
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function get_module_list($page, $courseid) {
        require_login();
        $params = self::validate_parameters(
            self::get_user_list_parameters(),
            [
                'page' => $page,
                'courseid' => $courseid,
            ]
        );
        return json_encode(tiny_cursive_data::get_courses_modules($params));
    }

    /**
     * get_module_list_returns
     *
     * @return external_value
     */
    public static function get_module_list_returns() {
        return new external_value(PARAM_RAW, 'All quizzes');
    }


    /**
     * cursive_json_func_parameters
     *
     * @return external_function_parameters
     */
    public static function cursive_json_func_parameters() {
        return new external_function_parameters(
            [
                'resourceId' => new external_value(PARAM_INT, 0, 'resourceId'),
                'key' => new external_value(PARAM_RAW, 'key detail', false, 'key'),
                'keyCode' => new external_value(PARAM_RAW, 'key code ', false, 'keycode'),
                'event' => new external_value(PARAM_RAW, 'event', false, 'event'),
                'cmid' => new external_value(PARAM_INT, 0, 'cmid'),
                'modulename' => new external_value(PARAM_RAW, 'quiz', 'modulename'),
                'editorid' => new external_value(PARAM_TEXT, 'editorid', false, 'editorid'),
            ]
        );
    }


    /**
     * cursive_json_func
     *
     * @param $resourceid
     * @param $key
     * @param $keycode
     * @param $event
     * @param $cmid
     * @param $modulename
     * @param $editorid
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     * @throws file_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     * @throws stored_file_creation_exception
     */
    public static function cursive_json_func(
        $resourceid = 0,
        $key = null,
        $keycode = null,
        $event = 'keyUp',
        $cmid = 0,
        $modulename = 'quiz',
        $editorid = null
    ) {
        require_login();
      
        global $USER, $SESSION, $DB, $CFG;
        require_once ($CFG->libdir . '/filestorage/file_storage.php');

        $params = self::validate_parameters(
            self::cursive_json_func_parameters(),
            [
                'resourceId' => $resourceid,
                'key' => $key,
                'keyCode' => $keycode,
                'event' => $event,
                'cmid' => $cmid,
                'modulename' => $modulename,
                'editorid' => $editorid,
            ]
        );
        if($modulename==='forum') {
            if(!empty($resourceid) && $resourceid != 0 ){

                $parentData= $DB->get_record('tiny_cursive_files',['resourceid'=> $resourceid]);
                $cmid=$parentData->cmid;
            }else if(!empty($cmid) && $cmid != 0 && $resourceid == 0){
                $parentData= $DB->get_record('tiny_cursive_files',['resourceid'=> $cmid]);
                $cmid=$parentData->cmid ?? $cmid;
                $sql="SELECT id from {forum_posts} ORDER BY id DESC";
                
                $resourceid=$DB->get_record_sql($sql);
                $resourceid=$resourceid->id+1;
            }
        }else{
            if ($resourceid == 0) {
                     $resourceid = $cmid;
             }
        }
        
        $courseid = 0;
        // if ($resourceid == 0) {
        //     $resourceid = $cmid;
        // }
        $userdata = ['resourceId' => $resourceid, 'key' => $key, 'keyCode' => $keycode, 'event' => $event];
        if ($cmid) {
            $cm = $DB->get_record('course_modules', ['id' => $cmid]);
            $userdata["courseId"] = $cm->course;
            $courseid = $cm->course;
        } else {
            $userdata["courseId"] = 0;
        }

        $timearr = explode('.', microtime("now") * 1000);
        $timestampinmilliseconds = $timearr[0];
        $userdata['unixTimestamp'] = $timestampinmilliseconds;
        $userdata["clientId"] = $CFG->wwwroot;
        $userdata["personId"] = $USER->id;
        $editoridarr = explode(':', $editorid);
        if (count($editoridarr) > 1) {
            $uniqueid = substr($editoridarr[0] . "\n", 1);
            $slot = substr($editoridarr[1] . "\n", 0, -11);
            $quba = question_engine::load_questions_usage_by_activity($uniqueid);
            $question = $quba->get_question($slot, false);
            $questionid = $question->id;
        }
        $dirname = make_temp_directory('userdata');

        $fname = $USER->id . '_' . $resourceid . '_' . $cmid . '_attempt' . '.json';
        if ($questionid) {
            $fname = $USER->id . '_' . $resourceid . '_' . $cmid . '_' . $questionid . '_attempt' . '.json';
        }
        // File path.
        $filename = $dirname . '/' . $fname;
     
        // Insert in database.
        // var_dump($filename);
        // die;

        $table = 'tiny_cursive_files';

        // if (!file_exists($dirname)) {
        //     mkdir($dirname, 777);
        // }
        $inp = file_get_contents($filename);

        $temparray = null;
        if ($inp) {
            $temparray = json_decode($inp, true);
            array_push($temparray, $userdata);
            $filerec = $DB->get_record($table, ['cmid' => $cmid, 'modulename' => $modulename, 'userid' => $USER->id]);
            if ($questionid) {
                $filerec = $DB->get_record($table, [
                    'cmid' => $cmid,
                    'modulename' => $modulename,
                    'userid' => $USER->id,
                    'questionid' => $questionid,
                ]);
            }
            $filerec->uploaded = 0;
            $DB->update_record($table, $filerec);
        } else {
         
            $temparray[] = $userdata;
            $dataobj = new stdClass();
            $dataobj->userid = $USER->id;
            $dataobj->resourceid = $resourceid;
            $dataobj->cmid = $cmid;
            $dataobj->modulename = $modulename;
            $dataobj->courseid = $courseid;
            $dataobj->timemodified = time();
            $dataobj->filename = $fname;
            $dataobj->questionid = $questionid ?? 0;
            $dataobj->uploaded = 0;
            $DB->insert_record($table, $dataobj);
        }

        $jsondata = json_encode($temparray);

        if (is_array($temparray)) {
            file_put_contents($filename, $jsondata);
        }

        $filerec = $DB->get_record($table, ['cmid' => $cmid, 'modulename' => $modulename, 'userid' => $USER->id]);

        // Saving the file to moodledata.
        $context = context_system::instance();
        $fs = get_file_storage();
        if ($fs->file_exists($context->id, 'tiny_cursive', 'attachment', $filerec->id, '/', $fname)) {
            $filegetfrommoodledata = $fs->get_file(
                $context->id,
                'tiny_cursive',
                'attachment',
                $filerec->id,
                '/',
                $fname
            );

            $fs->delete_area_files($context->id, 'tiny_cursive', 'attachment', $filerec->id);

            $savefile = $fs->create_file_from_pathname(
                [
                    'contextid' => $context->id,
                    'component' => 'tiny_cursive',
                    'filearea' => 'attachment',
                    'itemid' => $filerec->id,
                    'filepath' => '/',
                    'filename' => $fname,
                ],
                $filename
            );
        } else {
            $savefile = $fs->create_file_from_pathname(
                [
                    'contextid' => $context->id,
                    'component' => 'tiny_cursive',
                    'filearea' => 'attachment',
                    'itemid' => $filerec->id,
                    'filepath' => '/',
                    'filename' => $fname,
                ],
                $filename
            );
        }
        return $filename;
    }

    /**
     * cursive_json_func_returns
     *
     * @return external_value
     */
    public static function cursive_json_func_returns()
    {
        return new external_value(PARAM_RAW, 'result');
    }


    /**
     * cursive_reports_func_parameters
     *
     * @return external_function_parameters
     */
    public static function cursive_reports_func_parameters()
    {
        return new external_function_parameters(
            [
                'coursename' => new external_value(PARAM_INT, 0, 'coursename'),
                'quizname' => new external_value(PARAM_RAW, 'quizname detail', false, 'quizname'),
                'username' => new external_value(PARAM_RAW, 'username detail ', false, 'username'),
            ]
        );
    }

    /**
     * cursive_reports_func
     *
     * @param $coursename
     * @param $quizname
     * @param $username
     * @return string
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function cursive_reports_func($coursename = 0, $quizname = null, $username = 'keyUp') {
        require_login();
        global $USER, $SESSION, $DB;
        $params = self::validate_parameters(
            self::cursive_reports_func_parameters(),
            [
                'resourceId' => $coursename,
                'key' => $quizname,
                'keyCode' => $username,
            ]
        );
        return "cursive reports";
    }

    /**
     * cursive_reports_func_returns
     *
     * @return external_value
     */
    public static function cursive_reports_func_returns() {
        return new external_value(PARAM_RAW, 'result');
    }

    // User comments store.

    /**
     * cursive_user_comments_func_parameters
     *
     * @return external_function_parameters
     */
    public static function cursive_user_comments_func_parameters() {
        return new external_function_parameters(
            [
                'modulename' => new external_value(PARAM_TEXT, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid'),
                'resourceid' => new external_value(PARAM_INT, 'resourceid'),
                'courseid' => new external_value(PARAM_INT, 'courseid'),
                'usercomment' => new external_value(PARAM_TEXT, 'usercomment'),
                'timemodified' => new external_value(PARAM_INT, 'timemodified'),
                'editorid' => new external_value(PARAM_TEXT, 'editorid'),
            ]
        );
    }

    /**
     * cursive_user_comments_func
     *
     * @param $modulename
     * @param $cmid
     * @param $resourceid
     * @param $courseid
     * @param $usercomment
     * @param $timemodified
     * @param $editorid
     * @return void
     * @throws coding_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function cursive_user_comments_func(
        $modulename,
        $cmid,
        $resourceid,
        $courseid,
        $usercomment,
        $timemodified,
        $editorid
    ) {
        global $DB, $USER;
        require_login();
        $userid = $USER->id;
        $editorid;
        $editoridarr = explode(':', $editorid);
        if (count($editoridarr) > 1) {
            $uniqueid = substr($editoridarr[0] . "\n", 1);
            $slot = substr($editoridarr[1] . "\n", 0, -11);
            $quba = question_engine::load_questions_usage_by_activity($uniqueid);
            $question = $quba->get_question($slot, false);
            $questionid = $question->id;
            $questionid;
        }
        $dataobject = new stdClass();
        $dataobject->userid = $userid;
        $dataobject->cmid = $cmid;
        $dataobject->modulename = $modulename;
        $dataobject->resourceid = $resourceid;
        $dataobject->courseid = $courseid;
        $dataobject->questionid = $questionid;
        $dataobject->usercomment = $usercomment;
        $dataobject->timemodified = $timemodified;

        try {
            $DB->insert_record('tiny_cursive_comments', $dataobject);
        } catch (Exception $e) {
            echo $e;
            die("error occored");
        }
    }

    /**
     * cursive_user_comments_func_returns
     *
     * @return external_value
     */
    public static function cursive_user_comments_func_returns() {
        return new external_value(PARAM_RAW, 'All User Comments');
    }


    /**
     * cursive_approve_token_func_parameters
     *
     * @return external_function_parameters
     */
    public static function cursive_approve_token_func_parameters() {
        return new external_function_parameters(
            [
                'token' => new external_value(PARAM_TEXT, 'userid'),
            ]
        );
    }

    /**
     * cursive_approve_token_func
     *
     * @param $token
     * @return bool|string
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function cursive_approve_token_func($token) {
        global $DB, $CFG;
        require_login();
        $remoteurl = get_config('tiny_cursive', 'python_server');
        $remoteurl = $remoteurl . '/verify-token';
        $moodleurl = $CFG->wwwroot;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $remoteurl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ["token" => $token, "moodle_url" => $moodleurl]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'X-Moodle-Url:' . $moodleurl,
                'Content-Type: multipart/form-data',
                'Accept:application/json',
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $result;
    }

    /**
     * cursive_approve_token_func_returns
     *
     * @return external_value
     */
    public static function cursive_approve_token_func_returns() {
        return new external_value(PARAM_RAW, 'Token Approved');
    }



    // Service for assignment comment list.


    /**
     * get_comment_link_parameters
     *
     * @return external_function_parameters
     */
    public static function get_comment_link_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'cmid'),
                'questionid' => new external_value(PARAM_RAW, 'questionid', false, 'questionid'),
                'userid' => new external_value(PARAM_RAW, 'userid', false, 'questionid'),
            ]
        );
    }

    /**
     * get_comment_link
     *
     * @param $id
     * @param $modulename
     * @param $cmid
     * @param $questionid
     * @param $userid
     * @return false|string
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function get_comment_link($id, $modulename, $cmid = null, $questionid = null, $userid = null) {
        require_once ('../../config.php');
        global $DB, $CFG;
        require_once ($CFG->dirroot . '/lib/accesslib.php');
        require_once ($CFG->dirroot . '/question/lib.php');
        require_login();
        $params = self::validate_parameters(
            self::get_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
                'questionid' => $questionid,
                'userid' => $userid,
            ]
        );
        if ($modulename == 'quiz') {
            $data['filename'] = '';
            $conditions = ["resourceid" => $id, "cmid" => $cmid, "questionid" => $questionid, 'userid' => $userid];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $filename = $DB->get_record_sql('select filename, userid, id as file_id
            from {tiny_cursive_files} where resourceid = :resourceid AND cmid = :cmid
            AND modulename = :modulename AND questionid=:questionid AND userid=:userid ',
                [
                    'resourceid' => $id,
                    'cmid' => $cmid,
                    'modulename' => $modulename,
                    'questionid' => $questionid,
                    "userid" => $userid,
                ]
            );
            $filep=$CFG->dataroot."/temp/userdata/".$filename->filename;
            $data['filename'] = file_exists($filep)?$filep:null; 
            $data['questionid'] = $questionid;

            if ($data['filename']) {
                $sql = 'SELECT id as fileid FROM {tiny_cursive_files}
                    WHERE userid = :userid ORDER BY id ASC';
                $ffile = $DB->get_record_sql($sql, ['userid' => $filename->userid]);

                if ($ffile->fileid == $filename->file_id) {
                    $data['first_file'] = 1;
                } else {
                    $data['first_file'] = 0;
                }
            }

            if ($filename->file_id) {
                $report = $DB->get_record_sql(
                    'select * from {tiny_cursive_user_writing} where file_id = :fileid',
                    [
                        'fileid' => $filename->file_id,
                    ]
                );
                $data['score'] = $report->score;
                $data['total_time_seconds'] = $report->total_time_seconds;
                $data['word_count'] = $report->word_count;
                $data['words_per_minute'] = $report->words_per_minute;
                $data['backspace_percent'] = $report->backspace_percent;
                $data['copy_behavior'] = $report->copy_behavior;
                $data['key_count'] = $report->key_count;
                $data['file_id'] = $report->file_id;
                $data['character_count'] = $report->character_count;
                $data['characters_per_minute'] = $report->characters_per_minute;
                $data['keys_per_minute'] = $report->keys_per_minute;
            }
            $usercomment = [];
            if ($recs) {
                foreach ($recs as $key => $rec) {
                    array_push($usercomment, $rec);
                }
                return json_encode(['usercomment' => $usercomment, 'data' => $data]);

            } else {
                return json_encode(['usercomment' => 'comments', 'data' => $data]);
            }
        } else {
            $conditions = ["resourceid" => $id];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename
        FROM {tiny_cursive_user_writing} uw
                INNER JOIN {tiny_cursive_files} uf
                    ON uw.file_id =uf.id
        where uf.resourceid = $id
        AND uf.cmid = $cmid
        AND uf.modulename='" . $modulename . "'";
                    $data = $DB->get_record_sql($attempts);

                    if (!isset($data->filename)) {
                        $filename = $DB->get_record_sql('select filename from {tiny_cursive_files} where resourceid = :resourceid
        AND cmid = :cmid
        AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

                        $filep=$CFG->dataroot."/temp/userdata/".$filename->filename;
                        $data['filename'] = file_exists($filep)?$filep:null; 
                       
            }

            $usercomment = [];
            if ($recs) {
                foreach ($recs as $key => $rec) {
                    array_push($usercomment, $rec);
                }
                return json_encode(['usercomment' => $usercomment, 'data' => $data]);

            } else {
                return json_encode(['usercomment' => 'comments', 'data' => $data]);
            }
        }
    }


    /**
     * get_comment_link_returns
     *
     * @return external_value
     */
    public static function get_comment_link_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    /**
     * get_forum_comment_link_parameters
     *
     * @return external_function_parameters
     */
    public static function get_forum_comment_link_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_RAW, 'cmid', false, 'cmid'),
            ]
        );
    }

    /**
     * get_forum_comment_link
     *
     * @param $id
     * @param $modulename
     * @param $cmid
     * @return void
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function get_forum_comment_link($id, $modulename, $cmid = null) {
        require_once ('../../config.php');
        global $DB, $CFG;
        require_once ($CFG->dirroot . '/lib/accesslib.php');
        require_once ($CFG->dirroot . '/question/lib.php');
        require_login();
        $params = self::validate_parameters(
            self::get_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );
        
        $context = context_module::instance($cmid);

        $conditions = ["resourceid" => $id];
        $table = 'tiny_cursive_comments';
        $recs = $DB->get_records($table, $conditions);

        $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename,uw.file_id
        FROM {tiny_cursive_user_writing} uw
            INNER JOIN {tiny_cursive_files} uf
                ON uw.file_id =uf.id
        where uf.resourceid = $id
        AND uf.cmid = $cmid
        AND uf.modulename='" . $modulename . "'";
       
        $data = $DB->get_record_sql($attempts);
   
        $data['first_file'] = 0;
        // var_dump($id,$cmid,$modulename);
        // die;
        if (!isset($data->filename)) {
        $filename = $DB->get_record_sql('select filename,userid from {tiny_cursive_files} where resourceid = :resourceid
        AND cmid = :cmid
        AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

            $filep=$CFG->dataroot."/temp/userdata/".$filename->filename;
           
            $data['filename'] = file_exists($filep)?$filep:null; 
            $firstfile = $DB->get_record_sql('select * from {tiny_cursive_files}
         where userid = :userid ORDER BY id ASC LIMIT 1', ['userid' => $filename->userid]);
            if ($firstfile == $filename->file_id) {
                $data['first_file'] = 1;
            }
        }
  
        $firstfile = $DB->get_record_sql('select * from {tiny_cursive_files}
         where userid = :userid ORDER BY id ASC LIMIT 1', ['userid' => $filename->userid]);
        if ($firstfile == $filename->file_id) {
            $data['first_file'] = 1;
        }

        $usercomment = [];
        if ($recs) {
            foreach ($recs as $key => $rec) {
                array_push($usercomment, $rec);
            }
            return json_encode(['usercomment' => $usercomment, 'data' => $data]);
        } else {
            return json_encode(['usercomment' => 'comments', 'data' => $data]);
        }

    }

    /**
     * get_forum_comment_link_returns
     *
     * @return external_value
     */
    public static function get_forum_comment_link_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    /**
     * get_quiz_comment_link_parameters
     *
     * @return external_function_parameters
     */
    public static function get_quiz_comment_link_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'cmid'),
                'questionid' => new external_value(PARAM_INT, 'questionid', false, 'questionid'),
            ]
        );
    }

    /**
     * get_quiz_comment_link
     *
     * @param $id
     * @param $modulename
     * @param $cmid
     * @param $questionid
     * @return false|string
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function get_quiz_comment_link(
        $id,
        $modulename,
        $cmid = null,
        $questionid = null
    ) {
        require_once ('../../config.php');
        global $DB, $CFG;
        require_once ($CFG->dirroot . '/lib/accesslib.php');
        require_once ($CFG->dirroot . '/question/lib.php');
        require_login();
        $params = self::validate_parameters(
            self::get_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );
        if ($modulename == 'quiz') {
            $conditions = ["resourceid" => $id, "cmid" => $cmid, "questionid" => $questionid];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename
        FROM {tiny_cursive_user_writing} uw
                INNER JOIN {tiny_cursive_files} uf
                    ON uw.file_id =uf.id
        where uf.resourceid = $id
        AND uf.cmid = $cmid
        AND uf.modulename='" . $modulename . "'";
            $data = $DB->get_record_sql($attempts);

            if (!isset($data->filename)) {
                $filename = $DB->get_record_sql('select filename from {tiny_cursive_files} where resourceid = :resourceid
            AND cmid = :cmid
            AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

                $filep=$CFG->dataroot."/temp/userdata/".$filename->filename;
                $data['filename'] = file_exists($filep)?$filep:null; 
            }

        } else {
            $conditions = ["resourceid" => $id];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename
        FROM {tiny_cursive_user_writing} uw
                INNER JOIN {tiny_cursive_files} uf
                    ON uw.file_id =uf.id
        where uf.resourceid = $id
        AND uf.cmid = $cmid
        AND uf.modulename='" . $modulename . "'";
            $data = $DB->get_record_sql($attempts);

            if (!isset($data->filename)) {
                $filename = $DB->get_record_sql('select filename from {tiny_cursive_files} where resourceid = :resourceid
  AND cmid = :cmid
  AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

                $filep=$CFG->dataroot."/temp/userdata/".$filename->filename;
                $data['filename'] = file_exists($filep)?$filep:null; 
            }
        }
        $usercomment = [];
        if ($recs) {
            foreach ($recs as $key => $rec) {
                array_push($usercomment, $rec);
            }
            return json_encode(['usercomment' => $usercomment, 'data' => $data]);

        } else {
            return json_encode(['usercomment' => 'comments', 'data' => $data]);
        }
    }

    /**
     * get_quiz_comment_link_returns
     *
     * @return external_value
     */
    public static function get_quiz_comment_link_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    /**
     * get_assign_comment_link_parameters
     *
     * @return external_function_parameters
     */
    public static function get_assign_comment_link_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'course_detail'),
            ]
        );
    }

    /**
     * get_assign_comment_link
     *
     * @param $id
     * @param $modulename
     * @param $cmid
     * @return false|string
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function get_assign_comment_link($id, $modulename, $cmid) {
        global $DB;
        require_login();
        $params = self::validate_parameters(
            self::get_assign_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );
        $recassignsubmission = $DB->get_record('assign_submission', ['id' => $id], '*', false);
        $userid = $recassignsubmission->userid;
        $conditions = ["userid" => $userid, 'modulename' => $modulename, 'cmid' => $cmid];
        $table = 'tiny_cursive_comments';
        $recs = $DB->get_records($table, $conditions);
        $usercomment = [];
        if ($recs) {
            foreach ($recs as $rec) {
                array_push($usercomment, $rec);
            }
            return json_encode($usercomment);

        } else {
            return json_encode([['usercomment' => 'comments']]);
        }
    }

    /**
     * get_assign_comment_link_returns
     *
     * @return external_value
     */
    public static function get_assign_comment_link_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    /**
     * get_assign_grade_comment_parameters
     *
     * @return external_function_parameters
     */
    public static function get_assign_grade_comment_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'course_detail'),
            ]
        );
    }

    /**
     * get_assign_grade_comment
     *
     * @param $id
     * @param $modulename
     * @param $cmid
     * @return false|string
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function get_assign_grade_comment($id, $modulename, $cmid) {
        global $DB, $CFG;
        require_login();
        $params = self::validate_parameters(
            self::get_assign_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );

        $conditions = ["userid" => $id, 'modulename' => $modulename, 'cmid' => $cmid];
        $table = 'tiny_cursive_comments';
        $recs = $DB->get_records($table, $conditions);
        $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename, uf.userid, uw.file_id, uf.filename
        FROM {tiny_cursive_user_writing} uw
                INNER JOIN {tiny_cursive_files} uf
                    ON uw.file_id =uf.id
        where uf.userid = $id
        AND uf.cmid = $cmid
        AND uf.modulename='" . $modulename . "'";
        $data = $DB->get_record_sql($attempts);
        $data = (array) $data;
        if (!isset($data['filename'])) {
            $filename = $DB->get_record_sql('select filename,id,userid from {tiny_cursive_files} where userid = :resourceid
        AND cmid = :cmid
        AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

            $filep=$CFG->dataroot."/temp/userdata/".$filename->filename;
            $data['filename'] = file_exists($filep)?$filep:null; 
            $data['file_id'] = $filename->id;
            $data['userid'] = $filename->userid;
        }
        if ($data['filename']) {
            $sql = 'SELECT id as fileid FROM {tiny_cursive_files}
                    WHERE userid = :userid ORDER BY id ASC';
            $ffile = $DB->get_record_sql($sql, ['userid' => $data['userid']]);

            if ($ffile->fileid == $data['file_id']) {
                $data['first_file'] = 1;
            } else {
                $data['first_file'] = 0;
            }
        }

        $usercomment = [];
        if ($recs) {
            foreach ($recs as $key => $rec) {
                array_push($usercomment, $rec);
            }
            return json_encode(['usercomment' => $usercomment, 'data' => $data]);

        } else {
            return json_encode(['usercomment' => 'comments', 'data' => $data]);
        }
    }

    /**
     * get_assign_grade_comment_returns
     *
     * @return external_value
     */
    public static function get_assign_grade_comment_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    /**
     * get_user_list_submission_stats_parameters
     *
     * @return external_function_parameters
     */
    public static function get_user_list_submission_stats_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'course_detail'),
                'filename' => new external_value(PARAM_TEXT, 'filename', false),
            ]
        );
    }

    /**
     * get_user_list_submission_stats
     *
     * @param $id
     * @param $modulename
     * @param $cmid
     * @return false|string
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function get_user_list_submission_stats($id, $modulename, $cmid) {
        global $DB;
        require_login();
        $params = self::validate_parameters(
            self::get_user_list_submission_stats_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );

        $rec = get_user_submissions_data($id, $modulename, $cmid);

        return json_encode($rec);
    }

    /**
     * get_user_list_submission_stats_returns
     *
     * @return external_value
     */
    public static function get_user_list_submission_stats_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    /**
     * cursive_filtered_writing_func_parameters
     *
     * @return external_function_parameters
     */
    public static function cursive_filtered_writing_func_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_TEXT, 'id', false, 'id'),
            ]
        );
    }

    /**
     * cursive_filtered_writing_func
     *
     * @param $id
     * @return false|string
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function cursive_filtered_writing_func($id) {
        global $DB, $USER;
        require_login();
        $userid = $USER->id;
        $params = self::validate_parameters(
            self::cursive_filtered_writing_func_parameters(),
            [
                'id' => $id,
            ]
        );
        $attempts = "SELECT qa.resourceid as attemptid,qa.timemodified,uw.score,uw.copy_behavior, u.id as userid,
       u.firstname, u.lastname, u.email,  qa.cmid as cmid ,qa.courseid,qa.filename,uw.word_count,
       uw.words_per_minute , uw.total_time_seconds ,uw.backspace_percent FROM {user} u
        INNER JOIN {tiny_cursive_files} qa ON u.id = qa.userid
        LEFT JOIN {tiny_cursive_user_writing} uw ON qa.id = uw.file_id
        WHERE qa.userid!=1";

        if ($userid != 0) {
            $attempts .= " AND  qa.userid = $userid";
        }
        if ($id != 0) {
            $attempts .= "  AND qa.courseid=$id";
        }
        $res = $DB->get_records_sql($attempts);
        $recs = [];
        foreach ($res as $key => $value) {
            $value->timemodified = date("l jS \of F Y h:i:s A", $value->timemodified);
            $value->icon = 'fa fa-circle-o';
            $value->color = 'grey';
            array_push($recs, $value);
        }
        $resncount = ['count' => count($res), 'data' => $recs];
        return json_encode($resncount);
    }

    /**
     * cursive_filtered_writing_func_returns
     *
     * @return external_value
     */
    public static function cursive_filtered_writing_func_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    
       
    /**
     * Method store_user_writing_parameters
     *
     * @return object [explicite description]
     */
    public static function store_user_writing_parameters() {
        return new external_function_parameters(self::storing_user_writing_param());
    }
    
    /**
     * Method store_user_writing
     *
     * @param $person_id $person_id [explicite description]
     * @param $file_id $file_id [explicite description]
     * @param $character_count $character_count [explicite description]
     * @param $total_time_seconds $total_time_seconds [explicite description]
     * @param $characters_per_minute $characters_per_minute [explicite description]
     * @param $key_count $key_count [explicite description]
     * @param $keys_per_minute $keys_per_minute [explicite description]
     * @param $word_count $word_count [explicite description]
     * @param $words_per_minute $words_per_minute [explicite description]
     * @param $backspace_percent $backspace_percent [explicite description]
     * @param $copy_behaviour $copy_behaviour [explicite description]
     * @param $copy_behavior $copy_behavior [explicite description]
     * @param $score $score [explicite description]
     *
     * @return array [explicite description]
     */
    public static function store_user_writing($person_id, $file_id, $character_count, $total_time_seconds, $characters_per_minute, $key_count, $keys_per_minute, $word_count, $words_per_minute, $backspace_percent, $copy_behaviour, $copy_behavior, $score) {
        global $DB;

        try {

            $backspace_percent = round($backspace_percent, 4);
            $sql = "INSERT INTO {tiny_cursive_user_writing}
        (file_id, total_time_seconds, key_count, keys_per_minute,character_count,characters_per_minute,
        word_count,words_per_minute,backspace_percent,score,copy_behavior)
        VALUES ($file_id,$total_time_seconds,$key_count,
        $keys_per_minute,$character_count,$characters_per_minute,
        $word_count,$words_per_minute,$backspace_percent,
        $score,$copy_behavior)";

            $DB->execute($sql);
            return [
                'status' => 'success',
                'message' => "Data saved successfully",
            ];
        } catch (Exception $e) {
            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }

    }
    
    /**
     * Method store_user_writing_returns
     *
     * @return external_single_structure [explicite description]
     */
    public static function store_user_writing_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'status message'),
            'message' => new external_value(PARAM_TEXT, 'message'),
        ]);
    }

    
    /**
     * Method cursive_get_reply_json_parameters
     *
     * @return  external_single_structure [explicite description]
     */
    public static function cursive_get_reply_json_parameters() {
        return new external_function_parameters([
            'filepath' => new external_value(PARAM_TEXT, 'filepath', true),
        ]);
    }
    
    /**
     * Method cursive_get_reply_json
     *
     * @param $filepath $filepath [explicite description]
     *
     * @return object [explicite description]
     */
    public static function cursive_get_reply_json($filepath) {
        $data = new stdClass;
        try {
            if (!file_exists($filepath)) {
                throw new Exception('File not found.');
            }
            $content = file_get_contents($filepath);
            if ($content === false) {
                throw new Exception('Failed to read file.');
            }
            $data->data = $content;
        } catch (Exception $e) {
            $data->data = $e->getMessage();
        }
        return $data;
    }
    
    /**
     * Method cursive_get_reply_json_returns
     *
     * @return external_single_structure [explicite description]
     */
    public static function cursive_get_reply_json_returns()
    {
        return new external_single_structure([
            'data' => new external_value(PARAM_TEXT, 'Reply Json')
        ]);
    }
    
    /**
     * Method storing_user_writing_param
     *
     * @return array [explicite description]
     */
    static function storing_user_writing_param() {
        return [
            'person_id' => new external_value(PARAM_INT, 'person or user id', true),
            'file_id' => new external_value(PARAM_INT, 'file_id', true, 'course_detail'),
            'character_count' => new external_value(PARAM_INT, 'character_count', true, 'course_detail'),
            'total_time_seconds' => new external_value(PARAM_INT, 'total_time_seconds', true, 'course_detail'),
            'characters_per_minute' => new external_value(PARAM_INT, 'characters_per_minute', true, 'course_detail'),
            'key_count' => new external_value(PARAM_INT, 'key_count', true, 'course_detail'),
            'keys_per_minute' => new external_value(PARAM_INT, 'keys per minutes', true),
            'word_count' => new external_value(PARAM_INT, 'word_count', true, 'course_detail'),
            'words_per_minute' => new external_value(PARAM_INT, 'words_per_minute', true, 'course_detail'),
            'backspace_percent' => new external_value(PARAM_FLOAT, 'backspace_percent', true, 'course_detail'),
            'copy_behaviour' => new external_value(PARAM_FLOAT, 'copy_behavior', true, 'course_detail'),
            'copy_behavior' => new external_value(PARAM_FLOAT, 'copy_behavior', true, 'course_detail'),
            'score' => new external_value(PARAM_FLOAT, 'score', true, 'course_detail'),
        ];
    }
}
