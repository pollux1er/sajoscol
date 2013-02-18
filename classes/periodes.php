<?php
/*
 * $Id: periodes.php 6258 2011-01-01 18:45:39Z crob $
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
extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);
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

if (isset($is_posted) and ($is_posted == "yes")) {
	check_token();

    $msg = '';
    //
    // Insertion et suppresion de périodes
    //
    $pb_reg_per = '';
    $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $nb_periode = mysql_num_rows($periode_query);
    if ($nombre_periode < $nb_periode) {
        $k = $nombre_periode + 1;
        $nb_periode++;
        $autorisation_efface = 'oui';
        while ($k < $nb_periode) {
            $test = mysql_query("SELECT * FROM  j_eleves_classes WHERE (periode = '$k' and id_classe='$id_classe')");
            if (mysql_num_rows($test) !=0) {
                $msg .= "This class contains students for the period $k ! Impossible suppression. You must initially withdraw the students of the class.<br />";
                $autorisation_efface = 'non';
            }
            $k++;
        }
        if ($autorisation_efface == 'oui') {
            $pb_reg_per = 'no';
            $k = $nombre_periode + 1;
            while ($k < $nb_periode) {
                $efface = mysql_query("DELETE FROM periodes WHERE (num_periode = '$k' AND id_classe = '$id_classe')");
               updateOnline("DELETE FROM periodes WHERE (num_periode = '$k' AND id_classe = '$id_classe')");
			   if (!$efface) {$pb_reg_per = 'yes';}
                $test = mysql_query("SELECT login FROM j_eleves_classes WHERE (periode = '$k' AND id_classe = '$id_classe')");
                $nb_ligne = mysql_num_rows($test);
                $j = 0;
                while ($j < $nb_ligne) {
                    $login_eleve = mysql_result($test, $j, 'login');
                    $efface = mysql_query("DELETE FROM j_eleves_groupes WHERE (periode = '$k' AND login = '$login_eleve')");
                    updateOnline("DELETE FROM j_eleves_groupes WHERE (periode = '$k' AND login = '$login_eleve')");
				   if (!$efface) {$pb_reg_per = 'yes';}
                    $j++;
                }

                $efface = mysql_query("DELETE FROM j_eleves_classes WHERE (periode='$k' AND id_classe='$id_classe')");
               updateOnline("DELETE FROM j_eleves_groupes WHERE (periode = '$k' AND login = '$login_eleve')");
			   if (!$efface) {$pb_reg_per = 'yes';}
                $k++;

           }
        }
    } else {
        $pb_reg_per = 'no';
        $k = $nb_periode + 1;
        $nombre_periode++;
        while ($k < $nombre_periode) {
            $register = mysql_query("INSERT INTO periodes SET nom_periode='période ".$k."', num_periode='$k', verouiller = 'N', id_classe='$id_classe'");
            updateOnline("INSERT INTO periodes SET nom_periode='période ".$k."', num_periode='$k', verouiller = 'N', id_classe='$id_classe'");
			if (!$register) {$pb_reg_per = 'yes';}
            $k++;
        }
    }

    //
    // Verrouillage et déverrouillage; changement de noms
    //

   $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $nb_periode = mysql_num_rows($periode_query) + 1 ;
    $k = "1";
    while ($k < $nb_periode) {
        if (!isset($nom_period[$k])) $nom_period[$k] = '';
        $nom_period[$k] = trim($nom_period[$k]);
        if ($nom_period[$k] == '') $nom_period[$k] = "période ".$k;
        //$register = mysql_query("UPDATE periodes SET nom_periode='$nom_period[$k]' WHERE (num_periode='$k' and id_classe='$id_classe')");
        $register = mysql_query("UPDATE periodes SET nom_periode='".html_entity_decode($nom_period[$k])."' WHERE (num_periode='$k' and id_classe='$id_classe')");
        updateOnline("UPDATE periodes SET nom_periode='".html_entity_decode($nom_period[$k])."' WHERE (num_periode='$k' and id_classe='$id_classe')");
		if (!$register) {$pb_reg_per = 'yes';}
        $k++;
    }

   if ($pb_reg_per == 'no')  {
        $msg.="The modifications were recorded!";

    } else if ($pb_reg_per == 'yes') {
        $msg.="There was a problem during the attempt of modification of the number of periods !";
    }

}

$call_data = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_data, 0, "classe");
$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
$test_periode = mysql_num_rows($periode_query) ;
include "../lib/periodes.inc.php";



// =================================
// AJOUT: boireaus
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysql_query($sql);
if(mysql_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;

    $cpt_classe=0;
	$num_classe=-1;

	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}

		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;
	}
}
// =================================

$themessage  = 'Information was modified. Do you really want to leave without saving ?';
//**************** EN-TETE *****************
$titre_page = "Management of the classes - Management of periods";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return </a>\n";

if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Previous class</a>\n";}
if($chaine_options_classes!="") {

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";


	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Next class</a>\n";}

//=========================
// AJOUT: boireaus 20081224
$titre="Navigation";
$texte="";

//$texte.="<img src='../images/icons/date.png' alt='' /> <a href='periodes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Périodes</a><br />";
$texte.="<img src='../images/icons/edit_user.png' alt='' /> <a href='classes_const.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Students</a><br />";
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Courses</a><br />";
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">config.simplified</a><br />";
$texte.="<img src='../images/icons/configure.png' alt='' /> <a href='modify_nom_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Parameters</a>";

$ouvrir_infobulle_nav=getSettingValue("ouvrir_infobulle_nav");

if($ouvrir_infobulle_nav=="y") {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/vert.png' width='16' height='16' /></a></div>\n";
}
else {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/rouge.png' width='16' height='16' /></a></div>\n";
}

$texte.="<script type='text/javascript'>
	// <![CDATA[
	function modif_mode_infobulle_nav() {
		new Ajax.Updater($('save_mode_nav'),'classes_ajax_lib.php?mode=ouvrir_infobulle_nav',{method: 'get'});
	}
	//]]>
</script>\n";

$tabdiv_infobulle[]=creer_div_infobulle('navigation_classe',$titre,"",$texte,"",14,0,'y','y','n','n');

echo " | <a href='#' onclick=\"afficher_div('navigation_classe','y',-100,20);\"";
echo ">";
echo "Navigation";
echo "</a>";
//=========================

echo "</p>\n";
echo "</form>\n";

?>

<form enctype="multipart/form-data" method="post" action="periodes.php">
<center><input type='submit' value='Enregistrer' /></center>
<p class='bold'>Class : <?php echo $classe; ?></p>
<p><b>Notice : </b>The Locking/unlocking from a period  is possible while being connected under an account having the
statute "schooling".</p>

<?php

echo add_token_field();

echo "<p>Number of periods : ";

//$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_classe='$id_classe';";
$sql="SELECT 1=1 FROM j_groupes_classes jgc, j_eleves_groupes jeg WHERE jgc.id_classe='$id_classe' AND jeg.id_groupe=jgc.id_groupe;";
$verif=mysql_query($sql);
if(mysql_num_rows($verif)>0) {
	$temp = $nb_periode - 1;
	echo "<b>".$temp."</b>";
	echo "<input type='hidden' name='nombre_periode' value='$temp' />\n";
	echo "<br />\n";
	echo "<a href='ajouter_periode.php?id_classe=$id_classe'>Add</a> / <a href='supprimer_periode.php?id_classe=$id_classe'>Delete</a> periods<br />\n";
}
else {
	echo "<select size=1 name='nombre_periode'";
	echo " onchange='changement()'";
	echo ">\n";

	$temp = $nb_periode - 1;
	$i = "0" ;
	while ($i < '7') {
		echo "<option value=$i "; if ($i == $temp) {echo " selected";} echo ">$i</option>\n";
		$i++;
	}
	echo "</select>\n";
}
echo "</p>\n";

if ($test_periode == 0) {
	echo "<p>If you choose not to define periods for this class (number of periods = 0), this class will be regarded as virtual.</p>\n";
	echo "<p>Remarks : </p>\n";
	echo "<ul><li>You can assign one or more courses to a virtual class.</li>\n";
	echo "<li>You cannot assign students to a virtual class.</li>\n";
	echo "<li>A virtual class can be used within the framework of the textbooks : creation of a rubric accessible to the public and filled by a professor of a course assigned to this class.</li>\n";
	echo "</ul>\n";

} else {
?>
    <!--center-->
    <!--table width=100% border=2 cellspacing=1 bordercolor=#330033 cellpadding=3-->
    <table class='boireaus'>
    <tr>
    <th>&nbsp;</th>
    <th style='padding: 5px;'>Name of the period</th>
    </tr>
    <?php
    $k = '1';
	$alt=1;
    while ($k < $nb_periode) {
        if ($nom_periode[$k] == '') {$nom_periode[$k] = "période ".$k;}
        $alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
        echo "<td style='padding: 5px;'>Période $k</td>\n";
        echo "<td style='padding: 5px;'><input type='text' name='nom_period[$k]'";
		echo " onchange='changement()'";
		echo " value=\"".$nom_periode[$k]."\" size='30' /></td>\n";
        echo "</tr>\n";
        $k++;
    }
    ?>
    </table>
    <!--/center-->
<?php } ?>
<center><input type='submit' value='Enregistrer' style='margin: 30px 0 30px 0;'/></center>
<input type='hidden' name='is_posted' value="yes" />
<input type='hidden' name='id_classe' value='<?php echo $id_classe; ?>' />
</form>
<?php

if($ouvrir_infobulle_nav=='y') {
	echo "<script type='text/javascript'>
	setTimeout(\"afficher_div('navigation_classe','y',-100,20);\",1000)
</script>\n";
}

require("../lib/footer.inc.php");

?>
