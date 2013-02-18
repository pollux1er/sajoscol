<?php
/*
 * Last modification  : 14/03/2005
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//**************** EN-TETE *****************
$titre_page = "Outil de gestion | Importation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV
$long_max = 8000;
if (!isset($is_posted) or (isset($is_posted) and ($is_posted == 'R')) ) {
    ?><p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return </a>| <a href='javascript:centrerpopup("help_import.php",600,480,"scrollbars=yes,statusbar=no,resizable=yes")'>Help</a></p>
    <p><b>Significant remark</b> : you will import in base GEPI some data "student" from a file in the format csv (separator semicolon).<br />
    They can be new students or students already present in the base. In this last case, the existing data will be crushed by the data present in the file to import.
    <br /><b>Caution </b> : certain modifications in the course of year on students already
present in the base can involve inconsistencies in the bases and consequently a faulty operation of the application.

    </p>

    <form enctype="multipart/form-data" action="import_csv.php" method=post name=formulaire>
    <?php
		$csv_file="";
		echo add_token_field();
	?>
    <p>File CSV to be imported : <input TYPE=FILE NAME="csv_file"></p>
    <input TYPE=HIDDEN name=is_posted value = 1>
    <p>The file to be imported comprises a first heading line, to ignore&nbsp;
    <input TYPE=CHECKBOX NAME="en_tete" VALUE="yes" CHECKED></p>
    <input TYPE=SUBMIT value = "Valider"><br />
    </form>
    <?php
    echo "<p>The file of importation must be with the format csv (separator: semicolon)<br />";
    echo "The file must contain the various following fields, all obligatory :<br />";
    echo "--> <B>IDENTIFIER</B >: the identifier of the student (".$longmax_login." caractères maximum)<br />";
    echo "--> <B>Name</B><br />";
    echo "--> <B>First name</B><br />";
    echo "--> <B>Sex</B>  : F ou M<br />";
    echo "--> <B>Date of birth</B> : jj/mm/aaaa<br />";
    echo "--> <B>Class (fac.)</B> : the short name of a class already defined in base GEPI or the character - if the student is not affected to a class.<br />";
    echo "--> <B>Regim</B> : d/p (school luncher) ext. (external) int. (intern) ou i-e (externed intern)<br />";
    echo "--> <B>Doubling</B> : R (for a doubling)  - (for a not-doubling)<br />";
    echo "--> <B>".ucfirst(getSettingValue("gepi_prof_suivi"))."</B> : the identifier of a ".getSettingValue("gepi_prof_suivi")." already defined in base GEPI or the character - if the student does not have ".getSettingValue("gepi_prof_suivi").".<br />";
    echo "--> <B>Identifier of the school of origin </B> : the code RNE identifying each school and already defined in base GEPI, or character - if the school is not known.<br /></p>";
} else {
    ?><p class=bold><a href="import_csv.php?is_posted=R"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return </a>| <a href='javascript:centrerpopup("help_import.php",600,480,"scrollbars=yes,statusbar=no,resizable=yes")'>Help</a></p>
    <?php

	check_token(false);

    $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
    echo "<form enctype='multipart/form-data' action='traitement_csv.php' method=post >";

	echo add_token_field();

    if($csv_file['tmp_name'] != "") {
        $fp = @fopen($csv_file['tmp_name'], "r");
        if(!$fp) {
            echo "Impossible to open CSV file .";
        } else {
            $row = 0;
            echo "<table border=1><tr><td><p>Identifier</p></td><td><p>Name</p></td><td><p>First name</p></td><td><p>Sex</p></td><td><p>Date of birth</p></td><td><p>Class</p></td><td><p>Regim</p></td><td><p>Doubling</p></td><td><p>".ucfirst(getSettingValue("gepi_prof_suivi"))."</p></td><td><p>Id. school.</p></td></tr>";
            $valid = 1;
            while(!feof($fp)) {
                if (isset($en_tete) and ($en_tete=='yes')) {
                    $data = fgetcsv ($fp, $long_max, ";");
                    $en_tete = 'no';
                }
                $data = fgetcsv ($fp, $long_max, ";");
                $num = count ($data);
                if ($num == 10) {
                $row++;
                echo "<tr>";
                $test_login_existant = '';
                $login_exist = '';
                $login_valeur = '';
                for ($c=0; $c<$num; $c++) {
                    switch ($c) {
                    case 0:
                        //login
                        if (preg_match ("/^[a-zA-Z0-9_]{1,".$longmax_login."}$/", $data[$c])) {
                            $reg_login = "reg_".$row."_login";
                            $reg_statut = "reg_".$row."_statut";
                            $data[$c] =    strtoupper($data[$c]);
                            $call_login = mysql_query("SELECT login FROM eleves WHERE login='$data[$c]'");
                            $test = mysql_num_rows($call_login);
                            if ($test != 0) {
                                echo "<td><p><font color = red>$data[$c]</font></p></td>";
                                echo "<INPUT TYPE=HIDDEN name='$reg_statut' value=existant>";
                                $test_login_existant = "oui";
                                $login_exist = "oui";
                                $login_valeur = $data[$c];
                            } else {
                                echo "<td><p>$data[$c]</p></td>";
                                echo "<INPUT TYPE=HIDDEN name='$reg_statut' value=nouveau>";
                                $login_exist = "non";
                           }
                            $data_login = urlencode($data[$c]);
                            echo "<INPUT TYPE=HIDDEN name='$reg_login' value = $data_login>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 1:
                        //Nom
                        $test_nom_prenom_existant = 'no';
                        if (preg_match ("/^.{1,30}$/", $data[$c])) {
                            $temp = $c+1;
                            $call_nom = mysql_query("SELECT nom FROM eleves WHERE (nom='$data[$c]' and prenom = '$data[$temp]')");
                            $test = @mysql_num_rows($call_nom);
                            if ($test != 0) {
                                $test_nom_prenom_existant = 'yes';
                                echo "<td><p><font color = blue>$data[$c]</font></p></td>";
                            } else {
                                echo "<td><p>$data[$c]</p></td>";
                            }
                            $reg_nom = "reg_".$row."_nom";
                            $data_nom = urlencode($data[$c]);
                            echo "<INPUT TYPE=HIDDEN name='$reg_nom' value = $data_nom>";
                        } else {
                        echo "<td><font color = red>???</font></td>";
                        }
                        break;
                    case 2:
                        //Prenom
                        if (preg_match ("/^.{1,30}$/", $data[$c])) {
                            if ($test_nom_prenom_existant == 'yes') {
                                echo "<td><p><font color = blue>$data[$c]</font></p></td>";
                            } else {
                                echo "<td><p>$data[$c]</p></td>";
                            }
                            $reg_prenom = "reg_".$row."_prenom";
                            $data_prenom = urlencode($data[$c]);
                            echo "<INPUT TYPE=HIDDEN name='$reg_prenom' value = $data_prenom>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 3:
                        // Sexe
                        $data[$c] =    strtoupper($data[$c]);
                        if (preg_match ("/^[MF]$/", $data[$c])) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_sexe = "reg_".$row."_sexe";
                            echo "<INPUT TYPE=HIDDEN name='$reg_sexe' value = $data[$c]>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 4:
                        // Date de naissance
                        if (preg_match ("#^[0-3]{1}[0-9]{1}[/]{1}[0-1]{1}[0-9]{1}[/]{1}[0-9]{4}$#", $data[$c])) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_naissance = "reg_".$row."_naissance";
                            echo "<INPUT TYPE=HIDDEN name='$reg_naissance' value = $data[$c]>";

                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 5:
                        //Classe
                        if ($data[$c] == '-') {
                            if ($login_exist == "non") {
                                $valeur_classe='-';
                            } else {
                                $test_classe = mysql_query("SELECT * FROM j_eleves_classes WHERE login='$login_valeur'");
                                $result_test = mysql_num_rows($test_classe);
                                if ($result_test == 0) {
                                    $valeur_classe='-';
                                } else {
                                    $valeur_classe='????';
                                }
                            }
                        } else {
                            $call_classe = mysql_query("SELECT id FROM classes WHERE classe='$data[$c]'");
                            $test = mysql_num_rows($call_classe);
                            if ($test == 0) {
                                $valeur_classe='????';
                            } else {
                                $id_classe=@mysql_result($call_classe,0,id);
                                if ($login_exist == "non") {
                                    $valeur_classe = $data[$c];
                                } else {
                                    $test_classe = mysql_query("SELECT * FROM j_eleves_classes WHERE (login='$login_valeur' and id_classe='$id_classe')");
                                    $result_test = mysql_num_rows($test_classe);
                                    if ($result_test == 0) {
                                        $valeur_classe='????';
                                    } else {
                                        $valeur_classe = $data[$c];
                                    }
                                }
                            }
                        }
                        if ($valeur_classe != '????') {
                            echo "<td><p>$valeur_classe</p></td>";
                            $reg_classe = "reg_".$row."_classe";
                            echo "<INPUT TYPE=HIDDEN name='$reg_classe' value = $valeur_classe>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 6:
                        //Régime
                        $data[$c] =    strtolower($data[$c]);
                        if (preg_match ("#^(d/p|ext.|int.|i-e)$#", $data[$c])) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_regime = "reg_".$row."_regime";
                            echo "<INPUT TYPE=HIDDEN name='$reg_regime' value = $data[$c]>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;

                   case 7:
                        // Doublant
                        $data[$c] =    strtoupper($data[$c]);
                        if (preg_match ("/^[R\-]{1}$/", $data[$c])) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_doublant = "reg_".$row."_doublant";
                            echo "<INPUT TYPE=HIDDEN name='$reg_doublant' value = $data[$c]>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 8:
                        //Prof de suivi
                        if (($valeur_classe == '????') or ($valeur_classe == '-')) {
                            // si la classe n'est pas définie, le professeur de suivi ne peut pas l'être non plus !
                            if ($data[$c] != '-') {
                                $valeur_prof = '????';
                            } else {
                                $valeur_prof = '-';
                            }
                        } else {
                            $call_prof = mysql_query("SELECT * FROM utilisateurs u, j_groupes_professeurs jgp, j_groupes_classes jgc WHERE (" .
                            		"u.login = '$data[$c]' AND " .
                            		"u.login = jgp.id_professeur and " .
                            		"jgp.id_groupe = jgc.id_groupe and " .
                            		"jgc.id_classe = '$id_classe' )");
                            $test = mysql_num_rows($call_prof);
                            if (($test != 0)  or ($data[$c] == '-')) {
                                $valeur_prof = $data[$c];
                            } else {
                                $valeur_prof = '????';
                            }
                        }
                        if ($valeur_prof != '????') {
                            echo "<td><p>$valeur_prof</p></td>";
                            $reg_prof_suivi = "reg_".$row."_prof_suivi";
                            $valeur_prof = urlencode($valeur_prof);
                            echo "<INPUT TYPE=HIDDEN name='$reg_prof_suivi' value = $valeur_prof>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                        case 9:
                        //établissement d'origine
                        $call_etab = mysql_query("SELECT * FROM etablissements WHERE id = '$data[$c]'");
                        $test = mysql_num_rows($call_etab);
                        if (($test != 0) or ($data[$c] == '-')) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_etab = "reg_".$row."_etab";
                            $data_etab = urlencode($data[$c]);
                            echo "<INPUT TYPE=HIDDEN name='$reg_etab' value = $data_etab>";

                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    }
                }
                echo "</tr>";
                }
            }
            fclose($fp);
            echo "</table>";
            echo "<p>First phase of the importation : $row entrées importées !</p>";
            if ($row > 0) {
                if ($test_login_existant == "oui") {
                    echo "<p>--> The identifiers which appear in red correspond to identifiers already existing in base GEPI. The existing data will thus be crushed by the data present in the file to import !</p>";
                }
                if ($test_nom_prenom_existant == 'yes') {
                    echo "<p>--> The names and first names which appear in blue correspond to students already present in base GEPI and carrying the same names and first names.
                    <br />If the new identifier is different, a new student will be creates. If not, the data of GEPI will be modified. </p>";
                }
                if ($valid == '1') {
                    echo "<input type=submit value='Enregistrer les données'>";
                    echo "<INPUT TYPE=HIDDEN name=nb_row value = $row>";
                    echo "</FORM>";
                } else {
                    echo "<p>WARNING : Symbols ??? mean that the field in question is not valid. The operation of importation of the data cannot continue normally. Please correct the file to import or carry out the operations necessary in base GEPI !<br /></p>";
                    echo "</FORM>";
                }
            } else {
                echo "<p>The importation failed !</p>";
            }
        }
    } else {
        echo "<p>No file was selected !</p>";
    }
}

require("../lib/footer.inc.php");
?>