-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 10 Novembre 2016 à 17:21
-- Version du serveur :  5.7.11
-- Version de PHP :  7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `slim3`
--

-- --------------------------------------------------------

--
-- Structure de la table `hobbies`
--

CREATE TABLE `hobbies` (
  `id` int(11) NOT NULL,
  `id_owner` int(11) NOT NULL,
  `name_owner` varchar(255) NOT NULL,
  `morph` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Morph into creep colony',
  `eat` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Eat Terrans',
  `invade` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Invade Aiur',
  `obey` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Obey to the Overmind',
  `gather` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Gather minerals',
  `infest` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Infest command centers',
  `praises` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Praise sAviOr',
  `praisej` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Praise Jaedong',
  `burrow` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Burrow',
  `explode` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Die while exploding',
  `spawn` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Spawn more overlords',
  `kill_vessels` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Kill science vessels',
  `plague` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Plague marines',
  `hide` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Hide in dark swarms'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `hobbies`
--

INSERT INTO `hobbies` (`id`, `id_owner`, `name_owner`, `morph`, `eat`, `invade`, `obey`, `gather`, `infest`, `praises`, `praisej`, `burrow`, `explode`, `spawn`, `kill_vessels`, `plague`, `hide`) VALUES
(1, 1, 'Glioburd', 1, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text,
  `sexuality` varchar(255) DEFAULT 'bisexual',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `isactive` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `bio`, `sexuality`, `created_at`, `updated_at`, `isactive`) VALUES
(1, 'Glioburd', 'g@g.com', '$2y$10$ToC4Mag/3W7in1gJxTOWI.QoxQa2VrKnqoSGlNUAvBel13iuvtOqa', 'dsfdsfjdosfdoudsoifudsofudsoifudsoifudsoifds', 'hetero', '2016-11-10 17:53:37', '2016-11-10 18:03:43', 1);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `hobbies`
--
ALTER TABLE `hobbies`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `hobbies`
--
ALTER TABLE `hobbies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
