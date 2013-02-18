<?php
require_once("configu.inc.php");
if(isset($_POST['note']))
{
	$tabnote= $_POST['note'];
	$tablogin= $_POST['login'];
		 
	$matiere=$_POST['matiere'];
	$trimestre=$_POST['typnote'] ;
	//$text="";
	 for ($i=0;$i<count($tabnote);$i++)
	 {
		$login=$tablogin[$i];
		$note=$tabnote[$i];
		$req = $bdd->prepare("INSERT INTO notes(matiere, login, note, trimestre) VALUES(:matiere,:login,:note,:trimestre)");
		$req->execute(array(
			'matiere' => $matiere,
			'login' => $login,
			'note' => $note,
			'trimestre' => $trimestre,
			));
		//$text.=$login.";".$note.";".$matiere.";".$trimestre."\n";
	 }
	 //echo $text;
	 header("location:Choixnotes.php?mess=Notes where recorded");
}
else if(isset($_POST['note2']))
{
	$tabnote= $_POST['note2'];
	$tablogin= $_POST['login'];
		 
	$matiere= isset($_POST['matiere'])?$_POST['matiere']:"math";
	$trimestre=isset($_POST['typnote'])?$_POST['typnote']:"trim1" ;
	
	 for ($i=0;$i<count($tabnote);$i++)
	 {
		$login=$tablogin[$i];
		$note=$tabnote[$i];
 
		$req = $bdd->prepare('UPDATE notes SET note=:note WHERE ((login=:login) AND (matiere=:matiere) AND( trimestre=:trimestre))');

		$req->execute(array(
			'matiere' => $matiere,
			'login' => $login,
			'note' => $note,
			'trimestre' => $trimestre,
			));
	 }
	 
	 header("location:Choixnotes.php?mess=Modidication where recorded");

}
else if(isset($_POST['choixnote']))
{
	$classes=explode(';',$_POST['classes']);
	
	// $classes= $_POST['classes'];
	 $typnote=$_POST['typnote'] ;
	 $matiere=$_POST['matiere'];	 	 
$reponse = $bdd->prepare('SELECT DISTINCT eleves.login ,eleves.nom ,eleves.prenom,notes.note ,j_eleves_classes.id_classe FROM eleves INNER JOIN notes on eleves.login=notes.login INNER JOIN j_eleves_classes ON j_eleves_classes.login=eleves.login WHERE j_eleves_classes.id_classe=:classes AND trimestre=:typnote AND matiere=:matiere ORDER BY eleves.nom');
	$reponse->execute( array('classes' => $classes[0],
							 'typnote' => $typnote,
							 'matiere' => $matiere,
							 )); 
	 
	$donnees = $reponse->fetchAll();
	$nombre = count($donnees);
	 if($nombre<>0){
		 header("location:modifiernotes.php?classes=$classes[0]&typnote=$typnote&matiere=$matiere&nombre=$nombre&nomclasse=$classes[1]");
		 }
	 else{
		 header("location:saisienotes.php?classes=$classes[0]&typnote=$typnote&matiere=$matiere&nombre=$nombre&nomclasse=$classes[1]");
	 }
	 
	 
}
else if(isset($_POST['ajoutmatiere']))
{
	
	    $classe=$_POST['classe01'];
	    $matiere=$_POST['matiere'];
		$nomclass=$_POST['nomclass'];
	    $coef=$_POST['coef'];
		$req = $bdd->prepare('INSERT INTO j_classe_matiere(classe, matiere, coefficient) VALUES( :classe,:matiere,:coef)');
		$req->execute(array(
			'classe' => $classe,
			'matiere' => $matiere,
			'coef' => $coef,
			));	
		 header("location:matiere_classes.php?classes=$classe&nomclass=$nomclass");
}
else if(isset($_POST['modifmatiere']))
{
	    
		$classes=$_POST['classes'];
		$matiere=$_POST['matiere'];
		$coef=$_POST['coef'];
		$professor=$_POST['professor'];
		$nomclass=$_POST['nomclass'];
	
		$req = $bdd->prepare('UPDATE j_classe_matiere SET coefficient=:coef,professeur=:prof WHERE ((classe=:classe) AND (matiere=:matiere))');

		$req->execute(array(
			'matiere' => $matiere,
			'classe' => $classes,
			'coef' => $coef,
			'prof' => $professor,
			));
	 header("location:matiere_classes.php?classes=$classes&nomclass=$nomclass");
}
else if(isset($_POST['supprimermatiere']))
{
	$tabmatiere= $_POST['supp'];	
	$classes=$_POST['classes'];
    $nomclass=$_POST['nomclass'];
	
	
	 for ($i=0;$i<count($tabmatiere);$i++)
	 {
		$matiere=$tabmatiere[$i];
		$req = $bdd->prepare('DELETE FROM j_classe_matiere WHERE matiere=:matiere AND classe=:classe');
		$req->execute(array(
			'matiere' => $matiere,
			'classe' => $classes,
			));
	 }	
	  header("location:matiere_classes.php?classes=$classes&nomclass=$nomclass");
}
?>