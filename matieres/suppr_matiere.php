<?php
@set_time_limit(0);
/*
 * $Id: suppr_matiere.php 5907 2010-11-19 20:30:52Z crob $
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
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

//INSERT INTO `droits` VALUES ('/matieres/suppr_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression d une matiere', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : NULL);
$confirmation_suppr=isset($_POST['confirmation_suppr']) ? $_POST['confirmation_suppr'] : (isset($_GET['confirmation_suppr']) ? $_GET['confirmation_suppr'] : NULL);

//**************** EN-TETE *****************
$titre_page = "Suppression of a course";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return to home</a> | <a href='index.php'>Return to the management of the courses</a></p>\n";

echo "<h2>Suppression of a course</h2>\n";

if(!isset($matiere)) {
	echo "<p>No course was selected.</p>\n";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
}

$sql="SELECT * FROM matieres WHERE matiere='$matiere';";
$res_mat=mysql_query($sql);
if(mysql_num_rows($res_mat)==0) {
	echo "<p>Course '$matiere' do not exist in the table 'matieres'.</p>\n";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
}

if(!isset($confirmation_suppr)) {
	echo "<p>You want to remove the course '$matiere'.<br />\n";

	$sql="SELECT id_groupe FROM j_groupes_matieres WHERE id_matiere='$matiere';";
	$res_grp=mysql_query($sql);

	$nb_grp=mysql_num_rows($res_grp);
	if($nb_grp==0) {
		echo "It is associated no group.</p>\n";
	}
	elseif($nb_grp==1) {
		echo "It is associated a group.<br />\n";
	}
	else {
		echo "It is associated to $nb_grp groups.<br />\n";
	}

	if($nb_grp>0) {
		$nb_notes_app=0;
		while($lig_grp=mysql_fetch_object($res_grp)) {
			// Rechercher les groupes associés à des notes...
			if(test_before_group_deletion($lig_grp->id_groupe)) {
				$nb_notes_app++;
			}
		}
		if ($nb_notes_app==0) {
			echo "The group are not associated to any note/appreciation on a bulletin.</p>\n";
		}
		elseif ($nb_notes_app==1) {
			echo "The group or one of the groups is associated any note/appreciation on a bulletin.<br />You should not remove the course.</p>\n";
		}
		else {
			echo "The group or the groups is associated notes/appreciations on bulletins.<br />You should not remove the course.</p>\n";
		}
	}


	// Formulaire de confirmation de suppression
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='matiere' value=\"$matiere\" />\n";
	echo "<p><input type='submit' name='confirmation_suppr' value='Remove the course' /></p>\n";
	echo "</form>\n";


}
else {
	check_token();

	// Suppression proprement dite... avec une boucle sur les groupes pour ne pas risquer un timeout
	// Et finir par la suppression de la matière

	/*
	$sql="CREATE TABLE IF NOT EXISTS temp_suppr_matiere (
	id int(11) NOT NULL auto_increment,
	col1 VARCHAR(255) NOT NULL,
	col2 TEXT,
	PRIMARY KEY  (id)
	);";
	$create_table=mysql_query($sql);

	$sql="TRUNCATE temp_suppr_matiere;";
	$nettoyage=mysql_query($sql);
	*/

	$sql="SELECT id_groupe FROM j_groupes_matieres WHERE id_matiere='$matiere' LIMIT 1;";
	$res_grp=mysql_query($sql);

	$nb_grp=mysql_num_rows($res_grp);
	if($nb_grp==0) {
		echo "<p>All groups (*) associated to the course $matiere are removed.<br />(*) and associated records.</p>\n";

		echo "<p>\n";

		// Il reste à nettoyer:
		// - j_professeurs_matieres

		$sql="SELECT * FROM j_professeurs_matieres WHERE id_matiere='$matiere';";
		$res_jpm=mysql_query($sql);
		$nb_jpm=mysql_num_rows($res_jpm);
		if($nb_jpm>0) {
			echo "Suppression of $nb_jpm association(s) professor/course: ";
			$sql="DELETE FROM j_professeurs_matieres WHERE id_matiere='$matiere';";
			$res_jpm=mysql_query($sql);
			if($res_jpm) {
				echo "<span style='color:green;'>OK</span><br />\n";
			}
			else {
				echo "<span style='color:red;'>Error</span><br />\n";
			}
		}

		// - aid
		$sql="SELECT * FROM aid WHERE matiere1='$matiere';";
		$res_aid=mysql_query($sql);
		$nb_aid=mysql_num_rows($res_aid);
		if($nb_aid>0) {
			echo "Suppression of $nb_aid association(s) ida/course1: ";
			$sql="UPDATE aid SET matiere1='' WHERE matiere1='$matiere';";
			$res_aid=mysql_query($sql);
			if($res_aid) {
				echo "<span style='color:green;'>OK</span><br />\n";
			}
			else {
				echo "<span style='color:red;'>Error</span><br />\n";
			}
		}

		$sql="SELECT * FROM aid WHERE matiere2='$matiere';";
		$res_aid=mysql_query($sql);
		$nb_aid=mysql_num_rows($res_aid);
		if($nb_aid>0) {
			echo "Suppression of $nb_aid association(s) aid/course2: ";
			$sql="UPDATE aid SET matiere2='' WHERE matiere2='$matiere';";
			$res_aid=mysql_query($sql);
			if($res_aid) {
				echo "<span style='color:green;'>OK</span><br />\n";
			}
			else {
				echo "<span style='color:red;'>Error</span><br />\n";
			}
		}

		// - observatoire
		$test_existence=mysql_query("SHOW TABLES LIKE 'observatoire';");
		if(mysql_num_rows($test_existence)>0){
			$sql="SELECT * FROM observatoire WHERE matiere='$matiere';";
			$res_obs=mysql_query($sql);
			$nb_obs=mysql_num_rows($res_obs);
			if($nb_obs>0) {
				echo "Suppression of $nb_obs association(s) observatory/course: ";
				$sql="DELETE FROM observatoire WHERE matiere='$matiere';";
				$res_obs=mysql_query($sql);
				if($res_obs) {
					echo "<span style='color:green;'>OK</span><br />\n";
				}
				else {
					echo "<span style='color:red;'>Error</span><br />\n";
				}
			}
		}

		// - observatoire_comment
		$test_existence=mysql_query("SHOW TABLES LIKE 'observatoire_comment';");
		if(mysql_num_rows($test_existence)>0){
			$sql="SELECT * FROM observatoire_comment WHERE matiere='$matiere';";
			$res_obs=mysql_query($sql);
			$nb_obs=mysql_num_rows($res_obs);
			if($nb_obs>0) {
				echo "Suppression of $nb_obs association(s) observatory/course: ";
				$sql="DELETE FROM observatoire_comment WHERE matiere='$matiere';";
				$res_obs=mysql_query($sql);
				if($res_obs) {
					echo "<span style='color:green;'>OK</span><br />\n";
				}
				else {
					echo "<span style='color:red;'>Error</span><br />\n";
				}
			}
		}

		// - matieres
		echo "Suppression of the course $matiere from the table 'matieres': ";
		$sql="DELETE FROM matieres WHERE matiere='$matiere';";
		$res_obs=mysql_query($sql);
		if($res_obs) {
			echo "<span style='color:green;'>OK</span><br />\n";
		}
		else {
			echo "<span style='color:red;'>Error</span><br />\n";
		}

		echo "</p>\n";

		echo "<p>End of the suppression.</p>\n";

	}
	else {
		$lig_grp=mysql_fetch_object($res_grp);
		$current_group=get_group($lig_grp->id_groupe);

		echo "<p>Suppression of the group n°".$current_group['id']." associated to the course '$matiere': \n";

		if(delete_group($current_group['id'])==true) {
			echo "<span style='color:green;'>OK</span></p>\n";

			echo "<form action=\"".$_SERVER['PHP_SELF']."#suite\" name='suite' method=\"post\">\n";
			echo "<input type=\"hidden\" name=\"matiere\" value=\"$matiere\" />\n";
			echo "<input type=\"hidden\" name=\"confirmation_suppr\" value=\"y\" />\n";

			echo "<script type='text/javascript'>
	setTimeout(\"document.forms['suite'].submit();\", 1000);
</script>\n";

			echo "<noscript>\n";
			echo "<div id='fixe'><input type=\"submit\" name=\"ok\" value=\"Continuation of the cleaning\" /></div>\n";
			echo "</noscript>\n";

			echo "</form>\n";
		}
		else {
			echo "<span style='color:red;'>Error</span><br />Perhaps it will be necessary to carry out a Cleaning of the tables/Checking of the groups.</p>\n";

			echo "<form action=\"".$_SERVER['PHP_SELF']."#suite\" name='suite' method=\"post\">\n";
			echo "<input type=\"hidden\" name=\"matiere\" value=\"$matiere\" />\n";
			echo "<input type=\"hidden\" name=\"confirmation_suppr\" value=\"y\" />\n";

			echo "<div id='fixe'><input type=\"submit\" name=\"ok\" value=\"Continuation of cleaning\" /></div>\n";

			echo "</form>\n";
		}

	}
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
die();
//==================================================================================


//if(!isset($_GET['verif'])){
if(!isset($verif)) {
	echo "<h2>Checking of the groups</h2>\n";
	echo "<p>Cette page est destinée à repérer la cause d'éventuelles erreurs du type:</p>\n";
	echo "<pre style='color:green;'>Warning: mysql_result(): Unable to jump to row 0
on MySQL result index 468 in /var/wwws/gepi/lib/groupes.inc.php on line 143</pre>\n";
	echo "<p>To check, click on this link: <a href='".$_SERVER['PHP_SELF']."?verif=oui'>Checking</a><br />(<i>the operation can be very long</i>)</p>\n";
}
else{
	$ini=isset($_POST['ini']) ? $_POST['ini'] : NULL;


	echo "<h2>Search of erroneous inscriptions of students</h2>\n";
	flush();
	$err_no=0;

	// Liste des numéros de périodes
	$sql="SELECT DISTINCT num_periode FROM periodes ORDER BY num_periode;";
	$res_per=mysql_query($sql);
	if(mysql_num_rows($res_per)==0) {
		echo "<p>No period is yet defined.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		unset($tab_per);
		while($lig=mysql_fetch_object($res_per)) {
			$tab_per[]=$lig->num_periode;
		}
	}

	if(!isset($_POST['c_est_parti'])) {
		$sql="TRUNCATE tempo2;";
		$nettoyage=mysql_query($sql);

		$sql="SELECT DISTINCT login FROM j_eleves_groupes ORDER BY login;";
		$res_ele=mysql_query($sql);

		if(mysql_num_rows($res_ele)==0) {
			echo "<p>No student is still registered in a group.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		while($lig=mysql_fetch_object($res_ele)) {
			$sql="INSERT INTO tempo2 SET col1='$lig->login', col2='verif_grp';";
			$res_tempo2=mysql_query($sql);
		}


		$sql="CREATE TABLE IF NOT EXISTS tempo3 (
		id int(11) NOT NULL auto_increment,
		col1 VARCHAR(255) NOT NULL,
		col2 TEXT,
		PRIMARY KEY  (id)
		);";
		$create_table=mysql_query($sql);

		$sql="TRUNCATE tempo3;";
		$nettoyage=mysql_query($sql);

		$ini="";
	}

	/*
	// On commence par ne récupérer que les login/periode pour ne pas risquer d'oublier d'élèves
	// (il peut y avoir des incohérences non détectées si on essaye de récupérer davantage d'infos dans un premier temps)
	$sql="SELECT DISTINCT login,periode FROM j_eleves_groupes ORDER BY login,periode";
	$res_ele=mysql_query($sql);
	*/

	$sql="SELECT * FROM tempo3 WHERE col1='rapport_verif_grp' ORDER BY id;";
	$res_rapport=mysql_query($sql);
	if(mysql_num_rows($res_rapport)>0) {
		while($lig_rapp=mysql_fetch_object($res_rapport)){
			echo $lig_rapp->col2;
		}
	}

	$nb=20;
	$sql="SELECT col1 AS login FROM tempo2 WHERE col2='verif_grp' ORDER BY col1 LIMIT $nb";
	//echo "$sql<br />";
	$res_ele=mysql_query($sql);

	//$ini="A";
	//$ini="";
	//echo "<i>Parcours des login commençant par la lettre $ini</i>";

	if(mysql_num_rows($res_ele)>0) {
		$chaine_rapport="";
		while($lig_ele=mysql_fetch_object($res_ele)){
			$temoin_erreur="n";

			if(strtoupper(substr($lig_ele->login,0,1))!=$ini){
				$ini=strtoupper(substr($lig_ele->login,0,1));
				//echo " - <i>$ini</i>";
				echo "<a name='suite'></a>\n";
				$info="<p>\n<i>Course of the login starting with the letter $ini</i></p>\n";
				echo $info;
				$chaine_rapport.=$info;
			}

			for($loop=0;$loop<count($tab_per);$loop++) {
				$num_periode=$tab_per[$loop];

				// Récupération de la liste des groupes auxquels l'élève est inscrit sur la période en cours d'analyse:
				$sql="SELECT id_groupe FROM j_eleves_groupes WHERE login='$lig_ele->login' AND periode='$num_periode'";
				//echo "$sql<br />\n";
				affiche_debug($sql,$lig_ele->login);
				$res_jeg=mysql_query($sql);

				//while($lig_jeg=mysql_fetch_object($res_jeg)){
				if(mysql_num_rows($res_jeg)>0){
					// On vérifie si l'élève est dans une classe pour cette période:
					//$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$lig_ele->login' AND periode='$num_periode'";
					$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$lig_ele->login' AND periode='$num_periode'";
					affiche_debug($sql,$lig_ele->login);
					$res_jec=mysql_query($sql);

					if(mysql_num_rows($res_jec)==0){
						$temoin_erreur="y";
						// L'élève n'est dans aucune classe sur la période choisie.
						$sql="SELECT c.* FROM classes c, j_eleves_classes jec WHERE jec.login='$lig_ele->login' AND periode='$num_periode' AND jec.id_classe=c.id";
						affiche_debug($sql,$lig_ele->login);
						$res_class_test=mysql_query($sql);

						// Le test ci-dessous est forcément vrai si on est arrivé là!
						if(mysql_num_rows($res_class_test)==0){
							$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_eleves_classes jec WHERE jec.login='$lig_ele->login' AND jec.id_classe=c.id";
							affiche_debug($sql,$lig_ele->login);
							$res_class=mysql_query($sql);

							$chaine_msg="";
							$chaine_classes="";
							if(mysql_num_rows($res_class)!=0){
								while($lig_class=mysql_fetch_object($res_class)){
									$chaine_classes.=", $lig_class->classe";
									$chaine_msg.=",<br /><a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&amp;id_classe=".$lig_class->id."' target='_blank'>Control in $lig_class->classe</a>\n";
								}
								$chaine_msg=substr($chaine_msg,7);
								$chaine_classes=substr($chaine_classes,2);

								//echo "<br />\n";
								$info="<p>\n";
								$info.="<b>$lig_ele->login</b> of <b>$chaine_classes</b> is registered to groups for the period <b>$num_periode</b>, but is not in the class for this period.<br />\n";
								echo $info;
								$chaine_rapport.=$info;

								echo $chaine_msg;
								$chaine_rapport.=$chaine_msg;


								// Contrôler à quelles classes les groupes sont liés.
								unset($tab_tmp_grp);
								$tab_tmp_grp=array();
								if(isset($tab_tmp_clas)){unset($tab_tmp_clas);}
								$tab_tmp_clas=array();
								while($lig_grp=mysql_fetch_object($res_jeg)){
									$tab_tmp_grp[]=$lig_grp->id_groupe;
									$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc WHERE jgc.id_classe=c.id AND jgc.id_groupe='$lig_grp->id_groupe'";
									$res_grp2=mysql_query($sql);
									while($lig_tmp_clas=mysql_fetch_object($res_grp2)){
										if(!in_array($lig_tmp_clas->classe,$tab_tmp_clas)){
											$tab_tmp_clas[]=$lig_tmp_clas->classe;
										}
									}
								}

								$info="<br />\n";
								$info.="The groups of which <b>$lig_ele->login</b> is member are dependent ";
								echo $info;
								$chaine_rapport.=$info;

								if(count($tab_tmp_clas)>1){
									$info="to the following classes: ";
								}
								else{
									$info="to the following class: ";
								}
								echo $info;
								$chaine_rapport.=$info;

								$info=$tab_tmp_clas[0];
								echo $info;
								$chaine_rapport.=$info;

								for($i=1;$i<count($tab_tmp_clas);$i++){
									$info=", ".$tab_tmp_clas[$i];
									echo $info;
									$chaine_rapport.=$info;
								}
								$info="<br />\n";
								$info.="If <b>$lig_ele->login</b> is not in one of these classes, it would have to be affected in the class over at least one period to be able to remove its membership of these groups, or to carry out a cleaning of the tables of base GEPI.";
								$info.="</p>\n";
								echo $info;
								$chaine_rapport.=$info;
							}
							else{
								$info="<p>\n";
								$info.="<b>$lig_ele->login</b> is registered in groups for the period <b>$num_periode</b>, but is not in any class.<br />\n";
								// ... dans aucune classe sur aucune période.
								$info.="It should be affected in a class to be able to remove its inscriptions to groups.<br />\n";
								$info.="</p>\n";
								echo $info;
								$chaine_rapport.=$info;
							}
						}
						$err_no++;


						// Est-ce qu'en plus l'élève aurait des notes ou moyennes saisies sur la période?
						//$sql="SELECT * FROM matieres_notes WHERE id_groupe='$tab_tmp_grp[$i]' AND periode='$num_periode' AND login='$lig_ele->login'"
						$sql="SELECT * FROM matieres_notes WHERE periode='$num_periode' AND login='$lig_ele->login'";
						$res_mat_not=mysql_query($sql);
						if(mysql_num_rows($res_mat_not)>0){
							$info="<b>$lig_ele->login</b> has  average typed for the bulletin over the period <b>$num_periode</b>";
							echo $info;
							$chaine_rapport.=$info;
							/*
							echo " en "
							$lig_tmp=mysql_fetch_object($res_mat_not);
							$sql="SELECT description FROM groupes WHERE id='$lig_tmp->id_groupe'"
							*/
						}

					}
					else{
						if(mysql_num_rows($res_jec)==1){
							$lig_clas=mysql_fetch_object($res_jec);
							//$lig_grp=mysql_fetch_object($res_jeg);
							while($lig_grp=mysql_fetch_object($res_jeg)){
								// On cherche si l'association groupe/classe existe:
								$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_groupe='$lig_grp->id_groupe' AND id_classe='$lig_clas->id_classe'";
								affiche_debug($sql,$lig_ele->login);
								$res_test_grp_clas=mysql_query($sql);

								if(mysql_num_rows($res_test_grp_clas)==0){
									$temoin_erreur="y";
									$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
									$res_tmp=mysql_query($sql);
									$lig_tmp=mysql_fetch_object($res_tmp);
									$clas_tmp=$lig_tmp->classe;

									$sql="SELECT description FROM groupes WHERE id='$lig_grp->id_groupe'";
									$res_tmp=mysql_query($sql);
									$lig_tmp=mysql_fetch_object($res_tmp);
									$grp_tmp=$lig_tmp->description;

									$info="<p>\n";
									//echo "Il semble que $lig_ele->login de la classe $lig_clas->id_classe soit inscrit dans le groupe $lig_grp->id_groupe alors que ce groupe n'est pas associé à la classe dans 'j_groupes_classes'.<br />\n";
									$info.="<b>$lig_ele->login</b> is registered in period $num_periode in the group <b>$grp_tmp</b> (<i>group n°$lig_grp->id_groupe</i>) whereas this group is not associated to the class <b>$clas_tmp</b> in 'j_groupes_classes'.<br />\n";
									echo $info;
									$chaine_rapport.=$info;

									// /groupes/edit_eleves.php?id_groupe=285&id_classe=8
									//$sql="SELECT id_classe FROM j_groupes_classes WHERE id_groupe='$lig_grp->id_groupe';";
									$sql="SELECT jgc.id_classe, c.classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_groupe='$lig_grp->id_groupe' AND jgc.id_classe=c.id;";
									$res_tmp_clas=mysql_query($sql);
									if(mysql_num_rows($res_tmp_clas)>0){
										//$lig_tmp_clas=mysql_fetch_object($res_tmp_clas);
										//echo "Vous pouvez tenter de décocher l'élève de <b>$clas_tmp</b> du groupe <b>$grp_tmp</b> dans cette <a href='../groupes/edit_eleves.php?id_groupe=".$lig_grp->id_groupe."&id_classe=".$lig_tmp_clas->id_classe."' target='_blank'>page</a> si il s'y trouve.<br />\n";
										$info="You can try uncheck the student of <b>$clas_tmp</b> group <b>$grp_tmp</b> in one of the following pages ";
										echo $info;
										$chaine_rapport.=$info;

										$tab_tmp_class=array();
										$tab_tmp_classe=array();
										while($lig_tmp_clas=mysql_fetch_object($res_tmp_clas)){
											$tab_tmp_class[]=$lig_tmp_clas->id_classe;
											$tab_tmp_classe[]=$lig_tmp_clas->classe;
											$info="<a href='../groupes/edit_eleves.php?id_groupe=".$lig_grp->id_groupe."&amp;id_classe=".$lig_tmp_clas->id_classe."' target='_blank'>$lig_tmp_clas->classe</a>, ";
											echo $info;
											$chaine_rapport.=$info;
										}
										$info="if it is there.<br />\n";
										echo $info;
										$chaine_rapport.=$info;
									}

									$info="If no error is raised either in classes of ";
									$info.="<a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&amp;id_classe=".$lig_clas->id_classe."' target='_blank'>$clas_tmp</a>, \n";
									echo $info;
									$chaine_rapport.=$info;

									for($i=0;$i<count($tab_tmp_class);$i++){
										$info="<a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&amp;id_classe=".$tab_tmp_class[$i]."' target='_blank'>".$tab_tmp_classe[$i]."</a>, \n";
										echo $info;
										$chaine_rapport.=$info;
									}
									$info="one will have to be carried out <a href='clean_tables.php?maj=9'>cleaning of the tables of the data base GEPI</a> (<i>after a <a href='../gestion/accueil_sauve.php?action=dump' target='blank'>backup of the base</a></i>).<br />\n";
									$info.="</p>\n";
									echo $info;
									$chaine_rapport.=$info;

									$err_no++;
								}
							}
						}
						else{
							$temoin_erreur="y";
							$info="<p>\n";
							$info.="<b>$lig_ele->login</b> is registered in several classes over the period $num_periode:<br />\n";
							echo $info;
							$chaine_rapport.=$info;

							while($lig_clas=mysql_fetch_object($res_jec)){
								$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
								$res_tmp=mysql_query($sql);
								$lig_tmp=mysql_fetch_object($res_tmp);
								$clas_tmp=$lig_tmp->classe;
								$info="Class of <a href='../classes/classes_const.php?id_classe=$lig_clas->id_classe'>$clas_tmp</a> (<i>n°$lig_clas->id_classe</i>)<br />\n";
								echo $info;
								$chaine_rapport.=$info;
							}
							$info="That should not be possible.<br />\n";
							$info.="Do the Cleaning in manpower of the classes above.\n";
							$info.="</p>\n";
							echo $info;
							$chaine_rapport.=$info;
							$err_no++;
						}
					}
				}
				// Pour envoyer ce qui a été écrit vers l'écran sans attendre la fin de la page...
				flush();
			}

			$sql="UPDATE tempo2 SET col2='$temoin_erreur' WHERE col1='$lig_ele->login';";
			$update=mysql_query($sql);
		}


		// INSERER $chaine_rapport DANS UNE TABLE
		$sql="INSERT INTO tempo3 SET col1='rapport_verif_grp', col2='".addslashes($chaine_rapport)."';";
		$insert=mysql_query($sql);

		echo "<form action=\"".$_SERVER['PHP_SELF']."#suite\" name='suite' method=\"post\">\n";
		echo "<input type=\"hidden\" name=\"verif\" value=\"y\" />\n";
		echo "<input type=\"hidden\" name=\"ini\" value=\"$ini\" />\n";
		echo "<input type=\"hidden\" name=\"c_est_parti\" value=\"y\" />\n";

		echo "<script type='text/javascript'>
	setTimeout(\"document.forms['suite'].submit();\", 2000);
</script>\n";

		echo "<NOSCRIPT>\n";
		echo "<div id='fixe'><input type=\"submit\" name=\"ok\" value=\"Continuation of the checking\" /></div>\n";
		echo "</NOSCRIPT>\n";


		echo "</form>\n";


	}
	else {

		$sql="SELECT 1=1 FROM tempo2 WHERE col2='y';";
		$test_err=mysql_query($sql);
		$err_no=mysql_num_rows($test_err);

		if($err_no==0){
			echo "<p>No error of assignment in groups/classes was detected.</p>\n";
		}
		else{
			echo "<p>Errors were raised.<br />\n";
			echo "To correct, it is necessary to pass by 'Management of the bases/Management of the classes/manage the students' and control for which periods the student is in the class.<br />\n";
			echo "Then, click on the link 'Followed courses' for this student and uncheck the student of the suitable desired periods.<br />\n";
			echo "</p>\n";
			echo "<p>It is also possible that a <a href='clean_tables.php?maj=9'>cleaning of the base (<i>stage of the Groups</i>)</a> is  necessary.<br />\n";
			echo "Take care to do a <a href='../gestion/accueil_sauve.php?action=dump' target='blank'>backup of the base</a> previously for precaution.<br />\n";
		}

		echo "<hr />\n";

		echo "<h2>Search of references to identifiers of non-existent groups</h2>\n";

		$err_no=0;
		$table=array('j_groupes_classes','j_groupes_matieres','j_groupes_professeurs','j_eleves_groupes');
		$id_grp_suppr=array();

		for($i=0;$i<count($table);$i++){
			$sql="SELECT DISTINCT id_groupe FROM ".$table[$i]." ORDER BY id_groupe";
			$res_grp1=mysql_query($sql);

			if(mysql_num_rows($res_grp1)>0){
				echo "<p>On parcourt la table '".$table[$i]."'.</p>\n";
				while($ligne=mysql_fetch_array($res_grp1)){
					$sql="SELECT 1=1 FROM groupes WHERE id='".$ligne[0]."'";
					$res_test=mysql_query($sql);

					if(mysql_num_rows($res_test)==0){
						echo "<b>Erreur:</b> The group of identifier $ligne[0] is used in $table[$i] whereas the group does not exist in the table 'groupes'.<br />\n";
						$id_grp_suppr[]=$ligne[0];
						// FAIRE UNE SAUVEGARDE DE LA BASE AVANT DE DECOMMENTER LES 3 LIGNES CI-DESSOUS:
						/*
						$sql="DELETE FROM $table[$i] WHERE id_groupe='$ligne[0]'";
						echo "$sql<br />";
						$res_suppr=mysql_query($sql);
						*/
						$err_no++;
					}
					flush();
				}
			}
		}
		if($err_no==0){
			echo "<p>No error of identifier of group was raised in the tables 'j_groupes_classes', 'j_groupes_matieres', 'j_groupes_professeurs' and 'j_eleves_groupes'.</p>\n";
		}
		else{
			echo "<p>Errors were raised.<br />\n";
			echo "To correct, you should proceed to a<a href='clean_tables.php?maj=9'>cleaning of the base (<i>stage of the Groups</i>)</a>.<br />\n";
			echo "Take care to do a <a href='../gestion/accueil_sauve.php?action=dump' target='blank'>backup of the base</a> previously by precaution.<br />\n";
			echo "</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>