 /*
 **************************************************************************
 * NOTICE OF COPYRIGHT
 *
 * Copyright (C)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *          http://www.gnu.org/copyleft/gpl.html
 *
 *
 *
 **************************************************************************
 */

Timestat block for Moodle

This application was developed in cooperation by team composed of:
Barbara Dębska
Łukasz Musiał
Łukasz Sanokowski

Upgrade from 1.9 to 2.5 version was made thanks to contribution of:
Classroom Revolution
Lib Ertea
Mart van der Niet
Joseph Thibault

The application measure time of real activity done by Moodle users. Measured
activity time is incremented only when Moodle tab in web browser is active (it is done via Javascript).


INSTALLATION GUIDE:

1) Install block in standard way (copy it to '/moodle/blocks' folder and
click 'Notifications' in admin panel)

2) Add following line of code at end of function standard_end_of_body_html() in file /lib/outputrenderers.php:

    require_once($CFG->dirroot.'/blocks/timestat/lib/timestatlib.php');

so it should look like:

    public function standard_end_of_body_html() {
        global $CFG;

        (...)

        require_once($CFG->dirroot.'/blocks/timestat/lib/timestatlib.php');
        return $output;
    }

    
IMPORTANT FOR USERS OF PREVIOUS VERSIONS (less than 2014090400):
Previous versions of this app used column named 'timespent' in 'log' table. Now time is stored in table 'block_timestat'.
It is recommended to uninstall previous version first and install this new version next.

BACKUP YOUR DATABASE BEFORE EXECUTING FOLLOWING QUERIES:

Old data can be copied to new table with query:
INSERT INTO mdl_block_timestat (log_id, timespent) SELECT id, timespent FROM mdl_log;

After this the field 'timestpent' can be removed from table 'mdl_log':
ALTER TABLE mdl_log DROP timespent;


Contact:

mostly prefered by forum discussion:
http://moodle.org/mod/forum/discuss.php?d=167732