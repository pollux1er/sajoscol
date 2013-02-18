<?php
@set_time_limit(0);

// Auteur: Stephane Boireau
// $Id: lecture_xml_sts_emp.php 5937 2010-11-21 17:42:55Z crob $

// Initialisations files
require_once("../lib/initialisations.inc.php");

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

//================================================
// Fonction de génération de mot de passe récupérée sur TotallyPHP
// Aucune mention de licence pour ce script...

/*
 * The letter l (lowercase L) and the number 1
 * have been removed, as they can be mistaken
 * for each other.
*/

function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    //while ($i <= 7) {
    while ($i <= 5) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;
}
//================================================

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Reading of the XML Timetable of Sts-web and generation of CSV</title>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
    <meta name="author" content="Stephane Boireau, A.S. RUE de Bernay/Pont-Audemer" />
    <!--link type="text/css" rel="stylesheet" href="../styles.css" /-->
    <link type="text/css" rel="stylesheet" href="../style.css" />
</head>
<body>
    <div class="content">
        <?php

            // Initialisation du répertoire actuel de sauvegarde
            $dirname = getSettingValue("backup_directory");
            //$dirname="tmp";

            if(!file_exists("../backup/$dirname/csv")){
                //if(!mkdir("../backup/$dirname/csv","0770")){
                if(!mkdir("../backup/$dirname/csv")){
                    echo "<p style='color:red;'>Error! The folder csv could not be created.</p>\n";
                    echo "<p>Return to the <a href='index.php'>index</a></p>\n";
                    echo "</div></body></html>\n";
                    die();
                }
            }

            //$nom_corrige = my_ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $sav_file['name']);



            if(isset($_GET['nettoyage'])){
                echo "<h1 align='center'>Suppression of the CSV</h1>\n";
                echo "<p>If files CSV exist, they will be removed...</p>\n";
                $tabfich=array("f_wind.csv","f_men.csv","f_gpd.csv","f_div.csv","f_tmt.csv","profs.html");
                for($i=0;$i<count($tabfich);$i++){
                    if(file_exists("../backup/$dirname/csv/$tabfich[$i]")){
                        echo "<p>Suppression of $tabfich[$i]... ";
                        if(unlink("../backup/$dirname/csv/$tabfich[$i]")){
                            echo "succeeded.</p>\n";
                        }
                        else{
                            echo "<font color='red'>Failure!</font> Check the rights of writing on the server.</p>\n";
                        }
                    }
                }
            }
            else{
                echo "<h1 align='center'>Reading of the XML Timetable of Sts-Web and generation of CSV</h1>\n";
                if(!isset($_POST['is_posted'])){
                    echo "<p>This page makes it possible to fill of tables PHP with information professors, courses,... but not yet connections profs/courses/classes.<br />It generates files CSV allowing an importation of the accounts Profs for SambaEdu3.</p>\n";
                    echo "<p>It is necessary to provide him an Export XML carried out from the STS-Web application.<br />Ask gentillement your secretary access to STS-Web and to carry out 'Update/Exports/ Timetables'.</p>\n";
                    echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
                    echo "<p>Veuillez fournir le fichier XML: \n";
                    echo "<p><input type=\"file\" size=\"80\" name=\"xml_file\">\n";
                    echo "<input type='hidden' name='is_posted' value='yes'>\n";
                    echo "</p>\n";
                    echo "<p><input type=\"radio\" name=\"mdp\" value=\"alea\" checked> Generate a random password for each professor.<br />\n";
                    echo "<input type=\"radio\" name=\"mdp\" value=\"date\"> Rather use the date of birth in the format 'aaaammjj' as initial password (<i>it will have to be modified at the first login</i>).</p>\n";
                    echo "<input type='hidden' name='is_posted' value='yes'>\n";
                    //echo "</p>\n";
                    echo "<p><input type='submit' value='Validate'></p>\n";
                    echo "</form>\n";
                }
                else{
                    $temoin_au_moins_un_prof_princ="";

                    $xml_file = isset($_FILES["xml_file"]) ? $_FILES["xml_file"] : NULL;
                    $fp=fopen($xml_file['tmp_name'],"r");
                    if($fp){
                        echo "<h2>First phase...</h2>\n";
                        echo "<blockquote>\n";
                        echo "<h3>Reading of the file...</h3>\n";
                        echo "<blockquote>\n";
                        while(!feof($fp)){
                            $ligne[]=fgets($fp,4096);
                        }
                        fclose($fp);
                        echo "<p>Finished.</p>\n";
                        //echo "<p>Aller à la <a href='#se3'>section SambaEdu3</a></p>\n";
                        echo "<p>Go to <a href='#gepi'>GEPI section </a><br />If you have patience, direct links will be proposed to download the files.</p>\n";
                        echo "</blockquote>\n";

                        echo "<h3>Display of the XML</h3>\n";
                        echo "<blockquote>\n";
                        echo "<table border='0'>\n";
                        $cpt=0;
                        while($cpt<count($ligne)){
                            echo "<tr>\n";
                            echo "<td style='color: blue;'>$cpt</td><td>".htmlentities($ligne[$cpt])."</td>\n";
                            echo "</tr>\n";
                            $cpt++;
                        }
                        echo "</table>\n";
                        echo "<p>Finished.</p>\n";
                        echo "</blockquote>\n";
                        echo "</blockquote>\n";



                        echo "<h2>Etablissement</h2>\n";
                        echo "<blockquote>\n";
                        echo "<h3> Analysis of the file to extract the parameters from the school...</h3>\n";
                        echo "<blockquote>\n";
                        $cpt=0;
                        $etablissement=array();
                        $temoin_param=0;
                        $temoin_academie=0;
                        $temoin_annee=0;
                        while($cpt<count($ligne)){
                            //echo htmlentities($ligne[$cpt])."<br />\n";
                            if(strstr($ligne[$cpt],"<PARAMETRES>")){
                                echo "Beginning of the section PARAMETERS at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_param++;
                            }
                            if(strstr($ligne[$cpt],"</PARAMETRES>")){
                                echo "End of the section PARAMETERS at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_param++;
                            }
                            if($temoin_param==1){
                                // On analyse maintenant matière par matière:
                                /*
                                if(strstr($ligne[$cpt],"<UAJ CODE=")){
                                    unset($tabtmp);
                                    $tabtmp=explode('"',$ligne[$cpt]);
                                    $etablissement["code"]=trim($tabtmp[1]);
                                    $temoin_uaj=1;
                                    //echo "\$temoin_uaj=$temoin_uaj à la ligne $cpt et \$tabtmp[1]=$tabtmp[1]<br />\n";
                                }
                                */
                                if(strstr($ligne[$cpt],"<UAJ ")){
                                    unset($tabtmp);
                                    $tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
                                    $etablissement["code"]=trim($tabtmp[1]);
                                    $temoin_uaj=1;
                                }
                                if(strstr($ligne[$cpt],"</UAJ>")){
                                    $temoin_uaj=0;
                                }
                                if($temoin_uaj==1){
                                    if(strstr($ligne[$cpt],"<ACADEMIE>")){
                                        $temoin_academie=1;
                                        $etablissement["academie"]=array();
                                    }
                                    if(strstr($ligne[$cpt],"</ACADEMIE>")){
                                        $temoin_academie=0;
                                    }
                                    if($temoin_academie==1){
                                        if(strstr($ligne[$cpt],"<CODE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["academie"]["code"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<LIBELLE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["academie"]["libelle"]=trim($tabtmp[2]);
                                        }
                                    }
                                    else{
                                        if(strstr($ligne[$cpt],"<SIGLE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["sigle"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<DENOM_PRINC>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["denom_princ"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<DENOM_COMPL>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["denom_compl"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<CODE_NATURE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["code_nature"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<CODE_CATEGORIE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["code_categorie"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<ADRESSE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["adresse"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<COMMUNE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["commune"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<CODE_POSTAL>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["code_postal"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<BOITE_POSTALE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["boite_postale"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<CEDEX>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["cedex"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<TELEPHONE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["telephone"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<STATUT>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["statut"]=trim($tabtmp[2]);
                                        }
                                        if(strstr($ligne[$cpt],"<ETABLISSEMENT_SENSIBLE>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $etablissement["etablissement_sensible"]=trim($tabtmp[2]);
                                        }
                                    }
                                }


                                /*
                                if(strstr($ligne[$cpt],"<ANNEE_SCOLAIRE ANNEE=")){
                                    unset($tabtmp);
                                    $tabtmp=explode('"',$ligne[$cpt]);
                                    $etablissement["annee"]=array();
                                    $etablissement["annee"]["annee"]=trim($tabtmp[1]);
                                    $temoin_annee=1;
                                }
                                */
                                if(strstr($ligne[$cpt],"<ANNEE_SCOLAIRE ")){
                                    unset($tabtmp);
                                    $tabtmp=explode('"',strstr($ligne[$cpt]," ANNEE"));
                                    $etablissement["annee"]=array();
                                    $etablissement["annee"]["annee"]=trim($tabtmp[1]);
                                    $temoin_annee=1;
                                }
                                if(strstr($ligne[$cpt],"</ANNEE_SCOLAIRE>")){
                                    $temoin_annee=0;
                                }
                                if($temoin_annee==1){
                                    if(strstr($ligne[$cpt],"<DATE_DEBUT>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $etablissement["annee"]["date_debut"]=trim($tabtmp[2]);
                                    }
                                    if(strstr($ligne[$cpt],"<DATE_FIN>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $etablissement["annee"]["date_fin"]=trim($tabtmp[2]);
                                    }
                                }
                            }
                            $cpt++;
                        }
                        echo "<p>Terminé.</p>\n";
                        echo "</blockquote>\n";

                        echo "<h3>Display of the data extracted school PARAMETERS :</h3>\n";
                        echo "<blockquote>\n";
                        echo "<table border='1'>\n";
                        echo "<tr>\n";
                        //echo "<th style='color: blue;'>&nbsp;</th>\n";
                        echo "<th>Code</th>\n";
                        echo "<th>Academy code </th>\n";
                        echo "<th>Académie name</th>\n";
                        echo "<th>Initials</th>\n";
                        echo "<th>Denom_princ</th>\n";
                        echo "<th>Denom_compl</th>\n";
                        echo "<th>Code_nature</th>\n";
                        echo "<th>Code_category</th>\n";
                        echo "<th>Adress</th>\n";
                        echo "<th>Postal code</th>\n";
                        echo "<th>PO Box</th>\n";
                        echo "<th>Cedex</th>\n";
                        echo "<th>Telephone</th>\n";
                        echo "<th>Statute</th>\n";
                        echo "<th>Etablissement_sensible</th>\n";
                        echo "<th>Year</th>\n";
                        echo "<th>Beginning date </th>\n";
                        echo "<th>Ending date </th>\n";
                        echo "</tr>\n";
                        //$cpt=0;
                        //while($cpt<count($etablissement)){
                            echo "<tr>\n";
                            //echo "<td style='color: blue;'>$cpt</td>\n";
                            //echo "<td style='color: blue;'>&nbsp;</td>\n";
                            echo "<td>".$etablissement["code"]."</td>\n";
                            echo "<td>".$etablissement["academie"]["code"]."</td>\n";
                            echo "<td>".$etablissement["academie"]["libelle"]."</td>\n";
                            echo "<td>".$etablissement["sigle"]."</td>\n";
                            echo "<td>".$etablissement["denom_princ"]."</td>\n";
                            echo "<td>".$etablissement["denom_compl"]."</td>\n";
                            echo "<td>".$etablissement["code_nature"]."</td>\n";
                            echo "<td>".$etablissement["code_categorie"]."</td>\n";
                            echo "<td>".$etablissement["adresse"]."</td>\n";
                            echo "<td>".$etablissement["code_postal"]."</td>\n";
                            echo "<td>".$etablissement["boite_postale"]."</td>\n";
                            echo "<td>".$etablissement["cedex"]."</td>\n";
                            echo "<td>".$etablissement["telephone"]."</td>\n";
                            echo "<td>".$etablissement["statut"]."</td>\n";
                            echo "<td>".$etablissement["etablissement_sensible"]."</td>\n";
                            echo "<td>".$etablissement["annee"]["annee"]."</td>\n";
                            echo "<td>".$etablissement["annee"]["date_debut"]."</td>\n";
                            echo "<td>".$etablissement["annee"]["date_fin"]."</td>\n";
                            echo "</tr>\n";
                            $cpt++;
                        //}
                        echo "</table>\n";
                        echo "</blockquote>\n";
                        echo "</blockquote>\n";











                        echo "<h2>Courses</h2>\n";
                        echo "<blockquote>\n";
                        echo "<h3>Analyze file to extract the courses...</h3>\n";
                        echo "<blockquote>\n";
                        $cpt=0;
                        $temoin_matieres=0;
                        $matiere=array();
                        $i=0;
                        $temoin_mat=0;
                        while($cpt<count($ligne)){
                            //echo htmlentities($ligne[$cpt])."<br />\n";
                            if(strstr($ligne[$cpt],"<MATIERES>")){
                                echo "Beginning of the section COURSES at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_matieres++;
                            }
                            if(strstr($ligne[$cpt],"</MATIERES>")){
                                echo "End of the section COURSES at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_matieres++;
                            }
                            if($temoin_matieres==1){
                                // On analyse maintenant matière par matière:
                                /*
                                if(strstr($ligne[$cpt],"<MATIERE CODE=")){
                                    $matiere[$i]=array();
                                    unset($tabtmp);
                                    //$tabtmp=explode("=",my_ereg_replace(">","",my_ereg_replace("<","",$ligne[$cpt])));
                                    $tabtmp=explode('"',$ligne[$cpt]);
                                    $matiere[$i]["code"]=trim($tabtmp[1]);
                                    $temoin_mat=1;
                                }
                                */
                                if(strstr($ligne[$cpt],"<MATIERE ")){
                                    $matiere[$i]=array();
                                    unset($tabtmp);
                                    //$tabtmp=explode("=",my_ereg_replace(">","",my_ereg_replace("<","",$ligne[$cpt])));
                                    $tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
                                    $matiere[$i]["code"]=trim($tabtmp[1]);
                                    $temoin_mat=1;
                                }
                                if(strstr($ligne[$cpt],"</MATIERE>")){
                                    $temoin_mat=0;
                                    $i++;
                                }
                                if($temoin_mat==1){
                                    if(strstr($ligne[$cpt],"<CODE_GESTION>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        //$matiere[$i]["code_gestion"]=$tabtmp[2];
                                        $matiere[$i]["code_gestion"]=trim(my_ereg_replace("[^a-zA-Z0-9&_. -]","",html_entity_decode_all_version($tabtmp[2])));
                                    }
                                    if(strstr($ligne[$cpt],"<LIBELLE_COURT>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        //$matiere[$i]["libelle_court"]=$tabtmp[2];
                                        $matiere[$i]["libelle_court"]=trim(my_ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü&_. -]","",html_entity_decode_all_version($tabtmp[2])));
                                    }
                                    if(strstr($ligne[$cpt],"<LIBELLE_LONG>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $matiere[$i]["libelle_long"]=trim($tabtmp[2]);
                                    }
                                    if(strstr($ligne[$cpt],"<LIBELLE_EDITION>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $matiere[$i]["libelle_edition"]=trim($tabtmp[2]);
                                    }
                                }
                            }

                            $cpt++;
                        }
                        echo "<p>Finished.</p>\n";
                        echo "</blockquote>\n";

                        echo "<h3>Display of the data COURSES extracted :</h3>\n";
                        echo "<blockquote>\n";
                        echo "<table border='1'>\n";
                        echo "<tr>\n";
                        echo "<th style='color: blue;'>&nbsp;</th>\n";
                        echo "<th>Code</th>\n";
                        echo "<th>Code_gestion</th>\n";
                        echo "<th>Libelle_court</th>\n";
                        echo "<th>Libelle_long</th>\n";
                        echo "<th>Libelle_edition</th>\n";
                        echo "</tr>\n";
                        $cpt=0;
                        while($cpt<count($matiere)){
                            echo "<tr>\n";
                            echo "<td style='color: blue;'>$cpt</td>\n";
                            echo "<td>".$matiere[$cpt]["code"]."</td>\n";
                            echo "<td>".$matiere[$cpt]["code_gestion"]."</td>\n";
                            echo "<td>".$matiere[$cpt]["libelle_court"]."</td>\n";
                            echo "<td>".$matiere[$cpt]["libelle_long"]."</td>\n";
                            echo "<td>".$matiere[$cpt]["libelle_edition"]."</td>\n";
                            echo "</tr>\n";
                            $cpt++;
                        }
                        echo "</table>\n";
                        echo "</blockquote>\n";
                        echo "</blockquote>\n";














                        echo "<h2>Civilities</h2>\n";
                        echo "<blockquote>\n";
                        echo "<h3>Analyze file to extract civilities...</h3>\n";
                        echo "<blockquote>\n";
                        $cpt=0;
                        $temoin_civilites=0;
                        $civilites=array();
                        $i=0;
                        while($cpt<count($ligne)){
                            //echo htmlentities($ligne[$cpt])."<br />\n";
                            if(strstr($ligne[$cpt],"<CIVILITES>")){
                                echo "Beginning of section CIVILITES at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_civilites++;
                            }
                            if(strstr($ligne[$cpt],"</CIVILITES>")){
                                echo "End of section CIVILITES at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_civilites++;
                            }
                            if($temoin_civilites==1){
                                /*
                                if(strstr($ligne[$cpt],"<CIVILITE CODE=")){
                                    $civilites[$i]=array();
                                    unset($tabtmp);
                                    $tabtmp=explode('"',$ligne[$cpt]);
                                    $civilites[$i]["code"]=trim($tabtmp[1]);
                                    $temoin_civ=1;
                                }
                                */
                                if(strstr($ligne[$cpt],"<CIVILITE ")){
                                    $civilites[$i]=array();
                                    unset($tabtmp);
                                    $tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
                                    $civilites[$i]["code"]=trim($tabtmp[1]);
                                    $temoin_civ=1;
                                }
                                if(strstr($ligne[$cpt],"</CIVILITE>")){
                                    $temoin_civ=0;
                                    $i++;
                                }
                                if($temoin_civ==1){
                                    if(strstr($ligne[$cpt],"<LIBELLE_COURT>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $civilites[$i]["libelle_court"]=trim($tabtmp[2]);
                                    }
                                    if(strstr($ligne[$cpt],"<LIBELLE_LONG>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $civilites[$i]["libelle_long"]=trim($tabtmp[2]);
                                    }
                                }
                            }
                            $cpt++;
                        }
                        echo "<p>Finished.</p>\n";
                        echo "</blockquote>\n";

                        echo "<h3>Display of extracted data CIVILITES:</h3>\n";
                        echo "<blockquote>\n";
                        echo "<table border='1'>\n";
                        echo "<tr>\n";
                        echo "<th style='color: blue;'>&nbsp;</th>\n";
                        echo "<th>Code</th>\n";
                        echo "<th>Libelle_court</th>\n";
                        echo "<th>Libelle_long</th>\n";
                        echo "</tr>\n";
                        $cpt=0;
                        while($cpt<count($civilites)){
                            echo "<tr>\n";
                            echo "<td style='color: blue;'>$cpt</td>\n";
                            echo "<td>".$civilites[$cpt]["code"]."</td>\n";
                            echo "<td>".$civilites[$cpt]["libelle_court"]."</td>\n";
                            echo "<td>".$civilites[$cpt]["libelle_long"]."</td>\n";
                            echo "</tr>\n";
                            $cpt++;
                        }
                        echo "</table>\n";
                        echo "</blockquote>\n";
                        echo "</blockquote>\n";










                        echo "<h2>Personnel</h2>\n";
                        echo "<blockquote>\n";
                        echo "<h3>Analyze file to extract the professors,...</h3>\n";
                        echo "<blockquote>\n";
                        $cpt=0;
                        $temoin_professeurs=0;
                        $prof=array();
                        $i=0;
                        $temoin_prof=0;
                        while($cpt<count($ligne)){
                            //echo htmlentities($ligne[$cpt])."<br />\n";
                            if(strstr($ligne[$cpt],"<INDIVIDUS>")){
                                echo "Beginning of the section INDIVIDUALS at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_professeurs++;
                            }
                            if(strstr($ligne[$cpt],"</INDIVIDUS>")){
                                echo "End of the section INDIVIDUALS at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_professeurs++;
                            }
                            if($temoin_professeurs==1){
                                // On analyse maintenant matière par matière:
                                /*
                                if(strstr($ligne[$cpt],"<INDIVIDU ID=")){
                                    $prof[$i]=array();
                                    unset($tabtmp);
                                    $tabtmp=explode('"',$ligne[$cpt]);
                                    $prof[$i]["id"]=trim($tabtmp[1]);
                                    $prof[$i]["type"]=trim($tabtmp[3]);
                                    $temoin_prof=1;
                                }
                                */
                                if(strstr($ligne[$cpt],"<INDIVIDU ")){
                                    $prof[$i]=array();
                                    unset($tabtmp);
                                    $tabtmp=explode('"',strstr($ligne[$cpt]," ID="));
                                    $prof[$i]["id"]=trim($tabtmp[1]);
                                    $tabtmp=explode('"',strstr($ligne[$cpt]," TYPE="));
                                    $prof[$i]["type"]=trim($tabtmp[1]);
                                    $temoin_prof=1;
                                }
                                if(strstr($ligne[$cpt],"</INDIVIDU>")){
                                    $temoin_prof=0;
                                    $i++;
                                }
                                if($temoin_prof==1){
                                    if(strstr($ligne[$cpt],"<SEXE>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        //$prof[$i]["sexe"]=$tabtmp[2];
                                        $prof[$i]["sexe"]=trim(my_ereg_replace("[^1-2]","",$tabtmp[2]));
                                    }
                                    if(strstr($ligne[$cpt],"<CIVILITE>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        //$prof[$i]["civilite"]=$tabtmp[2];
                                        $prof[$i]["civilite"]=trim(my_ereg_replace("[^1-3]","",$tabtmp[2]));
                                    }
                                    if(strstr($ligne[$cpt],"<NOM_USAGE>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        //$prof[$i]["nom_usage"]=$tabtmp[2];
                                        $prof[$i]["nom_usage"]=trim(my_ereg_replace("[^a-zA-Z -]","",$tabtmp[2]));
                                    }
                                    if(strstr($ligne[$cpt],"<NOM_PATRONYMIQUE>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        //$prof[$i]["nom_patronymique"]=$tabtmp[2];
                                        $prof[$i]["nom_patronymique"]=trim(my_ereg_replace("[^a-zA-Z -]","",$tabtmp[2]));
                                    }
                                    if(strstr($ligne[$cpt],"<PRENOM>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        //$prof[$i]["prenom"]=$tabtmp[2];
                                        $prof[$i]["prenom"]=trim(my_ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. -]","",$tabtmp[2]));
                                    }
                                    if(strstr($ligne[$cpt],"<DATE_NAISSANCE>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        //$prof[$i]["date_naissance"]=$tabtmp[2];
                                        $prof[$i]["date_naissance"]=trim(my_ereg_replace("[^0-9-]","",$tabtmp[2]));
                                    }
                                    if(strstr($ligne[$cpt],"<GRADE>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $prof[$i]["grade"]=trim($tabtmp[2]);
                                    }
                                    if(strstr($ligne[$cpt],"<FONCTION>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $prof[$i]["fonction"]=trim($tabtmp[2]);
                                    }



                                    if(strstr($ligne[$cpt],"<PROFS_PRINC>")){
                                        $temoin_profs_princ=1;
                                        //$prof[$i]["prof_princs"]=array();
                                        $j=0;
                                    }
                                    if(strstr($ligne[$cpt],"</PROFS_PRINC>")){
                                        $temoin_profs_princ=0;
                                    }

                                    if($temoin_profs_princ==1){

                                        if(strstr($ligne[$cpt],"<PROF_PRINC>")){
                                            $temoin_prof_princ=1;
                                            $prof[$i]["prof_princ"]=array();
                                        }
                                        if(strstr($ligne[$cpt],"</PROF_PRINC>")){
                                            $temoin_prof_princ=0;
                                            $j++;
                                        }

                                        if($temoin_prof_princ==1){
                                            if(strstr($ligne[$cpt],"<CODE_STRUCTURE>")){
                                                unset($tabtmp);
                                                $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                                $prof[$i]["prof_princ"][$j]["code_structure"]=trim($tabtmp[2]);
                                                $temoin_au_moins_un_prof_princ="oui";
                                            }

                                            if(strstr($ligne[$cpt],"<DATE_DEBUT>")){
                                                unset($tabtmp);
                                                $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                                $prof[$i]["prof_princ"][$j]["date_debut"]=trim($tabtmp[2]);
                                            }
                                            if(strstr($ligne[$cpt],"<DATE_FIN>")){
                                                unset($tabtmp);
                                                $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                                $prof[$i]["prof_princ"][$j]["date_fin"]=trim($tabtmp[2]);
                                            }
                                        }
                                    }




                                    if(strstr($ligne[$cpt],"<DISCIPLINES>")){
                                        $temoin_disciplines=1;
                                        $prof[$i]["disciplines"]=array();
                                        $j=0;
                                    }
                                    if(strstr($ligne[$cpt],"</DISCIPLINES>")){
                                        $temoin_disciplines=0;
                                    }



                                    if($temoin_disciplines==1){
                                        /*
                                        if(strstr($ligne[$cpt],"<DISCIPLINE CODE=")){
                                            $temoin_disc=1;
                                            unset($tabtmp);
                                            $tabtmp=explode('"',$ligne[$cpt]);
                                            $prof[$i]["disciplines"][$j]["code"]=trim($tabtmp[1]);
                                        }
                                        */
                                        if(strstr($ligne[$cpt],"<DISCIPLINE ")){
                                            $temoin_disc=1;
                                            unset($tabtmp);
                                            $tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
                                            $prof[$i]["disciplines"][$j]["code"]=trim($tabtmp[1]);
                                        }
                                        if(strstr($ligne[$cpt],"</DISCIPLINE>")){
                                            $temoin_disc=0;
                                            $j++;
                                        }

                                        if($temoin_disc==1){
                                            if(strstr($ligne[$cpt],"<LIBELLE_COURT>")){
                                                unset($tabtmp);
                                                $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                                $prof[$i]["disciplines"][$j]["libelle_court"]=trim($tabtmp[2]);
                                            }
                                            if(strstr($ligne[$cpt],"<NB_HEURES>")){
                                                unset($tabtmp);
                                                $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                                $prof[$i]["disciplines"][$j]["nb_heures"]=trim($tabtmp[2]);
                                            }
                                        }
                                    }


                                }
                            }



                            // On va récupérer les divisions et associations profs/matières...
                            if(strstr($ligne[$cpt],"<STRUCTURE>")){
                                echo "Beginning of the section STRUCTURE at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_structure++;
                            }
                            if(strstr($ligne[$cpt],"</STRUCTURE>")){
                                echo "End of the section STRUCTURE at the line <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_structure++;
                            }
                            if($temoin_structure==1){
                                if(strstr($ligne[$cpt],"<DIVISIONS>")){
                                    echo "Beginning of the section DIVIDED at the line <span style='color: blue;'>$cpt</span><br />\n";
                                    $temoin_divisions++;
                                    $divisions=array();
                                    $i=0;
                                }
                                if(strstr($ligne[$cpt],"</DIVISIONS>")){
                                    echo "End of the section DIVIDED at the line <span style='color: blue;'>$cpt</span><br />\n";
                                    $temoin_divisions++;
                                }
                                if($temoin_divisions==1){
                                    /*
                                    if(strstr($ligne[$cpt],"<DIVISION CODE=")){
                                        $temoin_div=1;
                                        unset($tabtmp);
                                        $tabtmp=explode('"',$ligne[$cpt]);
                                        $divisions[$i]["code"]=trim($tabtmp[1]);
                                    }
                                    */
                                    if(strstr($ligne[$cpt],"<DIVISION ")){
                                        $temoin_div=1;
                                        unset($tabtmp);
                                        $tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
                                        $divisions[$i]["code"]=trim($tabtmp[1]);
                                    }
                                    if(strstr($ligne[$cpt],"</DIVISION>")){
                                        $temoin_div=0;
                                        $i++;
                                    }

                                    if($temoin_div==1){
                                        if(strstr($ligne[$cpt],"<SERVICES>")){
                                            $temoin_services=1;
                                            $j=0;
                                        }
                                        if(strstr($ligne[$cpt],"</SERVICES>")){
                                            $temoin_services=0;
                                        }

                                        if($temoin_services==1){
                                            /*
                                            if(strstr($ligne[$cpt],"<SERVICE CODE_MATIERE=")){
                                                $temoin_disc=1;
                                                unset($tabtmp);
                                                $tabtmp=explode('"',$ligne[$cpt]);
                                                $divisions[$i]["services"][$j]["code_matiere"]=trim($tabtmp[1]);
                                            }
                                            */
                                            if(strstr($ligne[$cpt],"<SERVICE ")){
                                                $temoin_disc=1;
                                                unset($tabtmp);
                                                $tabtmp=explode('"',strstr($ligne[$cpt]," CODE_MATIERE="));
                                                $divisions[$i]["services"][$j]["code_matiere"]=trim($tabtmp[1]);
                                            }
                                            if(strstr($ligne[$cpt],"</SERVICE>")){
                                                $temoin_disc=0;
                                                $j++;
                                            }

                                            if($temoin_disc==1){
                                                if(strstr($ligne[$cpt],"<ENSEIGNANTS>")){
                                                    $temoin_enseignants=1;
                                                    $divisions[$i]["services"][$j]["enseignants"]=array();
                                                    $k=0;
                                                }
                                                if(strstr($ligne[$cpt],"</ENSEIGNANTS>")){
                                                    $temoin_enseignants=0;
                                                }
                                                if($temoin_enseignants==1){
                                                    /*
                                                    if(strstr($ligne[$cpt],"<ENSEIGNANT ID=")){
                                                        //$temoin_ens=1;
                                                        unset($tabtmp);
                                                        $tabtmp=explode('"',$ligne[$cpt]);
                                                        $divisions[$i]["services"][$j]["enseignants"][$k]["id"]=trim($tabtmp[1]);
                                                    }
                                                    */
                                                    if(strstr($ligne[$cpt],"<ENSEIGNANT ")){
                                                        //$temoin_ens=1;
                                                        unset($tabtmp);
                                                        $tabtmp=explode('"',strstr($ligne[$cpt]," ID="));
                                                        $divisions[$i]["services"][$j]["enseignants"][$k]["id"]=trim($tabtmp[1]);
                                                    }
                                                    if(strstr($ligne[$cpt],"</ENSEIGNANT>")){
                                                        //$temoin_ens=0;
                                                        $k++;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }






                                if(strstr($ligne[$cpt],"<GROUPES>")){
                                    echo "Beginning of the section GROUPS at the line <span style='color: blue;'>$cpt</span><br />\n";
                                    $temoin_groupes++;
                                    $groupes=array();
                                    $i=0;
                                }
                                if(strstr($ligne[$cpt],"</GROUPES>")){
                                    echo "End of the section GROUPS at the line <span style='color: blue;'>$cpt</span><br />\n";
                                    $temoin_groupes++;
                                }
                                if($temoin_groupes==1){
                                    /*
                                    if(strstr($ligne[$cpt],"<GROUPE CODE=")){
                                        $temoin_grp=1;
                                        unset($tabtmp);
                                        $tabtmp=explode('"',$ligne[$cpt]);
                                        $groupes[$i]=array();
                                        $groupes[$i]["code"]=trim($tabtmp[1]);
                                        $j=0;
                                        $m=0;
                                    }
                                    */
                                    if(strstr($ligne[$cpt],"<GROUPE ")){
                                        $temoin_grp=1;
                                        unset($tabtmp);
                                        $tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
                                        $groupes[$i]=array();
                                        $groupes[$i]["code"]=trim($tabtmp[1]);
                                        $j=0;
                                        $m=0;
                                    }
                                    if(strstr($ligne[$cpt],"</GROUPE>")){
                                        $temoin_grp=0;
                                        $i++;
                                    }

                                    if($temoin_grp==1){
                                        if(strstr($ligne[$cpt],"<LIBELLE_LONG>")){
                                            unset($tabtmp);
                                            $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                            $groupes[$i]["libelle_long"]=trim($tabtmp[2]);
                                        }

                                        if(strstr($ligne[$cpt],"<DIVISIONS_APPARTENANCE>")){
                                            $temoin_div_appart=1;
                                        }
                                        if(strstr($ligne[$cpt],"</DIVISIONS_APPARTENANCE>")){
                                            $temoin_div_appart=0;
                                        }

                                        if($temoin_div_appart==1){
                                            /*
                                            if(strstr($ligne[$cpt],"<DIVISION_APPARTENANCE CODE=")){
                                                unset($tabtmp);
                                                $tabtmp=explode('"',$ligne[$cpt]);
                                                $groupes[$i]["divisions"][$j]["code"]=trim($tabtmp[1]);
                                                $j++;
                                            }
                                            */
                                            if(strstr($ligne[$cpt],"<DIVISION_APPARTENANCE ")){
                                                unset($tabtmp);
                                                $tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
                                                $groupes[$i]["divisions"][$j]["code"]=trim($tabtmp[1]);
                                                $j++;
                                            }
                                        }


                                        //<SERVICE CODE_MATIERE="020100" CODE_MOD_COURS="CG">
                                        /*
                                        if(strstr($ligne[$cpt],"<SERVICE CODE_MATIERE=")){
                                            unset($tabtmp);
                                            $tabtmp=explode('"',$ligne[$cpt]);
                                            $groupes[$i]["code_matiere"]=trim($tabtmp[1]);
                                        }
                                        */
                                        if(strstr($ligne[$cpt],"<SERVICE ")){
                                            unset($tabtmp);
                                            $tabtmp=explode('"',strstr($ligne[$cpt]," CODE_MATIERE="));
                                            $groupes[$i]["code_matiere"]=trim($tabtmp[1]);
                                        }


                                        //<ENSEIGNANT TYPE="epp" ID="31762">
                                        // Améliorer la récup de l'attribut ID...
                                        // ...découper en un tableau avec ' '
                                        // et rechercher quel champ du tableau commence par ID=

                                           //<ENSEIGNANT ID="11508" TYPE="epp">

                                        //if(strstr($ligne[$cpt],"<ENSEIGNANT TYPE=")){
                                        /*
                                        if(strstr($ligne[$cpt],"<ENSEIGNANT ID=")){
                                            unset($tabtmp);
                                            $tabtmp=explode('"',$ligne[$cpt]);
                                            //$groupes[$i]["enseignant"][$m]["id"]=$tabtmp[3];
                                            $groupes[$i]["enseignant"][$m]["id"]=trim($tabtmp[1]);
                                            $m++;
                                        }
                                        */
                                        if(strstr($ligne[$cpt],"<ENSEIGNANT ")){
                                            unset($tabtmp);
                                            $tabtmp=explode('"',strstr($ligne[$cpt]," ID="));
                                            //$groupes[$i]["enseignant"][$m]["id"]=$tabtmp[3];
                                            $groupes[$i]["enseignant"][$m]["id"]=trim($tabtmp[1]);
                                            $m++;
                                        }

                                    }
                                }










                            }

                            $cpt++;
                        }
                        echo "<p>Finished.</p>\n";
                        echo "</blockquote>\n";












/*
                        echo "<h2>Programmes</h2>\n";
                        echo "<blockquote>\n";
                        echo "<h3>Analyse du fichier pour extraire les programmes...</h3>\n";
                        echo "<blockquote>\n";
                        echo "<p>Il s'agit ici de remplir un tableau des liens entre les MEFS et les MATIERES.</p>\n";
                        $cpt=0;
                        $temoin_programmes=0;
                        $programme=array();
                        $i=0;
                        $temoin_mat=0;
                        while($cpt<count($ligne)){
                            //echo htmlentities($ligne[$cpt])."<br />\n";
                            if(strstr($ligne[$cpt],"<PROGRAMMES>")){
                                echo "Début de la section PROGRAMMES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_programmes++;
                            }
                            if(strstr($ligne[$cpt],"</PROGRAMMES>")){
                                echo "Fin de la section PROGRAMMES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
                                $temoin_programmes++;
                            }
                            if($temoin_programmes==1){
                                // On analyse maintenant matière par matière:
                                if(strstr($ligne[$cpt],"<PROGRAMME>")){
                                    $programme[$i]=array();
                                    $temoin_prog=1;
                                }
                                if(strstr($ligne[$cpt],"</PROGRAMME>")){
                                    $temoin_prog=0;
                                    $i++;
                                }
                                if($temoin_prog==1){
                                    if(strstr($ligne[$cpt],"<CODE_MEF>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $programme[$i]["code_mef"]=$tabtmp[2];
                                    }
                                    if(strstr($ligne[$cpt],"<CODE_MATIERE>")){
                                        unset($tabtmp);
                                        $tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
                                        $programme[$i]["code_matiere"]=$tabtmp[2];
                                    }
                                }
                            }

                            $cpt++;
                        }
                        echo "<p>Terminé.</p>\n";
                        echo "</blockquote>\n";
*/





                        echo "<h3>Display of the data PROFS,... extracted:</h3>\n";
                        echo "<blockquote>\n";
                        echo "<table border='1'>\n";
                        echo "<tr>\n";
                        echo "<th style='color: blue;'>&nbsp;</th>\n";
                        echo "<th>Id</th>\n";
                        echo "<th>Type</th>\n";
                        echo "<th>Sex</th>\n";
                        echo "<th>Civility</th>\n";
                        echo "<th>Name_use</th>\n";
                        echo "<th>Patronymic_name</th>\n";
                        echo "<th>Prenom</th>\n";
                        echo "<th>Date_birth</th>\n";
                        echo "<th>Grade</th>\n";
                        echo "<th>Function</th>\n";
                        echo "<th>Disciplines</th>\n";
                        echo "</tr>\n";
                        $cpt=0;
                        while($cpt<count($prof)){
                            echo "<tr>\n";
                            echo "<td style='color: blue;'>$cpt</td>\n";
                            echo "<td>".$prof[$cpt]["id"]."</td>\n";
                            echo "<td>".$prof[$cpt]["type"]."</td>\n";
                            echo "<td>".$prof[$cpt]["sexe"]."</td>\n";
                            echo "<td>".$prof[$cpt]["civilite"]."</td>\n";
                            echo "<td>".$prof[$cpt]["nom_usage"]."</td>\n";
                            echo "<td>".$prof[$cpt]["nom_patronymique"]."</td>\n";
                            echo "<td>".$prof[$cpt]["prenom"]."</td>\n";
                            echo "<td>".$prof[$cpt]["date_naissance"]."</td>\n";
                            echo "<td>".$prof[$cpt]["grade"]."</td>\n";
                            echo "<td>".$prof[$cpt]["fonction"]."</td>\n";

                            echo "<td align='center'>\n";

                            if($prof[$cpt]["fonction"]=="ENS"){
                                echo "<table border='1'>\n";
                                echo "<tr>\n";
                                echo "<th>Code</th>\n";
                                echo "<th>short name</th>\n";
                                echo "<th>Nb_hours</th>\n";
                                echo "</tr>\n";
                                for($j=0;$j<count($prof[$cpt]["disciplines"]);$j++){
                                    echo "<tr>\n";
                                    echo "<td>".$prof[$cpt]["disciplines"][$j]["code"]."</td>\n";
                                    echo "<td>".$prof[$cpt]["disciplines"][$j]["libelle_court"]."</td>\n";
                                    echo "<td>".$prof[$cpt]["disciplines"][$j]["nb_heures"]."</td>\n";
                                    echo "</tr>\n";
                                }
                                echo "</table>\n";
                            }

                            echo "</td>\n";
                            echo "</tr>\n";
                            $cpt++;
                        }
                        echo "</table>\n";
                        echo "</blockquote>\n";

                        echo "<p style='color:red;'><b>To make</b>: a file Profs for GEPI...</p>\n";






                        $temoin_au_moins_une_matiere="";
                        $temoin_au_moins_un_prof="";
                        // Affichage des infos Enseignements et divisions:
                        echo "<a name='divisions'></a><h3>Display of divisions</h3>\n";
                        echo "<blockquote>\n";
                        for($i=0;$i<count($divisions);$i++){
                            //echo "<p>\$divisions[$i][\"code\"]=".$divisions[$i]["code"]."<br />\n";
                            echo "<h4>Class of ".$divisions[$i]["code"]."</h4>\n";
                            echo "<ul>\n";
                            for($j=0;$j<count($divisions[$i]["services"]);$j++){
                                //echo "\$divisions[$i][\"services\"][$j][\"code_matiere\"]=".$divisions[$i]["services"][$j]["code_matiere"]."<br />\n";
                                echo "<li>\n";
                                for($m=0;$m<count($matiere);$m++){
                                    if($matiere[$m]["code"]==$divisions[$i]["services"][$j]["code_matiere"]){
                                        //echo "\$matiere[$m][\"code_gestion\"]=".$matiere[$m]["code_gestion"]."<br />\n";
                                        echo "Course: ".$matiere[$m]["code_gestion"]."<br />\n";
                                        $temoin_au_moins_une_matiere="oui";
                                    }
                                }
                                echo "<ul>\n";
                                for($k=0;$k<count($divisions[$i]["services"][$j]["enseignants"]);$k++){
                                //$divisions[$i]["services"][$j]["enseignants"][$k]["id"]
                                    for($m=0;$m<count($prof);$m++){
                                        if($prof[$m]["id"]==$divisions[$i]["services"][$j]["enseignants"][$k]["id"]){
                                            //echo $prof[$m]["nom_usage"]." ".$prof[$m]["prenom"]."|";
                                            echo "<li>\n";
                                            echo "Teacher: ".$prof[$m]["nom_usage"]." ".$prof[$m]["prenom"];
                                            echo "</li>\n";
                                            $temoin_au_moins_un_prof="oui";
                                        }
                                    }
                                }
                                echo "</ul>\n";
                                //echo "<br />\n";
                                echo "</li>\n";
                            }
                            echo "</ul>\n";
                            //echo "</p>\n";
                        }
                        echo "</blockquote>\n";
                        echo "</blockquote>\n";





    /*
                        echo "<a name='se3'></a><h2>Génération du CSV (F_WIND.txt) des profs pour SE3</h2>\n";
                        echo "<blockquote>\n";
                        $cpt=0;
                        while($cpt<count($prof)){
                            if($prof[$cpt]["fonction"]=="ENS"){
                                $date=str_replace("-","",$prof[$cpt]["date_naissance"]);
                                echo "P".$prof[$cpt]["id"]."|".$prof[$cpt]["nom_usage"]."|".$prof[$cpt]["prenom"]."|".$date."|".$prof[$cpt]["sexe"]."<br />\n";
                            }
                            $cpt++;
                        }
                        echo "<p>Vous pouvez copier/coller ces lignes dans un fichier texte pour effectuer l'import des comptes profs dans SambaEdu3.</p>\n";
                        echo "</blockquote>\n";




                        echo "<a name='f_div'></a><h2>Génération d'un CSV du F_DIV pour SambaEdu3</h2>\n";
                        echo "<blockquote>\n";
                        for($i=0;$i<count($divisions);$i++){
                            $numind_pp="";
                            for($m=0;$m<count($prof);$m++){
                                for($n=0;$n<count($prof[$m]["prof_princ"]);$n++){
                                    if($prof[$m]["prof_princ"][$n]["code_structure"]==$divisions[$i]["code"]){
                                        $numind_pp="P".$prof[$m]["id"];
                                    }
                                }
                            }
                            echo $divisions[$i]["code"]."|".$divisions[$i]["code"]."|".$numind_pp."<br />\n";
                        }
                        if($temoin_au_moins_un_prof_princ!="oui"){
                            echo "<p>Il semble que votre fichier ne comporte pas l'information suivante:<br />Qui sont les profs principaux?<br />Cela n'empêche cependant pas l'import du CSV dans SambaEdu3.</p>\n";
                        }
                        echo "</blockquote>\n";




                        echo "<a name='f_men'></a><h2>Génération d'un CSV du F_MEN pour SambaEdu3</h2>\n";
                        echo "<blockquote>\n";
                        if(($temoin_au_moins_une_matiere=="")||($temoin_au_moins_un_prof=="")){
                            echo "<p>Votre fichier ne comporte pas suffisamment d'informations pour générer ce CSV.<br />Il faut que les emplois du temps soient remontés vers STS pour que le fichier XML permette de générer ce CSV.</p>\n";
                        }
                        else{
                            for($i=0;$i<count($divisions);$i++){
                                //$divisions[$i]["services"][$j]["code_matiere"]
                                $classe=$divisions[$i]["code"];
                                for($j=0;$j<count($divisions[$i]["services"]);$j++){
                                    $mat="";
                                    for($m=0;$m<count($matiere);$m++){
                                        if($matiere[$m]["code"]==$divisions[$i]["services"][$j]["code_matiere"]){
                                            $mat=$matiere[$m]["code_gestion"];
                                        }
                                    }
                                    if($mat!=""){
                                        for($k=0;$k<count($divisions[$i]["services"][$j]["enseignants"]);$k++){
                                            echo $mat."|".$classe."|P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"]."<br />\n";
                                        }
                                    }
                                }
                            }
                        }
                        echo "</blockquote>\n";

    */
    //==================================================================

                        echo "<h2>Suppression of the existing CSV</h2>\n";
                        echo "<blockquote>\n";
                        echo "<p>If files CSV were already generated, one will start by removing them before generating the new ones...</p>\n";
                        $tabfich=array("f_wind.csv","f_men.csv","f_gpd.csv","f_div.csv","f_tmt.csv","profs.html");
                        for($i=0;$i<count($tabfich);$i++){
                            if(file_exists("../backup/$dirname/csv/$tabfich[$i]")){
                                echo "<p>Suppression de $tabfich[$i]... ";
                                if(unlink("../backup/$dirname/csv/$tabfich[$i]")){
                                    echo "réussie.</p>\n";
                                }
                                else{
                                    echo "<font color='red'>Echec!</font> Check the rights of writing on the server.</p>\n";
                                }
                            }
                        }
                        echo "<p>Finished.</p>\n";
                        echo "</blockquote>\n";



                        echo "<a name='gepi'></a>\n";

                        echo "<a name='f_wind_gepi'></a><h2>Generation of the CSV (F_WIND.CSV) of Profs for GEPI</h2>\n";
                        echo "<blockquote>\n";
                        $cpt=0;
                        $fich=fopen("../backup/$dirname/csv/f_wind.csv","w+");
                        $chaine="AINOMU;AIPREN;AICIVI;NUMIND;FONCCO;INDNNI";
                        if($fich){
                            //fwrite($fich,$chaine."\n");
                            fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                        }
                        echo $chaine."<br />\n";

                        if($_POST['mdp']=="alea"){
                            $fich2=fopen("../backup/$dirname/csv/profs.html","w+");
                            fwrite($fich2,"<?php
@set_time_limit(0);

// Initialisations files
require_once('../lib/initialisations.inc.php');

// Resume session
\$resultat_session = $session_gepi->security_check();
if (\$resultat_session == 'c') {
header('Location: ../utilisateurs/mon_compte.php?change_mdp=yes');
die();
} else if (\$resultat_session == '0') {
header('Location: ../logout.php?auto=1');
die();
};

if (!checkAccess()) {
header('Location: ../logout.php?auto=1');
die();
}
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
    <title>File Profs</title>
    <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-15' />
    <meta name='author' content='Stephane Boireau, A.S. RUE de Bernay/Pont-Audemer' />
    <link type='text/css' rel='stylesheet' href='../../style.css' />
</head>
<body>
<h1 align='center'>File of the initial passwords of the professors</h1>
<table border='1'>
<tr>
<th>Name</th>
<th>First name</th>
<th>Civility</th>
<th>Password</th>
</tr>\n");
                        }

                        while($cpt<count($prof)){
                            if($prof[$cpt]["fonction"]=="ENS"){

                                if($prof[$cpt]["sexe"]=="1"){
                                    $civi="M.";
                                }
                                else{
                                    $civi="MM";
                                }

                                switch($prof[$cpt]["civilite"]){
                                    case 1:
                                        $civi="M.";
                                        break;
                                    case 2:
                                        $civi="MM";
                                        break;
                                    case 3:
                                        $civi="ML";
                                        break;
                                }

                                if($_POST['mdp']=="alea"){
                                    $mdp=createRandomPassword();
                                }
                                else{
                                    $date=str_replace("-","",$prof[$cpt]["date_naissance"]);
                                    $mdp=$date;
                                }
                                //echo $prof[$cpt]["nom_usage"].";".$prof[$cpt]["prenom"].";".$civi.";"."P".$prof[$cpt]["id"].";"."ENS".";".$date."<br />\n";
                                $chaine=$prof[$cpt]["nom_usage"].";".$prof[$cpt]["prenom"].";".$civi.";"."P".$prof[$cpt]["id"].";"."ENS".";".$mdp;
                                if($fich){
                                    fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                                }
                                if($_POST['mdp']=="alea"){
                                    fwrite($fich2,"<tr>
<td>".$prof[$cpt]["nom_usage"]."</td>
<td>".$prof[$cpt]["prenom"]."</td>
<td>$civi</td>
<td>$mdp</td>
</tr>\n");
                                }
                                echo $chaine."<br />\n";
                            }
                            $cpt++;
                        }
                        fclose($fich);
                        if($_POST['mdp']=="alea"){
                            fwrite($fich2,"</table>
<p>Print this page, then remove it while carrying out cleaning as indicated in the previous page.</p>
</body>
</html>\n");
                            fclose($fich2);
                        }
                        echo "</blockquote>\n";



                        echo "<a name='f_men_gepi'></a><h2>Generation of a CSV of the F_MEN for GEPI</h2>\n";
                        echo "<blockquote>\n";
                        if(($temoin_au_moins_une_matiere=="")||($temoin_au_moins_un_prof=="")){
                            echo "<p>Your file does not comprise sufficient information to generate this CSV.<br />It is necessary that the timetables went up towards STS so that file XML makes it possible to generate this CSV.</p>\n";
                        }
                        else{
                            $fich=fopen("../backup/$dirname/csv/f_men.csv","w+");
                            $chaine="MATIMN;NUMIND;ELSTCO";
                            if($fich){
                                fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                            }
                            echo $chaine."<br />\n";
                            for($i=0;$i<count($divisions);$i++){
                                //$divisions[$i]["services"][$j]["code_matiere"]
                                $classe=$divisions[$i]["code"];
                                for($j=0;$j<count($divisions[$i]["services"]);$j++){
                                    $mat="";
                                    for($m=0;$m<count($matiere);$m++){
                                        if($matiere[$m]["code"]==$divisions[$i]["services"][$j]["code_matiere"]){
                                            $mat=$matiere[$m]["code_gestion"];
                                        }
                                    }
                                    if($mat!=""){
                                        for($k=0;$k<count($divisions[$i]["services"][$j]["enseignants"]);$k++){
                                            //echo $mat."|".$classe."|P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"]."<br />\n";
                                            //echo $mat.";P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"].";".$classe."<br />\n";
                                            $chaine=$mat.";P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"].";".$classe;
                                            if($fich){
                                                fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                                            }
                                            echo $chaine."<br />\n";
                                        }
                                    }
                                }
                            }

                            //echo "<hr width='200' />\n";
                            for($i=0;$i<count($groupes);$i++){
                                $grocod=$groupes[$i]["code"];
                                //echo "<p>Groupe $i: \$grocod=$grocod<br />\n";
                                for($m=0;$m<count($matiere);$m++){
                                    //echo "\$matiere[$m][\"code\"]=".$matiere[$m]["code"]." and \$groupes[$i][\"code_matiere\"]=".$groupes[$i]["code_matiere"]."<br />\n";
                                    if($matiere[$m]["code"]==$groupes[$i]["code_matiere"]){
                                        //$matimn=$programme[$k]["code_matiere"];
                                        $matimn=$matiere[$m]["code_gestion"];
                                        //echo "<b>Found: course n°$m: \$matimn=$matimn</b><br />\n";
                                    }
                                }
                                //$groupes[$i]["enseignant"][$m]["id"]
                                //$groupes[$i]["divisions"][$j]["code"]
                                if($matimn!=""){
                                    for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
                                        $elstco=$groupes[$i]["divisions"][$j]["code"];
                                        //echo "\$elstco=$elstco<br />\n";
                                        if(count($groupes[$i]["enseignant"])==0){
                                            $chaine="$matimn;;$elstco";
                                            if($fich){
                                                fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                                            }
                                            echo $chaine."<br />\n";
                                        }
                                        else{
                                            for($m=0;$m<count($groupes[$i]["enseignant"]);$m++){
                                                $numind=$groupes[$i]["enseignant"][$m]["id"];
                                                //echo "$matimn;P$numind;$elstco<br />\n";
                                                $chaine="$matimn;P$numind;$elstco";
                                                if($fich){
                                                    fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                                                }
                                                echo $chaine."<br />\n";
                                            }
                                        }
                                        //echo $grocod.";".$groupes[$i]["divisions"][$j]["code"]."<br />\n";
                                    }
                                }


/*
                                $matimn="";
                                //for($j=0;$j<count($groupes[$i]["mef"]);$j++){
                                    //$mef=$groupes[$i]["mef"][$j];
                                    $mef=$groupes[$i]["mef"][0];
                                    for($k=0;$k<count($programme);$k++){
                                        if($mef==$programme[$k]["code_mef"]){
                                            for($m=0;$m<count($matiere);$m++){
                                                if($matiere[$m]["code"]==$programme[$k]["code_matiere"]){
                                                    //$matimn=$programme[$k]["code_matiere"];
                                                    $matimn=$matiere[$m]["code_gestion"];
                                                }
                                            }
                                        }
                                    }
                                //}
                                if($matimn!=""){
                                    // Rechercher le NUMIND...
                                    //...
                                    //$groupes[$i]["enseignant"][$m]["id"]

                                    // Et enfin pour la liste des classes, on affiche une ligne:
                                    for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
                                        $elstco=$groupes[$i]["divisions"][$j]["code"];
                                        for($m=0;$m<count($groupes[$i]["enseignant"]);$m++){
                                            $numind=$groupes[$i]["enseignant"][$m]["id"];
                                            echo "$matimn;P$numind;$elstco<br />\n";
                                        }
                                        //echo $grocod.";".$groupes[$i]["divisions"][$j]["code"]."<br />\n";
                                    }
                                }
*/
                            }
                            fclose($fich);
                        }
                        echo "<p>I do not know too much for the prefix P.<br />It is not in file XML, but is used by SE3...<br />And on the other hand, on the F_WIND.DBF generated by AutoSco, there is a prefix E.</p>";
                        echo "</blockquote>\n";













                        echo "<a name='f_gpd_gepi'></a><h2>Generation of a CSV of the F_GPD for GEPI</h2>\n";
                        echo "<blockquote>\n";
    /*
                        if(($temoin_au_moins_une_matiere=="")||($temoin_au_moins_un_prof=="")){
                            echo "<p>Your file does not comprise sufficient information to generate this CSV.<br />It is necessary that the timetables went up towards STS so that file XML makes it possible to generate this CSV.</p>\n";
                        }
                        else{
    */
                            //echo "GROCOD;DIVCOD<br />\n";
                            $fich=fopen("../backup/$dirname/csv/f_gpd.csv","w+");
                            $chaine="GROCOD;DIVCOD";
                            if($fich){
                                fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                            }
                            echo $chaine."<br />\n";

                            for($i=0;$i<count($groupes);$i++){
                                //$divisions[$i]["services"][$j]["code_matiere"]
                                $grocod=$groupes[$i]["code"];
                                for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
                                    //echo $grocod.";".$groupes[$i]["divisions"][$j]["code"]."<br />\n";
                                    $chaine=$grocod.";".$groupes[$i]["divisions"][$j]["code"];
                                    if($fich){
                                        fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                                    }
                                    echo $chaine."<br />\n";
                                }
                            }
    //                  }
                        fclose($fich);
                        echo "</blockquote>\n";



                        echo "<a name='f_tmt_gepi'></a><h2>Generation of a CSV of the F_TMT for GEPI</h2>\n";
                        echo "<blockquote>\n";
                        //echo "MATIMN;MATILC<br />\n";
                        $fich=fopen("../backup/$dirname/csv/f_tmt.csv","w+");
                        $chaine="MATIMN;MATILC";
                        if($fich){
                            fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                        }
                        echo $chaine."<br />\n";
                        for($i=0;$i<count($matiere);$i++){
                            //echo $matiere[$i]["code_gestion"].";".$matiere[$i]["libelle_court"]."<br />\n";
                            $chaine=$matiere[$i]["code_gestion"].";".$matiere[$i]["libelle_court"];
                            if($fich){
                                fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                            }
                            echo $chaine."<br />\n";
                        }
                        fclose($fich);
                        echo "</blockquote>\n";



                        echo "<a name='f_div_gepi'></a><h2>Génération d'un CSV du F_DIV pour GEPI</h2>\n";
                        echo "<blockquote>\n";
                        $fich=fopen("../backup/$dirname/csv/f_div.csv","w+");
                        $chaine="DIVCOD;NUMIND";
                        if($fich){
                            fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                        }
                        echo $chaine."<br />\n";
                        for($i=0;$i<count($divisions);$i++){
                            $numind_pp="";
                            for($m=0;$m<count($prof);$m++){
                                for($n=0;$n<count($prof[$m]["prof_princ"]);$n++){
                                    if($prof[$m]["prof_princ"][$n]["code_structure"]==$divisions[$i]["code"]){
                                        $numind_pp="P".$prof[$m]["id"];
                                    }
                                }
                            }
                            //echo $divisions[$i]["code"].";".$divisions[$i]["code"].";".$numind_pp."<br />\n";
                            $chaine=$divisions[$i]["code"].";".$numind_pp;
                            if($fich){
                                fwrite($fich,html_entity_decode_all_version($chaine)."\n");
                            }
                            echo $chaine."<br />\n";
                        }
                        fclose($fich);
                        echo "<p>This CSV is intended to inform Principals Professors ...</p>\n";
                        echo "</blockquote>\n";

                        //echo "<div style='position:absolute; top: 50px; left: 50px; width: 300px; height: 200px; background: yellow; border: 1px solid black;'>\n";
                        echo "<div style='position:absolute; top: 70px; left: 300px; width: 300px; background: yellow; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-top: 0; '>\n";
                        echo "<h4 style='margin:0; padding:0; text-align:center;'>GEPI</h4>\n";
                        //echo "<p style='margin-top: 0;'>Make a Clic-right/save the target of the link as... for each file below.</p>\n";
                        echo "<p style='margin-top: 0;'>Recover the following CSV (<i>not by clic-right</i>).</p>\n";
                        echo "<table border='0'>\n";
/*
                        echo "<li>File Teachers: <a href='../backup/$dirname/csv/f_wind.csv'>f_wind.csv</a></li>\n";
                        echo "<li>File Classes/courses/profs: <a href='../backup/$dirname/csv/f_men.csv'>f_men.csv</a></li>\n";
                        echo "<li>File Groups/classes: <a href='../backup/$dirname/csv/f_gpd.csv'>f_gpd.csv</a></li>\n";
                        echo "<li>File courses: <a href='../backup/$dirname/csv/f_tmt.csv'>f_tmt.csv</a></li>\n";
                        echo "<li>File principal Profs : <a href='../backup/$dirname/csv/f_div.csv'>f_div.csv</a></li>\n";
*/
                        echo "<tr><td>File Profs:</td><td><a href='save_csv.php?fileid=0'>f_wind.csv</a></td></tr>\n";
                        echo "<tr><td>File Classes/courses/profs:</td><td><a href='save_csv.php?fileid=1'>f_men.csv</a></td></tr>\n";
                        echo "<tr><td>File Groups/classes:</td><td><a href='save_csv.php?fileid=2'>f_gpd.csv</a></td></tr>\n";
                        echo "<tr><td>File courses:</td><td><a href='save_csv.php?fileid=3'>f_tmt.csv</a></td></tr>\n";
                        echo "<tr><td>File principals Profs :</td><td><a href='save_csv.php?fileid=4'>f_div.csv</a></td></tr>\n";

                        echo "</table>\n";
                        if($_POST['mdp']=="alea"){
                            echo "<p>Here is also a <a href='../backup/$dirname/csv/profs.html' target='_blank'>page of the initial passwords of the professors</a> to print before carrying out cleaning below.</p>\n";
                        }
                        echo "<p>To remove the files after recovery: <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui'>Cleaning</a></p>\n";
                        echo "</div>\n";
                    }
                    else{
                        echo "<p>ERROR!<br /><a href='".$_SERVER['PHP_SELF']."'>Return</a>.</p>\n";
                    }
                }
            }

        ?>
        <p>Retour à l'<a href="index.php">index</a></p>
    </div>
</body>
</html>