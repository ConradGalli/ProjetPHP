-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 13 Mai 2016 à 08:34
-- Version du serveur :  5.7.11
-- Version de PHP :  5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projet_php`
--

-- --------------------------------------------------------

--
-- Structure de la table `nationality`
--

CREATE TABLE `nationality` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `nationality`
--

INSERT INTO `nationality` (`id`, `name`) VALUES
(1, 'Français'),
(2, 'Anglais'),
(3, 'Allemand'),
(4, 'Russe');

-- --------------------------------------------------------

--
-- Structure de la table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `room`
--

INSERT INTO `room` (`id`, `name`) VALUES
(1, '101'),
(2, '102'),
(3, '201'),
(4, '202');

-- --------------------------------------------------------

--
-- Structure de la table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `nationality_id` int(11) NOT NULL,
  `training_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `student`
--

INSERT INTO `student` (`id`, `name`, `surname`, `nationality_id`, `training_type_id`) VALUES
(1, 'Sharapova', 'Nadia', 4, 2),
(2, 'Monfils', 'Boby', 3, 2),
(4, 'Becket', 'Samuel', 2, 1),
(6, 'Dupont', 'Robert', 1, 2),
(8, 'Murray', 'Bill', 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `student_teacher`
--

CREATE TABLE `student_teacher` (
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `student_teacher`
--

INSERT INTO `student_teacher` (`student_id`, `teacher_id`, `date_start`, `date_end`) VALUES
(1, 3, '2016-05-17', '2016-05-24'),
(1, 4, '2016-05-10', '2016-05-23'),
(2, 3, '2016-05-03', '2016-05-16'),
(2, 4, '2011-08-26', '2011-10-18'),
(4, 3, '2011-08-17', '2015-10-22'),
(8, 2, '2011-08-15', '2012-02-15');

-- --------------------------------------------------------

--
-- Structure de la table `teacher`
--

CREATE TABLE `teacher` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `room_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `teacher`
--

INSERT INTO `teacher` (`id`, `name`, `surname`, `room_id`) VALUES
(1, 'Dupont', 'Robert', 1),
(2, 'Martin', 'Jean', 2),
(3, 'Durand', 'Paul', 3),
(4, 'Duval', 'Alain', 4);

-- --------------------------------------------------------

--
-- Structure de la table `training_type`
--

CREATE TABLE `training_type` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `code` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `training_type`
--

INSERT INTO `training_type` (`id`, `name`, `code`) VALUES
(1, 'Web designer', 'wd'),
(2, 'Développeur', 'dev');

-- --------------------------------------------------------

--
-- Structure de la table `training_type_teacher`
--

CREATE TABLE `training_type_teacher` (
  `training_type_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `training_type_teacher`
--

INSERT INTO `training_type_teacher` (`training_type_id`, `teacher_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 3),
(2, 4);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `nationality`
--
ALTER TABLE `nationality`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nationality_id` (`nationality_id`),
  ADD KEY `training_type_id` (`training_type_id`);

--
-- Index pour la table `student_teacher`
--
ALTER TABLE `student_teacher`
  ADD PRIMARY KEY (`student_id`,`teacher_id`),
  ADD KEY `student_teacher_ibfk_2` (`teacher_id`);

--
-- Index pour la table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Index pour la table `training_type`
--
ALTER TABLE `training_type`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `training_type_teacher`
--
ALTER TABLE `training_type_teacher`
  ADD PRIMARY KEY (`training_type_id`,`teacher_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `nationality`
--
ALTER TABLE `nationality`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT pour la table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `training_type`
--
ALTER TABLE `training_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`nationality_id`) REFERENCES `nationality` (`id`),
  ADD CONSTRAINT `student_ibfk_2` FOREIGN KEY (`training_type_id`) REFERENCES `training_type` (`id`);

--
-- Contraintes pour la table `student_teacher`
--
ALTER TABLE `student_teacher`
  ADD CONSTRAINT `student_teacher_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_teacher_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`);

--
-- Contraintes pour la table `training_type_teacher`
--
ALTER TABLE `training_type_teacher`
  ADD CONSTRAINT `training_type_teacher_ibfk_1` FOREIGN KEY (`training_type_id`) REFERENCES `training_type` (`id`),
  ADD CONSTRAINT `training_type_teacher_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
