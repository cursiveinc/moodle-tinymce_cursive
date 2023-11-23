<?php

/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

$linktext = get_string('tiny_cursive', 'tiny_cursive'); 
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
        'API URL',
        'API address URL',
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'tiny_cursive/confidence_threshold',
        'Confidence Threshold',
        'Each site may set its threshold for providing the successful match “green check” to the TypeID column for student submissions. We recommend .65. However, there may be arguments for lower or higher thresholds depending on your experience or academic honesty policy.',
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configcheckbox(
        'tiny_cursive/showcomments',
        'Show Comments',
        'Show comments under post when enabled',
        1
    ));
   
}
