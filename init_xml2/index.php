<?php

/*
 * $Id: index.php 7842 2011-08-20 14:45:42Z crob $
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
<p class='bold'><a href="../gestion/index.php#init_xml2"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>
<?php if (getSettingValue('use_ent') == 'y') {
	echo '<p>Before starting, you must recover the logins of your users in the ENT : <a href="../mod_ent/index.php">RECOVER</a></p>';
}
?>

<p>You will carry out the initialization of the school year which has just begun.</p>
<?php

	//if((getSettingValue('use_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")) {
	if((getSettingValue('use_sso')=="lcs")||(getSettingValue('auth_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")||(getSettingValue('auth_sso')=="ldap_scribe")) {
		echo "<p style='color:red;'><b>CAUTION&nbsp;:</b> You use a server LCS or SCRIBE.<br />
		There is a mode of initialization of the year suitable for <a href='../init_lcs/index.php'>LCS</a> on the one hand and to SCRIBE in addition (<i><a href='../init_scribe/index.php'>Scribe</a> and <a href='../init_scribe_ng/index.php'>Scribe_ng</a></i>).<br />
		If you initialize the year with mode XML, you will not be able to use the accounts of your server LCS/SCRIBE thereafter to access to GEPI.<br />Think there of twice before continuing.</p>\n";
		echo "</p>\n";
	}

	echo "<p>You thought of carrying out the various operations of end of the year and preparation of new year in the page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Change of year</a>&nbsp?</p>\n";

	/*
	$sql="SELECT 1=1 FROM matieres_notes LIMIT 1;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo "<p>Avez-vous pensé à <a href='#' onmouseover=\"afficher_div('archivage','y',20,20);\" onclick=\"return false;\">archiver</a> l'année qui se termine ?</p>\n";
		$texte="<p>L'archivage de l'année en cours vous permettra, une fois passé à l'année suivante, de consulter les bulletins antérieurs de chacun de vos élèves, pour peu qu'ils aient été scolarisés dans votre établissement.</p>";
		if (getSettingValue("active_annees_anterieures")=='y') {
			$texte.="<p>Procéder à l'<a href='../mod_annees_anterieures/conservation_annee_anterieure.php'>archivage de l'année</a>.</p>";
		}
		else {
			$texte.="<p>Cela nécessite l'activation du <a href='../mod_annees_anterieures/admin.php?quitter_la_page=y' target='_blank'>module 'Années antérieures'</a>.</p>";
		}
		$tabdiv_infobulle[]=creer_div_infobulle('archivage',"Archivage d'une année","",$texte,"",30,0,'y','y','n','n');
	}

	// CDT
	$sql="SELECT 1=1 FROM ct_entry LIMIT 1;";
	$test1=mysql_query($sql);
	$sql="SELECT 1=1 FROM ct_devoirs_entry LIMIT 1;";
	$test2=mysql_query($sql);
	if((mysql_num_rows($test1)>0)||(mysql_num_rows($test2)>0)) {
		echo "<p>Les cahiers de textes ne sont pas vides.<br />\n";
		echo "Vous devriez&nbsp;:</p>\n";
		echo "<ol>\n";
		echo "<li><a href='../cahier_texte_2/archivage_cdt.php'>archiver les cahiers de textes de l'an dernier</a> si ce n'est pas encore fait,</li>\n";
		echo "<li>puis <a href='../cahier_texte_admin/admin_ct.php'>vider les cahiers de textes de l'an dernier</a> avant de procéder à l'initialisation.</li>\n";
		echo "</ol>\n";
	}
	*/
?>
<ul>
	<li>
	<p>During the procedure, if necessary, certain data of the last year will be definitively erased from GEPI base (students, notes, appreciations, ...).<br />
	Only will be preserved the following data :<br /></p>
	<ul>
		<li><p>data relating to the schools,</p></li>
		<li><p>data relating to the classes : short headings, long headings, number of periods and names of the periods,</p></li>
		<li><p>data relating to the courses : complete identifiers and headings,</p></li>
		<li><p>data relating to the users (professors, administrators, ...). Concerning the professors, the courses taught by the professors are preserved,</p></li>
		<li><p>Data relating to the various types of AID.</p></li>
	</ul>
	</li>
	<li>
		<p>To proceed to the imports, four files are necessary:</p>
		<p>Three first, 'ElevesAvecAdresses.xml', 'Nomenclature.xml', 'ResponsablesAvecAdresses.xml', must be recovered from the application Web Sconet.<br />
		Nicely ask your secretary to go in 'Sconet/Access students Bases  normal mode/Exploitation/Exports standard/Exports XML generic' to recover the files 'ElevesAvecAdresses.xml', 'Nomenclature.xml' and 'ResponsablesAvecAdresses.xml'.</p>
		<p>The last, 'sts_emp_RNE_ANNEE.xml', must be recovered from the application STS/web.<br />
		Ask nicely your secretary access to STS-Web and carry out the following course: 'Update/Exports/Timetables'</p>

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

		<ul>
			<li>
				<p><a href='step1.php'>Proceed to the first phase</a> of importation of the students, of constitution of the classes and of assignment of the students in the classes : the file <b>ElevesAvecAdresses.xml</b> (<i>or ElevesSansAdresses.xml</i>) and the file <b>Nomenclature.xml</b> are necessary.<br />
				The second file is used to identify the short names of the options of the students (<i>the first file contains only the numeric digital codes of these options</i>).</p>
			</li>
			<li>
				<p><a href='responsables.php'>Proceed to the second phase</a> of importation of the responsibles of the students : the file <b>ResponsablesAvecAdresses.xml</b> is necessary.</p>
			</li>
			<li>
				<p><a href='matieres.php'>Proceed to the third phase</a> of importation of the courses : the file <b>sts_emp_RNE_ANNEE.xml</b> is necessary.</p>
			</li>
			<li>
				<p><a href='professeurs.php'>Proceed to the fourth phase</a> of importation of the professors.<br />
				The file <b>sts_emp_RNE_ANNEE.xml</b> must be provided at the previous stage to be able to be read again during this stage.</p>
			</li>
			<li>
				<p><a href='prof_disc_classe_csv.php?a=a<?php echo add_token_in_url();?>'>Proceed to the fifth phase</a> of assignment of the courses to each professor, of assignment of the professors in each class and of definition of the options followed by the students.<br />
				The file <b>sts_emp_RNE_ANNEE.xml</b> must be provided two stages to before be able to be read again during this stage.</p>
			</li>
			<li>
				<p><a href='init_pp.php'> Proceed to the sixth phase</a>: Initialization of the principal professors.</p>
			</li>
			<li>
				<p><a href='clean_tables.php?a=a<?php echo add_token_in_url();?>'>Proceed to the seventh phase</a> of cleaning of the data : the useless data imported starting from files XML during the various phases of initialization will be erased !</p>
			</li>
			<li>
				<p><a href='clean_temp.php?a=a<?php echo add_token_in_url();?>'>Proceed to the phase of cleaning of the files</a>: remove files XML and CSV which would not have been removed before.</p>
			</li>
		</ul>
	</li>
	<li>
		<p>Once all procedure of initialization of the data finished, it will be possible for you to carry out all the modifications necessary to individually by the means of the management tools included in <b>GEPI</b>.</p>
	</li>
</ul>
<?php require("../lib/footer.inc.php");?>