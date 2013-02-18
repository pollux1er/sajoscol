<?php

@set_time_limit(0);
/*
* $Id: disciplines_csv.php 7858 2011-08-21 13:12:55Z crob $
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

// Initialisations files
require_once("../lib/initialisations.inc.php");
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_matieres;

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year : Importation of courses";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>

<?php

// On vérifie si l'extension d_base est active
//verif_active_dbase();

echo "<center><h3 class='gepi'>Third phase of initialization<br />Importation of courses</h3></center>";

if (!isset($step1)) {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$flag=1;
		}
		$j++;
	}
	if ($flag != 0){
		echo "<p><b>CAUTION ...</b><br />";
		echo "Data concerning the courses are currently present in base GEPI<br /></p>";
		echo "<p>If you continue the procedure the data such as notes, appreciations, ... will be erased.</p>";
		echo "<p>Only the table containing the courses and the table connecting the courses and the professors will be preserved.</p>";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
		echo add_token_field();
		echo "<input type=hidden name='step1' value='y' />";
		echo "<input type='submit' name='confirm' value='Continue the procedure' />";
		echo "</form>";
		echo "</div>";
		echo "</body>";
		echo "</html>";
		die();
	}
}

if (!isset($is_posted)) {
	if(isset($step1)) {
		$j=0;
		while ($j < count($liste_tables_del)) {
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
			}
			$j++;
		}
	}

	echo "<p><b>CAUTION ...</b><br />You should proceed to this operation only if the constitution of the classes were carried out !</p>";
	echo "<p>Importation of the file <b>F_tmt.csv</b> containing the data relating to the courses : please specify the complete name of the file <b>F_tmt.csv</b>.";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
	echo add_token_field();
	echo "<input type=hidden name='is_posted' value='yes' />";
	echo "<input type=hidden name='step1' value='y' />";
	echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<p><input type=submit value='Validate' />";
	echo "</form>";

} else {
	check_token(false);

	$dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
	//if(strtoupper($dbf_file['name']) == "F_TMT.DBF") {
	if(strtoupper($dbf_file['name']) == "F_TMT.CSV") {
		//$fp = dbase_open($dbf_file['tmp_name'], 0);
		$fp = fopen($dbf_file['tmp_name'],"r");
		if(!$fp) {
			echo "<p>Impossible d'ouvrir le fichier dbf</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Click here </a> to restart !</center></p>";
		} else {
			// on constitue le tableau des champs à extraire
			$tabchamps = array("MATIMN","MATILC");

			//$nblignes = dbase_numrecords($fp); //number of rows
			//$nbchamps = dbase_numfields($fp); //number of fields

			$nblignes=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if($nblignes==0){
					// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
					// On ne retient pas ces ajouts pour $en_tete
					$temp=explode(";",$ligne);
					for($i=0;$i<sizeof($temp);$i++){
						$temp2=explode(",",$temp[$i]);
						$en_tete[$i]=$temp2[0];
					}

					//$en_tete=explode(";",$ligne);
					$nbchamps=sizeof($en_tete);
				}
				$nblignes++;
			}
			fclose ($fp);
/*
			if (@dbase_get_record_with_names($fp,1)) {
				$temp = @dbase_get_record_with_names($fp,1);
			} else {
				echo "<p>Le fichier sélectionné n'est pas valide !<br />";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> to restart !</center></p>";
				die();
			}

			$nb = 0;
			foreach($temp as $key => $val){
				$en_tete[$nb] = "$key";
				$nb++;
			}
*/
			// On range dans tabindice les indices des champs retenus
			/*
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					//if ($en_tete[$i] == $tabchamps[$k]) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[] = $i;
					}
				}
			}
			*/
			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[$cpt_tmp]=$i;
						$cpt_tmp++;
					}
				}
			}
			echo "<p>In the table below, the identifiers in red correspond to new courses in base GEPI. the identifiers in green correspond to course identifiers detected
in file GEP but already present in base GEPI.<br /><br />It is possible that certain courses below, although appearing in file CSV, are not used in your school this year. This is why it will be proposed to you at the end of the procedure of
initialsation, a cleaning of the base in order to remove these useless data.</p>";
			echo "<table border=1 cellpadding=2 cellspacing=2>";
			echo "<tr><td><p class=\"small\">Identifier of the course</p></td><td><p class=\"small\">Complete name</p></td></tr>";


			//=========================
			$fp=fopen($dbf_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_no = 0;
			for($k = 1; ($k < $nblignes+1); $k++){
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					//=========================
					// MODIF: boireaus 20071024
					//$ligne = fgets($fp, 4096);
					$ligne = preg_replace('/"/','',fgets($fp, 4096));
					//=========================
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							//$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[$tabindice[$i]]))));
							$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
						}
						$verif = mysql_query("select matiere, nom_complet from matieres where matiere='$affiche[0]'");
						$resverif = mysql_num_rows($verif);
						if($resverif == 0) {
							$req = mysql_query("insert into matieres set matiere='$affiche[0]', nom_complet='$affiche[1]', priority='0',matiere_aid='n',matiere_atelier='n'");
							if(!$req) {
								$nb_reg_no++; echo mysql_error();
							} else {
								echo "<tr><td><p><font color='red'>$affiche[0]</font></p></td><td><p>".htmlentities($affiche[1])."</p></td></tr>";
							}
						} else {
							$nom_complet = mysql_result($verif,0,'nom_complet');
							echo "<tr><td><p><font color='green'>$affiche[0]</font></p></td><td><p>".htmlentities($nom_complet)."</p></td></tr>";
						}
					}
				}
			}
			echo "</table>";
			//dbase_close($fp);
			fclose($fp);
			if ($nb_reg_no != 0) {
				echo "<p>During recording of the data there was $nb_reg_no errors. Test find the cause of the error and start again the procedure before passing to the next stage.";
			} else {
				echo "<p>The importation of the courses in base GEPI was carried out
successfully !<br />You can proceed to the fourth phase of importation of the professors.</p>";
			}
			echo "<center><p><a href='prof_csv.php?a=a".add_token_in_url()."'>Importation of the professors</a></p></center>";
		}
	} else if (trim($dbf_file['name'])=='') {
		echo "<p>No file was selected !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> to restart !</center></p>";

	} else {
		echo "<p>Le fichier sélectionné n'est pas valide !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> to restart !</center></p>";
	}
}
require("../lib/footer.inc.php");
?>