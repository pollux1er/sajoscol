<?php
/*
 * $Id: import_note_app.php 6609 2011-03-03 15:06:35Z crob $
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



$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
} else {
	$current_group = false;
}

$periode_num = isset($_POST['periode_num']) ? $_POST['periode_num'] : (isset($_GET['periode_num']) ? $_GET['periode_num'] : NULL);
if (!is_numeric($periode_num)) $periode_num = 0;

if ($_SESSION['statut'] != "secours") {
    if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
        $mess=rawurlencode("Vous n'�tes pas professeur de cet enseignement !");
        header("Location: index.php?msg=$mess");
        die();
    }
}

include "../lib/periodes.inc.php";

//**************** EN-TETE *****************
$titre_page = "Seizure of the averages and appreciations | Importation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// $long_max : doit �tre plus grand que la plus grande ligne trouv�e dans le fichier CSV
$long_max = 8000;

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return reception seized</a>";
//====================================
if($_SESSION['statut']=='professeur'){
	//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";


    $tab_groups = get_groups_for_prof($_SESSION["login"],"classe puis mati�re");
    //$tab_groups = get_groups_for_prof($_SESSION["login"]);

	if(!empty($tab_groups)) {
		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		//foreach($tab_groups as $tmp_group) {
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if($tab_groups[$loop]['id']==$id_groupe){
				$temoin_tmp=1;
				if(isset($tab_groups[$loop+1])){
					$id_grp_suiv=$tab_groups[$loop+1]['id'];
				}
				else{
					$id_grp_suiv=0;
				}
			}
			if($temoin_tmp==0){
				$id_grp_prec=$tab_groups[$loop]['id'];
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_num=$periode_num";
				echo "'>Preceding teaching</a>";
			}
		}
		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num";
				echo "'>Following teaching</a>";
				}
		}
	}
	// =================================
}
//====================================
echo "</p>\n";

echo "<p><span class = 'grand'>First phase of importation of the averages and appreciations </span>";
//echo "<p class = 'bold'>Groupe : " . $current_group["description"] ." (" . $current_group["classlist_string"] . ")| Mati�re : " . $current_group["matiere"]["nom_complet"] . " | P�riode : $nom_periode[$periode_num]</p>";
echo "<p class = 'bold'>Groupe : " . htmlentities($current_group["description"]) ." (" . $current_group["classlist_string"] . ")| Subject : " . htmlentities($current_group["matiere"]["nom_complet"]) . " | Period : $nom_periode[$periode_num]";
echo "</p>\n";


if (!isset($is_posted)) {
    ?>
    <form enctype="multipart/form-data" action="import_note_app.php" method=post name=formulaire>
    <?php
		$csv_file="";
		echo add_token_field();
	?>
    <p>File CSV to be imported : <input type='file' name="csv_file" />    <input type='submit' value='Ouvrir' /></p>
    <p>If the file to be imported included a first heading line (not empty)
to be unaware of, < Br/> shoot the box opposite&nbsp;
    <input type='checkbox' name="en_tete" value="yes" checked /></p>
    <input type='hidden' name=is_posted value = 1 />
    <?php
    echo "<input type='hidden' name='id_groupe' value='" . $id_groupe . "' />\n";
    echo "<input type='hidden' name='periode_num' value='" . $periode_num . "' />\n";
    ?>
    </form>
    <?php
    echo "<p>You decided to import a file of averages directly and/or of appreciations. The file of importation must be with the format csv (separator : semicolon) and must contain the three following fields :<br />\n";
    echo "--> <B>IDENTIFIER</B> : Identifier GEPI of the student(<b>See the lower explanations</b>).<br />\n";
    echo "--> <B>NOTE</B> : note between 0 and 20 with the point or the comma like decimal symbol.<br />Other possible codes (without the quotation marks) : \"<b>abs</b>\" for \"absent\", \"<b>disp</b>\" for \"exempted\", \"<b>-</b>\" for absence of note.<br />If this field is empty, There will not  be modification of the note
already recorded in GEPI for the student in question.<br />\n";
    echo "--> <B>Appreciation</B> : the text of the appreciation of the student.<br />If this field is empty, There will not  be modification of the
appreciation recorded in GEPI for the student in question.</p>\n";
    echo "<p>To constitute the file of importation you need to know the
identifier <b>GEPI</b> of each student. You can download:</p>\n";
    echo "<ul>\n";
    echo "<li>the file student (identifier GEPI, without name and first name) in <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;champs=3&amp;ligne_entete=y&amp;mode=Id_Note_App'><b>clicking here</b></a></li>\n";
    echo "<li>or the file student (name - first name - identifier GEPI) in <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;champs=5&amp;ligne_entete=y&amp;mode=Nom_Prenom_Id_Note_App'><b>clicking here</b></a><br />(<i>this second file is not directly adapted to the importation<br />(it will be necessary to remove the columns Name and First name of them
before importation)</i>)</li>\n";
    echo "</ul>\n";

    echo "<p>Once downloaded, use your usual spreadsheet to open this file by
specifying that the type of file is csv with semicolon like separator .</p>\n";

}
if (isset($is_posted )) {
	check_token();

    $non_def = 'no';
    $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
    echo "<form enctype='multipart/form-data' action='traitement_csv.php' method=post >";
	echo add_token_field();
    if($csv_file['tmp_name'] != "") {
        echo "<p><b>Caution</b>, the data are not recorded yet in base GEPI. You must confirm the
importation (button in bottom of the page) !</p>";

        $fp = @fopen($csv_file['tmp_name'], "r");
        if(!$fp) {
            echo "Impossible to open the file CSV";
        } else {
            $row = 0;
            echo "<table class='boireaus'>\n<tr>\n<th><p class='bold'>IDENTIFIANT</p></th>\n<th><p class='bold'>Name</p></th>\n<th><p class='bold'>First name</p></th>\n<th><p class='bold'>Note</p></th>\n<th><p class='bold'>Appreciation</p></th>\n</tr>\n";
            $valid = 1;
			$alt=1;
            while(!feof($fp)) {
                if (isset($en_tete)) {
                    $data = fgetcsv ($fp, $long_max, ";");
                    unset($en_tete);
                }
                $data = fgetcsv ($fp, $long_max, ";");
                 $num = count ($data);
                // On commence par rep�rer les lignes qui comportent 2 ou 3 champs tous vides de fa�on � ne pas les retenir
                if (($num == 2) or ($num == 3)) {
                    $champs_vides = 'yes';
                    for ($c=0; $c<$num; $c++) {
                        if ($data[$c] != '') {
                            $champs_vides = 'no';
                        }
                    }
                }
                // On ne retient que les lignes qui comportent 2 ou 3 champs dont au moins un est non vide
                if ((($num == 3) or ($num == 2)) and ($champs_vides == 'no')) {
                    $alt=$alt*(-1);
					$row++;
                    echo "<tr class='lig$alt'>\n";
                    for ($c=0; $c<$num; $c++) {
                        $col3 = '';
                        $reg_app = '';
                        $data_app = '';
                        switch ($c) {
                        case 0:
                            //login
                            $reg_login = "reg_".$row."_login";
                            $reg_statut = "reg_".$row."_statut";
                            $call_login = mysql_query("SELECT * FROM eleves WHERE login='" . $data[$c] . "'");
                            $test = @mysql_num_rows($call_login);
                            if ($test != 0) {
                                $nom_eleve = @mysql_result($call_login, 0, "nom");
                                $prenom_eleve = @mysql_result($call_login, 0, "prenom");

                                //
                                // Si l'�l�ve ne suit pas la mati�re
                                //
                                if (in_array($data[$c], $current_group["eleves"][$periode_num]["list"]))  {
                                    echo "<td><p>$data[$c]</p></td>\n";
                                } else {
                                    echo "<td><p><font color = red>* $data[$c] ??? *</font></p></td>\n";
                                    $valid = 0;
                                }
                                echo "<td><p>$nom_eleve</p></td>\n";
                                //echo "<td><p>$prenom_eleve</p></td>";
                                echo "<td><p>$prenom_eleve</p>";
                                $data_login = urlencode($data[$c]);
                                echo "<input type='hidden' name='$reg_login' value=\"$data_login\" />";
                                echo "</td>\n";
                            } else {
                                echo "<td><font color = red>???</font></td>\n";
                                echo "<td><font color = red>???</font></td>\n";
                                echo "<td><font color = red>???</font></td>\n";
                                echo "<td><font color = red>???</font></td>\n";
                                $valid = 0;
                            }
                            break;
                        case 1:
                            // Note
                            if (preg_match ("/^[0-9\.\,]{1,}$/", $data[$c])) {
                                $data[$c] = str_replace(",", ".", "$data[$c]");
                                $test_num = settype($data[$c],"double");
                                if ($test_num) {
                                    if (($data[$c] >= 0) and ($data[$c] <= 20)) {
                                        //echo "<td><p>$data[$c]</p></td>";
                                        echo "<td><p>$data[$c]</p>";
                                        $reg_note = "reg_".$row."_note";
                                        echo "<input type='hidden' name='$reg_note' value=\"$data[$c]\" />";
                                        echo "</td>\n";
                                    } else {
                                        echo "<td><font color = red>???</font></td>\n";
                                        $valid = 0;
                                    }
                                } else {
                                    echo "<td><font color = red>???</font></td>\n";
                                    $valid = 0;
                                }
                            } else {
                                $tempo = strtolower($data[$c]);
                                if (($tempo == "disp") or ($tempo == "abs") or ($tempo == "-")) {
                                    //echo "<td><p>$data[$c]</p></td>";
                                    echo "<td><p>$data[$c]</p>\n";
                                    $reg_note = "reg_".$row."_note";
                                    echo "<input type='hidden' name='$reg_note' value=\"$data[$c]\" />";
                                    echo "</td>\n";
                                } else if ($data[$c] == "") {
                                    //echo "<td><p><font color = green>ND</font></p></td>";
                                    echo "<td><p><font color = green>ND</font></p>";
                                    $reg_note = "reg_".$row."_note";
                                    echo "<input type='hidden' name='$reg_note' value='' />";
                                    echo "</td>\n";
                                    $non_def = 'yes';
                                } else {
                                    echo "<td><font color = red>???</font></td>\n";
                                    $valid = 0;
                                }
                            }
                            break;
                        case 2:
                            // Appr�ciation
							$non_def='';
                            if ($data[$c] == "") {
                                $col3 = "<font color = green>ND</font>";
                                $non_def = 'yes';
                                $data_app = '';
                            } else {
								// =====================================================
								// L'export CSV g�n�r� par le fichier ODS remplace les ; par des |POINT-VIRGULE|
								// pour ne pas provoquer de probl�me avec le s�parateur ; du CSV
								// AJOUT: boireaus
								//echo "<td>\$data[$c]=$data[$c]</td>";
								//$data[$c]=my_ereg_replace("|POINT-VIRGULE|",";",$data[$c]);
								//$data[$c]=my_ereg_replace("\|POINT-VIRGULE\|",";",$data[$c]);
								$data[$c]=str_replace("|POINT-VIRGULE|",";",$data[$c]);
								// =====================================================
                                $col3 = $data[$c];
                                $data_app = urlencode($data[$c]);
                            }
                            $reg_app = "reg_".$row."_app";
//                            echo "<INPUT TYPE=HIDDEN name='$reg_app' value = $data_app>";
								echo "<td><p>$col3</p>";
								if($non_def!='yes'){
									echo "<input type='hidden' name='$reg_app' value=\"$data_app\" />";
								}
								//echo "</td>\n</tr>\n";
								echo "</td>\n";
                            break;
                        }
                    }
                    //echo "<td><p>$col3</p>"</td></tr>";
					/*
                    echo "<td><p>$col3</p>";
                    echo "<INPUT TYPE=HIDDEN name='$reg_app' value = $data_app />";
                    echo "</td>\n</tr>\n";
					*/
                    echo "</tr>\n";
                // fin de la condition "if ($num == 3)"
                }

            // fin de la boucle "while(!feof($fp))"
            }
            fclose($fp);
            echo "</table>\n";
            echo "<p>First phase of the importation : $row imported entries !</p>\n";
            if ($row > 0) {
                if ($valid == '1') {
                    echo "<input type='hidden' name='nb_row' value=\"$row\" />\n";
                    echo "<input type='hidden' name='id_groupe' value=\"$id_groupe\" />\n";
                    echo "<input type='hidden' name='periode_num' value=\"$periode_num\" />\n";
                    echo "<input type='submit' value='Enregistrer les donn�es' />\n";
                    echo "</form>\n";
                    ?>
                    <script type="text/javascript" language="javascript">
                    <!--
                    alert("Caution, the data are not recorded yet in base GEPI. You must
confirm the importation (button in bottom of the page) !");
                    //-->
                    </script>
                    <?php
                } else {
                    echo "<p class='bold'>WARNING: Symbols <font color=red>???</font> mean that the field in question is not valid. The operation of importation of the data cannot continue normally. Please correct the file to be imported <br /></p>\n";
                    echo "</form>\n";
                }
                if ($non_def == 'yes') {
                    echo "<p class='bold'>Symbols <font color=green>ND</font> mean that the field in question will be ignored. There will not  be thus modification of the existing data in the base
of GEPI.<br /></p>\n";
                }
            } else {
                echo "<p>The importation failed !</p>\n";
            }
        }
    // suite de la condition "if($csv_file != "none")"
    } else {
        echo "<p>No file was selected !</p>\n";
    // fin de la condition "if($csv_file != "none")"
    }
}
require("../lib/footer.inc.php");
?>
