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

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


// header
$titre_page = "Export Admissions Post-Bac";
require_once("../lib/header.inc");
?>

<p class=bold><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Options générales</h2>
<form action="export_xml.php" name="form1" method="post">
<p>Numéro de l'export : <select name="num_export">
<?php
for($i=1;$i<5;$i++) {
  echo '<option value="'.$i.'">'.$i.'</option>';
}
?>
</select><br/>
<i>Note: you should have only two exports to carry out (the first in the
current of the year, with the opening of the increase of notes, the second to transmit the
results of the last period).</i></p>


<h2>Periods to be exported</h2>
<p>Please indicate below the last period for which the bulletins were
already published.<br/>
Caution! The exported data then will be integrated in the platform of
management of the admissions post-vat. Do not select a period for which all the averages would not have been
seized.</p>

<?php
// Sélection des classes concernées par l'export
$req_classes = mysql_query("SELECT id,classe,nom_complet, MAX(p.num_periode) periodes FROM classes c, periodes p WHERE c.apb_niveau = 'terminale' AND p.id_classe = c.id GROUP BY c.id");

$all_classes = array();
while($classe = mysql_fetch_object($req_classes)) {
  if (!array_key_exists($classe->periodes,$all_classes)) {
    $all_classes[$classe->periodes] = array();
  }
  $all_classes[$classe->periodes][] = $classe->classe;
}

while (list($key, $val) = each($all_classes)) {
    echo '<h3>Classes à '.$key.' périodes</h3>';
    echo '<p>Classes concernées : ';
    echo implode(', ',$val);
    echo '</p>';
    echo '<p>Dernière période définitivement saisie :<br/>';
    for($i=1;$i<=$key;$i++) {
      echo '<input type="radio" name="'.$key.'per" value="'.$i.'" /> Période '.$i.'<br/>';
    }
    echo '</p>';
}


?>
<p style='margin-top: 50px;'><input type="submit" value="Générer le fichier XML" style="font-variant: small-caps;"/></p>

<?php
	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
?>
