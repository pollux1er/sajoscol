<?php
	// Initialisations files
	require_once("../lib/initialisations.inc.php");
function saveAction($sql) {
	
	$filename = '../responsables/responsable.txt';
	$somecontent = $sql.";\n";

	// Assurons nous que le fichier est accessible en �criture
	if (is_writable($filename)) {

		if (!$handle = fopen($filename, 'a')) {
			 echo "Impossible d'ouvrir le fichier ($filename)";
			 exit;
		}

		// Ecrivons quelque chose dans notre fichier.
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Impossible d'�crire dans le fichier ($filename)";
			exit;
		}

		//echo "L'�criture de ($somecontent) dans le fichier ($filename) a r�ussi";

		fclose($handle);

	} else {
		echo "Le fichier $filename n'est pas accessible en �criture.";
	}
}

function updateOnline($sql) {
	$hostname = "173.254.25.235";
	$username = "sajoscol_gepi";
	$password = ";?5tvu45l-Lu";
	$databasename = "sajoscol_appli";
	$con = mysql_pconnect("$hostname", "$username", "$password");
	if (!$con) {
		saveAction($sql); //die('Could not connect: ' . mysql_error());
		//echo "Connexion reussi!"; 
		if(mysql_select_db($databasename, $con)) { 
			if (mysql_query($sql)) { 
				echo "<script type='text/javascript'>alert('Successly updated online!');</script>"; 
			} else {
				echo mysql_error();
			}
		}
	}
	
}
	// Ajouter des tests sur le statut du visiteur
	if(($_SESSION['statut']!='administrateur')&&($_SESSION['statut']!='scolarite')&&($_SESSION['statut']!="professeur")) {
		echo "<p style='color:red;'>Acc�s non autoris�.</p>";
		die();
	}

	if($_SESSION['statut']=="professeur") {
		if(getSettingValue('GepiAccesRestrAccesAppProfP')!="yes") {
			$msg="Prohibited access to the parameter setting of the access to  appreciatons/opinion for the parents and students.";
			header("Location: ../accueil.php?msg=".rawurlencode($msg));
			die();
		}

		$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec
					WHERE jep.professeur='".$_SESSION['login']."' AND
						jep.login=jec.login AND
						jec.id_classe='$id_classe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0){
			$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
			$msg="Vous n'�tes pas ".$gepi_prof_suivi." de la classe choisie.<br />You should not thus access this page.";
			header("Location: ../accueil.php?msg=".rawurlencode($msg));
			die();
		}
	}
	elseif($_SESSION['statut']=='scolarite') {
		$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;
		$sql="SELECT 1=1 FROM j_scol_classes jsc
						WHERE jsc.login='".$_SESSION['login']."' AND
							jsc.id_classe='$id_classe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0){
			$msg="You are not responsible for the selected class.<br />You should not thus access to this page.";
			header("Location: ../accueil.php?msg=".rawurlencode($msg));
			die();
		}
	}

	//debug_var();

	check_token();

	$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;     // entier
	if(strlen(preg_replace('/[0-9]/','',$id_classe))!=0) {$id_classe=NULL;}
	if($id_classe=='') {$id_classe=NULL;}

	$periode=isset($_GET['periode']) ? $_GET['periode'] : NULL;           // entier
	if(strlen(preg_replace('/[0-9]/','',$periode))!=0) {$periode=NULL;}
	if($periode=='') {$periode=NULL;}

	$statut=isset($_GET['statut']) ? $_GET['statut'] : NULL;              // eleve, responsable ou ele_resp
	$tab_statuts=array('eleve','responsable','ele_resp');
	if(!in_array($statut,$tab_statuts)) {$statut='eleve';}

	if($statut=='ele_resp') {$tab_liste_statuts=array('eleve','responsable');}
	else {$tab_liste_statuts=array("$statut");}

	$id_div=isset($_GET['id_div']) ? $_GET['id_div'] : NULL;              // ...
	if(strlen(preg_replace('/[0-9A-Za-z_]/','',$id_div))!=0) {$id_div=NULL;$id_classe=NULL;}

	if($_GET['mode']=='manuel') {

		if(($id_classe!=NULL)&&($periode!=NULL)) {
			for($i=0;$i<count($tab_liste_statuts);$i++) {
				$sql="SELECT acces FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
				//echo $sql;
				$test_acces=mysql_query($sql);
				if (mysql_num_rows($test_acces)==0) {
					$sql="INSERT INTO matieres_appreciations_acces SET acces='n', id_classe='$id_classe', periode='$periode', statut='$tab_liste_statuts[$i]';";
					//echo "$sql<br />";
					$insert=mysql_query($sql);
					updateOnline($sql);
	
					echo "<div style='background-color:orangered;'>";
					if($statut=='ele_resp') {
						echo "Inaccessible</div>\n";
					}
					else {
						echo "Manuel</div>\n";
					}
				}
				else {
					$lig_acces=mysql_fetch_object($test_acces);
	
					if($lig_acces->acces=='y') {
						$sql="UPDATE matieres_appreciations_acces SET acces='n' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
						//echo "$sql<br />";
						$update=mysql_query($sql);
						updateOnline($sql);

						// On n'�crit le DIV de couleur qu'une fois
						if($i==0) {
							echo "<div style='background-color:orangered;'>";
							if($statut=='ele_resp') {
								echo "Inaccessible</div>\n";
							}
							else {
								echo "Manuel</div>\n";
							}
						}
					}
					else {
						$sql="UPDATE matieres_appreciations_acces SET acces='y' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
						$update=mysql_query($sql);
						//echo "$sql<br />";
                         updateOnline($sql);
						// On n'�crit le DIV de couleur qu'une fois
						if($i==0) {
							echo "<div style='background-color:lightgreen;'>";
							if($statut=='ele_resp') {
								echo "Accessible</div>\n";
							}
							else {
								echo "Manual</div>\n";
							}
						}
					}
				}
			}
		}
	}
	elseif($_GET['mode']=='date') {
		$choix_date=isset($_GET['choix_date']) ? $_GET['choix_date'] : NULL;  // Contr�ler que la date est valide

		$poursuivre="y";
		if($choix_date=='') {
			$poursuivre="n";
			//echo "<script type='text/javascript'>alert('Veuillez saisir une date valide.');</script>\n";
			echo "<span style='color:red'>Invalid typing date</span>";
		}
		elseif(!my_ereg("[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}",$choix_date)) {
			$poursuivre="n";
			echo "<span style='color:red'>Invalid typing date</span>";
		}
		else {
			$tabdate=explode("/",$choix_date);
			$jour=$tabdate[0];
			$mois=$tabdate[1];
			$annee=$tabdate[2];

			if(!checkdate($mois,$jour,$annee)) {
				$poursuivre="n";
				echo "<span style='color:red'>Invalid typing date</span>";
			}
		}

		if($poursuivre=="y") {
			$choix_date=$annee."-".$mois."-".$jour;
			$display_date=$jour."/".$mois."/".$annee;
	
			if(($id_classe!=NULL)&&($periode!=NULL)) {
				for($i=0;$i<count($tab_liste_statuts);$i++) {
					$sql="SELECT acces FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
					//echo $sql;
					$test_acces=mysql_query($sql);
					if (mysql_num_rows($test_acces)==0) {
						$sql="INSERT INTO matieres_appreciations_acces SET acces='date', date='".$choix_date."', id_classe='$id_classe', periode='$periode', statut='$tab_liste_statuts[$i]';";
						//echo "$sql<br />";
						$insert=mysql_query($sql);updateOnline($sql);
					}
					else {
						$sql="UPDATE matieres_appreciations_acces SET acces='date', date='".$choix_date."' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
						//echo "$sql<br />";
						$update=mysql_query($sql);updateOnline($sql);
					}
	
					// On n'�crit le DIV de couleur qu'une fois
					if($i==0) {
						$timestamp_limite=mktime(0,0,0,$mois,$jour,$annee);
						$timestamp_courant=time();
						if($timestamp_courant>=$timestamp_limite) {
							echo "<div style='background-color:lightgreen;'>";
							if($statut=='ele_resp') {
								echo "Accessible&nbsp;: \n";
							}
							else {
								echo "Date&nbsp;: \n";
							}
						}
						else {
							echo "<div style='background-color:orangered;'>";
							if($statut=='ele_resp') {
								echo "Inaccessible&nbsp;: \n";
							}
							else {
								echo "Date&nbsp;: \n";
							}
						}
	
						echo "$display_date</div>\n";
					}
				}
			}
		}
	}
	elseif($_GET['mode']=='d') {

		if(($id_classe!=NULL)&&($periode!=NULL)) {
			for($i=0;$i<count($tab_liste_statuts);$i++) {
				$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
				//echo "$sql<br />";
				$update=mysql_query($sql);updateOnline($sql);
	
				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='$periode';";
				$res_per=mysql_query($sql);
				$lig_per=mysql_fetch_object($res_per);
				if($lig_per->verouiller!='O') {
					$accessible="n";
	
					if($lig_per->verouiller='P') {$etat_periode="Partially closed period";} else {$etat_periode="Open period";}
				}
				else {
					$delais_apres_cloture=getSettingValue('delais_apres_cloture');
	
					$tmp_tabdate=explode(" ",$lig_per->date_verrouillage);
					$tabdate=explode("-",$tmp_tabdate[0]);
					$jour=$tabdate[2];
					$mois=$tabdate[1];
					$annee=$tabdate[0];
	
					$timestamp_limite=mktime(0,0,0,$mois,$jour,$annee)+$delais_apres_cloture*24*3600;
					$timestamp_courant=time();
					if($timestamp_courant>=$timestamp_limite) {
						$accessible="y";
						if($annee=='0000') {
							$etat_periode="Accessible since the period closure";
						}
						else {
							$etat_periode="Accessible since<br />the $jour/$mois/$annee";
							//if($delais_apres_cloture>0) {echo " + ".$delais_apres_cloture."j";}
						}
					}
					else {
						$accessible="n";
						$tmp_date=getdate($timestamp_limite);
						$jour=sprintf("%02d",$tmp_date['mday']);
						$mois=sprintf("%02d",$tmp_date['mon']);
						$annee=$tmp_date['year'];
						$etat_periode="Possible access <br />the $jour/$mois/$annee";
					}
	
					//$etat_periode="P�riode close: $jour/$mois/$annee";
				}

				// On n'�crit le DIV de couleur qu'une fois
				if($i==0) {
	
					if($accessible=="y") {
						echo "<div style='background-color:lightgreen;'>";
					}
					else {
						echo "<div style='background-color:orangered;'>";
					}
					echo "$etat_periode</div>\n";
				}
			}
		}
	}
?>