<?php
/*
* @version: $Id: verrouillage.php 8451 2011-10-07 11:57:57Z crob $
*
* Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//pour gérer le verrouillage de la période depuis le fichier Vérif_bulletins.php
$classe=isset($_GET['classe']) ? $_GET['classe'] : 0;
$periode=isset($_GET['periode']) ? $_GET['periode'] : 0;
// quelle action après le verrouillage ?
$action_apres=isset($_GET['action']) ? $_GET['action'] : NULL;


if (isset($_POST['deverouillage_auto_periode_suivante'])) {
	check_token();
	if (!saveSetting("deverouillage_auto_periode_suivante", $_POST['deverouillage_auto_periode_suivante'])) {
		$msg .= "Error during the recording of deverouillage_auto_periode_suivante !";
		$reg_ok = 'no';
	}
}

if (isset($_POST['ok'])) {
	check_token();

	$pb_reg_ver = 'no';
	//$calldata = sql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	$calldata = sql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	if ($calldata) {
		for ($k = 0; ($row = sql_row($calldata, $k)); $k++) {
			$id_classe = $row[0];
			$periode_query = sql_query("SELECT verouiller, date_fin FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
			$nb_periode = sql_count($periode_query) + 1 ;
			if ($periode_query) {
				for ($i = 0; ($row_per = sql_row($periode_query, $i)); $i++) {
					$nom_classe = "cl_".$id_classe."_".$i;
					//echo "\$nom_classe=$nom_classe<br />";
					//echo "\$row_per[0]=$row_per[0]<br />";
					$t = $i+1;
					//if (isset($_POST[$nom_classe]))  {
					if ((isset($_POST[$nom_classe]))&&($_POST[$nom_classe]!=$row_per[0]))  {
						//$register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."' WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						if ($row_per[1] == 0) {//la date de fin n'est pas renseignee, on la renseigne
						    $register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."', date_verrouillage=NOW(), date_fin=NOW() WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						    if (!$register) {$pb_reg_ver = 'yes';}
						} else {
						    $register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."', date_verrouillage=NOW() WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						    if (!$register) {$pb_reg_ver = 'yes';}
						}
					}
					if ((isset($_POST["date_fin_".$nom_classe]))&&($_POST["date_fin_".$nom_classe]!=""))  {
						try {
						    $date_fin = new DateTime(str_replace("/",".",$_POST["date_fin_".$nom_classe]));
						    if ($date_fin->format('U') != $row_per[1]) {
							$register = sql_query("UPDATE periodes SET date_fin='".$date_fin->format('Y-m-d')."' WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
							if (!$register) {$pb_reg_ver = 'yes';}
						    }
						    //$register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."' WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						    //$register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."', date_verrouillage=NOW() WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						    //if (!$register) {$pb_reg_ver = 'yes';}
						} catch (Exception $x) {
						}
					}
				}
			}
		}
	}
	
	// Déverrouillage de la période suivante si le bouton radio est à Oui.
	//if ((($action_apres == 'retour') OR ($action_apres == 'imprime_html') OR ($action_apres == 'imprime_pdf') OR ($action_apres == 'rien')) AND isset($_POST['deverouillage_auto_periode_suivante'])) {
	if ((($action_apres == 'retour') OR ($action_apres == 'imprime_bull') OR ($action_apres == 'rien')) AND isset($_POST['deverouillage_auto_periode_suivante'])) {
		if (($_POST['deverouillage_auto_periode_suivante'])=='y') {
			//recherche du nombre de période pour la classe
			$sql_periode = "SELECT * FROM periodes WHERE id_classe='$classe';";
			$result_periode = mysql_query($sql_periode);
			$nb_periodes_classe = mysql_num_rows($result_periode);
			//echo $nb_periodes_classe;
			$periode_en_cours = $periode;
			$periode_suivante = $periode+1;
			//Pour la période modifiée on récupère son état
			$etat_periode=mysql_result($result_periode, $periode-1, "verouiller");
			//echo "<br/>".$etat_periode;
			//echo "<br/>".$periode_en_cours;
			//echo "<br/>".$nb_periodes_classe;
			//si l'état est P ou O on dévérouille totalement la période +1 (di elle existe !)
			if (($etat_periode=='P') OR $etat_periode=='O') {
				if ($periode_en_cours  < $nb_periodes_classe) {
				//echo "<br/>On déverrouille $periode_suivante";
				//$sql_maj_periode_suivante = "UPDATE periodes SET verouiller='N' WHERE (num_periode='".$periode_suivante."' and id_classe='".$classe."')";
				//$sql_maj_periode_suivante = "UPDATE periodes SET verouiller='N', date_verrouillage='".time()."' WHERE (num_periode='".$periode_suivante."' and id_classe='".$classe."')";
				$sql_maj_periode_suivante = "UPDATE periodes SET verouiller='N', date_verrouillage=NOW() WHERE (num_periode='".$periode_suivante."' and id_classe='".$classe."')";
				//echo "<br/>".$sql_maj_periode_suivante;
				$result_maj_periode_suivante = mysql_query($sql_maj_periode_suivante);
				if (!$result_maj_periode_suivante) {$pb_reg_ver = 'yes';}
				}
			}
		}
	}
	
	if ($pb_reg_ver == 'no') {
		$msg = "The modifications were recorded.";
	} else {
		$msg = "There was a problem during the recording of the data.";
	}

	if ($action_apres == 'retour') {
		header("Location: ./verif_bulletins.php");
	}

	//if ($action_apres == 'imprime_html') {
	if ($action_apres == 'imprime_bull') {
		//header("Location: ./index.php?id_classe=$classe");
		//echo "Location: ./bull_index.php?tab_id_classe[0]=$classe&tab_periode_num[0]=$periode&choix_periode_num=fait";
		header("Location: ./bull_index.php?tab_id_classe[0]=$classe&tab_periode_num[0]=$periode&choix_periode_num=fait");
	}
	/*
	if ($action_apres == 'imprime_pdf') {
		//header("Location: ./index.php?format=pdf");
		header("Location: ./bull_index.php?tab_id_classe[0]=$classe&tab_periode_num[0]=$periode&choix_periode_num=fait");
	}
	*/
}

$themessage  = 'Information was modified. Do you really want to leave without recording ?';
//**************** EN-TETE **************************************
$titre_page = "Locking and unlocking of the periods";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************
//debug_var();
?>
<script type='text/javascript' language='javascript'>
function CocheCase(rang,per) {
	nbelements = document.formulaire.elements.length;
	for (i=0;i<nbelements;i++) {
		if (document.formulaire.elements[i].type =='hidden') {
			if (document.formulaire.elements[i].value ==per) {
				document.formulaire.elements[i+1].checked = false ;
				document.formulaire.elements[i+2].checked = false ;
				document.formulaire.elements[i+3].checked = false ;
				document.formulaire.elements[i+rang].checked = true ;
			}
		}
	}
}
</script>
<?php
echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>";

if($_SESSION['statut']=='scolarite') {
	echo " | <a href='bull_index.php'>Visualization and impression of the bulletins</a>";
}

if(($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiScolImprBulSettings')=='yes')) {
	echo " | <a href='param_bull.php'>Parameter setting of bulletins</a>";
}
echo "</p>\n";

$texte_deverrouiller = urlencode("Déverrouiller");
$texte_verrouiller_part = urlencode("Verrouiller part.");
$texte_verrouiller_tot = urlencode("Verrouiller tot.");

// si la classe et la période sont définies (on vient de verif_bulletiin.php)
if (!(($classe != 0) AND ($periode !=0))) {
	// On va chercher les classes déjà existantes, et on les affiche.
	$max_per = sql_query1("SELECT num_periode FROM periodes ORDER BY num_periode DESC LIMIT 1");
	//$calldata = sql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	$calldata = mysql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	$nombreligne = sql_count($calldata);
	echo "Total : $nombreligne classes\n";
}

echo "<ul>
<li>When a period is <b>unlocked</b>,the filling of all the headings (notes, appreciations, opinion) is authorized, the visualization of
simple bulletins is authorized but visualization and the impression of the official bulletins are impossible.<br /><br /></li>
<li>When a period is <b>locked partially</b>, only filling and/or modification
in the opinion of staff meeting";
if ($gepiSettings['active_mod_ects'] == 'y') echo "and of ECTS credits";
echo "are possible. The visualization and the impression of the official bulletins are authorized.<br /><br /></li>
<li>When a period is <b>completely locked </b>,the filling and modification of the bulletin for the period concerned
are impossible. visualization and the impression are authorized.</li>\n";
echo "</ul>\n";
echo "<br /><br />\n";


// si la classe et la période sont définies (on vient de verif_bulletin.php)
if (($classe != 0) AND ($periode !=0)) {

	echo "<form action=\"verrouillage.php?classe=$classe&periode=$periode&action=$action_apres\" name=\"formulaire\" method=\"post\">\n";
	echo add_token_field();
	echo "<table class='boireaus' cellpadding='3' cellspacing='0' border='1' align='center'>\n";
	//echo "<tr class='fond_sombre'>\n<td>&nbsp;</td>\n";
	echo "<tr>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>\n";
	echo "</th>\n";
	echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_deverrouiller."&amp;width=22\" width=\"22\" border='0' alt=\"Unlocked\" /></th>\n";
	echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_part."&amp;width=22\" width=\"22\" border='0' alt=\"Lock partially\" /></th>\n";
	echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_tot."&amp;width=22\" width=\"22\" border='0' alt=\"Lock completely\" /></th>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	// Affichage de la classe (nom court)
	$sql_classe = "SELECT classe FROM classes WHERE id = '$classe'";
	$requete_classe = sql_query($sql_classe);
	$donner_modele = mysql_fetch_array($requete_classe);
	$nom_court_classe = $donner_modele['classe'];
	echo "<td><b>$nom_court_classe</b> ";
	echo "</td>\n";

	$sql_periode = "SELECT nom_periode, verouiller FROM periodes WHERE (id_classe = '$classe' AND num_periode='$periode')";
	$periode_query = sql_query($sql_periode);
	$nb_periode = sql_count($periode_query) + 1 ;
	$j = 0;

	//ajustement de l'indice periode 1, 2 , 3 dans la base en réalité : 0, 1, 2
	$indice_periode = $periode-1;
	if ($periode_query) for ($i = 0; ($row_per = sql_row($periode_query, $i)); $i++) {
		$nom_classe = "cl_".$classe."_".$indice_periode;
		echo "<td>".ucfirst($row_per[0])."</td>\n";
		//echo "<input type=\"hidden\" name=\"numperiode\" value=\"$i\" />";
		echo "<td><input type=\"hidden\" name=\"numperiode\" value=\"$indice_periode\" />";
		//echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"N\" ";
		echo "<input type=\"radio\" name=\"".$nom_classe."\" value=\"N\" ";
		if ($row_per[1] == "N") echo "checked";
		echo " /></td>\n";
		echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"P\" ";
		if ($row_per[1] == "P") echo "checked";
		echo " /></td>\n";
		echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"O\" ";
		if ($row_per[1] == "O") echo "checked";
		echo " /></td>\n";
		$j++;
	}

	echo "</table>\n<br />\n";

	// Option de déverrouillage automatique
	echo "<br />\n<table align='center'>\n";
	echo "<tr>\n";
	echo "<td>\n Also proceed to the automatic unlock of the next period <br />during the partial or total locking of the period above : ";
	
	echo "\n</td>\n<td>\n";

		echo "<input type=\"radio\" name=\"deverouillage_auto_periode_suivante\" id='deverouillage_auto_periode_suivante_y' value=\"y\" ";
		if (getSettingValue("deverouillage_auto_periode_suivante") == 'y') echo " checked";
		echo " /><label for='deverouillage_auto_periode_suivante_y'>&nbsp;Oui</label>\n";
		echo "<input type=\"radio\" name=\"deverouillage_auto_periode_suivante\" id='deverouillage_auto_periode_suivante_n' value=\"n\" ";
		if (getSettingValue("deverouillage_auto_periode_suivante") != 'y') echo " checked";
		echo " /><label for='deverouillage_auto_periode_suivante_n'>&nbsp;Non</label>\n";

	echo "\n</td>\n</tr>\n</table>\n<br />\n";

	if ($action_apres == 'rien') {

	echo "<center><input type=\"submit\" name=\"ok\" value=\"Register\" /></center>\n";

	//} elseif ($action_apres == 'imprime_html') {
	} elseif ($action_apres == 'imprime_bull') {

	echo "<center><input type=\"submit\" name=\"ok\" value=\"Register then go to the page of impression of the bulletins\" /></center>\n";
	}
	/*
	elseif ($action_apres == 'imprime_pdf') {
	echo "<center><input type=\"submit\" name=\"ok\" value=\"Enregistrer puis aller à la page impression PDF\" /></center>\n";
	}
	*/
	elseif ($action_apres == 'retour') {

	echo "<center><input type=\"submit\" name=\"ok\" value=\"Register then return to the verification page\" /></center>\n";

	}

	echo "</form>\n";

} else {
	if ($nombreligne != 0) {
		echo "<form action=\"verrouillage.php\" name=\"formulaire\" method=\"post\">";
		echo add_token_field();

		echo "<p align='center'><input type=\"submit\" name=\"ok\" value=\"Save\" /></p>\n";
		//echo "<table cellpadding='3' cellspacing='0' border='1' align='center'>";
		echo "<table class='boireaus' summary='Locking of periods' cellpadding='3' cellspacing='0' align='center'>\n";
		echo "<tr class='fond_sombre'><th>&nbsp;</th>\n";
		for ($i = 0; $i < $max_per; $i++) {
			echo "<th colspan='2'>\n";
			echo "<a href=\"javascript:CocheCase(1,".$i.")\">Unlock All</a><br />\n";
			echo "<a href=\"javascript:CocheCase(2,".$i.")\">Partially Unlock All</a><br />\n";
			echo "<a href=\"javascript:CocheCase(3,".$i.")\">completely lock All</a>\n";
			echo "</th>\n";
			echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_deverrouiller."&amp;width=22\" width=\"22\" border=0 alt=\"Unlock\" /></th>\n";
			echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_part."&amp;width=22\" width=\"22\" border=0 alt=\"Lock Partially\" /></th>\n";
			echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_tot."&amp;width=22\" width=\"22\" border=0 alt=\"Lock completely\" /></th>\n";
            if(getSettingValue("active_module_absence")=="2"){
                echo "<th>Date Fin</th>\n";
            }
		}
		echo "</tr>\n";
		//$flag = 0;
			$alt=1;
		if ($calldata) {
			for ($k = 0; ($row = sql_row($calldata, $k)); $k++) {
				$id_classe = $row[0];
				$classe = $row[1];
				$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'";
				//if ($flag==1) { echo " class='fond_sombre'"; $flag = 0;} else {$flag=1;};
				echo ">\n";
				echo "<td>\n";
				echo "<b>$classe</b> ";
				echo "</td>\n";
		
				$periode_query = sql_query("SELECT nom_periode, verouiller, date_fin FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
				$nb_periode = sql_count($periode_query) + 1 ;
				$j = 0;
				if ($periode_query) {
					for ($i = 0; ($row_per = sql_row($periode_query, $i)); $i++) {
						$nom_classe = "cl_".$id_classe."_".$i;
						echo "<td>".ucfirst($row_per[0])."</td>\n";
			
						if ($row_per[1] == "N") {
							echo "<td id='c_".$id_classe."_".$i."' style='font-size:small; color:green;'>Opened</td>\n";
						}
						elseif ($row_per[1] == "P") {
							echo "<td id='c_".$id_classe."_".$i."' style='font-size:small; color:orange;'>Partial.clos</td>\n";
						}
						elseif ($row_per[1] == "O") {
							echo "<td id='c_".$id_classe."_".$i."' style='font-size:small; color:red;'>Clos</td>\n";
						}
						else {
							// Ca ne devrait pas arriver
							echo "<td>&nbsp;</td>\n";
						}
			
						//echo "<input type=\"hidden\" name=\"numperiode\" value=\"$i\" />";
						echo "<td><input type=\"hidden\" name=\"numperiode\" value=\"$i\" />";
						//echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"N\" ";
						echo "<input type=\"radio\" name=\"".$nom_classe."\" value=\"N\" onchange=\"changement();actualise_cell_($id_classe,$i);\" ";
						if ($row_per[1] == "N") {echo "checked";}
						echo " /></td>\n";
						echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"P\" onchange=\"changement();actualise_cell_($id_classe,$i);\" ";
						if ($row_per[1] == "P") {echo "checked";}
						echo " /></td>\n";
						echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"O\" onchange=\"changement();actualise_cell_($id_classe,$i);\" ";
						if ($row_per[1] == "O") {echo "checked";}
						echo " /></td>\n";
                        if(getSettingValue("active_module_absence")=="2"){
                            echo "<td>";
                            echo "<input type=\"text\" size=\"8\" name=\"date_fin_".$nom_classe."\" value=\"";
                            if ($row_per[2] != 0) {
                                echo date("d/m/Y", strtotime($row_per[2]));
                            }
                            echo "\"/>";
                            echo "</td>\n";
                        }
						$j++;
					}
				}
				for ($i = $j; $i < $max_per; $i++) echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n";
		
				echo "</tr>\n";
			}
		}
		echo "</table>\n";
		echo "<br />\n";
		echo "<center><input type=\"submit\" name=\"ok\" value=\"Register\" /></center>\n";
		echo "</form>\n";
	
		echo "<script type='text/javascript'>
		function actualise_cell_(id_classe,i) {
			// id_classe correspond à la ligne (pas nécessairement le numéro de ligne)
			// i correspond au numéro de la période -1 (colonne)
	
			for (j=0;j<$max_per;j++) {
	
				if (eval('document.formulaire.cl_'+id_classe+'_'+i+'[j].checked')==true) {
					//alert('Classe '+id_classe+' période '+i+' état '+eval('document.formulaire.cl_'+id_classe+'_'+i+'[j].value'));
					if(eval('document.formulaire.cl_'+id_classe+'_'+i+'[j].value')=='N') {
						// Période ouverte en saisie
						document.getElementById('c_'+id_classe+'_'+i).innerHTML='Ouvert (*)';
						document.getElementById('c_'+id_classe+'_'+i).style.color='green';
					}
	
					if(eval('document.formulaire.cl_'+id_classe+'_'+i+'[j].value')=='P') {
						// Période ouverte en saisie
						document.getElementById('c_'+id_classe+'_'+i).innerHTML='Partiel.clos (*)';
						document.getElementById('c_'+id_classe+'_'+i).style.color='orange';
					}
	
					if(eval('document.formulaire.cl_'+id_classe+'_'+i+'[j].value')=='O') {
						// Période ouverte en saisie
						document.getElementById('c_'+id_classe+'_'+i).innerHTML='Clos (*)';
						document.getElementById('c_'+id_classe+'_'+i).style.color='red';
					}
				}
			}
		}
	</script>\n";

		echo "<br />\n";

		echo "<p><i>Notice&nbsp;:</i><br /><span style='margin-left: 3em;'> If you don't see all the classes, it may be that certain classes are not associated for you.</span><br /><span style='margin-left: 3em;'>Then ask to an administrator account to associate classes to you in <b>Management of the bases/Management of the classes/Setting schooling</b></span></p>\n";

	}
	else {
		echo "<p class='grand'>Caution : no class as been defined in GEPE base!<br />Or any class is associated to you (<i>then ask to an administrator account to associate classes to you in <b>Management of the bases/Management of the classes/Setting schooling</b></i>)</p>\n";
	}
} //else
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>