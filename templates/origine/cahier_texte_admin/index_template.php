<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id: index_template.php 8045 2011-08-30 07:54:40Z crob $
 *
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

<?php
	if(isset($_GET['ajout_index_documents'])) {
		echo ajout_index_sous_dossiers("../documents");

		$sql="SELECT * FROM infos_actions WHERE titre='Contrôle des index dans les documents des CDT requis';";
		$res_test=mysql_query($sql);
		if(mysql_num_rows($res_test)>0) {
			while($lig_ia=mysql_fetch_object($res_test)) {
				$sql="DELETE FROM infos_actions_destinataires WHERE id_info='$lig_ia->id';";
				$del=mysql_query($sql);
				if($del) {
					$sql="DELETE FROM infos_actions WHERE id='$lig_ia->id';";
					$del=mysql_query($sql);
				}
			}
		}

	}
?>

	<form action="index.php" id="form1" method="post">
	  <p class="center">
<?php
echo add_token_field();
?>
		<input type="submit" value="Enregistrer" />
	  </p>
	<h2>Activation of the textbooks</h2>
	  <p class="italic">
		  The desactivation of the textbooks does not involve any suppression of the data.
		  When the module is deactivated, the professors do not have access to the module and the public consultation
		   of the textbooks is impossible.
	  </p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activation</legend>

		<input type="radio"
				 name="activer"
				 id="activer_y"
				 value="y"
			 onchange='changement();'
				<?php if (getSettingValue("active_cahiers_texte")=='y') echo " checked='checked'"; ?> />
		<label for='activer_y' style='cursor: pointer;'>
		  Activate the textbooks (consultation and edition)
		</label>
	  <br />
		<input type="radio" 
				 name="activer" 
				 id="activer_n" 
				 value="n"
			 onchange='changement();'
				<?php if (getSettingValue("active_cahiers_texte")=='n') echo " checked='checked'"; ?> />
		<label for='activer_n' style='cursor: pointer;'>
		  Deactivate textbooks (consultation and edition)
		</label>
	  </fieldset>
	  
	  
	  <h2>Version of textbooks</h2>
<?php $extensions = get_loaded_extensions();
  if(!in_array('pdo_mysql',$extensions)) {
?>
	  <p>
		<span style='color:red'>
		  ATTENTION
		</span>
	  It seems that the extension php 'pdo_mysql' is not present.
	  <br />
	  That is likely to make impossible the use of version 2 of the textbook";
	  </p>
<?php
  }
  ?>
	  <p class="italic">
		Version 2 of the textbook necessite php 5.2.x minimum
	  </p>
	  <fieldset class="no_bordure">
		<legend class="invisible">Version</legend>
		<input type="radio"
				 name="version"
				 id="version_1"
				 value="1"
			 onchange='changement();'
				<?php if (getSettingValue("GepiCahierTexteVersion")=='1') echo " checked='checked'"; ?> />
		<label for='version_1' style='cursor: pointer;'>
		  Textbook version 1
		</label>
		(<span class="italic">
		  the textbook version 1 will not be supported any more in the future version 1.5.3
		</span>)
		<br />
		  <input type="radio"
				 name="version"
				 id="version_2"
				 value="2"
			 onchange='changement();'
				<?php if (getSettingValue("GepiCahierTexteVersion")=='2') echo " checked='checked'"; ?> />
		<label for='version_2' style='cursor: pointer;'>
		  Textbook version 2
		</label>
	  </fieldset>
	  
	  <h2>Beginning and end of the textbooks</h2>
	  <p class="italic">
		Only the rubrics whose date lies between the date of beginning and the completion date of the textbook are visible in the interface of public consultation.
		<br />
		The edition (modification/suppression/addition) of textbooks by the users of GEPI is not affected by these dates.
	  </p>
	  <fieldset class="no_bordure">
		<legend class="invisible">Version</legend>
        Return to the beginning of the textbooks:
<?php
        $bday = strftime("%d", getSettingValue("begin_bookings"));
        $bmonth = strftime("%m", getSettingValue("begin_bookings"));
        $byear = strftime("%Y", getSettingValue("begin_bookings"));
        genDateSelector("begin_", $bday, $bmonth, $byear,"more_years")
?>
	  <br />
        Completion date of the textbooks :
<?php
        $eday = strftime("%d", getSettingValue("end_bookings"));
        $emonth = strftime("%m", getSettingValue("end_bookings"));
        $eyear= strftime("%Y", getSettingValue("end_bookings"));
        genDateSelector("end_",$eday,$emonth,$eyear,"more_years")
?>
		<input type="hidden" name="is_posted" value="1" />
	  </fieldset>

	  <h2>Public access</h2>
	  <fieldset class="no_bordure">
		<legend class="invisible">public access</legend>
		  <input type='radio' 
				 name='cahier_texte_acces_public' 
				 id='cahier_texte_acces_public_n' 
				 value='no'
			 onchange='changement();'
				<?php if (getSettingValue("cahier_texte_acces_public") == "no") echo " checked='checked'";?> /> 
		<label for='cahier_texte_acces_public_n' style='cursor: pointer;'>
		  Deactivate public consultation of the textbooks 
		  (only logued users will be able to have access in consultation, if they are authorized there)
		</label>
	  <br />
		  <input type='radio' 
				 name='cahier_texte_acces_public' 
				 id='cahier_texte_acces_public_y' 
				 value='yes'
			 onchange='changement();'
				<?php if (getSettingValue("cahier_texte_acces_public") == "yes") echo " checked='checked'";?> /> 
		<label for='cahier_texte_acces_public_y' style='cursor: pointer;'>
		  Activate the public consultation of the textbooks 
		  (all textbooks directly visible , or by the typing of a login/mdp global)
		</label>
	  </fieldset>
	  <p>
		-&gt; Access to the <a href='../public/index.php?id_classe=-1'>public interface of consultation of the textbooks</a>
	  </p>
	  <p class="italic">
		In the absence of identifier and password, the access to the public interface of consultation of textbooks is completely free.
	  </p>
	  <p>
		Identifier :
		<input type="text" 
			   name="cahiers_texte_login_pub"
			 onchange='changement();'
			 title="Identifier"
			   value="<?php echo getSettingValue("cahiers_texte_login_pub"); ?>" 
			   size="20" />
	  </p>
	  <p>
		Password :
		<input type="text" 
			   name="cahiers_texte_passwd_pub"
			 onchange='changement();'
			 title="Password"
			   value="<?php echo getSettingValue("cahiers_texte_passwd_pub"); ?>" 
			   size="20" />
	  </p>

	  <h2>Time of visualization of the exams</h2>
	  <p class="italic">
		Indicate here the time in days during which the exams will be
visible, from the day of visualization selected, in the public interface of consulation of the textbooks.
		<br />
		Put value 0 if you do not wish to activate the module of filling of
the exams.
		In this case, the professors make appear the exams to be made
in the same box as the contents of the meetings.
	  </p>
	  <p>
		Delay :
		<input type="text"
			   name="delai_devoirs"
			 onchange='changement();'
			 title="Delay of the exams"
			   value="<?php echo getSettingValue("delai_devoirs"); ?>"
			   size="2" />
		days
	  </p>

	  <h2>Visibility of the joined documents</h2>
	  <p>
		<input type="checkbox"
			   name="cdt_possibilite_masquer_pj"
			   id="cdt_possibilite_masquer_pj"
			   onchange='changement();'
			   title="Visibility of the joined documents"
			   value="y"
		       <?php if(getSettingValue("cdt_possibilite_masquer_pj")=="y") {echo " checked";} ?>
			   />
		<label for='cdt_possibilite_masquer_pj'> Possibility for the professors of hiding from the students and responsibles documents joined to the Textbooks.</label>
	  </p>

	  <h2>Signature of textbooks</h2>
	  <fieldset class="no_bordure">
		<legend class="invisible">Visa</legend>
		  <input type='radio'
				 name='visa_cdt_inter_modif_notices_visees'
				 id='visa_cdt_inter_modif_notices_visees_y'
				 value='yes'
			 onchange='changement();'
			   <?php if (getSettingValue("visa_cdt_inter_modif_notices_visees") == "yes") echo " checked='checked'";?> />
		<label for='visa_cdt_inter_modif_notices_visees_y' style='cursor: pointer;'>
		 Activate prohibition for the teachers to modify a notice former to the date fixed during the visa of their textbook.
		</label>
	  <br />
		  <input type='radio'
				 name='visa_cdt_inter_modif_notices_visees'
				 id='visa_cdt_inter_modif_notices_visees_n'
				 value='no'
			 onchange='changement();'
			   <?php if (getSettingValue("visa_cdt_inter_modif_notices_visees") == "no") echo " checked='checked'";?> />
		<label for='visa_cdt_inter_modif_notices_visees_n' style='cursor: pointer;'>
		  Deactivate prohibition for the teachers to modify a notice after
the signature of the textbooks
		</label>
	  </fieldset>


	  <h2>Joint textbooks</h2>
	  <fieldset class="no_bordure">
		<legend class="invisible">Joint textbooks</legend>
			<p>In CDT2, by default, a professor cannot modify a notice/exam carried out by a colleague, even if it is about a shared course (<i>several professors in front of the same group of students</i>).<br />
			To modify this parameter setting&nbsp;:</p>
		  <input type='radio'
				 name='cdt_autoriser_modif_multiprof'
				 id='cdt_autoriser_modif_multiprof_y'
				 value='yes'
			 onchange='changement();'
			   <?php if (getSettingValue("cdt_autoriser_modif_multiprof") == "yes") {echo " checked='checked'";}?> />
		<label for='cdt_autoriser_modif_multiprof_y' style='cursor: pointer;'>
		  Authorize the colleagues working in binomial on a course to modify the notices/exams created by their colleague.
		</label>
	  <br />
		  <input type='radio'
				 name='cdt_autoriser_modif_multiprof'
				 id='cdt_autoriser_modif_multiprof_n'
				 value='no'
			 onchange='changement();'
			   <?php if ((getSettingValue("cdt_autoriser_modif_multiprof") == "no")||(getSettingValue("cdt_autoriser_modif_multiprof") == "")) {echo " checked='checked'";}?> />
		<label for='cdt_autoriser_modif_multiprof_n' style='cursor: pointer;'>
		  Prohibit the modification of notice/exam created by their colleague.
		</label>
	  </fieldset>


	  <p class="center">
		<input type="submit" value="Save" />
	  </p>
	</form>

	<hr />
	
	<h2>Management of the textbooks</h2>
	<ul>
	  <li><a href='modify_limites.php'>Maximum disk space, maximum size of a file</a></li>
	  <li><a href='modify_type_doc.php'>Types of files authorized in download</a></li>
	  <li><a href='admin_ct.php'>Administration of the textbooks</a> (seek inconsistencies, modifications, suppressions)</li>
	  <li><a href='visa_ct.php'>Sign textbooks</a> (Sign the textbooks)</li>
	  <li><a href='index.php?ajout_index_documents=y'>Protect under-folder 'documents/' against abnormal accesses</a></li>
	  <li><a href='../cahier_texte_2/archivage_cdt.php'>Filing of the textbooks at the end of the year school</a></li>
	  <li><a href='../cahier_texte_2/export_cdt.php'>Export of textbooks and access inspector (<i>without authentification</i>)</a></li>
	</ul>
	
	<hr />
	
	<h2>Astuce</h2>
	<p>
	  If you want to use only the textbook module in Gepi, consult the following page&nbsp;:
	  <br />
	  <a href='http://www.sylogix.org/projects/gepi/wiki/Use_only_cdt'>
		http://www.sylogix.org/projects/gepi/wiki/Use_only_cdt
	  </a>
	</p>

	<hr />

	<h2>Recall of the B.O.</h2>

	<?php
		require("../lib/textes.inc.php");
		echo $cdt_texte_bo;
	?>

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


