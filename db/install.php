<?php
defined('MOODLE_INTERNAL') || die();
/**
 * Post installation procedure
 */
use core_external\util;
function xmldb_tiny_cursive_install() {
    global $DB;
    // Ensure the web service is enabled
    enable_webservice();
    enable_webservice_protocol('rest');

    $token = create_token_for_user();

}
/**
 * Enable web services in Moodle
 */
function enable_webservice() {
    set_config('enablewebservices', 1);
    set_config('enablewsdocumentation', 1);
}

/**
 * Enable a specific web service protocol
 *
 * @param string $protocol The protocol to enable ('rest', 'soap', etc.)
 */
function enable_webservice_protocol($protocol) {
    global $DB;
    set_config('webserviceprotocols', 'rest');
}
/**
 * Create a token for a given user
 *
 * @param int $userid The ID of the user to create the token for
 * @return string The created token
 */
function create_token_for_user() {
    global $DB;
    $amdinid=get_admin();

    $service_shortname = 'moodle_mobile_app'; // Replace with your service shortname
    $service = $DB->get_record('external_services',['shortname' => $service_shortname]);
    $token = util::generate_token(EXTERNAL_TOKEN_PERMANENT,$service,$amdinid->id,context_system::instance());

    return $token;
}
