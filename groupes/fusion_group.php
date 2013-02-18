<?php
/*
* $Id: fusion_group.php 5920 2010-11-20 21:04:58Z crob $
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


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


// Initialisation des variables utilis�es dans le formulaire
$chemin_retour=isset($_GET['chemin_retour']) ? $_GET['chemin_retour'] : (isset($_POST['chemin_retour']) ? $_POST["chemin_retour"] : NULL);

$msg="";

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

//===============================
// DEBUG:
/*
echo "<p>\$current_group['id']=".$current_group['id']."<br />\n";
echo "\$reg_nom_groupe=".$reg_nom_groupe."<br />\n";
echo "\$reg_nom_complet=".$reg_nom_complet."<br />\n";
echo "\$reg_matiere=".$reg_matiere."<br />\n";
echo "\$reg_id_classe=".$reg_id_classe."<br />\n";
for($i=0;$i<count($reg_clazz);$i++){
	echo "\$reg_clazz[$i]=".$reg_clazz[$i]."<br />\n";
}
for($i=0;$i<count($reg_professeurs);$i++){
	echo "\$reg_professeurs[$i]=".$reg_professeurs[$i]."<br />\n";
}
echo "</p>\n";
*/
//===============================


$tab_classe = isset($_POST['tab_classe']) ? $_POST['tab_classe'] : NULL;
$precclasse = isset($_POST['precclasse']) ? $_POST['precclasse'] : NULL;
$nb_classes = isset($_POST['nb_classes']) ? $_POST['nb_classes'] : NULL;
$step = isset($_POST['step']) ? $_POST['step'] : NULL;
$tab_grp = isset($_POST['tab_grp']) ? $_POST['tab_grp'] : NULL;

/*
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
*/

/*
foreach ($current_group["periodes"] as $period) {
	$reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
}
*/


if(isset($step)){
	if($step==1){
		//$clazz = array();
		//$clazz[] = $id_classe;
		if(!isset($tab_classe)){
			$tab_classe=array();
		}
		if(!in_array($id_classe,$tab_classe)){
			$tab_classe[]=$id_classe;
		}

		// On contr�le si des classes ont �t� d�coch�es...
		for($i=0;$i<$nb_classes;$i++){
			if(isset($precclasse[$i])) {
				if(!isset($tab_classe[$i])){
					// La classe $tab_classe[$i] a �t� d�coch�e.
					// Ce n'est possible que si il n'y a pas de notes associ�es

					$tmpid=$tab_classe[$i];

					unset($tabtmp);
					$tabtmp=array();
					$test=0;
					$test2=0;
					$test3=0;
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
						$sql="SELECT e.nom,e.prenom,c.classe FROM cn_notes_devoirs cnd,
																	cn_devoirs cd,
																	cn_cahier_notes ccn,
																	j_eleves_classes jec,
																	classes c,
																	eleves e
													WHERE cnd.statut!='v' AND
															cnd.id_devoir=cd.id AND
															cd.id_racine=ccn.id_cahier_notes AND
															ccn.id_groupe='$id_groupe' AND
															cnd.login=jec.login AND
															jec.login=e.login AND
															jec.id_classe=c.id AND
															c.id='$tab_classe[$i]';";
						//echo "$sql<br />\n";
						$res_test=mysql_query($sql);
						if(mysql_num_rows($res_test)>0){
							//echo "$lig_tmp->login<br />\n";
							if(!in_array($lig_tmp->login,$tabtmp)){$tabtmp[]=$lig_tmp->login;}
							$test3++;
						}
					}

					$sql="SELECT classe FROM classes WHERE id='$tmpid'";
					$res_tmp=mysql_query($sql);
					$lig_tmp=mysql_fetch_object($res_tmp);
					$clas_tmp=$lig_tmp->classe;

					//if(!$verify){
					if(($test>0)||($test2>0)||($test3>0)){
						/*
						$sql="SELECT classe FROM classes WHERE id='$tmpid'";
						$res_tmp=mysql_query($sql);
						$lig_tmp=mysql_fetch_object($res_tmp);
						$clas_tmp=$lig_tmp->classe;
						*/

						$error = true;
						$msg .= "Existing data block the suppression of the class $clas_tmp from the group.<br />\nNo note nor appreciation of the bulletin must have been typed for the students of this group to allow the suppression of the group.<br />\n";
						$msg.="No note of exam must be typed for students of the class.<br />";
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
						//$clazz[] = $tmpid;
						$tab_classe[]=$tmpid;
					}
					else{
						// On teste aussi si il y a des �l�ves de la classe dans le groupe.
						$sql="SELECT jeg.login FROM j_eleves_groupes jeg, j_eleves_classes jec WHERE
									jeg.login=jec.login AND
									jeg.periode=jec.periode AND
									jeg.id_groupe='$id_groupe' AND
									jec.id_classe='$tmpid'";
						//echo "$sql<br />\n";
						$res_ele_clas_grp=mysql_query($sql);
						if(mysql_num_rows($res_ele_clas_grp)>0){
							$error = true;
							$msg .= "Existing data block the suppression of the class $clas_tmp from the group.<br />\nNo student of the class must be registered in the group.<br />\n<a href='edit_eleves.php?id_groupe=$id_groupe&id_classe=$tmpid'>Remove the students of the group</a> first.<br />\n";
							// Et on remet la classe dans la liste des classes:
							//$clazz[] = $tmpid;
							$tab_classe[]=$tmpid;
						}
					}
				}
			}
		}
	}
	elseif($step==2){

		check_token();

		/*
		if(!isset($tab_classe)){
			$tab_classe=array();
		}
		if(!in_array($tab_classe,$id_classe)){
			$tab_classe[]=$id_classe;
		}
		*/
		$tab_classe=array();

		$tab_professeurs = array();

		$tab_eleves = array();
		foreach ($current_group["periodes"] as $period) {
			$tab_eleves[$period["num_periode"]]=array();
		}

		// DEBUG:
		/*
		for($i=0;$i<count($tab_grp);$i++) {
			if(isset($tab_grp[$i])){
				echo "\$tab_grp[$i]=$tab_grp[$i]<br />";
			}
		}
		*/

		$test=0;
		$test2=0;
		$test3=0;
		$error=false;
		for($j=0;$j<count($tab_grp);$j++) {
			// R�cup�ration des classes, professeurs, �l�ves des groupes � fusionner

			$tmp_grp=get_group($tab_grp[$j]);

			/*
			// DEBUG:
			echo "<p>\$tmp_grp['id']=".$tmp_grp['id']."<br />\n";
			echo "\$tmp_grp['name']=".$tmp_grp['name']."<br />\n";
			echo "\$tmp_grp['description']=".$tmp_grp['description']."<br />\n";
			echo "\$tmp_grp['matiere']['matiere']=".$tmp_grp['matiere']['matiere']."<br />\n";
			for($i=0;$i<count($tmp_grp['classes']['list']);$i++){
				echo "\$tmp_grp['classes']['list'][$i]=".$tmp_grp['classes']['list'][$i]."<br />\n";
			}
			for($i=0;$i<count($tmp_grp["profs"]["list"]);$i++){
				echo "\$tmp_grp['profs']['list'][$i]=".$tmp_grp['profs']['list'][$i]."<br />\n";
			}
			*/



			if($tmp_grp['id']!=$id_groupe){
				$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='".$tmp_grp['id']."';";
				//echo "$sql<br />\n";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)>0){
					$test++;
					//============
					// DEBUG
					//echo "Une ou des moyennes trouv�es.<br />";
					//============
					$error=true;
				}
				$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='".$tmp_grp['id']."';";
				//echo "$sql<br />\n";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)>0){
					$test2++;
					//============
					// DEBUG
					//echo "Une ou des appr�ciations trouv�es.<br />";
					//============
					$error=true;
				}
				$sql="SELECT 1=1 FROM cn_notes_devoirs cnd,
										cn_devoirs cd,
										cn_cahier_notes ccn
						WHERE cnd.statut!='v' AND
								cnd.id_devoir=cd.id AND
								cd.id_racine=ccn.id_cahier_notes AND
								ccn.id_groupe='".$tmp_grp['id']."';";
				//echo "$sql<br />\n";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)>0){
					$test3++;
					//============
					// DEBUG
					//echo "Une ou des notes de devoirs trouv�es.<br />";
					//============
					$error=true;
				}

				if(($test>0)||($test2>0)||($test3>0)){
					$error = true;
					$msg.="Appreciations, averages or notes of exam make it impossible to remove the group n�".$tmp_grp['id'].".<br />";
				}
			}

			foreach ($current_group["periodes"] as $period) {
				$tab_eleves[$period["num_periode"]]=array_merge($tab_eleves[$period["num_periode"]],$tmp_grp["eleves"][$period["num_periode"]]["list"]);
				array_unique($tab_eleves[$period["num_periode"]]);
				//============
				// DEBUG
				/*
				for($i=0;$i<count($tab_eleves[$period["num_periode"]]);$i++){
					echo "\$tab_eleves[\$period[\"num_periode\"]][$i]=\$tab_eleves[".$period["num_periode"]."][$i]".$tab_eleves[$period["num_periode"]][$i]."<br />";
				}
				*/
				//============
			}

			$tab_professeurs=array_merge($tab_professeurs,$tmp_grp["profs"]["list"]);

			$tab_classe=array_merge($tab_classe,$tmp_grp["classes"]["list"]);

		}

		array_unique($tab_professeurs);
		array_unique($tab_classe);

		//======================
		// DEBUG:
		/*
		for($i=0;$i<count($tab_professeurs);$i++){
			echo "\$tab_professeurs[$i]=$tab_professeurs[$i]<br />";
		}
		for($i=0;$i<count($tab_classe);$i++){
			echo "\$tab_classe[$i]=$tab_classe[$i]<br />";
		}
		*/
		//======================

		if (empty($tab_classe)) {
			$error = true;
			$msg .= "You must select at least a class.<br />\n";
		}

		if (!$error) {
			// pas d'erreur : on continue avec la mise � jour du groupe
			$create = update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $tab_classe, $tab_professeurs, $tab_eleves);
			if (!$create) {
				$msg .= "Error during update of the group.";
			} else {

				for($i=0;$i<count($tab_grp);$i++) {
					if($tab_grp[$i]!=$id_groupe){
						$sql="DELETE FROM groupes WHERE id='".$tab_grp[$i]."';";
						//echo "$sql<br />";
						$suppr=mysql_query($sql);
updateOnline($sql);
						$sql="DELETE FROM j_eleves_groupes WHERE id_groupe='".$tab_grp[$i]."';";
						//echo "$sql<br />";
						$suppr=mysql_query($sql);
updateOnline($sql);
						$sql="DELETE FROM j_groupes_classes WHERE id_groupe='".$tab_grp[$i]."';";
						//echo "$sql<br />";
						$suppr=mysql_query($sql);
updateOnline($sql);
						$sql="DELETE FROM j_groupes_matieres WHERE id_groupe='".$tab_grp[$i]."';";
						//echo "$sql<br />";
						$suppr=mysql_query($sql);
updateOnline($sql);
						$sql="DELETE FROM j_groupes_professeurs WHERE id_groupe='".$tab_grp[$i]."';";
						//echo "$sql<br />";
						$suppr=mysql_query($sql);
						updateOnline($sql);
					}
				}

				//======================================
				// MODIF: boireaus
				//$msg = "Le groupe a bien �t� mis � jour.";
				$msg = "Course ". stripslashes($reg_nom_complet) . " was indeed updated.";
				$msg = urlencode($msg);

				if(isset($chemin_retour)){
					header("Location: $chemin_retour?&msg=$msg");
				}
				else{
					if(count($tab_classe)>1){
						header("Location: ./edit_group.php?id_groupe=$id_groupe&id_classe=$id_classe&mode=regroupement&msg=$msg");
					}
					else{
						header("Location: ./edit_group.php?id_groupe=$id_groupe&id_classe=$id_classe&mode=groupe&msg=$msg");
					}
				}

				/*
				//**************
				// Pour ne pas aller plus loin le temps du DEBUG
				$titre_page = "Gestion des groupes";
				require_once("../lib/header.inc");
				die();
				//**************
				*/
				//======================================
			}
			$current_group = get_group($id_groupe);
		}
	}
}

$themessage  = 'Information was modified. Do you really Want to leave without recording ?';
//**************** EN-TETE **************************************
$titre_page = "Management of the groups";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

echo "<p class='bold'>\n";
//============================
if(isset($chemin_retour)){
	echo "<a href=\"".$_GET['chemin_retour']."\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>";
}
else{
	echo "<a href=\"edit_group.php?id_classe=$id_classe&amp;id_groupe=$id_groupe\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>";
}
//============================

echo "<h3>Fusion of groups</h3>\n";

if(!isset($tab_classe)) {

	echo "<p>Choose the classes to be associated the group:</p>\n";

	//$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
	//$sql="SELECT * FROM classes ORDER BY classe";
	$sql="SELECT DISTINCT c.* FROM classes c,
							j_groupes_classes jgc,
							j_groupes_matieres jgm
			WHERE c.id=jgc.id_classe AND
					jgc.id_groupe=jgm.id_groupe AND
					jgm.id_matiere='$reg_matiere'
			ORDER BY c.classe";
	//echo "$sql<br />";
	$call_data = mysql_query($sql);

	// REVOIR LA REQUETE POUR NE PROPOSER QUE LES CLASSES QUI ONT UN GROUPE DANS LA MEME MATIERE
	//echo "<p style='color:red;'>REVOIR LA REQUETE POUR NE PROPOSER QUE LES CLASSES QUI ONT UN GROUPE DANS LA MEME MATIERE</p>";

	$nombre_lignes = mysql_num_rows($call_data);
	if ($nombre_lignes != 0) {

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";

		$i = 0;

		echo "<table width='100%' summary='Choice of the classes'>\n";
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
				//echo "<input type='checkbox' name='classe_" . $id_classe_temp . "' id='classe_" . $id_classe_temp . "' value='yes'";
				//if($id_classe_temp!=$id_classe){
				if (!in_array($id_classe_temp, $reg_clazz)){
					echo "<label id='label_classe_".$id_classe_temp."' for='classe_".$id_classe_temp."' style='cursor: pointer;'><input type='checkbox' name='tab_classe[$i]' id='classe_" . $id_classe_temp . "' value='$id_classe_temp'";
					if (in_array($id_classe_temp, $reg_clazz)){
						echo " checked";
					}
					echo " onchange='change_style_classe($id_classe_temp);changement();'";
					echo " /> $classe</label>\n";
				}
				else{
					echo "<input type='hidden' name='tab_classe[$i]' value='$id_classe_temp' />\n";
					echo "<img src='../images/enabled.png' width='20' height='20' alt='Original class of the group' title=' original Class of the group' /> <b>$classe</b>";
				}
				if (in_array($id_classe_temp, $reg_clazz)){
					// Pour contr�ler les suppressions de classes.
					// On conserve la liste des classes pr�c�demment coch�es:
					//echo "<input type='hidden' name='precclasse_".$id_classe_temp."' value='y' />\n";
					echo "<input type='hidden' name='precclasse[$i]' value='$id_classe_temp' />\n";
				}
				echo "<br />\n";
			}
			$i++;
		}
		echo "<input type='hidden' name='nb_classes' value='$nombre_lignes' />\n";
		echo "<input type='hidden' name='step' value='1' />\n";
		//echo "</p>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<p><input type='submit' name='valider' value='Validate' /></p>\n";

		echo "</form>\n";

		echo "<script type='text/javascript'>
	function change_style_classe(num) {
		if(document.getElementById('classe_'+num)) {
			if(document.getElementById('classe_'+num).checked) {
				document.getElementById('label_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_classe_'+num).style.fontWeight='normal';
			}
		}
	}
</script>\n";

	} else {
		echo "<p>No class defined  !</p>\n";
	}
}
else {
	// Les classes sont choisies
	/*
	// On contr�le si des classes ont �t� d�coch�es...
	for($i=0;$i<$nb_classes;$i++){
		if(isset($precclasse[$i])) {
			if(!isset($tab_classe[$i])){
				// La classe $tab_classe[$i] a �t� d�coch�e.
				// Ce n'est possible que si il n'y a pas de notes associ�es


			}
		}
	}
	*/
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
	echo "<input type='hidden' name='nb_classes' value='$nb_classes' />\n";

	/*
	for($i=0;$i<$nb_classes;$i++){
		if(isset($tab_classe[$i])){
			//echo "<input type='hidden' name='tab_classe[]' value='$tab_classe[$i]' />\n";
			echo "\$tab_classe[$i]=$tab_classe[$i]<br />\n";
		}
	}
	*/

	// On va proposer les groupes � associer (m�me mati�re)
	$sql="SELECT id_matiere FROM j_groupes_matieres WHERE id_groupe='$id_groupe';";
	$res_mat=mysql_query($sql);
	$lig_tmp=mysql_fetch_object($res_mat);
	$id_matiere=$lig_tmp->id_matiere;

	echo "<p>Cocher les groupes � fusionner avec $reg_nom_complet (<i>$reg_nom_groupe</i>)</p>\n";

	//sort($tab_classe);
	//array_unique($tab_classe);
	$tab_dedoub=array();

	echo "<table class='boireaus' summary='Groups'>\n";
	echo "<tr>\n";
	echo "<th>Classe</th>\n";
	echo "<th>Groupe</th>\n";
	echo "</tr>\n";
	$alt=1;
	for($i=0;$i<$nb_classes;$i++){
		if(isset($tab_classe[$i])){
			if(!in_array($tab_classe[$i],$tab_dedoub)){
				$tab_dedoub[]=$tab_classe[$i];
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<td>\n";
				$sql="SELECT classe FROM classes WHERE id='$tab_classe[$i]';";
				$res_clas=mysql_query($sql);
				$lig_tmp=mysql_fetch_object($res_clas);
				echo $lig_tmp->classe;
				echo "</td>\n";

				echo "<td style='text-align: left;'>\n";
				$sql="SELECT g.* FROM j_groupes_classes jgc,
									j_groupes_matieres jgm,
									groupes g
								WHERE jgc.id_groupe=jgm.id_groupe AND
										jgc.id_classe='$tab_classe[$i]' AND
										jgm.id_matiere='$id_matiere' AND
										jgc.id_groupe=g.id;";
				//echo "$sql<br />";
				$res_grp=mysql_query($sql);
				$cpt=0;
				while($lig_tmp=mysql_fetch_object($res_grp)){
					if($cpt>0){
						echo "<br />\n";
					}
          $cpt2=0;
  				$liste_profs='';
          $sql_profs="SELECT u.nom nom, u.prenom prenom from j_groupes_professeurs j, utilisateurs u
								WHERE j.id_groupe='".$lig_tmp->id."'
                and j.login=u.login
                ";
  				$res_profs=mysql_query($sql_profs);
  				while($lig_profs=mysql_fetch_object($res_profs)){
  					if($cpt2>0){
  						$liste_profs .= ", \n";
  					}
  					$liste_profs .= $lig_profs->nom." ".$lig_profs->prenom;
  					$cpt2++;
  				}
					if($lig_tmp->id==$id_groupe){
						echo "<input type='hidden' name='tab_grp[]' value='$lig_tmp->id' />";
						echo "<img src='../images/enabled.png' width='20' height='20' alt='Groupe original' title='Groupe original' />";
						echo $lig_tmp->description." (<i>".$lig_tmp->name."</i>)";
						echo " (<i>".$liste_profs."</i>)";
					}
					else{
						echo "<label for='tab_grp_".$i."_".$cpt."' style='cursor: pointer;'><input type='checkbox' id='tab_grp_".$i."_".$cpt."' name='tab_grp[]' value='$lig_tmp->id' />";
						echo $lig_tmp->description." (<i>".$lig_tmp->name."</i>)";
						echo " (<i>".$liste_profs."</i>)";
						echo "</label>\n";
					}
					$cpt++;
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
	}
	echo "</table>\n";
	echo "<input type='hidden' name='step' value='2' />\n";
	echo "<p><input type='submit' name='valider' value='Validate' /></p>\n";
	echo "</form>\n";

}

echo "<p><br /></p>\n";
echo "<p><i>NOTES:</i></p>\n";
echo "<ul>\n";
echo "<li>It is possible to merge groups only if no note is yet typed for the groups joining the selected group.</li>\n";
echo "<li>It is possible to merge groups only for one same course.</li>\n";
echo "</ul>\n";

require("../lib/footer.inc.php");
die();
?>
