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
global $DB, $CFG, $USER, $COURSE;

if (isloggedin()) {


      require_once($CFG->libdir.'/ddllib.php');
      require_once($CFG->libdir.'/dmllib.php');

        echo"<script type=\"text/javascript\" src=\"$CFG->wwwroot/blocks/timestat/lib/ajax_connection.js\"></script>";
        $sql = 'SELECT max(id) FROM {log} WHERE userid=? and course=?';
        $registerid = $DB->get_field_sql($sql, array($USER->id, $COURSE->id));
        echo "  <script type='text/javascript'>
                var start_of_url='$CFG->wwwroot/blocks/timestat/update_register.php?id=$registerid&time=';
                var isPopup=".ispopupwindow().";
                </script> ";

        echo"<script type=\"text/javascript\" src=\"$CFG->wwwroot/blocks/timestat/lib/timestatscript.js\"></script>";

}

function ispopupwindow() {
    if (strpos($_SERVER['SCRIPT_NAME'], 'mod/chat/gui_header_js/') > 0) {
        return 'true';
    } else {
        return 'false';
    }
}