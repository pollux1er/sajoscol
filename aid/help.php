<?php
/*
 * @version: $Id: help.php 2147 2008-07-23 09:01:04Z tbelliard $
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
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

//**************** EN-TETE *****************
$titre_page = "Aide en ligne";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>

<H2>Complementary tools for management of IDA</H2>
<p>By activating < B >complementary tools  to management of IDA</b>, you have access to additional fields (attribution of a room, possibility of defining a summary, the type of
production, keyword, a public recipient?).</p>

<p>These additional data are accessible through cards known as "cards project ".</p>

<p>These cards are accessible in GEPI to different types of connected users (administrator, professor, cpe, raise or responsable)</p >

<p>These cards are also partly accessible in the public interface from GEPI to different. A configuration makes it possible to determine the visible fields or not by the public.</p >

<p>According to his statute (responsible professors, responsible cpe or pupils) and when the administrator opened this possibility, the user has access in modification to certain fields of this card.</p>
<p>In addition to the professors person in charge for each AID, the
administrator can designate users (professors or CPE) having the right to modify the cards project even when the administrator disabled
this possibility for the professors responsables.</p >
<?php require("../lib/footer.inc.php");?>