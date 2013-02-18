<?php
@set_time_limit(0);
/*
* $Id: professeurs.php 8272 2011-09-19 14:58:27Z crob $
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
extract($_POST, EXTR_OVERWRITE);

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

//================================================
// Fonction de génération de mot de passe récupérée sur TotallyPHP
// Aucune mention de licence pour ce script...

/*
 * The letter l (lowercase L) and the number 1
 * have been removed, as they can be mistaken
 * for each other.
*/

function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    //while ($i <= 7) {
    while ($i <= 5) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;
}
//================================================

function affiche_debug($texte){
	// Passer à 1 la variable pour générer l'affichage des infos de debug...
	$debug=0;
	if($debug==1){
		echo "<font color='green'>".$texte."</font>";
		flush();
	}
}


include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_professeurs;

//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year : Importation of the professors";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

require_once("init_xml_lib.php");

// On vérifie si l'extension d_base est active
//verif_active_dbase();

?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return home initialization</a></p>
<?php
echo "<center><h3 class='gepi'>Fourth phase of initialization<br />Importation of the professors</h3></center>\n";

if (!isset($step1)) {
	$j=0;
	$flag=0;
	$chaine_tables="";
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1) {
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$flag=1;
			}
		}
		$j++;
	}

	for($loop=0;$loop<count($liste_tables_del);$loop++) {
		if($chaine_tables!="") {$chaine_tables.=", ";}
		$chaine_tables.="'".$liste_tables_del[$loop]."'";
	}

	$test = mysql_result(mysql_query("SELECT count(*) FROM utilisateurs WHERE statut='professeur'"),0);
	if ($test != 0) {$flag=1;}

	if ($flag != 0){
		echo "<p><b>CAUTION ...</b><br />\n";
		echo "Data concerning the professors are currently present in base GEPI<br /></p>\n";
		echo "<p>If you continue the procedure the data such as notes, appreciations, ... will be erased.</p>\n";

		echo "<p>Emptied tables will be&nbsp;: $chaine_tables</p>\n";

		echo "<ul><li>Only the table containing the users (professors, admin, ...) and the table connecting the courses and the professors will be preserved.</li>\n";
		echo "<li>Professors of the last year present in base GEPI and not present in file XML of this year are not erased base GEPI but simply declared \"inactive\".</li>\n";
		echo "</ul>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();
		echo "<input type=hidden name='step1' value='y' />\n";
		echo "<input type='submit' name='confirm' value='Continue the procedure' />\n";
		echo "</form>\n";
		echo "</div>\n";
		echo "</body>\n";
		echo "</html>\n";
		die();
	}
}

if (!isset($is_posted)) {
	if(isset($step1)) {
		$dirname=get_user_temp_directory();

		$sql="SELECT * FROM j_professeurs_matieres WHERE ordre_matieres='1';";
		$res_matiere_principale=mysql_query($sql);
		if(mysql_num_rows($res_matiere_principale)>0) {
			$fich_mp=fopen("../temp/".$dirname."/matiere_principale.csv","w+");
			if($fich_mp) {
				echo "<p>Creation of a file of backup of the principal course of each professor.</p>\n";
				while($lig_mp=mysql_fetch_object($res_matiere_principale)) {
					fwrite($fich_mp,"$lig_mp->id_professeur;$lig_mp->id_matiere\n");
				}
				fclose($fich_mp);
			}
			else {
				echo "<p style='color:red'>Failure of the creation of a file of backup of the principal course of each professor.</p>\n";
			}
		}

		$sql="SELECT * FROM j_professeurs_matieres ORDER BY ordre_matieres;";
		$res_matieres_profs=mysql_query($sql);
		if(mysql_num_rows($res_matieres_profs)>0) {
			$fich_mp=fopen("../temp/".$dirname."/matieres_profs_an_dernier.csv","w+");
			if($fich_mp) {
				echo "<p>Creation of a file of backup of the courses (<i>of the last year</i>) of each professor.</p>\n";
				while($lig_mp=mysql_fetch_object($res_matieres_profs)) {
					fwrite($fich_mp,"$lig_mp->id_professeur;$lig_mp->id_matiere\n");
				}
				fclose($fich_mp);
			}
			else {
				echo "<p style='color:red'>Failure of the creation of a file of backup of the courses (<i>of the last year</i>) of each professor.</p>\n";
			}
		}

		check_token(false);
		$j=0;
		while ($j < count($liste_tables_del)) {
			$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE '$liste_tables_del[$j]'"));
			if($test==1){
				if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
					$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
				}
			}
			$j++;
		}
	}
	$del = @mysql_query("DELETE FROM tempo2");

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	//echo "<p>Importation du fichier <b>F_wind.csv</b> contenant les données relatives aux professeurs.";

	echo "<p>Importation of the file <b>sts.xml</b> containing the data relating to the professors.\n";
	//echo "<p>Veuillez préciser le nom complet du fichier <b>F_wind.csv</b>.";
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=hidden name='step1' value='y' />\n";
	//echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<br /><br /><p>Which formula to apply for the generation of the login ?</p>\n";

	if(getSettingValue("use_ent")!='y') {
		$default_login_gen_type=getSettingValue('login_gen_type');
		if($default_login_gen_type=='') {$default_login_gen_type='name';}
	}
	else {
		$default_login_gen_type="";
	}

	if(getSettingValue('auth_sso')=="lcs") {
		echo "<span style='color:red'>Your Gepi uses a LCS authentification ; The format of login below will not be taken into account. The accounts must be imported in directory LDAP of the LCS before
carrying out the importation in GEPI.</span><br />\n";
	}

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_name' value='name' ";
	if($default_login_gen_type=='name') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_name'  style='cursor: pointer;'>name</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_name8' value='name8' ";
	if($default_login_gen_type=='name8') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_name8'  style='cursor: pointer;'>name (truncated to 8 characters)</label>\n";
	echo "<br />";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_fname8' value='fname8' ";
	if($default_login_gen_type=='fname8') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_fname8'  style='cursor: pointer;'>pname (truncated to 8 characters)</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_fname19' value='fname19' ";
	if($default_login_gen_type=='fname19') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_fname19'  style='cursor: pointer;'>pname (truncated to 19 characters)</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_firstdotname' value='firstdotname' ";
	if($default_login_gen_type=='firstdotname') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_firstdotname'  style='cursor: pointer;'>first name.name</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_firstdotname19' value='firstdotname19' ";
	if($default_login_gen_type=='firstdotname19') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_firstdotname19'  style='cursor: pointer;'>first name.name (truncated to 19 characters)</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_namef8' value='namef8' ";
	if($default_login_gen_type=='namef8') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_namef8'  style='cursor: pointer;'>namep (truncated to 8 characters)</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_lcs' value='lcs' ";
	if($default_login_gen_type=='lcs') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_lcs'  style='cursor: pointer;'>pname(façon LCS)</label>\n";
	echo "<br />\n";

	if (getSettingValue("use_ent") == "y") {
		echo "<input type='radio' name='login_gen_type' id='login_gen_type_ent' value='ent' checked=\"checked\" />\n";
		echo "<label for='login_gen_type_ent'  style='cursor: pointer;'>
			The logins are produced by a ENT (<span title=\"You must adapt the code of the file above towards line 710.\">Caution !</span>)</label>\n";
		echo "<br />\n";
	}
	echo "<br />\n";

	// Modifications jjocal dans le cas où c'est un serveur CAS qui s'occupe de tout
	if((getSettingValue("use_sso") == "cas")||(getSettingValue('auth_sso')=="lcs")) {
		$checked1 = ' checked="checked"';
		$checked0 = '';
	}else{
		$checked1 = '';
		$checked0 = ' checked="checked"';
	}

	echo "<p>These accounts will be used in Single Sign-on with CASE or LemonLDAP ? (<i>leave 'no' if you do not know what it is</i>)</p>\n";
	echo "<input type='radio' name='sso' id='sso_n' value='no'".$checked0." /> <label for='sso_n' style='cursor: pointer;'>No</label>\n";
	echo "<br /><input type='radio' name='sso' id='sso_y' value='yes'".$checked1." /> <label for='sso_y' style='cursor: pointer;'>Yes (no password will be generated)</label>\n";
	echo "<br />\n";
	echo "<br />\n";


	echo "<p>If the answer to the previous question is No, do you want to:</p>\n";
	echo "<p><input type=\"radio\" name=\"mode_mdp\" id='mode_mdp_alea' value=\"alea\" checked /> <label for='mode_mdp_alea' style='cursor: pointer;'> generate a random password for each professor</label>.<br />\n";
	echo "<input type=\"radio\" name=\"mode_mdp\" id='mode_mdp_date' value=\"date\" /> <label for='mode_mdp_date' style='cursor: pointer;'>rather use the date of birth to the format 'aaaammjj' as initial password (<i>it will have to be modified at the first login</i>)</label>.</p>\n";
	echo "<br />\n";

	echo "<p><input type='submit' value='Validate' /></p>\n";
	echo "</form>\n";
	echo "<p><br /></p>\n";

}
else {
	check_token();

	if(isset($_POST['login_gen_type'])) {
		saveSetting('login_gen_type',$_POST['login_gen_type']);
	}

	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>It seems that the temporary folder of the user ".$_SESSION['login']." is not defined!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}

	$dest_file="../temp/".$tempdir."/sts.xml";
	/*
	$fp=fopen($dest_file,"r");
	if(!$fp){
		echo "<p>Le XML STS Emploi du temps n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	*/

	$sts_xml=simplexml_load_file($dest_file);
	if(!$sts_xml) {
		echo "<p style='color:red;'>FAILURE of the loading of the file with simpleXML.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$nom_racine=$sts_xml->getName();
	if(strtoupper($nom_racine)!='STS_EDT') {
		echo "<p style='color:red;'>ERROR: Provided file XML does not seem to be a file XML STS_EMP_&lt;RNE&gt;_&lt;ANNEE&gt;.<br />Its root should be 'STS_EDT'.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>";
	echo "Analyze file to extract information from the section INDIVIDUALS...<br />\n";

	$prof=array();
	$i=0;

	$tab_champs_personnels=array("NOM_USAGE",
	"NOM_PATRONYMIQUE",
	"PRENOM",
	"SEXE",
	"CIVILITE",
	"DATE_NAISSANCE",
	"GRADE",
	"FONCTION");

	$prof=array();
	$i=0;

	foreach($sts_xml->DONNEES->INDIVIDUS->children() as $individu) {
		$prof[$i]=array();

		//echo "<span style='color:orange'>\$individu->NOM_USAGE=".$individu->NOM_USAGE."</span><br />";

		foreach($individu->attributes() as $key => $value) {
			// <INDIVIDU ID="4189" TYPE="epp">
			$prof[$i][strtolower($key)]=trim(traite_utf8($value));
		}

		// Champs de l'individu
		foreach($individu->children() as $key => $value) {
			if(in_array(strtoupper($key),$tab_champs_personnels)) {
				if(strtoupper($key)=='SEXE') {
					$prof[$i]["sexe"]=trim(preg_replace("/[^1-2]/","",$value));
				}
				elseif(strtoupper($key)=='CIVILITE') {
					$prof[$i]["civilite"]=trim(preg_replace("/[^1-3]/","",$value));
				}
				elseif((strtoupper($key)=='NOM_USAGE')||
				(strtoupper($key)=='NOM_PATRONYMIQUE')||
				(strtoupper($key)=='NOM_USAGE')) {
					$prof[$i][strtolower($key)]=trim(preg_replace("/[^A-Za-z -]/","",traite_utf8($value)));
				}
				elseif(strtoupper($key)=='PRENOM') {
					$prof[$i][strtolower($key)]=trim(preg_replace("/[^A-Za-zÆæ¼½".$liste_caracteres_accentues." -]/","",traite_utf8($value)));
				}
				elseif(strtoupper($key)=='DATE_NAISSANCE') {
					$prof[$i][strtolower($key)]=trim(preg_replace("/[^0-9-]/","",traite_utf8($value)));
				}
				elseif((strtoupper($key)=='GRADE')||
					(strtoupper($key)=='FONCTION')) {
					$prof[$i][strtolower($key)]=trim(preg_replace('/"/','',traite_utf8($value)));
				}
				else {
					$prof[$i][strtolower($key)]=trim(traite_utf8($value));
				}
				//echo "\$prof[$i][".strtolower($key)."]=".$prof[$i][strtolower($key)]."<br />";
			}
		}

		if(isset($individu->PROFS_PRINC)) {
		//if($temoin_prof_princ>0) {
			$j=0;
			foreach($individu->PROFS_PRINC->children() as $prof_princ) {
				//$prof[$i]["prof_princ"]=array();
				foreach($prof_princ->children() as $key => $value) {
					$prof[$i]["prof_princ"][$j][strtolower($key)]=trim(traite_utf8(preg_replace('/"/',"",$value)));
					$temoin_au_moins_un_prof_princ="oui";
				}
				$j++;
			}
		}

		//if($temoin_discipline>0) {
		if(isset($individu->DISCIPLINES)) {
			$j=0;
			foreach($individu->DISCIPLINES->children() as $discipline) {
				foreach($discipline->attributes() as $key => $value) {
					if(strtoupper($key)=='CODE') {
						$prof[$i]["disciplines"][$j]["code"]=trim(traite_utf8(preg_replace('/"/',"",$value)));
						break;
					}
				}

				foreach($discipline->children() as $key => $value) {
					$prof[$i]["disciplines"][$j][strtolower($key)]=trim(traite_utf8(preg_replace('/"/',"",$value)));
				}
				$j++;
			}
		}

		if($debug_import=='y') {
			echo "<pre style='color:green;'><b>Table \$prof[$i]&nbsp;:</b>";
			print_r($prof[$i]);
			echo "</pre>";
		}

		$i++;
	}

	// Les $prof[$i]["disciplines"] ne sont pas utilisées sauf à titre informatif à l'affichage...
	// Les $prof[$i]["prof_princ"][$j]["code_structure"] peuvent être exploitées à ce niveau pour désigner les profs principaux.

	//========================================================

	// On commence par rendre inactifs tous les professeurs
	$req = mysql_query("UPDATE utilisateurs set etat='inactif' where statut = 'professeur'");

	// on efface la ligne "display_users" dans la table "setting" de façon à afficher tous les utilisateurs dans la page  /utilisateurs/index.php
	$req = mysql_query("DELETE from setting where NAME = 'display_users'");


	if(getSettingValue('auth_sso')=='lcs') {
		require_once("../lib/lcs.inc.php");
		$ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");
	}

	echo "<p>In the table below, the identifiers in red correspond to new professors in base GEPI. the identifiers in green correspond to professors detected in files
CSV but already present in base GEPI.<br /><br />It is possible that certain professors below, although appearing in file CSV, are not any more in exercise in your school this year. This is why it will be proposed to you at the end of the procedure of
initialization, a cleaning of the base in order to remove these useless data.</p>\n";
	echo "<table border='1' class='boireaus' cellpadding='2' cellspacing='2' summary='Table of the professors'>\n";
	echo "<tr><th><p class=\"small\">Identifier of the professor</p></th><th><p class=\"small\">Name</p></th><th><p class=\"small\">First name</p></th><th>Password *</th></tr>\n";


	srand();

	$nb_reg_no = 0;

	$tab_nouveaux_profs=array();

	$info_pb_mdp="";

	$alt=1;
	for($k=0;$k<count($prof);$k++){
		//if(isset($prof[$k]["fonction"])) {
		//	if($prof[$k]["fonction"]=="ENS"){

		if(((isset($prof[$k]["fonction"]))&&($prof[$k]["fonction"]=="ENS"))||
			((!isset($prof[$k]["fonction"]))&&(isset($prof[$k]["nom_usage"]))&&(isset($prof[$k]["prenom"])))) {

				$civilite="M.";
				if(isset($prof[$k]["sexe"])) {
					if($prof[$k]["sexe"]=="2"){
						$civilite="Mme";
					}
					else{
						$civilite="M.";
					}
				}

				if(isset($prof[$k]["civilite"])) {
					switch($prof[$k]["civilite"]){
						case 1:
							$civilite="M.";
							break;
						case 2:
							$civilite="Mme";
							break;
						case 3:
							$civilite="Mlle";
							break;
					}
				}

				if($_POST['mode_mdp']=="alea") {
					$mdp=createRandomPassword();
				}
				elseif(!isset($prof[$k]["date_naissance"])) {
					// Cela peut arriver avec des personnes ajoutées dans STS par le principal
					// Elles peuvent apparaitre avec
					/*
						<INDIVIDU ID="3506" TYPE="local">
							<SEXE/>
							<CIVILITE>3</CIVILITE>
							<NOM_USAGE>ZETOFREY</NOM_USAGE>
							<NOM_PATRONYMIQUE/>
							<PRENOM>MELANIE</PRENOM>
						</INDIVIDU>
					*/
					$mdp=createRandomPassword();
					$info_pb_mdp.="<p style='color:red'>".$prof[$k]["nom_usage"]." ".casse_mot($prof[$k]["prenom"],'majf2')." does not have well informed date of birth.<br />Its password is generated randomly.</p>\n";
				}
				else{
					$date=str_replace("-","",$prof[$k]["date_naissance"]);
					$mdp=$date;
				}

				//echo $prof[$k]["nom_usage"].";".$prof[$k]["prenom"].";".$civi.";"."P".$prof[$k]["id"].";"."ENS".";".$date."<br />\n";
				//$chaine=$prof[$k]["nom_usage"].";".$prof[$k]["prenom"].";".$civi.";"."P".$prof[$k]["id"].";"."ENS".";".$mdp;


				$prenoms = explode(" ",$prof[$k]["prenom"]);
				$premier_prenom = $prenoms[0];
				$prenom_compose = '';
				if (isset($prenoms[1])) $prenom_compose = $prenoms[0]."-".$prenoms[1];

				$lcs_prof_en_erreur="n";
				if(getSettingValue('auth_sso')=='lcs') {
					$lcs_prof_en_erreur="y";
					$exist = 'no';
					if($prof[$k]["id"]!='') {
						$login_prof_gepi=get_lcs_login($prof[$k]["id"], 'professeur');
						//echo "get_lcs_login(".$prof[$k]["id"].", 'professeur')=".$login_prof_gepi."<br />";
						if($login_prof_gepi!='') {
							$lcs_prof_en_erreur="n";
							$sql="SELECT 1=1 FROM utilisateurs WHERE login='$login_prof_gepi';";
							$test_exist_prof=mysql_query($sql);
							if(mysql_num_rows($test_exist_prof)>0) {
								$exist = 'yes';
							}
							else {
								$exist = 'no';
							}
						}
						else {
							$lcs_prof_en_erreur="y";
						}
					}
				}
				else {
					// On effectue d'abord un test sur le NUMIND
					$sql="select login from utilisateurs where (
					numind='P".$prof[$k]["id"]."' and
					numind!='' and
					statut='professeur')";
					//echo "<tr><td>$sql</td></tr>";
					$test_exist = mysql_query($sql);
					$result_test = mysql_num_rows($test_exist);
					if ($result_test == 0) {
						// On tente ensuite une reconnaissance sur nom/prénom, si le test NUMIND a échoué
						$sql="select login from utilisateurs where (
						nom='".traitement_magic_quotes($prof[$k]["nom_usage"])."' and
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
								nom='".traitement_magic_quotes($prof[$k]["nom_usage"])."' and
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
					} else {
						$exist = 'yes';
						$login_prof_gepi = mysql_result($test_exist,0,'login');
					}
				}

				if($lcs_prof_en_erreur=="y") {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td><p><font color='red'>Not found in directory LDAP</font></p></td><td><p>".$prof[$k]["nom_usage"]."</p></td><td><p>".$premier_prenom."</p></td><td>&nbsp;</td></tr>\n";
				}
				/*
				elseif(getSettingValue('auth_sso')=='lcs') {
					if ($exist == 'no') {
						// On devrait récupérer nom, prénom,... du LDAP du LCS...
					}
					else {
					}
				}
				*/
				else {
					if ($exist == 'no') {
	
						// Aucun professeur ne porte le même nom dans la base GEPI. On va donc rentrer ce professeur dans la base
	
						$prof[$k]["prenom"]=traitement_magic_quotes(corriger_caracteres($prof[$k]["prenom"]));
	
						if ($_POST['login_gen_type'] == "name") {
							$temp1 = $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							//$temp1 = substr($temp1,0,8);
	
						} elseif ($_POST['login_gen_type'] == "name8") {
							$temp1 = $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,8);
						} elseif ($_POST['login_gen_type'] == "fname8") {
							$temp1 = $prof[$k]["prenom"]{0} . $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,8);
						} elseif ($_POST['login_gen_type'] == "fname19") {
							$temp1 = $prof[$k]["prenom"]{0} . $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,19);
						} elseif ($_POST['login_gen_type'] == "firstdotname") {
							if ($prenom_compose != '') {
								$firstname = $prenom_compose;
							} else {
								$firstname = $premier_prenom;
							}
	
							$temp1 = $firstname . "." . $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
	
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							//$temp1 = substr($temp1,0,19);
						} elseif ($_POST['login_gen_type'] == "firstdotname19") {
							if ($prenom_compose != '') {
								$firstname = $prenom_compose;
							} else {
								$firstname = $premier_prenom;
							}
	
							$temp1 = $firstname . "." . $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,19);
						} elseif ($_POST['login_gen_type'] == "namef8") {
							$temp1 =  substr($prof[$k]["nom_usage"],0,7) . $prof[$k]["prenom"]{0};
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							//$temp1 = substr($temp1,0,8);
						} elseif ($_POST['login_gen_type'] == "lcs") {
							$nom = $prof[$k]["nom_usage"];
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
							$prenom = $prof[$k]["prenom"];
							$prenom1 = $prof[$k]["prenom"]{0};
							$temp1 = $prenom1 . $nom1;
							$temp1 = remplace_accents($temp1,"all");
						}
						elseif($_POST['login_gen_type'] == 'ent'){
	
							if (getSettingValue("use_ent") == "y") {
								// Charge à l'organisme utilisateur de pourvoir à cette fonctionnalité
								// le code suivant n'est qu'une méthode proposée pour relier Gepi à un ENT
								$bx = 'oui';
								if (isset($bx) AND $bx == 'oui') {
									// On va chercher le login de l'utilisateur dans la table créée
									$sql_p = "SELECT login_u FROM ldap_bx
												WHERE nom_u = '".strtoupper($prof[$k]["nom_usage"])."'
												AND prenom_u = '".strtoupper($prof[$k]["prenom"])."'
												AND statut_u = 'teacher'";
									$query_p = mysql_query($sql_p);
									$nbre = mysql_num_rows($query_p);
									if ($nbre >= 1 AND $nbre < 2) {
										$temp1 = mysql_result($query_p, 0,"login_u");
									}else{
										// Il faudrait alors proposer une alternative à ce cas
										$temp1 = "erreur_".$k;
									}
								}
							}
							else{
								die('You did not authorize Gepi to use a ENT');
							}
						}

						if(getSettingValue('auth_sso')=='lcs') {
							$login_prof=$login_prof_gepi;
						}
						else {
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
						}
						$prof[$k]["nom_usage"] = traitement_magic_quotes(corriger_caracteres($prof[$k]["nom_usage"]));
						// Mot de passe et change_mdp
	
						$changemdp = 'y';
	
						//echo "<tr><td colspan='4'>strlen($affiche[5])=".strlen($affiche[5])."<br />\$affiche[4]=$affiche[4]<br />\$_POST['sso']=".$_POST['sso']."</td></tr>";
						if(getSettingValue('auth_sso')=="lcs") {
							$pwd = '';
							$mess_mdp = "aucun (sso)";
							$changemdp = 'n';
						}
						elseif (strlen($mdp)>2 and (!isset($prof[$k]["fonction"]) or $prof[$k]["fonction"]=="ENS") and $_POST['sso'] == "no") {
							//
							$pwd = md5(trim($mdp));
							//$mess_mdp = "NUMEN";
							if($_POST['mode_mdp']=='alea'){
								$mess_mdp = "$mdp";
							}
							elseif(!isset($prof[$k]["date_naissance"])) {
								$mess_mdp = "$mdp";
							}
							else{
								$mess_mdp = "Password according to the date of birth";
							}
							//echo "<tr><td colspan='4'>NUMEN: $affiche[5] $pwd</td></tr>";
						} elseif ($_POST['sso']== "no") {
							$pwd = md5(rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9));
							$mess_mdp = $pwd;
							//echo "<tr><td colspan='4'>Choix 2: $pwd</td></tr>";
							// $mess_mdp = "Inconnu (compte bloqué)";
						} elseif ($_POST['sso'] == "yes") {
							$pwd = '';
							$mess_mdp = "aucun (sso)";
							$changemdp = 'n';
							//echo "<tr><td colspan='4'>sso</td></tr>";
						}
	
						// utilise le prénom composé s'il existe, plutôt que le premier prénom
	
						//$res = mysql_query("INSERT INTO utilisateurs VALUES ('".$login_prof."', '".$prof[$k]["nom_usage"]."', '".$premier_prenom."', '".$civilite."', '".$pwd."', '', 'professeur', 'actif', 'y', '')");
						//$sql="INSERT INTO utilisateurs SET login='$login_prof', nom='".$prof[$k]["nom_usage"]."', prenom='$premier_prenom', civilite='$civilite', password='$pwd', statut='professeur', etat='actif', change_mdp='y'";
						$sql="INSERT INTO utilisateurs SET login='$login_prof', nom='".$prof[$k]["nom_usage"]."', prenom='$premier_prenom', civilite='$civilite', password='$pwd', statut='professeur', etat='actif', change_mdp='".$changemdp."', numind='P".$prof[$k]["id"]."'";
						if(getSettingValue('auth_sso')=='lcs') {
							$sql.=", auth_mode='sso'";
						}
						$res = mysql_query($sql);
						// Pour debug:
						//echo "<tr><td colspan='4'>$sql</td></tr>";
	
						$tab_nouveaux_profs[]="$login_prof|$mess_mdp";
	
						if(!$res){$nb_reg_no++;}
						$res = mysql_query("INSERT INTO tempo2 VALUES ('".$login_prof."', '"."P".$prof[$k]["id"]."')");
	
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td><p><font color='red'>".$login_prof."</font></p></td><td><p>".$prof[$k]["nom_usage"]."</p></td><td><p>".$premier_prenom."</p></td><td>".$mess_mdp."</td></tr>\n";
					} else {
						//$res = mysql_query("UPDATE utilisateurs set etat='actif' where login = '".$login_prof_gepi."'");
						// On corrige aussi les nom/prénom/civilité et numind parce que la reconnaissance a aussi pu se faire sur le nom/prénom
						$sql="UPDATE utilisateurs set etat='actif', nom='".$prof[$k]["nom_usage"]."', prenom='$premier_prenom', civilite='$civilite', numind='P".$prof[$k]["id"]."'";
						if(getSettingValue('auth_sso')=='lcs') {
							$sql.=", auth_mode='sso'";
						}
						$sql.=" where login = '".$login_prof_gepi."';";
						$res = mysql_query($sql);

						if(!$res) $nb_reg_no++;
						$res = mysql_query("INSERT INTO tempo2 VALUES ('".$login_prof_gepi."', '"."P".$prof[$k]["id"]."')");
	
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td><p><font color='green'>".$login_prof_gepi."</font></p></td><td><p>".$prof[$k]["nom_usage"]."</p></td><td><p>".$prof[$k]["prenom"]."</p></td><td>Unchanged</td></tr>\n";
					}
				}
			//}
		}
	}
	echo "</table>\n";


	if((isset($tab_nouveaux_profs))&&(count($tab_nouveaux_profs)>0)) {
		echo "<form action='../utilisateurs/impression_bienvenue.php' method='post' target='_blank'>\n";
		echo "<p>Print the cards welcome for the new professors&nbsp;: \n";
		for($i=0;$i<count($tab_nouveaux_profs);$i++) {
			$tmp_tab=explode('|',$tab_nouveaux_profs[$i]);
			echo "<input type='hidden' name='user_login[]' value='$tmp_tab[0]' />\n";
			echo "<input type='hidden' name='mot_de_passe[]' value='$tmp_tab[1]' />\n";
		}
		echo "<input type='submit' value='Print' /></p>\n";
		echo "</form>\n";
	}

	if((isset($info_pb_mdp))&&($info_pb_mdp!="")) {
		echo $info_pb_mdp;
	}

	if ($nb_reg_no != 0) {
		echo "<p>During recording of the data there was <span style='color:red;'>$nb_reg_no errors</span>. Test find the cause of the error and restart the procedure before passing to the following stage.\n";
	}
	else {
		echo "<p>The importation of the professors in base GEPI was carried out successfully !</p>\n";

		/*
		echo "<p><b>* Précision sur les mots de passe (en non-SSO) :</b><br />
		(il est conseillé d'imprimer cette page)</p>
		<ul>
		<li>Lorsqu'un nouveau professeur est inséré dans la base GEPI, son mot de passe lors de la première
		connexion à GEPI est son NUMEN.</li>
		<li>Si le NUMEM n'est pas disponible dans le fichier F_wind.csv, GEPI génère aléatoirement
		un mot de passe.</li></ul>";
		*/
		echo "<p><b>* Precision on the passwords (in non-SSO) :</b></p>\n";
		echo "<ul>
		<li>When a new professor is inserted in base GEPI, its password during first connection to GEPI is that selected at the previous stage:<br />
			<ul>
			<li>Password according to the date of birth to the format 'aaaammjj', ou</li>
			<li>a password generated randomly by GEPI.<br />(it is then advised to print this page)</li>
			</ul>
		</ul>\n";
		if ($_POST['sso'] != "yes") {
			echo "<p><b>In all the cases the new user is brought to change his password during his first connection.</b></p>\n";
		}
		echo "<br />\n<p>You can proceed to the fifth phase of assignment of the courses to each professor, of assignment of the professors in each class and of definition of the options followed by the students.</p>\n";
	}


	// Création du f_div.csv pour l'import des profs principaux plus loin
	affiche_debug("Creation of f_div.csv for the importation of principal Profs during another stage.<br />\n");
	$fich=fopen("../temp/$tempdir/f_div.csv","w+");
	$chaine="DIVCOD;NUMIND";
	if($fich){
		fwrite($fich,html_entity_decode_all_version($chaine)."\n");
	}
	affiche_debug($chaine."<br />\n");

	$tabchaine=array();
	for($m=0;$m<count($prof);$m++){
		if(isset($prof[$m]["prof_princ"])){
			for($n=0;$n<count($prof[$m]["prof_princ"]);$n++){
				$tabchaine[]=$prof[$m]["prof_princ"][$n]["code_structure"].";"."P".$prof[$m]["id"];
				//$chaine=$prof[$m]["prof_princ"][$n]["code_structure"].";"."P".$prof[$m]["id"];
				//if($fich){
				//	fwrite($fich,html_entity_decode_all_version($chaine)."\n");
				//}
				affiche_debug($chaine."<br />\n");
			}
		}
	}
	sort($tabchaine);
	for($i=0;$i<count($tabchaine);$i++){
		if($fich){
			fwrite($fich,html_entity_decode_all_version($tabchaine[$i])."\n");
		}
	}
	fclose($fich);


	if (getSettingValue("use_ent") == "y"){

		echo '<p style="text-align: center; font-weight: bold;"><a href="../mod_ent/gestion_ent_profs.php">Check the logins before continuing</a></p>'."\n";

	} else {

		echo "<p>The creation of the courses can be done in three different ways (<i>by order of preferably</i>)&nbsp;:</p>\n";

		echo "<ul>\n";
		echo "<li>\n";
		//  style="text-align: center; font-weight: bold;"
		echo "<p>";
		echo "If your timetable went up towards STS, you have a file <b>sts_emp_RNE_ANNEE.xml</b>&nbsp;:";
		echo "<br />";
		echo "<a href='prof_disc_classe_csv.php?a=a".add_token_in_url()."'>Proceed to the fifth phase of initialization</a></p>\n";
		echo "</li>\n";

		echo "<li>\n";
		echo "<p>If the increase towards STS were not carried out yet, you can carry out the initialization of the courses starting from an export CSV of UnDeuxTemps&nbsp;: <br /><a href='traite_csv_udt.php?a=a".add_token_in_url()."'>Procéder à la cinquième phase d'initialisation</a><br />(<i>procédure encore expérimentale... il se peut que vous ayez des groupes en trop</i>)</p>\n";
		echo "</li>\n";

		echo "<li>\n";
		echo "<p>If you do not have either  UnDeuxTemps CSV export&nbsp;: <br /><a href='init_alternatif.php?'>Alternative initialization of the courses</a><br />(<i>the most tiresome mode</i>)</p>\n";
		echo "</li>\n";

		echo "</ul>\n";

	}

	echo "<p><br /></p>\n";
}

require("../lib/footer.inc.php");
?>