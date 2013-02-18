<?php
/**
 *  $Id: transfert_cn.php 7748 2011-08-14 14:10:02Z regis $
 * 
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @package Epreuve_blanche
 * @subpackage Transfert
 */

/*
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

//$variables_non_protegees = 'yes';

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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/transfert_cn.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/transfert_cn.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='White test: Transfer to report card',
statut='';";
$insert=mysql_query($sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================


// La boite courante est mise à jour...
// ... mais pas la boite destination.

$id_epreuve=isset($_POST['id_epreuve']) ? $_POST['id_epreuve'] : (isset($_GET['id_epreuve']) ? $_GET['id_epreuve'] : NULL);

if(isset($_GET['creer_cn'])) {
	check_token();

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg="The selected test (<i>$id_epreuve</i>) do not exist.\n";
	}
	else {

		$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;
		$periode=isset($_GET['periode']) ? $_GET['periode'] : NULL;

		if(!isset($id_groupe)) {
			$msg="No group was selected.\n";
		}
		elseif(!isset($periode)) {
			$msg="Aucun période n'a été choisie.\n";
		}
		else {
			$sql="SELECT 1=1 FROM groupes WHERE id='$id_groupe';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				$msg="The group n°$id_groupe chosen does not exist.\n";
			}
			else {
				$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND periode='$periode';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					$msg="No pupil is affected in the n° group $id_groupe over the period $periode.\n";
				}
				else {
					$sql="SELECT 1=1 FROM cn_cahier_notes WHERE id_groupe='$id_groupe' AND periode='$periode';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0) {
						$msg="The book of notes already exists for the n° group $id_groupe over the period $periode.\n";
					}
					else {
						// On va créer le cahier de notes

						$tab_champs=array('matieres');
						$tmp_group=get_group($id_groupe,$tab_champs);

						$nom_complet_matiere = $tmp_group["matiere"]["nom_complet"];
						$nom_court_matiere = $tmp_group["matiere"]["matiere"];
						$reg = mysql_query("INSERT INTO cn_conteneurs SET id_racine='', nom_court='".traitement_magic_quotes($tmp_group["description"])."', nom_complet='". traitement_magic_quotes($nom_complet_matiere)."', description = '', mode = '2', coef = '1.0', arrondir = 's1', ponderation = '0.0', display_parents = '0', display_bulletin = '1', parent = '0'");
						if ($reg) {
							$id_racine = mysql_insert_id();
							$reg = mysql_query("UPDATE cn_conteneurs SET id_racine='$id_racine', parent = '0' WHERE id='$id_racine'");
							$reg = mysql_query("INSERT INTO cn_cahier_notes SET id_groupe = '$id_groupe', periode = '$periode', id_cahier_notes='$id_racine'");
							if(!$reg) {
								$msg="Error during the creation of the book of notes.\n";
							}
							else {
								$msg="Book of notes n° $id_racine created for the group n°$id_groupe over the period $periode.\n";
							}
						}
						else {
							$msg="Error during the creation of a container root for the book of notes.\n";
						}
					}
				}
			}
		}
	}
}

if(isset($_POST['transfert_cn'])) {
	check_token();

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg="The selected test (<i>$id_epreuve</i>) do not exist.\n";
	}
	else {
		$lig_epreuve=mysql_fetch_object($res);
		$intitule=$lig_epreuve->intitule;
		$date_epreuve=$lig_epreuve->date;
		$description=$lig_epreuve->description;
		$etat=$lig_epreuve->etat;
		$note_sur=$lig_epreuve->note_sur;
		$ramener_sur_20="n";
		if(getSettingValue("note_autre_que_sur_referentiel")=="F") {
			$note_sur=20;
			$ramener_sur_20="y";
		}

		if($etat!='clos') {

			$id_cn=isset($_POST['id_cn']) ? $_POST['id_cn'] : (isset($_GET['id_cn']) ? $_GET['id_cn'] : array());

			$msg="";

			$cpt_notes=0;
			for($i=0;$i<count($id_cn);$i++) {
				$tab_tmp=explode("|",$id_cn[$i]);
				$current_periode=$tab_tmp[0];
				$current_id_cn=$tab_tmp[1];

				$transfert="y";
				$sql="SELECT 1=1 FROM periodes p, j_groupes_classes jgc, eb_groupes eg, cn_cahier_notes ccn WHERE p.id_classe=jgc.id_classe AND (p.verouiller='O' OR p.verouiller='P') AND p.num_periode='$current_periode' AND eg.id_epreuve='$id_epreuve' AND eg.id_groupe=jgc.id_groupe AND ccn.id_groupe=jgc.id_groupe AND ccn.id_cahier_notes='$current_id_cn';";
				//echo "$sql<br />";
				$test=mysql_query($sql);
	
				if(mysql_num_rows($test)>0) {
					$transfert="n";
					// AJOUTER UNE ALERTE INTRUSION
				}

				if($transfert=="n") {
					$msg.="The period is (partiellement) closed for at least one of the classes of teaching associated with the book with notes $current_id_cn.<br />";
				}
				else {
					$id_racine=$current_id_cn;
					$id_conteneur=$current_id_cn;

					// Créer le devoir
					$sql="INSERT INTO cn_devoirs SET id_racine='$id_racine', id_conteneur='$id_conteneur', nom_court='nouveau', ramener_sur_referentiel='F', note_sur='$note_sur';";
					//echo "$sql<br />";
					$reg=mysql_query($sql);
					if(!$reg) {
						$msg.="Error during the creation of the duty for teaching associated with the book with notes $current_id_cn.<br />";
					}
					else {
						$id_devoir=mysql_insert_id();
						$new='yes';
						$reg_ok='yes';

						$nom_court=$intitule;
						$sql="UPDATE cn_devoirs SET nom_court='".corriger_caracteres($nom_court)."' WHERE id='$id_devoir'";
						$reg=mysql_query($sql);
						if (!$reg) {$reg_ok = "no";}

						$nom_complet=$nom_court;
						$sql="UPDATE cn_devoirs SET nom_complet='".corriger_caracteres($nom_court)."' WHERE id='$id_devoir'";
						$reg=mysql_query($sql);
						if (!$reg) {$reg_ok = "no";}

						if($description!='')  {
							$sql="UPDATE cn_devoirs SET nom_complet='".corriger_caracteres($description)."' WHERE id='$id_devoir'";
							$reg=mysql_query($sql);
							if (!$reg) {$reg_ok = "no";}
						}

						$tmp_coef=1;
						$sql="UPDATE cn_devoirs SET coef='$tmp_coef' WHERE id='$id_devoir'";
						$reg=mysql_query($sql);
						if (!$reg) {$reg_ok = "no";}

						$sql="UPDATE cn_devoirs SET date='$date_epreuve' WHERE id='$id_devoir'";
						$reg=mysql_query($sql);
						if (!$reg) {$reg_ok = "no";}

						$sql="UPDATE cn_devoirs SET facultatif='O' WHERE id='$id_devoir'";
						$reg=mysql_query($sql);
						if (!$reg) {$reg_ok = "no";}

						$sql="UPDATE cn_devoirs SET display_parents='1' WHERE id='$id_devoir'";
						$reg=mysql_query($sql);
						if (!$reg) {$reg_ok = "no";}


	
	
						// Transférer les notes
						$sql="SELECT DISTINCT ec.login_ele, ec.note, ec.statut FROM eb_copies ec, j_eleves_groupes jeg, cn_cahier_notes ccn WHERE ccn.id_groupe=jeg.id_groupe AND ccn.id_cahier_notes='$current_id_cn' AND ec.id_epreuve='$id_epreuve' AND jeg.periode='$current_periode' AND jeg.login=ec.login_ele;";
						//echo "$sql<br />";
						$res_ele=mysql_query($sql);
						while($lig_ele=mysql_fetch_object($res_ele)) {
							if(getSettingValue("note_autre_que_sur_referentiel")=="F") {
								if($lig_ele->statut=='') {
									$note_courante=round(10*20*$lig_ele->note/$lig_epreuve->note_sur)/10;
									$sql="INSERT INTO cn_notes_devoirs SET login='$lig_ele->login_ele', id_devoir='$id_devoir', note='$note_courante', statut='$lig_ele->statut';";
								}
								else {
									$sql="INSERT INTO cn_notes_devoirs SET login='$lig_ele->login_ele', id_devoir='$id_devoir', note='$lig_ele->note', statut='$lig_ele->statut';";
								}

							}
							else {
								$sql="INSERT INTO cn_notes_devoirs SET login='$lig_ele->login_ele', id_devoir='$id_devoir', note='$lig_ele->note', statut='$lig_ele->statut';";
							}
							//echo "$sql<br />";
							$insert=mysql_query($sql);
							if(!$insert) {
								$msg.="Error during the insertion of the note ($lig_ele->note|$lig_ele->statut) for $lig_ele->login_ele on the duty n° $id_devoir<br />";
								//echo "Erreur lors de l'insertion de la note ($lig_ele->note|$lig_ele->statut) pour $lig_ele->login_ele sur le devoir n°$id_devoir<br />";
							}
							else {
								$cpt_notes++;
							}
						}


						// Préparatifs de la mise à jour des moyennes de conteneurs
						$sql="SELECT id_groupe FROM cn_cahier_notes WHERE id_cahier_notes='$current_id_cn';";
						$res=mysql_query($sql);
						$lig=mysql_fetch_object($res);
						$current_group=get_group($lig->id_groupe);
						$periode_num=$current_periode;

						// Renseignement d'un témoin comme quoi le transfert a déjà été effectué pour le groupe
						$sql="UPDATE eb_groupes SET transfert='y' WHERE id_epreuve='$id_epreuve' AND id_groupe='$lig->id_groupe';";
						$update=mysql_query($sql);

						// Mise à jour des moyennes de conteneurs
						recherche_enfant($id_racine);

					}

				}
			}
		
			if(($msg=='')&&($cpt_notes>0)) {
				$msg="Recording of $cpt_notes notes carried out.";
			}
		}
		else {
			$msg="The selected test (<i>$id_epreuve</i>) is closed.\n";
		}
	}
}

include('lib_eb.php');

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Information was modified. Want you to really leave without recording ?';
//**************** EN-TETE *****************
$titre_page = "White test: Transfer to report card";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php?id_epreuve=$id_epreuve&amp;mode=modif_epreuve'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Return</a>";

if(!isset($id_epreuve)) {
	echo "</p>\n";

	echo "<p>No test was selected.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//==================================================================

echo "</p>\n";

echo "<p class='bold'>Test n° $id_epreuve</p>\n";

$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p>The selected test (<i>$id_epreuve</i>) do not exist.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

$lig=mysql_fetch_object($res);
$etat=$lig->etat;

echo "<blockquote>\n";
echo "<p><b>".$lig->intitule."</b> (<i>".formate_date($lig->date)."</i>)<br />\n";
if($lig->description!='') {
	echo nl2br(trim($lig->description))."<br />\n";
}
else {
	echo "No seized description.<br />\n";
}
echo "</blockquote>\n";


$sql="SELECT g.*,eg.transfert FROM eb_groupes eg, groupes g WHERE eg.id_epreuve='$id_epreuve' AND g.id=eg.id_groupe ORDER BY g.name,g.description;";
$res_grp=mysql_query($sql);
if(mysql_num_rows($res_grp)==0) {
	echo "<p>No group is associated the selected test (<i>$id_epreuve</i>).</p>\n";
	require("../lib/footer.inc.php");
	die();
}

// On remplit un tableau avec toutes les extractions avant de l'afficher pour récupérer le nombre max de périodes
$tab_grp=array();
$cpt=0;
$max_num_per_tt_grp=0;
while($lig=mysql_fetch_object($res_grp)) {
	// Remplir un tableau
	$tab_grp[$cpt]=array();
	$tab_grp[$cpt]['id']=$lig->id;
	$tab_grp[$cpt]['name']=$lig->name;
	$tab_grp[$cpt]['description']=$lig->description;
	$tab_grp[$cpt]['transfert']=$lig->transfert;

	// Récupérer la liste des classes associées
	$sql="SELECT DISTINCT c.classe FROM classes c, j_groupes_classes jgc WHERE c.id=jgc.id_classe AND jgc.id_groupe='$lig->id' ORDER BY c.classe;";
	//echo "$sql<br />";
	$res_clas=mysql_query($sql);
	$clas_list="";
	$cpt2=0;
	while($lig_clas=mysql_fetch_object($res_clas)) {
		if($cpt2>0) {$clas_list.=", ";}
		$clas_list.=$lig_clas->classe;
		$cpt2++;
	}
	$tab_grp[$cpt]['class_list']=$clas_list;

	// Récupérer la liste des profs associés
	$sql="SELECT DISTINCT u.nom,u.prenom,u.civilite FROM utilisateurs u, j_groupes_professeurs jgp WHERE u.login=jgp.login AND jgp.id_groupe='$lig->id' ORDER BY u.nom,u.prenom;";
	//echo "$sql<br />";
	$res_prof=mysql_query($sql);
	$prof_list="";
	$cpt2=0;
	while($lig_prof=mysql_fetch_object($res_prof)) {
		if($cpt2>0) {$prof_list.=", ";}
		$prof_list.=$lig_prof->civilite." ".casse_mot($lig_prof->nom)." ".strtoupper(substr($lig_prof->prenom,0,1));
		$cpt2++;
	}
	$tab_grp[$cpt]['profs_list']=$prof_list;

	// Récupérer la liste des périodes
	$sql="SELECT MAX(p.num_periode) AS max_num_per FROM periodes p, j_groupes_classes jgc WHERE p.id_classe=jgc.id_classe AND jgc.id_groupe='$lig->id' ORDER BY p.num_periode;";
	//echo "$sql<br />";
	$res_per=mysql_query($sql);
	$lig_per=mysql_fetch_object($res_per);

	if($lig_per->max_num_per>$max_num_per_tt_grp) {$max_num_per_tt_grp=$lig_per->max_num_per;}
	for($i=1;$i<=$lig_per->max_num_per;$i++) {
		$sql="SELECT 1=1 FROM periodes p, j_groupes_classes jgc WHERE p.id_classe=jgc.id_classe AND jgc.id_groupe='$lig->id' AND num_periode='$i' AND (verouiller='P' OR verouiller='O');";
		$res_ver=mysql_query($sql);
		if(mysql_num_rows($res_ver)==0) {
			$tab_grp[$cpt]['ver_periode'][$i]='N';
		}
		else {
			$tab_grp[$cpt]['ver_periode'][$i]='O';
		}

		$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='$lig->id' AND periode='$i';";
		$res_cn=mysql_query($sql);
		/*
		if(mysql_num_rows($res_cn)==0) {
			$tab_grp[$cpt]['id_cn'][$i]='';
		}
		else {
			while($lig_cn=mysql_fetch_object($res_cn)) {
			}
		*/
		if(mysql_num_rows($res_cn)>0) {
			$lig_cn=mysql_fetch_object($res_cn);
			$tab_grp[$cpt]['id_cn'][$i]=$lig_cn->id_cahier_notes;
		}
	}

	$cpt++;
}

echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo add_token_field();

echo "<p>Select the report cards on which to create a duty corresponding to the
test.</p>\n";

// Choisir la période parmi celles ouvertes (pb avec les périodes closes sur certaines classes seulement)
echo "<table class='boireaus' summary='Choice of the periods'>\n";
echo "<tr>\n";
echo "<th rowspan='2'>Teaching</th>\n";
echo "<th rowspan='2'>Classes</th>\n";
echo "<th rowspan='2'>Prof(s)</th>\n";
echo "<th colspan='$max_num_per_tt_grp'>Period</th>\n";
echo "</tr>\n";

echo "<tr>\n";
for($i=1;$i<=$max_num_per_tt_grp;$i++) {
	echo "<th style='width:4em;'>$i</th>\n";
}
echo "</tr>\n";

$alt=1;
for($j=0;$j<$cpt;$j++) {
	$alt=$alt*(-1);

	if($tab_grp[$j]['transfert']=='y') {
		echo "<tr style='background-color:gray;' title='Transfer to the report card already carried out'>\n";
	}
	else {
		echo "<tr class='lig$alt'>\n";
	}

	echo "<td>".$tab_grp[$j]['name']."</td>\n";
	echo "<td>".$tab_grp[$j]['class_list']."</td>\n";
	echo "<td>".$tab_grp[$j]['profs_list']."</td>\n";
	for($i=1;$i<=$max_num_per_tt_grp;$i++) {
		if($tab_grp[$j]['ver_periode'][$i]=='O') {
			echo "<td><span title='Closed or partially closed";
			if(ereg(",",$tab_grp[$j]['class_list'])) {echo " for one of the classes of teaching at least";}
			echo "'>Closed</span></td>\n";
		}
		elseif(isset($tab_grp[$j]['id_cn'][$i])) {
			echo "<td>";
			//echo "<input type='checkbox' name='id_cn[]' value='".$tab_grp[$j]['id_cn'][$i]."' />";
			//echo $tab_grp[$j]['id_cn'][$i];
			echo "<input type='checkbox' name='id_cn[]' id='checkbox_".$i."_".$tab_grp[$j]['id_cn'][$i]."' value='$i|".$tab_grp[$j]['id_cn'][$i]."' ";
			if($tab_grp[$j]['transfert']=='y') {
				echo "onchange=\"alert_transfert('checkbox_".$i."_".$tab_grp[$j]['id_cn'][$i]."')\" ";
			}
			echo "/>";
			echo "</td>\n";
		}
		elseif(isset($tab_grp[$j]['ver_periode'][$i])) {
			echo "<td><img src='../images/icons/flag.png' width='17' height='18' title='Book of note not initialized for this period' alt='Book of note not initialized for this period' /> <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;id_groupe=".$tab_grp[$j]['id']."&amp;periode=$i&amp;creer_cn=y".add_token_in_url()."'><img src='../images/icons/wizard.png' width='16' height='16' title='To create the book of note' alt='To create the book of note' /></a></td>\n";
		}
		else {
			echo "<td>-</td>\n";
		}
	}
	echo "</tr>\n";
}

echo "</table>\n";

if(getSettingValue("note_autre_que_sur_referentiel")=="F") {
	echo "<p><span style='font-weight:bold; color:red;'>ATTENTION</span>&nbsp;: The notes in the report cards are authorized only on 20.<br />If you do not authorize the professors to seize notes on another
reference frame only 20, the notes will be brought back on 20 during the transfer in the report
card.<br />On the other hand, if you wish to authorize the notes on other
reference frames, <a href='../cahier_notes_admin/index.php'>follow this bond</a>.</p>\n";
}


echo "<script type='text/javascript'>
function alert_transfert(id) {
	if(document.getElementById(id).checked) {
		alert('Etes-vous sur de vouloir transférer à nouveau les résultats?')
	}
}
</script>\n";

echo " <input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
echo "<p><input type='submit' name='transfert_cn' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";
echo "<p style='color:red;'>TO MAKE:</p>\n";
echo "<ul>\n";
echo "<li><p style='color:red;'>To rock the state in a state closed or prohibiting at least to modify
the notes.</p></li>\n";
echo "<li><p style='color:red;'>To be able to export these results with the format CSV.</p></li>\n";
//echo "<li><p style='color:red;'>Ajouter un témoin comme quoi le transfert a déjà été effectué.</p></li>\n";
echo "</ul>\n";

require("../lib/footer.inc.php");
?>
