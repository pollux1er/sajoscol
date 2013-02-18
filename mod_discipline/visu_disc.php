<?php

/*
* $Id: visu_disc.php 8741 2012-01-08 14:59:31Z crob $
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";
// Begin standart header
$niveau_arbo = 1;

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
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/visu_disc.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Discipline: Accès élève/parent', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("You try to reach the Discipline module which is decontaminated !");
	tentative_intrusion(1, "Attempt at access to the Discipline module which is decontaminated.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Discipline : Access ".$_SESSION['statut'];
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if($_SESSION['statut']=='eleve') {
	if(getSettingValue('visuEleDisc')!='yes') {
		echo "<p style='color:red'>You are not authorized to reach this page.</p>\n";
		tentative_intrusion(1, "Attempt at access to the module Disciplines without being authorized
there.");
		require("../lib/footer.inc.php");
		die();
	}
}
elseif($_SESSION['statut']=='responsable') {
	if(getSettingValue('visuRespDisc')!='yes') {
		echo "<p style='color:red'>You are not authorized to reach this page.</p>\n";
		tentative_intrusion(1, "Attempt at access to the module Disciplines without being authorized
there.");
		require("../lib/footer.inc.php");
		die();
	}
}

echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour à l'accueil' class='back_link'/> Return</a>";

if($_SESSION['statut']=='eleve') {
	$ele_login=$_SESSION['login'];
}
else {
	// Lien de choix de l'élève
	$ele_login=isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL;

	$tab_ele_login=array();
	$tab_enfants=get_enfants_from_resp_login($_SESSION['login'],'avec_classe');
	for($i=0;$i<count($tab_enfants);$i+=2) {
		//echo "\$tab_enfants[$i]=".$tab_enfants[$i]."<br />";
		$tab_ele_login[]=$tab_enfants[$i];
	}

	if((isset($ele_login))&&(!in_array($ele_login,$tab_ele_login))) {
		echo "<p style='color:red'>Attempt at access to the module Disciplines for a pupil of which you
are not responsible.</p>\n";
		tentative_intrusion(1, "Attempt at access to the module Disciplines for a pupil of which it is
not responsible : $ele_login");
		unset($ele_login);
	}

	if(!isset($ele_login)) {
		if(count($tab_ele_login)==1) {
			$ele_login=$tab_ele_login[0];
		}
		else {
			echo "<p>Choose the child of which you wish to consult the incidents&nbsp;:<br />\n";
			for($i=0;$i<count($tab_enfants);$i+=2) {
				echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=".$tab_enfants[$i]."'>".$tab_enfants[$i+1]."</a><br />\n";
			}

			require("../lib/footer.inc.php");
			die();
		}
	}

}
echo "</p>\n";

require_once("../mod_discipline/sanctions_func_lib.php");

$mode="";
$date_debut="";
$date_fin="";
//echo "<p>Tableau des incidents</p>\n";
echo tab_mod_discipline($ele_login,$mode,$date_debut,$date_fin);

require("../lib/footer.inc.php");

?>
