<?php
/*
* $Id: mon_compte.php 8386 2011-09-29 15:10:28Z crob $
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
	
	$filename = '../responsables/responsable.txt';
	$somecontent = $sql.";\n";

	// Assurons nous que le fichier est accessible en écriture
	/*if (is_writable($filename)) {

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
	}*/
	
} 

// On teste si on affiche le message de changement de mot de passe
if (isset($_GET['change_mdp'])) $affiche_message = 'yes';
$message_enregistrement = "For security reasons, you should change you password.";

// Resume session
if ($session_gepi->security_check() == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

if (($_SESSION['statut'] == 'professeur') or ($_SESSION['statut'] == 'cpe') or ($_SESSION['statut'] == 'responsable') or ($_SESSION['statut'] == 'eleve')) {
	// Mot de passe comportant des lettres et des chiffres
	$flag = 0;
} else {
	// Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
	$flag = 1;
}

if ((isset($_POST['valid'])) and ($_POST['valid'] == "yes"))  {
	check_token();

	$msg = '';
	$no_modif = "yes";
	$no_anti_inject_password_a = isset($_POST["no_anti_inject_password_a"]) ? $_POST["no_anti_inject_password_a"] : NULL;
	$no_anti_inject_password1 = isset($_POST["no_anti_inject_password1"]) ? $_POST["no_anti_inject_password1"] : NULL;
	$reg_password2 = isset($_POST["reg_password2"]) ? $_POST["reg_password2"] : NULL;
	$reg_email = isset($_POST["reg_email"]) ? $_POST["reg_email"] : NULL;
	$reg_show_email = isset($_POST["reg_show_email"]) ? $_POST["reg_show_email"] : "no";

	// On commence par récupérer quelques infos.
	$req = mysql_query("SELECT password, auth_mode FROM utilisateurs WHERE (login = '".$session_gepi->login."')");
	$old_password = mysql_result($req, 0, "password");
	$user_auth_mode = mysql_result($req, 0, "auth_mode");
	if ($no_anti_inject_password_a != '') {
		// Modification du mot de passe

		if ($no_anti_inject_password1 == $reg_password2) {
			// On a bien un mot de passe et sa confirmation qui correspond

			if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
				// On est en mode d'écriture LDAP.
				// On tente un bind pour tester le nouveau mot de passe, et s'assurer qu'il
				// est différent de celui actuellement utilisé :
				$ldap_server = new LDAPServer;
				$test_bind_nouveau = $ldap_server->authenticate_user($session_gepi->login, $no_anti_inject_password1);

				// On teste aussi l'ancien mot de passe.
				$test_bind_ancien = $ldap_server->authenticate_user($session_gepi->login, $no_anti_inject_password_a);

				if (!$test_bind_ancien) {
					// L'ancien mot de passe n'est pas correct
					$msg = "The old password is not correct !";
				} elseif ($test_bind_nouveau) {
					// Le nouveau mot de passe est le même que l'ancien
					$msg = "ERREUR : You must choose a new password different from the old.";
				} else {
					// C'est bon, on enregistre
					$write_ldap_success = $ldap_server->update_user($session_gepi->login, '', '', '', '', $no_anti_inject_password1,'');
					if ($write_ldap_success) {
						$msg = "The password has ete modified !";
						$reg = mysql_query("UPDATE utilisateurs SET change_mdp='n' WHERE login = '" . $session_gepi->login . "'");
						updateOnline("UPDATE utilisateurs SET change_mdp='n' WHERE login = '" . $session_gepi->login . "'");
						$no_modif = "no";
						if (isset($_POST['retour'])) {
							header("Location:../accueil.php?msg=$msg");
							die();
						}
					}
				}
			} else {

				function unhtmlentities($chaineHtml)
				{
					$tmp = get_html_translation_table(HTML_ENTITIES);
					$tmp = array_flip ($tmp);
					$chaineTmp = strtr ($chaineHtml, $tmp);
					return $chaineTmp;
				}

				// On fait la mise à jour sur la base de données
				if ($session_gepi->authenticate_gepi($session_gepi->login,$NON_PROTECT['password_a'])) {
					if  ($no_anti_inject_password_a == $no_anti_inject_password1) {
						$msg = "ERROR : You must choose a new password different from old.";
					} else if (!(verif_mot_de_passe($NON_PROTECT['password1'],$flag))) {
						$msg = "ERROR during typing of the password (<em> see the recommendations</em>), please restart !";
						if((isset($info_verif_mot_de_passe))&&($info_verif_mot_de_passe!="")) {$msg.="<br />".$info_verif_mot_de_passe;}
					} else {
						$reg = Session::change_password_gepi($session_gepi->login,$NON_PROTECT['password1']);
						if ($reg) {
							mysql_query("UPDATE utilisateurs SET change_mdp='n' WHERE login = '$session_gepi->login'");
							updateOnline("UPDATE utilisateurs SET change_mdp='n' WHERE login = '$session_gepi->login'");
							$msg = "The password has been modified !";
							$no_modif = "no";
							if (isset($_POST['retour'])) {
								header("Location:../accueil.php?msg=$msg");
								die();
							}
						}
					}
				} else {
					$msg = "The old password is not correct !";
				}
			}
		} else {
			$msg = "Error during typing of the password, the two passwords are not identical. Please restart !";
		}
	}

	$call_email = mysql_query("SELECT email,show_email FROM utilisateurs WHERE login='" . $_SESSION['login'] . "'");
	$user_email = mysql_result($call_email, 0, "email");
	$user_show_email = mysql_result($call_email, 0, "show_email");

	if(($_SESSION['statut']!='responsable')&&($_SESSION['statut']!='eleve')) {
		if ($user_email != $reg_email) {
			if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
				if (!isset($ldap_server)) $ldap_server = new LDAPServer;
				$write_ldap_success = $ldap_server->update_user($session_gepi->login, '', '', $reg_email, '', '', '');
			}
			$reg = mysql_query("UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
			updateOnline("UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
			if ($reg) {
				if($msg!="") {$msg.="<br />";}
				$msg.="The e_mail address was modified !";
				$no_modif = "no";
			}
		}
	}
	if(($_SESSION['statut']=='responsable')&&((getSettingValue('mode_email_resp')=='')||(getSettingValue('mode_email_resp')=='mon_compte'))) {
		if ($user_email != $reg_email) {
			if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
				if (!isset($ldap_server)) $ldap_server = new LDAPServer;
				$write_ldap_success = $ldap_server->update_user($session_gepi->login, '', '', $reg_email, '', '', '');
			}
			$reg = mysql_query("UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
			updateOnline("UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
			if ($reg) {
				if($msg!="") {$msg.="<br />";}
				$msg.="The e_mail address was modified !";
				$no_modif = "no";

				if((getSettingValue('mode_email_resp')=='mon_compte')) {
					$sql="UPDATE resp_pers SET mel='$reg_email' WHERE login='".$_SESSION['login']."';";
					$update_resp=mysql_query($sql);
					updateOnline($sql);
					if(!$update_resp) {$msg.="<br />Error during update of the table 'resp_pers'.";}

					if((getSettingValue('envoi_mail_actif')!='n')&&(getSettingValue('informer_scolarite_modif_mail')!='n')) {
						$sujet_mail=remplace_accents("Update mall ".$_SESSION['nom']." ".$_SESSION['prenom'],'all');
						$message_mail="Address email of the responsible ";
						$message_mail.=remplace_accents($_SESSION['nom']." ".$_SESSION['prenom'],'all')." is passed to '$reg_email'. You should update Sconet consequently.";
						$destinataire_mail=getSettingValue('gepiSchoolEmail');
						if(getSettingValue('gepiSchoolEmail')!='') {
							envoi_mail($sujet_mail, $message_mail, $destinataire_mail);
						}
					}

					if(getSettingValue('envoi_mail_actif')!='n') {
						$sujet_mail="Update of your address mall";
						$message_mail="You carried out the modification of your email address in 'Manage my account' the ".strftime('%A %d/%m/%Y à %H:%M:%S').". Your new address is thus '$reg_email'. It is this address which will be used for the next messages.";
						$destinataire_mail=$user_email;
						envoi_mail($sujet_mail, $message_mail, $destinataire_mail);
					}
				}
			}
		}
	}
	elseif(($_SESSION['statut']=='eleve')&&((getSettingValue('mode_email_ele')=='')||(getSettingValue('mode_email_ele')=='mon_compte'))) {
		if ($user_email != $reg_email) {
			if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
				if (!isset($ldap_server)) $ldap_server = new LDAPServer;
				$write_ldap_success = $ldap_server->update_user($session_gepi->login, '', '', $reg_email, '', '', '');
			}
			$reg = mysql_query("UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
			updateOnline("UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
			if ($reg) {
				if($msg!="") {$msg.="<br />";}
				$msg.="The e_mail address was modified !";
				$no_modif = "no";

				if((getSettingValue('mode_email_ele')=='mon_compte')) {
					$sql="UPDATE eleves SET email='$reg_email' WHERE login='".$_SESSION['login']."';";
					$update_eleve=mysql_query($sql);
					updateOnline($sql);
					if(!$update_eleve) {$msg.="<br />Error during update of the table 'eleves'.";}

					if((getSettingValue('envoi_mail_actif')!='n')&&(getSettingValue('informer_scolarite_modif_mail')!='n')) {
						$sujet_mail=remplace_accents("Mise à jour mail ".$_SESSION['nom']." ".$_SESSION['prenom'],'all');
						$message_mail="Address email of the student ";
						$message_mail.=remplace_accents($_SESSION['nom']." ".$_SESSION['prenom'],'all')." is passed to '$reg_email'. You should update Sconet consequently.";
						$destinataire_mail=getSettingValue('gepiSchoolEmail');
						if(getSettingValue('gepiSchoolEmail')!='') {
							envoi_mail($sujet_mail, $message_mail, $destinataire_mail);
						}
					}
				}
			}
		}
	}


	if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe")
	if ($user_show_email != $reg_show_email) {
	if ($reg_show_email != "no" and $reg_show_email != "yes") $reg_show_email = "no";
		$reg = mysql_query("UPDATE utilisateurs SET show_email = '$reg_show_email' WHERE login = '" . $_SESSION['login'] . "'");
		updateOnline("UPDATE utilisateurs SET show_email = '$reg_show_email' WHERE login = '" . $_SESSION['login'] . "'");
		if ($reg) {
			if($msg!="") {$msg.="<br />";}
			$msg.="The parameter setting of display of your email was modified !";
			$no_modif = "no";
		}
	}

	//======================================
	// pour le module trombinoscope
	/*
	if(($_SESSION['statut']=='administrateur')||
	($_SESSION['statut']=='scolarite')||
	($_SESSION['statut']=='cpe')||
	($_SESSION['statut']=='professeur')) {
	*/
	if((getSettingValue("active_module_trombino_pers")=='y')&&
		((($_SESSION['statut']=='administrateur')&&(getSettingValue("GepiAccesModifMaPhotoAdministrateur")=='yes'))||
		(($_SESSION['statut']=='scolarite')&&(getSettingValue("GepiAccesModifMaPhotoScolarite")=='yes'))||
		(($_SESSION['statut']=='cpe')&&(getSettingValue("GepiAccesModifMaPhotoCpe")=='yes'))||
		(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesModifMaPhotoProfesseur")=='yes')))) {

		// Envoi de la photo
		// si modification du nom ou du prénom ou du pseudo il faut modifier le nom de la photo d'identitée
		$i_photo = 0;
		$user_login=$_SESSION['login'];
		$calldata_photo = mysql_query("SELECT * FROM utilisateurs WHERE (login = '".$user_login."')");
		$ancien_nom = mysql_result($calldata_photo, $i_photo, "nom");
		$ancien_prenom = mysql_result($calldata_photo, $i_photo, "prenom");

		// En multisite, on ajoute le répertoire RNE
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			  // On récupère le RNE de l'établissement
		  $repertoire="../photos/".$_COOKIE['RNE']."/personnels/";
		}else{
		  $repertoire="../photos/personnels/";
		}

		//$repertoire = '../photos/personnels/';



		$ancien_code_photo = md5(strtolower($user_login));
		$nouveau_code_photo = $ancien_code_photo;

		/*
		// si on modify le nom ou le prénom de la personne et s'il y a une photo on renomme alors la photo.
		if ( $ancien_nom != $_POST['reg_nom'] or $ancien_prenom != $_POST['reg_prenom'] ) {
			$ancien_nom_fichier = $repertoire.$ancien_code_photo.'.jpg';
			$nouveau_nom_fichier = $repertoire.$nouveau_code_photo.'.jpg';

			@rename($ancien_nom_fichier, $nouveau_nom_fichier);
		}
		*/

		// DEBUG:
		//echo "\$ancien_code_photo=$ancien_code_photo<br />\n";
		//echo "\$nouveau_code_photo=$nouveau_code_photo<br />\n";

		if(isset($ancien_code_photo)) {
			if($ancien_code_photo != "") {
				//if(isset($_POST['suppr_filephoto']) and $valide_form === 'oui' ) {
				if(isset($_POST['suppr_filephoto'])) {
					if($_POST['suppr_filephoto']=='y') {
						if(@unlink($repertoire.$ancien_code_photo.".jpg")) {
							if($msg!="") {$msg.="<br />";}
							$msg.="The photo ".$repertoire.$ancien_code_photo.".jpg was removed. ";
							$no_modif="no";
						}
						else {
							if($msg!="") {$msg.="<br />";}
							$msg.="Failure of the removal of the photo ".$repertoire.$ancien_code_photo.".jpg ";
						}
					}
				}

				// DEBUG:
				//echo "\$HTTP_POST_FILES['filephoto']['tmp_name']=".$HTTP_POST_FILES['filephoto']['tmp_name']."<br />\n";
				//echo "\$_FILES['filephoto']['tmp_name']=".$_FILES['filephoto']['tmp_name']."<br />\n";

				// filephoto
				//if(isset($HTTP_POST_FILES['filephoto']['tmp_name'])) {
				if(isset($_FILES['filephoto']['tmp_name'])) {
					//$filephoto_tmp=$HTTP_POST_FILES['filephoto']['tmp_name'];
					$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
					//if ( $filephoto_tmp != '' and $valide_form === 'oui' ) {
					if ($filephoto_tmp!='') {
						//$filephoto_name=$HTTP_POST_FILES['filephoto']['name'];
						//$filephoto_size=$HTTP_POST_FILES['filephoto']['size'];
						//$filephoto_type=$HTTP_POST_FILES['filephoto']['type'];
						$filephoto_name=$_FILES['filephoto']['name'];
						$filephoto_size=$_FILES['filephoto']['size'];
						$filephoto_type=$_FILES['filephoto']['type'];
						if (!preg_match('/jpg$/',strtolower($filephoto_name)) || ($filephoto_type != "image/jpeg" && $filephoto_type != "image/pjpeg") ) {
							if($msg!="") {$msg.="<br />";}
							$msg .= "Error : only files having the extension .jpg are authorized.\n";
						} else {
							// Tester la taille max de la photo?
							if(is_uploaded_file($filephoto_tmp)) {
								$dest_file = $repertoire.$nouveau_code_photo.".jpg";
								//$source_file=stripslashes("$filephoto_tmp");
								$source_file=$filephoto_tmp;
								$res_copy=copy("$source_file" , "$dest_file");
								if($res_copy) {
									//$msg.="Mise en place de la photo effectuée.";
									if($msg!="") {$msg.="<br />";}
									$msg.="Installation of the photo done. <br />It can be necessary to refresh the page, empty the mask of the navigator<br />so that a change of photo is taken into account.";
									$no_modif="no";

									if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
										// si le redimensionnement des photos est activé on redimenssionne
										$source = imagecreatefromjpeg($repertoire.$nouveau_code_photo.".jpg"); // La photo est la source
										if (getSettingValue("active_module_trombinoscopes_rt")=='') { $destination = imagecreatetruecolor(getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes")); } // On crée la miniature vide
										if (getSettingValue("active_module_trombinoscopes_rt")!='') { $destination = imagecreatetruecolor(getSettingValue("h_resize_trombinoscopes"), getSettingValue("l_resize_trombinoscopes")); } // On crée la miniature vide

										// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
										$largeur_source = imagesx($source);
										$hauteur_source = imagesy($source);
										$largeur_destination = imagesx($destination);
										$hauteur_destination = imagesy($destination);

										// On crée la miniature
										imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
										if (getSettingValue("active_module_trombinoscopes_rt")!='') { $degrees = getSettingValue("active_module_trombinoscopes_rt"); /* $destination = imagerotate($destination,$degrees); */$destination = ImageRotateRightAngle($destination,$degrees); }
										// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
										imagejpeg($destination, $repertoire.$nouveau_code_photo.".jpg",100);
									}

								}
								else {
									if($msg!="") {$msg.="<br />";}
									$msg.="Error during installation of the photograph.";
								}
							}
							else {
								if($msg!="") {$msg.="<br />";}
								$msg.="Error during upload of the photo .";
							}
						}
					}
				}
			}
		}
	}
	//elseif($_SESSION['statut']=='eleve') {
	elseif(($_SESSION['statut']=='eleve')&&(getSettingValue("active_module_trombinoscopes")=='y')&&(getSettingValue("GepiAccesModifMaPhotoEleve")=='yes')) {
		// En multisite, on ajoute le répertoire RNE
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			  // On récupère le RNE de l'établissement
		  $repertoire="../photos/".$_COOKIE['RNE']."/eleves/";
		}else{
		  $repertoire="../photos/eleves/";
		}

		$sql="SELECT elenoet FROM eleves WHERE login='".$_SESSION['login']."';";
		$res_elenoet=mysql_query($sql);
		if(mysql_num_rows($res_elenoet)>0) {
			$lig_tmp_elenoet=mysql_fetch_object($res_elenoet);
			$reg_no_gep=$lig_tmp_elenoet->elenoet;

			// Envoi de la photo
			if(isset($reg_no_gep)) {
				if($reg_no_gep!="") {
					if(strlen(my_ereg_replace("[0-9]","",$reg_no_gep))==0) {
						if(isset($_POST['suppr_filephoto'])) {
							if($_POST['suppr_filephoto']=='y') {

								// Récupération du nom de la photo en tenant compte des histoires des zéro 02345.jpg ou 2345.jpg
								$photo=nom_photo($reg_no_gep);

								if("$photo"!="") {
									if(@unlink($repertoire.$photo)) {
										if($msg!="") {$msg.="<br />";}
										$msg.="The photo ".$repertoire.$photo." was removed. ";
										$no_modif="no";
									}
									else {
										if($msg!="") {$msg.="<br />";}
										$msg.="Failure of the removal of the photo ".$repertoire.$photo." ";
									}
								}
								else {
									if($msg!="") {$msg.="<br />";}
									$msg.="Failure of the removal of the photo corresponding to $reg_no_gep (<i>not found</i>) ";
								}
							}
						}

						// Contrôler qu'un seul élève a bien cet elenoet???
						$sql="SELECT 1=1 FROM eleves WHERE elenoet='$reg_no_gep'";
						$test=mysql_query($sql);
						$nb_elenoet=mysql_num_rows($test);
						if($nb_elenoet==1) {
							if(isset($_FILES['filephoto']['tmp_name'])) {
								// filephoto
								//$filephoto_tmp=$HTTP_POST_FILES['filephoto']['tmp_name'];
								$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
								if($filephoto_tmp!="") {
									//$filephoto_name=$HTTP_POST_FILES['filephoto']['name'];
									//$filephoto_size=$HTTP_POST_FILES['filephoto']['size'];
									//$filephoto_type=$HTTP_POST_FILES['filephoto']['type'];
									$filephoto_name=$_FILES['filephoto']['name'];
									$filephoto_size=$_FILES['filephoto']['size'];
									$filephoto_type=$_FILES['filephoto']['type'];
									if ((!preg_match('/jpg$/',strtolower($filephoto_name))) || ($filephoto_type != "image/jpeg" && $filephoto_type != "image/pjpeg") ) {
										//$msg = "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.";
										if($msg!="") {$msg.="<br />";}
										$msg .= "Error : only files having the extension .jpg are authorized.\n";
									} else {
									// Tester la taille max de la photo?

									if(is_uploaded_file($filephoto_tmp)) {
										$dest_file=$repertoire.$reg_no_gep.".jpg";
										//$source_file=stripslashes("$filephoto_tmp");
										$source_file=$filephoto_tmp;
										$res_copy=copy("$source_file" , "$dest_file");
										if($res_copy) {
											//$msg.="Mise en place de la photo effectuée.";
											if($msg!="") {$msg.="<br />";}
											$msg.="Installation of the photo carried out. <br />It can be necessary to refresh the page, empty the mask of the navigator<br />so that a change of photo is taken into account.";
											$no_modif="no";

											if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
												// si le redimensionnement des photos est activé on redimenssionne
												$source = imagecreatefromjpeg($repertoire.$reg_no_gep.".jpg"); // La photo est la source
												if (getSettingValue("active_module_trombinoscopes_rt")=='') { $destination = imagecreatetruecolor(getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes")); } // On crée la miniature vide
												if (getSettingValue("active_module_trombinoscopes_rt")!='') { $destination = imagecreatetruecolor(getSettingValue("h_resize_trombinoscopes"), getSettingValue("l_resize_trombinoscopes")); } // On crée la miniature vide

												// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
												$largeur_source = imagesx($source);
												$hauteur_source = imagesy($source);
												$largeur_destination = imagesx($destination);
												$hauteur_destination = imagesy($destination);

												// On crée la miniature
												imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
												if (getSettingValue("active_module_trombinoscopes_rt")!='') { $degrees = getSettingValue("active_module_trombinoscopes_rt"); /* $destination = imagerotate($destination,$degrees); */$destination = ImageRotateRightAngle($destination,$degrees); }
												// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
												imagejpeg($destination, $repertoire.$reg_no_gep.".jpg",100);
											}

										}
										else {
											if($msg!="") {$msg.="<br />";}
											$msg.="Error during installation of photo.";
										}
									}
									else {
										if($msg!="") {$msg.="<br />";}
										$msg.="Error during upload of photo.";
									}
									}
								}
							}
						}
						elseif($nb_elenoet==0) {
							if($msg!="") {$msg.="<br />";}
							//$msg.="Le numéro GEP de l'élève n'est pas enregistré dans la table 'eleves'.";
							$msg.="The interns number Sconet (elenoet) of the student is not recorded in the table 'eleves'.";
						}
						else {
							if($msg!="") {$msg.="<br />";}
							//$msg.="Le numéro GEP est commun à plusieurs élèves. C'est une anomalie.";
							$msg.="The interns number Sconet (elenoet) is common to several students. It is an anomaly.";
						}
					}
					else {
						if($msg!="") {$msg.="<br />";}
						//$msg.="Le numéro GEP proposé contient des caractères non numériques.";
						$msg.="The interns numbe Sconet (elenoet) proposed contains nonnumericals characters .";
					}
				} else {
						if($msg!="") {$msg.="<br />";}
						$msg.="The interns numbe Sconet (elenoet) is empty. Impossible to continue. Please announce this problem to the administrator.";
				}
			} else {
				if($msg!="") {$msg.="<br />";}
				$msg.="Vous n'avez pas numéro interne Sconet. Impossible to continue. Please announce this problem to the administrator.";
			}
		} else {
			if($msg!="") {$msg.="<br />";}
			$msg.="Vous n'avez pas numéro interne Sconet. Impossible to continue. Please announce this problem to the administrator.";
		}
	}

	//======================================
	if(($_SESSION['statut']=='professeur')&&(isset($_POST['matiere_principale']))) {
		/*
		// DANS /lib/session.inc, la matière principale du professeur est récupérée ainsi:
			$sql2 = "select id_matiere from j_professeurs_matieres where id_professeur = '" . $_login . "' order by ordre_matieres limit 1";
			$matiere_princ = sql_query1($sql2);

			mysql> show fields from j_professeurs_matieres;
			+----------------+-------------+------+-----+---------+-------+
			| Field          | Type        | Null | Key | Default | Extra |
			+----------------+-------------+------+-----+---------+-------+
			| id_professeur  | varchar(50) | NO   | PRI |         |       |
			| id_matiere     | varchar(50) | NO   | PRI |         |       |
			| ordre_matieres | int(11)     | NO   |     | 0       |       |
			+----------------+-------------+------+-----+---------+-------+
			3 rows in set (0.06 sec)

			mysql>
		*/

		$sql="SELECT DISTINCT jpm.id_matiere FROM j_professeurs_matieres jpm WHERE (jpm.id_professeur='".$_SESSION["login"]."') ORDER BY jpm.ordre_matieres;";
		//echo "$sql<br />\n";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$tab_matieres=array();
			while($lig_mat=mysql_fetch_object($test)) {
				$tab_matieres[]=$lig_mat->id_matiere;
				//echo $lig_mat->id_matiere." ";
			}
			//echo "<br />\n";

			// On n'accepte la modification que si la matière reçue fait bien déjà partie des matières du professeur
			if(in_array($_POST['matiere_principale'],$tab_matieres)) {
				// On ne modifie que si la matière principale choisie n'est pas celle enregistrée auparavant
				if($_POST['matiere_principale']!=$tab_matieres[0]) {
					$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur='".$_SESSION["login"]."';";
					updateOnline($sql);
					//echo "$sql<br />\n";
					$nettoyage=mysql_query($sql);

					$ordre_matieres=1;
					$sql="INSERT INTO j_professeurs_matieres SET id_professeur='".$_SESSION["login"]."', id_matiere='".$_POST['matiere_principale']."', ordre_matieres='$ordre_matieres';";
					//echo "$sql<br />\n";
					$insert=mysql_query($sql);
					updateOnline($sql);
					for($loop=0;$loop<count($tab_matieres);$loop++) {
						if($_POST['matiere_principale']!=$tab_matieres[$loop]) {
							$ordre_matieres++;
							$sql="INSERT INTO j_professeurs_matieres SET id_professeur='".$_SESSION["login"]."', id_matiere='".$tab_matieres[$loop]."', ordre_matieres='$ordre_matieres';";
							//echo "$sql<br />\n";
							$insert=mysql_query($sql);
							updateOnline($sql);
						}
					}

					$_SESSION['matiere']=$_POST['matiere_principale'];

					$no_modif="no";
					if($msg!="") {$msg.="<br />";}
					$msg.="Modification of the principal course done.";
				}
			}
		}
	}

	if((($_SESSION['statut']=='professeur')||
		($_SESSION['statut']=='scolarite')||
		($_SESSION['statut']=='cpe'))&&(isset($_POST['reg_civilite']))) {
		if($msg!="") {$msg.="<br />";}
		if(($_POST['reg_civilite']!='M.')&&($_POST['reg_civilite']!='Mlle')&&($_POST['reg_civilite']!='Mme')) {
			$msg.="Selected civility is not valid.";
		}
		else {
			$sql="UPDATE utilisateurs SET civilite='".$_POST['reg_civilite']."' WHERE login='".$_SESSION['login']."';";
			$update=mysql_query($sql);
			updateOnline($sql);
			if(!$update) {
				$msg.="Error during update of civility.";
			}
			else {
				$msg.="civility Updated .";
				$no_modif="no";
			}
		}
	}
	//======================================

	if ($no_modif == "yes") {
		if($msg!="") {$msg.="<br />";}
		$msg.="No modification was made !";
	}
}

$tab_statuts_barre=array('professeur', 'cpe', 'scolarite', 'administrateur');
$modifier_barre=isset($_POST['modifier_barre']) ? $_POST['modifier_barre'] : NULL;
if((isset($modifier_barre))&&(strtolower(substr(getSettingValue('utiliserMenuBarre'),0,1))=='y')&&(in_array($_SESSION['statut'], $tab_statuts_barre))) {
	$afficher_menu=isset($_POST['afficher_menu']) ? $_POST['afficher_menu'] : NULL;
	if((strtolower(substr($afficher_menu,0,1))!='y')&&(strtolower(substr($afficher_menu,0,1))!='n')) {
		if($msg!="") {$msg.="<br />";}
		$msg.="The choice '$afficher_menu' for the display or not of the bar of menu is invalid.<br />\n";
	}
	else {
		if(!savePref($_SESSION['login'], 'utiliserMenuBarre', $afficher_menu)) {
			$msg.="Error during backup of the preference of display or not of the bar of menu.<br />\n";
		}
		else {
			$msg.="Backup of the preference of display or not of the bar of menu carried out.<br />\n";
		}
	}
}


// On appelle les informations de l'utilisateur pour les afficher :
$call_user_info = mysql_query("SELECT nom,prenom,statut,email,show_email,civilite FROM utilisateurs WHERE login='" . $_SESSION['login'] . "'");
$user_civilite = mysql_result($call_user_info, "0", "civilite");
$user_nom = mysql_result($call_user_info, "0", "nom");
$user_prenom = mysql_result($call_user_info, "0", "prenom");
$user_statut = mysql_result($call_user_info, "0", "statut");
$user_email = mysql_result($call_user_info, "0", "email");
$user_show_email = mysql_result($call_user_info, "0", "show_email");

//**************** EN-TETE *****************
$titre_page = "Manage your account";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

// On initialise un flag pour savoir si l'utilisateur est 'éditable' ou non.
// Cela consiste à déterminer s'il s'agit d'un utilisateur local ou LDAP, et dans
// ce dernier cas à savoir s'il s'agit d'un accès en écriture ou non.
if ($session_gepi->current_auth_mode == "gepi" || $gepiSettings['ldap_write_access'] == "yes") {
	$editable_user = true;
	$affiche_bouton_submit = 'yes';
} else {
	$editable_user = false;
	$affiche_bouton_submit = 'no';
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>\n";
echo "<form enctype=\"multipart/form-data\" action=\"mon_compte.php\" method=\"post\">\n";
echo add_token_field();
echo "<h2>Personal information *</h2>\n";

if ($session_gepi->current_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
	echo "<p><span style='color: red;'>Note :</span> modifications of password and of email that you will carry out on this page will be propagated to the central directory, and thus to the other services which will use it.</p>";
}

echo "<table summary='Working'>\n";
echo "<tr><td>\n";
	echo "<table summary='Infos'>\n";
	echo "<tr><td>GEPI Identifier  : </td><td>" . $_SESSION['login']."</td></tr>\n";

	echo "<tr>\n";
	echo "<td>Civility : </td>\n";
	echo "<td>\n";
	if(($_SESSION['statut']=='professeur')||
		($_SESSION['statut']=='scolarite')||
		($_SESSION['statut']=='cpe')) {

		echo "<select name='reg_civilite' onchange='changement()'>\n";
		echo "<option value='M.' ";
		if ($user_civilite=='M.') {echo " selected ";}
		echo ">M.</option>\n";

		echo "<option value='Mme' ";
		if ($user_civilite=='Mme') {echo " selected ";}
		echo ">Mrs.</option>\n";

		echo "<option value='Mlle' ";
		if ($user_civilite=='Mlle') {echo " selected ";}
		echo ">Miss</option>\n";
		echo "</select>\n";
	}
	else {
		echo $user_civilite;
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr><td>Name : </td><td>".$user_nom."</td></tr>\n";
	echo "<tr><td>First name : </td><td>".$user_prenom."</td></tr>\n";
	if (($editable_user)&&
		((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
		(getSettingValue('mode_email_resp')!='sconet'))) {
		echo "<tr><td>Email : </td><td><input type=text name=reg_email size=30";
		if ($user_email) { echo " value=\"".$user_email."\"";}
		echo " /></td></tr>\n";
	} else {
		echo "<tr><td>Email : </td><td>".$user_email."<input type=\"hidden\" name=\"reg_email\" value=\"".$user_email."\" /></td></tr>\n";
	}
	if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe") {
		$affiche_bouton_submit = 'yes';
		echo "<tr><td></td><td><label for='reg_show_email' style='cursor: pointer;'><input type='checkbox' name='reg_show_email' id='reg_show_email' value='yes'";
		if ($user_show_email == "yes") echo " CHECKED";
		echo "/> Authorize the display of my address email<br />for the nonpersonal users of the school **</label></td></tr>\n";
	}
	echo "<tr><td>Statute : </td><td>".statut_accentue($user_statut)."</td></tr>\n";
	echo "</table>\n";
echo "</td>\n";

// PHOTO
echo "<td valign='top'>\n";
if(($_SESSION['statut']=='administrateur')||
($_SESSION['statut']=='scolarite')||
($_SESSION['statut']=='cpe')||
($_SESSION['statut']=='professeur')||
($_SESSION['statut']=='eleve')
) {
	$user_login=$_SESSION['login'];

	//echo "active_module_trombino_pers=".getSettingValue("active_module_trombino_pers")."<br />";
	//echo "active_module_trombinoscopes=".getSettingValue("active_module_trombinoscopes")."<br />";

	//if(getSettingValue("active_module_trombinoscopes")=='y') {
	if((($_SESSION['statut']=='eleve')&&(getSettingValue("active_module_trombinoscopes")=='y'))||
		(($_SESSION['statut']!='eleve')&&(getSettingValue("active_module_trombino_pers")=='y'))) {

		// pour module trombinoscope
		$photo_largeur_max=150;
		$photo_hauteur_max=150;

		$GepiAccesModifMaPhoto='GepiAccesModifMaPhoto'.ucfirst(strtolower($_SESSION['statut']));

		if($_SESSION['statut']=='eleve') {
			$sql="SELECT elenoet FROM eleves WHERE login='".$_SESSION['login']."';";
			$res_elenoet=mysql_query($sql);
			if(mysql_num_rows($res_elenoet)==0) {
				echo "</td></tr></table>\n";
				echo "<p><b>ERROR !</b> Your student statute does not seem to be confirmed in the table 'eleves'.</p>\n";
				// A FAIRE
				// AJOUTER UNE ALERTE INTRUSION
				require("../lib/footer.inc.php");
				die();
			}
			$lig_tmp_elenoet=mysql_fetch_object($res_elenoet);
			$reg_no_gep=$lig_tmp_elenoet->elenoet;

			if($reg_no_gep!="") {
				// Récupération du nom de la photo en tenant compte des histoires des zéro 02345.jpg ou 2345.jpg
				$photo=nom_photo($reg_no_gep);

				//echo "<td align='center'>\n";
				$temoin_photo="non";
				//if("$photo"!="") {
				if($photo) {
					//$photo="../photos/eleves/".$photo;
					if(file_exists($photo)) {
						$temoin_photo="oui";
						//echo "<td>\n";
						echo "<div align='center'>\n";
						$dimphoto=redimensionne_image2($photo);
						//echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />';
						echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border: 3px solid #FFFFFF;" alt="Ma photo" />';
						//echo "</td>\n";
						//echo "<br />\n";
						echo "</div>\n";
						echo "<div style='clear:both;'></div>\n";
					}
				}

				// Cas particulier des élèves pour une gestion plus fine avec les AIDs
				if ((getSettingValue("GepiAccesModifMaPhotoEleve")=='yes') and ($_SESSION['statut']=='eleve')) {
					// Une catégorie d'AID pour accès au trombino existe-t-elle ?
					if (getSettingValue("num_aid_trombinoscopes")!='') {
						// L'AID existe t-elle ?
						$test1 = sql_query1("select count(indice_aid) from aid_config where indice_aid='".getSettingValue("num_aid_trombinoscopes")."'");
						if ($test1!="0") {
							$test_eleve = sql_query1("select count(login) from j_aid_eleves where login='".$_SESSION['login']."' and indice_aid='".getSettingValue("num_aid_trombinoscopes")."'");
						}
						else {
							$test_eleve = "1";
						}
					} else {
						$test_eleve = "1";
					}
				}

				if ((getSettingValue($GepiAccesModifMaPhoto)=='yes') and ($test_eleve!=0)) {
					$affiche_bouton_submit ='yes';
					echo "<div align='center'>\n";
					//echo "<span id='lien_photo' style='font-size:xx-small;'>";
					echo "<div id='lien_photo' style='border: 1px solid black; padding: 5px; margin: 5px;'>";
					echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';document.getElementById('lien_photo').style.display='none';return false;\">";
					if($temoin_photo=="oui") {
						//echo "Modifier le fichier photo</a>\n";
						echo "Modifier le fichier photo</a>\n";
					}
					else {
						//echo "Envoyer un fichier photo</a>\n";
						echo "Send<br />a photo <br /> file</a>\n";
					}
					//echo "</span>\n";
					echo "</div>\n";
					echo "<div id='div_upload_photo' style='display:none;'>";
					echo "<input type='file' name='filephoto' size='30' />\n";
					echo "<input type='submit' name='Envoi_photo' value='Envoyer' />\n";
					if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
						echo "<br /><span class='small'><b>Notice : </b>The photographs are automatically re-dimensioned (width : ".getSettingValue("l_resize_trombinoscopes")." pixels, height : ".getSettingValue("h_resize_trombinoscopes")." pixels).<br />
						So that your photography is not deformed, dimensions of this one (respectively width and height) must be proportional to ".getSettingValue("l_resize_trombinoscopes")." and ".getSettingValue("h_resize_trombinoscopes").".</span>"."<br /><span class='small'>
						The photographs must moreover be in format JPEG with the extension '<strong>.jpg</strong>'.</span>";
					}

					if("$photo"!="") {
						if(file_exists($photo)) {
							echo "<br />\n";
							//echo "<input type='checkbox' name='suppr_filephoto' value='y' /> Supprimer la photo existante\n";
							echo "<input type='checkbox' name='suppr_filephoto' id='suppr_filephoto' value='y' />\n";
							echo "&nbsp;<label for='suppr_filephoto' style='cursor: pointer; cursor: hand;'>Remove the existing photo </label>\n";
						}
					}
					echo "</div>\n";
					echo "</div>\n";
				}
				//echo "</td>\n";
			}

		}
		else {
			echo "<table style='text-align: center;' summary='Photo'>\n";
			echo "<tr>\n";
			echo "<td style='text-align: center;'>\n";

				// En multisite, on ajoute le répertoire RNE
				if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
					// On récupère le RNE de l'établissement
					$repertoire="../photos/".$_COOKIE['RNE']."/personnels/";
				}
				else{
					$repertoire="../photos/personnels/";
				}

				$code_photo = md5(strtolower($user_login));

				$photo=$repertoire.$code_photo.".jpg";
				$temoin_photo="non";
				if(file_exists($photo)) {
					$temoin_photo="oui";
					echo "<div align='center'>\n";
					$dimphoto=redimensionne_image2($photo);
					echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />';
					echo "</div>\n";
					echo "<div style='clear:both;'></div>\n";
				}
				if(getSettingValue($GepiAccesModifMaPhoto)=='yes') {
					$affiche_bouton_submit ='yes';
					echo "<div align='center'>\n";
					echo "<span style='font-size:xx-small;'>\n";
					echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';return false;\">\n";
					if($temoin_photo=="oui") {
						echo "Modify the photo file</a>\n";
					}
					else {
						echo "Send a photo file</a>\n";
					}
					echo "</span>\n";
					echo "<div id='div_upload_photo' style='display: none;'>\n";
					echo "<input type='file' name='filephoto' size='30' />\n";

					echo "<input type='submit' name='Envoi_photo' value='Send' />\n";

					if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
						echo "<br /><span class='small'><b>Notice : </b>The photographs are automatically Re-dimensioned (width : ".getSettingValue("l_resize_trombinoscopes")." pixels, height : ".getSettingValue("h_resize_trombinoscopes")." pixels).<br />
						So that your photography is not deformed, dimensions of this one(respectively width and height) 
						must be proportional to ".getSettingValue("l_resize_trombinoscopes")." and ".getSettingValue("h_resize_trombinoscopes").".</span>"."<br /><span class='small'>
						The photographs must moreover be with format JPEG with the extension '<strong>.jpg</strong>'.</span>";
					}
					echo "<br />\n";
					echo "<input type='checkbox' name='suppr_filephoto' id='suppr_filephoto' value='y' />\n";
					echo "&nbsp;<label for='suppr_filephoto' style='cursor: pointer; cursor: hand;'>Remove the existing photograph</label>\n";
					echo "</div>\n";
					echo "</div>\n";
				}

			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
		}

	}
}
echo "</td>\n";
echo "</table>\n";
if ($affiche_bouton_submit=='yes') {
	echo "<p><input type='submit' value='Save' /></p>\n";
}
/*
//Supp ERIC
$tab_class_mat =  make_tables_of_classes_matieres();
if (count($tab_class_mat)!=0) {
	echo "<br /><br />Vous êtes professeur dans les classes et matières suivantes :";
	$i = 0;
	echo "<ul>";
	while ($i < count($tab_class_mat['id_c'])) {
		//echo "<li>".$tab_class_mat['nom_m'][$i]." dans la classe : ".$tab_class_mat['nom_c'][$i]."</li>";
		echo "<li>".$tab_class_mat['nom_c'][$i]." : ".$tab_class_mat['nom_m'][$i]."</li>";
		$i++;
	}
	echo "</ul>";
}
*/

// AJOUT Eric
//$groups = get_groups_for_prof($_SESSION["login"]);
$groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
if (empty($groups)) {
	echo "<br /><br />\n";
} else {
	echo "<br /><br />You are a professor in the classes and following courses :";
	echo "<ul>\n";
	foreach($groups as $group) {
		echo "<li><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
		echo "" . htmlentities($group["description"]);
		echo "</span>";
		echo "</li>\n";
	}
	echo "</ul>\n";

	// Matière principale:
	/*
	$test = mysql_query("SELECT DISTINCT(jgm.id_matiere) FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (" .
		"jgp.login = '".$_SESSION["login"]."' and " .
		"jgm.id_groupe = jgp.id_groupe)");
	*/
	$sql="SELECT DISTINCT jpm.id_matiere, m.nom_complet FROM j_professeurs_matieres jpm, matieres m WHERE (jpm.id_professeur='".$_SESSION["login"]."' AND m.matiere=jpm.id_matiere) ORDER BY m.nom_complet;";
	$test=mysql_query($sql);
	$nb=mysql_num_rows($test);
	//echo "\$nb=$nb<br />";
	if ($nb>1) {
		echo "Principal course&nbsp;: <select name='matiere_principale'>\n";
		while($lig_mat=mysql_fetch_object($test)) {
			echo "<option value='$lig_mat->id_matiere'";
			if($lig_mat->id_matiere==$_SESSION['matiere']) {echo " selected='selected'";}
			echo ">$lig_mat->nom_complet</option>\n";
		}
		echo "</select>\n";
		echo "<br />\n";
	}
}

$call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
$nombre_classe = mysql_num_rows($call_prof_classe);
if ($nombre_classe != "0") {
	$j = "0";
	echo "<p>You are ".getSettingValue("gepi_prof_suivi")." in the class of :</p>\n";
	echo "<ul>\n";
	while ($j < $nombre_classe) {
		$id_classe = mysql_result($call_prof_classe, $j, "id");
		$classe_suivi = mysql_result($call_prof_classe, $j, "classe");
		echo "<li><b>$classe_suivi</b></li>\n";
		$j++;
	}
	echo "</ul>\n";
}





echo "<p class='small'>* All the personal data present in base GEPI and concerning you are communicated to you on this page.
In accordance with the French law n° 78-17 from January 6, 1978 relating to data processing, the files and freedoms,
you can ask the Head of school or near the<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">administrator</a> of the site, la rectification de ces données.
Corrections are carried out in the 48 hours except weekend and public holidays which follow the request.";
if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe") {
	echo "<p class='small'>** Your email will be posted on certain pages only if their display were activated in a total way by the administrator and if you authorized the display of your email by notching the suitable box. ";
	echo "On the assumption that you authorize the display of your email, this one will be accessible only by the students which you have in class and/or their legal responsibles having an identifier to connect itself to Gepi.</p>\n";
}
// Changement du mot de passe
if ($editable_user) {
	echo "<hr /><a name=\"changemdp\"></a><H2>Change of the password</H2>\n";
	echo "<p><b>Caution : the password must comprise ".getSettingValue("longmin_pwd") ." characters minimum. ";
	if ($flag == 1)
		echo "It must comprise at least a letter, at least a figure and at least a special character among&nbsp;: ".htmlentities($char_spec);
	else
		echo "It must comprise at least a letter and at least a figure.";

	echo "<br /><span style='color: red;'>It is strongly not advised to choose a simple password</b>.</span>";
	echo "<br /><b>Your password is strictly personal, you should not diffuse it,<span style='color: red;'> it guarantees the secutity of your work.</b></span></p>\n";
	echo "<script type=\"text/javascript\" src=\"../lib/pwd_strength.js\"></script>";

	echo "<table summary='Password'><tr>\n";
	echo "<td>Old password : </td><td><input type=password name=no_anti_inject_password_a size=20 /></td>\n";
	echo "</tr><tr>\n";
	echo "<td>New password (".getSettingValue("longmin_pwd") ." characters minimum) :</td>";
	echo "<td> <input id=\"mypassword\" type=password name=no_anti_inject_password1 size=20 onkeyup=\"runPassword(this.value, 'mypassword');\" />";
	echo "<td>";
	echo "Complexity of your password : ";
	echo "		<div style=\"width: 150px;\"> ";
	echo "			<div id=\"mypassword_text\" style=\"font-size: 11px;\"></div>";
	echo "			<div id=\"mypassword_bar\" style=\"font-size: 1px; height: 3px; width: 0px; border: 1px solid white;\"></div> ";
	echo "		</div>";
	echo "</td>\n";
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td>New password (to confirm) : </td><td><input type=password name=reg_password2 size=20 /></td>\n";
	echo "</tr></table>\n";
	if ((isset($_GET['retour'])) or (isset($_POST['retour'])))
		echo "<input type=\"hidden\" name=\"retour\" value=\"Home\" />\n";
}
if ($affiche_bouton_submit=='yes')
	echo "<br /><center><input type=\"submit\" value=\"Save\" /></center>\n";
	echo "<input type=\"hidden\" name=\"valid\" value=\"yes\" />\n";
echo "</form>\n";
echo "  <hr />\n";

if((strtolower(substr(getSettingValue('utiliserMenuBarre'),0,1))=='y')&&(in_array($_SESSION['statut'], $tab_statuts_barre))) {
	$aff_barre="n";
	$sql="SELECT value FROM preferences WHERE login='".$_SESSION['login']."' AND name='utiliserMenuBarre';";
	$res_barre=mysql_query($sql);
	if(mysql_num_rows($res_barre)==0) {
		$aff_barre="y";
	}
	else {
		$lig_barre=mysql_fetch_object($res_barre);
		$aff_barre=strtolower(substr($lig_barre->value,0,1));
	}

	echo "<form enctype=\"multipart/form-data\" action=\"mon_compte.php\" method=\"post\">\n";
	echo add_token_field();

	echo "<fieldset id='afficherBarreMenu' style='border: 1px solid grey;'>\n";
	echo "<legend style='border: 1px solid grey;'>Manage the horizontal bar of the menu</legend>\n";
	echo "<input type='hidden' name='modifier_barre' value='ok' />\n";

	echo "<p>\n";
	echo "<label for='visibleMenu' id='texte_visibleMenu'>Make visible the horizontal bar of menu under the heading.</label>\n";
	echo "<input type='radio' id='visibleMenu' name='afficher_menu' value='yes'";
	if($aff_barre=="y") {
		echo " checked";
	}
	echo " />\n";
	echo "</p>\n";
	echo "<p>\n";
	echo "<label for='invisibleMenu' id='texte_invisibleMenu'>Not to use the horizontal bar of menu.</label>\n";
	echo "<input type='radio' id='invisibleMenu' name='afficher_menu' value='no'";
	if($aff_barre!="y") {
		echo " checked";
	}
	echo " />\n";
	echo "</p>\n";

	echo "<br /><center><input type=\"submit\" value=\"Save\" /></center>\n";
	echo "</fieldset>\n";
	echo "</form>\n";
	echo "  <hr />\n";
}

// Journal des connexions
echo "<a name=\"connexion\"></a>\n";
if (isset($_POST['duree'])) {
$duree = $_POST['duree'];
} else {
$duree = '7';
}

journal_connexions($_SESSION['login'],$duree);

/*

switch( $duree ) {
case 7:
$display_duree="une semaine";
break;
case 15:
$display_duree="quinze jours";
break;
case 30:
$display_duree="un mois";
break;
case 60:
$display_duree="deux mois";
break;
case 183:
$display_duree="six mois";
break;
case 365:
$display_duree="un an";
break;
case 'all':
$display_duree="le début";
break;
}

echo "<h2>Journal de vos connexions depuis <b>".$display_duree."</b>**</h2>\n";
$requete = '';
if ($duree != 'all') {$requete = "and START > now() - interval " . $duree . " day";}

$sql = "select START, SESSION_ID, REMOTE_ADDR, USER_AGENT, AUTOCLOSE, END from log where LOGIN = '".$_SESSION['login']."' ".$requete." order by START desc";

$day_now   = date("d");
$month_now = date("m");
$year_now  = date("Y");
$hour_now  = date("H");
$minute_now = date("i");
$seconde_now = date("s");
$now = mktime($hour_now, $minute_now, $seconde_now, $month_now, $day_now, $year_now);

?>
<ul>
<li>Les lignes en rouge signalent une tentative de connexion avec un mot de passe erroné.</li>
<li>Les lignes en orange signalent une session close pour laquelle vous ne vous êtes pas déconnecté correctement.</li>
<li>Les lignes en noir signalent une session close normalement.</li>
<li>Les lignes en vert indiquent les sessions en cours (cela peut correspondre à une connexion actuellement close mais pour laquelle vous ne vous êtes pas déconnecté correctement).</li>
</ul>
<table class="col" style="width: 90%; margin-left: auto; margin-right: auto; margin-bottom: 32px;" cellpadding="5" cellspacing="0" summary='Connexions'>
	<tr>
		<th class="col">Début session</th>
		<th class="col">Fin session</th>
		<th class="col">Adresse IP et nom de la machine cliente</th>
		<th class="col">Navigateur</th>
	</tr>
<?php
$res = sql_query($sql);
if ($res) {
	for ($i = 0; ($row = sql_row($res, $i)); $i++)
	{
		$annee_b = substr($row[0],0,4);
		$mois_b =  substr($row[0],5,2);
		$jour_b =  substr($row[0],8,2);
		$heures_b = substr($row[0],11,2);
		$minutes_b = substr($row[0],14,2);
		$secondes_b = substr($row[0],17,2);
		$date_debut = $jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b;

		$annee_f = substr($row[5],0,4);
		$mois_f =  substr($row[5],5,2);
		$jour_f =  substr($row[5],8,2);
		$heures_f = substr($row[5],11,2);
		$minutes_f = substr($row[5],14,2);
		$secondes_f = substr($row[5],17,2);
		$date_fin = $jour_f."/".$mois_f."/".$annee_f." à ".$heures_f." h ".$minutes_f;
		$end_time = mktime($heures_f, $minutes_f, $secondes_f, $mois_f, $jour_f, $annee_f);

		$temp1 = '';
		$temp2 = '';
		if ($end_time > $now) {
			$temp1 = "<font color='green'>";
			$temp2 = "</font>";
		} else if (($row[4] == 1) or ($row[4] == 2) or ($row[4] == 3)) {
			//$temp1 = "<font color=orange>\n";
			$temp1 = "<font color='#FFA500'>";
			$temp2 = "</font>";
		} else if ($row[4] == 4) {
			$temp1 = "<b><font color='red'>";
			$temp2 = "</font></b>";

		}

		echo "<tr>\n";
		echo "<td class=\"col\">".$temp1.$date_debut.$temp2."</td>\n";
		if ($row[4] == 2) {
			echo "<td class=\"col\">".$temp1."Tentative de connexion<br />avec mot de passe erroné.".$temp2."</td>\n";
		}
		else {
			echo "<td class=\"col\">".$temp1.$date_fin.$temp2."</td>\n";
		}
		if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
			$result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
		}
		else if ($active_hostbyaddr == "no_local") {
			if ((substr($row[2],0,3) == 127) or
				(substr($row[2],0,3) == 10.) or
				(substr($row[2],0,7) == 192.168)) {
				$result_hostbyaddr = "";
			}
			else {
				$tabip=explode(".",$row[2]);
				if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
					$result_hostbyaddr = "";
				}
				else {
					$result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
				}
			}
		}
		else {
			$result_hostbyaddr = "";
		}

		echo "<td class=\"col\"><span class='small'>".$temp1.$row[2].$result_hostbyaddr.$temp2. "</span></td>\n";
		echo "<td class=\"col\">".$temp1. detect_browser($row[3]) .$temp2. "</td>\n";
		echo "</tr>\n";
		flush();
	}
}


echo "</table>\n";

echo "<form action=\"".$_SERVER['PHP_SELF']."\" name=\"form_affiche_log\" method=\"post\">\n";
echo "Afficher le journal des connexions depuis : <select name=\"duree\" size=\"1\">\n";
echo "<option ";
if ($duree == 7) echo "selected";
echo " value=7>Une semaine</option>\n";
echo "<option ";
if ($duree == 15) echo "selected";
echo " value=15 >Quinze jours</option>\n";
echo "<option ";
if ($duree == 30) echo "selected";
echo " value=30>Un mois</option>\n";
echo "<option ";
if ($duree == 60) echo "selected";
echo " value=60>Deux mois</option>\n";
echo "<option ";
if ($duree == 183) echo "selected";
echo " value=183>Six mois</option>\n";
echo "<option ";
if ($duree == 365) echo "selected";
echo " value=365>Un an</option>\n";
echo "<option ";
if ($duree == 'all') echo "selected";
echo " value='all'>Le début</option>\n";
echo "</select>\n";
echo "<input type=\"submit\" name=\"Valider\" value=\"Valider\" />\n";

echo "</form>\n";

echo "<p class='small'>** Les renseignements ci-dessus peuvent vous permettre de vérifier qu'une connexion pirate n'a pas été effectuée sur votre compte.
Dans le cas d'une connexion inexpliquée, vous devez immédiatement en avertir l'<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">administrateur</a>.</p>\n";
*/
require("../lib/footer.inc.php");
?>
