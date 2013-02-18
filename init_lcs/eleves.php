<?php
/*
 * $Id: eleves.php 7858 2011-08-21 13:12:55Z crob $
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
function connect_ldap($l_adresse,$l_port,$l_login,$l_pwd) {
    $ds = @ldap_connect($l_adresse, $l_port);
    if($ds) {
       // On dit qu'on utilise LDAP V3, sinon la V2 par d?faut est utilis? et le bind ne passe pas.
       $norme = @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
       // Acc?s non anonyme
       if ($l_login != '') {
          // On tente un bind
          $b = @ldap_bind($ds, $l_login, $l_pwd);
       } else {
          // Acc?s anonyme
          $b = @ldap_bind($ds);
       }
       if ($b) {
           return $ds;
       } else {
           return false;
       }
    } else {
       return false;
    }
}


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

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_eleves;

// Initialisation
$lcs_ldap_people_dn = 'ou=people,'.$lcs_ldap_base_dn;
$lcs_ldap_groups_dn = 'ou=groups,'.$lcs_ldap_base_dn;

function add_eleve($_login, $_nom, $_prenom, $_civilite, $_naissance, $_elenoet = 0) {
    // Fonction d'ajout d'un élève dans la base Gepi
    if ($_civilite != "M" && $_civilite != "F") {
        if ($_civilite == 1) {
            $_civilite = "M";
        } elseif ($_civilite == 0) {
            $_civilite = "F";
        } else {
            $_civilite = "F";
        }
    }

    // Si l'élève existe déjà, on met simplement à jour ses informations...
    $test = mysql_query("SELECT login FROM eleves WHERE login = '" . $_login . "'");
    if (mysql_num_rows($test) > 0) {
        $record = mysql_query("UPDATE eleves SET nom = '" . $_nom . "', prenom = '" . $_prenom . "', sexe = '" . $_civilite . "', naissance = '" . $_naissance . "', elenoet = '" . $_elenoet . "' WHERE login = '" . $_login . "'");
    } else {
        $query = "INSERT into eleves SET
        login= '" . $_login . "',
        nom = '" . $_nom . "',
        prenom = '" . $_prenom . "',
        sexe = '" . $_civilite . "',
        naissance = '". $_naissance ."',
        elenoet = '".$_elenoet."'";
        $record = mysql_query($query);
    }

    if ($record) {
        return true;
    } else {
        return false;
    }
}


//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year : Importation of the students";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<script type="text/javascript">
<!--
function CocheCase(boul){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      document.formulaire.elements[i].checked = boul ;
    }
  }
 }

function InverseSel(){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      a=!document.formulaire.elements[i].checked  ;
      document.formulaire.elements[i].checked = a
    }
   }
}

function MetVal(cible){
len = document.formulaire.elements.length;
if ( cible== 'nom' ) {
  a=2;
  b=document.formulaire.nom.value;
  } else {
  a=3;
  b=document.formulaire.pour.value;
  }
for( i=0; i<len; i++) {
if ((document.formulaire.elements[i].type=='checkbox')
     &&
    (document.formulaire.elements[i].checked)
    ) {
document.formulaire.elements[i+a].value = b ;
}}}
 // -->
</script>

<?php



echo "<p class='bold'><a href='../init_lcs/index.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>";

if (isset($_POST['step'])) {
	check_token(false);

    // L'admin a validé la procédure, on procède donc...

    // On se connecte au LDAP
    $ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");

    //----***** STEP 1 *****-----//

    if ($_POST['step'] == "1") {
        // La première étape consiste à importer les classes

        if ($_POST['record'] == "yes") {
            // Les données ont été postées, on les traite donc immédiatement

            $j=0;
            while ($j < count($liste_tables_del)) {
                if (mysql_result(mysql_query("SELECT count(*) FROM ".$liste_tables_del[$j]),0)!=0) {
                    $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
                }
                $j++;
            }

            // On va enregistrer la liste des classes, ainsi que les périodes qui leur seront attribuées

            $sr = ldap_search($ds,$lcs_ldap_groups_dn,"(cn=Classe*)");
            $data = ldap_get_entries($ds,$sr);
            for ($i=0;$i<$data["count"];$i++) {
                $classe=preg_replace("/Classe_/","",$data[$i]["cn"][0]);
                // On enregistre la classe
                // On teste d'abord :
                $test = mysql_result(mysql_query("SELECT count(*) FROM classes WHERE (classe='$classe')"),0);

                if ($test == "0") {
                    //$reg_classe = mysql_query("INSERT INTO classes SET classe='".$classe."',nom_complet='".$_POST['reg_nom_complet'][$classe]."',suivi_par='".$_POST['reg_suivi'][$classe]."',formule='".$_POST['reg_formule'][$classe]."', format_nom='np'");
                    $reg_classe = mysql_query("INSERT INTO classes SET classe='".$classe."',nom_complet='".$_POST['reg_nom_complet'][$classe]."',suivi_par='".$_POST['reg_suivi'][$classe]."',formule='".html_entity_decode($_POST['reg_formule'][$classe])."', format_nom='np'");
                } else {
                    //$reg_classe = mysql_query("UPDATE classes SET classe='".$classe."',nom_complet='".$_POST['reg_nom_complet'][$classe]."',suivi_par='".$_POST['reg_suivi'][$classe]."',formule='".$_POST['reg_formule'][$classe]."', format_nom='np' WHERE classe='$classe'");
                    $reg_classe = mysql_query("UPDATE classes SET classe='".$classe."',nom_complet='".$_POST['reg_nom_complet'][$classe]."',suivi_par='".$_POST['reg_suivi'][$classe]."',formule='".html_entity_decode($_POST['reg_formule'][$classe])."', format_nom='np' WHERE classe='$classe'");
                }
                if (!$reg_classe) echo "<p>Error during recording of the class $classe.";

                // On enregistre les périodes pour cette classe
                // On teste d'abord :
                $id_classe = mysql_result(mysql_query("select id from classes where classe='$classe'"),0,'id');
                $test = mysql_result(mysql_query("SELECT count(*) FROM periodes WHERE (id_classe='$id_classe')"),0);
                if ($test == "0") {
                    $j = '0';
                    while ($j < $_POST['reg_periodes_num'][$classe]) {
                        $num = $j+1;
                        $nom_per = "Période ".$num;
                        if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                        $register = mysql_query("INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                        if (!$register) echo "<p>Error during recording of a period for the class $classe";
                        $j++;
                    }
                } else {
                    // on "démarque" les périodes des classes qui ne sont pas à supprimer
                    $sql = mysql_query("UPDATE periodes SET verouiller='N' where (id_classe='$id_classe' and num_periode='1')");
                    $sql = mysql_query("UPDATE periodes SET verouiller='O' where (id_classe='$id_classe' and num_periode!='1')");
                    //
                    $nb_per = mysql_num_rows(mysql_query("select num_periode from periodes where id_classe='$id_classe'"));
                    if ($nb_per > $_POST['reg_periodes_num'][$classe]) {
                        // Le nombre de périodes de la classe est inférieur au nombre enregistré
                        // On efface les périodes en trop
                        $k = 0;
                        for ($k=$_POST['reg_periodes_num'][$classe]+1; $k<$nb_per+1; $k++) {
                            $del = mysql_query("delete from periodes where (id_classe='$id_classe' and num_periode='$k')");
                        }
                    }
                    if ($nb_per < $_POST['reg_periodes_num'][$classe]) {

                        // Le nombre de périodes de la classe est supérieur au nombre enregistré
                        // On enregistre les périodes
                        $k = 0;
                        $num = $nb_per;
                        for ($k=$nb_per+1 ; $k < $_POST['reg_periodes_num'][$classe]+1; $k++) {
                            $num++;
                            $nom_per = "Période ".$num;
                            if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                            $register = mysql_query("INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                            if (!$register) echo "<p>Error during recording of a period for the class $classe";
                        }
                    }
                }

            }

            // On efface les classes qui ne sont pas réutilisées cette année  ainsi que les entrées correspondantes dans  j_classes_matieres_professeurs
            $sql = mysql_query("select distinct id_classe from periodes where verouiller='T'");
            $k = 0;
            while ($k < mysql_num_rows($sql)) {
               $id_classe = mysql_result($sql, $k);
               $res1 = mysql_query("delete from classes where id='".$id_classe."'");
               $res2 = mysql_query("delete from j_classes_matieres_professeurs where id_classe='".$id_classe."'");
               // On supprime les groupes qui étaient liées à la classe
               $get_groupes = mysql_query("SELECT id_groupe FROM j_groupes_classes WHERE id_classe = '" . $id_classe . "'");
               for ($l=0;$l<$nb_groupes;$l++) {
                    $id_groupe = mysql_result($get_groupes, $l, "id_groupe");
                    $delete2 = mysql_query("delete from j_groupes_classes WHERE id_groupe = '" . $id_groupe . "'");
                    // On regarde si le groupe est toujours lié à une autre classe ou pas
                    $check = mysql_result(mysql_query("SELECT count(*) FROM j_groupes_classes WHERE id_groupe = '" . $id_groupe . "'"), 0);
                    if ($check == "0") {
                        $delete1 = mysql_query("delete from groupes WHERE id = '" . $id_groupe . "'");
                        $delete2 = mysql_query("delete from j_groupes_matieres WHERE id_groupe = '" . $id_groupe . "'");
                        $delete2 = mysql_query("delete from j_groupes_professeurs WHERE id_groupe = '" . $id_groupe . "'");
                    }
               }
               $k++;
            }
            $res = mysql_query("delete from periodes where verouiller='T'");
            echo "<p>You have just carried out the recording of the data concerning the
classes. If there were No errors, you can go to the next stage to record the data concerning the students.</p>";
            echo "<p><b>CAUTION</b> :<br>Fields \"regim\" (school luncher, external, ...), \"doubling\"  and \"national identifier\" are not present in directory LDAP.
            It is the same for all information on the responsibles of the students.
            <br />At the end of this stage, <b>you will have to thus proceed to an operation consisting in converting
the table \"eleves\" and to import missing information.</b>
            <br />You will have for that to provide files CSV (ELEVES.CSV, PERSONNES.CSV, RESPONSABLES.CSV et ADRESSES.CSV) <b><a href=\"../init_xml/lecture_xml_sconet.php\" target=\"_blank\">generated here</a></b> from XML files extracted from SCONET.</p>";
            echo "<center>";
            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=\"hidden\" name=\"record\" value=\"no\" />";
            echo "<input type=\"hidden\" name=\"step\" value=\"2\" />";
            echo "<input type=\"submit\" value=\"Access to stage 2\" />";
            echo "</form>";
            echo "</center>";

			// On sauvegarde le témoin du fait qu'il va falloir
			// convertir pour générer l'ELE_ID et remplir ensuite les nouvelles tables responsables:
			saveSetting("conv_new_resp_table", 0);


        } else {
            // Les données n'ont pas encore été postées, on affiche donc le tableau des classes

            // On commence par "marquer" les classes existantes dans la base
            $sql = mysql_query("UPDATE periodes SET verouiller='T'");

            $sr = ldap_search($ds,$lcs_ldap_groups_dn,"(cn=Classe*)");
            $data = ldap_get_entries($ds,$sr);

            // On va enregistrer la liste des classes, ainsi que les périodes qui leur seront attribuées

            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=hidden name='record' value='yes'>";
            echo "<input type=hidden name='step' value='1'>";

            echo "<p>The classes in green indicate already existing classes in base GEPI.<br />The classes in red indicate new classes and which will be added to
base GEPI.<br /></p>";
            echo "<p>For the new classes, standard names are used for the periods (period 1, period 2...), and only the first period is not locked. You will be able to modify these parameters later</p>";
            echo "<p>Caution !!! There are no tests on the fields. Be vigilant, not put special characters in the fields ...</p>";
            echo "<p>Try to fill all the fields, that will avoid having to do it later.</p>";
            echo "<p>Do not forget <b>to record the data</b> by clicking on the button in bottom of the page<br /><br />";

            ?>
            <fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;">
            <legend style="font-variant: small-caps;"> Help to the filling </legend>
            <table border="0">
            <tr>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="25%">&nbsp;</td>
              <td width="53%">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="5">You can fill the boxes <font color="red">
            individually</font> and/or <font color="red">globaly</font> due to the functionalities offered below :</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">1) Initially, select the lines one per one</td>
            </tr>
              <tr>
              <td colspan="3">&nbsp;</td>
              <td colspan="3">You can too &nbsp;
              <a href="javascript:CocheCase(true)">
              CHECK</a> or
              <a href="javascript:CocheCase(false)">
              UNCHECK</a> all lines , or
              <a href="javascript:InverseSel()">
              REVERSE </a>the selection</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">2) Then, for the selected lines :</td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">the name in the bottom of the bulletin will be &nbsp;:&nbsp;</td>
              <td><input type="text" name="nom" maxlength="80" size="40">
              <input type ="button" name="but_nom" value="Recopy"
            onclick="javascript:MetVal('nom')"></td>
             </td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">the formula in the bottom of the bulletin will be
            &nbsp;:&nbsp;</td>
              <td><input type="text" name="pour" maxlength="80" size="40">
              <input type ="button" name="but_pour" value="Recopier"
            onclick="javascript:MetVal('pour')"></td>
             </td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">3) Click on the buttons "Recopy" to fill the selected fields.</td>
            </tr>

            </table>
            </fieldset>
            <br />
            <?php
            echo "<table border=1 cellpadding=2 cellspacing=2>";
            echo "<tr><td><p class=\"small\"><center>Help<br />Filling</center></p></td><td><p class=\"small\">Identifier of the class</p></td><td><p class=\"small\">Complete name</p></td><td><p class=\"small\">Name appearing in the bottom of the bulletin</p></td><td><p class=\"small\">formula in the bottom of the bulletin</p></td><td><p class=\"small\">Numbers of periods</p></td></tr>";
            for ($i=0;$i<$data["count"];$i++) {
                $classe_id = preg_replace("/Classe_/","",$data[$i]["cn"][0]);
                $description= $data[$i]["description"][0];
                if ($description == "") $description = $classe_id;

                $test_classe_exist = mysql_query("SELECT * FROM classes WHERE classe='$classe_id'");
                $nb_test_classe_exist = mysql_num_rows($test_classe_exist);

                if ($nb_test_classe_exist==0) {
                    $nom_complet = $description;
                    $nom_court = "<font color=red>".$classe_id."</font>";
                    $suivi_par = getSettingValue("gepiAdminPrenom")." ".getSettingValue("gepiAdminNom").", ".getSettingValue("gepiAdminFonction");
                    $formule = "";
                    $nb_per = '3';
                } else {
                    $id_classe = mysql_result($test_classe_exist, 0, 'id');
                    $nb_per = mysql_num_rows(mysql_query("select num_periode from periodes where id_classe='$id_classe'"));
                    $nom_court = "<font color=green>".$description."</font>";
                    $nom_complet = mysql_result($test_classe_exist, 0, 'nom_complet');
                    $suivi_par = mysql_result($test_classe_exist, 0, 'suivi_par');
                    $formule = mysql_result($test_classe_exist, 0, 'formule');
                }
                echo "<tr>";
                echo "<td><center><input type=\"checkbox\"></center></td>\n";
                echo "<td>";
                echo "<p><b><center>$nom_court</center></b></p>";
                echo "";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_nom_complet[$classe_id]' value=\"".$nom_complet."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_suivi[$classe_id]' value=\"".$suivi_par."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_formule[$classe_id]' value=\"".$formule."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<select size=1 name='reg_periodes_num[$classe_id]'>\n";
                for ($k=1;$k<7;$k++) {
                    echo "<option value='$k'";
                    if ($nb_per == "$k") echo " SELECTED";
                    echo ">$k";
                }
                echo "</select>";
                echo "</td></tr>";
            }
            echo "</table>\n";
            echo "<input type=hidden name='step2' value='y'>\n";
            echo "<center><input type='submit' value='Save the data'></center>\n";
            echo "</form>\n";

        }



    //----***** STEP 2 *****-----//
    } elseif ($_POST['step'] == "2") {
        // LDAP attribute
        $ldap_people_attr = array(
        "uid",               // login
        "cn",                // Prenom  Nom
        "sn",               // Nom
        "givenname",            // Pseudo
        "mail",              // Mail
        "homedirectory",           // Home directory personnal web space
        "description",
        "loginshell",
        "gecos",             // Date de naissance,Sexe (F/M),
        "employeenumber"    // identifiant gep
        );

        // La deuxième étape consiste à importer les élèves et à les affecter dans les classes
        $classes = mysql_query("SELECT id, classe FROM classes");
        $nb_classes = mysql_num_rows($classes);
        $eleves_de = array();
        echo "<table border=\"1\" cellpadding=\"3\" cellspacing=\"3\">\n<tr><td>Name of the class</td><td>Login student</td><td>Name </td><td>First name</td><td>Sex</td><td>Date of birth</td><td>Number GEP</td></tr>\n";
        for ($i=0;$i<$nb_classes;$i++) {
            $current_classe = mysql_result($classes, $i, "classe");
            $current_classe_id = mysql_result($classes, $i, "id");
            $filtre = "(cn=Classe_".$current_classe.")";
            $result= ldap_search ($ds, $lcs_ldap_groups_dn, $filtre);
            if ($result) {
                $info = @ldap_get_entries( $ds, $result );
                for ( $u = 0; $u < $info[0]["memberuid"]["count"] ; $u++ ) {
                  $uid = $info[0]["memberuid"][$u] ;
                  if (trim($uid) !="") {
                    $eleve_de[$current_classe_id]=$uid;
                    // Extraction des infos sur l'élève :
                    $result2 = @ldap_read ( $ds, "uid=".$uid.",".$lcs_ldap_people_dn, "(objectclass=posixAccount)", $ldap_people_attr );
                    if ($result2) {
                        $info2 = @ldap_get_entries ( $ds, $result2 );
                        if ( $info2["count"]) {
                            // Traitement du champ gecos pour extraction de date de naissance, sexe
                            $gecos = $info2[0]["gecos"][0];
                            $tmp = split ("[\,\]",$info2[0]["gecos"][0],4);
                            $ret_people = array (
                            "uid"         => $info2[0]["uid"][0],
                            "nom"         => stripslashes( utf8_decode($info2[0]["sn"][0]) ),
                            "fullname"        => stripslashes( utf8_decode($info2[0]["cn"][0]) ),
                            "pseudo"      => utf8_decode($info2[0]["givenname"][0]),
                            "email"       => $info2[0]["mail"][0],
                            "homedirectory"   => $info2[0]["homedirectory"][0],
                            "description" => utf8_decode($info2[0]["description"][0]),
                            "shell"           => $info2[0]["loginshell"][0],
                            "sexe"            => $tmp[2],
                            "naissance"       => $tmp[1],
                            "no_gep"          => $info2[0]["employeenumber"][0]
                            );
                            $long = strlen($ret_people["fullname"]) - strlen($ret_people["nom"]);
                            $prenom = substr($ret_people["fullname"], 0, $long) ;


                            $add = add_eleve($uid,$ret_people["nom"],$prenom,$tmp[2],$tmp[1],$ret_people["no_gep"]);
                            $get_periode_num = mysql_result(mysql_query("SELECT count(*) FROM periodes WHERE (id_classe = '" . $current_classe_id . "')"), 0);
                            $check = mysql_result(mysql_query("SELECT count(*) FROM j_eleves_classes WHERE (login = '" . $uid . "')"), 0);
                            if ($check > 0)
                                $del = mysql_query("DELETE from j_eleves_classes WHERE login = '" . $uid . "'");
                            for ($k=1;$k<$get_periode_num+1;$k++) {
                                $res = mysql_query("INSERT into j_eleves_classes SET login = '" . $uid . "', id_classe = '" . $current_classe_id . "', periode = '" . $k . "'");
                            }
                            $check = mysql_result(mysql_query("SELECT count(*) FROM j_eleves_regime WHERE (login = '" . $uid . "')"), 0);
                            if ($check > 0)
                                $del = mysql_query("DELETE from j_eleves_regime WHERE login = '" . $uid . "'");
                            $res = mysql_query("INSERT into j_eleves_regime SET login = '" . $uid . "',
                            regime  = 'd/p',
                            doublant  = '-'");
                        }
                        @ldap_free_result ( $result2 );
                    }
                    $date_naissance = substr($tmp[1],6,2)."-".substr($tmp[1],4,2)."-".substr($tmp[1],0,4) ;
                    echo "<tr><td>".$current_classe."</td><td>".$uid."</td><td>".$ret_people["nom"]."</td><td>".$prenom."</td><td>".$tmp[2]."</td><td>".$date_naissance."</td><td>".$ret_people["no_gep"]."</td></tr>\n";
                  }
                }
            }
            @ldap_free_result ( $result );
        }
        echo "</table><p>Operation made.</p>";
        echo "<p>Before passing at the next stage, you must make the conversion of the table \"eleves\" and the importation of the missing data :
        <a href='../responsables/conversion.php?mode=1'>Conversion and importation of the missing data</a>.</p>";
    }

} else {
    echo "<p>The operation of importation of the students from the LDAP of LCS will make the following operations :</p>";
    echo "<ul>";
    echo "<li>Importation of the classes.</li>";
    echo "<li>Attempt at addition of each students present in the LCS directory .</li>";
    echo "<li>If the student does not exist, it is created.</li>";
    echo "<li>If the student already exists, its basic information is updated.</li>";
    echo "<li>Assignment of the students to the classes.</li>";
    echo "</ul>";


    echo "<form enctype='multipart/form-data' action='eleves.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='step' value='1'>";
    echo "<input type=hidden name='record' value='no'>";
    $j=0;
    $flag=0;
    while (($j < count($liste_tables_del)) and ($flag==0)) {
        if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $flag=1;
        }
        $j++;
    }
    if ($flag != 0){
        echo "<p><b>CAUTION ...</b><br />";
        echo "Data concerning the constitution of the classes and the assignment of the students in the classes are present in base
GEPI ! If you continue the procedure, these data will be definitively erased !</p>";
    }

    echo "<p>Are you sure you want to import all the students since the directory of server LCS towards Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='I am sure'>";
    echo "</form>";
}

require("../lib/footer.inc.php");
?>