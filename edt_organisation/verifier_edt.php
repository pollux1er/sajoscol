<?php

/**
 *
 *
 * @version $Id: verifier_edt.php $
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Timetable - Maintenance";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");/*
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
	
}*/

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// ajout de la ligne suivante dans 'sql/data_gepi.sql' et 'utilitaires/updates/access_rights.inc.php'
// INSERT INTO droits VALUES ('/edt_organisation/verifier_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'vérifier la table edt_cours', '');

$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/verifier_edt.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/edt_organisation/verifier_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','vérifier la table edt_cours', '');";
	$res_insert=mysql_query($sql);
	updateOnline($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
if ($_SESSION["statut"] != "administrateur") {
	Die('You must ask for to your administrator the authorization of see this page.');
}

// ===== Initialisation des variables =====
$supprimer = isset($_GET["supprimer"]) ? $_GET["supprimer"] : (isset($_POST["supprimer"]) ? $_POST["supprimer"] : NULL);
$message = "";

// ============================================ Suppression d'un emploi du temps

if (isset($supprimer)) {
	check_token();

    if ($supprimer == "suppression_profs") {
	    $req_profs = mysql_query("SELECT DISTINCT login_prof FROM edt_cours WHERE
					    login_prof NOT IN (SELECT login FROM utilisateurs)
                        ");
        if (mysql_num_rows($req_profs) != 0) {
            $req_suppression_prof = mysql_query("DELETE FROM edt_cours WHERE
                        login_prof NOT IN (SELECT login FROM utilisateurs)
                        ");
						updateOnline("DELETE FROM edt_cours WHERE
                        login_prof NOT IN (SELECT login FROM utilisateurs)
                        ");
        }
    }
    elseif ($supprimer== "suppression_groupes") {
	    $req_groupes = mysql_query("SELECT DISTINCT id_groupe FROM edt_cours WHERE
					id_groupe NOT IN (SELECT id FROM groupes) AND
                    id_groupe != ''
                    ");
        if (mysql_num_rows($req_groupes) != 0) {
	        while ($rep_groupes = mysql_fetch_array($req_groupes)) {
                $req_suppression_groupe = mysql_query("DELETE FROM edt_cours WHERE
                            id_groupe = '".$rep_groupes['id_groupe']."'
                            ");
							updateOnline("DELETE FROM edt_cours WHERE
                            id_groupe = '".$rep_groupes['id_groupe']."'
                            ");
	        }
        }
	    $req_groupes = mysql_query("SELECT DISTINCT id_aid FROM edt_cours WHERE
					id_aid NOT IN (SELECT id FROM aid) AND
                    id_aid != ''
                    ");
        if (mysql_num_rows($req_groupes) != 0) {
	        while ($rep_groupes = mysql_fetch_array($req_groupes)) {
                $req_suppression_groupe = mysql_query("DELETE FROM edt_cours WHERE
                            id_aid = '".$rep_groupes['id_aid']."'
                            ");
				 updateOnline("DELETE FROM edt_cours WHERE
                            id_aid = '".$rep_groupes['id_aid']."'
                            ");
	        }
        }

    }
	elseif($supprimer == 'suppression_cours_duree_nulle') {
		$req_suppr_cours_duree_nulle=mysql_query("DELETE FROM edt_cours WHERE duree='0';");
		updateOnline("DELETE FROM edt_cours WHERE duree='0';");
	}

}


// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

//++++++++++ l'entête de Gepi +++++
require_once("../lib/header.inc");
//++++++++++ fin entête +++++++++++
//++++++++++ le menu EdT ++++++++++
require_once("./menu.inc.php");
//++++++++++ fin du menu ++++++++++

?>


<br/>
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

	<?php 
        if ($message != "") {
            echo $message;
        }
        require_once("./menu.inc.new.php"); ?>


<h2><strong>Maintenance</strong></h2>
<?php

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        

	$req_profs = mysql_query("SELECT DISTINCT login_prof FROM edt_cours WHERE
					login_prof NOT IN (SELECT login FROM utilisateurs)
                    ");
    if (mysql_num_rows($req_profs) != 0) {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 1</strong></p>';
        echo "<p style=\"text-align:center;\">".mysql_num_rows($req_profs)." teacher registered in the timetables does not exist any more in GEPI</p>";
        echo '<p style="text-align:center;padding:8px;">
               <a style="background-color:#FFFABD;border:2px solid black;padding:2px;" href="./verifier_edt.php?supprimer=suppression_profs'.add_token_in_url().'">Launch the procedure of cleaning</a>
            </p>';
    }
    else {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 1</strong></p>';
        echo '<p style="text-align:center;">There are perfect agreement between teachers recorded on GEPI and those recorded in the timetables</p>';

    }
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}


$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        

	$req_groupes = mysql_query("SELECT DISTINCT id_groupe FROM edt_cours WHERE
					id_groupe NOT IN (SELECT id FROM groupes) AND
                    id_groupe != ''
                    ");
    if (mysql_num_rows($req_groupes) != 0) {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 2</strong></p>';
	    echo "<p style=\"text-align:center;\">".mysql_num_rows($req_groupes)." course registered in the timetables does not exist any more in GEPI</p>";
        //while ($rep = mysql_fetch_array($req_groupes)) {
        //    echo "<p style=\"text-align:center;\">".$rep['id_groupe']."<p>";
        //}
        echo '<p style="text-align:center;padding:8px;">
	           <a style="background-color:#FFFABD;border:2px solid black;padding:2px;" href="./verifier_edt.php?supprimer=suppression_groupes'.add_token_in_url().'">Launch the procedure of cleaning</a>
            </p>';
    }
    else {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 2</strong></p>';
        echo '<p style="text-align:center;">There are perfect agreement between courses recorded on GEPI and those recorded in the timetables</p>';

    }
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        

	$req_groupes = mysql_query("SELECT DISTINCT id_aid FROM edt_cours WHERE
					id_aid NOT IN (SELECT id FROM aid) AND
                    id_aid != '' 
                    ");
    if (mysql_num_rows($req_groupes) != 0) {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 3</strong></p>';
	    echo "<p style=\"text-align:center;\">".mysql_num_rows($req_groupes)." ida registers in the timetables does not exist more in GEPI</p>";
        echo '<p style="text-align:center;padding:8px;">
	           <a style="background-color:#FFFABD;border:2px solid black;padding:2px;" href="./verifier_edt.php?supprimer=suppression_groupes'.add_token_in_url().'">Launch the procedure of cleaning</a>
            </p>';
    }
    else {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 3</strong></p>';
        echo '<p style="text-align:center;">There is perfect agreement between idas recorded on GEPI and those recorded in the timetables</p>';

    }
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}

//==========================================================

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        

	$req_duree_0=mysql_query("SELECT * FROM edt_cours WHERE duree=0 ORDER BY login_prof, jour_semaine;");
    if (mysql_num_rows($req_duree_0) != 0) {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 4</strong></p>';
	    echo "<p style=\"text-align:center;\">".mysql_num_rows($req_duree_0)." course has a null duration.<br /> That can cause large disturbances on the display of the EDT of the professors concerned.</p>\n";

        echo '<p style="text-align:center;padding:8px;">
	           <a style="background-color:#FFFABD;border:2px solid black;padding:2px;" href="./verifier_edt.php?supprimer=suppression_cours_duree_nulle'.add_token_in_url().'">Remove these courses in the EDT</a>
            </p>';
		echo "<div align='center'>\n";
		echo "<p>Or, try to correct them one by one&nbsp;:</p>\n";
		echo "<table class='boireaus'>\n";
		$alt=1;
		echo "<tr>\n";
		echo "<th>id_cours</th>\n";
		echo "<th>Professor</th>\n";
		echo "<th>Day</th>\n";
		echo "<th>Hour beginning</th>\n";
		echo "<th>Modify</th>\n";
		echo "<th> or Delete</th>\n";
		echo "</tr>\n";
		$tab_creneaux=array();
		while($lig=mysql_fetch_object($req_duree_0)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td>".$lig->id_cours."</td>\n";
			echo "<td>".civ_nom_prenom($lig->login_prof)."</td>\n";
			echo "<td>".$lig->jour_semaine."</td>\n";
			echo "<td>";
			if(!isset($tab_creneaux[$lig->id_definie_periode])) {
				$sql="SELECT ec.* FROM edt_creneaux ec WHERE ec.id_definie_periode='$lig->id_definie_periode';";
				$res_creneau=mysql_query($sql);
				if(mysql_num_rows($res_creneau)>0) {
					$lig_creneau=mysql_fetch_object($res_creneau);
					$tab_creneaux[$lig->id_definie_periode]=$lig_creneau->nom_definie_periode;
				}
				else {
					$tab_creneaux[$lig->id_definie_periode]="???";
				}
			}
			echo $tab_creneaux[$lig->id_definie_periode];
			echo "</td>\n";
			// Il manque le period_id... mais on ne peut pas avoir le même id_cours avec plusieurs period_id
			//echo "<td><a href='modifier_cours_popup.php?period_id="."&id_cours=$lig->id_cours&type_edt=prof&identite=$lig->login_prof' target='_blank'><img src='../images/icons/saisie.png' width='16' height='16' title='Editer' alt='Editer' /></a></td>\n";
			echo "<td><a href='modifier_cours.php?period_id="."&id_cours=$lig->id_cours&type_edt=prof&identite=$lig->login_prof' target='_blank'><img src='../images/icons/saisie.png' width='16' height='16' title='Edit' alt='Edit' /></a></td>\n";
			echo "<td><a href='effacer_cours.php?period_id="."&supprimer_cours=$lig->id_cours&type_edt=prof&identite=$lig->login_prof' target='_blank'><img src='../images/icons/delete.png' width='16' height='16' title='Delete' alt='Delete' /></a></td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "</div>\n";
    }
    else {
        echo '<p style="text-align:center;font-size:1.2em;border-bottom:1px solid black;"><strong>Test 4</strong></p>';
        echo '<p style="text-align:center;">No course has of null duration.</p>';

    }
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}

//==========================================================


?>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
