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

require_once ("$CFG->libdir/externallib.php");
require_once ($CFG->dirroot . '/mod/quiz/locallib.php');
require_once (__DIR__.'/locallib.php');

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
    public static function get_user_list_parameters()
    {
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
    public static function get_user_list($page, $courseid)
{
    require_login();

    // Validate parameters
    $params = self::validate_parameters(
        self::get_user_list_parameters(),
        [
            'page' => $page,
            'courseid' => $courseid,
        ]
    );

    // Ensure the global context and capabilities library are available
    global $CFG, $DB;

    // Get course context
    $cm = $DB->get_record('course_modules', ['course' => $courseid], '*', MUST_EXIST);
    $context = context_module::instance($cm->id);
    self::validate_context($context);
    require_capability('tiny/cursive:view', $context);
   

    // Get the list of users in the course
    $users = tiny_cursive_data::get_courses_users($params);

    // Return the user list as JSON
    return json_encode($users);
}


    /**
     * get_user_list_returns
     *
     * @return external_value
     */
    public static function get_user_list_returns()
    {
        return new external_value(PARAM_TEXT, 'All quizzes');
    }

    // Service for quizzes list.

    /**
     * get_module_list_parameters
     *
     * @return external_function_parameters
     */
    public static function get_module_list_parameters()
    {
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
    public static function get_module_list($page, $courseid)
{
    require_login();

    // Validate parameters
    $params = self::validate_parameters(
        self::get_user_list_parameters(), // This should probably be self::get_module_list_parameters() if it exists
        [
            'page' => $page,
            'courseid' => $courseid,
        ]
    );

    // Ensure the global context and capabilities library are available
    global $CFG, $DB;

    // Include required libraries
    require_once($CFG->libdir . '/accesslib.php');

    // Get course context
    $cm = $DB->get_record('course_modules', ['course' => $courseid], '*', MUST_EXIST);
    $context = context_module::instance($cm->id);
    self::validate_context($context);
    require_capability('tiny/cursive:view', $context);
   

    // Get the list of modules in the course
    $modules = tiny_cursive_data::get_courses_modules($params);

    // Return the module list as JSON
    return json_encode($modules);
}


    /**
     * get_module_list_returns
     *
     * @return external_value
     */
    public static function get_module_list_returns()
    {
        return new external_value(PARAM_TEXT, 'All quizzes');
    }


    /**
     * cursive_json_func_parameters
     *
     * @return external_function_parameters
     */
    public static function cursive_json_func_parameters()
    {
        return new external_function_parameters(
            [
                'resourceId' => new external_value(PARAM_INT, 0, 'resourceId'),
                'key' => new external_value(PARAM_TEXT, 'key detail', false, 'key'),
                'keyCode' => new external_value(PARAM_INT, 'key code ', false, 'keycode'),
                'event' => new external_value(PARAM_TEXT, 'event', false, 'event'),
                'cmid' => new external_value(PARAM_INT, 0, 'cmid'),
                'modulename' => new external_value(PARAM_TEXT, 'quiz', 'modulename'),
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
        $resourceId = 0,
        $key = null,
        $keyCode = null,
        $event = 'keyUp',
        $cmid = 0,
        $modulename = 'quiz',
        $editorid = null
    ) {
        require_login();

        global $USER, $DB, $CFG;

        $params = self::validate_parameters(
            self::cursive_json_func_parameters(),
            [
                'resourceId' => $resourceId,
                'key' => $key,
                'keyCode' => $keyCode,
                'event' => $event,
                'cmid' => $cmid,
                'modulename' => $modulename,
                'editorid' => $editorid,
            ]
        );
        
        if ($params['resourceId'] == 0 && $params['modulename'] !== 'forum') {
            $params['resourceId'] = $params['cmid']; // For Quiz and Assignment there is no resourceid that's why cmid is resourceid.
        }

        $courseid = 0;

        $userdata = ['resourceId' => $params['resourceId'], 'key' => $params['key'], 'keyCode' => $params['keyCode'], 'event' => $params['event']];
        if ($params['cmid']) {
            $cm = $DB->get_record('course_modules', ['id' => $params['cmid']]);
            $courseid = $cm->course;
            $userdata["courseId"] = $courseid;

            // Get course context
            $context = context_module::instance($params['cmid']);
            self::validate_context($context);
            require_capability('tiny/cursive:write', $context);

        } else {
            $userdata["courseId"] = 0;
        }

        $timearr = explode('.', microtime("now") * 1000);
        $timestampinmilliseconds = $timearr[0];
        $userdata['unixTimestamp'] = $timestampinmilliseconds;
        $userdata["clientId"] = $CFG->wwwroot;
        $userdata["personId"] = $USER->id;
        $editoridarr = explode(':', $params['editorid']);
        if (count($editoridarr) > 1) {
            $uniqueid = substr($editoridarr[0] . "\n", 1);
            $slot = substr($editoridarr[1] . "\n", 0, -11);
            $quba = question_engine::load_questions_usage_by_activity($uniqueid);
            $question = $quba->get_question($slot, false);
            $questionid = $question->id;
        }
        $dirname = make_temp_directory('userdata');

        $fname = $USER->id . '_' . $params['resourceId'] . '_' . $params['cmid'] . '_attempt' . '.json';
        if ($questionid) {
            $fname = $USER->id . '_' . $params['resourceId'] . '_' . $params['cmid'] . '_' . $questionid . '_attempt' . '.json';
        }
        // File path.
        $filename = $dirname . '/' . $fname;

        $table = 'tiny_cursive_files';
        $inp = file_get_contents($filename);

        $temparray = null;
        if ($inp) {

            $temparray = json_decode($inp, true);
            array_push($temparray, $userdata);
            $filerec = $DB->get_record($table, ['cmid' => $params['cmid'], 'modulename' => $params['modulename'], 'userid' => $USER->id]);
            if ($questionid) {
                $filerec = $DB->get_record($table, [
                    'cmid' => $params['cmid'],
                    'modulename' => $params['modulename'],
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
            $dataobj->resourceid = $params['resourceId'];
            $dataobj->cmid = $params['cmid'];
            $dataobj->modulename = $params['modulename'];
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

        return $filename;
    }

    /**
     * cursive_json_func_returns
     *
     * @return external_value
     */
    public static function cursive_json_func_returns()
    {
        return new external_value(PARAM_TEXT, 'result');
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
                'quizname' => new external_value(PARAM_TEXT, 'quizname detail', false, 'quizname'),
                'username' => new external_value(PARAM_TEXT, 'username detail ', false, 'username'),
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
    public static function cursive_reports_func($courseid = 0, $quizname = null, $username = 'keyUp')
    {
        require_login();
    
        global $DB, $CFG;
    
        require_once($CFG->libdir . '/accesslib.php'); // Include accesslib.php for capability checks
    
    
        // Ensure the user has the capability to view the cursive reports
        if ($courseid) {
            $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
            $cm = $DB->get_record('course_modules', ['course' => $courseid]);
            $context = context_module::instance($cm->id);
            self::validate_context($context);
            require_capability("tiny/cursive:write", $context);
            
        }
    
        // You can add additional logic here if needed
    
        return "cursive reports";
    }
    


    /**
     * cursive_reports_func_returns
     *
     * @return external_value
     */
    public static function cursive_reports_func_returns()
    {
        return new external_value(PARAM_TEXT, 'result');
    }

    // User comments store.

    /**
     * cursive_user_comments_func_parameters
     *
     * @return external_function_parameters
     */
    public static function cursive_user_comments_func_parameters()
    {
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
     * @return bool
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
        global $DB, $USER,$CFG;
        require_login();


       $params = self::validate_parameters(
            self::cursive_user_comments_func_parameters(),
            [
                'modulename' => $modulename,
                'cmid' => $cmid,
                'resourceid' => $resourceid,
                'courseid' => $courseid,
                'usercomment' => $usercomment,
                'timemodified' => $timemodified,
                'editorid' => $editorid,
            ]
        );
        require_once($CFG->libdir . '/accesslib.php'); 
        // Capability check
        $context = context_module::instance($params['cmid']);
        self::validate_context($context);
        require_capability("tiny/cursive:write",$context);


        $userid = $USER->id;
        $editoridarr = explode(':', $params['editorid']);
        if (count($editoridarr) > 1) {
            $uniqueid = substr($editoridarr[0] . "\n", 1);
            $slot = substr($editoridarr[1] . "\n", 0, -11);
            $quba = question_engine::load_questions_usage_by_activity($uniqueid);
            $question = $quba->get_question($slot, false);
            $questionid = $question->id;
        }
        $dataobject = new stdClass();
        $dataobject->userid = $userid;
        $dataobject->cmid = $params['cmid'];
        $dataobject->modulename = $params['modulename'];
        $dataobject->resourceid = $params['resourceid'];
        $dataobject->courseid = $params['courseid'];
        $dataobject->questionid = $questionid ?? 0;
        $dataobject->usercomment = $params['usercomment'];
        $dataobject->timemodified = $params['timemodified'];

        try {
            $DB->insert_record('tiny_cursive_comments', $dataobject);
            return true;
        } catch (Exception $e) {
            echo $e;
            return false;
        }
    }

    /**
     * cursive_user_comments_func_returns
     *
     * @return external_value
     */
    public static function cursive_user_comments_func_returns()
    {
        return new external_value(PARAM_BOOL, 'All User Comments');
    }


    /**
     * cursive_approve_token_func_parameters
     *
     * @return external_function_parameters
     */
    public static function cursive_approve_token_func_parameters()
    {
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
    public static function cursive_approve_token_func($token)
{
    global $CFG;
    require_login();
    $params = self::validate_parameters(
        self::cursive_approve_token_func_parameters(),
        [
            'token' => $token,
        ]
    );
    // Check if the user has the required capability
    $context = context_system::instance(); // Assuming a system-wide capability check
    self::validate_context($context);
    require_capability('tiny/cursive:editsettings', $context);

    $remoteurl = get_config('tiny_cursive', 'python_server') . '/verify-token';
    $moodleurl = $CFG->wwwroot;

    try {
        // Use Moodle's cURL library
        $curl = new curl();
        $options = [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_HTTPHEADER' => [
                'Authorization: Bearer ' . $params['token'],
                'X-Moodle-Url: ' . $moodleurl,
                'Content-Type: multipart/form-data',
                'Accept: application/json'
            ]
        ];

        // Prepare POST fields
        $postfields = [
            'token' => $params['token'],
            'moodle_url' => $moodleurl
        ];

        // Execute the request
        $result = $curl->post($remoteurl, $postfields, $options);

        // Check for cURL errors
        if ($result === false) {
            throw new moodle_exception('curlerror', 'tiny_cursive', '', null, $curl->error);
        }
    } catch (Exception $e) {
        // Log the exception
        error_log("Error in cursive_approve_token_func: " . $e->getMessage());

        // Return a Moodle exception
        throw new moodle_exception('errorverifyingtoken', 'tiny_cursive', '', null, $e->getMessage());
    }

    return $result;
}


    /**
     * cursive_approve_token_func_returns
     *
     * @return external_value
     */
    public static function cursive_approve_token_func_returns()
    {
        return new external_value(PARAM_TEXT, 'Token Approved');
    }



    // Service for assignment comment list.


    /**
     * get_comment_link_parameters
     *
     * @return external_function_parameters
     */
    public static function get_comment_link_parameters()
    {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'cmid'),
                'questionid' => new external_value(PARAM_INT, 'questionid', false, 'questionid'),
                'userid' => new external_value(PARAM_INT, 'userid', false, 'questionid'),
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
    public static function get_comment_link($id, $modulename, $cmid = null, $questionid = null, $userid = null)
    {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/config.php');
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

        
        if ($params['modulename'] == 'quiz') {
            $data['filename'] = '';
            $conditions = ["resourceid" => $params['id'], "cmid" => $params['cmid'], "questionid" => $params['questionid'], 'userid' => $params['userid']];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);
            $sql = 'SELECT filename, userid, id AS file_id
                      FROM {tiny_cursive_files} 
                     WHERE resourceid = :resourceid AND cmid = :cmid
                           AND modulename = :modulename AND questionid=:questionid AND userid = :userid ';
            $filename = $DB->get_record_sql($sql,
                                [
                                    'resourceid' => $params['id'],
                                    'cmid' => $params['cmid'],
                                    'modulename' => $params['modulename'],
                                    'questionid' => $params['questionid'],
                                    "userid" => $params['userid'],
                                ]
                            );
            $filep = $CFG->dataroot . "/temp/userdata/" . $filename->filename;
            $data['filename'] = file_exists($filep) ? $filep : null;
            $data['questionid'] = $params['questionid'];

            if ($data['filename']) {
                $sql = 'SELECT id AS fileid 
                          FROM {tiny_cursive_files}
                         WHERE userid = :userid ORDER BY id ASC';
                $ffile = $DB->get_record_sql($sql, ['userid' => $filename->userid]);

                if ($ffile->fileid == $filename->file_id) {
                    $data['first_file'] = 1;
                } else {
                    $data['first_file'] = 0;
                }
            }

            if ($filename->file_id) {
                $sql = 'SELECT * 
                          FROM {tiny_cursive_user_writing} 
                         WHERE file_id = :fileid';
                $report = $DB->get_record_sql($sql, ['fileid' => $filename->file_id]);
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
            $conditions = ["resourceid" => $params['id']];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
                                 uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , 
                                 uf.modulename,uf.userid, uf.filename
                           FROM {tiny_cursive_user_writing} uw
                     INNER JOIN {tiny_cursive_files} uf ON uw.file_id = uf.id
                          WHERE uf.resourceid = :id
                                AND uf.cmid = :cmid
                                AND uf.modulename = :modulename";
            $data = $DB->get_record_sql($attempts,['id' => $params['id'], 'cmid' => $params['cmid'], 'modulename' => $params['modulename']]);

            if (!isset($data->filename)) {
                $sql = 'SELECT filename from {tiny_cursive_files} 
                         WHERE resourceid = :resourceid
                                AND cmid = :cmid
                                AND modulename = :modulename';
                $filename = $DB->get_record_sql($sql, ['resourceid' => $params['id'], 'cmid' => $params['cmid'], 'modulename' => $params['modulename']]);

                $filep = $CFG->dataroot . "/temp/userdata/" . $filename->filename;
                $data['filename'] = file_exists($filep) ? $filep : null;

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
    public static function get_comment_link_returns()
    {
        return new external_value(PARAM_TEXT, 'Comment Link');
    }

    /**
     * get_forum_comment_link_parameters
     *
     * @return external_function_parameters
     */
    public static function get_forum_comment_link_parameters()
    {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'cmid'),
            ]
        );
    }

    /**
     * get_forum_comment_link
     *
     * @param $id
     * @param $modulename
     * @param $cmid
     * @return string
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public static function get_forum_comment_link($id, $modulename, $cmid = null)
    {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/config.php');
        require_once ($CFG->dirroot . '/lib/accesslib.php');
        require_once ($CFG->dirroot . '/question/lib.php');
        require_login();

        $params = self::validate_parameters(
            self::get_forum_comment_link_parameters(),
            [
                'id' => (int) $id,
                'modulename' => $modulename,
                'cmid' => (int) $cmid,
            ]
        );
    

        $context = context_module::instance($params['cmid']);
        self::validate_context($context);
        require_capability('tiny/cursive:view', $context);
      
        $conditions = ["resourceid" => $params['id']];
        $table = 'tiny_cursive_comments';
        $recs = $DB->get_records($table, $conditions);
       
        $attempts = "SELECT uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
                            uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , 
                            uf.modulename,uf.userid, uf.filename,uw.file_id
                       FROM {tiny_cursive_user_writing} uw
                 INNER JOIN {tiny_cursive_files} uf ON uw.file_id = uf.id
                      WHERE uf.resourceid = :id
                            AND uf.cmid = :cmid
                            AND uf.modulename = :modulename";

        $data = $DB->get_record_sql($attempts, ['id' => $params['id'], 'cmid' => $params['cmid'], 'modulename' => $params['modulename']]);
       
        $data = (array) $data;
        $data['first_file'] = 0;

        if (!isset($data['filename'])) {
            $sql = 'SELECT filename,userid 
                      FROM {tiny_cursive_files} 
                     WHERE resourceid = :resourceid
                            AND cmid = :cmid
                            AND modulename = :modulename';
            $filename = $DB->get_record_sql($sql, ['resourceid' => $params['id'], 'cmid' => $params['cmid'], 'modulename' => $params['modulename']]);
            
            $filep = $CFG->dataroot . "/temp/userdata/" . $filename->filename;
            
            $data['filename'] = file_exists($filep) ? $filep : null;
            
            $sql = 'SELECT * 
                      FROM {tiny_cursive_files}
                     WHERE userid = :userid ORDER BY id ASC LIMIT 1';
            $firstfile = $DB->get_record_sql($sql, ['userid' => $filename->userid]);
            if ($firstfile == $filename->file_id) {
                $data['first_file'] = 1;
            }
        }
        $sql = 'SELECT * 
                  FROM {tiny_cursive_files}
                 WHERE userid = :userid ORDER BY id ASC LIMIT 1';
        $firstfile = $DB->get_record_sql($sql, ['userid' => $filename->userid]);
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
    public static function get_forum_comment_link_returns()
    {
        return new external_value(PARAM_TEXT, 'Comment Link');
    }

    /**
     * get_quiz_comment_link_parameters
     *
     * @return external_function_parameters
     */
    public static function get_quiz_comment_link_parameters()
    {
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
        global $DB, $CFG;
        require_once($CFG->dirroot . '/config.php');
        require_once ($CFG->dirroot . '/lib/accesslib.php');
        require_once ($CFG->dirroot . '/question/lib.php');
        require_login();
        $params = self::validate_parameters(
            self::get_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
                'questionid' => $questionid
            ]
        );

        $context = context_module::instance($params['cmid']);
        self::validate_context($context);
        require_capability('tiny/cursive:view', $context);


        if ($modulename == 'quiz') {
            $conditions = ["resourceid" => $params['id'], "cmid" => $params['cmid'], "questionid" => $params['questionid']];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $attempts = "SELECT uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
                                uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , 
                                uf.modulename,uf.userid, uf.filename
                           FROM {tiny_cursive_user_writing} uw
                     INNER JOIN {tiny_cursive_files} uf ON uw.file_id =uf.id
                          WHERE uf.resourceid = :id
                                AND uf.cmid = :cmid
                                AND uf.modulenam e= :modulename";
           $data = $DB->get_record_sql($attempts,['id' => $params['id'], 'cmid' => $params['cmid'], 'modulename' => $params['modulename']]);

            if (!isset($data->filename)) {
                $sql = 'SELECT filename 
                          FROM {tiny_cursive_files} 
                         WHERE resourceid = :resourceid
                               AND cmid = :cmid 
                               AND modulename = :modulename';
                $filename = $DB->get_record_sql($sql, ['resourceid' => $params['id'], 'cmid' => $params['cmid'], 'modulename' => $params['modulename']]);

                $filep = $CFG->dataroot . "/temp/userdata/" . $filename->filename;
                $data['filename'] = file_exists($filep) ? $filep : null;
            }

        } else {
            $conditions = ["resourceid" => $params['id']];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $attempts = "SELECT uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
                                uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , 
                                uf.modulename,uf.userid, uf.filename
                           FROM {tiny_cursive_user_writing} uw
                     INNER JOIN {tiny_cursive_files} uf ON uw.file_id =uf.id
                          WHERE uf.resourceid = :id
                                AND uf.cmid = :cmid
                                AND uf.modulename = :modulename ";
            $data = $DB->get_record_sql($attempts, ['id' => $params['id'], 'cmid' => $params['cmid'], 'modulename' => $params['modulename']]);

            if (!isset($data->filename)) {
                $sql = 'SELECT filename 
                          FROM {tiny_cursive_files} 
                         WHERE resourceid = :resourceid
                               AND cmid = :cmid
                               AND modulename = :modulename';
                $filename = $DB->get_record_sql($sql, ['resourceid' => $params['id'], 'cmid' => $params['cmid'], 'modulename' => $params['modulename']]);

                $filep = $CFG->dataroot . "/temp/userdata/" . $filename->filename;
                $data['filename'] = file_exists($filep) ? $filep : null;
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
    public static function get_quiz_comment_link_returns()
    {
        return new external_value(PARAM_TEXT, 'Comment Link');
    }

    /**
     * get_assign_comment_link_parameters
     *
     * @return external_function_parameters
     */
    public static function get_assign_comment_link_parameters()
    {
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
    public static function get_assign_comment_link($id, $modulename, $cmid)
    {
        global $DB;
        require_login();

         // Check if user has capability to view assignment comments
        $context = context_module::instance($cmid);
        self::validate_context($context);
        require_capability('tiny/cursive:view', $context);

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
    public static function get_assign_comment_link_returns()
    {
        return new external_value(PARAM_TEXT, 'Comment Link');
    }

    /**
     * get_assign_grade_comment_parameters
     *
     * @return external_function_parameters
     */
    public static function get_assign_grade_comment_parameters()
    {
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
    public static function get_assign_grade_comment($id, $modulename, $cmid)
    {
        global $DB, $CFG;
        require_login();

        // Check if user has capability to view assignment comments
        $context = context_module::instance($cmid);
        self::validate_context($context);
        require_capability('tiny/cursive:view',$context);
      
        $conditions = ["userid" => $id, 'modulename' => $modulename, 'cmid' => $cmid];
        $table = 'tiny_cursive_comments';
        $recs = $DB->get_records($table, $conditions);
        
        $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
                             uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid, 
                             uf.modulename, uf.userid, uw.file_id, uf.filename
                       FROM {tiny_cursive_user_writing} uw
                 INNER JOIN {tiny_cursive_files} uf ON uw.file_id = uf.id
                      WHERE uf.userid = :id
                            AND uf.cmid = :cmid
                            AND uf.modulename = :modulename";
        $data = $DB->get_record_sql($attempts,['id' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);
        $data = (array) $data;
        if (!isset($data['filename'])) {
            $sql = 'SELECT filename, id, userid
                      FROM {tiny_cursive_files}
                     WHERE userid = :userid
                            AND cmid = :cmid
                            AND modulename = :modulename';
            $filename = $DB->get_record_sql($sql, ['userid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

            $data['filename'] = $filename->filename;
            $data['file_id'] = $filename->id;
            $data['userid'] = $filename->userid;
        }
        if ($data['filename']) {

            $filep = $CFG->dataroot . "/temp/userdata/" . $data['filename'];
            $data['filename'] = file_exists($filep) ? $filep : null;

            $sql = 'SELECT id AS fileid 
                      FROM {tiny_cursive_files}
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
    public static function get_assign_grade_comment_returns()
    {
        return new external_value(PARAM_TEXT, 'Comment Link');
    }

    /**
     * get_user_list_submission_stats_parameters
     *
     * @return external_function_parameters
     */
    public static function get_user_list_submission_stats_parameters()
    {
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
    public static function get_user_list_submission_stats($id, $modulename, $cmid)
    {
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
        $context = context_module::instance($params['cmid']);
        self::validate_context($context);
        require_capability("tiny/cursive:view",$context);

        $rec = get_user_submissions_data($params['id'], $params['modulename'], $params['cmid']);

        return json_encode($rec);
    }

    /**
     * get_user_list_submission_stats_returns
     *
     * @return external_value
     */
    public static function get_user_list_submission_stats_returns()
    {
        return new external_value(PARAM_TEXT, 'Comment Link');
    }

    /**
     * cursive_filtered_writing_func_parameters
     *
     * @return external_function_parameters
     */
    public static function cursive_filtered_writing_func_parameters()
    {
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
    public static function cursive_filtered_writing_func($id)
    {
        global $DB, $USER;
        require_login();
        $userid = $USER->id;
        $params=[];

        $sql = "SELECT *
                  FROM {course_modules} 
                 WHERE course = :course LIMIT 1";
        $cm = $DB->get_record_sql($sql, ['course' => $id]);
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('tiny/cursive:view', $context);

        $attempts = "SELECT qa.resourceid AS attemptid,qa.timemodified,uw.score,uw.copy_behavior, u.id AS userid,
                            u.firstname, u.lastname, u.email,  qa.cmid AS cmid ,qa.courseid,qa.filename,uw.word_count,
                            uw.words_per_minute , uw.total_time_seconds ,uw.backspace_percent 
                       FROM {user} u
                 INNER JOIN {tiny_cursive_files} qa ON u.id = qa.userid
                  LEFT JOIN {tiny_cursive_user_writing} uw ON qa.id = uw.file_id
                      WHERE qa.userid! = 1";

        if ($userid != 0) {
            $attempts .= " AND  qa.userid = :userid";
            $params['userid'] = $userid;
        }
        if ($id != 0) {
            $attempts .= "  AND qa.courseid = :id";
            $params['id'] = $id;
        }
        $res = $DB->get_records_sql($attempts,$params);
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
    public static function cursive_filtered_writing_func_returns()
    {
        return new external_value(PARAM_TEXT, 'Comment Link');
    }



    /**
     * Method store_user_writing_parameters
     *
     * @return object [explicite description]
     */
    public static function store_user_writing_parameters()
    {
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
    public static function store_user_writing($person_id, $file_id, $character_count, $total_time_seconds, $characters_per_minute, $key_count, $keys_per_minute, $word_count, $words_per_minute, $backspace_percent, $copy_behavior, $score)
    {
        global $DB;

        try {

            $context = context_system::instance(); // Assuming a system-wide capability check
            self::validate_context($context);
            require_capability('tiny/cursive:editsettings', $context);

            $backspace_percent = round($backspace_percent, 4);
        
            // Check if the record exists
            $recordExists = $DB->record_exists('tiny_cursive_user_writing', ['file_id' => $file_id]);
        
            // Retrieve existing data or initialize a new stdClass object
            $data = $recordExists ? $DB->get_record('tiny_cursive_user_writing', ['file_id' => $file_id]) : new stdClass();
        
            // Populate data attributes
            $data->file_id = $file_id;
            $data->total_time_seconds = $total_time_seconds;
            $data->key_count = $key_count;
            $data->keys_per_minute = $keys_per_minute;
            $data->character_count = $character_count;
            $data->characters_per_minute = $characters_per_minute;
            $data->word_count = $word_count;
            $data->words_per_minute = $words_per_minute;
            $data->backspace_percent = $backspace_percent;
            $data->score = $score;
            $data->copy_behavior = $copy_behavior;
        
            // Update or insert the record
            if ($recordExists) {
                $DB->update_record('tiny_cursive_user_writing', $data);
            } else {
                $DB->insert_record('tiny_cursive_user_writing', $data);
            }
        
            // Return success status
            return [
                'status' => get_string('success','tiny_cursive'),
                'message' => get_string('data_save','tiny_cursive'),
            ];
        } catch (dml_exception $e) {
            // Return failure status with error message
            return [
                'status' => get_string('failed','tiny_cursive'),
                'message' => $e->getMessage()
            ];
        }        
    }

    /**
     * Method store_user_writing_returns
     *
     * @return external_single_structure [explicite description]
     */
    public static function store_user_writing_returns()
    {
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
    public static function cursive_get_reply_json_parameters()
    {
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
    public static function cursive_get_reply_json($filepath)
    {
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
    static function storing_user_writing_param()
    {
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
            'copy_behavior' => new external_value(PARAM_FLOAT, 'copy_behavior', true, 'course_detail'),
            'score' => new external_value(PARAM_FLOAT, 'score', false, 0, true),
        ];
    }
}
