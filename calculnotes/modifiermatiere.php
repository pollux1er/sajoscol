 
   <?php
   		require_once("../lib/initialisations.inc.php");
		$titre_page = "Modify a courses";
		require_once("../lib/header.inc");
     require_once("configu.inc.php");
   ?>
<?php
	$classes=1;
	$typnote="trim1";
	if(isset($_GET['classes']))
	{
		 $classes=$_GET['classes'];
		 $nomclasse=$_GET['nomclasse'];
		 $typnote=$_GET['typnote'];
		 $matiere=$_GET['matiere'];
		 echo  "<input name='classses' type='hidden' value='".$_GET['classes']."' />";
		 echo   "<input name='matiere' type='hidden' value='".$_GET['matiere']."'  />";
		 echo  " <input name='typnote' type='hidden' value='".$_GET['typnote']."' />";
	}
?>



<form id="form1" name="formclass2" method="post" action="enregistrer.php">
<input name="modifmatiere" type="hidden" value="1" />

<?php
	 
	if(isset($_GET['param']))
	{
		 $params=explode(';',$_GET['param']);
		 
		 $classes=$params[0];
		 $nomclass=$params[1];
		 $matiere=$params[2];
		echo " <input name='classes' type='hidden' value='$classes' />";
		echo " <input name='nomclass' type='hidden' value='$nomclass' />";
		echo " <input name='matiere' type='hidden' value='$matiere' />";
		 
		 $coef=$params[3];
		 $prof=$params[4];
	}
    ?>

<table align="center">
  <tr>
    <td height="26">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="32">Class of </td>
    <td>
<?php echo $nomclass ;?>
 </td>
    <td>&nbsp;</td>
  </tr>

  <tr>
    <td>Courses</td>
    <td><?php echo $matiere ;?></td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td>Coefficient</td>
    <td> <input name="coef" type="text" value="<?php echo $coef ;?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Professor</td>
    <td> <select name="professor">
     <?php
	 $reponse2 = $bdd->prepare("SELECT * FROM utilisateurs INNER JOIN j_professeurs_matieres ON  j_professeurs_matieres.id_professeur = utilisateurs.login WHERE statut ='professeur' AND j_professeurs_matieres.id_matiere=:matiere");
	 $reponse2->execute( array('matiere' => $matiere,			 
							 ) );
	 while ($donnees2 = $reponse2->fetch()) {
	 if($donnees2['login']==$prof ) 
	 	 echo "<option value='".$donnees2['login'] ."' selected='selected'>".$donnees2['nom'] ." ".$donnees2['prenom']."</option>";
	 else
	     echo "<option value='".$donnees2['login'] ."'>".$donnees2['nom'] ." ".$donnees2['prenom']."</option>";
	 
	 }
	  ?></select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="Update" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
</form >
</body>
</html>