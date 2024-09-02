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
 * Tiny cursive plugin.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Cursive';
$string['coursename'] = 'Course name';
$string['pluginname_desc'] = 'This plugin configuration provides copy+paste interruption by default. To connect to the Cursive Machine Learning Server for authorship and writing analytics, please enter your token and server details below. If you do not have these, please contact info@cursivetechnology.com. ';
$string['secretkey'] = 'Cursive Secret Key';
$string['secretkey_desc'] = 'The API Secret Key of Cursive account';
$string['quizname'] = 'Activity name';
$string['tiny_cursive'] = 'Authorship and Analytics';
$string['serialno'] = 'Sr. No';
$string['userename'] = 'User name';
$string['username'] = 'User name';
$string['questionname'] = 'Question';
$string['questimereport'] = 'Question Report';
$string['attemptid'] = 'Attempt id';
$string['selectcourse'] = 'Select a Course';
$string['selectuser'] = 'Select a User';
$string['selectmodule'] = 'Select Module';
$string['enter_numericvalue'] = 'Please Enter Numeric Value';
$string['enter_nonzerovalue'] = 'Please Select atleast 5 seconds';
$string['email_exist'] = 'Email does not exist';
$string['total_time'] = 'Total Time';
$string['field_require'] = 'This field is required';
$string['downlaod_pdf'] = 'Download report as pdf';
$string["backto_setting"] = "Go to Setting ";
$string['downlaod_csv'] = 'Download report as csv';
$string["orderby"] = "Order By";
$string["modulename"] = "Module Name";
$string["writing"] = "Writing Report";
$string["all_writing"] = "All writing";
$string["confidence_threshold"] = "0.65";
$string["student_writing_statics"] = "Writing Statistics";
$string["download"] = "Download";
$string["download_attempt_json"] = "Download Attempt JSON";
$string["module_name"] = "Module Name";
$string["last_modified"] = "Last modified";
$string["analytics"] = "Analytics";
$string["typeid"] = "TypeID";
$string["learn_more"] = "Learn More";
$string["close"] = "Close";
$string['backspace'] = 'Backspace';
$string['cursivewritingreport'] = "Cursive Writing Report";
$string['nopaylod'] = "No payload data received yet";
$string['sourceurl'] = "sourceurl";
$string['paste'] = "Paste Text ";
$string['allmodule'] = "All Modules";
$string['alluser'] = "All Users";

$string["average_min"] = "Average Words Per minute:";
$string["all_course"] = "All Courses";
$string["select_course"] = "Select Course";
$string["total_time_writing"] = "Total Time Writing:";
$string['time_writing'] = "Time Writing: ";
$string["total_word"] = "Total Words:";
$string['wordpermin'] = "Words per minute:";
$string['helplinktext'] = "Cursive";
$string['warning'] = "You have no permissions to access the page.";
$string['filenotfound'] = "File not found!";
$string['cursive:view'] = 'View Writing Reports';
$string['cursive:editsettings'] = "Access Plugin Settings";
$string['cursive:writingreport'] = "Access to analytics";
$string['cursive:mywritingreport'] = "Accessing to my writing report";
$string['errorverifyingtoken'] = "Error verifying token";
$string['curlerror'] = "Curl error";
$string['cursive:write'] = 'Write JSON File or Insert records';

$string['privacy:metadata:database:tiny_cursive'] = 'Information about the tiny cursive data.';
$string['privacy:metadata:database:tiny_cursive:userid'] = 'The ID of the user who provided the data.';
$string['privacy:metadata:database:tiny_cursive:timemodified'] = 'The time when the data was last modified.';
$string['privacy:metadata:database:tiny_cursive_comments'] = 'Information about the tiny cursive comments data.';
$string['privacy:metadata:database:tiny_cursive_comments:userid'] = 'The ID of the user who provided the comment.';
$string['privacy:metadata:database:tiny_cursive_comments:commenttext'] = 'The text of the comment.';
$string['privacy:metadata:database:tiny_cursive_comments:timemodified'] = 'The time when the comment was last modified.';
$string['privacy:metadata:tiny_cursive'] = 'Tiny cursive plugin user data.';

$string['selectquiz'] = 'Select a quiz';
$string['field_required'] = 'This field is required';
$string['selecttime'] = 'Select time option';
$string['stndtime'] = 'Standard time';
$string['queswise'] = 'Question-wise';
$string['enter_time'] = 'Enter time';
$string['timesave_success'] = 'Time saved successfully';
$string['timesave_successfull'] = 'Time save operation was successful';
$string['download_csv'] = "Download cumulative Report";
$string['random_reflex'] = 'Your Random Reflection Prompt';
$string['authorship'] = 'Authorship Confidence: ';
$string['copy_behave'] = 'Copy Behavior: ';
$string['playback'] = 'Playback Video';
$string['fulname'] = 'Full Name';
$string['email'] = 'Email';
$string['selectcrs'] = 'Select Course';
$string['wractivityreport'] = "Writing Activity Report";
$string['data_save'] = 'Data saved successfully';
$string['success'] = 'success';
$string['failed'] = 'failed';

$string['test_token'] = "Test Token";
$string['api_url'] = 'API URL';
$string['api_addr_url'] = 'API address URL';
$string['moodle_host'] = 'Moodle Host';
$string['host_domain'] = 'You Host domain.';
$string['confidence_thresholds'] = 'Confidence Threshold';
$string['thresold_description'] = 'Each site may set its threshold for providing the successful match
        “green check” to the TypeID column for student submissions.
        We recommend .65. However, there may be arguments for lower or higher
        thresholds depending on your experience or academic honesty policy.';
$string['cite_src'] = 'Enable Cite-Source';
$string['cite_src_des'] = 'Show cite-source comments under post when enabled';
