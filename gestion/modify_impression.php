<?php
/*
 * $Id: modify_impression.php 5928 2010-11-21 10:47:40Z crob $
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}
include("../fckeditor/fckeditor.php") ;

if(!isset($msg)){$msg="";}

if (isset($_POST['ok'])) {
	check_token();
	$error = false;

	if	(isset($_POST['impression_personnelFCK'])) {
		$imp = html_entity_decode_all_version($_POST['impression_personnelFCK']);
		if (!saveSetting("Impression", $imp)) {
			$msg .= "Error during the recording of the welcome card for the personnel !";
			$erreur = true;
		}
    }

    if	(isset($_POST['impression_parentFCK'])) {
		$imp = html_entity_decode_all_version($_POST['impression_parentFCK']);
		if (!saveSetting("ImpressionFicheParent", $imp)) {
			$msg .= "Error during the recording of the welcome card for ".$gepiSettings['denomination_responsables']." !";
			$erreur = true;
		}
    }

    if (isset($_POST['impression_eleveFCK'])) {
		$imp = html_entity_decode_all_version($_POST['impression_eleveFCK']);
		if (!saveSetting("ImpressionFicheEleve", $imp)) {
			$msg .= "Error during the recording of the welcome card for ".$gepiSettings['denomination_eleves']." !";
			$erreur = true;
		}
    }


    $nb = isset($_POST['nb_impression']) ? (is_numeric($_POST['nb_impression']) ? $_POST['nb_impression'] : "1") : 1;
    if (!saveSetting("ImpressionNombre", $nb)) {
    	$error = true;
    }
    $nb = isset($_POST['nb_impression_parent']) ? (is_numeric($_POST['nb_impression_parent']) ? $_POST['nb_impression_parent'] : "1") : 1;
    if (!saveSetting("ImpressionNombreParent", $nb)) {
    	$error = true;
    }
    $nb = isset($_POST['nb_impression_eleve']) ? (is_numeric($_POST['nb_impression_eleve']) ? $_POST['nb_impression_eleve'] : "1") : 1;
    if (!saveSetting("ImpressionNombreEleve", $nb)) {
    	$error = true;
    }

    if (!$error) {
    	$msg = "The parameters were recorded.";
    }
}
//**************** EN-TETE *****************
$titre_page = "Management tool | Impression of the parameters";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();
?>
<form enctype="multipart/form-data" action="modify_impression.php" method=post name=formulaire>
<p class=bold><a href="index.php#modify_impression"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return </a>|<a href="modify_impression.php?fiche=personnels"> Card School Employee</a>|<a href="modify_impression.php?fiche=responsables"> Fiche <?php echo $gepiSettings['denomination_responsables']; ?></a>|<a href="modify_impression.php?fiche=eleves"> Fiche <?php echo $gepiSettings['denomination_eleves'];?> </a></p>

<?php

if (!loadSettings()) {
    die("Error loading settings");
}

echo add_token_field();

echo "<br />";
echo "<p>During the creation of a user, it is possible for you to print a sheet of information containing the parameters of connection to GEPI, the text differs according to the statute from the user created.
Attention, this text is in format HTML !</p>\n";

$fiche=isset($_POST["fiche"]) ? $_POST["fiche"] : (isset($_GET["fiche"]) ? $_GET["fiche"] : "personnels");

//echo "<table width=600>\n";
//echo "<tr>\n<td>\n";
echo "<div style='width: 600px;'>\n";

switch ($fiche) {
case 'personnels' :
		$impression = getSettingValue("Impression");
		$nb_impression = getSettingValue("ImpressionNombre");

		echo "<h3 class='gepi' align='center'>Card-index information: Personnel of the school</h3>\n";
		echo "<p>This card is printed during the creation of a new user to the statute 'professeur', 'cpe', 'scolarite' .</p>\n";
		echo "<p>Number of cards to be printed by page : \n";
		echo "<select name='nb_impression' size='1'>\n";
		for ($i=1;$i<25;$i++) {
			echo "<option value='$i'";
			if ($nb_impression == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<input type=\"hidden\" name=\"fiche\" value=\"$fiche\" />\n";
		echo "<br />Council: make tests to avoid nasty surprises during the impression in mass.</p>\n";
		echo "<br /><i>Working of the message :</i>\n";

		$oFCKeditor = new FCKeditor('impression_personnelFCK') ;
		$oFCKeditor->BasePath = '../fckeditor/' ;
		$oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
		$oFCKeditor->ToolbarSet = 'Basic' ;
		$oFCKeditor->Value      = $impression ;
		$oFCKeditor->Create() ;

		//echo "</div>\n";
    break;

case 'responsables' :
		$impression_parent = getSettingValue("ImpressionFicheParent");
		$nb_impression_parent = getSettingValue("ImpressionNombreParent");

		echo "<h3 class='gepi' align='center'>Card-index information : ".$gepiSettings['denomination_responsables']."</h3>\n";
		echo "<p>This card is printed during the creation of a new user of the statute 'responsable'.</p>\n";
		echo "<p>Number of cards to be printed by page : \n";
		echo "<select name='nb_impression_parent' size='1'>\n";
		for ($i=1;$i<25;$i++) {
			echo "<option value='$i'";
			if ($nb_impression_parent == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<input type=\"hidden\" name=\"fiche\" value=\"$fiche\" />\n";
		echo "<br />The Council : make tests to avoid nasty surprises during the impression in mass.</p>\n";
		echo "<br /><i>Mise en forme du message :</i>\n";

		$oFCKeditor = new FCKeditor('impression_parentFCK') ;
		$oFCKeditor->BasePath = '../fckeditor/' ;
		$oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
		$oFCKeditor->ToolbarSet = 'Basic' ;
		$oFCKeditor->Value      = $impression_parent ;
		$oFCKeditor->Create() ;

		//echo "</div>\n";
    break;

case 'eleves' :

		$impression_eleve = getSettingValue("ImpressionFicheEleve");
		$nb_impression_eleve = getSettingValue("ImpressionNombreEleve");

		echo "<h3 class='gepi' align='center'>Card-index information : ".$gepiSettings['denomination_eleves']."</h3>\n";
		echo "<p>This card is printed during the creation of a new user of the statute 'eleve'.</p>\n";
		echo "<p>Number of cards to be printed by page : \n";
		echo "<select name='nb_impression_eleve' size='1'>\n";
		for ($i=1;$i<25;$i++) {
			echo "<option value='$i'";
			if ($nb_impression_eleve == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<input type=\"hidden\" name=\"fiche\" value=\"$fiche\" />\n";
		echo "<br />Council : make tests to avoid nasty surprises during the impression in mass.</p>\n";
		echo "<br /><i>Working of the message :</i>\n";

		$oFCKeditor = new FCKeditor('impression_eleveFCK') ;
		$oFCKeditor->BasePath = '../fckeditor/' ;
		$oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
		$oFCKeditor->ToolbarSet = 'Basic' ;
		$oFCKeditor->Value      = $impression_eleve ;
		$oFCKeditor->Create() ;

		//echo "</div>\n";
	break;
}
echo "<input type='submit' name=\"ok\" value='Save' />\n";

echo "<br /><br />\n";
echo "<b><a href=\"./modele_fiche_information.php?fiche=$fiche\" target='_blank' >Preview of the card of information</a></b><br />\n";
echo "<i>Caution&nbsp;:</i> page-setting of the cards is very different to the screen and the impression.";
echo "Take care to use the function \"print preview\" in order to see the result.<br />\n";

//echo "</td>\n</tr>\n";
//echo "</table>\n";
echo "</div>\n";

if($fiche=="responsables") {
	echo "<br />\n";
	echo "<p><b>CAUTION&nbsp;:</b> Dimensions and positioning of the block addresses responsibles are
those of ";
	if(getSettingValue('GepiAdminImprBulSettings')=='yes') {
		echo "<a href='../bulletins/param_bull.php'>Parameter setting of impression of bulletins HTML</a>";
	}
	else {
		echo "Parameter setting of impression of bulletins HTML";
	}
	echo ".</p>\n";

	$addressblock_padding_right=getSettingValue("addressblock_padding_right");
	$addressblock_padding_top=getSettingValue("addressblock_padding_top");
	$addressblock_padding_text=getSettingValue("addressblock_padding_text");
	$addressblock_length=getSettingValue("addressblock_length");

	echo "<p style='margin-left: 3em;'>\n";
	echo "Space between the right margin of the sheet and the block 'adresse' : ".$addressblock_padding_right."&nbsp;mm\n";
	if($addressblock_padding_right>200) {echo " <span style='color='red'>This value appears high. Is there an error?</span>";}
	echo "<br />\n";
	echo "Space between the high margin of the sheet and the block 'adresse' : ".$addressblock_padding_top."&nbsp;mm\n";
	if($addressblock_padding_top>290) {echo " <span style='color='red'>Been worth This appears high. Is there an error?</span>";}
	echo "<br />\n";
	echo "Vertical space between the block 'adresse' and the block of the results : ".$addressblock_padding_text."&nbsp;mm\n";
	if($addressblock_padding_text>100) {echo " <span style='color='red'>Been worth This appears high. Is there an error?</span>";}
	echo "<br />\n";
	echo "Length of the block 'adresse' : ".$addressblock_length."&nbsp;mm\n";
	if($addressblock_length>170) {echo " <span style='color='red'>Been worth This appears high. Is there an error?</span>";}
	echo "<br />\n";
	echo "</p>\n";

	echo "<p>If you must make corrections, it is in ' Parameter setting of impression of bulletins HTML' that occurs.</p>\n";
}

?>
</form>
<br /><br />
<?php require("../lib/footer.inc.php");?>