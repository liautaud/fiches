-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 29 Novembre 2016 à 08:14
-- Version du serveur: 10.0.26-MariaDB-1~wheezy
-- Version de PHP: 5.6.27-1~dotdeb+7.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `fiches`
--

-- --------------------------------------------------------

--
-- Structure de la table `dependances`
--

CREATE TABLE IF NOT EXISTS `dependances` (
  `suivante_id` int(11) NOT NULL,
  `precedente_id` int(11) NOT NULL,
  PRIMARY KEY (`suivante_id`,`precedente_id`),
  KEY `IDX_78BC984C4D104D` (`suivante_id`),
  KEY `IDX_78BC984C562D9ABA` (`precedente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exercices`
--

CREATE TABLE IF NOT EXISTS `exercices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fiche_id` int(11) DEFAULT NULL,
  `numero` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1387EAE1DF522508` (`fiche_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `ext_log_entries`
--

CREATE TABLE IF NOT EXISTS `ext_log_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `logged_at` datetime NOT NULL,
  `object_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version` int(11) NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)',
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_class_lookup_idx` (`object_class`),
  KEY `log_date_lookup_idx` (`logged_at`),
  KEY `log_user_lookup_idx` (`username`),
  KEY `log_version_lookup_idx` (`object_id`,`object_class`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `ext_translations`
--

CREATE TABLE IF NOT EXISTS `ext_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_unique_idx` (`locale`,`object_class`,`field`,`foreign_key`),
  KEY `translations_lookup_idx` (`locale`,`object_class`,`foreign_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `fiches`
--

CREATE TABLE IF NOT EXISTS `fiches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mini_projet` tinyint(1) NOT NULL,
  `langage_id` int(11) DEFAULT NULL,
  `chemin_git` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `chemin_git_complementaires` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_459C25C9957BB53C` (`langage_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `fiches`
--

INSERT INTO `fiches` (`id`, `titre`, `mini_projet`, `langage_id`, `chemin_git`, `chemin_git_complementaires`) VALUES
(0, '', 0, NULL, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `langages`
--

CREATE TABLE IF NOT EXISTS `langages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `migrations`
--

INSERT INTO `migrations` (`version`) VALUES
('20161016031349'),
('20161017192002'),
('20161017211529'),
('20161018234002'),
('20161019151327'),
('20161019162139'),
('20161019215337'),
('20161020005542'),
('20161020155606'),
('20161020155702'),
('20161114131414');

-- --------------------------------------------------------

--
-- Structure de la table `rendus`
--

CREATE TABLE IF NOT EXISTS `rendus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fiche_id` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `etat` int(11) NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_traitement` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A999BBADDF522508` (`fiche_id`),
  KEY `IDX_A999BBADFB88E14F` (`utilisateur_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `reponses`
--

CREATE TABLE IF NOT EXISTS `reponses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exercice_id` int(11) DEFAULT NULL,
  `rendu_id` int(11) DEFAULT NULL,
  `numero` int(11) NOT NULL,
  `contenu` longtext COLLATE utf8_unicode_ci,
  `chemin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1E512EC689D40298` (`exercice_id`),
  KEY `IDX_1E512EC6C974D9ED` (`rendu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `responsable_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `statut` int(11) NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inscription_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `promotion` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_497B315E53C59D72` (`responsable_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `responsable_id`, `nom`, `prenom`, `email`, `statut`, `password`, `remember_token`, `inscription_token`, `promotion`) VALUES
(0, NULL, 'Administrateur', '', 'admin@localhost', 2, '$2y$10$T6bbKuaYW5CqXbmJGk62vew9LsDcaOfxJ7s7PskZJdbTGI1dwaOhC', NULL, NULL, 0);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `dependances`
--
ALTER TABLE `dependances`
  ADD CONSTRAINT `FK_78BC984C4D104D` FOREIGN KEY (`suivante_id`) REFERENCES `fiches` (`id`),
  ADD CONSTRAINT `FK_78BC984C562D9ABA` FOREIGN KEY (`precedente_id`) REFERENCES `fiches` (`id`);

--
-- Contraintes pour la table `exercices`
--
ALTER TABLE `exercices`
  ADD CONSTRAINT `FK_1387EAE1DF522508` FOREIGN KEY (`fiche_id`) REFERENCES `fiches` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiches`
--
ALTER TABLE `fiches`
  ADD CONSTRAINT `FK_459C25C9957BB53C` FOREIGN KEY (`langage_id`) REFERENCES `langages` (`id`);

--
-- Contraintes pour la table `rendus`
--
ALTER TABLE `rendus`
  ADD CONSTRAINT `FK_A999BBADDF522508` FOREIGN KEY (`fiche_id`) REFERENCES `fiches` (`id`),
  ADD CONSTRAINT `FK_A999BBADFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `reponses`
--
ALTER TABLE `reponses`
  ADD CONSTRAINT `FK_1E512EC689D40298` FOREIGN KEY (`exercice_id`) REFERENCES `exercices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_1E512EC6C974D9ED` FOREIGN KEY (`rendu_id`) REFERENCES `rendus` (`id`);

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `FK_497B315E53C59D72` FOREIGN KEY (`responsable_id`) REFERENCES `utilisateurs` (`id`);
