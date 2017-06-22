-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 23 Novembre 2016 à 18:10
-- Version du serveur :  5.7.11
-- Version de PHP :  7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `matcha`
--

-- --------------------------------------------------------

--
-- Structure de la table `hobbies`
--

CREATE TABLE `hobbies` (
  `id` int(11) NOT NULL,
  `id_owner` int(11) NOT NULL,
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
  `killVessels` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Kill science vessels',
  `plague` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Plague marines',
  `hide` tinyint(4) UNSIGNED DEFAULT NULL COMMENT 'Hide in dark swarms'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `hobbies`
--

INSERT INTO `hobbies` (`id`, `id_owner`, `morph`, `eat`, `invade`, `obey`, `gather`, `infest`, `praises`, `praisej`, `burrow`, `explode`, `spawn`, `killVessels`, `plague`, `hide`) VALUES
(1, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(3, 3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `pictures`
--

CREATE TABLE `pictures` (
  `id` int(11) NOT NULL,
  `id_owner` int(11) NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `src` varchar(255) DEFAULT NULL,
  `ismainpic` tinyint(1) UNSIGNED DEFAULT '0' COMMENT 'Value for main picture'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `pictures`
--

INSERT INTO `pictures` (`id`, `id_owner`, `date`, `src`, `ismainpic`) VALUES
(19, 2, '2016-11-23 12:42:32', '../../matcha/uploads/2/1477906899371.jpg', 1),
(20, 2, '2016-11-23 12:42:47', '../../matcha/uploads/2/1477914347269.png', 0),
(21, 2, '2016-11-23 13:54:15', '../../matcha/uploads/2/Arbiter_SC1_Head1.png', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` char(1) DEFAULT NULL,
  `bio` text,
  `sexuality` varchar(255) DEFAULT 'bisexual',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `isactive` tinyint(4) NOT NULL DEFAULT '1',
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  `isAlive` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `gender`, `bio`, `sexuality`, `created_at`, `updated_at`, `isactive`, `rank`) VALUES
(1, 'Admin', 'admin@matcha.com', '$2y$10$ToC4Mag/3W7in1gJxTOWI.QoxQa2VrKnqoSGlNUAvBel13iuvtOqa', 'm', 'dsfdsfjdosfdoudsoifudsofudsoifudsoifudsoifds', 'hetero', '2016-11-21 17:04:50', '2016-11-21 18:09:41', 1, 1),
(2, 'Blabl', 'desmo04@dd.com', '$2y$10$YaSUfkC0r965pU3glAyMx.eZrYFB5nNPtKF0sZtQD5Ap8SWFaLtoO', 'm', 'Awesome description okay okay okay', 'hetero', '2016-11-21 18:08:18', '2016-11-23 19:03:49', 1, 0),
(3, 'Tassz', 'tassz@tassz.com', '$2y$10$xqoYW0x3uItSejsRWtf24uExZkq0YTeALp2yt6lfgm2M17u.o5pKW', 'm', 'TasszTasszTasszTasszTasszTasszTasszTasszTasszTasszTasszTasszTasszTasszTasszTasszTasszTassz', 'hetero', '2016-11-21 18:11:43', '2016-11-23 19:04:45', 1, 0),
(4, 'totoyo', 'toto@toto.com', '$2y$10$HLnxOes8VdTqWO6fWn3ACO9E0R7Hgsms5hvcNCiqmiANYvaAKFcKi', NULL, NULL, 'bisexual', '2016-11-21 18:38:06', '2016-11-21 18:38:06', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `name_owner` varchar(255) NOT NULL,
  `name_visitor` varchar(255) NOT NULL,
  `visited_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `visitors`
--

INSERT INTO `visitors` (`id`, `name_owner`, `name_visitor`, `visited_at`) VALUES
(7, 'Tassz', 'Blabl', '2016-11-23 18:58:09'),
(8, 'Tassz', 'Blabl', '2016-11-23 18:58:10'),
(9, 'Tassz', 'Blabl', '2016-11-23 18:58:11'),
(10, 'Tassz', 'Blabl', '2016-11-23 18:58:11'),
(11, 'Tassz', 'Blabl', '2016-11-23 18:58:11'),
(12, 'Tassz', 'Blabl', '2016-11-23 18:58:12'),
(13, 'Tassz', 'Blabl', '2016-11-23 18:58:12'),
(14, 'Tassz', 'Blabl', '2016-11-23 19:04:11'),
(15, 'Tassz', 'Blabl', '2016-11-23 19:04:13'),
(16, 'Tassz', 'Blabl', '2016-11-23 19:04:15'),
(17, 'Tassz', 'Blabl', '2016-11-23 19:04:15'),
(18, 'Tassz', 'Blabl', '2016-11-23 19:04:15'),
(19, 'Tassz', 'Blabl', '2016-11-23 19:04:16'),
(20, 'Tassz', 'Blabl', '2016-11-23 19:04:16'),
(21, 'Tassz', 'Blabl', '2016-11-23 19:04:16'),
(22, 'Blabl', 'Tassz', '2016-11-23 19:08:36'),
(23, 'Blabl', 'Tassz', '2016-11-23 19:09:54');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `hobbies`
--
ALTER TABLE `hobbies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_owner` (`id_owner`);

--
-- Index pour la table `pictures`
--
ALTER TABLE `pictures`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users.name` (`name_owner`) USING BTREE;

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `hobbies`
--
ALTER TABLE `hobbies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `pictures`
--
ALTER TABLE `pictures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `hobbies`
--
ALTER TABLE `hobbies`
  ADD CONSTRAINT `hobbies_ibfk_1` FOREIGN KEY (`id_owner`) REFERENCES `users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
