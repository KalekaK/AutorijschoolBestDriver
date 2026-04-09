<?php

/*
Naam: Adrian
Versie: 1.1
Datum: 08-04-2026
Beschrijving: gebruiker class voor klanten/instructeurs (crud en inloggen).
*/

class Gebruiker
{
    private PDO $pdo;

    public function __construct()
    {
        // één keer de database connectie ophalen
        $this->pdo = Database::getInstance();
    }

    // 1. één gebruiker ophalen op id
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM gebruiker WHERE Gebruiker_id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // 2. één gebruiker ophalen op gebruikersnaam (alleen actieve)
    public function getByGebruikersnaam(string $gebruikersnaam): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM gebruiker 
             WHERE Gebruikersnaam = ? AND Actief = 1"
        );
        $stmt->execute([$gebruikersnaam]);
        return $stmt->fetch();
    }

    // 3. check of een gebruikersnaam al bestaat
    public function bestaatGebruikersnaam(string $gebruikersnaam, ?int $excludeId = null): bool
    {
        // optioneel een id uitsluiten (handig bij bewerken)
        if ($excludeId !== null) {
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

    // 4. alle klanten (rol = 3), optioneel zoekterm op naam
    public function getAlleKlanten(string $zoek = ''): array
    {
        if ($zoek !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM gebruiker
                 WHERE Rol = 3
                 AND (Voornaam LIKE ? OR Achternaam LIKE ?)
                 ORDER BY Achternaam"
            );
            $stmt->execute(["%$zoek%", "%$zoek%"]);
        } else {
            $stmt = $this->pdo->query(
                "SELECT * FROM gebruiker
                 WHERE Rol = 3
                 ORDER BY Achternaam"
            );
        }

        return $stmt->fetchAll();
    }

    // 5. alle actieve instructeurs (rol = 2)
    public function getAlleInstructeurs(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM gebruiker
             WHERE Rol = 2 AND Actief = 1
             ORDER BY Achternaam"
        );
        return $stmt->fetchAll();
    }

    // 6. instructeurs voor admin, met zoekmogelijkheid
    public function getAlleInstructeursAdmin(string $zoek = ''): array
    {
        if ($zoek !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM gebruiker
                 WHERE Rol = 2
                 AND (Voornaam LIKE ? OR Achternaam LIKE ? OR Gebruikersnaam LIKE ?)
                 ORDER BY Achternaam"
            );
            $stmt->execute(["%$zoek%", "%$zoek%", "%$zoek%"]);
        } else {
            $stmt = $this->pdo->query(
                "SELECT * FROM gebruiker
                 WHERE Rol = 2
                 ORDER BY Achternaam"
            );
        }

        return $stmt->fetchAll();
    }

    // 7. nieuwe gebruiker toevoegen
    public function toevoegen(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO gebruiker
             (Gebruikersnaam, Wachtwoord, Voornaam, Tussenvoegsel, Achternaam,
              Rol, Examenformatie, Actief, Geslaagd,
              Adres, Ophaaladres, Email, Telefoon, RegistratieDatum)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );

        // als er geen datum meegegeven is, vandaag gebruiken
        $registratieDatum = $data['registratiedatum'] ?? date('Y-m-d');

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
            $registratieDatum,
        ]);
    }

    // 8. bestaande gebruiker bijwerken
    public function bijwerken(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE gebruiker
             SET Voornaam = ?,
                 Tussenvoegsel = ?,
                 Achternaam = ?,
                 Gebruikersnaam = ?,
                 Actief = ?,
                 Geslaagd = ?,
                 Adres = ?,
                 Ophaaladres = ?,
                 Email = ?,
                 Telefoon = ?,
                 RegistratieDatum = ?
             WHERE Gebruiker_id = ?"
        );

        // zelfde truc met datum als bij toevoegen
        $registratieDatum = $data['registratiedatum'] ?? date('Y-m-d');

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
            $registratieDatum,
            $id,
        ]);
    }

    // 9. alleen wachtwoord aanpassen
    public function wachtwoordBijwerken(int $id, string $nieuwWachtwoord): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE gebruiker
             SET Wachtwoord = ?
             WHERE Gebruiker_id = ?"
        );

        return $stmt->execute([
            password_hash($nieuwWachtwoord, PASSWORD_DEFAULT),
            $id,
        ]);
    }

    // 10. actief / inactief zetten
    public function setActief(int $id, int $actief): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE gebruiker
             SET Actief = ?
             WHERE Gebruiker_id = ?"
        );

        return $stmt->execute([$actief, $id]);
    }

    // 11. “verwijderen” = inactief maken
    public function verwijderen(int $id): bool
    {
        return $this->setActief($id, 0);
    }

    // 12. volledige naam opbouwen uit losse velden
    public function getVolleNaam(array $gebruiker): string
    {
        return trim(
            $gebruiker['Voornaam'] . ' ' .
            ($gebruiker['Tussenvoegsel'] ?? '') . ' ' .
            $gebruiker['Achternaam']
        );
    }
}