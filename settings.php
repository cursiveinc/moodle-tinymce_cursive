<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Tiny cursive plugin settings.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$linktext = get_string('questimereport', 'tiny_cursive');
defined('MOODLE_INTERNAL') || die();
global $CFG, $PAGE;
$PAGE->requires->js_call_amd('tiny_cursive/token_approve', 'init', [1]);

if (is_siteadmin()) {
    $settings->add(new admin_setting_heading(
        'cursive_settings',
        '',
        get_string('pluginname_desc', 'tiny_cursive')
    ));
    $restweblink = $CFG->wwwroot . '/admin/settings.php?section=webserviceprotocols';
    $createtoken = $CFG->wwwroot . '/admin/webservice/tokens.php';
    $settings->add(new admin_setting_configtext(
        'tiny_cursive/secretkey',
        get_string('secretkey', 'tiny_cursive'),
        get_string('secretkey_desc', 'tiny_cursive') . '' .
        "<br/><a id='approve_token' href='#' class='btn btn-primary'>  Test Token </a>
            <span id='token_message'></span>",
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
        'tiny_cursive/host_url',
        'Moodle Host',
        'You Host domain.',
        $CFG->wwwroot,
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'tiny_cursive/confidence_threshold',
        'Confidence Threshold',
        'Each site may set its threshold for providing the successful match
        “green check” to the TypeID column for student submissions.
        We recommend .65. However, there may be arguments for lower or higher
        thresholds depending on your experience or academic honesty policy.',
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configcheckbox(
        'tiny_cursive/showcomments',
        'Enable Cite-Source',
        'Show cite-source comments under post when enabled',
        1
    ));

}
