<?php

@set_time_limit(0);
/*
* $Id: clean_tables.php 7107 2011-06-04 17:05:47Z crob $
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

check_token();

$liste_tables_del = array(
"classes",
"eleves",
"groupes",
//"responsables",
"responsables2",
"resp_pers",
"resp_adr",
"j_eleves_groupes",
"j_groupes_classes",
"j_groupes_professeurs",
"j_groupes_matieres",
"j_eleves_classes",
"j_professeurs_matieres",
"matieres",
"periodes",
"utilisateurs"
);

//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year : Cleaning of the tables ";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return home of initialization</a></p>
<?php
echo "<center><h3 class='gepi'>Seventh phase of initialization<br />Cleaning of the tables</h3></center>\n";
if (!isset($is_posted)) {
	echo "<p><b>CAUTION ...</b> : you should proceed to this operation only if all the data (students, classes, professeurs, disciplines, options) were defined !</p>\n";
	echo "<p>The useless data imported starting from files GEP during of the various phases of initialization will be erased !</p>\n";

	echo "<form enctype='multipart/form-data' action='clean_tables.php' method='post'>\n";
	echo add_token_field();
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<p>";
	echo "<input type='checkbox' name='simulation' id='simulation' value='y' /><label for='simulation'> Simulation (<i>Don't remove anything<i>)</label><br />\n";
	echo "<input type=submit value='Make the cleaning' /></p>\n";
	echo "</form>\n";
} else {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)==0) {
			$flag=1;
		}
		$j++;
	}
	if ($flag != 0){
		echo "<p><b>CAUTION ...</b><br />\n";
		echo "The initialization of the data of the year is not finished, certain data concerning the students, the classes, the groups, the professors or the courses are missing. The procedure cannot continue !</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$simulation=isset($_POST['simulation']) ? $_POST['simulation'] : "n";
	if($simulation=="n") {
		echo "<p><b>Simulation mode &nbsp;:</b> No data will be really removed.</p>\n";
	}

	//Suppression des données inutiles dans la tables utilisateurs
	echo "<h3 class='gepi'>Checking of the data concerning the professors</h3>\n";
	$req = mysql_query("select login from utilisateurs where (statut = 'professeur' and etat='actif')");
	$sup = 'no';
	$nb_prof = mysql_num_rows($req);
	$i = 0;
	while ($i < $nb_prof) {
		$login_prof = mysql_result($req, $i, 'login');
		$test = mysql_query("select id_professeur from j_professeurs_matieres where id_professeur = '$login_prof'");
		if (mysql_num_rows($test)==0) {
			if($simulation=="n") {
				$del = @mysql_query("delete from utilisateurs where login = '$login_prof'");
				echo "The professor $login_prof was removed from the base .<br />\n";
			}
			else {
				echo "The professor <a href='../utilisateurs/modify_user.php?user_login=$login_prof' target='_blank'>$login_prof</a> (<i>associated to any course</i>) would be removed from the base.<br />\n";
			}
			$sup = 'yes';
		} else {
			$test = mysql_query("select login from j_groupes_professeurs where login = '$login_prof'");
			if (mysql_num_rows($test)==0) {
				if($simulation=="n") {
					$del = @mysql_query("delete from utilisateurs where login = '$login_prof'");
					echo "The professor $login_prof  was removed from the base.<br />\n";
				}
				else {
					echo "The professor<a href='../utilisateurs/modify_user.php?user_login=$login_prof' target='_blank'>$login_prof</a> (<i>associated any course/group</i>) would be removed from the bases.<br />\n";
				}
				$sup = 'yes';
			}
		}
		$i++;
	}
	if ($sup == 'no') {
		if($simulation=="n") {
			echo "<p>No professor was removed !</p>\n";
		}
		else {
			echo "<p>No professor would be removed !</p>\n";
		}
	}

	//Suppression des données inutiles dans la tables des matières
	echo "<h3 class='gepi'>Checking of the data concerning the courses</h3>\n";
	$req = mysql_query("select matiere from matieres");
	$sup = 'no';
	$nb_mat = mysql_num_rows($req);
	$i = 0;
	while ($i < $nb_mat) {
		$mat = mysql_result($req, $i, 'matiere');
		$test1 = mysql_query("select id_matiere from j_professeurs_matieres where id_matiere = '$mat'");
		if (mysql_num_rows($test1)==0) {
			$test2 = mysql_query("select id_matiere from j_groupes_matieres where id_matiere = '$mat'");
			if (mysql_num_rows($test2)==0) {
				if($simulation=="n") {
					$del = @mysql_query("delete from matieres where matiere = '$mat'");
					echo "Course $mat was removed from the base.<br />\n";
				}
				else {
					//echo "La matière $mat would be removed from the bases (<i><a href='../utilisateurs/index.php?mode=personnels&amp;order_by=nom,prenom&amp;display=tous&amp;afficher_statut=professeur' target='_blank'>associer à un professeur</a></i>).<br />\n";
					echo "Course  $mat would be removed from the bases (<i><a href='../matieres/modify_matiere.php?current_matiere=$mat' target='_blank'> associate to a professor</a></i>).<br />\n";
				}
				$sup = 'yes';
			}
		}
		$i++;
	}
	if ($sup == 'no') {
		if($simulation=="n") {
			echo "<p>No course was removed !</p>\n";
		}
		else {
			echo "<p>No course would be removed !</p>\n";
		}
	}

	//Suppression des données inutiles dans la tables des responsables
	echo "<h3 class='gepi'>Checking of the data concerning the responsibles of the students</h3>\n";
	//$req = mysql_query("select ereno, nom1, prenom1 from responsables");
/*
	$req = mysql_query("select ele_id, pers_id from responsables2");
	$sup = 'no';
	$nb_resp = mysql_num_rows($req);
	$i = 0;
	while ($i < $nb_resp) {
		//$resp = mysql_result($req, $i, 'ereno');
		$ele_id=mysql_result($req, $i, 'ele_id');
		$test1 = mysql_query("select ele_id from eleves where ele_id='$ele_id'");
		if (mysql_num_rows($test1)==0) {
			$pers_id=mysql_result($req, $i, 'pers_id');
			$sql="SELECT nom, prenom FROM resp_pers WHERE ele_id='$ele_id'";
			$res_resp=mysql_query($sql);
			while($lig_resp=mysql_fetch_object($res_resp)){
				$nom_resp=$lig_resp->nom;
				$prenom_resp=$lig_resp->prenom;
				$del=@mysql_query("delete from responsables2 where ele_id='$ele_id'");
				//echo "Le responsable ".$prenom_resp." ".$nom_resp."  was removed from the base pour l'élève n°$ele_id.<br />";
				$sup = 'yes';
			}
		}
		$i++;
	}
*/
	$req = mysql_query("select pers_id,nom,prenom,adr_id from resp_pers order by nom,prenom");
	$sup = 'no';
	$nb_resp = mysql_num_rows($req);
	$i = 0;
	while ($i < $nb_resp) {
		$pers_id=mysql_result($req, $i, 'pers_id');
		$nom_resp=mysql_result($req, $i, 'nom');
		$prenom_resp=mysql_result($req, $i, 'prenom');
		$adr_id=mysql_result($req, $i, 'adr_id');

		$test1 = mysql_query("select r.ele_id from responsables2 r, eleves e where r.pers_id='$pers_id' AND e.ele_id=r.ele_id");
		//$test1 = mysql_query("select ele_id from eleves where ele_id='$ele_id'");
		if (mysql_num_rows($test1)==0) {
			if($simulation=="n") {
				$del=@mysql_query("delete from responsables2 where pers_id='$pers_id'");
				$del=@mysql_query("delete from resp_pers where pers_id='$pers_id'");
				echo "The responsible ".$prenom_resp." ".$nom_resp."  was removed from the base.<br />\n";
	
				// L'adresse héberge-t-elle encore un représentant d'élève de l'établissement?
				$sql="SELECT * FROM resp_adr ra, eleves e, responsables2 r, resp_pers rp WHERE
						ra.adr_id=rp.adr_id AND
						r.pers_id=rp.pers_id AND
						r.ele_id=e.ele_id AND
						adr_id='$adr_id'";
				$test2=mysql_query($sql);
				if (mysql_num_rows($test1)==0) {
					$sql="delete from resp_adr where adr_id='$adr_id'";
					$del=mysql_query($sql);
				}
			}
			else {
				echo "The responsible ".$prenom_resp." ".$nom_resp." would be removed from the bases.<br />\n";
			}
			$sup = 'yes';
		}
		$i++;
	}
	if ($sup == 'no') {
		if($simulation=="n") {
			echo "<p>No responsible was removed !</p>\n";
		}
		else {
			echo "<p>No responsible would be removed !</p>\n";
		}
	}

	//Suppression des données inutiles dans la table j_eleves_etablissements
	echo "<h3 class='gepi'>Checking of the data concerning the school of origin of the students</h3>\n";

	//SELECT e.* FROM eleves e LEFT JOIN j_eleves_etablissements jec ON jec.id_eleve=e.elenoet WHERE jec.id_eleve is NULL;
	//SELECT jec.* FROM j_eleves_etablissements jec LEFT JOIN eleves e ON jec.id_eleve=e.elenoet WHERE e.elenoet IS NULL;
	$sql="SELECT jec.* FROM j_eleves_etablissements jec
			LEFT JOIN eleves e ON jec.id_eleve=e.elenoet
			WHERE e.elenoet IS NULL;";
	$res_jee=mysql_query($sql);
	if(mysql_num_rows($res_jee)==0) {
		if($simulation=="n") {
			echo "<p>No association student/school was removed.</p>\n";
		}
		else {
			echo "<p>No association student/school would  be removed.</p>\n";
		}
	}
	else {
		$cpt_suppr_jee=0;
		while($lig_jee=mysql_fetch_object($res_jee)) {
			if($simulation=="n") {
				$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='".$lig_jee->id_eleve."' AND id_etablissement='".$lig_jee->id_etablissement."';";
				$suppr=mysql_query($sql);
				if($suppr) {
					$cpt_suppr_jee++;
				}
			}
			else {
				$cpt_suppr_jee++;
			}
		}

		if($simulation=="n") {
			echo "<p>$cpt_suppr_jee association(s) student/school was removed .<br />(<i>for students which are not any more in the school</i>).</p>\n";
		}
		else {
			echo "<p>$cpt_suppr_jee association(s) student/school would be removed .<br />(<i>for students which are not any more in the school</i>).</p>\n";
		}
	}


	echo "<p><br /></p>\n";
	//echo "<p>Fin de la procédure !</p>";

	if($simulation=="n") {
		echo "<p>End of the procedure of importation!</p>\n";

		echo "<p><a href='clean_temp.php?a=a".add_token_in_url()."'>Remove the XML and CSV which can remain in your temporary folder.</a></p>\n";
	}
	else {
		echo "<p>End of the simulation of cleaning!</p>\n";

		echo "<p class'bold' Do you want to restart the procedure without simulation?</p>\n";

		echo "<p>The useless data imported from files GEP during the various phases of initialization will be erased !</p>\n";
	
		echo "<form enctype='multipart/form-data' action='clean_tables.php' method='post'>\n";
		echo add_token_field();
		echo "<input type=hidden name='is_posted' value='yes' />\n";
		echo "<p>";
		echo "<input type='checkbox' name='simulation' id='simulation' value='y' /><label for='simulation'> Simulation (<i>Don't remove anything<i>)</label><br />\n";
		echo "<input type=submit value='Make the cleaning' /></p>\n";
		echo "</form>\n";

	}

	echo "<p><br /></p>\n";

	//echo "<p><b>Etape ajoutée:</b> Si vous disposez du F_DIV.CSV, vous pouvez <a href='init_pp.php'>initialiser les professeurs principaux</a>.</p>";
}
require("../lib/footer.inc.php");
?>