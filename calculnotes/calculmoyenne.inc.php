<?php
  require("configu.inc.php");
 
function calculscore($seq1,$seq2,$exam)
{
 return $seq1+$seq2+$exam;
}

function calculcoefscore($seq1,$seq2,$exam,$coef)
{
 return (20*$coef*($seq1+$seq2+$exam))/100;
}

function tcalculcoefscore($seq1,$seq2,$exam,$coef)
{	 
	 for ($i=1;$i<=count($seq1);$i++)
	 {
		 $t['matiere']=$seq1[$i]['matiere'];
		 $t['login']=$seq1[$i]['login'];
		 $t['note']=$seq1[$i]['note']+$seq2[$i]['note']+$exam[$i]['note'];
		 $t['notecoef']=(20*$coef*($seq1[$i]['note']+$seq2[$i]['note']+$exam[$i]['note']))/100;	 
		 $tab[$i]=$t;
		// $tab[$i]= (20*$coef*($seq1[$i]+$seq2[$i]+$exam[$i]))/100;	 
     }
	return $tab;
}


function tcalculcoefscoreeleve($seq1,$seq2,$exam)
{	 

	 $som=0;
	 $somcoef=0;
	 for ($i=1;$i<=count($seq1);$i++)
	 {
		 $t['nom']=$seq1[$i]['nom'];
		 $t['prenom']=$seq1[$i]['prenom'];
		 $t['matiere']=$seq1[$i]['matiere'];
		 $t['coefficient']=$seq1[$i]['coefficient'];
		 $t['login']=$seq1[$i]['login'];
		 $t['prenomprof']=$seq1[$i]['prenomprof'];
		 $t['nomprof']=$seq1[$i]['nomprof'];
		 $t['classe']=$seq1[$i]['classe'];
		 $t['note']=$seq1[$i]['note']+$seq2[$i]['note']+$exam[$i]['note'];
		 $t['notecoef']=(20*$seq1[$i]['coefficient']*($seq1[$i]['note']+$seq2[$i]['note']+$exam[$i]['note']))/100;	 
		 $tab[$i]=$t;
		$som +=$t['notecoef'];
		$somcoef +=$t['coefficient'];	
		// $tab[$i]= (20*$coef*($seq1[$i]+$seq2[$i]+$exam[$i]))/100;	 
     }
	 $t1['total']=$som;
	 $t1['totalcoef']=$somcoef;
	 $tab[0]=$t1;
	 
	 
	 
	return $tab;
}






function savecoefscore($tab,$trimestre)
{
	 require("configu.inc.php");
	 for ($i=1;$i<=count($tab);$i++)
	 {
		$login=$tab[$i]['login'];
		$note=$tab[$i]['note'];
		$matiere=$tab[$i]['matiere'];
	    $notecoef=$tab[$i]['notecoef'] ;
		$req = $bdd->prepare("INSERT INTO scores(matiere, login, note, trimestre,notecoef) VALUES(:matiere,:login,:note,:trimestre,:notecoef)");
		$req->execute(array(
			'matiere' => $matiere,
			'login' => $login,
			'note' => $note,
			'trimestre' => $trimestre,
			'notecoef' => $notecoef,
			));
	 }	
}
function recuperernotes($classes,$trim,$matiere)
{
  require("configu.inc.php");
  $reponse = $bdd->prepare("SELECT DISTINCT eleves.login, nom, prenom ,classes.id ,classes.classe ,note ,trimestre ,notes.matiere FROM eleves INNER  JOIN j_eleves_classes ON j_eleves_classes.login = eleves.login INNER JOIN classes ON  j_eleves_classes.id_classe = classes.id INNER JOIN notes ON  notes.login=eleves.login WHERE trimestre=:trimestre AND classes.id=:classe AND matiere=:matiere");
	 $reponse->execute( array('classe' => $classes,
							 'trimestre' => $trim,
							 'matiere' => $matiere,
							 ) );
	 $number=1;
	 while ($donnees = $reponse->fetch()) 
	 {
		$t['matiere']=$donnees['matiere'];
		$t['login']=$donnees['login'];
		$t['note']=$donnees['note'];
	    $tab[$number++]=$t;
	 }	
	 return $tab;
}


function recuperernoteleve($login,$classes,$trim)
{
 require("configu.inc.php");
 $reponse = $bdd->prepare("SELECT DISTINCT eleves.login, eleves.nom, eleves.prenom ,classes.id ,classes.classe ,note ,trimestre ,notes.matiere,j_classe_matiere.coefficient ,utilisateurs.nom AS nomprof ,utilisateurs.prenom AS prenomprof FROM eleves INNER  JOIN j_eleves_classes ON j_eleves_classes.login = eleves.login INNER JOIN classes ON  j_eleves_classes.id_classe = classes.id INNER JOIN notes ON  notes.login=eleves.login INNER JOIN j_classe_matiere ON j_classe_matiere.matiere=notes.matiere INNER JOIN utilisateurs ON j_classe_matiere.professeur=utilisateurs.login WHERE trimestre=:trimestre AND classes.id=:classe AND eleves.login=:login AND utilisateurs.statut='professeur'");
	 $reponse->execute( array('classe' => $classes,
							 'trimestre' => $trim,
							 'login' => $login,
							 ) );
	 $number=1;
	 while ($donnees = $reponse->fetch()) 
	 {
		$t['matiere']=$donnees['matiere'];
		$t['login']=$donnees['login'];
		$t['note']=$donnees['note'];
		$t['nom']=$donnees['nom'];
		$t['prenom']=$donnees['prenom'];
		$t['coefficient']=$donnees['coefficient'];
		$t['prenomprof']=$donnees['prenomprof'];
		$t['nomprof']=$donnees['nomprof'];
		$t['classe']=$donnees['classe'];
	    $tab[$number++]=$t;
	 }	
	 return $tab;
}

function recupererscorecoeffeleve($login,$classes,$trim)
{
 require("configu.inc.php");
 $reponse = $bdd->prepare("SELECT DISTINCT eleves.login, eleves.nom, eleves.prenom ,classes.id ,classes.classe ,note , notecoef, trimestre ,scores.matiere,j_classe_matiere.coefficient ,utilisateurs.nom AS nomprof ,utilisateurs.prenom AS prenomprof FROM eleves INNER  JOIN j_eleves_classes ON j_eleves_classes.login = eleves.login INNER JOIN classes ON  j_eleves_classes.id_classe = classes.id INNER JOIN scores ON  scores.login=eleves.login INNER JOIN j_classe_matiere ON j_classe_matiere.matiere=scores.matiere INNER JOIN utilisateurs ON j_classe_matiere.professeur=utilisateurs.login WHERE trimestre=:trimestre AND classes.id=:classe AND eleves.login=:login AND utilisateurs.statut='professeur'");
	 $reponse->execute( array('classe' => $classes,
							 'trimestre' => $trim,
							 'login' => $login,
							 ) );
	 $number=1;
	 $som=0;
	 $somcoef=0;
	 while ($donnees = $reponse->fetch()) 
	 {
		$t['matiere']=$donnees['matiere'];
		$t['login']=$donnees['login'];
		$t['note']=$donnees['note'];
		$t['notecoef']=$donnees['notecoef'];
        $t['nom']=$donnees['nom'];
		$t['prenom']=$donnees['prenom'];
		$t['coefficient']=$donnees['coefficient'];
		$t['prenomprof']=$donnees['prenomprof'];
		$t['nomprof']=$donnees['nomprof'];
		$t['classe']=$donnees['classe'];
	    $tab[$number++]=$t;
		$som +=$donnees['notecoef'];
		$somcoef +=$donnees['coefficient'];	
	 }	
   	 $t1['total']=$som;
	 $t1['totalcoef']=$somcoef;
	 $tab[0]=$t1;
	 return $tab;
}

function calculmoyenneclasse($classes,$trimestre,$Sequence1,$Sequence2)
{
	 require("configu.inc.php");
	$reponse = $bdd->prepare("SELECT DISTINCT eleves.login ,eleves.nom ,eleves.prenom  ,j_eleves_classes.id_classe FROM eleves INNER JOIN j_eleves_classes ON j_eleves_classes.login=eleves.login WHERE j_eleves_classes.id_classe=:classes ORDER BY eleves.nom");
	$reponse->execute( array('classes' => $classes,			 
							 ));
	$number=1;
	 while ($donnees = $reponse->fetch()) {
    	$student=$donnees['login'];
     $t1=recuperernoteleve($student,$classes,$Sequence1);
	 $t2=recuperernoteleve($student,$classes,$Sequence2);
	 $t3=recuperernoteleve($student,$classes,$trimestre);
	 $tb= tcalculcoefscoreeleve($t1,$t2,$t3);
	 $t['login']=$donnees['login'];
	 $t['moyenne']=$tb[0]['total']/$tb[0]['totalcoef'];
	// $t['total']=$tb[0]['total'];
	// $t['totalcoef']=$tb[0]['totalcoef'];
	 $tab[$number++]=$t;
    }
	 return $tab;
}

 function cmp($a, $b) {
   if ($a['moyenne'] < $b['moyenne']) {
     return -1 ;
   } elseif ($a['moyenne'] == $b['moyenne']) {
     return 0 ;
   } else {
     return 1 ;
   }
}
 function decroisscmp($a, $b) {
   if ($a['moyenne'] < $b['moyenne']) {
     return 1 ;
   } elseif ($a['moyenne'] == $b['moyenne']) {
     return 0 ;
   } else {
     return -1 ;
   }
}
function rang($tab,$login)
{
	$ran=0;
 for ($i=0;$i<count($tab);$i++)
 {
	 if($tab[$i]['login']==$login)
	 {
	  $ran= $i+1;
	  break;
	 }
 }
 
 if($ran==1)return "1st";
 if($ran==2)return "2nd";
 if($ran==3)return "3rd";
 return  $ran."th";
}

/*$tab=calculmoyenneclasse(1,"Quater 1","Sequence 1","Sequence 2");
 for ($i=1;$i<=count($tab);$i++)
 {
	echo "<br>".$i.";".$tab[$i]['login'].";".$tab[$i]['moyenne'].";" ; 
 
 }
usort($tab, 'decroisscmp');

 for ($i=0;$i<count($tab);$i++)
 {
	echo "<br>".$i.";".$tab[$i]['login'].";".$tab[$i]['moyenne'].";" ; 
 
 }*/
 



/*foreach($fruits as $cle => $valeur) {
   echo "$valeur, ";
}*/
function average($notes,$coef)
{
//notes est un tableau de notes coefficiées et $coef est un tableau contenanant tous les coefficients
 $i=0;
 $som=0;
 $somcoef=0;
	  for ($i=0;$i<=count($notes);$i++)
	  {
		  $som +=$notes[0];
		  $somcoef +=$coef[0];
	  }
	  $moyenne=$som/$somcoef;
	  return $moyenne;
}
 /*
 $t1=recuperernoteleve("achah",1,"Sequence 1");
 echo "Note of Sequence 1";
 for ($i=1;$i<=count($t1);$i++)
 {
	echo "<br>".$i.";".$t1[$i]['matiere'].";".$t1[$i]['login'].";".$t1[$i]['nom'].";".$t1[$i]['prenom'].";".$t1[$i]['note'].";".$t1[$i]['coefficient'].";".$t1[$i]['nomprof'].";".$t1[$i]['prenomprof'].";"; 
 }
  $t2=recuperernoteleve("achah",1,"Sequence 2");
 echo "<br><br>Note of Sequence 2";
 for ($i=1;$i<=count($t2);$i++)
 {
	echo "<br>".$i.";".$t2[$i]['matiere'].";".$t2[$i]['login'].";".$t2[$i]['nom'].";".$t2[$i]['prenom'].";".$t2[$i]['note'].";".$t2[$i]['coefficient'].";".$t2[$i]['nomprof'].";".$t2[$i]['prenomprof'].";"; 
 }
  $t3=recuperernoteleve("achah",1,"Quater 1");
 echo "<br><br>Note of Quater 1";
 for ($i=1;$i<=count($t3);$i++)
 {
	echo "<br>".$i.";".$t3[$i]['matiere'].";".$t3[$i]['login'].";".$t3[$i]['nom'].";".$t3[$i]['prenom'].";".$t3[$i]['note'].";".$t3[$i]['coefficient'].";".$t3[$i]['nomprof'].";".$t3[$i]['prenomprof'].";"; 
 }
  $tab=tcalculcoefscoreeleve($t1,$t2,$t3);
   echo "<br><br>Coefficied notes of Quater 1";
  for ($i=1;$i<count($tab);$i++)
 {
	echo "<br>".$i.";".$tab[$i]['matiere'].";".$tab[$i]['login'].";".$tab[$i]['nom'].";".$tab[$i]['prenom'].";".$tab[$i]['note'].";".$tab[$i]['notecoef'].";".$tab[$i]['coefficient'].";".$tab[$i]['nomprof'].";".$tab[$i]['prenomprof'].";"; 
 }

  echo "<br>Score of Quater 1";
 $tb= recupererscorecoeffeleve("achah",1,"Quater 1");
for ($i=1;$i<count($tb);$i++)
 {
	echo "<br>".$i.";".$tb[$i]['matiere'].";".$tb[$i]['login'].";".$tb[$i]['nom'].";".$tb[$i]['prenom'].";".$tb[$i]['note'].";".$tb[$i]['notecoef'].";".$tb[$i]['coefficient'].";".$tb[$i]['nomprof'].";".$tb[$i]['prenomprof'].";"; 
 }
echo "<br><br>TOTAL:".$tb[0]['total']."; Total Coef:".$tb[0]['totalcoef']; */

/*echo "The score is ".calculscore(12,12,70);
echo "The coef score is ".calculcoefscore(12,12,70,4);
$tab1=recuperernotes(1,"Sequence 1","Histoire");
$tab2=recuperernotes(1,"Sequence 2","Histoire");
$tab3=recuperernotes(1,"Quater 1","Histoire");

$tab=tcalculcoefscore($tab1,$tab2,$tab3,4);
 for ($i=1;$i<=count($tab);$i++)
 {
	echo "<br>".$i.";".$tab[$i]['matiere'].";".$tab[$i]['login'].";".$tab[$i]['note'].";".$tab[$i]['notecoef'].";"; 
 }*/
//savecoefscore($tab,"Quater 1");

?>