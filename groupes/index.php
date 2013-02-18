<?php
/*
 * $Id: index.php 7192 2011-06-10 19:30:33Z crob $
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


if (isset($_GET['action'])) {
	check_token();

	if ($_GET['action'] == "delete_group") {
		if (!is_numeric($_GET['id_groupe'])) $_GET['id_groupe'] = 0;

		$verify = test_before_group_deletion($_GET['id_groupe']);
		if ($verify) {
			$delete = delete_group($_GET['id_groupe']);
			if ($delete == true) {
				$msg .= "The group " . $_GET['id_groupe'] . " was removed.";
			} else {
				$msg .= "An error prevented the suppression of the group.";
			}
		} else {
			$msg .= "Existing data block the suppression of the group. No note nor appreciation must have been typing for the students of this group to allow the suppression of the group.";
		}
	}
}


//**************** EN-TETE **************************************
$titre_page = "Management of the groups";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

?>
<p class=bold>
<a href="../accueil_admin.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>
 | <a href="add_group.php?mode=regroupement">Add a regrouping (interclasses)</a>
 | <a href="add_group.php?mode=groupe">Add a group to a class</a>

<?php

// On va chercher les classes déjà existantes, et on les affiche.

$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
$nombre_lignes = mysql_num_rows($call_data);
if ($nombre_lignes != 0) {
    $flag = 1;
    echo "<table cellpadding=3 cellspacing=0 border=0>\n";
    $i = 0;
    while ($i < $nombre_lignes){
        $id_classe = mysql_result($call_data, $i, "id");
        $classe = mysql_result($call_data, $i, "classe");

        // On n'affiche que si la classe en question n'est pas une classe virtuelle
	    if (get_period_number($id_classe) != "0") {
	        echo "<tr";
	        if ($flag==1) { echo " class='fond_sombre'"; $flag = 0;} else {$flag=1;};
	        echo ">\n<td><b><a href='edit_class.php?id_classe=". $id_classe . "'>" . $classe . "</a></b>";
	        echo "</td>\n";
	        echo "<td><a href='add_group.php?id_classe=" . $id_classe . "&amp;mode=groupe'>Add group</a></td>\n";

			//$groups = get_groups_for_class($id_classe);
			$groups = get_groups_for_class($id_classe,"","n");
			echo "<td>\n";
			foreach ($groups as $group) {
				$total = count($group["classes"]);
				if ($total == "1") {
					echo "<a href='edit_group.php?id_groupe=". $group["id"] . "&amp;mode=groupe'>";
				} else {
					echo "<a href='edit_group.php?id_groupe=". $group["id"] . "&amp;mode=regroupement'>";
				}
				echo "<img src='../images/edit16.png' alt='Modifier' style='width:12px; heigth: 12px;' /></a>\n";


				echo "<a href='edit_eleves.php?id_groupe=". $group["id"] . "'><img src='../images/group16.png' alt='Manage the students' style='width:12px; heigth: 12px;' /></a>\n";
				echo "&nbsp;";

				echo "<b>" . htmlentities($group["description"]) . "</b>\n";

				$j= 1;
				if ($total > 1) {
					echo " (";

					foreach ($group["classes"] as $classe) {
						echo $classe["classe"];
						if ($j < $total) echo ", ";
						$j++;
					}
					echo ") ";
				}
				echo "<a href='index.php?id_groupe=". $group["id"] . "&amp;action=delete_group".add_token_in_url()."'><img src='../images/delete16.png' alt='Delete' style='width:12px; heigth: 12px;' /></a>";
				echo "<br />\n";

			}
			echo "</td>\n";
			echo "</tr>\n";
        }
    $i++;
    }
    echo "</table>\n";
} else {
    echo "<p class='grand'>Caution: no class was defined in base GEPI !</p>\n";
    echo "<p>You must have defined classes before being able to edit the groups.</p>\n";
}
require("../lib/footer.inc.php");
?>