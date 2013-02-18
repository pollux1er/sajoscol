<?php
/*
* $Id: param_gen.php 8377 2011-09-28 16:39:37Z crob $
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

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg = '';
if (isset($_POST['sup_logo'])) {
	check_token();

	$dest = '../images/';
	$ok = false;
	if ($f = @fopen("$dest/.test", "w")) {
		@fputs($f, '<'.'?php $ok = true; ?'.'>');
		@fclose($f);
		include("$dest/.test");
	}
	if (!$ok) {
		$msg = "Problem of writing on the repertory. Please signal this problem to the administrator of the site";
	} else {
		$old = getSettingValue("logo_etab");
		if (($old != '') and (file_exists($dest.$old))) unlink($dest.$old);
		$msg = "Le logo a été supprimé.";
		if (!saveSetting("logo_etab", '')) $msg .= "Error during recording in the table setting !";

	}

}

if (isset($_POST['valid_logo'])) {
	check_token();

	$doc_file = isset($_FILES["doc_file"]) ? $_FILES["doc_file"] : NULL;
	//if (ereg("\.([^.]+)$", $doc_file['name'], $match)) {
	//$match=array();
	//if (my_ereg("\.([^.]+)$", $doc_file['name'], $match)) {
	if (((function_exists("mb_ereg"))&&(mb_ereg("\.([^.]+)$", $doc_file['name'], $match)))||((function_exists("ereg"))&&(ereg("\.([^.]+)$", $doc_file['name'], $match)))) {
		$ext = strtolower($match[1]);
		if ($ext!='jpg' and $ext!='png'and $ext!='gif') {
		//if ($ext!='jpg' and $ext!='jpeg' and $ext!='png'and $ext!='gif') {
			$msg = "the only authorized extensions are gif, png et jpg";
		} else {
			$dest = '../images/';
			$ok = false;
			if ($f = @fopen("$dest/.test", "w")) {
				@fputs($f, '<'.'?php $ok = true; ?'.'>');
				@fclose($f);
				include("$dest/.test");
			}
			if (!$ok) {
				$msg = "Problem of writing on the repertory IMAGES. Please signal this problem to the administrator of the site";
			} else {
				$old = getSettingValue("logo_etab");
				if (file_exists($dest.$old)) @unlink($dest.$old);
				if (file_exists($dest.$doc_file)) @unlink($dest.$doc_file);
				$ok = @copy($doc_file['tmp_name'], $dest.$doc_file['name']);
				if (!$ok) $ok = @move_uploaded_file($doc_file['tmp_name'], $dest.$doc_file['name']);
				if (!$ok) {
					$msg = "Problem of transfer : the file could not be transferred on the repertory IMAGES. Please signal this problem to the administrator of the site";
				} else {
					$msg = "The file was transferred.";
				}
				if (!saveSetting("logo_etab", $doc_file['name'])) {
				$msg .= "Error during recording in the table setting !";
				}

			}
		}
	} else {
		$msg = "The selected file is not valid !";
	}
}



if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {
		check_token();



		// Max session length
		if (isset($_POST['sessionMaxLength'])) {
			if (!(my_ereg ("^[0-9]{1,}$", $_POST['sessionMaxLength'])) || $_POST['sessionMaxLength'] < 1) {
				$_POST['sessionMaxLength'] = 30;
			}
			if (!saveSetting("sessionMaxLength", $_POST['sessionMaxLength'])) {
				$msg .= "Error during recording of the max duration of inactivity !";
			}
		}
		if (isset($_POST['gepiSchoolRne'])) {
			$enregistrer_gepiSchoolRne='y';
			if(($multisite=='y')&&(isset($_COOKIE['RNE']))) {
				if(($_POST['gepiSchoolRne']!='')&&(strtoupper($_POST['gepiSchoolRne'])!=strtoupper($_COOKIE['RNE']))) {
					$msg .= "Error during recording of number RNE of the school !<br />The selected parameter is likely to prevent you from connecting you.<br />Refused recording!";
					$enregistrer_gepiSchoolRne='n';
				}
			}

			if($enregistrer_gepiSchoolRne=='y') {
				if (!saveSetting("gepiSchoolRne", $_POST['gepiSchoolRne'])) {
					$msg .= "Error during recording of RNE number of the school !";
				}
			}
		}
		if (isset($_POST['gepiYear'])) {
			if (!saveSetting("gepiYear", $_POST['gepiYear'])) {
				$msg .= "Error during recording of the school year !";
			}
		}
		if (isset($_POST['gepiSchoolName'])) {
			if (!saveSetting("gepiSchoolName", $_POST['gepiSchoolName'])) {
				$msg .= "Error during recording of the name of the school !";
			}
		}
		if (isset($_POST['gepiSchoolStatut'])) {
			if (!saveSetting("gepiSchoolStatut", $_POST['gepiSchoolStatut'])) {
				$msg .= "Error during recording of the statute of the school!";
			}
		}
		if (isset($_POST['gepiSchoolAdress1'])) {
			if (!saveSetting("gepiSchoolAdress1", $_POST['gepiSchoolAdress1'])) {
				$msg .= "Error during recording of the address !";
			}
		}
		if (isset($_POST['gepiSchoolAdress2'])) {
			if (!saveSetting("gepiSchoolAdress2", $_POST['gepiSchoolAdress2'])) {
				$msg .= "Error during recording of the address !";
			}
		}
		if (isset($_POST['gepiSchoolZipCode'])) {
			if (!saveSetting("gepiSchoolZipCode", $_POST['gepiSchoolZipCode'])) {
				$msg .= "Error during recording of the postal code !";
			}
		}
		if (isset($_POST['gepiSchoolCity'])) {
			if (!saveSetting("gepiSchoolCity", $_POST['gepiSchoolCity'])) {
				$msg .= "Error during recording of the city !";
			}
		}
		if (isset($_POST['gepiSchoolPays'])) {
			if (!saveSetting("gepiSchoolPays", $_POST['gepiSchoolPays'])) {
				$msg .= "Error during recording of the country !";
			}
		}
		if (isset($_POST['gepiSchoolAcademie'])) {
			if (!saveSetting("gepiSchoolAcademie", $_POST['gepiSchoolAcademie'])) {
				$msg .= "Error during recording of the academy !";
			}
		}
		if (isset($_POST['gepiSchoolTel'])) {
			if (!saveSetting("gepiSchoolTel", $_POST['gepiSchoolTel'])) {
				$msg .= "Error during recording of the telephone number !";
			}
		}
		if (isset($_POST['gepiSchoolFax'])) {
			if (!saveSetting("gepiSchoolFax", $_POST['gepiSchoolFax'])) {
				$msg .= "Error during recording of the number of fax!";
			}
		}
		if (isset($_POST['gepiSchoolEmail'])) {
			if (!saveSetting("gepiSchoolEmail", $_POST['gepiSchoolEmail'])) {
				$msg .= "Error during electronic address !";
			}
		}
		if (isset($_POST['gepiAdminNom'])) {
			if (!saveSetting("gepiAdminNom", $_POST['gepiAdminNom'])) {
				$msg .= "Error during recording of the name of the administrator !";
			}
		}
		if (isset($_POST['gepiAdminPrenom'])) {
			if (!saveSetting("gepiAdminPrenom", $_POST['gepiAdminPrenom'])) {
				$msg .= "Error during recording of the first name of the administrator !";
			}
		}
		if (isset($_POST['gepiAdminFonction'])) {
			if (!saveSetting("gepiAdminFonction", $_POST['gepiAdminFonction'])) {
				$msg .= "Error during recording of the function of the administrator !";
			}
		}
		
		if (isset($_POST['gepiAdminAdress'])) {
			if (!saveSetting("gepiAdminAdress", $_POST['gepiAdminAdress'])) {
				$msg .= "Error during recording of address email !";
			}
		}

		if (isset($_POST['gepiAdminAdressPageLogin'])) {
			if (!saveSetting("gepiAdminAdressPageLogin", 'y')) {
				$msg .= "Error during recording of the display of address email on the page of login!";
			}
		}
		else{
			if (!saveSetting("gepiAdminAdressPageLogin", 'n')) {
				$msg .= "Error during recording of the non-display of address email on the page of login !";
			}
		}

		if (isset($_POST['contact_admin_mailto'])) {
			if (!saveSetting("contact_admin_mailto", 'y')) {
				$msg .= "Error during the recording of 'contact_admin_mailto' !";
			}
		}
		else {
			if (!saveSetting("contact_admin_mailto", 'n')) {
				$msg .= "Error during the recording of 'contact_admin_mailto' !";
			}
		}

		if (isset($_POST['envoi_mail_liste'])) {
			if (!saveSetting("envoi_mail_liste", 'y')) {
				$msg .= "Error during the recording of 'envoi_mail_liste' !";
			}
		}
		else {
			if (!saveSetting("envoi_mail_liste", 'n')) {
				$msg .= "Error during the recording of 'envoi_mail_liste' !";
			}
		}

		if (isset($_POST['gepiAdminAdressFormHidden'])) {
			if (!saveSetting("gepiAdminAdressFormHidden", 'n')) {
				$msg .= "Error during recording of the display of address email in the form [Contact the administrator] !";
			}
		}
		else{
			if (!saveSetting("gepiAdminAdressFormHidden", 'y')) {
				$msg .= "Error during recording of the non-display of address email in the form [Contact the administrator] !";
			}
		}



	
	
		if (isset($_POST['longmin_pwd'])) {
			if (!saveSetting("longmin_pwd", $_POST['longmin_pwd'])) {
				$msg .= "Error during recording the minimal length of the password !";
			}
		}
		
		if (isset($_POST['mode_generation_pwd_majmin'])) {
			if (!saveSetting("mode_generation_pwd_majmin", $_POST['mode_generation_pwd_majmin'])) {
				$msg .= "Error during recording of the parameter Min/Maj on the passwords !";
			}
		}
	
		if (isset($_POST['mode_generation_pwd_excl'])) {
			if (!saveSetting("mode_generation_pwd_excl", $_POST['mode_generation_pwd_excl'])) {
				$msg .= "Error during recording of the parameter of exclusion of the characters lending to confusion on the passwords !";
			}
		}
		else{
			if (!saveSetting("mode_generation_pwd_excl", 'n')) {
				$msg .= "Error during recording of the parameter of exclusion of the characters lending to confusion on the passwords!";
			}
		}

		if (isset($_POST['mode_email_resp'])) {
			if (!saveSetting("mode_email_resp", $_POST['mode_email_resp'])) {
				$msg .= "Error during recording of the mode of update of the responsibles email  !";
			}
			else {
				$sql="SELECT * FROM infos_actions WHERE titre='Paramétrage mode_email_resp requis';";
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

		if (isset($_POST['mode_email_ele'])) {
			if (!saveSetting("mode_email_ele", $_POST['mode_email_ele'])) {
				$msg .= "Error during recording of the mode of update of the students email !";
			}
			else {
				$sql="SELECT * FROM infos_actions WHERE titre='Paramétrage mode_email_ele requis';";
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

		//===============================================================
		// Traitement des problemes de points d'interrogation à la place des accents
		if (isset($_POST['mode_utf8_bulletins_pdf'])) {
			if (!saveSetting("mode_utf8_bulletins_pdf", $_POST['mode_utf8_bulletins_pdf'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_bulletins_pdf !";
			}
		}
		else{
			if (!saveSetting("mode_utf8_bulletins_pdf", 'n')) {
				$msg .= "Error during recording of the parameter mode_utf8_bulletins_pdf !";
			}
		}
		/*
		if (isset($_POST['mode_utf8_listes_pdf'])) {
			if (!saveSetting("mode_utf8_listes_pdf", $_POST['mode_utf8_listes_pdf'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_listes_pdf !";
			}
		}
		else{
			if (!saveSetting("mode_utf8_listes_pdf", 'n')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_listes_pdf !";
			}
		}
		*/
		if (isset($_POST['mode_utf8_visu_notes_pdf'])) {
			if (!saveSetting("mode_utf8_visu_notes_pdf", $_POST['mode_utf8_visu_notes_pdf'])) {
				$msg .= "Error during recording of the parameter mode_utf8_visu_notes_pdf !";
			}
		}
		else{
			if (!saveSetting("mode_utf8_visu_notes_pdf", 'n')) {
				$msg .= "Error during recording of the parameter mode_utf8_visu_notes_pdf !";
			}
		}

		if (isset($_POST['mode_utf8_listes_pdf'])) {
			if (!saveSetting("mode_utf8_listes_pdf", $_POST['mode_utf8_listes_pdf'])) {
				$msg .= "Error during recording of the parameter mode_utf8_listes_pdf !";
			}
		}
		else{
			if (!saveSetting("mode_utf8_listes_pdf", 'n')) {
				$msg .= "Error during recording of the parameter mode_utf8_listes_pdf !";
			}
		}
	
		if (isset($_POST['type_bulletin_par_defaut'])) {
			if(($_POST['type_bulletin_par_defaut']=='html')||($_POST['type_bulletin_par_defaut']=='pdf')) {
				if (!saveSetting("type_bulletin_par_defaut", $_POST['type_bulletin_par_defaut'])) {
					$msg .= "Error during recording of the parameter type_bulletin_par_defaut !";
				}
			}
			else {
				$msg .= "Erroneous value for the recording of the parameter type_bulletin_par_defaut !";
			}
		}
	
		/*
		if (isset($_POST['mode_utf8_releves_pdf'])) {
			if (!saveSetting("mode_utf8_releves_pdf", $_POST['mode_utf8_releves_pdf'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_releves_pdf !";
			}
		}
		else{
			if (!saveSetting("mode_utf8_releves_pdf", 'n')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_releves_pdf !";
			}
		}
		*/
	
		if (isset($_POST['exp_imp_chgt_etab'])) {
			if (!saveSetting("exp_imp_chgt_etab", $_POST['exp_imp_chgt_etab'])) {
				$msg .= "Error during recording of the parameter exp_imp_chgt_etab !";
			}
		}
		else{
			if (!saveSetting("exp_imp_chgt_etab", 'no')) {
				$msg .= "Error during recording of the parameter exp_imp_chgt_etab !";
			}
		}
	
		if (isset($_POST['ele_lieu_naissance'])) {
			if (!saveSetting("ele_lieu_naissance", $_POST['ele_lieu_naissance'])) {
				$msg .= "Error during recording of the parameter ele_lieu_naissance !";
			}
		}
		else{
			if (!saveSetting("ele_lieu_naissance", 'no')) {
				$msg .= "Error during recording of the parameter ele_lieu_naissance !";
			}
		}
	
		if (isset($_POST['avis_conseil_classe_a_la_mano'])) {
			if (!saveSetting("avis_conseil_classe_a_la_mano", $_POST['avis_conseil_classe_a_la_mano'])) {
				$msg .= "Error during recording of the parameter avis_conseil_classe_a_la_mano !";
			}
		}
		else{
			if (!saveSetting("avis_conseil_classe_a_la_mano", 'n')) {
				$msg .= "Error during recording of the parameter avis_conseil_classe_a_la_mano !";
			}
		}
	
	
		//===============================================================
	
	
		// Dénomination du professeur de suivi
		if (isset($_POST['gepi_prof_suivi'])) {
			if (!saveSetting("gepi_prof_suivi", $_POST['gepi_prof_suivi'])) {
				$msg .= "Error during recording of gepi_prof_suivi !";
			}
		}
		
		// Dénomination des professeurs
		if (isset($_POST['denomination_professeur'])) {
			if (!saveSetting("denomination_professeur", $_POST['denomination_professeur'])) {
				$msg .= "Error during recording of denomination_professeur !";
			}
		}
		if (isset($_POST['denomination_professeurs'])) {
			if (!saveSetting("denomination_professeurs", $_POST['denomination_professeurs'])) {
				$msg .= "Error during recording of denomination_professeurs !";
			}
		}
		
		// Dénomination des responsables légaux
		if (isset($_POST['denomination_responsable'])) {
			if (!saveSetting("denomination_responsable", $_POST['denomination_responsable'])) {
				$msg .= "Error during recording of denomination_responsable !";
			}
		}
		if (isset($_POST['denomination_responsables'])) {
			if (!saveSetting("denomination_responsables", $_POST['denomination_responsables'])) {
				$msg .= "Error during recording of denomination_responsables !";
			}
		}
		
		// Dénomination des élèves
		if (isset($_POST['denomination_eleve'])) {
			if (!saveSetting("denomination_eleve", $_POST['denomination_eleve'])) {
				$msg .= "Error during recording of denomination_eleve !";
			}
		}
		if (isset($_POST['denomination_eleves'])) {
			if (!saveSetting("denomination_eleves", $_POST['denomination_eleves'])) {
				$msg .= "Error during recording of denomination_eleves !";
			}
		}
		// Initialiser à 'Boite'
		if (isset($_POST['gepi_denom_boite'])) {
			if (!saveSetting("gepi_denom_boite", $_POST['gepi_denom_boite'])) {
				$msg .= "Error during recording of gepi_denom_boite !";
			}
		}
		if (isset($_POST['gepi_denom_boite_genre'])) {
			if (!saveSetting("gepi_denom_boite_genre", $_POST['gepi_denom_boite_genre'])) {
				$msg .= "Error during recording of gepi_denom_boite_genre !";
			}
		}

		if((isset($_POST['gepi_denom_mention']))&&($_POST['gepi_denom_mention']!="")) {
			if (!saveSetting("gepi_denom_mention", $_POST['gepi_denom_mention'])) {
				$msg .= "Error during recording of gepi_denom_mention !";
			}
		}
		else {
			if (!saveSetting("gepi_denom_mention", "mention")) {
				$msg .= "Error during recording of gepi_denom_mention !";
			}
		}

		if (isset($_POST['gepi_stylesheet'])) {
			if (!saveSetting("gepi_stylesheet", $_POST['gepi_stylesheet'])) {
				$msg .= "Error during recording of the school year !";
			}
		}
		
		if (isset($_POST['num_enregistrement_cnil'])) {
			if (!saveSetting("num_enregistrement_cnil", $_POST['num_enregistrement_cnil'])) {
				$msg .= "Error during recording of the number of recording to the CNIL !";
			}
		}
		
		if (isset($_POST['mode_generation_login'])) {
			if (!saveSetting("mode_generation_login", $_POST['mode_generation_login'])) {
				$msg .= "Error during recording of the mode of generation of the logins !";
			}
			// On en profite pour mettre à jour la variable $longmax_login -> settings : longmax_login
					$nbre_carac = 12;
				if ($_POST['mode_generation_login'] == 'name8' OR $_POST['mode_generation_login'] == 'fname8' OR $_POST['mode_generation_login'] == 'namef8') {
					$nbre_carac = 8;
				}
				elseif ($_POST['mode_generation_login'] == 'fname19' OR $_POST['mode_generation_login'] == 'firstdotname19') {
					$nbre_carac = 19;
				}
				elseif ($_POST['mode_generation_login'] == 'firstdotname') {
					$nbre_carac = 30;
				}
				else {
					$nbre_carac = 12;
				}
			$req = "UPDATE setting SET value = '".$nbre_carac."' WHERE name = 'longmax_login'";
			$modif_maxlong = mysql_query($req);
		}
		
		
		if (isset($_POST['unzipped_max_filesize'])) {
			$unzipped_max_filesize=$_POST['unzipped_max_filesize'];
			if(substr($unzipped_max_filesize,0,1)=="-") {$unzipped_max_filesize=-1;}
			elseif(strlen(my_ereg_replace("[0-9]","",$unzipped_max_filesize))!=0) {
				$unzipped_max_filesize=10;
				$msg .= "Invalid characters for the parameter unzipped_max_filesize<br />Initialization to 10 Mo !";
			}
		
			if (!saveSetting("unzipped_max_filesize", $unzipped_max_filesize)) {
				$msg .= "Error during recording of the parameter unzipped_max_filesize !";
			}
		}


		if (isset($_POST['bul_rel_nom_matieres'])) {
			$bul_rel_nom_matieres=$_POST['bul_rel_nom_matieres'];
			if (!saveSetting("bul_rel_nom_matieres", $bul_rel_nom_matieres)) {
				$msg .= "Error during recording of the parameter bul_rel_nom_matieres !";
			}
		}



		if (isset($_POST['delais_apres_cloture'])) {
			$delais_apres_cloture=$_POST['delais_apres_cloture'];
			if (!(my_ereg ("^[0-9]{1,}$", $delais_apres_cloture)) || $delais_apres_cloture < 0) {
				//$delais_apres_cloture=0;
				$msg .= "Error during recording of delais_apres_cloture !";
			}
			else {
				if (!saveSetting("delais_apres_cloture", $delais_apres_cloture)) {
					$msg .= "Error during recording of delais_apres_cloture !";
				}
			}
		}
		
		if (isset($_POST['acces_app_ele_resp'])) {
			$acces_app_ele_resp=$_POST['acces_app_ele_resp'];
			if (!saveSetting("acces_app_ele_resp", $acces_app_ele_resp)) {
				$msg .= "Error during recording of acces_app_ele_resp !";
			}
		}



	}
}


if (isset($_POST['gepi_pmv'])) {
	check_token();

	if (!saveSetting("gepi_pmv", $_POST['gepi_pmv'])) {
		$msg .= "Error during recording of gepi_pmv !";
	}
}


/*
if(isset($_POST['is_posted'])){
	if (isset($_POST['export_cn_ods'])) {
		//if (!saveSetting("export_cn_ods", $_POST['export_cn_ods'])) {
		if (!saveSetting("export_cn_ods", 'y')) {
			$msg .= "Erreur lors de l'enregistrement de l'autorisation de l'export au format ODS !";
		}
	}
	else{
		if (!saveSetting("export_cn_ods", 'n')) {
			$msg .= "Erreur lors de l'enregistrement de l'interdiction de l'export au format ODS !";
		}
	}
}
*/

// Load settings
if (!loadSettings()) {
	die("Error loading settings");
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "The modifications were recorded !";


//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Information was modified. Do you really want to leave without recording ?';
//**************** EN-TETE *****************
// End standart header
$titre_page = "General parameters";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

?>
<p class=bold><a href="index.php#param_gen"<?php
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
?>><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>
<form action="param_gen.php" method="post" name="form1" style="width: 100%;">
<?php
echo add_token_field();
?>
<table style="width: 100%; border: 0;" cellpadding="5" cellspacing="5" summary='Paramètres'>
	<tr>
		<td style="width: 60%;font-variant: small-caps;">
		School year :
		</td>
		<td><input type="text" name="gepiYear" size="20" value="<?php echo(getSettingValue("gepiYear")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Number RNE of the school:
		</td>
		<td><input type="text" name="gepiSchoolRne" size="8" value="<?php echo(getSettingValue("gepiSchoolRne")); ?>" onchange='changement()' />
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		Name of the school:
		</td>
		<td><input type="text" name="gepiSchoolName" size="20" value="<?php echo(getSettingValue("gepiSchoolName")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Statute of the school :<br />
		(<span style='font-style:italic;font-size:x-small'>used for certain official documents</span>)
		</td>
		<td>
                    <select name='gepiSchoolStatut' onchange='changement()'>
			<option value='public'<?php if (getSettingValue("gepiSchoolStatut")=='public') echo " SELECTED"; ?>>public school</option>
			<option value='prive_sous_contrat'<?php if (getSettingValue("gepiSchoolStatut")=='prive_sous_contrat') echo " SELECTED"; ?>>Private school under contract</option>
			<option value='prive_hors_contrat'<?php if (getSettingValue("gepiSchoolStatut")=='prive_hors_contrat') echo " SELECTED"; ?>>Private school except contract</option>
                    </select>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		School address :
		</td>
		<td><input type="text" name="gepiSchoolAdress1" size="40" value="<?php echo(getSettingValue("gepiSchoolAdress1")); ?>" onchange='changement()' /><br />
		<input type="text" name="gepiSchoolAdress2" size="40" value="<?php echo(getSettingValue("gepiSchoolAdress2")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Postal code:
		</td>
		<td><input type="text" name="gepiSchoolZipCode" size="20" value="<?php echo(getSettingValue("gepiSchoolZipCode")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		City:
		</td>
		<td><input type="text" name="gepiSchoolCity" size="20" value="<?php echo(getSettingValue("gepiSchoolCity")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Country :<br />
		(<span style='font-style:italic;font-size:x-small'>The country is used to compare with that of the responsibles in the blocks addresses mails addressed to the responsibles</span>)
		</td>
		<td><input type="text" name="gepiSchoolPays" size="20" value="<?php echo(getSettingValue("gepiSchoolPays")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Academy :<br />
		(<span style='font-style:italic;font-size:x-small'>used for certain official documents</span>)
		</td>
		<td><input type="text" name="gepiSchoolAcademie" size="20" value="<?php echo(getSettingValue("gepiSchoolAcademie")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		school Telephone :
		</td>
		<td><input type="text" name="gepiSchoolTel" size="20" value="<?php echo(getSettingValue("gepiSchoolTel")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		school Fax :
		</td>
		<td><input type="text" name="gepiSchoolFax" size="20" value="<?php echo(getSettingValue("gepiSchoolFax")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		school E-mail :
		</td>
		<td><input type="text" name="gepiSchoolEmail" size="20" value="<?php echo(getSettingValue("gepiSchoolEmail")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Name of the administrator of the site:
		</td>
		<td><input type="text" name="gepiAdminNom" size="20" value="<?php echo(getSettingValue("gepiAdminNom")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		First name of the administrator of the site:
		</td>
		<td><input type="text" name="gepiAdminPrenom" size="20" value="<?php echo(getSettingValue("gepiAdminPrenom")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Function of the administrator of the site:
		</td>
		<td><input type="text" name="gepiAdminFonction" size="20" value="<?php echo(getSettingValue("gepiAdminFonction")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Email of the administrator of the site:
		</td>
		<td><input type="text" name="gepiAdminAdress" size="20" value="<?php echo(getSettingValue("gepiAdminAdress")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<label for='gepiAdminAdressPageLogin' style='cursor: pointer;'>Make appear the link [Contact the administrator] on the page of login :</label>
		</td>
		<td>
		<input type="checkbox" id='gepiAdminAdressPageLogin' name="gepiAdminAdressPageLogin" value="y"
		<?php
			if(getSettingValue("gepiAdminAdressPageLogin")!='n'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<label for='gepiAdminAdressFormHidden' style='cursor: pointer;'>Make appear the address of the administrator in the form [ Contact the administrator ] :</label>
		</td>
		<td>
		<input type="checkbox" name="gepiAdminAdressFormHidden" id="gepiAdminAdressFormHidden" value="n"
		<?php
			if(getSettingValue("gepiAdminAdressFormHidden")!='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<label for='contact_admin_mailto' style='cursor: pointer;'>Replace the form [Contact the administrator] by a link mailto :</label>
		</td>
		<td>
		<input type="checkbox" id='contact_admin_mailto' name="contact_admin_mailto" value="y"
		<?php
			if(getSettingValue("contact_admin_mailto")=='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<label for='envoi_mail_liste' style='cursor: pointer;'>Allow to send malls to a list of students :<br />
		<span style='font-size: small'>(<i>provided the malls are filled</i>)</span><br />
		<span style='font-size: small'>We draw your attention to the fact that to send a mall to a list of users via a link mailto allows each student to know the email of the other students without the authorization of disclosure or
not parameterized in <b>Manage my account</b> is  aken into account.</span></label>
		</td>
		<td valign='top'>
		<input type="checkbox" id='envoi_mail_liste' name="envoi_mail_liste" value="y"
		<?php
			if(getSettingValue("envoi_mail_liste")=='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<a name='sessionMaxLength'></a>Maximum duration of inactivity : <br />
		<span class='small'>(<i>Duration of inactivity, in minutes, at the end of which a user is automatically disconnected from Gepi.</i>) <b>Caution</b>, the variable <b>session.maxlifetime</b> in the file <b>php.ini</b> is regulated to <?php 
			$session_gc_maxlifetime=ini_get("session.gc_maxlifetime");
			$session_gc_maxlifetime_minutes=$session_gc_maxlifetime/60;

			if((getSettingValue("sessionMaxLength")!="")&&($session_gc_maxlifetime_minutes<getSettingValue("sessionMaxLength"))) {
				echo "<span style='color:red; font-weight:bold;'>".$session_gc_maxlifetime." seconds</span>, that is to say a maximum of <span style='color:red; font-weight:bold;'>".$session_gc_maxlifetime_minutes."minutes</span> for the session (<a href='../mod_serveur/test_serveur.php#reglages_php'>*</a>).";
			}
			else {
				echo $session_gc_maxlifetime." seconds, soit un maximum de ".$session_gc_maxlifetime_minutes."minutes for the session.";
			}
		?></span>
		</td>
		<td><input type="text" name="sessionMaxLength" size="20" value="<?php echo(getSettingValue("sessionMaxLength")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Minimal length of the password :</td>
		<td><input type="text" name="longmin_pwd" size="20" value="<?php echo(getSettingValue("longmin_pwd")); ?>" onchange='changement()' />
		</td>
	</tr>
		<?php 
			if (isset($use_custom_denominations) && $use_custom_denominations) {
		?>
	<tr>
		<td style="font-variant: small-caps;">
		Denomination of the professors :</td>
		<td>Sing. :<input type="text" name="denomination_professeur" size="20" value="<?php echo(getSettingValue("denomination_professeur")); ?>" onchange='changement()' />
		<br/>Plural :<input type="text" name="denomination_professeurs" size="20" value="<?php echo(getSettingValue("denomination_professeurs")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Denomination of the students :</td>
		<td>Sing. :<input type="text" name="denomination_eleve" size="20" value="<?php echo(getSettingValue("denomination_eleve")); ?>" />
		<br/>Plural :<input type="text" name="denomination_eleves" size="20" value="<?php echo(getSettingValue("denomination_eleves")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Denomination of the legal responsibles :</td>
		<td>Sing. :<input type="text" name="denomination_responsable" size="20" value="<?php echo(getSettingValue("denomination_responsable")); ?>" onchange='changement()' />
		<br/>Plural :<input type="text" name="denomination_responsables" size="20" value="<?php echo(getSettingValue("denomination_responsables")); ?>" onchange='changement()' />
		</td>
	</tr>
		<?php 
			} 
		?>
	<tr>
		<td style="font-variant: small-caps;">
		Denomination of the professor in charge of the follow-up of the students :</td>
		<td><input type="text" name="gepi_prof_suivi" size="20" value="<?php echo(getSettingValue("gepi_prof_suivi")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		Designation of box/containers/Positions/sub-course :</td>
		<td>
		<input type="text" name="gepi_denom_boite" size="20" value="<?php echo(getSettingValue("gepi_denom_boite")); ?>" onchange='changement()' /><br />
		<table summary='Genre'><tr valign='top'><td>Gender :</td><td>
		<input type="radio" name="gepi_denom_boite_genre" id="gepi_denom_boite_genre_m" value="m" <?php if(getSettingValue("gepi_denom_boite_genre")=="m"){echo 'checked';} ?> onchange='changement()' /> <label for='gepi_denom_boite_genre_m' style='cursor: pointer;'>Masculine</label><br />
		<input type="radio" name="gepi_denom_boite_genre" id="gepi_denom_boite_genre_f" value="f" <?php if(getSettingValue("gepi_denom_boite_genre")=="f"){echo 'checked';} ?> onchange='changement()' /> <label for='gepi_denom_boite_genre_f' style='cursor: pointer;'>Female</label><br />
		</td></tr></table>
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<a name='gepi_denom_mention'></a>
		Designation of "mentions" being able to be typing with the opinion of the staff meeting :<br />
		(<i>terme au singulier</i>)<br />
		<a href='../saisie/saisie_mentions.php' <?php 
			echo "onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		?>>Define "mentions"</a></td>
		<td>
		<input type="text" name="gepi_denom_mention" size="20" value="<?php
			
			$gepi_denom_mention=getSettingValue("gepi_denom_mention");
			if($gepi_denom_mention=="") {
				$gepi_denom_mention="mention";
			}

			echo $gepi_denom_mention;
		?>" onchange='changement()' /><br />
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		<a name='format_login_resp'></a>
		Automatic mode of generation of the logins :</td>
	<td>
	<select name='mode_generation_login' onchange='changement()'>
			<option value='name8'<?php if (getSettingValue("mode_generation_login")=='name8') echo " SELECTED"; ?>> nom (truncated to 8 characters)</option>
			<option value='fname8'<?php if (getSettingValue("mode_generation_login")=='fname8') echo " SELECTED"; ?>> pnom (truncated to 8 characters)</option>
			<option value='fname19'<?php if (getSettingValue("mode_generation_login")=='fname19') echo " SELECTED"; ?>> pnom (truncated to 19 characters)</option>
			<option value='firstdotname'<?php if (getSettingValue("mode_generation_login")=='firstdotname') echo " SELECTED"; ?>> prenom.nom (truncated to 30 characters)</option>
			<option value='firstdotname19'<?php if (getSettingValue("mode_generation_login")=='firstdotname19') echo " SELECTED"; ?>> prenom.nom (tronqué to 19 characters)</option>
			<option value='namef8'<?php if (getSettingValue("mode_generation_login")=='namef8') echo " SELECTED"; ?>> nomp (truncated to 8 characters)</option>
	</select>
	</td>
	</tr>


	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		Mode of generation of the passwords :<br />(<i style='font-size:small;'>Character set to be used in addition to the numerical characters</i>)</td>
	<td valign='top'>
		<input type="radio" name="mode_generation_pwd_majmin" id="mode_generation_pwd_majmin_y" value="y" <?php if((getSettingValue("mode_generation_pwd_majmin")=="y")||(getSettingValue("mode_generation_pwd_majmin")=="")) {echo 'checked';} ?> onchange='changement()' /> <label for='mode_generation_pwd_majmin_y' style='cursor: pointer;'>Capital letters and minuscules</label><br />
		<input type="radio" name="mode_generation_pwd_majmin" id="mode_generation_pwd_majmin_n" value="n" <?php if(getSettingValue("mode_generation_pwd_majmin")=="n"){echo 'checked';} ?> onchange='changement()' /> <label for='mode_generation_pwd_majmin_n' style='cursor: pointer;'>minuscules only</label><br />

		<table border='0' summary='Pass'>
		<tr>
		<td valign='top'>
		<input type="checkbox" name="mode_generation_pwd_excl" id="mode_generation_pwd_excl" value="y" <?php if(getSettingValue("mode_generation_pwd_excl")=="y") {echo 'checked';} ?> onchange='changement()' />
		</td>
		<td valign='top'> <label for='mode_generation_pwd_excl' style='cursor: pointer;'>Exclude the characters lending to confusion (<i>i, 1, l, I, 0, O, o</i>)</label><br />
		</td>
		</tr>
		</table>
	</td>
	</tr>




	<tr>
	<td style="font-variant: small-caps;" valign='top'>
		<a name='mode_email_resp'></a>
		<!--Mode de mise à jour des emails responsables et élèves :<br />(<i style='font-size:small;'>Les élèves et responsables peuvent avoir un email dans deux tables s'ils disposent d'un compte utilisateur ('eleves' et 'utilisateurs' pour les premiers, 'resp_pers' et 'utilisateurs' pour les seconds)<br />Ces email peuvent donc se trouver non synchronisés entre les tables</i>)-->
		Mode of update of the responsibles emails  :<br />(<i style='font-size:small;'>The responsibles can have an email in two tables if they have an to use
account  ('resp_pers' et 'utilisateurs')<br />These email can thus be not synchronized between the tables</i>)
	</td>
	<td valign='top'>
		<input type="radio" name="mode_email_resp" id="mode_email_resp_sconet" value="sconet" <?php if((getSettingValue("mode_email_resp")=="sconet")||(getSettingValue("mode_email_resp")=="")) {echo 'checked';} ?> onchange='changement()' /> <label for='mode_email_resp_sconet' style='cursor: pointer;'> Update of the email via Sconet only</label><br />
		<input type="radio" name="mode_email_resp" id="mode_email_resp_mon_compte" value="mon_compte" <?php if(getSettingValue("mode_email_resp")=="mon_compte"){echo 'checked';} ?> onchange='changement()' /> <label for='mode_email_resp_mon_compte' style='cursor: pointer;'>Update of the email only from Managing my account<br />&nbsp;&nbsp;&nbsp;&nbsp;(<i>modifications in Sconet not taken into account</i>)<br />&nbsp;&nbsp;&nbsp;&nbsp;(<i>except sso, see in this case [<a href='options_connect.php#cas_attribut_email'>Options of connection</a>]</i>)</label><br />
		<!--
		<input type="radio" name="mode_email_resp" id="mode_email_resp_sso" value="sso" <?php if(getSettingValue("mode_email_resp")=="sso"){echo 'checked';} ?> onchange='changement()' /> <label for='mode_email_resp_sso' style='cursor: pointer;'>Mise à jour de l'email via SSO (<i>???</i>)</label><br />
		-->
	</td>
	</tr>


	<tr>
	<td style="font-variant: small-caps;" valign='top'>
		<a name='mode_email_ele'></a>
		Mode of update of the students emails  :<br />(<i style='font-size:small;'>The students can have an email in two tables if they have an user
account ('eleves' et 'utilisateurs')<br />These email can thus be not synchronized between the tables</i>)
	</td>
	<td valign='top'>
		<input type="radio" name="mode_email_ele" id="mode_email_ele_sconet" value="sconet" <?php if((getSettingValue("mode_email_ele")=="sconet")||(getSettingValue("mode_email_ele")=="")) {echo 'checked';} ?> onchange='changement()' /> <label for='mode_email_ele_sconet' style='cursor: pointer;'>Update of the email via Sconet only</label><br />
		<input type="radio" name="mode_email_ele" id="mode_email_ele_mon_compte" value="mon_compte" <?php if(getSettingValue("mode_email_ele")=="mon_compte"){echo 'checked';} ?> onchange='changement()' /> <label for='mode_email_ele_mon_compte' style='cursor: pointer;'>Update of the email only from Manage my account<br />&nbsp;&nbsp;&nbsp;&nbsp;(<i>modifications in Sconet not taken into account</i>)<br />&nbsp;&nbsp;&nbsp;&nbsp;(<i>except sso, see in this case [<a href='options_connect.php#cas_attribut_email'> Options of connection</a>]</i>)</label><br />
		<!--
		<input type="radio" name="mode_email_ele" id="mode_email_ele_sso" value="sso" <?php if(getSettingValue("mode_email_ele")=="sso"){echo 'checked';} ?> onchange='changement()' /> <label for='mode_email_ele_sso' style='cursor: pointer;'>Mise à jour de l'email via SSO (<i>???</i>)</label><br />
		-->
	</td>
	</tr>


	<!-- Traitement des problemes de points d'interrogation à la place des accents -->
<?php
/*
	// Apparemment, ce n'est pas utile...
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_releves_pdf' style='cursor: pointer;'>Traitement UTF8 des caractères accentués des relevés de notes PDF&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_releves_pdf' name="mode_utf8_releves_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_releves_pdf")=='y'){echo " checked";}
		?>
		/>
		</td>
	</tr>
*/
?>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_visu_notes_pdf' style='cursor: pointer;'>UTF8 treatment of the characters accentuated in the visualization of the notes of the report card&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_visu_notes_pdf' name="mode_utf8_visu_notes_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_visu_notes_pdf")=='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_listes_pdf' style='cursor: pointer;'>UTF8 treatment of the characters accentuated in lists pdf&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_listes_pdf' name="mode_utf8_listes_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_listes_pdf")=='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_bulletins_pdf' style='cursor: pointer;'>UTF8 treatment of the accentuated characters of bulletins pdf&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_bulletins_pdf' name="mode_utf8_bulletins_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_bulletins_pdf")=='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		Default type of bulletins &nbsp;:
		</td>
		<td>
		<input type="radio" id='type_bulletin_par_defaut_pdf' name="type_bulletin_par_defaut" value="pdf"
		<?php
			if(getSettingValue("type_bulletin_par_defaut")=='pdf') {echo " checked";}
		?>
		onchange='changement()' /><label for='type_bulletin_par_defaut_pdf'>&nbsp;PDF</label><br />
		<input type="radio" id='type_bulletin_par_defaut_html' name="type_bulletin_par_defaut" value="html"
		<?php
			if(getSettingValue("type_bulletin_par_defaut")!='pdf') {echo " checked";}
		?>
		onchange='changement()' /><label for='type_bulletin_par_defaut_html'>&nbsp;HTML</label>
		</td>
	</tr>

<?php
/*
	// Apparemment, ce n'est pas utile...
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_listes_pdf' style='cursor: pointer;'>Traitement UTF8 des caractères accentués des listes PDF&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_listes_pdf' name="mode_utf8_listes_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_listes_pdf")=='y'){echo " checked";}
		?>
		/>
		</td>
	</tr>
*/
?>
	<tr>
		<td style="font-variant: small-caps;">
		Style sheet to be used :</td>
	<td>
	<select name='gepi_stylesheet' onchange='changement()'>
			<option value='style'<?php if (getSettingValue("gepi_stylesheet")=='style') echo " SELECTED"; ?>> New design</option>
			<option value='style_old'<?php if (getSettingValue("gepi_stylesheet")=='style_old') echo " SELECTED"; ?>> Design close to the old versions (1.4.*)</option>
	</select>
	</td>
	</tr>
	<?php
/*
		echo "<tr>\n";
		if(file_exists("../lib/ss_zip.class.php")){
			echo "<td style='font-variant: small-caps;'>Permettre l'export des carnets de notes au format ODS :<br />(<i>si les professeurs ne font pas le ménage après génération des exports,<br />ces fichiers peuvent prendre de la place sur le serveur</i>)</td>\n";
			echo "<td><input type='checkbox' name='export_cn_ods' value='y'";
			if(getSettingValue('export_cn_ods')=='y'){
				echo ' checked';
			}
			echo " />";
			echo "</td>\n";
		}
		else{
			echo "<td style='font-variant: small-caps;'>En mettant en place la bibliothèque 'ss_zip_.class.php' dans le dossier '/lib/', vous pouvez générer des fichiers tableur ODS pour permettre des saisies hors ligne, la conservation de données,...<br />Voir <a href='http://smiledsoft.com/demos/phpzip/' style=''>http://smiledsoft.com/demos/phpzip/</a><br />Une version limitée est disponible gratuitement.</td>\n";
			echo "<td>&nbsp;</td>\n";

			// Comme la bibliothèque n'est pas présente, on force la valeur à 'n':
			$svg_param=saveSetting("export_cn_ods", 'n');
		}
		echo "</tr>\n";
*/
	?>
	<?php
		echo "<tr>\n";
		if(file_exists("../lib/pclzip.lib.php")){
			echo "<td style='font-variant: small-caps;'>Maximum size extracted from unzipped files:<br />
(<i style='font-size:small;'>A unzipped file can take place enormously.<br />
By prudence, it is advisable to fix a limit at the size of an extracted file.<br />
By putting zero, you do not fix any limit.<br />
By putting a negative value, you decontaminate the désarchivage</i>)</td>\n";
			echo "<td valign='top'><input type='text' name='unzipped_max_filesize' value='";
			$unzipped_max_filesize=getSettingValue('unzipped_max_filesize');
			if($unzipped_max_filesize==""){
				echo '10';
			}
			else {
				echo $unzipped_max_filesize;
			}
			echo "' size='3' onchange='changement()' /> Mo";
			echo "</td>\n";
		}
		else{
			echo "<td style='font-variant: small-caps;'>By setting up the library 'pclzip.lib.php' in the folder '/lib/', you can send Zipped files to the server.<br />See <a href='http://www.phpconcept.net/pclzip/index.php' style=''";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">http://www.phpconcept.net/pclzip/index.php</a></td>\n";
			echo "<td>&nbsp;</td>\n";
		}
		echo "</tr>\n";
	?>

	<!--tr>
		<td style="font-variant: small-caps;">
		<a name='delais_apres_cloture'></a>
		Nombre de jours avant déverrouillage de l'accès aux appréciations des bulletins pour les responsables et les élèves une fois la période close&nbsp;:<br />
		<div style='font-variant: normal; font-style: italic; font-size: small;'>Sous réserve:<br />
		<ul>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>de créer des comptes pour les responsables et élèves,</li>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>d'autoriser l'accès aux bulletins simplifiés ou aux graphes dans <a href='droits_acces.php'<?php
			//echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			?>>Droits d'accès</a></li>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>d'opter pour le mode de déverrouillage automatique sur le critère "période close".</li>
		</ul>
		</div>
		</td>
		<td valign='top'>
			<?php
			/*
			$delais_apres_cloture=getSettingValue("delais_apres_cloture");
			if($delais_apres_cloture=="") {$delais_apres_cloture=0;}
			echo "<input type='text' name='delais_apres_cloture' size='2' value='$delais_apres_cloture' onchange='changement()' />\n";
			*/
			?>
		</td>
	</tr-->



	<tr>
		<td style="font-variant: small-caps; vertical-align:top;">
		<a name='bul_rel_nom_matieres'></a>
		For the column course in the bulletins and report booklets, to use&nbsp;:
		</td>
		<td valign='top'>

			<?php
			$bul_rel_nom_matieres=getSettingValue("bul_rel_nom_matieres");
			if($bul_rel_nom_matieres=="") {$bul_rel_nom_matieres="nom_complet_matiere";}

			echo "<input type='radio' name='bul_rel_nom_matieres' id='bul_rel_nom_matieres_nom_complet_matiere' value='nom_complet_matiere'";
			if($bul_rel_nom_matieres=='nom_complet_matiere') {echo " checked";}
			echo " onchange='changement()' />\n";
			echo "<label for='bul_rel_nom_matieres_nom_complet_matiere' style='cursor: pointer'> the complete course name</label>\n";
			echo "<br />\n";

			echo "<input type='radio' name='bul_rel_nom_matieres' id='bul_rel_nom_matieres_nom_groupe' value='nom_groupe'";
			if($bul_rel_nom_matieres=='nom_groupe') {echo " checked";}
			echo " onchange='changement()' />";
			echo "<label for='bul_rel_nom_matieres_nom_groupe' style='cursor: pointer'> the name (short) of the group</label>\n";
			echo "<br />\n";

			echo "<input type='radio' name='bul_rel_nom_matieres' id='bul_rel_nom_matieres_description_groupe' value='description_groupe'";
			if($bul_rel_nom_matieres=='description_groupe') {echo " checked";}
			echo " onchange='changement()' />";
			echo "<label for='bul_rel_nom_matieres_description_groupe' style='cursor: pointer'> the description of the group</label>\n";
			?>
		</td>
	</tr>



	<tr>
		<td style="font-variant: small-caps;">
		<a name='mode_ouverture_acces_appreciations'></a>
		<a name='delais_apres_cloture'></a>
		Access mode to the bulletins and results graphic, for the students
and their responsibles&nbsp;:<br />
		<div style='font-variant: normal; font-style: italic; font-size: small;'>Provided:<br />
		<ul>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>to create accounts for the responsibles and students,</li>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>to authorize the access to the simplified bulletins or the graphs in <a href='droits_acces.php#bull_simp_ele'<?php
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			?>>Access rights </a></li>
		</ul>
		</div>
		</td>
		<td valign='top'>
			<?php
			$acces_app_ele_resp=getSettingValue("acces_app_ele_resp");
			if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';}

			echo "<input type='radio' name='acces_app_ele_resp' id='acces_app_ele_resp_manuel' value='manuel' onchange='changement()' ";
			if($acces_app_ele_resp=='manuel') {echo "checked ";}
			echo "/><label for='acces_app_ele_resp_manuel'>manual (<i>opened by the schooling, class by class</i>)</label><br />\n";

			echo "<input type='radio' name='acces_app_ele_resp' id='acces_app_ele_resp_date' value='date' onchange='changement()' ";
			if($acces_app_ele_resp=='date') {echo "checked ";}
			echo "/><label for='acces_app_ele_resp_date'>at a selected date (<i>by the schooling</i>)</label><br />\n";

			$delais_apres_cloture=getSettingValue("delais_apres_cloture");
			if($delais_apres_cloture=="") {$delais_apres_cloture=0;}

			echo "<input type='radio' name='acces_app_ele_resp' id='acces_app_ele_resp_periode_close' value='periode_close' onchange='changement()' ";
			if($acces_app_ele_resp=='periode_close') {echo "checked ";}
			echo "/><label for='acces_app_ele_resp_periode_close'> <input type='text' name='delais_apres_cloture' value='$delais_apres_cloture' size='1' onchange='changement()' /> days after the period closure</label>\n";
			?>
		</td>
	</tr>



	<tr>
		<td style="font-variant: small-caps; vertical-align:top;">
		<a name='avis_conseil_classe_a_la_mano'></a>
		The opinions of the council are filled&nbsp;:
		</td>
		<td valign='top'>

			<?php
			$avis_conseil_classe_a_la_mano=getSettingValue("avis_conseil_classe_a_la_mano");
			if($avis_conseil_classe_a_la_mano=="") {$avis_conseil_classe_a_la_mano="n";}

			echo "<input type='radio' name='avis_conseil_classe_a_la_mano' id='avis_conseil_classe_saisis' value='n'";
			if($avis_conseil_classe_a_la_mano=='n') {echo " checked";}
			echo " onchange='changement()' />\n";
			echo "<label for='avis_conseil_classe_saisis' style='cursor: pointer'> before the impression of the bulletins</label>\n";
			echo "<br />\n";
			echo "<input type='radio' name='avis_conseil_classe_a_la_mano' id='avis_conseil_classe_a_la_mano' value='y'";
			if($avis_conseil_classe_a_la_mano=='y') {echo " checked";}
			echo " onchange='changement()' />";
			echo "<label for='avis_conseil_classe_a_la_mano' style='cursor: pointer'> with the hand on the printed bulletins</label>\n";
			?>
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		<a name='ancre_ele_lieu_naissance'></a>
		<label for='ele_lieu_naissance' style='cursor: pointer'>Make appear birthplaces of the students&nbsp;:</label><br />
		<div style='font-variant: normal; font-style: italic; font-size: small;'>
			Conditioned by the use of 'code_commune_insee' imported from Sconet and by the importation of the correspondences 'code_commune_insee/commune' in the table 'communes' from <a href='../eleves/import_communes.php' <?php
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			?>>Importation of the communes</a>.<br />
		</div>
		</td>
		<td valign='top'>
			<?php
			$ele_lieu_naissance=getSettingValue("ele_lieu_naissance");
			if($ele_lieu_naissance=="") {$ele_lieu_naissance="no";}
			echo "<input type='checkbox' name='ele_lieu_naissance' id='ele_lieu_naissance' value='y'";
			if($ele_lieu_naissance=='y') {echo " checked";}
			echo " onchange='changement()' />\n";
			?>
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		<a name='ancre_exp_imp_chgt_etab'></a>
		<label for='exp_imp_chgt_etab' style='cursor: pointer'>Allow export/import bulletins of students to CSV format &nbsp;:</label><br />
		<div style='font-variant: normal; font-style: italic; font-size: small;'>
			The file can be generated for a student which leaves the school in the course of year.<br />
			The school which receives the student can use this file to import the bulletins.<br />
		</div>
		</td>
		<td valign='top'>
			<?php
			$exp_imp_chgt_etab=getSettingValue("exp_imp_chgt_etab");
			if($exp_imp_chgt_etab=="") {$exp_imp_chgt_etab="no";}
			echo "<input type='checkbox' name='exp_imp_chgt_etab' id='exp_imp_chgt_etab' value='yes'";
			if($exp_imp_chgt_etab=='yes') {echo " checked";}
			echo " onchange='changement()' />\n";
			?>
		</td>
	</tr>


	<tr>
		<td style="font-variant: small-caps;">
		N° of recording to the CNIL : <br />
		<span class='small'>In accordance to article 16 of law 78-17 of January 6, 1978, known
as data-processing law and freedom, this installation of GEPI must be
the subject of a declaration of automated treatment of personal
information at the CNIL. If it is not yet the case, leave free the
field opposite</span>
		</td>
		<td><input type="text" name="num_enregistrement_cnil" size="20" value="<?php echo(getSettingValue("num_enregistrement_cnil")); ?>" onchange='changement()' />
		</td>
	</tr>
</table>
<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" name = "OK" value="Save" style="font-variant: small-caps;" /></center>
</form>
<hr />
<form enctype="multipart/form-data" action="param_gen.php" method="post" name="form2" style="width: 100%;">
<?php
echo add_token_field();
?>
<table border='0' cellpadding="5" cellspacing="5" summary='Logo'>
<?php
echo "<tr><td colspan=2 style=\"font-variant: small-caps;\"><b>Logo of the school : </b></td></tr>\n";
echo "<tr><td colspan=2>The logo is visible on the official bulletins, like on the public page of the textbooks</td></tr>\n";
echo "<tr><td>Modify the Logo (png, jpg et gif uniquement) : ";
echo "<input type=\"file\" name=\"doc_file\" onchange='changement()' />\n";
echo "<input type=\"submit\" name=\"valid_logo\" value=\"Save\" /><br />\n";
echo "Supprimer le logo : <input type=\"submit\" name=\"sup_logo\" value=\"Delete the logo\" /></td>\n";


$nom_fic_logo = getSettingValue("logo_etab");

$nom_fic_logo_c = "../images/".$nom_fic_logo;
if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
echo "<td><b>Logo actuel : </b><br /><img src=\"".$nom_fic_logo_c."\" border='0' alt=\"logo\" /></td>\n";
} else {
echo "<td><b><i> No logo currently</i></b></td>\n";
}
echo "</tr></table></form>\n";
?>

<p><i> Remarks&nbsp;</i> Transparencies on the images PNG, GIF do not allow an impression pdf (<i>channel alpha not supported by fpdf</i>).<br />
It was as announced as the JPEG progressive/interlaced can disturb the generation of PDF.</p>

<hr />
<form enctype="multipart/form-data" action="param_gen.php" method="post" name="form3" style="width: 100%;">
<?php
echo add_token_field();
?>
<table border='0' cellpadding="5" cellspacing="5" summary='Pmv'>
	<tr>
		<td style="font-variant: small-caps;">
		Test the presence of the module phpMyVisite (<i>pmv.php</i>) :</td>
	<td>
		<input type="radio" name="gepi_pmv" id="gepi_pmv_y" value="y" <?php if(getSettingValue("gepi_pmv")!="n"){echo 'checked';} ?> onchange='changement()' /><label for='gepi_pmv_y' style='cursor: pointer;'> Yes</label><br />
		<input type="radio" name="gepi_pmv" id="gepi_pmv_n" value="n" <?php if(getSettingValue("gepi_pmv")=="n"){echo 'checked';} ?> onchange='changement()' /><label for='gepi_pmv_n' style='cursor: pointer;'> No</label><br />
	</td>
	</tr>
</table>

<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" name = "OK" value="Save" style="font-variant: small-caps;" /></center>

<table summary='Remarque'><tr><td valign='top'><i>Notice:</i></td><td>It happens that this test of presence causes a display of error (<i>relative to pmv.php</i>).<br />
In this case, simply deactivate the test.</td></tr></table>
</form>
<p><br /></p>

<?php
require("../lib/footer.inc.php");
?>
