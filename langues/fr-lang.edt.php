<?php
/**
 * language file
 *
 * @version $Id: fr-lang.edt.php  $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// fichier appelé par edt_organisation/choix_langue.php
// langue = fr - iso latin-1
// Si GEPI UTF-8, utiliser utf8_encode dans chaque define

// --------------------------------------
// edt_organisation/...
// --------------------------------------

define('ASK_AUTHORIZATION_TO_ADMIN', "Vous devez demander à votre administrateur l'autorisation de voir cette page.");

// --------------------------------------
// edt_organisation/ajax_edtcouleurs.php
// --------------------------------------

define('NO_COLOR', "no color");
define('IMPOSSIBLE_TO_UPDATE', "Impossible de mettre à jour la table edt_setting");

// --------------------------------------
// edt_organisation/ajouter_salle.php
// --------------------------------------

define('TITLE_ADD_CLASSROOM', "Timetable");
define('MANAGE_GEPI_CLASSROOMS', "Manage classrooms");
define('ADD_CLASSROOM_IN_DB', "Add a classroom");
define('CHANGE_CLASSROOM_NAME', "La salle numéro %s s'appelle désormais %s");
//... à finir

// --------------------------------------
// edt_organisation/voir_edt.php
// --------------------------------------

define('TITLE_VOIR_EDT', "The timetable of :");

// --------------------------------------
// edt_organisation/edt_param_couleurs.php
// --------------------------------------

define('TITLE_EDT_PARAM_COLORS', "Setting colors of subjects");
define('CLICK_ON_COLOR', "Click on the color to update.");
define('TEXT1_EDT_PARAM_COLORS', "Pour voir ces couleurs dans les emplois du temps, il faut modifier les paramètres.");
define('FIELD', "Subject");
define('SHORT_NAME', "Short name");
define('COLOR', "Color");
define('MODIFY_COLOR', "Edit");

// --------------------------------------
// edt_organisation/edt_parametrer.php
// --------------------------------------

define('TITLE_EDT_PARAMETRER', "Timetable - Settings");
define('FIELDS_PARAM', "Subjects");
define('FIELDS_PARAM_BUTTON1', "Short name (du type HG,...).");
define('FIELDS_PARAM_BUTTON2', " Full name (History Geography,...).");
//.... à finir

// --------------------------------------
// edt_organisation/voir_edt_eleves.php
// --------------------------------------
define('LOOKFOR_STUDENTS_BY_NAME', "Search names beginning with :");
define('NEXT_LETTER', "la lettre suivante");
define('LOOKFOR_STUDENTS_BY_CLASS', " or the classlist of ");
define('THIS_CLASS', "This class");
define('PREVIOUS_STUDENT', "previous student");
define('NEXT_STUDENT', "following student");
define('CHOOSE_STUDENT', "Choice of student");

// --------------------------------------
// edt_organisation/voir_edt_prof.php
// --------------------------------------
define('PREVIOUS_TEACHER', "Previous teacher");
define('NEXT_TEACHER', "Next teacher");
define('CHOOSE_TEACHER', "Choice of teacher");

// --------------------------------------
// edt_organisation/voir_edt_classe.php
// --------------------------------------
define('PREVIOUS_CLASS', "Previous class");
define('NEXT_CLASS', "Next class");
define('CHOOSE_CLASS', "Choice of the class");

// --------------------------------------
// edt_organisation/voir_edt_salle.php
// --------------------------------------
define('PREVIOUS_CLASSROOM', "Previous room");
define('NEXT_CLASSROOM', "Next room");
define('CHOOSE_CLASSROOM', "Choice of room");

// --------------------------------------
// edt_organisation/menu.inc.php
// --------------------------------------
define('WEEK_NUMBER', "Week n° ");
define('VIEWS', "View");
define('TEACHERS', "Teachers");
define('CLASSES', "Classes");
define('CLASSROOMS', "Rooms");
define('STUDENTS', "Students");
define('MODIFY', "Edit");
define('LOOKFOR', "Search");
define('FREE_CLASSROOMS', "Salles libres");
define('ADMINISTRATOR', "Admin");
define('LESSONS', "Lessons");
define('GROUPS', "Groups");
define('INITIALIZE', "Initialize");
define('PARAMETER', "Set");
define('COLORS', "Colors");
define('CALENDAR', "calendar");
define('PERIODS', "Periods");
define('WEEKS', "Weeks");


// --------------------------------------
// edt_organisation/effacer_cours.php
// --------------------------------------
define('TITLE_DELETE_LESSON', "Delete a course of the timetable");
define('CANT_DELETE_OTHER_COURSE', "You can not delete the course of a collegue");
define('DELETE_CONFIRM', "Are you sure you want to delete this course ?");
define('DELETE_FAILURE', "Failure deleting");
define('DELETE_SUCCESS', "Deleted succesfully");
define('DELETE_NOTHING', "You are trying to delete a course that does not exist");
define('DELETE_BAD_RIGHTS', "You do not have enough rights to make this operation");
define('CONFIRM_BUTTON', "Confirm");
define('ABORT_BUTTON', "Cancel");

// --------------------------------------
// edt_organisation/fonctions_cours.php
// --------------------------------------
define('INCOMPATIBLE_LESSON_LENGTH', "la durée du cours n'est pas compatible avec les horaires de l'établissement.");
define('LESSON_OVERLAPPING', "Ce cours en chevauche un autre ");
define('CLASSROOM_NOT_FREE', "La salle demandée est déjà occupée par ");
define('STUDENTS_NOT_FREE', "Warning : Some students are already attending to the same subject with ");
define('SOME_STUDENTS_NOT_FREE', "Cours créé bien que certains élèves soient déjà en cours avec ");
define('GROUP_IS_EMPTY', "Veuillez choisir un enseignement pour créer le créneau");

// --------------------------------------
// edt_organisation/modifier_cours_popup.php
// --------------------------------------
define('TITLE_MODIFY_LESSON_POPUP', "Update a course of the timetable");
define('TITLE_PAGE', "Sajoscol - Update a course");
define('LESSON_MODIFICATION', "Updating the course");
define('CHOOSE_LESSON', "Choice of the course");
define('LESSON_START_AT_THE_BEGINNING', "Le cours commence au début d'un créneau");
define('LESSON_START_AT_THE_MIDDLE', "Le cours commence au milieu d'un créneau");
define('HOUR1', "1/2 heure");
define('HOUR2', "1 heure");
define('HOUR3', "1,5 heure");
define('HOUR4', "2 heures");
define('HOUR5', "2,5 heures");
define('HOUR6', "3 heures");
define('HOUR7', "3,5 heures");
define('HOUR8', "4 heures");
define('HOUR9', "4,5 heures");
define('HOUR10', "5 heures");
define('HOUR11', "5,5 heures");
define('HOUR12', "6 heures");
define('HOUR13', "6,5 heures");
define('HOUR14', "7 heures");
define('HOUR15', "7,5 heures");
define('HOUR16', "8 heures");
define('ALL_WEEKS', "Every week");
define('ENTIRE_YEAR', "Whole year");
define('REGISTER', "Save");
define('HOURS', "Hourly");
define('CLASSROOM', "Room");
?>
