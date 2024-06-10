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
 * Plugin functions for individual student report.
 *
 * @package   tiny_cursive
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once('classes/forms/userreportform.php');
require_once('locallib.php');
global $CFG, $DB, $USER, $PAGE;
require_login();

if (isguestuser()) {
    redirect(new moodle_url('/'));
    die;
}
if (\core\session\manager::is_loggedinas()) {
    redirect(new moodle_url('/user/index.php'));
    die;
}

$userid = optional_param('userid',0,PARAM_INT);
if(optional_param('id',0,PARAM_INT)) {
    $userid=optional_param('id', 0, PARAM_INT);
}

$orderby = optional_param('orderby', 'id', PARAM_RAW);
$order = optional_param('order', 'ASC', PARAM_RAW);
$page = optional_param('page', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);


if(optional_param('course', 0, PARAM_INT) && !is_siteadmin($USER->id) && optional_param('id',0,PARAM_RAW) !== $USER->id) {
    $courseid=optional_param('course', 0, PARAM_INT);
}
$limit = 5;
$isvalid = false;

$context = \CONTEXT_SYSTEM::instance();
$haseditcapability = has_capability('tiny/cursive:view', $context);

if($courseid) {

$role=$DB->get_record('role',['shortname' => 'editingteacher']);
$roleid=$role->id;
$coursecontext= context_course::instance($courseid);
$isvalid=$DB->get_records('role_assignments', ['userid' => $USER->id, 'roleid' => $roleid,'contextid' => $coursecontext->id]);
}


if (!$haseditcapability && $userid != $USER->id && !$isvalid) {

    return redirect(new moodle_url('/course/index.php'), get_string('warning', 'tiny_cursive'));
}



$PAGE->requires->js_call_amd('tiny_cursive/key_logger', 'init', [1]);
$PAGE->requires->jquery_plugin('jquery');
$PAGE->requires->js_call_amd('tiny_cursive/cursive_writing_reports', 'init', []);

$perpage = $page * $limit;
$user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
$systemcontext = context_system::instance();
$linkurl = new moodle_url("/lib/editor/tiny/plugins/cursive/my_writing_report.php?userid=" . $userid);
$linktext = get_string('tiny_cursive', 'tiny_cursive');
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_title($linktext);
$PAGE->set_heading(fullname($user));
$PAGE->set_pagelayout('mypublic');
$PAGE->set_pagetype('user-profile');
$struser = get_string('student_writing_statics', 'tiny_cursive');
$PAGE->set_url('/user/profile.php', ['id' => $userid]);
$PAGE->navbar->add($struser);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('student_writing_statics', 'tiny_cursive'));
$renderer = $PAGE->get_renderer('tiny_cursive');
$attempts = get_user_attempts_data($userid, $courseid, null, $orderby, $order, $perpage, $limit);
$userprofile = get_user_profile_data($userid, $courseid);
echo $renderer->user_writing_report($attempts, $userprofile, $userid, $page, $limit, $linkurl);
echo $OUTPUT->footer();


