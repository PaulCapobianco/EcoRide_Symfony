-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 10 déc. 2025 à 11:10
-- Version du serveur : 8.0.44-0ubuntu0.24.04.2
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`avis_id`, `utilisateur_id`, `commentaire`, `note`, `statut`, `covoiturage_id`) VALUES
(1, 3, 'Trajet super sympa, conduite fluide.', 4.8, 'VALIDE', 1),
(2, 3, 'Ponctuelle et voiture propre, je recommande.', 5, 'VALIDE', 2),
(3, 3, 'Très agréable, bonne ambiance à bord.', 4.7, 'VALIDE', 3),
(4, 4, 'Bonne conduite, un peu de retard au départ.', 4.2, 'VALIDE', 4),
(5, 4, 'Trajet conforme, RAS.', 4, 'VALIDE', 5),
(6, 4, 'Sympa mais musique un peu forte.', 3.9, 'VALIDE', 6),
(7, 5, 'Clara est top, très ponctuelle.', 4.9, 'VALIDE', 7),
(8, 5, 'Voiture nickel, discussion agréable.', 4.6, 'VALIDE', 8),
(9, 5, 'Trajet sans encombre, je referai.', 4.8, 'VALIDE', 9),
(10, 6, 'Conduite un peu rapide à mon goût.', 3.8, 'VALIDE', 10),
(11, 6, 'Globalement ok pour le prix.', 4, 'VALIDE', 1),
(12, 6, 'Lucas est resté discret mais pro.', 4.1, 'VALIDE', 2),
(13, 7, 'Super trajets avec Sophie, rien à dire.', 4.9, 'VALIDE', 3),
(14, 7, 'Très à l’écoute, pause café appréciée.', 4.7, 'VALIDE', 4),
(15, 7, 'Toujours à l’heure, je recommande.', 5, 'VALIDE', 5),
(16, 8, 'Romain connaît bien la route, trajet rapide.', 4.3, 'VALIDE', 6),
(17, 8, 'Un peu de retard mais prévenu à l’avance.', 4.1, 'VALIDE', 7),
(18, 8, 'Conduite sûre, bonne ambiance.', 4.4, 'VALIDE', 8),
(19, 9, 'Inès est adorable, trajet très agréable.', 4.8, 'VALIDE', 9),
(20, 9, 'Voiture propre, musique sympa.', 4.6, 'VALIDE', 10),
(21, 9, 'Top comme d’habitude.', 4.9, 'VALIDE', 11),
(22, 10, 'Bonne conduite, un peu silencieux.', 4, 'VALIDE', 12),
(23, 10, 'Trajet ok, rien à signaler.', 4.2, 'VALIDE', 13),
(24, 10, 'Prix correct pour la distance.', 4.1, 'VALIDE', 14),
(25, 11, 'Manon est super gentille, je recommande.', 4.9, 'VALIDE', 15),
(26, 11, 'Très ponctuelle et arrangeante.', 4.7, 'VALIDE', 1),
(27, 11, 'On a bien discuté, trajet rapide.', 4.8, 'VALIDE', 2),
(28, 12, 'Conduite un peu nerveuse.', 3.7, 'VALIDE', 3),
(29, 12, 'A fait un détour pour me déposer, top.', 4.3, 'VALIDE', 4),
(30, 12, 'Globalement satisfaisant.', 4, 'VALIDE', 5),
(31, 13, 'Trajet parfait, Léa est très pro.', 5, 'VALIDE', 6),
(32, 13, 'Voiture confortable, bonne musique.', 4.8, 'VALIDE', 7),
(33, 13, 'Toujours à l’heure, je recommande.', 4.9, 'VALIDE', 8),
(34, 17, NULL, 5, 'VALIDE', 25),
(35, 17, 'impeccable', 5, 'VALIDE', 26),
(36, 16, 'Horrible', 0, 'REFUSE', 24),
(37, 16, 'Super chauffeur et agréable !', 5, 'VALIDE', 28),
(38, 16, 'top', 0, 'VALIDE', 29),
(39, 17, 'Super', 5, 'VALIDE', 30);

--
-- Déchargement des données de la table `configuration`
--

INSERT INTO `configuration` (`id_configuration`, `utilisateur_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12),
(13, 13),
(14, 16);

--
-- Déchargement des données de la table `covoiturage`
--

INSERT INTO `covoiturage` (`covoiturage_id`, `utilisateur_id`, `voiture_id`, `date_depart`, `heure_depart`, `lieu_depart`, `date_arrivee`, `heure_arrivee`, `lieu_arrivee`, `statut`, `nb_place`, `prix_personne`, `adresse_depart`, `adresse_arrivee`, `started_at`, `finished_at`) VALUES
(1, 3, 1, '2025-12-20', '08:30:00', 'Lyon', '2025-12-20', '10:30:00', 'Grenoble', 'COMPLET', 0, 12, NULL, NULL, NULL, NULL),
(2, 3, 2, '2025-12-21', '09:00:00', 'Paris', '2025-12-21', '10:45:00', 'Rouen', 'OUVERT', 2, 14, NULL, NULL, NULL, NULL),
(3, 7, 3, '2025-12-22', '18:00:00', 'Bordeaux', '2025-12-22', '20:15:00', 'Toulouse', 'OUVERT', 4, 16, NULL, NULL, NULL, NULL),
(4, 12, 1, '2025-12-23', '08:10:00', 'Marseille', '2025-12-23', '08:50:00', 'Aix-en-Provence', 'COMPLET', 0, 6, NULL, NULL, NULL, NULL),
(5, 3, 4, '2025-12-24', '08:30:00', 'Marseille', '2025-12-24', '09:10:00', 'Aix-en-Provence', 'OUVERT', 3, 7, NULL, NULL, NULL, NULL),
(6, 4, 5, '2025-12-25', '07:15:00', 'Lille', '2025-12-25', '09:30:00', 'Paris', 'OUVERT', 3, 18, NULL, NULL, NULL, NULL),
(7, 5, 6, '2025-12-26', '08:00:00', 'Nantes', '2025-12-26', '09:30:00', 'Rennes', 'OUVERT', 0, 11, NULL, NULL, NULL, NULL),
(8, 6, 7, '2025-12-27', '17:30:00', 'Lyon', '2025-12-27', '19:30:00', 'Grenoble', 'OUVERT', 3, 13, NULL, NULL, NULL, NULL),
(9, 7, 8, '2025-12-28', '09:00:00', 'Paris', '2025-12-28', '10:45:00', 'Rouen', 'OUVERT', 4, 16, NULL, NULL, NULL, NULL),
(10, 8, 9, '2025-12-29', '18:00:00', 'Bordeaux', '2025-12-29', '20:15:00', 'Toulouse', 'OUVERT', 3, 18, NULL, NULL, NULL, NULL),
(11, 9, 10, '2025-12-30', '10:00:00', 'Nice', '2025-12-30', '10:40:00', 'Cannes', 'OUVERT', 2, 8, NULL, NULL, NULL, NULL),
(12, 10, 11, '2025-12-31', '07:45:00', 'Lyon', '2025-12-31', '10:15:00', 'Genève', 'OUVERT', 3, 25, NULL, NULL, NULL, NULL),
(13, 11, 12, '2025-12-02', '08:20:00', 'Montpellier', '2025-12-02', '09:05:00', 'Nîmes', 'OUVERT', 3, 10, NULL, NULL, NULL, NULL),
(14, 12, 13, '2025-12-03', '07:10:00', 'Dijon', '2025-12-03', '08:40:00', 'Besançon', 'OUVERT', 2, 14, NULL, NULL, NULL, NULL),
(15, 13, 14, '2025-12-04', '18:00:00', 'Strasbourg', '2025-12-04', '19:10:00', 'Colmar', 'OUVERT', 1, 12, NULL, NULL, NULL, NULL),
(16, 3, 4, '2025-12-05', '17:15:00', 'Marseille', '2025-12-05', '18:20:00', 'Toulon', 'COMPLET', 0, 10, NULL, NULL, NULL, NULL),
(17, 2, 2, '2025-12-16', '17:00:00', 'Marseille', '2025-12-16', NULL, 'Aix-en-provence', 'ANNULE', 0, 6, NULL, NULL, NULL, NULL),
(18, 2, 3, '2025-12-27', '17:08:00', 'Marseille', '2025-12-27', NULL, 'Grenoble', 'ANNULE', 0, 7, NULL, NULL, NULL, NULL),
(19, 2, 3, '2025-12-28', '18:21:00', 'Lyon', '2025-12-28', NULL, 'Gignac', 'ANNULE', 0, 24, NULL, NULL, NULL, NULL),
(20, 2, 2, '2025-12-27', '21:43:00', 'Marseille', '2025-12-27', '00:43:00', 'Cahors', 'ANNULE', 2, 1, NULL, NULL, NULL, NULL),
(22, 2, 3, '2025-12-31', '21:16:00', 'Lyon', '2025-12-31', NULL, 'Marseille', 'ANNULE', 0, 12, '12 rue Gil 13005Marseill', '5 rue de ju 58009 Grenoble', NULL, NULL),
(23, 2, 3, '2025-12-25', '14:09:00', 'Marseille', '2025-12-25', '19:09:00', 'Grenoble', 'ANNULE', 0, 10, '12 rue Gil 13005Marseill', '5 rue de ju 58009 Grenoble', '2025-12-03 10:10:37', '2025-12-03 10:10:45'),
(24, 2, 3, '2025-12-24', '14:11:00', 'Marseille', '2025-12-24', NULL, 'Marseille', 'TERMINE', 0, 10, '12 rue Gil 13005Marseill', '5 rue de ju 58009 Grenoble', '2025-12-03 10:15:22', '2025-12-03 10:15:24'),
(25, 2, 3, '2025-12-11', '14:57:00', 'Marseille', '2025-12-11', NULL, 'Marseille', 'TERMINE', 0, 12, '12 rue Gil 13005Marseill', '5 rue de ju 58009 Grenoble', '2025-12-03 10:59:44', '2025-12-03 10:59:46'),
(26, 2, 2, '2025-12-24', '15:04:00', 'Marseille', '2025-12-24', NULL, 'Marseille', 'TERMINE', 2, 5, '12 rue Gil 13005Marseill', '5 rue de ju 58009 Grenoble', '2025-12-03 11:07:05', '2025-12-03 11:07:08'),
(27, 2, 1, '2025-12-10', '16:35:00', 'Marseille', '2025-12-10', '17:35:00', 'Marseille', 'ANNULE', 0, 5, '12 rue de la republique 13002 Marseille', '12 rue de la republique 13002 Marseille', NULL, NULL),
(28, 2, 3, '2025-12-17', '16:37:00', 'Marseille', '2025-12-17', NULL, 'Marseille', 'TERMINE', 1, 5, '12 rue de la republique 13002 Marseille', '12 rue de la republique 13002 Marseille', '2025-12-04 14:38:21', '2025-12-04 14:38:24'),
(29, 2, 1, '2025-12-11', '16:46:00', 'Marseille', '2025-12-11', NULL, 'Marseille', 'TERMINE', 0, 5, '12 rue de la republique 13002 Marseille', '12 rue de la republique 13002 Marseille', '2025-12-04 15:14:07', '2025-12-04 15:14:09'),
(30, 2, 1, '2025-12-12', '16:27:00', 'Marseille', '2025-12-12', NULL, 'Marseille', 'TERMINE', 1, 5, '12 rue de la republique 13002 Marseille', '12 rue de la republique 13002 Marseille', '2025-12-05 14:30:10', '2025-12-05 14:36:23'),
(31, 28, 18, '2025-12-14', '12:00:00', 'Marseille', '2025-12-14', '17:00:00', 'Toulouse', 'TERMINE', 2, 15, 'Gare', 'Gare', '2025-12-05 15:59:41', '2025-12-05 15:59:53');

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251201105316', '2025-12-01 10:59:21', 4),
('DoctrineMigrations\\Version20251201130620', '2025-12-01 13:06:32', 77),
('DoctrineMigrations\\Version20251202095544', '2025-12-02 09:57:51', 71),
('DoctrineMigrations\\Version20251203120000', '2025-12-03 08:54:14', 65),
('DoctrineMigrations\\Version20251203123000', '2025-12-03 09:09:13', 138),
('DoctrineMigrations\\Version20251204120000', '2025-12-03 10:45:53', 76),
('DoctrineMigrations\\Version20251205100000', '2025-12-05 10:11:35', 65);

--
-- Déchargement des données de la table `marque`
--

INSERT INTO `marque` (`marque_id`, `libelle`) VALUES
(1, 'Renault'),
(2, 'Peugeot'),
(3, 'Tesla'),
(4, 'Toyota'),
(5, 'Citroën'),
(6, 'Dacia'),
(7, 'Volkswagen'),
(8, 'Audi'),
(9, 'BMW'),
(10, 'Mercedes-Benz'),
(11, 'Opel'),
(12, 'Ford'),
(13, 'Fiat'),
(14, 'Alfa Romeo'),
(15, 'Jeep'),
(16, 'Nissan'),
(17, 'Hyundai'),
(18, 'Kia'),
(19, 'Škoda'),
(20, 'SEAT'),
(21, 'Cupra'),
(22, 'Volvo'),
(23, 'Mini'),
(24, 'Suzuki'),
(25, 'Mazda'),
(26, 'Honda'),
(27, 'Mitsubishi'),
(28, 'Land Rover'),
(29, 'Jaguar'),
(30, 'Porsche'),
(31, 'DS Automobiles'),
(32, 'BYD'),
(33, 'MG'),
(34, 'Polestar'),
(35, 'Smart'),
(36, 'Aiways'),
(37, 'Lexus'),
(38, 'Subaru'),
(39, 'Saab'),
(40, 'Chevrolet'),
(41, 'Chrysler'),
(42, 'Dodge'),
(43, 'Infiniti'),
(44, 'Lancia'),
(45, 'Maserati'),
(46, 'Ferrari'),
(47, 'Lamborghini'),
(48, 'Bentley'),
(49, 'Rolls-Royce'),
(50, 'Aston Martin'),
(51, 'Bugatti');

--
-- Déchargement des données de la table `messenger_messages`
--

INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
(1, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;s:188:\\\"Bonjour Capobianco,\n\nLe trajet Lyon → Gignac du 28/12/2025 à 18:21 a été annulé par le conducteur.\n\nVos crédits ont été recrédités sur votre compte EcoRide.\n\nL’équipe EcoRide\\\";i:1;s:5:\\\"utf-8\\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"no-reply@ecoride.test\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:13:\\\"jimjim@sfr.fr\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:53:\\\"Annulation d’un covoiturage auquel vous participiez\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2025-12-02 15:33:58', '2025-12-02 15:33:58', NULL),
(2, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;s:49:\\\"Ceci est un email de test envoyé depuis EcoRide.\\\";i:1;s:5:\\\"utf-8\\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"no-reply@ecoride.test\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:18:\\\"test@ecoride.local\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:12:\\\"Test Mailpit\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2025-12-02 15:35:57', '2025-12-02 15:35:57', NULL),
(3, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;s:49:\\\"Ceci est un email de test envoyé depuis EcoRide.\\\";i:1;s:5:\\\"utf-8\\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"no-reply@ecoride.test\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:18:\\\"test@ecoride.local\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:12:\\\"Test Mailpit\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2025-12-02 15:38:32', '2025-12-02 15:38:32', NULL),
(4, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;s:49:\\\"Ceci est un email de test envoyé depuis EcoRide.\\\";i:1;s:5:\\\"utf-8\\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"no-reply@ecoride.test\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:18:\\\"test@ecoride.local\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:12:\\\"Test Mailpit\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2025-12-02 15:42:06', '2025-12-02 15:42:06', NULL),
(5, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;s:40:\\\"Ceci est un test Mailpit depuis EcoRide.\\\";i:1;s:5:\\\"utf-8\\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"no-reply@ecoride.test\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:16:\\\"test@example.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:12:\\\"Test Mailpit\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2025-12-02 15:49:28', '2025-12-02 15:49:28', NULL),
(6, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;s:40:\\\"Ceci est un test Mailpit depuis EcoRide.\\\";i:1;s:5:\\\"utf-8\\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"no-reply@ecoride.test\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:16:\\\"test@example.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:20:\\\"Test Mailpit EcoRide\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2025-12-02 15:52:17', '2025-12-02 15:52:17', NULL),
(7, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;s:40:\\\"Ceci est un test Mailpit depuis EcoRide.\\\";i:1;s:5:\\\"utf-8\\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"no-reply@ecoride.test\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:16:\\\"test@example.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:20:\\\"Test Mailpit EcoRide\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2025-12-02 15:52:24', '2025-12-02 15:52:24', NULL);

--
-- Déchargement des données de la table `parametre`
--

INSERT INTO `parametre` (`parametre_id`, `id_configuration`, `propriete`, `valeur`) VALUES
(6, 1, 'MUSIQUE', '1'),
(7, 1, 'CLIM', '1'),
(8, 1, 'ANIMAUX', '0'),
(9, 1, 'BAGAGES', '1'),
(10, 1, 'NON_FUMEUR', '1'),
(16, 3, 'MUSIQUE', '1'),
(17, 3, 'CLIM', '0'),
(18, 3, 'ANIMAUX', '0'),
(19, 3, 'BAGAGES', '1'),
(20, 3, 'NON_FUMEUR', '1'),
(21, 4, 'MUSIQUE', '0'),
(22, 4, 'CLIM', '1'),
(23, 4, 'ANIMAUX', '0'),
(24, 4, 'BAGAGES', '1'),
(25, 4, 'NON_FUMEUR', '0'),
(26, 5, 'MUSIQUE', '1'),
(27, 5, 'CLIM', '1'),
(28, 5, 'ANIMAUX', '1'),
(29, 5, 'BAGAGES', '0'),
(30, 5, 'NON_FUMEUR', '1'),
(31, 6, 'MUSIQUE', '1'),
(32, 6, 'CLIM', '0'),
(33, 6, 'ANIMAUX', '1'),
(34, 6, 'BAGAGES', '1'),
(35, 6, 'NON_FUMEUR', '0'),
(36, 7, 'MUSIQUE', '0'),
(37, 7, 'CLIM', '1'),
(38, 7, 'ANIMAUX', '0'),
(39, 7, 'BAGAGES', '0'),
(40, 7, 'NON_FUMEUR', '1'),
(41, 8, 'MUSIQUE', '1'),
(42, 8, 'CLIM', '1'),
(43, 8, 'ANIMAUX', '0'),
(44, 8, 'BAGAGES', '1'),
(45, 8, 'NON_FUMEUR', '0'),
(46, 9, 'MUSIQUE', '1'),
(47, 9, 'CLIM', '1'),
(48, 9, 'ANIMAUX', '1'),
(49, 9, 'BAGAGES', '1'),
(50, 9, 'NON_FUMEUR', '0'),
(51, 10, 'MUSIQUE', '0'),
(52, 10, 'CLIM', '0'),
(53, 10, 'ANIMAUX', '0'),
(54, 10, 'BAGAGES', '1'),
(55, 10, 'NON_FUMEUR', '1'),
(56, 11, 'MUSIQUE', '1'),
(57, 11, 'CLIM', '0'),
(58, 11, 'ANIMAUX', '1'),
(59, 11, 'BAGAGES', '0'),
(60, 11, 'NON_FUMEUR', '1'),
(61, 12, 'MUSIQUE', '0'),
(62, 12, 'CLIM', '1'),
(63, 12, 'ANIMAUX', '1'),
(64, 12, 'BAGAGES', '1'),
(65, 12, 'NON_FUMEUR', '1'),
(66, 13, 'MUSIQUE', '1'),
(67, 13, 'CLIM', '1'),
(68, 13, 'ANIMAUX', '0'),
(69, 13, 'BAGAGES', '0'),
(70, 13, 'NON_FUMEUR', '1'),
(71, 2, 'driver_pref_music', '0'),
(72, 2, 'driver_pref_ac', '0'),
(73, 2, 'driver_pref_pets', '0'),
(74, 2, 'driver_pref_baggage', '0'),
(75, 2, 'driver_pref_no_smoking', '1'),
(76, 2, 'driver_pref_custom', 'Pas d\'enfants'),
(87, 2, 'pref_pets', '1'),
(88, 2, 'music', '1'),
(92, 14, 'pets', '1'),
(94, 2, 'ac', '1');

--
-- Déchargement des données de la table `participation`
--

INSERT INTO `participation` (`id`, `covoiturage_id`, `utilisateur_id`, `nb_places`, `created_at`, `confirmation_status`, `confirmation_comment`, `confirmation_at`) VALUES
(1, 7, 2, 1, '2025-12-01 11:54:28', 'PENDING', NULL, NULL),
(2, 7, 2, 1, '2025-12-01 14:58:37', 'PENDING', NULL, NULL),
(5, 1, 2, 1, '2025-12-02 09:13:05', 'PENDING', NULL, NULL),
(8, 16, 2, 1, '2025-12-02 16:11:49', 'PENDING', NULL, NULL),
(9, 19, 16, 1, '2025-12-02 16:33:37', 'PENDING', NULL, NULL),
(10, 17, 16, 1, '2025-12-03 09:15:30', 'PENDING', NULL, NULL),
(11, 18, 16, 1, '2025-12-03 09:22:29', 'PENDING', NULL, NULL),
(12, 24, 16, 1, '2025-12-03 10:14:05', 'REFUSED', 'Horrible', '2025-12-03 11:11:28'),
(13, 25, 17, 1, '2025-12-03 10:59:27', 'VALIDATED', NULL, '2025-12-03 11:08:44'),
(14, 26, 17, 1, '2025-12-03 11:06:16', 'VALIDATED', 'impeccable', '2025-12-03 11:08:43'),
(15, 27, 16, 1, '2025-12-04 14:35:59', 'PENDING', NULL, NULL),
(16, 28, 16, 1, '2025-12-04 14:37:55', 'VALIDATED', 'Super chauffeur et agréable !', '2025-12-04 14:47:45'),
(17, 29, 16, 1, '2025-12-04 15:11:51', 'VALIDATED', 'top', '2025-12-04 15:14:49'),
(18, 15, 2, 1, '2025-12-05 12:03:03', 'PENDING', NULL, NULL),
(19, 15, 2, 1, '2025-12-05 12:08:07', 'PENDING', NULL, NULL),
(20, 30, 17, 1, '2025-12-05 14:29:51', 'VALIDATED', 'Super | [Crédit manuel] Désolé pour le désagrément', '2025-12-05 14:43:11'),
(21, 1, 28, 1, '2025-12-05 15:41:32', 'PENDING', NULL, NULL),
(22, 31, 2, 1, '2025-12-05 15:57:41', 'PENDING', NULL, NULL),
(23, 16, 28, 1, '2025-12-05 16:02:42', 'PENDING', NULL, NULL),
(24, 4, 1, 1, '2025-12-05 16:15:42', 'PENDING', NULL, NULL);

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`role_id`, `libelle`) VALUES
(1, 'ROLE_USER'),
(2, 'ROLE_EMPLOYE'),
(3, 'ROLE_ADMIN');

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`utilisateur_id`, `role_id`, `nom`, `prenom`, `email`, `password`, `telephone`, `adresse`, `date_naissance`, `pseudo`, `credit`, `profil_type`, `photo_profil_nom`, `photo_profil_update_at`, `email_verifie`, `verification_token`, `verification_requested_at`, `pending_email`, `email_verified_at`, `actif`, `reset_password_token`, `reset_requested_at`) VALUES
(1, 3, 'Admin', 'EcoRide', 'admin@ecoride.fr', '$2y$10$zbqPnwVSTWsAdY6lOf93G..VO58.jG0QUXTHSObJHOU1khYTeQ6O.', NULL, NULL, NULL, 'admin', 9994, 'passenger', '8614787-6932a003136d9400370356.jpg', '2025-12-05 10:04:03', 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(2, 1, 'User', 'EcoRide', 'user@ecoride.fr', '$2y$10$zbqPnwVSTWsAdY6lOf93G..VO58.jG0QUXTHSObJHOU1khYTeQ6O.', '0102030405', '17 rue d\'igloo', '1994-02-17', 'Utilisateur EcoRide', 11246, 'both', '8614787-69340682a2d97709823602.jpg', '2025-12-06 11:33:38', 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(3, 1, 'Moreau', 'Julie', 'julie.moreau@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000010', '3 Rue des Capucins, 69001 Lyon', '1993-05-14', 'JulieM', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(4, 1, 'Bernard', 'Thomas', 'thomas.bernard@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000011', '12 Rue Nationale, 59800 Lille', '1989-11-02', 'ThomasB', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(5, 1, 'Petit', 'Clara', 'clara.petit@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000012', '7 Rue du Faubourg, 21000 Dijon', '1995-08-23', 'ClaraP', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(6, 1, 'Robert', 'Lucas', 'lucas.robert@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000013', '25 Boulevard de la Liberté, 35000 Rennes', '1992-02-10', 'LucasR', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(7, 1, 'Garcia', 'Sophie', 'sophie.garcia@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000014', '4 Rue Saint-Ferréol, 13001 Marseille', '1994-09-05', 'SophieG', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(8, 1, 'Fontaine', 'Romain', 'romain.fontaine@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000015', '15 Rue Sainte-Catherine, 33000 Bordeaux', '1986-06-18', 'RomainF', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(9, 1, 'Lambert', 'Inès', 'ines.lambert@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000016', '8 Rue de la République, 34000 Montpellier', '1997-01-27', 'InesL', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(10, 1, 'Marchand', 'Pierre', 'pierre.marchand@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000017', '2 Place Kléber, 67000 Strasbourg', '1985-03-03', 'PierreM', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(11, 1, 'Perrin', 'Manon', 'manon.perrin@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000018', '11 Rue des Arts, 31000 Toulouse', '1996-12-11', 'ManonP', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(12, 1, 'Blanchard', 'Antoine', 'antoine.blanchard@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000019', '6 Rue de Béthune, 59000 Lille', '1991-04-29', 'AntoineB', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(13, 1, 'Caron', 'Léa', 'lea.caron@ecoride.test', '$2y$13$qod9QTXt/vfJqGS3z./f3OgGkSr.Dv0/DLt6PUGxW6I2M/SQNdCzC', '0600000020', '9 Avenue Jean Jaurès, 44000 Nantes', '1998-07-07', 'LeaC', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(14, 1, 'Capobianco', 'Paul', 'paul@ecoride.fr', '$2y$13$Am8uyH5lC3t7J.ojHWmbTOKGwRuB3jkvYbg2HL34Zx.qBSC1Y2SBa', '+33629746421', '7 Allée Cervantes, 13009 Marseille', '1994-02-17', 'Paul', 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(15, 1, 'Paul', 'Capobianc', 'paul.capobianco@ecoride.com', '$2y$13$kcnR9rbUfW6TYLmmbhezI.qXx1zNLCVOSW/PGfzy.KB1uuAo1QpSC', NULL, ',', NULL, NULL, 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(16, 2, 'Employé', 'EcoRide', 'employe@ecoride.fr', '$2y$10$zbqPnwVSTWsAdY6lOf93G..VO58.jG0QUXTHSObJHOU1khYTeQ6O.', NULL, '7 rue de noel, 13009 Marseille', NULL, NULL, 10050, 'both', '504-6932a02e0bec6621277566.jpg', '2025-12-05 10:04:46', 1, NULL, NULL, NULL, '2025-12-05 10:05:51', 1, NULL, NULL),
(17, 1, 'Laura', 'Bertoni', 'lauraberto@ecoride.com', '$2y$13$yFR3vrZAKBEkp350Vj098eMEtM8B.sEpcrf1Hq1o5ScOjyjmIcp2i', NULL, '15 rue de la soif', NULL, NULL, 20000, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, '2025-12-03 09:58:04', 1, NULL, NULL),
(27, 2, 'Doe', 'John', 'john@ecoride.fr', '$2y$13$RlPRalW5hlfJYoaTbaivRuk55vus.9k4MHocO/a8cGYuYDOsGsxLy', NULL, NULL, NULL, NULL, 20, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(28, 1, 'Zidane', 'Zinedine', 'zizou@mail.com', '$2y$13$f2S9J74BaMfFUXfnQTs7D.TvQqb/p7n0rlDpVePIivOsOEeOlKeeS', NULL, '13 avenue de Marseille, 13013 Marseille', NULL, 'Zizou', 98, 'both', NULL, NULL, 1, NULL, NULL, NULL, '2025-12-05 15:40:45', 1, NULL, NULL),
(29, 2, 'Derulo', 'Jason', 'jason.derulo@mail.com', '$2y$13$MbIy4CAcvoo5XR4UHLkjsugfKMA8U95C9.0C65qYggtVKI76H4F2C', NULL, NULL, NULL, NULL, 0, 'passenger', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL);

--
-- Déchargement des données de la table `voiture`
--

INSERT INTO `voiture` (`voiture_id`, `utilisateur_id`, `marque_id`, `modele`, `immatriculation`, `energie`, `couleur`, `date_premiere_immatriculation`) VALUES
(1, 2, 1, 'Clio V', 'AB-123-CD', 'Essence', 'Bleu', '2020-03-15'),
(2, 2, 2, '308 Hybrid', 'EF-456-GH', 'Hybride', 'Gris', '2022-05-10'),
(3, 2, 3, 'Model 3', 'IJ-789-KL', 'Électrique', 'Blanc', '2023-06-20'),
(4, 3, 1, 'Clio V', 'JM-101-AA', 'Essence', 'Rouge', '2018-03-12'),
(5, 4, 2, '308', 'TB-102-BB', 'Diesel', 'Gris', '2017-09-30'),
(6, 5, 1, 'Twingo', 'CP-103-CC', 'Essence', 'Bleu', '2019-05-20'),
(7, 6, 2, '208', 'LR-104-DD', 'Essence', 'Noir', '2020-03-15'),
(8, 7, 3, 'Model 3', 'SG-105-EE', 'Électrique', 'Blanc', '2022-01-10'),
(9, 8, 2, '3008', 'RF-106-FF', 'Hybride', 'Gris', '2021-09-05'),
(10, 9, 1, 'Zoé', 'IL-107-GG', 'Électrique', 'Vert', '2020-11-22'),
(11, 10, 3, 'Model Y', 'PM-108-HH', 'Électrique', 'Bleu nuit', '2023-02-14'),
(12, 11, 2, '208', 'MP-109-II', 'Essence', 'Jaune', '2019-07-07'),
(13, 12, 1, 'Mégane', 'AB-110-JJ', 'Diesel', 'Gris foncé', '2016-04-18'),
(14, 13, 2, '308', 'LC-111-KK', 'Hybride', 'Rouge', '2021-08-30'),
(17, 16, 4, 'Tesla', 'DE-562-FC', 'Électrique', 'Noire', '01/03/20'),
(18, 28, 1, 'Clio', 'OM-123-OM', 'Diesel', 'Rouge', '01/03/20');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
