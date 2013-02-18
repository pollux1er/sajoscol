<?php

/**
 * Fichier d'initialisation de l'EdT
 * Pour effacer la table avant une nouvelle initialisation il faut faire un TRUNCATE TABLE nom_table;
 * @version $Id: edt_initialiser.php 4592 2010-06-18 13:39:41Z adminpaulbert $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
$titre_page = "Timetable - Initialization";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");
require_once("./fonctions_cours.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}
// CSS et js particulier à l'EdT
$utilisation_win = 'oui';
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">
<?php
    require_once("./menu.inc.new.php");
	// Initialisation des variables de la page
$initialiser = isset($_POST["initialiser"]) ? $_POST["initialiser"] : NULL;
$choix_prof = isset($_POST["prof"]) ? $_POST["prof"] : NULL;
$enseignement = isset($_POST["enseignement"]) ? $_POST["enseignement"] : NULL;
$ch_heure = isset($_POST["ch_heure"]) ? $_POST["ch_heure"] : NULL;
$ch_jour_semaine = isset($_POST["ch_jour_semaine"]) ? $_POST["ch_jour_semaine"] : NULL;
$duree = isset($_POST["duree"]) ? $_POST["duree"] : NULL;
$heure_debut = isset($_POST["heure_debut"]) ? $_POST["heure_debut"] : NULL;
$choix_semaine = isset($_POST["choix_semaine"]) ? $_POST["choix_semaine"] : NULL;
$login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;
$init = isset($_GET["init"]) ? $_GET["init"] : NULL;

	// On affiche ou non les infos de base
$aff_reglages = GetSettingEdt("edt_aff_init_infos");

if ($aff_reglages == "oui") {
	echo "
	<p style=\"font-weight: bold;\">For beginning the creation of the courses in the timetable, it is
necessary to prepare the software.</p>

	<p>Part of initialization is common with the module absences :
	<br />&nbsp;-&nbsp;<a href=\"./admin_periodes_absences.php?action=visualiser\">th diiferents crenels</a> of the day.
	<br />&nbsp;-&nbsp;<a href=\"./admin_config_semaines.php?action=visualiser\">the type of week</a> (paire/impaire, A/B/C, 1/2,...).
	<br />&nbsp;-&nbsp;<a href=\"./admin_horaire_ouverture.php?action=visualiser\">schedules of the school</a>.</p>

<hr />

	<p>It is necessary to inform the calendar while clicking about the menu
on the left. All the periods which appear in the timetable must to be defined: quarters, holidays, ... If all your courses last the time of the school year, you can do without this stage.</p>

<hr />
	<p>
	To enter information on the timetable of Gepi, there are several possibilities.
	<br /><br />
	<p onclick=\"ouvrirWin2('id_manuel'); return false;\" style=\"float: left; border: 1px solid grey; background: #FFFFFF; width: 200px; text-align: center; cursor: pointer;\">
	Manual method</p>

	<p onclick=\"changerDisplayDiv('edt_init_import'); return false;\" style=\"position: relative; margin-left: 350px; border: 1px solid grey; background: #FFFFFF; width: 200px; text-align: center; cursor: pointer;\">
	Importation</p>
	";
}
// ============================= c'est là que j'ai sorti la partie manuelle ==================

	// une fois initialisé, la partie suivante peut être verrouillée
			// Pour déverrouiller, le traitement se fait ici là

echo '<div id="edt_init_import" style="display: none;">';

if (isset($init) AND $init == "ok") {
	$req_reprendre_init = mysql_query("UPDATE edt_setting SET valeur = 'oui' WHERE reglage = 'edt_aff_init_infos2'");
}
else if (isset($init) AND $init == "ko") {
	$req_reprendre_init = mysql_query("UPDATE edt_setting SET valeur = 'non' WHERE reglage = 'edt_aff_init_infos2'");
}

$aff_reglages2 = GetSettingEdt("edt_aff_init_infos2");

if ($aff_reglages2 == "oui") {
	echo '
	<span class="refus">The EdT module is not initialized.
	<a href="./edt_initialiser.php?init=ko">Click here when you finished initialization</a></span>

	<h5>For the initialization of the beginning of year, the various software of design of the timetables does not allow to have only one procedure. It is thus advisable to determine well what is possible. Before launching you in this initialization, you must ensure
yourselves to have parameterized
 	the whole of information relating to the schedules of the school.</h5>

 	<h4 class="red">For the following procedures, you can ask Gepi to erase the existing courses or to decide to
preserve them.</h4>

 	<div id="lien" style="background: #fefefe; margin-left: 200px; width: 400px;">
 		<br />
		<p class="edt_lien"><a href="./edt_init_csv.php">Files csv builds manually.</a></p>
		<p class="edt_lien"><a href="./edt_init_csv2.php">Export from UnDeuxTemps.</a></p>
		<p class="edt_lien"><a href="./edt_init_texte.php">Standard export Charlemagne, IndexEducation.</a></p>
		<br />
	</div>
 		';

 //<div id=\"lien\"><a href=\"./index_edt.php?initialiser=ok&xml=ok\">Cliquer ici pour une initialisation par fichiers xml (type export STSWeb)</a></div>
}
else if ($aff_reglages2 == "non") {
	echo '
	<span class="accept">The EdT module is initialized.
	<a href="./edt_initialiser.php?init=ok">Click here to redo this initialization</a></span>
		';
}
else {
	echo '
	There is a problem of adjustment in your data base, it perhaps should be updated.
		';

}
echo '</div>';
?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>