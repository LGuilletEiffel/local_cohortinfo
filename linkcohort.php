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
 * Initially developped for :
 * Université Gustave Eiffel
 * 5 Boulevard Descartes
 * 77420 Champs-sur-Marne
 * FRANCE
 *
 * Add ways to get info on cohorts for the teachers.
 *
 * @package   local_cohortinfo
 * @copyright 2020 Laurent Guillet <laurent.guillet@univ-eiffel.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * File : linkcohort.php
 * Page that ask for confirmation and link cohort to the course then redirect
 */

require_once("../../config.php");
require_once("$CFG->dirroot/enrol/cohort/locallib.php");
require_once("$CFG->dirroot/group/lib.php");

require_login();

global $DB;

$contextid = required_param('contextid', PARAM_INT);
$cohortid = required_param('cohortid', PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

$context = context::instance_by_id($contextid);
$PAGE->set_context($context);

$redirecturl = new moodle_url('/local/cohortinfo/viewinfo.php',
        array('contextid' => $contextid, 'origin' => 'course'));

$cohort = $DB->get_record('cohort', array('id' => $cohortid));

if (has_capability('enrol/cohort:config', $context)) {

    if ($cohort->visible || is_siteadmin()) {

        if ($confirm == true) {

            $course = $DB->get_record('course', array('id' => $context->instanceid));

            $datagroup = new stdClass();
            $datagroup->name = $cohort->name;
            $datagroup->idnumber = $cohort->idnumber;
            $datagroup->courseid = $course->id;

            if (!$DB->record_exists('groups', array('courseid' => $course->id, 'idnumber' => $cohort->idnumber))) {

                $groupid = groups_create_group($datagroup);
            } else {

                $groupid = $DB->get_record('groups', array('courseid' => $course->id, 'idnumber' => $cohort->idnumber))->id;
                $datagroup->id = $groupid;
                groups_update_group($datagroup);
            }

            $studentroleid = $DB->get_record('role', array('shortname' => 'student'))->id;

            $cohortplugin = enrol_get_plugin('cohort');
            $cohortplugin->add_instance($course, array('customint1' => $cohortid, 'roleid' => $studentroleid,
                'customint2' => $groupid));

            $trace = new null_progress_trace();
            enrol_cohort_sync($trace, $course->id);
            $trace->finished();

            redirect($redirecturl);
        } else {

            $cohort = $DB->get_record('cohort', array('id' => $cohortid));
            $courseid = $DB->get_record('context', array('id' => $contextid))->instanceid;
            $course = $DB->get_record('course', array('id' => $courseid));

            $pageurl = new moodle_url('/local/cohortinfo/linkcohort.php',
                    array('cohortid' => $cohort->id, 'contextid' => $contextid));
            $title = get_string('confirmcohort', 'local_cohortinfo', $cohort->name);
            $PAGE->set_title($title);
            $PAGE->set_url($pageurl);
            $PAGE->set_pagelayout('standard');
            $PAGE->set_heading($title);

            $PAGE->navbar->add(get_string('viewinfo', 'local_cohortinfo'),
                    new moodle_url('/local/cohortinfo/viewinfo.php',
                            array('contextid' => $contextid, 'origin' => 'course')));
            $PAGE->navbar->add(get_string('confirmcohort', 'local_cohortinfo'),
                    new moodle_url('/local/cohortinfo/confirmcohort.php', array('cohortid' => $cohort->id, 'contextid' => $contextid)));

            echo $OUTPUT->header();

            $stringdata = new stdClass();
            $stringdata->cohortname = $cohort->name;
            $stringdata->nbstudents = $DB->count_records('cohort_members', array('cohortid' => $cohort->id));
            $stringdata->coursename = $course->fullname;

            echo get_string('fullconfirmcohort', 'local_cohortinfo', $stringdata);
            echo "<input type=button class='btn btn-primary' "
            . "onClick=location.href='../cohortinfo/linkcohort.php?cohortid=$cohort->id&contextid=$contextid&confirm=true' "
            . "value=" . get_string('yes', 'local_cohortinfo') . ">    ";
            echo "<input type=button class='btn btn-cancel' "
            . "onClick=location.href='../cohortinfo/viewinfo.php?contextid=$contextid&origin=course' "
            . "value=" . get_string('no', 'local_cohortinfo') . ">";

            echo $OUTPUT->footer();
        }
    }
}

