<?php
/**
 * @version $Id$
 *
 * Module d'intégration de Gepi dans un ENT réalisé au moment de l'intégration de Gepi dans ARGOS dans l'académie de Bordeaux
 * Fichier permettant de récupérer de nouveaux élèves dans le ldap de l'ENT
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stéphane boireau, Julien Jocal
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

// Sécurité supplémentaire pour éviter d'aller voir ce fichier si on n'est pas dans un ent
if (getSettingValue("use_ent") != 'y') {
	DIE('Prohibited file.');
}

// ======================= Initialisation des variables ======================= //
$aff_liste_eleves = NULL;
$requete_c	= NULL;
$msg = NULL;
$enregistrer = isset($_POST['enregistrer']) ? $_POST['enregistrer'] : NULL;

$maj		= isset($_POST['maj']) ? $_POST['maj'] : NULL;
$_nom		= isset($_POST['nom']) ? $_POST['nom'] : NULL;
$_prenom	= isset($_POST['prenom']) ? $_POST['prenom'] : NULL;
$_sexe		= isset($_POST['sexe']) ? $_POST['sexe'] : NULL;
$_naissance	= isset($_POST['naissance']) ? $_POST['naissance'] : NULL;
$_regime	= isset($_POST['regime']) ? $_POST['regime'] : NULL;
$_elenoet	= isset($_POST['elenoet']) ? $_POST['elenoet'] : NULL;
$_national	= isset($_POST['national']) ? $_POST['national'] : NULL;

// ======================= code métier ======================================== //

// Si c'est demandé, on traite les nouveaux logins
if ($enregistrer == "Ajouter ces élèves") {
	check_token();

	/*
	Pour info : d/p ext. int. pour demi-pensionnaire, externe et interne.
	dans j_eleves_regime {login, doublant, regime} doublant = R sinon -
	Dans eleves {no_gep, login, nom, prénom, sexe, naissance, elenoet} - sexe {F M} naissance {aaaa-mm-jj} et no_gep est le numéro national
	*/

	$nbre_a_traiter = count($_nom);
	for($i = 0 ; $i < $nbre_a_traiter ; $i++){

		if (isset($maj[$i])) {

			// On peut alors ajouter cet élève à la base de Gepi
			/*echo '
				<br />On ajoute ' . $_nom[$i] . ' ' . $_prenom[$i] . ' Régime ' . $_regime[$i] . '
				(S : ' . $_sexe[$i] . ', N : ' . $_naissance[$i] . ', E : ' . $_elenoet[$i] . ', INE : ' . $_national[$i] . '.';*/

			// Quelques vérifications de base
			if(!is_numeric($_elenoet[$i]) OR mb_strlen($_elenoet[$i]) != 4){$_elenoet[$i] = '';}
			if(!is_numeric($_national[$i])){$_national[$i] = '';}
			$test_date = explode("-", $_naissance[$i]);
			if(!is_numeric($test_date[0]) OR mb_strlen($_naissance[$i]) != 10 OR count($test_date) != 3 OR $test_date[1] >= 13){
				$_naissance[$i] = '1990-01-02';
			}

			$sql_eleves = "INSERT INTO eleves SET no_gep = '" . $_national[$i] . "',
													login = '" . $maj[$i] . "',
													nom = '" . $_nom[$i] . "',
													prenom = '" . $_prenom[$i] . "',
													sexe = '" . $_sexe[$i] . "',
													naissance = '" . $_naissance[$i] . "',
													elenoet = '" . $_elenoet[$i] . "'";

			$query_eleves = mysql_query($sql_eleves) OR DIE('<br />Impossible to record this student <br />' . $sql_eleves . '<br /> --> ' . mysql_error());


			if ($query_eleves) {

				// Comme la première insertion a fonctionné, on ajoute la seconde
				if($_regime[$i] == 'inc' OR $_regime[$i] == 'dp'){
					$_regime[$i] = 'd/p';
				}elseif($_regime[$i] == 'ext'){
					$_regime[$i] = 'ext.';
				}elseif($_regime[$i] == 'int'){
					$_regime[$i] = 'int.';
				}
				$sql_reg = "INSERT INTO j_eleves_regime SET doublant = '-', regime = '" . $_regime[$i] . "', login = '".$maj[$i]."'";
				$query_reg = mysql_query($sql_reg);

				if ($query_reg) {
					$msg .= '<p style="color: green;">student ' . $_nom[$i] . ' ' . $_prenom[$i] . ' was indeed recorded in the base of the student of GEPI.</p>';
				}else{
					$msg .= '<p style="color: red;">student ' . $_nom[$i] . ' ' . $_prenom[$i] . ' was not recorded in the base of the pupils of GEPI .</p>';
				}
			}else{
				$msg .= '<p style="color: red;">student ' . $_nom[$i] . ' ' . $_prenom[$i] . ' was not recorded in the base of the student of GEPI.</p>';
			}

		}

	}

}


// On récupère la liste des élèves déjà inscrits dans la base
$sql_all = "SELECT DISTINCT login FROM eleves";
$query_all = mysql_query($sql_all);
$tab_all = array();
while($rep_all = mysql_fetch_array($query_all)){
	$tab_all[] = $rep_all['login'];
} // while

// Puis la liste des élèves présents dans la table ldap_bx
$sql_ent = "SELECT DISTINCT login_u FROM ldap_bx WHERE statut_u = 'student'";
$query_ent = mysql_query($sql_ent);
$tab_ent = array();
while($rep_ent = mysql_fetch_array($query_ent)){
	$tab_ent[] = $rep_ent['login_u'];
} // while

// Et enfin, on compare les deux tableaux et on garde les nouveaux logins
$tab_new_eleves = array_diff($tab_ent, $tab_all);

$test_new = count($tab_new_eleves);
if ($test_new >= 1) {
	// Alors il y a au moins un nouvel élève dans le ldap
	foreach($tab_new_eleves as $rep){

		$aff_liste_eleves .= $rep . '<br />';
		$requete_c .= 'login_u = "' . $rep . '" OR ';

	}
	// On récupère les nom_u, prenom_u et identite_u
	$complement_req = substr($requete_c, 0, -4);
	$sql_c = "SELECT * FROM ldap_bx WHERE (" . $complement_req . ")";
	$query_c = mysql_query($sql_c) OR DIE('<br />Error in request SQL <br /> --> ' . $sql_c . '<br />' . mysql_error());

	unset($tab_new_eleves); // pour repartir de zéro
	$a = 0;

	while($rep_c = mysql_fetch_array($query_c)){
		$tab_new_eleves[$a]['_login'] = $rep_c['login_u'];
		$tab_new_eleves[$a]['_nom'] = $rep_c['nom_u'];
		$tab_new_eleves[$a]['_prenom'] = $rep_c['prenom_u'];
		$tab_new_eleves[$a]['_statut'] = $rep_c['statut_u'];
		$tab_new_eleves[$a]['_no_gep'] = $rep_c['identite_u'];

		$a++;

	} // while

}


// =========== fichiers spéciaux ==========
$style_specifique = "edt_organisation/style_edt";
//**************** EN-TETE *****************
$titre_page = "Users of the ENT";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var(); // à enlever en production
$increment = 0; // pour les name du formulaire ci-dessous
?>
<p><a href="../accueil.php">RETURN towards the reception</a>&nbsp;-&nbsp;Add a new student registered in the ENT</p>
<p><br /></p>
<p style="color: red; font-weight: bold;">Before continuing, you must currently recover all the users in the ENT <a href="index.php">HERE</a>.</p>
<p>You must register the internal number of the student (elenoet) present
dansSconet to make the automatic updates.</p>

	<form method="post" action="miseajour_ent_eleves.php">
<?php
	echo add_token_field();
?>
<table>

	<tr>
		<th>Update</th>
		<th>Login</th>
		<th>Name</th>
		<th>First name</th>
		<th>Sex</th>
		<th>Date of birth<br /><span style="color: blue; font-size: 0.5em;">in the form aaaa-mm-jj</span></th>
		<th>Mode</th>
		<th>Elenoet<br /><span style="color: blue; font-size: 0.5em;">(internal number)</span></th>
		<th>I.N.E.<br /><span style="color: blue; font-size: 0.5em;">(national number)</span></th>
	</tr>
		<?php foreach($tab_new_eleves as $rep): ?>
	<tr>
		<td><p style="text-align: center;"><input type="checkbox" name="maj[<?php echo $increment; ?>]" value="<?php echo $rep['_login']; ?>" checked="checked" /></p></td>
		<td><?php echo $rep['_login']; ?></td>
		<td><input type="text" name="nom[<?php echo $increment; ?>]" value="<?php echo $rep['_nom']; ?>" /></td>
		<td><input type="text" name="prenom[<?php echo $increment; ?>]" value="<?php echo $rep['_prenom']; ?>" style="width: 80px;" /></td>
		<td>
			<select name="sexe[<?php echo $increment; ?>]">
				<option value="F" selected="selected">Female</option>
				<option value="M">Masculine</option>
			</select>
		</td>
		<td style="text-align: center;"><input type="text" name="naissance[<?php echo $increment; ?>]" value="" style="width: 100px;" /></td>
		<td>
			<select name="regime[<?php echo $increment; ?>]">
				<option value="inc">Unknown</option>
				<option value="dp" selected="selected">Demi-pens.</option>
				<option value="ext">External</option>
				<option value="int">Interne</option>
			</select>
		</td>
		<td><input type="text" name="elenoet[<?php echo $increment; ?>]" value="" style="width: 80px;" /></td>
		<td><input type="text" name="national[<?php echo $increment; ?>]" value="<?php echo $rep['_no_gep']; ?>" /></td>
	</tr>
		<?php $increment++; endforeach; ?>

</table>
		<p><input type="submit" name="enregistrer" value="Ajouter ces &eacute;l&egrave;ves" /></p>
	</form>
<p><br /></p>
<p>Once finished, you must <a href="../classes/index.php">Put these student in their class and to check their lesson</a>.</p>
<?php require_once("../lib/footer.inc.php");