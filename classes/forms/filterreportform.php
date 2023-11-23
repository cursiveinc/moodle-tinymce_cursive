<?php

/**
 * @package tiny_cursive
 * @category tiny
 * @copyright  CTI <info@cursivetechnology.com>
 * @author eLearningstack
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');

class filterreportform extends moodleform
{
    public function definition()
    {
        global $DB, $USER;
        $courses=  $DB->get_records ('course');
        $options=array();
       
        foreach($courses as $course){
            $options[$course->id]=$course->fullname;
        }
        $mform = &$this->_form;
        $mform->addElement('select', 'coursename', 'Course', $options);
        $mform->addRule('coursename', null, 'required', null, 'client');  

    }

   


   
}
