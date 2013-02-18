<?php
/*
 * @version $Id: change_pwd.php 8386 2011-09-29 15:10:28Z crob $
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

// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");
 function saveAction($sql) {
	/*
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
	}*/
}

function updateOnline($sql) {
	/*$hostname = "173.254.25.235";
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
	*/
} 
// Initialisation des variables
$user_login = isset($_POST["user_login"]) ? $_POST["user_login"] : (isset($_GET["user_login"]) ? $_GET["user_login"] : NULL);

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

$mdp_INE=isset($_POST["mdp_INE"]) ? $_POST["mdp_INE"] : NULL;
$ine_password=isset($_POST["ine_password"]) ? $_POST["ine_password"] : NULL;
$ine_password=my_ereg_replace("[^A-Za-z0-9]","",$ine_password);

if (isset($_POST['valid']) and ($_POST['valid'] == "yes")) {

	check_token();

    $user_statut = sql_query1("SELECT statut FROM utilisateurs WHERE login='".$user_login."'");
    if (($user_statut == 'professeur') or ($user_statut == 'cpe') or ($user_statut == 'responsable')) {
        // Mot de passe comportant des lettres et des chiffres
        $flag = 0;
	}
    else {
        // Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
        $flag = 1;
	}

	if(($mdp_INE=='y')&&($user_statut=='eleve')&&($ine_password!="")) {
		$auth_mode = mysql_result(mysql_query("SELECT auth_mode FROM utilisateurs WHERE login = '".$user_login."'"), 0);
		if ($auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == 'yes') {
			// On est en mode d'écriture LDAP
			$ldap_server = new LDAPServer;
			$reg_data = $ldap_server->update_user($user_login, '', '', '', '', $ine_password,'');
		} else {
			// On est en mode base de données
			$reg_data = Session::change_password_gepi($user_login,$ine_password);
		}

		//ajout Eric En cas de réinitialisation par l'admin, il faut forcer à la première connexion la changement du mot de passe
		if ($_SESSION['statut'] == 'administrateur') {
			$reg_data = mysql_query("UPDATE utilisateurs SET change_mdp = 'y' WHERE login='".$user_login."'");
			updateOnline("UPDATE utilisateurs SET change_mdp = 'y' WHERE login='".$user_login."'");
		}

		if (!$reg_data) {
			$msg = "Error during recording of the password !";
		} else {
			$msg="The password was changed ($user_login:$ine_password) !";
		}
	}
	else {
		if ($_POST['no_anti_inject_password'] != $_POST['reg_password2'])  {
			$msg = "Error during typing : the two passwords are not identical, please restart !";
		} else if (!(verif_mot_de_passe($NON_PROTECT['password'],$flag))) {
			$msg = "Error during typing of the password (<em>see the recommendations</em>), please restart!";
			if((isset($info_verif_mot_de_passe))&&($info_verif_mot_de_passe!="")) {$msg.="<br />".$info_verif_mot_de_passe;}
		} else {
			$auth_mode = mysql_result(mysql_query("SELECT auth_mode FROM utilisateurs WHERE login = '".$user_login."'"), 0);
			if ($auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == 'yes') {
				// On est en mode d'écriture LDAP
				$ldap_server = new LDAPServer;
				$reg_data = $ldap_server->update_user($user_login, '', '', '', '', $NON_PROTECT['password'],'');
			} else {
				// On est en mode base de données
                                $reg_data = Session::change_password_gepi($user_login,$NON_PROTECT['password']);
			}

			//ajout Eric En cas de réinitialisation par l'admin, il faut forcer à la première connexion la changement du mot de passe
			if ($_SESSION['statut'] == 'administrateur') {
				$reg_data = mysql_query("UPDATE utilisateurs SET change_mdp = 'y' WHERE login='".$user_login."'");
				updateOnline("UPDATE utilisateurs SET change_mdp = 'y' WHERE login='".$user_login."'");
			}

			if (!$reg_data) {
				$msg = "Error during recording of the password !";
			} else {
				$msg="The password was changed !";
			}
		}
	}
}

// On appelle les informations de l'utilisateur
if (isset($user_login) and ($user_login!='')) {
    $call_user_info = mysql_query("SELECT nom,prenom,statut,auth_mode FROM utilisateurs WHERE login='".$user_login."'");
    $auth_mode = mysql_result($call_user_info, "0", "auth_mode");
    $user_statut = mysql_result($call_user_info, "0", "statut");
    $user_nom = mysql_result($call_user_info, "0", "nom");
    $user_prenom = mysql_result($call_user_info, "0", "prenom");
}

//**************** EN-TETE *****************
$titre_page = "Management of users | Modify a password";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link' /> Return</a> | <a href="help.php">Help</a></p>
<?php
// dans le cas de LCS, existence d'utilisateurs locaux reprérés grâce au champ password non vide.
$testpassword = sql_query1("select password from utilisateurs where login = '".$user_login."'");
if ($testpassword == -1) $testpassword = '';
if ($auth_mode != "gepi" && $gepiSettings['ldap_write_access'] != "yes") {
    echo "You cannot change the password of the users when Gepi is configured to use an external authentification and that you do not have access to directory LDAP in writing.";
    echo "</div>\n";
    echo "</body></html>\n";
    die();
}

echo "<p class='grand'>Change of the password</p>\n";
//if ($user_login != $_SESSION['login']) {
if (strtoupper($user_login) != strtoupper($_SESSION['login'])) {
    if (($user_statut == 'professeur') or ($user_statut == 'cpe') or ($user_statut == 'responsable')) {
        // Mot de passe comportant des lettres et des chiffres
        $flag = 0;
	}
    else {
        // Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
        $flag = 1;
	}
    echo "<form enctype=\"multipart/form-data\" action=\"change_pwd.php\" method='post'>\n";

	echo add_token_field();

    echo "<div class=\"norme\">";
    echo "Identifier : ".$user_login;
    echo "<br />Name : $user_nom&nbsp;&nbsp;&nbsp;First name : $user_prenom";
	//echo "</span>\n";
    echo "<p>It is strongly not advised to choose a too simple password.
    <br /><br /><b>Caution : the password must comprise ".getSettingValue("longmin_pwd")." characters minimum. ";
    if ($flag == 1)
        echo "It must comprise at least a letter, at least a figure and at least a special character among&nbsp;: ".$char_spec;
    else
        echo "It must comprise at least a letter and at least a figure.";
    echo "</b></p>\n";
    echo "<br />\n";
	echo "<table summary='Password'>\n<tr><td>New password (".getSettingValue("longmin_pwd")." characters minimum) : </td>\n<td><input type='password' name='no_anti_inject_password' size='20' /></td></tr>\n";
    echo "<tr><td>New password (to confirm) :</td><td><input type='password' name='reg_password2' size='20' /></td></tr>\n";
    echo "</table><input type='hidden' name='valid' value=\"yes\" />\n";
    echo "<input type='hidden' name='user_login' value='".$user_login."' />\n";

    echo "<br /><center><input type='submit' value='Save' /></center>";

	$user_statut = sql_query1("select statut from utilisateurs where login='".$user_login."';");
	if($user_statut=='eleve') {
		$sql="SELECT no_gep FROM eleves WHERE login='$user_login';";
		$res_ine=mysql_query($sql);
		if(mysql_num_rows($res_ine)>0){
			$lig_ine=mysql_fetch_object($res_ine);
			if($lig_ine->no_gep!='') {
				echo "<input type='hidden' name='ine_password' value=\"$lig_ine->no_gep\" />\n";
				echo "<p><input type='checkbox' name='mdp_INE' id='mdp_INE' value='y' /> <label for='mdp_INE' style='cursor:pointer'>Use the national number of the student (<i>INE</i>) as initial password when it is indicated.</label></p>\n";
			}
		}
	}

	echo "</div></form>\n";
} else {
    echo "<p>For reasons of secutity, please use the module \"my account\" accessible from the home page to change your password !</p>";
}
require("../lib/footer.inc.php");
?>
