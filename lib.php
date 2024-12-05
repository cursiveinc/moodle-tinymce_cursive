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
 * Plugin functions for the tiny_cursive plugin.
 *
 * @package   tiny_cursive
 * @copyright 2024, CTI <info@cursivetechnology.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Given an array with a file path, it returns the itemid and the filepath for the defined filearea.
 *
 * @param array $args The path (the part after the filearea and before the filename).
 * @return array The itemid and the filepath inside the $args path, for the defined filearea.
 */
function tiny_cursive_get_path_from_pluginfile(array $args): array {
    // Cursive never has an itemid (the number represents the revision but it's not stored in database).
    array_shift($args);

    // Get the filepath.
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    return [
        'itemid' => 0,
        'filepath' => $filepath,
    ];
}

/**
 * Serves the tiny_cursive files.
 *
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @package  mod_tiny_cursive
 */
function tiny_cursive_pluginfile($context, $filearea, $args, $forcedownload, array $options = []) {
    $itemid = array_shift($args);
    $filename = array_pop($args);

    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    $fs = get_file_storage();

    $file = $fs->get_file($context->id, 'tiny_cursive', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * tiny_cursive_extend_navigation_course
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @return void
 * @throws moodle_exception
 */
function tiny_cursive_extend_navigation_course(\navigation_node $navigation, \stdClass $course) {
    global $CFG, $USER, $DB;
    require_once(__DIR__ . "/locallib.php");

    $url = new moodle_url($CFG->wwwroot . '/lib/editor/tiny/plugins/cursive/tiny_cursive_report.php', ['courseid' => $course->id]);
    $cmid = tiny_cursive_get_cmid($course->id);
    $cursive = tiny_cursive_status($course->id);

    if ($cmid && $cursive) {
        $context = context_module::instance($cmid);
        $hascap = has_capability("tiny/cursive:editsettings", $context);
        if ($hascap) {
            $navigation->add(
                get_string('wractivityreport', 'tiny_cursive'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                null,
                new pix_icon('i/report', '')
            );
        }
    }
}

/**
 * tiny_cursive_extend_navigation
 *
 * @param global_navigation $navigation
 * @return void
 */
function tiny_cursive_extend_navigation(global_navigation $navigation) {
    if ($home = $navigation->find('home', global_navigation::TYPE_SETTING)) {
        $home->remove();
    }
}


/**
 * Add a node to the myprofile navigation tree for writing reports.
 *
 * @param \core_user\output\myprofile\tree $tree Navigation tree to add node to
 * @param stdClass $user The user object
 * @param stdClass $course The course object
 * @return void
 * @throws coding_exception
 * @throws moodle_exception
 */
function tiny_cursive_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $course) {
    global $USER;
    if (empty($course)) {
        $course = get_fast_modinfo(SITEID)->get_course();
    }

    if (isguestuser() || !isloggedin()) {
        return;
    }

    if (\core\session\manager::is_loggedinas() || $USER->id != $user->id) {
        return;
    }

    $url = new moodle_url(
        '/lib/editor/tiny/plugins/cursive/my_writing_report.php',
        ['id' => $user->id, 'course' => isset($course->id) ? $course->id : "", 'mode' => 'cursive']
    );
    $node = new core_user\output\myprofile\node('reports', 'cursive', get_string('writing', 'tiny_cursive'), null, $url);
    $tree->add_node($node);
}

/**
 * Uploads a file record using multipart form data
 *
 * @param object $filerecord The file record object containing metadata
 * @param string $filenamewithfullpath Full path to the file to upload
 * @param string $wstoken Web service token for authentication
 * @param string $answertext Original submission text
 * @return bool|string Returns response from server or false on failure
 * @throws dml_exception
 */
function tiny_cursive_upload_multipart_record($filerecord, $filenamewithfullpath, $wstoken, $answertext) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/filelib.php');
    $moodleurl = get_config('tiny_cursive', 'host_url');
    $result = '';
    try {
        $token = get_config('tiny_cursive', 'secretkey');
        $remoteurl = get_config('tiny_cursive', 'python_server') . "/upload_file";
        $filetosend = '';

        $tempfilepath = tempnam(sys_get_temp_dir(), 'upload');

        $jsoncontent = json_decode($filerecord->content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON content in file.");
        }
            file_put_contents($tempfilepath, json_encode($jsoncontent));
            $filetosend = new CURLFILE($tempfilepath, 'application/json', 'uploaded.json');

            // Ensure the temporary file does not exceed the size limit.
        if (filesize($tempfilepath) > 16 * 1024 * 1024) {
            unlink($tempfilepath);
            throw new Exception("File exceeds the 16MB size limit.");
        }

        echo $remoteurl;

        $curl = new curl();
        $postdata = [
            'file' => $filetosend,
            'resource_id' => $filerecord->id,
            'person_id' => $filerecord->userid,
            'ws_token' => $wstoken,
            'originalsubmission' => $answertext,
        ];

        $headers = [
            'Authorization: Bearer ' . $token,
            'X-Moodle-Url: ' . $moodleurl,
            'Content-Type: multipart/form-data',
        ];

        $result = $curl->post($remoteurl, $postdata, [
            'CURLOPT_HTTPHEADER' => $headers,
            'CURLOPT_RETURNTRANSFER' => true,
        ]);

        $httpcode = $curl->get_info()['http_code'];

        if ($result === false) {
            echo "File not found: " . $filenamewithfullpath . "\n";
            echo "cURL Error: " . $curl->error . "\n";
        } else {
            echo "\nHTTP Status Code: " . $httpcode . "\n";
            echo "File Id: " . $filerecord->id . "\n";
            echo "response: " . $result . "\n";
        }

        // Remove the temporary file if it was created.
        if (isset($tempfilepath) && file_exists($tempfilepath)) {
            unlink($tempfilepath);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    return $result;
}

/**
 * Creates a URL for a file in the tiny_cursive file area
 *
 * @param \context $context The context object
 * @param stdClass $user The user object containing fileid
 * @return string|false Returns the download URL for the file, or false if no file found
 * @throws coding_exception
 */
function tiny_cursive_file_urlcreate($context, $user) {
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'tiny_cursive', 'attachment', $user->fileid, 'sortorder', false);

    foreach ($files as $file) {
        if ($file->get_filename() != '.') {
            $fileurl = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename(),
                true
            );
            // Display the image.
            $downloadurl = $fileurl->get_port() ?
                $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() :
                $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();
            return $downloadurl;
        }
    }
    return false;
}

/**
 * Gets the essay quiz responses for a specific user
 *
 * @param int $userid The ID of the user
 * @param int $courseid The ID of the course
 * @param int $resourceid The ID of the quiz attempt
 * @param string $modulename The name of the module ('quiz')
 * @param int $cmid The course module ID
 * @param int $questionid The ID of the essay question
 * @return string The response summary text for the essay question
 */
function tiny_cursive_get_user_essay_quiz_responses($userid, $courseid, $resourceid, $modulename, $cmid, $questionid) {
    global $DB;
    $sql = "SELECT q.name AS question_name, qna.questionsummary, qna.responsesummary
              FROM {question_attempt_steps} qas
              JOIN {question_attempts} qna ON qas.questionattemptid = qna.id
              JOIN {quiz_attempts} qa ON qna.questionusageid = qa.uniqueid
              JOIN {quiz} qz ON qa.quiz = qz.id
              JOIN {question} q ON qna.questionid = q.id
              JOIN {course_modules} cm ON qz.id = cm.instance AND cm.module = (
                   SELECT id FROM {modules} WHERE name = 'quiz')
             WHERE qa.userid = :userid
                   AND qz.course = :courseid
                   AND qa.id = :resourceid
                   AND cm.id = :cmid
                   AND q.id = :questionid
                   AND q.qtype = 'essay'
                   AND qas.state = 'complete'
          ORDER BY qa.attempt, qna.id, qas.sequencenumber";

    $result = $DB->get_record_sql(
        $sql,
        [
            'userid' => $userid,
            'courseid' => $courseid,
            'resourceid' => $resourceid,
            'modulename' => $modulename,
            'cmid' => $cmid,
            'questionid' => $questionid,
        ]
    );
    return $result->responsesummary ?? "";
}

/**
 * tiny_cursive_before_footer
 *
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 */
function tiny_cursive_before_footer() {
    global $PAGE, $COURSE, $USER, $CFG;
        require_once($CFG->dirroot . '/lib/editor/tiny/plugins/cursive/locallib.php');
        $cmid = isset($COURSE->id) ? tiny_cursive_get_cmid($COURSE->id) : 0;
        $cursive = tiny_cursive_status($COURSE->id);

    if (!empty($COURSE) && $cmid && $cursive) {

        $confidencethreshold = get_config('tiny_cursive', 'confidence_threshold');
        $confidencethreshold = !empty($confidencethreshold) ? floatval($confidencethreshold) : 0.65;
        $showcomments = get_config('tiny_cursive', 'showcomments');

        $context = context_course::instance($COURSE->id);
        $userrole = '';
        if (has_capability('report/courseoverview:view', $context, $USER->id, false) || is_siteadmin()) {
            $userrole = 'teacher_admin';
        }

        $PAGE->requires->js_call_amd('tiny_cursive/settings', 'init', [$showcomments, $userrole]);

        $context = context_module::instance($cmid);
        $capcheck = has_capability('tiny/cursive:writingreport', $context, $USER->id);

        if ($capcheck) {
            switch ($PAGE->bodyid) {
                case 'page-mod-forum-discuss':
                case 'page-mod-forum-view':
                    $PAGE->requires->js_call_amd(
                        'tiny_cursive/append_fourm_post',
                        'init',
                        [$confidencethreshold, $showcomments],
                    );
                    break;

                case 'page-mod-assign-grader':
                    $PAGE->requires->js_call_amd(
                        'tiny_cursive/show_url_in_submission_grade',
                        'init',
                        [$confidencethreshold, $showcomments],
                    );
                    break;

                case 'page-mod-assign-grading':
                    $PAGE->requires->js_call_amd(
                        'tiny_cursive/append_submissions_table',
                        'init',
                        [$confidencethreshold, $showcomments],
                    );
                    break;

                case 'page-mod-quiz-review':
                    $PAGE->requires->js_call_amd(
                        'tiny_cursive/show_url_in_quiz_detail',
                        'init',
                        [$confidencethreshold, $showcomments],
                    );
                    break;

                case 'page-course-view-participants':
                    $PAGE->requires->js_call_amd(
                        'tiny_cursive/append_participants_table',
                        'init',
                        [$confidencethreshold, $showcomments],
                    );
                    break;
            }
        }
    }
}

/**
 * Method tiny_cursive_get_user_onlinetext_assignments
 *
 * @param int $userid The ID of the user
 * @param int $courseid The ID of the course
 * @param string $modulename The name of the module ('assign')
 * @param int $cmid The course module ID
 * @return string The online text submission content
 */
function tiny_cursive_get_user_onlinetext_assignments($userid, $courseid, $modulename, $cmid) {
    global $DB;

    $sql = "SELECT cm.instance as assignmentid, ontext.onlinetext, :modulename AS modulename
              FROM {assign_submission} asub
              JOIN {assign} a ON asub.assignment = a.id
              JOIN {assignsubmission_onlinetext} ontext ON asub.id = ontext.submission
              JOIN {course_modules} cm ON a.id = cm.instance AND cm.module = (
                   SELECT id FROM {modules} WHERE name = 'assign')
             WHERE asub.userid = :userid
                   AND a.course = :courseid
                   AND asub.status = 'submitted'
                   AND cm.id = :cmid";

    $result =
        $DB->get_record_sql($sql, ['userid' => $userid, 'courseid' => $courseid, 'modulename' => $modulename, 'cmid' => $cmid]);
    return $result->onlinetext ?? "";
}

/**
 * Gets forum posts for a specific user
 *
 * @param int $userid The ID of the user
 * @param int $courseid The ID of the course
 * @param int $resourceid The ID of the forum post
 * @return string The message content of the forum post
 */
function tiny_cursive_get_user_forum_posts($userid, $courseid, $resourceid) {
    global $DB;

    $sql = "SELECT fp.id AS postid, fp.subject, fp.message
              FROM {forum_posts} fp
              JOIN {forum_discussions} fd ON fp.discussion = fd.id
              JOIN {forum} f ON fd.forum = f.id
             WHERE fp.userid = :userid
                   AND fd.course = :courseid
                   AND fp.id = :resourceid";
    $result = $DB->get_record_sql($sql, ['userid' => $userid, 'courseid' => $courseid, 'resourceid' => $resourceid]);

    return $result->message ?? "";
}
