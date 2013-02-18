<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 
<style type="text/css">
<!--
@import url("notes.css");
-->
</style>
      <?php
		require_once("../lib/initialisations.inc.php");
		$titre_page = "Choice of notes ";
		require_once("../lib/header.inc"); ?>
</head>

<body>
<form id="form1" name="formclass" method="post" action="enregistrer.php">
<input name="choixnote" type="hidden" value="1" />
<div class="message">
  <?php 
	
	if(isset($_GET['mess']))
	{
		echo  $_GET['mess'];
	}
   ?>
</div>
<table align="center">
  <tr>
    <td height="26">&nbsp;</td>
    <td>&nbsp;</td>
    <!--<td>&nbsp;</td>-->
  </tr>
  <tr>
    <td>Class</td>
    <td><?php
	require("configu.inc.php");
    $prof = isset($_SESSION['login'])?$_SESSION['login']:"melat";
	
$reponse5 = $bdd->prepare("SELECT * FROM utilisateurs INNER JOIN j_professeurs_matieres ON  j_professeurs_matieres.id_professeur = utilisateurs.login WHERE statut ='professeur' AND j_professeurs_matieres.id_professeur=:prof");
	 $reponse5->execute( array('prof' => $prof,) );
	if($donnees5 = $reponse5->fetch())
	{
		$matiere=$donnees5['id_matiere'];
	}
	$matiere=isset($_GET['matiere'])?$_GET['matiere']:$matiere;
	echo "<input name='matiere' type='hidden' value='". $matiere."' />";
	echo "<input name='prof' type='hidden' value='". $prof."' />";
     require_once("configu.inc.php");
     $reponse1 = $bdd->prepare('SELECT classes.id, classes.classe  FROM classes INNER JOIN  j_classe_matiere ON classes.id = j_classe_matiere.classe INNER JOIN utilisateurs ON utilisateurs.login = j_classe_matiere.professeur WHERE utilisateurs.login=:prof AND j_classe_matiere.matiere=:matiere');
	 //SELECT * FROM classes INNER JOIN  j_classe_matiere ON classes.id = j_classe_matiere.classe INNER JOIN utilisateurs ON utilisateurs.login = j_classe_matiere.professeur WHERE utilisateurs.login=:prof
	 $reponse1->execute(  array('prof' => $prof,	
								'matiere' => $matiere,
							 ));
   ?>
      <select name="classes" onchange=" ">
        <?php
	 while ($donnees1 = $reponse1->fetch()) {
		 
	 	 echo "<option value='".$donnees1['id'] .";".$donnees1['classe']."'>".$donnees1['classe'] ."</option>";
	}
	  ?>
      </select></td>
    <!--<td>&nbsp;</td>-->
  </tr>
  <tr>
    <td>Type of note</td>
    <td><select name="typnote" id="typnote">
      <option value="Sequence 1">Sequence 1</option>
      <option value="Sequence 2">Sequence 2</option>
      <option value="Quater 1">Quater 1 </option>
      <option value="Sequence 3">Sequence 3</option>
      <option value="Sequence 4">Sequence 4</option>
      <option value="Quater 2">Quater 2</option>
      <option value="Sequence 5">Sequence 5</option>
      <option value="Sequence 6">Sequence 6</option>
      <option value="Quater 3">Quater 3</option>
    </select></td>
    <!--<td>&nbsp;</td>-->
  </tr>
  <tr>
   <!-- <td>&nbsp;</td>-->
    <td colspan="2" align="center"><input type="submit" name="button" id="button" value="Type the notes" /></td>
    <!--<td>&nbsp;</td>-->
  </tr>
</table>
</form >


</body>
</html>