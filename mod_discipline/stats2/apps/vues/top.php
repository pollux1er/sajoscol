<?php
/*
 * $Id: top.php 7799 2011-08-17 08:38:10Z dblanqui $
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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
<div id="result">
  <div id="wrap" >
    <h3><font class="red">Signal 10 of the incidents for the period of: <?php echo $_SESSION['stats_periodes']['du'];?> to <?php echo $_SESSION['stats_periodes']['au'];?> </font> </h3>
    <?php ClassVue::afficheVue('parametres.php',$vars) ?>
    <div id="tableaux">
      <div class="float_left" >
        <h3>Signal 10 of the incidents </h3>
        <?php if($top_incidents): ?>
        <table class="boireaus" >
          <tr><th><font class='titre'>Name</font></th><th><font class='titre'>Class</font></th><th><font class='titre'>Numbers</font></th></tr>
          <?php
          $alt_b=1;
          foreach ($top_incidents as $eleve) {
            $alt_b=$alt_b*(-1); ?>
          <tr class="lig<?php echo $alt_b;?>"><td><a href="index.php?ctrl=Bilans&action=add_selection&login=<?php echo $eleve->login?>"><?php echo $eleve->nom.' '.$eleve->prenom; ?></a></td><td><?php echo $eleve->classe ?></td><td><?php echo $eleve->nb ?></td>
          </tr>
            <?php } ?>
        </table>
        <?php else: ?>
        <p>No incidents over the period with the selectionnés filters</p>
        <?php endif; ?>
      </div>
      <div class="float_left" >
        <h3>Signal 10 of the sanctions </h3>
        <?php if($top_sanctions): ?>
        <table class="boireaus" >
          <tr><th><font class='titre'>Name</font></th><th><font class='titre'>Class</font></th><th><font class='titre'>Numbers</font></th></tr>
          <?php
          $alt_b=1;
          foreach ($top_sanctions as $eleve) {
            $alt_b=$alt_b*(-1); ?>
          <tr class="lig<?php echo $alt_b;?>"><td><a href="index.php?ctrl=Bilans&action=add_selection&login=<?php echo $eleve->login?>"><?php echo $eleve->nom.' '.$eleve->prenom; ?></a></td><td><?php echo $eleve->classe ?></td><td><?php echo $eleve->nb ?></td>
          </tr>
            <?php } ?>
        </table>
        <?php else: ?>
        <p>No sanctions over the period with the selectionnés filters</p>
        <?php endif; ?>
      </div>
      <div class="float_left" >
        <h3>Signal 10 of the hours of reserves </h3>
         <?php if($top_retenues): ?>
        <table class="boireaus" >
          <tr><th><font class='titre'>Name</font></th><th><font class='titre'>Class</font></th><th><font class='titre'>Numbers</font></th></tr>
          <?php
          $alt_b=1;
          foreach ($top_retenues as $eleve) {
            $alt_b=$alt_b*(-1); ?>
          <tr class="lig<?php echo $alt_b;?>"><td><a href="index.php?ctrl=Bilans&action=add_selection&login=<?php echo $eleve->login?>"><?php echo $eleve->nom.' '.$eleve->prenom; ?></a></td><td><?php echo $eleve->classe ?></td><td><?php echo $eleve->nb ?></td>
          </tr>
            <?php } ?>
        </table>
        <?php else: ?>
        <p>No reserves over the period with the selectionnés filters</p>
        <?php endif; ?>
      </div>
      <div class="float_left" >
        <h3>Signal 10 of the number of exclusions </h3>
         <?php if($top_exclusions): ?>
        <table class="boireaus" >
          <tr><th><font class='titre'>Name</font></th><th><font class='titre'>Class</font></th><th><font class='titre'>Numbers</font></th></tr>
          <?php
          $alt_b=1;
          foreach ($top_exclusions as $eleve) {
            $alt_b=$alt_b*(-1); ?>
          <tr class="lig<?php echo $alt_b;?>"><td><a href="index.php?ctrl=Bilans&action=add_selection&login=<?php echo $eleve->login?>"><?php echo $eleve->nom.' '.$eleve->prenom; ?></a></td><td><?php echo $eleve->classe ?></td><td><?php echo $eleve->nb ?></td>
          </tr>
            <?php } ?>
        </table>
        <?php else: ?>
        <p>No exclusions over the period with the select filters</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>


