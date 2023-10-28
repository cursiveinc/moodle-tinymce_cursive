<?php 
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

function get_user_attempts_data($userid, $courseid, $moduleid, $orderby='id', $order='ASC',$perpage = '',$limit = '') { 
    $attempts=[];   
    global $DB;
    if($orderby == 'id'){
        $odby = 'u.id';
    }
    if($orderby == 'name'){
        $odby = 'u.firstname'; 
    }
    if($orderby == 'email'){
        $odby = 'u.email';
    }
    if($orderby == 'date'){
        $odby = 'qa.timemodified';
    }
    $attempts = "SELECT qa.resourceid as attemptid,qa.timemodified,uw.score, u.id as userid, u.firstname, u.lastname, u.email,  qa.cmid as cmid ,qa.courseid,qa.filename,uw.word_count
    , uw.words_per_minute , uw.total_time_seconds ,uw.backspace_percent FROM {user} u 
        INNER JOIN {tiny_cursive_files} qa ON u.id = qa.userid 
        LEFT JOIN {tiny_cursive_user_writing} uw ON qa.id = uw.file_id  
        WHERE qa.userid!=1";
    if($userid != 0){
       $attempts .= " AND  qa.userid = $userid";
    }
    if ($courseid != 0) {
        $attempts .= "  AND qa.courseid=$courseid";
    }

    if ($moduleid != 0) {
        $attempts .= "  AND qa.cmid=$moduleid";
    }
   
    $attempts .= " ORDER BY $odby $order";
    $totalcount = 0;
    // if ($limit) {
    //     $get_detail_count=$DB->get_records_sql($attempts);     
    //     $totalcount = count($get_detail_count);
    //     $attempts .= " LIMIT $perpage ,$limit ";
    // }
    $res = $DB->get_records_sql($attempts);
    $res_n_count=array('count'=>$totalcount,'data'=>$res);
    return $res_n_count;
        
}
function get_user_writing_data($userid, $courseid, $moduleid, $orderby='id', $order='ASC',$perpage = '',$limit = '') { 
    $attempts=[];   
    global $DB;
    if($orderby == 'id'){
        $odby = 'u.id';
    }
    if($orderby == 'name'){
        $odby = 'u.firstname'; 
    }
    if($orderby == 'email'){
        $odby = 'u.email';
    }
    if($orderby == 'date'){
        $odby = 'qa.timemodified';
    }
    $attempts = "SELECT qa.resourceid as attemptid,qa.timemodified,uw.score, u.id as userid, u.firstname, u.lastname, u.email,  qa.cmid as cmid ,qa.courseid,qa.filename,uw.word_count
    , uw.words_per_minute , uw.total_time_seconds ,uw.backspace_percent FROM {user} u 
        INNER JOIN {tiny_cursive_files} qa ON u.id = qa.userid 
        LEFT JOIN {tiny_cursive_user_writing} uw ON qa.id = uw.file_id  
        WHERE qa.userid!=1";
    if($userid != 0){
       $attempts .= " AND  qa.userid = $userid";
    }
    if ($courseid != 0) {
        $attempts .= "  AND qa.courseid=$courseid";
    }

    if ($moduleid != 0) {
        $attempts .= "  AND qa.cmid=$moduleid";
    }
   
    $attempts .= " ORDER BY $odby $order";
    $totalcount = 0;
    if ($limit) {
        $get_detail_count=$DB->get_records_sql($attempts);     
        $totalcount = count($get_detail_count);
        $attempts .= " LIMIT $perpage ,$limit ";
    }
    $res = $DB->get_records_sql($attempts);
    $res_n_count=array('count'=>$totalcount,'data'=>$res);
    return $res_n_count;
        
}
function get_user_profile_data($userid) { 
    $attempts=[];   
    global $DB;
    
    $attempts = "SELECT  sum(uw.total_time_seconds) as total_time,sum(uw.word_count) as word_count FROM {tiny_cursive_user_writing} uw
        INNER JOIN {tiny_cursive_files} uf ON uw.file_id =uf.id  where uf.userid =$userid";
    $res = $DB->get_record_sql($attempts);
  
    return $res;
        
}


function get_user_submissions_data($resourceid,$modulename,$cmid) { 
    
    $attempts=[];   
    global $DB;
    
    $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,uw.backspace_percent,uw.score,uf.resourceid , uf.modulename,uf.userid FROM {tiny_cursive_user_writing} uw
        INNER JOIN {tiny_cursive_files} uf ON uw.file_id =uf.id where uf.userid =$resourceid AND uf.cmid =$cmid AND uf.modulename='".$modulename."'";
    $res = $DB->get_record_sql($attempts);
    return $res;      
}
?>