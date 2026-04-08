<?php
if (session_status() === PHP_SESSION_NONE) session_start();

class Auth {
    public static function isLoggedIn(): bool {
        return isset($_SESSION['gebruiker_id']);
    }

    public static function getRol(): int {
        return $_SESSION['rol'] ?? 0;
    }

    public static function getGebruikerId(): int {
        return $_SESSION['gebruiker_id'] ?? 0;
    }

    public static function getNaam(): string {
        return $_SESSION['voornaam'] ?? '';
    }

    public static function login(array $gebruiker): void {
        $_SESSION['gebruiker_id'] = $gebruiker['Gebruiker_id'];
        $_SESSION['rol']          = $gebruiker['Rol'];
        $_SESSION['voornaam']     = $gebruiker['Voornaam'];
        $_SESSION['achternaam']   = $gebruiker['Achternaam'];
    }

    public static function logout(): void {
        session_destroy();
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/pages/login.php');
            exit;
        }
    }

    public static function requireRol(int $rol): void {
        self::requireLogin();
        if (self::getRol() !== $rol) {
            header('Location: ' . BASE_URL . '/pages/dashboard.php');
            exit;
        }
    }
}