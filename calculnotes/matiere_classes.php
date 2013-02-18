
     <?php
		require_once("../lib/initialisations.inc.php");
		$titre_page = "Management of the courses";
		require_once("../lib/header.inc");
	  require_once("configu.inc.php");
	  
	 $reponse2 = $bdd->prepare("SELECT * FROM utilisateurs WHERE statut ='professeur'");
	 $reponse2->execute( );
	 while ($donnees2 = $reponse2->fetch()) {
		 $tprof[$donnees2['login']]=$donnees2['nom'] ." ".$donnees2['prenom'];
	/* if($donnees2['login']==$prof ) 
	 	 echo "<option value='".$donnees2['login'] ."' selected='selected'>".$donnees2['nom'] ." ".$donnees2['prenom']."</option>";
	 else
	     echo "<option value='".$donnees2['login'] ."'>".$donnees2['nom'] ." ".$donnees2['prenom']."</option>";*/
	 
	 }
	  //echo $tprof["melat"];
	  ?>


<a href='../classes/index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Return</a><form id="form1" name="formclass" method="post" action="enregistrer.php">
<input name="supprimermatiere" type="hidden" value="1" />
<table align="center">
  <tr>
    <td>&nbsp;</td>
    <td>Courses</td>
    <td>
   Coeff
    </td>
    <td>Professor</td>
  </tr>
       <?php
	  
	  $classe=1;
	  $nomclass="5A";
	   if(isset($_GET['classes'])){
		 $classe=$_GET['classes'];
		 $nomclass=$_GET['nomclass'];
	   }
	    echo " <input name='classes' type='hidden' value='$classe' />";
		echo " <input name='nomclass' type='hidden' value='$nomclass' />";
	 $reponse1 = $bdd->prepare("SELECT * FROM classes INNER JOIN j_classe_matiere ON classes.id=j_classe_matiere.classe INNER JOIN matieres ON matieres.matiere =j_classe_matiere.matiere WHERE j_classe_matiere.classe=:classe ");
	 $reponse1->execute( array('classe' => $classe,					 
							 ));
	 
	 //SELECT * FROM classes INNER JOIN j_classe_matiere ON classes.id=j_classe_matiere.classe INNER JOIN matieres ON matieres.matiere =j_classe_matiere.matiere INNER JOIN utilisateurs ON utilisateurs.login=j_classe_matiere.professeur WHERE j_classe_matiere.classe=1 AND utilisateurs.statut ='professeur'
	 while ($donnees1 = $reponse1->fetch()) {
		 $prof="";
		 if(isset($donnees1['professeur']))
		 {
			  $prof = $donnees1['professeur'];
			  
		 }
	echo "<tr><td><input name='supp[]' type='checkbox' value='".$donnees1['matiere']."' /></td><td> <a href='modifiermatiere.php?param=".$classe.";".$nomclass.";".$donnees1['matiere'].";".$donnees1['coefficient'].";".$prof.";' >".$donnees1['matiere'] ."</a></td><td>".$donnees1['coefficient'] ."</td><td>";
	
	if(isset($donnees1['professeur']))
		echo $tprof[$donnees1['professeur']];//echo $donnees1['nom']." ".$donnees1['prenom'];
	else
		echo "" ;
	
	echo "</td> </tr>";
	 }
	  ?>
      
    

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td> 
   
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><!--<input type="submit" name="button" id="button" value="Type the notes" />--></td>
    <td>&nbsp;</td>
  </tr>
</table>
<p id="ajout"><a href="Ajoutermatiere.php?classes=<?php echo $classe ;?>&nomclass=<?php echo $nomclass ;?>" title="Add a course">Add a course to this class</a></p>
<p id="ajout"><input name="remove" type="submit" value="Remove selected courses" /></p>
</form >






</body>
</html>