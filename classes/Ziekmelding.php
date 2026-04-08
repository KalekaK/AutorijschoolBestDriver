<?php
/*
Naam: Ryan Sitaldien
Versie: 1.0
Datum: 08-04-2026
Beschrijving: Simpele class voor ziekmeldingen van instructeurs.
*/

class Ziekmelding {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function toevoegen(int $gebruikerId, string $van, string $tot, string $toelichting): bool {
        $van = trim($van);
        $tot = trim($tot);
        $toelichting = trim($toelichting);

        if ($gebruikerId < 1 || $van === '' || $tot === '' || $toelichting === '') {
            return false;
        }

        if (strtotime($van) === false || strtotime($tot) === false) {
            return false;
        }

        if (strtotime($van) > strtotime($tot)) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO ziekmelding (Van, Tot, Toelichting, GebruikerGebruiker_id) VALUES (?,?,?,?)"
        );
        return $stmt->execute([$van, $tot, $toelichting, $gebruikerId]);
    }

    public function getByGebruiker(int $gebruikerId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM ziekmelding WHERE GebruikerGebruiker_id = ? ORDER BY Van DESC"
        );
        $stmt->execute([$gebruikerId]);
        return $stmt->fetchAll();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query(
            "SELECT z.*, CONCAT(g.Voornaam,' ',g.Achternaam) AS instructeur_naam
             FROM ziekmelding z
             LEFT JOIN gebruiker g ON g.Gebruiker_id = z.GebruikerGebruiker_id
             ORDER BY z.Van DESC"
        );
        return $stmt->fetchAll();
    }
}

