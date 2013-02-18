<?php
/*
* $Id: help.php 7805 2011-08-17 13:43:12Z dblanqui $
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
<h2>This module is used to create a correspondence between logins Gépi
and ENT in the case of an authentification CASE</h2>
<h3>There are three possibilities for the installation of the
correspondence :</h3>
<ol>
	<li><strong>By importation of the correspondences since a file csv :</strong></li>
	<p>Click on Importation of data</p>
	<p>The file required must be called correspondances.csv</p>
	<p>It should contain by line only two data separated by one ;</p>
	<p>The first data is the login Gépi and the second the login sso (of the
ent for example)</p>
	<p>Here an example</p>
	<img src='img/fichier.png' />
	<p class='message_red'>Check well in a software like notepad++ for example that there are no
blank lines. </p>
	<p class='message_red'>Attention with the formatting of the data with spreadsheves like Excel
for example. </p>
	<p>Once the treatment carried out you will obtain a table with the
results:</p>
	<img src='img/resultat.png' />
	<p>If the user does not exist in Gépi, or if an entry exists déja in
the table of correspondence (login Gépi or sso),
	no correspondence is set up.</p>
	<p>If the user exists in Gépi but that the account is not parameterized
in sso the correspondence is installation but the mode of connection
must be modified in Gépi </p>
	<p>In the other cases the correspondence is installation.</p>
	<br />

	<li><strong>By manual installation of the correspondence for a user of Gépi :</strong></li>
	<p>Click on <em>Update of data </em></p>
	<p>Scan for the name of a user of Gépi </p>
	<p class='message_red'>Caution this user must have his mode of authentification
parameterized in sso</p>
	<p>Click on the login of the selected user :</p>
	<p>You can enter the login sso for the correspondence with Gépi.</p>
	<p>If a correspondence exists déja the login sso posts. You can update
it.</p>
	<p>Here a screen printing :</p>
	<img src='img/maj.png' />
	<br />
	<br />

	<li><strong>By research of the correspondences on the names and first names,
starting from a file csv :</strong></li>	
	<p>To click on <em>CVS export ENT</em></p>
	<p>The file required must be called < em>ENT-Identifiants.csv</em></p>
	<p>It must contain by line thirteen fields separated by one ;</p>
	<ol>
	  <li>RNE of the establishment: not used</li>
	  <li>UID: identifier SSO in the ENT, it is this field which is used as
joint</li>
	  <li>classify student: is used to locate the accounts parents and student</li>
	  <li>profile: is used to differentiate the doubled blooms parents and
pupils, the headings can be different from those of Gépi but must be
coherent</li>
	  <li>first name: the first must correspond to that of Gépi</li>
	  <li>name: must correspond to that of Gépi</li>
	  <li>login: login in the ENT, not used</li>
	  <li>password: password in the ENT, not used</li>
	  <li>cle of joint: not used</li>
	  <li>uid father: is used to locate the pupils and to find the responsible
ones in the event of doubled bloom</li>
	  <li>uid mother: is used to locate the pupils and to find the responsible
ones in the event of doubled bloom</li>
	  <li>uid tuteur1: is used to locate the pupils and to find the responsible
ones in the event of doubled bloom</li>
	  <li>uid tuteur2: is used to locate the pupils and to find the responsible
ones in the event of doubled bloom</li>
	</ol>
	<p>The fields not used can be left empty</p>
	<p>Here an example</p>
	<img src='img/identifiants.png' />
	<p class='message_red'>
		Check well in a software like notepad++ for example that there are no
blank lines.
	</p>
	<p>
		You can leave the first line with the field names. During the
treatment, you will obtain a recording in error in which you will be
able to check on which fields you make research
	</p>
	<img src='img/cvs_ent_id.png' />
	<p class='message_red'>Caution with the formatting of the data with spreadsheves like Excel
for example. </p>

	<p>Before setting up the correspondences in the base, you can test the
result of the importation:</p>
	<img src='img/cvs_ent.png' />
	<ul>
	<li>To seek errors: All the errors are posted, no data is not written in
the base</li>
	<li>Test: All the lines are treated and posted but no data is written in
the base</li>
	<li>Inscription in the base: All the lines are treated and posted, the
correspondences are written with the need in the base</li>

	</ul>
	<p>Once the treatment carried out you will obtain a table with the
results:</p>
	<img src='img/resultat.png' />
	<p>If the user does not exist in Gépi, no correspondence is
installation.</p>
	<p>If an entry exists déja in the table of correspondence, no
correspondence is installation and one posts if the correspondence is
different.</p>
	<p>If the user exists in Gépi but that the account is not parameterized
in SSO, the correspondence is installation but the mode of connection
must be modified in Gépi.</p>
	<p>In the other cases the correspondence is installation.</p>

</ol>
</body>
 </html>