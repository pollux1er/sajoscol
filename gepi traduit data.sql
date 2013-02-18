-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Sam 07 Avril 2012 à 11:04
-- Version du serveur: 5.1.36
-- Version de PHP: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `gepi03`
--

-- --------------------------------------------------------

--
-- Structure de la table `absences`
--

CREATE TABLE IF NOT EXISTS `absences` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `periode` int(11) NOT NULL DEFAULT '0',
  `nb_absences` char(2) NOT NULL DEFAULT '',
  `non_justifie` char(2) NOT NULL DEFAULT '',
  `nb_retards` char(2) NOT NULL DEFAULT '',
  `appreciation` text NOT NULL,
  PRIMARY KEY (`login`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `absences`
--


-- --------------------------------------------------------

--
-- Structure de la table `absences_actions`
--

CREATE TABLE IF NOT EXISTS `absences_actions` (
  `id_absence_action` int(11) NOT NULL AUTO_INCREMENT,
  `init_absence_action` char(2) NOT NULL DEFAULT '',
  `def_absence_action` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_absence_action`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `absences_actions`
--

INSERT INTO `absences_actions` (`id_absence_action`, `init_absence_action`, `def_absence_action`) VALUES
(1, 'RC', 'Postponement of the course'),
(2, 'RD', 'Definitive Postponement '),
(3, 'LP', 'Letter to parents'),
(4, 'CE', 'Demand of convocation of the student in school life'),
(5, 'A', 'None');

-- --------------------------------------------------------

--
-- Structure de la table `absences_eleves`
--

CREATE TABLE IF NOT EXISTS `absences_eleves` (
  `id_absence_eleve` int(11) NOT NULL AUTO_INCREMENT,
  `type_absence_eleve` char(1) NOT NULL DEFAULT '',
  `eleve_absence_eleve` varchar(25) NOT NULL DEFAULT '0',
  `justify_absence_eleve` char(3) NOT NULL DEFAULT '',
  `info_justify_absence_eleve` text NOT NULL,
  `motif_absence_eleve` varchar(4) NOT NULL DEFAULT '',
  `info_absence_eleve` text NOT NULL,
  `d_date_absence_eleve` date NOT NULL DEFAULT '0000-00-00',
  `a_date_absence_eleve` date DEFAULT NULL,
  `d_heure_absence_eleve` time DEFAULT NULL,
  `a_heure_absence_eleve` time DEFAULT NULL,
  `saisie_absence_eleve` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_absence_eleve`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `absences_eleves`
--

INSERT INTO `absences_eleves` (`id_absence_eleve`, `type_absence_eleve`, `eleve_absence_eleve`, `justify_absence_eleve`, `info_justify_absence_eleve`, `motif_absence_eleve`, `info_absence_eleve`, `d_date_absence_eleve`, `a_date_absence_eleve`, `d_heure_absence_eleve`, `a_heure_absence_eleve`, `saisie_absence_eleve`) VALUES
(1, 'A', 'siyapze', 'N', '', 'A', '', '2012-03-02', '2012-03-02', '08:00:00', '10:50:00', 'elijah'),
(2, 'A', 'bende', 'N', '', 'A', '', '2012-03-02', '2012-03-02', '10:05:00', '10:50:00', 'elijah'),
(3, 'A', 'achah', 'N', '', 'A', '', '2012-03-03', '2012-03-03', '07:15:00', '08:00:00', 'elijah');

-- --------------------------------------------------------

--
-- Structure de la table `absences_gep`
--

CREATE TABLE IF NOT EXISTS `absences_gep` (
  `id_seq` char(2) NOT NULL DEFAULT '',
  `type` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_seq`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `absences_gep`
--


-- --------------------------------------------------------

--
-- Structure de la table `absences_motifs`
--

CREATE TABLE IF NOT EXISTS `absences_motifs` (
  `id_motif_absence` int(11) NOT NULL AUTO_INCREMENT,
  `init_motif_absence` char(2) NOT NULL DEFAULT '',
  `def_motif_absence` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_motif_absence`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Contenu de la table `absences_motifs`
--

INSERT INTO `absences_motifs` (`id_motif_absence`, `init_motif_absence`, `def_motif_absence`) VALUES
(1, 'A', 'No reason'),
(2, 'AS', 'Sport Accident'),
(3, 'AT', 'Absent in detention'),
(4, 'C', 'In the court'),
(5, 'CF', 'Family suitabilities'),
(6, 'CO', 'Convocation office'),
(7, 'CS', 'Sporting competition'),
(8, 'DI', 'Exempt of E.P.S.'),
(9, 'ET', 'Error of timetable'),
(10, 'EX', 'Examination'),
(11, 'H', 'Hospitalization'),
(12, 'JP', 'Justification by the Headmaster'),
(13, 'MA', 'Disease'),
(14, 'OR', 'Adviser'),
(15, 'PR', 'Alarm clock'),
(16, 'RC', 'Refusal to come to school'),
(17, 'RE', 'Expulsion'),
(18, 'RT', 'Present in detention'),
(19, 'RV', 'Expulsion from the course'),
(20, 'SM', 'Refusal of justification'),
(21, 'SP', 'Teaching outing'),
(22, 'ST', 'Externel training course'),
(23, 'T', 'Telephone'),
(24, 'TR', 'Transport'),
(25, 'VM', 'Medical examination'),
(26, 'IN', 'Infirmary');

-- --------------------------------------------------------

--
-- Structure de la table `absences_rb`
--

CREATE TABLE IF NOT EXISTS `absences_rb` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `eleve_id` varchar(30) NOT NULL,
  `retard_absence` varchar(1) NOT NULL DEFAULT 'A',
  `groupe_id` varchar(8) NOT NULL,
  `edt_id` int(5) NOT NULL DEFAULT '0',
  `jour_semaine` varchar(10) NOT NULL,
  `creneau_id` int(5) NOT NULL,
  `debut_ts` int(11) NOT NULL,
  `fin_ts` int(11) NOT NULL,
  `date_saisie` int(20) NOT NULL,
  `login_saisie` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `eleve_debut_fin_retard` (`eleve_id`,`debut_ts`,`fin_ts`,`retard_absence`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `absences_rb`
--

INSERT INTO `absences_rb` (`id`, `eleve_id`, `retard_absence`, `groupe_id`, `edt_id`, `jour_semaine`, `creneau_id`, `debut_ts`, `fin_ts`, `date_saisie`, `login_saisie`) VALUES
(1, 'siyapze', 'A', '1', 0, 'Vendredi', 2, 1330675200, 1330677900, 1330677660, 'elijah'),
(2, 'appel', 'A', '1', 0, 'Vendredi', 2, 1330675200, 1330677900, 1330677720, 'elijah'),
(3, 'bende', 'A', '1', 0, 'Vendredi', 4, 1330682700, 1330685400, 1330683240, 'elijah'),
(4, 'siyapze', 'A', '1', 0, 'Vendredi', 4, 1330682700, 1330685400, 1330683240, 'elijah'),
(5, 'achah', 'A', '1', 0, 'Samedi', 1, 1330758900, 1330761600, 1330784460, 'elijah');

-- --------------------------------------------------------

--
-- Structure de la table `absences_repas`
--

CREATE TABLE IF NOT EXISTS `absences_repas` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `date_repas` date NOT NULL DEFAULT '0000-00-00',
  `id_groupe` varchar(8) NOT NULL,
  `eleve_id` varchar(30) NOT NULL,
  `pers_id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `absences_repas`
--


-- --------------------------------------------------------

--
-- Structure de la table `acces_cdt`
--

CREATE TABLE IF NOT EXISTS `acces_cdt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `chemin` varchar(255) NOT NULL DEFAULT '',
  `date1` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date2` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `acces_cdt`
--


-- --------------------------------------------------------

--
-- Structure de la table `acces_cdt_groupes`
--

CREATE TABLE IF NOT EXISTS `acces_cdt_groupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_acces` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `acces_cdt_groupes`
--


-- --------------------------------------------------------

--
-- Structure de la table `aid`
--

CREATE TABLE IF NOT EXISTS `aid` (
  `id` varchar(100) NOT NULL DEFAULT '',
  `nom` varchar(100) NOT NULL DEFAULT '',
  `numero` varchar(8) NOT NULL DEFAULT '0',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  `perso1` varchar(255) NOT NULL DEFAULT '',
  `perso2` varchar(255) NOT NULL DEFAULT '',
  `perso3` varchar(255) NOT NULL DEFAULT '',
  `productions` varchar(100) NOT NULL DEFAULT '',
  `resume` text NOT NULL,
  `famille` smallint(6) NOT NULL DEFAULT '0',
  `mots_cles` varchar(255) NOT NULL DEFAULT '',
  `adresse1` varchar(255) NOT NULL DEFAULT '',
  `adresse2` varchar(255) NOT NULL DEFAULT '',
  `public_destinataire` varchar(50) NOT NULL DEFAULT '',
  `contacts` text NOT NULL,
  `divers` text NOT NULL,
  `matiere1` varchar(100) NOT NULL DEFAULT '',
  `matiere2` varchar(100) NOT NULL DEFAULT '',
  `eleve_peut_modifier` enum('y','n') NOT NULL DEFAULT 'n',
  `prof_peut_modifier` enum('y','n') NOT NULL DEFAULT 'n',
  `cpe_peut_modifier` enum('y','n') NOT NULL DEFAULT 'n',
  `fiche_publique` enum('y','n') NOT NULL DEFAULT 'n',
  `affiche_adresse1` enum('y','n') NOT NULL DEFAULT 'n',
  `en_construction` enum('y','n') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid`
--


-- --------------------------------------------------------

--
-- Structure de la table `aid_appreciations`
--

CREATE TABLE IF NOT EXISTS `aid_appreciations` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_aid` varchar(100) NOT NULL DEFAULT '',
  `periode` int(11) NOT NULL DEFAULT '0',
  `appreciation` text NOT NULL,
  `statut` char(10) NOT NULL DEFAULT '',
  `note` float DEFAULT NULL,
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login`,`id_aid`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid_appreciations`
--


-- --------------------------------------------------------

--
-- Structure de la table `aid_config`
--

CREATE TABLE IF NOT EXISTS `aid_config` (
  `nom` char(100) NOT NULL DEFAULT '',
  `nom_complet` char(100) NOT NULL DEFAULT '',
  `note_max` int(11) NOT NULL DEFAULT '0',
  `order_display1` char(1) NOT NULL DEFAULT '0',
  `order_display2` int(11) NOT NULL DEFAULT '0',
  `type_note` char(5) NOT NULL DEFAULT '',
  `display_begin` int(11) NOT NULL DEFAULT '0',
  `display_end` int(11) NOT NULL DEFAULT '0',
  `message` varchar(40) NOT NULL DEFAULT '',
  `display_nom` char(1) NOT NULL DEFAULT '',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  `display_bulletin` char(1) NOT NULL DEFAULT 'y',
  `bull_simplifie` char(1) NOT NULL DEFAULT 'y',
  `outils_complementaires` enum('y','n') NOT NULL DEFAULT 'n',
  `feuille_presence` enum('y','n') NOT NULL DEFAULT 'n',
  `autoriser_inscript_multiples` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`indice_aid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid_config`
--

INSERT INTO `aid_config` (`nom`, `nom_complet`, `note_max`, `order_display1`, `order_display2`, `type_note`, `display_begin`, `display_end`, `message`, `display_nom`, `indice_aid`, `display_bulletin`, `bull_simplifie`, `outils_complementaires`, `feuille_presence`, `autoriser_inscript_multiples`) VALUES
('JOURN', 'JOURNALISM', 0, 'e', 0, 'no', 1, 3, '', 'n', 1, 'n', 'n', 'y', 'n', 'y');

-- --------------------------------------------------------

--
-- Structure de la table `aid_familles`
--

CREATE TABLE IF NOT EXISTS `aid_familles` (
  `ordre_affichage` smallint(6) NOT NULL DEFAULT '0',
  `id` smallint(6) NOT NULL DEFAULT '0',
  `type` varchar(250) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid_familles`
--

INSERT INTO `aid_familles` (`ordre_affichage`, `id`, `type`) VALUES
(0, 10, 'Information, press'),
(1, 11, 'Philosophy and psychology, thought'),
(2, 12, 'Religions'),
(3, 13, 'Social sciences, Society, humanitarian'),
(4, 14, 'Langues, langage'),
(5, 15, 'Sciences (hard sciences)'),
(6, 16, 'Techniques, sciences applied, medicine, cook...'),
(7, 17, 'Arts, leisures and sports'),
(8, 18, 'Literature, theatre, poetry'),
(9, 19, 'Geography and History, old civilizations');

-- --------------------------------------------------------

--
-- Structure de la table `aid_productions`
--

CREATE TABLE IF NOT EXISTS `aid_productions` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `aid_productions`
--

INSERT INTO `aid_productions` (`id`, `nom`) VALUES
(1, 'Folder paper'),
(2, 'Emission of radio'),
(3, 'Exposure'),
(4, 'Film'),
(5, 'Spectacle'),
(6, 'Plastic realization'),
(7, 'Technical or scientific realization'),
(8, 'Video game'),
(9, 'Cultural activity '),
(10, 'Model'),
(11, 'Internet site'),
(12, 'Diaporama'),
(13, 'Musical production'),
(14, 'Theatrical production'),
(15, 'Animation in educational circle'),
(16, 'Software programming'),
(17, 'Newspaper');

-- --------------------------------------------------------

--
-- Structure de la table `aid_public`
--

CREATE TABLE IF NOT EXISTS `aid_public` (
  `ordre_affichage` smallint(6) NOT NULL DEFAULT '0',
  `id` smallint(6) NOT NULL DEFAULT '0',
  `public` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `aid_public`
--

INSERT INTO `aid_public` (`ordre_affichage`, `id`, `public`) VALUES
(3, 1, 'High-school pupils'),
(2, 2, 'schoolboy'),
(1, 3, 'Schoolboys'),
(6, 4, 'General public'),
(5, 5, 'Experts (or specialists)'),
(4, 6, 'Students');

-- --------------------------------------------------------

--
-- Structure de la table `archivage_aids`
--

CREATE TABLE IF NOT EXISTS `archivage_aids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annee` varchar(200) NOT NULL DEFAULT '',
  `nom` varchar(100) NOT NULL DEFAULT '',
  `id_type_aid` int(11) NOT NULL DEFAULT '0',
  `productions` varchar(100) NOT NULL DEFAULT '',
  `resume` text NOT NULL,
  `famille` smallint(6) NOT NULL DEFAULT '0',
  `mots_cles` text NOT NULL,
  `adresse1` varchar(255) NOT NULL DEFAULT '',
  `adresse2` varchar(255) NOT NULL DEFAULT '',
  `public_destinataire` varchar(50) NOT NULL DEFAULT '',
  `contacts` text NOT NULL,
  `divers` text NOT NULL,
  `matiere1` varchar(100) NOT NULL DEFAULT '',
  `matiere2` varchar(100) NOT NULL DEFAULT '',
  `fiche_publique` enum('y','n') NOT NULL DEFAULT 'n',
  `affiche_adresse1` enum('y','n') NOT NULL DEFAULT 'n',
  `en_construction` enum('y','n') NOT NULL DEFAULT 'n',
  `notes_moyenne` varchar(255) NOT NULL,
  `notes_min` varchar(255) NOT NULL,
  `notes_max` varchar(255) NOT NULL,
  `responsables` text NOT NULL,
  `eleves` text NOT NULL,
  `eleves_resp` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `archivage_aids`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_aid_eleve`
--

CREATE TABLE IF NOT EXISTS `archivage_aid_eleve` (
  `id_aid` int(11) NOT NULL DEFAULT '0',
  `id_eleve` varchar(255) NOT NULL,
  `eleve_resp` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id_aid`,`id_eleve`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `archivage_aid_eleve`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_appreciations_aid`
--

CREATE TABLE IF NOT EXISTS `archivage_appreciations_aid` (
  `id_eleve` varchar(255) NOT NULL,
  `annee` varchar(200) NOT NULL,
  `classe` varchar(255) NOT NULL,
  `id_aid` int(11) NOT NULL,
  `periode` int(11) NOT NULL DEFAULT '0',
  `appreciation` text NOT NULL,
  `note_eleve` varchar(50) NOT NULL,
  `note_moyenne_classe` varchar(255) NOT NULL,
  `note_min_classe` varchar(255) NOT NULL,
  `note_max_classe` varchar(255) NOT NULL,
  PRIMARY KEY (`id_eleve`,`id_aid`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `archivage_appreciations_aid`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_disciplines`
--

CREATE TABLE IF NOT EXISTS `archivage_disciplines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annee` varchar(200) NOT NULL,
  `INE` varchar(255) NOT NULL,
  `classe` varchar(255) NOT NULL,
  `num_periode` tinyint(4) NOT NULL,
  `nom_periode` varchar(255) NOT NULL,
  `special` varchar(255) NOT NULL,
  `matiere` varchar(255) NOT NULL,
  `prof` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `moymin` varchar(255) NOT NULL,
  `moymax` varchar(255) NOT NULL,
  `moyclasse` varchar(255) NOT NULL,
  `rang` tinyint(4) NOT NULL,
  `appreciation` text NOT NULL,
  `nb_absences` int(11) NOT NULL,
  `non_justifie` int(11) NOT NULL,
  `nb_retards` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `INE` (`INE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `archivage_disciplines`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_ects`
--

CREATE TABLE IF NOT EXISTS `archivage_ects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annee` varchar(255) NOT NULL COMMENT 'Annee scolaire',
  `ine` varchar(255) NOT NULL COMMENT 'Identifiant de l''eleve',
  `classe` varchar(255) NOT NULL COMMENT 'Classe de l''eleve',
  `num_periode` int(11) NOT NULL COMMENT 'Identifiant de la periode',
  `nom_periode` varchar(255) NOT NULL COMMENT 'Nom complet de la periode',
  `special` varchar(255) NOT NULL COMMENT 'Cle utilisee pour isoler certaines lignes (par exemple un credit ECTS pour une periode et non une matiere)',
  `matiere` varchar(255) DEFAULT NULL COMMENT 'Nom de l''enseignement',
  `profs` varchar(255) DEFAULT NULL COMMENT 'Liste des profs de l''enseignement',
  `valeur` decimal(10,0) NOT NULL COMMENT 'Nombre de crédits obtenus par l''eleve',
  `mention` varchar(255) NOT NULL COMMENT 'Mention obtenue',
  PRIMARY KEY (`id`,`ine`,`num_periode`,`special`),
  KEY `archivage_ects_FI_1` (`ine`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `archivage_ects`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_eleves`
--

CREATE TABLE IF NOT EXISTS `archivage_eleves` (
  `ine` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL DEFAULT '',
  `prenom` varchar(255) NOT NULL DEFAULT '',
  `sexe` char(1) NOT NULL,
  `naissance` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`ine`),
  KEY `nom` (`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `archivage_eleves`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_eleves2`
--

CREATE TABLE IF NOT EXISTS `archivage_eleves2` (
  `annee` varchar(50) NOT NULL DEFAULT '',
  `ine` varchar(50) NOT NULL,
  `doublant` enum('-','R') NOT NULL DEFAULT '-',
  `regime` varchar(255) NOT NULL,
  PRIMARY KEY (`ine`,`annee`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `archivage_eleves2`
--


-- --------------------------------------------------------

--
-- Structure de la table `archivage_types_aid`
--

CREATE TABLE IF NOT EXISTS `archivage_types_aid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annee` varchar(200) NOT NULL DEFAULT '',
  `nom` varchar(100) NOT NULL DEFAULT '',
  `nom_complet` varchar(100) NOT NULL DEFAULT '',
  `note_sur` int(11) NOT NULL DEFAULT '0',
  `type_note` varchar(5) NOT NULL DEFAULT '',
  `display_bulletin` char(1) NOT NULL DEFAULT 'y',
  `outils_complementaires` enum('y','n') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `archivage_types_aid`
--


-- --------------------------------------------------------

--
-- Structure de la table `ateliers_config`
--

CREATE TABLE IF NOT EXISTS `ateliers_config` (
  `nom_champ` char(100) NOT NULL DEFAULT '',
  `content` char(255) NOT NULL DEFAULT '',
  `param` char(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `ateliers_config`
--


-- --------------------------------------------------------

--
-- Structure de la table `avis_conseil_classe`
--

CREATE TABLE IF NOT EXISTS `avis_conseil_classe` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `periode` int(11) NOT NULL DEFAULT '0',
  `avis` text NOT NULL,
  `id_mention` int(11) NOT NULL DEFAULT '0',
  `statut` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`login`,`periode`),
  KEY `login` (`login`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `avis_conseil_classe`
--


-- --------------------------------------------------------

--
-- Structure de la table `a_agregation_decompte`
--

CREATE TABLE IF NOT EXISTS `a_agregation_decompte` (
  `eleve_id` int(11) NOT NULL COMMENT 'id de l''eleve',
  `date_demi_jounee` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date de la demi journ?e agr?g?e : 00:00 pour une matin?e, 12:00 pour une apr?s midi',
  `manquement_obligation_presence` tinyint(4) DEFAULT '0' COMMENT 'Cette demi journ?e est compt?e comme absence',
  `justifiee` tinyint(4) DEFAULT '0' COMMENT 'Si cette demi journ?e est compt? comme absence, y a-t-il une justification',
  `notifiee` tinyint(4) DEFAULT '0' COMMENT 'Si cette demi journ?e est compt? comme absence, y a-t-il une notification ? la famille',
  `nb_retards` int(11) DEFAULT '0' COMMENT 'Nombre de retards d?compt?s dans la demi journ?e',
  `nb_retards_justifies` int(11) DEFAULT '0' COMMENT 'Nombre de retards justifi?s d?compt?s dans la demi journ?e',
  `motifs_absences` text COMMENT 'Liste des motifs (table a_motifs) associ?s ? cette demi-journ?e d''absence',
  `motifs_retards` text COMMENT 'Liste des motifs (table a_motifs) associ?s aux retard de cette demi-journ?e',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`eleve_id`,`date_demi_jounee`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table d''agregation des decomptes de demi journees d''absence ';

--
-- Contenu de la table `a_agregation_decompte`
--


-- --------------------------------------------------------

--
-- Structure de la table `a_justifications`
--

CREATE TABLE IF NOT EXISTS `a_justifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
  `nom` varchar(250) NOT NULL COMMENT 'Nom de la justification',
  `commentaire` text COMMENT 'commentaire saisi par l''utilisateur',
  `sortable_rank` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Liste des justifications possibles pour une absence' AUTO_INCREMENT=4 ;

--
-- Contenu de la table `a_justifications`
--

INSERT INTO `a_justifications` (`id`, `nom`, `commentaire`, `sortable_rank`, `created_at`, `updated_at`) VALUES
(1, 'Medical certificate', ' A justification established by a medical authority', 1, NULL, NULL),
(2, 'Family mail', 'Justification by mail of the family', 2, NULL, NULL),
(3, 'Document in proof of a public administration', 'Justification emitted by a public administration', 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `a_lieux`
--

CREATE TABLE IF NOT EXISTS `a_lieux` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
  `nom` varchar(250) NOT NULL COMMENT 'Nom du lieu',
  `commentaire` text COMMENT 'commentaire saisi par l''utilisateur',
  `sortable_rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Lieu pour les types d''absence ou les saisies' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `a_lieux`
--


-- --------------------------------------------------------

--
-- Structure de la table `a_motifs`
--

CREATE TABLE IF NOT EXISTS `a_motifs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
  `nom` varchar(250) NOT NULL COMMENT 'Nom du motif',
  `commentaire` text COMMENT 'commentaire saisi par l''utilisateur',
  `sortable_rank` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Liste des motifs possibles pour une absence' AUTO_INCREMENT=4 ;

--
-- Contenu de la table `a_motifs`
--

INSERT INTO `a_motifs` (`id`, `nom`, `commentaire`, `sortable_rank`, `created_at`, `updated_at`) VALUES
(1, 'Médical', 'The student is absent for medical reason', 1, NULL, NULL),
(2, 'Familial', 'The student is absent for family reason', 2, NULL, NULL),
(3, 'Sportive', 'The student is absent due to sporting competition', 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `a_notifications`
--

CREATE TABLE IF NOT EXISTS `a_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui envoi la notification',
  `a_traitement_id` int(12) NOT NULL COMMENT 'cle etrangere du traitement qu''on notifie',
  `type_notification` int(5) DEFAULT NULL COMMENT 'type de notification (0 : email, 1 : courrier, 2 : sms)',
  `email` varchar(100) DEFAULT NULL COMMENT 'email de destination (pour le type email)',
  `telephone` varchar(100) DEFAULT NULL COMMENT 'numero du telephone de destination (pour le type sms)',
  `adr_id` varchar(10) DEFAULT NULL COMMENT 'cle etrangere vers l''adresse de destination (pour le type courrier)',
  `commentaire` text COMMENT 'commentaire saisi par l''utilisateur',
  `statut_envoi` int(5) DEFAULT '0' COMMENT 'Statut de cet envoi (0 : etat initial, 1 : en cours, 2 : echec, 3 : succes, 4 : succes avec accuse de reception)',
  `date_envoi` datetime DEFAULT NULL COMMENT 'Date envoi',
  `erreur_message_envoi` text COMMENT 'Message d''erreur retourn? par le service d''envoi',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `a_notifications_FI_1` (`utilisateur_id`),
  KEY `a_notifications_FI_2` (`a_traitement_id`),
  KEY `a_notifications_FI_3` (`adr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Notification (a la famille) des absences' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `a_notifications`
--


-- --------------------------------------------------------

--
-- Structure de la table `a_saisies`
--

CREATE TABLE IF NOT EXISTS `a_saisies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a saisi l''absence',
  `eleve_id` int(11) DEFAULT NULL COMMENT 'id_eleve de l''eleve objet de la saisie, egal ? null si aucun eleve n''est saisi',
  `commentaire` text COMMENT 'commentaire de l''utilisateur',
  `debut_abs` datetime DEFAULT NULL COMMENT 'Debut de l''absence en timestamp UNIX',
  `fin_abs` datetime DEFAULT NULL COMMENT 'Fin de l''absence en timestamp UNIX',
  `id_edt_creneau` int(12) DEFAULT NULL COMMENT 'identifiant du creneaux de l''emploi du temps',
  `id_edt_emplacement_cours` int(12) DEFAULT NULL COMMENT 'identifiant du cours de l''emploi du temps',
  `id_groupe` int(11) DEFAULT NULL COMMENT 'identifiant du groupe pour lequel la saisie a ete effectuee',
  `id_classe` int(11) DEFAULT NULL COMMENT 'identifiant de la classe pour lequel la saisie a ete effectuee',
  `id_aid` int(11) DEFAULT NULL COMMENT 'identifiant de l''aid pour lequel la saisie a ete effectuee',
  `id_s_incidents` int(11) DEFAULT NULL COMMENT 'identifiant de la saisie d''incident discipline',
  `id_lieu` int(11) DEFAULT NULL COMMENT 'cle etrangere du lieu ou se trouve l''eleve',
  `deleted_by` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a supprim? la saisie',
  `created_at` datetime DEFAULT NULL COMMENT 'Date de creation de la saisie',
  `updated_at` datetime DEFAULT NULL COMMENT 'Date de modification de la saisie, y compris suppression, restauration et changement de version',
  `deleted_at` datetime DEFAULT NULL,
  `version` int(11) DEFAULT '0',
  `version_created_at` datetime DEFAULT NULL,
  `version_created_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `a_saisies_I_1` (`deleted_at`),
  KEY `a_saisies_I_2` (`debut_abs`),
  KEY `a_saisies_I_3` (`fin_abs`),
  KEY `a_saisies_FI_1` (`utilisateur_id`),
  KEY `a_saisies_FI_2` (`eleve_id`),
  KEY `a_saisies_FI_3` (`id_edt_creneau`),
  KEY `a_saisies_FI_4` (`id_edt_emplacement_cours`),
  KEY `a_saisies_FI_5` (`id_groupe`),
  KEY `a_saisies_FI_6` (`id_classe`),
  KEY `a_saisies_FI_7` (`id_aid`),
  KEY `a_saisies_FI_8` (`id_lieu`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Chaque saisie d''absence doit faire l''objet d''une ligne dans ' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `a_saisies`
--


-- --------------------------------------------------------

--
-- Structure de la table `a_saisies_version`
--

CREATE TABLE IF NOT EXISTS `a_saisies_version` (
  `id` int(11) NOT NULL,
  `utilisateur_id` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a saisi l''absence',
  `eleve_id` int(11) DEFAULT NULL COMMENT 'id_eleve de l''eleve objet de la saisie, egal ? null si aucun eleve n''est saisi',
  `commentaire` text COMMENT 'commentaire de l''utilisateur',
  `debut_abs` datetime DEFAULT NULL COMMENT 'Debut de l''absence en timestamp UNIX',
  `fin_abs` datetime DEFAULT NULL COMMENT 'Fin de l''absence en timestamp UNIX',
  `id_edt_creneau` int(12) DEFAULT NULL COMMENT 'identifiant du creneaux de l''emploi du temps',
  `id_edt_emplacement_cours` int(12) DEFAULT NULL COMMENT 'identifiant du cours de l''emploi du temps',
  `id_groupe` int(11) DEFAULT NULL COMMENT 'identifiant du groupe pour lequel la saisie a ete effectuee',
  `id_classe` int(11) DEFAULT NULL COMMENT 'identifiant de la classe pour lequel la saisie a ete effectuee',
  `id_aid` int(11) DEFAULT NULL COMMENT 'identifiant de l''aid pour lequel la saisie a ete effectuee',
  `id_s_incidents` int(11) DEFAULT NULL COMMENT 'identifiant de la saisie d''incident discipline',
  `id_lieu` int(11) DEFAULT NULL COMMENT 'cle etrangere du lieu ou se trouve l''eleve',
  `deleted_by` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a supprim? la saisie',
  `created_at` datetime DEFAULT NULL COMMENT 'Date de creation de la saisie',
  `updated_at` datetime DEFAULT NULL COMMENT 'Date de modification de la saisie, y compris suppression, restauration et changement de version',
  `deleted_at` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  `version_created_at` datetime DEFAULT NULL,
  `version_created_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`,`version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `a_saisies_version`
--


-- --------------------------------------------------------

--
-- Structure de la table `a_traitements`
--

CREATE TABLE IF NOT EXISTS `a_traitements` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
  `utilisateur_id` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a fait le traitement',
  `a_type_id` int(4) DEFAULT NULL COMMENT 'cle etrangere du type d''absence',
  `a_motif_id` int(4) DEFAULT NULL COMMENT 'cle etrangere du motif d''absence',
  `a_justification_id` int(4) DEFAULT NULL COMMENT 'cle etrangere de la justification de l''absence',
  `commentaire` text COMMENT 'commentaire saisi par l''utilisateur',
  `modifie_par_utilisateur_id` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a modifie en dernier le traitement',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `a_traitements_I_1` (`deleted_at`),
  KEY `a_traitements_FI_1` (`utilisateur_id`),
  KEY `a_traitements_FI_2` (`a_type_id`),
  KEY `a_traitements_FI_3` (`a_motif_id`),
  KEY `a_traitements_FI_4` (`a_justification_id`),
  KEY `a_traitements_FI_5` (`modifie_par_utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Un traitement peut gerer plusieurs saisies et consiste ? def' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `a_traitements`
--


-- --------------------------------------------------------

--
-- Structure de la table `a_types`
--

CREATE TABLE IF NOT EXISTS `a_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
  `nom` varchar(250) NOT NULL COMMENT 'Nom du type d''absence',
  `justification_exigible` tinyint(4) DEFAULT NULL COMMENT 'Ce type d''absence doit entrainer une justification de la part de la famille',
  `sous_responsabilite_etablissement` varchar(255) DEFAULT 'NON_PRECISE' COMMENT 'L''eleve est sous la responsabilite de l''etablissement. Typiquement : absence infirmerie, mettre la propri?t? ? vrai car l''eleve est encore sous la responsabilit? de l''etablissement. Possibilite : ''vrai''/''faux''/''non_precise''',
  `manquement_obligation_presence` varchar(50) DEFAULT 'NON_PRECISE' COMMENT 'L''eleve manque ? ses obligations de presence (L''absence apparait sur le bulletin). Possibilite : ''vrai''/''faux''/''non_precise''',
  `retard_bulletin` varchar(50) DEFAULT 'NON_PRECISE' COMMENT 'La saisie est comptabilis?e dans le bulletin en tant que retard. Possibilite : ''vrai''/''faux''/''non_precise''',
  `type_saisie` varchar(50) DEFAULT 'NON_PRECISE' COMMENT 'Enumeration des possibilit?s de l''interface de saisie de l''absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE',
  `commentaire` text COMMENT 'commentaire saisi par l''utilisateur',
  `id_lieu` int(11) DEFAULT NULL COMMENT 'cle etrangere du lieu ou se trouve l''?l?ve',
  `sortable_rank` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `a_types_FI_1` (`id_lieu`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Liste des types d''absences possibles dans l''etablissement' AUTO_INCREMENT=14 ;

--
-- Contenu de la table `a_types`
--

INSERT INTO `a_types` (`id`, `nom`, `justification_exigible`, `sous_responsabilite_etablissement`, `manquement_obligation_presence`, `retard_bulletin`, `type_saisie`, `commentaire`, `id_lieu`, `sortable_rank`, `created_at`, `updated_at`) VALUES
(1, 'School absence', 1, 'FAUX', 'VRAI', 'NON_PRECISE', 'NON_PRECISE', 'The student is not present to follow its schooling.', NULL, 1, NULL, NULL),
(2, 'Intercours delay ', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'The student is late during intercours', NULL, 2, NULL, NULL),
(3, 'External delay', 0, 'FAUX', 'VRAI', 'VRAI', 'NON_PRECISE', 'The student is late during its arrival in the school', NULL, 3, NULL, NULL),
(4, 'Error of typing', 0, 'NON_PRECISE', 'NON_PRECISE', 'NON_PRECISE', 'NON_PRECISE', 'There is probably an error of typing on this recording.', NULL, 4, NULL, NULL),
(5, 'Infirmary', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', ' the student is at the infirmary.', NULL, 5, NULL, NULL),
(6, 'School outing', 0, '1', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'The student is at school outing.', NULL, 6, NULL, NULL),
(7, 'Exclusion of the school', 0, 'FAUX', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'The student is excluded from the school.', NULL, 7, NULL, NULL),
(8, 'Exclusion/inclusion', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'The student is excluded but present within the school.', NULL, 8, NULL, NULL),
(9, 'Exclusion of course', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'DISCIPLINE', 'The student is excluded from course.', NULL, 9, NULL, NULL),
(10, 'Exempt (student present)', 1, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'The student is exempted but present physically during meeting.', NULL, 10, NULL, NULL),
(11, 'Exempt (student not present)', 1, 'FAUX', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', ' the student is exempted and not present physically during meeting.', NULL, 11, NULL, NULL),
(12, 'Training course', 0, 'FAUX', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'The student is in training course outside the school.', NULL, 12, NULL, NULL),
(13, 'Present', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'The student is present.', NULL, 13, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `a_types_statut`
--

CREATE TABLE IF NOT EXISTS `a_types_statut` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
  `id_a_type` int(11) NOT NULL COMMENT 'Cle etrangere de la table a_type',
  `statut` varchar(20) NOT NULL COMMENT 'Statut de l''utilisateur',
  PRIMARY KEY (`id`),
  KEY `a_types_statut_FI_1` (`id_a_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Liste des statuts autorises ? saisir des types d''absences' AUTO_INCREMENT=40 ;

--
-- Contenu de la table `a_types_statut`
--

INSERT INTO `a_types_statut` (`id`, `id_a_type`, `statut`) VALUES
(1, 1, 'professeur'),
(2, 1, 'cpe'),
(3, 1, 'scolarite'),
(4, 1, 'autre'),
(5, 2, 'professeur'),
(6, 2, 'cpe'),
(7, 2, 'scolarite'),
(8, 2, 'autre'),
(9, 3, 'cpe'),
(10, 3, 'scolarite'),
(11, 3, 'autre'),
(12, 4, 'professeur'),
(13, 4, 'cpe'),
(14, 4, 'scolarite'),
(15, 4, 'autre'),
(16, 5, 'professeur'),
(17, 5, 'cpe'),
(18, 5, 'scolarite'),
(19, 5, 'autre'),
(20, 6, 'professeur'),
(21, 6, 'cpe'),
(22, 6, 'scolarite'),
(23, 7, 'cpe'),
(24, 7, 'scolarite'),
(25, 8, 'cpe'),
(26, 8, 'scolarite'),
(27, 9, 'professeur'),
(28, 9, 'cpe'),
(29, 9, 'scolarite'),
(30, 10, 'cpe'),
(31, 10, 'scolarite'),
(32, 11, 'cpe'),
(33, 11, 'scolarite'),
(34, 12, 'cpe'),
(35, 12, 'scolarite'),
(36, 13, 'professeur'),
(37, 13, 'cpe'),
(38, 13, 'scolarite'),
(39, 13, 'autre');

-- --------------------------------------------------------

--
-- Structure de la table `cc_dev`
--

CREATE TABLE IF NOT EXISTS `cc_dev` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cn_dev` int(11) NOT NULL DEFAULT '0',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `nom_court` varchar(32) NOT NULL DEFAULT '',
  `nom_complet` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(128) NOT NULL DEFAULT '',
  `arrondir` char(2) NOT NULL DEFAULT 's1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `cc_dev`
--


-- --------------------------------------------------------

--
-- Structure de la table `cc_eval`
--

CREATE TABLE IF NOT EXISTS `cc_eval` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dev` int(11) NOT NULL DEFAULT '0',
  `nom_court` varchar(32) NOT NULL DEFAULT '',
  `nom_complet` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(128) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `note_sur` int(11) DEFAULT '5',
  PRIMARY KEY (`id`),
  KEY `dev_date` (`id_dev`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `cc_eval`
--


-- --------------------------------------------------------

--
-- Structure de la table `cc_notes_eval`
--

CREATE TABLE IF NOT EXISTS `cc_notes_eval` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_eval` int(11) NOT NULL DEFAULT '0',
  `note` float(10,1) NOT NULL DEFAULT '0.0',
  `statut` char(1) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  PRIMARY KEY (`login`,`id_eval`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `cc_notes_eval`
--


-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE IF NOT EXISTS `classes` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `classe` varchar(100) NOT NULL DEFAULT '',
  `nom_complet` varchar(100) NOT NULL DEFAULT '',
  `suivi_par` varchar(50) NOT NULL DEFAULT '',
  `formule` varchar(100) NOT NULL DEFAULT '',
  `format_nom` varchar(5) NOT NULL DEFAULT '',
  `display_rang` char(1) NOT NULL DEFAULT 'n',
  `display_address` char(1) NOT NULL DEFAULT 'n',
  `display_coef` char(1) NOT NULL DEFAULT 'y',
  `display_mat_cat` char(1) NOT NULL DEFAULT 'n',
  `display_nbdev` char(1) NOT NULL DEFAULT 'n',
  `display_moy_gen` char(1) NOT NULL DEFAULT 'y',
  `modele_bulletin_pdf` varchar(255) DEFAULT NULL,
  `rn_nomdev` char(1) NOT NULL DEFAULT 'n',
  `rn_toutcoefdev` char(1) NOT NULL DEFAULT 'n',
  `rn_coefdev_si_diff` char(1) NOT NULL DEFAULT 'n',
  `rn_datedev` char(1) NOT NULL DEFAULT 'n',
  `rn_sign_chefetab` char(1) NOT NULL DEFAULT 'n',
  `rn_sign_pp` char(1) NOT NULL DEFAULT 'n',
  `rn_sign_resp` char(1) NOT NULL DEFAULT 'n',
  `rn_sign_nblig` int(11) NOT NULL DEFAULT '3',
  `rn_formule` text NOT NULL,
  `ects_type_formation` varchar(255) NOT NULL DEFAULT '',
  `ects_parcours` varchar(255) NOT NULL DEFAULT '',
  `ects_code_parcours` varchar(255) NOT NULL DEFAULT '',
  `ects_domaines_etude` varchar(255) NOT NULL DEFAULT '',
  `ects_fonction_signataire_attestation` varchar(255) NOT NULL DEFAULT '',
  `apb_niveau` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `classe` (`classe`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `classes`
--

INSERT INTO `classes` (`id`, `classe`, `nom_complet`, `suivi_par`, `formule`, `format_nom`, `display_rang`, `display_address`, `display_coef`, `display_mat_cat`, `display_nbdev`, `display_moy_gen`, `modele_bulletin_pdf`, `rn_nomdev`, `rn_toutcoefdev`, `rn_coefdev_si_diff`, `rn_datedev`, `rn_sign_chefetab`, `rn_sign_pp`, `rn_sign_resp`, `rn_sign_nblig`, `rn_formule`, `ects_type_formation`, `ects_parcours`, `ects_code_parcours`, `ects_domaines_etude`, `ects_fonction_signataire_attestation`, `apb_niveau`) VALUES
(1, 'F1A', 'Form 1A', 'John Doe The Principal', '', 'cnp', 'y', 'n', 'y', 'n', 'n', 'y', '1', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 3, '', '', '', '', '', 'Principal', ''),
(2, 'F1B', 'Form 1B', 'FR. PETER NOUCK', '', 'cni', 'y', 'y', 'y', 'y', 'n', 'n', '1', 'n', 'y', 'y', 'n', 'y', 'n', 'n', 3, '', '', '', '', '', 'Reverant', '');

-- --------------------------------------------------------

--
-- Structure de la table `cn_cahier_notes`
--

CREATE TABLE IF NOT EXISTS `cn_cahier_notes` (
  `id_cahier_notes` int(11) NOT NULL AUTO_INCREMENT,
  `id_groupe` int(11) NOT NULL,
  `periode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_cahier_notes`,`id_groupe`,`periode`),
  KEY `groupe_periode` (`id_groupe`,`periode`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `cn_cahier_notes`
--

INSERT INTO `cn_cahier_notes` (`id_cahier_notes`, `id_groupe`, `periode`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `cn_conteneurs`
--

CREATE TABLE IF NOT EXISTS `cn_conteneurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_racine` int(11) NOT NULL DEFAULT '0',
  `nom_court` varchar(32) NOT NULL DEFAULT '',
  `nom_complet` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(128) NOT NULL DEFAULT '',
  `mode` char(1) NOT NULL DEFAULT '2',
  `coef` decimal(3,1) NOT NULL DEFAULT '1.0',
  `arrondir` char(2) NOT NULL DEFAULT 's1',
  `ponderation` decimal(3,1) NOT NULL DEFAULT '0.0',
  `display_parents` char(1) NOT NULL DEFAULT '0',
  `display_bulletin` char(1) NOT NULL DEFAULT '1',
  `parent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_racine` (`parent`,`id_racine`),
  KEY `racine_bulletin` (`id_racine`,`display_bulletin`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `cn_conteneurs`
--

INSERT INTO `cn_conteneurs` (`id`, `id_racine`, `nom_court`, `nom_complet`, `description`, `mode`, `coef`, `arrondir`, `ponderation`, `display_parents`, `display_bulletin`, `parent`) VALUES
(1, 1, 'Maths.', 'Maths.', 'Sequence 1', '2', '0.0', 's1', '5.0', '0', '1', 0);

-- --------------------------------------------------------

--
-- Structure de la table `cn_devoirs`
--

CREATE TABLE IF NOT EXISTS `cn_devoirs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_conteneur` int(11) NOT NULL DEFAULT '0',
  `id_racine` int(11) NOT NULL DEFAULT '0',
  `nom_court` varchar(32) NOT NULL DEFAULT '',
  `nom_complet` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(128) NOT NULL DEFAULT '',
  `facultatif` char(1) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `coef` decimal(3,1) NOT NULL DEFAULT '0.0',
  `note_sur` int(11) DEFAULT '20',
  `ramener_sur_referentiel` char(1) NOT NULL DEFAULT 'F',
  `display_parents` char(1) NOT NULL DEFAULT '',
  `display_parents_app` char(1) NOT NULL DEFAULT '0',
  `date_ele_resp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `conteneur_date` (`id_conteneur`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `cn_devoirs`
--


-- --------------------------------------------------------

--
-- Structure de la table `cn_notes_conteneurs`
--

CREATE TABLE IF NOT EXISTS `cn_notes_conteneurs` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_conteneur` int(11) NOT NULL DEFAULT '0',
  `note` float(10,1) NOT NULL DEFAULT '0.0',
  `statut` char(1) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  PRIMARY KEY (`login`,`id_conteneur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `cn_notes_conteneurs`
--

INSERT INTO `cn_notes_conteneurs` (`login`, `id_conteneur`, `note`, `statut`, `comment`) VALUES
('achah', 1, 0.0, '', ''),
('agbor', 1, 0.0, '', ''),
('ajong', 1, 0.0, '', ''),
('anaka', 1, 0.0, '', ''),
('apah', 1, 0.0, '', ''),
('atanga', 1, 0.0, '', ''),
('atem', 1, 0.0, '', ''),
('etengene', 1, 0.0, '', ''),
('ayuk', 1, 0.0, '', ''),
('babo', 1, 0.0, '', ''),
('baiye', 1, 0.0, '', ''),
('bende', 1, 0.0, '', ''),
('besong', 1, 0.0, '', ''),
('bessong', 1, 0.0, '', ''),
('bounoung', 1, 0.0, '', ''),
('jesse', 1, 0.0, '', ''),
('chifu', 1, 0.0, '', ''),
('djou', 1, 0.0, '', ''),
('dobgima', 1, 0.0, '', ''),
('nlend', 1, 0.0, '', ''),
('masango', 1, 0.0, '', ''),
('ekangwo', 1, 0.0, '', ''),
('elando', 1, 0.0, '', ''),
('elonge', 1, 0.0, '', ''),
('emoh', 1, 0.0, '', ''),
('enowebai', 1, 0.0, '', ''),
('epey', 1, 0.0, '', ''),
('foadjo', 1, 0.0, '', ''),
('fokou', 1, 0.0, '', ''),
('honge', 1, 0.0, '', ''),
('kameni', 1, 0.0, '', ''),
('kande', 1, 0.0, '', ''),
('kemajou', 1, 0.0, '', ''),
('kemne', 1, 0.0, '', ''),
('kenne', 1, 0.0, '', ''),
('kome', 1, 0.0, '', ''),
('kounchou', 1, 0.0, '', ''),
('lobe', 1, 0.0, '', ''),
('lytombe', 1, 0.0, '', ''),
('manyaka', 1, 0.0, '', ''),
('mbah', 1, 0.0, '', ''),
('mbi', 1, 0.0, '', ''),
('ndip', 1, 0.0, '', ''),
('mbu', 1, 0.0, '', ''),
('mekeme', 1, 0.0, '', ''),
('mende', 1, 0.0, '', ''),
('metuge', 1, 0.0, '', ''),
('mieguim', 1, 0.0, '', ''),
('mocto', 1, 0.0, '', ''),
('mokube', 1, 0.0, '', ''),
('mokwe', 1, 0.0, '', ''),
('nchotieh', 1, 0.0, '', ''),
('nekongoh', 1, 0.0, '', ''),
('ngaha', 1, 0.0, '', ''),
('ngalame', 1, 0.0, '', ''),
('ngando', 1, 0.0, '', ''),
('ngangmi', 1, 0.0, '', ''),
('nganjo', 1, 0.0, '', ''),
('ngoe', 1, 0.0, '', ''),
('ngouh', 1, 0.0, '', ''),
('ngounou', 1, 0.0, '', ''),
('ngulefac', 1, 0.0, '', ''),
('njabe', 1, 0.0, '', ''),
('njoya', 1, 0.0, '', ''),
('njume', 1, 0.0, '', ''),
('nkemlebe', 1, 0.0, '', ''),
('etaka', 1, 0.0, '', ''),
('mezation', 1, 0.0, '', ''),
('ntung', 1, 0.0, '', ''),
('nwanja', 1, 0.0, '', ''),
('nyingcho', 1, 0.0, '', ''),
('nyoki', 1, 0.0, '', ''),
('obenan', 1, 0.0, '', ''),
('obidimma', 1, 0.0, '', ''),
('sama', 1, 0.0, '', ''),
('siyapze', 1, 0.0, '', ''),
('sone', 1, 0.0, '', ''),
('amuruwa', 1, 0.0, '', ''),
('tabe', 1, 0.0, '', ''),
('tabot', 1, 0.0, '', ''),
('takang', 1, 0.0, '', ''),
('tamon', 1, 0.0, '', ''),
('tassoko', 1, 0.0, '', ''),
('tchamy', 1, 0.0, '', ''),
('tchangou', 1, 0.0, '', ''),
('teghoue', 1, 0.0, '', ''),
('tita', 1, 0.0, '', ''),
('ukatang', 1, 0.0, '', ''),
('veke', 1, 0.0, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `cn_notes_devoirs`
--

CREATE TABLE IF NOT EXISTS `cn_notes_devoirs` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_devoir` int(11) NOT NULL DEFAULT '0',
  `note` float(10,1) NOT NULL DEFAULT '0.0',
  `comment` text NOT NULL,
  `statut` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`login`,`id_devoir`),
  KEY `devoir_statut` (`id_devoir`,`statut`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `cn_notes_devoirs`
--


-- --------------------------------------------------------

--
-- Structure de la table `commentaires_types`
--

CREATE TABLE IF NOT EXISTS `commentaires_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commentaire` text NOT NULL,
  `num_periode` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `commentaires_types`
--


-- --------------------------------------------------------

--
-- Structure de la table `commentaires_types_profs`
--

CREATE TABLE IF NOT EXISTS `commentaires_types_profs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `app` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `commentaires_types_profs`
--


-- --------------------------------------------------------

--
-- Structure de la table `communes`
--

CREATE TABLE IF NOT EXISTS `communes` (
  `code_commune_insee` varchar(50) NOT NULL,
  `departement` varchar(50) NOT NULL,
  `commune` varchar(255) NOT NULL,
  PRIMARY KEY (`code_commune_insee`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `communes`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_devoirs_documents`
--

CREATE TABLE IF NOT EXISTS `ct_devoirs_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ct_devoir` int(11) NOT NULL DEFAULT '0',
  `titre` varchar(255) NOT NULL DEFAULT '',
  `taille` int(11) NOT NULL DEFAULT '0',
  `emplacement` varchar(255) NOT NULL DEFAULT '',
  `visible_eleve_parent` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ct_devoirs_documents`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_devoirs_entry`
--

CREATE TABLE IF NOT EXISTS `ct_devoirs_entry` (
  `id_ct` int(11) NOT NULL AUTO_INCREMENT,
  `id_groupe` int(11) NOT NULL,
  `date_ct` int(11) NOT NULL DEFAULT '0',
  `id_login` varchar(32) NOT NULL DEFAULT '',
  `id_sequence` int(11) NOT NULL DEFAULT '0',
  `contenu` text NOT NULL,
  `vise` char(1) NOT NULL DEFAULT 'n',
  `date_visibilite_eleve` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp précisant quand les devoirs sont portés ?  la conaissance des élèves',
  PRIMARY KEY (`id_ct`),
  KEY `id_groupe` (`id_groupe`),
  KEY `groupe_date` (`id_groupe`,`date_ct`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ct_devoirs_entry`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_documents`
--

CREATE TABLE IF NOT EXISTS `ct_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ct` int(11) NOT NULL DEFAULT '0',
  `titre` varchar(255) NOT NULL DEFAULT '',
  `taille` int(11) NOT NULL DEFAULT '0',
  `emplacement` varchar(255) NOT NULL DEFAULT '',
  `visible_eleve_parent` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ct_documents`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_entry`
--

CREATE TABLE IF NOT EXISTS `ct_entry` (
  `id_ct` int(11) NOT NULL AUTO_INCREMENT,
  `heure_entry` time NOT NULL DEFAULT '00:00:00',
  `id_groupe` int(11) NOT NULL,
  `date_ct` int(11) NOT NULL DEFAULT '0',
  `id_login` varchar(32) NOT NULL DEFAULT '',
  `id_sequence` int(11) NOT NULL DEFAULT '0',
  `contenu` text NOT NULL,
  `vise` char(1) NOT NULL DEFAULT 'n',
  `visa` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id_ct`),
  KEY `id_groupe` (`id_groupe`),
  KEY `id_date_heure` (`id_groupe`,`date_ct`,`heure_entry`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `ct_entry`
--

INSERT INTO `ct_entry` (`id_ct`, `heure_entry`, `id_groupe`, `date_ct`, `id_login`, `id_sequence`, `contenu`, `vise`, `visa`) VALUES
(1, '08:42:00', 1, 1172793600, 'elijah', 0, '<p>Today we have studied all purpose of functions in Mathematics </p>', 'n', 'n'),
(2, '10:15:00', 1, 1172793600, 'elijah', 0, '<p>Trigo </p>', 'n', 'n'),
(3, '14:21:00', 1, 1172880000, 'elijah', 0, '<p>Mathematics Arithmetik</p>', 'n', 'n');

-- --------------------------------------------------------

--
-- Structure de la table `ct_private_entry`
--

CREATE TABLE IF NOT EXISTS `ct_private_entry` (
  `id_ct` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire de la cotice privee',
  `heure_entry` time NOT NULL DEFAULT '00:00:00' COMMENT 'heure de l''entree',
  `date_ct` int(11) NOT NULL DEFAULT '0' COMMENT 'date du compte rendu',
  `contenu` text NOT NULL COMMENT 'contenu redactionnel du compte rendu',
  `id_groupe` int(11) NOT NULL COMMENT 'Cle etrangere du groupe auquel appartient le compte rendu',
  `id_login` varchar(32) DEFAULT NULL COMMENT 'Cle etrangere de l''utilisateur auquel appartient le compte rendu',
  `id_sequence` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_ct`),
  KEY `ct_private_entry_FI_1` (`id_groupe`),
  KEY `ct_private_entry_FI_2` (`id_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Notice privee du cahier de texte' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ct_private_entry`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_sequences`
--

CREATE TABLE IF NOT EXISTS `ct_sequences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ct_sequences`
--


-- --------------------------------------------------------

--
-- Structure de la table `ct_types_documents`
--

CREATE TABLE IF NOT EXISTS `ct_types_documents` (
  `id_type` bigint(21) NOT NULL AUTO_INCREMENT,
  `titre` text NOT NULL,
  `extension` varchar(10) NOT NULL DEFAULT '',
  `upload` enum('oui','non') NOT NULL DEFAULT 'oui',
  PRIMARY KEY (`id_type`),
  UNIQUE KEY `extension` (`extension`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

--
-- Contenu de la table `ct_types_documents`
--

INSERT INTO `ct_types_documents` (`id_type`, `titre`, `extension`, `upload`) VALUES
(1, 'JPEG', 'jpg', 'oui'),
(2, 'PNG', 'png', 'oui'),
(3, 'GIF', 'gif', 'oui'),
(4, 'BMP', 'bmp', 'oui'),
(5, 'Photoshop', 'psd', 'oui'),
(6, 'TIFF', 'tif', 'oui'),
(7, 'AIFF', 'aiff', 'oui'),
(8, 'Windows Media', 'asf', 'oui'),
(9, 'Windows Media', 'avi', 'oui'),
(10, 'Midi', 'mid', 'oui'),
(12, 'QuickTime', 'mov', 'oui'),
(13, 'MP3', 'mp3', 'oui'),
(14, 'MPEG', 'mpg', 'oui'),
(15, 'Ogg', 'ogg', 'oui'),
(16, 'QuickTime', 'qt', 'oui'),
(17, 'RealAudio', 'ra', 'oui'),
(18, 'RealAudio', 'ram', 'oui'),
(19, 'RealAudio', 'rm', 'oui'),
(20, 'Flash', 'swf', 'oui'),
(21, 'WAV', 'wav', 'oui'),
(22, 'Windows Media', 'wmv', 'oui'),
(23, 'Adobe Illustrator', 'ai', 'oui'),
(24, 'BZip', 'bz2', 'oui'),
(25, 'C source', 'c', 'oui'),
(26, 'Debian', 'deb', 'oui'),
(27, 'Word', 'doc', 'oui'),
(29, 'LaTeX DVI', 'dvi', 'oui'),
(30, 'PostScript', 'eps', 'oui'),
(31, 'GZ', 'gz', 'oui'),
(32, 'C header', 'h', 'oui'),
(33, 'HTML', 'html', 'oui'),
(34, 'Pascal', 'pas', 'oui'),
(35, 'PDF', 'pdf', 'oui'),
(36, 'PowerPoint', 'ppt', 'oui'),
(37, 'PostScript', 'ps', 'oui'),
(38, 'gr', 'gr', 'oui'),
(39, 'RTF', 'rtf', 'oui'),
(40, 'StarOffice', 'sdd', 'oui'),
(41, 'StarOffice', 'sdw', 'oui'),
(42, 'Stuffit', 'sit', 'oui'),
(43, 'OpenOffice Calc', 'sxc', 'oui'),
(44, 'OpenOffice Impress', 'sxi', 'oui'),
(45, 'OpenOffice', 'sxw', 'oui'),
(46, 'LaTeX', 'tex', 'oui'),
(47, 'TGZ', 'tgz', 'oui'),
(48, 'texte', 'txt', 'oui'),
(49, 'GIMP multi-layer', 'xcf', 'oui'),
(50, 'Excel', 'xls', 'oui'),
(51, 'XML', 'xml', 'oui'),
(52, 'Zip', 'zip', 'oui'),
(53, 'Texte OpenDocument', 'odt', 'oui'),
(54, 'Classeur OpenDocument', 'ods', 'oui'),
(55, 'Présentation OpenDocument', 'odp', 'oui'),
(56, 'Dessin OpenDocument', 'odg', 'oui'),
(57, 'Base de données OpenDocument', 'odb', 'oui');

-- --------------------------------------------------------

--
-- Structure de la table `droits`
--

CREATE TABLE IF NOT EXISTS `droits` (
  `id` varchar(200) NOT NULL DEFAULT '',
  `administrateur` char(1) NOT NULL DEFAULT '',
  `professeur` char(1) NOT NULL DEFAULT '',
  `cpe` char(1) NOT NULL DEFAULT '',
  `scolarite` char(1) NOT NULL DEFAULT '',
  `eleve` char(1) NOT NULL DEFAULT '',
  `responsable` char(1) NOT NULL DEFAULT '',
  `secours` char(1) NOT NULL DEFAULT '',
  `autre` char(1) NOT NULL DEFAULT 'F',
  `description` varchar(255) NOT NULL DEFAULT '',
  `statut` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `droits`
--

INSERT INTO `droits` (`id`, `administrateur`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `autre`, `description`, `statut`) VALUES
('/absences/index.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Typing of the absences', ''),
('/absences/saisie_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Typing of the absences', ''),
('/accueil_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/accueil_modules.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/accueil.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', '', ''),
('/aid/add_aid.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Configuration of IDA', ''),
('/aid/config_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of IDA', ''),
('/aid/export_csv_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of IDA', ''),
('/aid/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'CConfiguration of IDA', ''),
('/aid/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of IDA', ''),
('/aid/index2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of IDA', ''),
('/aid/modify_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of IDA', ''),
('/aid/modify_aid_new.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of IDA', ''),
('/bulletin/edit.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition of bulletins', '1'),
('/bulletin/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition of bulletins', '1'),
('/bulletin/param_bull.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition of bulletins', '1'),
('/bulletin/verif_bulletins.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Checking of the filling of the bulletins', ''),
('/bulletin/verrouillage.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'F', '(Un)Locking of the periods', ''),
('/cahier_notes_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the report cards', ''),
('/cahier_notes/add_modif_conteneur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/cahier_notes/add_modif_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/cahier_notes/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/cahier_notes/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/cahier_notes/toutes_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/cahier_notes/visu_releve_notes.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualization and impression of the report booklets', ''),
('/cahier_texte_admin/admin_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the textbook', ''),
('/cahier_texte_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the textbook', ''),
('/cahier_texte_admin/modify_limites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the textbook', ''),
('/cahier_texte_admin/modify_type_doc.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the textbook', ''),
('/cahier_texte/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/classes/classes_ajout.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of classes', ''),
('/classes/classes_const.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of classes', ''),
('/classes/cpe_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Assignment of the CPE to the classes', ''),
('/classes/duplicate_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of classes', ''),
('/classes/eleve_options.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration and management classes', ''),
('/classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of classes', ''),
('/classes/modify_nom_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of classes', ''),
('/classes/periodes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of classes', ''),
('/classes/prof_suivi.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of classes', ''),
('/classes/scol_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Assignment of the schooling accounts to classes', ''),
('/eleves/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of students', ''),
('/eleves/import_eleves_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of students', ''),
('/eleves/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Management of students', ''),
('/eleves/modify_eleve.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Management of students', ''),
('/etablissements/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of schools', ''),
('/etablissements/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of schoolsv', ''),
('/etablissements/modify_etab.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of schools', ''),
('/gestion/gestion_base_test.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of test data', ''),
('/groupes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition of the groups', ''),
('/groupes/add_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Addition of groups', ''),
('/groupes/edit_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Addition of groups', ''),
('/groupes/edit_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition of the students of groups', ''),
('/groupes/edit_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition of the groups of class', ''),
('/gestion/accueil_sauve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restoration, suppression and backup of the base', ''),
('/gestion/savebackup.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Download of backups the base', ''),
('/gestion/efface_base.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restoration, suppression and backup of the base', ''),
('/gestion/gestion_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of connections', ''),
('/gestion/help_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the school year', ''),
('/gestion/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/gestion/import_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the school year', ''),
('/gestion/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/gestion/modify_impression.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the parameters of the welcome sheet ', ''),
('/gestion/param_gen.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'General configuration', ''),
('/gestion/traitement_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the school year', ''),
('/init_csv/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization CSV of the school year', ''),
('/init_csv/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization CSV of the school year', ''),
('/init_csv/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization CSV of the school year', ''),
('/init_csv/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization CSV of the school year', ''),
('/init_csv/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization CSV of the school year', ''),
('/init_csv/eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization CSV of the school year', ''),
('/init_csv/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization CSV of the school year', ''),
('/init_csv/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization CSV of the school year', ''),
('/init_scribe/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the school year', ''),
('/init_scribe/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the school year', ''),
('/init_scribe/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the school year', ''),
('/init_scribe/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the school year', ''),
('/init_scribe/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the school year', ''),
('/init_scribe/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the school year', ''),
('/init_lcs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization LCS of the school year', ''),
('/init_lcs/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization LCS of the school year', ''),
('/init_lcs/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization LCS of the school year', ''),
('/init_lcs/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization LCS of the school year', ''),
('/init_lcs/affectations.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization LCS of the school year', ''),
('/lib/confirm_query.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/matieres/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the courses', ''),
('/matieres/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the courses', ''),
('/matieres/modify_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the courses', ''),
('/matieres/matieres_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the classes', ''),
('/matieres/matieres_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition of the categories of course', ''),
('/prepa_conseil/edit_limite.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition of the simplified bulletins (working papers)', ''),
('/prepa_conseil/help.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', ''),
('/prepa_conseil/index1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Visualization of notes and appreciations', '1'),
('/prepa_conseil/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualization of the notes per classes', ''),
('/prepa_conseil/index3.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition of the simplified bulletins (working papers)', ''),
('/prepa_conseil/visu_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', ' Visualization of the notes and appreciations IDA', ''),
('/prepa_conseil/visu_toutes_notes.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', ' Visualization of the notes per classes', ''),
('/responsables/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration and management of the responsibles students', ''),
('/responsables/modify_resp.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration and management of the responsibles students', ''),
('/saisie/help.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', ''),
('/saisie/import_class_csv.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', '', ''),
('/saisie/import_note_app.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', ''),
('/saisie/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', ''),
('/saisie/saisie_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Typing of the notes and appreciations IDA', ''),
('/saisie/saisie_appreciations.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Typing of the appreciations of the bulletins', ''),
('/saisie/ajax_appreciations.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Backup of the appreciations of the bulletins', ''),
('/saisie/saisie_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Typing of the opinions of the staff meeting', ''),
('/saisie/saisie_avis1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Typing of the opinions of the staff meeting', ''),
('/saisie/saisie_avis2.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Typing of the opinions of the staff meeting', ''),
('/saisie/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Typing of the opinions of the staff meeting', ''),
('/saisie/traitement_csv.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Typing of the notes of the bulletins', ''),
('/utilisateurs/change_pwd.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the users', ''),
('/utilisateurs/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the users', ''),
('/utilisateurs/import_prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the users', ''),
('/utilisateurs/impression_bienvenue.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the users', ''),
('/utilisateurs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the users', ''),
('/utilisateurs/reset_passwords.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Re-initialization of passwords', ''),
('/utilisateurs/modify_user.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the users', ''),
('/utilisateurs/mon_compte.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Management of the account (personal information, password, ...)', ''),
('/visualisation/classe_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Graphic visualization of the school results', ''),
('/visualisation/eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Graphic visualization of the school results', ''),
('/visualisation/eleve_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Graphic visualization of the school results', ''),
('/visualisation/evol_eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Graphic visualization of the school results', ''),
('/visualisation/evol_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Graphic visualization of the school results', ''),
('/visualisation/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Graphic visualization of the school results', ''),
('/visualisation/stats_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Graphic visualization of the school results', ''),
('/classes/classes_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the classes', ''),
('/fpdf/imprime_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', '', ''),
('/etablissements/import_etab_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration and management of the schools', ''),
('/saisie/import_app_cons.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Importation csv of the opinions of the staff meeting', ''),
('/messagerie/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Management of the Mail', ''),
('/absences/import_absences_gep.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Typing of the absences', ''),
('/absences/seq_gep_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Typing of the absences', ''),
('/utilitaires/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Maintenance', ''),
('/gestion/contacter_admin.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', '', ''),
('/mod_absences/gestion/gestion_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of absences', ''),
('/mod_absences/gestion/impression_absences_liste.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of the absences', ''),
('/mod_absences/gestion/impression_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of the absences', ''),
('/mod_absences/gestion/select.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of absences', ''),
('/mod_absences/gestion/ajout_ret.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of absences', ''),
('/mod_absences/gestion/ajout_dip.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of absences', ''),
('/mod_absences/gestion/ajout_inf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of absences', ''),
('/mod_absences/gestion/ajout_abs.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of absences', ''),
('/mod_absences/gestion/bilan_absence.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of absences', ''),
('/mod_absences/gestion/bilan.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of absences', ''),
('/mod_absences/gestion/lettre_aux_parents.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Management of absences', ''),
('/mod_absences/lib/tableau.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/lib/tableau_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration of the module absences', ''),
('/mod_absences/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration of the module absences', ''),
('/edt_organisation/admin_periodes_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration of the module absences', ''),
('/mod_absences/lib/liste_absences.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/lib/graphiques.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/professeurs/prof_ajout_abs.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Addition of the absences in class', ''),
('/mod_absences/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the actions absences', ''),
('/mod_trombinoscopes/trombinoscopes.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'Visualize the trombinoscope', ''),
('/mod_trombinoscopes/trombi_impr.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualize the trombinoscope', ''),
('/mod_trombinoscopes/trombinoscopes_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration of trombinoscope', ''),
('/cahier_notes/visu_toutes_notes2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualization of the averages of the Report cards', ''),
('/cahier_notes/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualization of the averages of the report cards', ''),
('/utilitaires/verif_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Checking of the inconsistencies of memberships of groups', ''),
('/referencement.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Referencing of Gepi on the centralized base of user of Gepi', ''),
('/utilisateurs/tab_profs_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Assignment of courses to the professors', ''),
('/matieres/matieres_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation of the courses from a file CSV', ''),
('/groupes/edit_class_grp_lot.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the simple courses by batch.', ''),
('/groupes/visu_profs_class.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualization of the teaching staffs', ''),
('/groupes/popup.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualization of the teaching staffs', ''),
('/visualisation/affiche_eleve.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Graphic visualization of school results', ''),
('/visualisation/draw_graphe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Graphic visualization of school results', ''),
('/groupes/mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Access to the CSV of the lists of students', ''),
('/groupes/get_csv.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Generation of CSV students', ''),
('/visualisation/choix_couleurs.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choice of the colors of the graphs of the school results', ''),
('/visualisation/couleur.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Choice of a color for the graph of the school results', ''),
('/gestion/config_prefs.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Definition of users preferences ', ''),
('/utilitaires/recalcul_moy_conteneurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction of the averages of the containers', ''),
('/saisie/commentaires_types.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Typing of comment-types', ''),
('/mod_absences/lib/fiche_eleve.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Card-index of follow-up of student', ''),
('/cahier_notes/releve_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Report booklet in the format PDF', ''),
('/impression/parametres_impression_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression of lists pdf; adjustment of parameters', ''),
('/impression/impression_serie.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression of the lists (PDF) in series', ''),
('/impression/impression.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Fast impression of lists (PDF) ', ''),
('/impression/liste_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression of lists (PDF)', ''),
('/init_xml/lecture_xml_sconet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/disciplines_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/lecture_xml_sts_emp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/save_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/responsables/maj_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Update from Sconet', ''),
('/responsables/conversion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conversion of the data responsibles', ''),
('/utilisateurs/create_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Creation of the users with the statute responsible', ''),
('/utilisateurs/create_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Creation of the users with the statute responsible', ''),
('/utilisateurs/edit_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition of the users with the statute responsible', ''),
('/utilisateurs/edit_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition of the users with the statute student', ''),
('/cahier_texte/consultation.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation of the textbooks', ''),
('/cahier_texte/see_all.php', 'F', 'V', 'V', 'F', 'V', 'V', 'F', 'F', 'Consultation of the textbooks', ''),
('/cahier_texte/visu_prof_jour.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Access_to_his_personal_textbook', ''),
('/gestion/droits_acces.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Parameter setting of the rights of access', ''),
('/groupes/visu_profs_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation teaching staff', ''),
('/saisie/impression_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression of the quarterly opinions  of the staff meetings.', ''),
('/impression/avis_pdf.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression of the quarterly opinions of the staff meetings. Module PDF', ''),
('/impression/parametres_impression_pdf_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression of the opinions staff meetings pdf; parameters settings', ''),
('/utilisateurs/password_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Export of the identifiers and passwords in csv', ''),
('/impression/password_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Impression of the identifiers and the passwords in pdf', ''),
('/bulletin/buletin_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', ' Report card in pdf format ', ''),
('/mod_absences/gestion/etiquette_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Label in pdf format ', ''),
('/mod_absences/lib/export_csv.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'File of export in csv of the absences', ''),
('/mod_absences/gestion/statistiques.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Statistics of the modulate school life', '1'),
('/mod_absences/lib/graph_camembert.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Camembert graph ', ''),
('/mod_absences/lib/graph_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Camembert graph', ''),
('/edt_organisation/admin_horaire_ouverture.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Definition of the schedules of opening of the school', ''),
('/edt_organisation/admin_config_semaines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of the types of weeks', ''),
('/mod_absences/gestion/fiche_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Card-index absences summary ', ''),
('/mod_absences/lib/graph_double_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphics absence and delay on the same graph', ''),
('/bulletin/param_bull_pdf.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'page of management of the parameters of the bulletin pdf', ''),
('/bulletin/bulletin_pdf_avec_modele_classe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'page generating bulletin pdf according to the model affected to the class ', ''),
('/gestion/security_panel.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Control panel of the security attacks ', ''),
('/gestion/security_policy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'definition of the policies of safety', ''),
('/mod_absences/gestion/alert_suivi.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'System of alarm  of follow-up of student', ''),
('/gestion/efface_photos.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Removal of the photographs nonassociated to students', ''),
('/responsables/gerer_adr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the addresses of responsibles', ''),
('/responsables/choix_adr_existante.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choice addresses existing responsible ', ''),
('/cahier_notes/export_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export CSV/ODS book of notes', ''),
('/cahier_notes/import_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Import CSV book of notes', ''),
('/gestion/options_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Options of connections', ''),
('/eleves/add_eleve.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Management of the students', ''),
('/saisie/export_class_ods.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export ODS of notes/appreciations', ''),
('/gestion/gestion_temp_dir.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des dossiers temporaires d utilisateurs', ''),
('/gestion/param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des couleurs pour Gepi', ''),
('/utilisateurs/creer_remplacant.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'script de création d un remplaçant', ''),
('/mod_absences/gestion/lettre_pdf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Mass mailing of the letters of absences PDF', '1'),
('/accueil_simpl_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Simplified home page for Profs', ''),
('/init_xml2/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/init_xml2/clean_temp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/mod_annees_anterieures/conservation_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conservation of the former data', ''),
('/mod_annees_anterieures/consultation_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation of the data of former years', ''),
('/mod_annees_anterieures/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Index former data', ''),
('/mod_annees_anterieures/popup_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation of former data', ''),
('/mod_annees_anterieures/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Activation/desactivation module former data', ''),
('/mod_annees_anterieures/nettoyer_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression of former data', ''),
('/responsables/maj_import1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Update from Sconet', ''),
('/responsables/maj_import2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Update from Sconet', ''),
('/mod_annees_anterieures/corriger_ine.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction of INE in table annees_anterieures', ''),
('/mod_annees_anterieures/liste_eleves_ajax.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Search for students', ''),
('/mod_absences/lib/graph_double_ligne_fiche.php', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'F', 'Graphics of the card student', '1'),
('/edt_organisation/edt_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the calendar', ''),
('/edt_organisation/index_edt.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Management of the timetables', ''),
('/edt_organisation/edt_initialiser.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization of the timetables', ''),
('/edt_organisation/effacer_cours.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Erase a course from EdT', ''),
('/edt_organisation/ajouter_salle.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the rooms', ''),
('/edt_organisation/edt_parametrer.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Manage the EdT parameters', ''),
('/edt_organisation/voir_groupe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'See the groups of Gepi', ''),
('/edt_organisation/modif_edt_tempo.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Temporary modification of EdT', ''),
('/edt_organisation/edt_init_xml.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization EdT by xml', ''),
('/edt_organisation/edt_init_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization EdT by csv', ''),
('/edt_organisation/edt_init_csv2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization EdT by another csv', ''),
('/edt_organisation/edt_init_texte.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization EdT by a text file', ''),
('/edt_organisation/edt_init_concordance.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization EdT by a text file', ''),
('/edt_organisation/edt_init_concordance2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization EdT by another file csv', ''),
('/edt_organisation/modifier_cours.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Modify a course', ''),
('/edt_organisation/modifier_cours_popup.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Modify a course', ''),
('/edt_organisation/edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Set the module timetable', ''),
('/edt_organisation/edt_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Set the module timetable', ''),
('/edt_organisation/edt_param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Set the colors of the courses (EdT)', ''),
('/edt_organisation/ajax_edtcouleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Change the colors of the courses (EdT)', ''),
('/absences/import_absences_sconet.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Typing of absences', ''),
('/mod_absences/admin/admin_config_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Define the various periods', ''),
('/bulletin/export_modele_pdf.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'export in csv of the modeles of bulletin pdf', ''),
('/absences/consulter_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Consult the absences', ''),
('/mod_absences/professeurs/bilan_absences_professeur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Assessment of the absences for each professor', ''),
('/mod_absences/professeurs/bilan_absences_classe.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Assessment of the absences for each professor', ''),
('/mod_absences/gestion/voir_absences_viescolaire.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consult the absences of the day', ''),
('/mod_absences/gestion/bilan_absences_quotidien.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consult the absences by crenel', ''),
('/mod_absences/gestion/bilan_absences_quotidien_pdf.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consult the absences by crenel in pdf', ''),
('/mod_absences/gestion/bilan_absences_classe.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consult the absences by class', ''),
('/mod_absences/gestion/bilan_repas_quotidien.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consult inscription to meals', ''),
('/mod_absences/absences.php', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'F', 'Consult the absences of his child', ''),
('/mod_absences/admin/interface_abs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Parameterize the interfaces of the professors', ''),
('/absences/import_absences_gepi.php', 'F', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Page of importation of the absences of gepi mod_absences', '1'),
('/lib/change_mode_header.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Page AJAX to change the variable cacher_header', '1'),
('/saisie/recopie_moyennes.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Recopy of averages', ''),
('/groupes/fusion_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Merge groups', ''),
('/gestion/security_panel_archives.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'page archives secutity panel ', ''),
('/lib/header_barre_menu.php/', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Horizontal bar of menu', ''),
('/responsables/corrige_ele_id.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction of the ELE_ID according to Sconet', ''),
('/mod_inscription/inscription_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '(De)activation of the module inscription', ''),
('/mod_inscription/inscription_index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'access to the module configuration', ''),
('/mod_inscription/inscription_config.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration of the module inscription', ''),
('/mod_inscription/help.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration of the module inscription', ''),
('/aid/index_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Complementary tools of management of IDAs', ''),
('/aid/visu_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Complementary tools of management of IDAs', ''),
('/aid/modif_fiches.php', 'V', 'V', 'V', 'F', 'V', 'V', 'F', 'F', 'Complementary tools of management of IDAs', ''),
('/aid/config_aid_fiches_projet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of Complementary tools of management of IDAs', ''),
('/aid/config_aid_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of Complementary tools of management of IDAs', ''),
('/aid/config_aid_productions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration of Complementary tools of management of IDAs', ''),
('/aid/annees_anterieures_accueil.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Configuration of IDA', ''),
('/classes/acces_appreciations.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration of the restriction of access to the appreciations for the students and responsibles', ''),
('/mod_notanet/notanet_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of module NOTANET', ''),
('/mod_notanet/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Home', ''),
('/mod_notanet/extract_moy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Extraction of averages', ''),
('/mod_notanet/corrige_extract_moy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Extraction of averages', ''),
('/mod_notanet/select_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Associations students/type of diploma', ''),
('/mod_notanet/select_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Associations matières/type of diploma', ''),
('/mod_notanet/saisie_app.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Typing of appreciations', ''),
('/mod_notanet/generer_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Generation of CSV', ''),
('/mod_notanet/choix_generation_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Generation of CSV', ''),
('/mod_notanet/verrouillage_saisie_app.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: (Un)Locking of the typing', ''),
('/bulletin/bull_index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition of bulletins', '1'),
('/cahier_notes/visu_releve_notes_bis.php', 'F', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Report booklet', '1'),
('/cahier_notes/param_releve_html.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Parameters of the report booklet', '1'),
('/utilisateurs/creer_statut.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Create personalized statutes', ''),
('/utilisateurs/creer_statut_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Authorize the creation of personalized statutes', ''),
('/classes/changement_eleve_classe.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Change of class for a student', '1'),
('/edt_gestion_gr/edt_aff_gr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Manage the groups of the EdT module', ''),
('/edt_gestion_gr/edt_ajax_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mnage the groups of the EdT module', ''),
('/edt_gestion_gr/edt_liste_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Manage the groups of the EdT module', ''),
('/edt_gestion_gr/edt_liste_profs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Manage the groups of the EdT module', ''),
('/edt_gestion_gr/edt_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Manage the groups of the EdT module', ''),
('/mod_notanet/saisie_avis.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Typing opinion of headmaster', ''),
('/mod_notanet/saisie_b2i_a2.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Typing bases B2i and A2', ''),
('/mod_notanet/poitiers/fiches_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Access to the NOTANET export ', ''),
('/mod_notanet/poitiers/param_fiche_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Parameters of impression', ''),
('/mod_notanet/rouen/fiches_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Access to export NOTANET', ''),
('/eleves/liste_eleves.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'List_of_students', ''),
('/eleves/visu_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Consultation_of_a_student', ''),
('/cahier_texte_admin/rss_cdt_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Manage rss flows of the textbook', ''),
('/matieres/suppr_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression of a course', ''),
('/mod_annees_anterieures/archivage_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Cards projects', '1'),
('/eleves/import_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation student bulletin ', ''),
('/eleves/export_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Export student bulletin ', ''),
('/mod_ent/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the integration of GEPI in a ENT', ''),
('/mod_ent/gestion_ent_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the integration of GEPI in a ENT', ''),
('/mod_ent/gestion_ent_profs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the integration of GEPI in a ENT', ''),
('/mod_ent/miseajour_ent_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the integration of GEPI in a ENT', ''),
('/cahier_texte_admin/visa_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page of signature of the textbooks', ''),
('/public/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the textbook', ''),
('/saisie/saisie_cmnt_type_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Typing appreciation-types for Profs', ''),
('/mod_discipline/traiter_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Treatment', ''),
('/mod_discipline/saisie_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Typing incident', ''),
('/mod_discipline/occupation_lieu_heure.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Occupation place', ''),
('/mod_discipline/liste_sanctions_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: List', ''),
('/mod_discipline/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Index', ''),
('/mod_discipline/incidents_sans_protagonistes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Incidents without protagonists', ''),
('/mod_discipline/edt_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: student EDT ', ''),
('/mod_discipline/ajout_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Addition sanction', ''),
('/mod_discipline/saisie_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Typing sanction', ''),
('/mod_discipline/definir_roles.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Definition of roles', ''),
('/mod_discipline/definir_mesures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Definition of measurements', ''),
('/mod_discipline/sauve_role.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg incidental role', ''),
('/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Define standard sanctions', ''),
('/mod_discipline/liste_retenues_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: List of detention of the day', ''),
('/mod_discipline/avertir_famille.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: inform family of incident ', ''),
('/mod_discipline/avertir_famille_html.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: inform family of incident ', ''),
('/mod_discipline/sauve_famille_avertie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg informed family', ''),
('/mod_discipline/discipline_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Activation/desactivation of the module', ''),
('/classes/classes_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page called via ajax.', ''),
('/saisie/saisie_secours_eleve.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Typing notes/appreciations for a student in emergency account ', ''),
('/responsables/dedoublonnage_adresses.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Dédoublonnage of the addresses responsibles', ''),
('/mod_ooo/rapport_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Model Ooo : Rapport Incident', ''),
('/mod_ooo/gerer_modeles_ooo.php', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Model Ooo : Manage and use models', ''),
('/mod_ooo/ooo_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Model Ooo : Admin', ''),
('/mod_ooo/retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Model Ooo : Retenue', ''),
('/mod_ooo/formulaire_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Model Ooo : form detention', ''),
('/mod_ooo/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Model Ooo: Index : Index', ''),
('/mod_discipline/update_colonne_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Display of a printer for the responsible of an incident', ''),
('/mod_discipline/definir_lieux.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Define the places', ''),
('/mod_notanet/fb_rouen_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Cards patent pdf for Rouen', ''),
('/mod_notanet/fb_montpellier_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', ' Cards patent pdf for Montpellier', ''),
('/mod_genese_classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Accueil', ''),
('/mod_genese_classes/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis classes: Activation/desactivation', ''),
('/mod_genese_classes/select_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Choice of the options', ''),
('/mod_genese_classes/select_eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Choice of the options of the students', ''),
('/mod_genese_classes/select_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Choice of the classes', ''),
('/mod_genese_classes/saisie_contraintes_opt_classe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Typing of the constraints options/classes', ''),
('/mod_genese_classes/liste_classe_fut.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: List of future classe (appel ajax)', ''),
('/mod_genese_classes/affiche_listes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Display of lists', ''),
('/mod_genese_classes/genere_ods.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Generation of a file ODS of lists', ''),
('/mod_genese_classes/affect_eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Assignment of the students', ''),
('/mod_genese_classes/select_arriv_red.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Selection of the newcomers/redoubling', ''),
('/mod_genese_classes/liste_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: List options of existing classes', ''),
('/mod_genese_classes/import_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Genesis of classes: Importation options from CSV', ''),
('/eleves/import_communes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Importation of the communes of birth', ''),
('/mod_notanet/fb_lille_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Cards patent pdf for Lille', ''),
('/mod_notanet/fb_creteil_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Cards patent pdf for Creteil', ''),
('/mod_plugins/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', ' add/ remove plugins', ''),
('/saisie/export_cmnt_type_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Export appreciation-types for Profs', ''),
('/mod_discipline/disc_stat.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Statistics', ''),
('/mod_epreuve_blanche/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'White tests: Activation/desactivation of the module', ''),
('/mod_examen_blanc/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Practice tests: Activation/desactivation of the module', ''),
('/mod_epreuve_blanche/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'White test: Home', ''),
('/mod_epreuve_blanche/transfert_cn.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'White test: Transfer to report card', ''),
('/mod_epreuve_blanche/saisie_notes.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'White test: Typing of the notes', ''),
('/mod_epreuve_blanche/genere_emargement.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'White test: Annotating generation', ''),
('/mod_epreuve_blanche/definir_salles.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'White test: define the rooms', ''),
('/mod_epreuve_blanche/attribuer_copies.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'White test: Allot the copies to the professors', ''),
('/mod_epreuve_blanche/bilan.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'White test: Assessment', ''),
('/mod_epreuve_blanche/genere_etiquettes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'White test: Labels generation', ''),
('/mod_examen_blanc/saisie_notes.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Practice test: Typing exam except course', ''),
('/mod_examen_blanc/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Practice test: Home', ''),
('/mod_examen_blanc/releve.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Practice test: Statement', ''),
('/mod_examen_blanc/bull_exb.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Practice test: Report cards', ''),
('/saisie/saisie_synthese_app_classe.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Synthesis of the appreciations on the group class .', ''),
('/gestion/saisie_message_connexion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Typing of messages of connection.', ''),
('/groupes/repartition_ele_grp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Distribute students in groups', ''),
('/prepa_conseil/edit_limite_bis.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition of the simplified bulletins (working papers)', ''),
('/prepa_conseil/index2bis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualization of the notes by classes', ''),
('/prepa_conseil/index3bis.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition of the simplified bulletins (working papers)', ''),
('/prepa_conseil/visu_toutes_notes_bis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualization of the notes by classes', ''),
('/utilitaires/import_pays.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Import of countries', ''),
('/mod_apb/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the module Admissions PostBac', ''),
('/mod_apb/index.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'Export XML for the system Post-vat Admissions ', ''),
('/mod_apb/export_xml.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'Export XML for the system Admissions Post-Bac', ''),
('/mod_gest_aid/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Managers IDA', ''),
('/saisie/ajax_edit_limite.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition of the simplified bulletins (working papers)', '');
INSERT INTO `droits` (`id`, `administrateur`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `autre`, `description`, `statut`) VALUES
('/mod_discipline/check_nature_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Discipline: Search for natures of incident', ''),
('/groupes/signalement_eleves.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Groups: description of the errors of assignment student', ''),
('/bulletin/envoi_mail.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Sending of mall via ajax', ''),
('/mod_discipline/destinataires_alertes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Parametrage of the recipients of mall of alarm', ''),
('/init_scribe_ng/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - index', ''),
('/init_scribe_ng/etape1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - stage 1', ''),
('/init_scribe_ng/etape2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - stage 2', ''),
('/init_scribe_ng/etape3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - stage 3', ''),
('/init_scribe_ng/etape4.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - stage 4', ''),
('/init_scribe_ng/etape5.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - stage 5', ''),
('/init_scribe_ng/etape6.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - stage 6', ''),
('/init_scribe_ng/etape7.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - stage 7', ''),
('/mod_ects/ects_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Module ECTS : Admin', ''),
('/mod_ects/index_saisie.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Home typing', ''),
('/mod_ects/saisie_ects.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Typing', ''),
('/mod_ects/edition.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Edition of the documents', ''),
('/mod_ooo/documents_ects.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Generation of the documents', ''),
('/mod_ects/recapitulatif.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Global summary', ''),
('/mod_discipline/stats2/index.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Module discipline: Statistics', ''),
('/mod_discipline/definir_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Define the categories', ''),
('/mod_abs2/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration of the module absences', ''),
('/mod_abs2/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration module absences', ''),
('/mod_abs2/admin/admin_types_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration module absences', ''),
('/mod_abs2/admin/admin_lieux_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration module absences', ''),
('/mod_abs2/admin/admin_justifications_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration of the module absences', ''),
('/mod_abs2/admin/admin_table_agregation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration of the module absences', ''),
('/mod_abs2/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration of the module absences', ''),
('/mod_abs2/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Administration of the module absences', ''),
('/mod_abs2/saisir_groupe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Display of the form of typing of absences', ''),
('/mod_abs2/absences_du_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Display of the absences of the day', ''),
('/mod_abs2/enregistrement_saisie_groupe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Recording of the typing of a group', ''),
('/mod_abs2/liste_saisies.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'List of typing', ''),
('/mod_abs2/liste_traitements.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', ' List of the treatments', ''),
('/mod_abs2/liste_notifications.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'List of notifications', ''),
('/mod_abs2/liste_saisies_selection_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'List of typing to make the treatment', ''),
('/mod_abs2/visu_saisie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Visualization of a typing', ''),
('/mod_abs2/visu_traitement.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Visualization of a typing', ''),
('/mod_abs2/visu_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualization of a notification', ''),
('/mod_abs2/enregistrement_modif_saisie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Modification of a typing', ''),
('/mod_abs2/enregistrement_modif_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Modification of a treatment', ''),
('/mod_abs2/enregistrement_modif_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Modification of a notification', ''),
('/mod_abs2/generer_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'generate a notification', ''),
('/mod_abs2/saisir_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'V', 'Type the absence of a eleve', ''),
('/mod_abs2/enregistrement_saisie_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'V', 'Record absence of a eleve', ''),
('/mod_abs2/creation_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Create a treatment', ''),
('/mod_discipline/saisie_incident_abs2.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Type an incident relative to an absence', ''),
('/mod_abs2/tableau_des_appels.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualization of the tables of the typing', ''),
('/mod_abs2/bilan_du_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualization of the assessment of the day', ''),
('/mod_abs2/extraction_saisies.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Extraction of the typing', ''),
('/mod_abs2/extraction_demi-journees.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Extraction of the typing', ''),
('/mod_abs2/ajax_edt_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Display edt', ''),
('/mod_abs2/generer_notifications_par_lot.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Grouped generation of the mails', ''),
('/mod_abs2/bilan_individuel.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Individual assessment of the student absences', ''),
('/mod_abs2/totaux_du_jour.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'V', 'Totals of absences of the the day', ''),
('/mod_abs2/statistiques.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Statistics of the absences', ''),
('/bulletin/autorisation_exceptionnelle_saisie_app.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Exceptional authorization of typing of appreciation', ''),
('/init_csv/export_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV: Export tables', ''),
('/cahier_texte_2/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_edition_compte_rendu.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_edition_notice_privee.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_duplication_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_affichage_duplication_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_deplacement_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_affichage_deplacement_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_suppression_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_enregistrement_compte_rendu.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_enregistrement_notice_privee.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_edition_devoir.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_enregistrement_devoir.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_affichages_liste_notices.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/ajax_affichage_dernieres_notices.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/exportcsv.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook', '1'),
('/cahier_texte_2/consultation.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation of the textbooks', ''),
('/cahier_texte_2/see_all.php', 'F', 'V', 'V', 'F', 'V', 'V', 'F', 'F', 'Consultation of the textbooks', ''),
('/cahier_texte_2/creer_sequence.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook - sequences', '1'),
('/cahier_texte_2/creer_seq_ajax_step1.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbook - sequences', '1'),
('/mod_trombinoscopes/trombino_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Trombinoscopes PDF', ''),
('/mod_trombinoscopes/trombino_decoupe.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Generation of a pdf grid for the trombinoscopes,...', ''),
('/groupes/menage_eleves_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Groupes: Desinscription of student without notes nor appreciations', ''),
('/statistiques/export_donnees_bulletins.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Export of data of the bulletins', ''),
('/statistiques/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Statistics: Index', ''),
('/statistiques/classes_effectifs.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Statistics: class , manpower', ''),
('/mod_annees_anterieures/ajax_bulletins.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'V', 'Access to the bulletins of former years', ''),
('/mod_annees_anterieures/ajax_signaler_faute.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Possibility of announcing a typing error in an appreciation', ''),
('/eleves/ajax_modif_eleve.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Recording of the modifications student', ''),
('/classes/ajouter_periode.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Classes: Add periods', ''),
('/classes/supprimer_periode.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Classes: Remove periods', ''),
('/groupes/visu_mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Access to the lists of students', ''),
('/cahier_notes/index_cc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/cahier_notes/add_modif_cc_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/cahier_notes/add_modif_cc_eval.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/cahier_notes/saisie_notes_cc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/cahier_notes/visu_cc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Report card', '1'),
('/responsables/synchro_mail.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Synchronization of mall responsibles', ''),
('/eleves/synchro_mail.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Synchronization of the students', ''),
('/cahier_texte_2/archivage_cdt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Archiving of textbook', ''),
('/documents/archives/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Archiving of textbook', ''),
('/saisie/saisie_vocabulaire.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Typing of vocabulary', ''),
('/mod_epreuve_blanche/genere_liste_affichage.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'White test: Generation lists display', ''),
('/cahier_texte_2/ajax_devoirs_classe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', ' Textbooks : Exams of a class for such day', ''),
('/cahier_texte_2/ajax_liste_notices_privees.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Textbooks : List private notices', ''),
('/mod_ooo/publipostage_ooo.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Model Ooo : Mass mailing', ''),
('/saisie/saisie_mentions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Typing of mentions', ''),
('/mod_discipline/visu_disc.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Discipline: Access student/parent', ''),
('/mod_discipline/definir_natures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: To define natures', ''),
('/init_xml2/traite_csv_udt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation of the courses via an Export CSV UDT', ''),
('/init_xml2/init_alternatif.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialization school year', ''),
('/mod_examen_blanc/copie_exam.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Practice test: Copy', ''),
('/mod_sso_table/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Management of the table of correspondence sso', ''),
('/gestion/changement_d_annee.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Change of year.', ''),
('/edt_organisation/verifier_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', ' check the table edt_cours', ''),
('/edt_organisation/voir_base.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', ' see the table edt_cours', ''),
('/edt_organisation/transferer_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', ' transfer an edt', ''),
('/mod_notanet/OOo/fiches_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Cards OpenOffice patent', ''),
('/mod_notanet/OOo/imprime_ooo.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Print cards patent OpenOffice', ''),
('/mod_notanet/saisie_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Cards brevet: Typing of the parameters', ''),
('/mod_notanet/saisie_socle_commun.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Typing common base', ''),
('/responsables/infos_parents.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Grid students/parents', ''),
('/sms/index.php', 'V', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Sending of SMS', '');

-- --------------------------------------------------------

--
-- Structure de la table `droits_aid`
--

CREATE TABLE IF NOT EXISTS `droits_aid` (
  `id` varchar(200) NOT NULL DEFAULT '',
  `public` char(1) NOT NULL DEFAULT '',
  `professeur` char(1) NOT NULL DEFAULT '',
  `cpe` char(1) NOT NULL DEFAULT '',
  `scolarite` char(1) NOT NULL DEFAULT '',
  `eleve` char(1) NOT NULL DEFAULT '',
  `responsable` char(1) NOT NULL DEFAULT 'F',
  `secours` char(1) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `statut` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `droits_aid`
--

INSERT INTO `droits_aid` (`id`, `public`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `description`, `statut`) VALUES
('nom', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'To specify', '1'),
('numero', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'To specify', '1'),
('perso1', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'To specify', '1'),
('perso2', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'To specify', '1'),
('productions', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Production', '1'),
('resume', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Summary', '1'),
('famille', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Famille', '1'),
('mots_cles', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Key words', '1'),
('adresse1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Public address', '1'),
('adresse2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Private address', '1'),
('public_destinataire', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Public recipient', '1'),
('contacts', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Contacts, ressources', '1'),
('divers', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Divers', '1'),
('matiere1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Principal discipline', '1'),
('matiere2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Discipline secondary', '1'),
('eleve_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'To specify', '1'),
('cpe_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'To specify', '1'),
('prof_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'To specify', '0'),
('fiche_publique', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'To specify', '1'),
('affiche_adresse1', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'To specify', '1'),
('en_construction', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'To specify', '1'),
('perso3', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'To specify', '0');

-- --------------------------------------------------------

--
-- Structure de la table `droits_speciaux`
--

CREATE TABLE IF NOT EXISTS `droits_speciaux` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_statut` int(11) NOT NULL,
  `nom_fichier` varchar(200) NOT NULL,
  `autorisation` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `droits_speciaux`
--


-- --------------------------------------------------------

--
-- Structure de la table `droits_statut`
--

CREATE TABLE IF NOT EXISTS `droits_statut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_statut` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `droits_statut`
--


-- --------------------------------------------------------

--
-- Structure de la table `droits_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `droits_utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_statut` int(11) NOT NULL,
  `login_user` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `droits_utilisateurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `eb_copies`
--

CREATE TABLE IF NOT EXISTS `eb_copies` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login_ele` varchar(255) NOT NULL,
  `n_anonymat` varchar(255) NOT NULL,
  `id_salle` int(11) NOT NULL DEFAULT '-1',
  `login_prof` varchar(255) NOT NULL,
  `note` float(10,1) NOT NULL DEFAULT '0.0',
  `statut` varchar(255) NOT NULL DEFAULT '',
  `id_epreuve` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `eb_copies`
--


-- --------------------------------------------------------

--
-- Structure de la table `eb_epreuves`
--

CREATE TABLE IF NOT EXISTS `eb_epreuves` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type_anonymat` varchar(255) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `etat` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `eb_epreuves`
--


-- --------------------------------------------------------

--
-- Structure de la table `eb_groupes`
--

CREATE TABLE IF NOT EXISTS `eb_groupes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_epreuve` int(11) unsigned NOT NULL,
  `id_groupe` int(11) unsigned NOT NULL,
  `transfert` varchar(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `eb_groupes`
--


-- --------------------------------------------------------

--
-- Structure de la table `eb_profs`
--

CREATE TABLE IF NOT EXISTS `eb_profs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_epreuve` int(11) unsigned NOT NULL,
  `login_prof` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `eb_profs`
--


-- --------------------------------------------------------

--
-- Structure de la table `eb_salles`
--

CREATE TABLE IF NOT EXISTS `eb_salles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `salle` varchar(255) NOT NULL,
  `id_epreuve` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `eb_salles`
--


-- --------------------------------------------------------

--
-- Structure de la table `ects_credits`
--

CREATE TABLE IF NOT EXISTS `ects_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL COMMENT 'Identifiant de l''eleve',
  `num_periode` int(11) NOT NULL COMMENT 'Identifiant de la periode',
  `id_groupe` int(11) NOT NULL COMMENT 'Identifiant du groupe',
  `valeur` decimal(3,1) DEFAULT NULL COMMENT 'Nombre de credits obtenus par l''eleve',
  `mention` varchar(255) DEFAULT NULL COMMENT 'Mention obtenue',
  `mention_prof` varchar(255) DEFAULT NULL COMMENT 'Mention presaisie par le prof',
  PRIMARY KEY (`id`,`id_eleve`,`num_periode`,`id_groupe`),
  KEY `ects_credits_FI_1` (`id_eleve`),
  KEY `ects_credits_FI_2` (`id_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ects_credits`
--


-- --------------------------------------------------------

--
-- Structure de la table `ects_global_credits`
--

CREATE TABLE IF NOT EXISTS `ects_global_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL COMMENT 'Identifiant de l''eleve',
  `mention` varchar(255) NOT NULL COMMENT 'Mention obtenue',
  PRIMARY KEY (`id`,`id_eleve`),
  KEY `ects_global_credits_FI_1` (`id_eleve`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ects_global_credits`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_calendrier`
--

CREATE TABLE IF NOT EXISTS `edt_calendrier` (
  `id_calendrier` int(11) NOT NULL AUTO_INCREMENT,
  `classe_concerne_calendrier` text NOT NULL,
  `nom_calendrier` varchar(100) NOT NULL DEFAULT '',
  `debut_calendrier_ts` varchar(11) NOT NULL,
  `fin_calendrier_ts` varchar(11) NOT NULL,
  `jourdebut_calendrier` date NOT NULL DEFAULT '0000-00-00',
  `heuredebut_calendrier` time NOT NULL DEFAULT '00:00:00',
  `jourfin_calendrier` date NOT NULL DEFAULT '0000-00-00',
  `heurefin_calendrier` time NOT NULL DEFAULT '00:00:00',
  `numero_periode` tinyint(4) NOT NULL DEFAULT '0',
  `etabferme_calendrier` tinyint(4) NOT NULL,
  `etabvacances_calendrier` tinyint(4) NOT NULL,
  PRIMARY KEY (`id_calendrier`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `edt_calendrier`
--

INSERT INTO `edt_calendrier` (`id_calendrier`, `classe_concerne_calendrier`, `nom_calendrier`, `debut_calendrier_ts`, `fin_calendrier_ts`, `jourdebut_calendrier`, `heuredebut_calendrier`, `jourfin_calendrier`, `heurefin_calendrier`, `numero_periode`, `etabferme_calendrier`, `etabvacances_calendrier`) VALUES
(1, '', 'Trimestre 1', '1317798900', '1330538400', '2011-10-05', '07:15:00', '2012-02-29', '18:00:00', 1, 1, 0),
(3, '1;2;', 'Trimestre 2', '1330214400', '1334707140', '2012-02-26', '00:00:00', '2012-04-17', '23:59:00', 0, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `edt_classes`
--

CREATE TABLE IF NOT EXISTS `edt_classes` (
  `id_edt_classe` int(11) NOT NULL AUTO_INCREMENT,
  `groupe_edt_classe` int(11) NOT NULL,
  `prof_edt_classe` varchar(25) NOT NULL,
  `matiere_edt_classe` varchar(10) NOT NULL,
  `semaine_edt_classe` varchar(5) NOT NULL,
  `jour_edt_classe` tinyint(4) NOT NULL,
  `datedebut_edt_classe` date NOT NULL,
  `datefin_edt_classe` date NOT NULL,
  `heuredebut_edt_classe` time NOT NULL,
  `heurefin_edt_classe` time NOT NULL,
  `salle_edt_classe` varchar(50) NOT NULL,
  PRIMARY KEY (`id_edt_classe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `edt_classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_cours`
--

CREATE TABLE IF NOT EXISTS `edt_cours` (
  `id_cours` int(3) NOT NULL AUTO_INCREMENT,
  `id_groupe` varchar(10) NOT NULL,
  `id_aid` varchar(10) NOT NULL,
  `id_salle` varchar(3) NOT NULL,
  `jour_semaine` varchar(10) NOT NULL,
  `id_definie_periode` varchar(3) NOT NULL,
  `duree` varchar(10) NOT NULL DEFAULT '2',
  `heuredeb_dec` varchar(3) NOT NULL DEFAULT '0',
  `id_semaine` varchar(3) NOT NULL DEFAULT '0',
  `id_calendrier` varchar(3) NOT NULL DEFAULT '0',
  `modif_edt` varchar(3) NOT NULL DEFAULT '0',
  `login_prof` varchar(50) NOT NULL,
  PRIMARY KEY (`id_cours`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `edt_cours`
--

INSERT INTO `edt_cours` (`id_cours`, `id_groupe`, `id_aid`, `id_salle`, `jour_semaine`, `id_definie_periode`, `duree`, `heuredeb_dec`, `id_semaine`, `id_calendrier`, `modif_edt`, `login_prof`) VALUES
(1, '3', '', 'rie', 'lundi', '1', '2', '0', '0', '0', '0', 'bih'),
(2, '3', '', 'rie', 'mercredi', '2', '2', '0', '0', '0', '0', 'bih'),
(3, '3', '', 'rie', 'jeudi', '1', '2', '0', '0', '0', '0', 'bih'),
(4, '3', '', 'rie', 'vendredi', '4', '2', '0', '0', '0', '0', 'bih'),
(5, '1', '', 'rie', 'lundi', '3', '2', '0', '0', '0', '0', 'elijah'),
(6, '1', '', 'rie', 'mardi', '2', '2', '0', '0', '0', '0', 'elijah'),
(7, '1', '', 'rie', 'mercredi', '5', '2', '0', '0', '0', '0', 'elijah');

-- --------------------------------------------------------

--
-- Structure de la table `edt_creneaux`
--

CREATE TABLE IF NOT EXISTS `edt_creneaux` (
  `id_definie_periode` int(11) NOT NULL AUTO_INCREMENT,
  `nom_definie_periode` varchar(10) NOT NULL DEFAULT '',
  `heuredebut_definie_periode` time NOT NULL DEFAULT '00:00:00',
  `heurefin_definie_periode` time NOT NULL DEFAULT '00:00:00',
  `suivi_definie_periode` tinyint(4) NOT NULL,
  `type_creneaux` varchar(15) NOT NULL,
  `jour_creneau` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_definie_periode`),
  KEY `heures_debut_fin` (`heuredebut_definie_periode`,`heurefin_definie_periode`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Contenu de la table `edt_creneaux`
--

INSERT INTO `edt_creneaux` (`id_definie_periode`, `nom_definie_periode`, `heuredebut_definie_periode`, `heurefin_definie_periode`, `suivi_definie_periode`, `type_creneaux`, `jour_creneau`) VALUES
(1, 'M1', '07:15:00', '08:00:00', 1, 'cours', ''),
(2, 'M2', '08:00:00', '08:45:00', 1, 'cours', ''),
(3, 'M3', '09:20:00', '10:05:00', 1, 'cours', ''),
(4, 'M4', '10:05:00', '10:50:00', 1, 'cours', ''),
(5, 'M5', '10:50:00', '11:35:00', 1, 'cours', ''),
(6, 'M8', '13:20:00', '14:00:00', 1, 'cours', ''),
(7, 'Lunch', '14:00:00', '14:30:00', 1, 'repas', ''),
(8, 'M9', '16:15:00', '17:00:00', 1, 'cours', ''),
(9, 'M10', '17:00:00', '17:45:00', 1, 'cours', ''),
(11, 'P1', '08:45:00', '09:20:00', 1, 'pause', ''),
(12, 'Siesta', '14:30:00', '15:30:00', 1, 'pause', ''),
(13, 'R', '12:20:00', '12:40:00', 1, 'repas', ''),
(14, 'M7', '12:40:00', '13:20:00', 1, 'cours', ''),
(15, 'M6', '11:35:00', '12:20:00', 1, 'cours', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `edt_creneaux_bis`
--

CREATE TABLE IF NOT EXISTS `edt_creneaux_bis` (
  `id_definie_periode` int(11) NOT NULL AUTO_INCREMENT,
  `nom_definie_periode` varchar(10) NOT NULL DEFAULT '',
  `heuredebut_definie_periode` time NOT NULL DEFAULT '00:00:00',
  `heurefin_definie_periode` time NOT NULL DEFAULT '00:00:00',
  `suivi_definie_periode` tinyint(4) NOT NULL,
  `type_creneaux` varchar(15) NOT NULL,
  PRIMARY KEY (`id_definie_periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `edt_creneaux_bis`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_dates_special`
--

CREATE TABLE IF NOT EXISTS `edt_dates_special` (
  `id_edt_date_special` int(11) NOT NULL AUTO_INCREMENT,
  `nom_edt_date_special` varchar(200) NOT NULL,
  `debut_edt_date_special` date NOT NULL,
  `fin_edt_date_special` date NOT NULL,
  PRIMARY KEY (`id_edt_date_special`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `edt_dates_special`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_init`
--

CREATE TABLE IF NOT EXISTS `edt_init` (
  `id_init` int(11) NOT NULL AUTO_INCREMENT,
  `ident_export` varchar(100) NOT NULL,
  `nom_export` varchar(200) NOT NULL,
  `nom_gepi` varchar(200) NOT NULL,
  PRIMARY KEY (`id_init`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `edt_init`
--


-- --------------------------------------------------------

--
-- Structure de la table `edt_semaines`
--

CREATE TABLE IF NOT EXISTS `edt_semaines` (
  `id_edt_semaine` int(11) NOT NULL AUTO_INCREMENT,
  `num_edt_semaine` int(11) NOT NULL DEFAULT '0',
  `type_edt_semaine` varchar(10) NOT NULL DEFAULT '',
  `num_semaines_etab` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_edt_semaine`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

--
-- Contenu de la table `edt_semaines`
--

INSERT INTO `edt_semaines` (`id_edt_semaine`, `num_edt_semaine`, `type_edt_semaine`, `num_semaines_etab`) VALUES
(1, 1, 'A', 0),
(2, 2, 'A', 0),
(3, 3, 'A', 0),
(4, 4, 'A', 0),
(5, 5, 'A', 0),
(6, 6, 'A', 0),
(7, 7, 'A', 0),
(8, 8, 'A', 0),
(9, 9, 'A', 0),
(10, 10, 'A', 0),
(11, 11, 'A', 0),
(12, 12, 'A', 0),
(13, 13, 'A', 0),
(14, 14, 'A', 0),
(15, 15, 'A', 0),
(16, 16, 'A', 0),
(17, 17, 'A', 0),
(18, 18, 'A', 0),
(19, 19, 'A', 0),
(20, 20, 'A', 0),
(21, 21, 'A', 0),
(22, 22, 'A', 0),
(23, 23, 'A', 0),
(24, 24, 'A', 0),
(25, 25, 'A', 0),
(26, 26, 'A', 0),
(27, 27, 'A', 0),
(28, 28, 'A', 0),
(29, 29, 'A', 0),
(30, 30, 'A', 0),
(31, 31, 'A', 0),
(32, 32, 'A', 0),
(33, 33, 'A', 0),
(34, 34, 'A', 0),
(35, 35, 'A', 0),
(36, 36, 'A', 0),
(37, 37, 'A', 0),
(38, 38, 'A', 0),
(39, 39, 'A', 0),
(40, 40, 'A', 0),
(41, 41, 'A', 0),
(42, 42, 'A', 0),
(43, 43, 'A', 0),
(44, 44, 'A', 0),
(45, 45, 'A', 0),
(46, 46, 'A', 0),
(47, 47, 'A', 0),
(48, 48, 'A', 0),
(49, 49, 'A', 0),
(50, 50, 'A', 0),
(51, 51, 'A', 0),
(52, 52, 'A', 0),
(53, 53, 'A', 0);

-- --------------------------------------------------------

--
-- Structure de la table `edt_setting`
--

CREATE TABLE IF NOT EXISTS `edt_setting` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `reglage` varchar(30) NOT NULL,
  `valeur` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `edt_setting`
--

INSERT INTO `edt_setting` (`id`, `reglage`, `valeur`) VALUES
(1, 'nom_creneaux_s', '1'),
(2, 'edt_aff_salle', 'nom'),
(3, 'edt_aff_matiere', 'long'),
(4, 'edt_aff_creneaux', 'noms'),
(5, 'edt_aff_init_infos', 'oui'),
(6, 'edt_aff_couleur', 'nb'),
(7, 'edt_aff_init_infos2', 'oui'),
(8, 'aff_cherche_salle', 'tous'),
(9, 'param_menu_edt', 'mouseover'),
(10, 'scolarite_modif_cours', 'y');

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE IF NOT EXISTS `eleves` (
  `no_gep` varchar(50) NOT NULL COMMENT 'Ancien numero GEP, Numero national de l''eleve',
  `login` varchar(50) NOT NULL COMMENT 'Login de l''eleve, est conserve pour le login utilisateur',
  `nom` varchar(50) NOT NULL COMMENT 'Nom eleve',
  `prenom` varchar(50) NOT NULL COMMENT 'Prenom eleve',
  `sexe` varchar(1) NOT NULL COMMENT 'M ou F',
  `naissance` date NOT NULL COMMENT 'Date de naissance AAAA-MM-JJ',
  `lieu_naissance` varchar(50) NOT NULL DEFAULT '' COMMENT 'Code de Sconet',
  `elenoet` varchar(50) NOT NULL COMMENT 'Numero interne de l''eleve dans l''etablissement',
  `ereno` varchar(50) NOT NULL COMMENT 'Plus utilise',
  `ele_id` varchar(10) NOT NULL DEFAULT '' COMMENT 'cle utilise par Sconet dans ses fichiers xml',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT 'Courriel de l''eleve',
  `id_eleve` int(11) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire autoincremente',
  `date_sortie` datetime DEFAULT NULL COMMENT 'Timestamp de sortie de l''?l?ve de l''?tablissement (fin d''inscription)',
  `mef_code` bigint(20) DEFAULT NULL COMMENT 'code mef de la formation de l''eleve',
  PRIMARY KEY (`id_eleve`),
  KEY `eleves_FI_1` (`mef_code`),
  KEY `I_referenced_j_eleves_classes_FK_1_1` (`login`),
  KEY `I_referenced_responsables2_FK_1_2` (`ele_id`),
  KEY `I_referenced_archivage_ects_FK_1_3` (`no_gep`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Liste des eleves de l''etablissement' AUTO_INCREMENT=91 ;

--
-- Contenu de la table `eleves`
--

INSERT INTO `eleves` (`no_gep`, `login`, `nom`, `prenom`, `sexe`, `naissance`, `lieu_naissance`, `elenoet`, `ereno`, `ele_id`, `email`, `id_eleve`, `date_sortie`, `mef_code`) VALUES
('', 'ngaha', 'NGAHA NJIKE', 'ABEDNEGO', 'M', '1900-01-01', '', '', '', 'e000000001', '', 1, NULL, NULL),
('', 'achah', 'ACHAH NTEMBE', 'LAURENCE', 'M', '1900-01-01', '', '', '', 'e000000002', '', 2, NULL, NULL),
('', 'ajong', 'AJONG ETIENDEM', 'FUANYA', 'M', '1900-01-01', '', '', '', 'e000000003', '', 3, NULL, NULL),
('', 'anaka', 'ANAKA AKU', 'SEDRICK', 'M', '1900-01-01', '', '', '', 'e000000004', '', 4, NULL, NULL),
('', 'nlend', 'DUBOIS NLEND', 'ANICET', 'M', '1900-01-01', '', '', '', 'e000000005', '', 5, NULL, NULL),
('', 'manyaka', 'MANYAKA NGALE KEKA', 'ANJORIN', 'M', '1900-01-01', '', '', '', 'e000000006', '', 6, NULL, NULL),
('', 'apah', 'APAH TAMBIA', 'MORAN', 'M', '1900-01-01', '', '', '', 'e000000007', '', 7, NULL, NULL),
('', 'atanga', 'ATANGA SUH', 'NEELEIN', 'M', '1900-01-01', '', '', '', 'e000000008', '', 8, NULL, NULL),
('', 'atem', 'ATEM ETACHEM', 'KIRAN', 'M', '1900-01-01', '', '', '', 'e000000009', '', 9, NULL, NULL),
('', 'ayuk', 'AYUk MANYOR', 'BERNARD', 'M', '1900-01-01', '', '', '', 'e000000010', '', 10, NULL, NULL),
('', 'etengene', 'AYUK ETENGENENG', 'VICTOR', 'M', '1900-01-01', '', '', '', 'e000000011', '', 11, NULL, NULL),
('', 'baiye', 'BAIYE TABE Jr.', 'STEPHEN Jr.', 'M', '1900-01-01', '', '', '', 'e000000012', '', 12, NULL, NULL),
('', 'bende', 'BENDE BENDE', 'VALENTINE', 'M', '1900-01-01', '', '', '', 'e000000013', '', 13, NULL, NULL),
('', 'besong', 'BESONG BESONG', 'SAMUEL', 'M', '1900-01-01', '', '', '', 'e000000014', '', 14, NULL, NULL),
('', 'bessong', 'BESSONG-OJONG', 'WILLINGTON', 'M', '1900-01-01', '', '', '', 'e000000015', '', 15, NULL, NULL),
('', 'bounoung', 'BOUNOUNG', 'FRANCK', 'M', '1900-01-01', '', '', '', 'e000000016', '', 16, NULL, NULL),
('', 'honge', 'HONGE ESSEMBIEG', 'BRADLEY', 'M', '1900-01-01', '', '', '', 'e000000017', '', 17, NULL, NULL),
('', 'nganjo', 'NGANJO KIMBI', 'BUD SEAN', 'M', '1900-01-01', '', '', '', 'e000000018', '', 18, NULL, NULL),
('', 'jesse', 'CHIBUZOR', 'JESSE', 'M', '1900-01-01', '', '', '', 'e000000019', '', 19, NULL, NULL),
('', 'chifu', 'CHIFU KITA ATEM', 'J. MARK', 'M', '1900-01-01', '', '', '', 'e000000020', '', 20, NULL, NULL),
('', 'djou', 'DJOU', 'ARNOLD KEVIN', 'M', '1900-01-01', '', '', '', 'e000000021', '', 21, NULL, NULL),
('', 'dobgima', 'DOBGIMA', 'NATHANIEL B.N', 'M', '1900-01-01', '', '', '', 'e000000022', '', 22, NULL, NULL),
('', 'nchotieh', 'NCHOTIEH AKOSUNG', 'EBEN', 'M', '1900-01-01', '', '', '', 'e000000023', '', 23, NULL, NULL),
('', 'masango', 'EGBENCHUNG', 'MASANGO', 'M', '1900-01-01', '', '', '', 'e000000024', '', 24, NULL, NULL),
('', 'ekangwo', 'EKANGWO', 'KENZANI ELAD', 'M', '1900-01-01', '', '', '', 'e000000025', '', 25, NULL, NULL),
('', 'elando', 'ELANDO á BENDEH ESEKE', 'BRAXTON', 'M', '1900-01-01', '', '', '', 'e000000026', '', 26, NULL, NULL),
('', 'elonge', 'ELONGE', 'ALLEN ESANGE', 'M', '1900-01-01', '', '', '', 'e000000027', '', 27, NULL, NULL),
('', 'emoh', 'EMOH ANAGHO MONONO', 'BREEZE', 'M', '1900-01-01', '', '', '', 'e000000028', '', 28, NULL, NULL),
('', 'enowebai', 'ENOWEBAI EBAIEGBEENOW', 'VALDES', 'M', '1900-01-01', '', '', '', 'e000000029', '', 29, NULL, NULL),
('', 'epey', 'EPEY EPEY', 'ELVIS', 'M', '1900-01-01', '', '', '', 'e000000030', '', 30, NULL, NULL),
('', 'foadjo', 'FOADJO', 'LELE-YANN JORDAN', 'M', '1900-01-01', '', '', '', 'e000000031', '', 31, NULL, NULL),
('', 'fokou', 'FOKOU NWANBA', 'IVAN LOIC', 'M', '1900-01-01', '', '', '', 'e000000032', '', 32, NULL, NULL),
('', 'veke', 'VEKE TEMBUK T.', 'GABRIELGIFT', 'M', '1900-01-01', '', '', '', 'e000000033', '', 33, NULL, NULL),
('', 'obenan', 'OBENANYANG', 'ARREY HANSON', 'M', '1900-01-01', '', '', '', 'e000000034', '', 34, NULL, NULL),
('', 'lytombe', 'LYTOMBE MBWAYE', 'HARRISON', 'M', '1900-01-01', '', '', '', 'e000000035', '', 35, NULL, NULL),
('', 'babo', 'BABO AIYENOWO', 'HENRY', 'M', '1900-01-01', '', '', '', 'e000000036', '', 36, NULL, NULL),
('', 'tabot', 'TABOT', 'IMMANUEL-KENKAID', 'M', '1900-01-01', '', '', '', 'e000000037', '', 37, NULL, NULL),
('', 'kameni', 'KAMENI  MFEUNGWANG', 'JEAN', 'M', '1900-01-01', '', '', '', 'e000000038', '', 38, NULL, NULL),
('', 'kande', 'KANDE NFORNEH SUH', 'PATRICK', 'M', '1900-01-01', '', '', '', 'e000000039', '', 39, NULL, NULL),
('', 'kemajou', 'KEMAJOU DJONGA', 'FRANK', 'M', '1900-01-01', '', '', '', 'e000000040', '', 40, NULL, NULL),
('', 'kemne', 'KEMNE TALLA', 'DYLAN', 'M', '1900-01-01', '', '', '', 'e000000041', '', 41, NULL, NULL),
('', 'kenne', 'KENNE FOPA', 'ROMIA', 'M', '1900-01-01', '', '', '', 'e000000042', '', 42, NULL, NULL),
('', 'kome', 'KOME NKWELLE', 'NATHANIEL', 'M', '1900-01-01', '', '', '', 'e000000043', '', 43, NULL, NULL),
('', 'kounchou', 'KOUNCHOU KOUNCHOU', 'H.', 'M', '1900-01-01', '', '', '', 'e000000044', '', 44, NULL, NULL),
('', 'mbi', 'MBI KITZITO', 'NCHAFFU', 'M', '1900-01-01', '', '', '', 'e000000045', '', 45, NULL, NULL),
('', 'ndip', 'MBI NDIP ANAMOH', 'Jr', 'M', '1900-01-01', '', '', '', 'e000000046', '', 46, NULL, NULL),
('', 'mbu', 'MBU', 'HUMPHREY', 'M', '1900-01-01', '', '', '', 'e000000047', '', 47, NULL, NULL),
('', 'mekeme', 'MEKEME BEDJOKO', 'MICHEL', 'M', '1900-01-01', '', '', '', 'e000000048', '', 48, NULL, NULL),
('', 'mende', 'MENDI OTTIA', 'ROGER EMILE', 'M', '1900-01-01', '', '', '', 'e000000049', '', 49, NULL, NULL),
('', 'metuge', 'METUGE ELINGESE', 'JUNIOR', 'M', '1900-01-01', '', '', '', 'e000000050', '', 50, NULL, NULL),
('', 'mieguim', 'MIEGUIM', 'MARC KEVIN', 'M', '1900-01-01', '', '', '', 'e000000051', '', 51, NULL, NULL),
('', 'mocto', 'MOCTO SOP', 'JAMEL SHARIF', 'M', '1900-01-01', '', '', '', 'e000000052', '', 52, NULL, NULL),
('', 'mokube', 'MOKUBE SAKWE', 'EYALO', 'M', '1900-01-01', '', '', '', 'e000000053', '', 53, NULL, NULL),
('', 'mokwe', 'MOKWE TAH', 'CLUVETTE', 'M', '1900-01-01', '', '', '', 'e000000054', '', 54, NULL, NULL),
('', 'nekongoh', 'NEKONGOH MUKETE', 'NOEL', 'M', '1900-01-01', '', '', '', 'e000000055', '', 55, NULL, NULL),
('', 'ngalame', 'NGALAME MESANGO', 'SIMON', 'M', '1900-01-01', '', '', '', 'e000000056', '', 56, NULL, NULL),
('', 'ngando', 'Ngando Ngole', 'Ferdinand', 'M', '1900-01-01', '', '', '', 'e000000057', '', 57, NULL, NULL),
('', 'ngangmi', 'NGANGMI NGU', 'MILLENIAN', 'M', '1900-01-01', '', '', '', 'e000000058', '', 58, NULL, NULL),
('', 'ngoe', 'NGOE', 'FABIAN SONESTONE', 'M', '1900-01-01', '', '', '', 'e000000059', '', 59, NULL, NULL),
('', 'ngouh', 'NGOUH', 'LAYOU A.', 'M', '1900-01-01', '', '', '', 'e000000060', '', 60, NULL, NULL),
('', 'ngounou', 'NGOUNOU ESEYE', 'EMMANUEL  Jr.', 'M', '1900-01-01', '', '', '', 'e000000061', '', 61, NULL, NULL),
('', 'ngulefac', 'NGULEFAC', 'KLUIVERT', 'M', '1900-01-01', '', '', '', 'e000000062', '', 62, NULL, NULL),
('', 'njabe', 'NJABE NJABE NKWELLE', 'JACKSON', 'M', '1900-01-01', '', '', '', 'e000000063', '', 63, NULL, NULL),
('', 'njoya', 'NJOYA ATSAMA', 'DONALD ULRICH', 'M', '1900-01-01', '', '', '', 'e000000064', '', 64, NULL, NULL),
('', 'njume', 'NJUME', 'ISAAC LAMBE Jr', 'M', '1900-01-01', '', '', '', 'e000000065', '', 65, NULL, NULL),
('', 'nkemlebe', 'NKEMLEBE', 'SADEH JONG', 'M', '1900-01-01', '', '', '', 'e000000066', '', 66, NULL, NULL),
('', 'etaka', 'NKONGHO-ETAKA', 'FELICIEN KOME', 'M', '1900-01-01', '', '', '', 'e000000067', '', 67, NULL, NULL),
('', 'mezation', 'NOUMBOUWO MEZATIO', 'CHRIST', 'M', '1900-01-01', '', '', '', 'e000000068', '', 68, NULL, NULL),
('', 'ntung', 'NTUNG NJUKANG', 'NJUNGWOH', 'M', '1900-01-01', '', '', '', 'e000000069', '', 69, NULL, NULL),
('', 'nwanja', 'NWANJA ETEBE', 'MIKE KEVIN', 'M', '1900-01-01', '', '', '', 'e000000070', '', 70, NULL, NULL),
('', 'nyingcho', 'NYINGCHO', 'NESTOR', 'M', '1900-01-01', '', '', '', 'e000000071', '', 71, NULL, NULL),
('', 'nyoki', 'NYOKI MEDRANA', 'BENTZ EFESOA', 'M', '1900-01-01', '', '', '', 'e000000072', '', 72, NULL, NULL),
('', 'obidimma', 'OBIDIMMA', 'JOSEPHAT', 'M', '1900-01-01', '', '', '', 'e000000073', '', 73, NULL, NULL),
('', 'sama', 'SAMA AYOUND', 'PAUL', 'M', '1900-01-01', '', '', '', 'e000000074', '', 74, NULL, NULL),
('', 'mbah', 'MBAH GODLOVE', 'PETER LARRY', 'M', '1900-01-01', '', '', '', 'e000000075', '', 75, NULL, NULL),
('', 'lobe', 'LOBE NAMA', 'ROLAND', 'M', '1900-01-01', '', '', '', 'e000000076', '', 76, NULL, NULL),
('', 'agbor', 'AGBOR A.', 'RYAN-DAVID', 'M', '1900-01-01', '', '', '', 'e000000077', '', 77, NULL, NULL),
('', 'siyapze', 'SIYAPZE', 'AUREL JORDY', 'M', '1900-01-01', '', '', '', 'e000000078', '', 78, NULL, NULL),
('', 'sone', 'SONE NNOKO NGAAJE', 'STEVE Jr.', 'M', '1900-01-01', '', '', '', 'e000000079', '', 79, NULL, NULL),
('', 'amuruwa', 'SUNDAY AMURUWA', 'IRENDE', 'M', '1900-01-01', '', '', '', 'e000000080', '', 80, NULL, NULL),
('', 'tabe', 'TABE ACHUO', 'DENIS DEON', 'M', '1900-01-01', '', '', '', 'e000000081', '', 81, NULL, NULL),
('', 'takang', 'TAKANG EBOT EKULE', 'JUNIOR', 'M', '1900-01-01', '', '', '', 'e000000082', '', 82, NULL, NULL),
('', 'tamon', 'TAMON TAMON', 'OBRINE', 'M', '1900-01-01', '', '', '', 'e000000083', '', 83, NULL, NULL),
('', 'tassoko', 'TASSOKO NUNGO', 'HERMAN', 'M', '1900-01-01', '', '', '', 'e000000084', '', 84, NULL, NULL),
('', 'tchamy', 'TCHAMY KEMADJOU', 'MARTIAL', 'M', '1900-01-01', '', '', '', 'e000000085', '', 85, NULL, NULL),
('', 'tchangou', 'TCHANGOU', 'DILANE MAXIME', 'M', '1900-01-01', '', '', '', 'e000000086', '', 86, NULL, NULL),
('', 'teghoue', 'TEGHOUE NOUANENGUE', 'JUDE', 'M', '1900-01-01', '', '', '', 'e000000087', '', 87, NULL, NULL),
('', 'tita', 'TITA SCHAMSU', 'DEEN', 'M', '1900-01-01', '', '', '', 'e000000088', '', 88, NULL, NULL),
('', 'ukatang', 'UKATANG ABADUM', 'GABRIEL', 'M', '1900-01-01', '', '', '', 'e000000089', '', 89, NULL, NULL),
('', 'abong', 'ABONG AGWANADE', 'COLBERT', 'M', '1900-01-01', '', '', '', 'e000000090', '', 90, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `eleves_groupes_settings`
--

CREATE TABLE IF NOT EXISTS `eleves_groupes_settings` (
  `login` varchar(50) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`id_groupe`,`login`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `eleves_groupes_settings`
--


-- --------------------------------------------------------

--
-- Structure de la table `etablissements`
--

CREATE TABLE IF NOT EXISTS `etablissements` (
  `id` char(8) NOT NULL DEFAULT '',
  `nom` char(50) NOT NULL DEFAULT '',
  `niveau` char(50) NOT NULL DEFAULT '',
  `type` char(50) NOT NULL DEFAULT '',
  `cp` int(10) NOT NULL DEFAULT '0',
  `ville` char(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `etablissements`
--

INSERT INTO `etablissements` (`id`, `nom`, `niveau`, `type`, `cp`, `ville`) VALUES
('999', 'étranger', 'aucun', 'aucun', 999, '');

-- --------------------------------------------------------

--
-- Structure de la table `etiquettes_formats`
--

CREATE TABLE IF NOT EXISTS `etiquettes_formats` (
  `id_etiquette_format` int(11) NOT NULL AUTO_INCREMENT,
  `nom_etiquette_format` varchar(150) NOT NULL,
  `xcote_etiquette_format` float NOT NULL,
  `ycote_etiquette_format` float NOT NULL,
  `espacementx_etiquette_format` float NOT NULL,
  `espacementy_etiquette_format` float NOT NULL,
  `largeur_etiquette_format` float NOT NULL,
  `hauteur_etiquette_format` float NOT NULL,
  `nbl_etiquette_format` tinyint(4) NOT NULL,
  `nbh_etiquette_format` tinyint(4) NOT NULL,
  PRIMARY KEY (`id_etiquette_format`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `etiquettes_formats`
--

INSERT INTO `etiquettes_formats` (`id_etiquette_format`, `nom_etiquette_format`, `xcote_etiquette_format`, `ycote_etiquette_format`, `espacementx_etiquette_format`, `espacementy_etiquette_format`, `largeur_etiquette_format`, `hauteur_etiquette_format`, `nbl_etiquette_format`, `nbh_etiquette_format`) VALUES
(1, 'Avery - A4 - 63,5 x 33,9 mm', 2, 2, 5, 5, 63.5, 33, 3, 8);

-- --------------------------------------------------------

--
-- Structure de la table `ex_classes`
--

CREATE TABLE IF NOT EXISTS `ex_classes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_exam` int(11) unsigned NOT NULL,
  `id_classe` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ex_classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `ex_examens`
--

CREATE TABLE IF NOT EXISTS `ex_examens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `etat` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ex_examens`
--


-- --------------------------------------------------------

--
-- Structure de la table `ex_groupes`
--

CREATE TABLE IF NOT EXISTS `ex_groupes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_exam` int(11) unsigned NOT NULL,
  `matiere` varchar(50) NOT NULL,
  `id_groupe` int(11) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `id_dev` int(11) NOT NULL DEFAULT '0',
  `valeur` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ex_groupes`
--


-- --------------------------------------------------------

--
-- Structure de la table `ex_matieres`
--

CREATE TABLE IF NOT EXISTS `ex_matieres` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_exam` int(11) unsigned NOT NULL,
  `matiere` varchar(255) NOT NULL,
  `coef` decimal(3,1) NOT NULL DEFAULT '1.0',
  `bonus` char(1) NOT NULL DEFAULT 'n',
  `ordre` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ex_matieres`
--


-- --------------------------------------------------------

--
-- Structure de la table `ex_notes`
--

CREATE TABLE IF NOT EXISTS `ex_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_ex_grp` int(11) unsigned NOT NULL,
  `login` varchar(255) NOT NULL DEFAULT '',
  `note` float(10,1) NOT NULL DEFAULT '0.0',
  `statut` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ex_notes`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_affichages`
--

CREATE TABLE IF NOT EXISTS `gc_affichages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_aff` int(11) unsigned NOT NULL,
  `id_req` int(11) unsigned NOT NULL,
  `projet` varchar(255) NOT NULL,
  `nom_requete` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `valeur` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_affichages`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_divisions`
--

CREATE TABLE IF NOT EXISTS `gc_divisions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `projet` varchar(255) NOT NULL,
  `id_classe` smallint(6) unsigned NOT NULL,
  `classe` varchar(100) NOT NULL DEFAULT '',
  `statut` enum('actuelle','future','red','arriv') NOT NULL DEFAULT 'future',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_divisions`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_eleves_options`
--

CREATE TABLE IF NOT EXISTS `gc_eleves_options` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `profil` enum('GC','C','RAS','B','TB') NOT NULL DEFAULT 'RAS',
  `moy` varchar(255) NOT NULL,
  `nb_absences` varchar(255) NOT NULL,
  `non_justifie` varchar(255) NOT NULL,
  `nb_retards` varchar(255) NOT NULL,
  `projet` varchar(255) NOT NULL,
  `id_classe_actuelle` varchar(255) NOT NULL,
  `classe_future` varchar(255) NOT NULL,
  `liste_opt` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_eleves_options`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_ele_arriv_red`
--

CREATE TABLE IF NOT EXISTS `gc_ele_arriv_red` (
  `login` varchar(255) NOT NULL,
  `statut` enum('Arriv','Red') NOT NULL,
  `projet` varchar(255) NOT NULL,
  PRIMARY KEY (`login`,`projet`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `gc_ele_arriv_red`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_options`
--

CREATE TABLE IF NOT EXISTS `gc_options` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `projet` varchar(255) NOT NULL,
  `opt` varchar(255) NOT NULL,
  `type` enum('lv1','lv2','lv3','autre') NOT NULL,
  `obligatoire` enum('o','n') NOT NULL,
  `exclusive` smallint(6) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_options`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_options_classes`
--

CREATE TABLE IF NOT EXISTS `gc_options_classes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `projet` varchar(255) NOT NULL,
  `opt_exclue` varchar(255) NOT NULL,
  `classe_future` varchar(255) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gc_options_classes`
--


-- --------------------------------------------------------

--
-- Structure de la table `gc_projets`
--

CREATE TABLE IF NOT EXISTS `gc_projets` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `projet` varchar(255) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `gc_projets`
--

INSERT INTO `gc_projets` (`id`, `projet`, `commentaire`) VALUES
(1, 'Sasseproject', '');

-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

CREATE TABLE IF NOT EXISTS `groupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `recalcul_rang` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_name` (`id`,`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `groupes`
--

INSERT INTO `groupes` (`id`, `name`, `description`, `recalcul_rang`) VALUES
(1, 'Maths', 'Maths.', 'nyyyyyyyy'),
(2, 'French', 'French', 'nyyyyyyyy'),
(3, 'English_language', 'English Lang.', 'nyyyyyyyy'),
(4, 'Computer', 'Information Technology', '');

-- --------------------------------------------------------

--
-- Structure de la table `horaires_etablissement`
--

CREATE TABLE IF NOT EXISTS `horaires_etablissement` (
  `id_horaire_etablissement` int(11) NOT NULL AUTO_INCREMENT,
  `date_horaire_etablissement` date NOT NULL,
  `jour_horaire_etablissement` varchar(15) NOT NULL,
  `ouverture_horaire_etablissement` time NOT NULL,
  `fermeture_horaire_etablissement` time NOT NULL,
  `pause_horaire_etablissement` time NOT NULL,
  `ouvert_horaire_etablissement` tinyint(4) NOT NULL,
  PRIMARY KEY (`id_horaire_etablissement`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `horaires_etablissement`
--

INSERT INTO `horaires_etablissement` (`id_horaire_etablissement`, `date_horaire_etablissement`, `jour_horaire_etablissement`, `ouverture_horaire_etablissement`, `fermeture_horaire_etablissement`, `pause_horaire_etablissement`, `ouvert_horaire_etablissement`) VALUES
(1, '0000-00-00', 'lundi', '07:15:00', '17:45:00', '02:25:00', 1),
(2, '0000-00-00', 'mardi', '08:00:00', '17:30:00', '00:45:00', 1),
(3, '0000-00-00', 'mercredi', '08:00:00', '12:00:00', '00:00:00', 1),
(4, '0000-00-00', 'jeudi', '08:00:00', '17:30:00', '00:45:00', 1),
(5, '0000-00-00', 'vendredi', '08:00:00', '17:30:00', '00:45:00', 1);

-- --------------------------------------------------------

--
-- Structure de la table `infos_actions`
--

CREATE TABLE IF NOT EXISTS `infos_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_titre` (`id`,`titre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `infos_actions`
--


-- --------------------------------------------------------

--
-- Structure de la table `infos_actions_destinataires`
--

CREATE TABLE IF NOT EXISTS `infos_actions_destinataires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_info` int(11) NOT NULL,
  `nature` enum('statut','individu') DEFAULT 'individu',
  `valeur` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_info` (`id_info`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `infos_actions_destinataires`
--


-- --------------------------------------------------------

--
-- Structure de la table `inscription_items`
--

CREATE TABLE IF NOT EXISTS `inscription_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(10) NOT NULL DEFAULT '',
  `heure` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `inscription_items`
--


-- --------------------------------------------------------

--
-- Structure de la table `inscription_j_login_items`
--

CREATE TABLE IF NOT EXISTS `inscription_j_login_items` (
  `login` varchar(20) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `inscription_j_login_items`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aidcateg_super_gestionnaires`
--

CREATE TABLE IF NOT EXISTS `j_aidcateg_super_gestionnaires` (
  `indice_aid` int(11) NOT NULL,
  `id_utilisateur` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aidcateg_super_gestionnaires`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aidcateg_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `j_aidcateg_utilisateurs` (
  `indice_aid` int(11) NOT NULL,
  `id_utilisateur` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aidcateg_utilisateurs`
--

INSERT INTO `j_aidcateg_utilisateurs` (`indice_aid`, `id_utilisateur`) VALUES
(1, 'ekah');

-- --------------------------------------------------------

--
-- Structure de la table `j_aid_eleves`
--

CREATE TABLE IF NOT EXISTS `j_aid_eleves` (
  `id_aid` varchar(100) NOT NULL DEFAULT '',
  `login` varchar(60) NOT NULL DEFAULT '',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_aid`,`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aid_eleves`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aid_eleves_resp`
--

CREATE TABLE IF NOT EXISTS `j_aid_eleves_resp` (
  `id_aid` varchar(100) NOT NULL DEFAULT '',
  `login` varchar(60) NOT NULL DEFAULT '',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_aid`,`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aid_eleves_resp`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aid_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `j_aid_utilisateurs` (
  `id_aid` varchar(100) NOT NULL DEFAULT '',
  `id_utilisateur` varchar(50) NOT NULL DEFAULT '',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_aid`,`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aid_utilisateurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_aid_utilisateurs_gest`
--

CREATE TABLE IF NOT EXISTS `j_aid_utilisateurs_gest` (
  `id_aid` varchar(100) NOT NULL DEFAULT '',
  `id_utilisateur` varchar(50) NOT NULL DEFAULT '',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_aid`,`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_aid_utilisateurs_gest`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_classe_matiere`
--

CREATE TABLE IF NOT EXISTS `j_classe_matiere` (
  `classe` smallint(6) NOT NULL,
  `matiere` varchar(255) NOT NULL,
  `coefficient` int(11) NOT NULL,
  `professeur` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_classe_matiere`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_classes`
--

CREATE TABLE IF NOT EXISTS `j_eleves_classes` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_classe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `rang` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login`,`id_classe`,`periode`),
  KEY `id_classe` (`id_classe`),
  KEY `login_periode` (`login`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_classes`
--

INSERT INTO `j_eleves_classes` (`login`, `id_classe`, `periode`, `rang`) VALUES
('atanga', 1, 2, 0),
('ngouh', 1, 2, 0),
('kemajou', 1, 1, 0),
('atanga', 1, 1, 0),
('ngouh', 1, 1, 0),
('kande', 1, 3, 0),
('apah', 1, 3, 0),
('ngoe', 1, 3, 0),
('kande', 1, 2, 0),
('apah', 1, 2, 0),
('ngoe', 1, 2, 0),
('kande', 1, 1, 0),
('apah', 1, 1, 0),
('ngoe', 1, 1, 0),
('kameni', 1, 3, 0),
('anaka', 1, 3, 0),
('nganjo', 1, 3, 0),
('kameni', 1, 2, 0),
('anaka', 1, 2, 0),
('nganjo', 1, 2, 0),
('kameni', 1, 1, 0),
('anaka', 1, 1, 0),
('nganjo', 1, 1, 0),
('honge', 1, 3, 0),
('ajong', 1, 3, 0),
('ngangmi', 1, 3, 0),
('honge', 1, 2, 0),
('ajong', 1, 2, 0),
('ngangmi', 1, 2, 0),
('honge', 1, 1, 0),
('ajong', 1, 1, 0),
('ngangmi', 1, 1, 0),
('fokou', 1, 3, 0),
('agbor', 1, 3, 0),
('ngando', 1, 3, 0),
('fokou', 1, 2, 0),
('agbor', 1, 2, 0),
('ngando', 1, 2, 0),
('fokou', 1, 1, 0),
('agbor', 1, 1, 0),
('ngando', 1, 1, 0),
('foadjo', 1, 3, 0),
('achah', 1, 3, 0),
('ngalame', 1, 3, 0),
('foadjo', 1, 2, 0),
('achah', 1, 2, 0),
('ngalame', 1, 2, 0),
('foadjo', 1, 1, 0),
('achah', 1, 1, 0),
('ngalame', 1, 1, 0),
('epey', 1, 3, 0),
('siyapze', 1, 3, 0),
('ngaha', 1, 3, 0),
('epey', 1, 2, 0),
('siyapze', 1, 2, 0),
('ngaha', 1, 2, 0),
('epey', 1, 1, 0),
('siyapze', 1, 1, 0),
('ngaha', 1, 1, 0),
('sama', 1, 3, 0),
('enowebai', 1, 3, 0),
('sama', 1, 2, 0),
('nekongoh', 1, 3, 0),
('sama', 1, 1, 0),
('enowebai', 1, 2, 0),
('obidimma', 1, 3, 0),
('nekongoh', 1, 2, 0),
('obidimma', 1, 2, 0),
('enowebai', 1, 1, 0),
('obidimma', 1, 1, 0),
('nekongoh', 1, 1, 0),
('emoh', 1, 3, 0),
('obenan', 1, 3, 0),
('nchotieh', 1, 3, 0),
('emoh', 1, 2, 0),
('obenan', 1, 2, 0),
('emoh', 1, 1, 0),
('obenan', 1, 1, 0),
('nchotieh', 1, 2, 0),
('elonge', 1, 3, 0),
('nyoki', 1, 3, 0),
('elonge', 1, 2, 0),
('nyoki', 1, 2, 0),
('nchotieh', 1, 1, 0),
('elonge', 1, 1, 0),
('nyoki', 1, 1, 0),
('mokwe', 1, 3, 0),
('elando', 1, 3, 0),
('nyingcho', 1, 3, 0),
('mokwe', 1, 2, 0),
('elando', 1, 2, 0),
('nyingcho', 1, 2, 0),
('mokwe', 1, 1, 0),
('elando', 1, 1, 0),
('nyingcho', 1, 1, 0),
('mokube', 1, 3, 0),
('ekangwo', 1, 3, 0),
('nwanja', 1, 3, 0),
('mokube', 1, 2, 0),
('ekangwo', 1, 2, 0),
('nwanja', 1, 2, 0),
('mokube', 1, 1, 0),
('ekangwo', 1, 1, 0),
('nwanja', 1, 1, 0),
('mocto', 1, 3, 0),
('masango', 1, 3, 0),
('ntung', 1, 3, 0),
('mocto', 1, 2, 0),
('masango', 1, 2, 0),
('ntung', 1, 2, 0),
('mocto', 1, 1, 0),
('masango', 1, 1, 0),
('ntung', 1, 1, 0),
('mieguim', 1, 3, 0),
('nlend', 1, 3, 0),
('mezation', 1, 3, 0),
('mieguim', 1, 2, 0),
('nlend', 1, 2, 0),
('mezation', 1, 2, 0),
('mieguim', 1, 1, 0),
('nlend', 1, 1, 0),
('mezation', 1, 1, 0),
('metuge', 1, 3, 0),
('dobgima', 1, 3, 0),
('etaka', 1, 3, 0),
('metuge', 1, 2, 0),
('dobgima', 1, 2, 0),
('etaka', 1, 2, 0),
('metuge', 1, 1, 0),
('dobgima', 1, 1, 0),
('etaka', 1, 1, 0),
('mende', 1, 3, 0),
('djou', 1, 3, 0),
('veke', 1, 3, 0),
('mende', 1, 2, 0),
('djou', 1, 2, 0),
('veke', 1, 2, 0),
('mende', 1, 1, 0),
('djou', 1, 1, 0),
('veke', 1, 1, 0),
('mekeme', 1, 3, 0),
('chifu', 1, 3, 0),
('ukatang', 1, 3, 0),
('mekeme', 1, 2, 0),
('chifu', 1, 2, 0),
('ukatang', 1, 2, 0),
('mekeme', 1, 1, 0),
('chifu', 1, 1, 0),
('ukatang', 1, 1, 0),
('mbu', 1, 3, 0),
('jesse', 1, 3, 0),
('tita', 1, 3, 0),
('mbu', 1, 2, 0),
('jesse', 1, 2, 0),
('tita', 1, 2, 0),
('mbu', 1, 1, 0),
('jesse', 1, 1, 0),
('tita', 1, 1, 0),
('ndip', 1, 3, 0),
('teghoue', 1, 3, 0),
('ndip', 1, 2, 0),
('bounoung', 1, 3, 0),
('teghoue', 1, 2, 0),
('ndip', 1, 1, 0),
('teghoue', 1, 1, 0),
('mbi', 1, 3, 0),
('bounoung', 1, 2, 0),
('tchangou', 1, 3, 0),
('mbi', 1, 2, 0),
('tchangou', 1, 2, 0),
('mbi', 1, 1, 0),
('bounoung', 1, 1, 0),
('tchangou', 1, 1, 0),
('mbah', 1, 3, 0),
('bessong', 1, 3, 0),
('tchamy', 1, 3, 0),
('mbah', 1, 2, 0),
('bessong', 1, 2, 0),
('tchamy', 1, 2, 0),
('mbah', 1, 1, 0),
('bessong', 1, 1, 0),
('tchamy', 1, 1, 0),
('manyaka', 1, 3, 0),
('besong', 1, 3, 0),
('tassoko', 1, 3, 0),
('nkemlebe', 1, 3, 0),
('manyaka', 1, 2, 0),
('besong', 1, 2, 0),
('tassoko', 1, 2, 0),
('manyaka', 1, 1, 0),
('besong', 1, 1, 0),
('tassoko', 1, 1, 0),
('nkemlebe', 1, 2, 0),
('lytombe', 1, 3, 0),
('bende', 1, 3, 0),
('tamon', 1, 3, 0),
('lytombe', 1, 2, 0),
('bende', 1, 2, 0),
('tamon', 1, 2, 0),
('nkemlebe', 1, 1, 0),
('lytombe', 1, 1, 0),
('bende', 1, 1, 0),
('tamon', 1, 1, 0),
('njume', 1, 3, 0),
('lobe', 1, 3, 0),
('baiye', 1, 3, 0),
('takang', 1, 3, 0),
('njume', 1, 2, 0),
('lobe', 1, 2, 0),
('baiye', 1, 2, 0),
('takang', 1, 2, 0),
('njume', 1, 1, 0),
('lobe', 1, 1, 0),
('baiye', 1, 1, 0),
('takang', 1, 1, 0),
('njoya', 1, 3, 0),
('babo', 1, 3, 0),
('tabot', 1, 3, 0),
('njoya', 1, 2, 0),
('kounchou', 1, 3, 0),
('babo', 1, 2, 0),
('tabot', 1, 2, 0),
('njoya', 1, 1, 0),
('kounchou', 1, 2, 0),
('babo', 1, 1, 0),
('tabot', 1, 1, 0),
('njabe', 1, 3, 0),
('ayuk', 1, 3, 0),
('tabe', 1, 3, 0),
('njabe', 1, 2, 0),
('kounchou', 1, 1, 0),
('ayuk', 1, 2, 0),
('tabe', 1, 2, 0),
('njabe', 1, 1, 0),
('kome', 1, 3, 0),
('ayuk', 1, 1, 0),
('tabe', 1, 1, 0),
('kome', 1, 2, 0),
('amuruwa', 1, 3, 0),
('ngulefac', 1, 3, 0),
('kome', 1, 1, 0),
('etengene', 1, 3, 0),
('amuruwa', 1, 2, 0),
('kenne', 1, 3, 0),
('amuruwa', 1, 1, 0),
('ngulefac', 1, 2, 0),
('kenne', 1, 2, 0),
('etengene', 1, 2, 0),
('sone', 1, 3, 0),
('kenne', 1, 1, 0),
('sone', 1, 2, 0),
('ngulefac', 1, 1, 0),
('kemne', 1, 3, 0),
('etengene', 1, 1, 0),
('sone', 1, 1, 0),
('ngounou', 1, 3, 0),
('kemne', 1, 2, 0),
('atem', 1, 3, 0),
('abong', 2, 3, 0),
('ngounou', 1, 2, 0),
('kemne', 1, 1, 0),
('atem', 1, 2, 0),
('abong', 2, 2, 0),
('ngounou', 1, 1, 0),
('kemajou', 1, 3, 0),
('atem', 1, 1, 0),
('abong', 2, 1, 0),
('ngouh', 1, 3, 0),
('kemajou', 1, 2, 0),
('atanga', 1, 3, 0);

-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_cpe`
--

CREATE TABLE IF NOT EXISTS `j_eleves_cpe` (
  `e_login` varchar(50) NOT NULL DEFAULT '',
  `cpe_login` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`e_login`,`cpe_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_cpe`
--

INSERT INTO `j_eleves_cpe` (`e_login`, `cpe_login`) VALUES
('achah', ''),
('agbor', ''),
('ajong', ''),
('amuruwa', ''),
('anaka', ''),
('apah', ''),
('atanga', ''),
('atem', ''),
('ayuk', ''),
('babo', ''),
('baiye', ''),
('bende', ''),
('besong', ''),
('bessong', ''),
('bounoung', ''),
('chifu', ''),
('djou', ''),
('dobgima', ''),
('ekangwo', ''),
('elando', ''),
('elonge', ''),
('emoh', ''),
('enowebai', ''),
('epey', ''),
('etaka', ''),
('etengene', ''),
('foadjo', ''),
('fokou', ''),
('honge', ''),
('jesse', ''),
('kameni', ''),
('kande', ''),
('kemajou', ''),
('kemne', ''),
('kenne', ''),
('kome', ''),
('kounchou', ''),
('lobe', ''),
('lytombe', ''),
('manyaka', ''),
('masango', ''),
('mbah', ''),
('mbi', ''),
('mbu', ''),
('mekeme', ''),
('mende', ''),
('metuge', ''),
('mezation', ''),
('mieguim', ''),
('mocto', ''),
('mokube', ''),
('mokwe', ''),
('nchotieh', ''),
('ndip', ''),
('nekongoh', ''),
('ngaha', ''),
('ngalame', ''),
('ngando', ''),
('ngangmi', ''),
('nganjo', ''),
('ngoe', ''),
('ngouh', ''),
('ngounou', ''),
('ngulefac', ''),
('njabe', ''),
('njoya', ''),
('njume', ''),
('nkemlebe', ''),
('nlend', ''),
('ntung', ''),
('nwanja', ''),
('nyingcho', ''),
('nyoki', ''),
('obenan', ''),
('obidimma', ''),
('sama', ''),
('siyapze', ''),
('sone', ''),
('tabe', ''),
('tabot', ''),
('takang', ''),
('tamon', ''),
('tassoko', ''),
('tchamy', ''),
('tchangou', ''),
('teghoue', ''),
('tita', ''),
('ukatang', ''),
('veke', '');

-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_etablissements`
--

CREATE TABLE IF NOT EXISTS `j_eleves_etablissements` (
  `id_eleve` varchar(50) NOT NULL DEFAULT '',
  `id_etablissement` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_eleve`,`id_etablissement`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_etablissements`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_groupes`
--

CREATE TABLE IF NOT EXISTS `j_eleves_groupes` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_groupe`,`login`,`periode`),
  KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_groupes`
--

INSERT INTO `j_eleves_groupes` (`login`, `id_groupe`, `periode`) VALUES
('achah', 1, 1),
('achah', 1, 2),
('achah', 1, 3),
('agbor', 1, 1),
('agbor', 1, 2),
('agbor', 1, 3),
('ajong', 1, 1),
('ajong', 1, 2),
('ajong', 1, 3),
('amuruwa', 1, 1),
('amuruwa', 1, 2),
('amuruwa', 1, 3),
('anaka', 1, 1),
('anaka', 1, 2),
('anaka', 1, 3),
('apah', 1, 1),
('apah', 1, 2),
('apah', 1, 3),
('atanga', 1, 1),
('atanga', 1, 2),
('atanga', 1, 3),
('atem', 1, 1),
('atem', 1, 2),
('atem', 1, 3),
('ayuk', 1, 1),
('ayuk', 1, 2),
('ayuk', 1, 3),
('babo', 1, 1),
('babo', 1, 2),
('babo', 1, 3),
('baiye', 1, 1),
('baiye', 1, 2),
('baiye', 1, 3),
('bende', 1, 1),
('bende', 1, 2),
('bende', 1, 3),
('besong', 1, 1),
('besong', 1, 2),
('besong', 1, 3),
('bessong', 1, 1),
('bessong', 1, 2),
('bessong', 1, 3),
('bounoung', 1, 1),
('bounoung', 1, 2),
('bounoung', 1, 3),
('chifu', 1, 1),
('chifu', 1, 2),
('chifu', 1, 3),
('djou', 1, 1),
('djou', 1, 2),
('djou', 1, 3),
('dobgima', 1, 1),
('dobgima', 1, 2),
('dobgima', 1, 3),
('ekangwo', 1, 1),
('ekangwo', 1, 2),
('ekangwo', 1, 3),
('elando', 1, 1),
('elando', 1, 2),
('elando', 1, 3),
('elonge', 1, 1),
('elonge', 1, 2),
('elonge', 1, 3),
('emoh', 1, 1),
('emoh', 1, 2),
('emoh', 1, 3),
('enowebai', 1, 1),
('enowebai', 1, 2),
('enowebai', 1, 3),
('epey', 1, 1),
('epey', 1, 2),
('epey', 1, 3),
('etaka', 1, 1),
('etaka', 1, 2),
('etaka', 1, 3),
('etengene', 1, 1),
('etengene', 1, 2),
('etengene', 1, 3),
('foadjo', 1, 1),
('foadjo', 1, 2),
('foadjo', 1, 3),
('fokou', 1, 1),
('fokou', 1, 2),
('fokou', 1, 3),
('honge', 1, 1),
('honge', 1, 2),
('honge', 1, 3),
('jesse', 1, 1),
('jesse', 1, 2),
('jesse', 1, 3),
('kameni', 1, 1),
('kameni', 1, 2),
('kameni', 1, 3),
('kande', 1, 1),
('kande', 1, 2),
('kande', 1, 3),
('kemajou', 1, 1),
('kemajou', 1, 2),
('kemajou', 1, 3),
('kemne', 1, 1),
('kemne', 1, 2),
('kemne', 1, 3),
('kenne', 1, 1),
('kenne', 1, 2),
('kenne', 1, 3),
('kome', 1, 1),
('kome', 1, 2),
('kome', 1, 3),
('kounchou', 1, 1),
('kounchou', 1, 2),
('kounchou', 1, 3),
('lobe', 1, 1),
('lobe', 1, 2),
('lobe', 1, 3),
('lytombe', 1, 1),
('lytombe', 1, 2),
('lytombe', 1, 3),
('manyaka', 1, 1),
('manyaka', 1, 2),
('manyaka', 1, 3),
('masango', 1, 1),
('masango', 1, 2),
('masango', 1, 3),
('mbah', 1, 1),
('mbah', 1, 2),
('mbah', 1, 3),
('mbi', 1, 1),
('mbi', 1, 2),
('mbi', 1, 3),
('mbu', 1, 1),
('mbu', 1, 2),
('mbu', 1, 3),
('mekeme', 1, 1),
('mekeme', 1, 2),
('mekeme', 1, 3),
('mende', 1, 1),
('mende', 1, 2),
('mende', 1, 3),
('metuge', 1, 1),
('metuge', 1, 2),
('metuge', 1, 3),
('mezation', 1, 1),
('mezation', 1, 2),
('mezation', 1, 3),
('mieguim', 1, 1),
('mieguim', 1, 2),
('mieguim', 1, 3),
('mocto', 1, 1),
('mocto', 1, 2),
('mocto', 1, 3),
('mokube', 1, 1),
('mokube', 1, 2),
('mokube', 1, 3),
('mokwe', 1, 1),
('mokwe', 1, 2),
('mokwe', 1, 3),
('nchotieh', 1, 1),
('nchotieh', 1, 2),
('nchotieh', 1, 3),
('ndip', 1, 1),
('ndip', 1, 2),
('ndip', 1, 3),
('nekongoh', 1, 1),
('nekongoh', 1, 2),
('nekongoh', 1, 3),
('ngaha', 1, 1),
('ngaha', 1, 2),
('ngaha', 1, 3),
('ngalame', 1, 1),
('ngalame', 1, 2),
('ngalame', 1, 3),
('ngando', 1, 1),
('ngando', 1, 2),
('ngando', 1, 3),
('ngangmi', 1, 1),
('ngangmi', 1, 2),
('ngangmi', 1, 3),
('nganjo', 1, 1),
('nganjo', 1, 2),
('nganjo', 1, 3),
('ngoe', 1, 1),
('ngoe', 1, 2),
('ngoe', 1, 3),
('ngouh', 1, 1),
('ngouh', 1, 2),
('ngouh', 1, 3),
('ngounou', 1, 1),
('ngounou', 1, 2),
('ngounou', 1, 3),
('ngulefac', 1, 1),
('ngulefac', 1, 2),
('ngulefac', 1, 3),
('njabe', 1, 1),
('njabe', 1, 2),
('njabe', 1, 3),
('njoya', 1, 1),
('njoya', 1, 2),
('njoya', 1, 3),
('njume', 1, 1),
('njume', 1, 2),
('njume', 1, 3),
('nkemlebe', 1, 1),
('nkemlebe', 1, 2),
('nkemlebe', 1, 3),
('nlend', 1, 1),
('nlend', 1, 2),
('nlend', 1, 3),
('ntung', 1, 1),
('ntung', 1, 2),
('ntung', 1, 3),
('nwanja', 1, 1),
('nwanja', 1, 2),
('nwanja', 1, 3),
('nyingcho', 1, 1),
('nyingcho', 1, 2),
('nyingcho', 1, 3),
('nyoki', 1, 1),
('nyoki', 1, 2),
('nyoki', 1, 3),
('obenan', 1, 1),
('obenan', 1, 2),
('obenan', 1, 3),
('obidimma', 1, 1),
('obidimma', 1, 2),
('obidimma', 1, 3),
('sama', 1, 1),
('sama', 1, 2),
('sama', 1, 3),
('siyapze', 1, 1),
('siyapze', 1, 2),
('siyapze', 1, 3),
('sone', 1, 1),
('sone', 1, 2),
('sone', 1, 3),
('tabe', 1, 1),
('tabe', 1, 2),
('tabe', 1, 3),
('tabot', 1, 1),
('tabot', 1, 2),
('tabot', 1, 3),
('takang', 1, 1),
('takang', 1, 2),
('takang', 1, 3),
('tamon', 1, 1),
('tamon', 1, 2),
('tamon', 1, 3),
('tassoko', 1, 1),
('tassoko', 1, 2),
('tassoko', 1, 3),
('tchamy', 1, 1),
('tchamy', 1, 2),
('tchamy', 1, 3),
('tchangou', 1, 1),
('tchangou', 1, 2),
('tchangou', 1, 3),
('teghoue', 1, 1),
('teghoue', 1, 2),
('teghoue', 1, 3),
('tita', 1, 1),
('tita', 1, 2),
('tita', 1, 3),
('ukatang', 1, 1),
('ukatang', 1, 2),
('ukatang', 1, 3),
('veke', 1, 1),
('veke', 1, 2),
('veke', 1, 3),
('achah', 2, 1),
('achah', 2, 2),
('achah', 2, 3),
('agbor', 2, 1),
('agbor', 2, 2),
('agbor', 2, 3),
('ajong', 2, 1),
('ajong', 2, 2),
('ajong', 2, 3),
('amuruwa', 2, 1),
('amuruwa', 2, 2),
('amuruwa', 2, 3),
('anaka', 2, 1),
('anaka', 2, 2),
('anaka', 2, 3),
('apah', 2, 1),
('apah', 2, 2),
('apah', 2, 3),
('atanga', 2, 1),
('atanga', 2, 2),
('atanga', 2, 3),
('atem', 2, 1),
('atem', 2, 2),
('atem', 2, 3),
('ayuk', 2, 1),
('ayuk', 2, 2),
('ayuk', 2, 3),
('babo', 2, 1),
('babo', 2, 2),
('babo', 2, 3),
('baiye', 2, 1),
('baiye', 2, 2),
('baiye', 2, 3),
('bende', 2, 1),
('bende', 2, 2),
('bende', 2, 3),
('besong', 2, 1),
('besong', 2, 2),
('besong', 2, 3),
('bessong', 2, 1),
('bessong', 2, 2),
('bessong', 2, 3),
('bounoung', 2, 1),
('bounoung', 2, 2),
('bounoung', 2, 3),
('chifu', 2, 1),
('chifu', 2, 2),
('chifu', 2, 3),
('djou', 2, 1),
('djou', 2, 2),
('djou', 2, 3),
('dobgima', 2, 1),
('dobgima', 2, 2),
('dobgima', 2, 3),
('ekangwo', 2, 1),
('ekangwo', 2, 2),
('ekangwo', 2, 3),
('elando', 2, 1),
('elando', 2, 2),
('elando', 2, 3),
('elonge', 2, 1),
('elonge', 2, 2),
('elonge', 2, 3),
('emoh', 2, 1),
('emoh', 2, 2),
('emoh', 2, 3),
('enowebai', 2, 1),
('enowebai', 2, 2),
('enowebai', 2, 3),
('epey', 2, 1),
('epey', 2, 2),
('epey', 2, 3),
('etaka', 2, 1),
('etaka', 2, 2),
('etaka', 2, 3),
('etengene', 2, 1),
('etengene', 2, 2),
('etengene', 2, 3),
('foadjo', 2, 1),
('foadjo', 2, 2),
('foadjo', 2, 3),
('fokou', 2, 1),
('fokou', 2, 2),
('fokou', 2, 3),
('honge', 2, 1),
('honge', 2, 2),
('honge', 2, 3),
('jesse', 2, 1),
('jesse', 2, 2),
('jesse', 2, 3),
('kameni', 2, 1),
('kameni', 2, 2),
('kameni', 2, 3),
('kande', 2, 1),
('kande', 2, 2),
('kande', 2, 3),
('kemajou', 2, 1),
('kemajou', 2, 2),
('kemajou', 2, 3),
('kemne', 2, 1),
('kemne', 2, 2),
('kemne', 2, 3),
('kenne', 2, 1),
('kenne', 2, 2),
('kenne', 2, 3),
('kome', 2, 1),
('kome', 2, 2),
('kome', 2, 3),
('kounchou', 2, 1),
('kounchou', 2, 2),
('kounchou', 2, 3),
('lobe', 2, 1),
('lobe', 2, 2),
('lobe', 2, 3),
('lytombe', 2, 1),
('lytombe', 2, 2),
('lytombe', 2, 3),
('manyaka', 2, 1),
('manyaka', 2, 2),
('manyaka', 2, 3),
('masango', 2, 1),
('masango', 2, 2),
('masango', 2, 3),
('mbah', 2, 1),
('mbah', 2, 2),
('mbah', 2, 3),
('mbi', 2, 1),
('mbi', 2, 2),
('mbi', 2, 3),
('mbu', 2, 1),
('mbu', 2, 2),
('mbu', 2, 3),
('mekeme', 2, 1),
('mekeme', 2, 2),
('mekeme', 2, 3),
('mende', 2, 1),
('mende', 2, 2),
('mende', 2, 3),
('metuge', 2, 1),
('metuge', 2, 2),
('metuge', 2, 3),
('mezation', 2, 1),
('mezation', 2, 2),
('mezation', 2, 3),
('mieguim', 2, 1),
('mieguim', 2, 2),
('mieguim', 2, 3),
('mocto', 2, 1),
('mocto', 2, 2),
('mocto', 2, 3),
('mokube', 2, 1),
('mokube', 2, 2),
('mokube', 2, 3),
('mokwe', 2, 1),
('mokwe', 2, 2),
('mokwe', 2, 3),
('nchotieh', 2, 1),
('nchotieh', 2, 2),
('nchotieh', 2, 3),
('ndip', 2, 1),
('ndip', 2, 2),
('ndip', 2, 3),
('nekongoh', 2, 1),
('nekongoh', 2, 2),
('nekongoh', 2, 3),
('ngaha', 2, 1),
('ngaha', 2, 2),
('ngaha', 2, 3),
('ngalame', 2, 1),
('ngalame', 2, 2),
('ngalame', 2, 3),
('ngando', 2, 1),
('ngando', 2, 2),
('ngando', 2, 3),
('ngangmi', 2, 1),
('ngangmi', 2, 2),
('ngangmi', 2, 3),
('nganjo', 2, 1),
('nganjo', 2, 2),
('nganjo', 2, 3),
('ngoe', 2, 1),
('ngoe', 2, 2),
('ngoe', 2, 3),
('ngouh', 2, 1),
('ngouh', 2, 2),
('ngouh', 2, 3),
('ngounou', 2, 1),
('ngounou', 2, 2),
('ngounou', 2, 3),
('ngulefac', 2, 1),
('ngulefac', 2, 2),
('ngulefac', 2, 3),
('njabe', 2, 1),
('njabe', 2, 2),
('njabe', 2, 3),
('njoya', 2, 1),
('njoya', 2, 2),
('njoya', 2, 3),
('njume', 2, 1),
('njume', 2, 2),
('njume', 2, 3),
('nkemlebe', 2, 1),
('nkemlebe', 2, 2),
('nkemlebe', 2, 3),
('nlend', 2, 1),
('nlend', 2, 2),
('nlend', 2, 3),
('ntung', 2, 1),
('ntung', 2, 2),
('ntung', 2, 3),
('nwanja', 2, 1),
('nwanja', 2, 2),
('nwanja', 2, 3),
('nyingcho', 2, 1),
('nyingcho', 2, 2),
('nyingcho', 2, 3),
('nyoki', 2, 1),
('nyoki', 2, 2),
('nyoki', 2, 3),
('obenan', 2, 1),
('obenan', 2, 2),
('obenan', 2, 3),
('obidimma', 2, 1),
('obidimma', 2, 2),
('obidimma', 2, 3),
('sama', 2, 1),
('sama', 2, 2),
('sama', 2, 3),
('siyapze', 2, 1),
('siyapze', 2, 2),
('siyapze', 2, 3),
('sone', 2, 1),
('sone', 2, 2),
('sone', 2, 3),
('tabe', 2, 1),
('tabe', 2, 2),
('tabe', 2, 3),
('tabot', 2, 1),
('tabot', 2, 2),
('tabot', 2, 3),
('takang', 2, 1),
('takang', 2, 2),
('takang', 2, 3),
('tamon', 2, 1),
('tamon', 2, 2),
('tamon', 2, 3),
('tassoko', 2, 1),
('tassoko', 2, 2),
('tassoko', 2, 3),
('tchamy', 2, 1),
('tchamy', 2, 2),
('tchamy', 2, 3),
('tchangou', 2, 1),
('tchangou', 2, 2),
('tchangou', 2, 3),
('teghoue', 2, 1),
('teghoue', 2, 2),
('teghoue', 2, 3),
('tita', 2, 1),
('tita', 2, 2),
('tita', 2, 3),
('ukatang', 2, 1),
('ukatang', 2, 2),
('ukatang', 2, 3),
('veke', 2, 1),
('veke', 2, 2),
('veke', 2, 3),
('achah', 3, 1),
('achah', 3, 2),
('achah', 3, 3),
('agbor', 3, 1),
('agbor', 3, 2),
('agbor', 3, 3),
('ajong', 3, 1),
('ajong', 3, 2),
('ajong', 3, 3),
('amuruwa', 3, 1),
('amuruwa', 3, 2),
('amuruwa', 3, 3),
('anaka', 3, 1),
('anaka', 3, 2),
('anaka', 3, 3),
('apah', 3, 1),
('apah', 3, 2),
('apah', 3, 3),
('atanga', 3, 1),
('atanga', 3, 2),
('atanga', 3, 3),
('atem', 3, 1),
('atem', 3, 2),
('atem', 3, 3),
('ayuk', 3, 1),
('ayuk', 3, 2),
('ayuk', 3, 3),
('babo', 3, 1),
('babo', 3, 2),
('babo', 3, 3),
('baiye', 3, 1),
('baiye', 3, 2),
('baiye', 3, 3),
('bende', 3, 1),
('bende', 3, 2),
('bende', 3, 3),
('besong', 3, 1),
('besong', 3, 2),
('besong', 3, 3),
('bessong', 3, 1),
('bessong', 3, 2),
('bessong', 3, 3),
('bounoung', 3, 1),
('bounoung', 3, 2),
('bounoung', 3, 3),
('chifu', 3, 1),
('chifu', 3, 2),
('chifu', 3, 3),
('djou', 3, 1),
('djou', 3, 2),
('djou', 3, 3),
('dobgima', 3, 1),
('dobgima', 3, 2),
('dobgima', 3, 3),
('ekangwo', 3, 1),
('ekangwo', 3, 2),
('ekangwo', 3, 3),
('elando', 3, 1),
('elando', 3, 2),
('elando', 3, 3),
('elonge', 3, 1),
('elonge', 3, 2),
('elonge', 3, 3),
('emoh', 3, 1),
('emoh', 3, 2),
('emoh', 3, 3),
('enowebai', 3, 1),
('enowebai', 3, 2),
('enowebai', 3, 3),
('epey', 3, 1),
('epey', 3, 2),
('epey', 3, 3),
('etaka', 3, 1),
('etaka', 3, 2),
('etaka', 3, 3),
('etengene', 3, 1),
('etengene', 3, 2),
('etengene', 3, 3),
('foadjo', 3, 1),
('foadjo', 3, 2),
('foadjo', 3, 3),
('fokou', 3, 1),
('fokou', 3, 2),
('fokou', 3, 3),
('honge', 3, 1),
('honge', 3, 2),
('honge', 3, 3),
('jesse', 3, 1),
('jesse', 3, 2),
('jesse', 3, 3),
('kameni', 3, 1),
('kameni', 3, 2),
('kameni', 3, 3),
('kande', 3, 1),
('kande', 3, 2),
('kande', 3, 3),
('kemajou', 3, 1),
('kemajou', 3, 2),
('kemajou', 3, 3),
('kemne', 3, 1),
('kemne', 3, 2),
('kemne', 3, 3),
('kenne', 3, 1),
('kenne', 3, 2),
('kenne', 3, 3),
('kome', 3, 1),
('kome', 3, 2),
('kome', 3, 3),
('kounchou', 3, 1),
('kounchou', 3, 2),
('kounchou', 3, 3),
('lobe', 3, 1),
('lobe', 3, 2),
('lobe', 3, 3),
('lytombe', 3, 1),
('lytombe', 3, 2),
('lytombe', 3, 3),
('manyaka', 3, 1),
('manyaka', 3, 2),
('manyaka', 3, 3),
('masango', 3, 1),
('masango', 3, 2),
('masango', 3, 3),
('mbah', 3, 1),
('mbah', 3, 2),
('mbah', 3, 3),
('mbi', 3, 1),
('mbi', 3, 2),
('mbi', 3, 3),
('mbu', 3, 1),
('mbu', 3, 2),
('mbu', 3, 3),
('mekeme', 3, 1),
('mekeme', 3, 2),
('mekeme', 3, 3),
('mende', 3, 1),
('mende', 3, 2),
('mende', 3, 3),
('metuge', 3, 1),
('metuge', 3, 2),
('metuge', 3, 3),
('mezation', 3, 1),
('mezation', 3, 2),
('mezation', 3, 3),
('mieguim', 3, 1),
('mieguim', 3, 2),
('mieguim', 3, 3),
('mocto', 3, 1),
('mocto', 3, 2),
('mocto', 3, 3),
('mokube', 3, 1),
('mokube', 3, 2),
('mokube', 3, 3),
('mokwe', 3, 1),
('mokwe', 3, 2),
('mokwe', 3, 3),
('nchotieh', 3, 1),
('nchotieh', 3, 2),
('nchotieh', 3, 3),
('ndip', 3, 1),
('ndip', 3, 2),
('ndip', 3, 3),
('nekongoh', 3, 1),
('nekongoh', 3, 2),
('nekongoh', 3, 3),
('ngaha', 3, 1),
('ngaha', 3, 2),
('ngaha', 3, 3),
('ngalame', 3, 1),
('ngalame', 3, 2),
('ngalame', 3, 3),
('ngando', 3, 1),
('ngando', 3, 2),
('ngando', 3, 3),
('ngangmi', 3, 1),
('ngangmi', 3, 2),
('ngangmi', 3, 3),
('nganjo', 3, 1),
('nganjo', 3, 2),
('nganjo', 3, 3),
('ngoe', 3, 1),
('ngoe', 3, 2),
('ngoe', 3, 3),
('ngouh', 3, 1),
('ngouh', 3, 2),
('ngouh', 3, 3),
('ngounou', 3, 1),
('ngounou', 3, 2),
('ngounou', 3, 3),
('ngulefac', 3, 1),
('ngulefac', 3, 2),
('ngulefac', 3, 3),
('njabe', 3, 1),
('njabe', 3, 2),
('njabe', 3, 3),
('njoya', 3, 1),
('njoya', 3, 2),
('njoya', 3, 3),
('njume', 3, 1),
('njume', 3, 2),
('njume', 3, 3),
('nkemlebe', 3, 1),
('nkemlebe', 3, 2),
('nkemlebe', 3, 3),
('nlend', 3, 1),
('nlend', 3, 2),
('nlend', 3, 3),
('ntung', 3, 1),
('ntung', 3, 2),
('ntung', 3, 3),
('nwanja', 3, 1),
('nwanja', 3, 2),
('nwanja', 3, 3),
('nyingcho', 3, 1),
('nyingcho', 3, 2),
('nyingcho', 3, 3),
('nyoki', 3, 1),
('nyoki', 3, 2),
('nyoki', 3, 3),
('obenan', 3, 1),
('obenan', 3, 2),
('obenan', 3, 3),
('obidimma', 3, 1),
('obidimma', 3, 2),
('obidimma', 3, 3),
('sama', 3, 1),
('sama', 3, 2),
('sama', 3, 3),
('siyapze', 3, 1),
('siyapze', 3, 2),
('siyapze', 3, 3),
('sone', 3, 1),
('sone', 3, 2),
('sone', 3, 3),
('tabe', 3, 1),
('tabe', 3, 2),
('tabe', 3, 3),
('tabot', 3, 1),
('tabot', 3, 2),
('tabot', 3, 3),
('takang', 3, 1),
('takang', 3, 2),
('takang', 3, 3),
('tamon', 3, 1),
('tamon', 3, 2),
('tamon', 3, 3),
('tassoko', 3, 1),
('tassoko', 3, 2),
('tassoko', 3, 3),
('tchamy', 3, 1),
('tchamy', 3, 2),
('tchamy', 3, 3),
('tchangou', 3, 1),
('tchangou', 3, 2),
('tchangou', 3, 3),
('teghoue', 3, 1),
('teghoue', 3, 2),
('teghoue', 3, 3),
('tita', 3, 1),
('tita', 3, 2),
('tita', 3, 3),
('ukatang', 3, 1),
('ukatang', 3, 2),
('ukatang', 3, 3),
('veke', 3, 1),
('veke', 3, 2),
('veke', 3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_groupes_delestage`
--

CREATE TABLE IF NOT EXISTS `j_eleves_groupes_delestage` (
  `login` varchar(50) DEFAULT NULL,
  `id_groupe` int(11) DEFAULT NULL,
  `periode` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_groupes_delestage`
--

INSERT INTO `j_eleves_groupes_delestage` (`login`, `id_groupe`, `periode`) VALUES
(NULL, 1, NULL),
(NULL, 2, NULL),
(NULL, 3, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_groupes_delestage2`
--

CREATE TABLE IF NOT EXISTS `j_eleves_groupes_delestage2` (
  `login` varchar(50) DEFAULT NULL,
  `id_groupe` int(11) DEFAULT NULL,
  `periode` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_groupes_delestage2`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_professeurs`
--

CREATE TABLE IF NOT EXISTS `j_eleves_professeurs` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `professeur` varchar(50) NOT NULL DEFAULT '',
  `id_classe` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login`,`professeur`,`id_classe`),
  KEY `classe_professeur` (`id_classe`,`professeur`),
  KEY `professeur_classe` (`professeur`,`id_classe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_professeurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_eleves_regime`
--

CREATE TABLE IF NOT EXISTS `j_eleves_regime` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `doublant` char(1) NOT NULL DEFAULT '',
  `regime` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_eleves_regime`
--

INSERT INTO `j_eleves_regime` (`login`, `doublant`, `regime`) VALUES
('ngaha', '-', 'int.'),
('achah', '-', 'int.'),
('ajong', '-', 'int.'),
('anaka', '-', 'int.'),
('nlend', '-', 'int.'),
('manyaka', '-', 'int.'),
('apah', '-', 'int.'),
('atanga', '-', 'int.'),
('atem', '-', 'int.'),
('ayuk', '-', 'int.'),
('etengene', '-', 'int.'),
('baiye', '-', 'int.'),
('bende', '-', 'int.'),
('besong', '-', 'int.'),
('bessong', '-', 'int.'),
('bounoung', '-', 'int.'),
('honge', '-', 'int.'),
('nganjo', '-', 'int.'),
('jesse', '-', 'int.'),
('chifu', '-', 'int.'),
('djou', '-', 'int.'),
('dobgima', '-', 'int.'),
('nchotieh', '-', 'int.'),
('masango', '-', 'int.'),
('ekangwo', '-', 'int.'),
('elando', '-', 'int.'),
('elonge', '-', 'int.'),
('emoh', '-', 'int.'),
('enowebai', '-', 'int.'),
('epey', '-', 'int.'),
('foadjo', '-', 'int.'),
('fokou', '-', 'int.'),
('veke', '-', 'int.'),
('obenan', '-', 'int.'),
('lytombe', '-', 'int.'),
('babo', '-', 'int.'),
('tabot', '-', 'int.'),
('kameni', '-', 'int.'),
('kande', '-', 'int.'),
('kemajou', '-', 'int.'),
('kemne', '-', 'int.'),
('kenne', '-', 'int.'),
('kome', '-', 'int.'),
('kounchou', '-', 'int.'),
('mbi', '-', 'int.'),
('ndip', '-', 'int.'),
('mbu', '-', 'int.'),
('mekeme', '-', 'int.'),
('mende', '-', 'int.'),
('metuge', '-', 'int.'),
('mieguim', '-', 'int.'),
('mocto', '-', 'd/p'),
('mokube', '-', 'd/p'),
('mokwe', '-', 'int.'),
('nekongoh', '-', 'int.'),
('ngalame', '-', 'int.'),
('ngando', '-', 'int.'),
('ngangmi', '-', 'int.'),
('ngoe', '-', 'int.'),
('ngouh', '-', 'int.'),
('ngounou', '-', 'int.'),
('ngulefac', '-', 'int.'),
('njabe', '-', 'int.'),
('njoya', '-', 'int.'),
('njume', '-', 'int.'),
('nkemlebe', '-', 'int.'),
('etaka', '-', 'int.'),
('mezation', '-', 'int.'),
('ntung', '-', 'int.'),
('nwanja', '-', 'int.'),
('nyingcho', '-', 'int.'),
('nyoki', '-', 'int.'),
('obidimma', '-', 'int.'),
('sama', '-', 'int.'),
('mbah', '-', 'int.'),
('lobe', '-', 'int.'),
('agbor', '-', 'int.'),
('siyapze', '-', 'int.'),
('sone', '-', 'int.'),
('amuruwa', '-', 'int.'),
('tabe', '-', 'int.'),
('takang', '-', 'int.'),
('tamon', '-', 'int.'),
('tassoko', '-', 'int.'),
('tchamy', '-', 'int.'),
('tchangou', '-', 'int.'),
('teghoue', '-', 'int.'),
('tita', '-', 'int.'),
('ukatang', '-', 'int.'),
('abong', '-', 'int.');

-- --------------------------------------------------------

--
-- Structure de la table `j_groupes_classes`
--

CREATE TABLE IF NOT EXISTS `j_groupes_classes` (
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `id_classe` int(11) NOT NULL DEFAULT '0',
  `priorite` smallint(6) NOT NULL,
  `coef` decimal(3,1) NOT NULL,
  `categorie_id` int(11) NOT NULL DEFAULT '1',
  `saisie_ects` tinyint(1) NOT NULL DEFAULT '0',
  `valeur_ects` int(11) DEFAULT NULL,
  `mode_moy` enum('-','sup10','bonus') NOT NULL DEFAULT '-',
  `apb_langue_vivante` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_groupe`,`id_classe`),
  KEY `id_classe_coef` (`id_classe`,`coef`),
  KEY `saisie_ects_id_groupe` (`saisie_ects`,`id_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_groupes_classes`
--

INSERT INTO `j_groupes_classes` (`id_groupe`, `id_classe`, `priorite`, `coef`, `categorie_id`, `saisie_ects`, `valeur_ects`, `mode_moy`, `apb_langue_vivante`) VALUES
(1, 1, 11, '5.0', 2, 0, 0, '-', ''),
(2, 1, 13, '5.0', 2, 0, 0, '-', ''),
(3, 1, 12, '0.0', 2, 0, NULL, '-', ''),
(4, 1, 22, '0.0', 6, 0, NULL, '-', '');

-- --------------------------------------------------------

--
-- Structure de la table `j_groupes_matieres`
--

CREATE TABLE IF NOT EXISTS `j_groupes_matieres` (
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `id_matiere` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_groupe`,`id_matiere`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_groupes_matieres`
--

INSERT INTO `j_groupes_matieres` (`id_groupe`, `id_matiere`) VALUES
(1, 'Maths'),
(2, 'French'),
(3, 'English_language'),
(4, 'Computer');

-- --------------------------------------------------------

--
-- Structure de la table `j_groupes_professeurs`
--

CREATE TABLE IF NOT EXISTS `j_groupes_professeurs` (
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `login` varchar(50) NOT NULL DEFAULT '',
  `ordre_prof` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_groupe`,`login`),
  KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_groupes_professeurs`
--

INSERT INTO `j_groupes_professeurs` (`id_groupe`, `login`, `ordre_prof`) VALUES
(1, 'elijah', 0),
(2, 'nkwenti', 0),
(3, 'bih', 0);

-- --------------------------------------------------------

--
-- Structure de la table `j_groupes_visibilite`
--

CREATE TABLE IF NOT EXISTS `j_groupes_visibilite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_groupe` int(11) NOT NULL,
  `domaine` varchar(255) NOT NULL DEFAULT '',
  `visible` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_groupe_domaine` (`id_groupe`,`domaine`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `j_groupes_visibilite`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_matieres_categories_classes`
--

CREATE TABLE IF NOT EXISTS `j_matieres_categories_classes` (
  `categorie_id` int(11) NOT NULL DEFAULT '0',
  `classe_id` int(11) NOT NULL DEFAULT '0',
  `priority` smallint(6) NOT NULL DEFAULT '0',
  `affiche_moyenne` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`categorie_id`,`classe_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_matieres_categories_classes`
--

INSERT INTO `j_matieres_categories_classes` (`categorie_id`, `classe_id`, `priority`, `affiche_moyenne`) VALUES
(1, 1, 5, 1),
(1, 2, 5, 1);

-- --------------------------------------------------------

--
-- Structure de la table `j_mentions_classes`
--

CREATE TABLE IF NOT EXISTS `j_mentions_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mention` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `ordre` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `j_mentions_classes`
--

INSERT INTO `j_mentions_classes` (`id`, `id_mention`, `id_classe`, `ordre`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `j_notifications_resp_pers`
--

CREATE TABLE IF NOT EXISTS `j_notifications_resp_pers` (
  `a_notification_id` int(12) NOT NULL COMMENT 'cle etrangere de la notification',
  `pers_id` varchar(10) NOT NULL COMMENT 'cle etrangere des personnes',
  PRIMARY KEY (`a_notification_id`,`pers_id`),
  KEY `j_notifications_resp_pers_FI_2` (`pers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre la notification et les personnes don';

--
-- Contenu de la table `j_notifications_resp_pers`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_professeurs_matieres`
--

CREATE TABLE IF NOT EXISTS `j_professeurs_matieres` (
  `id_professeur` varchar(50) NOT NULL DEFAULT '',
  `id_matiere` varchar(50) NOT NULL DEFAULT '',
  `ordre_matieres` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_professeur`,`id_matiere`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_professeurs_matieres`
--

INSERT INTO `j_professeurs_matieres` (`id_professeur`, `id_matiere`, `ordre_matieres`) VALUES
('elijah', 'Maths', 0),
('nkwenti', 'French', 1),
('bih', 'English_language', 1),
('bih', 'English_literature', 2),
('ekah', 'History', 0),
('morfaw', 'Computer', 1);

-- --------------------------------------------------------

--
-- Structure de la table `j_scol_classes`
--

CREATE TABLE IF NOT EXISTS `j_scol_classes` (
  `login` varchar(50) NOT NULL,
  `id_classe` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_scol_classes`
--

INSERT INTO `j_scol_classes` (`login`, `id_classe`) VALUES
('scolar', 1),
('scolar', 2);

-- --------------------------------------------------------

--
-- Structure de la table `j_signalement`
--

CREATE TABLE IF NOT EXISTS `j_signalement` (
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `login` varchar(50) NOT NULL DEFAULT '',
  `periode` int(11) NOT NULL DEFAULT '0',
  `nature` varchar(50) NOT NULL DEFAULT '',
  `valeur` varchar(50) NOT NULL DEFAULT '',
  `declarant` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_groupe`,`login`,`periode`,`nature`),
  KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `j_signalement`
--


-- --------------------------------------------------------

--
-- Structure de la table `j_traitements_saisies`
--

CREATE TABLE IF NOT EXISTS `j_traitements_saisies` (
  `a_saisie_id` int(12) NOT NULL COMMENT 'cle etrangere de l''absence saisie',
  `a_traitement_id` int(12) NOT NULL COMMENT 'cle etrangere du traitement de ces absences',
  PRIMARY KEY (`a_saisie_id`,`a_traitement_id`),
  KEY `j_traitements_saisies_FI_2` (`a_traitement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre la saisie et le traitement des absen';

--
-- Contenu de la table `j_traitements_saisies`
--


-- --------------------------------------------------------

--
-- Structure de la table `lettres_cadres`
--

CREATE TABLE IF NOT EXISTS `lettres_cadres` (
  `id_lettre_cadre` int(11) NOT NULL AUTO_INCREMENT,
  `nom_lettre_cadre` varchar(150) NOT NULL,
  `x_lettre_cadre` float NOT NULL,
  `y_lettre_cadre` float NOT NULL,
  `l_lettre_cadre` float NOT NULL,
  `h_lettre_cadre` float NOT NULL,
  `texte_lettre_cadre` text NOT NULL,
  `encadre_lettre_cadre` tinyint(4) NOT NULL,
  `couleurdefond_lettre_cadre` varchar(11) NOT NULL,
  PRIMARY KEY (`id_lettre_cadre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `lettres_cadres`
--

INSERT INTO `lettres_cadres` (`id_lettre_cadre`, `nom_lettre_cadre`, `x_lettre_cadre`, `y_lettre_cadre`, `l_lettre_cadre`, `h_lettre_cadre`, `texte_lettre_cadre`, `encadre_lettre_cadre`, `couleurdefond_lettre_cadre`) VALUES
(1, 'adresse responsable', 100, 40, 100, 5, 'To the attention of\r\n<civilitee_court_responsable> <nom_responsable> <prenom_responsable>\r\n<adresse_responsable>\r\n<cp_responsable> <commune_responsable>\r\n', 0, '||'),
(2, 'adresse etablissement', 0, 0, 0, 0, '', 0, ''),
(3, 'datation', 0, 0, 0, 0, '', 0, ''),
(4, 'corp avertissement', 10, 70, 0, 5, '<u>Objet: </u> <g>Warning</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nI see myself in the obligation to give a <b>WARNING</b>\r\n\r\nto <g><nom_eleve> <prenom_eleve></g> student of the class <g><classe_eleve></g>.\r\n\r\n\r\nfor the following reason : <g><sujet_eleve></g>\r\n\r\n<remarque_eleve>\r\n\r\n\r\n\r\nAs the rules of procedure of the school envisage it, it could be sanctioned starting from this day.\r\nPossible sanctions :\r\n\r\n\r\n\r\n\r\nI thank you for returning me this specimen after having dated it and having signed.\r\nPlease accept <nom_civilitee_long> <nom_responsable> the insurance of my distinguished consideration.\r\n\r\n\r\n\r\nDate and signatures of the parents :	', 0, '||'),
(5, 'corp blame', 10, 70, 0, 5, '<u>Objet</u>: <g>Blame</g>\r\n\r\n\r\n<nom_civilitee_long>\r\n\r\nI see myself in the obligation to give a BLAME \r\n\r\nto <g><nom_eleve> <prenom_eleve></g> student of the class <g><classe_eleve></g>.\r\n\r\nAsked by: <g><courrier_demande_par></g>\r\n\r\nfor the following reason: <g><raison></g>\r\n\r\n<remarque>\r\n\r\nI thank you for returning me this specimen after having dated it and having signed.\r\nPlease accept <g><nom_civilitee_long> <nom_responsable></g> the insurance of my distinguished consideration.\r\n\r\n<u>Date and signatures from the parents:</u>\r\n\r\n\r\n\r\n\r\n\r\nWe ask for a discussion with the person having asked for the sanction OUI / NON.\r\n(The appointment management is in your initiative)\r\n', 0, '||'),
(6, 'corp convocation parents', 10, 70, 0, 5, '<u>Objet</u>: <g>Convocation of the parents</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nYou are requested to contact the Principal Adviser of Education as soon as possible, about <g><nom_eleve> <prenom_eleve></g> registered in class of <g><classe_eleve></g>.\r\n\r\nfor the following reason:\r\n\r\n<remarque>\r\n\r\n\r\n\r\nWithout news of your share before ........................................., I will be obliged to proceed to the descolarisation of the student, with the consequences which will result from it, until your meeting.\r\n\r\n\r\nPlease accept <g><nom_civilitee_long> <nom_responsable></g> the insurance of my distinguished consideration.', 0, '||'),
(7, 'corp exclusion', 10, 70, 0, 5, '<u>Objet: </u> <g>Sanction - Exclusion of the school</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nBy the present, I hold to announce you that <nom_eleve>\r\n\r\nregistered in class of  <classe_eleve>\r\n\r\n\r\nbeing made guilty of the following facts : \r\n\r\n<remarque>\r\n\r\n\r\n\r\nIs excluded from the school,\r\nas from: <b><date_debut></b> at <b><heure_debut></b>,\r\nuntil: <b><date_fin></b> at <b><heure_fin></b>.\r\n\r\n\r\nIt will have to be presented, at the office of the School Life \r\n\r\nthe ....................................... at ....................................... ACCOMPANY BY ITS PARENTS.\r\n\r\n\r\n\r\n\r\nPlease accept &lt;TYPEPARENT&gt; &lt;NOMPARENT&gt; the insurance of my distinguished consideration.', 0, '||'),
(8, 'corp demande justificatif absence', 10, 70, 0, 5, '<u>Objet: </u> <g>Demand of document in proof of absence</g>\r\n\r\n\r\n<civilitee_long_responsable>,\r\n\r\nI regret to inform you that <b><nom_eleve> <prenom_eleve></b>, student in class of <b><classe_eleve></b> did not attend the courses:\r\n\r\n<liste>\r\n\r\nPlease agree to make me known the reason for his absence.\r\n\r\nTo allow an effective control of the presences, any absence of a student must be justified by its family, the same day by telephone, either in writing, or by fax.\r\n\r\nBefore regaining the courses, the student absent will have to be presented at the office of the Principal Adviser of Education with its notebook of correspondence with a signed document in proof of the parents.\r\n\r\nPlease accept <civilitee_long_responsable> <nom_responsable>, the insurance of my distinguished consideration.\r\n                                               \r\nCPE\r\n<civilitee_long_cpe> <nom_cpe> <prenom_cpe>\r\n\r\nReturn please, by return of the mail, these signed opinion of the parents :\r\n\r\nReason for the absence : \r\n________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________\r\n\r\n\r\n\r\nDate and signatures of the parents :  \r\n', 0, '||'),
(10, 'signature', 100, 180, 0, 5, '<b><courrier_signe_par_fonction></b>,\r\n<courrier_signe_par>\r\n', 0, '||');

-- --------------------------------------------------------

--
-- Structure de la table `lettres_suivis`
--

CREATE TABLE IF NOT EXISTS `lettres_suivis` (
  `id_lettre_suivi` int(11) NOT NULL AUTO_INCREMENT,
  `lettresuitealettren_lettre_suivi` int(11) NOT NULL,
  `quirecois_lettre_suivi` varchar(50) NOT NULL,
  `partde_lettre_suivi` varchar(200) NOT NULL,
  `partdenum_lettre_suivi` text NOT NULL,
  `quiemet_lettre_suivi` varchar(150) NOT NULL,
  `emis_date_lettre_suivi` date NOT NULL,
  `emis_heure_lettre_suivi` time NOT NULL,
  `quienvoi_lettre_suivi` varchar(150) NOT NULL,
  `envoye_date_lettre_suivi` date NOT NULL,
  `envoye_heure_lettre_suivi` time NOT NULL,
  `type_lettre_suivi` int(11) NOT NULL,
  `quireception_lettre_suivi` varchar(150) NOT NULL,
  `reponse_date_lettre_suivi` date NOT NULL,
  `reponse_remarque_lettre_suivi` varchar(250) NOT NULL,
  `statu_lettre_suivi` varchar(20) NOT NULL,
  PRIMARY KEY (`id_lettre_suivi`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `lettres_suivis`
--

INSERT INTO `lettres_suivis` (`id_lettre_suivi`, `lettresuitealettren_lettre_suivi`, `quirecois_lettre_suivi`, `partde_lettre_suivi`, `partdenum_lettre_suivi`, `quiemet_lettre_suivi`, `emis_date_lettre_suivi`, `emis_heure_lettre_suivi`, `quienvoi_lettre_suivi`, `envoye_date_lettre_suivi`, `envoye_heure_lettre_suivi`, `type_lettre_suivi`, `quireception_lettre_suivi`, `reponse_date_lettre_suivi`, `reponse_remarque_lettre_suivi`, `statu_lettre_suivi`) VALUES
(3, 0, 'siyapze', 'absences_eleves', ',1,', 'elijah', '2012-03-02', '10:14:36', '', '0000-00-00', '00:00:00', 6, '', '0000-00-00', '', 'en attente'),
(2, 0, 'bende', 'absences_eleves', ',2,', 'elijah', '2012-03-02', '10:14:36', '', '0000-00-00', '00:00:00', 6, '', '0000-00-00', '', 'en attente'),
(4, 0, 'achah', 'absences_eleves', ',3,', 'elijah', '2012-03-03', '14:21:28', '', '0000-00-00', '00:00:00', 6, '', '0000-00-00', '', 'en attente');

-- --------------------------------------------------------

--
-- Structure de la table `lettres_tcs`
--

CREATE TABLE IF NOT EXISTS `lettres_tcs` (
  `id_lettre_tc` int(11) NOT NULL AUTO_INCREMENT,
  `type_lettre_tc` int(11) NOT NULL,
  `cadre_lettre_tc` int(11) NOT NULL,
  `x_lettre_tc` float NOT NULL,
  `y_lettre_tc` float NOT NULL,
  `l_lettre_tc` float NOT NULL,
  `h_lettre_tc` float NOT NULL,
  `encadre_lettre_tc` int(1) NOT NULL,
  PRIMARY KEY (`id_lettre_tc`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=201 ;

--
-- Contenu de la table `lettres_tcs`
--

INSERT INTO `lettres_tcs` (`id_lettre_tc`, `type_lettre_tc`, `cadre_lettre_tc`, `x_lettre_tc`, `y_lettre_tc`, `l_lettre_tc`, `h_lettre_tc`, `encadre_lettre_tc`) VALUES
(1, 3, 0, 0, 0, 0, 0, 0),
(2, 3, 0, 0, 0, 0, 0, 0),
(3, 3, 0, 0, 0, 0, 0, 0),
(4, 3, 0, 0, 0, 0, 0, 0),
(5, 3, 0, 0, 0, 0, 0, 0),
(6, 3, 0, 0, 0, 0, 0, 0),
(7, 3, 0, 0, 0, 0, 0, 0),
(8, 3, 0, 0, 0, 0, 0, 0),
(9, 3, 0, 0, 0, 0, 0, 0),
(10, 3, 0, 0, 0, 0, 0, 0),
(11, 3, 0, 0, 0, 0, 0, 0),
(12, 3, 0, 0, 0, 0, 0, 0),
(13, 3, 0, 0, 0, 0, 0, 0),
(14, 3, 0, 0, 0, 0, 0, 0),
(15, 3, 0, 0, 0, 0, 0, 0),
(16, 3, 0, 0, 0, 0, 0, 0),
(17, 3, 0, 0, 0, 0, 0, 0),
(18, 3, 0, 0, 0, 0, 0, 0),
(19, 3, 0, 0, 0, 0, 0, 0),
(20, 3, 0, 0, 0, 0, 0, 0),
(21, 3, 0, 0, 0, 0, 0, 0),
(22, 3, 0, 0, 0, 0, 0, 0),
(23, 3, 0, 0, 0, 0, 0, 0),
(24, 3, 0, 0, 0, 0, 0, 0),
(25, 3, 0, 0, 0, 0, 0, 0),
(26, 3, 0, 0, 0, 0, 0, 0),
(27, 3, 0, 0, 0, 0, 0, 0),
(28, 3, 0, 0, 0, 0, 0, 0),
(29, 3, 0, 0, 0, 0, 0, 0),
(30, 3, 0, 0, 0, 0, 0, 0),
(31, 3, 0, 0, 0, 0, 0, 0),
(32, 3, 0, 0, 0, 0, 0, 0),
(33, 3, 0, 0, 0, 0, 0, 0),
(34, 3, 0, 0, 0, 0, 0, 0),
(35, 3, 0, 0, 0, 0, 0, 0),
(36, 3, 0, 0, 0, 0, 0, 0),
(37, 3, 0, 0, 0, 0, 0, 0),
(38, 3, 0, 0, 0, 0, 0, 0),
(39, 3, 0, 0, 0, 0, 0, 0),
(40, 3, 0, 0, 0, 0, 0, 0),
(41, 3, 0, 0, 0, 0, 0, 0),
(42, 3, 0, 0, 0, 0, 0, 0),
(43, 3, 0, 0, 0, 0, 0, 0),
(44, 3, 0, 0, 0, 0, 0, 0),
(45, 3, 0, 0, 0, 0, 0, 0),
(46, 3, 0, 0, 0, 0, 0, 0),
(47, 3, 0, 0, 0, 0, 0, 0),
(48, 3, 0, 0, 0, 0, 0, 0),
(49, 3, 0, 0, 0, 0, 0, 0),
(50, 3, 0, 0, 0, 0, 0, 0),
(51, 3, 0, 0, 0, 0, 0, 0),
(52, 3, 0, 0, 0, 0, 0, 0),
(53, 3, 0, 0, 0, 0, 0, 0),
(56, 3, 1, 100, 40, 100, 5, 0),
(57, 3, 4, 10, 70, 0, 5, 0),
(58, 1, 0, 0, 0, 0, 0, 0),
(59, 1, 0, 0, 0, 0, 0, 0),
(60, 1, 0, 0, 0, 0, 0, 0),
(61, 1, 0, 0, 0, 0, 0, 0),
(62, 1, 0, 0, 0, 0, 0, 0),
(63, 1, 0, 0, 0, 0, 0, 0),
(64, 1, 0, 0, 0, 0, 0, 0),
(65, 1, 1, 100, 40, 100, 5, 0),
(66, 1, 5, 10, 70, 0, 5, 0),
(68, 2, 1, 100, 40, 100, 5, 0),
(69, 2, 6, 10, 70, 0, 5, 0),
(70, 4, 1, 100, 40, 100, 5, 0),
(71, 4, 7, 10, 70, 0, 5, 0),
(72, 6, 0, 0, 0, 0, 0, 0),
(73, 6, 0, 0, 0, 0, 0, 0),
(74, 6, 0, 0, 0, 0, 0, 0),
(75, 6, 0, 0, 0, 0, 0, 0),
(76, 6, 0, 0, 0, 0, 0, 0),
(77, 6, 0, 0, 0, 0, 0, 0),
(78, 6, 0, 0, 0, 0, 0, 0),
(79, 6, 0, 0, 0, 0, 0, 0),
(80, 6, 0, 0, 0, 0, 0, 0),
(81, 6, 0, 0, 0, 0, 0, 0),
(82, 6, 0, 0, 0, 0, 0, 0),
(83, 6, 0, 0, 0, 0, 0, 0),
(84, 6, 0, 0, 0, 0, 0, 0),
(85, 6, 0, 0, 0, 0, 0, 0),
(86, 6, 0, 0, 0, 0, 0, 0),
(87, 6, 0, 0, 0, 0, 0, 0),
(88, 6, 0, 0, 0, 0, 0, 0),
(89, 6, 1, 100, 40, 100, 5, 0),
(90, 6, 8, 10, 70, 0, 5, 0),
(91, 7, 0, 0, 0, 0, 0, 0),
(92, 7, 0, 0, 0, 0, 0, 0),
(93, 7, 0, 0, 0, 0, 0, 0),
(94, 7, 0, 0, 0, 0, 0, 0),
(95, 7, 0, 0, 0, 0, 0, 0),
(96, 7, 0, 0, 0, 0, 0, 0),
(97, 7, 0, 0, 0, 0, 0, 0),
(98, 7, 0, 0, 0, 0, 0, 0),
(99, 7, 0, 0, 0, 0, 0, 0),
(100, 7, 0, 0, 0, 0, 0, 0),
(101, 7, 0, 0, 0, 0, 0, 0),
(102, 7, 0, 0, 0, 0, 0, 0),
(103, 7, 0, 0, 0, 0, 0, 0),
(104, 7, 0, 0, 0, 0, 0, 0),
(105, 7, 0, 0, 0, 0, 0, 0),
(106, 7, 0, 0, 0, 0, 0, 0),
(107, 7, 0, 0, 0, 0, 0, 0),
(108, 7, 0, 0, 0, 0, 0, 0),
(109, 7, 0, 0, 0, 0, 0, 0),
(110, 7, 0, 0, 0, 0, 0, 0),
(111, 1, 0, 0, 0, 0, 0, 0),
(112, 1, 0, 0, 0, 0, 0, 0),
(113, 1, 0, 0, 0, 0, 0, 0),
(114, 1, 0, 0, 0, 0, 0, 0),
(115, 1, 0, 0, 0, 0, 0, 0),
(116, 1, 0, 0, 0, 0, 0, 0),
(117, 1, 0, 0, 0, 0, 0, 0),
(118, 1, 0, 0, 0, 0, 0, 0),
(119, 1, 0, 0, 0, 0, 0, 0),
(120, 1, 0, 0, 0, 0, 0, 0),
(121, 1, 0, 0, 0, 0, 0, 0),
(122, 1, 0, 0, 0, 0, 0, 0),
(123, 1, 0, 0, 0, 0, 0, 0),
(124, 1, 0, 0, 0, 0, 0, 0),
(125, 1, 0, 0, 0, 0, 0, 0),
(126, 1, 0, 0, 0, 0, 0, 0),
(127, 1, 0, 0, 0, 0, 0, 0),
(128, 1, 0, 0, 0, 0, 0, 0),
(129, 1, 0, 0, 0, 0, 0, 0),
(130, 1, 0, 0, 0, 0, 0, 0),
(131, 2, 10, 100, 180, 0, 5, 0),
(132, 6, 0, 0, 0, 0, 0, 0),
(133, 6, 0, 0, 0, 0, 0, 0),
(134, 6, 0, 0, 0, 0, 0, 0),
(135, 6, 0, 0, 0, 0, 0, 0),
(136, 6, 0, 0, 0, 0, 0, 0),
(137, 6, 0, 0, 0, 0, 0, 0),
(138, 6, 0, 0, 0, 0, 0, 0),
(139, 6, 0, 0, 0, 0, 0, 0),
(140, 6, 0, 0, 0, 0, 0, 0),
(141, 6, 0, 0, 0, 0, 0, 0),
(142, 6, 0, 0, 0, 0, 0, 0),
(143, 6, 0, 0, 0, 0, 0, 0),
(144, 6, 0, 0, 0, 0, 0, 0),
(145, 6, 0, 0, 0, 0, 0, 0),
(146, 6, 0, 0, 0, 0, 0, 0),
(147, 6, 0, 0, 0, 0, 0, 0),
(148, 6, 0, 0, 0, 0, 0, 0),
(149, 6, 0, 0, 0, 0, 0, 0),
(150, 6, 0, 0, 0, 0, 0, 0),
(151, 6, 0, 0, 0, 0, 0, 0),
(152, 6, 0, 0, 0, 0, 0, 0),
(153, 6, 0, 0, 0, 0, 0, 0),
(154, 6, 0, 0, 0, 0, 0, 0),
(155, 6, 0, 0, 0, 0, 0, 0),
(156, 6, 0, 0, 0, 0, 0, 0),
(157, 6, 0, 0, 0, 0, 0, 0),
(158, 6, 0, 0, 0, 0, 0, 0),
(159, 6, 0, 0, 0, 0, 0, 0),
(160, 6, 0, 0, 0, 0, 0, 0),
(161, 6, 0, 0, 0, 0, 0, 0),
(162, 6, 0, 0, 0, 0, 0, 0),
(163, 6, 0, 0, 0, 0, 0, 0),
(164, 6, 0, 0, 0, 0, 0, 0),
(165, 6, 0, 0, 0, 0, 0, 0),
(166, 6, 0, 0, 0, 0, 0, 0),
(167, 6, 0, 0, 0, 0, 0, 0),
(168, 6, 0, 0, 0, 0, 0, 0),
(169, 6, 0, 0, 0, 0, 0, 0),
(170, 6, 0, 0, 0, 0, 0, 0),
(171, 6, 0, 0, 0, 0, 0, 0),
(172, 6, 0, 0, 0, 0, 0, 0),
(173, 6, 0, 0, 0, 0, 0, 0),
(174, 6, 0, 0, 0, 0, 0, 0),
(175, 6, 0, 0, 0, 0, 0, 0),
(176, 6, 0, 0, 0, 0, 0, 0),
(177, 6, 0, 0, 0, 0, 0, 0),
(178, 6, 0, 0, 0, 0, 0, 0),
(179, 6, 0, 0, 0, 0, 0, 0),
(180, 6, 0, 0, 0, 0, 0, 0),
(181, 6, 0, 0, 0, 0, 0, 0),
(182, 6, 0, 0, 0, 0, 0, 0),
(183, 6, 0, 0, 0, 0, 0, 0),
(184, 6, 0, 0, 0, 0, 0, 0),
(185, 6, 0, 0, 0, 0, 0, 0),
(186, 6, 0, 0, 0, 0, 0, 0),
(187, 6, 0, 0, 0, 0, 0, 0),
(188, 6, 0, 0, 0, 0, 0, 0),
(189, 6, 0, 0, 0, 0, 0, 0),
(190, 6, 0, 0, 0, 0, 0, 0),
(191, 6, 0, 0, 0, 0, 0, 0),
(192, 6, 0, 0, 0, 0, 0, 0),
(193, 6, 0, 0, 0, 0, 0, 0),
(194, 6, 0, 0, 0, 0, 0, 0),
(195, 6, 0, 0, 0, 0, 0, 0),
(196, 6, 0, 0, 0, 0, 0, 0),
(197, 6, 0, 0, 0, 0, 0, 0),
(198, 6, 0, 0, 0, 0, 0, 0),
(199, 6, 0, 0, 0, 0, 0, 0),
(200, 6, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `lettres_types`
--

CREATE TABLE IF NOT EXISTS `lettres_types` (
  `id_lettre_type` int(11) NOT NULL AUTO_INCREMENT,
  `titre_lettre_type` varchar(250) NOT NULL,
  `categorie_lettre_type` varchar(250) NOT NULL,
  `reponse_lettre_type` varchar(3) NOT NULL,
  PRIMARY KEY (`id_lettre_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `lettres_types`
--

INSERT INTO `lettres_types` (`id_lettre_type`, `titre_lettre_type`, `categorie_lettre_type`, `reponse_lettre_type`) VALUES
(1, 'blame', 'sanction', ''),
(2, 'convocation des parents', 'suivi', ''),
(3, 'avertissement', 'sanction', ''),
(4, 'exclusion', 'sanction', ''),
(5, 'certificat de scolarité', 'suivi', ''),
(6, 'demande de justificatif d''absence', 'suivi', 'oui'),
(7, 'demande de justificatif de retard', 'suivi', ''),
(8, 'rapport d''incident', 'sanction', ''),
(9, 'regime de sortie', 'suivi', ''),
(10, 'retenue', 'sanction', '');

-- --------------------------------------------------------

--
-- Structure de la table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `LOGIN` varchar(50) NOT NULL DEFAULT '',
  `START` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `SESSION_ID` varchar(255) NOT NULL DEFAULT '',
  `REMOTE_ADDR` varchar(16) NOT NULL DEFAULT '',
  `USER_AGENT` varchar(255) NOT NULL DEFAULT '',
  `REFERER` varchar(64) NOT NULL DEFAULT '',
  `AUTOCLOSE` enum('0','1','2','3','4') NOT NULL DEFAULT '0',
  `END` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`SESSION_ID`,`START`),
  KEY `start_time` (`START`),
  KEY `end_time` (`END`),
  KEY `login_session_start` (`LOGIN`,`SESSION_ID`,`START`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `log`
--

INSERT INTO `log` (`LOGIN`, `START`, `SESSION_ID`, `REMOTE_ADDR`, `USER_AGENT`, `REFERER`, `AUTOCLOSE`, `END`) VALUES
('admin', '2012-01-18 09:32:08', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', 'http://localhost/Gepi/login.php', '4', '2012-01-18 09:32:08'),
('ADMIN', '2012-01-18 09:34:07', 'qd6oih6mea4bdjccr2752t2sr3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '0', '2012-01-18 09:45:19'),
('ADMIN', '2012-01-18 10:03:41', 'n0qj6lj7v2r7ha01p83sfjd0t5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '0', '2012-01-18 10:13:38'),
('ADMIN', '2012-01-18 14:03:43', 'v0i9pk2de80f06nlu6lhh34q51', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-01-18 14:49:42'),
('ADMIN', '2012-01-20 11:06:25', 'plvmh6u5jr8cf78ahkko8eneg4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '0', '2012-01-20 11:07:36'),
('ADMIN', '2012-01-20 11:09:01', 'p369k1uqkodk2ij8ugsvbk4ro6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-01-20 11:56:13'),
('ADMIN', '2012-01-20 11:26:13', 'p369k1uqkodk2ij8ugsvbk4ro6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '0', '2012-01-20 11:34:19'),
('admin', '2012-01-20 11:55:45', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', 'http://localhost/Gepi/login.php', '4', '2012-01-20 11:55:45'),
('', '2012-01-20 12:18:24', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', 'http://localhost/Gepi/login.php', '4', '2012-01-20 12:18:24'),
('ADMIN', '2012-01-20 12:19:00', 'cs19tdcb2hvibu5ff64d71tit1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-01-20 12:49:00'),
('', '2012-01-21 09:34:26', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', 'http://localhost/Gepi/', '4', '2012-01-21 09:34:26'),
('admin', '2012-01-21 09:34:43', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', 'http://localhost/Gepi/login.php', '4', '2012-01-21 09:34:43'),
('ADMIN', '2012-01-21 09:34:55', 'qoi3pqp2phmrp1cr32rcot4et0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-01-21 10:35:17'),
('ADMIN', '2012-01-23 11:08:19', 'fc4n6hblasav7irme8fsug7vk2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '3', '2012-01-23 12:05:38'),
('ADMIN', '2012-01-24 09:42:39', 'u1um9mo33gcddos7i3hk7gt753', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '0', '2012-01-24 10:47:17'),
('ADMIN', '2012-01-24 10:47:31', 'f298f5oldl99d33nslmk865dq3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '0', '2012-01-24 12:31:54'),
('ADMIN', '2012-01-27 12:25:04', '7d15h0g77o3r8bhsq906jfe0i5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-01-27 13:25:15'),
('admin', '2012-02-05 13:37:57', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', 'http://localhost/Gepi/', '4', '2012-02-05 13:37:57'),
('admin', '2012-02-05 13:39:41', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', 'http://localhost/Gepi/login.php', '4', '2012-02-05 13:39:41'),
('ADMIN', '2012-02-05 13:40:08', 'fk5nuobt21ti9ee2s39g822133', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-02-05 14:24:20'),
('ADMIN', '2012-02-05 14:24:31', '3a45loq87ufqp4abh6n8egq2u5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-02-05 14:25:08'),
('ADMIN', '2012-02-05 14:25:22', '6tnerucofep915vuuclpfei3m5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '3', '2012-02-05 16:33:30'),
('admin', '2012-02-05 16:33:48', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', 'http://localhost/Gepi/login.php', '4', '2012-02-05 16:33:48'),
('ADMIN', '2012-02-05 16:34:01', 'ujtfq4m03bbs0074d4ngqhf8j0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-02-05 17:20:48'),
('admin', '2012-02-06 10:02:25', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', 'http://localhost/Gepi/', '4', '2012-02-06 10:02:25'),
('ADMIN', '2012-02-06 10:06:38', 'snou469d6qktp5al3bog8dm4l7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-02-06 10:42:53'),
('ADMIN', '2012-02-13 16:57:23', '8v229jj3nuag1ba1mp85ucugc4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '2', '2012-02-13 16:57:24'),
('ADMIN', '2012-02-13 16:58:21', '7bk9dsdnu1be5od58oda4jcb44', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '2', '2012-02-13 16:58:22'),
('admin', '2012-02-13 16:58:58', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', 'http://localhost/gepii/Gepi/', '4', '2012-02-13 16:58:58'),
('admin', '2012-02-13 16:59:14', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', 'http://localhost/gepii/Gepi/login.php', '4', '2012-02-13 16:59:14'),
('admin', '2012-02-13 16:59:32', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', 'http://localhost/gepii/Gepi/login.php', '4', '2012-02-13 16:59:32'),
('admin', '2012-02-13 17:01:32', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', 'http://localhost/gepii/', '4', '2012-02-13 17:01:32'),
('ADMIN', '2012-02-13 17:02:53', 'f5ai27t12kh6fk4td979s7h617', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-13 18:01:18'),
('ADMIN', '2012-02-13 18:01:39', 'f50ngt817dopgkapfv7m28omv3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '1', '2012-02-13 18:31:39'),
('admin', '2012-02-15 08:04:38', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', 'http://localhost/gepii/', '4', '2012-02-15 08:04:38'),
('admin', '2012-02-15 08:06:15', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', 'http://localhost/gepii/login.php', '4', '2012-02-15 08:06:15'),
('admin', '2012-02-15 08:06:30', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', 'http://localhost/gepii/login.php', '4', '2012-02-15 08:06:30'),
('admin', '2012-02-15 08:09:06', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', 'http://localhost/gepii/login.php', '4', '2012-02-15 08:09:06'),
('ADMIN', '2012-02-15 08:09:19', 'hm65f8gnimejg03i8trv5judd2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '0', '2012-02-15 08:13:42'),
('ADMIN', '2012-02-15 08:13:57', '6g37hmh5a3bfcsm3q69ueqjth6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-15 09:32:32'),
('ADMIN', '2012-02-15 09:32:42', 'vm2gdsn939dutgbidrag0ah636', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '1', '2012-02-15 10:02:42'),
('ADMIN', '2012-02-15 11:43:56', 'd384f6eokt8npqai913a55dbt5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-15 20:49:31'),
('ADMIN', '2012-02-15 20:49:53', 'v62r52oai2gipqhqd2rhnvgkq4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-15 22:46:44'),
('ADMIN', '2012-02-15 22:46:53', '7ckih2s6257kc3iskc6f86d2b7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-16 09:49:34'),
('elijah', '2012-02-15 23:07:11', '80edcdo0496l316gmvvoeov1l3', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.9.168 Version/11.50', '', '3', '2012-02-16 15:38:29'),
('ADMIN', '2012-02-16 09:49:45', '1lco55ps031evh46b52h49btg0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '1', '2012-02-16 10:19:46'),
('ADMIN', '2012-02-16 09:49:46', '1lco55ps031evh46b52h49btg0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-16 20:54:53'),
('ADMIN', '2012-02-16 20:55:06', 'nu2rc0dnuvhp7onrrq23q83850', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-16 22:26:52'),
('ADMIN', '2012-02-17 11:56:03', 'o8b4n4ofvl9gemp034d0hk19a7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-17 12:35:13'),
('ADMIN', '2012-02-17 12:35:23', 'mshh3k6ic03foe25htvg7uvgv7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-17 14:51:06'),
('ADMIN', '2012-02-21 00:01:58', 'jiqrakosbkejfq3fmr3mc9uj06', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '1', '2012-02-21 00:47:37'),
('ADMIN', '2012-02-21 22:25:17', '7ijapdvcq440c7biu4ioc0nqn2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '0', '2012-02-21 23:05:25'),
('ADMIN', '2012-02-22 09:25:26', 'pv0572ea55d7cfh942bodlq4e3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-22 10:45:22'),
('ADMIN', '2012-02-22 10:45:31', '6digatncmnujujvjve60jp8cn2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-22 18:42:47'),
('ADMIN', '2012-02-22 18:43:00', '55mqg7ccsj1qhe6gg40birtt42', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '1', '2012-02-22 19:16:51'),
('ADMIN', '2012-02-23 13:25:09', '9id40ao7ctpqsjtoijg1dlkrt0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '3', '2012-02-23 18:29:27'),
('ADMIN', '2012-02-23 18:29:39', '6bnes0vvtitl2t3q4barl0s204', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.1) Gecko/20100101 Firefox/10.0.1', '', '1', '2012-02-23 19:19:03'),
('ADMIN', '2012-02-25 08:48:46', 'fe0qgg1kuue9gpko9tjp4jn1i0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-02-25 11:30:02'),
('ADMIN', '2012-02-25 11:30:14', '5ldk5vvm0moi1chj3mitoie7b1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-02-25 13:53:38'),
('ADMIN', '2012-02-25 13:53:46', 'vi7c3cf7962f3t7lm6ikc2nqf1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-02-25 14:26:38'),
('ADMIN', '2012-02-25 14:26:46', 'i5mtat8e0vl8v2o2867h8u60c6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-02-25 17:24:02'),
('ADMIN', '2012-02-25 17:24:14', '36h935gb9drll9ade6tsrhaka0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-02-25 18:21:24'),
('ADMIN', '2012-02-25 18:21:33', 'a9hhit3mlau7eq738vnkcdqc31', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-02-26 09:51:53'),
('ADMIN', '2012-02-26 09:52:15', 'jm113bbuhe9jthv7adbhtr4s45', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-02-26 10:28:18'),
('ADMIN', '2012-02-26 16:51:15', '60bme5mcblprmu9r80tjlhumu2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-02-26 18:06:54'),
('Makengne', '2012-02-26 17:21:21', '6e2etv5sdh36e3i474gs6r00n1', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '4', '2012-02-26 17:35:10'),
('Nikeme', '2012-02-26 17:35:10', '', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', 'http://localhost/gepii/', '4', '2012-02-26 17:35:10'),
('Nikeme', '2012-02-26 17:35:24', '', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', 'http://localhost/gepii/login.php', '4', '2012-02-26 17:35:24'),
('Makengne', '2012-02-26 17:35:38', 'cahavi1f0j6kfaorr92q4pn2i4', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '1', '2012-02-26 18:06:10'),
('ADMIN', '2012-02-27 08:50:06', 'h0q6las4rvec2dp4uo1r31juv6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-02-27 18:02:07'),
('Makengne', '2012-02-27 09:18:10', '1tjb2n7nbissi13462on6r2sh0', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '0', '2012-02-27 09:39:35'),
('Makengne', '2012-02-27 09:39:51', 'aik89hm13rmo43fqu7aunn8o02', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '1', '2012-02-27 10:25:03'),
('Makengne', '2012-02-27 09:41:24', '2plg0k2q1ht2g3dfgmdeqtlps5', '192.168.137.121', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-02-27 10:25:11'),
('ADMIN', '2012-02-27 18:02:26', '2p1ni3upm4q4da4nnosehslj70', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-02-28 13:30:23'),
('ADMIN', '2012-02-28 13:30:33', '6achd9l4htiejqao7i189ofa07', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-02-28 14:00:33'),
('ADMIN', '2012-02-28 15:32:57', 'osi6ov5fgcebj0vg4h5450mg13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '0', '2012-02-28 15:44:00'),
('ADMIN', '2012-02-28 15:44:18', 'fqbgjcdss84rcsfit5c22eh035', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '0', '2012-02-28 15:44:21'),
('fsdf', '2012-02-28 15:44:34', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-02-28 15:44:34'),
('ADMIN', '2012-02-28 15:44:49', 'vk6cro4c4jt7cfsrg6v437g4s3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-02-29 13:17:26'),
('Makengne', '2012-02-28 16:14:58', 'r7b5m5mlq1rck22610qb85orc5', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '1', '2012-02-28 16:44:58'),
('Makengne', '2012-02-29 13:23:20', 'f3bes585grmmm6k6scotg58f82', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '1', '2012-02-29 13:54:35'),
('admin', '2012-02-29 13:25:25', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-02-29 13:25:25'),
('admin', '2012-02-29 13:25:38', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-02-29 13:25:38'),
('ADMIN', '2012-02-29 13:25:47', 'dtte7od0lappbnt4m45torhef6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-02-29 13:56:39'),
('admin', '2012-03-01 09:24:23', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/', '4', '2012-03-01 09:24:23'),
('ADMIN', '2012-03-01 09:24:38', '0lefifall60iulrgd96j89o3h5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-01 11:43:28'),
('ADMIN', '2012-03-01 11:43:37', 'trenf3dt74vpp1cq66prsl1hq6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-02 04:52:50'),
('ADMIN', '2012-03-02 04:55:04', 'i04565r3p50hgsckhavi89ntj5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-03-02 05:33:14'),
('admin', '2012-03-02 05:33:31', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-02 05:33:31'),
('admin', '2012-03-02 05:33:40', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-02 05:33:40'),
('ADMIN', '2012-03-02 05:35:11', 'gdnig13589fmpskts4uip091q4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-03-02 05:35:34'),
('admin', '2012-03-02 05:42:12', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-02 05:42:12'),
('admin', '2012-03-02 05:42:57', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-02 05:42:57'),
('admin', '2012-03-02 05:44:06', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-02 05:44:06'),
('admin', '2012-03-02 05:44:54', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-02 05:44:54'),
('duo', '2012-03-02 05:45:31', '458acnkakdr8r5mokdhbfvvoc6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-02 08:35:44'),
('admin', '2012-03-02 08:35:52', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-02 08:35:52'),
('admin', '2012-03-02 08:36:04', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-02 08:36:04'),
('duo', '2012-03-02 08:36:22', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-02 08:36:22'),
('duo', '2012-03-02 08:36:40', 'n81scdlfi190b63qfeaeoc3eb4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-02 09:22:05'),
('duo', '2012-03-02 09:22:19', 'tp2bunelitbbi8b1qsu74hv7n7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-02 10:42:52'),
('elijah', '2012-03-02 09:39:02', 'hj4k0mfu3piot2qurbmqokruk6', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '3', '2012-03-02 10:52:23'),
('Makengne', '2012-03-02 09:42:48', 'i71avdrg87oi33raf33f7dhep0', '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; GTB7.2; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; eSobiSubscriber 2.0.4.16; MAAR; InfoPath.3; .NET4.0C; .NET CLR 1.1.4322)', '', '3', '2012-03-02 11:22:12'),
('duo', '2012-03-02 10:43:01', '9cicrkfdrchptkgmdt57acmb83', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '0', '2012-03-02 11:01:48'),
('elijah', '2012-03-02 10:52:31', 'i78ve4hp4l6mku5gkeb2lobrh0', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '0', '2012-03-02 11:13:05'),
('duo', '2012-03-02 11:02:15', 'a2udnt4qusv73lrdlh9kerrcv5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-02 15:14:42'),
('elijah', '2012-03-02 11:13:16', 'lbigdv4q7g7at1on9sb72mi452', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '3', '2012-03-02 21:02:09'),
('scolar', '2012-03-02 11:19:50', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11', 'http://localhost/gepii/', '4', '2012-03-02 11:19:50'),
('scolar', '2012-03-02 11:20:18', 'uen8hj6drgu1qqfur88lh1brp5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11', '', '1', '2012-03-02 11:57:52'),
('Makengne', '2012-03-02 11:22:23', '', '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; GTB7.2; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; eSobiSubscriber 2.0.4.16; MAAR; InfoPath.3; .NET4.0C; .NET CLR 1.1.4322)', 'http://localhost/gepii/login.php', '4', '2012-03-02 11:22:23'),
('Makengne', '2012-03-02 11:22:31', 'vs8f7s1emq2l8tje5fagcur682', '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; GTB7.2; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; eSobiSubscriber 2.0.4.16; MAAR; InfoPath.3; .NET4.0C; .NET CLR 1.1.4322)', '', '3', '2012-03-02 21:01:31'),
('admin', '2012-03-02 22:33:28', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/', '4', '2012-03-02 22:33:28'),
('duo', '2012-03-02 22:33:37', 'g7k2n5vb7nn1c2t3f6a2amc4t2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-03-02 23:03:52'),
('admin', '2012-03-03 00:32:27', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/', '4', '2012-03-03 00:32:27'),
('duo', '2012-03-03 00:32:41', 'inorl1mfo7t4t4o77ndceolfq6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-03-03 01:26:03'),
('duo', '2012-03-03 00:56:03', 'inorl1mfo7t4t4o77ndceolfq6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-03 07:13:01'),
('duo', '2012-03-03 07:13:29', 'p6hkn0f0hcteuglpf25o1v33a1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-03-03 08:11:48'),
('duo', '2012-03-03 08:22:07', 't15u24d6mbgdr9trk1s1l4l822', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-03 10:56:20'),
('duo', '2012-03-03 09:16:35', '9ebiuogkk2hu5nl03l6bogf8c0', '192.168.10.2', 'Mozilla/5.0 (Windows NT 6.1; rv:8.0.1) Gecko/20100101 Firefox/8.0.1', '', '1', '2012-03-03 12:14:21'),
('elijah', '2012-03-03 10:28:09', 'imc7crslnkcd29e53mo8609g24', '127.0.0.1', 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61', '', '1', '2012-03-03 11:11:49'),
('duo', '2012-03-03 10:56:32', '3sma17lblklsb3trcle9mkb970', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-03 12:37:08'),
('duo', '2012-03-03 12:37:34', 'p09jtfokufiibre26pfe5kd471', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '3', '2012-03-03 14:33:24'),
('Makengne', '2012-03-03 14:37:25', 'vepsa98k1u3bpn8601a870ca31', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '0', '2012-03-03 14:41:26'),
('duo', '2012-03-03 14:41:38', 'dk8q2kocarv14hdp2rl1j1aio3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '0', '2012-03-03 15:19:28'),
('elijah', '2012-03-03 15:19:40', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', 'http://localhost/gepii/login.php', '4', '2012-03-03 15:19:40'),
('elijah', '2012-03-03 15:19:55', '28v2einlnlifr76v98i3se44c1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '4', '2012-03-03 15:22:49'),
('Makengne', '2012-03-03 15:22:49', 'ufi95u3t4l0sgmk9b18o1og4i3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '0', '2012-03-03 15:25:20'),
('duo', '2012-03-03 15:25:37', 'njmnsvjng3jjbh50va0fqqc080', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '1', '2012-03-03 15:57:09'),
('duo', '2012-03-05 09:40:34', 'albph7vrcu3u6bn1uqcjnbks36', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2', '', '0', '2012-03-05 10:07:26'),
('admin', '2012-03-22 17:56:25', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1', 'http://localhost/Gepi2/login.php', '4', '2012-03-22 17:56:25'),
('admin', '2012-03-22 17:56:43', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1', 'http://localhost/Gepi2/login.php', '4', '2012-03-22 17:56:43'),
('ADMIN', '2012-03-22 17:59:07', '6i5cutc621rcr4aobmj59qf332', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1', '', '0', '2012-03-22 18:04:16'),
('euie', '2012-03-26 11:38:25', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1', 'http://localhost/Gepi2/login.php', '4', '2012-03-26 11:38:25'),
('ADMIN', '2012-04-07 10:08:07', 'mpjd6059ms42n0bvajunr1u9c0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1', '', '1', '2012-04-07 11:09:09');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE IF NOT EXISTS `matieres` (
  `matiere` varchar(255) NOT NULL DEFAULT '',
  `nom_complet` varchar(200) NOT NULL DEFAULT '',
  `priority` smallint(6) NOT NULL DEFAULT '0',
  `categorie_id` int(11) NOT NULL DEFAULT '1',
  `matiere_aid` char(1) NOT NULL DEFAULT 'n',
  `matiere_atelier` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`matiere`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres`
--

INSERT INTO `matieres` (`matiere`, `nom_complet`, `priority`, `categorie_id`, `matiere_aid`, `matiere_atelier`) VALUES
('Maths', 'Maths.', 11, 2, 'n', 'n'),
('English_language', 'English Lang.', 12, 2, 'n', 'n'),
('French', 'French', 13, 2, 'n', 'n'),
('History', 'History', 14, 3, 'n', 'n'),
('Geography', 'Geography', 15, 3, 'n', 'n'),
('English_literature', 'English Lit.', 16, 3, 'n', 'n'),
('Chemistry', 'Chemistry', 17, 4, 'n', 'n'),
('Biology', 'Biology', 18, 4, 'n', 'n'),
('Physics', 'Physics', 19, 4, 'n', 'n'),
('Latin', 'Latin', 20, 6, 'n', 'n'),
('Spanish', 'Spanish', 21, 6, 'n', 'n'),
('Computer', 'Information Technology', 22, 6, 'n', 'n'),
('Religion', 'Religion', 23, 6, 'n', 'n'),
('Citizenship', 'Citizenship', 24, 6, 'n', 'n');

-- --------------------------------------------------------

--
-- Structure de la table `matieres_appreciations`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY (`login`,`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_appreciations`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_appreciations_acces`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations_acces` (
  `id_classe` int(11) NOT NULL,
  `statut` varchar(255) NOT NULL,
  `periode` int(11) NOT NULL,
  `date` date NOT NULL,
  `acces` enum('y','n','date','d') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_appreciations_acces`
--

INSERT INTO `matieres_appreciations_acces` (`id_classe`, `statut`, `periode`, `date`, `acces`) VALUES
(1, 'responsable', 1, '0000-00-00', 'y'),
(1, 'responsable', 2, '0000-00-00', 'y'),
(1, 'responsable', 3, '0000-00-00', 'n'),
(2, 'responsable', 1, '0000-00-00', 'n'),
(2, 'responsable', 2, '0000-00-00', 'n'),
(2, 'responsable', 3, '0000-00-00', 'n'),
(1, 'eleve', 1, '0000-00-00', 'y'),
(1, 'eleve', 2, '0000-00-00', 'y');

-- --------------------------------------------------------

--
-- Structure de la table `matieres_appreciations_grp`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations_grp` (
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY (`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_appreciations_grp`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_appreciations_tempo`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations_tempo` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY (`login`,`id_groupe`,`periode`),
  KEY `groupe_periode` (`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_appreciations_tempo`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_app_corrections`
--

CREATE TABLE IF NOT EXISTS `matieres_app_corrections` (
  `login` varchar(255) NOT NULL DEFAULT '',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY (`login`,`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_app_corrections`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_app_delais`
--

CREATE TABLE IF NOT EXISTS `matieres_app_delais` (
  `periode` int(11) NOT NULL DEFAULT '0',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `date_limite` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`periode`,`id_groupe`),
  KEY `id_groupe` (`id_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_app_delais`
--


-- --------------------------------------------------------

--
-- Structure de la table `matieres_categories`
--

CREATE TABLE IF NOT EXISTS `matieres_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_court` varchar(255) NOT NULL DEFAULT '',
  `nom_complet` varchar(255) NOT NULL DEFAULT '',
  `priority` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `matieres_categories`
--

INSERT INTO `matieres_categories` (`id`, `nom_court`, `nom_complet`, `priority`) VALUES
(1, 'Autres', 'Autres', 6),
(2, 'Compulsory', 'Compulsory subject', 1),
(3, 'Arts', 'Arts subjects', 2),
(4, 'Science', 'Scientific subject', 3),
(5, 'Commercial', 'Commercial subject', 4),
(6, 'Additional', 'Additional subject', 5);

-- --------------------------------------------------------

--
-- Structure de la table `matieres_notes`
--

CREATE TABLE IF NOT EXISTS `matieres_notes` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `note` float(10,1) DEFAULT NULL,
  `statut` varchar(10) NOT NULL DEFAULT '',
  `rang` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login`,`id_groupe`,`periode`),
  KEY `groupe_periode_statut` (`id_groupe`,`periode`,`statut`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `matieres_notes`
--


-- --------------------------------------------------------

--
-- Structure de la table `mef`
--

CREATE TABLE IF NOT EXISTS `mef` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire de la classe',
  `mef_code` bigint(20) DEFAULT NULL COMMENT 'Numero de la nomenclature officielle (numero MEF)',
  `libelle_court` varchar(50) NOT NULL COMMENT 'libelle de la formation',
  `libelle_long` varchar(300) NOT NULL COMMENT 'libelle de la formation',
  `libelle_edition` varchar(300) NOT NULL COMMENT 'libelle de la formation pour presentation',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Module ?l?mentaire de formation' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `mef`
--


-- --------------------------------------------------------

--
-- Structure de la table `mentions`
--

CREATE TABLE IF NOT EXISTS `mentions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mention` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `mentions`
--

INSERT INTO `mentions` (`id`, `mention`) VALUES
(1, 'PASSED'),
(2, 'FAILED');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `texte` text NOT NULL,
  `date_debut` int(11) NOT NULL DEFAULT '0',
  `date_fin` int(11) NOT NULL DEFAULT '0',
  `auteur` varchar(50) NOT NULL DEFAULT '',
  `statuts_destinataires` varchar(10) NOT NULL DEFAULT '',
  `login_destinataire` varchar(50) NOT NULL DEFAULT '',
  `date_decompte` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `date_debut_fin` (`date_debut`,`date_fin`),
  KEY `login_destinataire` (`login_destinataire`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `messages`
--


-- --------------------------------------------------------

--
-- Structure de la table `message_login`
--

CREATE TABLE IF NOT EXISTS `message_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `texte` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `message_login`
--


-- --------------------------------------------------------

--
-- Structure de la table `miseajour`
--

CREATE TABLE IF NOT EXISTS `miseajour` (
  `id_miseajour` int(11) NOT NULL AUTO_INCREMENT,
  `fichier_miseajour` varchar(250) NOT NULL,
  `emplacement_miseajour` varchar(250) NOT NULL,
  `date_miseajour` date NOT NULL,
  `heure_miseajour` time NOT NULL,
  PRIMARY KEY (`id_miseajour`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `miseajour`
--


-- --------------------------------------------------------

--
-- Structure de la table `modeles_grilles_pdf`
--

CREATE TABLE IF NOT EXISTS `modeles_grilles_pdf` (
  `id_modele` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL DEFAULT '',
  `nom_modele` varchar(255) NOT NULL,
  `par_defaut` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`id_modele`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `modeles_grilles_pdf`
--


-- --------------------------------------------------------

--
-- Structure de la table `modeles_grilles_pdf_valeurs`
--

CREATE TABLE IF NOT EXISTS `modeles_grilles_pdf_valeurs` (
  `id_modele` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL DEFAULT '',
  `valeur` varchar(255) NOT NULL,
  KEY `id_modele_champ` (`id_modele`,`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `modeles_grilles_pdf_valeurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `model_bulletin`
--

CREATE TABLE IF NOT EXISTS `model_bulletin` (
  `id_model_bulletin` int(11) NOT NULL AUTO_INCREMENT,
  `nom_model_bulletin` varchar(100) NOT NULL DEFAULT '',
  `active_bloc_datation` decimal(4,0) NOT NULL DEFAULT '0',
  `active_bloc_eleve` tinyint(4) NOT NULL DEFAULT '0',
  `active_bloc_adresse_parent` tinyint(4) NOT NULL DEFAULT '0',
  `active_bloc_absence` tinyint(4) NOT NULL DEFAULT '0',
  `active_bloc_note_appreciation` tinyint(4) NOT NULL DEFAULT '0',
  `active_bloc_avis_conseil` tinyint(4) NOT NULL DEFAULT '0',
  `active_bloc_chef` tinyint(4) NOT NULL DEFAULT '0',
  `active_photo` tinyint(4) NOT NULL DEFAULT '0',
  `active_coef_moyenne` tinyint(4) NOT NULL DEFAULT '0',
  `active_nombre_note` tinyint(4) NOT NULL DEFAULT '0',
  `active_nombre_note_case` tinyint(4) NOT NULL DEFAULT '0',
  `active_moyenne` tinyint(4) NOT NULL DEFAULT '0',
  `active_moyenne_eleve` tinyint(4) NOT NULL DEFAULT '0',
  `active_moyenne_classe` tinyint(4) NOT NULL DEFAULT '0',
  `active_moyenne_min` tinyint(4) NOT NULL DEFAULT '0',
  `active_moyenne_max` tinyint(4) NOT NULL DEFAULT '0',
  `active_regroupement_cote` tinyint(4) NOT NULL DEFAULT '0',
  `active_entete_regroupement` tinyint(4) NOT NULL DEFAULT '0',
  `active_moyenne_regroupement` tinyint(4) NOT NULL DEFAULT '0',
  `active_rang` tinyint(4) NOT NULL DEFAULT '0',
  `active_graphique_niveau` tinyint(4) NOT NULL DEFAULT '0',
  `active_appreciation` tinyint(4) NOT NULL DEFAULT '0',
  `affiche_doublement` tinyint(4) NOT NULL DEFAULT '0',
  `affiche_date_naissance` tinyint(4) NOT NULL DEFAULT '0',
  `affiche_dp` tinyint(4) NOT NULL DEFAULT '0',
  `affiche_nom_court` tinyint(4) NOT NULL DEFAULT '0',
  `affiche_effectif_classe` tinyint(4) NOT NULL DEFAULT '0',
  `affiche_numero_impression` tinyint(4) NOT NULL DEFAULT '0',
  `caractere_utilse` varchar(20) NOT NULL DEFAULT '',
  `X_parent` float NOT NULL DEFAULT '0',
  `Y_parent` float NOT NULL DEFAULT '0',
  `X_eleve` float NOT NULL DEFAULT '0',
  `Y_eleve` float NOT NULL DEFAULT '0',
  `cadre_eleve` tinyint(4) NOT NULL DEFAULT '0',
  `X_datation_bul` float NOT NULL DEFAULT '0',
  `Y_datation_bul` float NOT NULL DEFAULT '0',
  `cadre_datation_bul` tinyint(4) NOT NULL DEFAULT '0',
  `hauteur_info_categorie` float NOT NULL DEFAULT '0',
  `X_note_app` float NOT NULL DEFAULT '0',
  `Y_note_app` float NOT NULL DEFAULT '0',
  `longeur_note_app` float NOT NULL DEFAULT '0',
  `hauteur_note_app` float NOT NULL DEFAULT '0',
  `largeur_coef_moyenne` float NOT NULL DEFAULT '0',
  `largeur_nombre_note` float NOT NULL DEFAULT '0',
  `largeur_d_une_moyenne` float NOT NULL DEFAULT '0',
  `largeur_niveau` float NOT NULL DEFAULT '0',
  `largeur_rang` float NOT NULL DEFAULT '0',
  `X_absence` float NOT NULL DEFAULT '0',
  `Y_absence` float NOT NULL DEFAULT '0',
  `hauteur_entete_moyenne_general` float NOT NULL DEFAULT '0',
  `X_avis_cons` float NOT NULL DEFAULT '0',
  `Y_avis_cons` float NOT NULL DEFAULT '0',
  `longeur_avis_cons` float NOT NULL DEFAULT '0',
  `hauteur_avis_cons` float NOT NULL DEFAULT '0',
  `cadre_avis_cons` tinyint(4) NOT NULL DEFAULT '0',
  `X_sign_chef` float NOT NULL DEFAULT '0',
  `Y_sign_chef` float NOT NULL DEFAULT '0',
  `longeur_sign_chef` float NOT NULL DEFAULT '0',
  `hauteur_sign_chef` float NOT NULL DEFAULT '0',
  `cadre_sign_chef` tinyint(4) NOT NULL DEFAULT '0',
  `affiche_filigrame` tinyint(4) NOT NULL DEFAULT '0',
  `texte_filigrame` varchar(100) NOT NULL DEFAULT '',
  `affiche_logo_etab` tinyint(4) NOT NULL DEFAULT '0',
  `entente_mel` tinyint(4) NOT NULL DEFAULT '0',
  `entente_tel` tinyint(4) NOT NULL DEFAULT '0',
  `entente_fax` tinyint(4) NOT NULL DEFAULT '0',
  `L_max_logo` tinyint(4) NOT NULL DEFAULT '0',
  `H_max_logo` tinyint(4) NOT NULL DEFAULT '0',
  `toute_moyenne_meme_col` tinyint(4) NOT NULL DEFAULT '0',
  `active_reperage_eleve` tinyint(4) NOT NULL DEFAULT '0',
  `couleur_reperage_eleve1` smallint(6) NOT NULL DEFAULT '0',
  `couleur_reperage_eleve2` smallint(6) NOT NULL DEFAULT '0',
  `couleur_reperage_eleve3` smallint(6) NOT NULL DEFAULT '0',
  `couleur_categorie_entete` tinyint(4) NOT NULL DEFAULT '0',
  `couleur_categorie_entete1` smallint(6) NOT NULL DEFAULT '0',
  `couleur_categorie_entete2` smallint(6) NOT NULL DEFAULT '0',
  `couleur_categorie_entete3` smallint(6) NOT NULL DEFAULT '0',
  `couleur_categorie_cote` tinyint(4) NOT NULL DEFAULT '0',
  `couleur_categorie_cote1` smallint(6) NOT NULL DEFAULT '0',
  `couleur_categorie_cote2` smallint(6) NOT NULL DEFAULT '0',
  `couleur_categorie_cote3` smallint(6) NOT NULL DEFAULT '0',
  `couleur_moy_general` tinyint(4) NOT NULL DEFAULT '0',
  `couleur_moy_general1` smallint(6) NOT NULL DEFAULT '0',
  `couleur_moy_general2` smallint(6) NOT NULL DEFAULT '0',
  `couleur_moy_general3` smallint(6) NOT NULL DEFAULT '0',
  `titre_entete_matiere` varchar(50) NOT NULL DEFAULT '',
  `titre_entete_coef` varchar(20) NOT NULL DEFAULT '',
  `titre_entete_nbnote` varchar(20) NOT NULL DEFAULT '',
  `titre_entete_rang` varchar(20) NOT NULL DEFAULT '',
  `titre_entete_appreciation` varchar(50) NOT NULL DEFAULT '',
  `active_coef_sousmoyene` tinyint(4) NOT NULL DEFAULT '0',
  `arrondie_choix` float NOT NULL DEFAULT '0',
  `nb_chiffre_virgule` tinyint(4) NOT NULL DEFAULT '0',
  `chiffre_avec_zero` tinyint(4) NOT NULL DEFAULT '0',
  `autorise_sous_matiere` tinyint(4) NOT NULL DEFAULT '0',
  `affichage_haut_responsable` tinyint(4) NOT NULL DEFAULT '0',
  `entete_model_bulletin` tinyint(4) NOT NULL DEFAULT '0',
  `ordre_entete_model_bulletin` tinyint(4) NOT NULL DEFAULT '0',
  `affiche_etab_origine` tinyint(4) NOT NULL DEFAULT '0',
  `imprime_pour` tinyint(4) NOT NULL DEFAULT '0',
  `largeur_matiere` float NOT NULL DEFAULT '0',
  `nom_etab_gras` tinyint(4) NOT NULL,
  `taille_texte_date_edition` float NOT NULL,
  `taille_texte_matiere` float NOT NULL,
  `active_moyenne_general` tinyint(4) NOT NULL,
  `titre_bloc_avis_conseil` varchar(50) NOT NULL,
  `taille_titre_bloc_avis_conseil` float NOT NULL,
  `taille_profprincipal_bloc_avis_conseil` float NOT NULL,
  `affiche_fonction_chef` tinyint(4) NOT NULL,
  `taille_texte_fonction_chef` float NOT NULL,
  `taille_texte_identitee_chef` float NOT NULL,
  `tel_image` varchar(20) NOT NULL,
  `tel_texte` varchar(20) NOT NULL,
  `fax_image` varchar(20) NOT NULL,
  `fax_texte` varchar(20) NOT NULL,
  `courrier_image` varchar(20) NOT NULL,
  `courrier_texte` varchar(20) NOT NULL,
  `largeur_bloc_eleve` float NOT NULL,
  `hauteur_bloc_eleve` float NOT NULL,
  `largeur_bloc_adresse` float NOT NULL,
  `hauteur_bloc_adresse` float NOT NULL,
  `largeur_bloc_datation` float NOT NULL,
  `hauteur_bloc_datation` float NOT NULL,
  `taille_texte_classe` float NOT NULL,
  `type_texte_classe` varchar(1) NOT NULL,
  `taille_texte_annee` float NOT NULL,
  `type_texte_annee` varchar(1) NOT NULL,
  `taille_texte_periode` float NOT NULL,
  `type_texte_periode` varchar(1) NOT NULL,
  `taille_texte_categorie_cote` float NOT NULL,
  `taille_texte_categorie` float NOT NULL,
  `type_texte_date_datation` varchar(1) NOT NULL,
  `cadre_adresse` tinyint(4) NOT NULL,
  `centrage_logo` tinyint(4) NOT NULL DEFAULT '0',
  `Y_centre_logo` float NOT NULL DEFAULT '18',
  `ajout_cadre_blanc_photo` tinyint(4) NOT NULL DEFAULT '0',
  `affiche_moyenne_mini_general` tinyint(4) NOT NULL DEFAULT '1',
  `affiche_moyenne_maxi_general` tinyint(4) NOT NULL DEFAULT '1',
  `affiche_date_edition` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_model_bulletin`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `model_bulletin`
--

INSERT INTO `model_bulletin` (`id_model_bulletin`, `nom_model_bulletin`, `active_bloc_datation`, `active_bloc_eleve`, `active_bloc_adresse_parent`, `active_bloc_absence`, `active_bloc_note_appreciation`, `active_bloc_avis_conseil`, `active_bloc_chef`, `active_photo`, `active_coef_moyenne`, `active_nombre_note`, `active_nombre_note_case`, `active_moyenne`, `active_moyenne_eleve`, `active_moyenne_classe`, `active_moyenne_min`, `active_moyenne_max`, `active_regroupement_cote`, `active_entete_regroupement`, `active_moyenne_regroupement`, `active_rang`, `active_graphique_niveau`, `active_appreciation`, `affiche_doublement`, `affiche_date_naissance`, `affiche_dp`, `affiche_nom_court`, `affiche_effectif_classe`, `affiche_numero_impression`, `caractere_utilse`, `X_parent`, `Y_parent`, `X_eleve`, `Y_eleve`, `cadre_eleve`, `X_datation_bul`, `Y_datation_bul`, `cadre_datation_bul`, `hauteur_info_categorie`, `X_note_app`, `Y_note_app`, `longeur_note_app`, `hauteur_note_app`, `largeur_coef_moyenne`, `largeur_nombre_note`, `largeur_d_une_moyenne`, `largeur_niveau`, `largeur_rang`, `X_absence`, `Y_absence`, `hauteur_entete_moyenne_general`, `X_avis_cons`, `Y_avis_cons`, `longeur_avis_cons`, `hauteur_avis_cons`, `cadre_avis_cons`, `X_sign_chef`, `Y_sign_chef`, `longeur_sign_chef`, `hauteur_sign_chef`, `cadre_sign_chef`, `affiche_filigrame`, `texte_filigrame`, `affiche_logo_etab`, `entente_mel`, `entente_tel`, `entente_fax`, `L_max_logo`, `H_max_logo`, `toute_moyenne_meme_col`, `active_reperage_eleve`, `couleur_reperage_eleve1`, `couleur_reperage_eleve2`, `couleur_reperage_eleve3`, `couleur_categorie_entete`, `couleur_categorie_entete1`, `couleur_categorie_entete2`, `couleur_categorie_entete3`, `couleur_categorie_cote`, `couleur_categorie_cote1`, `couleur_categorie_cote2`, `couleur_categorie_cote3`, `couleur_moy_general`, `couleur_moy_general1`, `couleur_moy_general2`, `couleur_moy_general3`, `titre_entete_matiere`, `titre_entete_coef`, `titre_entete_nbnote`, `titre_entete_rang`, `titre_entete_appreciation`, `active_coef_sousmoyene`, `arrondie_choix`, `nb_chiffre_virgule`, `chiffre_avec_zero`, `autorise_sous_matiere`, `affichage_haut_responsable`, `entete_model_bulletin`, `ordre_entete_model_bulletin`, `affiche_etab_origine`, `imprime_pour`, `largeur_matiere`, `nom_etab_gras`, `taille_texte_date_edition`, `taille_texte_matiere`, `active_moyenne_general`, `titre_bloc_avis_conseil`, `taille_titre_bloc_avis_conseil`, `taille_profprincipal_bloc_avis_conseil`, `affiche_fonction_chef`, `taille_texte_fonction_chef`, `taille_texte_identitee_chef`, `tel_image`, `tel_texte`, `fax_image`, `fax_texte`, `courrier_image`, `courrier_texte`, `largeur_bloc_eleve`, `hauteur_bloc_eleve`, `largeur_bloc_adresse`, `hauteur_bloc_adresse`, `largeur_bloc_datation`, `hauteur_bloc_datation`, `taille_texte_classe`, `type_texte_classe`, `taille_texte_annee`, `type_texte_annee`, `taille_texte_periode`, `type_texte_periode`, `taille_texte_categorie_cote`, `taille_texte_categorie`, `type_texte_date_datation`, `cadre_adresse`, `centrage_logo`, `Y_centre_logo`, `ajout_cadre_blanc_photo`, `affiche_moyenne_mini_general`, `affiche_moyenne_maxi_general`, `affiche_date_edition`) VALUES
(1, 'Standard', '1', 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 18, 5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 0, 1, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 0, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 0, 0.01, 2, 0, 1, 1, 1, 1, 0, 0, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1),
(2, 'Standard avec photo', '1', 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 18, 5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 0, 1, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 0, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 0, 0, 2, 0, 1, 1, 1, 1, 0, 0, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1),
(3, 'Affiche tout', '1', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 16.5, 6.5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 1, 1, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 1, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 1, 0.01, 2, 0, 1, 1, 2, 1, 1, 1, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `notanet`
--

CREATE TABLE IF NOT EXISTS `notanet` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `ine` text NOT NULL,
  `id_mat` int(4) NOT NULL,
  `notanet_mat` varchar(255) NOT NULL,
  `matiere` varchar(50) NOT NULL,
  `note` varchar(4) NOT NULL DEFAULT '',
  `note_notanet` varchar(4) NOT NULL,
  `id_classe` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_app`
--

CREATE TABLE IF NOT EXISTS `notanet_app` (
  `login` varchar(50) NOT NULL,
  `id_mat` int(4) NOT NULL,
  `matiere` varchar(50) NOT NULL,
  `appreciation` text NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `notanet_app`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_avis`
--

CREATE TABLE IF NOT EXISTS `notanet_avis` (
  `login` varchar(50) NOT NULL,
  `favorable` enum('O','N') NOT NULL,
  `avis` text NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet_avis`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_corresp`
--

CREATE TABLE IF NOT EXISTS `notanet_corresp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_brevet` tinyint(4) NOT NULL,
  `id_mat` int(4) NOT NULL,
  `notanet_mat` varchar(255) NOT NULL DEFAULT '',
  `matiere` varchar(50) NOT NULL DEFAULT '',
  `statut` enum('imposee','optionnelle','non dispensee dans l etablissement') NOT NULL DEFAULT 'imposee',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `notanet_corresp`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_ele_type`
--

CREATE TABLE IF NOT EXISTS `notanet_ele_type` (
  `login` varchar(50) NOT NULL,
  `type_brevet` tinyint(4) NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet_ele_type`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_lvr`
--

CREATE TABLE IF NOT EXISTS `notanet_lvr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `notanet_lvr`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_lvr_ele`
--

CREATE TABLE IF NOT EXISTS `notanet_lvr_ele` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `id_lvr` int(11) NOT NULL,
  `note` enum('','VA','NV') NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `notanet_lvr_ele`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_socles`
--

CREATE TABLE IF NOT EXISTS `notanet_socles` (
  `login` varchar(50) NOT NULL,
  `b2i` enum('MS','ME','MN','AB') NOT NULL,
  `a2` enum('MS','ME','AB') NOT NULL,
  `lv` varchar(50) NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet_socles`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_socle_commun`
--

CREATE TABLE IF NOT EXISTS `notanet_socle_commun` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `champ` varchar(10) NOT NULL,
  `valeur` enum('MS','ME','MN','AB','') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `notanet_socle_commun`
--


-- --------------------------------------------------------

--
-- Structure de la table `notanet_verrou`
--

CREATE TABLE IF NOT EXISTS `notanet_verrou` (
  `id_classe` smallint(6) NOT NULL,
  `type_brevet` tinyint(4) NOT NULL,
  `verrouillage` char(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notanet_verrou`
--


-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `matiere` varchar(20) NOT NULL,
  `login` varchar(20) NOT NULL,
  `note` int(11) NOT NULL,
  `trimestre` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notes`
--


-- --------------------------------------------------------

--
-- Structure de la table `pays`
--

CREATE TABLE IF NOT EXISTS `pays` (
  `code_pays` varchar(50) NOT NULL,
  `nom_pays` varchar(255) NOT NULL,
  PRIMARY KEY (`code_pays`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `pays`
--


-- --------------------------------------------------------

--
-- Structure de la table `periodes`
--

CREATE TABLE IF NOT EXISTS `periodes` (
  `nom_periode` varchar(50) NOT NULL DEFAULT '',
  `num_periode` int(11) NOT NULL DEFAULT '0',
  `verouiller` char(1) NOT NULL DEFAULT '',
  `id_classe` int(11) NOT NULL DEFAULT '0',
  `date_verrouillage` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_fin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`num_periode`,`id_classe`),
  KEY `id_classe` (`id_classe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `periodes`
--

INSERT INTO `periodes` (`nom_periode`, `num_periode`, `verouiller`, `id_classe`, `date_verrouillage`, `date_fin`) VALUES
('Term 2', 2, 'N', 1, '2012-02-27 10:24:03', '0000-00-00 00:00:00'),
('Term 1', 1, 'N', 2, '2012-03-01 10:34:02', '0000-00-00 00:00:00'),
('Term 1', 1, 'N', 1, '2012-02-27 10:24:03', '0000-00-00 00:00:00'),
('Term 3', 3, 'N', 2, '2012-03-01 10:34:02', '0000-00-00 00:00:00'),
('Term 2', 2, 'N', 2, '2012-03-01 10:34:02', '0000-00-00 00:00:00'),
('Term 3', 3, 'N', 1, '2012-02-27 10:24:03', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `plugins`
--

CREATE TABLE IF NOT EXISTS `plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `repertoire` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `ouvert` char(1) DEFAULT 'n',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `plugins`
--


-- --------------------------------------------------------

--
-- Structure de la table `plugins_autorisations`
--

CREATE TABLE IF NOT EXISTS `plugins_autorisations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_id` int(11) NOT NULL,
  `fichier` varchar(100) NOT NULL,
  `user_statut` varchar(50) NOT NULL,
  `auth` char(1) DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `plugins_autorisations`
--


-- --------------------------------------------------------

--
-- Structure de la table `plugins_menus`
--

CREATE TABLE IF NOT EXISTS `plugins_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_id` int(11) NOT NULL,
  `user_statut` varchar(50) NOT NULL,
  `titre_item` varchar(255) NOT NULL,
  `lien_item` varchar(255) NOT NULL,
  `description_item` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `plugins_menus`
--


-- --------------------------------------------------------

--
-- Structure de la table `preferences`
--

CREATE TABLE IF NOT EXISTS `preferences` (
  `login` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  KEY `login_name` (`login`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `preferences`
--

INSERT INTO `preferences` (`login`, `name`, `value`) VALUES
('ADMIN', 'footer_sound', 'KDE_Beep_Pop.wav');

-- --------------------------------------------------------

--
-- Structure de la table `ref_wiki`
--

CREATE TABLE IF NOT EXISTS `ref_wiki` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `ref_wiki`
--

INSERT INTO `ref_wiki` (`id`, `ref`, `url`) VALUES
(1, 'enseignement_invisible', 'http://www.sylogix.org/wiki/gepi/Enseignement_invisible');

-- --------------------------------------------------------

--
-- Structure de la table `responsables`
--

CREATE TABLE IF NOT EXISTS `responsables` (
  `ereno` varchar(10) NOT NULL DEFAULT '',
  `nom1` varchar(50) NOT NULL DEFAULT '',
  `prenom1` varchar(50) NOT NULL DEFAULT '',
  `adr1` varchar(100) NOT NULL DEFAULT '',
  `adr1_comp` varchar(100) NOT NULL DEFAULT '',
  `commune1` varchar(50) NOT NULL DEFAULT '',
  `cp1` varchar(6) NOT NULL DEFAULT '',
  `nom2` varchar(50) NOT NULL DEFAULT '',
  `prenom2` varchar(50) NOT NULL DEFAULT '',
  `adr2` varchar(100) NOT NULL DEFAULT '',
  `adr2_comp` varchar(100) NOT NULL DEFAULT '',
  `commune2` varchar(50) NOT NULL DEFAULT '',
  `cp2` varchar(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`ereno`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `responsables`
--


-- --------------------------------------------------------

--
-- Structure de la table `responsables2`
--

CREATE TABLE IF NOT EXISTS `responsables2` (
  `ele_id` varchar(10) NOT NULL,
  `pers_id` varchar(10) NOT NULL,
  `resp_legal` varchar(1) NOT NULL,
  `pers_contact` varchar(1) NOT NULL,
  KEY `pers_id` (`pers_id`),
  KEY `ele_id` (`ele_id`),
  KEY `resp_legal` (`resp_legal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `responsables2`
--

INSERT INTO `responsables2` (`ele_id`, `pers_id`, `resp_legal`, `pers_contact`) VALUES
('e000000002', 'p000000008', '1', ''),
('e000000001', 'p000000001', '1', ''),
('e000000078', 'p000000002', '1', ''),
('e000000088', 'p000000094', '1', ''),
('e000000048', 'p000000050', '1', ''),
('e000000089', 'p000000095', '1', ''),
('e000000002', 'p000000009', '2', ''),
('e000000003', 'p000000010', '1', ''),
('e000000003', 'p000000011', '2', ''),
('e000000004', 'p000000012', '1', ''),
('e000000005', 'p000000013', '1', ''),
('e000000005', 'p000000014', '2', ''),
('e000000006', 'p000000015', '1', ''),
('e000000006', 'p000000016', '2', ''),
('e000000007', 'p000000017', '1', ''),
('e000000007', 'p000000019', '2', ''),
('e000000008', 'p000000020', '1', ''),
('e000000008', 'p000000021', '2', ''),
('e000000009', 'p000000022', '1', ''),
('e000000009', 'p000000023', '2', ''),
('e000000010', 'p000000024', '1', ''),
('e000000011', 'p000000025', '1', ''),
('e000000087', 'p000000093', '2', ''),
('e000000013', 'p000000027', '1', ''),
('e000000013', 'p000000028', '2', ''),
('e000000014', 'p000000029', '1', ''),
('e000000014', 'p000000030', '2', ''),
('e000000015', 'p000000031', '1', ''),
('e000000015', 'p000000032', '2', ''),
('e000000017', 'p000000034', '2', ''),
('e000000016', 'p000000033', '1', ''),
('e000000016', 'p000000035', '2', ''),
('e000000017', 'p000000036', '1', ''),
('e000000043', 'p000000037', '1', ''),
('e000000020', 'p000000038', '1', ''),
('e000000043', 'p000000039', '2', ''),
('e000000020', 'p000000040', '2', ''),
('e000000044', 'p000000041', '1', ''),
('e000000021', 'p000000042', '1', ''),
('e000000044', 'p000000043', '2', ''),
('e000000022', 'p000000045', '1', ''),
('e000000045', 'p000000044', '1', ''),
('e000000045', 'p000000046', '2', ''),
('e000000046', 'p000000047', '1', ''),
('e000000024', 'p000000048', '1', ''),
('e000000046', 'p000000049', '2', ''),
('e000000048', 'p000000050', '2', ''),
('e000000024', 'p000000051', '2', ''),
('e000000049', 'p000000052', '1', ''),
('e000000026', 'p000000053', '1', ''),
('e000000049', 'p000000054', '2', ''),
('e000000026', 'p000000055', '2', ''),
('e000000027', 'p000000058', '1', ''),
('e000000027', 'p000000059', '2', ''),
('e000000029', 'p000000060', '1', ''),
('e000000029', 'p000000061', '2', ''),
('e000000030', 'p000000062', '1', ''),
('e000000030', 'p000000063', '2', ''),
('e000000031', 'p000000064', '1', ''),
('e000000051', 'p000000057', '1', ''),
('e000000031', 'p000000065', '2', ''),
('e000000052', 'p000000066', '1', ''),
('e000000032', 'p000000067', '1', ''),
('e000000052', 'p000000068', '2', ''),
('e000000032', 'p000000069', '2', ''),
('e000000033', 'p000000070', '1', ''),
('e000000053', 'p000000071', '1', ''),
('e000000033', 'p000000072', '2', ''),
('e000000053', 'p000000073', '2', ''),
('e000000054', 'p000000074', '1', ''),
('e000000034', 'p000000075', '1', ''),
('e000000054', 'p000000076', '2', ''),
('e000000055', 'p000000077', '1', ''),
('e000000034', 'p000000078', '2', ''),
('e000000056', 'p000000079', '1', ''),
('e000000056', 'p000000080', '2', ''),
('e000000058', 'p000000081', '1', ''),
('e000000058', 'p000000082', '2', ''),
('e000000035', 'p000000083', '1', ''),
('e000000059', 'p000000084', '1', ''),
('e000000061', 'p000000085', '1', ''),
('e000000037', 'p000000086', '1', ''),
('e000000061', 'p000000087', '2', ''),
('e000000062', 'p000000088', '1', ''),
('e000000062', 'p000000089', '2', ''),
('e000000086', 'p000000090', '1', ''),
('e000000086', 'p000000091', '2', ''),
('e000000087', 'p000000092', '1', ''),
('e000000089', 'p000000096', '2', ''),
('e000000060', 'p000000097', '1', '');

-- --------------------------------------------------------

--
-- Structure de la table `resp_adr`
--

CREATE TABLE IF NOT EXISTS `resp_adr` (
  `adr_id` varchar(10) NOT NULL,
  `adr1` varchar(100) NOT NULL,
  `adr2` varchar(100) NOT NULL,
  `adr3` varchar(100) NOT NULL,
  `adr4` varchar(100) NOT NULL,
  `cp` varchar(6) NOT NULL,
  `pays` varchar(50) NOT NULL,
  `commune` varchar(50) NOT NULL,
  PRIMARY KEY (`adr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `resp_adr`
--

INSERT INTO `resp_adr` (`adr_id`, `adr1`, `adr2`, `adr3`, `adr4`, `cp`, `pays`, `commune`) VALUES
('a000000001', 'Douala', '', '', '', '2912', 'Cameroun', 'Littoral'),
('a000000002', 'Yaoundé', '', '', '', '2912', 'Cameroun', 'Douala'),
('a000000094', 'TEST1', '', '', '', '237', 'Cameroun', 'Bamenda'),
('a000000004', 'Bonaberi', '', '', '', '10212', 'Cameroun', 'Douala'),
('a000000009', 'Test', '', '', '', '2912', 'Cameroun', 'Yaoundé'),
('a000000008', 'Buea', '', '', '', '2912', 'Cameroun', 'SW'),
('a000000095', 'TEST1', '', '', '', '237', 'Cameroun', 'Bamenda'),
('a000000010', 'Test', '', '', '', '12000', 'Cameroun', 'SW'),
('a000000011', 'Test', '', '', '', '10212', 'Cameroun', 'SW'),
('a000000012', 'Test', '', '', '', '7896', 'Cameroun', 'SW'),
('a000000013', 'Test', '', '', '', '2912', 'Cameroun', 'SW'),
('a000000014', 'Test', '', '', '', '2912', 'Cameroun', 'SW'),
('a000000015', 'Test', '', '', '', '2912', 'Cameroun', 'SW'),
('a000000016', 'Test', '', '', '', '12000', 'Cameroun', 'SW'),
('a000000017', 'Yaoundé', '', '', '', '2912', 'Cameroun', 'SW'),
('a000000035', 'TEST1', '', '', '', '237', 'Cameroun', 'Buea'),
('a000000019', 'Buea', '', '', '', '12000', 'Cameroun', 'Yaoundé'),
('a000000020', 'Yaoundé', '', '', '', '2912', 'Cameroun', 'Yaoundé'),
('a000000021', 'Yaoundé', '', '', '', '2912', 'Cameroun', 'SW'),
('a000000022', 'Test', '', '', '', '10212', 'Cameroun', 'Douala'),
('a000000023', 'Test', '', '', '', '2912', 'Cameroun', 'Yaoundé'),
('a000000024', 'Yaoundé', '', '', '', '2912', 'Cameroun', 'SW'),
('a000000025', 'Test', '', '', '', '2912', 'Cameroun', 'Yaoundé'),
('a000000026', 'Yaoundé', '', '', '', '12000', 'Cameroun', 'Yaoundé'),
('a000000027', 'Test', '', '', '', '2912', 'Cameroun', 'Yaoundé'),
('a000000028', 'Yaoundé', '', '', '', '12000', 'Cameroun', 'Douala'),
('a000000029', 'Buea', '', '', '', '2912', 'Cameroun', 'Douala'),
('a000000030', 'Yaoundé', '', '', '', '7896', 'Cameroun', 'Douala'),
('a000000031', 'Yaoundé', '', '', '', '12000', 'Cameroun', 'Douala'),
('a000000032', 'Test', '', '', '', '2912', 'Cameroun', 'Yaoundé'),
('a000000033', 'TEST1', '', '', '', '2912', 'Cameroun', 'Buea'),
('a000000034', 'Test', '', '', '', '7896', 'Cameroun', 'Douala'),
('a000000036', 'TEST1', '', '', '', '237', 'Cameroun', 'Cameroun'),
('a000000037', 'Yaoundé', '', '', '', '2912', 'Cameroun', 'Littoral'),
('a000000038', 'TEST1', '', '', '', '237', 'Cameroun', 'Douala'),
('a000000039', 'Test', '', '', '', '7896', 'Cameroun', 'SW'),
('a000000040', 'TEST1', '', '', '', '237', 'Cameroun', 'Douala'),
('a000000041', 'Yaoundé', '', '', '', '7896', 'Cameroun', 'Yaoundé'),
('a000000042', 'TEST1', '', '', '', '237', 'Cameroun', 'Buea'),
('a000000043', 'Buea', '', '', '', '7896', 'Cameroun', 'Douala'),
('a000000044', 'Yaoundé', '', '', '', '7896', 'Cameroun', 'Littoral'),
('a000000045', 'TEST1', '', '', '', '237', 'Cameroun', 'Buea'),
('a000000046', 'Bonaberi', '', '', '', '7896', 'Cameroun', 'Douala'),
('a000000047', 'Yaoundé', '', '', '', '12000', 'Cameroun', 'Littoral'),
('a000000048', 'TEST1', '', '', '', '237', 'Cameroun', 'Douala'),
('a000000049', 'Test', '', '', '', '7896', 'Cameroun', 'Douala'),
('a000000050', 'Yaoundé', '', '', '', '2912', 'Cameroun', 'Yaoundé'),
('a000000051', 'TEST1', '', '', '', '237', 'Cameroun', 'Douala'),
('a000000052', 'Yaoundé', '', '', '', '2912', 'Cameroun', 'Yaoundé'),
('a000000053', '77692042', '', '', '', '237', 'Cameroun', 'Douala'),
('a000000054', 'Douala', '', '', '', '12000', 'Cameroun', 'Littoral'),
('a000000055', 'TEST1', '', '', '', '237', 'Cameroun', 'Yaounde'),
('a000000056', 'Buea', '', '', '', '2912', 'Cameroun', 'Douala'),
('a000000057', 'Yaoundé', '', '', '', '7896', 'Cameroun', 'SW'),
('a000000058', 'TEST1', '', '', '', '237', 'Cameroun', 'Buea'),
('a000000059', 'TEST1', '', '', '', '237', 'Cameroun', 'Yaounde'),
('a000000060', 'TEST1', '', '', '', '237', 'Cameroun', 'Limbe'),
('a000000061', 'TEST1', '', '', '', '237', 'Cameroun', 'Limbe'),
('a000000062', 'TEST1', '', '', '', '237', 'Cameroun', 'Limbe'),
('a000000063', 'TEST1', '', '', '', '237', 'Cameroun', 'Bamenda'),
('a000000064', 'TEST1', '', '', '', '237', 'Cameroun', 'Bamenda'),
('a000000065', 'TEST1', '', '', '', '237', 'Cameroun', 'Bamenda'),
('a000000066', 'Buea', '', '', '', '12000', 'Cameroun', 'Yaoundé'),
('a000000067', 'TEST1', '', '', '', '237', 'Cameroun', 'Yaounde'),
('a000000068', 'Bonaberi', '', '', '', '2912', 'Cameroun', 'Littoral'),
('a000000069', 'TEST1', '', '', '', '237', 'Cameroun', 'Bamenda'),
('a000000070', 'TEST1', '', '', '', '237', 'Cameroun', 'Buea'),
('a000000071', 'Bonaberi', '', '', '', '2912', 'Cameroun', 'Littoral'),
('a000000072', 'TEST1', '', '', '', '237', 'Cameroun', 'Buea'),
('a000000073', 'Yaoundé', '', '', '', '7896', 'Cameroun', 'SW'),
('a000000074', 'Test', '', '', '', '12000', 'Cameroun', 'SW'),
('a000000075', 'TEST1', '', '', '', '237', 'Cameroun', 'Yaounde'),
('a000000076', 'Yaoundé', '', '', '', '7896', 'Cameroun', 'Yaoundé'),
('a000000077', 'Yaoundé', '', '', '', '12000', 'Cameroun', 'Douala'),
('a000000078', 'TEST1', '', '', '', '237', 'Cameroun', 'Yaounde'),
('a000000079', 'Yaoundé', '', '', '', '12000', 'Cameroun', 'Douala'),
('a000000080', 'Test', '', '', '', '7896', 'Cameroun', 'SW'),
('a000000081', 'Test', '', '', '', '7896', 'Cameroun', 'Douala'),
('a000000082', 'Test', '', '', '', '2912', 'Cameroun', 'SW'),
('a000000083', 'TEST1', '', '', '', '237', 'Cameroun', 'Yaounde'),
('a000000084', 'Test', '', '', '', '7896', 'Cameroun', 'SW'),
('a000000085', 'Test', '', '', '', '7896', 'Cameroun', 'Douala'),
('a000000086', 'TEST1', '', '', '', '237', 'Cameroun', 'Bamenda'),
('a000000087', 'Yaoundé', '', '', '', '12000', 'Cameroun', 'SW'),
('a000000088', 'Test', '', '', '', '12000', 'Cameroun', 'Douala'),
('a000000089', 'Yaoundé', '', '', '', '7896', 'Cameroun', 'Yaoundé'),
('a000000090', 'TEST1', '', '', '', '237', 'Cameroun', 'Buea'),
('a000000091', 'TEST1', '', '', '', '237', 'Cameroun', 'Bamenda'),
('a000000092', 'TEST1', '', '', '', '237', 'Cameroun', 'Yaounde'),
('a000000093', 'TEST1', '', '', '', '237', 'Cameroun', 'Yaounde'),
('a000000096', 'TEST1', '', '', '', '237', 'Cameroun', 'Bamenda'),
('a000000097', 'Yaoundé', '', '', '', '7896', 'Cameroun', 'Douala');

-- --------------------------------------------------------

--
-- Structure de la table `resp_pers`
--

CREATE TABLE IF NOT EXISTS `resp_pers` (
  `pers_id` varchar(10) NOT NULL,
  `login` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `civilite` varchar(5) NOT NULL,
  `tel_pers` varchar(255) NOT NULL,
  `tel_port` varchar(255) NOT NULL,
  `tel_prof` varchar(255) NOT NULL,
  `mel` varchar(100) NOT NULL,
  `adr_id` varchar(10) NOT NULL,
  PRIMARY KEY (`pers_id`),
  KEY `login` (`login`),
  KEY `adr_id` (`adr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `resp_pers`
--

INSERT INTO `resp_pers` (`pers_id`, `login`, `nom`, `prenom`, `civilite`, `tel_pers`, `tel_port`, `tel_prof`, `mel`, `adr_id`) VALUES
('p000000001', 'Pere', 'Parent', 'ABEDNEGO NGAHA NJIKE', '', '', '76689201', '', '', 'a000000001'),
('p000000002', 'Makengne', 'Makengne', 'Nikème', 'Mme', '96711073', '96711073', '', 'nik@yahoo.fr', 'a000000002'),
('p000000004', 'Assontia', 'Assontia', 'Thomas', 'M.', '99542932', '99124249', '', 'assontia@gmail.com', 'a000000004'),
('p000000094', '', 'Parent 1', 'TITA SCHAMSU DEEN', '', '', '776100113', '', '', 'a000000094'),
('p000000008', '', 'Parent 1', 'ACHAH LAURENCE NTEMBE', '', '', '70544539', '', '', 'a000000008'),
('p000000009', '', 'Parent 2', 'ACHAH LAURENCE NTEMBE', '', '', '74698145', '', '', 'a000000009'),
('p000000095', '', 'Parent 1', 'UKATANG ABADUM GABRIEL', '', '', '75229088', '', '', 'a000000095'),
('p000000010', '', 'Parent 1', 'AJONG ETIENDEM FUANYA', '', '', '77608099', '', '', 'a000000010'),
('p000000011', '', 'Parent 2', 'AJONG ETIENDEM FUANYA', '', '', '77723871', '', '', 'a000000011'),
('p000000012', '', 'Parent', 'ANAKA SEDRICK AKU', '', '', '77251418', '', '', 'a000000012'),
('p000000013', '', 'Parent 1', 'ANICET DUBOIS NLEND', '', '', '99946208', '', '', 'a000000013'),
('p000000014', '', 'Parent 2', 'ANICET DUBOIS NLEND', '', '', '94033887', '', '', 'a000000014'),
('p000000015', '', 'Parent 1', 'ANJORIN MANYAKA NGALE KEKA', '', '', '77605420', '', '', 'a000000015'),
('p000000016', '', 'Parent 2', 'ANJORIN MANYAKA NGALE KEKA', '', '', '77556154', '', '', 'a000000016'),
('p000000017', '', 'Parent 1', 'APAH MORAN TAMBIA', '', '', '75014317', '', '', 'a000000017'),
('p000000035', '', 'Parent 1', 'BOUNOUNG FRANCK', '', '', '75772985', '', '', 'a000000035'),
('p000000019', '', 'Parent 2', 'APAH MORAN TAMBIA', '', '', '70737481', '', '', 'a000000019'),
('p000000020', '', 'Parent 1', 'ATANGA NEELEIN SUH', '', '', '77719084', '', '', 'a000000020'),
('p000000021', '', 'Parent 2', 'ATANGA NEELEIN SUH', '', '', '77239737', '', '', 'a000000021'),
('p000000022', '', 'Parent 1', 'ATEM KIRAN ETACHEM', '', '', '70488866', '', '', 'a000000022'),
('p000000023', '', 'Parent 2', 'ATEM KIRAN ETACHEM', '', '', '75563746', '', '', 'a000000023'),
('p000000024', '', 'Parent', 'AYUK BENARD MANYOR', '', '', '77642730', '', '', 'a000000024'),
('p000000025', '', 'Parent', 'AYUK VICTOR ETENGENENG', '', '', '77642730', '', '', 'a000000025'),
('p000000026', '', 'Parent', 'BAIYE STEPHEN TABE Jr.', '', '', '99844794', '', '', 'a000000026'),
('p000000027', '', 'Parent 1', 'BENDE VALENTINE BENDE', '', '', '75933785', '', '', 'a000000027'),
('p000000028', '', 'Parent 2', 'BENDE VALENTINE BENDE', '', '', '96887631', '', '', 'a000000028'),
('p000000029', '', 'Parent 1', 'BESONG SAMUEL BESONG', '', '', '77235536', '', '', 'a000000029'),
('p000000030', '', 'Parent 2', 'BESONG SAMUEL BESONG', '', '', '77451175', '', '', 'a000000030'),
('p000000031', '', 'Parent 1', 'BESSONG-OJONG WILLINGTON', '', '', '75197501', '', '', 'a000000031'),
('p000000032', '', 'Parent 2', 'BESSONG-OJONG WILLINGTON', '', '', '74880664', '', '', 'a000000032'),
('p000000033', '', 'Parent 2', 'BOUNOUNG FRANCK', '', '', '94175545', '', '', 'a000000033'),
('p000000034', '', 'Parent', 'BRADLEY HONGE ESSEMBIEG', '', '', '77738702', '', '', 'a000000034'),
('p000000036', '', 'Parent 1', 'BRADLEY HONGE ESSEMBIEG', '', '', '77738702', '', '', 'a000000036'),
('p000000037', '', 'Parent 1', 'KOME NATHANIEL NKWELLE', '', '', '70367929', '', '', 'a000000037'),
('p000000038', '', 'Parent 1', 'CHIFU KITA ATEM J. MARK', '', '', '78699817', '', '', 'a000000038'),
('p000000039', '', 'Parent 2', 'KOME NATHANIEL NKWELLE', '', '', '75988503', '', '', 'a000000039'),
('p000000040', '', 'Parent 2', 'CHIFU KITA ATEM J. MARK', '', '', '74169835', '', '', 'a000000040'),
('p000000041', '', 'Parent 1', 'KOUNCHOU KOUNCHOU H.', '', '', '74363535', '', '', 'a000000041'),
('p000000042', '', 'Parent 1', 'DJOU ARNOLD KEVIN', '', '', '79849072', '', '', 'a000000042'),
('p000000043', '', 'Parent 2', 'KOUNCHOU KOUNCHOU H.', '', '', '99327377', '', '', 'a000000043'),
('p000000044', '', 'Parent 1', 'MBI KITZITO NCHAFFU', '', '', '77853534', '', '', 'a000000044'),
('p000000045', '', 'Parent 1', 'DOBGIMA NATHANIEL B.N', '', '', '75703613', '', '', 'a000000045'),
('p000000046', '', 'Parent 2', 'MBI KITZITO NCHAFFU', '', '', '75932765', '', '', 'a000000046'),
('p000000047', '', 'Parent 1', 'MBI NDIP ANAMOH Jr', '', '', '77972757', '', '', 'a000000047'),
('p000000048', '', 'Parent 1', 'EGBENCHUNG MASANGO', '', '', '77881031', '', '', 'a000000048'),
('p000000049', '', 'Parent 2', 'MBI NDIP ANAMOH Jr', '', '', '75190083', '', '', 'a000000049'),
('p000000050', '', 'Parent', 'MEKEME BEDJOKO MICHEL', '', '', '77697503', '', '', 'a000000050'),
('p000000051', '', 'Parent 2', 'EGBENCHUNG MASANGO', '', '', '77547394', '', '', 'a000000051'),
('p000000052', '', 'Parent 1', 'MENDI OTTIA ROGER EMILE', '', '', '77582116', '', '', 'a000000052'),
('p000000053', '', 'Parent 1', 'ELANDO á BENDEH ESEKE BRAXTON', '', '', '', '', '', 'a000000053'),
('p000000054', '', 'Parent 2', 'MENDI OTTIA ROGER EMILE', '', '', '79425303', '', '', 'a000000054'),
('p000000055', '', 'Parent 2', 'ELANDO á BENDEH ESEKE BRAXTON', '', '', '77771771', '', '', 'a000000055'),
('p000000056', '', 'Parent', 'METUGE JUNIOR ELINGESE', '', '', '79860423', '', 'psonemetuge@yahoo.com', 'a000000056'),
('p000000057', '', 'Parent', 'MIEGUIM MARC KEVIN', '', '', '79345111', '', '', 'a000000057'),
('p000000058', '', 'Parent 1', 'ELONGE ALLEN ESANGE', '', '96665859', '', '', '', 'a000000058'),
('p000000059', '', 'Parent 2', 'ELONGE ALLEN ESANGE', '', '', '78022146', '', '', 'a000000059'),
('p000000060', '', 'Parent 1', 'ENOWEBAI VALDES EBAIEGBEENOW', '', '', '76666093', '', '', 'a000000060'),
('p000000061', '', 'Parent 2', 'ENOWEBAI VALDES EBAIEGBEENOW', '', '', '77845004', '', '', 'a000000061'),
('p000000062', '', 'Parent 2', 'EPEY ELVIS EPEY', '', '', '99395643', '', '', 'a000000062'),
('p000000063', '', 'Parent 2', 'EPEY ELVIS EPEY', '', '', '99395643', '', '', 'a000000063'),
('p000000064', '', 'Parent 1', 'FOADJO LELE-YANN JORDAN', '', '', '99814670', '', '', 'a000000064'),
('p000000065', '', 'Parent 2', 'FOADJO LELE-YANN JORDAN', '', '', '99440751', '', '', 'a000000065'),
('p000000066', '', 'Parent 1', 'MOCTO SOP JAMEL SHARIF', '', '', '99958919', '', '', 'a000000066'),
('p000000067', '', 'Parent 1', 'FOKOU IVAN LOIC NWANBA', '', '', '77498448', '', '', 'a000000067'),
('p000000068', '', 'Parent 2', 'MOCTO SOP JAMEL SHARIF', '', '', '75656483', '', '', 'a000000068'),
('p000000069', '', 'Parent 2', 'FOKOU IVAN LOIC NWANBA', '', '', '74638320', '', '', 'a000000069'),
('p000000070', '', 'Parent 1', 'GABRIELGIFT VEKE TEMBUK T.', '', '', '75732181', '', '', 'a000000070'),
('p000000071', '', 'Parent 1', 'MOKUBE SAKWE EYALO', '', '', '79864760', '', '', 'a000000071'),
('p000000072', '', 'Parent 2', 'GABRIELGIFT VEKE TEMBUK T.', '', '', '74806619', '', '', 'a000000072'),
('p000000073', '', 'Parent 2', 'MOKUBE SAKWE EYALO', '', '', '77665947', '', '', 'a000000073'),
('p000000074', '', 'Parent 1', 'MOKWE CLUVETTE TAH', '', '', '75893777', '', '', 'a000000074'),
('p000000075', '', 'Parent 1', 'HANSON OBENANYANG ARREY', '', '', '77272790', '', '', 'a000000075'),
('p000000076', '', 'Parent 2', 'MOKWE CLUVETTE TAH', '', '', '77663799', '', '', 'a000000076'),
('p000000077', '', 'Parent', 'NEKONGOH NOEL MUKETE', '', '', '75496837', '', '', 'a000000077'),
('p000000078', '', 'Parent 2', 'HANSON OBENANYANG ARREY', '', '', '75083083', '', '', 'a000000078'),
('p000000079', '', 'Parent 1', 'NGALAME SIMON MESANGO', '', '', '77455211', '', '', 'a000000079'),
('p000000080', '', 'Parent 2', 'NGALAME SIMON MESANGO', '', '', '74612042', '', '', 'a000000080'),
('p000000081', '', 'Parent 1', 'NGANGMI MILLENIAN NGU', '', '', '77666407', '', '', 'a000000081'),
('p000000082', '', 'Parent 2', 'NGANGMI MILLENIAN NGU', '', '', '99875530', '', '', 'a000000082'),
('p000000083', '', 'Parent 1', 'HARRISON LYTOMBE MBWAYE', '', '', '75371570', '', '', 'a000000083'),
('p000000084', '', 'Parent', 'NGOE FABIAN SONESTONE', '', '', '74710664', '', '', 'a000000084'),
('p000000085', '', 'Parent 1', 'NGOUNOU EMMANUEL ESEYE Jr.', '', '', '77666550', '', '', 'a000000085'),
('p000000086', '', 'Parent 1', 'IMMANUEL-KENKAID TABOT', '', '', '77815235', '', '', 'a000000086'),
('p000000087', '', 'Parent 2', 'NGOUNOU EMMANUEL ESEYE Jr.', '', '', '77399529', '', '', 'a000000087'),
('p000000088', '', 'Parent 1', 'NGULEFAC KLUIVERT', '', '', '77682333', '', '', 'a000000088'),
('p000000089', '', 'Parent 2', 'NGULEFAC KLUIVERT', '', '', '77584263', '', '', 'a000000089'),
('p000000090', '', 'Parent 1', 'TCHANGOU DILANE MAXIME', '', '', '77296119', '', '', 'a000000090'),
('p000000091', '', 'Parent 2', 'TCHANGOU DILANE MAXIME', '', '', '99692649', '', '', 'a000000091'),
('p000000092', '', 'Parent 1', 'TEGHOUE NOUANENGUE JUDE', '', '', '77870312', '', '', 'a000000092'),
('p000000093', '', 'Parent 2', 'TEGHOUE NOUANENGUE JUDE', '', '', '77769391', '', '', 'a000000093'),
('p000000096', '', 'Parent 2', 'UKATANG ABADUM GABRIEL', '', '', '75016847', '', '', 'a000000096'),
('p000000097', 'Assontia2', 'Assontia', 'Patient', 'M.', '', '99124249', '', '', 'a000000097');

-- --------------------------------------------------------

--
-- Structure de la table `salle_cours`
--

CREATE TABLE IF NOT EXISTS `salle_cours` (
  `id_salle` int(3) NOT NULL AUTO_INCREMENT,
  `numero_salle` varchar(10) NOT NULL,
  `nom_salle` varchar(50) NOT NULL,
  PRIMARY KEY (`id_salle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `salle_cours`
--


-- --------------------------------------------------------

--
-- Structure de la table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `NAME` varchar(255) NOT NULL DEFAULT '',
  `VALUE` text NOT NULL,
  PRIMARY KEY (`NAME`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `setting`
--

INSERT INTO `setting` (`NAME`, `VALUE`) VALUES
('version', 'trunk'),
('versionRc', ''),
('versionBeta', ''),
('sessionMaxLength', '30'),
('Impression', '<center><p class = "grand">Management of the Students By Internet</p></center>\r\n<br />\r\n<p class = "grand">What is GEPI ?</p>\r\n\r\n<p>In order to study the methods of computerization of the report cards : notes and appreciations via Internet, an experimentation (baptized Management of the Students By Internet)was installed. This experimentation relates to the following classes : \r\n<br />* ....\r\n<br />* ....\r\n<br />\r\n<br />\r\nThis concerns you because you are a professor teaching in one or the other of these classes.\r\n<br />\r\n<br />\r\nFrom the reception of this document, you will be able to fill the computerized bulletins :\r\n<span class = "norme">\r\n<UL><li>maybe with the college starting from any station connected to Internet,\r\n<li>maybe on to your house if you have a Internet connection .\r\n</ul>\r\n</span>\r\n<p class = "grand">How access to the module of typing (notes and appreciations) :</p>\r\n<span class = "norme">\r\n<UL>\r\n    <LI>Connect itself to Internet\r\n  <LI>Launch a navigator (FireFox preferably, Opera, Internet Explorer, ...)\r\n    <LI>Connect itself to the site : https://adresse_du_site/gepi\r\n    <LI>After a few moments a page appears inviting you to enter a name of identifier and a password (this information appears in top of this page).\r\n    <br />CAUTION : your password is strictly confidential.\r\n    <br />\r\n    <br />Once this information provided, click on the button "Ok".\r\n    <LI> After a few moments a home page appears.<br />\r\nThe first time, Gepi requires of you to change your password.\r\nChoose one which is easy to retain, but noncommonplace (avoid any date\r\nof birth, name of familiar animal, first name, etc.), and containing\r\nletter(s), quantify(s), and character(s) nonalphanumeric(s).<br />\r\nNext times, you will arrive directly at the general menu of\r\n the application. For taking note of the possibilities of\r\nthe application, do not hesitate to leave all the links available !\r\n</ul></span>\r\n<p class = "grand">Notice :</p>\r\n<p>GEPI is envisaged so that each professor cannot modify the notes or appreciations only in the rubrics which relate to it and only for its students.\r\n<br />\r\nI remain at your disposal for any further information.\r\n    <br />\r\n    The assistant headmaster\r\n</p>'),
('gepiYear', '2011/2012'),
('gepiSchoolName', 'St Joseph''s College Sasse'),
('gepiSchoolAdress1', 'Adresse'),
('gepiSchoolAdress2', 'Boîte postale'),
('gepiSchoolZipCode', '237'),
('gepiSchoolCity', 'Buea'),
('gepiAdminAdress', 'martialdenu@yahoo.fr'),
('titlesize', '14'),
('textsize', '8'),
('cellpadding', '3'),
('cellspacing', '1'),
('largeurtableau', '800'),
('col_matiere_largeur', '150'),
('begin_bookings', '1157058000'),
('end_bookings', '1188594000'),
('max_size', '307200'),
('total_max_size', '5242880'),
('col_note_largeur', '30'),
('active_cahiers_texte', 'y'),
('active_carnets_notes', 'y'),
('logo_etab', 'logo.png'),
('longmin_pwd', '5'),
('duree_conservation_logs', '365'),
('GepiRubConseilProf', 'yes'),
('GepiRubConseilScol', 'yes'),
('bull_ecart_entete', '0'),
('gepi_prof_suivi', 'professeur principal'),
('GepiProfImprBul', 'no'),
('GepiProfImprBulSettings', 'no'),
('GepiScolImprBulSettings', 'yes'),
('GepiAdminImprBulSettings', 'yes'),
('GepiAccesReleveScol', 'yes'),
('GepiAccesReleveCpe', 'yes'),
('GepiAccesReleveProf', 'no'),
('GepiAccesReleveProfTousEleves', 'no'),
('GepiAccesReleveProfToutesClasses', 'no'),
('GepiAccesReleveProfP', 'yes'),
('page_garde_imprime', 'no'),
('page_garde_texte', 'Madam, Mister<br/><br/>Please find here the report card of your child. We remind to you that the day <span style="font-weight: bold;">Open doors</span> College will take place Saturday 20 May between 10 h and 17 h.<br/><br/>Please accept, Madam, Mister, the expression of my best feelings.<br/><br/><div style="text-align: right;">The headmaster</div>'),
('page_garde_padding_top', '4'),
('page_garde_padding_left', '11'),
('page_garde_padding_text', '6'),
('addressblock_padding_top', '400'),
('addressblock_padding_right', '200'),
('addressblock_padding_text', '200'),
('addressblock_length', '600'),
('cnv_addressblock_dim_144', 'y'),
('p_bulletin_margin', '5'),
('bull_espace_avis', '5'),
('change_ordre_aff_matieres', 'ok'),
('disable_login', 'no'),
('bull_formule_bas', 'Report card to preserve preciously. No duplicate will be delivered. - GEPI : free solution of management and follow-up of the school results.'),
('delai_devoirs', '7'),
('active_module_absence', 'y'),
('active_module_absence_professeur', 'y'),
('gepiSchoolTel', '00 00 00 00 00'),
('gepiSchoolFax', '00 00 00 00 00'),
('gepiSchoolEmail', 'sasse@sasse.com'),
('col_boite_largeur', '120'),
('bull_mention_doublant', 'no'),
('bull_affiche_numero', 'no'),
('nombre_tentatives_connexion', '5'),
('temps_compte_verrouille', '60'),
('bull_affiche_appreciations', 'y'),
('bull_affiche_absences', 'y'),
('bull_affiche_avis', 'y'),
('bull_affiche_aid', 'y'),
('bull_affiche_formule', 'y'),
('bull_affiche_signature', 'y'),
('l_max_aff_trombinoscopes', '120'),
('h_max_aff_trombinoscopes', '160'),
('l_max_imp_trombinoscopes', '70'),
('h_max_imp_trombinoscopes', '100'),
('active_module_msj', 'n'),
('site_msj_gepi', 'http://gepi.sylogix.net/releases/'),
('rc_module_msj', 'n'),
('beta_module_msj', 'n'),
('dossier_ftp_gepi', 'gepi'),
('bull_affiche_tel', 'n'),
('bull_affiche_fax', 'n'),
('note_autre_que_sur_20', 'F'),
('gepi_denom_boite', 'boite'),
('gepi_denom_boite_genre', 'f'),
('addressblock_font_size', '12'),
('addressblock_logo_etab_prop', '50'),
('addressblock_classe_annee', '35'),
('bull_ecart_bloc_nom', '1'),
('addressblock_debug', 'n'),
('GepiAccesReleveEleve', 'yes'),
('GepiAccesCahierTexteEleve', 'yes'),
('GepiAccesReleveParent', 'yes'),
('GepiAccesCahierTexteParent', 'yes'),
('enable_password_recovery', 'yes'),
('GepiPasswordReinitProf', 'no'),
('GepiPasswordReinitScolarite', 'no'),
('GepiPasswordReinitCpe', 'no'),
('GepiPasswordReinitAdmin', 'yes'),
('GepiPasswordReinitEleve', 'yes'),
('GepiPasswordReinitParent', 'yes'),
('cahier_texte_acces_public', 'no'),
('GepiAccesEquipePedaEleve', 'yes'),
('GepiAccesEquipePedaEmailEleve', 'no'),
('GepiAccesEquipePedaParent', 'yes'),
('GepiAccesEquipePedaEmailParent', 'no'),
('GepiAccesReport cardSimpleParent', 'yes'),
('GepiAccesReport cardSimpleEleve', 'yes'),
('GepiAccesGraphEleve', 'yes'),
('GepiAccesGraphParent', 'yes'),
('choix_bulletin', '2'),
('min_max_moyclas', '0'),
('bull_categ_font_size_avis', '10'),
('bull_police_avis', 'Times New Roman'),
('bull_font_style_avis', 'Normal'),
('bull_affiche_eleve_une_ligne', 'yes'),
('bull_mention_nom_court', 'yes'),
('option_modele_bulletin', '2'),
('security_alert_email_admin', 'yes'),
('security_alert_email_min_level', '2'),
('security_alert1_normal_cumulated_level', '3'),
('security_alert1_normal_email_admin', 'yes'),
('security_alert1_normal_block_user', 'no'),
('security_alert1_probation_cumulated_level', '1'),
('security_alert1_probation_email_admin', 'yes'),
('security_alert1_probation_block_user', 'no'),
('security_alert2_normal_cumulated_level', '6'),
('security_alert2_normal_email_admin', 'yes'),
('security_alert2_normal_block_user', 'yes'),
('security_alert2_probation_cumulated_level', '3'),
('security_alert2_probation_email_admin', 'yes'),
('security_alert2_probation_block_user', 'yes'),
('deverouillage_auto_periode_suivante', 'n'),
('bull_intitule_app', 'Appréciations / Conseils'),
('GepiAccesMoyennesProf', 'yes'),
('GepiAccesMoyennesProfTousEleves', 'yes'),
('GepiAccesMoyennesProfToutesClasses', 'yes'),
('GepiAccesReport cardSimpleProf', 'yes'),
('GepiAccesReport cardSimpleProfTousEleves', 'no'),
('GepiAccesReport cardSimpleProfToutesClasses', 'no'),
('gepi_stylesheet', 'style'),
('edt_calendrier_ouvert', 'y'),
('scolarite_modif_cours', 'y'),
('active_annees_anterieures', 'n'),
('active_notanet', 'n'),
('longmax_login', '8'),
('autorise_edt_tous', 'y'),
('autorise_edt_admin', 'y'),
('autorise_edt_eleve', 'yes'),
('utiliserMenuBarre', 'no'),
('active_absences_parents', 'y'),
('creneau_different', 'n'),
('active_inscription', 'n'),
('active_inscription_utilisateurs', 'n'),
('mod_inscription_explication', '<p> <strong>Presentation of the devices of the College in the colleges which organize meetings with the parents.</strong> <br />\r\n<br />\r\nEach one among you knows the situation in which the schools are placed : </p>\r\n<ul>\r\n    <li> demographic flop</li>\r\n    <li>regulation of the means</li>\r\n    <li>- ... </li>\r\n</ul>\r\nThis year still we must be present in the meetings organized within the colleges in order to have our specificities, our added value, evolution of the project, the international label, ... <br />\r\non this sheet, you have the possibility of registering you in order to intervene in one or more colleges according to your suitabilities.'),
('mod_inscription_titre', 'Intervention dans les collèges'),
('active_ateliers', 'n'),
('GepiAccesRestrAccesAppProfP', 'no'),
('l_resize_trombinoscopes', '120'),
('h_resize_trombinoscopes', '160'),
('multisite', 'n'),
('statuts_prives', 'n'),
('mod_edt_gr', 'n'),
('use_ent', 'n'),
('rss_cdt_eleve', 'n'),
('auth_locale', 'yes'),
('auth_ldap', 'no'),
('auth_sso', 'none'),
('ldap_write_access', 'no'),
('may_import_user_profile', 'no'),
('statut_utilisateur_defaut', 'professeur'),
('texte_visa_cdt', 'Textbook Signed this day <br />The Headmaster <br /> M. XXXXX<br />'),
('visa_cdt_inter_modif_notices_visees', 'yes'),
('denomination_eleve', 'student'),
('denomination_eleves', 'students'),
('denomination_professeur', 'professor'),
('denomination_professeurs', 'professors'),
('denomination_responsable', 'legal responsible'),
('denomination_responsables', 'legal responsibles'),
('delais_apres_cloture', '0'),
('active_mod_ooo', 'n'),
('use_only_cdt', 'n'),
('edt_remplir_prof', 'n'),
('active_mod_genese_classes', 'y'),
('active_mod_ects', 'y'),
('GepiAccesSaisieEctsProf', 'no'),
('GepiAccesSaisieEctsPP', 'no'),
('GepiAccesSaisieEctsScolarite', 'no'),
('GepiAccesRecapitulatifEctsScolarite', 'no'),
('GepiAccesRecapitulatifEctsProf', 'yes'),
('GepiAccesEditionDocsEctsPP', 'no'),
('GepiAccesEditionDocsEctsScolarite', 'no'),
('gepiSchoolStatut', 'prive_hors_contrat'),
('gepiSchoolAcademie', ''),
('note_autre_que_sur_referentiel', 'F'),
('referentiel_note', '20'),
('active_mod_apb', 'n'),
('active_mod_gest_aid', 'n'),
('unzipped_max_filesize', '10'),
('autorise_commentaires_mod_disc', 'yes'),
('sso_cas_table', 'no'),
('utiliser_mb', 'y'),
('filtrage_html', 'inputfilter'),
('utiliser_no_php_in_img', 'n'),
('backup_directory', '6sv8lv9218A1gfrV7lRGcU7sHa4dbP8v2As7M'),
('backupdir_lastchange', '1333789687'),
('mode_sauvegarde', 'mysqldump'),
('gepiSchoolRne', ''),
('gepiSchoolPays', 'Cameroon'),
('gepiAdminNom', 'Denu'),
('gepiAdminPrenom', 'Martial'),
('gepiAdminFonction', 'IT Officer'),
('gepiAdminAdressPageLogin', 'y'),
('contact_admin_mailto', 'n'),
('envoi_mail_liste', 'n'),
('gepiAdminAdressFormHidden', 'n'),
('mode_generation_pwd_majmin', 'n'),
('mode_generation_pwd_excl', 'y'),
('mode_email_resp', 'sconet'),
('mode_email_ele', 'sconet'),
('mode_utf8_bulletins_pdf', 'n'),
('mode_utf8_visu_notes_pdf', 'n'),
('mode_utf8_listes_pdf', 'n'),
('type_bulletin_par_defaut', 'html'),
('exp_imp_chgt_etab', 'no'),
('ele_lieu_naissance', 'no'),
('avis_conseil_classe_a_la_mano', 'n'),
('gepi_denom_mention', 'mention'),
('num_enregistrement_cnil', ''),
('mode_generation_login', 'name8'),
('bul_rel_nom_matieres', 'nom_complet_matiere'),
('acces_app_ele_resp', 'manuel'),
('display_users', 'tous'),
('active_mod_discipline', 'y'),
('message_login', '0'),
('export_cn_ods', 'n'),
('utiliser_sacoche', 'no'),
('sacocheUrl', ''),
('sacoche_base', ''),
('appreciations_types_profs', 'no'),
('autoriser_correction_bulletin', 'no'),
('autoriser_signalement_faute_app_prof', 'no'),
('GepiAccesVisuToutesEquipProf', 'no'),
('AAProfTout', 'no'),
('AAProfClasses', 'no'),
('AAProfGroupes', 'no'),
('GepiAccesGestElevesProf', 'no'),
('GepiAccesModifMaPhotoProfesseur', 'no'),
('visuDiscProfClasses', 'no'),
('visuDiscProfGroupes', 'no'),
('CommentairesTypesPP', 'no'),
('autoriser_signalement_faute_app_pp', 'no'),
('GepiAccesReport cardSimplePP', 'no'),
('GepiAccesGestElevesProfP', 'no'),
('GepiAccesGestPhotoElevesProfP', 'no'),
('AAProfPrinc', 'no'),
('modExbPP', 'no'),
('CommentairesTypesScol', 'no'),
('autoriser_signalement_faute_app_scol', 'no'),
('GepiAccesCdtScol', 'no'),
('GepiAccesCdtScolRestreint', 'no'),
('GepiAccesCdtVisa', 'no'),
('GepiAccesVisuToutesEquipScol', 'no'),
('AAScolTout', 'no'),
('AAScolResp', 'no'),
('GepiAccesModifMaPhotoScolarite', 'no'),
('GepiAccesTouteFicheEleveScolarite', 'no'),
('GepiAccesCdtCpe', 'yes'),
('GepiAccesCdtCpeRestreint', 'yes'),
('autoriser_signalement_faute_app_cpe', 'yes'),
('GepiAccesVisuToutesEquipCpe', 'no'),
('AACpeTout', 'no'),
('AACpeResp', 'no'),
('GepiAccesModifMaPhotoCpe', 'no'),
('GepiAccesTouteFicheEleveCpe', 'no'),
('GepiAccesAbsTouteClasseCpe', 'no'),
('GepiAccesModifMaPhotoAdministrateur', 'yes'),
('GepiAccesOptionsReleveEleve', 'no'),
('GepiAccesCpePPEmailEleve', 'no'),
('AAEleve', 'no'),
('GepiAccesModifMaPhotoEleve', 'no'),
('GepiAccesEleTrombiTousEleves', 'no'),
('GepiAccesEleTrombiElevesClasse', 'no'),
('GepiAccesEleTrombiPersonnels', 'no'),
('GepiAccesEleTrombiProfsClasse', 'no'),
('visuEleDisc', 'no'),
('GepiAccesOptionsReleveParent', 'no'),
('GepiAccesCpePPEmailParent', 'no'),
('AAResponsable', 'no'),
('visuRespDisc', 'no'),
('ImpressionFicheParent', '<p>Hello very dear parent. We thank you for having registered your child in our school. On this card, here accesses to our site for your account.</p>\r\n<p>Cordially</p>'),
('ImpressionNombre', '1'),
('ImpressionNombreParent', '1'),
('ImpressionNombreEleve', '1'),
('absence_classement_top', '10');

-- --------------------------------------------------------

--
-- Structure de la table `sso_table_correspondance`
--

CREATE TABLE IF NOT EXISTS `sso_table_correspondance` (
  `login_gepi` varchar(100) NOT NULL DEFAULT '',
  `login_sso` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`login_gepi`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `sso_table_correspondance`
--


-- --------------------------------------------------------

--
-- Structure de la table `suivi_eleve_cpe`
--

CREATE TABLE IF NOT EXISTS `suivi_eleve_cpe` (
  `id_suivi_eleve_cpe` int(11) NOT NULL AUTO_INCREMENT,
  `eleve_suivi_eleve_cpe` varchar(30) NOT NULL DEFAULT '',
  `parqui_suivi_eleve_cpe` varchar(150) NOT NULL,
  `date_suivi_eleve_cpe` date NOT NULL DEFAULT '0000-00-00',
  `heure_suivi_eleve_cpe` time NOT NULL,
  `komenti_suivi_eleve_cpe` text NOT NULL,
  `niveau_message_suivi_eleve_cpe` varchar(1) NOT NULL,
  `action_suivi_eleve_cpe` varchar(2) NOT NULL,
  `support_suivi_eleve_cpe` tinyint(4) NOT NULL,
  `courrier_suivi_eleve_cpe` int(11) NOT NULL,
  PRIMARY KEY (`id_suivi_eleve_cpe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `suivi_eleve_cpe`
--


-- --------------------------------------------------------

--
-- Structure de la table `synthese_app_classe`
--

CREATE TABLE IF NOT EXISTS `synthese_app_classe` (
  `id_classe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `synthese` text NOT NULL,
  PRIMARY KEY (`id_classe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `synthese_app_classe`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_alerte_mail`
--

CREATE TABLE IF NOT EXISTS `s_alerte_mail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_classe` smallint(6) unsigned NOT NULL,
  `destinataire` varchar(50) NOT NULL DEFAULT '',
  `adresse` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_classe` (`id_classe`,`destinataire`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_alerte_mail`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_autres_sanctions`
--

CREATE TABLE IF NOT EXISTS `s_autres_sanctions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_sanction` int(11) NOT NULL,
  `id_nature` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_autres_sanctions`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_categories`
--

CREATE TABLE IF NOT EXISTS `s_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categorie` varchar(50) NOT NULL DEFAULT '',
  `sigle` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_categories`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_communication`
--

CREATE TABLE IF NOT EXISTS `s_communication` (
  `id_communication` int(11) NOT NULL AUTO_INCREMENT,
  `id_incident` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `nature` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id_communication`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_communication`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_delegation`
--

CREATE TABLE IF NOT EXISTS `s_delegation` (
  `id_delegation` int(11) NOT NULL AUTO_INCREMENT,
  `fct_delegation` varchar(100) NOT NULL,
  `fct_autorite` varchar(50) NOT NULL,
  `nom_autorite` varchar(50) NOT NULL,
  PRIMARY KEY (`id_delegation`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_delegation`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_exclusions`
--

CREATE TABLE IF NOT EXISTS `s_exclusions` (
  `id_exclusion` int(11) NOT NULL AUTO_INCREMENT,
  `id_sanction` int(11) NOT NULL DEFAULT '0',
  `date_debut` date NOT NULL DEFAULT '0000-00-00',
  `heure_debut` varchar(20) NOT NULL DEFAULT '',
  `date_fin` date NOT NULL DEFAULT '0000-00-00',
  `heure_fin` varchar(20) NOT NULL DEFAULT '',
  `travail` text NOT NULL,
  `lieu` varchar(255) NOT NULL DEFAULT '',
  `nombre_jours` varchar(50) NOT NULL,
  `qualification_faits` text NOT NULL,
  `num_courrier` varchar(50) NOT NULL,
  `type_exclusion` varchar(50) NOT NULL,
  `id_signataire` int(11) NOT NULL,
  PRIMARY KEY (`id_exclusion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_exclusions`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_incidents`
--

CREATE TABLE IF NOT EXISTS `s_incidents` (
  `id_incident` int(11) NOT NULL AUTO_INCREMENT,
  `declarant` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `heure` varchar(20) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `nature` varchar(255) NOT NULL,
  `id_categorie` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `etat` varchar(20) NOT NULL,
  `message_id` varchar(50) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`id_incident`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `s_incidents`
--

INSERT INTO `s_incidents` (`id_incident`, `declarant`, `date`, `heure`, `id_lieu`, `nature`, `id_categorie`, `description`, `etat`, `message_id`, `commentaire`) VALUES
(1, 'elijah', '2012-03-02', 'M3', 4, 'Maladie', NULL, 'Vomissement après nourriture', 'clos', '1.20120302085357.60a70f', ''),
(2, 'elijah', '2012-03-03', '', 0, '', NULL, '', '', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `s_lieux_incidents`
--

CREATE TABLE IF NOT EXISTS `s_lieux_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lieu` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `s_lieux_incidents`
--

INSERT INTO `s_lieux_incidents` (`id`, `lieu`) VALUES
(1, 'Classe'),
(2, 'Couloir'),
(3, 'Cour'),
(4, 'Réfectoire'),
(5, 'Autre'),
(6, 'Stade de Basket');

-- --------------------------------------------------------

--
-- Structure de la table `s_mesures`
--

CREATE TABLE IF NOT EXISTS `s_mesures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('prise','demandee') DEFAULT NULL,
  `mesure` varchar(50) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `s_mesures`
--

INSERT INTO `s_mesures` (`id`, `type`, `mesure`, `commentaire`) VALUES
(1, 'prise', 'Travail supplémentaire', ''),
(2, 'prise', 'Mot dans le carnet de liaison', ''),
(3, 'demandee', 'Retenue', ''),
(4, 'demandee', 'Exclusion', '');

-- --------------------------------------------------------

--
-- Structure de la table `s_natures`
--

CREATE TABLE IF NOT EXISTS `s_natures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nature` varchar(50) NOT NULL DEFAULT '',
  `id_categorie` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `s_natures`
--

INSERT INTO `s_natures` (`id`, `nature`, `id_categorie`) VALUES
(1, 'Refus de travail', 0),
(2, 'Travail non fait', 0),
(3, 'Degradation', 0),
(4, 'Retards Répétés', 0),
(5, 'Oubli de matériel', 0),
(6, 'Insolence et comportement', 0),
(7, 'Violence verbale', 0),
(8, 'Violence physique', 0),
(9, 'Violence verbale et physique', 0),
(10, 'Bavardages répétés', 0);

-- --------------------------------------------------------

--
-- Structure de la table `s_protagonistes`
--

CREATE TABLE IF NOT EXISTS `s_protagonistes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_incident` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `qualite` varchar(50) NOT NULL,
  `avertie` enum('N','O') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `s_protagonistes`
--

INSERT INTO `s_protagonistes` (`id`, `id_incident`, `login`, `statut`, `qualite`, `avertie`) VALUES
(1, 1, 'siyapze', 'eleve', 'Responsable', 'N'),
(2, 2, 'honge', 'eleve', 'Victime', 'N');

-- --------------------------------------------------------

--
-- Structure de la table `s_qualites`
--

CREATE TABLE IF NOT EXISTS `s_qualites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qualite` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `s_qualites`
--

INSERT INTO `s_qualites` (`id`, `qualite`) VALUES
(1, 'Responsable'),
(2, 'Victime'),
(3, 'Témoin'),
(4, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `s_reports`
--

CREATE TABLE IF NOT EXISTS `s_reports` (
  `id_report` int(11) NOT NULL AUTO_INCREMENT,
  `id_sanction` int(11) NOT NULL,
  `id_type_sanction` int(11) NOT NULL,
  `nature_sanction` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `informations` text NOT NULL,
  `motif_report` varchar(255) NOT NULL,
  PRIMARY KEY (`id_report`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_reports`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_retenues`
--

CREATE TABLE IF NOT EXISTS `s_retenues` (
  `id_retenue` int(11) NOT NULL AUTO_INCREMENT,
  `id_sanction` int(11) NOT NULL,
  `date` date NOT NULL,
  `heure_debut` varchar(20) NOT NULL,
  `duree` float NOT NULL,
  `travail` text NOT NULL,
  `lieu` varchar(255) NOT NULL,
  PRIMARY KEY (`id_retenue`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_retenues`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_sanctions`
--

CREATE TABLE IF NOT EXISTS `s_sanctions` (
  `id_sanction` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `nature` varchar(255) NOT NULL,
  `effectuee` enum('N','O') NOT NULL,
  `id_incident` int(11) NOT NULL,
  PRIMARY KEY (`id_sanction`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_sanctions`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_traitement_incident`
--

CREATE TABLE IF NOT EXISTS `s_traitement_incident` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_incident` int(11) NOT NULL,
  `login_ele` varchar(50) NOT NULL,
  `login_u` varchar(50) NOT NULL,
  `id_mesure` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `s_traitement_incident`
--

INSERT INTO `s_traitement_incident` (`id`, `id_incident`, `login_ele`, `login_u`, `id_mesure`) VALUES
(1, 1, 'siyapze', 'elijah', 2);

-- --------------------------------------------------------

--
-- Structure de la table `s_travail`
--

CREATE TABLE IF NOT EXISTS `s_travail` (
  `id_travail` int(11) NOT NULL AUTO_INCREMENT,
  `id_sanction` int(11) NOT NULL,
  `date_retour` date NOT NULL,
  `heure_retour` varchar(20) NOT NULL,
  `travail` text NOT NULL,
  PRIMARY KEY (`id_travail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_travail`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_travail_mesure`
--

CREATE TABLE IF NOT EXISTS `s_travail_mesure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_incident` int(11) NOT NULL,
  `login_ele` varchar(50) NOT NULL,
  `travail` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `s_travail_mesure`
--


-- --------------------------------------------------------

--
-- Structure de la table `s_types_sanctions`
--

CREATE TABLE IF NOT EXISTS `s_types_sanctions` (
  `id_nature` int(11) NOT NULL AUTO_INCREMENT,
  `nature` varchar(255) NOT NULL,
  PRIMARY KEY (`id_nature`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `s_types_sanctions`
--

INSERT INTO `s_types_sanctions` (`id_nature`, `nature`) VALUES
(1, 'Warning work'),
(2, 'Warning behavior');

-- --------------------------------------------------------

--
-- Structure de la table `tempo`
--

CREATE TABLE IF NOT EXISTS `tempo` (
  `id_classe` int(11) NOT NULL DEFAULT '0',
  `max_periode` int(11) NOT NULL DEFAULT '0',
  `num` char(32) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `tempo`
--

INSERT INTO `tempo` (`id_classe`, `max_periode`, `num`) VALUES
(1, 9, '7ckih2s6257kc3iskc6f86d2b7'),
(1, 9, '1lco55ps031evh46b52h49btg0'),
(1, 9, 'o8b4n4ofvl9gemp034d0hk19a7'),
(1, 9, 'jiqrakosbkejfq3fmr3mc9uj06'),
(1, 9, '7ijapdvcq440c7biu4ioc0nqn2'),
(1, 9, '6digatncmnujujvjve60jp8cn2'),
(1, 9, '55mqg7ccsj1qhe6gg40birtt42'),
(1, 9, '36h935gb9drll9ade6tsrhaka0'),
(1, 3, '60bme5mcblprmu9r80tjlhumu2'),
(1, 3, '0lefifall60iulrgd96j89o3h5'),
(1, 3, 'inorl1mfo7t4t4o77ndceolfq6'),
(1, 3, 'p6hkn0f0hcteuglpf25o1v33a1');

-- --------------------------------------------------------

--
-- Structure de la table `tempo2`
--

CREATE TABLE IF NOT EXISTS `tempo2` (
  `col1` varchar(100) NOT NULL DEFAULT '',
  `col2` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `tempo2`
--


-- --------------------------------------------------------

--
-- Structure de la table `tempo3`
--

CREATE TABLE IF NOT EXISTS `tempo3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `col1` varchar(255) NOT NULL,
  `col2` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `tempo3`
--


-- --------------------------------------------------------

--
-- Structure de la table `tempo3_cdt`
--

CREATE TABLE IF NOT EXISTS `tempo3_cdt` (
  `id_classe` int(11) NOT NULL DEFAULT '0',
  `classe` varchar(255) NOT NULL DEFAULT '',
  `matiere` varchar(255) NOT NULL DEFAULT '',
  `enseignement` varchar(255) NOT NULL DEFAULT '',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `fichier` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `tempo3_cdt`
--


-- --------------------------------------------------------

--
-- Structure de la table `temp_gep_import`
--

CREATE TABLE IF NOT EXISTS `temp_gep_import` (
  `ID_TEMPO` varchar(40) NOT NULL DEFAULT '',
  `LOGIN` varchar(40) NOT NULL DEFAULT '',
  `ELENOM` varchar(40) NOT NULL DEFAULT '',
  `ELEPRE` varchar(40) NOT NULL DEFAULT '',
  `ELESEXE` varchar(40) NOT NULL DEFAULT '',
  `ELEDATNAIS` varchar(40) NOT NULL DEFAULT '',
  `ELENOET` varchar(40) NOT NULL DEFAULT '',
  `ERENO` varchar(40) NOT NULL DEFAULT '',
  `ELEDOUBL` varchar(40) NOT NULL DEFAULT '',
  `ELENONAT` varchar(40) NOT NULL DEFAULT '',
  `ELEREG` varchar(40) NOT NULL DEFAULT '',
  `DIVCOD` varchar(40) NOT NULL DEFAULT '',
  `ETOCOD_EP` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT1` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT2` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT3` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT4` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT5` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT6` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT7` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT8` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT9` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT10` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT11` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT12` varchar(40) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `temp_gep_import`
--


-- --------------------------------------------------------

--
-- Structure de la table `temp_gep_import2`
--

CREATE TABLE IF NOT EXISTS `temp_gep_import2` (
  `ID_TEMPO` varchar(40) NOT NULL DEFAULT '',
  `LOGIN` varchar(40) NOT NULL DEFAULT '',
  `ELENOM` varchar(40) NOT NULL DEFAULT '',
  `ELEPRE` varchar(40) NOT NULL DEFAULT '',
  `ELESEXE` varchar(40) NOT NULL DEFAULT '',
  `ELEDATNAIS` varchar(40) NOT NULL DEFAULT '',
  `ELENOET` varchar(40) NOT NULL DEFAULT '',
  `ELE_ID` varchar(40) NOT NULL DEFAULT '',
  `ELEDOUBL` varchar(40) NOT NULL DEFAULT '',
  `ELENONAT` varchar(40) NOT NULL DEFAULT '',
  `ELEREG` varchar(40) NOT NULL DEFAULT '',
  `DIVCOD` varchar(40) NOT NULL DEFAULT '',
  `ETOCOD_EP` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT1` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT2` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT3` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT4` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT5` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT6` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT7` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT8` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT9` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT10` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT11` varchar(40) NOT NULL DEFAULT '',
  `ELEOPT12` varchar(40) NOT NULL DEFAULT '',
  `LIEU_NAISSANCE` varchar(50) NOT NULL DEFAULT '',
  `MEL` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `temp_gep_import2`
--


-- --------------------------------------------------------

--
-- Structure de la table `tentatives_intrusion`
--

CREATE TABLE IF NOT EXISTS `tentatives_intrusion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL DEFAULT '',
  `adresse_ip` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `niveau` smallint(6) NOT NULL,
  `fichier` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `statut` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

--
-- Contenu de la table `tentatives_intrusion`
--

INSERT INTO `tentatives_intrusion` (`id`, `login`, `adresse_ip`, `date`, `niveau`, `fichier`, `description`, `statut`) VALUES
(1, '-', '127.0.0.1', '2012-01-18 09:32:08', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(2, '-', '127.0.0.1', '2012-01-20 11:55:45', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(3, '-', '127.0.0.1', '2012-01-20 12:18:24', 1, '/login.php', 'Tentative de connexion avec un login incorrect (n''existe pas dans la base Gepi). Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login utilisé : )', 'new'),
(4, '-', '127.0.0.1', '2012-01-21 09:34:25', 1, '/login.php', 'Tentative de connexion avec un login incorrect (n''existe pas dans la base Gepi). Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login utilisé : )', 'new'),
(5, '-', '127.0.0.1', '2012-01-21 09:34:43', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(6, '-', '127.0.0.1', '2012-02-05 13:37:57', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(7, '-', '127.0.0.1', '2012-02-05 13:39:41', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(8, 'ADMIN', '127.0.0.1', '2012-02-05 14:24:19', 1, '/gestion/param_ordre_item.php', 'Tentative d''accès à un fichier sans avoir les droits nécessaires', 'new'),
(9, 'ADMIN', '127.0.0.1', '2012-02-05 14:25:08', 1, '/gestion/param_ordre_item.php', 'Tentative d''accès à un fichier sans avoir les droits nécessaires', 'new'),
(10, '-', '127.0.0.1', '2012-02-05 14:25:12', 1, '/gestion/index.php', 'Accès à une page sans être logué (peut provenir d''un timeout de session).', 'new'),
(11, '-', '127.0.0.1', '2012-02-05 16:33:48', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(12, '-', '127.0.0.1', '2012-02-06 10:02:25', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(13, 'ADMIN', '127.0.0.1', '2012-02-13 15:57:23', 2, 'i/Gepi/accueil.php', 'Tentative d''accès avec modification sauvage de gepiPath', 'new'),
(14, 'ADMIN', '127.0.0.1', '2012-02-13 15:58:21', 2, '/accueil.php', 'Tentative d''accès avec modification sauvage de gepiPath', 'new'),
(15, '-', '127.0.0.1', '2012-02-13 15:58:58', 1, '/gepii/Gepi/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(16, '-', '127.0.0.1', '2012-02-13 15:59:14', 1, '/gepii/Gepi/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(17, '-', '127.0.0.1', '2012-02-13 15:59:32', 1, '/gepii/Gepi/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(18, '-', '127.0.0.1', '2012-02-13 16:01:32', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(19, '-', '127.0.0.1', '2012-02-13 16:01:32', 2, '/login.php', 'Verrouillage du compte admin en raison d''un trop grand nombre de tentatives de connexion infructueuses. Ce peut être une tentative d''attaque brute-force.', 'new'),
(20, '-', '127.0.0.1', '2012-02-15 07:04:38', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(21, '-', '127.0.0.1', '2012-02-15 07:06:15', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(22, '-', '127.0.0.1', '2012-02-15 07:06:30', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(23, '-', '127.0.0.1', '2012-02-15 07:09:06', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(24, 'Makengne', '127.0.0.1', '2012-02-26 16:34:41', 2, '/prepa_conseil/index3.php', 'Changement de la valeur de id_classe pour un type non numérique.', 'new'),
(25, '-', '127.0.0.1', '2012-02-26 16:35:10', 1, '/login.php', 'Tentative de connexion avec un login incorrect (n''existe pas dans la base Gepi). Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login utilisé : Nikeme)', 'new'),
(26, '-', '127.0.0.1', '2012-02-26 16:35:24', 1, '/login.php', 'Tentative de connexion avec un login incorrect (n''existe pas dans la base Gepi). Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login utilisé : Nikeme)', 'new'),
(27, '-', '127.0.0.1', '2012-02-27 08:02:59', 1, '/edt_organisation/edt_eleve.php', 'Accès à une page sans être logué (peut provenir d''un timeout de session).', 'new'),
(28, '-', '127.0.0.1', '2012-02-28 14:44:34', 1, '/login.php', 'Tentative de connexion avec un login incorrect (n''existe pas dans la base Gepi). Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login utilisé : fsdf)', 'new'),
(29, '-', '127.0.0.1', '2012-02-29 12:23:07', 1, '/accueil.php', 'Accès à une page sans être logué (peut provenir d''un timeout de session).', 'new'),
(30, '-', '127.0.0.1', '2012-02-29 12:25:25', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(31, '-', '127.0.0.1', '2012-02-29 12:25:38', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(32, '-', '127.0.0.1', '2012-03-01 08:24:23', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(33, '-', '127.0.0.1', '2012-03-01 09:02:23', 1, '/edt_organisation/edt_eleve.php', 'Accès à une page sans être logué (peut provenir d''un timeout de session).', 'new'),
(34, 'ADMIN', '127.0.0.1', '2012-03-02 04:33:12', 1, '/sms/index.php', 'Tentative d''accès à un fichier sans avoir les droits nécessaires', 'new'),
(35, '-', '127.0.0.1', '2012-03-02 04:33:30', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(36, '-', '127.0.0.1', '2012-03-02 04:33:40', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(37, 'ADMIN', '127.0.0.1', '2012-03-02 04:35:33', 1, '/sms/index.php', 'Tentative d''accès à un fichier sans avoir les droits nécessaires', 'new'),
(38, '-', '127.0.0.1', '2012-03-02 04:42:12', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(39, '-', '127.0.0.1', '2012-03-02 04:42:57', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(40, '-', '127.0.0.1', '2012-03-02 04:42:57', 2, '/login.php', 'Verrouillage du compte admin en raison d''un trop grand nombre de tentatives de connexion infructueuses. Ce peut être une tentative d''attaque brute-force.', 'new'),
(41, '-', '127.0.0.1', '2012-03-02 04:44:06', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(42, '-', '127.0.0.1', '2012-03-02 04:44:06', 2, '/login.php', 'Verrouillage du compte admin en raison d''un trop grand nombre de tentatives de connexion infructueuses. Ce peut être une tentative d''attaque brute-force.', 'new'),
(43, '-', '127.0.0.1', '2012-03-02 04:44:54', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(44, '-', '127.0.0.1', '2012-03-02 04:44:54', 2, '/login.php', 'Verrouillage du compte admin en raison d''un trop grand nombre de tentatives de connexion infructueuses. Ce peut être une tentative d''attaque brute-force.', 'new'),
(45, '-', '127.0.0.1', '2012-03-02 07:35:52', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(46, '-', '127.0.0.1', '2012-03-02 07:36:04', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(47, '-', '127.0.0.1', '2012-03-02 07:36:22', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : duo)', 'new'),
(48, '-', '127.0.0.1', '2012-03-02 10:19:50', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : scolar)', 'new'),
(49, '-', '127.0.0.1', '2012-03-02 10:22:23', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : Makengne)', 'new'),
(50, '-', '127.0.0.1', '2012-03-02 21:33:28', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(51, '-', '127.0.0.1', '2012-03-02 23:32:27', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(52, '-', '127.0.0.1', '2012-03-03 13:40:56', 1, '/cahier_notes/add_modif_dev.php', 'Accès à une page sans être logué (peut provenir d''un timeout de session).', 'new'),
(53, '-', '127.0.0.1', '2012-03-03 14:19:40', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : elijah)', 'new'),
(54, '-', '127.0.0.1', '2012-03-22 16:56:25', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(55, '-', '127.0.0.1', '2012-03-22 16:56:43', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new'),
(56, '-', '127.0.0.1', '2012-03-26 10:35:01', 1, '/calculnotes/eleves.php', 'Accès à une page sans être logué (peut provenir d''un timeout de session).', 'new'),
(57, '-', '127.0.0.1', '2012-03-26 10:38:25', 1, '/login.php', 'Tentative de connexion avec un login incorrect (n''existe pas dans la base Gepi). Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login utilisé : euie)', 'new');

-- --------------------------------------------------------

--
-- Structure de la table `udt_corresp`
--

CREATE TABLE IF NOT EXISTS `udt_corresp` (
  `champ` varchar(255) NOT NULL DEFAULT '',
  `nom_udt` varchar(255) NOT NULL DEFAULT '',
  `nom_gepi` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `udt_corresp`
--


-- --------------------------------------------------------

--
-- Structure de la table `udt_lignes`
--

CREATE TABLE IF NOT EXISTS `udt_lignes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `division` varchar(255) NOT NULL DEFAULT '',
  `matiere` varchar(255) NOT NULL DEFAULT '',
  `prof` varchar(255) NOT NULL DEFAULT '',
  `groupe` varchar(255) NOT NULL DEFAULT '',
  `regroup` varchar(255) NOT NULL DEFAULT '',
  `mo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `udt_lignes`
--


-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `nom` varchar(50) NOT NULL DEFAULT '',
  `prenom` varchar(50) NOT NULL DEFAULT '',
  `civilite` varchar(5) NOT NULL DEFAULT '',
  `password` varchar(128) NOT NULL DEFAULT '',
  `salt` varchar(128) DEFAULT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `show_email` varchar(3) NOT NULL DEFAULT 'no',
  `statut` varchar(20) NOT NULL DEFAULT '',
  `etat` varchar(20) NOT NULL DEFAULT '',
  `change_mdp` char(1) NOT NULL DEFAULT 'n',
  `date_verrouillage` datetime NOT NULL DEFAULT '2006-01-01 00:00:00',
  `password_ticket` varchar(255) NOT NULL,
  `ticket_expiration` datetime NOT NULL,
  `niveau_alerte` smallint(6) NOT NULL DEFAULT '0',
  `observation_securite` tinyint(4) NOT NULL DEFAULT '0',
  `temp_dir` varchar(255) NOT NULL,
  `numind` varchar(255) NOT NULL,
  `auth_mode` enum('gepi','ldap','sso') NOT NULL DEFAULT 'gepi',
  PRIMARY KEY (`login`),
  KEY `statut` (`statut`),
  KEY `etat` (`etat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`login`, `nom`, `prenom`, `civilite`, `password`, `salt`, `email`, `show_email`, `statut`, `etat`, `change_mdp`, `date_verrouillage`, `password_ticket`, `ticket_expiration`, `niveau_alerte`, `observation_securite`, `temp_dir`, `numind`, `auth_mode`) VALUES
('ADMIN', 'SAJOSCOL', 'Administrator', 'M.', 'ead325d878724499578533f5af89874fce9696d3ecf410fcda188c539c10d8fe', '6478fe26654e796c4c67fb1b67fc6eed', '', 'no', 'administrateur', 'actif', 'n', '2012-03-02 05:44:54', '', '0000-00-00 00:00:00', 8, 0, 'ADMIN_3Dbv9J8BQj2qQZsvGTr3PM7m4p2Fo7T5800UHbqj9b0', '', 'gepi'),
('elijah', 'Elijah', '', 'M.', 'f46603718eeabf318d2d7204aff1bb249d1c41aeffa6bc97af9afc8ee0a4b145', '5f58e2f0c3bc1fccfd7cdaaaef91321b', 'elijah@sajoscol.net', 'no', 'professeur', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, 'elijah_YBN4Gw7Q9nQqo0Dp5EZO38NoJl6hvQUPoQQO49u', '', 'gepi'),
('nkwenti', 'NKWENTI', 'PASCALINE', 'Mlle', 'a6b41929f22bb9786016cf5a4d3b7613', NULL, 'pascaline.nkwenti@sajoscol.net', 'no', 'professeur', 'actif', 'y', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, '', '', 'gepi'),
('tchomnou', 'TCHOMNOU', 'Raphael', 'M.', 'dfcbdb9ea042728d9b5eac53ed83eb8e', NULL, '', 'no', 'cpe', 'actif', 'y', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, '', '', 'gepi'),
('bih', 'BIH FUH', 'JACQUELINE', 'Mlle', 'dfcbdb9ea042728d9b5eac53ed83eb8e', NULL, 'jacqueline.bih@sajoscol.net', 'no', 'professeur', 'actif', 'y', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, '', '', 'gepi'),
('ekah', 'EKAH', 'ROBERT', 'M.', 'dfcbdb9ea042728d9b5eac53ed83eb8e', NULL, 'robert.ekah@sajoscol.net', 'no', 'professeur', 'actif', 'y', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, '', '', 'gepi'),
('Pere', 'Parent', 'ABEDNEGO NGAHA NJIKE', '', '', NULL, '', 'no', 'responsable', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, '', '', 'gepi'),
('Makengne', 'Makengne', 'Nikème', 'Mme', '30fd7c57e50026bb6931c4d6066d9993b00095a9c5d728c385adc3991cbabb13', 'cb04933c5bc12453d8e0a3386367f091', 'nik@yahoo.fr', 'no', 'responsable', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 2, 0, '', '', 'gepi'),
('Assontia2', 'Assontia', 'Patient', 'M.', '', NULL, '', 'no', 'responsable', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, '', '', 'gepi'),
('Assontia', 'Assontia', 'Thomas', 'M.', '9457cb0186ad62015e17b6ce1e6150aac89ea5f021911d164ea32e7e7491574b', '4e4bb13f42ba4aab98026c88ac654a16', 'assontia@gmail.com', 'no', 'responsable', 'actif', 'y', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, '', '', 'gepi'),
('duo', 'Admin', 'Duo', 'M.', '425ed32408afe8af07a3a707d820ee1b8ad5de75d48d0a5aef62146a69fc92d3', '913f9c87ec300efe69476134d9794849', 'patient@camertic.com', 'no', 'administrateur', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, 'duo_0n8NzyM60S66pfnLjB6oFjkSS8Qb7T0bPI0', '', 'gepi'),
('morfaw', 'Morfaw', 'Elvis', 'M.', 'dfcbdb9ea042728d9b5eac53ed83eb8e', NULL, '', 'no', 'professeur', 'actif', 'y', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, '', '', 'gepi'),
('scolar', 'Scolar', 'Admin', 'M.', 'de89aedbde16e62fb4018bbcb040c791227b50ec3c40cfc576f119442894e980', '0809cb6938876a4864cd0d82370fa6b8', 'patient', 'no', 'scolarite', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 0, 0, 'scolar_Azbi36P3bhpBp5RLNLI5rZVJfJh1n33aML54y2', '', 'gepi');

-- --------------------------------------------------------

--
-- Structure de la table `vocabulaire`
--

CREATE TABLE IF NOT EXISTS `vocabulaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `terme` varchar(255) NOT NULL DEFAULT '',
  `terme_corrige` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `vocabulaire`
--

INSERT INTO `vocabulaire` (`id`, `terme`, `terme_corrige`) VALUES
(1, 'il peu', 'il peut'),
(2, 'elle peu', 'elle peut'),
(3, 'un peut', 'un peu'),
(4, 'trop peut', 'trop peu'),
(5, 'baise', 'baisse'),
(6, 'baisé', 'baissé'),
(7, 'baiser', 'baisser'),
(8, 'courge', 'courage'),
(9, 'camer', 'calmer'),
(10, 'camé', 'calmé'),
(11, 'came', 'calme'),
(12, 'tu est', 'tu es'),
(13, 'tu et', 'tu es'),
(14, 'il et', 'il est'),
(15, 'il es', 'il est'),
(16, 'elle et', 'elle est'),
(17, 'elle es', 'elle est');

-- --------------------------------------------------------

--
-- Structure de la table `vs_alerts_eleves`
--

CREATE TABLE IF NOT EXISTS `vs_alerts_eleves` (
  `id_alert_eleve` int(11) NOT NULL AUTO_INCREMENT,
  `eleve_alert_eleve` varchar(100) NOT NULL,
  `date_alert_eleve` date NOT NULL,
  `groupe_alert_eleve` int(11) NOT NULL,
  `type_alert_eleve` int(11) NOT NULL,
  `nb_trouve` int(11) NOT NULL,
  `temp_insert` varchar(100) NOT NULL,
  `etat_alert_eleve` tinyint(4) NOT NULL,
  `etatpar_alert_eleve` varchar(100) NOT NULL,
  PRIMARY KEY (`id_alert_eleve`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `vs_alerts_eleves`
--


-- --------------------------------------------------------

--
-- Structure de la table `vs_alerts_groupes`
--

CREATE TABLE IF NOT EXISTS `vs_alerts_groupes` (
  `id_alert_groupe` int(11) NOT NULL AUTO_INCREMENT,
  `nom_alert_groupe` varchar(150) NOT NULL,
  `creerpar_alert_groupe` varchar(100) NOT NULL,
  PRIMARY KEY (`id_alert_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `vs_alerts_groupes`
--


-- --------------------------------------------------------

--
-- Structure de la table `vs_alerts_types`
--

CREATE TABLE IF NOT EXISTS `vs_alerts_types` (
  `id_alert_type` int(11) NOT NULL AUTO_INCREMENT,
  `groupe_alert_type` int(11) NOT NULL,
  `type_alert_type` varchar(10) NOT NULL,
  `specifisite_alert_type` varchar(25) NOT NULL,
  `eleve_concerne` text NOT NULL,
  `date_debut_comptage` date NOT NULL,
  `nb_comptage_limit` varchar(200) NOT NULL,
  PRIMARY KEY (`id_alert_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `vs_alerts_types`
--

