<?php
/*
 * $Id: synchro_mail.php 8183 2011-09-10 11:52:36Z crob $
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

$sql="SELECT 1=1 FROM droits WHERE id='/eleves/synchro_mail.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/eleves/synchro_mail.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Synchronisation des mail élèves',
statut='';";
$insert=mysql_query($sql);
updateOnline($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


if(!isset($msg)){
	$msg="";
}

$suppr_infos_actions_diff_mail=isset($_GET['suppr_infos_actions_diff_mail']) ? $_GET['suppr_infos_actions_diff_mail'] : "n";

if((isset($_GET['synchroniser']))&&($_GET['synchroniser']=='y')) {
	check_token();

	$sql="SELECT u.*, e.email as e_email FROM utilisateurs u, eleves e WHERE e.login=u.login AND u.statut='eleve' AND u.email!=e.email ORDER BY e.nom, e.prenom;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg="All the addresses mall students are already synchronized between the tables 'eleves' and 'utilisateurs'.<br />\n";
	}
	else {
		$cpt=0;
		$erreur=0;
		if(getSettingValue('mode_email_ele')=='sconet') {
			while($lig=mysql_fetch_object($res)) {
				$sql="UPDATE utilisateurs SET email='$lig->e_email' WHERE login='$lig->login' AND statut='eleve';";
				$update=mysql_query($sql);updateOnline($sql);
				if($update) {
					$cpt++;
				}
				else {
					$erreur++;
				}
			}
		}
		elseif(getSettingValue('mode_email_ele')=='mon_compte') {
			while($lig=mysql_fetch_object($res)) {
				$sql="UPDATE eleves SET email='$lig->email' WHERE login='$lig->login';";
				$update=mysql_query($sql);updateOnline($sql);
				if($update) {
					$cpt++;
				}
				else {
					$erreur++;
				}
			}
		}

		if($cpt==0) {
			$msg="No address was updated.<br />";
		}
		elseif($cpt==1) {
			$msg="An address was updated.<br />";
			$suppr_infos_actions_diff_mail="y";
		}
		else {
			$msg="$cpt addresses were updated.<br />";
			$suppr_infos_actions_diff_mail="y";
		}

		if($erreur==1) {
			$msg.="An error occurred.<br />";
		}
		elseif($erreur>1) {
			$msg.="$erreur errors occurred.<br />";
		}
	}
}

//if((isset($_GET['suppr_infos_actions_diff_mail']))&&($_GET['suppr_infos_actions_diff_mail']=='y')) {
if($suppr_infos_actions_diff_mail=='y') {
	check_token();

	$sql="select * from infos_actions where titre like 'Adresse mail non synchro pour%' and description like '%adresse email renseignée par l%élève%';";
	$test_infos_actions=mysql_query($sql);
	if(mysql_num_rows($test_infos_actions)>0) {
		$sql="delete from infos_actions where titre like 'Adresse mail non synchro pour%' and description like '%adresse email renseignée par l%élève%';";
		$del=mysql_query($sql);updateOnline($sql);
		if(!$del) {
			$msg.="ERROR during the suppression of the descriptions of difference of mail on page of home.<br />\n";
		}
		else {
			$msg.="Suppression of the descriptions of difference of mail on page of home carried out.<br />\n";
		}
	}
	else {
		$msg.="No description existed on page of home.<br />\n";
	}
}

//**************** EN-TETE *******************************
$titre_page = "Synchronization of the addresses mail students";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ***************************

//debug_var();

if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0){
		echo "<p>A conversion of the responsibles  data is necessary.</p>\n";
		echo "<p>Follow this link: <a href='conversion.php'>CONVERT</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>A conversion of the students/responsibles data is necessary.</p>\n";
		echo "<p>Follow this link: <a href='conversion.php'>CONVERT</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p>A conversion of the students/responsibles data is necessary.</p>\n";
			echo "<p>Follow this link: <a href='conversion.php'>CONVERT</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}

?>
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>
<?php
	$sql="select * from infos_actions where titre like 'Adresse mail non synchro pour%' and description like '%adresse email renseignée par l%élève%';";
	$test_infos_actions=mysql_query($sql);
	if(mysql_num_rows($test_infos_actions)>0) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?suppr_infos_actions_diff_mail=y".add_token_in_url()."'>Remove the descriptions of differences on page of home</a>";
	}
	echo "</p>\n";

	$sql="SELECT u.*, e.email as e_email FROM utilisateurs u, eleves e WHERE e.login=u.login AND u.statut='eleve' AND u.email!=e.email ORDER BY e.nom, e.prenom;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>All the addresses mall students are synchronized between the tables 'eleves' et 'utilisateurs'.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>".mysql_num_rows($res)." addresses mall students differ between the tables 'eleves' et 'utilisateurs'.</p>\n";

	echo "<table class='boireaus' summary='Table of differences'>\n";
	echo "<tr>\n";
	echo "<th>Nom</th>\n";
	echo "<th>Prenom</th>\n";
	echo "<th>Email utilisateur<br />(<i>Manage my account</i>)</th>\n";
	echo "<th>Email élève<br />(<i>Sconet,...</i>)</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td><a href='modify_ele.php?eleve_login=$lig->login'>$lig->nom</a></td>\n";
		echo "<td>$lig->prenom</td>\n";
		echo "<td>$lig->email</td>\n";
		echo "<td>$lig->e_email</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo "<p>The parameter setting of synchronization is currently&nbsp;:".getSettingValue('mode_email_ele')."</p>\n";

	if(getSettingValue('mode_email_ele')=='sconet') {
		echo "<p>To update the email of the accounts of users according to the Sconet values , <a href='".$_SERVER['PHP_SELF']."?synchroniser=y".add_token_in_url()."'>click here</a>.</p>\n";
	}
	elseif(getSettingValue('mode_email_ele')=='mon_compte') {
		echo "<p>To update the email of the students according to the values of the accounts of users, <a href='".$_SERVER['PHP_SELF']."?synchroniser=y".add_token_in_url()."'>click here</a>.</p>\n";
	}
	elseif(getSettingValue('mode_email_ele')=='sso') {
		echo "<p style='color:red'>Not yet managed situation.</p>\n";
	}

	if($_SESSION['statut']=='administrateur') {
		echo "<p>This parameter setting can be modified in <a href='../gestion/param_gen.php#mode_email_ele'>General configuration</a></p>\n";
	}

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
?>
