-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2026 at 09:48 AM
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
  `Geslaagd` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gebruiker_lespakket`
--

CREATE TABLE `gebruiker_lespakket` (
  `Gebruiker_Lespakket_id` int(10) NOT NULL,
  `GebruikerGebruiker_id` int(10) NOT NULL,
  `LespakketLespakket_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `les_onderwerp`
--

CREATE TABLE `les_onderwerp` (
  `LesLes_id` int(10) NOT NULL,
  `OnderwerpOnderwerp_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `onderwerp`
--

CREATE TABLE `onderwerp` (
  `Onderwerp_id` int(10) NOT NULL,
  `Onderwerp` varchar(255) NOT NULL,
  `Omschrijving` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `soort`
--

CREATE TABLE `soort` (
  `Soort_id` int(10) NOT NULL,
  `Type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `Auto_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gebruiker`
--
ALTER TABLE `gebruiker`
  MODIFY `Gebruiker_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gebruiker_lespakket`
--
ALTER TABLE `gebruiker_lespakket`
  MODIFY `Gebruiker_Lespakket_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `les`
--
ALTER TABLE `les`
  MODIFY `Les_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lespakket`
--
ALTER TABLE `lespakket`
  MODIFY `Lespakket_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `onderwerp`
--
ALTER TABLE `onderwerp`
  MODIFY `Onderwerp_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ophaallocatie`
--
ALTER TABLE `ophaallocatie`
  MODIFY `Ophaallocatie_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `soort`
--
ALTER TABLE `soort`
  MODIFY `Soort_id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
