<?php
/** Fonctions accessibles dans toutes les pages
 * 
 * $Id: share.inc.php 8733 2011-12-22 15:22:19Z crob $
 * 
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Initialisation
 * @subpackage general
 *
*/



/**
 * Fonctions de manipulation du gepi_alea contre les attaques CRSF
 * 
 * @see share-csrf.inc.php
 */
include_once dirname(__FILE__).'/share-csrf.inc.php';
/**
 * Fonctions qui produisent du code html
 * 
 * @see share-html.inc.php
 */
include_once dirname(__FILE__).'/share-html.inc.php';
/**
 * Fonctions de manipulation des conteneurs et des notes
 * 
 * @see share-notes.inc.php
 */
include_once dirname(__FILE__).'/share-notes.inc.php';
/**
 * Fonctions de manipulation des conteneurs et des notes
 * 
 * @see share-aid.inc.php
 */
include_once dirname(__FILE__).'/share-aid.inc.php';
/**
 * Fonctions de manipulation des conteneurs et des notes
 * 
 * @see share-pdf.inc.php
 */
include_once dirname(__FILE__).'/share-pdf.inc.php';








/**
 * Envoi d'un courriel
 *
 * @param string $sujet Le sujet du message
 * @param string $message Le message
 * @param string $destinataire Le destinataire
 * @param string $ajout_headers Text ‡ ajouter dans le header
 */
function envoi_mail($sujet, $message, $destinataire, $ajout_headers='') {

	$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";

	if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}

  $subject = $gepiPrefixeSujetMail."GEPI : $sujet";
  $subject = "=?ISO-8859-1?B?".base64_encode($subject)."?=\r\n";
  
  $headers = "X-Mailer: PHP/" . phpversion()."\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
  $headers .= "From: Mail automatique Gepi <ne-pas-repondre@".$_SERVER['SERVER_NAME'].">\r\n";
  $headers .= $ajout_headers;

	// On envoie le mail
	$envoi = mail($destinataire,
		$subject,
		$message,
	  $headers);
}

/**
 * Verification de la validitÈ d'un mot de passe
 * 
 * longueur : getSettingValue("longmin_pwd") minimum
 * 
 * composÈ de lettres et d'au moins un chiffre
 *

 * @param string $password Mot de passe
 * @param boolean $flag Si $flag = 1, il faut Ègalement au moins un caractËres spÈcial (voir $char_spec dans global.inc)
 * @return boolean TRUE si le mot de passe est valable
 * @see getSettingValue()
 * @todo on dÈclare $char_spec alors qu'on ne l'utilise pas, n'y aurait-il pas un problËme ?
 */
function verif_mot_de_passe($password,$flag) {
	global $info_verif_mot_de_passe;

	if ($flag == 1) {
		if(preg_match("/(^[a-zA-Z]*$)|(^[0-9]*$)/", $password)) {
			$info_verif_mot_de_passe="The password should not be only numerical or only alphabetical.";
			return FALSE;
		}
		elseif(preg_match("/^[[:alnum:]\W]{".getSettingValue("longmin_pwd").",}$/", $password) and preg_match("/[\W]+/", $password) and preg_match("/[0-9]+/", $password)) {
			$info_verif_mot_de_passe="";
			return TRUE;
		}
		else {
			if(preg_match("/^[A-Za-z0-9]*$/", $password)) {
				$info_verif_mot_de_passe="The password must comprise at least a special character (#, *,...).";
			}
			elseif (strlen($password) < getSettingValue("longmin_pwd")) {
				$info_verif_mot_de_passe="The length of the password must be higher or equal to ".getSettingValue("longmin_pwd").".";
				return FALSE;
			}
			else {
				// Euh... qu'est-ce qui a ÈtÈ saisi?
				$info_verif_mot_de_passe="";
			}
			return FALSE;
		}
	}
	else {
		if(preg_match("/(^[a-zA-Z]*$)|(^[0-9]*$)/", $password)) {
			$info_verif_mot_de_passe="The password should not be only numerical or only alphabetical.";
			return FALSE;
		}
		elseif (strlen($password) < getSettingValue("longmin_pwd")) {
			$info_verif_mot_de_passe="The length of the password must be higher or equal to ".getSettingValue("longmin_pwd").".";
			return FALSE;
		}
		else {
			$info_verif_mot_de_passe="";
			return TRUE;
		}
	}
}

/**
 * Teste si le login existe dÈj‡ dans la base
 *
 * @param string $s le login testÈ
 * @return string yes si le login existe, no sinon
 */
function test_unique_login($s) {
    // On vÈrifie que le login ne figure pas dÈj‡ dans la base utilisateurs
    $test1 = mysql_num_rows(mysql_query("SELECT login FROM utilisateurs WHERE (login='$s' OR login='".strtoupper($s)."')"));
    if ($test1 != "0") {
        return 'no';
    } else {
        $test2 = mysql_num_rows(mysql_query("SELECT login FROM eleves WHERE (login='$s' OR login = '".strtoupper($s)."')"));
        if ($test2 != "0") {
            return 'no';
        } else {
			$test3 = mysql_num_rows(mysql_query("SELECT login FROM resp_pers WHERE (login='$s' OR login='".strtoupper($s)."')"));
			if ($test3 != "0") {
				return 'no';
			} else {
	            return 'yes';
	        }
        }
    }
}

/**
 * VÈrifie l'unicitÈ du login
 * 
 * On vÈrifie que le login ne figure pas dÈj‡ dans une des bases ÈlËve des annÈes passÈes 
 *
 * @param string $s le login ‡ vÈrifier
 * @param <type> $indice ??
 * @return string yes si le login existe, no sinon
 */
function test_unique_e_login($s, $indice) {
    // On vÈrifie que le login ne figure pas dÈj‡ dans la base utilisateurs
    $test7 = mysql_num_rows(mysql_query("SELECT login FROM utilisateurs WHERE (login='$s' OR login='".strtoupper($s)."')"));

    if ($test7 != "0") {

        // Si le login figure dÈj‡ dans une des bases ÈlËve des annÈes passÈes ou bien
        // dans la base utilisateurs, on retourne 'no' !
        return 'no';
    } else {
        // Si le login ne figure pas dans une des bases ÈlËve des annÈes passÈes ni dans la base
        // utilisateurs, on vÈrifie qu'un mÍme login ne vient pas d'Ítre attribuÈ !
        $test_tempo2 = mysql_num_rows(mysql_query("SELECT col2 FROM tempo2 WHERE (col2='$s' or col2='".strtoupper($s)."')"));
        if ($test_tempo2 != "0") {
            return 'no';
        } else {
            $reg = mysql_query("INSERT INTO tempo2 VALUES ('$indice', '$s')");
            return 'yes';
        }
    }
}

/**
 * GÈnÈre le login ‡ partir du nom et du prÈnom
 * 
 * GÈnËre puis nettoie un login pour qu'il soit valide et unique
 * 
 * Le mode de gÈnÈration doit Ítre passÈ en argument
 * 
 * name             ‡ partir du nom
 * 
 * name8            ‡ partir du nom, rÈduit ‡ 8 caractËres
 * 
 * fname8           premiËre lettre du prÈnom + nom, rÈduit ‡ 8 caractËres
 * 
 * fname19          premiËre lettre du prÈnom + nom, rÈduit ‡ 19 caractËres
 * 
 * firstdotname     prÈnom.nom
 * 
 * firstdotname19   prÈnom.nom rÈduit ‡ 19 caractËres
 * 
 * namef8           nom rÈduit ‡ 7 caractËres + premiËre lettre du prÈnom
 * 
 * si $_mode est NULL, fname8 est utilisÈ
 * 
 * @param string $_nom nom de l'utilisateur
 * @param string $_prenom prÈnom de l'utilisateur
 * @param string $_mode Le mode de gÈnÈration ou NULL
 * @return string|booleanLe login gÈnÈrÈ ou FALSE si on obtient un login vide
 * @see test_unique_login()
 */
function generate_unique_login($_nom, $_prenom, $_mode) {

	if ($_mode == NULL) {
		$_mode = "fname8";
	}
    // On gÈnËre le login
	$_prenom = strtr($_prenom, "ÁÈËÎÍ…»À ¸˚˘‹€ÔÓœŒ‰‚‡ƒ¬¿", "ceeeeEEEEuuuUUiiIIaaaAAA");
    $_prenom = preg_replace("/[^a-zA-Z.\-]/", "", $_prenom);
	$_nom = strtr($_nom, "ÁÈËÎÍ…»À ¸˚˘‹€ÔÓœŒ‰‚‡ƒ¬¿", "ceeeeEEEEuuuUUiiIIaaaAAA");
    $_nom = preg_replace("/[^a-zA-Z.\-]/", "", $_nom);

	if($_nom=='') {return FALSE;}

    if ($_mode == "name") {
            $temp1 = $_nom;
            $temp1 = preg_replace("/ /","", $temp1);
            $temp1 = preg_replace("/-/","_", $temp1);
            $temp1 = preg_replace("/'/","", $temp1);
        } elseif ($_mode == "name8") {
            $temp1 = $_nom;
            $temp1 = preg_replace("/ /","", $temp1);
            $temp1 = preg_replace("/-/","_", $temp1);
            $temp1 = preg_replace("/'/","", $temp1);
            $temp1 = substr($temp1,0,8);
        } elseif ($_mode == "fname8") {
			if($_prenom=='') {return FALSE;}
            $temp1 = $_prenom{0} . $_nom;
            $temp1 = preg_replace("/ /","", $temp1);
            $temp1 = preg_replace("/-/","_", $temp1);
            $temp1 = preg_replace("/'/","", $temp1);
            $temp1 = substr($temp1,0,8);
        } elseif ($_mode == "fname19") {
			if($_prenom=='') {return FALSE;}
            $temp1 = $_prenom{0} . $_nom;
            $temp1 = preg_replace("/ /","", $temp1);
            $temp1 = preg_replace("/-/","_", $temp1);
            $temp1 = preg_replace("/'/","", $temp1);
            $temp1 = substr($temp1,0,19);
        } elseif ($_mode == "firstdotname") {
			if($_prenom=='') {return FALSE;}
            $temp1 = $_prenom . "." . $_nom;

            $temp1 = preg_replace("/ /","", $temp1);
            $temp1 = preg_replace("/-/","_", $temp1);
            $temp1 = preg_replace("/'/","", $temp1);
        } elseif ($_mode == "firstdotname19") {
			if($_prenom=='') {return FALSE;}
            $temp1 = $_prenom . "." . $_nom;
            $temp1 = preg_replace("/ /","", $temp1);
            $temp1 = preg_replace("/'/","", $temp1);
            $temp1 = substr($temp1,0,19);
        } elseif ($_mode == "namef8") {
			if($_prenom=='') {return FALSE;}
            $temp1 =  substr($_nom,0,7) . $_prenom{0};
            $temp1 = preg_replace("/ /","", $temp1);
            $temp1 = preg_replace("/-/","_", $temp1);
            $temp1 = preg_replace("/'/","", $temp1);
        } else {
        	return FALSE;
        }

        $login_user = $temp1;

        // Nettoyage final
        $login_user = substr($login_user, 0, 50);
        $login_user = preg_replace("/[^A-Za-z0-9._\-]/","",trim($login_user));

        $test1 = $login_user{0};
		while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
			$login_user = substr($login_user, 1);
			$test1 = $login_user{0};
		}

		$test1 = $login_user{strlen($login_user)-1};
		while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
			$login_user = substr($login_user, 0, strlen($login_user)-1);
			$test1 = $login_user{strlen($login_user)-1};
		}

        // On teste l'unicitÈ du login que l'on vient de crÈer
        $m = '';
        $test_unicite = 'no';
        while ($test_unicite != 'yes') {
            $test_unicite = test_unique_login($login_user.$m);
            if ($test_unicite != 'yes') {
            	if ($m == '') {
            		$m = 2;
            	} else {
                	$m++;
            	}
            } else {
            	$login_user = $login_user.$m;
            }
        }

		return $login_user;
}

/**
 * Fonction qui propose l'ordre d'affichage du nom, prÈnom et de la civilitÈ en fonction des rÈglages de la classe de l'ÈlËve
 *
 * @param string $login login de l'utilisateur
 * @param integer $id_classe Id de la classe
 * @return string nom, prÈnom, civilitÈ formatÈ
 */
function affiche_utilisateur($login,$id_classe) {
    $req = mysql_query("select nom, prenom, civilite from utilisateurs where login = '".$login."'");
	$nom = @mysql_result($req, 0, 'nom');
    $prenom = @mysql_result($req, 0, 'prenom');
    $civilite = @mysql_result($req, 0, 'civilite');
    $req_format = mysql_query("select format_nom from classes where id = '".$id_classe."'");
    $format = mysql_result($req_format, 0, 'format_nom');
    $result = "";
    $i='';
    if ((($format == 'ni') OR ($format == 'in') OR ($format == 'cni') OR ($format == 'cin')) AND ($prenom != '')) {
        $temp = explode("-", $prenom);
        $i = substr($temp[0], 0, 1);
        if (isset($temp[1]) and ($temp[1] != '')) $i .= "-".substr($temp[1], 0, 1);
        $i .= ". ";
    }
    switch( $format ) {
    case 'np':
    $result = $nom." ".$prenom;
    break;
    case 'pn':
    $result = $prenom." ".$nom;
    break;
    case 'in':
    $result = $i.$nom;
    break;
    case 'ni':
    $result = $nom." ".$i;
    break;
    case 'cnp':
    if ($civilite != '') $result = $civilite." ";
    $result .= $nom." ".$prenom;
    break;
    case 'cpn':
    if ($civilite != '') $result = $civilite." ";
    $result .= $prenom." ".$nom;
    break;
    case 'cin':
    if ($civilite != '') $result = $civilite." ";
    $result .= $i.$nom;
    break;
    case 'cni':
    if ($civilite != '') $result = $civilite." ";
    $result .= $nom." ".$i;
    break;
    $result = $nom." ".$prenom;

    }
    return $result;
}

/**
 * Verifie si l'extension d_base est active
 *
 * Affiche une page d'avertissement si le module dbase n'est pas actif
 * 
 */
function verif_active_dbase() {
    if (!function_exists("dbase_open"))  {
        echo "<center><p class=grand>CAUTION : PHP is not configured to manage files GEP (dbf).
        <br />The extension d_base is not active. Contact the administrator of the server to correct the
problem.</p></center></body></html>";
        die();
    }
}

/**
 * Ecrit une balise <select> de date jour mois annÈe
 * correction W3C : ajout de la balise de fin </option> ‡ la fin de $out_html
 * CrÈation d'un label pour passer les tests WAI
 *
 * @param string $prefix l'attribut name sera de la forme $prefixday, $prefixMois,...
 * @param integer $day
 * @param integer $month
 * @param integer $year
 * @param string $option Si = more_years, on ajoute +5 et -5 annÈes aux annÈes possibles
 * @see getSettingValue()
 */
function genDateSelector($prefix, $day, $month, $year, $option)
{
    if($day   == 0) $day = date("d");
    if($month == 0) $month = date("m");
    if($year  == 0) $year = date("Y");

	 echo "\n<label for=\"${prefix}jour\"><span style='display:none;'>Day</span></label>\n";
    echo "<select id=\"${prefix}jour\" name=\"${prefix}day\">\n";

    for($i = 1; $i <= 31; $i++)
        echo "<option value = \"$i\"" . ($i == $day ? " selected=\"selected\"" : "") . ">$i</option>\n";

    echo "</select>\n";

	 echo "\n<label for=\"${prefix}mois\"><span style='display:none;'>Month</span></label>\n";
    echo "<select id=\"${prefix}mois\" name=\"${prefix}month\">\n";

    for($i = 1; $i <= 12; $i++)
    {
        $m = strftime("%b", mktime(0, 0, 0, $i, 1, $year));

        echo "<option value=\"$i\"" . ($i == $month ? " selected=\"selected\"" : "") . ">$m</option>\n";
    }

    echo "</select>\n";

	 echo "\n<label for=\"${prefix}annee\"><span style='display:none;'>Year</span></label>\n";
    echo "<select id=\"${prefix}annee\" name=\"${prefix}year\">\n";

    $min = strftime("%Y", getSettingValue("begin_bookings"));
    if ($option == "more_years") $min = date("Y") - 5;

    $max = strftime("%Y", getSettingValue("end_bookings"));
    if ($option == "more_years") $max = date("Y") + 5;

    for($i = $min; $i <= $max; $i++)
        print "<option" . ($i == $year ? " selected=\"selected\"" : "") . ">$i</option>\n";
    
    echo "</select>\n";
}


/**
 * Remplit un fichier de suivi des actions
 * 
 * Passer la variable $local_debug ‡ "y" pour activer le remplissage du fichier "/tmp/calcule_moyenne.txt" de debug
 * 
 * @param string $texte 
 */
function fdebug($texte){
	$local_debug="n";
	if($local_debug=="y") {
		$fich=fopen("/tmp/calcule_moyenne.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}


/**
 * VÈrifie que la page est bien accessible par l'utilisateur
 *
 * @global string 
 * @return booleanTRUE si la page est accessible, FALSE sinon
 * @see tentative_intrusion()
 */
function checkAccess() {
    global $gepiPath;
    $url = parse_url($_SERVER['SCRIPT_NAME']);
    if ($_SESSION["statut"] == 'autre') {

    	$sql = "SELECT autorisation
	    from droits_speciaux
    	where nom_fichier = '" . substr($url['path'], strlen($gepiPath)) . "'
		AND id_statut = '" . $_SESSION['statut_special_id'] . "'";

    }else{

		$sql = "select " . $_SESSION['statut'] . "
	    from droits
    	where id = '" . substr($url['path'], strlen($gepiPath)) . "'
    	;";

	}

    $dbCheckAccess = sql_query1($sql);
    if (substr($url['path'], 0, strlen($gepiPath)) != $gepiPath) {
        tentative_intrusion(2, "Attempt of access with wild modification of gepiPath");
        return (FALSE);
    } else {
        if ($dbCheckAccess == 'V') {
            return (TRUE);
        } else {
            tentative_intrusion(1, "Attempt of access to a file without having the rights necessary");
            return (FALSE);
        }
    }
}


/**
 * VÈrifie qu'un enseignant enseigne une matiËre dans une classe
 *
 * @deprecated la table j_classes_matieres_professeurs n'existe plus
 * @param string $login Login de l'enseignant
 * @param int $id_classe Id de la classe
 * @param type $matiere
 * @return boolean
 */
function Verif_prof_classe_matiere ($login,$id_classe,$matiere) {
    if(empty($login) || empty($id_classe) || empty($matiere)) {return FALSE;}
    $call_prof = mysql_query("SELECT id_professeur FROM j_classes_matieres_professeurs WHERE (id_classe='".$id_classe."' AND id_matiere='".$matiere."')");
    $nb_profs = mysql_num_rows($call_prof);
    $k = 0;
    $flag = 0;
    while ($k < $nb_profs) {
        $prof = @mysql_result($call_prof, $k, "id_professeur");
        if (strtolower($login) == strtolower($prof)) {$flag = 1;}
        $k++;
    }
    if ($flag == 0) {
        return FALSE;
    } else {
        return TRUE;
    }
}

/**
 * Recherche dans la base l'adresse courriel d'un utilisateur
 *
 * @param string $login_u Login de l'utilisateur
 * @return string adresse courriel de l'utilisateur
 */
function retourne_email ($login_u) {
$call = mysql_query("SELECT email FROM utilisateurs WHERE login = '$login_u'");
$email = @mysql_result($call, 0, "email");
return $email;

}

/**
 * Renvoie une chaine dÈbarassÈe de l'encodage ASCII
 *
 * @param string $s le texte ‡ convertir
 * @return string le texte avec les lettres accentuÈes
 */
function dbase_filter($s){
  for($i = 0; $i < strlen($s); $i++){
    $code = ord($s[$i]);
    switch($code){
    case 129:    $s[$i] = "¸"; break;
    case 130:   $s[$i] = "È"; break;
    case 131:    $s[$i] = "‚"; break;
    case 132:    $s[$i] = "‰"; break;
    case 133:    $s[$i] = "‡"; break;
    case 135:    $s[$i] = "Á"; break;
    case 136:    $s[$i] = "Í"; break;
    case 137:    $s[$i] = "Î"; break;
    case 138:    $s[$i] = "Ë"; break;
    case 139:    $s[$i] = "Ô"; break;
    case 140:    $s[$i] = "Ó"; break;
    case 147:    $s[$i] = "Ù"; break;
    case 148:    $s[$i] = "ˆ"; break;
    case 150:    $s[$i] = "˚"; break;
    case 151:    $s[$i] = "˘"; break;
    }
  }
  return $s;
}

/**
 * Renvoie le navigateur et sa version
 *
 * @param string $HTTP_USER_AGENT
 * @return string navigateur - version
 */
function detect_browser($HTTP_USER_AGENT) {
	// D'aprËs le fichier db_details_common.php de phpmyadmin
	/*
	$f=fopen("/tmp/detect_browser.txt","a+");
	fwrite($f,date("d/m/Y His").": $HTTP_USER_AGENT\n");
	fclose($f);
	*/
	if(function_exists('preg_match')) {
		if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'OPERA';
		} elseif(preg_match('/MSIE ([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'Internet Explorer';
		} elseif(preg_match('/OmniWeb\/([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'OMNIWEB';
		} elseif(preg_match('/(Konqueror\/)(.*)(;)/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'KONQUEROR';
		} elseif(preg_match('/Mozilla\/([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			if(preg_match('/Chrome\/([0-9.]*)/', $HTTP_USER_AGENT, $log_version2)) {
				$BROWSER_VER = $log_version2[1];
				$BROWSER_AGENT = 'GoogleChrome';
			} elseif(preg_match('/Safari\/([0-9]*)/', $HTTP_USER_AGENT, $log_version2)) {
				$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
				$BROWSER_AGENT = 'SAFARI';
			} elseif(preg_match('/Firefox\/([0-9.]*)/', $HTTP_USER_AGENT, $log_version2)) {
				$BROWSER_VER = $log_version2[1];
				$BROWSER_AGENT = 'Firefox';
			} else {
				$BROWSER_VER = $log_version[1];
				$BROWSER_AGENT = 'MOZILLA';
			}
		} else {
			$BROWSER_VER = '';
			$BROWSER_AGENT = $HTTP_USER_AGENT;
		}
	}
	elseif(function_exists('mb_ereg')) {
		if (mb_ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'OPERA';
		} elseif(mb_ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'Internet Explorer';
		} elseif(mb_ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'OMNIWEB';
		} elseif(mb_ereg('(Konqueror/)(.*)(;)', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'KONQUEROR';
		} elseif((mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(mb_ereg('GoogleChrome/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'GoogleChrome';
		} elseif((mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(mb_ereg('Safari/([0-9]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
			$BROWSER_AGENT = 'SAFARI';
		} elseif((mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(mb_ereg('Firefox/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'Firefox';
		} elseif(mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'MOZILLA';
		} else {
			$BROWSER_VER = '';
			$BROWSER_AGENT = $HTTP_USER_AGENT;
		}
	}
	elseif(function_exists('ereg')) {
		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'OPERA';
		} elseif(ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'Internet Explorer';
		} elseif(ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'OMNIWEB';
		} elseif(ereg('(Konqueror/)(.*)(;)', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'KONQUEROR';
		} elseif((ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(ereg('GoogleChrome/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'GoogleChrome';
		} elseif((ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(ereg('Safari/([0-9]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
			$BROWSER_AGENT = 'SAFARI';
		} elseif(ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'MOZILLA';
		} elseif((ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(ereg('Firefox/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'Firefox';
		} else {
			$BROWSER_VER = '';
			$BROWSER_AGENT = $HTTP_USER_AGENT;
		}
	}
	else {
		$BROWSER_VER = '';
		$BROWSER_AGENT = $HTTP_USER_AGENT;
	}
	return  $BROWSER_AGENT." - ".$BROWSER_VER;
}

/**
 * Formate une date en jour/mois/annÈe
 * 
 * Accepte les dates aux formats YYYY-MM-DD ou YYYYMMDD ou YYYY-MM-DD xx:xx:xx
 * 
 * Retourne la date passÈe en argument si le format n'est pas bon
 *
 * @param date $date La date ‡ formater
 * @return string la date formatÈe
 */
function affiche_date_naissance($date) {
    if (strlen($date) == 10) {
        // YYYY-MM-DD
        $annee = substr($date, 0, 4);
        $mois = substr($date, 5, 2);
        $jour = substr($date, 8, 2);
    }
    elseif (strlen($date) == 8 ) {
        // YYYYMMDD
        $annee = substr($date, 0, 4);
        $mois = substr($date, 4, 2);
        $jour = substr($date, 6, 2);
    }
    elseif (strlen($date) == 19 ) {
        // YYYY-MM-DD xx:xx:xx
        $annee = substr($date, 0, 4);
        $mois = substr($date, 5, 2);
        $jour = substr($date, 8, 2);
    }

    else {
        // Format inconnu
        return($date);
    }
    return $jour."/".$mois."/".$annee ;
}

/**
 *
 * @global mixed 
 * @global mixed 
 * @global mixed 
 * @return booleanTRUE si on a une nouvelle version 
 */
function test_maj() {
    global $gepiVersion, $gepiRcVersion, $gepiBetaVersion;
    $version_old = getSettingValue("version");
    $versionRc_old = getSettingValue("versionRc");
    $versionBeta_old = getSettingValue("versionBeta");

   if ($version_old =='') {
       return TRUE;
       die();
   }
   if ($gepiVersion > $version_old) {
        // On a une nouvelle version stable
       return TRUE;
       die();
   }
   if (($gepiVersion == $version_old) and ($versionRc_old!='')) {
        // On avait une RC
       if (($gepiRcVersion > $versionRc_old) or ($gepiRcVersion=='')) {
            // Soit on a une nouvelle RC, soit on est passÈ de RC ‡ stable
           return TRUE;
           die();
       }
   }
   if (($gepiVersion == $version_old) and ($versionBeta_old!='')) {
        // On avait une Beta
       if (($gepiBetaVersion > $versionBeta_old) or ($gepiBetaVersion=='')) {
            // Soit on a une nouvelle Beta, soit on est passÈ ‡ une RC ou une stable
           return TRUE;
           die();
       }
   }
   return FALSE;
}

/**
 * Recherche si la mise ‡ jour est ‡ faire
 *
 * @global mixed 
 * @global mixed 
 * @global mixed 
 * @param mixed $num le numÈro de version
 * @return booleanTRUE s'il faut faire la mise ‡ jour
 */
function quelle_maj($num) {
    global $gepiVersion, $gepiRcVersion, $gepiBetaVersion;
    $version_old = getSettingValue("version");
    $versionRc_old = getSettingValue("versionRc");
    $versionBeta_old = getSettingValue("versionBeta");
    if ($version_old < $num) {
        return TRUE;
        die();
    }
    if ($version_old == $num) {
        if ($gepiRcVersion > $versionRc_old) {
            return TRUE;
            die();
        }
        if ($gepiRcVersion == $versionRc_old) {
            if ($gepiBetaVersion > $versionBeta_old) {
                return TRUE;
                die();
            }
        }
    }
    return FALSE;
}

/**
 *
 * @global text
 * @return booleanTRUE si tout c'est bien passÈ 
 * @see getSettingValue()
 * @see saveSetting()
 */
function check_backup_directory() {

	global $multisite;

	$pref_multi="";
	if(($multisite=='y')&&(isset($_COOKIE['RNE']))) {
		$pref_multi=$_COOKIE['RNE']."_";
	}

    $current_backup_dir = getSettingValue("backup_directory");
    if ($current_backup_dir == NULL) {$current_backup_dir = "no_folder";}
    if (!file_exists("./backup/".$current_backup_dir)) {
        $backupDirName = NULL;
        if ($multisite != 'y') {
        	// On regarde d'abord si le rÈpertoire de backup n'existerait pas dÈj‡...
        	$handle=opendir('./backup');

        	while ($file = readdir($handle)) {
            	if (strlen($file) > 34 and is_dir('./backup/'.$file)) $backupDirName = $file;
        	}

        	closedir($handle);
        }

        if ($backupDirName != NULL) {
            // Il existe : on met simplement ‡ jour le nom du rÈpertoire...
            $update = saveSetting("backup_directory",$backupDirName);
        } else {
            // Il n'existe pas
            // On crÈe le rÈpertoire de backup
            $length = rand(35, 45);
            for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
            $dirname = $pref_multi.$r;
            $create = mkdir("./backup/" . $dirname, 0700);
            copy("./backup/index.html","./backup/".$dirname."/index.html");
            if ($create) {
                saveSetting("backup_directory", $dirname);
                saveSetting("backupdir_lastchange",time());
            } else {
                return FALSE;
                die();
            }

            // On dÈplace les Èventuels fichiers .sql dans ce nouveau rÈpertoire

            $handle=opendir('./backup');
            $tab_file = array();
            $n=0;
            while ($file = readdir($handle)) {
                if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
                and (preg_match('/sql$/',$file)) and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html') ) {
                    $tab_file[] = $file;
                    $n++;
                }
            }
            closedir($handle);
            foreach($tab_file as $filename) {
                rename("backup/".$filename, "backup/".$dirname."/".$filename);
            }
        }
    }

    // On vÈrifie la date du dernier changement, et on change le nom
    // du rÈpertoire si le dernier changement a eu lieu il y a plus de 48h
    $lastchange = getSettingValue("backupdir_lastchange");
    $current_time = time();

    // Si le dernier changement a eu lieu il y a plus de 48h, on change le nom du rÈpertoire
    if ($current_time-$lastchange > 172800) {
        $dirname = getSettingValue("backup_directory");
        $length = rand(35, 45);
        for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2) ? mt_rand(48,57):(!mt_rand(0,1)?mt_rand(65,90):mt_rand(97,122))));
        $newdirname = $pref_multi.$r;
        if (rename("./backup/".$dirname, "./backup/".$newdirname)) {
            saveSetting("backup_directory",$newdirname);
            saveSetting("backupdir_lastchange",time());
            return TRUE;
        } else {
            echo "Error during of the renaming of the folder of backup.<br />";
            return FALSE;
        }
    }
    return TRUE;

}

/**
 * Fonction qui retourne le nombre de pÈriodes pour une classe
 *
 * @param int identifiant numÈrique de la classe
 * @return int Nombre de periodes dÈfinies pour cette classe
 */
function get_period_number($_id_classe) {
    $periode_query = mysql_query("SELECT count(*) FROM periodes WHERE id_classe = '" . $_id_classe . "'");
    $nb_periode = mysql_result($periode_query, 0);
    return $nb_periode;
}

/**
 * Renvoie le numÈro et le nom de la premiËre pÈriode active pour une classe
 *
 * @param int $_id_classe identifiant unique de la classe
 * @return array numÈro de la pÈriode 'num' et son nom 'nom'
 */
function get_periode_active($_id_classe){
  $periode_query  = mysql_query("SELECT num_periode, nom_periode FROM periodes WHERE id_classe = '" . $_id_classe . "' AND verouiller = 'N'");
  $reponse        = mysql_fetch_array($periode_query);

  return $retour = array('nom' => $reponse["num_periode"], 'nom' => $reponse["nom_periode"]);

}

/**
 *  Equivalent ‡ html_entity_decode()
 * 
 * Pour les utilisateurs ayant des versions antÈrieures ‡ PHP 4.3.0 :
 * la fonction html_entity_decode() est disponible a partir de la version 4.3.0 de php.
 * 
 * @deprecated GEPI ne fonctionne plus sans php 5.2 et plus
 * @param string $string
 * @return type 
 */
function html_entity_decode_all_version ($string)
{
   global $use_function_html_entity_decode;
   if (isset($use_function_html_entity_decode) and ($use_function_html_entity_decode == 0)) {
       // Remplace les entitÈs numÈriques
       $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
       $string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);
       // Remplace les entitÈs litÈrales
       $trans_tbl = get_html_translation_table (HTML_ENTITIES);
       $trans_tbl = array_flip ($trans_tbl);
       return strtr ($string, $trans_tbl);
   } else
       return html_entity_decode($string);
}

/**
 * Cette fonction est ‡ appeler dans tous les cas o˘ une tentative
 * d'utilisation illÈgale de Gepi est manifestement avÈrÈe.
 * Elle est ‡ appeler notamment dans tous les tests de sÈcuritÈ lorsqu'un test est nÈgatif.
 * PossibilitÈ d'envoyer un mail ‡ l'administrateur et de bloquer l'utilisateur
 *
 * @global string
 * @param integer $_niveau Niveau d'intrusion enregistrÈ
 * @param string $_description Message enregistrÈ pour cette tentative
 * @see getSettingValue()
 * @see mail()
 */
function tentative_intrusion($_niveau, $_description) {

	global $gepiPath;

	// On commence par enregistrer la tentative en question

	if (!isset($_SESSION['login'])) {
		// Ici, Áa veut dire que l'attaque est extÈrieure. Il n'y a pas d'utilisateur loguÈ.
		$user_login = "-";
	} else {
		$user_login = $_SESSION['login'];
	}
	$adresse_ip = $_SERVER['REMOTE_ADDR'];
	$date = strftime("%Y-%m-%d %H:%M:%S");
	$url = parse_url($_SERVER['REQUEST_URI']);
    $fichier = substr($url['path'], strlen($gepiPath));
	$res = mysql_query("INSERT INTO tentatives_intrusion SET " .
			"login = '".$user_login."', " .
			"adresse_ip = '".$adresse_ip."', " .
			"date = '".$date."', " .
			"niveau = '".(int)$_niveau."', " .
			"fichier = '".$fichier."', " .
			"description = '".addslashes($_description)."', " .
			"statut = 'new'");

	// On a enregistrÈ.

	// On initialise des marqueurs pour les deux actions possibles : envoie d'un email ‡ l'admin
	// et blocage du compte de l'utilisateur

	$send_email = FALSE;
	$block_user = FALSE;

	// Est-ce qu'on envoie un mail quoi qu'il arrive ?
	if (getSettingValue("security_alert_email_admin") == "yes" AND $_niveau >= getSettingValue("security_alert_email_min_level")) {
		$send_email = TRUE;
	}

	// Si la tentative d'intrusion a ÈtÈ effectuÈe par un utilisateur connectÈ ‡ Gepi,
	// on regarde si des seuils ont ÈtÈ dÈpassÈs et si certaines actions doivent Ítre
	// effectuÈes.

	if ($user_login != "-") {
		// On rÈcupËre quelques infos
		$req = mysql_query("SELECT nom, prenom, statut, niveau_alerte, observation_securite FROM utilisateurs WHERE (login = '".$user_login."')");
		$user = mysql_fetch_object($req);
		// On va utiliser Áa pour gÈnÈrer automatiquement les noms de settings, Áa fait du code en moins...
		if ($user->observation_securite == "1") {
			$obs = "probation";
		} else {
			$obs = "normal";
		}

		// D'abord, on met ‡ jour le niveau cumulÈ
		$nouveau_cumul = (int)$user->niveau_alerte+(int)$_niveau;

		$res = mysql_query("UPDATE utilisateurs SET niveau_alerte = '".$nouveau_cumul ."' WHERE (login = '".$user_login."')");

		$seuil1 = FALSE;
		$seuil2 = FALSE;
		// Maintenant on regarde les seuils.
		if ($nouveau_cumul >= getSettingValue("security_alert1_".$obs."_cumulated_level")
				AND $nouveau_cumul < getSettingValue("security_alert2_".$obs."_cumulated_level")) {
			// Seuil 1
			if (getSettingValue("security_alert1_".$obs."_email_admin") == "yes") $send_email = TRUE;
			if (getSettingValue("security_alert1_".$obs."_block_user") == "yes") $block_user = TRUE;
			$seuil1 = TRUE;

		} elseif ($nouveau_cumul >= getSettingValue("security_alert2_".$obs."_cumulated_level")) {
			// Seuil 2
			if (getSettingValue("security_alert2_".$obs."_email_admin") == "yes") $send_email = TRUE;
			if (getSettingValue("security_alert2_".$obs."_block_user") == "yes") $block_user = TRUE;
			$seuil2 = TRUE;
		}

		// On dÈsactive le compte de l'utilisateur si nÈcessaire :
		if ($block_user) {
			$res = mysql_query("UPDATE utilisateurs SET etat = 'inactif' WHERE (login = '".$user_login."')");
		}
	} // Fin : if ($user_login != "-")

	// On envoie un email ‡ l'administrateur si nÈcessaire
	if ($send_email) {
		$message = "** Automatic alarm Gepi secutity **\n\n";
		$message .= "A new attempt of intrusion was detected by Gepi. The following details were recorded in the data base :\n\n";
		$message .= "Date : ".$date."\n";
		$message .= "File concerned : ".$fichier."\n";
		$message .= "Level of gravity : ".$_niveau."\n";
		$message .= "Description : ".$_description."\n\n";
		if ($user_login == "-") {
			$message .= "The attempt of intrusion was carried out by a user off-line to Gepi.\n";
			$message .= "Adresse IP : ".$adresse_ip."\n";
		} else {
			$message .= "Information on the user :\n";
			$message .= "Login : ".$user_login."\n";
			$message .= "Name : ".$user->prenom . " ".$user->nom."\n";
			$message .= "Statute : ".$user->statut."\n";
			$message .= "Score cumulated : ".$nouveau_cumul."\n\n";
			if ($seuil1) $message .= "The user exceeded the threshold of alarm 1.\n\n";
			if ($seuil2) $message .= "The user exceeded the threshold of alarm 2.\n\n";
			if ($block_user) $message .= "The account of the user was deactivated.\n";
		}

		$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
		if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}

    $subject = $gepiPrefixeSujetMail."GEPI : Alarm secutity -- Attempt of intrusion";
    $subject = "=?ISO-8859-1?B?".base64_encode($subject)."?=\r\n";

    
    $headers = "X-Mailer: PHP/" . phpversion()."\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
    $headers .= "From: Mail automatique Gepi <ne-pas-repondre@".$_SERVER['SERVER_NAME'].">\r\n";

		// On envoie le mail
		$envoi = mail(getSettingValue("gepiAdminAdress"),
		    $subject,
		    $message,
        $headers);
	}
}

/**
 * Fonction destinÈe ‡ crÈer un dossier temporaire alÈatoire /temp/<alea>
 * 
 * Test le dossier en Ècriture et le crÈe au besoin
 *
 * @return booleanTRUE si tout c'est bien passÈ
 * @see getSettingValue()
 * @see saveSetting()
 */
function check_temp_directory(){

	$dirname=getSettingValue("temp_directory");
	if(($dirname=='')||(!file_exists("./temp/$dirname"))){
		// Il n'existe pas
		// On crÈÈ le rÈpertoire temp
		$length = rand(35, 45);
		for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
		$dirname = $r;
		$create = mkdir("./temp/".$dirname, 0700);

		if ($create) {
			$fich=fopen("./temp/".$dirname."/index.html","w+");
			fwrite($fich,'<html><head><script type="text/javascript">
    document.location.replace("../../login.php")
</script></head></html>
');
			fclose($fich);

			saveSetting("temp_directory", $dirname);
			return TRUE;
		} else {
			return FALSE;
			die();
		}
	} else {
		return TRUE;
	}
}

/**
 * Fonction destinÈe ‡ crÈer un dossier /temp/<alea> propre au professeur
 * 
 * Test le dossier en Ècriture et le crÈe au besoin
 * La fonction est appelÈe depuis la racine de l'arborescence GEPI (sinon Áa peut bugger)
 *
 * @return booleanTRUE si tout c'est bien passÈ
 */
function check_user_temp_directory(){
	global $multisite;

	$pref_multi="";
	if(($multisite=='y')&&(isset($_COOKIE['RNE']))) {
		$pref_multi=$_COOKIE['RNE']."_";
	}

	$sql="SELECT temp_dir FROM utilisateurs WHERE login='".$_SESSION['login']."'";
	$res_temp_dir=mysql_query($sql);

	if(mysql_num_rows($res_temp_dir)==0){
		// Cela revient ‡ dire que l'utilisateur n'est pas dans la table utilisateurs???
		return FALSE;
	}
	else{
		$lig_temp_dir=mysql_fetch_object($res_temp_dir);
		$dirname=$lig_temp_dir->temp_dir;

		if($dirname=="") {
			// Le dossier n'existe pas
			// On crÈÈ le rÈpertoire temp
			$length = rand(35, 45);
			for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
			$dirname = $pref_multi.$_SESSION['login']."_".$r;
			$create = mkdir("./temp/".$dirname, 0700);

			if($create){
				$fich=fopen("./temp/".$dirname."/index.html","w+");
				fwrite($fich,'<html><head><script type="text/javascript">
	document.location.replace("../../login.php")
</script></head></html>
');
				fclose($fich);

				$sql="UPDATE utilisateurs SET temp_dir='$dirname' WHERE login='".$_SESSION['login']."'";
				$res_update=mysql_query($sql);
				if($res_update){
					return TRUE;
				}
				else{
					return FALSE;
				}
			}
			else{
				return FALSE;
			}
		}
		else {
			if(($pref_multi!='')&&(!preg_match("/^$pref_multi/", $dirname))&&(file_exists("./temp/".$dirname))) {
				// Il faut renommer le dossier
				if(!rename("./temp/".$dirname,"./temp/".$pref_multi.$dirname)) {
					return FALSE;
					exit();
				}
				else {
					$dirname=$pref_multi.$dirname;

					$sql="UPDATE utilisateurs SET temp_dir='$dirname' WHERE login='".$_SESSION['login']."'";
					$res_update=mysql_query($sql);
					if(!$res_update){
						return FALSE;
						exit();
					}
				}
			}

			if(!file_exists("./temp/".$dirname)){
				// Le dossier n'existe pas
				// On crÈÈ le rÈpertoire temp
				$create = mkdir("./temp/".$dirname, 0700);

				if($create){
					$fich=fopen("./temp/".$dirname."/index.html","w+");
					fwrite($fich,'<html><head><script type="text/javascript">
	document.location.replace("../../login.php")
</script></head></html>
');
					fclose($fich);
					return TRUE;
				}
				else{
					return FALSE;
				}
			}
			else{
				$fich=fopen("./temp/".$dirname."/test_ecriture.tmp","w+");
				$ecriture=fwrite($fich,'Test d Ècriture.');
				$fermeture=fclose($fich);
				if(file_exists("./temp/".$dirname."/test_ecriture.tmp")){
					unlink("./temp/".$dirname."/test_ecriture.tmp");
				}

				if(($fich)&&($ecriture)&&($fermeture)){
					return TRUE;
				}
				else{
					return FALSE;
				}
			}
		}
	}
}

/**
 * Renvoie le nom du rÈpertoire temporaire de l'utilisateur
 *
 * @return bool|string retourne FALSE s'il n'existe pas et le nom du rÈpertoire s'il existe, sans le chemin
 */
function get_user_temp_directory(){
	$sql="SELECT temp_dir FROM utilisateurs WHERE login='".$_SESSION['login']."'";
	$res_temp_dir=mysql_query($sql);
	if(mysql_num_rows($res_temp_dir)>0){
		$lig_temp_dir=mysql_fetch_object($res_temp_dir);
		$dirname=$lig_temp_dir->temp_dir;

		if(($dirname!="")&&(strlen(preg_replace("/[A-Za-z0-9_.]/","",$dirname))==0)) {
			if(file_exists("temp/".$dirname)){
				return $dirname;
			}
			else if(file_exists("../temp/".$dirname)) {
				return $dirname;
			}
			else if(file_exists("../../temp/".$dirname)) {
				return $dirname;
			}
			else{
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}
	else{
		return FALSE;
	}
}

/**
 * Retourne un nombre formatÈ en Mo, ko ou o suivant Áa taille
 *
 * @param int $volume le nombre ‡ formater
 * @return string le nombre formatÈ
 */
function volume_human($volume){
	if($volume>=1048576){
		$volume=round(10*$volume/1048576)/10;
		return $volume." Mo";
	}
	elseif($volume>=1024){
		$volume=round(10*$volume/1024)/10;
		return $volume." ko";
	}
	else{
		return $volume." o";
	}
}

/**
 * Renvoie la taille d'un rÈpertoire
 *
 * @global int 
 * @param string $dir Le rÈpertoire ‡ tester
 * @return string la taille formatÈe 
 * @see volume_dir()
 * @see volume_human()
 */
function volume_dir_human($dir){
	global $totalsize;
	$totalsize=0;

	$volume=volume_dir($dir);
	return volume_human($volume);
}

/**
 * Additionne la taille des rÈpertoires et sous-rÈpertoires
 *
 * @global int
 * @param string $dir rÈpertoire ‡ parser
 * @return int la taille totale du rÈpertoire
 */
function volume_dir($dir){
	global $totalsize;

	$handle = @opendir($dir);
	while ($file = @readdir ($handle)){
		if (preg_match("/^\.{1,2}$/i",$file))
			continue;
		if(is_dir("$dir/$file")){
			$totalsize+=volume_dir("$dir/$file");
		}
		else{
			$tabtmpsize=stat("$dir/$file");
			$size=$tabtmpsize[7];

			$totalsize+=$size;
		}
	}
	@closedir($handle);

	return($totalsize);
}

/**
 * Supprime les fichiers d'un dossier
 *
 * @param string $dir le rÈpertoire ‡ vider
 * @return boolean TRUE si tout c'est bien passÈ
 * @todo En ajoutant un paramËtre ‡ la fonction, on pourrait activer la suppression rÈcursive (avec une profondeur par exemple)
 */
function vider_dir($dir){
	$statut=TRUE;
	$handle = @opendir($dir);
	while ($file = @readdir ($handle)){
		if (preg_match("/^\.{1,2}$/i",$file)){
			continue;
		}
		if(is_dir("$dir/$file")){
			// On ne cherche pas ‡ vider rÈcursivement.
			$statut=FALSE;

			echo "<!-- DOSSIER: $dir/$file -->\n";
			// En ajoutant un paramËtre ‡ la fonction, on pourrait activer la suppression rÈcursive (avec une profondeur par exemple) lancer ici vider_dir("$dir/$file");
		}
		else{
			if(!unlink($dir."/".$file)) {
				$statut=FALSE;
				echo "<!-- Echec suppression: $dir/$file -->\n";
				break;
			}
		}
	}
	@closedir($handle);

	return $statut;
}


/**
 * Cette mÈthode prend une chaÓne de caractËres et s'assure qu'elle est bien
 * retournÈe en ISO-8859-1.
 * 
 * @param string La chaine ‡ tester
 * @return string La chaine traitÈe
 * @todo On pourrait au moins passer en ISO-8859-15
 */
function ensure_iso8859_1($str) {
	$encoding = mb_detect_encoding($str);
	if ($encoding == 'ISO-8859-1') {
		return $str;
	} else {
		return mb_convert_encoding($str, 'ISO-8859-1');
	}
}

/**
 * Encode une chaine en utf8
 * 
 * @param string $chaine La chaine ‡ tester
 * @return string La chaine traitÈe
 */
function caract_ooo($chaine){
	if(function_exists('utf8_encode')){
		$retour=utf8_encode($chaine);
	}
	else{
		$caract_accent=array("¿","‡","¬","‚","ƒ","‰","…","È","»","Ë"," ","Í","À","Î","Œ","Ó","œ","Ô","‘","Ù","÷","ˆ","Ÿ","˘","€","˚","‹","¸");
		$caract_utf8=array("√Ä","√ ","√Ç","√¢","√Ñ","√§","√â","√©","√®","√ä","√™","√ã","√´","√é","√Æ","√è","√Ø","√î","√¥","√ñ","√∂","√ô","√π","√õ","√ª","√ú","√º","u");

		$retour=$chaine;
		for($i=0;$i<count($caract_accent);$i++){
			$retour=str_replace($caract_accent[$i],$caract_utf8[$i],$retour);
		}
	}

	$caract_special=array("&",
							'"',
							"'",
							"<",
							">");

	$caract_sp_encode=array("&amp;",
							"&quot;",
							"&apos;",
							"&lt;",
							"&gt;");

	for($i=0;$i<count($caract_special);$i++){
		$retour=str_replace($caract_special[$i],$caract_sp_encode[$i],$retour);
	}

	return $retour;
}

/**
 * Correspondances de caractËres accentuÈs/dÈsaccentuÈs
 * 
 * @global string $GLOBALS['liste_caracteres_accentues']
 * @name $liste_caracteres_accentues
 */
$GLOBALS['liste_caracteres_accentues']="¬ƒ¿¡√≈« À»…ŒœÃÕ—‘÷“”’ÿ¶€‹Ÿ⁄›æ¥·‡‚‰„ÂÁÈËÍÎÓÔÏÌÒÙˆÚÛı¯®˚¸˘˙˝ˇ∏";

/**
 * Correspondances de caractËres accentuÈs/dÈsaccentuÈs
 * 
 * @global string $GLOBALS['liste_caracteres_desaccentues']
 * @name $liste_caracteres_desaccentues
 */
$GLOBALS['liste_caracteres_desaccentues']="AAAAAACEEEEIIIINOOOOOOSUUUUYYZaaaaaaceeeeiiiinooooooosuuuuyyz";

/**
 * Remplace les accents dans une chaine
 * 
 * $mode = 'all' On remplace espaces et apostrophes par des '_' et les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
 * 
 * $mode = 'all_nospace' On remplace apostrophes par des '_' et les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
 * 
 *  Sinon, on remplace les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
 *
 * @global string 
 * @global string 
 * @param type $chaine La chaine ‡ tester
 * @param type $mode Mode de conversion
 * @return type 
 */
function remplace_accents($chaine,$mode=''){
	global $liste_caracteres_accentues, $liste_caracteres_desaccentues;

	if($mode == 'all'){
		// On remplace espaces et apostrophes par des '_' et les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
		$retour=strtr(preg_replace("/∆/","AE",preg_replace("/Ê/","ae",preg_replace("/º/","OE",preg_replace("/Ω/","oe","$chaine"))))," '$liste_caracteres_accentues","__$liste_caracteres_desaccentues");
	}
	elseif($mode == 'all_nospace'){
		// On remplace apostrophes par des '_' et les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
		$retour1=strtr(preg_replace("/∆/","AE",preg_replace("/Ê/","ae",preg_replace("/º/","OE",preg_replace("/Ω/","oe","$chaine")))),"'$liste_caracteres_accentues"," $liste_caracteres_desaccentues");
		// On enlËve aussi les guillemets
		$retour = preg_replace('/"/', '', $retour1);
	}
	else {
		// On remplace les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
		$retour=strtr(preg_replace("/∆/","AE",preg_replace("/Ê/","ae",preg_replace("/º/","OE",preg_replace("/Ω/","oe","$chaine")))),"$liste_caracteres_accentues","$liste_caracteres_desaccentues");
	}
	return $retour;
}

/**
 * Cette mÈthode prend une chaÓne de caractËres et s'assure qu'elle est bien retournÈe en UTF-8
 * Attention, certain encodages sont trËs similaire et ne peuve pas Ítre thÈoriquement distinguÈ sur une chaine de caractere.
 * Si vous connaissez dÈj‡ l'encodage de votre chaine de dÈpart, il est prÈfÈrable de le prÈciser
 * 
 * @param string $str La chaine ‡ encoder
 * @param string $encoding L'encodage de dÈpart
 * @return string La chaine en utf8
 * @throws Exception si la chaine n'a pas pu Ítre encodÈe correctement
 */
function ensure_utf8($str, $from_encoding = null) {
    if ($str === null || $str === '') {
        return $str;
    } else if ($from_encoding == null && check_utf8($str)) {
	    return $str;
	}
	
    if ($from_encoding != null) {
        $encoding =  $from_encoding;
    } else {
	    $encoding = detect_encoding($str);
    }
	$result = null;
    if ($encoding !== false && $encoding != null) {
        if ($result == null && function_exists('mb_convert_encoding')) {
            $result = mb_convert_encoding($str, 'UTF-8', $encoding);
        }
    }
	if ($result === null || !check_utf8($result)) {
	    throw new Exception('Impossible de convertir la chaine vers l\'utf8');
	}
	return $result;
}


/**
 * Cette mÈthode prend une chaÓne de caractËres et teste si elle est bien encodÈe en UTF-8
 * 
 * @param string $str La chaine ‡ tester
 * @return boolean
 */
function check_utf8 ($str) {
  
    // From http://w3.org/International/questions/qa-forms-utf-8.html
    $preg_match_result = 1 == preg_match('%^(?:
          [\x09\x0A\x0D\x20-\x7E]            # ASCII
        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $str);
    
    if ($preg_match_result) {
        return true;
    } else {
        //le test preg renvoie faux, et on va vÈrifier avec d'autres fonctions
        $result = true;
        $test_done = false;
        if (function_exists('mb_check_encoding')) {
            $test_done = true;
            $result = $result && @mb_check_encoding($str, 'UTF-8');
        }
        if (function_exists('mb_detect_encoding')) {
            $test_done = true;
            $result = $result && @mb_detect_encoding($str, 'UTF-8', true);
        }
        if (function_exists('iconv')) {
            $test_done = true;
            $result = $result && ($str === (@iconv('UTF-8', 'UTF-8//IGNORE', $str)));
        }
        if (function_exists('mb_convert_encoding')) {
            $test_done = true;
            $result && ($str === @mb_convert_encoding ( @mb_convert_encoding ( $str, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32' ));
        }
        return $test_done && $result;
    }
}
    
    
/**
 * Cette mÈthode prend une chaÓne de caractËres et dÈtecte son encodage
 * 
 * @param string $str La chaine ‡ tester
 * @return l'encodage ou false si indÈtectable
 */
function detect_encoding($str) {
    //on commence par vÈrifier si c'est de l'utf8
    if (check_utf8($str)) {
        return 'UTF-8';
    }
    
    //on va commencer par tester ces encodages
    static $encoding_list = array('UTF-8', 'ISO-8859-15','windows-1251');
    foreach ($encoding_list as $item) {
        if (function_exists('iconv')) {
            $sample = @iconv($item, $item, $str);
            if (md5($sample) == md5($str)) {
                return $item;
            }
        } else if (function_exists('mb_detect_encoding')) {
            if (@mb_detect_encoding($str, $item, true)) {
                return $item;
            }
        }
    }
    
    //la mÈthode prÈcÈdente n'a rien donnÈe
    if (function_exists('mb_detect_encoding')) {
        return mb_detect_encoding($str);
    } else {
        return false;
    }
}
/**
 * Fonction qui renvoie le login d'un ÈlËve en Èchange de son ele_id
 *
 * @param int $id_eleve ele_id de l'ÈlËve
 * @return string login de l'ÈlËve
 */
function get_login_eleve($id_eleve){

	$sql = "SELECT login FROM eleves WHERE id_eleve = '".$id_eleve."'";
	$query = mysql_query($sql) OR trigger_error('Impossible to recover the login of this student.', E_USER_ERROR);
	if ($query) {
		$retour = mysql_result($query, 0,"login");
	}else{
		$retour = 'erreur';
	}
	return $retour;

}

/**
 * fonction qui renvoie le nom de la classe d'un ÈlËve pour chaque pÈriode
 *
 * @param string $ele_login login de l'ÈlËve
 * @return array Tableau des classes en fonction des pÈriodes
 */
function get_class_from_ele_login($ele_login){
	$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY periode,classe;";
	$res_class=mysql_query($sql);
	$a = 0;
	$tab_classe=array();
	if(mysql_num_rows($res_class)>0){
		$tab_classe['liste'] = "";
		$tab_classe['liste_nbsp'] = "";
		while($lig_tmp=mysql_fetch_object($res_class)){

			$tab_classe[$lig_tmp->id_classe]=$lig_tmp->classe;

			if($a>0) {$tab_classe['liste'].=", ";}
			$tab_classe['liste'].=$lig_tmp->classe;

			if($a>0) {$tab_classe['liste_nbsp'].=", ";}
			$tab_classe['liste_nbsp'].=preg_replace("/ /","&nbsp;",$lig_tmp->classe);

			$tab_classe['id'.$a] = $lig_tmp->id_classe;
			$a = $a++;
		}
	}
	return $tab_classe;
}

/**
 * Retourne les classes d'un ÈlËve ordonnÈes par pÈriodes puis classes
 *
 * @param string $ele_login Login de l'ÈlËve
 * @return array 
 */
function get_noms_classes_from_ele_login($ele_login){
	$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY periode,classe;";
	$res_class=mysql_query($sql);

	$tab_classe=array();
	if(mysql_num_rows($res_class)>0){
		while($lig_tmp=mysql_fetch_object($res_class)){
			$tab_classe[]=$lig_tmp->classe;
		}
	}
	return $tab_classe;
}

/**
 * Renvoie les ÈlËves liÈs ‡ un responsable
 *
 * @param string $resp_login Login du responsable
 * @param string $mode Si avec_classe renvoie aussi la classe
 * @return array 
 * @see get_class_from_ele_login()
 */
function get_enfants_from_resp_login($resp_login,$mode='simple'){
	$sql="SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.login='$resp_login' AND
											(r.resp_legal='1' OR r.resp_legal='2')
										ORDER BY e.nom,e.prenom;";
	$res_ele=mysql_query($sql);

	$tab_ele=array();
	if(mysql_num_rows($res_ele)>0){
		while($lig_tmp=mysql_fetch_object($res_ele)){
			$tab_ele[]=$lig_tmp->login;
			if($mode=='avec_classe') {
				$tmp_chaine_classes="";

				$tmp_tab_clas=get_class_from_ele_login($lig_tmp->login);
				if(isset($tmp_tab_clas['liste'])) {
					$tmp_chaine_classes=" (".$tmp_tab_clas['liste'].")";
				}

				$tab_ele[]=ucfirst(strtolower($lig_tmp->prenom))." ".strtoupper($lig_tmp->nom).$tmp_chaine_classes;
			}
			else {
				$tab_ele[]=ucfirst(strtolower($lig_tmp->prenom))." ".strtoupper($lig_tmp->nom);
			}
		}
	}
	return $tab_ele;
}

/**
 * Renvoie les ÈlËves liÈs ‡ un responsable
 *
 * @param string $pers_id identifiant sconet du responsable
 * @param string $mode Si avec_classe renvoie aussi la classe
 * @return array 
 * @see get_class_from_ele_login()
 */
function get_enfants_from_pers_id($pers_id,$mode='simple'){
	$sql="SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.pers_id='$pers_id' AND
											(r.resp_legal='1' OR r.resp_legal='2')
										ORDER BY e.nom,e.prenom;";
	$res_ele=mysql_query($sql);

	$tab_ele=array();
	if(mysql_num_rows($res_ele)>0){
		while($lig_tmp=mysql_fetch_object($res_ele)){
			$tab_ele[]=$lig_tmp->login;
			if($mode=='avec_classe') {
				$tmp_chaine_classes="";

				$tmp_tab_clas=get_class_from_ele_login($lig_tmp->login);
				if(isset($tmp_tab_clas['liste'])) {
					$tmp_chaine_classes=" (".$tmp_tab_clas['liste'].")";
				}

				$tab_ele[]=ucfirst(strtolower($lig_tmp->prenom))." ".strtoupper($lig_tmp->nom).$tmp_chaine_classes;
			}
			else {
				$tab_ele[]=ucfirst(strtolower($lig_tmp->prenom))." ".strtoupper($lig_tmp->nom);
			}
		}
	}
	return $tab_ele;
}

/**
 * Renvoie le statut avec des accents
 *
 * @param string $user_statut Statut ‡ corriger
 * @return string Le statut corrigÈ
 */
function statut_accentue($user_statut){
	switch($user_statut){
		case "administrateur":
			$chaine="administrateur";
			break;
		case "scolarite":
			$chaine="scolaritÈ";
			break;
		case "professeur":
			$chaine="professeur";
			break;
		case "secours":
			$chaine="secours";
			break;
		case "cpe":
			$chaine="cpe";
			break;
		case "eleve":
			$chaine="ÈlËve";
			break;
		case "responsable":
			$chaine="responsable";
			break;
		default:
			$chaine="statut inconnu";
			break;
	}
	return $chaine;
}

/**
 * Renvoie le nom d'une classe ‡ partir de son Id
 * 
 * Renvoie classes.classe
 *
 * @param type $id_classe Id de la classe
 * @return string|bool Le nom d'une classe ou FALSE
 */
function get_nom_classe($id_classe){
	$sql="SELECT classe FROM classes WHERE id='$id_classe';";
	$res_class=mysql_query($sql);

	if(mysql_num_rows($res_class)>0){
		$lig_tmp=mysql_fetch_object($res_class);
		$classe=$lig_tmp->classe;
		return $classe;
	}
	else{
		return FALSE;
	}
}

/**
 * Formate une date au format jj/mm/aa
 *
 * @param string $date
 * @return string La date formatÈe
 */
function formate_date($date) {
	$tmp_date=explode(" ",$date);
	$tab_date=explode("-",$tmp_date[0]);

	if(isset($tab_date[2])) {
		return sprintf("%02d",$tab_date[2])."/".sprintf("%02d",$tab_date[1])."/".$tab_date[0];
	}
	elseif(isset($tab_date[0])) {
		return $tab_date[0];
	}
	else {
		return $date;
	}
}

/**
 * Convertit les codes rÈgimes de Sconet
 *
 * @param int $code_regime Le code Sconet
 * @return string Le rÈgime dans GÈpi
 */
function traite_regime_sconet($code_regime){
	$premier_caractere_code_regime=substr($code_regime,0,1);
	switch($premier_caractere_code_regime){
		case "0":
			// 0       EXTERN  EXTERNE LIBRE
			return "ext.";
			break;
		case "1":
			// 1       EX.SUR  EXTERNE SURVEILLE
			return "ext.";
			break;
		case "2":
			/*
			2       DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT
			21      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 1
			22      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 2
			23      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 3
			24      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 4
			25      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 5
			26      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 6
			29      AU TIC  DEMI-PENSIONNAIRE AU TICKET
			*/
			return "d/p";
			break;
		case "3":
			/*
			3       INTERN  INTERNE DANS L'ETABLISSEMENT
			31      INT 1J  INTERNE 1 JOUR
			32      INT 2J  INTERNE 2 JOURS
			33      INT 3J  INTERNE 3 JOURS
			34      INT 4J  INTERNE 4 JOURS
			35      INT 5J  INTERNE 5 JOURS
			36      INT 6J  INTERNE 6 JOURS
			38      1/2 IN  DEMI INTERNE
			39      INT WE  INTERNE WEEK END
			*/
			return "int.";
			break;
		case "4":
			// 4       IN.EX.  INTERNE EXTERNE
			return "i-e";
			break;
		case "5":
			// 5       IN.HEB  INTERNE HEBERGE
			return "int.";
			break;
		case "6":
			// 6       DP HOR  DEMI-PENSIONNAIRE HORS L'ETABLISSEMENT
			return "d/p";
			break;
		default:
			return "ERR";
			//return "d/p";
			break;
	}
}

/**
 * Renvoie les prÈfÈrences d'un utilisateur pour un item en interrogeant la table preferences
 *
 * @param string $login Login de l'utilisateur
 * @param string $item Item recherchÈ
 * @param string $default Valeur par dÈfaut
 * @return string La valeur de l'item
 */
function getPref($login,$item,$default){
	$sql="SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	$res_prefs=mysql_query($sql);

	if(mysql_num_rows($res_prefs)>0){
		$ligne=mysql_fetch_object($res_prefs);
		return $ligne->value;
	}
	else{
		return $default;
	}
}

/**
 * Enregistre les prÈfÈrences d'un utilisateur pour un item dans la table preferences
 *
 * @param string $login Login de l'utilisateur
 * @param string $item Item recherchÈ
 * @param string $valeur Valeur ‡ enregistrer
 * @return boolean TRUE si tout c'est bien passÈ
 */
function savePref($login,$item,$valeur){
	$sql="SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	$res_prefs=mysql_query($sql);

	if(mysql_num_rows($res_prefs)>0){
		$sql="UPDATE preferences SET value='$valeur' WHERE login='$login' AND name='$item';";
	}
	else{
		$sql="INSERT INTO preferences SET login='$login', name='$item', value='$valeur';";
	}
	$res=mysql_query($sql);
	if($res) {return TRUE;} else {return FALSE;}
}

/**
 * Position horizontale initiale pour permettre un affichage sans superposition
 *
 * @global int $GLOBALS['$posDiv_infobulle']
 * @name $posDiv_infobulle
 */
$GLOBALS['$posDiv_infobulle'] = 0;

/**
 * 
 * @global array $GLOBALS['tabid_infobulle']
 * @name $tabid_infobulle
 */
$GLOBALS['tabid_infobulle'] = array();

/**
 * 
 * @global string $GLOBALS['unite_div_infobulle']
 * @name $unite_div_infobulle
 */
$GLOBALS['unite_div_infobulle'] = '';

/**
 * Les infobulles ne sont pas dÈcallÈes si ‡ oui
 * 
 * @global string $GLOBALS['pas_de_decalage_infobulle']
 * @name $pas_de_decalage_infobulle
 */
$GLOBALS['pas_de_decalage_infobulle'] = '';

/**
 * Ajoute un argument aux classes du div
 * 
 * @global string $GLOBALS['class_special_infobulle']
 * @name $class_special_infobulle
 */
$GLOBALS['class_special_infobulle'] = '';

/**
 * $bg_titre: Si $bg_titre est vide, on utilise la couleur par dÈfaut correspondant ‡ .infobulle_entete (dÈfini dans style.css et Èventuellement modifiÈ dans style_screen_ajout.css)
 * 
 * $bg_texte: Si $bg_texte est vide, on utilise la couleur par dÈfaut correspondant ‡ .infobulle_corps (dÈfini dans style.css et Èventuellement modifiÈ dans style_screen_ajout.css)
 * 
 * $hauteur: En mettant 0, on laisse le DIV s'adapter au contenu (se rÈduire/s'ajuster)
 * 
 * $bouton_close: S'il est affichÈ, c'est dans la barre de titre. Si la barre de titre n'est pas affichÈe, ce bouton ne peut pas Ítre affichÈ.
		
 * 
 * @global type 
 * @global array 
 * @global type 
 * @global type 
 * @global type 
 * @global type 
 * @param string $id Identifiant du DIV conteneur
 * @param string $titre Texte du titre du DIV
 * @param string $bg_titre Couleur de fond de la barre de titre.
 * @param string $texte Texte du contenu du DIV
 * @param string $bg_texte Couleur de fond du DIV contenant le texte
 * @param int $largeur Largeur du DIV conteneur
 * @param int $hauteur Hauteur (minimale) du DIV conteneur
 * @param string $drag 'y' ou 'n' pour rendre le DIV draggable
 * @param string $bouton_close 'y' ou 'n' pour afficher le bouton Close
 * @param string $survol_close 'y' ou 'n' pour refermer le DIV automatiquement lorsque le survol quitte le DIV
 * @param string $overflow 'y' ou 'n' activer l'overflow automatique sur la partie Texte. Il faut que $hauteur soit non NULLe
 * @param int $zindex_infobulle 
 * @return string 
 */
function creer_div_infobulle($id,$titre,$bg_titre,$texte,$bg_texte,$largeur,$hauteur,$drag,$bouton_close,$survol_close,$overflow,$zindex_infobulle=1){
	/*	
		
		$overflow:		
	*/
	global $posDiv_infobulle;
	global $tabid_infobulle;
	global $unite_div_infobulle;
	global $niveau_arbo;
	global $pas_de_decalage_infobulle;
	global $class_special_infobulle;

	$style_box="color: #000000; border: 1px solid #000000; padding: 0px; position: absolute; z-index:$zindex_infobulle;";
	
	$style_bar="color: #ffffff; cursor: move; font-weight: bold; padding: 0px;";
	$style_close="color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;";

	// On fait la liste des identifiants de DIV pour cacher les Div avec javascript en fin de chargement de la page (dans /lib/footer.inc.php).
	$tabid_infobulle[]=$id;

	// Conteneur:
	if($bg_texte==''){
		$div="<div id='$id' class='infobulle_corps";
		if((isset($class_special_infobulle))&&($class_special_infobulle!='')) {$div.=" ".$class_special_infobulle;}
		$div.="' style='$style_box width: ".$largeur.$unite_div_infobulle."; ";
	}
	else{
		$div="<div id='$id' ";
		if((isset($class_special_infobulle))&&($class_special_infobulle!='')) {$div.="class='".$class_special_infobulle."' ";}
		$div.="style='$style_box background-color: $bg_texte; width: ".$largeur.$unite_div_infobulle."; ";
	}
	if($hauteur!=0){
		$div.="height: ".$hauteur.$unite_div_infobulle."; ";
	}
	// Position horizontale initiale pour permettre un affichage sans superposition si Javascript est dÈsactivÈ:
	$div.="left:".$posDiv_infobulle.$unite_div_infobulle.";";
	$div.="'>\n";


	// Barre de titre:
	// Elle n'est affichÈe que si le titre est non vide
	if($titre!=""){
		if($bg_titre==''){
			$div.="<div class='infobulle_entete' style='$style_bar width: ".$largeur.$unite_div_infobulle.";'";
		}
		else{
			$div.="<div style='$style_bar background-color: $bg_titre; width: ".$largeur.$unite_div_infobulle.";'";
		}
		if($drag=="y"){
			// L‡ on utilise les fonctions de http://www.brainjar.com stockÈes dans brainjar_drag.js
			$div.=" onmousedown=\"dragStart(event, '$id')\"";
		}
		$div.=">\n";

		if($bouton_close=="y"){
			//$div.="<div style='$style_close'><a href='#' onclick=\"cacher_div('$id');return FALSE;\">";
			$div.="<div style='$style_close'><a href=\"javascript:cacher_div('$id')\">";
			if(isset($niveau_arbo)&&$niveau_arbo==0){
				$div.="<img src='./images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
			else if(isset($niveau_arbo)&&$niveau_arbo==1) {
				$div.="<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
			else if(isset($niveau_arbo)&&$niveau_arbo==2) {
				$div.="<img src='../../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
      else {
				$div.="<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
      }
			$div.="</a></div>\n";
		}
		$div.="<span style='padding-left: 1px;'>\n";
		$div.=$titre."\n";
		$div.="</span>\n";
		$div.="</div>\n";
	}


	// Partie texte:
	//==================
	// 20110113
	$div.="<div id='".$id."_contenu_corps'";
	//==================
	if($survol_close=="y"){
		// On referme le DIV lorsque la souris quitte la zone de texte.
		$div.=" onmouseout=\"cacher_div('$id');\"";
	}
	$div.=">\n";
	if(($overflow=='y')&&($hauteur!=0)){
		$hauteur_hors_titre=$hauteur-1;
		$div.="<div style='width: ".$largeur.$unite_div_infobulle."; height: ".$hauteur_hors_titre.$unite_div_infobulle."; overflow: auto;'>\n";
		$div.="<div style='padding-left: 1px;'>\n";
		$div.=$texte;
		$div.="</div>\n";
		$div.="</div>\n";
	}
	else{
		$div.="<div style='padding-left: 1px;'>\n";
		$div.=$texte;
		$div.="</div>\n";
	}
	$div.="</div>\n";

	$div.="</div>\n";

	// Les div vont s'afficher cÙte ‡ cÙte sans superposition en bas de page si JavaScript est dÈsactivÈ:
	if (isset($pas_de_decalage_infobulle) AND $pas_de_decalage_infobulle == "oui") {
		// on ne dÈcale pas les div des infobulles
		$posDiv_infobulle = $posDiv_infobulle;
	}else{
		$posDiv_infobulle = $posDiv_infobulle+$largeur;
	}

	return $div;
}

/**
 * tableau des variables transmises d'une page ‡ l'autre
 * 
 * @global array $GLOBALS['debug_var_count']
 * @name $debug_var_count
 */
$GLOBALS['debug_var_count']=array();

/**
 * indice de la variable transmise
 * 
 * @global int $GLOBALS['cpt_debug_debug_var']
 * @name $cpt_debug_debug_var
 */
$GLOBALS['cpt_debug_debug_var']=0;

/**
 * Affiche les variables transmises d'une page ‡ l'autre: GET, POST, SERVER et SESSION
 *
 * @global array
 * @global int
 */
$debug_var_count=array();
$cpt_debug_debug_var=0;
function debug_var() {
	global $debug_var_count;
	global $cpt_debug_debug_var;

	$debug_var_count['POST']=0;
	$debug_var_count['GET']=0;
	$debug_var_count['SESSION']=0;
	$debug_var_count['SERVER']=0;

	$debug_var_count['COOKIE']=0;

	$debug_var_count['FILES']=0;

	// Fonction destinÈe ‡ afficher les variables transmises d'une page ‡ l'autre: GET, POST et SESSION
	echo "<div style='border: 1px solid black; background-color: white; color: black;'>\n";

	$cpt_debug_debug_var=0;

	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p><strong>Variables transmises en POST, GET, SESSION,...</strong> (<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)</p>\n";

	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;

	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyÈes en POST: ";
	if(count($_POST)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;

	echo "<script type='text/javascript'>
	tab_etat_debug_var=new Array();
	function affiche_debug_var(id,mode) {
		if(document.getElementById(id)) {
			if(mode==1) {
				document.getElementById(id).style.display='';
			}
			else {
				document.getElementById(id).style.display='none';
			}
		}
	}
</script>\n";


/**
 * Affiche un tableau des valeurs de GET, POST, SERVER ou SESSION
 *
 * @global int 
 * @global array 
 * @param type $chaine_tab_niv1
 * @param type $tableau
 * @param type $pref_chaine 
 */
	function tab_debug_var($chaine_tab_niv1,$tableau,$pref_chaine) {
		global $cpt_debug_debug_var;
		global $debug_var_count;

		echo " (<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)\n";

		echo "<table id='container_debug_var_$cpt_debug_debug_var' summary=\"Tableau de debug\">\n";
		foreach($tableau as $post => $val) {
			echo "<tr><td valign='top'>".$pref_chaine."['".$post."']=</td><td>".$val;

			if(is_array($tableau[$post])) {
				$cpt_debug_debug_var++;

				tab_debug_var($chaine_tab_niv1,$tableau[$post],$pref_chaine.'['.$post.']');

				$cpt_debug_debug_var++;
			}
			elseif(isset($debug_var_count[$chaine_tab_niv1])) {
				$debug_var_count[$chaine_tab_niv1]++;
			}

			echo "</td></tr>\n";
		}
		echo "</table>\n";
	}


	echo "<table summary=\"Table of debug\">\n";
	foreach($_POST as $post => $val) {
		echo "<tr><td valign='top'>\$_POST['".$post."']=</td><td>".$val;

		if(is_array($_POST[$post])) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
			tab_debug_var('POST',$_POST[$post],'$_POST['.$post.']');

			$cpt_debug_debug_var++;
		}
		else {
			$debug_var_count['POST']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Number of values in POST: <b>".$debug_var_count['POST']."</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables sent in GET: ";
	if(count($_GET)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Table of debug on GET\">";
	foreach($_GET as $get => $val){
		
		echo "<tr><td valign='top'>\$_GET['".$get."']=</td><td>".$val;

		if(is_array($_GET[$get])) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
			tab_debug_var('GET',$_GET[$get],'$_GET['.$get.']');

			$cpt_debug_debug_var++;
		}
		else {
			$debug_var_count['GET']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Number of values in GET: <b>".$debug_var_count['GET']."</b></p>\n";

	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables sent in SESSION: ";
	if(count($_SESSION)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Table of debug on SESSION\">";
	foreach($_SESSION as $variable => $val){
		
		echo "<tr><td valign='top'>\$_SESSION['".$variable."']=</td><td>".$val;
		if(is_array($_SESSION[$variable])) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
			tab_debug_var('SESSION',$_SESSION[$variable],'$_SESSION['.$variable.']');

			$cpt_debug_debug_var++;
		}
		else {
			$debug_var_count['SESSION']++;
		}
		echo "</td></tr>\n";

	}
	echo "</table>\n";

	echo "<p>Number of values in SESSION: <b>".$debug_var_count['SESSION']."</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables sent in SERVER: ";
	if(count($_SERVER)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Table of debug on SERVER\">";
	foreach($_SERVER as $variable => $valeur){
		echo "<tr><td>\$_SERVER['".$variable."']=</td><td>".$valeur."</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Number of values in SERVER: <b>".$debug_var_count['SERVER']."</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables sent in FILES: ";
	if((!isset($_FILES))||(count($_FILES)==0)) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	if((isset($_FILES))&&(count($_FILES)>0)) {
		echo "<blockquote>\n";
		echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
		$cpt_debug_debug_var++;

		echo "<table summary=\"Table of debug\">\n";
		foreach($_FILES as $key => $val) {
			echo "<tr><td valign='top'>\$_FILES['".$key."']=</td><td>".$val;
	
			if(is_array($_FILES[$key])) {
				echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
				tab_debug_var('FILES',$_FILES[$key],'$_FILES['.$key.']');
	
				$cpt_debug_debug_var++;
			}
	
			echo "</td></tr>\n";
		}
		echo "</table>\n";
	
		echo "<p>Number of values in FILES: <b>".$debug_var_count['FILES']."</b></p>\n";
		echo "</div>\n";
		echo "</blockquote>\n";
	}

	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables COOKIES: ";
	if(count($_COOKIE)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Table of debug on COOKIE\">";
	foreach($_COOKIE as $variable => $val){

		echo "<tr><td valign='top'>\$_COOKIE['".$variable."']=</td><td>".$val;

		if(is_array($val)) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
			tab_debug_var('COOKIE',$val,'$_COOKIE['.$variable.']');

			$cpt_debug_debug_var++;
		}
		else {
			$debug_var_count['COOKIE']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<script type='text/javascript'>
	// On masque le cadre de debug au chargement:
	//affiche_debug_var('container_debug_var',var_debug_var_etat);

	//for(i=0;i<tab_etat_debug_var.length;i++) {
	for(i=0;i<$cpt_debug_debug_var;i++) {
		if(document.getElementById('container_debug_var_'+i)) {
			affiche_debug_var('container_debug_var_'+i,-1);
		}
		// Variable destinÈe ‡ alterner affichage/masquage
		tab_etat_debug_var[i]=-1;
	}
</script>\n";

	echo "</div>\n";
	echo "</div>\n";
}

/**
 *permet de vÈrifier si tel statut peut avoir accËs ‡ l'EdT en fonction des settings de l'admin
 * 
 * @param string $statut Statut testÈ
 * @return string yes si peut avoir accËs ‡ l'EdT, no sinon
 * @see getSettingValue()
 */
function param_edt($statut){
		$verif = "";
	if ($statut == "administrateur") {
		$verif = getSettingValue("autorise_edt_admin");
	} elseif ($statut == "professeur" OR $statut == "scolarite" OR $statut == "cpe" OR $statut == "secours" OR $statut == "autre") {
		$verif = getSettingValue("autorise_edt_tous");
	} elseif ($statut = "eleve" OR $statut = "responsable") {
		$verif = getSettingValue("autorise_edt_eleve");
	} else {
		$verif = "";
	}
	// On vÈrifie $verif et on renvoie le return
	if ($verif == "y" or $verif == "yes") {
		return "yes";
	} else {
		return "no";
	}
}

/**
 * Renvoie le nom de la photo de l'ÈlËve ou du prof
 *
 * Renvoie NULL si :
 *
 * - le module trombinoscope n'est pas activÈ
 * - la photo n'existe pas.
 *
 * @param string $_elenoet_ou_login selon les cas, soit l'elenoet de l'ÈlËve soit le login du professeur
 * @param string $repertoire "eleves" ou "personnels"
 * @param int $arbo niveau d'aborescence (1 ou 2).
 * @return string Le chemin vers la photo ou NULL
 * @see getSettingValue()
 */
function nom_photo($_elenoet_ou_login,$repertoire="eleves",$arbo=1) {
	if ($arbo==2) {$chemin = "../";} else {$chemin = "";}
	if (($repertoire != "eleves") and ($repertoire != "personnels")) {
		return NULL;
		die();
	}
	if (getSettingValue("active_module_trombinoscopes")!='y') {
		return NULL;
		die();
	}
		$photo=NULL;

	// En multisite, on ajoute le rÈpertoire RNE
	if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		  // On rÈcupËre le RNE de l'Ètablissement
      $repertoire2=$_COOKIE['RNE']."/";
	}else{
	  $repertoire2="";
	}

	// Cas des ÈlËves
	if ($repertoire == "eleves") {
	  
	  if($_elenoet_ou_login!='') {

		// on vÈrifie si la photo existe

		if(file_exists($chemin."../photos/".$repertoire2."eleves/".$_elenoet_ou_login.".jpg")) {
			$photo=$chemin."../photos/".$repertoire2."eleves/".$_elenoet_ou_login.".jpg";
		}
		else if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y')
		{
		  // En multisite, on recherche aussi avec les logins
		  if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			// On rÈcupËre le login de l'ÈlËve
			$sql = 'SELECT login FROM eleves WHERE elenoet = "'.$_elenoet_ou_login.'"';
			$query = mysql_query($sql);
			$_elenoet_ou_login = mysql_result($query, 0,'login');
		  }

		  if(file_exists($chemin."../photos/".$repertoire2."eleves/$_elenoet_ou_login.jpg")) {
				$photo=$chemin."../photos/".$repertoire2."eleves/$_elenoet_ou_login.jpg";
			}
			else {
				if(file_exists($chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",$_elenoet_ou_login).".jpg")) {
					$photo=$chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",$_elenoet_ou_login).".jpg";
				} else {
					for($i=0;$i<5;$i++){
						if(substr($_elenoet_ou_login,$i,1)=="0"){
							$test_photo=substr($_elenoet_ou_login,$i+1);
							if(($test_photo!='')&&(file_exists($chemin."../photos/".$repertoire2."eleves/".$test_photo.".jpg"))) {
								$photo=$chemin."../photos/".$repertoire2."eleves/".$test_photo.".jpg";
								break;
							}
						}
					}
				}
			}

		}

	  }
	}
	// Cas des non-ÈlËves
	else {

		$_elenoet_ou_login = md5(strtolower($_elenoet_ou_login));
			if(file_exists($chemin."../photos/".$repertoire2."personnels/$_elenoet_ou_login.jpg")){
				$photo=$chemin."../photos/".$repertoire2."personnels/$_elenoet_ou_login.jpg";
			} else {
				$photo = NULL;
		}
	}
	return $photo;
}


/**
 * Le message ‡ afficher
 * 
 * @global string $GLOBALS['themessage']
 * @name $themessage
 */
$GLOBALS['themessage'] = '';

/**
 * Affiche un fenÍtre de confirmation via javascript
 * 
 * Ajoute un attribut onclick ‡ une balise pour appeler une fonction javascript contenant le message
 *
 * @global string
 * @return  string l'attribut onclick ou vide
 */
function insert_confirm_abandon(){
	global $themessage;

	if(isset($themessage)) {
		if($themessage!="") {
			return " onclick=\"return confirm_abandon(this, change, '$themessage')\" ";
		}
		else{
			return "";
		}
	}
	else{
		return "";
	}
}

/**
 * Largeur maximum dÈsirÈe
 * 
 * @global int $GLOBALS['photo_largeur_max']
 * @name $photo_largeur_max
 */
$GLOBALS['photo_largeur_max'] = 0;

/**
 * Hauteur maximum dÈsirÈe;
 * 
 * @global int $GLOBALS['photo_hauteur_max']
 * @name $photo_hauteur_max
 */
$GLOBALS['photo_hauteur_max'] = 0;

/**
 * Redimensionne une image
 *
 * @global int 
 * @global int 
 * @param string $photo l'adresse de la photo
 * @return array Les nouvelles dimensions de l'image (largeur, hauteur)
 */
function redimensionne_image2($photo){
	global $photo_largeur_max, $photo_hauteur_max;

	// prendre les informations sur l'image
	$info_image=getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur=$info_image[0];
	$hauteur=$info_image[1];

	// calcule le ratio de redimensionnement
	$ratio_l=$largeur/$photo_largeur_max;
	$ratio_h=$hauteur/$photo_hauteur_max;
	$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

	// dÈfinit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur=round($largeur/$ratio);
	$nouvelle_hauteur=round($hauteur/$ratio);

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

/**
 * Enregistre les calculs de moyennes dans un fichier
 * 
 * Passer ‡ 1 la variable $debug pour gÈnÈrer un fichier de debug...
 *
 * @param string $texte Le calcul ‡ enregistrer
 * @see get_user_temp_directory()
 */
function calc_moy_debug($texte){
	$debug=0;
	if($debug==1){
		$tmp_dir=get_user_temp_directory();
		if((!$tmp_dir)||(!file_exists("../temp/".$tmp_dir))) {$tmp_dir="/tmp";} else {$tmp_dir="../temp/".$tmp_dir;}
		$fich=fopen($tmp_dir."/calc_moy_debug.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}

/**
 * Renvoie le nom d'une classe ‡ partir de son Id
 *
 * @param int $id_classe Id de la classe recherchÈe
 * @return type nom de la classe (classe.classes)
 */
function get_class_from_id($id_classe) {
	$sql="SELECT classe FROM classes c WHERE id='$id_classe';";
	$res_class=mysql_query($sql);

	if(mysql_num_rows($res_class)>0){
		$lig_tmp=mysql_fetch_object($res_class);
		$classe=$lig_tmp->classe;
		return $classe;
	}
	else{
		return FALSE;
	}
}




/*
function fdebug_mail_connexion($texte){
	// Passer la variable ‡ "y" pour activer le remplissage du fichier de debug pour calcule_moyenne()
	$local_debug="n";
	if($local_debug=="y") {
		$fich=fopen("/tmp/mail_connexion.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}
*/

/**
 *
 * @global string  
 */
function mail_connexion() {
	global $active_hostbyaddr;

	$test_envoi_mail=getSettingValue("envoi_mail_connexion");

	//$date = strftime("%Y-%m-%d %H:%M:%S");
	//$date = ucfirst(strftime("%A %d-%m-%Y ‡ %H:%M:%S"));
	//fdebug_mail_connexion("\$_SESSION['login']=".$_SESSION['login']."\n\$test_envoi_mail=$test_envoi_mail\n\$date=$date\n====================\n");

	if($test_envoi_mail=="y") {
		$user_login = $_SESSION['login'];

		$sql="SELECT nom,prenom,email FROM utilisateurs WHERE login='$user_login';";
		$res_user=mysql_query($sql);
		if (mysql_num_rows($res_user)>0) {
			$lig_user=mysql_fetch_object($res_user);

			$adresse_ip = $_SERVER['REMOTE_ADDR'];
			$date = ucfirst(strftime("%A %d-%m-%Y ‡ %H:%M:%S"));

			if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
				$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
			}
			else if($active_hostbyaddr == "no_local") {
				if ((substr($adresse_ip,0,3) == 127) or (substr($adresse_ip,0,3) == 10.) or (substr($adresse_ip,0,7) == 192.168)) {
					$result_hostbyaddr = "";
				}
				else{
					$tabip=explode(".",$adresse_ip);
					if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
						$result_hostbyaddr = "";
					}
					else{
						$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
					}
				}
			}
			else{
				$result_hostbyaddr = "";
			}

			$message = "** Mall Gepi connection **\n\n";
			$message .= "\n";
			$message .= "You (*) are connected to GEPI :\n\n";
			$message .= "Identity                : ".strtoupper($lig_user->nom)." ".ucfirst(strtolower($lig_user->prenom))."\n";
			$message .= "Login                   : ".$user_login."\n";
			$message .= "Date                    : ".$date."\n";
			$message .= "Origin of connection : ".$adresse_ip."\n";
			if($result_hostbyaddr!="") {
				$message .= "Address IP solved in   : ".$result_hostbyaddr."\n";
			}
			$message .= "\n";
			$message .= "This message, if it reaches you whereas you did not connect yourselves to the date/hour indicated, is likely to indicate that your identity could be usurped.\nYou should control your data, change your password and to inform the administrator (and/or administration of the school) so that it can take suitable measurements.\n";
			$message .= "\n";
			$message .= "(*) You or a person trying to usurp your identity.\n";

			// On envoie le mail
			//fdebug_mail_connexion("\$message=$message\n====================\n");
			$destinataire=$lig_user->email;
			$sujet="GEPI : Connexion $date";
			envoi_mail($sujet, $message, $destinataire);
		}
	}
}

/**
 * Envoi un courriel ‡ un utilisateur en cas de connexion avec son compte
 *
 * @global string
 * @param string $sujet Sujet du message
 * @param string $texte Texte du message
 * @param type $informer_admin Envoi aussi un courriel ‡ l'administrateur si pas ‡ 'n'
 * @see envoi_mail()
 * @see getSettingValue()
 */
function mail_alerte($sujet,$texte,$informer_admin='n') {
	global $active_hostbyaddr;

	$user_login = $_SESSION['login'];

	$sql="SELECT nom,prenom,email FROM utilisateurs WHERE login='$user_login';";
	$res_user=mysql_query($sql);
	if (mysql_num_rows($res_user)>0) {
		$lig_user=mysql_fetch_object($res_user);

		$adresse_ip = $_SERVER['REMOTE_ADDR'];
		//$date = strftime("%Y-%m-%d %H:%M:%S");
		$date = ucfirst(strftime("%A %d-%m-%Y ‡ %H:%M:%S"));
		//$url = parse_url($_SERVER['REQUEST_URI']);

		if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
			$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
		}
		else if($active_hostbyaddr == "no_local") {
			if ((substr($adresse_ip,0,3) == 127) or (substr($adresse_ip,0,3) == 10.) or (substr($adresse_ip,0,7) == 192.168)) {
				$result_hostbyaddr = "";
			}
			else{
				$tabip=explode(".",$adresse_ip);
				if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
					$result_hostbyaddr = "";
				}
				else{
					$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
				}
			}
		}
		else{
			$result_hostbyaddr = "";
		}


		//$message = "** Mail connexion Gepi **\n\n";
		$message=$texte;
		$message .= "\n";
		$message .= "You (*) are connected to GEPI :\n\n";
		$message .= "Identity                : ".strtoupper($lig_user->nom)." ".ucfirst(strtolower($lig_user->prenom))."\n";
		$message .= "Login                   : ".$user_login."\n";
		$message .= "Date                    : ".$date."\n";
		$message .= "Origin of connection : ".$adresse_ip."\n";
		if($result_hostbyaddr!="") {
			$message .= "Address IP solved in   : ".$result_hostbyaddr."\n";
		}
		$message .= "\n";
		$message .= "This message, if it reaches you whereas you did not connect yourselves to the date/hour indicated, is likely to indicate that your identity could be usurped.\nYou should control your data, change your password and inform the administrator (and/or administration of the school) so that it can take suitable measurements.\n";
		$message .= "\n";
		$message .= "(*) You or a person trying to usurp your identity.\n";

		$ajout="";
		if(($informer_admin!='n')&&(getSettingValue("gepiAdminAdress")!='')) {
			$ajout="Bcc: ".getSettingValue("gepiAdminAdress")."\r\n";
		}

		// On envoie le mail
		//fdebug_mail_connexion("\$message=$message\n====================\n");

		$destinataire=$lig_user->email;
		$sujet="GEPI : $sujet $date";
		envoi_mail($sujet, $message, $destinataire, $ajout);

	}
}

/**
 * Formate un texte
 * 
 * - Si le texte contient des < et >, on affiche tel quel
 * - Sinon, on transforme les retours ‡ la ligne en <br />
 *
 * @param string $texte Le texte ‡ formater
 * @return string Le texte formatÈ
 */
function texte_html_ou_pas($texte){
	if((strstr($texte,">"))||(strstr($texte,"<"))){
		$retour=$texte;
	}
	else{
		$retour=nl2br($texte);
	}
	return $retour;
}

/**
 * Activer le mode debug, "y" pour oui
 *
 * @global string $GLOBALS['debug']
 * @name $debug
 */
$GLOBALS['debug'] = '';

/**
 * 
 *
 * @global array $GLOBALS['tab_instant']
 * @name $tab_instant
 */
$GLOBALS['tab_instant'] = array();

/**
 * 
 *
 * @global array
 * @global string
 * @param type $motif
 * @param string $texte 
 */
function decompte_debug($motif,$texte) {
	global $tab_instant, $debug;
	if($debug=="y") {
		$instant=microtime();
		if(isset($tab_instant[$motif])) {
			$tmp_tab1=explode(" ",$instant);
			$tmp_tab2=explode(" ",$tab_instant[$motif]);
			if($tmp_tab1[1]!=$tmp_tab2[1]) {
				$diff=$tmp_tab1[1]-$tmp_tab2[1];
			}
			else {
				$diff=$tmp_tab1[0]-$tmp_tab2[0];
			}
				echo "<p style='color:green;'>$texte: ".$diff." s</p>\n";
		}
		else {
				echo "<p style='color:green;'>$texte</p>\n";
		}
		$tab_instant[$motif]=$instant;
	}
}

 
/**
 * Retourne l'URI des ÈlËves pour les flux rss
 *
 * @global string
 * @param string $eleve Login de l'ÈlËve
 * @param string $https La page est-elle sÈcurisÈe ? en https si 'y'
 * @param string $type 'cdt' ou ''
 * @return string
 * @see getSettingValue()
 */
function retourneUri($eleve, $https, $type){

	global $gepiPath;
	$rep = array();

	// on vÈrifie que la table en question existe dÈj‡
	$test_table = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'rss_users'"));
	if ($test_table >= 1) {

		$sql = "SELECT user_uri FROM rss_users WHERE user_login = '".$eleve."' LIMIT 1";
		$query = mysql_query($sql);
		$nbre = mysql_num_rows($query);
		if ($nbre == 1) {
			$uri = mysql_fetch_array($query);
			if ($https == 'y') {
				$web = 'https://';
			}else{
				$web = 'http://';
			}
			if ($type == 'cdt') {
				$rep["uri"] = $web.$_SERVER["SERVER_NAME"].$gepiPath.'/class_php/syndication.php?rne='.getSettingValue("gepiSchoolRne").'&amp;ele_l='.$_SESSION["login"].'&amp;type=cdt&amp;uri='.$uri["user_uri"];
				$rep["text"] = $web.$_SERVER["SERVER_NAME"].$gepiPath.'/class_php/syndication.php?rne='.getSettingValue("gepiSchoolRne").'&amp;ele_l='.$_SESSION["login"].'&amp;type=cdt&amp;uri='.$uri["user_uri"];
			}

		}else{
			$rep["text"] = 'erreur1';
			$rep["uri"] = '#';
		}
	}else{

		$rep["text"] = 'Ask your administrator to generate the URI.';
		$rep["uri"] = '#';

	}

	return $rep;
}

/**
 * Met une date en franÁais
 *
 * @return text La date formatÈe 
 */
function get_date_php() {
	$eng_words = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$french_words = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche', 'Janvier', 'FÈvrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao˚t', 'Septembre', 'Octobre', 'Novembre', 'DÈcembre');
	$date_str = date('l').' '.date('d').' '.date('F').' '.date('Y');
	$date_str = str_replace($eng_words, $french_words, $date_str);
	return $date_str;
}

/**
 * Met en forme un prÈnom
 *
 * @param type $prenom Le prÈnom ‡ traiter
 * @return type Le prÈnom traitÈ
 */
function casse_prenom($prenom) {
	$tab=explode("-",$prenom);

	$retour="";
	for($i=0;$i<count($tab);$i++) {
		if($i>0) {
			$retour.="-";
		}
		$tab[$i]=ucwords(strtolower($tab[$i]));
		$retour.=$tab[$i];
	}

	return $retour;
}

/**
 * Drapeau pour encoder un texte en utf8 si ‡ "y"
 *
 * @global string  $GLOBALS['mode_utf8_pdf']
 * @name $mode_utf8_pdf
 */
$GLOBALS['mode_utf8_pdf'] = '';

/**
 * Encode une chaine en utf8 si $mode_utf8_pdf="y"
 *
 * @global type 
 * @param type $chaine Chaine ‡ encoder
 * @return type 
 */
function traite_accents_utf8($chaine) {
	global $mode_utf8_pdf;
	if($mode_utf8_pdf=="y") {
		return utf8_encode($chaine);
	}
	else {
		return $chaine;
	}
}

/**
 * Arrondi un nombre avec un certain nombre de chiffres aprËs la virgule
 *
 * @param type $nombre Le nombre ‡ convertir
 * @param type $nb_chiffre_apres_virgule
 * @return decimal Le nombre arrondi
 */
function nf($nombre,$nb_chiffre_apres_virgule=1) {
	// Formatage des nombres
	// Precision:
	// Pour Ítre s˚r d'avoir un entier
	$nb_chiffre_apres_virgule=floor($nb_chiffre_apres_virgule);
	if($nb_chiffre_apres_virgule<1) {
		$precision=0.1;
		$nb_chiffre_apres_virgule=0;
	}
	else {
		$precision=pow(10,-1*$nb_chiffre_apres_virgule);
	}

	if(($nombre=='')||($nombre=='-')) {
		$valeur=$nombre;
	}
	else {
		$nombre=strtr($nombre,",",".");
		$valeur=number_format(round($nombre/$precision)*$precision, $nb_chiffre_apres_virgule, ',', '');
	}
	return $valeur;
}



/**
 * Envoit les informations de debug dans un fichier si ‡ 'fichier', vers l'Ècran sinon
 *
 * @global string $GLOBALS['mode_my_echo_debug']
 * @name $mode_my_echo_debug
 */
$GLOBALS['mode_my_echo_debug'] = '';

/**
 * …crit les informations de debug si ‡ 1
 *
 * @global int $GLOBALS['my_echo_debug']
 * @name $my_echo_debug
 */
$GLOBALS['my_echo_debug'] = NULL;

/**
 * Ecrit des informations de debug dans un fichier ou ‡ l'Ècran
 * 
 * $dossier est ‡ "/tmp" pour simplifier en debug sur une machine perso sous *nix,
 * Commenter la ligne au besoin
 * 
 * @global string 
 * @global int 
 * @global int
 * @param string $texte 
 * @see get_user_temp_directory()
 */
function my_echo_debug($texte) {
	global $mode_my_echo_debug, $my_echo_debug, $niveau_arbo;

	if($my_echo_debug==1) {
		if($mode_my_echo_debug!='fichier') {
			echo $texte;
		}
		else {
			$tempdir=get_user_temp_directory();
			if (isset($niveau_arbo) and ($niveau_arbo == "0")) {
				$points=".";
			}
			elseif (isset($niveau_arbo) and ($niveau_arbo == "2")) {
				$points="../..";
			}
			else {
				$points="..";
			}
			$dossier=$points."/temp/".$tempdir;

			// Pour simplifier en debug sur une machine perso sous *nix:
			$dossier="/tmp";

			$fichier=$dossier."/my_echo_debug_".date("Ymd_Hi").".txt";

			$f=fopen($fichier,"a+");
			fwrite($f,$texte);
			fclose($f);
		}
	}
}

/**
 * Retourne une chaine avec la bonne casse
 * 
 * $mode
 * - 'maj'   -> tout en majuscules
 * - 'min'   -> tout en minuscules
 * - 'majf'  -> PremiËre lettre en majuscule
 * - 'majf2' -> PremiËre lettre de tous les mots en majuscule
 *
 * @param type $mot chaine ‡ modifier
 * @param type $mode Mode de conversion
 * @return type chaine mise en forme
 */
function casse_mot($mot,$mode='maj') {
	if($mode=='maj') {
		return strtr(strtoupper($mot),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ");
	}
	elseif($mode=='min') {
		return strtr(strtolower($mot),"ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ","‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯");
	}
	elseif($mode=='majf') {
		if(strlen($mot)>1) {
			return strtr(strtoupper(substr($mot,0,1)),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ").strtr(strtolower(substr($mot,1)),"ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ","‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯");
		}
		else {
			return strtr(strtoupper($mot),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ");
		}
	}
	elseif($mode=='majf2') {
		$chaine="";
		$tab=explode(" ",$mot);
		for($i=0;$i<count($tab);$i++) {
			if($i>0) {$chaine.=" ";}
			$tab2=explode("-",$tab[$i]);
			for($j=0;$j<count($tab2);$j++) {
				if($j>0) {$chaine.="-";}
				if(strlen($tab2[$j])>1) {
					$chaine.=strtr(strtoupper(substr($tab2[$j],0,1)),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ").strtr(strtolower(substr($tab2[$j],1)),"ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ","‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯");
				}
				else {
					$chaine.=strtr(strtoupper($tab2[$j]),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ");
				}
			}
		}
		return $chaine;
	}
}

/**
 * Renvoie le nom et le prÈnom d'un ÈlËve
 * 
 * ou civilitÈ nom prÈnom (non-ÈlËve) si ce n'est pas un ÈlËve
 *
 * @param string $login_ele Login de l'ÈlËve
 * @param string $mode si 'avec_classe' on retourne aussi la(les) classe(s)
 * @return string 
 * @see civ_nom_prenom()
 * @see get_class_from_ele_login()
 * @see casse_mot()
 */
function get_nom_prenom_eleve($login_ele,$mode='simple') {
	$sql="SELECT nom,prenom FROM eleves WHERE login='$login_ele';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		// Si ce n'est pas un ÈlËve, c'est peut-Ítre un utilisateur prof, cpe, responsable,...
		$sql="SELECT 1=1 FROM utilisateurs WHERE login='$login_ele';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			return civ_nom_prenom($login_ele)." (non-ÈlËve)";
		}
		else {
			return "Unknown student ($login_ele)";
		}
	}
	else {
		$lig=mysql_fetch_object($res);

		$ajout="";
		if($mode=='avec_classe') {
			$tmp_tab_clas=get_class_from_ele_login($login_ele);
			if((isset($tmp_tab_clas['liste']))&&($tmp_tab_clas['liste']!='')) {
				$ajout=" (".$tmp_tab_clas['liste'].")";
			}
		}

		return casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2').$ajout;
	}
}

/**
 * Retourne une commune ‡ partir de son code insee
 * 
 * $mode :
 * - 0 -> la commune
 * - 1 -> la commune (<em>le dÈpartement</em>)
 * - 2 -> la commune (le dÈpartement)
 * 
 * @param string $code_commune_insee
 * @param int $mode
 * @return string La commune
 */
function get_commune($code_commune_insee,$mode){
	$retour="";

	if(strstr($code_commune_insee,'@')) {
		// On a affaire ‡ une commune ÈtrangËre
		$tmp_tab=explode('@',$code_commune_insee);
		$sql="SELECT * FROM pays WHERE code_pays='$tmp_tab[0]';";
		//echo "$sql<br />";
		$res_pays=mysql_query($sql);
		if(mysql_num_rows($res_pays)==0) {
			$retour=stripslashes($tmp_tab[1])." ($tmp_tab[0])";
		}
		else {
			$lig_pays=mysql_fetch_object($res_pays);
			$retour=stripslashes($tmp_tab[1])." (".$lig_pays->nom_pays.")";
		}
	}
	else {
		$sql="SELECT * FROM communes WHERE code_commune_insee='$code_commune_insee';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			if($mode==0) {
				$retour=$lig->commune;
			}
			elseif($mode==1) {
				$retour=$lig->commune." (<em>".$lig->departement."</em>)";
			}
			elseif($mode==2) {
				$retour=$lig->commune." (".$lig->departement.")";
			}
		}
	}
	return $retour;
}

/**
 * Renvoi civilite nom prÈnom d'un utilisateur
 *
 * @param string $login Login de l'utilisateur recherchÈ
 * @param string $mode si 'prenom' inverse le nom et le prÈnom
 * @return string civilite nom prÈnom de l'utilisateur
 */
function civ_nom_prenom($login,$mode='prenom') {
	$retour="";
	$sql="SELECT nom,prenom,civilite FROM utilisateurs WHERE login='$login';";
	$res_user=mysql_query($sql);
	if (mysql_num_rows($res_user)>0) {
		$lig_user=mysql_fetch_object($res_user);
		if($lig_user->civilite!="") {
			$retour.=$lig_user->civilite." ";
		}
		if($mode=='prenom') {
			$retour.=strtoupper($lig_user->nom)." ".ucfirst(strtolower($lig_user->prenom));
		}
		else {
			// Initiale
			$retour.=strtoupper($lig_user->nom)." ".strtoupper(substr($lig_user->prenom,0,1));
		}
	}
	return $retour;
}

/**
 *Enleve le numÈro des titres numÈrotÈs ("1. Titre" -> "Titre")
 * 
 * Exemple :  "12. Titre"  donne "Titre"
 * @param string $texte Le titre de dÈpart
 * @return string  Le titre formatÈ
 */
function supprimer_numero($texte) {
 return preg_replace(",^[[:space:]]*([0-9]+)([.)])[[:space:]]+,S","", $texte);
}


/**
 * Teste si style_screen_ajout.css existe et est accessible en Ècriture
 *
 * @return boolean TRUE si on peut Ècrire dans le fichier
 */
function test_ecriture_style_screen_ajout() {
	$nom_fichier='style_screen_ajout.css';
	$f=@fopen("../".$nom_fichier, "a+");
	if($f) {
		$ecriture=fwrite($f, "/* Test of writing in $nom_fichier */\n");
		fclose($f);
		if($ecriture) {return TRUE;} else {return FALSE;}
	}
	else {
		return FALSE;
	}
}


/**********************************************************************************************
 *                                  Fonctions Trombinoscope
 **********************************************************************************************/

/**
 * CrÈe les rÈpertoires photos/RNE_Etablissement, photos/RNE_Etablissement/eleves et
 * photos/RNE_Etablissement/personnels s'ils n'existent pas
 * @return boolean TRUE si tout se passe bien ou FALSE si la crÈation d'un rÈpertoire Èchoue
 * @see getSettingValue()
 */
function cree_repertoire_multisite() {
  if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		// On rÈcupËre le RNE de l'Ètablissement
	if (!$repertoire=$_COOKIE['RNE'])
	  return FALSE;
	//on vÈrifie que le dossier photos/RNE_Etablissement n'existe pas
	if (!is_dir("../photos/".$repertoire)){
	  // On crÈe le rÈpertoire photos/RNE_Etablissement
	  if (!mkdir("../photos/".$repertoire, 0700))
		return FALSE;
	  // On enregistre un fichier index.html dans photos/RNE_Etablissement
	  if (!copy  (  "../photos/index.html"  ,  "../photos/".$repertoire."/index.html" ))
		return FALSE;
	}
	//on vÈrifie que le dossier photos/RNE_Etablissement/eleves n'existe pas
	if (!is_dir("../photos/".$repertoire."/eleves")){
	  // On crÈe le rÈpertoire photos/RNE_Etablissement/eleves
	  if (!mkdir("../photos/".$repertoire."/eleves", 0700))
		return FALSE;
	  // On enregistre un fichier index.html dans photos/RNE_Etablissement/eleves
	  if (!copy  (  "../photos/index.html"  ,  "../photos/".$repertoire."/eleves/index.html" ))
		return FALSE;
	 }
	//on vÈrifie que le dossier photos/RNE_Etablissement/personnels n'existe pas
	if (!is_dir("../photos/".$repertoire."/personnels")){
	  // On crÈe le rÈpertoire photos/RNE_Etablissement/personnels
	  if (!mkdir("../photos/".$repertoire."/personnels", 0700))
		return FALSE;
	  // On enregistre un fichier index.html dans photos/RNE_Etablissement/personnels
	  if (!copy  (  "../photos/index.html"  ,  "../photos/".$repertoire."/personnels/index.html" ))
		return FALSE;
	  }
	}
	return TRUE;
}

/**
 * Recherche les ÈlËves sans photos
 *
 * @return array tableau de login - nom - prÈnom - classe - classe court - eleonet
 * @see nom_photo()
 */
function recherche_eleves_sans_photo() {
  $eleve=NULL;
  $requete_liste_eleve = "SELECT e.elenoet, e.login, e.nom, e.prenom, c.nom_complet, c.classe
	FROM eleves e, j_eleves_classes jec, classes c
	WHERE e.login = jec.login
	AND jec.id_classe = c.id
	GROUP BY e.login
	ORDER BY id_classe, nom, prenom ASC";
  $res_eleve = mysql_query($requete_liste_eleve);
  while ($row = mysql_fetch_object($res_eleve)) {
	$nom_photo = nom_photo($row->elenoet);
	if (!($nom_photo and file_exists($nom_photo))) {
	  $eleve[]=$row;
	}
  }
  return $eleve;
}

/**
 *
 * @param string $statut statut recherchÈ
 * @return array tableau des personnels sans photo ou NULL
 * @see nom_photo()
 */
function recherche_personnel_sans_photo($statut='professeur') {
  $personnel=NULL;
  $requete_liste_personnel = "SELECT login,nom,prenom FROM utilisateurs u
	WHERE u.statut='".$statut."' AND u.etat='actif' 
	ORDER BY nom, prenom ASC";
  $res_personnel = mysql_query($requete_liste_personnel);
  while ($row = mysql_fetch_object($res_personnel)) {
	$nom_photo = nom_photo($row->login,"personnels");
	if (!($nom_photo and file_exists($nom_photo))) {
	  $personnel[]=$row;
	}
  }
  return $personnel;
}

/**
 * Efface le dossier photo passÈ en argument
 * @param string $photos le dossier ‡ effacer personnels ou eleves
 * @return string L'Ètat de la suppression
 * @see cree_zip_archive()
 * @see getSettingValue()
 */
function efface_photos($photos) {
// on liste les fichier du dossier photos/personnels ou photos/eleves
  if (!($photos=="eleves" || $photos=="personnels"))
	return ("The folder <strong>".$photos."</strong> is not valid.");
  if (cree_zip_archive("photos")==TRUE){
	$fichier_sup=array();
	if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		  // On rÈcupËre le RNE de l'Ètablissement
	  if (!$repertoire=$_COOKIE['RNE'])
		return ("Error during recovery of the folder school.");
	} else {
	  $repertoire="";
	}
	$folder = "../photos/".$repertoire.$photos."/";
	$dossier = opendir($folder);
	while ($Fichier = readdir($dossier)) {
	  if ($Fichier != "." && $Fichier != ".." && $Fichier != "index.html") {
		$nomFichier = $folder."".$Fichier;
		$fichier_sup[] = $nomFichier;
	  }
	}
	closedir($dossier);
	if(count($fichier_sup)==0) {
	  return ("The folder <strong>".$folder."</strong> does not contain photograph.") ;
	} else {
	  foreach ($fichier_sup as $fic_efface) {
		if(file_exists($fic_efface)) {
		  @unlink($fic_efface);
		  if(file_exists($fic_efface)) {
			return ("The file  <strong>".$fic_efface."</strong> could not be unobtrusive.");
		  }
		}
	  }
	  unset ($fic_efface);
	  return ("The folder <strong>".$folder."</strong> was emptied.") ;
	}
  }else{
	return ("Error during creation of the file.") ;
  }

}

/**********************************************************************************************
 *                               Fin Fonctions Trombinoscope
 **********************************************************************************************/

/**********************************************************************************************
 *                                   Fil d'Ariane
 **********************************************************************************************/
/**
 * gestion du fil d'ariane en remplissant le tableau $_SESSION['ariane']
 * @param string $lien page atteinte par le lien
 * @param string $texte texte ‡ afficher dans le fil d'ariane
 * @return boolean True si tout s'est bien passÈ, False sinon
 */
function suivi_ariane($lien,$texte){
  if (!isset($_SESSION['ariane'])){
	$_SESSION['ariane']['lien'][] =$lien;
	$_SESSION['ariane']['texte'][] =$texte;
	return TRUE;
  }else{
	$trouve=FALSE;
	foreach ($_SESSION['ariane']['lien'] as $index=>$lienActuel){
	  if ($trouve){
		unset ($_SESSION['ariane']['lien'][$index]);
		unset ($_SESSION['ariane']['texte'][$index]);
	  }else{
		if ($lienActuel==$lien)
		  $trouve=TRUE;
	  }
	}
	unset ($index, $lienActuel);
	if (!$trouve){
	  $_SESSION['ariane']['lien'][] =$lien;
	  $_SESSION['ariane']['texte'][] =$texte;
	}
	  return TRUE;
  }
}

/**
 * Affiche le fil d'Ariane
 * 
 * une validation sera demandÈe en cas de modification de la page si validation est ‡ TRUE 
 * et si le javascript est activÈ
 * @param <boolean> $validation validation si TRUE,
 * @param <texte> $themessage message ‡ afficher lors de la confirmation
 */
function affiche_ariane($validation= FALSE,$themessage="" ){
  if (isset($_SESSION['ariane'])){
	echo "<p class='ariane'>";
	foreach ($_SESSION['ariane']['lien'] as $index=>$lienActuel){
	  if ($index!="0"){
		echo " &gt;&gt; ";
	  }
	  if ($validation){
	  echo "<a class='bold' href='".$lienActuel."' onclick='return confirm_abandon (this, change, \"".$themessage."\")' >";
	  } else {
	  echo "<a class='bold' href='".$lienActuel."' >";
	  }
		echo $_SESSION['ariane']['texte'][$index] ;
	  echo " </a>";
	}
	unset ($index,$lienActuel);
	echo "</p>";
  }
}
/**********************************************************************************************
 *                               Fin Fil d'Ariane
 **********************************************************************************************/
/**********************************************************************************************
 *                               Manipulation de fichiers
 **********************************************************************************************/

/**
 * Renvoie le chemin relatif pour remonter ‡ la racine du site
 * @param int $niveau niveau dans l'arborescence
 * @return string chemin relatif vers la racine
 */
function path_niveau($niveau=1){
  switch ($niveau) {
	case 0:
	  $path = "./";
		  break;
	case 1:
	  $path = "../";
		  break;
	case 2:
	  $path = "../../";
	default:
	  $path = "../";
  }
  return $path;
}

/**
 * CrÈe une archive Zip des dossiers documents ou photos
 *
 * @param string $dossier_a_archiver limitÈ ‡ documents ou photos
 * @param int $niveau niveau dans l'arborescence de la page appelante, racine = 0
 * @return boolean
 */
function cree_zip_archive($dossier_a_archiver,$niveau=1) {
  $path = path_niveau();
  $dirname = "backup/".getSettingValue("backup_directory")."/";
  define( 'PCLZIP_TEMPORARY_DIR', $path.$dirname );
  require_once($path.'lib/pclzip.lib.php');

  if (isset($dossier_a_archiver)) {
	$suffixe_zip="_le_".date("Y_m_d_\a_H\hi");
	switch ($dossier_a_archiver) {
	case "documents":
	  $chemin_stockage = $path.$dirname."_cdt".$suffixe_zip.".zip"; //l'endroit o˘ sera stockÈe l'archive
	  $dossier_a_traiter = $path.'documents/'; //le dossier ‡ traiter
	  $dossier_dans_archive = 'documents'; //le nom du dossier dans l'archive crÈÈe
	  break;
	case "photos":
	  $chemin_stockage = $path.$dirname."_photos".$suffixe_zip.".zip";
	  $dossier_a_traiter = $path.'photos/'; //le dossier ‡ traiter
	  if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		$dossier_a_traiter .=$_COOKIE['RNE']."/";
	  }
	  $dossier_dans_archive = 'photos'; //le nom du dossier dans l'archive crÈer
	  break;
	default:
	  $chemin_stockage = '';
	}

	if ($chemin_stockage !='') {
	  $archive = new PclZip($chemin_stockage);
	  $v_list = $archive->create($dossier_a_traiter,
			  PCLZIP_OPT_REMOVE_PATH,$dossier_a_traiter,
			  PCLZIP_OPT_ADD_PATH, $dossier_dans_archive);
	  if ($v_list == 0) {
		 die("Error : ".$archive->errorInfo(TRUE));
		return FALSE;
	  }else {
		return TRUE;
	  }
	}
  }
}

/**
 * DÈplace un fichier de $source vers $dest
 * @param string $source : emplacement du fichier ‡ dÈplacer
 * @param string $dest : Nouvel emplacement du fichier
 * @return bool
 */
function deplacer_upload($source, $dest) {
    $ok = @copy($source, $dest);
    if (!$ok) $ok = (@move_uploaded_file($source, $dest));
    return $ok;
}

/**
 * TÈlÈcharge un fichier dans $dirname aprËs avoir nettoyer son nom 
 * 
 * si tout se passe bien :
 * $sav_file['name']=my_ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $sav_file['name'])
 * @param array $sav_file tableau de type $_FILE["nom_du_fichier"]
 * @param string $dirname
 * @return string ok ou message d'erreur
 * @see deplacer_upload()
 */
function telecharge_fichier($sav_file,$dirname,$ext="",$type=""){
  if (!isset($sav_file['tmp_name']) or ($sav_file['tmp_name'] =='')) {
	return ("Error of download.");
  } else if (!file_exists($sav_file['tmp_name'])) {
	return ("Error of download 2.");
  } else if (($ext!="") && (!preg_match('/'.$ext.'$/i',$sav_file['name']))){
	return ("Error : only files having the extension .".$ext." are authorized.");
  //} else if ($sav_file['type']!=$type ){
  } else if (($type!="") && (strripos($type,$sav_file['type'])===false)) {
	return ("Error : only files of the type '".$type."' are authorized<br />Your file is of type ".$sav_file['type']);
  } else {
	$nom_corrige = preg_replace("/[^.a-zA-Z0-9_=-]+/", "_", $sav_file['name']);
	if (!deplacer_upload($sav_file['tmp_name'], $dirname."/".$nom_corrige)) {
	  return ("Problem of transfer : the file could not be transferred on the repertory ".$dirname);
	} else {
	  $sav_file['name']=$nom_corrige;
	  return ("ok");
	}
  }
}

/**
 * Extrait une archive Zip
 * @param string $fichier le nom du fichier ‡ dÈzipper
 * @param string $repertoire le rÈpertoire de destination
 * @param int $niveau niveau dans l'arborescence de la page appelante
 * @return string ok ou message d'erreur
 */
function dezip_PclZip_fichier($fichier,$repertoire,$niveau=1){
  $path = path_niveau();
  require_once($path.'lib/pclzip.lib.php');
  $archive = new PclZip($fichier);
  //if ($archive->extract() == 0) {
if ($archive->extract(PCLZIP_OPT_PATH, $repertoire) == 0) {
	return "An error was met during extraction of the file zip";
  }else {
	return "ok";
  }
}

/**********************************************************************************************
 *                              Fin Manipulation de fichiers
 **********************************************************************************************/
/**
 * VÈrifie qu'un statut ‡ les droits sur une page
 *
 * @param string $id le lien vers la page ‡ tester
 * @param string $statut Le statut ‡ tester
 * @return int  
 */
function check_droit_acces($id,$statut) {
    $tab_id = explode("?",$id);
    $query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
    $droit = @mysql_result($query_droits, 0, $statut);
    if ($droit == "V") {
        return "1";
    } else {
        return "0";
    }
}

/**
 * Renvoie des balises option contenant les ÈlËves
 * 
 * Renvoie une chaine contenant une balise option par ÈlËve ‡ insÈrer dans un select
 *
 * @param int $id_classe Id de la classe
 * @param string $login_eleve_courant Login de l'ÈlËve qui sera sÈlectionnÈ par dÈfaut
 * @param request $sql_ele requÍte ‡ utiliser
 * @return string Les balises options
 */
function lignes_options_select_eleve($id_classe,$login_eleve_courant,$sql_ele="") {
	if($sql_ele!="") {
		$sql=$sql_ele;
	}
	else {
		$sql="SELECT DISTINCT jec.login,e.nom,e.prenom FROM j_eleves_classes jec, eleves e
							WHERE jec.login=e.login AND
								jec.id_classe='$id_classe'
							ORDER BY e.nom,e.prenom";
	}
	//echo "$sql<br />";
	//echo "\$login_eleve=$login_eleve<br />";
	$res_ele_tmp=mysql_query($sql);
	$chaine_options_login_eleves="";
	$cpt_eleve=0;
	$num_eleve=-1;
	if(mysql_num_rows($res_ele_tmp)>0){
		$login_eleve_prec=0;
		$login_eleve_suiv=0;
		$temoin_tmp=0;
		while($lig_ele_tmp=mysql_fetch_object($res_ele_tmp)){
			if($lig_ele_tmp->login==$login_eleve_courant){
				$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login' selected='TRUE'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
	
				$num_eleve=$cpt_eleve;
	
				$temoin_tmp=1;
				if($lig_ele_tmp=mysql_fetch_object($res_ele_tmp)){
					$login_eleve_suiv=$lig_ele_tmp->login;
					$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
				}
				else{
					$login_eleve_suiv=0;
				}
			}
			else{
				$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
			}
	
			if($temoin_tmp==0){
				$login_eleve_prec=$lig_ele_tmp->login;
			}
			$cpt_eleve++;
		}
	}

	return $chaine_options_login_eleves;
}

/**
 *VÈrifie si un utilisateur est prof principal (gepi_prof_suivi)
 * 
 * $id_classe : identifiant de la classe (si vide, on teste juste si le prof est PP 
 * (Èventuellement pour un ÈlËve particulier si login_eleve est non vide))
 * 
 * $login_eleve : login de l'ÈlËve ‡ tester (si vide, on teste juste si le prof est PP 
 * (Èventuellement pour la classe si id_classe est non vide))
 * 
 * @param type $login_prof login de l'utilisateur ‡ tester
 * @param type $id_classe identifiant de la classe
 * @param type $login_eleve login de l'ÈlËve
 * @return boolean 
 */
function is_pp($login_prof,$id_classe="",$login_eleve="") {
	$retour=FALSE;
	if($login_eleve=='') {
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE ";
		if($id_classe!="") {$sql.="id_classe='$id_classe' AND ";}
		$sql.="professeur='$login_prof';";
	}
	else {
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE ";
		if($id_classe!="") {$sql.="id_classe='$id_classe' AND ";}
		$sql.="professeur='$login_prof' AND login='$login_eleve';";
	}
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {$retour=TRUE;}

	return $retour;
}

/**
 * VÈrifie qu'un utilisateur a le droit de voir la page en lien
 *
 * @param string $id l'adresse de la page telle qu'enregistrÈe dans la table droits
 * @param string $statut le statut de l'utilisateur
 * @return entier 1 si l'utilisateur a le droit de voir la page 0 sinon
 * @todo Je l'ai dÈj‡ vu au-dessus dans le fichier
 */
function acces($id,$statut) 
{ 
	if ($_SESSION['statut']!='autre') {
		$tab_id = explode("?",$id);
		$query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
		$droit = @mysql_result($query_droits, 0, $statut);
		if ($droit == "V") {
			return "1";
		} else {
			return "0";
		}
	} else {
		$sql="SELECT ds.autorisation FROM `droits_speciaux` ds,  `droits_utilisateurs` du
					WHERE (ds.nom_fichier='".$id."'
						AND ds.id_statut=du.id_statut
						AND du.login_user='".$_SESSION['login']."');" ;
		$result=mysql_query($sql);
		if (!$result) {
			return FALSE;
		} else {
			$row = mysql_fetch_row($result) ;
			if ($row[0]=='V' || $row[0]=='v'){
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
}

/**
 * VÈrifie que le dossier (et ses sous-dossiers) contient bien un fichier index.html
 *
 * @global int
 * @param string $dossier Le dossier
 * @return string Un message formatÈ
 */
function ajout_index_sous_dossiers($dossier) {
	global $niveau_arbo;

	$nb_creation=0;
	$nb_erreur=0;
	$nb_fich_existant=0;

	$retour="";

	//$dossier="../documents";
	$dir= opendir($dossier);
	if(!$dir) {
		$retour.="<p style='color:red'>Error during access to the folder '$dossier'.</p>\n";
	}
	else {
		$retour.="<p style='color:green'>Success of the access to the folder '$dossier'.</p>\n";
		while($entree=@readdir($dir)) {
			if(is_dir($dossier.'/'.$entree)&&($entree!='.')&&($entree!='..')) {
				if(!file_exists($dossier."/".$entree."/index.html")) {
					if ($f = @fopen($dossier.'/'.$entree."/index.html", "w")) {
						if((!isset($niveau_arbo))||($niveau_arbo==1)) {
							@fputs($f, '<script type="text/javascript">document.location.replace("../login.php")</script>');
						}
						elseif($niveau_arbo==0) {
							@fputs($f, '<script type="text/javascript">document.location.replace("./login.php")</script>');
						}
						elseif($niveau_arbo==2) {
							@fputs($f, '<script type="text/javascript">document.location.replace("../../login.php")</script>');
						}
						else {
							@fputs($f, '<script type="text/javascript">document.location.replace("../../../login.php")</script>');
						}
						@fclose($f);
						$nb_creation++;
					}
					else {
						$retour.="<span style='color:red'>Error during creation of '$dir/$entree/index.html'.</span><br />\n";
						$nb_erreur++;
					}
				}
				else {
					$nb_fich_existant++;
				}
			}
		}

		if($nb_erreur>0) {
			$retour.="<p style='color:red'>$nb_erreur error(s) during the treatment.</p>\n";
		}
		else {
			$retour.="<p style='color:green'>No error during creation of the files index.html</p>\n";
		}
	
		if($nb_creation>0) {
			$retour.="<p style='color:green'>Creation of $nb_creation file(s) index.html</p>\n";
		}
		else {
			$retour.="<p style='color:green'>No creation of files index.html was carried out.</p>\n";
		}
		$retour.="<p style='color:blue'>There existed before the operation $nb_fich_existant file(s) index.html</p>\n";
	}

	return $retour;
}

// MÈthode pour envoyer les en-tÍtes HTTP nÈcessaires au tÈlÈchargement de fichier.
// Le content-type est obligatoire, ainsi que le nom du fichier.
/**
 * MÈthode pour envoyer les en-tÍtes HTTP nÈcessaires au tÈlÈchargement de fichier.
 * 
 * Le content-type est obligatoire, ainsi que le nom du fichier.
 * @param string $content_type type Mime
 * @param string $filename Nom du fichier
 * @param type $content_disposition Content-Disposition 'attachment' par dÈfaut
 */
function send_file_download_headers($content_type, $filename, $content_disposition = 'attachment') {

  //header('Content-Encoding: utf-8');
  header('Content-Type: '.$content_type);
  header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  header('Content-Disposition: '.$content_disposition.'; filename="' . $filename . '"');
  
  // Contournement d'un bug IE lors d'un tÈlÈchargement en HTTPS...
  if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)) {
    header('Pragma: private');
    header('Cache-Control: private, must-revalidate');
  } else {
    header('Pragma: no-cache');
  }
}

/**
 * Enregistrer une action ‡ effectuer pour qu'elle soit par la suite affichÈe en page d'accueil pour tels ou tels utilisateurs
 *
 * @param string $titre titre de l'action/info
 * @param string $description le dÈtail de l'action ‡ effectuer avec autant que possible un lien vers la page et paramËtres utiles pour l'action
 * @param string $destinataire le tableau des login ou statuts des utilisateurs pour lesquels l'affichage sera rÈalisÈ
 * @param string $mode vaut 'individu' si $destinataire dÈsigne des logins et 'statut' si ce sont des statuts
 * @return int|boolean Id de l'enregistrement s'est bien effectuÈ FALSE sinon
 *
 *
 */
function enregistre_infos_actions($titre,$texte,$destinataire,$mode) {
	if(is_array($destinataire)) {
		$tab_dest=$destinataire;
	}
	else {
		$tab_dest=array($destinataire);
	}

	$sql="INSERT INTO infos_actions SET titre='".addslashes($titre)."', description='".addslashes($texte)."', date=NOW();";
	$insert=mysql_query($sql);
	if(!$insert) {
		return FALSE;
	}
	else {
		$id_info=mysql_insert_id();
		$return=$id_info;
		for($loop=0;$loop<count($tab_dest);$loop++) {
			$sql="INSERT INTO infos_actions_destinataires SET id_info='$id_info', nature='$mode', valeur='$tab_dest[$loop]';";
			$insert=mysql_query($sql);
			if(!$insert) {
				$return=FALSE;
			}
		}

		return $return;
	}
}

/**
 * Supprime une action ‡ effectuer de la base
 *
 * @param type $id_info Id de l'action a effacer de la base
 * @return boolean TRUE si l'action a ÈtÈ effacÈe de la base 
 */
function del_info_action($id_info) {
	// Dans le cas des infos destinÈes ‡ un statut... c'est le premier qui supprime qui vire pour tout le monde?
	// S'il s'agit bien de loguer des actions ‡ effectuer... elle ne doit Ítre effectuÈe qu'une fois.
	// Ou alors il faudrait ajouter des champs pour marquer les actions comme effectuÈes et n'afficher par dÈfaut que les actions non effectuÈes

	$sql="SELECT 1=1 FROM infos_actions_destinataires WHERE id_info='$id_info' AND ((nature='statut' AND valeur='".$_SESSION['statut']."') OR (nature='individu' AND valeur='".$_SESSION['login']."'));";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		$sql="DELETE FROM infos_actions_destinataires WHERE id_info='$id_info';";
		$del=mysql_query($sql);
		if(!$del) {
			return FALSE;
		}
		else {
			$sql="DELETE FROM infos_actions WHERE id='$id_info';";
			$del=mysql_query($sql);
			if(!$del) {
				return FALSE;
			}
			else {
				return TRUE;
			}
		}
	}
}

/**
 * affiche sous la forme JJ/MM/AAAA la date de sortie d'un ÈlËve 
 * prÈsente dans la base comme un timestamp
 *
 * @param date $date_sortie date (timestamp)
 * @return string La date formatÈe 
 */
function affiche_date_sortie($date_sortie) {
	//
    $eleve_date_de_sortie_time=strtotime($date_sortie);
	//rÈcupÈration du jour, du mois et de l'annÈe
	$eleve_date_sortie_jour=date('j', $eleve_date_de_sortie_time); 
	$eleve_date_sortie_mois=date('m', $eleve_date_de_sortie_time);
	$eleve_date_sortie_annee=date('Y', $eleve_date_de_sortie_time); 
	return $eleve_date_sortie_jour."/".$eleve_date_sortie_mois."/".$eleve_date_sortie_annee;
}

/**
 * Traite une chaine de caractËres JJ/MM/AAAA vers un timestamp AAAA-MM-JJ 00:00:00
 * 
 * @param string $date_sortie date (JJ/MM/AAAA)
 * @return date date (timestamp)
 */
function traite_date_sortie_to_timestamp($date_sortie) {
	//
	$date=explode("/", $date_sortie);
	$jour = $date[0];
	$mois = $date[1];
	$annee = $date[2];

	return $annee."-".$mois."-".$jour." 00:00:00"; 
}

/**
 * Supprime les accËs au cahier de textes
 *
 * @param int $id_acces Id du cahier de texte
 * @return boolean TRUE si tout c'est bien passÈ 
 */
function del_acces_cdt($id_acces) {

	$sql="SELECT * FROM acces_cdt WHERE id='$id_acces';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);

		$chemin=preg_replace("#/index.(html|php)#","",$lig->chemin);
		if((!preg_match("#^documents/acces_cdt_#",$chemin))||(strstr($chemin,".."))) {
			echo "<p><span style='color:red'>Chemin $chemin invalide</span></p>";
			return FALSE;
		}
		else {
                  if ((isset($GLOBALS['multisite']))&&($GLOBALS['multisite'] == 'y')){
                    $test = explode("?", $chemin);
                    $chemin = count($test) > 1 ? $test[0] : $chemin;
                  }
			$suppr=deltree($chemin,TRUE);
			if(!$suppr) {
				echo "<p><span style='color:red'>Error during suppression of $chemin</span></p>";
				return FALSE;
			}
			else {
				$sql="DELETE FROM acces_cdt_groupes WHERE id_acces='$id_acces';";
				$del=mysql_query($sql);
				if(!$del) {
					echo "<p><span style='color:red'>Error during suppression of the groups associated to the access n∞$id_acces</span></p>";
					return FALSE;
				}
				else {
					$sql="DELETE FROM acces_cdt WHERE id='$id_acces';";
					$del=mysql_query($sql);
					if(!$del) {
						echo "<p><span style='color:red'>Error during suppression of the access n∞$id_acces</span></p>";
						return FALSE;
					}
					else {
						return TRUE;
					}
				}
			}
		}
	}
}

//=======================================================
// Fonction rÈcupÈrÈe dans /mod_ooo/lib/lib_mod_ooo.php

/**
 * Supprime une arborescence
 * 
 * Retourne TRUE si tout s'est bien passÈ,
 * FALSE si un fichier est restÈ (problËme de permission ou attribut lecture sous Win.
 * Dans tous les cas, le maximum possible est supprimÈ.
 * @staticvar int $niv niveau dans l'arborescence
 * @param string $rep Le rÈpertoire de dÈpart
 * @param boolean $repaussi TRUE ~> efface aussi $rep
 * @return boolean TRUE si tout s'est bien passÈ
 */
function deltree($rep,$repaussi=TRUE) {
	static $niv=0;
	$niv++;
	if (!is_dir($rep)) {return FALSE;}
	$handle=opendir($rep);
	if (!$handle) {return FALSE;}
	while ($entree=readdir($handle)) {
		if (is_dir($rep.'/'.$entree)) {
			if ($entree!='.' && $entree!='..') {
				$ok=deltree($rep.'/'.$entree);
			}
			else {$ok=TRUE;}
		}
		else {
			$ok=@unlink($rep.'/'.$entree);
		}
	}
	closedir($handle);
	$niv--;
	if ($niv || $repaussi) $ok &= @rmdir($rep);
	return $ok;
}
//=======================================================


/**
 *
 * @param type $email
 * @param type $mode
 * @return boolean  
 */
function check_mail($email,$mode='simple') {
	if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $email)) {
		return FALSE;
	}
	else {
		if(($mode=='simple')||(!function_exists('checkdnsrr'))) {
			return TRUE;
		}
		else {
			$tab=explode('@', $email);
			if(checkdnsrr($tab[1], 'MX')) {return TRUE;}
			elseif(checkdnsrr($tab[1], 'A')) {return TRUE;}
		}
	}
}


/**
 * Fonction destinÈe ‡ prendre une date mysql aaaa-mm-jj HH:MM:SS 
 * et ‡ retourner une date au format jj/mm/aaaa
 * 
 * @param date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @return string  date (jj/mm/aaaa)
 * @todo on a dÈj‡ cette fonction
 */
function get_date_slash_from_mysql_date($mysql_date) {
	$tmp_tab=explode(" ",$mysql_date);
	if(isset($tmp_tab[0])) {
		$tmp_tab2=explode("-",$tmp_tab[0]);
		if(isset($tmp_tab2[2])) {
			return $tmp_tab2[2]."/".$tmp_tab2[1]."/".$tmp_tab2[0];
		}
		else {
			return "Date '".$tmp_tab[0]."' badly formatted?";
		}
	}
	else {
		return "Date '$mysql_date' badly formatted?";
	}
}

// Fonction destinÈe ‡ prendre une date mysql aaaa-mm-jj HH:MM:SS et ‡ retourner une heure au format HH:MM

/**
 * Fonction destinÈe ‡ prendre une date mysql aaaa-mm-jj HH:MM:SS 
 * et ‡ retourner une heure au format HH:MM
 * 
 * @param date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @return string  heure (HH:MM)
 */
function get_heure_2pt_minute_from_mysql_date($mysql_date) {
	$tmp_tab=explode(" ",$mysql_date);
	if(isset($tmp_tab[1])) {
		$tmp_tab2=explode(":",$tmp_tab[1]);
		if(isset($tmp_tab2[1])) {
			return $tmp_tab2[0].":".$tmp_tab2[1];
		}
		else {
			return "Heure '".$tmp_tab[1]."' badly formatted?";
		}
	}
	else {
		return "Date '$mysql_date' badly formatted?";
	}
}

/**
 * Fonction destinÈe ‡ prendre une date mysql aaaa-mm-jj HH:MM:SS 
 * et ‡ retourner une date  au format jj/mm/aaaa HH:MM
 * 
 * @param date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @return string  heure (jj/mm/aaaa HH:MM)
 */
function get_date_heure_from_mysql_date($mysql_date) {
	return get_date_slash_from_mysql_date($mysql_date)." ".get_heure_2pt_minute_from_mysql_date($mysql_date);
}

/**
 *
 * @param type $mysql_date
 * @return type 
 */
function mysql_date_to_unix_timestamp($mysql_date) {
	$tmp_tab=explode(" ",$mysql_date);
	$tmp_tab2=explode("-",$tmp_tab[0]);
	if((!isset($tmp_tab[1]))||(!isset($tmp_tab2[2]))) {
		// Ces retours ne sont pas adaptÈs... on fait gÈnÈralement une comparaison sur le retour de cette fonction
		return "Date '$mysql_date' badly formatted?";
	}
	else {
		$tmp_tab3=explode(":",$tmp_tab[1]);

		if(!isset($tmp_tab3[2])) {
			// Ces retours ne sont pas adaptÈs... on fait gÈnÈralement une comparaison sur le retour de cette fonction
			return "Date '$mysql_date' badly formatted?";
		}
		else {
			$jour=$tmp_tab2[2];
			$mois=$tmp_tab2[1];
			$annee=$tmp_tab2[0];
		
			$heure=$tmp_tab3[0];
			$min=$tmp_tab3[1];
			$sec=$tmp_tab3[2];
		
			return mktime($heure,$min,$sec,$mois,$jour,$annee);
		}
	}
}

/**
 * Recherche les profs principaux d'une classe
 *
 * @param string $id_classe id de la classe
 * @return array Tableau des logins des profs principaux
 */
function get_tab_prof_suivi($id_classe) {
	$tab=array();

	$sql="SELECT DISTINCT jep.professeur 
		FROM j_eleves_professeurs jep, j_eleves_classes jec 
		WHERE jec.id_classe='$id_classe' 
		AND jec.login=jep.login
		AND jec.id_classe=jep.id_classe
		ORDER BY professeur;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab[]=$lig->professeur;
		}
	}

	return $tab;
}

/**
 * Enregistre pour Affichage un message sur la page d'accueil du destinataire (ML 5/2011)
 * 
 * Les appels possibles
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel") : affiche le message "Bonjour Untel" sur la page d'accueil du destinataire de login "UNTEL" dËs l'appel de la fonction, pour une durÈe de 7 jours, avec dÈcompte sur le 7iËme jour
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" ‡ partir de la date 130674844, pour une durÈe de 7 jours, avec dÈcompte sur le 7iËme jour	
 *  - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844,130684567) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" ‡ partir de la date 130674844, jusqu'‡ la date 130684567, avec dÈcompte sur la date 130684567
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844,130684567,130690844) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" ‡ partir de la date 130674844, jusqu'‡ la date 130684567, avec dÈcompte sur la date 130690844
 * 
 * @param type $login_destinataire login du destinataire (obligatoire)
 * @param type $texte texte du message contenant Èventuellement des balises HTML et encodÈ en iso-8859-1 (obligatoire)
 * @param type $date_debut date ‡ partir de laquelle est affichÈ le message (timestamp, optionnel)
 * @param type $date_fin date ‡ laquelle le message n'est plus affichÈ (timestamp, optionnel)
 * @param type $date_decompte date butoir du dÈcompte, la chaÓne _DECOMPTE_ dans $texte est remplacÈe par un dÈcompte (timestamp, optionnel)
 * @return type TRUE ou FALSE selon que le message a ÈtÈ enregistrÈ ou pas
 */
function message_accueil_utilisateur($login_destinataire,$texte,$date_debut=0,$date_fin=0,$date_decompte=0)
{
	// On arrondit le timestamp d'appel ‡ l'heure (pas nÈceassaire mais pour l'esthÈtique)
	$t_appel=time()-(time()%3600);
	// suivant le nombre de paramËtres passÈs :
	switch (func_num_args())
		{
		case 3:
			$date_fin=$date_debut + 3600*24*7;
			$date_decompte=$date_fin;
			break;
		case 4:
			$date_decompte=$date_fin;
			break;
		case 5:
			break;
		default :
			// valeurs par dÈfaut
			$date_debut=$t_appel;
			$date_fin=$t_appel + 3600*24*7;
			$date_decompte=$date_fin;		
		}
	$r_sql="INSERT INTO `messages` values('','".addslashes($texte)."','".$date_debut."','".$date_fin."','".$_SESSION['login']."','_','".$login_destinataire."','".$date_decompte."')";
	return mysql_query($r_sql);
}

/**
 * Transforme un tableau en chaine, les lignes sont sÈparÈes par une ,
 *
 * @param array $tableau Le tableau ‡ parser
 * @return string La chaine produite 
 */
function array_to_chaine($tableau) {
	$chaine="";
	$cpt=0;
	foreach($tableau as $key => $value) {
		if($cpt>0) {$chaine.=", ";}
		$chaine.="'$value'";
		$cpt++;
	}
    unset ($key);
    unset ($value);
	return $chaine;
}

/**
 * Supprime les sauts de lignes dupliquÈs
 * 
 * @param string $chaine La chaine  ‡ parser
 * @return string La chaine produite 
 */
function suppression_sauts_de_lignes_surnumeraires($chaine) {
	$retour=preg_replace('/(\\\r\\\n)+/',"\r\n",$chaine);
	$retour=preg_replace('/(\\\r)+/',"\r",$retour);
	$retour=preg_replace('/(\\\n)+/',"\n",$retour);
	return $retour;
}

/**
 * Affiche le nombre de notes ou commentaires saisis pour les bulletins
 *
 * @param string $type "notes" pour voir les notes sinon commentaires
 * @param int $id_groupe Id du groupe
 * @param int $periode_num numÈro de la pÈriode
 * @param string $mode Si "couleur" le texte est sur fond orange si tous les ÈlËves ne sont pas notÈs
 * @return string le nombre de notes ou commentaires saisis
 */
function nb_saisies_bulletin($type, $id_groupe, $periode_num, $mode="") {
	$retour="";

	if($type=="notes") {
		$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='".$id_groupe."' AND periode='".$periode_num."';";
	}
	else {
		$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='".$id_groupe."' AND periode='".$periode_num."';";
	}
	$test=mysql_query($sql);
	$nb_saisies_bulletin=mysql_num_rows($test);

	$tab_champs=array('eleves');
	$current_group=get_group($id_groupe, $tab_champs);
	$effectif_groupe=count($current_group["eleves"][$periode_num]["users"]);

	if($mode=="couleur") {
		if($nb_saisies_bulletin==$effectif_groupe){
			$retour="<span style='font-size: x-small;' title='Complete typing'>";
			$retour.="($nb_saisies_bulletin/$effectif_groupe)";
			$retour.="</span>";
		}
		else {
			$retour="<span style='font-size: x-small; background-color: orangered;' title='Incomplete typing or not still carried out'>";
			$retour.="($nb_saisies_bulletin/$effectif_groupe)";
			$retour.="</span>";
		}
	}
	else {
		$retour="($nb_saisies_bulletin/$effectif_groupe)";
	}

	return $retour;
}

/**
 * CrÈe un fichier index.html de redirection vers login.php
 *
 * @param string $chemin_relatif Le rÈpertoire ‡ protÈger
 * @param int $niveau_arbo Niveau dans l'arborescence GEPI
 * @return boolean TRUE si le fichier est crÈÈ
 */
function creation_index_redir_login($chemin_relatif,$niveau_arbo=1) {
	$retour=TRUE;

	if($niveau_arbo==0) {
		$pref=".";
	}
	else {
		$pref="";
		for($i=0;$i<$niveau_arbo;$i++) {
			if($i>0) {
				$pref.="/";
			}
			$pref.="..";
		}
	}

	$fich=fopen($chemin_relatif."/index.html","w+");
	if(!$fich) {
		$retour=FALSE;
	}
	else {
		$res=fwrite($fich,'<html><head><script type="text/javascript">
    document.location.replace("'.$pref.'/login.php")
</script></head></html>
');
		if(!$res) {
			$retour=FALSE;
		}
		fclose($fich);
	}

	return $retour;
}

/**
 * Renvoie un tableau des fichiers contenus dans le dossier
 *
 * @param string $path Le dossier ‡ parser
 * @param array $tab_exclusion Fichiers ‡ ne pas prendre en compte
 * @return array Tableau des fichiers
 */
function get_tab_file($path,$tab_exclusion=array(".", "..", "remove.txt", ".htaccess", ".htpasswd", "index.html")) {
	$tab_file = array();

	$handle=opendir($path);
	$n=0;
	while ($file = readdir($handle)) {
		if (!in_array(strtolower($file), $tab_exclusion)) {
			$tab_file[] = $file;
			$n++;
		}
	}
	closedir($handle);
	//arsort($tab_file);
	rsort($tab_file);

	return $tab_file;
}


/**
 * Tableau des mentions pour les bulletins
 *
 * @global array $GLOBALS['tableau_des_mentions_sur_le_bulletin']
 * @name $tableau_des_mentions_sur_le_bulletin
 */
$GLOBALS['tableau_des_mentions_sur_le_bulletin'] = array();

/**
 * Retourne une mention pour les bulletins ‡ partir de son Id
 * 
 * @global array
 * @param int $code Id de la mention recherchÈe
 * @return string 
 * @see get_mentions()
 */
function traduction_mention($code) {
	global $tableau_des_mentions_sur_le_bulletin;

	if((!is_array($tableau_des_mentions_sur_le_bulletin))||(count($tableau_des_mentions_sur_le_bulletin)==0)) {
		$tableau_des_mentions_sur_le_bulletin=get_mentions();
	}

	$retour="";
	if(!isset($tableau_des_mentions_sur_le_bulletin[$code])) {$retour="-";}
	else {$retour=$tableau_des_mentions_sur_le_bulletin[$code];}

	return $retour;
}

/**
 * Retourne un tableau des mentions pour les bulletins
 * 
 * tableau[index de la mention] = texte de la mention;
 * 
 * @param int $id_classe Id de la classe
 * @return array Le tableau des mentions
 */
function get_mentions($id_classe=NULL) {
	$tab=array();
	if(!isset($id_classe)) {
		$sql="SELECT * FROM mentions ORDER BY id;";
	}
	else {
		$sql="SELECT m.* FROM mentions m, j_mentions_classes j WHERE j.id_mention=m.id AND j.id_classe='$id_classe' ORDER BY j.ordre, m.mention, m.id;";
	}
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab[$lig->id]=$lig->mention;
		}
	}
	return $tab;
}

/**
 * Retourne un tableau des mentions dÈj‡ utilisÈes dans les bulletins
 *
 * Pour interdire la suppression d'une mention saisie pour un ÈlËve
 * 
 * @param int $id_classe Id de la classe
 * @return array Le tableau des mentions
 */
function get_tab_mentions_affectees($id_classe=NULL) {
	$tab=array();
	if(!isset($id_classe)) {
		$sql="SELECT DISTINCT j.id_mention FROM j_mentions_classes j, avis_conseil_classe a WHERE a.id_mention=j.id_mention;";
	}
	else {
		$sql="SELECT DISTINCT j.id_mention FROM j_mentions_classes j, avis_conseil_classe a, j_eleves_classes jec WHERE a.id_mention=j.id_mention AND j.id_classe=jec.id_classe AND jec.periode=a.periode AND jec.login=a.login AND j.id_classe='$id_classe';";
	}
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab[]=$lig->id_mention;
		}
	}
	return $tab;
}

/**
 * Renvoie une balise <select> avec les mentions de bulletin
 *
 * @param string $nom_champ_select valeur des attribut name et id du select
 * @param int $id_classe Id de la classe
 * @param string $id_mention_selected Id de la mention ‡ sÈlectionner par dÈfaut
 * @return string La balise
 */
function champ_select_mention($nom_champ_select,$id_classe,$id_mention_selected='') {

	$tab_mentions=get_mentions($id_classe);
	$retour="<select name='$nom_champ_select' id='$nom_champ_select'>\n";
	$retour.="<option value=''";
	if(($id_mention_selected=="")||(!array_key_exists($id_mention_selected,$tab_mentions))) {
		$retour.=" selected='selected'";
	}
	$retour.="> </option>\n";
	foreach($tab_mentions as $key => $value) {
		$retour.="<option value='$key'";
		if($id_mention_selected==$key) {
			$retour.=" selected='selected'";
		}
		//$retour.=">".$value." ".$key."</option>\n";
		$retour.=">".$value."</option>\n";
	}
	$retour.="</select>\n";

	return $retour;
}

/**
 * Teste s'il y a des mentions de bulletin dÈfinies pour une classe
 *
 * @param type $id_classe Id de la classe
 * @return boolean TRUE si il y a des mentions 
 */
function test_existence_mentions_classe($id_classe) {
	$sql="SELECT 1=1 FROM j_mentions_classes WHERE id_classe='$id_classe';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		return TRUE;
	}
	else {
		return FALSE;
	}

}

/**
 * Teste si un compte est actif
 * 
 * - 0 si l'utilisateur n'est pas trouvÈ
 * - 1 compte actif
 * - 2 compte non-actif
 *
 * @param type $login Login de l'utilisateur
 * @return int  
 */
function check_compte_actif($login) {
	$sql="SELECT etat FROM utilisateurs WHERE login='$login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		return 0;
	}
	else {
		$lig=mysql_fetch_object($res);
		if($lig->etat=='actif') {
			return 1;
		}
		else {
			return 2;
		}
	}
}

/**
 * CrÈe un lien derriËre une image pour modifier les donnÈes d'un utilisateur
 *
 * @global string
 * @param string $login id de l'utilisateur cherchÈ
 * @param string $statut statut de l'utilisateur (si '', il sera cherchÈ avec get_statut_from_login())
 * @param string $target pour ouvrir dans une autre fenÍtre
 * @param string $avec_lien 'y' ou absent pour crÈer un lien
 * @return string Le code html
 * @see check_compte_actif()
 * @see get_statut_from_login()
 * @see get_infos_from_login_utilisateur()
 * @todo si $target='_blank' il faudrait ajouter un argument title pour prÈvenir
 */
function lien_image_compte_utilisateur($login, $statut='', $target='', $avec_lien='y') {
	global $gepiPath;

	$retour="";

	if($target!="") {$target=" target='$target'";}

	$test=check_compte_actif($login);
	if($test!=0) {
		if($statut=="") {
			$statut=get_statut_from_login($login);
		}

		if($statut!="") {
			$refermer_lien="y";

			if($avec_lien=="y") {
				if($statut=='eleve') {
					$retour.="<a href='".$gepiPath."/eleves/modify_eleve.php?eleve_login=$login'$target>";
				}
				elseif($statut=='responsable') {
					$infos=get_infos_from_login_utilisateur($login);
					if(isset($infos['pers_id'])) {
						$retour.="<a href='".$gepiPath."/responsables/modify_resp.php?pers_id=".$infos['pers_id']."'$target>";
					}
					else {
						$refermer_lien="n";
					}
				}
				elseif($statut=='autre') {
					$retour.="<a href='".$gepiPath."/utilisateurs/creer_statut.php'$target>";
				}
				else {
					$retour.="<a href='".$gepiPath."/utilisateurs/modify_user.php?user_login=$login'$target>";
				}
			}

			if($test==1) {
				$retour.="<img src='".$gepiPath."/images/icons/buddy.png' width='16' height='16' alt='Compte $login actif' title='Count $login active' />";
			}
			else {
				$retour.="<img src='".$gepiPath."/images/icons/buddy_no.png' width='16' height='16' alt='Compte $login inactif' title='Count $login inactive' />";
			}

			if($avec_lien=="y") {
				if($refermer_lien=="y") {
					$retour.="</a>";
				}
			}
		}
	}

	return $retour;
}

/**
 * Renvoie le statut d'un utilisateur ‡ partir de son login
 *
 * @param string $login Login de l'utilisateur
 * @return string Le statut
 */
function get_statut_from_login($login) {
	$sql="SELECT statut FROM utilisateurs WHERE login='$login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		return "";
	}
	else {
		$lig=mysql_fetch_object($res);
		return $lig->statut;
	}
}

/**
 * Renvoie dans un tableau les informations d'un utilisateur ‡ partir de son login
 * 
 * Champs disponibles dans le tableau
 * - tout utilisateur ->  'nom', 'prenom', 'civilite', 'email','show_email','statut','etat','change_mdp','date_verrouillage','ticket_expiration','niveau_alerte','observation_securite','temp_dir','numind','auth_mode'
 * - responsable -> pers_id
 * - eleve -> 'no_gep','sexe','naissance','lieu_naissance','elenoet','ereno','ele_id','id_eleve','id_mef','date_sortie'
 * 
 * @param string $login Login de l'utilisateur
 * @param string $tab_champs Tableau non utilisÈ
 * @return array Le tableau des informations
 * @todo $tab_champs n'est pas utilisÈ pour l'instant
 * @todo DÈterminer les champs supplÈmentaires pour le statut autre
 */
function get_infos_from_login_utilisateur($login, $tab_champs=array()) {
	$tab=array();

	$tab_champs_utilisateur=array('nom', 'prenom', 'civilite', 'email','show_email','statut','etat','change_mdp','date_verrouillage','ticket_expiration','niveau_alerte','observation_securite','temp_dir','numind','auth_mode');
	$sql="SELECT * FROM utilisateurs WHERE login='$login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		foreach($tab_champs_utilisateur as $key => $value) {
			$tab[$value]=$lig->$value;
		}
        unset ($key, $value);

		if($tab['statut']=='responsable') {
			$sql="SELECT pers_id FROM resp_pers WHERE login='$login';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$lig=mysql_fetch_object($res);
				$tab['pers_id']=$lig->pers_id;

				if(in_array('enfants', $tab_champs)) {
					// A complÈter
				}
			}
		}
		elseif($tab['statut']=='eleve') {
			$sql="SELECT * FROM eleves WHERE login='$login';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$lig=mysql_fetch_object($res);

				$tab_champs_eleve=array('no_gep','sexe','naissance','lieu_naissance','elenoet','ereno','ele_id','id_eleve','id_mef','date_sortie');
				foreach($tab_champs_eleve as $key => $value) {
					$tab[$value]=$lig->$value;
				}
                unset ($key, $value);

				if(in_array('parents', $tab_champs)) {
					// A complÈter
				}
			}

		}
		elseif($tab['statut']=='autre') {
			// A complÈter
			$tab['statut_autre']="A EXTRAIRE";
		}
	}
	return $tab;
}

/**
 * VÈrifie qu'un responsable a accËs au module discipline
 *
 * @param string $login_resp Login du responsable
 * @return boolean TRUE si le responsable a accËs
 * @see check_compte_actif()
 * @see getSettingValue()
 */
function acces_resp_disc($login_resp) {
	if((check_compte_actif($login_resp)!=0)&&(getSettingValue('visuRespDisc')=='yes')) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * VÈrifie qu'un ÈlËve a accËs au module discipline
 *
 * @param string $login_ele Login de l'ÈlËve
 * @return boolean TRUE si l'ÈlËve a accËs
 * @see check_compte_actif()
 * @see getSettingValue()
 */
function acces_ele_disc($login_ele) {
	if((check_compte_actif($login_ele)!=0)&&(getSettingValue('visuEleDisc')=='yes')) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * Renvoie un tableau des responsables d'un ÈlËve
 * 
 * $tab[indice] = array('login','nom','prenom','civilite','designation'=>civilite nom prenom)
 *
 * @param string $ele_login Login de l'ÈlËve
 * @return array Le tableau
 */
function get_resp_from_ele_login($ele_login) {
	$tab="";

	$sql="SELECT rp.* FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND (r.resp_legal='1' OR r.resp_legal='2');";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysql_fetch_object($res)) {
			$tab[$cpt]=array();

			$tab[$cpt]['login']=$lig->login;
			$tab[$cpt]['nom']=$lig->nom;
			$tab[$cpt]['prenom']=$lig->prenom;
			$tab[$cpt]['civilite']=$lig->civilite;

			$tab[$cpt]['designation']=$lig->civilite." ".$lig->nom." ".$lig->prenom;

			$cpt++;
		}
	}

	//print_r($tab);

	return $tab;
}

/**
 *
 * @param type $callback
 * @param ArrayAccess $array
 * @return type 
 */
function array_map_deep($callback, $array) {
    $new = array();
    if (is_array($array) || $array instanceof ArrayAccess) {
    	foreach ($array as $key => $val) {
	        if (is_array($val)) {
	            $new[$key] = array_map_deep($callback, $val);
	        } else {
	            $new[$key] = call_user_func($callback, $val);
	        }
    	}
    }
    else $new = call_user_func($callback, $array);
    return $new;
} 

/**
 * VÈrifie si une variable est en UTF8 et la rÈencode au besoin
 * @param string $var La variable ‡ vÈrifier
 * @return string La variable dÈcodÈe 
 */
function check_utf8_and_convert($var) {
	if(function_exists("mb_check_encoding")) {
		if (!mb_check_encoding($var, 'UTF-8')) {
    		return utf8_encode($var);
    	} else {
    		return $var;
    	}
	}
} 


/** fonction retournant le jour traduit en franÁais
 *
 * @param string $jour_en Le jour en anglais (Mon, Tue, Wed,...)
 * @return string La date en franÁais 
 */
function jour_fr($jour_en, $mode="") {
	$tab['mon']="lun";
	$tab['tue']="mar";
	$tab['wed']="mer";
	$tab['thu']="jeu";
	$tab['fri']="ven";
	$tab['sat']="sam";
	$tab['sun']="dim";

	if(isset($tab[mb_strtolower($jour_en)])) {
		if($mode=='majf2') {
			return casse_mot($tab[mb_strtolower($jour_en)], 'majf2');
		}
		else {
			return $tab[mb_strtolower($jour_en)];
		}
	}
	else {
		return $jour_en;
	}
}
?>
