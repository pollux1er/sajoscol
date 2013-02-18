<?php
@set_time_limit(0);
/*
 * $Id: efface_photos.php 7953 2011-08-24 14:23:50Z regis $
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


//**************** EN-TETE *****************
$titre_page = "Management tool | Obliteration of the students photographs ";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?><p class=bold><a href='index.php#efface_photos'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>
<h2>Obliteration of the photographs of students</h2>
<?php
// En multisite, on ajoute le répertoire RNE
if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
	// On récupère le RNE de l'établissement
	$rep_photos='../photos/'.$_COOKIE['RNE'].'/eleves';
}
else {
	$rep_photos='../photos/eleves';		
}

if((isset($_POST['is_posted']))&&(isset($_POST['supprimer']))) {
	check_token(false);

	$handle=opendir($rep_photos);
	//$tab_file = array();
	$n=0;
	$nbsuppr=0;
	$nberreur=0;
	$chaine="";
	while ($file = readdir($handle)) {
		if((my_eregi(".jpg$",$file))||(my_eregi(".jpeg$",$file))){

			$prefixe=substr($file,0,strrpos($file,"."));
			$sql="SELECT 1=1 FROM eleves WHERE elenoet='$prefixe'";
			//echo "<br />$sql<br />\n";
			$test=mysql_query($sql);

			if(mysql_num_rows($test)==0){
				//$tab_file[] = $file;
				if($n>0){
					//echo ", \n";
					$chaine.=", \n";
				}
				if(unlink($rep_photos."/".$file)){
					$chaine.="$file";
					$nbsuppr++;
				}
				else{
					$chaine.="<font color='red'>$file</font>";
					$nberreur++;
				}
				$n++;
			}
		}
	}
	closedir($handle);
	if($chaine!=""){
		echo "<p>Result of cleaning: $nbsuppr suppression(s) succeeded ";
		if($nberreur>0){echo " and $nberreur failures.<br />Control the rights on these files and retry";}
		echo ":<br />\n";
		echo "$chaine\n";
		echo "</p>\n";
	}
}
else {
    echo "<p><b>CAUTION:</b> This procedure erases all the photographs nonassociated to students.</p>\n";

	$handle=opendir($rep_photos);
	//$tab_file = array();
	$n=0;
	$nbjpg=0;
	$chaine="";
	while ($file = readdir($handle)) {
		if((my_eregi(".jpg$",$file))||(my_eregi(".jpeg$",$file))){
			$nbjpg++;

			$prefixe=substr($file,0,strrpos($file,"."));
			$sql="SELECT 1=1 FROM eleves WHERE elenoet='$prefixe'";
			//echo "<br />$sql<br />\n";
			$test=mysql_query($sql);

			if(mysql_num_rows($test)==0){
				//$tab_file[] = $file;
				if($n>0){
					//echo ", \n";
					$chaine.=", \n";
				}
				//echo "<a href='../photos/eleves/$file'>$file</a>";
				$chaine.="<a href='".$rep_photos."/$file' target='blank'>$file</a>";
				$n++;
			}
		}
	}
	closedir($handle);
	if($chaine!=""){
		echo "<p>The following photos would be removed:\n";
		echo "$chaine\n";
		echo "<br />That is to say a total of $n photo(s).</p>\n";

		echo "<p><b>Are sure you want to continue ?</b></p>\n";
		echo "<form action='".$_SERVER['PHP_SELF']."' method=\"post\" name=\"formulaire\">\n";
		echo add_token_field();
		echo "<input type='hidden' name=is_posted value = '1' />\n";
		echo "<input type='submit' name='supprimer' value='Remove these photos' />\n";
		echo "</form>\n";
	}
	else{
		if($nbjpg>0){
			echo "<p>No photo correspond to this criterion.</p>\n";
		}
		else{
			echo "<p>No photo JPEG was found.</p>\n";
		}
	}
}

?>

</body>
</html>
