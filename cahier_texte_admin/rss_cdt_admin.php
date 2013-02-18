<?php

/**
 *
 * @version $Id: rss_cdt_admin.php 5951 2010-11-22 17:05:48Z crob $
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

$accessibilite="y";
$titre_page = "Parameterize rss flows of textbook";
$affiche_connexion = "oui";
$niveau_arbo = 1;
$gepiPathJava="./..";
  $post_reussi=FALSE;

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
//
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// ======================== Initialisation des données ==================== //
$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$rss_cdt_ele = isset($_POST["rss_cdt_ele"]) ? $_POST["rss_cdt_ele"] : NULL;
$rss_acces_ele = isset($_POST["rss_acces_ele"]) ? $_POST["rss_acces_ele"] : NULL;
$genereflux = isset($_GET["genereflux"]) ? $_GET["genereflux"] : NULL;
$generefluxcsv = isset($_GET["generefluxcsv"]) ? $_GET["generefluxcsv"] : NULL;
$msg = $result = NULL;
$lienFlux=array();
$a=0;




// ======================== Traitement des données ======================== //
if ($action == "modifier") {
	check_token();
	$save = saveSetting("rss_cdt_eleve", $rss_cdt_ele);
	if (!$save) {
		$msg .= '<p class="red">The modification was not recorded.</p>'."\n";
	}

}
if (isset($rss_acces_ele)) {
	check_token();
	$save_d = saveSetting("rss_acces_ele", $rss_acces_ele);
	if (!$save_d) {
		$msg .= '<p class="red">The modification was not recorded.</p>';
	}
}

// On teste si l'admin veut autoriser les flux pour créer la table adéquate
  $test_table = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'rss_users'"));

if (getSettingValue("rss_cdt_eleve") == "y" AND $genereflux == "y") {
	check_token();
	$suivant = "non";
	// on teste si la table existe déjà et on la crée le cas échéant
	$result .= '<p>Gepi checks if the table necessary are in the base.</p>';

    if ($test_table == 0) {
		$query1 = mysql_query("CREATE TABLE `rss_users` (`id` int(11) NOT NULL auto_increment, `user_login` varchar(30) NOT NULL, `user_uri` varchar(30) NOT NULL, PRIMARY KEY  (`id`));");
        if ($query1) {
            $result .= "<span class='green'>The necessary table  is created !</span><br />";
			$creeTable=1;
            $suivant = "oui";
        } else {
			$result .= "<span class='red'>Error during the creation of the table.</span><br />";
			$creeTable=0;
		}
	} else {
		$result .= "<font class='blue'>The table already exists.</span><br />";
		$creeTable=2;
		$suivant = "oui";
	}

	// ICI, on remplit la table
	// Mais on la vide avant de la re-remplir (ou de la remplir).
	$truncate = mysql_query("TRUNCATE TABLE `rss_users`");

	$select_el = mysql_query("SELECT DISTINCT login FROM eleves ORDER BY nom, prenom");
	$erreur = '';

	while($rep_el = mysql_fetch_array($select_el)){

		// On produit une URI pour chaque utilisateur à partir du login, du RNE et d'un nombre aléatoire
		$uri_el = md5($rep_el["login"].getSettingValue("gepiSchoolRne").mt_rand());
		$insert = mysql_query("INSERT INTO rss_users (id, user_login, user_uri) VALUES ('', '".$rep_el["login"]."', '".$uri_el."')");
		updateOnline("INSERT INTO rss_users (id, user_login, user_uri) VALUES ('', '".$rep_el["login"]."', '".$uri_el."')");
		if (!$insert) {
			$erreur .= 'Error on '.$rep_el["login"].'<br />';
		}

	}

	if ($erreur == '') {
		$msg .= 'The table of URI is filled out.<br />';
	}


	// On envoie le csv si l'admin le demande
	if (getSettingValue("rss_acces_ele") == "csv") {
		// le code nécessaire à générer le csv "classe";"nom";"prenom";"login";"uri de la ressource"
		$msg .= 'the csv is available : <a href="../eleves/import_eleves_csv.php?rss=y">Download the csv</a>';
	}

}

// On teste si l'admin veut autoriser les flux pour créer la table adéquate
  $test_table = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'rss_users'"));

  if ($test_table == 0) {
	if (getSettingValue("rss_cdt_eleve") == "y") {
/*
			$lien_generateflux = '
			<a href="rss_cdt_admin.php?genereflux=y">
			Générer les adresses personnelles (<acronym title="identifiant de ressource sur un réseau">URI</acronym>) pour chaque élève
			</a>'."\n";
 *
 */
	  $lienFlux[$a]['lien']="rss_cdt_admin.php?genereflux=y";
	  $lienFlux[$a]['texte']="Generate the personal addresses (<acronym title='Uniform Resource Identifier : identifier of resource on a network'>URI</acronym>) for each student";
	  $lienFlux[$a]['confirme']=FALSE;
	  $a++;
	}else{
	  $lien_generateflux = '';
	}
  }elseif ($test_table == 1){
	$themessage = "Are you sure to want to generate these URI again ? All the students who already use a URI will have to adopt the news.";

/*
 * 		$lien_generateflux = '
		La table existe et les URI sont en place.&nbsp;&nbsp;
		<a href="'.$gepiPath.'/cahier_texte_admin/rss_cdt_admin.php?genereflux=y"'.insert_confirm_abandon().'>
		Re-Générer les <acronym title="identifiant de ressource sur un réseau">URI</acronym></a>'."\n";
 *
 */
	$lienFlux[$a]['lien']=$gepiPath."/cahier_texte_admin/rss_cdt_admin.php?genereflux=y";
	$lienFlux[$a]['texte']="Re-generate the<acronym title='Uniform Resource Identifier : identifier of resource on a network'>URI</acronym>";
	$lienFlux[$a]['confirme']=TRUE;
	$a++;
  }

// On vérifie les checked
// et on définit si on doit afficher le div qui suit ou pas
if (getSettingValue("rss_cdt_eleve") == "y") {
	$checked_ele = ' checked="checked"';
	$style_ele = ' style="Display: block;"';
}else{
	$checked_ele = '';
	$style_ele = ' style="Display: none;"';
}

if (getSettingValue("rss_acces_ele") == "direct") {
	$style_ele_dir = ' checked="checked"';
	$style_ele_csv = '';
}else{
	$style_ele_dir = '';
	$style_ele_csv = ' checked="checked"';
}


if ($msg=="" && ($action=="modifier"||$rss_acces_ele)) {
  $msg = "The modifications were recorded !";
  $post_reussi=TRUE;
}
// ======================== CSS et js particuliers ========================
$utilisation_win = "oui";
$utilisation_jsdivdrag = "non";
//$javascript_specifique = "";
//$style_specifique = "";

// ===================== entete Gepi ======================================//
// require_once("../lib/header.inc");
// ===================== fin entete =======================================//


/****************************************************************
                     HAUT DE PAGE
****************************************************************/

// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc");

/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

if (!suivi_ariane($_SERVER['PHP_SELF'],"paramétrage Flux RSS"))
		echo "error during the creation of the wire of ARIANE";


/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/cahier_texte_admin/rss_cdt_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche,$lienFlux);






/*
echo "<!-- page Parametrer_les_flux_rss_du_cahier_de_textes.-->";

?>
<p class="bold"><a href="../accueil_modules.php"><img src="../images/icons/back.png" alt="Retour" class="back_link" /> Retour</a></p>

<h3>Cet outil permet d'autoriser la g&eacute;n&eacute;ration de flux <acronym title="Appel&eacute; aussi syndication">rss</acronym> 2.0 des cahiers de textes de Gepi. </h3>

<p style="font-weight: bold; color: green;"><?php echo $lien_generateflux; ?>&nbsp;</p>

<form name="form_rss" action="rss_cdt_admin.php" method="post">
		<input type="hidden" name="action" value="modifier" />

	<p>
		<input type="checkbox" id="autoRssCdt" name="rss_cdt_ele" value="y" onclick="changementDisplay('accesEle', '');" onchange='document.getElementById("").submit();'<?php echo $checked_ele; ?> />
		<label for="autoRssCdt">&nbsp;Les &eacute;l&egrave;ves peuvent utiliser le flux rss de leur cahier de textes</label>
	</p>
</form>
<br />
	<div id="accesEle"<?php echo $style_ele; ?>>

<form name="form_rss_ele" action="rss_cdt_admin.php" method="post">
	<p>
		<input type="radio" id="rssAccesEle" name="rss_acces_ele" value="direct" onchange='document.form_rss_ele.submit();'<?php echo $style_ele_dir; ?> />
		<label for="rssAccesEle">Les &eacute;l&egrave;ves r&eacute;cup&egrave;rent l'adresse (url) d'abonnement directement par leur acc&egrave;s &agrave; Gepi</label>
	</p>
	<p>
		<input type="radio" id="rssAccesEle2" name="rss_acces_ele" value="csv" onchange='document.form_rss_ele.submit();'<?php echo $style_ele_csv; ?> />
		<label for="rssAccesEle2">L'admin r&eacute;cup&egrave;re un fichier csv de ces adresses (une par &eacute;l&egrave;ve)</label>
	</p>
</form>

	</div>




<?php
// Inclusion du bas de page
require_once("../lib/footer.inc.php");
 *
 */
?>
