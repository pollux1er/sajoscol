<?php

/*
 * $Id: index.php 5937 2010-11-21 17:42:55Z crob $
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
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

// Page bourrinée... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();

if (!function_exists("dbase_open"))  {
    $msg = "CAUTION : PHP is not configured to manage files GEP (dbf). Extension  d_base is not active. Cantact the administrator of the server to correct the problem.";
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold>|<a href="../gestion/index.php">Return</a>|</p>

<p>You will carry out the initialization of the school year which has just begun.</p>
<ul>
<li>During the procedure, if necessary, certain data of the last year will be definitively erased from the base GEPI (students, notes, appreciations, ...) . Only will be preserved the following data :<br /><br />
- data relating to the schools,<br />
- data relating to the classes : short headings, long headings, number of periods and names of the periods,<br />
- data relating to the courses : complete identifiers and headings,<br />
- data relating to the users (professors, administrators, ...). Concerning the professors, the courses taught by the professors are preserved,<br />
- Data relating to the various types of IDA.</li><br />

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

	<p>Initialization is carried out in four phases, each one requiring a particular file GEP :</p>
    <ul>
    <li><p><a href='step1.php'>Proceed to the first phase</a> of importation of the students,  of constitution of the classes and assignment of the students in the classes : le fichier <b>F_ELE.DBF</b> est requis.<br />&nbsp;</p></li>
    <li><p><a href='responsables.php'>To proceed to the second phase</a> of importation of the responsibles of the students: the file <b>F_ERE.DBF</b> is necessary.<br />&nbsp;</p></li>
    <li><p><a href='disciplines.php'>To proceed to the third phase</a> of importation of the courses : the file <b>F_tmt.dbf</b> is necessary.<br />&nbsp;</p></li>
    <li><p><a href='professeurs.php'>Procéder à la quatrième phase</a> d'importation des professeurs : le fichier <b>F_wind.dbf</b> is necessary.<br />&nbsp;</p></li>
    <li><p><a href='prof_disc_classe.php'>To proceed to the fifth phase</a> of assignment of the courses to each professor, of assignment of the professors in each class and of definition of the options followed by the students : files <b>F_men.dbf</b> and <b>F_gpd.dbf</b> are necessary.<br />&nbsp;</p></li>
    <li><p><a href='clean_tables.php'>To proceed to the sixth phase</a> of cleaning of the data : the useless data imported from files GEP during of thevarious phases of initialization will be erased !<br />&nbsp;</p></li>
    </ul>
</li>
<li><p>Once all procedure of initialization of the data finished, it will be possible for you to carry out all the modifications necessary to individually by the means of the management tools included in <b>GEPI</b>.<br />&nbsp;</p></li>
</ul>
<?php
require("../lib/footer.inc.php");
?>