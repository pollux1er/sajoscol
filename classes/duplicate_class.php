<?php
/*
 * $Id: duplicate_class.php 6605 2011-03-03 13:59:15Z crob $
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


$msg = '';

if (isset($new_name_defined)) {

    if (!my_ereg("^[[:print:]]{1,10}$",trim($nom_court))) {
    $msg .= "Short name must be composed of alphanumerics (from 1 to 10 characters).<br />";
    unset($new_name_defined);
    }

    if (!my_ereg("^([[:print:]]|[âäàéèêëüûöôîï]){1,50}$",trim($nom_complet))) {
        $msg .= "The complete name must be composed of alphanumerics (from 1 to 50 characters).<br />";
    unset($new_name_defined);
    }
}

if (isset($eleves_selected)) {
	check_token();

    // On fait l'enregistrement de la nouvelle classe
    $get_settings = mysql_query("SELECT * FROM classes WHERE id='$id_classe'");
    $suivi_par = traitement_magic_quotes(corriger_caracteres(@mysql_result($get_settings, "0", "suivi_par")));
    $formule = traitement_magic_quotes(corriger_caracteres(@mysql_result($get_settings, "0", "formule")));
    $nom_court = traitement_magic_quotes(corriger_caracteres(urldecode($nom_court)));
    $nom_complet = traitement_magic_quotes(corriger_caracteres(urldecode($nom_complet)));



    $register_newclass = mysql_query("INSERT INTO classes SET
    classe='$nom_court',
    nom_complet='$nom_complet',
    formule='$formule',
    suivi_par='$suivi_par',
    format_nom='np'");
	updateOnline("INSERT INTO classes SET
    classe='$nom_court',
    nom_complet='$nom_complet',
    formule='$formule',
    suivi_par='$suivi_par',
    format_nom='np'");
    if (!$register_newclass) $msg .= "Error during the recording of the new class.<br />";

    $newclass_id = mysql_result(mysql_query("SELECT max(id) FROM classes"), 0);

    // Maintenant on duplique les entrées de la table j_classes_matieres_professeurs
    $get_data1 = mysql_query("SELECT * FROM j_classes_matieres_professeurs WHERE id_classe='$id_classe'");
    $nb1 = mysql_num_rows($get_data1);
    for ($i1=0;$i1<$nb1;$i1++) {
        $id_matiere = mysql_result($get_data1, $i1, "id_matiere");
        $id_professeur = mysql_result($get_data1, $i1, "id_professeur");
        $priorite = mysql_result($get_data1, $i1, "priorite");
        $ordre_prof = mysql_result($get_data1, $i1, "ordre_prof");

        $register1 = mysql_query("INSERT INTO j_classes_matieres_professeurs SET id_classe='$newclass_id',id_matiere='$id_matiere',id_professeur='$id_professeur',priorite='$priorite',ordre_prof='$ordre_prof',coef='0', recalcul_rang='y'");
       updateOnline("INSERT INTO j_classes_matieres_professeurs SET id_classe='$newclass_id',id_matiere='$id_matiere',id_professeur='$id_professeur',priorite='$priorite',ordre_prof='$ordre_prof',coef='0', recalcul_rang='y'");
	   if (!$register1) $msg .= "Error during the recording in j_classes_matieres_professeurs for the course $id_matiere.<br />";
    }

    // table périodes
    $get_data2 = mysql_query("SELECT * FROM periodes WHERE id_classe='$id_classe'");
    $nb2 = mysql_num_rows($get_data2);
    for ($i2=0;$i2<$nb2;$i2++) {
        $nom_periode = traitement_magic_quotes(corriger_caracteres(mysql_result($get_data2, $i2, "nom_periode")));
        $num_periode = mysql_result($get_data2, $i2, "num_periode");
        $verouiller = mysql_result($get_data2, $i2, "verouiller");
        $register2 = mysql_query("INSERT INTO periodes SET nom_periode='$nom_periode',num_periode='$num_periode',verouiller='$verouiller',id_classe='$newclass_id'");
         updateOnline("INSERT INTO periodes SET nom_periode='$nom_periode',num_periode='$num_periode',verouiller='$verouiller',id_classe='$newclass_id'");
		if (!$register2) $msg .= "Error during the recording of the period $nom_periode.<br />";
    }

    // On appelle la liste pour faire un traitement élève par élève.

    $query = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' AND e.login = c.login)");
    $number = mysql_num_rows($query);

    for ($l=0;$l<$number;$l++) {
        $eleve_login = mysql_result($query, $l, "login");

        if (isset($$eleve_login)) {

            $update1 = mysql_query("UPDATE j_eleves_classes SET id_classe='$newclass_id' WHERE (login='$eleve_login' AND id_classe='$id_classe')");
            updateOnline("UPDATE j_eleves_classes SET id_classe='$newclass_id' WHERE (login='$eleve_login' AND id_classe='$id_classe')");
			if (!$update1) $msg .= "Error during the update of the connection student / class for the student $eleve_login.<br />";
            $update2 = mysql_query("UPDATE j_eleves_professeurs SET id_classe='$newclass_id' WHERE (login='$eleve_login' AND id_classe='$id_classe')");
			updateOnline("UPDATE j_eleves_professeurs SET id_classe='$newclass_id' WHERE (login='$eleve_login' AND id_classe='$id_classe')");
            if (!$update2) $msg .= "Error during the update of the connection student / professor for the student $eleve_login.<br />";
        }
    }

}

//**************** EN-TETE *****************
$titre_page = "Duplication or scission of a class";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return to the management of the classes</a>
</p>
<?php


if (!isset($new_name_defined)) {
    echo "<p>You are on the point of dividing a class.<br />According to the case, you will do this operation several times.<br /><br />Note that the parameters of period of the new class will be identical
to those of the class of origin. It is the same for the already assigned professors and courses. In the same way the students keep the same principal professor. This information can be modified thereafter, once the  operation is finished.</p>";

    echo "<form action='duplicate_class.php' method=post>";

	echo add_token_field();

    echo "<p>Please select the class that you wish to divide :<br />";
    $call_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    $nombreligne = mysql_num_rows($call_classes);
    echo "<select name='id_classe' size=1>\n";
    $i = "0" ;
    while ($i < $nombreligne) {
        $id_classe = mysql_result($call_classes, $i, "id");
        $l_classe = mysql_result($call_classes, $i, "classe");
        //echo "<option value='$id_classe' size=1>$l_classe</option>";
        echo "<option value='$id_classe'>$l_classe</option>\n";
    $i++;
    }
    echo "</select>\n";
    echo "<input type=hidden name=new_name_defined value='1' />";
    echo "<p>Enter short name of the new class (ex: 2NDE3) :</p>";
    echo "<p><input type=text size=20 name='nom_court' value='' />";
    echo "<br /><p>Enter the new complete name of the new class (ex: Seconde 3) :</p>";
    echo "<p><input type=text size=20 name='nom_complet' value='' />";
    echo "<br /><p><input type=submit value='Next Stape' />";
    echo "</form>";

}

if (isset($new_name_defined) && !isset($eleves_selected)) {
    echo "<p>You must select the students which will be withdrawn
from the class of origin to be assigned to the new class. This assignment is effective for all the periods of the class of origin.</p>";

    echo "<p>Students to be assigned to the new class :</p>";

    $call_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
    WHERE (c.id_classe = '$id_classe' AND e.login = c.login)");
    $nb = mysql_num_rows($call_eleves);

    echo "<form action='duplicate_class.php' method=post>";

	echo add_token_field();

    echo "<table cellpadding=5>";

    for ($k=0;$k<$nb;$k++) {

        $eleve_login = mysql_result($call_eleves, $k, "login");
        $eleve_nom = mysql_result($call_eleves, $k, "nom");
        $eleve_prenom = mysql_result($call_eleves, $k, "prenom");

        echo "<tr><td>";
        echo "<input type=checkbox name='$eleve_login' value='new' />";
        echo "</td><td>";
        echo "$eleve_nom $eleve_prenom";
        echo "</td></tr>";

    }

    echo "</table>";
    echo "<input type=hidden name='eleves_selected' value='1' />";
    echo "<input type=hidden name='id_classe' value='$id_classe' />";
    echo "<input type=hidden name='new_name_defined' value='1' />";
    echo "<input type=hidden name='nom_court' value='".urlencode($nom_court)."' />";
    echo "<input type=hidden name='nom_complet' value='".urlencode($nom_complet)."' />";
    echo "<p><input type=submit value='Save the duplication' />";
    echo "<p>Caution !!! Irremediable procedure !</p>";
    echo "</form>";


}

if (isset($eleves_selected) && isset($new_name_defined)) {
    echo "<p class=alert>The new class was creates. Please check that everything is correct by visualize the
parameters of this new class.</p>";
}
require("../lib/footer.inc.php");
?>