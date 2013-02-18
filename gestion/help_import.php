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
$titre_page = "On line help";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<p class=bold>Importation to importation</p>
<p>The file of importation must be in the format csv (separator : semicolon)
<br />The file must contain the various following fields, all obligatory :<br />
--> <B>IDENTIFIER</B> : the identifier of the student<br />
--> <B>Name</B><br />
--> <B>First name</B><br />
--> <B>Sex</B>  : F ou M<br />
--> <B>Date of birth</B> : dd/mm/yyyy<br />
--> <B>Classify (fac.)</B> : the short name of a class already defined in base GEPI or the
character - if the student is not affected to a class.<br />
--> <B>Regim</B> : d/p (school luncher) ext. (external) int. (intern) ou i-e (intern externed)<br />
--> <B>Doubling</B> : R (for a doubling)  - (for a non-doubling)<br />
--> <B><?php echo ucfirst(getSettingValue("gepi_prof_suivi")); ?></B> : the identifier of a  <?php echo getSettingValue("gepi_prof_suivi"); ?> already defined in base GEPI or the character - if the student does
not have <?php echo getSettingValue("gepi_prof_suivi"); ?>.<br />
--> <B>Identifier of the school of origin </B> : code RNE identifying each school school and already defined in
base GEPI, or character - if the school is not known.<br /></p>

<p class='bold'>IDENTIFIER</p>
<p>Identifier of the student : it can be a question of any continuation of characters and/or of number without space. If this format is not respected, the continuation of characters ??? appears in the place of the identifier. The identifiers which appear in red correspond to names of user already existing in GEPI base. The existing data will then be crushed by the data present in the file
to import !</p>
<p class='bold'>Name</p>
<p>Name of the student. It can be a question of any continuation of characters and/or of number with possibly of spaces</p>
<p class='bold'>Prénom</p>
<p>First name of the student. Same notices that for the name. The names and first names which appear in blue correspond to students existing in base GEPI and having the same names and first names.</p>
<p class='bold'>Sex</p>
<p>The only accepted characters are F for female and M for masculine (respect the capital letters). If this format is not respected, the continuation of characters ??? appears.</p>
<p class='bold'>Date of birth</p>
<p>It is about the date of birth of the student. The only authorized format is dd/mm/yyyy. For example, for a student born on April 15, 1985, one will type 15/04/1985. If this format is not respected, the continuation of characters???
appears.</p>
<p class='bold'>Classe</p>
<p>Class in which the student is affected. Only the accepted data are :
<br />--> the short name of a class already defined in GEPI base 
<br />--> or character - if the student is not affected to a class.
<br />If the class is not defined in base GEPI, this one will be regarded as erroneous.
<br />The procedure of importation does not make it possible to change a student of class.
<br />On the other hand, it is possible to assign to a class, an existing student of the base which is not already affected to a class.<br /></p>
<p class='bold'>Regim</p>
<p>The only continuations of characters accepted are "d/p", "ext.", "int." et "i-e" (respect the minuscules). In all the other cases, the continuation of characters??? appears.
<br />--> d/p for school luncher,
<br />--> ext. for external,
<br />--> int. for intern,
<br />--> i-e  for externed intern .</p>
<p class='bold'>Doublant</p>
<p>The only accepted characters are "R" and "-". In all the other cases, the continuation of character??? appears.
<br />--> R for doubling,
<br />--> - for non-doubling.</p>
<p class='bold'><?php echo ucfirst(getSettingValue("gepi_prof_suivi")); ?></p>
<p>The identifier of a <?php echo getSettingValue("gepi_prof_suivi"); ?> already defined in base GEPI or the character - if the student does not have <?php echo getSettingValue("gepi_prof_suivi"); ?>.
<br />It is obligatorily about a professor of the class of the student. In the contrary case, the continuation of characters??? appears. It is the same if the class is not defined.</p>
<p class='bold'>Identifier of the school of origin </p>
<p>Code RNE identifying each school school and already defined in
base GEPI, or character - if the school is not known.<br /></p>
<?php require("../lib/footer.inc.php");?>