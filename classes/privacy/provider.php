<?php

namespace block_timestat\privacy;

use coding_exception;
use context;
use context_block;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\{approved_contextlist, approved_userlist, contextlist, core_userlist_provider, transform, userlist, writer};
use dml_exception;

class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    core_userlist_provider {


    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'block_timestat',
            [
                'log_id' => 'privacy:metadata:block_timestat:log_id',
                'timespent' => 'privacy:metadata:block_timestat:timespent',
            ],
            'privacy:metadata:block_timestat'
        );
        return $collection;
    }

    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if (!$context instanceof context_block) {
            return;
        }

        $sql = "SELECT lsl.userid
                FROM {block_timestat} bt
                JOIN {logstore_standard_log} lsl ON bt.log_id = lsl.id
                WHERE lsl.contextinstanceid = :contextid";
        $params = ['contextid' => $context->id];
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $userids = $userlist->get_userids();

        [$insql, $inparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $sql = "SELECT bt.id
                FROM {block_timestat} bt
                JOIN {logstore_standard_log} lsl ON bt.log_id = lsl.id
                WHERE lsl.userid $insql";
        $records = $DB->get_records_sql($sql, $inparams);

        foreach ($records as $record) {
            $DB->delete_records('block_timestat', ['id' => $record->id]);
        }
    }

    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT lsl.contextinstanceid
                FROM {block_timestat} bt
                JOIN {logstore_standard_log} lsl ON bt.log_id = lsl.id
                WHERE lsl.userid = :userid";
        $params = ['userid' => $userid];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * @throws dml_exception|coding_exception
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $data = [];
        $userid = (int)$contextlist->get_user()->id;

        $sql = "SELECT lsl.id, lsl.courseid, bt.timespent, lsl.timecreated AS timestart, lsl.contextinstanceid
            FROM {block_timestat} bt
            JOIN {logstore_standard_log} lsl ON bt.log_id = lsl.id
            WHERE lsl.userid = :userid";

        $results = $DB->get_records_sql($sql, ['userid' => $userid]);

        foreach ($results as $result) {
            $data[$result->contextinstanceid][] =
                (object)[
                    'log_id' => $result->log_id,
                    'timespent' => $result->timespent,
                    'timestart' => transform::datetime($result->timestart),
                ];
        }

        if (!empty($data)) {
            foreach ($contextlist as $context) {
                // filter data for this contextinstanceid, get data for this context $data[$context->instanceid]
                $contextdata = (object)['block_timestat' => $data[$context->instanceid]];
                writer::with_context($context)->export_data(
                    [get_string('privacy:metadata:block_timestat', 'block_timestat')],
                    $contextdata
                );
            }
        }
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context $context Context to delete data from.
     * @throws dml_exception
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;
        if ($context instanceof \context_course) {
            $sql = "SELECT bt.id
                    FROM {block_timestat} bt
                    JOIN {logstore_standard_log} lsl ON bt.log_id = lsl.id
                    WHERE lsl.contextinstanceid = :contextid";
            $params = ['contextid' => $context->id];
            $records = $DB->get_records_sql($sql, $params);

            foreach ($records as $record) {
                $DB->delete_records('block_timestat', ['id' => $record->id]);
            }
        }
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $contextids = $contextlist->get_contextids();
        [$insql, $inparams] = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
        $user = $contextlist->get_user();

        $sql = "SELECT bt.id
                FROM {block_timestat} bt
                JOIN {logstore_standard_log} lsl ON bt.log_id = lsl.id
                WHERE lsl.userid = :userid AND lsl.contextinstanceid $insql";
        $params = ['userid' => $user->id] + $inparams;
        $records = $DB->get_records_sql($sql, $params);

        foreach ($records as $record) {
            $DB->delete_records('block_timestat', ['id' => $record->id]);
        }
        return count($records);
    }
}
