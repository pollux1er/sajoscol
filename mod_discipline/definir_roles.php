<?php

/*
 * $Id: definir_roles.php 7138 2011-06-05 17:37:14Z crob $
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

//$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/saisie_qualites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Saisie des qualités', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_qualites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Saisie des qualités', '');;";
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

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// REMARQUE: Le terme de 'qualité' a été remplacé par 'rôle'
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require('sanctions_func_lib.php');

$msg="";

$suppr_qualite=isset($_POST['suppr_qualite']) ? $_POST['suppr_qualite'] : NULL;
$qualite=isset($_POST['qualite']) ? $_POST['qualite'] : NULL;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

if(isset($suppr_qualite)) {
	check_token();

	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_qualite[$i])) {
			$sql="DELETE FROM s_qualites WHERE id='$suppr_qualite[$i]';";
			$suppr=mysql_query($sql);updateOnline($sql);
			if(!$suppr) {
				//$msg.="ERREUR lors de la suppression de la qualité n°".$suppr_qualite[$i].".<br />\n";
				$msg.="ERROR during the suppression of the n° role".$suppr_qualite[$i].".<br />\n";
			}
			else {
				$msg.="Suppression of the n° role".$suppr_qualite[$i].".<br />\n";
			}
		}
	}
}

if((isset($qualite))&&($qualite!='')) {
	$a_enregistrer='y';

	$sql="SELECT qualite FROM s_qualites ORDER BY qualite;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$tab_qualite=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_qualite[]=$lig->qualite;
		}

		if(in_array($qualite,$tab_qualite)) {$a_enregistrer='n';}
	}

	if($a_enregistrer=='y') {
		check_token();

		$qualite=preg_replace('/(\\\r\\\n)+/',"\r\n",$qualite);
		$qualite=preg_replace('/(\\\r)+/',"\r",$qualite);
		$qualite=preg_replace('/(\\\n)+/',"\n",$qualite);

		$sql="INSERT INTO s_qualites SET qualite='".$qualite."';";
		$res=mysql_query($sql);updateOnline($sql);
		if(!$res) {
			$msg.="ERROR during the recording of ".$qualite."<br />\n";
		}
		else {
			$msg.="Recording of ".$qualite."<br />\n";
		}
	}
}

$themessage  = 'Information was modified. Want you to really leave without recording ?';
//**************** EN-TETE *****************
//$titre_page = "Sanctions: Définition des qualités";
$titre_page = "Discipline: Definition of the roles";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
echo add_token_field();

//echo "<p class='bold'>Saisie des qualités dans un incident&nbsp;:</p>\n";
echo "<p class='bold'>Seizure of the roles in an incident&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM s_qualites ORDER BY qualite;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	//echo "<p>Aucune qualité n'est encore définie.</p>\n";
	echo "<p>No role is yet defined.</p>\n";
}
else {
	//echo "<p>Qualités existantes&nbsp;:</p>\n";
	//echo "<table class='boireaus' border='1' summary='Tableau des qualités existantes'>\n";
	echo "<p>Existing roles&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Table of the existing roles'>\n";
	echo "<tr>\n";
	//echo "<th>Qualité</th>\n";
	echo "<th>Role</th>\n";
	echo "<th>Remove</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		echo "<label for='suppr_qualite_$cpt' style='cursor:pointer;'>";
		echo $lig->qualite;
		echo "</label>";
		echo "</td>\n";

		echo "<td><input type='checkbox' name='suppr_qualite[]' id='suppr_qualite_$cpt' value=\"$lig->id\" onchange='changement();' /></td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}
echo "</blockquote>\n";

echo "<p>New quality&nbsp;: <input type='text' name='qualite' value='' onchange='changement();' /></p>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>