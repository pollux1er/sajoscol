<?php
/*
* $Id: cvsent.php 7744 2011-08-14 13:07:15Z dblanqui $
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
<p>You will set up the correspondences between the logins of Gépi and
those of your ENT according to the names and the first names,
controlez in the assistance the constraints on this file :</p>
<form action="index.php?ctrl=cvsent&action=result" enctype='multipart/form-data' method="post">
<p>
	<input type="radio" name="choix" value="erreur" checked="checked" />Seek errors: only the errors are posted, no data is not written in the
base<br/>
	<input type="radio" name="choix" value="test" />Test: all the entries are listed with their state, no data is not
written in the base<br/>
	<input type="radio" name="choix" value="ecrit" />Inscription in the base: all the entries are treated then listed with
their state. The data are written in the base <br/>
</p>
<p class="title-page">Please provide the file csv :</p>
<p>
	<input type='file'  name='fichier'  />

<input type='submit' value='Téléchargement' />
</p>
</form>
