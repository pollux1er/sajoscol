<?php
/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard
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
$titre_page = "Tool of initialization of the year for directory LDAP Scribe NG";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="../gestion/index.php#init_scribe_ng"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<p>You will carry out the initialization of the school year which has
just begun, directly from directory LDAP of Scribe NG. This one must thus be fed beforehand. This procedure is carried out only once.</p>

<?php
	echo "<p>You thought of carrying out the various operations of end of the year
and preparation of new year in the page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Change of year</a>&nbsp?</p>\n";
?>

<ul>
<li>During the procedure, if necessary, certain data of the last year will be definitively erased base
GEPI (students, notes, appreciations, ...) . Only will be preserved the following data :<br /><br />
- data relating to the schools,<br />
- data relating to the classes : short headings, long headings, number of periods and names of the periods,<br />
- data relating to the courses : complete identifiers and headings,<br />
- data relating to the personal users in the school (professors, administrators, ...). Concerning the professors, the courses taught by the professors are preserved (and possibly supplemented in phase 5),<br />
- Data relating to the various types of IDA.</li><br />

<li>Initialization is carried out in various phases :<br />
    <ul>
    <br />
    <li><a href='etape1.php'>Proceed to the first phase</a> of importation of the students and creation of the classes.</li>
    <br />
    <li><a href='etape2.php'>Proceed to the second phase</a> of creation of the periods and assignment of the students in the classes.</li>
    <br />
    <li><a href='etape3.php'>Proceed to the third phase</a> of importation of the legal responsibles of the students.</li>
    <br />
    <li><a href='etape4.php'>Proceed to the fourth phase</a> of importation of the professors.</li>
    <br />
    <li><a href='etape5.php'>Proceed to the fifth phase</a> of importation of the courses.</li>
    <br />
    <li><a href='etape6.php'>Proceed to the sixth phase</a> of importation of the courses.</li>
    <br />
    <li><a href='etape7.php'>Proceed to the seventh phase</a> of importation of the not-teachers personnel .</li>
    <br />
    <br />
</li>
<li>Once all procedure of initialization of the data finished, it will be possible for you to carry out all the modifications necessary to individually by the means of the management tools included in <b>GEPI</b>.</li>
</ul>
<?php require("../lib/footer.inc.php");?>
