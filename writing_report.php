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
global $CFG, $DB, $USER, $PAGE,$USERS;
require_login(null, false);

if (isguestuser()) {
    // No guests here!
    redirect(new moodle_url('/'));
    die;
}
if (\core\session\manager::is_loggedinas()) {
    // No login-as users.
    redirect(new moodle_url('/user/index.php'));
    die;
}

$userid   = optional_param('userid', 0, PARAM_INT);//$USER->id;
$username   =$userid ;// $USER->id;
$PAGE->requires->js_call_amd('tiny_cursive/key_logger', 'init', array(1));
$PAGE->requires->jquery_plugin('jquery');
$orderby  = optional_param('orderby', 'id', PARAM_RAW);
$order  = optional_param('order', 'ASC', PARAM_RAW);
$page = optional_param('page', 0, PARAM_INT);
$limit = 5;
$perpage = $page * $limit;
$user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
//$personalcontext = context_user::instance($user->id);
$systemcontext = context_system::instance();
$linkurl = new moodle_url('/lib/editor/tiny/plugins/cursive/writing_report.php?userid='.$userid);
$linktext = get_string('questimereport', 'tiny_cursive'); 
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_title($linktext);
$PAGE->set_heading(fullname($user));
//require_capability('moodle/user:create', $systemcontext);
$PAGE->set_pagelayout('mypublic');
$PAGE->set_pagetype('user-profile');
$struser = get_string('student_writing_statics', 'tiny_cursive');
$PAGE->set_url('/user/profile.php', array('id' => $userid));
$PAGE->navbar->add($struser);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('student_writing_statics', 'tiny_cursive'));
$renderer = $PAGE->get_renderer('tiny_cursive');
$users=get_user_writing_data($username, null, null, $orderby, $order, $perpage, $limit );
$user_profile=get_user_profile_data($username);
echo $renderer->user_writing_report($users,$user_profile,$page, $limit, $linkurl);
echo $OUTPUT->footer();
?>