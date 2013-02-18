<?php
/*
 * $Id: index.php 2554 2008-10-12 14:49:29Z crob $
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

// SQL : INSERT INTO droits VALUES ( '/mod_ooo/gerer_modeles_ooo.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Index', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/gerer_modeles_ooo.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Index', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}


include_once('./lib/lib_mod_ooo.php'); //les fonctions
$nom_fichier_modele_ooo =''; //variable à initialiser à blanc pour inclure le fichier suivant et éviter une notice. Pour les autres inclusions, cela est inutile.
include_once('./lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

//Liste des fichiers à compléter à la main (3 données par fichier)
    // L'entête de la section pour le 1er fichier de la section sinon "" (vide)
    //Le nom du fichier en minuscule avec son extension
	//La description du document
	
    //Retenue
    $entete_section[]="MODULATE DISCIPLINE";
	$fich[]="retenue.odt";
    $utilisation[]="Form of reserve";	
    //rapport incident
    $entete_section[]="";
	$fich[]="rapport_incident.odt";
    $utilisation[]="Form of incident report";
	//Exclusion temporaire
    $entete_section[]="";
	$fich[]="discipline_exclusion.odt";
    $utilisation[]="Temporary exclusion of the establishment";

    //modèle ABS2
	$entete_section[]="MODULE ABSENCE";
    $fich[]="absence_extraction_demi-journees.ods";
    $utilisation[]="ABS2: Table of the half-days of absences";
	
	$entete_section[]="";
    $fich[]="absence_extraction_saisies.ods";
    $utilisation[]="ABS2: Table of the seizures of absences";
	
	$entete_section[]="";
    $fich[]="absence_extraction_traitements.ods";
    $utilisation[]="ABS2 : Table of the treatments of absences";
    
    $entete_section[]="";
    $fich[]="absence_taux_absenteisme.ods";
    $utilisation[]="ABS2 : Table of the absentee rates";

    $entete_section[]="";
    $fich[]="absence_extraction_bilan.ods";
    $utilisation[]="ABS2 : Table assessment per day by pupil with the format spreadsheet";

    $entete_section[]="";
    $fich[]="absence_extraction_bilan.odt";
    $utilisation[]="ABS2 : Table assessment per day by pupil with the format text processing";

	$entete_section[]="";
    $fich[]="absence_modele_lettre_parents.odt";
    $utilisation[]="ABS2 : Model of letter to the parents";
	
	$entete_section[]="";
    $fich[]="absence_modele_impression_par_lot.odt";
    $utilisation[]="ABS2 : Model of impression per batch";

	$entete_section[]="";
    $fich[]="absence_email.txt";
    $utilisation[]="ABS2 : Model of the courriel sent to the parents";
	
	$entete_section[]="";
    $fich[]="absence_sms.txt";
    $utilisation[]="ABS2 : Model of SMS sent to the parents";


    //Fiches brevet
	$entete_section[]="MODULATE NOTANET";
    $fich[]="fb_CLG_lv2.ods";
    $utilisation[]="Card-index patent series college LV2";
	
	$entete_section[]="";
    $fich[]="fb_CLG_dp6.ods";
    $utilisation[]="Card-index patent series college ODP 6 hours";
	
	$entete_section[]="";
    $fich[]="fb_PRO.ods";
    $utilisation[]="Card-index patent professional series without ODP";
	
	$entete_section[]="";
    $fich[]="fb_PRO_dp6.ods";
    $utilisation[]="Card-index patent professional series ODP 6 hours";
	
	$entete_section[]="";
    $fich[]="fb_PRO_agri.ods";
    $utilisation[]="Card-index patent professional series agricultural option";
	
	$entete_section[]="";
    $fich[]="fb_TECHNO.ods";
    $utilisation[]="Card-index technological patent series without ODP";
	
	$entete_section[]="";
    $fich[]="fb_TECHNO_dp6.ods";
    $utilisation[]="Card-index technological patent series ODP 6 hours";
	
	$entete_section[]="";
    $fich[]="fb_TECHNO_agri.ods";
    $utilisation[]="Card-index technological patent series agricultural option";

    //rapport incident
	$entete_section[]="MODULE ECTS";
    $fich[]="documents_ects.odt";
    $utilisation[]="Documents ECTS (for BTS, prépas...)";
	
	
    $nbfich=sizeof($fich);
// Fin liste des fichiers

$PHP_SELF=basename($_SERVER['PHP_SELF']);
creertousrep($nom_dossier_modeles_ooo_mes_modeles.$rne);

$retour_apres_upload=isset($_POST['retour_apres_upload']) ? $_POST['retour_apres_upload'] : (isset($_GET['retour_apres_upload']) ? $_GET['retour_apres_upload'] : NULL);
if(!isset($retour_apres_upload)) {
	$retour=$_SESSION['retour'];
	$_SESSION['retour']=$_SERVER['PHP_SELF'] ;
}
else {
	$retour="../accueil.php";
}

//**************** EN-TETE *****************
$titre_page = "Open Model Office - Manage its models";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<script type='text/javascript' src=\"./lib/mod_ooo.js\"> </script>";
//debug_var();

if (isset($_GET['op'])) { $op=$_GET["op"]; }
if (isset($_GET['fic'])) { $fic=$_GET["fic"]; }
if (isset($_POST['btn'])) { $btn=$_POST["btn"]; }
if (isset($_POST['fich_cible'])) { $fich_cible=$_POST["fich_cible"]; }

echo "<p class='bold'><a href='".$retour;
if(isset($btn)) {echo "?retour_apres_upload=y";}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>";
echo "</p>\n";
echo "<br />\n";
echo "<p>This module is intended to manage the models Open Office of Gepi.</p>\n";
echo "</p>\n";
echo "<br />\n";

if ((isset($op)) && ($op=="supp")) { //Supprimer un fichier perso
     // alert("EFFACER $fic");
	  @unlink($nom_dossier_modeles_ooo_mes_modeles.$rne.$fic);
}

echo "<body>";


if (!isset($btn)) { //premier passage : formulaire
    echo "<p >A personalized model, sent on the waiter will be used by Gepi</p><hr>\n";
    echo "<p >It does not matter the current name (keep the format Open Office: ODT
- text, ODS - spreadsheet or txt - text), each file will be famous correctly.<br />\n";
    echo "The personalized files can be removed (icon dustbin), contrary to
those by defect.<br />\n";
	echo "The whole of the files can be consulted while clicking on their icon.</p><br />\n";
	echo "When you create a new model, to pay well attention to the syntax of
the variables used in the model by defect.</p><br />\n";
    echo "They are sensitive to the box. The format of a variable is [ var.xxxxx
]</p><br /><br />\n";
    echo "<p><u>Particular case of the model of letter to the parents for the module
absence 2 : </u><br />\n";
    echo "A too significant modification of this model can entrainer of the
disfonctionnements or problems of page-setting with the functionality
of impression per batch of the mails. </u><br />\n";
    echo "This is why it is recommended, in this case there, to limit itself has
modifications simple (nature of the text for example) of the basic
model suggested in Gépi.</p><br /><br />\n";
    //Tableau des différents fichiers à envoyer
    echo "<table class='boireaus' align='center'>\n";
    echo "<tr>\n";
    echo "<td>Initial model<br/>Visualiser</td>\n";
    echo "<td>Personal model <br/>Supprimer / Visualiser</td>\n";
    echo "<td>Name of the file</td>\n";
    echo "<td>Description of the file</td>\n";
    echo "<td>Choose the file <br/>to download</td>\n";
    echo "<td>Action</td>\n";
    echo "</tr>\n";
	$alt=1;
    for ($i=0;$i<$nbfich;$i++) {
	  $alt=$alt*(-1);
      //Une ligne du tableau
      //paire ou impaire	  
	  if ($entete_section[$i] != "") { // Cas d'un entête
	      echo "<tr>";
	      echo "<td colspan=\"6\"></br></br><b>$entete_section[$i]</br></br></b></br></br></td>";
		  echo "</tr>";
	  }
	  echo "<tr class='lig$alt'><form name=\"form$i\" method='post' ENCTYPE='multipart/form-data' action='$PHP_SELF' onsubmit=\"return bonfich('$i')\" >\n";
	echo add_token_field();
	  echo "<input type=\"hidden\" name='fich_cible' value='$fich[$i]' / >\n";
		 $type_ext = renvoi_nom_image(extension_nom_fichier($fich[$i]));
		 echo "<td align='center'><a href=\"$nom_dossier_modeles_ooo_par_defaut$fich[$i]\"><img src=\"./images/$type_ext\" border=\"0\" title=\"Consult the model by defect\"></a>\n";
		 echo "</td>\n";
	  if  (file_exists($nom_dossier_modeles_ooo_mes_modeles.$rne.$fich[$i]))   {
		 echo "<td align='center'><a href=\"$PHP_SELF?op=supp&fic=$fich[$i]".add_token_in_url()."\" onclick='return confirmer()'><img src=\"./images/poubelle.gif\" border=\"0\" title=\"CAUTION, immediate deleted !\"></a>\n";
		 echo "&nbsp;&nbsp;<a href='".$nom_dossier_modeles_ooo_mes_modeles.$rne.$fich[$i]."'><img src=\"./images/$type_ext\" border=\"0\" title=\"Consult the new model\"></a>\n";
		 echo "</td>\n";
	  } else {
		 echo "</td>\n<td>&nbsp;</td>\n";
	  }

	  echo "<td>$fich[$i]</td><td>\n";
	  echo "$utilisation[$i]</td><td>\n";
	  echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"512000\" />";
	  echo "<input type='file' name='monfichier' value='it clicked the guy' />&nbsp;</td><td>\n";
	  echo "&nbsp;&nbsp;<input type='submit' name='btn' Align='middle' value='Send' />&nbsp;&nbsp;  \n";
	  echo "</td></form>\n";
	  echo "</tr>\n";
    }
    echo "</table>\n";

}
else { // passage 2 : le nom du fichier a été choisi
    //print_r($_FILES['monfichier']);
	echo "<h2>file sent : ".$_FILES['monfichier']['name']." </h2>\n";
	check_token();
    $desterreur=$PHP_SELF;
    $dest=$desterreur;
    //alert($dest);

    //Récup du fichier téléchargé
    $t=$_FILES['monfichier'];
    //print_r($t);

    $monfichiername=$t['name'];
    $monfichiertype=$t['type'];
    $monfichiersize=$t['size'];
    $monfichiertmp_name=$t['tmp_name'];

    if ($monfichiername=="") {
       alert ("No the file indicated ! It is necessary to start again...");
       $dest=$desterreur;
       echo "<script type='text/javascript'>\n";
       echo "<!-- \n";
       echo "w=window.open('','mafenetre');\n"; //récupérer le même objet fenêtre
       echo "w.document.writeln('<h3>Closing in progress...</h3>');\n";
       echo "// - JavaScript - -->\n";
       echo "</script>\n";
       aller_a($dest);
    }
    else {
        echo "<script type='text/javascript'>\n";
        echo "<!-- \n";
        echo "w=window.open('','mafenetre');\n"; //récupérer le même objet fenêtre
        echo "w.document.writeln('<h3>copy in progress...</h3>');\n";
        echo "// - JavaScript - -->\n";
        echo "</script>\n";


        $fichiercopie=strtolower($monfichiername);
        //alert("fichier copié : ".$fichiercopie);

        $cible=$nom_dossier_modeles_ooo_mes_modeles.$rne.$fich_cible;
        //alert("avant la copie".$cible);
        if (!move_uploaded_file($monfichiertmp_name,$cible)) {
            echo "Error of copy<br />\n";
            echo "origin     : $monfichiername <br />\n";
            echo "destination : ".$nom_dossier_modeles_ooo_mes_modeles.$rne.$fichiercopie;
            $me="The copy was not carried out !\n Check the size of the file (max 512ko)\n";
            alert($me);
            $dest=$desterreur;
        }
        else {
            //echo "<p>$cible a été copié</p>";
            $dest.="?fichier=$cible";
            echo($fich_cible." was copied correctly :<br />");
            echo "<p align='center'>";
            unset($monfichiername);
            echo "<form name='retour' method='POST' action='$PHP_SELF'>\n";
            echo "<input type='hidden' name='retour_apres_upload' value='y' />\n";
            echo "<input type='submit' name='ret' Align='middle' value='Return' />\n";
            echo "</form>\n";

            }
        } //fin de monfichier != ""
        echo "<script type='text/javascript'>\n";
        echo "<!-- JavaScript\n";
        echo "w.close()\n";
        echo "// - JavaScript - -->\n";
        echo "</script>\n";

}
?>
</body>
</html>
