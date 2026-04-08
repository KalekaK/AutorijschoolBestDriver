<?php


/*
Naam: Adrian
Versie: 1.1
Datum: 08-04-2026
Beschrijving: Simple Logout flow.
*/

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Log de gebruiker uit
Auth::logout();

// Stuur terug naar de inlogpagina
header('Location: ' . BASE_URL . '/pages/login.php');
exit;