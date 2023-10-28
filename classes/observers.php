<?php 

/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

namespace tiny_cursive;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/config.php');

class observers{
  public static function update_comment($event){
    global $DB;  
    $eventdata = $event->get_data(); 
    $table = 'tiny_cursive_comments';
    $conditions=array("userid"=>$eventdata['userid'],"modulename"=>'forum','resourceid'=>0);
    $recs=	$DB->get_records($table,$conditions);
    if($recs){
      foreach ($recs as $rec) {
          $dataObj = new \stdClass();
          $dataObj->userid = $eventdata['userid'];
          $dataObj->id = $rec->id;
          $dataObj->cmid = $eventdata['contextinstanceid'];
          $dataObj->courseid = $eventdata['courseid'];
          $dataObj->resourceid = $eventdata['objectid'];
          $DB->update_record($table, $dataObj,true);
      }
    }
  }
  public static function update_cursive_files($event){
    global $DB,$CFG;  
    $eventdata = $event->get_data();
    $table = 'tiny_cursive_files';
    $conditions=array("userid"=>$eventdata['userid'],"modulename"=>'forum','resourceid'=>0);
    $recs=	$DB->get_records($table,$conditions);
    if($recs){
      foreach ($recs as $rec) {
        $userid = $eventdata['userid'];
        $cmid = $eventdata['contextinstanceid'];
        $resourceid = $eventdata['objectid'];
        $dirname = $CFG->dirroot .'/lib/editor/tiny/plugins/cursive/userdata/';
        $fname=$userid.'_'.$resourceid.'_'.$cmid.'_attempt'.'.json';
        $source_file=$dirname.$rec->filename;
        $des_filename = $dirname.$fname;        
        $inp = file_get_contents($des_filename);
        $tempArray = null;
        if($inp){
        $tempArray = json_decode($inp, true);
        $merged=json_encode(
          array_merge($tempArray, json_decode(file_get_contents($source_file))));
            file_put_contents($des_filename, $merged);
            unlink($source_file);
        $DB->delete_records($table, array('id'=>$rec->id));
        }
        else{   
        rename($source_file,$des_filename);
          $dataObj = new \stdClass();
          $dataObj->userid = $userid;
          $dataObj->id = $rec->id;
          $dataObj->cmid = $cmid;
          $dataObj->courseid = $eventdata['courseid'];
          $dataObj->resourceid = $resourceid;
          $dataObj->filename = $fname;
          $DB->update_record($table, $dataObj,true);
      }
    }
    }
  }
  public static function observer_login(\mod_forum\event\post_created  $event) {
    self::update_comment($event);
    self::update_cursive_files($event);
  }
     
  public static function post_updated(\mod_forum\event\post_updated  $event) {
    self::update_comment($event);
    self::update_cursive_files($event);
  }
  public static function discussion_created(\mod_forum\event\discussion_created  $event) {

        global $DB;
        $eventdata = $event->get_data(); 
        $objectid=$eventdata['objectid'];
        $discussions_table='forum_discussions';
        $discussions_rec=	$DB->get_record($discussions_table,array('id'=>$objectid));
        $table = 'tiny_cursive_comments';
        $conditions=array("userid"=>$eventdata['userid'],"modulename"=>'forum','resourceid'=>0);
        $recs=	$DB->get_records($table,$conditions);
      if($recs){
        foreach ($recs as $rec) {
            $dataObj = new \stdClass();
            $dataObj->userid = $eventdata['userid'];
            $dataObj->id = $rec->id;
            $dataObj->cmid = $eventdata['contextinstanceid'];
            $dataObj->courseid = $eventdata['courseid'];
            $dataObj->resourceid = $discussions_rec->firstpost;
            $DB->update_record($table, $dataObj,true);
        }
      }
    }

    public static function submission_created(\mod_assign\event\submission_created $event) {
      global $DB,$CFG,$PAGE,$USER;
      $eventdata = $event->get_data(); 
  
    }
    public static function assessable_submitted(\mod_assign\event\assessable_submitted $event) {
      global $DB,$CFG,$PAGE,$USER;
      $eventdata = $event->get_data(); 
  }
}