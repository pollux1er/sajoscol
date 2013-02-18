<?php

/*
 *
 * @version $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Import bulletin eleve";

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

// fonctions complémentaires et/ou librairies utiles


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// SQL : INSERT INTO droits VALUES ( '/eleves/import_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation bulletin élève', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/eleves/import_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation bulletin élève', '');";
//


if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}




function get_nom_prenom_from_login($ele_login,$mode) {
	$retour="";

	$sql="SELECT nom,prenom FROM eleves WHERE login='$ele_login';";
	$res=mysql_query($sql);updateOnline($sql);
	if(mysql_num_rows($res)==0) {
		$retour="LOGIN INCONNU";
	}
	else {
		$lig=mysql_fetch_object($res);

		if($mode=="np") {
			$retour=strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
		}
		else {
			$retour=ucfirst(strtolower($lig->prenom))." ".strtoupper($lig->nom);
		}
	}

	return $retour;
}

function get_infos_from_ele_login($ele_login,$mode) {
	$retour=array();

	//$sql="SELECT nom,prenom FROM eleves WHERE login='$ele_login';";
	$sql="SELECT * FROM eleves WHERE login='$ele_login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$retour['denomination']="LOGIN INCONNU";
	}
	else {
		$lig=mysql_fetch_object($res);

		if($mode=="np") {
			$retour['denomination']=strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
		}
		else {
			$retour['denomination']=ucfirst(strtolower($lig->prenom))." ".strtoupper($lig->nom);
		}

		$retour['nom']=$lig->nom;
		$retour['prenom']=$lig->prenom;
		$retour['no_gep']=$lig->no_gep;
		$retour['ele_id']=$lig->ele_id;
		$retour['elenoet']=$lig->elenoet;
		$retour['sexe']=$lig->sexe;
	}

	return $retour;
}

// PB: Il faut remplacer le login PROF par ANONYME_EXT... ou le nom de l'établissement
// A l'import, il faut avoir créé l'élève,
//             créer des enseignements? dans une classe EXTERIEUR... il faut une classe par élève...
//             si on a plusieurs arrivées, ça fait des matières en plus,... pas un pb...
//             seules les matières suivies par l'élève sont prises en compte...
//             créer des cours différents pour chaque élève pour éviter des moyennes de classe fantaisistes
// A l'export, une ligne pour l'association: LOGIN_ETAB -> Nom, prénom pour la table utilisateurs


// Nom: gepiSchoolName
// Prénom: gepiSchoolCity

// ======================== CSS et js particuliers ========================
$utilisation_win = "non";
$utilisation_jsdivdrag = "non";
//$javascript_specifique = ".js";
//$style_specifique = ".css";

// ===================== entete Gepi ======================================//
require_once("../lib/header.inc");
// ===================== fin entete =======================================//

$page="import_bull_eleve.php";

$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);

//debug_var();

//echo "<div class='norme'><p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "<div class='norme'><p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>\n";

if(getSettingValue('exp_imp_chgt_etab')!='yes') {
	// Pour activer le dispositif:
	// DELETE FROM setting WHERE name='exp_imp_chgt_etab';INSERT INTO setting SET name='exp_imp_chgt_etab', value='yes';
	echo "<p>This page is intended to import the averages and appreciations of the bulletin of a student comming from another school<br />\n";
	echo "The student must be created beforehand in your base and affected in a class for at least one period.</p>\n";
	echo "<p><br /></p>\n";
	echo "<p>The device (<i>still in the course of test the 17/07/2008</i>) do not seem activated.</p>\n";
	require_once("../lib/footer.inc.php");
	die();
}

//if(!isset($ele_login)) {
if((!isset($ele_login))&&(!isset($_POST['Recherche_sans_js']))) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>This page is intended to import the averages and appreciations of the bulletin of an arriving student of another school<br />\n";
	echo "The student must be created beforehand in your base and affected in a class for at least one period.</p>\n";

	echo "<p><i>Example:</i></p>\n";
	echo "<blockquote>\n";
	echo "<p>One registers a student arriving in December in a class for quarters 2 and 3.<br />And only quarter 1 will be imported since a file CSV.\n";
	echo "</p>\n";
	echo "</blockquote>\n";

	echo "<p class='bold'>Choice of the student:</p>\n";
	// Formulaire pour navigateur SANS Javascript:
	echo "<noscript>
	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire1'>
		<p>
		Display the students of which the <b>name</b> contains: <input type='text' name='rech_nom' value='' />
		<input type='hidden' name='page' value='$page' />
		<input type='submit' name='Recherche_sans_js' value='Search' />
		</p>
	</form>
</noscript>\n";

	// Portion d'AJAX:
	echo "<script type='text/javascript'>
	function cherche_eleves() {
		rech_nom=document.getElementById('rech_nom').value;

		var url = 'liste_eleves.php';
		var myAjax = new Ajax.Request(
			url,
			{
				method: 'post',
				postBody: 'rech_nom='+rech_nom+'&page=$page',
				onComplete: affiche_eleves
			});
	}

	function affiche_eleves(xhr) {
		if (xhr.status == 200) {
			document.getElementById('liste_eleves').innerHTML = xhr.responseText;
		}
		else {
			document.getElementById('liste_eleves').innerHTML = xhr.status;
		}
	}
</script>\n";

	// DIV avec formulaire pour navigateur AVEC Javascript:
	echo "<div id='recherche_avec_js' style='display:none;'>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' onsubmit='cherche_eleves();return false;' method='post' name='formulaire'>";
	echo "<p>";
	echo "Afficher les élèves dont le <b>nom</b> contient: <input type='text' name='rech_nom' id='rech_nom' value='' />\n";
	echo "<input type='hidden' name='page' value='$page' />\n";
	echo "<input type='button' name='Recherche' value='Rechercher' onclick='cherche_eleves()' />\n";
	echo "</p>\n";
	echo "</form>\n";

	echo "<div id='liste_eleves'></div>\n";

	echo "</div>\n";
	echo "<script type='text/javascript'>document.getElementById('recherche_avec_js').style.display='';</script>\n";

	echo "<p><br /></p>\n";
	echo "<p><i>Notice:</i></p>\n";
	echo "<blockquote>\n";
	echo "<p>The bulletins of the old school will comprise erroneous
information:<br />The structure of the table 'j_eleves_cpe' account of the periods does not take so that the CPE responsible for the periods where the student was in its previous school will appear as being the CPE of your school.<br />It will be necessary to change the structure of 'j_eleves_cpe' in a forthcoming version of Gepi to correct this bug.</p>\n";
	echo "</blockquote>\n";
}
elseif(isset($_POST['Recherche_sans_js'])) {
	// On ne passe ici que si JavaScript est désactivé
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choose another student</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	include("recherche_eleve.php");
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choose another student</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	//$info_eleve=get_nom_prenom_from_login($ele_login,"pn");
	$info_eleve=get_infos_from_ele_login($ele_login,"pn");
	//if($info_eleve=="LOGIN INCONNU") {
	if($info_eleve['denomination']=="LOGIN INCONNU") {
		echo "<p>The login '$ele_login' is unknown in the table 'eleves'.</p>\n";

		require_once("../lib/footer.inc.php");
		die;
	}

	echo "<p>You chose ".$info_eleve['denomination']."<br />\n";

	if(!isset($_FILES["csv_file"])) {

		$tab_per=array();

		$sql="SELECT c.classe,jec.periode,p.nom_periode FROM j_eleves_classes jec, classes c, periodes p WHERE jec.login='$ele_login' AND jec.id_classe=c.id AND p.num_periode=jec.periode ORDER BY jec.periode;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "L'élève n'est inscrit";
			if($info_eleve['sexe']=='F') {echo "e";}
			echo " in any class for any period.<br />\n";
			echo "</p>\n";
		}
		else {
			while($lig=mysql_fetch_object($res)) {
				if(!in_array($lig->periode,$tab_per)) {
					$tab_per[]=$lig->periode;
					echo "The student is registered";
					if($info_eleve['sexe']=='F') {echo "e";}
					echo " in ".$lig->classe." for the period ".$lig->nom_periode.".<br />\n";
				}
			}
			echo "</p>\n";

			echo "<p>Only the period not listed above could be imported from CSV file.</p>\n";
		}

		//echo nl2br(get_bull($ele_login));

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		echo add_token_field();
		echo "File to import: <input type='file' name='csv_file' value='' />\n";
		echo "<input type='hidden' name='ele_login' value=\"$ele_login\" />\n";
		echo "<input type='submit' name='envoi' value='Send' />\n";
		echo "</form>\n";

		echo "<p><i>Caution</i>&nbsp;: Please import only csv file generated by Gepi.<br />In the doubt, made a preliminary safeguard of the base.</p>\n";
	}
	else {

		//if((!is_array($_FILES["csv_file"]))||(!is_uploaded_file($_FILES["csv_file"]['tmp_name']))) {
		if(!is_uploaded_file($_FILES["csv_file"]['tmp_name'])) {
			echo "<p>The file was not uploaded...<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login'>Return to the choice of the file</a></p>\n";
			require_once("../lib/footer.inc.php");
			die();
		}

		echo "<p>File uploaded...<br />\n";
		$csv_file=$_FILES["csv_file"];
		//echo "\$csv_file['tmp_name']=".$csv_file['tmp_name']."<br />";

		check_token(false);

		//flush();

		$sql="SELECT MAX(num_periode) AS nb_per FROM periodes p, j_eleves_classes jec WHERE jec.login='$ele_login' AND jec.id_classe=p.id_classe;";
		//echo "$sql<br />";

		//die();

		//flush();
		$res=mysql_query($sql);
		//$nb_per=3;
		$nb_per=0;
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			$nb_per=$lig->nb_per;
		}

		$tab_per=array();
		$sql="SELECT jec.periode FROM j_eleves_classes jec WHERE jec.login='$ele_login' ORDER BY jec.periode;";
		//echo "$sql<br />";
		//flush();
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {
				$tab_per[]=$lig->periode;
			}
		}

		echo "<p>Reading of the file: <br />\n";
		//$csv_file=$_FILES["csv_file"];
		$fich=fopen($csv_file['tmp_name'],"r");
		echo "<div style='color: green; border: 1px solid black;'>\n";
		while (!feof($fich)) {
			$ligne=fgets($fich, 4096);
			if(trim($ligne)!="") {
				//echo $ligne."<br />\n";
				echo htmlentities($ligne)."<br />\n";
			}
		}
		echo "</div>\n";
		fclose($fich);
		//echo "</p>\n";

		// Recherche des infos établissement
		//$csv_file=$_FILES["csv_file"];
		$fich=fopen($csv_file['tmp_name'],"r");
		while (!feof($fich)) {
			$ligne=fgets($fich, 4096);
			if(trim($ligne)!="") {
				if(substr($ligne,0,20)=="INFOS_ETABLISSEMENT;") {
					$tab_tmp=explode(";",$ligne);
					$nom_etab_ori=$tab_tmp[1];
					$ville_etab_ori=$tab_tmp[2];
					$rne_etab_ori=$tab_tmp[3];
				}
			}
		}
		fclose($fich);
		//echo "</p>\n";

		// Inscription dans j_eleves_etablissement
		$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='".$info_eleve['elenoet']."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Insertion of association student/school of origin: ";
			$sql="INSERT INTO j_eleves_etablissements SET id_eleve='".$info_eleve['elenoet']."', id_etablissement='$rne_etab_ori';";
			$res=mysql_query($sql);updateOnline($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERROR</span>";
				echo "</p>\n";
				//require_once("../lib/footer.inc.php");
				//die();
			}
		}
		else {
			echo "<p>Update of association student/school of origin: ";
			$sql="UPDATE j_eleves_etablissements SET id_etablissement='$rne_etab_ori' WHERE id_eleve='".$info_eleve['elenoet']."';";
			$res=mysql_query($sql);updateOnline($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERROR</span>";
				echo "</p>\n";
				//require_once("../lib/footer.inc.php");
				//die();
			}
		}


		// Créer la classe
		//$sql="SELECT classe FROM classes WHERE classe LIKE '$nom_etab_ori%';";
		$sql="SELECT classe FROM classes WHERE classe='$nom_etab_ori';";
		//echo "$sql<br />";
		//flush();
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Creation of a class '$nom_etab_ori': ";

			//$sql="INSERT INTO classes SET classe='$nom_etab_ori', nom_complet='$nom_etab_ori';";
			$sql="INSERT INTO classes SET classe='".$nom_etab_ori."',
										nom_complet='".$nom_etab_ori."',
										display_mat_cat='n',
										suivi_par='$nom_etab_ori',
										formule='Pour le conseil',
										format_nom='np',
										modele_bulletin_pdf='1'
										;";
			$res=mysql_query($sql);updateOnline($sql);
			if($res) {
				$id_classe_etab=mysql_insert_id();
				$classe_etab=$nom_etab_ori;
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERROR</span>";
				echo "</p>\n";
				require_once("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$cpt=1;
			while(true) {
				//$sql="SELECT classe FROM classes WHERE classe LIKE '".$nom_etab_ori.$cpt."';";
				$sql="SELECT classe FROM classes WHERE classe='".$nom_etab_ori.$cpt."';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					echo "<p>Creation of a class '".$nom_etab_ori.$cpt."': ";

					$sql="INSERT INTO classes SET classe='".$nom_etab_ori.$cpt."',
												nom_complet='".$nom_etab_ori.$cpt."',
												display_mat_cat='n',
												suivi_par='$nom_etab_ori',
												formule='Pour le conseil',
												format_nom='np',
												modele_bulletin_pdf='1'
												;";
					$res=mysql_query($sql);updateOnline($sql);
					if($res) {
						$id_classe_etab=mysql_insert_id();
						$classe_etab=$nom_etab_ori.$cpt;
						echo "<span style='color:green;'>OK</span>";
						echo "</p>\n";
						break;
					}
					else {
						echo "<span style='color:red;'>ERROR</span>";
						echo "</p>\n";
						require_once("../lib/footer.inc.php");
						die();
					}
				}
				else {
					//echo "$cpt<br />";
					//flush();
					$cpt++;
				}
			}
		}
		echo "<p>You will be able to rename the class later on if you want.</p>\n";


		// Insertion du même nombre de périodes pour l'ancienne classe que pour l'actuelle
		// L'élève ne sera pas affecté dans la classe pour toutes les périodes
		if($nb_per>0) {echo "<p>Creation of periods for the old class of the student ('$classe_etab'): ";}
		for($i=1;$i<=$nb_per;$i++) {
			if($i>1) {echo ", ";}
			echo $i;
			$sql="INSERT INTO periodes SET num_periode='$i', nom_periode='Période $i', verouiller='O', id_classe='$id_classe_etab';";
			$res=mysql_query($sql);updateOnline($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				//echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERROR</span>";
				echo "</p>\n";
				//require_once("../lib/footer.inc.php");
				//die();
			}
		}


		// Créer l'utilisateur prof...
		$sql="SELECT login FROM utilisateurs WHERE nom='$nom_etab_ori' AND prenom='$ville_etab_ori';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Creation of a user professor '$nom_etab_ori': ";

			// CREER UN LOGIN
			$login_etab=generate_unique_login($nom_etab_ori,$ville_etab_ori,"name");

			$sql="INSERT INTO utilisateurs SET login='$login_etab',
												nom='$nom_etab_ori',
												prenom='$ville_etab_ori',
												civilite='M.',
												password='',
												statut='professeur',
												etat='inactif';";
			$res=mysql_query($sql);updateOnline($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERROR</span>";
				echo "</p>\n";
				require_once("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$lig=mysql_fetch_object($test);
			$login_etab=$lig->login;
		}

		// Penser à inscrire dans j_scol_classe les comptes scolarité qui ont la classe actuelle de l'élève
		// et si l'élève n'est dans aucune classe, proposer le lien.
		$sql="SELECT DISTINCT jsc.login FROM j_eleves_classes jec,j_scol_classes jsc WHERE jec.login='$ele_login' AND jec.id_classe=jsc.id_classe;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_query($res)) {
				$sql="SELECT 1=1 FROM j_scol_classes WHERE id_classe='$id_classe_etab' AND login='$lig->login';";
				$res1=mysql_query($sql);
				if(mysql_num_rows($res1)==0) {
					echo "Insertion of the authorization of consultation for ".affiche_utilisateur($lig->login,'np').": ";
					$sql="INSERT INTO j_scol_classes SET id_classe='$id_classe_etab', login='$lig->login';";
					$res2=mysql_query($sql);updateOnline($sql);
					if($res2) {
						echo "<span style='color:green;'>OK</span>";
						echo "</p>\n";
					}
					else {
						echo "<span style='color:red;'>ERROR</span>";
						echo "</p>\n";
						//require_once("../lib/footer.inc.php");
						//die();
					}
				}
			}
		}


		/*
		// On a forcément une info erronée sur le nom du CPE
		// parce que la table j_eleves_cpe n'a que deux champs e_login et cpe_login

		// Créer l'utilisateur CPE...
		$sql="SELECT login FROM utilisateurs WHERE nom='CPE $nom_etab_ori' AND prenom='$ville_etab_ori';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Création d'un utilisateur 'CPE $nom_etab_ori': ";

			// CREER UN LOGIN
			$login_etab=generate_unique_login("CPE $nom_etab_ori","$ville_etab_ori","name");

			$sql="INSERT INTO utilisateurs SET login='$login_etab',
												nom='CPE $nom_etab_ori',
												prenom='$ville_etab_ori',
												civilite='M.',
												password='',
												statut='cpe',
												etat='inactif';";
			$res=mysql_query($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERREUR</span>";
				echo "</p>\n";
				require_once("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$lig=mysql_fetch_object($test);
			$login_cpe_etab=$lig->login;
		}

		*/


		// Pour ne pas créer autant de groupes par matière qu'il y a de périodes:
		//$tab_src_id_groupes_crees=array();
		$tab_dst_id_groupes_crees=array();

		// Recherche des infos matières,...
		//$csv_file=$_FILES["csv_file"];
		$fich=fopen($csv_file['tmp_name'],"r");
		while (!feof($fich)) {
			$ligne=fgets($fich, 4096);
			if(trim($ligne)!="") {
				if(substr($ligne,0,20)=="AVIS_CONSEIL_CLASSE;") {
					$tab_tmp=explode(";",$ligne);

					$periode=$tab_tmp[1];
					if(!in_array($periode,$tab_per)) {
						$avis=$tab_tmp[2];


						// A REVOIR: Il y a une économie de requêtes à faire sur les tests ci-dessous en stockant les infos dans un tableau


						// Créer l'association dans 'periodes' si elle n'est pas déjà présente
						$sql="SELECT 1=1 FROM periodes WHERE num_periode='$periode' AND id_classe='$id_classe_etab';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription of association period/class for the period '$periode': ";
							$sql="INSERT INTO periodes SET num_periode='$periode', nom_periode='Période $periode', verouiller='O', id_classe='$id_classe_etab';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERROR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Inscription de l'élève dans la classe
						$sql="SELECT 1=1 FROM j_eleves_classes WHERE periode='$periode' AND id_classe='$id_classe_etab' AND login='$ele_login';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription of the student in the class '$classe_etab' for the period '$periode': ";
							$sql="INSERT INTO j_eleves_classes SET periode='$periode', id_classe='$id_classe_etab', login='$ele_login';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERROR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Insertion de l'avis
						$sql="SELECT 1=1 FROM avis_conseil_classe WHERE login='$ele_login' AND periode='$periode';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription of the opinion of the staff meeting for the period '$periode': ";
							//$sql="INSERT INTO avis_conseil_classe SET login='$ele_login', periode='$periode', avis='$avis';";
							$sql="INSERT INTO avis_conseil_classe SET login='$ele_login', periode='$periode', avis='".my_ereg_replace("_POINT_VIRGULE_",";",$avis)."';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERROR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}
						else {
							// Ca ne devrait pas arriver...
							// Dans la lecture du fichier CSV, on ne retient que les périodes pour lesquelles il n'y a rien sur le bulletin de l'élève dans le nouvel établissement...
							// ... ou plutôt les périodes pour lesquelles l'élève n'est dans aucune classe (donc rien sur le bulletin)
							// Si on passe ici, c'est qu'il y a plusieurs lignes pour l'avis du conseil de classe pour une même période dans ce CSV.
							echo "<p><span style='color:red;'>BIZARRE:</span> It seems that there are several lines of opinion of the staff meeting for the period '$periode'.</p>\n";
						}
					}
				}
				//else {
				elseif(substr($ligne,0,9)=="ABSENCES;") {

					$tab_tmp=explode(";",$ligne);

					$periode=$tab_tmp[1];
					if(!in_array($periode,$tab_per)) {
						$nb_absences=$tab_tmp[2];
						$non_justifie=$tab_tmp[3];
						$nb_retards=$tab_tmp[4];
						$app=$tab_tmp[5];

						$sql="SELECT 1=1 FROM absences WHERE login='$ele_login' AND periode='$periode';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription of the absences/delays for the period '$periode': ";
							$sql="INSERT INTO absences SET login='$ele_login',
															periode='$periode',
															nb_absences='$nb_absences',
															non_justifie='$non_justifie',
															nb_retards='$nb_retards',
															appreciation='".my_ereg_replace("_POINT_VIRGULE_",";",$app)."';";
							//								appreciation='".$app."';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERROR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}
						else {
							// Ca ne devrait pas arriver...
							echo "<p><span style='color:red;'>BIZARRE:</span> It seems that there are several lines of totals of absences for the period '$periode'.</p>\n";
						}
					}
				}
				elseif((substr($ligne,0,20)!="INFOS_ETABLISSEMENT;")&&(substr($ligne,0,12)!="INFOS_ELEVE;")&&(substr($ligne,0,9)!="ABSENCES;")) {
					// $ligne devrait correspondre à une matière
					// Il faudrait identifier auparavant les matières et les associer aux matières du nouvel établissement...

					$tab_tmp=explode(";",$ligne);

					$periode=$tab_tmp[2];
					if(!in_array($periode,$tab_per)) {
						$matiere=$tab_tmp[0];
						$matiere_nom_complet=$tab_tmp[1];

						$src_id_groupe=$tab_tmp[3];

						$note=$tab_tmp[4];
						$statut=$tab_tmp[5];
						$app=$tab_tmp[6];

						// A REVOIR: Il y a une économie de requêtes à faire sur les tests ci-dessous en stockant les infos dans un tableau


						// Créer l'association dans 'periodes' si elle n'est pas déjà présente
						$sql="SELECT 1=1 FROM periodes WHERE num_periode='$periode' AND id_classe='$id_classe_etab';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription of association period/class for the period '$periode': ";
							$sql="INSERT INTO periodes SET num_periode='$periode', nom_periode='Période $periode', verouiller='O', id_classe='$id_classe_etab';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERROR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Inscription de l'élève dans la classe
						$sql="SELECT 1=1 FROM j_eleves_classes WHERE periode='$periode' AND id_classe='$id_classe_etab' AND login='$ele_login';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription of the student in the class '$classe_etab' for the period '$periode': ";
							$sql="INSERT INTO j_eleves_classes SET periode='$periode', id_classe='$id_classe_etab', login='$ele_login';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}


						// Insertion de la matière
						//$sql="SELECT 1=1 FROM matieres WHERE matiere='$matiere' AND nom_complet='$matiere_nom_complet';";
						$sql="SELECT 1=1 FROM matieres WHERE matiere='$matiere';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription of the course '".htmlentities($matiere)."' in the table 'matieres': ";
							$sql="INSERT INTO matieres SET matiere='$matiere', nom_complet='$matiere_nom_complet';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERROR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Insertion de l'association prof/matière
						$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_matiere='$matiere' AND id_professeur='$login_etab';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription of association professor '$login_etab' / course '".htmlentities($matiere)."' in the table 'j_professeurs_matieres': ";
							$sql="INSERT INTO j_professeurs_matieres SET id_matiere='$matiere', id_professeur='$login_etab';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERROR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Insertion du groupe
						// function create_group($_name, $_description, $_matiere, $_classes, $_categorie = 1)

						$tmp_classes=array($id_classe_etab);
						//if(in_array($src_id_groupe,$tab_src_id_groupes_crees) {
						if(isset($tab_dst_id_groupes_crees[$src_id_groupe])) {
							$current_id_groupe=$tab_dst_id_groupes_crees[$src_id_groupe];
						}
						else {
							$current_id_groupe=create_group($matiere, $matiere_nom_complet, $matiere, $tmp_classes);
						}
						//$current_id_groupe=mysql_insert_id();

						/*
						// FAIT PAR LE create_group()
						// Insertion de l'association groupe/classe
						echo "<p>Inscription de l'association groupe/classe dans la table 'j_groupes_classes': ";
						$sql="INSERT INTO j_groupes_classes SET id_groupe='$current_id_groupe', id_classe='$id_classe_etab', coef='1.0';";
						$res=mysql_query($sql);
						if($res) {
							echo "<span style='color:green;'>OK</span>";
							echo "</p>\n";
						}
						else {
							echo "<span style='color:red;'>ERREUR</span>";
							echo "</p>\n";
							require_once("../lib/footer.inc.php");
							die();
						}
						*/

						// Insertion de l'association groupe/élève
						echo "<p>Inscription of the association groups/student in the table 'j_eleves_groupes': ";
						$sql="INSERT INTO j_eleves_groupes SET id_groupe='$current_id_groupe', login='$ele_login', periode='$periode';";
						$res=mysql_query($sql);updateOnline($sql);
						if($res) {
							echo "<span style='color:green;'>OK</span>";
							echo "</p>\n";
						}
						else {
							echo "<span style='color:red;'>ERROR</span>";
							echo "</p>\n";
							require_once("../lib/footer.inc.php");
							die();
						}

						// Insertion de l'association groupe/prof
						$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$current_id_groupe' AND login='$login_etab';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription of the association professor '$login_etab' / group '".htmlentities($current_id_groupe)."' in the table 'j_groupes_professeurs': ";
							$sql="INSERT INTO j_groupes_professeurs SET id_groupe='$current_id_groupe', login='$login_etab';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERROR</span>";
								echo "</p>\n";
								//require_once("../lib/footer.inc.php");
								//die();
							}
						}

						/*
						// FAIT PAR LE create_group()
						// Insertion de l'association groupe/matière
						echo "<p>Inscription de l'association groupe/matière dans la table 'j_groupes_matieres': ";
						$sql="INSERT INTO j_groupes_matieres SET id_groupe='$current_id_groupe', matiere='$matiere';";
						$res=mysql_query($sql);
						if($res) {
							echo "<span style='color:green;'>OK</span>";
							echo "</p>\n";
						}
						else {
							echo "<span style='color:red;'>ERREUR</span>";
							echo "</p>\n";
							require_once("../lib/footer.inc.php");
							die();
						}

						// Il manquait aussi l'insertion dans matiere_categorie...
						*/

						// Insertion de la moyenne
						echo "<p>Inscription of the average on the bulletin in the table 'matieres_notes': ";
						$sql="INSERT INTO matieres_notes SET login='$ele_login', id_groupe='$current_id_groupe', periode='$periode', note='$note', statut='$statut';";
						$res=mysql_query($sql);updateOnline($sql);
						if($res) {
							echo "<span style='color:green;'>OK</span>";
							echo "</p>\n";
						}
						else {
							echo "<span style='color:red;'>ERROR</span>";
							echo "</p>\n";
							require_once("../lib/footer.inc.php");
							die();
						}



						// Insertion de l'appréciation
						/*
						$sql="SELECT 1=1 FROM matieres_appreciations WHERE login='$ele_login' AND id_groupe='$current_id_groupe' AND periode='$periode';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
						*/
							echo "<p>Inscription of the appreciation for the course '".htmlentities($matiere)."' over the period '$periode': ";
							//$sql="INSERT INTO matieres_appreciations SET login='$ele_login', periode='$periode', id_groupe='$current_id_groupe', appreciation='$app';";
							$sql="INSERT INTO matieres_appreciations SET login='$ele_login', periode='$periode', id_groupe='$current_id_groupe', appreciation='".my_ereg_replace("_POINT_VIRGULE_",";",$app)."';";
							$res=mysql_query($sql);updateOnline($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERROR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						/*
						}
						else {
							// Ca ne devrait pas arriver...
							echo "<p><span style='color:red;'>BIZARRE:</span> Il semble qu'il y ait plusieurs lignes d'appréciation pour un même groupe sur la période '$periode'.</p>\n";
						}
						*/

						// Stockage de l'ancien groupe comme déjà créé:
						//$tab_src_id_groupes_crees[]=$src_id_groupe;
						$tab_dst_id_groupes_crees[$src_id_groupe]=$current_id_groupe;

					}
				}
			}
		}
		fclose($fich);
		//echo "</p>\n";

		echo "<p>Finished.</p>\n";

	}
}


// Inclusion du bas de page
require_once("../lib/footer.inc.php");
?>
