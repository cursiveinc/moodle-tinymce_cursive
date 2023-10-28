<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();
 
$observers  = array( 
    array(
        'eventname' => '\mod_forum\event\post_created',
        'callback' => '\tiny_cursive\observers::observer_login',
        'internal'    => true,
        'priority'    => 9999,
    ),
    array(
        'eventname' => '\mod_forum\event\post_updated',
        'callback' => '\tiny_cursive\observers::post_updated',
        'internal'    => true,
        'priority'    => 9999,
    ),
    array(
        'eventname' => '\mod_forum\event\discussion_created',
        'callback' => '\tiny_cursive\observers::discussion_created',
        'internal'    => true,
        'priority'    => 9999,
    ),
    array(
        'eventname' => '\mod_assign\event\submission_created',
        'callback' => '\tiny_cursive\observers::submission_created',
        'internal'    => true,
        'priority'    => 9999,
    ),
    array(
        'eventname' => '\mod_assign\event\assessable_submitted',
        'callback' => '\tiny_cursive\observers::assessable_submitted',
        'internal'    => true,
        'priority'    => 9999,
    )
    
);

