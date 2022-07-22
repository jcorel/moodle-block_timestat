<?php
/**
 * Simple file test_custom.php to drop into root of Moodle installation.
 * This is an example of using a sql_table class to format data.
 */

global $PAGE, $OUTPUT, $DB, $CFG;

require_once(__DIR__ . '/../../config.php');
require "$CFG->libdir/tablelib.php";

use block_timestat\output\timestat_table;

$id = optional_param('id', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

$urlparams = ['id' => $id];
$PAGE->set_url('/block/timestat/index.php', $urlparams);

$context = context_course::instance($id);
$PAGE->set_context($context);
$course = get_course($id);
require_login($course);

$filters = array(
    'timecreated' => 0,
);

$ufiltering = new \block_timestat\output\filtering($filters, $PAGE->url);
list($extrasql, $params) = $ufiltering->get_sql_filter();

$table = new timestat_table('uniqueid', $extrasql, $params);
$table->is_downloading($download, $course->fullname, $course->fullname);

if (!$table->is_downloading()) {
    $PAGE->set_title(get_string('pluginname', 'block_timestat'));
    $PAGE->set_heading(get_string('pluginname', 'block_timestat'));
    echo $OUTPUT->header();

   $ufiltering = new \block_timestat\output\filtering($filters, $PAGE->url);
    $ufiltering->display_add();
    $ufiltering->display_active();
}

$table->define_baseurl(new moodle_url('/block/timestat/timestatreport.php', $urlparams));
$table->out(20, false);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}