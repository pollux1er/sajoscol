 <?php

 Class Eleve
 {
  private $Nom;
  private $Prenom;
  private $Id_classe
  private $Classe;
 }
 Class Classe
 {
  private $Id_classe
  private $Nom;
  private $Matiere;//Tableau de matière de la classe :Objets de type Matiere
 
 }
 
  Class Matiere
 {
  private $Id_matiere
  private $Nom;
  private $coef;
 
	 function __construct($Id,$nom,$coef)
	 {
		this->Id_matiere=$Id;
		this->Nom=$nom;
		this->coef=$coef;
	 }
 }
 Class note
 {
  private $Id_matiere
  private $Nommatiere;
  private $coef;
  private $notecoef;
  private $note;
 }
 
 Select from notes 
 
 
 
?>