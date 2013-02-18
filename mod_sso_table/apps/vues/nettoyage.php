<?php
/*
* $Id: nettoyage.php 7744 2011-08-14 13:07:15Z dblanqui $
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

<div>[onload;block=div; when [var.choix_info]='table_vide']
  La table de correspondance est vide. Vous ne pouvez utiliser cette option.
</div>

<div>[onload;block=div; when [var.choix_info]='avertissement_vidage_complet']
  Vous allez supprimer [nbre_entrees] entrées de la table de correspondances. <br/>
  <form action="index.php?ctrl=nettoyage&action=vidage_complet" enctype='multipart/form-data' method="post">
    <p>This action is irreversible. To validate while clicking on the button
To empty.</p>
    <input type='submit' value='Vider' />
  </form>
</div>


<div>[onload;block=div; when [var.choix_info]='vidage_complet']
  You removed [nbre_entrees_nettoyees] entries of the table of correspondences. <br/>
</div>
<div>[onload;block=div; when [var.choix_info]='aucun_anciens_comptes']
  <p> No entry seems to remove in the table of correspondence.
</div>


<div>[onload;block=div; when [var.choix_info]='avertissement_anciens_comptes']
  <p>Here logins of the correspondences to be removed:</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="20%"><strong>Logins Gépi not having an account users in the application</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.login_gepi;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.login_gepi;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
  <form action="index.php?ctrl=nettoyage&action=supp_anciens_comptes" enctype='multipart/form-data' method="post">
    <p>Remove the correspondences of the table click on removing.</p>
    <input type='submit' value='Supprimer' />
  </form>
</div>


<div>[onload;block=div; when [var.choix_info]='supp_anciens_comptes']
  <br />
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="40%"><strong>Logins Gépi not having an account users in the application</strong></td>
      <td ><strong>Action carried out</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.login_gepi;block=tr]</td>
      <td ><div>[b1.couleur;att=div#class][b1.message]</div></td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.login_gepi;block=tr]</td>
      <td ><div>[b1.couleur;att=div#class][b1.message]</div></td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>  
</div>
<div>[onload;block=div; when [var.choix_info]='choix_profil']
  <div class="red">[onload;block=div; when [var.message]!='']
    [var.message]
  </div>
  <p>You will choose one or more user profiles to give to zero the
correspondences; Several choices are possible but any cleaning is
irreversible</p>
  <p class="title-page">Attention, once their correspondence cleaned the users will not be
able to be connected any more in SSO with this module</p>
  <form action="index.php?ctrl=nettoyage&action=choix_profil" enctype='multipart/form-data' method="post">
    <p>
      <input type="checkbox" name="choix_profil[]" value="Administrateur"  />Administrator<br/>
      <input type="checkbox" name="choix_profil[]" value="Cpe"  />Cpe<br/>
      <input type="checkbox" name="choix_profil[]" value="Scolarite"  />Schooling<br/>
      <input type="checkbox" name="choix_profil[]" value="Secours"  />Help<br/>
      <input type="checkbox" name="choix_profil[]" value="Autre"  />Other<br/>
      <input type="checkbox" name="choix_profil[]" value="Professeur"  />Professor<br/>
      <input type="checkbox" name="choix_profil[]" value="Eleve"  />student<br/>
      <input type="checkbox" name="choix_profil[]" value="Responsable"  />Responsible<br/>
    </p>
    <input type='submit' value='Choisir' />
  </form>
</div>

<div>[onload;block=div; when [var.choix_info]='avertissement_profil']
  <p>Number of correspondences to be removed</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="40%"><strong>Profile</strong></td>
      <td ><strong>Numbers of correspondences to be removed</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
  <form action="index.php?ctrl=nettoyage&action=supp_profil" enctype='multipart/form-data' method="post">
    <p>Remove the correspondences of the table click on removing.</p>
    <input type='submit' value='Supprimer' />
  </form>
</div>

<div>[onload;block=div; when [var.choix_info]='resultat_profil']
  <p>Results of the suppressions :</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="40%"><strong>Profile</strong></td>
      <td ><strong>Numbers of correspondences removed</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
</div>

<div>[onload;block=div; when [var.choix_info]='choix_classe']
  <div class="red">[onload;block=div; when [var.message]!='']
    [var.message]
  </div>
  <p>You will give to zero the correspondences for one or more classes
(raised and/or responsible); Several choices are possible but any cleaning is irreversible</p>
  <p class="title-page">Caution , Once their correspondence cleaned the users will not be able to be connected any more in SSO with this module</p>
  <form action="index.php?ctrl=nettoyage&action=choix_classe" enctype='multipart/form-data' method="post">
    <div class="left">
      <p>
        <input type="checkbox" name="choix_classe[]" value="[b1.id;block=p]" >[b1.classe]
      </p>
    </div>
    <div class="left">
      <input type="checkbox" name="choix_profil[]" value="Eleve"   >student<br/>
      <input type="checkbox" name="choix_profil[]" value="Responsable"  />Responsible<br/>
    </div>
    <div>
      <input type='submit' value='Choisir' />
    </div>
  </form>
</div>

<div>[onload;block=div; when [var.choix_info]='avertissement_classe']
  <p>Number of correspondences to be removed</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="20%"><strong>Class</strong></td>
      <td width="20%"><strong>Profile</strong></td>
      <td ><strong>Numbers of correspondences to be removed</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.classe;block=tr]</td>
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.classe;block=tr]</td>
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
  <form action="index.php?ctrl=nettoyage&action=supp_classe" enctype='multipart/form-data' method="post">
    <p>Remove the correspondences of the table click on removing.</p>
    <input type='submit' value='Supprimer' />
  </form>
</div>

<div>[onload;block=div; when [var.choix_info]='resultat_classe']
  <p>Number of removed correspondences</p>
  <table width="70%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
      <td width="20%"><strong>Class</strong></td>
      <td width="20%"><strong>Profile</strong></td>
      <td ><strong>Numbers of removed correspondences</strong></td>
    </tr>
    <tr bgcolor="#F0F0F0">
      <td>[b1.classe;block=tr]</td>
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#E6E6E6" >
      <td>[b1.classe;block=tr]</td>
      <td>[b1.profil;block=tr]</td>
      <td>[b1.nombre;block=tr]</td>
    </tr>
    <tr bgcolor="#FFCFB9">
      <td colspan="4">[b1;block=tr;nodata]There is no data. </td>
    </tr>
  </table>
</div>
</body>
</html>