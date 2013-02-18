<?php
/*
 * @version: $Id: config_aid_fiches_projet.php 6588 2011-03-02 17:53:54Z crob $
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
$is_posted = isset($_POST["is_posted"]) ? $_POST["is_posted"] : NULL;
// ========== fin initialisation ===================

$requete = "select * from droits_aid where (id!='cpe_peut_modifier' and id!='prof_peut_modifier' and id!='eleve_peut_modifier' and id != 'fiche_publique' and id!='affiche_adresse1' and id!='en_construction' and id!='nom' and id!='numero') order by id";
if (isset($is_posted) and ($is_posted == "1")) {
	check_token();

    $res = mysql_query($requete);
    $nb_lignes = mysql_num_rows($res);
    $i = 0;
    while ($i < $nb_lignes) {
        $id = mysql_result($res,$i,"id");
        if ($_POST["description_".$id]=="") $_POST["description_".$id] = "A préciser";
        if (!(isset($_POST["public_".$id]))) $_POST["public_".$id] = '-';
        if (!(isset($_POST["professeur_".$id]))) $_POST["professeur_".$id] = '-';
        if (!(isset($_POST["cpe_".$id]))) $_POST["cpe_".$id] = '-';
        if (!(isset($_POST["eleve_".$id]))) $_POST["eleve_".$id] = '-';
        if (!(isset($_POST["statut_".$id]))) $_POST["statut_".$id]= '0';
        $sql = mysql_query("update droits_aid set
        public = '".$_POST["public_".$id]."',
        professeur = '".$_POST["professeur_".$id]."',
        cpe = '".$_POST["cpe_".$id]."',
        eleve = '".$_POST["eleve_".$id]."',
        description = '".$_POST["description_".$id]."',
        statut = '".$_POST["statut_".$id]."'
        where id = '".$id."'");
        $i++;
        }
}


//**************** EN-TETE *********************
$titre_page = "Configuration des fiches projet";

$lang = isset($_GET['lang']) ? "_" . $_GET['lang'] : "";

// $lavariable La variable et son affectation par defaut, deja presente dans le code

$titre_page = empty($lang) ? ${'titre_page' . $lang} = "Configuration of the cards project" : $titre_page;

require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<form enctype="multipart/form-data" name="formulaire" action="config_aid_fiches_projet.php" method="post">
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>
|<a href="javascript:centrerpopup('help.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')">Help</a>|
<a href="config_aid_matieres.php">Disciplines Configuration </a>|
<a href="config_aid_productions.php">Productions Configuration </a>|
<input type="submit" value="Save" /><br />
<?php

echo add_token_field();

echo "<p>The following table makes it possible to fix the rights on the various fields of the cards project.";
echo "<br /><br />Notice :
<ul>
<li>When a field is < b>enabled</b >, it is visible in the private interface of GEPI (connected user) by the administrators, professors, CPE, students and responsables.</li >
<li>When a field is not < b>enabled</b >, that means that it is not used in GEPI.</li>
<li>The code \"<b>R &amp; W</b>\" mean that the field is accessible in reading and writing.
<br />The code \"<b>R</b>\" mean that the field is accessible in reading only.
<br />The code \"<b>-</b>\" mean that the field is not accessible.
</li>
</ul>";
echo "<table border='1' cellpadding='5' class='boireaus'>";
echo "<tr><th><b>Champ de la fiche projet</b></th>
<th><span class='small'>Name of the field</span></th>
<th><span class='small'>The field is visible in the public interface</span></th>
<th><span class='small'>The professors can modify this field</span></th>
<th><span class='small'>The C.P.E can modify this field</span></th>
<th><span class='small'>The students can modify this field</span></th>
<th><span class='small'>The field is enabled</span></th>
</tr>";
$res = mysql_query($requete);
$nb_lignes = mysql_num_rows($res);
$i = 0;
$alt=1;
while ($i < $nb_lignes) {
    $id = mysql_result($res,$i,"id");
    $public = mysql_result($res,$i,"public");
    $professeur = mysql_result($res,$i,"professeur");
    $cpe = mysql_result($res,$i,"cpe");
    $eleve = mysql_result($res,$i,"eleve");
    $responsable = mysql_result($res,$i,"responsable");
    $description = mysql_result($res,$i,"description");
    $_statut = mysql_result($res,$i,"statut");
    $alt=$alt*(-1);
    echo "<tr class='lig$alt'>";
    if (($id!="perso1") and ($id!="perso2") and ($id!="perso3"))
        echo "<td>".$description."</td>\n";
    else
        echo "<td>".$id."</td>\n";
    if (($id=="perso1") or ($id=="perso2") or ($id=="perso3")) {
        echo "<td><input type=\"text\" name=\"description_".$id."\" value=\"".htmlentities($description)."\" size=\"20\" /></td>\n";
    } else {
        echo "<td><input type=\"hidden\" name=\"description_".$id."\" value=\"".htmlentities($description)."\" /> - </td>\n";

    }

    echo "<td><select name=\"public_".$id."\">\n";
    echo "<option value=\"F\" ";
    if ($public=='F') echo " selected ";
    echo " >R</option>\n";
    echo "<option value=\"-\" ";
    if ($public=='-') echo " selected ";
    echo " >-</option>\n";
    echo "</select>\n</td>\n";

    echo "<td><select name=\"professeur_".$id."\">\n";
    echo "<option value=\"V\" ";
    if ($professeur=='V') echo " selected ";
    echo " >R &amp; W</option>\n";
    echo "<option value=\"F\" ";
    if ($professeur=='F') echo " selected ";
    echo " >R</option>\n";
    echo "<option value=\"-\" ";
    if ($professeur=='-') echo " selected ";
    echo " >-</option>\n";
    echo "</select>\n</td>\n";

    echo "<td><select name=\"cpe_".$id."\">\n";
    echo "<option value=\"V\" ";
    if ($cpe=='V') echo " selected ";
    echo " >R &amp; W</option>\n";
    echo "<option value=\"F\" ";
    if ($cpe=='F') echo " selected ";
    echo " >R</option>\n";
    echo "<option value=\"-\" ";
    if ($cpe=='-') echo " selected ";
    echo " >-</option>\n";
    echo "</select>\n</td>\n";

    echo "<td><select name=\"eleve_".$id."\">\n";
    echo "<option value=\"V\" ";
    if ($eleve=='V') echo " selected ";
    echo " >R &amp; W</option>\n";
    echo "<option value=\"F\" ";
    if ($eleve=='F') echo " selected ";
    echo " >R</option>\n";
    echo "<option value=\"-\" ";
    if ($eleve=='-') echo " selected ";
    echo " >-</option>\n";
    echo "</select>\n</td>\n";

    echo "<td><input type=\"checkbox\" name=\"statut_".$id."\" value=\"1\" ";
    if ($_statut=='1') echo " checked ";
    echo "/></td>\n";
    echo "</tr>\n";
    $i++;
}
echo "</table><br /><br />";
?>
<input type="hidden" name="is_posted" value="1" />
<div id='fixe'>
<input type="submit" value="Save" />
</div>
</form>
<?php require("../lib/footer.inc.php"); ?>