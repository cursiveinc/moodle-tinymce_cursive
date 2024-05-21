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
require_login();

$resourceid = optional_param('resourceid', 0, PARAM_INT);
$userid = optional_param('user_id', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$fname = optional_param('fname', '', PARAM_RAW);

$filename = '';
$dirname = __DIR__ . '/userdata/';
if ($fname) {
    $filename = $dirname . $fname;
} else {
    $filename = $dirname . $userid . '_' . $resourceid . '_' . $cmid . '_attempt' . '.json';
}

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
flush();
$inp = file_get_contents($filename);
echo $inp;
die();
