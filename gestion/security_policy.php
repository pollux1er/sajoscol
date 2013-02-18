<?php
/*
 * $Id: security_policy.php 6675 2011-03-22 16:57:28Z crob $
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

// Enregistrement des données postées

if (isset($_POST) and !empty($_POST)) {
	check_token();

	// Envoyer un email à l'administrateur systématiquement
	if (isset($_POST['security_alert_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
	if (!saveSetting(("security_alert_email_admin"), $reg)) {
		$msg = "Error during the recording of security_alert_email_admin !";
	}

	// Niveau minimal pour l'envoi du mail
	if (isset($_POST['security_alert_email_min_level'])) {
		$reg = $_POST['security_alert_email_min_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert_email_min_level"), $reg)) {
			$msg = "Error during the recording of security_alert_email_min_level !";
		}
	}

	// Niveau d'alerte 1
	
	// Utilisateur sans antécédent
	
	// Seuil
	if (isset($_POST['security_alert1_normal_cumulated_level'])) {
		$reg = $_POST['security_alert1_normal_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert1_normal_cumulated_level"), $reg)) {
			$msg = "Error during the recording of security_alert1_normal_cumulated_level !";
		}
	}

	// Envoyer un email à l'administrateur
	if (isset($_POST['security_alert1_normal_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_normal_email_admin"), $reg)) {
        $msg = "Error during the recording of security_alert1_normal_email_admin !";
    }

	// Désactiver le compte de l'utilisateur
	if (isset($_POST['security_alert1_normal_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_normal_block_user"), $reg)) {
        $msg = "Error during the recording of security_alert1_normal_block_user !";
    }

	// Utilisateur surveillé
	
	// Seuil
	if (isset($_POST['security_alert1_probation_cumulated_level'])) {
		$reg = $_POST['security_alert1_probation_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert1_probation_cumulated_level"), $reg)) {
			$msg = "Error during the recording of security_alert1_probation_cumulated_level !";
		}
	}

	// Envoyer un email à l'administrateur
	if (isset($_POST['security_alert1_probation_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_probation_email_admin"), $reg)) {
        $msg = "Error during the recording of security_alert1_probation_email_admin !";
    }

	// Désactiver le compte de l'utilisateur
	if (isset($_POST['security_alert1_probation_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_probation_block_user"), $reg)) {
        $msg = "Error during the recording of security_alert1_probation_block_user !";
    }

	// Niveau d'alerte 2
	
	// Utilisateur sans antécédent
	
	// Seuil
	if (isset($_POST['security_alert2_normal_cumulated_level'])) {
		$reg = $_POST['security_alert2_normal_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert2_normal_cumulated_level"), $reg)) {
			$msg = "Error during the recording of security_alert2_normal_cumulated_level !";
		}
	}
	
	// Envoyer un email à l'administrateur
	if (isset($_POST['security_alert2_normal_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_normal_email_admin"), $reg)) {
        $msg = "Error during the recording of security_alert2_normal_email_admin !";
    }

	// Désactiver le compte de l'utilisateur
	if (isset($_POST['security_alert2_normal_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_normal_block_user"), $reg)) {
        $msg = "Error during the recording of security_alert2_normal_block_user !";
    }

	// Utilisateur surveillé
	
	// Seuil
	if (isset($_POST['security_alert2_probation_cumulated_level'])) {
		$reg = $_POST['security_alert2_probation_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert2_probation_cumulated_level"), $reg)) {
			$msg = "Error during the recording of security_alert2_probation_cumulated_level !";
		}
	}
	
	// Envoyer un email à l'administrateur
	if (isset($_POST['security_alert2_probation_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_probation_email_admin"), $reg)) {
        $msg = "Error during the recording of security_alert2_probation_email_admin !";
    }

	// Désactiver le compte de l'utilisateur
	if (isset($_POST['security_alert2_probation_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_probation_block_user"), $reg)) {
        $msg = "Error during the recording of security_alert2_probation_block_user !";
    }

	if (empty($msg)) {
		$msg = "The data were recorded.";
	}

}

//echo "\$filtrage_html=$filtrage_html<br />";
if (isset($_POST['filtrage_html'])) {
	check_token();
	if(($_POST['filtrage_html']=='inputfilter')||
		($_POST['filtrage_html']=='htmlpurifier')||
		($_POST['filtrage_html']=='pas_de_filtrage_html')) {

		if (!saveSetting(("filtrage_html"), $_POST['filtrage_html'])) {
			$msg = "Error during recording of filtrage_html !";
		}
	}

	if (isset($_POST['utiliser_no_php_in_img'])) {
		if (!saveSetting(("utiliser_no_php_in_img"), 'y')) {
			$msg = "Error during recording of utiliser_no_php_in_img !";
		}
	}
	else {
		if (!saveSetting(("utiliser_no_php_in_img"), 'n')) {
			$msg = "Error during recording of utiliser_no_php_in_img !";
		}
	}

	$utiliser_no_php_in_img=getSettingValue('utiliser_no_php_in_img');


	if (isset($_POST['csrf_mode'])) {
		if (!saveSetting(("csrf_mode"), $_POST['csrf_mode'])) {
			$msg = "Error during recording of csrf_mode !";
		}
		else {
			$sql="SELECT * FROM infos_actions WHERE titre='Paramétrage csrf_mode requis';";
			$res_test=mysql_query($sql);
			if(mysql_num_rows($res_test)>0) {
				while($lig_ia=mysql_fetch_object($res_test)) {
					$sql="DELETE FROM infos_actions_destinataires WHERE id_info='$lig_ia->id';";
					$del=mysql_query($sql);
					if($del) {
						$sql="DELETE FROM infos_actions WHERE id='$lig_ia->id';";
						$del=mysql_query($sql);
					}
				}
			}
		}
	}

}


// Fin : if isset($_POST)

$htmlpurifier_autorise='y';
$tab_version_php=explode(".",phpversion());
if($tab_version_php[0]==4) {
	$htmlpurifier_autorise='n';
}
elseif(($tab_version_php[0]==5)&&($tab_version_php[1]==0)&&($tab_version_php[2]<5)) {
	$htmlpurifier_autorise='n';
}

$filtrage_html=getSettingValue('filtrage_html');
if(($filtrage_html=='htmlpurifier')&&($htmlpurifier_autorise=='n')) {
	saveSetting(("filtrage_html"), 'inputfilter');
	$filtrage_html='inputfilter';
}

//echo "\$filtrage_html=$filtrage_html<br />";
if(($filtrage_html!='inputfilter')&&
	($filtrage_html!='htmlpurifier')&&
	($filtrage_html!='pas_de_filtrage_html')) {
	saveSetting(("filtrage_html"), 'htmlpurifier');

	$filtrage_html=getSettingValue('filtrage_html');
}
//echo "\$filtrage_html=$filtrage_html<br />";

//**************** EN-TETE *********************
$titre_page = "Policy of secutity";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

//on récupère le chemin de la page d'appel pour en faire le lien de retour
if(isset($_SERVER['HTTP_REFERER'])) {
	$url_retour = parse_url($_SERVER['HTTP_REFERER']);

	if($_SERVER['PHP_SELF']==$url_retour['path']) {
		$url_retour['path']='index.php#security_policy';
	}
}
else {
	$url_retour['path']='index.php#security_policy';
}
/*
foreach($url_retour as $key => $value) {
	echo "\$url_retour['$key']=$value<br />";
}
debug_var();
//$_SERVER['PHP_SELF']
*/

echo "<p class='bold'><a href='".$url_retour['path']."'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>\n";

echo "<form action='security_policy.php' method='post'>\n";
echo add_token_field();
echo "<center><input type='submit' value='Save' /></center>\n";

// Gestion des tentatives d'intrusion
echo "<h2>Management of the attempts of intrusion</h2>\n";
echo "<div style='margin-left:3em;'>\n";
	
	// Options générales
	echo "<h3>Generals Options </h3>\n";
	echo "<div style='margin-left:3em;'>\n";
		echo "<input type='checkbox' name='security_alert_email_admin' value='yes'";
		if (getSettingValue("security_alert_email_admin") == "yes") echo " CHECKED";
		echo " />\n";
		echo " Systematically send an email to the administrator during attempt of intrusion.<br/>\n";
		echo "Minimal level of gravity for the sending of the mall : ";
		echo "<select name='security_alert_email_min_level' size='1'>\n";
		for ($i = 1; $i <= 3;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert_email_min_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
	echo "</div>\n";

	// Seuils d'alerte et actions à entreprendre
	echo "<h3>Thresholds of alarms</h3>\n";
	echo "<div style='margin-left:3em;'>\n";
		echo "<p>You can define two thresholds of alarm and their associated actions. These thresholds apply to the attempts at intrusion carried out by the
users of Gepi. Each attempt has a level of gravity from 1 to 3; the thresholds correspond to the cumul of these levels of gravity for the same user.</p>\n";
		echo "<p>A user can be placed in observation by the administrator, with distinct thresholds of alarm. That makes it possible to lay down a more restrictive policy in caset of repetition.</p>\n";
		
		echo "<table class='normal' summary=\"Thresholds of alarm\">\n";
		echo "<tr>\n";
		echo "<th>Threshold</th>\n";
		echo "<th>User without antecedent</th>\n";
		echo "<th>Supervised user</th>\n";
		echo "</tr>\n";
		
		// Niveau d'alerte 1
		echo "<tr>\n";
		echo "<td>\n";
		echo "<p>Threshold 1</p>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur sans antécédent
		echo "Cumulated level : ";
		echo "<select name='security_alert1_normal_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert1_normal_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert1_normal_email_admin' value='yes'";
		if (getSettingValue("security_alert1_normal_email_admin") == "yes") echo " CHECKED";
		echo " /> Send an email to the administrator<br/>\n";
		echo "<input type='checkbox' name='security_alert1_normal_block_user' value='yes'";
		if (getSettingValue("security_alert1_normal_block_user") == "yes") echo " CHECKED";
		echo " />Deactivate the account of the user<br/>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur en observation
		echo "Cumulated Level : ";
		echo "<select name='security_alert1_probation_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert1_probation_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert1_probation_email_admin' value='yes'";
		if (getSettingValue("security_alert1_probation_email_admin") == "yes") echo " CHECKED";
		echo " /> Send an email to the administrator<br/>\n";
		echo "<input type='checkbox' name='security_alert1_probation_block_user' value='yes'";
		if (getSettingValue("security_alert1_probation_block_user") == "yes") echo " CHECKED";
		echo " /> Deactivate the account of the user<br/>\n";
		echo "</td>\n";
		echo "</tr>\n";
		
		// Niveau d'alerte 2
		echo "<tr>\n";
		echo "<td>\n";
		echo "<p>Seuil 2</p>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur sans antécédent
		echo "Cumulated level : ";
		echo "<select name='security_alert2_normal_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert2_normal_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert2_normal_email_admin' value='yes'";
		if (getSettingValue("security_alert2_normal_email_admin") == "yes") echo " CHECKED";
		echo " /> Send an email to the administrator<br/>\n";
		echo "<input type='checkbox' name='security_alert2_normal_block_user' value='yes'";
		if (getSettingValue("security_alert2_normal_block_user") == "yes") echo " CHECKED";
		echo " /> Deactivate the account of the user<br/>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur en observation
		echo "Cumulated level : ";
		echo "<select name='security_alert2_probation_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert2_probation_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert2_probation_email_admin' value='yes'";
		if (getSettingValue("security_alert2_probation_email_admin") == "yes") echo " CHECKED";
		echo " /> Send an email to the administrator<br/>\n";
		echo "<input type='checkbox' name='security_alert2_probation_block_user' value='yes'";
		if (getSettingValue("security_alert2_probation_block_user") == "yes") echo " CHECKED";
		echo " /> Deactivate the account of the user<br/>\n";
		echo "</td>\n";
		echo "</tr>\n";
		
		echo "</table>\n";
		echo "<br/><br/>\n";
	echo "</div>\n";
echo "</div>\n";

// Filtrage HTML
echo "<h2>HTML Filtering </h2>\n";
echo "<div style='margin-left:3em;'>\n";

	echo "<p>To prevent attempts at injection of malicious HTML code in the forms, GEPI proposes two devices&nbsp;:</p>\n";
	echo "<table summary='Mode of filtering'>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='filtrage_html' id='filtrage_html_inputfilter' value='inputfilter' ";
	if($filtrage_html=='inputfilter') {echo "checked ";}
	echo "/>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='filtrage_html_inputfilter'> InputFilter (<i>php4/php5</i>)</label><br />\n";
	echo "<span style='font-size:small'>\n";
	echo "This device authorizes only the following tag and attributes&nbsp;:<br />";
	echo "<b>Balises&nbsp;:</b> ";
	for($i=0;$i<count($aAllowedTags);$i++) {
		if($i>0) {echo ", ";}
		echo $aAllowedTags[$i];
	}
	echo "<br />\n";
	echo "<b>Attributs&nbsp;:</b> ";
	for($i=0;$i<count($aAllowedAttr);$i++) {
		if($i>0) {echo ", ";}
		echo $aAllowedAttr[$i];
	}
	echo "</span>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";

	if($htmlpurifier_autorise=='n') {
		echo "<img src='../images/disabled.png' width='20' height='20' alt='Nonaccessible mode' />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo " HTMLpurifier (<i color='red'>php>=5.0.5</i>)<br />\n";
	}
	else {
		echo "<input type='radio' name='filtrage_html' id='filtrage_html_htmlpurifier' value='htmlpurifier' ";
		if($filtrage_html=='htmlpurifier') {echo "checked ";}
		echo "/>\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='filtrage_html_htmlpurifier'> HTMLpurifier (<i>php>=5.0.5</i>)</label><br />\n";
	}
	echo "<span style='font-size:small'>\n";
	echo "More complete than InputFilter in filterings carried out.<br />\n";
	echo "It also tries to make code HTML more correct/valid according to W3C.<br />\n";
	echo "<i>To note&nbsp;:</i> HTMLpurifier do not function well when the magic_quotes_gpc are activated (<i>cf. <a href='http://htmlpurifier.org/docs#toclink4'>http://htmlpurifier.org/docs#toclink4</a></i>).<br />\nThis device must disappear in the long term with PHP6, but if it is activated, one go from HTMLpurifier to InputFilter to avoid the problem.";
	echo "</span>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='filtrage_html' id='pas_de_filtrage_html' value='pas_de_filtrage_html' ";
	if($filtrage_html=='pas_de_filtrage_html') {echo "checked ";}
	echo "/>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='pas_de_filtrage_html'> No HTML filtering </label><br />\n";
	echo "<span style='font-size:small'>\n";
	echo "If you choose this choice, it is possible to malicious users to put dangerous code in the forms .<br />\n";
	echo "</span>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";

	echo "<p style='font-weight:bold; color:red;'>It is very strongly disadvised to deactivate the filtering.</p>\n";

	echo "<br />";

	echo "<p><input type='checkbox' id='utiliser_no_php_in_img' name='utiliser_no_php_in_img' value='y' ";
	if($utiliser_no_php_in_img=='y') {echo "checked ";}
	echo "/><label for='utiliser_no_php_in_img'> Prohibit to insert in appreciations, notices of textbooks of the images generated by PHP</label>.</p>\n";

	echo "<br />";

echo "</div>\n";

echo "<h2><a name='csrf_mode'></a>CSRF</h2>";
echo "<div style='margin-left:3em;'>\n";

	echo "<p>";
	echo "<input type='radio' id='csrf_mode_vide' name='csrf_mode' value='' ";
	if(getSettingValue('csrf_mode')=='') {echo "checked ";}
	echo "/><label for='csrf_mode_vide'> Let make the recording without informing the administrator (<i>strongly disadvised</i>)</label>.<br />";

	echo "<input type='radio' id='csrf_mode_mail_seul' name='csrf_mode' value='mail_seul' ";
	if(getSettingValue('csrf_mode')=='mail_seul') {echo "checked ";}
	echo "/><label for='csrf_mode_mail_seul'> Send a mall to the administrator, but let make the recording (<i>disadvised, because some damage are not simple to repair</i>)</label>.<br />";

	echo "<input type='radio' id='csrf_mode_strict' name='csrf_mode' value='strict' ";
	if(getSettingValue('csrf_mode')=='strict') {echo "checked ";}
	echo "/><label for='csrf_mode_strict'> Refuse the recording and send a mall to the administrator (<i>advised</i>)</label>.";

	echo "</p>\n";

	echo "<p>It is recommended to secure possible attacks CSRF whose users could be victims.<br />
You should choose the last mode above.<br />
See <a href='http://fr.wikipedia.org/wiki/CSRF'>http://fr.wikipedia.org/wiki/CSRF</a> for more details.</p>\n";


echo "</div>\n";

echo "<center><input type='submit' value='Save' /></center>\n";
echo "</form>\n";
echo "<br/><br/><br/>\n";
require("../lib/footer.inc.php");
?>