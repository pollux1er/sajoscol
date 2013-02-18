<?php
	try
	{
		$bdd = new PDO('mysql:host=localhost;dbname=gepi03', 'root', 'sasse');
	}
	catch (Exception $e)
	{
        die('Erreur : ' . $e->getMessage());
	}	

?>
