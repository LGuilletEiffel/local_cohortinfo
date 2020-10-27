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
 * File : viewinfo.php
 * View cohorts from a context and get a link for more informations
 */

require_once("../../config.php");

global $PAGE, $DB;

require_login();

$contextid = required_param('contextid', PARAM_INT);
$origin = required_param('origin', PARAM_TEXT);

$context = context::instance_by_id($contextid);

$PAGE->set_context($context);

$pageurl = new moodle_url('/local/cohortinfo/viewinfo.php',
        array('contextid' => $contextid, 'origin' => $origin));

if ($origin == 'course') {

    $courseid = $DB->get_record('context', array('id' => $contextid))->instanceid;
    $courseinstance = $DB->get_record('course', array('id' => $courseid));

    $title = get_string('viewinfo', 'local_cohortinfo') . " : " . $courseinstance->fullname;
} else {

    $title = get_string('viewinfo', 'local_cohortinfo');
}

$PAGE->set_title($title);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($title);

if ($origin == 'course') {

    $courseid = $DB->get_record('context', array('id' => $contextid))->instanceid;

    $course = get_course($courseid);

    require_login($course);
} else {

    require_login();
}

$instanceid = $DB->get_record('context', array('id' => $contextid))->instanceid;

echo $OUTPUT->header();

if (($origin == 'course' && has_capability('local/cohortinfo:viewinfocourse', $context)) ||
        ($origin == 'course_cat' && has_capability('local/cohortinfo:viewinfocategory', $context))) {

    if ($origin == 'course' || $origin == 'course_cat') {

        $listcohorts = array();

        $listparentcontexts = explode('/', $context->path);

        foreach ($listparentcontexts as $parentcontext) {

            if ($parentcontext != "") {

                $listcohortsparents = $DB->get_records('cohort', array('contextid' => $parentcontext));

                foreach ($listcohortsparents as $cohortparent) {

                    $listcohorts[] = $cohortparent;
                }
            }
        }
    } else {

        echo "Ce message ne doit pas s\'afficher";
        $listcohorts = null;
    }

    if ($origin == 'course') {

        $table = new html_table();
        $table->head = array(get_string('category'), get_string('name', 'local_cohortinfo'),
            get_string('idnumber', 'local_cohortinfo'), get_string('description', 'local_cohortinfo'),
            get_string('memberscount', 'local_cohortinfo'), get_string('linkcohort', 'local_cohortinfo'),
            get_string('moreinfo', 'local_cohortinfo'));
        $table->colclasses = array('leftalign category', 'leftalign name', 'leftalign id', 'leftalign description',
            'leftalign size', 'leftalign linkbutton', 'leftalign moreinfo');
        $table->id = 'cohorts';
        $table->attributes['class'] = 'admintable generaltable';
    } else {

        $table = new html_table();
        $table->head = array(get_string('category'), get_string('name', 'local_cohortinfo'),
            get_string('idnumber', 'local_cohortinfo'), get_string('description', 'local_cohortinfo'),
            get_string('memberscount', 'local_cohortinfo'), get_string('moreinfo', 'local_cohortinfo'));
        $table->colclasses = array('leftalign category', 'leftalign name', 'leftalign id', 'leftalign description',
            'leftalign size', 'leftalign moreinfo');
        $table->id = 'cohorts';
        $table->attributes['class'] = 'admintable generaltable';
    }

    foreach ($listcohorts as $cohort) {

        if ($cohort->visible || is_siteadmin()) {

            $line = array();
            $cohortcontext = context::instance_by_id($cohort->contextid);
            if ($cohortcontext->contextlevel == CONTEXT_COURSECAT) {
                $line[] = $cohortcontext->get_context_name(false);
            } else {
                $line[] = $cohortcontext->get_context_name(false);
            }

            $line[] = "<strong>" . format_string($cohort->name) . "</strong>";
            $line[] = s($cohort->idnumber); // All idnumbers are plain text.
            $line[] = format_text($cohort->description, $cohort->descriptionformat);

            $line[] = $DB->count_records('cohort_members', array('cohortid' => $cohort->id));

            if ($origin == 'course') {

                $cohortid = $cohort->id;

                if ($DB->record_exists('enrol',
                        array('enrol' => 'cohort', 'customint1' => $cohortid,
                                    'courseid' => $courseid))) {

                    $line[] = get_string('linkedcohort', 'local_cohortinfo');
                } else {

                    // Ici, insérer un bouton qui redirige vers linkcohort avec les bons paramètres.

                    $textlink = get_string('linkcohort', 'local_cohortinfo');

                    $line[] = "<input type=button class='btn btn-secondary' "
                            . "onClick=location.href='../cohortinfo/linkcohort.php?"
                            . "cohortid=$cohort->id&contextid=$contextid'"
                            . " value=$textlink>";
                }
            }

            $urlinfocohort = new moodle_url('/local/cohortinfo/infocohort.php',
                    array('cohortid' => $cohort->id, 'origin' => $origin, 'contextid' => $contextid));
            $line[] = "<a href=$urlinfocohort>" .
                    get_string('moreinfo', 'local_cohortinfo') . "<br></a>";

            $data[] = $row = new html_table_row($line);
            if (!$cohort->visible) {
                $row->attributes['class'] = 'dimmed_text';
            }
        }
    }

    $table->data  = $data;
    echo html_writer::table($table);

} else {

    $home = new moodle_url('/', array());
    echo "<a href=$home>" . get_string('returnhome', 'local_cohortinfo') . "</a>";
}

echo $OUTPUT->footer();