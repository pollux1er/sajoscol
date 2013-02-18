<?php
/*
* $Id: changement_d_annee.php 7904 2011-08-22 15:04:03Z crob $
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

$sql="SELECT 1=1 FROM droits WHERE id='/gestion/changement_d_annee.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/gestion/changement_d_annee.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Changement d\'année.',
statut='';";
$insert=mysql_query($sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg = '';

if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {
		check_token();

		if (isset($_POST['gepiYear'])) {
			if (!saveSetting("gepiYear", $_POST['gepiYear'])) {
				$msg .= "Error during the recording of the school year !";
			}
		}

		if (isset($_POST['begin_day']) and isset($_POST['begin_month']) and isset($_POST['begin_year'])) {
			$begin_bookings = mktime(0,0,0,$_POST['begin_month'],$_POST['begin_day'],$_POST['begin_year']);
			if (!saveSetting("begin_bookings", $begin_bookings))
					$msg .= "Error during recording of begin_bookings !";
		}
		if (isset($_POST['end_day']) and isset($_POST['end_month']) and isset($_POST['end_year'])) {
			$end_bookings = mktime(0,0,0,$_POST['end_month'],$_POST['end_day'],$_POST['end_year']);
			if (!saveSetting("end_bookings", $end_bookings))
					$msg .= "Error during recording of end_bookings !";
		}
	}
	elseif ($_POST['is_posted']=='2') {
		check_token();

		if (isset($_POST['log_day']) and isset($_POST['log_month']) and isset($_POST['log_year'])) {
			//$log_clean_date = mktime(0,0,0,$_POST['log_month'],$_POST['log_day'],$_POST['log_year']);
			//echo $log_clean_date;

			unset($log_year);
			unset($log_month);
			unset($log_day);
			if(preg_match('/^[0-9]+$/',$_POST['log_year'])) {$log_year=$_POST['log_year'];}
			if(preg_match('/^[0-9]+$/',$_POST['log_month'])) {$log_month=$_POST['log_month'];}
			if(preg_match('/^[0-9]+$/',$_POST['log_day'])) {$log_day=$_POST['log_day'];}

			if((isset($log_year))&&(isset($log_month))&&(isset($log_day))) {
				// Pour éviter de flinguer la session en cours
				$hier_day=date('d', mktime() - 24*3600);
				$hier_month=date('m', mktime() - 24*3600);
				$hier_year=date('Y', mktime() - 24*3600);

				//$sql="SELECT * FROM log WHERE start<'$log_year-$log_month-$log_day 00:00:00' AND start<'".date('Y')."-".date('m')."-".$hier." 00:00:00';";
				$sql="DELETE FROM log WHERE start<'$log_year-$log_month-$log_day 00:00:00' AND start<'".$hier_year."-".$hier_month."-".$hier_day." 00:00:00';";
				//echo "$sql<br />\n";
				$del=mysql_query($sql);
				if(!$del) {
					$msg.="Failure of cleaning.<br />\n";
				}
				else {
					$msg.="Cleaning done.<br />\n";
				}
			}
			else {
				$msg .= "The date suggested is invalid.<br />";
			}
			//if (!)
			//		$msg .= "Erreur lors de l'enregistrement de log_bookings !";
		}

	}
}

// Load settings
if (!loadSettings()) {
	die("Error loading settings");
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "The modifications were recorded !";

if(isset($_SESSION['chgt_annee'])) {
	unset($_SESSION['chgt_annee']);
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Information was modified. Do you really want to leave without recording ?';
//**************** EN-TETE *****************
// End standart header
$titre_page = "Change of year";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php#chgt_annee' ".insert_confirm_abandon()."><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>\n";

echo "<p>At the change of year, before initializing the new school year, it is advisable to make some operations.<br />They are in theory detailed (<i>perhaps even more up to date if additions were brought there after the
release of your version of GEPI</i>) on  the <a href='https://www.sylogix.org/projects/gepi/wiki/GuideAdministrateur' target='_blank'>Wiki</a>.</p>\n";

echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form1' style='width: 100%;'>\n";
echo "<fieldset>\n";
echo add_token_field();

$msg_svg="It is recommended to make a copy of backup on an external peripheral (to store in safe for example)";
$lien_svg="<a href='#svg_ext' ".insert_confirm_abandon()."><img src='../images/icons/ico_ampoule.png' width='15' height='25' title='$msg_svg' alt='$msg_svg' /></a>";

echo "<p>The points are as follows&nbsp;:</p>\n";
echo "<p>The archiving part of end of the year&nbsp;:</p>\n";
echo "<ol>\n";
echo "<li><p><a href='accueil_sauve.php?chgt_annee=y'>Backup the base</a> $lien_svg</p></li>\n";
if(strtolower(substr(getSettingValue('active_cahiers_texte'),0,1))=='y') {
	echo "<li><p>Possibly, do an<a href='../cahier_texte_2/export_cdt.php?chgt_annee=y'>export of the textbooks</a><br />and a <a href='accueil_sauve.php?chgt_annee=y#zip'>backup of the documents of the Textbook</a> $lien_svg</p></li>\n";
	echo "<li><p><a href='../cahier_texte_2/archivage_cdt.php?chgt_annee=y'>Archive the textbooks</a> to allow to the professors a consultation of their past textbook.</p></li>\n";
}
if(getSettingValue('active_module_absence')=='2') {
	echo "<li><p><a href='../mod_abs2/extraction_saisies.php?date_absence_eleve_debut=".(date('Y')-1)."-08-01&date_absence_eleve_fin=".date('Y')."-08-01&type_extrait=1&retour=../gestion/changement_d_annee.php'>Make a CSV extraction of the absences</a>,\n";
	echo " puis <a onclick=\"return(confirm('Voulez vous vider les tables d\'absences ?'));\" href='../utilitaires/clean_tables.php?action=clean_absences&amp;date_limite=31/07/".date('Y')."&amp;chgt_annee=y".add_token_in_url()."'/>purge the tables absences for the absences former to 31/07/".date('Y')."</a></p></li>";
}
echo "<li><p>Backup the Gepi tree structure (<em>par ftp, sftp,...</em>) $lien_svg</p></li>\n";
echo "<li><p>Preserve the data of the past year via <a href='../mod_annees_anterieures/conservation_annee_anterieure.php?chgt_annee=y'>modulate former Years</a>.</p></li>\n";
echo "</ol>\n";

echo "<p>The part concerning the new year&nbsp;:</p>\n";
echo "<ol>\n";
echo "<li><p>Modify the school year&nbsp; (currently ".getSettingValue('gepiYear').") : <input type='text' name='gepiYear' size='20' value='".date('Y')."/".(date('Y')+1)."' onchange='changement()' /></li>\n";
echo "<li><p>Modify the dates of beginning and end of the textbooks&nbsp;:<br />";
?>

<table>
	<tr>
		<td>
		Date of beginning of the textbooks (currently <?php echo strftime("%d/%m/%Y", getSettingValue("begin_bookings")); ?>) :
		</td>
		<td><?php
		$bday = strftime("%d", getSettingValue("begin_bookings"));
		$bmonth = strftime("%m", getSettingValue("begin_bookings"));
		$byear = date('Y');
		genDateSelector("begin_", $bday, $bmonth, $byear,"more_years");
		?>
		</td>
	</tr>
	<tr>
		<td>
		Completion date of the textbooks (currently <?php echo strftime("%d/%m/%Y", getSettingValue("end_bookings")); ?>) :
		</td>
		<td><?php
		$eday = strftime("%d", getSettingValue("end_bookings"));
		$emonth = strftime("%m", getSettingValue("end_bookings"));
		$eyear = date('Y')+1;
		genDateSelector("end_",$eday,$emonth,$eyear,"more_years");
		?>
		</td>
	</tr>
</table>

<?php
echo "</li>\n";
echo "</ol>\n";

echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='submit' name='Valider' value='Validate' />\n";
echo "</fieldset>\n";
echo "</form>\n";

echo "<br />\n";

echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form1' style='width: 100%;'>\n";
echo "<fieldset>\n";
echo add_token_field();
echo "<p><em>Optional&nbsp;:</em> Clean the table 'log'.<br />\n";
echo "This table contains the dates of connection/disconnection of the users.<br />\n";
echo "Store these information beyond one year does not have really interest.<br >\n";
echo "If is needed, if you took care to carry out a backup of the base, information is there.</p>\n";
$lday = strftime("%d", getSettingValue("end_bookings"));
$lmonth = strftime("%m", getSettingValue("end_bookings"));
$lyear = date('Y')-1;
echo "<p>Clean the logs former to&nbsp;:&nbsp;\n";
genDateSelector("log_",$lday,$lmonth,$lyear,"more_years");
echo "<input type='hidden' name='is_posted' value='2' />\n";
echo "<input type='submit' name='Valider' value='Validate' />\n";
echo "</p>\n";
echo "</fieldset>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

echo "<a name='svg_ext'></a>";
echo "<p><em>NOTES&nbsp;:</em></p>\n";
echo "<p style='margin-left:3em;'>The backup on external peripheral makes it possible to replace GEPI if ever your GEPI on line is damage (<em>crash of the hard disk lodging your GEPI, burn the room server,...</em>).<br />You will not normally never need these backups, but better is worth to take precautions.</p>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
