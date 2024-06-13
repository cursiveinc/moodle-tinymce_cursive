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

defined('MOODLE_INTERNAL') || die;

require_login();

/**
 * Tiny cursive plugin.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tiny_cursive_renderer extends plugin_renderer_base {

    /**
     * get_link_icon
     *
     * @param $score
     * @return string
     * @throws dml_exception
     */
    public function get_link_icon($score,$firstfile = 0) {
        $scoresetting = get_config('tiny_cursive', 'confidence_threshold');
        $scoresetting = $scoresetting ? $scoresetting : 0.65;
        $icon = 'fa fa-circle-o';
        $color = 'font-size:24px;color:black';
        if ($firstfile) {
            $icon = 'fa  fa fa-solid fa-info-circle typeid';
            $color = 'font-size:24px;color:#000000';
        }else {
            if ($score >= $scoresetting && $score != null) {
                $icon = 'fa fa-check-circle typeid';
                $color = 'font-size:24px;color:green';
            } else if ($score < $scoresetting && $score != null) {
                $icon = 'fa fa-question-circle typeid';
                $color = 'font-size:24px;color:#A9A9A9';
            } else {
                $icon = 'fa fa-circle-o typeid';
                $color = 'font-size:24px;color:black';
            }
    }
        return '<i  class="' . $icon . '"' . ' style="' . $color . '";></i>';
    }

    /**
     * get_html_modal
     *
     * @param $user
     * @param $modulename
     * @return string
     * @throws coding_exception
     */
    public function get_html_modal($user, $modulename = "title") {
        // Start constructing the modal HTML.
        $content = html_writer::start_div('modal', ['id' => $user->attemptid, 'role' => 'dialog']);
        $content .= html_writer::start_div('modal-dialog');
        $content .= html_writer::start_div('modal-content');

        // Modal header.
        $content .= html_writer::start_div('modal-header');
        $content .= html_writer::tag('h4', $modulename, ['class' => 'modal-title']);
        $content .= html_writer::end_div();

        // Modal body.
        $content .= html_writer::start_div('modal-body');
        $content .= html_writer::start_div('position');

        if (isset($user->total_time_seconds) && $user->total_time_seconds > 0) {
            // Format the time using the date function.
            $formattedTime = date('H:i:s', mktime(0, 0, $user->total_time_seconds));
        } else {
            // Handle the case when there is no time data.
            $formattedTime = '00:00:00';
        }

        // Use html_writer::tag to create the content with the formatted time.
        $content .= html_writer::tag(
            'p',
            ' ' . get_string('total_time', 'tiny_cursive') . ': ' . $formattedTime
        );
        $content .= html_writer::end_div();
        $content .= html_writer::start_div('position');
        $content .= html_writer::tag('p', get_string('average_min', 'tiny_cursive') . ' ' . $user->words_per_minute);
        $content .= html_writer::end_div();
        $content .= html_writer::start_div('username');
        $content .= html_writer::tag('p', get_string('total_word', 'tiny_cursive') . $user->word_count);
        $content .= html_writer::end_div();
        $content .= html_writer::start_div('position');
        $content .= html_writer::tag('p', 'Backspace %: ' . $user->backspace_percent);
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();

        // Modal footer.
        $content .= html_writer::start_div('modal-footer');
        $content .= html_writer::tag(
            'button',
            get_string('close', 'tiny_cursive'),
            ['class' => 'modal-close btn btn-primary', 'data-dismiss' => 'modal']
        );
        $content .= html_writer::end_div();

        // Close modal elements.
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        return $content;
    }

    /**
     * get_html_score_modal
     *
     * @param $user
     * @param $modulename
     * @return string
     * @throws coding_exception
     */
    public function get_html_score_modal($user, $modulename = "title") {
        $content = html_writer::start_div('modal', ['id' => 'score' . $user->attemptid, 'role' => 'dialog']);
        $content .= html_writer::start_div('modal-dialog');
        $content .= html_writer::start_div('modal-content');
        $content .= html_writer::tag('div', 'Your Random Reflection Prompt', ['class' => 'modal-header']);
        $content .= html_writer::start_div('modal-body');
        $content .= html_writer::tag('div', 'Authorship Confidence: ' . $user->score, ['class' => 'position']);
        $content .= html_writer::tag('div', 'Copy Behavior: ' . $user->copy_behavior, ['class' => 'position']);
        $content .= html_writer::end_div();
        $content .= html_writer::start_div('modal-footer');
        $content .= html_writer::tag(
            'button',
            get_string('close', 'tiny_cursive'),
            ['class' => 'modal-close btn btn-primary', 'data-dismiss' => 'modal']
        );
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        return $content;
    }

    /**
     * get_playback_modal
     *
     * @param $user
     * @param $modulename
     * @return string
     * @throws coding_exception
     */
    public function get_playback_modal($user, $modulename = "title") {
        $content = html_writer::start_div('modal', ['id' => 'playback_' . $user->attemptid, 'role' => 'dialog']);
        $content .= html_writer::start_div('modal-dialog');
        $content .= html_writer::start_div('modal-content');
        $content .= html_writer::tag('div', 'Playback Video', ['class' => 'modal-header']);
        $content .= html_writer::start_div('modal-body');
        $content .= html_writer::start_div('div', ['id' => 'output_playback_' . $user->attemptid]);
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::start_div('modal-footer');
        $content .= html_writer::tag(
            'button',
            get_string('close', 'tiny_cursive'),
            ['class' => 'modal-close btn btn-primary', 'data-dismiss' => 'modal']
        );
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();

        return $content;
    }

    /**
     * timer_report
     *
     * @param $users
     * @param $courseid
     * @param $page
     * @param $limit
     * @param $baseurl
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function timer_report($users, $courseid, $page = 0, $limit = 10, $baseurl = '') {
        global $CFG, $DB;
        $totalcount = $users['count'];
        $data = $users['data'];

        $table = new html_table();
        $table->head = [
            'Attemptid',
            'Full Name',
            'Email',
            get_string('module_name', 'tiny_cursive'),
            get_string('last_modified', 'tiny_cursive'),
            get_string('playback', 'tiny_cursive'),
            get_string('analytics', 'tiny_cursive'),
            get_string('typeid', 'tiny_cursive'),
            '',
        ];

        $sr = 1;
        foreach ($data as $user) {
            $sql = 'SELECT id as fileid
            FROM {tiny_cursive_files} WHERE userid = :userid ORDER BY id ASC Limit 1';

                $ffile = $DB->get_record_sql($sql, ['userid' => $user->usrid]);
                $ffile = (array)$ffile;
                if ($ffile['fileid'] == $user->fileid) {
                    $firstfile= 1;
                } else {
                    $firstfile = 0;
                }

            $linkicon = $this->get_link_icon($user->score,$firstfile);
            $modinfo = get_fast_modinfo($courseid);
            $cm = $modinfo->get_cm($user->cmid);
            $getmodulename = get_coursemodule_from_id($cm?->modname, $user->cmid, 0, false, MUST_EXIST);
            $content = $this->get_html_modal($user, $getmodulename->name);
            $scorecontent = $this->get_html_score_modal($user, $courseid > 0 ? $getmodulename->name : 'Score');
            $playbackcontent = $this->get_playback_modal($user, $courseid > 0 ? $getmodulename->name : 'Playback Video');
            $filepath = file_exists($CFG->dataroot . '/temp/userdata/' . $user->filename)?urlencode($CFG->dataroot . '/temp/userdata/' . $user->filename):null;
            $row = [];
            $row[] = $user->fileid;
            $row[] = $user->firstname . ' ' . $user->lastname ?? '';
            $row[] = $user->email;
            $row[] = $getmodulename->name;
            $row[] = date("l jS \of F Y h:i:s A", $user->timemodified);
            $row[] = '<a data-filepath ="' . $filepath . '" data-id=playback_' . $user->attemptid . '
            href ="#" class = "video_playback_icon">
            <i class="fa fa fa-circle-play"
            style="font-size:24px;color:black" aria-hidden="true"
            style = "padding-left:25px; font-size:x-large;"></i>
            </a>' . $playbackcontent;

            $row[] = '<a data-id=' . $user->attemptid . ' href = "#" class="popup_item">
            <i class="fa fa-area-chart" style="font-size:24px;color:black"
            aria-hidden="true" style = "padding-left:25px; font-size:x-large;"></i>
            </a>' . $content;

            $row[] = "<a data-id=score" . $user->attemptid . "
            href ='#' class = 'link_icon'>" . $linkicon . "</a>" . $scorecontent;

            $row[] = html_writer::link(
                new moodle_url('/lib/editor/tiny/plugins/cursive/download_json.php', [
                    'fname' => $user->filename,
                    'quizid' => 2,
                    'user_id' => $user->usrid
                ]),
                get_string('download', 'tiny_cursive'),
                [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-right:50px;',
                    'aria-describedby' => get_string('download_attempt_json', 'tiny_cursive'),
                    'role' => 'button',
                ]
            );
            $table->data[] = $row;
        };
        echo html_writer::table($table);
        echo $this->output->paging_bar($totalcount, $page, $limit, $baseurl);
    }

    /**
     * user_writing_report
     *
     * @param $users
     * @param $userprofile
     * @param $username
     * @param $page
     * @param $limit
     * @param $baseurl
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function user_writing_report($users, $userprofile, $userid, $page = 0, $limit = 5, $baseurl = '') {
        global $CFG, $DB, $PAGE;
        require_once ($CFG->dirroot . "/lib/editor/tiny/plugins/cursive/lib.php");

        echo get_string('total_word', 'tiny_cursive') . " $userprofile->word_count</br>";

        if (isset($userprofile->total_time) && $userprofile->total_time > 0) {
            $seconds = $userprofile->total_time;

            // Create a DateInterval from the total seconds.
            $interval = new DateInterval('PT' . $seconds . 'S');

            // Create a DateTime object and add the interval to it.
            $datetime = new DateTime('@0');
            $datetime->add($interval);

            // Extract hours, minutes, and seconds.
            $hrs = $datetime->format('G'); // 'G' is used for 24-hour format without leading zeros.
            $mins = $datetime->format('i'); // 'i' is used for minutes with leading zeros.
            $secs = $datetime->format('s'); // 's' is used for seconds with leading zeros.

            echo get_string('total_time', 'tiny_cursive') . ": " . (int)$hrs . "h : " . (int)$mins . "m : " . (int)$secs . "s</br>";

            // Calculate average words per minute if total time is greater than zero.
            $avgwords = round($userprofile->word_count / ($userprofile->total_time / 60));
        } else {
            // Handle the case when there is no time data.
            echo get_string('total_time', 'tiny_cursive') . " 0h:0m:0s</br>";
            $avgwords = 0;
        }

        echo get_string('average_min', 'tiny_cursive') . " " . $avgwords . "</br></br>";
        $courses = $DB->get_records_sql("
        SELECT c.fullname, c.id, u.id AS userid, u.firstname, u.lastname
        FROM {course} c
        INNER JOIN {enrol} en ON en.courseid = c.id
        INNER JOIN {user_enrolments} ue ON ue.enrolid = en.id
        INNER JOIN {user} u ON u.id = ue.userid
        WHERE ue.userid = :userid", ['userid' => $userid]);

        // Remove 'courseid' from the base URL if it exists
        $currentUrl = new moodle_url($baseurl, ['userid' => $userid, 'courseid' => null]);
        $allCoursesUrl = $currentUrl->out(false, ['courseid' => null]);

        echo "<div class='dropdown mb-4'>";
        echo '<button class="btn btn-secondary dropdown-toggle" type="button"
        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false">Select Course</button>';

        echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
        $allCoursesClass = empty($_GET['courseid']) ? 'active' : '';
        echo '<a class="dropdown-item ' . $allCoursesClass . '" href="' . $allCoursesUrl . '">All Courses</a>';

        foreach ($courses as $course) {
            $courseUrl = new moodle_url($baseurl, ['userid' => $userid, 'courseid' => $course->id]);
            $courseClass = (isset($_GET['courseid']) && $_GET['courseid'] == $course->id) ? 'active' : '';
            echo '<a class="dropdown-item ' . $courseClass . '" href="' . $courseUrl . '">' . $course->fullname . '</a>';
        }

        echo '</div>';
        echo "</div>";

        $table = new html_table();
        $table->id = 'writing_report_table';

        $totalcount = $users['count'];
        $data = $users['data'];

        $table->head = [
            get_string('module_name', 'tiny_cursive'),
            get_string('last_modified', 'tiny_cursive'),
            get_string('playback', 'tiny_cursive'),
            get_string('analytics', 'tiny_cursive'),
            get_string('typeid', 'tiny_cursive'),
            '',
        ];

        $firstfile = $DB->get_record_sql(
            'SELECT id FROM {tiny_cursive_files} WHERE userid = :userid ORDER BY id ASC LIMIT 1',
            ['userid' => $userid]
        );

        foreach ($data as $user) {
            $courseid = $user->courseid;
            $cm = null;

            if ($courseid) {
                $modinfo = get_fast_modinfo($courseid);
                if ($modinfo) {
                    $cm = $modinfo->get_cm($user->cmid);
                }
            }

            $getmodulename = $cm ? get_coursemodule_from_id($cm->modname, $user->cmid, 0, false, MUST_EXIST) : null;

            $scorecontent = $this->get_html_score_modal($user, $getmodulename ? $getmodulename->name : 'Score');

            if ($firstfile->id == $user->fileid) {
                $linkicon = $this->get_link_icon(200, $firstfile = 1);
            } else {
                $linkicon = $this->get_link_icon($user->score);
            }

            $row = [];
            $content = $this->get_html_modal($user, $courseid > 0 ? $getmodulename->name : 'Stats');
            $playbackcontent = $this->get_playback_modal($user, $courseid > 0 ? $getmodulename->name : 'Playback Video');

            $filep = $CFG->dataroot . '/temp/userdata/' . $user->filename;
            $filepath = file_exists($filep) ? $filep : null;

            $row[] = $getmodulename ? $getmodulename->name : '';
            $row[] = date("l jS \of F Y h:i:s A", $user->timemodified);
            $row[] = '<a data-filepath="' . $filepath . '" data-id="playback_' . $user->attemptid . '"
            href="#" class="video_playback_icon">
            <i class="fa fa-circle-play" style="font-size:24px;color:black" aria-hidden="true"></i>
            </a>' . $playbackcontent;
            $row[] = '<a data-id="' . $user->attemptid . '" href="#" class="popup_item">
            <i class="fa fa-area-chart" style="font-size:24px;color:black" aria-hidden="true"></i>
            </a>' . $content;
            $row[] = '<a data-id="score' . $user->attemptid . '" href="#" class="link_icon">' . $linkicon . '</a>' . $scorecontent;
            $row[] = html_writer::link(
                new moodle_url('/lib/editor/tiny/plugins/cursive/download_json.php', [
                    'fname' => $user->filename,
                    'quizid' => 2,
                    'user_id' => $user->usrid,
                ]),
                get_string('download', 'tiny_cursive'),
                [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-right:50px;',
                    'aria-describedby' => get_string('download_attempt_json', 'tiny_cursive'),
                    'role' => 'button',
                ]
            );
            $table->data[] = $row;
        }

        echo html_writer::table($table);

        $pagingbar = new paging_bar($totalcount, $page, $limit, $baseurl);
        echo $this->output->render($pagingbar);

        echo html_writer::start_tag('div', ['class' => 'text-right']);
        echo html_writer::start_tag('a', [
            'class' => 'btn btn-icon bg-secondary icon-no-margin',
            'href' => 'https://docs.moodle.org/403/en/Cursive',
            'target' => '_blank',
        ]);
        echo html_writer::empty_tag('i', [
            'class' => 'icon fa fa-question fa-fw',
            'style' => 'position: relative; top: 10px;',
            'aria-hidden' => 'true',
        ]);
        echo html_writer::end_tag('i');
        echo html_writer::end_tag('a');
        echo html_writer::start_tag('span', ['class' => 'ml-2']);
        echo get_string("learn_more", "tiny_cursive");
        echo html_writer::end_tag('span');
        echo html_writer::end_tag('div');

        echo "<script>
        document.querySelectorAll('#writing_report_table tr th').forEach((el, index) => {
            switch (index) {
                case 0:
                    el.setAttribute('aria-describedby','');
                    break;
                case 1:
                    el.setAttribute('aria-describedby','');
                    break;
                case 2:
                    el.setAttribute('aria-describedby','');
                    break;
                case 3:
                    el.setAttribute('aria-describedby','');
                    break;
                case 4:
                    el.setAttribute('aria-describedby','TypeID is a confidence score related to authorship');
                    break;
            }
        });
    </script>";
    }


}

