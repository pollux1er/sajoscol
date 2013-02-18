<?php
/*
 * $Id: import_communes.php 6746 2011-04-04 05:59:18Z crob $
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

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// INSERT INTO `droits` VALUES ('/eleves/import_communes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Import des communes de naissance', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";

/*
function extr_valeur($lig) {
	unset($tabtmp);
	$tabtmp=explode(">",my_ereg_replace("<",">",$lig));
	return trim($tabtmp[2]);
}

function ouinon($nombre) {
	if($nombre==1) {return "O";}elseif($nombre==0) {return "N";}else {return "";}
}
function sexeMF($nombre) {
	//if($nombre==2) {return "F";}else {return "M";}
	if($nombre==2) {return "F";}elseif($nombre==1) {return "M";}else {return "";}
}
*/

function affiche_debug($texte) {
	// Passer à 1 la variable pour générer l'affichage des infos de debug...
	$debug=0;
	if($debug==1) {
		echo "<font color='green'>".$texte."</font>";
		flush();
	}
}

// Initialisation du répertoire actuel de sauvegarde
$dirname = getSettingValue("backup_directory");

function info_debug($texte) {
	global $step;
	global $dirname;

	$debug=0;
	if($debug==1) {
		//$fich_debug=fopen("/tmp/debug_maj_import2.txt","a+");
		$fich_debug=fopen("../backup/".$dirname."/debug_import_communes.txt","a+");
		fwrite($fich_debug,"$step;$texte;".time()."\n");
		fclose($fich_debug);
	}
}
/*
function maj_ini_prenom($prenom) {
	$prenom2="";
	$tab1=explode("-",$prenom);
	for($i=0;$i<count($tab1);$i++) {
		if($i>0) {
			$prenom2.="-";
		}
		$tab2=explode(" ",$tab1[$i]);
		for($j=0;$j<count($tab2);$j++) {
			if($j>0) {
				$prenom2.=" ";
			}
			$prenom2.=ucfirst(strtolower($tab2[$j]));
		}
	}
	return $prenom2;
}
*/

/*
function get_commune($code_commune_insee,$mode) {
	$retour="";

	$sql="SELECT * FROM communes WHERE code_commune_insee='$code_commune_insee';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		if($mode==0) {
			$retour=$lig->commune;
		}
		else {
			$retour=$lig->commune." (<i>".$lig->departement."</i>)";
		}
	}
	return $retour;
}
*/

// Etape...
$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

$parcours_diff=isset($_POST['parcours_diff']) ? $_POST['parcours_diff'] : NULL;

//$tab_ele_id=isset($_POST['tab_ele_id']) ? $_POST['tab_ele_id'] : NULL;
//$tab_ele_id_diff=isset($_POST['tab_ele_id_diff']) ? $_POST['tab_ele_id_diff'] : NULL;

$nb_parcours=isset($_POST['nb_parcours']) ? $_POST['nb_parcours'] : NULL;

/*
$tab_pers_id=isset($_POST['tab_pers_id']) ? $_POST['tab_pers_id'] : NULL;
$tab_pers_id_diff=isset($_POST['tab_pers_id_diff']) ? $_POST['tab_pers_id_diff'] : NULL;

$total_pers_diff=isset($_POST['total_pers_diff']) ? $_POST['total_pers_diff'] : NULL;

$valid_pers_id=isset($_POST['valid_pers_id']) ? $_POST['valid_pers_id'] : NULL;
$liste_pers_id=isset($_POST['liste_pers_id']) ? $_POST['liste_pers_id'] : NULL;

$tab_adr_id=isset($_POST['tab_adr_id']) ? $_POST['tab_adr_id'] : NULL;
$tab_adr_id_diff=isset($_POST['tab_adr_id_diff']) ? $_POST['tab_adr_id_diff'] : NULL;
*/

/*
$tab_resp_id=isset($_POST['tab_resp_id']) ? $_POST['tab_resp_id'] : NULL;
$tab_resp_id_diff=isset($_POST['tab_resp_id_diff']) ? $_POST['tab_resp_id_diff'] : NULL;
*/

/*
$tab_resp=isset($_POST['tab_resp']) ? $_POST['tab_resp'] : NULL;
$tab_resp_diff=isset($_POST['tab_resp_diff']) ? $_POST['tab_resp_diff'] : NULL;

$total_diff=isset($_POST['total_diff']) ? $_POST['total_diff'] : NULL;

$liste_assoc=isset($_POST['liste_assoc']) ? $_POST['liste_assoc'] : NULL;

$ne_pas_proposer_resp_sans_eleve=isset($_POST['ne_pas_proposer_resp_sans_eleve']) ? $_POST['ne_pas_proposer_resp_sans_eleve'] : (isset($_GET['ne_pas_proposer_resp_sans_eleve']) ? $_GET['ne_pas_proposer_resp_sans_eleve'] : (isset($_SESSION['ne_pas_proposer_resp_sans_eleve']) ? $_SESSION['ne_pas_proposer_resp_sans_eleve'] : "si"));
*/

$stop=isset($_POST['stop']) ? $_POST['stop'] : (isset($_GET['stop']) ? $_GET['stop'] :'n');

//$style_specifique="responsables/maj_import2";

$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";

$mysql_collate=getSettingValue("mysql_collate") ? getSettingValue("mysql_collate") : "";
$chaine_mysql_collate="";
if($mysql_collate!="") {$chaine_mysql_collate="COLLATE $mysql_collate";}

//**************** EN-TETE *****************
$titre_page = "Importation of the communes of birth";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

if(isset($step)) {
	if(($step==0)||
		($step==1)||
		($step==2)
		) {

		echo "<div style='float: right; border: 1px solid black; width: 4em;'>
<form name='formstop' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='checkbox' name='stop' id='stop' value='y' onchange='stop_change()' ";
//if(isset($stop)) {
if($stop=='y') {
	echo "checked ";
}
echo "/> <a href='#' onmouseover=\"afficher_div('div_stop','y',10,20);\">Stop</a>";
echo add_token_field();
echo "</form>\n";
		echo "</div>\n";

		echo creer_div_infobulle("div_stop","","","This button allows if it is checked to stop the automatic passages to the next page","",12,0,"n","n","y","n");

		echo "<script type='text/javascript'>
	temporisation_chargement='ok';
	cacher_div('div_stop');
</script>\n";


							echo "<script type='text/javascript'>
function stop_change() {
	stop='n';
	if(document.getElementById('stop')) {
		if(document.getElementById('stop').checked==true) {
			stop='y';
		}
	}
	if(document.getElementById('id_form_stop')) {
		document.getElementById('id_form_stop').value=stop;
	}
}

//function test_stop(num) {
function test_stop(num,compteur,nblig) {
	stop='n';
	if(document.getElementById('stop')) {
		if(document.getElementById('stop').checked==true) {
			stop='y';
		}
	}
	//document.getElementById('id_form_stop').value=stop;
	if(stop=='n') {
		//setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=1')\",2000);
		//document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&amp;stop='+stop);
		document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&compteur='+compteur+'&nblig='+nblig+'&stop='+stop+'".add_token_in_url(false)."');
	}
}

function test_stop2() {
	stop='n';
	if(document.getElementById('stop')) {
		if(document.getElementById('stop').checked==true) {
			stop='y';
		}
	}
	document.getElementById('id_form_stop').value=stop;
	if(stop=='n') {
		//setTimeout(\"document.forms['formulaire'].submit();\",1000);
		document.forms['formulaire'].submit();
	}
}





function test_stop_suite(num) {
	stop='n';
	if(document.getElementById('stop')) {
		if(document.getElementById('stop').checked==true) {
			stop='y';
		}
	}

	document.location.replace('".$_SERVER['PHP_SELF']."?step='+num";
// AJOUT A FAIRE VALEUR STOP
echo "+'&stop='+stop";
echo "+'".add_token_in_url(false)."'";
echo ");
}

</script>\n";

	}
}

echo "<p class=bold>";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//echo "</p>\n";

// On fournit les fichiers CSV générés depuis les XML de SCONET...
//if (!isset($is_posted)) {
if(!isset($step)) {
	echo " | <a href=\"../utilitaires/import_pays.php\">Import des pays</a>";
	echo "</p>\n";

	echo "<h2>Importation of the communes of birth of the students</h2>\n";

	$sql="SELECT e.* FROM eleves e WHERE e.lieu_naissance='';";
	$res=mysql_query($sql);
	$nb_lieu_nais_non_renseignes=mysql_num_rows($res);
	if($nb_lieu_nais_non_renseignes>0) {
		if($nb_lieu_nais_non_renseignes==1) {
			echo "<p>".$nb_lieu_nais_non_renseignes." birthplace is not indicated&nbsp;: \n";
			$lig=mysql_fetch_object($res);
			echo casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
			echo "</p>\n";
		}
		elseif($nb_lieu_nais_non_renseignes>1) {
			echo "<p>".$nb_lieu_nais_non_renseignes." birthplaces are not indicated&nbsp;: \n";
			$cpt=0;
			while($lig=mysql_fetch_object($res)) {
				if($cpt>0) {echo ", ";}
				echo casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
				// Pour des tests... echo " (UPDATE eleves SET lieu_naissance='' WHERE login='$lig->login';)<br />";
				$cpt++;
			}
			echo "</p>\n";
		}

		// METTRE DES LIENS VERS LES FICHES ELEVES
		// MODIFIER LA FICHE ELEVE POUR PERMETTRE LA SAISIE D'UNE COMMUNE ET FAIRE UNE RECHERCHE SUR LE CODE COMMUNE CORRESPONDANT DANS communes

		if(getSettingValue('import_maj_xml_sconet')==1) {
			echo "<p>Make an <a href='../responsables/maj_import.php'>update from Sconet</a> to retrive the code_commune_insee of birthplaces of the students.</p>\n";
		}
		echo "<p><br /></p>\n";

	}

	$sql="SELECT e.* FROM eleves e
	LEFT JOIN communes c ON c.code_commune_insee=e.lieu_naissance
	where c.code_commune_insee is NULL;";
	$res=mysql_query($sql);
	//if(mysql_num_rows($res)==0) {
	if(mysql_num_rows($res)<=$nb_lieu_nais_non_renseignes) {
		echo "<p>All the birthplaces typed for the students have their correspondant in the table 'communes'.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="TRUNCATE TABLE tempo2;";
	$res0=mysql_query($sql);

	$retour_commune_manquante="";
	$retour_commune_etrangere="";
	$cpt=0;
	$cpt2=0;
	while($lig=mysql_fetch_object($res)) {
		if($lig->lieu_naissance!='') {

			if(strstr($lig->lieu_naissance,'@')) {
				if($cpt2>0) {$retour_commune_etrangere.="<br />";}
				$retour_commune_etrangere.=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2')." <span style='font-size:small'>(".get_commune($lig->lieu_naissance,1).")</span>";
				$cpt2++;
			}
			else {
				if($cpt>0) {$retour_commune_manquante.=", ";}
				$retour_commune_manquante.=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
				$cpt++;
			}

		}
	}

	if($cpt>0) {
		echo "<p>The birthplaces are missing for ";
		echo $retour_commune_manquante;
		echo "</p>\n";
	}
	else {
		echo "<p>All the birthplaces in a French commune are indicated.</p>\n";
	}

	if($retour_commune_etrangere!='') {
		echo "<p>The birthplaces in foreign communes are&nbsp;:</p>\n";
		echo "<p style='margin-left:3em;'>";
		echo $retour_commune_etrangere;
		echo "</p>\n";
		echo "<p>If these places are correctly indicated, you do not have anything to do.<br />If not... it should be waited until a page is developed to fill the birthplaces abroad apart from the method 'Import Sconet'.</p>\n";
	}

	echo "<p><br /></p>\n";

	if($cpt>0) {
		echo "<p>You will import the correspondences code_commune_insee/name of commune from a CSV file.<br />
This file is bulky (<i>France counts some communes;o</i>).<br />
It would be a shame to make unnecessarily swell your base by filling it with all the communes of France.<br />
This page thus will traverse the file, fill out a temporary table and to retain finally only common the correspondent of it to your students.<br />
The file required below can be downloaded here&nbsp;: <a href='https://www.sylogix.org/attachments/647/communes1102.csv.zip'>https://www.sylogix.org/attachments/647/communes1102.csv.zip</a></p>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	
		//echo "<input type=hidden name='is_posted' value='yes' />\n";
		echo "<input type=hidden name='step' value='0' />\n";
		//echo "<input type=hidden name='mode' value='1' />\n";
		if ($gepiSettings['unzipped_max_filesize']>=0) {
			echo "<p>Select the file <b>communes.csv.zip</b>&nbsp;:<br />\n";
		}
		else {
			echo "<p>please unzip the file (<i>avoid opening it/modify/save with a spreadsheet</i>) and provide the file <b>communes.csv</b>&nbsp;:<br />\n";
		}
		echo "<input type=\"file\" size=\"80\" name=\"communes_csv_file\" /><br />\n";
	
		echo "Traverse the file by sections of <input type=\"text\" size=\"6\" name=\"nblig\" value=\"500\" /> lines.<br />\n";
		//==============================
		// AJOUT pour tenir compte de l'automatisation ou non:
		//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
		echo "<input type='checkbox' name='stop' id='id_form_stop' value='y' /><label for='id_form_stop' style='cursor: pointer;'> Disable the automatic mode.</label></p>\n";
		//==============================

		echo add_token_field();

		echo "<p><input type='submit' value='Validate' /></p>\n";
		echo "</form>\n";
	
		echo "<p><br /></p>\n";
	
		echo "<p style='color:red;'>TO DO: propose to import once the communes.<br />Announce the size of the table obtained.</p>\n";
	}
}
else {
	if($step>0) {
		echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Importation of the communes</a>";
	}
	echo " | <a href=\"../utilitaires/import_pays.php\">Importation of countries</a>";
	echo "</p>\n";

	check_token(false);

	//echo "\$step=$step<br />\n";

	// On va uploader le fichier CSV dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir) {
		echo "<p style='color:red'>It seems that the temporary folder of the user ".$_SESSION['login']." is not defined!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}

	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	$nblig=isset($_POST['nblig']) ? $_POST['nblig'] : (isset($_GET['nblig']) ? $_GET['nblig'] : 500);

	//if(!isset($_POST['step'])) {
	switch($step) {
		case 0:
			// Affichage des informations élèves
			echo "<h2>Transfert du fichier des communes</h2>\n";

			$csv_file = isset($_FILES["communes_csv_file"]) ? $_FILES["communes_csv_file"] : NULL;

			if(!is_uploaded_file($csv_file['tmp_name'])) {
				echo "<p style='color:red;'>The upload of the file failed.</p>\n";

				echo "<p>Variables of php.ini can perhaps explain the problem:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "</p>\n";

				// Il ne faut pas aller plus loin...
				// SITUATION A GERER
				require("../lib/footer.inc.php");
				die();
			}
			else {
				if(!file_exists($csv_file['tmp_name'])) {
					echo "<p style='color:red;'>The file would have been uploadé... but would not be present/preserved.</p>\n";

					echo "<p>The variables of the php.ini can perhaps explain the problem:<br />\n";
					echo "post_max_size=$post_max_size<br />\n";
					echo "upload_max_filesize=$upload_max_filesize<br />\n";
					echo "and the volume of ".$csv_file['name']." would be<br />\n";
					echo "\$csv_file['size']=".volume_human($csv_file['size'])."<br />\n";
					echo "</p>\n";
					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>The file was uploadé.</p>\n";

				/*
				echo "\$csv_file['tmp_name']=".$csv_file['tmp_name']."<br />\n";
				echo "\$tempdir=".$tempdir."<br />\n";

				echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "\$csv_file['size']=".volume_human($csv_file['size'])."<br />\n";
				echo "</p>\n";
				*/

				//$source_file=stripslashes($csv_file['tmp_name']);
				$source_file=$csv_file['tmp_name'];
				$dest_file="../temp/".$tempdir."/communes.csv";
				//$res_copy=copy("$source_file" , "$dest_file");
				//echo $source_file." -&gt; ".$dest_file.'<br />';

				//===============================================================
				// ajout prise en compte des fichiers ZIP: Marc Leygnac

				$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
				// $unzipped_max_filesize = 0    pas de limite de taille pour les fichiers extraits
				// $unzipped_max_filesize < 0    extraction zip désactivée
				if($unzipped_max_filesize>=0) {
					$fichier_emis=$csv_file['name'];
					$extension_fichier_emis=strtolower(strrchr($fichier_emis,"."));
					if (($extension_fichier_emis==".zip")||($csv_file['type']=="application/zip"))
						{

						$dest_zip_file="../temp/".$tempdir."/communes.csv.zip";
						$res_copy=copy("$source_file" , "$dest_zip_file");

						require_once('../lib/pclzip.lib.php');
						//$archive = new PclZip($dest_file);
						$archive = new PclZip($dest_zip_file);

						if (($list_file_zip = $archive->listContent()) == 0) {
							echo "<p style='color:red;'>Erreur : ".$archive->errorInfo(true)."</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						if(sizeof($list_file_zip)!=1) {
							echo "<p style='color:red;'>Error: The archive contains more than one file.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						/*
						echo "<p>\$list_file_zip[0]['filename']=".$list_file_zip[0]['filename']."<br />\n";
						echo "\$list_file_zip[0]['size']=".$list_file_zip[0]['size']."<br />\n";
						echo "\$list_file_zip[0]['compressed_size']=".$list_file_zip[0]['compressed_size']."</p>\n";
						*/
						//echo "<p>\$unzipped_max_filesize=".$unzipped_max_filesize."</p>\n";

						if(($list_file_zip[0]['size']>$unzipped_max_filesize)&&($unzipped_max_filesize>0)) {
							echo "<p style='color:red;'>Error: Size of the extracted file (<i>".$list_file_zip[0]['size']." bytes</i>) exceed the parameterized limit (<i>$unzipped_max_filesize bytes</i>).</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$res_extract=$archive->extract(PCLZIP_OPT_PATH, "../temp/".$tempdir);
						if ($res_extract != 0) {
							echo "<p>The uploaded file was unzipped.</p>\n";
							$fichier_extrait=$res_extract[0]['filename'];
							//echo "Fichier extrait: ".$fichier_extrait."<br />";
							//unlink("$dest_file"); // Pour Wamp...
							$res_copy=rename("$fichier_extrait" , "$dest_file");
						}
						else {
							echo "<p style='color:red'>Failure of the extraction of the ZIP archive .</p>\n";
							require("../lib/footer.inc.php");
							die();
						}
					}
					else {
						$res_copy=copy("$source_file" , "$dest_file");
					}
				}
				else {
					$res_copy=copy("$source_file" , "$dest_file");
				}
				//fin  ajout prise en compte des fichiers ZIP
				//===============================================================

				if(!$res_copy) {
					echo "<p style='color:red;'>The copy of the file towards the temporary folder failed.<br />Check that the user or the apache group or www-data has access to the folder temp/$tempdir</p>\n";
					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}
				else {
					echo "<p>The copy of the file towards the temporary folder succeeded.</p>\n";

					$sql="TRUNCATE TABLE tempo2;";
					$res0=mysql_query($sql);
				
					$sql="SELECT e.* FROM eleves e
					LEFT JOIN communes c ON c.code_commune_insee=e.lieu_naissance
					where c.code_commune_insee is NULL;";
					$res=mysql_query($sql);
				
					while($lig=mysql_fetch_object($res)) {
						if($lig->lieu_naissance!='') {
							$sql="INSERT INTO tempo2 SET col1='$lig->login', col2='$lig->lieu_naissance';";
							$res2=mysql_query($sql);updateOnline($sql);
						}
					}

					echo "<script type='text/javascript'>
	setTimeout(\"test_stop('1',1,$nblig)\",3000);
</script>\n";

					//echo "<a href=\"javascript:test_stop('1',0,$nblig)\">Suite</a>";
					echo "<a href=\"".$_SERVER['PHP_SELF']."?step=1&amp;compteur=1&amp;nblig=$nblig".add_token_in_url()."\">Continuation</a>";

					require("../lib/footer.inc.php");
					die();
				}
			}
			break;
		case 1:
			echo "<h2>Course of the file of the communes</h2>\n";

			// AFFICHER UN TEMON SUR LE NOMBRE DE LIGNES ENCORE PRESENTES... OU DEJA PARCOURUES
			$compteur=isset($_GET['compteur']) ? $_GET['compteur'] : 1;
			//$nblig=isset($_GET['nblig']) ? $_GET['nblig'] : 100;

			$delais=1000;

			$nb_tranches=ceil(38894/$nblig);

			echo "<p>Tranche $compteur/$nb_tranches&nbsp;:";

			$src_file="../temp/".$tempdir."/communes.csv";
			$dest_file="../temp/".$tempdir."/_communes.csv";

			@unlink($dest_file);
			if(!rename($src_file,$dest_file)) {
				echo "<p style='color:red;'>Error during the initial treatment (renaming) of the file communes.csv en _communes.csv</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			echo " Lecture de $nblig lignes.</p>\n";

			$tab_ele=array();
			$tab_lieu=array();
			$sql="SELECT * FROM tempo2;";
			$res=mysql_query($sql);
			$cpt=0;
			while($lig=mysql_fetch_object($res)) {
				$tab_ele[$cpt]=$lig->col1;
				$tab_lieu[$cpt]=$lig->col2;
				$cpt++;
			}

			$fin_fichier='n';

			$temoin_trouve=0;
			// On ne va lire/traiter que les 100 premières lignes du fichier
			// Le fichier en compte 38894... ça fait 389 passages si une commune recherchée est à la fin...
			$fich=fopen($dest_file,"r");
			for($i=0;$i<$nblig;$i++) {
				if(feof($fich)) {
					$fin_fichier='y';
					break;
				}
				$ligne=trim(fgets($fich,4096));
				//echo "<p>Ligne $i: $ligne<br />\n";

				unset($tab);
				$tab=explode(";",$ligne);
				$code_commune_insee=$tab[0];

				if(in_array($code_commune_insee,$tab_lieu)) {
					// Effectuer le traitement

					$delais=3000;

					$departement=$tab[1];
					$commune=$tab[2];

					if($temoin_trouve==0) {echo "<p>";}
					echo "Birthplace found&nbsp;: $code_commune_insee -&gt; $commune<br />\n";

					$sql="INSERT INTO communes SET code_commune_insee='$code_commune_insee', departement='$departement', commune='".addslashes($commune)."';";
					//echo "$sql<br />\n";
					$insert=mysql_query($sql);updateOnline($sql);

					$sql="DELETE FROM tempo2 WHERE col2='$code_commune_insee';";
					//echo "$sql<br />\n";
					$del=mysql_query($sql);updateOnline($sql);

					$temoin_trouve++;
				}
			}

			$sql="SELECT 1=1 FROM tempo2;";
			$res=mysql_query($sql);
			$nb_eleves_a_traiter=mysql_num_rows($res);

			if($fin_fichier=='y') {
				fclose($fich);
				if($nb_eleves_a_traiter==0) {
					echo "<p>All the birthplaces were found.</p>\n";
					unlink($dest_file);
				}
				else {
					if($nb_eleves_a_traiter==1) {
						echo "<p>A birthplace was not found and the file communes.csv was entirely traversed&nbsp;: \n";
					}
					else {
						echo "<p>The birthplace was not found for $nb_eleves_a_traiter students and the file communes.csv was entirely traversed (???)&nbsp;: ";
					}
	
					// A FAIRE: Lister les élèves
					$sql="SELECT e.login,e.nom,e.prenom,e.lieu_naissance, t.col2 FROM tempo2 t, eleves e WHERE e.login=t.col1 ORDER BY e.nom, e.prenom;";
					$res=mysql_query($sql);
					$cpt=0;
					if(mysql_num_rows($res)==0) {
						echo "Aucun élève trouvé";
						echo ".</p>\n";
					}
					else {
						echo "<table class='boireaus' summary=\"Table of the students for which the birthplace is not in the CSV.\">\n";
						echo "<tr>\n";
						echo "<th>Élève</th>\n";
						echo "<th>Lieu</th>\n";
						echo "</tr>\n";
						$alt=1;
						while($lig=mysql_fetch_object($res)) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt white_hover'>\n";
							echo "<td>".casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2')."</td>\n";
							echo "<td>$lig->col2</td>\n";
							echo "</tr>\n";
							//if($cpt>0) {echo ", ";}
							//echo casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2')." (<i>$lig->col2</i>)";
							//$cpt++;
						}
						echo "</table>\n";
					}
					//echo ".</p>\n";

					echo "<p><b>NOTE</b>&nbsp;: The students born in a foreign commune can seem not found in the file of communes.<br />If information between brackets is correct, it is not necessary to be alarmed.</p>\n";
				}
			}
			else {

				$suite=fread($fich,filesize($dest_file));
	
				$dest_file="../temp/".$tempdir."/communes.csv";
				$fich2=fopen($dest_file,"w+");
				fwrite($fich2,$suite);
				fclose($fich2);

				fclose($fich);

				if($nb_eleves_a_traiter==0) {
					echo "<p>All the birthplaces were found.</p>\n";
					unlink($dest_file);
				}
				else {
					if($nb_eleves_a_traiter==1) {
						echo "<p>A birthplace must still be required.</p>\n";
					}
					else {
						echo "<p>The birthplaces must still be required for $nb_eleves_a_traiter students.</p>\n";
					}
		
					// Si on n'a pas trouvé tous les lieux de naissance manquants: Générer le code javascript pour relancer la boucle
	
					$compteur++;
	
					echo "<script type='text/javascript'>
	//setTimeout(\"test_stop('1',$compteur,$nblig)\",3000);
	setTimeout(\"test_stop('1','$compteur','$nblig')\",$delais);
</script>\n";
	
					//echo "<a href=\"javascript:test_stop('1',$compteur,$nblig)\">Suite</a>";
					echo "<a href=\"".$_SERVER['PHP_SELF']."?step=1&amp;compteur=$compteur&amp;nblig=$nblig".add_token_in_url()."\">Continuation</a>";
				}
			}
			break;
	}
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
