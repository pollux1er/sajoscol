<?php
/*
* $Id: choix_nettoyage.php 7744 2011-08-14 13:07:15Z dblanqui $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};

?>
[onload;file=menu.php]
<p>This section makes it possible to clean the table of correspondence;
Several options are possible but any cleaning is irreversible</p>
<p class="title-page">Caution, once their correspondence cleaned the users will not be able
to be connected any more in SSO with this module</p>
<form action="index.php?ctrl=nettoyage&action=choix" enctype='multipart/form-data' method="post">
<p>
	<input type="radio" name="choix" value="vidage_complet" checked="checked" />To empty the table of correspondences completely<br/>
	<input type="radio" name="choix" value="anciens_comptes" />Remove table accounts not existing more in Gépi<br/>
	<input type="radio" name="choix" value="profil" />Remove the correspondences for a profile (teacher, student,tutor....) <br/>
    <input type="radio" name="choix" value="classe" />Remove the correspondences for a class<br/>
</p>
<input type='submit' value='Vider la table' />
</form>
</body>
 </html>