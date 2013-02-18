<?php
/*
 * @version: $Id: config_aid_productions.php 6588 2011-03-02 17:53:54Z crob $
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

if (isset($is_posted) and ($is_posted == "1")) {
	check_token();

    $msg = "";
    $pb = "no";
    foreach ($_POST as $key => $value) {
        $value = trim($value);
        $test = sql_query1("select count(id) from aid_productions where id='".$key."'");
        if ($test != 0) {
            if ($value == "") {
                $test = sql_query1("select count(id) from aid where productions='".$key."'");
                if ($test > 0) {
                    $msg .= "The type ".$value." cannot be removed ,it is already used in at least a card project.<br />";
                    $pb = "yes";
                } else {
                    $req = mysql_query("delete from aid_productions where id='".$key."'");
                    if (!$req) {
                        $msg .= "Problem during the suppression of the type $value <br />";
                        $pb = "yes";
                    }
                }
            } else {
                $req = mysql_query("update aid_productions set nom = '".$value."' where id='".$key."'");
                if (!$req) {
                    $msg .= "Problemduringthe update of the type $value <br />";
                    $pb = "yes";
                }
            }
        } else {
            if (($key!="is_posted") and ($value!='')) {
                $req = mysql_query("insert into aid_productions set nom = '".$value."'");
                if (!$req) {
                    $msg .= "Problem during the insertion of the type $value <br />";
                    $pb = "yes";
                }
            }
        }
    }
    if ($pb!="yes") $msg = "The modifications were recorded.";
}
$requete = "select * from aid_productions order by nom";


//**************** EN-TETE *********************
$titre_page = "Configuration des types de productions pour les fiches projet";

$lang = isset($_GET['lang']) ? "_" . $_GET['lang'] : "";
// $lavariable La variable et son affectation par defaut, deja presente dans le code
$titre_page = empty($lang) ? ${'titre_page' . $lang} = "Configuration of the types of productions for the cards project" : $titre_page;

require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<form enctype="multipart/form-data" name="formulaire" action="config_aid_productions.php" method="post">
<p class="bold"><a href="config_aid_fiches_projet.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>
|<a href="javascript:centrerpopup('help.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')">Help</a>|
<input type="submit" value="Save" /><br />
<?php

  echo add_token_field();

  echo "<p>Among the fields of the cards project appear the fields \"production\".
<br />You can below modify, add or remove types of productions.
<br /><b>Notice : </b>If you already created cards project and affected of the productions, any change in the types below can have more or less harmful consequences.";

echo "<table border='1' cellpadding='5' class='boireaus'>";
echo "<tr><th><b>Identifier</b></th>
<th><span class='small'>Heading of the type of production</span></th>
</tr>";
$res = mysql_query($requete);
$nb_lignes = mysql_num_rows($res);
$i = 0;
$alt=1;
while ($i < $nb_lignes) {
    $id = mysql_result($res,$i,"id");
    $nom = mysql_result($res,$i,"nom");
    echo "<tr class='lig$alt'>";
    echo "<td>".$id."</td>\n";
    echo "<td><input type=\"text\" name=\"".$id."\" value =\"".htmlentities($nom)."\" size=\"40\" /></td>\n";
    echo "</tr>\n";
    $alt=$alt*(-1);
    $i++;
}
echo "<tr><td>New type :</td>\n";
echo "<td><input type=\"text\" name=\"new\" value =\"\" size=\"40\" /></td></tr>\n";

echo "</table><br /><br />";
?>
<input type="hidden" name="is_posted" value="1" />
<div id='fixe'>
<input type="submit" value="Register" />
</div>
</form>
<?php require("../lib/footer.inc.php"); ?>