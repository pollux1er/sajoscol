<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2008-2011
 * Fichier d'inclusion à la gestion des statuts 'autre'
 *
 */
//$niveau_arbo = 1;
	// Initialisations files
	//require_once("../lib/initialisations.inc.php");

if (!$_SESSION["statut"] OR ($_SESSION["statut"] != 'autre' AND $_SESSION["statut"] != 'administrateur')) {

		//tentative_intrusion(1, "Tentative d'accès à un fichier sans avoir les droits nécessaires");
		trigger_error('You carried out a prohibited reading of file', E_USER_ERROR);

}

// droits généraux et communs à tous les utilisateurs
$autorise[0] = array('/accueil.php',
				'/utilisateurs/mon_compte.php',
				'/gestion/contacter_admin.php',
				'/gestion/info_gepi.php');
// droits spécifiques sur les pages relatives aux droits possibles
$autorise[1] = array('/cahier_notes/visu_releve_notes.php');
$autorise[2] = array('/prepa_conseil/index3.php', '/prepa_conseil/edit_limite.php');
$autorise[3] = array('/mod_absences/gestion/voir_absences_viescolaire.php',
						'/mod_absences/gestion/bilan_absences_quotidien.php',
						'/mod_absences/gestion/bilan_absences_classe.php',
						'/mod_absences/gestion/bilan_absences_quotidien_pdf.php',
						'/mod_absences/lib/tableau.php',
						'/mod_absences/lib/export_csv.php');
$autorise[4] = array('/mod_absences/gestion/select.php',
						'/mod_absences/gestion/ajout_abs.php',
						'/mod_absences/lib/liste_absences.php');
$autorise[5] = array('/cahier_texte/see_all.php');
$autorise[6] = array('/cahier_texte_admin/visa_ct.php');
$autorise[7] = array('/edt_organisation/index_edt.php');
$autorise[8] = array('/tous_les_edt');
$autorise[9] = array('/messagerie/index.php');
$autorise[10]= array('/eleves/visu_eleve.php',
						'/eleves/liste_eleves.php');
$autorise[11]= array('/voir_resp');
$autorise[12]= array('/voir_ens');
$autorise[13]= array('/voir_notes');
$autorise[14]= array('/voir_bulle');
$autorise[15]= array('/voir_abs');
//$autorise[16]= array('/voir_anna');
//$autorise[16]= array('/mod_annees_anterieures/popup_annee_anterieure.php');
$autorise[16]= array('/mod_annees_anterieures/consultation_annee_anterieure.php', '/mod_annees_anterieures/popup_annee_anterieure.php');
$autorise[17]= array('/mod_trombinoscopes/trombinoscopes.php', '/mod_trombinoscopes/trombi_impr.php', '/mod_trombinoscopes/trombino_pdf.php');
$autorise[18]= array('/mod_discipline/index.php',
					 '/mod_discipline/saisie_incident.php',
					 '/mod_discipline/check_nature_incident.php',
					 '/mod_discipline/incidents_sans_protagonistes.php',
					 '/mod_discipline/sauve_role.php',
					 '/mod_discipline/update_colonne_retenue.php',
					 '/mod_discipline/traiter_incident.php',
					 '/mod_ooo/retenue.php',
					 '/mod_ooo/rapport_incident.php');
$autorise[19]= array('/mod_abs2/index.php');
$autorise[20]= array('/mod_abs2/saisir_eleve.php',
					 '/mod_abs2/visu_saisie.php',
					 '/mod_abs2/enregistrement_modif_saisie.php',
					 '/mod_abs2/liste_saisies.php',
					 '/mod_abs2/enregistrement_saisie_eleve.php');
$autorise[21]= array('/mod_abs2/bilan_individuel.php' );
$autorise[22]= array('/mod_abs2/totaux_du_jour.php' );


$iter = count($autorise);
$nbre_menu = $iter - 1;
// Intitulés pour le menu et le champ name du formulaire
$menu_accueil[0] = array($nbre_menu);
$menu_accueil[1] = array('report booklets', 'View the report booklets of all the students', 'ne');
$menu_accueil[2] = array('Simplified bulletins', 'View all the simplified bulletins of all the students', 'bs');
$menu_accueil[3] = array('See the absences', 'View all the absences of the school', 'va');
$menu_accueil[4] = array('Type the absences', 'Type the absences of all the students of the school', 'sa');
$menu_accueil[5] = array('Textbooks', 'View all the textbooks of the school', 'cdt');
$menu_accueil[6] = array('Sign the textbooks', 'Sign textbooks', 'cdt_visa');
$menu_accueil[7] = array('Timetable', 'View the timetables of all the students', 'ee');
$menu_accueil[8] = array('Timetable', 'View all the timetables of the school', 'te');
$menu_accueil[9] = array('Display Panel', 'Manage the messages to display on the page of the users.', 'pa');
$menu_accueil[10] = array('Students Cards ', 'General access on the cards of the students', 've');
$menu_accueil[11] = array('Cards : ', 'Cards : See the responsibles', 'vre');
$menu_accueil[12] = array('Cards : ', 'Cards : See the courses', 'vee');
$menu_accueil[13] = array('Cards : ', 'Cards : See the report booklets', 'vne');
$menu_accueil[14] = array('Cards : ', 'Cards : See the simplified bulletins', 'vbe');
$menu_accueil[15] = array('Cards : ', 'Cards : See the absences', 'vae');
$menu_accueil[16] = array('Cards : ', 'Cards : See former year', 'anna');
$menu_accueil[17] = array('Trombinoscope', 'Trombinoscope of students and personnel', 'tr');
$menu_accueil[18] = array('Discipline', 'Access to the Disciplines module  : Declare and manage its incidents', 'dsi');
$menu_accueil[19] = array('Absence2', 'Absence2 : access to the module.', 'abs');
$menu_accueil[20] = array('Absence2', 'Absence2 : type the absences.', 'abs_saisie');
$menu_accueil[21] = array('Absence2', 'Absence2 : access to the page individual summa .', 'ry');
$menu_accueil[22] = array('Absence2', 'Absence2 : access to the page totals of the day.', 'abs_totaux');


?>