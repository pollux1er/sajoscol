<?php
	/*
	$Id: tableau_choix_parametres_releves_notes.php 8469 2011-10-13 11:24:20Z crob $
	*/

	echo "<table class='boireaus' border='1' summary='Tableau des items'>\n";
	echo "<tr>\n";
	//echo "<th width='30%'>Item</th>\n";
	echo "<th>Item</th>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<th>".get_class_from_id($tab_id_classe[$i])."</th>\n";
	}
	echo "<th>\n";
	echo "<a href=\"javascript:ToutCocher()\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:ToutDeCocher()\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>";
	echo "</th>\n";
	echo "</tr>\n";

	$gepiProfSuivi=getSettingValue("gepi_prof_suivi");

	$tab_item=array();
	$tab_item[]='rn_nomdev';
	$tab_traduc['rn_nomdev']="With the name of the exams";
	$tab_item[]='rn_toutcoefdev';
	$tab_traduc['rn_toutcoefdev']="With coefficients";
	$tab_item[]='rn_coefdev_si_diff';
	$tab_traduc['rn_coefdev_si_diff']="With coefficients if there are several different coefficients";
	//$tab_item[]='rn_app';
	//$tab_traduc['rn_app']="Avec l'appréciation (sous réserve d'autorisation par le professeur)";
	$tab_item[]='rn_datedev';
	$tab_traduc['rn_datedev']="With the dates";
	$tab_item[]='rn_sign_chefetab';
	$tab_traduc['rn_sign_chefetab']="With box for signature of the head of school (<i>HTML report</i>)";
	$tab_item[]='rn_sign_pp';
	$tab_traduc['rn_sign_pp']="With box for signature of $gepiProfSuivi";
	$tab_item[]='rn_sign_resp';
	$tab_traduc['rn_sign_resp']="With box for signature of the responsible";

	/*
	$tab_item[]='rn_sign_nblig';
	$tab_traduc['rn_sign_nblig']="Nombre de lignes pour la signature";
	$tab_item[]='rn_formule';
	$tab_traduc['rn_formule']="Formule à afficher en bas de page";
	*/
	// Il manque $avec_appreciation_devoir
	$chaine_coef="coef.: ";

	//++++++++++++
	// A REVOIR: ON FAIT LES MEMES REQUETES A PLUSIEURS REPRISES...
	//++++++++++++

	$alt=1;
	// Affichage du nom de la classe Nom long  Nom court  Nom long (Nom court)
	//$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td style='text-align:left;'>display of the name of the class (<i>relevé PDF</i>)<br />\n";
	echo "Long name (1) / Short name (2) / Short name (Long name) (3)";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<td>\n";
		//echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."' value='1' />&nbsp;1<br />\n";
		//echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."' value='2' />&nbsp;2<br />\n";
		//echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."' value='3' />&nbsp;3<br />\n";
		echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."_1' value='1' checked /><br />\n";
		echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."_2' value='2' /><br />\n";
		echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."_3' value='3' />\n";
		echo "</td>\n";
	}

	echo "<td>\n";
	//echo "&nbsp;";
	echo "Long name <br />\n";
	echo "Short name<br />\n";
	echo "Short name (Long name )\n";
	echo "</td>\n";
	echo "</tr>\n";


	for($k=0;$k<count($tab_item);$k++) {
		$affiche_ligne="y";
		if ((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&(my_ereg("^rn_sign",$tab_item[$k]))) {
			$affiche_ligne="n";
		}

		if($affiche_ligne=="y") {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			//echo "<td style='text-align:left;'>".$tab_traduc[$tab_item[$k]]."\n";
			echo "<td style='text-align:left;'>".$tab_traduc[$tab_item[$k]]."\n";
			echo "</td>\n";

			for($i=0;$i<count($tab_id_classe);$i++) {
				echo "<td>\n";
				echo "<input type='checkbox' name='".$tab_item[$k]."[$i]' id='".$tab_item[$k]."_".$i."' value='y' ";
				$sql="SELECT * FROM classes WHERE id='".$tab_id_classe[$i]."';";
				$res_class_tmp=mysql_query($sql);
				if(mysql_num_rows($res_class_tmp)>0){
					$lig_class_tmp=mysql_fetch_object($res_class_tmp);

					if($lig_class_tmp->$tab_item[$k]=="y") {echo "checked ";}
				}
				echo "/>\n";
				echo "</td>\n";
			}

			echo "<td>\n";
			echo "<a href=\"javascript:CocheLigne('".$tab_item[$k]."')\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:DecocheLigne('".$tab_item[$k]."')\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>";
			echo "</td>\n";
			echo "</tr>\n";
		}
	}

	//$tab_item[]='rn_app';
	//$tab_traduc['rn_app']="Avec l'appréciation (sous réserve d'autorisation par le professeur)";
	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td style='text-align:left;'>With the appreciation (<i>subject to authorization by the professor</i>)\n";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<td>\n";
		echo "<input type='checkbox' name='rn_app[$i]' id='rn_app_".$i."' size='2' value='y' />\n";
		echo "</td>\n";
	}

	echo "<td>\n";
	echo "<a href=\"javascript:CocheLigne('rn_app')\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:DecocheLigne('rn_app')\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>";
	echo "</td>\n";
	echo "</tr>\n";


	//=================================
	// 20100526
	// Il ne faut peut-être pas l'autoriser pour tous les utilisateurs?
	//if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>With the average of the class for each exam\n";
		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='checkbox' name='rn_moy_classe[$i]' id='rn_moy_classe_".$i."' size='2' value='y' />\n";
			echo "</td>\n";
		}
	
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_moy_classe')\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:DecocheLigne('rn_moy_classe')\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>";
		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>With the averages min/class/max of each exam\n";
		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='checkbox' name='rn_moy_min_max_classe[$i]' id='rn_moy_min_max_classe_".$i."' size='2' value='y' />\n";
			echo "</td>\n";
		}
	
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_moy_min_max_classe')\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:DecocheLigne('rn_moy_min_max_classe')\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>";
		echo "</td>\n";
		echo "</tr>\n";

	//}
	//=================================

	$rn_retour_ligne_defaut="y";
	if((isset($_SESSION['pref_rn_retour_ligne']))&&(($_SESSION['pref_rn_retour_ligne']=='y')||($_SESSION['pref_rn_retour_ligne']=='n'))) {
		$rn_retour_ligne_defaut=$_SESSION['pref_rn_retour_ligne'];
	}

	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td style='text-align:left;'>With return to the line after each exam if the name of the exam or the comment is displayed\n";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<td>\n";
		echo "<input type='checkbox' name='rn_retour_ligne[$i]' id='rn_retour_ligne_".$i."' size='2' value='y' ";
		if($rn_retour_ligne_defaut=='y') {echo "checked ";}
		echo "/>\n";
		echo "</td>\n";
	}
	echo "<td>\n";
	echo "<a href=\"javascript:CocheLigne('rn_retour_ligne')\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:DecocheLigne('rn_retour_ligne')\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>";
	echo "</td>\n";
	echo "</tr>\n";

	if(isset($_SESSION['pref_rn_rapport_standard_min_font'])) {
		$rn_rapport_standard_min_font_defaut=$_SESSION['pref_rn_rapport_standard_min_font'];
	}
	else {
		$rn_rapport_standard_min_font_defaut=getSettingValue('rn_rapport_standard_min_font_defaut');
		$rn_rapport_standard_min_font_defaut=(($rn_rapport_standard_min_font_defaut!='')&&(preg_match("/^[0-9.]*$/",$rn_rapport_standard_min_font_defaut))&&($rn_rapport_standard_min_font_defaut>0)) ? $rn_rapport_standard_min_font_defaut : 3;
	}

	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td style='text-align:left;'>Rapport taille_standard / taille_minimale_de_police (<i>PDF report with cell_ajustee()</i>)<br />(<i>If to make notes hold in the cell, it is necessary to reduce the police force,removes the returns to the line.</i>)\n";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<td>\n";
		echo "<input type='text' name='rn_rapport_standard_min_font[$i]' id='rn_rapport_standard_min_font_".$i."' size='2' value='".$rn_rapport_standard_min_font_defaut."' />\n";
		echo "</td>\n";
	}
	echo "<td>\n";
	echo "<a href=\"javascript:CocheLigne('rn_rapport_standard_min_font')\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:DecocheLigne('rn_rapport_standard_min_font')\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>";
	echo "</td>\n";
	echo "</tr>\n";


	if (($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
		// "Afficher le bloc adresse du responsable de l'élève :"
		// Non présent dans /classes/modify_nom_class.php?id_classe=...
		// mais il faudrait peut-être l'y ajouter...
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>Display the addresses block of the responsible for the student\n";
		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='checkbox' name='rn_adr_resp[$i]' id='rn_adr_resp_".$i."' size='2' value='y' />\n";
			echo "</td>\n";
		}
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_adr_resp')\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:DecocheLigne('rn_adr_resp')\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>";
		echo "</td>\n";
		echo "</tr>\n";


		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>Display the observations block(<i>PDF report</i>)\n";

		$titre_infobulle="Observations block in PDF\n";
		$texte_infobulle="<p>The observations block is displayed if one of the following conditions is filled&nbsp;:</p>\n";
		$texte_infobulle.="<ul>\n";
		$texte_infobulle.="<li>The box observations bloc  is checked.</li>\n";
		$texte_infobulle.="<li>One of the boxes signature is checked.</li>\n";
		$texte_infobulle.="</ul>\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_bloc_observations',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_bloc_observations','y',100,100);\"  onmouseout=\"cacher_div('a_propos_bloc_observations');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		echo "</p>\n";

		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='checkbox' name='rn_bloc_obs[$i]' id='rn_bloc_obs_".$i."' size='2' value='y' />\n";
			echo "</td>\n";
		}
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_bloc_obs')\"><img src='../images/enabled.png' width='15' height='15' alt='Check all' /></a> / <a href=\"javascript:DecocheLigne('rn_bloc_obs')\"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' /></a>";
		echo "</td>\n";
		echo "</tr>\n";


		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>Number of lines for the signature\n";
		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='text' name='rn_sign_nblig[$i]' id='rn_sign_nblig_".$i."' size='2' ";
			$sql="SELECT * FROM classes WHERE id='".$tab_id_classe[$i]."';";
			$res_class_tmp=mysql_query($sql);
			if(mysql_num_rows($res_class_tmp)>0){
				$lig_class_tmp=mysql_fetch_object($res_class_tmp);
				echo "value='$lig_class_tmp->rn_sign_nblig' ";
			}
			else {
				echo "value='3' ";
			}
			echo "/>\n";
			echo "</td>\n";
		}
		echo "<td>\n";
		//echo "&nbsp;";

		$titre_infobulle="Default settings\n";
		$texte_infobulle="The default settings are proposed according to the setting of the class.<br />\n";
		$texte_infobulle.="In administrator account&nbsp;: <b>Management of the bases/Management of the class/&lt;une_classe&gt; Parameters/Parameters of the report booklets</b><br />or<br /><b>Management of the bases/Management of the classes/Parameter setting of several classes by batches/Parameters of reports booklet</b>\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_parametres_defaut_releve',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_parametres_defaut_releve','y',100,100);\"  onmouseout=\"cacher_div('a_propos_parametres_defaut_releve');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<p>Préfixe pour les coefficients&nbsp;: \n";
		echo "<input type='text' name='chaine_coef' size='5' value='$chaine_coef' />\n";
		echo "</p>\n";

		//echo "<p>Formule à afficher en bas de page (<i>relevé HTML</i>):</p>\n";
		echo "<p>Formule à afficher en bas de page&nbsp;: \n";

		$titre_infobulle="Formula of bottom of page\n";
		$texte_infobulle="The formula of bottom of page (<i>by defect</i>) can be parameterized in <b>Management of the bases/Management of the classes/&lt;une_classe&gt; Parameters/Parameters of the report booklet</b><br />or<br /><b>Management of the bases/Management of the classes/Parameter setting of several classes by batches/Parameters of the report booklet</b><br />\n";
		$texte_infobulle.="&nbsp;<br />\n";
		$texte_infobulle.="If the formula in the field below is empty, it is the formula defined in <b>Parameters of HTML report</b> who is used.<br />\n";
		$texte_infobulle.="&nbsp;<br />\n";
		$texte_infobulle.="A difference between HTML and pdf report&nbsp;:<br />\n";
		$texte_infobulle.="In the case of HTML report the formula of <b>Parameters of HTML report</b> is displayed in addition to the formula below.<br />\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_formule_bas_de_page',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_formule_bas_de_page','y',100,100);\"  onmouseout=\"cacher_div('a_propos_formule_bas_de_page');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		echo "</p>\n";

		echo "<table border='0' summary='Table of formulas of bottom of page'>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<tr><td><b>".get_class_from_id($tab_id_classe[$i])."</b>: </td>";
			echo "<td><input type='text' name='rn_formule[$i]' id='rn_formule_".$i."' size='40' value=\"";
			$sql="SELECT * FROM classes WHERE id='".$tab_id_classe[$i]."';";
			$res_class_tmp=mysql_query($sql);
			if(mysql_num_rows($res_class_tmp)>0){
				$lig_class_tmp=mysql_fetch_object($res_class_tmp);
				echo $lig_class_tmp->rn_formule;
			}
			echo "\" /></td></tr>\n";
		}

	}
	echo "</table>\n";

//echo "\$chaine_coef=$chaine_coef<br />";
?>