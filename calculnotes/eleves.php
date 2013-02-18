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
		$titre_page = "Report cards";
		require_once("../lib/header.inc"); ?>
</head>
      
<body>

<div class="gauche" id="gauche">

<?php
   require("configu.inc.php");
   $classes=1;
	$student="achah";
	$trimestre="Quater 1";
	$Sequence1="Sequence 1";	
	$Sequence2="Sequence 2";
	if(isset($_GET['trimestre'])){
		$trimestre=$_GET['trimestre'];
		if($trimestre=="Quater 1")
		{
			$Sequence1="Sequence 1";	
			$Sequence2="Sequence 2";
		}
		if($trimestre=="Quater 2")
		{
			$Sequence1="Sequence 3";	
			$Sequence2="Sequence 4";
		}
		if($trimestre=="Quater 3")
		{
			$Sequence1="Sequence 5";	
			$Sequence2="Sequence 6";
		}
		
	}
   if(isset($_GET['classes']))
 	{
		 $classes=$_GET['classes'];
	}  
$reponse = $bdd->prepare("SELECT DISTINCT eleves.login ,eleves.nom ,eleves.prenom  ,j_eleves_classes.id_classe FROM eleves INNER JOIN j_eleves_classes ON j_eleves_classes.login=eleves.login WHERE j_eleves_classes.id_classe=:classes ORDER BY eleves.nom");
	$reponse->execute( array('classes' => $classes,			 
							 ));
	$number=1;
	echo "<ul> ";
	 while ($donnees = $reponse->fetch()) {
	
	$login=$donnees['login'];
	echo "<li><a href='eleves.php?student=$login&trimestre=Quater 1' >".$donnees['nom']." ". $donnees['prenom']."</a></li>";
	}
	echo " </ul>";
  ?>

</div>
<div class="contenu" id="contenu">
 
  <?php
	require("configu.inc.php");
	require("calculmoyenne.inc.php");
	?>
        <?php
		$classes=1;
	if(isset($_GET['classes']))
	{
		$classes=$_GET['classes'];
		$nomclasse=$_GET['nomclasse'];
	}	
		if(isset($_GET['student'])){$student=$_GET['student'];}
		if(isset($_GET['Sequence1'])){$Sequence1=$_GET['Sequence1'];}
		if(isset($_GET['Sequence2'])){$Sequence2=$_GET['Sequence2'];}
		if(isset($_GET['trimestre'])){$trimestre=$_GET['trimestre'];}
    ?>
<form id="notes" name="notes" method="post" action="enregistrer.php">
<input name="saisienote" type="hidden" value="1" />
  <?php
     $t1=recuperernoteleve($student,$classes,$Sequence1);
	 $t2=recuperernoteleve($student,$classes,$Sequence2);
	 $t3=recuperernoteleve($student,$classes,$trimestre);
   //$tb= recupererscorecoeffeleve("ajong",1,"Quater 1");
	 $tb= tcalculcoefscoreeleve($t1,$t2,$t3);	 
	 $tab=calculmoyenneclasse($classes,$trimestre,$Sequence1,$Sequence2);
	 usort($tab, 'decroisscmp');
	 $rang = rang($tab,$student);
	?>
	 Class:<?php echo $tb[1]['classe']."<br>";?> 
     Name:<?php echo $tb[1]['nom']." ".$tb[1]['prenom']."<br>";?>
<table  align="center">
  <tr>
    <th scope="col" rowspan="2">Subject</th>
    <th scope="col" colspan="3">Marks</th>
    <th scope="col" rowspan="2">Score</th>
    <th scope="col" rowspan="2">Coefficient</th>
    <th scope="col" rowspan="2">Coefficied Score</th>
    <th scope="col" rowspan="2">Position</th>
    <th scope="col" rowspan="2">Remarks</th>
  </tr>
  <tr>
    <td ><?php echo $Sequence1;?></td>
    <td ><?php echo $Sequence2;?></td>
    <td >Exam </td>
    
  </tr>  
<?php
   
for ($i=1;$i<count($tb);$i++)
 {
 echo "<tr>".$i."</td><td>".$tb[$i]['matiere']."<br>".$tb[$i]['prenomprof']." ".$tb[$i]['nomprof']."</td>
 <td>".$t1[$i]['note']."</td>
 <td>".$t2[$i]['note']."</td>
 <td>".$t3[$i]['note']."</td>
 <td>".$tb[$i]['note']."</td>
 <td>".$tb[$i]['coefficient']."</td>
 <td>".$tb[$i]['notecoef']."</td>
 <td>".""."</td>
 <td>".""."</td>
 </tr>"; 
 
 }
  //echo "<br><br>TOTAL:".$tb[0]['total']."; Total Coef:".$tb[0]['totalcoef'];	
  ?>
   <tr>
     <td class="noborder" > </td>
     <td class="noborder"></td>
     <td class="noborder"></td>
     <td class="noborder"></td>
     <td class="noborder"></td>
       <td ><?php echo "Total : ".$tb[0]['totalcoef'];?></td>
       <td><?php echo "Total : ".$tb[0]['total'];?> </td>
  </tr>
  <tr>
  	<td colspan="9" align="center" class="noborder">Average :<?php echo $tb[0]['total']/$tb[0]['totalcoef'];?>
    RANK:<?php echo $rang ;?>  </td>
  </tr>
</table>
 </div>
</body>
</html>