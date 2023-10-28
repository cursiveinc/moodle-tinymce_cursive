<?php

/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

namespace tiny_cursive;

require_once ($CFG->dirroot . '/mod/quiz/lib.php');
require_once ($CFG->dirroot . '/config.php');
require_once ($CFG->dirroot . '/mod/quiz/locallib.php');
require_once ($CFG->dirroot . '/mod/quiz/attemptlib.php');


use quiz;
use stdClass;

class tiny_cursive_data
{

    public static function get_courses_users($params)
    { 
        global $DB;
        $allusers = new stdClass();
        $allusers->userlist = array();
        $udetail = array();
        $udetail2 = array();
        $courseid = (int)$params['courseid'];
        $quizid = (int)$params['quizid'];
        $users = $DB->get_records_sql("SELECT ue.id as enrolid,u.id as id,u.firstname,u.lastname FROM {enrol} e 
        INNER JOIN {user_enrolments} ue ON e.id = ue.enrolid 
        INNER JOIN {user} u ON u.id = ue.userid WHERE e.courseid = $courseid AND u.id != 1");
        $udetail2['id'] = 0;
        $udetail2['name'] = 'All Users';
        $allusers->userlist[] = $udetail2;
        foreach ($users as $user)
        {
            $udetail['id'] = $user->id;

            $udetail['name'] = $user->firstname . ' ' . $user->lastname;

            $allusers->userlist[] = $udetail;

        }
        return $allusers;
    }

    public static function get_courses_modules($params)
    { 

        global $DB;
        $allusers = new stdClass();
        $allusers->userlist = array();

        $udetail = array();
        $udetail2 = array();

        $courseid = (int)$params['courseid'];     
    
        $udetail2['id'] = 0;
        $udetail2['name'] = 'All Modules';
        $allusers->userlist[] = $udetail2;
         $modules = $DB->get_records_sql("SELECT id, instance  FROM {course_modules} WHERE course = $courseid ");
            foreach ($modules as $cm) {
                $modinfo = get_fast_modinfo($courseid);
                 $cm = $modinfo->get_cm($cm->id);
                 $get_module_name = get_coursemodule_from_id($cm->modname, $cm->id, 0, false, MUST_EXIST);
                 $udetail['id'] = $cm->id;
                 $udetail['name'] = $get_module_name->name;
                 $allusers->userlist[] = $udetail;
            }      
       

        return $allusers;

    }

}