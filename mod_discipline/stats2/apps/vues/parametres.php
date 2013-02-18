<?php
/*
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
<div class="bilans">
  <form action="index.php?ctrl=bilans&action=<?php echo $action_from;?>" method="post" name="select_evolution" id="select_evolution">
      <table class="boireaus">
      <tr>
        <td class="nouveau"> Choose the filters </td>
        <td><a href="index.php?ctrl=bilans&action=choix_filtres&action_from=<?php echo $action_from;?>"><img src="apps/img/filtres.png" alt="filtres" title="filtrer"/></a></td>
      </tr>
      </table><br />      
      <table class="boireaus">
      <tr>
         <td  class="nouveau" colspan="3">Selected parameters (filters and evolutions)</td>
      </tr>
      <tr>
         <th>Type</th>
         <th>Filters</th>
         <th>Choice for the tables of evolution</th>
      </tr>
      <tr>
        <td class="nouveau"><?php if($filtres_categories) { ?><a href="index.php?ctrl=bilans&action=maj_filtre&type=categories&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Cliquez pour vider" >Categories</a><?php } else echo'Categories'; ?></td>
        <td>
          <?php if($filtres_categories): ?>
            <?php foreach($libelles_categories as $categorie): ?>
          <a href="index.php?ctrl=bilans&action=maj_filtre&type=categories&choix=<?php echo $categorie?>&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Click to remove"><?php echo $categorie,' - '; ?></a>
            <?php endforeach ;?>
          <?php else: ?>
          None
          <?php endif;?>
        </td>        
        <td>
          <input type="radio" name="evolution" id="evolution" value="Catégories" <?php if ($_SESSION['choix_evolution']=='Catégories') echo 'checked';?>>
        </td>
      </tr>
      <tr>
        <td class="nouveau"><?php if($filtres_mesures) { ?><a href="index.php?ctrl=bilans&action=maj_filtre&type=mesures&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Click to empty" >Measurements taken</a><?php } else echo 'Measurements taken';?></td>
        <td>
          <?php if($filtres_mesures) {
            foreach($libelles_mesures as $mesure) {?>
          <a href="index.php?ctrl=bilans&action=maj_filtre&type=mesures&choix=<?php echo $mesure?>&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Click to remove"><?php echo $mesure,' - '?></a>
              <?php    }
          }else {
            echo'None';
          } ?>
        </td>        
        <td>
          <input type="radio" name="evolution" id="evolution" value="Measurements taken" <?php if ($_SESSION['choix_evolution']=='Mesures prises') echo 'checked';?>>
        </td>
      </tr>
      <tr>
        <td class="nouveau"><?php if($filtres_sanctions) {?><a href="index.php?ctrl=bilans&action=maj_filtre&type=sanctions&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Click to empty" >punishment</a><?php } else echo 'punishment';?></td>
        <td>
          <?php if($filtres_sanctions) {
            foreach($filtres_sanctions as $sanction) { ?>
          <a href="index.php?ctrl=bilans&action=maj_filtre&type=sanctions&choix=<?php echo $sanction?>&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Click to remove"><?php echo $sanction,' - '?></a>
              <?php    }
          }else {
            echo'Aucun';
          } ?>
        </td>        
        <td>
          <input type="radio" name="evolution" id="evolution" value="punishment" <?php if ($_SESSION['choix_evolution']=='Sanctions') echo 'checked';?>>
        </td>
      </tr>
      <tr>
        <td class="nouveau"><?php if($filtres_roles) {?><a href="index.php?ctrl=bilans&action=maj_filtre&type=roles&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Click to empty" >Rôles</a><?php } else echo 'Roles';?></td>
        <td><?php if($filtres_roles) {
            foreach($filtres_roles as $role) {?>
          <a href="index.php?ctrl=bilans&action=maj_filtre&type=roles&choix=<?php echo $role;?>&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Click to remove"><?php if($role=="") echo "No affected role - "; else echo $role,' - ';?></a>
              <?php    }
          }else {
            echo'None';
          } ?>
        </td>        
        <td>
          <input type="radio" name="evolution" id="evolution" value="Rôles" <?php if ($_SESSION['choix_evolution']=='Rôles') echo 'checked';?>>
        </td>
      </tr>
    </table>
  </form>
</div>
<div class="bilans">
    <?php if($action_from=='affiche_bilans'):?>
    <table class="boireaus">
    <tr>
      <td  class="nouveau">Choose the mode of representation</td>
      <td><a href="index.php?ctrl=bilans&action=affiche_details"><img src="apps/img/simple.png" alt="simple" title="simplified"/></a>&nbsp;<a href="index.php?ctrl=bilans&action=affiche_details&value=ok"><img src="apps/img/details.png" title="détaillé" alt="detailed"/></a>&nbsp;</td>
    </tr>
    <tr>
      <td  class="nouveau">Active mode of representation</td>
      <td colspan="3"><?php if($mode_detaille) {?>Detailed<?php }else {?>Simplified <?php }?> </td>
    </tr>
    <tr>
    </table>
    <?php endif; ?>
</div>
