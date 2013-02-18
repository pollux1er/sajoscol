<?php

/*
 * $Id$
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

$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/saisie_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie incident', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie incident', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("You try to reach the Discipline module which is decontaminated !");
	tentative_intrusion(1, "Attempt at access to the Discipline module which is decontaminated.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$id_absence_eleve_saisie=isset($_POST['id_absence_eleve_saisie']) ? $_POST['id_absence_eleve_saisie'] : (isset($_GET['id_absence_eleve_saisie']) ? $_GET['id_absence_eleve_saisie'] : NULL);

$saisie = AbsenceEleveSaisieQuery::create()->findPk($id_absence_eleve_saisie);
if ($saisie == null) {
    require_once("../lib/header.inc");
    echo "Error, Identify of seizure of not transmitted absence";
    require("../lib/footer.inc.php");
    die();
}

$msg = "";

if ($saisie->getIdSIncidents() == null || $saisie->getIdSIncidents() == -1) {
	check_token();

    //l'incident n'est pas encore enregistré, on l'enregistre donc
    $sql="INSERT INTO s_incidents SET declarant='".$_SESSION['login']."',
									    date='".$saisie->getDebutAbs('Y-m-d')."',
									    heure='".$saisie->getDebutAbs('H:i')."',
									    nature='exclusion de cours',
									    description='".$saisie->getCommentaire()."',
									    id_lieu='',
									    message_id='';";
    //echo "$sql<br />\n";
    $res=mysql_query($sql);updateOnline($sql);
    if(!$res) {
	    $msg.="ERROR during the recording of the incident &nbsp;:".$sql."<br />\n";
    }
    else {
	    $id_incident=mysql_insert_id();
	    $msg.="Recording of the incident n°".$id_incident." effectué.<br />\n";
    }
    $saisie->setIdSIncidents($id_incident);
    $saisie->save();

    $sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$saisie->getEleve()->getLogin()."', statut='eleve', qualite='Responsable';";
    $res=mysql_query($sql);updateOnline($sql);
    if(!$res) {
	    $msg.="ERROR during the recording of ".$saisie->getEleve()->getLogin()."<br />\n";
    }
}

//reglage des parametres
$_GET['id_incident'] = $saisie->getIdSIncidents();
$_GET['step'] = '2';

include('saisie_incident.php');

?>