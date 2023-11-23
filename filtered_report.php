<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require(__DIR__ . '/../../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once('classes/forms/filterreportform.php');
require_once('locallib.php');
global $CFG, $DB, $USER, $PAGE,$USERS;
require_login(null, false);
$courseid   = optional_param('coursename', 0, PARAM_INT);
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);

$PAGE->requires->js_call_amd('tiny_cursive/filter_writing_report',"init", array(0));

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/cursive/filtered_report.php'));
echo $OUTPUT->header();
$mform = new filterreportform(null, array(
    'coursename' => $courseid,
), 'post', '', ['class' => 'timer_report','id' => 'elstimerreport']);
$mform->display();
echo "<div id='id_username'></div>";
    echo $OUTPUT->footer();