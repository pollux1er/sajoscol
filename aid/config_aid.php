<?php
/*
 * @version: $Id: config_aid.php 6588 2011-03-02 17:53:54Z crob $
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


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
// ========== Iniialisation des variables ==========
$reg_nom = isset($_POST["reg_nom"]) ? $_POST["reg_nom"] : NULL;
$reg_nom_complet = isset($_POST["reg_nom_complet"]) ? $_POST["reg_nom_complet"] : NULL;
$note_max = isset($_POST["note_max"]) ? $_POST["note_max"] : NULL;
$display_begin = isset($_POST["display_begin"]) ? $_POST["display_begin"] : NULL;
$display_end = isset($_POST["display_end"]) ? $_POST["display_end"] : NULL;
$type_note = isset($_POST["type_note"]) ? $_POST["type_note"] : NULL;
$order_display1 = isset($_POST["order_display1"]) ? $_POST["order_display1"] : NULL;
$order_display2 = isset($_POST["order_display2"]) ? $_POST["order_display2"] : NULL;
$message = isset($_POST["message"]) ? $_POST["message"] : NULL;
$display_nom = isset($_POST["display_nom"]) ? $_POST["display_nom"] : NULL;
$indice_aid = isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : (isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : NULL);
$display_bulletin = isset($_POST["display_bulletin"]) ? $_POST["display_bulletin"] : 'n';
$autoriser_inscript_multiples = isset($_POST["autoriser_inscript_multiples"]) ? $_POST["autoriser_inscript_multiples"] : 'n';
$bull_simplifie = isset($_POST["bull_simplifie"]) ? $_POST["bull_simplifie"] : 'n';
$activer_outils_comp = isset($_POST["activer_outils_comp"]) ? $_POST["activer_outils_comp"] : 'n';
$feuille_presence = isset($_POST["feuille_presence"]) ? $_POST["feuille_presence"] : 'n';
$is_posted = isset($_POST["is_posted"]) ? $_POST["is_posted"] : NULL;
// ========== fin initialisation ===================

if (isset($is_posted) and ($is_posted == "1")) {
  check_token();
  $msg_inter = "";
  if ($autoriser_inscript_multiples != 'y') {
    $test = sql_query1("select count(login) c from j_aid_eleves where indice_aid='".$indice_aid."' group by login order by c desc limit 1");
    if ($test > 1) {
      $msg_inter = "Currently, one or more pupils are registered in several IDA at the same time.
      Impossible thus to remove the authorization to register one student in several IDA of a same category.";
      $autoriser_inscript_multiples = 'y';
    }
  }
	if ($display_end < $display_begin) {$display_end = $display_begin;}
	$del = mysql_query("DELETE FROM aid_config WHERE indice_aid = '".$indice_aid."'");
	echo "<!-- DELETE FROM aid_config WHERE indice_aid = '".$indice_aid."' -->";
	$reg_data = mysql_query("INSERT INTO aid_config SET
			nom='".$reg_nom."',
			nom_complet='".$reg_nom_complet."',
			note_max='".$note_max."',
			display_begin='".$display_begin."',
			display_end='".$display_end."',
			type_note='".$type_note."',
			order_display1 = '".$order_display1."',
			order_display2 = '".$order_display2."',
			message ='".$message."',
			display_nom='".$display_nom."',
			indice_aid='".$indice_aid."',
			display_bulletin='".$display_bulletin."',
			autoriser_inscript_multiples='".$autoriser_inscript_multiples."',
			bull_simplifie = '".$bull_simplifie."',
			feuille_presence = '".$feuille_presence."',
			outils_complementaires = '".$activer_outils_comp."'");
	  if (!$reg_data)
		  $msg_inter .= "Erreur lors de l'enregistrement des données !<br />";

		// Suppression de professeurs dans le cas des outils complémentaire
		$call_profs = mysql_query("SELECT id_utilisateur FROM j_aidcateg_utilisateurs WHERE (indice_aid='$indice_aid')");
		$nb_profs = mysql_num_rows($call_profs);
		$i = 0;
		while($i < $nb_profs) {
		    $login_prof = mysql_result($call_profs,$i);
		    if (isset($_POST["delete_".$login_prof])) {
		        $reg_data = mysql_query("delete from j_aidcateg_utilisateurs WHERE (id_utilisateur = '$login_prof' and indice_aid='$indice_aid')");
            if (!$reg_data) $msg_inter .= "Error during the removal of the professor $login_prof!<br />";
		    }
		    $i++;
		} // while
    if (isset($_POST["reg_prof_login"]) and ($_POST["reg_prof_login"] !="")) {
        // On commence par vérifier que le professeur n'est pas déjà présent dans cette liste.
        $test = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_utilisateurs WHERE (id_utilisateur = '$reg_prof_login' and indice_aid='$indice_aid')");
        if ($test != "0") {
            $msg = "The professor that you tried to add belongs already to this IDA";
        } else {
            $reg_data = mysql_query("INSERT INTO j_aidcateg_utilisateurs SET id_utilisateur= '".$_POST["reg_prof_login"]."', indice_aid='".$indice_aid."'");
            if (!$reg_data) $msg_inter .= "Error during the addition of the professor !<br />";
        }
    }
		// Suppression de "super-gestionaires"
		$call_profs = mysql_query("SELECT id_utilisateur FROM j_aidcateg_super_gestionnaires WHERE (indice_aid='$indice_aid')");
		$nb_profs = mysql_num_rows($call_profs);
		$i = 0;
		while($i < $nb_profs) {
		    $login_gestionnaire = mysql_result($call_profs,$i);
		    if (isset($_POST["delete_gestionnaire_".$login_gestionnaire])) {
		        $reg_data = mysql_query("delete from j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '$login_gestionnaire' and indice_aid='$indice_aid')");
            if (!$reg_data) $msg_inter .= "Error during the removal of the professor $login_gestionnaire!<br />";
		    }
		    $i++;
		} // while


    if (isset($_POST["reg_gestionnaire_login"]) and ($_POST["reg_gestionnaire_login"] !="")) {
        // On commence par vérifier que le professeur n'est pas déjà présent dans cette liste.
        $test = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '$reg_gestionnaire_login' and indice_aid='$indice_aid')");
        if ($test != "0") {
            $msg = "The professor that you tried to add belongs already to this IDA";
        } else {
            $reg_data = mysql_query("INSERT INTO j_aidcateg_super_gestionnaires SET id_utilisateur= '".$_POST["reg_gestionnaire_login"]."', indice_aid='".$indice_aid."'");
            if (!$reg_data) $msg_inter .= "Error during the addition of the professor !<br />";
        }
    }


    if ($msg_inter !="") {
        $msg = $msg_inter;
    } else {
        $msg = "Successful recording !";
    }
}


//**************** EN-TETE *********************
$titre_page = "Gestion des AID";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>

<script type="text/javascript" language="javascript">
var errorMsg0  = 'The form is incomplete !';
var errorMsg1  = ' please enter a number ! ';
var errorMsg2  = ' : this value is not authorized ! ';
function mise_a_zero() {
    window.document.formulaire.note_max.value = '';
}

function verif_type_note() {
    if (window.document.formulaire.type_note[2].checked == true) {
        window.document.formulaire.note_max.value = '';
    }
    if (window.document.formulaire.type_note[2].checked != true && window.document.formulaire.note_max.value == '')
        {
            window.document.formulaire.note_max.value = '20';
        }
}

//=================================
// AJOUT: boireaus
function emptyFormElements(formulaire,champ){
	//eval("document.forms['"+formulaire+"']."+champ+".value=''");
	// J'ai viré la ligne parce qu'elle vide le champ avant que la valeur soit transmise
	// et du coup on insère dans la table des noms vides.
	return true;
}

function checkFormElementInRange(formulaire,champ,vmin,vmax){
	eval("vchamp=document.forms['"+formulaire+"']."+champ+".value");
	chaine_reg=new RegExp('[0-9]+');
	if((vchamp<0)||(vchamp>100)||(vchamp.replace(chaine_reg,'')).length!=0){
		alert("The value of the field "+champ+" ("+vchamp+") does not lie between 0 and 100.");
		return false;
	}
	else{
		return true;
	}
}
//=================================

</script>

<?php
if (isset($indice_aid)) {
    $call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
    $reg_nom = @mysql_result($call_data, 0, "nom");
    $reg_nom_complet = @mysql_result($call_data, 0, "nom_complet");
    $note_max = @mysql_result($call_data, 0, "note_max");
    $order_display1 = @mysql_result($call_data, 0, "order_display1");
    $order_display2 = @mysql_result($call_data, 0, "order_display2");
    $type_note = @mysql_result($call_data, 0, "type_note");
    $display_begin = @mysql_result($call_data, 0, "display_begin");
    $display_end = @mysql_result($call_data, 0, "display_end");
    $message = @mysql_result($call_data, 0, "message");
    $display_nom = @mysql_result($call_data, 0, "display_nom");
    $display_bulletin = @mysql_result($call_data, 0, "display_bulletin");
    $autoriser_inscript_multiples = @mysql_result($call_data, 0, "autoriser_inscript_multiples");
    $bull_simplifie = @mysql_result($call_data, 0, "bull_simplifie");
    $activer_outils_comp = @mysql_result($call_data, 0, "outils_complementaires");
    $feuille_presence = @mysql_result($call_data, 0, "feuille_presence");
    // Compatibilité avec version
    if ($display_bulletin=='')  $display_bulletin = "y";
    if ($autoriser_inscript_multiples=='')  $autoriser_inscript_multiples = "n";
} else {
    $call_data = mysql_query("SELECT max(indice_aid) max FROM aid_config");
    $indice_aid = @mysql_result($call_data, 0, "max");
    $indice_aid++;
    $note_max = 20;
    $display_begin = '';
    $display_end = '';
    $display_nom = '';
    $message = '';
    $order_display1 = '';
    $order_display2 = '';
    $type_note = "every";
    $display_bulletin = "y";
    $autoriser_inscript_multiples = "n";
    $bull_simplifie = "y";
    $activer_outils_comp = "n";
    $feuille_presence = "n";
}
?>

<!--form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements(this, 'reg_nom_complet') && (emptyFormElements(this, 'reg_nom')) && checkFormElementInRange(this, 'order_display2', 0, 100))"-->
<!--form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements(this, 'reg_nom_complet') &amp;&amp; (emptyFormElements(this, 'reg_nom')) &amp;&amp;s checkFormElementInRange(this, 'order_display2', 0, 100))"-->
<!--form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements('formulaire', 'reg_nom_complet') && (emptyFormElements('formulaire', 'reg_nom')) && checkFormElementInRange('formulaire', 'order_display2', 0, 100))"-->
<form enctype="multipart/form-data" name="formulaire" action="config_aid.php" method="post" onsubmit="return (emptyFormElements('formulaire', 'reg_nom_complet') &amp;&amp; (emptyFormElements('formulaire', 'reg_nom')) &amp;&amp; checkFormElementInRange('formulaire', 'order_display2', 0, 100))">

<?php
echo add_token_field();
?>
<div class='norme'><p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>

<input type="submit" value="Save" /><br />

<br /><b>Configuration of the IDA (Interdisciplinary Activities) :</b>

<hr />

Choose the complete name of the IDA (for example Framed Personal Work):

<br />Complete name : <input type="text" name="reg_nom_complet" size="40" <?php if (isset($reg_nom_complet)) { echo "value=\"".$reg_nom_complet."\"";}?> />

<br /><br />Choose the shortened name of the IDA (for example F.P.W.) :

<br />Name : <input type="text" name="reg_nom" size="20" <?php if (isset($reg_nom)) { echo "value=\"".$reg_nom."\"";}?> />

<hr />

Type of notation :  <br />

<input type="radio" name="type_note" value="every" <?php if (($type_note == "every") or ($type_note == "")) { echo ' checked="checked"';} ?> /> A note for each period

<input type="radio" name="type_note" value="last" <?php if ($type_note == "last") { echo ' checked="checked"';} ?> /> A note only for the last period

<input type="radio" name="type_note" value="no" <?php if ($type_note == "no") { echo ' checked="checked"';} ?> onclick="mise_a_zero()" /> No note

<hr />



<?php

$query_max_periode = mysql_query("SELECT max(num_periode) max FROM periodes");

$max_periode = mysql_result($query_max_periode, 0, "max")+1;

echo "Durée de l'AID : ";

if ($max_periode == '1') {

   echo " <font color='red'>Attention, no period is currently defined (start by creating one or more classes over one or more periods).</font>";

   $max_periode = '2';

} echo "<br /> The ida begins at the period";

echo "<SELECT name=\"display_begin\">";

$i = 1;

while ($i < $max_periode) {

    echo "<option"; if ($display_begin==$i) {echo ' selected="selected"';} echo ">$i";

    $i++;

}

?>

</SELECT>

(included) until the period

<SELECT name="display_end">

<?php

$i = 1;

while ($i < $max_periode) {

    echo "<option"; if ($display_end==$i) {echo ' selected="selected"';} echo ">$i";

    $i++;

}

?>

</SELECT>

(included).



<hr />

Choose if necessary the maximum note on which the IDA is noted:

<br />Maximum Note: <input type="text" name="note_max" size="20" <?php if ($note_max) { echo "value=\"".$note_max."\"";}?> onBlur="verif_type_note()" />

<hr />

In the final bulletin, the complete title appears and precedes the appreciation in the box appreciation :<br />

<input type="radio" name="display_nom" value="y" <?php if (($display_nom == "y") or ($display_nom == "")) { echo ' checked="checked"';} ?> /> Yes

<input type="radio" name="display_nom" value="n" <?php if ($display_nom == "n") { echo ' checked="checked"';} ?> /> No

<hr />

In the final bulletin, the following message precedes the complete title in the box appreciation :<br />

<input type="text" name="message" size="40" maxlength="40" <?php if ($message) { echo "value=\"".$message."\"";}?> /><br />
<span style='font-size:small;'>(This message will take place in the box appreciation on the bulletin)</span>

<hr />

<p>Place of the box reserved for this aid in the final bulletin :</p>
<p>
<input type="radio" id="orderDisplay1Y" name="order_display1" value="b" <?php if (($order_display1 == "b") or (!$order_display1)) { echo ' checked="checked"';;} ?> />
<label for="orderDisplay1Y"> At the beginning of the bulletin</label>
<input type="radio"id="orderDisplay1N" name="order_display1" value="e" <?php if ($order_display1 == "e") { echo ' checked="checked"';;} ?> />
<label for="orderDisplay1N"> At the end of the bulletin</label>
</p>





<br />

Position compared to the others aid (enter a number between 1 and 100):

<input type="text" name="order_display2" size="1" <?php if (isset($order_display2)) { echo "value=\"".$order_display2."\"";}?> />

<hr />

<p><b>Display :  </b></p>
<p>
<input type="checkbox" id="display_Report card" name="display_bulletin" value="y" <?php if ($display_bulletin == "y") { echo ' checked="checked"';} ?> />
<label for="display_Report card">The IDA appears in the official bulletin</label>
</p>
<p>
<input type="checkbox" id="bullSimplifie" name="bull_simplifie" value='y' <?php if ($bull_simplifie == "y") { echo ' checked="checked"';} ?> />
<label for="bullSimplifie">The IDA appears in the simplified bulletin .</label>
</p>

<hr />

<p><b>Multiple inscriptions :  </b></p>
<p>
By defect, a student cannot be registered in more than one IDA by category of IDA.
<br />However, in certain cases, it can be useful to authorize the inscription of one student in several IDA of a same category.</p>
<input type="checkbox" id="autoriser_inscript_multiples" name="autoriser_inscript_multiples" value="y" <?php if ($autoriser_inscript_multiples == "y") { echo ' checked="checked"';} ?> />
<label for="autoriser_inscript_multiples">Authorize multiple inscriptions</label>
</p>

<hr />
<?php
// si le plugin "gestion_autorisations_publications" existe et est activé, on exclue la rubrique correspondante
$test_plugin = sql_query1("select ouvert from plugins where nom='gestion_autorisations_publications'");

if ((getSettingValue("active_mod_gest_aid")=="y") and ($test_plugin=='y') and (getSettingValue("indice_aid_autorisations_publi") != $indice_aid)) {
?>
<p><b>Addition/suppression of "super-managers"</b></p>
<p>In addition to the professors in charge for each AID, you can indicate users below (professors or CPE) having the right of manage IDA of this category (addition, suppression, modification of IDA, professors or Student)</p>
<?php
$call_liste_data = mysql_query("SELECT u.login, u.prenom, u.nom FROM utilisateurs u, j_aidcateg_super_gestionnaires j WHERE (j.indice_aid='$indice_aid' and u.login=j.id_utilisateur and (statut='professeur' or statut='cpe'))  order by u.nom, u.prenom");
$nombre = mysql_num_rows($call_liste_data);
if ($nombre !=0)
    echo "<table border=0>";
$i = "0";
while ($i < $nombre) {
    $login_gestionnaire = mysql_result($call_liste_data, $i, "login");
    $nom_prof = mysql_result($call_liste_data, $i, "nom");
    $prenom_prof = @mysql_result($call_liste_data, $i, "prenom");
    echo "<tr><td><b>";
    echo "$nom_prof $prenom_prof</b></td><td> <input type=\"checkbox\" name=\"delete_gestionnaire_".$login_gestionnaire."\" value=\"y\" /> (check to remove)</td></tr>\n";

    $i++;
}
if ($nombre !=0)
    echo "</table>";
echo "<select size=1 name=reg_gestionnaire_login>\n";
echo "<option value=''>(aucun)</option>\n";
$call_prof = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' OR statut = 'cpe') order by nom");
$nombreligne = mysql_num_rows($call_prof);
$i = "0" ;
while ($i < $nombreligne) {
    $login_prof = mysql_result($call_prof, $i, 'login');
    $nom_el = mysql_result($call_prof, $i, 'nom');
    $prenom_el = mysql_result($call_prof, $i, 'prenom');
    echo "<option value=\"".$login_prof."\">".$nom_el." ".$prenom_el."</option>\n";
    $i++;
}
?>
</select>
<hr />
<?php } ?>

<p><b>Complementary tools  to management of IDA :</b></p>
<p> By activating the complementary tools  to management of IDA, you have access to additional fields
(attribution of a room, possibility of defining a summary, the type of production, key words, a public recipient?).
<a href="javascript:centrerpopup('help.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')">Consult the assistance</a>.</p>
<p>
<input type="radio" onclick="javascript:Element.show('outils_comp');" name="activer_outils_comp" value="y" <?php if ($activer_outils_comp=='y') echo " checked"; ?> />&nbsp;Activate the complementary tools<br />
<input type="radio" onclick="javascript:Element.hide('outils_comp');" name="activer_outils_comp" value="n" <?php if ($activer_outils_comp=='n') echo " checked"; ?> />&nbsp;Deactivate the complementary tools
</p>
<?php if ($activer_outils_comp=='y') {?>
    <div id="outils_comp">
<?php } else { ?>
    <div id="outils_comp" style="display:none;">
<?php } ?>
<hr />
<p><b>Modification of the project cards  : </b></p>
<p>In addition to the professors in charge for each IDA, you can indicate below users (professors or CPE) having the right to modify the cards project (documentalist, ...)
even when the administrator deactivate this possibility for the responsible professors.</p>
<?php
$call_liste_data = mysql_query("SELECT u.login, u.prenom, u.nom FROM utilisateurs u, j_aidcateg_utilisateurs j WHERE (j.indice_aid='$indice_aid' and u.login=j.id_utilisateur and (statut='professeur' or statut='cpe'))  order by u.nom, u.prenom");
$nombre = mysql_num_rows($call_liste_data);
if ($nombre !=0)
    echo "<table border=0>";
$i = "0";
while ($i < $nombre) {
    $login_prof = mysql_result($call_liste_data, $i, "login");
    $nom_prof = mysql_result($call_liste_data, $i, "nom");
    $prenom_prof = @mysql_result($call_liste_data, $i, "prenom");
    echo "<tr><td><b>";
    echo "$nom_prof $prenom_prof</b></td><td> <input type=\"checkbox\" name=\"delete_".$login_prof."\" value=\"y\" /> (cocher pour supprimer)</td></tr>\n";

    $i++;
}
if ($nombre !=0)
    echo "</table>";
echo "<select size=1 name=reg_prof_login>\n";
echo "<option value=''>(aucun)</option>\n";
$call_prof = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' OR statut = 'cpe') order by nom");
$nombreligne = mysql_num_rows($call_prof);
$i = "0" ;
while ($i < $nombreligne) {
    $login_gestionnaire = mysql_result($call_prof, $i, 'login');
    $nom_el = mysql_result($call_prof, $i, 'nom');
    $prenom_el = mysql_result($call_prof, $i, 'prenom');
    echo "<option value=\"".$login_gestionnaire."\">".$nom_el." ".$prenom_el."</option>\n";
    $i++;
}

?>
</select>
<hr /><p><b>Attendance sheet : </b></p>
<p>By check the box presence below, you have the possibility, in the interface of visualization, post a link allowing to print attendance sheets.</p>
<p>
<input type="checkbox" id="feuillePresence" name="feuille_presence" value="y" <?php if ($feuille_presence == "y") { echo ' checked="checked"';} ?> />
<label for="feuille_presence"> Post a link allowing the impression of attendance sheets</label>
</p>
</div>

</div>
<input type="hidden" name="is_posted" value="1" />
<input type="hidden" name="indice_aid" value="<?php echo $indice_aid;?>" />
<div id='fixe'>
<input type="submit" value="Save" />
</div>
</form>
<?php require("../lib/footer.inc.php"); ?>
