<?php
/*
Naam: Krishna Sardarsing
Versie: 1.1
Datum: 08-04-2026
Beschrijving: Simpele class om auto’s (in de wagenpark) te beheren.
*/

class Auto {
    private PDO $pdo;
// Database connectie wordt opgehaald via de Database class
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
// Alle auto's ophalen, voor kenteken, merk en model
    public function getAll(string $zoek = ''): array {
        if ($zoek !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT a.*, s.Type AS soort
                 FROM auto a
                 LEFT JOIN soort s ON s.Soort_id = a.SoortSoort_id
                 WHERE a.Kenteken LIKE ? OR a.Merk LIKE ? OR a.Model LIKE ?
                 ORDER BY a.Kenteken"
            );
            $stmt->execute(["%$zoek%", "%$zoek%", "%$zoek%"]);
            return $stmt->fetchAll();
        }

        $stmt = $this->pdo->query(
            "SELECT a.*, s.Type AS soort
             FROM auto a
             LEFT JOIN soort s ON s.Soort_id = a.SoortSoort_id
             ORDER BY a.Kenteken"
        );
        return $stmt->fetchAll();
    }
// Auto ophalen op ID, inclusief de soortnaam
    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT a.*, s.Type AS soort
             FROM auto a
             LEFT JOIN soort s ON s.Soort_id = a.SoortSoort_id
             WHERE a.Auto_id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getSoorten(): array {
        $stmt = $this->pdo->query("SELECT * FROM soort ORDER BY Type");
        return $stmt->fetchAll();
    }
// Nieuwe auto toevoegen, met validatie van de invoer
    public function toevoegen(array $data): bool {
        $merk = trim($data['merk'] ?? '');
        $model = trim($data['model'] ?? '');
        $kenteken = strtoupper(trim($data['kenteken'] ?? ''));
        $soortId = (int)($data['soort_id'] ?? 0);

        if ($kenteken === '' || $merk === '' || $model === '' || $soortId < 1) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO auto (Merk, Model, Kenteken, SoortSoort_id) VALUES (?,?,?,?)"
        );
        return $stmt->execute([$merk, $model, $kenteken, $soortId]);
    }
// Bestaande auto bijwerken, met validatie van de invoer
    public function bijwerken(int $id, array $data): bool {
        $merk = trim($data['merk'] ?? '');
        $model = trim($data['model'] ?? '');
        $kenteken = strtoupper(trim($data['kenteken'] ?? ''));
        $soortId = (int)($data['soort_id'] ?? 0);

        if ($kenteken === '' || $merk === '' || $model === '' || $soortId < 1) {
            return false;
        }
// Controleren of de auto bestaat
        $stmt = $this->pdo->prepare(
            "UPDATE auto SET Merk = ?, Model = ?, Kenteken = ?, SoortSoort_id = ? WHERE Auto_id = ?"
        );
        return $stmt->execute([$merk, $model, $kenteken, $soortId, $id]);
    }
    public function verwijderen(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM auto WHERE Auto_id = ?");
        return $stmt->execute([$id]);
    }
}
