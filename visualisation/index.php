<?php
/*
 * $Id: index.php 6197 2010-12-18 20:26:12Z crob $
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
$titre_page = "Tool for visualization";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return home</a></p>
<center>
<p>
You can choose various ways of visualization below :
</p>

<!--table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5 -->
<table class="boireaus" width="700" class="bordercolor" summary="Choix de l'outil">
<tr class='lig1'>
    <td width=200><a href="eleve_classe.php"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> compared to the class</a></td>
    <td>Allows to compare the results of a <?php echo $gepiSettings['denomination_eleve'];?>  to the average of the class, course by course, period per period.</td>
</tr>
<tr class='lig-1'>
    <td width=200><a href="eleve_eleve.php"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> compared to another <?php echo $gepiSettings['denomination_eleve'];?></a></td>
    <td>Allows to compares the results of a <?php echo $gepiSettings['denomination_eleve'];?>  to the results of another <?php echo $gepiSettings['denomination_eleve'];?> (unspecified), course by course, period per period (also allows to compare the results of the past year for a redoubling).</td>
</tr>
<tr class='lig1'>
    <td width=200><a href="evol_eleve.php">Evolution of a <?php echo $gepiSettings['denomination_eleve'];?> over the year</a></td>
    <td>Allows to visualize the evolution of the results of a <?php echo $gepiSettings['denomination_eleve'];?> on the year, course by course.</td>
</tr>
<tr class='lig-1'>
    <td width=200><a href="evol_eleve_classe.php">Evolution of a <?php echo $gepiSettings['denomination_eleve'];?> and class over the year</a></td>
    <td>Allows to visualize the evolution of the results of a <?php echo $gepiSettings['denomination_eleve'];?>  to the evolution of the class, course by course.</td>
</tr>
<tr class='lig1'>
    <td width=200><a href="stats_classe.php">Evolution of the averages of classes</a></td>
    <td>Allows to obtain the various averages of the class (maxi, mini, average, etc.) course by course, with evolution over the year.</td>
</tr>
<tr class='lig-1'>
    <td width=200><a href="classe_classe.php">Class compared to another class</a></td>
    <td>Allows to compare the results of a class to another class, course by course, period per period.</td>
</tr>
<tr class='lig1'>
    <td width=200><a href="affiche_eleve.php?type_graphe=courbe"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> compared to a <?php echo $gepiSettings['denomination_eleve'];?> or an average</a></td>
    <td><b>Graph in curve</b>: Allows to compare the results of a <?php echo $gepiSettings['denomination_eleve'];?>, to the averages min/max/class and compared to another <?php echo $gepiSettings['denomination_eleve'];?>, course by course, period per period.<br />Alternatively, this choice makes it possible to obtain the 3 quarter curves.</td>
</tr>
<tr class='lig-1'>
    <td width=200><a href="affiche_eleve.php?type_graphe=etoile"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> par rapport à un <?php echo $gepiSettings['denomination_eleve'];?> ou une moyenne</a></td>
    <td><b>Graph in star/polygon</b>: Allows to compare the results of a <?php echo $gepiSettings['denomination_eleve'];?>,  to the averages min/max/class and compared to another <?php echo $gepiSettings['denomination_eleve'];?>, course by course, period per period.<br />Alternatively, this choice makes it possible to obtain the 3 quarter polygons.</td>
</tr>
</table>
<p><br /></p>
</center>
<?php require("../lib/footer.inc.php");?>