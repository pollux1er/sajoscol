<?php
/*
 * $Id : $
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

// INSERT INTO droits VALUES ('/mod_annees_anterieures/nettoyer_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression de donn�es ant�rieures', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


// Si le module n'est pas activ�...
if(getSettingValue('active_annees_anterieures')!="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'acc�s illicite?

	header("Location: ../logout.php?auto=1");
	die();
}

// si le plugin "port_folio" existe et est activ�
$test_plugin = sql_query1("select ouvert from plugins where nom='port_folio'");
if ($test_plugin=='y') $flag_port_folio='y';

$confirmer=isset($_POST['confirmer']) ? $_POST['confirmer'] : NULL;
$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : NULL;

$msg="";
if(isset($confirmer)) {
	check_token();

	$nb_suppr=0;
	$nb_err=0;
	for($i=0;$i<count($suppr);$i++){
    $sql="DELETE FROM archivage_eleves WHERE ine='$suppr[$i]';";
		$res_suppr1=mysql_query($sql);

		$sql="DELETE FROM archivage_eleves2 WHERE ine='$suppr[$i]';";
		$res_suppr2=mysql_query($sql);
		$sql="DELETE FROM archivage_aid_eleve WHERE id_eleve='$suppr[$i]';";
		$res_suppr3=mysql_query($sql);
		$sql="DELETE FROM archivage_appreciations_aid WHERE id_eleve='$suppr[$i]';";
		$res_suppr4=mysql_query($sql);
		$sql="DELETE FROM archivage_disciplines WHERE INE='$suppr[$i]';";
		$res_suppr5=mysql_query($sql);
		$sql="DELETE FROM archivage_ects WHERE INE='$suppr[$i]';";
		$res_suppr6=mysql_query($sql);
    if (isset($flag_port_folio)) {
      $sql="DELETE FROM port_folio_validations_archives  WHERE login='$suppr[$i]';";
  		mysql_query($sql);
    }

		if (($res_suppr1) and ($res_suppr2) and ($res_suppr3) and ($res_suppr4)  and ($res_suppr5) and ($res_suppr6)) {
			$nb_suppr++;
		}
		else{
			$nb_err++;
		}
	}
	if($nb_suppr>0){
		if($nb_suppr==1){$s="";}else{$s="s";}
		$msg.="Former data of $nb_suppr ancien$s �l�ve$s were removed.";
	}
	if($nb_err>0){
		if($nb_err==1){$s="";}else{$s="s";}
		if($msg!=""){$msg.="<br />";}
		$msg.="For $nb_err ancien$s �l�ve$s, problems were encountered.";
	}
}

$style_specifique="mod_annees_anterieures/annees_anterieures";

$themessage="Modifications were carried out. Do you want to really leave without recording?";

//**************** EN-TETE *****************
$titre_page = "Cleaning of the former data";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<div class='norme'><p class=bold><a href='";
echo "index.php";
echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>\n";

echo "</div>\n";


$sql="SELECT DISTINCT a.nom,a.prenom,a.ine,a.naissance
			FROM archivage_eleves a
			LEFT JOIN eleves e
			ON a.ine=e.no_gep
			WHERE e.no_gep IS NULL;";
$res1=mysql_query($sql);
$nb_ele=mysql_num_rows($res1);
if($nb_ele==0){
	echo "<p>All student present in the table 'annees_anterieures' sont dans la table 'eleves'.</p>\n";
}
else{
	echo "<p>Here the list of the student present in the table 'archivage_eleves', but absent from the table 'eleves'.<br />
	They are normally pupils having left the establishment.<br />
	It can however happen that student whose number INE was not (<i>correctly</i>) filled at the time of the conservation of the year is proposed in the
list below.<br />
	In this case, number INE used has a prefix LOGIN_.<br />
	It is not a correct identifier because the login of a pupil is not
necessarily fixed one year on the other (<i>dans le cas des doublons</i>).<br />
	You can also choose <a href='corriger_ine.php'>Correct nonwell informed or badly well informed INE</a></p>\n";

	echo "<form name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();

	echo "<table align='center' class='table_annee_anterieure boireaus' summary='Tableau des �l�ves'>\n";
	echo "<tr style='background-color:white;'>\n";
	echo "<th>Remove<br />";
	echo "<a href='javascript:modif_coche(true)'><img src='../images/enabled.png' width='15' height='15' alt='All tick' /></a>/\n";
	echo "<a href='javascript:modif_coche(false)'><img src='../images/disabled.png' width='15' height='15' alt='All shoot' /></a>\n";
	echo "</th>\n";
	echo "<th>student</th>\n";
	echo "<th>Date of birth</th>\n";
	echo "<th>N�INE</th>\n";
	echo "</tr>\n";
	$cpt=0;
	$alt=1;
	while($lig_ele=mysql_fetch_object($res1)){
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover' style='text-align:center;' id='tr_$cpt'>\n";
		echo "<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value='$lig_ele->ine' onchange=\"modif_une_coche('$cpt');\" /></td>\n";
		echo "<td>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</td>\n";
		echo "<td>".formate_date($lig_ele->naissance)."</td>\n";
		echo "<td>";
		echo "<a href='consultation_annee_anterieure.php?ine=$lig_ele->ine'>";
		if(substr($lig_ele->ine,0,6)=="LOGIN_") {echo "<span style='color:red;'>";}
		echo $lig_ele->ine;
		if(substr($lig_ele->ine,0,6)=="LOGIN_"){echo "</span>";}
		echo "</a>";
		echo "</td>\n";
		echo "</tr>\n";
		$cpt++;
	}
	echo "</table>\n";

	echo "<p align='center'><input type='submit' name='confirmer' value='Supprimer' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript' language='javascript'>
	function modif_coche(statut){
		// statut: true ou false
		for(k=0;k<$cpt;k++){
			if(document.getElementById('suppr_'+k)){
				document.getElementById('suppr_'+k).checked=statut;

				if(statut==true){
					document.getElementById('tr_'+k).style.backgroundColor='orange';
				}
				else{
					document.getElementById('tr_'+k).style.backgroundColor='';
				}
			}
		}
		changement();
	}

	function modif_une_coche(ligne){
		statut=document.getElementById('suppr_'+ligne).checked;

		if(statut==true){
			document.getElementById('tr_'+ligne).style.backgroundColor='orange';
		}
		else{
			document.getElementById('tr_'+ligne).style.backgroundColor='';
		}
		changement();
	}
</script>\n";

}

require("../lib/footer.inc.php");
?>
