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
 * File : lib.php
 * Library file
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot .'/cohort/lib.php');

function local_cohortinfo_extend_settings_navigation(settings_navigation $nav, context $context) {

    global $DB, $COURSE;

    if ($DB->record_exists('context', array('id' => $context->id, 'contextlevel' => 40))) {

        if (has_capability('local/cohortinfo:viewinfocategory', $context) &&
                $DB->record_exists('cohort', array('contextid' => $context->id))) {

            $branch = $nav->get('categorysettings');

            if (isset($branch)) {

                $params = array('contextid' => $context->id, 'origin' => 'course_cat');
                $manageurl = new moodle_url('/local/cohortinfo/viewinfo.php', $params);
                $managetext = get_string('viewinfo', 'local_cohortinfo');

                $icon = new pix_icon('cohort', $managetext, 'local_cohortinfo');
                $branch->add($managetext, $manageurl, $nav::TYPE_CONTAINER, null, null, $icon);
            }
        }
    }

    if ($DB->record_exists('context', array('id' => $context->id, 'contextlevel' => 50))) {

        if (count(cohort_get_available_cohorts($context)) > 0) {

            $canusecohorts = true;
        } else {

            $canusecohorts = false;
        }

        if (has_capability('local/cohortinfo:viewinfocourse', $context) && $canusecohorts && $COURSE->id != 1) {

            $branch = $nav->get('courseadmin');

            if (isset($branch)) {

                $params = array('contextid' => $context->id, 'origin' => 'course');
                $manageurl = new moodle_url('/local/cohortinfo/viewinfo.php', $params);
                $managetext = get_string('viewinfo', 'local_cohortinfo');

                $icon = new pix_icon('cohort', $managetext, 'local_cohortinfo');
                $branch->add($managetext, $manageurl, $nav::TYPE_CONTAINER, null, null, $icon);
            }
        }
    }
}
