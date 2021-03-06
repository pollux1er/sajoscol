<?php
/**
 * Ent�te de la page
 * 
 * $Id: header.inc 7797 2011-08-16 23:34:45Z regis $
 *
 * @copyright Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Patrick Duthilleul
 * 
 * @license GNU/GPL v2
 * @package General
 * @subpackage Affichage
 * @see get_enfants_from_resp_login()
 * @see get_noms_classes_from_ele_login()
 * @see getPref()
 * @see getSettingValue()
 * @see insert_confirm_abandon()
 * @see last_connection()
 * @todo ne pourrait-on pas utiliser $gepiPath plut�t que construire $prefix ?
 */

/* This file is part of GEPI.
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

/**
 * Renvoie la derni�re connexion de l'utilisateur
 * @global string
 * @return string la derni�re connexion
 */
function last_connection() {
	global $gepiPath;
   $sql = "select START, AUTOCLOSE, REMOTE_ADDR from log where LOGIN = '".$_SESSION['login']."' and SESSION_ID != '".session_id()."' order by START desc";
   $res = sql_query($sql);
   $r = '';
   if ($res) {
      $row = sql_row($res, 0);
      $annee_b = substr($row[0],0,4);
      $mois_b =  substr($row[0],5,2);
      $jour_b =  substr($row[0],8,2);
      $heures_b = substr($row[0],11,2);
      $minutes_b = substr($row[0],14,2);
      $secondes_b = substr($row[0],17,2);
      if ($row[0]  != '') {
          if ($row[1]  == "4") {
              $r = "<span style=\"color: red\"><strong>Tentative de connexion le ".$jour_b."/".$mois_b."/".$annee_b." � ".$heures_b." h ".$minutes_b. " avec un mot de passe erron�</strong></span> (<a href='".$gepiPath."/utilisateurs/mon_compte.php#connexion'".insert_confirm_abandon().">journal des connexions</a>)";
              // On compte le nombre de tentatives infructueuses successives
              $nb_tentative = 0;
              $flag = 0;
              for ($i = 0; (($row_b = sql_row($res, $i)) and ($flag < 1)); $i++)
              {
				if (($row_b[1]  == "2") and ($row_b[2]  == $row[2])) {
					$nb_tentative++;
				}
				else {
					$flag = 1;
				}
              }
              if ($nb_tentative > 1) {$r .= "<br /><strong>Nombre de tentatives de connexion successives : ".$nb_tentative.".</strong></font>";}
          } else {
              $r = "  Last session opened the ".$jour_b."/".$mois_b."/".$annee_b." at ".$heures_b." h ".$minutes_b. " (<a href='".$gepiPath."/utilisateurs/mon_compte.php#connexion'".insert_confirm_abandon().">journal des connexions</a>)";
			}
      }
    }
    return $r;
}


$sessionMaxLength=24;
$session_gc_maxlifetime=ini_get("session.gc_maxlifetime");
$sessionMaxLength=getSettingValue("sessionMaxLength");
if(($sessionMaxLength!="")&&(preg_match("/^[0-9]*$/",$sessionMaxLength))) {
	$sessionMaxLength=$sessionMaxLength*60;
	if(($session_gc_maxlifetime!="")&&(preg_match("/^[0-9]*$/",$session_gc_maxlifetime))&&($session_gc_maxlifetime<$sessionMaxLength)) {
		$sessionMaxLength=$session_gc_maxlifetime;
	}
}
elseif(($session_gc_maxlifetime!="")&&(preg_match("/^[0-9]*$/",$session_gc_maxlifetime))) {
	$sessionMaxLength=$session_gc_maxlifetime;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />

<title><?php echo getSettingValue("gepiSchoolName"); ?> : base de donn�es �l�ves</title>

<script type="text/javascript">
//<![CDATA[ 
<?php if (isset($affiche_message) and ($affiche_message == 'yes')) { ?>
alert("<?php echo $message_enregistrement; ?>");
<?php } ?>
function changement() {
change = 'yes';
}
//]]>
</script>

<script type="text/javascript">
//<![CDATA[ 
    function ouvre_popup(url) {
        eval("window.open('<?php echo($gepiPath); ?>/mod_miseajour/utilisateur/fenetre.php','fen','width=600,height=500,menubar=no,scrollbars=yes')");
        fen.focus();
    }
//]]>
</script>

<?php

// ====================== Affichage du favicon ========================
echo '
<link rel="SHORTCUT ICON" href="'.$gepiPath.'/favicon.ico" />
	'."\n";
// ====================== Fin affichage favicon =======================

// ====================== Affichage des javascripts ===================
echo '	<!-- D�but des fichiers en javascript -->'."\n";
echo '<script type="text/javascript" src="'.$gepiPath.'/lib/functions.js"></script>'."\n";

// Ajout du framework prototype 1.6.0.3
echo '<script type="text/javascript" src="'.$gepiPath.'/lib/prototype.js"></script>'."\n";

// Ajout de la librairie Scriptaculous.js conditionn� � la variable $utilisation_scriptaculous="ok"
$scriptaculous = isset($utilisation_scriptaculous) ? $utilisation_scriptaculous : NULL;
$script_effet = isset($scriptaculous_effet) ? $scriptaculous_effet : NULL;
if ($scriptaculous == "ok") {
  // On affiche le lien qui charge scriptaculous
  echo '<script type="text/javascript" src="'.$gepiPath.'/lib/scriptaculous.js?load='.$script_effet.'"></script>'."\n";
}

// Utilisation de dojo
if (isset($dojo)) {
    echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.6.1/dojo/dojo.xd.js" djConfig="parseOnLoad: true"></script>'."\n";
    echo '<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.6.1/dijit/themes/claro/claro.css" />';
}

// Utilisation de windows.js
$windows = isset($utilisation_win) ? $utilisation_win : NULL;
if ($windows == 'oui') {
 	echo '
	<script type="text/javascript" src="'.$gepiPath.'/edt_effets/javascripts/effects.js"> </script>
	<script type="text/javascript" src="'.$gepiPath.'/edt_effets/javascripts/window.js"> </script>
	<script type="text/javascript" src="'.$gepiPath.'/edt_effets/javascripts/window_effects.js"> </script>
	<link href="'.$gepiPath.'/edt_effets/themes/default.css" rel="stylesheet" type="text/css"/>
	<link href="'.$gepiPath.'/edt_effets/themes/alphacube.css" rel="stylesheet" type="text/css"/>
	 ';
 }
// Utilisation de tablekit
$tablekit = isset($utilisation_tablekit) ? $utilisation_tablekit : NULL;
if ($tablekit == "ok") {
  // On affiche le lien qui charge tablekit
  echo '<script type="text/javascript" src="'.$gepiPath.'/lib/tablekit.js"></script>'."\n";
}
// js sp�cifique
if(isset($javascript_specifique)) {
	// Il faudrait filtrer le contenu de la variable...
	// On ajoute le ".js" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence

	if(is_array($javascript_specifique)) {
		foreach($javascript_specifique as $current_javascript_specifique) {
			if(strlen(my_ereg_replace("[A-Za-z0-9_/\-]","",$current_javascript_specifique))==0) {
				// Javascript sp�cifique � une page:
				echo "<script type='text/javascript' src='$gepiPath/$current_javascript_specifique.js'></script>\n";
			}
		}
	}
	else {
		if(strlen(my_ereg_replace("[A-Za-z0-9_/\-]","",$javascript_specifique))==0) {
			// Javascript sp�cifique � une page:
			echo "<script type='text/javascript' src='$gepiPath/$javascript_specifique.js'></script>\n";
		}
	}
}
// On affiche tout le temps brainjar sauf quand on dit � Gepi de ne pas le faire
$utilisation_jsdivdrag = isset($utilisation_jsdivdrag) ? $utilisation_jsdivdrag : NULL;
if (isset($utilisation_jsdivdrag) AND $utilisation_jsdivdrag == "non") {
	echo "<!-- Pas de brainjar-->\n";
} else {
	//===================================
	// Pour a�rer les infobulles si jamais Javascript n'est pas actif.
	// Sinon, avec le position:absolute, les div se superposent.
	$posDiv_infobulle=0;
	// $posDiv_infobulle permet de fixer la position horizontale initiale du Div.



	// Choix de l'unit� pour les dimensions des DIV: em, px,...
	$unite_div_infobulle="em";
	// Pour l'overflow dans les DIV d'aide, il vaut mieux laisser 'em'.

	// Variable pass�e � 'ok' en fin de page via le /lib/footer.inc.php
	echo "<script type='text/javascript'>
		var temporisation_chargement='n';
	</script>\n";

	echo "<script type='text/javascript' src='$gepiPath/lib/brainjar_drag.js'></script>\n";
	echo "<script type='text/javascript' src='$gepiPath/lib/position.js'></script>\n";


} // if(isset($utilisation_jsdivdrag...

?>

<script type="text/javascript" src="<?php echo $gepiPath; ?>/lib/cookieClass.js"></script>
<script type="text/javascript">
//<![CDATA[ 
debut_alert=new Date()
cookie_modified = false;
warn_msg1_already_displayed = false;
warn_msg2_already_displayed = false;
gepi_start_session = new Cookies();
if (gepi_start_session.get('GEPI_start_session')) {
	gepi_start_session.clear('GEPI_start_session');
}
gepi_start_session.set('GEPI_start_session', debut_alert.getTime())


function show_message_deconnexion() {

			/* =================================================
			 =
			 =
			 =
			 =================================================== */
			function display_alert(message) {
				if ($('alert_message')) {
					$('alert_message').update(message);

					if (Prototype.Browser.IE) {
						//document.documentElement.scroll = "no";
						//document.documentElement.style.overflow = 'hidden';
					}
					else {
						//document.body.scroll = "no";
						//document.body.style.overflow = 'hidden';				
					}					
					var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
					var width = viewport.width; // Usable window width
					var height = viewport.height; // Usable window height
					if( typeof( window.pageYOffset ) == 'number' ) 
						{y = window.pageYOffset;}
					else if (typeof(document.documentElement.scrollTop) == 'number') {
						y=document.documentElement.scrollTop;
					}
					//$('alert_cache').setStyle({width: "100%"});
					//$('alert_cache').setStyle({height: height+"px"});
					//$('alert_cache').setStyle({top: y+"px"});
					//$('alert_cache').setStyle({display: 'block'});
					//$('alert_cache').setOpacity(0.5);
					play_footer_sound();
					$('alert_entete').setStyle({top: y+2+"px"});
					$('alert_entete').setStyle({left: Math.abs((width-640)/2)+"px"});
					$('alert_entete').setOpacity(1);
					$('alert_entete').setStyle({display: 'block'});
					$('alert_popup').setStyle({top: y+50+"px"});
					$('alert_popup').setStyle({left: Math.abs((width-640)/2)+"px"});
					$('alert_popup').setOpacity(1);
					$('alert_popup').setStyle({display: 'block'});
					$('alert_bouton_ok').observe('click', function(event) {
						$('alert_popup').setStyle({display: 'none'});	
						$('alert_cache').setStyle({display: 'none'});
						$('alert_entete').setStyle({display: 'none'});
						if (Prototype.Browser.IE) {
							//document.documentElement.scroll = "yes";
							//document.documentElement.style.overflow = 'scroll';
						}
						else {
							//document.body.scroll = "yes";
							//document.body.style.overflow = 'scroll';				
						}						
					
					});
					//$('alert_bouton_reload').observe('click', function(event) {
					//	location.reload(true); 				
					//
					//});	
				}
				else {
					alert(message);				
				
				}
					
			
			}
	var seconds_before_alert = 180;
	var seconds_int_betweenn_2_msg = 30;
	if (gepi_start_session.get('GEPI_start_session')) {
		debut_alert.setTime(parseInt(gepi_start_session.get('GEPI_start_session'),10));
	}
  var digital=new Date()
  var seconds=(digital-debut_alert)/1000
  //if (1==1) {
  if (seconds>=<?php echo $sessionMaxLength; ?>) {
	if (!warn_msg2_already_displayed) {
		var message = "vous avez �t� probablement d�connect� du serveur, votre travail ne pourra pas �tre enregistr� dans gepi depuis cette page, merci de le sauvegarder dans un bloc note.";
		display_alert(message); 
		warn_msg2_already_displayed = true;
	}
  
  }
	//if (1==1) {
  else if (seconds><?php echo $sessionMaxLength; ?> - seconds_before_alert) {
  
  	if (!warn_msg1_already_displayed) {
		var seconds_reste = Math.floor(<?php echo $sessionMaxLength; ?> - seconds);
		now=new Date()
		var hrs=now.getHours();
		var mins=now.getMinutes();
		var secs=now.getSeconds();

		var heure = hrs + " H " + mins + "' " + secs + "'' ";
		var message ="A "+ heure + ", il vous reste moins de 3 minutes avant d'�tre d�connect� ! \nPour �viter cela, rechargez cette page en ayant pris soin d'enregistrer votre travail !";
		display_alert(message);
		warn_msg1_already_displayed = true;
	}
	
  }
  setTimeout("show_message_deconnexion()",seconds_int_betweenn_2_msg*1000)
}
//]]>
</script>



<?php

echo "\n".'	<!-- fin des fichiers en javascript -->'."\n\n";
// ======================= fin de l'affichage des javascripts ========================
// ======================= D�but de l'affichage des feuilles de style ================
echo '	<!-- D�but des styles -->'."\n";
$style = getSettingValue("gepi_stylesheet");
if (empty($style)) $style = "style";

echo '<link rel="stylesheet" type="text/css" href="'.$gepiPath.'/'.$style.'.css" />'."\n";
// Couleur de fond des pages
if (!isset($titre_page)) $bgcouleur = "bgcolor= \"#FFFFFF\""; else $bgcouleur = "";


if(isset($style_specifique)) {
	// Il faudrait filtrer le contenu de la variable...
	// ne doit contenir que certains types de caract�res et se terminer par .css
	// Non... on ajoute le ".css" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence

	if(is_array($style_specifique)) {
		foreach($style_specifique as $current_style_specifique) {
			if(strlen(my_ereg_replace("[A-Za-z0-9_/]","",$current_style_specifique))==0) {
				// Styles sp�cifiques � une page:
				echo "<link rel='stylesheet' type='text/css' href='$gepiPath/$current_style_specifique.css' />\n";
			}
		}
	}
	else {
		if(strlen(my_ereg_replace("[A-Za-z0-9_/]","",$style_specifique))==0) {
			// Styles sp�cifiques � une page:
			echo "<link rel='stylesheet' type='text/css' href='$gepiPath/$style_specifique.css' />\n";
		}
	}
}

   // v�rifie si on est dans le modules absences
   $files = array("gestion_absences", "select", "ajout_abs", "ajout_ret", "ajout_dip", "ajout_inf", "tableau", "impression_absences", "prof_ajout_abs", "statistiques", "alert_suivi", "admin_config_semaines", "admin_motifs_absences", "admin_horaire_ouverture", "admin_actions_absences", "admin_periodes_absences");
    if(in_array(basename($_SERVER['PHP_SELF'],".php"), $files)) {
        ?><link rel='stylesheet' type='text/css' href='<?php echo $gepiPath; ?>/mod_absences/styles/mod_absences.css' /><?php
    }

// Voir si on restreint l'usage aux pages du module cahier de textes
// Restreint aux pages modifi�es pour une meilleure accessibilit�

if(isset($accessibilite)) {
	if($accessibilite=="y") {
		echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/accessibilite.css' media='screen' />\n";
		// Feuilles de styles Param�tres de configuration du cahier de textes � partir de /lib/global.inc
		// echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/maj_global.css' media='screen' />\n";
      // Feuilles de styles imprimante
		echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/accessibilite_print.css' media='print' />\n";
   }
}


echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/portable.css' media='handheld' />\n";


// Feuilles de styles du bandeau
echo "\n<link title='bandeau' rel='stylesheet' type='text/css' href='$gepiPath/css/bandeau_r01.css' media='screen' />\n";
echo "<!--[if lte IE 7]>\n";
	echo "	<link title='bandeau' rel='stylesheet' type='text/css' href='$gepiPath/css/bandeau_r01_ie.css' media='screen' />\n";
echo "<![endif]-->\n";
echo "<!--[if lte IE 6]>\n";
	echo "	<link title='bandeau' rel='stylesheet' type='text/css' href='$gepiPath/css/bandeau_r01_ie6.css' media='screen' />\n";
echo "<![endif]-->\n";
echo "<!--[if IE 7]>\n";
	echo "	<link title='bandeau' rel='stylesheet' type='text/css' href='$gepiPath/css/bandeau_r01_ie7.css' media='screen' />\n";
echo "<![endif]-->\n";

if(isset($style_screen_ajout)) {

	// Styles param�trables depuis l'interface:
	if($style_screen_ajout=='y') {
		// La variable $style_screen_ajout se param�tre dans le /lib/global.inc
		// C'est une s�curit�... il suffit de passer la variable � 'n' pour d�sactiver ce fichier CSS et �ventuellement r�tablir un acc�s apr�s avoir impos� une couleur noire sur noire
		// Il faudra d�finir un nom de la forme style_screen_ajout_RNE.css pour le multisite
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			if (@file_exists($gepiPath.'/style_screen_ajout_'.getSettingValue("gepiSchoolRne").'.css')) {
					echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout_".getSettingValue("gepiSchoolRne").".css' />\n";
			}
		}
		else {
			if(isset($niveau_arbo)) {
				if((($niveau_arbo==0)&&(file_exists('./style_screen_ajout.css')))||
				(($niveau_arbo==1)&&(file_exists('../style_screen_ajout.css')))||
				(($niveau_arbo==2)&&(file_exists('../../style_screen_ajout.css')))) {
					echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
				}
			}
			elseif(file_exists('../style_screen_ajout.css')) {
				// La plupart du temps, on ne renseigne pas $niveau_arbo pour le niveau 1
				echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
			}
		}
	}
}

echo '	<!-- Fin des styles -->'."\n\n";
/**
 * R�glage des couleurs
 */
include 'maj_coul_global.inc';

// ============================== FIN des feuilles de style =======================
// *********** sur le onload *************** //
if (isset($use_observeur) AND $use_observeur == 'ok') {
  $charger_observeur = ' observeur();';
}else{
  $charger_observeur = NULL;
}
?>
</head>
<!-- ************************* -->
<!-- D�but du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php echo $charger_observeur; ?>" <?php if (isset($dojo)) {echo 'class=" claro " style="font:11pt Verdana,sans-serif;"';}?> >

<?php
if (getSettingValue("impose_petit_entete_prof") == 'y' AND $_SESSION['statut'] == 'professeur') {
	$_SESSION['cacher_header']="y";
}
// Taille � r�cup�rer dans la base pour initialiser $_SESSION['cacher_header']
	// petit bandeau toute valeur sauf "n" ;
	// grand bandeau "n";
if (isset($titre_page)) {
	if(!isset($_SESSION['cacher_header'])) {
		$_SESSION['cacher_header']="n";
	}

	echo "<!-- Header start -->\n";

/* ===== affichage du bandeau ===== */

	if(getSettingValue('gepi_stylesheet')=='style') {
		// D�termine le fond du bandeau
		if(getSettingValue('utiliser_degrade')=='y') {
			$degrade_entete="degrade1";
		}
		else{
			$degrade_entete="darkfade";
		}
	}
	else{
			$degrade_entete="no_style";
	}

// Initialisation du bandeau � grand bandeau darkfade pour ceux qui n'ont pas de javascript
	echo "<div id='bandeau' class='gd_bandeau darkfade gd_bandeau_darkfade'>\n";

// Initialisation du bandeau � la bonne couleur
	echo "<script type='text/javascript'>
		<!--
			modifier_couleur_bandeau('$degrade_entete');
		// -->
	</script>";
// Initialisation du bandeau � la bonne taille
	$cacher_header = isset($_SESSION['cacher_header']) ? $_SESSION['cacher_header'] : "n";

	if(isset($mode_header_reduit)) {
		$cacher_header="y";
	}

	if($cacher_header=="n") {
		$taille_bandeau_header="g";
	}
	else {
		$taille_bandeau_header="p";
	}

	if($taille_bandeau_header=="p") {
		echo "<script type='text/javascript'>
			<!--
				modifier_taille_bandeau();
			// -->
		</script>";
	}

			echo "<!-- Page title, access rights -->\n";
			echo "<!-- User name, status, main matter, home, logout, account management -->\n";

			// Bandeau de gauche
			
				if(!isset($mode_header_reduit)) {
						// Bouton r�duire le bandeau
						echo "<a class='change_taille_gd' href=\"#\" onclick=\"modifier_taille_bandeau();change_mode_header('y', '".$gepiPath."');return false;\">";
							echo "<img src='$gepiPath/images/up.png' alt='Cacher le bandeau' title='Cacher le bandeau' />";
						echo "</a>";
						// Bouton agrandir le bandeau
						echo "<a class='change_taille_pt' href=\"#\" onclick=\"modifier_taille_bandeau();change_mode_header('n', '".$gepiPath."');return false;\">";
							echo "<img src='$gepiPath/images/down.png' alt='Afficher le bandeau' title='Afficher le bandeau' />";
						echo "</a>";
				}

				if(getSettingValue('afficher_temoin_filtrage_html')=='y') {
					if($filtrage_html=='htmlpurifier') {
						echo " <img src='$gepiPath/images/bulle_verte.png' width='9' height='9' alt='Filtrage HTML avec HTMLPurifier' title='Filtrage HTML avec HTMLPurifier' />\n";
					}
					elseif($filtrage_html=='inputfilter') {
						echo " <img src='$gepiPath/images/bulle_bleue.png' width='9' height='9' alt='Filtrage HTML avec InputFilter' title='Filtrage HTML avec InputFilter' />\n";
					}
					else {
						echo " <img src='$gepiPath/images/bulle_rouge.png' width='9' height='9' alt='Pas de filtrage HTML' title='Pas de filtrage HTML' />\n";
					}
				}

				//=== Titre de la page ===
				echo "<h1>".$titre_page."</h1>\n";
				echo "<!-- Derni�re connexion -->\n";

				//=== Nom Pr�nom utilisateur ===
				echo "<p id='bd_nom'>\n";
					if((!isset($_SESSION['prenom']))||(!isset($_SESSION['nom']))) {
						$sql="SELECT nom, prenom FROM utilisateurs WHERE login='".$_SESSION['login']."';";
						$res_np=mysql_query($sql);
						if(mysql_num_rows($res_np)>0) {
							$lig_np=mysql_fetch_object($res_np);
							$_SESSION['prenom']=$lig_np->prenom;
							$_SESSION['nom']=$lig_np->nom;
						}
					}
					if((isset($_SESSION['prenom']))||(isset($_SESSION['nom']))) {
						echo($_SESSION['prenom'] . " " . $_SESSION['nom'] . "\n");
					}
					else {
						echo "NOM Prenom";
					}
				echo "</p>\n";

				//=== Derni�re connexion ===
				if (isset($affiche_connexion)) {
					echo "<p id='dern_connect'>".last_connection();
					echo "&nbsp;"; // Pour �viter un <p></p> lors de la premi�re connexion
					echo "</p>\n";
				}

				//=== statut utilisateur ===
				echo "<p id='bd_parent'>\n";
					if ($_SESSION['statut'] == "administrateur") {
						echo "<span class='red'>Administrateur</span>\n";
					}
					elseif ($_SESSION['statut'] == "professeur") {
						$nom_complet_matiere = sql_query1("select nom_complet from matieres
						where matiere = '".$_SESSION['matiere']."'");
						if ($nom_complet_matiere != '-1') {
							echo("Teacher of : " . htmlentities($nom_complet_matiere));
						} else {
							echo "Guess";
						}
					}
					elseif ($_SESSION['statut'] == "scolarite") {
						echo "Bursary";
					}
					elseif ($_SESSION['statut'] == "cpe") {
						echo "CPE";
					}
					elseif ($_SESSION['statut'] == "eleve") {
						$tab_tmp_info_classes=get_noms_classes_from_ele_login($_SESSION['login']);
						echo "Student of ".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1];
					}
					elseif ($_SESSION['statut'] == "responsable") {
						$tab_tmp_ele=get_enfants_from_resp_login($_SESSION['login']);
						$chaine_enfants="";
						if(count($tab_tmp_ele)>0) {
							$chaine_enfants=$tab_tmp_ele[1];
							$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[0]);
							if(count($tab_tmp_info_classes)>0) {
								$chaine_enfants.=" (<em>".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</em>)";
							}
							for($i=3;$i<count($tab_tmp_ele);$i+=2) {
								$chaine_enfants.=", ".$tab_tmp_ele[$i];
								unset($tab_tmp_info_classes);
								$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[$i-1]);
								if(count($tab_tmp_info_classes)>0) {
									$chaine_enfants.=" (<em>".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</em>)";
								}
							}
						}
						echo "Responsable de $chaine_enfants";
					}
					elseif($_SESSION["statut"] == "autre") {
						echo $_SESSION["statut_special"];
					}
				echo "</p>\n";

				//On v�rifie si le module de mise � jour est activ�
				if (getSettingValue("active_module_msj")==='y' and $_SESSION['statut'] == 'administrateur') {
					echo "<a href='javascript:ouvre_popup()'>";
						echo "<img style='border: 0px; width: 15px; height: 15px;' src='$gepiPath/images/info.png' alt='info' title='info' align='top' />";
					echo "</a>\n<br />\n";
				}

				// menus de droite
				// menu accueil
				echo "<ol id='premier_menu'>\n";
					if ($session_gepi->current_auth_mode == "sso" && $gepiSettings['sso_display_portail'] == 'yes') {
					echo "<li class='ligne_premier_menu'>\n";
						echo " <a href='".$gepiSettings['sso_url_portail']."'>";
							echo "<img src='$gepiPath/images/icons/retour_sso.png' width='16' height='16' alt='Portail' title='Retour portail' />";
							echo "<span class='menu_bandeau'>";
								echo "&nbsp;Retour portail";
							echo "</span>";
						echo "</a>\n";
					echo "</li>\n";
					}
					if ($session_gepi->current_auth_mode != "sso" || $gepiSettings['sso_hide_logout'] != 'yes') {
					echo "<li class='ligne_premier_menu'>\n";
						echo " <a href='$gepiPath/logout.php?auto=0'".insert_confirm_abandon().">";
							echo "<img src='$gepiPath/images/icons/quit_16.png' alt='Se d�connecter' title='Se d�connecter' />";
							echo "<span class='menu_bandeau'>";
								echo "&nbsp;Logout";
							echo "</span>";
						echo "</a>\n";
					echo "</li>\n";
					}
					echo "<li class='ligne_premier_menu'>\n";
						echo " <a href='$gepiPath/utilisateurs/mon_compte.php'".insert_confirm_abandon().">";
							echo "<img src='$gepiPath/images/icons/buddy.png' alt='My account' title='My account' />";
							echo "<span class='menu_bandeau'>";
								echo "&nbsp;Manage my account";
							echo "</span>";
						echo "</a>\n";
					echo "</li>\n";
					echo "<li class='ligne_premier_menu'>\n";
						echo "<a href='$gepiPath/accueil.php'".insert_confirm_abandon().">";
							echo "<img src='$gepiPath/images/icons/home.png' alt='Accueil' title='Accueil' />";
							echo "<span class='menu_bandeau'>";
								echo "&nbsp;Home";
							echo "</span>";
						echo "</a>\n";
					echo "</li>\n";
				echo "</ol>\n";
				echo "<div class='spacer'> </div>\n"; // s�pare les 2 menus
				// menu contact
				echo "<ol id='deux_menu'>";
						echo "<!-- Contact -->\n";
					$prefix = '';
					if (!isset($niveau_arbo)) {
						$prefix = "../";
					}elseif($niveau_arbo==1) {
						$prefix = "../";
					}elseif ($niveau_arbo==2) {
						$prefix = "../../";
					}
					echo "<li class='ligne_deux_menu'>\n";
						if ($_SESSION['statut'] == 'administrateur') {
							echo "<a onclick=\"window.open(this.href, '_blank'); return false;\" href=\"http://www.sajoscol.net/\">SAJOSCOL web site</a>";
						} else {
							if (getSettingValue("contact_admin_mailto")=='y') {
								$gepiAdminAdress=getSettingValue("gepiAdminAdress");
								$tmp_date=getdate();
								echo "<a href='mailto:$gepiAdminAdress?Subject=Gepi&amp;body=";
								if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
								echo ",%0d%0aCordialement.'>Contact administrator</a>";
							}
							else {
								echo("<a href=\"$gepiPath/gestion/contacter_admin.php\" onclick=\"centrerpopup('$gepiPath/gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes');return false;\" >Contact administrator</a>");
							}
						}
					echo "</li>\n";
					echo "<li class='ligne_deux_menu'>\n";
						echo "<a href=\"javascript:centrerpopup('$gepiPath/gestion/info_gepi.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">General information</a>";
					echo "</li>\n";
					echo "<li class='ligne_deux_menu'>\n";
						echo "<a href='javascript:centrerpopup(\"$gepiPath/gestion/info_vie_privee.php\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>Privacy</a>";
					echo "</li>\n";
				echo "</ol>\n";

		 //=== Affichage de la version de Gepi ===
				if ($_SESSION['statut'] == "administrateur") {
					  $version_gepi = '';
					  if ($gepiVersion == 'trunk') {
							// On a une version trunk
							$version_gepi .= "gepi-trunk";
					  } else {
							// On a une version num�rot�e
							$version_gepi .= "v".$gepiVersion;
							if ($gepiRcVersion != '') $version_gepi .= "-RC".$gepiRcVersion;
							if ($gepiBetaVersion != '') $version_gepi .= "-beta".$gepiBetaVersion;
							if ($gepiVersionMaintenance != '') $version_gepi .= ".".$gepiVersionMaintenance;
					  }
					  if ($gepiSvnRev != '') $version_gepi .= " r".$gepiSvnRev;

					echo "<p id='num_version'>".$version_gepi."</p>";
				}
				else{
					echo "<p id='num_version'>&nbsp;</p>"; // n�cessaire pour recaler le bandeau dans tous les navigateurs
				}

	// Fin du conteneur de Header

	echo "</div>\n";


// ==========> On ajoute la barre de menu <========================= //
if (!isset($nobar)) { $nobar = "non"; }

if (getSettingValue("utiliserMenuBarre") == "yes" AND $_SESSION["statut"] == "professeur" AND $nobar != 'oui') {

	// On v�rifie que l'utilisateur ne l'a pas enlev�e
	if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
		// ne pourrait-on pas utiliser $gepiPath plut�t que construire $prefix un peu plus haut ?
		if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
          /**
           * Inclusion des fonctions de  calendrier
           */
			require_once($prefix."edt_organisation/fonctions_calendrier.php");
		}elseif(file_exists("fonctions_calendrier.php")) {
          /**
           * Inclusion des fonctions de  calendrier
           */
			require_once("./fonctions_calendrier.php");
		}
        /**
         * Inclusion barre menu professeur
         */
		include("header_barre_menu.php");
	}
}
elseif ($_SESSION["statut"] == "administrateur") {

	// On v�rifie que l'utilisateur ne l'a pas enlev�e
	if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
		// ne pourrait-on pas utiliser $gepiPath plut�t que construire $prefix un peu plus haut ?
		if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
          /**
           * Inclusion des fonctions de  calendrier
           */
			require_once($prefix."edt_organisation/fonctions_calendrier.php");
		}elseif(file_exists("fonctions_calendrier.php")) {
          /**
           * Inclusion des fonctions de  calendrier
           */
			require_once("./fonctions_calendrier.php");
		}
        /**
         * Inclusion barre menu administrateur
         */
		include("header_barre_menu_admin.php");
	}
}
elseif ($_SESSION["statut"] == "scolarite") {

	// On v�rifie que l'utilisateur ne l'a pas enlev�e
	if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
		// ne pourrait-on pas utiliser $gepiPath plut�t que construire $prefix un peu plus haut ?
		if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
          /**
           * Inclusion des fonctions de  calendrier
           */
			require_once($prefix."edt_organisation/fonctions_calendrier.php");
		}elseif(file_exists("fonctions_calendrier.php")) {
          /**
           * Inclusion des fonctions de  calendrier
           */
			require_once("./fonctions_calendrier.php");
		}
        /**
         * Inclusion barre menu scolarit�
         */
		include("header_barre_scolarite_template.php");

		if (count($tbs_menu_scol)>0) {
			echo '<div id="menu_barre">
	<div class="menu_barre_bottom"></div>
	<div class="menu_barre_container">
		<ul class="niveau1">
';
			foreach ($tbs_menu_scol as $value) { 
				if ("$value[li]"!="") {
					echo $value['li'];
				}
			}
			unset($value); 
			echo '
		</ul>
	</div>
</div>
';
		}
	}
}
elseif ($_SESSION["statut"] == "cpe") {

	// On v�rifie que l'utilisateur ne l'a pas enlev�e
	if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
		// ne pourrait-on pas utiliser $gepiPath plut�t que construire $prefix un peu plus haut ?
		if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
			require_once($prefix."edt_organisation/fonctions_calendrier.php");
		}elseif(file_exists("fonctions_calendrier.php")) {
			require_once("./fonctions_calendrier.php");
		}
		include("header_barre_cpe_template.php");

		if (count($tbs_menu_cpe)>0) {
			echo '<div id="menu_barre">
	<div class="menu_barre_bottom"></div>
	<div class="menu_barre_container">
		<ul class="niveau1">
';
			foreach ($tbs_menu_cpe as $value) { 
				if ("$value[li]"!="") {
					echo $value['li'];
				}
			}
			unset($value); 
			echo '
		</ul>
	</div>
</div>
';
		}
	}
}
echo "<div id='container'>\n";
if ((isset($_GET['msg'])) or (isset($_POST['msg'])) or (isset($msg))) {
	$msg = isset($_POST['msg']) ? unslashes($_POST['msg']) : (isset($_GET['msg']) ? unslashes($_GET['msg']) : $msg);
	if ($msg != '') {
		echo "<div class=\"headerMessage\">";
		echo($msg);
		echo "</div>";
	}
}
}
// D�commenter la ligne ci -dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();

?>

