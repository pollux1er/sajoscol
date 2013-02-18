<?php
/*
 * $Id: bilans.php 7799 2011-08-17 08:38:10Z dblanqui $
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
    <h3><font class="red">Assessments of the incidents for the period of: <?php echo $_SESSION['stats_periodes']['du'];?> to <?php echo $_SESSION['stats_periodes']['au'];?> </font> </h3>
    <?php ClassVue::afficheVue('parametres.php',$vars) ?>
  </div>
  <div id="tableaux">   
    <div id="banner">
      <ul  class="css-tabs"  id="menutabs">
        <?php  $i=0;
        foreach ($incidents as $titre=>$incidents_titre) :?>
          <?php if($titre=='L\'Etablissement') {
            if($affichage_etab) : ?>
        <li><a href="#tab<?php echo $i;?>" title="Assessment of the incidents" name="<?php echo $titre;?>-onglet-01"><?php echo $titre;?></a></li>
        <li><a href="#tab<?php echo $i+1;?>" name="<?php echo $titre;?>-onglet-02"><img src="apps/img/user.png" alt="Individual synthesis" title="Individual synthesis"/></a>&nbsp;&nbsp;</li>
              <?php $i=$i+2;
            endif;
          } else if ($titre=='Tous les élèves' ||$titre=='Tous les personnels' ) { ?>
        <li><a href="#tab<?php echo $i;?>" title="Assessment of the incidents" name="<?php echo $titre;?>-onglet-01"><?php echo $titre;?></a></li>
        <li><a href="#tab<?php echo $i+1;?>" name="<?php echo $titre;?>-onglet-02"><img src="apps/img/user.png" alt="Individual synthesis" title="Individual synthesis"/></a>&nbsp;&nbsp;</li>
            <?php  $i=$i+2;
          } else { ?>
        <li><a href="#tab<?php echo $i;?>" name="<?php echo $titre;?>-onglet-01" title="Assessment of the incidents">
                <?php if (isset($infos_individus[$titre])) {
                  echo substr($infos_individus[$titre]['prenom'],0,1).'.'.$infos_individus[$titre]['nom'];
                  if (isset($infos_individus[$titre]['classe'])) echo'('.$infos_individus[$titre]['classe'].')';
                }
                else echo $titre;?></a>
        </li>
            <?php if (isset($infos_individus[$titre]['classe'])|| !isset($infos_individus[$titre])) { ?>
        <li><a href="#tab<?php echo $i+1;?>" name="<?php echo $titre;?>-onglet-02"><img src="apps/img/user.png" alt="Synthesis by sttudent" title="Synthesis by student"/></a>&nbsp;&nbsp;</li>
              <?php
              $i=$i+2;
            }
            else {
              $i=$i+1;
            }
        }
        endforeach ?>
      </ul>
    </div>
    <div class="css-panes" id="containDiv">
      <?php
      $i=0;
      foreach ($incidents as $titre=>$incidents_titre) {
        if ($titre!=='L\'Etablissement' || ($titre=='L\'Etablissement' && !is_null($affichage_etab) ) ) {?>
      <div class="panel" id="tab<?php echo $i;?>">
            <?php
            if (isset($incidents_titre['error'])) {?>
        <table class="boireaus">
          <tr ><td class="nouveau"><font class='titre'>Assessment of the incidents concerning :</font>
                    <?php if (isset($infos_individus[$titre])) {
                      echo $infos_individus[$titre]['prenom'].' '.$infos_individus[$titre]['nom'];
                      if (isset($infos_individus[$titre]['classe'])) echo'('.$infos_individus[$titre]['classe'].')';
                    }
                    else echo $titre;?>
            </td></tr>
          <tr><td class='nouveau'>No incidents with the selected criteria...</td></tr>
        </table><br /><br />

              <?php echo'</div>';?>
              <?php if ($titre!=='L\'Etablissement' || $titre=='Tous les élèves' ||$titre=='Tous les personnels') {?>
        <div class="panel" id="tab<?php echo $i+1;?>">
          <table class="boireaus">
            <tr><td class="nouveau"><strong>Individual assessment</strong> </td></tr>
            <tr><td class="nouveau">No incidents with the selected criteria...</td></tr>
          </table>
        </div>
                <?php
                $i=$i+2;
              }
            } else { ?>
        <table class="boireaus">
          <tr >
            <td rowspan="6"  colspan="5" class='nouveau'>
              <p><font class='titre'>Assessment of the incidents concerning: </font>
                      <?php if (isset($infos_individus[$titre])) {
                        echo $infos_individus[$titre]['prenom'].' '.$infos_individus[$titre]['nom'];
                        if (isset($infos_individus[$titre]['classe'])) echo'('.$infos_individus[$titre]['classe'].')';
                      }
                      else echo $titre;?>
              </p>
                    <?php if($filtres_categories||$filtres_mesures||$filtres_roles||$filtres_sanctions) { ?><p>with the select filters</p><?php }?>
            </td>
            <td  <?php if ($titre=='L\'Etablissement' ) {?> colspan="3" <?php }?> class='nouveau'><font class='titre'>Numbers of incidents over the period:</font> <?php echo $totaux[$titre]['incidents']; ?></td><?php if ($titre!=='L\'Etablissement' ) {?> <td  class='nouveau' > <font class='titre'>% over the period/Etab: </font> <?php echo round((100*($totaux[$titre]['incidents']/$totaux['L\'Etablissement']['incidents'])),2);?></td><?php } ?></tr>
          <tr><td  <?php if ($titre=='L\'Etablissement' ) {?> colspan="2" <?php }?> class='nouveau'><font class='titre'>Numbers total measures taken for these incidents :</font> <?php echo $totaux[$titre]['mesures_prises']; ?></td><?php if ($titre!=='L\'Etablissement' ) {?> <td  class='nouveau' > <font class='titre'>% over the period/Etab: </font> <?php if($totaux['L\'Etablissement']['mesures_prises']) echo round((100*($totaux[$titre]['mesures_prises']/$totaux['L\'Etablissement']['mesures_prises'])),2); else echo'0';?></td><?php } ?></tr>
          <tr><td  <?php if ($titre=='L\'Etablissement' ) {?> colspan="2" <?php }?> class='nouveau'><font class='titre'>Numbers total the measurements requested for these incidents :</font> <?php echo $totaux[$titre]['mesures_demandees']; ?></td><?php if ($titre!=='L\'Etablissement' ) {?> <td  class='nouveau' > <font class='titre'>% over the period/Etab: </font> <?php if($totaux['L\'Etablissement']['mesures_demandees']) echo round((100*($totaux[$titre]['mesures_demandees']/$totaux['L\'Etablissement']['mesures_demandees'])),2); else echo'0';?></td><?php } ?></tr>
          <tr><td  <?php if ($titre=='L\'Etablissement' ) {?> colspan="2" <?php }?> class='nouveau'><font class='titre'>Numbers total sanctions taken for these incidents:</font> <?php echo $totaux[$titre]['sanctions']; ?></td><?php if ($titre!=='L\'Etablissement' ) {?> <td  class='nouveau' > <font class='titre'>% over the period/Etab: </font> <?php if($totaux['L\'Etablissement']['sanctions']) echo round((100*($totaux[$titre]['sanctions']/$totaux['L\'Etablissement']['sanctions'])),2); else echo'0';?></td><?php } ?></tr>
          <tr><td  <?php if ($titre=='L\'Etablissement' ) {?> colspan="2" <?php }?> class='nouveau'><font class='titre'>Numbers total hours of reserves for these incidents:</font> <?php echo $totaux[$titre]['heures_retenues']; ?></td><?php if ($titre!=='L\'Etablissement' ) {?> <td  class='nouveau' > <font class='titre'>% over the period/Etab: </font> <?php if($totaux['L\'Etablissement']['heures_retenues']) echo round((100*($totaux[$titre]['heures_retenues']/$totaux['L\'Etablissement']['heures_retenues'])),2); else echo '0'; ?></td><?php } ?></tr>
          <tr><td  <?php if ($titre=='L\'Etablissement' ) {?> colspan="2" <?php }?> class='nouveau'><font class='titre'>Numbers total days of exclusions for these incidents:</font> <?php echo $totaux[$titre]['jours_exclusions']; ?></td><?php if ($titre!=='L\'Etablissement' ) {?> <td  class='nouveau' > <font class='titre'>% over the period/Etab: </font> <?php if($totaux['L\'Etablissement']['jours_exclusions']) echo round((100*($totaux[$titre]['jours_exclusions']/$totaux['L\'Etablissement']['jours_exclusions'])),2); else echo '0'; ?></td><?php } ?></tr>
        </table>
              <?php if($mode_detaille) { ?>
        <table class="sortable resizable " id="table<?php echo $i;?>">
          <thead>
            <tr><th><font class='titre'>Date</font></th><th class="text"><font class='titre'>Informant</font></th><th><font class='titre'>Hour</font></th><th class="text"><font class='titre'>Nature</font></th>
              <th><font class='titre' title="Catégories">Cat.</font></th><th class="text" ><font class='titre'>Description</font></th><th  width="50%" class="nosort"><font class='titre'>Follow-up</font></th></tr>
          </thead>
                  <?php $alt_b=1;
                  foreach($incidents_titre as  $incident) {
                    $alt_b=$alt_b*(-1);?>
          <tr class='lig<?php echo $alt_b;?>'><td><?php echo $incident->date; ?></td><td><?php echo $incident->declarant; ?></td><td><?php echo $incident->heure; ?></td>
            <td><?php echo $incident->nature; ?></td><td><?php if(!is_null($incident->id_categorie))echo $incident->sigle_categorie;else echo'-'; ?></td><td><?php echo $incident->description; ?></td>
            <td class="nouveau"><?php if(!isset($protagonistes[$incident->id_incident]))echo'<h3 class="red">No protagonist defined for this incident</h3>';
                        else { ?>
              <table class="boireaus" width="100%" >
                            <?php foreach($protagonistes[$incident->id_incident] as $protagoniste) {?>
                <tr><td>
                                  <?php echo $protagoniste->prenom.' '.$protagoniste->nom.' <br/>  ';
                                  echo $protagoniste->statut.' ';
                                  if($protagoniste->classe) echo $protagoniste->classe .' - '; else echo ' - ' ;
                                  if($protagoniste->qualite=="") echo'<font class="red">No affected role.</font><br />';
                                  else echo $protagoniste->qualite.'<br />';
                                  ?></td><td ><?php
                                  if (isset($mesures[$incident->id_incident][$protagoniste->login])) { ?>
                    <p><strong>Measurements :</strong></p>
                    <table class="boireaus" >
                      <tr><th><font class='titre'>Plain</font></th><th><font class='titre'>Measure</font></th></tr>
                                      <?php
                                      $alt_b=1;
                                      foreach ($mesures[$incident->id_incident][$protagoniste->login] as $mesure) {
                                        $alt_b=$alt_b*(-1); ?>
                      <tr class="lig<?php echo $alt_b;?>"><td><?php echo $mesure->mesure; ?></td>
                        <td><?php echo $mesure->type.' par '.$mesure->login_u; ?></td></tr> <?php } ?>
                    </table>
                                    <?php  }
                                  if (isset($sanctions[$incident->id_incident][$protagoniste->login])) { ?>
                    <p><strong>Sanctions :</strong></p>
                    <table class="boireaus" width="100%">
                      <tr><th><font class='titre'>plain</font></th><th><font class='titre'>made</font></th><th><font class='titre'>Date</font></th>
                          <th><font class='titre'>Duration</font></th>
                      </tr>
                                      <?php
                                      $alt_b=1;
                                      foreach ($sanctions[$incident->id_incident][$protagoniste->login] as $sanction) {
                                        $alt_b=$alt_b*(-1); ?>
                      <tr class="lig<?php echo $alt_b;?>"><td><?php echo $sanction->nature; ?></td>
                        <td><?php echo $sanction->effectuee; ?></td>
                        <td><?php if($sanction->nature=='retenue')echo $sanction->ret_date;
                                  if($sanction->nature=='exclusion')echo 'of '.$sanction->exc_date_debut.' au '.$sanction->exc_date_fin;
                                  if($sanction->nature=='travail')echo 'For '.$sanction->trv_date_retour;?>
                        </td>
                        <td><?php if($sanction->nature=='retenue') {
                                              echo $sanction->ret_duree.' hour';
                                              if ($sanction->ret_duree >1) echo 's';
                                            }else if($sanction->nature=='exclusion') {
                                              echo $sanction->exc_duree.' day';
                                              if ($sanction->exc_duree >1) echo 's';
                                            }else{
                                                echo'-';
                                            }
                                                ?>
                        </td>
                      </tr>
                                        <?php } ?>
                    </table>
                                    <?php } ?>
                  </td></tr>
                              <?php  } ?></table>
                          <?php } ?></td></tr>
                    <?php }
                }?>
        </table>
        <br /><br /><a href="#wrap"><img src="apps/img/retour_haut.png" alt="simple" title="simplifié"/>Retour aux selections </a>
      </div>
            <?php if (isset($liste_eleves[$titre])): ?>
      <div class="panel" id="tab<?php echo $i+1;?>">
        <table class="boireaus"> <tr><td class="nouveau" colspan="11"><strong>Bilan individuel</strong></td><td><a href="#" class="export_csv" name="<?php echo $temp_dir.'/separateur/'.$titre;?>"><img src="../../images/notes_app_csv.png" alt="export_csv"/></a></td></tr></table>
        <table  class="sortable resizable ">
          <thead>
            <tr>
              <?php if($titre=='L\'Etablissement' || $titre=='Tous les élèves' ||$titre=='Tous les personnels' ){?>
              <th colspan="3"
              <?php } else { ?>
              <th colspan="2"
              <?php } ?>
              <?php if (!isset($totaux_indiv[$titre])) {?> <?php }?>>Individual</th>
              <th >Incidents</th><th colspan="2" <?php if (!isset($totaux_indiv[$titre])) {?> <?php }?>>Measurements taken</th>
              <th colspan="2" <?php if (!isset($totaux_indiv[$titre])) {?> <?php }?>>punishment taken</th>
              <th colspan="2" <?php if (!isset($totaux_indiv[$titre])) {?> <?php }?>>Hours of reserves</th>
              <th colspan="2" <?php if (!isset($totaux_indiv[$titre])) {?> <?php }?>>Days of exclusion</th>
            </tr>
            <tr>
              <th>Name</th><th>First name</th>
              <?php if($titre=='L\'Etablissement' || $titre=='Tous les élèves' ||$titre=='Tous les personnels' ){?>
              <th class="text">Class</th>
              <?php }  ?>
              <th>Numbers</th><th>Numbers</th><th>%/Etab</th><th>Numbers</th><th>%/Etab</th><th>Numbers</th><th>%/Etab</th><th>Numbers</th><th>%/Etab</th>
            </tr>
          </thead>
          <tbody>
                    <?php
                    $alt_b=1;
                    foreach ($liste_eleves[$titre] as $eleve) {
                      $alt_b=$alt_b*(-1);?>
            <tr <?php if ($alt_b==1) echo"class='alt'";?>><td><a href="index.php?ctrl=Bilans&action=add_selection&login=<?php echo $eleve?>"><?php echo $totaux_indiv[$eleve]['nom']; ?></a></td><td><?php echo $totaux_indiv[$eleve]['prenom']; ?></td>
               <?php if($titre=='L\'Etablissement' || $titre=='Tous les élèves' ||$titre=='Tous les personnels' ){?>
              <td><?php echo $totaux_indiv[$eleve]['classe']; ?></td>
              <?php }  ?>
              <td><?php echo $totaux_indiv[$eleve]['incidents']; ?></td><td><?php if(isset($totaux_indiv[$eleve]['mesures'])) echo $totaux_indiv[$eleve]['mesures'];else echo'0'; ?></td><td><?php if($totaux['L\'Etablissement']['mesures_prises'])echo round(100*($totaux_indiv[$eleve]['mesures']/$totaux['L\'Etablissement']['mesures_prises']),2); else echo'0';?></td>
              <td><?php if(isset($totaux_indiv[$eleve]['sanctions'])) echo $totaux_indiv[$eleve]['sanctions']; else echo '0';?></td><td><?php if($totaux['L\'Etablissement']['sanctions']) echo round(100* ($totaux_indiv[$eleve]['sanctions']/$totaux['L\'Etablissement']['sanctions']),2); else echo'0';?></td>
              <td><?php if(isset($totaux_indiv[$eleve]['heures_retenues'])) echo $totaux_indiv[$eleve]['heures_retenues'];else echo '0'; ?></td><td><?php if($totaux['L\'Etablissement']['heures_retenues'])echo round(100*($totaux_indiv[$eleve]['heures_retenues']/$totaux['L\'Etablissement']['heures_retenues']),2);else echo'0';?></td>
              <td><?php if(isset($totaux_indiv[$eleve]['jours_exclusions'])) echo $totaux_indiv[$eleve]['jours_exclusions'];else echo '0'; ?></td><td><?php if($totaux['L\'Etablissement']['jours_exclusions'])echo round(100*($totaux_indiv[$eleve]['jours_exclusions']/$totaux['L\'Etablissement']['jours_exclusions']),2);else echo'0';?></td></tr>
                      <?php }?>
          </tbody>
                  <?php if (!isset($totaux_indiv[$titre])) { ?>
          <tfoot>
            <tr>
              <?php if($titre=='L\'Etablissement' || $titre=='Tous les élèves' ||$titre=='Tous les personnels' ){?>
              <td colspan="3">
              <?php } else{ ?>
              <td colspan="2">
              <?php } ?>
              Total</td>
              <td><?php if(isset($totaux_par_classe[$titre]['incidents']))echo $totaux_par_classe[$titre]['incidents']; else echo'0';?></td><td><?php if(isset($totaux['L\'Etablissement']['mesures_prises'])) echo $totaux_par_classe[$titre]['mesures']; else echo'0';?></td><td><?php  if(isset($totaux['L\'Etablissement']['mesures_prises']) && $totaux['L\'Etablissement']['mesures_prises']>0) echo round(100*($totaux_par_classe[$titre]['mesures']/$totaux['L\'Etablissement']['mesures_prises']),2); else echo'0';?></td>
              <td><?php if(isset($totaux_par_classe[$titre]['sanctions'])) echo $totaux_par_classe[$titre]['sanctions']; else echo'0';?></td><td><?php if(isset($totaux['L\'Etablissement']['sanctions']) && $totaux['L\'Etablissement']['sanctions']>0) echo round(100*($totaux_par_classe[$titre]['sanctions']/$totaux['L\'Etablissement']['sanctions']),2); else echo'0';?></td>
              <td><?php if(isset($totaux_par_classe[$titre]['heures_retenues'])) echo $totaux_par_classe[$titre]['heures_retenues']; else echo'0';?></td><td><?php if(isset($totaux['L\'Etablissement']['heures_retenues']) && $totaux['L\'Etablissement']['heures_retenues']>0) echo round(100*($totaux_par_classe[$titre]['heures_retenues']/$totaux['L\'Etablissement']['heures_retenues']),2); else echo'0';?></td>
              <td><?php if(isset($totaux_par_classe[$titre]['jours_exclusions'])) echo $totaux_par_classe[$titre]['jours_exclusions']; else echo'0';?></td><td><?php if(isset($totaux['L\'Etablissement']['jours_exclusions']) && $totaux['L\'Etablissement']['jours_exclusions']>0) echo round(100*($totaux_par_classe[$titre]['jours_exclusions']/$totaux['L\'Etablissement']['jours_exclusions']),2);else echo'0';?></td>
            </tr>
          </tfoot>
                    <?php }?>
        </table>
      </div>
              <?php  $i=$i+2;
            else :
              $i=$i+1;
            endif;
          }
        }
      }?>
    </div>
  </div>
</div>