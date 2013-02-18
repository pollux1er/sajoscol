<?php

/*
 * $Id: index.php 7844 2011-08-20 14:49:46Z crob $
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

/*
if (!function_exists("dbase_open"))  {
    $msg = "ATTENTION : PHP n'est pas configuré pour gérer les fichiers GEP (dbf). L'extension  d_base n'est pas active. Adressez-vous à l'administrateur du serveur pour corriger le problème.";
}
*/

//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="../gestion/index.php#init_xml"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>

<p>You will carry out the initialization of the school year which has just begun.<br />
<?php

	if((getSettingValue('use_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")) {
		echo "<p style='color:red;'><b>CAUTION&nbsp;:</b> You use a LCS gold SCRIBE server.<br />
		There is a mode of initialization of the year suitable for <a href='../init_lcs/index.php'>LCS</a> on the one hand and to SCRIBE on the other hand (<i><a href='../init_scribe/index.php'>Scribe</a> and <a href='../init_scribe_ng/index.php'>Scribe_ng</a></i>).<br />
		If you initialize the year with mode XML, you will not be able to use thereafter the accounts of your server
LCS/SCRIBE to access to GEPI.<br />Think there of twice before continuing.</p>\n";
		echo "<br />\n";
	}

	echo "<p>Have you carry out the various operations of end of the year and preparation of new year in the page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Change of year</a>&nbsp?</p>\n";
	/*
	$sql="SELECT 1=1 FROM matieres_notes LIMIT 1;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo "Avez-vous pensé à <a href='#' onmouseover=\"afficher_div('archivage','y',20,20);\" onclick=\"return false;\">archiver</a> l'année qui se termine ?</p>\n";
		$texte="<p>L'archivage de l'année en cours vous permettra, une fois passé à l'année suivante, de consulter les bulletins antérieurs de chacun de vos élèves, pour peu qu'ils aient été scolarisés dans votre établissement.</p><p>Cela nécessite l'activation du <a href='../mod_annees_anterieures/admin.php'>module 'Années antérieures'</a>.</p>";
		$tabdiv_infobulle[]=creer_div_infobulle('archivage',"Archivage d'une année","",$texte,"",30,0,'y','y','n','n');
	}

	// CDT
	$sql="SELECT 1=1 FROM ct_entry LIMIT 1;";
	$test1=mysql_query($sql);
	$sql="SELECT 1=1 FROM ct_devoirs_entry LIMIT 1;";
	$test2=mysql_query($sql);
	if((mysql_num_rows($test1)>0)||(mysql_num_rows($test2)>0)) {
		echo "<p>Les cahiers de textes ne sont pas vides.<br />Vous devriez <a href='cahier_texte_admin/admin_ct.php'>vider les cahiers de textes de l'an dernier</a> avant de procéder à l'initialisation.</p>\n";
	}
	*/
?>
</p>
<ul>
<li>
<p>During the procedure, if necessary, certain data of the last year will be definitively erased base GEPI (students, notes, appreciations, ...) . 
Only will be preserved the following data :<br /></p>
	<ul>
		<li><p>data relating to the schools,</p></li>
		<li><p>data relating to the classes : short headings, long headings, number of periods and names of the periods,</p></li>
		<li><p> data relating to the courses : complete identifiers and headings,</p></li>
		<li><p>data relating to the personal users in the school (professors, administrators, ...). Concerning the professors, the courses taught by the professors are preserved (and possibly supplemented in phase 5),</p></li>
		<li><p>Data relating to the various types of IDA.</p></li>
	</ul>
</li>
<li>

	<!--p>L'initialisation s'effectue en quatre phases, chacune nécessitant un fichier GEP particulier <b>ou des CSV</b>:</p>
	<ul>
		<li><p>Vous devez disposer des fichiers F_ELE.DBF et F_ERE.DBF générés par l'AutoSco.<br />
		Générer le F_ELE.CSV correspondant au F_ELE.DBF depuis Sconet est assez facile sauf pour l'ERENO qui n'est pas récupéré et du coup générer un F_ERE.CSV n'est pas commode.<br />Il faut en effet fixer arbitrairement un ERENO pour faire le lien entre parents et enfants et ne récupérer que les entrées souhaitées de Sconet pour les parents (<i>on récupère là plus de deux lignes par élève...</i>)...<br />Bref, j'ai laissé en plan.</p></li>
		<li><p>Vous pouvez générer les fichiers F_TMT.CSV, F_MEN.CSV et F_GPD.CSV à l'aide de l'export XML de STS une fois l'emploi du temps remonté.</p>
		<p>Vous pouvez également compléter partiellement le F_WIND.CSV de cette façon.<br />
		Partiellement parce que certains champs ne sont pas récupérés:</p>
		<ul>
			<li>le NUMEN (INDNNI) utilisé comme mot de passe par défaut par GEPI n'est pas récupéré.<br />
			Il est alors proposé de définir un mot de passe aléatoire ou d'utiliser la date de naissance à la place.</li>
			<li>La civilité n'est pas récupérée non plus (<i>mais il est assez facile de la compléter</i>).</li>
			<li>Enfin, le champ FONCCO n'est pas rempli non plus (<i>mais c'est en principe 'ENS' pour tous les enseignants</i>).</li>
		</ul>
		<p><a href='lecture_xml_sts_emp.php'>Générer les fichiers CSV à partir de l'export XML de STS</a>.</p>
		<p><b>AJOUT:</b> <a href='lecture_xml_sconet.php'>Générer les fichiers CSV à partir des exports XML de Sconet</a>.</p></li>
	</ul-->
	<p>Professors, courses,...: <a href='lecture_xml_sts_emp.php'>Generate CSV files starting from export XML of STS</a>.</p>
	<p>Students: <a href='lecture_xml_sconet.php'>Generate CSV files starting from exports XML of Sconet</a>.</p>
</li>
<li>

	<?php
	//==================================
	// RNE de l'établissement pour comparer avec le RNE de l'établissement de l'année précédente
	$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
	//==================================
	if($gepiSchoolRne=="") {
		echo "<p><b style='color:red;'>Caution</b>: The RNE of the school is not indicated in ' general Management/<a href='../gestion/param_gen.php' target='_blank'>General configuration</a>'<br />That can disturb the importation of the school of origin of the students.<br />You should correct before continuing.</p>\n";
	}
	?>

	<p>To proceed to the imports:</p>
	<ul>
		<li><p><a href='step1.php'>To proceed to the first phase</a> of importation of the students,  of constitution of the classes and assignment of the students in
the classes : the file <b>ELEVES.CSV</b> is necessary.</p></li>
		<li><p><a href='responsables.php'>To proceed to the second phase</a> of importation of the responsibles of the students : files <b>PERSONNES.CSV</b>, <b>RESPONSABLES.CSV</b> and <b>ADRESSES.CSV</b> are necessary.</p></li>
		<li><p><a href='disciplines_csv.php'>Procéder à la troisième phase</a> d'importation des matières : le fichier <b>F_tmt.csv</b> est requis.</p></li>
		<li><p><a href='prof_csv.php?a=a<?php echo add_token_in_url();?>'>To proceed to the fourth phase</a> of importation of the professors: the file <b>F_wind.csv</b> is necessary.</p></li>
		<li><p><a href='prof_disc_classe_csv.php?a=a<?php echo add_token_in_url();?>'>To proceed to the fifth phase</a> of assignment of the courses to each professor, of assignment of the professors in each class and definition of the options followed by the students : files <b>F_men.csv</b> and <b>F_gpd.csv</b> are necessary.</p></li>

		<li><p><a href='init_pp.php'>To proceed to the sixth phase</a>: Initialization of the principal professors.</p></li>

		<li><p><a href='clean_tables.php?a=a<?php echo add_token_in_url();?>'>To proceed to the seventh phase</a> of cleaning of the data : the useless data imported from files GEP during the
various phases of initialization will be erased !</p></li>

	</ul>
</li>
<li>
	<p>Once all procedure of initialization of the data finished, it will be possible for you to carry out all the modifications
necessary individually with the management tools included in <b>GEPI</b>.</p>
</li>
</ul>
<?php require("../lib/footer.inc.php");?>