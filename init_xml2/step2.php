<?php
@set_time_limit(0);
/*
 * $Id: step2.php 7858 2011-08-21 13:12:55Z crob $
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


//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year : Importation of the students - Stage 2";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On vérifie si l'extension d_base est active
//verif_active_dbase();

?>
<script type="text/javascript" language="JavaScript">
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
echo "<center><h3 class='gepi'>First phase of initialization<br />Importation of the students,  constitution of the classes and assignment of the students in the
classes</h3></center>";
echo "<center><h3 class='gepi'>Second stage : Recording of the classes</h3></center>";

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_eleves;

if (!isset($step2)) {
    $j=0;
    $flag=0;
	$chaine_tables="";
    while (($j < count($liste_tables_del)) and ($flag==0)) {
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1){
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

    if ($flag != 0){
        echo "<p><b>CAUTION ...</b><br />\n";
        echo "Data concerning the constitution of the classes and the assignment of the students in the classes are present in base
GEPI ! If you continue the procedure, these data will be definitively erased !</p>\n";

		echo "<p>The emptied tables will be&nbsp;: $chaine_tables</p>\n";

        echo "<form enctype='multipart/form-data' action='step2.php' method='post'>\n";
		echo add_token_field();
        echo "<input type=hidden name='step2' value='y' />\n";
        echo "<input type='submit' value='Continue the procedure' />\n";
        echo "</form>\n";
		require("../lib/footer.inc.php");
        die();
    }
}

check_token(false);

if (isset($is_posted)) {
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

	// Suppression des comptes d'élèves:
	$sql="DELETE FROM utilisateurs WHERE statut='eleve';";
	$del=mysql_query($sql);

	// Liste des comptes scolarité pour associer aux nouvelles classes
	$sql="SELECT login FROM utilisateurs WHERE statut='scolarite';";
	$res_scol=mysql_query($sql);
	$tab_user_scol=array();
	if(mysql_num_rows($res_scol)>0) {
		while($lig_scol=mysql_fetch_object($res_scol)) {$tab_user_scol[]=$lig_scol->login;}
	}

    // On va enregistrer la liste des classes, ainsi que les périodes qui leur seront attribuées
    //$call_data = mysql_query("SELECT distinct(DIVCOD) classe FROM temp_gep_import WHERE DIVCOD!='' ORDER BY DIVCOD");
    $call_data = mysql_query("SELECT distinct(DIVCOD) classe FROM temp_gep_import2 WHERE DIVCOD!='' ORDER BY DIVCOD");
    $nb = mysql_num_rows($call_data);
    $i = "0";

    while ($i < $nb) {
        $classe = mysql_result($call_data, $i, "classe");
        // On enregistre la classe
        // On teste d'abord :
        $test = mysql_result(mysql_query("SELECT count(*) FROM classes WHERE (classe='$classe')"),0);
        if ($test == "0") {
            //$reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($reg_formule[$classe]))."', format_nom='np'");
            //$reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($reg_formule[$classe])))."', format_nom='np'");
            $reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($reg_formule[$classe])))."', format_nom='cni'");

			$id_classe=mysql_insert_id();
			for($loop=0;$loop<count($tab_user_scol);$loop++) {
				// TEST déjà assoc... cela peut arriver si des scories subsistent...
				$sql="SELECT 1=1 FROM j_scol_classes WHERE login='$tab_user_scol[$loop]' AND id_classe='$id_classe';";
				$test_j_scol_class=mysql_query($sql);
				if(mysql_num_rows($test_j_scol_class)==0) {
					//$tab_user_scol
					$sql="INSERT INTO j_scol_classes SET login='$tab_user_scol[$loop]', id_classe='$id_classe';";
					$insert_j_scol_class=mysql_query($sql);
				}
			}

        } else {
            //$reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($reg_formule[$classe]))."', format_nom='np' WHERE classe='$classe'");
            //$reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($reg_formule[$classe])))."', format_nom='np' WHERE classe='$classe'");
            $reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($reg_formule[$classe])))."', format_nom='cni' WHERE classe='$classe'");
        }
        if (!$reg_classe) echo "<p>Error during recording of the class $classe.";

        // On enregistre les périodes pour cette classe
        // On teste d'abord :
        $id_classe = mysql_result(mysql_query("select id from classes where classe='$classe'"),0,'id');
        $test = mysql_result(mysql_query("SELECT count(*) FROM periodes WHERE (id_classe='$id_classe')"),0);
        if ($test == "0") {
            $j = '0';
            while ($j < $reg_periodes_num[$classe]) {
                $num = $j+1;
                $nom_per = "Période ".$num;
                if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                $register = mysql_query("INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                if (!$register) echo "<p>Error during recording a period for the class $classe";
                $j++;
            }
        } else {
            // on "démarque" les périodes des classes qui ne sont pas à supprimer
            $sql = mysql_query("UPDATE periodes SET verouiller='N' where (id_classe='$id_classe' and num_periode='1')");
            $sql = mysql_query("UPDATE periodes SET verouiller='O' where (id_classe='$id_classe' and num_periode!='1')");
            //
            $nb_per = mysql_num_rows(mysql_query("select num_periode from periodes where id_classe='$id_classe'"));
            if ($nb_per > $reg_periodes_num[$classe]) {
                // Le nombre de périodes de la classe est inférieur au nombre enregistré
                // On efface les périodes en trop
                $k = 0;
                for ($k=$reg_periodes_num[$classe]+1; $k<$nb_per+1; $k++) {
                    $del = mysql_query("delete from periodes where (id_classe='$id_classe' and num_periode='$k')");
                }
            }
            if ($nb_per < $reg_periodes_num[$classe]) {

                // Le nombre de périodes de la classe est supérieur au nombre enregistré
                // On enregistre les périodes
                $k = 0;
                $num = $nb_per;
                for ($k=$nb_per+1 ; $k < $reg_periodes_num[$classe]+1; $k++) {
                    $num++;
                    $nom_per = "Période ".$num;
                    if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                    $register = mysql_query("INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                    if (!$register) echo "<p>Error during recording a period for the class $classe";
                }
            }
        }

        $i++;
    }
    // On efface les classes qui ne sont pas réutilisées cette année  ainsi que les entrées correspondantes dans  j_groupes_classes
    $sql = mysql_query("select distinct id_classe from periodes where verouiller='T'");
    $k = 0;
    while ($k < mysql_num_rows($sql)) {
       $id_classe = mysql_result($sql, $k);
       $res1 = mysql_query("delete from classes where id='".$id_classe."'");
       $res2 = mysql_query("delete from j_groupes_classes where id_classe='".$id_classe."'");
       $k++;
    }
    // On supprime les groupes qui n'ont plus aucune affectation de classe
    $res = mysql_query("delete from groupes g, j_groupes_classes jgc, j_eleves_groupes jeg, j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (" .
    		"g.id != jgc.id_groupe and jeg.id_groupe != jgc.id_groupe and jgp.id_groupe != jgc.id_groupe and jgm.id_groupe != jgc.id_groupe)");

    $res = mysql_query("delete from periodes where verouiller='T'");
    echo "<p>You have just carried out the recording of the data concerning the
classes. If there were no errors, you can go to the next stage to record the data concerning the students.";
    echo "<center><p><a href='step3.php'>Access to the stage 3</a></p></center>";
} else {
    // On commence par "marquer" les classes existantes dans la base
    $sql = mysql_query("UPDATE periodes SET verouiller='T'");
    //
    //$call_data = mysql_query("SELECT distinct(DIVCOD) classe FROM temp_gep_import WHERE DIVCOD!='' ORDER BY DIVCOD");
    $call_data = mysql_query("SELECT distinct(DIVCOD) classe FROM temp_gep_import2 WHERE DIVCOD!='' ORDER BY DIVCOD");
    $nb = mysql_num_rows($call_data);
    $i = "0";
    echo "<form enctype='multipart/form-data' action='step2.php' method=post name='formulaire'>";
	echo add_token_field();
    echo "<input type=hidden name='is_posted' value='yes' />";
    echo "<p>The classes in green indicate already existing classes in base GEPI.<br />The classes in red indicate new classes and which will be added to base GEPI.<br /></p>";
    echo "<p>For the new classes, standard names are used for the periods (period 1, period 2...), and only the first period is not locked. You will be able to modify these parameters later on</p>";
    echo "<p>Caution !!! There are no tests on the entered fields. Be vigilant not to put special characters in the fields ...</p>";
    echo "<p> Try to fill all the fields, that will avoid having to do it later .</p>";
    echo "<p>Do not forget <b>to record the data</b> by clicking on the button in bottom of the page<br /><br />";
?>
<fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;">
<legend style="font-variant: small-caps;"> Help to the filling </legend>
<table border="0" summary='Tableau de remplissage'>
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
individually</font> and/or <font color="red">globaly</font> with the functionalities offered below :</td>
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
  UNCHECK</a> all lines, or
  <a href="javascript:InverseSel()">
  REVERSE </a> selection</td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td>
  <td colspan="4">2) Then, for the notched lines :</td>
</tr>
 <tr>
  <td colspan="4">&nbsp;</td>
  <td align="right">the name in the bottom of the bulletin will be &nbsp;:&nbsp;</td>
  <td>
    <input type="text" name="nom" maxlength="80" size="40" />
  <input type ="button" name="but_nom" value="Recopy"
onclick="javascript:MetVal('nom')" />
 </td>
</tr>
 <tr>
  <td colspan="4">&nbsp;</td>
  <td align="right">the formula in the bottom of the bulletin will be
&nbsp;:&nbsp;</td>
  <td><input type="text" name="pour" maxlength="80" size="40" />
  <input type ="button" name="but_pour" value="Recopy"
onclick="javascript:MetVal('pour')" />
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

    echo "<table border=1 class='boireaus' cellpadding='2' cellspacing='2' summary='Table of the classes'>";
    echo "<tr>
<th><p class=\"small\" align=\"center\">Help<br />Filling</p></th>
<th><p class=\"small\">Identifier of the class</p></th>
<th><p class=\"small\">Complete name</p></th>
<th><p class=\"small\">Name appearing at the bottom of the bulletin</p></th>
<th><p class=\"small\">formulate at the bottom of the bulletin</p></th>
<th><p class=\"small\">Numbers of periods</p></th></tr>\n";
	$num_id1=1;
	$num_id2=$nb+1;
	$num_id3=2*$nb+1;
	$alt=1;
    while ($i < $nb) {
		$alt=$alt*(-1);
        $classe_id = mysql_result($call_data, $i, "classe");
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
        echo "<tr class='lig$alt'>\n";
        echo "<td><center><input type=\"checkbox\" /></center></td>\n";
        echo "<td>\n";
        echo "<p align='center'><b>$nom_court</b></p>\n";
        //echo "";
        echo "</td>\n";
        echo "<td>\n";
        echo "<input type=text id=\"n".$num_id1."\" onKeyDown=\"clavier(this.id,event);\" name='reg_nom_complet[$classe_id]' value=\"".$nom_complet."\" /> \n";
        echo "</td>\n";
        echo "<td>\n";
        echo "<input type=text id=\"n".$num_id2."\" onKeyDown=\"clavier(this.id,event);\" name='reg_suivi[$classe_id]' value=\"".$suivi_par."\" />\n";
        echo "</td>\n";
        echo "<td>\n";
        echo "<input type=text id=\"n".$num_id3."\" onKeyDown=\"clavier(this.id,event);\" name='reg_formule[$classe_id]' value=\"".$formule."\" />\n";
        echo "</td>\n";
        echo "<td>\n";
        echo "<select size=1 name='reg_periodes_num[$classe_id]'>\n";
        for ($k=1;$k<7;$k++) {
            echo "<option value='$k'";
            if ($nb_per == "$k") echo " SELECTED";
            echo ">$k</option>\n";
        }
        echo "</select>\n";
        echo "</td></tr>\n";
        $i++;
		$num_id1++;
		$num_id2++;
		$num_id3++;
    }
    echo "</table>\n";
    echo "<input type=hidden name='step2' value='y' />\n";
    echo "<p align='center'><input type='submit' value='Save the data' /></p>\n";
    echo "</form>\n";
}

?>
<p><em>Notice on the periods&nbsp;:</em></p>
<blockquote>
	<p>The number of periods must correspond to the number of bulletins which will be published for each student over the year.<br />
	In college for example, one will type three periods (<em>quarters</em>).<br />
	That will not prevent an edit six report booklets by student during the year if you wish report booklets of half-period.<br />
	It would not be on the other hand possible of edit a bulletin fusion two periods.</p>
</blockquote>
<p><br /></p>
</div>
</body>
</html>