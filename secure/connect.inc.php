<?php
# La ligne suivante est � modifier si vous voulez utiliser le multisite
# Regardez le fichier modeles/connect-modele.inc.php pour information
$multisite = 'n';
# Les cinq lignes suivantes sont � modifier selon votre configuration
# Pensez � renommer ce fichier connect.cfg.php en connect.inc.php
#
# ligne suivante : le nom du serveur qui herberge votre base mysql.
# Si c'est le m�me que celui qui heberge les scripts, mettre "localhost"
$dbHost="localhost";
# ligne suivante : le nom de votre base mysql
$dbDb="sasse";
# ligne suivante : le nom de l'utilisateur mysql qui a les droits sur la base
$dbUser="root";
# ligne suivante : le mot de passe de l'utilisateur mysql ci-dessus
$dbPass="";
# Chemin relatif vers GEPI
$gepiPath="/sajoscol";
#
# Authentification par CAS ?
# Si vous souhaitez int�grer Gepi dans un environnement SSO avec CAS,
# vous devrez renseigner le fichier /secure/config_cas.inc.php avec les
# informations n�cessaires � l'identification du serveur CAS
$use_cas = false; // false|true
?>
