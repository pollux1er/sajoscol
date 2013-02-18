<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id: mod_discipline_template.php 4973 2010-07-31 16:50:27Z regis $
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
*
* ******************************************** *
* Appelle les sous-modèles                     *
* templates/origine/header_template.php        *
* templates/origine/bandeau_template.php       *
* ******************************************** *
*/
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
	<?php
	  $tbs_bouton_taille = "..";
	  include('../templates/origine/header_template.php');
	?>

	<link rel="stylesheet" type="text/css" href="../templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />


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


	<a name="contenu" class="invisible">Beginning of the page</a>
	
	<p class="center">
	  This module is intended to seize and follow the incidents and
sanctions.
	</p>

<!-- début corps menu	-->
<?php
	if (count($menuTitre)) {
		foreach ($menuTitre as $newEntreeMenu) {
?>
		<h2 class="<?php echo $newEntreeMenu->classe ?>">
			<img src="<?php echo $newEntreeMenu->icone['chemin'] ?>" alt="<?php echo $newEntreeMenu->icone['alt'] ?>" /> - <?php echo $newEntreeMenu->texte ?>
		</h2>


<?php
		if (count($menuPage)) {
			foreach ($menuPage as $newentree) {
			  if ($newentree->indexMenu==$newEntreeMenu->indexMenu) {

?>
				<div class='div_tableau'>
				  <h3 class="colonne ie_gauche">
					  <a href="<?php echo "../".substr($newentree->chemin,1) ?>">
						  <?php echo $newentree->titre ?>
					  </a>
				  </h3>
				  <p class="colonne ie_droite">
					  <?php echo $newentree->expli ?>
				  </p>
				</div>
<?php
			  }
			}
		}

	  }
	}
?>

<!-- Fin menu	général -->
<p>
  <em>NOTES&nbsp;</em>
</p>
<ul>
  <li>
	<p>
	  Once a closed incident, it cannot be modified any more and no dependent sanction can be added/modified/removed.
	</p>
  </li>
  <li>
	<p>
	  The module does not preserve a history of the modifications of an
incident.<br />If several people modify an incident, they must do it in good intelligence.
	</p>
  </li>
  <li>
	<p>A professor can seize an incident, but cannot seize the sanctions.<br />
A professor can modify only the incidents (<em>not closed</em>) that it declared it self.<br />It can consult only the incidents (<em>and their continuations</em>) that it declared, or of which he is protagonist, or of which one of
the student, of which he is a principal professor, is protagonist.
	</p>
  </li>
  <li>
	<p>
	  <em>TO MAKE:</em>
	  Add tests 'changement()' in the pages of seizure not to leave a stage without recording.
	</p>
  </li>
  <li>
	<p>
	  <em>TO MAKE:</em>
	  To allow to consult other incidents that them his clean.<br />Possibly with limitation with the student of its classes.
	</p>
  </li>
  <li>
	<p>
	  <em>TO STILL MAKE:</em>
	  To allow to file the incidents/sanctions one year and to empty the tables incidents/sanctions at the time of initialization to avoid jokes with the login
student realloted with new student (<em>homonymy,...</em>)
	</p>
  </li>
</ul>

<!-- Début du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>


	<script type='text/javascript'>
		temporisation_chargement='ok';
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


