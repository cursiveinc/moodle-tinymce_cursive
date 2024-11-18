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
 * Tiny cursive plugin upgrade script.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Run all ClamAV plugin upgrade steps between the current DB version and the current version on disk.
 *
 * @param int $oldversion The old version of atto in the DB.
 * @return bool
 */
function xmldb_tiny_cursive_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2023041937) {
        $table = new xmldb_table('tiny_cursive_files');
        $field = new xmldb_field('questionid', XMLDB_TYPE_INTEGER, '20', null, null, null, '0', 'uploaded');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2023041937, 'tiny', 'cursive');
    }

    if ($oldversion < 2024060227) {
        $table = new xmldb_table('tiny_cursive_writing_diff');
        // Check if the table exists.
        if ($dbman->table_exists($table)) {
            // Drop the existing table.
            $dbman->drop_table($table);
        }

        // Define table fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('file_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('reconstructed_text', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL, null, null);
        $table->add_field('submitted_text', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL, null, null);
        $table->add_field('meta', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        // Define table keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Create the new table.
        $dbman->create_table($table);

        // Save upgrade path.
        upgrade_plugin_savepoint(true, 2024060227, 'tiny', 'cursive');

    }

    if ($oldversion < 2024060228) {
        $table = new xmldb_table('tiny_cursive_files');
        $field = new xmldb_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null, 'filename');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2024060228, 'tiny', 'cursive');
    }

    if ($oldversion < 2024060281) {
        // Define table tiny_cursive_quality_metrics.
        $table = new xmldb_table('tiny_cursive_quality_metrics');

        // Add fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, 'Primary key');
        $table->add_field('file_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'Foreign key reference to tiny_cursive_files.id');
        $table->add_field('total_active_time', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, 'Total active time spent writing in seconds');
        $table->add_field('edits', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'Number of edits made to the text');
        $table->add_field('verbosity', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, 'Measure of text verbosity');
        $table->add_field('word_count', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'Total number of words in the text');
        $table->add_field('sentence_count', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'Total number of sentences in the text');
        $table->add_field('q_count', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'Number of questions in the text');
        $table->add_field('word_len_mean', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'Average word length');
        $table->add_field('sent_word_count_mean', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'Average number of words per sentence');
        $table->add_field('p_burst_mean', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'Mean production burst length');
        $table->add_field('p_burst_cnt', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'Count of production bursts');

        // Add keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('file_id_fk', XMLDB_KEY_FOREIGN, ['file_id'], 'tiny_cursive_files', ['id']);

        // Create table if it doesn't exist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Upgrade savepoint reached.
        upgrade_plugin_savepoint(true, 2024060281, 'tiny', 'cursive');
    }

    return true;
}

