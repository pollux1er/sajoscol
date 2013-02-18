<?php
$host = 'localhost';
$user = 'root';
$pass = 'sasse';
$dbname = 'gepi03';

mysql_connect($host, $user, $pass) or die('Cant connect to mysql server');
echo 'Connection a la BD reussi!<br />';
mysql_select_db($dbname) or die('Cant select the database');
echo 'Selection de la BD reussi!<br />';

$eleves = array(
// mod_abs2
"a_agregation_decompte",
"a_notifications",
"a_saisies",
"a_saisies_version",
"a_traitements",
// Absences
"absences",
"absences_gep",
"absences_rb",
"absences_repas",
"absences_eleves",
"vs_alerts_eleves",
"vs_alerts_groupes",
"vs_alerts_types",
// AID
"aid",
"aid_appreciations",
"avis_conseil_classe",
"j_aid_eleves",
"j_aid_utilisateurs",
"j_aid_eleves_resp",
"j_aid_utilisateurs_gest",
"j_aidcateg_super_gestionnaires",
"j_aidcateg_utilisateurs",

// Elèves et responsables
"eleves",
"responsables",
/*
// NE FAUDRAIT-IL PAS VIDER ICI responsables2, resp_pers et reps_adr?
// NON: Cela empêche de conserver les comptes utilisateurs pour les responsables
"responsables2",
"resp_pers",
"resp_adr",
*/
"j_eleves_classes",
//==========================
// On ne vide plus la table chaque année
// Problème avec Sconet qui récupère seulement l'établissement de l'année précédente qui peut être l'établissement courant
//"j_eleves_etablissements",
//==========================
"j_eleves_professeurs",
"j_eleves_regime",
// Notes et appréciations
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
"synthese_app_classe",
//==========================
// Tables notanet
'notanet',
'notanet_avis',
'notanet_app',
'notanet_lvr_ele',
'notanet_socle_commun',
'notanet_verrou',
'notanet_socles',
'notanet_ele_type',
//==========================
"observatoire",
"observatoire_comment",
"observatoire_suivi",

"tempo2",
"tempo",
// Découpe de trombinoscopes
"trombino_decoupe",
"trombino_decoupe_param",
// Cahier de notes
"cc_dev",
"cc_eval",
"cc_notes_eval",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
/*
"ct_entry",
// Cahier de textes
"ct_documents",
"ct_devoirs_entry",
"ct_private_entry"
*/
// mod_examen_blanc
"ex_classes",
"ex_groupes",
"ex_notes",
// mod_epreuve_blanche
"eb_copies",
"eb_epreuves",
"eb_groupes",
"eb_profs",
// Génèse des classes
"gc_ele_arriv_red",
"gc_eleves_options",
// mod_discipline
"s_communication",
"s_exclusions",
"s_incidents",
"s_protagonistes",
"s_reports",
"s_retenues",
"s_sanctions",
"s_traitement_incident",
"s_travail",
"s_travail_mesure",
// Table optionnelle pour les fils RSS de CDT
"rss_users"
);
/*
echo "Tables a vider : <br />";
foreach($eleves as $e) {
	$sql = "SELECT count(*) FROM $e;";
	$res = mysql_query($sql);
	if($res)
		$nb = mysql_result($res,0);
			
	echo("$e . Nb records : $nb");
	if($nb != 0){
		$sql = "DELETE FROM $e;";
		$del = @mysql_query($sql);
		echo " Table videe";
	}
	echo "<br />";
		
}
*/
//Suppression des comptes d'élèves:
// $sql = "DELETE FROM utilisateurs WHERE statut='eleves';";
// $del = mysql_query($sql);
require_once("lib/initialisations.inc.php");
$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
 $sql="SELECT * FROM temp_gep_import2;";
$res_temp=mysql_query($sql);

while ($lig=mysql_fetch_object($res_temp)) {
	$reg_nom = $lig->ELENOM;
	$reg_prenom = $lig->ELEPRE;
	$reg_naissance = $lig->ELEDATNAIS;
	$reg_id_int = $lig->ELENOET;
	$reg_id_nat = $lig->ELENONAT;
	$reg_etab_prec = $lig->ETOCOD_EP;
	$reg_double = $lig->ELEDOUBL;
	$reg_regime = $lig->ELEREG;
	$reg_sexe = $lig->ELESEXE;

	$reg_nom = preg_replace("/Æ/","AE",preg_replace("/æ/","ae",preg_replace("/¼/","OE",preg_replace("/½/","oe",preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtoupper($reg_nom)))))));

	if (strlen($reg_nom) > 50) $reg_nom = substr($reg_nom, 0, 50);
	$reg_prenom = preg_replace("/Æ/","AE",preg_replace("/æ/","ae",preg_replace("/¼/","OE",preg_replace("/½/","oe",preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim($reg_prenom))))));

	if (strlen($reg_prenom) > 50) $reg_prenom = substr($reg_prenom, 0, 50);
	$naissance = explode("/", $reg_naissance);
	if (!preg_match("/[0-9]/", $naissance[0]) OR strlen($naissance[0]) > 2 OR strlen($naissance[0]) == 0) $naissance[0] = "00";
	if (strlen($naissance[0]) == 1) $naissance[0] = "0" . $naissance[0];

	if (!preg_match("/[0-9]/", $naissance[1]) OR strlen($naissance[1] OR strlen($naissance[1]) == 0) > 2) $naissance[1] = "00";
	if (strlen($naissance[1]) == 1) $naissance[1] = "0" . $naissance[1];

	if (!preg_match("/[0-9]/", $naissance[2]) OR strlen($naissance[2]) > 4 OR strlen($naissance[2]) == 3 OR strlen($naissance[2]) == 1) $naissance[2] = "00";
	if (strlen($naissance[2]) == 1) $naissance[2] = "0" . $naissance[2];

	$reg_naissance = $naissance[2] . "-" . $naissance[1] . "-" . $naissance[0];
	$reg_id_int = preg_replace("/[^0-9]/","",trim($reg_id_int));

	$reg_id_nat = preg_replace("/[^A-Z0-9]/","",trim($reg_id_nat));

	$reg_etab_prec = preg_replace("/[^A-Z0-9]/","",trim($reg_etab_prec));

	$reg_double = trim(strtoupper($reg_double));
	if ($reg_double != "OUI" AND $reg_double != "NON") $reg_double = "NON";
	
	$reg_regime = trim(strtoupper($reg_regime));
	if ($reg_regime != "INTERN" AND $reg_regime != "EXTERN" AND $reg_regime != "IN.EX." AND $reg_regime != "DP DAN") $reg_regime = "DP DAN";

	if ($reg_sexe != "F" AND $reg_sexe != "M") $reg_sexe = "F";

	// Maintenant que tout est propre, on fait un test sur la table eleves pour s'assurer que l'élève n'existe pas déjà.
	// Ca permettra d'éviter d'enregistrer des élèves en double

	//$test = mysql_result(mysql_query("SELECT count(login) FROM eleves WHERE elenoet = '" . $reg_id_int . "'"), 0);
$test = 0;
	if ($test == 0) {
		$reg_login = preg_replace("/\040/","_", $reg_nom);
		$reg_login = strtr($reg_login,"àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ","aaaeeeeiioouuucAAAEEEEIIOOUUUC");
		$reg_login = preg_replace("/[^a-zA-Z]/", "", $reg_login);
		if (strlen($reg_login) > 9) $reg_login = substr($reg_login, 0, 9);
		$reg_login .= "_" . strtr(substr($reg_prenom, 0, 1),"àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ","aaaeeeeiioouuucAAAEEEEIIOOUUUC");
		$reg_login = strtoupper($reg_login);

		$p = 1;
		while (true) {
			$test_login = mysql_result(mysql_query("SELECT count(login) FROM eleves WHERE login = '" . $reg_login . "'"), 0);
			if ($test_login != 0) {
				$reg_login .= strtr(substr($reg_prenom, $p, 1), "àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ", "aaaeeeeiioouuucAAAEEEEIIOOUUUC");
				$p++;
			} else {
				break 1;
			}
			$reg_login = strtoupper($reg_login);
		}

		$insert = mysql_query("INSERT INTO eleves SET " .
					"no_gep = '" . $reg_id_nat . "', " .
					"login = '" . $reg_login . "', " .
					"nom = '" . $reg_nom . "', " .
					"prenom = '" . $reg_prenom . "', " .
					"sexe = '" . $reg_sexe . "', " .
					"naissance = '" . $reg_naissance . "', " .
					"elenoet = '" . $reg_id_int . "', " .
					"ereno = '" . $reg_id_int . "'");

			if (!$insert) {
				$error++;
				echo mysql_error();
			} else {
				$total++;

				// On enregistre l'établissement d'origine, le régime, et si l'élève est redoublant
				//============================================
				if (($reg_etab_prec != '')&&($reg_id_int != '')) {
		
					if($gepiSchoolRne!="") {
						if($gepiSchoolRne!=$reg_etab_prec) {
							$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_id_int';";
							$test_etab=mysql_query($sql);
							if(mysql_num_rows($test_etab)==0){
								$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_id_int', id_etablissement='$reg_etab_prec';";
								$insert_etab=mysql_query($sql);
								if (!$insert_etab) {
									//echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab_prec.</p>\n";
									$error++;
									echo mysql_error();
								}
							}
							else {
								$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab_prec' WHERE id_eleve='$reg_id_int';";
								$update_etab=mysql_query($sql);
								if (!$update_etab) {
									//echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab_prec.</p>\n";
									$error++;
									echo mysql_error();
								}
							}
						}
					}
					else {
						$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_id_int';";
						$test_etab=mysql_query($sql);
						if(mysql_num_rows($test_etab)==0){
							$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_id_int', id_etablissement='$reg_etab_prec';";
							$insert_etab=mysql_query($sql);
							if (!$insert_etab) {
								//echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab_prec.</p>\n";
								$error++;
								echo mysql_error();
							}
						}
					}

				}
				//============================================

				if ($reg_double == "OUI") {
					$reg_double = "R";
				} else {
					$reg_double = "-";
				}

				if ($reg_regime == "INTERN") {
					$reg_regime = "int.";
				} else if ($reg_regime == "EXTERN") {
					$reg_regime = "ext.";
				} else if ($reg_regime == "DP DAN") {
					$reg_regime = "d/p";
				} else if ($reg_regime == "IN.EX.") {
					$reg_regime = "i-e";
				}

				$insert3 = mysql_query("INSERT INTO j_eleves_regime SET login = '" . $reg_login . "', doublant = '" . $reg_double . "', regime = '" . $reg_regime . "'");
				if (!$insert3) {
					$error++;
					echo mysql_error();
				}
			}

		}
		$i++;
		//if (!isset($_POST['ligne'.$i.'_nom'])) break 1;
	}
?> 
