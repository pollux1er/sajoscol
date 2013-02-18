<?php
/*
* $Id: edit_class.php 8444 2011-10-07 05:25:51Z crob $
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
//require("../calculnotes/functions.php");
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

$msg="";

$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
if (!is_numeric($id_classe)) {$id_classe = 0;}
$classe = get_classe($id_classe);

if(isset($_GET['forcer_recalcul_rang'])) {
	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode DESC LIMIT 1;";
	$res_per=mysql_query($sql);
	if(mysql_num_rows($res_per)>0) {
		$lig_per=mysql_fetch_object($res_per);
		$recalcul_rang="";
		for($i=0;$i<$lig_per->num_periode;$i++) {$recalcul_rang.="y";}
		$sql="UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		updateOnline($sql);
		if(!$res) {
			$msg="Error during programming of the recalculation of the rows for this class.";
		}
		else {
			$msg="Recalculation of the rows programmed for this class.";
		}
	}
	else {
		$msg="No period is defined for this class.<br />Impossible recalculation of the rows for this class.";
	}
}

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
}// =================================

$priority_defaut = 5;

//================================
// Liste de domaines à déplacer par la suite dans global.inc ?
/*
$tab_domaines=array('bulletins', 'cahier_notes', 'absences', 'cahier_textes', 'edt');
$tab_domaines_sigle=array('B', 'CN', 'Abs', 'CDT', 'EDT');
$tab_domaines_texte=array('Report cards', 'Cahiers de Notes', 'Absences', 'Cahiers De Textes', 'EDT');
*/
$tab_domaines=array('bulletins', 'cahier_notes');
$tab_domaines_sigle=array('B', 'CN');
$tab_domaines_texte=array('Report cards', 'Cahiers de Notes');
//================================
$invisibilite_groupe=array();
for($loop=0;$loop<count($tab_domaines);$loop++) {
	$invisibilite_groupe[$tab_domaines[$loop]]=array();
}
$sql="SELECT jgv.* FROM j_groupes_classes jgc, j_groupes_visibilite jgv WHERE jgv.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND jgv.visible='n';";
$res_jgv=mysql_query($sql);
if(mysql_num_rows($res_jgv)>0) {
	while($lig_jgv=mysql_fetch_object($res_jgv)) {
		$invisibilite_groupe[$lig_jgv->domaine][]=$lig_jgv->id_groupe;
	}
}
//================================

if (isset($_POST['is_posted'])) {
	check_token();

    $error = false;

	$tab_id_groupe=array();

    foreach ($_POST as $key => $value) {
        $pattern = "/^priorite\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
			$tab_id_groupe[]=$group_id;
            $options[$group_id]["priorite"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^coef\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["coef"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        //$pattern = "/^note\_sup\_10\_/";
        $pattern = "/^mode\_moy\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            //$options[$group_id]["mode_moy"] = "sup10";
            $options[$group_id]["mode_moy"] = $value;
			//echo "mode_moy pour $group_id : $value<br />";
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^no_saisie_ects\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["saisie_ects"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^saisie_ects\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["saisie_ects"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^valeur_ects\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["valeur_ects"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^categorie\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["categorie_id"] = $value;
        }
    }

    foreach ($options as $key => $value) {
        // Toutes les vérifications de sécurité sont faites dans la fonction
        $update = update_group_class_options($key, $id_classe, $value);
    }

	for($loo=0;$loo<count($tab_domaines);$loo++) {
		/*
		echo "<p>\$tab_domaines[$loo]=$tab_domaines[$loo]<br />";
		foreach($invisibilite_groupe[$tab_domaines[$loo]] as $key => $value) {
			echo "\$invisibilite_groupe[$tab_domaines[$loo]][$key]=$value<br />";
		}
		*/
		unset($visibilite_groupe_domaine_courant);
		$visibilite_groupe_domaine_courant=isset($_POST['visibilite_groupe_'.$tab_domaines[$loo]]) ? $_POST['visibilite_groupe_'.$tab_domaines[$loo]] : array();
		/*
		foreach($visibilite_groupe_domaine_courant as $key => $value) {
			echo "\$visibilite_groupe_domaine_courant[$key]=$value<br />";
		}
		*/
		for($loop=0;$loop<count($tab_id_groupe);$loop++) {
			//echo "\$tab_id_groupe[$loop]=$tab_id_groupe[$loop]<br />";
			if(in_array($tab_id_groupe[$loop], $invisibilite_groupe[$tab_domaines[$loo]])) {
				if(in_array($tab_id_groupe[$loop], $visibilite_groupe_domaine_courant)) {
					$sql="DELETE FROM j_groupes_visibilite WHERE id_groupe='".$tab_id_groupe[$loop]."' AND domaine='".$tab_domaines[$loo]."';";
					//echo "$sql<br />";
					$suppr=mysql_query($sql);
					updateOnline($sql);
					if(!$suppr) {$msg.="Error during suppression of the invisibility of the group n°".$tab_id_groupe[$loop]." on the ".$tab_domaines_texte[$loo].".<br />";}
				}
			}
			else {
				if(!in_array($tab_id_groupe[$loop], $visibilite_groupe_domaine_courant)) {
					$sql="INSERT j_groupes_visibilite SET id_groupe='".$tab_id_groupe[$loop]."', domaine='".$tab_domaines[$loo]."', visible='n';";
					//echo "$sql<br />";
					$insert=mysql_query($sql);
					updateOnline($sql);
					if(!$insert) {$msg.="Error during recording of the invisibility of the group n°".$tab_id_groupe[$loop]." on the ".$tab_domaines_texte[$loo].".<br />";}
				}
			}

		}
	}

	//================================
	$invisibilite_groupe=array();
	for($loop=0;$loop<count($tab_domaines);$loop++) {
		$invisibilite_groupe[$tab_domaines[$loop]]=array();
	}
	$sql="SELECT jgv.* FROM j_groupes_classes jgc, j_groupes_visibilite jgv WHERE jgv.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND jgv.visible='n';";
	$res_jgv=mysql_query($sql);
	if(mysql_num_rows($res_jgv)>0) {
		while($lig_jgv=mysql_fetch_object($res_jgv)) {
			$invisibilite_groupe[$lig_jgv->domaine][]=$lig_jgv->id_groupe;
		}
	}
	//================================

	$msg="Enregistrement effectué.";

}

if (isset($_GET['action'])) {
	check_token();

    $msg = null;
    //if ($_GET['action'] == "delete_group") {
    if(($_GET['action'] == "delete_group")&&(isset($_GET['confirm_delete_group']))&&($_GET['confirm_delete_group'] == "y")) {
        if (!is_numeric($_GET['id_groupe'])) $_GET['id_groupe'] = 0;
        $verify = test_before_group_deletion($_GET['id_groupe']);
        if ($verify) {
            //================================
            // MODIF: boireaus
            $sql="SELECT * FROM groupes WHERE id='".$_GET['id_groupe']."'";
            $req_grp=mysql_query($sql);
            $ligne_grp=mysql_fetch_object($req_grp);
            //================================
            $delete = delete_group($_GET['id_groupe']);
            if ($delete == true) {
                //================================
                // MODIF: boireaus
                //$msg .= "Le groupe " . $_GET['id_groupe'] . " a été supprimé.";

                //$sql="SELECT * FROM groupes WHERE id='".$_GET['id_groupe']."'";
                //$req_grp=mysql_query($sql);
                //$ligne_grp=mysql_fetch_object($req_grp);
                // Le groupe n'existe déjà plus
                $msg .= "The group $ligne_grp->name (" . $_GET['id_groupe'] . ") was removed.";
                //================================
            } else {
                $msg .= "An error prevented the suppression of the group.";
            }
        } else {
            $msg .= "Existing data block the suppression of the group. No note nor appreciation of the bulletin must have been typed for the students of this group to allow the suppression of the group.";
        }
    }
}

$themessage  = 'Information was modified. Do you really want to leave without recording ?';
//**************** EN-TETE **************************************
//$titre_page = "Gestion des groupes";
$titre_page = "Management of the courses";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

//debug_var();

if((isset($_GET['action']))&&($_GET['action']=="delete_group")&&(!isset($_GET['confirm_delete_group']))) {
	check_token(false);

	// On va détailler ce qui serait supprimé en cas de confirmation
	$tmp_group=get_group($_GET['id_groupe']);
	echo "<div style='border: 2px solid red;'>\n";
	echo "<p><b>CAUTION&nbsp;:</b> You want to remove the following course&nbsp;: ".$tmp_group['name']." (<i>".$tmp_group['description']."</i>) en ".$tmp_group['classlist_string']."<br />\n";
	echo "Here some elements on the course&nbsp;:</p>\n";
	$suppression_possible='y';

	$lien_bull_simp="";
	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode DESC LIMIT 1;";
	//echo "$sql<br />";
	$res_per=mysql_query($sql);
	if(mysql_num_rows($res_per)>0) {
		$lig_per=mysql_fetch_object($res_per);

		$lien_bull_simp="<a href='../prepa_conseil/edit_limite.php?choix_edit=1&amp;id_classe=$id_classe&amp;periode1=1&amp;periode2=$lig_per->num_periode' target='_blank'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='Simple bulletin in a new page' title='Simple bulletin in a new page' /></a>";
	}

	echo "<p style='margin-left:5em;'>";
	$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_mn=mysql_query($sql);
	$nb_mn=mysql_num_rows($test_mn);
	if($nb_mn==0) {
		echo "No note on the bulletins.<br />\n";
	}
	else {
		echo "<span style='color:red;'>$nb_mn note(s) on the bulletins</span> (<i>for all periods</i>)&nbsp;: $lien_bull_simp<br />\n";
		$suppression_possible='n';
	}

	$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_ma=mysql_query($sql);
	$nb_ma=mysql_num_rows($test_ma);
	if($nb_ma==0) {
		echo "No appreciation on the bulletins.<br />\n";
	}
	else {
		echo "<span style='color:red;'>$nb_ma appreciation(s) on the bulletins</span> (<i>for all periods</i>)&nbsp;: $lien_bull_simp<br />\n";
		$suppression_possible='n';
	}

	$temoin_non_vide='n';
	// CDT
	$sql="SELECT 1=1 FROM ct_entry WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_notice_cdt=mysql_query($sql);
	$nb_notice_cdt=mysql_num_rows($test_notice_cdt);
	if($nb_notice_cdt==0) {
		echo "No notice in the textbook.<br />\n";
	}
	else {
		echo "$nb_notice_cdt notice(s) in the textbook.<br />\n";
		$temoin_non_vide='y';
	}

	$sql="SELECT 1=1 FROM ct_devoirs_entry WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_devoir_cdt=mysql_query($sql);
	$nb_devoir_cdt=mysql_num_rows($test_devoir_cdt);
	if($nb_devoir_cdt==0) {
		echo "No exam in the textbook.<br />\n";
	}
	else {
		echo "$nb_devoir_cdt exam(s) in the textbook.<br />\n";
		$temoin_non_vide='y';
	}

	// NOTES
	// Récupérer les cahier de notes
	$sql="SELECT DISTINCT id_cahier_notes, periode FROM cn_cahier_notes WHERE id_groupe='".$_GET['id_groupe']."' ORDER BY periode;";
	$res_ccn=mysql_query($sql);
	if(mysql_num_rows($res_ccn)==0) {
		echo "No book of notes is initialized for this course.<br />\n";
	}
	else {
		while($lig_id_cn=mysql_fetch_object($res_ccn)) {
			$sql="SELECT 1=1 FROM cn_devoirs WHERE id_racine='$lig_id_cn->id_cahier_notes';";
			$res_dev=mysql_query($sql);
			$nb_dev=mysql_num_rows($res_dev);
			if($nb_dev==0) {
				echo "Period $lig_id_cn->periode&nbsp;: No exam.<br />\n";
			}
			else {
				echo "Period $lig_id_cn->periode&nbsp;: $nb_dev exam(s) in the report card.<br />\n";
				$temoin_non_vide='y';
			}
		}
	}
	echo "</p>\n";

	if($suppression_possible=='y') {
		if($temoin_non_vide=='y') {
			echo "<p>If you want to make ";
			echo "despite everything ";
			echo "the suppression of the course&nbsp;: ";
			echo "<a href='edit_class.php?id_groupe=".$_GET['id_groupe']."&amp;action=delete_group&amp;confirm_delete_group=y&amp;id_classe=$id_classe".add_token_in_url()."' onclick=\"return confirmlink(this, 'CAUTION !!! The course is not completely empty, even if the bulletins do not
contain a reference to this course.\\nAre you REALLY SURE you want to continue ?', 'Confirmation of the suppression')\">Delete</a>";
			echo "</p>\n";
		}
		else {
			echo "<p>If you want to confirm the suppression of the course&nbsp;: ";
			echo "<a href='edit_class.php?id_groupe=".$_GET['id_groupe']."&amp;action=delete_group&amp;confirm_delete_group=y&amp;id_classe=$id_classe".add_token_in_url()."'>Delete</a>";
			echo "</p>\n";
		}
	}
	else {
		echo "<p style='color:red;'>Existing data block the suppression of the group.<br />No note nor appreciation of the bulletin must have been typed for the students of this group to allow the suppression of the group.</p>\n";
	}
	echo "</div>\n";
}


$display_mat_cat="n";
$sql="SELECT display_mat_cat FROM classes WHERE id='$id_classe';";
$res_display_mat_cat=mysql_query($sql);
if(mysql_num_rows($res_display_mat_cat)>0) {
	$lig_display_mat_cat=mysql_fetch_object($res_display_mat_cat);
	$display_mat_cat=$lig_display_mat_cat->display_mat_cat;

	$url_wiki="#";
	$sql="SELECT * FROM ref_wiki WHERE ref='enseignement_invisible';";
	$res_ref_wiki=mysql_query($sql);
	if(mysql_num_rows($res_ref_wiki)>0) {
		$lig_wiki=mysql_fetch_object($res_ref_wiki);
		$url_wiki=$lig_wiki->url;
	}
	$titre="Invisible course";
	$texte="<p>This course will not appear on the bulletins nor on the report booklets.<br />";
	$texte.="See <a href='$url_wiki' target='_blank'>Invisible course on the bulletins and report booklets</a>.<br />";
	$tabdiv_infobulle[]=creer_div_infobulle('enseignement_invisible',$titre,"",$texte,"",25,0,'y','y','n','n');
}
else {
	echo "<p style='color:red;'>Anomaly&nbsp;: Infos concerning 'display_mat_cat' could not be recovered for this class.</p>\n";
}

echo "<table border='0' summary='Menu'><tr>\n";
echo "<td width='40%' align='left'>\n";
echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
echo "<p class='bold'>\n";
echo "<a href='../classes/index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>";
//if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>";}
if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">prev. Class</a>";}
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
//if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Next Class</a>";}

//=========================
// AJOUT: boireaus 20081224
$titre="Navigation";
$texte="";
$texte.="<img src='../images/icons/date.png' alt='' /> <a href='../classes/periodes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Periods</a><br />";
$texte.="<img src='../images/icons/edit_user.png' alt='' /> <a href='../classes/classes_const.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Students</a><br />";
//$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignements</a><br />";
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">simplified config.</a><br />";
$texte.="<img src='../images/icons/configure.png' alt='' /> <a href='../classes/modify_nom_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Parameters</a>";

$ouvrir_infobulle_nav=getSettingValue("ouvrir_infobulle_nav");
//echo "\$ouvrir_infobulle_nav=$ouvrir_infobulle_nav<br />";

if($ouvrir_infobulle_nav=="y") {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/vert.png' width='16' height='16' alt='Yes' /></a></div>\n";
}
else {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/rouge.png' width='16' height='16' alt='No' /></a></div>\n";
}

$texte.="<script type='text/javascript'>
	// <![CDATA[
	function modif_mode_infobulle_nav() {
		new Ajax.Updater($('save_mode_nav'),'../classes/classes_ajax_lib.php?mode=ouvrir_infobulle_nav',{method: 'get'});
	}
	//]]>
</script>\n";

$tabdiv_infobulle[]=creer_div_infobulle('navigation_classe',$titre,"",$texte,"",14,0,'y','y','n','n');

echo " | <a href='#' onclick=\"afficher_div('navigation_classe','y',-100,20);\"";
echo ">";
echo "Navigation";
echo "</a>";
//=========================

echo " | <a href='menage_eleves_groupes.php?id_classe=$id_classe'>Deregistration by batches</a>";

echo " | <a href='../groupes/repartition_ele_grp.php'>Distribute students into several groups</a>";

echo "</p>\n";
echo "</form>\n";


echo "<h3>Management of the courses for the class :" . $classe["classe"]."</h3>\n";

echo "</td>\n";
echo "<td width='60%' align='center'>\n";
echo "<form enctype='multipart/form-data' action='add_group.php' name='new_group' method='get'>\n";
//==============================
// MODIF: boireaus
//echo "<p>Ajouter un enseignement : ";
//$query = mysql_query("SELECT matiere, nom_complet FROM matieres");
echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";
echo "<table border='0' summary='Addition of course'>\n<tr valign='top'>\n<td>\n";
echo "Add a course : ";
echo "</td>\n";
$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
//==============================
$nb_mat = mysql_num_rows($query);

echo "<td>\n";
echo "<select name='matiere' size='1'>\n";
echo "<option value='null'>-- Select course --</option>\n";
for ($i=0;$i<$nb_mat;$i++) {
    $matiere = mysql_result($query, $i, "matiere");
    $nom_matiere = mysql_result($query, $i, "nom_complet");
    //echo "<option value='" . $matiere . "'";
    echo "<option value='" . $matiere . "'";
    echo ">" . htmlentities($nom_matiere) . "</option>\n";
}
echo "</select>\n";
echo "</td>\n";
echo "<td>\n";
echo "&nbsp;in&nbsp;";
echo "</td>\n";
//==============================
// MODIF: boireaus
/*
echo "<select name='mode' size='1'>";
echo "<option value='null'>-- Sélectionner mode --</option>";
echo "<option value='groupe' selected>cette classe seulement (" . $classe["classe"] .")</option>";
echo "<option value='regroupement'>plusieurs classes</option>";
echo "</select>";
*/
echo "<td>\n";
echo "<input type='radio' name='mode' id='mode_groupe' value='groupe' checked /><label for='mode_groupe' style='cursor: pointer;'> this class only (" . $classe["classe"] .")</label><br />\n";
echo "<input type='radio' name='mode' id='mode_regroupement' value='regroupement' /><label for='mode_regroupement' style='cursor: pointer;'> several classes</label>\n";
echo "</td>\n";
echo "</tr>\n</table>\n";
//==============================

echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />\n";
echo "<input type='submit' value='Create' />\n";
echo "</fieldset>\n";
echo "</form>\n";
echo "</td>\n</tr>\n</table>\n";

//$groups = get_groups_for_class($id_classe);
$groups = get_groups_for_class($id_classe,"","n");
if(count($groups)==0){

	if($ouvrir_infobulle_nav=='y') {
		echo "<script type='text/javascript'>
		setTimeout(\"afficher_div('navigation_classe','y',-100,20);\",1000)
	</script>\n";
	}
	
	require("../lib/footer.inc.php");

    //echo "</body></html>\n";
    die();
}
?>
<form enctype="multipart/form-data" action="edit_class.php" name="formulaire" method="post">
<?php
echo add_token_field();
?>
<!--form enctype="multipart/form-data" action="edit_class.php" name="formulaire" id="form_mat" method=post-->

<!--p>Définir les priorités d'après <input type='button' value="l'ordre alphabétique" onClick="ordre_alpha();" /> / <input type='button' value="l'ordre par défaut des matières" onClick="ordre_defaut();" /><br /-->
<!--table border='0' width='100%'><tr align='center'><td width='30%'>&nbsp;</td><td width='30%'>Afficher les matières dans l'ordre <a href='javascript:ordre_alpha();'>alphabétique</a> ou <a href='javascript:ordre_defaut();'>des priorités</a>.</td>
<td width='30%'>Mettre tous les coefficients à <select name='coefficient_recop' id='coefficient_recopie'-->
<!--table border='0' width='100%'><tr align='center'><td>Afficher les matières dans l'ordre <a href='javascript:ordre_alpha();'>alphabétique</a> ou <a href='javascript:ordre_defaut();'>des priorités</a>.</td-->

<table border='0' width='100%' summary='Parameters'>
<tr align='center'>
<td width='40%'>
<fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;">
<p>For this class,
<input type='button' value="regulate the priorities of display" onClick='choix_ordre();' />:</p>
<!--ul>
<li><a href='javascript:ordre_defaut();'>égales aux valeurs définies par défaut</a>,</li>
<li><a href='javascript:ordre_alpha();'>suivant l'ordre alphabétique des matières.</a></li>
</ul-->
<input type='radio' name='ordre' id='ordre_defaut' value='ordre_defaut' /><label for='ordre_defaut' style='cursor: pointer;'> equal to the values defined by default,</label><br />
<input type='radio' name='ordre' id='ordre_alpha' value='ordre_alpha' /><label for='ordre_alpha' style='cursor: pointer;'> according to the alphabetical order of the courses.</label>
</fieldset>
</td>

<td><input type='submit' value='Save' /></td>

<td width='40%'>
<?php


$call_nom_class = mysql_query("SELECT * FROM classes WHERE id = '$id_classe'");
$display_rang = mysql_result($call_nom_class, 0, 'display_rang');
if($display_rang=='y') {
	$titre="Recalculation of the ranks";
	$texte="<p>A user encountered one day the following problem&nbsp;:<br />The ran was calculated for the courses, but not for the general rank of the student.<br />This link makes it possible to force the recalculation of the
ranks for the courses as for the general rank.<br />The recalculation will be done during of the next display of bulletin or averages.</p>";
	$tabdiv_infobulle[]=creer_div_infobulle('recalcul_rang',$titre,"",$texte,"",25,0,'y','y','n','n');
	
	echo "<fieldset style='padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;'>\n";
	echo "<p>For this class, <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;forcer_recalcul_rang=y' onclick=\"return confirm_abandon (this, change, '$themessage')\">force the recalculation of the ranks</a> ";
	
	echo "<a href='#' onclick=\"afficher_div('recalcul_rang','y',-100,20);return false;\"";
	echo ">";
	echo "<img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Forcer le recalcul des rangs' title='Force the recalculation of the rank' />\n";
	echo "</a>";
	
	echo ".</p>\n";
	echo "</fieldset>\n";
}
?>

<br />

<fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;">
<!--a href='javascript:coeff();'>Mettre tous les coefficients à</a-->
<input type='button' value='Put all the coefficients to' onClick='coeff(); changement();' />
<select name='coefficient_recop' id='coefficient_recopie' >
<?php
for($i=0;$i<10;$i++){
    echo "<option value='$i'>$i</option>\n";
}
?>
</select>
<!--input type='button' value='Modifier' onClick='coeff();' /-->
<!--Mettre tous les coefficients à <input type='button' value='0' onClick='coeff(0);' /> / <input type='button' value='1' onClick='coeff(1);' /-->
<!--/p-->
</fieldset>
</td></tr></table>
<!--p><i>Pour les enseignements impliquant plusieurs classes, le coefficient s'applique à tous les élèves de la classe courante et peut être réglé indépendamment d'une classe à l'autre (pour le régler individuellement par élève, voir la liste des élèves inscrits).</i-->
<?php
    // si le module ECTS est activé, on calcul la valeur total d'ECTS attribués aux groupes
    if ($gepiSettings['active_mod_ects'] == "y") {
        $total_ects = mysql_result(mysql_query("SELECT sum(valeur_ects) FROM j_groupes_classes WHERE (id_classe = '".$id_classe."' and saisie_ects = TRUE)"), 0);
        echo "<p style='margin-top: 10px;'>Nombre total d'ECTS actuellement attribués pour cette classe : ".intval($total_ects)."</p>\n";
        if ($total_ects < 30) {
            echo "<p style='color: red;'>Caution, the total of ECTS for one six-month period should be at least equal to 30.</p>\n";
        }
    }

	// Mettre un témoin pour repérer le prof principal
	$tab_prof_suivi=get_tab_prof_suivi($id_classe);
	$nb_prof_suivi=count($tab_prof_suivi);
	if($nb_prof_suivi>1) {
		$liste_prof_suivi="";
		for($loop=0;$loop<count($tab_prof_suivi);$loop++) {
			if($loop>0) {$liste_prof_suivi.=", ";}
			$liste_prof_suivi.=civ_nom_prenom($tab_prof_suivi[$loop]);
		}
	}

    $cpt_grp=0;
    $res = mysql_query("SELECT id, nom_court, nom_complet, priority FROM matieres_categories");
    $mat_categories = array();
    while ($row = mysql_fetch_object($res)) {
        $mat_categories[] = $row;
    }
    foreach ($groups as $group) {

        $current_group = get_group($group["id"]);
        $total = count($group["classes"]);
        echo "<br/>\n";
        echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";
        echo "<table border = '0' width='100%' summary='Suppression'><tr><td width='25%'>\n";
        //echo "<a href='edit_class.php?id_groupe=". $group["id"] . "&amp;action=delete_group&amp;id_classe=$id_classe' onclick=\"return confirmlink(this, 'ATTENTION !!! LISEZ CET AVERTISSEMENT : La suppression d\'un enseignement est irréversible. Une telle suppression ne devrait pas avoir lieu en cours d\'année. Si c\'est le cas, cela peut entraîner la présence de données orphelines dans la base. Si des données officielles (notes et appréciations du bulletin) sont présentes, la suppression sera bloquée. Dans le cas contraire, toutes les données liées au groupe seront supprimées, incluant les notes saisies par les professeurs dans le carnet de notes ainsi que les données présentes dans le cahier de texte. Etes-vous *VRAIMENT SÛR* de vouloir continuer ?', 'Confirmation de la suppression')\"><img src='../images/icons/delete.png' alt='Supprimer' style='width:13px; heigth: 13px;' /></a>";
        echo "<a href='edit_class.php?id_groupe=". $group["id"] . "&amp;action=delete_group&amp;id_classe=$id_classe".add_token_in_url()."'><img src='../images/icons/delete.png' alt='Supprimer' style='width:13px; heigth: 13px;' /></a>";
        echo " -- <span class=\"norme\">";
        echo "<b>";
        if ($total == "1") {
            echo "<a href='edit_group.php?id_groupe=". $group["id"] . "&amp;id_classe=" . $id_classe . "&amp;mode=groupe'>";
        } else {
            echo "<a href='edit_group.php?id_groupe=". $group["id"] . "&amp;id_classe=" . $id_classe . "&amp;mode=regroupement'>";
        }
        //echo $group["description"] . "</a></b>";
        echo htmlentities($group["description"]) . "</a></b>";
        //===============================
        // AJOUT: boireaus
        echo "<input type='hidden' name='enseignement_".$cpt_grp."' id='enseignement_".$cpt_grp."' value=\"".htmlentities($group["description"])."\" />\n";
        //===============================
        echo "</span>";

        //===============================
        // AJOUT: boireaus
        unset($result_matiere);
        // On récupère l'ordre par défaut des matières dans matieres pour permettre de fixer les priorités d'après les priorités par défaut de matières.
        // Sinon, pour l'affichage, c'est la priorité dans j_groupes_classes qui est utilisée à l'affichage dans les champs select.
        $sql="SELECT m.priority, m.categorie_id FROM matieres m, j_groupes_matieres jgc WHERE jgc.id_groupe='".$group["id"]."' AND m.matiere=jgc.id_matiere";
        //$sql="SELECT jgc.priorite, m.categorie_id FROM matieres m, j_groupes_matieres jgm, j_groupes_classes jgc WHERE jgc.id_groupe='".$group["id"]."' AND m.matiere=jgm.id_matiere AND jgc.id_groupe=jgm.id_groupe;";
        //$sql="SELECT priorite FROM j_groupes_classes jgc WHERE jgc.id_groupe='".$group["id"]."' AND id_classe='$id_classe'";
        //echo "$sql<br />\n";
        $result_matiere=mysql_query($sql);
        $ligmat=mysql_fetch_object($result_matiere);
        $mat_priorite[$cpt_grp]=$ligmat->priority;
        //$mat_priorite[$cpt_grp]=$ligmat->priorite;
        $mat_cat_id[$cpt_grp]=$ligmat->categorie_id;
        //$mat_priorite[$cpt_grp]=$ligmat->priorite;
        //echo "\$mat_priorite[$cpt_grp]=".$mat_priorite[$cpt_grp]."<br />\n";
        //===============================

        $j= 1;
        if ($total > 1) {
            echo "&nbsp;&nbsp;(avec : ";
            //==========================================
            // AJOUT: boireaus
            unset($tabclasse);
            //==========================================
            foreach ($group["classes"] as $classe) {
                //==========================================
                // MODIF: boireaus
                /*
                if ($classe["id"] != $id_classe) {
                    echo $classe["classe"];
                    if ($j < $total) echo ", ";
                }
                */
                if ($classe["id"] != $id_classe) {
                    $tabclasse[]=$classe["classe"];
                }
                //==========================================
                $j++;
            }
            //==============================
            // AJOUT: boireaus
            echo $tabclasse[0];
            for($i=1;$i<count($tabclasse);$i++){
                echo ", $tabclasse[$i]";
            }
            //==============================
            echo ")";
        }

        echo "</td>\n";

        $inscrits = null;
    //echo "=======================================<br />\n";
        foreach($current_group["periodes"] as $period) {
        //echo "\$period[\"num_periode\"]=".$period["num_periode"]."<br />\n";
        if($period["num_periode"]!=""){
            $inscrits .= count($current_group["eleves"][$period["num_periode"]]["list"]) . "-";
        }
        }

        $inscrits = substr($inscrits, 0, -1);

        echo "<td><b><a href='edit_eleves.php?id_groupe=". $group["id"] . "&amp;id_classe=" . $id_classe . "' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/edit_user.png' alt=''/> Registered students (" . $inscrits . ")</a>";
        echo "</b></td>\n";
        echo "<td width='20%'>Priorité d'affichage";
        //=================================
        // MODIF: boireaus
        //echo "<select size=1 name='" . "priorite_" . $current_group["id"] . "'>";
        // Attention à ne pas confondre l'Id et le Name qui ne coïncident pas.
        echo "<select onchange=\"changement()\" size=1 id='priorite_".$cpt_grp."' name='priorite_" . $current_group["id"] . "'>\n";
        //=================================
        echo "<option value=0";
        if  ($current_group["classes"]["classes"][$id_classe]["priorite"] == '0') echo " SELECTED";
        echo ">0";
        if ($priority_defaut == 0) echo " (default value)";
        echo "</option>\n";
        $k = 0;

        $k=11;
        $j = 1;
        while ($k < 61) {
            echo "<option value=$k";
			if ($current_group["classes"]["classes"][$id_classe]["priorite"] == $k) {echo " SELECTED";}
			echo ">".$j;
            if ($priority_defaut == $k) {echo " (default value)";}
            echo "</option>\n";
            $k++;
            $j = $k - 10;
        }
        echo "</select>\n";
        echo "</td>\n";
        // Catégories de matières
        echo "<td>Catégorie : ";
        echo "<select onchange=\"changement()\" size=1 id='categorie_".$cpt_grp."' name='categorie_" .$current_group["id"]. "'>\n";
        echo "<option value='0'";
        if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] == "0") {echo " SELECTED";}
        echo ">Aucune</option>\n";
		$tab_categorie_id=array();
        foreach ($mat_categories as $cat) {
			$tab_categorie_id[]=$cat->id;
            echo "<option value='".$cat->id . "'";
            if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] == $cat->id) {
               echo " SELECTED";
            }
            echo ">".html_entity_decode_all_version($cat->nom_court)."</option>\n";
        }
        echo "</select>\n";

		if(($current_group["classes"]["classes"][$id_classe]["categorie_id"]!=0)&&(!in_array($current_group["classes"]["classes"][$id_classe]["categorie_id"],$tab_categorie_id))) {
			$temoin_anomalie_categorie="y";
			echo "<a href='#' onclick=\"afficher_div('association_anormale_enseignement_categorie','y',-100,20);return false;\"'><img src='../images/icons/flag2.gif' width='17' height='18' /></a>";
		}

        if(($display_mat_cat=='y')&&($current_group["classes"]["classes"][$id_classe]["categorie_id"]=="0")) {
            //echo "<br />\n";
            $message_categorie_aucune="Course will not appear on the bulletins and report booklets. See http://www.sylogix.org/wiki/gepi/Enseignement_invisible";
            //echo "<img src='../images/icons/ico_attention.png' width='22' height='19' alt='$message_categorie_aucune' title='$message_categorie_aucune' />\n";

            echo "<a href='#' onclick=\"afficher_div('enseignement_invisible','y',-100,20);return false;\"";
            echo ">";
            echo "<img src='../images/icons/ico_attention.png' width='22' height='19' alt='$message_categorie_aucune' title='$message_categorie_aucune' />\n";
            echo "</a>";
        }

		//=========================================================
		echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";

		// Visibilité sur les bulletins, CN,...
		for($loop=0;$loop<count($tab_domaines);$loop++) {
			echo "<div style='float: left; width: ".max(2,strlen($tab_domaines_sigle[$loop]))."em; border: 1px solid black; margin: 2px; text-align:center; background-color: white; font-weight: bold;'>\n";
			if(!in_array($current_group["id"],$invisibilite_groupe[$tab_domaines[$loop]])) {
				echo "<span style='color: blue;' title='Enseignement visible sur les ".$tab_domaines_texte[$loop]."'>".$tab_domaines_sigle[$loop]."</span><br />\n";
				echo "<input type='checkbox' name='visibilite_groupe_".$tab_domaines[$loop]."[]' value='".$current_group["id"]."'";
				echo " checked";
				echo " />\n";
			}
			else {
				echo "<span style='color: lightgray;' title='Course invisible  on ".$tab_domaines_texte[$loop]."'>".$tab_domaines_sigle[$loop]."</span><br />\n";
				echo "<input type='checkbox' name='visibilite_groupe_".$tab_domaines[$loop]."[]' value='".$current_group["id"]."'";
				echo " />\n";
			}
			echo "</div>\n";
		}
		//=========================================================

        echo "</td>\n";

        // Coefficient
        //echo "<td>Coefficient : <input type=\"text\" onchange=\"changement()\" id='coef_".$cpt_grp."' name='". "coef_" . $current_group["id"] . "' value='" . $current_group["classes"]["classes"][$id_classe]["coef"] . "' size=\"5\" /></td></tr>";
        echo "<td align='center'>Coefficient : <input type=\"text\" onchange=\"changement()\" id='coef_".$cpt_grp."' name='". "coef_" . $current_group["id"] . "' value='" . $current_group["classes"]["classes"][$id_classe]["coef"] . "' size=\"5\" />\n";
        echo "<br />\n";

		/*
        echo "<input type='checkbox' name='note_sup_10_".$current_group["id"]."' id='note_sup_10_".$current_group["id"]."' value='y' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="sup10") {echo "checked ";}
        echo "/><label for='note_sup_10_".$current_group["id"]."'> Note&gt;10</label>\n";
		*/

		echo "<table class='boireaus' summary='Mode of consideration of the average'>\n";
		echo "<tr>\n";
		echo "<th class='small'><label for='note_standard_".$current_group["id"]."' title='The note counts normally in the average.'>The note<br />compte</label></th>\n";
		echo "<th class='small'><label for='note_bonus_".$current_group["id"]."' title='Coefficiented points above 10 are added without increasing the total of the coefficients.'>Bonus</label></th>\n";
		echo "<th class='small'><label for='note_sup_10_".$current_group["id"]."' title='The note counts only if it is higher or equal to 10'>Sup10</label></th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>\n";
        echo "<input type='radio' name='mode_moy_".$current_group["id"]."' id='note_standard_".$current_group["id"]."' value='-' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="-") {echo "checked ";}
        echo "/>\n";
		echo "</td>\n";
		echo "<td>\n";
        echo "<input type='radio' name='mode_moy_".$current_group["id"]."' id='note_bonus_".$current_group["id"]."' value='bonus' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="bonus") {echo "checked ";}
        echo "/>\n";
		echo "</td>\n";
		echo "<td>\n";
        echo "<input type='radio' name='mode_moy_".$current_group["id"]."' id='note_sup_10_".$current_group["id"]."' value='sup10' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="sup10") {echo "checked ";}
        echo "/>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

        echo "</td>\n";
        echo "</tr>\n";

        echo "<tr>\n";
        echo "<td colspan='5'>\n";
        $first = true;
        foreach($current_group["profs"]["list"] as $prof) {
            if (!$first) {echo ", ";}
            //echo $current_group["profs"]["users"][$prof]["prenom"];
            echo casse_mot($current_group["profs"]["users"][$prof]["prenom"],'majf2');
            echo " ";
            echo $current_group["profs"]["users"][$prof]["nom"];

			if(in_array($current_group["profs"]["users"][$prof]["login"],$tab_prof_suivi)) {
				echo " <img src='../images/bulle_verte.png' width='9' height='9' title=\"Principal professor of at least a student of the class on one of the periods.";
				if($nb_prof_suivi>1) {echo " The list of ".getSettingValue('prof_suivi')." is ".$liste_prof_suivi.".";}
				echo "\" />\n";
			}
            $first = false;
        }
        echo "</td>\n";
        echo "</tr>\n";
        if ($gepiSettings['active_mod_ects'] == "y") {
            echo "<tr><td>&nbsp;</td>\n";
            echo "<td><label for='saisie_ects_".$cpt_grp."'>Activer la saisie ECTS</label>&nbsp;<input id='saisie_ects_".$cpt_grp."' type='checkbox' name='saisie_ects_".$current_group["id"]."' value='1'";
            if($current_group["classes"]["classes"][$id_classe]["saisie_ects"]) {
                echo " checked";
            }
            echo "/>\n";
            echo "<input id='no_saisie_ects_".$cpt_grp."' type='hidden' name='no_saisie_ects_".$current_group["id"]."' value='0' />\n";
            echo "</td>\n";
            echo "<td>\n";
            echo "Number of ECTS by default for a period : ";
            echo "<select onchange=\"changement()\" id='valeur_ects_".$cpt_grp."' name='". "valeur_ects_" . $current_group["id"] . "'>\n";
            for($c=0;$c<31;$c++) {
                echo "<option value='$c'";
                if (intval($current_group["classes"]["classes"][$id_classe]["valeur_ects"]) == $c) echo " SELECTED ";
                echo ">$c</option>\n";
            }
            echo "</select>\n";
            echo "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
        echo "</fieldset>\n";

        $cpt_grp++;
    }


if(isset($temoin_anomalie_categorie)&&($temoin_anomalie_categorie=='y')) {
	$titre="Anomaly of association course/category";
	$texte="<p>This course is associated to category which does not exist or more.<br />Please control the parameters and click on <b>Save</b> to correct.";
	$tabdiv_infobulle[]=creer_div_infobulle('association_anormale_enseignement_categorie',$titre,"",$texte,"",30,0,'y','y','n','n');
}

echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />\n";
echo "<p align='center'><input type='submit' value='Save' /></p>\n";
echo "</form>\n";

//================================================
// AJOUT:boireaus
echo "<script type='text/javascript' language='javascript'>
    function choix_ordre(){
    if(document.getElementById('ordre_alpha').checked){
        ordre_alpha();
    }
    else{
        ordre_defaut();
    }
    }
    function ordre_alpha(){
        cpt=0;
        enseignement=new Array();
        while(cpt<$cpt_grp){
            enseignement[cpt]=document.getElementById('enseignement_'+cpt).value;
            cpt++;
        }
        enseignement.sort();
        cpt=0;
        while(cpt<$cpt_grp){
            for(i=0;i<$cpt_grp;i++){
                docens=document.getElementById('enseignement_'+i).value;
                if(enseignement[cpt]==document.getElementById('enseignement_'+i).value){
                    document.getElementById('priorite_'+i).selectedIndex=cpt+1;
                }
            }
            cpt++;
        }
        //document.forms['formulaire'].submit();
        changement();
    }

    function ordre_defaut(){";
        for($i=0;$i<count($mat_priorite);$i++){
            $rang=0;
            if($mat_priorite[$i]>0){$rang=$mat_priorite[$i]-10;}
            //echo "document.getElementById('priorite_'+$i).selectedIndex=$mat_priorite[$i];\n";
            echo "document.getElementById('priorite_'+$i).selectedIndex=$rang;\n";
        }
echo "}

    function coeff(){
        nombre=document.getElementById('coefficient_recopie').value;
        chaine_reg=new RegExp('[0-9]+');
        if(nombre.replace(chaine_reg,'').length!=0){
            nombre=0;
        }
        cpt=0;
        while(cpt<$cpt_grp){
            document.getElementById('coef_'+cpt).value=nombre;
            cpt++;
        }
        //document.forms['formulaire'].submit();
        changement();
    }
</script>\n";
?>


<!--form enctype="multipart/form-data" action="edit_class.php" name="formulaire2" method=post>
    <input type='button' value="Définir les priorités d'après l'ordre alphabétique" onClick="ordre_alpha();" /><br />
    Mettre tous les coefficients à <input type='button' value='0' onClick='coeff(0);' /> / <input type='button' value='1' onClick='coeff(1);' />
</form-->
<p><i>Remarks:</i></p>
<ul>
<li>Only one not null coefficient causes appearance of all the coefficients on the bulletins.</li>
<li>Nonnull coefficients are necessary so that the general average line appears on the bulletin.</li>
<!--li>Les coefficients réglés ici ne s'appliquent qu'à la classe <?php echo $classe["classe"]?>, même dans le cas des enseignements concernant d'autres classes.</li-->
<li>For the courses concerning several classes, the coefficient applies to all the students of the current class
and can be regulated independently from one class to another (to regulate it student  by student, see the list of theregistered students).<br />
The coefficients regulated here thus apply only to the class
<?php
    // Bizarre... $classe peut contenir une autre classe que celle en cours???
    $classe_tmp = get_classe($id_classe);
    echo $classe_tmp["classe"];
?>
, even in the case of the courses concerning of the regroupings of several classes.</li>
<li>
	The modes of taking into account of the average of a course in the general average are as follows&nbsp;:
	<ul>
		<li>The note counts&nbsp;: The note counts normally in the average.</li>
		<li>Bonus&nbsp;: The points above 10 are coefficied and added on the whole of the points, but the total of the coefficients is not increased.</li>
		<li>Sup10&nbsp;: The note is counted only if it is higher than 10.<br />
		Notice&nbsp;: That does not improve necessarily the general average of the
student because if it had 13 of general average without this note, it loses points if it has 12 with a course counted sup10.<br />
		And the student which has 9 in this course does not lose an
unjust point?, not?</li>
	</ul>
</li>
</ul>
<?php

if($ouvrir_infobulle_nav=='y') {
	echo "<script type='text/javascript'>
	setTimeout(\"afficher_div('navigation_classe','y',-100,20);\",1000)
</script>\n";
}

require("../lib/footer.inc.php");

?>