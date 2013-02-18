<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: $
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
*
*/

/**
* Appelle les sous-modèles
* templates/origine/header_template.php
* templates/origine/bandeau_template.php
 *
 * @author regis
 */


?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
	<?php
	  $tbs_bouton_taille = "..";
	  include('../templates/origine/header_template.php');
	?>

  <script type="text/javascript" src="../templates/origine/lib/fonction_change_ordre_menu.js"></script>

	<link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/gestion.css" media="screen" />

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie7.css' media='screen' />
	<![endif]-->


<!-- Style_screen_ajout.css -->
	<?php
		if (count($Style_CSS)) {
			foreach ($Style_CSS as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
				}
			}
		}
	?>

<!-- Fin des styles -->



</head>


<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php echo $tbs_charger_observeur;?>">

<!-- on inclut le bandeau -->
	<?php include('../templates/origine/bandeau_template.php');?>

<!-- fin bandeau_template.html      -->

  <div id='container'>
<!-- Fin haut de page -->

  <h2>General configuration</h2>
  
  <p>
	<em>
	  The deactivation of the model module Open Office does not involve any
delection of the data. 
	  When the module is deactivate, it is not possible any more to manage its own models.
	</em>
  </p>
  <form action="ooo_admin.php" id="form1" method="post">
	<fieldset class="no_bordure">
<?php
echo add_token_field();
?>
	  <legend class="invisible">Activation</legend>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			<?php if (getSettingValue("active_mod_ooo")=='y') echo " checked='checked'"; ?> />
	  <label for='activer_y'>
		Activate the model module Open Office
	  </label>
	  <br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			<?php if (getSettingValue("active_mod_ooo")=='n') echo " checked='checked'"; ?> />
	  <label for='activer_n'>
		deactivate the model module Open Office
	  </label>
	</fieldset>

<?php

echo "<p><span class='bold'>Relief cock of file &nbsp;:</span> <br /><em>Gepi needs a relief cock of file to create the documents OOo.</em></p>\n";

echo "<p>";
$fb_dezip_ooo=getSettingValue("fb_dezip_ooo");
echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_0' value='0' ";
if($fb_dezip_ooo=="0"){
	echo "checked='checked' />";
}
else{
	echo "/>";
}
echo "<label for='fb_dezip_ooo_0'> ZIPARCHIVE and TinyDoc : the choice by defect but can create files corrompus if your version of PHP is lower than 5.2.8 (<em>Use OOo 3.2 to repair the files</em>) </label><br />\n";

echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_1' value='1' ";
if($fb_dezip_ooo=="1"){
	echo "checked='checked' />";
}
else{
	echo "/>";
}
echo "<label for='fb_dezip_ooo_1'> ZIP-UNZIP and TinyDoc : require that ZIP et UNZIP are installed on the waiter and that their ways are defined in the
variable of environment PATH </label><br />\n";

echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_2' value='2' ";
if($fb_dezip_ooo=="2"){
	echo "checked='checked' />";
}
else{
	echo "/>";
}
echo "<label for='fb_dezip_ooo_2'> PCLZIP and TBSooo : classify older, all functionalities of TinyDoc are not available in the gauges but functions with PHP 5.2 </label><br />\n";

echo "</p>";
?>

	
	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer"/>
	</p>
</form>


<?php
  if (count($droitRepertoire)){
	foreach ($droitRepertoire as $droit){
	  echo "<p class='grandEspaceHaut rouge bold'>".$droit."</p>";
	}
	unset($droit);
  }

?>






<!-- Début du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
	  //<![CDATA[
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
	  //]]>
	</script>


	<script type='text/javascript'>
	  //<![CDATA[
		temporisation_chargement='ok';
	  //]]>
	</script>

</div>

		<?php
			if ($tbs_microtime!="") {
				echo "
   <p class='microtime'>Page generated in ";
   			echo $tbs_microtime;
				echo " sec</p>
   			";
	}
?>

		<?php
			if ($tbs_pmv!="") {
				echo "
	<script type='text/javascript'>
		//<![CDATA[
   			";
				echo $tbs_pmv;
				echo "
		//]]>
	</script>
   			";
		
	}
?>

</body>
</html>

