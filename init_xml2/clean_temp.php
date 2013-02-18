<?php
@set_time_limit(0);
/*
* $Id: clean_temp.php 6246 2010-12-28 16:21:53Z crob $
*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
extract($_POST, EXTR_OVERWRITE);

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

check_token();

//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year: Removal of the temporary files";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return home initialization</a></p>
<?php
echo "<center><h3 class='gepi'>Removal of the temporary files</h3></center>\n";

$tempdir=get_user_temp_directory();
if(!$tempdir){
	echo "<p style='color:red'>It seems that the temporary folder of the user ".$_SESSION['login']." is not defined!?</p>\n";
	// Il ne faut pas aller plus loin...
	// SITUATION A GERER
}

if (!isset($is_posted)) {
	$temoin=0;
	$tab=array('eleves.xml','nomenclature.xml','sts.xml','f_div.csv','f_gpd.csv','f_men.csv','matiere_principale.csv');
	for($i=0;$i<count($tab);$i++){
		if(file_exists("../temp/$tempdir/$tab[$i]")){
			$temoin++;
		}
	}

	if($temoin==0){
		echo "<p>No files of initialization is yet present in your temporary folder.</p>\n";

		echo "<p><i>Notice&nbsp;:</i> It is recommended to <a href='../responsables/dedoublonnage_adresses.php'>remove double of addresses of the responsibles</a> at the end of the initialization (<i>Sconet tends to count two recordings even for responsibles living under
the same house</i>)</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	elseif($temoin==1){
		echo "<p>One of the files of initialization is still present in your temporary folder.</p>\n";
	}
	else{
		echo "<p>$temoin files of initialization are still present in your temporary folder.</p>\n";
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<input type=hidden name='step1' value='y' />\n";
	echo "<input type='submit' name='is_posted' value='Remove the files' />\n";
	echo "</form>\n";
	require("../lib/footer.inc.php");
	die();
}
else {

	$dest_file="../temp/".$tempdir."/sts.xml";
	$tab=array('eleves.xml','nomenclature.xml','responsables.xml','sts.xml','f_div.csv','f_gpd.csv','f_men.csv','matiere_principale.csv');
	for($i=0;$i<count($tab);$i++){
		if(file_exists("../temp/$tempdir/$tab[$i]")){
			echo "<p>Suppression de $tab[$i] ... ";
			if(unlink("../temp/".$tempdir."/$tab[$i]")){
				echo "réussie.</p>\n";
			}
			else{
				echo "<font color='red'>Echec!</font> Check the rights of writing on the server.</p>\n";
			}
		}
	}

	echo "<p><i>Notice&nbsp;:</i> It is recommended to <a href='../responsables/dedoublonnage_adresses.php'>remove doubles addresses of the responsibles</a> at the end of the initialization (<i>Sconet tends to count two recordings even for responsibles living under the same house</i>)</p>\n";

}

require("../lib/footer.inc.php");
?>