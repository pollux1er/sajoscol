<?php
/*
* $Id: modify_etab.php 5912 2010-11-20 10:37:24Z crob $
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

$champs_vides = "non";
if (isset($is_posted) and ($is_posted == '1')) {
	check_token();
	if (($id != '') and ($nom_etab != '') and ($niveau_etab != '') and ($type_etab != '') and ($cp_etab != '') ) {
		$call_test = mysql_query("SELECT * FROM etablissements WHERE id = '$id'");
		$count = mysql_num_rows($call_test);

		// CORRECTION DU CONTENU DES CHAMPS: on interdit les guillemets,...
		$id=my_ereg_replace("[^A-Za-z0-9]*","",$id);
		$nom_etab=strtr($nom_etab,'"',' ');
		$cp_etab=my_ereg_replace("[^0-9]*","",$cp_etab);
		$ville_etab=strtr($ville_etab,'"',' ');

		if ($count == "0") {
			$register_etab = mysql_query("INSERT INTO etablissements SET id = '".$id."', nom='".$nom_etab."', niveau='".$niveau_etab."', type='".$type_etab."', cp= '".$cp_etab."', ville= '".$ville_etab."'");
			if (!$register_etab) {
				$msg = "An error occurred during the recording of the new school.";
			} else {
				$msg = "The new school was recorded.";
			}
		} else {
			if ($nouvel_etab == 'no') {
				$register_etab = mysql_query("UPDATE etablissements SET nom='".$nom_etab."', niveau='".$niveau_etab."', type='".$type_etab."', cp= '".$cp_etab."', ville= '".$ville_etab."' WHERE id = '".$id."'");
				if (!$register_etab) {
					$msg = "An error occurred during the modification of the school.";
					} else {
					$msg = "The card school was indeed modified.";
				}
			} else {
				$msg = "A school having same identifier RNE already exists in the base.Impossible recording !";
				$id = '';
			}
		}
	} else {
		$msg = "One or more fields are empty!";
		$champs_vides = "oui";
	}
}

//**************** EN-TETE *******************************
$titre_page = "Management of the schools | Add, modify a school";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ***************************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>
</p>

<?php
if ((isset($id)) and ($champs_vides == "non")) {
	$call_data = mysql_query("SELECT * FROM etablissements WHERE id = '$id'");
	$nom_etab = @mysql_result($call_data, 0, "nom");
	$niveau_etab = @mysql_result($call_data, 0, "niveau");
	$type_etab = @mysql_result($call_data, 0, "type");
	$cp_etab = @mysql_result($call_data, 0, "cp");
	$ville_etab = @mysql_result($call_data, 0, "ville");
}

if (!isset($nom_etab)) $nom_etab='';
if (!isset($niveau_etab)) $niveau_etab='aucun';
if (!isset($cp_etab)) $cp_etab='';
if (!isset($ville_etab)) $ville_etab='';
if (!isset($type_etab)) $type_etab='aucun';

?>
<form enctype="multipart/form-data" action="modify_etab.php" method="post">
<?php
echo add_token_field();
?>
<div class='norme'>
<table>
<?php
if (!(isset($id)) or ($id == '')) {
	echo "<tr><td>Identifier of the school : </td><td><input type=text size=30 name=id value=\"\" />\n";
	echo "<input type='hidden' name='nouvel_etab' value='yes' /></td></tr>\n";
} else {
	echo "<tr><td>Identifier RNE of the school : $id</td>\n";
	echo "<td><input type='hidden' name='id' value='$id' />\n";
	echo "<input type='hidden' name='nouvel_etab' value='no' /></td></tr>\n";
}
?>
<!--div class='norme'-->
<!--tr><td>Nom de l'établissement : </td><td><input type='text' size='30' name='nom_etab' value='"<?php echo $nom_etab; ?>"'></input></td></tr-->
<tr><td>Name of the school : </td><td><input type='text' size='30' name='nom_etab' value="<?php echo $nom_etab; ?>"></input></td></tr>
<tr><td>Level : </td><td><select name='niveau_etab' size='1'>
<?php
foreach ($type_etablissement as $type => $nom_etab) {
	echo "<option value=\"".$type."\" ";
	if ($niveau_etab == $type) { echo " selected ";}
	echo ">";
	if ($nom_etab != '') echo $nom_etab; else echo "(empty)";
	echo "</option>\n";
}
?>
</select></td></tr>
<tr><td>Type : </td><td><SELECT name=type_etab size=1>
<option value='public' <?php if ($type_etab == "public") { echo "selected";}?>>Public</option>
<option value='prive' <?php if ($type_etab == "prive") { echo "selected";}?>>Private</option>
<option value='aucun' <?php if ($type_etab == "aucun") { echo "selected";}?>>(empty)</option>
</select>
</td></tr>

<!--tr><td>Code postal : </td><td><input type='text' size='6' name='cp_etab' value='"<?php echo $cp_etab; ?>"'></input></td></tr-->
<tr><td>Code postal : </td><td><input type='text' size='6' name='cp_etab' value="<?php echo $cp_etab; ?>" /></td></tr>
<!--tr><td>Ville : </td><td><input type='text' size='20' name='ville_etab' value='"<?php echo $ville_etab; ?>"'></input></td></tr-->
<tr><td>Ville : </td><td><input type='text' size='20' name='ville_etab' value="<?php echo $ville_etab; ?>" /></td></tr>
</table>
</div>
<input type='hidden' name='is_posted' value='1' />
<input type='submit' value='Save' />
</form>
<?php require("../lib/footer.inc.php");?>