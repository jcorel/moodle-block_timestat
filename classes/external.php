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
 * This is the external API for this component.
 *
 * @package    block_timestat
 * @copyright  2022 Jorge C. {}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_timestat;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use core_course\external\course_summary_exporter;
use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

/**
 * This is the external API for this component.
 *
 * @copyright  2020 Mathew May {@link https://mathew.solutions}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * update_register_parameters
     *
     * @return external_function_parameters
     */
    public static function update_register_parameters() {
        return new external_function_parameters(
                array(
                        'timespent' => new external_value(PARAM_INT),
                        'registerid' => new external_value(PARAM_RAW),
                )
        );
    }

    /**
     * For some given input find and return any course that matches it.
     *
     * @param string $timespent The user time spent
     */
    public static function update_register(string $timespent, $registerid) {
        global $DB;
        
        $params = self::validate_parameters(
                self::update_register_parameters(),
                ['timespent' => $timespent, 'registerid' => $registerid]
        );
       
        $recordtimestat =  $DB->get_record('block_timestat', array('log_id' => $registerid));
        
        if (!$recordtimestat){
            $recordbt = new \stdClass();
            $recordbt->log_id = $registerid;
            $recordbt->timespent = $timespent;
            $DB->insert_record('block_timestat', $recordbt);
            return [];
        }

        $recordtimestat->timespent = $timespent;
        $DB->update_record('block_timestat', $recordtimestat);

        return [];
    }

    /**
     * update_register_returns.
     *
     * @return \external_description
     */
    public static function update_register_returns() {
        return new external_single_structure([]);
    }
}