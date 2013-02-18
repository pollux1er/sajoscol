<?php

/**
 *
 *
 * @version $Id: aide_initialisation.php $
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Timetable - Help to initialization";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");
function saveAction($sql) {
	
	$filename = '../responsables/responsable.txt';
	$somecontent = $sql.";\n";

	// Assurons nous que le fichier est accessible en écriture
	if (is_writable($filename)) {

		if (!$handle = fopen($filename, 'a')) {
			 echo "Impossible d'ouvrir le fichier ($filename)";
			 exit;
		}

		// Ecrivons quelque chose dans notre fichier.
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Impossible d'écrire dans le fichier ($filename)";
			exit;
		}

		//echo "L'écriture de ($somecontent) dans le fichier ($filename) a réussi";

		fclose($handle);

	} else {
		echo "Le fichier $filename n'est pas accessible en écriture.";
	}
}

function updateOnline($sql) {
	$hostname = "173.254.25.235";
	$username = "sajoscol_gepi";
	$password = ";?5tvu45l-Lu";
	$databasename = "sajoscol_appli";
	$con = mysql_pconnect("$hostname", "$username", "$password");
	if (!$con) {
		saveAction($sql); //die('Could not connect: ' . mysql_error());
	}
	else { 
		//echo "Connexion reussi!"; 
		if(mysql_select_db($databasename, $con)) { 
			if (mysql_query($sql)) { 
				echo "<script type='text/javascript'>alert('Successly updated online!');</script>"; 
			} else {
				echo mysql_error();
			}
		}
	}
	
}

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// ajout de la ligne suivante dans 'sql/data_gepi.sql' et 'utilitaires/updates/access_rights.inc.php'
// INSERT INTO droits VALUES ('/edt_organisation/aide_initialisation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','aide à l\'initialisation', '');
$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/aide_initialisation.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/edt_organisation/aide_initialisation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','aide à l\'initialisation', '');";
	$res_insert=mysql_query($sql);
	updateOnline($requete);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
if ($_SESSION["statut"] != "administrateur") {
	Die('You must ask to your administrator the authorization of see this page.');
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

//++++++++++ l'entête de Gepi +++++
require_once("../lib/header.inc");
//++++++++++ fin entête +++++++++++
//++++++++++ le menu EdT ++++++++++
require_once("./menu.inc.php");
//++++++++++ fin du menu ++++++++++
?>
<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
    require_once("./menu.inc.new.php");
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        
?>

<h2><strong>Help to initialization</strong></h2>
<p>The module of timetables of GEPI has three vocations : </p>
<p><strong>Objective 1 </strong>: propose the timetables of the classes, rooms, Profs and of each student at the year or over definite periods.</p>
<p><strong>Objective 2 </strong>: propose tools of search for free rooms.</p>
<p><strong>Objective 3 </strong>: allow the teachers to use the module of absences.</p>
<p>Before being able to use all this, you must fill the timetables of the teachers manually or
automatically. To make it, we propose several possible scenari to you .</p>

<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>

<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        
?>
<p><h3><strong>Scenario 1 : Manual initialization of the timetables at the year</strong></h3></p>
<p>It is the simplest and most intuitive scenario to implement. The procedure below is not inevitably to strictly take in the order
suggested but each stage must be carried out carefully.</p>
<p><strong>1. Weeks : </strong>You must define the alternate weeks if there is in your school (typical case Week A - Week B). In the current version, the module of timetable does not manage simultaneously an alternation on more than two weeks. In the menu, click on [Creation] [create/edit the weeks] </p>
<p><strong>2. Timetables : </strong>You must also define the days and the schedules of opening of your school. In the menu, click on [Creation] [Define the schedules of opening] </p>
<p><strong>3. Crenels : </strong>You must define the crenels which cut out the day of course. For the module of timetable, by default all the definite crenels are of one hour . Thereafter, the module of timetable leaves you the possibility for the typing of the courses of dividing each crenel into two pennies crenels 30 minute old. Nothingprevent you from defining a crenel of 1h30 but for the timetables, it will be has one hour basic crenel. It is often the case when one defines the lunch pause which, in the majority of the cases, take 1h30 (12h00 - 13h30). For this precise case, you can define this Repas crenel of 12h00 to 13h30. However, it is necessary to keep in mind that it will be a one hour old crenel which will be appointed. In the menu, click on [Creation] [Define the crenels] </p>
<p><strong>4. IDA : </strong>You must create the courses which do not form part of the courses defined in the classes. This is the case when groups are defined in certain disciplines (example : SVT Gr1, Techno GrA etc...). It is also the case when certain teachers have responsibilities such as the hours for Laboratory (SVT, Histoire/Géo), crenels UNSS and of Coordinator for the EPS, the workshops in general which do not enter the "traditional" courses. For all these cases, it is necessary to define what one calls of the IDA. From the page of Home GEPI, click on [Management of the bases] [Management of IDA] </p>
<p><strong>5. Rooms : </strong>You must define the rooms in which the courses proceed. In the menu, click on [Creation] [Create/Edit the rooms] </p>
<p><strong>6. Timetables Profs : </strong>The filling of the timetables is done from those of the teachers. The module of timetable is then given the responsability to build the timetables of the classes, each student and the rooms. In this scenario, the typing is done manually starting from the timetable of each teacher. You can choose to fill yourself these timetables or to delegate this spot to each teacher. This delegation is done from [Management of Modules] [Timetables]. To create the timetables Profs, click on [ Creation][Manual initialization].</p>
<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>


<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        
?>
<p><h3><strong>Scenario 2 : Manual initialization of the timetables per period</strong></h3></p>
<p>This scenario is a little more complex to set up than the previous
one since it requires the management of periods over the year. The idea here is to create periods (it can be quarters, half-quarters, months, weeks or a mixture of all that) in which we defines the timetables (in fact, one defines the edt during the first period and one duplicates them in the next ones). The advantage of cutting in period is that the timetables can vary
from one period to another. This is very useful so certain courses do not exist at the year
(semi-annual courses). One can also be useful oneself of this cutting in preparation for
special weeks for which the timetables are different (weeks of practice tests, weeks of training courses in 3rd). The preliminary phase is identical to that of the scenario 1 :</p>
<p>1. You carry out the first 5 stages seen previously (Weeks, Timetables, Crenels, IDA, Rooms)</p>
<p><strong>2. Periods : </strong>You will define the periods which will cut out the school year. Avoid the overlapping of the periods which is not a functionality envisaged in the module of timetable. From the menu, click on [Creation] [create/edit the periods].</p>
<p><strong>3. Timetables Profs : </strong>The filling of the timetables is done from those of the teachers. The module of timetable is then given the responsability to build the timetables of the classes, of each student and rooms. In this scenario, the typing is done manually starting from the timetable of each teacher. You can choose to fill to you even these timetables or to delegate this spot to each teacher. This delegation is done from [Management of Modules] [Timetables]. To create the timetables Profs, click on [Creation][Manual initialization]. <strong>To use the system of the periods and thus to be able to modify the timetables in each one of these periods, it is necessary to start by type the timetables of the first period and to duplicate them via the interface of the simple periods by making copy/paste.</strong></p>
<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>

<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        
?>
<p><h3><strong>Scenario 3 : Automatic initialization by importation</strong></h3></p>
<p>This scenario is different from the two previous ones at the time of the filling of the timetables. Instead of making a manual typing, one imports a file containing all the timetables. This file must be formatted in order to be recognized by GEPI. Currently, the module of timetable proposes three formats of possible importation to you : a simple file .csv, a file of export of the software UnDeuxTemps, an export of the type Charlemagne. The difficulty of this importation is big : coincider should be made the courses contained in GEPI with those contained in the imported file. Moreover, it is not rare exam to manually finish this work of importation for some courses. So that this importation occurs as well as possible, following preliminary work is essential.</p>
<p>1. You carry out initially stages 1 and 2 of the previous scenario.</p>
<p><strong>2. Importation of the file : </strong>Click on [ Creation ] [Automatic initialization] and follow the stages which correspond to the type of importation carried out.</strong></p>
<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>