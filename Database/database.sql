-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Gegenereerd op: 27 mei 2024 om 14:55
-- Serverversie: 8.3.0
-- PHP-versie: 8.1.2-1ubuntu2.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rijschool`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Auto`
--

CREATE TABLE `Auto` (
  `Auto_id` int NOT NULL,
  `Merk` varchar(255) NOT NULL,
  `Model` varchar(255) NOT NULL,
  `Kenteken` varchar(255) NOT NULL,
  `SoortSoort_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Gebruiker`
--

CREATE TABLE `Gebruiker` (
  `Gebruiker_id` int NOT NULL,
  `Gebruikersnaam` varchar(255) NOT NULL,
  `Wachtwoord` varchar(255) NOT NULL,
  `Voornaam` varchar(255) NOT NULL,
  `Tussenvoegsel` varchar(255) NOT NULL,
  `Achternaam` varchar(255) NOT NULL,
  `Rol` int NOT NULL,
  `Examenformatie` text NOT NULL,
  `Actief` int NOT NULL,
  `Geslaagd` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Gebruiker_Lespakket`
--

CREATE TABLE `Gebruiker_Lespakket` (
  `Gebruiker_Lespakket_id` int NOT NULL,
  `GebruikerGebruiker_id` int NOT NULL,
  `LespakketLespakket_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Les`
--

CREATE TABLE `Les` (
  `Les_id` int NOT NULL,
  `Lestijd` datetime NOT NULL,
  `OphaallocatieOphaallocatie_id` int NOT NULL,
  `Instructeur_id` int NOT NULL,
  `Doel` text NOT NULL,
  `Opmerking_student` text NOT NULL,
  `Opmerking_instructeur` text NOT NULL,
  `Lespakket_id` int NOT NULL,
  `Geannuleerd` int NOT NULL,
  `RedenAnnuleren` text NOT NULL,
  `AutoAuto_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Lespakket`
--

CREATE TABLE `Lespakket` (
  `Lespakket_id` int NOT NULL,
  `Naam` varchar(255) NOT NULL,
  `Omschrijving` text NOT NULL,
  `Aantal` int NOT NULL,
  `Prijs` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Les_Onderwerp`
--

CREATE TABLE `Les_Onderwerp` (
  `LesLes_id` int NOT NULL,
  `OnderwerpOnderwerp_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Onderwerp`
--

CREATE TABLE `Onderwerp` (
  `Onderwerp_id` int NOT NULL,
  `Onderwerp` varchar(255) NOT NULL,
  `Omschrijving` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Ophaallocatie`
--

CREATE TABLE `Ophaallocatie` (
  `Ophaallocatie_id` int NOT NULL,
  `Adres` varchar(255) NOT NULL,
  `Postcode` varchar(8) NOT NULL,
  `Plaats` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Soort`
--

CREATE TABLE `Soort` (
  `Soort_id` int NOT NULL,
  `Type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Ziekmelding`
--

CREATE TABLE `Ziekmelding` (
  `Ziekmelding_id` int NOT NULL,
  `Van` date NOT NULL,
  `Tot` date NOT NULL,
  `Toelichting` text NOT NULL,
  `GebruikerGebruiker_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexen voor geÃ«xporteerde tabellen
--

--
-- Indexen voor tabel `Auto`
--
ALTER TABLE `Auto`
  ADD PRIMARY KEY (`Auto_id`);

--
-- Indexen voor tabel `Gebruiker`
--
ALTER TABLE `Gebruiker`
  ADD PRIMARY KEY (`Gebruiker_id`);

--
-- Indexen voor tabel `Gebruiker_Lespakket`
--
ALTER TABLE `Gebruiker_Lespakket`
  ADD PRIMARY KEY (`Gebruiker_Lespakket_id`);

--
-- Indexen voor tabel `Les`
--
ALTER TABLE `Les`
  ADD PRIMARY KEY (`Les_id`);

--
-- Indexen voor tabel `Lespakket`
--
ALTER TABLE `Lespakket`
  ADD PRIMARY KEY (`Lespakket_id`);

--
-- Indexen voor tabel `Onderwerp`
--
ALTER TABLE `Onderwerp`
  ADD PRIMARY KEY (`Onderwerp_id`);

--
-- Indexen voor tabel `Ophaallocatie`
--
ALTER TABLE `Ophaallocatie`
  ADD PRIMARY KEY (`Ophaallocatie_id`);

--
-- Indexen voor tabel `Soort`
--
ALTER TABLE `Soort`
  ADD PRIMARY KEY (`Soort_id`);

--
-- AUTO_INCREMENT voor geÃ«xporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `Auto`
--
ALTER TABLE `Auto`
  MODIFY `Auto_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Gebruiker`
--
ALTER TABLE `Gebruiker`
  MODIFY `Gebruiker_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Gebruiker_Lespakket`
--
ALTER TABLE `Gebruiker_Lespakket`
  MODIFY `Gebruiker_Lespakket_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Les`
--
ALTER TABLE `Les`
  MODIFY `Les_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Lespakket`
--
ALTER TABLE `Lespakket`
  MODIFY `Lespakket_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Onderwerp`
--
ALTER TABLE `Onderwerp`
  MODIFY `Onderwerp_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Ophaallocatie`
--
ALTER TABLE `Ophaallocatie`
  MODIFY `Ophaallocatie_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Soort`
--
ALTER TABLE `Soort`
  MODIFY `Soort_id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;