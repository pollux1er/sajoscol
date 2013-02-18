<?php
@set_time_limit(0);
/*
 * $Id: step1.php 5937 2010-11-21 17:42:55Z crob $
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


// Initialisations files
require_once("../lib/initialisations.inc.php");
extract($_POST, EXTR_OVERWRITE);


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Page bourrinée... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();

//**************** EN-TETE *****************
$titre_page = "Tool of initialization of the year : Importation of the students - Stage 1";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return to home of initialization</a></p>
<?php

// On vérifie si l'extension d_base est active
verif_active_dbase();

echo "<center><h3 class='gepi'>First phase of initialization<br />Importation of the students,  constitution of the classes and assignment of the students in the classes</h3></center>";


if (!isset($is_posted)) {
    echo "<p>You will carry out the first stage : it consists in importing the file <b>F_ELE.DBF</b> containing all the data in a temporary table of the data base of <b>GEPI</b>.";
    echo "<p>Please specify the complete name of the file <b>F_ELE.DBF</b>.";
    echo "<form enctype='multipart/form-data' action='step1.php' method=post>";
    echo "<input type=hidden name='is_posted' value='yes'>";
    echo "<p><input type=\"file\" size=\"80\" name=\"dbf_file\">";
    echo "<p><input type=submit value='Validate'>";
    echo "</form>";

} else {
    $dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
    if(strtoupper($dbf_file['name']) == "F_ELE.DBF") {
        $fp = dbase_open($dbf_file['tmp_name'], 0);

        if(!$fp) {
            echo "<p>Impossible d'ouvrir le fichier dbf !</p>";
            echo "<p><a href='step1.php'>Click here </a> to restart !</center></p>";
        } else {
            $del = @mysql_query("DELETE FROM temp_gep_import");
            // on constitue le tableau des champs à extraire
            $tabchamps = array("ELENOM","ELEPRE","ELESEXE","ELEDATNAIS","ELENOET","ERENO","ELEDOUBL","ELENONAT","ELEREG","DIVCOD","ETOCOD_EP", "ELEOPT1", "ELEOPT2", "ELEOPT3", "ELEOPT4", "ELEOPT5", "ELEOPT6", "ELEOPT7", "ELEOPT8", "ELEOPT9", "ELEOPT10", "ELEOPT11", "ELEOPT12");

            $nblignes = dbase_numrecords($fp); //number of rows
            $nbchamps = dbase_numfields($fp); //number of fields

            // On range dans un tableau les en-têtes des champs
            if (@dbase_get_record_with_names($fp,1)) {
                $temp = @dbase_get_record_with_names($fp,1);
            } else {
                echo "<p>The selected file is not valid !<br />";
                echo "<a href='step1.php'>Click here </a> to restart !</center></p>";
                die();
            }

            $nb = 0;
            foreach($temp as $key => $val){
                $en_tete[$nb] = "$key";
                $nb++;
            }

            // On range dans tabindice les indices des champs retenus
            for ($k = 0; $k < count($tabchamps); $k++) {
                for ($i = 0; $i < count($en_tete); $i++) {
                    if ($en_tete[$i] == $tabchamps[$k]) {
                        $tabindice[] = $i;
                    }
                }
            }

            $nb_reg_ok = 0;
            $nb_reg_no = 0;
            for($k = 1; ($k < $nblignes+1); $k++){
                $enregistre = "yes";
                $ligne = dbase_get_record($fp,$k);
                $query = "INSERT INTO temp_gep_import VALUES ('$k',''";
                for($i = 0; $i < count($tabchamps); $i++) {
                    $query = $query.",";

                    $ind = $tabindice[$i];
                    $affiche = dbase_filter(trim($ligne[$ind]));
                    $query = $query."\"".$affiche."\"";
                    if (($en_tete[$ind] == 'DIVCOD') and ($affiche == '')) {$enregistre = "no";}
                }
                $query = $query.")";
                if ($enregistre == "yes") {
                    $register = mysql_query($query);
                    if (!$register) {
                        echo "<p class=\"small\"><font color='red'>Analyze the line $k : error during recording !</font></p>";
                        $nb_reg_no++;
                    } else {
                        $nb_reg_ok++;
//                        echo ".";
                    }
                } else {
//                    echo ".";
                }
            }

            dbase_close($fp);
            if ($nb_reg_no != 0) {
                echo "<p>During recording of the data there was $nb_reg_no errors, you cannot proceed the rest initialization. Find the cause of the error and start again the procedure, after having emptied the temporary table.";
            } else {
                echo "<p>The $nblignes lines of the file F_ELE.DBF were analyzed.<br />$nb_reg_ok lines of data corresponding to students of the current year were
recorded in a temporary table.<br />There were No errors, you can proceed at the next stage.</p>";
                echo "<center><p><a href='step2.php'>Access to stage 2</a></p></center>";
            }
        }
    } else if (trim($dbf_file['name'])=='') {

        echo "<p>No file was selected !<br />";
        echo "<a href='step1.php'>Click here </a> to restart !</center></p>";

    } else {
        echo "<p>The selected file is not valid !<br />";
        echo "<a href='step1.php'>Click here </a> to restart !</center></p>";
    }
}
require("../lib/footer.inc.php");
?>