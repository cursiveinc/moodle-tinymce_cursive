<?php

/**
 * Plugin functions for the tiny_cursive plugin.
 *
 * @package   tiny_cursive
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// require(__DIR__ . '/../../../../../config.php');

function tiny_cursive_extend_navigation_course(\navigation_node $navigation, \stdClass $course, \context $context) {
    global $CFG, $PAGE, $SESSION;
    
$url = new moodle_url($CFG->wwwroot .'/lib/editor/tiny/plugins/cursive/tiny_cursive_report.php/', ['courseid' => $course->id]);
    $navigation->add(
        "Writing Activity Report",
        $url,
        navigation_node::TYPE_SETTING,
        null,
        null,
        new pix_icon('i/report', '')
    );
}
function tiny_cursive_extend_navigation(global_navigation $navigation) {
    global $CFG, $PAGE;
    if ($home = $navigation->find('home', global_navigation::TYPE_SETTING)) {
        $home->remove();
    }
}

function tiny_cursive_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $USER;
    if (empty($course)) {
        $course = get_fast_modinfo(SITEID)->get_course();
    } 
        
    if (isguestuser() or !isloggedin()) {
        return;
    }
    if (\core\session\manager::is_loggedinas() or $USER->id != $user->id) {
       return;
    }  
        $url = new moodle_url('/lib/editor/tiny/plugins/cursive/my_writing_report.php',
                array('id' => $user->id, 'course' => $course->id, 'mode' => 'cursive'));
        $node = new core_user\output\myprofile\node('reports', 'cursive', get_string('writing', 'tiny_cursive'), null, $url);
        $tree->add_node($node);          
}
function upload_multipart_record($file_record,$file_name_with_full_path,$remote_url){ 
    global $CFG;
    // if (function_exists('curl_file_create')) { 
    //     $cFile = curl_file_create($file_name_with_full_path);
    //   } else { // 
    //     $cFile = '@' . realpath($file_name_with_full_path);
    //   }
    $moodle_Url=get_config('tiny_cursive','host_url');//$CFG->wwwroot;
    $moodle_Url = preg_replace("(^https?://)", "", $moodle_Url );
    $moodle_Url='https://'.$moodle_Url;
      try {
      $token=get_config('tiny_cursive','secretkey');
     $remote_url=get_config('tiny_cursive','python_server');
     $remote_url=$remote_url."/upload_file";
     echo $remote_url;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$remote_url);
      curl_setopt($ch, CURLOPT_POST,true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'file' => new CURLFILE($file_name_with_full_path),
        'resource_id' => $file_record->id,
        'person_id' => $file_record->userid
    ]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'X-Moodle-Url:'.$moodle_Url,
        'Content-Type: multipart/form-data'
    ]);
       $result=curl_exec ($ch);
       $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close ($ch);
    } catch (Exception $e) {
        print_r($e->getMessage());
      }
      $uploaded=false;
      if($httpcode==200){
        $uploaded=true;
      }
      return $result;
    }
    
function tiny_cursive_before_footer() {
    
    global $PAGE,$COURSE, $USER;
    $confidence_threshold= get_config('tiny_cursive','confidence_threshold');
    $showcomments= get_config('tiny_cursive','showcomments');
    $context = get_context_instance(CONTEXT_COURSE,$COURSE->id);
    $user_role='';
    if (has_capability('report/courseoverview:view', $context, $USER->id, false) || is_siteadmin()) {
        $user_role='teacher_admin'; 
     }
    $PAGE->requires->js_call_amd('tiny_cursive/settings', 'init', array($showcomments,$user_role));
   //&&  !is_siteadmin()
    if(get_config('tiny_cursive','showcomments') ){
    if($PAGE->bodyid=='page-mod-forum-discuss'||$PAGE->bodyid=='page-mod-forum-view'){
        $PAGE->requires->js_call_amd('tiny_cursive/append_fourm_post', 'init', array($confidence_threshold,$showcomments));
    }
    if($PAGE->bodyid=='page-mod-assign-grader'){
        $PAGE->requires->js_call_amd('tiny_cursive/show_url_in_submission_grade', 'init', array($confidence_threshold,$showcomments));
    }
    if($PAGE->bodyid=='page-mod-assign-viewpluginassignsubmission'){  
        $PAGE->requires->js_call_amd('tiny_cursive/show_url_in_submission_detail', 'init', array($confidence_threshold,$showcomments));
    }
    }
    if($PAGE->bodyid=='page-mod-assign-grading'){
        $PAGE->requires->js_call_amd('tiny_cursive/append_submissions_table', 'init', array($confidence_threshold,$showcomments));
    }
    if($PAGE->bodyid=='page-mod-quiz-review'){
        $PAGE->requires->js_call_amd('tiny_cursive/show_url_in_quiz_detail', 'init', array($confidence_threshold,$showcomments));
    }
    if($PAGE->bodyid=='page-course-view-participants'){
        $PAGE->requires->js_call_amd('tiny_cursive/append_participants_table', 'init', array($confidence_threshold,$showcomments));
    }   
}