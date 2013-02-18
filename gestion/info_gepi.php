<?php

/*
 * $Id: info_gepi.php 4446 2010-05-15 19:39:28Z delineau $
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisations files
require_once("../lib/initialisations.inc.php");
// Resume session

$resultat_session = $session_gepi->security_check();

if ($resultat_session == '0') {

   header("Location: ../logout.php?auto=1");

   die();

};

//**************** EN-TETE *****************

require_once("../lib/header.inc");

//**************** FIN EN-TETE *************



?>

<h1 class='gepi'>GEPI - General information</h1>

<?php

echo "You are currently connected on the application <b>GEPI (".getSettingValue("gepiSchoolName").")</b>.

<br />By safety, if you do not send any information to the server (activation of a link or sending of a form) during more <b>".getSettingValue("sessionMaxLength")." minutes</b>, you will be automatically disconnected from the application.";

echo "<h2>Administration of application GEPI</h2>\n";

echo "<table cellpadding='5' summary='Infos'>\n";

echo "<tr><td>Name and first name of the administrator : </td><td><b>".getSettingValue("gepiAdminNom")." ".getSettingValue("gepiAdminPrenom")."</b></td></tr>\n";

echo "<tr><td>Function of the administrator : </td><td><b>".getSettingValue("gepiAdminFonction")."</b></td></tr>\n";

echo "<tr><td>Email of the administrator : </td><td><b><a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">".getSettingValue("gepiAdminAdress")."</a></b></td></tr>\n";

echo "<tr><td> Name of the school : </td><td><b>".getSettingValue("gepiSchoolName")."</b></td></tr>\n";

echo "<tr><td Valign='top'>Adress : </td><td><b>".getSettingValue("gepiSchoolAdress1")."<br />".getSettingValue("gepiSchoolAdress2")."<br />".getSettingValue("gepiSchoolZipCode").", ".getSettingValue("gepiSchoolCity")."</b></td></tr>\n";

echo "</table>\n";



echo "<h2>Objectives of application GEPI</h2>\n";

echo "The objective of GEPI is <b>teaching management of the students and their schooling</b>.

To this end, data are collected and stored in a single base of MySql type.";



echo "<h2>Obligations of the user</h2>\n";

echo "The members of the teaching staff are held to fill the rubrics which were affected to them by the administrator

during the parameter setting of the application.";

echo "<br />It is possible to modify the contents of a rubric as long as the period concerned was not closed by the administrator.";



echo "<h2>Recipients of the data relating to the school bulletin </h2>\n";

echo "Concerning the report card, the following data are collected near the members of the teaching staff :

<ul><li>absences (for each period : a many half-days of absence, number not justified absences, number of delays, observations)</li>

<li>averages and appreciations by course,</li>

<li>averages and appreciations by interdisciplinary project,</li>

<li>opinion of the staff meeting.</li>

</ul>

All this information is completely reproduced on a bulletin at the end of each period (see below).

<br /><br />

These data are used for:

<ul>

<li>development of one bulletin at the end of each period, edited by the schooling service and communicated to the student

and to its legal responsibles : notes obtained, absences, averages, appreciations of the teachers, opinion of the staff meeting.</li>

<li>elaboration of a working paper taking again information of the official bulletin and available for the members of the teaching staff of the class concerned</li>

</ul>\n";





//On vérifie si le module cahiers de texte est activé

if (getSettingValue("active_cahiers_texte")=='y') {

    echo "<h2>Recipients of the data relating to the textbook</h2>\n";

    echo "In accordance with the directives of National Education, each professor has in GEPI a textbook for each of his classes which it can hold up to date

    while being connected.

    <br />

    The textbook reports the work completed in class:

    <ul>

    <li>project of the teaching staff,</li>

    <li>teaching contents of each meeting, chronology, objective, work to be made ...</li>

    <li>various documents,</li>

    <li>evaluations, ...</li>

    </ul>

    It constitutes a tool of communication for the student, the disciplinary teams

    and multi-field, administration, the head of school, inspection and families.

    <br /> The textbooks are accessible on line.";

    if ((getSettingValue("cahiers_texte_login_pub") != '') and (getSettingValue("cahiers_texte_passwd_pub") != '')) {

       echo " <b>Because of the personal character of the contents, the access to the interface of public consultation is restricted</b>. To access to the textbooks, it is necessary to ask to the administrator,

       the name of an user and the valid password.";

    } else {

       echo " <b>The access to the interface of public consultation is entirely free and is subjected to no restriction.</b>\n";

    }



}

//On vérifie si le module carnet de notes est activé

if (getSettingValue("active_carnets_notes")=='y') {

    echo "<h2>Recipients of the data relating to the report card</h2>\n";

    echo "Each professor has in GEPI a report card for each one of his classes, that it can hold up to date

    while being connected.

    <br />

    The report card allows the typing of the notes and/or the comments of any type of evaluation (formative, sommatives, oral examination,TP, TD,...).

    <br /><b>The professor commits himself making appear in the report card only
notes and comments made available of the student (note and comment related to the copy, ...).</b>

    These data stored in GEPI do not have an other recipient different from the professor himself and the principal professors of the class.

    <br />The notes can be used for the calculation of an average which will appear in the official bulletin at the end of each period.";

}

//On vérifie si le plugin suivi_eleves est activé
$test_plugin = sql_query1("select ouvert from plugins where nom='suivi_eleves'");
if ($test_plugin=='y') {
    echo "<h2>Recipients of the data relating to the module of follow-up of the students</h2>\n";

    echo "Each professor has in GEPI of a tool of follow-up the students (\"observatory\") for each one of its classes, which it can hold up to date

    while being connected.

    <br />

    In the observatory, the professor has the possibility of allotting to each one of his students a code for each period.

    These codes and their significance are set by the administrators of the observatory appointed by the general administrator of GEPI.

    <br />.

    The professor also has the possibility of type a comment for each one of his students

    in the respect of the law and the strict framework of National Education.

    <br /><br />The observatory and the data which appear in it are accessible to the unit from the teaching staff from the school.

    <br /><br />In the respect of the data-processing law and freedom 78-17 of January 6, 1978, each student also has access in its space GEPI to the data which relate to it";

}
require("../lib/footer.inc.php");
?>