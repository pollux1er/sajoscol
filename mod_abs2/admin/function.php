<?php
/*
 *
 * $Id: function.php 7239 2011-06-17 18:39:28Z jjacquard $
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

function ajoutMotifsParDefaut() {
    $motif = new AbsenceEleveMotif();
    $motif->setNom("Médical");
    $motif->setCommentaire("The student misses for medical reason");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }

    $motif = new AbsenceEleveMotif();
    $motif->setNom("Familial");
    $motif->setCommentaire("The student misses for family reason");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }

    $motif = new AbsenceEleveMotif();
    $motif->setNom("Sportive");
    $motif->setCommentaire("The student misses due to sporting competition");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }
}

function ajoutLieuxParDefaut() {
    $lieu = new AbsenceEleveLieu();
    $lieu->setNom("Etablissement");
    $lieu->setCommentaire("The student is in the enclosure of the establishment");
    if (AbsenceEleveLieuQuery::create()->filterByNom($lieu->getNom())->find()->isEmpty()) {
	$lieu->save();
    }
}

function initLieuEtab(){
    $lieu_etab=AbsenceEleveLieuQuery::create()->filterByNom("Etablissement")->findOne();
    if(is_null($lieu_etab)){
       $lieu_etab= new AbsenceEleveLieu();
       $lieu_etab->setNom("Etablissement");
       $lieu_etab->setCommentaire("The student is in the enclosure of the establishment");
       $lieu_etab->save();
    }
    return($lieu_etab->getId());
}

function ajoutJustificationsParDefaut() {
    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Certificat médical");
    $justifications->setCommentaire("A justification established by a medical authority");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }

    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Courrier familial");
    $justifications->setCommentaire("Justification by mail of the family");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }

    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Document in proof of a public administration");
    $justifications->setCommentaire("Justification emitted by a public administration");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }
}

function ajoutTypesParDefaut() {
    $id_lieu_etab=initLieuEtab();
    $type = new AbsenceEleveType();
    $type->setNom("Absence scolaire");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is not present to follow his schooling.");
	$type->setJustificationExigible(true);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Delay intercours");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is late at the time of the intercours");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("External delay");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is late at the time of his arrival in the establishment");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI);
	$type->setRetardReport card(AbsenceEleveType::RETARD_BULLETIN_VRAI);
        
	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Error of seizure");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("There is probably an error of seizure on this recording. To be not
entered,
            a seizure of the type ' Error of saisie' should be associated no other
type, but exclusively the error type of seizure.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_NON_PRECISE);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_NON_PRECISE);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

 	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Infirmerie");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is with the infirmary.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Sortie scolaire");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is at school exit.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(true);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exclusion of the establishment");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is excluded from the establishment.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exclusion/inclusion");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is excluded but present within the establishment.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exclusion of course");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is excluded from course.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
	$type->setTypeSaisie(AbsenceEleveType::$TYPE_SAISIE_DISCIPLINE);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exempted (raises present)");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is exempted but present physically at the time of the
meeting.");
	$type->setJustificationExigible(true);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exempted (not present raises)");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is exempted and not present physically at the time of the
meeting.");
	$type->setJustificationExigible(true);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Stage");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is in training course has the outside of the establishment.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Raise present");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("The student is present.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);
    
	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

}
?>
