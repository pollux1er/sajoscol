<?php
/*
* $Id: saisie_secours_eleve.php 8708 2011-12-05 10:54:51Z crob $
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

// On indique qu'il faut creer des variables non prot�g�es (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

if ($_SESSION['statut'] != "secours") {
	$mess=rawurlencode("You do not have access to this page !");
	header("Location: index.php?msg=$mess");
	die();
}

// Initialistion
$id_classe=isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);
$periode_num=isset($_POST["periode_num"]) ? $_POST["periode_num"] :(isset($_GET["periode_num"]) ? $_GET["periode_num"] :NULL);
$ele_login=isset($_POST["ele_login"]) ? $_POST["ele_login"] :(isset($_GET["ele_login"]) ? $_GET["ele_login"] :NULL);
$id_groupe=isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : NULL;
$note_grp=isset($_POST["note_grp"]) ? $_POST["note_grp"] : NULL;

if((isset($_POST['is_posted']))&&
(isset($id_classe))&&
(isset($periode_num))&&
(isset($ele_login))&&
(isset($id_groupe))&&
(isset($note_grp))) {

	check_token();

	$msg="";

	// v�rifier si l'�l�ve est bien dans la classe et si la p�riode est ouverte en saisie

	$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$ele_login' AND id_classe='$id_classe' AND periode='$periode_num';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0) {
		$msg.="student $ele_login is not in the class n�$id_classe over the selected period $periode_num.\n";
	}

	include "../lib/periodes.inc.php";

	if($ver_periode[$periode_num]=="O") {
		$msg.="The selected period ".$nom_periode[$periode_num]." is closed.\n";
	}

	if($msg=="") {
		//echo "count(\$id_groupe)=".count($id_groupe)."<br />";
		for($k=0;$k<count($id_groupe);$k++) {

			// Note
			$note=$note_grp[$k];

			$elev_statut = '';
			if (($note == 'disp')) {
				$note = '0';
				$elev_statut = 'disp';
			}
			else if (($note == 'abs')) {
				$note = '0';
				$elev_statut = 'abs';
			}
			else if (($note == '-')) {
				$note = '0';
				$elev_statut = '-';
			}
			else if (preg_match ("/^[0-9\.\,]{1,}$/", $note)) {
				$note = str_replace(",", ".", "$note");
				if (($note < 0) or ($note > 20)) {
					$note = '';
					$elev_statut = '';
				}
			}
			else {
				$note = '';
				$elev_statut = '';
			}

			if (($note != '') or ($elev_statut != '')) {
				$test_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$ele_login' AND id_groupe='".$id_groupe[$k]."' AND periode='$periode_num')");
				$test = mysql_num_rows($test_eleve_note_query);
				if ($test != "0") {
					$register = mysql_query("UPDATE matieres_notes SET note='$note',statut='$elev_statut', rang='0' WHERE (login='$ele_login' AND id_groupe='".$id_groupe[$k]."' AND periode='$periode_num')");
					if (!$register) {$msg.="Error at the time of the update of the note of teaching n�".$id_groupe[$k];}
				} else {
					$register = mysql_query("INSERT INTO matieres_notes SET login='$ele_login', id_groupe='".$id_groupe[$k]."', periode='$periode_num', note='$note', statut='$elev_statut', rang='0'");
					if (!$register) {$msg.="Error during the insertion of the note of teaching n�".$id_groupe[$k];}
				}
			} else {
				$register = mysql_query("DELETE FROM matieres_notes WHERE (login='$ele_login' AND id_groupe='".$id_groupe[$k]."' AND periode='$periode_num')");
				if (!$register) {$msg.="Error during the suppression of the note of teaching n�".$id_groupe[$k];}
			}


			// Appr�ciation
			if (isset($NON_PROTECT["app_grp_".$k])){
				$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["app_grp_".$k]));
			}
			else{
				$app = "";
			}
			//echo "$k: $app<br />";
			// Contr�le des saisies pour supprimer les sauts de lignes surnum�raires.
			//$app=my_ereg_replace('(\\\r\\\n)+',"\r\n",$app);
			$app=preg_replace('/(\\\r\\\n)+/',"\r\n",$app);
			$app=preg_replace('/(\\\r)+/',"\r",$app);
			$app=preg_replace('/(\\\n)+/',"\n",$app);

			$test_app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (id_groupe='" . $id_groupe[$k]."' AND periode='$periode_num' AND login='$ele_login')");
			$test = mysql_num_rows($test_app_query);
			if ($test != "0") {
				if ($app != "") {
					$register = mysql_query("UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (id_groupe='" . $id_groupe[$k]."' AND periode='$periode_num' AND login='$ele_login')");
					if (!$register) {$msg.="Error at the time of the update of the appreciation of teaching n�".$id_groupe[$k];}
				} else {
					$register = mysql_query("DELETE FROM matieres_appreciations WHERE (id_groupe='".$id_groupe[$k]."' AND periode='$periode_num' AND login='$ele_login')");
					if (!$register) {$msg.="Error during the suppression of the appreciation of teaching n�".$id_groupe[$k];}
				}
			} else {
				if ($app != "") {
					$register = mysql_query("INSERT INTO matieres_appreciations SET id_groupe='" . $id_groupe[$k]."',periode='$periode_num',appreciation='" . $app . "',login='$ele_login'");
					if (!$register) {$msg.="Error during the insertion of the appreciation of teaching n�".$id_groupe[$k];}
				}
			}
		}

		if($msg=="") {
			$affiche_message = 'yes';
		}
	}
}

$themessage = 'Modifications were made. Do you want to really leave without recording ?';
$message_enregistrement = "The modifications were recorded !";
$utilisation_prototype = "ok";
//$javascript_specifique = "saisie/scripts/js_saisie";
//**************** EN-TETE *****************
$titre_page = "Seizure of the notes and appreciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<div class='norme'>\n";

?>
<script type="text/javascript" language="javascript">
	change = 'no';
</script>
<?php

if(!isset($id_classe)) {
	echo "<p class='bold'>\n";
	echo "<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Home typing</a>";
	echo "</p>\n";
	echo "</div>\n";

	$sql="SELECT DISTINCT c.* FROM classes c WHERE 1 ORDER BY classe;";
	//echo "$sql<br />";
	$calldata = mysql_query($sql);
	$nombreligne = mysql_num_rows($calldata);
	echo "<p>Total : $nombreligne classe";
	if ($nombreligne>1) {
		echo "s";
	}
	echo "</p>\n";

	if($nombreligne==0){
		echo "<p>No class is defined.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		echo "<p>Choose a class.</p>\n";
		echo "<blockquote>\n";

		unset($lien_classe);
		unset($txt_classe);
		$i = 0;
		while ($i < $nombreligne){
			$lien_classe[]=$_SERVER['PHP_SELF']."?id_classe=".mysql_result($calldata, $i, "id");
			$txt_classe[]=mysql_result($calldata, $i, "classe");
			$i++;
		}

		tab_liste($txt_classe,$lien_classe,3);
		echo "</blockquote>\n";
	}
	require("../lib/footer.inc.php");
	die();
}
elseif(!isset($periode_num)) {
	echo "<p class='bold'>\n";
	echo "<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Reception seized</a>";

	// Choisir une autre classe
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choice classifies</a>";
	echo "</p>\n";
	echo "</div>\n";

	include "../lib/periodes.inc.php";

	echo "<p>Choose the period:</p>\n";

	echo "<ul>\n";
	for($i=1;$i<=count($nom_periode);$i++) {
		if($ver_periode[$i]!="O") {
			echo "<li><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode_num=$i'>".$nom_periode[$i]."</a></li>\n";
		}
		else {
			echo "<li>".$nom_periode[$i].": Closed period</li>\n";
		}
	}
	echo "</ul>\n";
	require("../lib/footer.inc.php");
	die();
}
elseif(!isset($ele_login)) {
	echo "<p class='bold'>\n";
	echo "<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Reception seized</a>";

	// Choisir une autre classe
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choice classifies</a>";

	// Choisir une autre p�riode
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Period choice</a>";

	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Choose a student:</p>\n";

	echo "<blockquote>\n";

	$sql="SELECT e.nom, e.prenom, e.login FROM j_eleves_classes jec, eleves e WHERE jec.login=e.login AND jec.id_classe='$id_classe' AND jec.periode='$periode_num';";
	$res_ele=mysql_query($sql);

	$nombreligne=mysql_num_rows($res_ele);

	$nbcol=3;

	// Nombre de lignes dans chaque colonne:
	$nb_class_par_colonne=round($nombreligne/$nbcol);

	echo "<table width='100%' summary=\"Table of choice\">\n";
	echo "<tr valign='top' align='center'>\n";
	echo "<td align='left'>\n";

	$i = 0;
	while ($i < $nombreligne){
		$lig_ele=mysql_fetch_object($res_ele);

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		//echo "<br />\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode_num=$periode_num&amp;ele_login=$lig_ele->login'>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</a>";
		echo "<br />\n";
		$i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "</blockquote>\n";

}
else {
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form0' method='post'>\n";

	echo "<p class='bold'>\n";
	echo "<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Reception seized</a>";

	// Choisir une autre classe
	echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choice classifies</a>";

	// Choisir une autre p�riode
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Period choice</a>";

	// Choisir un autre �l�ve de la m�me classe
	//echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode_num=$periode_num' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choix �l�ve</a>";

	$sql="SELECT DISTINCT jec.login,e.nom,e.prenom FROM j_eleves_classes jec, eleves e
							WHERE jec.login=e.login AND
								jec.id_classe='$id_classe'
							ORDER BY e.nom,e.prenom";
	//echo "$sql<br />";
	//echo "\$ele_login=$ele_login<br />";
	$res_ele_tmp=mysql_query($sql);
	$chaine_options_login_eleves="";
	$cpt_eleve=0;
	$num_eleve=-1;
	if(mysql_num_rows($res_ele_tmp)>0){
		$login_eleve_prec=0;
		$login_eleve_suiv=0;
		$temoin_tmp=0;
		while($lig_ele_tmp=mysql_fetch_object($res_ele_tmp)){
			if($lig_ele_tmp->login==$ele_login){
				$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login' selected='true'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
	
				$num_eleve=$cpt_eleve;
	
				$temoin_tmp=1;
				if($lig_ele_tmp=mysql_fetch_object($res_ele_tmp)){
					$login_eleve_suiv=$lig_ele_tmp->login;
					$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
				}
				else{
					$login_eleve_suiv=0;
				}
			}
			else{
				$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
			}
	
			if($temoin_tmp==0){
				$login_eleve_prec=$lig_ele_tmp->login;
			}
			$cpt_eleve++;
		}
	}
	// =================================

	if("$login_eleve_prec"!="0"){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode_num=$periode_num&amp;ele_login=$login_eleve_prec'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">student preceding</a>";
	}


	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_eleve(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form0.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form0.submit();
			}
			else{
				document.getElementById('ele_login').selectedIndex=$num_eleve;
			}
		}
	}
</script>\n";


	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";

	//echo " | <select name='ele_login' onchange='document.form1.submit()'>\n";
	echo " | <select name='ele_login' id='ele_login'";
	echo " onchange=\"confirm_changement_eleve(change, '$themessage');\"";
	echo ">\n";
	echo $chaine_options_login_eleves;
	echo "</select>\n";

	if("$login_eleve_suiv"!="0"){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode_num=$periode_num&amp;ele_login=$login_eleve_suiv'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">next student </a>";
	}

	echo "</p>\n";
	echo "</form>\n";
	echo "</div>\n";




	$sql="SELECT nom,prenom FROM eleves WHERE login='$ele_login';";
	$res_ele=mysql_query($sql);
	if(mysql_num_rows($res_ele)==0) {
		echo "<p>student $ele_login is unknown.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_ele=mysql_fetch_object($res_ele);
	$info_ele=strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom));

	include "../lib/periodes.inc.php";

	if($ver_periode[$periode_num]=="O") {
		echo "<p>$info_ele sur la p�riode ".$nom_periode[$periode_num].": The selected period is closed.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>Seizure of the notes and appreciations for $info_ele over the period ".$nom_periode[$periode_num]."</p>\n";

	$sql="SELECT jeg.id_groupe, g.name, g.description, m.nom_complet
			FROM groupes g,
				matieres m,
				j_groupes_matieres jgm,
				j_eleves_groupes jeg
			WHERE g.id=jeg.id_groupe AND
				g.id=jgm.id_groupe AND
				jgm.id_matiere=m.matiere AND
				jeg.login='".$ele_login."' AND
				jeg.periode='".$periode_num."';";
	//echo "$sql<br />\n";
	$res_grp=mysql_query($sql);
	if(mysql_num_rows($res_grp)==0) {
		echo "<p>The student  is registered in no teaching over the period.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		$alt=1;
		$cpt=0;

		echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" name='form1' method=\"post\">\n";

		echo add_token_field();

		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";
		echo "<input type='hidden' name='ele_login' value=\"$ele_login\" />\n";

		echo "<table class='boireaus'>\n";
		echo "<tr>\n";
		echo "<th>Teaching</th>\n";
		echo "<th>Note</th>\n";
		echo "<th>Appreciation</th>\n";
		echo "</tr>\n";
		while($lig_grp=mysql_fetch_object($res_grp)) {
			$id_groupe=$lig_grp->id_groupe;
			$matiere_nom_complet=$lig_grp->nom_complet;
			$description_groupe=$lig_grp->description;

			$app="";
			$sql="SELECT appreciation FROM matieres_appreciations WHERE id_groupe='$id_groupe' AND login='$ele_login' AND periode='$periode_num';";
			//echo "$sql<br />\n";
			$res_app=mysql_query($sql);
			if(mysql_num_rows($res_app)>0) {
				$lig_app=mysql_fetch_object($res_app);
				$app=$lig_app->appreciation;
			}

			$note="";
			$sql="SELECT note,statut FROM matieres_notes WHERE id_groupe='$id_groupe' AND login='$ele_login' AND periode='$periode_num';";
			//echo "$sql<br />\n";
			$res_note=mysql_query($sql);
			if(mysql_num_rows($res_note)>0) {
				$lig_note=mysql_fetch_object($res_note);
				if($lig_note->statut=='') {
					$note=$lig_note->note;
				}
				else {
					$note=$lig_note->statut;
				}
			}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";

			echo "<td>\n";
			echo htmlentities($matiere_nom_complet);
			if($matiere_nom_complet!=$description_groupe) {echo "<br /><span style='font-size:x-small;'>".htmlentities($description_groupe)."</span>\n";}
			echo "<input type='hidden' name='id_groupe[$cpt]' value='$id_groupe' />\n";
			echo "</td>\n";

			$num_id=2*$cpt;
			echo "<td>\n";
			echo "<input type='text' size='3' id=\"n".$num_id."\" name='note_grp[$cpt]' value='$note' onKeyDown=\"clavier(this.id,event);\" />";
			echo "</td>\n";

			$num_id=2*$cpt+1;
			echo "<td>\n";
			echo "<textarea id=\"n".$num_id."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$cpt."\" rows='2' cols='70' onchange=\"changement()\"";
			echo ">".$app."</textarea>\n";
			echo "</td>\n";

			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";
		//echo "<input type='hidden' name='cpt' value='$cpt' />\n";
		echo "<p><input type='submit' name='is_posted' value='Enregistrer' /></p>\n";
		echo "</form>\n";

		echo "<p><a href='javascript:tout_vider()'>Empty all the notes and appreciations of this student</a>.</p>\n";

		echo "<script type='text/javascript'>
	function tout_vider() {
		if(confirm('Etes-vous s�r de vouloir vider toutes les notes et appreciations ?')) {
			for(i=0;i<=$num_id;i++) {
				if(document.getElementById('n'+i)) {document.getElementById('n'+i).value='';}
			}
		}
	}
</script>\n";
	}
}
require("../lib/footer.inc.php");
?>
