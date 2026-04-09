<?php
/*
Naam: Dominik Bulla
Versie: 1.0
Datum: 08-04-2026 / 09-04-2026
Beschrijving: Hoofd dashboard pagina die gebruikers na inloggen zien. Toont verschillende opties afhankelijk van de rol (admin, instructeur, klant). Admins krijgen een overzicht met links naar alle beheerpagina's, instructeurs zien hun lesoverzicht en ziekmeldingen, klanten zien hun lessen en profiel.
*/
$pageTitle = 'Dashboard';
// Vereiste bestanden includen
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Gebruiker.php';
require_once __DIR__ . '/../includes/auth.php';

// Alleen toegankelijk voor ingelogde gebruikers
Auth::requireLogin();
// Rol ophalen voor gepersonaliseerde dashboard
$rol = Auth::getRol();
include __DIR__ . '/../includes/header.php';
?>
<div class="container-fluid">
<div class="row">

<!-- Sidebars per rol, dus admin, instructeur en klant hebben verschillende opties -->
<?php if ($rol === 1): ?>
<nav class="col-auto sidebar pt-3">
    <ul class="nav flex-column gap-1">
        <li><a href="<?= BASE_URL ?>/pages/dashboard.php" class="nav-link active">Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/les-overzicht.php" class="nav-link">Lesoverzicht</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/lespakket.php" class="nav-link">Lespakket</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/klant-overzicht.php" class="nav-link">Klanten</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/wagenpark.php" class="nav-link">Wagenpark</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/ziekmelding.php" class="nav-link">Ziekmeldingen</a></li>
    </ul>
</nav>
<?php endif; ?>

<main class="col main-content">
    <h5 class="mb-4">Welkom, <?= htmlspecialchars(Auth::getNaam()) ?>!</h5>

    <!-- Admin dashboard -->
    <?php if ($rol === 1): ?>
    <div class="row g-3">
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/pages/admin/les-overzicht.php" class="text-decoration-none">
                <div class="dashboard-card text-center">
                    <h6 class="fw-semibold">Lessen</h6>
                    <p class="text-muted small mb-2">Bekijk en beheer alle lessen</p>
                    <button class="btn btn-primary btn-sm w-100">Ga naar lessen</button>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/pages/admin/lespakket.php" class="text-decoration-none">
                <div class="dashboard-card text-center">
                    <h6 class="fw-semibold">Lespakketten</h6>
                    <p class="text-muted small mb-2">Beheer lespakketten en prijzen</p>
                    <button class="btn btn-primary btn-sm w-100">Ga naar pakketten</button>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/pages/admin/klant-overzicht.php" class="text-decoration-none">
                <div class="dashboard-card text-center">
                    <h6 class="fw-semibold">Klanten</h6>
                    <p class="text-muted small mb-2">Beheer klantgegevens</p>
                    <button class="btn btn-primary btn-sm w-100">Ga naar klanten</button>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/pages/admin/wagenpark.php" class="text-decoration-none">
                <div class="dashboard-card text-center">
                    <h6 class="fw-semibold">Wagenpark</h6>
                    <p class="text-muted small mb-2">Beheer voertuigen</p>
                    <button class="btn btn-primary btn-sm w-100">Ga naar wagenpark</button>
                </div>
            </a>
        </div>
    </div>

    <!-- Instructeur dashboard -->
    <?php elseif ($rol === 2): ?>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="dashboard-card">
                <h6 class="fw-semibold">Mijn lessen</h6>
                <p class="text-muted small">Bekijk je geplande lessen.</p>
                <a href="<?= BASE_URL ?>/pages/instructor/les-overzicht.php" class="btn btn-primary btn-sm">Lesoverzicht</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-card">
                <h6 class="fw-semibold">Ziekmeld</h6>
                <p class="text-muted small">Ben je ziek? Meld je hier af.</p>
                <a href="<?= BASE_URL ?>/pages/instructor/ziekmelden.php" class="btn btn-outline-danger btn-sm">Ziekmeldingen</a>
            </div>
        </div>
    </div>

    <!-- Klant dashboard -->
    <?php elseif ($rol === 3): ?>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="dashboard-card">
                <h6 class="fw-semibold">Mijn lessen</h6>
                <p class="text-muted small">Bekijk je geplande lessen.</p>
                <a href="<?= BASE_URL ?>/pages/klant/les-overzicht.php" class="btn btn-primary btn-sm">Bekijken</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-card">
                <h6 class="fw-semibold">Mijn profiel</h6>
                <p class="text-muted small">Bekijk je persoonsgegevens.</p>
                <a href="<?= BASE_URL ?>/pages/klant/profiel.php" class="btn btn-outline-primary btn-sm">Profiel</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>