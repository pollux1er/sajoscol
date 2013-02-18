<?php
/**
 * EdT Gepi : le menu pour les includes require_once().
 *
 * @version $Id: menu.inc.new.php $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
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

// S�curit� : �viter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
	if ($analyse[3] == "menu.inc.new.php") {
		die();
	}

// ========================= R�cup�rer le bon fichier de langue

require_once('./choix_langue.php');

// ================= D�sactivation de ce type de menu pour IE6 - pb de z-index insoluble !

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
	echo '

<div class="menu_deroulant">
    <ul>
        <li><a href="#">Display<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./index_edt.php?visioedt=prof1">Timetables professors</a></li>
            <li><a href="./index_edt.php?visioedt=classe1">Timetables classes</a></li>
            <li><a href="./index_edt.php?visioedt=salle1">Timetables rooms</a></li>
            <li><a href="./index_edt.php?visioedt=eleve1">Timetables students</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>

        </li>
    </ul>
    
    <ul>
        <li><a href="#">Outils<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            ';
	        // =====================La fonction chercher_salle est param�trable
            $aff_cherche_salle = GetSettingEdt("aff_cherche_salle");
	        if ($aff_cherche_salle == "tous") {
		        $aff_ok = "oui";
	        }
	        else if ($aff_cherche_salle == "admin") {
		        $aff_ok = "administrateur";
	        }
	        else {
	            $aff_ok = "non";
            }
	        if ($aff_ok == "oui" OR $_SESSION["statut"] == $aff_ok) {
		        echo '
            <li><a href="./index_edt.php?salleslibres=ok">Search free rooms</a></li>
                ';
            }
            // ================================================================
            echo '
            <li><a href="javascript:window.print()">Print the page</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
    <ul>
        ';
        if ($_SESSION['statut'] == "administrateur") {
        echo '
        <li><a href="#">Maintenance<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./verifier_edt.php">Check/Correct the base</a></li>
            <li><a href="./voir_base.php">Voir la base</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>

    <ul>
        <li><a href="#">Gestion<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./edt.php" >Management of access</a></li>
            <li><a href="./index.php?action=propagation" >Management of propagations</a></li>
            <li><a href="./transferer_edt.php" >Management of replacements</a></li>
            <li><a href="./ajouter_salle.php">Management of rooms</a></li>
            <li><a href="./edt_calendrier.php">Management of calendar</a></li>
            <li><a href="./index.php?action=calendriermanager">Management of calendar version 2</a></li>
            <li><a href="./admin_config_semaines.php?action=visualiser">Define the types of weeks</a></li>
            <li><a href="./admin_horaire_ouverture.php?action=visualiser">Define the schedules of opening</a></li>
            <li><a href="./admin_periodes_absences.php?action=visualiser">Define the standard day</a></li>
            <li><a href="./edt_initialiser.php">Automatic initialization</a></li>
            <li><a href="./index_edt.php?visioedt=prof1">Manual initialization</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
    <ul>
        <li><a href="#">Options<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./edt_parametrer.php">Personalize the display</a></li>
            <li><a href="./edt_param_couleurs.php">Define the colors</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
    <ul>
        <li><a href="#">?<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./aide_initialisation.php">Help for initialization</a></li>
            <li><a href="./aide_maintenance.php">Help for maintenance</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
        ';
        } 
        echo '
</div>
<div style="clear:both;"></div>
	';
	}
?>