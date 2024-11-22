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
require_once(__DIR__.'/locallib.php');
global $DB, $CFG;
require_login();

$resourceid = optional_param('resourceid', 0, PARAM_INT);
$userid = optional_param('user_id', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$fname = optional_param('fname', '', PARAM_TEXT);

$context = context_module::instance($cmid);
require_capability('tiny/cursive:writingreport', $context);

$filename = '';
$dirname = $CFG->tempdir . '/userdata/';

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($fname) . "\"");
flush();

if ($fname) {
    $filename = $dirname . $fname;
    if (!file_exists($filename)) {
        $filerow = $DB->get_record('tiny_cursive_files', ['filename' => $fname]);
        if ($filerow->content) {
            $content = file_stream($filerow->content, $fname);
            echo $content;
            die();
        } else {
            $url = new moodle_url('/lib/editor/tiny/plugins/cursive/writing_report.php?userid=' . $userid);
            return redirect($url, get_string('filenotfound', 'tiny_cursive'));
        }
    } else {
        readfile($filename);
        die();
    }
} else {
    $filename = $dirname . $userid . '_' . $resourceid . '_' . $cmid . '_attempt' . '.json';
    $content = file_stream($filename, $filename);
    echo $content;
    die();
}
