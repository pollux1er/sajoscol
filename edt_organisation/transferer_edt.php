<?php

/**
 *
 *
 * @version $Id: transferer_edt.php $
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

$titre_page = "Emploi du temps - Transfert";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

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
// INSERT INTO droits VALUES ('/edt_organisation/transferer_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'transférer un edt', '');

$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/transferer_edt.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/edt_organisation/transferer_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','transférer un edt', '');";
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
$login = isset($_GET["login"]) ? $_GET["login"] : (isset($_POST["login"]) ? $_POST["login"] : NULL);
$couper = isset($_GET["couper"]) ? $_GET["couper"] : (isset($_POST["couper"]) ? $_POST["couper"] : NULL);
$coller = isset($_GET["coller"]) ? $_GET["coller"] : (isset($_POST["coller"]) ? $_POST["coller"] : NULL);
$message = "";

// ============================================ Suppression d'un emploi du temps

if (isset($supprimer) AND isset($login)) {
    if ($supprimer == "ok") {
        $message = '<div class="cadreInformation">
                            Please confirm the suppression :
                            <a style="background-color:white;border:2px solid black;padding:4px;" href="./transferer_edt.php?supprimer=confirme_suppression&amp;login='.addslashes($login).'">Confirm</a>
                            <a style="background-color:white;border:2px solid black;padding:2px;" href="./transferer_edt.php?supprimer=annuler_suppression&amp;login='.addslashes($login).'">Cancel</a>
                    </div>
                    ';    

    }
    else if ($supprimer== "confirme_suppression") {
        // ====================== Vérifier que $login est bien un professeur
        $req_statut = mysql_query("SELECT statut FROM utilisateurs WHERE login = '".addslashes($login)."' ");
        $rep_statut = mysql_fetch_array($req_statut);
        if ($rep_statut["statut"] == "professeur") {
            $req_suppression = mysql_query("DELETE FROM edt_cours WHERE login_prof = '".addslashes($login)."' ");
			updateOnline("DELETE FROM edt_cours WHERE login_prof = '".addslashes($login)."' ");
            $deletedRows = mysql_affected_rows();
            if ($deletedRows != 0) {
                $message = "<div class=\"cadreInformation\">The timetable was removed successfully.</div>";
            }
            else {
                $message =  "<div class=\"cadreInformation\">There is nothing to remove !</div>";
            }
        } else {
            $message = "<div class=\"cadreInformation\">The account concerned is not that of a professor !</div>";
        }
    }

}

// ============================================ Copier un emploi du temps dans le "presse-papier"
if (isset($couper) AND isset($login)) {
    if ($couper=="ok") {
        $_SESSION["couper_edt"] = $login;
        $message="<div class=\"cadreInformation\">The timetable is ready to be transferred</div>";
    }
}

// ============================================ Transférer un emploi du temps
if (isset($coller) AND isset($login) AND isset($_SESSION["couper_edt"])) {
    if ($login != $_SESSION["couper_edt"]) {
        // ====================== Vérifier que $login est bien un professeur
        $req_statut = mysql_query("SELECT statut FROM utilisateurs WHERE login = '".addslashes($login)."' ");
        $rep_statut = mysql_fetch_array($req_statut);
        if ($rep_statut["statut"] == "professeur") {

            $req_compare_groupes = mysql_query("SELECT id_groupe FROM j_groupes_professeurs WHERE 
                                    login = '".$_SESSION["couper_edt"]."' AND
                                    id_groupe NOT IN (SELECT id_groupe FROM j_groupes_professeurs WHERE
                                                    login = '".$login."' ) 
                                    ");
            if (mysql_num_rows($req_compare_groupes) == 0) {
                $req_edt_prof = mysql_query("SELECT * FROM edt_cours WHERE 
                                                            login_prof = '".$login."'
                                                            ") or die(mysql_error());  
                if (mysql_num_rows($req_edt_prof) == 0) {
                        $remplacement = mysql_query("UPDATE edt_cours SET login_prof = '".$login."' WHERE login_prof = '".$_SESSION["couper_edt"]."' ");
updateOnline("UPDATE edt_cours SET login_prof = '".$login."' WHERE login_prof = '".$_SESSION["couper_edt"]."' ");                       
					   $message = "<div class=\"cadreInformation\">transfer done. The courses were moved successfully</div>";
                }
                else {
                    $message = "<div class=\"cadreInformation\">The timetable of the teacher recipient is not empty.</div>";
                }
            }
            else {
                $message = "<div class=\"cadreInformation\">The groups of courses of the two professors are incompatible.</div>";
            }
        }
        else {
            $message = "<div class=\"cadreInformation\">The recipient of the timetable must be a professor.</div>";
        }
    }
    else {
        $message = "<div class=\"cadreInformation\">You cannot transfer a timetable on itself !</div>";
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


<h2><strong>Transfer/remove timetables</strong></h2>
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

		echo "<div class=\"titre_nom_t_edt\"><strong>name</strong></div>";
		echo "<div class=\"titre_prenom_t_edt\">first name</div>";
		echo "<div class=\"titre_creneau_t_edt\">crenels</div>";
        echo "<div style=\"clear:both;\"></div>";

	$req_profs = mysql_query("SELECT login, nom , prenom FROM utilisateurs WHERE
					statut = 'professeur' ORDER BY nom ASC");
	while ($rep_profs = mysql_fetch_array($req_profs)) {
		$req_cours = mysql_query("SELECT id_cours FROM edt_cours WHERE
					login_prof = '".$rep_profs['login']."'");
		echo "<div class=\"texte_nom_t_edt\"><strong>".$rep_profs['nom']."</strong></div>";
		echo "<div class=\"texte_prenom_t_edt\">".$rep_profs['prenom']."</div>";
		echo "<div class=\"texte_creneau_t_edt\">".mysql_num_rows($req_cours)."</div>";
		echo "<div class=\"bouton_supprimer_t_edt\"><a href=\"./transferer_edt.php?supprimer=ok&amp;login=".$rep_profs['login']." \" ><img src=\"../templates/".NameTemplateEDT()."/images/erase.png\" title=\"Remove the timetable\" alt=\"Remove\" /></a></div>";
		echo "<div class=\"bouton_copier_t_edt\"><a href=\"./transferer_edt.php?couper=ok&amp;login=".$rep_profs['login']."\" title=\"Déplacer cet emploi du temps\"><img src=\"../templates/".NameTemplateEDT()."/images/copy.png\" title=\"Move this timetable\" alt=\"Copy\" /></a></div>";
		echo "<div class=\"bouton_coller_t_edt\"><a href=\"./transferer_edt.php?coller=ok&amp;login=".$rep_profs['login']."\" ><img src=\"../templates/".NameTemplateEDT()."/images/paste.png\" title=\"Teacher recipient\" alt=\"Paste\" /></a></div>";
        echo "<div style=\"clear:both;\"></div>";
	}

if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}

?>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>