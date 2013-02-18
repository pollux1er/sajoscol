-- phpMyAdmin SQL Dump
-- version 3.3.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2012 at 10:08 AM
-- Server version: 5.1.49
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gepi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absences`
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
-- Dumping data for table `absences`
--


-- --------------------------------------------------------

--
-- Table structure for table `absences_actions`
--

CREATE TABLE IF NOT EXISTS `absences_actions` (
  `id_absence_action` int(11) NOT NULL AUTO_INCREMENT,
  `init_absence_action` char(2) NOT NULL DEFAULT '',
  `def_absence_action` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_absence_action`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `absences_actions`
--

INSERT INTO `absences_actions` (`id_absence_action`, `init_absence_action`, `def_absence_action`) VALUES
(1, 'RC', 'Renvoi du cours'),
(2, 'RD', 'Renvoi d&eacute;finitif'),
(3, 'LP', 'Lettre aux parents'),
(4, 'CE', 'Demande de convocation de l&#039;&eacute;l&egrave;ve en vie scolaire'),
(5, 'A', 'Aucune');

-- --------------------------------------------------------

--
-- Table structure for table `absences_eleves`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `absences_eleves`
--


-- --------------------------------------------------------

--
-- Table structure for table `absences_gep`
--

CREATE TABLE IF NOT EXISTS `absences_gep` (
  `id_seq` char(2) NOT NULL DEFAULT '',
  `type` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_seq`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `absences_gep`
--


-- --------------------------------------------------------

--
-- Table structure for table `absences_motifs`
--

CREATE TABLE IF NOT EXISTS `absences_motifs` (
  `id_motif_absence` int(11) NOT NULL AUTO_INCREMENT,
  `init_motif_absence` char(2) NOT NULL DEFAULT '',
  `def_motif_absence` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_motif_absence`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `absences_motifs`
--

INSERT INTO `absences_motifs` (`id_motif_absence`, `init_motif_absence`, `def_motif_absence`) VALUES
(1, 'A', 'Aucun motif'),
(2, 'AS', 'Accident sport'),
(3, 'AT', 'Absent en retenue'),
(4, 'C', 'Dans la cour'),
(5, 'CF', 'Convenances familiales'),
(6, 'CO', 'Convocation bureau'),
(7, 'CS', 'Compétition sportive'),
(8, 'DI', 'Dispense d''E.P.S.'),
(9, 'ET', 'Erreur d''emploi du temps'),
(10, 'EX', 'Examen'),
(11, 'H', 'Hospitalisation'),
(12, 'JP', 'Justification par le Principal'),
(13, 'MA', 'Maladie'),
(14, 'OR', 'Conseiller'),
(15, 'PR', 'Réveil'),
(16, 'RC', 'Refus de venir en cours'),
(17, 'RE', 'Renvoi'),
(18, 'RT', 'Présent en retenue'),
(19, 'RV', 'Renvoi du cours'),
(20, 'SM', 'Refus de justification'),
(21, 'SP', 'Sortie pédagogique'),
(22, 'ST', 'Stage à l''extérieur'),
(23, 'T', 'Téléphone'),
(24, 'TR', 'Transport'),
(25, 'VM', 'Visite médicale'),
(26, 'IN', 'Infirmerie');

-- --------------------------------------------------------

--
-- Table structure for table `absences_rb`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `absences_rb`
--


-- --------------------------------------------------------

--
-- Table structure for table `absences_repas`
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
-- Dumping data for table `absences_repas`
--


-- --------------------------------------------------------

--
-- Table structure for table `acces_cdt`
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
-- Dumping data for table `acces_cdt`
--


-- --------------------------------------------------------

--
-- Table structure for table `acces_cdt_groupes`
--

CREATE TABLE IF NOT EXISTS `acces_cdt_groupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_acces` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `acces_cdt_groupes`
--


-- --------------------------------------------------------

--
-- Table structure for table `aid`
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
-- Dumping data for table `aid`
--


-- --------------------------------------------------------

--
-- Table structure for table `aid_appreciations`
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
-- Dumping data for table `aid_appreciations`
--


-- --------------------------------------------------------

--
-- Table structure for table `aid_config`
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
-- Dumping data for table `aid_config`
--


-- --------------------------------------------------------

--
-- Table structure for table `aid_familles`
--

CREATE TABLE IF NOT EXISTS `aid_familles` (
  `ordre_affichage` smallint(6) NOT NULL DEFAULT '0',
  `id` smallint(6) NOT NULL DEFAULT '0',
  `type` varchar(250) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `aid_familles`
--

INSERT INTO `aid_familles` (`ordre_affichage`, `id`, `type`) VALUES
(0, 10, 'Information, presse'),
(1, 11, 'Philosophie et psychologie, pensée'),
(2, 12, 'Religions'),
(3, 13, 'Sciences sociales, société, humanitaire'),
(4, 14, 'Langues, langage'),
(5, 15, 'Sciences (sciences dures)'),
(6, 16, 'Techniques, sciences appliquées, médecine, cuisine...'),
(7, 17, 'Arts, loisirs et sports'),
(8, 18, 'Littérature, théâtre, poésie'),
(9, 19, 'Géographie et Histoire, civilisations anciennes');

-- --------------------------------------------------------

--
-- Table structure for table `aid_productions`
--

CREATE TABLE IF NOT EXISTS `aid_productions` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `aid_productions`
--

INSERT INTO `aid_productions` (`id`, `nom`) VALUES
(1, 'Dossier papier'),
(2, 'Emission de radio'),
(3, 'Exposition'),
(4, 'Film'),
(5, 'Spectacle'),
(6, 'Réalisation plastique'),
(7, 'Réalisation technique ou scientifique'),
(8, 'Jeu vidéo'),
(9, 'Animation culturelle'),
(10, 'Maquette'),
(11, 'Site internet'),
(12, 'Diaporama'),
(13, 'Production musicale'),
(14, 'Production théâtrale'),
(15, 'Animation en milieu scolaire'),
(16, 'Programmation logicielle'),
(17, 'Journal');

-- --------------------------------------------------------

--
-- Table structure for table `aid_public`
--

CREATE TABLE IF NOT EXISTS `aid_public` (
  `ordre_affichage` smallint(6) NOT NULL DEFAULT '0',
  `id` smallint(6) NOT NULL DEFAULT '0',
  `public` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `aid_public`
--

INSERT INTO `aid_public` (`ordre_affichage`, `id`, `public`) VALUES
(3, 1, 'Lycéens'),
(2, 2, 'Collègiens'),
(1, 3, 'Ecoliers'),
(6, 4, 'Grand public'),
(5, 5, 'Experts (ou spécialistes)'),
(4, 6, 'Etudiants');

-- --------------------------------------------------------

--
-- Table structure for table `archivage_aids`
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
-- Dumping data for table `archivage_aids`
--


-- --------------------------------------------------------

--
-- Table structure for table `archivage_aid_eleve`
--

CREATE TABLE IF NOT EXISTS `archivage_aid_eleve` (
  `id_aid` int(11) NOT NULL DEFAULT '0',
  `id_eleve` varchar(255) NOT NULL,
  `eleve_resp` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id_aid`,`id_eleve`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `archivage_aid_eleve`
--


-- --------------------------------------------------------

--
-- Table structure for table `archivage_appreciations_aid`
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
-- Dumping data for table `archivage_appreciations_aid`
--


-- --------------------------------------------------------

--
-- Table structure for table `archivage_disciplines`
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
-- Dumping data for table `archivage_disciplines`
--


-- --------------------------------------------------------

--
-- Table structure for table `archivage_ects`
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
  `valeur` decimal(10,0) NOT NULL COMMENT 'Nombre de crÃ©dits obtenus par l''eleve',
  `mention` varchar(255) NOT NULL COMMENT 'Mention obtenue',
  PRIMARY KEY (`id`,`ine`,`num_periode`,`special`),
  KEY `archivage_ects_FI_1` (`ine`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `archivage_ects`
--


-- --------------------------------------------------------

--
-- Table structure for table `archivage_eleves`
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
-- Dumping data for table `archivage_eleves`
--


-- --------------------------------------------------------

--
-- Table structure for table `archivage_eleves2`
--

CREATE TABLE IF NOT EXISTS `archivage_eleves2` (
  `annee` varchar(50) NOT NULL DEFAULT '',
  `ine` varchar(50) NOT NULL,
  `doublant` enum('-','R') NOT NULL DEFAULT '-',
  `regime` varchar(255) NOT NULL,
  PRIMARY KEY (`ine`,`annee`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `archivage_eleves2`
--


-- --------------------------------------------------------

--
-- Table structure for table `archivage_types_aid`
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
-- Dumping data for table `archivage_types_aid`
--


-- --------------------------------------------------------

--
-- Table structure for table `ateliers_config`
--

CREATE TABLE IF NOT EXISTS `ateliers_config` (
  `nom_champ` char(100) NOT NULL DEFAULT '',
  `content` char(255) NOT NULL DEFAULT '',
  `param` char(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ateliers_config`
--


-- --------------------------------------------------------

--
-- Table structure for table `avis_conseil_classe`
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
-- Dumping data for table `avis_conseil_classe`
--


-- --------------------------------------------------------

--
-- Table structure for table `a_agregation_decompte`
--

CREATE TABLE IF NOT EXISTS `a_agregation_decompte` (
  `eleve_id` int(11) NOT NULL COMMENT 'id de l''eleve',
  `date_demi_jounee` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date de la demi journée agrégée : 00:00 pour une matinée, 12:00 pour une après midi',
  `manquement_obligation_presence` tinyint(4) DEFAULT '0' COMMENT 'Cette demi journée est comptée comme absence',
  `justifiee` tinyint(4) DEFAULT '0' COMMENT 'Si cette demi journée est compté comme absence, y a-t-il une justification',
  `notifiee` tinyint(4) DEFAULT '0' COMMENT 'Si cette demi journée est compté comme absence, y a-t-il une notification à la famille',
  `nb_retards` int(11) DEFAULT '0' COMMENT 'Nombre de retards décomptés dans la demi journée',
  `nb_retards_justifies` int(11) DEFAULT '0' COMMENT 'Nombre de retards justifiés décomptés dans la demi journée',
  `motifs_absences` text COMMENT 'Liste des motifs (table a_motifs) associés à cette demi-journée d''absence',
  `motifs_retards` text COMMENT 'Liste des motifs (table a_motifs) associés aux retard de cette demi-journée',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`eleve_id`,`date_demi_jounee`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table d''agregation des decomptes de demi journees d''absence ';

--
-- Dumping data for table `a_agregation_decompte`
--


-- --------------------------------------------------------

--
-- Table structure for table `a_justifications`
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
-- Dumping data for table `a_justifications`
--

INSERT INTO `a_justifications` (`id`, `nom`, `commentaire`, `sortable_rank`, `created_at`, `updated_at`) VALUES
(1, 'Certificat médical', 'Une justification établie par une autorité médicale', 1, NULL, NULL),
(2, 'Courrier familial', 'Justification par courrier de la famille', 2, NULL, NULL),
(3, 'Justificatif d''une administration publique', 'Justification émise par une administration publique', 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `a_lieux`
--

CREATE TABLE IF NOT EXISTS `a_lieux` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
  `nom` varchar(250) NOT NULL COMMENT 'Nom du lieu',
  `commentaire` text COMMENT 'commentaire saisi par l''utilisateur',
  `sortable_rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Lieu pour les types d''absence ou les saisies' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `a_lieux`
--


-- --------------------------------------------------------

--
-- Table structure for table `a_motifs`
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
-- Dumping data for table `a_motifs`
--

INSERT INTO `a_motifs` (`id`, `nom`, `commentaire`, `sortable_rank`, `created_at`, `updated_at`) VALUES
(1, 'Médical', 'L''élève est absent pour raison médicale', 1, NULL, NULL),
(2, 'Familial', 'L''élève est absent pour raison familiale', 2, NULL, NULL),
(3, 'Sportive', 'L''élève est absent pour cause de compétition sportive', 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `a_notifications`
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
  `erreur_message_envoi` text COMMENT 'Message d''erreur retourné par le service d''envoi',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `a_notifications_FI_1` (`utilisateur_id`),
  KEY `a_notifications_FI_2` (`a_traitement_id`),
  KEY `a_notifications_FI_3` (`adr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Notification (a la famille) des absences' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `a_notifications`
--


-- --------------------------------------------------------

--
-- Table structure for table `a_saisies`
--

CREATE TABLE IF NOT EXISTS `a_saisies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a saisi l''absence',
  `eleve_id` int(11) DEFAULT NULL COMMENT 'id_eleve de l''eleve objet de la saisie, egal à null si aucun eleve n''est saisi',
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
  `deleted_by` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a supprimé la saisie',
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
-- Dumping data for table `a_saisies`
--


-- --------------------------------------------------------

--
-- Table structure for table `a_saisies_version`
--

CREATE TABLE IF NOT EXISTS `a_saisies_version` (
  `id` int(11) NOT NULL,
  `utilisateur_id` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a saisi l''absence',
  `eleve_id` int(11) DEFAULT NULL COMMENT 'id_eleve de l''eleve objet de la saisie, egal à null si aucun eleve n''est saisi',
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
  `deleted_by` varchar(100) DEFAULT NULL COMMENT 'Login de l''utilisateur professionnel qui a supprimé la saisie',
  `created_at` datetime DEFAULT NULL COMMENT 'Date de creation de la saisie',
  `updated_at` datetime DEFAULT NULL COMMENT 'Date de modification de la saisie, y compris suppression, restauration et changement de version',
  `deleted_at` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  `version_created_at` datetime DEFAULT NULL,
  `version_created_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`,`version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `a_saisies_version`
--


-- --------------------------------------------------------

--
-- Table structure for table `a_traitements`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Un traitement peut gerer plusieurs saisies et consiste à def' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `a_traitements`
--


-- --------------------------------------------------------

--
-- Table structure for table `a_types`
--

CREATE TABLE IF NOT EXISTS `a_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
  `nom` varchar(250) NOT NULL COMMENT 'Nom du type d''absence',
  `justification_exigible` tinyint(4) DEFAULT NULL COMMENT 'Ce type d''absence doit entrainer une justification de la part de la famille',
  `sous_responsabilite_etablissement` varchar(255) DEFAULT 'NON_PRECISE' COMMENT 'L''eleve est sous la responsabilite de l''etablissement. Typiquement : absence infirmerie, mettre la propriété à vrai car l''eleve est encore sous la responsabilité de l''etablissement. Possibilite : ''vrai''/''faux''/''non_precise''',
  `manquement_obligation_presence` varchar(50) DEFAULT 'NON_PRECISE' COMMENT 'L''eleve manque à ses obligations de presence (L''absence apparait sur le bulletin). Possibilite : ''vrai''/''faux''/''non_precise''',
  `retard_bulletin` varchar(50) DEFAULT 'NON_PRECISE' COMMENT 'La saisie est comptabilisée dans le bulletin en tant que retard. Possibilite : ''vrai''/''faux''/''non_precise''',
  `type_saisie` varchar(50) DEFAULT 'NON_PRECISE' COMMENT 'Enumeration des possibilités de l''interface de saisie de l''absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE',
  `commentaire` text COMMENT 'commentaire saisi par l''utilisateur',
  `id_lieu` int(11) DEFAULT NULL COMMENT 'cle etrangere du lieu ou se trouve l''élève',
  `sortable_rank` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `a_types_FI_1` (`id_lieu`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Liste des types d''absences possibles dans l''etablissement' AUTO_INCREMENT=14 ;

--
-- Dumping data for table `a_types`
--

INSERT INTO `a_types` (`id`, `nom`, `justification_exigible`, `sous_responsabilite_etablissement`, `manquement_obligation_presence`, `retard_bulletin`, `type_saisie`, `commentaire`, `id_lieu`, `sortable_rank`, `created_at`, `updated_at`) VALUES
(1, 'Absence scolaire', 1, 'FAUX', 'VRAI', 'NON_PRECISE', 'NON_PRECISE', 'L''élève n''est pas présent pour suivre sa scolarité.', NULL, 1, NULL, NULL),
(2, 'Retard intercours', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est en retard lors de l''intercours', NULL, 2, NULL, NULL),
(3, 'Retard extérieur', 0, 'FAUX', 'VRAI', 'VRAI', 'NON_PRECISE', 'L''élève est en retard lors de son arrivée dans l''etablissement', NULL, 3, NULL, NULL),
(4, 'Erreur de saisie', 0, 'NON_PRECISE', 'NON_PRECISE', 'NON_PRECISE', 'NON_PRECISE', 'Il y a probablement une erreur de saisie sur cet enregistrement.', NULL, 4, NULL, NULL),
(5, 'Infirmerie', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est à l''infirmerie.', NULL, 5, NULL, NULL),
(6, 'Sortie scolaire', 0, '1', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est en sortie scolaire.', NULL, 6, NULL, NULL),
(7, 'Exclusion de l''établissement', 0, 'FAUX', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est exclus de l''établissement.', NULL, 7, NULL, NULL),
(8, 'Exclusion/inclusion', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est exclus mais présent au sein de l''établissement.', NULL, 8, NULL, NULL),
(9, 'Exclusion de cours', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'DISCIPLINE', 'L''élève est exclus de cours.', NULL, 9, NULL, NULL),
(10, 'Dispense (eleve présent)', 1, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est dispensé mais présent physiquement lors de la seance.', NULL, 10, NULL, NULL),
(11, 'Dispense (élève non présent)', 1, 'FAUX', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est dispensé et non présent physiquement lors de la seance.', NULL, 11, NULL, NULL),
(12, 'Stage', 0, 'FAUX', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est en stage a l''extérieur de l''établissement.', NULL, 12, NULL, NULL),
(13, 'Présent', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est présent.', NULL, 13, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `a_types_statut`
--

CREATE TABLE IF NOT EXISTS `a_types_statut` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
  `id_a_type` int(11) NOT NULL COMMENT 'Cle etrangere de la table a_type',
  `statut` varchar(20) NOT NULL COMMENT 'Statut de l''utilisateur',
  PRIMARY KEY (`id`),
  KEY `a_types_statut_FI_1` (`id_a_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Liste des statuts autorises à saisir des types d''absences' AUTO_INCREMENT=40 ;

--
-- Dumping data for table `a_types_statut`
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
-- Table structure for table `cc_dev`
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
-- Dumping data for table `cc_dev`
--


-- --------------------------------------------------------

--
-- Table structure for table `cc_eval`
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
-- Dumping data for table `cc_eval`
--


-- --------------------------------------------------------

--
-- Table structure for table `cc_notes_eval`
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
-- Dumping data for table `cc_notes_eval`
--


-- --------------------------------------------------------

--
-- Table structure for table `classes`
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `classe`, `nom_complet`, `suivi_par`, `formule`, `format_nom`, `display_rang`, `display_address`, `display_coef`, `display_mat_cat`, `display_nbdev`, `display_moy_gen`, `modele_bulletin_pdf`, `rn_nomdev`, `rn_toutcoefdev`, `rn_coefdev_si_diff`, `rn_datedev`, `rn_sign_chefetab`, `rn_sign_pp`, `rn_sign_resp`, `rn_sign_nblig`, `rn_formule`, `ects_type_formation`, `ects_parcours`, `ects_code_parcours`, `ects_domaines_etude`, `ects_fonction_signataire_attestation`, `apb_niveau`) VALUES
(1, 'F1A', 'Form 1A', 'John Doe The Principal', '', 'cnp', 'y', 'n', 'y', 'n', 'n', 'y', '1', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 3, '', '', '', '', '', 'Principal', '');

-- --------------------------------------------------------

--
-- Table structure for table `cn_cahier_notes`
--

CREATE TABLE IF NOT EXISTS `cn_cahier_notes` (
  `id_cahier_notes` int(11) NOT NULL AUTO_INCREMENT,
  `id_groupe` int(11) NOT NULL,
  `periode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_cahier_notes`,`id_groupe`,`periode`),
  KEY `groupe_periode` (`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cn_cahier_notes`
--


-- --------------------------------------------------------

--
-- Table structure for table `cn_conteneurs`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cn_conteneurs`
--


-- --------------------------------------------------------

--
-- Table structure for table `cn_devoirs`
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
-- Dumping data for table `cn_devoirs`
--


-- --------------------------------------------------------

--
-- Table structure for table `cn_notes_conteneurs`
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
-- Dumping data for table `cn_notes_conteneurs`
--


-- --------------------------------------------------------

--
-- Table structure for table `cn_notes_devoirs`
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
-- Dumping data for table `cn_notes_devoirs`
--


-- --------------------------------------------------------

--
-- Table structure for table `commentaires_types`
--

CREATE TABLE IF NOT EXISTS `commentaires_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commentaire` text NOT NULL,
  `num_periode` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `commentaires_types`
--


-- --------------------------------------------------------

--
-- Table structure for table `commentaires_types_profs`
--

CREATE TABLE IF NOT EXISTS `commentaires_types_profs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `app` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `commentaires_types_profs`
--


-- --------------------------------------------------------

--
-- Table structure for table `communes`
--

CREATE TABLE IF NOT EXISTS `communes` (
  `code_commune_insee` varchar(50) NOT NULL,
  `departement` varchar(50) NOT NULL,
  `commune` varchar(255) NOT NULL,
  PRIMARY KEY (`code_commune_insee`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `communes`
--


-- --------------------------------------------------------

--
-- Table structure for table `ct_devoirs_documents`
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
-- Dumping data for table `ct_devoirs_documents`
--


-- --------------------------------------------------------

--
-- Table structure for table `ct_devoirs_entry`
--

CREATE TABLE IF NOT EXISTS `ct_devoirs_entry` (
  `id_ct` int(11) NOT NULL AUTO_INCREMENT,
  `id_groupe` int(11) NOT NULL,
  `date_ct` int(11) NOT NULL DEFAULT '0',
  `id_login` varchar(32) NOT NULL DEFAULT '',
  `id_sequence` int(11) NOT NULL DEFAULT '0',
  `contenu` text NOT NULL,
  `vise` char(1) NOT NULL DEFAULT 'n',
  `date_visibilite_eleve` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp prÃ©cisant quand les devoirs sont portÃ©s Ã  la conaissance des Ã©lÃ¨ves',
  PRIMARY KEY (`id_ct`),
  KEY `id_groupe` (`id_groupe`),
  KEY `groupe_date` (`id_groupe`,`date_ct`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ct_devoirs_entry`
--


-- --------------------------------------------------------

--
-- Table structure for table `ct_documents`
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
-- Dumping data for table `ct_documents`
--


-- --------------------------------------------------------

--
-- Table structure for table `ct_entry`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ct_entry`
--


-- --------------------------------------------------------

--
-- Table structure for table `ct_private_entry`
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
-- Dumping data for table `ct_private_entry`
--


-- --------------------------------------------------------

--
-- Table structure for table `ct_sequences`
--

CREATE TABLE IF NOT EXISTS `ct_sequences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ct_sequences`
--


-- --------------------------------------------------------

--
-- Table structure for table `ct_types_documents`
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
-- Dumping data for table `ct_types_documents`
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
-- Table structure for table `droits`
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
-- Dumping data for table `droits`
--

INSERT INTO `droits` (`id`, `administrateur`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `autre`, `description`, `statut`) VALUES
('/absences/index.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/absences/saisie_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/accueil_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/accueil_modules.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/accueil.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', '', ''),
('/aid/add_aid.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/config_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/export_csv_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/index2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/modify_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/aid/modify_aid_new.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', ''),
('/bulletin/edit.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1'),
('/bulletin/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1'),
('/bulletin/param_bull.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1'),
('/bulletin/verif_bulletins.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Vérification du remplissage des bulletins', ''),
('/bulletin/verrouillage.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'F', '(de)Verrouillage des périodes', ''),
('/cahier_notes_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des carnets de notes', ''),
('/cahier_notes/add_modif_conteneur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/add_modif_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/toutes_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/visu_releve_notes.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation et impression des relevés de notes', ''),
('/cahier_texte_admin/admin_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/cahier_texte_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/cahier_texte_admin/modify_limites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/cahier_texte_admin/modify_type_doc.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/cahier_texte/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte', '1'),
('/cahier_texte/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte', '1'),
('/classes/classes_ajout.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/classes_const.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/cpe_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des CPE aux classes', ''),
('/classes/duplicate_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/eleve_options.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/modify_nom_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/periodes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/prof_suivi.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/classes/scol_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des comptes scolarité aux classes', ''),
('/eleves/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', ''),
('/eleves/import_eleves_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', ''),
('/eleves/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', ''),
('/eleves/modify_eleve.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', ''),
('/etablissements/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', ''),
('/etablissements/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', ''),
('/etablissements/modify_etab.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', ''),
('/gestion/gestion_base_test.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'gestion données de test', ''),
('/groupes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes', ''),
('/groupes/add_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajout de groupes', ''),
('/groupes/edit_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition de groupes', ''),
('/groupes/edit_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des élèves des groupes', ''),
('/groupes/edit_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes de la classe', ''),
('/gestion/accueil_sauve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', ''),
('/gestion/savebackup.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Téléchargement de sauvegardes la base', ''),
('/gestion/efface_base.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', ''),
('/gestion/gestion_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des connexions', ''),
('/gestion/help_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/gestion/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/gestion/import_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/gestion/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/gestion/modify_impression.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des paramètres de la feuille de bienvenue', ''),
('/gestion/param_gen.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration générale', ''),
('/gestion/traitement_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_csv/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_csv/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l''année scolaire', ''),
('/init_scribe/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_scribe/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', ''),
('/init_lcs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/init_lcs/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/init_lcs/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/init_lcs/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/init_lcs/affectations.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l''année scolaire', ''),
('/lib/confirm_query.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', ''),
('/matieres/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', ''),
('/matieres/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', ''),
('/matieres/modify_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', ''),
('/matieres/matieres_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/matieres/matieres_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des catégories de matière', ''),
('/prepa_conseil/edit_limite.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', ''),
('/prepa_conseil/help.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', ''),
('/prepa_conseil/index1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Visualisation des notes et appréciations', '1'),
('/prepa_conseil/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', ''),
('/prepa_conseil/index3.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', ''),
('/prepa_conseil/visu_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Visualisation des notes et appréciations AID', ''),
('/prepa_conseil/visu_toutes_notes.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', ''),
('/responsables/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des responsables élèves', ''),
('/responsables/modify_resp.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des responsables élèves', ''),
('/saisie/help.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', ''),
('/saisie/import_class_csv.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', '', ''),
('/saisie/import_note_app.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', ''),
('/saisie/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', ''),
('/saisie/saisie_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes et appréciations AID', ''),
('/saisie/saisie_appreciations.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des appréciations du bulletins', ''),
('/saisie/ajax_appreciations.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Sauvegarde des appréciations du bulletins', ''),
('/saisie/saisie_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', ''),
('/saisie/saisie_avis1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', ''),
('/saisie/saisie_avis2.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', ''),
('/saisie/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes du bulletins', ''),
('/saisie/traitement_csv.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes du bulletins', ''),
('/utilisateurs/change_pwd.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/import_prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/impression_bienvenue.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/reset_passwords.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Réinitialisation des mots de passe', ''),
('/utilisateurs/modify_user.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', ''),
('/utilisateurs/mon_compte.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Gestion du compte (informations personnelles, mot de passe, ...)', ''),
('/visualisation/classe_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/eleve_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/evol_eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/evol_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/stats_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/classes/classes_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', ''),
('/fpdf/imprime_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', '', ''),
('/etablissements/import_etab_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', ''),
('/saisie/import_app_cons.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Importation csv des avis du conseil de classe', ''),
('/messagerie/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion de la messagerie', ''),
('/absences/import_absences_gep.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/absences/seq_gep_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/utilitaires/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Maintenance', ''),
('/gestion/contacter_admin.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', '', ''),
('/mod_absences/gestion/gestion_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/impression_absences_liste.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/impression_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/select.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/ajout_ret.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/ajout_dip.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/ajout_inf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/ajout_abs.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/bilan_absence.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/bilan.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/gestion/lettre_aux_parents.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', ''),
('/mod_absences/lib/tableau.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/lib/tableau_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_absences/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/edt_organisation/admin_periodes_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_absences/lib/liste_absences.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/lib/graphiques.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', '', ''),
('/mod_absences/professeurs/prof_ajout_abs.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajout des absences en classe', ''),
('/mod_absences/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des actions absences', ''),
('/mod_trombinoscopes/trombinoscopes.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'Visualiser le trombinoscope', ''),
('/mod_trombinoscopes/trombi_impr.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualiser le trombinoscope', ''),
('/mod_trombinoscopes/trombinoscopes_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du trombinoscope', ''),
('/cahier_notes/visu_toutes_notes2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des moyennes des carnets de notes', ''),
('/cahier_notes/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des moyennes des carnets de notes', ''),
('/utilitaires/verif_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Vérification des incohérences d appartenances à des groupes', ''),
('/referencement.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Référencement de Gepi sur la base centralisée des utilisateurs de Gepi', ''),
('/utilisateurs/tab_profs_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des matieres aux professeurs', ''),
('/matieres/matieres_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation des matières depuis un fichier CSV', ''),
('/groupes/edit_class_grp_lot.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des enseignements simples par lot.', ''),
('/groupes/visu_profs_class.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des équipes pédagogiques', ''),
('/groupes/popup.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des équipes pédagogiques', ''),
('/visualisation/affiche_eleve.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/visualisation/draw_graphe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', ''),
('/groupes/mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Accès aux CSV des listes d élèves', ''),
('/groupes/get_csv.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Génération de CSV élèves', ''),
('/visualisation/choix_couleurs.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choix des couleurs des graphiques des résultats scolaires', ''),
('/visualisation/couleur.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Choix d une couleur pour le graphique des résultats scolaires', ''),
('/gestion/config_prefs.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des préférences d utilisateurs', ''),
('/utilitaires/recalcul_moy_conteneurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction des moyennes des conteneurs', ''),
('/saisie/commentaires_types.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Saisie de commentaires-types', ''),
('/mod_absences/lib/fiche_eleve.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Fiche du suivie de l''élève', ''),
('/cahier_notes/releve_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Relevé de note au format PDF', ''),
('/impression/parametres_impression_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes PDF; réglage des paramètres', ''),
('/impression/impression_serie.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes (PDF) en série', ''),
('/impression/impression.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression rapide d une listes (PDF) ', ''),
('/impression/liste_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes (PDF)', ''),
('/init_xml/lecture_xml_sconet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/disciplines_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/lecture_xml_sts_emp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/save_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/responsables/maj_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', ''),
('/responsables/conversion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conversion des données responsables', ''),
('/utilisateurs/create_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création des utilisateurs au statut responsable', ''),
('/utilisateurs/create_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création des utilisateurs au statut responsable', ''),
('/utilisateurs/edit_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des utilisateurs au statut responsable', ''),
('/utilisateurs/edit_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des utilisateurs au statut élève', ''),
('/cahier_texte/consultation.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', ''),
('/cahier_texte/see_all.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', ''),
('/cahier_texte/visu_prof_jour.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Acces_a_son_cahier_de_textes_personnel', ''),
('/gestion/droits_acces.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Paramétrage des droits d accès', ''),
('/groupes/visu_profs_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation équipe pédagogique', ''),
('/saisie/impression_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression des avis trimestrielles des conseils de classe.', ''),
('/impression/avis_pdf.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression des avis trimestrielles des conseils de classe. Module PDF', ''),
('/impression/parametres_impression_pdf_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression des avis conseil classe PDF; reglage des parametres', ''),
('/utilisateurs/password_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Export des identifiants et mots de passe en csv', ''),
('/impression/password_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Impression des identifiants et des mots de passe en PDF', ''),
('/bulletin/buletin_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Report card scolaire au format PDF', ''),
('/mod_absences/gestion/etiquette_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Etiquette au format PDF', ''),
('/mod_absences/lib/export_csv.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Fichier d''exportation en csv des absences', ''),
('/mod_absences/gestion/statistiques.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Statistique du module vie scolaire', '1'),
('/mod_absences/lib/graph_camembert.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique camembert', ''),
('/mod_absences/lib/graph_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique camembert', ''),
('/edt_organisation/admin_horaire_ouverture.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des horaires d''ouverture de l''établissement', ''),
('/edt_organisation/admin_config_semaines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des types de semaines', ''),
('/mod_absences/gestion/fiche_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Fiche récapitulatif des absences', ''),
('/mod_absences/lib/graph_double_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique absence et retard sur le même graphique', ''),
('/bulletin/param_bull_pdf.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'page de gestion des parametres du bulletin pdf', ''),
('/bulletin/bulletin_pdf_avec_modele_classe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'page generant le bulletin pdf en fonction du modele affecte a la classe ', ''),
('/gestion/security_panel.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Panneau de controle des atteintes a la securite', ''),
('/gestion/security_policy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'definition des politiques de securite', ''),
('/mod_absences/gestion/alert_suivi.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'système d''alerte de suivi d''élève', ''),
('/gestion/efface_photos.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression des photos non associées à des élèves', ''),
('/responsables/gerer_adr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des adresses de responsables', ''),
('/responsables/choix_adr_existante.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choix adresse de responsable existante', ''),
('/cahier_notes/export_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export CSV/ODS du cahier de notes', ''),
('/cahier_notes/import_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Import CSV du cahier de notes', ''),
('/gestion/options_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Options de connexions', ''),
('/eleves/add_eleve.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', ''),
('/saisie/export_class_ods.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export ODS des notes/appréciations', ''),
('/gestion/gestion_temp_dir.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des dossiers temporaires d utilisateurs', ''),
('/gestion/param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des couleurs pour Gepi', ''),
('/utilisateurs/creer_remplacant.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'script de création d un remplaçant', ''),
('/mod_absences/gestion/lettre_pdf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Publipostage des lettres d absences PDF', '1'),
('/accueil_simpl_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Page d accueil simplifiée pour les profs', ''),
('/init_xml2/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/init_xml2/clean_temp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/mod_annees_anterieures/conservation_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conservation des données antérieures', ''),
('/mod_annees_anterieures/consultation_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des données d années antérieures', ''),
('/mod_annees_anterieures/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Index données antérieures', ''),
('/mod_annees_anterieures/popup_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des données antérieures', ''),
('/mod_annees_anterieures/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Activation/désactivation du module données antérieures', ''),
('/mod_annees_anterieures/nettoyer_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression de données antérieures', ''),
('/responsables/maj_import1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', ''),
('/responsables/maj_import2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', ''),
('/mod_annees_anterieures/corriger_ine.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction d INE dans la table annees_anterieures', ''),
('/mod_annees_anterieures/liste_eleves_ajax.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Recherche d élèves', ''),
('/mod_absences/lib/graph_double_ligne_fiche.php', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'F', 'Graphique de la fiche élève', '1'),
('/edt_organisation/edt_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation du calendrier', ''),
('/edt_organisation/index_edt.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des emplois du temps', ''),
('/edt_organisation/edt_initialiser.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation des emplois du temps', ''),
('/edt_organisation/effacer_cours.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Effacer un cours des EdT', ''),
('/edt_organisation/ajouter_salle.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des salles', ''),
('/edt_organisation/edt_parametrer.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les paramètres EdT', ''),
('/edt_organisation/voir_groupe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Voir les groupes de Gepi', ''),
('/edt_organisation/modif_edt_tempo.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modification temporaire des EdT', ''),
('/edt_organisation/edt_init_xml.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation EdT par xml', ''),
('/edt_organisation/edt_init_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par csv', ''),
('/edt_organisation/edt_init_csv2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un autre csv', ''),
('/edt_organisation/edt_init_texte.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un fichier texte', ''),
('/edt_organisation/edt_init_concordance.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un fichier texte', ''),
('/edt_organisation/edt_init_concordance2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un autre fichier csv', ''),
('/edt_organisation/modifier_cours.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Modifier un cours', ''),
('/edt_organisation/modifier_cours_popup.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Modifier un cours', ''),
('/edt_organisation/edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Régler le module emploi du temps', ''),
('/edt_organisation/edt_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Régler le module emploi du temps', ''),
('/edt_organisation/edt_param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Régler les couleurs des matières (EdT)', ''),
('/edt_organisation/ajax_edtcouleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Changer les couleurs des matières (EdT)', ''),
('/absences/import_absences_sconet.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', ''),
('/mod_absences/admin/admin_config_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définir les différentes périodes', ''),
('/bulletin/export_modele_pdf.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'exportation en csv des modeles de bulletin pdf', ''),
('/absences/consulter_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Consulter les absences', ''),
('/mod_absences/professeurs/bilan_absences_professeur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences pour chaque professeur', ''),
('/mod_absences/professeurs/bilan_absences_classe.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences pour chaque professeur', ''),
('/mod_absences/gestion/voir_absences_viescolaire.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences du jour', ''),
('/mod_absences/gestion/bilan_absences_quotidien.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par créneau', ''),
('/mod_absences/gestion/bilan_absences_quotidien_pdf.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par créneau en pdf', ''),
('/mod_absences/gestion/bilan_absences_classe.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par classe', ''),
('/mod_absences/gestion/bilan_repas_quotidien.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter l inscription aux repas', ''),
('/mod_absences/absences.php', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'F', 'Consulter les absences de son enfant', ''),
('/mod_absences/admin/interface_abs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Paramétrer les interfaces des professeurs', ''),
('/absences/import_absences_gepi.php', 'F', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Page d''importation des absences de gepi mod_absences', '1'),
('/lib/change_mode_header.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Page AJAX pour changer la variable cacher_header', '1'),
('/saisie/recopie_moyennes.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Recopie des moyennes', ''),
('/groupes/fusion_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fusionner des groupes', ''),
('/gestion/security_panel_archives.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'page archive du panneau de sécurité', ''),
('/lib/header_barre_menu.php/', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Barre horizontale de menu', ''),
('/responsables/corrige_ele_id.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction des ELE_ID d apres Sconet', ''),
('/mod_inscription/inscription_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '(De)activation du module inscription', ''),
('/mod_inscription/inscription_index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'accès au module configuration', ''),
('/mod_inscription/inscription_config.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration du module inscription', ''),
('/mod_inscription/help.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration du module inscription', ''),
('/aid/index_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Outils complémentaires de gestion des AIDs', ''),
('/aid/visu_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Outils complémentaires de gestion des AIDs', ''),
('/aid/modif_fiches.php', 'V', 'V', 'V', 'F', 'V', 'V', 'F', 'F', 'Outils complémentaires de gestion des AIDs', ''),
('/aid/config_aid_fiches_projet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', ''),
('/aid/config_aid_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', ''),
('/aid/config_aid_productions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', ''),
('/aid/annees_anterieures_accueil.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Configuration des AID', ''),
('/classes/acces_appreciations.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration de la restriction d accès aux appréciations pour les élèves et responsables', ''),
('/mod_notanet/notanet_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion du module NOTANET', ''),
('/mod_notanet/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Accueil', ''),
('/mod_notanet/extract_moy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Extraction des moyennes', ''),
('/mod_notanet/corrige_extract_moy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Extraction des moyennes', ''),
('/mod_notanet/select_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Associations élèves/type de brevet', ''),
('/mod_notanet/select_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Associations matières/type de brevet', ''),
('/mod_notanet/saisie_app.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Saisie des appréciations', ''),
('/mod_notanet/generer_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Génération de CSV', ''),
('/mod_notanet/choix_generation_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Génération de CSV', ''),
('/mod_notanet/verrouillage_saisie_app.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: (Dé)Verrouillage des saisies', ''),
('/bulletin/bull_index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1'),
('/cahier_notes/visu_releve_notes_bis.php', 'F', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Relevé de notes', '1'),
('/cahier_notes/param_releve_html.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Paramètres du relevé de notes', '1'),
('/utilisateurs/creer_statut.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Créer des statuts personnalisés', ''),
('/utilisateurs/creer_statut_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Autoriser la création des statuts personnalisés', ''),
('/classes/changement_eleve_classe.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Changement de classe pour un élève', '1'),
('/edt_gestion_gr/edt_aff_gr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/edt_gestion_gr/edt_ajax_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/edt_gestion_gr/edt_liste_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/edt_gestion_gr/edt_liste_profs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/edt_gestion_gr/edt_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', ''),
('/mod_notanet/saisie_avis.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Saisie avis chef etablissement', ''),
('/mod_notanet/saisie_b2i_a2.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Saisie socles B2i et A2', ''),
('/mod_notanet/poitiers/fiches_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Accès à l export NOTANET', ''),
('/mod_notanet/poitiers/param_fiche_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Paramètres d impression', ''),
('/mod_notanet/rouen/fiches_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Accès à l export NOTANET', ''),
('/eleves/liste_eleves.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Lister_des_eleves', ''),
('/eleves/visu_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Consultation_d_un_eleve', ''),
('/cahier_texte_admin/rss_cdt_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gerer les flux rss du cdt', ''),
('/matieres/suppr_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression d une matiere', ''),
('/mod_annees_anterieures/archivage_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches projets', '1'),
('/eleves/import_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation bulletin élève', ''),
('/eleves/export_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Exportation bulletin élève', ''),
('/mod_ent/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', ''),
('/mod_ent/gestion_ent_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', ''),
('/mod_ent/gestion_ent_profs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', ''),
('/mod_ent/miseajour_ent_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', ''),
('/cahier_texte_admin/visa_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page de signature des cahiers de texte', ''),
('/public/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', ''),
('/saisie/saisie_cmnt_type_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie appréciations-types pour les profs', ''),
('/mod_discipline/traiter_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Traitement', ''),
('/mod_discipline/saisie_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie incident', ''),
('/mod_discipline/occupation_lieu_heure.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Occupation lieu', ''),
('/mod_discipline/liste_sanctions_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Liste', ''),
('/mod_discipline/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Index', ''),
('/mod_discipline/incidents_sans_protagonistes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Incidents sans protagonistes', ''),
('/mod_discipline/edt_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: EDT élève', ''),
('/mod_discipline/ajout_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajout sanction', ''),
('/mod_discipline/saisie_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie sanction', ''),
('/mod_discipline/definir_roles.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définition des rôles', ''),
('/mod_discipline/definir_mesures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définition des mesures', ''),
('/mod_discipline/sauve_role.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg rôle incident', ''),
('/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir types sanctions', ''),
('/mod_discipline/liste_retenues_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Liste des retenues du jour', ''),
('/mod_discipline/avertir_famille.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', ''),
('/mod_discipline/avertir_famille_html.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', ''),
('/mod_discipline/sauve_famille_avertie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg famille avertie', ''),
('/mod_discipline/discipline_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Activation/desactivation du module', ''),
('/classes/classes_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page appelée via ajax.', ''),
('/saisie/saisie_secours_eleve.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie notes/appréciations pour un élève en compte secours', ''),
('/responsables/dedoublonnage_adresses.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Dédoublonnage des adresses responsables', ''),
('/mod_ooo/rapport_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Rapport Incident', ''),
('/mod_ooo/gerer_modeles_ooo.php', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Gérer et utiliser les modèles', ''),
('/mod_ooo/ooo_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Admin', ''),
('/mod_ooo/retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Retenue', ''),
('/mod_ooo/formulaire_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : formulaire retenue', ''),
('/mod_ooo/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo: Index : Index', ''),
('/mod_discipline/update_colonne_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Affichage d une imprimante pour le responsable d un incident', ''),
('/mod_discipline/definir_lieux.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les lieux', ''),
('/mod_notanet/fb_rouen_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Rouen', ''),
('/mod_notanet/fb_montpellier_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Montpellier', ''),
('/mod_genese_classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Accueil', ''),
('/mod_genese_classes/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Activation/désactivation', ''),
('/mod_genese_classes/select_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Choix des options', ''),
('/mod_genese_classes/select_eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Choix des options des élèves', ''),
('/mod_genese_classes/select_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Choix des classes', ''),
('/mod_genese_classes/saisie_contraintes_opt_classe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Saisie des contraintes options/classes', ''),
('/mod_genese_classes/liste_classe_fut.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Liste des classes futures (appel ajax)', ''),
('/mod_genese_classes/affiche_listes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Affichage de listes', ''),
('/mod_genese_classes/genere_ods.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Génération d un fichier ODS de listes', ''),
('/mod_genese_classes/affect_eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Affectation des élèves', ''),
('/mod_genese_classes/select_arriv_red.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Sélection des arrivants/redoublants', ''),
('/mod_genese_classes/liste_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Liste des options de classes existantes', ''),
('/mod_genese_classes/import_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Import options depuis CSV', ''),
('/eleves/import_communes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Import des communes de naissance', ''),
('/mod_notanet/fb_lille_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Lille', ''),
('/mod_notanet/fb_creteil_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Creteil', ''),
('/mod_plugins/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajouter/enlever des plugins', ''),
('/saisie/export_cmnt_type_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Export appréciations-types pour les profs', ''),
('/mod_discipline/disc_stat.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Statistiques', ''),
('/mod_epreuve_blanche/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Epreuves blanches: Activation/désactivation du module', ''),
('/mod_examen_blanc/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Examens blancs: Activation/désactivation du module', ''),
('/mod_epreuve_blanche/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Accueil', ''),
('/mod_epreuve_blanche/transfert_cn.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Transfert vers carnet de notes', ''),
('/mod_epreuve_blanche/saisie_notes.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Saisie des notes', ''),
('/mod_epreuve_blanche/genere_emargement.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Génération émargement', ''),
('/mod_epreuve_blanche/definir_salles.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Définir les salles', ''),
('/mod_epreuve_blanche/attribuer_copies.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Attribuer les copies aux professeurs', ''),
('/mod_epreuve_blanche/bilan.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Bilan', ''),
('/mod_epreuve_blanche/genere_etiquettes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Génération étiquettes', ''),
('/mod_examen_blanc/saisie_notes.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Examen blanc: Saisie devoir hors enseignement', ''),
('/mod_examen_blanc/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Examen blanc: Accueil', ''),
('/mod_examen_blanc/releve.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Examen blanc: Relevé', ''),
('/mod_examen_blanc/bull_exb.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Examen blanc: Report cards', ''),
('/saisie/saisie_synthese_app_classe.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Synthèse des appréciations sur le groupe classe.', ''),
('/gestion/saisie_message_connexion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie de messages de connexion.', ''),
('/groupes/repartition_ele_grp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Répartir des élèves dans des groupes', ''),
('/prepa_conseil/edit_limite_bis.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', ''),
('/prepa_conseil/index2bis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', ''),
('/prepa_conseil/index3bis.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', ''),
('/prepa_conseil/visu_toutes_notes_bis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', ''),
('/utilitaires/import_pays.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Import des pays', ''),
('/mod_apb/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion du module Admissions PostBac', '');
INSERT INTO `droits` (`id`, `administrateur`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `autre`, `description`, `statut`) VALUES
('/mod_apb/index.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'Export XML pour le système Admissions Post-Bac', ''),
('/mod_apb/export_xml.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'Export XML pour le système Admissions Post-Bac', ''),
('/mod_gest_aid/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestionnaires AID', ''),
('/saisie/ajax_edit_limite.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', ''),
('/mod_discipline/check_nature_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Discipline: Recherche de natures d incident', ''),
('/groupes/signalement_eleves.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Groupes: signalement des erreurs d affectation élève', ''),
('/bulletin/envoi_mail.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Envoi de mail via ajax', ''),
('/mod_discipline/destinataires_alertes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Parametrage des destinataires de mail d alerte', ''),
('/init_scribe_ng/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - index', ''),
('/init_scribe_ng/etape1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 1', ''),
('/init_scribe_ng/etape2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 2', ''),
('/init_scribe_ng/etape3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 3', ''),
('/init_scribe_ng/etape4.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 4', ''),
('/init_scribe_ng/etape5.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 5', ''),
('/init_scribe_ng/etape6.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 6', ''),
('/init_scribe_ng/etape7.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 7', ''),
('/mod_ects/ects_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Module ECTS : Admin', ''),
('/mod_ects/index_saisie.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Accueil saisie', ''),
('/mod_ects/saisie_ects.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Saisie', ''),
('/mod_ects/edition.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Edition des documents', ''),
('/mod_ooo/documents_ects.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Génération des documents', ''),
('/mod_ects/recapitulatif.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Recapitulatif globaux', ''),
('/mod_discipline/stats2/index.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Module discipline: Statistiques', ''),
('/mod_discipline/definir_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les catégories', ''),
('/mod_abs2/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_abs2/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_abs2/admin/admin_types_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_abs2/admin/admin_lieux_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_abs2/admin/admin_justifications_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_abs2/admin/admin_table_agregation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_abs2/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', ''),
('/mod_abs2/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Administration du module absences', ''),
('/mod_abs2/saisir_groupe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Affichage du formulaire de saisie de absences', ''),
('/mod_abs2/absences_du_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Affichage des absences du jour', ''),
('/mod_abs2/enregistrement_saisie_groupe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Enregistrement des saisies d un groupe', ''),
('/mod_abs2/liste_saisies.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Liste des saisies', ''),
('/mod_abs2/liste_traitements.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Liste des traitements', ''),
('/mod_abs2/liste_notifications.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Liste des notifications', ''),
('/mod_abs2/liste_saisies_selection_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Liste des saisits pour faire les traitement', ''),
('/mod_abs2/visu_saisie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Visualisation d une saisies', ''),
('/mod_abs2/visu_traitement.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Visualisation d une saisie', ''),
('/mod_abs2/visu_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualisation d une notification', ''),
('/mod_abs2/enregistrement_modif_saisie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Modification d une saisies', ''),
('/mod_abs2/enregistrement_modif_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Modification d un traitement', ''),
('/mod_abs2/enregistrement_modif_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Modification d une notification', ''),
('/mod_abs2/generer_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'generer une notification', ''),
('/mod_abs2/saisir_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'V', 'Saisir l absence d un eleve', ''),
('/mod_abs2/enregistrement_saisie_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'V', 'Enregistrer absence d un eleve', ''),
('/mod_abs2/creation_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Crer un traitement', ''),
('/mod_discipline/saisie_incident_abs2.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Saisir un incident relatif a une absence', ''),
('/mod_abs2/tableau_des_appels.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualisation du tableaux des saisies', ''),
('/mod_abs2/bilan_du_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualisation du bilan du jour', ''),
('/mod_abs2/extraction_saisies.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Extraction des saisies', ''),
('/mod_abs2/extraction_demi-journees.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Extraction des saisies', ''),
('/mod_abs2/ajax_edt_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Affichage edt', ''),
('/mod_abs2/generer_notifications_par_lot.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Génération groupée des courriers', ''),
('/mod_abs2/bilan_individuel.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Bilan individuel des absences eleve', ''),
('/mod_abs2/totaux_du_jour.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'V', 'Totaux du jour des absences', ''),
('/mod_abs2/statistiques.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Statistiques des absences', ''),
('/bulletin/autorisation_exceptionnelle_saisie_app.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Autorisation exceptionnelle de saisie d appréciation', ''),
('/init_csv/export_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV: Export tables', ''),
('/cahier_texte_2/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_edition_compte_rendu.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_edition_notice_privee.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_duplication_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_affichage_duplication_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_deplacement_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_affichage_deplacement_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_suppression_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_enregistrement_compte_rendu.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_enregistrement_notice_privee.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_edition_devoir.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_enregistrement_devoir.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_affichages_liste_notices.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/ajax_affichage_dernieres_notices.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/exportcsv.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1'),
('/cahier_texte_2/consultation.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation des cahiers de textes', ''),
('/cahier_texte_2/see_all.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', ''),
('/cahier_texte_2/creer_sequence.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte - sequences', '1'),
('/cahier_texte_2/creer_seq_ajax_step1.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte - sequences', '1'),
('/mod_trombinoscopes/trombino_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Trombinoscopes PDF', ''),
('/mod_trombinoscopes/trombino_decoupe.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Génération d une grille PDF pour les trombinoscopes,...', ''),
('/groupes/menage_eleves_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Groupes: Desinscription des eleves sans notes ni appreciations', ''),
('/statistiques/export_donnees_bulletins.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Export de données des bulletins', ''),
('/statistiques/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Statistiques: Index', ''),
('/statistiques/classes_effectifs.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Statistiques: classe, effectifs', ''),
('/mod_annees_anterieures/ajax_bulletins.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'V', 'Accès aux bulletins d années antérieures', ''),
('/mod_annees_anterieures/ajax_signaler_faute.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Possibilité de signaler une faute de frappe dans une appréciation', ''),
('/eleves/ajax_modif_eleve.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Enregistrement des modifications élève', ''),
('/classes/ajouter_periode.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Classes: Ajouter des périodes', ''),
('/classes/supprimer_periode.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Classes: Supprimer des périodes', ''),
('/groupes/visu_mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Accès aux listes d élèves', ''),
('/cahier_notes/index_cc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/add_modif_cc_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/add_modif_cc_eval.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/saisie_notes_cc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/cahier_notes/visu_cc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1'),
('/responsables/synchro_mail.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Synchronisation des mail responsables', ''),
('/eleves/synchro_mail.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Synchronisation des mail élèves', ''),
('/cahier_texte_2/archivage_cdt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Archivage des CDT', ''),
('/documents/archives/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Archives des CDT', ''),
('/saisie/saisie_vocabulaire.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie de vocabulaire', ''),
('/mod_epreuve_blanche/genere_liste_affichage.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Génération liste affichage', ''),
('/cahier_texte_2/ajax_devoirs_classe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Cahiers de textes : Devoirs d une classe pour tel jour', ''),
('/cahier_texte_2/ajax_liste_notices_privees.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahiers de textes : Liste des notices privées', ''),
('/mod_ooo/publipostage_ooo.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Modèle Ooo : Publipostage', ''),
('/saisie/saisie_mentions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie de mentions', ''),
('/mod_discipline/visu_disc.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Discipline: Accès élève/parent', ''),
('/mod_discipline/definir_natures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les natures', ''),
('/init_xml2/traite_csv_udt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Import des enseignements via un Export CSV UDT', ''),
('/init_xml2/init_alternatif.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', ''),
('/mod_examen_blanc/copie_exam.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Examen blanc: Copie', ''),
('/mod_sso_table/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de la table de correspondance sso', ''),
('/gestion/changement_d_annee.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Changement d''année.', '');

-- --------------------------------------------------------

--
-- Table structure for table `droits_aid`
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
-- Dumping data for table `droits_aid`
--

INSERT INTO `droits_aid` (`id`, `public`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `description`, `statut`) VALUES
('nom', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('numero', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('perso1', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('perso2', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('productions', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Production', '1'),
('resume', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Résumé', '1'),
('famille', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Famille', '1'),
('mots_cles', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Mots clés', '1'),
('adresse1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Adresse publique', '1'),
('adresse2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Adresse privée', '1'),
('public_destinataire', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Public destinataire', '1'),
('contacts', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Contacts, ressources', '1'),
('divers', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Divers', '1'),
('matiere1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Discipline principale', '1'),
('matiere2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Discipline secondaire', '1'),
('eleve_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A préciser', '1'),
('cpe_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A préciser', '1'),
('prof_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A préciser', '0'),
('fiche_publique', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('affiche_adresse1', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('en_construction', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1'),
('perso3', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'A préciser', '0');

-- --------------------------------------------------------

--
-- Table structure for table `droits_speciaux`
--

CREATE TABLE IF NOT EXISTS `droits_speciaux` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_statut` int(11) NOT NULL,
  `nom_fichier` varchar(200) NOT NULL,
  `autorisation` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `droits_speciaux`
--


-- --------------------------------------------------------

--
-- Table structure for table `droits_statut`
--

CREATE TABLE IF NOT EXISTS `droits_statut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_statut` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `droits_statut`
--


-- --------------------------------------------------------

--
-- Table structure for table `droits_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `droits_utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_statut` int(11) NOT NULL,
  `login_user` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `droits_utilisateurs`
--


-- --------------------------------------------------------

--
-- Table structure for table `eb_copies`
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
-- Dumping data for table `eb_copies`
--


-- --------------------------------------------------------

--
-- Table structure for table `eb_epreuves`
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
-- Dumping data for table `eb_epreuves`
--


-- --------------------------------------------------------

--
-- Table structure for table `eb_groupes`
--

CREATE TABLE IF NOT EXISTS `eb_groupes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_epreuve` int(11) unsigned NOT NULL,
  `id_groupe` int(11) unsigned NOT NULL,
  `transfert` varchar(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eb_groupes`
--


-- --------------------------------------------------------

--
-- Table structure for table `eb_profs`
--

CREATE TABLE IF NOT EXISTS `eb_profs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_epreuve` int(11) unsigned NOT NULL,
  `login_prof` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eb_profs`
--


-- --------------------------------------------------------

--
-- Table structure for table `eb_salles`
--

CREATE TABLE IF NOT EXISTS `eb_salles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `salle` varchar(255) NOT NULL,
  `id_epreuve` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eb_salles`
--


-- --------------------------------------------------------

--
-- Table structure for table `ects_credits`
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
-- Dumping data for table `ects_credits`
--


-- --------------------------------------------------------

--
-- Table structure for table `ects_global_credits`
--

CREATE TABLE IF NOT EXISTS `ects_global_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL COMMENT 'Identifiant de l''eleve',
  `mention` varchar(255) NOT NULL COMMENT 'Mention obtenue',
  PRIMARY KEY (`id`,`id_eleve`),
  KEY `ects_global_credits_FI_1` (`id_eleve`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ects_global_credits`
--


-- --------------------------------------------------------

--
-- Table structure for table `edt_calendrier`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `edt_calendrier`
--


-- --------------------------------------------------------

--
-- Table structure for table `edt_classes`
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
-- Dumping data for table `edt_classes`
--


-- --------------------------------------------------------

--
-- Table structure for table `edt_cours`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `edt_cours`
--


-- --------------------------------------------------------

--
-- Table structure for table `edt_creneaux`
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `edt_creneaux`
--

INSERT INTO `edt_creneaux` (`id_definie_periode`, `nom_definie_periode`, `heuredebut_definie_periode`, `heurefin_definie_periode`, `suivi_definie_periode`, `type_creneaux`, `jour_creneau`) VALUES
(1, 'M1', '08:00:00', '08:55:00', 1, 'cours', ''),
(2, 'M2', '08:55:00', '09:50:00', 1, 'cours', ''),
(3, 'M3', '10:05:00', '11:00:00', 1, 'cours', ''),
(4, 'M4', '11:00:00', '11:55:00', 1, 'cours', ''),
(5, 'M5', '11:55:00', '12:30:00', 1, 'cours', ''),
(6, 'S1', '13:30:00', '14:25:00', 1, 'cours', ''),
(7, 'S2', '14:25:00', '15:20:00', 1, 'cours', ''),
(8, 'S3', '15:35:00', '16:30:00', 1, 'cours', ''),
(9, 'S4', '16:30:00', '17:30:00', 1, 'cours', ''),
(10, 'S5', '17:30:00', '18:25:00', 1, 'cours', ''),
(11, 'P1', '09:50:00', '10:05:00', 1, 'pause', ''),
(12, 'P2', '15:20:00', '15:35:00', 1, 'pause', ''),
(13, 'R', '12:00:00', '13:00:00', 1, 'repas', ''),
(14, 'R1', '13:00:00', '13:30:00', 1, 'pause', '');

-- --------------------------------------------------------

--
-- Table structure for table `edt_creneaux_bis`
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
-- Dumping data for table `edt_creneaux_bis`
--


-- --------------------------------------------------------

--
-- Table structure for table `edt_dates_special`
--

CREATE TABLE IF NOT EXISTS `edt_dates_special` (
  `id_edt_date_special` int(11) NOT NULL AUTO_INCREMENT,
  `nom_edt_date_special` varchar(200) NOT NULL,
  `debut_edt_date_special` date NOT NULL,
  `fin_edt_date_special` date NOT NULL,
  PRIMARY KEY (`id_edt_date_special`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `edt_dates_special`
--


-- --------------------------------------------------------

--
-- Table structure for table `edt_init`
--

CREATE TABLE IF NOT EXISTS `edt_init` (
  `id_init` int(11) NOT NULL AUTO_INCREMENT,
  `ident_export` varchar(100) NOT NULL,
  `nom_export` varchar(200) NOT NULL,
  `nom_gepi` varchar(200) NOT NULL,
  PRIMARY KEY (`id_init`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `edt_init`
--


-- --------------------------------------------------------

--
-- Table structure for table `edt_semaines`
--

CREATE TABLE IF NOT EXISTS `edt_semaines` (
  `id_edt_semaine` int(11) NOT NULL AUTO_INCREMENT,
  `num_edt_semaine` int(11) NOT NULL DEFAULT '0',
  `type_edt_semaine` varchar(10) NOT NULL DEFAULT '',
  `num_semaines_etab` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_edt_semaine`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

--
-- Dumping data for table `edt_semaines`
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
-- Table structure for table `edt_setting`
--

CREATE TABLE IF NOT EXISTS `edt_setting` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `reglage` varchar(30) NOT NULL,
  `valeur` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `edt_setting`
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
-- Table structure for table `eleves`
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
  `date_sortie` datetime DEFAULT NULL COMMENT 'Timestamp de sortie de l''élève de l''établissement (fin d''inscription)',
  `mef_code` bigint(20) DEFAULT NULL COMMENT 'code mef de la formation de l''eleve',
  PRIMARY KEY (`id_eleve`),
  KEY `eleves_FI_1` (`mef_code`),
  KEY `I_referenced_j_eleves_classes_FK_1_1` (`login`),
  KEY `I_referenced_responsables2_FK_1_2` (`ele_id`),
  KEY `I_referenced_archivage_ects_FK_1_3` (`no_gep`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Liste des eleves de l''etablissement' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eleves`
--


-- --------------------------------------------------------

--
-- Table structure for table `eleves_groupes_settings`
--

CREATE TABLE IF NOT EXISTS `eleves_groupes_settings` (
  `login` varchar(50) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`id_groupe`,`login`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eleves_groupes_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `etablissements`
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
-- Dumping data for table `etablissements`
--

INSERT INTO `etablissements` (`id`, `nom`, `niveau`, `type`, `cp`, `ville`) VALUES
('999', 'étranger', 'aucun', 'aucun', 999, '');

-- --------------------------------------------------------

--
-- Table structure for table `etiquettes_formats`
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
-- Dumping data for table `etiquettes_formats`
--

INSERT INTO `etiquettes_formats` (`id_etiquette_format`, `nom_etiquette_format`, `xcote_etiquette_format`, `ycote_etiquette_format`, `espacementx_etiquette_format`, `espacementy_etiquette_format`, `largeur_etiquette_format`, `hauteur_etiquette_format`, `nbl_etiquette_format`, `nbh_etiquette_format`) VALUES
(1, 'Avery - A4 - 63,5 x 33,9 mm', 2, 2, 5, 5, 63.5, 33, 3, 8);

-- --------------------------------------------------------

--
-- Table structure for table `ex_classes`
--

CREATE TABLE IF NOT EXISTS `ex_classes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_exam` int(11) unsigned NOT NULL,
  `id_classe` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ex_classes`
--


-- --------------------------------------------------------

--
-- Table structure for table `ex_examens`
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
-- Dumping data for table `ex_examens`
--


-- --------------------------------------------------------

--
-- Table structure for table `ex_groupes`
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
-- Dumping data for table `ex_groupes`
--


-- --------------------------------------------------------

--
-- Table structure for table `ex_matieres`
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
-- Dumping data for table `ex_matieres`
--


-- --------------------------------------------------------

--
-- Table structure for table `ex_notes`
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
-- Dumping data for table `ex_notes`
--


-- --------------------------------------------------------

--
-- Table structure for table `gc_affichages`
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
-- Dumping data for table `gc_affichages`
--


-- --------------------------------------------------------

--
-- Table structure for table `gc_divisions`
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
-- Dumping data for table `gc_divisions`
--


-- --------------------------------------------------------

--
-- Table structure for table `gc_eleves_options`
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
-- Dumping data for table `gc_eleves_options`
--


-- --------------------------------------------------------

--
-- Table structure for table `gc_ele_arriv_red`
--

CREATE TABLE IF NOT EXISTS `gc_ele_arriv_red` (
  `login` varchar(255) NOT NULL,
  `statut` enum('Arriv','Red') NOT NULL,
  `projet` varchar(255) NOT NULL,
  PRIMARY KEY (`login`,`projet`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gc_ele_arriv_red`
--


-- --------------------------------------------------------

--
-- Table structure for table `gc_options`
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
-- Dumping data for table `gc_options`
--


-- --------------------------------------------------------

--
-- Table structure for table `gc_options_classes`
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
-- Dumping data for table `gc_options_classes`
--


-- --------------------------------------------------------

--
-- Table structure for table `gc_projets`
--

CREATE TABLE IF NOT EXISTS `gc_projets` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `projet` varchar(255) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `gc_projets`
--

INSERT INTO `gc_projets` (`id`, `projet`, `commentaire`) VALUES
(1, 'Sasseproject', '');

-- --------------------------------------------------------

--
-- Table structure for table `groupes`
--

CREATE TABLE IF NOT EXISTS `groupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `recalcul_rang` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_name` (`id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `groupes`
--


-- --------------------------------------------------------

--
-- Table structure for table `horaires_etablissement`
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
-- Dumping data for table `horaires_etablissement`
--

INSERT INTO `horaires_etablissement` (`id_horaire_etablissement`, `date_horaire_etablissement`, `jour_horaire_etablissement`, `ouverture_horaire_etablissement`, `fermeture_horaire_etablissement`, `pause_horaire_etablissement`, `ouvert_horaire_etablissement`) VALUES
(1, '0000-00-00', 'lundi', '08:00:00', '17:30:00', '00:45:00', 1),
(2, '0000-00-00', 'mardi', '08:00:00', '17:30:00', '00:45:00', 1),
(3, '0000-00-00', 'mercredi', '08:00:00', '12:00:00', '00:00:00', 1),
(4, '0000-00-00', 'jeudi', '08:00:00', '17:30:00', '00:45:00', 1),
(5, '0000-00-00', 'vendredi', '08:00:00', '17:30:00', '00:45:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `infos_actions`
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
-- Dumping data for table `infos_actions`
--


-- --------------------------------------------------------

--
-- Table structure for table `infos_actions_destinataires`
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
-- Dumping data for table `infos_actions_destinataires`
--


-- --------------------------------------------------------

--
-- Table structure for table `inscription_items`
--

CREATE TABLE IF NOT EXISTS `inscription_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(10) NOT NULL DEFAULT '',
  `heure` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `inscription_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `inscription_j_login_items`
--

CREATE TABLE IF NOT EXISTS `inscription_j_login_items` (
  `login` varchar(20) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inscription_j_login_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_aidcateg_super_gestionnaires`
--

CREATE TABLE IF NOT EXISTS `j_aidcateg_super_gestionnaires` (
  `indice_aid` int(11) NOT NULL,
  `id_utilisateur` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_aidcateg_super_gestionnaires`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_aidcateg_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `j_aidcateg_utilisateurs` (
  `indice_aid` int(11) NOT NULL,
  `id_utilisateur` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_aidcateg_utilisateurs`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_aid_eleves`
--

CREATE TABLE IF NOT EXISTS `j_aid_eleves` (
  `id_aid` varchar(100) NOT NULL DEFAULT '',
  `login` varchar(60) NOT NULL DEFAULT '',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_aid`,`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_aid_eleves`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_aid_eleves_resp`
--

CREATE TABLE IF NOT EXISTS `j_aid_eleves_resp` (
  `id_aid` varchar(100) NOT NULL DEFAULT '',
  `login` varchar(60) NOT NULL DEFAULT '',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_aid`,`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_aid_eleves_resp`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_aid_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `j_aid_utilisateurs` (
  `id_aid` varchar(100) NOT NULL DEFAULT '',
  `id_utilisateur` varchar(50) NOT NULL DEFAULT '',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_aid`,`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_aid_utilisateurs`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_aid_utilisateurs_gest`
--

CREATE TABLE IF NOT EXISTS `j_aid_utilisateurs_gest` (
  `id_aid` varchar(100) NOT NULL DEFAULT '',
  `id_utilisateur` varchar(50) NOT NULL DEFAULT '',
  `indice_aid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_aid`,`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_aid_utilisateurs_gest`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_eleves_classes`
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
-- Dumping data for table `j_eleves_classes`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_eleves_cpe`
--

CREATE TABLE IF NOT EXISTS `j_eleves_cpe` (
  `e_login` varchar(50) NOT NULL DEFAULT '',
  `cpe_login` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`e_login`,`cpe_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_eleves_cpe`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_eleves_etablissements`
--

CREATE TABLE IF NOT EXISTS `j_eleves_etablissements` (
  `id_eleve` varchar(50) NOT NULL DEFAULT '',
  `id_etablissement` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_eleve`,`id_etablissement`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_eleves_etablissements`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_eleves_groupes`
--

CREATE TABLE IF NOT EXISTS `j_eleves_groupes` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_groupe`,`login`,`periode`),
  KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_eleves_groupes`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_eleves_professeurs`
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
-- Dumping data for table `j_eleves_professeurs`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_eleves_regime`
--

CREATE TABLE IF NOT EXISTS `j_eleves_regime` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `doublant` char(1) NOT NULL DEFAULT '',
  `regime` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_eleves_regime`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_groupes_classes`
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
-- Dumping data for table `j_groupes_classes`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_groupes_matieres`
--

CREATE TABLE IF NOT EXISTS `j_groupes_matieres` (
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `id_matiere` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_groupe`,`id_matiere`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_groupes_matieres`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_groupes_professeurs`
--

CREATE TABLE IF NOT EXISTS `j_groupes_professeurs` (
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `login` varchar(50) NOT NULL DEFAULT '',
  `ordre_prof` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_groupe`,`login`),
  KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_groupes_professeurs`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_groupes_visibilite`
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
-- Dumping data for table `j_groupes_visibilite`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_matieres_categories_classes`
--

CREATE TABLE IF NOT EXISTS `j_matieres_categories_classes` (
  `categorie_id` int(11) NOT NULL DEFAULT '0',
  `classe_id` int(11) NOT NULL DEFAULT '0',
  `priority` smallint(6) NOT NULL DEFAULT '0',
  `affiche_moyenne` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`categorie_id`,`classe_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_matieres_categories_classes`
--

INSERT INTO `j_matieres_categories_classes` (`categorie_id`, `classe_id`, `priority`, `affiche_moyenne`) VALUES
(1, 1, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `j_mentions_classes`
--

CREATE TABLE IF NOT EXISTS `j_mentions_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mention` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `ordre` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `j_mentions_classes`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_notifications_resp_pers`
--

CREATE TABLE IF NOT EXISTS `j_notifications_resp_pers` (
  `a_notification_id` int(12) NOT NULL COMMENT 'cle etrangere de la notification',
  `pers_id` varchar(10) NOT NULL COMMENT 'cle etrangere des personnes',
  PRIMARY KEY (`a_notification_id`,`pers_id`),
  KEY `j_notifications_resp_pers_FI_2` (`pers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre la notification et les personnes don';

--
-- Dumping data for table `j_notifications_resp_pers`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_professeurs_matieres`
--

CREATE TABLE IF NOT EXISTS `j_professeurs_matieres` (
  `id_professeur` varchar(50) NOT NULL DEFAULT '',
  `id_matiere` varchar(50) NOT NULL DEFAULT '',
  `ordre_matieres` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_professeur`,`id_matiere`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_professeurs_matieres`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_scol_classes`
--

CREATE TABLE IF NOT EXISTS `j_scol_classes` (
  `login` varchar(50) NOT NULL,
  `id_classe` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `j_scol_classes`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_signalement`
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
-- Dumping data for table `j_signalement`
--


-- --------------------------------------------------------

--
-- Table structure for table `j_traitements_saisies`
--

CREATE TABLE IF NOT EXISTS `j_traitements_saisies` (
  `a_saisie_id` int(12) NOT NULL COMMENT 'cle etrangere de l''absence saisie',
  `a_traitement_id` int(12) NOT NULL COMMENT 'cle etrangere du traitement de ces absences',
  PRIMARY KEY (`a_saisie_id`,`a_traitement_id`),
  KEY `j_traitements_saisies_FI_2` (`a_traitement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre la saisie et le traitement des absen';

--
-- Dumping data for table `j_traitements_saisies`
--


-- --------------------------------------------------------

--
-- Table structure for table `lettres_cadres`
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
-- Dumping data for table `lettres_cadres`
--

INSERT INTO `lettres_cadres` (`id_lettre_cadre`, `nom_lettre_cadre`, `x_lettre_cadre`, `y_lettre_cadre`, `l_lettre_cadre`, `h_lettre_cadre`, `texte_lettre_cadre`, `encadre_lettre_cadre`, `couleurdefond_lettre_cadre`) VALUES
(1, 'adresse responsable', 100, 40, 100, 5, 'A l''attention de\r\n<civilitee_court_responsable> <nom_responsable> <prenom_responsable>\r\n<adresse_responsable>\r\n<cp_responsable> <commune_responsable>\r\n', 0, '||'),
(2, 'adresse etablissement', 0, 0, 0, 0, '', 0, ''),
(3, 'datation', 0, 0, 0, 0, '', 0, ''),
(4, 'corp avertissement', 10, 70, 0, 5, '<u>Objet: </u> <g>Avertissement</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nJe me vois dans l''obligation de donner un <b>AVERTISSEMENT</b>\r\n\r\nà <g><nom_eleve> <prenom_eleve></g> élève de la classe <g><classe_eleve></g>.\r\n\r\n\r\npour la raison suivante : <g><sujet_eleve></g>\r\n\r\n<remarque_eleve>\r\n\r\n\r\n\r\nComme le prévoit le règlement intérieur de l''établissement, il pourra être sanctionné à partir de ce jour.\r\nSanction(s) possible(s) :\r\n\r\n\r\n\r\n\r\nJe vous remercie de me renvoyer cet exemplaire après l''avoir daté et signé.\r\nVeuillez agréer <nom_civilitee_long> <nom_responsable> l''assurance de ma considération distinguée.\r\n\r\n\r\n\r\nDate et signatures des parents :	', 0, '||'),
(5, 'corp blame', 10, 70, 0, 5, '<u>Objet</u>: <g>Blâme</g>\r\n\r\n\r\n<nom_civilitee_long>\r\n\r\nJe me vois dans l''obligation de donner un BLAME \r\n\r\nà <g><nom_eleve> <prenom_eleve></g> élève de la classe <g><classe_eleve></g>.\r\n\r\nDemandé par: <g><courrier_demande_par></g>\r\n\r\npour la raison suivante: <g><raison></g>\r\n\r\n<remarque>\r\n\r\nJe vous remercie de me renvoyer cet exemplaire après l''avoir daté et signé.\r\nVeuillez agréer <g><nom_civilitee_long> <nom_responsable></g> l''assurance de ma considération distinguée.\r\n\r\n<u>Date et signatures des parents:</u>\r\n\r\n\r\n\r\n\r\n\r\nNous demandons un entretien avec la personne ayant demandé la sanction OUI / NON.\r\n(La prise de rendez-vous est à votre initiative)\r\n', 0, '||'),
(6, 'corp convocation parents', 10, 70, 0, 5, '<u>Objet</u>: <g>Convocation des parents</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nVous êtes prié de prendre contact avec le Conseiller Principal d''Education dans les plus brefs délais, au sujet de <g><nom_eleve> <prenom_eleve></g> inscrit en classe de <g><classe_eleve></g>.\r\n\r\npour le motif suivant:\r\n\r\n<remarque>\r\n\r\n\r\n\r\nSans nouvelle de votre part avant le ........................................., je serai dans l''obligation de procéder à la descolarisation de l''élève, avec les conséquences qui en résulteront, jusqu''à votre rencontre.\r\n\r\n\r\nVeuillez agréer <g><nom_civilitee_long> <nom_responsable></g> l''assurance de ma considération distinguée.', 0, '||'),
(7, 'corp exclusion', 10, 70, 0, 5, '<u>Objet: </u> <g>Sanction - Exclusion de l''établissement</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nPar la présente, je tiens à vous signaler que <nom_eleve>\r\n\r\ninscrit en classe de  <classe_eleve>\r\n\r\n\r\ns''étant rendu coupable des faits suivants : \r\n\r\n<remarque>\r\n\r\n\r\n\r\nEst exclu de l''établissement,\r\nà compter du: <b><date_debut></b> à <b><heure_debut></b>,\r\njusqu''au: <b><date_fin></b> à <b><heure_fin></b>.\r\n\r\n\r\nIl devra se présenter, au bureau de la Vie Scolaire \r\n\r\nle ....................................... à ....................................... ACCOMPAGNE DE SES PARENTS.\r\n\r\n\r\n\r\n\r\nVeuillez agréer &lt;TYPEPARENT&gt; &lt;NOMPARENT&gt; l''assurance de ma considération distinguée.', 0, '||'),
(8, 'corp demande justificatif absence', 10, 70, 0, 5, '<u>Objet: </u> <g>Demande de justificatif d''absence</g>\r\n\r\n\r\n<civilitee_long_responsable>,\r\n\r\nJ''ai le regret de vous informer que <b><nom_eleve> <prenom_eleve></b>, élève en classe de <b><classe_eleve></b> n''a pas assisté au(x) cours:\r\n\r\n<liste>\r\n\r\nJe vous prie de bien vouloir me faire connaître le motif de son absence.\r\n\r\nPour permettre un contrôle efficace des présences, toute absence d''un élève doit être justifiée par sa famille, le jour même soit par téléphone, soit par écrit, soit par fax.\r\n\r\nAvant de regagner les cours, l''élève absent devra se présenter au bureau du Conseiller Principal d''Education muni de son carnet de correspondance avec un justificatif signé des parents.\r\n\r\nVeuillez agréer <civilitee_long_responsable> <nom_responsable>, l''assurance de ma considération distinguée.\r\n                                               \r\nCPE\r\n<civilitee_long_cpe> <nom_cpe> <prenom_cpe>\r\n\r\nPrière de renvoyer, par retour du courrier, le présent avis signé des parents :\r\n\r\nMotif de l''absence : \r\n________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________\r\n\r\n\r\n\r\nDate et signatures des parents :  \r\n', 0, '||'),
(10, 'signature', 100, 180, 0, 5, '<b><courrier_signe_par_fonction></b>,\r\n<courrier_signe_par>\r\n', 0, '||');

-- --------------------------------------------------------

--
-- Table structure for table `lettres_suivis`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lettres_suivis`
--


-- --------------------------------------------------------

--
-- Table structure for table `lettres_tcs`
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
-- Dumping data for table `lettres_tcs`
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
-- Table structure for table `lettres_types`
--

CREATE TABLE IF NOT EXISTS `lettres_types` (
  `id_lettre_type` int(11) NOT NULL AUTO_INCREMENT,
  `titre_lettre_type` varchar(250) NOT NULL,
  `categorie_lettre_type` varchar(250) NOT NULL,
  `reponse_lettre_type` varchar(3) NOT NULL,
  PRIMARY KEY (`id_lettre_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `lettres_types`
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
-- Table structure for table `log`
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
-- Dumping data for table `log`
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
('ADMIN', '2012-02-06 10:06:38', 'snou469d6qktp5al3bog8dm4l7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1', '', '1', '2012-02-06 10:42:53');

-- --------------------------------------------------------

--
-- Table structure for table `matieres`
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
-- Dumping data for table `matieres`
--


-- --------------------------------------------------------

--
-- Table structure for table `matieres_appreciations`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations` (
  `login` varchar(50) NOT NULL DEFAULT '',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY (`login`,`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `matieres_appreciations`
--


-- --------------------------------------------------------

--
-- Table structure for table `matieres_appreciations_acces`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations_acces` (
  `id_classe` int(11) NOT NULL,
  `statut` varchar(255) NOT NULL,
  `periode` int(11) NOT NULL,
  `date` date NOT NULL,
  `acces` enum('y','n','date','d') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `matieres_appreciations_acces`
--


-- --------------------------------------------------------

--
-- Table structure for table `matieres_appreciations_grp`
--

CREATE TABLE IF NOT EXISTS `matieres_appreciations_grp` (
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY (`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `matieres_appreciations_grp`
--


-- --------------------------------------------------------

--
-- Table structure for table `matieres_appreciations_tempo`
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
-- Dumping data for table `matieres_appreciations_tempo`
--


-- --------------------------------------------------------

--
-- Table structure for table `matieres_app_corrections`
--

CREATE TABLE IF NOT EXISTS `matieres_app_corrections` (
  `login` varchar(255) NOT NULL DEFAULT '',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `appreciation` text NOT NULL,
  PRIMARY KEY (`login`,`id_groupe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `matieres_app_corrections`
--


-- --------------------------------------------------------

--
-- Table structure for table `matieres_app_delais`
--

CREATE TABLE IF NOT EXISTS `matieres_app_delais` (
  `periode` int(11) NOT NULL DEFAULT '0',
  `id_groupe` int(11) NOT NULL DEFAULT '0',
  `date_limite` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`periode`,`id_groupe`),
  KEY `id_groupe` (`id_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `matieres_app_delais`
--


-- --------------------------------------------------------

--
-- Table structure for table `matieres_categories`
--

CREATE TABLE IF NOT EXISTS `matieres_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_court` varchar(255) NOT NULL DEFAULT '',
  `nom_complet` varchar(255) NOT NULL DEFAULT '',
  `priority` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `matieres_categories`
--

INSERT INTO `matieres_categories` (`id`, `nom_court`, `nom_complet`, `priority`) VALUES
(1, 'Autres', 'Autres', 5);

-- --------------------------------------------------------

--
-- Table structure for table `matieres_notes`
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
-- Dumping data for table `matieres_notes`
--


-- --------------------------------------------------------

--
-- Table structure for table `mef`
--

CREATE TABLE IF NOT EXISTS `mef` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire de la classe',
  `mef_code` bigint(20) DEFAULT NULL COMMENT 'Numero de la nomenclature officielle (numero MEF)',
  `libelle_court` varchar(50) NOT NULL COMMENT 'libelle de la formation',
  `libelle_long` varchar(300) NOT NULL COMMENT 'libelle de la formation',
  `libelle_edition` varchar(300) NOT NULL COMMENT 'libelle de la formation pour presentation',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Module élémentaire de formation' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mef`
--


-- --------------------------------------------------------

--
-- Table structure for table `mentions`
--

CREATE TABLE IF NOT EXISTS `mentions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mention` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mentions`
--


-- --------------------------------------------------------

--
-- Table structure for table `messages`
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
-- Dumping data for table `messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `message_login`
--

CREATE TABLE IF NOT EXISTS `message_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `texte` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `message_login`
--


-- --------------------------------------------------------

--
-- Table structure for table `miseajour`
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
-- Dumping data for table `miseajour`
--


-- --------------------------------------------------------

--
-- Table structure for table `modeles_grilles_pdf`
--

CREATE TABLE IF NOT EXISTS `modeles_grilles_pdf` (
  `id_modele` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL DEFAULT '',
  `nom_modele` varchar(255) NOT NULL,
  `par_defaut` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`id_modele`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `modeles_grilles_pdf`
--


-- --------------------------------------------------------

--
-- Table structure for table `modeles_grilles_pdf_valeurs`
--

CREATE TABLE IF NOT EXISTS `modeles_grilles_pdf_valeurs` (
  `id_modele` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL DEFAULT '',
  `valeur` varchar(255) NOT NULL,
  KEY `id_modele_champ` (`id_modele`,`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `modeles_grilles_pdf_valeurs`
--


-- --------------------------------------------------------

--
-- Table structure for table `model_bulletin`
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
-- Dumping data for table `model_bulletin`
--

INSERT INTO `model_bulletin` (`id_model_bulletin`, `nom_model_bulletin`, `active_bloc_datation`, `active_bloc_eleve`, `active_bloc_adresse_parent`, `active_bloc_absence`, `active_bloc_note_appreciation`, `active_bloc_avis_conseil`, `active_bloc_chef`, `active_photo`, `active_coef_moyenne`, `active_nombre_note`, `active_nombre_note_case`, `active_moyenne`, `active_moyenne_eleve`, `active_moyenne_classe`, `active_moyenne_min`, `active_moyenne_max`, `active_regroupement_cote`, `active_entete_regroupement`, `active_moyenne_regroupement`, `active_rang`, `active_graphique_niveau`, `active_appreciation`, `affiche_doublement`, `affiche_date_naissance`, `affiche_dp`, `affiche_nom_court`, `affiche_effectif_classe`, `affiche_numero_impression`, `caractere_utilse`, `X_parent`, `Y_parent`, `X_eleve`, `Y_eleve`, `cadre_eleve`, `X_datation_bul`, `Y_datation_bul`, `cadre_datation_bul`, `hauteur_info_categorie`, `X_note_app`, `Y_note_app`, `longeur_note_app`, `hauteur_note_app`, `largeur_coef_moyenne`, `largeur_nombre_note`, `largeur_d_une_moyenne`, `largeur_niveau`, `largeur_rang`, `X_absence`, `Y_absence`, `hauteur_entete_moyenne_general`, `X_avis_cons`, `Y_avis_cons`, `longeur_avis_cons`, `hauteur_avis_cons`, `cadre_avis_cons`, `X_sign_chef`, `Y_sign_chef`, `longeur_sign_chef`, `hauteur_sign_chef`, `cadre_sign_chef`, `affiche_filigrame`, `texte_filigrame`, `affiche_logo_etab`, `entente_mel`, `entente_tel`, `entente_fax`, `L_max_logo`, `H_max_logo`, `toute_moyenne_meme_col`, `active_reperage_eleve`, `couleur_reperage_eleve1`, `couleur_reperage_eleve2`, `couleur_reperage_eleve3`, `couleur_categorie_entete`, `couleur_categorie_entete1`, `couleur_categorie_entete2`, `couleur_categorie_entete3`, `couleur_categorie_cote`, `couleur_categorie_cote1`, `couleur_categorie_cote2`, `couleur_categorie_cote3`, `couleur_moy_general`, `couleur_moy_general1`, `couleur_moy_general2`, `couleur_moy_general3`, `titre_entete_matiere`, `titre_entete_coef`, `titre_entete_nbnote`, `titre_entete_rang`, `titre_entete_appreciation`, `active_coef_sousmoyene`, `arrondie_choix`, `nb_chiffre_virgule`, `chiffre_avec_zero`, `autorise_sous_matiere`, `affichage_haut_responsable`, `entete_model_bulletin`, `ordre_entete_model_bulletin`, `affiche_etab_origine`, `imprime_pour`, `largeur_matiere`, `nom_etab_gras`, `taille_texte_date_edition`, `taille_texte_matiere`, `active_moyenne_general`, `titre_bloc_avis_conseil`, `taille_titre_bloc_avis_conseil`, `taille_profprincipal_bloc_avis_conseil`, `affiche_fonction_chef`, `taille_texte_fonction_chef`, `taille_texte_identitee_chef`, `tel_image`, `tel_texte`, `fax_image`, `fax_texte`, `courrier_image`, `courrier_texte`, `largeur_bloc_eleve`, `hauteur_bloc_eleve`, `largeur_bloc_adresse`, `hauteur_bloc_adresse`, `largeur_bloc_datation`, `hauteur_bloc_datation`, `taille_texte_classe`, `type_texte_classe`, `taille_texte_annee`, `type_texte_annee`, `taille_texte_periode`, `type_texte_periode`, `taille_texte_categorie_cote`, `taille_texte_categorie`, `type_texte_date_datation`, `cadre_adresse`, `centrage_logo`, `Y_centre_logo`, `ajout_cadre_blanc_photo`, `affiche_moyenne_mini_general`, `affiche_moyenne_maxi_general`, `affiche_date_edition`) VALUES
(1, 'Standard', 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 18, 5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 0, 1, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 0, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 0, 0.01, 2, 0, 1, 1, 1, 1, 0, 0, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1),
(2, 'Standard avec photo', 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 18, 5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 0, 1, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 0, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 0, 0, 2, 0, 1, 1, 1, 1, 0, 0, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1),
(3, 'Affiche tout', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 16.5, 6.5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 1, 1, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 1, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 1, 0.01, 2, 0, 1, 1, 2, 1, 1, 1, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notanet`
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
-- Dumping data for table `notanet`
--


-- --------------------------------------------------------

--
-- Table structure for table `notanet_app`
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
-- Dumping data for table `notanet_app`
--


-- --------------------------------------------------------

--
-- Table structure for table `notanet_avis`
--

CREATE TABLE IF NOT EXISTS `notanet_avis` (
  `login` varchar(50) NOT NULL,
  `favorable` enum('O','N') NOT NULL,
  `avis` text NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notanet_avis`
--


-- --------------------------------------------------------

--
-- Table structure for table `notanet_corresp`
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
-- Dumping data for table `notanet_corresp`
--


-- --------------------------------------------------------

--
-- Table structure for table `notanet_ele_type`
--

CREATE TABLE IF NOT EXISTS `notanet_ele_type` (
  `login` varchar(50) NOT NULL,
  `type_brevet` tinyint(4) NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notanet_ele_type`
--


-- --------------------------------------------------------

--
-- Table structure for table `notanet_socles`
--

CREATE TABLE IF NOT EXISTS `notanet_socles` (
  `login` varchar(50) NOT NULL,
  `b2i` enum('MS','ME','MN','AB') NOT NULL,
  `a2` enum('MS','ME','AB') NOT NULL,
  `lv` varchar(50) NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notanet_socles`
--


-- --------------------------------------------------------

--
-- Table structure for table `notanet_verrou`
--

CREATE TABLE IF NOT EXISTS `notanet_verrou` (
  `id_classe` smallint(6) NOT NULL,
  `type_brevet` tinyint(4) NOT NULL,
  `verrouillage` char(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notanet_verrou`
--


-- --------------------------------------------------------

--
-- Table structure for table `pays`
--

CREATE TABLE IF NOT EXISTS `pays` (
  `code_pays` varchar(50) NOT NULL,
  `nom_pays` varchar(255) NOT NULL,
  PRIMARY KEY (`code_pays`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pays`
--


-- --------------------------------------------------------

--
-- Table structure for table `periodes`
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
-- Dumping data for table `periodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `plugins`
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
-- Dumping data for table `plugins`
--


-- --------------------------------------------------------

--
-- Table structure for table `plugins_autorisations`
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
-- Dumping data for table `plugins_autorisations`
--


-- --------------------------------------------------------

--
-- Table structure for table `plugins_menus`
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
-- Dumping data for table `plugins_menus`
--


-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE IF NOT EXISTS `preferences` (
  `login` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  KEY `login_name` (`login`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `preferences`
--


-- --------------------------------------------------------

--
-- Table structure for table `ref_wiki`
--

CREATE TABLE IF NOT EXISTS `ref_wiki` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ref_wiki`
--

INSERT INTO `ref_wiki` (`id`, `ref`, `url`) VALUES
(1, 'enseignement_invisible', 'http://www.sylogix.org/wiki/gepi/Enseignement_invisible');

-- --------------------------------------------------------

--
-- Table structure for table `responsables`
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
-- Dumping data for table `responsables`
--


-- --------------------------------------------------------

--
-- Table structure for table `responsables2`
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
-- Dumping data for table `responsables2`
--


-- --------------------------------------------------------

--
-- Table structure for table `resp_adr`
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
-- Dumping data for table `resp_adr`
--


-- --------------------------------------------------------

--
-- Table structure for table `resp_pers`
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
-- Dumping data for table `resp_pers`
--


-- --------------------------------------------------------

--
-- Table structure for table `salle_cours`
--

CREATE TABLE IF NOT EXISTS `salle_cours` (
  `id_salle` int(3) NOT NULL AUTO_INCREMENT,
  `numero_salle` varchar(10) NOT NULL,
  `nom_salle` varchar(50) NOT NULL,
  PRIMARY KEY (`id_salle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `salle_cours`
--


-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `NAME` varchar(255) NOT NULL DEFAULT '',
  `VALUE` text NOT NULL,
  PRIMARY KEY (`NAME`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`NAME`, `VALUE`) VALUES
('version', 'trunk'),
('versionRc', ''),
('versionBeta', ''),
('sessionMaxLength', '30'),
('Impression', '<center><p class = "grand">Gestion des Elèves Par Internet</p></center>\r\n<br />\r\n<p class = "grand">Qu''est-ce que GEPI ?</p>\r\n\r\n<p>Afin d''étudier les modalités d''informatisation des bulletins scolaires : notes et appréciations via Internet, une expérimentation (baptisée Gestion des Elèves Par Internet)a été mise en place. Cette expérimentation concerne les classes suivantes : \r\n<br />* ....\r\n<br />* ....\r\n<br />\r\n<br />\r\nCeci vous concerne car vous êtes professeur enseignant dans l''une ou l''autre de ces classes.\r\n<br />\r\n<br />\r\nA partir de la réception de ce document, vous pourrez remplir les bulletins informatisés :\r\n<span class = "norme">\r\n<UL><li>soit au lycée à partir de n''importe quel poste connecté à Internet,\r\n<li>soit chez vous si vous disposez d''une connexion Internet.\r\n</ul>\r\n</span>\r\n<p class = "grand">Comment accéder au module de saisie (notes etappréciations) :</p>\r\n<span class = "norme">\r\n<UL>\r\n    <LI>Se connecter à Internet\r\n    <LI>Lancer un navigateur (FireFox de préférence, Opera, Internet Explorer, ...)\r\n    <LI>Se connecter au site : https://adresse_du_site/gepi\r\n    <LI>Après quelques instants une page apparaît vous invitant à entrer un nom d''identifiant et un mot de passe (cesinformations figurent en haut de cette page).\r\n    <br />ATTENTION : votre mot de passe est strictement confidentiel.\r\n    <br />\r\n    <br />Une fois ces informations fournies, cliquez sur le bouton "Ok".\r\n    <LI> Après quelques instants une page d''accueil apparaît.<br />\r\nLa première fois, Gepi vous demande de changer votre mot de passe.\r\nChoisissez-en un facile à retenir, mais non trivial (évitez toute date\r\nde naissance, nom d''animal familier, prénom, etc.), et contenant\r\nlettre(s), chiffre(s), et caractère(s) non alphanumérique(s).<br />\r\nLes fois suivantes, vous arriverez directement au menu général de\r\nl''application. Pour bien prendre connaissance des possibilités de\r\nl''application, n''hésitez pas à essayer tous les liens disponibles !\r\n</ul></span>\r\n<p class = "grand">Remarque :</p>\r\n<p>GEPI est prévu pour que chaque professeur ne puisse modifier les notes ou les appréciations que dans les rubriques qui le concernent et uniquement pour ses élèves.\r\n<br />\r\nJe reste à votre disposition pour tout renseignement complémentaire.\r\n    <br />\r\n    Le proviseur adjoint\r\n</p>'),
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
('GepiAdminImprBulSettings', 'no'),
('GepiAccesReleveScol', 'yes'),
('GepiAccesReleveCpe', 'no'),
('GepiAccesReleveProf', 'no'),
('GepiAccesReleveProfTousEleves', 'no'),
('GepiAccesReleveProfToutesClasses', 'no'),
('GepiAccesReleveProfP', 'yes'),
('page_garde_imprime', 'no'),
('page_garde_texte', 'Madame, Monsieur<br/><br/>Veuillez trouvez ci-joint le bulletin scolaire de votre enfant. Nous vous rappelons que la journ&eacute;e <span style="font-weight: bold;">Portes ouvertes</span> du Lyc&eacute;e aura lieu samedi 20 mai entre 10 h et 17 h.<br/><br/>Veuillez agr&eacute;er, Madame, Monsieur, l''expression de mes meilleurs sentiments.<br/><br/><div style="text-align: right;">Le proviseur</div>'),
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
('bull_formule_bas', 'Report card à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.'),
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
('GepiPasswordReinitAdmin', 'no'),
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
('autorise_edt_eleve', 'no'),
('utiliserMenuBarre', 'no'),
('active_absences_parents', 'no'),
('creneau_different', 'n'),
('active_inscription', 'n'),
('active_inscription_utilisateurs', 'n'),
('mod_inscription_explication', '<p> <strong>Pr&eacute;sentation des dispositifs du Lyc&eacute;e dans les coll&egrave;ges qui organisent des rencontres avec les parents.</strong> <br />\r\n<br />\r\nChacun d&rsquo;entre vous conna&icirc;t la situation dans laquelle sont plac&eacute;s les &eacute;tablissements : </p>\r\n<ul>\r\n    <li>baisse d&eacute;mographique</li>\r\n    <li>r&eacute;gulation des moyens</li>\r\n    <li>- ... </li>\r\n</ul>\r\nCette ann&eacute;e encore nous devons &ecirc;tre pr&eacute;sents dans les r&eacute;unions organis&eacute;es au sein des coll&egrave;ges afin de pr&eacute;senter nos sp&eacute;cificit&eacute;s, notre valeur ajout&eacute;e, les &eacute;volution du projet, le label international, ... <br />\r\nsur cette feuille, vous avez la possibilit&eacute; de vous inscrire afin d''intervenir dans un ou plusieurs coll&egrave;ges selon vos convenances.'),
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
('texte_visa_cdt', 'Cahier de textes visé ce jour <br />Le Principal <br /> M. XXXXX<br />'),
('visa_cdt_inter_modif_notices_visees', 'yes'),
('denomination_eleve', 'élève'),
('denomination_eleves', 'élèves'),
('denomination_professeur', 'professeur'),
('denomination_professeurs', 'professeurs'),
('denomination_responsable', 'responsable légal'),
('denomination_responsables', 'responsables légaux'),
('delais_apres_cloture', '0'),
('active_mod_ooo', 'n'),
('use_only_cdt', 'n'),
('edt_remplir_prof', 'n'),
('active_mod_genese_classes', 'y'),
('active_mod_ects', 'y'),
('GepiAccesSaisieEctsProf', 'no'),
('GepiAccesSaisieEctsPP', 'no'),
('GepiAccesSaisieEctsScolarite', 'yes'),
('GepiAccesRecapitulatifEctsScolarite', 'yes'),
('GepiAccesRecapitulatifEctsProf', 'yes'),
('GepiAccesEditionDocsEctsPP', 'no'),
('GepiAccesEditionDocsEctsScolarite', 'yes'),
('gepiSchoolStatut', 'prive_hors_contrat'),
('gepiSchoolAcademie', ''),
('note_autre_que_sur_referentiel', 'F'),
('referentiel_note', '20'),
('active_mod_apb', 'n'),
('active_mod_gest_aid', 'n'),
('unzipped_max_filesize', '10'),
('autorise_commentaires_mod_disc', 'no'),
('sso_cas_table', 'no'),
('utiliser_mb', 'y'),
('filtrage_html', 'htmlpurifier'),
('utiliser_no_php_in_img', 'n'),
('backup_directory', '2x75EX9UauxNHHEURtvnE6j20kDnDNXU0Alju5Xk4oq4T'),
('backupdir_lastchange', '1328445609'),
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
('active_mod_discipline', 'y');

-- --------------------------------------------------------

--
-- Table structure for table `suivi_eleve_cpe`
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
-- Dumping data for table `suivi_eleve_cpe`
--


-- --------------------------------------------------------

--
-- Table structure for table `synthese_app_classe`
--

CREATE TABLE IF NOT EXISTS `synthese_app_classe` (
  `id_classe` int(11) NOT NULL DEFAULT '0',
  `periode` int(11) NOT NULL DEFAULT '0',
  `synthese` text NOT NULL,
  PRIMARY KEY (`id_classe`,`periode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `synthese_app_classe`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_alerte_mail`
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
-- Dumping data for table `s_alerte_mail`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_autres_sanctions`
--

CREATE TABLE IF NOT EXISTS `s_autres_sanctions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_sanction` int(11) NOT NULL,
  `id_nature` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `s_autres_sanctions`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_categories`
--

CREATE TABLE IF NOT EXISTS `s_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categorie` varchar(50) NOT NULL DEFAULT '',
  `sigle` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `s_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_communication`
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
-- Dumping data for table `s_communication`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_delegation`
--

CREATE TABLE IF NOT EXISTS `s_delegation` (
  `id_delegation` int(11) NOT NULL AUTO_INCREMENT,
  `fct_delegation` varchar(100) NOT NULL,
  `fct_autorite` varchar(50) NOT NULL,
  `nom_autorite` varchar(50) NOT NULL,
  PRIMARY KEY (`id_delegation`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `s_delegation`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_exclusions`
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
-- Dumping data for table `s_exclusions`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_incidents`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `s_incidents`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_lieux_incidents`
--

CREATE TABLE IF NOT EXISTS `s_lieux_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lieu` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `s_lieux_incidents`
--

INSERT INTO `s_lieux_incidents` (`id`, `lieu`) VALUES
(1, 'Classe'),
(2, 'Couloir'),
(3, 'Cour'),
(4, 'Réfectoire'),
(5, 'Autre');

-- --------------------------------------------------------

--
-- Table structure for table `s_mesures`
--

CREATE TABLE IF NOT EXISTS `s_mesures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('prise','demandee') DEFAULT NULL,
  `mesure` varchar(50) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `s_mesures`
--

INSERT INTO `s_mesures` (`id`, `type`, `mesure`, `commentaire`) VALUES
(1, 'prise', 'Travail supplémentaire', ''),
(2, 'prise', 'Mot dans le carnet de liaison', ''),
(3, 'demandee', 'Retenue', ''),
(4, 'demandee', 'Exclusion', '');

-- --------------------------------------------------------

--
-- Table structure for table `s_natures`
--

CREATE TABLE IF NOT EXISTS `s_natures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nature` varchar(50) NOT NULL DEFAULT '',
  `id_categorie` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `s_natures`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_protagonistes`
--

CREATE TABLE IF NOT EXISTS `s_protagonistes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_incident` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `qualite` varchar(50) NOT NULL,
  `avertie` enum('N','O') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `s_protagonistes`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_qualites`
--

CREATE TABLE IF NOT EXISTS `s_qualites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qualite` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `s_qualites`
--

INSERT INTO `s_qualites` (`id`, `qualite`) VALUES
(1, 'Responsable'),
(2, 'Victime'),
(3, 'Témoin'),
(4, 'Autre');

-- --------------------------------------------------------

--
-- Table structure for table `s_reports`
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
-- Dumping data for table `s_reports`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_retenues`
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
-- Dumping data for table `s_retenues`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_sanctions`
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
-- Dumping data for table `s_sanctions`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_traitement_incident`
--

CREATE TABLE IF NOT EXISTS `s_traitement_incident` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_incident` int(11) NOT NULL,
  `login_ele` varchar(50) NOT NULL,
  `login_u` varchar(50) NOT NULL,
  `id_mesure` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `s_traitement_incident`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_travail`
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
-- Dumping data for table `s_travail`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_travail_mesure`
--

CREATE TABLE IF NOT EXISTS `s_travail_mesure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_incident` int(11) NOT NULL,
  `login_ele` varchar(50) NOT NULL,
  `travail` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `s_travail_mesure`
--


-- --------------------------------------------------------

--
-- Table structure for table `s_types_sanctions`
--

CREATE TABLE IF NOT EXISTS `s_types_sanctions` (
  `id_nature` int(11) NOT NULL AUTO_INCREMENT,
  `nature` varchar(255) NOT NULL,
  PRIMARY KEY (`id_nature`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `s_types_sanctions`
--

INSERT INTO `s_types_sanctions` (`id_nature`, `nature`) VALUES
(1, 'Avertissement travail'),
(2, 'Avertissement comportement');

-- --------------------------------------------------------

--
-- Table structure for table `tempo`
--

CREATE TABLE IF NOT EXISTS `tempo` (
  `id_classe` int(11) NOT NULL DEFAULT '0',
  `max_periode` int(11) NOT NULL DEFAULT '0',
  `num` char(32) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tempo`
--


-- --------------------------------------------------------

--
-- Table structure for table `tempo2`
--

CREATE TABLE IF NOT EXISTS `tempo2` (
  `col1` varchar(100) NOT NULL DEFAULT '',
  `col2` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tempo2`
--


-- --------------------------------------------------------

--
-- Table structure for table `tempo3`
--

CREATE TABLE IF NOT EXISTS `tempo3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `col1` varchar(255) NOT NULL,
  `col2` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tempo3`
--


-- --------------------------------------------------------

--
-- Table structure for table `tempo3_cdt`
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
-- Dumping data for table `tempo3_cdt`
--


-- --------------------------------------------------------

--
-- Table structure for table `temp_gep_import`
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
-- Dumping data for table `temp_gep_import`
--


-- --------------------------------------------------------

--
-- Table structure for table `temp_gep_import2`
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
-- Dumping data for table `temp_gep_import2`
--


-- --------------------------------------------------------

--
-- Table structure for table `tentatives_intrusion`
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `tentatives_intrusion`
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
(12, '-', '127.0.0.1', '2012-02-06 10:02:25', 1, '/login.php', 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : admin)', 'new');

-- --------------------------------------------------------

--
-- Table structure for table `udt_corresp`
--

CREATE TABLE IF NOT EXISTS `udt_corresp` (
  `champ` varchar(255) NOT NULL DEFAULT '',
  `nom_udt` varchar(255) NOT NULL DEFAULT '',
  `nom_gepi` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `udt_corresp`
--


-- --------------------------------------------------------

--
-- Table structure for table `udt_lignes`
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
-- Dumping data for table `udt_lignes`
--


-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
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
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`login`, `nom`, `prenom`, `civilite`, `password`, `salt`, `email`, `show_email`, `statut`, `etat`, `change_mdp`, `date_verrouillage`, `password_ticket`, `ticket_expiration`, `niveau_alerte`, `observation_securite`, `temp_dir`, `numind`, `auth_mode`) VALUES
('ADMIN', 'GEPI', 'Administrateur', 'M.', 'ead325d878724499578533f5af89874fce9696d3ecf410fcda188c539c10d8fe', '6478fe26654e796c4c67fb1b67fc6eed', '', 'no', 'administrateur', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', 2, 0, 'ADMIN_3Dbv9J8BQj2qQZsvGTr3PM7m4p2Fo7T5800UHbqj9b0', '', 'gepi');

-- --------------------------------------------------------

--
-- Table structure for table `vocabulaire`
--

CREATE TABLE IF NOT EXISTS `vocabulaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `terme` varchar(255) NOT NULL DEFAULT '',
  `terme_corrige` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `vocabulaire`
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
-- Table structure for table `vs_alerts_eleves`
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
-- Dumping data for table `vs_alerts_eleves`
--


-- --------------------------------------------------------

--
-- Table structure for table `vs_alerts_groupes`
--

CREATE TABLE IF NOT EXISTS `vs_alerts_groupes` (
  `id_alert_groupe` int(11) NOT NULL AUTO_INCREMENT,
  `nom_alert_groupe` varchar(150) NOT NULL,
  `creerpar_alert_groupe` varchar(100) NOT NULL,
  PRIMARY KEY (`id_alert_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `vs_alerts_groupes`
--


-- --------------------------------------------------------

--
-- Table structure for table `vs_alerts_types`
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
-- Dumping data for table `vs_alerts_types`
--

