<?php
/*
 * @version: $Id: admin_ct.php 5940 2010-11-21 20:23:57Z crob $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}
// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Pour garder une trace du retour
$_SESSION["retour"] = 'admin_ct';

// Suppression d'un ou plusieurs cahiers de texte
if (isset($_POST['sup_ct'])) {
	check_token();

  //$sql="SELECT DISTINCT id_groupe, id_login FROM ct_entry ORDER BY id_groupe;";
  $sql="SELECT DISTINCT id_groupe FROM ct_entry ORDER BY id_groupe;";
  //echo "$sql<br />\n";
  $query = sql_query($sql);
  $msg = '';
  for ($i=0; ($row=sql_row($query,$i)); $i++) {
      $id_groupe = $row[0];
      //$id_prop = $row[1];
      //$temp = "sup".$id_groupe."_".$id_prop;
      $temp = "sup".$id_groupe;
      //echo "\$temp=$temp<br />\n";
      if (isset($_POST[$temp])) {
         $id_prop=$_POST[$temp];

         $error = 'no';
         $sql="SELECT id_ct  FROM ct_entry WHERE (id_groupe='".$id_groupe."' and id_login = '".$id_prop."');";
         //echo "$sql<br />\n";
         $appel_ct=sql_query($sql);
         if (($appel_ct) and (sql_count($appel_ct)!=0)) {
           for ($k=0; ($row2 = sql_row($appel_ct,$k)); $k++) {
             $id_ctexte = $row2[0];
             $sql="select emplacement from ct_documents where id_ct='".$id_ctexte."';";
             //echo "$sql<br />\n";
             $appel_doc = sql_query($sql);
             for ($j=0; ($row3 = sql_row($appel_doc,$j)); $j++) {
                $empl = $row3[0];
                if ($empl != -1) $del = @unlink($empl);
             }
             $sql="delete from ct_documents where id_ct='".$id_ctexte."';";
             //echo "$sql<br />\n";
             $del_doc = sql_query($sql);
             if (!($del_doc)) $error = 'yes';
             $sql="delete from ct_entry where id_ct = '".$id_ctexte."';";
             //echo "$sql<br />\n";
             $del_ct = sql_query($sql);
             if (!($del_ct)) $error = 'yes';
           }
           if ($error == 'no') {
              $msg .= "Removal of the notices in ct_entry succeeded for $id_prop on the group n°$id_groupe.<br />";
           } else {
              $msg .= "There was a problem during the removal of the notices in ct_entry for $id_prop on the group n°$id_groupe.<br />";
           }
         } else {
           $msg .= "No notice to remove in ct_entry for $id_prop on the group n°$id_groupe.<br />";
         }
      }
   }

  //$sql="SELECT DISTINCT id_groupe, id_login FROM ct_devoirs_entry ORDER BY id_groupe;";
  $sql="SELECT DISTINCT id_groupe FROM ct_devoirs_entry ORDER BY id_groupe;";
  //echo "$sql<br />\n";
  $query = sql_query($sql);
  for ($i=0; ($row=sql_row($query,$i)); $i++) {
      $id_groupe = $row[0];
      //$id_prop = $row[1];
      //$temp = "sup".$id_groupe."_".$id_prop;
      $temp = "sup".$id_groupe;
      if (isset($_POST[$temp])) {
         $id_prop=$_POST[$temp];

         $error = 'no';
         $sql="SELECT id_ct  FROM ct_devoirs_entry WHERE (id_groupe='".$id_groupe."' and id_login = '".$id_prop."');";
         //echo "$sql<br />\n";
         $appel_ct_devoirs_entry=sql_query($sql);
         if (($appel_ct_devoirs_entry) and (sql_count($appel_ct_devoirs_entry)!=0)) {
           $del_ct_devoirs = sql_query("delete  FROM ct_devoirs_entry WHERE (id_groupe='".$id_groupe."' and id_login = '".$id_prop."')");
           if (!($del_ct_devoirs)) {$error = 'yes';}

           if ($error == 'no') {
             $msg .= "Removal of notices in ct_devoirs_entry succeeded for $id_prop on the group n°$id_groupe.<br />";
           } else {
             $msg .= "There was a problem during the removal of the notices in ct_devoirs_entry for $id_prop on the group n°$id_groupe.<br />";
           }
         } else {
              $msg .= "No notice to remove in ct_devoirs_entry for $id_prop on the group n°$id_groupe.<br />";
         }
      }
  }

  //$sql="SELECT DISTINCT id_groupe, id_login FROM ct_private_entry ORDER BY id_groupe;";
  $sql="SELECT DISTINCT id_groupe FROM ct_private_entry ORDER BY id_groupe;";
  $query=sql_query($sql);
  for ($i=0; ($row=sql_row($query,$i)); $i++) {
      $id_groupe = $row[0];
      //$id_prop = $row[1];
      //$temp = "sup".$id_groupe."_".$id_prop;
      $temp = "sup".$id_groupe;
      if (isset($_POST[$temp])) {
         $id_prop=$_POST[$temp];

         $error = 'no';
         $sql="SELECT id_ct  FROM ct_private_entry WHERE (id_groupe='".$id_groupe."' and id_login = '".$id_prop."');";
         //echo "$sql<br />\n";
         $appel_ct_private_entry=sql_query($sql);
         if (($appel_ct_private_entry) and (sql_count($appel_ct_private_entry)!=0)) {
           $del_ct_devoirs = sql_query("delete  FROM ct_private_entry WHERE (id_groupe='".$id_groupe."' and id_login = '".$id_prop."')");
           if (!($del_ct_devoirs)) $error = 'yes';
           if ($error == 'no') {
               $msg .= "Removal of the notices in ct_private_entry succeeded for $id_prop on the group n°$id_groupe.<br />";
           } else {
               $msg .= "There was a problem during the removal of the notices in ct_devoirs_entry for $id_prop on the group n°$id_groupe.<br />";
           }
         } else {
           $msg .= "No notice to remove in ct_private_entry for $id_prop on the group n°$id_groupe.<br />";
         }
      }
   }
}

// Modification d'un cahier de texte - Etape 2
if (isset($_POST['action'])) {
	check_token();
  $id_groupe = $_POST['id_groupe'];
  $id_prop = $_POST['id_prop'];

  if ($_POST['action'] == 'change_groupe') {
  	 $id_former_group = $_POST['id_former_group'];
     $sql1 = sql_query("UPDATE ct_entry SET id_groupe='".$id_groupe."' WHERE (id_groupe='".$id_former_group."' and id_login='".$id_prop."')");
     $sql2 = sql_query("UPDATE ct_devoirs_entry SET id_groupe='".$id_groupe."' WHERE (id_groupe='".$id_former_group."' and id_login='".$id_prop."')");
     if (($sql1) and ($sql2)) {
        $msg = "The change of group was done.";
     } else {
        $msg = "There was a problem during the change of group.";
     }
  }

  if ($_POST['action'] == 'change_prop') {
     $sql1 = sql_query("UPDATE ct_entry SET id_login='".$id_prop."' WHERE (id_groupe='".$id_groupe."')");
     $sql2 = sql_query("UPDATE ct_entry SET id_login='".$id_prop."' WHERE (id_groupe='".$id_groupe."')");
     if (($sql1) and ($sql2)) {
        $msg = "The change of owner was carried out.";
     } else {
        $msg = "There was a problem during the change of owner.";
     }
  }


}

//===================================================
// header
$titre_page = "Administration of the textbooks";
require_once("../lib/header.inc");
//===================================================

//debug_var();

// Modification d'un cahier de texte - Etape 1
if (isset($_GET['action'])) {
	check_token(false);
  echo "<p class='bold'><a href=\"admin_ct.php\"><img src='../images/icons/back.png' alt='Return' class='back_link'/> Return</a></p>\n";
  $id_groupe = $_GET['id_groupe'];
  $id_prop = $_GET['id_prop'];
  $classes = null;
  $nom_groupe = sql_query1("select name from groupes where id = '".$id_groupe."'");
  if ($nom_groupe == "-1") {
     $nom_groupe = "<font color='red'>".$id_groupe." : non-existent group</font>\n";
  } else {
  	  $get_classes = mysql_query("SELECT c.classe FROM classes c, j_groupes_classes jc WHERE (c.id = jc.id_classe and jc.id_groupe = '" . $id_groupe . "')");
      $nb_classes = mysql_num_rows($get_classes);
      for ($c=0;$c<$nb_classes;$c++) {
      	$current_classe = mysql_result($get_classes, $c, "classe");
      	$classes .= $current_classe;
      	if ($c+1<$nb_classes) $classes .= ", ";
      }
  }
  $sql_prof = sql_query("select nom, prenom from utilisateurs where login = '".$id_prop."'");
  if (!($sql_prof)) {
     $nom_prof = "<font color='red'>".$id_prop." : non-existent user</font>\n";
  } else {
     $row_prof=sql_row($sql_prof,0);
     $nom_prof = $row_prof[1]." ".$row_prof[0];
         $test_groupe_prof = sql_query("select login from j_groupes_professeurs WHERE (id_groupe='".$id_groupe."' and login = '".$id_prop."')");
         if (sql_count($test_groupe_prof) == 0) $nom_prof = "<font color='red'>".$nom_prof." : <br />This professor does not teach in this group</font>\n";
  }

  if ($_GET['action'] == 'modif_groupe') {
     echo "<form action=\"admin_ct.php\" name=\"formulaire2\" method=\"post\">\n";
	echo add_token_field();
     echo "<H2>Textbook - Modification of the group</h2>\n";
     echo "<p>Current group : <b>".$nom_groupe."</b><br />\n";
     echo "In the class of : <b>".$classes."</b><br />\n";
     echo "Current owner : <b>".$nom_prof."</b></p>\n";
     echo "<p>You can allot to this textbook a new group.</p>\n";
     echo "<p>Choose the new class : </p>\n";

     $sql_groupe = sql_query("select g.id, g.name from groupes g, classes c, j_groupes_classes jc " .
     		"WHERE (".
     		"c.id = jc.id_classe and ".
			"jc.id_groupe = g.id) " .
			"order by c.classe");


     echo "<select name=\"id_groupe\" size=\"1\">\n";
     for ($i=0; ($row=sql_row($sql_groupe,$i)); $i++) {
        $new_id_groupe = $row[0];
        $nom_groupe = $row[1];
        $classes = null;
        $get_classes = mysql_query("SELECT c.classe FROM classes c, j_groupes_classes jc WHERE (c.id = jc.id_classe and jc.id_groupe = '" . $new_id_groupe . "')");
	    $nb_classes = mysql_num_rows($get_classes);
	      for ($c=0;$c<$nb_classes;$c++) {
	      	$current_classe = mysql_result($get_classes, $c, "classe");
	      	$classes .= $current_classe;
	      	if ($c+1<$nb_classes) $classes .= ", ";
	      }
        echo "<option value=\"".$new_id_groupe."\">".$classes." | " . $nom_groupe ."</option>\n";
     }
     echo "</select>\n";
     echo "<input type=\"hidden\" name=\"id_prop\" value=\"".$id_prop."\" />\n";
     echo "<input type=\"hidden\" name=\"id_former_group\" value=\"".$id_groupe."\" />\n";
     echo "<input type=\"hidden\" name=\"action\" value=\"change_groupe\" />\n";
     echo "<br /><input type=\"submit\" value=\"Save\" />\n";
     echo "</form>\n";

  }

  if ($_GET['action'] == 'modif_prop') {
	check_token(false);

     echo "<form action=\"admin_ct.php\" name=\"formulaire2\" method=\"post\">\n";
	echo add_token_field();
     echo "<H2>Textbook - Modification of the owner</h2>\n";
     echo "<p>Current group: <b>".$nom_groupe."</b><br />\n";
     echo "Class of : <b>".$classes."</b><br />\n";
     echo "Current owner : <b>".$nom_prof."</b></p>\n";
     echo "<p>You can allot to this textbook a new owner.</p>\n";
     echo "<p>Choose the new owner: </p>\n";
     $sql_matiere = sql_query("select DISTINCT u.login, u.nom, u.prenom from utilisateurs u, matieres m, j_groupes_professeurs j where " .
     		"(u.login=j.login and " .
     		"j.id_groupe='".$id_groupe."'" .
			") order by 'u.nom, u.prenom'");
     echo "<select name=\"id_prop\" size=\"1\">\n";
     for ($i=0; ($row=sql_row($sql_matiere,$i)); $i++) {
        $id_prop = $row[0];
        $nom_prop = $row[1];
        $prenom_prop = $row[2];
        echo "<option value=\"".$id_prop."\">".$nom_prop." ".$prenom_prop."</option>\n";
     }
     echo "</select>\n";
     echo "<input type=\"hidden\" name=\"id_groupe\" value=\"".$id_groupe."\" />\n";
     echo "<input type=\"hidden\" name=\"action\" value=\"change_prop\" />\n";
     echo "<br /><input type=\"submit\" value=\"Save\" />\n";
     echo "</form>\n";

  }
}

if (!(isset($_GET['action']))) {
  // Affichage du tableau complet
  ?>
  <p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
  <H2>Administration of the textbooks</h2>
  <p>The table below currently present the whole of the textbooks on line. The problems are announced in red.
  <br />You can modify the group or the owner of a textbook by clicking on the corresponding link.
  <br />You can also remove definitively a textbook (joined notices and documents).</p>


  <form action="admin_ct.php" name="formulaire1" method="post">
<?php
	echo add_token_field();
?>
  <table border="1" class='boireaus' summary='Administration des CDT'><tr valign='center' align='center'>
  <th><b><a href='admin_ct.php?order_by=jc.id_classe,jm.id_matiere'>Class(es)</a></b></th>
  <th><b><a href='admin_ct.php?order_by=jm.id_matiere,jc.id_classe'>Group</a></b></th>
  <th><b><a href='admin_ct.php?order_by=ct.id_login,jc.id_classe,jm.id_matiere'>Owner</a></b></th>
  <th><b>Numbers<br />of notices</b></th>
  <th><b>Numbers<br />of notices<br />"exams"</b></th>
  <th>
  <b>Action</b></th><th><b><input type="submit" name="sup_ct" value="Suppression" onclick="return confirmlink(this, 'The removal of a textbook is final. The notices as well as the joined documents will be removed. Are you sure you want to continue ?', 'Confirmation of the suppression')" /></b><br />
  <a href="javascript:CocheCase(true)"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href="javascript:CocheCase(false)"><img src='../images/disabled.png' width='15' height='15' alt='Uncheck all' title='Uncheck all' /></a>
  </th></tr>

  <?php
  /*
  if (!isset($_GET['order_by'])) {
     $order_by = "jc.id_classe,jm.id_matiere";
  } else {
     $order_by = $_GET['order_by'];
  }
  */
  $order_by=isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST['order_by'] : "jc.id_classe,jm.id_matiere");

  $cpt=0;
  $alt=1;
  $query = sql_query("SELECT DISTINCT ct.id_groupe, ct.id_login FROM ct_entry ct, j_groupes_classes jc, j_groupes_matieres jm WHERE (jc.id_groupe = ct.id_groupe AND jm.id_groupe = ct.id_groupe) ORDER BY ".$order_by);
  for ($i=0; ($row=sql_row($query,$i)); $i++) {
      $id_groupe = $row[0];
      $id_prop = $row[1];
      $nom_groupe = sql_query1("select name from groupes where id = '".$id_groupe."'");
      $nom_matiere = sql_query1("select m.nom_complet from matieres m, j_groupes_matieres jm where (jm.id_groupe = '".$id_groupe."' AND m.matiere = jm.id_matiere)");
      $get_classes = mysql_query("SELECT c.classe FROM classes c, j_groupes_classes jc WHERE (c.id = jc.id_classe and jc.id_groupe = '" . $id_groupe . "')");
      $nb_classes = mysql_num_rows($get_classes);
      $classes = null;
      for ($c=0;$c<$nb_classes;$c++) {
      	$current_classe = mysql_result($get_classes, $c, "classe");
      	$classes .= $current_classe;
      	if ($c+1<$nb_classes) $classes .= ", ";
      }

      if ($nom_groupe == "-1") $nom_groupe = "<font color='red'>Non-existent group</font>\n";
      $sql_prof = sql_query("select nom, prenom from utilisateurs where login = '".$id_prop."'");
      if (!($sql_prof)) {
         $nom_prof = "<font color='red'>".$id_prop." : non-existent user</font>\n";
      } else {
         $row_prof=sql_row($sql_prof,0);
         $nom_prof = $row_prof[1]." ".$row_prof[0];
         $test_groupe_prof = sql_query("select login from j_groupes_professeurs WHERE (id_groupe='".$id_groupe."' and login = '".$id_prop."')");
         if (sql_count($test_groupe_prof) == 0) $nom_prof = "<font color='red'>".$nom_prof." : <br />This professor does not teach in this group</font>\n";
      }
      // Nombre de notices de chaque utilisateurs
      $nb_ct = sql_count(sql_query("select 1=1 FROM ct_entry WHERE (id_groupe='".$id_groupe."' and id_login='".$id_prop."') "));

      // Nombre de notices devoirs de haque utilisateurs
      $nb_ct_devoirs = sql_count(sql_query("select 1=1 FROM ct_devoirs_entry WHERE (id_groupe='".$id_groupe."' and id_login='".$id_prop."') "));

      // Affichage des lignes
      $alt=$alt*(-1);
      echo "<tr class='lig$alt white_hover'><td>".$classes."</td>\n";
      echo "<td><a href='admin_ct.php?id_groupe=".$id_groupe."&id_prop=".$id_prop."&action=modif_groupe".add_token_in_url()."' title='modify the course'>".$nom_groupe."</a></td>\n";
      echo "<td><a href='admin_ct.php?id_groupe=".$id_groupe."&id_prop=".$id_prop."&action=modif_prop".add_token_in_url()."' title='modify the owner'>".$nom_prof."</a></td>\n";
      echo "<td>".$nb_ct."</td>\n";
      echo "<td>".$nb_ct_devoirs."</td>\n";
      //echo "<td><a href='../public/index.php?id_groupe=".$id_groupe."' target='_blank'>Voir</a></td>\n";
      echo "<td><a href='../cahier_texte/see_all.php?id_groupe=".$id_groupe."' target='_blank'>See</a></td>\n";
      //echo "<td><center><input type=\"checkbox\" name=\"sup".$id_groupe."_".$id_prop."\" /></center></td>\n";
      echo "<td><center><input type=\"checkbox\" id='sup$cpt' name=\"sup".$id_groupe."\" value=\"$id_prop\" /></center></td>\n";
      echo "</tr>\n";
      $cpt++;
  }
  echo "</table>\n";
  echo "<input type='hidden' name='order_by' value='$order_by' />\n";
  echo "</form>\n";

  echo "<script type='text/javascript' language='javascript'>
function CocheCase(boul) {
 for(i=0;i<$cpt;i++) {
   if (document.getElementById('sup'+i)) {
      document.getElementById('sup'+i).checked = boul ;
   }
 }
}
</script>
";
  echo "<p><br /></p>\n";

}
require ("../lib/footer.inc.php");
?>