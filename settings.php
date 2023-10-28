<?php

/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

$linktext = get_string('questimereport', 'tiny_cursive'); 
defined('MOODLE_INTERNAL') || die();
global $CFG,$PAGE;
$PAGE->requires->js_call_amd('tiny_cursive/token_approve', 'init', array(1));

if (is_siteadmin()) {
    $settings->add(new admin_setting_heading(
        'cursive_settings',
        '',
        get_string('pluginname_desc', 'tiny_cursive')
    ));
    $rest_web_link = $CFG->wwwroot . '/admin/settings.php?section=webserviceprotocols';
    $create_token = $CFG->wwwroot . '/admin/webservice/tokens.php';
    $settings->add(new admin_setting_configtext(
        'tiny_cursive/secretkey',
        get_string('secretkey', 'tiny_cursive'),
        get_string('secretkey_desc', 'tiny_cursive').''."<br/><a id='approve_token' href='#' class='btn btn-primary'>  Test Token </a>  <span id='token_message'></span>",
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'tiny_cursive/python_server',
        'tiny_cursive',
        'python_server',
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configcheckbox(
        'tiny_cursive/showcomments',
        'showcomments',
        'Show comments under post when enabled',
        1
    ));
   
}
