<?php

/*
 * Last modification  : 14/03/2005
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


//**************** EN-TETE *****************
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************

?>
<H1 class='gepi'>GEPI - Private life</H1>
<?php
echo "<h2>Legal framework</h2>";
echo "<p>Gepi is a software of processing of data entering within the framework of the Numerical Environments of Work (ENT).";
echo "<br/>This title, it is subjected to a particular legal framing. We invite you to consult <a href='http://www.cnil.fr/vos-responsabilites/declarer-a-la-cnil/declarer-un-fichier/declaration/mon-secteur-dactivite/mon-theme/je-dois-declarer/declaration-selectionnee/dec-mode/DISPLAYSINGLEFICHEDECL/dec-uid/30/'>the Decree of November 30, 2006</a> relating to the devices of processing of data within the ministry for national education.</p>";

if (getSettingValue("num_enregistrement_cnil") != '')  {

echo "<h2>Declaration of the CNIL</h2>";

echo "In accordance with article 16 of law 78-17 of January 6, 1978, said data-processing law and freedom, we inform you

 that this site was the subject of a declaration of automated treatment of personal information at the CNIL   : the site is recorded under N° ".getSettingValue("num_enregistrement_cnil");

}

echo "<H2>1/ Cookies</H2>";

echo "With each one of your visits GEPI tries to generate a session cookie. The acceptance of this cookie by your navigator is obligatory to
access to the site. This cookie of session is a temporary cookie required for

reasons of safety. This type of cookie does not record information on your computer, it allots a number of session to you

 that it communicates to the server to be able to follow your session in full safety. It is put temporarily in the memory of

  our computer and is exploitable only during the time of connection. It is then destroyed when you disconnect yourselves or when you close all the windows of your navigator.";



echo "<H2>2/ Transmitted information</H2>";



echo "At the time of the opening of a session certain information is transmitted to the server :

<ul>

<li>the number of your session ( see above),</li>

<li>your identifier,</li>

<li>address IP of your machine,</li>

<li>the type of your navigator,

<li>the origin of connection to the present site,</li>

<li>hours and dates of beginning and end of the session.</li>

</ul>";

switch (getSettingValue("duree_conservation_logs")) {

case 30:

$duree="one month";

break;

case 60:

$duree="two months";

break;

case 183:

$duree="six months";

break;

case 365:

$duree="one year";

break;

}

echo "For reasons of safety, this information is preserved during <b>".$duree."</b> starting from their recording.";



echo "<H2>3/ Safety</H2>";

echo "<b>By safety measure, think of disconnecting you at the end of your visit on the site (bond in top on the right).</b>";

require("../lib/footer.inc.php");
?>