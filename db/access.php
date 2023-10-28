<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();
$capabilities = array(
    // Users with this capability are exhempt from the requirements that they
    // must be using the Secure browser to attempt or preview the quiz.
    // Note that teachers will already be exempt from the check by virtue of
    // having the mod/quiz:preview capability.
    'tiny_cursive/cursive:editsettings' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
        ),
        
    )
   
);