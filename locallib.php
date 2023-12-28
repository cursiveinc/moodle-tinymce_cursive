<?php 
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

function get_user_attempts_data($userid, $courseid, $moduleid, $orderby='id', $order='ASC',$perpage = 1,$limit = 5) { 
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
        $odby = 'uf.timemodified';
    }
    $attempts = " SELECT uf.id as fileid, u.id as usrid,uw.id as uniqueid, u.firstname,u.email,uf.courseid,
    uf.id as attemptid,uf.timemodified, uf.cmid as cmid,
    uf.filename,uf.id as fileid, uw.total_time_seconds as total_time_seconds, 
    uw.key_count as key_count, uw.keys_per_minute as keys_per_minute, 
    uw.character_count as character_count, 
    uw.characters_per_minute as characters_per_minute, 
    uw.word_count as word_count, uw.words_per_minute as words_per_minute, 
    uw.backspace_percent as backspace_percent, uw.score as score, 
    uw.copy_behavior as copy_behavior 
      FROM  {tiny_cursive_files} uf
      INNER JOIN {user} u ON uf.userid =u.id 
LEFT JOIN {tiny_cursive_user_writing} uw ON uw.file_id =uf.id   
   WHERE uf.userid!=1 ";
    if($userid != 0){
       $attempts .= " AND  uf.userid = $userid";
    }
    if ($courseid != 0) {
        $attempts .= "  AND uf.courseid=$courseid";
    }

    if ($moduleid != 0) {
        $attempts .= "  AND uf.cmid=$moduleid";
    }
      
    $attempts .= " ORDER BY $odby ";
    $totalcount = 0;

    if ($limit) {
        $get_detail_count=$DB->get_records_sql($attempts);     
       $totalcount = count($get_detail_count);
        $attempts .= " LIMIT $perpage , $limit ";      
    } 
     $attempts;
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
        $odby = 'uf.timemodified';
    }
    $attempts = "  SELECT uf.id as fileid, u.id as usrid,uw.id as uniqueid,
  u.firstname,u.email,uf.courseid,uf.resourceid as attemptid,uf.timemodified,
  uf.cmid as cmid,uf.filename,
  uw.total_time_seconds as total_time_seconds, 
  uw.key_count as key_count,
  uw.keys_per_minute as keys_per_minute,
  uw.character_count as character_count,
  uw.characters_per_minute as characters_per_minute,
  uw.word_count as word_count,
  uw.words_per_minute as words_per_minute,
  uw.backspace_percent as backspace_percent,
  uw.score as score, 
  uw.copy_behavior as copy_behavior 
  FROM  {tiny_cursive_files} uf
  INNER JOIN {user} u ON uf.userid =u.id 
  LEFT JOIN {tiny_cursive_user_writing} uw ON uw.file_id =uf.id  
  WHERE uf.userid!=1";
    
    if($userid != 0){
       $attempts .= " AND  uf.userid = $userid";
    }
    if ($courseid != 0) {
        $attempts .= "  AND uf.courseid=$courseid";
    }

    if ($moduleid != 0) {
        $attempts .= "  AND uf.cmid=$moduleid";
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
function get_user_profile_data($userid,$courseid=0) { 
    $attempts=[];   
    global $DB;
    $attempts = "SELECT  sum(uw.total_time_seconds) as total_time,sum(uw.word_count) as word_count FROM {tiny_cursive_user_writing} uw
        INNER JOIN {tiny_cursive_files} uf ON uw.file_id =uf.id  where uf.userid =$userid";
   if ($courseid != 0) {
    $attempts .= "  AND uf.courseid=$courseid";
}
    $res = $DB->get_record_sql($attempts);
    return $res;
        
}

function get_user_submissions_data($resourceid,$modulename,$cmid,$courseid=0) { 
    
    $attempts=[];   
    global $DB;
    $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid FROM {tiny_cursive_user_writing} uw
        INNER JOIN {tiny_cursive_files} uf ON uw.file_id =uf.id where uf.userid =$resourceid AND uf.cmid =$cmid AND uf.modulename='".$modulename."'";
   if ($courseid != 0) {
    $attempts .= "  AND uf.courseid=$courseid";
}
   $res = $DB->get_record_sql($attempts);
    return $res;      
}
?>
