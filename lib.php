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
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use MoodleHQ\editor\tiny\plugins\cursive\classes\forms\fileupload;


/**
 * Given an array with a file path, it returns the itemid and the filepath for the defined filearea.
 *
 * @param  string $filearea The filearea.
 * @param  array  $args The path (the part after the filearea and before the filename).
 * @return array The itemid and the filepath inside the $args path, for the defined filearea.
 */
function tiny_cursive_get_path_from_pluginfile(string $filearea, array $args): array {
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
 * @package  mod_tiny_cursive
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function tiny_cursive_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=[]) {
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
 * @param context $context
 * @return void
 * @throws moodle_exception
 */
function tiny_cursive_extend_navigation_course(\navigation_node $navigation, \stdClass $course, \context $context) {
    global $CFG, $PAGE, $SESSION;

    $url = new moodle_url($CFG->wwwroot . '/lib/editor/tiny/plugins/cursive/tiny_cursive_report.php/', ['courseid' => $course->id]);
    $navigation->add(
        "Writing Activity Report",
        $url,
        navigation_node::TYPE_SETTING,
        null,
        null,
        new pix_icon('i/report', '')
    );
}

/**
 * tiny_cursive_extend_navigation
 *
 * @param global_navigation $navigation
 * @return void
 */
function tiny_cursive_extend_navigation(global_navigation $navigation) {
    global $CFG, $PAGE;
    if ($home = $navigation->find('home', global_navigation::TYPE_SETTING)) {
        $home->remove();
    }
}

/**
 * tiny_cursive_myprofile_navigation
 *
 * @param \core_user\output\myprofile\tree $tree
 * @param $user
 * @param $iscurrentuser
 * @param $course
 * @return void
 * @throws coding_exception
 * @throws moodle_exception
 */
function tiny_cursive_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
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
    $url = new moodle_url('/lib/editor/tiny/plugins/cursive/my_writing_report.php',
        ['id' => $user->id, 'course' => $course->id, 'mode' => 'cursive']);
    $node = new core_user\output\myprofile\node('reports', 'cursive', get_string('writing', 'tiny_cursive'), null, $url);
    $tree->add_node($node);
}

/**
 * upload_multipart_record
 *
 * @param $filerecord
 * @param $filenamewithfullpath
 * @return bool|string
 * @throws dml_exception
 */
function upload_multipart_record($filerecord, $filenamewithfullpath) {
    global $CFG;

    $moodleurl = get_config('tiny_cursive', 'host_url');
    $moodleurl = preg_replace("(^https?://)", "", $moodleurl);
    $moodleurl = 'https://' . $moodleurl;
    try {
        $token = get_config('tiny_cursive', 'secretkey');
        $remoteurl = get_config('tiny_cursive', 'python_server');
        $remoteurl = $remoteurl . "/upload_file";
        echo $remoteurl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new CURLFILE($filenamewithfullpath),
            'resource_id' => $filerecord->id,
            'person_id' => $filerecord->userid,
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'X-Moodle-Url:' . $moodleurl,
            'Content-Type: multipart/form-data',
        ]);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    $uploaded = false;
    if ($httpcode == 200) {
        $uploaded = true;
    }
    return $result;
}

/**
 * tiny_cursive_before_footer
 *
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 */
function tiny_cursive_before_footer() {
    global $PAGE, $COURSE, $USER, $DB, $CFG;
    $confidencethreshold = get_config('tiny_cursive', 'confidence_threshold');
    $confidencethreshold = !empty($confidencethreshold) ? $confidencethreshold : .65;
    $confidencethreshold = floatval($confidencethreshold);
    $showcomments = get_config('tiny_cursive', 'showcomments');
    $context = context_course::instance($COURSE->id);
    $userrole = '';
    if (has_capability('report/courseoverview:view', $context, $USER->id, false) || is_siteadmin()) {
        $userrole = 'teacher_admin';
    }
    $PAGE->requires->js_call_amd('tiny_cursive/settings', 'init', [$showcomments, $userrole]);
    if (get_config('tiny_cursive', 'showcomments')) {
        if ($PAGE->bodyid == 'page-mod-forum-discuss' || $PAGE->bodyid == 'page-mod-forum-view') {
            $PAGE->requires->js_call_amd('tiny_cursive/append_fourm_post',
                'init', [$confidencethreshold, $showcomments]);
        }
        if ($PAGE->bodyid == 'page-mod-assign-grader') {

            $PAGE->requires->js_call_amd('tiny_cursive/show_url_in_submission_grade',
                'init', [$confidencethreshold, $showcomments]);
        }
        if ($PAGE->bodyid == 'page-mod-assign-viewpluginassignsubmission') {
            $PAGE->requires->js_call_amd('tiny_cursive/show_url_in_submission_detail',
                'init', [$confidencethreshold, $showcomments]);
        }
    }
    if ($PAGE->bodyid == 'page-mod-assign-grading') {
        $PAGE->requires->js_call_amd('tiny_cursive/append_submissions_table', 'init', [$confidencethreshold, $showcomments]);
    }
    if ($PAGE->bodyid == 'page-mod-quiz-review') {
        $PAGE->requires->js_call_amd('tiny_cursive/show_url_in_quiz_detail', 'init', [$confidencethreshold, $showcomments]);
    }
    if ($PAGE->bodyid == 'page-course-view-participants') {
        $PAGE->requires->js_call_amd('tiny_cursive/append_participants_table', 'init', [$confidencethreshold, $showcomments]);
    }
}

/**
 * file_urlcreate
 *
 * @param $context
 * @param $user
 * @return false|string
 * @throws coding_exception
 */
function file_urlcreate ($context, $user) {
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
