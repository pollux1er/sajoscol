<?php
/*
 * $Id: professeurs.php 5938 2010-11-21 18:14:45Z crob $
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
 */

function connect_ldap($l_adresse,$l_port,$l_login,$l_pwd) {
    $ds = @ldap_connect($l_adresse, $l_port);
    if($ds) {
       // On dit qu'on utilise LDAP V3, sinon la V2 par d?faut est utilis? et le bind ne passe pas.
       $norme = @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
       // Acc?s non anonyme
       if ($l_login != '') {
          // On tente un bind
          $b = @ldap_bind($ds, $l_login, $l_pwd);
       } else {
          // Acc?s anonyme
          $b = @ldap_bind($ds);
       }
       if ($b) {
           return $ds;
       } else {
           return false;
       }
    } else {
       return false;
    }
}

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

function add_user($_login, $_nom, $_prenom, $_sexe, $_statut, $_email) {
    // Fonction d'ajout de l'utilisateur

        if ($_sexe == "M") {
            $_civilite = "M.";
        } else {
            $_civilite = "Mme";
        }


    // Si l'utilisateur existe déjà, on met simplement à jour ses informations...
    $test = mysql_query("SELECT login FROM utilisateurs WHERE login = '" . $_login . "'");
    if (mysql_num_rows($test) > 0) {
        $record = mysql_query("UPDATE utilisateurs SET
        nom = '" . $_nom . "',
        prenom = '" . $_prenom . "',
        civilite = '" . $_civilite . "',
        email = '" . $_email . "',
        statut = '" . $_statut . "',
        etat = 'actif',
        auth_mode='sso'
        WHERE login = '" . $_login . "'");
    } else {
        $query = "INSERT into utilisateurs SET
        login= '" . $_login . "',
        nom = '" . $_nom . "',
        prenom = '" . $_prenom . "',
        password = '',
        civilite = '" . $_civilite . "',
        email = '" . $_email . "',
        statut = '" . $_statut . "',
        etat ='actif',
        auth_mode='sso',
        change_mdp = 'n'";
        $record = mysql_query($query);
    }

    if ($record) {
        return true;
    } else {
        return false;
    }
}


// Initialisation
$lcs_ldap_people_dn = 'ou=people,'.$lcs_ldap_base_dn;
$lcs_ldap_groups_dn = 'ou=groups,'.$lcs_ldap_base_dn;

//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year : Importation of the professors";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='../init_lcs/index.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>";

if (isset($_POST['is_posted'])) {
	check_token();

    // L'admin a validé la procédure, on procède donc...

    // On se connecte au LDAP
    $ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");
    // LDAP attribute
    $ldap_people_attr = array(
        "uid",               // login
        "cn",                // Prenom  Nom
        "sn",               // Nom
        "givenname",            // Pseudo
        "mail",              // Mail
        "homedirectory",           // Home directory personnal web space
        "description",
        "loginshell",
        "gecos"             // Date de naissance,Sexe (F/M),
        );

    echo "<table border=\"1\" cellpadding=\"3\" cellspacing=\"3\">\n<tr><td>Login Professor</td><td>Name </td><td>First name</td><td>Sex</td><td>Email</td></tr>\n";
    // On commence par récupérer tous les profs depuis le LDAP
    $attr[] = "memberuid";
    $result = ldap_read ( $ds, "cn=Profs,".$lcs_ldap_groups_dn, "(objectclass=*)",$attr);

    // On met tous les professeurs en état inactif
    $update = mysql_query("UPDATE utilisateurs SET etat='inactif' WHERE statut='professeur'");
    $info = ldap_get_entries ( $ds, $result );
    if ( $info["count"]) {
         for($i=0;$i<$info[0]["memberuid"]["count"];$i++) {
             $uid = $info[0]["memberuid"][$i];
             if (($uid != "etabw") and ($uid!="webmaster.etab") and ($uid!="spip.manager")) {

             // Extraction des infos sur le professeur :
             $result2 = @ldap_read ( $ds, "uid=".$uid.",".$lcs_ldap_people_dn, "(objectclass=posixAccount)", $ldap_people_attr );
             if ($result2) {
                 $info2 = @ldap_get_entries ( $ds, $result2 );
                 if ( $info2["count"]) {
                     // Traitement du champ gecos pour extraction de date de naissance, sexe
                     $gecos = $info2[0]["gecos"][0];
                     $tmp = split ("[\,\]",$info2[0]["gecos"][0],4);
                     $ret_people = array (
                     "nom"         => stripslashes( utf8_decode($info2[0]["sn"][0]) ),
                     "fullname"        => stripslashes( utf8_decode($info2[0]["cn"][0]) ),
                     "email"       => $info2[0]["mail"][0],
                     "sexe"            => $tmp[2],
                     );
                     $long = strlen($ret_people["fullname"]) - strlen($ret_people["nom"]);
                     $prenom = substr($ret_people["fullname"], 0, $long) ;
                 }
                 @ldap_free_result ( $result2 );
             }
             // On ajoute l'utilisateur. La fonction s'occupe toute seule de vérifier que
             // le login n'existe pas déjà dans la base. S'il existe, on met simplement à jour
             // les informations
             // function add_user($_login, $_nom, $_prenom, $_statut) {
             $add = add_user($uid,$ret_people["nom"],$prenom,$ret_people["sexe"],"professeur",$ret_people["email"]);
             echo "<tr><td>".$uid."</td><td>".$ret_people["nom"]."</td><td>".$prenom."</td><td>".$tmp[2]."</td><td>".$ret_people["email"]."</td></tr>\n";
             }
         }
         echo "<table>";
    }

    echo "<p>Operation done.</p>";
    echo "<p>You can check the importation while going on the page of <a href='../utilisateurs/index.php'>management of the users</a>.</p>";

} else {
    echo "<p>The operation of importation of the professors from the LDAP of LCS will carry out the following operations :</p>";
    echo "<ul>";
    echo "<li>Passage to the state 'inactive' of all the professors already present in the Gepi base.</li>";
    echo "<li>Attempt at addition of each user 'professor' present in directory LDAP of LCS.</li>";
    echo "<li>If the user does not exist, it is created and is directly usable.</li>";
    echo "<li>If the user already exists, its basic information is updated and it passes in state 'actif', becoming directly usable.</li>";
    echo "</ul>";
    echo "<form enctype='multipart/form-data' action='professeurs.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='is_posted' value='yes'>";

    echo "<p>Are you sure you want to import all the users since the directory of server LCS towards Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='I am sure'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>