<?php
/*
* @version: $Id: archivage_cdt.php 7926 2011-08-23 16:24:17Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisations.inc.php");
require_once("../lib/transform_functions.php");
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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/archivage_cdt.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/archivage_cdt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Archivage des CDT',
statut='';";
$insert=mysql_query($sql);updateOnline($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$confirmer_ecrasement=isset($_POST['confirmer_ecrasement']) ? $_POST['confirmer_ecrasement'] : (isset($_GET['confirmer_ecrasement']) ? $_GET['confirmer_ecrasement'] : 'n');

include('cdt_lib.php');

//**************** EN-TETE *****************
$titre_page = "text book - Archiving";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************

//debug_var();

//===================================
// Permettre de choisir l'ordre dans lequel exporter?
$current_ordre='ASC';

$dossier_etab=get_dossier_etab_cdt_archives();
//===================================

if(isset($_GET['chgt_annee'])) {$_SESSION['chgt_annee']="y";}

echo "<p class='bold'><a href='";
if(isset($_SESSION['chgt_annee'])) {
	echo "../gestion/changement_d_annee.php";
}
else {
	echo "../cahier_texte_admin/index.php";
}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

//echo "<br />\$dossier_etab=$dossier_etab<br />";

if($dossier_etab=="") {
	echo "</p>\n";

	echo "<p style='color:red'>The file of the archiving of the school could not be identified.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

// Création d'un espace entre le bandeau et le reste 
//echo "<p></p>\n";

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	echo "</p>\n";

	echo "<p class='grand centre_texte'>The text book is not accessible for the moment.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

echo " | <a href='../documents/archives/index.php'>Years archived</a>";

echo "</p>\n";

if(!isset($step)) {

	// A FAIRE: Si multisite, ne pas permettre d'aller plus loin si le RNE n'est pas renseigné? ou utiliser le RNE récupéré de... la session?

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo "<p>You are going to archive the text book.</p>\n";

	$annee=preg_replace('/[^0-9a-zA-Z_-]/','_',getSettingValue('gepiYear'));
	echo "<p>Year&nbsp;: <input type='text' name='annee' value='$annee' /><br />(<i>the authorized characters are the numbers (from 0 to 9), not accentuated letters and indents (- and _)</i>)</p>\n";
	echo "<p>\n";
	echo "<input type='radio' id='mode_transfert' name='mode' value='transfert' /><label for='mode_transfert'> archive the text books and <b>delete the joined documents</b> after transfer</label><br />\n";
	echo "<input type='radio' id='mode_copie' name='mode' value='copie' checked /><label for='mode_copie'>archive the text books, <b>without removing the joined documents</b> after archiving</label>.</p>\n";
	echo add_token_field();
	echo "<input type='hidden' name='step' value='1' />\n";
	echo "<p><input type='submit' value='Validate' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	echo "<p><em>NOTES&nbsp;:</em></p>\n";
	echo "<ul>\n";
	echo "<li><p>The archiving procedure is normally used at the end of the year.</p></li>\n";
	echo "<li><p>During the archiving, the text book are traversed to set up a tree structure copies of tree structure of the text books.<br />The procedure does not empty the tables of the text books.</p></li>\n";
	echo "<li><p>If you want to test the archiving procedure, you can, at any moment of the year, archive without transfer of the joined documents.<br />A tree structure copy will be installation.<br />You will be able to consult it... and to remove it if you wish it without impact on the text books in use.<br />On the other hand, if you check Transfert, the documents joined to the text books will be moved.<br />A professor who would consult his text book of the current year, would find his reports, but the joined documents would not be available any more.</p></li>\n";
	echo "<li><p>At the end of the year, it is recommended to archive with transfer of the documents to not leave slags for the lesson of the following years (<em>and to avoid encumbering the tree structure of the server with useless file </em>).</p><p>Once the archiving of end of the year carried out, you will be able to empty the tables of the text book in <a href='../utilitaires/clean_tables.php'>General management/Cleaning of tables</a><br />(<em>this ' manual' cleaning  of the tables is not essential; it is carried out automatically at the time of the initialization of the year if you do not make an initialization all with the hand</em>)</p></li>\n";
	echo "<li><p>In the archive of CDT, the professors will be able to consult only their own text books.<br />Accounts of statute 'administrator', 'scolarite' will have access to all the archive of text book .<br />The other statutes will have no access there.</p></li>\n";
	echo "</ul>\n";
}
else {
	check_token();

	$annee=isset($_POST['annee']) ? $_POST['annee'] : (isset($_GET['annee']) ? $_GET['annee'] : "");

	$annee_ini=$annee;
	$annee=preg_replace('/[^0-9a-zA-Z_-]/','',$annee);

	if($annee=="") {
		echo "<p style='color:red'>The name of year provided '$annee_ini' is not valid.</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Stocker date archivage strftime("%Y%m%d_%H%M%S")

	// Sécurité:
	if(($dossier_etab=='index.php')||($dossier_etab=='entete.php')) {
		echo "<p style='color:red'>The name of shool file '$dossier_etab' is not valid.</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."'>Return</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$dossier_annee="../documents/archives/".$dossier_etab."/cahier_texte_".$annee;
	$dossier_cdt=$dossier_annee."/cdt";
	$dossier_documents=$dossier_annee."/documents";
	$dossier_css=$dossier_annee."/css";

	if($step==1) {
		// Remplissage d'une table temporaire avec la liste des groupes.
		$sql="TRUNCATE TABLE tempo2;";
		$res=mysql_query($sql);
		if(!$res) {
			echo "<p style='color:red'>ABANDON&nbsp;: There was a problem during the cleaning of the table 'tempo2'.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		//$sql="INSERT INTO tempo2 SELECT id,name FROM groupes;";
		// On ne retient que les groupes associés à des classes... les autres sont des scories qui devraient être supprimées par un Nettoyage de la base
		$sql="INSERT INTO tempo2 SELECT id,name FROM groupes WHERE id IN (SELECT DISTINCT id_groupe FROM j_groupes_classes);";
		$res=mysql_query($sql);updateOnline($sql);
		if(!$res) {
			echo "<p style='color:red'>ABANDON&nbsp;: There was a problem during the insertion of the list of the groups in the table 'tempo2'.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		//$sql="CREATE TABLE tempo3 (id_classe int(11) NOT NULL default '0', name varchar(60) NOT NULL default '');";
		$sql="CREATE TABLE IF NOT EXISTS tempo3_cdt (id_classe int(11) NOT NULL default '0', classe varchar(255) NOT NULL default '', matiere varchar(255) NOT NULL default '', enseignement varchar(255) NOT NULL default '', id_groupe int(11) NOT NULL default '0', fichier varchar(255) NOT NULL default '');";
		$res=mysql_query($sql);updateOnline($sql);
		if(!$res) {
			echo "<p style='color:red'>ABANDON&nbsp;: Error during the creation of the temporary table 'tempo3_cdt'.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$sql="TRUNCATE TABLE tempo3_cdt;";
		$res=mysql_query($sql);
		if(!$res) {
			echo "<p style='color:red'>ABANDON&nbsp;: There was a problem during the cleaning of the table 'tempo3_cdt'.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		if(!file_exists("../documents/archives/")) {
			$res=mkdir("../documents/archives/");
			if(!$res) {
				echo "<p style='color:red;'>Error during the preparation of the tree structure ../documents/archives/</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}

		if(!file_exists("../documents/archives/".$dossier_etab)) {
			//$res=mkdir("../documents/archives/".$dossier_etab);
			//$res=creer_rep_docs_joints("../documents/archives/", $dossier_etab, "../../..");
			$res=creer_rep_docs_joints("../documents/archives/", $dossier_etab);
		}

		if(!file_exists("../documents/archives/".$dossier_etab."/index.html")) {
			//$res=creer_index_logout("../documents/archives/".$dossier_etab, "../../..");
			$res=creer_index_logout("../documents/archives/".$dossier_etab);
		}

		// Page HTML à faire à ce niveau pour accéder aux différentes années...
		// Stocker dans une table la liste des années archivées?

		if(file_exists($dossier_annee)) {
			if($confirmer_ecrasement!='y') {
				echo "<p style='color:red;'>The Folder $dossier_annee already exist.</p>\n";
	
				// CONFIRMER
				echo "<p>Do you want, despite everything, to archive again the text books?<br />The archived pages will be crushed.<br />You should perhaps start by downloading the pages currently archived by precaution.</p>\n";
	
				echo "<p><a href='".$_SERVER['PHP_SELF']."?confirmer_ecrasement=y&amp;step=1&amp;mode=$mode&amp;annee=$annee".add_token_in_url()."'>Archive again</a>.</p>";
	
				require("../lib/footer.inc.php");
				die();
			}

			echo "<p style='font-weight: bold;'>The folder $dossier_annee already exist.</p>\n";
			echo "<p>The pages previously filed will be crushed.</p>\n";

		}
		else {
			$res=mkdir($dossier_annee);
		}

		if(!file_exists($dossier_annee."/index.html")) {
			//$res=creer_index_logout($dossier_annee, "../../../..");
			$res=creer_index_logout($dossier_annee);
		}

		if(!file_exists($dossier_cdt)) {
			$res=mkdir($dossier_cdt);
			if(!$res) {
				echo "<p style='color:red;'>Error during the preparation of the tree structure $dossier_cdt</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		if(!file_exists($dossier_documents)) {
			$res=mkdir($dossier_documents);
			if(!$res) {
				echo "<p style='color:red;'>Error during the preparation of the tree structure $dossier_documents</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}

		if(!file_exists($dossier_documents."/index.html")) {
			//$res=creer_index_logout($dossier_annee, "../../../../..");
			$res=creer_index_logout($dossier_annee);
		}

		// On copie les feuilles de style pour:
		// 1. Se prémunir de modifications de styles dans des versions ultérieures de Gepi
		// 2. Permettre d'avoir un code couleur variant par année par exemple
		if(!file_exists($dossier_css)) {
			$res=mkdir($dossier_css);
			if(!$res) {
				echo "<p style='color:red;'>Error during the preparation of the tree structure $dossier_css</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
	
		// Copie des feuilles de styles
		$tab_styles=array("style.css", "style_old.css", "style_screen_ajout.css", "accessibilite.css", "accessibilite_print.css", "portable.css");
		for($i=0;$i<count($tab_styles);$i++) {
			if(file_exists("../".$tab_styles[$i])) {
				copy("../".$tab_styles[$i],$dossier_annee."/".$tab_styles[$i]);
			}
		}
	
		// Copie des feuilles de styles
		$tab_styles=array('bandeau_r01.css',
						'bandeau_r01_ie6.css',
						'bandeau_r01_ie7.css',
						'bandeau_r01_ie.css',
						'style.css',
						'style_ecran.css',
						'style_ecran_login.css',
						'style_ecran_login_IE.css',
						'style_imprime.css',
						'style_telephone.css',
						'style_telephone_login.css');
		for($i=0;$i<count($tab_styles);$i++) {
			copy("../css/".$tab_styles[$i],$dossier_css."/".$tab_styles[$i]);
		}

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		echo "<p>Les préparatifs sont faits.<br />Let us pass to the archiving itself&nbsp;:\n";
		echo add_token_field();
		echo "<input type='hidden' name='step' value='2' />\n";
		echo "<input type='hidden' name='mode' value='$mode' />\n";
		echo "<input type='hidden' name='annee' value='$annee' />\n";
		echo "<input type='submit' value='Archive' />\n";
		echo "</p>\n";
		echo "</form>\n";
	}
	else {

		$gepiSchoolName=getSettingValue('gepiSchoolName');
		$gepiYear=getSettingValue('gepiYear');

		$timestamp_debut_export=getSettingValue("begin_bookings");
		$timestamp_fin_export=getSettingValue("end_bookings");

		$display_date_debut=strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
		$display_date_fin=strftime("%d/%m/%Y", getSettingValue("end_bookings"));

		$largeur_tranche=10;

		$temoin_erreur="n";

		$extension="php";

		//$nom_fichier=array();

		$sql="SELECT * FROM tempo2 LIMIT $largeur_tranche;";
		$res_grp=mysql_query($sql);
		if(mysql_num_rows($res_grp)>0) {
			echo "<p><b>Archivage de</b>&nbsp;:<br />\n";
			while($lig_grp=mysql_fetch_object($res_grp)) {
				$id_groupe=$lig_grp->col1;
				//echo "<p>\$id_groupe=$id_groupe<br />";
				$current_group=get_group($id_groupe);

				// ====================================================
				// Page de l'enseignement n°$id_groupe de l'archive CDT
				// ====================================================

				/*
				$nom_groupe=preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['name'],'all')));
				$description_groupe=preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['description'],'all')));
				$classlist_string_groupe=preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['classlist_string'],'all')));
				*/
				$nom_groupe=preg_replace('/[^A-Za-z0-9\.-]/','_',preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['name'],'all'))));
				$description_groupe=preg_replace('/[^A-Za-z0-9\.-]/','_',preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['description'],'all'))));
				$classlist_string_groupe=preg_replace('/[^A-Za-z0-9\.-]/','_',preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['classlist_string'],'all'))));
				$nom_page_html_groupe=strtr($id_groupe."_".$nom_groupe."_".$description_groupe."_".$classlist_string_groupe.".$extension","/","_");


				$nom_complet_matiere=preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['matiere']['nom_complet'],'all')));
				$nom_enseignement=preg_replace('/&/','et',unhtmlentities(remplace_accents($nom_groupe." (".$description_groupe.")",'all')));


				$nom_detaille_groupe=$current_group['name']." (<i>".$current_group['description']." en (".$current_group['classlist_string'].")</i>)";

				$nom_detaille_groupe_non_html=$current_group['name']." (".$current_group['description']." en (".$current_group['classlist_string']."))";

				echo $nom_detaille_groupe."<br />";

				$nom_fichier=$nom_page_html_groupe;

				$tab_dates=array();
				$tab_dates2=array();
				$tab_chemin_url=array();
				$tab_notices=array();
				$tab_dev=array();


				$chaine_login_prof="";
				for($loop=0;$loop<count($current_group["profs"]["list"]);$loop++) {
					if($loop>0) {$chaine_login_prof.=", ";}
					$chaine_login_prof.="'".$current_group["profs"]["list"][$loop]."'";
				}

				$html="";
		
				//=====================
				// Le retour doit être différent pour un prof et pour les autres statuts
				$html.='<?php
if($_SESSION["statut"]=="professeur") {
	echo "<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'cdt_".$_SESSION["login"].".'.$extension.'\'>Retour</a></div>\n";
}
else {
';
				foreach($current_group['classes']['classes'] as $key => $value) {
					$html.='echo "<div class=\'noprint\' style=\'float:right; width:6em; margin: 3px; text-align:center; border: 1px solid black;\'><a href=\'classe_'.$value["id"].'.'.$extension.'\'>'.$value["classe"].'</a></div>\n";';
				}

				foreach($current_group['profs']['list'] as $key => $login_prof) {
					$html.='echo "<div class=\'noprint\' style=\'float:right; width:10em; margin: 3px; text-align:center; border: 1px solid black;\'><a href=\'cdt_'.$login_prof.'.'.$extension.'\'>'.$current_group['profs']['users'][$login_prof]['civilite'].' '.$current_group['profs']['users'][$login_prof]['nom'].' '.strtoupper(substr($current_group['profs']['users'][$login_prof]['prenom'],0,1)).'</a></div>\n";';
				}

				$html.='}
?>
';
				//=====================

				$html.="<h1 style='text-align:center;'>Log book (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
				$html.="<p style='text-align:center;'>Extraction of $display_date_debut au $display_date_fin</p>\n";
				$html.="<h2 style='text-align:center;'>Log book of ".$nom_detaille_groupe." (<i>$display_date_debut - $display_date_fin</i>)&nbsp;:</h2>\n";
		
				$sql="SELECT cte.* FROM ct_entry cte WHERE (contenu != ''
					AND date_ct != ''
					AND date_ct >= '".$timestamp_debut_export."'
					AND date_ct <= '".$timestamp_fin_export."'
					AND id_groupe='".$id_groupe."'
					) ORDER BY date_ct DESC, heure_entry DESC;";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				$cpt=0;
				while($lig=mysql_fetch_object($res)) {
					//echo "$lig->date_ct<br />";
					$date_notice=strftime("%a %d %b %y", $lig->date_ct);
					if(!in_array($date_notice,$tab_dates)) {
						$tab_dates[]=$date_notice;
						$tab_dates2[]=$lig->date_ct;
					}
					$tab_notices[$date_notice][$cpt]['id_ct']=$lig->id_ct;
					$tab_notices[$date_notice][$cpt]['id_login']=$lig->id_login;
					$tab_notices[$date_notice][$cpt]['contenu']=$lig->contenu;
					//echo " <span style='color:red'>\$tab_notices[$date_notice][$cpt]['contenu']=$lig->contenu</span><br />";
					$cpt++;
				}
			
				$sql="SELECT ctd.* FROM ct_devoirs_entry ctd WHERE (contenu != ''
					AND date_ct != ''
					AND date_ct >= '".$timestamp_debut_export."'
					AND date_ct <= '".$timestamp_fin_export."'
					AND id_groupe='".$id_groupe."'
					) ORDER BY date_ct DESC;";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				$cpt=0;
				while($lig=mysql_fetch_object($res)) {
					//echo "$lig->date_ct<br />";
					$date_dev=strftime("%a %d %b %y", $lig->date_ct);
					if(!in_array($date_dev,$tab_dates)) {
						$tab_dates[]=$date_dev;
						$tab_dates2[]=$lig->date_ct;
					}
					$tab_dev[$date_dev][$cpt]['id_ct']=$lig->id_ct;
					$tab_dev[$date_dev][$cpt]['id_login']=$lig->id_login;
					$tab_dev[$date_dev][$cpt]['contenu']=$lig->contenu;
					//echo " <span style='color:green'>\$tab_dev[$date_dev][$cpt]['contenu']=$lig->contenu</span><br />";
					$cpt++;
				}
				//echo "\$current_ordre=$current_ordre<br />";
				//sort($tab_dates);
				if($current_ordre=='ASC') {
					array_multisort ($tab_dates, SORT_DESC, SORT_NUMERIC, $tab_dates2, SORT_ASC, SORT_NUMERIC);
				}
				else {
					array_multisort ($tab_dates, SORT_ASC, SORT_NUMERIC, $tab_dates2, SORT_DESC, SORT_NUMERIC);
				}

				$html.=lignes_cdt($tab_dates, $tab_notices, $tab_dev,$dossier_documents,$mode);

				/*
				echo "<div style='border: 1px solid black;'>\n";
				echo $html;
				echo "</div>\n";
		
				echo "<script type='text/javascript'>
	if(document.getElementById('div_lien_retour_".$id_groupe."')) {
		document.getElementById('div_lien_retour_".$id_groupe."').style.display='none';
	}
</script>\n";
				*/

				$html=html_entete("CDT: ".$nom_detaille_groupe_non_html,1,'y',"$chaine_login_prof").$html;
				$html.=html_pied_de_page();

				//echo "\$dossier_cdt=$dossier_cdt<br />";
				//echo "\$nom_fichier=$nom_fichier<br />";
				$f=fopen($dossier_cdt."/".$nom_fichier,"w+");
				fwrite($f,$html);
				fclose($f);

				foreach($current_group["classes"]["classes"] as $key => $value) {
					// Pour ne créer les liens que pour les cahiers de textes non vides
					if(count($tab_dates)>0) {
						//$sql="INSERT INTO tempo3_cdt SET id_classe='".$value['id']."', classe='".$value['classe']." (".$value['nom_complet'].")"."', matiere='$nom_complet_matiere', enseignement='$nom_enseignement', id_groupe='".$id_groupe."', fichier='$nom_fichier';";
						$sql="INSERT INTO tempo3_cdt SET id_classe='".$value['id']."', classe='".addslashes($value['classe'])." (".addslashes($value['nom_complet']).")"."', matiere='".addslashes($nom_complet_matiere)."', enseignement='".addslashes($nom_enseignement)."', id_groupe='".$id_groupe."', fichier='$nom_fichier';";
						$insert=mysql_query($sql);updateOnline($sql);
						if(!$insert) {
							$temoin_erreur="y";
		
							echo "<p style='color:red'>ERROR during the recording in 'tempo3_cdt'&nbsp;: $sql</p>\n";
						}
					}
				}

				$sql="DELETE FROM tempo2 WHERE col1='$id_groupe';";
				$menage=mysql_query($sql);updateOnline($sql);
				if(!$menage) {
					$temoin_erreur="y";

					echo "<p style='color:red'> ERROR during cleaning of 'tempo2'&nbsp;: $sql</p>\n";
				}

				// A FAIRE: Ajouter à une liste? pour construire par la suite les pages d'index?

			}

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
			echo add_token_field();
			echo "<input type='hidden' name='step' value='2' />\n";
			echo "<input type='hidden' name='mode' value='$mode' />\n";
			echo "<input type='hidden' name='annee' value='$annee' />\n";
			echo "<p><input type='submit' value='Suite' /></p>\n";
			echo "</form>\n";

			if($temoin_erreur!='y') {
				echo "<script type='text/javascript'>
	setTimeout('document.formulaire.submit()',1000);
</script>\n";
			}


		}
		else {
			// Les pages des enseignements n°$id_groupe de l'archive CDT ont été générés à l'étape précedente

			echo "<p>The archiving of the lesson is done.<br />The index pages will now be created.</p>\n";

			// ============================
			// Page racine de l'archive CDT
			// ============================
			//$sql="SELECT * FROM tempo3_cdt ORDER BY classe, matiere;";
			$sql="SELECT DISTINCT id_classe, classe FROM tempo3_cdt ORDER BY classe;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {

				$html='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'../../../index.'.$extension.'\'>Return</a></div>';

				$html.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
				$html.="<p style='text-align:center;'>Extraction du $display_date_debut au $display_date_fin\n";
				$html.="<br />\n";
				$html.="(<i>Archiving carried out the ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
				$html.="</p>\n";

				$html.="<h2 style='text-align:center;'>Classes&nbsp;:</h2>\n";

				$html.="<div align='center'>\n";
				$html.="<table summary='Table of classes'>\n";
				while($lig_class=mysql_fetch_object($res)) {
					//$html.="Classe de <a href='classe_".$lig_class->id_classe.".$extension'>".$lig_class->classe."</a><br />";
					$html.="<tr><td>Classe de </td><td><a href='classe_".$lig_class->id_classe.".$extension'>".$lig_class->classe."</a></td></tr>\n";
					//$sql="SELECT * FROM tempo3_cdt WHERE classe='$lig_class->classe';";
				}
				$html.="</table>\n";
				$html.="</div>\n";

				$html.="<p><br /></p>\n";

				$html=html_entete("CDT: Index of classes",1,'y').$html;
				$html.=html_pied_de_page();
		
				$f=fopen($dossier_cdt."/index_classes.$extension","w+");
				fwrite($f,$html);
				fclose($f);
			}


			// =======================================
			// Page index des classes de l'archive CDT
			// =======================================
			$sql="SELECT DISTINCT id_classe, classe FROM tempo3_cdt ORDER BY classe;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				while($lig_class=mysql_fetch_object($res)) {

					$html='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'index_classes.'.$extension.'\'>Retour</a></div>';

					$html.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
					$html.="<p style='text-align:center;'>Extraction du $display_date_debut au $display_date_fin\n";
					$html.="<br />\n";
					$html.="(<i>Archivage effectué le ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
					$html.="</p>\n";
	
					$html.="<h2 style='text-align:center;'>Class of $lig_class->classe&nbsp;:</h2>\n";

					$sql="SELECT * FROM tempo3_cdt WHERE classe='".addslashes($lig_class->classe)."';";
					//echo "$sql<br />";
					$res2=mysql_query($sql);
					if(mysql_num_rows($res2)>0) {
						$html.="<div align='center'>\n";
						$html.="<table summary='Table of courses'>\n";
						while($lig_mat=mysql_fetch_object($res2)) {
							//$html.="<b>$lig_mat->matiere</b>&nbsp;:<a href='$lig_mat->fichier'> $lig_mat->enseignement</a><br />";

							$sql="SELECT DISTINCT u.* FROM utilisateurs u, j_groupes_professeurs jgp, tempo3_cdt t WHERE t.id_groupe=jgp.id_groupe AND u.login=jgp.login AND t.fichier='$lig_mat->fichier';";
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)>0) {
								$liste_profs="";
								while($lig_prof=mysql_fetch_object($res3)) {
									if($liste_profs!="") {$liste_profs.=", ";}
									$liste_profs.=$lig_prof->civilite." ".strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2');
								}
							}

							$html.="<tr><td><b>$lig_mat->matiere</b>&nbsp;:</td><td><a href='$lig_mat->fichier'> $lig_mat->enseignement</a></td><td>$liste_profs</td></tr>\n";
						}
						$html.="</table>\n";
						$html.="</div>\n";
					}

					$html=html_entete("CDT: Classe de ".$lig_class->classe,1,'y').$html;
					$html.=html_pied_de_page();
			
					$f=fopen($dossier_cdt."/classe_".$lig_class->id_classe.".$extension","w+");
					fwrite($f,$html);
					fclose($f);
				}
			}

			// ===========================================
			// Page index des professeurs de l'archive CDT
			// ===========================================
			$sql="SELECT DISTINCT u.* FROM tempo3_cdt t, j_groupes_professeurs jgp, utilisateurs u WHERE jgp.id_groupe=t.id_groupe AND jgp.login=u.login ORDER BY u.nom, u.prenom;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$html='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'index.'.$extension.'\'>Retour</a></div>';

				$html.="<h1 style='text-align:center;'>Log books (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
				$html.="<p style='text-align:center;'>Extraction of $display_date_debut au $display_date_fin\n";
				$html.="<br />\n";
				$html.="(<i>Archiving carried out the ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
				$html.="</p>\n";

				$html.="<h2 style='text-align:center;'>Professors&nbsp;:</h2>\n";

				$html.="<div align='center'>\n";
				while($lig_prof=mysql_fetch_object($res)) {
					$html.="<a href='cdt_".$lig_prof->login.".$extension'> $lig_prof->civilite ".strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2')."</a><br />";

					$sql="SELECT * FROM tempo3_cdt t, j_groupes_professeurs jgp WHERE jgp.id_groupe=t.id_groupe AND jgp.login='$lig_prof->login' ORDER BY classe, matiere;";
					$res2=mysql_query($sql);
					if(mysql_num_rows($res2)>0) {
						// ================================================================================================
						// Page index des enseignements du professeur courant ((essoufflé) dans la boucle) de l'archive CDT
						// ================================================================================================
						//$html2='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'index_professeurs.'.$extension.'\'>Retour</a></div>';
						$html2='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'';
						$html2.='<?php'."\n";
						//$html2.='if($_SESSION["statut"]=="professeur") {echo "CDT_".$_SESSION["login"];} else {echo "index_professeurs";}'."\n";
						$html2.='if($_SESSION["statut"]=="professeur") {echo "../../../index";} else {echo "index_professeurs";}'."\n";
						$html2.='?>';
						$html2.='.';
						$html2.=$extension.'\'>Retour</a></div>';

						$html2.="<h1 style='text-align:center;'>Log books (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
						$html2.="<p style='text-align:center;'>Extraction of $display_date_debut au $display_date_fin\n";
						$html2.="<br />\n";
						$html2.="(<i>Archiving carried out the ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
						$html2.="</p>\n";
		
						$html2.="<h2 style='text-align:center;'>Professor&nbsp;: $lig_prof->civilite ".strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2')."</h2>\n";

						$html2.="<div align='center'>\n";
						$html2.="<table border='0' summary='Table of the courses of $lig_prof->civilite ".strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2')."'>\n";
						$classe_prec="";
						$cpt=0;
						while($lig_clas_mat=mysql_fetch_object($res2)) {
							if($lig_clas_mat->classe!=$classe_prec) {
								if($classe_prec!="") {
									$html2.="</td>\n";
									$html2.="</tr>\n";
								}

								$classe_prec=$lig_clas_mat->classe;

								$html2.="<tr>\n";
								$html2.="<td style='vertical-align:top;'>$lig_clas_mat->classe</td>\n";
								$html2.="<td>\n";

							}
							$html2.="<b>$lig_clas_mat->matiere</b>&nbsp;:<a href='$lig_clas_mat->fichier'> $lig_clas_mat->enseignement</a><br />";

							$cpt++;
						}
						if($cpt>0) {
							$html2.="</td>\n";
							$html2.="</tr>\n";
						}
						$html2.="</table>\n";
						$html2.="</div>\n";

						$html2=html_entete("CDT: Professor ".$lig_prof->civilite." ".strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2'),1,'y',"'$lig_prof->login'").$html2;
						$html2.=html_pied_de_page();
				
						$f=fopen($dossier_cdt."/cdt_".$lig_prof->login.".$extension","w+");
						fwrite($f,$html2);
						fclose($f);
					}

				}
				$html.="</div>\n";

				$html.="<p><br /></p>\n";

				$html=html_entete("CDT: List of professors",1,'y').$html;
				$html.=html_pied_de_page();
		
				$f=fopen($dossier_cdt."/index_professeurs.$extension","w+");
				fwrite($f,$html);
				fclose($f);
			}


			// ==========================================================
			// Page de choix Index_classe ou Index_profs de l'archive CDT
			// ==========================================================
			// Faire en dessous une page qui parcourt les sous-dossiers d'années
			$html='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'../../../index.'.$extension.'\'>Return</a></div>';

			$html.="<h1 style='text-align:center;'>Log books (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
			$html.="<p style='text-align:center;'>Extraction of $display_date_debut au $display_date_fin\n";
			$html.="<br />\n";
			$html.="(<i>Archivage effectué le ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
			$html.="</p>\n";

			$html.="<div align='center'>\n";

			$html.="<p><a href='index_classes.".$extension."'>Index of classes</a></p>\n";
			$html.="<p><a href='index_professeurs.".$extension."'>Index of professors</a></p>\n";
			$html.="</div>\n";

			$html=html_entete("CDT: Index",1,'y').$html;
			$html.=html_pied_de_page();
	
			$f=fopen($dossier_cdt."/index.$extension","w+");
			fwrite($f,$html);
			fclose($f);

			echo "<p>Finished.<br />The pages of index were created now.</p>\n";

		}
	}
}
echo "<p><br /></p>\n";

// Evaluer le nom du dossier établissement selon le cas multisite ou non.<br />
// Calculer l'année à archiver selon la date courante ou d'après le paramétrage 'gepiYear'... ou proposer de saisir un autre nom d'année.<br /><br />
//Ajouter les liens dans le cahier de textes des profs... et scol? cpe?<br /><br />
echo "<p style='color:red'>TO MAKE: Don't propose the link to the archived years if no year is archived for the current user (variable according to whether one is a teacher or not)</p>\n";

require("../lib/footer.inc.php");
die();

?>
