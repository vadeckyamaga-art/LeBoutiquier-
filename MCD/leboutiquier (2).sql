-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 24 mars 2026 à 18:09
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `leboutiquier`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id_article` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nom_article` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `desc_article` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prix_article` double DEFAULT NULL,
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `photo_article` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `photo2` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `photo3` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Statut` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quantite_stock` int DEFAULT NULL,
  `id_cat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_commerçant` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_article`),
  KEY `id_cat` (`id_cat`),
  KEY `id_commerçant` (`id_commerçant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id_article`, `nom_article`, `desc_article`, `prix_article`, `date_ajout`, `photo_article`, `photo2`, `photo3`, `Statut`, `quantite_stock`, `id_cat`, `id_commerçant`) VALUES
('ART-2025-1218', 'Crayon de couleur', 'Aide les enfants à travailler', 1000, '2025-12-31 10:30:21', 'fourniture8_20251231103021.jpg', 'det_1767177038_0.jpg', 'det_1767177038_1.jpg', 'Epuisé', 1000, 'CAT-2025-5441', 'BTQ-2025-9772'),
('ART-2025-1532', 'YearPhone', 'Compatible avec tous les téléphones', 5000, '2025-12-26 10:23:20', 'écouteurs_20251226102320_20260317015512_20260317015548.jpg', 'det_1766792077_0.JPG', 'det_1766792077_1.png', 'Epuisé', 9, 'CAT-2025-3932', 'BTQ-2025-2716'),
('ART-2025-1601', 'AZERT', 'DFTYUI', 3456, '2025-12-25 22:17:09', 'Screenshot 2025-07-03 224423 (2)_20251225101709.png', 'det_1766766612_0.jpg', 'det_1766766612_1.jpg', 'Epuisé', 45678, 'CAT-2025-2947', 'BTQ-2025-2716'),
('ART-2025-3101', 'Gourde d\'eau', 'Très pratique et rafraichis l\'eau en continu', 5000, '2025-12-26 10:26:46', 'best2_20251226102646.jpg', 'det_1766790103_0.JPG', 'det_1766790103_1.png', 'Epuisé', 70, 'CAT-2025-4750', 'BTQ-2025-2716'),
('ART-2025-3186', 'AZERTY', 'SDFGH', 12345, '2025-12-25 22:23:04', 'IMG_E4854_20251225102304.jpg', 'det_1766786006_0.jpg', 'det_1766786006_1.jpg', 'Epuisé', 565, 'CAT-2025-2947', 'BTQ-2025-2716'),
('ART-2025-6783', 'Légumes Bio Nature', '1=&amp;gt;200 FCFA\r\n5=&amp;gt;800 FCFA', 300, '2025-12-26 23:26:24', 'bio3_20260104035241.jpg', '', '', 'Epuisé', 92, 'CAT-2025-2666', 'BTQ-2025-2716'),
('ART-2025-7014', 'Cahier TP', 'Utile pour la gestion', 500, '2025-12-31 10:29:03', 'bureau_20251231102903.jpg', 'det_1767176969_0.jpg', '', 'Epuisé', 20, 'CAT-2025-5441', 'BTQ-2025-9772'),
('ART-2025-7736', 'Pain du Jour', '1=&gt;150 FCFA\r\n5=&gt;600 FCFA', 150, '2025-12-26 11:22:12', 'boulangerie_20251226112212.jpg', 'det_1766789596_0.JPG', 'det_1766789596_1.png', 'Epuisé', 94, 'CAT-2025-2666', 'BTQ-2025-2716'),
('ART-2025-8169', 'HeadPhone', 'Compatible avec tout type d\'appareil, bluethooth e', 9000, '2025-12-25 23:20:25', 'écouteurs3_20251226102533.jpg', 'det_1766789995_0.png', 'det_1766789995_1.png', 'Epuisé', 65, 'CAT-2025-3932', 'BTQ-2025-2716'),
('ART-2026-1023', 'Tennis homme ', 'Pratique pour les marches et le sport ', 15000, '2026-01-08 14:30:57', 'IMG_6830_20260108023057.jpeg', '', '', 'Epuisé', 50, 'CAT-2025-4909', 'BTQ-2025-2716'),
('ART-2026-2439', 'Watto schip', '%ML%KLMK', 5000, '2026-03-21 11:08:45', 'IMG_7108_20260321110845.jpg', '', '', 'Epuisé', 3, 'CAT-2025-2666', 'BTQ-2025-3760'),
('ART-2026-3226', 'Beignets ', 'Pas cher ', 500, '2026-03-23 07:35:04', '', '', '', 'Epuisé', 5000, 'CAT-2025-2666', 'BTQ-2026-9783'),
('ART-2026-4097', 'BEYAH', 'Nouvel album de damso, en vynile', 15000, '2026-02-22 07:09:38', 'IMG_5368_20260222070938.jpg', '', '', 'Epuisé', 50, 'CAT-2025-5441', 'BTQ-2026-3621'),
('ART-2026-7175', 'PHP-Wampserver', 'Langage de programmation coté serveur', 15000, '2026-03-04 08:37:11', 'ec1759f8cd003e098c4af39b4a827515_20260304083711.jpg', '', '', 'Epuisé', 50, 'CAT-2025-5441', 'BTQ-2025-3760'),
('ART-2026-7372', 'Bootstrap', 'COOL', 20000, '2026-02-21 16:08:12', '18435ae66b0b4ce59b491aef643ee8e1_20260221040812.jpg', '', '', 'Epuisé', 100, 'CAT-2025-4750', 'BTQ-2026-9950'),
('ART-2026-7972', 'SGBD', 'Conception facile des Bases de Données', 150000, '2026-02-21 14:23:35', '097b349ab1d78c15744c3a89ff457939_20260221022335.jpg', 'det_1771683848_0.jpg', '', 'Epuisé', 100000, 'CAT-2025-3932', 'BTQ-2026-9950');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id_avis` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `note` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `commentaire` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_avis` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_commerçant` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_client` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_livreur` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_avis`),
  KEY `id_commerçant` (`id_commerçant`),
  KEY `id_client` (`id_client`),
  KEY `id_livreur` (`id_livreur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id_avis`, `note`, `commentaire`, `date_avis`, `id_commerçant`, `id_client`, `id_livreur`) VALUES
('AVS-2025-1598', '5', 'YOUNG', '2025-12-28 13:47:46', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-1889', '5', 'VADECK', '2025-12-28 13:07:00', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-2362', '5', 'YVES', '2025-12-28 14:04:05', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-2454', '4', 'jaime', '2025-12-28 20:50:03', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-3464', '5', '01:55', '2025-12-29 00:55:47', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-3558', '5', 'EDFVBN', '2025-12-28 21:39:58', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-3702', '5', 'azertyuiom', '2025-12-28 12:39:15', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-4151', '4', 'UYUI', '2025-12-28 13:25:28', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-4722', '5', 'JEAN DUPONT', '2025-12-31 15:35:54', 'BTQ-2025-9772', 'CLT-2025-8633', NULL),
('AVS-2025-5216', '5', 'BONSOIR', '2025-12-28 23:34:42', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-5823', '4', 'ENCORE MOI', '2025-12-30 14:56:22', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-6418', '1', 'BPM', '2025-12-28 23:51:44', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-6633', '5', 'ertyuiop', '2025-12-30 08:29:28', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-6963', '5', 'BONJOUR', '2025-12-28 12:39:53', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-7155', '4', 'JADORE', '2025-12-28 21:26:10', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-7514', '5', 'hgfccv', '2025-12-31 13:21:28', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-8226', '5', 'MORGAN', '2025-12-28 14:10:11', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-8357', '5', 'AZERTYUIOP', '2025-12-28 15:38:51', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-9098', '5', 'fzertyu', '2025-12-28 11:53:08', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-9571', '4', 'JHGDFG', '2025-12-28 11:50:04', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2025-9938', '4', 'DANY', '2025-12-28 13:08:42', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2026-1478', '5', '', '2026-03-09 12:05:18', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2026-1548', '5', 'J\'ADORE', '2026-01-08 14:22:11', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2026-2048', '4', 'COOL', '2026-01-03 09:34:50', 'BTQ-2025-9772', 'CLT-2025-8633', NULL),
('AVS-2026-2094', '4', 'CVBN?.', '2026-01-07 16:23:15', 'BTQ-2025-9772', 'CLT-2025-8633', NULL),
('AVS-2026-5473', '5', 'YOO', '2026-01-03 09:58:55', 'BTQ-2025-9772', 'CLT-2025-8633', NULL),
('AVS-2026-5686', '2', 'Mauvais service', '2026-01-04 22:43:27', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2026-6379', '5', 'C’est pas grave ', '2026-01-08 14:38:43', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2026-6540', '5', 'C’est cool', '2026-01-08 14:35:26', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2026-7553', '4', 'J\'adore', '2026-02-21 19:38:00', 'BTQ-2026-9950', 'CLT-2025-8633', NULL),
('AVS-2026-8647', '4', 'VBN?.', '2026-01-07 16:22:29', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2026-9022', '3', 'COOL', '2026-01-03 09:49:04', 'BTQ-2025-2716', 'CLT-2025-8633', NULL),
('AVS-2026-9600', '2', 'DAMSO', '2026-01-03 09:35:44', 'BTQ-2025-9772', 'CLT-2025-8633', NULL),
('AVS-2026-9814', '5', 'AZSDCVBHN .', '2026-01-03 09:37:42', 'BTQ-2025-9772', 'CLT-2025-8633', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `catégorie`
--

DROP TABLE IF EXISTS `catégorie`;
CREATE TABLE IF NOT EXISTS `catégorie` (
  `id_cat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nom_cat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_cat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `catégorie`
--

INSERT INTO `catégorie` (`id_cat`, `nom_cat`, `image_cat`) VALUES
('CAT-2025-2666', 'Agro-Alimentaire', 'alimentation.jpg'),
('CAT-2025-2947', 'Quincaillerie', 'pièce.jpg'),
('CAT-2025-3932', 'Electronique', 'electronique.JPG'),
('CAT-2025-4748', 'Produits d\'entretien', 'produit entretient.JPG'),
('CAT-2025-4750', 'Electroménager', 'electromenager.JPG'),
('CAT-2025-4909', 'Epicerie', 'epicerie.JPG'),
('CAT-2025-5441', 'Bouquin', 'bouquin.JPG'),
('CAT-2025-5540', 'Parfumerie', 'parfumerie.JPG');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id_client` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_client`),
  KEY `id_users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id_client`, `id`) VALUES
('CLT-2025-8633', 'USER-2025-3485'),
('CLT-2026-5446', 'USER-2026-3160'),
('CLT-2026-3446', 'USER-2026-4982'),
('CLT-2026-7656', 'USER-2026-5823'),
('CLT-2026-8348', 'USER-2026-5865'),
('CLT-2026-4421', 'USER-2026-5900'),
('CLT-2026-8697', 'USER-2026-6069'),
('CLT-2026-5558', 'USER-2026-7006'),
('CLT-2026-6908', 'USER-2026-7789');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id_commande` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Code_retrait` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Date_commande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `type_achat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `frais_livraison` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `statut` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `motif` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Montant_commande` double DEFAULT NULL,
  `numero_livraison` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `adresse_livraison` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_client` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_commerçant` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_livreur` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_commande`),
  KEY `id_client` (`id_client`),
  KEY `id_commerçant` (`id_commerçant`),
  KEY `id_livreur` (`id_livreur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `Code_retrait`, `Date_commande`, `type_achat`, `frais_livraison`, `statut`, `motif`, `Montant_commande`, `numero_livraison`, `adresse_livraison`, `id_client`, `id_commerçant`, `id_livreur`) VALUES
('CMD-2026-1191', 'CDR-2026-2470', '2026-02-11 15:29:21', 'retrait', '0', 'En attente', '', 5000, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-1224', 'CDR-2026-5178', '2026-03-23 07:40:08', 'retrait', '0', 'En attente', '', 9000, '697935271', 'Retrait en boutique', 'CLT-2026-5558', 'BTQ-2025-2716', NULL),
('CMD-2026-1464', 'CDR-2026-4832', '2026-03-22 11:13:23', 'retrait', '0', 'En attente', '', 300, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-1475', 'CDR-2026-5353', '2026-02-12 12:34:03', 'livraison', '1500', 'En attente', '', 1500, '692238528', 'ESG ', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-1541', 'CDR-2026-1610', '2026-02-11 12:10:11', 'retrait', '0', 'En attente', '', 5000, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-1631', 'CDR-2026-6078', '2026-02-12 11:29:15', 'livraison', '1500', 'En attente', '', 1800, '692238528', 'Ngodo-Bakoko', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-1671', 'CDR-2026-6937', '2026-01-31 19:58:58', 'livraison', '1500', 'En Préparation', '', 6800, '697935271', 'Logbaba ', 'CLT-2026-5558', 'BTQ-2025-2716', NULL),
('CMD-2026-1769', 'CDR-2026-4630', '2026-02-11 15:35:27', 'retrait', '0', 'En attente', '', 0, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-1860', 'CDR-2026-7935', '2026-03-18 16:43:46', 'livraison', '1500', 'En attente', '', 1500, '693301917', 'Ari, Ngodi-Bakoko', 'CLT-2025-8633', 'BTQ-2026-9950', NULL),
('CMD-2026-2002', 'CDR-2026-7061', '2026-03-18 16:45:06', 'retrait', '0', 'En attente', '', 0, '693301917', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-2250', 'CDR-2026-3316', '2026-02-11 15:34:39', 'retrait', '0', 'En attente', '', 300, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-2814', 'CDR-2026-1293', '2026-02-22 13:51:51', 'livraison', '1500', 'En attente', '', 46500, '699887766', 'nkolbong', 'CLT-2026-4421', 'BTQ-2025-2716', NULL),
('CMD-2026-2897', 'CDR-2026-6228', '2026-02-11 12:12:19', 'retrait', '0', 'En attente', '', 12345, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-3152', 'CDR-2026-4088', '2026-02-21 19:38:41', 'livraison', '1500', 'Annulé', 'Stock épuissé', 151500, '692238528', 'Ari, Ngodi-Bakoko', 'CLT-2025-8633', 'BTQ-2026-9950', NULL),
('CMD-2026-3157', 'CDR-2026-6377', '2026-02-11 12:15:22', 'retrait', '0', 'En attente', '', 300, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-3309', 'CDR-2026-3464', '2026-02-05 18:09:34', 'livraison', '1500', 'En Préparation', '', 97895, '692238528', 'Ngodi-Bakoko', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-3318', 'CDR-2026-9590', '2026-03-21 12:03:46', 'livraison', '1500', 'En attente', '', 1500, '693301917', 'sfdfgnd', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-3410', 'CDR-2026-6352', '2026-02-05 19:58:45', 'retrait', '0', 'En attente', '', 8500, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-9772', NULL),
('CMD-2026-3506', 'CDR-2026-9218', '2026-02-05 19:04:30', 'livraison', '1500', 'Annulé', 'ESSAIE', 19145, '693301917', 'SDFTYUI', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-3616', 'CDR-2026-4243', '2026-01-31 19:55:02', 'livraison', '1500', 'En Préparation', '', 21950, '697935271', 'Logbaba Maison blanche ', 'CLT-2026-5558', 'BTQ-2025-2716', NULL),
('CMD-2026-3619', 'CDR-2026-6058', '2026-03-22 11:09:09', 'retrait', '0', 'En attente', '', 17645, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-3728', 'CDR-2026-6530', '2026-03-22 20:25:01', 'retrait', '0', 'En attente', '', 150, '682709250', 'Retrait en boutique', 'CLT-2026-3446', 'BTQ-2025-2716', NULL),
('CMD-2026-4046', 'CDR-2026-1851', '2026-03-23 07:37:12', 'livraison', '1500', 'En attente', '', 16500, '682709250', 'Tradex', 'CLT-2026-3446', 'BTQ-2025-2716', NULL),
('CMD-2026-4085', 'CDR-2026-4198', '2026-02-05 18:14:58', 'livraison', '1500', 'En Préparation', '', 19145, '693301917', 'Ngodi-Bakoko', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-4249', 'CDR-2026-2966', '2026-02-05 21:22:51', 'retrait', '0', 'Annulé', 'ESSAIE', 26645, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-4345', 'CDR-2026-6882', '2026-02-11 12:03:33', 'retrait', '0', 'Annulé', 'ESSAIE', 5000, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-4623', 'CDR-2026-8215', '2026-02-22 14:26:55', 'livraison', '1500', 'En attente', '', 1650, '699887766', 'NKOLBONG', 'CLT-2026-4421', 'BTQ-2025-2716', NULL),
('CMD-2026-4630', 'CDR-2026-3213', '2026-02-11 15:13:44', 'retrait', '0', 'En attente', '', 12345, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-4632', 'CDR-2026-5622', '2026-02-11 14:17:55', 'retrait', '0', 'En attente', '', 10000, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-4742', 'CDR-2026-1740', '2026-01-02 10:15:10', 'livraison', '1500', 'En attente', '', 4000, '4567899', 'AKWA', 'CLT-2025-8633', 'BTQ-2025-9772', NULL),
('CMD-2026-5024', 'CDR-2026-2730', '2026-02-05 20:00:36', 'retrait', '0', 'En attente', '', 1500, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-9772', NULL),
('CMD-2026-5049', 'CDR-2026-6180', '2026-03-22 11:10:56', 'retrait', '0', 'En attente', '', 20000, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2026-9950', NULL),
('CMD-2026-5067', 'CDR-2026-4409', '2026-03-22 10:29:54', 'livraison', '1500', 'En attente', '', 401500, '692238528', 'Ari-Village', 'CLT-2025-8633', 'BTQ-2026-9950', NULL),
('CMD-2026-5122', 'CDR-2026-5047', '2026-02-11 11:49:22', 'retrait', '0', 'Annulé', 'ESSAIE', 300, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-5376', 'CDR-2026-4490', '2026-03-22 10:53:23', 'livraison', '1500', 'En attente', '', 151500, '692238528', 'Ari-village', 'CLT-2025-8633', 'BTQ-2026-9950', NULL),
('CMD-2026-5385', 'CDR-2026-5824', '2026-02-11 12:16:30', 'retrait', '0', 'Annulé', 'ESSAIE', 12345, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-5505', 'CDR-2026-5753', '2026-03-23 07:24:40', 'livraison', '1500', 'En attente', '', 1650, '697935271', 'LOGBABA ', 'CLT-2026-5558', 'BTQ-2025-2716', NULL),
('CMD-2026-5508', 'CDR-2026-2893', '2026-01-05 09:55:50', 'retrait', '0', 'Livré', '', 2400, '45678', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-5751', 'CDR-2026-3555', '2026-02-22 13:51:59', 'livraison', '1500', 'En attente', '', 1500, '699887766', 'nkolbong', 'CLT-2026-4421', 'BTQ-2025-2716', NULL),
('CMD-2026-5909', 'CDR-2026-4011', '2026-02-11 15:22:29', 'retrait', '0', 'En attente', '', 0, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-5939', 'CDR-2026-6750', '2026-02-12 12:33:46', 'livraison', '1500', 'En attente', '', 6500, '692238528', 'ESG ', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-6003', 'CDR-2026-6854', '2026-03-22 20:18:56', 'retrait', '0', 'En attente', '', 1500, '682709250', 'Retrait en boutique', 'CLT-2026-3446', 'BTQ-2025-9772', NULL),
('CMD-2026-6006', 'CDR-2026-2117', '2026-02-11 11:56:47', 'retrait', '0', 'Annulé', 'ESSAIE', 5000, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-6143', 'CDR-2026-9910', '2026-02-14 12:21:46', 'livraison', '1500', 'En attente', '', 1800, '692238528', 'azerfgh', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-6168', 'CDR-2026-7745', '2026-03-22 10:21:44', 'retrait', '0', 'En attente', '', 16050, '693301917', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-6340', 'CDR-2026-9535', '2026-02-11 12:02:46', 'retrait', '0', 'Annulé', 'ESSAIE', 300, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-6347', 'CDR-2026-3747', '2026-02-14 12:23:31', 'livraison', '1500', 'En attente', '', 6500, '692238528', 'wxcvb', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-6566', 'CDR-2026-3287', '2026-01-01 16:44:32', 'livraison', '1500', 'Livré', '', 27845, '4567899', 'DOUALA', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-6890', 'CDR-2026-5542', '2026-03-22 11:04:05', 'retrait', '0', 'En attente', '', 20000, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2026-9950', NULL),
('CMD-2026-6961', 'CDR-2026-4876', '2026-02-05 19:30:34', 'retrait', '0', 'Annulé', 'ESSAIE', 17645, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-7054', 'CDR-2026-8304', '2026-02-11 10:56:07', 'retrait', '0', 'Annulé', 'ESSAIE', 17645, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-7097', 'CDR-2026-6139', '2026-02-14 12:21:54', 'livraison', '1500', 'En attente', '', 1500, '692238528', 'azerfgh', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-7596', 'CDR-2026-9199', '2026-03-22 10:42:42', 'livraison', '1500', 'En attente', '', 24445, '692238528', 'Akwa', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-7803', 'CDR-2026-7801', '2026-02-11 15:25:41', 'retrait', '0', 'En attente', '', 12345, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-8013', 'CDR-2026-8664', '2026-02-05 21:24:11', 'retrait', '0', 'Annulé', 'ESSAIE', 17645, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-8015', 'CDR-2026-6104', '2026-02-05 19:26:13', 'retrait', '0', 'Annulé', 'ESSAIE', 22345, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-8114', 'CDR-2026-7014', '2026-02-12 11:29:21', 'livraison', '1500', 'En attente', '', 1500, '692238528', 'Ngodo-Bakoko', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-8194', 'CDR-2026-6359', '2026-02-11 15:36:52', 'retrait', '0', 'En attente', '', 0, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-8562', 'CDR-2026-1524', '2026-02-11 15:22:21', 'retrait', '0', 'En attente', '', 300, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-8587', 'CDR-2026-1083', '2026-02-11 15:25:46', 'retrait', '0', 'En attente', '', 0, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-8726', 'CDR-2026-8856', '2026-01-04 22:51:53', 'livraison', '1500', 'Livré', '', 47845, '45678', 'Ari-Village', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-8755', 'CDR-2026-2084', '2026-01-11 11:06:04', 'retrait', '0', 'Annulé', 'SDFGHJK', 35590, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-8768', 'CDR-2026-1769', '2026-02-22 14:28:47', 'livraison', '1500', 'En attente', '', 1500, '699887766', 'NKOLBONG', 'CLT-2026-4421', 'BTQ-2025-2716', NULL),
('CMD-2026-8987', 'CDR-2026-6542', '2026-02-05 22:37:50', 'retrait', '0', 'Annulé', 'ESSAIE', 17645, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-9036', 'CDR-2026-4292', '2026-02-11 12:00:44', 'retrait', '0', 'En attente', '', 5000, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-9176', 'CDR-2026-4552', '2026-03-18 16:43:38', 'livraison', '1500', 'En attente', '', 1500, '693301917', 'Ari, Ngodi-Bakoko', 'CLT-2025-8633', 'BTQ-2026-9950', NULL),
('CMD-2026-9206', 'CDR-2026-9843', '2026-02-21 19:39:51', 'retrait', '0', 'En attente', '', 20000, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2026-9950', NULL),
('CMD-2026-9276', 'CDR-2026-8758', '2026-02-11 11:02:52', 'livraison', '1500', 'Annulé', 'ESSAIE', 19145, '692238528', 'azertyu', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-9313', 'CDR-2026-2202', '2026-01-31 19:53:13', 'livraison', '1500', 'Livré', '', 17400, '697935271', 'Logbaba Maison blanche ', 'CLT-2026-5558', 'BTQ-2025-2716', NULL),
('CMD-2026-9331', 'CDR-2026-8094', '2026-01-08 14:23:26', 'livraison', '1500', 'Annulé', 'Je n\'ai plus aucun de ces articles en stock', 19145, '45678', 'IUG-ISTA', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-9483', 'CDR-2026-9799', '2026-02-11 15:29:25', 'retrait', '0', 'En attente', '', 0, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-9569', 'CDR-2026-3203', '2026-01-05 08:09:37', 'livraison', '1500', 'Annulé', 'Plus d\'articles en stock', 5100, '679806237', 'Elf-village', 'CLT-2026-6908', 'BTQ-2025-2716', NULL),
('CMD-2026-9637', 'CDR-2026-8519', '2026-02-11 11:57:59', 'retrait', '0', 'Annulé', 'ESSAIE', 12345, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-9770', 'CDR-2026-8257', '2026-02-11 12:08:55', 'retrait', '0', 'En attente', '', 12345, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-9874', 'CDR-2026-7713', '2026-02-11 10:39:15', 'retrait', '0', 'Annulé', 'ESSAIE', 35290, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL),
('CMD-2026-9905', 'CDR-2026-6137', '2026-02-11 15:34:46', 'retrait', '0', 'En attente', '', 0, '692238528', 'Retrait en boutique', 'CLT-2025-8633', 'BTQ-2025-2716', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commerçant`
--

DROP TABLE IF EXISTS `commerçant`;
CREATE TABLE IF NOT EXISTS `commerçant` (
  `id_commerçant` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nom_commerçant` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nom_boutique` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description_boutique` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Quartier_boutique` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `statut` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profil_boutique` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ajoute_par` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_commerçant`),
  KEY `id_users` (`id`),
  KEY `fk_admin_createur_commercant` (`ajoute_par`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commerçant`
--

INSERT INTO `commerçant` (`id_commerçant`, `nom_commerçant`, `nom_boutique`, `description_boutique`, `Quartier_boutique`, `statut`, `profil_boutique`, `id`, `ajoute_par`) VALUES
('BTQ-2025-2716', 'Dany', 'KIVANTOU', 'Matin Bonheur', '{\"lat\":5.07,\"long\":12.74,\"timestamp\":\"2026-02-21T10:22:53.379Z\"}', NULL, 'IMG_7053_20260205052114.jpg', 'USER-2025-5435', NULL),
('BTQ-2025-3760', 'Yamag', 'Vadeck\'Shop', 'Ventre d\'article agro-alimentaire', '{\"lat\":5.07,\"long\":13.74,\"timestamp\":\"2026-02-21T10:22:53.379Z\"}', NULL, 'IMG_6875_20260304083441.jpg', 'USER-2025-9532', NULL),
('BTQ-2025-6448', 'Marine', NULL, NULL, '', NULL, NULL, 'USER-2025-9509', NULL),
('BTQ-2025-9642', 'Morgan', NULL, NULL, NULL, NULL, NULL, 'USER-2025-1693', NULL),
('BTQ-2025-9772', 'Dupont', 'Dupont Service', 'Vente de de matériel de bureau', '', NULL, 'lune_20251231102752.jpg', 'USER-2025-9546', NULL),
('BTQ-2026-1122', 'Nkwentie', NULL, NULL, '', NULL, NULL, 'USER-2026-9380', NULL),
('BTQ-2026-3621', 'QDFG', 'MEVTR', 'Vente de disque de vinyle', '{\"lat\":4.07,\"long\":9.74,\"timestamp\":\"2026-02-21T10:05:02.048Z\"}', 'certifie', 'IMG_6581_20260222070718.jpg', 'USER-2026-9587', 'ADM-2026-5392'),
('BTQ-2026-9038', 'John', 'John&amp;Coe', 'Vente de matériel informatique et élèctronique', '{\"lat\":7.07,\"long\":9.74,\"timestamp\":\"2026-02-21T10:49:12.002Z\"}', 'certifie', 'IMG_5136_20260221105026.jpg', 'USER-2026-7747', 'ADM-2026-5392'),
('BTQ-2026-9783', 'Yvana', NULL, NULL, '', NULL, NULL, 'USER-2026-2973', NULL),
('BTQ-2026-9950', 'HGFDS', 'TheVie Radio', 'Vente des ordinateurs et téléphones portables', '{\"lat\":35.07,\"long\":0.21,\"timestamp\":\"2026-02-21T10:22:53.379Z\"}', 'certifie', 'IMG_5998_20260221022553.jpg', 'USER-2026-4260', 'ADM-2026-5392');

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

DROP TABLE IF EXISTS `details_commande`;
CREATE TABLE IF NOT EXISTS `details_commande` (
  `id_detail` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `quantite_cmd` int NOT NULL,
  `prix_unitaire` float NOT NULL,
  `id_article` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_commande` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_article` (`id_article`),
  KEY `id_commande` (`id_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `details_commande`
--

INSERT INTO `details_commande` (`id_detail`, `quantite_cmd`, `prix_unitaire`, `id_article`, `id_commande`) VALUES
('DTL-2026-1048', 1, 12345, 'ART-2025-3186', 'CMD-2026-7596'),
('DTL-2026-1093', 1, 12345, 'ART-2025-3186', 'CMD-2026-7054'),
('DTL-2026-1119', 1, 300, 'ART-2025-6783', 'CMD-2026-8562'),
('DTL-2026-1144', 2, 5000, 'ART-2025-1532', 'CMD-2026-8015'),
('DTL-2026-1232', 8, 300, 'ART-2025-6783', 'CMD-2026-5508'),
('DTL-2026-1347', 1, 500, 'ART-2025-7014', 'CMD-2026-5024'),
('DTL-2026-1365', 1, 300, 'ART-2025-6783', 'CMD-2026-1631'),
('DTL-2026-1427', 1, 20000, 'ART-2026-7372', 'CMD-2026-5049'),
('DTL-2026-1477', 2, 12345, 'ART-2025-3186', 'CMD-2026-9874'),
('DTL-2026-1486', 1, 300, 'ART-2025-6783', 'CMD-2026-2250'),
('DTL-2026-1688', 1, 12345, 'ART-2025-3186', 'CMD-2026-9637'),
('DTL-2026-1753', 1, 15000, 'ART-2026-1023', 'CMD-2026-3309'),
('DTL-2026-1765', 24, 150, 'ART-2025-7736', 'CMD-2026-9569'),
('DTL-2026-1774', 1, 5000, 'ART-2025-1532', 'CMD-2026-6347'),
('DTL-2026-1859', 1, 12345, 'ART-2025-3186', 'CMD-2026-3619'),
('DTL-2026-1891', 1, 12345, 'ART-2025-3186', 'CMD-2026-4249'),
('DTL-2026-1914', 1, 12345, 'ART-2025-3186', 'CMD-2026-6566'),
('DTL-2026-1979', 1, 5000, 'ART-2025-1532', 'CMD-2026-3619'),
('DTL-2026-2067', 1, 300, 'ART-2025-6783', 'CMD-2026-6340'),
('DTL-2026-2131', 1, 150000, 'ART-2026-7972', 'CMD-2026-5376'),
('DTL-2026-2235', 1, 12345, 'ART-2025-3186', 'CMD-2026-8726'),
('DTL-2026-2310', 1, 12345, 'ART-2025-3186', 'CMD-2026-4085'),
('DTL-2026-2318', 1, 5000, 'ART-2025-1532', 'CMD-2026-4345'),
('DTL-2026-2403', 1, 12345, 'ART-2025-3186', 'CMD-2026-8987'),
('DTL-2026-2505', 1, 9000, 'ART-2025-8169', 'CMD-2026-6566'),
('DTL-2026-2550', 3, 500, 'ART-2025-7014', 'CMD-2026-6003'),
('DTL-2026-2825', 1, 9000, 'ART-2025-8169', 'CMD-2026-1224'),
('DTL-2026-2848', 6, 150, 'ART-2025-7736', 'CMD-2026-9313'),
('DTL-2026-3082', 1, 300, 'ART-2025-6783', 'CMD-2026-6143'),
('DTL-2026-3148', 3, 150, 'ART-2025-7736', 'CMD-2026-3616'),
('DTL-2026-3182', 2, 5000, 'ART-2025-1532', 'CMD-2026-8755'),
('DTL-2026-3243', 2, 150000, 'ART-2026-7972', 'CMD-2026-5067'),
('DTL-2026-3245', 3, 5000, 'ART-2025-1532', 'CMD-2026-6168'),
('DTL-2026-3354', 1, 300, 'ART-2025-6783', 'CMD-2026-7054'),
('DTL-2026-3374', 1, 15000, 'ART-2026-1023', 'CMD-2026-4046'),
('DTL-2026-3390', 9, 5000, 'ART-2025-1532', 'CMD-2026-3309'),
('DTL-2026-3608', 1, 300, 'ART-2025-6783', 'CMD-2026-9331'),
('DTL-2026-3737', 5, 20000, 'ART-2026-7372', 'CMD-2026-5067'),
('DTL-2026-3918', 1, 5000, 'ART-2025-3101', 'CMD-2026-3309'),
('DTL-2026-4031', 7, 150, 'ART-2025-7736', 'CMD-2026-6168'),
('DTL-2026-4347', 2, 5000, 'ART-2025-1532', 'CMD-2026-7596'),
('DTL-2026-4394', 1, 5000, 'ART-2025-1532', 'CMD-2026-9331'),
('DTL-2026-4434', 1, 5000, 'ART-2025-1532', 'CMD-2026-5939'),
('DTL-2026-4459', 1, 20000, 'ART-2026-7372', 'CMD-2026-9206'),
('DTL-2026-4464', 1, 500, 'ART-2025-7014', 'CMD-2026-4742'),
('DTL-2026-4516', 1, 12345, 'ART-2025-3186', 'CMD-2026-8013'),
('DTL-2026-4593', 1, 300, 'ART-2025-6783', 'CMD-2026-4085'),
('DTL-2026-4732', 1, 12345, 'ART-2025-3186', 'CMD-2026-3309'),
('DTL-2026-4744', 2, 1000, 'ART-2025-1218', 'CMD-2026-4742'),
('DTL-2026-4770', 1, 300, 'ART-2025-6783', 'CMD-2026-6961'),
('DTL-2026-4807', 1, 150000, 'ART-2026-7972', 'CMD-2026-3152'),
('DTL-2026-4811', 2, 150, 'ART-2025-7736', 'CMD-2026-1671'),
('DTL-2026-4911', 1, 5000, 'ART-2025-1532', 'CMD-2026-8013'),
('DTL-2026-5042', 1, 12345, 'ART-2025-3186', 'CMD-2026-9276'),
('DTL-2026-5486', 1, 12345, 'ART-2025-3186', 'CMD-2026-5385'),
('DTL-2026-5706', 1, 300, 'ART-2025-6783', 'CMD-2026-9276'),
('DTL-2026-5721', 1, 150, 'ART-2025-7736', 'CMD-2026-3728'),
('DTL-2026-5851', 1, 300, 'ART-2025-6783', 'CMD-2026-8987'),
('DTL-2026-6058', 1, 300, 'ART-2025-6783', 'CMD-2026-3157'),
('DTL-2026-6228', 8, 1000, 'ART-2025-1218', 'CMD-2026-3410'),
('DTL-2026-6263', 1, 150, 'ART-2025-7736', 'CMD-2026-4623'),
('DTL-2026-6367', 1, 300, 'ART-2025-6783', 'CMD-2026-3506'),
('DTL-2026-6377', 1, 300, 'ART-2025-6783', 'CMD-2026-3619'),
('DTL-2026-6407', 1, 9000, 'ART-2025-8169', 'CMD-2026-8726'),
('DTL-2026-6647', 2, 300, 'ART-2025-6783', 'CMD-2026-9874'),
('DTL-2026-6703', 1, 300, 'ART-2025-6783', 'CMD-2026-4249'),
('DTL-2026-6805', 3, 300, 'ART-2025-6783', 'CMD-2026-8755'),
('DTL-2026-6859', 1, 300, 'ART-2025-6783', 'CMD-2026-5122'),
('DTL-2026-6937', 2, 12345, 'ART-2025-3186', 'CMD-2026-8755'),
('DTL-2026-7015', 1, 5000, 'ART-2025-1532', 'CMD-2026-1541'),
('DTL-2026-7050', 1, 1000, 'ART-2025-1218', 'CMD-2026-5024'),
('DTL-2026-7172', 2, 300, 'ART-2025-6783', 'CMD-2026-7596'),
('DTL-2026-7501', 1, 5000, 'ART-2025-1532', 'CMD-2026-6566'),
('DTL-2026-7551', 1, 150, 'ART-2025-7736', 'CMD-2026-5505'),
('DTL-2026-7569', 2, 5000, 'ART-2025-1532', 'CMD-2026-4632'),
('DTL-2026-7736', 1, 9000, 'ART-2025-8169', 'CMD-2026-3309'),
('DTL-2026-7845', 5, 300, 'ART-2025-6783', 'CMD-2026-3309'),
('DTL-2026-8012', 1, 12345, 'ART-2025-3186', 'CMD-2026-6961'),
('DTL-2026-8106', 1, 5000, 'ART-2025-1532', 'CMD-2026-4085'),
('DTL-2026-8405', 1, 12345, 'ART-2025-3186', 'CMD-2026-7803'),
('DTL-2026-8536', 1, 5000, 'ART-2025-1532', 'CMD-2026-8987'),
('DTL-2026-8556', 57, 150, 'ART-2025-7736', 'CMD-2026-3309'),
('DTL-2026-8630', 1, 5000, 'ART-2025-1532', 'CMD-2026-4249'),
('DTL-2026-8692', 1, 5000, 'ART-2025-1532', 'CMD-2026-1191'),
('DTL-2026-8960', 3, 5000, 'ART-2025-1532', 'CMD-2026-9313'),
('DTL-2026-9012', 5, 5000, 'ART-2025-1532', 'CMD-2026-8726'),
('DTL-2026-9038', 1, 5000, 'ART-2025-1532', 'CMD-2026-3506'),
('DTL-2026-9139', 1, 5000, 'ART-2025-1532', 'CMD-2026-9276'),
('DTL-2026-9140', 1, 9000, 'ART-2025-8169', 'CMD-2026-4249'),
('DTL-2026-9153', 1, 5000, 'ART-2025-1532', 'CMD-2026-6006'),
('DTL-2026-9212', 1, 12345, 'ART-2025-3186', 'CMD-2026-3506'),
('DTL-2026-9246', 1, 12345, 'ART-2025-3186', 'CMD-2026-9331'),
('DTL-2026-9313', 1, 5000, 'ART-2025-1532', 'CMD-2026-1671'),
('DTL-2026-9367', 1, 300, 'ART-2025-6783', 'CMD-2026-8013'),
('DTL-2026-9534', 1, 12345, 'ART-2025-3186', 'CMD-2026-8015'),
('DTL-2026-9584', 1, 12345, 'ART-2025-3186', 'CMD-2026-4630'),
('DTL-2026-9654', 1, 300, 'ART-2025-6783', 'CMD-2026-1464'),
('DTL-2026-9731', 1, 5000, 'ART-2025-1532', 'CMD-2026-9036'),
('DTL-2026-9764', 1, 20000, 'ART-2026-7372', 'CMD-2026-6890'),
('DTL-2026-9817', 1, 500, 'ART-2025-7014', 'CMD-2026-3410'),
('DTL-2026-9848', 2, 5000, 'ART-2025-1532', 'CMD-2026-9874'),
('DTL-2026-9858', 1, 12345, 'ART-2025-3186', 'CMD-2026-9770'),
('DTL-2026-9864', 1, 5000, 'ART-2025-1532', 'CMD-2026-7054'),
('DTL-2026-9931', 9, 5000, 'ART-2025-1532', 'CMD-2026-2814'),
('DTL-2026-9936', 1, 5000, 'ART-2025-1532', 'CMD-2026-6961'),
('DTL-2026-9940', 1, 12345, 'ART-2025-3186', 'CMD-2026-2897'),
('DTL-2026-9985', 4, 5000, 'ART-2025-1532', 'CMD-2026-3616');

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

DROP TABLE IF EXISTS `favoris`;
CREATE TABLE IF NOT EXISTS `favoris` (
  `id_favoris` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_client` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_article` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_favoris`),
  KEY `id_client` (`id_client`),
  KEY `id_article` (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `favoris`
--

INSERT INTO `favoris` (`id_favoris`, `date_ajout`, `id_client`, `id_article`) VALUES
('FAV-2025-2122', '2025-12-30 13:33:31', 'CLT-2025-8633', 'ART-2025-3186'),
('FAV-2025-4025', '2025-12-31 12:48:32', 'CLT-2025-8633', 'ART-2025-1532'),
('FAV-2026-2432', '2026-01-05 07:53:39', 'CLT-2026-6908', 'ART-2025-3101'),
('FAV-2026-2444', '2026-02-23 04:03:27', 'CLT-2025-8633', 'ART-2025-6783'),
('FAV-2026-3986', '2026-02-03 10:07:03', 'CLT-2025-8633', 'ART-2025-7014'),
('FAV-2026-5270', '2026-02-21 19:37:23', 'CLT-2025-8633', 'ART-2026-7972'),
('FAV-2026-6169', '2026-03-02 08:12:21', 'CLT-2026-8697', 'ART-2025-1218'),
('FAV-2026-7038', '2026-02-21 19:37:32', 'CLT-2025-8633', 'ART-2026-7372'),
('FAV-2026-7081', '2026-01-05 07:52:36', 'CLT-2026-6908', 'ART-2025-7736'),
('FAV-2026-9755', '2026-01-31 19:45:20', 'CLT-2026-5558', 'ART-2025-7736');

-- --------------------------------------------------------

--
-- Structure de la table `livreur`
--

DROP TABLE IF EXISTS `livreur`;
CREATE TABLE IF NOT EXISTS `livreur` (
  `id_livreur` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `statut` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `profil` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_livreur`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livreur`
--

INSERT INTO `livreur` (`id_livreur`, `statut`, `profil`, `id`) VALUES
('LVR-2026-7313', 'Actif', 'IMG_4960_20260227052552.png', 'USER-2026-7086'),
('LVR-2026-9976', 'Actif', 'IMG_5717_20260302121526.jpg', 'USER-2026-4280');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `id_panier` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Quantite` int NOT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_article` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_client` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_panier`),
  KEY `id_client` (`id_client`),
  KEY `id_article` (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`id_panier`, `Quantite`, `date_ajout`, `id_article`, `id_client`) VALUES
('PAN-2026-1021', 1, '2026-03-23 07:39:43', 'ART-2026-7972', 'CLT-2026-5558'),
('PAN-2026-3210', 1, '2026-03-18 16:42:11', 'ART-2026-7175', 'CLT-2025-8633'),
('PAN-2026-9338', 1, '2026-03-22 10:28:57', 'ART-2025-7014', 'CLT-2025-8633');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pass` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `compte` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `localisation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dateNaiss` date NOT NULL,
  `dateInsc` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `pass`, `tel`, `compte`, `localisation`, `dateNaiss`, `dateInsc`, `reset_token`, `token_expires`, `remember_token`) VALUES
('ADM-2026-5392', 'Yamaga', 'Vadeck', 'admin01@gmail.com', 'admin01', '693301917', 'admin', NULL, '2005-10-29', '2026-02-21 08:18:11', NULL, NULL, NULL),
('USER-2025-1693', 'Morgan', 'Dylan', 'morgan@gmail.com', '1234', '23456765', 'boutiquier', '', '2025-12-04', '2025-12-23 12:28:25', NULL, NULL, NULL),
('USER-2025-3485', 'Patrick', 'Dal', 'dalya@gmail.com', '2010', '692238528', 'client', '', '2025-12-05', '2025-12-28 11:26:50', NULL, NULL, NULL),
('USER-2025-5435', 'Dany', ' Anelka', 'dany@gmail.com', '1234', '692238528', 'boutiquier', '{\"lat\":4.0505,\"long\":9.7022,\"timestamp\":\"2025-12-23T22:57:38.951Z\"}', '2025-12-06', '2025-12-23 22:58:45', NULL, NULL, NULL),
('USER-2025-9509', 'Marine', 'jeanne', 'marine@gmail.com', '2005', '4567898765', 'boutiquier', '', '2025-12-03', '2025-12-24 13:58:25', NULL, NULL, NULL),
('USER-2025-9532', 'Yamaga', 'Vadeck', 'vadeckyamaga@gmail.com', 'vadeck2005', '692238528', 'boutiquier', '{\"lat\":4.09,\"long\":9.74,\"timestamp\":\"2025-12-24T01:58:18.175Z\"}', '2025-12-05', '2025-12-24 01:49:29', 'b4bc77a7b3f8e4b831aa7c5a61a79e1492e614fc2f7fe9253793939642474420', '2026-03-11 13:58:23', NULL),
('USER-2025-9546', 'Dupont', 'Jean', 'Dupont@gmail.com', '2005', '367898765', 'boutiquier', '', '2025-12-02', '2025-12-31 10:26:00', NULL, NULL, NULL),
('USER-2026-2973', 'Yvana', 'Mia', 'yvanamia@gmail.com', 'yvanamia2008', '671495118', 'boutiquier', '', '2008-01-04', '2026-03-23 07:31:23', NULL, NULL, '4ce508a49bcb241427da1c27a53e5076c23c1744967eef7dff49bed2fef59c60'),
('USER-2026-3160', 'Djoyap', 'Sylvie', 'djoyaps@gmail.com', 'sylvie1975', '693301917', 'client', '{\"lat\":3.9790293,\"long\":9.775014,\"timestamp\":\"2026-01-31T19:28:28.832Z\"}', '1975-12-13', '2026-01-31 19:29:47', NULL, NULL, NULL),
('USER-2026-4260', 'William', 'Thevie', 'williamTh@gmail.com', '12345', '675927770', 'boutiquier', '{\"lat\":4.07,\"long\":9.74,\"timestamp\":\"2026-02-21T10:22:53.379Z\"}', '2026-02-07', '2026-02-21 10:23:25', NULL, NULL, NULL),
('USER-2026-4280', 'Montero', 'Richy', 'montero@gmail.com', 'montero2026', '693000340', 'livreur', NULL, '0000-00-00', '2026-03-02 12:15:26', NULL, NULL, NULL),
('USER-2026-4982', 'Geremi guy', 'Timeni tchoudi', 'geremitimeni43@gmail.com', 'Tim237@?', '682709250', 'client', '', '2026-03-22', '2026-03-22 19:59:54', NULL, NULL, NULL),
('USER-2026-5823', 'Saint-Laurent', 'Yves', 'yves@gmail.com', 'yves2026', '786778556', 'client', '{\"lat\":4.0505,\"long\":9.7022,\"timestamp\":\"2026-03-22T19:23:30.277Z\"}', '2026-03-06', '2026-03-22 19:24:38', NULL, NULL, NULL),
('USER-2026-5865', 'Neybi', 'Dior', 'dior04@gmail.com', '1a2b', '657599386', 'client', '', '2006-12-22', '2026-03-22 19:35:59', NULL, NULL, NULL),
('USER-2026-5900', 'ert', 'poi', 'rtyu@gmail.com', '54321', '699887766', 'client', '', '2026-02-22', '2026-02-22 13:46:38', NULL, NULL, NULL),
('USER-2026-6069', 'Miguel', 'Daruis', 'migueldarius766@gmail.com', 'Merci12345', '640930486', 'client', '', '2006-06-20', '2026-03-02 08:11:24', NULL, NULL, NULL),
('USER-2026-7006', 'Tiche', 'Chelsea', 'tichetchouanchec@gmail.com', '1sergele', '697935271', 'client', '', '2005-12-07', '2026-01-31 19:33:30', NULL, NULL, NULL),
('USER-2026-7086', 'Dupont', 'Jean', 'jeandupont@gmail.com', 'jean2026', '677246469', 'livreur', NULL, '0000-00-00', '2026-02-27 17:25:52', NULL, NULL, NULL),
('USER-2026-7747', 'John', 'Doe', 'johndoe@gmail.com', '12345', '693301917', 'boutiquier', '{\"lat\":4.07,\"long\":9.74,\"timestamp\":\"2026-02-21T10:49:12.002Z\"}', '2026-02-05', '2026-02-21 10:50:26', NULL, NULL, NULL),
('USER-2026-7789', 'DYM', 'MIGUEL', 'dym@gmail.com', '1234', '679806237', 'client', '{\"lat\":4.042846,\"long\":9.753228,\"timestamp\":\"2026-01-05T07:46:01.324Z\"}', '2023-07-06', '2026-01-05 07:48:45', NULL, NULL, NULL),
('USER-2026-9380', 'Nkwentie', 'Robert', 'rnkwentie@gmail.com', 'robert1966', '677246469', 'boutiquier', '', '1966-01-08', '2026-01-31 19:31:35', NULL, NULL, NULL),
('USER-2026-9587', 'Morgan', 'Dylan', 'Morgan@dy.com', '12345', '656828393', 'boutiquier', '{\"lat\":4.07,\"long\":9.74,\"timestamp\":\"2026-02-21T10:05:02.048Z\"}', '2000-09-07', '2026-02-21 10:16:03', NULL, NULL, NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`id_cat`) REFERENCES `catégorie` (`id_cat`),
  ADD CONSTRAINT `article_ibfk_2` FOREIGN KEY (`id_commerçant`) REFERENCES `commerçant` (`id_commerçant`);

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`id_commerçant`) REFERENCES `commerçant` (`id_commerçant`),
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`),
  ADD CONSTRAINT `avis_ibfk_3` FOREIGN KEY (`id_livreur`) REFERENCES `livreur` (`id_livreur`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`),
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`id_commerçant`) REFERENCES `commerçant` (`id_commerçant`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `commande_ibfk_3` FOREIGN KEY (`id_livreur`) REFERENCES `livreur` (`id_livreur`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `commerçant`
--
ALTER TABLE `commerçant`
  ADD CONSTRAINT `commerçant_ibfk_1` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `fk_admin_createur_commercant` FOREIGN KEY (`ajoute_par`) REFERENCES `utilisateur` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD CONSTRAINT `details_commande_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`),
  ADD CONSTRAINT `details_commande_ibfk_2` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`);

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`),
  ADD CONSTRAINT `favoris_ibfk_2` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `livreur`
--
ALTER TABLE `livreur`
  ADD CONSTRAINT `livreur_ibfk_1` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
