<?php
/*
 * $Id: creer_remplacant.php 8386 2011-09-29 15:10:28Z crob $
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

// INSERT INTO `droits` VALUES ('/utilisateurs/creer_remplacant.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'script de création d''un remplaçant', '');


// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
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


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Initialisation des variables
$user_login = isset($_POST["user_login"]) ? $_POST["user_login"] : (isset($_GET["user_login"]) ? $_GET["user_login"] : NULL);
$valid = isset($_POST["valid"]) ? $_POST["valid"] : (isset($_GET["valid"]) ? $_GET["valid"] : NULL);
$login_prof_remplace = isset($_POST["login_prof_remplace"]) ? $_POST["login_prof_remplace"] : (isset($_GET["login_prof_remplace"]) ? $_GET["login_prof_remplace"] : NULL);
$form_nom = isset($_POST["form_nom"]) ? $_POST["form_nom"] : (isset($_GET["form_nom"]) ? $_GET["form_nom"] : NULL);
$form_prenom = isset($_POST["form_prenom"]) ? $_POST["form_prenom"] : (isset($_GET["form_prenom"]) ? $_GET["form_prenom"] : NULL);
$form_civilite = isset($_POST["form_civilite"]) ? $_POST["form_civilite"] : (isset($_GET["form_civilite"]) ? $_GET["form_civilite"] : NULL);
$form_email= isset($_POST["form_email"]) ? $_POST["form_email"] : (isset($_GET["form_email"]) ? $_GET["form_email"] : NULL);

$afficher_inactifs=isset($_POST["afficher_inactifs"]) ? $_POST["afficher_inactifs"] : (isset($_GET["afficher_inactifs"]) ? $_GET["afficher_inactifs"] : "n");
$utiliser_compte_existant=isset($_POST["utiliser_compte_existant"]) ? $_POST["utiliser_compte_existant"] : "n";
$compte_existant=isset($_POST["compte_existant"]) ? $_POST["compte_existant"] : "";
$id_groupe=isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : "";

$temoin_erreur="n";

if (isset($_POST['valid']) and ($_POST['valid'] == "yes")) {
	check_token();

    // Cas LCS : on teste s'il s'agit d'un utilisateur local ou non
    if (getSettingValue("use_sso") == "lcs") {
        if ($_POST['is_lcs'] == "y") {$is_pwd = 'n';} else {$is_pwd = 'y';}
	}
    else {
        $is_pwd = "y";
	}

	if(($compte_existant!="")&&($utiliser_compte_existant=='y')) {
		$temoin_erreur_affect_compte_existant="n";
		$sql="SELECT nom, prenom, civilite FROM utilisateurs WHERE (statut='professeur' AND login='$compte_existant');";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			$msg="The selected account does not exist or is not a professor: '$compte_existant'<br />\n";
			$temoin_erreur_affect_compte_existant="y";
		}
		else {

			// Ajout au remplaçant des classes et matières enseignées du prof remplacé.

			// On recheche les matières du prof remplacé qui ne sont pas déjà affectées au prof
			//$sql_matieres = "select * from j_professeurs_matieres where id_professeur='$login_prof_remplace'";
			$sql_matieres = "select * from j_professeurs_matieres where id_professeur='$login_prof_remplace' AND id_matiere NOT IN (SELECT id_matiere FROM j_professeurs_matieres where id_professeur='$compte_existant');";
			$result_matieres = mysql_query($sql_matieres);
			$nombre_matieres = mysql_num_rows($result_matieres);

			$id_matiere_prof_remplace=array();
			for ($i=0;$i<$nombre_matieres;$i++) {
				$id_matiere_prof_remplace[$i] = mysql_result($result_matieres,$i,'id_matiere');
				$ordre_matiere_prof_remplace[$i] = mysql_result($result_matieres,$i,'ordre_matieres');
				//echo "<br>".$id_matiere_prof_remplace[$i]." ".$ordre_matiere_prof_remplace[$i]."<br>";
			}

			// On affecte les matières au prof remplaçant
			for ($i=0;$i<sizeof($id_matiere_prof_remplace);$i++) {
				$sql_matieres = "insert into j_professeurs_matieres set id_matiere='$id_matiere_prof_remplace[$i]', id_professeur='$compte_existant', ordre_matieres='$ordre_matiere_prof_remplace[$i]'";
				//echo "<br>".$sql_matieres;
				$insertion = mysql_query($sql_matieres);
				updateOnline($sql_matieres);
				if(!$insertion) {$temoin_erreur_affect_compte_existant="y";}
			}

			/*
			// On recheche les groupes du prof remplacé qui ne sont pas déjà associés au remplaçant
			//$sql_groupes = "select * from j_groupes_professeurs where login='$login_prof_remplace'";
			$sql_groupes = "select * from j_groupes_professeurs where login='$login_prof_remplace' AND id_groupe NOT IN (SELECT id_groupe FROM j_groupes_professeurs WHERE login='$compte_existant');";
			$result_groupes = mysql_query($sql_groupes);
			$nombre_groupes = mysql_num_rows($result_groupes);

			for ($i=0;$i<$nombre_groupes;$i++) {
				$id_groupes_prof_remplace[$i] = mysql_result($result_groupes,$i,'id_groupe');
				$ordre_groupes_prof_remplace[$i] = mysql_result($result_groupes,$i,'ordre_prof');
				//echo "<br>".$id_matiere_prof_remplace[$i]." ".$ordre_matiere_prof_remplace[$i]."<br>";
			}

			//on affecte les groupes au prof remplaçant
			for ($i=0;$i<sizeof($id_groupes_prof_remplace);$i++) {
				$sql_groupes = "insert into j_groupes_professeurs set id_groupe='$id_groupes_prof_remplace[$i]', login='$compte_existant', ordre_prof='$ordre_groupes_prof_remplace[$i]'";
				//echo "<br>".$sql_groupes;
				$insertion = mysql_query($sql_groupes);
				if(!$insertion) {$temoin_erreur_affect_compte_existant="y";}
			}
			*/
			for($i=0;$i<count($id_groupe);$i++) {
				$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$id_groupe[$i]' AND login='$compte_existant';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					$sql="select * from j_groupes_professeurs where login='$login_prof_remplace';";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)>0) {
						$lig=mysql_fetch_object($res);
						$sql_groupes = "insert into j_groupes_professeurs set id_groupe='$id_groupe[$i]', login='$compte_existant', ordre_prof='$lig->ordre_prof';";
						//echo "<br>".$sql_groupes;
						$insertion = mysql_query($sql_groupes);
						updateOnline($sql_groupes);
						if(!$insertion) {$temoin_erreur_affect_compte_existant="y";}
					}
				}
			}
		}
	}
	else {
		if ($_POST['form_nom'] == '')  {
			$msg = "Please enter a name for the user !";
		} else {
			$k = 0;
			if(isset($_POST['max_mat'])) {
				while ($k < $_POST['max_mat']) {
					$temp = "matiere_".$k;
					$reg_matiere[$k] = $_POST[$temp];
					$k++;
				}
			}
			//
			// actions si un nouvel utilisateur a été défini
			//
			if (true) {
	
				$reg_password_c = md5($NON_PROTECT['password1']);
				$resultat = "";
				if (($_POST['no_anti_inject_password1'] != $_POST['reg_password2']) and ($is_pwd == "y")) {
					$msg = "Error during typing : the two passwords are not identical, restart !";
					$temoin_erreur="y";
				} else if ((!(verif_mot_de_passe($_POST['no_anti_inject_password1'],0)))  and ($is_pwd == "y")) {
					$msg = "Error during typing of the password (<em> see the recommendations</em>), please restart !";
					if((isset($info_verif_mot_de_passe))&&($info_verif_mot_de_passe!="")) {$msg.="<br />".$info_verif_mot_de_passe;}
					$temoin_erreur="y";
				} else {
					// CREATION DU LOGIN
					// partie du code reprise dans /init_xml/prof_csv
						$affiche[1] = $form_prenom;
						$affiche[0] = $form_nom;
	
						$prenoms = explode(" ",$affiche[1]);
						$premier_prenom = $prenoms[0];
						$prenom_compose = '';
						if (isset($prenoms[1])) $prenom_compose = $prenoms[0]."-".$prenoms[1];
	
						$sql="select login from utilisateurs where (
						nom='".traitement_magic_quotes($affiche[0])."' and
						prenom = '".traitement_magic_quotes($premier_prenom)."' and
						statut='professeur')";
						// Pour debug:
						//echo "$sql<br />";
						$test_exist = mysql_query($sql);
						$result_test = mysql_num_rows($test_exist);
						if ($result_test == 0) {
							if ($prenom_compose != '') {
								$test_exist2 = mysql_query("select login from utilisateurs
								where (
								nom='".traitement_magic_quotes($affiche[0])."' and
								prenom = '".traitement_magic_quotes($prenom_compose)."' and
								statut='professeur'
								)");
								$result_test2 = mysql_num_rows($test_exist2);
								if ($result_test2 == 0) {
									$exist = 'no';
								} else {
									$exist = 'yes';
									$login_prof_gepi = mysql_result($test_exist2,0,'login');
								}
							} else {
								$exist = 'no';
							}
						} else {
							$exist = 'yes';
							$login_prof_gepi = mysql_result($test_exist,0,'login');
						}
	
	
					if ($exist == 'no') {
						// Aucun professeur ne porte le même nom dans la base GEPI. On va donc rentrer ce professeur dans la base
	
						$affiche[1] = traitement_magic_quotes(corriger_caracteres($affiche[1]));
	
						$mode_generation_login=getSettingValue("mode_generation_login");
	
						if ($mode_generation_login == "name") {
							$temp1 = $affiche[0];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							//$temp1 = substr($temp1,0,8);
	
						} elseif ($mode_generation_login == "name8") {
							$temp1 = $affiche[0];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,8);
						} elseif ($mode_generation_login == "fname8") {
							$temp1 = $affiche[1]{0} . $affiche[0];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,8);
						} elseif ($mode_generation_login == "fname19") {
							$temp1 = $affiche[1]{0} . $affiche[0];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,19);
						} elseif ($mode_generation_login == "firstdotname") {
							if ($prenom_compose != '') {
								$firstname = $prenom_compose;
							} else {
								$firstname = $premier_prenom;
							}
							$temp1 = $firstname . "." . $affiche[0];
							$temp1 = strtoupper($temp1);
	
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							//$temp1 = substr($temp1,0,19);
						} elseif ($mode_generation_login == "firstdotname19") {
							if ($prenom_compose != '') {
								$firstname = $prenom_compose;
							} else {
								$firstname = $premier_prenom;
							}
							$temp1 = $firstname . "." . $affiche[0];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,19);
						} elseif ($mode_generation_login == "namef8") {
							$temp1 =  substr($affiche[0],0,7) . $affiche[1]{0};
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							//$temp1 = substr($temp1,0,8);
						} elseif ($mode_generation_login == "lcs") {
							$nom = $affiche[0];
							$nom = strtolower($nom);
							if (preg_match("/\s/",$nom)) {
								$noms = preg_split("/\s/",$nom);
								$nom1 = $noms[0];
								if (strlen($noms[0]) < 4) {
									$nom1 .= "_". $noms[1];
									$separator = " ";
								} else {
									$separator = "-";
								}
							} else {
								$nom1 = $nom;
								$sn = ucfirst($nom);
							}
							$firstletter_nom = $nom1{0};
							$firstletter_nom = strtoupper($firstletter_nom);
							$prenom = $affiche[1];
							$prenom1 = $affiche[1]{0};
							$temp1 = $prenom1 . $nom1;
							$temp1 = remplace_accents($temp1,"all");
						}
						$login_prof = $temp1;
						//$login_prof = remplace_accents($temp1,"all");
						// On teste l'unicité du login que l'on vient de créer
						$m = 2;
						$test_unicite = 'no';
						$temp = $login_prof;
						while ($test_unicite != 'yes') {
							$test_unicite = test_unique_login($login_prof);
							if ($test_unicite != 'yes') {
								$login_prof = $temp.$m;
								$m++;
							}
						}
						$affiche[0] = traitement_magic_quotes(corriger_caracteres($affiche[0]));
						// Mot de passe
						//echo "<tr><td colspan='4'>strlen($affiche[5])=".strlen($affiche[5])."<br />\$affiche[4]=$affiche[4]<br />\$_POST['sso']=".$_POST['sso']."</td></tr>";
						//if ($_POST['sso']== "no") {
						if((!isset($_POST['sso']))||($_POST['sso']== "no")) {
							$pwd = md5(rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9));
							$mess_mdp = $pwd;
							//echo "<tr><td colspan='4'>Choix 2: $pwd</td></tr>";
				//                       $mess_mdp = "Inconnu (compte bloqué)";
						} elseif ($_POST['sso'] == "yes") {
							$pwd = '';
							$mess_mdp = "aucun (sso)";
							//echo "<tr><td colspan='4'>sso</td></tr>";
						}
						// Fin code génération login de init_xml
	
	
						//choix du format
						//test du login
						$test = mysql_query("SELECT * FROM utilisateurs WHERE login = '".$login_prof."'");
						$nombreligne = mysql_num_rows($test);
						if ($nombreligne != 0) {
							$resultat = "NON";
							$msg = "*** Caution ! A user having the same identifier already exists. Impossible recording ! ***";
						}
	
						if ($resultat != "NON") {
							if ($is_pwd == "y") {
								$reg_data = mysql_query("INSERT INTO utilisateurs SET nom='".$_POST['form_nom']."',prenom='".$_POST['form_prenom']."',civilite='".$_POST['form_civilite']."',login='".$login_prof."',password='$reg_password_c',statut='professeur',email='".$_POST['form_email']."',etat='actif', change_mdp='y'");
							updateOnline("INSERT INTO utilisateurs SET nom='".$_POST['form_nom']."',prenom='".$_POST['form_prenom']."',civilite='".$_POST['form_civilite']."',login='".$login_prof."',password='$reg_password_c',statut='professeur',email='".$_POST['form_email']."',etat='actif', change_mdp='y'");
							}
							else {
								$reg_data = mysql_query("INSERT INTO utilisateurs SET nom='".$_POST['form_nom']."',prenom='".$_POST['form_prenom']."',civilite='".$_POST['form_civilite']."',login='".$login_prof."',password='',statut='professeur',email='".$_POST['form_email']."',etat='actif', change_mdp='n'");
							updateOnline("INSERT INTO utilisateurs SET nom='".$_POST['form_nom']."',prenom='".$_POST['form_prenom']."',civilite='".$_POST['form_civilite']."',login='".$login_prof."',password='',statut='professeur',email='".$_POST['form_email']."',etat='actif', change_mdp='n'");
							}
						}
						$msg="You have just created a new user !<br />By default, this user is regarded as credit.";
						//$msg = $msg."<br />Pour imprimer les paramètres de l'utilisateur (identifiant, mot de passe, ...), cliquez <a href='impression_bienvenue.php?user_login=".$_POST['new_login']."&mot_de_passe=".urlencode($NON_PROTECT['password1'])."' target='_blank'>ici</a> !";
						$msg = $msg."<br />To print the parameters of the user (identifier, password, ...), click <a href='impression_bienvenue.php?user_login=".$login_prof."&amp;mot_de_passe=".urlencode($NON_PROTECT['password1'])."' target='_blank'>here</a> !";
						$msg = $msg."<br />Caution : later , it will be impossible for you to print again the password of a user ! ";
						$user_login = $login_prof;
	
	
						// Ajout au remplaçant des classes et matières enseignées du prof remplacé.
	
						//on recheche les matières du prof remplacé
						$sql_matieres = "select * from j_professeurs_matieres where id_professeur='$login_prof_remplace'";
	
						$result_matieres = mysql_query($sql_matieres);
						$nombre_matieres = mysql_num_rows($result_matieres);
	
						for ($i=0;$i<$nombre_matieres;$i++) {
							$id_matiere_prof_remplace[$i] = mysql_result($result_matieres,$i,'id_matiere');
							$ordre_matiere_prof_remplace[$i] = mysql_result($result_matieres,$i,'ordre_matieres');
							//echo "<br>".$id_matiere_prof_remplace[$i]." ".$ordre_matiere_prof_remplace[$i]."<br>";
						}
	
						//on affecte les matières au prof remplaçant
						for ($i=0;$i<sizeof($id_matiere_prof_remplace);$i++) {
							$sql_matieres = "insert into j_professeurs_matieres set id_matiere='$id_matiere_prof_remplace[$i]', id_professeur='$login_prof', ordre_matieres='$ordre_matiere_prof_remplace[$i]'";
							//echo "<br>".$sql_matieres;
							$insertion = mysql_query($sql_matieres);
							updateOnline($sql_matieres);
						}

						/*
						// On recheche les groupes du prof remplacé
						$sql_groupes = "select * from j_groupes_professeurs where login='$login_prof_remplace'";
	
						$result_groupes = mysql_query($sql_groupes);
						$nombre_groupes = mysql_num_rows($result_groupes);
	
						for ($i=0;$i<$nombre_groupes;$i++) {
							$id_groupes_prof_remplace[$i] = mysql_result($result_groupes,$i,'id_groupe');
							$ordre_groupes_prof_remplace[$i] = mysql_result($result_groupes,$i,'ordre_prof');
							//echo "<br>".$id_matiere_prof_remplace[$i]." ".$ordre_matiere_prof_remplace[$i]."<br>";
						}
	
						// On affecte les groupes au prof remplaçant
						for ($i=0;$i<sizeof($id_groupes_prof_remplace);$i++) {
							$sql_groupes = "insert into j_groupes_professeurs set id_groupe='$id_groupes_prof_remplace[$i]', login='$login_prof', ordre_prof='$ordre_groupes_prof_remplace[$i]'";
							//echo "<br>".$sql_groupes;
							$insertion = mysql_query($sql_groupes);
						}
						*/

						for($i=0;$i<count($id_groupe);$i++) {
							$sql="select * from j_groupes_professeurs where login='$login_prof_remplace';";
							$res=mysql_query($sql);
							if(mysql_num_rows($res)>0) {
								$lig=mysql_fetch_object($res);
								$sql_groupes = "insert into j_groupes_professeurs set id_groupe='$id_groupe[$i]', login='$login_prof', ordre_prof='$lig->ordre_prof';";
								//echo "<br>".$sql_groupes;
								$insertion = mysql_query($sql_groupes);
								updateOnline($sql_groupes);
							}
						}
	
					}
					else {
						$msg="The person already exists in the base (same name and same first name)";
					}
				}
				//
				//action s'il s'agit d'une modification
				//
			}  else {
				$msg = "The identifier of the user must be only made up of letters and figures !";
	
			}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Management of the users | Create a substitute";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();
?>
<p class='bold'>
<a href="index.php?mode=personnels"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>
</p>

<?php
	/*
	//$sql_groupes = "select DISTINCT g.*, c.classe from j_groupes_professeurs jgp, g.id, j_groupes_classes jgc, classes c where jgp.login='$login_prof_remplace' AND jgp.id_groupe=g.id AND g.id=jgc.id_groupe AND jgc.id_classe=c.id;";
	$sql_groupes = "select DISTINCT g.* from j_groupes_professeurs jgp, g.id, j_groupes_classes jgc WHERE jgp.login='$login_prof_remplace' AND jgp.id_groupe=g.id AND g.id=jgc.id_groupe;";
	$result_groupes = mysql_query($sql_groupes);
	$nombre_groupes = mysql_num_rows($result_groupes);
	if($nombre_groupes==0) {
		echo "<p style='color:red'>Le professeur '$login_prof_remplace' n'a aucun enseignement.<br />Le remplacement ne se justifie pas.</p>\n";
		require("../lib/footer.inc.php");
	}
	else {
		while($lig=mysql_fetch_object($result_groupes)) {

		}
	}
	*/
	$groups=get_groups_for_prof($login_prof_remplace);
	if(count($groups)==0) {
		echo "<p style='color:red'>The professor '$login_prof_remplace' has no course .<br />The replacement is not justified.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

//affichage du formulaire
if ($valid!='yes') {
	// On appelle les informations de l'utilisateur pour les afficher :
	if (isset($login_prof_remplace) and ($login_prof_remplace!='')) {
		$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE login='".$login_prof_remplace."'");
		$user_nom = mysql_result($call_user_info, "0", "nom");
		$user_prenom = mysql_result($call_user_info, "0", "prenom");
		$user_civilite = mysql_result($call_user_info, "0", "civilite");
	}

	echo "<br /><p>Creation of a substitute for the identifier : <b>".$login_prof_remplace."</b> (".$user_civilite." ".$user_prenom." ".$user_nom.")</p>";
	echo "<br />";
	//Affichage formulaire
	echo "<form enctype=\"multipart/form-data\" action=\"creer_remplacant.php?login_prof_remplace=".$login_prof_remplace."\" method=post>";
	echo "<fieldset>\n";
	echo add_token_field();
	echo "<div class = \"norme\">\n";
	echo "<table>\n";
	echo "<tr><td>Name : </td><td><input type=text name=form_nom size=20 /></td></tr>\n";
	echo "<tr><td>First name : </td><td><input type=text name=form_prenom size=20 /></td></tr>\n";
	echo "<tr><td>Civility : </td><td><select name=\"form_civilite\" size=\"1\">\n";
	echo "<option value='M.' >M.</option>\n";
	echo "<option value='Mme' >Mme</option>\n";
	echo "<option value='Mlle' >Mlle</option>\n";
	echo "</select>\n";
	echo "</td></tr>\n";
	echo "<tr><td>Email : </td><td><input type=text name=form_email size=30  /></td></tr>\n";
	echo "</table>\n";

	if (!(isset($user_login)) or ($user_login=='')) {
		if (getSettingValue("use_sso") == "lcs") {
			echo "<table border=\"1\" cellpadding=\"5\" cellspacing=\"1\"><tr><td>";
			echo "<input type=\"radio\" name=\"is_lcs\" value=\"y\" checked /> User LCS";
			echo "<br /><i>A LCS user is a user authenticated by LCS : in this case, not fill the fields \"password\" below.</i>";
			echo "</td></tr><tr><td>";
			echo "<input type=\"radio\" name=\"is_lcs\" value=\"n\" /> Local user";
			echo "<br /><i>A local user must be systematically identified on GEPI with the
password below, even if he is a user authenticated by LCS.</i>";
			echo "<br /><i><b>Notice</b> : the address to connect itself locally is of the type : http://mon.site.fr/gepi/login.php?local=y (do not omit \"<b>?local=y</b>\").</i>";
			echo "<br /><br />";
		}
		echo "<table><tr><td>Password (".getSettingValue("longmin_pwd") ." characters minimum) : </td><td><input type=password name=no_anti_inject_password1 size=20 /></td></tr>";
		echo "<tr><td>Password (to confirm) : </td><td><input type=password name=reg_password2 size=20 /></td></tr></table>";
		echo "<br /><b>Caution : the password must comprise ".getSettingValue("longmin_pwd")." characters minimum and must be made up at the same time of letters and figures.</b>";
		echo "<br /><b>Notice</b> : during creation of a user, it is recommended to choose the NUMEN as password.<br />";
		if (getSettingValue("use_sso") == "lcs") echo "</td></tr></table>";
	}
	echo "<br />\n";

	echo "<input type=hidden name=valid value=\"yes\" />\n";
	if (isset($user_login)) echo "<input type=hidden name=user_login value=\"".$user_login."\" />\n";

	echo "<p>List of courses replaced &nbsp;:<br />";
	$cpt=0;
	foreach($groups as $current_group) {
		echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='".$current_group['id']."' checked /><label for='id_groupe_$cpt'>".$current_group['name']." (<i>".$current_group['description']."</i>) en ".$current_group['classlist_string']."</label><br />\n";
		$cpt++;
	}
	echo "</p>\n";

	echo "<center><input type=submit value=\"Create the substitute\" /></center>\n";
	echo "<!--/span-->\n";
	echo "</div>\n";
	echo "</fieldset>\n";
	echo "</form>\n";

	//============================================================================
	echo "<br />\n";

	echo "<p class='bold'>Or select an existing user&nbsp:</p>\n";
	echo "<form enctype=\"multipart/form-data\" action=\"creer_remplacant.php?login_prof_remplace=".$login_prof_remplace."\" method=post>";
	echo "<fieldset>\n";
	echo add_token_field();
	echo "<div class = \"norme\">\n";
	echo "Compte existant&nbsp;: <select name='compte_existant'>\n";
	$sql="SELECT * FROM utilisateurs WHERE (statut='professeur'";
	if($afficher_inactifs!="y") {$sql.=" AND etat='actif'";}
	$sql.=") ORDER BY nom,prenom;";
	$calldata = mysql_query($sql);
	$nombreligne = mysql_num_rows($calldata);
	$i = 0;
	echo "<option value=''>---</option>\n";
	while ($i < $nombreligne){
		$prof_nom = mysql_result($calldata, $i, "nom");
		$prof_prenom = mysql_result($calldata, $i, "prenom");
		$prof_civilite = mysql_result($calldata, $i, "civilite");
		$prof_login = mysql_result($calldata, $i, "login");
		$prof_etat[$i] = mysql_result($calldata, $i, "etat");
		echo "<option value='$prof_login'>$prof_civilite ".strtoupper($prof_nom)." ".casse_prenom($prof_prenom)."</option>\n";
		$i++;
	}
	echo "</select>\n";
	echo "<br />\n";

	echo "<p> List of the replaced courses&nbsp;:<br />";
	$cpt=0;
	foreach($groups as $current_group) {
		echo "<input type='checkbox' name='id_groupe[]' id='id_groupe2_$cpt' value='".$current_group['id']."' checked /><label for='id_groupe2_$cpt'>".$current_group['name']." (<i>".$current_group['description']."</i>) en ".$current_group['classlist_string']."</label><br />\n";
		$cpt++;
	}
	echo "</p>\n";
	echo "<input type='hidden' name='valid' value=\"yes\" />\n";
	echo "<input type='hidden' name='utiliser_compte_existant' value=\"y\" />\n";
	//if (isset($user_login)) {echo "<input type=hidden name=user_login value=\"".$user_login."\" />\n";}
	echo "<input type=submit value=\"Declare this user substitute\" />\n";
	echo "<!--/span-->\n";
	echo "</div>\n";
	echo "<p>If an account that you know existing does not appear in the list, it may be that it is inactive.<br />Start then by activating the account or click <a href='".$_SERVER['PHP_SELF']."?login_prof_remplace=$login_prof_remplace&amp;afficher_inactifs=y'>here</a> to display also inactive accounts.</p>\n";
	echo "</fieldset>\n";
	echo "</form>\n";
	echo "<p><br /></p>\n";

}
elseif(isset($temoin_erreur_affect_compte_existant)) {
	if($temoin_erreur_affect_compte_existant=="y") {
		echo "<p style='color:red'>An error occurred during declaration of '<b>$compte_existant</b>' as substitute of '<b>$login_prof_remplace</b>'.</p>\n";
	}
	else {
		echo "<p>The account '<b>$compte_existant</b>' was declared substitute of '<b>$login_prof_remplace</b>'.</p>\n";
	}
}
elseif($temoin_erreur=="y") {
  echo "<center><br/><br/><b>There was an error.</b></center>";
}
else {// fin affichage formulaire
  echo "<center><br/><br/><b>Substitute created</b></center>";
}

require("../lib/footer.inc.php");

?>