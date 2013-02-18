<?php
/*
 *  $Id: modif_fiches.php 6588 2011-03-02 17:53:54Z crob $
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
$nb_max_cases = 3;
$non_defini = "<font color='red'>Non d�fini</font>";
require_once("../lib/initialisations.inc.php");

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
$indice_aid = isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : (isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : NULL);
$aid_id = isset($_POST["aid_id"]) ? $_POST["aid_id"] : (isset($_GET["aid_id"]) ? $_GET["aid_id"] : NULL);
$annee = isset($_POST["annee"]) ? $_POST["annee"] : (isset($_GET["annee"]) ? $_GET["annee"] : '');

// V�rification de la validit� de $indice_aid et $aid_id
if (!VerifAidIsAcive($indice_aid,$aid_id,$annee)) {
    echo "<p>You try to reach tools which are not activated. Contact the administrator.</p></body></html>";
    die();
}
// Gestion du lien retour
if (isset($_GET["retour"])) {
    if ($_GET["retour"]=='') $_SESSION['retour']='';
    else
    $_SESSION['retour']= $_GET["retour"]."?indice_aid=".$indice_aid;
    if ($_GET["retour"]=="annees_anterieures_accueil.php") $_SESSION['retour'] .= "&amp;annee_scolaire=".$annee;
    if ($_GET["retour"]=="index_fiches.php") $_SESSION['retour'] .= "&amp;action=liste_projet";
}
// Par d�faut, on revient � index_fiches.php.
if (!isset($_SESSION['retour'])) $_SESSION['retour'] = "index_fiches.php?indice_aid=".$indice_aid."&amp;action=liste_projet";
 $action = isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : "visu");
if ($annee=='') {
    $nom_projet = sql_query1("select nom from aid_config where indice_aid='".$indice_aid."'");
}
else {
    $nom_projet = sql_query1("select nom from  archivage_types_aid where id='".$indice_aid."' and annee='".$annee."'");
}
$call_productions = mysql_query("select * from aid_productions");
$nb_productions = mysql_num_rows($call_productions);
$call_public = mysql_query("select * from aid_public order by ordre_affichage");
$nb_public = mysql_num_rows($call_public );

// Si l'utilisateur n'est pas autoris� � modifier la fiche, on force $action = "visu"
if (!VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'','',$annee)) $action = "visu";

// Enregistrement des donn�es
if (isset($_POST["is_posted"])) {
	check_token();

    // La personne connect�e a-t-telle le droit d'enregistrer ?
    if (!VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'','',$annee))
        die();
    $msg = "";
    if ($annee=='')
        $sql_aid = "update aid set ";
    else
        $sql_aid = "update archivage_aids set ";

    $met_virgule='n';
    // R�sum�
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'resume','W',$annee)) and (isset($_POST["reg_resume"]))) {
      $reg_resume = isset($_POST["reg_resume"]) ? $_POST["reg_resume"] : NULL;
      if (strlen($reg_resume) > 600) {
        $reg_resume = substr($reg_resume,0,597)."...";
        $msg .= "Erreur : Votre r�sum� exc�de 600 caract�res.<br />";
      }
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "resume = '".$reg_resume."'";
      $met_virgule='y';
    }
    // Divers
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'divers','W',$annee)) and (isset($_POST["reg_divers"]))) {
      $reg_divers = isset($_POST["reg_divers"]) ? $_POST["reg_divers"] : NULL;
      if (strlen($reg_divers) > 600) {
        if ($msg != "") $msg .= "<br />";
        $msg .= "Error: the various fields exceeds 600 characters.<br />";
        $reg_divers = substr($reg_divers,0,597)."...";
      }
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "divers = '".$reg_divers."'";
      $met_virgule='y';
    }
    // Contacts
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'contacts','W',$annee)) and (isset($_POST["reg_contacts"]))) {
      $reg_contacts = isset($_POST["reg_contacts"]) ? $_POST["reg_contacts"] : NULL;
      if (strlen($reg_contacts) > 600) {
        if ($msg != "") $msg .= "<br />";
        $msg .= "Error: the fields \"External contacts \" exceeds 600 characters.<br />";
        $reg_contacts = substr($reg_contacts,0,597)."...";
      }
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "contacts = '".$reg_contacts."'";
      $met_virgule='y';

    }
    // productions
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'productions','W',$annee)) {
      $reg_productions = "";
      $i = 0;
      $nb_cases = 0;
      while ($i < $nb_productions) {
        $temp = "p".$i;
        if (isset($_POST[$temp])) {
            $nb_cases++;
            if ($nb_cases <= $nb_max_cases)
                $reg_productions .= $_POST[$temp]."|";
        }
        $i++;
      }
      if ($nb_cases > $nb_max_cases) {
        if ($msg != "") $msg .= "<br />";
        $msg .= "Error: you must check to the maximum ".$nb_max_cases." items to characterize your project.";
      }
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "productions = '".$reg_productions."'";
      $met_virgule='y';
    }
    // public
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'public_destinataire','W',$annee)) {
      $reg_public = "";
      $i = 0;
      while ($i < $nb_public) {
        $temp = "public".$i;
        if (isset($_POST[$temp])) {
            $reg_public .= $_POST[$temp]."|";
        }
        $i++;
      }
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "public_destinataire = '".$reg_public."'";
      $met_virgule='y';
    }
    //Mots cl�s :
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'mots_cles','W',$annee)) {
      $reg_mots_cles = "";
      $k = 0;
      while ($k < 5) {
        $temp = "mc".$k;
        if ((isset($_POST[$temp])) and (trim($_POST[$temp]) != ""))
            $reg_mots_cles .= $_POST[$temp]."|";
        $k++;
      }
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "mots_cles = '".$reg_mots_cles."'";
      $met_virgule='y';
    }

    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse1','W',$annee)) and (isset($_POST["reg_adresse1"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "adresse1 = '".$_POST["reg_adresse1"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse2','W',$annee)) and (isset($_POST["reg_adresse2"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "adresse2 = '".$_POST["reg_adresse2"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'famille','W',$annee)) and (isset($_POST["reg_famille"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "famille = '".$_POST["reg_famille"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere1','W',$annee)) and (isset($_POST["reg_discipline1"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "matiere1 = '".$_POST["reg_discipline1"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere2','W',$annee)) and (isset($_POST["reg_discipline2"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "matiere2 = '".$_POST["reg_discipline2"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso1','W',$annee)) and (isset($_POST["reg_perso1"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "perso1 = '".$_POST["reg_perso1"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso2','W',$annee)) and (isset($_POST["reg_perso2"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "perso2 = '".$_POST["reg_perso2"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso3','W',$annee)) and (isset($_POST["reg_perso3"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "perso3 = '".$_POST["reg_perso3"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'numero','W',$annee)) and (isset($_POST["reg_num"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "numero = '".$_POST["reg_num"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'nom','W',$annee)) and (isset($_POST["reg_nom"]))) {
      if ($annee=='') {
      //  On regarde si une aid porte d�j� le m�me nom
        $test = sql_query1("SELECT count(id) FROM aid WHERE (nom='".$_POST["reg_nom"]."' and indice_aid='".$indice_aid."' and id!='".$aid_id."')");
        if ($test == "1") {
            if ($msg != "") $msg .= "<br />";
            $msg .= " Attention, a IDA having the same name already exist !";
        } else if ($test > 1) {
            if ($msg != "") $msg .= "<br />";
            $msg .= " Attention, several AID having the same name already exist !";
        }
      }
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "nom = '".$_POST["reg_nom"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'eleve_peut_modifier','W',$annee)) and ($annee=='')) {
        if (isset($_POST["reg_eleve_peut_modifier"]))
            $reg_eleve_peut_modifier = "y";
        else
            $reg_eleve_peut_modifier = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "eleve_peut_modifier = '".$reg_eleve_peut_modifier."'";
        $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'prof_peut_modifier','W',$annee)) and ($annee=='')) {
        if (isset($_POST["reg_prof_peut_modifier"]))
            $reg_prof_peut_modifier = "y";
        else
            $reg_prof_peut_modifier = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "prof_peut_modifier = '".$reg_prof_peut_modifier."'";
        $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'cpe_peut_modifier','W',$annee))  and ($annee=='')) {
        if (isset($_POST["reg_cpe_peut_modifier"]))
            $reg_cpe_peut_modifier = "y";
        else
            $reg_cpe_peut_modifier = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "cpe_peut_modifier = '".$reg_cpe_peut_modifier."'";
        $met_virgule='y';
    }
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'fiche_publique','W',$annee)) {
        if (isset($_POST["reg_fiche_publique"]))
            $reg_fiche_publique = "y";
        else
            $reg_fiche_publique = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "fiche_publique = '".$reg_fiche_publique."'";
        $met_virgule='y';
    }
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'affiche_adresse1','W',$annee)) {
        if (isset($_POST["reg_affiche_adresse1"]))
            $reg_affiche_adresse1 = "y";
        else
            $reg_affiche_adresse1 = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "affiche_adresse1 = '".$reg_affiche_adresse1."'";
        $met_virgule='y';
    }
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'en_construction','W',$annee)) {
        if (isset($_POST["reg_en_construction"]))
            $reg_en_construction = "y";
        else
            $reg_en_construction = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "en_construction = '".$reg_en_construction."'";
        $met_virgule='y';
    }
    // Fin de la requ�te
    if ($annee=='')
        $sql_aid .= " where (id = '".$aid_id."' and indice_aid='".$indice_aid."')";
    else
        $sql_aid .= " where (id = '".$aid_id."' and id_type_aid='".$indice_aid."'  and annee='".$annee."')";

    $enr_aid = sql_query($sql_aid);
    if ($msg != "") $msg .= "<br />";
    if (!$enr_aid)
        $msg .= "Error during the recording !";
    else
        $msg .= "Successful recording !";


}
// Appel de toutes les infos sur le projet
if ($annee=='') {
  $call_data_projet = mysql_query("select * from aid where (id = '$aid_id' and indice_aid='$indice_aid')");
}
else {
  $call_data_projet = mysql_query("select * from archivage_aids where (id = '$aid_id' and id_type_aid='$indice_aid' and annee='".$annee."')");
}

$aid_nom = mysql_result($call_data_projet, 0, "nom");
$reg_resume = @mysql_result($call_data_projet,0,"resume");
$reg_contacts = @mysql_result($call_data_projet,0,"contacts");
$reg_divers = @mysql_result($call_data_projet,0,"divers");
$reg_famille = @mysql_result($call_data_projet,0,"famille");
$reg_discipline1 = @mysql_result($call_data_projet,0,"matiere1");
$reg_discipline2 = @mysql_result($call_data_projet,0,"matiere2");
$reg_adresse1 = @mysql_result($call_data_projet,0,"adresse1");
$reg_productions = @mysql_result($call_data_projet,0,"productions");
$reg_public = @mysql_result($call_data_projet,0,"public_destinataire");
$reg_mots_cles = @mysql_result($call_data_projet,0,"mots_cles");
$reg_adresse2 = @mysql_result($call_data_projet,0,"adresse2");
$reg_num = @mysql_result($call_data_projet,0,"numero");
$reg_fiche_publique = @mysql_result($call_data_projet,0,"fiche_publique");
$reg_affiche_adresse1 = @mysql_result($call_data_projet,0,"affiche_adresse1");
$reg_en_construction = @mysql_result($call_data_projet,0,"en_construction");
if ($annee=='') {
  $reg_perso1 = @mysql_result($call_data_projet,0,"perso1");
  $reg_perso2 = @mysql_result($call_data_projet,0,"perso2");
  $reg_perso3 = @mysql_result($call_data_projet,0,"perso3");
  $reg_eleve_peut_modifier = @mysql_result($call_data_projet,0,"eleve_peut_modifier");
  $reg_prof_peut_modifier = @mysql_result($call_data_projet,0,"prof_peut_modifier");
  $reg_cpe_peut_modifier = @mysql_result($call_data_projet,0,"cpe_peut_modifier");
} else {
  $eleves_resp = @mysql_result($call_data_projet,0,"eleves_resp");
  $eleves = @mysql_result($call_data_projet,0,"eleves");
  $responsables = @mysql_result($call_data_projet,0,"responsables");
}

$style_specifique = "aid/style_fiche";

//**************** EN-TETE *********************
if ($action == "visu")
    $titre_page = "Visualisation d'une fiche projet ".$nom_projet;
else
    $titre_page = "Modification d'une fiche projet ".$nom_projet;
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if ($action != "visu") {
?>
<script type="text/javascript">
//*** Param�tres
//*** texte : objet repr�sentant le textarea
//*** max : nombre de caract�res maximum
function CaracMax(texte, max)
{
if (texte.value.length >= max)
{
alert('Pas plus de ' + max + ' caract�re(s) !!!') ;
texte.value = texte.value.substr(0, max - 1) ;
}
}

function compteur_coches(obj) {
max = <?php echo $nb_max_cases; ?>;
nombre = 0;
<?php
$k = 0;
while ($k < $nb_productions) {
    echo "if (document.forms[\"form\"].elements['p".$k."'].checked) nombre++;";
    $k++;
}
?>
if (nombre > max) {
alert("You cannot check more than" + max + " boxes !");
obj.checked = false;
}
}

</script>
<?php
}

echo "<p class=bold>";
if ($_SESSION['retour']!='')
echo "<a href=\"".$_SESSION['retour']."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if ($action == "visu") {
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,"","",$annee)) {
        echo " | <a href='modif_fiches.php?aid_id=$aid_id&amp;indice_aid=$indice_aid&amp;annee=$annee&amp;action=modif'>Modify the card</a>";
    }
} else {
    echo " | <a href='modif_fiches.php?aid_id=$aid_id&amp;indice_aid=$indice_aid&amp;annee=$annee&amp;action=visu'>Visualize the card</a>";
}
echo "</p>\n";
// Nom du projet
echo "<p class='grand'>project ".$nom_projet." : ".$aid_nom."</p>\n";

// D�but du formulaire
If ($action != "visu") {
    echo "<form action=\"modif_fiches.php\" name=\"form\" method=\"post\">\n";
	echo add_token_field();
}

echo "<div class='bloc'>";

// El�ves responsables
echo "<span class = 'bold'>Students responsible for the project :</span>\n";
if ($annee=='') {
  // appel de la liste des �l�ves de l'AID :
  $call_liste_data = mysql_query("SELECT e.login, e.nom, e.prenom
  FROM eleves e, j_aid_eleves_resp j
  WHERE (j.id_aid='$aid_id' and e.login=j.login and j.indice_aid='$indice_aid')
  ORDER BY nom, prenom");
  $nombre = mysql_num_rows($call_liste_data);
  $i = "0";
  while ($i < $nombre) {
    $login_eleve = mysql_result($call_liste_data, $i, "login");
    $nom_eleve = mysql_result($call_liste_data, $i, "nom");
    $prenom_eleve = @mysql_result($call_liste_data, $i, "prenom");
    $call_classe = mysql_query("SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$login_eleve' and j.id_classe = c.id) order by j.periode DESC");
    $classe_eleve = @mysql_result($call_classe, '0', "classe");
    echo "$nom_eleve $prenom_eleve ($classe_eleve)";
    if ($i < $nombre-1) echo " - ";
    $i++;
 }
} else {
   echo $eleves_resp;
}

// Professeurs responsables
echo "\n<br /><span class = 'bold'>Professors responsible for the project : </span>\n";
if ($annee=='') {
  // appel de la liste des professeurs de l'AID :
  $call_liste_data = mysql_query("SELECT e.login, e.nom, e.prenom
  FROM utilisateurs e, j_aid_utilisateurs j
  WHERE (j.id_aid='$aid_id' and e.login=j.id_utilisateur and j.indice_aid='$indice_aid')
  ORDER BY nom, prenom");
  $nombre = mysql_num_rows($call_liste_data);
  $i = "0";
  while ($i < $nombre) {
    $login_eleve = mysql_result($call_liste_data, $i, "login");
    $nom_eleve = mysql_result($call_liste_data, $i, "nom");
    $prenom_eleve = @mysql_result($call_liste_data, $i, "prenom");
    echo $nom_eleve." ".$prenom_eleve;
    if ($i < $nombre-1) echo " - ";
  $i++;
  }
} else {
  echo $responsables;
}

if ($annee=='') {
 // perso1
 If ($action != "visu") {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso1','W',$annee)) {
    echo "<br /><span class = 'bold'>".LibelleChampAid("perso1")." : </span>\n";
    echo "<input type=\"text\" name=\"reg_perso1\" value=\"".htmlentities($reg_perso1)."\" size=\"40\" />";
  }
 } else {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso1','R',$annee)) {
    echo "<br /><span class = 'bold'>".LibelleChampAid("perso1")." : </span>\n";
    echo htmlentities($reg_perso1)."\n";
  }
 }
 // perso2
 If ($action != "visu") {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso2','W',$annee)) {
    echo "<br /><span class = 'bold'>".LibelleChampAid("perso2")." : </span>\n";
    echo "<input type=\"text\" name=\"reg_perso2\" value=\"".htmlentities($reg_perso2)."\" size=\"40\" />";
  }
 } else {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso2','R',$annee)) {
    echo "<br /><span class = 'bold'>".LibelleChampAid("perso2")." : </span>\n";
    echo htmlentities($reg_perso2)."\n";
  }
 }
 // perso3
 If ($action != "visu") {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso3','W',$annee)) {
    echo "<br /><span class = 'bold'>".LibelleChampAid("perso3")." : </span>\n";
    echo "<input type=\"text\" name=\"reg_perso3\" value=\"".htmlentities($reg_perso3)."\" size=\"40\" />";
  }
 } else {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso3','R',$annee)) {
    echo "<br /><span class = 'bold'>".LibelleChampAid("perso3")." : </span>\n";
    echo htmlentities($reg_perso3)."\n";
  }
 }
}
echo "</div>\n";

// Partie r�serv�e � l'admin
if ($_SESSION["statut"]=="administrateur") {
    echo "<div class='bloc'>\n";
    If ($action != "visu") {
        //nom
        echo "<p>Nom : <input type=\"text\" name=\"reg_nom\" size=\"50\" value=\"".htmlentities($aid_nom)."\" /></p>\n";
        if ($annee=='') {
          //numero
          echo "<p>Number (fac.) : <input type=\"text\" name=\"reg_num\" size=\"4\" value=\"".$reg_num."\" /></p>\n";
          //eleve_peut_modifier
          echo "<p><input type=\"checkbox\" name=\"reg_eleve_peut_modifier\" value=\"y\" ";
          if ($reg_eleve_peut_modifier == 'y') echo " checked ";
          echo "/> \n";
          echo "The responsible student can modify the card.</p>\n";
          //prof_peut_modifier
          echo "<p><input type=\"checkbox\" name=\"reg_prof_peut_modifier\" value=\"y\" ";
          if ($reg_prof_peut_modifier == 'y') echo " checked ";
          echo "/> \n";
          echo "The responsible professors can modify the card.</p>\n";
          //cpe_peut_modifier
          echo "<p><input type=\"checkbox\" name=\"reg_cpe_peut_modifier\" value=\"y\" ";
          if ($reg_cpe_peut_modifier == 'y') echo " checked ";
          echo "/> \n";
          echo "The CPE can modify the card.</p>\n";
        }
        //fiche_publique
        echo "<p><input type=\"checkbox\" name=\"reg_fiche_publique\" value=\"y\" ";
        if ($reg_fiche_publique == 'y') echo " checked ";
        echo "/> \n";
        echo "La fiche est visible dans <a href=\"javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">l'interface publique</a>.</p>\n";
        //affiche_adresse1
        echo "<p><input type=\"checkbox\" name=\"reg_affiche_adresse1\" value=\"y\" ";
        if ($reg_affiche_adresse1 == 'y') echo " checked ";
        echo "/> \n";
        echo "The public address of access to the production is in link on the public card.</p>\n";
        //en_construction
        echo "<p><input type=\"checkbox\" name=\"reg_en_construction\" value=\"y\" ";
        if ($reg_en_construction == 'y') echo " checked ";
        echo "/> \n";
        echo "The public address is declared \"in construction\" on the public card.</p>\n";
    } else {
        echo "<ul>";
        if ($annee=='') {
          //eleve_peut_modifier
          if ($reg_eleve_peut_modifier == 'y')
              echo "<li>The responsible student can modify the card.</li>\n";
          else
              echo "<li>The responsible student cannot modify the card.</li>\n";
          //prof_peut_modifier
          if ($reg_prof_peut_modifier == 'y')
              echo "<li>The responsible professors can modify the card.</li>\n";
          else
              echo "<li>The responsible professors cannot modify the card.</li>\n";
          //cpe_peut_modifier
          if ($reg_cpe_peut_modifier == 'y')
              echo "<li>The CPE can modify the card.</li>\n";
          else
              echo "<li>The CPE cannot modify the card.</li>\n";
        }
        //fiche_publique
        if ($reg_fiche_publique == 'y')
            echo "<li>The card is visible in <a href=\"javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">the public interface</a>.</li>\n";
        else
            echo "<li>The card is not visible in <a href=\"javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">the public interface</a>.</li>\n";
        //affiche_adresse1
        if ($reg_affiche_adresse1 == 'y')
            echo "<li>The public address of access to the production is in a link on the public card.</li>\n";
        else
            echo "<li>There is no link on the public card towards the public address of access to the production.</li>\n";
        //en_construction
        if ($reg_en_construction == 'y')
            echo "<li>The public address is declared \"in construction\" on the public card.</li>\n";
        else
            echo "<li>The public address is not declared \"in construction\" on the public card.</li>\n";
        echo "</ul>\n";

    }


    echo "</div>\n";
}

// r�sum�
if ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'resume',"W",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Summary</span> (limit� � 600 caract�res) :\n";
    echo "<br /><i>Project presentation, objectives, achievements, ....</i>\n";
    echo "<br /><textarea name=\"reg_resume\" rows=\"6\" cols=\"100\" onKeyPress=\"CaracMax(this, 600)\" >".htmlentities($reg_resume)."</textarea>\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'resume',"R",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Summary</span> (Project presentation, objectives, achievements, ....)\n";
    if ($reg_resume == "")
        echo "<br />".$non_defini."\n";
    else
        echo "<br />".htmlentities($reg_resume)."\n";
  }
}
echo "</div>\n";

// Famille
echo "<div class='bloc'>";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'famille',"W",$annee)) {
    echo "<span class = 'bold'>Classify your project in the following list (Dewey classification) : </span><br />\n";
    $call_famille = mysql_query("select * from aid_familles order by ordre_affichage");
    $nb_famille = mysql_num_rows($call_famille);
    echo "<select name=\"reg_famille\" size=\"1\">\n";
    echo "<option value=\"\">(choose)</option>\n";
    $k = 0;
    while ($k < $nb_famille) {
        $id_famille = mysql_result($call_famille,$k,"id");
        $nom_famille = mysql_result($call_famille,$k,"type");
        echo "<option value=\"".$id_famille."\" ";
        if ($id_famille == $reg_famille) echo " selected ";
        echo ">".$nom_famille."</option>\n";
        $k++;
    }
    echo "</select>\n";
    echo "<br />Do not hesitate to ask council to the documentatist.
    To help you, <a href=\"javascript:centrerpopup('100cases.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">a detailed table is also available</a>.";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'famille',"R",$annee)) {
    $famille = sql_query1("select type from aid_familles where id = '".$reg_famille."'");
    if ($famille == -1) $famille = $non_defini;
    echo "<span class = 'bold'>Project classified in the family</span> : ".$famille."\n";
  }
}
echo "</div>\n";

//Mots cl�s :
$mc = explode("|",$reg_mots_cles);
$k = 0;
while ($k < 5) {
    if ((!isset($mc[$k])) or (trim($mc[$k]) == "")) $mc[$k] = "";
    $k++;
}

echo "<div class='bloc'>\n";
If ($action != "visu")  {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'mots_cles',"W",$annee)) {
    echo "<span class = 'bold'>Mots cl�s </span> :\n";
    echo "<br /><i>Tapez entre 3 et 5 mots-cles</i>";
    echo "<table><tr>";
    $k = 0;
    while ($k < 5) {
        echo "<td><input type=\"text\" name=\"mc".$k."\" value=\"".htmlentities($mc[$k])."\" size=\"15\" /></td>\n";
        $k++;
    }
    echo "</tr></table>";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'mots_cles',"R",$annee)) {
    echo "<span class = 'bold'>Mots cl�s </span> :\n";
    $aff_motcle = "";
    $k = 0;
    while ($k < 5) {
        if ($mc[$k] != "") {
            if ($aff_motcle != "") $aff_motcle .= " - ";
            $aff_motcle .= $mc[$k];
        }
        $k++;
    }
    if ($aff_motcle == "")
        echo $non_defini;
    else
        echo $aff_motcle;
  }
}
echo "</div>\n";

//Production
$p = explode("|",$reg_productions);
$k = 0;
while ($k < $nb_productions) {
    if (!isset($p[$k]) or (trim($p[$k]) == "")) $p[$k] = "";
    $k++;
}

echo "<div class='bloc'>\n";
If ($action != "visu")  {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'productions',"W",$annee)) {
    echo "<p><span class = 'bold'>Production </span> :\n";
    echo "<br /><i>check in maximum ".$nb_max_cases." items which characterizes your project as well as possible.</i>";
    echo "<table border = 1>";
    $newligne = 1;
    $k = 0;
    while ($k < $nb_productions) {
        if ($newligne == 1) echo "<tr>";
        $id_productions = mysql_result($call_productions,$k,"id");
        $nom_productions = mysql_result($call_productions,$k,"nom");
        echo "<td><input type=\"checkbox\" name=\"p".$k."\" value=\"".$id_productions."\" ";
        if (in_array($id_productions, $p))  echo " checked ";
        echo " onClick=\"compteur_coches(this)\" />";
        echo $nom_productions."</td>\n";
        $newligne++;
        if ($newligne == 5) {
            echo "</tr>";
            $newligne = 1;
        }
        $k++;
    }
    if ($newligne != 1) echo "</tr>";
    echo "</table>";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'productions',"R",$annee)) {
    echo "<p><span class = 'bold'> Waited Production </span> :\n";
    $k = 0;
    $liste = "";
    while ($k < $nb_productions) {
        $nom_productions = sql_query1("select nom from aid_productions where id = '".$p[$k]."'");
        if ($nom_productions != -1) {
            if ($liste != "") $liste .= " - ";
            $liste .= $nom_productions;
        }
        $k++;
    }
    if ($liste == "")
        echo $non_defini;
    else
        echo $liste;
  }
}
echo "</div>\n";

//Public
$public = explode("|",$reg_public);
$k = 0;
while ($k < $nb_public) {
    if (!isset($public[$k]) or (trim($public[$k]) == "")) $public[$k] = "";
    $k++;
}

echo "<div class='bloc'>\n";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'public_destinataire',"W",$annee)) {
    echo "<p><span class = 'bold'>Recipient public</span> :\n";
    echo "<br /><i>check the items corresponding to the public to which  the project is addressed.</i>";
    echo "<table border = 1>";
    $newligne = 1;
    $k = 0;
    while ($k < $nb_public) {
        if ($newligne == 1) echo "<tr>";
        $id_public = mysql_result($call_public,$k,"id");
        $nom_public = mysql_result($call_public,$k,"public");
        echo "<td><input type=\"checkbox\" name=\"public".$k."\" value=\"".$id_public."\" ";
        if (in_array($id_public, $public))  echo " checked ";
        echo " />";
        echo $nom_public."</td>\n";
        $newligne++;
        if ($newligne == 7) {
            echo "</tr>";
            $newligne = 1;
        }
        $k++;
    }
    if ($newligne != 1) echo "</tr>";
    echo "</table>";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'public_destinataire',"R",$annee)) {
    echo "<p><span class = 'bold'Recipient public </span> :\n";
    $k = 0;
    $liste = "";
    while ($k < $nb_public) {
        $nom_public = sql_query1("select public from aid_public where id = '".$public[$k]."'");
        if ($nom_public != -1) {
            if ($liste != "") $liste .= " - ";
            $liste .= $nom_public;
        }
        $k++;
    }
    if ($liste == "")
        echo $non_defini;
    else
        echo $liste;
  }
}
echo "</div>\n";


// Adresses publique :
echo "<div class='bloc'>\n";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse1',"W",$annee)) {
    echo "<span class = 'bold'>Indicate possibly below a <b>public link of Internet type</B> who gives access to the production :</span>\n";
    echo "<br /><input type=\"text\" name=\"reg_adresse1\" value=\"".htmlentities($reg_adresse1)."\" size=\"50\" />\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse1',"R",$annee)) {
    echo "<span class = 'bold'>Public link giving access to the production : </span>\n";
    if ($reg_adresse1 == "") echo $non_defini;
    else echo "<br />".$reg_adresse1;
  }
}
echo "</div>\n";

// Adresses � acc�s restreint :
echo "<div class='bloc'>\n";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse2',"W",$annee)) {
    echo "<span class = 'bold'>Indicate below <b>a link with restricted access</B>
    <br />(for example, <b>access path to the production on a server</b>) :</span>\n";
    echo "<br /><input type=\"text\" name=\"reg_adresse2\" value=\"".htmlentities($reg_adresse2)."\" size=\"50\" />\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse2',"R",$annee)) {
    echo "<span class = 'bold'Restricted access  to the production : </span>\n";
    if ($reg_adresse2 == "") echo $non_defini;
    else echo "<br />".$reg_adresse2;
  }
}
echo "</div>\n";

// Contacts
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'contacts',"W",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Contacts made outside the establishment, resources people...</span> (limited to 600 characters) :\n";
    echo "<br /><i>List external contacts (name, first name, association, corporate name...)</i>\n";
    echo "<br /><textarea name=\"reg_contacts\" rows=\"6\" cols=\"100\" onKeyPress=\"CaracMax(this, 600)\" >".htmlentities($reg_contacts)."</textarea>\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'contacts',"R",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>List of external contacts, resources people ...</span>\n";
    if ($reg_contacts == "")
        echo "<br />".$non_defini."\n";
    else
        echo "<br />".htmlentities($reg_contacts)."\n";
  }
}
echo "</div>\n";


// Disciplines 1
echo "<div class='bloc'>";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere1',"W",$annee)) {
    echo "<span class = 'bold'>Indicate the principal discipline to which your project is attached : </span><br />\n";
    $call_discipline = mysql_query("select matiere, nom_complet from matieres where (matiere_aid='y') order by nom_complet");
    $nb_discipline = mysql_num_rows($call_discipline);
    echo "<select name=\"reg_discipline1\" size=\"1\">\n";
    echo "<option value=\"\">(choisissez)</option>\n";
    $k = 0;
    $discipline_reconnue=FALSE;
    while ($k < $nb_discipline) {
        $id_discipline = mysql_result($call_discipline,$k,"matiere");
        $nom_discipline = mysql_result($call_discipline,$k,"nom_complet");
        echo "<option value=\"".$id_discipline."\" ";
        if ($id_discipline == $reg_discipline1) {
            echo " selected ";
            $discipline_reconnue=TRUE;
        }
        echo ">".$nom_discipline."</option>\n";
        $k++;
    }
    if ((!$discipline_reconnue) and ($reg_discipline1!=''))
        echo "<option value=\"".$reg_discipline1."\" selected >Discipline code not recognized  : ".$reg_discipline1."</option>\n";
    echo "</select>\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere1',"R",$annee)) {
    $discipline1 = sql_query1("select nom_complet from matieres where matiere = '".$reg_discipline1."'");
    if ($discipline1 == "-1") {
      if ($reg_discipline1=='')
        $discipline1 = $non_defini;
      else
        $discipline1 = $reg_discipline1;
    }

    echo "<span class = 'bold'>Principal discipline to which the project is attached</span> : ".$discipline1."\n";
  }
}
echo "</div>\n";

// Disciplines 2
echo "<div class='bloc'>";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere2',"W,$annee")) {
    echo "<span class = 'bold'>Indicate the secondary discipline to which your project is attached : </span><br />\n";
    $call_discipline = mysql_query("select matiere, nom_complet from matieres where (matiere_aid='y') order by nom_complet");
    $nb_discipline = mysql_num_rows($call_discipline);
    echo "<select name=\"reg_discipline2\" size=\"1\">\n";
    echo "<option value=\"\">(choose)</option>\n";
    $k = 0;
    $discipline_reconnue=FALSE;
    while ($k < $nb_discipline) {
        $id_discipline = mysql_result($call_discipline,$k,"matiere");
        $nom_discipline = mysql_result($call_discipline,$k,"nom_complet");
        echo "<option value=\"".$id_discipline."\" ";
        if ($id_discipline == $reg_discipline2) {
            echo " selected ";
            $discipline_reconnue=TRUE;
        }
        echo ">".$nom_discipline."</option>\n";
        $k++;
    }
    if ((!$discipline_reconnue) and ($reg_discipline2!=''))
        echo "<option value=\"".$reg_discipline2."\" selected >Discipline code not recognized  : ".$reg_discipline2."</option>\n";

    echo "</select>\n";

  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere2',"R",$annee)) {
    $discipline2 = sql_query1("select nom_complet from matieres where matiere = '".$reg_discipline2."'");
    if ($discipline2 == "-1") {
      if ($reg_discipline2=='')
        $discipline2 = $non_defini;
      else
        $discipline2 = $reg_discipline2;
    }
    echo "<span class = 'bold'>Secondary discipline  to which the project is attached</span> : ".$discipline2."\n";
  }
}
echo "</div>\n";


// divers
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'divers',"W",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Others</span> (limited to 600 characters) :\n";
    echo "<br /><textarea name=\"reg_divers\" rows=\"6\" cols=\"100\" onKeyPress=\"CaracMax(this, 600)\" >".htmlentities($reg_divers)."</textarea>\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'divers',"R",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Others</span>\n";
    if ($reg_divers == "")
        echo "<br />".$non_defini."\n";
    else
        echo "<br />".htmlentities($reg_divers)."\n";
   }
}
echo "</div>\n";


if ($action != "visu") {
    echo "<input type=\"hidden\" name=\"indice_aid\" value=\"".$indice_aid."\" />";
    echo "<input type=\"hidden\" name=\"aid_id\" value=\"".$aid_id."\" />";
    echo "<input type=\"hidden\" name=\"action\" value=\"".$action."\" />";
    echo "<input type=\"hidden\" name=\"annee\" value=\"".$annee."\" />";
    echo "<input type=\"hidden\" name=\"is_posted\" value=\"yes\" />";
    echo "<center><div id = \"fixe\"><input type=\"submit\" value=\"Registrer\" /></div></center>";
    echo "</form>";
}
include "../lib/footer.inc.php";
?>