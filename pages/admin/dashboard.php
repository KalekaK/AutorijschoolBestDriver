
<?php
/*
Naam: Dominik Bulla
Versie: 1.0
Datum: 08-04-2026
Beschrijving: Admin dashboard (doorsturen naar hoofd dashboard).
*/

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';

Auth::requireRol(1);
header('Location: ' . BASE_URL . '/pages/dashboard.php');
exit;

