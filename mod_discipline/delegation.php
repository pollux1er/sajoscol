<?php

/*
 * $Id: delegation.php 7138 2011-06-05 17:37:14Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Didier Blanqui
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

$variables_non_protegees = 'yes';
// Initialisations files
require_once("../lib/initialisations.inc.php");
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
	}
	else { 
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
/*
$sql = "SELECT 1=1 FROM `droits` WHERE id='/mod_discipline/delegation.php';";
$test = mysql_query($sql);
if (mysql_num_rows($test) == 0) {
    $sql = "INSERT INTO droits VALUES ( '/mod_discipline/delegation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les délégations pour exclusion temporaire', '')";
    $test = mysql_query($sql);
}
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/delegation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les délégations pour exclusion temporaire', '');";
*/
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("You try to reach the Discipline module which is decontaminated !");
	tentative_intrusion(1, "Attempt at access to the Discipline module which is decontaminated.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$msg = "";

$cpt = isset($_POST['cpt']) ? $_POST['cpt'] : 0;
$suppr_delegation = isset($_POST['suppr_delegation']) ? $_POST['suppr_delegation'] : NULL;
$fct_delegation=isset($_POST['fct_delegation']) ? $_POST['fct_delegation'] : NULL;
$fct_autorite=isset($_POST['fct_autorite']) ? $_POST['fct_autorite'] : NULL;
$nom_autorite=isset($_POST['nom_autorite']) ? $_POST['nom_autorite'] : NULL;

if (isset($NON_PROTECT["fct_delegation"])){
			$fct_delegation=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["fct_delegation"]));
			// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
			$fct_delegation=preg_replace('/(\\\r\\\n)+/',"\r\n",$fct_delegation);
			$fct_delegation=preg_replace('/(\\\r)+/',"\r",$fct_delegation);
			$fct_delegation=preg_replace('/(\\\n)+/',"\n",$fct_delegation);
		}
		else {
			$fct_delegation="";
		}

if (isset($suppr_delegation)) {
	check_token();
    for ($i = 0; $i < $cpt; $i++) {
        if (isset($suppr_delegation[$i])) {
            $sql = "DELETE FROM s_delegation WHERE id_delegation='$suppr_delegation[$i]';";
			//echo $sql;
            $suppr = mysql_query($sql);updateOnline($sql);
            if (!$suppr) {
                $msg.="ERROR during the removal of the delegation n°" . $suppr_delegation[$i] . ".<br />\n";
            } else {
                  $msg.="Removal of the delegation n°" . $suppr_delegation[$i] . ".<br />\n";
                 $sql = "UPDATE s_exclusions SET id_signataire=0 WHERE id_signataire=" . $suppr_delegation[$i] . ";";
                 $res = mysql_query($sql);updateOnline($sql);
                if (!$res) {
                    $msg.="ERROR at the time of the update the delegation with marked exclusions ! <br />\n";
                } else {
                    $msg.="Update of the delegation to marked exclusions carried out.<br />\n";
                }
            }
        }
    }
}

if ((isset($fct_autorite)) && ($fct_autorite != '')) {
	check_token();

    $a_enregistrer = 'y';

    $sql = "SELECT fct_autorite FROM s_delegation ORDER BY fct_autorite;";
	//echo $sql;
    $res = mysql_query($sql);
    if (mysql_num_rows($res) > 0) {
        $tab_delegation = array();
        while ($lig = mysql_fetch_object($res)) {
            $tab_delegation[] = $lig->fct_autorite;
        }

        if (in_array($delegation, $tab_delegation)) {
            $a_enregistrer = 'n';
        }
    }
	
    if ($a_enregistrer == 'y') {
        $sql = "INSERT INTO s_delegation SET fct_delegation='" . $fct_delegation . "', fct_autorite='" . $fct_autorite . "', nom_autorite='" . $nom_autorite. "';";
		
        $res = mysql_query($sql);updateOnline($sql);
        if (!$res) {
            $msg.="ERROR during the recording of " . $fct_autorite . "<br />\n";
        } else {
            $msg.="Recording of " . $fct_autorite . "<br />\n";
        }
    }
}

$themessage = 'Information was modified. do  you want to really leave without recording ?';
//**************** EN-TETE *****************
//$titre_page = "Sanctions: Définition des qualités";
$titre_page = "Discipline: Management of the delegations of temporary exclusion";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Returnr</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='" . $_SERVER['PHP_SELF'] . "' method='post' name='formulaire'>\n";
echo add_token_field();

//echo "<p class='bold'>Saisie des qualités dans un incident&nbsp;:</p>\n";
echo "<p class='bold'>Seizure of the delegations&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt = 0;
$sql = "SELECT * FROM s_delegation ORDER BY id_delegation;";
$res = mysql_query($sql);
if (mysql_num_rows($res) == 0) {
    //echo "<p>Aucune qualité n'est encore définie.</p>\n";
    echo "<p>No delegation is yet defined.</p>\n";
} else {
    //echo "<p>Qualités existantes&nbsp;:</p>\n";
    echo "<p>Existing delegations&nbsp;:</p>\n";
    echo "<table class='boireaus' border='1' summary='Table of the existing delegations'>\n";
    echo "<tr>\n";
    echo "<th>Text of delegation of the head of establishment</th>\n";
    echo "<th>Function of the authority signatory</th>\n";
	echo "<th>Name of the authority signatory</th>\n";
    echo "<th>Remove</th>\n";
    echo "</tr>\n";
    $alt = 1;
	
    while ($lig = mysql_fetch_object($res)) {
        $alt = $alt * (-1);
        echo "<tr class='lig$alt'>\n";

        echo "<td>\n";
        echo "<label for='suppr_delegation_$cpt' style='cursor:pointer;'>";
        echo $lig->fct_delegation;
        echo "</label>";
        echo "</td>\n";

        echo "<td>\n";
        echo "<label for='suppr_delegation_$cpt' style='cursor:pointer;'>";
        echo $lig->fct_autorite;
        echo "</label>";
        echo "</td>\n";
		
		echo "<td>\n";
        echo "<label for='suppr_delegation_$cpt' style='cursor:pointer;'>";
        echo $lig->nom_autorite;
        echo "</label>";
        echo "</td>\n";


        echo "<td><input type='checkbox' name='suppr_delegation[]' id='suppr_delegation_$cpt' value=\"$lig->id_delegation\" onchange='changement();' /></td>\n";
        echo "</tr>\n";

        $cpt++;
    }

    echo "</table>\n";
}
	echo "</blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Seizure of information of delegation'>\n";
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Text of the delegation of the head of establishment&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<textarea name='no_anti_inject_fct_delegation' cols='50' onchange='changement();'></textarea>\n";
	echo "<i>(facultatif) Ex: For the Head of establishment,</BR></BR>and by delegation,</i></td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Function of the authority signatory&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='fct_autorite' id='fct_autorite' value='' onchange='changement();' />\n";
	echo "<i>Function of the management staff or the délégataire</i></td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Name of the authority signatory&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='nom_autorite' id='nom_autorite' value='' onchange='changement();' />\n";
	echo "<i>Name of the management staff or the délégataire</i></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>