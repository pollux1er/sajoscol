<?php
/*
 * $Id: create_eleve.php 8663 2011-11-26 09:07:12Z crob $
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


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Initialisation des variables

$create_mode = isset($_POST["mode"]) ? $_POST["mode"] : NULL;
$_POST['reg_auth_mode'] = (!isset($_POST['reg_auth_mode']) OR !in_array($_POST['reg_auth_mode'], array("auth_locale", "auth_ldap", "auth_sso"))) ? "auth_locale" : $_POST['reg_auth_mode'];

$mdp_INE=isset($_POST["mdp_INE"]) ? $_POST["mdp_INE"] : (isset($_GET["mdp_INE"]) ? $_GET["mdp_INE"] : NULL);

if ($create_mode == "classe" OR $create_mode == "individual") {
	// On a une demande de création, on continue
	check_token();

	// On veut alimenter la variable $quels_eleves avec un résultat mysql qui contient
	// la liste des élèves pour lesquels on veut créer un compte
	$error = false;
	$msg = "";
	if ($create_mode == "individual") {
		$test = mysql_query("SELECT count(e.login) FROM eleves e WHERE (e.login = '" . $_POST['eleve_login'] ."')");
		if (mysql_result($test, 0) == "0") {
			$error = true;
			$msg .= "Error during creation of the user : no student with this login was found !<br />";
		} else {
			$quels_eleves = mysql_query("SELECT e.* FROM eleves e WHERE (" .
				"e.login = '" . $_POST['eleve_login'] ."')");
		}
	} else {
		// On est en mode 'classe'
		if ($_POST['classe'] == "all") {
			$quels_eleves = mysql_query("SELECT distinct(e.login), e.nom, e.prenom, e.sexe, e.email " .
					"FROM classes c, j_eleves_classes jec, eleves e WHERE (" .
					"e.login = jec.login AND " .
					"jec.id_classe = c.id)");
			if (!$quels_eleves) $msg .= mysql_error();
		} elseif (is_numeric($_POST['classe'])) {
			$quels_eleves = mysql_query("SELECT distinct(e.login), e.nom, e.prenom, e.sexe, e.email " .
					"FROM classes c, j_eleves_classes jec, eleves e WHERE (" .
					"e.login = jec.login AND " .
					"jec.id_classe = '" . $_POST['classe']."')");
			if (!$quels_eleves) $msg .= mysql_error();
		} else {
			$error = true;
			$msg .= "You must select at least a class !<br />";
		}
	}

	if (!$error) {
		//check_token();

		$nb_comptes_preexistants=0;

		$nb_comptes = 0;
		while ($current_eleve = mysql_fetch_object($quels_eleves)) {
			// Création du compte utilisateur pour l'élève considéré
			$reg = true;
			$civilite = '';
			if ($current_eleve->sexe == "M") {
				$civilite = "M.";
			} elseif ($current_eleve->sexe == "F") {
				$civilite = "Mlle";
			}

			// Si on a un accès LDAP en écriture, on créé le compte sur le LDAP
			// On ne procède que si le LDAP est configuré en écriture, qu'on a activé
			// l'auth LDAP ou SSO, et que c'est un de ces deux modes qui a été choisi pour cet utilisateur.
			if (LDAPServer::is_setup() && $gepiSettings["ldap_write_access"] == "yes" && ($session_gepi->auth_ldap || $session_gepi->auth_sso) && ($_POST['reg_auth_mode'] == 'auth_ldap' || $_POST['reg_auth_mode'] == 'auth_sso')) {
				$write_ldap = true;
				$write_ldap_success = false;
				// On tente de créer l'utilisateur sur l'annuaire LDAP
				$ldap_server = new LDAPServer();
				if ($ldap_server->test_user($current_eleve->login)) {
					// L'utilisateur a été trouvé dans l'annuaire. On ne l'enregistre pas.
					$write_ldap_success = true;
					$msg.= "The user could not be added to directory LDAP, because it is already present there. It nevertheless will be created in the Gepi base.";
				} else {
					$write_ldap_success = $ldap_server->add_user($current_eleve->login, $current_eleve->nom, $current_eleve->prenom, $current_eleve->email, $civilite, '', 'eleve');
				}
			} else {
				$write_ldap = false;
			}


			if (!$write_ldap || ($write_ldap && $write_ldap_success)) {
				if ($_POST['reg_auth_mode'] == "auth_locale") {
					$reg_auth = "gepi";
				} elseif ($_POST['reg_auth_mode'] == "auth_ldap") {
					$reg_auth = "ldap";
				} elseif ($_POST['reg_auth_mode'] == "auth_sso") {
					$reg_auth = "sso";
				}

				$sql="SELECT 1=1 FROM utilisateurs WHERE login='".$current_eleve->login."';";
				//echo "$sql<br />";
				$test_existence_compte=mysql_query($sql);
				if(mysql_num_rows($test_existence_compte)==0) {
					$reg = mysql_query("INSERT INTO utilisateurs SET " .
							"login = '" . $current_eleve->login . "', " .
							"nom = '" . addslashes($current_eleve->nom) . "', " .
							"prenom = '". addslashes($current_eleve->prenom) ."', " .
							"password = '', " .
							"civilite = '" . $civilite."', " .
							"email = '" . $current_eleve->email . "', " .
							"statut = 'eleve', " .
							"etat = 'actif', " .
							"auth_mode = '".$reg_auth."', ".
							"change_mdp = 'n'");
							
							updateOnline("INSERT INTO utilisateurs SET " .
							"login = '" . $current_eleve->login . "', " .
							"nom = '" . addslashes($current_eleve->nom) . "', " .
							"prenom = '". addslashes($current_eleve->prenom) ."', " .
							"password = '', " .
							"civilite = '" . $civilite."', " .
							"email = '" . $current_eleve->email . "', " .
							"statut = 'eleve', " .
							"etat = 'actif', " .
							"auth_mode = '".$reg_auth."', ".
							"change_mdp = 'n'");

					if (!$reg) {
						$msg .= "Error during creation of the account ".$current_eleve->login."<br />";
					} else {
						$nb_comptes++;
					}
				}
				else {
					// On compte les comptes existants
					$nb_comptes_preexistants++;
				}
			} else {
				$msg .= "Error during creation of the account ".$current_eleve->login." : the user could not be created on directory LDAP.<br />";

			}
		}
		if ($nb_comptes == 1) {
			$msg .= "An account was created successfully.<br />";
		} elseif ($nb_comptes > 1) {
			$msg .= $nb_comptes." accounts were created successfully.<br />";
		}
		if ($nb_comptes > 0 && ($_POST['reg_auth_mode'] == "auth_locale" || $gepiSettings['ldap_write_access'] == "yes")) {

			if(isset($mdp_INE)) {
				$chaine_mdp_INE="&amp;mdp_INE=$mdp_INE";
			}
			else {
				$chaine_mdp_INE="";
			}

			if ($create_mode == "individual") {
				// Mode de création de compte individuel. On fait un lien spécifique pour la fiche de bienvenue
	            $msg .= "<a href='reset_passwords.php?user_login=".$_POST['eleve_login']."$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Print the card 'identifiers'</a>";
			} else {
				// On est ici en mode de création par classe
				// Si on opère sur toutes les classes, on ne spécifie aucune classe

            	if ($_POST['classe'] == "all") {
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;mode=html$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Print the card(s) 'identifiers' (Impression HTML)</a>";
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;mode=csv$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Print the card(s) 'identifiers' (Export CSV)</a>";
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;mode=pdf$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Print the card(s) 'identifiers' (Impression PDF)</a>";
				} elseif (is_numeric($_POST['classe'])) {
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;user_classe=".$_POST['classe']."&amp;mode=html$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Print the card(s) 'identifiers' (Impression HTML)</a>";
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;user_classe=".$_POST['classe']."&amp;mode=csv$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Print the card(s) 'identifiers' (Export CSV)</a>";
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;user_classe=".$_POST['classe']."&amp;mode=pdf$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Print the card(s) 'identifiers' (Impression PDF)</a>";
				}
			}
			$msg .= "<br />You must carry out this operation now !";
		} else {
			if ($nb_comptes > 0) {
				$msg .= "You created accounts of access in mode SSO or LDAP, but without to have configured access LDAP in writing. Consequently, you cannot generate password for the users.<br />";
			}
		}


		if($nb_comptes_preexistants>0) {
			if($nb_comptes_preexistants==1) {
				$msg.="An account existed already for the selection.<br />";
			}
			else {
				$msg.="$nb_comptes_preexistants accounts existed already for the selection.<br />";
			}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Create students accounts";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();
?>
<p class='bold'>
<a href="edit_eleve.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<?php
$quels_eleves = mysql_query("SELECT e.* FROM eleves e LEFT JOIN utilisateurs u ON e.login=u.login WHERE (" .
		"u.login IS NULL) " .
		"ORDER BY e.nom,e.prenom");
$nb = mysql_num_rows($quels_eleves);
if($nb==0){
	echo "<p>All the students have an user account , or no student was still created.</p>\n";
}
else{
	//echo "<p>Les $nb élèves ci-dessous n'ont pas encore de compte d'accès à Gepi.</p>\n";
	echo "<p>$nb students do not have yet an account of access to Gepi.</p>\n";

	if (!$session_gepi->auth_locale && $gepiSettings['ldap_write_access'] != "yes") {
		echo "<p><b>Note :</b> You use an external authentification with Gepi (LDAP or SSO) without to have defined access in writing in directory LDAP. No password will thus be assigned with the users that you are on the
point of creating. Be certain to generate the login according in the same format as for your source of authentification SSO.</p>\n";
	}

	echo "<p><b>Create accounts by batch</b> :</p>\n";
	echo "<blockquote>\n";

	echo "<p>Select the mode of authentification applied to the accounts&nbsp;:</p>\n";
	echo "<form action='create_eleve.php' method='post'>\n";
	echo add_token_field();
	echo "<select name='reg_auth_mode' size='1'>\n";
	if ($session_gepi->auth_locale) {
		echo "<option value='auth_locale'>Local authentification (base Gepi)</option>\n";
	}
	if ($session_gepi->auth_ldap) {
		echo "<option value='auth_ldap'>Authentification LDAP</option>\n";
	}
	if ($session_gepi->auth_sso) {
		echo "<option value='auth_sso'";
		if((getSettingValue('use_sso')=="lcs")||(getSettingValue('auth_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")||(getSettingValue('auth_sso')=="ldap_scribe")) {
			echo " selected='selected'";
		}
		echo ">Single authentification (SSO)</option>\n";
	}
	echo "</select>\n";

	echo "<p>Select a class or the whole of the classes then click on 'validate'.</p>\n";


	echo "<input type='hidden' name='mode' value='classe' />\n";
	echo "<select name='classe' size='1'>\n";
	echo "<option value='none'>Select a class</option>\n";
	echo "<option value='all'>All classes</option>\n";

	$quelles_classes = mysql_query("SELECT id,classe FROM classes ORDER BY classe");
	while ($current_classe = mysql_fetch_object($quelles_classes)) {
		echo "<option value='".$current_classe->id."'>".$current_classe->classe."</option>\n";
	}
	echo "</select>\n";

	echo "<input type='submit' name='Valider' value='Validate' />\n";

	echo "<p><input type='checkbox' name='mdp_INE' id='mdp_INE' value='y' /> <label for='mdp_INE' style='cursor:pointer'>Use the national number of the student (<i>INE</i>) as initial password when it is indicated.</label></p>\n";

	echo "</form>\n";

	include("randpass.php");

	echo "<p style='font-size:small;'>During creation, the accounts receive a random password selected among the following characters: ";
	if (LOWER_AND_UPPER) {
		if(EXCLURE_CARACT_CONFUS) {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		}
		else {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		}
	} else {
		if(EXCLURE_CARACT_CONFUS) {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		}
		else {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		}
	}
	$cpt=0;
	foreach($alphabet as $key => $value) {
		if($cpt>0) {echo ", ";}
		echo $value;
		$cpt++;
	}

	if(EXCLURE_CARACT_CONFUS) {
		$cpt=2;
	}
	else {
		$cpt=0;
	}
	for($i=$cpt;$i<=9;$i++) {
		echo ", $i";
	}
	echo ".</p>\n";

	echo "</blockquote>\n";

	echo "<br />\n";



	echo "<p><b>Create accounts individually</b> :</p>\n";
	echo "<blockquote>\n";

	$afficher_tous_les_eleves=isset($_POST['afficher_tous_les_eleves']) ? $_POST['afficher_tous_les_eleves'] : "n";
	$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
	$critere_recherche=preg_replace("/[^a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü_ -]/", "", $critere_recherche);


	$sql="SELECT e.* FROM eleves e LEFT JOIN utilisateurs u ON e.login=u.login WHERE (u.login IS NULL";
	if($afficher_tous_les_eleves!='y'){
		if($critere_recherche!=""){
			$sql.=" AND e.nom like '%".$critere_recherche."%'";
		}
	}
	$sql.=") ORDER BY e.nom,e.prenom";
	if($afficher_tous_les_eleves!='y'){
		if($critere_recherche==""){
			$sql.=" LIMIT 20";
		}
	}
	//echo "$sql<br />";
	$quels_eleves = mysql_query($sql);
	$nb2=mysql_num_rows($quels_eleves);


	echo "<p>";
	if(($afficher_tous_les_eleves!='y')&&($critere_recherche=="")){
		echo "At the maximum $nb2 students are posted below (<i>to limit the time of loading of the page</i>).<br />\n";
	}
	echo "Use the form of research to adapt research.";
	echo "</p>\n";

	//====================================
	echo "<form enctype='multipart/form-data' name='form_rech' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<table style='border:1px solid black;' summary=\"Filtrage\">\n";
	echo "<tr>\n";
	echo "<td valign='top' rowspan='3'>\n";
	echo "Filtrage:";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='submit' name='filtrage' value='Display' /> students without login of which the <b>name</b> contains: ";
	echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "ou";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='button' name='afficher_tous' value='Display all students without login' onClick=\"document.getElementById('afficher_tous_les_eleves').value='y'; document.form_rech.submit();\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='afficher_tous_les_eleves' id='afficher_tous_les_eleves' value='n' />\n";
	echo "</form>\n";
	//====================================
	echo "<br />\n";


	echo "<p>Click on the button 'Create' of a student to create an associated account.</p>\n";
	echo "<form id='form_create_one_eleve' action='create_eleve.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='mode' value='individual' />\n";
	echo "<input type='hidden' name='mdp_INE' id='indiv_mdp_INE' value='' />\n";
	echo "<input id='eleve_login' type='hidden' name='eleve_login' value='' />\n";
	echo "<input type='hidden' name='critere_recherche' value='$critere_recherche' />\n";
	echo "<input type='hidden' name='afficher_tous_les_eleves' value='$afficher_tous_les_eleves' />\n";

	// Sélection du mode d'authentification
	echo "<p>Mode d'authentification : <select name='reg_auth_mode' size='1'>";
	if ($session_gepi->auth_locale) {
		echo "<option value='auth_locale'>Local authentification (base Gepi)</option>";
	}
	if ($session_gepi->auth_ldap) {
		echo "<option value='auth_ldap'>Authentification LDAP</option>";
	}
	if ($session_gepi->auth_sso) {
		echo "<option value='auth_sso'";
		if((getSettingValue('use_sso')=="lcs")||(getSettingValue('auth_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")||(getSettingValue('auth_sso')=="ldap_scribe")) {
			echo " selected='selected'";
		}
		echo ">Single authentification (SSO)</option>\n";
	}
	echo "</select>";
	echo "</p>";

	echo "<table class='boireaus' border='1' summary='Create'>\n";
	$alt=1;
	while ($current_eleve = mysql_fetch_object($quels_eleves)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input type='submit' value='Create' onclick=\"$('eleve_login').value='".$current_eleve->login."';$('indiv_mdp_INE').value='n'; $('form_create_one_eleve').submit();\" />\n";
			echo "</td>\n";

			echo "<td>\n";
			echo "<input type='submit' value=\"Create according to INE\" onclick=\"$('eleve_login').value='".$current_eleve->login."';$('indiv_mdp_INE').value='y'; $('form_create_one_eleve').submit();\" />\n";
			//echo "<input type='submit' value=\"Créer d'après INE\" onclick=\"$('eleve_login').value='".$current_eleve->login."';$('indiv_mdp_INE').value='y';\" />\n";
			echo "</td>\n";

			echo "<td>".$current_eleve->nom." ".$current_eleve->prenom."</td>\n";


			echo "<td>\n";
			$tmp_class=get_class_from_ele_login($current_eleve->login);
			if(isset($tmp_class['liste'])) {
				echo $tmp_class['liste'];
			}
			else {
				echo "<span style='color:red;'>None</span>";
			}
			echo "</td>\n";

		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</form>";
	echo "</blockquote>\n";
}
require("../lib/footer.inc.php");
?>