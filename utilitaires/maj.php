<?php
/**
 * Mise à jour des bases
 * 
 * $Id: maj.php 8338 2011-09-23 19:57:59Z crob $
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 */

/* This file is part of GEPI.
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

//test version de php
if (version_compare(PHP_VERSION, '5') < 0) {
    die('GEPI nécessite PHP5 pour fonctionner');
}
// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
// cela ici concerne le mot de passe
$variables_non_protegees = 'yes';
$pb_maj = '';

// Initialisations files
/**
 * Fichier d'initialisation
 */
require_once ("../lib/initialisations.inc.php");
/**
 * Fonctions de mise à jour
 */
require_once ("./update_functions.php");


// Resume session
$resultat_session = $session_gepi->security_check();

if (isset ($_POST['submit'])) {
	if (isset ($_POST['login']) && isset ($_POST['no_anti_inject_password'])) {
		$_POST['login'] = strtoupper($_POST['login']);
		$md5password = md5($NON_PROTECT['password']);
		$sql = "SELECT UPPER(login) login, password, prenom, nom, statut FROM utilisateurs WHERE (login = '" . $_POST['login'] . "' and password = '" . $md5password . "' and etat != 'inactif' and statut = 'administrateur')";

		$res_user = sql_query($sql);
		$num_row = sql_count($res_user);

		if ($num_row == 1) {
			$valid = 'yes';
			$resultat_session = "1";
			$_SESSION['login'] = $_POST['login'];
			$_SESSION['statut'] = 'administrateur';
			$_SESSION['etat'] = 'actif';
			$_SESSION['start'] = mysql_result(mysql_query("SELECT now();"),0);
			$sql = "INSERT INTO log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
					'" . $_SESSION['login'] . "',
					'".$_SESSION['start']."',
					'" . session_id() . "',
					'" . $_SERVER['REMOTE_ADDR'] . "',
					'" . $_SERVER['HTTP_USER_AGENT'] . "',
					'" . $_SERVER['HTTP_REFERER'] . "',
					'1',
					'".$_SESSION['start']."' + interval " . getSettingValue('sessionMaxLength') . " minute
				)
			;";
			$res = sql_query($sql);

		} else {
			$message = "Identifier or incorrect password, or you are not an administrator.";
		}
	}
}

//debug_var();

$valid = isset ($_POST["valid"]) ? $_POST["valid"] : 'no';
$force_maj = isset ($_POST["force_maj"]) ? $_POST["force_maj"] : '';

// Numéro de version effective
$version_old = getSettingValue("version");
// Numéro de version RC effective
$versionRc_old = getSettingValue("versionRc");
// Numéro de version Beta effective
$versionBeta_old = getSettingValue("versionBeta");

$rc_old = '';
if ($versionRc_old != '') {
	$rc_old = "-RC" . $versionRc_old;
}
$rc = '';
if ($gepiRcVersion != '') {
	$rc = "-RC" . $gepiRcVersion;
}

$beta_old = '';
if ($versionBeta_old != '') {
	$beta_old = "-beta" . $versionBeta_old;
}
$beta = '';
if ($gepiBetaVersion != '') {
	$beta = "-beta" . $gepiBetaVersion;
}


echo ('
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
		<link rel="stylesheet" href="../style.css" type="text/css" />
		<link rel="stylesheet" href="updates/updates.css" type="text/css" />
		<title>Update of the GEPI database </title>
		<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
		<link rel="icon" type="image/ico" href="../favicon.ico" />
		');

if(isset($style_screen_ajout)){
	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
	}
}

echo ('
	</head>
	<body>
');


if (($resultat_session == '0') and ($valid != 'yes')) {
  
	echo('
		<form action="maj.php" method="post">
			<div class="center">
				<h1 class="grand center">Update of the GEPI database <br />(Access administrator)</h1>
			');

	if (isset ($message)) {
		echo ("<p class='center rouge'>" . $message . "</p>");
	}
	echo('
				<fieldset class="form_identite">
					<legend class="legend_identite">Identify yourself</legend>
					<p>
						<label class="colonne droite" for="login">Identifier</label>
                    	<input type="text" id="login" name="login" size="16" />
                    </p>
					<p>
                    	<label class="colonne droite" for="no_anti_inject_password">Password</label>
                     	<input type="password" id="no_anti_inject_password" name="no_anti_inject_password" size="16" />
                    </p>
					<p class="center"><input type="submit" name="submit" value="Send" style="font-variant: small-caps;" /></p>
				</fieldset>
			</div>
		</form>
	</body>
</html>
');
    
	die();
}

if ((isset ($_SESSION['statut'])) and ($_SESSION['statut'] != 'administrateur')) {
	if(($is_lcs_plugin!='yes')||($login_user!='admin')) {
		echo "<p class='grand center rouge'>Update of the MySql base of GEPI.<br />You do not have the sufficient rights  for access to this page.</p></body></html>";
		die();
	}
}

if (isset ($_POST['maj'])) {
	//check_token();

//if ((isset ($_POST['maj'])) || (($is_lcs_plugin!='yes')&&(isset($login_user))&&($login_user=='admin'))) {
	$pb_maj = '';
	// On commence la mise à jour
	$mess = "Update carried out.<br />(read attentively the result of the update, in bottom of this page)";
	$result = '';
	$result_inter = '';


        // Remise à zéro de la table des droits d'accès
        require 'updates/access_rights.inc.php';


	if (($force_maj == 'yes') or (quelle_maj("1.5.0"))) {
            require 'updates/144_to_150.inc.php';
	}


	if (($force_maj == 'yes') or (quelle_maj("1.5.1"))) {
            require 'updates/150_to_151.inc.php';
	}


	if (($force_maj == 'yes') or (quelle_maj("1.5.2"))) {
            require 'updates/151_to_152.inc.php';
	}

	if (($force_maj == 'yes') or (quelle_maj("1.5.3"))) {
            require 'updates/152_to_153.inc.php';
	}

	if (($force_maj == 'yes') or (quelle_maj("1.5.3.1"))) {
            require 'updates/153_to_1531.inc.php';
	}

	if (($force_maj == 'yes') or (quelle_maj("1.5.4"))) {
            require 'updates/1531_to_154.inc.php';
	}

	if (($force_maj == 'yes') or (quelle_maj("1.5.5"))) {
            require 'updates/154_to_155.inc.php';
	}

	// Mise à jour du numéro de version
	saveSetting("version", $gepiVersion);
	saveSetting("versionRc", $gepiRcVersion);
	saveSetting("versionBeta", $gepiBetaVersion);
	saveSetting("pb_maj", $pb_maj);
}


// Load settings
if (!loadSettings()) {
	die("Error loading settings");
}

// Numéro de version effective
$version_old = getSettingValue("version");
// Numéro de version RC effective
$versionRc_old = getSettingValue("versionRc");
// Numéro de version beta effective
$versionBeta_old = getSettingValue("versionBeta");

$rc_old = '';
if ($versionRc_old != '') {
	$rc_old = "-RC" . $versionRc_old;
}
$rc = '';
if ($gepiRcVersion != '') {
	$rc = "-RC" . $gepiRcVersion;
}

$beta_old = '';
if ($versionBeta_old != '') {
	$beta_old = "-beta" . $versionBeta_old;
}
$beta = '';
if ($gepiBetaVersion != '') {
	$beta = "-beta" . $gepiBetaVersion;
}

// Pb de mise à jour lors de la dernière mise à jour
$pb_maj_bd = getSettingValue("pb_maj");

if (isset ($mess)) {
	echo "<p class='grand center rouge'>" . $mess . "</p>";
}
echo "<h1 class='grand center'>Update of the MySql database of GEPI</h1>";

echo "<hr /><p class='grand center ecarte'>Current number of version of the MySql base : GEPI " . $version_old . $rc_old . $beta_old . "</p>";
echo "<hr />";
// Mise à jour de la base de donnée

if ($pb_maj_bd != 'yes') {
	if (test_maj()) {
		echo "<h2 class='grand center'>Update of the database towards GEPI version  " . $gepiVersion . $rc . $beta . "</h3>";
		if (isset ($_SESSION['statut'])) {
			echo "<p class='center'>It is highly advised to make a backup of the MySql base before proceeding to the update</p>";
			echo "<form enctype=\"multipart/form-data\" action=\"../gestion/accueil_sauve.php\" method='post' name='formulaire'><p class='center'>";
			//echo add_token_field();
			if (getSettingValue("mode_sauvegarde") == "mysqldump") {
				echo "<input type='hidden' name='action' value='system_dump' />";
			} else {
				echo "<input type='hidden' name='action' value='dump' />";
			}
			echo "<input type=\"submit\" value=\"Launch a backup of the data base\" /></p></form>";
		}
		echo "<p class='center'>Notice : procedure of update towards the version <strong>GEPI " . $gepiVersion . $rc . $beta . "</strong> est utilisable à partir d'une version GEPI 1.2 ou plus récente.</p>";
		echo "<form action=\"maj.php\" method=\"post\">";
		//echo add_token_field();
		echo "<p class='rouge center'><strong>CAUTION : Your database does not seem to be up to date.";
		if ($version_old != '')
		echo " Number of version of the data base : GEPI " . $version_old . $rc_old . $beta_old;
		echo "</strong><br />";
		echo "Click on the following button  to carry out the update towards the version <strong>GEPI " . $gepiVersion . $rc . $beta . "</strong>";
		echo "<p class='center'><span class='center'><input type='submit' value='Update' /></span>";
		echo "<input type='hidden' name='maj' value='yes' />";
		echo "<input type='hidden' name='valid' value='$valid' /></p>";
		echo "</form>";
	} else {
		echo "<h2 class='grand center'>Update of the database</h2>";
		echo "<p class='center'><strong>Your database is up to date. You do not have an update to carry out.</strong></p>";
		if(isset($_SESSION['gepi_alea'])) {
			echo "<p class='grand center'><strong><a href='../gestion/index.php#maj'>Return</a></strong></p>";
		}
		else {
			echo "<p class='grand center'><strong><a href='../logout.php'>Reconnect</a><br />after an update</strong></p>";
		}
		echo "<form action=\"maj.php\" method=\"post\">";
		//echo add_token_field();
		echo "<p class='center'><strong>Nevertheless, you can force the update. This procedure, although without risk, is useful only in certain cases .</strong><br />";
		echo "Click on the button Next to carry out the forced update towards the version <strong>GEPI " . $gepiVersion . $rc . $beta . "</strong></p>";
		echo "<p class='center'><input type='submit' value='Force the update' />";
		echo "<input type='hidden' name='maj' value='yes' />";
		echo "<input type='hidden' name='force_maj' value='yes' />";
		echo "<input type='hidden' name='valid' value='$valid' /></p>";
		echo "</form>";
	}
} else {
	echo "<h3 class='center'>Update of the data base</h3>";
	echo "<p class='rouge'><strong>One or more errors were met during last update of the data base</strong></p>";
	echo "<form action=\"maj.php\" method=\"post\">";
	//echo add_token_field();
	echo "<p><strong>If you think that you have solved the problems involving these errors, you can try a new update</strong>";
	echo " by clicking on the button Next to carry out the update towards the version <strong>GEPI " . $gepiVersion . $rc . $beta . "</strong>.</p>";
	echo "<p class='center'><input type='submit' value='Try a new update' />";
	echo "<input type='hidden' name='maj' value='yes' />";
	echo "<input type='hidden' name='force_maj' value='yes' />";
	echo "<input type='hidden' name='valid' value='$valid' /></p>";
	echo "</form>";
}
echo "<hr />";
if (isset ($result)) {
	//echo "<table style='width:80%; margin:0 auto;' border=\"1\" cellpadding=\"5\" cellspacing=\"1\" summary='Résultat de mise à jour'><tr><td><h2 style ='text-align:center'>Résultat de la mise à jour</h2>";
    echo "<div class='cadreMaJ'>";
	echo "<h2 class='center'>Résultat de la mise à jour</h2>";
	if(!getSettingValue('conv_new_resp_table')){
		$sql="SELECT 1=1 FROM responsables";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p class='rouge'><strong>CAUTION:</strong></p>\n";
			echo "<blockquote>\n";
			echo "<p class='center'>A conversion of the data responsibles is necessary.</p>\n";
			echo "<p class='center'>Follow this link: <a href='../responsables/conversion.php'> CONVERT</a></p>\n";
			echo "<p class='center'>You can nevertheless take time to read attentively information of update below.</p>\n";
			echo "</blockquote>\n";
		}
	}

	echo $result;
	//echo "</td></tr></table>";
	echo '</div>';
}
?>
</body></html>
