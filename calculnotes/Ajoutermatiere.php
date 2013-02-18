      <?php
		require_once("../lib/initialisations.inc.php");
		$titre_page = "Management of the courses";
		require_once("../lib/header.inc"); ?>
<form id="form1" name="formclass" method="post" action="enregistrer.php">
<input name="ajoutmatiere" type="hidden" value="1" />

<?php
	 
	if(isset($_GET['classes']))
	{
		 $classes=$_GET['classes'];
		 $nomclasse=$_GET['nomclass'];
		 echo  "<input name='classe01' type='hidden' value='".$_GET['classes']."' />";
		 echo  " <input name='nomclass' type='hidden' value='".$_GET['nomclass']."' />";
	}
    ?>

<table align="center">
  <tr>
    <td height="26">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="32">Class of</td>
    <td>
   <?php
     require_once("configu.inc.php");
    /* $reponse1 = $bdd->prepare('SELECT * FROM classes');
	 $reponse1->execute( );*/
	 echo  $nomclasse;
   ?>
   </td>
    <td>&nbsp;</td>
  </tr>

  <tr>
    <td>Courses</td>
    <td> 
    <select name="matiere" onchange=" ">
      <?php
	 $reponse1 = $bdd->prepare('SELECT * FROM matieres');
	 $reponse1->execute( );
	 while ($donnees1 = $reponse1->fetch()) {
		 
	 	 echo "<option value='".$donnees1['matiere'] ."'>".$donnees1['matiere'] ."</option>";
	 
	 }
	  ?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td>Coefficient</td>
    <td> <input name="coef" type="text" value="1" /></td>
    <td>&nbsp;</td>
  </tr>
    
  <!-- <tr>
    <td>Professor</td>
    <td><select name="professor">
    -->
   <?php
/*	 $reponse2 = $bdd->prepare("SELECT * FROM utilisateurs WHERE statut ='professeur'");
	 $reponse2->execute( );
	 while ($donnees2 = $reponse2->fetch()) {
		 
	 	 echo "<option value='".$donnees2['login'] ."'>".$donnees2['nom'] ." ".$donnees2['prenom']."</option>";
	 
	 }*/
	  ?>
    
    
    
  <!--    </select>   </td>
    <td>&nbsp;</td>
  </tr>-->
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="Add" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
</form >

</body>
</html>