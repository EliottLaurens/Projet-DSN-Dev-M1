-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  ven. 22 sep. 2023 à 02:48
-- Version du serveur :  5.7.17
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `cesf`
--

-- --------------------------------------------------------

--
-- Structure de la table `point`
--

CREATE TABLE `point` (
  `ID` int(11) NOT NULL,
  `Structure` varchar(50) NOT NULL,
  `Type` int(11) NOT NULL DEFAULT '1',
  `Professionnel` varchar(50) NOT NULL,
  `MailPro` varchar(255) DEFAULT NULL,
  `TelPro` varchar(10) DEFAULT NULL,
  `Datedernierstage` date DEFAULT NULL,
  `Adresse` varchar(255) NOT NULL,
  `Codepostal` int(11) NOT NULL,
  `Ville` varchar(50) NOT NULL,
  `Latitude` float NOT NULL,
  `Longitude` float NOT NULL,
  `Etat` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `point`
--

INSERT INTO `point` (`ID`, `Structure`, `Type`, `Professionnel`, `MailPro`, `TelPro`, `Datedernierstage`, `Adresse`, `Codepostal`, `Ville`, `Latitude`, `Longitude`, `Etat`) VALUES
(1, 'Cité de l\'espace', 4, 'Jean Martin', '', '0655898874', '2023-09-06', 'Avenue Jean Gonord', 31500, 'Toulouse', 43.5852, 1.49183, 2),
(2, 'Limayrac', 1, 'Julien Dupres', 'julien.dupres@gmail.com', '0608688958', '2023-09-14', '50 rue de Limayrac', 31500, 'Toulouse', 43.5938, 1.47065, 4),
(3, 'Clinique Rive Gauche', 3, 'Jeanne Dubois', 'jeanne.dubois@gmail.com', '0648558965', '2023-08-08', '49 Allée Charles de Fitte', 31300, 'Toulouse', 43.5961, 1.43219, 3),
(4, 'Hôpital Rangueil', 2, 'Catherine Dupuis', 'catherine.dupuis@gmail.com', '0607441256', '2023-06-08', '1 Avenue du Professeur Jean Poulhès', 31400, 'Toulouse', 43.5597, 1.45416, 1),
(13, 'Hôpital Purpan', 2, 'Céline Durant', 'celine.durant@gmail.com', '0697448752', '2023-05-12', 'Place du Docteur Joseph Baylac', 31300, 'Toulouse', 43.6077, 1.39736, 2),
(14, 'CCAS Toulouse', 4, 'Didier Morand', 'didier.morand@gmail.com', '', '2023-02-06', '2 BIS Rue de Belfort', 31300, 'Toulouse', 43.6087, 1.45082, 3);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('administrateur','enseignant') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `email`, `mot_de_passe`, `role`) VALUES
(1, 'vincent.riviere@gmail.com', '1234', 'administrateur'),
(2, 'antoine.claverie@gmail.com', '1234', 'enseignant'),
(3, 'eliott.laurens@gmail.com', '1234', 'enseignant');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `point`
--
ALTER TABLE `point`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `point`
--
ALTER TABLE `point`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
