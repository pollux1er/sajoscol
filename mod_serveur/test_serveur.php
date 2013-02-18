<?php

/**
 * Fichier temporaire uniquement présent dans les versions RC pour teter les configurations serveur
 * et d'autres paramètres pour comprendre certaines erreurs.
 *
 * @version $Id: test_serveur.php 8596 2011-11-04 14:05:15Z jjacquard $ 1.5.1RC1
 *
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// On initialise
$titre_page = "Administration - Paramètres du serveur";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Définition de la classe php
require_once("../class_php/serveur_infos.class.php");

//fonction de tests d'encodage
require_once(dirname(__FILE__)."/test_encoding_functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Instance de la classe infos (voir serveur_infos.class.php)
$test = new infos;

// Analyse des paramètres
if ($test->secureServeur() == 'on') {
	$style_register = ' style="color: red; font-weight: bold;"';
}elseif($test->secureServeur() == 'off'){
	$style_register = '';
}else{
	$style_register = ' style="color: red; font-style: italic;"';
}
if ($test->maxExecution() <= '30') {
	$warning_maxExec = '&nbsp;(This value can be a little short if your establishment is significant)';
}else{
	$warning_maxExec = '&nbsp;(This value should be enough in the great majority to the cases)';
}
$charset = $test->defautCharset();
/*+++++++++++++++++++++ On insère l'entête de Gepi ++++++++++++++++++++*/
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";

require_once("../lib/header.inc");
/*++++++++++++++++++++++ fin entête ++++++++++++++++++++++++++++++++++++*/
echo '
<p class="bold"><a href="../gestion/index.php#test_serveur">
	<img src="../images/icons/back.png" alt="Retour" class="back_link" /> Return</a>
</p>
';


/* ======= Affichage des paramètres ============= */



echo '
	<h4>Source data of your Web server :</h4>
	<p'.$style_register.'>the register_globals is with '.$test->secureServeur().'.</p>
	<p>The Web server is '.$test->version_serveur().'</p>
	<p>Encoding '.$charset['toutes'].' -> encodage par défaut : '.$charset['defaut'].'.</p>';

echo '<p>Your version of php is '.$test->versionPhp().'.</p>
	<p>Your version of database server MySql is '.$test->versionMysql().'.</p>';
if ($test->versionGd()) {
	echo '<p>Your version of the module GD is '.$test->versionGd().'&nbsp;(essential for all the images).</p>';
} else {
	echo '<p class="red">GD is not installed (the module GD is essential for the images)';
}
	echo '<br />
	<hr />
	<h4>&nbsp;&nbsp;List modules implemented with your php : </h4>'.$test->listeExtension().'
	<hr />
	<a name="reglages_php"></a>
	<h4>The adjustments php : </h4>
	<ul style="list-style-type:circle; margin-left:3em;">
	<li style="list-style-type:circle">The allocated maximum memory with php is of '.$test->memoryLimit().' (<i>memory_limit</i>).
	</li>
	<li style="list-style-type:circle">The maximum size of a variable sent to Gepi should not exceed '.$test->maxSize().' (<i>post_max_size</i>).
	</li>
	<li style="list-style-type:circle">Allocated maximum time with php to treat a script is of '.$test->maxExecution().' seconds'.$warning_maxExec.' (<i>max_execution_time</i>).
	</li>
	<li style="list-style-type:circle">The maximum size of a file sent to Gepi is of '.$test->tailleMaxFichier().' (<i>upload_max_filesize</i>).
	</li>';
	$max_file_uploads=ini_get('max_file_uploads');
	echo '
	<li style="list-style-type:circle">It can be uploadé to the maximum '.$max_file_uploads.' file at the same time (<i>max_file_uploads</i>).
	</li>';
	$session_gc_maxlifetime=ini_get("session.gc_maxlifetime");
	$session_gc_maxlifetime_minutes=$session_gc_maxlifetime/60;
	if((getSettingValue("sessionMaxLength")!="")&&($session_gc_maxlifetime_minutes<getSettingValue("sessionMaxLength"))) {
		echo '
	<li style="list-style-type:circle">The maximum duration of session is regulated with <span style="color:red; font-weight:bold;">'.$session_gc_maxlifetime.' seconds</span>, that is to say a maximum of <span style="color:red; font-weight:bold;">'.$session_gc_maxlifetime_minutes.' minutes</span> (<i>session.maxlifetime</i> in the file php.ini).<br />
	That restricts the maximum duration of session more than what is
parameterized in <a href="../gestion/param_gen.php#sessionMaxLength">General configuration</a>.</li>
	It is the faible/restrictive value which is taken into account.</li>';
	}
	else {
		echo '
	<li style="list-style-type:circle">Llasted maximum of session is regulated with '.$session_gc_maxlifetime.' seconds, that is to say a maximum of '.$session_gc_maxlifetime_minutes.' minutes (<i>session.maxlifetime</i> in the file php.ini).</li>';
	}
	echo "</ul>\n";

	$suhosin_post_max_totalname_length=ini_get('suhosin.post.max_totalname_length');
	if($suhosin_post_max_totalname_length!='') {
		echo "<h4>Configuration suhosin</h4>\n";
		echo "<p>The module suhosin is activated.<br />\nA too restrictive parameter setting of this module can disturb the
operation of Gepi, particularly in the pages comprising of many fields of form (<i>such as for example in the page of seizure of the appreciations by the
professors</i>)</p>\n";

		$tab_suhosin=array('suhosin.cookie.max_totalname_length', 
		'suhosin.get.max_totalname_length', 
		'suhosin.post.max_totalname_length', 
		'suhosin.post.max_value_length', 
		'suhosin.request.max_totalname_length', 
		'suhosin.request.max_value_length', 
		'suhosin.request.max_vars');

		for($i=0;$i<count($tab_suhosin);$i++) {
			echo "- ".$tab_suhosin[$i]." = ".ini_get($tab_suhosin[$i])."<br />\n";
		}

		echo "In the event of problem, you can, either to decontaminate the module,
or to increase the values.<br />\n";
		echo "The file of configuration of suhosin is usually in /etc/php5/conf.d/suhosin.ini<br />\nIn the event of modification of this file, think of starting again the
service apache then to take into account the modification.<br />\n";
	}

	echo "<br />\n";
	echo "<hr />\n";
       echo "<h4>Encoding of the characters : </h4>\n";
       if (function_exists('iconv')) {
           echo "iconv is installed on your system<br />";
       } else {
           echo "iconv is not installed on your system, that is not essential but it is
recomandé<br />";
       }
       if (function_exists('mb_convert_encoding')) {
           echo "mbstring is installed on your system<br />";
       } else {
           echo "<p style=\"color:red;\">mbstring (Character strings multi-bytes) is not installed on your system, it is
necessary starting from next version 1.6.0</p>";
       }

       echo "<p style=\"color:red;\">";
       if (!test_check_utf8()) {
           echo ' : failure of test_check_utf8()</p>';
       } else {
           echo "</p>success of test_check_utf8()<br />\n";
       }
       echo "<p style=\"color:red;\">";
       if (!test_detect_encoding()) {
           echo ' : failure of test_detect_encoding()</p>';
       } else {
        echo "</p>success of test_detect_encoding()<br />\n";
       }
       echo "<p style=\"color:red;\">";
       if (!test_ensure_utf8()) {
           echo ' : failure of test_ensure_utf8()</p>';
       } else {
           echo "</p>success of test_ensure_utf8()<br />\n";
       }
       echo "<br />\n";
       
	
	echo "<hr />\n";
	echo "<h4>Local of the system : </h4>\n";
	$locale = setlocale(LC_TIME,0);
	echo "local currently used for the dates : $locale";
	
	//on va tester les locale sur LC_NUMERIC
	$locale_num = setlocale(LC_NUMERIC,0);
	$return = @setlocale(LC_NUMERIC,'fr-utf-8','fr_FR.utf-8','fr_FR.utf8','fr_FR.UTF-8','fr_FR.UTF8');
	if (!isset($return) || !$return) {
	    echo "<p style=\"color:red;\">";
	    echo 'For next version 1.6.0, your system does not seem to have of local utf-8 d\'installed. It is possible that without local utf-8 certain postings of
dates are inestetic for the version 1.6.0</p>';
	}
	@setlocale(LC_NUMERIC,$locale_num);//on remet comme avant le test
	echo "<br />\n";
	
	
       
   echo "<hr />\n";
	echo "<h4>Rights on the files : </h4>\n";
	echo "Certain files must be accessible in writing for Gepi.<br />\n";
	test_ecriture_dossier();
	echo "If the rights are not correct, you will have to correct them in ftp,
SFTP or in console according to access's of which you lay out on the
waiter.<br />\n";

	echo "<br />\n";
	echo "<p>Test of writing in the file of personalization of the colors (<i>voir <a href='../gestion/param_couleurs.php'>General Management /Colors settings</a></i>)&nbsp;:<br />";
	$test=test_ecriture_style_screen_ajout();
	if($test) {
		echo "The file style_screen_ajout.css with the root of the tree structure Gepi is accessible in writing.\n";
	}
	else {
		echo "<sapn style='color:red'><b>ERREUR</b>&nbsp;: The file style_screen_ajout.css with the root of the Gepi tree structure could not be created or is
not accessible in writing.</span>\n";
	}
	echo "</p>\n";

echo '<br /><br /><br />';

/**
 * inclusion du footer
 */
require_once("../lib/footer.inc.php");
?>