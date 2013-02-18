<?php
/*
 * @version: $Id: export_csv_aid.php 6588 2011-03-02 17:53:54Z crob $
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

$call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
$nom_generique_aid = @mysql_result($call_data, 0, "nom");

//**************** EN-TETE *****************
$titre_page = "Management of ".$nom_generique_aid." | importation Tool ";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href=\"index2.php?indice_aid=$indice_aid\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | ";

if (isset($is_posted) and ($is_posted=='avec_id_etape_4')) {echo "<a href=\"export_csv_aid.php?is_posted=avec_id_etape_1&indice_aid=$indice_aid".add_token_in_url()."\">Import another file</a> |";}
if (isset($is_posted) and ($is_posted=='sans_id_etape_4')) {echo "<a href=\"export_csv_aid.php?is_posted=sans_id_etape_1&indice_aid=$indice_aid".add_token_in_url()."\">Import another file</a> |";}

echo "</p>";

// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV

$long_max = 8000;

if (!isset($is_posted)) {

    $test = mysql_query("SELECT * FROM aid WHERE indice_aid='$indice_aid'");

    $nb_test = mysql_num_rows($test);

    if ($nb_test == 0) {

        // Par sécurité, on efface d'éventuelles données résiduelles dans les tables j_aid_utilisateurs et j_aid_eleves
        $del = mysql_query("DELETE FROM j_aidcateg_super_gestionnaires WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_utilisateurs WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_utilisateurs_gest WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_eleves WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_eleves_resp WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM aid_appreciations WHERE indice_aid='$indice_aid'");

        $is_posted='debut';

    } else {

        if (!isset($confirm) or ($confirm != 'Effacer')) {

            echo "<p><b>CAUTION</b> :  $nom_generique_aid were already enregistré(e)s. The procedure of importation allows the insertion of <b>new data</b> and the < b>update</b > of the existing data. <br /><b>The data already present in GEPI are thus not destroyed by this procedure</b>.<br /><br />
			Click on < b>\"Erase\"</b > if you wish to erase < b>all</b > the
data already data concerning the $nom_generique_aid,<br />Click on <b>\"Continue\"</b> if you wish to preserve the  existing data.</p >";

            echo "<table border=0><tr><td>";

            echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";

            echo add_token_field();

            echo "<input type=hidden name=indice_aid value=$indice_aid />";

            echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Effacer' />";

            echo "</FORM></td><td>";

            echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire2>";

            echo add_token_field();

            echo "<INPUT TYPE=HIDDEN name=is_posted value = 'debut' /> ";

            echo "<input type=hidden name=indice_aid value=$indice_aid />";

            echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Continuer' />";

            echo "</FORM></td></tr></table>";

        } else {

            echo "<p><b>Are you sure  you want to erase all the data concerning $nom_generique_aid ?</b></p>";

            echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";

            echo add_token_field();

            echo "<INPUT TYPE=HIDDEN name=is_posted value = 'debut' /> ";

            echo "<input type=hidden name=indice_aid value=$indice_aid />";

            echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Oui' />";

            echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Non' />";

            echo "</FORM>";
        }
    }
}

if (isset($is_posted) and ($is_posted == 'debut')) {
    //check_token();

    if (isset($confirm) and ($confirm == 'Oui')) {
        check_token(false);

        $del = mysql_query("DELETE FROM aid WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_utilisateurs WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_eleves WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM aid_appreciations WHERE indice_aid='$indice_aid'");
        echo "<p>Data concerning $nom_generique_aid were definitively removed !</p>";
    }
    echo "<p>Choose one of the two following options :</p>";
    echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";

    echo add_token_field();

    echo "<p>--&gt; You have <b></b >definite a single identifier for each $nom_generique_aid.";
    echo "<INPUT TYPE=SUBMIT value = 'Valider' /></p>";
    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_1' /> ";
    echo "<input type=hidden name=indice_aid value=$indice_aid />";
    echo "</FORM>";
    echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire2>";
    echo add_token_field();
    echo "<p>--&gt; You want to leave <b>GEPI</b> define a single identifier for each $nom_generique_aid .";
    echo "<INPUT TYPE=SUBMIT value = 'Valider' /></p>";
    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'sans_id_etape_1' /> ";
    echo "<input type=hidden name=indice_aid value=$indice_aid />";
    echo "</FORM>";
}

//*************************************************************************************************
// Début de la procédure dans laquelle on laisse GEPI définir un identifiant unique pour chaque AID
//*************************************************************************************************

if (isset($is_posted) and ($is_posted == "sans_id_etape_1")) {
    check_token(false);

    echo "<table border=0>";
    //    cas où on importe un fichier ELEVES-AID
    echo "<tr><td><p>Importer un fichier <b>\"ELEVES-$nom_generique_aid\"</b></p></td>";
    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";
    echo add_token_field();
    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'sans_id_etape_2' /> ";
    echo "<input type=hidden name=indice_aid value=$indice_aid />";
    echo "<INPUT TYPE=HIDDEN name=type_import value = 1 /> ";
    echo "<INPUT TYPE=SUBMIT value = Valider />";
    echo "</FORM></td></tr>";
    //    cas où on importe un fichier prof-AID
    echo "<tr><td><p>Importer un fichier <b>\"PROF-$nom_generique_aid\"</b></p></td>";
    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire2>";
    echo add_token_field();
    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'sans_id_etape_2' /> ";
    echo "<input type=hidden name=indice_aid value=$indice_aid />";
    echo "<INPUT TYPE=HIDDEN name=type_import value=2 /> ";
    echo "<INPUT TYPE=SUBMIT value=Valider />";
    echo "</FORM></td></tr>";
    echo "</table>";
}


if (isset($is_posted) and ($is_posted == 'sans_id_etape_2')) {
    check_token(false);

    ?>
    <form enctype="multipart/form-data" action="export_csv_aid.php" method=post name=formulaire>
    <?php
    $csvfile="";
    echo add_token_field();
    ?>
    <p>CSV File  to be imported <a href='help_import.php'>Help </a> : <input TYPE=FILE NAME="csvfile" /></p>
    <input TYPE=HIDDEN name=is_posted value = 'sans_id_etape_3' />
    <input type=hidden name=indice_aid value=<?php echo $indice_aid;?> />
    <input TYPE=HIDDEN name=type_import value = "<?php echo $type_import; ?>" />
    <p>The file to be imported comprises a first heading line, to ignore &nbsp;
    <input TYPE=CHECKBOX NAME="en_tete" VALUE="yes" CHECKED /></p>
    <input TYPE=SUBMIT value = "Valider" /><br />
    </form>
    <?php
    echo "<p>The file of importation must be with the cvs format (separator : semicolon)<br />";
    if ($type_import == 1) {
        echo "The file must contain the two following, obligatory fields :<br />";
        echo "--&gt; <B>IDENTIFIER</B> : the identifier of the student<br />";
        echo "--&gt; <B>Complete name of the activity</B><br /></p>";
    } else if ($type_import == 2) {
        echo "The file must contain the two following, obligatory fields :<br />";
        echo "--&gt; <B>IDENTIFIER</B> : the identifier of the professor<br />";
        echo "--&gt; <B>Complete name of the activity</B><br /></p>";
    }
}

if (isset($is_posted) and ($is_posted == 'sans_id_etape_3')) {
    check_token(false);

	$csvfile = isset($_FILES["csvfile"]) ? $_FILES["csvfile"] : NULL;
   //if($csvfile != "none") {
    if(isset($csvfile)) {
        //$fp = fopen($csvfile, "r");
        $fp = fopen($csvfile['tmp_name'], "r");
        if(!$fp) {
            echo "Impossible to open the CSV file  (".$csvfile['name'].")";
        } else {
            $erreur = 'no';
            //    Dans le cas où on importe un fichier PROF-AID ou ELEVE-AID, on vérifie le login
            $row = 0;
            while(!feof($fp)) {
                if ($en_tete == 'yes') {
                    $data = fgetcsv ($fp, $long_max, ";");
                    $en_tete = 'no';
                    $en_tete2 = 'yes';
                }
                $data = fgetcsv ($fp, $long_max, ";");
                $num = count ($data);
                if ($num == 2) {
                    $row++;
                    //login
                    if ($type_import == 1) {
                        $call_login = mysql_query("SELECT login FROM eleves WHERE login='$data[0]'");
                    } else {
                        $call_login = mysql_query("SELECT login FROM utilisateurs WHERE login='$data[0]'");
                    }
                    $test = mysql_num_rows($call_login);
                    if ($test == 0) {
                        $erreur = 'yes';
                        echo "<p><font color='red'>Error in the file the line $row : $data[0] does not correspond to any identifier GEPI.</font></p>";
                    }
                }
            }
            fclose($fp);
            //

            // On stocke les info du fichier dans une table

            //

            if ($erreur == 'no') {

                $del = mysql_query("delete from tempo2");

                //$fp = fopen($csvfile, "r");
                $fp = fopen($csvfile['tmp_name'], "r");

                $row = 0;

                $erreur_reg = 'no';

                while(!feof($fp)) {

                    if ($en_tete2 == 'yes') {

                        $data = fgetcsv ($fp, $long_max, ";");

                        $en_tete2 = 'no';

                    }

                    $data = fgetcsv ($fp, $long_max, ";");

                    $num = count ($data);

                    if ($num == 2) {

                        $row++;

                        $data[1] = traitement_magic_quotes(corriger_caracteres($data[1]));

                        $query = "INSERT INTO tempo2 VALUES('$data[0]', '$data[1]')";

                        $register = mysql_query($query);

                        if (!$register) {

                            $erreur_reg = 'yes';

                            echo "<p><font color='red'>Error during the recording of the line $row in the temporary table.</font></p>";

                        }

                    }

                }

                fclose($fp);

                if ($erreur_reg == 'no') {

                    // On affiche les aid détectées dans la table tempo2

                    echo "<form enctype='multipart/form-data' action='export_csv_aid.php' method=post >";

                    echo add_token_field();

                    if ($type_import == 1) {

                        echo "<input type=submit value='Record $nom_generique_aid and update the students' />";

                    } else if ($type_import == 2) {

                        echo "<input type=submit value='Record $nom_generique_aid and update the professors' />";

                    } else {

                        echo "<input type=submit value='Record $nom_generique_aid' />";

                    }

                    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'sans_id_etape_4' />";

                    echo "<input type=hidden name=indice_aid value=$indice_aid />";

                    echo "<INPUT TYPE=HIDDEN name=type_import value='$type_import' />";

                    echo "</FORM>";

                    echo "<p>If a $nom_generique_aid already exist in GEPI base , only an update of the data will be carried out in accordance with the data appearing in the file csv</p>";



                    $call_data = mysql_query("SELECT distinct col2 FROM tempo2 WHERE col2!='' ORDER BY col2");

                    $nb_aid = mysql_num_rows($call_data);





                    echo "<table border=1 cellpadding=2 cellspacing=2>";

                    echo "<tr><td><p class=\"small\">$nom_generique_aid :Nom</p></td>";

                    echo "<td><p class=\"small\">Notice</p></td></tr>";

                    $i = "0";

                    while ($i < $nb_aid) {

                        $nom_aid = mysql_result($call_data, $i, "col2");

                        $temp = traitement_magic_quotes(corriger_caracteres($nom_aid));

                        $test = mysql_query("SELECT * FROM aid WHERE (nom = '$temp' and indice_aid='$indice_aid')");

                        $nb_test = mysql_num_rows($test);

                        if ($nb_test == 0) {

                            $mess = "<font color='green'>This activity does not exist in GEPI.</font>";

                        } else {

                            $mess = "<font color='blue'>This activity already exists in GEPI.</font>";

                        }

                        echo "<tr><td><p><b>$nom_aid</b></p></td>";

                        echo "<td><p>$mess</p></td></tr>";

                        $i++;

                    }

                    echo "</table>";



                } else {

                    $del = mysql_query("delete from tempo2");

                    echo "<p>WARNING : One or more errors were detected during the recording of the data in the temporary table: the operation of importation cannot continue !</p>";

                }

            } else {

                echo "<p>WARNING : One or more errors were detected in the file : the operation of importation cannot continue !</p>";

            }

        }

    } else {

        echo "<p>No file was selected !</p>";

    }

}



if (isset($is_posted) and ($is_posted == 'sans_id_etape_4')) {
    check_token(false);

    echo "<p class='bold'>Update of the list of $nom_generique_aid</p>";

    echo "<table border=1 cellpadding=2 cellspacing=2><tr>";

    echo "<td><p class=\"small\">Name of the acticity</p></td>";

    echo "<td><p class=\"small\">Notice</p></td></tr>";

    $call_max = mysql_query("SELECT max(id) max FROM aid WHERE indice_aid='$indice_aid'");

    $max_id = mysql_result($call_max,0,max);

    $call_data = mysql_query("SELECT distinct col2 FROM tempo2 WHERE col2!='' ORDER BY col2");

    $nb_aid = mysql_num_rows($call_data);

    // On enregistre les AID

    $pb_reg = 'no';

    $i = "0";

    while ($i < $nb_aid) {

        $nom_aid = mysql_result($call_data, $i, "col2");

        $temp = traitement_magic_quotes(corriger_caracteres($nom_aid));

        $num_aid = '';

        $test = mysql_query("SELECT * FROM aid WHERE (nom = '$temp' and indice_aid='$indice_aid')");

        $nb_test = mysql_num_rows($test);

        if ($nb_test == 0) {

            $max_id++;

            $reg = mysql_query("INSERT INTO aid SET id = '$max_id', nom='$temp', numero='$num_aid', indice_aid='$indice_aid'");

            if ($reg) {

                $mess = "<font color='green'>The activity was recorded successfully !</font>";

            } else {

                $mess = "<font color='red'>Problem during the recording !</font>";

                $pb_reg = 'yes';

            }

        } else {

            $mess = "<font color='blue'>No the recording: this acticity existed already in GEPI!</font>";

        }

        echo "<tr>";

        echo "<td><p><b>$nom_aid</b></p></td>";

        echo "<td><p>$mess</p></td></tr>";

        $i++;

    }

    echo "</table>";



    if ($pb_reg == 'yes') {

        echo "<p>There was a problem during the recording of $nom_generique_aid, the operation of importation cannot continue : the table of the identifiers for $nom_generique_aid was not updated !</p>";

    } else {

        // initialisation de variables

        if ($type_import == 1) {

            $aid_table = "j_aid_eleves";

            $nom_champ = "login";

        } else {

            $aid_table = "j_aid_utilisateurs";

            $nom_champ = "id_utilisateur";

        }

        // On enregistre les login

        $nb = 0;

        $call_data = mysql_query("SELECT * FROM tempo2");

        $nb_lignes = mysql_num_rows($call_data);

        $pb_reg = "no";

        $i = "0";

        while ($i < $nb_lignes) {

            $champ1 = mysql_result($call_data, $i, "col1");

            if ($type_import == 1) {

                $call_login = mysql_query("SELECT login FROM eleves WHERE login='$champ1'");

            } else {

                $call_login = mysql_query("SELECT login FROM utilisateurs WHERE login='$champ1'");

            }

            $test = mysql_num_rows($call_login);

            if ($test != 0) {

                // cas où un login existe dans la table eleves ou utilisateurs

                // On peut continuer !

                $nom_aid = mysql_result($call_data, $i, "col2");

                $temp = traitement_magic_quotes(corriger_caracteres($nom_aid));

                $call_id = mysql_query("SELECT id FROM aid WHERE (nom = '$temp' and indice_aid='$indice_aid')");

                $id_aid = mysql_result($call_id, 0, "id");

                if ($type_import == 1) {

                    $call_test = mysql_query("SELECT * FROM $aid_table WHERE ($nom_champ='$champ1' and indice_aid='$indice_aid')");

                } else {

                    $call_test = mysql_query("SELECT * FROM $aid_table WHERE ($nom_champ='$champ1' and id_aid='$id_aid' and indice_aid='$indice_aid')");

                }

                $test2 = mysql_num_rows($call_test);

                // pour les élèves : un élève ne peut suivre qu'une seule AID. Si une ligne existe déjà on la met à jour (update)

                // pour les prof : un prof peut être responsable de plusieurs AID, mais on teste qu'il n'y ait pas de lignes 'doublons' dans le fichier j_aid_utilisateurs.

                if ($test2 == 0) {

                    $reg = mysql_query("INSERT INTO $aid_table SET id_aid='$id_aid', $nom_champ = '$champ1', indice_aid='$indice_aid'");

                    if (!$reg) {

                        $pb_reg = "yes";

                    } else {

                        $nb++;

                    }

                } else {

                    if ($type_import == 1) {

                        $reg = mysql_query("UPDATE $aid_table SET id_aid='$id_aid' WHERE ($nom_champ = '$champ1' and indice_aid='$indice_aid')");

                        if (!$reg) {

                            $pb_reg = "yes";

                        } else {

                            $nb++;

                        }

                    }

                }

                $i++;

            }

        }

        if ($type_import == 1) {

            echo "<p class='bold'>Update of the students</p>";

            echo "<p>$nb rows of students were updated in the table of connection <b>student&lt;--&gt;$nom_generique_aid</b> !</p>";

            if ($pb_reg == "yes") {

                echo "<p><font color = 'red'>There were problems of recording for one or more other students !</font></p>";

            }

        } else {

            echo "<p class='bold'>Update of the professors</p>";

            echo "<p>$nb rows of professors were updated in the table of connection <b>Professors&lt;--&gt;$nom_generique_aid</b> !</p>";

            if ($pb_reg == "yes") {

                echo "<p><font color = 'red'>There were problems of recording for one or more other professors !</font></p>";

            }

        }



        $del = mysql_query("delete from tempo2");

    }

}



//*************************************************************************************************

// Fin de la procédure dans laquelle on laisse GEPI définir un identifiant unique pour chaque AID

//*************************************************************************************************



//*************************************************************************************************

// Début de la procédure dans laquelle l'utilisateur définie lui-même un identifiant unique pour chaque AID

//*************************************************************************************************



if (isset($is_posted) and ($is_posted == 'avec_id_etape_1')) {
    check_token(false);

    echo "<table border=0>";

    //    cas où on importe un fichier numéro-AID

    echo "<tr><td><p>Import a file <b>\"$nom_generique_aid - Identifier $nom_generique_aid\"</b></p></td>";

    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";

    echo add_token_field();

    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_2' /> ";

    echo "<input type=hidden name=indice_aid value=$indice_aid />";

    echo "<INPUT TYPE=HIDDEN name=type_import value = 3 /> ";

    echo "<INPUT TYPE=SUBMIT value = Valider />";

    echo "</FORM></td></tr>";

    //    cas où on Importe un fichier ELEVES-N° AID

    echo "<tr><td><p>Import a file <b>\"STUDENTS-Identifier $nom_generique_aid\"</b></p></td>";

    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire2>";

    echo add_token_field();

    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_2' /> ";

    echo "<input type=hidden name=indice_aid value=$indice_aid />";

    echo "<INPUT TYPE=HIDDEN name=type_import value = 1 /> ";

    echo "<INPUT TYPE=SUBMIT value = Valider />";

    echo "</FORM></td></tr>";

    //    cas où on importe un fichier prof-AID

    echo "<tr><td><p>Import a file <b>\"PROF-Identifier $nom_generique_aid\"</b></p></td>";

    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire3>";

    echo add_token_field();

    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_2' /> ";

    echo "<input type=hidden name=indice_aid value=$indice_aid />";

    echo "<INPUT TYPE=HIDDEN name=type_import value = 2 /> ";

    echo "<INPUT TYPE=SUBMIT value = Valider />";

    echo "</FORM></td></tr>";



    echo "</table>";

}





if (isset($is_posted) and ($is_posted == 'avec_id_etape_2')) {
    check_token(false);

    ?>

    <form enctype="multipart/form-data" action="export_csv_aid.php" method=post name=formulaire>

    <?php
    $csvfile="";
    echo add_token_field();
    ?>

    <p>CSV File to import <a href='help_import.php'>Help </a> : <INPUT TYPE=FILE NAME="csvfile" /></p>

    <input TYPE=HIDDEN name=is_posted value = 'avec_id_etape_3' />

    <input type=hidden name=indice_aid value=<?php echo $indice_aid;?> />

    <input TYPE=HIDDEN name=type_import value = "<?php echo $type_import; ?>" />

    <p>The file to import  comprises a first heading line, to ignore&nbsp;

    <input TYPE=CHECKBOX NAME="en_tete" VALUE="yes" CHECKED /></p>

    <input TYPE=SUBMIT value = "Valider" /><br />

    </form>

    <?php

    echo "<p>The file of importation must be in the csv format  (separating:semicolon)<br />";

    if ($type_import == 1) {

        echo "The file must contain the two following, obligatory fields :<br />";

        echo "--&gt; <B>The identifier of the student</b><br />";

        echo "--&gt; <B>The identifier of the activity</B><br /></p>";

    } else if ($type_import == 2) {

        echo "The file must contain the two following, obligatory fields :<br />";

        echo "--&gt; <B>The identifier of the professor</b><br />";

        echo "--&gt; <B>The identifier of the activity</B><br /></p>";

    } else {

        echo "The file must contain the two following, obligatory fields :<br />";

        echo "--&gt; <B>Complete name of the activity</B><br />";

        echo "--&gt; <B>The identifier of the activity</B><br /></p>";

    }

}



if (isset($is_posted) and ($is_posted == 'avec_id_etape_3')) {
    check_token(false);

	$csvfile = isset($_FILES["csvfile"]) ? $_FILES["csvfile"] : NULL;
    //if($csvfile != "none") {
    if(isset($csvfile)) {

        //$fp = fopen($csvfile, "r");
        $fp = fopen($csvfile['tmp_name'], "r");

        if(!$fp) {

            //echo "Impossible d'ouvrir le fichier CSV ($csvfile)";
            echo "Impossible to open CSV file (".$csvfile['name'].")";

        } else {

            $erreur = 'no';

            //

            //    Dans le cas où on importe un fichier PROF-AID ou ELEVE-AID, on vérifie le login

            //  ainsi que l'existence d'une AID corrspondant à chaque identifiant AID

            //

            $row = 0;

            while(!feof($fp)) {

                if ($en_tete == 'yes') {

                    $data = fgetcsv ($fp, $long_max, ";");

                    $en_tete = 'no';

                    $en_tete2 = 'yes';

                }

                $data = fgetcsv ($fp, $long_max, ";");

                $num = count ($data);

                if ($num == 2) {

                    $row++;

                    // vérification du login

                    if ($type_import == 1) {

                        $call_login = mysql_query("SELECT login FROM eleves WHERE login='$data[0]'");

                        $test = mysql_num_rows($call_login);

                    } else if ($type_import == 2) {

                        $call_login = mysql_query("SELECT login FROM utilisateurs WHERE login='$data[0]'");

                        $test = mysql_num_rows($call_login);

                    } else {

                        $test = 1;

                    }

                    if ($test == 0) {

                        $erreur = 'yes';

                        echo "<p><font color='red'>Error in the file to the line $row : $data[0] does not correspond to any identifier GEPI.</font></p>";

                    }

                    //

                    // Vérification sur l'identifiant AID

                    //

                    if (!(preg_match("/^[a-zA-Z0-9_]{1,10}$/", $data[1]))) {

                        $erreur = 'yes';

                        echo "<p><font color='red'>Error in the file at the line: $$row> the identifier $nom_generique_aid is not valid (an identifier must consist of only of numbers, letters and characters of underlining).</font></p>";

                    }

                    $call_aid = mysql_query("SELECT * FROM aid WHERE (id='$data[1]' and indice_aid='$indice_aid')");

                    $test = mysql_num_rows($call_aid);

                    if (($test == 0) and ($type_import != 3)) {

                        // Vérification de l'existence d'une AID correspondant à chaque identifiant AID

                        //

                        $erreur = 'yes';

                        echo "<p><font color='red'>Error in the file at the line $row : the identifier $nom_generique_aid does not correspond to any $nom_generique_aid already recorded .</font></p>";

                    } else if (($test != 0) and ($type_import == 3)) {

                        // Vérification que l'identifiant n'existe pas déjà

                        //

                        $erreur = 'yes';

                        echo "<p><font color='red'>Error in the file at the line $row : the identifier $nom_generique_aid already exist for one(e) $nom_generique_aid already recorded !</font></p>";

                    }

                    // Recherche de doublons sur les identifiants

                    if ($type_import == 3) {

                        $doublons = 'no';

                        $tab_id[$row] = $data[1];

                        for ($k=1;$k<$row;$k++) {

                            if ($data[1] == $tab_id[$k]) {

                                $erreur = 'yes';

                                echo "<p><font color='red'>Error in the file: there are doubled blooms in the identifiers $nom_generique_aid !</font></p>";

                            }

                        }

                    }

                }

            }

            fclose($fp);

            //

            // On stocke les info du fichier dans une table

            //

            if ($erreur == 'no') {

                $del = mysql_query("delete from tempo2");

                //$fp = fopen($csvfile, "r");
                $fp = fopen($csvfile['tmp_name'], "r");

                $row = 0;

                $erreur_reg = 'no';

                while(!feof($fp)) {

                    if ($en_tete2 == 'yes') {

                        $data = fgetcsv ($fp, $long_max, ";");

                        $en_tete2 = 'no';

                    }

                    $data = fgetcsv ($fp, $long_max, ";");

                    $num = count ($data);

                    if ($num == 2) {

                        $row++;

                        $data[0] = traitement_magic_quotes(corriger_caracteres($data[0]));

                        $query = "INSERT INTO tempo2 VALUES('$data[0]', '$data[1]')";

                        $register = mysql_query($query);

                        if (!$register) {

                            $erreur_reg = 'yes';

                            echo "<p><font color='red'>Error during the recording of the line $row in the temporary table.</font></p>";

                        }

                    }

                }

                fclose($fp);



                if ($erreur_reg == 'no') {

                    // On affiche les aid détectées dans la table tempo2

                    echo "<form enctype='multipart/form-data' action='export_csv_aid.php' method=post >";

                    echo add_token_field();

                    if ($type_import != 3) {

                        echo "<input type=submit value='Enregistrer' />";

                        $call_data = mysql_query("SELECT * FROM tempo2 WHERE ((col1 !='') and (col2!='')) ORDER BY col1");

                        $nb_aid = mysql_num_rows($call_data);

                        echo "<table border=1 cellpadding=2 cellspacing=2>";

                        echo "<tr><td><p class=\"small\">Name first name</p></td><td><p class=\"small\">Name of the activity</p></td></tr>";

                        $i = "0";

                        while ($i < $nb_aid) {

                            $login_individu = mysql_result($call_data, $i, "col1");

                            $id_aid = mysql_result($call_data, $i, "col2");

                            if ($type_import == 1) {

                                $call_individus = mysql_query("SELECT nom, prenom FROM eleves WHERE login='$login_individu'");

                            } else {

                                $call_individus = mysql_query("SELECT nom, prenom FROM utilisateurs WHERE login='$login_individu'");

                            }

                            $nom_individu = mysql_result($call_individus, 0, 'nom');

                            $prenom_individu = mysql_result($call_individus, 0, 'prenom');

                            $call_aid = mysql_query("SELECT nom FROM aid WHERE (id='$id_aid' and indice_aid='$indice_aid')");

                            $nom_aid = mysql_result($call_aid, 0, 'nom');



                            echo "<tr><td><p>$nom_individu $prenom_individu</p></td>";

                            echo "<td><p>$nom_aid</p></td></tr>";

                            $i++;

                        }

                        echo "</table>";

                    } else {

                        echo "<input type=submit value='Register $nom_generique_aid' />";

                        $call_data = mysql_query("SELECT DISTINCT * FROM tempo2 WHERE ((col1 !='') and (col2!='')) ORDER BY col1");

                        $nb_aid = mysql_num_rows($call_data);

                        echo "<table border=1 cellpadding=2 cellspacing=2>";

                        echo "<tr><td><p class=\"small\">Identifier</p></td><td><p class=\"small\">Name of the activity</p></td></tr>";

                        $i = "0";

                        while ($i < $nb_aid) {

                            $nom_aid = mysql_result($call_data, $i, "col1");

                            $id_aid = mysql_result($call_data, $i, "col2");

                            echo "<tr><td><p><b>$id_aid</b></p></td>";

                            echo "<td><p><b>$nom_aid</b></p></td></tr>";

                            $i++;

                        }

                        echo "</table>";

                    }

                    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_4' />";

                    echo "<input type=hidden name=indice_aid value=$indice_aid />";

                    echo "<INPUT TYPE=HIDDEN name=type_import value='$type_import' />";

                    echo "</FORM>";

                } else {

                    $del = mysql_query("delete from tempo2");

                    echo "<p>WARNING : One or more errors were detected during the recording of the data in the temporary table : the operation of importation cannot continue !</p>";

                }



            } else {

                echo "<p>WARNING : One or more errors were detected in the file: the operation of importation cannot continue !</p>";

            }

        }

    } else {

        echo "<p>No file was selected!</p>";

    }

}



if (isset($is_posted) and ($is_posted == 'avec_id_etape_4')) {
    check_token(false);

    if ($type_import == 3) {

        $call_data = mysql_query("SELECT DISTINCT * FROM tempo2 WHERE ((col1 !='') and (col2!='')) ORDER BY col1");

        $nb_aid = mysql_num_rows($call_data);

        // On enregistre les AID

        $i = "0";

        while ($i < $nb_aid) {

            $nom_aid = mysql_result($call_data, $i, "col1");

            $temp = traitement_magic_quotes(corriger_caracteres($nom_aid));

            $id_aid = mysql_result($call_data, $i, "col2");

            $test = mysql_query("SELECT * FROM aid WHERE (id='$id_aid' and indice_aid='$indice_aid')");

            $nb_test = mysql_num_rows($test);

            if ($nb_test == 0) {

                $reg = mysql_query("INSERT INTO aid SET id = '$id_aid', nom='$temp', numero='$id_aid', indice_aid='$indice_aid'");

                if ($reg) {

                    echo "<p><font color='green'>The activity $nom_aid was recorded successfully !</font></p>";

                } else {

                    echo "<p><font color='red'>There was a problem during the recording of the activity $nom_aid  !</font></p>";

                }

            } else {

                echo "<p><font color='red'>The activity $nom_aid was not recorded, because one  $nom_generique_aid having the same identifier already exists in the base !</font></p>";

            }

            $i++;

        }

    } else {

        // initialisation de variables

        if ($type_import == 1) {

            $aid_table = "j_aid_eleves";

            $nom_champ = "login";

        } else {

            $aid_table = "j_aid_utilisateurs";

            $nom_champ = 'id_utilisateur';

        }

        // On enregistre les login

        $nb = 0;

        $call_data = mysql_query("SELECT * FROM tempo2");

        $nb_lignes = mysql_num_rows($call_data);

        $pb_reg = "no";

        $i = "0";

        while ($i < $nb_lignes) {

            $champ1 = mysql_result($call_data, $i, "col1");

            if ($type_import == 1) {

                $call_login = mysql_query("SELECT login FROM eleves WHERE login='$champ1'");

            } else {

                $call_login = mysql_query("SELECT login FROM utilisateurs WHERE login='$champ1'");

            }

            $test = mysql_num_rows($call_login);

            if ($test != 0) {

                // cas où un login existe dans la table eleves ou utilisateurs

                // On peut continuer !

                $id_aid = mysql_result($call_data, $i, "col2");

                $call_aid = mysql_query("SELECT * FROM aid WHERE (id = '$id_aid' and indice_aid='$indice_aid')");

                $test1 = mysql_num_rows($call_aid);

                if ($test1 != 0) {

                    if ($type_import == 1) {

                        $call_test = mysql_query("SELECT * FROM $aid_table WHERE ($nom_champ='$champ1' and indice_aid='$indice_aid')");

                    } else {

                        $call_test = mysql_query("SELECT * FROM $aid_table WHERE ($nom_champ='$champ1' and id_aid='$id_aid' and indice_aid='$indice_aid')");

                    }

                    $test2 = mysql_num_rows($call_test);

                    // pour les élèves : un élève ne peut suivre qu'une seule AID. Si une ligne existe déjà on la met à jour (update)

                    // pour les prof : un prof peut être responsable de plusieurs AID, mais on teste qu'il n'y ait pas de lignes 'doublons' dans le fichier j_aid_utilisateurs.

                    if ($test2 == 0) {

                        $reg = mysql_query("INSERT INTO $aid_table SET id_aid='$id_aid', $nom_champ = '$champ1', indice_aid='$indice_aid'");

                        if (!$reg) {

                            $pb_reg = "yes";

                        } else {

                            $nb++;

                        }

                    } else {

                        if ($type_import == 1) {

                            $reg = mysql_query("UPDATE $aid_table SET id_aid='$id_aid' WHERE ($nom_champ = '$champ1' and indice_aid='$indice_aid')");

                            if (!$reg) {

                                $pb_reg = "yes";

                            } else {

                                $nb++;

                            }

                        }

                    }

                }

            }

            $i++;

        }

    }

    if ($type_import == 1) {

        echo "<p class='bold'>Update of the students</p>";

        echo "<p>$nb pupils were updated in the table of connection <b>students&lt;--&gt;$nom_generique_aid</b> !</p>";

        if ($pb_reg == "yes") {

            echo "<p><font color = 'red'>There were problems of recording for one or more other pupils !</font></p>";

        }

    } else if ($type_import == 2) {

        echo "<p class='bold'>Update of the professors</p>";

        echo "<p>$nb professors were updated in the table of connection <b>Professors&lt;--&gt;$nom_generique_aid</b> !</p>";

        if ($pb_reg == "yes") {

            echo "<p><font color = 'red'>There were problems of recording for one or more other professors !</font></p>";

        }

    }

    $del = mysql_query("delete from tempo2");

}

//*************************************************************************************************

// Fin de la procédure dans laquelle l'utilisateur définie lui-même un identifiant unique pour chaque AID

//*************************************************************************************************


require("../lib/footer.inc.php");
?>