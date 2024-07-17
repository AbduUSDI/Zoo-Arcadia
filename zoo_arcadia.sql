-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 14 juil. 2024 à 18:37
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `zoo_arcadia`
--
CREATE DATABASE IF NOT EXISTS `zoo_arcadia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `zoo_arcadia`;

-- --------------------------------------------------------

--
-- Structure de la table `animals`
--

CREATE TABLE `animals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `species` varchar(255) NOT NULL,
  `habitat_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `animal_id` int(11) DEFAULT NULL,
  `habitat_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `food`
--

CREATE TABLE `food` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) DEFAULT NULL,
  `food_given` varchar(255) DEFAULT NULL,
  `food_quantity` int(11) DEFAULT NULL,
  `date_given` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `habitats`
--

CREATE TABLE `habitats` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `habitat_comments`
--

CREATE TABLE `habitat_comments` (
  `id` int(11) NOT NULL,
  `habitat_id` int(11) NOT NULL,
  `vet_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subject` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `visitor_name` varchar(255) DEFAULT NULL,
  `animal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'employé'),
(3, 'vétérinaire');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role_id`, `email`) VALUES
(1, 'Administrateur', '$2y$10$fw1Y2d.N1Tm2s4HlE7Gzk.5f3GTMy2jYklKfHhDnrkRjBFvEA1O82', 1, 'admin.arcadia@gmail.com'),
(2, 'Vétérinaire', '$2y$10$AsF/0agVAcFxJ9w5mfvwS.Ye//rr5AlB0VlIYDaXNER3u1A7p9lrK', 3, 'vet.arcadia@gmail.com'),
(3, 'Employé', '$2y$10$0.6u0AQ2GeCZEOijPWJPCedfdMqMQ.vABd0xl7jWfeSfKOpWBWYFi', 2, 'employee.arcadia@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `vet_reports`
--

CREATE TABLE `vet_reports` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `vet_id` int(11) NOT NULL,
  `health_status` text NOT NULL,
  `food_given` text NOT NULL,
  `food_quantity` varchar(100) NOT NULL,
  `visit_date` date NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `zoo_hours`
--

CREATE TABLE `zoo_hours` (
  `id` int(11) NOT NULL,
  `day` varchar(255) NOT NULL,
  `open_time` time NOT NULL,
  `close_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `zoo_hours`
--

INSERT INTO `zoo_hours` (`id`, `day`, `open_time`, `close_time`) VALUES
(1, 'Lundi', '11:00:00', '18:00:00'),
(2, 'Mardi', '08:00:00', '18:00:00'),
(3, 'Mercredi', '08:00:00', '18:00:00'),
(4, 'Jeudi', '08:00:00', '18:00:00'),
(5, 'Vendredi', '08:00:00', '18:00:00'),
(6, 'Samedi', '10:00:00', '18:00:00'),
(7, 'Dimanche', '11:00:00', '16:00:00');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habitat_id` (`habitat_id`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `habitat_id` (`habitat_id`);

--
-- Index pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `food`
--
ALTER TABLE `food`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `habitats`
--
ALTER TABLE `habitats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Index pour la table `habitat_comments`
--
ALTER TABLE `habitat_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habitat_id` (`habitat_id`),
  ADD KEY `vet_id` (`vet_id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- Index pour la table `vet_reports`
--
ALTER TABLE `vet_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `vet_id` (`vet_id`);

--
-- Index pour la table `zoo_hours`
--
ALTER TABLE `zoo_hours`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `animals`
--
ALTER TABLE `animals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `food`
--
ALTER TABLE `food`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `habitats`
--
ALTER TABLE `habitats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `habitat_comments`
--
ALTER TABLE `habitat_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `vet_reports`
--
ALTER TABLE `vet_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `zoo_hours`
--
ALTER TABLE `zoo_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `animals`
--
ALTER TABLE `animals`
  ADD CONSTRAINT `animals_ibfk_1` FOREIGN KEY (`habitat_id`) REFERENCES `habitats` (`id`);

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`habitat_id`) REFERENCES `habitats` (`id`);

--
-- Contraintes pour la table `habitat_comments`
--
ALTER TABLE `habitat_comments`
  ADD CONSTRAINT `habitat_comments_ibfk_1` FOREIGN KEY (`habitat_id`) REFERENCES `habitats` (`id`),
  ADD CONSTRAINT `habitat_comments_ibfk_2` FOREIGN KEY (`vet_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Contraintes pour la table `vet_reports`
--
ALTER TABLE `vet_reports`
  ADD CONSTRAINT `vet_reports_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vet_reports_ibfk_2` FOREIGN KEY (`vet_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
