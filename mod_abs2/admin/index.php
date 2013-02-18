<?php
/*
 *
 *$Id: index.php 8056 2011-08-30 20:43:42Z jjacquard $
 *
 * Copyright 2010-2011 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
//include("../lib/functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

$retour=$_SESSION['retour'];
$_SESSION['retour']=$_SERVER['PHP_SELF'] ;

$msg = '';

if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {

		if (isset($_POST['activer'])) {
			if (!saveSetting("active_module_absence", $_POST['activer'])) {
				$msg = "Error during the recording of the parameter activation/desactivation !";
			}
		}
		if (isset($_POST['activer_prof'])) {
			if (!saveSetting("active_module_absence_professeur", $_POST['activer_prof'])) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the seizure by the professors !";
			}
		}
		if (isset($_POST['activer_resp'])) {
			if (!saveSetting("active_absences_parents", $_POST['activer_resp'])) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the consultation by the responsible pupils !";
			}
		}
		if (isset($_POST['gepiAbsenceEmail'])) {
			if (!saveSetting("gepiAbsenceEmail", $_POST['gepiAbsenceEmail'])) {
				$msg = "Error during the recording of the parameter management absence email !";
			}
		}
		if (isset($_POST['abs2_sms_prestataire'])) {
			if (!saveSetting("abs2_sms_prestataire", $_POST['abs2_sms_prestataire'])) {
				$msg = "Error during the recording of the parameter providing sms !";
			}
		}
		if (isset($_POST['abs2_sms_username'])) {
			if (!saveSetting("abs2_sms_username", $_POST['abs2_sms_username'])) {
				$msg = "Error during the recording of the name of user providing sms !";
			}
		}
		if (isset($_POST['abs2_sms_password'])) {
			if (!saveSetting("abs2_sms_password", $_POST['abs2_sms_password'])) {
				$msg = "Error during the recording of the password providing sms !";
			}
		}
		if (isset($_POST['abs2_retard_critere_duree'])) {
			if (!saveSetting("abs2_retard_critere_duree", $_POST['abs2_retard_critere_duree'])) {
				$msg = "Error during the recording of abs2_retard_critere_duree !";
			}
		}
		if (isset($_POST['abs2_heure_demi_journee'])) {
			try {
				$heure = new DateTime($_POST['abs2_heure_demi_journee']);
				if (!saveSetting("abs2_heure_demi_journee", $heure->format('H:i'))) {
					$msg = "Error during the recording of abs2_heure_demi_journee !";
				}
			} catch (Exception $x) {
				$message_enregistrement .= "Bad format of hour.<br/>";
			}
		}

		if (isset($_POST['abs2_alleger_abs_du_jour'])) {
			if (!saveSetting("abs2_alleger_abs_du_jour", $_POST['abs2_alleger_abs_du_jour'])) {
				$msg = "Error during the recording of the parameter abs2_alleger_abs_du_jour";
			}
		} else {
			if (!saveSetting("abs2_alleger_abs_du_jour", 'n')) {
				$msg = "Error during the recording of the parameter abs2_alleger_abs_du_jour";
			}
		}

		if (isset($_POST['abs2_import_manuel_bulletin'])) {
			if (!saveSetting("abs2_import_manuel_bulletin", $_POST['abs2_import_manuel_bulletin'])) {
				$msg = "Error during the recording of the parameter abs2_import_manuel_bulletin";
			}
		} else {
			if (!saveSetting("abs2_import_manuel_bulletin", 'n')) {
				$msg = "Error during the recording of the parameter abs2_import_manuel_bulletin";
			}
		}

                if (isset($_POST['abs2_saisie_prof_decale_journee'])) {
			if (!saveSetting("abs2_saisie_prof_decale_journee", $_POST['abs2_saisie_prof_decale_journee'])) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the seizure shifted over the day for the professors !";
			}
		} else {
			if (!saveSetting("abs2_saisie_prof_decale_journee", 'n')) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the seizure shifted over the day for the professors !";
			}
		}

		if (isset($_POST['abs2_saisie_prof_decale'])) {
			if (!saveSetting("abs2_saisie_prof_decale", $_POST['abs2_saisie_prof_decale'])) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the unbounded seizure shifted for the professors !";
			}
		} else {
			if (!saveSetting("abs2_saisie_prof_decale", 'n')) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the unbounded seizure shifted for the professors !";
			}
		}

		if (isset($_POST['abs2_saisie_prof_hors_cours'])) {
			if (!saveSetting("abs2_saisie_prof_hors_cours", $_POST['abs2_saisie_prof_hors_cours'])) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the seizure by the professors except course envisaged !";
			}
		} else {
			if (!saveSetting("abs2_saisie_prof_hors_cours", 'n')) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the seizure by the professors except course envisaged!";
			}
		}

		if (isset($_POST['abs2_modification_saisie_une_heure'])) {
			if (!saveSetting("abs2_modification_saisie_une_heure", $_POST['abs2_modification_saisie_une_heure'])) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the modification seized by the professors in the hour following the seizure !";
			}
		} else {
			if (!saveSetting("abs2_modification_saisie_une_heure", 'n')) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the modification seized by the professors in the hour following the
seizure!";
			}
		}

		if (isset($_POST['abs2_sms'])) {
			if (!saveSetting("abs2_sms", $_POST['abs2_sms'])) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the modification seized by the professors in the hour following the
seizure!";
			}
		} else {
			if (!saveSetting("abs2_sms", 'n')) {
				$msg = "Error during the recording of the parameter activation/desactivation
of the modification seized by the professors in the hour following the
seizure !";
			}
		}

		if (isset($_POST['abs2_saisie_par_defaut_sans_manquement'])) {
			if (!saveSetting("abs2_saisie_par_defaut_sans_manquement", $_POST['abs2_saisie_par_defaut_sans_manquement'])) {
				$msg = "Error during the recording of the parameter abs2_saisie_par_defaut_sans_manquement";
			}
		} else {
			if (!saveSetting("abs2_saisie_par_defaut_sans_manquement", 'n')) {
				$msg = "Error during the recording of the parameter abs2_saisie_par_defaut_sans_manquement";
			}
		}

		if (isset($_POST['abs2_saisie_multi_type_sans_manquement'])) {
			if (!saveSetting("abs2_saisie_multi_type_sans_manquement", $_POST['abs2_saisie_multi_type_sans_manquement'])) {
				$msg = "Error during the recording of the parameter abs2_saisie_multi_type_sans_manquement !";
			}
		} else {
			if (!saveSetting("abs2_saisie_multi_type_sans_manquement", 'n')) {
				$msg = "Error during the recording of the parameter abs2_saisie_multi_type_sans_manquement !";
			}
		}

		if (isset($_POST['abs2_saisie_par_defaut_sous_responsabilite_etab'])) {
			if (!saveSetting("abs2_saisie_par_defaut_sous_responsabilite_etab", $_POST['abs2_saisie_par_defaut_sous_responsabilite_etab'])) {
				$msg = "Error during the recording of the parameter abs2_saisie_par_defaut_sous_responsabilite_etab !";
			}
		} else {
			if (!saveSetting("abs2_saisie_par_defaut_sous_responsabilite_etab", 'n')) {
				$msg = "Error during the recording of the parameter abs2_saisie_par_defaut_sous_responsabilite_etab !";
			}
		}

		if (isset($_POST['abs2_saisie_multi_type_sous_responsabilite_etab'])) {
			if (!saveSetting("abs2_saisie_multi_type_sous_responsabilite_etab", $_POST['abs2_saisie_multi_type_sous_responsabilite_etab'])) {
				$msg = "Error during the recording of the parameter abs2_saisie_multi_type_sous_responsabilite_etab !";
			}
		} else {
			if (!saveSetting("abs2_saisie_multi_type_sous_responsabilite_etab", 'n')) {
				$msg = "Error during the recording of the parameter abs2_saisie_multi_type_sous_responsabilite_etab !";
			}
		}

		if (isset($_POST['abs2_saisie_multi_type_non_justifiee'])) {
			if (!saveSetting("abs2_saisie_multi_type_non_justifiee", $_POST['abs2_saisie_multi_type_non_justifiee'])) {
				$msg = "Error during the recording of the parameter abs2_saisie_multi_type_non_justifiee !";
			}
		} else {
			if (!saveSetting("abs2_saisie_multi_type_non_justifiee", 'n')) {
				$msg = "Error during the recording of the parameter abs2_saisie_multi_type_non_justifiee !";
			}
		}
//		if (isset($_POST['abs2_modification_saisie_sans_limite'])) {
//			if (!saveSetting("abs2_modification_saisie_sans_limite", $_POST['abs2_modification_saisie_sans_limite'])) {
//				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la modification sasie par les professeurs dans l'heure suivant la saisie !";
//			}
//		} else {
//			if (!saveSetting("abs2_modification_saisie_sans_limite", 'n')) {
//				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la modification sasie par les professeurs dans l'heure suivant la saisie !";
//			}
//		}

	}
}


if (isset($_POST['classement'])) {
	if (!saveSetting("absence_classement_top", $_POST['classement'])) {
		$msg = "Error during the recording of the parameter of classification of the
absences (SIGNAL 10) !";
	}
}
if (isset($_POST['installation_base'])) {
            // Remise à zéro de la table des droits d'accès
	$result = "";
        require '../../utilitaires/updates/access_rights.inc.php';
	require '../../utilitaires/updates/mod_abs2.inc.php';
}

if (isset($_POST['is_posted']) and ($msg=='')) $msg = "The modifications were recorded!";

// A propos du TOP 10 : récupération du setting pour le select en bas de page
$selected10 = $selected20 = $selected30 = $selected40 = $selected50 = NULL;

if (getSettingValue("absence_classement_top") == '10'){
  $selected10 = ' selected="selected"';
}elseif (getSettingValue("absence_classement_top") == '20') {
  $selected20 = ' selected="selected"';
}elseif (getSettingValue("absence_classement_top") == '30') {
  $selected30 = ' selected="selected"';
}elseif (getSettingValue("absence_classement_top") == '40') {
  $selected40 = ' selected="selected"';
}elseif (getSettingValue("absence_classement_top") == '50') {
  $selected50 = ' selected="selected"';
}

// header
$titre_page = "Management of the module absence";
require_once("../../lib/header.inc");


echo "<p class='bold'><a href=\"../../accueil_modules.php\"><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>";
echo "</p>";
    if (isset ($result)) {
	    echo "<center><table width=\"80%\" border=\"1\" cellpadding=\"5\" cellspacing=\"1\" summary='Result of update'><tr><td><h2 align=\"center\">Result of the update</h2>";
	    echo $result;
	    echo "</td></tr></table></center>";
    }
?>
<h2>Management of the absences by the CPE</h2>
<p style="font-style: italic;">The desactivation of the module of the management of the absences does not involve any suppression of the data. When the module is decontaminated, the CPE do not have access to the
module.</p>

<form action="index.php" name="form1" method="post">
<?php
echo add_token_field();
?>
<p>
	<input type="radio" id="activerY" name="activer" value="y"
	<?php if (getSettingValue("active_module_absence")=='y') echo ' checked="checked"'; ?> />
	<label for="activerY">&nbsp;Activate the module of the management of the absences</label>
</p>
<p>
	<input type="radio" id="activer2" name="activer" value="2"
	<?php if (getSettingValue("active_module_absence")=='2') echo ' checked="checked"'; ?> />
	<label for="activer2">&nbsp;Activate the module of the management of the absences version 2</label>
</p>
<p>
	<input type="radio" id="activerN" name="activer" value="n"
	<?php if (getSettingValue("active_module_absence")=='n') echo ' checked="checked"'; ?> />
	<label for="activerN">&nbsp;Deactivate the module of the management of the absences</label>
	<input type="hidden" name="is_posted" value="1" />
</p>
<p>
	<input type="checkbox" name="abs2_import_manuel_bulletin" value="y"
	<?php if (getSettingValue("abs2_import_manuel_bulletin")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_import_manuel_bulletin">&nbsp;Use an importation (manual, gep or sconet) for the bulletins and cards raises.</label>
</p>
<p>
	<input type="checkbox" name="abs2_alleger_abs_du_jour" value="y"
	<?php if (getSettingValue("abs2_alleger_abs_du_jour")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_alleger_abs_du_jour">&nbsp;Alleger calculations of the absence page of the day: Deactivate the research of the contradictory seizures and presences.</label>
</p>
<p>
E-mail management establishment absence :
<input type="text" name="gepiAbsenceEmail" size="20" value="<?php echo(getSettingValue("gepiAbsenceEmail")); ?>"/>
</p>

<h2>Absences by the teachers</h2>
<p style="font-style: italic;">The desactivation of the module of the management of the absences does not involve any suppression of the data seized by the professors. When the module is deactivate, the professors do not have access to the module.
Normally, this module should be activated only if the module above itself is activated.</p>
<p>
	<input type="radio" id="activerProfY" name="activer_prof" value="y"
	<?php if (getSettingValue("active_module_absence_professeur")=='y') echo " checked='checked'"; ?> />
	<label for="activerProfY">&nbsp;Activate the module of the seizure of the absences by the
professors</label>
</p>
<p>
	<input type="radio" id="activerProfN" name="activer_prof" value="n"
	<?php if (getSettingValue("active_module_absence_professeur")=='n') echo " checked='checked'"; ?> />
	<label for="activerProfN">&nbsp;Deactivate the module of the seizure of the absences by the professors</label>
	<input type="hidden" name="is_posted" value="1" />
</p>
<p>
	<input type="checkbox" name="abs2_saisie_prof_decale_journee" value="y"
	<?php if (getSettingValue("abs2_saisie_prof_decale_journee")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_prof_decale_journee">&nbsp;Allow the seizure shifted over the same day by the professors</label>
</p>
<p>
	<input type="checkbox" name="abs2_saisie_prof_decale" value="y"
	<?php if (getSettingValue("abs2_saisie_prof_decale")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_prof_decale">&nbsp;Allow the unbounded shifted seizure of time by the professors</label>
</p>
<p>
	<input type="checkbox" name="abs2_saisie_prof_hors_cours" value="y"
	<?php if (getSettingValue("abs2_saisie_prof_hors_cours")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_prof_hors_cours">&nbsp;Allow the seizure of an absence out of the courses envisaged in the timetable of the professor</label>
</p>
<p>
	<input type="checkbox" name="abs2_modification_saisie_une_heure" value="y"
	<?php if (getSettingValue("abs2_modification_saisie_une_heure")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_modification_saisie_une_heure">&nbsp;Allow the modification of a seizure by the professor in the hour which followed its creation</label>
</p>
<!--p>
	<input type="checkbox" name="abs2_modification_saisie_sans_limite" value="y"
	<?php //if (getSettingValue("abs2_modification_saisie_sans_limite")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_modification_saisie_sans_limite">&nbsp;Permettre la modification d'une saisie sans limite de temps</label>
</p-->

<h2>Sending of the SMS</h2>
<p>
	<input type="checkbox" id="abs2_sms" name="abs2_sms" value="y"
	<?php if (getSettingValue("abs2_sms")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_sms">&nbsp;Activate the sending of the sms</label>
</p>
<?php
  $extensions = get_loaded_extensions();
  if(!in_array('curl',$extensions)) {
      echo "<p style='font-style: italic; color:red'>CAUTION: It seems that your waiter is not configured for the sending of SMS. This functionality nécéssite extension PHP CURL.";
      echo "</p>";
  };
 ?>
<p>
    <label for="abs2_sms_prestataire">Choose a person receiving benefits</label>
	<select id="abs2_sms_prestataire" name="abs2_sms_prestataire">
	<option value=''></option>
	<option value='tm4b' <?php if (getSettingValue("abs2_sms_prestataire")=='tm4b') echo " selected "; ?> >www.tm4b.com</option>
    <option value='pluriware' <?php if (getSettingValue("abs2_sms_prestataire")=='pluriware') echo " selected "; ?> >Pluriware (agréée EN)</option>
	<option value='123-sms' <?php if (getSettingValue("abs2_sms_prestataire")=='123-sms') echo " selected "; ?> >www.123-sms.net</option>
	</select><br/>
	Name of user of the service <input type="text" name="abs2_sms_username" size="20" value="<?php echo(getSettingValue("abs2_sms_username")); ?>"/><br/>
	Password <input type="text" name="abs2_sms_password" size="20" value="<?php echo(getSettingValue("abs2_sms_password")); ?>"/><br/>
</p>

<h2>Configuration of the seizures</h2>
<p>
	<input type="checkbox" id="abs2_saisie_par_defaut_sans_manquement" name="abs2_saisie_par_defaut_sans_manquement" value="y"
	<?php if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_par_defaut_sans_manquement">&nbsp;In the case of a seizure without type, to consider that the pupil does not fail to fulfil his obligations.
	   (Thus these seizures will not be counted in the bulletins)</label>
</p>
<p>
	<input type="checkbox" id="abs2_saisie_multi_type_sans_manquement" name="abs2_saisie_multi_type_sans_manquement" value="y"
	<?php if (getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_multi_type_sans_manquement">&nbsp;In the case of several simultaneous seizures with contradictory types,
to consider that thestudent does not fail to fulfil his obligations.
	   (Thus these seizures will not be counted in the bulletins)</label>
</p>
<p>
	<input type="checkbox" id="abs2_saisie_multi_type_non_justifiee" name="abs2_saisie_multi_type_non_justifiee" value="y"
	<?php if (getSettingValue("abs2_saisie_multi_type_non_justifiee")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_multi_type_non_justifiee">&nbsp;In the case of several simultaneous seizures with contradictory types,
to consider that the seizure is not justified.</label>
</p>
<p>
	<input type="checkbox" id="abs2_saisie_par_defaut_sous_responsabilite_etab" name="abs2_saisie_par_defaut_sous_responsabilite_etab" value="y"
	<?php if (getSettingValue("abs2_saisie_par_defaut_sous_responsabilite_etab")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_par_defaut_sous_responsabilite_etab">&nbsp;In the case of a seizure without type, to consider that the pupil is
by defect under the responsibility of the establishment.</label>
</p>
<p>
	<input type="checkbox" id="abs2_saisie_multi_type_sous_responsabilite_etab" name="abs2_saisie_multi_type_sous_responsabilite_etab" value="y"
	<?php if (getSettingValue("abs2_saisie_multi_type_sous_responsabilite_etab")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_multi_type_sous_responsabilite_etab">&nbsp;In the case of several simultaneous seizures with contradictory types,
to consider that the pupil is by defect under the responsibility of
the establishment.</label>
</p>
<p>
	<?php if (getSettingValue("abs2_retard_critere_duree") == null || getSettingValue("abs2_retard_critere_duree") == '') saveSetting("abs2_retard_critere_duree", 30); ?>
	Configuration of the bulletin: In the calculation of the half-days of
absence, to consider the seizure lower than
	<select name="abs2_retard_critere_duree">
		<option value="00" <?php if (getSettingValue("abs2_retard_critere_duree") == '00') echo " selected"; ?>>00</option>
		<option value="10" <?php if (getSettingValue("abs2_retard_critere_duree") == '10') echo " selected"; ?>>10</option>
		<option value="20" <?php if (getSettingValue("abs2_retard_critere_duree") == '20') echo " selected"; ?>>20</option>
		<option value="30" <?php if (getSettingValue("abs2_retard_critere_duree") == '30') echo " selected"; ?>>30</option>
		<option value="40" <?php if (getSettingValue("abs2_retard_critere_duree") == '40') echo " selected"; ?>>40</option>
		<option value="50" <?php if (getSettingValue("abs2_retard_critere_duree") == '50') echo " selected"; ?>>50</option>
	</select>
	min like delays.<br/>
	Note: if the crenels last 45 minutes and that this parameter is
regulated on 50 min, the majority of your seizures will be deducted
like delay.<br/>
	Note: are regarded as delay the seizures of durations lower than the
parameter above and the seizures whose type is deducted like delay
	(see the page <a href="admin_types_absences.php?action=visualiser">To define the types of absence</a>).<br/>

</p>
<br/>
<p>
	<?php if (getSettingValue("abs2_heure_demi_journee") == null || getSettingValue("abs2_heure_demi_journee") == '') saveSetting("abs2_heure_demi_journee", '11:50'); ?>
	<input style="font-size:88%;" name="abs2_heure_demi_journee" value="<?php echo getSettingValue("abs2_heure_demi_journee")?>" type="text" maxlength="5" size="4"/>
	Hour of rocker of half-day for the calculation of the half-days
</p>

<!--h2>G&eacute;rer l'acc&egrave;s des responsables d'&eacute;l&egrave;ves</h2>
<p style="font-style: italic">Vous pouvez permettre aux responsables d'acc&eacute;der aux donn&eacute;es brutes
entr&eacute;es dans Gepi par le biais du module absences.</p>
<p>
	<input type="radio" id="activerRespOk" name="activer_resp" value="y"
	<?php if (getSettingValue("active_absences_parents") == 'y') echo ' checked="checked"'; ?> />
	<label for="activerRespOk">Permettre l'acc&egrave;s aux responsables</label>
</p>
<p>
	<input type="radio" id="activerRespKo" name="activer_resp" value="n"
	<?php if (getSettingValue("active_absences_parents") == 'n') echo ' checked="checked"'; ?> />
	<label for="activerRespKo">Ne pas permettre cet acc&egrave;s</label>
</p-->
	
<br/>
<div class="centre"><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></div>

</form>

<br/><br/>
<h2>Advanced configuration</h2>
<blockquote>
	<a href="admin_types_absences.php?action=visualiser">Define the types of absence</a><br />
	<a href="admin_motifs_absences.php?action=visualiser">Define the reasons for the absences</a><br />
    <a href="admin_lieux_absences.php?action=visualiser">Define the places of the absences</a><br />
	<a href="admin_justifications_absences.php?action=visualiser">Define the justifications</a><br />
	<a href="../../mod_ooo/gerer_modeles_ooo.php">Manage its own models of documents of the module</a><br />
</blockquote>

<?PHP
require("../../lib/footer.inc.php");
?>
