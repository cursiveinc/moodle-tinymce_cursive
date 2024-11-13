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
 * Tiny cursive download json functionality.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../../config.php');
require_once(__DIR__ . '/locallib.php');
global $DB, $CFG;
require_login();

$resourceid = optional_param('resourceid', 0, PARAM_INT);
$userid = optional_param('user_id', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$fname = clean_param(optional_param('fname', '', PARAM_FILE), PARAM_FILE);

if ($cmid <= 0 || $userid <= 0) {
    throw new moodle_exception('invalidparameters', 'tiny_cursive');
}

$context = context_module::instance($cmid);
require_capability('tiny/cursive:writingreport', $context);

$dirname = $CFG->tempdir . '/userdata/';
if (!is_dir($dirname) || strpos(realpath($dirname), $CFG->dataroot) !== 0) {
    throw new moodle_exception('invaliddirectory', 'tiny_cursive');
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($fname) . "\"");

if ($fname) {
    $filename = $dirname . basename($fname);
    $realpath = realpath($filename);

    if (!file_exists($filename)) {
        $filerow = $DB->get_record('tiny_cursive_files', ['filename' => $fname]);
        if ($filerow && $filerow->content) {
            echo base64_decode($filerow->content);
            die();
        } else {
            redirect(get_local_referer(false), get_string('filenotfound', 'tiny_cursive'));
        }
    } else {
        if ($realpath === false || strpos($realpath, realpath($dirname)) !== 0) {
            redirect(get_local_referer(false), get_string('filenotfound', 'tiny_cursive'));
        } else {
            readfile($filename);
            die();
        }
    }
} else {
    $filename = $dirname . clean_filename($userid . '_' . $resourceid . '_' . $cmid . '_attempt.json');
    echo tiny_cursive_file_stream($filename);
    die();
}
