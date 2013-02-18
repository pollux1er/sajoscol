<?php
/*
 * $Id: add_group.php 7980 2011-08-25 19:59:52Z crob $
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

// Initialisation des variables utilisées dans le formulaire

$reg_nom_groupe = '';
$reg_nom_complet = '';
$reg_matiere = isset($_GET['matiere']) ? $_GET['matiere'] : (isset($_POST['matiere']) ? $_POST['matiere'] : null);
if ($reg_matiere == "null") $reg_matiere = null;
$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST['id_classe'] : null);
$reg_id_classe = $id_classe;
$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_POST['mode']) ? $_POST['mode'] : "groupe");
if(isset($reg_matiere)){
    if($reg_matiere!="" && $reg_matiere != "null"){
        $sql="SELECT * FROM matieres WHERE matiere='$reg_matiere'";
        $resultat_recup_matiere=mysql_query($sql);

        $ligne=mysql_fetch_object($resultat_recup_matiere);
        $reg_nom_groupe=$ligne->matiere;
        $reg_nom_complet=$ligne->nom_complet;
        $matiere_categorie = $ligne->categorie_id;
    } else {
        $matiere_categorie = 1;
    }
} else {
    $matiere_categorie = 1;
}

$reg_clazz = array();

if (isset($_POST['is_posted'])) {
	check_token();

    $error = false;
    $reg_nom_groupe = html_entity_decode_all_version($_POST['groupe_nom_court']);
    $reg_nom_complet = html_entity_decode_all_version($_POST['groupe_nom_complet']);
    $reg_matiere = $_POST['matiere'];
    $reg_categorie = $_POST['categorie'];
	//echo "\$reg_categorie=$reg_categorie<br />";

    if (empty($reg_nom_groupe)) {
        $error = true;
        $msg .= "You must give a short name to the group.<br/>\n";
    }

    if (empty($reg_nom_groupe)) {
        $error = true;
        $msg .= "You must give a complete name to the group.<br/>\n";
    }

    $clazz = array();

    if ($_POST['mode'] == "groupe") {
        $clazz[] = $_POST['id_classe'];
        $reg_id_classe = $_POST['id_classe'];
        $mode = "groupe";
    } else if ($_POST['mode'] == "regroupement") {
        $mode = "regroupement";
        foreach ($_POST as $key => $value) {
            if (preg_match("/^classe\_/", $key)) {
                $temp = explode("_", $key);
                $classe_id = $temp[1];
                $clazz[] = $classe_id;
            }
        }
    }

    $reg_clazz = $clazz;

    if (empty($reg_clazz)) {
        $error = true;
        $msg .= "You must select at least a class.<br/>\n";
    }

    if (!is_numeric($reg_categorie)) {
        $reg_categorie = 1;
    }

	//echo "\$reg_categorie=$reg_categorie<br />";
	//$error=true;

    if (!$error) {
        // pas d'erreur : on continue avec la création du groupe
        $create = create_group($reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_categorie);
        if (!$create) {
            $msg .= "Error during creation of the group. ";
        } else {
            $msg = "The course was indeed created. ";
            $msg = urlencode($msg);

            // On s'occupe des profs, s'il y en a.
                $reg_professeurs = array();
                foreach ($_POST as $key => $value) {
                    if (preg_match("/^prof\_/", $key)) {
                        $id = preg_replace("/^prof\_/", "", $key);
                        $proflogin = $_POST["proflogin_".$id];
                        $reg_professeurs[] = $proflogin;
                    }
                }


				// METTRE TOUS LES ELEVES DES CLASSES CONCERNEES DANS LE GROUPE
				$reg_eleves=array();
				$current_group=get_group($create);
				foreach ($current_group["periodes"] as $period) {
					$reg_eleves[$period['num_periode']]=array();
					foreach($reg_clazz as $tmp_id_classe){
						$sql="SELECT login FROM j_eleves_classes WHERE id_classe='$tmp_id_classe' AND periode='".$period['num_periode']."'";
						$res_ele=mysql_query($sql);
						if(mysql_num_rows($res_ele)>0){
							while($lig_ele=mysql_fetch_object($res_ele)){
								$reg_eleves[$period['num_periode']][]=$lig_ele->login;
							}
						}
					}
				}


                if (count($reg_professeurs) == 0) {
                    header("Location: ./edit_group.php?id_groupe=$create&msg=$msg&id_classe=$id_classe&mode=$mode");
                } else {
                    //$res = update_group($create, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, array());
                    $res = update_group($create, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);

					//if($res){$msg.="Mise à jour des professeurs du groupe effectuée. ";}else{$msg.="Echec de la mise à jour des professeurs du groupe. ";}
					if (count($reg_professeurs) == 1) {
						if($res){$msg.="Assignment of the professor to the course carried out. ";}else{$msg.="Failure of the assignment of the professor to the course. ";}
					}
					else{
						if($res){$msg.="Assignment of the professor to the course carried out. ";}else{$msg.="Failure of the assignment of the professors to the course. ";}
					}
                    //header("Location: ./edit_class.php?id_classe=$id_classe");
                    header("Location: ./edit_class.php?id_classe=$id_classe&msg=$msg");
                }
        }

    }

}

//**************** EN-TETE **************************************
$titre_page = "Management of the groups";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************
?>
<p class="bold">
<a href="edit_class.php?id_classe=<?php echo $id_classe;?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<?php
if ($mode == "groupe") {
    echo "<h3>Add a group to a classe</h3>\n";
} elseif ($mode == "regroupement") {
    echo "<h3>Add a regrouping of students of various classes</h3>\n";
}

?>
<form enctype="multipart/form-data" action="add_group.php" method=post>
<div style="width: 95%;">
<div style="width: 45%; float: left;">
<p>Short name : <input type=text size=30 name=groupe_nom_court value = "<?php echo $reg_nom_groupe; ?>" /></p>

<p>Complete name : <input type=text size=30 name=groupe_nom_complet value = "<?php echo $reg_nom_complet; ?>" /></p>

<p>Course taught to this group :
<?php

echo add_token_field();

$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
$nb_mat = mysql_num_rows($query);

echo "<select name='matiere' size='1'>\n";

for ($i=0;$i<$nb_mat;$i++) {
    $matiere = mysql_result($query, $i, "matiere");
    $nom_matiere = mysql_result($query, $i, "nom_complet");
    echo "<option value='" . $matiere . "'";
    if ($reg_matiere == $matiere) {echo " SELECTED";}
    //echo ">" . $nom_matiere . "</option>\n";
    echo ">" . htmlentities($nom_matiere) . "</option>\n";
}
echo "</select>\n";
echo "</p>\n";

if ($mode == "groupe") {
    echo "<p>Class to which the new course belongs :\n";
    echo "<select name='id_classe' size='1'>\n";

    $call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
    $nombre_lignes = mysql_num_rows($call_data);
    if ($nombre_lignes != 0) {
        $i = 0;
        while ($i < $nombre_lignes){
            $id_classe = mysql_result($call_data, $i, "id");
            $classe = mysql_result($call_data, $i, "classe");
                echo "<option value='" . $id_classe . "'";
                if ($reg_id_classe == $id_classe) {echo " SELECTED";}
                echo ">$classe</option>\n";
        $i++;
        }
    } else {
        echo "<option value='false'>No class defined !</option>\n";
    }
    echo "</select>\n";
    echo "</p>\n";

} else if ($mode == "regroupement") {
    echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />\n";
    echo "<p>Select the classes to which the new course belongs :<br />\n";
    echo "<span style='color: red;'>Note : only the classes having the same number of periods appear .</span>\n";
    $current_classe_period_num = get_period_number($id_classe);
    $call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
    $nombre_lignes = mysql_num_rows($call_data);
    if ($nombre_lignes != 0) {

        $i = 0;

		$tmp_tab_classe=array();
		$tmp_tab_id_classe=array();
		while ($i < $nombre_lignes){
			$id_classe_temp=mysql_result($call_data, $i, "id");
			$classe=mysql_result($call_data, $i, "classe");
			if (get_period_number($id_classe_temp) == get_period_number($id_classe)) {
				$tmp_tab_classe[]=$classe;
				$tmp_tab_id_classe[]=$id_classe_temp;
			}
			$i++;
		}

        echo "<table width='100%'>\n";
        echo "<tr valign='top' align='left'>\n";
        echo "<td>\n";
        //$nb_class_par_colonne=round($nombre_lignes/3);
        $nb_class_par_colonne=round(count($tmp_tab_classe)/3);
        //while ($i < $nombre_lignes){
		for($i=0;$i<count($tmp_tab_classe);$i++) {
            if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
                echo "</td>\n";
                echo "<td>\n";
            }

			$_id_classe=$tmp_tab_id_classe[$i];
			$classe=$tmp_tab_classe[$i];

			echo "<input type='checkbox' name='classe_" . $_id_classe . "' id='classe_" . $_id_classe . "' value='yes'";

			if (in_array($_id_classe, $reg_clazz) OR $_id_classe == $id_classe) {echo " checked";}

			echo " /><label for='classe_".$_id_classe."' style='cursor: pointer;'>$classe</label>\n";
			//echo ">$classe</option>\n";

			echo "<br />\n";
        }
		/*
        echo "<table width='100%'>\n";
        echo "<tr valign='top' align='left'>\n";
        echo "<td>\n";
        $nb_class_par_colonne=round($nombre_lignes/3);
        while ($i < $nombre_lignes){
            if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
                echo "</td>\n";
                echo "<td>\n";
            }

            $_id_classe = mysql_result($call_data, $i, "id");
            $classe = mysql_result($call_data, $i, "classe");
            if (get_period_number($_id_classe) == $current_classe_period_num) {
                echo "<input type='checkbox' name='classe_" . $_id_classe . "' id='classe_" . $_id_classe . "' value='yes'";

                if (in_array($_id_classe, $reg_clazz) OR $_id_classe == $id_classe) {echo " checked";}

                echo " /><label for='classe_".$_id_classe."' style='cursor: pointer;'>$classe</label>\n";
                //echo ">$classe</option>\n";

				echo "<br />\n";
            }
	        $i++;
        }
		*/
        //echo "</p>\n";
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
    } else {
        echo "<p>No class defined  !</p>\n";
    }
}
echo "<p>Category of course to which the course belongs : ";
echo "<select size='1' name='categorie'>\n";
$get_cat = mysql_query("SELECT id, nom_court FROM matieres_categories");
$test = mysql_num_rows($get_cat);

echo "<option value='0'";
if ($matiere_categorie == 0) {echo " SELECTED";}
echo ">None</option>\n";
while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
    echo "<option value='".$row["id"]."'";
    if ($matiere_categorie == $row["id"]) {echo " SELECTED";}
    echo ">".html_entity_decode_all_version($row["nom_court"])."</option>\n";
}
echo "</select>\n";

echo "</div>\n";
// On affiche une sélection des profs si la matière a été choisie

if ($reg_matiere != null) {
    echo "<div style='width: 45%; float: right;'>\n";
    echo "<p>Notch the professors who take part in this course : </p>\n";

    $sql="SELECT u.login, u.nom, u.prenom, u.civilite,u.statut FROM utilisateurs u, j_professeurs_matieres j WHERE (j.id_matiere = '$reg_matiere' and j.id_professeur = u.login and u.etat!='inactif') ORDER BY u.nom;";
    //echo "$sql<br />";
	$calldata = mysql_query($sql);
    $nb = mysql_num_rows($calldata);
    $prof_list = array();
    $prof_list["list"] = array();
    for ($i=0;$i<$nb;$i++) {
        $prof_login = mysql_result($calldata, $i, "login");
        $prof_nom = mysql_result($calldata, $i, "nom");
        $prof_prenom = mysql_result($calldata, $i, "prenom");
        $civilite = mysql_result($calldata, $i, "civilite");
        $prof_statut = mysql_result($calldata, $i, "statut");

        $prof_list["list"][] = $prof_login;
        //$prof_list["users"][$prof_login] = array("login" => $prof_login, "nom" => $prof_nom, "prenom" => $prof_prenom, "civilite" => $civilite);
        $prof_list["users"][$prof_login] = array("login" => $prof_login, "nom" => $prof_nom, "prenom" => $prof_prenom, "civilite" => $civilite, "statut" => $prof_statut);
    }

    if (count($prof_list["list"]) == "0") {
        echo "<p><font color=red>ERREUR !</font> No professor was defined as qualified in the course considered.</p>\n";
    } else {
        $total_profs = array_unique($prof_list["list"]);
        $p = 0;
        echo "<table class='boireaus'>\n";
		$alt=1;
		$temoin_nettoyage_requis='n';
        foreach($total_profs as $prof_login) {
			$alt=$alt*(-1);
            if($prof_list["users"][$prof_login]["statut"]=='professeur') {
				echo "<tr class='lig$alt'>\n";
				echo "<td>\n";
				echo "<input type='hidden' name='proflogin_".$p."' value='".$prof_login."' />\n";
				echo "<input type='checkbox' name='prof_".$p."' id='prof_".$p."' ";
				echo "onchange='checkbox_change($p);changement();'";
				echo " />\n";
				echo "</td>\n";
				echo "<td style='text-align:left;'>\n";
				echo "<label id='civ_nom_prenom_prof_$p' for='prof_".$p."' style='cursor: pointer;'>\n";
				echo " " . $prof_list["users"][$prof_login]["nom"] . " " . $prof_list["users"][$prof_login]["prenom"];
				echo "</label>\n";
				echo "</td>\n";
				echo "</tr>\n";
				$p++;
			}
			else {
				echo "<tr class='lig$alt'>\n";
				echo "<td>\n";
				echo "&nbsp;&nbsp;";
				echo "</td>\n";
				echo "<td style='text-align:left;'>\n";
				echo "<b>ANOMALY</b>&nbsp;:";
				echo " " . $prof_list["users"][$prof_login]["nom"] . " " . $prof_list["users"][$prof_login]["prenom"];
				echo " (<i style='color:red'>compte ".$prof_list["users"][$prof_login]["statut"]."</i>)";
				echo "<br />\n";
				$temoin_nettoyage_requis='y';
				//echo "Un <a href='../utilitaires/clean_tables.php'>nettoyage des tables</a> s'impose.";
				echo "</td>\n";
				echo "</tr>\n";
			}
        }
        echo "</table>\n";
		if($temoin_nettoyage_requis!='n') {
			echo "A <a href='../utilitaires/clean_tables.php'>cleaning of the tables</a> is needed.";
		}
    }
    echo "</div>\n";
}
// Fin : professeurs

echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('prof_'+cpt)) {
		if(document.getElementById('prof_'+cpt).checked) {
			document.getElementById('civ_nom_prenom_prof_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('civ_nom_prenom_prof_'+cpt).style.fontWeight='normal';
		}
	}
}

for(i=0;i<$p;i++) {
	checkbox_change(i);
}
</script>\n";


echo "<div style='float: left; width: 100%'>\n";
echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='mode' value='" . $mode . "' />\n";
echo "<p align='center'><input type='submit' value='Validate' /></p>\n";
echo "</div>\n";
echo "</div>\n";

echo "<div style='clear:both;'>&nbsp;</div>
<p><i>NOTES</i>&nbsp;:</p>
<ul>
<li><p>If you associate professors to the course, all the students of the class will be registered in the course.<br />
You will be able thereafter uncheck certain students if necessary.</p></li>
<li><p>If you do not associate any professor the course, the students will not be automatically affected in the course.<br />
You will have to affect the students in the course thereafter.</p></li>
<li><p>If you do not associate any professor a course, you can nevertheless type averages and appreciations on the bulletins with an account having the statute 'secours'.</p></li>
</ul>\n";

?>
</form>
<?php require("../lib/footer.inc.php");?>