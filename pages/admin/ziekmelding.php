<?php
/*
Naam: Ryan Sitaldien
Versie: 1.0
Datum: 08-04-2026
Beschrijving: Admin overzicht van alle ziekmeldingen (alleen bekijken).
*/

$pageTitle = 'Ziekmeldingen';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Ziekmelding.php';
require_once __DIR__ . '/../../includes/auth.php';
//Alleen admin mag alle ziekmeldingen bekijken//
Auth::requireRol(1);

$model = new Ziekmelding();
$ziekmeldingen = $model->getAll();

include __DIR__ . '/../../includes/header.php';
?>
<div class="container-fluid">
<div class="row">
<nav class="col-auto sidebar pt-3">
    <ul class="nav flex-column gap-1">
        <li><a href="<?= BASE_URL ?>/pages/dashboard.php" class="nav-link">Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/les-overzicht.php" class="nav-link">Lesoverzicht</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/lespakket.php" class="nav-link">Lespakket</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/klant-overzicht.php" class="nav-link">Klanten</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/instructeur-overzicht.php" class="nav-link">Instructeurs</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/wagenpark.php" class="nav-link">Wagenpark</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/ziekmelding.php" class="nav-link active">Ziekmeldingen</a></li>
    </ul>
</nav>

<main class="col main-content">
    <h5 class="mb-3">Ziekmeldingen</h5>

    <div class="bg-white rounded border">
        <table class="table table-hover table-bestdriver mb-0">
            <thead>
                <tr>
                    <th>Instructeur</th>
                    <th>Van</th>
                    <th>Tot</th>
                    <th>Toelichting</th>
                </tr>
            </thead>
            <tbody>
                   //Controleer of er ziekmeldingen zijn//
            <?php if (empty($ziekmeldingen)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Geen ziekmeldingen.</td></tr>
            <?php else: foreach ($ziekmeldingen as $z): ?>
                <tr>
                    <td><?= htmlspecialchars($z['instructeur_naam'] ?? '') ?></td>
                    <td><?= date('d-m-Y', strtotime($z['Van'])) ?></td>
                    <td><?= date('d-m-Y', strtotime($z['Tot'])) ?></td>
                    <td><?= htmlspecialchars($z['Toelichting']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="p-2 text-muted small border-top"><?= count($ziekmeldingen) ?> resultaten</div>
    </div>
</main>
</div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
