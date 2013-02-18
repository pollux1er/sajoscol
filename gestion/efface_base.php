<?php
@set_time_limit(0);
/*
 * $Id: efface_base.php 5934 2010-11-21 13:33:58Z crob $
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

if (isset($_POST['confirm']) and ($_POST['confirm']=='Non')) {
    header("Location: index.php");
}

//**************** EN-TETE *****************
$titre_page = "Management tool | Suppression of the students data ";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?><p class=bold><a href='index.php#efface_base'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>
<H2>Erasing of the base</H2>
<?php
if (isset($_POST['is_posted']) and ($_POST['is_posted'] == 1)) {
    if ($_POST['confirm']=='Oui') {
		check_token(false);

		echo add_token_field();
        ?>
        <center><p class='grand'><font color='red'><b>CAUTION, the suppression of the data is irreversible !</b></font></p>
        <form action="efface_base.php" method="post" name="formulaire">
        <!--table border = 10 bordercolor='red'-->
        <!--table style='border:10px solid red'-->
        <table class='bordercolor10'>
        <tr><td><INPUT TYPE=SUBMIT value ="ERASE THE DATA <?php echo strtoupper($gepiSettings['denomination_eleves']);?>" /></td></tr></table>
        <INPUT TYPE=HIDDEN name=is_posted value = 2 />
        </FORM></center>
        <?php
    }
}

if (!isset($_POST['is_posted'])) {
    echo "<p><b>CAUTION : This procedure erases all the contents of the data base concerning ".$gepiSettings['denomination_eleves']." (personal data, notes, appreciations, ...)</b>
    <br />If you wish to initialize the year using GEP files , it is useless to use this procedure, the obliteration of the data will be proposed to you during the procedure of initialization.

    <br /><br />The following data are preserved:
    <ul>
    <li>The classes (names, periods, ...)</li>
    <li>Categories of AID</li>
    <li>The base school</li>
    <li>Logs of connection</li>
    <li>The base of the courses</li>
    <li>The base of the users</li>
    <li>The general parameter setting.</li>
    <li>Textbooks/li>
    </ul>";


	echo add_token_field();

    echo "<p><b>Are you sure you want to continue ?</b></p>";
    echo "<form action=\"efface_base.php\" method=\"post\" name=\"formulaire\">";
    echo "<INPUT TYPE=HIDDEN name=is_posted value = '1' /> ";
    echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Yes' />";
    echo "<INPUT TYPE=SUBMIT name='confirm' value = 'No' />";
    echo "</FORM>";

}

if (isset($_POST['is_posted']) and ($_POST['is_posted'] == 2)) {
	check_token(false);

   $liste_tables_del = array(
"absences",
//"absences_actions",
//"edt_creneaux",
"edt_creneaux_bis",
"absences_eleves",
"absences_gep",
//"absences_motifs",
"absences_rb",
"aid",
"aid_appreciations",
//"aid_config",
//"aid_familles",
//"aid_productions",
//"aid_public",
/*
"archivage_aid_eleve",
"archivage_aids",
"archivage_appreciations_aid",
"archivage_disciplines",
"archivage_ects",
"archivage_eleves",
"archivage_eleves2",
"archivage_types_aid",
*/
"avis_conseil_classe",
//"classes",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
"ct_devoirs_entry",
"ct_documents",
"ct_entry",
//"ct_types_documents",
//"droits",
//"droits_aid",
"edt_calendrier",
"edt_classes",
"edt_cours",
"edt_dates_special",
"edt_init",
//"edt_semaines",
//"edt_setting",
"eleves",
"eleves_groupes_settings",
//"etablissements",
//"etiquettes_formats",
"groupes",
//"horaires_etablissement",
"inscription_items",
"inscription_j_login_items",
"j_aid_eleves",
"j_aid_eleves_resp",
"j_aid_utilisateurs",
"j_aid_utilisateurs_gest",
"j_aidcateg_utilisateurs",
"j_aidcateg_super_gestionnaires",
"j_eleves_classes",
"j_eleves_cpe",
"j_eleves_etablissements",
"j_eleves_groupes",
"j_eleves_professeurs",
"j_eleves_regime",
"j_groupes_classes",
"j_groupes_matieres",
"j_groupes_professeurs",
"j_matieres_categories_classes",
//"j_professeurs_matieres",
"j_scol_classes",
//"lettres_cadres",
"lettres_suivis",
//"lettres_tcs",
//"lettres_types",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_appreciations_acces",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
"matieres_categories",
"matieres_notes",
"messages",
"miseajour",
//"model_bulletin",
//"periodes",
"preferences",
"resp_adr",
"resp_pers",
"responsables",
"responsables2",
"s_autres_sanctions",
"s_communication",
"s_exclusions",
"s_incidents",
"s_lieux_incidents",
"s_mesures",
"s_protagonistes",
"s_qualites",
"s_retenues",
"s_sanctions",
"s_traitement_incident",
"s_travail",
"s_types_sanctions",
"salle_cours",
//"setting",
"suivi_eleve_cpe",
"temp_gep_import",
"tempo",
"tempo2",
"tentatives_intrusion",
//"utilisateurs",
"vs_alerts_eleves",
"vs_alerts_groupes",
"vs_alerts_types"
);
   $j=0;
   while ($j < count($liste_tables_del)) {
       $del = mysql_query("DELETE FROM $liste_tables_del[$j]");
       $j++;
   }
   echo "<p class='grand'>Suppression of the data succeeded.</p>";
}
require("../lib/footer.inc.php");
?>