<?php

/**
 * Fichier d'initialisation de l'EdT par des fichiers CSV
 *
 * @version $Id: edt_init_csv.php 8315 2011-09-23 05:48:25Z crob $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Timetable - Initialization";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";
//==============PROTOTYPE===============
$utilisation_prototype = "ok";
//============fin PROTOTYPE=============
// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");

//+++++++++++++++++++GESTION DU RETOUR vers absences+++++++++++++++++
$_SESSION["retour"] = "edt_init_csv";
//+++++++++++++++++++FIN GESTION RETOUR vers absences++++++++++++++++

?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php

 // Initialisation des variables
$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
$truncate_cours = isset($_POST["truncate_cours"]) ? $_POST["truncate_cours"] : NULL;
$truncate_salles = isset($_POST["truncate_salles"]) ? $_POST["truncate_salles"] : NULL;
$aff_infos = isset($_POST["aff_infos"]) ? $_POST["aff_infos"] : NULL;

$aff_depart = ""; // pour ne plus afficher le html après une initialisation
$compter_echecs = 2; // pour afficher à la fin le message : Tous ces cours ont bien été enregistrés.

	// Initialisation de l'EdT (fichier g_edt.csv). Librement copié du fichier init_csv/eleves.php
        // On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
        // en proposant des champs de saisie pour modifier les données si on le souhaite
	if ($action == "upload_file") {
        // On vérifie le nom du fichier...
        if(strtolower($csv_file['name']) == "g_edt.csv") {

            // Le nom est ok. On ouvre le fichier
            $fp = fopen($csv_file['tmp_name'],"r");

            if(!$fp) {
                // Prob sur l'ouverture du fichier
                echo "<p>Impossible to open CSV file  !</p>";
                echo "<p><a href=\"./edt_init_csv.php\">click here </a> to restart !</center></p>";
            } //!$fp
            else {
            	// A partir de là, on vide la table edt_cours
            if ($truncate_cours == "oui") {
            	$vider_table = mysql_query("TRUNCATE TABLE edt_cours");
            }

            	// On ouvre alors toutes les lignes de tous les champs
            	$nbre = 1;
	while($tab = fgetcsv($fp, 1000, ";")) {
			// On met le commentaire dans les variables et on l'affiche que s'il y a un problème
				$message = "";
				$message1 = "";
				$message2 = "";
				$num = count($tab);
    			$message .= "<p> ".$num." fields for the line ".$nbre.": </p>\n";
    			$message2 .= "The line ".$nbre." : ";
    				$nbre++;
    			$message1 .= '<span class="legende">';
    					for ($c=0; $c < $num; $c++) {
        					$message1 .= $tab[$c] . " - \n";
     					}
    			$message1 .= '</span> ';
			$req_insert_csv = "";
    	// On considère qu'il n'y a aucun problème dans la ligne
    		$probleme = "";
    // Pour chaque entrée, on cherche l'id_groupe qui correspond à l'association prof-matière-classe
    	// On récupère le login du prof
    	$prof_login = strtoupper(strtr($tab[0], "éèêë", "eeee"));
    $req_prof = mysql_query("SELECT nom FROM utilisateurs WHERE login = '".$prof_login."'");
    $rep_prof = mysql_fetch_array($req_prof);
    	if ($rep_prof["nom"] == "") {
    		$probleme .="<p>The professor is not recognized.</p>\n";
    	}

		// On récupère l'id de la matière et l'id de la classe
		$matiere = strtoupper(strtr($tab[1], "éèêë", "eeee"));
		$sql_matiere = mysql_query("SELECT nom_complet FROM matieres WHERE matiere = '".$matiere."'");
		$rep_matiere = mysql_fetch_array($sql_matiere);
			if ($rep_matiere["nom_complet"] == "") {
				$probleme .= "<p>Gepi does not find the good course.</p>\n";
			}
		$classe = strtoupper(strtr($tab[2], "éèêë", "eeee"));
	$sql_classe = mysql_query("SELECT id FROM classes WHERE classe = '".$classe."'");
	$rep_classe = mysql_fetch_array($sql_classe);
		if ($rep_classe == "") {
			$probleme .= "<p>The class was not found.</p>\n";
		}

		// On récupère l'id de la salle
	$sql_salle = mysql_query("SELECT id_salle FROM salle_cours WHERE numero_salle = '".$tab[3]."'");
	$req_salle = mysql_fetch_array($sql_salle);
	$rep_salle = $req_salle["id_salle"];
		if ($rep_salle == "") {
			$probleme .= "<p>The room was not found.</p>\n";
		}

		// Le jour
	$rep_jour = $tab[4];

		// Le créneau de début du cours
	$creneau_csv = $tab[5];
	$verif_dec = explode("_", $creneau_csv);
		if ($verif_dec[0] == "d") {
			$rep_heuredeb_dec = '0.5';
			$verif_creneau = $verif_dec[1];
		} else {
			$rep_heuredeb_dec = '0';
			$verif_creneau = $verif_dec[0];
		}
	// On cherche l'id du créneau en question
	$req_creneau = mysql_query("SELECT id_definie_periode FROM edt_creneaux WHERE nom_definie_periode = '".$verif_creneau."'");
	$rep_creneau = mysql_fetch_array($req_creneau);
			if ($rep_creneau == "") {
				$probleme .= "<p>The crenel was not found.</p>\n";
			} else {
				$rep_heuredebut = $rep_creneau["id_definie_periode"];
			}

		// et la durée du cours et le type de semaine
		// Il faudrait vérifier si la durée est valide ainsi que le type de semaine
	$tab[6]=preg_replace('/,/','.',$tab[6]);
	$rep_duree = $tab[6] * 2;
	$rep_typesemaine = $tab[7];

		// le champ modif_edt = 0 pour toutes les entrées
		$rep_modifedt = '0';
		// Vérifier si ce cours dure toute l'année ou seulement durant une période
		if ($tab[8] == "0") {
			$rep_calendar = '0';
		}
		else {
			$req_calendar = mysql_query("SELECT id_calendrier FROM edt_calendrier WHERE nom_calendrier = '".$tab[8]."'");
			$req_tab_calendar = mysql_fetch_array($req_calendar);
				if ($req_tab_calendar == "") {
					$probleme .= "<p>The period of the calendar was not found.</p>\n";
				} else {
					$rep_calendar = $req_tab_calendar["id_calendrier"];
				}
		}

		// On retrouve l'id_groupe et on vérifie qu'il est unique
	$req_groupe = mysql_query("SELECT jgp.id_groupe FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgp.login = '".$prof_login."' AND jgc.id_classe = '".$rep_classe["id"]."' AND jgm.id_matiere = '".$matiere."' AND jgp.id_groupe = jgc.id_groupe AND jgp.id_groupe = jgm.id_groupe");
    		$rep_groupe = mysql_fetch_array($req_groupe);
    		if ($rep_groupe == "") {
				$probleme .= "<p>Gepi does not find the good course.</p>\n";
			} else {
    			if (count($req_groupe) > 1) {
    				echo "This combination returns several groups : ";
    				for ($a=0; $a<count($rep_groupe); $a++) {
						// Il faut trouver un truc pour que l'admin choisisse le bon groupe
						// Il faut donc afficher les infos sur les groupes en question
						// (liste d'élève, classe, matière en question) avec une infobulle.
						echo $rep_groupe[$a]." - ";
					}
    			}
    		} // fin du else

		// Si tout est ok, on rentre la ligne dans la table sinon, on affiche le problème
		$insert_csv = "INSERT INTO edt_cours
						(`id_groupe`, `id_salle`, `jour_semaine`, `id_definie_periode`, `duree`, `heuredeb_dec`, `id_semaine`, `id_calendrier`, `modif_edt`, `login_prof`)
						VALUES ('$rep_groupe[0]', '$rep_salle', '$rep_jour', '$rep_heuredebut', '$rep_duree', '$rep_heuredeb_dec', '$rep_typesemaine', '$rep_calendar', '0', '$prof_login')";
			// On vérifie que les items existent
		if ($rep_groupe[0] != "" AND $rep_jour != "" AND $rep_heuredebut != "" AND $probleme == "") {
			$req_insert_csv = mysql_query($insert_csv);
			// Si l'utilisateur décide de ne pas voir le suivi de ses entrées, on n'affiche rien
			if ($aff_infos == "oui") {
				echo "<br /><span class=\"accept\">".$message2."courses recorded</span>\n";
			} else {
				// on n'affiche rien
			}
		}
		else {
			echo "<br /><span class=\"refus\">This course is not recognized by Gepi :</span>\n".$message."(".$message1.")".$probleme.".";
			$compter_echecs = $compter_echecs++;
		}
    	//echo $rep_groupe[0]." salle n°".$tab[4]."(id n° ".$rep_salle["id_salle"]." ) le ".$rep_jour." dans le créneau dont l'id est ".$rep_heuredebut." et pour une durée de ".$rep_duree." demis-créneaux et le calend =".$rep_calendar.".";
	} // while
			} // else du début
		fclose($fp);

		// Si tous les cours ont été enregistrés, on affiche que tant de cours ont été enregistrés.
if ($aff_infos != "oui") {
	// On vérifie $compter_echec
	if ($compter_echecs == 2) {
		$aff_nbr = $nbre - 1;
		echo "<br /><p class=\"accept\">The ".$aff_nbr." courses were recorded.</p>";
	}
}

		// on n'affiche plus le reste de la page
		$aff_depart = "non";
		echo "<hr /><a href=\"./edt_init_csv.php\">Return to initialization by csv.</a>";
	} // if ... == "g_edt.csv")
	else
	echo 'It is not the good file name, retrogress by <a href="edt_init_csv.php">clicking here</a> !';
} // if ($action == "upload_file")


	// On s'occupe maintenant du fichier des salles
	if ($action == "upload_file_salle") {
        // On vérifie le nom du fichier...
        if(strtolower($csv_file['name']) == "g_salles.csv") {

            // Le nom est ok. On ouvre le fichier
            $fp = fopen($csv_file['tmp_name'],"r");

            // A partir de là, on vide la table salle_cours
            if ($truncate_salles == "oui") {
            	$vider_table = mysql_query("TRUNCATE TABLE salle_cours");
            }

            if(!$fp) {
                // Prob sur l'ouverture du fichier
                echo "<p>Impossible to open CSV file !</p>";
                echo "<p><a href=\"./edt_init_csv.php\">Click here </a> to restart !</center></p>";
            } // if (!$fp)...
            else {

            	// On affiche alors toutes les lignes de tous les champs
				while($tab_salle = fgetcsv($fp, 1000, ";")) {
					$numero = htmlentities($tab_salle[0]);
					$nom_brut_salle = htmlentities($tab_salle[1]);
				// On ne garde que les 30 premiers caractères du nom de la salle
				$nom_salle = substr($nom_brut_salle, 0, 30);
					if ($nom_salle == "") {
						$affnom_salle = 'Sans nom';
					} else {
						$affnom_salle = $nom_salle;
					}
				// On lance la requête pour insérer les nouvelles salles
				$req_insert_salle = mysql_query("INSERT INTO salle_cours (`numero_salle`, `nom_salle`) VALUES ('$numero', '$nom_salle')");
					if (!$req_insert_salle) {
						echo "The room : ".$nom_salle." having the number : ".$numero." was not recorded.<br />";
					} else {
						echo "The room  : ".$numero." is recorded (<i>".$affnom_salle."</i>).<br />";
					}
				} // while
			} // else
		fclose($fp);
			// on n'affiche plus le reste de la page
		$aff_depart = "non";
		echo "<hr /><a href=\"./edt_init_csv.php\">Return to initialization by csv.</a>";

		} //if(strtolower($csv_file['name']) =....
		else {
			echo '
			<h3>It is not the good file name !</h3>
			<p><a href="./edt_init_csv.php">Click here </a> to restart !</center></p>
				';
		}
	} // if ($action == "upload_file_salle")

	// On précise l'état du display du div aff_init_csv en fonction de $aff_depart
	if ($aff_depart == "oui") {
		$aff_div_csv = "block";
	} elseif ($aff_depart == "non") {
		$aff_div_csv = "none";
	} else {
		$aff_div_csv = "block";
	}

	// Pour la liste de <p> de l'aide id="aide_initcsv", on précise les contenus
		$forme_matiere = mysql_fetch_array(mysql_query("SELECT matiere, nom_complet FROM matieres"));
			$aff1_forme_matiere = $forme_matiere["matiere"];
			$aff2_forme_matiere = $forme_matiere["nom_complet"];
	$contenu_matiere = "Attention to respect strictly short name used in Gepi. It is of the form $aff1_forme_matiere for $aff2_forme_matiere.";
		$forme_classe = mysql_fetch_array(mysql_query("SELECT classe FROM classes WHERE id = '1'"));
		$aff_forme_classe = $forme_classe["classe"];
		// La liste des créneaux
				$aff_liste_creneaux = "";
		$sql_creneaux = mysql_query("SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause'");
		$nbre_creneaux = mysql_num_rows($sql_creneaux);
			for ($a=0; $a < $nbre_creneaux; $a++) {
				$liste_creneaux[$a] = mysql_result($sql_creneaux, $a, "nom_definie_periode");
				$aff_liste_creneaux .= $liste_creneaux[$a]." - ";
			}
		// Afficher les différents types de semaine : $aff_type_semaines
		$aff_type_semaines = "";
		$sql_semaines = mysql_query("SELECT DISTINCT type_edt_semaine FROM edt_semaines") or die ('Erreur dans la requête [Select distinct] : '.mysql_error());
		$nbre_types = mysql_num_rows($sql_semaines);
			for($b=0; $b < $nbre_types; $b++) {
				$liste_types[$b] = mysql_result($sql_semaines, $b, "type_edt_semaine");
				if ($nbre_types === 1) {
					$aff_type_semaines = "Only the type ".$liste_types[$b]." is defined";
				}
				$aff_type_semaines .= $liste_types[$b]." - ";
			}
		// Afficher le nom des différentes périodes du calendrier
		$aff_calendrier = "";
		$sql_calendar = mysql_query("SELECT nom_calendrier FROM edt_calendrier") or die ('Error in the request nom_calendrier :'.mysql_error());
		$nbre_calendar = mysql_num_rows($sql_calendar);
			if ($nbre_calendar === 0) {
				$aff_calendrier = "<span class=\"red\">You did not define periods of course.</span>";
			} else {
				for ($c=0; $c < $nbre_calendar; $c++) {
					$liste_calendar[$c] = mysql_result($sql_calendar, $c, "nom_calendrier");
					$aff_calendrier .= $liste_calendar[$c]." - ";
				}
			}

?>
<div id="aff_init_csv" style="display: <?php echo $aff_div_csv; ?>;">
Initialization starting from csv files proceeds in several stages:

<hr />
	<h4 class="refus">First stage</h4>
	<p>Part of initialization is common with the module absences : <a href="../mod_absences/admin/admin_periodes_absences.php?action=visualiser">differents crenels</a> of the day,
	 <a href="./admin_config_semaines.php?action=visualiser">the type of week</a> (pair/odd, A/B/C, 1/2,...) and
	 <a href="../mod_absences/admin/admin_horaire_ouverture.php?action=visualiser">schedules of the school</a>.</p>


<hr />
	<h4 class="refus">Second stage</h4>
	<p>It is necessary to inform the calendar while clicking Creation in the menu
 , Create the periods. All the periods which appear in the timetable must be defined : quarters, holidays, ... If all your courses take the time of the school year, you can skip this stage.</p>
<hr />
	<h4 class="refus">Third stage</h4>
	<p>Caution, this initialization erases all the data concerning the rooms already
present except if you deselect the button.
	For the rooms of your school, you must provide a csv file . You will be able to then add, remove or modify their name in the menu Management of the rooms.</p>
	<p>The following fields must be present, in the order, <b>separated by a semicolon and framed by quotation marks ""</b> (without line of heading) :</p>
	<ol>
		<li>number room (5 characters max.)</li>
		<li>name room (30 characters max.)</li>
	</ol>
	<p>Please specify the complete name of the file <b>g_salles.csv</b>.</p>
	<form enctype="multipart/form-data" action="edt_init_csv.php" method="post">
		<input type="hidden" name="action" value="upload_file_salle" />
		<input type="hidden" name="initialiser" value="ok" />
		<input type="hidden" name="csv" value="ok" />
		<p><label for="truncateSalles">Erase the rooms already created </label>
		<input type="checkbox" id="truncateSalles" name="truncate_salles" value="oui" checked="checked" /></p>
		<p><input type="file" size="80" name="csv_file" /></p>
		<p><input type="submit" value="Valider" /></p>
	</form>

<hr />
	<h4 class="refus">Fourth stage</h4>
	<p>Caution, this initialization erases all the data concerning courses already present except if you deselect the button.</p>
	<p><span class="red">Caution</span> to strictly respect the hours, day, name of course,... of Gepi which you specified before.
	For the timetable, you must provide a csv file of which following fields
	 must be present, in the order, <b>separated by a semicolon and framed by quotation marks ""</b> (without line of heading) :</p>
<!-- AIDE init csv -->

<a href="#ancre1" onclick="javascript:changerDisplayDiv('aide_initcsv');" name="ancre1">
	<img src="../images/info.png" alt="Plus d'infos..." Title="Cliquez pour plus d'infos..." />
</a>
	<div style="display: none;" id="aide_initcsv">
	<hr />
	<span class="red">Caution</span>, these fields have rules to follow : it is necessary to respect the form retained by Gepi
	<br />
	<p>"login_prof";"course";"class";numéro_salle;"day";"nom_créneau";duration;"type_semaine";
	"nom_periode_cours"</p>
	<p>For the login of the professors, you can recover them by this <a href="<?php echo $gepiPath; ?>/utilisateurs/import_prof_csv.php">LINK</a>.</p>
	<p>For the course, it is necessary to use short name which is the form <?php echo "\"".$aff1_forme_matiere."\" pour ".$aff2_forme_matiere; ?>.</p>
	<p>For the class, short name is the form "<?php echo $aff_forme_classe; ?>".</p>
	<p>The number of the room and the day must correspond to existing information already in Gepi.</p>
	<p>For the name of the crenel : <?php echo $aff_liste_creneaux; ?>If a course starts in the medium of a course,
	 it is necessary to precede the name by the crenel by the prefix d_ (ex : d_M1 pour M1).</p>
	<p>The duration is expressed in a number of occupied crenels. For the courses which last a crenel and half,
	the form "1.5" should be used  -</p>
	<p>The type of week is equal to "0" for the proceeding courses every week. For the other courses,
	specify between these types : <?php echo $aff_type_semaines; ?>.</p>
	<p>For the courses which do not take place all the year, specify the name of the period of course.
	(<?php echo $aff_calendrier; ?>)<br /> For the other courses, this field must be equal to "0".</p>
	<hr />
	</div>
<!-- Fin aide init csv -->
	<ol>
	 	<li>login professor</li>
		<li>course</li>
		<li>class</li>
		<li>number of the room</li>
		<li>day</li>
		<li>name of the crenel</li>
		<li>duration of the course</li>
		<li>type of week</li>
		<li>Name of the period of course</li>
	</ol>

	<p>Please specify the complete name of the file <b>g_edt.csv</b>.</p>
		<form enctype="multipart/form-data" action="edt_init_csv.php" method="post">
			<input type="hidden" name="action" value="upload_file" />
			<input type="hidden" name="initialiser" value="ok" />
			<input type="hidden" name="csv" value="ok" />
			<p><label for="truncateCours">Erase the courses already created </label>
			<input type="checkbox" id="truncateCours" name="truncate_cours" value="oui" checked="checked" />
			<label for="affInfosEdt">Display the recording of all the courses</label>
			<input type="checkbox" id="affInfosEdT" name="aff_infos" value="oui" checked="checked" /></p>
			<p><input type="file" size="80" name="csv_file" /></p>
			<p><input type="submit" value="Valider" /></p>
		</form>
</div><!-- fin du div aff_init_csv -->
	</div><!-- fin du div lecorps -->

<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>