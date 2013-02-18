<?php
/*
 * $Id: saisie_vocabulaire.php 6649 2011-03-10 20:58:21Z crob $
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
//$resultat_session = resumeSession();
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
	die();
}
//include("../fckeditor/fckeditor.php") ;

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$export_vocab=isset($_GET['export_vocab']) ? $_GET['export_vocab'] : NULL;

if((isset($export_vocab))&&($export_vocab=="y")) {
	$nom_fic = "vocabulaire_et_correction.csv";

	send_file_download_headers('text/x-csv',$nom_fic);

	$fd='';
	
	$sql="SELECT * FROM vocabulaire ORDER BY terme, terme_corrige;";
	$txt=mysql_query($sql);
	
	while($lig=mysql_fetch_object($txt)){
		$fd.=$lig->terme.";".$lig->terme_corrige."\r\n";
	}
	echo $fd;
	die();
}


//================================
$titre_page = "Saisie de vocabulaire";
require_once("../lib/header.inc");
//================================

if (!loadSettings()) {
	die("Error loading settings");
}

echo "<p class='bold'><a href='../accueil.php'>Return</a>";
//echo "<p class='bold'><a href='../cahier_notes_admin/index.php'>Retour</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."?import_vocab=y'>Import a file of vocabulary</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."?export_vocab=y'>Export the file of vocabulary</a>";
echo "</p>\n";

$import_vocab=isset($_GET['import_vocab']) ? $_GET['import_vocab'] : NULL;
$valide_import_vocab=isset($_POST['valide_import_vocab']) ? $_POST['valide_import_vocab'] : NULL;

if(isset($import_vocab)) {
	echo "<h3 class='gepi'>Importation of a file of vocabulary &nbsp;:</h3>\n";

	echo "<p>The file must contain a couple 'terme ou enchaînement de mots mal \"orthographiés\" or tapés;terme... corrigé' by line.<br />\n";
	echo "<i>Example&nbsp;:</i><br />\n";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;it little;it can <br />\n";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;one can;a little<br />\n";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;marrow;courage<br />\n";
	echo "</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
	echo "<p><input type='submit' name='valide_import_vocab' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";

	/*
	echo "<p><i>NOTE&nbsp;</i> Le principe est le suivant&nbsp;: L'appréciation saisie est reformatée en remplaçant les points, virgules, points-virgules par des espaces pour rechercher si l'appréciation contient des chaines ressemblant à celles jugées comme mal tapées ou orthographiées.</p>\n";
	*/

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}
elseif(isset($valide_import_vocab)) {
	check_token(false);

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if (trim($csv_file['name'])=='') {
		echo "<p>No file was selected !<br />\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?import_vocab=y'>Cliquer ici</a> Start again !</p>\n";
	}
	else{

		//echo "mime_content_type(".$csv_file['tmp_name'].")=".mime_content_type($csv_file['tmp_name'])."<br />";
		//die();
		if((mime_content_type($csv_file['tmp_name'])!="text/plain")&&(mime_content_type($csv_file['tmp_name'])!='text/x-csv')) {
			echo "<p style='color:red;'>The type of the file is not appropriate: ".mime_content_type($csv_file['tmp_name'])."<br />\n";
			echo "You must provide a file TXT/CSV (<i>scratch pad type</i>), not a file text processing or anything of other.</p>\n";
		}
		else {
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp){
				echo "<p>Impossible to open the file CSV !</p>\n";
				echo "<p><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Click here</a> to start again !</center></p>\n";
			}
			else{

				$fp=fopen($csv_file['tmp_name'],"r");

				//$nb_max_reg=100;

				$nb_reg=0;
				$temoin_erreur='n';

				while(!feof($fp)){
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!="") {
						$ligne=trim($ligne);
						$tab_tmp=explode(";",$ligne);

						$sql="SELECT 1=1 FROM vocabulaire WHERE terme='".addslashes($tab_tmp[0])."' AND terme_corrige='".addslashes($tab_tmp[1])."';";
						//echo "$sql<br />";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)==0) {
							$sql="INSERT INTO vocabulaire SET terme='".addslashes($tab_tmp[0])."', terme_corrige='".addslashes($tab_tmp[1])."';";
							//echo "$sql<br />";
							$insert=mysql_query($sql);
							if($insert) {
								$nb_reg++;
							}
							else {
								echo "<span style='color:red;'><b>Error during insertion of :</b> $ligne</span><br />\n";
								$temoin_erreur='y';
							}
						}
					}
					/*
					if($nb_reg>=$nb_max_reg) {
						echo "<p style='color:red;'>On n'enregistre pas plus de $nb_max_reg appréciations lors d'un import.</p>";
						break;
					}
					*/
				}
				fclose($fp);

				if(($nb_reg>0)&&($temoin_erreur=='n')) {echo "<span style='color:red;'>Importation carried out .</span><br />";}
			}
		}
	}
}

echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
echo add_token_field();

$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : "";

$compteur_nb_vocab=isset($_POST['compteur_nb_vocab']) ? $_POST['compteur_nb_vocab'] : NULL;

if(isset($compteur_nb_vocab)) {
	check_token(false);

	if((isset($NON_PROTECT['terme']))&&(isset($NON_PROTECT['terme_corrige']))) {
		$terme=casse_mot(traitement_magic_quotes(corriger_caracteres($NON_PROTECT['terme'])),'min');
		$terme_corrige=casse_mot(traitement_magic_quotes(corriger_caracteres($NON_PROTECT['terme_corrige'])),'min');

		$chaine_collate="";
		$sql="show full columns from vocabulaire WHERE Field='terme';";
		$res_col=mysql_query($sql);
		if(mysql_num_rows($res_col)>0) {
			$lig_col=mysql_fetch_object($res_col);
			if($lig_col->Collation!='utf8_unicode_ci') {$chaine_collate="COLLATE latin1_bin ";}
		}


		if(($terme!='')&&($terme_corrige!='')) {
			$sql="SELECT 1=1 FROM vocabulaire WHERE terme $chaine_collate='".$terme."' AND terme_corrige $chaine_collate='".$terme_corrige."';";
			//echo "$sql<br />";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				$sql="INSERT INTO vocabulaire SET terme='".$terme."', terme_corrige='".$terme_corrige."';";
				//echo "$sql<br />";
				$insert=mysql_query($sql);
				if($insert) {
					echo "<span style='color:red;'><b>Recording carried out.</span><br />\n";
				}
				else {
					echo "<span style='color:red;'><b>Error during insertion of :</b> $terme &gt; $terme_corrige</span><br />\n";
					$temoin_erreur='y';
				}
			}
			else {
				echo "<span style='color:red;'>The couple <b>$terme</b> and <b>$terme_corrige</b> already exist.</span><br />\n";
			}
		}
	}

	// Validation des suppressions...
	$nb_suppr=0;
	for($i=1;$i<=$compteur_nb_vocab;$i++){
		if(isset($suppr[$i])) {
			$sql="DELETE FROM vocabulaire WHERE id='".$suppr[$i]."';";
			//echo "sql=$sql<br />";
			$del=mysql_query($sql);
			if(!$del) {
				echo "<span style='color:red;'>Error during the removal of the couple n°".$suppr[$i].".</span><br />\n";
			}
			else {
				$nb_suppr++;
			}
		}
	}

	if($nb_suppr>0) {
		echo "<span style='color:red;'>$nb_suppr suppressions carried out.</span><br />\n";
	}
}

echo "<p>Seizure of an expression and its correction &nbsp;:</p>\n";
echo "<blockquote>\n";
echo "<table>\n";
echo "<tr>\n";
echo "<td>\n";
echo "Erroneous formulation &nbsp;: ";
echo "</td>\n";
echo "<td>\n";
echo "<input type='text' name='no_anti_inject_terme' id='no_anti_inject_terme' cols='60' onchange='changement()' />\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td>\n";
echo "Corrected formulation &nbsp;: ";
echo "</td>\n";
echo "<td>\n";
echo "<input type='text' name='no_anti_inject_terme_corrige' id='no_anti_inject_terme_corrige' cols='60' onchange='changement()' />\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</blockquote>\n";


// Recherche du vocabulaire déjà saisi:
$sql="SELECT DISTINCT * FROM vocabulaire ORDER BY terme, terme_corrige;";
//echo "$sql";
$resultat_vocab=mysql_query($sql);
$cpt=1;
if(mysql_num_rows($resultat_vocab)!=0){
	echo "<p>Here the list of the terms of vocabulary and their corrections &nbsp;:</p>\n";
	echo "<blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Vocabulaire'>\n";
	echo "<tr style='text-align:center;'>\n";
	echo "<th>Term</th>\n";
	echo "<th>Term corrigé</th>\n";
	echo "<th>Remove</th>\n";
	echo "</tr>\n";

	$alt=1;
	while($ligne_vocab=mysql_fetch_object($resultat_vocab)){
		$alt=$alt*(-1);
		echo "<tr class='lig$alt' style='text-align:center;'>\n";

		echo "<td>";
		echo stripslashes($ligne_vocab->terme);
		echo "</td>\n";

		echo "<td>";
		echo stripslashes($ligne_vocab->terme_corrige);
		echo "</td>\n";

		echo "<td><input type='checkbox' name='suppr[$cpt]' value='$ligne_vocab->id' /></td>\n";
		echo "</tr>\n";
		$cpt++;
	}
	echo "</table>\n";
	echo "</blockquote>\n";
}

echo "<script type='text/javascript'>
	document.getElementById('no_anti_inject_terme').focus();
</script>\n";

echo "<input type='hidden' name='compteur_nb_vocab' value='$cpt' />\n";

echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";

echo "</form>\n";
echo "<p><br /></p>\n";


echo "<p><i>NOTES&nbsp;:</i></p>\n";
echo "<ul>
	<li>The couples are recorded in small letters to circumvent.</li>
	<li>It is not a question to provide a dictionary, but to announce typing errors which correspond in spite of very to words of the dictionary and can give unhappy results.</li>
	<li>The principle in the page of seizure of the appreciations is as
follows &nbsp;: The seized appreciation is reformatée by replacing the points, commas, semicolons by spaces to seek if the appreciation contains chains resembling those
judged like evil typed or spelled .<br />If a possible error is detected, a message is posted.</li>
</ul>\n";
echo "<p><br /></p>\n";


require("../lib/footer.inc.php");
?>
