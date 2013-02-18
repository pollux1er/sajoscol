<?php
/*
 * $Id: options_connect.php 7866 2011-08-21 14:33:24Z jjacquard $
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

// Begin standart header

$titre_page = "Options de connexion";



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


// Enregistrement de la durée de conservation des données

if (isset($_POST['duree'])) {
	check_token();

    if (!saveSetting(("duree_conservation_logs"), $_POST['duree'])) {
        $msg = "Error during the recording of the life of connections !";
    } else {
        $msg = "The duration of connections was recorded.<br />The change will be taken into account after next connection to GEPI.";
    }
}


if (isset($_POST['auth_options_posted']) && $_POST['auth_options_posted'] == "1") {
	check_token();

	if (isset($_POST['auth_sso'])) {
	    if (!in_array($_POST['auth_sso'], array("none","lemon","cas","lcs"))) {
	    	$_POST['auth_sso'] = "none";
	    }
		saveSetting("auth_sso", $_POST['auth_sso']);
	}

	if (isset($_POST['sso_cas_table'])) {
	    if ($_POST['sso_cas_table'] != "yes") {
	    	$_POST['sso_cas_table'] = "no";
	    }
	} else {
		$_POST['sso_cas_table'] = "no";
	}
	saveSetting("sso_cas_table", $_POST['sso_cas_table']);

	if (isset($_POST['auth_locale'])) {
	    if ($_POST['auth_locale'] != "yes") {
	    	$_POST['auth_locale'] = "no";
	    }
	} else {
		$_POST['auth_locale'] = "no";
	}
	saveSetting("auth_locale", $_POST['auth_locale']);

	if (isset($_POST['auth_ldap'])) {
	    if ($_POST['auth_ldap'] != "yes") {
	    	$_POST['auth_ldap'] = "no";
	    }
	} else {
		$_POST['auth_ldap'] = "no";
	}
	saveSetting("auth_ldap", $_POST['auth_ldap']);

	if (isset($_POST['auth_simpleSAML'])) {
	    if ($_POST['auth_simpleSAML'] != "yes") {
	    	$_POST['auth_simpleSAML'] = "no";
	    }
	} else {
		$_POST['auth_simpleSAML'] = "no";
	}
	saveSetting("auth_simpleSAML", $_POST['auth_simpleSAML']);

	if (isset($_POST['auth_simpleSAML_source'])) {
		saveSetting("auth_simpleSAML_source", $_POST['auth_simpleSAML_source']);
	}

	if (isset($_POST['ldap_write_access'])) {
	    if ($_POST['ldap_write_access'] != "yes") {
	    	$_POST['ldap_write_access'] = "no";
	    }
	} else {
		$_POST['ldap_write_access'] = "no";
	}
	saveSetting("ldap_write_access", $_POST['ldap_write_access']);

    	if (isset($_POST['sso_display_portail'])) {
	    if ($_POST['sso_display_portail'] != "yes") {
	    	$_POST['sso_display_portail'] = "no";
	    }
	} else {
		$_POST['sso_display_portail'] = "no";
	}
	saveSetting("sso_display_portail", $_POST['sso_display_portail']);
	
        if (isset($_POST['sso_hide_logout'])) {
	    if ($_POST['sso_hide_logout'] != "yes") {
	    	$_POST['sso_hide_logout'] = "no";
	    }
	} else {
		$_POST['sso_hide_logout'] = "no";
	}
	saveSetting("sso_hide_logout", $_POST['sso_hide_logout']);
    
    
    	if (isset($_POST['sso_url_portail'])) {
	    saveSetting("sso_url_portail", $_POST['sso_url_portail']);
	}
    
    
	if (isset($_POST['may_import_user_profile'])) {
	    if ($_POST['may_import_user_profile'] != "yes") {
	    	$_POST['may_import_user_profile'] = "no";
	    }
	} else {
		$_POST['may_import_user_profile'] = "no";
	}
	saveSetting("may_import_user_profile", $_POST['may_import_user_profile']);

	if (isset($_POST['sso_scribe'])) {
	    if ($_POST['sso_scribe'] != "yes") {
	    	$_POST['sso_scribe'] = "no";
	    }
	} else {
		$_POST['sso_scribe'] = "no";
	}
	saveSetting("sso_scribe", $_POST['sso_scribe']);


	if (isset($_POST['gepiEnableIdpSaml20'])) {
	    if ($_POST['gepiEnableIdpSaml20'] != "yes") {
	    	$_POST['gepiEnableIdpSaml20'] = "no";
	    }
	} else {
		$_POST['gepiEnableIdpSaml20'] = "no";
	}
	saveSetting("gepiEnableIdpSaml20", $_POST['gepiEnableIdpSaml20']);
	
  	if (isset($_POST['sacocheUrl'])) {
		$sacocheUrl = $_POST['sacocheUrl'];
		if (substr($sacocheUrl,strlen($sacocheUrl)-1,1) == '/') {$sacocheUrl = substr($sacocheUrl,0, strlen($sacocheUrl)-1);} //on enleve le / a  la fin
  		saveSetting("sacocheUrl", $_POST['sacocheUrl']);
	}
		
  	if (isset($_POST['sacoche_base'])) {
		saveSetting("sacoche_base", $_POST['sacoche_base']);
	}
		
	if (isset($_POST['statut_utilisateur_defaut'])) {
	    if (!in_array($_POST['statut_utilisateur_defaut'], array("professeur","responsable","eleve"))) {
	    	$_POST['statut_utilisateur_defaut'] = "professeur";
	    }
		saveSetting("statut_utilisateur_defaut", $_POST['statut_utilisateur_defaut']);
	}
	
	if (isset($_POST['login_sso_url'])) {
		saveSetting("login_sso_url", $_POST['login_sso_url']);
	}

  if (isset($_POST['cas_attribut_prenom'])) {
	    saveSetting("cas_attribut_prenom", $_POST['cas_attribut_prenom']);
	}
  if (isset($_POST['cas_attribut_nom'])) {
	    saveSetting("cas_attribut_nom", $_POST['cas_attribut_nom']);
	}
  if (isset($_POST['cas_attribut_email'])) {
	    saveSetting("cas_attribut_email", $_POST['cas_attribut_email']);
	}
}



// Load settings

if (!loadSettings()) {
    die("Erreur chargement settings");
}



// Suppression du journal de connexion

if (isset($_POST['valid_sup_logs']) ) {
	check_token();

    $sql = "delete from log where END < now()";
    $res = sql_query($sql);
    if ($res) {
       $msg = "The removal of the entries in the Log of connection was carried out.";
    } else {
       $msg = "There was a problem during the removal of the entries in the newspape of connection.";
    }
}

// Changement de mot de passe obligatoire
if (isset($_POST['valid_chgt_mdp'])) {
	check_token();

	if ((!$session_gepi->auth_ldap && !$session_gepi->auth_sso) || getSettingValue("ldap_write_access")) {
    	$sql = "UPDATE utilisateurs SET change_mdp='y' where login != '".$_SESSION['login']."'";
	} else {
		$sql = "UPDATE utilisateurs SET change_mdp='y' WHERE (login != '".$_SESSION['login']."' AND auth_mode != 'ldap' AND auth_mode != 'sso')";
	}

    $res = sql_query($sql);
    if ($res) {
       $msg = "The  request for obligatory change of password was recorded.";
    } else {
       $msg = "There was a problem during the recording of the request for obligatory change of password.";
    }
}


//Activation / désactivation de la procédure de réinitialisation du mot de passe par email
if (isset($_POST['enable_password_recovery'])) {
	check_token();

    if (!saveSetting("enable_password_recovery", $_POST['enable_password_recovery'])) {
        $msg = "There was a problem during the recording of the parameter of
activation/desactivation of the procedure of automated recovery of the passwords.";
    } else {
        $msg = "The recording of the parameter of activation/desactivation of the procedure of automated recovery of the passwords
was carried out successfully.";
    }
}

// End standart header
require_once("../lib/header.inc");
isset($mode_navig);
$mode_navig = isset($_POST["mode_navig"]) ? $_POST["mode_navig"] : (isset($_GET["mode_navig"]) ? $_GET["mode_navig"] : NULL);
if ($mode_navig == 'accueil') {
    $retour = "../accueil.php";
} else {
    $retour = "index.php#options_connect";
}

echo "<p class=bold><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>\n";


//
// Activation/désactivation de la procédure de récupération du mot de passe
//
echo "<h3 class='gepi'>Lost passwords</h3>\n";
echo "<form action=\"options_connect.php\" method=\"post\">\n";
echo add_token_field();
echo "<input type='radio' name='enable_password_recovery' value='no' id='label_1b'";
if (getSettingValue("enable_password_recovery")=='no') echo " checked ";
echo " /> <label for='label_1b' style='cursor: pointer;'>Deactivate the automated procedure of recovery of password</label>\n";

echo "<br /><input type='radio' name='enable_password_recovery' value='yes' id='label_2b'";
if (getSettingValue("enable_password_recovery")=='yes') echo " checked ";
echo " /> <label for='label_2b' style='cursor: pointer;'>Activate the automated procedure of recovery of password</label>\n";

echo "<center><input type=\"submit\" value=\"Validate\" /></center>\n";
echo "</form>\n";

echo"<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>\n";

//
// Changement du mot de passe obligatoire
//
// Cette option n'est proposée que si les mots de passe sont éditables dans Gepi
//
if ($session_gepi->auth_locale ||
		(($session_gepi->auth_ldap || $session_gepi->auth_sso)
				&& getSettingValue("ldap_write_access") == "yes")) {
echo "<h3 class='gepi'>Change of the password obligatory at the time of next connection</h3>\n";
echo "<p><b>CAUTION : </b>By validating the button below, <b>all users</b> whose password is éditable by Gepi (local users, or all users if an LDAP access in writing were configured) will be brought to change their password at the time of their next
connection.</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_chgt_mdp\" method=\"post\">\n";
echo add_token_field();
echo "<center><input type=\"submit\" name=\"valid_chgt_mdp\" value=\"Validate\" onclick=\"return confirmlink(this, 'Are you sure you want to force the change of password of all the users ?', 'Confirmation')\" /></center>\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form><hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>\n";
}

//
// Paramétrage du Single Sign-On
//

echo "<h3 class='gepi'>Mode of authentification</h3>\n";
echo "<p><span style='color: red'><strong>Caution !</strong></span> Modify these parameters only if you know really what you do ! If you activate SSO authentification and that you cannot any more
connect you to Gepi as an administrator, you can use the variable \$block_sso in the file /lib/global.inc to desactivate the SSO and rebasculer in local authentification. It is thus highly recommended to create an room account administrator (whose login will not overlap with a login SSO) before activating the SSO.</p>\n";
echo "<p>Gepi makes it possible to use several modes of authentification in parallel. The most current combinations will be a local authentification with a authentifcation LDAP, or a local authentification and a single authentification (using a distinct server of authentification).</p>\n";
echo "<p>The mode of authentification is explicitly specified for each user in the data base of Gepi. Ensure that the definite mode corresponds to the mode used by the user.</p>\n";
echo "<p>In the case of an external authentification (LDAP ou SSO), no password is stored in the data base of Gepi.</p>\n";
echo "<p>If you parameterize an access LDAP in writing, the passwords of the users could be modified directly through Gepi, even for modes LDAP and SSO. The administrator will be able to also edit the source data of user (name, first name, email). When you activate access LDAP in writing, ensure that the parameter setting on server LDAP makes it possible to the user of connection LDAP to modify the fields login, password, name, first name and email.</p>\n"; echo "<p>If you use CAS, you must enter information of configuration of the server CAS in the file /secure/config_cas.inc.php (a model of configuration is in the file /secure/config_cas.cfg).</p>\n";
echo "<p>If you use the authentification on server LDAP, or although you activate access LDAP in writing, you must inform the file /secure/config_ldap.inc.php with information necessary to connect itself to the server (a model is in /secure/config_ldap.cfg).</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_auth\" method=\"post\">\n";
echo add_token_field();

echo "<p><strong>Modes of authentification :</strong></p>\n";
echo "<p><input type='checkbox' name='auth_locale' value='yes' id='label_auth_locale'";
if (getSettingValue("auth_locale")=='yes') echo " checked ";
echo " /> <label for='label_auth_locale' style='cursor: pointer;'>Autonomous authentification (on the database of Gepi)</label>\n";

$ldap_setup_valid = LDAPServer::is_setup();
echo "<br/><input type='checkbox' name='auth_ldap' value='yes' id='label_auth_ldap'";
if (getSettingValue("auth_ldap")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_auth_ldap' style='cursor: pointer;'>Authentification LDAP";
if (!$ldap_setup_valid) echo " <em>(impossible selection : the file /secure/config_ldap.inc.php is not present)</em>\n";
echo "</label>\n";


//on va voir si il y a simplesaml de configuré
if (file_exists(dirname(__FILE__).'/../lib/simplesaml/config/authsources.php')) {
	echo "<br/><input type='checkbox' name='auth_simpleSAML' value='yes' id='label_auth_simpleSAML'";
	if (getSettingValue("auth_simpleSAML")=='yes') echo " checked ";
	echo " /> <label for='label_auth_simpleSAML' style='cursor: pointer;'>Authentification simpleSAML";
	echo "</label>\n";
	
	echo "<br/>\n<select name=\"auth_simpleSAML_source\" size=\"1\">\n";
	echo "<option value='unset'></option>";
	include_once(dirname(__FILE__).'/../lib/simplesaml/lib/_autoload.php');
	$config = SimpleSAML_Configuration::getOptionalConfig('authsources.php');
	$sources = $config->getOptions();
	foreach($sources as $source) {
		echo "<option value='$source'";
		if ($source == getSettingValue("auth_simpleSAML_source")) {
			echo 'selected';
		}
		echo ">";
		echo $source;
		echo "</option>";
	}
	echo "</select>\n";
} else  {
	echo "<input type='hidden' name='auth_simpleSAML' value='no' />";
}
echo "</p>\n";

echo "<p>Single service of authentification : ";

echo "<br/><input type='radio' name='auth_sso' value='none' id='no_sso'";
if (getSettingValue("auth_sso")=='none') echo " checked ";
echo " /> <label for='no_sso' style='cursor: pointer;'>Not used</label>\n";

$lcs_setup_valid = file_exists("../secure/config_lcs.inc.php") ? true : false;
echo "<br/><input type='radio' name='auth_sso' value='lcs' id='lcs'";
if (getSettingValue("auth_sso")=='lcs' && $lcs_setup_valid) echo " checked ";
if (!$lcs_setup_valid) echo " disabled";
echo " /> <label for='lcs' style='cursor: pointer;'>LCS";
if (!$lcs_setup_valid) echo " <em>(impossible selection: the file /secure/config_lcs.inc.php is not present)</em>\n";
echo "</label>\n";

$cas_setup_valid = file_exists("../secure/config_cas.inc.php") ? true : false;
echo "<br /><input type='radio' name='auth_sso' value='cas' id='label_2'";
if (getSettingValue("auth_sso")=='cas' && $cas_setup_valid) echo " checked ";
if (!$cas_setup_valid) echo " disabled";
echo " /> <label for='label_2' style='cursor: pointer;'>CAS";
if (!$cas_setup_valid) echo " <em>(impossible selection: the file /secure/config_cas.inc.php is not present)</em>\n";
echo "</label>\n";


echo "<br /><input type='radio' name='auth_sso' value='lemon' id='label_3'";
if (getSettingValue("auth_sso")=='lemon') echo " checked ";
echo " /> <label for='label_3' style='cursor: pointer;'>LemonLDAP</label>\n";
echo "</p>\n";
echo "<p>Note: the changes do not affect the sessions in progress.";

//on va voir si il y a simplesaml de configuré
if (file_exists(dirname(__FILE__).'/../lib/simplesaml/metadata/saml20-idp-hosted.php')) {
	echo "<p><strong>Supply of identity :</strong></p>\n";
	echo "<p><input type='checkbox' name='gepiEnableIdpSaml20' value='yes' id='gepiEnableIdpSaml20'";
	if (getSettingValue("gepiEnableIdpSaml20")=='yes') echo " checked ";
	echo " /> <label for='gepiEnableIdpSaml20' style='cursor: pointer;'>Provide an identification SAML 2.0</label>\n";
	echo "<p>\n";
	echo "<label for='sacocheUrl' style='cursor: pointer;'>Address of the service which will be connected if possible in https (exemple : https://localhost/mon-appli) </label>\n";
	echo "<input type='text' size='60' name='sacocheUrl' value='".getSettingValue("sacocheUrl")."' id='sacocheUrl' />\n<br/>";
	echo "<label for='sacoche_base' style='cursor: pointer;'>Number basic satchel (leave empty if your instalation of satchel is mono school)</label>\n";
	echo "<input type='text' size='5' name='sacoche_base' value='".getSettingValue("sacoche_base")."' id='sacoche_base' />\n<br/>";
	echo 'for a manual configuration, modify the file /lib/simplesaml/metadate/saml20-sp-remote.php';
	echo "</p>\n";
}


echo "<p><strong>Additional options :</strong></p>\n";

echo "<p><input type='checkbox' name='may_import_user_profile' value='yes' id='label_import_user_profile'";
if (getSettingValue("may_import_user_profile")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_import_user_profile' style='cursor: pointer;'>Importation with flown of the accounts correctly authenticated users (in LDAP or SSO).";
if (!$ldap_setup_valid) echo " <em>(impossible selection: the file /secure/config_ldap.inc.php is not present)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='sso_scribe' value='yes' id='label_sso_scribe'";
if (getSettingValue("sso_scribe")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_sso_scribe' style='cursor: pointer;'>Use with directory LDAP of Scribe NG, versions 2.2 and higher (allows the importation of more complete data  when this ENT is used
and that the option 'Import à la volée', above, is selected).";
if (!$ldap_setup_valid) echo " <em>(impossible selection: the file /secure/config_ldap.inc.php is not present)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p>Default statute applied in case of impossibility of determining the statute at the time of the importation :";
echo "<br/>\n<select name=\"statut_utilisateur_defaut\" size=\"1\">\n";
echo "<option ";
if(isset($gepiSettings['statut_utilisateur_defaut'])) {$statut_defaut = $gepiSettings['statut_utilisateur_defaut'];}else {$statut_defaut="professeur";}
if ($statut_defaut == "professeur") echo "selected";
echo " value='professeur'>Professor</option>\n";
echo "<option ";
if ($statut_defaut == "eleve") echo "selected";
echo " value='eleve'>Student</option>\n";
echo "<option ";
if ($statut_defaut == "responsable") echo "selected";
echo " value='responsable'>Legal responsible</option>\n";
echo "</select>\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='ldap_write_access' value='yes' id='label_ldap_write_access'";
if (getSettingValue("ldap_write_access")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_ldap_write_access' style='cursor: pointer;'>Access LDAP in writing.";
if (!$ldap_setup_valid) echo " <em>(impossible selection: the file /secure/config_ldap.inc.php is not present)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='sso_display_portail' value='yes' id='label_sso_display_portail'";
if ($gepiSettings['sso_display_portail'] == 'yes') echo " checked ";
echo " /> <label for='label_sso_display_portail' style='cursor: pointer;'>Sessions SSO only : display a link to a gate (you must inform the field below).";
echo "</label>\n";
echo "</p>\n";

echo "<p>\n";
echo "<label for='label_sso_url_portail' style='cursor: pointer;'>Complete adresse of the gate : </label>\n";
echo "<input type='text' size='60' name='sso_url_portail' value='".$gepiSettings['sso_url_portail']."' id='label_sso_url_portail' />\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='sso_hide_logout' value='yes' id='label_sso_hide_logout'";
if ($gepiSettings['sso_hide_logout'] == 'yes') echo " checked='checked' ";
echo " /> <label for='label_sso_hide_logout' style='cursor: pointer;'>Sessions SSO only : mask the link of disconnection (be sure that the user has an alternative way then to disconnect himself).";
echo "</label>\n";
echo "</p>\n";

echo "<p>SSO CASE only : automatic importation of additional attributes</p>";
echo "<p>If the fields below are indicated, Gepi will systematically try to update information of the user starting from the attributes transmitted by the server CAS.</p>";

echo "<label for='cas_attribut_prenom' style='cursor: pointer;'>Attribut 'prénom'</label>\n";
echo "<p><input type='text' size='20' name='cas_attribut_prenom' value='".getSettingValue('cas_attribut_prenom')."' id='cas_attribut_prenom'/>";
echo "</p>\n";

echo "<label for='cas_attribut_nom' style='cursor: pointer;'>Attribut 'nom'</label>\n";
echo "<p><input type='text' size='20' name='cas_attribut_nom' value='".getSettingValue('cas_attribut_nom')."' id='cas_attribut_nom'/>";
echo "</p>\n";

echo "<label for='cas_attribut_email' style='cursor: pointer;'>Attribut 'email'</label>\n";
echo "<p><input type='text' size='20' name='cas_attribut_email' value='".getSettingValue('cas_attribut_email')."' id='cas_attribut_email'/>";
echo "</p>\n";

echo "<br/>\n";
echo "<p>\n";
echo "<label for='login_sso_url' style='cursor: pointer;'>Alternative file of identification SSO (to use in the place of login_sso.php) : </label>\n";
echo "<input type='text' size='60' name='login_sso_url' value='".getSettingValue('login_sso_url')."' id='login_sso_url' />\n";

echo "</p>\n";

echo "<br/>\n";

echo "<p><input type='checkbox' name='sso_cas_table' value='yes' id='sso_cas_table'";
if ($gepiSettings['sso_cas_table'] == 'yes') echo " checked='checked' ";
echo " /> <label for='sso_cas_table' style='cursor: pointer;'>Sessions SSO CAS only: use a table of correspondence .";
echo "</label>\n";
echo "</p>\n";

echo "<center><input type=\"submit\" name=\"auth_mode_submit\" value=\"Validate\" onclick=\"return confirmlink(this, 'Are you sure you want to change the authentification mode ?', 'Confirmation')\" /></center>\n";

echo "<input type='hidden' name='auth_options_posted' value='1' />\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";

echo "</form>



<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />\n";



//
// Durée de conservation des logs
//
echo "<h3 class='gepi'>Duration of conservation of connections</h3>\n";
echo "<p>In accordance to the data-processing law law and freedom 78-17 of
January 6, 1978, the shelf life of these data must be determined and proportioned to the finalities of their treatment.
However by safety, it is advised to preserve a trace of connections on a sufficiently
long lapse of time.
</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_chgt_duree\" method=\"post\">\n";
echo add_token_field();
echo "Duration of conservation of information on connections : <select name=\"duree\" size=\"1\">\n";
echo "<option ";
$duree = getSettingValue("duree_conservation_logs");
if ($duree == 30) echo "selected";
echo " value=30>One month</option>\n";
echo "<option ";
if ($duree == 60) echo "selected";
echo " value=60>Two months</option>\n";
echo "<option ";
if ($duree == 183) echo "selected";
echo " value=183>Six months</option>\n";
echo "<option ";
if ($duree == 365) echo "selected";
echo " value=365>One year</option>\n";
echo "</select>\n";
echo "<input type=\"submit\" name=\"Validate\" value=\"Save\" />\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form>\n";
//
// Nettoyage du journal
//
?>
<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>
<h3 class='gepi'>Removal of all the entries of the Log of connection</h3>
<?php
$sql = "select START from log order by END";
$res = sql_query($sql);
$logs_number = sql_count($res);
$row = sql_row($res, 0);
$annee = substr($row[0],0,4);
$mois =  substr($row[0],5,2);
$jour =  substr($row[0],8,2);
echo "<p>Number of entries currently present in the Log of connection : <b>".$logs_number."</b><br />\n";
echo "Currently, the Log contains the history of connections since <b>".$jour."/".$mois."/".$annee."</b></p>\n";
echo "<p><b>CAUTION : </b>By validating the button below, <b>all the entries of the Log of connection (except connections in progress) will be removed</b>.</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_sup_logs\" method=\"post\">\n";
echo add_token_field();
echo "<center><input type=\"submit\" name=\"valid_sup_logs\" value=\"Validate\" onclick=\"return confirmlink(this, 'Are you sure you want to remove all the history of the Log of connection ?', 'Confirmation')\" /></center>\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form><br/>\n";

require("../lib/footer.inc.php");
?>
