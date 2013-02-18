 
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
        <li><a href="#">Tools<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]--> 
            <li><a href="./index_edt.php?salleslibres=ok">Search free rooms</a></li> 
            <li><a href="javascript:window.print()">Print the page</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
	<?php if ($_SESSION['statut'] == 'administrateur') { ?>
    <ul> 
        <li><a href="#">Maintenance<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./verifier_edt.php">Check/Correct the base</a></li>
            <li><a href="./voir_base.php">See the base</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>

    <ul>
        <li><a href="#">Management<!--[if IE 7]><!--></a><!--<![endif]-->
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
            <li><a href="./aide_initialisation.php">Help to initialization</a></li>
            <li><a href="./aide_maintenance.php">Help to maintenance</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul> 
	<?php } ?>
 </div>
<div style="clear:both;"></div>
 
