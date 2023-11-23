<?php

/**
 * @package tiny_cursive
 * @category tiny
 * @copyright  CTI <info@cursivetechnology.com>
 * @author eLearningstack
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');

class wrreportform extends moodleform
{
    public function definition()
    {
        global $DB, $USER;
        //start dropdowns of course, quiz and user email search field in mform

        $mform = &$this->_form;
        $attributes = '';
        $courseid = $this->_customdata['coursename'];
       // $username = $this->_customdata['username'];
        //$quizzes = self::get_quiz($courseid);
        //$users = self::get_user($courseid);
        $modules = self::get_modules($courseid);
        $options = array('multiple' => false, 'includefrontpage' => false);
        $mform->addElement('course', 'coursename', get_string('coursename', 'tiny_cursive'), $options);
        $mform->addRule('coursename', null, 'required', null, 'client');  
        $options = array(
           'id' => 'ID',
           'name' => 'Name',
           'email' => 'Email',
           'date' => 'Date',
        );
        $mform->addElement('select', 'orderby', get_string('orderby', 'tiny_cursive'), $options, $attributes);
        $mform->setType('orderby', PARAM_RAW);
        // $mform->addRule('username', null, 'required', null, 'client');
        $this->add_action_buttons(false, get_string('submit'));
    }

    function get_data()
    {
        $data = parent::get_data();
        if (!empty($data)) {
            $mform = &$this->_form;
            // Add the studentid properly to the $data object.
            if (!empty($mform->_submitValues['coursename'])) {
                $data->coursename = $mform->_submitValues['coursename'];
            }
            if (!empty($mform->_submitValues['username'])) {
                $data->username = $mform->_submitValues['username'];
            }
            if (!empty($mform->_submitValues['modulename'])) {
                $data->modulename = $mform->_submitValues['modulename'];
            }
        }
        return $data;
    }

    public function get_modules($courseid)
    {   // Get users dropdown.
        global $DB;
        $mdetail = array();
        $mdetail[0] = 'All Modules';
        if($courseid){
            $modules = $DB->get_records_sql("SELECT id, instance  FROM {course_modules} WHERE course = $courseid ");
            foreach ($modules as $cm) {
                $modinfo = get_fast_modinfo($courseid);
                 $cm = $modinfo->get_cm($cm->id);
                 $get_module_name = get_coursemodule_from_id($cm->modname, $cm->id, 0, false, MUST_EXIST);
                  $mdetail[$cm->id] = $get_module_name->name;               
            } 
        }
        /*if (!empty($courseid)) {
            $modules = $DB->get_records_sql("SELECT id, instance  FROM {course_modules} WHERE course = $courseid ");
            foreach ($modules as $module) {
                $mdetail[$module->id] = $module->id;
            }
        }*/
        return $mdetail;
    }

        public function get_user($courseid)
    {   // Get users dropdown.
        global $DB;
        $udetail = array();
        $udetail[0] = 'All Users';
        if (!empty($courseid)) {
            $users = $DB->get_records_sql("SELECT ue.id,u.id as userid,u.firstname,u.lastname FROM {enrol} e 
            INNER JOIN {user_enrolments} ue ON e.id = ue.enrolid 
            INNER JOIN {user} u ON u.id = ue.userid WHERE e.courseid = $courseid AND u.id != 1
            ");
            foreach ($users as $user) {
                $udetail[$user->userid] = $user->firstname . ' ' . $user->lastname;
            }
        }
        return $udetail;
    }
}
