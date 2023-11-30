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
require_once('classes/forms/userreportform.php');
require_once('locallib.php');
global $CFG, $DB, $USER, $PAGE,$USERS,$SESSION;

$courseid   = optional_param('coursename', 0, PARAM_INT);
$username   = optional_param('username', 0, PARAM_INT);
$moduleid   = optional_param('moduleid', 0, PARAM_INT);
$orderby  = optional_param('orderby', 'id', PARAM_RAW);
$order  = optional_param('order', 'ASC', PARAM_RAW);
$page = optional_param('page', 0, PARAM_INT);
$limit = 10;
$perpage = $page * $limit;
$linkurl = '/lib/editor/tiny/plugins/cursive/tiny_cursive_report.php';
$linkurl .= '?sesskey='.$USER->sesskey.'&_qf__userreportform=1&coursename='.$courseid.'&modulename='.$moduleid.'&username='.$username.'&orderby='.$orderby.'&submitbutton=Submit';
$PAGE->requires->js_call_amd('tiny_cursive/key_logger', 'init', array(1));
$PAGE->requires->css("/css/style.css");
$systemcontext = context_system::instance();
$linktext = get_string('tiny_cursive', 'tiny_cursive'); 
$PAGE->set_context($systemcontext);
//$PAGE->set_url($linkurl);
$PAGE->set_title($linktext);
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('tiny_cursive', 'tiny_cursive'));
require_login(); // teacher and admin can see this page
$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');
echo $OUTPUT->header();
$mform = new userreportform(null, array(
        'coursename' => $courseid,
        'username' => $username,
        'moduleid'=>$moduleid,
        'orderby'=>$orderby
 ), '', '', []);
    $mform->display();

    if($formdata = $mform->get_data()) {
        if($formdata->coursename){
            $context = context_course::instance($courseid);
            require_capability('mod/quiz:viewreports', $context);  
        }
        
        $users=get_user_attempts_data($formdata->username, $formdata->coursename, $formdata->modulename, $orderby, $order, $perpage, $limit);
        $renderer = $PAGE->get_renderer('tiny_cursive');
        echo   '<a target="_blank" href="'.$CFG->wwwroot.'/lib/editor/tiny/plugins/cursive/csvexport.php?courseid='.$courseid.'&moduleid='.$moduleid.'&userid='.$username.'" id="export" role="button" class="btn btn-primary mb-4" style="margin-right:50px;" >'.
        'Download cumulative Report'.'</a>';
        //echo $renderer->timer_report($users, $courseid, $username, $moduleid);
        echo $renderer->timer_report($users, $courseid, $page, $limit, $linkurl);

    }
        echo $OUTPUT->footer();