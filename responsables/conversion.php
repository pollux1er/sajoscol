<?php
/*
 *
 * $Id: conversion.php 5938 2010-11-21 18:14:45Z crob $
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// A FAIRE:
// ALTER TABLE `eleves` ADD `ele_id` VARCHAR( 10 ) NOT NULL ;


//**************** EN-TETE *****************
$titre_page = "Update of student/responsible";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<p class=bold>";
echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Retour</a>";
echo "</p>\n";

// Traitement particulier LCS : on met à jour l'identifiant national dans eleves (np_gep)
if(getSettingValue('auth_sso')=="lcs") {
	echo "<h2>Update of the data student and responsible</h2>\n";
}
else {
	echo "<h2>Conversion student/responsible</h2>\n";
}

// Suppression de l'adresse de retour mise pour permettre la génération des CSV
if(isset($_SESSION['ad_retour'])){
	unset($_SESSION['ad_retour']);
}


// Solution de conversion d'une part...
// ... et proposer d'autre part une mise à jour par import Sconet

$sql="SELECT value FROM setting WHERE name='conv_new_resp_table'";
$test=mysql_query($sql);
if(mysql_num_rows($test) > 0){
	$ligtmp=mysql_fetch_object($test);
	if($ligtmp->value>0){
		echo "<p>The update was already carried out.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$temoin=1;
	}
}
else{
	$temoin=1;
}

if($temoin==1){
	$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

	// Ajout, si nécessaire, du champ 'ele_id' à la table 'eleves':
	$sql="SHOW FIELDS FROM eleves";
	$test=mysql_query($sql);
	$temoin_ele_id="";
	while($tabtmp=mysql_fetch_array($test)){
		if(strtolower($tabtmp[0])=="ele_id"){
			$temoin_ele_id="oui";
		}
	}
	if($temoin_ele_id==""){
		$sql="ALTER TABLE `eleves` ADD `ele_id` VARCHAR( 10 ) NOT NULL ;";
		$res_ele_id=mysql_query($sql);
	}


	$sql="CREATE TABLE IF NOT EXISTS `responsables2` (
	`ele_id` varchar(10) NOT NULL,
	`pers_id` varchar(10) NOT NULL,
	`resp_legal` varchar(1) NOT NULL,
	`pers_contact` varchar(1) NOT NULL
	);";
	$res_create=mysql_query($sql);updateOnline($sql);

	$sql="CREATE TABLE IF NOT EXISTS `resp_adr` (
	`adr_id` varchar(10) NOT NULL,
	`adr1` varchar(100) NOT NULL,
	`adr2` varchar(100) NOT NULL,
	`adr3` varchar(100) NOT NULL,
	`adr4` varchar(100) NOT NULL,
	`cp` varchar(6) NOT NULL,
	`pays` varchar(50) NOT NULL,
	`commune` varchar(50) NOT NULL,
	PRIMARY KEY  (`adr_id`)
	);";
	$res_create=mysql_query($sql);updateOnline($sql);

	$sql="CREATE TABLE IF NOT EXISTS `resp_pers` (
	`pers_id` varchar(10) NOT NULL,
	`login` varchar(50) NOT NULL,
	`nom` varchar(30) NOT NULL,
	`prenom` varchar(30) NOT NULL,
	`civilite` varchar(5) NOT NULL,
	`tel_pers` varchar(255) NOT NULL,
	`tel_port` varchar(255) NOT NULL,
	`tel_prof` varchar(255) NOT NULL,
	`mel` varchar(100) NOT NULL,
	`adr_id` varchar(10) NOT NULL,
	PRIMARY KEY  (`pers_id`)
	);";
	$res_create=mysql_query($sql);updateOnline($sql);


	if(!isset($mode)){
		echo "<p>Vous pouvez effectuer la mise à jour des tables:</p>\n";
		echo "<ul>";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=1'>avec SCONET</a>: In this case, it is necessary to provide generated files CSV <a href='../init_xml/lecture_xml_sconet.php?ad_retour=".$_SERVER['PHP_SELF']."'>here</a> since files XML extracted from SCONET.<br />\nIf you carry out this choice, you will be able to thereafter carry out
new imports to insert the student/responsible arrived in the course of year.<br />\nThis choice implies that field ELENOET of the table 'eleves' that is to say correctly filled with values corresponding to those of
old F_ELE.DBF<br />\nContents of the table 'responsables' is ignored and the new responsible tables are filled out according to
the provided CSV.</li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=2'>sans SCONET</a>: In this case, one makes only the conversion of the tables.<br />\nYou will have in this case to manage the future news inscriptions with
the hand (<i>or by imports CSV</i>).<br />\nHere, it is connection ERENO of your tables 'eleves' and 'responsables' who is used to ensure the migration towards the new tables.<br />\nThis mode does not allow updates in the course of year.</li>\n";
		echo "</ul>";

		$sql="SELECT * FROM eleves WHERE elenoet=''";
		$res1=mysql_query($sql);
		if(mysql_num_rows($res1)>0){
			echo "<p>The following pupils do not have them ELENOET informed.<br />They will thus not be identified/associated thereafter with the student
registered in Sconet.<br />If you consider the mode with Sconet (<i>recommended when it is possible</i>), you should start by seeking them ELENOET lacks and to inform them in the Management of the student.</p>\n";
			echo "<table border='1'>\n";
			echo "<tr>\n";
			echo "<td style='font-weight:bold; text-align:center;'>Login</td>\n";
			echo "<td style='font-weight:bold; text-align:center;'>Name</td>\n";
			echo "<td style='font-weight:bold; text-align:center;'>First name</td>\n";
			echo "<td style='font-weight:bold; text-align:center;'>Birth</td>\n";
			echo "</tr>\n";
			while($lig1=mysql_fetch_object($res1)){
				echo "<tr>\n";
				echo "<td><a href='../eleves/modify_eleve.php?eleve_login=$lig1->login' target='_blank'>$lig1->login</a></td>\n";
				echo "<td>$lig1->nom</td>\n";
				echo "<td>$lig1->prenom</td>\n";
				echo "<td>$lig1->naissance</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}

	}
	elseif($mode==2){

		if(!isset($confirmer)){
			echo "<p><b>ATTENTION:</b> The mode without SCONET does not allow updates in the course of year.<br />\nThat means that the corrections carried out on your software of
management of the pupils and responsible (<i>changes of addresses, corrections? </i >) could not be automatically
imported in GEPI.<br />You will have thus a double-seizure to carry out to manage these
updates.<br />\nMode with SCONET, he, would make it possible to import the corrections in the course of
year.</p>\n";
			echo "<p>This choice is irreversible.<br />\n Are you sure that you do not wish to use the importation with SCONET?</p>\n";
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=2&amp;confirmer=oui".add_token_in_url()."'>YES</a> or <a href='".$_SERVER['PHP_SELF']."'>NOT</a></p>\n";
		}
		else{
			check_token(false);

			$erreur=0;
			$sql="SELECT * FROM eleves ORDER BY nom,prenom";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)>0){
				// On vide les tables avant traitement (au cas où il aurait fallu s'y prendre à deux fois)
				$sql="TRUNCATE TABLE resp_adr";
				$res_truncate=mysql_query($sql);
				$sql="TRUNCATE TABLE resp_pers";
				$res_truncate=mysql_query($sql);
				$sql="TRUNCATE TABLE responsables2";
				$res_truncate=mysql_query($sql);

				while($lig1=mysql_fetch_object($res1)){
					//if($lig1->ele_id==''){
					unset($ele_id);
					if(!isset($lig1->ele_id)){
						$ele_id="";
					}
					else{
						$ele_id=$lig1->ele_id;
					}

					if($ele_id==''){
						//echo "<p>On va générer un ele_id pour $lig1->nom $lig1->prenom ($lig1->elenoet)<br />\n";
						// Recherche du plus grand ele_id:
						$sql="SELECT ele_id FROM eleves WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
						//echo "$sql<br />\n";
						$res_ele_id_eleve=mysql_query($sql);
						if(mysql_num_rows($res_ele_id_eleve)>0){
							$tmp=0;
							$lig_ele_id_eleve=mysql_fetch_object($res_ele_id_eleve);
							$tmp=substr($lig_ele_id_eleve->ele_id,1);
							$tmp++;
							$max_ele_id=$tmp;
						}
						else{
							$max_ele_id=1;
						}

						$sql="SELECT ele_id FROM responsables2 WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
						//echo "$sql<br />\n";
						$res_ele_id_responsables2=mysql_query($sql);
						if(mysql_num_rows($res_ele_id_responsables2)>0){
							$tmp=0;
							$lig_ele_id_responsables2=mysql_fetch_object($res_ele_id_responsables2);
							$tmp=substr($lig_ele_id_responsables2->ele_id,1);
							$tmp++;
							$max_ele_id2=$tmp;
						}
						else{
							$max_ele_id2=1;
						}

						$tmp=max($max_ele_id,$max_ele_id2);
						$ele_id="e".sprintf("%09d",max($max_ele_id,$max_ele_id2));

						//$sql="UPDATE eleves SET ele_id='$ele_id' WHERE elenoet='$lig1->elenoet'";
						$sql="UPDATE eleves SET ele_id='$ele_id' WHERE login='$lig1->login'";
						//echo "$sql<br />\n";
						$res_update=mysql_query($sql);updateOnline($sql);
						if(!$res_update){
							//echo "<font color='red'>Erreur</font> lors de la définition de l'ele_id $ele_id pour $lig1->nom $lig1->prenom ($lig1->elenoet).<br />\n";
							echo "<font color='red'>Error</font> at the time of the definition of l'ele_id $ele_id pour $lig1->nom $lig1->prenom ($lig1->login).<br />\n";
							$erreur++;
						}
						else{
							//echo "<p>Définition de l'ele_id $ele_id pour $lig1->nom $lig1->prenom ($lig1->elenoet).<br />\n";
							echo "<p>Definition of l'ele_id $ele_id pour $lig1->nom $lig1->prenom ($lig1->login).<br />\n";
						}
					}
					else{
						//echo "<p>$lig1->nom $lig1->prenom ($lig1->elenoet) dispose déjà d'un ele_id<br />\n";
						$ele_id=$lig1->ele_id;
					}
					/*
					echo "<p>nom=$lig1->nom<br />\n";
					echo "prenom=$lig1->prenom<br />\n";
					echo "elenoet=$lig1->elenoet<br />\n";
					echo "ereno=$lig1->ereno<br />\n";
					echo "ele_id=$ele_id</p>\n";
					*/

					if($lig1->ereno!=''){
						$sql="SELECT * FROM responsables WHERE ereno='$lig1->ereno'";
						//echo "$sql<br />\n";
						$res2=mysql_query($sql);
						if(mysql_num_rows($res2)>0){
							while($lig2=mysql_fetch_object($res2)){
								// Est-ce que cet ereno a déjà fait l'objet d'une insertion dans les nouvelles tables?
								// Recherche des pers_id ou recherche du plus grand pers_id affecté.

								$sql="SELECT r2.* FROM responsables2 r2, responsables r, eleves e WHERE r2.ele_id=e.ele_id AND r.ereno=e.ereno AND e.ereno='$lig1->ereno'";
								//echo "$sql<br />\n";
								$test=mysql_query($sql);
								if(mysql_num_rows($test)>0){
									// Le couple de responsables correspondant à $lig1->ereno est déjà dans les nouvelles tables.
									while($ligtmp=mysql_fetch_object($test)){
										//$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$ligtmp->pers_id' AND ele_id='$lig1->ele_id'";
										$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$ligtmp->pers_id' AND ele_id='$ele_id'";
										//echo "$sql<br />\n";
										$test2=mysql_query($sql);
										if(mysql_num_rows($test2)==0){
											// L'élève courant n'est pas encore inscrit...
											//$sql="INSERT INTO responsables2 SET ele_id='$lig1->ele_id', pers_id='$ligtmp->pers_id', resp_legal='$ligtmp->resp_legal', pers_contact='$ligtmp->pers_contact'";
											$sql="INSERT INTO responsables2 SET ele_id='$ele_id', pers_id='$ligtmp->pers_id', resp_legal='$ligtmp->resp_legal', pers_contact='$ligtmp->pers_contact'";
											//echo "$sql<br />\n";
											$res_insert=mysql_query($sql);updateOnline($sql);
											if(!$res_insert){
												echo "<font color='red'>Error</font> during the insertion of association with the responsible one ($ligtmp->resp_legal) $ligtmp->pers_id<br />\n";
												$erreur++;
											}
											else{
												echo "Insertion of association with the responsible one ($ligtmp->resp_legal) $ligtmp->pers_id <br />\n";
											}
										}
									}
								}
								else{
									// Le couple n'a pas encore été inscrit dans les nouvelles tables.

									// Recherche du plus grand pers_id:
									$sql="SELECT pers_id FROM resp_pers WHERE pers_id LIKE 'p%' ORDER BY pers_id DESC";
									//echo "$sql<br />\n";
									$restmp=mysql_query($sql);
									if(mysql_num_rows($restmp)==0){
										$nb1=1;
									}
									else{
										$ligtmp=mysql_fetch_object($restmp);
										$nb1=substr($ligtmp->pers_id,1);
										$nb1++;
									}
									$pers_id="p".sprintf("%09d",$nb1);

									// Recherche du plus grand adr_id:
									$sql="SELECT adr_id FROM resp_adr WHERE adr_id LIKE 'a%' ORDER BY adr_id DESC";
									//echo "$sql<br />\n";
									$restmp=mysql_query($sql);
									if(mysql_num_rows($restmp)==0){
										$nb2=1;
									}
									else{
										$ligtmp=mysql_fetch_object($restmp);
										$nb2=substr($ligtmp->adr_id,1);
										$nb2++;
									}
									$adr_id="a".sprintf("%09d",$nb2);




									if($lig2->nom1!=''){
										$sql="INSERT INTO responsables2 SET pers_id='$pers_id', ele_id='$ele_id', resp_legal='1', pers_contact='1'";
										//echo "$sql<br />\n";
										$res_insert1=mysql_query($sql);updateOnline($sql);
										if(!$res_insert1){
											echo "<font color='red'>Erreur</font> during the insertion of the association of the pupil $ele_id with the responsible one (1) $pers_id<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion of the association of the student $ele_id avec le responsable (1) $pers_id<br />\n";
										}

										//$sql="INSERT INTO resp_pers SET pers_id='$pers_id', nom='$lig2->nom1',prenom='$lig2->prenom1',adr_id='$adr_id'";
										$sql="INSERT INTO resp_pers SET pers_id='$pers_id', nom='".addslashes($lig2->nom1)."',prenom='".addslashes($lig2->prenom1)."',adr_id='$adr_id'";
										//echo "$sql<br />\n";
										$res_insert2=mysql_query($sql);updateOnline($sql);
										if(!$res_insert2){
											echo "<font color='red'>Error</font> during the insertion of the responsible one ($pers_id): $lig2->nom1 $lig2->prenom1 (with the n° addresses $adr_id).<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion of the responsible one ($pers_id): $lig2->nom1 $lig2->prenom1 (with the n° addresses $adr_id).<br />\n";
										}

										//$sql="INSERT INTO resp_adr SET adr1='$lig2->adr1',adr2='$lig2->adr1_comp',cp='$lig2->cp1',commune='$lig2->commune1',adr_id='$adr_id'";
										$sql="INSERT INTO resp_adr SET adr1='".addslashes($lig2->adr1)."',adr2='".addslashes($lig2->adr1_comp)."',cp='$lig2->cp1',commune='".addslashes($lig2->commune1)."',adr_id='$adr_id'";
										//echo "$sql<br />\n";
										$res_insert3=mysql_query($sql);updateOnline($sql);
										if(!$res_insert3){
											echo "<font color='red'>Error</font> during the insertion of the address $lig2->adr1, $lig2->adr1_comp, $lig2->cp1, $lig2->commune1 with the n° addresses $adr_id.<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion of the address $lig2->adr1, $lig2->adr1_comp, $lig2->cp1, $lig2->commune1 with the n° addresses $adr_id.<br />\n";
										}
									}


									if($lig2->nom2!=''){
										// Pour le deuxième responsable:
										$nb1++;
										$pers_id="p".sprintf("%09d",$nb1);

										if(($lig2->adr2!=$lig2->adr1)||($lig2->adr2_comp!=$lig2->adr1_comp)||($lig2->cp2!=$lig2->cp1)||($lig2->commune2!=$lig2->commune1)){
											if(($lig2->adr2!='')||($lig2->adr2_comp!='')||($lig2->cp2!='')||($lig2->commune2!='')){
												$nb2++;
												$adr_id="a".sprintf("%09d",$nb2);

												echo "The second responsible one does not have the same address.<br />\n";

												//$sql="INSERT INTO resp_adr SET adr1='$lig2->adr2',adr2='$lig2->adr2_comp',cp='$lig2->cp2',commune='$lig2->commune2',adr_id='$adr_id'";
												$sql="INSERT INTO resp_adr SET adr1='".addslashes($lig2->adr2)."',adr2='".addslashes($lig2->adr2_comp)."',cp='$lig2->cp2',commune='".addslashes($lig2->commune2)."',adr_id='$adr_id'";
												//echo "$sql<br />\n";
												$res_insert3=mysql_query($sql);
												if(!$res_insert3){
													echo "<font color='red'>Error</font> during the insertion of the address $lig2->adr2, $lig2->adr2_comp, $lig2->cp2, $lig2->commune2 with the n° addresses $adr_id.<br />\n";
													$erreur++;
												}
												else{
													echo "Insertion of the address $lig2->adr2, $lig2->adr2_comp, $lig2->cp2, $lig2->commune2 with the n° addresses $adr_id.<br />\n";
												}
											}
										}

										$sql="INSERT INTO responsables2 SET pers_id='$pers_id', ele_id='$ele_id', resp_legal='2', pers_contact='1'";
										//echo "$sql<br />\n";
										$res_insert1=mysql_query($sql);updateOnline($sql);
										if(!$res_insert1){
											echo "<font color='red'>Error</font> insertion of the association of the student $ele_id with the responsible one (2) $pers_id<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion of the association of the student $ele_id with the responsible one (2) $pers_id<br />\n";
										}

										//$sql="INSERT INTO resp_pers SET pers_id='$pers_id', nom='$lig2->nom2',prenom='$lig2->prenom2',adr_id='$adr_id'";
										$sql="INSERT INTO resp_pers SET pers_id='$pers_id', nom='".addslashes($lig2->nom2)."',prenom='".addslashes($lig2->prenom2)."',adr_id='$adr_id'";
										//echo "$sql<br />\n";
										$res_insert2=mysql_query($sql);updateOnline($sql);
										if(!$res_insert2){
											echo "<font color='red'>Error</font> insertion of the responsible one ($pers_id): $lig2->nom2 $lig2->prenom2 (with the n° addresses $adr_id).<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion of the responsible one ($pers_id): $lig2->nom2 $lig2->prenom2 (with the n° addresses $adr_id).<br />\n";
										}
									}
								}
							}
						}
					}
				}

				if($erreur==0){
					echo "<p>The operation correctly proceeded.</p>\n";
					echo "<center><p><a href='../accueil.php'>To turn over to the reception</a></p></center>\n";

					// On renseigne le témoin de mise à jour effectuée:
					saveSetting("conv_new_resp_table", 1);
					saveSetting("import_maj_xml_sconet", 0);
				}
				else{
					echo "<p>Errors occurred.</p>\n";
				}
			}
			else{
				echo "<p>It seems that the table 'eleves' is empty.</p>\n";
			}
		}
	}
	elseif($mode==1) {
		// On fournit les fichiers CSV générés depuis les XML de SCONET...
		if (!isset($is_posted)) {
			echo "<p>You will import the files <b>CSV</b> (<i><a href='../init_xml/lecture_xml_sconet.php?ad_retour=".$_SERVER['PHP_SELF']."'>generated</a> starting from exports XML of Sconet</i>).</p>\n";
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method=post>\n";
			echo add_token_field();
			echo "<input type=hidden name='is_posted' value='yes' />\n";
			echo "<input type=hidden name='mode' value='1' />\n";
			echo "<p>Select the file <b>ELEVES.CSV</b>:<br /><input type=\"file\" size=\"80\" name=\"ele_file\" /></p>\n";
			echo "<p>And files the responsible ones:</p>\n";
			echo "<p>Select the file <b>PERSONNES.CSV</b>:<br /><input type='file' size='80' name='pers_file' />\n";
			echo "<p>Select the file <b>RESPONSABLES.CSV</b>:<br /><input type='file' size='80' name='resp_file' />\n";
			echo "<p>Select the file <b>ADRESSES.CSV</b>:<br /><input type='file' size='80' name='adr_file' />\n";
			echo "<p><input type=submit value='Valider' /></p>\n";
			echo "</form>\n";
		}
		else {
			check_token();

			unset($tab_elenoet_non_trouves);
			$tab_elenoet_non_trouves=array();

			$csv_file = isset($_FILES["ele_file"]) ? $_FILES["ele_file"] : NULL;
			if(strtoupper($csv_file['name']) == "ELEVES.CSV"){
				//$fp = dbase_open($csv_file['tmp_name'], 0);
				$fp=fopen($csv_file['tmp_name'],"r");

				if(!$fp){
					echo "<p>Impossible to open the file ELEVES.CSV !</p>";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Click here </a> Start again !</center></p>\n";
				}
				else{
					echo "<p>Initially, the new field is informed 'ele_id' in the table 'eleves'\n";
					if(getSettingValue('auth_sso')=="lcs") {
						echo " and the data student are updated (mode, doubling, national identifier, establishment of origin)";

						// Par sécurité, on vide la table j_eleves_etablissements (établissement d'origine)
						mysql_query("TRUNCATE TABLE j_eleves_etablissements");
					}
					echo ".</p>";

					echo "<div id='div_eleves' style='display:none;'>\n";

					// Traitement particulier LCS : on met à jour l'identifiant national dans eleves (np_gep)
					if(getSettingValue('auth_sso')=="lcs") {
						$tabchamps = array("ELENOET","ELE_ID","ELENONAT","ELEDOUBL","ELEREG","ETOCOD_EP");
					}
					else {
						$tabchamps = array("ELENOET","ELE_ID");
					}
					$erreur=0;

					$nblignes=0;
					while(!feof($fp)) {
						$ligne=fgets($fp, 4096);
						if($nblignes==0){
							// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
							// On ne retient pas ces ajouts pour $en_tete
							$temp=explode(";",$ligne);
							for($i=0;$i<sizeof($temp);$i++){
								$temp2=explode(",",$temp[$i]);
								$en_tete[$i]=$temp2[0];
							}

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					unset($tabindice);
					/*
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
								//echo "Champ $tabchamps[$k] trouvé à l'indice $i<br />";
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

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					for($k = 1; ($k < $nblignes+1); $k++){
						if(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								$affiche=array();
								for($i = 0; $i < count($tabchamps); $i++) {
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
									//echo "A l'indice $i, on a \$tabligne[\$tabindice[$i]]=\$tabligne[$tabindice[$i]]=".$tabligne[$tabindice[$i]]."<br />";
								}

								//$affiche[0]=sprintf("%05d",$affiche[0]);
								//if(strlen($affiche[0])){
								//}

								//$sql="SELECT * FROM eleves WHERE elenoet='$affiche[0]'";
								$sql="SELECT * FROM eleves WHERE elenoet='$affiche[0]' OR elenoet='".sprintf("%05d",$affiche[0])."'";
								//echo "$sql<br />\n";
								$res1=mysql_query($sql);
								if(mysql_num_rows($res1)>0) {
									//$sql="UPDATE eleves SET ele_id='$affiche[1]' WHERE elenoet='$affiche[0]'";
									$sql="UPDATE eleves SET ele_id='$affiche[1]' WHERE elenoet='$affiche[0]' OR elenoet='".sprintf("%05d",$affiche[0])."'";
									//echo "$sql<br />\n";
									$res_update=mysql_query($sql);
									if(!$res_update){
										$erreur++;
										echo "<font color='red'>Error</font> at the time of the information of l'ele_id with the value $affiche[1] for student d'ELENOET $affiche[0]<br />\n";
									}
									else{
										echo "Information of l'ele_id with the value $affiche[1] for student d'ELENOET $affiche[0]<br />\n";
									}

	  								// Traitement particulier LCS : on met à jour l'identifiant national dans eleves (np_gep)
									if(getSettingValue('auth_sso')=="lcs") {
										// Récupération du login
										$eleve_login = sql_query1("select login from eleves where elenoet='$affiche[0]' OR elenoet='".sprintf("%05d",$affiche[0])."'");
										// Mise à jour de l'identifiant national
										if ($affiche[2]=='') {
       										echo "<font color='red'>Error</font> The national identifier for the student d'ELENOET $affiche[0] ($eleve_login) was not recorded because it misses file eleves.csv.<br />\n";
										}
										else {
											$sql="UPDATE eleves SET no_gep='$affiche[2]' WHERE elenoet='$affiche[0]' OR elenoet='".sprintf("%05d",$affiche[0])."'";
											$res_update=mysql_query($sql);updateOnline($sql);
											if(!$res_update){
													echo "<font color='red'>Erreur</font> at the time of the information of the national identifier the value $affiche[2] for the student d'ELENOET $affiche[0] ($eleve_login)<br />\n";
											}
											else {
												echo "Information of the national identifier with the value $affiche[2] for the student d'ELENOET $affiche[0] ($eleve_login)<br />\n";
											}
										}
										// mise à jour du champ Doublant et du champ regime
										if ($affiche[3]=='N') {
											$doublant = "-";
										}
										else {
											$doublant = "R";
										}

										if ($affiche[4]=='3') {
											$regime = "d/p";
										}
										else if ($affiche[4]=='2') {
											$regime = "int.";
										}
										else if ($affiche[4]=='1') {
											$regime = "i-e";
										}
										else {
											$regime = "ext.";
										}

										$res = mysql_query("update j_eleves_regime SET regime='".$regime."', doublant = '".$doublant."' where login ='".$eleve_login."'");
									updateOnline("update j_eleves_regime SET regime='".$regime."', doublant = '".$doublant."' where login ='".$eleve_login."'");
									// Etablissement d'origine
										$sql="insert into j_eleves_etablissements SET id_etablissement='$affiche[5]', id_eleve='".sprintf("%05d",$affiche[0])."'";
     									$res_insert=mysql_query($sql);updateOnline($sql);
									}
								}
								else {
									echo "<font color='red'>No student with l'ELENOET $affiche[0] was not found in your table 'eleves'; it could be created at the time of a later importation.</font><br />\n";
									$tab_elenoet_non_trouves[]=$affiche[0];
								}
							}
						}
					}
					fclose($fp);

					echo "</div>\n";

					echo "<p><a href=\"#\" onClick=\"document.getElementById('div_eleves').style.display='';return false;\">Display</a> / <a href=\"#\" onClick=\"document.getElementById('div_eleves').style.display='none';return false;\">Masquer</a> details.</p>\n";
					echo "<p><br /></p>\n";

				}
			}

			// Et la partie responsables:
			// C'est la copie de la page /init_xml/responsables.php
			$nb_reg_no1=-1;
			$nb_reg_no2=-1;
			$nb_reg_no3=-1;

			$csv_file = isset($_FILES["pers_file"]) ? $_FILES["pers_file"] : NULL;
			//echo strtoupper($csv_file['name'])."<br />";
			if(strtoupper($csv_file['name']) == "PERSONNES.CSV") {
				$fp=fopen($csv_file['tmp_name'],"r");
				if(!$fp){
					echo "<p>Impossible to open the file PERSONNES.CSV.</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> to start again !</center></p>\n";
				}
				else{
					echo "<p>Reading of PERSONNES.CSV to inform the new table 'resp_pers' with the name, first name, telephone... of the responsible ones.</p>";

					echo "<div id='div_personnes' style='display:none;'>\n";

					// On vide la table avant traitement (au cas où il aurait fallu s'y prendre à deux fois)
					$sql="TRUNCATE TABLE resp_pers";
					$res_truncate=mysql_query($sql);

					// on constitue le tableau des champs à extraire
					//$tabchamps=array("pers_id","nom","prenom","tel_pers","tel_port","tel_prof","mel","adr_id");
					$tabchamps=array("pers_id","nom","prenom","civilite","tel_pers","tel_port","tel_prof","mel","adr_id");

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

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					unset($tabindice);
					/*
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
								//echo "Champ $tabchamps[$k] trouvé à l'indice $i<br />";
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

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					//=========================
					$nb_reg_no3=0;
					$nb_record3=0;
					for($k = 1; ($k < $nblignes+1); $k++){
						//$ligne = dbase_get_record($fp,$k);
						if(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								for($i = 0; $i < count($tabchamps); $i++) {
									//$ind = $tabindice[$i];
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
									//echo "A l'indice $i, on a \$tabligne[\$tabindice[$i]]=\$tabligne[$tabindice[$i]]=".$tabligne[$tabindice[$i]]."<br />";
								}
								$sql="insert into resp_pers set
											pers_id = '$affiche[0]',
											nom = '$affiche[1]',
											prenom = '$affiche[2]',
											civilite = '".ucfirst(strtolower($affiche[3]))."',
											tel_pers = '$affiche[4]',
											tel_port = '$affiche[5]',
											tel_prof = '$affiche[6]',
											mel = '$affiche[7]',
											adr_id = '$affiche[8]'
											";
								//echo "$sql<br />\n";
								$req = mysql_query($sql);updateOnline($sql);
								if(!$req) {
									$nb_reg_no3++;
									echo mysql_error();
									echo "<font color='red'>Error</font> during the insertion of the responsible one ($affiche[0]) $affiche[1] $affiche[2] with the phone numbers $affiche[4], $affiche[5], $affiche[6], the mel $affiche[7] and the number of address $affiche[8].<br />\n";
								} else {
									$nb_record3++;
									echo "Insertion of the responsible one ($affiche[0]) $affiche[1] $affiche[2] with the phone numbers $affiche[4], $affiche[5], $affiche[6], the mel $affiche[7] and the number of address $affiche[8].<br />\n";
								}
							}
						}
					}
					//dbase_close($fp);
					fclose($fp);

					echo "</div>\n";

					if ($nb_reg_no3 != 0) {
						echo "<p>During the recording of the data of PERSONNES.CSV, there was $nb_reg_no3 erreurs. Test find the cause of the error.</p>\n";
					} else {
						echo "<p>The importation of the people (responsible) in base GEPI was carried out successfully (".$nb_record3." recordings on the whole).</p>\n";
					}

					echo "<p><a href=\"#\" onClick=\"document.getElementById('div_personnes').style.display='';return false;\">Display</a> / <a href=\"#\" onClick=\"document.getElementById('div_personnes').style.display='none';return false;\">Masquer</a> details.</p>\n";
					echo "<p><br /></p>\n";

				}
			} else if (trim($csv_file['name'])=='') {
				echo "<p>No file PERSONNES.CSV was not selected !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> to start again !</center></p>\n";

			} else {
				echo "<p>The selected file is not valid !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> to start again !</center></p>\n";
			}




			$csv_file = isset($_FILES["resp_file"]) ? $_FILES["resp_file"] : NULL;
			//echo strtoupper($csv_file['name'])."<br />";
			if(strtoupper($csv_file['name']) == "RESPONSABLES.CSV") {
				$fp=fopen($csv_file['tmp_name'],"r");
				if(!$fp){
					echo "<p>Impossible to open the file RESPONSABLES.CSV.</p>";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Click here </a> to start again !</center></p>";
				}
				else{
					echo "<p>Reading of the file RESPONSABLES.CSV to inform associations student/responsible.</p>\n";

					echo "<div id='div_responsables' style='display:none;'>\n";

					// On vide la table avant traitement (au cas où il aurait fallu s'y prendre à deux fois)
					$sql="TRUNCATE TABLE responsables2";
					$res_truncate=mysql_query($sql);

					// on constitue le tableau des champs à extraire
					$tabchamps=array("ele_id","pers_id","resp_legal","pers_contact");

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

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					unset($tabindice);
					/*
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
								//echo "Champ $tabchamps[$k] trouvé à l'indice $i<br />";
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

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					//=========================
					$nb_reg_no1=0;
					$nb_record1=0;
					for($k = 1; ($k < $nblignes+1); $k++){
						//$ligne = dbase_get_record($fp,$k);
						if(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								for($i = 0; $i < count($tabchamps); $i++) {
									//$ind = $tabindice[$i];
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
								}
								$sql="insert into responsables2 set
											ele_id = '$affiche[0]',
											pers_id = '$affiche[1]',
											resp_legal = '$affiche[2]',
											pers_contact = '$affiche[3]'
											";
								//echo "$sql<br />\n";
								$req = mysql_query($sql);updateOnline($sql);
								if(!$req) {
									$nb_reg_no1++;
									echo mysql_error();
									echo "<font color='red'>Erreur</font> during the insertion of association student $affiche[0] and responsible ($affiche[2]) $affiche[1].<br />\n";
								} else {
									$nb_record1++;
									echo "Insertion of association student $affiche[0] and responsible ($affiche[2]) $affiche[1].<br />\n";
								}
							}
						}
					}
					//dbase_close($fp);
					fclose($fp);

					echo "</div>\n";

					if ($nb_reg_no1 != 0) {
						echo "<p>During the recording of the data of RESPONSABLES.CSV, there was $nb_reg_no1 erreurs. Test find the cause of the error.</p>\n";
					}
					else {
						echo "<p>The importation of the relations student/responsible in base GEPI was carried out successfully (".$nb_record1." recordings on the whole).</p>\n";
					}

					echo "<p><a href=\"#\" onClick=\"document.getElementById('div_responsables').style.display='';return false;\">Display </a> / <a href=\"#\" onClick=\"document.getElementById('div_responsables').style.display='none';return false;\">Mask</a> details.</p>\n";
					echo "<p><br /></p>\n";

				}
			} else if (trim($csv_file['name'])=='') {
				echo "<p>No file RESPONSABLES.CSV was not selected !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> Start again !</center></p>\n";

			} else {
				echo "<p>Selected file is not valid !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> to start again !</center></p>\n";
			}



			$csv_file = isset($_FILES["adr_file"]) ? $_FILES["adr_file"] : NULL;
			//echo strtoupper($csv_file['name'])."<br />";
			if(strtoupper($csv_file['name']) == "ADRESSES.CSV") {
				$fp=fopen($csv_file['tmp_name'],"r");
				if(!$fp){
					echo "<p>Impossible to open the file ADRESSES.CSV.</p>";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Click here </a> Start again !</center></p>";
				}
				else{
					echo "<p>Reading of the file ADRESSES.CSV</p>";

					echo "<div id='div_adresses' style='display:none;'>\n";

					// On vide la table avant traitement (au cas où il aurait fallu s'y prendre à deux fois)
					$sql="TRUNCATE TABLE resp_adr";
					$res_truncate=mysql_query($sql);

					// on constitue le tableau des champs à extraire
					$tabchamps=array("adr_id","adr1","adr2","adr3","adr4","cp","pays","commune");

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

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					unset($tabindice);
					/*
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
								//echo "Champ $tabchamps[$k] trouvé à l'indice $i<br />";
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

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					//=========================
					$nb_reg_no2=0;
					$nb_record2=0;
					for($k = 1; ($k < $nblignes+1); $k++){
						//$ligne = dbase_get_record($fp,$k);
						if(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								for($i = 0; $i < count($tabchamps); $i++) {
									//$ind = $tabindice[$i];
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
								}
								$sql="insert into resp_adr set
											adr_id = '$affiche[0]',
											adr1 = '$affiche[1]',
											adr2 = '$affiche[2]',
											adr3 = '$affiche[3]',
											adr4 = '$affiche[4]',
											cp = '$affiche[5]',
											pays = '$affiche[6]',
											commune = '$affiche[7]'
											";
								//echo "$sql<br />\n";
								$req = mysql_query($sql);updateOnline($sql);
								if(!$req) {
									$nb_reg_no2++;
									echo mysql_error();
									echo "<font color='red'>Error</font> during the insertion of the address $affiche[1], $affiche[2], $affiche[3], $affiche[4], $affiche[5], $affiche[7], ($affiche[6]), with the number $affiche[0].<br />\n";
								} else {
									$nb_record2++;
									echo "Insertion of the address $affiche[1], $affiche[2], $affiche[3], $affiche[4], $affiche[5], $affiche[7], ($affiche[6]), with the number $affiche[0].<br />\n";
								}
							}
						}
					}
					//dbase_close($fp);
					fclose($fp);

					echo "</div>\n";

					if ($nb_reg_no2 != 0) {
						echo "<p>During the recording of the data of ADRESSES.CSV, there was $nb_reg_no2 error. Test find the cause of the error.</p>\n";
					} else {
						echo "<p>The importation of the addresses the responsible ones in base GEPI was
carried out successfully (".$nb_record2." recordings on the whole).</p>\n";
					}

					echo "<p><a href=\"#\" onClick=\"document.getElementById('div_adresses').style.display='';return false;\">Display</a> / <a href=\"#\" onClick=\"document.getElementById('div_adresses').style.display='none';return false;\">Mask</a> details.</p>\n";
					echo "<p><br /></p>\n";

					if(count($tab_elenoet_non_trouves)>0){
						echo "<h2>CAUTION</h2>\n";
						echo "<p>The file 'eleves.csv' provided contained ELENOET student not present in the table 'eleves' of your base GEPI.<br />These new student registered in Sconet were not created.<br />Only the conversion of the existing data was carried out.<br />You will be able to proceed to new <a href='maj_import.php'>importation by update</a> Create these student.</p>\n";
						echo "<p><br /></p>\n";
						echo "<p>Summary of ELENOET not found in your table 'eleves':<br />\n";
						echo "$tab_elenoet_non_trouves[0]";
						for($i=1;$i<count($tab_elenoet_non_trouves);$i++){
							echo ", $tab_elenoet_non_trouves[$i]";
						}
						echo "</p>\n";
					}

				}
			} else if (trim($csv_file['name'])=='') {
				echo "<p>No file ADRESSES.CSV was not selected !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> Start again !</center></p>\n";

			} else {
				echo "<p>The selected file is not valid !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Click here </a> Start again !</center></p>\n";
			}


			if(($nb_reg_no1==0)&&($nb_reg_no2==0)&&($nb_reg_no3==0)&&($erreur==0)){
				echo "<p>The operation correctly proceeded.</p>\n";
				echo "<center><p><a href='../accueil.php'>Turn over to the reception</a></p></center>\n";

				// On renseigne le témoin de mise à jour effectuée:
				saveSetting("conv_new_resp_table", 1);
				saveSetting("import_maj_xml_sconet", 1);
			}
			else{
				echo "<p>Errors occurred.</p>\n";
			}
		}
	}
}
require("../lib/footer.inc.php");
?>
