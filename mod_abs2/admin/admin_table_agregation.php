<?php
/*
 *
 *  @version $Id: admin_table_agregation.php 7939 2011-08-24 08:33:52Z dblanqui $
 *
 * Copyright 2010-2011 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

$niveau_arbo = 2;
// Initialisations files
include("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

//initialisation des variables 
$action= isset($_POST['action'])?$_POST['action']:Null;
$page= isset($_POST['page'])?$_POST['page']:1;
$maxPerPage=5;

if ($action == "vidage" || $action=="regeneration") {
    check_token(); 
}
//gestion des dates
require_once("../../orm/helpers/EdtHelper.php");
$date_debut =  EdtHelper::getPremierJourAnneeScolaire();
$date_fin = EdtHelper::getDernierJourAnneeScolaire();
$date_fin->setTime(23,59,59);

// header
$titre_page = "Management of the table of aggregation of the half-days of absence";
$javascript_specifique[] = "mod_abs2/lib/include";
require_once("../../lib/header.inc");

echo "<p class=bold>";
echo "<a href=\"index.php\">";
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";
?>

<div style="text-align:center">
   <h2>Maintenance of the table of aggregation of the half-days of absence</h2>    
    
    <div style="text-align:center">
        <?php if ($action == "vidage" || $action=="regeneration") : ?>
            <h2>Emptying and regeneration of the table of aggregation</h2>
                <?php
                if ($action == "vidage") {
                    $del = AbsenceAgregationDecompteQuery::create()->deleteAll();
                    $nb = AbsenceAgregationDecompteQuery::create()->count();
                    if ($nb === 0) {
                        echo"<p>The Table is empty.</p>";
                        die();
                    } else {
                        echo"<p>A problem occurred.</p>";
                        die();
                    }
                } elseif ($action == "regeneration") {
                    $eleve_col = EleveQuery::create()->paginate($page, $maxPerPage);
                    echo'<div id="contain_div" class="css-panes">
                        <p> Treatment of the section of student ' . $page . '/' . $eleve_col->getLastPage() . ' in course... <br />
                            Attention this operation can be long.</p>
                         </div>';
                    if(ob_get_contents()){
                       ob_flush(); 
                    }                   
                    flush();
                    foreach ($eleve_col as $eleve) {
                        $eleve->checkAndUpdateSynchroAbsenceAgregationTable($date_debut, $date_fin);
                    }
                    if ($page != $eleve_col->getLastPage()) {
                        echo"<p> Treatment of the section of student " . $page . "/" . $eleve_col->getLastPage() . " finished <br /></p>";
                        $page++;
                    } else {
                        echo"<p>Finished treatment</p>";
                        die();
                    }
                }
                ?>
        <?php else : ?>
            <h2>CAUTION: In the event of modification of one of the types of absence
you must empty the table and reremplir it.</h2>
            <p>While clicking on the button below you will launch the emptying or the
re-filling of the table.</p>
        <?php endif; ?>
        
        <form action="admin_table_agregation.php" method="post" name="form_table" id="form_table">
            <?php echo add_token_field();?>
            <?php if($action==Null) :?>
            <input type="radio" name="action" value="vidage" /> To empty the Table <br />
            <input type="radio" name="action" value="regeneration" 
                   <?php if ($action !== "regeneration" &&  $action !== "vidage") : ?> 
                   checked 
                   <?php endif;?>
                   />To fill out the Table<br />
            <?php else :?>
            <input type="hidden" name="action" value="<?php echo $action; ?>" />
            <?php endif;?>
            <input type="hidden" name="page" value="<?php echo $page; ?>" />
            <br /><br /><br />            
            <?php if ($action !== "regeneration" &&  $action !== "vidage") : ?> 
                <input type="submit" name="Submit" value="Valider" onclick="return(confirm('Etes-vous s�r de vouloir lancer le processus ?'));" /> 
            <?php else : ?> 
                <script type="text/javascript">
                    postform(document.getElementById('form_table'));
                </script>  
                <noscript>
                <input type="submit" name="Submit" value="Continuer" />
                </noscript>
            <?php endif; ?>  
        </form>
    </div>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
<?php require("../../lib/footer.inc.php");?>