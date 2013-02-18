<?php
/*
 * $Id: config_prefs.php 8407 2011-10-01 14:20:24Z crob $
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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// INSERT INTO droits VALUES ('/gestion/consult_prefs.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Définition des préférences d utilisateurs', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


/*
function getPref($login,$item,$default){
	$sql="SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	$res_prefs=mysql_query($sql);

	if(mysql_num_rows($res_prefs)>0){
		$ligne=mysql_fetch_object($res_prefs);
		return $ligne->value;
	}
	else{
		return $default;
	}
}
*/
// Ajout de la possibilité d'afficher ou pas le menu en barre horizontale
$afficherMenu = isset($_POST["afficher_menu"]) ? $_POST["afficher_menu"] : NULL;
$modifier_le_menu = isset($_POST["modifier_le_menu"]) ? $_POST["modifier_le_menu"] : NULL;
$modifier_entete_prof = isset($_POST['modifier_entete_prof']) ? $_POST['modifier_entete_prof'] : NULL;
$page = isset($_GET['page']) ? $_GET['page'] : (isset($_POST['page']) ? $_POST['page'] : NULL);
$prof = isset($_POST['prof']) ? $_POST['prof'] : NULL;
$enregistrer=isset($_POST['enregistrer']) ? $_POST['enregistrer'] : NULL;
$msg="";

if($_SESSION['statut']!="administrateur"){
	unset($prof);
	$prof = array($_SESSION['login']);
}
// +++++++++++++++++++++ MENU en barre horizontale ++++++++++++++++++++

	// Petite fonction pour déterminer le checked="checked" des input en tenant compte des deux utilisations (admin et prof)
	function eval_checked($Settings, $yn, $statut, $nom){
		$aff_check = '';
		if ($statut == "professeur") {
			/*
			$req_setting = mysql_fetch_array(mysql_query("SELECT value FROM preferences WHERE login = '".$nom."' AND name = '".$Settings."'"))
								OR DIE ('Erreur requête eval_setting (prof) : '.mysql_error());
			*/
			$test=mysql_query("SELECT value FROM preferences WHERE login = '".$nom."' AND name = '".$Settings."'");
			if(mysql_num_rows($test)>0) {
				$req_setting = mysql_fetch_array($test);
			}
		}
		elseif ($statut == "administrateur") {

			$test=mysql_query("SELECT value FROM setting WHERE name = '".$Settings."'");
			if(mysql_num_rows($test)>0) {
				$req_setting = mysql_fetch_array($test);
			}
		}

		if((isset($req_setting["value"]))&&($req_setting["value"]==$yn)) {
			$aff_check = ' checked="checked"';
		}else {
			$aff_check = '';
		}

		return $aff_check;
	} //function eval_checked()

	if (!isset($niveau_arbo)) {$niveau_arbo = 1;}
	
	if ($niveau_arbo == "0") {
		$chemin_sound="./sounds/";
	} elseif ($niveau_arbo == "1") {
		$chemin_sound="../sounds/";
	} elseif ($niveau_arbo == "2") {
		$chemin_sound="../../sounds/";
	} elseif ($niveau_arbo == "3") {
		$chemin_sound="../../../sounds/";
	}
	$tab_sound=get_tab_file($chemin_sound);

	if((count($tab_sound)>0)&&(isset($_POST['footer_sound']))&&(in_array($_POST['footer_sound'],$tab_sound))&&(preg_match('/\.wav/i',$_POST['footer_sound']))&&(file_exists($chemin_sound.$_POST['footer_sound']))) {
		$footer_sound_pour_qui=isset($_POST['footer_sound_pour_qui']) ? $_POST['footer_sound_pour_qui'] : 'perso';
		$statut_sound=array();
		$nb_err_sound=0;
		$nb_reg_sound=0;
		if(($footer_sound_pour_qui=='perso')||($_SESSION['statut']!='administrateur')) {
			if(!savePref($_SESSION['login'],'footer_sound',$_POST['footer_sound'])) {
				$msg.="Error during recording of the sound alarm of end of session.<br />";
			}
			else {
				$msg.="Recording of the sound alarm of end of session carried out.<br />";
			}
		}
		elseif($footer_sound_pour_qui=='tous_profs') {
			$statut_sound[]='professeur';
		}
		elseif($footer_sound_pour_qui=='tous_personnels') {
			$statut_sound[]='administrateur';
			$statut_sound[]='professeur';
			$statut_sound[]='scolarite';
			$statut_sound[]='cpe';
			$statut_sound[]='secours';
			$statut_sound[]='autre';
		}
		elseif($footer_sound_pour_qui=='tous') {
			$statut_sound[]='administrateur';
			$statut_sound[]='professeur';
			$statut_sound[]='scolarite';
			$statut_sound[]='cpe';
			$statut_sound[]='secours';
			$statut_sound[]='autre';
			$statut_sound[]='eleve';
			$statut_sound[]='responsable';
		}

		for($loop=0;$loop<count($statut_sound);$loop++) {
			$sql="SELECT DISTINCT login FROM utilisateurs WHERE statut='$statut_sound[$loop]';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				while($lig=mysql_fetch_object($res)) {
					if(!savePref($lig->login,'footer_sound',$_POST['footer_sound'])) {
						$nb_err_sound++;
					}
					else {
						$nb_reg_sound++;
					}
				}
			}
		}

		if($nb_err_sound>0) {
			$msg.="Error ($nb_err_sound) during recording of the sound alarm of end of session.<br />";
		}
		elseif($nb_reg_sound>0) {
			$msg.="Recording of the sound alarm of end of session done.<br />";
		}
	}

	// On traite si c'est demandé
$messageMenu = '';
if ($modifier_le_menu == "ok") {
	check_token();

	// On fait la modif demandée
	// pour l'administrateur général
	if ($_SESSION["statut"] == "administrateur"){
		$sql = "UPDATE setting SET value = '".$afficherMenu."' WHERE name = 'utiliserMenuBarre'";
	// ou pour les professeurs
	}elseif ($_SESSION["statut"] == "professeur") {
		// Pour le prof, on vérifie si ce réglage existe ou pas
		$query = mysql_query("SELECT value FROM preferences WHERE name = 'utiliserMenuBarre' AND login = '".$_SESSION["login"]."'");
		$verif = mysql_num_rows($query);
		if ($verif == 1) {
			// S'il existe, on le modifie
			$sql = "UPDATE preferences SET value = '".$afficherMenu."' WHERE name = 'utiliserMenuBarre' AND login = '".$_SESSION["login"]."'";
		}else {
			// Sinon, on le crée
			$sql = "INSERT INTO preferences SET login = '".$_SESSION["login"]."', name = 'utiliserMenuBarre', value = '".$afficherMenu."'";
		}
	}
		// Dans tous les cas, on envoie la requête et on renvoie le message adéquat.
		$requete = mysql_query($sql);
		if ($requete) {
			$messageMenu = "<p style=\"color: green\">The modification was recorded</p>";
		}else{
			$messageMenu = "<p style=\"color: red\">The modification failed, you should update your base before continuing</p>";
		}
} // fin du if ($modifier_le_menu...
// +++++++++++++++++++++ FIN -- MENU en barre horizontale -- FIN ++++++++++++++++++++

// ====== hauteur du header ======= //
	$message_header_prof = NULL;

if ($modifier_entete_prof == 'ok') {
	check_token();

	// On traite alors la demande
	$reglage = isset($_POST['header_bas']) ? $_POST['header_bas'] : 'n';

	if (saveSetting('impose_petit_entete_prof', $reglage)) {
		$message_header_prof = '<p style="color: green;">modification recorded </p>';
	}else{
		$message_header_prof = '<p style="color: red;">Impossible to record the modification</p>';
	}
}

if(($_SESSION['statut']=='professeur')&&(isset($_POST['ouverture_auto_WinDevoirsDeLaClasse']))) {
	check_token();

	if(($_POST['ouverture_auto_WinDevoirsDeLaClasse']=='y')||($_POST['ouverture_auto_WinDevoirsDeLaClasse']=='n')) {
		if(!savePref($_SESSION['login'],'ouverture_auto_WinDevoirsDeLaClasse',$_POST['ouverture_auto_WinDevoirsDeLaClasse'])) {
			$msg.="Error during recording of ouverture_auto_WinDevoirsDeLaClasse.<br />";
		}
		else {
			$msg.="Recording of ouverture_auto_WinDevoirsDeLaClasse.<br />";
		}
	}
}

if(isset($_POST['mod_discipline_travail_par_defaut'])) {
	check_token();

	if(!savePref($_SESSION['login'],'mod_discipline_travail_par_defaut',traitement_magic_quotes($_POST['mod_discipline_travail_par_defaut']))) {
		$msg.="Error during recording of mod_discipline_travail_par_defaut.<br />";
	}
	else {
		$msg.="Recording of mod_discipline_travail_par_defaut.<br />";
	}
}

// Tester les valeurs de $page
// Les valeurs autorisées sont (actuellement): accueil, add_modif_dev, add_modif_conteneur
//if(isset($page)){
if((isset($page))&&($_SESSION['statut']=="administrateur")){
	if(($page!="accueil_simpl")&&($page!="add_modif_dev")&&($page!="add_modif_conteneur")){
		$page=NULL;
		$enregistrer=NULL;
		$msg.="The selected page is not appropriate.";
	}
}

if(isset($enregistrer)) {
	check_token();
	for($i=0;$i<count($prof);$i++){
		//if($page=='accueil_simpl'){
		if(($page=='accueil_simpl')||($_SESSION['statut']=='professeur')){
			//$tab=array('accueil_simpl','accueil_ct','accueil_cn','accueil_bull','accueil_visu','accueil_trombino','accueil_liste_pdf','accueil_aff_txt_icon');
			$tab=array('accueil_simpl','accueil_infobulles','accueil_ct','accueil_cn','accueil_bull','accueil_visu','accueil_trombino','accueil_liste_pdf');

			for($j=0;$j<count($tab);$j++){
				unset($valeur);
				//$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;
				$tmp_champ=$tab[$j]."_".$i;
				$valeur=isset($_POST[$tmp_champ]) ? $_POST[$tmp_champ] : NULL;

				$sql="DELETE FROM preferences WHERE login='".$prof[$i]."' AND name='".$tab[$j]."'";
				//echo $sql."<br />\n";
				$res_suppr=mysql_query($sql);

				if(isset($valeur)){
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='$valeur'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Error during recording of $tab[$j] for $prof[$i]<br />\n";
					}
				}
				else{
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='n'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Error during recording of $tab[$j] for $prof[$i]<br />\n";
					}
				}
			}
		}

		if(($page=='add_modif_dev')||($_SESSION['statut']=='professeur')){
			//$tab=array('add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_boite');
			$tab=array('add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite');
			for($j=0;$j<count($tab);$j++){
				unset($valeur);
				//$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;
				$tmp_champ=$tab[$j]."_".$i;
				$valeur=isset($_POST[$tmp_champ]) ? $_POST[$tmp_champ] : NULL;

				$sql="DELETE FROM preferences WHERE login='".$prof[$i]."' AND name='".$tab[$j]."'";
				//echo $sql."<br />\n";
				$res_suppr=mysql_query($sql);

				if(isset($valeur)){
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='$valeur'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Error during recording of $tab[$j] for $prof[$i]<br />\n";
					}
				}
				else{
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='n'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Error during recording of $tab[$j] for $prof[$i]<br />\n";
					}
				}
			}
		}

		if(($page=='add_modif_conteneur')||($_SESSION['statut']=='professeur')){
			$tab=array('add_modif_conteneur_simpl','add_modif_conteneur_nom_court','add_modif_conteneur_nom_complet','add_modif_conteneur_description','add_modif_conteneur_coef','add_modif_conteneur_boite','add_modif_conteneur_aff_display_releve_notes','add_modif_conteneur_aff_display_bull');
			for($j=0;$j<count($tab);$j++){
				unset($valeur);
				//$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;
				$tmp_champ=$tab[$j]."_".$i;
				$valeur=isset($_POST[$tmp_champ]) ? $_POST[$tmp_champ] : NULL;

				$sql="DELETE FROM preferences WHERE login='".$prof[$i]."' AND name='".$tab[$j]."'";
				//echo $sql."<br />\n";
				$res_suppr=mysql_query($sql);

				if(isset($valeur)){
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='$valeur'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Error during recording of $tab[$j] for $prof[$i]<br />\n";
					}
				}
				else{
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='n'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Error during recording of $tab[$j] for $prof[$i]<br />\n";
					}
				}
			}
		}

		if ($_SESSION['statut']=='professeur') {
			$aff_quartiles_cn=isset($_POST['aff_quartiles_cn']) ? $_POST['aff_quartiles_cn'] : "n";

			$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name='aff_quartiles_cn';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='aff_quartiles_cn', value='$aff_quartiles_cn';";
				//echo $sql."<br />\n";
				if(!mysql_query($sql)){
					$msg.="Erreur lors de l'enregistrement de aff_quartiles_cn<br />\n";
					//$msg.="Erreur lors de l'enregistrement de l'affichage par défaut ou non des moyenne, médiane, quartiles,... sur les carnets de notes.<br />\n";
				}
			}
			else {
				$sql="UPDATE preferences SET value='$aff_quartiles_cn' WHERE login='".$_SESSION['login']."' AND name='aff_quartiles_cn';";
				//echo $sql."<br />\n";
				if(!mysql_query($sql)){
					$msg.="Error during recording of aff_quartiles_cn for ".$_SESSION['login']."<br />\n";
				}
			}


			$aff_photo_cn=isset($_POST['aff_photo_cn']) ? $_POST['aff_photo_cn'] : "n";

			$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name='aff_photo_cn';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='aff_photo_cn', value='$aff_photo_cn';";
				//echo $sql."<br />\n";
				if(!mysql_query($sql)){
					$msg.="Error during recording of aff_photo_cn<br />\n";
					//$msg.="Erreur lors de l'enregistrement de l'affichage par défaut ou non des moyenne, médiane, photo,... sur les carnets de notes.<br />\n";
				}
			}
			else {
				$sql="UPDATE preferences SET value='$aff_photo_cn' WHERE login='".$_SESSION['login']."' AND name='aff_photo_cn';";
				//echo $sql."<br />\n";
				if(!mysql_query($sql)){
					$msg.="Error during recording of aff_photo_cn pour ".$_SESSION['login']."<br />\n";
				}
			}


			$aff_photo_saisie_app=isset($_POST['aff_photo_saisie_app']) ? $_POST['aff_photo_saisie_app'] : "n";

			$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name='aff_photo_saisie_app';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='aff_photo_saisie_app', value='$aff_photo_saisie_app'";
				//echo $sql."<br />\n";
				if(!mysql_query($sql)){
					$msg.="Error during recording of aff_photo_saisie_app<br />\n";
					//$msg.="Erreur lors de l'enregistrement de l'affichage par défaut ou non des moyenne, médiane, quartiles,... sur les carnets de notes.<br />\n";
				}
			}
			else {
				$sql="UPDATE preferences SET value='$aff_photo_saisie_app' WHERE login='".$_SESSION['login']."' AND name='aff_photo_saisie_app';";
				//echo $sql."<br />\n";
				if(!mysql_query($sql)){
					$msg.="Error during recording of aff_photo_saisie_app pour ".$_SESSION['login']."<br />\n";
				}
			}

			$cn_avec_min_max=isset($_POST['cn_avec_min_max']) ? $_POST['cn_avec_min_max'] : "n";
			if(!savePref($_SESSION['login'],'cn_avec_min_max',$cn_avec_min_max)) {
				$msg.="Error during recording of 'cn_avec_min_max'<br />\n";
			}

			$cn_avec_mediane_q1_q3=isset($_POST['cn_avec_mediane_q1_q3']) ? $_POST['cn_avec_mediane_q1_q3'] : "n";
			if(!savePref($_SESSION['login'],'cn_avec_mediane_q1_q3',$cn_avec_mediane_q1_q3)) {
				$msg.="Error during recording of 'cn_avec_mediane_q1_q3'<br />\n";
			}

		}
	}

	if($msg==""){
		$msg="Successful recording.";
	}

	//unset($page);
}

// Style spécifique pour la page:
//$style_specifique="gestion/config_prefs";

// Couleur pour les cases dans lesquelles une modif est faite:
$couleur_modif='orange';

// Message d'alerte pour ne pas quitter par erreur sans valider:
$themessage="Modifications were carried out. Do you really want to leave without recording?";


//**************** EN-TETE *****************
$titre_page = "Configuration of the simplified interfaces";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

// Initialisation de la variable utilisée pour noter si des modifications ont été effectuées dans la page.
echo "<script type='text/javascript'>
	change='no';
</script>\n";

/*
- Choisir la page à afficher
- Choisir les profs? ou juste répéter la ligne de titre?
*/

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
echo "<div class='norme'><p class=bold>";
echo "<a href='";
if($_SESSION['statut']=='administrateur'){
	echo "index.php#config_prefs";
}
else{
	echo "../accueil.php";
}
echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>\n";

//if(!isset($page)){
if((!isset($page))&&($_SESSION['statut']=="administrateur")){
	echo "</div>\n";

	echo "<p>This page makes it possible to configure the simplified interface for:</p>\n";
	echo "<ul>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?page=accueil_simpl'>Simplified home page for ".$gepiSettings['denomination_professeurs']."</a></li>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?page=add_modif_dev'>Page of creation of evaluation</a></li>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?page=add_modif_conteneur'>Page of creation of ".strtolower(getSettingValue("gepi_denom_boite"))."</a></li>\n";
	echo "</ul>\n";

}
else{
	if($_SESSION['statut']=="administrateur"){
		echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choice of the page</a>";
	}
	echo "</div>\n";

	echo add_token_field();

	unset($prof);
	$prof=array();
	if($_SESSION['statut']=="administrateur"){

		//$sql="SELECT DISTINCT nom,prenom,login FROM utilisateurs WHERE statut='professeur' ORDER BY nom, prenom";
		$sql="SELECT DISTINCT nom,prenom,login FROM utilisateurs WHERE statut='professeur' AND etat='actif' ORDER BY nom, prenom";
		$res_prof=mysql_query($sql);
		if(mysql_num_rows($res_prof)==0){
			echo "<p>Aucun ".$gepiSettings['denomination_professeur']." n'est encore défini.<br />Start by creating the accounts ".$gepiSettings['denomination_professeurs'].".</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$i=0;
		while($lig_prof=mysql_fetch_object($res_prof)){
			$prof[$i]=array();
			$prof[$i]['login']=$lig_prof->login;
			$prof[$i]['nom']=$lig_prof->nom;
			$prof[$i]['prenom']=$lig_prof->prenom;
			$i++;
		}
	}
	else{
		$i=0;
		$prof[$i]['login']=$_SESSION['login'];
		$prof[$i]['nom']=$_SESSION['nom'];
		$prof[$i]['prenom']=$_SESSION['prenom'];
	}

	$nb_profs=count($prof);


	function cellule_checkbox($prof_login,$item,$num,$special){
		echo "<td align='center'";
		echo " id='td_".$item."_".$num."' ";
		//echo " style='text-align:center; ";
		$checked="";
		$coche="";
		$sql="SELECT * FROM preferences WHERE login='$prof_login' AND name='$item'";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$lig_test=mysql_fetch_object($test);
			if($lig_test->value=="y"){
				//echo " style='background-color: lightgreen;'";
				//echo "background-color: lightgreen;";
				echo " class='coche'";
				$checked=" checked";
				$coche="y";
			}
			else{
				//echo " style='background-color: lightgray;'";
				//echo "background-color: lightgray;";
				echo " class='decoche'";
				$coche="n";
			}
		}
		//echo "'";
		echo ">";
		echo "<input type='checkbox' name='$item"."_"."$num' id='$item"."_"."$num' value='y'";

		/*
		// Supprimé après avoir permis l'affichage des tableaux sur une seule page pour l'accès prof à ses propres paramétrages
		if($special=="y"){
			echo " onchange=\"modif_ligne($num)\"";
		}
		*/

		echo $checked;
		//echo " onchange='changement();'";
		echo " onchange=\"changement_et_couleur('$item"."_"."$num','";
		//if($special=="y"){
		if($special!=''){
			//echo "td_nomprenom_$num";
			//echo "td_nomprenom_".$num."_".$special;
			$chaine_td="td_nomprenom_".$num."_".$special;
			echo $chaine_td;
		}
		echo "');\"";
		echo " />";

		//if($special=="y"){
		if($special!=''){
			if($coche=="y"){
				echo "<script type='text/javascript'>
	//document.getElementById('td_nomprenom_'+$num).style.backgroundColor='lightgreen';
	document.getElementById('$chaine_td').style.backgroundColor='lightgreen';
</script>\n";
			}
			elseif($coche=="n"){
				echo "<script type='text/javascript'>
	//document.getElementById('td_nomprenom_'+$num).style.backgroundColor='lightgray';
	document.getElementById('$chaine_td').style.backgroundColor='lightgray';
</script>\n";
			}
		}

		echo "</td>\n";
	} // FIN function cellule_checkbox


/*
	echo "<style type='text/css'>
	table.contenu {
		border: 1px solid black;
		border-collapse: collapse;
	}

	.contenu th {
		font-weight:bold;
		text-align: center;
		background-color: white;
		border: 1px solid black;
	}

	.contenu td {
		vertical-align: middle;
		text-align: center;
		border: 1px solid black;
	}

	.contenu tr.entete {
		background-color: white;
	}

	.contenu .coche {
		background-color: lightgreen;
	}

	.contenu .decoche {
		background-color: lightgray;
	}
</style>\n";
*/

	echo "<p align='center'><input type=\"submit\" name='enregistrer' value=\"Valider\" style=\"font-variant: small-caps;\" /></p>\n";

	//if($page=="accueil_simpl"){
	if(($page=="accueil_simpl")||($_SESSION['statut']=='professeur')){
		echo "<p>Parameter setting of the simplified < b>home</b > page for ".$gepiSettings['denomination_professeurs'].".</p>\n";

		//$tabchamps=array('accueil_simpl','accueil_ct','accueil_trombino','accueil_cn','accueil_bull','accueil_visu','accueil_liste_pdf');
		//accueil_aff_txt_icon
		$tabchamps=array('accueil_simpl','accueil_infobulles','accueil_ct','accueil_trombino','accueil_cn','accueil_bull','accueil_visu','accueil_liste_pdf');

		//echo "<table border='1'>\n";
		echo "<table class='contenu' border='1' summary='Preferences professors'>\n";

		// 1ère ligne
		//$lignes_entete="<tr style='background-color: white;'>\n";
		$lignes_entete="<tr class='entete'>\n";
		if($_SESSION['statut']!='professeur'){
			$lignes_entete.="<th rowspan='3'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		else{
			$lignes_entete.="<th rowspan='2'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		$lignes_entete.="<th rowspan='2'>Use the simplified interface</th>\n";
		$lignes_entete.="<th rowspan='2'>Display infobulles</th>\n";
		$lignes_entete.="<th colspan='6'>Display links for</th>\n";
		if($_SESSION['statut']!='professeur') {$lignes_entete.="<th rowspan='3'>Check all / uncheck</th>\n";}
		$lignes_entete.="</tr>\n";

		// 2ème ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete.="<tr class='entete'>\n";
		$lignes_entete.="<th>the Textbook</th>\n";
		$lignes_entete.="<th>Trombinoscope</th>\n";
		$lignes_entete.="<th>the Report card</th>\n";
		$lignes_entete.="<th>notes and appreciations of the Report cards</th>\n";
		$lignes_entete.="<th>the Visualization of the graphs and simplified bulletins </th>\n";
		$lignes_entete.="<th>Pdf lists of the students</th>\n";
		$lignes_entete.="</tr>\n";

		// 3ème ligne
		if($_SESSION['statut']!='professeur'){
			//$lignes_entete.="<tr style='background-color: white;'>\n";
			$lignes_entete.="<tr class='entete'>\n";
			for($i=0;$i<count($tabchamps);$i++){
				$lignes_entete.="<th>";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a>/\n";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>\n";
				$lignes_entete.="</th>\n";
			}
			$lignes_entete.="</tr>\n";
		}

		//$i=0;
		//while($lig_prof=mysql_fetch_object($res_prof)){
		for($i=0;$i<count($prof);$i++){
			if($i-ceil($i/10)*10==0){
				echo $lignes_entete;
			}

			echo "<tr>\n";

			//echo "<td id='td_nomprenom_".$i."'>";
			echo "<td id='td_nomprenom_".$i."_accueil_simpl'>";
			//echo strtoupper($lig_prof->nom)." ".ucfirst(strtolower($lig_prof->prenom));
			echo strtoupper($prof[$i]['nom'])." ".ucfirst(strtolower($prof[$i]['prenom']));
			//echo "<input type='hidden' name='prof[$i]' value='$lig_prof->login' />";
			echo "<input type='hidden' name='prof[$i]' value='".$prof[$i]['login']."' />";
			echo "</td>\n";

			/*
			cellule_checkbox($prof[$i]['login'],'accueil_simpl',$i,'y');

			cellule_checkbox($prof[$i]['login'],'accueil_ct',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_trombino',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_cn',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_bull',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_visu',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_liste_pdf',$i,'');
			*/

			$j=0;
			//cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'y');
			cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'accueil_simpl');
			//for($j=0;$j<count($tabchamps);$j++){
			for($j=1;$j<count($tabchamps);$j++){
				cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'');
			}

			if($_SESSION['statut']!='professeur') {
				echo "<th>";
				echo "<a href='javascript:coche_ligne($i,true)'><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a>/\n";
				echo "<a href='javascript:coche_ligne($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>\n";
				echo "</th>\n";
			}

			echo "</tr>\n";
			//$i++;
		}

		echo "</table>\n";
	}







	if($_SESSION['statut']=='professeur') {
		echo "<p><br /></p>\n";
		echo "<p><b>Parameters of the report card&nbsp;:</b></p>\n";

		$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name='aff_quartiles_cn'";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$aff_quartiles_cn="n";
		}
		else {
			$lig_test=mysql_fetch_object($test);
			$aff_quartiles_cn=$lig_test->value;
		}
		echo "<p>\n";
		echo "<input type='checkbox' name='aff_quartiles_cn' id='aff_quartiles_cn' value='y' ";
		echo "onchange=\"checkbox_change('aff_quartiles_cn');changement()\" ";
		if($aff_quartiles_cn=='y') {echo 'checked';}
		echo "/><label for='aff_quartiles_cn' id='texte_aff_quartiles_cn'> Display by defect the infobulle containing the average, median, quartiles, min, max on the report cards.</label>\n";
		echo "</p>\n";

		$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name='aff_photo_cn'";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$aff_photo_cn="n";
		}
		else {
			$lig_test=mysql_fetch_object($test);
			$aff_photo_cn=$lig_test->value;
		}
		echo "<p>\n";
		echo "<input type='checkbox' name='aff_photo_cn' id='aff_photo_cn' value='y' ";
		echo "onchange=\"checkbox_change('aff_photo_cn');changement()\" ";
		if($aff_photo_cn=='y') {echo 'checked';}
		echo "/><label for='aff_photo_cn' id='texte_aff_photo_cn'> Display by defect the photograph of the students on the report cards.</label>\n";
		echo "</p>\n";

		echo "<p>\n";
		$cn_avec_min_max=getPref($_SESSION['login'], 'cn_avec_min_max', 'y');
		echo "<input type='checkbox' name='cn_avec_min_max' id='cn_avec_min_max' value='y' ";
		echo "onchange=\"checkbox_change('cn_avec_min_max');changement()\" ";
		if($cn_avec_min_max=='y') {echo 'checked';}
		echo "/><label for='cn_avec_min_max' id='texte_cn_avec_min_max'> Display for each column of notes values minimal and maximum.</label>\n";
		echo "</p>\n";

		echo "<p>\n";
		$cn_avec_mediane_q1_q3=getPref($_SESSION['login'], 'cn_avec_mediane_q1_q3', 'y');
		echo "<input type='checkbox' name='cn_avec_mediane_q1_q3' id='cn_avec_mediane_q1_q3' value='y' ";
		echo "onchange=\"checkbox_change('cn_avec_mediane_q1_q3');changement()\" ";
		if($cn_avec_mediane_q1_q3=='y') {echo 'checked';}
		echo "/><label for='cn_avec_mediane_q1_q3' id='texte_cn_avec_mediane_q1_q3'> Display for each column of notes the median value, 1st and 3rd quartiles.</label>\n";
		echo "</p>\n";
	}


	if(($page=="add_modif_dev")||($_SESSION['statut']=='professeur')){
		echo "<p>Parameter setting of the page of <b>creation of evaluation</b> for ".$gepiSettings['denomination_professeurs']."</p>\n";

		if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
			//$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_boite');
			$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite');
		} else {
			//$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_date','add_modif_dev_boite');	
			$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite');	
		}
		//echo "<table border='1'>\n";
		echo "<table class='contenu' border='1' summary='Preferences professors'>\n";

		// 1ère ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete="<tr class='entete'>\n";
		if($_SESSION['statut']!='professeur'){
			$lignes_entete.="<th rowspan='3'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		else{
			$lignes_entete.="<th rowspan='2'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		$lignes_entete.="<th rowspan='2'>Utiliser l'interface simplifiée</th>\n";
		if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
			$lignes_entete.="<th colspan='8'>Display fields</th>\n";
		} else {
			$lignes_entete.="<th colspan='7'>Display fields</th>\n";
		}
		if($_SESSION['statut']!='professeur') {$lignes_entete.="<th rowspan='3'>Check all / uncheck</th>\n";}
		$lignes_entete.="</tr>\n";

		// 2ème ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete.="<tr class='entete'>\n";
		$lignes_entete.="<th>Short name</th>\n";
		$lignes_entete.="<th>Complete name</th>\n";
		$lignes_entete.="<th>Description</th>\n";
		$lignes_entete.="<th>Coefficient</th>\n";
		if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
			$lignes_entete.="<th>Note other that on the reference</th>\n";
		}
		$lignes_entete.="<th>Date</th>\n";
		$lignes_entete.="<th>Date ele/resp</th>\n";
		$lignes_entete.="<th>".ucfirst(strtolower(getSettingValue("gepi_denom_boite")))."</th>\n";
		$lignes_entete.="</tr>\n";

		// 3ème ligne
		if($_SESSION['statut']!='professeur'){
			//$lignes_entete.="<tr style='background-color: white;'>\n";
			$lignes_entete.="<tr class='entete'>\n";
			for($i=0;$i<count($tabchamps);$i++){
				$lignes_entete.="<th>";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a>/\n";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all /></a>\n";
				$lignes_entete.="</th>\n";
			}
			$lignes_entete.="</tr>\n";
		}

		//$i=0;
		//while($lig_prof=mysql_fetch_object($res_prof)){
		for($i=0;$i<count($prof);$i++){
			if($i-ceil($i/10)*10==0){
				echo $lignes_entete;
			}

			echo "<tr>\n";

			//echo "<td>";
			//echo "<td id='td_nomprenom_".$i."'>";
			echo "<td id='td_nomprenom_".$i."_add_modif_dev'>";
			//echo strtoupper($lig_prof->nom)." ".ucfirst(strtolower($lig_prof->prenom));
			echo strtoupper($prof[$i]['nom'])." ".ucfirst(strtolower($prof[$i]['prenom']));
			//echo "<input type='hidden' name='prof[$i]' value='$lig_prof->login' />";
			echo "<input type='hidden' name='prof[$i]' value='".$prof[$i]['login']."' />";
			echo "</td>\n";

			$j=0;
			//cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'y');
			cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'add_modif_dev');
			//for($j=0;$j<count($tabchamps);$j++){
			for($j=1;$j<count($tabchamps);$j++){
				cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'');
			}

			if($_SESSION['statut']!='professeur') {
				echo "<th>";
				echo "<a href='javascript:coche_ligne($i,true)'><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a>/\n";
				echo "<a href='javascript:coche_ligne($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>\n";
				echo "</th>\n";
			}

			echo "</tr>\n";
			//$i++;
		}

		echo "</table>\n";
	}


	if(($page=="add_modif_conteneur")||($_SESSION['statut']=='professeur')){
		echo "<p>Parameter setting of the page of <b>creation of ".ucfirst(strtolower(getSettingValue("gepi_denom_boite")))."</b> for ".$gepiSettings['denomination_professeurs']."</p>\n";

		$tabchamps=array('add_modif_conteneur_simpl','add_modif_conteneur_nom_court','add_modif_conteneur_nom_complet','add_modif_conteneur_description','add_modif_conteneur_coef','add_modif_conteneur_boite','add_modif_conteneur_aff_display_releve_notes','add_modif_conteneur_aff_display_bull');

		//echo "<table border='1'>\n";
		echo "<table class='contenu' border='1' summary='Preferences professors'>\n";

		// 1ère ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete="<tr class='entete'>\n";
		if($_SESSION['statut']!='professeur'){
			$lignes_entete.="<th rowspan='3'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		else{
			$lignes_entete.="<th rowspan='2'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		$lignes_entete.="<th rowspan='2'>Use the simplified interface</th>\n";
		$lignes_entete.="<th colspan='7'>Display fields</th>\n";
		if($_SESSION['statut']!='professeur') {$lignes_entete.="<th rowspan='3'>Check/ uncheck all </th>\n";}
		$lignes_entete.="</tr>\n";

		// 2ème ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete.="<tr class='entete'>\n";
		$lignes_entete.="<th>Short name</th>\n";
		$lignes_entete.="<th>Complete name</th>\n";
		$lignes_entete.="<th>Description</th>\n";
		$lignes_entete.="<th>Coefficient</th>\n";
		$lignes_entete.="<th>".ucfirst(strtolower(getSettingValue("gepi_denom_boite")))."</th>\n";
		$lignes_entete.="<th>Display on the report booklet</th>\n";
		$lignes_entete.="<th>Display on the bulletin</th>\n";
		$lignes_entete.="</tr>\n";

		// 3ème ligne
		if($_SESSION['statut']!='professeur'){
			//$lignes_entete.="<tr style='background-color: white;'>\n";
			$lignes_entete.="<tr class='entete'>\n";
			for($i=0;$i<count($tabchamps);$i++){
				$lignes_entete.="<th>";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a>/\n";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>\n";
				$lignes_entete.="</th>\n";
			}
			$lignes_entete.="</tr>\n";
		}

		//$i=0;
		//while($lig_prof=mysql_fetch_object($res_prof)){
		for($i=0;$i<count($prof);$i++){
			if($i-ceil($i/10)*10==0){
				echo $lignes_entete;
			}

			echo "<tr>\n";

			//echo "<td>";
			//echo "<td id='td_nomprenom_".$i."'>";
			echo "<td id='td_nomprenom_".$i."_add_modif_conteneur'>";
			//echo strtoupper($lig_prof->nom)." ".ucfirst(strtolower($lig_prof->prenom));
			echo strtoupper($prof[$i]['nom'])." ".ucfirst(strtolower($prof[$i]['prenom']));
			//echo "<input type='hidden' name='prof[$i]' value='$lig_prof->login' />";
			echo "<input type='hidden' name='prof[$i]' value='".$prof[$i]['login']."' />";
			echo "</td>\n";

			$j=0;
			//cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'y');
			cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'add_modif_conteneur');
			//for($j=0;$j<count($tabchamps);$j++){
			for($j=1;$j<count($tabchamps);$j++){
				cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'');
			}

			if($_SESSION['statut']!='professeur') {
				echo "<th>";
				echo "<a href='javascript:coche_ligne($i,true)'><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a>/\n";
				echo "<a href='javascript:coche_ligne($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>\n";
				echo "</th>\n";
			}
			echo "</tr>\n";
			//$i++;
		}

		echo "</table>\n";
	}





	if($_SESSION['statut']=='professeur') {
		echo "<p><br /></p>\n";
		echo "<p><b>Parameters of typing of the appreciations&nbsp;:</b></p>\n";

		$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name='aff_photo_saisie_app'";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$aff_photo_saisie_app="n";
		}
		else {
			$lig_test=mysql_fetch_object($test);
			$aff_photo_saisie_app=$lig_test->value;
		}

		echo "<p>\n";
		echo "<input type='checkbox' name='aff_photo_saisie_app' id='aff_photo_saisie_app' value='y' ";
		echo "onchange=\"checkbox_change('aff_photo_saisie_app');changement()\" ";
		if($aff_photo_saisie_app=='y') {echo 'checked';}
		echo "/><label for='aff_photo_saisie_app' id='texte_aff_photo_saisie_app'> Display by defect photographs of the students during typing of the appreciations on the bulletins.</label>\n";
		echo "</p>\n";
	}





	// La page n'est considérée que pour l'admin pour réduire la longueur de la liste
	if($_SESSION['statut']=='administrateur'){
		echo "<input type=\"hidden\" name='page' value=\"$page\" />\n";
	}

	echo "<p align='center'><input type=\"submit\" name='enregistrer' value=\"Validate\" style=\"font-variant: small-caps;\" /></p>\n";

	echo "<script type='text/javascript' language='javascript'>

	function modif_coche(item,statut){
		// statut: true ou false
		for(k=0;k<$nb_profs;k++){
			if(document.getElementById(item+'_'+k)){
				document.getElementById(item+'_'+k).checked=statut;

				document.getElementById('td_'+item+'_'+k).style.backgroundColor='$couleur_modif';
			}
		}
		changement();
	}

	tab_item=new Array('accueil_simpl','accueil_infobulles','accueil_ct','accueil_cn','accueil_bull','accueil_visu','accueil_trombino','accueil_liste_pdf','add_modif_conteneur_simpl','add_modif_conteneur_nom_court','add_modif_conteneur_nom_complet','add_modif_conteneur_description','add_modif_conteneur_coef','add_modif_conteneur_boite','add_modif_conteneur_aff_display_releve_notes','add_modif_conteneur_aff_display_bull','add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite');
	function coche_ligne(ligne,statut){
		// statut: true ou false
		for(k=0;k<tab_item.length;k++){
			if(document.getElementById(tab_item[k]+'_'+ligne)){
				document.getElementById(tab_item[k]+'_'+ligne).checked=statut;

				document.getElementById('td_'+tab_item[k]+'_'+ligne).style.backgroundColor='$couleur_modif';
			}
		}
		changement();
	}

	function changement_et_couleur(id,special){
		if(document.getElementById(id)){
			document.getElementById('td_'+id).style.backgroundColor='$couleur_modif';
		}

		if(special!=''){
			document.getElementById(special).style.backgroundColor='$couleur_modif';
		}

		changement();
	}
";

	/*
	echo "
	function modif_ligne(num){";

	$liste_champs="";
	for($k=0;$k<count($tabchamps);$k++){
		if($k>0){$liste_champs.=", ";}
		$liste_champs.="'$tabchamps[$k]'";
	}

		echo "
		tabchamps=Array($liste_champs);
		for(k=0;k<tabchamps.length;k++){
			item=tabchamps[k];
			if(document.getElementById('td_'+item+'_'+num)){
				document.getElementById('td_'+item+'_'+num).style.backgroundColor='orange';
			}
		}
		changement();
	}
";
	*/
	echo "</script>\n";


	echo "<p><i>Remarks:</i></p>\n";
	echo "<ul>\n";
	echo "<li>The taking into account of the selected fields is conditioned by the
fact of having notched or not the column 'Use the simplified interface' for the user considered.</li>\n";
	echo "<li>Fields not proposed in the simplified interfaces remain accessible to the users while clicking on the links 'complete Interface' proposed in the pages of simplified interfaces .</li>\n";
	echo "</ul>\n";
	//}
}

echo "</form>\n";

if ((getSettingValue('active_cahiers_texte')!='n')&&($_SESSION["statut"] == "professeur")) {
	$ouverture_auto_WinDevoirsDeLaClasse=getPref($_SESSION['login'], 'ouverture_auto_WinDevoirsDeLaClasse', 'y');
	echo "<form name='form_cdt_pref' method='post' action='./config_prefs.php'>\n";
	echo add_token_field();
	echo "<fieldset style='border: 1px solid grey;'>\n";
	echo "<legend style='border: 1px solid grey;'>Textbook 2</legend>\n";
	echo "<p>During typing of notices of Work to be made in CDT2,<br />\n";
	echo "<input type='radio' name='ouverture_auto_WinDevoirsDeLaClasse' id='ouverture_auto_WinDevoirsDeLaClasse_y' value='y' ";
	echo "onchange=\"checkbox_change('ouverture_auto_WinDevoirsDeLaClasse_y');checkbox_change('ouverture_auto_WinDevoirsDeLaClasse_n');changement()\" ";
	if($ouverture_auto_WinDevoirsDeLaClasse=='y') {echo " checked";}
	echo "/><label for='ouverture_auto_WinDevoirsDeLaClasse_y' id='texte_ouverture_auto_WinDevoirsDeLaClasse_y'> automatically open the window listing the work given by the other professors,</label><br />\n";
	echo "<input type='radio' name='ouverture_auto_WinDevoirsDeLaClasse' id='ouverture_auto_WinDevoirsDeLaClasse_n' value='n' ";
	echo "onchange=\"checkbox_change('ouverture_auto_WinDevoirsDeLaClasse_y');checkbox_change('ouverture_auto_WinDevoirsDeLaClasse_n');changement()\" ";
	if($ouverture_auto_WinDevoirsDeLaClasse!='y') {echo " checked";}
	echo "/><label for='ouverture_auto_WinDevoirsDeLaClasse_n' id='texte_ouverture_auto_WinDevoirsDeLaClasse_n'> not automatically open the window  listing the work given by the
other professors.</label><br />\n";

	echo "<input type='submit' name='Valider' value='Validate' />\n";

	echo "</p>\n";
	echo "</fieldset>\n";
	echo "</form>\n";

	echo "<br />\n";
}

if (getSettingValue('active_mod_discipline')!='n') {
	$mod_discipline_travail_par_defaut=getPref($_SESSION['login'], 'mod_discipline_travail_par_defaut', 'Travail : ');
	echo "<form name='form_cdt_pref' method='post' action='./config_prefs.php'>\n";
	echo add_token_field();
	echo "<fieldset style='border: 1px solid grey;'>\n";
	echo "<legend style='border: 1px solid grey;'>Module Discipline and sanctions</legend>\n";
	echo "<p>During typing of work to be made, the text by defect suggested will be&nbsp;: ,<br />\n";
	echo "<input type='text' name='mod_discipline_travail_par_defaut' value='$mod_discipline_travail_par_defaut' size='30' /><br />\n";
	echo "<input type='submit' name='Valider' value='Validate' />\n";
	echo "</p>\n";
	echo "</fieldset>\n";
	echo "</form>\n";

	echo "<br />\n";
}

	// On ajoute le réglage pour le menu en barre horizontale
	$aff = "non";
if ($_SESSION["statut"] == "administrateur") {
	$aff = "oui";
}elseif($_SESSION["statut"] == "professeur" AND getSettingValue("utiliserMenuBarre") == "yes") {
	$aff = "oui";
}else {
	$aff = "non";
}
// On affiche si c'est autorisé
if ($aff == "oui") {
	echo '
		<form name="change_menu" method="post" action="./config_prefs.php">
';

	echo add_token_field();

	echo '
	<fieldset id="afficherBarreMenu" style="border: 1px solid grey;">
		<legend style="border: 1px solid grey;">Manage the horizontal bar of the menu</legend>
			<input type="hidden" name="modifier_le_menu" value="ok" />
		<p>
			<label for="visibleMenu" id="texte_visibleMenu">Make visible the horizontal bar of menu under the heading.</label>
			<input type="radio" id="visibleMenu" name="afficher_menu" value="yes"'.eval_checked("utiliserMenuBarre", "yes", $_SESSION["statut"], $_SESSION["login"]).' onclick="document.change_menu.submit();" />
		</p>
		<p>
			<label for="invisibleMenu" id="texte_invisibleMenu">Not use the horizontal bar of menu.</label>
			<input type="radio" id="invisibleMenu" name="afficher_menu" value="no"'.eval_checked("utiliserMenuBarre", "no", $_SESSION["statut"], $_SESSION["login"]).' onclick="document.change_menu.submit();" />
		</p>
	</fieldset>
		</form>
		'.$messageMenu
		;
} // fin du if ($aff == "oui")

echo '<br />' . "\n";

if ($_SESSION["statut"] == 'administrateur') {
	// On propose de pouvoir obliger tous les professeurs à avoir un header court
	echo '
		<form name="change_header_prof" method="post" action="config_prefs.php">
';

	echo add_token_field();

	echo '

			<fieldset style="border: 1px solid grey;">
				<legend style="border: 1px solid grey;">Manage the height of the heading for the professors</legend>
				<input type="hidden" name="modifier_entete_prof" value="ok" />
				<p>
					<label for="headerBas" id="texte_headerBas">Impose a low heading</label>
					<input type="radio" id="headerBas" name="header_bas" value="y"'.eval_checked("impose_petit_entete_prof", "y", "administrateur", $_SESSION["login"]).' onclick="document.change_header_prof.submit();" />
				</p>
				<p>
					<label for="headerNormal" id="texte_headerNormal">Impose nothing </label>
					<input type="radio" id="headerNormal" name="header_bas" value="n"'.eval_checked("impose_petit_entete_prof", "n", "administrateur", $_SESSION["login"]).' onclick="document.change_header_prof.submit();" />
				</p>
				' . $message_header_prof . '
			</fieldset>
		</form>';
}

echo js_checkbox_change_style('checkbox_change', 'texte_', 'y');

//============================================
// Choix de l'alerte sonore de fin de session
/*
if (!isset($niveau_arbo)) {$niveau_arbo = 1;}

if ($niveau_arbo == "0") {
	$chemin_sound="./sounds/";
} elseif ($niveau_arbo == "1") {
	$chemin_sound="../sounds/";
} elseif ($niveau_arbo == "2") {
	$chemin_sound="../../sounds/";
} elseif ($niveau_arbo == "3") {
	$chemin_sound="../../../sounds/";
}
$tab_sound=get_tab_file($chemin_sound);
*/
if(count($tab_sound)>=0) {
	$footer_sound_actuel=getPref($_SESSION['login'],'footer_sound',"");

	echo "<br />\n";
	echo "<form name='change_footer_sound' method='post' action='".$_SERVER['PHP_SELF']."'>\n";
	echo add_token_field();

	echo "<fieldset style='border: 1px solid grey;'>
	<legend style='border: 1px solid grey;'>Choice of the sound alarm of end of session</legend>
	<p><select name='footer_sound' id='footer_sound' onchange='test_play_footer_sound()'>\n";
	echo "	<option value=''";
	if($footer_sound_actuel=='') {echo " selected='true'";}
	echo ">Aucun son</option>\n";
	for($i=0;$i<count($tab_sound);$i++) {
		echo "	<option value='".$tab_sound[$i]."'";
		if($tab_sound[$i]==$footer_sound_actuel) {echo " selected='true'";}
		echo ">".$tab_sound[$i]."</option>\n";
	}
	echo "	</select>
	<a href='javascript:test_play_footer_sound()'><img src='../images/icons/sound.png' width='16' height='16' alt='Listen the selected sound' title='Listen the selected sound' /></a>
	</p>\n";

	if($_SESSION['statut']=='administrateur') {
		echo "<p><input type='radio' name='footer_sound_pour_qui' id='footer_sound_pour_qui_perso' value='perso' onchange='maj_style_label_checkbox()' checked /><label for='footer_sound_pour_qui_perso' id='texte_footer_sound_pour_qui_perso'> Only apply this choice on my account</label><br />\n";
		echo "<input type='radio' name='footer_sound_pour_qui' id='footer_sound_pour_qui_tous_profs' value='tous_profs' onchange='maj_style_label_checkbox()' /><label for='footer_sound_pour_qui_tous_profs' id='texte_footer_sound_pour_qui_tous_profs'> Apply this choice on all the accounts professors</label><br />\n";
		echo "<input type='radio' name='footer_sound_pour_qui' id='footer_sound_pour_qui_tous_personnels' value='tous_personnels' onchange='maj_style_label_checkbox()' /><label for='footer_sound_pour_qui_tous_personnels' id='texte_footer_sound_pour_qui_tous_personnels'> Apply this choice on all the accounts of personnel</label><br />\n";
		echo "<input type='radio' name='footer_sound_pour_qui' id='footer_sound_pour_qui_tous' value='tous' onchange='maj_style_label_checkbox()' /><label for='footer_sound_pour_qui_tous' id='texte_footer_sound_pour_qui_tous'> Apply this choice on all the accounts without reference to statute</label></p>\n";
	}
	else {
		echo "<input type='hidden' name='footer_sound_pour_qui' id='footer_sound_pour_qui_perso' value='perso' />\n";
	}

	echo "
	<p align='center'><input type='submit' name='enregistrer' value='Save' style='font-variant: small-caps;' /></p>
</fieldset>
</form>\n";

	for($i=0;$i<count($tab_sound);$i++) {
		echo "<audio id='footer_sound_$i' preload='auto' autobuffer>
  <source src='$chemin_sound".$tab_sound[$i]."' />
</audio>\n";
	}

	echo "<script type='text/javascript'>
function test_play_footer_sound() {
	n=document.getElementById('footer_sound').selectedIndex;
	if(n>0) {
		n--;
		if(document.getElementById('footer_sound_'+n)) {
			document.getElementById('footer_sound_'+n).play();
		}
	}
}

var champs_checkbox=new Array('aff_quartiles_cn', 'aff_photo_cn', 'aff_photo_saisie_app', 'cn_avec_min_max', 'cn_avec_mediane_q1_q3', 'visibleMenu', 'invisibleMenu', 'headerBas', 'headerNormal', 'footer_sound_pour_qui_perso', 'footer_sound_pour_qui_tous_profs', 'footer_sound_pour_qui_tous_personnels', 'footer_sound_pour_qui_tous', 'ouverture_auto_WinDevoirsDeLaClasse_y', 'ouverture_auto_WinDevoirsDeLaClasse_n');
function maj_style_label_checkbox() {
	for(i=0;i<champs_checkbox.length;i++) {
		checkbox_change(champs_checkbox[i]);
	}
}
maj_style_label_checkbox();
</script>
";
}

//============================================

echo "<br />\n";
require("../lib/footer.inc.php");
?>
