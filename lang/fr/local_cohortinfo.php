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
 * File : fr/local_cohortinfo.php
 * French language file
 */

$string['pluginname'] = 'Informations sur les cohortes';
$string['viewinfo'] = 'Informations sur les cohortes';
$string['returnhome'] = 'Retour à l\'accueil';
$string['moreinfo'] = 'Plus d\'infos';
$string['infocohort'] = 'Information sur la cohorte {$a}';
$string['cohortusers'] = 'Membres de la cohorte {$a}';
$string['coursewithcohort'] = 'Liste des cours utilisant la cohorte {$a}';
$string['cohortinfo:viewinfocourse'] = 'Voir les informations sur les cohortes du cours';
$string['cohortinfo:viewinfocategory'] = 'Voir les informations sur les cohortes de la catégorie';
$string['name'] = 'Nom';
$string['idnumber'] = 'Identifiant cohorte';
$string['studentidnumber'] = 'Numéro étudiant';
$string['description'] = 'Description';
$string['memberscount'] = 'Effectif de la cohorte';
$string['firstname'] = 'Prénom';
$string['lastname'] = 'Nom';
$string['course'] = 'Cours';
$string['linkcohort'] = 'Lier à ce cours';
$string['linkedcohort'] = 'Cohorte déjà liée';
$string['confirmcohort'] = 'Confirmer le choix';
$string['fullconfirmcohort'] = 'Voulez vous vraiment lier la cohorte {$a->cohortname} de {$a->nbstudents} étudiants dans le cours {$a->coursename} ?<br>';
$string['yes'] = 'Oui';
$string['no'] = 'Non';
$string['privacy:metadata'] = 'Le plugin d\'informations sur les cohortes ne stocke aucune données personnelle en dehors '
        . 'des systèmes standards de Moodle.';
