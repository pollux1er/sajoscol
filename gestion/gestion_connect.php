<?php
/*
 * $Id: gestion_connect.php 8050 2011-08-30 08:32:43Z crob $
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

$titre_page = "Gestion des connexions";



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

/*
// Enregistrement de la durée de conservation des données

if (isset($_POST['duree'])) {
    if (!saveSetting(("duree_conservation_logs"), $_POST['duree'])) {
        $msg = "Erreur lors de l'enregistrement de la durée de conservation des connexions !";
    } else {
        $msg = "La durée de conservation des connexions a été enregistrée.<br />Le changement sera pris en compte après la prochaine connexion à GEPI.";
    }
}


if (isset($_POST['use_sso'])) {
    if (!saveSetting(("use_sso"), $_POST['use_sso'])) {
        $msg = "Erreur lors de l'enregistrement du mode d'authentification !";
    } else {
        $msg = "Le mode d'authentification a été enregistré.";
    }
}
*/

// Load settings

if (!loadSettings()) {
    die("Error loading settings");
}


/*// Suppression du journal de connexion

if (isset($_POST['valid_sup_logs']) ) {
    $sql = "delete from log where END < now()";
    $res = sql_query($sql);
    if ($res) {
       $msg = "La suppression des entrées dans le journal de connexion a été effectuée.";
    } else {
       $msg = "Il y a eu un problème lors de la suppression des entrées dans le journal de connexion.";
    }
}

// Changement de mot de passe obligatoire
if (isset($_POST['valid_chgt_mdp'])) {
    $sql = "update utilisateurs set change_mdp='y' where login != '".$_SESSION['login']."'";
    $res = sql_query($sql);
    if ($res) {
       $msg = "La demande de changement obligatoire de mot de passe a été enregistrée.";
    } else {
       $msg = "Il y a eu un problème lors de l'enregistrement de la demande de changement obligatoire de mot de passe.";
    }
}
*/

//
// Protection contre les attaques.
//
if (isset($_POST['valid_param_mdp'])) {
	check_token();

    settype($_POST['nombre_tentatives_connexion'],"integer");
    settype($_POST['temps_compte_verrouille'],"integer");
    if ($_POST['nombre_tentatives_connexion'] < 1) $_POST['nombre_tentatives_connexion'] = 1;
    if ($_POST['temps_compte_verrouille'] < 0) $_POST['temps_compte_verrouille'] = 0;
    if (!saveSetting("nombre_tentatives_connexion", $_POST['nombre_tentatives_connexion'])) {
        $msg1 = "There was a problem during recording of the parameter nombre_tentatives_connexion.";
    } else {
        $msg1 = "";
    }
    if (!saveSetting("temps_compte_verrouille", $_POST['temps_compte_verrouille'])) {
        $msg2 = "There was a problem during recording of the parameter temps_compte_verrouille.";
    } else {
        $msg2 = "";
    }
    if (($msg1 == "") and ($msg2 == ""))
        $msg = "The parameters were correctly recorded";
    else
        $msg = $msg1." ".$msg2;
}



//Activation / désactivation du login
if (isset($_POST['disable_login'])) {
	check_token();

    if (!saveSetting("disable_login", $_POST['disable_login'])) {
        $msg = "There was a problem during recording of the parameter ofactivation/desactivation of connections.";
    } else {
        $msg = "the recording of the parameter of activation/desactivation of connections was carried out successfully.";
    }
}

//Activation / désactivation de la procédure de réinitialisation du mot de passe par email
if (isset($_POST['enable_password_recovery'])) {
	check_token();

    if (!saveSetting("enable_password_recovery", $_POST['enable_password_recovery'])) {
        $msg = "There was a problem during recording of the parameter of activation/desactivation of connections.";
    } else {
        $msg = "the recording of the parameter of activation/desactivation of connections was carried out successfully.";
    }
}


//EXPORT CSV
if(isset($_GET['mode'])){
	if($_GET['mode']=="csv"){

	if (!isset($_SESSION['donnees_export_csv_log'])) { $ligne_csv = false ; } else {$ligne_csv =  $_SESSION['donnees_export_csv_log'];}

		$chaine_titre="Export_log_Annee_scolaire_".getSettingValue("gepiYear");
		$now = gmdate('D, d M Y H:i:s') . ' GMT';
		$nom_fic=$chaine_titre."_".$now.".csv";

		send_file_download_headers('text/x-csv',$nom_fic);

		$nb_ligne = count($ligne_csv);

		for ($i=0;$i<$nb_ligne;$i++) {
		  echo $ligne_csv[$i];
		}
		die();
	}
}
//FIN EXPORT CSV


if(isset($_POST['valid_envoi_mail_connexion'])){
	check_token();

	$envoi_mail_connexion=isset($_POST['envoi_mail_connexion']) ? $_POST['envoi_mail_connexion'] : "n";
	if($envoi_mail_connexion!="y") {
		$envoi_mail_connexion="n";
	}
	if (!saveSetting("envoi_mail_connexion", $envoi_mail_connexion)) {
		$msg = "There was a problem during recording of the parameter of sending or not of mall during of connections.";
	} else {
		$msg = "the recording of the parameter of sending or not of mall during connections was carried out successfully.";
	}
}

if(isset($_POST['valid_message'])){
	check_token();

	$message_login=isset($_POST['message_login']) ? $_POST['message_login'] : 0;
	//$sql="UPDATE setting SET value='$message_login' WHERE name='message_login'";
	saveSetting('message_login',$message_login);
}

//================================
// End standart header
require_once("../lib/header.inc");
//================================

//debug_var();

isset($mode_navig);
$mode_navig = isset($_POST["mode_navig"]) ? $_POST["mode_navig"] : (isset($_GET["mode_navig"]) ? $_GET["mode_navig"] : NULL);
if ($mode_navig == 'accueil') {
    $retour = "../accueil.php";
} else {
    $retour = "index.php#gestion_connect";
}

echo "<p class='bold'><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a></p>";



//
// Affichage des personnes connectées
//
echo "<h3 class='gepi'>Users connected in this moment</h3>";
echo "<div title=\"Connected users\">";
echo "<ul>";
// compte le nombre d'enregistrement dans la table
//$sql = "select u.login, concat(u.prenom, ' ', u.nom) utilisa, u.email from log l, utilisateurs u where (l.LOGIN = u.login and l.END > now())";
$sql = "select u.login, concat(u.prenom, ' ', u.nom) utilisa, u.email, u.auth_mode from log l, utilisateurs u where (l.LOGIN = u.login and l.END > now())";

$res = sql_query($sql);
if ($res) {
    for ($i = 0; ($row = sql_row($res, $i)); $i++) {
		echo("<li>" . $row[1]. " | <a href=\"mailto:" . $row[2] . "\">Send a mail</a>");

		$afficher_deconnecter_et_changer_mdp="n";
		//if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and getSettingValue("use_sso") != "lcs" and getSettingValue("use_sso") != "ldap_scribe")) {
		if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and getSettingValue("auth_sso") != "lcs" and getSettingValue("use_sso") != "ldap_scribe")) {
			$afficher_deconnecter_et_changer_mdp="y";
		}
		elseif((getSettingValue("auth_sso") == "lcs")&&($row[3]=='gepi')) {
			$afficher_deconnecter_et_changer_mdp="y";
		}

		if($afficher_deconnecter_et_changer_mdp=="y") {
			echo " | <a href=\"../utilisateurs/change_pwd.php?user_login=".$row[0].add_token_in_url()."\">Disconnect by changing the password</a>";
		}
		echo "</li>";
    }
}

?>
</ul>
</div>

<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>
<?php
//
// Activation/désactivation des connexions
//
echo "<h3 class='gepi'>Activation/desactivation of connections</h3>\n";


$disable_login=getSettingValue("disable_login");

if($disable_login=="yes"){
	echo "<p>Connections are currently <span style='font-weight:bold'>deactivated</span>.</p>\n";
}
elseif($disable_login=="no"){
	echo "<p>Connections are currently  <span style='font-weight:bold'>activated</span>.</p>\n";
}
else{
	echo "<p>The Connections <span style='font-weight:bold'>futures</span> are currently <span style='font-weight:bold'>deactivated</span>.<br />No new connection is accepted.</p>\n";
}

echo "<p>By deactivating connections, you make connection impossible to the site for the users, except the administrators.</p>\n";

echo "<form action=\"gestion_connect.php\" name=\"form_acti_connect\" method=\"post\">\n";
echo add_token_field();

echo "<table border='0' summary='Activation/desactivation of connections'>\n";
echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<input type='radio' name='disable_login' value='yes' id='label_1a'";
if ($disable_login=='yes'){ echo " checked ";}
echo " />\n";
echo "</td>\n";
echo "<td>\n";
echo "<label for='label_1a' style='cursor: pointer;'>Deactivate connections</label>\n";
echo "<br />\n";
echo "(<i><span style='color:red;'>Attention, the currently connected users are automatically disconnected.</span></i>)\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<input type='radio' name='disable_login' value='soft' id='label_3a'";
if ($disable_login=='soft'){ echo " checked ";}
echo " />\n";
echo "</td>\n";
echo "<td>\n";
echo "<label for='label_3a' style='cursor: pointer;'>Deactivate future connections</label>\n";
echo "<br />(<i>and to await the end of current connections to be able deactivate connections and to proceed to a maintenance action, for example</i>)\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<input type='radio' name='disable_login' value='no' id='label_2a'";
if ($disable_login=='no'){ echo " checked ";}
echo " />\n";
echo "</td>\n";
echo "<td>\n";
echo "<label for='label_2a' style='cursor: pointer;'>Activate connections</label>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<center><input type=\"submit\" name=\"valid_acti_mdp\" value=\"Validate\" /></center>\n";
echo "</form>\n";

echo "<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />\n";


//
// Message sur la page de login
//
echo "<a name='message_login'></a>\n";
echo "<h3 class='gepi'>Make appear a message on the page of login</h3>\n";

$message_login=getSettingValue("message_login");
if($message_login=='') {$message_login=0; saveSetting('message_login',$message_login);}

$sql="SELECT * FROM message_login ORDER BY texte;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p>No message was still typed.</p>\n";
	echo "<p><a href='saisie_message_connexion.php'>Type new messages.</a></p>\n";
}
else {
	echo "<form action=\"gestion_connect.php\" name=\"form_message_login\" method=\"post\">\n";
	echo add_token_field();

	echo "<table summary='Choice of the message'>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='message_login' id='message_login0' value='0' ";
	if($message_login==0) {echo "checked ";}
	echo "/>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='message_login0'> No message</label><br />\n";
	echo "</td>\n";
	echo "</tr>\n";
	while($lig=mysql_fetch_object($res)) {
		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='message_login' id='message_login$lig->id' value='$lig->id'";
		if($message_login==$lig->id) {echo "checked ";}
		echo ">\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='message_login$lig->id'> ".nl2br($lig->texte)."</label><br />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo "<center><input type=\"submit\" name=\"valid_message\" value=\"Validate\" /></center>\n";
	echo "</form>\n";

	echo "<p><a href='saisie_message_connexion.php'>Type new messages or modify existing messages.</a></p>\n";

}

echo "<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />\n";



/*
//
// Activation/désactivation de la procédure de récupération du mot de passe
//
echo "<h3 class='gepi'>Mots de passe perdus</h3>";
echo "<form action=\"gestion_connect.php\" method=\"post\">";
echo "<input type='radio' name='enable_password_recovery' value='no' id='label_1b'";
if (getSettingValue("enable_password_recovery")=='no') echo " checked ";
echo " /> <label for='label_1b'>Désactiver la procédure automatisée de récupération de mot de passe</label>";

echo "<br /><input type='radio' name='enable_password_recovery' value='yes' id='label_2b'";
if (getSettingValue("enable_password_recovery")=='yes') echo " checked ";
echo " /> <label for='label_2b'>Activer la procédure automatisée de récupération de mot de passe</label>";

echo "<center><input type=\"submit\" value=\"Valider\" /></center>";
echo "</form>";

echo"<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>";
*/
//
// Protection contre les attaques.
//
echo "<h3 class='gepi'>Protection against brute forces attacks .</h3>";
echo "<p>Configuration of GEPI to temporarily block the account of a user
after a certain number of unfruitful attempts to connection.
<br />In the other hand, a pirate can be use of this mechanism of self-defence to block permanently accounts user or administrator.
<br />If you are confronted to this emergency, you will be able in the file \"config.inc.php\", force the unlock of administrator accounts 
and/ or to put in black list, accused IP addresses.<br /></p>";

echo "<form action=\"gestion_connect.php\" name=\"form_param_mdp\" method=\"post\">";
echo add_token_field();
echo "<table summary='Parameter setting'><tr>";
echo "<td>Maximum numbers of unfruitful attempts of connection: </td>";
echo "<td><input type=\"text\" name=\"nombre_tentatives_connexion\" value=\"".getSettingValue("nombre_tentatives_connexion")."\" size=\"20\" /></td>";
echo "</tr><tr>";
echo "<td>Time in minutes during which an account is temporarily locked after a too great number of unfruitful tests : </td>";
echo "<td><input type=\"text\" name=\"temps_compte_verrouille\" value=\"".getSettingValue("temps_compte_verrouille")."\" size=\"20\" /></td>";
echo "</tr></table>";

echo "<center><input type=\"submit\" name=\"valid_param_mdp\" value=\"Validate\" /></center>";
echo "</form><hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />";




//
// Avertissement des utilisateurs lors des connexions
//
echo "<h3 class='gepi'>Warning during connections</h3>";
echo "<p>It is possible to inform the users by mall during theirs
connection, if their mall address is indicated in Gepi (<i>information editable by the link 'Manage my account'</i>).<br />If the address is not indicated no mall cannot arrive at the user who
connects himself.<br />If the address is correctly indicated, in the event of usurpation like legitimate connection, the user will receive a mall.<br />If it does not react by changing password and by informing the
administrator during usurpation, later intrusions could be operated without the user being informed if
the intruder takes care to remove/modify the address mall in ' Managing my account '.</p>\n";

echo "<form action=\"gestion_connect.php\" name=\"form_mail_connexion\" method=\"post\">";
echo add_token_field();
echo "<table summary='Mail'>\n";
echo "<tr>\n";
echo "<td valign='top'>Activate the sending of mall during connection: </td>\n";
echo "<td>\n";
echo "<label for='envoi_mail_connexion_y' style='cursor: pointer;'><input type=\"radio\" name=\"envoi_mail_connexion\" id=\"envoi_mail_connexion_y\" value='y' ";
if(getSettingValue("envoi_mail_connexion")=="y") {
	echo "checked ";
}
echo " /> Oui</label>\n";
echo "<br />\n";
echo "<label for='envoi_mail_connexion_n' style='cursor: pointer;'><input type=\"radio\" name=\"envoi_mail_connexion\" id=\"envoi_mail_connexion_n\" value='n' ";
if(getSettingValue("envoi_mail_connexion")!="y") {
	echo "checked ";
}
echo " /> No</label>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<center><input type=\"submit\" name=\"valid_envoi_mail_connexion\" value=\"Validate\" /></center>";
echo "</form>\n";
echo "<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />\n";



/*
//
// Changement du mot de passe obligatoire
//
if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and getSettingValue("use_sso") != "lcs" and getSettingValue("use_sso") != "ldap_scribe")) {
echo "<h3 class='gepi'>Changement du mot de passe obligatoire lors de la prochaine connexion</h3>";
echo "<p><span style='font-weight:bold'>ATTENTION : </span>En validant le bouton ci-dessous, <span style='font-weight:bold'>tous les utilisateurs</span> seront amenés à changer leur mot de passe lors de leur prochaine connexion.</p>";
echo "<form action=\"gestion_connect.php\" name=\"form_chgt_mdp\" method=\"post\">";
echo "<center><input type=\"submit\" name=\"valid_chgt_mdp\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir forcer le changement de mot de passe de tous les utilisateurs ?', 'Confirmation')\" /></center>";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form><hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>";
}
//
// Paramétrage du Single Sign-On
//

echo "<h3 class='gepi'>Mode d'authentification</h3>";
echo "<p><span style='font-weight:bold'>ATTENTION :</span> Dans le cas d'une authentification en Single Sign-On avec CAS, LemonLDAP ou LCS, seuls les utilisateurs pour lesquels aucun mot de passe n'est présent dans la base de données pourront se connecter. Toutefois, il est recommandé de conserver un compte administrateur avec un mot de passe afin de pouvoir vous connecter en bloquant le SSO par le biais de la variable 'block_sso' du fichier /lib/global.inc.</p>";
echo "<p>Si vous utilisez CAS, vous devez entrer les coordonnées du serveur CAS dans le fichier /secure/config_cas.inc.php.</p>";
echo "<p>Si vous utilisez l'authentification sur serveur LDAP, vous devez renseigner le fichier /secure/config_ldap.inc.php avec les informations nécessaires pour se connecter au serveur.</p>";
echo "<form action=\"gestion_connect.php\" name=\"form_auth\" method=\"post\">";

echo "<input type='radio' name='use_sso' value='no' id='label_1'";
if (getSettingValue("use_sso")=='no' OR !getSettingValue("use_sso")) echo " checked ";
echo " /> <label for='label_1'>Authentification autonome (sur la base de données de Gepi) [défaut]</label>";

echo "<br/><input type='radio' name='use_sso' value='lcs' id='lcs'";
if (getSettingValue("use_sso")=='lcs') echo " checked ";
echo " /> <label for='lcs'>Authentification sur serveur LCS</label>";

echo "<br/><input type='radio' name='use_sso' value='ldap_scribe' id='label_ldap_scribe'";
if (getSettingValue("use_sso")=='ldap_scribe') echo " checked ";
echo " /> <label for='label_ldap_scribe'>Authentification sur serveur Eole SCRIBE (LDAP)</label>";

echo "<br /><input type='radio' name='use_sso' value='cas' id='label_2'";
if (getSettingValue("use_sso")=='cas') echo " checked ";
echo " /> <label for='label_2'>Authentification SSO par un serveur CAS</label>";

echo "<br /><input type='radio' name='use_sso' value='lemon' id='label_3'";
if (getSettingValue("use_sso")=='lemon') echo " checked ";
echo " /> <label for='label_3'>Authentification SSO par LemonLDAP</label>";

echo "<p>Remarque : les changements n'affectent pas les sessions en cours.";

echo "<center><input type=\"submit\" name=\"auth_mode_submit\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir changer le mode d\' authentification ?', 'Confirmation')\" /></center>";

echo "<input type=hidden name=mode_navig value='$mode_navig' />";

echo "</form>

<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />";



//
// Durée de conservation des logs
//
echo "<h3 class='gepi'>Durée de conservation des connexions</h3>";
echo "<p>Conformément à la loi loi informatique et liberté 78-17 du 6 janvier 1978, la durée de conservation de ces données doit être déterminée et proportionnée aux finalités de leur traitement.
Cependant par sécurité, il est conseillé de conserver une trace des connexions sur un laps de temps suffisamment long.
</p>";
echo "<form action=\"gestion_connect.php\" name=\"form_chgt_duree\" method=\"post\">";
echo "Durée de conservation des informations sur les connexions : <select name=\"duree\" size=\"1\">";
echo "<option ";
$duree = getSettingValue("duree_conservation_logs");
if ($duree == 30) echo "selected";
echo " value=30>Un mois</option>";
echo "<option ";
if ($duree == 60) echo "selected";
echo " value=60>Deux mois</option>";
echo "<option ";
if ($duree == 183) echo "selected";
echo " value=183>Six mois</option>";
echo "<option ";
if ($duree == 365) echo "selected";
echo " value=365>Un an</option>";
echo "</select>";
echo "<input type=\"submit\" name=\"Valider\" value=\"Save\" />";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form>";
//
// Nettoyage du journal
//
?>
<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>
<h3 class='gepi'>Suppression de toutes les entrées du journal de connexion</h3>
<?php
$sql = "select START from log order by END";
$res = sql_query($sql);
$logs_number = sql_count($res);
$row = sql_row($res, 0);
$annee = substr($row[0],0,4);
$mois =  substr($row[0],5,2);
$jour =  substr($row[0],8,2);
echo "<p>Nombre d'entrées actuellement présentes dans le journal de connexion : <span style='font-weight:bold'>".$logs_number."</span><br />";
echo "Actuellement, le journal contient l'historique des connexions depuis le <span style='font-weight:bold'>".$jour."/".$mois."/".$annee."</span></p>";
echo "<p><span style='font-weight:bold'>ATTENTION : </span>En validant le bouton ci-dessous, <span style='font-weight:bold'>toutes les entrées du journal de connexion (hormis les connexions en cours) seront supprimées</span>.</p>";
echo "<form action=\"gestion_connect.php\" name=\"form_sup_logs\" method=\"post\">";
echo "<center><input type=\"submit\" name=\"valid_sup_logs\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir supprimer tout l\'historique du journal de connexion ?', 'Confirmation')\" /></center>";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form>";
*/
//
// Journal des connections
//
?>
<!--<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>-->
<?php
if (isset($_POST['duree2'])) {
   $duree2 = $_POST['duree2'];
} else {
	if (isset($_GET['duree2'])) {
		$duree2 = $_GET['duree2'];
	} else {
		$duree2 = '20dernieres';
	}
}

if(($duree2!="20dernieres")&&
	($duree2!="2")&&
	($duree2!="7")&&
	($duree2!="15")&&
	($duree2!="30")&&
	($duree2!="60")&&
	($duree2!="183")&&
	($duree2!="365")&&
	($duree2!="all")
	) {
		$duree2="20dernieres";
}

switch( $duree2 ) {
   case '20dernieres' :
   $display_duree="the 20 last";
   break;
   case 2:
   $display_duree="since two days";
   break;
   case 7:
   $display_duree="since one week";
   break;
   case 15:
   $display_duree="since fifteen days";
   break;
   case 30:
   $display_duree="since one month";
   break;
   case 60:
   $display_duree="since two months";
   break;
   case 183:
   $display_duree="since six months";
   break;
   case 365:
   $display_duree="since one year";
   break;
   case 'all':
   $display_duree="since the beginning";
   break;
}

echo "<h3 class='gepi'>Log of connections <span style='font-weight:bold'>".$display_duree."</span></h3>";

?>
<div title="Journal des connections" style="width: 100%;">
<ul>
<li>The red lines announce an attempt at connection with an erroneous password.</li>
<li>The orange lines announce a closed session for which the user did not disconnect himself correctly.</li>
<li>The lines in black announce a normally closed session.</li>
<li>The lines in green indicate the sessions in progress (that can correspond to a currently closed connection but for which the user did not disconnect himself correctly).</li>
</ul>

<?php

echo "<form action=\"gestion_connect.php#tab_connexions\" name=\"form_affiche_log\" method=\"post\">\n";
echo "Display the Log of connections : <select name=\"duree2\" size=\"1\">\n";
echo "<option ";
if ($duree2 == '20dernieres') echo "selected";
echo " value='20dernieres'>the 20 last</option>\n";
echo "<option ";
if ($duree2 == 2) echo "selected";
echo " value=2>since Two days</option>\n";
echo "<option ";
if ($duree2 == 7) echo "selected";
echo " value=7>since One week</option>\n";
echo "<option ";
if ($duree2 == 15) echo "selected";
echo " value=15 >since Fifteen days</option>\n";
echo "<option ";
if ($duree2 == 30) echo "selected";
echo " value=30>since One month</option>\n";
echo "<option ";
if ($duree2 == 60) echo "selected";
echo " value=60>since Two months</option>\n";
echo "<option ";
if ($duree2 == 183) echo "selected";
echo " value=183>since Six months</option>\n";
echo "<option ";
if ($duree2 == 365) echo "selected";
echo " value=365>since One year</option>\n";
echo "<option ";
if ($duree2 == 'all') echo "selected";
echo " value='all'>since the beginning</option>\n";
echo "</select>\n";
echo " <input type=\"submit\" name=\"Valider\" value=\"Validate\" /><br /><br />\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form>\n";

echo "<div class='noprint' style='float: right; border: 1px solid black; background-color: white; width: 3em; height: 1em; text-align: center;'>\n";
echo "<a href='".$_SERVER['PHP_SELF']."?mode=csv";
echo "'>CSV</a>\n";
echo "</div>\n";

?>

<a name='tab_connexions'></a>
<table class="col" style="width: 90%; margin-left: auto; margin-right: auto; margin-bottom: 32px;" cellpadding="5" cellspacing="0" summary='Table of connections'>
    <!--tr>
        <th class="col">Statut</th>
		<th class="col">Identifiant</th>
        <th class="col">Début session</th>
        <th class="col">Fin session</th>
        <th class="col">Adresse IP et nom de la machine cliente</th>
        <th class="col">Navigateur</th>
        <th class="col">Provenance</th>
    </tr-->

    <tr>
        <!--th class="col"><a href='gestion_connect.php?order_by=statut'>Statut</a></th>
		<th class="col"><a href='gestion_connect.php?order_by=login'>Identifiant</a></th-->
        <th class="col">Statut</th>
		<th class="col">Identifier</th>
        <th class="col">Session beginning</th>
        <th class="col">End of session</th>
        <th class="col"><a href='gestion_connect.php?order_by=ip<?php if(isset($duree2)){echo "&amp;duree2=$duree2";}?>#tab_connexions'>Address IP and name of the machine source</a></th>
        <th class="col">Navigator</th>
        <th class="col">Source</th>
    </tr>

<?php
$requete = '';
$requete1 = '';
if ($duree2 != 'all') {$requete = "where l.START > now() - interval " . $duree2 . " day";}
if ($duree2 == '20dernieres') {$requete1 = "LIMIT 0,20"; $requete='';}
/*
$sql = "select l.LOGIN, concat(prenom, ' ', nom) utili, l.START, l.SESSION_ID, l.REMOTE_ADDR, l.USER_AGENT, l.REFERER,
 l.AUTOCLOSE, l.END, u.email, u.statut
from log l LEFT JOIN utilisateurs u ON l.LOGIN = u.login ".$requete." order by START desc ".$requete1;
*/
$sql = "select l.LOGIN, concat(prenom, ' ', nom) utili, l.START, l.SESSION_ID, l.REMOTE_ADDR, l.USER_AGENT, l.REFERER,
 l.AUTOCLOSE, l.END, u.email, u.statut
from log l LEFT JOIN utilisateurs u ON l.LOGIN = u.login ".$requete;

$sql.=" order by ";
if(isset($_GET['order_by'])) {
	$order_by=$_GET['order_by'];
	/*
	// Seuls les tris sur la table 'log' peuvent fonctionner étant donnée la requête ci-dessus...
	// ... sinon, il faudrait passer par un tableau PHP intermédiaire ou revoir complètement la requête...
	if($order_by=='statut') {
		$sql.="u.statut, ";
	}
	elseif($order_by=='login') {
		$sql.="u.login, ";
	}
	elseif($order_by=='ip') {
	*/
	if($order_by=='ip') {
		$sql.="l.REMOTE_ADDR, ";
	}
	else {
		unset($order_by);
	}
}
$sql.="START desc ".$requete1;

//echo "<tr><td colspan='7'>$sql</td></tr>\n";
//flush();

// $row[0] : log.LOGIN
// $row[1] : USER
// $row[2] : START
// $row[3] : SESSION_ID
// $row[4] : REMOTE_ADDR
// $row[5] : USER_AGENT
// $row[6] : REFERER
// $row[7] : AUTOCLOSE
// $row[8] : END
// $row[9] : EMAIL
// $row[9] : STATUT

$day_now   = date("d");
$month_now = date("m");
$year_now  = date("Y");
$hour_now  = date("H");
$minute_now = date("i");
$now = mktime($hour_now, $minute_now, 0, $month_now, $day_now, $year_now);
$res = sql_query($sql);

$ligne_csv[0] = "statut;login;debut_session;fin_session;adresse_ip;navigateur;provenance\n";
$nb_ligne = 1;

if ($res) {
    for ($i = 0; ($row = sql_row($res, $i)); $i++) {
        $annee_f = substr($row[8],0,4);
        $mois_f =  substr($row[8],5,2);
        $jour_f =  substr($row[8],8,2);
        $heures_f = substr($row[8],11,2);
        $minutes_f = substr($row[8],14,2);
        $secondes_f = substr($row[8],17,2);
        //$date_fin_f = $jour_f."/".$mois_f."/".$annee_f." à ".$heures_f." h ".$minutes_f;
        $date_fin_f = $jour_f."/".$mois_f."/".$annee_f." à ".$heures_f."&nbsp;h&nbsp;".$minutes_f;
        $end_time = mktime($heures_f, $minutes_f, $secondes_f, $mois_f, $jour_f, $annee_f);
        $annee_b = substr($row[2],0,4);
        $mois_b =  substr($row[2],5,2);
        $jour_b =  substr($row[2],8,2);
        $heures_b = substr($row[2],11,2);
        $minutes_b = substr($row[2],14,2);
        $secondes_b = substr($row[2],17,2);
        //$date_debut = $jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b;
        $date_debut = $jour_b."/".$mois_b."/".$annee_b." à ".$heures_b."&nbsp;h&nbsp;".$minutes_b;
        $temp1 = '';
        $temp2 = '';
        if ($end_time > $now) {
            $temp1 = "<span style='color:green'>";
            $temp2 = "</span>";
        }
        if ($row[1] == '') {$row[1] = "<span style='color:red;font-weight:bold'>Unknown user</span>";}

        echo "<tr>\n";
		 echo "<td class=\"col\"><span class='small'>".$temp1.$row[10].$temp2."</span></td>\n";
        echo "<td class=\"col\"><span class='small'>".$temp1.$row[0]."<br />";
		if($row[9]!='') {
			echo "<a href=\"mailto:" .$row[9]. "\">".$row[1]."</a>";
		}
		else {
			echo $row[1];
		}
		echo $temp2."</span></td>\n";
        echo "<td class=\"col\"><span class='small'>".$temp1.$date_debut.$temp2."</span></td>\n";

		//$ligne_csv[$nb_ligne] = "$row[10];$row[0];$date_debut;";
		$ligne_csv[$nb_ligne] = my_ereg_replace("&nbsp;"," ","$row[10];$row[0];$date_debut;");

        if ($row[7] == 4) {
           echo "<td class=\"col\" style=\"color: red;\"><span class='small'><span style='font-weight:bold'>Attempt of connection<br />with erroneous password.</span></span></td>\n";
        } else if ($end_time > $now) {
            echo "<td class=\"col\" style=\"color: green;\"><span class='small'>" .$date_fin_f. "</span></td>\n";
        } else if (($row[7] == 1) or ($row[7] == 2) or ($row[7] == 3)) {
            echo "<td class=\"col\" style=\"color: orange;\"><span class='small'>" .$date_fin_f. "</span></td>\n";
        } else {
            echo "<td class=\"col\"><span class='small'>" .$date_fin_f. "</span></td>";
        }
        if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
            $result_hostbyaddr = " - ".@gethostbyaddr($row[4]);
		}
        else if($active_hostbyaddr == "no_local") {
            if ((substr($row[4],0,3) == 127) or (substr($row[4],0,3) == 10.) or (substr($row[4],0,7) == 192.168)) {
                $result_hostbyaddr = "";
            }
			else{
				$tabip=explode(".",$row[4]);
				if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
					$result_hostbyaddr = "";
				}
				else{
	                $result_hostbyaddr = " - ".@gethostbyaddr($row[4]);
				}
			}
		}
		else{
            $result_hostbyaddr = "";
		}
        echo "<td class=\"col\"><span class='small'>".$temp1.$row[4].$result_hostbyaddr.$temp2. "</span></td>\n";
        echo "<td class=\"col\"><span class='small'>".$temp1. detect_browser($row[5]) .$temp2. "</span></td>\n";
        //echo "<td class=\"col\"><span class='small'>".$temp1. $row[6] .$temp2. "</span></td>\n";
        echo "<td class=\"col\"><span class='small'>";
		if($row[6]=="") {
			echo "&nbsp;";
		}
		else {
			echo $temp1. $row[6] .$temp2;
		}
		echo "</span></td>\n";

		//$ligne_csv[$nb_ligne] .= "$date_fin_f;$result_hostbyaddr;".detect_browser($row[5]).";$row[6]\n";
		$ligne_csv[$nb_ligne] .= my_ereg_replace("&nbsp;"," ","$date_fin_f;$result_hostbyaddr;".detect_browser($row[5]).";$row[6]\n");

        echo "</tr>\n";

		$nb_ligne++;

		flush();
    }
}

$_SESSION['donnees_export_csv_log']=$ligne_csv;

echo "</table>\n";

echo "<p><i>NOTES:</i></p>\n";
echo "<ul>\n";
echo "<li><p>The resolution of address IP in DNS name can slow down the display of this page.<br />
In the case of a server located on a local area network, it may be that no DNS server is able to ensure the resolution IP/NAME.<br />
If waiting weighs you, you can modify the parameter setting of the variable <span style='font-weight:bold'>\$active_hostbyaddr</span> in the file <span style='font-weight:bold'>lib/global.inc.php</span></p>\n";

$texte="<p style='text-align:justify;'>The organization managing the space of public addressing (IP addresses routables) is Internet Assigned Number Authority (IANA). The RFC 1918 defines a space of private addressing making it possible any
organization to allot IP addresses to the machines of its internal network without risk to enter in conflict with a public address IP allocated by IANA. These addresses known as non-routables correspond to the beaches of
following addresses :</p>

<ul>
<li>Classe A : plage de 10.0.0.0 à 10.255.255.255 ;</li>
<li>Classe B : plage de 172.16.0.0 à 172.31.255.255 ;</li>
<li>Classe C : plage de 192.168.0.0 à 192.168.255.55 ;</li>
</ul>

<p style='text-align:justify;'>All machines of an internal network, connected to Internet by the intermediary of a router and not having a public address IP must use an address contained in one of
these beaches. For the small domestic networks, the beach of addresses of 192.168.0.1 à 192.168.0.255 is generally used.</p>";
$tabdiv_infobulle[]=creer_div_infobulle('ip_priv',"Espaces d'adressage","",$texte,"",30,0,'y','y','n','n');

echo "<p>Here are possible values for the variable:</p>
<table class='boireaus' summary='Valeurs de active_hostbyaddr'>
<tr>
	<th>Value</th>
	<th>Significance</th>
</tr>
<tr class='lig1'>
	<td>all</td>
	<td>the opposite resolution of all IP addresses is activated.<br />
	That can result in slownesses with the display of this page.
	</td>
</tr>
<tr class='lig-1'>
	<td>no</td>
	<td>the opposite resolution of IP addresses is deactivated.<br />
	Radical, but all the provided addresses are in IP.</td>
</tr>
<tr class='lig1'>
	<td>no_local</td>
	<td>the resolution reverses local IP addresses (<i>private</i>) is deactivated.<br />
	Only IP addresses of <a href='#' onmouseover=\"afficher_div('ip_priv','y',20,20);\" onclick=\"return false;\">not-private networks</a> are translated into DNS names .</td>
</tr>
</table>
<p>The current value of the variable <span style='font-weight:bold'>\$active_hostbyaddr</span> on your GEPI is: <span style='font-weight:bold'>$active_hostbyaddr</span></p>
</li>\n";
echo "</ul>\n";

echo "</div>\n";

require("../lib/footer.inc.php");
?>
