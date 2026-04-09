<?php
/*
Naam: Dominik Bulla
Versie: 1.0
Datum: 08-04-2026
Beschrijving: Simpele class om lespakketten te beheren.
*/

class Lespakket {
    // PDO instantie voor databaseverbinding
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    // Alle lespakketten ophalen, optioneel filteren op zoekterm
    public function getAll(string $zoek = ''): array {
        if ($zoek !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM lespakket WHERE Naam LIKE ? OR Omschrijving LIKE ? ORDER BY Naam"
            );
            // Zoekterm gebruiken in de query met wildcard voor gedeeltelijke matches
            $stmt->execute(["%$zoek%", "%$zoek%"]);
            return $stmt->fetchAll();
        }

        $stmt = $this->pdo->query("SELECT * FROM lespakket ORDER BY Naam");
        return $stmt->fetchAll();
    }
    // Alle lespakketten ophalen met het aantal ingeschreven gebruikers, optioneel filteren op zoekterm
    public function getAllMetInschrijvingen(string $zoek = ''): array {
        if ($zoek !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT lp.*, COUNT(DISTINCT glp.GebruikerGebruiker_id) AS aantal_ingeschreven
                 FROM lespakket lp
                 LEFT JOIN gebruiker_lespakket glp ON glp.LespakketLespakket_id = lp.Lespakket_id
                 WHERE lp.Naam LIKE ? OR lp.Omschrijving LIKE ?
                 GROUP BY lp.Lespakket_id
                 ORDER BY lp.Naam"
            );
            $stmt->execute(["%$zoek%", "%$zoek%"]);
            return $stmt->fetchAll();
        }

        // Alle lespakketten ophalen met het aantal ingeschreven gebruikers
        $stmt = $this->pdo->query(
            "SELECT lp.*, COUNT(DISTINCT glp.GebruikerGebruiker_id) AS aantal_ingeschreven
             FROM lespakket lp
             LEFT JOIN gebruiker_lespakket glp ON glp.LespakketLespakket_id = lp.Lespakket_id
             GROUP BY lp.Lespakket_id
             ORDER BY lp.Naam"
        );
        return $stmt->fetchAll();
    }

    // Lespakket ophalen op ID
    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM lespakket WHERE Lespakket_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Lespakket toevoegen aan de database
    public function toevoegen(array $data): bool {
        $naam = trim($data['naam'] ?? '');
        $omschrijving = trim($data['omschrijving'] ?? '');
        $aantal = (int)($data['aantal'] ?? 0);
        $prijs = (float)($data['prijs'] ?? 0);

        if ($naam === '' || $aantal < 1) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO lespakket (Naam, Omschrijving, Aantal, Prijs) VALUES (?,?,?,?)"
        );
        return $stmt->execute([$naam, $omschrijving, $aantal, $prijs]);
    }

    // Lespakket bijwerken in de database
    public function bijwerken(int $id, array $data): bool {
        $naam = trim($data['naam'] ?? '');
        $omschrijving = trim($data['omschrijving'] ?? '');
        $aantal = (int)($data['aantal'] ?? 0);
        $prijs = (float)($data['prijs'] ?? 0);

        if ($naam === '' || $aantal < 1) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE lespakket SET Naam = ?, Omschrijving = ?, Aantal = ?, Prijs = ? WHERE Lespakket_id = ?"
        );
        return $stmt->execute([$naam, $omschrijving, $aantal, $prijs, $id]);
    }

    // Lespakket verwijderen uit de database
    public function verwijderen(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM lespakket WHERE Lespakket_id = ?");
        return $stmt->execute([$id]);
    }
}
