 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 
<title>Modification of Students notes</title>
<style type="text/css">
<!--
@import url("notes.css");
-->
</style>
      <?php
		require_once("../lib/initialisations.inc.php");
		$titre_page = "Modify a note";
		require_once("../lib/header.inc"); ?>
</head>

<body>
<p>
  <?php
	require_once("configu.inc.php");
	?>
<?php
	//$classes=1;
	//$typnote="trim1";
	if(isset($_GET['classes']))
	{
		 $classes=$_GET['classes'];
		 $nomclasse=$_GET['nomclasse'];
		 $typnote=$_GET['typnote'];
		 $matiere=$_GET['matiere'];
	}
    ?>
<a href="Choixnotes.php">Return to choose another Class ,periode or Course</a></p>
<p>Class of :<?php echo $nomclasse;?></p> 
<p> Note of :<?php echo $typnote;?></p>
<p>Course : <?php echo $_GET['matiere'];?> </p>
<p>List of students</p>

<form id="notes" name="notes" method="post" action="enregistrer.php">
<input name="modifnote" type="hidden" value="1" />
<?php
	 
	if(isset($_GET['classes']))
	{ 
		 echo  "<input name='classses' type='hidden' value='".$_GET['classes']."' />";
		 echo   "<input name='matiere' type='hidden' value='".$_GET['matiere']."'  />";
		 echo  " <input name='nomclasse' type='hidden' value='".$_GET['nomclasse']."' />";
		 echo  " <input name='typnote' type='hidden' value='".$_GET['typnote']."' />";
	}
    ?>
<table  align="center">
  <tr>
    <th scope="col">Number</th>
    <th scope="col">Name </th>
    <th scope="col">Notes</th>
  </tr>
  
   <?php
   
   	$reponse = $bdd->prepare("SELECT DISTINCT eleves.login ,eleves.nom ,eleves.prenom,notes.note ,j_eleves_classes.id_classe FROM eleves INNER JOIN notes on eleves.login=notes.login INNER JOIN j_eleves_classes ON j_eleves_classes.login=eleves.login WHERE j_eleves_classes.id_classe=:classes AND trimestre=:typnote AND matiere=:matiere ORDER BY eleves.nom");
	$reponse->execute( array('classes' => $classes,
							 'typnote' => $typnote,
							 'matiere' => $matiere,							 
							 ));
	//$donnees = $reponse->fetch();
	$number=1;
	 while ($donnees = $reponse->fetch()) {
	 echo "<tr><td>".$number++ ." </td>  <td>".$donnees['nom']." ". $donnees['prenom']."</td><td><input name='note2[]' type='text' value='".$donnees['note'] ."'/></td><input name='login[]' type='hidden' value=".$donnees['login']." /></tr>";	 
	 }
	
  ?>
  <tr>
    
    <td colspan="3" align="center"><input type="submit" name="button" id="button" value="Validate" /></td>
    
   
  </tr>
</table>

</form >
</body>
</html>