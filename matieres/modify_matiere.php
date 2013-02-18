<?php
/*
 * $Id: modify_matiere.php 8248 2011-09-16 11:29:30Z crob $
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


if (isset($_POST['isposted'])) {
	check_token();
    $ok = 'yes';
    if (isset($_POST['reg_current_matiere'])) {
        // On vérifie d'abord que l'identifiant est constitué uniquement de lettres et de chiffres :
        $matiere_name = $_POST['reg_current_matiere'];
        if (!is_numeric($_POST['matiere_categorie'])) {
            // On empêche les mise à jour globale automatiques, car on n'est pas sûr de ce qui s'est passé si l'ID n'est pas numérique...
            $ok = "no";
            $matiere_categorie = "0";
        } else {
            $matiere_categorie = $_POST['matiere_categorie'];
        }
        //if (ereg ("^[a-zA-Z_]{1}[a-zA-Z0-9_]{1,19}$", $matiere_name)) {
        if (preg_match("/^[a-zA-Z_]{1}[a-zA-Z0-9_]{1,19}$/", $matiere_name)) {
            $verify_query = mysql_query("SELECT * from matieres WHERE matiere='$matiere_name'");
            $verify = mysql_num_rows($verify_query);
            if ($verify == 0) {
                //========================
                // MODIF: boireaus
                // Quand on poste un &, c'est un &amp; qui est reçu.
                //$matiere_nom_complet = $_POST['matiere_nom_complet'];
				//echo "\$matiere_nom_complet=$matiere_nom_complet<br />\n";
                $matiere_nom_complet = html_entity_decode_all_version($_POST['matiere_nom_complet']);
				//echo "\$matiere_nom_complet=$matiere_nom_complet<br />\n";
                //========================
                $matiere_priorite = $_POST['matiere_priorite'];
                $sql="INSERT INTO matieres SET matiere='".$matiere_name."', nom_complet='".$matiere_nom_complet."', priority='".$matiere_priorite."', categorie_id = '" . $matiere_categorie . "',matiere_aid='n',matiere_atelier='n';";
				//echo "$sql<br />\n";
                $register_matiere = mysql_query($sql);
				updateOnline($sql);
                if (!$register_matiere) {
                    $msg = "An error occurred during recording of the new course. <br />";
                    $ok = 'no';
                } else {
                    $msg = "The new Course was indeed recorded. <br />";
                }
            } else {
                $msg = "This course already exists !! <br />";
                $ok = 'no';
            }
        } else {
            $msg = "The course identifier must be only made up of letters and figures with a maximum of 19 characters ! <br />";
            $ok = 'no';
        }
    } else {

        $matiere_nom_complet = $_POST['matiere_nom_complet'];
		$matiere_nom_complet = html_entity_decode_all_version($_POST['matiere_nom_complet']);
        $matiere_priorite = $_POST['matiere_priorite'];
        $matiere_name = $_POST['matiere_name'];
        if (!is_numeric($_POST['matiere_categorie'])) {
            $matiere_categorie = "0";
        } else {
            $matiere_categorie = $_POST['matiere_categorie'];
        }

        $sql="UPDATE matieres SET nom_complet='".$matiere_nom_complet."', priority='".$matiere_priorite."', categorie_id = '" . $matiere_categorie . "' WHERE matiere='".$matiere_name."';";
		//echo "$sql<br />\n";
        $register_matiere = mysql_query($sql);
	    updateOnline($sql);

        if (!$register_matiere) {
            $msg = "An error occurred during modification of the course <br />";
            $ok = 'no';
        } else {
            $msg = "The modifications were recorded ! <br />";
        }
    }
    if ((isset($_POST['force_defaut'])) and ($ok == 'yes')) {
        $sql="UPDATE j_groupes_matieres jgm, j_groupes_classes jgc SET jgc.priorite='".$matiere_priorite."'
        WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$matiere_name."')";
        //echo "$sql<br />";
        //$msg = rawurlencode($sql);
        $req = mysql_query($sql);
		updateOnline($sql);
    }
    if ((isset($_POST['force_defaut_categorie'])) and ($ok == 'yes')) {
        $sql="UPDATE j_groupes_classes jgc, j_groupes_matieres jgm SET jgc.categorie_id='".$matiere_categorie."'
        WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$matiere_name."')";
        //echo "$sql<br />";
        //$msg = rawurlencode($sql);
        $req = mysql_query($sql);
		updateOnline($sql);
    }

	if($ok=='yes') {
		$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : NULL;
		if(isset($login_prof)) {
			// Récupérer la liste des profs actuellement associés
			$tab_profs_associes=array();
			$sql="SELECT u.login FROM j_professeurs_matieres jpm, utilisateurs u WHERE jpm.id_professeur=u.login and id_matiere='$matiere_name' ORDER BY u.nom, u.prenom;";
			$res_profs=mysql_query($sql);
			
			if(mysql_num_rows($res_profs)>0) {
				while($lig=mysql_fetch_object($res_profs)) {
					$tab_profs_associes[]=$lig->login;
				}
			}
	
			$nb_inser=0;
			for($loop=0;$loop<count($login_prof);$loop++) {
				if(!in_array($login_prof[$loop], $tab_profs_associes)) {
					// Recherche de l'ordre matière le plus élevé pour ce prof
					$sql="SELECT MAX(ordre_matieres) max_ordre FROM j_professeurs_matieres WHERE id_professeur='".$login_prof[$loop]."';";
					$res=mysql_query($sql);
					
					if(mysql_num_rows($res)==0) {
						$ordre_matieres=1;
					}
					else {
						$ordre_matieres=mysql_result($res, 0, "max_ordre")+1;
					}
	
					// On ajoute le prof
					$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$login_prof[$loop]', id_matiere='$matiere_name', ordre_matieres='$ordre_matieres';";
					$insert=mysql_query($sql);
					updateOnline($sql);
					if(!$insert) {
						$msg.="Error during association of ".$login_prof[$loop]." with the course $matiere_name<br />";
					}
					else {
						$nb_inser++;
					}
				}
			}
	
			if($nb_inser>0) {
				$msg.="$nb_inser professors were associated to the course $matiere_name<br />";
			}
	
			$nb_suppr=0;
			for($loop=0;$loop<count($tab_profs_associes);$loop++) {
				if(!in_array($tab_profs_associes[$loop], $login_prof)) {
					$sql="SELECT 1=1 FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (jgp.login='".$tab_profs_associes[$loop]."' AND jgm.id_matiere='$matiere_name' AND jgm.id_groupe=jgp.id_groupe)";
					//echo "$sql<br />";
					$res=mysql_query($sql);
					
					if(mysql_num_rows($res)==0) {
						/*
						$sql="SELECT ordre_matieres FROM j_professeurs_matieres WHERE id_professeur='$login_prof' AND id_matiere='$matiere_name';";
						$res=mysql_query($sql);
						*/
	
						$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur='".$tab_profs_associes[$loop]."' AND id_matiere='$matiere_name';";
						$suppr=mysql_query($sql);
						updateOnline($sql);
						if(!$suppr) {
							$msg.="Error during suppression of association of ".$tab_profs_associes[$loop]." with the course $matiere_name<br />";
						}
						else {
							$nb_suppr++;
						}
					}
					else {
						$msg.="Impossible dissociation: The professor ".$tab_profs_associes[$loop]." teach the course $matiere_name .<br />";
					}
				}
			}
	
			if($nb_suppr>0) {
				$msg.="$nb_suppr professors were dissociated from the course $matiere_name<br />";
			}
	
		}
	}

	//$msg = rawurlencode($msg);
    header("location: index.php?msg=$msg");
    die();

}

$themessage = 'Modifications were carried out. Want you to really leave without recording ?';
//**************** EN-TETE *******************************
$titre_page = "Management of courses | Modify a course";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ****************************
?>
<form enctype="multipart/form-data" action="modify_matiere.php" method=post>
<p class=bold><a href="index.php"<?php echo insert_confirm_abandon();?>><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a> | <input type=submit value=Save></input>
</p>
<?php
echo add_token_field();
// On va chercher les infos de la matière que l'on souhaite modifier
if (isset($_GET['current_matiere'])) {
    $call_data = mysql_query("SELECT nom_complet, priority, categorie_id from matieres WHERE matiere='".$_GET['current_matiere']."'");
    $matiere_nom_complet = mysql_result($call_data, 0, "nom_complet");
    $matiere_priorite = mysql_result($call_data, 0, "priority");
    $matiere_cat_id = mysql_result($call_data, 0, "categorie_id");
    $current_matiere = $_GET['current_matiere'];
} else {
    $matiere_nom_complet = "";
    $matiere_priorite = "0";
    $current_matiere = "";
    $matiere_cat_id = "0";
}
?>

<div style='float:right; width: 40 em; border: 1px solid black; margin-left: 1em;'>
<?php
	$tab_profs_associes=array();
	if($current_matiere!="") {
		$sql="SELECT u.login FROM j_professeurs_matieres jpm, utilisateurs u WHERE jpm.id_professeur=u.login and id_matiere='$current_matiere' ORDER BY u.nom, u.prenom;";
		$res_profs=mysql_query($sql);
		
		if(mysql_num_rows($res_profs)>0) {
			while($lig=mysql_fetch_object($res_profs)) {
				$tab_profs_associes[]=$lig->login;
			}
		}

		if(count($tab_profs_associes)>0) {
			if(count($tab_profs_associes)>1) {
				echo "<p class='bold'>The associated professors are&nbsp;<br />\n";
			}
			elseif(count($tab_profs_associes)==1) {
				echo "<p class='bold'>A professor is associated&nbsp;<br />\n";
			}
			echo "<table class='boireaus' style='margin-left: 1em;'>\n";
			$alt=1;
			for($loop=0;$loop<count($tab_profs_associes);$loop++) {
				$alt=$alt*(-1);
				//echo civ_nom_prenom($tab_profs_associes[$loop],"ini")."<br />\n";
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>\n";
				echo civ_nom_prenom($tab_profs_associes[$loop],"ini");
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}
	}

	$cpt=0;
	$sql="SELECT DISTINCT u.login,u.nom,u.prenom,u.civilite FROM utilisateurs u WHERE u.statut='professeur' AND u.etat='actif' ORDER BY u.nom, u.prenom;";
	$res_profs=mysql_query($sql);
	if(mysql_num_rows($res_profs)>0) {
		echo "<p class='bold'>Associate professors&nbsp;:</p>\n";
		//$cpt=0;
		while($lig=mysql_fetch_object($res_profs)) {
			echo "<input type='checkbox' name='login_prof[]' id='login_prof_$cpt' value='$lig->login' ";
			echo "onchange=\"checkbox_change($cpt)\" ";
			if(in_array($lig->login,$tab_profs_associes)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
			echo "/><label for='login_prof_$cpt'><span id='texte_login_prof_$cpt'$temp_style>".$lig->civilite." ".$lig->nom." ".substr($lig->prenom,0,1).".</span></label><br />\n";
			$cpt++;
		}
	}

	echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('login_prof_'+cpt)) {
		if(document.getElementById('login_prof_'+cpt).checked) {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

?>
</div>

<table>
<tr>
<td>Course name : </td>
<td>
<?php
if (!isset($_GET['current_matiere'])) {
    echo "<input type=text size='19' maxlength='19' name='reg_current_matiere' onchange='changement()' /> (<span style='font-style: italic; font-size: small;'>19 caractères maximum</span>)";
} else {
    echo "<input type=hidden name=matiere_name value=\"".$current_matiere."\" />".$current_matiere;
}
?>
</td></tr>
<tr>
<td>Complete name : </td>
<td><input type='text' name='matiere_nom_complet' value="<?php echo $matiere_nom_complet;?>" onchange='changement()' /></td>
</tr>
<tr>
<td>Default display Priority </td>
<td>
<?php
echo "<select size='1' name='matiere_priorite' onchange='changement()' >\n";
$k = '0';
echo "<option value=0>0</option>\n";
$k='11';
$j = '1';
while ($k < '51'){
    echo "<option value=$k"; if ($matiere_priorite == $k) {echo " SELECTED";} echo ">$j</option>\n";
    $k++;
    $j = $k - 10;
}
echo "</select></td>";
?>
<tr>
<td>Default category</td>
<td>
<?php
echo "<select size='1' name='matiere_categorie' onchange='changement()' >\n";
$get_cat = mysql_query("SELECT id, nom_court FROM matieres_categories");
$test = mysql_num_rows($get_cat);

if ($test == 0) {
    echo "<option disabled>No definite category</option>";
} else {
    while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
        echo "<option value='".$row["id"]."'";
        if ($matiere_cat_id == $row["id"]) echo " SELECTED";
        echo ">".html_entity_decode_all_version($row["nom_court"])."</option>";
    }
}
echo "</select>";

?>
</td>
</table>
<p>
<label for='force_defaut' style='cursor: pointer;'><b>For all the classes, force the value of the priority of display to the default
value above :</b>
<input type="checkbox" name="force_defaut" id="force_defaut" onchange="changement()" checked /></label>
</p>
<p>
<label for='force_defaut_categorie' style='cursor: pointer;'><b>For all the classes, force the value of the category of course to the default
value above :</b>
<input type="checkbox" name="force_defaut_categorie" id="force_defaut_categorie" onchange="changement()" checked /></label>
</p>
<input type="hidden" name="isposted" value="yes" />
</form>
<!-- ============================================================================ -->
<hr />
<p><b>Help :</b></p>
<ul>
<li><b>Course name</b>
<br /><br />It is the identifier of the course. It is made up to the maximum of 20 characters : letters, figures or "_" and should not begin with a figure.
Once recorded, it is not possible any more to modify it.
</li>
<li><b>Complete name</b>
<br /><br />It is the heading of the course, such as it appears to the users on the bulletins, report booklets, etc.
Once recorded, it is always possible to modify it.
</li>
<li><b>Default priority of by  display</b>
<br /><br />Allows to define the default order of display of the courses in the report card and in the summary tables of the averages.
<br /><b>Remarks :</b>
<ul>
<li>During management of the courses in a class, it is this value which is recorded by default. It is then possible to change the value for a given class.</li>
<li>It is possible to allot the same weight to several courses not appearing on the same bulletin. For example, all the LV1 can have the same weight, etc.</li>
<li>If two courses appearing on the same bulletin have the same priority, GEPI displays the first course extracted from the base.</li>
</ul>
</ul>
<!--/li>
</ul-->
<?php require("../lib/footer.inc.php");?>