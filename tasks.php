<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();
$tasks = [
    [
        'classname' => 'cursive\task\upload_student_json',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '0',
        'day' => '1',
        'month' => '0',
        'dayofweek' => '0',
    ],
];