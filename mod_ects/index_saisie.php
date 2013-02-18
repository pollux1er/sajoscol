<?php
/*
 * $Id: saisie_avis.php 2147 2008-07-23 09:01:04Z tbelliard $
 *
 * Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//**************** EN-TETE *****************
$titre_page = "Seizure of appropriations ECTS";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a></p>";

// On va initialiser des marqueurs qui simplifieront les conditions par la suite
$acces_prof_suivi = false;
$acces_prof = false;
$acces_scol = false;

$prof_suivi = sql_count(sql_query("SELECT professeur FROM j_eleves_professeurs  WHERE professeur = '".$_SESSION['login']."'")) != "0" ? true : false;

if (($_SESSION['statut'] == 'professeur') && $gepiSettings["GepiAccesSaisieEctsPP"] =='yes' && $prof_suivi) {
  $acces_prof_suivi = true;
}
if (($_SESSION['statut'] == 'professeur') && $gepiSettings["GepiAccesSaisieEctsProf"] =='yes') {
  $acces_prof = true;
}
if ((($_SESSION['statut'] == 'scolarite') and $gepiSettings["GepiAccesSaisieEctsScolarite"] =='yes') or $_SESSION['statut'] == 'secours') {
  $acces_scol = true;
}

if (!$acces_prof_suivi && !$acces_prof && !$acces_scol) {
  die("Insufficient rights to reach this page.");
}


if ($acces_scol) {

  echo "<p>Access for seizure complete of appropriations ECTS. Select the class
for which you wish to carry out the seizure :</p>";
    // On ne sélectionne que les classes qui ont au moins un enseignement ouvrant à crédits ECTS
	if($_SESSION['statut']=='scolarite'){
		$call_classe = mysql_query("SELECT DISTINCT c.*
                                    FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc
                                    WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id=jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
	}
	else{
		$call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
	}

    $nombre_classe = mysql_num_rows($call_classe);
	if($nombre_classe==0){
		echo "<p>No class with parameter setting ECTS is allotted to you.<br />Contact the administrator so that it carries out the suitable
parameter setting in the Management of the classes.</p>\n";
	}
	else{

		$j = "0";
		$alt=1;
		while ($j < $nombre_classe) {
			$id_classe = mysql_result($call_classe, $j, "id");
			$classe_suivi = mysql_result($call_classe, $j, "classe");
			echo "<br /><b>$classe_suivi</b> --- <a href='saisie_ects.php?id_classe=$id_classe'>To seize the appropriations, raises by pupil, with visualization of
the results of the pupil.</a><br />";
			$j++;
		}

	}
}

if ($acces_prof_suivi) {
    echo "<br/>";
    echo "<p>Access for final seizure and supplements appropriations ECTS. Select
the class for which you wish to carry out the seizure :</p>";
    $call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc, j_groupes_classes jgc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)");
    $nombre_classe = mysql_num_rows($call_prof_classe);
    
    if ($nombre_classe == "0") {
        echo "You are not ".$gepiSettings['gepi_prof_suivi']." in classes having right lesson opening with ECTS.";
    } else {
        $j = "0";
        echo "<p>You are ".$gepiSettings['gepi_prof_suivi']." in the class of :</p>";
        while ($j < $nombre_classe) {
            $id_classe = mysql_result($call_prof_classe, $j, "id");
            $classe_suivi = mysql_result($call_prof_classe, $j, "classe");
            echo "<br /><b>$classe_suivi</b> --- <a href='saisie_ects.php?id_classe=$id_classe'>To seize the appropriations, raises by student, with visualization of
the results of the pupil.</a><br />";
            $j++;
        }
    }
}

if ($acces_prof) {
    echo "<br/>";
    echo "<p>Access to the interface of pre-seizure of appropriations ECTS for the
lesson of which you are responsible :</p>";
    $call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE
        (jgp.login = '" . $_SESSION['login'] . "' AND jgc.id_groupe = jgp.id_groupe AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)");
    $nombre_classe = mysql_num_rows($call_prof_classe);
    if ($nombre_classe == "0") {
        echo "<p>No teaching of which you are responsible opens right to appropriations
ECTS.</p>";
    } else {
        $j = "0";
        while ($j < $nombre_classe) {
            $id_classe = mysql_result($call_prof_classe, $j, "id");
            $classe_suivi = mysql_result($call_prof_classe, $j, "classe");
            echo "<br /><b>$classe_suivi</b> --- <a href='saisie_ects.php?mode=presaisie&id_classe=$id_classe'>Seize the appropriations, raises by pupil, with visualization of
the results of the student.</a><br />";
            $j++;
        }
    }
}



echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
