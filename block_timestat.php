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
 *
 * @package    block_timestat
 * @copyright  2014 Barbara Dębska, Łukasz Sanokowski, Łukasz Musiał
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_timestat extends block_base {

    function init() {
        $this->title = get_string('blocktitle', 'block_timestat');

    }

    function get_content() {
        global $CFG, $COURSE, $USER;
        $context = context_block::instance($this->instance->id);

        if (!has_capability('block/timestat:view', $context)) {
            $this->content = null;
            return $this->content;
        } else {
            $this->content = new stdClass;
            $url = $CFG->wwwroot.'/blocks/timestat/index.php?id='.$COURSE->id;
            $this->content->text = '<center><a href="'.$url.'">'.get_string('link', 'block_timestat').'<img src="'.
            $CFG->wwwroot.'/blocks/timestat/images/clock.gif" class="icon" alt="brak" /></a></center>';
            $this->content->footer = null;
            return $this->content;
        }
    }


    function applicable_formats() {
         return array(
                 'site-index' => false,
                 'course-view' => true,
                'course-view-social' => true,
                 'mod' => false,
                 'mod-quiz' => false,
         'course' => true
           );
    }

    function instance_allow_multiple() {
        return false;
    }

}