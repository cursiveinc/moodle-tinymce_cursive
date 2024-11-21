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

global $CFG, $PAGE;
$PAGE->requires->js_call_amd('tiny_cursive/token_approve', 'init', [1]);

if (is_siteadmin()) {
    $settings->add(
        new admin_setting_heading(
            'cursive_settings',
            '',
            get_string('pluginname_desc', 'tiny_cursive')
        )
    );
    $restweblink = $CFG->wwwroot . '/admin/settings.php?section=webserviceprotocols';
    $createtoken = $CFG->wwwroot . '/admin/webservice/tokens.php';
    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/secretkey',
            get_string('secretkey', 'tiny_cursive'),
            get_string('secretkey_desc', 'tiny_cursive') . '' .
            "<br/><a id='approve_token' href='#' class='btn btn-primary'>  " . get_string('test_token', 'tiny_cursive') . " </a>
            <span id='token_message'></span>",
            "", PARAM_TEXT
        )
    );
    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/python_server',
            get_string('api_url', 'tiny_cursive'),
            get_string('api_addr_url', 'tiny_cursive'),
            '',
            PARAM_TEXT
        )
    );
    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/host_url',
            get_string('moodle_host', 'tiny_cursive'),
            get_string('host_domain', 'tiny_cursive'),
            $CFG->wwwroot,
            PARAM_TEXT
        )
    );
    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/confidence_threshold',
            get_string('confidence_thresholds', 'tiny_cursive'),
            get_string('thresold_description', 'tiny_cursive'),
            '',
            PARAM_TEXT
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'tiny_cursive/showcomments',
            get_string('cite_src', "tiny_cursive"),
            get_string('cite_src_des', 'tiny_cursive'),
            1
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/cursivetoken',
            get_string('webservicetoken', "tiny_cursive"),
            "<a id='generate_cursivetoken' href='#' class=''>  " .
            get_string('generate', 'tiny_cursive') . " </a>".' '.
            get_string('webservicetoken_des', 'tiny_cursive')."<br><span id='cursivetoken_'></span>",
            '',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/syncinterval',
            get_string('syncinterval', 'tiny_cursive'),
            get_string('syncinterval_des', 'tiny_cursive'),
            '10 sec',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_heading(
            'cursive_settings_footer',
            get_string('sectionadvance', 'tiny_cursive'),
            get_string('sectionadvance_desc', 'tiny_cursive')
        )
    );
    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/word_len_mean',
            get_string('word_len_mean', 'tiny_cursive'),
            get_string('word_len_mean_des', 'tiny_cursive'),
            4.66,
            PARAM_TEXT
        ));

        $settings->add(
            new admin_setting_configtext(
                'tiny_cursive/edits',
                get_string('edits', 'tiny_cursive'),
                get_string('edits_des', 'tiny_cursive'),
                178.13,
                PARAM_TEXT
            ));

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/p_burst_cnt',
            get_string('p_burst_cnt', 'tiny_cursive'),
            get_string('p_burst_cnt_des', 'tiny_cursive'),
            22.7,
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/p_burst_mean',
            get_string('p_burst_mean', 'tiny_cursive'),
            get_string('p_burst_mean_des', 'tiny_cursive'),
            82.14,
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/q_count',
            get_string('q_count', 'tiny_cursive'),
            get_string('q_count_des', 'tiny_cursive'),
            1043.92,
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/sentence_count',
            get_string('sentence_count', 'tiny_cursive'),
            get_string('sentence_count_des', 'tiny_cursive'),
            13.36,
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/total_active_time',
            get_string('total_active_time', 'tiny_cursive'),
            get_string('total_active_time_des', 'tiny_cursive'),
            21.58,
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/verbosity',
            get_string('verbosity', 'tiny_cursive'),
            get_string('verbosity_des', 'tiny_cursive'),
            1617.83,
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/word_count',
            get_string('word_count', 'tiny_cursive'),
            get_string('word_count_des', 'tiny_cursive'),
            190.67,
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'tiny_cursive/sent_word_count_mean',
            get_string('sent_word_count_mean', 'tiny_cursive'),
            get_string('sent_word_count_mean_des', 'tiny_cursive'),
            14.27170659,
            PARAM_TEXT
        )
    );

}
