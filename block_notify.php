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
 * Block Notify
 *
 * @package    block_notify
 * @copyright  Marty Gilbert <martygilbert@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_notify extends block_base {

    public function init() {
        $this->title = get_string('blockdispname', 'block_notify');
    }

    public function get_content() {
        global $CFG, $OUTPUT, $USER, $COURSE, $DB, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $messages = $DB->get_records('block_notify', array('mdluserid' => $USER->id, 'courseid' => $COURSE->id));

        if (!$messages) {
            return;
        }

        $now = time();
        $nummessages = 0;
        $this->content->text = ' ';
        $message = '';

        foreach ($messages as $msg) {

            if ($now > $msg->end || $now < $msg->start) {
                continue;
            }

            $nummessages++;
            $this->content->text .= '<h3>'.$msg->title.'</h3>'."\n";
            $this->content->text .= $msg->message;

        }

        if ($nummessages == 0) {
            $this->content->text = '';
            return;
        }

        return $this->content;
    }

    public function applicable_formats() {
        return array('all' => false,
                     'site' => true,
                     'site-index' => true,
                     'course-view' => false,
                     'course-view-social' => false,
                     'mod' => false,
                     'mod-quiz' => false);
    }

    public function instance_allow_multiple() {
          return false;
    }

    public function has_config() {
        return true;
    }

    public function instance_delete() {
        global $DB;
        $DB->delete_records('block_notify', array('courseid' => 1));
    }

    public function cron() {
            mtrace( "Hey, my cron script is running" );
            return true;
    }

    public function _self_test() {
        return true;
    }
}
