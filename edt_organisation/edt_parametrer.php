<?php
/**
 *
 * @version $Id: edt_parametrer.php 6918 2011-05-14 09:01:04Z adminpaulbert $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// Fichier utilisé par l'administrateur pour paramétrer l'EdT de Gepi
require_once("./choix_langue.php");

$titre_page = TITLE_EDT_PARAMETRER;
$affiche_connexion = 'yes';
$niveau_arbo = 1;

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

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}
// CSS et js particulier à l'EdT
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_edt";
$ua = getenv("HTTP_USER_AGENT");
if (strstr($ua, "MSIE 6.0")) {
	$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_ie6_param";
}
else if (strstr($ua, "MSIE 7")) {
	$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_ie7_param";
}
$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_param";

//=========Utilisation de prototype et des js de base ===========
$utilisation_prototype = "";
$utilisation_jsbase = "";
//=========Fin des Prototype et autres js =======================

// Initialiser les variables
$edt_aff_matiere=isset($_POST['edt_aff_matiere']) ? $_POST['edt_aff_matiere'] : NULL;
$edt_aff_creneaux=isset($_POST['edt_aff_creneaux']) ? $_POST['edt_aff_creneaux'] : NULL;
$edt_aff_couleur=isset($_POST['edt_aff_couleur']) ? $_POST['edt_aff_couleur'] : NULL;
$edt_aff_couleur_prof=isset($_POST['edt_aff_couleur_prof']) ? $_POST['edt_aff_couleur_prof'] : NULL;
$edt_aff_couleur_salle=isset($_POST['edt_aff_couleur_salle']) ? $_POST['edt_aff_couleur_salle'] : NULL;
$edt_aff_salle=isset($_POST['edt_aff_salle']) ? $_POST['edt_aff_salle'] : NULL;
$aff_cherche_salle = isset($_POST["aff_cherche_salle"]) ? $_POST["aff_cherche_salle"] : NULL;
$parametrer=isset($_POST['parametrer']) ? $_POST['parametrer'] : NULL;
$parametrer_ok=isset($_POST['parametrer1']) ? $_POST['parametrer1'] : NULL;
$param_menu_edt = isset($_POST["param_menu_edt"]) ? $_POST["param_menu_edt"] : NULL;
	$req = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_couleur_salle'");
	$test = mysql_fetch_array($req);
	if (!$test) {
		mysql_query("INSERT INTO edt_setting SET valeur = 'nb', 
												 reglage = 'edt_aff_couleur_salle'");		
		updateOnline("INSERT INTO edt_setting SET valeur = 'nb', 
												 reglage = 'edt_aff_couleur_salle'");												 
	}
	$req = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_couleur_prof'");
	$test = mysql_fetch_array($req);
	if (!$test) {
		mysql_query("INSERT INTO edt_setting SET 	valeur = 'nb' ,
													reglage = 'edt_aff_couleur_prof'");	
updateOnline("INSERT INTO edt_setting SET 	valeur = 'nb' ,
													reglage = 'edt_aff_couleur_prof'");														
	}
// Récupérer les paramètres tels qu'ils sont déjà définis
if (isset($parametrer_ok)) {
	$aff_message = "";
	// Le réglage de l'affichage des matières
	$req_reg_mat = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_matiere'");
	$tab_reg_mat = mysql_fetch_array($req_reg_mat);

	if ($edt_aff_matiere === $tab_reg_mat['valeur']) {
		$aff_message .= "<p class=\"accept\">Aucune modification de l'affichage des matières</p>\n";
	}
	else {
		$modif_aff_mat = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_matiere' WHERE reglage = 'edt_aff_matiere'");
		updateOnline("UPDATE edt_setting SET valeur = '$edt_aff_matiere' WHERE reglage = 'edt_aff_matiere'");
		$aff_message .= "<p class=\"refus\"> Modification de l'affichage des matières enregistrée</p>\n";
	}

	// Le réglage de l'affichage du type d'heure
	$req_reg_cre = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_creneaux'");
	$tab_reg_cre = mysql_fetch_array($req_reg_cre);

	if ($edt_aff_creneaux === $tab_reg_cre['valeur']) {
		$aff_message .= "<p class=\"accept\">No modification of the display of the crenels</p>\n";
	}
	else {
		$modif_aff_cre = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_creneaux' WHERE reglage = 'edt_aff_creneaux'");
		$aff_message .= "<p class=\"refus\"> Modification of the display of the crenels recorded</p>\n";
	}

	// Le réglage de l'affichage des couleurs
	$req_reg_coul = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_couleur'");
	$tab_reg_coul = mysql_fetch_array($req_reg_coul);

	if ($edt_aff_couleur === $tab_reg_coul['valeur']) {
		$aff_message .= "<p class=\"accept\">No modification of the colors</p>\n";
	}
	else {
		$modif_aff_coul = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_couleur' WHERE reglage = 'edt_aff_couleur'");
		$aff_message .= "<p class=\"refus\"> Modification of the display of the colors recorded</p>\n";
	updateOnline("UPDATE edt_setting SET valeur = '$edt_aff_couleur' WHERE reglage = 'edt_aff_couleur'");
	}
	// Le réglage de l'affichage des couleurs profs
	$req_reg_coul = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_couleur_prof'");
	$tab_reg_coul = mysql_fetch_array($req_reg_coul);

	if ($edt_aff_couleur_prof === $tab_reg_coul['valeur']) {
		$aff_message .= "<p class=\"accept\">No modification of the colors</p>\n";
	}
	else {
		$modif_aff_coul = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_couleur_prof' WHERE reglage = 'edt_aff_couleur_prof'");
		updateOnline("UPDATE edt_setting SET valeur = '$edt_aff_couleur_prof' WHERE reglage = 'edt_aff_couleur_prof'");
		$aff_message .= "<p class=\"refus\"> Modification of the display of the colors recorded (prof)</p>\n";
	}
	
	// Le réglage de l'affichage des couleurs salles
	$req_reg_coul = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_couleur_salle'");
	$tab_reg_coul = mysql_fetch_array($req_reg_coul);

	if ($edt_aff_couleur_salle === $tab_reg_coul['valeur']) {
		$aff_message .= "<p class=\"accept\">No modification of the colors</p>\n";
	}
	else {
		$modif_aff_coul = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_couleur_salle' WHERE reglage = 'edt_aff_couleur_salle'");
		updateOnline("UPDATE edt_setting SET valeur = '$edt_aff_couleur_salle' WHERE reglage = 'edt_aff_couleur_salle'");
		$aff_message .= "<p class=\"refus\"> Modification of the display of the colors recorded (room)</p>\n";
	}

	//Le réglage de l'affichage des salles
	$req_reg_salle = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_salle'");
	$tab_reg_salle = mysql_fetch_array($req_reg_salle);

	if ($edt_aff_salle === $tab_reg_salle['valeur']) {
		$aff_message .= "<p class=\"accept\">No modification of the display of the rooms</p>\n";
	}
	else {
		$modif_aff_salle = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_salle' WHERE reglage = 'edt_aff_salle'");
		updateOnline("UPDATE edt_setting SET valeur = '$edt_aff_salle' WHERE reglage = 'edt_aff_salle'");
		$aff_message .= "<p class=\"refus\"> Modification of the display of the room recorded </p>\n";

	}

	// le réglage de l'affichage du menu CHERCHER
	$req_cherche_salle = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'aff_cherche_salle'");
	$rep_cherche_salle = mysql_fetch_array($req_cherche_salle);

	if ($aff_cherche_salle === $rep_cherche_salle["valeur"]) {
		$aff_message .= "<p class=\"accept\">No modification of the display of the menu CHERCHER</p>\n";
	}
	else {
		$modif_cherch_salle = mysql_query("UPDATE edt_setting SET valeur = '$aff_cherche_salle' WHERE reglage = 'aff_cherche_salle'");
		updateOnline("UPDATE edt_setting SET valeur = '$aff_cherche_salle' WHERE reglage = 'aff_cherche_salle'");
		$aff_message .= "<p class=\"refus\"> Modification of the display of the menu CHERCHER recorded</p>\n";
	}

	// Le réglage du fonctionnement du menu (param_menu_edt)
	$req_param_menu = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'param_menu_edt'");
	$rep_param_menu = mysql_fetch_array($req_param_menu);

	if ($param_menu_edt === $rep_param_menu["valeur"]) {
		$aff_message .= "<p class=\"accept\">No modification of the functions of the menu.</p>\n";
	} else {
		$modif_param_menu = mysql_query("UPDATE edt_setting SET valeur = '$param_menu_edt' WHERE reglage = 'param_menu_edt'");
		updateOnline("UPDATE edt_setting SET valeur = '$param_menu_edt' WHERE reglage = 'param_menu_edt'");
		$aff_message .= "<p class=\"refus\">Modification of the functions of the menu recorded.</p>\n";
	}

} //if (isset($parametrer_ok))
else {
	$message = "In this page, you can parameterize the display of the timetables for all the users of Gepi.";
}



// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");
?>
<br/>
<!-- la page du corps de l'EdT -->

	<div id="lecorps">
<?php

    require_once("./menu.inc.new.php");
if (isset($aff_message)) {
	echo $aff_message;
}
?>

<form name="parametrer" method="post" action="edt_parametrer.php">
	<div id="art-main">
        <div class="art-sheet">
            <div class="art-sheet-tl"></div>
            <div class="art-sheet-tr"></div>
            <div class="art-sheet-bl"></div>
            <div class="art-sheet-br"></div>
            <div class="art-sheet-tc"></div>
            <div class="art-sheet-bc"></div>
            <div class="art-sheet-cl"></div>
            <div class="art-sheet-cr"></div>
            <div class="art-sheet-cc"></div>
            <div class="art-sheet-body">
                <div class="art-nav">
                	<div class="l"></div>
                	<div class="r"></div>
                </div>
                        <div class="art-layout-cell art-sidebar1">
                        </div>
                        <div class="art-layout-cell art-content">
						
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												Display of the courses
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
		<p>
			<input type="radio" id="edtMatiereCourt" name="edt_aff_matiere" value="court" <?php echo (aff_checked("edt_aff_matiere", "court")); ?>/>
			<label for="edtMatiereCourt"><?php echo FIELDS_PARAM_BUTTON1 ?></label>
			<br />
			<input type="radio" id="edtMatiereLong" name="edt_aff_matiere" value="long" <?php echo (aff_checked("edt_aff_matiere", "long")); ?>/>
			<label for="edtMatiereLong"><?php echo FIELDS_PARAM_BUTTON2 ?></label>

		</p>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>

<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                           <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												Display of the schedules
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
		<p>
			<input type="radio" id="edtCreneauxNoms" name="edt_aff_creneaux" value="noms" <?php echo (aff_checked("edt_aff_creneaux", "noms")); ?>/>
			<label for="edtCreneauxNoms">Display the name of the crenels (M1, M2,...).</label>
			<br />
			<input type="radio" id="edtCreneauxHeures" name="edt_aff_creneaux" value="heures" <?php echo (aff_checked("edt_aff_creneaux", "heures")); ?>/>
			<label for="edtCreneauxHeures">Display hours of beginning and end of the crenel.</label>
		</p>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>				
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                           <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												Display of the Colors (timetables classes and students)
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
		<p>
			<input type="radio" id="edtAffCouleur" name="edt_aff_couleur" value="coul" <?php echo (aff_checked("edt_aff_couleur", "coul")); ?>/>
			<label for="edtAffCouleur"> Display with colors</label>
			<br />
			<input type="radio" id="edtAffNb" name="edt_aff_couleur" value="nb" <?php echo (aff_checked("edt_aff_couleur", "nb")); ?>/>
			<label for="edtAffNb">Display without color</label>
		</p>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>		
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                           <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												Display of the Colors (timetables professors)
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
		<p>
			<input type="radio" id="edtAffCouleurProf" name="edt_aff_couleur_prof" value="coul" <?php echo (aff_checked("edt_aff_couleur_prof", "coul")); ?>/>
			<label for="edtAffCouleurProf">Display with colors</label>
			<br />
			<input type="radio" id="edtAffNbProf" name="edt_aff_couleur_prof" value="nb" <?php echo (aff_checked("edt_aff_couleur_prof", "nb")); ?>/>
			<label for="edtAffNbProf">Display without color</label>
		</p>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>	
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                           <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												Display of the Colors (timetables rooms)
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
		<p>
			<input type="radio" id="edtAffCouleurSalle" name="edt_aff_couleur_salle" value="coul" <?php echo (aff_checked("edt_aff_couleur_salle", "coul")); ?>/>
			<label for="edtAffCouleurSalle">Display with colors</label>
			<br />
			<input type="radio" id="edtAffNbSalle" name="edt_aff_couleur_salle" value="nb" <?php echo (aff_checked("edt_aff_couleur_salle", "nb")); ?>/>
			<label for="edtAffNbSalle">Display without color</label>
		</p>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>	
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                           <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												Display of the rooms
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
		<p>
			<input type="radio" id="affSalleNom" name="edt_aff_salle" value="nom" <?php echo (aff_checked("edt_aff_salle", "nom")); ?>/>
			<label for="affSalleNom">By the name of the room (room 2, conference room,...).</label>
			<br />
			<input type="radio" id="affSalleNumero" name="edt_aff_salle" value="numero" <?php echo (aff_checked("edt_aff_salle", "numero")); ?>/>
			<label for="affSalleNumero">By the number of the room only.</label>
		</p>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                           <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												Search the empty rooms
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
		<p>
			<input type="radio" id="affSalleAdmin" name="aff_cherche_salle" value="admin" <?php echo (aff_checked("aff_cherche_salle", "admin")); ?>/>
			<label for="affSalleAdmin"> the administrator has access to this functionality.</label>
			<br />
			<input type="radio" id="affSalleTous" name="aff_cherche_salle" value="tous" <?php echo (aff_checked("aff_cherche_salle", "tous")); ?>/>
			<label for="affSalleTous"> All the users have access to this functionality except the students and the responsibles of students.</label>
		</p>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>	
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                           <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												Menu on Internet Explorer 6
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
			<p>
				<input type="radio" id="edtMenuOver" name="param_menu_edt" value="mouseover" <?php echo (aff_checked("param_menu_edt", "mouseover")); ?>/>
				<label for="edtMenuOver">The links are displayed when the mouse passes on the title.</label>
			</p>

			<p>
				<input type="radio" id="edtMenuClick" name="param_menu_edt" value="click" <?php echo (aff_checked("param_menu_edt", "click")); ?>/>
				<label for="edtMenuClick">The links are displayed when the user clicks on the title.</label>
			</p>

			<p>
				<input type="radio" id="edtMenuRien" name="param_menu_edt" value="rien" <?php echo (aff_checked("param_menu_edt", "rien")); ?>/>
				<label for="edtMenuRien">All the links are visible all the time.</label>
			</p>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>	
			<div class="art-post">
			<input type="hidden" name="parametrer" value="ok" />
			<input type="hidden" name="parametrer1" value="ok" />

			<input class="art-button-wrapper" type="submit" name="Valider" value="Valider" />

			</div>


						</div>
			</div>
		</div>
	</div>
</form>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
