<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

defined('MOODLE_INTERNAL') || die;
//require_once('classes/forms/userreportform.php');
//require_once('lib.php');
$page = optional_param('page', 0, PARAM_INT);
require_login();
global $PAGE, $DB;

class tiny_cursive_renderer extends plugin_renderer_base
{
 
    public function get_link_icon($score=0){
      $score_setting=get_config('tiny_cursive','confidence_threshold');
      $score_setting=$score_setting?$score_setting:0.65;

        $icon='fa fa-circle-o';
        $color='font-size:24px;color:black';
        if($score>=$score_setting){
            $icon='fa fa-check-circle';
            $color='font-size:24px;color:green';
            
        }
        // else if($score>=0.35){
        //     $icon='fa fa-question';
        //     $color='font-size:36px;color:grey';
        // }
        else if($score>=-1){
          $icon='fa fa-question-circle';
          $color='font-size:24px;color:#A9A9A9';
      }
        else{
            $icon='fa fa-circle-o'; 
            $color='font-size:24px;color:black';
        }
        return '<i  class="'.$icon.'"'.' style="'.$color.'";></i>';
    }
    public function get_html_modal($user,$module_name="title"){
        $content='
        <div  class="modal" id="'.$user->attemptid.'" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">'.$module_name.'</h4>
            </div>
            <div class="modal-body">
            <div class="position"><p>Time Writing: '.sprintf('%02d:%02d', ($user->total_time_seconds/ 60 % 60), $user->total_time_seconds% 60).' </p><span></span></div>
            <div class="position"><p>Words per minute: '.$user->words_per_minute.' </p><span></span></div>
              <div class="username"><p>Total Words: '.$user->word_count.'</p><span></span></div>
              <div class="position"><p>Backspace %: '.$user->backspace_percent.' </p><span></span></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="modal-close btn btn-primary" data-dismiss="modal">Close</button>
            </div>
          </div>
          
        </div>
        </div>';
        return $content;
    }
    public function get_html_score_modal($user,$module_name="title"){
      
      $content='
      <div  class="modal" id="score'.$user->attemptid.'" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">'.'Your Random Reflection Prompt'.'</h4>
          </div>
          <div class="modal-body">
          <div class="position"><p>Authorship Confidence: '.$user->score.' </p><span></span></div>
          <div class="position"><p>Copy Behavior: '.$user->copy_behavior.' </p><span></span></div>
     
          </div>
          <div class="modal-footer">
            <button type="button" class="modal-close btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>
        
      </div>
      </div>';
      return $content;
  }
    public function timer_report($users, $courseid, $page=0, $limit=10, $baseurl='')
    {
        global $CFG, $DB, $OUTPUT;
       $totalcount = $users['count']; 
        $data = $users['data']; 
        $table = new html_table();
        $table->head = array('Attemptid', 'Full Name', 'Email', 'Module Name','Last modified', 'Analytics','TypeID', '');
        $sr=1;
        foreach ($data as $user) {
          $link_icon=$this->get_link_icon($user->score);
            $modinfo = get_fast_modinfo($courseid);
            $cm = $modinfo->get_cm($user->cmid);
            $get_module_name = get_coursemodule_from_id($cm->modname, $user->cmid, 0, false, MUST_EXIST);
            $content=$this->get_html_modal($user,$get_module_name->name);
            $score_content=$this->get_html_score_modal($user,$courseid>0?$get_module_name->name:'Score');//,

            $row = [];
            $row[] = $user->fileid;
            $row[] = $user->firstname . ' ' . $user->lastname;
            $row[] = $user->email;
            $row[] = $get_module_name->name;
            $row[] = date("l jS \of F Y h:i:s A",$user->timemodified);
            $row[] =  '<a data-id='.$user->attemptid.' href = "#" class = "popup_item"><i class="fa fa-area-chart" style="font-size:24px;color:black" aria-hidden="true" style = "padding-left:25px; font-size:x-large;"></i></a>'.$content;
            //$row[] = "<a href ='#'>$link_icon</a>";
            $row[] = "<a data-id=score".$user->attemptid."  href ='#' class = 'link_icon'>".$link_icon."</a>".$score_content;

//$row[] = "<a href=" .$CFG->wwwroot.'/lib/editor/tiny/plugins/cursive/download_json.php?user_id=' . $user->userid . '&resourceId=' . $user->attemptid . '&cmid=' . $user->cmid . '&quizid=2&id="export" role="button" class="btn btn-primary" style="margin-right:50px;" >' . "Download" . "</a>";
$row[] = "<a href=" .$CFG->wwwroot. '/lib/editor/tiny/plugins/cursive/download_json.php?fname=' . $user->filename .  '&quizid=2id="export" role="button" class="btn btn-primary" style="margin-right:50px;" >' . "Download" . "</a>";
         
            $table->data[] = $row;
        };
        echo html_writer::table($table);
        echo $OUTPUT->paging_bar($totalcount, $page, $limit, $baseurl);
    }
    public function user_writing_report($users, $user_profile,$page=0, $limit=5, $baseurl='',$username)
    {
        global $CFG,$OUTPUT,$DB;
       echo "Total Words: $user_profile->word_count</br>";
       
       echo "Total Time Writing:". sprintf('%02dh %02dm %02ds', ($user_profile->total_time/ 60*60 % 60),($user_profile->total_time/ 60 % 60), $user->total_time% 60)."</br>";
       $avg_words=0;
       if($user_profile->total_time>0){

       $avg_words=round($user_profile->word_count/($user_profile->total_time/60));
       }

       echo "Average Words Per minute:".$avg_words."</br></br>";
      //  $courses=  $DB->get_records_sql ("select c.fullname,c.id from {course} c
      //  INNER JOIN {user_enrolments} ue ON ue.userid=c.id where ue.userid=$username");
      $courses=  $DB->get_records_sql ("select c.fullname,c.id from {course} c
      INNER JOIN {enrol} en ON en.courseid=c.id
      INNER JOIN {user_enrolments} ue ON ue.enrolid=en.id
      where ue.userid=$username");
      // die("DFgfdg");
       // get_records_sqlmdl_
       $options=array();

       echo"<div class='dropdown mb-4' >";
       echo'<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select Course</button>';
      // echo"<option value=''>Select Course</option>";
      $userid=0;
      echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
        //$base_url=$CFG->wwwroot.'/lib/editor/tiny/plugins/cursive/my_writing_report.php?userid='.$userid.'&courseid=';
        echo" <a class='dropdown-item' href=$baseurl >All Courses</a>";
        foreach($courses as $course){
            echo" <a class='dropdown-item' href=$baseurl&courseid=$course->id >$course->fullname</a>";
       }
       echo '</div>';
       echo"</div >";
       $table = new html_table();//

    $totalcount = $users['count']; 
    $data = $users['data']; 
        $table->head = array( 'Module Name','Last modified','Analytics','TypeID', '');
         foreach ($data as $user) {
          $score_content=$this->get_html_score_modal($user,$courseid>0?$get_module_name->name:'Score');//,
            $link_icon=$this->get_link_icon($user->score);
            $courseid=$user->courseid;
            $courseid=$courseid>0?$courseid:'';
             $modinfo = ($courseid!='')?get_fast_modinfo($courseid):null;
              $cm = $modinfo!=null?$modinfo->get_cm($user->cmid):null;
             $get_module_name = $courseid>0?get_coursemodule_from_id($cm->modname, $user->cmid, 0, false, MUST_EXIST):0;
             $row = [];
             $content=$this->get_html_modal($user,$courseid>0?$get_module_name->name:'Stats');//,

             $score_content=$this->get_html_score_modal($user,$courseid>0?$get_module_name->name:'Score');//,
             $row[] = $get_module_name?$get_module_name->name:'';
             $row[] = date("l jS \of F Y h:i:s A",$user->timemodified);
             $row[] =  '<a data-id='.$user->attemptid.' href = "#" class = "popup_item"><i class="fa fa-area-chart" style="font-size:24px;color:black" aria-hidden="true" style = "padding-left:25px; font-size:x-large;"></i></a>'.$content;
  
             $row[] = "<a data-id=score".$user->attemptid."  href ='#' class = 'link_icon'>".$link_icon."</a>".$score_content;
             $row[] = "<a href=" .$CFG->wwwroot. '/lib/editor/tiny/plugins/cursive/download_json.php?fname=' . $user->filename .  '&quizid=2id="export" role="button" class="btn btn-primary" style="margin-right:50px;" >' . "Download" . "</a>";
             $table->data[] = $row;
        }       
        echo html_writer::table($table);   
        echo $OUTPUT->paging_bar($totalcount, $page, $limit, $baseurl);
    }
   
}
?>
