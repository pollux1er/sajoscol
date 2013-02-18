<?php
/*
 * $Id: index.php 7843 2011-08-20 14:48:22Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="../gestion/index.php#init_csv"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a> | <a href='export_tables.php'>Export the current contents of the tables in CSV format </a></p>

<p>You are going to initialize the school year which has just begun.<br />
<?php

	if((getSettingValue('use_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")) {
		echo "<p style='color:red;'><b>CAUTION&nbsp;:</b> You use a LCS or SCRIBE server.<br />
		There is a mode of initialization of the year specific for <a href='../init_lcs/index.php'>LCS</a> on one hand and to SCRIBE in other hand (<i><a href='../init_scribe/index.php'>Scribe</a> et <a href='../init_scribe_ng/index.php'>Scribe_ng</a></i>).<br />
		If you initialize the year with XML mode, you will not be able to use the accounts of your server LCS/SCRIBE thereafter to access to GEPI.<br />Think there twice before continuing.</p>\n";
		echo "<br />\n";
	}

	echo "<p>Have done the various operations of end of the year
and preparation of new year in the page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Change of year</a>&nbsp?</p>\n";

	/*
	$sql="SELECT 1=1 FROM matieres_notes LIMIT 1;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo "Avez-vous pensé à <a href='#' onmouseover=\"afficher_div('archivage','y',20,20);\" onclick=\"return false;\">archiver</a> l'année qui se termine ?</p>\n";
		$texte="<p>L'archivage de l'année en cours vous permettra, une fois passé à l'année suivante, de consulter les bulletins antérieurs de chacun de vos élèves, pour peu qu'ils aient été scolarisés dans votre établissement.</p><p>Cela nécessite l'activation du <a href='../mod_annees_anterieures/admin.php'>module 'Années antérieures'</a>.</p>";
		$tabdiv_infobulle[]=creer_div_infobulle('archivage',"Archivage d'une année","",$texte,"",30,0,'y','y','n','n');
	}
	else {
		echo "</p>\n";
	}

	// CDT
	$sql="SELECT 1=1 FROM ct_entry LIMIT 1;";
	$test1=mysql_query($sql);
	$sql="SELECT 1=1 FROM ct_devoirs_entry LIMIT 1;";
	$test2=mysql_query($sql);
	if((mysql_num_rows($test1)>0)||(mysql_num_rows($test2)>0)) {
		echo "<p>Les cahiers de textes ne sont pas vides.<br />Vous devriez <a href='../cahier_texte_admin/admin_ct.php'>vider les cahiers de textes de l'an dernier</a> avant de procéder à l'initialisation.</p>\n";
	}
	*/
?>
<!--/p-->
<ul>
<li><p>During the procedure, if necessary, certain data of the last year will be definitively erased from GEPI base (students, notes, appreciations, ...) . Only will be preserved the following data, who will be only updated if necessary :<br /><br />
- data relating to the schools,<br />
- data relating to the classes : short headings, long headings, number of periods and names of the periods,<br />
- data relating to the courses : complete identifiers and headings,<br />
- data relating to the users (professors, administrators, ...). Concerning the professors, the courses taught by the professors are preserved,<br />
- Data relating to the various types of AID.<br />&nbsp;</p></li>

<li>
	<?php
	//==================================
	// RNE de l'établissement pour comparer avec le RNE de l'établissement de l'année précédente
	$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
	//==================================
	if($gepiSchoolRne=="") {
		echo "<p><b style='color:red;'>Caution</b>: The RNE of the school is not indicated in 'General management/<a href='../gestion/param_gen.php' target='_blank'>General configuration</a>'<br />That can disturb the importation of the school of origin of the
students.<br />You should correct before continuing.</p>\n";
	}
	?>

	<p>Initialization is carried out in several successive phases, each one requiring a specific file CSV, that you will have to provide to the good format :</p>
    <ul>
    <li><p><a href='eleves.php'>Proceed to the first phase</a> of importation of the students. <b>g_eleves.csv</b> is necessary.
    	<br/>It must contain, in the order the following fields :
    	<br/>Name ; First name ; Date of birth ; n° internal identifier (School) ; n° national identifier ; Code previous school ; Doubling (OUI | NON) ; Regim (INTERN | EXTERN | IN.EX. | DP DAN) ; Sex (F or M)<br />&nbsp;</p></li>

    <li><p><a href='responsables.php'>Proceed to the second phase</a> of importation of the responsibles of the students : the file <b>g_responsables.csv</b> is necessary.
    	<br/> It must contain, in the order, the following fields:
    	<br/>n° identifier student(internal) to the school ; Name of the responsible ; First name of the responsible ; Civility ;  Line 1 Adresse ; Line 2 Adresse ; Postal code ; Commune<br />&nbsp;</p></li>

    <li><p><a href='disciplines.php'>Proceed to the third phase</a> of importation of the courses : the file <b>g_disciplines.csv</b> is necessary.
    	<br/>It must contain, in the order, the following fields:
    	<br/>Short name course; Long name course<br />&nbsp;</p></li>

    <li><p><a href='professeurs.php'>Proceed to the fourth phase</a> of importation of the professors: the file <b>g_professeurs.csv</b> is necessary.
    	<br/>It must contain, in the order, the following fields:
    	<br/>Name; First name; Civility; Adresse e-mail<br />&nbsp;</p></li>

    <li><p><a href='eleves_classes.php'>Proceed to the fifth phase</a> of assignment of the students to the classes  : the file <b>g_eleves_classes.csv</b> necessary.
    	<br/>It must contain, in the order, the following fields:
    	<br/>n° identifier student(internal) to the school ; Short identifier of the class
    	<br/>Notice : this operation automatically created the classes in Gepi, but allots only one short name to them (identifier). You will have to add the long name by the interface of management of the classes.<br />&nbsp;</p></li>


    <li><p><a href='prof_disc_classes.php'>Proceed to the sixth phase</a> of assignment of the courses to each professor and assignment of the professors in each class : the file <b>g_prof_disc_classes.csv</b> is necessary. This importation will define competences of the professors and create the groups of course in each class.
    	<br />It must contain, in the order, the following fields :
    	<br />Login of the professor; Short name of the course ; Identifiers of class (separated by!) ; The type of course (CG (= general course) | OPT (= option))
    	<br />Remarks :
    	<br />If the last field is empty and only one class is present in the third field, the type will be defined like "General". If it is empty and that several classes were defined, then the type will be defined as "option".
    	<br />When the course is general, all the students of the class are automatically associated to this course.
    	<br />When the course is an option, no student is associated to it, association being done at the seventh stage.
    	<br />Caution ! Put several classes for the same course only if it is about only one course! If a professor teaches the same course in two different classes, one then needs two distinct lines in file CSV, with only one class defined for each line.<br />&nbsp;</p></li>

    <li><p><a href='eleves_options.php'>Proceed to the seventh phase</a> of assignment of the students to each group of option : the file <b>g_eleves_options.csv</b> is necessary.
    	<br/>It must contain, in the order, the following fields:
    	<br/>n° identifier student(internal) to the school; Identifiers of the courses followed as option, separated by!
    	<br/>Notice : if several groups with the same course are found in the class of the student, then the student will be associated to all these various groups.<br />&nbsp;</p></li>
    </ul>
	<br />
</li>
<li><p>Once all procedure of initialization of the data finished, it will be possible for you to make all the modifications necessary individually by the way of the management tools included in <b>GEPI</b>.</p></li>
</ul>
<p><br /></p>

<p><b>CAUTION:</b> The <i>n° identifier student(internal) to the school</i> must be made up only of figures.</p>
<p><br /></p>

<?php require("../lib/footer.inc.php");?>
