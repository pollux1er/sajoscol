<?php
/*
* $Id: edit_group.php 8350 2011-09-24 15:20:10Z crob $
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

// Initialisation des variables utilisées dans le formulaire

$chemin_retour=isset($_GET['chemin_retour']) ? $_GET['chemin_retour'] : (isset($_POST['chemin_retour']) ? $_POST["chemin_retour"] : NULL);
$ancre=isset($_GET['ancre']) ? $_GET['ancre'] : (isset($_POST['ancre']) ? $_POST["ancre"] : NULL);

$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
$id_groupe = isset($_GET['id_groupe']) ? $_GET['id_groupe'] : (isset($_POST['id_groupe']) ? $_POST["id_groupe"] : NULL);

if (!is_numeric($id_groupe)) {$id_groupe = 0;}
$current_group = get_group($id_groupe);
$reg_nom_groupe = $current_group["name"];
$reg_nom_complet = $current_group["description"];
$reg_matiere = $current_group["matiere"]["matiere"];
$reg_id_classe = $current_group["classes"]["list"][0];
$reg_clazz = $current_group["classes"]["list"];
$reg_professeurs = (array)$current_group["profs"]["list"];

/*
foreach($reg_clazz as $key => $value) {
echo "\$reg_clazz[$key]=$value<br />";
}
*/

$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_POST['mode']) ? $_POST["mode"] : null);
if ($mode == null and $id_classe == null) {
	$mode = "groupe";
} else if ($mode == null and $current_group) {
	if (count($current_group["classes"]["list"]) > 1) {
		$mode = "regroupement";
	} else {
		$mode = "groupe";
	}
}

foreach ($current_group["periodes"] as $period) {
	$reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
}

if (isset($_POST['is_posted'])) {
	check_token();

	$msg="";
	$error = false;
	//=======================================
	// MODIF: boireaus
	/*
	$reg_nom_groupe = $_POST['groupe_nom_court'];
	$reg_nom_complet = $_POST['groupe_nom_complet'];
	*/
	$reg_nom_groupe = html_entity_decode_all_version($_POST['groupe_nom_court']);
	$reg_nom_complet = html_entity_decode_all_version($_POST['groupe_nom_complet']);
	//=======================================
	$reg_matiere = $_POST['matiere'];

	if (empty($reg_nom_groupe)) {
		$error = true;
		$msg .= "You must give a short name to the group.<br />\n";
	}

	if (empty($reg_nom_groupe)) {
		$error = true;
		$msg .= "You must give a complete name to the group.<br />\n";
	}

	$clazz = array();

	// Classes

	if ($_POST['mode'] == "groupe") {
		// Ajout sécurité:
		if((!isset($id_classe))||($id_classe=='')) {$id_classe=$current_group['classes']['list'][0];}

		$clazz[] = $id_classe;
		$reg_id_classe = $id_classe;
		$mode = "groupe";
	} else if ($_POST['mode'] == "regroupement") {
		$mode = "regroupement";
		foreach ($_POST as $key => $value) {
			if (preg_match("/^classe\_/", $key)) {
				$temp = explode("_", $key);
				$id = $temp[1];
				$clazz[] = $id;
			}
		}



		foreach ($_POST as $key => $value) {
			if (preg_match("/^precclasse\_/", $key)) {
				$temp = explode("_", $key);
				$tmpid = $temp[1];
				// On vérifie si la classe a été décochée:
				if(!isset($_POST['classe_'.$tmpid])){
					// On vérifie si l'identifiant de classe $tmpid peut être décoché.
		
					unset($tabtmp);
					$tabtmp=array();
					$test=0;
					$test2=0;
					$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$tmpid'";
					$res_tmp=mysql_query($sql);
					while($lig_tmp=mysql_fetch_object($res_tmp)){
						$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='$id_groupe' AND login='$lig_tmp->login'";
						//echo "$sql<br />\n";
						$res_test=mysql_query($sql);
						if(mysql_num_rows($res_test)>0){
							//echo "$lig_tmp->login<br />\n";
							if(!in_array($lig_tmp->login,$tabtmp)){$tabtmp[]=$lig_tmp->login;}
							$test++;
						}
						$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='$id_groupe' AND login='$lig_tmp->login'";
						//echo "$sql<br />\n";
						$res_test=mysql_query($sql);
						if(mysql_num_rows($res_test)>0){
							//echo "$lig_tmp->login<br />\n";
							if(!in_array($lig_tmp->login,$tabtmp)){$tabtmp[]=$lig_tmp->login;}
							$test2++;
						}
					}
		
					$sql="SELECT classe FROM classes WHERE id='$tmpid'";
					$res_tmp=mysql_query($sql);
					$lig_tmp=mysql_fetch_object($res_tmp);
					$clas_tmp=$lig_tmp->classe;
		
					//if(!$verify){
					if(($test>0)||($test2>0)){
						/*
						$sql="SELECT classe FROM classes WHERE id='$tmpid'";
						$res_tmp=mysql_query($sql);
						$lig_tmp=mysql_fetch_object($res_tmp);
						$clas_tmp=$lig_tmp->classe;
						*/
		
						$error = true;
						$msg .= "Existing data block the suppression of the class $clas_tmp of the group.<br />\nNo note nor appreciation of the bulletin must have been typed for
the students of this group to allow the suppression of the group.<br />\n";
						if(count($tabtmp)==1){
							$msg.="The student having averages or appreciations typed is $tabtmp[0].<br />\n";
						}
						else{
							$msg.="The students having averages or appreciations typed are $tabtmp[0]";
							for($i=1;$i<count($tabtmp);$i++){
								$msg.=", $tabtmp[$i]";
							}
							$msg.=".<br />\n";
						}
						// Et on remet la classe dans la liste des classes:
								$clazz[] = $tmpid;
					}
					else{
						// On teste aussi si il y a des élèves de la classe dans le groupe.
						$sql="SELECT jeg.login FROM j_eleves_groupes jeg, j_eleves_classes jec WHERE
									jeg.login=jec.login AND
									jeg.periode=jec.periode AND
									jeg.id_groupe='$id_groupe' AND
									jec.id_classe='$tmpid'";
						//echo "$sql<br />\n";
						$res_ele_clas_grp=mysql_query($sql);
						if(mysql_num_rows($res_ele_clas_grp)>0){
							$error = true;
							$msg .= "Existing data block the suppression of the class $clas_tmp of the group.<br />\nNo student of the class must be registered in the group.<br />\n<a href='edit_eleves.php?id_groupe=$id_groupe&id_classe=$tmpid'>Remove the students of the group</a> before.<br />\n";
							// Et on remet la classe dans la liste des classes:
							$clazz[] = $tmpid;
						}
					}
				}
			}
		}


	}

	// On ajoute un test pour s'assurer qu'on n'a pas un tableau vide pour les classes
	if (count($clazz) == 0) {
		$clazz[0] = $id_classe;
	}

	// Professeurs
	$reg_professeurs = array();
	foreach ($_POST as $key => $value) {
		if (preg_match("/^prof\_/", $key)) {
			$id = preg_replace("/^prof\_/", "", $key);
			$proflogin = $_POST["proflogin_".$id];
			// Normalement on a un traitement anti-injection sur $_POST, donc pas de soucis.
			// Mais ça serait bien de faire un test quand même. Si un dev passe par là...
			//$reg_professeurs[] = $proflogin;

			$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_professeur='$proflogin' AND id_matiere='$reg_matiere';";
			$test_prof_matiere=mysql_query($sql);
			if(mysql_num_rows($test_prof_matiere)>0) {
				$reg_professeurs[] = $proflogin;
			}
		}
	}

	$reg_clazz = $clazz;

	/*
	echo "Apres modif:<br />";
	foreach($reg_clazz as $key => $value) {
		echo "\$reg_clazz[$key]=$value<br />";
	}
	*/

	if (empty($reg_clazz)) {
		$error = true;
		$msg .= "You must select at least a class.<br />\n";
	}

	if (!$error) {
		// pas d'erreur : on continue avec la mise à jour du groupe
		$create = update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);
		if (!$create) {
			$msg .= "Error during update of the group.";
		} else {
			//======================================
			// MODIF: boireaus
			//$msg = "Le groupe a bien été mis à jour.";
			$msg = "Course ". stripslashes($reg_nom_complet) . " was updated.";
			$msg = urlencode($msg);

			if(isset($chemin_retour)) {
				if(strstr($chemin_retour,'utilisateurs/index.php')) {
					// On n'arrive sur edit_group.php en venant de utilisateurs/index.php que depuis la partie Gestion de comptes utilisateurs Personnels de l'établissement
					if(isset($ancre)) {
						header("Location: $chemin_retour?&msg=$msg&mode=personnels#$ancre");
					}
					else {
						header("Location: $chemin_retour?&msg=$msg&mode=personnels");
					}
				}
				else {
					header("Location: $chemin_retour?&msg=$msg");
				}
			}
			else{
				header("Location: ./edit_class.php?id_classe=$id_classe&msg=$msg");
			}
			//======================================
		}
		$current_group = get_group($id_groupe);
	}
}
/* DEBUG
echo "<pre>\n";
print_r($_POST);
echo "</pre>\n";
echo html_entity_decode_all_version("prof_ERIC_ALARY");

echo "<pre>\n";
print_r($current_group);
echo "</pre>\n";

echo "<pre>\n";
print_r($reg_professeurs);
echo "</pre>\n";
*/

$themessage  = 'Information was modified. Do you really want to leave without recording ?';
//**************** EN-TETE **************************************
$titre_page = "Management of groups";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

//debug_var();

//echo "\$_SERVER['HTTP_REFERER']=".$_SERVER['HTTP_REFERER']."<br />\n";

/*
foreach ($reg_clazz as $tmp_classe) {
	echo "\$tmp_classe=$tmp_classe<br />\n";
}
*/
?>
<p class="bold">
<?php
//============================
// MODIF: boireaus
//if(isset($_GET['chemin_retour'])){
if(isset($chemin_retour)){
	echo "<a href=\"".$_GET['chemin_retour']."\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a> | ";
}
else{
	echo "<a href=\"edit_class.php?id_classe=$id_classe\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a> | ";
}
//============================

echo "<a href='mes_listes.php?id_groupe=$id_groupe'>Export the composition of the group</a> | ";
?>
<a href="edit_class.php?id_classe=<?php echo $id_classe;?>&amp;action=delete_group&amp;id_groupe=<?php echo $id_groupe;?><?php echo add_token_in_url();?>" onclick="return confirmlink(this, 'CAUTION !!! READ THIS WARNING : The suppression of a course is irreversible. Such a suppression should not take place in the course of year. If it is the case, that can involve the presence of orphan data in the
base. If official data (notes and appreciations of the bulletin) are present, the suppression will be blocked. In the contrary case, all the data related to the group will be removed, including the notes typed by the professors in the report card
as well as the data present in the textbook. Are you REALLY SURE you want to continue ?', 'Confirmation of the suppression')"> Delete the group</a>
<?php
if ($mode == "groupe") {
	echo "<h3>Modifier le groupe</h3>\n";
} elseif ($mode == "regroupement") {
	echo "<h3>Modifier le regroupement</h3>\n";
}
?>
<form enctype="multipart/form-data" action="edit_group.php" method="post">
<div style="width: 95%;">
<div style="width: 45%; float: left;">
<p>Short name : <input type=text size=30 name=groupe_nom_court value = "<?php echo $reg_nom_groupe; ?>" /></p>

<p>Complete name : <input type=text size=50 name=groupe_nom_complet value = "<?php echo $reg_nom_complet; ?>" /></p>

<?php

echo add_token_field();

// Classes

if ($mode == "groupe") {
	echo "<p>\n";
	if((isset($current_group))&&(count($current_group["eleves"]["all"]["list"])==0)) {
		echo "Sélectionnez la classe à laquelle appartient le groupe :\n";
		echo "<select name='id_classe' size='1'";
		echo " onchange='changement();'";
		echo ">\n";
	
		$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
		$nombre_lignes = mysql_num_rows($call_data);
		if ($nombre_lignes != 0) {
			$i = 0;
			while ($i < $nombre_lignes){
				$id_classe2 = mysql_result($call_data, $i, "id");
				$classe = mysql_result($call_data, $i, "classe");
				if (get_period_number($id_classe2) != "0") {
					echo "<option value='" . $id_classe2 . "'";
					if (in_array($id_classe2, $reg_clazz)) echo " SELECTED";
					echo ">$classe</option>\n";
				}
			$i++;
			}
		} else {
			echo "<option value='false'>No class defined  !</option>\n";
		}
		echo "</select>\n";
		//echo "<br />[-> <a href='edit_group.php?id_classe=".$id_classe."&id_groupe=".$id_groupe."&mode=regroupement'>sélectionner plusieurs classes</a>]</p>\n";
		echo "<br />\n";
	}
	else {
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		if(isset($current_group)) {
			echo "Enseignement en <b>".$current_group['classlist_string']."</b>.";
			echo "<br />\n";
		}
	}

	echo "[-> <a href='edit_group.php?id_classe=".$id_classe."&amp;id_groupe=".$id_groupe."&amp;mode=regroupement'>select several classes</a>]\n";

	// On ne propose de fusionner le groupe avec un/des groupes existants que si le groupe n'a pas déjà de notes,...
	// ... NON: On fera le test sur les groupes à y associer seulement.
	//          Ce sont les autres groupes qui seraient susceptibles de voir leurs notes disparaitre
	echo "<br />[-> <a href='fusion_group.php?id_classe=".$id_classe."&amp;id_groupe=".$id_groupe."'>merge the group with existing groups</a>]";

	// AJOUTER UN TEST: sur le fait que le groupe est vide...
	//echo "<br />[-> <a href='scinder_group.php?id_classe=".$id_classe."&amp;id_groupe=".$id_groupe."'>scinder le groupe</a>]";

	echo "</p>\n";

} else if ($mode == "regroupement") {
	echo "<input type='hidden' name='id_classe' value='".$id_classe."' />\n";
	echo "<p>Select the classes to which the regrouping belongs :";

	//$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
	//$sql="SELECT * FROM classes c, periodes p WHERE p.id_classe=c.id AND MAX(p.num_periode)='".get_period_number($id_classe)."' ORDER BY classe;";
	$sql="SELECT * FROM classes ORDER BY classe;";
	//echo "$sql<br />";
	$call_data = mysql_query($sql);
	$nombre_lignes = mysql_num_rows($call_data);
	if ($nombre_lignes != 0) {

		$i = 0;

		$tmp_tab_classe=array();
		$tmp_tab_id_classe=array();
		while ($i < $nombre_lignes){
			$id_classe_temp=mysql_result($call_data, $i, "id");
			$classe=mysql_result($call_data, $i, "classe");
			if (get_period_number($id_classe_temp) == get_period_number($id_classe)) {
				$tmp_tab_classe[]=$classe;
				$tmp_tab_id_classe[]=$id_classe_temp;
			}
			$i++;
		}

		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='left'>\n";
		echo "<td>\n";
		//$nb_class_par_colonne=round($nombre_lignes/3);
		$nb_class_par_colonne=round(count($tmp_tab_classe)/3);
		for($i=0;$i<count($tmp_tab_classe);$i++) {
			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				echo "<td>\n";
			}

			$id_classe_temp=$tmp_tab_id_classe[$i];
			$classe=$tmp_tab_classe[$i];

			//echo "<br /><input type='checkbox' name='classe_" . $id_classe_temp . "' value='yes'";
			echo "<input type='checkbox' name='classe_" . $id_classe_temp . "' id='classe_" . $id_classe_temp . "' value='yes'";
			if (in_array($id_classe_temp, $reg_clazz)){
				echo " checked";
			}
			//echo " />$classe</option>\n";
			echo " onchange='changement();'";
			echo " /><label for='classe_".$id_classe_temp."' style='cursor: pointer;'>$classe</label>\n";
			if (in_array($id_classe_temp, $reg_clazz)){
				// Pour contrôler les suppressions de classes.
				// On conserve la liste des classes précédemment cochées:
				echo "<input type='hidden' name='precclasse_".$id_classe_temp."' value='y' />\n";
			}
			echo "<br />\n";
		}

		/*
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='left'>\n";
		echo "<td>\n";
		$nb_class_par_colonne=round($nombre_lignes/3);
		while ($i < $nombre_lignes){
			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				echo "<td>\n";
			}

			$id_classe_temp = mysql_result($call_data, $i, "id");
			$classe = mysql_result($call_data, $i, "classe");
			if (get_period_number($id_classe_temp) == get_period_number($id_classe)) {
				//echo "<br /><input type='checkbox' name='classe_" . $id_classe_temp . "' value='yes'";
				echo "<input type='checkbox' name='classe_" . $id_classe_temp . "' id='classe_" . $id_classe_temp . "' value='yes'";
				if (in_array($id_classe_temp, $reg_clazz)){
					echo " checked";
				}
				//echo " />$classe</option>\n";
				echo " onchange='changement();'";
				echo " /><label for='classe_".$id_classe_temp."' style='cursor: pointer;'>$classe</label>\n";
				if (in_array($id_classe_temp, $reg_clazz)){
					// Pour contrôler les suppressions de classes.
					// On conserve la liste des classes précédemment cochées:
					echo "<input type='hidden' name='precclasse_".$id_classe_temp."' value='y' />\n";
				}
				echo "<br />\n";
			}
			$i++;
		}
		*/
		//echo "</p>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		// On ne propose de fusionner le groupe avec un/des groupes existants que si le groupe n'a pas déjà de notes,...
		// ... NON: On fera le test sur les groupes à y associer seulement.
		//          Ce sont les autres groupes qui seraient susceptibles de voir leurs notes disparaitre
		echo "<p>[-> <a href='fusion_group.php?id_classe=".$id_classe."&amp;id_groupe=".$id_groupe."'>merge the group with existing groups</a>]";

		echo "</p>\n";

	} else {
		echo "<p>No class defined!</p>\n";
	}
}

//-- Fin classes


?>



<!--p>Sélectionnez la matière enseignée à ce groupe :-->
<?php
/*
$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
$nb_mat = mysql_num_rows($query);

echo "<select name='matiere' size='1'>\n";

for ($i=0;$i<$nb_mat;$i++) {
	$matiere = mysql_result($query, $i, "matiere");
	$nom_matiere = mysql_result($query, $i, "nom_complet");
	echo "<option value='" . $matiere . "'";
	if ($reg_matiere == $matiere) echo " SELECTED";
	//echo ">" . $nom_matiere . "</option>\n";
	echo ">" . htmlentities($nom_matiere) . "</option>\n";
}
echo "</select>\n";
//echo "</p>\n";
*/
echo "</div>\n";
// Edition des professeurs
echo "<div style='width: 45%; float: right;'>\n";

//=================================================
echo "<p>Select the course taught to this group : ";

$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
$nb_mat = mysql_num_rows($query);

echo "<select name='matiere' size='1'";
echo " onchange='changement();'";
echo ">\n";

for ($i=0;$i<$nb_mat;$i++) {
	$matiere = mysql_result($query, $i, "matiere");
	$nom_matiere = mysql_result($query, $i, "nom_complet");
	echo "<option value='" . $matiere . "'";
	if ($reg_matiere == $matiere) echo " SELECTED";
	//echo ">" . $nom_matiere . "</option>\n";
	//echo ">" . html_entity_decode($nom_matiere) . "</option>\n";
	echo ">" . htmlentities($nom_matiere) . "</option>\n";
}
echo "</select>\n";
echo "</p>\n";
//=================================================

// Mettre un témoin pour repérer le prof principal

$tab_prof_suivi=array();
$nb_prof_suivi=0;
if(isset($id_classe)) {
	$tab_prof_suivi=get_tab_prof_suivi($id_classe);
	$nb_prof_suivi=count($tab_prof_suivi);
	if($nb_prof_suivi>1) {
		$liste_prof_suivi="";
		for($loop=0;$loop<count($tab_prof_suivi);$loop++) {
			if($loop>0) {$liste_prof_suivi.=", ";}
			$liste_prof_suivi.=civ_nom_prenom($tab_prof_suivi[$loop]);
		}
	}
}

echo "<p>Select the professors who take part in this course : </p>\n";

//$calldata = mysql_query("SELECT u.login, u.nom, u.prenom, u.civilite FROM utilisateurs u, j_professeurs_matieres j WHERE (j.id_matiere = '$reg_matiere' and j.id_professeur = u.login and u.etat!='inactif') ORDER BY u.login");
$sql="SELECT u.login, u.nom, u.prenom, u.civilite, u.statut FROM utilisateurs u, j_professeurs_matieres j WHERE (j.id_matiere = '$reg_matiere' and j.id_professeur = u.login and u.etat!='inactif') ORDER BY u.nom;";
//echo "$sql<br />";
$calldata = mysql_query($sql);
$nb = mysql_num_rows($calldata);
$prof_list = array();
$prof_list["list"] = array();
for ($i=0;$i<$nb;$i++) {
	$prof_login = mysql_result($calldata, $i, "login");
	$prof_nom = mysql_result($calldata, $i, "nom");
	$prof_prenom = mysql_result($calldata, $i, "prenom");
	$civilite = mysql_result($calldata, $i, "civilite");
	$prof_statut = mysql_result($calldata, $i, "statut");

	$prof_list["list"][] = $prof_login;
	//$prof_list["users"][$prof_login] = array("login" => $prof_login, "nom" => $prof_nom, "prenom" => $prof_prenom, "civilite" => $civilite);
	$prof_list["users"][$prof_login] = array("login" => $prof_login, "nom" => $prof_nom, "prenom" => $prof_prenom, "civilite" => $civilite, "statut" => $prof_statut);
}

if (count($prof_list["list"]) == "0") {
	echo "<p><font color='red'>ERROR !</font> No professor was defined as qualified in the course considered.</p>\n";
} else {
	$total_profs = array_merge($prof_list["list"], $reg_professeurs);
	$total_profs = array_unique($total_profs);

	$p = 0;
	echo "<table class='boireaus'>\n";
	$alt=1;
	$temoin_nettoyage_requis='n';
	foreach($total_profs as $prof_login) {
		$alt=$alt*(-1);
		if((isset($prof_list["users"][$prof_login]["statut"]))&&($prof_list["users"][$prof_login]["statut"]=='professeur')) {
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input type='hidden' name='proflogin_".$p."' value='".$prof_login."' />\n";
			echo "<input type='checkbox' name='prof_".$p."' id='prof_".$p."' ";
			echo "onchange='checkbox_change($p);changement();'";
			if (in_array($prof_login, $reg_professeurs)) {
				if (array_key_exists($prof_login, $current_group["profs"]["users"])){
					echo " checked />\n";
					echo "</td>\n";
					echo "<td style='text-align:left;'>\n";
					echo "<label id='civ_nom_prenom_prof_$p' for='prof_".$p."' style='cursor: pointer;'>". $current_group["profs"]["users"][$prof_login]["civilite"] . " " .
						casse_mot($current_group["profs"]["users"][$prof_login]["prenom"],'majf2') . " " .
						$current_group["profs"]["users"][$prof_login]["nom"] . "</label>\n";
				} else {
					echo " checked />\n";
					echo "</td>\n";
					echo "<td style='text-align:left;'>\n";
					echo "<label id='civ_nom_prenom_prof_$p' for='prof_".$p."' style='cursor: pointer;'>". $prof_list["users"][$prof_login]["civilite"] . " " .
						casse_mot($prof_list["users"][$prof_login]["prenom"],'majf2') . " " .
						$prof_list["users"][$prof_login]["nom"] . "</label>\n";
				}
			} else {
				echo " />\n";
				echo "</td>\n";
				echo "<td style='text-align:left;'>\n";
				echo "<label id='civ_nom_prenom_prof_$p' for='prof_".$p."' style='cursor: pointer;'>". $prof_list["users"][$prof_login]["civilite"] . " " .
						casse_mot($prof_list["users"][$prof_login]["prenom"],'majf2') . " " .
						$prof_list["users"][$prof_login]["nom"] . "</label>";
			}

			if(in_array($prof_login,$tab_prof_suivi)) {
				echo " <img src='../images/bulle_verte.png' width='9' height='9' title=\"Principal professor of at least a student of the class on one of the periods.";
				if($nb_prof_suivi>1) {echo " La liste des ".getSettingValue('prof_suivi')." est ".$liste_prof_suivi.".";}
				echo "\" />\n";
			}

			echo "<br />\n";

			echo "</td>\n";
			echo "</tr>\n";
			$p++;
		}
		else {
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "&nbsp;&nbsp;";
			echo "</td>\n";
			echo "<td style='text-align:left;'>\n";
			echo "<b>ANOMALY</b>&nbsp;:";
			//echo " " . $prof_list["users"][$prof_login]["nom"] . " " . $prof_list["users"][$prof_login]["prenom"];
			echo " <a href='../utilisateurs/modify_user.php?user_login=$prof_login'  onclick=\"return confirm_abandon (this, change, '$themessage')\">".civ_nom_prenom($prof_login)."</a>";
			if(isset($prof_list["users"][$prof_login]["statut"])) {
				echo " (<i style='color:red'>compte ".$prof_list["users"][$prof_login]["statut"]."</i>)";
			}
			echo "<br />\n";
			$temoin_nettoyage_requis='y';
			//echo "Un <a href='../utilitaires/clean_tables.php'>nettoyage des tables</a> s'impose.";
			echo "</td>\n";
			echo "</tr>\n";
		}
	}
	echo "</table>\n";
	if($temoin_nettoyage_requis!='n') {
		echo "Un <a href='../utilitaires/clean_tables.php'>cleaning of the tables</a> s'impose.";
	}

	echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('prof_'+cpt)) {
		if(document.getElementById('prof_'+cpt).checked) {
			document.getElementById('civ_nom_prenom_prof_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('civ_nom_prenom_prof_'+cpt).style.fontWeight='normal';
		}
	}
}

for(i=0;i<$p;i++) {
	checkbox_change(i);
}
</script>\n";

}

echo "</div>\n";
echo "<div style='float: left; width: 100%'>\n";
echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='mode' value='" . $mode . "' />\n";
echo "<input type='hidden' name='id_groupe' value='" . $id_groupe . "' />\n";
//============================
// MODIF: boireaus
if(isset($chemin_retour)){
	echo "<input type='hidden' name='chemin_retour' value='$chemin_retour' />\n";
}
if(isset($ancre)){
	echo "<input type='hidden' name='ancre' value='$ancre' />\n";
}
echo "<p align='center'><input type='submit' value='Validate' /></p>\n";
echo "</div>\n";
echo "</div>\n";

echo "</form>\n";

require("../lib/footer.inc.php");
?>
