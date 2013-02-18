<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: help_template.php 4951 2010-07-29 13:13:17Z regis $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

<!-- fil d'ariane -->
<div id="cache_ariane" class="bold">
<?php
  if (isset($messageEnregistrer) && $messageEnregistrer !="" ){
	affiche_ariane(TRUE,$messageEnregistrer);
  }else{
	if(isset($_SESSION['ariane']) && (count($_SESSION['ariane']['lien'])>1)){
	  affiche_ariane();
	}
  }
?>
  </div>
  <script type='text/javascript'>
	//<![CDATA[
	document.getElementById('cache_ariane').addClassName("invisible");
	//]]>
  </script>
<!-- fin fil d'ariane -->

  <div id='container'>
	
  <h1>The module Inscription</h1>
  <p>
	The Inscription module makes it possible to define one or more items (day, training course, intervention, ...), with the which users will be able to be registered or unsubcrible by
notching or stripping a cross.
  </p>
  <ul>
	<li>The configuration of the module is accessible to the administrators
and the schooling.</li>
	<li>The interface of registry/unregistry is accessible to the professors, cpe, administrators and schooling.</li>
  </ul>

  <p>
	After having activated the module, the administrators and the
schooling have in the banner page of a new module configuration.
  </p>
  <p>The first stage consists in configuring this module :</p>
  <ul>
	<li>
	  <span class="bold">
		Activation / Deactivation :
	  </span>
	  <br />
	  As long as the module is not entirely configured, you may find it
beneficial not to activate the page authorizing the inscriptions. In this way, this module remains invisible with the other users (professors and
cpe).
	  <br />
	  In the same way, when the inscriptions are closed, you can
deactivate the registry, while keeping
	  the access to the module of configuration.
	</li>
	<li>
	  <span class="bold">
		List items :
	  </span>
	  <br />
	  It is the list of the entities to which the users will be able
register.
	  <br />
	  Each entity is caraterized by a numerical identifier, a date (format AAAA/MM/JJ),
	  a hour (20 characters max), a description (200 characters max).
	</li>
	<li>
	  <span class="bold">
		Title of module :
	  </span>
	  <br />
	  You have here the possibility of personalizing the heading of the
visible module in the banner page.
	</li>
	<li>
	  <span class="bold">
		Text explanatory :
	  </span>
	  <br />
	  This text will be visible by the people reaching the module of
register/unregister.
	</li>
  </ul>


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

