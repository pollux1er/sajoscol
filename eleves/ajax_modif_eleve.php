<?php

/*
 * $Id: ajax_modif_eleve.php 6162 2010-12-16 20:26:17Z crob $
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

@set_time_limit(0);

// Initialisations files
require_once("../lib/initialisations.inc.php");
function saveAction($sql) {
	
	$filename = '../responsables/responsable.txt';
	$somecontent = $sql.";\n";

	// Assurons nous que le fichier est accessible en écriture
	if (is_writable($filename)) {

		if (!$handle = fopen($filename, 'a')) {
			 echo "Impossible d'ouvrir le fichier ($filename)";
			 exit;
		}

		// Ecrivons quelque chose dans notre fichier.
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Impossible d'écrire dans le fichier ($filename)";
			exit;
		}

		//echo "L'écriture de ($somecontent) dans le fichier ($filename) a réussi";

		fclose($handle);

	} else {
		echo "Le fichier $filename n'est pas accessible en écriture.";
	}
}

function updateOnline($sql) {
	$hostname = "173.254.25.235";
	$username = "sajoscol_gepi";
	$password = ";?5tvu45l-Lu";
	$databasename = "sajoscol_appli";
	$con = mysql_pconnect("$hostname", "$username", "$password");
	if (!$con) {
		saveAction($sql); //die('Could not connect: ' . mysql_error());
	}
	else { 
		//echo "Connexion reussi!"; 
		if(mysql_select_db($databasename, $con)) { 
			if (mysql_query($sql)) { 
				echo "<script type='text/javascript'>alert('Successly updated online!');</script>"; 
			} else {
				echo mysql_error();
			}
		}
	}
	
}

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

//INSERT INTO droits SET id='/eleves/ajax_modif_eleve.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Enregistrement des modifications élève',statut='';
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

check_token();

header('Content-Type: text/html; charset=ISO-8859-15');

/*
$signalement_login_eleve=isset($_POST['signalement_login_eleve']) ? $_POST['signalement_login_eleve'] : "";
$signalement_id_groupe=isset($_POST['signalement_id_groupe']) ? $_POST['signalement_id_groupe'] : "";
$signalement_message=isset($_POST['signalement_message']) ? $_POST['signalement_message'] : "";
*/

$mode=isset($_GET['mode']) ? $_GET['mode'] : "";
$login_eleve=isset($_GET['login_eleve']) ? $_GET['login_eleve'] : "";
$regime_eleve=isset($_GET['regime_eleve']) ? $_GET['regime_eleve'] : "";

/*
echo "\$mode=$mode<br />";
echo "\$login_eleve=$login_eleve<br />";
echo "\$regime_eleve=$regime_eleve<br />";
*/

if($mode=='changer_regime') {
	$tab_regime=array('d/p', 'ext.', 'int.','i-e');
	if(($login_eleve=='')||($regime_eleve=='')||(!in_array($regime_eleve,$tab_regime))) {
		echo "<span style='color:red'> KO</span>";
		return false;
		die();
	}

	$sql="SELECT 1=1 FROM j_eleves_regime WHERE login='$login_eleve';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0) {
		$sql="INSERT INTO j_eleves_regime SET regime='$regime_eleve', login='$login_eleve';";
	}
	else {
		$sql="UPDATE j_eleves_regime SET regime='$regime_eleve' WHERE login='$login_eleve';";
	}
	$res=mysql_query($sql);updateOnline($sql);

	if($res) {
		echo "<span style='color:green;'>$regime_eleve</span>";
	}
	else {
		echo "<span style='color:red;'>ERREUR</span>";
	}
}

?>
