<?php

/*
* $Id: index.php 8729 2011-12-13 20:04:12Z crob $
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisation
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

include "../lib/periodes.inc.php";
//**************** EN-TETE *****************// $lavariable La variable et son affectation par defaut, deja presente dans le code

$titre_page = "Saisie des absences";

$lang = isset($_GET['lang']) ? "_" . $_GET['lang'] : "";
$titre_page = empty($lang) ? ${'titre_page' . $lang} = "typing of the absences" : $titre_page;

require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if (!isset($id_classe)) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo " | <a href='import_absences_sconet.php'>Import the absences of Sconet by batches</a>\n";
	echo "</p>\n";

	if ((($_SESSION['statut']=="cpe")&&(getSettingValue('GepiAccesAbsTouteClasseCpe')=='yes'))||($_SESSION['statut']!="cpe")) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe;";
	} else {
		$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_cpe e, j_eleves_classes jc WHERE (e.cpe_login = '".$_SESSION['login']."' AND jc.login = e.e_login AND c.id = jc.id_classe)  ORDER BY classe;";
	}
	$calldata = mysql_query($sql);
	$nombreligne = mysql_num_rows($calldata);

	echo "<p>Total : $nombreligne classe";
	if($nombreligne>1){echo "s";}
	echo " - ";
	echo "Click on the class for which you wish to type the absences :</p>\n";
	echo "<p>Notice : Are displayed the class for which you are responsible for the follow-up of at least one".$gepiSettings['denomination_eleve']." of the class.</p>\n";

	/*
	$i = 0;
	while ($i < $nombreligne){
		$id_classe = mysql_result($calldata, $i, "id");
		$classe_liste = mysql_result($calldata, $i, "classe");
		echo "<br /><a href='index.php?id_classe=$id_classe'>$classe_liste</a>\n";
		$i++;

	}
	*/

	$i = 0;
	unset($tab_lien);
	unset($tab_txt);
	$tab_txt=array();
	$tab_lien=array();
	while ($i < $nombreligne){
		$tab_lien[$i] = "index.php?id_classe=".mysql_result($calldata, $i, "id");
		$tab_txt[$i] = mysql_result($calldata, $i, "classe");
		$i++;
	}
	tab_liste($tab_txt,$tab_lien,3);



	echo "<br />\n";
} else {
	// On choisit la p�riode :
	echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Choose another class</a>";

	echo " | <a href='import_absences_sconet.php'>Import the absences of Sconet by batches</a>\n";

	echo "</p>\n";

	$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
	$classe = mysql_result($call_classe, "0", "classe");
	echo "<h2>Class of ".$classe."</h2>\n";
	echo "<p><b>Manual typing - Choose the period : </b></p>\n";
	//echo "<ul>\n";
	$i="1";
	echo "<table class='boireaus' cellpadding='3'>\n";

	// si le module de gestion des absences est activ� alors on ajout un colspan de 2 pour l'ent�t d'importation
	$colspan = '2';
	if ( getSettingValue("active_module_absence") === 'y' || getSettingValue("abs2_import_manuel_bulletin")==='y') {
		$colspan = '3';
	}

	echo "<tr><th>Period</th><th style='width:6em;'>Saisir</th><th style='width:6em;'>Consult</th><th colspan='$colspan'>Import the absences</th></tr>\n";
	$alt=1;
	while ($i < $nb_periode) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<th>".ucfirst($nom_periode[$i])."</th>\n";
		if(($ver_periode[$i] == "N")||
		 (($ver_periode[$i]!="O")&&($_SESSION['statut']=='secours'))) {
		  echo "<td><a href='saisie_absences.php?id_classe=$id_classe&amp;periode_num=$i'><img src='../images/edit16.png' width='16' height='16' alt='Saisir' title='Saisir' /></a></td>\n";
		  //echo "<td><a href='saisir_groupe.php?id_classe=$id_classe&amp;periode_num=$i'><img src='../images/edit16.png' width='16' height='16' alt='Saisir' title='Saisir' /></a></td>\n";
		} else {
			echo "<td style='color:red;'><img src='../images/disabled.png' width='20' height='20' alt='".$gepiClosedPeriodLabel."' title='".$gepiClosedPeriodLabel."' /></td>\n";
		}
		echo "<td><a href='consulter_absences.php?id_classe=$id_classe&amp;periode_num=$i'><img src='../images/icons/chercher.png' width='16' height='16' alt='Consulter' title='Consulter' /></a></td>\n";

		if(($ver_periode[$i] == "N")||
		 (($ver_periode[$i]!="O")&&($_SESSION['statut']=='secours'))) {
			echo "<td style='width:5em;'><a href='import_absences_gep.php?id_classe=$id_classe&amp;periode_num=$i'>of GEP</a></td>\n";
		} else {
			echo "<td style='color:red;' colspan='$colspan'><img src='../images/disabled.png' width='20' height='20' alt='".$gepiClosedPeriodLabel."' title='".$gepiClosedPeriodLabel."' /></td>\n";
		}

	    // si le module de gestion des absences de gepi est activ� alors on propose l'importation des absences de ce module
	    if ( getSettingValue("active_module_absence") === 'y' || getSettingValue("abs2_import_manuel_bulletin")==='y' ) {
			if(($ver_periode[$i] == "N")||
			(($ver_periode[$i]!="O")&&($_SESSION['statut']=='secours'))) {
				echo "<td style='width:5em;'><a href='import_absences_gepi.php?id_classe=$id_classe&amp;periode_num=$i'>of GEPI</a></td>\n";
			}
			/*
			else {
				echo "<td style='color:red;'>".$gepiClosedPeriodLabel."</td>\n";
			}
			*/
	    }

		if(($ver_periode[$i] == "N")||
		 (($ver_periode[$i]!="O")&&($_SESSION['statut']=='secours'))) {
			echo "<td style='width:5em;'><a href='import_absences_sconet.php?id_classe=$id_classe&amp;num_periode=$i'>of Sconet</a></td>\n";
		}
		/*
		else {
			echo "<td style='color:red;'>".$gepiClosedPeriodLabel."</td>\n";
		}
		*/
		$i++;
	}
	echo "</table>\n";
	//echo "</ul>\n";

	echo "<p><br /></p>\n";

	echo "<p><i>NOTES:</i></p>\n";
	echo "<ul>\n";
	echo "<li><p>For the importation of the absences from GEP, files F_EABS.DBF and F_NOMA.DBF of base GEP are necessary.</p></li>\n";
	echo "<li><p>For the importation of the absences from Sconet, the file ExportAbsences.xml de Sconet is necessary.</p></li>\n";
	echo "</ul>\n";

	/*
	$i="1";
	// On propose l'importation � partir d'un fichier GEP
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation � partir du fichier F_EABS.DBF de la base GEP (fichier F_NOMA.DBF �galement requis) :</p>\n";
			echo "<ul><li><a href='import_absences_gep.php?id_classe=$id_classe&amp;periode_num=$i'>Importer les absences � partir du fichier F_EABS.DBF</a></li></ul>\n";
		}
		$i++;
	}
	*/


	/*
	$i="1";
	// On propose l'importation � partir d'un fichier GEP
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation � partir du fichier <b>exportAbsences.xml</b> de <b>Sconet</b> :</p>\n";
			echo "<ul><li><a href='import_absences_sconet.php?id_classe=$id_classe&amp;periode_num=$i'>Importer les absences � partir du fichier exportAbsences.xml</a></li></ul>\n";
		}
		$i++;
	}
	*/
}
require "../lib/footer.inc.php";
?>
