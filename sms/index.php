<?php
/*
* $Id: index.php 8549 2011-10-26 16:47:02Z crob $
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

function log_debug($texte) {
	$fich=fopen("/tmp/debug.txt","a+");
	fwrite($fich,$texte."\n");
	fclose($fich);
}

//log_debug('Avant initialisations');

// Initialisations files
require_once("../lib/initialisations.inc.php");

//log_debug('Après initialisations');

extract($_GET, EXTR_OVERWRITE);
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

//log_debug('Après $session_gepi->security_check()');

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

if(isset($_SESSION['retour_apres_maj_sconet'])) {
	unset($_SESSION['retour_apres_maj_sconet']);
}

//log_debug('Après checkAccess()');

//log_debug(debug_var());
//debug_var();


 //répertoire des photos

// En multisite, on ajoute le répertoire RNE


$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
if($_SESSION['statut']=="professeur") {
	if(getSettingValue('GepiAccesGestElevesProfP')!='yes') {
		tentative_intrusion("2", "Tentative d'accès par un prof à des fiches élèves, sans en avoir l'autorisation.");
		echo "Vous ne pouvez pas accéder à cette page car l'accès professeur n'est pas autorisé !";
		require ("../lib/footer.inc.php");
		die();
	}
	else {
		// Le professeur est-il professeur principal dans une classe au moins.
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if (mysql_num_rows($test)==0) {
			tentative_intrusion("2", "Tentative d'accès par un prof qui n'est pas $gepi_prof_suivi à des fiches élèves, sans en avoir l'autorisation.");
			echo "Vous ne pouvez pas accéder à cette page car vous n'êtes pas $gepi_prof_suivi !";
			require ("../lib/footer.inc.php");
			die();
		}
	}
}

// Le statut scolarite ne devrait pas être proposé ici.
// La page confirm_query.php n'est accessible qu'en administrateur
if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {
	if (isset($is_posted) and ($is_posted == '1')) {

		check_token();

		
		//header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&amp;action=del_eleve");
		if($liste_cible!=''){
			header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&liste_cible2=$liste_cible2&action=del_eleve".add_token_in_url(false));
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Gestion des SMS";
require_once("../lib/header.inc");
//************** FIN EN-TETE *****************

if(getSettingValue('eleves_index_debug_var')=='y') {
	debug_var();
}
?>

<script type='text/javascript' language="JavaScript">
	
</script>

<?php
if ($_SESSION['statut'] == 'administrateur') {
	$retour = "../accueil_admin.php";
}
else{
	$retour = "../accueil.php";
}
if (isset($quelles_classes)) {
	$retour = "index.php";
}
echo "<p class=bold><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>\n";


if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {
	echo " | <a href='index.php?mode=unique'>Send SMS to a specific parent</a>\n";
	echo " | <a href='index.php?mode=class'>Send SMS to a class parents</a>\n";
	if(filesize('sms.txt') != 0) {
		echo " | <a href='index.php?mode=sendpendingsms'>Send pending SMS</a>\n";
	}

}

// if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {echo " | <a href='synchro_mail.php'>Synchroniser les adresses mail élèves</a>\n";}
if(isset($_GET['mode'])) { 
	if($_GET['mode'] == 'unique') {
		$query = "SELECT resp.resp_legal, rp.civilite, rp.nom, rp.prenom, rp.tel_port FROM responsables2 AS resp LEFT JOIN resp_pers AS rp ON rp.pers_id = resp.pers_id";
		//var_dump($query);
		$result = @mysql_query($query);
		$destinataires = array();
		while($row = mysql_fetch_assoc($result))
			$destinataires[] = $row;
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$action = "sendsms";
			$userid = "726e02c0-b0cc-45d2-8e28-928c9fe84d0e";
			$password = "25fevrier";
			$sender = "SAJOSCOL";
			$to = "237" . $_POST['to'];
			$msg = urlencode($_POST['message']);
			$url = "http://iYam.mobi/apiv1/?";
			$urlsend = $url . "action=" . $action . "&userid=" . $userid . "&password=" . $password . "&sender=" . $sender . "&to=" . $to . "&msg=" . $msg;
			
			$return = file_get_contents($urlsend);
			$response = json_decode($return);
			//var_dump($response);
		}
		?>
		<center><p class='grand'>Send SMS to one parent</p></center>
<?php if(@$response->status == "success") echo '<h2 style="color:blue;">' . 'Message succesfully sent to ' .  $_POST['to'] . '</h2>'; ?>
<form id="form1" name="form1" method="post" action="">
  <table width="40%" border="0" cellspacing="2" cellpadding="2">
    <tr>
      <th class="p-r-10" scope="row"><div align="right">Sender</div></th>
      <td><input name="sender" type="text" disabled="disabled" id="sender" readonly="readonly"value="SASSE" />
	  <input type="hidden" name="parent" value=""/></td>
    </tr>
    <tr>
      <th class="p-r-10" scope="row"><div align="right">Recipient</div></th>
      <td><select name="to" id="number" >
		<?php foreach($destinataires as $d){ ?>
			<option value="<?php echo $d['tel_port']; ?>"><?php echo $d['nom'] .  " " . $d['prenom'] ?> </option>
			<?php } ?>
		</select>
	  </td>
    </tr>
    <tr>
      <th class="p-r-10" scope="row"><div align="right">Message</div></th>
      <td><textarea name="message" id="textarea" cols="40" rows="3"></textarea></td>
    </tr>
    <tr>
      <th colspan="2" scope="row"><input type="submit" name="button" id="button" value="Send SMS" /></th>
    </tr>
  </table>
</form>
		<?php
	} elseif($_GET['mode'] == 'class') {
		$query = "SELECT * FROM classes";

		//var_dump($query);
		$result = @mysql_query($query);
		$classes = array();
		while($row = mysql_fetch_assoc($result))
			$classes[] = $row;
		var_dump($destinataires);
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$action = "sendsms";
			$userid = "726e02c0-b0cc-45d2-8e28-928c9fe84d0e";
			$password = "25fevrier";
			$sender = "SAJOSCOL";
			$msg = urlencode($_POST['message']);
			$url = "http://iYam.mobi/apiv1/?";
			
			$query = "SELECT DISTINCT rsp.pers_id, rp.civilite, rp.nom, rp.tel_port, c.nom_complet, c.classe FROM `responsables2` AS rsp 
			LEFT JOIN resp_pers AS rp ON rp.pers_id = rsp.pers_id 
			INNER JOIN eleves AS e ON e.ele_id = rsp.ele_id 
			INNER JOIN j_eleves_classes AS j ON j.login = e.login 
			LEFT JOIN classes AS c ON c.id = j.id_classe WHERE c.classe = '" . $_POST['toclass'] . "' AND tel_port != ''";
			
			$result = @mysql_query($query);
			$parents = array();
			while($row = mysql_fetch_assoc($result))
				$parents[] = $row;
			foreach($parents as $p) {
				$to = "237" . $p['tel_port'];
				$urlsend = $url . "action=" . $action . "&userid=" . $userid . "&password=" . $password . "&sender=" . $sender . "&to=" . $to . "&msg=" . $msg;
				$return = file_get_contents($urlsend);
				$response = json_decode($return);
			}
			
			//var_dump($response);
		}
		
		?>
		<center><p class='grand'>SENS SmS to a class</p></center>

<form id="form1" name="form1" method="post" action="">
  <table width="40%" border="0" cellspacing="2" cellpadding="2">
    <tr>
      <th class="p-r-10" scope="row"><div align="right">Sender</div></th>
      <td><input name="sender" type="text" disabled="disabled" id="sender" readonly="readonly" value="SASSE" /></td>
    </tr>
    <tr>
      <th class="p-r-10" scope="row"><div align="right">Recipient</div></th>
      <td><select name="toclass" id="number" >
		<?php foreach($classes as $d){ ?>
			<option value="<?php echo $d['classe']; ?>"><?php echo $d['nom_complet']; ?> </option>
			<?php } ?>
		</select>
	  </td>
    </tr>
    <tr>
      <th class="p-r-10" scope="row"><div align="right">Message</div></th>
      <td><textarea name="message" id="textarea" cols="40" rows="3"></textarea></td>
    </tr>
    <tr>
      <th colspan="2" scope="row"><input type="submit" name="button" id="button" value="Send SMS" /></th>
    </tr>
  </table>
</form><?php
	
	
	} 
	elseif($_GET['mode'] == 'sendpendingsms') {
		$handle = @fopen("sms.txt", "r");
		echo "<br />";
		$sms = array();
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				$sms[] = $buffer;
			}
			if (!feof($handle)) {
				echo "Erreur: fgets() a échoué\n";
			}
			fclose($handle);
		}
		
		$url = "http://iYam.mobi/apiv1/?";
		$nbsmssent = 0;
		foreach($sms as $s) {
			$urlsend = $url . $s;
			$return = file_get_contents($urlsend);
			if($return)
				$response = json_decode($return);
			else {
				$req = "";
			}
			$nbsmssent++;
		}
		echo $nbsmssent . " SMS successfully sent!";
	}
	else {
	
	}
}
else {
	if($_SERVER['REQUEST_METHOD']=='POST') { 
		$action = "sendsms";
		$userid = "726e02c0-b0cc-45d2-8e28-928c9fe84d0e";
		$password = "25fevrier";
		$sender = "SAJOSCOL";
		$to = "237" . $_POST['to'];
		$msg = urlencode($_POST['message']);
		//var_dump($msg);
		$url = "http://iYam.mobi/apiv1/?";
		$urlsend = $url . "action=" . $action . "&userid=" . $userid . "&password=" . $password . "&sender=" . $sender . "&to=" . $to . "&msg=" . $msg;
		
		$return = file_get_contents($urlsend);
		$response = json_decode($return);
		//
	}
	// echo "<pre>";
	// var_dump($_POST);
	// var_dump($urlsend);
	// echo "</pre>";// die;
?>

<center><p class='grand'>Send SmS</p></center>
<?php if(@$response->status == "success") echo '<h2 style="color:blue;">' . 'Message succesfully sent to ' .  $_POST['to'] . '</h2>'; ?>
<form id="form1" name="form1" method="post" action="">
  <table width="40%" border="0" cellspacing="2" cellpadding="2">
    <tr>
      <th class="p-r-10" scope="row"><div align="right">Sender</div></th>
      <td><input name="sender" type="text" disabled="disabled" id="sender" readonly="readonly" value="SASSE" /></td>
    </tr>
    <tr>
      <th class="p-r-10" scope="row"><div align="right">Number</div></th>
      <td><input type="text" name="to" id="number" /></td>
    </tr>
    <tr>
      <th class="p-r-10" scope="row"><div align="right">Message</div></th>
      <td><textarea name="message" id="textarea" cols="40" rows="3"></textarea></td>
    </tr>
    <tr>
      <th colspan="2" scope="row"><input type="submit" name="button" id="button" value="Send SMS" /></th>
    </tr>
  </table>
</form>
<?php
}
require("../lib/footer.inc.php");
?>