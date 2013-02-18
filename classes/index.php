<?php
/*
* $Id: index.php 5907 2010-11-19 20:30:52Z crob $
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

//**************** EN-TETE **************************************
$titre_page = "Management of the classes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
?>
<p class=bold>
<a href="../accueil_admin.php"><img src='../images/icons/back.png' alt='Return' class='back_link' /> Return </a>
| <a href='classes_param.php'>Parameter setting of several classes by batches</a>
| <a href='cpe_resp.php'>Fast parameter setting of CPE Responsible</a>
| <a href='scol_resp.php'>Parameter setting schooling</a>
| <a href='acces_appreciations.php'>Parameter setting of the access to the appreciations</a>
| <a href='../groupes/repartition_ele_grp.php'>Distribute students between several groups</a>
</p>
<p style='margin-top: 10px;'>
<img src='../images/icons/add.png' alt='' class='back_link' /> <a href="modify_nom_class.php">Add a class</a>
</p>

<?php
// On va chercher les classes déjà existantes, et on les affiche.
$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
$nombre_lignes = mysql_num_rows($call_data);
if ($nombre_lignes != 0) {
	$flag = 1;
	//echo "<table cellpadding=3 cellspacing=0 style='border: none; border-collapse: collapse;'>\n";
	echo "<table class='boireaus padd_et_bordg'>\n";
	$i = 0;
	$alt=1;
	while ($i < $nombre_lignes){
		$alt=$alt*(-1);
		$id_classe = mysql_result($call_data, $i, "id");
		$classe = mysql_result($call_data, $i, "classe");
		echo "<tr";
		echo " class='lig$alt white_hover'";
		echo ">\n";
		echo "<td>\n";
		echo "<b>$classe</b> ";
		echo "</td>\n";

		echo "<td>\n";
		echo "<a href='periodes.php?id_classe=$id_classe'><img src='../images/icons/date.png' alt='' /> Periods</a></td>\n";
		//echo "<td>|<a href='modify_class.php?id_classe=$id_classe'>Gérer les matières</a></td>\n";

		$sql="select id_classe from periodes where id_classe = '$id_classe';";
		$res_nb_per=mysql_query($sql);
		$nb_per = mysql_num_rows($res_nb_per);
		echo "<td>\n";
		if ($nb_per != 0) {
			echo "<a href='classes_const.php?id_classe=$id_classe'><img src='../images/icons/edit_user.png' alt='' /> Students</a>\n";
		}
		else {
			echo "&nbsp;";
		}
		echo "</td>\n";
		
		echo "<td>\n";
		echo "<a href='../groupes/edit_class.php?id_classe=$id_classe'> <img src='../images/icons/document.png' alt='' /> Courses</a>\n";
		echo "</td>\n";
		
		echo "<td>\n";
		echo "<a href='../calculnotes/matiere_classes.php?classes=$id_classe&nomclass=$classe'> <img src='../images/icons/document.png' alt='' /> Courses for marks</a>\n";
		echo "</td>\n";

/* 		echo "<td>\n";
		if ($nb_per != 0) {
			echo "[<a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe'>config. simplified</a>]\n";
		}
		else {
			echo "&nbsp;\n";
		}
		echo "</td>\n"; */

		echo "<td>\n";
		echo "<a href='modify_nom_class.php?id_classe=$id_classe'><img src='../images/icons/configure.png' alt='' /> Parameters</a>\n";
		echo "</td>\n";

		echo "<td>\n";
		echo "<a href='../lib/confirm_query.php?liste_cible=$id_classe&amp;action=del_classe".add_token_in_url()."'><img src='../images/icons/delete.png' alt='' /> Delete</a>\n";
		echo "</td>\n";

		//=======================================
		echo "<td>\n";
		if ($nb_per == 0) echo " <b>(Classe virtuelle)</b> "; else echo "&nbsp;";
		echo "</td>\n";
		echo "</tr>\n";
		$i++;
	}

	echo "</table>\n";
}
else {
	echo "<p class='grand'>Caution : no class was defined in base GEPI !</p>\n";
	echo "<p>You can add classes to the base while clicking on the link above, or directly <br /><a href='../initialisation/index.php'>to import the students and the classes from files GEP.</a></p>\n";
}
require("../lib/footer.inc.php");
?>