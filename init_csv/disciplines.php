<?php
@set_time_limit(0);
/*
* $Id: disciplines.php 8209 2011-09-13 16:40:20Z crob $
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
$titre_page = "Tool of initialization of the year : Importation of the courses";
require_once("../lib/header.inc");
//************** FIN EN-TETE ***************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/>Return home of initialization</a></p>
<?php

echo "<center><h3 class='gepi'>Third phase of initialization<br />Importation of the courses</h3></center>\n";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//

	echo "<p>You will carry out the third stage: it consists in importing the file <b>g_disciplines.csv</b> containing the data relating to the disciplines.</p>\n";
	echo "<p><i>Notice :</i> this operation does not erase any data in the base. It makes only one update, if necessary, of the list of the courses.</p>\n";
	echo "<p>The following fields must be present, in the order, and <b>separated by a semicolon</b> : </p>\n";
	echo "<ul><li> Short name of the course (it must be single)</li>\n" .
			"<li>Long name of the course</li>\n" .
			"</ul>\n";
	echo "<p>Please specify the complete name of the file <b>g_disciplines.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='disciplines.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
	//echo "<p><input type=\"checkbox\" name=\"ligne_entete\" value='y' /> Cocher si le fichier comporte une ligne d'entête.</p>\n";
    echo "<p><label for='ligne_entete' style='cursor:pointer;'>If the file to be imported contain a first heading line (nonempty)
to be ignore , <br />check the box opposite</label>&nbsp;<input type='checkbox' name='ligne_entete' id='ligne_entete' value='yes' checked /></p>\n";
	echo "<p><input type='submit' value='Validate' /></p>\n";
	echo "</form>\n";

} else {
	//
	// Quelque chose a été posté
	//
	if ($_POST['action'] == "save_data") {
		check_token(false);
		//
		// On enregistre les données dans la base.
		// Le fichier a déjà été affiché, et l'utilisateur est sûr de vouloir enregistrer
		//

		$sql="SELECT * FROM tempo2;";
		$res_temp=mysql_query($sql);
		if(mysql_num_rows($res_temp)==0) {
			echo "<p style='color:red'>ERROR&nbsp;: No association student/option was not found&nbsp;???</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		//$go = true;
		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		$nb_matieres_existantes=0;
		//while ($go) {
		while ($lig=mysql_fetch_object($res_temp)) {

			//$reg_nom_court = $_POST["ligne".$i."_nom_court"];
			//$reg_nom_long = $_POST["ligne".$i."_nom_long"];

			$reg_nom_court = $lig->col1;
			$reg_nom_long = $lig->col2;

			// On nettoie et on vérifie :
			$reg_nom_court = preg_replace("/[^A-Za-z0-9.\-]/","",trim(strtoupper($reg_nom_court)));
			if (strlen($reg_nom_court) > 50) $reg_nom_court = substr($reg_nom_court, 0, 50);
			//$reg_nom_long = preg_replace("/[^A-Za-z0-9 .\-éèüëïäêç]/","",trim($reg_nom_long));
			$reg_nom_long = preg_replace("/Æ/","AE",preg_replace("/æ/","ae",preg_replace("/¼/","OE",preg_replace("/½/","oe",preg_replace("/[^A-Za-z0-9 .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim($reg_nom_long))))));
			if (strlen($reg_nom_long) > 200) $reg_nom_long = substr($reg_nom_long, 0, 200);

			// Maintenant que tout est propre, on fait un test sur la table pour voir si la matière existe déjà ou pas

			$test = mysql_result(mysql_query("SELECT count(matiere) FROM matieres WHERE matiere = '" . $reg_nom_court . "'"), 0);

			if ($test == 0) {
				// Test négatif : aucune matière avec ce nom court... on enregistre !

				$insert = mysql_query("INSERT INTO matieres SET " .
						"matiere = '" . $reg_nom_court . "', " .
						"nom_complet = '" . $reg_nom_long . "',priority='0',matiere_aid='n',matiere_atelier='n'");
						//"nom_complet = '" . htmlentities($reg_nom_long) . "'");

				if (!$insert) {
					$error++;
					echo mysql_error();
				} else {
					$total++;
				}

			}
			else {
				$nb_matieres_existantes++;
			}


			$i++;
			if (!isset($_POST['ligne'.$i.'_nom_court'])) $go = false;
		}

		//if ($error > 0) echo "<p><font color='red'>Il y a eu " . $error . " erreurs.</font></p>\n";
		if ($error > 0){
			if ($error == 1){
				echo "<p><font color='red'>There was " . $error . " error.</font></p>\n";
			}
			else{
				echo "<p><font color='red'>There was " . $error . " errors.</font></p>\n";
			}
		}
		//if ($total > 0) echo "<p>" . $total . " matières ont été enregistrées.</p>\n";
		if ($total > 0){
			if ($total == 1){
				echo "<p>" . $total . " course was recorded.</p>\n";
			}
			else{
				echo "<p>" . $total . " courses were recorded.</p>\n";
			}
		}

		if($nb_matieres_existantes>0) {
			if ($nb_matieres_existantes == 1){
				echo "<p>" . $nb_matieres_existantes . " course existed already.</p>\n";
			}
			else{
				echo "<p>" . $nb_matieres_existantes . " courses existed already.</p>\n";
			}
		}

		echo "<p><a href='index.php'>Return in the previous page</a></p>\n";


	} else if ($_POST['action'] == "upload_file") {
		check_token(false);
		//
		// Le fichier vient d'être envoyé et doit être traité
		// On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
		// en proposant des champs de saisie pour modifier les données si on le souhaite
		//

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
		$ligne_entete=isset($_POST['ligne_entete']) ? $_POST['ligne_entete'] : 'no';

		// On vérifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur à être rigoureux
		if(strtolower($csv_file['name']) == "g_disciplines.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
				echo "<p>Impossible to open CSV file!</p>\n";
				echo "<p><a href='disciplines.php'>Click here </a> to restart !</p>\n";
			} else {

				// Fichier ouvert ! On attaque le traitement

				// On va stocker toutes les infos dans un tableau
				// Une ligne du CSV pour une entrée du tableau
				$data_tab = array();

				//=========================
				if($ligne_entete=="yes"){
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
				}
				//=========================

					$k = 0;
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if(trim($ligne)!="") {

							$tabligne=explode(";",$ligne);

							// 0 : Nom court de la matière
							// 1 : Nom long de la matière


							// On nettoie et on vérifie :
							$tabligne[0] = preg_replace("/[^A-Za-z0-9.\-]/","",trim(strtoupper($tabligne[0])));
							if (strlen($tabligne[0]) > 50) $tabligne[0] = substr($tabligne[0], 0, 50);
							//$tabligne[1] = preg_replace("/[^A-Za-z0-9 .\-éèüëïäêç]/","",trim($tabligne[1]));
							$tabligne[1] = preg_replace("/Æ/","AE",preg_replace("/æ/","ae",preg_replace("/¼/","OE",preg_replace("/½/","oe",preg_replace("/[^A-Za-z0-9 .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim($tabligne[1]))))));
							if (strlen($tabligne[1]) > 200) $tabligne[1] = substr($tabligne[1], 0, 200);

							$data_tab[$k] = array();



							$data_tab[$k]["nom_court"] = $tabligne[0];
							$data_tab[$k]["nom_long"] = $tabligne[1];

						}
					$k++;
					}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				$sql="TRUNCATE TABLE tempo2;";
				$vide_table = mysql_query($sql);

				$nb_error=0;

				echo "<form enctype='multipart/form-data' action='disciplines.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table border='1' class='boireaus' summary='Table of courses'>\n";
				echo "<tr><th>Short name (unique)</th><th>Long name</th></tr>\n";


				$alt=1;
				for ($i=0;$i<$k-1;$i++) {
					$alt=$alt*(-1);
                    echo "<tr class='lig$alt'>\n";
					echo "<td>\n";
					$sql="INSERT INTO tempo2 SET col1='".$data_tab[$i]["nom_court"]."',
					col2='".addslashes($data_tab[$i]["nom_long"])."';";
					$insert=mysql_query($sql);
					if(!$insert) {
						echo "<span style='color:red'>";
						echo $data_tab[$i]["nom_court"];
 						echo "</span>";
						$nb_error++;
					}
					else {
						echo $data_tab[$i]["nom_court"];
					}
					//echo "<input type='hidden' name='ligne".$i."_nom_court' value='" . $data_tab[$i]["nom_court"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["nom_long"];
					//echo "<input type='hidden' name='ligne".$i."_nom_long' value='" . $data_tab[$i]["nom_long"] . "' />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";

				if($nb_error>0) {
					echo "<span style='color:red'>$nb_error error detected during preparation.</style><br />\n";
				}

				echo "<input type='submit' value='Save' />\n";

				echo "</form>\n";
			}

		} else if (trim($csv_file['name'])=='') {

			echo "<p>No file was selected !<br />\n";
			echo "<a href='disciplines.php'>Click here </a> to restart !</p>\n";

		} else {
			echo "<p>The selected file is not valid !<br />\n";
			echo "<a href='disciplines.php'>Click here </a> to restart !</p>\n";
		}
	}
}
require("../lib/footer.inc.php");
?>