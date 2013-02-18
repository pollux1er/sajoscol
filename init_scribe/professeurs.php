<?php
/*
 * $Id: professeurs.php 5939 2010-11-21 18:41:12Z crob $
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
$titre_page = "Tool of initialization of the year : Importation of the professors";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='../init_scribe/index.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>";

if (isset($_POST['is_posted'])) {
	check_token();

	// L'admin a validé la procédure, on procède donc...
	include "../lib/eole_sync_functions.inc.php";
	// On commence par récupérer tous les profs depuis le LDAP
	$ldap_server = new LDAPServer;
	$sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(&(uid=*)(objectclass=administrateur))");
	$info = ldap_get_entries($ldap_server->ds,$sr);
	
	// On met tous les professeurs en état inactif
	$update = mysql_query("UPDATE utilisateurs SET etat='inactif' WHERE statut='professeur'");
	
	for($i=0;$i<$info["count"];$i++) {
		
		// On ajoute l'utilisateur. La fonction s'occupe toute seule de vérifier que
		// le login n'existe pas déjà dans la base. S'il existe, on met simplement à jour
		// les informations
		
		// function add_user($_login, $_nom, $_prenom, $_civilite, $_statut, $_email) {
		
		// Scribe NG : ne pas modifier l'utilisateur admin
		if ($info[$i]["uid"][0] == "admin") {
			continue;
		}
		
		
		// Le mail et le code civilité ne sont pas systématiquement renseignés...
		
		if (!array_key_exists("mail", $info[$i])) {
			$info[$i]["mail"] = array();
			$info[$i]["mail"][0] = null;
		}
		
		if (!array_key_exists("codecivilite", $info[$i])) {
			$info[$i]["codecivilite"] = array();
			$info[$i]["codecivilite"][0] = 1;
		}
		
		$add = add_user($info[$i]["uid"][0],
						$info[$i]["sn"][0],
						$info[$i]["givenname"][0],
						$info[$i]["codecivilite"][0],
						"professeur",
						$info[$i]["mail"][0]
						);
					
						// Debug :
						//echo "<pre>";
						//print_r($info[$i]);
						//echo "</pre><br/><br/>";
	}
	
	echo "<p>Operation done.</p>";
	echo "<p>You can check the importation while going on the page of <a href='../utilisateurs/index.php'>management of the users</a>.</p>";
	echo "<br />";
	echo "<p><center><a href='disciplines.php'>Next phase : importation of courses</a></center></p>";
	
} else {
	echo "<p>The operation of importation of the professors from the LDAP of Scribe will carry out the following operations :</p>";
	echo "<ul>";
	echo "<li>Passage to the ' inactive' state  of all the professors already present in the Gepi base</li>";
	echo "<li>Attempt of addition of each user ' professor' present in the LDAP</li>";
	echo "<li>If the user does not exist, it is created and is directly usable</li>";
	echo "<li>If the user already exists, its basic information is updated and it passes in ' active ' state, becoming directly usable</li>";
	echo "</ul>";
	echo "<form enctype='multipart/form-data' action='professeurs.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='is_posted' value='yes'>";
    
    echo "<p>Are you sure you want to import all the users from the directory of the Scribe server towards Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='I am sure'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>