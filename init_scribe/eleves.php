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



echo "<p class='bold'><a href='../init_scribe/index.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>";

if (isset($_POST['step'])) {
	check_token(false);

    // L'admin a validé la procédure, on procède donc...
    include "../lib/eole_sync_functions.inc.php";

    // On se connecte au LDAP
    $ldap_server = new LDAPServer;


    //----***** STEP 1 *****-----//

    if ($_POST['step'] == "1") {
        // La première étape consiste à importer les classes

        if ($_POST['record'] == "yes") {
            // Les données ont été postées, on les traite donc immédiatement

            $j=0;
            while ($j < count($liste_tables_del)) {
                if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
                    $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
                }
                $j++;
            }

                // On va enregistrer la liste des classes, ainsi que les périodes qui leur seront attribuées
            $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(description=Classe*)");
            $data = ldap_get_entries($ldap_server->ds,$sr);

            for ($i=0;$i<$data["count"];$i++) {

                $classe = $data[$i]["cn"][0];

                // On enregistre la classe
                // On teste d'abord :
                $test = mysql_result(mysql_query("SELECT count(*) FROM classes WHERE (classe='$classe')"),0);

                if ($test == "0") {
                    //$reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe]))."', format_nom='np'");
                    $reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe])))."', format_nom='np'");
                } else {
                    //$reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe]))."', format_nom='np' WHERE classe='$classe'");
                    $reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe])))."', format_nom='np' WHERE classe='$classe'");
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
                        $nom_per = "Period ".$num;
                        if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                        $register = mysql_query("INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                        if (!$register) echo "<p>Error during recording of a period  for the class $classe";
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
                            if (!$register) echo "<p>Error during recording of a period  for the class $classe";
                        }
                    }
                }
            }

            // On efface les classes qui ne sont pas réutilisées cette année  ainsi que les entrées correspondantes dans les groupes
            $sql = mysql_query("select distinct id_classe from periodes where verouiller='T'");
            $k = 0;
            while ($k < mysql_num_rows($sql)) {
               $id_classe = mysql_result($sql, $k);
               $res1 = mysql_query("delete from classes where id='".$id_classe."'");
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
            echo "<p>You have just carried out the recording of the data concerning the classes. If there were no errors, you can go to the next stage to record the data concerning the students.";
            echo "<center>";
            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=hidden name='record' value='no'>";
            echo "<input type=hidden name='step' value='2'>";
            echo "<input type='submit' value=\"Access to stage 2\">";
            echo "</form>";
            echo "</center>";

			// On sauvegarde le témoin du fait qu'il va falloir
			// convertir pour générer l'ELE_ID et remplir ensuite les nouvelles tables responsables:
			saveSetting("conv_new_resp_table", 0);

        } else {
            // Les données n'ont pas encore été postées, on affiche donc le tableau des classes

            // On commence par "marquer" les classes existantes dans la base
            $sql = mysql_query("UPDATE periodes SET verouiller='T'");

            $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(description=Classe*)");
            $data = ldap_get_entries($ldap_server->ds,$sr);

            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=hidden name='record' value='yes'>";
            echo "<input type=hidden name='step' value='1'>";

            echo "<p>The classes in green indicate already existing classes in base GEPI.<br />The classes in red indicate new classes and which will be added to
base GEPI.<br /></p>";
            echo "<p>For the new classes, standard names are used for the periods (period 1, period 2...), and only the first period is not locked. You will be able to modify these parameters later </p>";
            echo "<p>Caution !!! There are no tests on the entered fields. Be vigilant not to put special characters in the fields ...</p>";
            echo "<p>Try to fill all the fields, that will avoid having to do it later .</p>";
            echo "<p>Do not forget <b>to record the data</b> while clicking on the button in bottom of the page<br /><br />";

            ?>
            <fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;">
            <legend style="font-variant: small-caps;"> Assistance for the filling </legend>
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
            une à une</font> and/or <font color="red">globaly</font> with to the functionalities offered below :</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">1) Initially, notch the lines one per one</td>
            </tr>
              <tr>
              <td colspan="3">&nbsp;</td>
              <td colspan="3">You can also &nbsp;
              <a href="javascript:CocheCase(true)">
              CHECK</a> or
              <a href="javascript:CocheCase(false)">
              UNCHECK</a> all lines , or
              <a href="javascript:InverseSel()">
              REVERSE </a> selection</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">2) Then, for the notched lines :</td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">the name at the bottom of the bulletin will be &nbsp;:&nbsp;</td>
              <td><input type="text" name="nom" maxlength="80" size="40">
              <input type ="button" name="but_nom" value="Recopy"
            onclick="javascript:MetVal('nom')"></td>
             </td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">the formula at the bottom of the bulletin will be
            &nbsp;:&nbsp;</td>
              <td><input type="text" name="pour" maxlength="80" size="40">
              <input type ="button" name="but_pour" value="Recopy"
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
            echo "<tr><td><p class=\"small\"><center>Help<br />Filling</center></p></td><td><p class=\"small\">Identifier of the class</p></td><td><p class=\"small\">Complete name</p></td><td><p class=\"small\">Name appearing at the bottom of the bulletin</p></td><td><p class=\"small\">formulate at the bottom of the bulletin</p></td><td><p class=\"small\">Numbers of periods</p></td></tr>";
            for ($i=0;$i<$data["count"];$i++) {
                $classe_id = $data[$i]["cn"][0];
                $test_classe_exist = mysql_query("SELECT * FROM classes WHERE classe='$classe_id'");
                $nb_test_classe_exist = mysql_num_rows($test_classe_exist);

                if ($nb_test_classe_exist==0) {
                    $nom_complet = $classe_id;
                    $nom_court = "<font color=red>".$classe_id."</font>";
                    $suivi_par = getSettingValue("gepiAdminPrenom")." ".getSettingValue("gepiAdminNom").", ".getSettingValue("gepiAdminFonction");
                    $formule = "";
                    $nb_per = '3';
                } else {
                    $id_classe = mysql_result($test_classe_exist, 0, 'id');
                    $nb_per = mysql_num_rows(mysql_query("select num_periode from periodes where id_classe='$id_classe'"));
                    $nom_court = "<font color=green>".$classe_id."</font>";
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
        // La deuxième étape consiste à importer les élèves et à les affecter dans les classes

        // On créé un tableau avec tous les professeurs principaux de chaque classe

        $classes = mysql_query("SELECT id, classe FROM classes");
        $nb_classes = mysql_num_rows($classes);
        $pp = array();
        for ($i=0;$i<$nb_classes;$i++) {
            $current_classe = mysql_result($classes, $i, "classe");
            $current_classe_id = mysql_result($classes, $i, "id");
            $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(&(objectClass=administrateur)(divcod=" . $current_classe ."))");
            $prof = ldap_get_entries($ldap_server->ds,$sr);
            if (array_key_exists(0, $prof)) {
                $pp[$current_classe_id] = $prof[0]["uid"][0];
            }
        }

        // Debug profs principaux
        //echo "<pre>";
        //print_r($pp);
        //echo "</pre>";

        $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(&(uid=*)(objectClass=Eleves))");
        $info = ldap_get_entries($ldap_server->ds,$sr);

        for($i=0;$i<$info["count"];$i++) {

            // On ajoute l'utilisateur. La fonction s'occupe toute seule de vérifier que
            // le login n'existe pas déjà dans la base. S'il existe, on met simplement à jour
            // les informations

            // function add_eleve($_login, $_nom, $_prenom, $_sexe, $_naissance, $_elenoet) {

            $date_naissance = substr($info[$i]["datenaissance"][0], 0, 4) . "-" .
                                substr($info[$i]["datenaissance"][0], 4, 2) . "-" .
                                substr($info[$i]["datenaissance"][0], 6, 2);

            // -----
            // DEPRECIATION : les lignes ci-dessous ne sont plus nécessaire, Gepi a été mis à jour
            //
            // Pour des raisons de compatibilité avec le code existant de Gepi, il n'est pas possible d'avoir
            // un point dans le login... (le point est transformé bizarrement en "_" dans les $_POST)...

            //$info[$i]["uid"][0] = preg_replace("/\./", "_", $info[$i]["uid"][0]);
			// -----

            // En théorie ici chaque login est de toute façon unique.
            $add = add_eleve($info[$i]["uid"][0],
                            $info[$i]["sn"][0],
                            $info[$i]["givenname"][0],
                            $info[$i]["codecivilite"][0],
                            $date_naissance);
                            //$info[$i]["employeenumber"]);

            $id_classe = mysql_result(mysql_query("SELECT id FROM classes WHERE classe = '" . $info[$i]["divcod"][0] . "'"), 0);

            $check = mysql_result(mysql_query("SELECT count(*) FROM j_eleves_professeurs WHERE (login = '" . $info[$i]["uid"][0] . "')"), 0);
            if ($check > 0) {
                $del = mysql_query("DELETE from j_eleves_professeurs WHERE login = '" . $info[$i]["uid"][0] . "'");
            }
            if (array_key_exists($id_classe, $pp)) {
                //echo "Debug : $pp[$id_classe]<br/>";
                $res = mysql_query("INSERT INTO j_eleves_professeurs SET login = '" . $info[$i]["uid"][0] . "', id_classe = '" . $id_classe . "', professeur = '" . $pp[$id_classe] . "'");
            }

            $get_periode_num = mysql_result(mysql_query("SELECT count(*) FROM periodes WHERE (id_classe = '" . $id_classe . "')"), 0);

            $check = mysql_result(mysql_query("SELECT count(*) FROM j_eleves_classes WHERE (login = '" . $info[$i]["uid"][0] . "')"), 0);
            if ($check > 0) {
                $del = mysql_query("DELETE from j_eleves_classes WHERE login = '" . $info[$i]["uid"][0] . "'");
            }

            for ($k=1;$k<$get_periode_num+1;$k++) {
                $res = mysql_query("INSERT into j_eleves_classes SET login = '" . $info[$i]["uid"][0] . "', id_classe = '" . $id_classe . "', periode = '" . $k . "'");
            }

            echo "<br/>Login student : " . $info[$i]["uid"][0] . "  ---  " . $date_naissance . " --- Classe " . $info[$i]["divcod"][0];
        }

        echo "<p>Operation done.</p>";
        echo "<p>You can check the importation while going on the page of <a href='../eleves/index.php'>management of student</a>.</p>";
        echo "<br />";
        echo "<p><center><a href='professeurs.php'>Next phase : importation of the professors</a></center></p>";
    }

} else {

    echo "<p>The operation of importation of the students from the LDAP of Scribe will carry out the following operations :</p>";
    echo "<ul>";
    echo "<li>Importation of classes</li>";
    echo "<li>Attempt of addition of each students present in the LDAP</li>";
    echo "<li>If the user does not exist, it is created and is directly usable</li>";
    echo "<li>If the user already exists, its basic information is updated and it passes in 'active' state  , becoming directly usable</li>";
    echo "<li> Assignment of the students to classes</li>";
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

    echo "<p>Are you sure you want to import all the students from the directory of the Scribe server towards Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='I am sure'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>