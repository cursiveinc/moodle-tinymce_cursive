<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

require_once("$CFG->libdir/externallib.php");
require_once('locallib.php');
use tiny_cursive\tiny_cursive_data;

class cursive_json_func_data extends external_api {
	 public static function cursive_json_func_is_allowed_from_ajax() {
        return true;
    }
    public static function cursive_reports_func_is_allowed_from_ajax() {
        return true;
    }
        // Service for quizzes list.
    public static function get_user_list_is_allowed_from_ajax() {
            return true;
    }
    
    public static function get_user_list_returns() {
            return new external_value(PARAM_RAW, 'All quizzes');
    }
    
    public static function get_user_list_parameters() {
            return new external_function_parameters(
                array(
                    'page' => new external_value(PARAM_INT, '', false),
                    'courseid' => new external_value(PARAM_INT, 'Course id', false, 'course_detail')
                )
            );
    }
    
    public static function get_user_list($page, $courseid) {
            require_login();
            $params = self::validate_parameters(
                self::get_user_list_parameters(),
                array(
                    'page' => $page,
                    'courseid' => $courseid
                )
            );
            return json_encode(tiny_cursive_data::get_courses_users($params));
    }

         // Service for quizzes list.
    public static function get_module_list_is_allowed_from_ajax() {
            return true;
    }
    
    public static function get_module_list_returns() {
            return new external_value(PARAM_RAW, 'All quizzes');
    }
    
    public static function get_module_list_parameters() {
            return new external_function_parameters(
                array(
                    'page' => new external_value(PARAM_INT, '', false),
                    'courseid' => new external_value(PARAM_INT, 'Course id', false, 'course_detail')
                )
            );
    }
    
    public static function get_module_list($page, $courseid) {
            require_login();
            $params = self::validate_parameters(
                self::get_user_list_parameters(),
                array(
                    'page' => $page,
                    'courseid' => $courseid
                )
            );
            return json_encode(tiny_cursive_data::get_courses_modules($params));
    }


    public static function cursive_json_func_returns() {
        return new external_value(PARAM_RAW, 'result');
    }
    public static function cursive_reports_func_returns() {
        return new external_value(PARAM_RAW, 'result');
    }

    public static function cursive_json_func_parameters() {
        return new external_function_parameters(
            array(       
                'resourceId' => new external_value(PARAM_INT, 0,  'resourceId'),
                'key' => new external_value(PARAM_RAW, 'key detail', false, 'key'),
                'keyCode' => new external_value(PARAM_RAW, 'key code ', false, 'keycode'),
                'event' => new external_value(PARAM_RAW, 'event', false, 'event'),
                'cmid'=>new external_value(PARAM_INT, 0,  'cmid'),
                'modulename'=>new external_value(PARAM_RAW, 'quiz',  'modulename'),
            )
        );
    }
    public static function cursive_reports_func_parameters() {
        return new external_function_parameters(
            array(       
                'coursename' => new external_value(PARAM_INT, 0,  'coursename'),
                'quizname' => new external_value(PARAM_RAW, 'quizname detail', false, 'quizname'),
                'username' => new external_value(PARAM_RAW, 'username detail ', false, 'username')
            )
        );
    }

    public static function cursive_json_func($resourceId = 0, $key = null, $keyCode = null, $event='keyUp',$cmid=0, $modulename='quiz') {
        require_login();
        global $USER, $SESSION, $DB;
        $params = self::validate_parameters(
            self::cursive_json_func_parameters(),
            array(               
                'resourceId' => $resourceId,
                'key' => $key,
                'keyCode' => $keyCode,
                'event' => $event,
                'cmid'=>$cmid, 
                'modulename'=>$modulename,  
            )
        );
        $courseId=0;
        if ($resourceId==0) {
           $resourceId = $cmid;
        }
        $user_data=array('resourceId'=>$resourceId,'key'=>$key,'keyCode'=>$keyCode,'event'=>$event);
        if($cmid){
            $cm = $DB->get_record('course_modules', array('id'=> $cmid));
            $user_data["courseId"]=  $cm->course;
            $courseId=$cm->course;
        }
        else{
            $user_data["courseId"]=  0; 
        }

        //$user_data['unixTimestamp']=time(); //13 digit time 13 digits ("the number of milliseconds since the epoch").
        // list($microseconds, $seconds) = explode(' ', microtime());
        // $milliseconds = sprintf('%03d', round($microseconds * 1000));
        // $timestamp_in_milliseconds = $seconds . $milliseconds;
        //echo $timestamp_in_milliseconds; //int value
        $time_arr= explode('.', microtime("now")*1000);
        $timestamp_in_milliseconds =  $time_arr[0];
        $user_data['unixTimestamp']= $timestamp_in_milliseconds; //13 digit time 13 digits ("the number of milliseconds since the epoch").

        $user_data["clientId"]= "2df2e6fc-dac2-4706-ac1b-992fb3019343";
        $user_data["personId"]=  $USER->id;

        //$cmid
        $dirname = __DIR__ .'/userdata/';
        $fname=$USER->id.'_'.$resourceId.'_'.$cmid.'_attempt'.'.json';
        $filename = __DIR__ .'/userdata/'.$fname ;
        //insert in database

        $table = 'tiny_cursive_files';

        if (file_exists($dirname)) {
            //echo "The directory $filename exists.";
        } else {
            mkdir( $dirname, 0755);
        }
        $inp = file_get_contents($filename);
        $tempArray = null;
        if($inp){
        $tempArray = json_decode($inp, true);
        array_push($tempArray, $user_data);
        $file_rec = $DB->get_record($table, array('cmid'=> $cmid,'modulename'=>$modulename,'userid'=>$USER->id)); 
        $file_rec->uploaded =0;
       // print_r($file_rec);
        $DB->update_record($table, $file_rec);
    }else{
            $tempArray[] = $user_data;
            $dataObj = new stdClass();
            $dataObj->userid = $USER->id;
            $dataObj->resourceid = $resourceId;
            $dataObj->cmid = $cmid;
            $dataObj->modulename = $modulename;
            $dataObj->courseid = $courseId;
            $dataObj->timemodified = time();
            $dataObj->filename = $fname;
            $dataObj->uploaded = 0;
           
            $DB->insert_record($table, $dataObj);
        }


        $jsonData = json_encode($tempArray);

        if(is_array($tempArray)){
            file_put_contents($filename, $jsonData);
        }else{
        // echo "not an array".$jsonData;
        }
        return $filename;
    }
    public static function cursive_reports_func($coursename = 0,$quizname = null, $username='keyUp') {
        require_login();
        global $USER, $SESSION, $DB;
        $params = self::validate_parameters(
            self::cursive_reports_func_parameters(),
            array(               
                'resourceId' => $coursename,
                'key' => $quizname,
                'keyCode' => $username                           
            )
        );      
        return "cursive reports";
    }
    //user comments store 
public static function cursive_user_comments_func_is_allowed_from_ajax() {
        return true;
}
     

public static function cursive_user_comments_func_returns() {
        return new external_value(PARAM_RAW, 'All User Comments');
}

public static function cursive_user_comments_func_parameters() {
        return new external_function_parameters(
            array(
                'modulename' => new external_value(PARAM_TEXT, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid'),
                'resourceid' => new external_value(PARAM_INT, 'resourceid'),
                'courseid' => new external_value(PARAM_INT, 'courseid'),
                'usercomment' => new external_value(PARAM_TEXT, 'usercomment'),
                'timemodified' => new external_value(PARAM_INT, 'timemodified')
            )
        );
}

public static function cursive_user_comments_func($modulename, $cmid,$resourceid,$courseid,$usercomment,$timemodified) {
    global  $DB,$USER;
        require_login();
        $userid=$USER->id;
        $dataobject = new stdClass();
        $dataobject->userid = $userid ;
        $dataobject->cmid = $cmid ;
        $dataobject->modulename= $modulename;
        $dataobject->resourceid = $resourceid ;
        $dataobject->courseid = $courseid ;
        $dataobject->usercomment = $usercomment ;
        $dataobject->timemodified = $timemodified ;
        try{
             $DB->insert_record('tiny_cursive_comments',$dataobject);
        }catch(Exception $e){
            Print_r($e);
            die("error occored");
        }      
}
// Approve token
    public static function cursive_approve_token_func_is_allowed_from_ajax() {
        return true;
}   
public static function cursive_approve_token_func_returns() {
        return new external_value(PARAM_RAW, 'Token Approved');
}
public static function cursive_approve_token_func_parameters() {
        return new external_function_parameters(
            array(
                'token' => new external_value(PARAM_TEXT, 'userid')
            )
        );
}
public static function cursive_approve_token_func($token) {
    global  $DB,$CFG;
        require_login();
        // $remote_url="http://52.205.247.22/verify-token";
         $remote_url=get_config('tiny_cursive','python_server');
         $remote_url=$remote_url.'/verify-token';
         $moodle_url=$CFG->wwwroot;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$remote_url);
            curl_setopt($ch, CURLOPT_POST,true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array("token"=>$token,"moodle_url"=>$moodle_url));
            $result=curl_exec($ch);
            curl_close ($ch);
         } catch (Exception $e) {
              print_r($e->getMessage());
           }
        return $result;
}
   // Service for assignment comment list.
   public static function get_comment_link_is_allowed_from_ajax() {
    return true;
}

public static function get_comment_link_returns() {
    return new external_value(PARAM_RAW, 'Comment Link');
}

public static function get_comment_link_parameters() {
    return new external_function_parameters(
        array(
            'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
            'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename')
        )
    );
}

public static function get_comment_link($id,$modulename) {
    global  $DB;
    require_login();
    $params = self::validate_parameters(
        self::get_comment_link_parameters(),
        array(
            'id' => $id,
           'modulename'=>$modulename
        )
    );
    $conditions=array("resourceid"=>$id);
    $table = 'tiny_cursive_comments';
    $recs=	$DB->get_records($table,$conditions);
    $usercomment=[];
    if($recs){
        foreach ($recs as $key => $rec) {
            array_push($usercomment,$rec);
        }
        return json_encode($usercomment);

    }else{
        return json_encode(array(array('usercomment'=>'comments')));
    }
}
//Service for Quiz Attempt list
public static function get_assign_comment_link_is_allowed_from_ajax() {
    return true;
}

public static function get_assign_comment_link_returns() {
    return new external_value(PARAM_RAW, 'Comment Link');
}

public static function get_assign_comment_link_parameters() {
    return new external_function_parameters(
        array(
            'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
            'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
            'cmid' => new external_value(PARAM_INT, 'cmid', false, 'course_detail'),
        )
    );
}

public static function get_assign_comment_link($id,$modulename,$cmid) {
    global  $DB;
    require_login();
    $params = self::validate_parameters(
        self::get_assign_comment_link_parameters(),
        array(
            'id'=>$id,
           'modulename'=>$modulename,
           'cmid'=>$cmid
        )
    );
    $rec_assign_submission=$DB->get_record('assign_submission',array('id'=>$id),'*',false);
    $userid=$rec_assign_submission->userid;
    //echo $userid;
    $conditions=array("userid"=>$userid,'modulename'=>$modulename,'cmid'=>$cmid);
    $table = 'tiny_cursive_comments';
    $recs=	$DB->get_records($table,$conditions);
    $usercomment=[];
    if($recs){
        foreach ($recs as $rec) {
            array_push($usercomment,$rec);
        }
        return json_encode($usercomment);

    }else{
        return json_encode(array(array('usercomment'=>'comments')));
    }
}
// submissions stats modal get_user_submissions_data

public static function get_assign_grade_comment_is_allowed_from_ajax() {
    return true;
}

public static function get_assign_grade_comment_returns() {
    return new external_value(PARAM_RAW, 'Comment Link');
}

public static function get_assign_grade_comment_parameters() {
    return new external_function_parameters(
        array(
            'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
            'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
            'cmid' => new external_value(PARAM_INT, 'cmid', false, 'course_detail'),
        )
    );
}

public static function get_assign_grade_comment($id,$modulename,$cmid) {
    global  $DB;
    require_login();
    $params = self::validate_parameters(
        self::get_assign_comment_link_parameters(),
        array(
            'id'=>$id,
           'modulename'=>$modulename,
           'cmid'=>$cmid
        )
    );
    // $rec_assign_submission=$DB->get_record('assign_submission',array('id'=>$id),'*',false);
    // $userid=$rec_assign_submission->userid;
    //echo $userid;
    $conditions=array("userid"=>$id,'modulename'=>$modulename,'cmid'=>$cmid);
    $table = 'tiny_cursive_comments';
    $recs=	$DB->get_records($table,$conditions);
    $usercomment=[];
    if($recs){
        foreach ($recs as $rec) {
            array_push($usercomment,$rec);
        }
        return json_encode($usercomment);

    }else{
        return json_encode(array(array('usercomment'=>'comments')));
    }
}

// submition grade
public static function get_user_list_submission_stats_is_allowed_from_ajax() {
    return true;
}

public static function get_user_list_submission_stats_returns() {
    return new external_value(PARAM_RAW, 'Comment Link');
}

public static function get_user_list_submission_stats_parameters() {
    return new external_function_parameters(
        array(
            'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
            'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
            'cmid' => new external_value(PARAM_INT, 'cmid', false, 'course_detail'),
        )
    );
}

public static function get_user_list_submission_stats($id,$modulename,$cmid) {
    global  $DB;
    require_login();
    $params = self::validate_parameters(
        self::get_user_list_submission_stats_parameters(),
        array(
            'id' => $id,
           'modulename'=>$modulename,
           'cmid'=>$cmid
        )
    );
    //$conditions=array("resourceid"=>$id);
    //$table = 'tiny_cursive_comments';
    $rec=	get_user_submissions_data($id,$modulename,$cmid);
    return json_encode($rec);
}
///////////////////////////////////
public static function cursive_filtered_writing_func_is_allowed_from_ajax() {
    return true;
}

public static function cursive_filtered_writing_func_returns() {
    return new external_value(PARAM_RAW, 'Comment Link');
}

public static function cursive_filtered_writing_func_parameters() {
    return new external_function_parameters(
        array(
            'id' => new external_value(PARAM_TEXT, 'id', false, 'id'),
        )
    );
}

public static function cursive_filtered_writing_func($id) {
    global  $DB,$USER;
    require_login();
    $userid=$USER->id;
    $params = self::validate_parameters(
        self::cursive_filtered_writing_func_parameters(),
        array(
            'id'=>$id,
        )
    );
    $attempts = "SELECT qa.resourceid as attemptid,qa.timemodified,uw.score,uw.copy_behavior, u.id as userid, u.firstname, u.lastname, u.email,  qa.cmid as cmid ,qa.courseid,qa.filename,uw.word_count
    , uw.words_per_minute , uw.total_time_seconds ,uw.backspace_percent FROM {user} u 
        INNER JOIN {tiny_cursive_files} qa ON u.id = qa.userid 
        LEFT JOIN {tiny_cursive_user_writing} uw ON qa.id = uw.file_id  
        WHERE qa.userid!=1";
        
         if($userid != 0){
            $attempts .= " AND  qa.userid = $userid";
         }
         if ($id != 0) {
            $attempts .= "  AND qa.courseid=$id";
        }
    $res = $DB->get_records_sql($attempts);
    $recs=array();
    foreach ($res as $key => $value) {
        $value->timemodified=  date("l jS \of F Y h:i:s A",$value->timemodified);
       // $value->attemptid= '';
       $value->icon='fa fa-circle-o';
       $value->color='grey';
        array_push($recs,$value);
    }
   // print_r( $recs);
    $res_n_count=array('count'=>$totalcount,'data'=>$recs);
    return json_encode($res_n_count);   
}
}
?>
