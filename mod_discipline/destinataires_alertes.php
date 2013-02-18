<?php
/*
 * $Id: destinataires_alertes.php 5989 2010-11-25 11:51:39Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

	// Assurons nous que le fichier est accessible en �criture
	if (is_writable($filename)) {

		if (!$handle = fopen($filename, 'a')) {
			 echo "Impossible d'ouvrir le fichier ($filename)";
			 exit;
		}

		// Ecrivons quelque chose dans notre fichier.
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Impossible d'�crire dans le fichier ($filename)";
			exit;
		}

		//echo "L'�criture de ($somecontent) dans le fichier ($filename) a r�ussi";

		fclose($handle);

	} else {
		echo "Le fichier $filename n'est pas accessible en �criture.";
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
// Modif Eric : Table s_alerte_mail � modifier : ajout champs
// ALTER TABLE `s_alerte_mail` ADD `adresse` VARCHAR( 250 ) NULL 
//INSERT INTO droits VALUES ('/mod_discipline/destinataires_alertes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Parametrage des destinataires de mail d alerte', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("You try to reach the Discipline module which is decontaminated !");
	tentative_intrusion(1, "Attempt at access to the Discipline module which is decontaminated.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

if (isset($_POST['action']) and ($_POST['action'] == "reg_dest")) {
	check_token();

	$msg = '';
	$notok = false;

	$tab_statut=$_POST['tab_statut'];
	$tab_id_clas=$_POST['tab_id_clas'];
	
	for($j=0;$j<count($tab_id_clas);$j++){
		for($i=0;$i<count($tab_statut);$i++){
			if(isset($_POST['case_'.$i.'_'.$j])){
			    $requete= "SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND destinataire='".$tab_statut[$i]."'";
				//echo $requete; echo "</br>";
				$test=mysql_query($requete);
				if(mysql_num_rows($test)==0){
				    // Modif Eric Ajout Adresse autre
					if(isset($_POST['adresse_'.$i.'_'.$j]) and isset($_POST['case_'.$i.'_'.$j])){ 
					    $contenu_adresse = $_POST['adresse_'.$i.'_'.$j];
					    if ($contenu_adresse != '') {
						   $sql="INSERT INTO s_alerte_mail SET id_classe='".$tab_id_clas[$j]."', destinataire='".$tab_statut[$i]."', adresse='".$contenu_adresse."'";
						}
				    } else {
					    $sql="INSERT INTO s_alerte_mail SET id_classe='".$tab_id_clas[$j]."', destinataire='".$tab_statut[$i]."'";
					}
					// Fin modif
					$reg_data=mysql_query($sql);
					if(!$reg_data){
						$msg.= "Error during the insertion of a new recording $tab_id_clas[$j] for $tab_statut[$i].";
						$notok = true;
					}
				}
				// Sinon: l'enregistrement est d�j� pr�sent.
			}
			else{
				$test=mysql_query("SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND destinataire='".$tab_statut[$i]."'");
				if(mysql_num_rows($test)>0){
					$sql="DELETE FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND destinataire='".$tab_statut[$i]."'";
					$reg_data=mysql_query($sql);
					updateOnline($sql);
					if(!$reg_data){
						$msg.= "Error during the suppression of the recording $tab_id_clas[$j] pour $tab_statut[$i].";
						$notok = true;
					}
				}
			}
		}
	}


	if ($notok == true) {
		$msg .= "There were errors during the recording of the data";
	} else {
		$msg .= "The recording of the data occurred well.";
	}
}


$themessage  = 'Information was modified. do you want to really leave without recording ?';
//**************** EN-TETE **************************************
$titre_page = "Recipients of alarms";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************
//debug_var();
// Cette page a �t� ouverte en target='blank' depuis une autre page (par exemple /eleves/modify_eleve.php)
// Apr�s modification �ventuelle, il faut quitter cette page.
echo "<p class='bold'>";
echo "<a href='index.php' onClick=\"if(confirm_abandon (this, change, '$themessage')){self.close()};return false;\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>";
echo "</p>\n";
?>

<p>Choose the recipients of the malls of alarm for incidents whose student
are protagonists.</p>
<?php

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	echo add_token_field();

	//Ajout Eric
	$contenu_adresse = "";

	$tab_statut=array('cpe', 'scolarite', 'pp', 'professeurs', 'administrateur', 'mail');

	//echo "<table border='1'>\n";
	echo "<table class='boireaus'>\n";
	//#96C8F0
	$ligne_statuts="<tr style='background-color:#FAFABE;'>\n";
	//$ligne_comptes_scol.="<td style='text-align:center; font-weight:bold;'>Comptes</td>\n";
	$ligne_statuts.="<th style='text-align:center; font-weight:bold;'>Statuts</th>\n";
	$ligne_statuts.="<th>CPE</th>\n";
	$ligne_statuts.="<th>Scolarit�<br />responsible<br />class</th>\n";
	$gepi_prof_suivi=ucfirst(getSettingValue("gepi_prof_suivi"));
	$ligne_statuts.="<th>".$gepi_prof_suivi."</th>\n";
	$ligne_statuts.="<th>Professors<br />class</th>\n";
	$ligne_statuts.="<th>Administrators</th>\n";
	$ligne_statuts.="<th>Other addresses <br/>(Tick then to seize the address directly)</th>\n"; 
	$ligne_statuts.="<th>\n";
	$ligne_statuts.="&nbsp;\n";
	$ligne_statuts.="</th>\n";
	$ligne_statuts.="</tr>\n";
	echo $ligne_statuts;

	echo "<tr style='background-color:#FAFABE;'>\n";
	echo "<th style='text-align:center; font-weight:bold;'>Classes</th>\n";
	for($i=0;$i<count($tab_statut);$i++){
		echo "<th style='text-align:center;'>\n";

		echo "<a href=\"javascript:modif_case($i,true,'col');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='All tick' /></a>/\n";
		echo "<a href=\"javascript:modif_case($i,false,'col');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='All shoot' /></a>\n";

		echo "<input type='hidden' name='tab_statut[$i]' value='$tab_statut[$i]' />";
		//echo "</td>\n";
		echo "</th>\n";
	}
	echo "<th>&nbsp;</th>\n";
	echo "</tr>\n";

	$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
	$nombre_lignes = mysql_num_rows($call_data);
	
	if ($nombre_lignes != 0) {
		// Lignes classes...
		$j=0;
		$alt=1;
		while($lig_clas=mysql_fetch_object($call_data)){
			if(($j%10==0)&&$j>0){echo $ligne_statuts;}

			$alt=$alt*(-1);

			//if($j%2==0){$bgcolor="style='background-color: gray;'";}else{$bgcolor='';}
			//echo "<tr $bgcolor>\n";
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td style='text-align:center;'>";
			echo "<input type='hidden' name='tab_id_clas[$j]' value='$lig_clas->id' />\n";
			echo "$lig_clas->classe";
			echo "</td>\n";
			for($i=0;$i<count($tab_statut);$i++){
				$sql="SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$lig_clas->id."' AND destinataire='".$tab_statut[$i]."';";
				//echo "$sql<br />";
				$test=mysql_query($sql);
				//if(mysql_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: #AAE6AA;";}
				if(mysql_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: plum;";}

				echo "<td style='text-align:center;$bgcolor'>\n";
				echo "<input type='checkbox' name='case_".$i."_".$j."' id='case_".$i."_".$j."' value='y' onchange='changement();' $checked/>\n";
				//Ajout Eric traitement autre mail
				$sql="SELECT * FROM s_alerte_mail WHERE id_classe='".$lig_clas->id."' AND destinataire='mail';";
				//echo $sql;
				$test=mysql_query($sql);
				if(mysql_num_rows($test)!=0) {
					$contenu_requete=mysql_fetch_object($test);
					if ($tab_statut[$i]== 'mail') {
					    if ($contenu_requete->adresse != NULL) {
						    $contenu_adresse = $contenu_requete->adresse;
						} else { 
						    $contenu_adresse = '';
						}
						echo "Adress : <input type='text' name='adresse_".$i."_".$j."' value='$contenu_adresse' onchange='changement();' />\n";    
					} 
				} else if ($tab_statut[$i]== 'mail') echo "Adresse : <input type='text' name='adresse_".$i."_".$j."' value='' onchange='changement();' />\n";
				echo "</td>\n";
			}
			echo "<td>\n";
			echo "<a href=\"javascript:modif_case($j,true,'lig');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='All tick' /></a>/\n";
			//echo "<a href='javascript:modif_case($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout d�cocher' /></a>\n";
			echo "<a href=\"javascript:modif_case($j,false,'lig');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='All shoot' /></a>\n";
			echo "</td>\n";
			echo "</tr>\n";
			$j++;
		}

		echo "</table>\n";
		echo "<input type='hidden' name='action' value='reg_dest' />\n";
		echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";
	} else {
		echo "</table>\n";
		echo "<p class='grand'><b>Caution :</b> no class was defined in base GEPI !</p>\n";
	}






	//============================================
	// AJOUT: boireaus
	echo "<script type='text/javascript' language='javascript'>
		function modif_case(id,statut,mode){
			// id: num�ro de:
			//					. colonne correspondant au login
			//					. ligne
			// statut: true ou false
			// mode: col ou lig
			if(mode=='col'){
				for(k=0;k<$nombre_lignes;k++){
					if(document.getElementById('case_'+id+'_'+k)){
						document.getElementById('case_'+id+'_'+k).checked=statut;
					}
				}
			}
			else{
				for(k=0;k<".count($tab_statut).";k++){
					if(document.getElementById('case_'+k+'_'+id)){
						document.getElementById('case_'+k+'_'+id).checked=statut;
					}
				}
			}
			changement();
		}
	</script>\n";
	//============================================
?>
</form>
<?php require("../lib/footer.inc.php");?>