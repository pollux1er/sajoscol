<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: edt_template.php 6697 2011-03-25 21:54:27Z regis $
 * *
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
* ******************************************** *
* Appelle les sous-modèles                     *
* templates/origine/header_template.php        *
* templates/origine/bandeau_template.php       *
* ******************************************** *
*/

/**
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

	<link rel="stylesheet" type="text/css" href="../templates/origine/css/accueil.css" media="screen" />
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
			unset ($value);
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
	
	<h2>Management of the access to the timetable</h2>
	
	<p>
	  (All accounts except student and responsible)
	</p>
	
	<hr />
	
	<form action="edt.php" method="post" id="autorise_edt">
		<fieldset class="no_bordure">
<?php
echo add_token_field();
?>
		  <legend class="invisible">Activation of the EDT</legend>
		  <em>
			The desactivation of the timetables does not involve any suppression
of the data. When the module is deactivated, nobody has access to the module and the consultation of the timetables is impossible.
		  </em>
		  <br />
		  
		  <input name="activ_tous"
				 id="activTous"
				 value="y"
				 type="radio"<?php echo eval_checked("autorise_edt_tous", "y"); ?>
				 onclick="document.getElementById('autorise_edt').submit();"
				 />
		  <label for="activTous">
			Activate the timetables for all the users
		  </label>
		  <br />
		  <input name="activ_tous"
				 id="activPas"
				 value="n"
				 type="radio"<?php echo eval_checked("autorise_edt_tous", "n"); ?>
				 onclick="document.getElementById('autorise_edt').submit();"
				 />
		  <label for="activPas">
			Deactivate timetables for all the users
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_active" />
		  </span>
		</fieldset>

	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_active').className = 'invisible';

		//]]>
	</script>

	<form action="edt.php" method="post" id="autorise_prof">
		<fieldset class="no_bordure grandEspaceHaut">
<?php
echo add_token_field();
?>
		  <legend class="invisible">Activation for the teachers</legend>
		  <input type="radio"
				 name="autorise_saisir_prof"
				 id="autoProf"
				 value="y"<?php echo eval_checked("edt_remplir_prof", "y"); ?>
				 onclick="document.getElementById('autorise_prof').submit();"
				 />
		  <label for="autoProf">
			Authorize the professor to type his timetable
		  </label>
		  <br />

		  <input type="radio"
				 name="autorise_saisir_prof"
				 id="autoProfNon"
				 value="n"<?php echo eval_checked("edt_remplir_prof", "n"); ?>
				 onclick="document.getElementById('autorise_prof').submit();"
				 />
		  <label for="autoProfNon">
			Prohobit the professor to type his timetable
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_prof" />
		  </span>
		</fieldset>
	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_prof').className = 'invisible';
		//]]>
	</script>
	
	<form action="edt.php" method="post" id="autorise_admin">
		<fieldset class="no_bordure grandEspaceHaut">
<?php
echo add_token_field();
?>
		  <legend class="invisible">Activation for the administrators</legend>
		  <em>Accounts </em>administrator<em>  has access to the timetables if this one is activated for them.
		  If you deactivate; the access for all, you can nevertheless authorize the accounts
		  </em>administrator<em> to have access.</em>
		  <br />
		  <input name="activ_ad"
				 id="activAdY"
				 value="y"
				 type="radio"<?php echo eval_checked("autorise_edt_admin", "y"); ?>
				 onclick="document.getElementById('autorise_admin').submit();"
				 class="grandEspaceHaut"
				 />
		  <label for="activAdY">
		 activate the timetables for the administrators
		  </label>

		  <br />
		  <input name="activ_ad"
				 id="activAdN"
				 value="n"
				 type="radio"<?php echo eval_checked("autorise_edt_admin", "n"); ?>
				 onclick="document.getElementById('autorise_admin').submit();"
				 />
		  <label for="activAdN">
			Deactivate timetables for the administrators
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_admin" />
		  </span>
		</fieldset>
	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_admin').className = 'invisible';
		//]]>
	</script>
	
	<hr />

	<h2> Management of the access for the students and their responsibles</h2>

	<form action="edt.php" method="post" id="autorise_ele">
	  <p>
<?php
echo add_token_field();
?>
			<em>
				If you wish to make available their timetable to the students and their responsibles,
				it should imperatively be authorized here.
			</em>
	  </p>

		<fieldset class="no_bordure grandEspaceHaut">
		  <legend class="invisible">Activation for the students and their responsibles</legend>
		  <input name="activ_ele"
				 id="activEleY"
				 value="yes"
				 type="radio"<?php echo eval_checked("autorise_edt_eleve", "yes"); ?>
				 onclick="document.getElementById('autorise_ele').submit();"
				 />
		  <label for="activEleY">
		 activate the timetables for the students and their responsibles
		  </label>

		  <br />
		  <input name="activ_ele"
				 id="activEleN"
				 value="no"
				 type="radio"<?php echo eval_checked("autorise_edt_eleve", "no"); ?>
				 onclick="document.getElementById('autorise_ele').submit();"
				 />
		  <label for="activEleN">
			Deactivate timetables for the students and their responsibles
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Save" id="btn_eleve" />
		  </span>
		</fieldset>
	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_eleve').className = 'invisible';
		//]]>
	</script>
	
	
	
	
	


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

