<?php
/*
Naam: Dominik Bulla
Versie: 1.0
Datum: 08-04-2026
Beschrijving: Database class die zorgt voor een singleton PDO verbinding. Hiermee kunnen andere classes eenvoudig een databaseverbinding krijgen zonder steeds opnieuw verbinding te maken. Foutafhandeling is inbegrepen om eventuele databaseproblemen duidelijk te melden.
*/
class Database {
    // Singleton instantie van PDO
    private static ?PDO $instance = null;

    // connectie maken met de database met behulp van dsn en configuratiegegevens
    public static function getInstance(): PDO { 
        if (self::$instance === null) {
            //data samenstellen voor de databaseverbinding
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4"; 
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
                ]); 
            } catch (PDOException $e) {
                die('<p style="color:red;padding:2rem;font-family:sans-serif">
                    <strong>Database fout:</strong> '
                    . htmlspecialchars($e->getMessage()) . '</p>'); //foutmelding tonen als er een probleem is met de databaseverbinding
            }
        }
        return self::$instance;
    }
}