<?php

@set_time_limit(0);
/*
* $Id: init_pp.php 5936 2010-11-21 17:32:17Z crob $
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

$liste_tables_del = array(
"j_eleves_professeurs"
);

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year : Importation of the principal professors";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return home initialization</a></p>

<?php

// On vérifie si l'extension d_base est active
//verif_active_dbase();

echo "<center><h3 class='gepi'>Sixth phase<br />Importation of the principals professors </h3></center>\n";

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
		echo "<p><b>CAUTION ...</b><br />\n";
		echo "Principal professors are currently defined in base GEPI<br /></p>\n";
		echo "<p>If you continue the procedure these data will be removed and replaced by those of your file F_DIV.CSV</p>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();
		echo "<input type='hidden' name='step1' value='y' />\n";
		echo "<input type='submit' name='confirm' value='Continue the procedure' />\n";
		echo "</form>\n";
		echo "<br />\n";
		require("../lib/footer.inc.php");
		die();
	}
}

if (!isset($is_posted)) {
	if(isset($step1)) {
		check_token(false);
		$j=0;
		while ($j < count($liste_tables_del)) {
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
			}
			$j++;
		}
	}

	echo "<p><b>CAUTION ...</b><br />You should proceed to this operation only if the constitution of
the classes were carried out and the professors were imported !</p>\n";
	echo "<p>Importation of the file <b>F_div.csv</b> containing associations class/principal professor  : please specify the complete name of the file <b>F_div.csv</b>.\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=hidden name='step1' value='y' />\n";
	echo "<p><input type='file' size='80' name='dbf_file' />\n";
	echo "<p><input type=submit value='Validate' />\n";
	echo "</form>\n";

} else {
	$dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
	//if(strtoupper($dbf_file['name']) == "F_TMT.DBF") {
	if(strtoupper($dbf_file['name']) == "F_DIV.CSV") {
		//$fp = dbase_open($dbf_file['tmp_name'], 0);
		$fp = fopen($dbf_file['tmp_name'],"r");
		if(!$fp) {
			echo "<p>Impossible d'ouvrir le fichier CSV</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Click here </a> to restart !</center></p>";
		} else {
			// on constitue le tableau des champs à extraire
			//$tabchamps = array("MATIMN","MATILC");
			$tabchamps = array("DIVCOD","NUMIND");

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
/*
			echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des nouvelles matières dans la base GEPI. les identifiants en vert correspondent à des identifiants de matières détectés dans le fichier GEP mais déjà présents dans la base GEPI.<br /><br />Il est possible que certaines matières ci-dessous, bien que figurant dans le fichier GEP, ne soient pas utilisées dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialsation, un nettoyage de la base afin de supprimer ces données inutiles.</p>";
*/
			echo "<table border=1 cellpadding=2 cellspacing=2>\n";
			echo "<tr><th><p class=\"small\">Class</p></th><th><p class=\"small\">Principal professor</p></th></tr>\n";


			//=========================
			$fp=fopen($dbf_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_no = 0;
			$nb_reg_ok = 0;
			for($k = 1; ($k < $nblignes+1); $k++){
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					//=========================
					// MODIF: boireaus 20071024
					//$ligne = fgets($fp, 4096);
					$ligne = my_ereg_replace('"','',fgets($fp, 4096));
					//=========================
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						$temoin_erreur="non";
						for($i = 0; $i < count($tabchamps); $i++) {
							//$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[$tabindice[$i]]))));
							$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
							//echo "<tr><td colspan='2'>|\$affiche[$i]|=|$affiche[$i]|</td></tr>";
							if($affiche[$i]==""){
								$temoin_erreur="oui";
								$nb_reg_no++;
							}
						}
						if($temoin_erreur!="oui"){
							$sql="SELECT id FROM classes WHERE classe='$affiche[0]'";
							$res_classe=mysql_query($sql);
							if(mysql_num_rows($res_classe)==1){
								$lig_classe=mysql_fetch_object($res_classe);
								$id_classe=$lig_classe->id;

								/*
								$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode"
								$res_periodes=mysql_query($sql);
								unset($tab_num_periode);
								$tab_num_periode=array();
								while($lig_periode=mysql_fetch_object($res_periodes)){
									$tab_num_periode[]=$lig_periode->num_periode;
								}
								*/
								$sql="SELECT col1 FROM tempo2 WHERE col2='$affiche[1]'";
								$res_prof=mysql_query($sql);
								$lig_prof=mysql_fetch_object($res_prof);

								//$sql="SELECT login,periode FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login,periode"
								//$sql="SELECT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login";
								$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login";
								$res_eleve=mysql_query($sql);
								$temoin_erreur_classe=0;
								while($lig_eleve=mysql_fetch_object($res_eleve)){
									$sql="INSERT INTO j_eleves_professeurs VALUES('$lig_eleve->login','$lig_prof->col1','$id_classe')";
									$res_prof_eleve=mysql_query($sql);
									if(!$res_prof_eleve){
										$temoin_erreur_classe++;
										//echo "<tr><td>$sql</td></tr>\n";
									}
								}
								if($temoin_erreur_classe==0){
									echo "<tr style='background-color:#aae6aa;'><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
									$nb_reg_ok++;
								}
								else{
									echo "<tr style='background-color:red;'><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
									$nb_reg_no++;
								}
								//echo "<tr><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
							}
						}
						else{
							echo "<tr style='background-color:red;'><td>$affiche[0]</td><td>$affiche[1]</td></tr>\n";
						}
					}
				}
			}
			echo "</table>\n";
			//dbase_close($fp);
			fclose($fp);
			if ($nb_reg_no != 0) {
				echo "<p>During recording of the data there was $nb_reg_no errors.<br />Test find the cause of the error and restart the procedure before passing to the next stage.</p>\n";
			} else {
				if($nb_reg_ok>0){
					echo "<p>The importation of the principal professors in base GEPI was carried out successfully !</p>\n";
				}
				else{
					echo "<p>No principal professor was registered in base GEPI !</p>\n";
				}

				echo "<p>Before carrying out a cleaning of the tables to remove the useless
data, you should carry out one <a href='../gestion/accueil_sauve.php?action=dump".add_token_in_url()."'>backup</a><br />\n";
				echo "After this backup, carry out cleaning while passing again in 'General management/Initialization of the data starting from files DBF and XML/Proceed to the seventh phase'.<br />\n";
				echo "If the data are indeed useless, it is finished.<br />\n";
				echo "If not, you will be able to restore your backup and you will have been able to note associations profs/courses/missing classes... to carry out thereafter manually in 'Management of bases'.</p>";

				echo "<p>You can proceed at the next stage of cleaning of tables GEPI.</p>\n";
				echo "<center><p><a href='clean_tables.php?a=a".add_token_in_url()."'>Suppression of the useless data</a></p></center>\n";
			}
		}
	} else if (trim($dbf_file['name'])=='') {
		echo "<p>No file was selected !<br />\n";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> to restart !</center></p>\n";

	} else {
		echo "<p>The selected file is not valid !<br />\n";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> to restart !</center></p>\n";
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>