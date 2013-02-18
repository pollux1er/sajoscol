<?php
/*
 * @version: $Id: modify_limites.php 8496 2011-10-19 16:39:24Z crob $
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
$msg = '';
if (isset($_POST['max_size_ko'])) {
	check_token();
    if (my_ereg ("^[0-9]{1,}$", $_POST['max_size_ko'])) {
        $max_size = $_POST['max_size_ko']*1024;
        if (!saveSetting("max_size", $max_size)) $msg = "Error during the recording of the maximum size authorized for a file !";
    }
}
if (isset($_POST['total_max_size_ko'])) {
	check_token();
    if (my_ereg ("^[0-9]{1,}$", $_POST['total_max_size_ko'])) {
        $total_max_size = $_POST['total_max_size_ko']*1024;
        if (!saveSetting("total_max_size", $total_max_size)) $msg = "Error during the recording of the size of the maximum disk space authorized for a rubric !";
    }
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "The modifications were recorded !";

// header
$titre_page = "Management of the textbooks";
require_once("../lib/header.inc");
?>
<p class=bold>
<a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>
</p>
<H2>Management of the textbooks - Download of documents</H2>
<form action="modify_limites.php" method="post" style="width: 100%;">
<?php
echo add_token_field();
?>
<table style="width: 100%; border: 0;" cellpadding="5" cellspacing="5">
    <tr>
        <td style="font-variant: small-caps;">
        Maximum size authorized for a file in KB :
        </td>
        <td><input type="text" name="max_size_ko" size="20" value="<?php echo(getSettingValue("max_size")/1024); ?>" />
        </td>
    </tr>
    <tr>
        <td style="font-variant: small-caps;">
        Maximum disk space authorized for a rubric :
        </td>
        <td><input type="text" name="total_max_size_ko" size="20" value="<?php echo(getSettingValue("total_max_size")/1024); ?>" />
        </td>
    </tr>
</table>
<input type="hidden" name="is_posted" value="1">
<center><input type="submit" value="Save" style="font-variant: small-caps;"/></center>
</form>

<p><em>NOTE&nbsp;:</em></p>
<p style='margin-left:3em;'>The above selected maximum size for a file can also be restricted by PHP parameter settings  of your server&nbsp;:<br />
<b>upload_max_filesize&nbsp;:</b> <?php echo ini_get('upload_max_filesize');?><br />
<b>post_max_size&nbsp;:</b> <?php echo ini_get('post_max_size');?></p>
<?php require("../lib/footer.inc.php");?>