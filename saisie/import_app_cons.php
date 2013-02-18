<?php
/*
 * $Id: import_app_cons.php 6611 2011-03-03 15:23:08Z crob $
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
include "../lib/periodes.inc.php";

// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
   die("Insufficient rights to carry out this operation");
}

// On teste si le service scolarité peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
   die("Insufficient rights to carry out this operation");
}


$themessage  = 'Information was modified. Want you to really leave without recording ?';
//**************** EN-TETE *****************
$titre_page = "Seizure of the appreciations of the council | Importation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV
$long_max = 8000;

echo "<form enctype='multipart/form-data' action='import_app_cons.php' method='post' name='form1'>\n";
echo "<input type='hidden' name='periode_num' value=\"$periode_num\" />\n";

echo "<p class='bold'><a href='saisie_avis.php'><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a>\n";

// ===========================================
// Ajout lien classe précédente / classe suivante
if($_SESSION['statut']=='scolarite'){
	$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
}
elseif($_SESSION['statut']=='professeur'){
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
}
elseif($_SESSION['statut']=='cpe'){
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
		p.id_classe = c.id AND
		jec.id_classe=c.id AND
		jec.periode=p.num_periode AND
		jecpe.e_login=jec.login AND
		jecpe.cpe_login='".$_SESSION['login']."'
		ORDER BY classe";
}

$cpt_classe=0;
$num_classe=-1;
$chaine_options_classes="";
$res_class_tmp=mysql_query($sql);
if(mysql_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;
	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";

			$num_classe=$cpt_classe;

			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}

		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}
		$cpt_classe++;
	}
}
// =================================
if(isset($id_class_prec)){
	if($id_class_prec!=0){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec&amp;periode_num=$periode_num'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">Preceding Class</a>\n";
	}
}
if($chaine_options_classes!="") {
	echo " | <select name='id_classe' id='form1_id_classe'";
	//echo " onchange=\"document.forms['form1'].submit();\"";
	echo " onchange=\"confirm_changement_classe(change, '$themessage');\"";
	echo ">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}
if(isset($id_class_suiv)){
	if($id_class_suiv!=0){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv&amp;periode_num=$periode_num'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">Next class</a>\n";
	}
}
echo "</p>\n";
echo "</form>\n";

echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('form1_id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";

//fin ajout lien classe précédente / classe suivante
// ===========================================


$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_classe, "0", "classe");
echo "<p><span class = 'grand'>First phase of importation of the appreciations </span>";
echo "<p class = 'bold'>Classe : $classe | Period : $nom_periode[$periode_num]</p>";

if (!isset($is_posted)) {
    ?>
    <form enctype="multipart/form-data" action="import_app_cons.php" method=post name=formulaire>
    <?php
		$csv_file=""; 
		echo add_token_field();
	?>
    <p>File CSV to be imported : <input type='file' name="csv_file" />    <input type='submit' value='Ouvrir' /></p>
    <p>If the file to be imported comprises a first heading line (not empty) to be unaware of, <br />tick the box opposite&nbsp;
    <input type='checkbox' name="en_tete" value="yes" checked /></p>
    <input type='hidden' name='is_posted' value='1' />
    <?php
    echo "<input type='hidden' name='id_classe' value=\"$id_classe\" />\n";
    echo "<input type='hidden' name='periode_num' value=\"$periode_num\" />\n";
    ?>
    </form>
    <?php
    echo "<p>You decided to import a file of appreciations directly. The file of
importation must be with the format csv (separator : semicolon) and must contain the two following fields :<br />\n";
    echo "--> <B>IDENTIFIER</B> : Identifier GEPI of the student (<b>See the lower explanations</b>).<br />\n";
    echo "--> <B>Appreciation</B> : the text of the appreciation of the student.<br />If this field is empty, There will not  be modification of the
appreciation recorded in GEPI for the student in question.</p>\n";
    echo "<p>Constitute the file of importation you need to know the identifier <b>GEPI</b> of each student. You can download :
    <ul>
    <li>the file student of the identifiers GEPI (<i>without the name and first name</i>) by
    <a href='import_class_csv.php?id_classe=$id_classe&amp;periode_num=$periode_num&amp;champs=2&amp;ligne_entete=y&amp;mode=Id_App'><b>clicking here</b></a></li>\n";

    //echo ", ou bien,";
    echo "<li>or the file student (name - first name - identifier GEPI) by
    <a href='import_class_csv.php?id_classe=$id_classe&amp;periode_num=$periode_num&amp;champs=4&amp;ligne_entete=y&amp;mode=Nom_Prenom_Id_App'><b>clicking here</b></a><br />(<i>this second file is not directly adapted to the importation<br />(it will be necessary to remove the columns Nom and First name of them
before importation)</i>)</li>\n";

    echo "</ul>
    <p>Once downloaded, use your usual spreadsheet to open this file by
specifying that the type of file is csv with semicolon like separator .</p>\n";

}
if (isset($is_posted ) and ($is_posted==1)) {
	check_token(false);

    $non_def = 'no';
    $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
    echo "<form enctype='multipart/form-data' action='import_app_cons.php' method='post'>\n";
	echo add_token_field();
    if($csv_file['tmp_name'] != "") {
        echo "<p><b>Caution</b>, the data are not recorded yet in base GEPI. You must confirm the
importation (button in bottom of the page) !</p>\n";

        $fp = @fopen($csv_file['tmp_name'], "r");
        if(!$fp) {
            echo "Impossible to open the file CSV";
        } else {
            $row = 0;
            echo "<table class='boireaus'>
			<tr>
            <th><p class='bold'>IDENTIFIER</p></th>
            <th><p class='bold'>Name</p></th>
            <th><p class='bold'>First name</p></th>
            <th><p class='bold'>Class</p></th>
            <th><p class='bold'>Appreciation</p></th>
            <th><p class='bold'>Statute</p></th>
            </tr>\n";
            $valid = 1;
			$alt=1;
            while(!feof($fp)) {
                if (isset($en_tete)) {
                    $data = fgetcsv ($fp, $long_max, ";");
                    unset($en_tete);
                }
                $data = fgetcsv ($fp, $long_max, ";");
                 $num = count ($data);
                // On commence par repérer les lignes qui comportent 2 champs vides de façon à ne pas les retenir
                if ($num == 2) {
                    $champs_vides = 'yes';
                    for ($c=0; $c<$num; $c++) {
                        if ($data[$c] != '') {
                            $champs_vides = 'no';
                        }
                    }
                }
                // On ne retient que les lignes qui comportent 2 champs dont au moins un est non vide
                if (($num == 2) and ($champs_vides == 'no')) {
					$alt=$alt*(-1);
                    $row++;
                    echo "<tr class='lig$alt'>\n";
                    for ($c=0; $c<$num; $c++) {
                        $col3 = '';
                        $reg_app = '';
                        $data_app = '';
                        $reg_ok = "reg_".$row."_ok";
                        switch ($c) {
                        case 0:
                            //login
                            $reg_login = "reg_".$row."_login";
                            $reg_statut = "reg_".$row."_statut";
                            $call_login = mysql_query("SELECT * FROM eleves WHERE login='$data[$c]'");
                            $test = @mysql_num_rows($call_login);
                            if ($test != 0) {
                                $nom_eleve = @mysql_result($call_login, 0, "nom");
                                $prenom_eleve = @mysql_result($call_login, 0, "prenom");

                                $classe_eleve = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_classes j WHERE (j.login = '$data[$c]' AND j.id_classe =  c.id AND j.periode='$periode_num')");
                                $eleve_classe = @mysql_result($classe_eleve, 0, "classe");
                                $eleve_id_classe = @mysql_result($classe_eleve, 0, "id");
                                if ($eleve_classe == '') {
                                   $eleve_classe = "<font color='red'>???</font>";
                                   $valid = 0;
                                }

                                //
                                // On verifie que l'élève a bien pour professeur principal le professeur connecté.
                                //
                                if ($_SESSION['statut'] != 'scolarite') {
                                    $eleve_profsuivi_query = mysql_query("SELECT * FROM  j_eleves_professeurs
                                    WHERE (
                                    login='$data[$c]' AND
                                    professeur='".$_SESSION['login']."' AND
                                    id_classe = '$id_classe')");
                                    $test_suivi = mysql_num_rows($eleve_profsuivi_query);
                                } else {
                                    $test_suivi = 1;
                                }
                                //
                                // Si l'utilisateur n'est pas prof de suivi de l'élève ou si l'élève n'appartient pas à la classe, echec !
                                //
                                if (($test_suivi != "0") and ($eleve_id_classe == $id_classe))  {
                                    echo "<td><p>$data[$c]</p>\n";
                                    $stat = "OK";
                                    echo "<input type='hidden' name='$reg_ok' value='yes' />\n";
									echo "</td>\n";

                                } else {
                                    echo "<td><p><font color='red'>* $data[$c] (non valide) *</font></p>\n";
                                    $valid = 0;
                                    $stat = "<font color='red'>Non valide</font>";
                                    echo "<input type='hidden' name='$reg_ok' value='no' />\n";
									echo "</td>\n";

                                }
                                echo "<td><p>$nom_eleve</p></td>\n";
                                echo "<td><p>$prenom_eleve</p></td>\n";
                                echo "<td><p>$eleve_classe</p>\n";
                                $data_login = urlencode($data[$c]);
                                echo "<input type='hidden' name='$reg_login' value = $data_login />\n";
								echo "</td>\n";
                            } else {
                                echo "<td><font color='red'>???</font></td>\n";
                                echo "<td><font color='red'>???</font></td>\n";
                                echo "<td><font color='red'>???</font></td>\n";
                                echo "<td><font color='red'>???</font>\n";
                                $valid = 0;
                                $stat = "<font color='red'>Not validates</font>";
								echo "<input type='hidden' name='$reg_ok' value = 'no' />\n";
								echo "</td>\n";

                            }
                            break;
                        case 1:
                            // Appréciation
                            if ($data[$c] == "") {
                                $col3 = "<font color='green'>ND</font>";
                                $non_def = 'yes';
                                $data_app = '';
                            } else {
                                $col3 = $data[$c];
                                $data_app = urlencode($data[$c]);
                            }
                            $reg_app = "reg_".$row."_app";
                            break;
                        }
                    }
                    echo "<td><p>$col3</p></td>\n";

                    //echo "<td>".$stat."</td>\n";
                    echo "<td>".$stat;
                    echo "<input type='hidden' name='$reg_app' value=\"$data_app\" />\n";
					echo "</td>\n";
                    echo "</tr>\n";
                // fin de la condition "if ($num == 2)"
                }

            // fin de la boucle "while(!feof($fp))"
            }
            fclose($fp);
            echo "</table>\n";
            echo "<p>First phase of the importation : $row detected entries !</p>\n";
            if ($row > 0) {
                echo "<input type='hidden' name='nb_row' value=\"$row\" />\n";
                echo "<input type='hidden' name='id_classe' value=\"$id_classe\" />\n";
                echo "<input type='hidden' name='periode_num' value=\"$periode_num\" />\n";
                echo "<input type='hidden' name='is_posted' value=\"2\" />\n";
                echo "<input type='submit' value='Record the data' />\n";
                echo "</form>";
                if ($valid != '1') {
                    echo "<p class='bold'>WARNING : Symbols <font color=red>???</font> and the messages in red indicate nonvalid lines and which will not be
recorded.<br /></p>\n";
                    echo "</form>\n";
                }
                if ($non_def == 'yes') {
                    echo "<p class='bold'>Symbols <font color=green>ND</font> mean that the field in question will be ignored. There will not  be thus modification of the existing data in the base
of GEPI.<br /></p>\n";
                }
                ?>
                <script type="text/javascript">
                <!--
                alert("Attention, the data are not recorded yet in base GEPI. You must confirm the importation (button in bottom of the page) !");
                //-->
                </script>
                <?php


				//if() {
					echo "<script type='text/javascript'>changement();</script>\n";
				//}


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

// On enregistre les données
if (isset($is_posted ) and ($is_posted==2)) {
	check_token(false);

    // on vérifie que la période n'est pas totalement verrouillée
    if ($ver_periode[$periode_num] == "O") {
        echo "<p class='grand'>The period on which you want to record is locked.</p>\n";
		require("../lib/footer.inc.php");
        die();
    }
    // si la période n'est pas totalement verrouillée, on continue
    $nb_row++;
    for ($row=1; $row<$nb_row; $row++) {
        $temp = "reg_".$row."_ok";
        if (isset($$temp)) {
            $reg_ok = $$temp;
        } else {
            $reg_ok = 'no';
        }
        // Si la ligne est valide, on continue
        if ($reg_ok=='yes') {
            $temp = "reg_".$row."_login";
            if (isset($$temp)) {
                $reg_login = $$temp;
                $reg_login = urldecode($reg_login);
            } else {
                $reg_login = '';
            }
            $temp = "reg_".$row."_app";
            if (isset($$temp)) {
                $reg_app = $$temp;
                $reg_app = urldecode($reg_app);
                $reg_app = traitement_magic_quotes(corriger_caracteres($reg_app));
            } else {
                $reg_app = '';
            }
            if (($reg_app != "") and ($reg_login !='')) {
                $test_eleve_app_query = mysql_query("SELECT * FROM avis_conseil_classe
                WHERE (login='$reg_login' AND
                periode='$periode_num')");
                $test = mysql_num_rows($test_eleve_app_query);
                if ($test != "0") {
                    $reg_data = mysql_query("UPDATE avis_conseil_classe
                    SET avis='$reg_app', statut=''
                    WHERE (login='$reg_login' AND periode='$periode_num')");
                } else {
                    $reg_data = mysql_query("INSERT INTO avis_conseil_classe
                    SET login='$reg_login',
                    periode='$periode_num',
                    avis='$reg_app',
                    statut=''");
                }
            } else {
                $reg_data ='ok';
            }

            if (!$reg_data) {
                echo "<font color='red'>Error during the modification of the appreciation of the user $reg_login !</font><br />\n";
            } else {
                echo "The appreciation of the user $reg_login was modified successfully !<br />\n";
            }
        }
    }
    echo "</p>\n";
    echo "<br /><a href='saisie_avis1.php?id_classe=$id_classe'>Reach the page of seizure of the appreciations for checking</a></p>\n";
}
require("../lib/footer.inc.php");
?>