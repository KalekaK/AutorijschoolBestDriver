-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2026 at 10:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `autorijschoolbestdriver`
--

-- --------------------------------------------------------

--
-- Table structure for table `auto`
--

CREATE TABLE `auto` (
  `Auto_id` int(10) NOT NULL,
  `Merk` varchar(255) NOT NULL,
  `Model` varchar(255) NOT NULL,
  `Kenteken` varchar(255) NOT NULL,
  `SoortSoort_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auto`
--

INSERT INTO `auto` (`Auto_id`, `Merk`, `Model`, `Kenteken`, `SoortSoort_id`) VALUES
(1, 'Volkswagen', 'Golf', 'AB-123-C', 1),
(2, 'Toyota', 'Yaris', 'CD-456-E', 2),
(4, 'DAF', 'DAF XF 450', '92-XS-X8', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gebruiker`
--

CREATE TABLE `gebruiker` (
  `Gebruiker_id` int(10) NOT NULL,
  `Gebruikersnaam` varchar(255) NOT NULL,
  `Wachtwoord` varchar(255) NOT NULL,
  `Voornaam` varchar(255) NOT NULL,
  `Tussenvoegsel` varchar(255) NOT NULL,
  `Achternaam` varchar(255) NOT NULL,
  `Rol` int(1) NOT NULL,
  `Examenformatie` text NOT NULL,
  `Actief` int(1) NOT NULL,
  `Geslaagd` int(1) NOT NULL,
  `Adres` varchar(255) DEFAULT NULL,
  `Ophaaladres` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Telefoon` varchar(50) DEFAULT NULL,
  `RegistratieDatum` date DEFAULT NULL,
  `Geboortedatum` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gebruiker`
--

INSERT INTO `gebruiker` (`Gebruiker_id`, `Gebruikersnaam`, `Wachtwoord`, `Voornaam`, `Tussenvoegsel`, `Achternaam`, `Rol`, `Examenformatie`, `Actief`, `Geslaagd`, `Adres`, `Ophaaladres`, `Email`, `Telefoon`, `RegistratieDatum`, `Geboortedatum`) VALUES
(4, 'admin_01', '$2y$10$aqEO3zzbBUXH/0.Ua/V31uUBBi0wLbLuxwhmnyBJVqD8QOthxEE2a', 'Admin', '', 'BestDriver', 1, '', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'dominik123', '$2y$10$mwWQVNccxpbwztYLr2iLu.vJWjd2rQqsIr58LLngMoIBDfr9PazZS', 'Dominik', '', 'Bulla', 3, '', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'admin_01', '$2y$10$7O8z.1dGiGxBbhL58uzZVOmm0hCRzO4DS1fCTo4ju9gx5h498vnQ6', 'Dominik', '', 'Admin', 1, '', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'instr_01', '$2y$10$CtkP8MYeA32K1IQN3RjhTuhjYjCAeotDERVZmICbGebXQWocNRfiC', 'Mark', 'van', 'Jansen', 2, '', 1, 0, 'appel straat 123', '', 'testgmail@outlook.com', '06676767676', '2026-04-08', '2002-02-08'),
(8, 'instr_02', '$2y$10$CtkP8MYeA32K1IQN3RjhTuhjYjCAeotDERVZmICbGebXQWocNRfiC', 'Sanne', '', 'Bakker', 2, '', 1, 0, 'Teststraat 1212', '', 'test@gmail.com', '06767676767', '2026-04-08', '1998-02-20'),
(9, 'klant_01', '$2y$10$78tU2mVQveO8CMAZpPCaGObzJEpidzOrdHDRK9Y3RNPwMGLERVDRW', 'Lisa', '', 'Vermeer', 3, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'klant_02', '$2y$10$78tU2mVQveO8CMAZpPCaGObzJEpidzOrdHDRK9Y3RNPwMGLERVDRW', 'Henry', '', 'Smit', 3, '', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'krishna123', '$2y$10$iGxKGmZP6jxkjUXXkHzqOePJ2b9Ag9mepFrfqCE0AwIXVukcJmchi', 'Krishna', '', 'Sardarsing', 3, '', 1, 0, 'Teststraat 413', 'Stationsplein 1', 'krishnatest@gmail.com', '067177131', '2026-04-08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gebruiker_lespakket`
--

CREATE TABLE `gebruiker_lespakket` (
  `Gebruiker_Lespakket_id` int(10) NOT NULL,
  `GebruikerGebruiker_id` int(10) NOT NULL,
  `LespakketLespakket_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gebruiker_lespakket`
--

INSERT INTO `gebruiker_lespakket` (`Gebruiker_Lespakket_id`, `GebruikerGebruiker_id`, `LespakketLespakket_id`) VALUES
(1, 9, 2),
(2, 10, 1),
(3, 5, 2),
(4, 10, 3),
(5, 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `les`
--

CREATE TABLE `les` (
  `Les_id` int(10) NOT NULL,
  `Lestijd` datetime NOT NULL,
  `OphaallocatieOphaallocatie_id` int(10) NOT NULL,
  `Instructeur_id` int(10) NOT NULL,
  `Doel` text NOT NULL,
  `Opmerking_student` text NOT NULL,
  `Opmerking_instructeur` text NOT NULL,
  `Lespakket_id` int(10) NOT NULL,
  `Geannuleerd` int(1) NOT NULL,
  `RedenAnnuleren` text NOT NULL,
  `AutoAuto_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `les`
--

INSERT INTO `les` (`Les_id`, `Lestijd`, `OphaallocatieOphaallocatie_id`, `Instructeur_id`, `Doel`, `Opmerking_student`, `Opmerking_instructeur`, `Lespakket_id`, `Geannuleerd`, `RedenAnnuleren`, `AutoAuto_id`) VALUES
(1, '2026-04-10 11:41:58', 1, 7, 'Basis sturen', '', '', 1, 0, '', 1),
(2, '2026-04-13 11:41:58', 2, 8, 'Parkeren oefenen', '', '', 2, 1, 'Instructeur ziek', 2),
(3, '2026-04-23 11:43:00', 2, 8, 'Parkeren', '', '', 3, 0, '', 2),
(4, '2026-04-23 15:03:00', 1, 7, 'Vrachtwagen rijbewijs', '', '', 4, 1, 'Geen instructeur meer', 4),
(5, '2026-04-08 17:50:00', 2, 8, 'parkeren', '', '', 5, 0, '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `lespakket`
--

CREATE TABLE `lespakket` (
  `Lespakket_id` int(10) NOT NULL,
  `Naam` varchar(100) NOT NULL,
  `Omschrijving` text NOT NULL,
  `Aantal` int(3) NOT NULL,
  `Prijs` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lespakket`
--

INSERT INTO `lespakket` (`Lespakket_id`, `Naam`, `Omschrijving`, `Aantal`, `Prijs`) VALUES
(1, 'Auto', 'Auto rijbewijs', 1, 55.00),
(2, 'Motor', 'Motor rijbewijs', 10, 520.00),
(3, 'Groot', 'Vrachtwagen rijbewijs pakket', 1, 0.00),
(4, 'BE', 'bijzondere rijbewijs pakket', 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `les_onderwerp`
--

CREATE TABLE `les_onderwerp` (
  `LesLes_id` int(10) NOT NULL,
  `OnderwerpOnderwerp_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `les_onderwerp`
--

INSERT INTO `les_onderwerp` (`LesLes_id`, `OnderwerpOnderwerp_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `onderwerp`
--

CREATE TABLE `onderwerp` (
  `Onderwerp_id` int(10) NOT NULL,
  `Onderwerp` varchar(255) NOT NULL,
  `Omschrijving` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onderwerp`
--

INSERT INTO `onderwerp` (`Onderwerp_id`, `Onderwerp`, `Omschrijving`) VALUES
(1, 'Parkeren', 'Fileparkeren en achteruit inparkeren.');

-- --------------------------------------------------------

--
-- Table structure for table `ophaallocatie`
--

CREATE TABLE `ophaallocatie` (
  `Ophaallocatie_id` int(10) NOT NULL,
  `Adres` varchar(255) NOT NULL,
  `Postcode` varchar(6) NOT NULL,
  `Plaats` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ophaallocatie`
--

INSERT INTO `ophaallocatie` (`Ophaallocatie_id`, `Adres`, `Postcode`, `Plaats`) VALUES
(1, 'Stationsplein 1', '1234AB', 'Best'),
(2, 'Dorpsstraat 10', '5678CD', 'Eindhoven');

-- --------------------------------------------------------

--
-- Table structure for table `soort`
--

CREATE TABLE `soort` (
  `Soort_id` int(10) NOT NULL,
  `Type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soort`
--

INSERT INTO `soort` (`Soort_id`, `Type`) VALUES
(1, 'Schakel'),
(2, 'Automaat');

-- --------------------------------------------------------

--
-- Table structure for table `ziekmelding`
--

CREATE TABLE `ziekmelding` (
  `Ziekmelding_id` int(10) NOT NULL,
  `Van` date NOT NULL,
  `Tot` date NOT NULL,
  `Toelichting` text NOT NULL,
  `GebruikerGebruiker_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ziekmelding`
--

INSERT INTO `ziekmelding` (`Ziekmelding_id`, `Van`, `Tot`, `Toelichting`, `GebruikerGebruiker_id`) VALUES
(1, '2026-04-08', '2026-04-10', 'Griep, kan niet lesgeven.', 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auto`
--
ALTER TABLE `auto`
  ADD PRIMARY KEY (`Auto_id`);

--
-- Indexes for table `gebruiker`
--
ALTER TABLE `gebruiker`
  ADD PRIMARY KEY (`Gebruiker_id`);

--
-- Indexes for table `gebruiker_lespakket`
--
ALTER TABLE `gebruiker_lespakket`
  ADD PRIMARY KEY (`Gebruiker_Lespakket_id`);

--
-- Indexes for table `les`
--
ALTER TABLE `les`
  ADD PRIMARY KEY (`Les_id`);

--
-- Indexes for table `lespakket`
--
ALTER TABLE `lespakket`
  ADD PRIMARY KEY (`Lespakket_id`);

--
-- Indexes for table `onderwerp`
--
ALTER TABLE `onderwerp`
  ADD PRIMARY KEY (`Onderwerp_id`);

--
-- Indexes for table `ophaallocatie`
--
ALTER TABLE `ophaallocatie`
  ADD PRIMARY KEY (`Ophaallocatie_id`);

--
-- Indexes for table `soort`
--
ALTER TABLE `soort`
  ADD PRIMARY KEY (`Soort_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auto`
--
ALTER TABLE `auto`
  MODIFY `Auto_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gebruiker`
--
ALTER TABLE `gebruiker`
  MODIFY `Gebruiker_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `gebruiker_lespakket`
--
ALTER TABLE `gebruiker_lespakket`
  MODIFY `Gebruiker_Lespakket_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `les`
--
ALTER TABLE `les`
  MODIFY `Les_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lespakket`
--
ALTER TABLE `lespakket`
  MODIFY `Lespakket_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `onderwerp`
--
ALTER TABLE `onderwerp`
  MODIFY `Onderwerp_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ophaallocatie`
--
ALTER TABLE `ophaallocatie`
  MODIFY `Ophaallocatie_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `soort`
--
ALTER TABLE `soort`
  MODIFY `Soort_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
