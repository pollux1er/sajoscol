<?php

/*
 * $Id: definir_autres_sanctions.php 7138 2011-06-05 17:37:14Z crob $
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir types sanctions', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir types sanctions', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("You try  to reach the Discipline module which is decontaminated !");
	tentative_intrusion(1, "Attempt at access to the Discipline module which is decontaminated.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$msg="";

$suppr_nature=isset($_POST['suppr_nature']) ? $_POST['suppr_nature'] : NULL;
$nature=isset($_POST['nature']) ? $_POST['nature'] : NULL;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

if(isset($suppr_nature)) {
	check_token();

	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_nature[$i])) {
			$sql="SELECT 1=1 FROM s_autres_sanctions WHERE id_nature='$suppr_nature[$i]';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				$msg.="It is not possible to remove the type of sanction n°".$suppr_nature[$i]." because it is associated sanctions already seized for student.<br />\n";
			}
			else {
				$sql="DELETE FROM s_types_sanctions WHERE id_nature='$suppr_nature[$i]';";
				$suppr=mysql_query($sql);
				updateOnline($sql);
				if(!$suppr) {
					$msg.="ERROR during the delection of nature n°".$suppr_nature[$i].".<br />\n";
				}
				else {
					$msg.="delection of nature n°".$suppr_nature[$i].".<br />\n";
				}
			}
		}
	}
}

//if((isset($nature))&&($nature!='')&&(isset($type))&&(($type=='prise')||($type=='demandee'))) {
if(isset($nature)) {
	$a_enregistrer='y';

	check_token();

	$sql="SELECT * FROM s_types_sanctions ORDER BY nature;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$tab_nature=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_nature[]=$lig->nature;
		}

		if(in_array($nature,$tab_nature)) {$a_enregistrer='n';}
	}


	if((isset($nature))&&($nature!='')) {
		if($a_enregistrer=='y') {
			$nature=preg_replace('/(\\\r\\\n)+/',"\r\n",$nature);
			$nature=preg_replace('/(\\\r)+/',"\r",$nature);
			$nature=preg_replace('/(\\\n)+/',"\n",$nature);

			$sql="INSERT INTO s_types_sanctions SET nature='".$nature."';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			updateOnline($sql);
			if(!$res) {
				$msg.="ERROR during the recording of ".$nature."<br />\n";
			}
			else {
				$msg.="Recording of ".$nature."<br />\n";
			}
		}
	}
}

$themessage  = 'Information was modified. Want you to really leave without recording ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Definition of the types of sanctions";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>\n";
echo "</p>\n";

echo "<p>The preset types of sanctions are: Reserve, Exclusion, Work.<br />
The present page is intended to add other types of sanctions (<i>'setting with the pilori', 'scourging with nettles', 'Look at Questions for a champion',... according to the tastes of the establishment as regards various
torments;o</i>).</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
echo add_token_field();

echo "<p class='bold'>Seizure of the types of sanctions&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM s_types_sanctions ORDER BY nature;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p>No additional sanction is yet defined.</p>\n";
}
else {
	echo "<p>Existing sanctions&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Table of the existing sanctions'>\n";
	echo "<tr>\n";
	echo "<th>Nature</th>\n";
	echo "<th>Remove</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		echo "<label for='suppr_nature_$cpt' style='cursor:pointer;'>";
		echo $lig->nature;
		echo "</label>";
		echo "</td>\n";

		echo "<td><input type='checkbox' name='suppr_nature[]' id='suppr_nature_$cpt' value=\"$lig->id_nature\" onchange='changement();' /></td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}

echo "<p>New nature&nbsp;: <input type='text' name='nature' value='' onchange='changement();' />\n";
echo "</p>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";

echo "</blockquote>\n";
echo "</form>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>