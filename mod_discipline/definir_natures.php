<?php

/*
* $Id: definir_natures.php 7507 2011-07-24 11:48:01Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Didier Blanqui
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

//$variables_non_protegees = 'yes';
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
$sql = "SELECT 1=1 FROM `droits` WHERE id='/mod_discipline/definir_natures.php';";
$test = mysql_query($sql);
if (mysql_num_rows($test) == 0) {
	$sql = "INSERT INTO droits VALUES ( '/mod_discipline/definir_natures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les natures', '')";
	$test = mysql_query($sql);
	updateOnline($sql);
}

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

$msg = "";

$suppr_nature = isset($_POST['suppr_nature']) ? $_POST['suppr_nature'] : NULL;

$id_nature= isset($_POST['id_nature']) ? $_POST['id_nature'] : NULL;
$id_categorie= isset($_POST['id_categorie']) ? $_POST['id_categorie'] : NULL;
$cpt = isset($_POST['cpt']) ? $_POST['cpt'] : 0;

$nature = isset($_POST['nature']) ? $_POST['nature'] : NULL;
$id_categorie_nature_nouvelle= isset($_POST['id_categorie_nature_nouvelle']) ? $_POST['id_categorie_nature_nouvelle'] : 0;

if (isset($suppr_nature)) {
	check_token();

	for ($i = 0; $i < $cpt; $i++) {
		if (isset($suppr_nature[$i])) {
			$sql = "DELETE FROM s_natures WHERE id='$suppr_nature[$i]';";
			//echo "$sql<br />";
			$suppr = mysql_query($sql);
			updateOnline($sql);
			if (!$suppr) {
				//$msg.="ERREUR lors de la suppression de la qualité n°".$suppr_lieu[$i].".<br />\n";
				$msg.="ERROR during the suppression of nature n°" . $suppr_nature[$i] . ".<br />\n";
			} else {
				$msg.="Suppression of nature n°" . $suppr_nature[$i] . ".<br />\n";
			}
		}
	}
}

$tab_categorie=array();
$sql = "SELECT * FROM s_categories ORDER BY categorie;";
//echo "$sql<br />";
$res2 = mysql_query($sql);
if(mysql_num_rows($res2)>0) {
	while ($lig2=mysql_fetch_object($res2)) {
		$tab_categorie[$lig2->id]=$lig2->categorie;
	}
}

if ((isset($nature))&&($nature != '')) {
	check_token();

	$a_enregistrer = 'y';

	$sql = "SELECT nature FROM s_natures ORDER BY nature;";
	//echo "$sql<br />";
	$res = mysql_query($sql);
	if (mysql_num_rows($res) > 0) {
		$tab_nature = array();
		while ($lig = mysql_fetch_object($res)) {
			$tab_nature[] = $lig->nature;
		}

		if (in_array($nature, $tab_nature)) {
			$a_enregistrer = 'n';
			$msg.="Nature suggested already exists.<br />";
		}
	}

	if ($a_enregistrer == 'y') {
		$nature=preg_replace('/(\\\r\\\n)+/',"\r\n",$nature);
		$nature=preg_replace('/(\\\r)+/',"\r",$nature);
		$nature=preg_replace('/(\\\n)+/',"\n",$nature);

		if(!array_key_exists($id_categorie_nature_nouvelle,$tab_categorie)) {
			$id_categorie_nature_nouvelle=0;
			$msg.="The selected category for new nature does not exist.<br />";
		}

		$sql = "INSERT INTO s_natures SET nature='" . $nature . "', id_categorie='".$id_categorie_nature_nouvelle."';";
		//echo "$sql<br />";
		$res = mysql_query($sql);updateOnline($sql);
		if (!$res) {
			$msg.="ERROR during the recording of " . $nature . "<br />\n";
		} else {
			$msg.="Recording of " . $nature . "<br />\n";
			//echo "Ajout de la nouvelle nature avec l'id ".mysql_insert_id()."<br />";
		}
	}
}


if((isset($id_nature))&&(count($id_nature)>0)&&(isset($id_categorie))&&(count($id_categorie)>0)) {
	check_token();

	for($i=0;$i<count($id_nature);$i++) {
		if(($id_categorie[$i]==0)||(array_key_exists($id_categorie[$i],$tab_categorie))) {
			$sql="UPDATE s_natures SET id_categorie='$id_categorie[$i]' WHERE id='$id_nature[$i]';";
			//echo "$sql<br />";
			$update=mysql_query($sql);updateOnline($sql);
			if (!$update) {
				//$msg.="Erreur lors de la mise à jour de la catégorie pour la nature ".$tab_nature[$id_nature[$i]]['nature']."<br />";
				$msg.="Error at the time of the update of the category for nature n°".$id_nature[$i]."<br />";
			}
		}
	}
}

$tab_nature=array();
//$sql = "(SELECT sn.* FROM s_natures sn, s_categories sc WHERE sn.id_categorie=sc.id ORDER BY sc.categorie) UNION (SELECT * FROM s_natures WHERE id_categorie NOT IN (SELECT id FROM s_categories) ORDER BY nature);";
//$sql = "(SELECT * FROM s_natures WHERE id_categorie NOT IN (SELECT id FROM s_categories) ORDER BY nature) UNION (SELECT sn.* FROM s_natures sn, s_categories sc WHERE sn.id_categorie=sc.id ORDER BY sc.categorie);";
// Il y a un problème de tri avec UNION SELECT... je passe à deux requêtes
$sql = "SELECT sn.* FROM s_natures sn, s_categories sc WHERE sn.id_categorie=sc.id ORDER BY sc.categorie;";
//echo "$sql<br />";
$res = mysql_query($sql);

$sql = "SELECT * FROM s_natures WHERE id_categorie NOT IN (SELECT id FROM s_categories) ORDER BY nature;";
//echo "$sql<br />";
$res2 = mysql_query($sql);
if((mysql_num_rows($res)>0)||(mysql_num_rows($res2)>0)) {
	$cpt=0;
	while ($lig=mysql_fetch_object($res)) {
		$tab_nature[$cpt]['id']=$lig->id;
		$tab_nature[$cpt]['nature']=$lig->nature;
		$tab_nature[$cpt]['id_categorie']=$lig->id_categorie;
		$cpt++;
	}
	while ($lig2=mysql_fetch_object($res2)) {
		$tab_nature[$cpt]['id']=$lig2->id;
		$tab_nature[$cpt]['nature']=$lig2->nature;
		$tab_nature[$cpt]['id_categorie']=$lig2->id_categorie;
		$cpt++;
	}
}
else {
	$tab_natures_par_defaut=array('Refusal of work', 'Work not made', 'Degradation', 'Repeated delays', 'Lapse of memory of material', 'Insolence and behavior', 'Verbal violence', 'Physical violence', 'Verbal and physical violence', 'Repeated chatterings');

	for($i=0;$i<count($tab_natures_par_defaut);$i++) {
		$sql="INSERT INTO s_natures SET nature='".$tab_natures_par_defaut[$i]."';";
		//echo "$sql<br />";
		$insert=mysql_query($sql);updateOnline($sql);
	}

	$sql = "SELECT * FROM s_natures ORDER BY nature;";
	//echo "$sql<br />";
	$res2 = mysql_query($sql);
	if(mysql_num_rows($res2)>0) {
		$cpt=0;
		while ($lig2=mysql_fetch_object($res2)) {
			$tab_nature[$cpt]['id']=$lig2->id;
			$tab_nature[$cpt]['nature']=$lig2->nature;
			$tab_nature[$cpt]['id_categorie']=$lig2->id_categorie;
			$cpt++;
		}
	}
}

/*
if((isset($id_nature))&&(count($id_nature)>0)&&(isset($id_categorie))&&(count($id_categorie)>0)) {
	check_token();

	for($i=0;$i<count($id_nature);$i++) {
		if(($id_categorie[$i]==0)||(array_key_exists($id_categorie[$i],$tab_categorie))) {
			$sql="UPDATE s_natures SET id_categorie='$id_categorie[$i]' WHERE id='$id_nature[$i]';";
			//echo "$sql<br />";
			$update=mysql_query($sql);
			if (!$update) {
				$msg.="Erreur lors de la mise à jour de la catégorie pour la nature ".$tab_nature[$id_nature[$i]]['nature']."<br />";
			}
		}
	}
}
*/

if(isset($_POST['DisciplineNaturesRestreintes'])) {
	check_token();

	$DisciplineNaturesRestreintes=$_POST['DisciplineNaturesRestreintes'];

	$reg_DisciplineNaturesRestreintes=saveSetting("DisciplineNaturesRestreintes", $DisciplineNaturesRestreintes);

	if(!$reg_DisciplineNaturesRestreintes) {
		$msg.="Error during the recording of 'DisciplineNaturesRestreintes' with the value '$DisciplineNaturesRestreintes'<br />\n";
	}
	else {
		$msg.="Recording of 'DisciplineNaturesRestreintes' with the value '$DisciplineNaturesRestreintes' carried out.<br />\n";
	}
}

$DisciplineNaturesRestreintes=getSettingValue('DisciplineNaturesRestreintes');
if($DisciplineNaturesRestreintes=='') {
	$DisciplineNaturesRestreintes=1;
}

$themessage = 'Information was modified. do you want to really leave without recording ?';
//**************** EN-TETE *****************
//$titre_page = "Sanctions: Définition des qualités";
$titre_page = "Discipline: Definition of natures";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Returnr</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='" . $_SERVER['PHP_SELF'] . "' method='post' name='formulaire'>\n";
echo add_token_field();

echo "<p class='bold'>Seizure of natures of incidents&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt = 0;

echo "<p>Existing natures&nbsp;:</p>\n";
echo "<table class='boireaus' border='1' summary='Table of existing natures'>\n";
echo "<tr>\n";
echo "<th>Nature</th>\n";
echo "<th>Category</th>\n";
echo "<th>Remove</th>\n";
echo "</tr>\n";
$alt = 1;
for($i=0;$i<count($tab_nature);$i++) {
	$alt = $alt * (-1);
	echo "<tr class='lig$alt'>\n";

	echo "<td>\n";
	echo "<label for='suppr_nature_$cpt' style='cursor:pointer;'>";
	echo $tab_nature[$i]['nature'];
	echo "</label>";
	echo "</td>\n";

	echo "<td>\n";
	echo "<input type='hidden' name='id_nature[$cpt]' value='".$tab_nature[$i]['id']."' />\n";
	echo "<select name='id_categorie[$cpt]'>\n";
	echo "<option value='0'";
	if($tab_nature[$i]['id_categorie']==0) {echo " selected='true'";}
	echo ">---</option>\n";
	foreach($tab_categorie as $key => $value) {
		echo "<option value='$key'";
		if($tab_nature[$i]['id_categorie']==$key) {echo " selected='true'";}
		echo ">$value</option>\n";
	}
	echo "</select>";
	echo "</td>\n";

	echo "<td><input type='checkbox' name='suppr_nature[]' id='suppr_nature_$cpt' value=\"".$tab_nature[$i]['id']."\" onchange='changement();' /></td>\n";
	echo "</tr>\n";

	$cpt++;
}

echo "</table>\n";

echo "</blockquote>\n";

echo "<table border='0'>\n";
echo "<tr><td>New nature&nbsp;: </td><td><input type='text' name='nature' value='' onchange='changement();' /></td></tr>\n";
echo "<tr><td>Category&nbsp;: </td><td>";
echo "<select name='id_categorie_nature_nouvelle'>\n";
echo "<option value='0' selected='true'>---</option>\n";
foreach($tab_categorie as $key => $value) {
	echo "<option value='$key'";
	echo ">$value</option>\n";
}
echo "</select>";
echo "</td></tr>\n";
echo "</table>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p>\n";
echo "<input type='radio' name='DisciplineNaturesRestreintes' id='DisciplineNaturesRestreintes_0' value='0' ";
if($DisciplineNaturesRestreintes=="0") {echo "checked ";}
echo "/><label for='DisciplineNaturesRestreintes_0'> Not to use the list of nature suggested here.<br />The users will be able to seize natures of incident freely and will
see themselves proposing only natures among those seized previously at
the time of other incidents.</label><br />\n";

echo "<input type='radio' name='DisciplineNaturesRestreintes' id='DisciplineNaturesRestreintes_1' value='1' ";
if($DisciplineNaturesRestreintes=="1") {echo "checked ";}
echo "/><label for='DisciplineNaturesRestreintes_1'> The users will be able to seize natures of incident freely, but will
see themselves proposing only natures of the list above.</label><br />\n";

echo "<input type='radio' name='DisciplineNaturesRestreintes' id='DisciplineNaturesRestreintes_2' value='2' ";
if($DisciplineNaturesRestreintes=="2") {echo "checked ";}
echo "/><label for='DisciplineNaturesRestreintes_2'> Restrict natures of incidents above being able to be selected with
only natures.<br />The users will have to choose one of natures of the list above.</label><br />\n";
echo "</p>\n";

echo "<input type='hidden' name='is_posted' value='y' />\n";
echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

echo "<p><i>NOTES&nbsp;:</i></p>
<ul>
	<li>
		<p>To restrict natures of incidents above being able to be selected with
only natures makes it possible to avoid a too great dispersion of
natures (<i>one can if not to have 'Insolence', 'Behavior insolate', 'insolent',...</i>).<br />
		However, too to restrict them can obstruct the users.</p>
	</li>
</ul>\n";

require("../lib/footer.inc.php");
?>