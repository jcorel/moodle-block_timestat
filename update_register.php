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

  require_once('../../config.php');
  global $DB, $CFG, $USER, $COURSE;

if (isloggedin()) {

    if ($record = $DB->get_record('log', array('id' => required_param('id', PARAM_INT)))) {
        if ($record->userid == $USER->id && $record->course = $COURSE->id) {
            if ($recordbt = $DB->get_record('block_timestat', array('log_id' => $record->id ))) {
                $recordbt->timespent = $recordbt->timespent + required_param('time', PARAM_INT);
                $DB->update_record('block_timestat', $recordbt);
            } else {
                $recordbt = new stdClass();
                $recordbt->log_id = $record->id;
                $recordbt->timespent = required_param('time', PARAM_INT);
                $DB->insert_record('block_timestat', $recordbt);
            }
        }
    }
}