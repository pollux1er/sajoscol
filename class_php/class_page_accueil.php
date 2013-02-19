
<?php
/*
 * $Id: class_page_accueil.php 8099 2011-09-01 12:59:16Z jjacquard $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
 * Description of class_page_accueil
 *
 * @author regis
 */
class class_page_accueil {

  public $titre_Menu=array();
  public $menu_item=array();
  public $canal_rss=array();
  public $message_admin=array();
  public $nom_connecte=array();
  public $referencement=array();
  public $message=array();
  public $probleme_dir=array();
  public $canal_rss_flux="";
  public $gere_connect="";
  public $alert_sums="";
  public $signalement="";
  public $nb_connect="";
  public $nb_connect_lien="";

  protected $ordre_menus=array();
  protected $cheminRelatif="";
  protected $loginUtilisateur="";
  public $statutUtilisateur="";
  protected $gepiSettings="";
  protected $test_prof_matiere="";
  protected $test_prof_suivi="";
  protected $test_prof_ects="";
  protected $test_scol_ects="";
  protected $test_prof_suivi_ects="";
  protected $test_https="";
  protected $a=0;
  protected $b=0;

/**
 * Construit les entrées de la page d'accueil
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
	$this->test_prof_matiere = sql_count(sql_query("SELECT login
			FROM j_groupes_professeurs
			WHERE login = '".$this->loginUtilisateur."'"));

// On teste si le l'utilisateur est prof de suivi. Si oui on affiche la ligne relative remplissage de l'avis du conseil de classe
	$this->test_prof_suivi = sql_count(sql_query("SELECT professeur
			FROM j_eleves_professeurs
			WHERE professeur = '".$this->loginUtilisateur."'"));

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
	//if (getSettingAOui('autorise_edt_tous')){
	  $this->verif_exist_ordre_menu('bloc_emploi_du_temps');
	  if ($this->emploiDuTemps())
	  $this->chargeAutreNom('bloc_emploi_du_temps');
	//}

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

	if (getSettingAOui("active_mod_discipline")) {
		// Discipline
		$this->verif_exist_ordre_menu('bloc_module_discipline_famille');
		if ($this->modDiscFamille())
		$this->chargeAutreNom('bloc_module_discipline_famille');
	}
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
	if (getSettingAOui("active_mod_discipline")) {
	  $this->verif_exist_ordre_menu('bloc_module_discipline');
	  if ($this->discipline())
	  $this->chargeAutreNom('bloc_module_discipline');
	}

/***** Module Modèle Open Office *****/
	$this->verif_exist_ordre_menu('bloc_modeles_Open_Office');
	if ($this->modeleOpenOffice())
	$this->chargeAutreNom('bloc_modeles_Open_Office');

/***** Module plugins : affichage des menus des plugins en fonction des droits *****/
	$this->verif_exist_ordre_menu('');
	$this->plugins();

/***** Module Genese des classes *****/
	if (getSettingAOui("active_mod_genese_classes")) {
	  $this->verif_exist_ordre_menu('bloc_Genese_classes');
	  if ($this->geneseClasses())
	  $this->chargeAutreNom('bloc_Genese_classes');
	}

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

  protected function creeNouveauTitre($classe,$texte,$icone,$titre="",$alt=""){
	$this->titre_Menu[$this->a]=new menuGeneral();
	$this->titre_Menu[$this->a]->indexMenu=$this->a;
	$this->titre_Menu[$this->a]->classe=$classe;
	$this->titre_Menu[$this->a]->texte=$texte;
	$this->titre_Menu[$this->a]->icone['chemin']=$this->cheminRelatif.$icone;
	$this->titre_Menu[$this->a]->icone['titre']=$titre;
	$this->titre_Menu[$this->a]->icone['alt']=$alt;
  }

  protected function creeNouveauItem($chemin,$titre,$expli){
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin=$chemin;
	if ($nouveauItem->acces($nouveauItem->chemin,$this->statutUtilisateur))
	{
	  $nouveauItem->indexMenu=$this->a;
	  $nouveauItem->titre=$titre;
	  $nouveauItem->expli=$expli;
	  $nouveauItem->indexItem=$this->b;
	  $this->menu_item[]=$nouveauItem;
	  $this->b++;
	}
	unset($nouveauItem);
  }

  protected function creeNouveauItemPlugin($chemin,$titre,$expli){
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin=$chemin;
	$nouveauItem->indexMenu=$this->a;
	$nouveauItem->titre=$titre;
	$nouveauItem->expli=$expli;
	$nouveauItem->indexItem=$this->b;
	$this->menu_item[]=$nouveauItem;
	$this->b++;
	unset($nouveauItem);
  }

  protected function administration() {
	if ($this->statutUtilisateur == 'administrateur'){

	  $this->b=0;

	  $this->creeNouveauItem('/gestion/accueil_sauve.php',
			  "Data base saving",
			  "Data base saving, the following directories: \"documents\" (containing documents attached to the diaries) et \"photos\" (containing  photos) will not be saved.");

	  $this->creeNouveauItem('/gestion/index.php',
			  "General management",
			  "To access the management tools (security, general configuration, data bases, GEPI initialization).");
	  $this->creeNouveauItem('/accueil_modules.php',
			  "Modules management",
			  "To manage modules (diaries, school reports, absences, class album).");
	  $this->creeNouveauItem('/accueil_admin.php',
			  "Bases managment",
			  "To manage databases (institution, users, subjects, classes, ".$this->gepiSettings['denomination_eleves'].", ".$this->gepiSettings['denomination_responsables'].", AIDs).");
	  if (getSettingValue('use_ent') == 'y') {
		  // On ajoute la page du module ENT
		$this->creeNouveauItem('/mod_ent/index.php',
				"Liaison ENT",
				"Entrer en liaison avec l\'ENT pour gérer les utilisateurs et récupérer les logins pour le sso");
	  }

	  if ($this->b>0){
		$this->creeNouveauTitre('accueil',"Management",'images/icons/configure.png');
		return true;
	  }
	}
  }

  protected function absences_vie_scol() {

			$this->b=0;
		if (getSettingValue("active_module_absence")=='y') {

			$this->creeNouveauItem('/mod_absences/gestion/gestion_absences.php',
					"Manage Absences, examptions, lateness et infirmary",
					"This tool allows you to manage absences, examptions, lateness and sickness at infirmary of ".$this->gepiSettings['denomination_eleves'].".");

			$this->creeNouveauItem('/mod_absences/gestion/voir_absences_viescolaire.php',
					"View absences",
					"You can view niche by niche absences.");


	  } else if (getSettingValue("active_module_absence")=='2' && ($this->statutUtilisateur=="scolarite" || $this->statutUtilisateur=="cpe")) {
		$this->creeNouveauItem("/mod_abs2/index.php",
				"Absences Management",
				"This tool allows you to manage students absences");
	  }

			if ($this->b>0){
			$this->creeNouveauTitre('accueil',"Management of late coming and absences",'images/icons/absences.png');
			return true;
			}

  }

  protected function absences_profs(){

	if (getSettingValue("active_module_absence_professeur")=='y') {

	  $this->b=0;

	  $nouveauItem = new itemGeneral();
	  if (getSettingValue("active_module_absence")=='y' ) {
		$this->creeNouveauItem("/mod_absences/professeurs/prof_ajout_abs.php",
				"Absences Management",
				"This tool allows you to manage students absences");
	  } else if (getSettingValue("active_module_absence")=='2' && !($this->statutUtilisateur=="scolarite" || $this->statutUtilisateur=="cpe") ) {
		$this->creeNouveauItem("/mod_abs2/index.php",
				"Absences Management",
				"This tool allows you to manage students absences");
	  }

	  if ($this->b>0){
		$this->creeNouveauTitre('accueil',"Management of late coming and absences",'images/icons/absences.png');
		return true;
	  }

    }
  }

  private function saisie(){

	$this->b=0;

	$afficher_correction_validation="n";
	$sql="SELECT 1=1 FROM matieres_app_corrections;";
	$test_mac=mysql_query($sql);
	if($test_mac AND mysql_num_rows($test_mac)>0) {$afficher_correction_validation="y";}


        if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
	  $this->creeNouveauItem("/absences/index.php",
			  "School Report : absences entry",
			  "This tool allows you to enter absences on school reports." );
        }


	if ((($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur'))
			and (getSettingValue("active_cahiers_texte")=='y'))
	  $this->creeNouveauItem("/cahier_texte/index.php",
			  "Log book",
			  "This tool allows you to create a log book for each class." );

	if ((($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur'))
			and (getSettingValue("active_carnets_notes")=='y'))
	  $this->creeNouveauItem("/cahier_notes/index.php",
			  "Notebook : notes entry",
			  "This tool allows you to create a notebook for each period and enter notes for all your assessments.");

	if (($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur'))
	  $this->creeNouveauItem("/saisie/index.php",
			  "School report : entry of averages and appreciations per subject",
			  "This tool allows to enter directly, without using the notebook, the averages and the appreciations of the school report");

	if($afficher_correction_validation=="y")
	  $this->creeNouveauItem("/saisie/validation_corrections.php",
			  "Correction des bulletins",
			  "This tool allows you to validate the corrections of appreciations proposed by teachers after the closure of a period.<br /><span style='color:red;'>One or more proposals need your attention.</span>\n");

	if ((($this->test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes'))
			or (($this->statutUtilisateur!='professeur') and (getSettingValue("GepiRubConseilScol")=='yes') )
			or ($this->statutUtilisateur=='secours')  )
	  $this->creeNouveauItem("/saisie/saisie_avis.php",
			  "School report : enter staff meeting opinions",
			  "This tool allows entry of the staff meeting opinions.");

	// Saisie ECTS - ne doit être affichée que si l'utilisateur a bien des classes ouvrant droit à ECTS
	if ($this->statutUtilisateur == 'professeur') {
		$this->test_prof_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_groupes_professeurs jgp
				WHERE (jgc.saisie_ects = TRUE
				  AND jgc.id_groupe = jgp.id_groupe
				  AND jgp.login = '".$this->loginUtilisateur."')"));
		$this->test_prof_suivi_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_eleves_professeurs jep, j_eleves_groupes jeg
				WHERE (jgc.saisie_ects = TRUE
				AND jgc.id_groupe = jeg.id_groupe
				AND jeg.login = jep.login AND jep.professeur = '".$this->loginUtilisateur."')"));
	} else {
		$this->test_scol_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_scol_classes jsc
				WHERE (jgc.saisie_ects = TRUE
				AND jgc.id_classe = jsc.id_classe
				AND jsc.login = '".$this->loginUtilisateur."')"));
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
	  $this->creeNouveauItem("/mod_ects/index_saisie.php","Crédits ECTS","Saisie des crédits ECTS");

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
								WHERE (id_utilisateur = '".$this->loginUtilisateur."'
								AND indice_aid = '".$indice_aid."')");
	  $nb_result = mysql_num_rows($call_prof);
	  if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
		$nom_aid = @mysql_result($call_data, $i, "nom");
		$this->creeNouveauItem("/saisie/saisie_aid.php?indice_aid=".$indice_aid,
				$nom_aid,
				"Cet outil permet la saisie des appréciations des ".$this->gepiSettings['denomination_eleves']." pour les $nom_aid.");
	  }
	  $i++;
	}

	//==============================
// Pour permettre la saisie de commentaires-type, renseigner la variable $commentaires_types dans /lib/global.inc
// Et récupérer le paquet commentaires_types sur... ADRESSE A DEFINIR:
	if(file_exists('saisie/commentaires_types.php')) {
	  if ((($this->statutUtilisateur=='professeur')
			  AND (getSettingValue("CommentairesTypesPP")=='yes')
			  AND (mysql_num_rows(mysql_query("SELECT 1=1 FROM j_eleves_professeurs
											  WHERE professeur='".$this->loginUtilisateur."'"))>0))
			  OR (($this->statutUtilisateur=='scolarite')
					  AND (getSettingValue("CommentairesTypesScol")=='yes')))
	  {
		$this->creeNouveauItem("/saisie/commentaires_types.php",
				"Entry of comments",
				"Allows to define comments for the staff meeting opinion.");
	  }
	}

	  if ($this->b>0){
		$this->creeNouveauTitre('accueil',"Log Book",'images/icons/configure.png');
		return true;
	  }
  }

  private function cahierTexteCPE(){
	$this->b=0;

	$condition = (
	getSettingValue("active_cahiers_texte")=='y' AND (
		($this->statutUtilisateur == "cpe"
			AND getSettingValue("GepiAccesCdtCpe") == 'yes'
			AND getSettingValue("GepiAccesCdtCpeRestreint") != 'yes')
		OR ($this->statutUtilisateur == "scolarite"
			AND getSettingValue("GepiAccesCdtScol") == 'yes'
			AND getSettingValue("GepiAccesCdtScolRestreint") != 'yes')
	));

	if ($condition) {
	  $this->creeNouveauItem("/cahier_texte_2/see_all.php",
			  "Log books",
			  "Allows to consult reports of sessions and assignments to do for the teachings off all the ".$this->gepiSettings['denomination_eleves']);
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Diary",'images/icons/document.png');
	  return true;
	}
  }

  protected function cahierTexteCPE_Restreint(){
	$this->b=0;

	$condition = (
	getSettingValue("active_cahiers_texte")=='y' AND (
		($this->statutUtilisateur == "cpe" AND getSettingValue("GepiAccesCdtCpeRestreint") == 'yes')
		OR ($this->statutUtilisateur == "scolarite" AND getSettingValue("GepiAccesCdtScolRestreint") == 'yes')
	));

	if ($condition) {
	  $this->creeNouveauItem("/cahier_texte_2/see_all.php",
			  "Log books of classes followed",
			  "Allows to consult reports of session and assignments to do for the teachings of the ".$this->gepiSettings['denomination_eleves']." dont vous avez la responsabilité");
	}
	if ($this->b>0)
	  $this->creeNouveauTitre('accueil',"Cahier de texte",'images/icons/document.png');
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
								  WHERE (id_utilisateur = '" . $this->loginUtilisateur . "'
								  AND indice_aid = '$indice_aid')");
		$nb_result = mysql_num_rows($call_prof);
		if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
		  $nom_aid = @mysql_result($call_data, $i, "nom");
		  $this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "This tool allows you to visualize which ".$this->gepiSettings['denomination_eleves']." have the right to upload/update their photo.");
		}
		$i++;
	  }
	}

	  if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Trombinoscope",'images/icons/trombinoscope.png');
		return true;
	  }
  }

  protected function releve_notes(){
	$this->b=0;

	$condition = ((getSettingValue("active_carnets_notes")=='y')
	  AND
        ((($this->statutUtilisateur == "scolarite") AND (getSettingValue("GepiAccesReleveScol") == "yes"))
        OR
        (
        ($this->statutUtilisateur == "professeur") AND
            (
            (getSettingValue("GepiAccesReleveProf") == "yes") OR
            (getSettingValue("GepiAccesReleveProfTousEleves") == "yes") OR
            (getSettingValue("GepiAccesReleveProfToutesClasses") == "yes") OR
            ((getSettingValue("GepiAccesReleveProfP") == "yes") AND ($this->test_prof_suivi != "0"))
            )
        )
        OR
        (($this->statutUtilisateur == "cpe") AND getSettingValue("GepiAccesReleveCpe") == "yes")));

	$condition2 = ($this->statutUtilisateur != "professeur" OR
				(
				$this->statutUtilisateur == "professeur" AND
				(
	            	(getSettingValue("GepiAccesMoyennesProf") == "yes") OR
	            	(getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
	            	(getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")
				)
				)
			);

	if ($condition)
	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes_bis.php",
			  "Visualization and printing of report cards",
			  "This tool allows you to visualize on screen and print report cards, ".$this->gepiSettings['denomination_eleve']." by ".$this->gepiSettings['denomination_eleve'].", class by class.");

	if ($condition && $condition2)
	  $this->creeNouveauItem("/cahier_notes/index2.php",
			  "Visualization of averages of note books",
			  "This tool allows you to visualize on screen averages calculated from the content of the note books, regardless of the entry of averages on the report cards.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Statements of notes",'images/icons/releve.png');
	  return true;
	}

  }

  protected function releve_ECTS(){
	$this->b=0;

	$condition = ($this->gepiSettings['active_mod_ects'] == 'y'
			and ((($this->test_prof_suivi != "0")
				and ($this->gepiSettings['GepiAccesEditionDocsEctsPP'] =='yes')
				and $this->test_prof_ects != "0")
			  or (($this->statutUtilisateur=='scolarite')
				and ($this->gepiSettings['GepiAccesEditionDocsEctsScolarite'] =='yes')
				and $this->test_scol_ects != "0")
			  or ($this->statutUtilisateur=='secours')  )
			);

	$chemin = array();
	if ($condition)
	  $this->creeNouveauItem("/mod_ects/edition.php",
			  "ECTS documents creation",
			  "This tool allows you to create ECTS documents (statement, attestation, annex) for the concerned classes.");

	$recap_ects = ($this->gepiSettings['active_mod_ects'] == 'y'
					and (
					  ($this->statutUtilisateur == 'professeur'
						and $this->gepiSettings['GepiAccesRecapitulatifEctsProf'] == 'yes'
						and $this->test_prof_ects != '0')
					  or ($this->statutUtilisateur == 'scolarite'
						and $this->gepiSettings['GepiAccesRecapitulatifEctsScolarite'] == 'yes'
						and $this->test_scol_ects != '0')
					)
				  );
	if ($recap_ects)
	  $this->creeNouveauItem("/mod_ects/recapitulatif.php",
			  "Visualize all ECTS",
			  "Visualiser les tableaux récapitulatif by class of all ECTS credits.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Documents ECTS",'images/icons/releve.png');
	  return true;
	}
  }

  private function emploiDuTemps(){
	$this->b=0;
  if (getSettingAOui('autorise_edt_tous') || (getSettingAOui('autorise_edt_admin') && $this->statutUtilisateur == 'administrateur')){
    $this->creeNouveauItem("/edt_organisation/index_edt.php",
			"Timetable",
			"This tool allows consultation/management of the schedule.");

	if ($_SESSION["statut"] == 'responsable') {
	  if (getSettingValue("autorise_edt_eleve")=="yes"){
		// on propose l'edt d'un élève, les autres enfants seront disponibles dans la page de l'edt.
		$tab_tmp_ele = get_enfants_from_resp_login($this->loginUtilisateur);
		$this->creeNouveauItem("/edt_organisation/edt_eleve.php?login_edt=".$tab_tmp_ele[0],
			  "Timetable",
			  "This tool allows consultation of the schedule of your child.");
	  }
	}else if($_SESSION["statut"] == 'eleve'){
	  if (getSettingValue("autorise_edt_eleve")=="yes"){
		$this->creeNouveauItem("/edt_organisation/edt_eleve.php",
			  "Timetable",
			  "This tool allows consultation of your schedule.");
	  }
	}else{
	  $this->creeNouveauItem("/edt_organisation/edt_eleve.php",
			  "Timetable",
			  "This tool allows consultation of your schedule.");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Timetable",'images/icons/document.png');
	  return true;
	}
	}
 }

  private function cahierTexteFamille(){
	$this->b=0;

	$condition = (
	getSettingValue("active_cahiers_texte")=='y' AND (
		($this->statutUtilisateur == "responsable" AND getSettingValue("GepiAccesCahierTexteParent") == 'yes')
		OR ($this->statutUtilisateur == "eleve" AND getSettingValue("GepiAccesCahierTexteEleve") == 'yes')
	));

	if ($condition) {
		if ($this->statutUtilisateur == "responsable") {
		  $this->creeNouveauItem("/cahier_texte/consultation.php",
				  "Log books",
				  "This tool allows to consult reports of session and assignments to do for the ".$this->gepiSettings['denomination_eleves']." that you are the ".$this->gepiSettings['denomination_responsable'].".");
		} else {
		  $this->creeNouveauItem("/cahier_texte/consultation.php",
				  "Log books",
				  "Allows to consult reports of sessions et assignments to do for the teachings that you follow.");
		}
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Log book",'images/icons/document.png');
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
		if ($this->statutUtilisateur == "responsable") {
		  $this->creeNouveauItem("/cahier_notes/visu_releve_notes_bis.php",
				  "Report cards",
				  "Allows to consult report cards of ".$this->gepiSettings['denomination_eleves']." that you are the ".$this->gepiSettings['denomination_responsable'].".");
		} else {
		  $this->creeNouveauItem("/cahier_notes/visu_releve_notes_bis.php",
				  "Report card",
				  "Allows to consult your detailed report cards.");
		}
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Report Card",'images/icons/releve.png');
	  return true;
	}
  }


  private function modDiscFamille(){
	$this->b=0;

	if(($_SESSION['statut']=='eleve')) {
		if(getSettingValue('visuEleDisc')=='yes') {
			$this->creeNouveauItem("/mod_discipline/visu_disc.php",
					"Discipline",
					"Incidents concerning you.");
		}
	}
	elseif(($_SESSION['statut']=='responsable')) {
		if(getSettingValue('visuRespDisc')=='yes') {
			$this->creeNouveauItem("/mod_discipline/visu_disc.php",
					"Discipline",
					"Incidents concerning the students/children you are responsible.");
		}
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Report card",'images/icons/releve.png');
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
	  if ($this->statutUtilisateur == "responsable") {
		  $this->creeNouveauItem("/groupes/visu_profs_eleve.php",
				  "Teaching staff",
				  "Allows to consult the teaching staff of ".$this->gepiSettings['denomination_eleves']." you are ".$this->gepiSettings['denomination_responsable'].".");
		 } else {
		  $this->creeNouveauItem("/groupes/visu_profs_eleve.php",
				  "Teaching staff",
				  "Allows to consult the teaching staff that concerns you.");
		}
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
	  if ($this->statutUtilisateur == "responsable") {
		  $this->creeNouveauItem("/prepa_conseil/index3.php",
				  "Simplified report cards",
				  "Allows to consult simplified repord cards of ".$this->gepiSettings['denomination_eleves']." you are ".$this->gepiSettings['denomination_responsable'].".");
		 } else {
		  $this->creeNouveauItem("/prepa_conseil/index3.php",
				  "Simplified report cards",
				  "Allows to consult your report card simplified.");
		}
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Simplified report card",'images/icons/bulletin_simp.png');
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
	  if ($this->statutUtilisateur == "responsable") {
		  $this->creeNouveauItem("/visualisation/affiche_eleve.php",
				  "Graphic visualization",
				  "Allows to visualize in graphic form the results of ".$this->gepiSettings['denomination_eleves']." of which you are ".$this->gepiSettings['denomination_responsable'].", compared to the class.");
		} else {
		  $this->creeNouveauItem("/visualisation/affiche_eleve.php",
				  "Graphic visualization",
				  "Allows to visualize your results in graphic form compared to the class.");
		}
    }

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Graphic visualization",'images/icons/graphes.png');
	  return true;
	}
  }

  protected function absencesFamille(){
	$this->b=0;
	$conditions2 = ($this->statutUtilisateur == "responsable" AND
					getSettingValue("active_module_absence") == '2' AND
					getSettingAOui("active_absences_parents"));

	$conditions3 = ($this->statutUtilisateur == "responsable" AND
					getSettingValue("active_module_absence") == 'y' AND
					getSettingAOui("active_absences_parents"));

	if ($conditions2) {
	  $this->creeNouveauItem("/mod_abs2/bilan_parent.php",
			  "Absences",
			  "Allows to follow the absences and the delays of the pupils of which I
am ".$this->gepiSettings['denomination_responsable']);

	} else if ($conditions3) {
	  $this->creeNouveauItem("/mod_absences/absences.php",
			  "Absences",
			  "Allows to follow the absences and the delays of the pupils of which I
am ".$this->gepiSettings['denomination_responsable']);
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Absences",'images/icons/absences.png');
	  return true;
	}
  }

  protected function gestionAID(){
	$this->b=0;

	$call_data = sql_query("SELECT distinct ac.indice_aid, ac.nom
		  FROM aid_config ac, aid a
		  WHERE ac.outils_complementaires = 'y'
		  AND a.indice_aid=ac.indice_aid
		  ORDER BY ac.nom_complet");
	$nb_aid = mysql_num_rows($call_data);

	$call_data2 = sql_query("SELECT id
		  FROM archivage_types_aid
		  WHERE outils_complementaires = 'y'");
	$nb_aid_annees_anterieures = mysql_num_rows($call_data2);
	$nb_total=$nb_aid+$nb_aid_annees_anterieures;

	if ($nb_total != 0) {
	  $i = 0;
	  while ($i<$nb_aid) {
		$indice_aid = mysql_result($call_data,$i,"indice_aid");
		$nom_aid = mysql_result($call_data,$i,"nom");
		if ($this->AfficheAid($indice_aid)) {
		  $this->creeNouveauItem("/aid/index_fiches.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "Summary table, list of ".$this->gepiSettings['denomination_eleves'].", ...");
		}
		$i++;
	  }
	  if (($nb_aid_annees_anterieures > 0)) {
		$this->creeNouveauItem("/aid/annees_anterieures_accueil.php",
				"Projects cards of the former years",
				"Access to project cards of the former years");
	  }
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',
			  "Tools of visualization and edition of the projects cards projects",
			  'images/icons/document.png');
	  return true;
	}
  }

  private function AfficheAid($indice_aid){
    if ($this->statutUtilisateur == "eleve") {
        $test = sql_query1("SELECT count(login) FROM j_aid_eleves
				  WHERE login='".$this->loginUtilisateur."'
				  AND indice_aid='".$indice_aid."' ");
        if ($test == 0)
            return false;
        else
            return true;
    } else
        return true;
  }

  protected function bulletins(){
	$this->b=0;

	if ((($this->test_prof_suivi != "0")
			and (getSettingValue("GepiProfImprBul")=='yes'))
			or ($this->statutUtilisateur!='professeur')){
	  $this->creeNouveauItem("/bulletin/verif_bulletins.php",
			  "Tool for checking",
			  "Allows to check if all the headings of the school reports are filled.");
	 }

	if ($this->statutUtilisateur!='professeur'){
	  $this->creeNouveauItem("/bulletin/autorisation_exceptionnelle_saisie_app.php",
			  "Special authorization of appreciations entry",
			  "Allows to authorize speciallly a teacher to submit an entry of appreciation for a teacher on period partialy closed.");
	}

	if ($this->statutUtilisateur!='professeur'){
	  $this->creeNouveauItem("/bulletin/verrouillage.php",
			  "Lock/Unlock period",
			  "Allows to lock or unlock a period for one or more class.");
	}

//==========================================================
// AJOUT: boireaus 20080219
//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves

//        Sur quel droit s'appuyer pour donner l'accès?
//            GepiAccesRestrAccesAppProfP : peut saisir les avis du conseil de classe pour sa classe
	if ((($this->test_prof_suivi != "0")
					AND ($this->statutUtilisateur=='professeur')
					AND (getSettingValue("GepiAccesRestrAccesAppProfP")=='yes'))
			 OR ($this->statutUtilisateur=='scolarite')
			 OR ($this->statutUtilisateur=='administrateur')){
	  $this->creeNouveauItem("/classes/acces_appreciations.php",
			  "Access of ".$this->gepiSettings['denomination_eleves']." and ".$this->gepiSettings['denomination_responsables']." to appreciations",
			  "Allow to define when ".$this->gepiSettings['denomination_eleves']." and ".$this->gepiSettings['denomination_responsables']." accounts(if they exist) can access to appreciations of ".$this->gepiSettings['denomination_professeurs']." on the school report and opinion of the class council.");
	 }

//==========================================================

	if ((($this->test_prof_suivi != "0")
					AND ($this->statutUtilisateur=='professeur')
					AND (getSettingValue("GepiProfImprBul")=='yes')
					AND (getSettingValue("GepiProfImprBulSettings")=='yes'))
			OR (($this->statutUtilisateur=='scolarite')
					AND (getSettingValue("GepiScolImprBulSettings")=='yes'))
			OR (($this->statutUtilisateur=='administrateur')
					AND (getSettingValue("GepiAdminImprBulSettings")=='yes'))){
	  $this->creeNouveauItem("/bulletin/param_bull.php",
			  "School reports printing parameters",
			  "Allows to modify parameters of school reports printing.");
	}

	if ($this->statutUtilisateur=='scolarite'){
	  $this->creeNouveauItem("/responsables/index.php",
			  "Management of the cards ".$this->gepiSettings['denomination_responsables'],
			  "This tool allows you modify/delete/add cards of ".$this->gepiSettings['denomination_responsables']." des ".$this->gepiSettings['denomination_eleves'].".");
	}

	if ($this->statutUtilisateur=='scolarite'){
	  $this->creeNouveauItem("/eleves/index.php",
			  "Management of cards ".$this->gepiSettings['denomination_eleves'],
			  "This tool allows you modify/delete/add cards ".$this->gepiSettings['denomination_eleves'].".");
	}

	if ((($this->test_prof_suivi != "0")
				AND (getSettingValue("GepiProfImprBul")=='yes'))
			OR ($this->statutUtilisateur!='professeur')){
	  $this->creeNouveauItem("/bulletin/bull_index.php",
			  "Visualization and printing of school reports",
			  "This tool allows to visualize on screen and print school reports, class by class.");
	}

	if ($this->statutUtilisateur=='administrateur'){
		$this->creeNouveauItem("/statistiques/index.php",
			  "Stats extraction",
			  "This tool allows you to extract data for stats (of school reports, ...).");

		$gepi_denom_mention=getSettingValue("gepi_denom_mention");
		if($gepi_denom_mention=="") {
			$gepi_denom_mention="mention";
		}

		$this->creeNouveauItem("/saisie/saisie_mentions.php",
			  ucfirst($gepi_denom_mention)."s of the reports",
			  "This tool allows you to define the ".$gepi_denom_mention."s (<i>Congratulations, Encouragements,...</i>) of the reports.");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"School report",'images/icons/bulletin_16.png');
	  return true;
	}
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
			"This allows you to know all the ".$this->gepiSettings['denomination_professeurs']." of classes in which you intervene, as well as the compositions of the groups concerned.");

	if(($this->statutUtilisateur=='scolarite')||
			($this->statutUtilisateur=='professeur')||
			($this->statutUtilisateur=='cpe')){
	  $this->creeNouveauItem("/groupes/visu_mes_listes.php",
			  "Visualization of my students",
			  "This menu allows you to consult your lists of".$this->gepiSettings['denomination_eleves']." by group made up and taught.");
	}

	if(getSettingValue('active_mod_ooo')=='y') {
		if(($this->statutUtilisateur=='scolarite')||
				($this->statutUtilisateur=='administrateur')||
				($this->statutUtilisateur=='professeur')||
				($this->statutUtilisateur=='cpe')){
		$this->creeNouveauItem("/mod_ooo/publipostage_ooo.php",
				"Publipostage OOo",
				"Ce menu permet de vous permet d'effectuer des publipostages OpenOffice.org à l'aide des données des tables 'eleves' et 'classes'.");
		}
	}

	$this->creeNouveauItem("/eleves/visu_eleve.php",
			"Consultation of a ".$this->gepiSettings['denomination_eleve'],
			"This menu allows you to consult in the same page the information concerning a ".$this->gepiSettings['denomination_eleve']." (followed teachings, school reports, note book, ".$this->gepiSettings['denomination_responsables'].",...). Some elements are available only for some categories of visitors.");

	if(getSettingValue("active_cahiers_texte")=="y") {
		if(($this->statutUtilisateur=="professeur") OR
			(($this->statutUtilisateur=="cpe")&&((getSettingValue("GepiAccesCdtCpe")=="yes")||(getSettingValue("GepiAccesCdtCpeRestreint")=="yes"))) OR
			(($this->statutUtilisateur == "scolarite")&&((getSettingValue("GepiAccesCdtScol")=="yes")||(getSettingValue("GepiAccesCdtScolRestreint")=="yes")))) {
				$this->creeNouveauItem("/cahier_texte_2/see_all.php",
					"Consultation of text books",
					"This menu allows to consult text books.");
		}

		if($this->statutUtilisateur=="professeur") {
			$this->creeNouveauItem("/documents/archives/index.php",
				"My archives of text books",
				"This menu allows to consult text books of the previous years.");
		}
		elseif(($this->statutUtilisateur=="cpe")||($this->statutUtilisateur=="scolarite")||($this->statutUtilisateur=="administrateur")) {
			$this->creeNouveauItem("/documents/archives/index.php",
				"Log books archives",
				"This menu allows to consult text books of the previous years.");
		}
	}

	$this->creeNouveauItem("/impression/impression_serie.php",
			"PDF lists printing",
			"This allows you to print in PDF of the lists with ".$this->gepiSettings['denomination_eleves'].", with the unit or in series. The appearance of the lists can be customized.");

	if(($this->statutUtilisateur=='scolarite')||(($this->statutUtilisateur=='professeur')
			AND ($this->test_prof_suivi != "0"))){
	  $this->creeNouveauItem("/saisie/impression_avis.php",
			  "PDF printing of the opinions of the staff meeting",
			  "This allows you to print in pdf the synthesis of the opinions of the
staff meeting.");
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
			  "Visualize all the averages of a class",
			  "Summary table of the averages of a class.");
	}

	if ($conditions_bulsimples) {
	  $this->creeNouveauItem("/prepa_conseil/index3.php",
			  "Visualize the simplified report card",
			  "Simplified report cards of a class.");
	}
	elseif(($this->statutUtilisateur=='professeur')&&(getSettingValue("GepiAccesReport cardSimplePP")=="yes")) {
	  $sql="SELECT 1=1 FROM j_eleves_professeurs
			WHERE professeur='".$this->loginUtilisateur."';";
	  $test_pp=mysql_num_rows(mysql_query($sql));
	  if($test_pp>0) {
		$this->creeNouveauItem("/prepa_conseil/index3.php",
				"Visualize the simplified carryforward card",
				"Simplified carryforward cards of has class.");
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
								WHERE (id_utilisateur = '".$this->loginUtilisateur."'
								AND indice_aid = '".$indice_aid."')");
	  $nb_result = mysql_num_rows($call_prof);
	  if ($nb_result != 0) {
		$nom_aid = @mysql_result($call_data, $i, "nom");
		$this->creeNouveauItem("/prepa_conseil/visu_aid.php?indice_aid=".$indice_aid,
				"Visualize appreciations ".$nom_aid,
				"This tool allows the visualization and the printing of the
appreciations of ".$this->gepiSettings['denomination_eleves']." for the ".$nom_aid.".");
	  }
	  $i++;
	}

	if(($this->statutUtilisateur=='professeur')&&(getSettingValue('GepiAccesGestElevesProfP')=='yes')) {
	  // Le professeur est-il professeur principal dans une classe au moins.
	  $sql="SELECT 1=1 FROM j_eleves_professeurs
			WHERE professeur='".$this->loginUtilisateur."';";
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
						  jgp.login='".$this->loginUtilisateur."' AND
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
				"Notanet/Fiches Patent "," This tool allows:<br/> - to curry out
calculations and the generation of CSV spins necessary for Notanet.
The operation also provides information to the tables necessary to
generate the patent cards.<br/> - to generate patent cards");
	  }
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Notanet/Fiches Brevet",'images/icons/document.png');
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
							WHERE jep.professeur='".$this->loginUtilisateur."' AND
									jep.id_classe=c.id
							ORDER BY c.classe";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Previous years",
					  "This tool allows to generate and consult data of previous years (simplified reports,...).");
			}
		  }

		}
		elseif($this->statutUtilisateur=='scolarite') {
		  $AAScolTout=getSettingValue('AAScolTout');
		  $AAScolResp=getSettingValue('AAScolResp');

		  if($AAScolTout=="yes"){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Previous years",
					"This tool allows to generate and consult data of previous years (simplified reports,...).");
		  }
		  elseif($AAScolResp=="yes"){
			$sql="SELECT 1=1 FROM j_scol_classes jsc
							WHERE jsc.login='".$this->loginUtilisateur."';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Previous years",
					  "This tool allows to generate and consult data of previous years (simplified reports,...).");
			}
		  }

		}
		elseif($this->statutUtilisateur=='cpe') {
		  $AACpeTout=getSettingValue('AACpeTout');
		  $AACpeResp=getSettingValue('AACpeResp');

		  if($AACpeTout=="yes"){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Previous years",
					"This tool allows to generate and consult data of previous years (simplified reports,...).");
		  }
		  elseif($AACpeResp=="yes"){
			$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$this->loginUtilisateur."'";
			$test=mysql_query($sql);

			if(mysql_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Previous years",
					  "This tool allows to generate and consult data of previous years (simplified,...).");
			}

		  }

		}
		elseif($this->statutUtilisateur=='responsable') {
		  $AAResponsable=getSettingValue('AAResponsable');

		  if($AAResponsable=="yes"){
			// Est-ce que le responsable est bien associé à un élève?
			$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e
				WHERE rp.pers_id=r.pers_id AND
					  r.ele_id=e.ele_id AND
					  rp.login='".$this->loginUtilisateur."'";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Previous years",
					  "This tool allows to generate and consult data of previous years (simplified,...).");
 			}
		  }

		}
		elseif($this->statutUtilisateur=='eleve') {
		  $AAEleve=getSettingValue('AAEleve');

		  if($AAEleve=="yes"){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Previous years",
					"This tool allows to generate and consult data of previous years (simplified,...).");
		  }

		}

	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Previous years",'images/icons/document.png');
	  return true;
	}
  }

  protected function messages(){
	$this->b=0;

	$this->creeNouveauItem("/messagerie/index.php",
			"Display panel",
			"This tool allows the management of the messages to post on the users home page.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Display panel",'images/icons/mail.png');
	  return true;
	}
  }

  protected function inscription(){
	$this->b=0;

	if (getSettingValue("active_inscription")=='y') {
	  $this->creeNouveauItem("/mod_inscription/inscription_config.php",
			  "Configuration of the enrolment module/visualization",
			  "Configuration of the various parameters of the module");

	  if (getSettingValue("active_inscription_utilisateurs")=='y'){
		$this->creeNouveauItem("/mod_inscription/inscription_index.php",
				"Access to the enrolment module/visualization",
				"Enrol or se cancel enrolment - Consult  enrolments");
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Enrolment",'images/icons/document.png');
	  return true;
	}
  }

  protected function discipline() {
		$this->b=0;

		/*
		if(($_SESSION['statut']=='eleve')) {
			if(getSettingValue('visuEleDisc')=='y') {
				$this->creeNouveauItem("/mod_discipline/visu_disc.php",
						"Discipline",
						"Incidents vous concernant.");
			}
		}
		elseif(($_SESSION['statut']=='responsable')) {
			if(getSettingValue('visuRespDisc')=='y') {
				$this->creeNouveauItem("/mod_discipline/visu_disc.php",
						"Discipline",
						"Incidents concernant les élèves/enfants dont vous êtes responsable.");
			}
		}
		else {
		*/
			$this->creeNouveauItem("/mod_discipline/index.php",
					"Discipline",
					"Announce incidents, take measures, sanctions.");
		//}

		if ($this->b>0){
			$this->creeNouveauTitre('accueil',"Discipline",'images/icons/document.png');
			return true;
		}

  }

  protected function modeleOpenOffice(){
	$this->b=0;

	if (getSettingValue("active_mod_ooo")=='y') {

	  $this->creeNouveauItem("/mod_ooo/index.php",
			"Open Office Model",
			"Manage Open Office models in Gepi and Use entry forms");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Open Office Models",'mod_ooo/images/ico_gene_ooo.png');
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

		if (function_exists($nom_fonction_autorisation))
		  // Si une fonction du type calcul_autorisation_nom_du_plugin existe, on calcule le droit de l'utilisateur à afficher cet item dans le menu
		  $result_autorisation = $nom_fonction_autorisation($this->loginUtilisateur,$menuItem->lien_item);
		else
		  $result_autorisation=true;

		if (($menuItem->user_statut == $this->statutUtilisateur) and ($result_autorisation)) {
		  $this->creeNouveauItemPlugin("/".$menuItem->lien_item,
				supprimer_numero($menuItem->titre_item),
				$menuItem->description_item);
		}

	  }

	  if ($this->b>0){
        $descriptionPlugin = $plugin->description;
		$this->creeNouveauTitre('accueil',"$descriptionPlugin",'images/icons/package.png');
	  }

	}

  }

  protected function geneseClasses(){
	$this->b=0;

	$this->creeNouveauItem("/mod_genese_classes/index.php",
			"Classes genesis",
			"To carry out the distribution of the students by classes according to  options,...?");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Classes genesis",'images/icons/document.png');
	  return true;
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
		$uri_el = retourneUri($this->loginUtilisateur, $this->test_https, 'cdt');
		$this->canal_rss=array("lien"=>$uri_el["uri"] ,
				  "texte"=>$uri_el["text"],
				  "mode"=>1 ,
				  "expli"=>"By clicking on the left cell,
				  you can recover your URI (if javascript is enabled on your browsers).");
	  }elseif(getSettingValue("rss_acces_ele") == 'csv'){
		$this->canal_rss=array("lien"=>"" , "texte"=>"", "mode"=>2, "expli"=>"");
	  }

	  $this->creeNouveauTitre('accueil',"Your RSS feed",'images/icons/rss.png');
	  return true;
	}

  }

  protected function statutAutre(){

	$this->b=0;

	if ($_SESSION["statut"] == 'autre') {
	  // On récupère la liste des fichiers à autoriser
	  require_once("utilisateurs/creer_statut_autorisation.php");
	  $nbre_a = count($autorise);

	  $a = 1;
	  while($a < $nbre_a){
		$numitem=$a;
		// On récupère le droit sur le fichier
		$sql_f = "SELECT autorisation FROM droits_speciaux
				  WHERE id_statut = '".$_SESSION["statut_special_id"]."'
				  AND nom_fichier = '".$autorise[$a][0]."'
				  ORDER BY id";
		$query_f = mysql_query($sql_f) OR trigger_error('Impossible de trouver le droit : '.mysql_error(), E_USER_WARNING);
		$nbre = mysql_num_rows($query_f);
		if ($nbre >= 1) {
		  $rep_f = mysql_result($query_f, 0, "autorisation");
		}else{
		  $rep_f = '';
		}

		if ($rep_f == 'V') {
		  $test = explode(".", $autorise[$a][0]); // On teste pour voir s'il y a un .php à la fin de la chaîne

		  if (!isset($test[1])) {
				// rien, la vérification se fait dans le module EdT
				// ou alors dans les autres modules spécifiés
		  }else{
			if($a == 4){
				// Dans le cas de la saisie des absences, il faut ajouter une variable pour le GET
				$var = '?type=A';
			}else{
				$var = '';
			}

			$this->creeNouveauItem($_SESSION["gepiPath"].$autorise[$a][0].$var,
					$menu_accueil[$a][0],
					$menu_accueil[$a][1]);
		  }

		}

		$a++;
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Navigation",'images/icons/document.png');
	  return true;
	}
  }

  protected function epreuvesBlanches(){
	$this->b=0;

	//insert into setting set name='active_mod_epreuve_blanche', value='y';
	if (getSettingValue("active_mod_epreuve_blanche")=='y') {
	  $this->creeNouveauItem("/mod_epreuve_blanche/index.php",
			  "Épreuves blanches",
			  "Organisation d'épreuves blanches,...");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"White tests",'images/icons/document.png');
	  return true;
	}
  }

  protected function examenBlanc(){
	$this->b=0;

//insert into setting set name='active_mod_epreuve_blanche', value='y';

	if (getSettingValue("active_mod_examen_blanc")=='y') {
		$acces_mod_examen_blanc="y";
		if($_SESSION['statut']=='professeur') {
			$acces_mod_examen_blanc="n";

			if((is_pp($_SESSION['login']))&&(getSettingValue('modExbPP')=='yes')) {
				$acces_mod_examen_blanc="y";
			}
		}

		if($acces_mod_examen_blanc=="y") {
			$this->creeNouveauItem("/mod_examen_blanc/index.php",
					"Practice tests",
					"Organization of practice tests,...");
		}
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Practice tests",'images/icons/document.png');
	  return true;
	}
  }

  protected function adminPostBac(){
	$this->b=0;

	if (getSettingValue("active_mod_apb")=='y') {
	  $this->creeNouveauItem("/mod_apb/index.php",
			  "Export APB",
			  "Export du fichier XML pour le système Admissions Post-Bac");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Export Post-Bac",'images/icons/document.png');
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
					WHERE indice_aid = '".$indice_aid."' and id_utilisateur='".$this->loginUtilisateur."'");
		$nb_result1 = mysql_num_rows($call_prof1);
		$call_prof2 = mysql_query("SELECT *
					FROM j_aidcateg_super_gestionnaires
					WHERE indice_aid = '".$indice_aid."' and id_utilisateur='".$this->loginUtilisateur."'");
		$nb_result2 = mysql_num_rows($call_prof2);

		if (($nb_result1 != 0) or ($nb_result2 != 0)) {
		  $nom_aid = @mysql_result($call_data, $i, "nom");
  		if ($nb_result2 != 0)
      		$this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "This tool allows you to manage groups (create, delete, modify).");
			else
      		$this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "This tool allows you to manage the membership of the students to the various groups.");
		}

		$i++;
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"AID Management",'images/icons/document.png');
	  return true;
	}
  }



  protected function cahierTexte_Visa(){
	$this->b=0;

	if (getSettingValue("GepiAccesCdtVisa")=='yes') {
	  $this->creeNouveauItem("/cahier_texte_admin/visa_ct.php",
			  "Visa des cahiers de textes",
			  "Voir et viser les cahiers de textes");
	}

	if ($this->b>0)
	  $this->creeNouveauTitre('accueil',"Visa CDT",'images/icons/document.png');

  }


  protected function verif_exist_ordre_menu($_item){
	if (!isset($this->ordre_menus[$_item]))
	  $this->ordre_menus[$_item] = max($this->ordre_menus)+1;
	  $this->a=$this->ordre_menus[$_item];
  }

  protected function chargeOrdreMenu($ordre_menus){
	//$this->ordre_menus=$ordre_menus;
	$sql="SHOW TABLES LIKE 'mn_ordre_accueil'";
	$resp = mysql_query($sql);

	if(mysql_num_rows($resp)>0) {
	  $sql2="SELECT bloc, num_menu
			FROM mn_ordre_accueil
			WHERE statut
			LIKE '$this->statutUtilisateur' " ;
	  $resp2 = mysql_query($sql2);
	  if (mysql_num_rows($resp2)>0){
		while($lig_log=mysql_fetch_object($resp2)) {
		  $this->ordre_menus[$lig_log->bloc]=$lig_log->num_menu;
		}
	  }else{
		$this->ordre_menus=$ordre_menus;
	  }
	}else{
	  $this->ordre_menus=$ordre_menus;
	}

  }

  private function chargeAutreNom($bloc){

	$sql1="SHOW TABLES LIKE 'mn_ordre_accueil'";
	$resp1 = mysql_query($sql1);

	if(mysql_num_rows($resp1)>0) {
	  $sql="SELECT nouveau_nom FROM mn_ordre_accueil
			WHERE bloc LIKE '$bloc'
			AND statut LIKE '$this->statutUtilisateur'
			AND nouveau_nom NOT LIKE ''
			;";
	  $resp=mysql_query($sql);

	  if (mysql_num_rows($resp)>0){
		$this->titre_Menu[$this->a]->texte=mysql_fetch_object($resp)->nouveau_nom;
	  }
	}

  }


}

?>
