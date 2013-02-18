<?php
/*
 * $Id: signalement_eleves.php 6913 2011-05-13 15:58:05Z crob $
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
function saveAction($sql) {
	
	$filename = '../responsables/responsable.txt';
	$somecontent = $sql.";\n";

	// Assurons nous que le fichier est accessible en �criture
	if (is_writable($filename)) {

		if (!$handle = fopen($filename, 'a')) {
			 echo "Impossible d'ouvrir le fichier ($filename)";
			 exit;
		}

		// Ecrivons quelque chose dans notre fichier.
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Impossible d'�crire dans le fichier ($filename)";
			exit;
		}

		//echo "L'�criture de ($somecontent) dans le fichier ($filename) a r�ussi";

		fclose($handle);

	} else {
		echo "Le fichier $filename n'est pas accessible en �criture.";
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

$sql="SELECT 1=1 FROM droits WHERE id='/groupes/signalement_eleves.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
    $sql="INSERT INTO droits SET id='/groupes/signalement_eleves.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Groupes: signalement des erreurs d affectation �l�ve',
statut='';";
    $insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$sql="CREATE TABLE IF NOT EXISTS j_signalement (id_groupe int(11) NOT NULL default '0',
login varchar(50) NOT NULL default '',
periode int(11) NOT NULL default '0',
nature varchar(50) NOT NULL default '',
valeur varchar(50) NOT NULL default '',
declarant varchar(50) NOT NULL default '',
PRIMARY KEY (id_groupe,login,periode,nature), INDEX (login));";
$test=mysql_query($sql);
updateOnline($sql);
// Initialisation des variables utilis�es dans le formulaire

//$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : (isset($_POST['id_groupe']) ? $_POST["id_groupe"] : NULL);
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

if(!isset($_SESSION['chemin_retour'])) {
	$_SESSION['chemin_retour']=isset($_GET['chemin_retour']) ? $_GET['chemin_retour'] : (isset($_POST['chemin_retour']) ? $_POST['chemin_retour'] : "../cahier_notes/index.php?id_groupe=$id_groupe");
}

//$periode_cn=isset($_GET['periode_cn']) ? $_GET['periode_cn'] : (isset($_POST['periode_cn']) ? $_POST["periode_cn"] : NULL);
//$id_conteneur = isset($_GET['id_conteneur']) ? $_GET['id_conteneur'] : (isset($_POST['id_conteneur']) ? $_POST["id_conteneur"] : NULL);
// Contr�ler si $id_groupe et $id_conteneur sont bien associ�es
//$sql="";

function debug_edit_eleves($texte) {
	$debug_edit_eleves=0;
	if($debug_edit_eleves==1) {
		echo "<span style='color:green'>$texte</span><br />\n";
	}
}

debug_edit_eleves("id_groupe=$id_groupe");
if (!is_numeric($id_groupe)) $id_groupe = 0;
debug_edit_eleves("id_groupe=$id_groupe");
$current_group = get_group($id_groupe);

/*
$reg_nom_groupe = $current_group["name"];
debug_edit_eleves("reg_nom_groupe=$reg_nom_groupe");
$reg_nom_complet = $current_group["description"];
$reg_matiere = $current_group["matiere"]["matiere"];

//$reg_id_classe = $id_classe;

$reg_clazz = $current_group["classes"]["list"];
$reg_professeurs = (array)$current_group["profs"]["list"];
$mode = isset($_GET['mode']) ? $_GET['mode'] : "groupe";
*/

if (count($current_group["classes"]["list"]) > 1) {
	$multiclasses = true;
} else {
	$multiclasses = false;
}

// Requ�te utilis�e pour la liste des logins	
$reg_eleves = array();
foreach ($current_group["periodes"] as $period) {
	//echo '$period["num_periode"]='.$period["num_periode"]."<br />";
	if($period["num_periode"]!=""){
		$reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
		//$msg.="\$reg_eleves[\$period[\"num_periode\"]]=\$reg_eleves[".$period["num_periode"]."]=".$reg_eleves[$period["num_periode"]]."<br />";
	}
}

$msg="";
if (isset($_POST['is_posted'])) {
	check_token();
	//$error = false;

	// M�nage:
	$sql="DELETE FROM j_signalement WHERE id_groupe='$id_groupe' AND nature='erreur_affect';";
	//echo "$sql<br />";
	$del=mysql_query($sql);
	updateOnline($sql);
	if($del) {
		// El�ves
		$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec, j_groupes_classes jgc WHERE jgc.id_groupe='$id_groupe' AND jec.id_classe=jgc.id_classe ORDER BY login";
		debug_edit_eleves($sql);
		//echo "$sql<br />";
		$result_liste_eleves_classes_du_grp=mysql_query($sql);
		while($lig_eleve=mysql_fetch_object($result_liste_eleves_classes_du_grp)){
			$tab_ele[]=$lig_eleve->login;
			//echo " ".$lig_eleve->login;
		}
	
		//=========================
		// AJOUT: boireaus 20071010
		$login_eleve=$_POST['login_eleve'];
		//=========================

		$nom_declarant="Non_Identifi�";
		$email_declarant="";
		$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
		$req=mysql_query($sql);
		if(mysql_num_rows($req)>0) {
			$lig_u=mysql_fetch_object($req);
			$nom_declarant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
			$email_declarant=$lig_u->email;
		}

		$sujet_mail="[GEPI] Description of missing students or in excess";
		$texte_mail="Description by ".$nom_declarant." of missing students or in excess in ".$current_group["name"]." (".$current_group["description"]." in ".$current_group["classlist_string"].").\n";
		foreach($current_group["periodes"] as $period) {
			for($i=0;$i<count($login_eleve);$i++){
				if(isset($_POST['eleve_'.$period["num_periode"].'_'.$i])) {
					if(in_array($login_eleve[$i],$tab_ele)) {
						$nom_eleve=get_nom_prenom_eleve($login_eleve[$i]);

						$sql="INSERT INTO j_signalement SET id_groupe='$id_groupe', declarant='".$_SESSION['login']."', nature='erreur_affect', login='".$login_eleve[$i]."', periode='".$period["num_periode"]."', ";
						if(in_array($login_eleve[$i],$reg_eleves[$period["num_periode"]])) {
							$sql.="valeur='en_trop';";
							$texte_mail.="$nom_eleve is in excess in period ".$period["num_periode"].".\n";
						}
						else {
							$sql.="valeur='manquant';";
							$texte_mail.="$nom_eleve is missing in period ".$period["num_periode"].".\n";
						}
						//echo "$sql<br />\n";
						$insert=mysql_query($sql);
						updateOnline($sql);
						if(!$insert) {
							$msg.="Erreur pour ".$nom_eleve."<br />";
						}
						//flush();
					}
				}
			}
		}

		//echo nl2br($texte_mail);
		//flush();

		$envoi_mail_actif=getSettingValue('envoi_mail_actif');
		if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
			$envoi_mail_actif='y'; // Passer � 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
		}
		if($envoi_mail_actif=='y') {
			// On utilise un t�moin
			if((isset($nom_eleve))&&($nom_eleve!="")&&(getSettingValue("gepiAdminAdress")!='')) {
				$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
				if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}
	
				$ajout_header="";
				if($email_declarant!="") {$ajout_header.="Cc: $nom_declarant <".$email_declarant.">\r\n";}
	
				$envoi = envoi_mail($sujet_mail, $texte_mail, getSettingValue("gepiAdminAdress"), $ajout_header);	
			}
		}

		// On utilise un t�moin
		if((isset($nom_eleve))&&($nom_eleve!="")&&($msg=="")) {
			$msg="Enregistrement effectu�.";
		}
	}
}

$themessage  = 'Information was modified. Do you really want to leave without recording ?';
//**************** EN-TETE **************************************
$titre_page = "Management of groups";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

//debug_var();

$tab_sig=array();
$sql="SELECT * FROM j_signalement WHERE id_groupe='$id_groupe' AND nature='erreur_affect' ORDER BY periode, login;";
//echo "$sql<br />";
$res_sig=mysql_query($sql);
if(mysql_num_rows($res_sig)>0) {
	while($lig_sig=mysql_fetch_object($res_sig)) {
		//$tab_sig[$lig_sig->periode][$lig_sig->login]=$lig_sig->valeur;
		$tab_sig[$lig_sig->periode][]=$lig_sig->login;
	}
}

//=========================
// AJOUT: boireaus 20071010
$nb_periode=$current_group['nb_periode'];
//=========================

echo "<script type='text/javascript' language='javascript'>

function CocheCase(boul) {
	nbelements = document.formulaire.elements.length;
	for (i = 0 ; i < nbelements ; i++) {
		if (document.formulaire.elements[i].type =='checkbox') {
			document.formulaire.elements[i].checked = boul ;
		}
	}
}

function CocheLigne(ki) {
	for (var i=1;i<$nb_periode;i++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = true;
		}
	}
}

function DecocheLigne(ki) {
	for (var i=1;i<$nb_periode;i++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = false;
		}
	}
}

// Initialisation
change='no';
</script>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='form_passage_a_un_autre_groupe' method='post'>\n";
echo "<p class='bold'>\n";
echo "<a href='".$_SESSION['chemin_retour']."'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo "><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>\n";

$sql="SELECT DISTINCT jgp.id_groupe FROM groupes g, j_groupes_professeurs jgp WHERE jgp.login='".$_SESSION['login']."' AND g.id=jgp.id_groupe ORDER BY g.name;";
//echo "$sql<br />\n";
$res_grp=mysql_query($sql);
if(mysql_num_rows($res_grp)>1) {
	echo " | ";

	echo "<select name='id_groupe' id='id_groupe_a_passage_autre_grp' onchange=\"confirm_changement_grp(change, '$themessage');\">\n";
	$cpt_grp=0;
	$chaine_js=array();
	//echo "<option value=''>---</option>\n";
	while($lig_grp=mysql_fetch_object($res_grp)) {

		$tmp_grp=get_group($lig_grp->id_groupe);

		echo "<option value='$lig_grp->id_groupe'";
		if($lig_grp->id_groupe==$id_groupe) {echo " selected";$indice_grp_courant=$cpt_grp;}
		echo ">".$tmp_grp['description']." (".$tmp_grp['name']." en ".$tmp_grp["classlist_string"].")</option>\n";
		$cpt_grp++;
	}
	echo "</select>\n";

	echo "</p>\n";

	echo "<script type='text/javascript'>
	// Initialisation faite plus haut
	//change='no';

	function confirm_changement_grp(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.forms['form_passage_a_un_autre_groupe'].submit();
		}
		else {
			var is_confirmed = confirm(themessage);
			if(is_confirmed) {
				document.forms['form_passage_a_un_autre_groupe'].submit();
			}
			else {
				document.getElementById('id_groupe_a_passage_autre_grp').selectedIndex=$indice_grp_courant;
			}
		}
	}
</script>\n";

}
else {
	echo "</p>\n";
}
echo "</form>\n";

?>

<?php
	echo "<h3>Manages the students of the course&nbsp;: ";
	echo htmlentities($current_group["description"]) . " (<i>" . $current_group["classlist_string"] . "</i>)";
	echo "</h3>\n";
	//$temp["profs"]["users"][$p_login] = array("login" => $p_login, "nom" => $p_nom, "prenom" => $p_prenom, "civilite" => $civilite);
	if(count($current_group["profs"]["users"])>0){
		echo "<p>Course taught by ";
		$cpt_prof=0;
		foreach($current_group["profs"]["users"] as $tab_prof){
			if($cpt_prof>0){echo ", ";}
			echo ucfirst(strtolower($tab_prof['prenom']))." ".strtoupper($tab_prof['nom']);
			$cpt_prof++;
		}
		echo ".</p>\n";
	}

	echo "<p>This page is intended to enable you to announce to the administrator
of the errors of assignment of students (<i>students in excess or lacks</i>).<br />\n";
	echo "The administrator will be able to take account of your description.<br />\nNote well that the taking into account of your request is not
instantaneous.<br />An intervention of the administrator will be necessary.";
	if(getSettingValue("gepiAdminAdress")!='') {
		echo "<br />\nThe administrator will receive a mall for this description.";
	}
	echo "</p>\n";

?>


<p>
<b><a href="javascript:CocheCase(true);changement();">Check all</a> - <a href="javascript:CocheCase(false);changement();">Uncheck all</a></b>
</p>
<?php

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
echo add_token_field();
echo "<p><input type='submit' value='Save' /></p>\n";

// Edition des �l�ves

echo "<p>Select the students which miss or are in excess in this course, for each period&nbsp;: </p>\n";

echo "<table border='1' class='boireaus' summary='Follow-up of this course by the students according to the periods'>\n";
echo "<tr>\n";
echo "<th><a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;order_by=nom'>Name/First name</a></th>\n";
if ($multiclasses) {
	echo "<th><a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;order_by=classe'>Class</a></th>\n";
}
foreach ($current_group["periodes"] as $period) {
	if($period["num_periode"]!=""){
		echo "<th>" . $period["nom_periode"] . "</th>\n";
	}
}
echo "<th>&nbsp;</th>\n";
//echo "<th>Coef</th>";
echo "</tr>\n";

$conditions = "e.login = j.login and (";
foreach ($current_group["classes"]["list"] as $query_id_classe) {
	$conditions .= "j.id_classe = '" . $query_id_classe . "' or ";
}
$conditions = substr($conditions, 0, -4);
$conditions .= ") and c.id = j.id_classe";

// D�finition de l'ordre de la liste
if ($order_by == "classe") {
	// Classement par classe puis nom puis pr�nom
	$order_conditions = "j.id_classe, e.nom, e.prenom";
} elseif ($order_by == "nom") {
	$order_conditions = "e.nom, e.prenom";
}

//=============================
// AJOUT: boireaus
echo "<tr>\n<th>\n";
//=============================

//=========================
// AJOUT: boireaus 20071010
unset($login_eleve);
//=========================

$sql="SELECT distinct(j.login), j.id_classe, c.classe, e.nom, e.prenom FROM eleves e, j_eleves_classes j, classes c WHERE (" . $conditions . ") ORDER BY ".$order_conditions;
$calldata = mysql_query($sql);
$nb = mysql_num_rows($calldata);
$eleves_list = array();
$eleves_list["list"]=array();
for ($i=0;$i<$nb;$i++) {
	$e_login = mysql_result($calldata, $i, "login");
	//================================
	// AJOUT: boireaus
	//echo "<input type='hidden' name='login_eleve[$i]' value='$e_login' />\n";
	echo "<input type='hidden' name='login_eleve[$i]' id='login_eleve_$i' value='$e_login' />\n";
	//=========================
	// AJOUT: boireaus 20071010
	$login_eleve[$i]=$e_login;
	//=========================
	//================================
	$e_nom = mysql_result($calldata, $i, "nom");
	$e_prenom = mysql_result($calldata, $i, "prenom");
	$e_id_classe = mysql_result($calldata, $i, "id_classe");
	$classe = mysql_result($calldata, $i, "classe");
	$eleves_list["list"][] = $e_login;
	$eleves_list["users"][$e_login] = array("login" => $e_login, "nom" => $e_nom, "prenom" => $e_prenom, "classe" => $classe, "id_classe" => $e_id_classe);
}
//echo "count(\$eleves_list)=".count($eleves_list)."<br />";
$total_eleves = $eleves_list["list"];
//echo "count(\$total_eleves)=".count($total_eleves)."<br />";

foreach ($current_group["periodes"] as $period) {
	if($period["num_periode"]!=""){
		//$total_eleves = array_merge($total_eleves, (array)$reg_eleves[$period["num_periode"]]);
		if(count($reg_eleves[$period["num_periode"]])>0) {$total_eleves = array_merge($total_eleves, (array)$reg_eleves[$period["num_periode"]]);}
		//echo "count(\$reg_eleves[".$period["num_periode"]."])=".count($reg_eleves[$period["num_periode"]])."<br />";
	}
}
//echo "count(\$total_eleves)=".count($total_eleves)."<br />";
$total_eleves = array_unique($total_eleves);
//echo "count(\$total_eleves)=".count($total_eleves)."<br />";

$elements = array();
foreach ($current_group["periodes"] as $period) {
	$elements[$period["num_periode"]] = null;
	foreach($total_eleves as $e_login) {
		$elements[$period["num_periode"]] .= "'eleve_" . $period["num_periode"] . "_"  . $e_login  . "',";
	}
    $elements[$period["num_periode"]] = substr($elements[$period["num_periode"]], 0, -1);
}

//=============================
// MODIF: boireaus
//echo "<tr><td>&nbsp;</td>";
echo "&nbsp;</td>\n";
//=============================

if ($multiclasses) { echo "<td>&nbsp;</td>"; }
echo "\n";
foreach ($current_group["periodes"] as $period) {
	if($period["num_periode"]!=""){
		//echo "<td>";
		//echo "<a href=\"javascript:CochePeriode(" . $elements[$period["num_periode"]] . ")\">Tout</a> <br/> <a href=\"javascript:DecochePeriode(" . $elements[$period["num_periode"]] . ")\">Aucun</a>";
		echo "<th>";
		//=========================
		// MODIF: boireaus 20071010
		//echo "<a href=\"javascript:CochePeriode(" . $elements[$period["num_periode"]] . ")\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecochePeriode(" . $elements[$period["num_periode"]] . ")\"><img src='../images/disabled.png' width='15' height='15' alt='Tout d�cocher' /></a>";

		if(count($total_eleves)>0) {
			echo "<a href=\"javascript:CocheColonne(".$period["num_periode"].");changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' title='Check all' /></a> / <a href=\"javascript:DecocheColonne(".$period["num_periode"].");changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' title='Uncheck all' /></a>";
		}
		//=========================
		echo "<br/>Registered &nbsp;: " . count($current_group["eleves"][$period["num_periode"]]["list"]);
		echo "</th>\n";
	}
}
echo "<th>&nbsp;</th>\n";
//echo "<th>&nbsp;</th>\n";
echo "</tr>\n";

// Marqueurs pour identifier quand on change de classe dans la liste
$prev_classe = 0;
$new_classe = 0;
$empty_td = false;

//=====================================
// AJOUT: boireaus 20080229
$chaine_sql_classe="(";
for($i=0;$i<count($current_group["classes"]["list"]);$i++) {
	if($i>0) {$chaine_sql_classe.=" OR ";}
	$chaine_sql_classe.="id_classe='".$current_group["classes"]["list"][$i]."'";
}
$chaine_sql_classe.=")";
//=====================================

if(count($total_eleves)>0) {
	$alt=1;
	foreach($total_eleves as $e_login) {

		//=========================
		// AJOUT: boireaus 20071010
		// R�cup�ration du num�ro de l'�l�ve:
		$num_eleve=-1;
		for($i=0;$i<count($login_eleve);$i++){
			if($e_login==$login_eleve[$i]){
				$num_eleve=$i;
				break;
			}
		}
		if($num_eleve!=-1) {

			//=========================
			// AJOUT: boireaus 20080229
			// Test de l'appartenance � plusieurs classes
			$sql="SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login='$e_login';";
			$test_plusieurs_classes=mysql_query($sql);
			if(mysql_num_rows($test_plusieurs_classes)==1) {
				$temoin_eleve_changeant_de_classe="n";
			}
			else {
				$temoin_eleve_changeant_de_classe="y";
			}
			//=========================

			//=========================
			//$new_classe = $eleves_list["users"][$e_login]["id_classe"];
			if(isset($eleves_list["users"][$e_login])) {
				$new_classe = $eleves_list["users"][$e_login]["id_classe"];
			}
			else {
				$new_classe="BIZARRE";
			}

			if ($new_classe != $prev_classe and $order_by == "classe" and $multiclasses) {
				echo "<tr style='background-color: #CCCCCC;'>\n";
				echo "<td colspan='3' style='padding: 5px; font-weight: bold;'>";
				echo "Classe de&nbsp;: " . $eleves_list["users"][$e_login]["classe"];
				echo "</td>\n";
				foreach ($current_group["periodes"] as $period) {
					echo "<td>&nbsp;</td>\n";
				}
				//echo "<td>&nbsp;</td>\n";
				echo "</tr>\n";
				$prev_classe = $new_classe;
			}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			if (array_key_exists($e_login, $eleves_list["users"])) {
				/*
				echo "<td>" . $eleves_list["users"][$e_login]["prenom"] . " " .
					$eleves_list["users"][$e_login]["nom"] .
					"</td>";
				*/
				echo "<td>";
				echo $eleves_list["users"][$e_login]["nom"];
				echo " ";
				echo $eleves_list["users"][$e_login]["prenom"];
				echo "</td>";

				if ($multiclasses) {echo "<td>" . $eleves_list["users"][$e_login]["classe"] . "</td>";}
				echo "\n";
			}
			else {
				/*
				echo "<td>" . $e_login . "</td>" .
					"<td>" . $current_group["eleves"]["users"][$e_login]["prenom"] . " " .
					$current_group["eleves"]["users"][$e_login]["nom"] .
					"</td>";
				*/
				echo "<td>";
				if($new_classe=="BIZARRE"){
					echo "<font color='red'>$e_login</font>";
				}
				else{
					echo "$e_login";
				}
				echo "</td>\n";
				if ($multiclasses) {echo "<td>" . $current_group["eleves"]["users"][$e_login]["classe"] . "</td>";}
				echo "\n";
			}
	
	
			foreach ($current_group["periodes"] as $period) {
				if($period["num_periode"]!="") {
					echo "<td align='center'>";
	
					//=========================
					// MODIF: boireaus 20080229
					//$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$e_login' AND id_classe='".$new_classe."' AND periode='".$period["num_periode"]."'";
					$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$e_login' AND $chaine_sql_classe AND periode='".$period["num_periode"]."'";
					//=========================
					$res_test=mysql_query($sql);
					if(mysql_num_rows($res_test)>0){
						//=========================
						// MODIF: boireaus 20071010

						/*
						echo "<input type='checkbox' name='eleve_".$period["num_periode"]."_".$num_eleve."' id='case_".$period["num_periode"]."_".$num_eleve."' ";
						//=========================
						echo " onchange='changement();'";
						if (in_array($e_login, (array)$current_group["eleves"][$period["num_periode"]]["list"])) {
							echo " checked />";
						} else {
							echo " />";
						}
						*/

						// Test sur la pr�sence de notes dans cn ou de notes/app sur bulletin
						if (!test_before_eleve_removal($e_login, $current_group['id'], $period["num_periode"])) {
							echo "<img id='img_bull_non_vide_".$period["num_periode"]."_".$num_eleve."' src='../images/icons/bulletin_16.png' width='16' height='16' title='Nonempty bulletin' alt='Nonempty bulletin' />";
						}

						$sql="SELECT DISTINCT id_devoir FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE (cnd.login = '".$e_login."' AND cnd.statut='' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe = '".$current_group['id']."' AND ccn.periode = '".$period["num_periode"]."')";
						$test_cn=mysql_query($sql);
						$nb_notes_cn=mysql_num_rows($test_cn);
						if($nb_notes_cn>0) {
							echo "<img id='img_cn_non_vide_".$period["num_periode"]."_".$num_eleve."' src='../images/icons/cn_16.png' width='16' height='16' title='Carnet de notes non vide: $nb_notes_cn notes' alt='Nonempty Report card : $nb_notes_cn notes' />";
							//echo "$sql<br />";
						}

						if (in_array($e_login, (array)$current_group["eleves"][$period["num_periode"]]["list"])) {
							echo "<img src='../images/enabled.png' width='15' height='15' title='Student affected to this course' alt='Student affected to this course' />\n";
						}
						else {
							echo "<img src='../images/disabled.png' width='15' height='15' title='Student not affected to this course' alt='Student not affected to this course' />\n";
						}

						/*
						// Test sur la pr�sence de notes dans cn ou de notes/app sur bulletin
						if (!test_before_eleve_removal($e_login, $current_group['id'], $period["num_periode"])) {
							echo "<img id='img_bull_non_vide_".$period["num_periode"]."_".$num_eleve."' src='../images/icons/bulletin_16.png' width='16' height='16' title='Report card non vide' alt='Report card non vide' />";
						}

						$sql="SELECT DISTINCT id_devoir FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE (cnd.login = '".$e_login."' AND cnd.statut='' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe = '".$current_group['id']."' AND ccn.periode = '".$period["num_periode"]."')";
						$test_cn=mysql_query($sql);
						$nb_notes_cn=mysql_num_rows($test_cn);
						if($nb_notes_cn>0) {
							echo "<img id='img_cn_non_vide_".$period["num_periode"]."_".$num_eleve."' src='../images/icons/cn_16.png' width='16' height='16' title='Carnet de notes non vide: $nb_notes_cn notes' alt='Carnet de notes non vide: $nb_notes_cn notes' />";
							//echo "$sql<br />";
						}
						*/

						//=====================
						// Signaler une erreur:
						echo "<input type='checkbox' name='eleve_".$period["num_periode"]."_".$num_eleve."' id='case_".$period["num_periode"]."_".$num_eleve."' ";
						//=========================
						echo " onchange='changement();'";

						if (in_array($e_login, (array)$current_group["eleves"][$period["num_periode"]]["list"])) {
							echo " value='en_trop'";
						} else {
							echo " value='manquant'";
						}

						if ((isset($tab_sig[$period["num_periode"]]))&&(in_array($e_login, (array)$tab_sig[$period["num_periode"]]))) {
							echo " checked />";
						} else {
							echo " />";
						}
						//=====================


						//=========================
						// AJOUT: boireaus 20080229
						if($temoin_eleve_changeant_de_classe=="y") {
							$sql="SELECT c.classe FROM classes c, j_eleves_classes jec WHERE jec.login='$e_login' AND jec.id_classe=c.id AND jec.periode='".$period["num_periode"]."';";
							$res_classe_ele=mysql_query($sql);
							if(mysql_num_rows($res_classe_ele)>0){
								$lig_tmp=mysql_fetch_object($res_classe_ele);
								echo " $lig_tmp->classe";
							}
						}
						//=========================
					}
					else{
						echo "&nbsp;\n";
						//echo "<input type='hidden' name='eleve_".$period["num_periode"] . "_" . $e_login."' />\n";
					}
					echo "</td>\n";
				}
			}
	
			$elementlist = null;
			foreach ($current_group["periodes"] as $period) {
				if($period["num_periode"]!="") {
					$elementlist .= "'eleve_" . $period["num_periode"] . "_" . $e_login . "',";
				}
			}
			$elementlist = substr($elementlist, 0, -1);
	
			echo "<td><a href=\"javascript:CocheLigne($num_eleve);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:DecocheLigne($num_eleve);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a></td>\n";

			/*
			$setting = get_eleve_groupe_setting($e_login, $id_groupe, "coef");
			if (!$setting) {$setting = array(null);}
			//echo "<td><input type='text' size='3' name='setting_coef[".$num_eleve."]' value='".$setting[0]."' /></td>\n";
			echo "<td><input type='text' size='3' name='setting_coef_".$num_eleve."' value='".$setting[0]."' onchange='changement();' /></td>\n";
			*/

			//=========================
	
			echo "</tr>\n";
		}
	}

/*
	echo "<tr>\n";
	echo "<th>\n";
	echo "&nbsp;\n";
	echo "</th>\n";
	if ($multiclasses) {
		echo "<th>&nbsp;</th>\n";
	}
	echo "\n";
	foreach ($current_group["periodes"] as $period) {
		if($period["num_periode"]!="") {
			echo "<th>";
			if(count($total_eleves)>0) {
				echo "<a href=\"javascript:DecocheColonne_si_bull_et_cn_vide(".$period["num_periode"].");changement();\"><img src='../images/icons/wizard.png' width='16' height='16' alt='D�cocher les �l�ves sans note/app sur les bulletin et carnet de notes' title='D�cocher les �l�ves sans note/app sur les bulletin et carnet de notes' /></a>";
			}
			echo "</th>\n";
		}
	}
	echo "<th>&nbsp;</th>\n";
	//echo "<th>&nbsp;</th>\n";
	echo "</tr>\n";
*/

	echo "</table>\n";
	echo "<input type='hidden' name='is_posted' value='1' />\n";
	//echo "<input type='hidden' name='mode' value='" . $mode . "' />\n";
	echo "<input type='hidden' name='id_groupe' value='" . $id_groupe . "' />\n";
	//echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />\n";
	echo "<p align='center'><input type='submit' value='Save' /></p>\n";
	
	
	$nb_eleves=count($total_eleves);
	
	echo "<script type='text/javascript'>
	
	function CocheColonne(i) {
		for (var ki=0;ki<$nb_eleves;ki++) {
			if(document.getElementById('case_'+i+'_'+ki)){
				document.getElementById('case_'+i+'_'+ki).checked = true;
			}
		}
	}

	function DecocheColonne(i) {
		for (var ki=0;ki<$nb_eleves;ki++) {
			if(document.getElementById('case_'+i+'_'+ki)){
				document.getElementById('case_'+i+'_'+ki).checked = false;
			}
		}
	}
/*
	function DecocheColonne_si_bull_et_cn_vide(i) {
		for (var ki=0;ki<$nb_eleves;ki++) {
			if((document.getElementById('case_'+i+'_'+ki))&&(!document.getElementById('img_bull_non_vide_'+i+'_'+ki))&&(!document.getElementById('img_cn_non_vide_'+i+'_'+ki))) {
				document.getElementById('case_'+i+'_'+ki).checked = false;
			}
		}
	}

	function recopie_grp_ele(num) {
		tab=eval('tab_grp_ele_'+num);
		//alert('tab[0]='+tab[0]);
	
		for(j=0;j<$nb_eleves;j++) {
			DecocheLigne(j);
		}
	
		for(i=0;i<tab.length;i++) {
			for(j=0;j<$nb_eleves;j++) {
	
				if(document.getElementById('login_eleve_'+j).value==tab[i]) {
					CocheLigne(j);
				}
			}
		}
	}

	function recopie_inverse_grp_ele(num) {
		tab=eval('tab_grp_ele_'+num);
		//alert('tab[0]='+tab[0]);

		for(j=0;j<$nb_eleves;j++) {
			CocheLigne(j);
		}

		for(i=0;i<tab.length;i++) {
			for(j=0;j<$nb_eleves;j++) {
				if(document.getElementById('login_eleve_'+j).value==tab[i]) {
					DecocheLigne(j);
				}
			}
		}
	}
*/
	</script>
	";

	echo "<p><br /></p>\n";

	//echo "<a href='javascript:DecocheColonne_si_bull_et_cn_vide(1)'>1</a>";

	echo "<p><i>NOTE&nbsp;:</i></p>\n";

	echo "<p style='margin-left:3em;'>One can deregister only the students who do not have a note
nor of appreciation on the bulletins.<br />On the other hand, the presence of notes in the report card does not prevent the deregistration.</p>\n";
}
else {
	echo "</table>\n";

	echo "<p style='color:red;'>The classes associated with the course do not included any student yet.</p>\n";
}
?>
</form>
<?php require("../lib/footer.inc.php");?>
