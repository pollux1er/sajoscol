<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Document sans titre</title>
</head>

<body>



<?php
   require_once("configu.inc.php");
   $classes=1;
   if(isset($_GET['classes']))
 	{
		 $classes=$_GET['classes'];
		// $nomclasse=$_GET['nomclasse'];
	}  
$reponse = $bdd->prepare("SELECT DISTINCT eleves.login ,eleves.nom ,eleves.prenom  ,j_eleves_classes.id_classe FROM eleves INNER JOIN j_eleves_classes ON j_eleves_classes.login=eleves.login  WHERE j_eleves_classes.id_classe=:classes ORDER BY eleves.nom");
	$reponse->execute( array('classes' => $classes,			 
							 ));
	
//SELECT matieres.matiere,utilisateurs.login FROM j_professeurs_matieres INNER JOIN utilisateurs ON  j_professeurs_matieres.id_professeur = utilisateurs.login INNER JOIN matieres ON matieres.matiere = j_professeurs_matieres.id_matiere WHERE utilisateurs.statut='professeur'
	$number=1;
	 while ($donnees = $reponse->fetch()) {
	/* echo "<tr><td>".$number++ ." </td>  <td>".$donnees['nom']." ". $donnees['prenom']."</td><td><input name='note2[]' type='text' value='".$donnees['note'] ."'/></td><input name='login[]' type='hidden' value=".$donnees['login']." /></tr>";	 
*/	
	echo "<ul> ";
	echo "<li>".$donnees['nom']." ". $donnees['prenom']."</li>";
	echo " </ul>";
	
	
	}
	
  ?>
 
</body>
</html>