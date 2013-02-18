<?php
/**
 * Ajouter, modifier un devoir dans une évaluation cumule
 * 
 * $Id: add_modif_cc_dev.php 7742 2011-08-13 23:37:55Z regis $
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * @package Carnet_de_notes
 * @subpackage Evaluation_cumule
 * @license GNU/GPL 
 * @see add_token_field()
 * @see check_token()
 * @see checkAccess()
 * @see get_group()
 * @see getSettingValue()
 * @see Session::security_check()
 * @see traitement_magic_quotes()
 * @see Verif_prof_cahier_notes()
 */

/* This file is part of GEPI.
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("The module is not activated.");
}
/**
 * Calcul des arrondis
 */
require('cc_lib.php');

$id_racine = isset($_POST["id_racine"]) ? $_POST["id_racine"] : (isset($_GET["id_racine"]) ? $_GET["id_racine"] : NULL);

if(!isset($id_racine)) {
	$mess="Root not specified for $nom_cc.<br />";
	header("Location: index.php?msg=$mess");
	die();
}

// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("You try to penetrate in a report card which does not belong to you !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group = get_group($id_groupe);
$periode_num = mysql_result($appel_cahier_notes, 0, 'periode');
/**
 * Gestion des périodes
 */
include "../lib/periodes.inc.php";

$id_dev = isset($_POST["id_dev"]) ? $_POST["id_dev"] : (isset($_GET["id_dev"]) ? $_GET["id_dev"] : NULL);

if ($id_dev)  {
	$sql="SELECT * FROM cc_dev WHERE id='$id_dev' AND id_groupe='$id_groupe';";
	$query = mysql_query($sql);
	if($query) {
		$id_cn_dev = mysql_result($query, 0, 'id_cn_dev');
		$nom_court = mysql_result($query, 0, 'nom_court');
		$nom_complet = mysql_result($query, 0, 'nom_complet');
		$description = mysql_result($query, 0, 'description');
	    $precision = mysql_result($query, 0, 'arrondir');
	}
	else {
		header("Location: index.php?msg=".rawurlencode("The number of Exam is not associated this group."));
		die();
	}
}
else {

	$nom_court = "CC";
	$nom_complet = ucfirst($nom_cc)." n°";
	$description = "";
	$precision="s1";
}

$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];

// enregistrement des données
if (isset($_POST['is_posted'])) {
	check_token();

	$msg="";

	$nom_court=traitement_magic_quotes($_POST['nom_court']);
	$nom_complet=traitement_magic_quotes($_POST['nom_complet']);
	$description=traitement_magic_quotes($_POST['description']);
	$precision=$_POST['precision'];
	if(!my_ereg("^(s1|s5|se|p1|p5|pe)$", $precision)) {
		$msg.="Invalid  '$precision' Precision; It was replaced by 's1'.";
	}

	if(!isset($id_dev)) {
		$sql="INSERT INTO cc_dev SET id_groupe='$id_groupe', nom_court='$nom_court', nom_complet='$nom_complet', description='$description', arrondir='$precision';";
		$insert=mysql_query($sql);
		if(!$insert) {
			$msg.="Error during creation of $nom_cc.";
		}
		else {
			$id_dev=mysql_insert_id();
			$msg.="Creation of $nom_cc effectuée.";
		}
		header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
		die();
	}
	else {
		// Le devoir existe déjà
		// S'il est rattaché à un devoir existant dans le carnet de notes, il ne doit pas pouvoir être modifié si la période correspondante est close.

		// Sinon, il faut mettre à jour le devoir associé
		
		$sql="UPDATE cc_dev SET nom_court='$nom_court', nom_complet='$nom_complet', description='$description', arrondir='$precision' WHERE id_groupe='$id_groupe' AND id='$id_dev';";
		$update=mysql_query($sql);
		if(!$update) {
			$msg.="Error at the time of the update of $nom_cc.";
		}
		else {
			$msg.="$nom_cc updated.";
		}
		header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
		die();

	}

}

//**************** EN-TETE *****************
$titre_page = "Report card - Addition/modification of $nom_cc";
/**
 * Entête de la page
 */
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
echo add_token_field();

echo "<div class='norme'>\n";
echo "<p class='bold'>\n";
echo "<a href='index_cc.php?id_racine=$id_racine'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>\n";
echo "</p>\n";
echo "</div>\n";

echo "<h2 class='gepi'>Configuration of $nom_cc&nbsp;:</h2>\n";

$aff_nom_court="y";
$aff_nom_complet="y";
$aff_description="y";
$aff_date="y";
$aff_note_sur="y";
$aff_precision="y";

echo "<div align='center'>\n";
echo "<table class='boireaus' border='1' summary='Parameters of the Exam'>\n";

if($aff_nom_court=='y') {
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Short name&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name = 'nom_court' size='33' value = \"".$nom_court."\" onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else {
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Shorts name&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name = 'nom_court' size='33' value = \"".$nom_court."\" onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}


if($aff_nom_complet=='y') {
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Complete name&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name = 'nom_complet' size='33' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else {
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Complete name&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name = 'nom_complet' size='33' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

if($aff_description=='y') {
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<textarea name='description' rows='2' cols='40' >".$description."</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else {
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='description' value='$description' />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

if($aff_precision=='y') {
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Precision&nbsp;:</td>\n";
	echo "<td>\n";
	
		echo "<table>\n";
		$alt=1;
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='precision' id='precision_s1' value='s1' "; if ($precision=='s1') echo "checked"; echo " />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='precision_s1' style='cursor: pointer;'>";
		echo "Round to the tenth of higher point";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='precision' id='precision_s5' value='s5' "; if ($precision=='s5') echo "checked"; echo " />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='precision_s5' style='cursor: pointer;'>";
		echo "Round to the half-mark superior";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='precision' id='precision_se' value='se' "; if ($precision=='se') echo "checked"; echo " />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='precision_se' style='cursor: pointer;'>";
		echo "Round to higher whole mark";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='precision' id='precision_p1' value='p1' "; if ($precision=='p1') echo "checked"; echo " />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='precision_p1' style='cursor: pointer;'>";
		echo "Round to the tenth of more close mark";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='precision' id='precision_p5' value='p5' "; if ($precision=='p5') echo "checked"; echo " />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='precision_p5' style='cursor: pointer;'>";
		echo "Round to the most close half-mark";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='precision' id='precision_pe' value='pe' "; if ($precision=='pe') echo "checked"; echo " />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='precision_pe' style='cursor: pointer;'>";
		echo "Round to the whole nearest mark ";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";
}
else {
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Precision&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='precision' value='$precision' />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

echo "</table>\n";

if(isset($id_dev)) {
	echo "<input type='hidden' name='id_dev' value='$id_dev' />\n";
}
echo "<input type='hidden' name='id_racine' value='$id_racine' />\n";
echo "<input type=\"hidden\" name='is_posted' value=\"1\" />\n";

echo "<p align='center'><input type=\"submit\" name='ok' value=\"Register\" /></p>\n";

echo "</form>\n";
echo "<br />\n";

if($aff_nom_court=='y') {
	echo "<script type='text/javascript'>
	document.formulaire.nom_court.focus();
</script>\n";
}

/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>
