<?php

/*
 * $Id: class_accueil_ordre_menu.php 5752 2010-10-25 12:08:35Z jjacquard $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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



/**
 * Description of class_accueil_ordre_menu
 *
 * @author regis
 */
class class_accueil_ordre_menu extends class_page_accueil {



/**
 *
 *
 * Charge les menus Accueil en fonction du statut passé en argument
 *
 * @author regis
 */

  function __construct($statut, $gepiSettings, $niveau_arbo,$ordre_menus) {

	switch ($niveau_arbo){
	  case 0:
		$this->cheminRelatif = './';
		break;
	  case 1:
		$this->cheminRelatif = '../';
		break;
	  case 2:
		$this->cheminRelatif = '../../';
		break;
	  case 3:
		$this->cheminRelatif = '../../../';
		break;
	  default:
		$this->cheminRelatif = './';
	}

	$this->statutUtilisateur = $statut;
	$this->gepiSettings=$gepiSettings;
	$this->loginUtilisateur=$_SESSION['login'];

	$this->chargeOrdreMenu($ordre_menus);

	// On teste si on l'utilisateur est un prof avec des matières. Si oui, on affiche les lignes relatives au cahier de textes et au carnet de notes

	$this->test_prof_matiere = 1;

// On teste si le l'utilisateur est prof de suivi. Si oui on affiche la ligne relative remplissage de l'avis du conseil de classe
	$this->test_prof_suivi = 1;

	$this->test_https = 'y'; // pour ne pas avoir à refaire le test si on a besoin de l'URL complète (rss)
	if (!isset($_SERVER['HTTPS'])
		OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
		OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https"))
	{
		$this->test_https = 'n';
	}

/***** Outils d'administration *****/
	$this->verif_exist_ordre_menu('bloc_administration');
	if ($this->administration())
	$this->chargeAutreNom('bloc_administration');

/***** Outils de gestion des absences vie scolaire *****/
	$this->verif_exist_ordre_menu('bloc_absences_vie_scol');
	if ($this->absences_vie_scol())
	$this->chargeAutreNom('bloc_absences_vie_scol');

/***** Outils de gestion des absences par les professeurs *****/
	$this->verif_exist_ordre_menu('bloc_absences_professeur');
	if ($this->absences_profs())
	$this->chargeAutreNom('bloc_absences_professeur');

/***** Saisie ***********/
	$this->verif_exist_ordre_menu('bloc_saisie');
	if ($this->saisie())
	$this->chargeAutreNom('bloc_saisie');

/***** Cahier de texte CPE ***********/
	$this->verif_exist_ordre_menu('bloc_Cdt_CPE');
	if ($this->cahierTexteCPE()){
	  $this->chargeAutreNom('bloc_Cdt_CPE');
	}

/***** Cahier de texte CPE Restreint ***********/
	$this->verif_exist_ordre_menu('bloc_Cdt_CPE_Restreint');
	if ($this->cahierTexteCPE_Restreint()){
	  $this->chargeAutreNom('bloc_Cdt_CPE_Restreint');
	}

/***** Visa Cahier de texte Scolarite ***********/
	$this->verif_exist_ordre_menu('bloc_Cdt_Visa');
	if ($this->cahierTexte_Visa()){
	  $this->chargeAutreNom('bloc_Cdt_Visa');
	}

/***** gestion des trombinoscopes : module de Christian Chapel ***********/
	$this->verif_exist_ordre_menu('bloc_trombinoscope');
	if ($this->trombinoscope())
	$this->chargeAutreNom('bloc_trombinoscope');

/***** Outils de relevé de notes *****/
	$this->verif_exist_ordre_menu('bloc_releve_notes');
	if ($this->releve_notes())
	$this->chargeAutreNom('bloc_releve_notes');

/***** Outils de relevé ECTS *****/
	$this->verif_exist_ordre_menu('bloc_releve_ects');
	if ($this->releve_ECTS())
	$this->chargeAutreNom('bloc_releve_ects');

/***** Emploi du temps *****/
	$this->verif_exist_ordre_menu('bloc_emploi_du_temps');
	if ($this->emploiDuTemps())
	$this->chargeAutreNom('bloc_emploi_du_temps');

/***** Outils destinés essentiellement aux parents et aux élèves *****/

// Cahier de textes
	$this->verif_exist_ordre_menu('bloc_cahier_texte_famille');
	if ($this->cahierTexteFamille())
	$this->chargeAutreNom('bloc_cahier_texte_famille');
// Relevés de notes
	$this->verif_exist_ordre_menu('bloc_carnet_notes_famille');
	if ($this->releveNotesFamille())
	$this->chargeAutreNom('bloc_carnet_notes_famille');
// Equipes pédagogiques
	$this->verif_exist_ordre_menu('bloc_equipe_peda_famille');
	if ($this->equipePedaFamille())
	$this->chargeAutreNom('bloc_equipe_peda_famille');
// Report cards simplifiés
	$this->verif_exist_ordre_menu('bloc_bull_simple_famille');
	if ($this->bulletinFamille())
	$this->chargeAutreNom('bloc_bull_simple_famille');
// Graphiques
	$this->verif_exist_ordre_menu('bloc_graphique_famille');
	if ($this->graphiqueFamille())
	$this->chargeAutreNom('bloc_graphique_famille');
// les absences
	$this->verif_exist_ordre_menu('bloc_absences_famille');
	if ($this->absencesFamille())
	$this->chargeAutreNom('bloc_absences_famille');

/***** Outils complémentaires de gestion des AID *****/
	$this->verif_exist_ordre_menu('bloc_outil_comp_gestion_aid');
	if ($this->gestionAID())
	$this->chargeAutreNom('bloc_outil_comp_gestion_aid');

/***** Outils de gestion des Report cards scolaires *****/
	$this->verif_exist_ordre_menu('bloc_gestion_bulletins_scolaires');
	if ($this->bulletins())
	$this->chargeAutreNom('bloc_gestion_bulletins_scolaires');

/***** Visualisation / Impression *****/
	$this->verif_exist_ordre_menu('bloc_visulation_impression');
	if ($this->impression())
	$this->chargeAutreNom('bloc_visulation_impression');

/***** Gestion Notanet *****/
	$this->verif_exist_ordre_menu('bloc_notanet_fiches_brevet');
	if ($this->notanet())
	$this->chargeAutreNom('bloc_notanet_fiches_brevet');

/***** Gestion années antérieures *****/
	$this->verif_exist_ordre_menu('bloc_annees_antérieures');
	if ($this->anneeAnterieure())
	$this->chargeAutreNom('bloc_annees_antérieures');

/***** Gestion des messages *****/
	$this->verif_exist_ordre_menu('bloc_panneau_affichage');
	if ($this->messages())
	$this->chargeAutreNom('bloc_panneau_affichage');

/***** Module inscription *****/
	$this->verif_exist_ordre_menu('bloc_module_inscriptions');
	if ($this->inscription())
	$this->chargeAutreNom('bloc_module_inscriptions');

/***** Module discipline *****/
	$this->verif_exist_ordre_menu('bloc_module_discipline');
	if ($this->discipline())
	$this->chargeAutreNom('bloc_module_discipline');

/***** Module Modèle Open Office *****/
	$this->verif_exist_ordre_menu('bloc_modeles_Open_Office');
	if ($this->modeleOpenOffice())
	$this->chargeAutreNom('bloc_modeles_Open_Office');

/***** Module plugins : affichage des menus des plugins en fonction des droits *****/
	$this->verif_exist_ordre_menu('');
	$this->plugins();

/***** Module Genese des classes *****/
	$this->verif_exist_ordre_menu('bloc_Genese_classes');
	if ($this->geneseClasses())
	$this->chargeAutreNom('bloc_Genese_classes');

/***** Lien vers les flux rss pour les élèves s'ils sont activés *****/
	$this->verif_exist_ordre_menu('bloc_RSS');
	if ($this->fluxRSS())
	$this->chargeAutreNom('bloc_RSS');

/***** Statut AUTRE *****/
	$this->verif_exist_ordre_menu('bloc_navigation');
	if ($this->statutAutre())
	$this->chargeAutreNom('bloc_navigation');

/***** Module Epreuves blanches *****/
	$this->verif_exist_ordre_menu('bloc_epreuve_blanche');
	if ($this->epreuvesBlanches())
	$this->chargeAutreNom('bloc_epreuve_blanche');

/***** Module Examen blanc *****/
	$this->verif_exist_ordre_menu('bloc_examen_blanc');
	if ($this->examenBlanc())
	$this->chargeAutreNom('bloc_examen_blanc');

/***** Module Admissions Post-Bac *****/
	$this->verif_exist_ordre_menu('bloc_admissions_post_bac');
	if ($this->adminPostBac())
	$this->chargeAutreNom('bloc_admissions_post_bac');

/***** Module Gestionnaire d'AID *****/
	$this->verif_exist_ordre_menu('bloc_Gestionnaire_aid');
	if ($this->gestionEleveAID())
	$this->chargeAutreNom('bloc_Gestionnaire_aid');

/***** Tri des menus *****/
  sort($this->titre_Menu);

  }











  private function saisie(){

	$this->b=0;

	$afficher_correction_validation="n";
	$sql="SELECT 1=1 FROM matieres_app_corrections;";
	$test_mac=mysql_query($sql);
	if(mysql_num_rows($test_mac)>0) {$afficher_correction_validation="y";}

        if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
  $this->creeNouveauItem("/absences/index.php",
			  "Report card : entry of absences",
			  "This tool allows you to entry absences on school reports." );
        }

	if ((($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur'))
			and (getSettingValue("active_cahiers_texte")=='y'))
	  $this->creeNouveauItem("/cahier_texte/index.php",
			  "Log books",
			  "This tool allows you to set up a text book for each of your classes." );

	if ((($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur'))
			and (getSettingValue("active_carnets_notes")=='y'))
	  $this->creeNouveauItem("/cahier_notes/index.php",
			  "Notebook : entry of notes",
			  "This tool allows you to set up a botebook for each period and entry notes of all assessments.");

	if (($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur'))
	  $this->creeNouveauItem("/saisie/index.php",
			  "Report card : entry of average and appreciation per subject",
			  "This tool allows you to entry directly, without using school reports, averages and appreciation of the");

	if($afficher_correction_validation=="y")
	  $this->creeNouveauItem("/saisie/validation_corrections.php",
			  "Correction of report cards",
			  "This tool allows you to validate corrections of appreciations proposed by teachers after the closure of a period.<br /><span style='color:red;'>One or more proposition require your attention attention.</span>\n");

	if ((($this->test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes'))
			or (($this->statutUtilisateur!='professeur') and (getSettingValue("GepiRubConseilScol")=='yes') )
			or ($this->statutUtilisateur=='secours')  )
	  $this->creeNouveauItem("/saisie/saisie_avis.php",
			  "Report card : entry of opinions of the board",
			  "This tool allows you to entry opinion of the staff meeting.");

	// Saisie ECTS - ne doit être affichée que si l'utilisateur a bien des classes ouvrant droit à ECTS
	if ($this->statutUtilisateur == 'professeur') {
		$this->test_prof_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_groupes_professeurs jgp
				WHERE (jgc.saisie_ects = TRUE
				  AND jgc.id_groupe = jgp.id_groupe
				  )"));
		$this->test_prof_suivi_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_eleves_professeurs jep, j_eleves_groupes jeg
				WHERE (jgc.saisie_ects = TRUE
				AND jgc.id_groupe = jeg.id_groupe
				AND jeg.login = jep.login )"));
	} else {
		$this->test_scol_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_scol_classes jsc
				WHERE (jgc.saisie_ects = TRUE
				AND jgc.id_classe = jsc.id_classe
				)"));
	}
	$conditions_ects = ($this->gepiSettings['active_mod_ects'] == 'y' AND
		  (($this->test_prof_suivi != "0" and $this->gepiSettings['GepiAccesSaisieEctsPP'] =='yes'
			  AND $this->test_prof_suivi_ects != "0")
		  OR ($this->statutUtilisateur == 'professeur'
			  AND $this->gepiSettings['GepiAccesSaisieEctsProf'] =='yes'
			  AND $this->test_prof_ects != "0")
		  OR ($this->statutUtilisateur=='scolarite'
			  AND $this->gepiSettings['GepiAccesSaisieEctsScolarite'] =='yes'
			  AND $this->test_scol_ects != "0")
		  OR ($this->statutUtilisateur=='secours')));
	if ($conditions_ects)
	  $this->creeNouveauItem("/mod_ects/index_saisie.php","ECTS credits","Entry of ECTS credits");

	// Pour un professeur, on n'appelle que les aid qui sont sur un bulletin
	$call_data = mysql_query("SELECT * FROM aid_config
							  WHERE display_bulletin = 'y'
							  OR bull_simplifie = 'y'
							  ORDER BY nom");
	$nb_aid = mysql_num_rows($call_data);
	$i=0;
	while ($i < $nb_aid) {
	  $indice_aid = @mysql_result($call_data, $i, "indice_aid");
	  $call_prof = mysql_query("SELECT * FROM j_aid_utilisateurs
								WHERE indice_aid = '".$indice_aid."'");
	  $nb_result = mysql_num_rows($call_prof);
	  if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
		$nom_aid = @mysql_result($call_data, $i, "nom");
		$this->creeNouveauItem("/saisie/saisie_aid.php?indice_aid=".$indice_aid,
				$nom_aid,
				"This tool allows to enter appreciations off ".$this->gepiSettings['denomination_eleves']." for the $nom_aid.");
	  }
	  $i++;
	}

	//==============================
// Pour permettre la saisie de commentaires-type, renseigner la variable $commentaires_types dans /lib/global.inc
// Et récupérer le paquet commentaires_types sur... ADRESSE A DEFINIR:
	if(file_exists('saisie/commentaires_types.php')) {
	  if ((($this->statutUtilisateur=='professeur')
			  AND (getSettingValue("CommentairesTypesPP")=='yes')
			  )
			  OR (($this->statutUtilisateur=='scolarite')
					  AND (getSettingValue("CommentairesTypesScol")=='yes')))
	  {
		$this->creeNouveauItem("/saisie/commentaires_types.php",
				"Entry of comments",
				"Allows to define comments for the staff meeting opinion.");
	  }
	}

	  if ($this->b>0){
		$this->creeNouveauTitre('accueil',"Saisie",'images/icons/configure.png');
		return true;
	  }
  }

  private function cahierTexteCPE(){
	$this->b=0;

	$condition = (
	getSettingValue("active_cahiers_texte")=='y' AND (
		($this->statutUtilisateur == "cpe" AND getSettingValue("GepiAccesCdtCpe") == 'yes')
		OR ($this->statutUtilisateur == "scolarite" AND getSettingValue("GepiAccesCdtScol") == 'yes')
	));

	if ($condition) {
	  $this->creeNouveauItem("/cahier_texte_2/see_all.php",
			  "Log books",
			  "Allows to consult reports of sessions and assignments to do for the teachings off all the ".$this->gepiSettings['denomination_eleves']);
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Cahier de texte",'images/icons/document.png');
	  return true;
	}
  }



  private function trombinoscope(){
	//On vérifie si le module est activé

	$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");
	$active_module_trombino_pers=getSettingValue("active_module_trombino_pers");

	$this->b=0;

	$affiche="yes";
	if(($this->statutUtilisateur=='eleve')) {
	  $GepiAccesEleTrombiTousEleves=getSettingValue("GepiAccesEleTrombiTousEleves");
	  $GepiAccesEleTrombiElevesClasse=getSettingValue("GepiAccesEleTrombiElevesClasse");
	  $GepiAccesEleTrombiPersonnels=getSettingValue("GepiAccesEleTrombiPersonnels");
	  $GepiAccesEleTrombiProfsClasse=getSettingValue("GepiAccesEleTrombiProfsClasse");

	  if(($GepiAccesEleTrombiTousEleves!="yes")&&
			($GepiAccesEleTrombiElevesClasse!="yes")&&
			($GepiAccesEleTrombiPersonnels!="yes")&&
			($GepiAccesEleTrombiProfsClasse!="yes")) {
		$affiche = 'no';
	  }else {
		// Au moins un des droits est donné aux élèves.
		$affiche = 'yes';

		if (($active_module_trombinoscopes!='y')
				&&($GepiAccesEleTrombiPersonnels!="yes")
				&&($GepiAccesEleTrombiProfsClasse!="yes")) {
		  $affiche = 'no';
		}

		if (($active_module_trombino_pers!='y')
				&&($GepiAccesEleTrombiTousEleves!="yes")
				&&($GepiAccesEleTrombiElevesClasse!="yes")) {
		  $affiche = 'no';
		}
	  }
	}

	if ($affiche=="yes"
			&& (($active_module_trombinoscopes=='y')
			||($active_module_trombino_pers=='y'))) {

	  $this->creeNouveauItem("/mod_trombinoscopes/trombinoscopes.php",
			  "Trombinoscopes",
			  "Cet outil vous permet de visualiser les trombinoscopes des classes.");

	  // On appelle les aid "trombinoscope"
	  $call_data = mysql_query("SELECT * FROM aid_config
								WHERE indice_aid= '".getSettingValue("num_aid_trombinoscopes")."'
								ORDER BY nom");
	  $nb_aid = mysql_num_rows($call_data);
	  $i=0;
	  while ($i < $nb_aid) {
		$indice_aid = @mysql_result($call_data, $i, "indice_aid");
		$call_prof = mysql_query("SELECT * FROM j_aid_utilisateurs_gest
								  WHERE indice_aid = '$indice_aid'");
		$nb_result = mysql_num_rows($call_prof);
		if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
		  $nom_aid = @mysql_result($call_data, $i, "nom");
		  $this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "Cet outil vous permet de visualiser quels ".$this->gepiSettings['denomination_eleves']." ont le droit d'envoyer/modifier leur photo.");
		}
		$i++;
	  }
	}

	  if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Trombinoscope",'images/icons/trombinoscope.png');
		return true;
	  }
  }





  private function emploiDuTemps(){
	$this->b=0;

    $this->creeNouveauItem("/edt_organisation/index_edt.php",
			"Timetable",
			"This tool allows the consultation/management of the.");

	$this->creeNouveauItem("/edt_organisation/edt_eleve.php",
			  "Timetable",
			  "This tool enable the consultation of your schedule.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Emploi du temps",'images/icons/document.png');
	  return true;
	}
 }

  private function cahierTexteFamille(){
	$this->b=0;

	$condition = (
	getSettingValue("active_cahiers_texte")=='y' AND (
		($this->statutUtilisateur == "responsable" AND getSettingValue("GepiAccesCahierTexteParent") == 'yes')
		OR ($this->statutUtilisateur == "eleve" AND getSettingValue("GepiAccesCahierTexteEleve") == 'yes')
	));

		  $this->creeNouveauItem("/cahier_texte/consultation.php",
				  "Diary",
				  "Allows to consult reports of meeting and  assessments to do for the teachings you follow.");


	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Cahier de texte",'images/icons/document.png');
	  return true;
	}
  }

  private function releveNotesFamille(){
	$this->b=0;

   $condition = (
		getSettingValue("active_carnets_notes")=='y' AND (
			($this->statutUtilisateur == "responsable" AND getSettingValue("GepiAccesReleveParent") == 'yes')
			OR ($this->statutUtilisateur == "eleve" AND getSettingValue("GepiAccesReleveEleve") == 'yes')
			));

	if ($condition) {
	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes_bis.php",
				  "Statements of notes",
				  "Allows to consult your detailed statements of notes.");
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Carnet de notes",'images/icons/releve.png');
	  return true;
	}
  }

  private function equipePedaFamille(){
	$this->b=0;

	$condition = (
			($this->statutUtilisateur == "responsable"
			  AND getSettingValue("GepiAccesEquipePedaParent") == 'yes')
			OR ($this->statutUtilisateur == "eleve"
			  AND getSettingValue("GepiAccesEquipePedaEleve") == 'yes')
			);

	if ($condition) {

		  $this->creeNouveauItem("/groupes/visu_profs_eleve.php",
				  "Teaching staff",
				  "Allows to consult the teaching staff which concerns you.");

	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Teaching staff",'images/icons/trombinoscope.png');
	  return true;
	}
  }

  private function bulletinFamille(){
	$this->b=0;

	$condition = (
			($this->statutUtilisateur == "responsable"
			  AND getSettingValue("GepiAccesReport cardSimpleParent") == 'yes')
			OR ($this->statutUtilisateur == "eleve"
			  AND getSettingValue("GepiAccesReport cardSimpleEleve") == 'yes')
			);

	if ($condition) {

		  $this->creeNouveauItem("/prepa_conseil/index3.php",
				  "Simplified reports",
				  "Allows to consult your report cards in simplified form.");

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Simplified reports",'images/icons/bulletin_simp.png');
	  return true;
	}
  }

  private function graphiqueFamille(){
	$this->b=0;

	$condition = (
			($this->statutUtilisateur == "responsable" AND getSettingValue("GepiAccesGraphParent") == 'yes')
			OR ($this->statutUtilisateur == "eleve" AND getSettingValue("GepiAccesGraphEleve") == 'yes')
			);

	if ($condition) {

		  $this->creeNouveauItem("/visualisation/affiche_eleve.php",
				  "Graphic visualization",
				  "Allows to consult your results in graphic form, compared with the class.");

    }

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Graphic visualization",'images/icons/graphes.png');
	  return true;
	}
  }





  private function AfficheAid($indice_aid){
    if ($this->statutUtilisateur == "eleve") {
        $test = sql_query1("SELECT count(login) FROM j_aid_eleves
				  WHERE indice_aid='".$indice_aid."' ");
        if ($test == 0)
            return false;
        else
            return true;
    } else
        return true;
  }



  private function impression(){
	$this->b=0;

	$conditions_moyennes = (
        ($this->statutUtilisateur != "professeur")
        OR
        (
        ($this->statutUtilisateur == "professeur") AND
            (
            (getSettingValue("GepiAccesMoyennesProf") == "yes") OR
            (getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
            (getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")
            )
        )
        );

	$conditions_bulsimples = (
        	(
	        ($this->statutUtilisateur != "eleve") AND ($this->statutUtilisateur != "responsable")
        	)
        AND
        (
        ($this->statutUtilisateur != "professeur") OR
        (
	    	($this->statutUtilisateur == "professeur") AND
	            (
	            (getSettingValue("GepiAccesReport cardSimpleProf") == "yes") OR
	            (getSettingValue("GepiAccesReport cardSimpleProfTousEleves") == "yes") OR
	            (getSettingValue("GepiAccesReport cardSimpleProfToutesClasses") == "yes")
	            )
        	)
        )
        );

	$this->creeNouveauItem("/groupes/visu_profs_class.php",
			"Visualization of the teaching staffs",
			"This allows you to know all the ".$this->gepiSettings['denomination_professeurs']." of classes where you intervene, as well as the compositions of the groups concerned.");

	$this->creeNouveauItem("/eleves/visu_eleve.php",
			"Consultation of a ".$this->gepiSettings['denomination_eleve'],
			"This menu allows you to consult in the same page information concerning one ".$this->gepiSettings['denomination_eleve']." (followed lesson, reports, statements of notes, ".$this->gepiSettings['denomination_responsables'].",...). Some elements can be accessible only for certain categories of visitors.");

	$this->creeNouveauItem("/impression/impression_serie.php",
			"PDF list printing",
			"This allows you to print pdf lists with ".$this->gepiSettings['denomination_eleves'].", with the unit or in series. The appearance of the lists can be customized.");

	if(($this->statutUtilisateur=='scolarite')||(($this->statutUtilisateur=='professeur')
			AND ($this->test_prof_suivi != "0"))){
	  $this->creeNouveauItem("/saisie/impression_avis.php",
			  "PDF printing of the opinions of the staff meeting",
			  "This allows you to print in pdf the synthesis of the opinions of the staff meeting.");
	}

	if(($this->statutUtilisateur=='scolarite')||
			($this->statutUtilisateur=='professeur')||
			($this->statutUtilisateur=='cpe')){
	  $this->creeNouveauItem("/groupes/mes_listes.php",
			  "Export my lists",
			  "This menu allows to download its lists with all the ".$this->gepiSettings['denomination_eleves']." with CSV format with the fields CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS.");
	}

	$this->creeNouveauItem("/visualisation/index.php",
			"Graphic tools for visualization",
			"Graphic visualization of the results of ".$this->gepiSettings['denomination_eleves']." or of the classes, by merging the data by multiple manners.");

	if (($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur')) {

	  if ($this->statutUtilisateur!='scolarite'){
		$this->creeNouveauItem("/prepa_conseil/index1.php",
				"To visualize my averages and appreciations of school reports",
				"Summary table of your averages and/or appreciations appearing in the school reports with display of useful statistics for the filling of the school reports.");
	  }
	  else{
		$this->creeNouveauItem("/prepa_conseil/index1.php",
				"Visualize the averages and appreciations of the report cards",
				"Summary counts of your averages and/or appreciations appearing in the
school carryforwards with display of useful statistics for the filling
of the school carryforwards.");
	  }

	}

	if ($conditions_moyennes)  {
	  $this->creeNouveauItem("/prepa_conseil/index2.php",
			  "Visualiser toutes les moyennes d'une classe",
			  "Tableau récapitulatif des moyennes d'une classe.");
	}

	if ($conditions_bulsimples) {
	  $this->creeNouveauItem("/prepa_conseil/index3.php",
			  "Visualize simplified report cards",
			  "Simplified report cards of a class.");
	}
	elseif(($this->statutUtilisateur=='professeur')&&(getSettingValue("GepiAccesReport cardSimplePP")=="yes")) {
	  $sql="SELECT 1=1 FROM j_eleves_professeurs ;";
	  $test_pp=mysql_num_rows(mysql_query($sql));
	  if($test_pp>0) {
		$this->creeNouveauItem("/prepa_conseil/index3.php",
				"Visualize simplified report cards",
				"Simplified report cards of a class.");
	  }
	}

	$call_data = mysql_query("SELECT * FROM aid_config
					WHERE display_bulletin = 'y'
					OR bull_simplifie = 'y'
					ORDER BY nom");
	$nb_aid = mysql_num_rows($call_data);

	$i=0;
	while ($i < $nb_aid) {
	  $indice_aid = @mysql_result($call_data, $i, "indice_aid");
	  $call_prof = mysql_query("SELECT * FROM j_aid_utilisateurs
								WHERE indice_aid = '".$indice_aid."'");
	  $nb_result = mysql_num_rows($call_prof);
	  if ($nb_result != 0) {
		$nom_aid = @mysql_result($call_data, $i, "nom");
		$this->creeNouveauItem("/prepa_conseil/visu_aid.php?indice_aid=".$indice_aid,
				"Visualize appreciations ".$nom_aid,
				"This tool allows visualization and printing of appreciations of ".$this->gepiSettings['denomination_eleves']." for the ".$nom_aid.".");
	  }
	  $i++;
	}

	if(($this->statutUtilisateur=='professeur')&&(getSettingValue('GepiAccesGestElevesProfP')=='yes')) {
	  // Le professeur est-il professeur principal dans une classe au moins.
	  $sql="SELECT 1=1 FROM j_eleves_professeurs ;";
	  $test=mysql_query($sql);
	  if (mysql_num_rows($test)>0) {
		$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
		$this->creeNouveauItem("/eleves/index.php",
				"Management of ".$this->gepiSettings['denomination_eleves'],
				"This tool gives access information of ".$this->gepiSettings['denomination_eleves']." you are ".$gepi_prof_suivi.".");
	  }
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Visualization - Printing",'images/icons/print.png');
	  return true;
	}

  }

  private function notanet(){
	$this->b=0;

	$affiche='yes';
	if($this->statutUtilisateur=='professeur') {
	  $sql="SELECT DISTINCT g.*,c.classe FROM groupes g,
						  j_groupes_classes jgc,
						  j_groupes_professeurs jgp,
						  j_groupes_matieres jgm,
						  classes c,
						  notanet n
					  WHERE g.id=jgc.id_groupe AND
						  jgc.id_classe=n.id_classe AND
						  jgc.id_classe=c.id AND
						  jgc.id_groupe=jgp.id_groupe AND
						  jgm.id_groupe=g.id AND
						  jgm.id_matiere=n.matiere
					  ORDER BY jgc.id_classe;";
	  //echo "$sql<br />";
	  $res_grp=mysql_query($sql);
	  if(mysql_num_rows($res_grp)==0) {
		  $affiche='no';
	  }
	}

	if ((getSettingValue("active_notanet")=='y')&&($affiche=='yes')) {
	  if($this->statutUtilisateur=='professeur') {
		$this->creeNouveauItem("/mod_notanet/index.php",
				"Notanet/Fiches Brevet",
				"Cet outil permet de saisir les appréciations pour les Fiches Brevet.");
	  }
	  else {
		$this->creeNouveauItem("/mod_notanet/index.php",
				"Notanet/Fiches Brevet",
				"Notanet/Fiches Patent "," This tool allows:<br/> - to curry out
calculations and the generation of CSV spins necessary for Notanet.
The operation also provides information to the tables necessary to
generate the patent cards.<br/> - to generate patent cards");
	  }
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Notanet/Brevet Cards",'images/icons/document.png');
	  return true;
	}
  }

  private function anneeAnterieure(){
	$this->b=0;

	if (getSettingValue("active_annees_anterieures")=='y') {

	  if($this->statutUtilisateur=='administrateur'){
		$this->creeNouveauItem("/mod_annees_anterieures/index.php",
				"Previous years",
				"This tool allows to generate and consult data of previous years (simplified reports,...).");
	  }
	  else{
		if($this->statutUtilisateur=='professeur') {
		  $AAProfTout=getSettingValue('AAProfTout');
		  $AAProfPrinc=getSettingValue('AAProfPrinc');
		  $AAProfClasses=getSettingValue('AAProfClasses');
		  $AAProfGroupes=getSettingValue('AAProfGroupes');

		  if(($AAProfTout=="yes")||($AAProfClasses=="yes")||($AAProfGroupes=="yes")){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Previous years",
					"This tool allows to generate and consult data of previous years
(simplified reports).");
		  }
		  elseif($AAProfPrinc=="yes"){
			$sql="SELECT 1=1 FROM classes c,
									j_eleves_professeurs jep
							WHERE jep.id_classe=c.id
							ORDER BY c.classe";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Previous years",
					  "This tool allows to generate and consult data of previous years
(simplified reports).");
			}
		  }

		}
		elseif($this->statutUtilisateur=='scolarite') {
		  $AAScolTout=getSettingValue('AAScolTout');
		  $AAScolResp=getSettingValue('AAScolResp');

		  if($AAScolTout=="yes"){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Previous years",
					"This tool allows to generate and consult data of previous years
(simplified reports).");
		  }
		  elseif($AAScolResp=="yes"){
			$sql="SELECT 1=1 FROM j_scol_classes ;";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Previous years",
					  "This tool allows to generate and consult data of previous years(simplified reports).");
			}
		  }

		}
		elseif($this->statutUtilisateur=='cpe') {
		  $AACpeTout=getSettingValue('AACpeTout');
		  $AACpeResp=getSettingValue('AACpeResp');

		  if($AACpeTout=="yes"){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Previous years",
					"This tool allows to generate and consult data of previous years(simplified reports).");
		  }
		  elseif($AACpeResp=="yes"){
			$sql="SELECT 1=1 FROM j_eleves_cpe ";
			$test=mysql_query($sql);

			if(mysql_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Previous years",
					  "This tool allows to generate and consult data of previous years(simplified reports).");
			}

		  }

		}
		elseif($this->statutUtilisateur=='responsable') {
		  $AAResponsable=getSettingValue('AAResponsable');

		  if($AAResponsable=="yes"){
			// Est-ce que le responsable est bien associé à un élève?
			$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e
				WHERE rp.pers_id=r.pers_id AND
					  r.ele_id=e.ele_id ";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Previous years",
					  "This tool allows to generate and consult data of previous years(simplified reports).");
 			}
		  }

		}
		elseif($this->statutUtilisateur=='eleve') {
		  $AAEleve=getSettingValue('AAEleve');

		  if($AAEleve=="yes"){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Previous years",
					"This tool allows to generate and consult data of previous years(simplified reports)..");
		  }

		}

	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Previous years",'images/icons/document.png');
	  return true;
	}
  }









  private function plugins(){
	$this->b=0;

	$query = mysql_query('SELECT * FROM plugins WHERE ouvert = "y" order by description');

	while ($plugin = mysql_fetch_object($query)){
	$this->b=0;
	  $nomPlugin=$plugin->nom;
	  $this->verif_exist_ordre_menu('bloc_plugin_'.$nomPlugin);
	  // On offre la possibilité d'inclure un fichier functions_nom_du_plugin.php
	  // Ce fichier peut lui-même contenir une fonction calcul_autorisation_nom_du_plugin voir plus bas.
	  if (file_exists($this->cheminRelatif."mod_plugins/".$nomPlugin."/functions_".$nomPlugin.".php"))
		include_once($this->cheminRelatif."mod_plugins/".$nomPlugin."/functions_".$nomPlugin.".php");

	  $querymenu = mysql_query('SELECT * FROM plugins_menus
								WHERE plugin_id = "'.$plugin->id.'"
								ORDER by titre_item');

	  while ($menuItem = mysql_fetch_object($querymenu)){
		// On regarde si le plugin a prévu une surcharge dans le calcul de l'affichage de l'item dans le menu
		// On commence par regarder si une fonction du type calcul_autorisation_nom_du_plugin existe
		$nom_fonction_autorisation = "calcul_autorisation_".$nomPlugin;


		  $result_autorisation=true;

		if (($menuItem->user_statut == $this->statutUtilisateur) and ($result_autorisation)) {
		  $this->creeNouveauItemPlugin("/".$menuItem->lien_item,
				supprimer_numero(iconv("utf-8","iso-8859-1",$menuItem->titre_item)),
				iconv("utf-8","iso-8859-1",$menuItem->description_item));
		}

	  }

	  if ($this->b>0){
		$descriptionPlugin= iconv("utf-8","iso-8859-1",$plugin->description);
		$this->creeNouveauTitre('accueil',"$descriptionPlugin",'images/icons/package.png');
		$this->chargeAutreNom('bloc_plugin_'.$nomPlugin);
	  }

	}

  }



  private function fluxRSS(){
	$this->b=0;

	if (getSettingValue("rss_cdt_eleve") == 'y' AND $this->statutUtilisateur == "eleve") {
	  // Les flux rss sont ouverts pour les élèves
	  $this->canal_rss_flux=1;

	  // A vérifier pour les cdt
	  if (getSettingValue("rss_acces_ele") == 'direct') {
	// echo "il y a un flux RSS direct";
		$this->canal_rss=array("lien"=>"student URL" ,
				  "texte"=>"student URL",
				  "mode"=>1 ,
				  "expli"=>"By clicking on the left cell,
				  you can get your URI (if javascript is enabled on your browser).");
	  }elseif(getSettingValue("rss_acces_ele") == 'csv'){
		$this->canal_rss=array("lien"=>"" , "texte"=>"", "mode"=>2, "expli"=>"");
	  }

	  $this->creeNouveauTitre('accueil',"Your RSS Feed",'images/icons/rss.png');
	  return true;
	}

  }








  private function gestionEleveAID(){
	$this->b=0;

	if (getSettingValue("active_mod_gest_aid")=='y') {

	  $sql = "SELECT * FROM aid_config ";
	  // on exclue la rubrique permettant de visualiser quels élèves ont le droit d'envoyer/modifier leur photo
	  $flag_where = 'n';

	  if (getSettingValue("num_aid_trombinoscopes") != "") {
		$sql .= "WHERE indice_aid!= '".getSettingValue("num_aid_trombinoscopes")."'";
		$flag_where = 'y';
	  }

	  // si le plugin "gestion_autorisations_publications" existe et est activé, on exclue la rubrique correspondante
	  $test_plugin = sql_query1("select ouvert from plugins where nom='gestion_autorisations_publications'");

	  if (($test_plugin=='y') and (getSettingValue("indice_aid_autorisations_publi") != ""))
		if ($flag_where == 'n')
		  $sql .= "WHERE indice_aid!= '".getSettingValue("indice_aid_autorisations_publi")."'";
		else
		  $sql .= "and indice_aid!= '".getSettingValue("indice_aid_autorisations_publi")."'";

	  $sql .= " ORDER BY nom";
	  $call_data = mysql_query($sql);
	  $nb_aid = mysql_num_rows($call_data);
	  $i=0;

	  while ($i < $nb_aid) {
		$indice_aid = @mysql_result($call_data, $i, "indice_aid");
		$call_prof1 = mysql_query("SELECT *
					FROM j_aid_utilisateurs_gest
					WHERE indice_aid = '".$indice_aid."'");
		$nb_result1 = mysql_num_rows($call_prof1);
		$call_prof2 = mysql_query("SELECT *
					FROM j_aidcateg_super_gestionnaires
					WHERE indice_aid = '".$indice_aid."'");
		$nb_result2 = mysql_num_rows($call_prof2);

		if (($nb_result1 != 0) or ($nb_result2 != 0)) {
		  $nom_aid = @mysql_result($call_data, $i, "nom");
  		if ($nb_result2 != 0)
      		$this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "This tool enables you to manage groups (creation, deletion, modification).");
			else
      		$this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "This tool enables you to manage the membership of the students to the various groups.");
		}

		$i++;
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Management of AID",'images/icons/document.png');
	  return true;
	}
  }










  private function chargeAutreNom($bloc){

	$this->titre_Menu[$this->a]->bloc=$bloc;
	$sql1="SHOW TABLES LIKE 'mn_ordre_accueil'";
	$resp1 = mysql_query($sql1);

	if(mysql_num_rows($resp1)>0) {
	  $sql="SELECT nouveau_nom FROM mn_ordre_accueil
			WHERE bloc LIKE '$bloc'
			AND statut LIKE '$this->statutUtilisateur'
			;";
	  $resp=mysql_query($sql);

	  if (mysql_num_rows($resp)>0){
		$this->titre_Menu[$this->a]->nouveauNom=mysql_fetch_object($resp)->nouveau_nom;
	  }else{
		$this->titre_Menu[$this->a]->nouveauNom="";
	  }

	}else{
		$this->titre_Menu[$this->a]->nouveauNom="";
	}

  }


}

?>
