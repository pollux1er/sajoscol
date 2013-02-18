<?php
/*
 * $Id: edit_responsable.php 8658 2011-11-23 19:47:57Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Eric Lebrun
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

	// Assurons nous que le fichier est accessible en Ècriture
	if (is_writable($filename)) {

		if (!$handle = fopen($filename, 'a')) {
			 echo "Impossible d'ouvrir le fichier ($filename)";
			 exit;
		}

		// Ecrivons quelque chose dans notre fichier.
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Impossible d'Ècrire dans le fichier ($filename)";
			exit;
		}

		//echo "L'Ècriture de ($somecontent) dans le fichier ($filename) a rÈussi";

		fclose($handle);

	} else {
		echo "Le fichier $filename n'est pas accessible en Ècriture.";
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


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Initialisation des variables
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : false);
$action = isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : false);

$msg = '';

$compteur_aff_time=0;
function aff_time() {
	global $compteur_aff_time;

	// Pour tenter de repÈrer ‡ quel niveau cela traine:
	$debug=0;
	if($debug==1) {
		echo "$compteur_aff_time: ".strftime("%D %T")."<br />";
	}

	$compteur_aff_time++;
}

aff_time();

// Si on est en traitement par lot, on sÈlectionne tout de suite la liste des utilisateurs impliquÈs
$error = false;
if ($mode == "classe") {
	$nb_comptes = 0;
	if ($_POST['classe'] == "all") {
		$quels_parents = mysql_query("SELECT distinct(r.login), u.auth_mode " .
				"FROM utilisateurs u, resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
				"u.login = r.login AND r.pers_id = re.pers_id AND " .
				"re.ele_id = e.ele_id AND " .
				"e.login = jec.login AND " .
				"jec.id_classe = c.id)");
		if (!$quels_parents) $msg .= mysql_error();
	} elseif (is_numeric($_POST['classe'])) {
		$quels_parents = mysql_query("SELECT distinct(r.login), u.auth_mode " .
				"FROM utilisateurs u, resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
				"u.login = r.login AND r.pers_id = re.pers_id AND " .
				"re.ele_id = e.ele_id AND " .
				"e.login = jec.login AND " .
				"jec.id_classe = '" . $_POST['classe']."')");
		if (!$quels_parents) $msg .= mysql_error();
	} else {
		$error = true;
		$msg .= "You must select at least a class !<br />";
	}
}

aff_time();

// Trois actions sont possibles depuis cette page : activation, dÈsactivation et suppression.
// L'Èdition se fait directement sur la page de gestion des responsables
if (!$error) {
	if($action) {
		check_token();
	}

	if ($action == "rendre_inactif") {
		// DÈsactivation d'utilisateurs actifs
		if ($mode == "individual") {
			// DÈsactivation pour un utilisateur unique
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE (login = '" . $_GET['parent_login']."' AND etat = 'actif')"), 0);
			if ($test == "0") {
				$msg .= "Error during desactivation of the user : this one does not exist or is already inactive.";
			} else {
				$res = mysql_query("UPDATE utilisateurs SET etat='inactif' WHERE (login = '".$_GET['parent_login']."')");
				updateOnline("UPDATE utilisateurs SET etat='inactif' WHERE (login = '".$_GET['parent_login']."')");
				if ($res) {
					$msg .= "The user ".$_GET['parent_login'] . " was deactivated.";
				} else {
					$msg .= "Error during desactivation of the user.";
				}
			}
		} elseif ($mode == "classe" and !$error) {
			// Pour tous les parents qu'on a dÈj‡ sÈlectionnÈs un peu plus haut, on dÈsactive les comptes
			while ($current_parent = mysql_fetch_object($quels_parents)) {
				$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
				if ($test > 0) {
					// L'utilisateur existe bien dans la tables utilisateurs, on dÈsactive
					$res = mysql_query("UPDATE utilisateurs SET etat = 'inactif' WHERE login = '" . $current_parent->login . "'");
					updateOnline("UPDATE utilisateurs SET etat = 'inactif' WHERE login = '" . $current_parent->login . "'");
					if (!$res) {
						$msg .= "Error during desactivation of the account ".$current_parent->login."<br />";
					} else {
						$nb_comptes++;
					}
				}
			}
			$msg .= "$nb_comptes accounts were deactivated.";
		}
	} elseif ($action == "rendre_actif") {
		// Activation d'utilisateurs prÈalablement dÈsactivÈs
		if ($mode == "individual") {
			// Activation pour un utilisateur unique
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE (login = '" . $_GET['parent_login']."' AND etat = 'inactif')"), 0);
			if ($test == "0") {
				$msg .= "Error during desactivation of the user : this one does not exist or is already active.";
			} else {
				$res = mysql_query("UPDATE utilisateurs SET etat='actif' WHERE (login = '".$_GET['parent_login']."')");
				updateOnline("UPDATE utilisateurs SET etat='actif' WHERE (login = '".$_GET['parent_login']."')");
				if ($res) {
					$msg .= "The user ".$_GET['parent_login'] . " was activated.";
				} else {
					$msg .= "Error during activation of the user.";
				}
			}
		} elseif ($mode == "classe") {
			// Pour tous les parents qu'on a dÈj‡ sÈlectionnÈs un peu plus haut, on dÈsactive les comptes
			while ($current_parent = mysql_fetch_object($quels_parents)) {
				$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
				if ($test > 0) {
					// L'utilisateur existe bien dans la tables utilisateurs, on dÈsactive
					$res = mysql_query("UPDATE utilisateurs SET etat = 'actif' WHERE login = '" . $current_parent->login . "'");
					updateOnline("UPDATE utilisateurs SET etat = 'actif' WHERE login = '" . $current_parent->login . "'");
					if (!$res) {
						$msg .= "Error during activation of the account ".$current_parent->login."<br />";
					} else {
						$nb_comptes++;
					}
				}
			}
			$msg .= "$nb_comptes accounts were activated.";
		}

	} elseif ($action == "supprimer") {
		// Suppression d'un ou plusieurs utilisateurs
		if ($mode == "individual") {
			// Suppression pour un utilisateur unique
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE (login = '" . $_GET['parent_login']."')"), 0);
			if ($test == "0") {
				$msg .= "Error during suppression of the user: this one does not exist.";
			} else {
				$res = mysql_query("DELETE FROM utilisateurs WHERE (login = '".$_GET['parent_login']."')");
				updateOnline("DELETE FROM utilisateurs WHERE (login = '".$_GET['parent_login']."')");
				if ($res) {
					$msg .= "The user ".$_GET['parent_login'] . " was removed.";
					$res2 = mysql_query("UPDATE resp_pers SET login='' WHERE login = '".$_GET['parent_login'] . "'");
					updateOnline("UPDATE resp_pers SET login='' WHERE login = '".$_GET['parent_login'] . "'");
				} else {
					$msg .= "Error during suppression of the user.";
				}
			}
		} elseif ($mode == "classe") {
			// Pour tous les parents qu'on a dÈj‡ sÈlectionnÈs un peu plus haut, on dÈsactive les comptes
			while ($current_parent = mysql_fetch_object($quels_parents)) {
				$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
				if ($test > 0) {
					// L'utilisateur existe bien dans la tables utilisateurs, on dÈsactive
					$res = mysql_query("DELETE FROM utilisateurs WHERE login = '" . $current_parent->login . "'");
					updateOnline("DELETE FROM utilisateurs WHERE login = '" . $current_parent->login . "'");
					if (!$res) {
						$msg .= "Error during activation of the account ".$current_parent->login."<br />";
					} else {
						$res = mysql_query("UPDATE resp_pers SET login = '' WHERE login = '" . $current_parent->login ."'");
						updateOnline("UPDATE resp_pers SET login = '' WHERE login = '" . $current_parent->login ."'");
						$nb_comptes++;
					}
				}
			}
			$msg .= "$nb_comptes accounts were removed.";
		}
	} elseif ($action == "reinit_password") {
		if ($mode != "classe") {
			$msg .= "Error : You must select a class.";
		} elseif ($mode == "classe") {
			if ($_POST['classe'] == "all") {
				$msg .= "You are going to reinitialize the passwords of all the users having the statute 'responsible'.<br />If you are really sure to want to carry out this operation, click on the link below :";
				$msg .= "<br /><a href=\"reset_passwords.php?user_status=responsable&amp;mode=html".add_token_in_url()."\" target='_blank'>RÈinitialiser les mots de passe (Impression HTML)</a> - ou (<a href=\"reset_passwords.php?user_status=responsable&amp;mode=html&amp;affiche_adresse_resp=y".add_token_in_url()."\" target='_blank'>Impression HTML with address</a>)";
				$msg .= "<br /><a href=\"reset_passwords.php?user_status=responsable&amp;mode=csv".add_token_in_url()."\" target='_blank'>RÈinitialiser les mots de passe (Export CSV)</a>";
			} else if (is_numeric($_POST['classe'])) {
				$msg .= "You are going to reinitialize the passwords of all the users having the statute 'responsible' for this class.<br />If you are really sure to want to carry out this operation, click on the link below :";
				$msg .= "<br /><a href=\"reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=html".add_token_in_url()."\" target='_blank'>Reinitialize the passwords (Impression HTML)</a> - ou (<a href=\"reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=html&amp;affiche_adresse_resp=y".add_token_in_url()."\" target='_blank'>Impression HTML with address</a>)";
				$msg .= "<br /><a href=\"reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=csv".add_token_in_url()."\" target='_blank'>Reinitialize the passwords (Export CSV)</a>";
			}
		}
	}elseif ($action == "change_auth_mode") {
		if ($gepiSettings['ldap_write_access'] == "yes") {
			$ldap_write_access = true;
			$ldap_server = new LDAPServer;
		}
		$nb_comptes = 0;
		$reg_auth_mode = (in_array($_POST['reg_auth_mode'], array("gepi", "ldap", "sso"))) ? $_POST['reg_auth_mode'] : "gepi";
		if ($mode != "classe") {
			$msg .= "Error : You must select a class.";
		} elseif ($mode == "classe") {
			while ($current_parent = mysql_fetch_object($quels_parents)) {
				$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
				if ($test > 0) {
					// L'utilisateur existe bien dans la tables utilisateurs, on modifie
					// Si on change le mode d'authentification, il faut quelques opÈrations particuliËres
					$old_auth_mode = $current_parent->auth_mode;
					if ($_POST['reg_auth_mode'] != $old_auth_mode) {
						// On modifie !
						$nb_comptes++;
						$res = mysql_query("UPDATE utilisateurs SET auth_mode = '".$reg_auth_mode."' WHERE login = '".$current_parent->login."'");
updateOnline("UPDATE utilisateurs SET auth_mode = '".$reg_auth_mode."' WHERE login = '".$current_parent->login."'");
						// On regarde si des opÈrations spÈcifiques sont nÈcessaires
						if ($old_auth_mode == "gepi" && ($_POST['reg_auth_mode'] == "ldap" || $_POST['reg_auth_mode'] == "sso")) {
							// On passe du mode Gepi ‡ un mode externe : il faut supprimer le mot de passe
							$oldmd5password = mysql_result(mysql_query("SELECT password FROM utilisateurs WHERE login = '".$current_parent->login."'"), 0);
							mysql_query("UPDATE utilisateurs SET password = '', salt = '' WHERE login = '".$current_parent->login."'");
							updateOnline("UPDATE utilisateurs SET password = '', salt = '' WHERE login = '".$current_parent->login."'");
							// Et si on a un accËs en Ècriture au LDAP, il faut crÈer l'utilisateur !
							if ($ldap_write_access) {
								$create_ldap_user = true;
							}
						} elseif (($old_auth_mode == "sso" || $old_auth_mode == "ldap") && $_POST['reg_auth_mode'] == "gepi") {
							// Passage au mode Gepi, rien de spÈcial ‡ faire, si ce n'est annoncer ‡ l'administrateur
							// qu'il va falloir rÈinitialiser les mots de passe
							$pass_init_required = true;
							// Et si accËs en Ècriture au LDAP, on supprime le compte.
							if ($ldap_write_access) {
								$delete_ldap_user = true;
							}
						}

						// On effectue les opÈrations LDAP
						if (isset($create_ldap_user) && $create_ldap_user) {
							if (!$ldap_server->test_user($current_parent->login)) {
								$parent = mysql_fetch_object(mysql_query("SELECT distinct(r.login), r.nom, r.prenom, r.civilite, r.mel " .
														"FROM resp_pers r WHERE (" .
														"r.login = '" . $current_parent->login."')"));
								$write_ldap_success = $ldap_server->add_user($parent->login, $parent->nom, $parent->prenom, $parent->mel, $parent->civilite, md5(rand()), "responsable");
								// On transfert le mot de passe ‡ la main
								$ldap_server->set_manual_password($current_parent->login, "{MD5}".base64_encode(pack("H*",$oldmd5password)));
							}
						}
						if (isset($delete_ldap_user) && $delete_ldap_user) {
							if (!$ldap_server->test_user($current_parent->login)) {
								// L'utilisateur n'a pas ÈtÈ trouvÈ dans l'annuaire.
								$write_ldap_success = true;
							} else {
								$write_ldap_success = $ldap_server->delete_user($current_parent->login);
							}
						}

					}
				}
			}
			$msg .= "$nb_comptes comptes ont ÈtÈ modifiÈs.";
			if (isset($pass_init_required) && $pass_init_required) {
				$msg .= "<br/>Caution ! Modifications applied require reinitialization of passwords of users !";
			}
		}
	}
}

aff_time();

//**************** EN-TETE *****************
$titre_page = "Modify responsibles accounts ";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

aff_time();

?>
<p class='bold'>
<a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a> |
<a href="create_responsable.php"> Add new accounts</a>
<?php

	$quels_parents = mysql_query("SELECT u.*, r.pers_id FROM utilisateurs u, resp_pers r WHERE (u.statut = 'responsable' AND r.login = u.login) ORDER BY u.nom,u.prenom LIMIT 1");
	if(mysql_num_rows($quels_parents)==0){
		echo "<p>No account responsible exists yet.<br />You can add responsibles accounts using the link above.</p>\n";
		require("../lib/footer.inc.php");
		die;
	}
	echo " | <a href='impression_bienvenue.php?mode=responsable'>welcome Cards </a>";

	echo " | <a href='import_prof_csv.php?export_statut=responsable'>Export CSV</a>";

	echo "</p>\n";

	aff_time();

	echo "<form action='edit_responsable.php' method='post'>\n";
	echo add_token_field();

	echo "<p style='font-weight:bold;'>Actions by batch for the existing responsibles accounts  : </p>\n";
	flush();
	echo "<blockquote>\n";
	echo "<p>\n";
	echo "<select name='classe' size='1'>\n";
	echo "<option value='none'>Select a class</option>\n";
	echo "<option value='all'>All classes</option>\n";

	//$quelles_classes = mysql_query("SELECT id,classe FROM classes ORDER BY classe");
	$quelles_classes = mysql_query("SELECT DISTINCT c.id,c.classe FROM classes c,
																		j_eleves_classes jec,
																		eleves e,
																		responsables2 r,
																		resp_pers rp,
																		utilisateurs u
										WHERE jec.login=e.login AND
												e.ele_id=r.ele_id AND
												r.pers_id=rp.pers_id AND
												rp.login=u.login AND
												jec.id_classe=c.id
										ORDER BY classe");

	while ($current_classe = mysql_fetch_object($quelles_classes)) {
		echo "<option value='".$current_classe->id."'>".$current_classe->classe."</option>\n";
	}
	//flush();
	echo "</select>\n";
	echo "<br />\n";
	aff_time();
	flush();


	echo "<input type='hidden' name='mode' value='classe' />\n";
	echo "<input type='radio' name='action' id='action_rendre_inactif' value='rendre_inactif' /> <label for='action_rendre_inactif' style='cursor:pointer;'>Make inactive</label>\n";
	echo "<input type='radio' name='action' id='action_rendre_actif' value='rendre_actif' style='margin-left: 20px;'/> <label for='action_rendre_actif' style='cursor:pointer;'>Make active </label>\n";
	if ($session_gepi->auth_locale || $gepiSettings['ldap_write_access']) {
		echo "<input type='radio' name='action' id='action_reinit_password' value='reinit_password' style='margin-left: 20px;'/> <label for='action_reinit_password' style='cursor:pointer;'>Reinitialize passwords</label>\n";
	}
	echo "<input type='radio' name='action' id='action_supprimer' value='supprimer' style='margin-left: 20px;' /> <label for='action_supprimer' style='cursor:pointer;'>Delete</label><br />\n";
	echo "<input type='radio' name='action' value='change_auth_mode' /> Modify authentification : ";
	?>
	<select id="select_auth_mode" name="reg_auth_mode" size="1">
	<option value='gepi'>Local (base Gepi)</option>
	<option value='ldap'>LDAP</option>
	<option value='sso'>SSO (Cas, LCS, LemonLDAP)</option>
	</select>
	<?php
	echo "<br />\n";
	echo "&nbsp;<input type='submit' name='Valider' value='Validate' />\n";
	echo "</p>\n";

	echo "</blockquote>\n";
	echo "</form>\n";


	echo "<p><br /></p>\n";

	echo "<p><b>List of existing responsibles accounts </b> :</p>\n";
	echo "<blockquote>\n";

	$afficher_tous_les_resp=isset($_POST['afficher_tous_les_resp']) ? $_POST['afficher_tous_les_resp'] : "n";
	$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
	$critere_recherche=preg_replace("/[^a-zA-Z¿ƒ¬…» ÀŒœ‘÷Ÿ€‹Ωº«Á‡‰‚ÈËÍÎÓÔÙˆ˘˚¸_ -]/", "", $critere_recherche);
  	$critere_recherche_login=isset($_POST['critere_recherche_login']) ? $_POST['critere_recherche_login'] : "";
	$critere_recherche_login=preg_replace("/[^a-zA-Z¿ƒ¬…» ÀŒœ‘÷Ÿ€‹Ωº«Á‡‰‚ÈËÍÎÓÔÙˆ˘˚¸_ -]/", "", $critere_recherche_login);

	//====================================

	echo "<form enctype='multipart/form-data' name='form_rech' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<table style='border:1px solid black;' summary=\"Filtering\">\n";
	echo "<tr>\n";
	echo "<td valign='top' rowspan='4'>\n";
	echo "Filtering:";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='submit' name='filtrage' value='Display' /> responsibles having a login of which the <b> name</b> contains: ";
	echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='submit' name='filtrage' value='Display' /> responsibles having a <b>login</b> who contains: ";
	echo "<input type='text' name='critere_recherche_login' value='$critere_recherche_login' />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td>\n";
	echo "ou";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='button' name='afficher_tous' value='Display all responsibles having a login' onClick=\"document.getElementById('afficher_tous_les_resp').value='y'; document.form_rech.submit();\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='afficher_tous_les_resp' id='afficher_tous_les_resp' value='n' />\n";
	echo "</form>\n";
	//====================================
	echo "<br />\n";

?>
<!--table border="1"-->
<table class='boireaus' border='1' summary="List of existing accounts">
<tr>
	<th>Identifier</th>
	<th>Name First name</th>
	<th>Responsible of</th>
	<th>State</th>
	<th>Actions</th>
</tr>
<?php
//$quels_parents = mysql_query("SELECT u.*, r.pers_id FROM utilisateurs u, resp_pers r WHERE (u.statut = 'responsable' AND r.login = u.login) ORDER BY u.nom,u.prenom");

$sql="SELECT u.*, r.pers_id FROM utilisateurs u, resp_pers r WHERE (u.statut = 'responsable' AND r.login = u.login";

if($afficher_tous_les_resp!='y'){
	if($critere_recherche!=""){
		$sql.=" AND u.nom like '%".$critere_recherche."%'";
	} else {
		if($critere_recherche_login!=""){
			$sql.=" AND u.login like '%".$critere_recherche_login."%'";
		}
    }
}
$sql.=") ORDER BY u.nom,u.prenom";

// Effectif sans login avec filtrage sur le nom:
//$nb1 = mysql_num_rows(mysql_query($sql));

if($afficher_tous_les_resp!='y'){
	if(($critere_recherche=="")&&($critere_recherche_login=="")) {
		$sql.=" LIMIT 20";
	}
}

$quels_parents = mysql_query($sql);

// Effectif sans login avec filtrage sur le nom:
$nb1 = mysql_num_rows($quels_parents);

$alt=1;
while ($current_parent = mysql_fetch_object($quels_parents)) {
	$alt=$alt*(-1);
	echo "<tr class='lig$alt' style='text-align:center;'>\n";
		echo "<td>";
			echo "<a href='../responsables/modify_resp.php?pers_id=".$current_parent->pers_id."&amp;journal_connexions=y'>".$current_parent->login."</a>";
		echo "</td>\n";
		echo "<td>";
			echo $current_parent->nom . " " . $current_parent->prenom;
		echo "</td>\n";
		echo "<td>";
		$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM eleves e,
												j_eleves_classes jec,
												classes c,
												responsables2 r
											WHERE e.login=jec.login AND
												jec.id_classe=c.id AND
												r.ele_id=e.ele_id AND
												r.pers_id='$current_parent->pers_id' AND
												(r.resp_legal='1' OR r.resp_legal='2')
											ORDER BY e.nom,e.prenom";
		$res_enfants=mysql_query($sql);
		//echo "$sql<br />";
		if(mysql_num_rows($res_enfants)==0){
			echo "<span style='color:red;' title='No student, or more of the students which are not any more in any class'>No student</span>";
		}
		else{
			while($current_enfant=mysql_fetch_object($res_enfants)){
				echo ucfirst(strtolower($current_enfant->prenom))." ".strtoupper($current_enfant->nom)." (<i>".$current_enfant->classe."</i>)<br />\n";
			}
		}
		echo "</td>\n";
		echo "<td align='center'>";
			if ($current_parent->etat == "actif") {
				echo "<font color='green'>".$current_parent->etat."</font>";
				if($current_parent->login!='') {
					echo "<br />";
					echo "<a href='edit_responsable.php?action=rendre_inactif&amp;mode=individual&amp;parent_login=".$current_parent->login.add_token_in_url()."'>Deactivate";
				}
			} else {
				echo "<font color='red'>".$current_parent->etat."</font>";
				if($current_parent->login!='') {
					echo "<br />";
					echo "<a href='edit_responsable.php?action=rendre_actif&amp;mode=individual&amp;parent_login=".$current_parent->login.add_token_in_url()."'>Activate";
				}
			}
			echo "</a>";
		echo "</td>\n";
		echo "<td>";
		echo "<a href='edit_responsable.php?action=supprimer&amp;mode=individual&amp;parent_login=".$current_parent->login.add_token_in_url()."' onclick=\"javascript:return confirm('Are you sure to want to remove the user ?')\">Delete</a>";

		if($current_parent->etat == "actif" && ($current_parent->auth_mode == "gepi" || $gepiSettings['ldap_write_access'] == "yes")) {
			echo "<br />";
			echo "Reinitialize the password : <a href=\"reset_passwords.php?user_login=".$current_parent->login."&amp;user_status=responsable&amp;mode=html".add_token_in_url()."\" onclick=\"javascript:return confirm('Are you sure to want to carry out this operation ?\\n This one is irreversible, and will reinitialize the password of the
user with an alpha-numeric password generated by chance.\\n By clicking on OK, you will launch the procedure, which will generate a page containing the welcome card to be printed immediately for distribution with the user concerned.')\" target='_blank'>Randomly</a>";

			echo " - <a href=\"reset_passwords.php?user_login=".$current_parent->login."&amp;user_status=responsable&amp;mode=html&amp;affiche_adresse_resp=y".add_token_in_url()."\" onclick=\"javascript:return confirm('Are you sure to want to carry out this operation ?\\n This one is irreversible, and will reinitialize the password of the
user with an alpha-numeric password generated by chance.\\n By clicking on OK, you will launch the procedure, which will generate a page containing the welcome card to be printed immediately for distribution with l\' user concerned.')\" target='_blank'>Random. with&nbsp;adress</a>";

			echo " - <a href=\"change_pwd.php?user_login=".$current_parent->login.add_token_in_url()."\" onclick=\"javascript:return confirm('Are you sure to want to carry out this operation ?\\n This one will rÈinitialisera the password of the user with a password which you will choose.\\n While clicking on OK, you will launch a page which will require of you to type a password and to validate it.')\" target='_blank'>chosen </a>";
		}
		echo "</td>\n";
	echo "</tr>\n";
	flush();
}
echo "</table>\n";
aff_time();
echo "</blockquote>\n";

?>

<?php
if (mysql_num_rows($quels_parents) == "0") {
	echo "<p>To create new accesses accounts associated to the students responsibles defined in Gepi, you must click on the link 'Add new accounts' above.</p>";
}
require("../lib/footer.inc.php");
?>