<?php
/*
Naam: Dominik Bulla
Versie: 1.0
Datum: 08-04-2026
Beschrijving: Auth class die zorgt voor gebruikersauthenticatie en autorisatie. Deze class beheert de sessiegegevens van de gebruiker, zoals inloggen, uitloggen en controleren van gebruikersrollen. Hiermee kunnen pagina's eenvoudig toegang beperken tot bepaalde gebruikers of rollen. De class maakt gebruik van PHP-sessies om de status van de gebruiker bij te houden.
*/

// Start de sessie als deze nog niet is gestart
if (session_status() === PHP_SESSION_NONE) session_start();

class Auth {
    // Controleren of een gebruiker is ingelogd
    public static function isLoggedIn(): bool {
        // Controleren of de gebruiker_id in de sessie is ingesteld, wat aangeeft dat de gebruiker is ingelogd
        return isset($_SESSION['gebruiker_id']);
    }

    // De rol van de gebruiker ophalen
    public static function getRol(): int {
        return $_SESSION['rol'] ?? 0;
    }

    // De ID van de gebruiker ophalen
    public static function getGebruikerId(): int {
        return $_SESSION['gebruiker_id'] ?? 0;
    }

    // De naam van de gebruiker ophalen (voornaam)
    public static function getNaam(): string {
        return $_SESSION['voornaam'] ?? '';
    }

    // De volledige naam van de gebruiker ophalen (voornaam + achternaam) het verwacht een array en laat met void zien dat er geen waarde teruggegeven wordt
    public static function login(array $gebruiker) {
        $_SESSION['gebruiker_id'] = $gebruiker['Gebruiker_id'];
        $_SESSION['rol']          = $gebruiker['Rol'];
        $_SESSION['voornaam']     = $gebruiker['Voornaam'];
        $_SESSION['achternaam']   = $gebruiker['Achternaam'];
    }

    // Uitloggen door de sessie te vernietigen
    public static function logout() {
        session_destroy();
    }

    // Vereisen dat een gebruiker is ingelogd, anders doorsturen naar de loginpagina
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/pages/login.php');
            exit;
        }
    }

    // Vereisen dat een gebruiker een specifieke rol heeft, anders doorsturen naar het dashboard
    public static function requireRol(int $rol) {
        self::requireLogin();
        if (self::getRol() !== $rol) {
            header('Location: ' . BASE_URL . '/pages/dashboard.php');
            exit;
        }
    }
}