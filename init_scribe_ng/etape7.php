<?php

/*
 * $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard + auteur du script original (ac. Orléans-Tours)
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

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
require_once("../lib/LDAPServerScribe.class.php");
require_once("eleves_fonctions.php");
include("config_init_annuaire.inc.php");

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

//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year : importation of the administrative staffs";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Utilisation de la classe LDAP chargee et configuree
$ldap = new LDAPServerScribe();

echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>";

if ($_POST['step'] == "7") {
	check_token(false);

    // On se connecte au LDAP
    $ldap->connect();

    // Si on a bien les donnees dans la session, on peut continuer
    /*
     * Recherche de tous les profs de l'établissement (pour ce RNE)
    */
    $personnels = $ldap->get_all_personnels();
    $nb_pers = $personnels['count'];


    /*
    * Ajout des profs
    */

    // Infos nécessaires
    $nom_complet = '';
    $uid_as_login = '';
    $mail = '';

    // On parcours tous les utilisateurs et on les ajoute, si nécessaire
    for($cpt=0; $cpt<$personnels['count']; $cpt++) {
        $uid_as_login = $personnels[$cpt][$ldap->champ_login][0];
        $nom = $personnels[$cpt][$ldap->champ_nom][0];
        $prenom = $personnels[$cpt][$ldap->champ_prenom][0];
        $civ = $personnels[$cpt]['personaltitle'][0];
        $mail = $personnels[$cpt][$ldap->champ_email][0];

        // On test si l'uid est deja connu de GEPI
        $compte_utilisateur = UtilisateurProfessionnelPeer::retrieveByPK($uid_as_login);
        if ($compte_utilisateur != null) {
            echo "L'utilisateur "
            .$compte_utilisateur->getPrenom()
            .$compte_utilisateur->getNom()
            ." (".$compte_utilisateur->getLogin()
            .") existe déja<br>";
        }
        else {
            $new_compte_utilisateur = new UtilisateurProfessionnel();
            $new_compte_utilisateur->setAuthMode('sso');
            $new_compte_utilisateur->setCivilite($civ);
            $new_compte_utilisateur->setEmail($mail);
            $new_compte_utilisateur->setEtat('actif');
            $new_compte_utilisateur->setLogin($uid_as_login);
            $new_compte_utilisateur->setNom($nom);
            $new_compte_utilisateur->setPrenom($prenom);
            $new_compte_utilisateur->setShowEmail('no');
            $new_compte_utilisateur->setStatut('scolarite');
            $new_compte_utilisateur->save();
        }
    } // fin parcours de tous les personnels
        /*
         * Résumé des profs trouvés :
         */
    echo "<br/><br/>Numbers of personnel found  : $nb_pers"."<br/><br/>";

    echo "<form enctype='multipart/form-data' action='../accueil_admin.php' method=post>";
	//echo add_token_field();

    echo "<p>If you etes arrived at this stage, you finished the importation of the data coming from directory ENT.</p>";
    echo "<p>You can now go in the management part of the bases to refine the various imported data.</p>";
    echo "<p>Do not forget to audit the accounts of accesses created for the
administrative staffs, who all were initialized by default with the statute ' schooling'.</p>";
    echo "<input type='submit' value='Access to the management of the bases'>";
    echo "</form>";
}

else {
    // Affichage de la page des explications de l'etape 4 (aucune donnee postee)

    echo "<br><p>Stage 7 enables you to import the accounts of the personnel not-teacher of the school.</p>";
    echo "<p>Significant note : directory LDAP not allowing to distinguish the personnel between them, all found users and not existing already in the base will be initialized with the statute ' schooling'. It is thus essential that you redefine the good statutes in the interface of management of the accounts of access.</p>";
    echo "<form enctype='multipart/form-data' action='etape7.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='step' value='7'>";
    echo "<input type='submit' value='I am sure'>";
    echo "</form>";
    echo "<br>";

    require("../lib/footer.inc.php");

}

?>
