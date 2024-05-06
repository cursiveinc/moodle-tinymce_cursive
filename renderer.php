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
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

defined('MOODLE_INTERNAL') || die;

require_login();

class tiny_cursive_renderer extends plugin_renderer_base {

    public function get_link_icon($score = 0) {
        $scoresetting = get_config('tiny_cursive', 'confidence_threshold');
        $scoresetting = $scoresetting ? $scoresetting : 0.65;

        $icon = 'fa fa-circle-o';
        $color = 'font-size:24px;color:black';
        if ($score == 200) {
            $icon = 'fa fa-info-circle';
            $color = 'font-size:24px;color:black';
        }
        else if ($score >= $scoresetting) {
            $icon = 'fa fa-check-circle';
            $color = 'font-size:24px;color:green';

        } else if ($score >= -1) {
            $icon = 'fa fa-question-circle';
            $color = 'font-size:24px;color:#A9A9A9';
        } else {
            $icon = 'fa fa-circle-o';
            $color = 'font-size:24px;color:black';
        }
        return '<i  class="' . $icon . '"' . ' style="' . $color . '";></i>';
    }

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
        $content .= html_writer::tag('p', ' ' .get_string('total_time','tiny_cursive') .''.
            sprintf('%02d:%02d',
                ($user->total_time_seconds / 60 % 60), $user->total_time_seconds % 60));
        $content .= html_writer::end_div();
        $content .= html_writer::start_div('position');
        $content .= html_writer::tag('p', get_string('average_min','tiny_cursive') .' ' . $user->words_per_minute);
        $content .= html_writer::end_div();
        $content .= html_writer::start_div('username');
        $content .= html_writer::tag('p', get_string('total_word','tiny_cursive') . $user->word_count);
        $content .= html_writer::end_div();
        $content .= html_writer::start_div('position');
        $content .= html_writer::tag('p', 'Backspace %: ' . $user->backspace_percent);
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();

        // Modal footer.
        $content .= html_writer::start_div('modal-footer');
        $content .= html_writer::tag('button', get_string('close', 'tiny_cursive'),
            ['class' => 'modal-close btn btn-primary', 'data-dismiss' => 'modal']);
        $content .= html_writer::end_div();

        // Close modal elements.
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        return $content;
    }

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
        $content .= html_writer::tag('button', get_string('close', 'tiny_cursive'),
            ['class' => 'modal-close btn btn-primary', 'data-dismiss' => 'modal']);
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        return $content;
    }

    public function get_playback_modal($user, $modulename = "title") {
        $content = html_writer::start_div('modal', ['id' => 'playback_' . $user->attemptid, 'role' => 'dialog']);
        $content .= html_writer::start_div('modal-dialog');
        $content .= html_writer::start_div('modal-content');
        $content .= html_writer::tag('div', 'Playback Video', ['class' => 'modal-header']);
        $content .= html_writer::start_div('modal-body');
        $content .= html_writer::start_div('div', ['id' => 'output_playback_'.$user->attemptid]);
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::start_div('modal-footer');
        $content .= html_writer::tag('button', get_string('close', 'tiny_cursive'),
            ['class' => 'modal-close btn btn-primary', 'data-dismiss' => 'modal']);
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();
        $content .= html_writer::end_div();

        return $content;
    }

    public function timer_report($users, $courseid, $page = 0, $limit = 10, $baseurl = '') {
        global $CFG, $OUTPUT;
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
            $linkicon = $this->get_link_icon($user->score);
            $modinfo = get_fast_modinfo($courseid);
            $cm = $modinfo->get_cm($user->cmid);
            $getmodulename = get_coursemodule_from_id($cm?->modname, $user->cmid, 0, false, MUST_EXIST);
            $content = $this->get_html_modal($user, $getmodulename->name);
            $scorecontent = $this->get_html_score_modal($user, $courseid > 0 ? $getmodulename->name : 'Score');
            $playbackcontent = $this->get_playback_modal($user, $courseid > 0 ? $getmodulename->name : 'Playback Video');
            $filepath = urlencode($CFG->wwwroot . '/lib/editor/tiny/plugins/cursive/userdata/' . $user->filename);
            $row = [];
            $row[] = $user->fileid;
            $row[] = $user->firstname ?? '' . ' ' . $user->lastname ?? '';
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
        echo $OUTPUT->paging_bar($totalcount, $page, $limit, $baseurl);
    }

    public function user_writing_report($users, $userprofile, $username, $page = 0, $limit = 5, $baseurl = '') {
        global $CFG, $DB, $OUTPUT;
        require_once($CFG->dirroot."/lib/editor/tiny/plugins/cursive/lib.php");


        echo get_string('total_word','tiny_cursive')." $userprofile->word_count</br>";
        $seconds = $userprofile->total_time;
        $secs = $seconds % 60;
        $hrs = $seconds / 60;
        $mins = $hrs % 60;
        $hrs = $hrs / 60;
        print (get_string('total_time','tiny_cursive')."" . (int)$hrs . "h:" . (int)$mins . "m:" . (int)$secs) . "s</br>";
        $avgwords = 0;
        if ($userprofile->total_time > 0) {

            $avgwords = round($userprofile->word_count / ($userprofile->total_time / 60));
        }

        echo get_string('average_min', 'tiny_cursive') . " " . $avgwords . "</br></br>";
        $courses = $DB->get_records_sql("select c.fullname,c.id from {course} c
      INNER JOIN {enrol} en ON en.courseid=c.id
      INNER JOIN {user_enrolments} ue ON ue.enrolid=en.id
      where ue.userid=$username");
        $options = [];

        echo"<div class='dropdown mb-4' >";
        echo'<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select Course</button>';
        // echo"<option value=''>Select Course</option>";
        $userid=0;
        echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
        $base_url=$CFG->wwwroot.'/lib/editor/tiny/plugins/cursive/my_writing_report.php?userid='.$userid.'&courseid=';
        echo" <a class='dropdown-item' href=$baseurl >All Courses</a>";
        foreach($courses as $course){
            echo" <a class='dropdown-item' href=$baseurl&courseid=$course->id >$course->fullname</a>";
        }
        echo '</div>';
        echo"</div >";
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
        foreach ($data as $user) {
            $first_file = $DB->get_record_sql('select id from {tiny_cursive_files} where userid =:userid ORDER BY id ASC',['userid' => $user->usrid]);

            $courseid = $user->courseid;
            $courseid = $courseid > 0 ? $courseid : '';
            $cm = null;

            $modinfo = ($courseid != '') ? get_fast_modinfo($courseid) : null;
            $cm = $modinfo != null ? $modinfo->get_cm($user->cmid) : null;
            $getmodulename = $cm != null ? get_coursemodule_from_id($cm->modname, $user->cmid, 0, false, MUST_EXIST) : 0;

            $scorecontent = $this->get_html_score_modal($user, $courseid > 0 ? $getmodulename->name : 'Score');

            if ($first_file->id == $user->fileid){
                $linkicon = $this->get_link_icon(200);
            }
            else{
                $linkicon = $this->get_link_icon($user->score);
            }



            $row = [];
            $content = $this->get_html_modal($user, $courseid > 0 ? $getmodulename->name : 'Stats');
            $playbackcontent = $this->get_playback_modal($user, $courseid > 0 ? $getmodulename->name : 'Playback Video');

            // Saving the file to moodledata
            $context = context_system::instance();

            // Creat URL of the json file from moodledata
            // $filepath = file_urlcreate ($context, $user);
            $filepath = $CFG->wwwroot. '/lib/editor/tiny/plugins/cursive/userdata/' . $user->filename;


            $row[] = $getmodulename ? $getmodulename->name : '';
            $row[] = date("l jS \of F Y h:i:s A", $user->timemodified);
            $row[] = '<a data-filepath ="'.$filepath.'" data-id=playback_' . $user->attemptid . '  href ="#" class = "video_playback_icon">
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
        echo html_writer::start_tag('span', ['class' => 'ml-2'], get_string("learn_more", "tiny_cursive"));
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
        echo $OUTPUT->paging_bar($totalcount, $page, $limit, $baseurl);
    }
}

