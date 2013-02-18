<?php
/*
 * $Id: param_bull.php 7477 2011-07-21 18:57:02Z crob $
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Begin standart header
$titre_page = "Paramètres de configuration des bulletins scolaires HTML";

$lang = isset($_GET['lang']) ? "_" . $_GET['lang'] : "";

$titre_page = empty($lang) ? ${'titre_page' . $lang} = "Parameters of configuration of HTML bulletins " : $titre_page;

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
include("../fckeditor/fckeditor.php") ;

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
$reg_ok = 'yes';
$msg = '';
$bgcolor = "#DEDEDE";



// Tableau des couleurs HTML:
$tabcouleur=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");

// tableau des polices pour avis du CC de classe
$tab_polices_avis=Array("Arial","Helvetica","Serif","Times","Times New Roman","Verdana",);

//Style des caractères avis
// tableau des styles de polices pour avis du CC de classe
$tab_styles_avis=Array("Normal","Gras","Italique","Gras et Italique");

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

if (isset($_POST['is_posted'])) {
	check_token();
	if (isset($_POST['textsize'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['textsize'])) || $_POST['textsize'] < 1) {
			$_POST['textsize'] = 10;
		}
		if (!saveSetting("textsize", $_POST['textsize'])) {
			$msg .= "Error during the recording of textsize !";
			$reg_ok = 'no';
		}
	}
	
	//==================================
	// AJOUT: boireaus
	if (isset($_POST['p_bulletin_margin'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['p_bulletin_margin'])) || $_POST['p_bulletin_margin'] < 1) {
			$_POST['p_bulletin_margin'] = 5;
		}
		if (!saveSetting("p_bulletin_margin", $_POST['p_bulletin_margin'])) {
			$msg .= "Error during the recording of p_bulletin_margin !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_body_marginleft'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_body_marginleft']))) {
			$_POST['bull_body_marginleft'] = 1;
		}
		if (!saveSetting("bull_body_marginleft", $_POST['bull_body_marginleft'])) {
			$msg .= "Error during the recording de bull_body_marginleft !";
			$reg_ok = 'no';
		}
	}
	
	
	//==================================
	
	
	if (isset($_POST['titlesize'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['titlesize'])) || $_POST['titlesize'] < 1) {
			$_POST['titlesize'] = 16;
		}
		if (!saveSetting("titlesize", $_POST['titlesize'])) {
			$msg .= "Error during the recording of titlesize !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['cellpadding'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['cellpadding'])) || $_POST['cellpadding'] < 0) {
			$_POST['cellpadding'] = 5;
		}
		if (!saveSetting("cellpadding", $_POST['cellpadding'])) {
			$msg .= "Error during the recording of cellpadding !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['cellspacing'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['cellspacing'])) || $_POST['cellspacing'] < 0) {
			$_POST['cellspacing'] = 2;
		}
		if (!saveSetting("cellspacing", $_POST['cellspacing'])) {
			$msg .= "Error during the recording of cellspacing !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['largeurtableau'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['largeurtableau'])) || $_POST['largeurtableau'] < 1) {
			$_POST['largeurtableau'] = 1440;
		}
		if (!saveSetting("largeurtableau", $_POST['largeurtableau'])) {
			$msg .= "Error during the recording of largeurtableau !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['col_matiere_largeur'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['col_matiere_largeur'])) || $_POST['col_matiere_largeur'] < 1) {
			$_POST['col_matiere_largeur'] = 300;
		}
		if (!saveSetting("col_matiere_largeur", $_POST['col_matiere_largeur'])) {
			$msg .= "Error during the recording of col_matiere_largeur !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['col_note_largeur'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['col_note_largeur'])) || $_POST['col_note_largeur'] < 1) {
			$_POST['col_note_largeur'] = 50;
		}
		if (!saveSetting("col_note_largeur", $_POST['col_note_largeur'])) {
			$msg .= "Error during the recording of col_note_largeur !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['col_boite_largeur'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['col_boite_largeur'])) || $_POST['col_boite_largeur'] < 1) {
			$_POST['col_boite_largeur'] = 120;
		}
		if (!saveSetting("col_boite_largeur", $_POST['col_boite_largeur'])) {
			$msg .= "Error during the recording of col_boite_largeur !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['col_hauteur'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['col_hauteur'])) || $_POST['col_hauteur'] < 1) {
			$_POST['col_hauteur'] = 0;
		}
		if (!saveSetting("col_hauteur", $_POST['col_hauteur'])) {
			$msg .= "Error during the recording of col_hauteur !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_ecart_entete'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_ecart_entete']))) {
			$_POST['bull_ecart_entete'] = 0;
		}
		if (!saveSetting("bull_ecart_entete", $_POST['bull_ecart_entete'])) {
			$msg .= "Error during the recording of bull_ecart_entete !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_espace_avis'])) {
	
		if ((!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_espace_avis']))) or ($_POST['bull_espace_avis'] <= 0)) {
			$_POST['bull_espace_avis'] = 1;
		}
		if (!saveSetting("bull_espace_avis", $_POST['bull_espace_avis'])) {
			$msg .= "Error during the recording of bull_espace_avis !";
			$reg_ok = 'no';
		}
	}
	
	
	if (isset($_POST['addressblock_padding_right'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_padding_right']))) {
			$_POST['addressblock_padding_right'] = 0;
		}
		if (!saveSetting("addressblock_padding_right", $_POST['addressblock_padding_right'])) {
			$msg .= "Error during the recording of addressblock_padding_right !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['addressblock_padding_top'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_padding_top']))) {
			$_POST['addressblock_padding_top'] = 0;
		}
		if (!saveSetting("addressblock_padding_top", $_POST['addressblock_padding_top'])) {
			$msg .= "Error during the recording of addressblock_padding_top !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['addressblock_padding_text'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_padding_text']))) {
			$_POST['addressblock_padding_text'] = 0;
		}
		if (!saveSetting("addressblock_padding_text", $_POST['addressblock_padding_text'])) {
			$msg .= "Error during the recording of addressblock_padding_text !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['addressblock_length'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_length']))) {
			$_POST['addressblock_length'] = 0;
		}
		if (!saveSetting("addressblock_length", $_POST['addressblock_length'])) {
			$msg .= "Error during the recording of addressblock_length !";
			$reg_ok = 'no';
		}
	}
	
	
	//==================================
	// Ajout: boireaus
	if (isset($_POST['addressblock_font_size'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_font_size']))) {
			$_POST['addressblock_font_size'] = 12;
		}
		if (!saveSetting("addressblock_font_size", $_POST['addressblock_font_size'])) {
			$msg .= "Error during the recording of addressblock_font_size !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['addressblock_logo_etab_prop'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_logo_etab_prop']))) {
				$addressblock_logo_etab_prop=50;
		}
		else{
				$addressblock_logo_etab_prop=$_POST['addressblock_logo_etab_prop'];
		}
	}
	else{
		if(getSettingValue("addressblock_logo_etab_prop")){
			$addressblock_logo_etab_prop=getSettingValue("addressblock_logo_etab_prop");
		}
		else{
			$addressblock_logo_etab_prop=50;
		}
	}
	
	if (isset($_POST['addressblock_classe_annee'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_classe_annee']))) {
				$addressblock_classe_annee=35;
		}
		else{
				$addressblock_classe_annee=$_POST['addressblock_classe_annee'];
		}
	}
	else{
		if(getSettingValue("addressblock_classe_annee")){
			$addressblock_classe_annee=getSettingValue("addressblock_classe_annee");
		}
		else{
			$addressblock_classe_annee=30;
		}
	}
	
	if((isset($_POST['addressblock_classe_annee']))&&(isset($_POST['addressblock_logo_etab_prop']))){
		$valtest=$addressblock_logo_etab_prop+$addressblock_classe_annee;
		if($valtest>100){
			$msg.="Erreur! La somme addressblock_logo_etab_prop+addressblock_classe_annee dépasse 100% de la largeur de la page !";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("addressblock_logo_etab_prop", $addressblock_logo_etab_prop)) {
				$msg .= "Error during the recording of addressblock_logo_etab_prop !";
				$reg_ok = 'no';
			}
	
			if (!saveSetting("addressblock_classe_annee", $addressblock_classe_annee)) {
				$msg .= "Error during the recording of addressblock_classe_annee !";
				$reg_ok = 'no';
			}
		}
	}
	
	
	if (isset($_POST['bull_ecart_bloc_nom'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_ecart_bloc_nom']))) {
			$_POST['bull_ecart_bloc_nom'] = 0;
		}
		if (!saveSetting("bull_ecart_bloc_nom", $_POST['bull_ecart_bloc_nom'])) {
			$msg .= "Error during the recording of bull_ecart_bloc_nom !";
			$reg_ok = 'no';
		}
	}
	
	
	if (isset($_POST['addressblock_debug'])) {
		if (($_POST['addressblock_debug']!="y")&&($_POST['addressblock_debug']!="n")) {
			$_POST['addressblock_debug'] = "n";
		}
		if (!saveSetting("addressblock_debug", $_POST['addressblock_debug'])) {
			$msg .= "Error during the recording of addressblock_debug !";
			$reg_ok = 'no';
		}
	}
	//==================================
	
	
	if (isset($_POST['page_garde_padding_top'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['page_garde_padding_top']))) {
			$_POST['page_garde_padding_top'] = 0;
		}
		if (!saveSetting("page_garde_padding_top", $_POST['page_garde_padding_top'])) {
			$msg .= "Error during the recording of page_garde_padding_top !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['page_garde_padding_left'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['page_garde_padding_left']))) {
			$_POST['page_garde_padding_left'] = 0;
		}
		if (!saveSetting("page_garde_padding_left", $_POST['page_garde_padding_left'])) {
			$msg .= "Error during the recording of page_garde_padding_left !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['page_garde_padding_text'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['page_garde_padding_text']))) {
			$_POST['page_garde_padding_text'] = 0;
		}
		if (!saveSetting("page_garde_padding_text", $_POST['page_garde_padding_text'])) {
			$msg .= "Error during the recording of de page_garde_padding_text !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['ok'])) {
	
		if (isset($_POST['page_garde_imprime'])) {
			$temp = 'yes';
		} else {
			$temp = 'no';
		}
		if (!saveSetting("page_garde_imprime", $temp)) {
			$msg .= "Error during the recording of page_garde_imprime !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($NON_PROTECT['page_garde_texte'])) {
		$imp = traitement_magic_quotes($NON_PROTECT['page_garde_texte']);
		if (!saveSetting("page_garde_texte", $imp)) {
			$msg .= "Error during the recording of page_garde_texte !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($NON_PROTECT['bull_formule_bas'])) {
		$imp = traitement_magic_quotes($NON_PROTECT['bull_formule_bas']);
		if (!saveSetting("bull_formule_bas", $imp)) {
			$msg .= "Error during the recording of bull_formule_bas !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_mention_nom_court'])) {
	
		if (!saveSetting("bull_mention_nom_court", $_POST['bull_mention_nom_court'])) {
			$msg .= "Error during the recording of bull_mention_nom_court !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_mention_doublant'])) {
	
		if (!saveSetting("bull_mention_doublant", $_POST['bull_mention_doublant'])) {
			$msg .= "Error during the recording of bull_mention_doublant !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affiche_eleve_une_ligne'])) {
	
		if (!saveSetting("bull_affiche_eleve_une_ligne", $_POST['bull_affiche_eleve_une_ligne'])) {
			$msg .= "Error during the recording of bull_mention_nom_court !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affiche_graphiques'])) {
	
		if (!saveSetting("bull_affiche_graphiques", $_POST['bull_affiche_graphiques'])) {
			$msg .= "Error during the recording of bull_affiche_graphiques !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affiche_appreciations'])) {
	
		if (!saveSetting("bull_affiche_appreciations", $_POST['bull_affiche_appreciations'])) {
			$msg .= "Error during the recording of bull_affiche_appreciations !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affiche_absences'])) {
	
		if (!saveSetting("bull_affiche_absences", $_POST['bull_affiche_absences'])) {
			$msg .= "Error during the recording of bull_affiche_absences !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_affiche_avis'])) {
	
		if (!saveSetting("bull_affiche_avis", $_POST['bull_affiche_avis'])) {
			$msg .= "Error during the recording of bull_affiche_avis !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_affiche_aid'])) {
	
		if (!saveSetting("bull_affiche_aid", $_POST['bull_affiche_aid'])) {
			$msg .= "Error during the recording of bull_affiche_aid !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_affiche_formule'])) {
	
		if (!saveSetting("bull_affiche_formule", $_POST['bull_affiche_formule'])) {
			$msg .= "Error during the recording of bull_affiche_formule !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_affiche_signature'])) {
	
		if (!saveSetting("bull_affiche_signature", $_POST['bull_affiche_signature'])) {
			$msg .= "Error during the recording of bull_affiche_signature !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_affiche_numero'])) {
	
		if (!saveSetting("bull_affiche_numero", $_POST['bull_affiche_numero'])) {
			$msg .= "Error during the recording of bull_affiche_numero !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affiche_etab'])) {
		if (!saveSetting("bull_affiche_etab", $_POST['bull_affiche_etab'])) {
			$msg .= "Error during the recording of bull_affiche_etab !";
			$reg_ok = 'no';
		}
	}
	
	
	if(isset($_POST['bull_bordure_classique'])) {
		if (!saveSetting("bull_bordure_classique", $_POST['bull_bordure_classique'])) {
			$msg .= "Error during the recording of bull_bordure_classique !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['choix_bulletin'])) {
	
		if (!saveSetting("choix_bulletin", $_POST['choix_bulletin'])) {
			$msg .= "Error during the recording of choix_bulletin";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['min_max_moyclas'])) {
	
		if (!saveSetting("min_max_moyclas", $_POST['min_max_moyclas'])) {
			$msg .= "Error during the recording of min_max_moyclas !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['activer_photo_bulletin'])) {
		if (!saveSetting("activer_photo_bulletin", $_POST['activer_photo_bulletin'])) {
			$msg .= "Error during the recording of activer_photo_bulletin !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_photo_hauteur_max'])) {
		if (!saveSetting("bull_photo_hauteur_max", $_POST['bull_photo_hauteur_max'])) {
			$msg .= "Error during the recording of bull_photo_hauteur_max !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_photo_largeur_max'])) {
		if (!saveSetting("bull_photo_largeur_max", $_POST['bull_photo_largeur_max'])) {
			$msg .= "Error during the recording of bull_photo_largeur_max !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_categ_font_size'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_categ_font_size']))) {
			$_POST['bull_categ_font_size'] = 10;
		}
		if (!saveSetting("bull_categ_font_size", $_POST['bull_categ_font_size'])) {
			$msg .= "Error during the recording of bull_categ_font_size !";
			$reg_ok = 'no';
		}
	}
	
	
	if(isset($_POST['bull_intitule_app'])) {
		if (!saveSetting("bull_intitule_app", $_POST['bull_intitule_app'])) {
			$msg .= "Error during the recording of bull_intitule_app !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_affiche_INE_eleve'])) {
		if (!saveSetting("bull_affiche_INE_eleve", $_POST['bull_affiche_INE_eleve'])) {
			$msg .= "Error during the recording of bull_affiche_INE_eleve !";
			$reg_ok = 'no';
		}
	}
	
	
	if(isset($_POST['bull_affiche_tel'])) {
		if (!saveSetting("bull_affiche_tel", $_POST['bull_affiche_tel'])) {
			$msg .= "Error during the recording of bull_affiche_tel !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_affiche_fax'])) {
		if (!saveSetting("bull_affiche_fax", $_POST['bull_affiche_fax'])) {
			$msg .= "Error during the recording of bull_affiche_fax !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_categ_bgcolor'])) {
		if((!in_array($_POST['bull_categ_bgcolor'],$tabcouleur))&&($_POST['bull_categ_bgcolor']!='')){
			$msg .= "Error during the recording of bull_categ_bgcolor ! (couleur invalide)";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("bull_categ_bgcolor", $_POST['bull_categ_bgcolor'])) {
				$msg .= "Error during the recording of bull_categ_bgcolor !";
				$reg_ok = 'no';
			}
		}
	}
	
	if (isset($_POST['bull_police_avis'])) {
		if((!in_array($_POST['bull_police_avis'],$tab_polices_avis))&&($_POST['bull_police_avis']!='')){
			$msg .= "Error during the recording of bull_police_avis ! (police invalide)";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("bull_police_avis", $_POST['bull_police_avis'])) {
				$msg .= "Error during the recording of bull_police_avis !";
				$reg_ok = 'no';
			}
		}
	}
	
	if (isset($_POST['bull_font_style_avis'])) {
		if((!in_array($_POST['bull_font_style_avis'],$tab_styles_avis))&&($_POST['bull_font_style_avis']!='')){
			$msg .= "Error during the recording of bull_font_style_avis ! (police invalide)";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("bull_font_style_avis", $_POST['bull_font_style_avis'])) {
				$msg .= "Error during the recording of bull_font_style_avis !";
				$reg_ok = 'no';
			}
		}
	}
	
	//taille de la police avis
	if(isset($_POST['bull_categ_font_size_avis'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_categ_font_size_avis']))) {
			$_POST['bull_categ_font_size_avis'] = 10;
		}
		if (!saveSetting("bull_categ_font_size_avis", $_POST['bull_categ_font_size_avis'])) {
			$msg .= "Error during the recording of bull_categ_font_size_avis !";
			$reg_ok = 'no';
		}
	}
	
	
	if (isset($_POST['genre_periode'])) {
		if (!saveSetting("genre_periode", $_POST['genre_periode'])) {
			$msg .= "Error during the recording of genre_periode !";
			$reg_ok = 'no';
		}
	}
	
	
	
	if (isset($_POST['bull_affich_nom_etab'])) {
		if($_POST['bull_affich_nom_etab']=="n") {
			$bull_affich_nom_etab="n";
		}
		else{
			$bull_affich_nom_etab="y";
		}
		if (!saveSetting("bull_affich_nom_etab", $bull_affich_nom_etab)) {
			$msg .= "Error during the recording of bull_affich_nom_etab !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affich_adr_etab'])) {
		if($_POST['bull_affich_adr_etab']=="n") {
			$bull_affich_adr_etab="n";
		}
		else{
			$bull_affich_adr_etab="y";
		}
		if (!saveSetting("bull_affich_adr_etab", $bull_affich_adr_etab)) {
			$msg .= "Error during the recording of bull_affich_adr_etab !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affich_mentions'])) {
		if($_POST['bull_affich_mentions']=="n") {
			$bull_affich_mentions="n";
		}
		else{
			$bull_affich_mentions="y";
		}
		if (!saveSetting("bull_affich_mentions", $bull_affich_mentions)) {
			$msg .= "Error during the recording of bull_affich_mentions !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affich_intitule_mentions'])) {
		if($_POST['bull_affich_intitule_mentions']=="n") {
			$bull_affich_intitule_mentions="n";
		}
		else{
			$bull_affich_intitule_mentions="y";
		}
		if (!saveSetting("bull_affich_intitule_mentions", $bull_affich_intitule_mentions)) {
			$msg .= "Error during the recording of bull_affich_intitule_mentions !";
			$reg_ok = 'no';
		}
	}

}

if (($reg_ok == 'yes') and (isset($_POST['ok']))) {
$msg = "Successful recording !";
}


// End standart header
require_once("../lib/header.inc");
if (!loadSettings()) {
    die("Error loading settings");
}
?>

<script type="text/javascript">
<!-- Debut
var nb='';
function SetDefaultValues(nb){
	if (nb=='A4V') {
		window.document.formulaire.titlesize.value = '14';
		window.document.formulaire.textsize.value = '8';
		window.document.formulaire.largeurtableau.value = '800';
		window.document.formulaire.col_matiere_largeur.value = '150';
		window.document.formulaire.col_note_largeur.value = '30';
		window.document.formulaire.col_boite_largeur.value = '120';
		window.document.formulaire.cellpadding.value = '3';
		window.document.formulaire.cellspacing.value = '1';
	}
	if(nb=='A3H'){
		window.document.formulaire.titlesize.value = '16';
		window.document.formulaire.textsize.value = '10';
		window.document.formulaire.largeurtableau.value = '1440';
		window.document.formulaire.col_matiere_largeur.value = '300';
		window.document.formulaire.col_note_largeur.value = '50';
		window.document.formulaire.col_boite_largeur.value = '150';
		window.document.formulaire.cellpadding.value = '5';
		window.document.formulaire.cellspacing.value = '2';
	}
	if(nb=='Adresse'){
		window.document.formulaire.addressblock_padding_right.value = '20';
		window.document.formulaire.addressblock_padding_top.value = '40';
		window.document.formulaire.addressblock_padding_text.value = '20';
		window.document.formulaire.addressblock_length.value = '60';
		window.document.formulaire.addressblock_font_size.value = '12';
		window.document.formulaire.addressblock_logo_etab_prop.value = '50';
		window.document.formulaire.addressblock_classe_annee.value = '35';
		window.document.formulaire.bull_ecart_bloc_nom.value = '1';

		//window.document.formulaire.addressblock_debug.value = 'n';
		window.document.getElementById('addressblock_debugn').checked='true';
	}
}
// fin du script -->
</script>

<p class=bold><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return </a>
| <!--a href="./index.php"> Imprimer les bulletins au format HTML</a-->
<a href="./bull_index.php">Print the bulletins</a>
| <a href="./param_bull_pdf.php"> Parameters of impression of pdf bulletins </a>
</p>

<?php
if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
{
    die("Insufficient rights to carry out this operation");
}
?>


<form name="formulaire" action="param_bull.php" method="post" style="width: 100%;">
<?php
echo add_token_field();
?>
<input type='hidden' name='is_posted' value='y' />
<H3>Page-setting of the bulletins</H3>
<table cellpadding="8" cellspacing="0" width="100%" border="0" summary='Mise en page'>

    <tr <?php $nb_ligne = 1; if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Restore the default settings :<br />
        &nbsp;&nbsp;&nbsp;<A HREF="javascript:SetDefaultValues('A4V')">Impression on A4 "portrait"</A><br />
        &nbsp;&nbsp;&nbsp;<A HREF="javascript:SetDefaultValues('A3H')">Impression on A3 "landscape"</A>

        </td>
        <td>
        &nbsp;
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_body_marginleft' style='cursor: pointer;'>margin left of the page (in pixels) :</label>
        </td>
        <td><input type="text" name="bull_body_marginleft" id="bull_body_marginleft" size="20" value="<?php
			if(getSettingValue("bull_body_marginleft")) {
				echo getSettingValue("bull_body_marginleft");
			}
			else{
				echo 1;
			}
		?>" onKeyDown="clavier_2(this.id,event,0,1000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='titlesize' style='cursor: pointer;'>Size in points of the headlines :</label>
        </td>
        <td><input type="text" name="titlesize" id="titlesize" size="20" value="<?php echo(getSettingValue("titlesize")); ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='textsize' style='cursor: pointer;'>Size in points of the text (except the titles) :</label>
        </td>
        <td><input type="text" name="textsize" id="textsize" size="20" value="<?php echo(getSettingValue("textsize")); ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <!-- Début AJOUT: boireaus -->
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='p_bulletin_margin' style='cursor: pointer;'>Top and bottom margins of the paragraphs in points of the text (except the titles) :</label>
        </td>
        <td><input type="text" name="p_bulletin_margin" id="p_bulletin_margin" size="20" value="<?php
		if(getSettingValue("p_bulletin_margin")!=""){
			echo(getSettingValue("p_bulletin_margin"));
		}
		else{
			echo "5";
		}?>" onKeyDown="clavier_2(this.id,event,0,40);" />
        </td>
    </tr>
    <!-- Fin AJOUT: boireaus -->
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='largeurtableau' style='cursor: pointer;'>Width of the table in pixels :</label>
        </td>
        <td><input type="text" name="largeurtableau" id="largeurtableau" size="20" value="<?php echo(getSettingValue("largeurtableau")); ?>" onKeyDown="clavier_2(this.id,event,0,5000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='col_matiere_largeur' style='cursor: pointer;'>Width of the first column (course) in pixels :</label><br />
        <span class="small">(If the content of a cell of the column is larger than the size envisaged, the mention above becomes null and void. The column in this case will be dimensioned by the navigator himself.)</span>
        </td>
        <td><input type="text" name="col_matiere_largeur" id="col_matiere_largeur" size="20" value="<?php echo(getSettingValue("col_matiere_largeur")); ?>" onKeyDown="clavier_2(this.id,event,0,2000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='col_note_largeur' style='cursor: pointer;'>Width of the columns min, max, classes and students in pixels :</label><br />
        <span class="small">(Same notices that above)</span>
        </td>
        <td><input type="text" name="col_note_largeur" id="col_note_largeur" size="20" value="<?php echo(getSettingValue("col_note_largeur")); ?>" onKeyDown="clavier_2(this.id,event,0,2000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='col_boite_largeur' style='cursor: pointer;'>Width of the cells containing the notes of the bulletins to be posted on the bulletins :</label><br />
        <span class="small">(Same notes that above)</span>
        </td>
        <td><input type="text" name="col_boite_largeur" id="col_boite_largeur" size="20" value="<?php echo(getSettingValue("col_boite_largeur")); ?>" onKeyDown="clavier_2(this.id,event,0,2000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='col_hauteur' style='cursor: pointer;'>Minimal height of lines in pixels ("0" if automatic) :</label><br />
        <span class="small">(If the content of a cell is such that the height fixed above is insufficient, the row height will be dimensioned by the navigator himself.)</span>
        </td>
        <td><input type="text" name="col_hauteur" id="col_hauteur" size="20" value="<?php echo(getSettingValue("col_hauteur")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='cellpadding' style='cursor: pointer;'>Space in pixels between the edge of a cell of the table and the contents of the cell :</label>
        </td>
        <td><input type="text" name="cellpadding" id="cellpadding" size="20" value="<?php echo(getSettingValue("cellpadding")); ?>" onKeyDown="clavier_2(this.id,event,0,50);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='cellspacing' style='cursor: pointer;'>Space in pixels between the cells of the table :</label>
        </td>
        <td><input type="text" name="cellspacing" id="cellspacing" size="20" value="<?php echo(getSettingValue("cellspacing")); ?>" onKeyDown="clavier_2(this.id,event,0,50);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_ecart_entete' style='cursor: pointer;'>Space (number of blank lines) between the heading of the bulletin and the table of the notes and appreciations :</label>
        </td>
        <td><input type="text" name="bull_ecart_entete" id="bull_ecart_entete" size="20" value="<?php echo(getSettingValue("bull_ecart_entete")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_espace_avis' style='cursor: pointer;'>Space (number of blank lines) for a manual typing of the opinion of the Staff meeting, if this one were not typed in GEPI :</label>
        </td>
        <td><input type="text" name="bull_espace_avis" id="bull_espace_avis" size="20" value="<?php echo(getSettingValue("bull_espace_avis")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Edges of the cells of the table of the averages and appreciations :
        </td>
        <td>
		<?php
			if(getSettingValue("bull_bordure_classique")=='n'){
				$bull_bordure_classique="n";
			}
			else{
				$bull_bordure_classique="y";
			}

			echo "<input type=\"radio\" name=\"bull_bordure_classique\" id='bull_bordure_classiquey' value=\"y\" ";
			if ($bull_bordure_classique=='y') echo " checked";
			echo " /><label for='bull_bordure_classiquey' style='cursor: pointer;'>&nbsp;classique&nbsp;HTML</label><br />\n";
			echo "<input type=\"radio\" name=\"bull_bordure_classique\" id='bull_bordure_classiquen' value=\"n\" ";
			if ($bull_bordure_classique=='n') echo " checked";
			echo " /><label for='bull_bordure_classiquen' style='cursor: pointer;'>&nbsp;trait&nbsp;noir</label>\n";
		?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_categ_font_size' style='cursor: pointer;'>Size in points of the text of the categories of courses (<i>when they are displayed</i>) :</label>
        </td>
	<?php
		if(getSettingValue("bull_categ_font_size")){
			$bull_categ_font_size=getSettingValue("bull_categ_font_size");
		}
		else{
			$bull_categ_font_size=10;
		}
	?>
        <td><input type="text" name="bull_categ_font_size" id="bull_categ_font_size" size="20" value="<?php echo $bull_categ_font_size; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_categ_bgcolor' style='cursor: pointer;'>Background color of the lines of categories of courses (<i>when they are displayed</i>) :</label>
        </td>
	<?php
		if(getSettingValue("bull_categ_bgcolor")){
			$bull_categ_bgcolor=getSettingValue("bull_categ_bgcolor");
		}
		else{
			$bull_categ_bgcolor="";
		}
	?>
        <td>
	<?php
		//<input type="text" name="bull_categ_bgcolor" size="20" value="echo $bull_categ_bgcolor;" />
		echo "<select name='bull_categ_bgcolor' id='bull_categ_bgcolor'>\n";
		echo "<option value=''>None</option>\n";
		for($i=0;$i<count($tabcouleur);$i++){
			if($tabcouleur[$i]=="$bull_categ_bgcolor"){
				$selected=" selected='true'";
			}
			else{
				$selected="";
			}
			echo "<option value='$tabcouleur[$i]'$selected>$tabcouleur[$i]</option>\n";
		}
		echo "</select>\n";
        ?>
	</td>
    </tr>

<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_categ_font_size_avis' style='cursor: pointer;'>Size in points of the text of the opinion of the staff meeting :</label>
        </td>
	<?php
		if(getSettingValue("bull_categ_font_size_avis")){
			$bull_categ_font_size_avis=getSettingValue("bull_categ_font_size_avis");
		}
		else{
			$bull_categ_font_size_avis=10;
		}
	?>
        <td><input type="text" name="bull_categ_font_size_avis" id="bull_categ_font_size_avis" size="20" value="<?php echo $bull_categ_font_size_avis; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_police_avis' style='cursor: pointer;'>font of character for the opinion of the staff meeting :</label>
        </td>
	<?php
		if(getSettingValue("bull_police_avis")){
			$bull_police_avis=getSettingValue("bull_police_avis");
		}
		else{
			$bull_police_avis="";
		}
	?>
        <td>
	<?php
		echo "<select name='bull_police_avis' id='bull_police_avis'>\n";
		echo "<option value=''>Aucune</option>\n";
		for($i=0;$i<count($tab_polices_avis);$i++){
			if($tab_polices_avis[$i]=="$bull_police_avis"){
				$selected=" selected='true'";
			}
			else{
				$selected="";
			}
			echo "<option value=\"$tab_polices_avis[$i]\" $selected>$tab_polices_avis[$i]</option>\n";
		}
		echo "</select>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_font_style_avis' style='cursor: pointer;'>Style of characters for the opinion of the staff meeting :</label>
        </td>
	<?php
		if(getSettingValue("bull_font_style_avis")){
			$bull_font_style_avis=getSettingValue("bull_font_style_avis");
		}
		else{
			$bull_font_style_avis="normal";
		}
	?>
        <td>
	<?php
		echo "<select name='bull_font_style_avis' id='bull_font_style_avis'>\n";
		for($i=0;$i<count($tab_styles_avis);$i++){
			if($tab_styles_avis[$i]=="$bull_font_style_avis"){
				$selected=" selected='true'";
			}
			else{
				$selected="";
			}
			echo "<option value=\"$tab_styles_avis[$i]\" $selected>$tab_styles_avis[$i]</option>\n";
		}
		echo "</select>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Kind of periods :<br />(<i>'quarter' ou 'six-month period' is male; 'period' is female</i>)
        </td>
	<?php
		if(getSettingValue("genre_periode")){
			$genre_periode=getSettingValue("genre_periode");
		}
		else{
			$genre_periode="M";
		}
	?>
        <td>
	<?php
        echo "<label for='genre_periodeM' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"genre_periode\" id=\"genre_periodeM\" value=\"M\" ";
        if ($genre_periode == 'M') {echo " checked";}
        echo " />&nbsp;Masculine</label>\n";
		echo "<br />\n";
        echo "<label for='genre_periodeF' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"genre_periode\" id=\"genre_periodeF\" value=\"F\" ";
        if ($genre_periode == 'F') {echo " checked";}
        echo " />&nbsp;Female</label>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the name of the school on the bulletin:<br />(<i>some school have the name in the Logo</i>)
        </td>
	<?php
		if(getSettingValue("bull_affich_nom_etab")){
			$bull_affich_nom_etab=getSettingValue("bull_affich_nom_etab");
		}
		else{
			$bull_affich_nom_etab="y";
		}
	?>
        <td>
	<?php
        echo "<label for='bull_affich_nom_etab_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_nom_etab\" id=\"bull_affich_nom_etab_y\" value=\"y\" ";
        if ($bull_affich_nom_etab == 'y') {echo " checked";}
        echo " />&nbsp;Yes</label>\n";
		echo "<br />\n";
        echo "<label for='bull_affich_nom_etab_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_nom_etab\" id=\"bull_affich_nom_etab_n\" value=\"n\" ";
        if ($bull_affich_nom_etab == 'n') {echo " checked";}
        echo " />&nbsp;No</label>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the address of the school on the bulletin :<br />(<i>some school have the address in the Logo</i>)
        </td>
	<?php
		if(getSettingValue("bull_affich_adr_etab")){
			$bull_affich_adr_etab=getSettingValue("bull_affich_adr_etab");
		}
		else{
			$bull_affich_adr_etab="y";
		}
	?>
        <td>
	<?php
        echo "<label for='bull_affich_adr_etab_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_adr_etab\" id=\"bull_affich_adr_etab_y\" value=\"y\" ";
        if ($bull_affich_adr_etab == 'y') {echo " checked";}
        echo " />&nbsp;Yes</label>\n";
		echo "<br />\n";
        echo "<label for='bull_affich_adr_etab_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_adr_etab\" id=\"bull_affich_adr_etab_n\" value=\"n\" ";
        if ($bull_affich_adr_etab == 'n') {echo " checked";}
        echo " />&nbsp;No</label>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display <?php echo $gepi_denom_mention;?>s (<i>Congratulations, encouragements, warnings,...</i>) with the opinion of the staff meeting.
        </td>
	<?php
		if(getSettingValue("bull_affich_mentions")){
			$bull_affich_mentions=getSettingValue("bull_affich_mentions");
		}
		else{
			$bull_affich_mentions="y";
		}
	?>
        <td>
	<?php
        echo "<label for='bull_affich_mentions_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_mentions\" id=\"bull_affich_mentions_y\" value=\"y\" ";
        if ($bull_affich_mentions == 'y') {echo " checked";}
        echo " />&nbsp;Yes</label>\n";
		echo "<br />\n";
        echo "<label for='bull_affich_mentions_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_mentions\" id=\"bull_affich_mentions_n\" value=\"n\" ";
        if ($bull_affich_mentions == 'n') {echo " checked";}
        echo " />&nbsp;No</label>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the heading <?php echo $gepi_denom_mention;?>s in bold before the <?php echo $gepi_denom_mention;?> chosen for a student with the opinion of the staff meeting.
        </td>
	<?php
		if(getSettingValue("bull_affich_intitule_mentions")){
			$bull_affich_intitule_mentions=getSettingValue("bull_affich_intitule_mentions");
		}
		else{
			$bull_affich_intitule_mentions="y";
		}
	?>
        <td>
	<?php
        echo "<label for='bull_affich_intitule_mentions_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_intitule_mentions\" id=\"bull_affich_intitule_mentions_y\" value=\"y\" ";
        if ($bull_affich_intitule_mentions == 'y') {echo " checked";}
        echo " />&nbsp;Yes</label>\n";
		echo "<br />\n";
        echo "<label for='bull_affich_intitule_mentions_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_intitule_mentions\" id=\"bull_affich_intitule_mentions_n\" value=\"n\" ";
        if ($bull_affich_intitule_mentions == 'n') {echo " checked";}
        echo " />&nbsp;No</label>\n";
        ?>
	</td>
    </tr>

</table>
<hr />


<center><input type="submit" name="ok" value="Save" style="font-variant: small-caps;"/></center>


<hr />
<?php
//Informations devant figurer sur le bulletin scolaire</H3>
?>
<h3>Information having to be reproduced on the Report cards</h3>
<table cellpadding="8" cellspacing="0" width="100%" border="0" summary='Informations'>
<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the short name of the class :
        </td>
        <!--td style='width:8em; text-align:right;'-->
        <td style='width:8em;'>
        <?php
        echo "<input type=\"radio\" name=\"bull_mention_nom_court\" id=\"bull_mention_nom_courty\" value=\"yes\" ";
        if (getSettingValue("bull_mention_nom_court") == 'yes') echo " checked";
        echo " /><label for='bull_mention_nom_courty' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_mention_nom_court\" id=\"bull_mention_nom_courtn\" value=\"no\" ";
        if (getSettingValue("bull_mention_nom_court") == 'no') echo " checked";
        echo " /><label for='bull_mention_nom_courtn' style='cursor: pointer;'>&nbsp;No</label>";
        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Dsiplay the mention "doubling" if necessary :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_mention_doublant\" id=\"bull_mention_doublanty\" value=\"yes\" ";
        if (getSettingValue("bull_mention_doublant") == 'yes') echo " checked";
        echo " /><label for='bull_mention_doublanty' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_mention_doublant\" id=\"bull_mention_doublantn\" value=\"no\" ";
        if (getSettingValue("bull_mention_doublant") == 'no') echo " checked";
        echo " /><label for='bull_mention_doublantn' style='cursor: pointer;'>&nbsp;No</label>";
        ?>
        </td>
    </tr>
	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display information on the student on only one line <i>(otherwise, one information by line)</i> :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_eleve_une_ligne\" id=\"bull_affiche_eleve_une_ligney\" value=\"yes\" ";
        if (getSettingValue("bull_affiche_eleve_une_ligne") == 'yes') echo " checked";
        echo " /><label for='bull_affiche_eleve_une_ligney' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_eleve_une_ligne\" id=\"bull_affiche_eleve_une_lignen\" value=\"no\" ";
        if (getSettingValue("bull_affiche_eleve_une_ligne") == 'no') echo " checked";
        echo " /><label for='bull_affiche_eleve_une_lignen' style='cursor: pointer;'>&nbsp;No</label>";
        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the appreciations of the courses :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_appreciations\" id=\"bull_affiche_appreciationsy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_appreciations") == 'y') echo " checked";
        echo " /><label for='bull_affiche_appreciationsy' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_appreciations\" id=\"bull_affiche_appreciationsn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_appreciations") != 'y') echo " checked";
        echo " /><label for='bull_affiche_appreciationsn' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the data on the absences :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_absences\" id=\"bull_affiche_absencesy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_absences") == 'y') echo " checked";
        echo " /><label for='bull_affiche_absencesy' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_absences\" id=\"bull_affiche_absencesn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_absences") != 'y') echo " checked";
        echo " /><label for='bull_affiche_absencesn' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the opinions of the staff meeting :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_avis\" id=\"bull_affiche_avisy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_avis") == 'y') echo " checked";
        echo " /><label for='bull_affiche_avisy' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_avis\" id=\"bull_affiche_avisn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_avis") != 'y') echo " checked";
        echo " /><label for='bull_affiche_avisn' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the data on the IDA :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_aid\" id=\"bull_affiche_aidy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_aid") == 'y') echo " checked";
        echo " /><label for='bull_affiche_aidy' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_aid\" id=\"bull_affiche_aidn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_aid") != 'y') echo " checked";
        echo " /><label for='bull_affiche_aidn' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the number of the bulletin :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_numero\" id=\"bull_affiche_numeroy\" value=\"yes\" ";
        if (getSettingValue("bull_affiche_numero") == 'yes') echo " checked";
        echo " /><label for='bull_affiche_numeroy' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_numero\" id=\"bull_affiche_numeron\" value=\"no\" ";
        if (getSettingValue("bull_affiche_numero") == 'no') echo " checked";
        echo " /><label for='bull_affiche_numeron' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the graphs indicating the levels (A, B, C+, C-, D ou E) :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_graphiques\" id=\"bull_affiche_graphiquesy\" value=\"yes\" ";
        if (getSettingValue("bull_affiche_graphiques") == 'yes') echo " checked";
        echo " /><label for='bull_affiche_graphiquesy' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_graphiques\" id=\"bull_affiche_graphiquesn\" value=\"no\" ";
        if (getSettingValue("bull_affiche_graphiques") != 'yes') echo " checked";
        echo " /><label for='bull_affiche_graphiquesn' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the name of the principal professor and the head of School :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_signature\" id=\"bull_affiche_signaturey\" value=\"y\" ";
        if (getSettingValue("bull_affiche_signature") == 'y') echo " checked";
        echo " /><label for='bull_affiche_signaturey' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_signature\" id=\"bull_affiche_signaturen\" value=\"n\" ";
        if (getSettingValue("bull_affiche_signature") != 'y') echo " checked";
        echo " /><label for='bull_affiche_signaturen' style='cursor: pointer;'>&nbsp;No</label>";
        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the school of origin on the bulletin :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_etab\" id=\"bull_affiche_etaby\" value=\"y\" ";
        if (getSettingValue("bull_affiche_etab") == 'y') echo " checked";
        echo " /><label for='bull_affiche_etaby' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_etab\" id=\"bull_affiche_etabn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_etab") != 'y') echo " checked";
        echo " /><label for='bull_affiche_etabn' style='cursor: pointer;'>&nbsp;No</label>";
        ?>
        </td>
    </tr>


<?php
if (getSettingValue("active_module_trombinoscopes")=='y') {
	echo "<tr ";
	if($nb_ligne % 2){echo "bgcolor=".$bgcolor;}
	$nb_ligne++;
	echo ">\n";
?>
        <td style="font-variant: small-caps;">
        Display the photograph of the student on the bulletin :
        </td>
        <td>
<?php
	echo "<input type='radio' name='activer_photo_bulletin' id='activer_photo_bulletiny' value='y'";
	if (getSettingValue("activer_photo_bulletin")=='y'){echo "checked";}
	echo " onchange=\"aff_lig_photo('afficher')\" /><label for='activer_photo_bulletiny' style='cursor: pointer;'>&nbsp;Yes</label>\n";
	echo "<input type='radio' name='activer_photo_bulletin' id='activer_photo_bulletinn' value='n'";
	if (getSettingValue("activer_photo_bulletin")!='y'){echo "checked";}
	echo " onchange=\"aff_lig_photo('cacher')\" /><label for='activer_photo_bulletinn' style='cursor: pointer;'>&nbsp;No</label>\n";
?>
        </td>
    </tr>
<?php
	if(getSettingValue("bull_photo_hauteur_max")){
		$bull_photo_hauteur_max=getSettingValue("bull_photo_hauteur_max");
	}
	else{
		$bull_photo_hauteur_max=80;
	}

	if(getSettingValue("bull_photo_largeur_max")){
		$bull_photo_largeur_max=getSettingValue("bull_photo_largeur_max");
	}
	else{
		$bull_photo_largeur_max=80;
	}
?>
    <tr id='ligne_bull_photo_hauteur_max'>
	<td style="font-variant: small-caps;"><label for='bull_photo_hauteur_max' style='cursor: pointer;'>Maximum height of the photograph in pixels :</label></td>
	<td><input type="text" name="bull_photo_hauteur_max" id="bull_photo_hauteur_max" size='4' value="<?php echo $bull_photo_hauteur_max;?>" /></td>
    </tr>
    <tr id='ligne_bull_photo_largeur_max'>
	<td style="font-variant: small-caps;"><label for='bull_photo_largeur_max' style='cursor: pointer;'>Maximum width of the photograph in pixels :</label></td>
	<td><input type="text" name="bull_photo_largeur_max" id="bull_photo_largeur_max" size='4' value="<?php echo $bull_photo_largeur_max;?>" />

	<script type='text/javascript'>
		function aff_lig_photo(mode){
			if(mode=='afficher'){
				document.getElementById('ligne_bull_photo_hauteur_max').style.display='';
				document.getElementById('ligne_bull_photo_largeur_max').style.display='';
			}
			else{
				document.getElementById('ligne_bull_photo_hauteur_max').style.display='none';
				document.getElementById('ligne_bull_photo_largeur_max').style.display='none';
			}
		}

		if(document.getElementById('activer_photo_bulletiny').checked==false){
			aff_lig_photo('cacher');
		}
	</script>
	</td>
    </tr>
<?php
}
?>




    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the telephone number of the school :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_tel\" id=\"bull_affiche_tely\" value=\"y\" ";
        if (getSettingValue("bull_affiche_tel") == 'y') echo " checked";
        echo " /><label for='bull_affiche_tely' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_tel\" id=\"bull_affiche_teln\" value=\"n\" ";
        if (getSettingValue("bull_affiche_tel") != 'y') echo " checked";
        echo " /><label for='bull_affiche_teln' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the fax number of the school :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_fax\" id=\"bull_affiche_faxy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_fax") == 'y') echo " checked";
        echo " /><label for='bull_affiche_faxy' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_fax\" id=\"bull_affiche_faxn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_fax") != 'y') echo " checked";
        echo " /><label for='bull_affiche_faxn' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;" colspan='2'>
        <label for='bull_intitule_app' style='cursor: pointer;'>Heading of the column Appreciations :</label>
        <?php
		echo "<input type=\"text\" name=\"bull_intitule_app\" id=\"bull_intitule_app\" value=\"".getSettingValue('bull_intitule_app')."\" size='100' />";
        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the number INE of the student :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_INE_eleve\" id=\"bull_affiche_INE_elevey\" value=\"y\" ";
        if (getSettingValue("bull_affiche_INE_eleve") == 'y') echo " checked";
        echo " /><label for='bull_affiche_INE_elevey' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_INE_eleve\" id=\"bull_affiche_INE_eleven\" value=\"n\" ";
        if (getSettingValue("bull_affiche_INE_eleve") != 'y') echo " checked";
        echo " /><label for='bull_affiche_INE_eleven' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Display the formula appearing in bottom of each bulletin :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_formule\" id=\"bull_affiche_formuley\" value=\"y\" ";
        if (getSettingValue("bull_affiche_formule") == 'y') echo " checked";
        echo " /><label for='bull_affiche_formuley' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_formule\" id=\"bull_affiche_formulen\" value=\"n\" ";
        if (getSettingValue("bull_affiche_formule") != 'y') echo " checked";
        echo " /><label for='bull_affiche_formulen' style='cursor: pointer;'>&nbsp;No</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;" colspan="2">
        <label for='no_anti_inject_bull_formule_bas' style='cursor: pointer;'>Formula appearing in bottom of each bulletin :</label>
        <input type="text" name="no_anti_inject_bull_formule_bas" id="no_anti_inject_bull_formule_bas" size="100" value="<?php echo(getSettingValue("bull_formule_bas")); ?>" />
        </td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Choice of the appearance of the bulletin (position and regrouping of the averages of the class)
		<ul>
		<li><i><label for='choix_bulletin1' style='cursor: pointer;'>All the information quantified on the class and the student are before the column <?php echo getSettingValue('bull_intitule_app')?>.</label></i><br />
		<li><i><label for='choix_bulletin2' style='cursor: pointer;'>Idem choice 1. Information on the class is gathered in a category "For the class".</label></i><br />
		<li><i><label for='choix_bulletin3' style='cursor: pointer;'>Idem choice 2. Information for the class is located after the column <?php echo getSettingValue('bull_intitule_app')?>.</label></i><br />
        </ul>
		</td>
        <td> <br />
        <?php
		echo "<input type='radio' name='choix_bulletin' id='choix_bulletin1' value='1'";
		if (getSettingValue("choix_bulletin") == '1') echo " checked";
		echo " /> <label for='choix_bulletin1' style='cursor: pointer;'>Choice 1</label><br />";
		echo "<input type='radio' name='choix_bulletin' id='choix_bulletin2' value='2'";
		if (getSettingValue("choix_bulletin") == '2') echo " checked";
		echo " /> <label for='choix_bulletin2' style='cursor: pointer;'>Choice 2</label><br />";
		echo "<input type='radio' name='choix_bulletin' id='choix_bulletin3' value='3'";
		//echo "toto".getSettingValue("choix_bulletin");
		if (getSettingValue("choix_bulletin") == '3') echo " checked";
		echo " /> <label for='choix_bulletin3' style='cursor: pointer;'>Choice 3</label><br />";
        ?>
        </td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">Display the minimal, class and maximum averages in only one column to gain place for the appreciation : </td>
        <td>
	    <?php
        echo "<input type=\"radio\" name=\"min_max_moyclas\" id=\"min_max_moyclas1\" value='1' ";
        if (getSettingValue("min_max_moyclas") == '1') echo " checked";
        echo " /><label for='min_max_moyclas1' style='cursor: pointer;'>&nbsp;Yes</label>";
        echo "<input type=\"radio\" name=\"min_max_moyclas\" id=\"min_max_moyclas0\" value='0' ";
        if (getSettingValue("min_max_moyclas") != '1') echo " checked";
        echo " /><label for='min_max_moyclas0' style='cursor: pointer;'>&nbsp;No</label>";
        ?>
        </td>
    </tr>

</table>
<hr />




<center><input type="submit" name="ok" value="Save" style="font-variant: small-caps;"/></center>



<hr />
<H3>Addresses Block</H3>
<center><table border="1" cellpadding="10" width="90%" summary='Bloc adresse'><tr><td>
These options control the positioning of the block addresses of the person in charge of the student directly on the bulletin (and not on the title page - to see below). The Display of this block is controlled class by class, on the level of the parameter setting of the class.
</td></tr></table></center>

<table cellpadding="8" cellspacing="0" width="100%" border="0" summary='Bloca adresse'>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;;$nb_ligne++;?>>
        <td colspan='2' style="font-variant: small-caps;">
	<a href="javascript:SetDefaultValues('Adresse')">Restore the default settings</a>
        </td>
     </tr>


    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_padding_right' style='cursor: pointer;'>Space in mm between the right margin of the sheet and the block "addresses" :</label>
        </td>
        <td><input type="text" name="addressblock_padding_right" id="addressblock_padding_right" size="20" value="<?php echo(getSettingValue("addressblock_padding_right")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Take account of the right margin of impression to calculate space between the flat rim of the sheet and the block addresses</i></td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_padding_top' style='cursor: pointer;'>Space in mm between the top margin of the sheet and the block "addresses" :</label>
        </td>
        <td><input type="text" name="addressblock_padding_top" id="addressblock_padding_top" size="20" value="<?php echo(getSettingValue("addressblock_padding_top")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Take account of the top margin of impression to calculate space between the top edge of the sheet and the block addresses</i></td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_padding_text' style='cursor: pointer;'>Vertical space in mm between the block "addresses" and the block of the results :</label>
        </td>
        <td><input type="text" name="addressblock_padding_text" id="addressblock_padding_text" size="20" value="<?php echo(getSettingValue("addressblock_padding_text")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_length' style='cursor: pointer;'>Length in mm of the block "addresses" :</label>
        </td>
        <td><input type="text" name="addressblock_length" id="addressblock_length" size="20" value="<?php echo(getSettingValue("addressblock_length")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_font_size' style='cursor: pointer;'>Size in points of the texts of the block "addresses" :</label>
        </td>
	<?php
		if(!getSettingValue("addressblock_font_size")){
			$addressblock_font_size=12;
		}
		else{
			$addressblock_font_size=getSettingValue("addressblock_font_size");
		}
	?>
        <td><input type="text" name="addressblock_font_size" id="addressblock_font_size" size="20" value="<?php echo $addressblock_font_size; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_logo_etab_prop' style='cursor: pointer;'>Proportion (in % of the width of page) allocated to the logo and the address of the school :</label>
        </td>
	<?php
		if(!getSettingValue("addressblock_logo_etab_prop")){
			$addressblock_logo_etab_prop=50;
		}
		else{
			$addressblock_logo_etab_prop=getSettingValue("addressblock_logo_etab_prop");
		}
	?>
        <td><input type="text" name="addressblock_logo_etab_prop" id="addressblock_logo_etab_prop" size="20" value="<?php echo $addressblock_logo_etab_prop; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_classe_annee' style='cursor: pointer;'>Proportion (in % of the width of page) allocated to the block "Classifies, year, period" :</label>
        </td>
	<?php
		if(!getSettingValue("addressblock_classe_annee")){
			$addressblock_classe_annee=35;
		}
		else{
			$addressblock_classe_annee=getSettingValue("addressblock_classe_annee");
		}
	?>
        <td><input type="text" name="addressblock_classe_annee" id="addressblock_classe_annee" size="20" value="<?php echo $addressblock_classe_annee; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_ecart_bloc_nom' style='cursor: pointer;'>A number of jumps of line between the Logo+School block and the block Name, first name? of the pupil :</label>
        </td>
	<?php
		if(!getSettingValue("bull_ecart_bloc_nom")){
			$bull_ecart_bloc_nom=0;
		}
		else{
			$bull_ecart_bloc_nom=getSettingValue("bull_ecart_bloc_nom");
		}
	?>
        <td><input type="text" name="bull_ecart_bloc_nom" id="bull_ecart_bloc_nom" size="20" value="<?php echo $bull_ecart_bloc_nom; ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <font color='red'>Activate the display of the edges to include/understand the
presentation with block "addresses"</font> :<br />
		<span style='font-size:x-small;'>It is necessary to adjust the parameters of the fields '<i>Space in mm between the top margin of the sheet and the block
"addresses"</i>', '<i>Length in mm of the block "addresses"</i>' and '<i>Proportion (in % of the width of page) allocated to the logo and the address of the school</i>' so that the blue and green boxes do not enter in collision (<i>you can modify the size of the window of the navigator using the mouse to understand the possible aberrations of presentation for certain combinations of values</i>).</span>
        </td>
	<?php
		if(!getSettingValue("addressblock_debug")){
			$addressblock_debug="n";
		}
		else{
			$addressblock_debug=getSettingValue("addressblock_debug");
		}
	?>
        <td valign='top'><input type="radio" id="addressblock_debugy" name="addressblock_debug" value="y" <?php if($addressblock_debug=="y"){echo "checked";}?> /><label for='addressblock_debugy' style='cursor: pointer;'> Oui</label> <input type="radio" id="addressblock_debugn" name="addressblock_debug" value="n" <?php if($addressblock_debug=="n"){echo "checked";}?> /><label for='addressblock_debugn' style='cursor: pointer;'> No</label>
        </td>
    </tr>
</table>
<hr />



<center><input type="submit" name="ok" value="Save" style="font-variant: small-caps;"/></center>



<hr />
<H3>Title page</H3>
<center><table border="1" cellpadding="10" width="90%" summary='Title page'><tr><td>
The title page contains following information :
<ul>
<li>the address where to send the bulletin. If you use window envelopes, you can modify the parameters below so that it appears within the frame envisaged for this purpose,</li>
<li>a text which you can personalize (see down).</li>
</ul>
<b><a href='javascript:centrerpopup("./modele_page_garde.php",600,600,"scrollbars=yes,statusbar=yes,menubar=yes,resizable=yes")'>Preview of the title page</a></b>
(Caution : The layout <!--des bulletins -->is very different on the screen and the impression.
Please use the function "print preview" in order to see the result.
</td></tr></table></center>
<table cellpadding="8" cellspacing="0" width="100%" border="0" summary='Page de garde'>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;"><label for='page_garde_imprime' style='cursor: pointer;'>Print the title pages : </label></td>
        <td><input type="checkbox" name="page_garde_imprime" id="page_garde_imprime" value="yes" <?php if (getSettingValue("page_garde_imprime")=='yes') echo "checked"; ?>/>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;?>>
        <td style="font-variant: small-caps;">
        <label for='page_garde_padding_left' style='cursor: pointer;'>Space in cm between the left margin of the sheet and the block "addresses" :</label>
        </td>
        <td><input type="text" name="page_garde_padding_left" id="page_garde_padding_left" size="20" value="<?php echo(getSettingValue("page_garde_padding_left")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Take account of the left margin of impression to calculate space between the right rim of the sheet and the addresses block </i></td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;?>>
        <td style="font-variant: small-caps;">
        <label for='page_garde_padding_top' style='cursor: pointer;'>Space in cm between the top margin of the sheet and the block "addresses" :</label>
        </td>
        <td><input type="text" name="page_garde_padding_top" id="page_garde_padding_top" size="20" value="<?php echo(getSettingValue("page_garde_padding_top")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Take account of the high margin of impression to calculate space between the top edge of the sheet and the addresses block </i></td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='page_garde_padding_text' style='cursor: pointer;'>Space in cm between the block "addresses" and the block "text" :</label>
        </td>
        <td><input type="text" name="page_garde_padding_text" id="page_garde_padding_text" size="20" value="<?php echo(getSettingValue("page_garde_padding_text")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
    <?php
    $impression = getSettingValue("page_garde_texte");
    echo "<td colspan=\"2\" valign=\"top\"  style=\"font-variant: small-caps;\">Text of the title page appearing after the address : </td>
	</tr>";
    // Modif : on utilise toute la largeur de la page pour afficher l'éditeur de textes
    echo "
	<tr><td colspan=\"2\" ><div class='small'>
		<i>Mise en forme du message :</i>";

    $oFCKeditor = new FCKeditor('no_anti_inject_page_garde_texte') ;
    $oFCKeditor->BasePath = '../fckeditor/' ;
    $oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
    $oFCKeditor->ToolbarSet = 'Basic' ;
    $oFCKeditor->Value      = $impression ;
    $oFCKeditor->Create() ;
?>

		</div>
	</td></tr>

</table>

<hr />
<p style="text-align: center;"><input type="submit" name="ok" value="Save" style="font-variant: small-caps;"/></p>
</form>

<?php require("../lib/footer.inc.php");