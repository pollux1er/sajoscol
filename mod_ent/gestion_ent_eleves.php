<?php

/*
 * $Id: gestion_ent_eleves.php 6602 2011-03-03 11:38:21Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Eric Lebrun, St�phane boireau, Julien Jocal
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

// S�curit� suppl�mentaire pour �viter d'aller voir ce fichier si on n'est pas dans un ent
if (getSettingValue("use_ent") != 'y') {
	DIE('Prohibited file.');
}

// ======================= Initialisation des variables ==========================
$aff_erreurs = $aff_logins_m = NULL;
$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$nbre_req = isset($_POST["nbre_req"]) ? $_POST["nbre_req"] : NULL;
//$ = isset($_POST[""]) ? $_POST[""] : NULL;


// ======================= Traitement des donn�es ================================
if ($action == "modifier") {
	check_token();

	// L'utilisateur vient d'envoyer la liste des login � modifier
	for($i = 0; $i < $nbre_req ; $i++){

		$login_a_modifier = isset($_POST["modifier_".$i]) ? $_POST["modifier_".$i] : NULL;
		$id_col1 = isset($_POST["id_".$i]) ? $_POST["id_".$i] : NULL;

		// On met � jour la base
		$sql_u = "UPDATE tempo2 SET col2 = '".$login_a_modifier."' WHERE col1 = '".$id_col1."'";
		$query_u = mysql_query($sql_u) OR DIE('Error in '.$sql_u.'<br />'.mysql_error());

		$aff_logins_m .= '<p>'.$login_a_modifier.' -> '.$id_col1.'</p>';

	}

} else {
	// On r�cup�re les donn�es '�l�ves' de la table eleve
	$sql = "SELECT ID_TEMPO,ELENOM,ELEPRE,ELENOET,ELE_ID,ELESEXE,ELEDATNAIS,ELEDOUBL,ELENONAT,ELEREG,DIVCOD,ETOCOD_EP
									FROM temp_gep_import2
									ORDER BY DIVCOD,ELENOM,ELEPRE";
	$call_data = mysql_query($sql) OR DIE('Erreur dans la requ�te '.$sql.' '.mysql_error());

    $nb = mysql_num_rows($call_data);
    $i = "0";
	$j = 0;
    while ($i < $nb) {
        $req = mysql_query("select col1, col2 from tempo2 where col1 = '$i'");
        $reg_login = @mysql_result($req, 0, 'col2');
		$inc = @mysql_result($req, 0, 'col1');

        $id_tempo = @mysql_result($call_data, $i, "ID_TEMPO");
        $no_gep = @mysql_result($call_data, $i, "ELENONAT");
        $reg_nom = traitement_magic_quotes(corriger_caracteres(@mysql_result($call_data, $i, "ELENOM")));
        $reg_prenom = @mysql_result($call_data, $i, "ELEPRE");
        $reg_elenoet = @mysql_result($call_data, $i, "ELENOET");
        //$reg_ereno = @mysql_result($call_data, $i, "ERENO");
        $reg_ele_id = @mysql_result($call_data, $i, "ELE_ID");
        $reg_sexe = @mysql_result($call_data, $i, "ELESEXE");
        $reg_naissance = @mysql_result($call_data, $i, "ELEDATNAIS");
        $reg_doublant = @mysql_result($call_data, $i, "ELEDOUBL");

        // si le login comporte le motif 'erreur', alors on affiche
        if (strpos($reg_login, "erreur") === false) {
        	// On ne fait rien
        }else{
        	// On v�rifie quand m�me si il n'y a pas un nom qui correspond � celui-ci dans ldap_bx
        	$sql_r = "SELECT login_u, nom_u, prenom_u FROM ldap_bx WHERE nom_u = '".$reg_nom."' AND prenom_u = '".$reg_prenom."' AND statut_u = 'student'";
        	$query_r = mysql_query($sql_r);
        	$nbre_v = mysql_num_rows($query_r);
        	$result_r = mysql_fetch_array($query_r);
        	if (isset($result_r["login_u"]) AND $nbre_v <= 1) {
        		$aff_rep_r = $result_r["login_u"];
        		$aff_rep_nomprenom_r = '--> ?? (' . $result_r["nom_u"] . '&nbsp;' . $result_r["prenom_u"] . ')';
        	}else{
        		// On teste avec seulement le nom
        		$sql_r2 = "SELECT login_u, nom_u, prenom_u FROM ldap_bx WHERE nom_u = '".trim($reg_nom)."' AND statut_u = 'student'";
        		$query_r2 = mysql_query($sql_r2);
        		$nbre_v2 = mysql_num_rows($query_r2);
        		$result_r2 = mysql_fetch_array($query_r2);
        		if (isset($result_r2["login_u"]) AND $nbre_v2 <= 1) {
        			$aff_rep_r = 'With VERIFIER';
        			$aff_rep_nomprenom_r = '<span style="color: red;">--> ' . $result_r2["login_u"] . ' (' . $result_r2["nom_u"] . '&nbsp;' . $result_r2["prenom_u"] . ')</span>';
        		}
				$aff_rep_r = $reg_login;
				$aff_rep_nomprenom_r = '(No the response on the name first name)';
			}

			$aff_erreurs .= '
			<p>An error on this student : '.$reg_nom.' '.$reg_prenom.' (num. int. '.$reg_eleonet.') - '.$reg_sexe.' - '.$reg_naissance.'
			<input type="text" name="modifier_'.$j.'" value="'.$aff_rep_r.'" />'.$aff_rep_nomprenom_r.'
			<input type="hidden" name="id_'.$j.'" value="'.$inc.'" />.</p>';
			$j++;
		}
		$i++;
    }
}

// =========== fichiers sp�ciaux ==========
$style_specifique = "edt_organisation/style_edt";
//**************** EN-TETE *****************
$titre_page = "Management of the errors of login of the ENT";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var(); // � enlever en production
?>

<!-- page de gestion des erreurs -->

<h3>List logins which were not found by Gepi in the base of the ENT</h3>

<p>You will be able to modify with the hand the good login for each
student</p>

<form id="gestErreur" action="gestion_ent_eleves.php" method="post">
	<fieldset id="affErreurs">
		<legend>List accounts to be modified</legend>

	<?php
		echo $aff_erreurs;
		if ($action == "modifier") { echo $aff_logins_m;}

		echo add_token_field();
	?>

	</fieldset>

	<p>

	<?php
	if ($action != "modifier") {
		echo '
			<input type="hidden" name="nbre_req" value="'.$j.'" />
			<input type="hidden" name="action" value="modify" />
		<input type="submit" name="enregistrer" value="update the list of the logins" />';
	}else{
		echo '<input type="submit" name="rien" value="To return to the checking" />
		';
	} ?>
	</p>
</form>

<?php
if ($action == "modifier") {
	echo '
		<br />

		<form enctype="multipart/form-data" action="../init_xml2/step3.php" method="post">
			<input type="hidden" name="is_posted" value="yes" />
';
	echo add_token_field();
	echo '
			<input type="submit" name="rien" value="Continue initialization" />
		</form>';
}
?>

<br />

<?php require("../lib/footer.inc.php"); ?>