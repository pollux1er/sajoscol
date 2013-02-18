<?php
/*
 * $Id: ajax_edit_limite.php 6822 2011-04-26 12:07:42Z crob $
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
@set_time_limit(0);



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


// Ebauche de liste des variables reçues:
// $choix_edit correspond au choix de ce qui doit être affiché:
// Pour $choix_edit=1:
//    - Tous les élèves que le prof a en cours, ou rattaché à une classe qu'a le prof, ou tous les élèves selon le choix paramétré en admin dans Droits d'accès
//    - En compte scolarité ou cpe: Tous les élèves de la classe
// $choix_edit=2
//    - Uniquement l'élève sélectionné: la variable $login_eleve, qui est de toute façon affectée, doit alors être prise en compte pour limiter l'affichage à cet élève
// $choix_edit=3
//    - Ce choix correspond aux classes avec plusieur professeurs principaux
//      On a alors une variable $login_prof affectée pour limiter les affichages aux élèves suivi par un des profs principaux seulement
//      Cette variable $login_prof ne devrait être prise en compte que dans le cas $choix_edit==3
// $choix_edit=4
//    - Affichage du bulletin des avis sur la classe

include "../lib/periodes.inc.php";
include "../lib/bulletin_simple.inc.php";
//include "../lib/bulletin_simple_bis.inc.php";
//==============================
// AJOUT: boireaus 20080209
include "../lib/bulletin_simple_classe.inc.php";
//include "../lib/bulletin_simple_classe_bis.inc.php";
//==============================

header('Content-Type: text/html; charset=ISO-8859-15');

//==============================
// Dans le cas d'un appel via ajax, on ne met pas de header: affichage dans une infobulle
//require_once("../lib/header.inc");
//==============================

// Vérifications de sécurité
if (
	($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesReport cardSimpleParent") != "yes") OR
	($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesReport cardSimpleEleve") != "yes")
	) {
	tentative_intrusion(2, "Attempt at visualization of a bulletin simplified without being
authorized there.");
	echo "<p>You are not authorized to visualize this page.</p>";
	require "../lib/footer.inc.php";
	die();
}

// Et une autre vérification de sécurité : est-ce que si on a un statut 'responsable' le $login_eleve est bien un élève dont le responsable a la responsabilité
if ($_SESSION['statut'] == "responsable") {
	$test = mysql_query("SELECT count(e.login) " .
			"FROM eleves e, responsables2 re, resp_pers r " .
			"WHERE (" .
			"e.login = '" . $login_eleve . "' AND " .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2'))");
	if (mysql_result($test, 0) == 0) {
	    tentative_intrusion(3, "Attempt at a relative to visualize a simplified bulletin of a student of which it is not responsible legal.");
	    echo "You can visualize only the simplified bulletins of the pupils for whom
you are responsible legal.\n";
	    require("../lib/footer.inc.php");
		die();
	}
}

// Et une autre...
if ($_SESSION['statut'] == "eleve" AND strtoupper($_SESSION['login']) != strtoupper($login_eleve)) {
    tentative_intrusion(3, "Attempt at a student to visualize a simplified bulletin of another
student.");
    echo "You can visualize only your simplified bulletins.\n";
    require("../lib/footer.inc.php");
	die();
}

// Et encore une : si on a un reponsable ou un élève, alors seul l'édition pour un élève seul est autorisée
if (($_SESSION['statut'] == "responsable" OR $_SESSION['statut'] == "eleve") AND $choix_edit != "2") {
    tentative_intrusion(3, "Attempt (student or parent) change of the mode of visualization of a
bulletin simplified (the imposed mode is visualization for only one student)");
    echo "Do not try to cheat...\n";
    require("../lib/footer.inc.php");
	die();
}

//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesReport cardSimpleProfToutesClasses") != "yes") {
	$test = mysql_num_rows(mysql_query("SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
	if ($test == "0") {
		tentative_intrusion("2", "Attempt at access by a teacher to a class in which it does not teach, without having the authorization of it.");
		echo "You cannot reach this class because do not be to you a professor
there !";
		require ("../lib/footer.inc.php");
		die();
	}
}

//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" and $choix_edit == "2") {
if ($_SESSION['statut'] == "professeur" AND
getSettingValue("GepiAccesReport cardSimpleProfToutesClasses") != "yes" AND
getSettingValue("GepiAccesReport cardSimpleProfTousEleves") != "yes" AND
$choix_edit == "2") {

	$test = mysql_num_rows(mysql_query("SELECT jeg.* FROM j_eleves_groupes jeg, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jeg.id_groupe = jgp.id_groupe AND jeg.login = '".$login_eleve."')"));
	if ($test == "0") {
		tentative_intrusion("2", "Attempt at access by a teacher to a simplified bulletin of a student whom it does not have in progress, without having the authorization of
it.");
		echo "You cannot reach to this student !";
		require ("../lib/footer.inc.php");
		die();
	}
}

//debug_var();

// On a passé les barrières, on passe au traitement
include("edit_limite.inc.php");

//==============================
// Dans le cas d'un appel via ajax, on ne met pas de header: affichage dans une infobulle
require("../lib/footer.inc.php");
//==============================

?>