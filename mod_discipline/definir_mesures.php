<?php

/*
 * $Id: definir_mesures.php 7138 2011-06-05 17:37:14Z crob $
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/saisie_mesures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Saisie des mesures', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_mesures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Saisie des mesures', '');;";
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

require('sanctions_func_lib.php');

$suppr_mesure=isset($_POST['suppr_mesure']) ? $_POST['suppr_mesure'] : NULL;
$mesure=isset($_POST['mesure']) ? $_POST['mesure'] : NULL;
//$commentaire=isset($_POST['commentaire']) ? $_POST['commentaire'] : NULL;
$type=isset($_POST['type']) ? $_POST['type'] : 0;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

$msg="";

if(isset($suppr_mesure)) {
	check_token();

	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_mesure[$i])) {
			$sql="SELECT 1=1 FROM s_traitement_incident sti WHERE sti.id_mesure='".$suppr_mesure[$i]."';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				$msg.="Suppression of n° measurement".$suppr_mesure[$i]." impossible bus associated with ".mysql_num_rows($test)." incidents.<br />\n";
			}
			else {
				//$sql="DELETE FROM s_mesures WHERE mesure='$suppr_mesure[$i]';";
				$sql="DELETE FROM s_mesures WHERE id='".$suppr_mesure[$i]."';";
				$suppr=mysql_query($sql);
				updateOnline($sql);
				if(!$suppr) {
					//$msg.="ERREUR lors de la suppression de la mesure ".$suppr_mesure[$i].".<br />\n";
					$msg.="ERROR during the suppression of n° measurement".$suppr_mesure[$i].".<br />\n";
				}
			}
		}
	}
}

//if((isset($mesure))&&($mesure!='')&&(isset($type))&&(($type=='prise')||($type=='demandee'))) {
if(isset($mesure)) {
	$a_enregistrer='y';

	check_token();

	$tab_mesure=array();
	$sql="SELECT * FROM s_mesures ORDER BY mesure;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		//$tab_mesure=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_mesure[]=$lig->mesure;

			//echo "Id_mesure: $lig->id<br />";
			if(isset($NON_PROTECT["commentaire_".$lig->id])) {
				$commentaire=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["commentaire_".$lig->id]));
				$commentaire=preg_replace('/(\\\r\\\n)+/',"\r\n",$commentaire);
				$commentaire=preg_replace('/(\\\r)+/',"\r",$commentaire);
				$commentaire=preg_replace('/(\\\n)+/',"\n",$commentaire);

				$sql="UPDATE s_mesures SET commentaire='$commentaire' WHERE id='".$lig->id."';";
				//echo "$sql<br />\n";
				$update=mysql_query($sql);
				updateOnline($sql);
				if(!$update) {
					$msg.="ERROR at the time of the update of ".$lig->mesure."<br />\n";
				}
			}
		}

		if($msg=="") {
			$msg.="Update of the comments of measurements previously seized carried out.<br />";
		}
		//if(in_array($mesure,$tab_mesure)) {$a_enregistrer='n';}
	}


	if((isset($mesure))&&($mesure!='')&&(isset($type))&&(($type=='prise')||($type=='demandee'))) {

		if(in_array($mesure,$tab_mesure)) {$a_enregistrer='n';}

		if($a_enregistrer=='y') {
			//$mesure=addslashes(preg_replace('/(\\\r\\\n)+/',"\r\n",preg_replace("/&#039;/","'",html_entity_decode($mesure))));
			$mesure=preg_replace('/(\\\r\\\n)+/',"\r\n",$mesure);
			$mesure=preg_replace('/(\\\r)+/',"\r",$mesure);
			$mesure=preg_replace('/(\\\n)+/',"\n",$mesure);

			if(isset($NON_PROTECT["commentaire"])) {
				$commentaire=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["commentaire"]));
			}
			else {
				$commentaire="";
			}
			$commentaire=preg_replace('/(\\\r\\\n)+/',"\r\n",$commentaire);
			$commentaire=preg_replace('/(\\\r)+/',"\r",$commentaire);
			$commentaire=preg_replace('/(\\\n)+/',"\n",$commentaire);

			$sql="INSERT INTO s_mesures SET mesure='".$mesure."', commentaire='$commentaire', type='".$type."';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);updateOnline($sql);
			if(!$res) {
				$msg.="ERROR during the recording of ".$mesure."<br />\n";
			}
			else {
				$msg.="Recording of ".$mesure." carried out.<br />\n";
			}
		}
	}

}

$themessage  = 'Information was modified. Want you to really leave without recording ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Definition of measurements";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
echo add_token_field();

echo "<p class='bold'>Seizure of the measurements taken or requested following an incident&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM s_mesures ORDER BY type,mesure;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p>No measurement is yet defined.</p>\n";
}
else {
	echo "<p>Existing measurements&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Table of existing measurements'>\n";
	echo "<tr>\n";
	echo "<th>Measure</th>\n";
	echo "<th>Comment</th>\n";
	echo "<th>Type</th>\n";
	echo "<th>Remove</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		echo "<label for='suppr_mesure_$cpt' style='cursor:pointer;'>";
		echo $lig->mesure;
		echo "</label>";
		//echo "<input type='hidden' name='id_mesure[$cpt]' value=\"$lig->id\" />\n";
		echo "</td>\n";

		echo "<td>\n";
		/*
		echo "<label for='suppr_mesure_$cpt' style='cursor:pointer;'>";
		echo $lig->commentaire;
		echo "</label>";
		*/
		//echo "<textarea class='wrap' name=\"no_anti_inject_commentaire_$cpt\" rows='2' cols='100' onchange=\"changement()\">$lig->commentaire</textarea>\n";
		echo "<textarea class='wrap' name=\"no_anti_inject_commentaire_".$lig->id."\" rows='2' cols='60' onchange=\"changement()\">$lig->commentaire</textarea>\n";
		echo "</td>\n";

		echo "<td>\n";
		echo preg_replace("/demandee/","demandée",$lig->type);
		echo "</td>\n";

		//echo "<td><input type='checkbox' name='suppr_mesure[]' id='suppr_mesure_$cpt' value=\"$lig->mesure\" onchange='changement();' /></td>\n";
		echo "<td><input type='checkbox' name='suppr_mesure[]' id='suppr_mesure_$cpt' value=\"$lig->id\" onchange='changement();' /></td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}

echo "<p>New measurement&nbsp;:</p>\n";

echo "<table class='boireaus' border='1' summary='Nouvelle mesure'>\n";
echo "<tr class='lig1'>\n";
echo "<td>Measure&nbsp;</td>\n";
echo "<td><input type='text' name='mesure' value='' onchange='changement();' /></td>\n";
echo "</tr>\n";
echo "<tr class='lig1'>\n";
echo "<td>Comment&nbsp;</td>\n";
echo "<td>\n";
//echo "<input type='text' name='commentaire' value='' onchange='changement();' />\n";

echo "<textarea class='wrap' name=\"no_anti_inject_commentaire\" rows='2' cols='60' onchange=\"changement()\"></textarea>\n";

echo "</td>\n";
echo "</tr>\n";
echo "<tr class='lig1'>\n";
echo "<td valign='top'>Type&nbsp;</td>\n";
echo "<td style='text-align:left;'>\n";
echo "<input type='radio' name='type' value='prise' id='type_prise' onchange='changement();' checked='checked' />\n";
echo "<label for='type_prise' style='cursor:pointer;'>";
echo " Catch\n";
echo "</label>";
echo "<br />\n";
echo "<input type='radio' name='type' id='type_demandee' value='demandee' onchange='changement();' />\n";
echo "<label for='type_demandee' style='cursor:pointer;'>";
echo " Asked\n";
echo "</label>";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";

echo "</blockquote>\n";

echo "</form>\n";

echo "<p><br /></p>\n";

echo "<p><em>NOTES&nbsp;:</em></p>\n";
echo "<ul>\n";
echo "<li><p>A measurement requested (<em>by a professor</em>) must be validated by CPE/scol.</p></li>\n";
echo "<li><p>The comment is posted in infobulle in the page of seizure of incident.</p></li>\n";
echo "</ul>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>