<?php
/* $Id: class_page_accueil_autre.php 8716 2011-12-06 11:40:11Z crob $
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

class class_page_accueil_autre {


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
  function __construct($gepiSettings, $niveau_arbo,$ordre_menus) {


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

	$this->statutUtilisateur = "autre";
	$this->gepiSettings=$gepiSettings;
	$this->loginUtilisateur=$_SESSION['login'];

	$this->chargeOrdreMenu($ordre_menus);

/***** Outils de gestion des absences vie scolaire *****/
	$this->verif_exist_ordre_menu('bloc_absences_vie_scol');
	if ($this->absences_vie_scol())
	$this->chargeAutreNom('bloc_absences_vie_scol');

/***** Cahier de texte CPE ***********/
	$this->verif_exist_ordre_menu('bloc_saisie');
	if ($this->cahierTexte()){
	  $this->chargeAutreNom('bloc_saisie');
	}

/***** Outils de relevé de notes *****/
	$this->verif_exist_ordre_menu('bloc_releve_notes');
	if ($this->releve_notes())
	$this->chargeAutreNom('bloc_releve_notes');

/***** Emploi du temps *****/
	$this->verif_exist_ordre_menu('bloc_emploi_du_temps');
	if ($this->emploiDuTemps())
	$this->chargeAutreNom('bloc_emploi_du_temps');

/***** gestion des trombinoscopes : module de Christian Chapel ***********/
	$this->verif_exist_ordre_menu('bloc_trombinoscope');
	if ($this->trombinoscope())
	$this->chargeAutreNom('bloc_trombinoscope');

/***** Visualisation / Impression *****/
	$this->verif_exist_ordre_menu('bloc_visulation_impression');
	if ($this->impression())
	$this->chargeAutreNom('bloc_visulation_impression');

/***** Outils de relevé ECTS *****/
	$this->verif_exist_ordre_menu('bloc_releve_ects');
	if ($this->releve_ECTS())
	$this->chargeAutreNom('bloc_releve_ects');

/***** Outils complémentaires de gestion des AID *****/
	$this->verif_exist_ordre_menu('bloc_outil_comp_gestion_aid');
	if ($this->gestionAID())
	$this->chargeAutreNom('bloc_outil_comp_gestion_aid');

/***** Outils de gestion des Report cards scolaires *****/
	$this->verif_exist_ordre_menu('bloc_gestion_bulletins_scolaires');
	if ($this->bulletins())
	$this->chargeAutreNom('bloc_gestion_bulletins_scolaires');

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

/***** Module plugins : affichage des menus des plugins en fonction des droits *****/
	$this->verif_exist_ordre_menu('');
	$this->plugins();

/***** Module Genese des classes *****/
	$this->verif_exist_ordre_menu('bloc_Genese_classes');
	if ($this->geneseClasses())
	$this->chargeAutreNom('bloc_Genese_classes');

/***** Module Epreuves blanches *****/
	$this->verif_exist_ordre_menu('bloc_epreuve_blanche');
	if ($this->epreuvesBlanches())
	$this->chargeAutreNom('bloc_epreuve_blanche');

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

  private function itemPermis($chemin){
	$sql="SELECT ds.autorisation FROM `droits_speciaux` ds,  `droits_utilisateurs` du
				WHERE (ds.nom_fichier='".$chemin."'
				  AND ds.id_statut=du.id_statut
				  AND du.login_user='".$this->loginUtilisateur."');" ;
	$result=mysql_query($sql);
	if (!$result) {
	  return TRUE;
	} else {
	  $row = mysql_fetch_row($result) ;
	  if ($row[0]=='V' || $row[0]=='v'){
		return TRUE;
	  } else {
		return FALSE;
	  }
	}
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
	if ($this->itemPermis($nouveauItem->chemin))
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

  protected function verif_exist_ordre_menu($_item){
	if (!isset($this->ordre_menus[$_item]))
	  $this->ordre_menus[$_item] = max($this->ordre_menus)+1;
	  $this->a=$this->ordre_menus[$_item];
  }

  private function chargeAutreNom($bloc){
	$sql1="SHOW TABLES LIKE 'mn_ordre_accueil'";
	$resp1 = mysql_query($sql1);
	if(mysql_num_rows($resp1)>0) {
	  $sql="SELECT nouveau_nom FROM mn_ordre_accueil
			WHERE bloc LIKE '$bloc'
			AND statut LIKE 'autre'
			AND nouveau_nom NOT LIKE ''
			;";
	  $resp=mysql_query($sql);
	  if (mysql_num_rows($resp)>0){
		$this->titre_Menu[$this->a]->texte=mysql_fetch_object($resp)->nouveau_nom;
	  }
	}
  }
  
  protected function absences_vie_scol() {
	if (getSettingValue("active_module_absence")) {
	  $this->b=0;
	  $nouveauItem = new itemGeneral();
	  if (getSettingValue("active_module_absence")=='y' ) {
	  $this->creeNouveauItem('/mod_absences/gestion/gestion_absences.php',
			  "Absences Management, exemptions, lateness and infirmaries",
			  "This tool allows you to manage absences, exemptions, lateness and others ".$this->gepiSettings['denomination_eleves'].".");
	  $this->creeNouveauItem('/mod_absences/gestion/voir_absences_viescolaire.php',
			  "Visualize absences",
			  "You can visualize crenel by crenel the entry of absences.");
		$this->creeNouveauItem("/mod_absences/professeurs/prof_ajout_abs.php",
				"Absences Management",
				"This tool allows you to manage students absences");
	  } else if (getSettingValue("active_module_absence")=='2' ) {
		$this->creeNouveauItem("/mod_abs2/index.php",
				"Absences Management",
				"This tool allows you to manage students absences");
	  }
	  if ($this->b>0){
		$this->creeNouveauTitre('accueil',"Management of lateness and absences",'images/icons/absences.png');
		return true;
	  }
    }
  }

  private function cahierTexte(){
	$this->b=0;
	if (getSettingValue("active_cahiers_texte")=='y') {
	  $this->creeNouveauItem("/cahier_texte/see_all.php",
			  "Diary",
			  "This tool allow to consult report of meeting and assessments to to for the teachings of all thes ".$this->gepiSettings['denomination_eleves']);
	  $this->creeNouveauItem("/cahier_texte_admin/visa_ct.php",
			  "Signature of diraires",
			  "Allow to sign diaries" );
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Diary",'images/icons/document.png');
	  return true;
	}
  }

  private function releve_notes(){
	$this->b=0;
	if (getSettingValue("active_carnets_notes")=='y') {
	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes_2.php",
			  "Visualization and printing of  statements of notes",
			  "This tool allows you to visualize on screen and print statements of notes,
				".$this->gepiSettings['denomination_eleve']." par ".$this->gepiSettings['denomination_eleve'].",
				  classe par classe.");

	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes.php",
			  "Visualazation and printing of statements of notes",
			  "This tool allow you to visualize on screen and print statements of notes,
				".$this->gepiSettings['denomination_eleve']." par ".$this->gepiSettings['denomination_eleve'].",
				  classe par classe.");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Statements of notes",'images/icons/document.png');
	  return true;
	}
  }
 
  private function emploiDuTemps(){
	$this->b=0;
    $this->creeNouveauItem("/edt_organisation/index_edt.php",
			"Timetable",
			"This tool allows the consultation/management of the timetable.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Timetable",'images/icons/document.png');
	  return true;
	}
  }  private function trombinoscope(){
	//On vérifie si le module est activé

	$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");
	$active_module_trombino_pers=getSettingValue("active_module_trombino_pers");

	$this->b=0;


	if (($active_module_trombinoscopes=='y')
			||($active_module_trombino_pers=='y')) {

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
				  "This tool enables you to visualize which ".$this->gepiSettings['denomination_eleves']." have the right to upload/update their photo.");
		}
		$i++;
	  }
	}

	  if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Trombinoscope",'images/icons/trombinoscope.png');
		return true;
	  }
  }









  private function impression(){
	$this->b=0;

	$this->creeNouveauItem("/groupes/visu_profs_class.php",
			"Visualization of the teaching staffs",
			"This enables you to know all the ".$this->gepiSettings['denomination_professeurs']." of the classes in which you intervene, as well as the compositions of the groups concerned.");
/*
	$this->creeNouveauItem("/eleves/liste_eleves.php",
			"Visualisation des équipes pédagogiques",
			"Ceci vous permet de connaître tous les ".$this->gepiSettings['denomination_professeurs']." des classes dans lesquelles vous intervenez, ainsi que les compositions des groupes concernés.");
*/
	$this->creeNouveauItem("/eleves/visu_eleve.php",
			"Consultation of a ".$this->gepiSettings['denomination_eleve'],
			"This menu enables you to consult in the same page information
concerning one ".$this->gepiSettings['denomination_eleve']." (followed lessons, report cards, statement of notes, ".$this->gepiSettings['denomination_responsables'].",...). Some elements can be accessible only for some categories of
visitors.");

	$this->creeNouveauItem("/impression/impression_serie.php",
			"Printing pdf lists",
			"This enables you to print lists with ".$this->gepiSettings['denomination_eleves'].". The appearance of the lists can be configured.");

	  $this->creeNouveauItem("/groupes/mes_listes.php",
			  "To export my lists",
			  "This menu makes it possible to download its lists with all the ".$this->gepiSettings['denomination_eleves']." on CSV format with the field CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS.");

	$this->creeNouveauItem("/visualisation/index.php",
			"Graphic tools for visualization",
			"Graphic visualization of the results of ".$this->gepiSettings['denomination_eleves']." or classess, by merging the data in multiple manners.");
	$this->creeNouveauItem("/prepa_conseil/index1.php",
			"To visualize my averages and appreciations of the report cards",
			"Summary table of your averages and/or appreciations appearing in the
report cards with display of useful statistics for the filling of the
school reports.");
	$this->creeNouveauItem("/prepa_conseil/index1.php",
				"To visualize the averages and appreciations of the bulletins",
				"Summary table of your averages and/or appreciations appearing in the
report cards with display of useful statistics for the filling of the
school reports.");

	$this->creeNouveauItem("/prepa_conseil/index2.php",
			"Visualize all the averages of a class",
			"Summary table of the averages of a class.");

	$this->creeNouveauItem("/prepa_conseil/index3.php",
			"To visualize the simplified report cards",
			"Simplified report cards of a class.");
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
				"This tool allows visualization and printing of appreciations of ".$this->gepiSettings['denomination_eleves']." for the ".$nom_aid.".");
	  }
	  $i++;
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Visualization - Printing",'images/icons/print.png');
	  return true;
	}
  }
  
  protected function releve_ECTS(){
	$this->b=0;

	$chemin = array();
	$this->creeNouveauItem("/mod_ects/edition.php",
			  "Generation of ECTS documents",
			  "This tool allows you to generate ECTS documentsCet outil vous permet de générer les documents ECTS (statement, certificate, appendix)
				for the classes concerned.");

	  $this->creeNouveauItem("/mod_ects/recapitulatif.php",
			  "Visualize all ECTS",
			  "To visualize the summary tables by class of all ECTS credits.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"ECTS Documents",'images/icons/releve.png');
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
				"Projects cards of the former years ",
				"Access to the projects cards of the former years");
	  }
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',
			  "Tools of visualization and edition of the projects cards",
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

	$this->creeNouveauItem("/bulletin/verif_bulletins.php",
			  "Verification tool",
			  "Allows to check if all the headings of the report cards are filled.");
	$this->creeNouveauItem("/bulletin/verrouillage.php",
			  "Locking/Unlocking of the periods",
			  "Allows to lock or unlock a period for one or more classes.");
	$this->creeNouveauItem("/classes/acces_appreciations.php",
			  "Access of ".$this->gepiSettings['denomination_eleves']." and ".$this->gepiSettings['denomination_responsables']." to appreciations",
			  "Allows to define when the accounts ".$this->gepiSettings['denomination_eleves']." and ".$this->gepiSettings['denomination_responsables']."
			  (if they exist) can access to appreciations of ".$this->gepiSettings['denomination_professeurs']."
				on the report card and opinion of staff meeting.");
	$this->creeNouveauItem("/bulletin/param_bull.php",
			  "Parameters of reports printing",
			  "Allows to modify the parameters of page-setting and printing of the
report cards.");
	$this->creeNouveauItem("/responsables/index.php",
			  "Management of the cards ".$this->gepiSettings['denomination_responsables'],
			  "This tool allows you edit/delete/add cards
			  of ".$this->gepiSettings['denomination_responsables']." des ".$this->gepiSettings['denomination_eleves'].".");
	$this->creeNouveauItem("/eleves/index.php",
			  "Management of the cards ".$this->gepiSettings['denomination_eleves'],
			  "This tool enables you to modify/delete/add cards ".$this->gepiSettings['denomination_eleves'].".");
	$this->creeNouveauItem("/bulletin/bull_index.php",
			  "Visualization and printing of the report cards",
			  "This tool enables you to visualize with the screen and print the report cards, class by class.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Report cards",'images/icons/bulletin_16.png');
	  return true;
	}
  }

  private function notanet(){
	$this->b=0;

	if ((getSettingValue("active_notanet")=='y')) {
	  $this->creeNouveauItem("/mod_notanet/index.php",
				"Notanet/Fiches Brevet",
				"This tool allows :<br />
				- to carry out calculations and the generation of CSV file necessary for Notanet.
				The operation also provides information to the tables necessary to generate the brevet cards.<br/>
				- to generate brevet cards");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Notanet/Brevet Cards",'images/icons/document.png');
	  return true;
	}
  }

  private function anneeAnterieure(){
	$this->b=0;

	if (getSettingValue("active_annees_anterieures")=='y') {
		$this->creeNouveauItem("/mod_annees_anterieures/index.php",
				"Former years",
				"This tool allows to manage and consult data of previous years (simplified report cards,...).");

		$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
				"Former years",
				"This tool allows to manage and consult data of previous years (simplified report cards,...).");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Former years",'images/icons/document.png');
	  return true;
	}
  }

  protected function messages(){
	$this->b=0;
	$this->creeNouveauItem("/messagerie/index.php",
			"Display panel",
			"This tool allows the management of the messages to be posted on the users home page.");
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Display panel",'images/icons/mail.png');
	  return true;
	}
  }

  protected function inscription(){
	$this->b=0;

	if (getSettingValue("active_inscription")=='y') {
	  $this->creeNouveauItem("/mod_inscription/inscription_config.php",
			  "Configuration of enrollment/visualization module",
			  "Configuration of the various parameters of the module");

	  if (getSettingValue("active_inscription_utilisateurs")=='y'){
		$this->creeNouveauItem("/mod_inscription/inscription_index.php",
				"Accès to the enrollment/visualization module",
				"To enroll or cancel enrollment - Consulte enrollments");
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Enrollment",'images/icons/document.png');
	  return true;
	}
  }

  protected function discipline(){
	$this->b=0;

	$this->creeNouveauItem("/mod_discipline/index.php",
			"Discipline",
			"To announce incidents, to take measures, sanctions.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Discipline",'images/icons/document.png');
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
			"Generation of the classes",
			"To carry out the distribution of the pupils by classes according to the options?,...");
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Generation of classes",'images/icons/document.png');
	  return true;
	}
  }

  protected function epreuvesBlanches(){
	$this->b=0;

	//insert into setting set name='active_mod_epreuve_blanche', value='y';
	if (getSettingValue("active_mod_epreuve_blanche")=='y') {
	  $this->creeNouveauItem("/mod_epreuve_blanche/index.php",
			  "White tests",
			  "Organization of white tests,...");
	}
//insert into setting set name='active_mod_epreuve_blanche', value='y';
	if (getSettingValue("active_mod_examen_blanc")=='y') {
	  $this->creeNouveauItem("/mod_examen_blanc/index.php",
			  "Practice tests",
			  "Organization of practice tests,...");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"White tests",'images/icons/document.png');
	  return true;
	}
  }

  protected function adminPostBac(){
	$this->b=0;

	if (getSettingValue("active_mod_apb")=='y') {
	  $this->creeNouveauItem("/mod_apb/index.php",
			  "APB Export",
			  "Export of XML file for the Post-GCE Admission systemc");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Post GCE Export",'images/icons/document.png');
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
		$call_prof = mysql_query("SELECT *
					FROM j_aid_utilisateurs_gest
					WHERE (id_utilisateur = '" . $this->loginUtilisateur . "'
					AND indice_aid = '$indice_aid')");
		$nb_result = mysql_num_rows($call_prof);

		if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
		  $nom_aid = @mysql_result($call_data, $i, "nom");
		  $this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "This tool allows you to manage the membership of the pupils to the,various groups.");
		}

		$i++;
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Management of AID",'images/icons/document.png');
	  return true;
	}
  }




  
  
  
  
  
  

}
?>
