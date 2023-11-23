<?php

/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

require(__DIR__ . '/../../../../../config.php');

require_once($CFG->libdir . "/csvlib.class.php");

require_once('lib.php');

use csv_export_writer;

$courseid = optional_param('courseid', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
global $CFG, $DB, $OUTPUT;
$report = array();
$headers = array(
    'FullName',
    'Email',
    'CourseID',
    'total_time_seconds',
    'key_count',
    'keys_per_minute',
    'character_count',
    'characters_per_minute	',
    'word_count',
    'words_per_minute',
    'backspace_percent',
    'score',
    'copy_behavior',
  //  'Course'

);
$exportcsv = new csv_export_writer('comma');
$exportcsv->set_filename("ExportUsersData");
$exportcsv->add_data($headers); //Add Header Row
// SELECT 
//  u.firstname,u.email,uf.courseid,
//   sum(uw.total_time_seconds) as total_time, 
//   sum(uw.key_count) as key_count,
//   sum(uw.keys_per_minute) as keys_per_minute,
//   sum(uw.character_count)as character_count,
//   sum(uw.characters_per_minute) as characters_per_minute,
//   sum(uw.word_count) as word_count,
//   sum(uw.words_per_minute) as words_per_minute,
//   sum(uw.backspace_percent) as backspace_percent,
//   sum(uw.score) as score, 
//   sum(uw.copy_behavior) as copy_behavior 
//    FROM mdl_tiny_cursive_user_writing uw 
//    INNER JOIN mdl_tiny_cursive_files uf ON uw.file_id =uf.id 
// INNER JOIN mdl_user u ON uf.userid =u.id 
//    where uf.courseid=10
//     GROUP BY uf.userid;
    if ($courseid != 0) {
        $attempts = "SELECT 
        uf.id as fileid, u.id as usrid,
 u.firstname,u.email,uf.courseid,
  sum(uw.total_time_seconds) as total_time, 
  sum(uw.key_count) as key_count,
  avg(uw.keys_per_minute) as keys_per_minute,
  sum(uw.character_count)as character_count,
  avg(uw.characters_per_minute) as characters_per_minute,
  sum(uw.word_count) as word_count,
  avg(uw.words_per_minute) as words_per_minute,
  avg(uw.backspace_percent) as backspace_percent,
  avg(uw.score) as score, 
  sum(uw.copy_behavior) as copy_behavior 
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
    $attempts .= " GROUP BY uf.userid;";
 //where uf.userid =$user_course->userid 
        $ress = $DB->get_records_sql($attempts);
        foreach ($ress as $key => $res) {     
        if ($res != null) {
            $userrow = array(
                $res->firstname . ' ' . $res->lastname,
                $res->email,
                $res->courseid,
                $res->total_time,
                $res->key_count,
                $res->keys_per_minute,
                $res->character_count,
                $res->characters_per_minute,
                $res->word_count,
                $res->words_per_minute,
                $res->backspace_percent,
                $res->score,
                $res->copy_behavior,
                $user_course->fullname,
            );
            $exportcsv->add_data($userrow);
        }
    }
    }
//}
$exportcsv->download_file();
