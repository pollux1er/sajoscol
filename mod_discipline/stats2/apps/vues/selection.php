<?php
/*
 * $Id: selection.php 7799 2011-08-17 08:38:10Z dblanqui $
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
  <div id="wrap">
    <h3><font class='red'>Selection of the data to be treated and the period of treatment:</font></h3>
    <div id="select">
      <div id="periode">
        <form action="index.php?ctrl=select&choix=<?php echo $_SESSION['stats_choix']; ?>" method="post" name="select_donnees" id="select_donnees">
          <fieldset id="bloc_periode">
            <legend class="legend">Period of treatment</legend>
            <?php if ($periodes_calendrier) :?>
              <?php for($i=0;$i<count($periodes_calendrier);$i++) :?>
            <p class="selected">
              <label for="periode<?php echo $i; ?>"><?php echo $periodes_calendrier[$i]['nom_calendrier'];?></label>
              <input type="radio"  name="id_calendrier" id="id_calendrier<?php echo $i; ?>" value="<?php echo $periodes_calendrier[$i]['id_calendrier']?>"<?php if (isset($_SESSION['stats_periodes']['periode']) && $_SESSION['stats_periodes']['periode']==$periodes_calendrier[$i]['id_calendrier']) echo 'checked';?>>
                <?php endfor;?>
              ou:
            </p>
            <?php endif ?>
            <p class="selected">
              <label for="choix_month">Choose one month of treatment</label>
              <select  name="month" id="month" size="0" >
                <?php foreach($months as $key=>$value) :?>
                  <?php  if(isset($_SESSION['stats_periodes']['month'])&& $_SESSION['stats_periodes']['month']==$key) :?>
                <option selected value="<?php echo $key;?>" ><?php echo $value;?></option>
                  <?php  else : ?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                  <?php endif;?>
                <?php endforeach ?>
              </select>&nbsp;or :
            </p>
            <label for="du">of</label>
            <input name="du"  id="du"  type="text" tabindex="4" value="<?php  echo $_SESSION['stats_periodes']['du']; ?>" size="10" maxlength="10" style="border: 1px solid #000000;" />
            <a href="#calend" onClick="<?php echo $cal_1->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
            <label for="au">to</label>
            <input name="au"  id="au" type="text" tabindex="5"  value="<?php echo $_SESSION['stats_periodes']['au']; ?>" size="10" maxlength="10" style="border: 1px solid #000000;" />
            <a href="#calend" onClick="<?php echo $cal_2->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><br />
            <br />            
            <input type="submit" value="Enregistrer" class="submit"/>
          </fieldset>
          <div>
            <fieldset class="individus"><legend class="individus">Selection</legend>
              <p class="selected">Complete data :
              </p>
              <p class="selected">
                establishment
                <input type="checkbox"  name="etab_all" id="etab_all0" <?php if (isset($_SESSION['etab_all']))echo'checked'; ?>/>                
                Student
                <input type="checkbox"  name="eleve_all" id="eleve_all" <?php if (isset($_SESSION['eleve_all']))echo'checked'; ?>/>
                Personnel
                <input type="checkbox"  name="pers_all" id="pers_all" <?php if (isset($_SESSION['pers_all']))echo'checked'; ?>/>                
                <input type="hidden" name='posted' value='ok'/>
              </p>
              <p class="selected">and/or :
              </p>
              <div class="sous_menu">
                <span id="indiv"><a href="index.php?choix=eleves" >Individual research</a></span>|
                <span id="classe"><a href="index.php?choix=classe" >Search by class</a></span>
              </div>
              <br />
              <?php if ($_SESSION['stats_choix']=='eleves'||$_SESSION['stats_choix']=='personnels') {?>
              <div id="recherche_indiv">
                <input type="radio" name="choix" id="choix" value="eleves" <?php if ($_SESSION['stats_choix']=='eleves'||!isset($_SESSION['choix']))echo'checked'; ?> />
                student                  
                <input type="radio" name="choix" id="choix2" value="personnels" <?php if ($_SESSION['stats_choix']=='personnels')echo'checked'; ?> />
                Personnel                 
                <br /><br />
                <label for="nom"></label>
                <input type="text" name="nom" id="nom" value="" />
                <span id="indicateur" style="display: none;"><img src="apps/img/loader.gif" alt="loader"/></span>
                <div class="auto_complete" id="auto_complete"></div>
                <input type="hidden" name="login" id="login" value="" />
                <br />                
                <br /><br />
              </div>
                <?php } else {?>
              <div id="recherche_classe">
                  <?php $max=count($classes); ?>
                <a href="javascript:modif_case('classes',true,<?php echo $max; ?>)"><img src='../../images/enabled.png' width='15' height='15' alt='all tick' title='all tick' /></a>/
                <a href="javascript:modif_case('classes',false,<?php echo $max; ?>)"><img src='../../images/disabled.png' width='15' height='15' alt='all shoot' title='all shoot'/></a>
                <br />
                  <?php
                  $cpt=0;
                  foreach($classes as $classe) {
                    echo "<input type='checkbox' name='classes[]' id='classes_$cpt' value='$classe->id' /><label for='classes_$cpt'>$classe->classe ($classe->nom_complet)</label><br />\n";
                    $cpt++;
                  } ?>
                <input type="submit"  value="Ajouter" class="submit"/>
              </div>
                <?php } ?>              
            </fieldset>
          </div>
        </form>
      </div>
    </div>
    <div id="selected">
      <p>This module makes it possible to carry out statistics/followed on the incidents declared in the establishment. <br /> For that you must as a preliminary :
      </p>
      <ul>
        <li>Adjust if necessary the period of treatment</li>
        <li>Select the data to be treated (given total, select by individual
or class)</li>
      </ul>
      <div >
        <p class="selected_titre">Period of Treatment:</p>
        <ul class="selected_titre">
          <li id="selected"><?php echo'of '.$_SESSION['stats_periodes']['du'].' to '.$_SESSION['stats_periodes']['au'];?></li>
        </ul>
        <?php if (isset($_SESSION['etab_all'])|| isset($_SESSION['eleve_all'] )|| isset($_SESSION['pers_all'])) :?>
        <p class="selected_titre">Complete data :</p>           
        <ul class="selected_titre">
            <?php if (isset($_SESSION['etab_all'])) :?>
          <li id="selected"> establishment </li>
            <?php endif ?>
            <?php if (isset($_SESSION['eleve_all'])) :?>
          <li id="selected">student </li>
            <?php endif ?>
            <?php if (isset($_SESSION['pers_all'])):?>
          <li id="selected">Personnel </li>
            <?php endif ?>
        </ul>
        <?php endif ?>
        <?php if (isset($_SESSION['individus'])) : ?>
        <p class="selected_titre">People: All to erase <a href="index.php?ctrl=select&del_type=all_data&del=individus"><img src="apps/img/close12.png" alt="close"/></a></p>
        <ul class="selected_titre">
            <?php if (isset($_SESSION['individus'])) :?>
              <?php   foreach($individus_identites as $key=>$value) : ?>
          <li id="selected"><?php echo $value['nom'].' '.$value['prenom'];?><a href="index.php?ctrl=select&del_type=individus&del=<?php echo $value['login'];?>"><img src="apps/img/close12.png" alt="close"></a></li>
              <?php endforeach ?>
            <?php  endif ?>
        </ul>
        <?php  endif ?>
        <?php if ( isset($_SESSION['stats_classes_selected'])) : ?>
        <p class="selected_titre">Classes: All delecte <a href="index.php?ctrl=select&del_type=all_data&del=classes"><img src="apps/img/close12.png" alt="close"/></a></p>
        <ul class="selected_titre">
            <?php if (isset($_SESSION['stats_classes_selected'])) :?>
              <?php foreach($noms_classes as $value) :?>
          <li id="selected"><?php  echo $value[0]['classe'].'( '.$value[0]['nom_complet'].' )' ; ?><a href="index.php?ctrl=select&del_type=classes&del=<?php echo $value[0]['id'];?>"><img src="apps/img/close12.png" alt="close"></a></li>
              <?php endforeach ?>
            <?php endif ?>
        </ul>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>