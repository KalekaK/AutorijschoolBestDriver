<?php
/*
Naam: Krishna Sardarsing
Versie: 1.5
Datum: 08-04-2026
Beschrijving: Simpele class voor lessen (overzichten en plannen).
*/

class Les {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

// alle lessen ophalen, met zoekfunctie
    public function getAll(string $zoek = ''): array {
        $sql = "SELECT l.*,
            CONCAT(gi.Voornaam,' ',gi.Achternaam) AS instructeur_naam,
            CONCAT(gk.Voornaam,' ',gk.Achternaam) AS klant_naam,
            o.Adres AS ophaallocatie,
            lp.Naam AS lespakket_naam
            FROM les l
            LEFT JOIN gebruiker gi ON gi.Gebruiker_id = l.Instructeur_id
            LEFT JOIN gebruiker_lespakket glp ON glp.Gebruiker_Lespakket_id = l.Lespakket_id
            LEFT JOIN gebruiker gk ON gk.Gebruiker_id = glp.GebruikerGebruiker_id AND gk.Rol = 3
            LEFT JOIN lespakket lp ON lp.Lespakket_id = glp.LespakketLespakket_id
            LEFT JOIN ophaallocatie o ON o.Ophaallocatie_id = l.OphaallocatieOphaallocatie_id
            ORDER BY l.Lestijd DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

// lessen ophalen op basis van lespakket type, instructeur of klant
    public function getByLespakketType(int $lespakketTypeId): array {
        $stmt = $this->pdo->prepare(
            "SELECT l.*,
             CONCAT(gi.Voornaam,' ',gi.Achternaam) AS instructeur_naam,
             CONCAT(gk.Voornaam,' ',gk.Achternaam) AS klant_naam,
             o.Adres AS ophaallocatie,
             CONCAT(a.Kenteken,' - ',a.Merk,' ',a.Model) AS auto,
             lp.Naam AS lespakket_naam
             FROM les l
             LEFT JOIN gebruiker gi ON gi.Gebruiker_id = l.Instructeur_id
             JOIN gebruiker_lespakket glp ON glp.Gebruiker_Lespakket_id = l.Lespakket_id
             LEFT JOIN gebruiker gk ON gk.Gebruiker_id = glp.GebruikerGebruiker_id AND gk.Rol = 3
             LEFT JOIN lespakket lp ON lp.Lespakket_id = glp.LespakketLespakket_id
             LEFT JOIN ophaallocatie o ON o.Ophaallocatie_id = l.OphaallocatieOphaallocatie_id
             LEFT JOIN auto a ON a.Auto_id = l.AutoAuto_id
             WHERE glp.LespakketLespakket_id = ?
             ORDER BY l.Lestijd DESC"
        );
        $stmt->execute([$lespakketTypeId]);
        return $stmt->fetchAll();
    }

    public function getByInstructeur(int $instructeurId): array {
        $stmt = $this->pdo->prepare(
            "SELECT l.*,
             CONCAT(g.Voornaam,' ',g.Achternaam) AS klant_naam,
             o.Adres AS ophaallocatie,
             lp.Naam AS lespakket_naam
             FROM les l
             LEFT JOIN gebruiker_lespakket glp ON glp.Gebruiker_Lespakket_id = l.Lespakket_id
             LEFT JOIN gebruiker g ON g.Gebruiker_id = glp.GebruikerGebruiker_id AND g.Rol = 3
             LEFT JOIN lespakket lp ON lp.Lespakket_id = glp.LespakketLespakket_id
             LEFT JOIN ophaallocatie o ON o.Ophaallocatie_id = l.OphaallocatieOphaallocatie_id
             WHERE l.Instructeur_id = ?
             ORDER BY l.Lestijd DESC"
        );
        $stmt->execute([$instructeurId]);
        return $stmt->fetchAll();
    }

    public function getByKlant(int $klantId): array {
        $stmt = $this->pdo->prepare(
            "SELECT l.*,
             CONCAT(gi.Voornaam,' ',gi.Achternaam) AS instructeur_naam,
             o.Adres AS ophaallocatie,
             lp.Naam AS lespakket_naam
             FROM les l
             JOIN gebruiker_lespakket glp ON glp.Gebruiker_Lespakket_id = l.Lespakket_id
             LEFT JOIN gebruiker gi ON gi.Gebruiker_id = l.Instructeur_id
             LEFT JOIN lespakket lp ON lp.Lespakket_id = glp.LespakketLespakket_id
             LEFT JOIN ophaallocatie o ON o.Ophaallocatie_id = l.OphaallocatieOphaallocatie_id
             WHERE glp.GebruikerGebruiker_id = ?
             ORDER BY l.Lestijd DESC"
        );
        $stmt->execute([$klantId]);
        return $stmt->fetchAll();
    }

//hier gebruiken we een private functie die eerst kijkt of er al een gebruiker_lespakket bestaat.
    private function getOfMaakGebruikerLespakketId(int $klantId, int $lespakketTypeId): int {
        $stmt = $this->pdo->prepare(
            "SELECT Gebruiker_Lespakket_id
             FROM gebruiker_lespakket
             WHERE GebruikerGebruiker_id = ? AND LespakketLespakket_id = ?
             LIMIT 1"
        );
        $stmt->execute([$klantId, $lespakketTypeId]);
        $row = $stmt->fetch();
        if ($row && isset($row['Gebruiker_Lespakket_id'])) {
            return (int)$row['Gebruiker_Lespakket_id'];
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO gebruiker_lespakket (GebruikerGebruiker_id, LespakketLespakket_id) VALUES (?,?)"
        );
        $ok = $stmt->execute([$klantId, $lespakketTypeId]);
        if (!$ok) {
            return 0;
        }

        return (int)$this->pdo->lastInsertId();
    }

// les toevoegen, annuleren en verwijderen
    public function toevoegen(array $data): bool {
        $klantId = (int)($data['klant_id'] ?? 0);
        $lespakketTypeId = (int)($data['lespakket_id'] ?? 0);

        if ($klantId < 1 || $lespakketTypeId < 1) {
            return false;
        }

        $gebruikerLespakketId = $this->getOfMaakGebruikerLespakketId($klantId, $lespakketTypeId);
        if ($gebruikerLespakketId < 1) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO les
             (Lestijd, OphaallocatieOphaallocatie_id, Instructeur_id,
              Doel, Opmerking_student, Opmerking_instructeur,
              Lespakket_id, Geannuleerd, RedenAnnuleren, AutoAuto_id)
             VALUES (?,?,?,?,?,?,?,0,'',?)"
        );
        return $stmt->execute([
            $data['lestijd'],
            $data['ophaallocatie_id'],
            $data['instructeur_id'],
            $data['doel'] ?? '',
            $data['opmerking_student'] ?? '',
            $data['opmerking_instructeur'] ?? '',
            $gebruikerLespakketId,
            $data['auto_id'],
        ]);
    }

    public function annuleren(int $lesId, string $reden): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE les SET Geannuleerd = 1, RedenAnnuleren = ? WHERE Les_id = ?"
        );
        return $stmt->execute([$reden, $lesId]);
    }

    public function verwijderen(int $lesId): bool { 
        $stmt = $this->pdo->prepare("DELETE FROM les WHERE Les_id = ?");
        return $stmt->execute([$lesId]);
    }
 