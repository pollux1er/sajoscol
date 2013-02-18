<?php
	@set_time_limit(0);

	// $Id: matieres.php 8335 2011-09-23 17:04:45Z crob $

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
	$titre_page = "Tool of initialization of the year : Importation of the courses";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************

	require_once("init_xml_lib.php");

	function extr_valeur($lig){
		unset($tabtmp);
		$tabtmp=explode(">",preg_replace("/</",">",$lig));
		return trim($tabtmp[2]);
	}

	function ouinon($nombre){
		if($nombre==1){return "O";}elseif($nombre==0){return "N";}else{return "";}
	}
	function sexeMF($nombre){
		//if($nombre==2){return "F";}else{return "M";}
		if($nombre==2){return "F";}elseif($nombre==1){return "M";}else{return "";}
	}

	function affiche_debug($texte){
		// Passer à 1 la variable pour générer l'affichage des infos de debug...
		$debug=0;
		if($debug==1){
			echo "<font color='green'>".$texte."</font>";
		}
	}

		// Etape...
	$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

	$verif_tables_non_vides=isset($_POST['verif_tables_non_vides']) ? $_POST['verif_tables_non_vides'] : NULL;

	if(isset($_GET['ad_retour'])){
		$_SESSION['ad_retour']=$_GET['ad_retour'];
	}
	//echo "\$_SESSION['ad_retour']=".$_SESSION['ad_retour']."<br />";

	include("../lib/initialisation_annee.inc.php");
	$liste_tables_del = $liste_tables_del_etape_matieres;


	// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>It seems that the temporary folder of the user ".$_SESSION['login']." is not defined!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}


	// =======================================================
	// EST-CE ENCORE UTILE?
	if(isset($_GET['nettoyage'])){
		check_token(false);

		//echo "<h1 align='center'>Suppression des CSV</h1>\n";
		echo "<h2>Suppression of XML</h2>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])){
			echo $_SESSION['ad_retour'];
		}
		else{
			echo "index.php";
		}
		echo "'> <img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>";
		echo "<a href='".$_SERVER['PHP_SELF']."'> | Another importation</a></p>\n";
		//echo "</div>\n";

		echo "<p>If files XML exist, they will be removed...</p>\n";
		//$tabfich=array("f_ele.csv","f_ere.csv");
		$tabfich=array("sts.xml","nomenclature.xml");

		for($i=0;$i<count($tabfich);$i++){
			if(file_exists("../temp/".$tempdir."/$tabfich[$i]")) {
				echo "<p>Suppression de $tabfich[$i]... ";
				if(unlink("../temp/".$tempdir."/$tabfich[$i]")){
					echo "réussie.</p>\n";
				}
				else{
					echo "<font color='red'>Failure!</font> Check the rights of writing on the server.</p>\n";
				}
			}
		}

		require("../lib/footer.inc.php");
		die();
	}
	// =======================================================
	else{
		echo "<center><h3 class='gepi'>Importation of the courses</h3></center>\n";
		//echo "<h2>Préparation des données élèves/classes/périodes/options</h2>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])){
			// On peut venir de l'index init_xml, de la page de conversion ou de la page de mise à jour Sconet
			echo $_SESSION['ad_retour'];
		}
		else{
			echo "index.php";
		}
		echo "'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>";

		//echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre import</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Removal of existing XML files </a>";
		echo "</p>\n";
		//echo "</div>\n";

		//if(!isset($_POST['is_posted'])){
		if(!isset($step)){

			if(!isset($verif_tables_non_vides)) {
				$j=0;
				$flag=0;
				$chaine_tables="";
				while (($j < count($liste_tables_del)) and ($flag==0)) {
					//if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
					$sql="SELECT 1=1 FROM $liste_tables_del[$j];";
					//echo "$sql<br />";
					$test_del=mysql_query($sql);
					if(mysql_num_rows($test_del)>0) {
						if (mysql_result($test_del,0)!=0) {
							$flag=1;
						}
					}
					$j++;
				}
				for($loop=0;$loop<count($liste_tables_del);$loop++) {
					if($chaine_tables!="") {$chaine_tables.=", ";}
					$chaine_tables.="'".$liste_tables_del[$loop]."'";
				}

				if ($flag != 0){
					echo "<p><b>CAUTION ...</b><br />\n";
					echo "Data concerning the courses are currently present in base GEPI<br /></p>\n";
					echo "<p>If you continue the procedure the data such as notes, appreciations, ... will be erased.</p>\n";
					echo "<p>Only the table containing the courses and the table connecting the courses and the professors will be preserved.</p>\n";

					echo "<p>The emptied tables will be&nbsp;: $chaine_tables</p>\n";

					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
					echo add_token_field();
					echo "<input type=hidden name='verif_tables_non_vides' value='y' />\n";
					echo "<input type='submit' name='confirm' value='Continue the procedure' />\n";
					echo "</form>\n";
					echo "</div>\n";
					echo "</body>\n";
					echo "</html>\n";
					die();
				}
			}


			if(isset($verif_tables_non_vides)) {
				check_token(false);

				$j=0;
				while ($j < count($liste_tables_del)) {
					if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
						$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
					}
					$j++;
				}
			}

			echo "<p><b>CAUTION ...</b><br />You should proceed to this operation only if the constitution of the classes were carried out !</p>\n";

			echo "<p>This page allows to upload a file which will be useful to fill the tables of GEPI with information of professors, courses,...</p>\n";

			echo "<p>It is necessary to provide him an Export XML carried out from the
STS-Web application.<br />Ask nicely your secretary access with STS-Web and tomake 'Update/Exports/Timetables'.</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo add_token_field();
			echo "<p>Please provide file XML <b>sts_emp_<i>RNE</i>_<i>ANNEE</i>.xml</b>&nbsp;: \n";
			echo "<p><input type=\"file\" size=\"65\" name=\"xml_file\" />\n";
			echo "<p><input type=\"hidden\" name=\"step\" value=\"0\" />\n";
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "</p>\n";


			echo "<input type='hidden' name='is_posted' value='yes' />\n";

			echo "<p><input type='submit' value='Validate' /></p>\n";
			echo "</form>\n";
		}
		else{
			check_token(false);

			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');

			if($step==0){
				$xml_file=isset($_FILES["xml_file"]) ? $_FILES["xml_file"] : NULL;

				if(!is_uploaded_file($xml_file['tmp_name'])) {
					echo "<p style='color:red;'>The upload of the file failed.</p>\n";

					echo "<p>The variables of the php.ini can perhaps explain the problem:<br />\n";
					echo "post_max_size=$post_max_size<br />\n";
					echo "upload_max_filesize=$upload_max_filesize<br />\n";
					echo "</p>\n";

					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}
				else{
					if(!file_exists($xml_file['tmp_name'])){
						echo "<p style='color:red;'>The file would have been uploaded... but would not be present/preserved.</p>\n";

						echo "<p>The variables of the php.ini can perhaps explain the problem:<br />\n";
						echo "post_max_size=$post_max_size<br />\n";
						echo "upload_max_filesize=$upload_max_filesize<br />\n";
						echo "and the volume of ".$xml_file['name']." would be<br />\n";
						echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
						echo "</p>\n";
						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}

					echo "<p>The file was uploaded.</p>\n";


					//$source_file=stripslashes($xml_file['tmp_name']);
					$source_file=$xml_file['tmp_name'];
					$dest_file="../temp/".$tempdir."/sts.xml";
					$res_copy=copy("$source_file" , "$dest_file");

					if(!$res_copy){
						echo "<p style='color:red;'>The copy of the file towards the temporary folder failed.<br />Check that the user or the apache group or www-data has access to the folder temp /$tempdir</p>\n";
						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}
					else{
						echo "<p>The copy of the file towards the temporary folder succeeded.</p>\n";

						// Table destinée à stocker l'association code/code_gestion utilisée dans d'autres parties de l'initialisation
						$sql="CREATE TABLE IF NOT EXISTS temp_matieres_import (
								code varchar(40) NOT NULL default '',
								code_gestion varchar(40) NOT NULL default '',
								libelle_court varchar(40) NOT NULL default '',
								libelle_long varchar(255) NOT NULL default '',
								libelle_edition varchar(255) NOT NULL default ''
								);";
						$create_table = mysql_query($sql);

						$sql="TRUNCATE TABLE temp_matieres_import;";
						$vide_table = mysql_query($sql);


						/*
						// On va lire plusieurs fois le fichier pour remplir des tables temporaires.
						$fp=fopen($dest_file,"r");
						if($fp){
							echo "<p>Lecture du fichier STS Emploi du temps...<br />\n";
							//echo "<blockquote>\n";
							while(!feof($fp)){
								$ligne[]=fgets($fp,4096);
							}
							fclose($fp);
							//echo "<p>Terminé.</p>\n";
						}
						*/
						flush();

						$sts_xml=simplexml_load_file($dest_file);
						if(!$sts_xml) {
							echo "<p style='color:red;'>FAILURE of the loading of the file with simpleXML.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}
		
						$nom_racine=$sts_xml->getName();
						if(strtoupper($nom_racine)!='STS_EDT') {
							echo "<p style='color:red;'><b>ERROR&nbsp;:</b> Provided file XML does not seem to be a file XML STS_EMP_&lt;RNE&gt;_&lt;ANNEE&gt;.<br />Its root should be 'STS_EDT'.</p>\n";

							if(strtoupper($nom_racine)=='EDT_STS') {
								echo "<p style='color:red;'>You were mistaken in export.<br />You probably used an export of your software EDT of Index Education, instead of export XML coming from STS.</p>\n";
							}

							require("../lib/footer.inc.php");
							die();
						}

						// On commence par la section MATIERES.
						echo "Analyze file to extract information from the section COURSES...<br />\n";

						$tab_champs_matiere=array("CODE_GESTION",
						"LIBELLE_COURT",
						"LIBELLE_LONG",
						"LIBELLE_EDITION");

						$matiere=array();
						// Compteur matieres:
						$i=0;
				
						foreach($sts_xml->NOMENCLATURES->MATIERES->children() as $objet_matiere) {
				
							foreach($objet_matiere->attributes() as $key => $value) {
								// <MATIERE CODE="090100">
								$matiere[$i][strtolower($key)]=trim(traite_utf8($value));
							}
				
							// Champs de la matière
							foreach($objet_matiere->children() as $key => $value) {
								if(in_array(strtoupper($key),$tab_champs_matiere)) {
									if(strtoupper($key)=='CODE_GESTION') {
										$matiere[$i][strtolower($key)]=trim(preg_replace("/[^a-zA-Z0-9&_. -]/","",html_entity_decode_all_version(traite_utf8($value))));
									}
									elseif(strtoupper($key)=='LIBELLE_COURT') {
										$matiere[$i][strtolower($key)]=trim(preg_replace("/[^A-Za-zÆæ¼½".$liste_caracteres_accentues."0-9&_. -]/","",html_entity_decode_all_version(traite_utf8($value))));
									}
									else {
										$matiere[$i][strtolower($key)]=traitement_magic_quotes(corriger_caracteres(trim(preg_replace('/"/','',traite_utf8($value)))));
									}
								}
							}

							if($debug_import=='y') {
								echo "<pre style='color:green;'><b>Table \$adresses[$i]&nbsp;:</b>";
								print_r($adresses[$i]);
								echo "</pre>";
							}
				
							$i++;
						}

						$i=0;
						$nb_err=0;
						$stat=0;
						while($i<count($matiere)){
							//$sql="INSERT INTO temp_resp_pers_import SET ";
							$sql="INSERT INTO temp_matieres_import SET ";
							$sql.="code='".$matiere[$i]["code"]."', ";
							$sql.="code_gestion='".$matiere[$i]["code_gestion"]."', ";
							$sql.="libelle_court='".$matiere[$i]["libelle_court"]."', ";
							$sql.="libelle_long='".$matiere[$i]["libelle_long"]."', ";
							$sql.="libelle_edition='".$matiere[$i]["libelle_edition"]."';";
							affiche_debug("$sql<br />\n");
							$res_insert=mysql_query($sql);
							if(!$res_insert){
								echo "Error during request $sql<br />\n";
								flush();
								$nb_err++;
							}
							else{
								$stat++;
							}

							$i++;
						}



						echo "<p>In the table below, the identifiers in red correspond to new courses in base GEPI. the identifiers in green correspond to course identifiers detected
in file GEP but already present in base GEPI.<br /><br />It is possible that certain courses below, although appearing in file CSV, are not used in your school this year. This is why it will be proposed to you at the end of the procedure of
initialization, a cleaning of the base in order to remove these useless data.</p>\n";

						echo "<table border='1' class='boireaus' cellpadding='2' cellspacing='2' summary='Table of the courses'>\n";

						echo "<tr><th><p class=\"small\">Identifier of the course</p></th><th><p class=\"small\">Complete name</p></th></tr>\n";

						$i=0;
						//$nb_err=0;
						$nb_reg_no=0;
						//$stat=0;

						$alt=1;
						while($i<count($matiere)){
							$sql="select matiere, nom_complet from matieres where matiere='".$matiere[$i]['code_gestion']."';";
							$verif=mysql_query($sql);
							$resverif = mysql_num_rows($verif);
							if($resverif==0) {
								$sql="insert into matieres set matiere='".$matiere[$i]['code_gestion']."', nom_complet='".$matiere[$i]['libelle_court']."', priority='0',matiere_aid='n',matiere_atelier='n';";
								$req=mysql_query($sql);
								if(!$req) {
									$nb_reg_no++;
									echo mysql_error();
								}
								else {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'>\n";
									echo "<td><p><font color='red'>".$matiere[$i]['code_gestion']."</font></p></td><td><p>".htmlentities($matiere[$i]['libelle_court'])."</p></td></tr>\n";
								}
							} else {
								$nom_complet = mysql_result($verif,0,'nom_complet');
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'>\n";
								echo "<td><p><font color='green'>".$matiere[$i]['code_gestion']."</font></p></td><td><p>".htmlentities($nom_complet)."</p></td></tr>\n";
							}

							$i++;
						}

						echo "</table>\n";




						// Importation des MEF
						$divisions=array();
						$tab_mef_code=array();
						$i=0;
						foreach($sts_xml->DONNEES->STRUCTURE->DIVISIONS->children() as $objet_division) {
							$divisions[$i]=array();
					
							foreach($objet_division->attributes() as $key => $value) {
								if(strtoupper($key)=='CODE') {
									$divisions[$i]['code']=preg_replace('/"/','',trim(traite_utf8($value)));
									//echo "<p>\$divisions[$i]['code']=".$divisions[$i]['code']."<br />";
									break;
								}
							}

							// Champs de la division
							foreach($objet_division->MEFS_APPARTENANCE->children() as $mef_appartenance) {
								foreach($mef_appartenance->attributes() as $key => $value) {
									// Normalement, on ne devrait faire qu'un tour:
									$divisions[$i]["mef_code"][]=trim(traite_utf8($value));
									$tab_mef_code[]=trim(traite_utf8($value));
									//echo "\$divisions[$i][\"mef_code\"][]=trim(traite_utf8($value))<br />";
								}
							}
							$i++;
						}

						for($i=0;$i<count($divisions);$i++) {
							if(isset($divisions[$i]["mef_code"][0])) {
								$sql="UPDATE eleves SET mef_code='".$divisions[$i]["mef_code"][0]."' WHERE login IN (SELECT j.login FROM j_eleves_classes j, classes c WHERE j.id_classe=c.id AND c.classe='".addslashes($divisions[$i]["code"])."');";
								//echo "$sql<br />";
								$update_mef=mysql_query($sql);
							}
						}

						$tab_champs_mef=array("LIBELLE_COURT",
						"LIBELLE_LONG",
						"LIBELLE_EDITION");

						$mefs=array();
						$i=0;
						foreach($sts_xml->NOMENCLATURES->MEFS->children() as $objet_mef) {
							$mefs[$i]=array();
					
							foreach($objet_mef->attributes() as $key => $value) {
								if(strtoupper($key)=='CODE') {
									$mefs[$i]['code']=preg_replace('/"/','',trim(traite_utf8($value)));
									break;
								}
							}

							if(in_array($mefs[$i]['code'],$tab_mef_code)) {
								// Champs MEF
								foreach($objet_mef->children() as $key => $value) {
									if(in_array(strtoupper($key),$tab_champs_mef)) {
										$mefs[$i][strtolower($key)]=trim(preg_replace("/[^A-Za-zÆæ¼½".$liste_caracteres_accentues."0-9&_. -]/","",html_entity_decode_all_version(traite_utf8($value))));
									}
								}
								$i++;
							}
						}

						for($i=0;$i<count($mefs);$i++) {
							$sql="SELECT 1=1 FROM mef WHERE mef_code='".$mefs[$i]['code']."';";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)>0) {
								$sql="UPDATE mef SET ";
								if(isset($mefs[$i]["libelle_court"])) {
									$sql.=" libelle_court='".$mefs[$i]["libelle_court"]."',";
								}
								//elseif(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_court='".$mefs[$i]["libelle_long"]."',";}
								else {
									$sql.=" libelle_court='',";
								}
								if(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_long='".$mefs[$i]["libelle_long"]."',";}
								if(isset($mefs[$i]["libelle_edition"])) {$sql.=" libelle_edition='".$mefs[$i]["libelle_edition"]."',";}
								$sql.=" mef_code='".$mefs[$i]["code"]."' WHERE mef_code='".$mefs[$i]["code"]."';";
								//echo "$sql<br />";
								$update_mef=mysql_query($sql);
							}
							else{
								$sql="INSERT INTO mef SET ";
								//if(isset($mefs[$i]["libelle_court"])) {$sql.=" libelle_court='".$mefs[$i]["libelle_court"]."',";} elseif(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_court='".$mefs[$i]["libelle_long"]."',";}
								if(isset($mefs[$i]["libelle_court"])) {
									$sql.=" libelle_court='".$mefs[$i]["libelle_court"]."',";
								}
								//elseif(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_court='".$mefs[$i]["libelle_long"]."',";}
								else {
									$sql.=" libelle_court='',";
								}
								if(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_long='".$mefs[$i]["libelle_long"]."',";}
								if(isset($mefs[$i]["libelle_edition"])) {$sql.=" libelle_edition='".$mefs[$i]["libelle_edition"]."',";}
								$sql.=" mef_code='".$mefs[$i]["code"]."';";
								//echo "$sql<br />";
								$insert=mysql_query($sql);
							}
						}


						if ($nb_reg_no != 0) {
							echo "<p>During recording of the data there was $nb_reg_no errors. Test find the cause of the error and start again the procedure before passing at the following stage.";
						} else {
							echo "<p>The importation of the courses in base GEPI was carried out successfully !<br />You can proceed to the fourth phase of importation of the professors.</p>";
						}

						//echo "<center><p><a href='prof_csv.php'>Importation des professeurs</a></p></center>";
						//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1'>Importation des professeurs</a></p>\n";
						echo "<p align='center'><a href='professeurs.php'>Importation of professors</a></p>\n";
						echo "<p><br /></p>\n";

						require("../lib/footer.inc.php");
						die();
					}
				}
			}
		}
	}
	require("../lib/footer.inc.php");
?>