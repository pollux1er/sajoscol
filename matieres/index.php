<?php
/*
 * $Id: index.php 8675 2011-11-30 14:10:19Z crob $
 *
 * Copyright 2001-2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
//debug_var();
$msg = '';
$error = false;
if (isset($_POST['is_posted'])) {
    // Les donn�es ont �t� post�es, on met � jour
    check_token();

    $get_all_matieres = mysql_query("SELECT matiere, priority, categorie_id FROM matieres");
    while ($row = mysql_fetch_object($get_all_matieres)) {
        // On passe les mati�res une par une et on met � jour
        $varname_p = strtolower($row->matiere)."_priorite";
		//echo "<p>Test \$varname_p=$varname_p<br />";
        if (isset($_POST[$varname_p])) {
			//echo "isset(\$_POST[$varname_p]) oui<br />";
            if (is_numeric($_POST[$varname_p])) {
				//echo "is_numeric(\$_POST[$varname_p]) oui<br />";
            	// La valeur est correcte
            	if ($_POST[$varname_p] != $row->priority) {
                // On a une valeur diff�rente. On met � jour.
                    $res = mysql_query("UPDATE matieres SET priority = '".$_POST[$varname_p] . "' WHERE matiere = '" . $row->matiere . "'");
                    if (!$res) {
                        $msg .= "<br/>Error during update of the priority of the course ".$row->matiere.".";
                        $error = true;
                    }
                }
                // On met � jour toutes les priorit�s dans les classes si �a a �t� demand�
                if (isset($_POST['forcer_defauts']) AND $_POST['forcer_defauts'] == "yes") {
			        $sql="UPDATE j_groupes_matieres jgm, j_groupes_classes jgc SET jgc.priorite='".$_POST[$varname_p]."' " .
			        		"WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$row->matiere."')";
					//echo "$sql<br />";
					$req = mysql_query($sql);
			        if (!$req) {
			        	$msg .="<br/>Error during update of the course priority in the classes for the course ".$row->matiere.".";
			        	$error = true;
			        }
                }
            }
        }

        // La m�me chose pour la cat�gorie de mati�re
        $varname_c = strtolower($row->matiere)."_categorie";
        if (isset($_POST[$varname_c])) {
        	if (is_numeric($_POST[$varname_c])) {
        		// On a une valeur correcte. On y va !
            	if ($_POST[$varname_c] != $row->categorie_id) {
                	// On a une valeur diff�rente. On met � jour.
                    $res = mysql_query("UPDATE matieres SET categorie_id = '".$_POST[$varname_c] . "' WHERE matiere = '" . $row->matiere . "'");
                    if (!$res) {
                        $msg .= "<br/>Error during update of the category of the course ".$row->matiere.".";
                        $error = true;
                    }
                }

                // On met � jour toutes les cat�gories dans les classes si �a a �t� demand�
                if (isset($_POST['forcer_defauts']) AND $_POST['forcer_defauts'] == "yes") {
			        $req = mysql_query("UPDATE j_groupes_classes jgc, j_groupes_matieres jgm SET jgc.categorie_id='".$_POST[$varname_c]."' " .
			        		"WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$row->matiere."')");
			        if (!$req) {
			        	$msg .="<br/>Error during update of the category of course in the classes for the course ".$row->matiere.".";
			        	$error = true;
			        }
                }
            }
        }


    }
    if ($error) {
        $msg .= "<br/>Errors occurred during update of the data.";
    } else {
        $msg .= "<br/>Update carried out.";
    }
}

if(isset($_GET['msg'])) {
	$_GET['msg']=html_entity_decode($_GET['msg']);
}

$themessage = 'Modifications were carried out. Do you really want to leave without record  ?';
//**************** EN-TETE *****************
$titre_page = "Management of courses";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<p class=bold><a href="../accueil_admin.php"<?php echo insert_confirm_abandon();?>><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>
 | <a href="modify_matiere.php"<?php echo insert_confirm_abandon();?>>Add course</a>
 | <a href='matieres_param.php'<?php echo insert_confirm_abandon();?>>Parameter setting of several courses by batches</a>
 | <a href='matieres_categories.php'<?php echo insert_confirm_abandon();?>>Edit categories of courses</a>
 | <a href='matieres_csv.php'<?php echo insert_confirm_abandon();?>>Import a CSV of the list of the courses</a>
</p>
<form enctype="multipart/form-data" action="index.php" method=post>
<?php
echo add_token_field();
?>
<input type='submit' value='Enregistrer' style='margin-left: 10%; margin-bottom: 0px;' />
<p><label for='forcer_defauts' style='cursor: pointer;'>For all the classes, force the values defined for all the courses below <input type='checkbox' name='forcer_defauts' id='forcer_defauts' value='yes' /></label>
<br/><b>Caution !</b> This function will erase all your manual changes concerning the priority and the category of each course in the various classes !</p>
<input type='hidden' name='is_posted' value='1' />
<table class='boireaus' width = '100%' cellpadding = '5'>
<tr>
    <th><p class='bold'><a href='./index.php?orderby=m.matiere'<?php echo insert_confirm_abandon();?>>Course Identifier </a></p></th>
    <th><p class='bold'><a href='./index.php?orderby=m.nom_complet'<?php echo insert_confirm_abandon();?>>Complete name</a></p></th>
    <th><p class='bold'><a href='./index.php?orderby=m.priority,m.nom_complet'<?php echo insert_confirm_abandon();?>>Order of display<br />by default</a></p></th>
    <th><p class='bold'>Default category</p></th>
    <th><p class='bold'>Delete</p></th>
</tr>
<?php
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : (isset($_POST['orderby']) ? $_POST["orderby"] : 'm.priority,m.nom_complet');
if ($orderby != "m.matiere" AND $orderby != "m.nom_complet" AND $orderby != "m.priority,m.matiere") {
    $orderby = "m.priority,m.nom_complet";
}
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
// On va chercher les classes d�j� existantes, et on les affiche.

$call_data = mysql_query("SELECT m.matiere, m.nom_complet, m.priority, m.categorie_id FROM matieres m ORDER BY $orderby");
$get_cat = mysql_query("SELECT id, nom_court FROM matieres_categories");
while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
    $categories[] = $row;
}

$nombre_lignes = mysql_num_rows($call_data);
$i = 0;
$alt=1;
while ($i < $nombre_lignes){
    $alt=$alt*(-1);

	$current_matiere = mysql_result($call_data, $i, "matiere");
    $current_matiere_nom = mysql_result($call_data, $i, "nom_complet");
    $current_matiere_priorite = mysql_result($call_data, $i, "priority");
    $current_matiere_categorie_id = mysql_result($call_data, $i, "categorie_id");

    if ($current_matiere_priorite > 1) $current_matiere_priorite -= 10;
    echo "<tr class='lig$alt white_hover'><td><a href='modify_matiere.php?current_matiere=$current_matiere'".insert_confirm_abandon().">$current_matiere</a></td>\n";
    //echo "<td>$current_matiere_nom</td>";
    //echo "<td>".html_entity_decode($current_matiere_nom)."</td>";
    echo "<td>".htmlentities($current_matiere_nom)."</td>\n";
    // La priorit� par d�faut
    echo "<td>\n";
    echo "<select size=1 name='" . strtolower($current_matiere)."_priorite' onchange='changement()'>\n";
    $k = '0';
    echo "<option value=0>0</option>\n";
    $k='11';
    $j = '1';
    //while ($k < '51'){
    while ($k < '61'){
        echo "<option value=$k"; if ($current_matiere_priorite == $j) {echo " SELECTED";} echo ">$j</option>\n";
        $k++;
        $j = $k - 10;
    }
    //echo "</select></td>\n";
    echo "</select>\n";

    "</td>\n";

    echo "<td>\n";
    echo "<select size=1 name='" . strtolower($current_matiere)."_categorie' onchange='changement()'>\n";

	echo "<option value='0'";
	if ($current_matiere_categorie_id == '0') {echo " SELECTED";}
	echo ">Aucune</option>\n";
    foreach ($categories as $row) {
        echo "<option value='".$row["id"]."'";
        if ($current_matiere_categorie_id == $row["id"]) {echo " SELECTED";}
        echo ">".html_entity_decode_all_version($row["nom_court"])."</option>\n";
    }
    echo "</select>\n";
    echo "</td>\n";
    //echo "<td><a href=\"../lib/confirm_query.php?liste_cible=$current_matiere&amp;action=del_matiere\" onclick=\"return confirmlink(this, 'La suppression d\'une mati�re est irr�versible. Une telle suppression ne devrait pas avoir lieu en cours d\'ann�e. Si c\'est le cas, cela peut entra�ner la pr�sence de donn�es orphelines dans la base. Etes-vous s�r de vouloir continuer ?', 'Confirmation de la suppression')\">Supprimer</a></td></tr>\n";
    echo "<td><a href=\"suppr_matiere.php?matiere=$current_matiere\" onclick=\"return confirmlink(this, 'The removal of a course is irreversible. Such a suppression should not take place in the course of year. If it is the case, that can involve the presence of orphan data in the base. Are you sure you want to continue ?', 'Confirmation of the suppression')\">Delete</a></td></tr>\n";
	$i++;
}
?>
</table>
<input type='submit' value='Save' style='margin-left: 70%; margin-top: 25px; margin-bottom: 100px;' />
</form>
<?php require("../lib/footer.inc.php");?>