<?php
/*
* $Id: modify_user.php 8386 2011-09-29 15:10:28Z crob $
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
$affiche_connexion = 'yes';
$niveau_arbo = 1;
require_once("../lib/initialisations.inc.php");
 function saveAction($sql, $file = null) {/*
	if(is_null($file))
		$filename = '../responsables/responsable.txt';
	else
		$filename = $file;

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
	//$fp = fsockopen('www.sajoscol.net', 80);
	//if($fp) {
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
	}*/
	
}
function sendSms($num, $msg) {
	$action = "sendsms";
	$userid = "726e02c0-b0cc-45d2-8e28-928c9fe84d0e";
	$password = "25fevrier";
	$sender = "SASSE";
	$to = "237" . $num;
	$msg = urlencode($msg);
	$url = "http://iYam.mobi/apiv1/?";
	$urlsend = "action=" . $action . "&userid=" . $userid . "&password=" . $password . "&sender=" . $sender . "&to=" . $to . "&msg=" . $msg;
	$sendtosend = $url . $urlsend;
	$return = file_get_contents($urlsend);
	$response = json_decode($return);
	if(@$response->status == "success") {
		return true;
	} else {
		saveAction($urlsend, '../sms/sms.txt');
		return false;
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
$user_login = isset($_POST["user_login"]) ? $_POST["user_login"] : (isset($_GET["user_login"]) ? $_GET["user_login"] : NULL);
$msg = '';

$journal_connexions=isset($_POST['journal_connexions']) ? $_POST['journal_connexions'] : (isset($_GET['journal_connexions']) ? $_GET['journal_connexions'] : 'n');
$duree=isset($_POST['duree']) ? $_POST['duree'] : NULL;

// pour module trombinoscope
$photo_largeur_max=150;
$photo_hauteur_max=150;

function redimensionne_image($photo) {
	global $photo_largeur_max, $photo_hauteur_max;

	// prendre les informations sur l'image
	$info_image=getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur=$info_image[0];
	$hauteur=$info_image[1];

	// calcule le ratio de redimensionnement
	$ratio_l=$largeur/$photo_largeur_max;
	$ratio_h=$hauteur/$photo_hauteur_max;
	$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur=round($largeur/$ratio);
	$nouvelle_hauteur=round($hauteur/$ratio);

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

// fonction de sécuritée
// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
if(empty($_SESSION['uid_prime'])) {
	$_SESSION['uid_prime']='';
}

if (empty($_GET['uid_post']) and empty($_POST['uid_post'])) {
	$uid_post='';
}
else {
	if (isset($_GET['uid_post'])) {
		$uid_post=$_GET['uid_post'];
	}
	if (isset($_POST['uid_post'])) {
		$uid_post=$_POST['uid_post'];
	}
}

$uid = md5(uniqid(microtime(), 1));
// on remplace les %20 par des espaces
$uid_post = preg_replace('/%20/',' ',$uid_post);
if($uid_post===$_SESSION['uid_prime']) {
	$valide_form = 'oui';
}
else {
	$valide_form = 'non';
}
$_SESSION['uid_prime'] = $uid;
// fin de la fonction de sécurité

// fin pour module trombinoscope

if (isset($_POST['valid']) and ($_POST['valid'] == "yes")) {
check_token();
//------------------------------------------------------
//--- Partie retirée par Thomas Belliard
// Cas LCS : on teste s'il s'agit d'un utilisateur local ou non
//	if (getSettingValue("use_sso") == "lcs"){
//		if ($_POST['is_lcs'] == "y") {
//			$is_pwd = 'n';
//		}
//		else {
//			$is_pwd = 'y';
//		}
//	}elseif(getSettingValue("use_sso") == 'cas'){
//
//		$is_pwd = 'n';
//
//	}
//	else {
//		$is_pwd = "y";
//	}
//------------------------------------------------------

	// On teste si on doit enregistrer un mot de passe ou non :
	if ($_POST['reg_auth_mode'] == "gepi" || $gepiSettings['ldap_write_access'] == "yes") {
		$is_pwd = "y";
	} else {
		$is_pwd = "n";
	}


	if ($_POST['reg_nom'] == '')  {
		$msg = "Please enter a name for the user !";
	}
	else {
		$k = 0;
		while ($k < $_POST['max_mat']) {
			$temp = "matiere_".$k;
			$reg_matiere[$k] = $_POST[$temp];
			$k++;
		}

		//
		// actions si un nouvel utilisateur a été défini
		//

		$temoin_ajout_ou_modif_ok="n";

		if ((isset($_POST['new_login'])) and ($_POST['new_login']!='') and (preg_match("/^[a-zA-Z_]{1}[a-zA-Z0-9_.]{0,".($longmax_login-1)."}$/", $_POST['new_login'])) ) {
			// Modif Thomas : essayons d'accepter des logins sensibles à la casse, pour mieux s'adapter aux sources externes (LDAP).
			//$_POST['new_login'] = strtoupper($_POST['new_login']);
			$reg_password_c = md5($NON_PROTECT['password1']);
			$resultat = "";
			if (($_POST['no_anti_inject_password1'] != $_POST['reg_password2']) and ($is_pwd == "y")) {
				$msg = "Error during typing : the two passwords are not identical, please restart !";
			} else if ((!(verif_mot_de_passe($_POST['no_anti_inject_password1'],0)))  and ($is_pwd == "y")) {
				$msg = "Error during typing of the password (<em> see the recommendations</em>), please restart !";
				if((isset($info_verif_mot_de_passe))&&($info_verif_mot_de_passe!="")) {$msg.="<br />".$info_verif_mot_de_passe;}
			} else {
				// Le teste suivant détecte si un utilisateur existe avec le même login (insensible à la casse)
				$test = mysql_query("SELECT login FROM utilisateurs WHERE (login = '".$_POST['new_login']."' OR login = '".strtoupper($_POST['new_login'])."')");
				$nombreligne = mysql_num_rows($test);
				if ($nombreligne != 0) {
					$resultat = "NON";
					$msg = "*** Caution ! A user having the same identifier already exists. Impossible recording ! ***";
				}
				if ($resultat != "NON") {
					// On enregistre l'utilisateur

					// Si on a activé l'accès LDAP en écriture, on commence par ça.
					// En cas d'échec, l'enregistrement ne sera pas poursuivi.

					// On ne continue que si le LDAP est configuré en écriture, qu'on a activé
					// l'auth LDAP ou SSO, et que c'est un de ces deux modes qui a été choisi pour cet utilisateur.
					if (LDAPServer::is_setup() && $gepiSettings["ldap_write_access"] == "yes" && ($session_gepi->auth_ldap || $session_gepi->auth_sso) && ($_POST['reg_auth_mode'] == 'ldap' || $_POST['reg_auth_mode'] == 'sso')) {
						$write_ldap = true;
						$write_ldap_success = false;
						// On tente de créer l'utilisateur sur l'annuaire LDAP
						$ldap_server = new LDAPServer();
						if ($ldap_server->test_user($_POST['new_login'])) {
							// L'utilisateur a été trouvé dans l'annuaire. On ne l'enregistre pas.
							$write_ldap_success = true;
							$msg.= "The user could not be added to directory LDAP, because it is already present there. It nevertheless will be created in the Gepi base.";
						} else {
							$write_ldap_success = $ldap_server->add_user($_POST['new_login'], $_POST['reg_nom'], $_POST['reg_prenom'], $_POST['reg_email'], $_POST['reg_civilite'], $NON_PROTECT['password1'], $_POST['reg_statut']);
						}
					} else {
						$write_ldap = false;
					}

					# On poursuit si le LDAP s'est bien passé (ou bien si on n'avait rien à faire avec...)
					if (!$write_ldap or ($write_ldap && $write_ldap_success)) {
						// Ensuite, on enregistre dans la base, en distinguant selon le type d'authentification.
						if ($_POST['reg_auth_mode'] == "gepi") {
							// On enregistre le mot de passe
							$reg_data = mysql_query("INSERT INTO utilisateurs SET nom='".$_POST['reg_nom']."',prenom='".$_POST['reg_prenom']."',civilite='".$_POST['reg_civilite']."',login='".$_POST['new_login']."',password='$reg_password_c',statut='".$_POST['reg_statut']."',email='".$_POST['reg_email']."',tel='".$_POST['reg_tel']."', auth_mode = '".$_POST['reg_auth_mode']."',etat='actif', change_mdp='n'");
					updateOnline("INSERT INTO utilisateurs SET nom='".$_POST['reg_nom']."',prenom='".$_POST['reg_prenom']."',civilite='".$_POST['reg_civilite']."',login='".$_POST['new_login']."',password='$reg_password_c',statut='".$_POST['reg_statut']."',email='".$_POST['reg_email']."',tel='".$_POST['reg_tel']."', auth_mode = '".$_POST['reg_auth_mode']."',etat='actif', change_mdp='n'");
					} else {
							// Auth LDAP ou SSO, pas de mot de passe.
							$reg_data = mysql_query("INSERT INTO utilisateurs SET nom='".$_POST['reg_nom']."',prenom='".$_POST['reg_prenom']."',civilite='".$_POST['reg_civilite']."',login='".$_POST['new_login']."',password='',statut='".$_POST['reg_statut']."',email='".$_POST['reg_email']."', auth_mode = '".$_POST['reg_auth_mode']."',etat='actif', change_mdp='n'");
updateOnline("INSERT INTO utilisateurs SET nom='".$_POST['reg_nom']."',prenom='".$_POST['reg_prenom']."',civilite='".$_POST['reg_civilite']."',login='".$_POST['new_login']."',password='',statut='".$_POST['reg_statut']."',email='".$_POST['reg_email']."', auth_mode = '".$_POST['reg_auth_mode']."',etat='actif', change_mdp='n'");				
				}

						if ($_POST['reg_statut'] == "professeur") {
							$del = mysql_query("DELETE FROM j_professeurs_matieres WHERE id_professeur = '".$_POST['new_login']."'");
							updateOnline("DELETE FROM j_professeurs_matieres WHERE id_professeur = '".$_POST['new_login']."'");
							$m = 0;
							while ($m < $_POST['max_mat']) {
								if ($reg_matiere[$m] != '') {
									$test = mysql_query("SELECT * FROM j_professeurs_matieres WHERE (id_professeur = '".$_POST['new_login']."' and id_matiere = '$reg_matiere[$m]')");
									$resultat = mysql_num_rows($test);
									if ($resultat == 0) {
										$reg = mysql_query("INSERT INTO j_professeurs_matieres SET id_professeur = '".$_POST['new_login']."', id_matiere = '$reg_matiere[$m]', ordre_matieres = '0'");
									updateOnline("INSERT INTO j_professeurs_matieres SET id_professeur = '".$_POST['new_login']."', id_matiere = '$reg_matiere[$m]', ordre_matieres = '0'");
									}
								}
								$reg_matiere[$m] = '';
								$m++;
							}
						}
						$msge = "Dear ";
						$msge .= $_POST['reg_civilite'] . " " . $_POST['reg_nom'] . " here your access informations to Sajoscol App! Keep them. Login : " . $_POST['new_login'] . "pass : " . urlencode($NON_PROTECT['password1']);
						if(strlen($_POST['reg_tel']) == 8) sendSms($_POST['reg_tel'], $msge);
						$msg="You have just created a new user !<br />By default, this user is regarded as credit.";
						//$msg = $msg."<br />Pour imprimer les paramètres de l'utilisateur (identifiant, mot de passe, ...), cliquez <a href='impression_bienvenue.php?user_login=".$_POST['new_login']."&mot_de_passe=".urlencode($NON_PROTECT['password1'])."' target='_blank'>ici</a> !";
						$msg = $msg."<br />To print the parameters of the user (identifier, password, ...), click <a href='impression_bienvenue.php?user_login=".$_POST['new_login']."&amp;mot_de_passe=".urlencode($NON_PROTECT['password1'])."' target='_blank'>here</a> !";
						$msg = $msg."<br />Caution : later, it will be impossible for you to again print the password of a user ! ";
						$user_login = $_POST['new_login'];

						$temoin_ajout_ou_modif_ok="y";
					}
				}
			}

			if($temoin_ajout_ou_modif_ok=="y") {
				if ($_POST['reg_statut']=='scolarite'){
					$sql="SELECT c.id FROM classes c;";
					$res_liste_classes=mysql_query($sql);
					if(mysql_num_rows($res_liste_classes)>0){
						while($ligtmp=mysql_fetch_object($res_liste_classes)) {
							$sql="INSERT INTO j_scol_classes SET id_classe='$ligtmp->id', login='".$_POST['new_login']."';";
							$insert=mysql_query($sql);
							updateOnline($sql);
							if(!$insert){
								$msg.="<br />Error during association with the class ".get_class_from_id($ligtmp->id);
							}
						}
					}
				}
			}

		}
		//
		//action s'il s'agit d'une modification
		//
		else if ((isset($user_login)) and ($user_login!='')) {

			// On regarde quel est le format du login, majuscule ou minuscule...
			$test = sql_count(sql_query("SELECT login FROM utilisateurs WHERE (login = '".$user_login."')"));
			if ($test == "0") $user_login = strtoupper($user_login);

			if (isset($_POST['deverrouillage'])) {
				$reg_data = sql_query("UPDATE utilisateurs SET date_verrouillage=now() - interval " . getSettingValue("temps_compte_verrouille") . " minute  WHERE (login='".$user_login."')");
			updateOnline("UPDATE utilisateurs SET date_verrouillage=now() - interval " . getSettingValue("temps_compte_verrouille") . " minute  WHERE (login='".$user_login."')");
			}

			// Si on change le mode d'authentification, il faut quelques opérations particulières
			$old_auth_mode = mysql_result(mysql_query("SELECT auth_mode FROM utilisateurs WHERE login = '".$user_login."'"), 0);
			if ($old_auth_mode == "gepi" && ($_POST['reg_auth_mode'] == "ldap" || $_POST['reg_auth_mode'] == "sso")) {
				// On passe du mode Gepi à un mode externe : il faut supprimer le mot de passe
				$oldmd5password = mysql_result(mysql_query("SELECT password FROM utilisateurs WHERE login = '".$user_login."'"), 0);
				mysql_query("UPDATE utilisateurs SET password = '', salt = '' WHERE login = '".$user_login."'");
				updateOnline("UPDATE utilisateurs SET password = '', salt = '' WHERE login = '".$user_login."'");
				$msg = "Passage to a mode of external authentification : ";
				// Et si on a un accès en écriture au LDAP, il faut créer l'utilisateur !
				if ($gepiSettings['ldap_write_access'] == "yes") {
					$create_ldap_user = true;
					$msg .= "the password of the user is unchanged.<br/>";
				} else {
					$msg .= "the password of the user was erased.<br/>";
				}
			} elseif (($old_auth_mode == "sso" || $old_auth_mode == "ldap") && $_POST['reg_auth_mode'] == "gepi") {
				// On passe d'un mode externe à un mode Gepi. On prévient l'admin qu'il faut modifier le mot de passe.
				$msg = "Passage of an external mode of authentification to a local mode : the password of the user *must* be re-initialized.<br/>";
				// Et si accès en écriture au LDAP, on supprime le compte.
				if ($gepiSettings['ldap_write_access'] == "yes" && (!isset($_POST['prevent_ldap_removal']) or $_POST['prevent_ldap_removal'] != "yes")) {
					$delete_ldap_user = true;
				}
			}
			$change = "yes";
			$flag = '';
			if ($_POST['reg_statut'] != "professeur") {
				$test = mysql_query("SELECT * FROM j_groupes_professeurs WHERE (login='".$user_login."')");
				$nb = mysql_num_rows($test);
				if ($nb != 0) {
					$msg = "Impossible to change the statute. This user is currently a professor in certain classes !";
					$change = "no";
				} else {
					$k = 0;
					while ($k < $_POST['max_mat']) {
						$reg_matiere[$k] = '';
						$k++;
					}
				}
			}

			if ($_POST['reg_statut'] == "professeur") {
				//$test = mysql_query("SELECT jgm.id_matiere FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (" .
				$test = mysql_query("SELECT DISTINCT(jgm.id_matiere) FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (" .
					"jgp.login = '".$user_login."' and " .
					"jgm.id_groupe = jgp.id_groupe)");
				$nb = mysql_num_rows($test);
				if ($nb != 0) {
					$k = 0;
					$change = "yes";
					while ($k < $nb) {
						// ===============
						// Pour chaque matière associée au prof, on réinitialise le témoin:
						$flag="no";
						// ===============
						$id_matiere = mysql_result($test, $k, 'id_matiere');
						//echo "\$k=$k<br />";
						//echo "\$id_matiere=$id_matiere<br />";
						$m = 0;
						while ($m < $_POST['max_mat']) {
							//echo "\$m=$m - \$id_matiere=$id_matiere - \$reg_matiere[$m]=$reg_matiere[$m]";
							if ($id_matiere == $reg_matiere[$m]) {
								$flag = "yes";
							}
							//if(isset($flag)){echo " \$flag=$flag";}
							//echo "<br />";
							$m++;
						}
						if ($flag != "yes") {
							$change = "no";
						}
						$k++;
					}
					if ($change == "no") {
						$msg = "Impossible to change the courses. This user is currently a professor in certain classes of the courses that you want to remove !";
					}
				}
			}

			if ($change == "yes") {
				// Variable utilisée pour la partie photo:
				$temoin_ajout_ou_modif_ok="y";

				$sql="SELECT statut FROM utilisateurs WHERE login='$user_login';";
				$res_statut_user=mysql_query($sql);
				$lig_tmp=mysql_fetch_object($res_statut_user);

				// Si l'utilisateur était CPE, il faut supprimer les associations dans la table j_eleves_cpe
				if($lig_tmp->statut=="cpe"){
					if($_POST['reg_statut']!="cpe"){
						$sql="DELETE FROM j_eleves_cpe WHERE cpe_login='$user_login';";
						$nettoyage=mysql_query($sql);
						updateOnline($sql);
					}
				}

				// Si l'utilisateur était SCOLARITE, il faut supprimer les associations dans la table j_scol_classes
				if($lig_tmp->statut=="scolarite"){
					if($_POST['reg_statut']!="scolarite"){
						$sql="DELETE FROM j_scol_classes WHERE login='$user_login';";
						$nettoyage=mysql_query($sql);
						updateOnline($sql);
					}
				}

				// On effectue les opérations LDAP
				if (isset($create_ldap_user) && $create_ldap_user) {
					$ldap_server = new LDAPServer;
					if ($ldap_server->test_user($user_login)) {
						// L'utilisateur a été trouvé dans l'annuaire. On ne l'enregistre pas.
						$write_ldap_success = true;
						$msg.= "The user could not be added to directory LDAP, because it is already present there.<br/>";
					} else {
						$write_ldap_success = $ldap_server->add_user($user_login, $_POST['reg_nom'], $_POST['reg_prenom'], $_POST['reg_email'], $_POST['reg_civilite'], md5(rand()), $_POST['reg_statut']);
						// On transfert le mot de passe à la main
						$ldap_server->set_manual_password($user_login, "{MD5}".base64_encode(pack("H*",$oldmd5password)));
					}
				}

				if (isset($delete_ldap_user) && $delete_ldap_user) {
					$ldap_server = new LDAPServer;
					if (!$ldap_server->test_user($user_login)) {
						// L'utilisateur n'a pas été trouvé dans l'annuaire.
						$write_ldap_success = true;
					} else {
						$write_ldap_success = $ldap_server->delete_user($user_login);
					}
				}


				$reg_data = mysql_query("UPDATE utilisateurs SET nom='".$_POST['reg_nom']."',prenom='".$_POST['reg_prenom']."',civilite='".$_POST['reg_civilite']."', login='".$_POST['reg_login']."',statut='".$_POST['reg_statut']."',email='".$_POST['reg_email']."',tel='".$_POST['reg_tel']."',etat='".$_POST['reg_etat']."',auth_mode='".$_POST['reg_auth_mode']."' WHERE login='".$user_login."'");
				updateOnline("UPDATE utilisateurs SET nom='".$_POST['reg_nom']."',prenom='".$_POST['reg_prenom']."',civilite='".$_POST['reg_civilite']."', login='".$_POST['reg_login']."',statut='".$_POST['reg_statut']."',email='".$_POST['reg_email']."',etat='".$_POST['reg_etat']."',auth_mode='".$_POST['reg_auth_mode']."' WHERE login='".$user_login."'");
				$del = mysql_query("DELETE FROM j_professeurs_matieres WHERE id_professeur = '".$user_login."'");
				updateOnline("DELETE FROM j_professeurs_matieres WHERE id_professeur = '".$user_login."'");
				$m = 0;
				while ($m < $_POST['max_mat']) {
					$num=$m+1;
					if ($reg_matiere[$m] != '') {
						$test = mysql_query("SELECT * FROM j_professeurs_matieres WHERE (id_professeur = '".$user_login."' and id_matiere = '$reg_matiere[$m]')");
						$resultat = mysql_num_rows($test);
						if ($resultat == 0) {
						$reg = mysql_query("INSERT INTO j_professeurs_matieres SET id_professeur = '".$user_login."', id_matiere = '$reg_matiere[$m]', ordre_matieres = '$num'");
						updateOnline("INSERT INTO j_professeurs_matieres SET id_professeur = '".$user_login."', id_matiere = '$reg_matiere[$m]', ordre_matieres = '$num'");
						}
						$reg_matiere[$m] = '';
					}
					$m++;
				}
				if (!$reg_data) {
					$msg = "Error during recording of the data";
				} else {
					$msg.="The modifications were indeed recorded !";
				}
			}
		} // elseif...
		else {
			if (strlen($_POST['new_login']) > $longmax_login) {
				$msg = "the identifier is too long, it should not exceed ".$longmax_login." characters.";
			}
			else {
				$msg = "The identifier of the user must be only made up of letters and figures !";
			}
		}


		if($temoin_ajout_ou_modif_ok=="y"){
			// pour le module trombinoscope
			// Envoi de la photo
			$i_photo = 0;
			$calldata_photo = mysql_query("SELECT * FROM utilisateurs WHERE (login = '".$user_login."')");

		// En multisite, on ajoute le répertoire RNE
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			  // On récupère le RNE de l'établissement
		  $repertoire="../photos/".$_COOKIE['RNE']."/personnels/";
		}else{
		  $repertoire="../photos/personnels/";
		}
			//$repertoire = '../photos/personnels/';
			$code_photo = md5(strtolower($user_login));



					if(isset($_POST['suppr_filephoto']) and $valide_form === 'oui' ){
						if($_POST['suppr_filephoto']=='y'){
							if(unlink($repertoire.$code_photo.".jpg")){
								$msg = "The  photo ".$repertoire.$code_photo.".jpg was removed. ";
							}
							else{
								$msg = "Failure of the removal of the photo ".$repertoire.$code_photo.".jpg ";
							}
						}
					}

					// filephoto
					if(isset($_FILES['filephoto']['tmp_name'])){
						$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
						if ( $filephoto_tmp != '' and $valide_form === 'oui' ){
							$filephoto_name=$_FILES['filephoto']['name'];
							$filephoto_size=$_FILES['filephoto']['size'];
							// Tester la taille max de la photo?

							if(is_uploaded_file($filephoto_tmp)){
								$dest_file = $repertoire.$code_photo.".jpg";
								$source_file = stripslashes("$filephoto_tmp");
								$res_copy=copy("$source_file" , "$dest_file");
								if($res_copy){
									$msg = "Installation of the photo carried out.";
								}
								else{
									$msg = "Error during installation of the photo .";
								}
							}
							else{
								$msg = "Error during upload of the photo .";
							}
						}
					}


				// si suppression de la fiche il faut supprimer la photo

			// fin pour le module trombinoscope
		}
	}
}
elseif(isset($_POST['suppression_assoc_user_groupes'])) {
	check_token();

	$user_group=isset($_POST["user_group"]) ? $_POST["user_group"] : array();

	$call_classes = mysql_query("SELECT g.id group_id, g.name name, c.classe classe, c.id classe_id " .
			"FROM j_groupes_professeurs jgp, j_groupes_classes jgc, groupes g, classes c WHERE (" .
			"jgp.login = '$user_login' and " .
			"g.id = jgp.id_groupe and " .
			"jgc.id_groupe = jgp.id_groupe and " .
			"c.id = jgc.id_classe) order by jgc.id_classe");
	$nb_classes = mysql_num_rows($call_classes);
	if($nb_classes>0) {
		$k = 0;
		$user_classe=array();
		while ($k < $nb_classes) {
			$user_classe['classe_nom_court'] = mysql_result($call_classes, $k, "classe");
			$user_classe['matiere_nom_court'] = mysql_result($call_classes, $k, "name");
			$user_classe['classe_id'] = mysql_result($call_classes, $k, "classe_id");
			$user_classe['group_id'] = mysql_result($call_classes, $k, "group_id");

			if(!in_array($user_classe['group_id'],$user_group)) {
				$sql="DELETE FROM j_groupes_professeurs WHERE id_groupe='".$user_classe['group_id']."' AND login='$user_login';";
				//echo "$sql<br />\n";
				$suppr=mysql_query($sql);
				updateOnline($sql);
				if($suppr) {
					$msg.="Suppression of association with the course ".$user_classe['matiere_nom_court']." in ".$user_classe['classe_nom_court']."<br />\n";
				}
				else {
					$msg.="ERROR during suppression of association with the course ".$user_classe['matiere_nom_court']." in ".$user_classe['classe_nom_court']."<br />\n";
				}
			}
			$k++;
		}
		unset($user_classe);
	}
}

// On appelle les informations de l'utilisateur pour les afficher :
if (isset($user_login) and ($user_login!='')) {
	$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE login='".$user_login."'");
	$user_auth_mode = mysql_result($call_user_info, "0", "auth_mode");
	$user_nom = mysql_result($call_user_info, "0", "nom");
	$user_prenom = mysql_result($call_user_info, "0", "prenom");
	$user_civilite = mysql_result($call_user_info, "0", "civilite");
	$user_statut = mysql_result($call_user_info, "0", "statut");
	$user_email = mysql_result($call_user_info, "0", "email");
	$user_etat = mysql_result($call_user_info, "0", "etat");
	$date_verrouillage = mysql_result($call_user_info, "0", "date_verrouillage");

	$call_matieres = mysql_query("SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '".$user_login."' ORDER BY ordre_matieres");
	$nb_mat = mysql_num_rows($call_matieres);
	$k = 0;
	while ($k < $nb_mat) {
		$user_matiere[$k] = mysql_result($call_matieres, $k, "id_matiere");
		$k++;
	}

	// Utilisateurs précédent/suivant:
	//$sql="SELECT login,nom,prenom FROM utilisateurs WHERE statut='$user_statut' ORDER BY nom,prenom";
	$sql="SELECT login,nom,prenom FROM utilisateurs WHERE statut='$user_statut' AND etat='actif' ORDER BY nom,prenom";
	$res_liste_user=mysql_query($sql);
	if(mysql_num_rows($res_liste_user)>0){
		$login_user_prec="";
		$login_user_suiv="";
		$temoin_tmp=0;
		$liste_options_user="";
		while($lig_user_tmp=mysql_fetch_object($res_liste_user)){
			if("$lig_user_tmp->login"=="$user_login"){
				$liste_options_user.="<option value='$lig_user_tmp->login' selected='true'>".strtoupper($lig_user_tmp->nom)." ".ucfirst(strtolower($lig_user_tmp->prenom))."</option>\n";
				$temoin_tmp=1;
				if($lig_user_tmp=mysql_fetch_object($res_liste_user)){
					$login_user_suiv=$lig_user_tmp->login;
					$liste_options_user.="<option value='$lig_user_tmp->login'>".strtoupper($lig_user_tmp->nom)." ".ucfirst(strtolower($lig_user_tmp->prenom))."</option>\n";
				}
				else{
					$login_user_suiv="";
				}
			}
			else{
					$liste_options_user.="<option value='$lig_user_tmp->login'>".strtoupper($lig_user_tmp->nom)." ".ucfirst(strtolower($lig_user_tmp->prenom))."</option>\n";
			}
			if($temoin_tmp==0){
				$login_user_prec=$lig_user_tmp->login;
			}
		}
	}

} else {
	$nb_mat = 0;
	if (isset($_POST['reg_civilite']))
		$user_civilite = $_POST['reg_civilite'];
	else
		$user_civilite = 'M.';
	$user_auth_mode = isset($_POST['reg_auth_mode']) ? $_POST['reg_auth_mode'] : "gepi";
	if (isset($_POST['reg_nom'])) $user_nom = $_POST['reg_nom'];
	if (isset($_POST['reg_prenom'])) $user_prenom = $_POST['reg_prenom'];
	if (isset($_POST['reg_statut'])) $user_statut = $_POST['reg_statut'];
	if (isset($_POST['reg_email'])) $user_email = $_POST['reg_email'];
	if (isset($_POST['reg_tel'])) $user_tel = $_POST['reg_tel'];
	if (isset($_POST['reg_etat'])) $user_etat = $_POST['reg_etat'];
}

$themessage  = 'Information was modified. Do you really want to leave without recording ?';
$themessage2 = "are you sure to want to carry out this operation ?\\n Currently this user connects himself to GEPI while authenticating
himself at a SSO.\\n By allotting a password, you will launch the procedure, who will generate a local password. This user will not be able to thus connect himself any more to GEPI via the SSO but only locally.";
//**************** EN-TETE *****************
//$titre_page = "Gestion des utilisateurs | Modifier un utilisateur";
$titre_page = "Creation/modification of a personnel";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>
<script type='text/javascript'>
	function display_password_fields(id,rw){
		if ($(id).value=='gepi' || rw == true) {
			$('password_fields').style.display='block';
			$('password_fields').style.visibility='visible';
		} else {
			$('password_fields').style.visibility='hidden';
			$('password_fields').style.display='none';
		}
	}

	change='no';
</script>

<?php

//echo "\$login_user_prec=$login_user_prec<br />";
//echo "\$login_user_suiv=$login_user_suiv<br />";

echo "<form enctype='multipart/form-data' name='form_choix_user' action='modify_user.php' method='post'>\n";

echo "<p class='bold'>";
echo "<a href='index.php?mode=personnels' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a> | <a href='javascript:centrerpopup(\"help.php\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>Help</a>";

// dans le cas de LCS, existence d'utilisateurs locaux repérés grâce au champ password non vide.
$testpassword = sql_query1("select password from utilisateurs where login = '".$user_login."'");
if ($testpassword == -1) $testpassword = '';
if (isset($user_login) and ($user_login!='')) {
	if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and ((getSettingValue("use_sso") != "lcs") or ($testpassword !='')) and getSettingValue("use_sso") != "ldap_scribe") OR $block_sso) {
		echo " | <a href=\"change_pwd.php?user_login=".$user_login."\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Change the password</a>\n";
	} else if (getSettingValue('use_sso') == "lcs") {
		echo " | <a href=\"change_pwd.php?user_login=".$user_login."&amp;attib_mdp=yes\" onclick=\"return confirm ('$themessage2')\">Allot a password</a>\n";
  }
	echo " | <a href=\"modify_user.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Add a new user</a>\n";
}

if(isset($liste_options_user)){
	if("$liste_options_user"!=""){
		if("$login_user_prec"!=""){echo " | <a href='modify_user.php?user_login=$login_user_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Previous</a>\n";}
		echo " | <select name='user_login' onchange=\"if(confirm_abandon (this, change, '$themessage')){document.form_choix_user.submit()}\">\n";
		echo $liste_options_user;
		echo "</select>\n";
		if("$login_user_suiv"!=""){echo " | <a href='modify_user.php?user_login=$login_user_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Next</a>\n";}
	}
}
echo "</p>\n";
echo "</form>\n";

$ldap_write_access = getSettingValue("ldap_write_access") == "yes" ? true : false;
if (!LDAPServer::is_setup()) {$ldap_write_access = false;}

if ($ldap_write_access) {
	echo "<p><strong><span style='color: red;'>Caution !</strong> An LDAP access in writing was defined.
			Consequently, any modification carried out on a user having for mode of authentification LDAP or SSO will be reflected on the LDAP (that includes the creation of the password, for the new users).
			If the user already exists in directory LDAP, its information will not be updated in the directory but it will be created in the Gepi base.
			In the event of modification of a user existing at the same time in the directory and in Gepi, the modifications will be reflected on directory LDAP.</strong></p>";
}
?>

<form enctype="multipart/form-data" action="modify_user.php" method="post">
<fieldset>
<?php
echo add_token_field();
if (isset($user_login)) {
	echo "<div style='float:right; width:; height:;'><a href='".$_SERVER['PHP_SELF']."?user_login=$user_login&amp;journal_connexions=y#connexion' title='Log of connections'><img src='../images/icons/document.png' width='16' height='16' alt='Log of connections' /></a></div>\n";
}
?>
<!--span class = "norme"-->
<div class = "norme">
<b>Identifiant <?php
if (!isset($user_login)) echo "(" . $longmax_login . " caractères maximum) ";?>:</b>
<?php
if (isset($user_login) and ($user_login!='')) {
	echo "<b>".$user_login."</b>\n";
	echo "<input type=hidden name=reg_login value=\"".$user_login."\" />\n";
} else {
	echo "<input type=text name=new_login size=20 value=\"";
	if (isset($user_login)) echo $user_login;
	echo "\" onchange=\"changement()\" />\n";
}

if (!$session_gepi->auth_ldap || !$session_gepi->auth_sso) {
	$remarque = "<p style='font-size: small;'><em>Note : ";
	if (!$session_gepi->auth_ldap && !$session_gepi->auth_sso) {
		$remarque .= "the modes of authentification LDAP and SSO are currently inactive. If you choose one of these modes, the user will not have any means of authenticating itself in Gepi.</em></p>";
	} else {
		$remarque .= "the authentification ";
		if (!$session_gepi->auth_ldap) {
			$remarque .= "LDAP ";
		} else {
			$remarque .= "SSO ";
		}
		$remarque .= "is currently inactive. If you choose this mode of authentification, the user will not have any way of authenticating itself in Gepi.</em></p>";
	}
	echo $remarque;
}

?>
<table summary="Infos">
	<tr><td>
	<table summary="Authentification">
<tr><td>Authentification&nbsp;:</td>
<?php
if (!isset($user_login) or $user_login == '') {
	$rw_access = $ldap_write_access ? "true":"false";
	$onchange_value = "changement(); display_password_fields(this.id,".$rw_access.");";
} else {
	$onchange_value = "changement();";
}
?>
	<td><select id="select_auth_mode" name="reg_auth_mode" size="1" onchange="<?php echo $onchange_value; ?>">
<option value='gepi' <?php if ($user_auth_mode=='gepi') echo " selected ";  ?>>Local (Gepi base )</option>
<option value='ldap' <?php if ($user_auth_mode=='ldap') echo " selected ";  ?>>LDAP</option>
<option value='sso' <?php if ($user_auth_mode=='sso') echo " selected ";  ?>>SSO (Cas, LCS, LemonLDAP)</option>
</select>
</td></tr>
<?php
if ($ldap_write_access) {
	echo "<p style='font-size: small;'><input type='checkbox' name='prevent_ldap_removal' value='yes' checked /> Do not remove from LDAP<br/>(if this box is stripped and that you pass from a mode of
authentification LDAP or SSO to local authentification mode, the user will be removed from the directory LDAP).</p>";
	echo "<tr><td></td>&nbsp;<td>";
	echo "</td></tr>";
}
 ?>
<tr><td>Name&nbsp;:</td><td><input type=text name=reg_nom size=20 <?php if (isset($user_nom)) { echo "value=\"".$user_nom."\"";}?> /></td></tr>
<tr><td>First name&nbsp;:</td><td><input type=text name=reg_prenom size=20 <?php if (isset($user_prenom)) { echo "value=\"".$user_prenom."\"";}?> /></td></tr>
<tr><td>Civility&nbsp;:</td><td><select name="reg_civilite" size="1" onchange="changement()">
<option value=''>(nothing)</option>
<option value='Rev. Fr.' <?php if ($user_civilite=='Rev. Fr') echo " selected ";  ?>>Rev. Fr.</option>
<option value='M.' <?php if ($user_civilite=='M.') echo " selected ";  ?>>M.</option>
<option value='Mme' <?php if ($user_civilite=='Mme') echo " selected ";  ?>>Mrs.</option>
<option value='Mlle' <?php if ($user_civilite=='Mlle') echo " selected ";  ?>>Miss</option>
</select>
</td></tr>
<tr><td>Phone&nbsp;:</td><td><input type=text name=reg_tel size=30 <?php if (isset($user_tel)) { echo "value=\"".$user_tel."\"";}?> onchange="changement()" /></td></tr>
<tr><td>Email&nbsp;:</td><td><input type=text name=reg_email size=30 <?php if (isset($user_email)) { echo "value=\"".$user_email."\"";}?> onchange="changement()" /></td></tr>
</table>
</td>

<td>
<?php
// trombinoscope

if(getSettingValue("active_module_trombinoscopes")=='y'){

	// En multisite, on ajoute le répertoire RNE
	if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		  // On récupère le RNE de l'établissement
	  $repertoire="../photos/".$_COOKIE['RNE']."/personnels/";
	}else{
	  $repertoire="../photos/personnels/";
	}
	if ((isset($user_login))and($user_login!='')&&(isset($user_nom))and($user_nom!='')&&(isset($user_prenom))and($user_prenom!='')) {
		$code_photo = md5(strtolower($user_login));
		$photo=$repertoire.$code_photo.".jpg";
		echo "<table style='text-align: center;' summary='Photo'>\n";
		echo "<tr>\n";
		echo "<td style='text-align: center;'>\n";
		$temoin_photo="non";
		if(file_exists($photo)){
			$temoin_photo="oui";
			//echo "<td>\n";
			echo "<div align='center'>\n";
			$dimphoto=redimensionne_image($photo);
			echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />';
			//echo "</td>\n";
			//echo "<br />\n";
			echo "</div>\n";
			echo "<div style='clear:both;'></div>\n";
		}
		echo "<div align='center'>\n";
		echo "<span style='font-size:xx-small;'>\n";
		echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';return false;\">\n";
		if($temoin_photo=="oui"){
			echo "Modify the photo file</a>\n";
		}
		else{
			echo "Send a photo file</a>\n";
		}
	}
	else{
		echo "<table style='text-align: center;' summary='Photo'>\n";
		echo "<tr>\n";
		echo "<td style='text-align: center;'>\n";
		$temoin_photo="non";
		echo "<div align='center'>\n";
		echo "<span style='font-size:xx-small;'>\n";
		echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';return false;\">\n";
		echo "Send a photo file</a>\n";
	}

	?></span>

	<div id="div_upload_photo" style="display: none;">
		<input type="file" name="filephoto" size="12" />
		<input type="hidden" name="uid_post" value="<?php echo preg_replace('/ /','%20',$uid); ?>" />
	<?php
	if ((isset($user_login))and($user_login!='')&&(isset($user_nom))and($user_nom!='')&&(isset($user_prenom))and($user_prenom!='')) {
		if(file_exists($photo)){
			?><br /><input type="checkbox" name="suppr_filephoto" id="suppr_filephoto" value="y" />
			&nbsp;<label for="suppr_filephoto" style="cursor: pointer; cursor: hand;">Remove the existing photo</label><?php
		}
	}
	?>
		<br /><input type="submit" value="Save" />
	</div>
	</div>
	</td>
	</tr>
	</table><?php
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
// fin trombinoscope
?>

<?php
if (!(isset($user_login)) or ($user_login=='')) {
	# On créé un nouvel utilisateur. On définit son mot de passe.
	echo "<div id='password_fields' style='visibility: visible;'>";
	echo "<table summary='Password'><tr><td>Password (".getSettingValue("longmin_pwd") ." characters minimum) : </td><td><input type=password name=no_anti_inject_password1 size=20 onchange=\"changement()\" /></td></tr>\n";
	echo "<tr><td>Password (to confirm) : </td><td><input type=password name=reg_password2 size=20 onchange=\"changement()\" /></td></tr></table>\n";
	echo "<br /><b>Caution : the password must comprise ".getSettingValue("longmin_pwd")." characters minimum and must be made up at the same time of letters and figures.</b>\n";
	echo "<br /><b>Notice</b> : during creation of a user, it is recommended to choose the NUMEN as password.<br />\n";
	echo "</td></tr></table>\n";
	echo "</div>";
}
?>
<br />Statute (consult '<a href='javascript:centrerpopup("help.php",600,480,"scrollbars=yes,statusbar=no,resizable=yes")'>help</a>) : <SELECT name=reg_statut size=1 onchange="changement()">
<?php if (!isset($user_statut)) $user_statut = "professeur"; ?>
<option value="professeur" <?php if ($user_statut == "professeur") { echo ' selected="selected"';}?>>Professor</option>
<option value="administrateur" <?php if ($user_statut == "administrateur") { echo ' selected="selected"';}?>>Administrator</option>
<option value="cpe" <?php if ($user_statut == "cpe") { echo ' selected="selected"';}?>>C.P.E.</option>
<option value="scolarite" <?php if ($user_statut == "scolarite") { echo ' selected="selected"';}?>>Schooling</option>
<option value="secours" <?php if ($user_statut == "secours") { echo ' selected="selected"';}?>>Emergency</option>
<?php
if (getSettingValue("statuts_prives") == "y") {
	if ($user_statut == "autre") { $sel = ' selected="selected"';}else{ $sel = '';}
	echo '
	<option value="autre"'.$sel.'>Other</option>';
}
?>

</select>
<?php
if (getSettingValue("statuts_prives") == "y") {
	if ($user_statut == "autre") {
		echo "<a href='creer_statut.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">specify the statute 'other'</a>";
	}
}
?>
<br />

<br />State :<select name="reg_etat" size="1" onchange="changement()">
<?php if (!isset($user_etat)) $user_etat = "actif"; ?>
<option value="actif" <?php if ($user_etat == "actif") { echo "selected";}?>>active</option>
<option value="inactif" <?php if ($user_etat == "inactif") { echo "selected";}?>>Inactive</option>
</select>
<br />

<?php
$k = 0;
while ($k < $nb_mat+1) {
	$num_mat = $k+1;
	echo "Course N°$num_mat (if professor): ";
	$temp = "matiere_".$k;
	echo "<select size=1 name='$temp' onchange=\"changement()\">\n";
	$calldata = mysql_query("SELECT * FROM matieres ORDER BY matiere");
	$nombreligne = mysql_num_rows($calldata);
	echo "<option value='' "; if (!(isset($user_matiere[$k]))) {echo " selected";} echo ">(empty)</option>\n";
	$i = 0;
	while ($i < $nombreligne){
		$matiere_list = mysql_result($calldata, $i, "matiere");
		$matiere_complet_list = mysql_result($calldata, $i, "nom_complet");
		//echo "<option value=$matiere_list "; if (isset($user_matiere[$k]) and ($matiere_list == $user_matiere[$k])) {echo " selected";} echo ">$matiere_list | $matiere_complet_list</option>\n";
		echo "<option value=$matiere_list "; if (isset($user_matiere[$k]) and ($matiere_list == $user_matiere[$k])) {echo " selected";} echo ">$matiere_list | ".htmlentities($matiere_complet_list)."</option>\n";
		$i++;
	}
	echo "</select><br />\n";
	$k++;
}
$nb_mat++;

if (isset($user_login) and ($user_login!='') and ($user_statut=='scolarite')) {
	echo "Follow this link <a href='../classes/scol_resp.php?quitter_la_page=y' target='_blank'>to associate the account to classes</a>.<br />\n";
}

// Déverrouillage d'un compte
if (isset($user_login) and ($user_login!='')) {
	$day_now   = date("d");
	$month_now = date("m");
	$year_now  = date("Y");
	$hour_now  = date("H");
	$minute_now = date("i");
	$seconde_now = date("s");
	$now = mktime($hour_now, $minute_now, $seconde_now, $month_now, $day_now, $year_now);

	$annee_verrouillage = substr($date_verrouillage,0,4);
	$mois_verrouillage =  substr($date_verrouillage,5,2);
	$jour_verrouillage =  substr($date_verrouillage,8,2);
	$heures_verrouillage = substr($date_verrouillage,11,2);
	$minutes_verrouillage = substr($date_verrouillage,14,2);
	$secondes_verrouillage = substr($date_verrouillage,17,2);
	$date_verrouillage = mktime($heures_verrouillage, $minutes_verrouillage, $secondes_verrouillage, $mois_verrouillage, $jour_verrouillage, $annee_verrouillage);
	if ($date_verrouillage  > ($now- getSettingValue("temps_compte_verrouille")*60)) {
		echo "<br /><center><table border=\"1\" cellpadding=\"5\" width = \"90%\" bgcolor=\"#FFB0B8\"  summary='Locking'><tr><td>\n";
		echo "<h2>Locking/Unlocking of the account</h2>\n";
		echo "After a too great number of attempts at unfruitful connections, the account is currently locked.";
		echo "<br /><input type=\"checkbox\" name=\"deverrouillage\" value=\"yes\" onchange=\"changement()\" /> check the box to unlock the account";
		echo "</td></tr></table></center>\n";
	}
}

echo "<input type=hidden name=max_mat value=$nb_mat />\n";
?>
<input type=hidden name=valid value="yes" />
<?php if (isset($user_login)) echo "<input type=hidden name=user_login value=\"".$user_login."\" />\n"; ?>
<center><input type=submit value=Save /></center>
<!--/span-->
</div>
</fieldset>
</form>

<?php
	if((isset($user_login))&&(isset($user_statut))&&($user_statut=='professeur')) {
		$call_classes = mysql_query("SELECT g.id group_id, g.name name, c.classe classe, c.id classe_id " .
				"FROM j_groupes_professeurs jgp, j_groupes_classes jgc, groupes g, classes c WHERE (" .
				"jgp.login = '$user_login' and " .
				"g.id = jgp.id_groupe and " .
				"jgc.id_groupe = jgp.id_groupe and " .
				"c.id = jgc.id_classe) order by jgc.id_classe");
		$nb_classes = mysql_num_rows($call_classes);
		if($nb_classes>0) {
			echo "<p>&nbsp;</p>\n";
			echo "<form enctype='multipart/form-data' action='modify_user.php' method='post'>\n";
			echo "<fieldset>\n";
			echo add_token_field();
			echo "<p>The professor is associated to the following courses.<br />You can remove (<i>uncheck</i>) association with certain courses&nbsp;:</p>";
			$k = 0;
			while ($k < $nb_classes) {
				$user_classe['classe_nom_court'] = mysql_result($call_classes, $k, "classe");
				$user_classe['matiere_nom_court'] = mysql_result($call_classes, $k, "name");
				$user_classe['classe_id'] = mysql_result($call_classes, $k, "classe_id");
				$user_classe['group_id'] = mysql_result($call_classes, $k, "group_id");
		
				echo "<input type='checkbox' id='user_group_$k' name='user_group[]' value='".$user_classe["group_id"]."' checked /><label for='user_group_$k'> ".$user_classe['classe_nom_court']." (".$user_classe['matiere_nom_court'].")</label><br />\n";
	
				$k++;
			}
			echo "<input type='hidden' name='user_login' value='$user_login' />\n";
			echo "<input type='hidden' name='suppression_assoc_user_groupes' value='y' />\n";
			echo "<center><input type='submit' value=\"Remove association with the selected courses\" /></center>\n";
			echo "</fieldset>\n";
			echo "</form>\n";
		}
	}
	echo "<p>&nbsp;</p>\n";

	if((isset($user_login))&&($journal_connexions=='n')) {
		echo "<p><a href='".$_SERVER['PHP_SELF']."?user_login=$user_login&amp;journal_connexions=y#connexion' title='Log of connections'>Log of connections</a></p>\n";
	}

	if($journal_connexions=='y') {
		// Journal des connexions
		echo "<a name=\"connexion\"></a>\n";
		if (isset($_POST['duree'])) {
			$duree = $_POST['duree'];
		} else {
			$duree = '7';
		}
		
		journal_connexions($user_login,$duree,'modify_user');

	}

	echo "<p>&nbsp;</p>\n";
?>

<?php require("../lib/footer.inc.php");?>
