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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


// Si le module n'est pas activé...
if(getSettingValue('active_annees_anterieures')!="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'accès illicite?

	header("Location: ../logout.php?auto=1");
	die();
}




$confirmer=isset($_POST['confirmer']) ? $_POST['confirmer'] : NULL;

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$ine=isset($_POST['ine']) ? $_POST['ine'] : (isset($_GET['ine']) ? $_GET['ine'] : NULL);
$ine_corrige=isset($_POST['ine_corrige']) ? $_POST['ine_corrige'] : (isset($_GET['ine_corrige']) ? $_GET['ine_corrige'] : NULL);

$recherche1=isset($_POST['recherche1']) ? $_POST['recherche1'] : NULL;
$recherche1_nom=isset($_POST['recherche1_nom']) ? $_POST['recherche1_nom'] : NULL;
$recherche1_prenom=isset($_POST['recherche1_prenom']) ? $_POST['recherche1_prenom'] : NULL;

$msg="";
if(isset($confirmer)) {
	check_token();

	$cpt=0;
	if((isset($ine))&&(isset($ine_corrige))) {
		for($i=0;$i<count($ine);$i++){
			if($ine_corrige[$i]!=''){
				$sql="UPDATE archivage_eleves SET ine='$ine_corrige[$i]' WHERE ine='$ine[$i]'";
				$update1=mysql_query($sql);
				$sql="UPDATE archivage_eleves2 SET ine='$ine_corrige[$i]' WHERE ine='$ine[$i]'";
				$update2=mysql_query($sql);
				$sql="UPDATE archivage_aid_eleve SET id_eleve='$ine_corrige[$i]' WHERE id_eleve='$ine[$i]'";
				$update3=mysql_query($sql);
				$sql="UPDATE archivage_appreciations_aid SET id_eleve='$ine_corrige[$i]' WHERE id_eleve='$ine[$i]'";
				$update4=mysql_query($sql);
				$sql="UPDATE archivage_disciplines SET INE='$ine_corrige[$i]' WHERE INE='$ine[$i]'";
				$update5=mysql_query($sql);
				if ((!$update1) or (!$update2) or (!$update3) or (!$update4) or (!$update5)){
					$msg.="<b>Error</b> $ine[$i] -&gt; $ine_corrige[$i]<br />\n";
					//$msg.="$sql<br />\n";
				}
				else{
					$cpt++;
				}
			}
		}
	}
	else{
		// Ca ne devrait pas arriver: Soit tout est renseigné, soit rien n'est renseigné et on a pas validé le formulaire.
		$msg="Fields were not correctly indicated.";
	}

	if(($msg=="")&&($cpt>0)){$msg="Successful recording.";}
}


$style_specifique="mod_annees_anterieures/annees_anterieures";

$themessage="Modifications were carried out. Want you to really leave without recording?";

//**************** EN-TETE *****************
$titre_page = "Correction of INE for the former data";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<script type="text/javascript" language="JavaScript">
	function get_eleves(f) {
		/*
		var l1    = f.elements["classe"];
		var l2    = f.elements["eleve"];
		var index = l1.selectedIndex;
		if(index < 1)
			l2.options.length = 0;
		else {
		*/
			var xhr_object = null;

			if(window.XMLHttpRequest) // Firefox
				xhr_object = new XMLHttpRequest();
			else if(window.ActiveXObject) // Internet Explorer
				xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
			else { // XMLHttpRequest non supporté par le navigateur
				alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
				return;
			}

			xhr_object.open("POST", "liste_eleves_ajax.php", true);

			xhr_object.onreadystatechange = function() {
				if(xhr_object.readyState == 4)
					eval(xhr_object.responseText);
			}

			xhr_object.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			//var data = "id_classe="+escape(l1.options[index].value)+"&form="+f.name+"&select=eleve";
			var data = "nom_ele="+escape(f.nom_ele.value)+"&prenom_ele="+escape(f.prenom_ele.value)+"&form="+f.name;
			xhr_object.send(data);
		//}
	}
</script>

<?php
echo "<div class='norme'><p class=bold><a href='";
echo "index.php";
echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>\n";

if(!isset($mode)){
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>It happens that at the time of the conservation of the one year data, number INE of a student is not (<i>correctly</i>) filled.<br />This number is used to establish the link between a pupil of the current year (<i>table 'student'</i>) and its former data.<br />If this number does not coincide between the two tables, the consultation is disturbed.</p>\n";
	echo "<p>This page is intended to correct INE registered in the tables of
filing.</p>\n";

	echo "<p>You want:</p>\n";
	echo "<ul>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=ine_login'>to post the pupils whose INE was not filled at the time of the conservation of the former data</a>.</li>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=recherche'>search a student</a></li>\n";
	echo "</ul>\n";
}
elseif($mode=="ine_login"){
	echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Correction of INE</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Display of the student whose number INE was not filled At the time of
has conservation of the to form dated.</p>\n";

	$sql="SELECT DISTINCT ine,nom,prenom,naissance FROM archivage_eleves WHERE ine LIKE 'LOGIN_%' ORDER BY nom,prenom";
	$res1=mysql_query($sql);

	if(mysql_num_rows($res1)==0){
		echo "<p>No student in the table 'archivage_eleves' has the prefix INE 'LOGIN_'<br />(<i>i.e. whose INE was not filled at the time of an operation of conservation of the former data</i>).</p>\n";
	}
	else{

		echo "<form name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo "<input type='hidden' name='mode' value=\"ine_login\" />\n";
		echo add_token_field();

		echo "<table class='table_annee_anterieure' summary='Tableau des élèves'>\n";
		echo "<tr style='background-color: white;'>\n";
		echo "<th>Recorded INE</th>\n";
		echo "<th>Name</th>\n";
		echo "<th>First name</th>\n";
		echo "<th>Date of birth</th>\n";
		echo "<th>Corrected INE</th>\n";
		echo "<th>search</th>\n";
		echo "</tr>\n";

		$cpt=0;
		$alt=-1;
		while($lig1=mysql_fetch_object($res1)){
			$alt=$alt*(-1);
			echo "<tr style='background-color:";
			if($alt==1){
				echo "silver";
			}
			else{
				echo "white";
			}
			echo "; text-align: center;'>\n";

			echo "<td style='color: red;'>";
			echo $lig1->ine;
			echo "<input type='hidden' name='ine[$cpt]' value=\"$lig1->ine\" />\n";
			echo "</td>\n";

			echo "<td>";
			echo $lig1->nom;
			echo "<input type='hidden' name='nom_eleve[$cpt]' id='nom_eleve_$cpt' value=\"$lig1->nom\" />\n";
			echo "</td>\n";

			echo "<td>";
			echo $lig1->prenom;
			echo "<input type='hidden' name='prenom_eleve[$cpt]' id='prenom_eleve_$cpt' value=\"$lig1->prenom\" />\n";
			echo "</td>\n";

			echo "<td>";
			echo formate_date($lig1->naissance);
			echo "</td>\n";

			echo "<td>";
			echo "<input type='text' name='ine_corrige[$cpt]' id='ine_corrige_$cpt' value='' onchange='changement();' />\n";
			echo "</td>\n";

			echo "<td>";
			echo " <a href='#' onClick=\"";
			// On renseigne le formulaire de recherche avec le nom et le prénom:
			echo "document.getElementById('nom_ele').value=document.getElementById('nom_eleve_$cpt').value;";
			echo "document.getElementById('prenom_ele').value=document.getElementById('prenom_eleve_$cpt').value;";
			// Pour le lien de renseignement de corrige_ine:
			echo "document.getElementById('ine_recherche').value='ine_corrige_$cpt';";
			// On fait le nettoyage pour ne pas laisser les traces d'une précédente requête:
			echo "document.getElementById('div_resultat').innerHTML='';";
			echo "afficher_div('div_search','y',-400,20);";
			echo "return false;";
			echo "\">";
			echo "<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' />";
			echo "</a>";
			echo "</td>\n";
			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";

		echo "<p align='center'><input type='submit' name='confirmer' value='Enregistrer' /></p>\n";
		echo "</form>\n";

		echo creer_div_infobulle("div_search","Form of research in the table 'eleves'","","<p>To seize a portion of the name to be sought...</p>
<form name='recherche' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='hidden' name='ine_recherche' id='ine_recherche' value='' />
<table border='0' summary='Recherche'>
	<tr>
		<th>Name: </th>
		<td><input type='text' name='nom_ele' id='nom_ele' value='' onBlur='get_eleves(this.form)' /></td>
		<td rowspan='2'><input type='button' name='chercher' value='Chercher' onClick='get_eleves(this.form)' /></td>
	</tr>
	<tr>
		<th>First name: </th>
		<td><input type='text' name='prenom_ele' id='prenom_ele' value='' onBlur='get_eleves(this.form)' /></td>
	</tr>
</table>
</form>

<div id='div_resultat' style='margin: 1px;'></div>

","",27,0,"y","y","n","n");

		echo "<p><br /></p>\n";
		echo "<p><b>Caution:</b> If you modify a INE by allotting the INE of another student that the good, you are likely to more be able to sort what corresponds to a student indeed.<br />Carry out the correction only after checking.</p>\n";
	}

	//echo "<div id='idretour' style='border: 1px solid black; background-color: white; width: 100px; height: 30px;'></div>\n";

}
elseif($mode=="recherche"){
	echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Correction of INE</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Search for pupils to correct an erroneous number INE in the table of
the former data.</p>\n";

	if(!isset($recherche1)){
		echo "<form name='recherche' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='hidden' name='mode' value='recherche' />
<input type='hidden' name='recherche1' value='y' />
<table border='0' summary='Recherche'>
	<tr>
		<!--td rowspan='2' valign='top'>student whose </td-->
		<td>student whose </td>
		<td align='center'>le <b>name</b></td>
		<td> contains :</td>
		<td><input type='text' name='recherche1_nom' value='' /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align='center'>et le <b>first name</b></td>
		<td> contient: </td>
		<td><input type='text' name='recherche1_prenom' value='' /></td>
	</tr>
</table>
<input type='submit' name='chercher' value='Chercher' />
</form>\n";
	}
	else{
		$sql="SELECT DISTINCT ine,nom,prenom,naissance FROM archivage_eleves
				WHERE nom LIKE '%$recherche1_nom%' AND
					prenom LIKE '%$recherche1_prenom%'
				ORDER BY nom,prenom";
		//echo "$sql<br />";
		$res1=mysql_query($sql);

		if(mysql_num_rows($res1)==0){
			echo "<p>No student in the table 'archivage_eleves' does not fill the criteria requested.</p>\n";
		}
		else{

			echo "<form name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
			echo "<input type='hidden' name='mode' value=\"recherche\" />\n";
			echo "<input type='hidden' name='recherche1' value=\"y\" />\n";
			echo "<input type='hidden' name='recherche1_nom' value=\"$recherche1_nom\" />\n";
			echo "<input type='hidden' name='recherche1_prenom' value=\"$recherche1_prenom\" />\n";

			echo "<table class='table_annee_anterieure' summary='Tableau des élèves'>\n";
			echo "<tr style='background-color: white;'>\n";
			echo "<th>INE recorded</th>\n";
			echo "<th>Name</th>\n";
			echo "<th>First name</th>\n";
			echo "<th>Date of birth</th>\n";
			echo "<th>INE corrected</th>\n";
			echo "<th>search</th>\n";
			echo "</tr>\n";

			$cpt=0;
			$alt=-1;
			while($lig1=mysql_fetch_object($res1)){
				$alt=$alt*(-1);
				echo "<tr style='background-color:";
				if($alt==1){
					echo "silver";
				}
				else{
					echo "white";
				}
				echo "; text-align: center;'>\n";

				echo "<td style='color: red;'>";
				echo $lig1->ine;
				echo "<input type='hidden' name='ine[$cpt]' value=\"$lig1->ine\" />\n";
				echo "</td>\n";

				echo "<td>";
				echo $lig1->nom;
				echo "<input type='hidden' name='nom_eleve[$cpt]' id='nom_eleve_$cpt' value=\"$lig1->nom\" />\n";
				echo "</td>\n";

				echo "<td>";
				echo $lig1->prenom;
				echo "<input type='hidden' name='prenom_eleve[$cpt]' id='prenom_eleve_$cpt' value=\"$lig1->prenom\" />\n";
				echo "</td>\n";

				echo "<td>";
				echo formate_date($lig1->naissance);
				echo "</td>\n";

				echo "<td>";
				echo "<input type='text' name='ine_corrige[$cpt]' id='ine_corrige_$cpt' value='' onchange='changement();' />\n";
				echo "</td>\n";

				echo "<td>";
				echo " <a href='#' onClick=\"";
				// On renseigne le formulaire de recherche avec le nom et le prénom:
				echo "document.getElementById('nom_ele').value=document.getElementById('nom_eleve_$cpt').value;";
				echo "document.getElementById('prenom_ele').value=document.getElementById('prenom_eleve_$cpt').value;";
				// Pour le lien de renseignement de corrige_ine:
				echo "document.getElementById('ine_recherche').value='ine_corrige_$cpt';";
				// On fait le nettoyage pour ne pas laisser les traces d'une précédente requête:
				echo "document.getElementById('div_resultat').innerHTML='';";
				echo "afficher_div('div_search','y',-400,20);";
				echo "return false;";
				echo "\">";
				echo "<img src='../images/icons/chercher.png' width='16' height='16' alt='search' />";
				echo "</a>";
				echo "</td>\n";
				echo "</tr>\n";
				$cpt++;
			}
			echo "</table>\n";

			echo "<p align='center'><input type='submit' name='confirmer' value='Record' /></p>\n";
			echo "</form>\n";

			echo creer_div_infobulle("div_search","Form of research in the table 'eleves'","","<p>Seize a portion of the name to be sought...</p>
<form name='recherche' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='hidden' name='ine_recherche' id='ine_recherche' value='' />
<table border='0' summary='Recherche'>
	<tr>
		<th>Name: </th>
		<td><input type='text' name='nom_ele' id='nom_ele' value='' onBlur='get_eleves(this.form)' /></td>
		<td rowspan='2'><input type='button' name='chercher' value='Chercher' onClick='get_eleves(this.form)' /></td>
	</tr>
	<tr>
		<th>First name: </th>
		<td><input type='text' name='prenom_ele' id='prenom_ele' value='' onBlur='get_eleves(this.form)' /></td>
	</tr>
</table>
</form>

<div id='div_resultat' style='margin: 1px;'></div>

","",27,0,"y","y","n","n");

			echo "<p><br /></p>\n";
			echo "<p><b>Caution:</b> SI you modify a INE by allotting the INE of another pupil that the good, you are likely to more be able to sort what corresponds to a student indeed.<br />Carry out the correction only after checking.</p>\n";
		}
	}
}


echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
die();


















$sql="SELECT DISTINCT a.nom,a.prenom,a.INE,a.naissance
			FROM archivage_eleves a
			LEFT JOIN eleves e
			ON a.INE=e.no_gep
			WHERE e.no_gep IS NULL;";
$res1=mysql_query($sql);
$nb_ele=mysql_num_rows($res1);
if($nb_ele==0){
	echo "<p>All the student  present in the table ' archivage_eleves' are in the
table 'eleves'.</p>\n";
}
else{
	echo "<p>Here the list of the student present in the table 'archivage_eleves', but absent from the table 'eleves'.<br />
	They are normally student having left the establishment.<br />
	It can however happen that student whose number INE was not (<i>correctly</i>) filled at the time of the conservation of the year is proposed in the list below.<br />
	In this case, number INE used has a prefix LOGIN_.<br />
	It is not a correct identifier because the login of a pupil is not
necessarily fixed one year on the other (<i>in the case of the doubled blooms</i>).<br />
	<font color='red'>A page must be developed to enable you to correct these INE</font>.</p>\n";

	echo "<form name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

	echo "<table align='center' class='table_annee_anterieure' summary='Table of the pupils'>\n";
	echo "<tr style='background-color:white;'>\n";
	echo "<th>Remove<br />";
	echo "<a href='javascript:modif_coche(true)'><img src='../images/enabled.png' width='15' height='15' alt='All tick' /></a>/\n";
	echo "<a href='javascript:modif_coche(false)'><img src='../images/disabled.png' width='15' height='15' alt='All shoot' /></a>\n";
	echo "</th>\n";
	echo "<th>Student</th>\n";
	echo "<th>Date of birth</th>\n";
	echo "<th>N°INE</th>\n";
	echo "</tr>\n";
	$cpt=0;
	while($lig_ele=mysql_fetch_object($res1)){
		echo "<tr style='text-align:center;' id='tr_$cpt'>\n";
		echo "<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value='$lig_ele->INE' onchange=\"modif_une_coche('$cpt');\" /></td>\n";
		echo "<td>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</td>\n";
		echo "<td>".formate_date($lig_ele->naissance)."</td>\n";
		echo "<td>";
		if(substr($lig_ele->INE,0,6)=="LOGIN_") {echo "<span style='color:red;'>";}
		echo $lig_ele->INE;
		if(substr($lig_ele->INE,0,6)=="LOGIN_"){echo "</span>";}
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
