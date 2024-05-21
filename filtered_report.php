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
 * Tiny cursive plugin filtered report.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../../config.php');

global $CFG, $DB, $USER, $PAGE, $OUTPUT;

require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once('classes/forms/filterreportform.php');
require_once('locallib.php');

require_login(null, false);

$courseid = optional_param('coursename', 0, PARAM_INT);
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);

$PAGE->requires->js_call_amd('tiny_cursive/filter_writing_report', "init", [0]);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/cursive/filtered_report.php'));
echo $OUTPUT->header();
$mform = new filterreportform(null, [
    'coursename' => $courseid,
], 'post', '', ['class' => 'timer_report', 'id' => 'elstimerreport']);
$mform->display();
echo "<div id='id_username'></div>";
echo $OUTPUT->footer();
