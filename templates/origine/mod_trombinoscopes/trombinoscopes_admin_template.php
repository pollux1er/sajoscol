<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/**
 * $Id: trombinoscopes_admin_template.php 8600 2011-11-05 20:11:36Z jjacquard $
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

	<h2 class="colleHaut">General configuration</h2>
	<p>
	  <em>
		The deactivation of the module trombinoscope does not involve any
suppression of the data.
		When the module is deactivate, there is no access to the module.
	  </em>
	</p>
	<form action="trombinoscopes_admin.php" id="form1" method="post" title="Configuration générale">
	  <fieldset>
	<?php
	echo add_token_field();
	?>
		<legend class="bold">student :</legend>
		<input type="radio"
			   name="activer"
			   id='activer_y'
			   value="y"
			  <?php if (getSettingValue("active_module_trombinoscopes")=='y') echo " checked='checked'"; ?>
			   />
		<label for='activer_y' style='cursor:pointer'>
		  Activate the module trombinoscope
		</label>
		<br/>
		<input type="radio"
			   name="activer"
			   id='activer_n'
			   value="n"
			  <?php if (getSettingValue("active_module_trombinoscopes")!='y') echo " checked='checked'"; ?>
			   />
		<label for='activer_n'
			   style='cursor:pointer'>
		  Deactivate the module trombinoscope
		</label>
		<input type="hidden" name="is_posted" value="1" />
	  </fieldset>
	  
	  <fieldset>
		<legend class="bold">Personnel :</legend>
		<input type="radio"
			   name="activer_personnels"
			   id='activer_personnels_y'
			   value="y"
			  <?php if (getSettingValue("active_module_trombino_pers")=='y') echo " checked='checked'"; ?>
			   />
		<label for='activer_personnels_y' style='cursor:pointer'>
		  Activate the module trombinoscope of the personnel
		</label>
		<br/>
		<input type="radio"
			   name="activer_personnels"
			   id='activer_personnels_n'
			   value="n"
			  <?php if (getSettingValue("active_module_trombino_pers")!='y')echo " checked='checked'"; ?>
			   />
		<label for='activer_personnels_n' style='cursor:pointer'>
		 Deactivate the module trombinoscope of the personnel
		</label>
	  </fieldset>

	  <p class="center">
		<input type="submit"
			   value="Enregistrer"
			   style="font-variant: small-caps;" />
	  </p>

	  <h2>Configuration of display and storage</h2>
	  <p>
		<em>
		  The values below serve to you with the parameter setting as the
maximum values of the widths and heights.
		</em>
	  </p>
	  <fieldset>
		<legend class="bold">For the screen</legend>
		maximum width 
		<input type="text"
			   name="l_max_aff_trombinoscopes"
			   size="3" 
			   maxlength="3" 
			   value="<?php echo getSettingValue("l_max_aff_trombinoscopes"); ?>"
			   title="largeur maxi"
			   />
		maximum height
		<input type="text"
			   name="h_max_aff_trombinoscopes"
			   size="3" 
			   maxlength="3" 
			   value="<?php echo getSettingValue("h_max_aff_trombinoscopes"); ?>"
			   title="hauteur maxi"
			   />
	  </fieldset>
	  
	  <fieldset>
		<legend class="bold">For the impression</legend>
		maximum width
		<input type="text"
			   name="l_max_imp_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   title="largeur maxi"
			   value="<?php echo getSettingValue("l_max_imp_trombinoscopes"); ?>" 
			   />
		maximum height
		<input type="text"
			   name="h_max_imp_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   title="hauteur maxi"
			   value="<?php echo getSettingValue("h_max_imp_trombinoscopes"); ?>" 
			   />
		Number of columns
		<input type="text"
			   name="nb_col_imp_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   value="<?php echo getSettingValue("nb_col_imp_trombinoscopes"); ?>"
			   title="Nombre de colonnes"
			   />
	  </fieldset>

	  <fieldset>
		<legend class="bold">For storage on the waiter</legend>
		width
		<input type="text"
			   name="l_resize_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   title="largeur"
			   value="<?php echo getSettingValue("l_resize_trombinoscopes"); ?>" 
			   />
		height
		<input type="text"
			   name="h_resize_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   title="hauteur"
			   value="<?php echo getSettingValue("h_resize_trombinoscopes"); ?>" 
			   />
	  </fieldset>

	  <p class="center">
		<input type="submit"
			   value="Enregistrer"
			   style="font-variant: small-caps;" />
	  </p>
	  
	  <h2>Configuration of the redimensioning of the photographs</h2>
	  <p>
		<em>
		  The deactivation of the redimensioning of the photographs does not
involve any suppression of the data. 
		  When the system of redimensioning is decontaminated, photographs
transferées on the site 
		  will not be reduced in 
		  <?php echo getSettingValue("l_resize_trombinoscopes");?>x<?php echo getSettingValue("h_resize_trombinoscopes");?>.
		</em>
	  </p>
	  <fieldset>
		<legend class="invisible">Activation</legend>
		<input type="radio" 
			   name="activer_redimensionne" 
			   id="activer_redimensionne_y" 
			   value="y" 
			  <?php if (getSettingValue("active_module_trombinoscopes_rd")=='y') echo " checked='checked'"; ?> 
			   />
		<label for='activer_redimensionne_y' style='cursor:pointer'>
		  Activate the redimensioning of the photographs in 
		  <?php echo getSettingValue("l_resize_trombinoscopes");?>x<?php echo getSettingValue("h_resize_trombinoscopes");?>
		</label>
	  <br/>
		<strong>Notice</strong> caution GD must be active on the waiter of GEPI to use
redimensioning.
	  <br/>
		<input type="radio" 
			   name="activer_redimensionne" 
			   id="activer_redimensionne_n" 
			   value="n" 
			  <?php if (getSettingValue("active_module_trombinoscopes_rd")=='n') echo " checked='checked'"; ?> 
			   />
		<label for='activer_redimensionne_n' style='cursor:pointer'>
		  Deactivation the redimensioning of the photographs
		</label>
	  </fieldset>

	  <fieldset>
		<legend class="bold">Rotation of the image :</legend>
		<input name="activer_rotation"
			   value=""
			   type="radio"
			   title="Tourner de 0°"
			  <?php if (getSettingValue("active_module_trombinoscopes_rt")=='') echo "checked='checked'"; ?>
			   />
		0°
		<input name="activer_rotation"
			   value="90"
			   type="radio"
			   title="Tourner de 90°"
			  <?php if (getSettingValue("active_module_trombinoscopes_rt")=='90') echo "checked='checked'"; ?>
			   />
		90°
		<input name="activer_rotation"
			   value="180"
			   type="radio"
			   title="Tourner de 180°"
			  <?php if (getSettingValue("active_module_trombinoscopes_rt")=='180') echo "checked='checked'"; ?>
			  />
		180°
		<input name="activer_rotation"
			   value="270"
			   type="radio"
			   title="Tourner de 270°"
			  <?php if (getSettingValue("active_module_trombinoscopes_rt")=='270') echo "checked='checked'"; ?>
			   />
		270°
		Select a value if you wish a rotation of the original photograph
	  </fieldset>

	  <p class="center">
		<input type="submit"
			   value="Enregistrer"
			   style="font-variant: small-caps;" />
	  </p>

	  <h2>Management of the access of the student</h2>
	  <p>
		In the page "Gestion générale"-&gt;"Droits d'accès", you have the possibility of giving to
		<strong>all student</strong> right to send/Modify itself its photograph in the interface
		"Gérer mon compte".
	  </p>
	  <p>
		<strong>If this option is activated</strong>, you can, below, to manage more finely which student have the right to send/Modify their photograph.
	  </p>
	  <p class="bold">
		Procedure :
	  </p>
	  <ul id="expli_AID" class="colleHaut">
		<li>Create one "catégorie d'AID" having for example for heading "trombinoscope".</li>
		<li>
		  Configure the display of this category of AID so that :
		  <ul>
			<li>The AID did not appear in the official bulletin,</li>
			<li>The AID did not appear in the simplified bulletin.</li>
			<li>The other parameters do not have importance.</li>
		  </ul>
		</li>
		<li>In "List aid of the category", add one or more AIDs.</li>
		<li>
		  Below, to select in the list of the categories of AIDs, that bearing
the name which you have
		  given above. <em>(this list does not appear if you did not give the possibility to all
the pupils of sending/Modify their photograph in "General management"-&gt;"Access Rights")</em>.
		</li>
		<li>
		  All the students registered in one of AIDs of the above-named category will be able then
		  to send/modify their photog (<em>except for the students without Sconet or "elenoet" number </em>).
		</li>
	  </ul>

<?php

if (!isset($aid_trouve)) {
?>
	  <p>
		<strong>
		  You must create IDA to be able to limit the access of the students to the trombinoscope
		</strong>
	  </p>

<?php
} else {
?>
	  <p>
		<strong>
		  Name of the category of IDA allowing to manage the access of the students :
		</strong>
		<select name="num_aid_trombinoscopes" size="1" title="Choose an IDA">
		  <option value="">
			(none)
		  </option>
<?php
  foreach ($aid_trouve as $aid_disponible){
?>
		  <option value="<?php echo $aid_disponible["indice"] ;?>"<?php if ($aid_disponible["selected"]){ ?> selected="selected"<?php ;} ?> >
			<?php echo $aid_disponible["nom"] ;?>
		  </option>
<?php
  }
  unset ($aid_disponible)
?>
		</select>
	  </p>
	  <p>
		<strong>Notice :</strong> If "none" IDA is defined, <strong>all students</strong> can
		 send/ modify their photo  (<em>except those without elenoet</em>).
	  </p>
	  <p class="center">
		<input type="hidden" 
			   name="is_posted" 
			   value="1" />
		<input type="submit" 
			   value="Save"
			   style="font-variant: small-caps;" />
	  </p>
<?php
}
?>
	</form>

	<h2>Management of the files</h2>
<?php if(!file_exists('../photos/'.$repertoire.'eleves/') && !file_exists('../photos/'.$repertoire.'eleves/')) {?>
	  <p>
		The folders of photogs do not exist
	  </p>
<?php } else 
{ ?>
	<form action="trombinoscopes_admin.php" id="form2" method="post" title="Management of files">
	<?php
	echo add_token_field();
	?>
	  <fieldset>
		<legend class="bold">
		  <label for="supprime">Suppression</label>
		</legend>
<?php if( file_exists('../photos/'.$repertoire.'personnels/') ) { ?>
		<input type="checkbox"
			   name="sup_pers"
			   id='supprime_personnels'
			   value="oui"
			   />
		<label for="supprime_personnel" id='sup_pers'>
		  Empty the folder photos of the personnel
		</label>
<?php } if( file_exists('../photos/'.$repertoire.'eleves/') ) {  ?>
		<br/>
		<input type="checkbox"
			   name="supp_eleve"
			   id='supprime_eleves'
			   value="oui" />
		<label for="supprime_eleve" id='sup_ele'>
		 Empty the folder photos of the students
		</label>
		<br/><em>A file of backup will be created,  recover it and remove it in the module of management of the backups.</em>
	  </fieldset>
<?php } ?>
	  <p class="center">
		<input type="submit" 
			   value="Empty the folders"
			   style="font-variant: small-caps;" />
	  </p>
	 </form>


	<form action="trombinoscopes_admin.php" id="form3" method="post" title="Management of the files">
	<?php
	echo add_token_field();
	?>
	<fieldset>
		<legend class="bold">
		  Management
		</legend>
<?php if( file_exists('../photos/'.$repertoire.'personnels/') ) { ?>
		<input type="checkbox"
			   name="voirPerso"
			   id='voir_personnel'
			   value="yes" />
		<label for="voir_personnel">
		List the professors without photos
		</label>
  <?php } if( file_exists('../photos/'.$repertoire.'eleves/') ) {?>
		<br/>
		<input type="checkbox"
			   name="voirEleve"
			   id='voir_eleve'
			   value="yes" />
		<label for="voir_eleve">
		  List the students without photos
		</label>
  <?php } ?>
	  </fieldset>
	  <p class="center">
		<input type="submit" 
			   value="Lister"
			   style="font-variant: small-caps;" />
	  </p>
	</form>

	<?php if( file_exists('../photos/'.$repertoire.'eleves/') ) {?>
	<form action="trombinoscopes_admin.php" id="form4" method="post" title="Purge the photo">
	<?php
	echo add_token_field();
	?>
	<p>
	<fieldset>
		<legend class="bold">Purge the folder of photos</legend>
		Remove the photos of the students and professors who are not referred any more in the base.<br/>
		<input type="hidden" name="purge_dossier_photos" value="oui">
		<input type="checkbox"
			   name="cpts_inactifs"
			   id='cpts_inactifs'
			   value="oui" />
		<label for="purge_dossier_photos">
		  Also erase the photographs of the students and professors whose account is deactivated.
		</label>
		<br/><em>A file of backup will be created,  recovering it and remove it in the module of management of the backups.</em>
 	  </fieldset>
	  <p class="center">
		<input type="submit" 
			   value="Purge"
			   style="font-variant: small-caps;" />
	</p>
	</form>
 <?php 
 } ?>
 
	<h2>Download the photos</h2>
	<form method="post" action="trombinoscopes_admin.php" id="formEnvoi1" enctype="multipart/form-data">
	<fieldset>
	<?php
	echo add_token_field();
	?>
		<legend class="bold">
			Download the photographs of the students from a ZIP file 
		</legend>
		<input type="hidden" name="action" value="upload_photos_eleves" />
		<input type="file" name="nom_du_fichier" title="Name of the file to downloaded"/>
		<input type="submit" value="Download"/>
		<br/>
		<input type="checkbox"
			   name="ecraser"
			   id='ecrase_photo'
			   value="yes" />
		<label for="ecrase_photo">
		  Crush the photographs if the names correspond
		</label>
		<p>
		  <em>
			If notched, the photos already present will be replaced by the news.
			If not, the old photos will be preserved
		  </em>
		</p>

		<p>ZIP File must contain :<br/>
		<span style="margin-left: 40px;">
		- Whether photographs encoded in format JPEG named according to the ELENOET of the students (<em>ELENOET.jpg</em>) or for, a multisite GEPI, login of the students (<em>login.jpg</em>) ;<br/>
		</span><span style="margin-left: 40px;">
		- or photographs encoded in format JPEG and a file in <a href="http://fr.wikipedia.org/wiki/Comma-separated_values" target="_blank">format CSV</a>, <b>imperatively named <em>correspondances.csv</em></b>, establishing the correspondences between (first field) names of the files photographs and (second field) ELENOET of the students or, for a multisite GEPI, the login of the students (<a href="correspondances.csv" target="_blank">example of file correspondances.csv</a>) ; to generate the file correspondances.csv you can <a href="trombinoscopes_admin.php?liste_eleves=oui<?php echo add_token_in_url(); ?>"> recover the list</a> of students with first name, name, eleonet and login.  <br/>
		</span>
		</p>

		<p>The <b>maximum size</b> of a file downloaded towards the server is of <b><?php echo ini_get('upload_max_filesize');?>.</b><br/>Make if necessary your download in several Zip files.</p>

	  </fieldset>
	</form>

	<br/>

	<form method="post" action="trombinoscopes_admin.php" id="formEnvoi2" enctype="multipart/form-data">
	<fieldset>
	<?php
	echo add_token_field();
	?>
		<legend class="bold">
			Restore the photosfrom a backup (or of a ZIP file )
		</legend>
		<input type="hidden" name="action" value="upload" />
		<input type="file" name="nom_du_fichier" title="Name of the file to downloaded"/>
		<input type="submit" value="Restore"/>
		<br/>
		<input type="checkbox"
			   name="ecraser"
			   id='ecrase_photo'
			   value="yes" />
		<label for="ecrase_photo">
		  Crush the photographs if the names correspond
		</label>
		<p>
		  <em>
			If notched, the photos already present will be replaced by the news.
			If not, the old photos will be preserved
		  </em>
		</p>

		<p>ZIP File must contain a tree structure <b>photos/Students</b> and/or <b>photos/personnel</b> containing respectively the photographs of the students and personnel encoded in format JPEG. The photos of the students must be named according to the ELENOET of the students (<em>ELENOET.jpg</em>) or, for a multisite GEPI, according to the login of the students (<em>login.jpg</em>).

		<p>The <b>maximum size</b> of a file downloaded towards the server is  <b><?php echo ini_get('upload_max_filesize');?>.</b><br/>Make if necessary your download in several files ZIP.</p>

	  </fieldset>
	</form>

	<hr />

  <?php }
  if (isset ($eleves_sans_photo)){
  ?>
	<table class="boireaus">
	  <caption>Students without photos</caption>
	  <tr>
		<th>Name</th>
		<th>First name</th>
	  </tr>
  <?php
		$lig="lig1";
	foreach ($eleves_sans_photo as $pas_photo){
	  if ($lig=="lig1"){
		$lig="lig-1";
	  } else{
		$lig="lig1";
	  }
  ?>
	  <tr class="<?php echo $lig ;?>" >
		<td><?php echo $pas_photo->nom ;?></td>
		<td><?php echo $pas_photo->prenom ;?></td>
	  </tr>
  <?php
	}
	unset($pas_photo);
  ?>
	</table>
  <?php
  }

  if (isset ($personnel_sans_photo)){
  ?>
	<table class="boireaus">
	  <caption>Professors without photos</caption>
	  <tr>
		<th>Name</th>
		<th>First name</th>
	  </tr>
  <?php 
		$lig="lig1";
	foreach ($personnel_sans_photo as $pas_photo){
	  if ($lig=="lig1"){
		$lig="lig-1";
	  } else{
		$lig="lig1";
	  }
  ?>
	  <tr class="<?php echo $lig ;?> white_hover" >
		<td><?php echo $pas_photo->nom ;?></td>
		<td><?php echo casse_mot($pas_photo->prenom,"majf2") ;?></td>
	  </tr>
  <?php 
	}
	unset($pas_photo);
  ?>
	</table>
  <?php 
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
   <p class='microtime'>Page générée en ";
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


