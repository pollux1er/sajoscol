<?php
/*
 * $Id: index.php 2554 2008-10-12 14:49:29Z crob $
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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// SQL : INSERT INTO droits VALUES ( '/mod_ooo/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Mod�le Ooo : Index', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Mod�le Ooo: Index : Index', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}


//**************** EN-TETE *****************
$titre_page = "Open Model Office";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a>";

echo "</p>\n";

echo "<p>This module is intended to manage the models Open Office of Gepi.</p>\n";

$phrase_commentaire="";
$_SESSION['retour']=$_SERVER['PHP_SELF'] ;


//D�but de la table configuration
if($_SESSION['statut']=='administrateur') {
  echo "<table class='menu' summary='Modele Open Office'>\n";
  echo "<tr>\n";
  echo "<th colspan='2'><img src='../images/icons/control-center.png' alt='Configuration of the Model module Open Office' class='link'/> - Configuration of the module</th>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td width='30%'><a href='../mod_ooo/gerer_modeles_ooo.php'Manage its own models of document</a>";
  echo "</td>\n";
  echo "<td> Manage its own models of Open document Office</td>\n";
  echo "</tr>\n"; 
  echo "</table>\n";
}
//fin de la table configuration

// Table Formulaires
echo "<table class='menu' summary='Modele Open Office'>\n";
echo "<tr>\n";
echo "<th colspan='2'><img src='../images/icons/saisie.png' alt='Open Forms Office' class='link'/> - List forms</th>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td width='30%'><a href='../mod_ooo/formulaire_retenue.php'>Reserve</a>";
echo "</td>\n";
echo "<td>Seize the form of reserve to print it</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo $phrase_commentaire;

echo "<p><br /></p>\n";


echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
