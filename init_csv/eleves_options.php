<?php
@set_time_limit(0);
/*
* $Id: eleves_options.php 8209 2011-09-13 16:40:20Z crob $
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

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return home of initialization</a></p>
<?php

echo "<center><h3 class='gepi'>Seventh phase of initialization<br />Importation of associations students-options</h3></center>\n";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//

	echo "<p>You will carry out the seventh and last stage : it consists in importing the file <b>g_eleves_options.csv</b> containing associations of the students and courses of the
type 'groupe', i.e. which does not corerespondent ot a course of whole class.\n";
	echo "<p>Notice : this operation does not erase the classes. It makes only an update, if necessary, of the list of the courses.\n";
	echo "<p>The following fields must be present, in the order, and <b>separated by a semicolon</b> : \n";
	echo "<ul><li>Identifier (intern) of the student</li>\n" .
			"<li>Course identifier (separated by a point of exclamation)</li>\n" .
			"</ul>\n";
	echo "<p>Remarque : you can specify only one line by student, by indicating all the courses followed in the second field by
separating the identifiers from courses with a point of
exclamation, but you can also have a line for a simple association, and to have as many lines as courses followed by the student.</p>\n";
	echo "<p>Please specify the complete name of the file <b>g_eleves_options.csv</b>.\n";
	echo "<form enctype='multipart/form-data' action='eleves_options.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";

    echo "<p><label for='en_tete' style='cursor:pointer;'>If the file to be imported comprises a first heading line (not empty) to ignore, <br />check the box opposite</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes' checked /></p>\n";

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
			echo "<p style='color:red'>ERROR&nbsp;: No course was found&nbsp;???</p>\n";
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
		//while ($go) {
		while ($lig=mysql_fetch_object($res_temp)) {
			/*
			$reg_id_int = $_POST["ligne".$i."_id_int"];
			$reg_options = $_POST["ligne".$i."_options"];
			*/
			$reg_id_int = $lig->col1;
			$reg_options = $lig->col2;

			// On nettoie et on vérifie :
			$reg_id_int = preg_replace("/[^0-9]/","",trim($reg_id_int));
			if (strlen($reg_id_int) > 50) $reg_id_int = substr($reg_id_int, 0, 50);
			$reg_options = preg_replace("/[^A-Za-z0-9.\-!]/","",trim($reg_options));
			if (strlen($reg_options) > 2000) $reg_options = substr($reg_options, 0, 2000); // Juste pour éviter une tentative d'overflow...


			// Première étape : on s'assure que l'élève existe et on récupère son login... S'il n'existe pas, on laisse tomber.
			$test = mysql_query("SELECT login FROM eleves WHERE elenoet = '" . $reg_id_int . "'");
			if (mysql_num_rows($test) == 1) {
				$login_eleve = mysql_result($test, 0, "login");

				// Maintenant on récupère les différentes matières, et on vérifie qu'elles existent
				$reg_options = explode("!", $reg_options);
				$valid_options = array();
				foreach ($reg_options as $option) {
					$test = mysql_result(mysql_query("SELECT count(matiere) FROM matieres WHERE matiere = '" . $option ."'"), 0);
					if ($test == 1) {
						$valid_options[] = $option;
					}
				}

				// On a maintenant un tableau avec les options que l'élève doit suivre.

				// On récupère la classe de l'élève.

				$test = mysql_query("SELECT DISTINCT(id_classe) FROM j_eleves_classes WHERE login = '" . $login_eleve . "'");

				if (mysql_num_rows($test) != 0) {
					// L'élève fait bien parti d'une classe

					$id_classe = mysql_result($test, 0, "id_classe");

					// Maintenant on a tout : les options, la classe de l'élève, et son login
					// Enfin il reste quand même un truc à récupérer : le nombre de périodes :

					$num_periods = mysql_result(mysql_query("SELECT count(num_periode) FROM periodes WHERE id_classe = '" . $id_classe . "'"), 0);

					// Bon cette fois c'est bon, on a tout. On va donc procéder de la manière suivante :
					// - on regarde s'il existe un groupe pour la classe dans la matière considérée
					// - on teste pour voir si l'élève n'est pas déjà inscrit dans ce groupe
					// - s'il ne l'est pas, on l'inscrit !
					// Simple, non ?

					// On procède matière par matière :

					foreach ($valid_options as $matiere) {
						$test = mysql_query("SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE (" .
								"jgc.id_classe = '" . $id_classe . "' AND " .
								"jgc.id_groupe = jgm.id_groupe AND " .
								"jgm.id_matiere = '" . $matiere . "')");
						if (mysql_num_rows($test) > 0) {
							// Au moins un groupe existe, c'est bon signe
							// On passe groupe par groupe pour vérifier si l'élève est déjà inscrit ou pas
							for ($j=0;$j<mysql_num_rows($test);$j++) {
								// On extrait l'ID du groupe
								$group_id = mysql_result($test, $j, "id_groupe");
								// On regarde si l'élève est inscrit
								$res = mysql_result(mysql_query("SELECT count(login) FROM j_eleves_groupes WHERE (id_groupe = '" . $group_id . "' AND login = '" . $login_eleve . "')"), 0);
								if ($res == 0) {
									// L'élève ne fait pas encore parti du groupe. On l'inscrit pour les périodes de la classe
									for ($p=1;$p<=$num_periods;$p++) {
										// On enregistre
										$reg = mysql_query("INSERT INTO j_eleves_groupes SET id_groupe = '" . $group_id . "', login = '" . $login_eleve . "', periode = '" . $p . "'");
										if (!$reg) {
											$error++;
											echo mysql_error();
										} else {
											if ($p == 1) $total++;
										}
									}
								}
							}
						}
					}
				}
			}

			$i++;
			//if (!isset($_POST['ligne'.$i.'_id_int'])) $go = false;
		}

		if ($error > 0) {echo "<p><font color='red'>There was " . $error . " errors.</font></p>\n";}
		if ($total > 0) {echo "<p>" . $total . " associations students-options were recorded.</p>\n";}
		if(($error==0)&&($total==0)) {echo "<p>No association student-option was recorded???</p>\n";}

		echo "<p><a href='index.php'>Return to the previous page</a></p>\n";


	} else if ($_POST['action'] == "upload_file") {
		check_token(false);
		//
		// Le fichier vient d'être envoyé et doit être traité
		// On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
		// en proposant des champs de saisie pour modifier les données si on le souhaite
		//

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		// On vérifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur à être rigoureux
		if(strtolower($csv_file['name']) == "g_eleves_options.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
				echo "<p><a href='eleves_options.php'>Click here </a> to restart !</center></p>\n";
			} else {

				// Fichier ouvert ! On attaque le traitement

				// On va stocker toutes les infos dans un tableau
				// Une ligne du CSV pour une entrée du tableau
				$data_tab = array();

				//=========================
				// On lit une ligne pour passer la ligne d'entête:
				if($en_tete=="yes") {
					$ligne = fgets($fp, 4096);
				}
				//=========================

				$k = 0;
				while (!feof($fp)) {
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!="") {

						$tabligne=explode(";",$ligne);

						// 0 : ID interne de l'élève
						// 1 : Identifiants de matières


						// On nettoie et on vérifie :
						$tabligne[0] = preg_replace("/[^0-9]/","",trim($tabligne[0]));
						if (strlen($tabligne[0]) > 50) $tabligne[0] = substr($tabligne[0], 0, 50);

						if(!isset($tabligne[1])) {$tabligne[1]="";}
						$tabligne[1] = preg_replace("/[^A-Za-z0-9.\-!]/","",trim($tabligne[1]));
						if (strlen($tabligne[1]) > 2000) $tabligne[1] = substr($tabligne[1], 0, 2000);


						$data_tab[$k] = array();



						$data_tab[$k]["id_int"] = $tabligne[0];
						$data_tab[$k]["options"] = $tabligne[1];

						$k++;
					}
					//$k++;
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				$sql="TRUNCATE TABLE tempo2;";
				$vide_table = mysql_query($sql);

				$nb_error=0;

				echo "<form enctype='multipart/form-data' action='eleves_options.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table class='boireaus' summary='Students/options'>\n";
				echo "<tr><th>interns ID of the student</th><th>Courses</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k-1;$i++) {
					$alt=$alt*(-1);
                    echo "<tr class='lig$alt'>\n";
					echo "<td>\n";
					$sql="INSERT INTO tempo2 SET col1='".$data_tab[$i]["id_int"]."',
					col2='".addslashes($data_tab[$i]["options"])."';";
					$insert=mysql_query($sql);
					if(!$insert) {
						echo "<span style='color:red'>";
						echo $data_tab[$i]["id_int"];
 						echo "</span>";
						$nb_error++;
					}
					else {
						echo $data_tab[$i]["id_int"];
					}
					//echo "<input type='hidden' name='ligne".$i."_id_int' value='" . $data_tab[$i]["id_int"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["options"];
					//echo "<input type='hidden' name='ligne".$i."_options' value='" . $data_tab[$i]["options"] . "' />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";

				if($nb_error>0) {
					echo "<span style='color:red'>$nb_error errors detected during preparation.</style><br />\n";
				}

				echo "<p><input type='submit' value='Save' /></p>\n";

				echo "</form>\n";
			}

		} else if (trim($csv_file['name'])=='') {

			echo "<p>No file was selected !<br />\n";
			echo "<a href='eleves_options.php'>Click here </a> to restart !</p>\n";

		} else {
			echo "<p>The selected file is not valid !<br />\n";
			echo "<a href='eleves_options.php'>Click here </a> to restart !</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>