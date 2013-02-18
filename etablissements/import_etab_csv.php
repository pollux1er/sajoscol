<?php
/*
* $Id: import_etab_csv.php 8627 2011-11-14 20:37:05Z crob $
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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
$titre_page = "Schools | Importation of a csv file ";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV
$long_max = 8000;

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Return' class='back_link' /> Return</a></>\n";;

if (!isset($is_posted)) {
	echo "<p><span class = 'grand'>First phase of importation of the schools </span></p>\n";
	echo "<hr />\n";
	echo "<p>Choose a file csv among those currently available in GEPI distribution  : <br />\n";
	echo "<form enctype=\"multipart/form-data\" action=\"import_etab_csv.php\" method=post name=\"formulaire\">\n";

	echo add_token_field();

	$handle=opendir('./bases');
	echo "<select name=\"csv_file\" size=\"1\">\n";
	$file_tab = array();
	while ($file = readdir($handle)) {
	if (($file != '.') and ($file != '..'))
		// On met le fichier dans un tableau, histoire de pouvoir classer tout ça
		$files_tab[] = $file;
	}
	sort($files_tab);
	foreach ($files_tab as $file) {
	echo "<option>".$file."</option>\n";
	}
	echo "</select>\n";
	closedir($handle);
	echo "<input type='submit' value='Validate' />\n";
	echo "<input type='hidden' name='is_posted' value='1' />\n";
	echo "<input type='hidden' name='choix' value=\"gepi\" />\n";
	echo "</form>\n";

	echo "<br /><br /><hr />\n";
/*
	echo "<p>Choisir un autre fichier de votre choix :<br />
	<form enctype=\"multipart/form-data\" action=\"import_etab_csv.php\" method=\"post\" name=\"formulaire\">\n";
*/
	echo "<p>Choose another file of your choice :<br />
	<form enctype=\"multipart/form-data\" action=\"import_etab_csv.php\" method=\"post\" name=\"formulaire2\">\n";
	echo add_token_field();

	$csv_file = "";
	echo "<input type='file' name=\"csv_file\" />\n";
	echo "<input type='submit' value='Validate' />\n";
	?>
	<p><label for='en_tete' style='cursor: pointer;'>If the file to be imported comprises a first heading line (<i>not vacuum</i>) to be unaware of, check the box opposite&nbsp;
	<input type='checkbox' name="en_tete" id="en_tete" value="yes" /></label></p>
	<input type='hidden' name='is_posted' value='1' />
	<input type='hidden' name='choix' value="autre" />

	</FORM>
	<?php
	echo "<p>The file of importation perhaps made up using a spreadsheet starting from the information contained in the file \"nmetabc.txt \" which is in GEP.";
	echo "<br />It must be with the format csv (separating: semicolon) and must contain the six following fields:<br />\n";
	echo "--> <B>N° RNE of the school</B><br />\n";
	echo "--> <B>The name of the school</B><br />\n";
	echo "--> <B>The type :</B>\n<ul>\n";
	foreach ($type_etablissement as $type_etab => $nom_etablissement) {
		if ($nom_etablissement != "") echo "<li>\"<b>".$type_etab."</b>\" (for the schools of the type \"".$nom_etablissement."\")</li>\n";
	}
	echo "</ul>\nOnly these possibilities are authorized (attention to respect cast).<br /><br />\n";
	echo "--> <B>The type  \"public\" or \"private\". Only these two possibilities are authorized.</B><br />\n";
	echo "--> <B>The postal code of the city.</B><br />\n";
	echo "--> <B>The city.</B>\n";

} else if (isset($is_posted ) and ($is_posted==1 )) {
	check_token(false);

	echo "<p><span class = 'grand'>Second phase of importation of the schools </span></p>\n";
	$table_etab=array();
	if ($_POST['choix'] == 'gepi') {
		$fp = @fopen("./bases/".$_POST['csv_file'], "r");
	} else {
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
	if($csv_file['tmp_name'] == "") {
		echo "<p>No file was selected !</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	$fp = @fopen($csv_file['tmp_name'], "r");
	}

	echo "<form enctype='multipart/form-data' action='import_etab_csv.php' method='post'>\n";
	echo "<p><b>Caution</b>, the data are not recorded yet in base GEPI. You must confirm the importation (button in bottom of the page)!</p>\n";
	if(!$fp) {
		echo "Impossible to open CSV file";
	} else {
		// Nombre total de lignes lues
		$row = 0;
		// Nombre total de lignes insérées dans la base
		$ind = 0;
		echo "<table class='boireaus'><tr>
		<th><p class='bold'>N° RNE</p></th>
		<th><p class='bold'>Name of the school</p></th>
		<th><p class='bold'>Type college/school/...</p></th>
		<th><p class='bold'>Type public/private</p></th>
		<th><p class='bold'>Postal code</p></th>
		<th><p class='bold'>City</p></th></tr>\n";
		$alt=1;
		while(!feof($fp)) {
			if (isset($en_tete)) {
				$data = fgetcsv ($fp, $long_max, ";");
				unset($en_tete);
			}
			else{
				$alt=$alt*(-1);
			}
			$data = fgetcsv ($fp, $long_max, ";");
			$num = count ($data);
			if ($num == 6)  {
				$reg_rne = '';
				$reg_nom = '';
				$reg_type2 = '';
				$reg_type1 = '';
				$reg_cp = '';
				$reg_ville = '';
				$row++;
				echo "<tr class='lig$alt white_hover'>\n";
				for ($c=0; $c<$num; $c++) {
					switch ($c) {
					case 0:
						//RNE
						$call_rne = mysql_query("SELECT * FROM etablissements WHERE id='$data[$c]'");
						$test = @mysql_num_rows($call_rne);
						$couleur = 'black';
						if ($test != 0) {
							$couleur = 'red';
							$reg_ligne='no';
						}
						//echo "<td><p><b><font color = ".$couleur.">".$data[$c]."</font></p></b></td>\n";
						echo "<td><p><b><font color = ".$couleur.">".$data[$c]."</font></b></p></td>\n";
						$reg_rne=$data[$c];
						break;
					case 1:
						// Nom
						if ($data[$c] == "") {
						$col = "<b><font color='red'>Nondefinite</font></b>\n";
							$reg_ligne='no';
						} else {
							$reg_nom = traitement_magic_quotes(corriger_caracteres($data[$c]));
							$col = $data[$c];
						}
						echo "<td>$col</td>\n";
						break;
					case 2:
						// Type lycée/collège
						$tempo = $data[$c];
						$valid='no';
						foreach ($type_etablissement as $type_etabli => $nom_etablissement) {
							if ($tempo == $type_etabli) {
								$tempo = $nom_etablissement;
								$reg_type1 = $type_etabli;
								$valid='yes';

							}
						}
						if ($valid=='yes') {
							echo "<td><p>$tempo</p></td>\n";
						} else {
							echo "<td><b><font color='red'>Nondefinite</font></b></td>\n";
							$reg_ligne='no';
						}
						break;
					case 3:
						// Type public/privé
						$tempo = strtolower($data[$c]);
						$valid='yes';
						switch($tempo) {
							case "public":
							$reg_type2 = "public";
							break;
							case "prive":
							$reg_type2 = "prive";
							break;
							$valid = 'no';
						}
						if ($valid=='yes') {
							echo "<td><p>$tempo</p></td>\n";
						} else {
							echo "<td><b><font color='red'>Nondefinite</font></b></td>\n";
							$reg_ligne='no';
						}
						break;
					case 4:
						// Code postal
						if (preg_match ("/^[0-9]{1,5}$/", $data[$c])) {
							echo "<td><p>$data[$c]</p></td>\n";
							$reg_cp=$data[$c];
						} else {
							echo "<td><b><font color='red'>Nondefinite</font></b></td>\n";
							$reg_ligne='no';
						}
						break;
					case 5:
						// Ville
					if ($data[$c] == "") {
							$col = "<b><font color='red'>Nondefinite</font></b>\n";
							$reg_ligne='no';
							$reg_ville = '';
						} else {
							$col = $data[$c];
							$reg_ville = traitement_magic_quotes(corriger_caracteres($data[$c]))    ;
						}
						echo "<td>$col</td></tr>\n";
						break;
					}
				}
				if (isset($reg_ligne)) {
					unset($reg_ligne);
				} else {
					$table_etab[$ind][] = $reg_rne;
					$table_etab[$ind][] = $reg_nom;
					$table_etab[$ind][] = $reg_type1;
					$table_etab[$ind][] = $reg_type2;
					$table_etab[$ind][] = $reg_cp;
					$table_etab[$ind][] = $reg_ville;
					$ind++;
				}
			}
			// fin de la boucle "while(!feof($fp))"
		}
		fclose($fp);
		echo "</table>\n";
		echo "<p>First phase of the importation : <b>$row detected entries</b> !</p>\n";
		if ($row > 0) {
			$table_etab=serialize($table_etab);
			$_SESSION['table_etab']=$table_etab;
			echo "<p class='bold'>AVERTISSEMENT : </p>
			<ul><li>N° RNE which appears in red correspond to schools already present in the base.
			The corresponding lines will be ignored at the time of the final phase of importation.</li >
			<li>Headings \"<font color=red>Nondefinite</font>\" mean that the field in question is not valid.
			The corresponding line will be ignored at the time of the final phase of importation.</li>
			</ul>\n";
			if ($ind != 0) {
				if ($ind == 1) {
					echo "<center><p><b>".$ind." line is ready to be recorded.</b></p>\n";
				}
				else{
					echo "<center><p><b>".$ind." lines are ready to be recorded.</b></p>\n";
				}
				echo "<input type='submit' value='Save the data' /></center>\n";
				echo "<input type='hidden' name='is_posted' value='2' />\n";
			} else {
				echo "<center><p><b>There is no school to enter in the base.</p></center>\n";
			}

			echo add_token_field();

			echo "</form>\n";
		} else {
			echo "<p>The importation failed !</p>\n";
		}
	}
} else {
	echo "<p><span class = 'grand'>Third phase of importation of the schools </span></p>\n";
	if (!isset($_SESSION['table_etab'])) {
		echo "<center><p class='grand'>Operation non in conformity.</p></center></body></html>\n";
		die();
	}

	check_token(false);

	$table_etab=unserialize($_SESSION['table_etab']);
	$pb = 'no';
	for ($c=0; $c<count ($table_etab); $c++) {
		$couleur[$c] = '';
		$sql = mysql_query("INSERT INTO etablissements SET
		id='".$table_etab[$c][0]."',
		nom='".$table_etab[$c][1]."',
		niveau='".$table_etab[$c][2]."',
		type='".$table_etab[$c][3]."',
		cp='".$table_etab[$c][4]."',
		ville='".$table_etab[$c][5]."'
		");
		if (!$sql) {
			$couleur[$c] = 'red';
			$pb = 'yes';
		}
	}
	If ($pb == 'yes') {
		echo "<p>There were one or more problems during the recording.
		The lines in red indicate the defective recordings.</p>\n";
	} else {
		echo "<p class='bold'>".count ($table_etab)." schools were inserted successfully in the base.</p>\n";
	}

	echo "<table class='boireaus' cellpadding=\"2\" cellspacing=\"2\">\n";
	$alt=1;
	for ($c=0; $c<count ($table_etab); $c++) {
		$alt=$alt*(-1);
		if($couleur[$c]!=''){
			echo "<tr bgColor=\"".$couleur[$c]."\">\n";
		}
		else{
			echo "<tr class='lig$alt white_hover'>\n";
		}

		for ($j=0; $j<count($table_etab[$c]); $j++) {
			// Pour l'affichage final, on enlève les caractère \ qu'on a rajouté avec traitement_magic_quotes plus haut
			echo "<td>".stripSlashes($table_etab[$c][$j])."</td>\n";
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
	unset($_SESSION['table_etab']);

}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
