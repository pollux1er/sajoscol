<?php
/*
 * $Id: maj_import.php 4023 2010-01-16 17:10:24Z crob $
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if(strstr($_SERVER['HTTP_REFERER'],"eleves/index.php")) {$_SESSION['retour_apres_maj_sconet']="../eleves/index.php";}

//**************** EN-TETE *****************
$titre_page = "Update student/responsible";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<p class=bold>";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>";
echo "</p>\n";

/*
echo "<p>Vous pouvez effectuer les mises à jour de deux façons:</p>\n";
echo "<ul>\n";
echo "<li><a href='maj_import2.php'>Nouvelle méthode (<i>plus complète</i>)</a>: Nouvelle méthode, en fournissant directement les fichiers XML de Sconet/STS.</li>\n";
echo "<li><a href='maj_import1.php'>Ancienne méthode</a>: En générant des fichiers CSV à partir des fichiers XML de Sconet/STS.</li>\n";
echo "</ul>\n";
echo "<p><br /></p>\n";
*/

echo "<p><a href='maj_import2.php'>Update of the data student/responsible using the files XML of Sconet/STS</a>.</p>\n";
echo "<p><br /></p>\n";

//==================================
// RNE de l'établissement pour comparer avec le RNE de l'établissement de l'année précédente
$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
//==================================
if($gepiSchoolRne=="") {
	echo "<p><b style='color:red;'>Caution</b>: The RNE of the establishment is not indicated in 'General management/<a href='../gestion/param_gen.php' target='_blank'>General configuration</a>'<br />That can disturb the importation of the establishment of origin of the student.<br />You should correct before continuing.</p>\n";
	echo "<p><br /></p>\n";
}

$sql="SELECT 1=1 FROM eleves;";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0){
	echo "<p>No student seems yet present in the base.</p>\n";
}
else{
	$sql="SELECT * FROM eleves WHERE ele_id LIKE 'e%' OR ele_id LIKE '';";
	$res_ele=mysql_query($sql);

	if(mysql_num_rows($res_ele)==0){
		echo "<p>All your student have an identifier 'ele_id' formatted like those coming from Sconet.<br />It is what it is necessary for the update according to Sconet.</p>\n";
	}
	else{
		echo "<p>student have an identifier 'ele_id' correspondent with an initialization without Sconet or a manual individual creation.<br />These student could not be updated automatically according to Sconet.</p>";

		echo "<p>See in <a href='#notes_correction'>sous le tableau</a> possibilities of correction.</p>\n";

		echo "<blockquote>\n";
		echo "<table class='boireaus' summary='student to be corrected'>\n";
		echo "<tr>\n";
		echo "<th>Identifier<br />'ele_id'</th>\n";
		echo "<th>Identifier<br />'elenoet'</th>\n";
		echo "<th>Login</th>\n";
		echo "<th>Name</th>\n";
		echo "<th>First name</th>\n";
		echo "<th>Class</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysql_fetch_object($res_ele)){
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>".$lig->ele_id."</td>\n";
			echo "<td>".$lig->elenoet."</td>\n";
			echo "<td>".$lig->login."</td>\n";
			echo "<td>".strtoupper($lig->nom)."</td>\n";
			echo "<td>".ucfirst(strtolower($lig->prenom))."</td>\n";
			echo "<td>\n";

			$sql="SELECT DISTINCT c.classe FROM classes c, j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='$lig->login';";
			$res_clas=mysql_query($sql);
			if(mysql_num_rows($res_clas)==0){
				echo "(<i><span style='color:red;'>no class</span></i>)\n";
			}
			else{
				$cpt_clas=0;
				echo "(<i>";
				while($lig3=mysql_fetch_object($res_clas)){
					if($cpt_clas>0){echo ", \n";}
					echo $lig3->classe;
					$cpt_clas++;
				}
				echo "</i>)\n";
			}

			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";

		echo "<a name='notes_correction'></a>\n";
		echo "<p>If them ELE_ID are not correct, but that them ELENOET table 'eleves' correspond well to those of the file 'ElevesSansAdresses.xml', you can correct them 'ELE_ID' automatically in the following page: <a href='corrige_ele_id.php'>Correction des ELE_ID</a></p>\n";

		echo "</blockquote>\n";
	}
}


$sql="SELECT 1=1 FROM resp_pers;";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0){
	echo "<p>No responsible seems yet defined.</p>\n";
}
else{
	$sql="SELECT * FROM resp_pers WHERE pers_id LIKE 'p%';";
	$res_pers=mysql_query($sql);
	if(mysql_num_rows($res_pers)==0){
		echo "<p>All your responsible has an identifier 'pers_id' formatted like those coming from Sconet.<br />It is what it is necessary for the update according to Sconet.</p>\n";
	}
	else{
		echo "<p>Responsible has an identifier 'pers_id' correspondent with an initialization without Sconet or a manual
individual creation.<br />These responsible could not be updated automatically according to
Sconet.</p>\n";

		echo "<blockquote>\n";
		echo "<table class='boireaus'>\n";
		echo "<tr>\n";
		echo "<th>Identifier<br />'pers_id'</th>\n";
		echo "<th>Name</th>\n";
		echo "<th>First name</th>\n";
		echo "<th>Responsible of</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysql_fetch_object($res_pers)){
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>".$lig->pers_id."</td>\n";
			echo "<td>".strtoupper($lig->nom)."</td>\n";
			echo "<td>".ucfirst(strtolower($lig->prenom))."</td>\n";
			echo "<td>\n";

			$sql="SELECT e.login,e.nom,e.prenom FROM eleves e, responsables2 r WHERE e.ele_id=r.ele_id AND r.pers_id='$lig->pers_id';";
			$res_resp=mysql_query($sql);
			if(mysql_num_rows($res_resp)==0){
				echo "<span style='color:red;'>No associated pupil</span>\n";
			}
			else{
				$cpt_ele=0;
				while($lig2=mysql_fetch_object($res_resp)){
					if($cpt_ele>0){echo "<br />\n";}
					echo ucfirst(strtolower($lig2->prenom))." ".strtoupper($lig2->nom);
					$sql="SELECT DISTINCT c.classe FROM classes c, j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='$lig2->login';";
					$res_clas=mysql_query($sql);
					if(mysql_num_rows($res_clas)==0){
						echo "(<i><span style='color:red;'>no class</span></i>)\n";
					}
					else{
						$cpt_clas=0;
						echo "(<i>";
						while($lig3=mysql_fetch_object($res_clas)){
							if($cpt_clas>0){echo ", \n";}
							echo $lig3->classe;
							$cpt_clas++;
						}
						echo "</i>)\n";
					}
					$cpt_ele++;
				}
			}

			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "</blockquote>\n";
	}
}


echo "<p><br /></p>\n";
echo "<p><i>NOTE&nbsp;:</i> This page does not make it possible to initialize one year, but only
to update in the course of year information pupils (<i>name, first name, birth, INE, mode,...</i>) and responsible (<i>name, first name, change of address, phone,...</i>), and to import the student/responsible additions in the course of year.</p>\n";

// Il faudrait permettre de corriger l'ELE_ID et le PERS_ID
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
