<?php

/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

namespace tiny_cursive\task;

class upload_student_json_cron extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'tiny_cursive');
    }

    public function execute() { 
        global $CFG, $DB;
        $table='tiny_cursive_files';
        $sql= "select * from mdl_tiny_cursive_files where timemodified>uploaded";
        $file_records = $DB->get_records_sql($sql);
        $dirname = $CFG->dirroot.'/lib/editor/tiny/plugins/cursive/userdata/';       
        require_once($CFG->dirroot.'/lib/editor/tiny/plugins/cursive/lib.php');
        foreach($file_records as $file_record){
                $file_path=$dirname.$file_record->filename;
                 $uploaded=upload_multipart_record($file_record,$file_path);
               // echo '$uploaded'.$uploaded;
                if($uploaded){
                    $file_record->uploaded= strtotime(date('Y-m-d H:i:s'));
                    $DB->update_record($table, $file_record);
                    $uploaded=false;
                }
 
        } 
    }

}