<?php
/* $Id: select_options.php 7357 2011-07-01 16:35:26Z crob $ */
/*
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/select_options.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/select_options.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genesis of the classes: Choice of the options',
statut='';";
$insert=mysql_query($sql);
}

//======================================================================================
// Section checkAccess() � d�commenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);
$choix_options=isset($_POST['choix_options']) ? $_POST['choix_options'] : NULL;
$lv1=isset($_POST['lv1']) ? $_POST['lv1'] : NULL;
$lv2=isset($_POST['lv2']) ? $_POST['lv2'] : NULL;
$lv3=isset($_POST['lv3']) ? $_POST['lv3'] : NULL;
$autre_option=isset($_POST['autre_option']) ? $_POST['autre_option'] : NULL;

if((isset($choix_options))&&((isset($lv1))||(isset($lv2))||(isset($lv3))||(isset($autre_option)))) {
	$nb_reg1=0;
	$nb_reg2=0;
	$nb_reg3=0;
	$nb_reg4=0;
	$nb_err=0;

	$sql="DELETE FROM gc_options WHERE projet='$projet';";
	//echo "$sql<br />";
	$suppr=mysql_query($sql);

	if(isset($lv1)) {
		$enregistrements_inseres=array();
		for($i=0;$i<count($lv1);$i++) {
			// Il faudrait contr�ler que les options sont valides et �viter certains caract�res.
			if(($lv1[$i]!="")&&(!in_array($lv1[$i],$enregistrements_inseres))) {
				$sql="INSERT INTO gc_options SET projet='$projet', opt='".$lv1[$i]."', type='lv1';";
				//echo "$sql<br />";
				if($insert=mysql_query($sql)) {$nb_reg1++;} else {$nb_err++;}
				$enregistrements_inseres[]=$lv1[$i];
			}
		}
	}

	if(isset($lv2)) {
		$enregistrements_inseres=array();
		for($i=0;$i<count($lv2);$i++) {
			// Il faudrait contr�ler que les options sont valides et �viter certains caract�res.
			if(($lv2[$i]!="")&&(!in_array($lv2[$i],$enregistrements_inseres))) {
				$sql="INSERT INTO gc_options SET projet='$projet', opt='".$lv2[$i]."', type='lv2';";
				//echo "$sql<br />";
				if($insert=mysql_query($sql)) {$nb_reg2++;} else {$nb_err++;}
				$enregistrements_inseres[]=$lv2[$i];
			}
		}
	}

	if(isset($lv3)) {
		$enregistrements_inseres=array();
		for($i=0;$i<count($lv3);$i++) {
			// Il faudrait contr�ler que les options sont valides et �viter certains caract�res.
			if(($lv3[$i]!="")&&(!in_array($lv3[$i],$enregistrements_inseres))) {
				$sql="INSERT INTO gc_options SET projet='$projet', opt='".$lv3[$i]."', type='lv3';";
				//echo "$sql<br />";
				if($insert=mysql_query($sql)) {$nb_reg3++;} else {$nb_err++;}
				$enregistrements_inseres[]=$lv3[$i];
			}
		}
	}

	if(isset($autre_option)) {
		$enregistrements_inseres=array();
		for($i=0;$i<count($autre_option);$i++) {
			// Il faudrait contr�ler que les options sont valides et �viter certains caract�res.
			if(($autre_option[$i]!="")&&(!in_array($autre_option[$i],$enregistrements_inseres))) {
				$sql="INSERT INTO gc_options SET projet='$projet', opt='".$autre_option[$i]."', type='autre';";
				//echo "$sql<br />";
				if($insert=mysql_query($sql)) {$nb_reg4++;} else {$nb_err++;}
				$enregistrements_inseres[]=$autre_option[$i];
			}
		}
	}

	if($nb_err==0) {
		$msg="Regeneration of the list of the options carried out: ";
		$msg.="$nb_reg1 LV1, $nb_reg2 LV2, $nb_reg3 LV3 et $nb_reg4 other recorded options.";
	}
	else {
		$msg="ERROR at the time of the regeneration of the list of the options: ";
		$msg.="$nb_reg1 LV1, $nb_reg2 LV2, $nb_reg3 LV3 et $nb_reg4 other recorded options.";
	}
}

//**************** EN-TETE *****************
$titre_page = "Genesis classifies: Options choice";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

if((!isset($projet))||($projet=="")) {
	echo "<p style='color:red'>ERROR: The project is not selected.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'>Return</a>";
echo "</p>\n";
//echo "</div>\n";

echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

echo "<table class='boireaus' summary='Table of the options'>\n";

$alt=1;
echo "<tr class='lig$alt'>\n";
echo "<td style='vertical-align:top;'>\n";
echo "LV1";
echo "</td>\n";
echo "<td>\n";
$cpt=0;
$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv1' ORDER BY opt;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "None LV1<br />\n";
}
else {
	//$cpt=0;
	while($lig=mysql_fetch_object($res)) {
		echo "<input type='checkbox' name='lv1[]' id='lv1_$cpt' value='$lig->opt' checked /><label for='lv1_$cpt'>$lig->opt</label><br />\n";
		$cpt++;
	}
}
//echo "<input type='hidden' name='lv1[]' id='lv1_ajoutee' value='' />\n";
echo "<a href='#' onclick=\"afficher_div('ajout_lv1','y',100,100);\">Add</a>";

$titre="Ajout LV1";
$texte_checkbox_matieres="";
$sql="SELECT matiere FROM matieres ORDER BY matiere;";
$res=mysql_query($sql);
if(mysql_num_rows($res)>0) {
	//$cpt=0;
	while($lig=mysql_fetch_object($res)) {
		$texte_checkbox_matieres.="<input type='checkbox' name='lv1[]' id='lv1_$cpt' value='$lig->matiere' /><label for='lv1_$cpt'>$lig->matiere</label><br />";
		$cpt++;
	}
}
//$tabdiv_infobulle[]=creer_div_infobulle('ajout_lv1',$titre,"",$texte,"",35,0,'y','y','n','n');
echo creer_div_infobulle('ajout_lv1',$titre,"",$texte_checkbox_matieres,"",20,20,'y','y','n','y');

echo "</td>\n";
echo "</tr>\n";
//===============================================================================
$alt=$alt*(-1);
echo "<tr class='lig$alt'>\n";
echo "<td style='vertical-align:top;'>\n";
echo "LV2";
echo "</td>\n";
echo "<td>\n";
$cpt=0;
$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv2' ORDER BY opt;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "None LV2<br />\n";
}
else {
	//$cpt=0;
	while($lig=mysql_fetch_object($res)) {
		echo "<input type='checkbox' name='lv2[]' id='lv2_$cpt' value='$lig->opt' checked /><label for='lv2_$cpt'>$lig->opt</label><br />\n";
		$cpt++;
	}
}
//echo "<input type='hidden' name='lv1[]' id='lv1_ajoutee' value='' />\n";
echo "<a href='#' onclick=\"afficher_div('ajout_lv2','y',100,100);\">Add</a>";

$titre="Ajout LV2";
$texte_checkbox_matieres="";
$sql="SELECT matiere FROM matieres ORDER BY matiere;";
$res=mysql_query($sql);
if(mysql_num_rows($res)>0) {
	//$cpt=0;
	while($lig=mysql_fetch_object($res)) {
		$texte_checkbox_matieres.="<input type='checkbox' name='lv2[]' id='lv2_$cpt' value='$lig->matiere' /><label for='lv2_$cpt'>$lig->matiere</label><br />";
		$cpt++;
	}
}
echo creer_div_infobulle('ajout_lv2',$titre,"",$texte_checkbox_matieres,"",20,20,'y','y','n','y');

echo "</td>\n";
echo "</tr>\n";
//===============================================================================
$alt=$alt*(-1);
echo "<tr class='lig$alt'>\n";
echo "<td style='vertical-align:top;'>\n";
echo "LV3";
echo "</td>\n";
echo "<td>\n";
$cpt=0;
$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv3' ORDER BY opt;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "Aucune lv3<br />\n";
}
else {
	//$cpt=0;
	while($lig=mysql_fetch_object($res)) {
		echo "<input type='checkbox' name='lv3[]' id='lv3_$cpt' value='$lig->opt' checked /><label for='lv3_$cpt'>$lig->opt</label><br />\n";
		$cpt++;
	}
}
//echo "<input type='hidden' name='lv1[]' id='lv1_ajoutee' value='' />\n";
echo "<a href='#' onclick=\"afficher_div('ajout_lv3','y',100,100);\">Add</a>";

$titre="Ajout lv3";
$texte_checkbox_matieres="";
$sql="SELECT matiere FROM matieres ORDER BY matiere;";
$res=mysql_query($sql);
if(mysql_num_rows($res)>0) {
	//$cpt=0;
	while($lig=mysql_fetch_object($res)) {
		$texte_checkbox_matieres.="<input type='checkbox' name='lv3[]' id='lv3_$cpt' value='$lig->matiere' /><label for='lv3_$cpt'>$lig->matiere</label><br />";
		$cpt++;
	}
}
echo creer_div_infobulle('ajout_lv3',$titre,"",$texte_checkbox_matieres,"",20,20,'y','y','n','y');

echo "</td>\n";
echo "</tr>\n";
//===============================================================================
$alt=$alt*(-1);
echo "<tr class='lig$alt'>\n";
echo "<td style='vertical-align:top;'>\n";
echo "Another option";
echo "</td>\n";
echo "<td>\n";
$cpt=0;
$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='autre' ORDER BY opt;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "No other option<br />\n";
}
else {
	//$cpt=0;
	while($lig=mysql_fetch_object($res)) {
		echo "<input type='checkbox' name='autre_option[]' id='autre_option_$cpt' value='$lig->opt' checked /><label for='autre_option_$cpt'>$lig->opt</label><br />\n";
		$cpt++;
	}
}
//echo "<input type='hidden' name='lv1[]' id='lv1_ajoutee' value='' />\n";
echo "<a href='#' onclick=\"afficher_div('ajout_autre_option','y',100,100);\">Add</a>";

$titre="Add other option";
$texte_checkbox_matieres="";
//$cpt=0;
$texte_checkbox_matieres.="<input type='text' name='autre_option[]' id='autre_option_$cpt' value='' /><br />\n";
$cpt++;
$sql="SELECT matiere FROM matieres ORDER BY matiere;";
$res=mysql_query($sql);
if(mysql_num_rows($res)>0) {
	while($lig=mysql_fetch_object($res)) {
		$texte_checkbox_matieres.="<input type='checkbox' name='autre_option[]' id='autre_option_$cpt' value='$lig->matiere' /><label for='autre_option_$cpt'>$lig->matiere</label><br />\n";
		$cpt++;
	}
}
echo creer_div_infobulle('ajout_autre_option',$titre,"",$texte_checkbox_matieres,"",20,20,'y','y','n','y');

echo "</td>\n";
echo "</tr>\n";
//===============================================================================


echo "</table>\n";

echo "<input type='hidden' name='projet' value='$projet' />\n";
echo "<p><input type='submit' name='choix_options' value='Validate' /></p>\n";
echo "</form>\n";

//echo "</blockquote>\n";

echo "<p><em>NOTES&nbsp;:</em></p>\n";
echo "<ul>\n";
echo "<li>It is possible to impose constraints to indicate that one does not
want LATIN in �2 and 3B2 (<i>the student making LATIN could then be notched in all the columns
except �2 and 3B2</i>).<br />
For as much, the solution by exclusion of such option on such class is
not always enough.<br />
It can be convenient to create options like z_3B1, z_3B2,... for the student who should not be put in another class.<br >
By combining the option z_3B1 with an exclusion of the type not of
option z_3B1 in the classes other than 3B1 you will be able to add
constraints differently not g�rables.</li>\n";
echo "<li>To create an option Z_XXX for the dubious student (<i>announced departure but not confirmed,...</i>) allows to locate quickly if one distributed the dubious ones well on
the various classes.</li>\n";
echo "</ul>\n";

require("../lib/footer.inc.php");
?>
