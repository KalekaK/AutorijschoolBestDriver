<?php

/*
Naam: Adrian
Versie: 1.1
Datum: 08-04-2026
Beschrijving: Gebruiker class voor klanten/instructeurs (CRUD en inloggen).
*/

class Gebruiker
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    // 1 gebruiker ophalen op ID
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM gebruiker WHERE Gebruiker_id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // 1 actieve gebruiker ophalen op gebruikersnaam
    public function getByGebruikersnaam(string $gebruikersnaam): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM gebruiker WHERE Gebruikersnaam = ? AND Actief = 1"
        );
        $stmt->execute([$gebruikersnaam]);
        return $stmt->fetch();
    }

    // Bestaat deze gebruikersnaam al? (excludeId = uitzondering bij bewerken)
    public function bestaatGebruikersnaam(string $gebruikersnaam, int $excludeId = 0): bool
    {
        if ($excludeId > 0) {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM gebruiker
                 WHERE Gebruikersnaam = ? AND Gebruiker_id <> ?"
            );
            $stmt->execute([$gebruikersnaam, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM gebruiker WHERE Gebruikersnaam = ?"
            );
            $stmt->execute([$gebruikersnaam]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }

    // Alle klanten (Rol = 3), optioneel zoeken
    public function getAlleKlanten(string $zoek = ''): array
    {
        if ($zoek !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM gebruiker
                 WHERE Rol = 3 AND (Voornaam LIKE ? OR Achternaam LIKE ?)
                 ORDER BY Achternaam"
            );
            $stmt->execute(["%$zoek%", "%$zoek%"]);
        } else {
            $stmt = $this->pdo->query(
                "SELECT * FROM gebruiker WHERE Rol = 3 ORDER BY Achternaam"
            );
        }
        return $stmt->fetchAll();
    }

    // Alle actieve instructeurs (Rol = 2)
    public function getAlleInstructeurs(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM gebruiker WHERE Rol = 2 AND Actief = 1 ORDER BY Achternaam"
        );
        return $stmt->fetchAll();
    }

    // Alle instructeurs voor admin, optioneel zoeken
    public function getAlleInstructeursAdmin(string $zoek = ''): array
    {
        if ($zoek !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM gebruiker
                 WHERE Rol = 2 AND (Voornaam LIKE ? OR Achternaam LIKE ? OR Gebruikersnaam LIKE ?)
                 ORDER BY Achternaam"
            );
            $stmt->execute(["%$zoek%", "%$zoek%", "%$zoek%"]);
        } else {
            $stmt = $this->pdo->query(
                "SELECT * FROM gebruiker WHERE Rol = 2 ORDER BY Achternaam"
            );
        }
        return $stmt->fetchAll();
    }

    // Nieuwe gebruiker toevoegen
    public function toevoegen(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO gebruiker
             (Gebruikersnaam, Wachtwoord, Voornaam, Tussenvoegsel, Achternaam,
              Rol, Examenformatie, Actief, Geslaagd,
              Adres, Ophaaladres, Email, Telefoon, RegistratieDatum, Geboortedatum)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );

        return $stmt->execute([
            $data['gebruikersnaam'],
            password_hash($data['wachtwoord'], PASSWORD_DEFAULT),
            $data['voornaam'],
            $data['tussenvoegsel'] ?? '',
            $data['achternaam'],
            $data['rol'],
            $data['examenformatie'] ?? '',
            $data['actief'] ?? 1,
            $data['geslaagd'] ?? 0,
            $data['adres'] ?? '',
            $data['ophaaladres'] ?? '',
            $data['email'] ?? '',
            $data['telefoon'] ?? '',
            $data['registratiedatum'] ?? date('Y-m-d'),
            $data['geboortedatum'] ?: null,
        ]);
    }

    // Bestaande gebruiker bijwerken
    public function bijwerken(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE gebruiker
             SET Voornaam = ?, Tussenvoegsel = ?, Achternaam = ?,
                 Gebruikersnaam = ?, Actief = ?, Geslaagd = ?,
                 Adres = ?, Ophaaladres = ?, Email = ?, Telefoon = ?,
                 RegistratieDatum = ?, Geboortedatum = ?
             WHERE Gebruiker_id = ?"
        );

        return $stmt->execute([
            $data['voornaam'],
            $data['tussenvoegsel'] ?? '',
            $data['achternaam'],
            $data['gebruikersnaam'],
            $data['actief'],
            $data['geslaagd'],
            $data['adres'] ?? '',
            $data['ophaaladres'] ?? '',
            $data['email'] ?? '',
            $data['telefoon'] ?? '',
            $data['registratiedatum'] ?? date('Y-m-d'),
            $data['geboortedatum'] ?: null,
            $id,
        ]);
    }

    // Alleen wachtwoord wijzigen
    public function wachtwoordBijwerken(int $id, string $nieuwWachtwoord): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE gebruiker SET Wachtwoord = ? WHERE Gebruiker_id = ?"
        );
        return $stmt->execute([
            password_hash($nieuwWachtwoord, PASSWORD_DEFAULT),
            $id,
        ]);
    }

    // Gebruiker actief of inactief zetten
    public function setActief(int $id, int $actief): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE gebruiker SET Actief = ? WHERE Gebruiker_id = ?"
        );
        return $stmt->execute([$actief, $id]);
    }

    // Soft delete = inactief zetten
    public function verwijderen(int $id): bool
    {
        return $this->setActief($id, 0);
    }

    // Volledige naam als string
    public function getVolleNaam(array $gebruiker): string
    {
        return trim(
            $gebruiker['Voornaam'] . ' ' .
            ($gebruiker['Tussenvoegsel'] ?? '') . ' ' .
            $gebruiker['Achternaam']
        );
    }
}