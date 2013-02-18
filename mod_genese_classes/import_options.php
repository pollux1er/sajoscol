<?php
/* $Id: import_options.php 7298 2011-06-22 15:36:15Z crob $ */
/*
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/import_options.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/import_options.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genesis of the classes: Importation options since CSV',
statut='';";
$insert=mysql_query($sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);


//**************** EN-TETE *****************
$titre_page = "Genesis classifies: Importation CSV of the options";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

if((!isset($projet))||($projet=="")) {
	echo "<p class='bold'><a href='index.php'>Return</a></p>\n";

	echo "<p style='color:red'>ERROR: The project is not selected.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'>Return</a> | <a href='".$_SERVER['PHP_SELF']."?projet=$projet'>Another importation</a>";
//echo "</div>\n";

$afficher_listes=isset($_POST['afficher_listes']) ? $_POST['afficher_listes'] : (isset($_GET['afficher_listes']) ? $_GET['afficher_listes'] : NULL);

$action=isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : NULL);

if($action=="upload_file") {
	echo "</p>\n";

	echo "<h2>Project $projet</h2>\n";

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	// Le nom est ok. On ouvre le fichier
	$fp=fopen($csv_file['tmp_name'],"r");

	if(!$fp) {
		// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
		echo "<p>Impossible to open the file CSV !</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."?projet=$projet'>Click here</a> to start again !</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	else {

		$sql="DELETE FROM gc_eleves_options WHERE projet='$projet';";
		$del=mysql_query($sql);

		$tab_non_option=array('NOM','PRENOM','SEXE','NAISSANCE','LOGIN','ELENOET','ELE_ID','INE','EMAIL','CLASSE');

		// Lecture de la ligne d'entête du CSV
		$ligne=trim(fgets($fp, 4096));
		$tabligne_entete=explode(";",$ligne);

		$tab_options=array();

		$tabligne_entete_inverse=array();
		for($i=0;$i<count($tabligne_entete);$i++) {
			$tabligne_entete_inverse["$tabligne_entete[$i]"]=$i;

			if(!in_array($tabligne_entete[$i],$tab_non_option)) {


				// VERIFIER AUSSI SI L'OPTION PRéSUMéE EST DANS gc_options
				$sql="SELECT 1=1 FROM gc_options WHERE projet='$projet' AND opt='".$tabligne_entete[$i]."';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					if(!in_array($tabligne_entete[$i],$tab_options)) {
						$tab_options[]=$tabligne_entete[$i];
					}
				}
			}
		}

		$cle="";
		if(in_array('LOGIN',$tabligne_entete)) {
			$cle='login';
		}
		elseif(in_array('ELENOET',$tabligne_entete)) {
			$cle='elenoet';
		}
		elseif(in_array('ELE_ID',$tabligne_entete)) {
			$cle='ele_id';
		}
		elseif(in_array('INE',$tabligne_entete)) {
			$cle='no_gep';
		}

		if($cle=="") {
			echo "<p style='color:red'>ERROR: The file does not contain any keys LOGIN, ELENOET, ELE_ID ou INE.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<table class='boireaus' border='1' summary='Imported options'>\n";
		echo "<tr><th>Login</th><th>Options</th></tr>\n";
		$val_login_precedent="";
		$alt=1;
		$cpt=0;
		$nat_num = array();
		while (!feof($fp)) {
			$ligne = fgets($fp, 4096);
			if(trim($ligne)!="") {
				$tabligne=explode(";",$ligne);

				if($cle=='no_gep') {
					$valeur_cle=$tabligne[$tabligne_entete_inverse['INE']];
				}
				else {
					$valeur_cle=$tabligne[$tabligne_entete_inverse[strtoupper($cle)]];
				}

				$val_login="";
				// Si la clé n'est pas LOGIN, il faut récupérer le login d'après la table eleves... A FAIRE
				if($cle=="") {
					$sql="SELECT 1=1 FROM eleves WHERE login='".$valeur_cle."';";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)==1) {
						$val_login=$valeur_cle;
					}
					elseif(mysql_num_rows($res)==0) {
						echo "<tr><td colspan='2'>\n";
						echo "<span style='color:red'>No recording was found in the table 'eleves' for the login corresponding to the line '<span style='color:blue'>$ligne</span>'</span><br />\n";
						echo "</td></tr>\n";
					}
					else {
						echo "<tr><td colspan='2'>\n";
						echo "<span style='color:red'>Several recording were found in the table 'eleves' for the login '<span style='color:blue'>$valeur_cle</span>' corresponding to the line '<span style='color:blue'>$ligne</span>'.It is a large anomaly.</span><br />\n";
						echo "</td></tr>\n";
					}
				}
				else {
					//$sql="SELECT login FROM eleves WHERE ".strtolower($tabligne_entete_inverse["$cle"])."='".$valeur_cle."';";
					$sql="SELECT login FROM eleves WHERE ".$cle."='".$valeur_cle."';";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)==1) {
						$lig_tmp=mysql_fetch_object($res);
						$val_login=$lig_tmp->login;
					}
					elseif(mysql_num_rows($res)==0) {
						echo "<tr><td colspan='2'>\n";
						echo "<span style='color:red'>No recording was found in the table 'eleves' for the login corresponding to the line '<span style='color:blue'>$ligne</span>'</span><br />\n";
						echo "</td></tr>\n";
					}
					else {
						echo "<tr><td colspan='2'>\n";
						echo "<span style='color:red'>Several values were found in the table 'eleves' for the login corresponding to the line '<span style='color:blue'>$ligne</span>'</span><br />\n";
						echo "</td></tr>\n";
					}
				}

				if($val_login!="") {
					if($val_login!=$val_login_precedent) {
						if($cpt>0) {
							echo "</td></tr>\n";
						}
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'><td style='text-align:left;'><b>$val_login</b>&nbsp;:</td><td style='text-align:left;'>\n";
					}
					$chaine_opt_eleve="";
					for($i=0;$i<count($tab_options);$i++) {
						if($tabligne[$tabligne_entete_inverse["$tab_options[$i]"]]==1) {

							echo $tab_options[$i]." ";
							$chaine_opt_eleve.="|".$tab_options[$i];
							//$sql="INSERT INTO gc_eleves_options SET projet='$projet', login='$val_login', opt='".$tab_options[$i]."';";
							//echo "$sql<br />\n";
							//$res=mysql_query($sql);
						}
					}
					if($chaine_opt_eleve!="") {
						$chaine_opt_eleve.="|";
						$sql="INSERT INTO gc_eleves_options SET projet='$projet', login='$val_login', liste_opt='".$chaine_opt_eleve."';";
						//echo "$sql<br />\n";
						$res=mysql_query($sql);
					}
				}

				$val_login_precedent=$val_login;
				$cpt++;
			}
		}
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "<p>Completed importation.</p>\n";
	}
}
else {
	echo "</p>\n";

	echo "<h2>Project $projet</h2>\n";

	echo "<p>Please provide a file CSV to the format... adapted... to import the future options of the pupils.</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<input type='hidden' name='projet' value='$projet' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";
	echo "<p><input type='submit' value='Validate' />\n";
	echo "</form>\n";

	echo "<p><i>NOTES&nbsp;:</i></p>\n";
	echo "<ul>\n";
	echo "<li><p>The options seized beforehand for this project will be lost.<p></li>\n";
	echo "<li><p>The format of the CSV could be for example&nbsp;:<br />NAME;FIRST NAME;BIRTH;ELENOET;CLASS;AGL1;AGL2;ALL1;ALL2;ATHLE;DECP3;ESP2;LATIN;Redoubling;Departure<br />In this example, ELENOET will be the key to identify the student.<br />The other valid keys are LOGIN, ELE_ID, INE.<br />The names of the columns must coincide with the matter names in Gepi.</p><p>Simplest to obtain this file consists in following the stages in the
order.<br />At the time of stage 2 ' To list the current options of the pupils ',
a file CSV with the good format is generated.</p></li>\n";
	echo "</ul>\n";
}


require("../lib/footer.inc.php");
?>
