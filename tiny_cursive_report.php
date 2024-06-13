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
 * Tiny cursive plugin writing report.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../../config.php');
global $CFG, $DB, $USER, $PAGE, $SESSION, $OUTPUT;

require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once('classes/forms/userreportform.php');
require_once('locallib.php');

require_login(); // Teacher and admin can see this page.
$context = \CONTEXT_SYSTEM::instance();
$haseditcapability = has_capability('tiny/cursive:view', $context);

if (!$haseditcapability) {

    return redirect(new moodle_url('/course/index.php'), get_string('warning', 'tiny_cursive'));
}

$courseid = optional_param('coursename', 0, PARAM_INT);
$username = optional_param('username', 0, PARAM_INT);
$moduleid = optional_param('moduleid', 0, PARAM_INT);
$orderby = optional_param('orderby', 'id', PARAM_RAW);
$order = optional_param('order', 'ASC', PARAM_RAW);
$page = optional_param('page', 0, PARAM_INT);
$limit = 100;
$perpage = $page * $limit;
$linkurl = '/lib/editor/tiny/plugins/cursive/tiny_cursive_report.php';
$linkurl .= '?sesskey=' . $USER->sesskey . '&_qf__userreportform=1&coursename=' .
    $courseid . '&modulename=' . $moduleid . '&username=' . $username .
    '&orderby=' . $orderby . '&submitbutton=Submit';
$PAGE->requires->js_call_amd('tiny_cursive/key_logger', 'init', [1]);
$PAGE->requires->css("/css/style.css");
$systemcontext = context_system::instance();
$linktext = get_string('tiny_cursive', 'tiny_cursive');
$PAGE->set_context($systemcontext);
$PAGE->set_title($linktext);
$PAGE->set_title($linktext);
$PAGE->set_url($linkurl);
$PAGE->set_heading(get_string('tiny_cursive', 'tiny_cursive'));

$PAGE->requires->jquery_plugin('jquery');
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->js_call_amd('tiny_cursive/cursive_writing_reports', 'init', []);
echo $OUTPUT->header();
$mform = new userreportform(null, [
    'coursename' => $courseid,
    'username' => $username,
    'moduleid' => $moduleid,
    'orderby' => $orderby,
], '', '', []);
$mform->display();

if ($formdata = $mform->get_data()) {
    if ($formdata->coursename) {
        $context = context_course::instance($courseid);
        require_capability('mod/quiz:viewreports', $context);
    }

    $users = get_user_attempts_data($formdata->username, $formdata->coursename,
        $formdata->modulename, $orderby, $order, $perpage, $limit);
    $renderer = $PAGE->get_renderer('tiny_cursive');
    echo '<a target="_blank" href="' . $CFG->wwwroot . '/lib/editor/tiny/plugins/cursive/csvexport.php?courseid=' . $courseid .
        '&moduleid=' . $moduleid . '&userid=' . $username . '" id="export" role="button"
        class="btn btn-primary mb-4" style="margin-right:50px;" >' .
        'Download cumulative Report' . '</a>';
    echo $renderer->timer_report($users, $courseid, $page, $limit, $linkurl);

}
echo $OUTPUT->footer();
