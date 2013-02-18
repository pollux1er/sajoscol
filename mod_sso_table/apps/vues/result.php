<?php
/*
* $Id: result.php 7744 2011-08-14 13:07:15Z dblanqui $
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
<div>[onload;block=div; when [var.choix_info]='affich_result']
<p>Results of the treatment :</p>
<table width="80%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
        <td width="20%"><strong>Login gépi</strong></td>
        <td width="20%"><strong>Login sso</strong></td>
        <td width="60%"><strong>Result</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
        <td>[b1.login_gepi]</td>
        <td>[b1.login_sso;block=tr]</td>
        <td ><div>[b1.couleur;att=div#class][b1.message]</div></td>
    </tr>
    <tr bgcolor="#E6E6E6" >
        <td>[b1.login_gepi]</td>
        <td>[b1.login_sso;block=tr]</td>
        <td ><div>[b1.couleur;att=div#class][b1.message]</div></td>
    </tr>
</table>
</div>
<div>[onload;block=div; when [var.choix_info]='no_error']
    <p> Apparently no error is has to announce </p>
</div>
<div>[onload;block=div; when [var.choix_info]='no_data']
    <p> Apparently the file does not contain data has to amalgamate !! </p>
</div>
</body>
</html>