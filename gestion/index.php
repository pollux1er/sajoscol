<?php
/*
 * $Id: index.php 7780 2011-08-16 08:58:17Z crob $
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
 
 
/* ---------Variables envoyées au gabarit
*
*	$tbs_menu
*				-> classe								classe CSS
*				-> image								icone du lien
*				-> texte								texte du titre du menu
*				-> entree								entrées du menu
*							-> lien						lien vers la page
*							-> titre   				texte du lien
*							-> expli					explications
*	$niveau_arbo									Niveau dans l'arborescence
*	$titre_page										Titre de la page
*	$tbs_last_connection					Vide, pour ne pas avoir d'erreur dans le bandeau
*	$tbs_retour										Lien retour arrière
*	$tbs_ariane										Fil d'arianne
*
*
*	Variables héritées de :
*
*	header_template.inc
*	header_barre_prof_template.inc
*	footer_template.inc.php
*
 */

$niveau_arbo = 1;

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

//**************** EN-TETE *****************
// Begin standart header
$titre_page = "General management";
$tbs_last_connection="";

// ====== Inclusion des balises head et du bandeau =====
//include_once("./../lib/header_template.inc");
include_once("../lib/header_template.inc");
// require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

/*
<!-- <p class='bold'><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a></p> -->
*/
	$tbs_retour="../accueil.php"; 
	$tbs_ariane[0]=array("titre" => "accueil" , "lien"=>"../accueil.php");
	
if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";

/*
<!-- 
<center>
-->
<!-- 
<table class='menu' summary='Menu sécurité'>
<tr>
	<th colspan='2'><img src='../images/icons/securite.png' alt='Sécurité' class='link'/> - Sécurité</th>
</tr>
<tr>
    <td width='200'><a href="gestion_connect.php">Gestion des connexions</a></td>
    <td>Affichage des connexions en cours, activation/désactivation des connexions pour le site, protection contre les attaques forces brutes, journal des connexions, changement de mot de passe obligatoire.
    </td>
</tr>
<tr>
    <td width='200'><a href="security_panel.php">Panneau de contrôle sécurité</a></td>
    <td>Visualiser les tentatives d'utilisation illégale de Gepi.
    </td>
</tr>
<tr>
    <td width='200'><a href="security_policy.php">Politique de sécurité</a></td>
    <td>Définir les seuils d'alerte et les actions à entreprendre dans le cas de tentatives d'intrusion ou d'accès illégal à des ressources.
    </td>
</tr>
<tr>
	<td width="200"><a href="../mod_serveur/test_serveur.php">Configuration serveur</a></td>
	<td>Voir la configuration du serveur php/Mysql pour v&eacute;rifier la compatibilit&eacute; avec Gepi.</td>
</tr>
</table>
 -->
*/
	$nummenu=0;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/securite.png' , 'texte'=>"Sécurité");
	$chemin = array();
  $titre = array();
  $expli = array();
  $ancre = array();
  
  $chemin = "gestion_connect.php";
  $titre = "Management of connections";
  $expli = "Display of connections in progress, activation/desactivation of connections for the site, protection against brutes forces attacks, Log of connections, obligatory change of password.";
  $ancre="gestion_connect";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "security_panel.php";
  $titre = "Control panel security";
  $expli = "Visualize the attempts of illegal use of Gepi.";
  $ancre="security_panel";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "security_policy.php";
  $titre = "Policy of Security";
  $expli = "Define the thresholds of alarm and the actions to be undertaken in the case of attempts of intrusion or of illegal access to resources.";
  $ancre="security_policy";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../mod_serveur/test_serveur.php";
  $titre = "Server Configuration ";
  $expli = "See the configuration of the Server php/Mysql to check compatibilié with Gepi.";
  $ancre="test_serveur";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

/*
<!--
<table class='menu' summary='Menu général'>
<tr>
	<th colspan='2'><img src='../images/icons/configure.png' alt='Configuration' class='link' /> - Général</th>
</tr>
<tr>
    <td width='200'><a href="param_gen.php">Configuration générale</a></td>
    <td>Permet de modifier des paramètres généraux (nom de l'établissement, adresse, ...).
    </td>
</tr>
<tr>
    <td width='200'><a href="droits_acces.php">Droits d'accès</a></td>
    <td>Modifier les droits d'accès à certaines fonctionnalités selon le statut de l'utilisateur.
    </td>
</tr>
<tr>
    <td width='200'><a href="options_connect.php">Options de connexions</a></td>
    <td>Gestion de la procédure automatisée de récupération de mot de passe, paramétrage du mode de connexion (autonome ou Single Sign-On), changement de mot de passe obligatoire, réglage de la durée de conservation des connexions, suppression de toutes les entrées du journal de connexion.
    </td>
</tr>
<tr>
    <td width='200'><a href="modify_impression.php">Gestion de la fiche "bienvenue"</a></td>
    <td>Permet de modifier la feuille d'information à imprimer pour chaque nouvel utilisateur créé.
    </td>
</tr>
<tr>
    <td width='200'><a href="config_prefs.php">Paramétrage de l'interface <?php echo $gepiSettings['denomination_professeur']; ?></a></td>
    <td>Paramétrage des items de l'interface simplifiée pour certaines pages. Gestion du menu en barre horizontale.</td>
</tr>
<tr>
    <td width='200'><a href="param_couleurs.php">Paramétrage des couleurs</a></td>
    <td>Paramétrage des couleurs de fond d'écran et du dégradé d'entête.</td>
</tr>
</table>
 -->
*/ 
 
	$nummenu=1;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/configure.png' , 'texte'=>"Général");
	$chemin = array();
  $titre = array();
  $expli = array();
  
  $chemin = "param_gen.php";
  $titre = "General configuration";
  $expli = "Allows to modify general parameters (name of the school, addresses, ...).";
  $ancre="param_gen";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "droits_acces.php";
  $titre = "Access rights";
  $expli = "Modify the rights of access to certain functionalities according to the statute of the user.";
  $ancre="droits_acces";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "options_connect.php";
  $titre = "Options of connections";
  $expli = "Management of the automated procedure of recovery of password, parameter setting of the mode of connection (autonomous or Single Sign-On), obligatory changement of  password, adjustment of the life of connections, suppresson of all the entries of the Log of connection.";
  $ancre="options_connect";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "modify_impression.php";
  $titre = "Management of the card \"welcome\"";
  $expli = "Allows to modify the sheet of information to be printed for each new user created.";
  $ancre="modify_impression";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "config_prefs.php";
  $titre = "Parameter setting of the interface ".$gepiSettings['denomination_professeur'];
  $expli = "Parameter setting of the items of the simplified interface for certain pages. Management of the menu in horizontal bar.";
  $ancre="config_prefs";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

  $chemin = "param_couleurs.php";
  $titre = "Parameter setting of the colors";
  $expli = "Paramétrage des couleurs de fond d'écran et du dégradé d'entête.";
  $ancre="param_couleurs";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

  $chemin = "param_ordre_item.php";
  $titre = "Parameter setting of the order of the menus";
  $expli = "Parameter setting of the order of the items in the menus";
  $ancre="param_ordre_item";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

/*
<!--
<table class='menu' summary='Menu gestion des BDD'>
<tr>
	<th colspan='2'><img src='../images/icons/database.png' alt='Gestion bases de données' class='link' /> - Gestion des bases de données </th>
</tr>
<tr>
    <td width='200'><a href="accueil_sauve.php">Sauvegardes et restauration</a></td>
    <td>Save la base GEPI sous la forme d'un fichier au format "mysql".<br />
    Restaurer des données dans la base Mysql de GEPI à partir d'un fichier.
    </td>
</tr>
<tr>
    <td width='200'><a href="../utilitaires/maj.php">Mise à jour de la base</a></td>
    <td>Permet d'effectuer une mise à jour de la base MySql après un changement de version  de GEPI.
    </td>
</tr>
<tr>
    <td width='200'><a href="../utilitaires/clean_tables.php">Nettoyage des tables</a></td>
    <td>Procéder à un nettoyage des tables de la base MySql de GEPI (suppression de certains doublons et/ou lignes obsolètes ou orphelines).
    </td>
</tr>
<tr>
    <td width='200'><a href="efface_base.php">Effacer la base</a></td>
    <td>Permet de réinitialiser les bases en effaçant toutes les données <?php echo $gepiSettings['denomination_eleves']; ?> de la base.
    </td>
</tr>
<tr>
    <td width='200'><a href="efface_photos.php">Effacer les photos</a></td>
    <td>Permet d'effacer les photos des <?php echo $gepiSettings['denomination_eleves']; ?> qui ne sont plus dans la base.</td>
</tr>
<tr>
    <td width='200'><a href="gestion_temp_dir.php">Gestion des dossiers temporaires</a></td>
    <td>Permet de contrôler le volume occupé par les dossiers temporaires (<i>utilisés notamment pour générer les fichiers tableur OpenOffice (ODS), lorsque la fonction est activée dans le module carnet de notes</i>), de supprimer ces dossiers,...</td>
</tr>

</table>
 -->
*/
 
	$nummenu=2;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/database.png' , 'texte'=>"Gestion des bases de données");
	$chemin = array();
  $titre = array();
  $expli = array();  

  $chemin = "accueil_sauve.php";
  $titre = "Backup and restoration";
  $expli = "Backup GEPI base in a file to the format \"mysql\".<br />
    Restore data in the base Mysql of GEPI from a file.";
  $ancre="accueil_sauve";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../utilitaires/maj.php";
  $titre = "Update of the base";
  $expli = "Make an update of the base MySql after a change of version of GEPI.";
  $ancre="maj";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../utilitaires/clean_tables.php";
  $titre = "Cleaning of the tables ";
  $expli = "Make a cleaning of the tables of the MySql base of GEPI (suppression of certain doubled data and/or obsolete or orphan lines).";
  $ancre="clean_tables";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "efface_base.php";
  $titre = "Erase the base";
  $expli = "Allows to re-initialize the bases by erasing all the data ".$gepiSettings['denomination_eleves']." of the base.";
  $ancre="efface_base";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

  if ($multisite != 'y') {
	$chemin = "efface_photos.php";
	$titre = "Erase the photos";
	$expli = "Allows to erase the photos of ".$gepiSettings['denomination_eleves']." who are not any more in the base.";
	$ancre="efface_photos";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  }
  
  $chemin = "gestion_temp_dir.php";
  $titre = "Management of the temporary folders";
  $expli = "Allows to control the volume occupied by the temporary folders (<em>used in particular to generate the files OpenOffice spreadsheet (ODS), when this function is activated in the module report card</em>), to remove these folders,...";
  $ancre="gestion_temp_dir";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "gestion_base_test.php";
  $titre = "Management of the data of test";
  $expli = "Allows to insert data test in the base. Not to use on a base in production.";
  $ancre="gestion_base_test";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

/*
<!--
<table class='menu' summary='Menu initialisation'>
<tr>
<th colspan='2'><img src='../images/icons/package.png' alt='Initialisation' class='link' /> - Outils d'initialisation</th>
</tr>
<?php

if (LDAPServer::is_setup()) {
    ?>
<tr>
    <td width='200'><a href="../init_scribe/index.php">Initialisation à partir de l'annuaire LDAP du serveur Eole Scribe</a></td>
    <td>Permet d'importer les données <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, matières directement depuis le serveur LDAP de Scribe.
    </td>
</tr>
<tr>
    <td width='200'><a href="../init_lcs/index.php">Initialisation à partir de l'annuaire LDAP du serveur LCS</a></td>
    <td>Permet d'importer les données <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, matières directement depuis le serveur LDAP de LCS.
    </td>
</tr>
<?php
}
?>
<tr>
    <td width='200'><a href="../init_csv/index.php">Initialisation des données à partir de fichiers CSV</a></td>
    <td>Permet d'importer les données <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, matières depuis des fichiers CSV, par exemple des exports depuis Sconet.
    </td>
</tr>
<tr>
    <td width='200'><a href="../init_xml2/index.php">Initialisation des données à partir de fichiers XML</a></td>
    <td>Permet d'importer les données <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, matières depuis les exports XML de Sconet/STS.<br />
	<b>Nouvelle procédure:</b> Plus simple et moins gourmande en ressources que l'ancienne méthode ci-dessous.
    </td>
</tr>
<tr>
    <td width='200'><a href="../init_xml/index.php">Initialisation des données à partir de fichiers XML</a></td>
    <td>Permet d'importer les données <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, matières depuis les exports XML de Sconet/STS.<br />
	<i>Les XML sont traités pour générer des fichiers CSV qui sont ensuite réclamés dans les différentes étapes de l'initialisation.</i>
    </td>
</tr>
 -->
<!--tr>
    <td width='200'><a href="../init_dbf_sts/index.php">Initialisation des données à partir de fichiers DBF et XML</a> (OBSOLETE)</td>
    <td>Permet d'importer les données <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, matières depuis deux fichiers DBF et l'export XML de STS.<br />
	<span style='color:red; '>Cette solution ne sera plus maintenue dans la future version 1.5.2 de Gepi.</span>
    </td>
</tr>
<tr>
    <td width='200'><a href="../initialisation/index.php">Initialisation des données à partir des fichiers GEP</a> (OBSOLETE)</td>
    <td>Permet d'importer les données <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, matières depuis les fichiers GEP. Cette procédure est désormais obsolète avec la généralisation de Sconet.<br />
	<span style='color:red; '>Cette solution ne sera plus maintenue dans la future version 1.5.2 de Gepi.</span>
    </td>
</tr-->
<!--
</table>
 -->
*/

	$nummenu=3;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/package.png' , 'texte'=>"Initialization Tools");
	$chemin = array();
  $titre = array();
  $expli = array();

	$chemin="changement_d_annee.php";
	$titre = "Change of year";
	$expli = "Allows to carry out the operations of archiving at the end of the year and operations preceding the initialization of the new year.";
	$ancre="chgt_annee";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

if (LDAPServer::is_setup()) {	
	
	$chemin="../init_scribe_ng/index.php";
	$titre = "Initialization starting from directory LDAP of the server Eole Scribe NG";
	$expli = "Allows to import the data ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", courses directly from the LDAP Server of Scribe NG.";
	$ancre="init_scribe_ng";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

	$chemin="../init_lcs/index.php";
	$titre = "Initialization starting from LDAP directory of server LCS";
	$expli = "Allows to import the data ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", courses directly from the LDAP Server of Scribe  LCS.";
	$ancre="init_lcs";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
    
}
	
	$chemin = "../init_csv/index.php";
	$titre = "Initialization of the data starting from CSV files ";
	$expli = "Permet d'importer les données ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", courses from files CSV, for example of exports from Sconet.";
	$ancre="init_csv";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../init_xml2/index.php";
  $titre = "Initialization of the data starting from XML files ";
  $expli = "Allows to import the data ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", courses from XML exports of Sconet/STS.<br />
	<strong>New procedure:</strong> Simpler and less greedy in resources than the old method below.";
	$ancre="init_xml2";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../init_xml/index.php";
  $titre = "Initialization of the data starting from files XML converted into CSV";
  $expli = "Allows to import the data ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", courses from XML exports of Sconet/STS.<br />
	<em>The XML are treated to generate CSV files who are then claimed in the various stages of initialization.</em>";
	$ancre="init_xml";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
	
	
/*
<!--
</center>
 -->
 */

	$tbs_microtime	="";
	$tbs_pmv="";
	//require_once ("./../lib/footer_template.inc.php");
	require_once ("../lib/footer_template.inc.php");

	
//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();

//include('./../templates/origine/gestion_generale_template.php');
include('../templates/origine/gestion_generale_template.php');

?>
