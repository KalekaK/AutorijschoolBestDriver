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

    // 1. Eén gebruiker ophalen op ID
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM gebruiker WHERE Gebruiker_id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // 2. Eén gebruiker ophalen op gebruikersnaam (alleen actieve)
    public function getByGebruikersnaam(string $gebruikersnaam): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM gebruiker 
             WHERE Gebruikersnaam = ? AND Actief = 1"
        );
        $stmt->execute([$gebruikersnaam]);
        return $stmt->fetch();
    }

    // 3. Bestaat deze gebruikersnaam al?
    public function bestaatGebruikersnaam(string $gebruikersnaam): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM gebruiker WHERE Gebruikersnaam = ?"
        );
        $stmt->execute([$gebruikersnaam]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // 4. Alle klanten (Rol = 3), optioneel zoeken op naam
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

    // 5. Alle instructeurs (Rol = 2), alleen actieve
    public function getAlleInstructeurs(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM gebruiker
             WHERE Rol = 2 AND Actief = 1
             ORDER BY Achternaam"
        );
        return $stmt->fetchAll();
    }

    // 6. Instructeurs voor admin, met zoekfunctie
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

    // 7. Nieuwe gebruiker toevoegen
    public function toevoegen(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO gebruiker
             (Gebruikersnaam, Wachtwoord, Voornaam, Tussenvoegsel, Achternaam,
              Rol, Examenformatie, Actief, Geslaagd,
              Adres, Ophaaladres, Email, Telefoon, RegistratieDatum)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );

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

    // 8. Gebruiker bijwerken
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

    // 9. Alleen wachtwoord aanpassen
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

    // 10. Actief / inactief zetten
    public function setActief(int $id, int $actief): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE gebruiker
             SET Actief = ?
             WHERE Gebruiker_id = ?"
        );

        return $stmt->execute([$actief, $id]);
    }

    // 11. “Verwijderen” = inactief zetten
    public function verwijderen(int $id): bool
    {
        return $this->setActief($id, 0);
    }

    // 12. Volledige naam opbouwen
    public function getVolleNaam(array $gebruiker): string
    {
        return trim(
            $gebruiker['Voornaam'] . ' ' .
            ($gebruiker['Tussenvoegsel'] ?? '') . ' ' .
            $gebruiker['Achternaam']
        );
    }
}