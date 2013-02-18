<?php

/**
 * @version $Id: interface_abs.php 2147 2008-07-23 09:01:04Z tbelliard $
 *
 * Fichier de paramétrage de l'interface professeur pour la saisie des absences
 *
 * @copyright 2008
 *
 *  * This file is part of GEPI.
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
$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
// les fonctions du module absences
include("../lib/functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}
// ===================== Initialisation des variables =======
$message = '';
$clic = isset($_POST["clic"]) ? $_POST["clic"] : NULL;
$date_phase1 = isset($_POST["date_phase1"]) ? $_POST["date_phase1"] : "n";
$liste_absents = isset($_POST["liste_absents"]) ? $_POST["liste_absents"] : "n";
$voir_fiche_eleve = isset($_POST["voir_fiche_eleve"]) ? $_POST["voir_fiche_eleve"] : "n";
$renseigner_retard = isset($_POST["renseigner_retard"]) ? $_POST["renseigner_retard"] : "n";
$module_edt = isset($_POST["module_edt"]) ? $_POST["module_edt"] : "n";
$memorisation = isset($_POST["memorisation"]) ? $_POST["memorisation"] : "n";
//$ = isset($_POST[""]) ? $_POST[""] : "n";
// ===================== Fin de l'initialisation ============
// On initialise aussi les réglages et les checked pour l'affichage
$test_query = array();
$test_query[0]["value"] = $date_phase1;
$test_query[0]["name"] = "date_phase1";

$test_query[1]["value"] = $liste_absents;
$test_query[1]["name"] = "liste_absents";

$test_query[2]["value"] = $voir_fiche_eleve;
$test_query[2]["name"] = "voir_fiche_eleve";

$test_query[3]["value"] = $renseigner_retard;
$test_query[3]["name"] = "renseigner_retard";

$test_query[4]["value"] = $module_edt;
$test_query[4]["name"] = "module_edt";

$test_query[5]["value"] = $memorisation;
$test_query[5]["name"] = "memorisation";

// Traitement des réglages
if ($clic == "ok") {
				$message = "";
	// On fait tous les tests pour tous les réglages
	for($a = 0; $a < 6; $a++) {
		$query = mysql_query("SELECT value FROM setting WHERE name = '".$test_query[$a]."'");
		$verif = mysql_num_rows($query);

			// Si le setting n'existe pas, on le crée
		if ($verif == 0) {
			$creationSetting = mysql_query("INSERT INTO setting (name, value) values ('".$test_query[$a]["name"]."', '".$test_query[$a]["value"]."')");
			// On recharge la requête car elle a été mise à jour
			$query = mysql_query("SELECT value FROM setting WHERE name = '".$test_query[$a]."'");
		}
		// et on récupère sa valeur
		$rep_phase1 = mysql_fetch_array($query);

		if ($rep_phase1["value"] == $test_query[$a]["value"]) {
		// On ne fait rien
		}else {
			$modif = mysql_query("UPDATE setting SET value = '".$test_query[$a]["value"]."' WHERE name = '".$test_query[$a]["name"]."'");
			if (!$modif) {
				$message .= "<p style=\"color: red;\">An error occured when saving !(".$test_query[$a]["name"].")</p>";
			}else{
				$message .= "<p style=\"color: green;\">The change is recorded(".$test_query[$a]["name"].")</p>";
			}
		}
	}
	$test_query[0]["checked"] = isset($date_phase1) ? $date_phase1 : getSettingValue("date_phase1");
	$test_query[1]["checked"] = isset($liste_absents) ? $liste_absents : getSettingValue("liste_absents");
	$test_query[2]["checked"] = isset($voir_fiche_eleve) ? $voir_fiche_eleve : getSettingValue("voir_fiche_eleve");
	$test_query[3]["checked"] = isset($renseigner_retard) ? $renseigner_retard : getSettingValue("renseigner_retard");
	$test_query[4]["checked"] = isset($module_edt) ? $module_edt : getSettingValue("module_edt");
	$test_query[5]["checked"] = isset($memorisation) ? $memorisation : getSettingValue("memorisation");
}

$test_query[0]["checked"] = isset($_POST["date_phase1"]) ? $_POST["date_phase1"] : getSettingValue("date_phase1");
$test_query[1]["checked"] = isset($_POST["liste_absents"]) ? $_POST["liste_absents"] : getSettingValue("liste_absents");
$test_query[2]["checked"] = isset($_POST["voir_fiche_eleve"]) ? $_POST["voir_fiche_eleve"] : getSettingValue("voir_fiche_eleve");
$test_query[3]["checked"] = isset($_POST["renseigner_retard"]) ? $_POST["renseigner_retard"] : getSettingValue("renseigner_retard");
$test_query[4]["checked"] = isset($_POST["module_edt"]) ? $_POST["module_edt"] : getSettingValue("module_edt");
$test_query[5]["checked"] = isset($_POST["memorisation"]) ? $_POST["memorisation"] : getSettingValue("memorisation");

// On décide de l'affichage des checked
		$checked = array();
for($a = 0; $a < 6; $a++) {
	if ($test_query[$a]["checked"] == "n") {
		$checked[$a] = '';
	}else{
		$checked[$a] = ' checked="checked"';
	}
}

// ===================== Header et ses réglages =============
$titre_page = "The input interface of absences";
require_once("../../lib/header.inc");
// ===================== fin du header ======================

?>
<p class="bold">
	<a href="./index.php">
		<img src="../../images/icons/back.png" alt="Retour" class="back_link" />
		 Return
	</a>
</p>
<h2>Setting the display interface for entering teacher absences</h2>

<form name="interface_prof" action="interface_abs.php" method="post">
<p>The teacher can :</p>
	<fieldset id="datesCreneaux">
		<legend>Dates and slots</legend>
			<p>
				<input type="checkbox" id="datePhase1" name="date_phase1" value="y"<?php echo $checked[0]; ?> />
				<label for="datePhase1">Change the date of entry of absences.</label>
			</p>
	</fieldset>
	<fieldset id="absencesEtFiches">
		<legend>The lists of absent</legend>
			<p>
				<input type="checkbox" id="listeAbsents" name="liste_absents" value="y"<?php echo $checked[1]; ?> />
				<label for="listeAbsents">See list of missing the entire property</label>
			</p>
			<p>
				<input type="checkbox" id="voirFicheEleve" name="voir_fiche_eleve" value="y"<?php echo $checked[2]; ?> />
				<label for="voirFicheEleve">See the personal record of each student</label>
			</p>
			<p>
				<input type="checkbox" id="renseignerRetard" name="renseigner_retard" value="y"<?php echo $checked[3]; ?> />
				<label for="renseignerRetard">Inform the delay of a student</label>
			</p>

	</fieldset>
	<fieldset id="utilisationEdt">
		<legend>Course management</legend>
			<p>
				<input type="checkbox" id="moduleEdt" name="module_edt" value="y"<?php echo $checked[4]; ?> />
				<label for="moduleEdt">Use the module timetable (to
initialized by the administrator).</label>
			</p>
			<p>
				<input type="checkbox" id="memorisationY" name="memorisation" value="y"<?php echo $checked[5]; ?> />
				<label for="memorisationY">Use the memory module absences.</label>
			</p>
			<p>It is better to choose one of two modes, even if both are used in parallel .</p>
	</fieldset>
	<input type="hidden" name="clic" value="ok" />
	<input type="submit" name="valider" value="Enregistrer" />

</form>
<?php echo $message;
// le footer
require_once("../../lib/footer.inc.php")
?>