-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 04 mai 2026 à 17:50
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
-- Base de données : `magasin`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id_article` varchar(30) NOT NULL,
  `design` varchar(50) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `categorie` varchar(30) NOT NULL,
  PRIMARY KEY (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id_article`, `design`, `prix`, `categorie`) VALUES
('A001', 'Chaise scandinave', 120.00, 'Mobilier'),
('A002', 'Lampe de bureau LED', 4.00, 'Éclairage'),
('Bll foot', 'Ballon de football', 30.00, 'Sport'),
('Casq A', 'Casque audio sans fil', 90.00, 'Technologie'),
('M CAFE', 'Machine à café expresso', 320.00, 'Électroménager'),
('Mtre C', 'Montre connectée', 150.00, 'Technologie'),
('R dp', 'Réfrigérateur double porte', 500.00, 'Électroménager'),
('S-B', 'Sneakers blanches', 110.00, 'Mode'),
('SAC DOS', 'Sac à dos en cuir', 90.00, 'Accessoires'),
('Sp 128 Go', 'Smartphone 128 Go', 700.00, 'Technologie'),
('TBV', 'Table basse en verre', 200.00, 'Mobilier'),
('TPS B', 'Tapis berbère', 150.00, 'Décoration'),
('Vst J', 'Veste en jean', 76.00, 'Mode');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id_client` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `age` tinyint UNSIGNED DEFAULT NULL,
  `adresse` varchar(60) NOT NULL,
  `ville` varchar(30) NOT NULL,
  `mail` varchar(50) DEFAULT 'pas de mail',
  PRIMARY KEY (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=1247 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id_client`, `nom`, `prenom`, `age`, `adresse`, `ville`, `mail`) VALUES
(124, 'ALI', 'Perso', 28, 'C_S', 'cotonou', 'aliperso@gmail.com'),
(1245, 'ALIB', 'Persoa', 28, 'C_S', 'cotonou', 'alibpersoa@gmail.com'),
(1246, 'ALIBABA', 'Personnage', 30, 'C_S', 'Parakou', 'alibabapersonnage@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id_comm` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `id_client` mediumint UNSIGNED NOT NULL,
  PRIMARY KEY (`id_comm`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_comm`, `date`, `id_client`) VALUES
(2, '2026-05-04', 124),
(3, '2026-05-04', 1245),
(8, '2026-05-04', 1246);

-- --------------------------------------------------------

--
-- Structure de la table `lignes`
--

DROP TABLE IF EXISTS `lignes`;
CREATE TABLE IF NOT EXISTS `lignes` (
  `id_comm` mediumint UNSIGNED NOT NULL,
  `id_article` varchar(30) NOT NULL,
  `quantite` tinyint UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_comm`,`id_article`),
  KEY `id_article` (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `lignes`
--

INSERT INTO `lignes` (`id_comm`, `id_article`, `quantite`) VALUES
(2, 'SAC DOS', 2),
(3, 'A002', 2),
(8, 'TBV', 3);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `identifiant` int NOT NULL AUTO_INCREMENT,
  `names` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`identifiant`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`identifiant`, `names`, `password`, `createdate`) VALUES
(2, 'Aurelle', 'Aurelle123', '2026-05-04 00:00:00');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`);

--
-- Contraintes pour la table `lignes`
--
ALTER TABLE `lignes`
  ADD CONSTRAINT `lignes_ibfk_1` FOREIGN KEY (`id_comm`) REFERENCES `commande` (`id_comm`),
  ADD CONSTRAINT `lignes_ibfk_2` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
