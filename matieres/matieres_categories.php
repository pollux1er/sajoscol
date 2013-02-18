<?php
/*
* $Id: matieres_categories.php 6181 2010-12-17 16:54:04Z crob $
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
	}
	else { 
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

$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : (isset($_POST['orderby']) ? $_POST["orderby"] : 'priority,nom_complet');
if ($orderby != "nom_court" AND $orderby != "nom_complet" AND $orderby != "priority, nom_court") {
    $orderby = "priority,nom_complet";
}

$msg = null;

if (isset($_POST['action'])) {
	check_token();
    $error = false;
    if ($_POST['action'] == "add") {
        // On enregistre une nouvelle catégorie
        // On filtre un peu
        if (!is_numeric($_POST['priority'])) $_POST['priority'] = "0";
        // Le reste passera sans soucis, mais on coupe quand même si jamais c'est trop long
        if (strlen($_POST['nom_court']) > 250) $_POST['nom_court'] = substr($_POST['nom_court'], 0, 250);
        if (strlen($_POST['nom_complet']) > 250) $_POST['nom_complet'] = substr($_POST['nom_complet'], 0, 250);
        // On enregistre
        if ($_POST['nom_court'] == '') {
            $msg .= "The short name cannot be empty.<br/>";
            $error = true;
            $res = false;
        }
        if (strtolower($_POST['nom_court']) == 'aucune') {
            $msg .= "The short name cannot be 'Aucune'.<br/>";
            $error = true;
            $res = false;
        }
        if ($_POST['nom_complet'] == '') {
            $msg .= "The heading cannot be empty.<br/>";
            $error = true;
            $res = false;
        }

        if (!$error) {
            $res = mysql_query("INSERT INTO matieres_categories SET nom_court = '" . htmlentities($_POST['nom_court']) . "', nom_complet = '" . htmlentities($_POST['nom_complet']) . "', priority = '" . $_POST["priority"] . "'");
			updateOnline("INSERT INTO matieres_categories SET nom_court = '" . htmlentities($_POST['nom_court']) . "', nom_complet = '" . htmlentities($_POST['nom_complet']) . "', priority = '" . $_POST["priority"] . "'");
	   }
        if (!$res) {
            $msg .= "Error during recording of the new category.</br>";
            echo mysql_error();
        }
    } elseif ($_POST['action'] == "edit") {
        // On met à jour une catégorie
        // On filtre un peu
        if (!is_numeric($_POST['priority'])) $_POST['priority'] = "0";
        if (!is_numeric($_POST['categorie_id'])) $_POST['categorie_id'] = "0";
        // Le reste passera sans soucis, mais on coupe quand même si jamais c'est trop long
        if (strlen($_POST['nom_court']) > 250) $_POST['nom_court'] = substr($_POST['nom_court'], 0, 250);
        if (strlen($_POST['nom_complet']) > 250) $_POST['nom_complet'] = substr($_POST['nom_complet'], 0, 250);

        if ($_POST['nom_court'] == '') {
            $msg .= "The short name cannot be empty.<br/>";
            $error = true;
            $res = false;
        }
        if (strtolower($_POST['nom_court']) == 'aucune') {
            $msg .= "The short name cannot be 'Aucune'.<br/>";
            $error = true;
            $res = false;
        }
        if ($_POST['nom_complet'] == '') {
            $msg .= "The heading cannot be empty.<br/>";
            $error = true;
            $res = false;
        }

        if (!$error) {
            // On enregistre
            $res = mysql_query("UPDATE matieres_categories SET nom_court = '" . htmlentities($_POST['nom_court']) . "', nom_complet = '" . htmlentities($_POST['nom_complet']) . "', priority = '" . $_POST["priority"] . "' WHERE id = '".$_POST['categorie_id']."'");
        updateOnline("UPDATE matieres_categories SET nom_court = '" . htmlentities($_POST['nom_court']) . "', nom_complet = '" . htmlentities($_POST['nom_complet']) . "', priority = '" . $_POST["priority"] . "' WHERE id = '".$_POST['categorie_id']."'");
		}

        if (!$res) $msg .= "Error during update of the category.";
    } elseif ($_POST['action'] == "delete") {
        // On teste d'abord l'ID
        if (!is_numeric($_POST['categorie_id'])) {
            // Inutile d'en dire plus...
            $msg .= "Erreur.";
        } else {
            // On a un ID valide.
            // Si c'est l'ID 1, on ne supprime pas. C'est la catégorie par défaut
            if ($_POST['categorie_id'] == 1) {
                $msg .= "You cannot remove the default category !";
            } else {

                // On teste l'utilisation de cette catégorie
                $res = mysql_query("SELECT matiere FROM matieres WHERE categorie_id = '" . $_POST['categorie_id'] ."'");
                $test = mysql_num_rows($res);

                $res2 = mysql_query("SELECT DISTINCT id_groupe, c.id, c.classe FROM j_groupes_classes jgc, classes c WHERE c.id=jgc.id_classe AND categorie_id='".$_POST['categorie_id']."'");
                $test2 = mysql_num_rows($res2);

                if ($test>0) {
                    // On a des entrées... la catégorie a déjà été associée à des matières, donc on ne la supprime pas.
					$liste_matieres_associees="";
					while($lig=mysql_fetch_object($res)) {
						if($liste_matieres_associees!='') {$liste_matieres_associees.=", ";}
						$liste_matieres_associees.="<a href='index.php' target='_blank'>".$lig->matiere."</a>";
					}
                    $msg .= "The category could not be removed, because it was already associated to courses (<i>$liste_matieres_associees</i>).<br/>";
				}
                elseif ($test2>0) {
					$liste_classes_associees="";
					while($lig=mysql_fetch_object($res2)) {
						if($liste_classes_associees!='') {$liste_classes_associees.=", ";}
						$liste_classes_associees.="<a href='../groupes/edit_class.php?id_classe=$lig->id_classe' target='_blank'>".get_class_from_id($lig->id_classe)."</a>";
					}
                    $msg .= "The category could not be removed, because it was already associated courses for classes (<i>$liste_classes_associees</i>).<br/>";
                }
				else {
                    $res = mysql_query("DELETE FROM matieres_categories WHERE id = '" . $_POST['categorie_id']."'");
					updateOnline("DELETE FROM matieres_categories WHERE id = '" . $_POST['categorie_id']."'");
                    if (!$res) {
                        $msg .= "Error during suppression of the category.<br/>";
                    } else {
                        $msg .= "The category was removed.<br/>";
                    }
                }
            }
        }
    }
}

//**************** EN-TETE **************************************
$titre_page = "Management of the categories courses";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

if (isset($_GET['action'])) {
    // On a une action : soit on ajoute soit on édite soit on delete
    ?>
    <p class=bold><a href="matieres_categories.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>
    <?php
    if ($_GET['action'] == "add") {
        // On ajoute une catégorie
        // On affiche le formulaire d'ajout
        echo "<form enctype='multipart/form-data' action='matieres_categories.php' name='formulaire' method=post>";
		echo add_token_field();
        echo "<input type='hidden' name='action' value='add'>";
        echo "<p>Short name (used in the tools of configuration) : <input type='text' name='nom_court'></p>";
        echo "<p>Complete heading (used on the official documents) : <input type='text' name='nom_complet'></p>";
        echo "<p>Default Priority of display : ";
        echo "<select name='priority' size='1'>";
        for ($i=0;$i<11;$i++) {
            echo "<option value='$i'>$i</option>";
        }
        echo "</select>";

        echo "<p>";
        echo "<input type='submit' value='Save'>";
        echo "</p>";
        echo "</form>";
    } elseif ($_GET['action'] == "edit") {
        // On édite la catégorie existante
        if (!is_numeric($_GET['categorie_id'])) $_GET['categorie_id'] == 0;

        $res = mysql_query("SELECT id, nom_court, nom_complet, priority FROM matieres_categories WHERE id = '" . $_GET['categorie_id'] . "'");
        $current_cat = mysql_fetch_array($res, MYSQL_ASSOC);

        if ($current_cat) {
			if($current_cat["nom_court"]=='Aucune') {
				echo "<p style='color:red'>ANOMALY&nbsp;: It should not exist category entitled 'Aucune'.<br />See <a href='http://www.sylogix.org/wiki/gepi/Enseignement_invisible'>http://www.sylogix.org/wiki/gepi/Enseignement_invisible</a> and <a href='http://www.sylogix.org/wiki/gepi/Suppr_Cat_Aucune'>http://www.sylogix.org/wiki/gepi/Suppr_Cat_Aucune</a> for explanations</p>\n";
			}
            echo "<form enctype='multipart/form-data' action='matieres_categories.php' name='formulaire' method=post>";
			echo add_token_field();
            echo "<input type='hidden' name='action' value='edit'>";
            echo "<input type='hidden' name='categorie_id' value='".$current_cat["id"] . "'>";
            echo "<p>Short name (used in the tools of configuration) : <input type='text' name='nom_court' value='".html_entity_decode_all_version($current_cat["nom_court"]) ."' /></p>";
            echo "<p>Complete heading (used on the official documents) : <input type='text' name='nom_complet' value='".html_entity_decode_all_version($current_cat["nom_complet"]) ."' /></p>";
            echo "<p>Default Priority of display : ";
            echo "<select name='priority' size='1'>";
            for ($i=0;$i<11;$i++) {
                echo "<option value='$i'";
                if ($current_cat["priority"] == $i) echo " SELECTED";
                echo ">$i</option>";
            }
            echo "</select>";

            echo "<p>";
            echo "<input type='submit' value='Save'>";
            echo "</p>";
            echo "</form>";
        }

    }



} else {
    // Pas d'action. On affiche la liste des rubriques
    ?>
    <p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a> | <a href="matieres_categories.php?action=add">Add a category</a></p>
    <p>Notice : the default category cannot be removed. It is automatically associated to the existing courses and to the new courses, and for all the groups. You can rename it (Others, Except categories, etc.), but always leave a generic name.</p>

    <table class='boireaus' width='100%' border='1' cellpadding='5' summary='Tableau des catégories'>
<tr>
    <th><p class='bold'><a href='./matieres_categories.php?orderby=nom_court'>Short name</a></p></th>
    <th><p class='bold'><a href='./matieres_categories.php?orderby=m.nom_complet'>Complete heading</a></p></th>
    <th><p class='bold'><a href='./matieres_categories.php?orderby=m.priority,m.nom_complet'>Order of display<br />by default</a></p></th>
    <th><p class='bold'>Delete</p></th>
</tr>
    <?php
	$temoin_anomalie_categ_Aucune='n';
	$alt=1;
    $res = mysql_query("SELECT id, nom_court, nom_complet, priority FROM matieres_categories ORDER BY $orderby");
    while ($current_cat = mysql_fetch_array($res, MYSQL_ASSOC)) {
		$alt=$alt*(-1);
        echo "<tr class='lig$alt white_hover'>\n";
        echo "<td><a href='matieres_categories.php?action=edit&categorie_id=".$current_cat["id"]."'>".html_entity_decode_all_version($current_cat["nom_court"])."</a></td>\n";
        echo "<td>".html_entity_decode_all_version($current_cat["nom_complet"])."</td>\n";
        echo "<td>".$current_cat["priority"]."</td>\n";
        echo "<td>";
        if ($current_cat["id"] != "1") {
            echo "<form enctype='multipart/form-data' action='matieres_categories.php' name='formulaire' method=post>\n";
			echo add_token_field();
            echo "<input type='hidden' name='action' value='delete' />\n";
            echo "<input type='hidden' name='categorie_id' value='".$current_cat["id"]."' />\n";
            echo "<input type='submit' value='Delete' />\n</form>\n";
        } else {
            echo "Default category (impossible suppression)";
        }
		echo "</td>\n";
        echo "</tr>\n";
		if($current_cat["nom_court"]=='Aucune') {$temoin_anomalie_categ_Aucune='y';}
    }
    echo "</table>\n";
	if($temoin_anomalie_categ_Aucune=='y') {
		echo "<p style='color:red'>ANOMALY&nbsp;: It should not exist category entitled 'Aucune'.<br />See <a href='http://www.sylogix.org/wiki/gepi/Enseignement_invisible'>http://www.sylogix.org/wiki/gepi/Enseignement_invisible</a> and <a href='http://www.sylogix.org/wiki/gepi/Suppr_Cat_Aucune'>http://www.sylogix.org/wiki/gepi/Suppr_Cat_Aucune</a> for explanations</p>\n";
	}
}
require("../lib/footer.inc.php");
?>
