<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id: index_template.php 7866 2011-08-21 14:33:24Z jjacquard $
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

  <form action="index.php" id="form1" method="post">
	<p class="center">
<?php
	echo add_token_field();
?>
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer" />
	</p>
	
	<h2 class="colleHaut">General configuration</h2>
	<p class="italic">
	  The desactivation of the report cards does not involve any suppression of the data. 
	  When the module is decontaminated, the professors do not have access to the module.
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activated or not</legend>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			<?php if (getSettingValue("active_carnets_notes")=='y') echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_y' style='cursor: pointer;'>
		Activate the report cards
	  </label>
	<br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			<?php if (getSettingValue("active_carnets_notes")=='n') echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_n' style='cursor: pointer;'>
		Deactivate report cards
	  </label>
	</fieldset>
	
	<p class="grandEspaceHaut">
<?php
	if(file_exists("../lib/ss_zip.class.php")){ 
?>
	  <input type='checkbox' 
			 name='export_cn_ods'
			 id='export_cn_ods'
			 value='y'
			 onchange='changement();'
<?php
	  if(getSettingValue('export_cn_ods')=='y'){
?>
		checked="checked"
<?php
	  }
?>
	  />
	  <label for='export_cn_ods' style='cursor: pointer;'>
		Allow the export of the report cards in format ODS.
	  </label>
	  <br />
	  (<em>if the professors do not do the cleaning after generation of exports, these files can take place on the server</em>)\n";
<?php
	}
	else{
?>
	  By setting up the library 'ss_zip_.class.php' in the folder '/lib/', you can generate spreadsheet files ODS to allow typing off line, conservation of data,...
	  <br />
	  Voir <a href='http://smiledsoft.com/demos/phpzip/'>http://smiledsoft.com/demos/phpzip/</a>
	</p>
	<p>
	  A limited version is available free.
	  <br />
	  Alternative site:
	  <a href='http://stephane.boireau.free.fr/informatique/gepi/ss_zip.class.php.zip'>
		http://stephane.boireau.free.fr/informatique/gepi/ss_zip.class.php.zip
	  </a>

<?php
	  // Comme la bibliothèque n'est pas présente, on force la valeur à 'n':
	  $svg_param=saveSetting("export_cn_ods", 'n');
	}
?>
	</p>

	<h2>
	  Referential of notes:
	</h2>
	<p>
	  Referential of notes by default : 
	  <input type="text" 
			 name="referentiel_note" 
			 size="8"
			 title="notes sur"
			 value="<?php echo(getSettingValue("referentiel_note")); ?>" />
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Referential or no </legend>
	  <input type="radio" 
			 name="note_autre_que_sur_referentiel" 
			 id="note_sur_referentiel" 
			 value="V" 
			 <?php if(getSettingValue("note_autre_que_sur_referentiel")=="V"){echo "checked='checked'";} ?> />
	  <label for='note_sur_referentiel'> 
		Authorize the notes other than which on the default reference 
	  </label>
	  <br />
	  <input type="radio" 
			 name="note_autre_que_sur_referentiel" 
			 id="note_autre_que_referentiel" 
			 value="F" 
			 <?php if(getSettingValue("note_autre_que_sur_referentiel")=="F"){echo "checked='checked'";} ?> />
	  <label for='note_autre_que_referentiel'> 
		Notes only on the default reference 
	  </label>
	</fieldset>

	<h2>
	  Evaluation by competence
	</h2>
	<p>
	  Use of an external software for the evaluation by competence (beta)
	  <input type="checkbox" 
			 name="utiliser_sacoche" 
			 size="8"
			 title="utiliser_sacoche"
			 <?php if (getSettingValue("utiliser_sacoche") == 'yes') {echo 'checked="checked"';} ?> />
	 <br/>
	 <?php if (getSettingValue("utiliser_sacoche") == 'yes') {
	 	echo '<a href="'.getSettingValue("sacocheUrl").'?id='.getSettingValue('sacoche_base').'">Access to administration of the Evaluation by competence</a>';
	 } ?>
	 <br/>
	 <label for='sacocheUrl' style='cursor: pointer;'>Address of the service of evaluation per competence if possible in https (exemple : https://localhost/panier) </label>
	 <input type='text' size='60' name='sacocheUrl' value='<?php echo(getSettingValue("sacocheUrl")); ?>' id='sacocheUrl' /><br/>
	 <label for='sacoche_base' style='cursor: pointer;'>Number of technical of «base» (to leave vacuum if your instalation of the software of evaluation per competence is mono school)</label>
	 <input type='text' size='5' name='sacoche_base' value='<?php echo(getSettingValue("sacoche_base")); ?>' id='sacoche_base' /><br/>
	</p>
	
	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Save" />
	</p>

</form>






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


