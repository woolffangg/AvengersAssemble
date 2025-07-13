-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 09 juil. 2025 à 16:39
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `chat_web`
--

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

DROP TABLE IF EXISTS `membre`;
CREATE TABLE IF NOT EXISTS `membre` (
  `fkU` int NOT NULL,
  `fkS` int NOT NULL,
  PRIMARY KEY (`fkU`,`fkS`),
  KEY `fkS` (`fkS`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `pkMsg` int NOT NULL AUTO_INCREMENT,
  `fkU` int DEFAULT NULL,
  `fkS` int DEFAULT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pkMsg`),
  KEY `fkU` (`fkU`),
  KEY `fkS` (`fkS`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`pkMsg`, `fkU`, `fkS`, `message`, `timestamp`) VALUES
(1, 1, 1, 'testtt messageeee', '2025-07-04 14:53:02'),
(2, 1, 1, 'ahaha', '2025-07-04 15:06:46'),
(3, 1, 1, 'ououou', '2025-07-04 15:06:51'),
(4, 1, 1, 'ouou', '2025-07-04 15:06:55'),
(5, 1, 1, 'edazdzadazdza', '2025-07-06 12:27:28'),
(6, 2, 1, 'dzadzadza', '2025-07-09 18:31:44'),
(7, 2, 1, 'dqsdqs', '2025-07-09 18:31:47'),
(8, 1, 1, 'dazdazdza', '2025-07-09 18:32:11');

-- --------------------------------------------------------

--
-- Structure de la table `moderer`
--

DROP TABLE IF EXISTS `moderer`;
CREATE TABLE IF NOT EXISTS `moderer` (
  `fkU` int NOT NULL,
  `fkS` int NOT NULL,
  PRIMARY KEY (`fkU`,`fkS`),
  KEY `fkS` (`fkS`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `pkR` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY (`pkR`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`pkR`, `label`) VALUES
(1, 'Membre'),
(2, 'Admin');

-- --------------------------------------------------------

--
-- Structure de la table `salon`
--

DROP TABLE IF EXISTS `salon`;
CREATE TABLE IF NOT EXISTS `salon` (
  `pkS` int NOT NULL AUTO_INCREMENT,
  `fkU_proprio` int DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `visibilite` tinyint(1) DEFAULT '1',
  `prive` tinyint(1) DEFAULT '0',
  `topic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pkS`),
  KEY `fkU_proprio` (`fkU_proprio`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `salon`
--

INSERT INTO `salon` (`pkS`, `fkU_proprio`, `nom`, `visibilite`, `prive`, `topic`) VALUES
(1, 1, 'nathanLeGay', 1, 0, 'testtt'),
(2, 1, 'ahahahaha', 1, 0, 'hahahaaXDDDXDX');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `pkU` int NOT NULL AUTO_INCREMENT,
  `fkRole` int DEFAULT NULL,
  `pseudo` varchar(50) NOT NULL,
  `login` varchar(100) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`pkU`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `fkRole` (`fkRole`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`pkU`, `fkRole`, `pseudo`, `login`, `mdp`, `email`) VALUES
(1, 1, 'test', 'test', '$2y$10$u9nE/yqor4PavMUb9Sokc.vnASplpTM3iNnz23pqwZzALSWXX20eW', 'adamdepaep31@gmail.com'),
(2, 1, 'hihi', 'hihi', '$2y$10$8ZDwieEVuiaMxXl8WCSNEeoP6hOX4/L.7xLdFC9uNqkEZY1mYGNj.', 'hihi@hihi.fr');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;