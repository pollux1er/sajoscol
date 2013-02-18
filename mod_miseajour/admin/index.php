<?php
/*
 * $Id: index.php 6585 2011-03-02 17:16:31Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$msg = '';
if (isset($_POST['is_posted'])) {
	check_token();

	if (isset($_POST['activer'])) {
		if (!saveSetting("active_module_msj", $_POST['activer'])) $msg = "Error during the recording of the parameter activation/desactivation !";
	}
	if (isset($_POST['site_msj_gepi'])) {
		if (!saveSetting("site_msj_gepi", $_POST['site_msj_gepi'])) $msg = "Error during the recording of the name of the file of installation of gepi on ftp !";
	}
	if (isset($_POST['dossier_ftp_gepi'])) {
		if (!saveSetting("dossier_ftp_gepi", $_POST['dossier_ftp_gepi'])) $msg = "Error during the recording of the name of the file of installation of gepi on ftp !";
	}
	if (isset($_POST['activer_rc'])) {
		if (!saveSetting("rc_module_msj", $_POST['activer_rc'])) $msg = "Error during the recording of the parameter activation/desactivation of RCs!";
	}
	if (isset($_POST['activer_beta'])) {
		if (!saveSetting("beta_module_msj", $_POST['activer_beta'])) $msg = "Error during the recording of the parameter activation/desactivation of BETAs!";
	}
}

if (isset($_POST['is_posted']) and ($msg=='')) $msg = "The modifications were recorded !";

// ============= header ==============
	// Inclusion du style spécifique
$style_specifique = "/mod_miseajour/lib/style_maj";

$titre_page = "Management of the module of update";
require_once("../../lib/header.inc");
// ============= fin header ==========
?>
<p class="bold">
|<a href="../../accueil.php">Reception</a>|
<a href="../../accueil_modules.php">Return administration of the modules</a>|
</p>
<h2>Management of the update of GEPI</h2>
<p><i>The desactivation of the module of the management of the updates does
not involve any suppression of the data. When the module is decontaminated, the administrators do not have access to the module.</i></p>
<p>Note: the option 'allow_url_fopen' in php.ini must be with 'On' on the waiter so that this module can function.</p>
<br />
<form action="index.php" name="form1" method="post">
<fieldset>
<?php
echo add_token_field();
?>
	<p>
	<input type="radio" id="activMaj" name="activer" value="y" <?php if (getSettingValue("active_module_msj")=='y') echo ' checked="checked"'; ?> />
	<label for="activMaj">&nbsp;Activate the module of update of GEPI</label>
	</p>
	<p>
	<input type="radio" id="desactiMaj" name="activer" value="n" <?php if (getSettingValue("active_module_msj")=='n') echo ' checked="checked"'; ?> />
	<label for="desactiMaj">&nbsp;Deactivate the module of update of GEPI</label>
	</p>
<br />
	<p>By defect, only the stable versions are checked, but you can include
the other versions.</p>
	<p class="decale">
	Display versions RC &nbsp;<a class="info" style="font-weight: bold;">?
		<span style="width: 400px;">Caution version RC are versions of test thus never not to be used in production.</span></a>
		<input type="radio" id="activRc" name="activer_rc" value="y" <?php if (getSettingValue("rc_module_msj")=='y') echo ' checked="checked"'; ?> />
		<label for="actiRc">Activer</label>
		<input type="radio" id="desactivRc" name="activer_rc" value="n" <?php if (getSettingValue("rc_module_msj")=='n') echo ' checked="checked"'; ?> />
		<label for="desactivRc">Deactivate</label>
	</p>
	<p class="decale">
	Display the versions BETA&nbsp;<a class="info" style="font-weight: bold;">?
		<span style="width: 400px;">Caution the version BETA are versions of development thus never not to be used in production.</span></a>
		<input type="radio" id="activBeta" name="activer_beta" value="y" <?php if (getSettingValue("beta_module_msj")=='y') echo ' checked="checked"'; ?> />
		<label for="activBeta">Activate</label>
		<input type="radio" id="desactivBeta" name="activer_beta" value="n" <?php if (getSettingValue("beta_module_msj")=='n') echo ' checked="checked"'; ?> />
		<label for="desactivBeta">Deactivate</label>
	</p>

<h2>Information site of update of GEPI</h2>
	<p class="decale">
	<label for="siteMaj">Address Internet site of update of GEPI</label>
	<input type="text" id="siteMaj" name="site_msj_gepi" value="<?php echo getSettingValue("site_msj_gepi"); ?>" size="40" />
	</p>

<h2>Information waiter ftp</h2>
	<p class="decale">
	<label for="dossierFtp">Name of the file of installation of GEPI on the ftp used</label>
	<input type="text" id="dossierFtp" name="dossier_ftp_gepi" value="<?php echo getSettingValue("dossier_ftp_gepi"); ?>" size="20" />&nbsp; ex: gepi
	</p>
	<p class="decale">
	<input type="hidden" name="is_posted" value="1" />
	<input type="submit" value="Enregistrer" style="font-variant: small-caps;" />
	</p>
</fieldset>
</form>

<br />

<?php
require_once("../../lib/footer.inc.php");
?>

