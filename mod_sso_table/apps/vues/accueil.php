<?php
/*
* $Id: accueil.php 7744 2011-08-14 13:07:15Z dblanqui $
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
// On emp�che l'acc�s direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};
?>
 [onload;file=menu.php]
  <ol>
  <li><a href="index.php?ctrl=import">Populate the table of correspondence starting from a file csv</a></li>
  <li><a href="index.php?ctrl=maj">Update the correspondence for a given account</a></li>
  <li><a href="index.php?ctrl=cvsent">Populate the table of correspondence starting from a file csv
resulting from a ENT</a></li>
  <li><a href="index.php?ctrl=nettoyage">Cleaning of the table of correspondence (change of year, test?)</a></li>
  <li><a href="index.php?ctrl=help">Help on the operation of the module</a></li>
  </ol>
 </body>
 </html>
