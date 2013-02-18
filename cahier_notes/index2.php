<?php
/**
 * Visualisation des moyennes des carnets de notes
 * 
 * $Id: index2.php 7849 2011-08-20 18:19:12Z regis $
 *
 * @copyright Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL, 
 * @package Carnet_de_notes
 * @subpackage affichage
 * @see add_token_field()
 * @see checkAccess()
 * @see getSettingValue()
 * @see Session::security_check()
 * @see sql_query1()
 * @see tentative_intrusion()
 */

/*
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

/**
 * Fichiers d'initialisation
 */
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

// INSERT INTO `droits` VALUES ('/cahier_notes/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// On fait quelques tests si le statut est 'prof', pour vérifier les restrictions d'accès
if ($_SESSION['statut'] == "professeur") {
	if ( (getSettingValue("GepiAccesMoyennesProf") != "yes") AND
         (getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes") AND
         (getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes")
       ) {
       	tentative_intrusion("1","Attempt of a teacher to access to the averages of the report cards without having the necessary authorizations .");
       	echo "You are not authorized to being here.";
/**
 * inclusion du pied de page
 */
		require ("../lib/footer.inc.php");
		die();
       }

}


$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

if (isset($id_classe)) {
	// On regarde si le type est correct :
	if (!is_numeric($id_classe)) {
		tentative_intrusion("2", "Change of the value of id_classe for a nonnumerical type.");
		echo "Error.";
/**
 * inclusion du pied de page
 */
		require ("../lib/footer.inc.php");
		die();
	}
	// On teste si le professeur a le droit d'accéder à cette classe
	if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
		$test = mysql_num_rows(mysql_query("SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
		if ($test == "0") {
			tentative_intrusion("2", "Attempt to access by a teacher to a class in which it does not teach, without having the authorization of it.");
			echo "You cannot access this class because you are not a professor there !";
            /**
             * inclusion du pied de page
             */
			require ("../lib/footer.inc.php");
			die();
		}
	}
}

$javascript_specifique="prepa_conseil/colorisation_visu_toutes_notes";
//**************** EN-TETE *****************
$titre_page = "Visualization of the averages of the report cards";

/**
 * Entête de la page
 */
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<?php
if (isset($id_classe)) {
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Home</a>";

	$current_eleve_classe = sql_query1("SELECT classe FROM classes WHERE id='$id_classe'");

	//echo "<a href=\"index2.php\">Choisir une autre classe</a> | Classe : ".$current_eleve_classe." |</p>\n";

	// ===========================================
	// Ajout lien classe précédente / classe suivante
	if($_SESSION['statut']=='scolarite'){
		$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}
	$chaine_options_classes="";

	$res_class_tmp=mysql_query($sql);
	if(mysql_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
					$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
					$id_class_suiv=$lig_class_tmp->id;
				}
				else{
					$id_class_suiv=0;
				}
			}
			else {
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
			}
			if($temoin_tmp==0){
				$id_class_prec=$lig_class_tmp->id;
			}
		}
	}
	// =================================
	if(isset($id_class_prec)){
		if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Previous Class</a>";}
	}
	if($chaine_options_classes!="") {
		echo " | Classe : <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
	}
	if(isset($id_class_suiv)){
		if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Next Class </a>";}
	}
	//fin ajout lien classe précédente / classe suivante
	// ===========================================
	//echo " | Classe : ".$current_eleve_classe."</p>\n";
	echo "</p>\n";
	echo "</form>\n";

	if(!isset($_SESSION['vtn_pref_num_periode'])) {
		$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'vtn_pref_%';";
		$get_pref=mysql_query($sql);
		if(mysql_num_rows($get_pref)>0) {
			while($lig_pref=mysql_fetch_object($get_pref)) {
				$_SESSION[$lig_pref->name]=$lig_pref->value;
			}
		}
	}

	echo "<form target=\"_blank\" name=\"visu_toutes_notes\" method=\"post\" action=\"visu_toutes_notes2.php\">\n";
	echo add_token_field();
	echo "<table border=\"1\" cellspacing=\"1\" cellpadding=\"10\" summary='Choice of the period'><tr>";
	echo "<td valign=\"top\"><b>Choose&nbsp;la&nbsp;period&nbsp;:&nbsp;</b><br />\n";
    /**
     * Gestion des périodes
     */
	include "../lib/periodes.inc.php";
	$i="1";
	while ($i < $nb_periode) {
		echo "<br />\n<input type=\"radio\" name=\"num_periode\" id='num_periode_$i' value=\"$i\" ";
		if(isset($_SESSION['vtn_pref_num_periode'])) {
			if($_SESSION['vtn_pref_num_periode']==$i) {echo "checked ";}
		}
		elseif ($i == 1) {echo "checked ";}
		echo "/>&nbsp;";
		echo "<label for='num_periode_$i' style='cursor:pointer;'>\n";
		echo ucfirst($nom_periode[$i]);
		echo "</label>\n";
		$i++;
	}
	echo "<br />\n<input type=\"radio\" name=\"num_periode\" id='num_periode_annee' value=\"annee\" ";
	if((isset($_SESSION['vtn_pref_num_periode']))&&($_SESSION['vtn_pref_num_periode']=='annee')) {echo "checked ";}
	echo "/>&nbsp;";
	echo "<label for='num_periode_annee' style='cursor:pointer;'>\n";
	echo "Whole year";
	echo "</label>\n";
	echo "</td>\n";

	echo "<td valign=\"top\">\n";
    echo "<b>Display Parameters</b><br />\n";
	echo "<input type=\"hidden\" name=\"id_classe\" value=\"".$id_classe."\" />";

	echo "<table border='0' width='100%' summary='Parameters'>\n";
	echo "<tr>\n";
	echo "<td>\n";

		echo "<table border='0' summary='Parameters'>\n";
		echo "<tr>\n";
		echo "<td>Width in pixel of the table : </td>\n";
		echo "<td><input type=text name=larg_tab size=3 ";
		if(isset($_SESSION['vtn_pref_larg_tab'])) {
			echo "value=\"".$_SESSION['vtn_pref_larg_tab']."\"";
		}
		else {
			echo "value=\"680\"";
		}
		echo " /></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>Edges in pixel of the table : </td>\n";
		echo "<td><input type=text name=bord size=3 ";
		if(isset($_SESSION['vtn_pref_bord'])) {
			echo "value=\"".$_SESSION['vtn_pref_bord']."\"";
		}
		else {
			echo "value=\"1\"";
		}
		echo " /></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "<label for='couleur_alterne' style='cursor:pointer;'>\n";
		echo "Background of the alternate lines : \n";
		echo "</label>\n";
		echo "</td>\n";
		echo "<td><input type=\"checkbox\" name=\"couleur_alterne\" id=\"couleur_alterne\" value='y' ";
		if(isset($_SESSION['vtn_pref_couleur_alterne'])) {
			if($_SESSION['vtn_pref_couleur_alterne']=='y') {
				echo "checked";
			}
		}
		else {
			echo "checked";
		}
		echo " /></td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "<td>\n";

		echo "<table border='0' summary='Fields'>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_abs\" id=\"aff_abs\" value='y' ";
		if(isset($_SESSION['vtn_pref_aff_abs'])) {
			if($_SESSION['vtn_pref_aff_abs']=='y') {
				echo "checked";
			}
		}
		else {
			echo "checked";
		}
		echo " /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_abs' style='cursor:pointer;'>\n";
		echo "Display the absences";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_reg\" id=\"aff_reg\" value='y' ";
		if(isset($_SESSION['vtn_pref_aff_reg'])) {
			if($_SESSION['vtn_pref_aff_reg']=='y') {
				echo "checked ";
			}
		}
		else {
			echo "checked ";
		}
		echo "/></td>\n";
		echo "<td>\n";
		echo "<label for='aff_reg' style='cursor:pointer;'>\n";
		echo "Display the regim\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_doub\" id=\"aff_doub\" value='y' ";
		if(isset($_SESSION['vtn_pref_aff_doub'])) {
			if($_SESSION['vtn_pref_aff_doub']=='y') {
				echo "checked";
			}
		}
		else {
			echo "checked";
		}
		echo " /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_doub' style='cursor:pointer;'>\n";
		echo "Display the mention doubling\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
		// On teste la présence d'au moins un coeff pour afficher la colonne des coef
		$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

		if (($affiche_rang == 'y') and ($test_coef != 0)) {
			echo "<tr>\n";
			echo "<td><input type=\"checkbox\" name=\"aff_rang\" id=\"aff_rang\" value='y' ";
			if(isset($_SESSION['vtn_pref_aff_rang'])) {
				if($_SESSION['vtn_pref_aff_rang']=='y') {
					echo "checked";
				}
			}
			else {
				echo "checked";
			}
			echo " /></td>\n";
			echo "<td>\n";
			echo "<label for='aff_rang' style='cursor:pointer;'>\n";
			echo "display the rank of the student\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

		echo "<tr>\n";
		echo "<td valign='top'><input type=\"checkbox\" name=\"aff_date_naiss\" id=\"aff_date_naiss\" /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_date_naiss' style='cursor:pointer;'>\n";
		echo "Display the date of birth of the students\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";


	echo "<br />\n<center><input type=\"submit\" name=\"ok\" value=\"Validate\" /></center>\n";
	echo "<br />\n<span class='small'>Notice : the table of notes is displayed without heading and in a new page. To return to this screen, you may close the window of the table of notes.</span>\n";
	echo "</td></tr>\n</table>\n";

	//============================================
	// Colorisation des résultats
	echo "<input type='checkbox' id='vtn_coloriser_resultats' name='vtn_coloriser_resultats' value='y' onchange=\"display_div_coloriser()\" ";
	if(isset($_SESSION['vtn_pref_coloriser_resultats'])) {
		if($_SESSION['vtn_pref_coloriser_resultats']=='y') {
			echo "checked";
		}
	}
	else {
		echo "checked";
	}
	echo "/><label for='vtn_coloriser_resultats'> Colors results.</label><br />\n";
	

	// Tableau des couleurs HTML:
	$chaine_couleurs='"aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen"';

	$tabcouleur=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");
	
	echo "<div id='div_coloriser'>\n";
	echo "<table id='table_couleur' class='boireaus' summary='Colors results'>\n";
	echo "<thead>\n";
		echo "<tr>\n";
		echo "<th><a href='#colorisation_resultats' onclick='add_tr_couleur();return false;'>Higher<br />limit</a></th>\n";
		echo "<th>Text color</th>\n";
		echo "<th>Cell Color</th>\n";
		echo "<th>Delete</th>\n";
		echo "</tr>\n";
	echo "</thead>\n";
	echo "<tbody id='table_body_couleur'>\n";

	$vtn_borne_couleur=array();
	$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'vtn_%' ORDER BY name;";
	$res_pref=mysql_query($sql);
	if(mysql_num_rows($res_pref)>0) {
		while($lig_pref=mysql_fetch_object($res_pref)) {
			if(substr($lig_pref->name,0,17)=='vtn_couleur_texte') {
				$vtn_couleur_texte[]=$lig_pref->value;
			}
			elseif(substr($lig_pref->name,0,19)=='vtn_couleur_cellule') {
				$vtn_couleur_cellule[]=$lig_pref->value;
			}
			elseif(substr($lig_pref->name,0,17)=='vtn_borne_couleur') {
				$vtn_borne_couleur[]=$lig_pref->value;
			}
		}

		/**
         *
         * @global type $cpt_couleur 
         * @global array $tabcouleur
         * @global type $vtn_borne_couleur
         * @global type $vtn_couleur_texte
         * @global type $vtn_couleur_cellule 
         */
		function add_tr_couleur() {
			global $cpt_couleur, $tabcouleur, $vtn_borne_couleur, $vtn_couleur_texte, $vtn_couleur_cellule;

			$cpt_tmp=$cpt_couleur+1;

			$alt=pow((-1),$cpt_couleur);
			echo "<tr id='tr_couleur_$cpt_tmp' class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input size='2' value='".$vtn_borne_couleur[$cpt_couleur]."' id='vtn_borne_couleur_$cpt_tmp' name='vtn_borne_couleur[]' type='text'>\n";
			echo "</td>\n";

			echo "<td>\n";
			echo "<select id='vtn_couleur_texte_$cpt_tmp' name='vtn_couleur_texte[]'>\n";
			echo "<option value=''>---</option>\n";
			for($i=0;$i<count($tabcouleur);$i++) {
				echo "<option style='background-color: $tabcouleur[$i];' value='$tabcouleur[$i]'";
				if($tabcouleur[$i]==$vtn_couleur_texte[$cpt_couleur]) {echo " selected='true'";}
				echo ">$tabcouleur[$i]</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";

			echo "<td>\n";
			echo "<select id='vtn_couleur_cellule_$cpt_tmp' name='vtn_couleur_cellule[]'>\n";
			echo "<option value=''>---</option>\n";
			for($i=0;$i<count($tabcouleur);$i++) {
				echo "<option style='background-color: $tabcouleur[$i];' value='$tabcouleur[$i]'";
				if($tabcouleur[$i]==$vtn_couleur_cellule[$cpt_couleur]) {echo " selected='true'";}
				echo ">$tabcouleur[$i]</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";

			echo "<td>\n";
			echo "<a href='#colorisation_resultats' onclick='suppr_ligne_couleur($cpt_tmp);return false;'><img src='../images/delete16.png' height='16' width='16' alt='Delete the ligne' /></a>\n";
			echo "</td>\n";

			echo "</tr>\n";
		}

		for($cpt_couleur=0;$cpt_couleur<count($vtn_borne_couleur);$cpt_couleur++) {add_tr_couleur();}
		$cpt_couleur++;

		echo "</tbody>\n";
		echo "</table>\n";
		echo "<a name='colorisation_resultats'></a>\n";
	
		echo "<script type='text/javascript'>
		// Couleurs prises en compte dans colorisation_visu_toutes_notes.js
		var tab_couleur=new Array($chaine_couleurs);

		var cpt_couleur=$cpt_couleur;

		//retouches_tab_couleur();
\n";

	}
	else {
		echo "</tbody>\n";
		echo "</table>\n";
		echo "<a name='colorisation_resultats'></a>\n";
	
		echo "<script type='text/javascript'>
		// Couleurs prises en compte dans colorisation_visu_toutes_notes.js
		var tab_couleur=new Array($chaine_couleurs);\n";

		echo "	// To start with three lines:
	add_tr_couleur();
	add_tr_couleur();
	add_tr_couleur();
	vtn_couleurs_par_defaut();\n";
	}
	echo "</script>\n";
//}
	echo "<p><br /></p>\n";
	echo "</div>\n";

	echo "<script type='text/javascript'>
function display_div_coloriser() {
if(document.getElementById('vtn_coloriser_resultats').checked==true) {
document.getElementById('div_coloriser').style.display='';
}
else {
document.getElementById('div_coloriser').style.display='none';
}
}
display_div_coloriser();
</script>\n";



	echo "</form>\n";
} else {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Home</a>";

	echo "</p>\n";
	echo "<p><b>Visualize the averages of the report cards by class :</b><br />\n";

	if($_SESSION['statut'] == 'scolarite'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes") {
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c  ORDER BY c.classe");
	}
	elseif($_SESSION['statut'] == 'cpe'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	}

	$lignes = mysql_num_rows($appel_donnees);

	if($lignes==0){
		echo "<p>No class is allotted to you.<br />Contact the administrator so that it carries out the suitable parameter setting in the Management of the classes.</p>\n";
	}
	else{
		$i = 0;
		unset($tab_lien);
		unset($tab_txt);
		while ($i < $lignes){
			$tab_lien[$i] = $_SERVER['PHP_SELF']."?id_classe=".mysql_result($appel_donnees, $i, "id");
			$tab_txt[$i] = mysql_result($appel_donnees, $i, "classe");
			$i++;

		}
		tab_liste($tab_txt,$tab_lien,3);
	}
}
echo "<p><i>Notice:</i> The averages visualized here are photographs at one moment t of what was typed by the professors.<br />\n";
echo "That necessarily does not correspond to what will appear on the bulletin after typing of other results and possible adjustments of the coefficients.</p>\n";
if ($_SESSION['statut'] == "professeur"
	AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"
	AND getSettingValue("GepiAccesMoyennesProfToutesTousEleves") != "yes") {
		echo "<p>If you do not teach to whole classes, only the student to which you teach will appear in the list, and the averages calculated will consider only the displayed 
student.</p>";
	}

/**
 * inclusion du pied de page
 */
require ("../lib/footer.inc.php");
?>