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

// INSERT INTO droits VALUES ('/mod_annees_anterieures/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Index données antérieures', '');
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




//**************** EN-TETE *****************
$titre_page = "Former data";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<div class='norme'><p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a> | \n";
echo "</p></div>\n";

echo "<p>Perhaps the use of this module requires a particular authorization at
the CNIL.<br />
Here for information an extract of mall passed on the mailing list [gepi-users]:</p>\n";
echo "<pre>it is in my document
http://julien.noel.free.fr/gepi/arguments_gepi_v_1.0.3.sxw

article 4
« Shelf life : With the exception of those concerning the class, the group, division
attended and of the options followed during the preceding school year
which can be preserved during two school years, the information relating to the schooling of the student like to their
financial standing aimings in article 3 C and D should not be stored
beyond the school year for which they were recorded, except contrary legal provisions; Information relating to the identity
of the student as well as legal responsible sound aimed to article 3 has
and B should not be preserved beyond the departure of the student of the
establishment. ». See
http://www.cnil.fr/index.php?id=1232 for more information.</pre>\n";
echo "<p><br /></p>\n";


echo "<p>With the menu:</p>\n";
echo "<p>Pages of administration:</p>\n";
echo "<ul>\n";
echo "<li><p><a href='nettoyer_annee_anterieure.php'>Cleaning of the data of student having left the establishment</a>.</p></li>\n";
echo "<li><p><a href='corriger_ine.php'>Correction of the nonwell informed or badly well informed INE at the time of the conservation</a>.</p></li>\n";
echo "<li><p><a href='conservation_annee_anterieure.php'>Conservation of the data (*) dE the year which finishes</a><br />(<i>(*) others that AIDs</i>).</p></li>\n";
echo "<li><p><a href='archivage_aid.php'>Conservation of the data of AIDs</a>.</p></li>\n";
echo "</ul>\n";

echo "<p>Pages of consultation:</p>\n";
echo "<ul>\n";
echo "<li><p><a href='consultation_annee_anterieure.php'>Consult the former seizures</a></p></li>\n";
echo "<li><p>A function of consultation opening in popup: popup_annee_anterieure.php?logineleve=...<br />The function and the page are ready... remains to be placed here and
there of the bonds towards  popup_annee_anterieure.php?logineleve=... while testing if the module is activated, if the statute of the user
gives access to him,...</p></li>\n";
echo "<li><p>Consultation of a summary of the opinions of the staff meetings.<br />The function and the page are ready? remains to be placed here and
there of the bonds towards popup_annee_anterieure.php?logineleve=... while testing if the module is activated, if the statute of the user
gives access to him,...</p></li>\n";
echo "</ul>\n";

/*
echo "<p>...</p>\n";
echo "<ul>\n";
echo "<li><p>A FAIRE: Une page de recherche selon divers critères: nom, prénom, année,...</p></li>\n";
echo "<li><p>...</p></li>\n";
echo "</ul>\n";
*/

require("../lib/footer.inc.php");
?>