<?php
/*
Naam: Adrian
Versie: 1.1
Datum: 08-04-2026
Beschrijving: Admin lespakketten (CRUD eenvoudig).
*/
 // Vereiste bestanden en initialisatie
$pageTitle = 'Lespakketten';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Lespakket.php';
require_once __DIR__ . '/../../includes/auth.php';
 
// Alleen toegankelijk voor admins (rol 1)
Auth::requireRol(1);
 
$lespakketModel = new Lespakket();
$melding = '';
 
// Verwijderen
if (isset($_GET['verwijder'])) {
    $lespakketModel->verwijderen((int)$_GET['verwijder']);
    header('Location: lespakket.php');
    exit;
}
 
// Toevoegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['actie'] ?? '') === 'toevoegen') {
    $lespakketModel->toevoegen($_POST);
    header('Location: lespakket.php');
    exit;
}
 
// Zoekterm verwerken en pakketten ophalen
$zoek      = trim($_GET['zoek'] ?? '');
$pakketten = $lespakketModel->getAll($zoek);
include __DIR__ . '/../../includes/header.php';
?>
<!-- Sidebar en main content -->
<div class="container-fluid">
<div class="row">
<nav class="col-auto sidebar pt-3">
    <ul class="nav flex-column gap-1">
        <li><a href="<?= BASE_URL ?>/pages/dashboard.php" class="nav-link">Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/les-overzicht.php" class="nav-link">Lesoverzicht</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/lespakket.php" class="nav-link active">Lespakket</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/klant-overzicht.php" class="nav-link">Klanten</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/instructeur-overzicht.php" class="nav-link">Instructeurs</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/wagenpark.php" class="nav-link">Wagenpark</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/ziekmelding.php" class="nav-link">Ziekmeldingen</a></li>
    </ul>
</nav>
<main class="col main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Lespakketten</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#pakketModal">
            Pakket toevoegen
        </button>
    </div>
 
    <form class="mb-3" method="GET">
        <div class="input-group" style="max-width:300px">
            <input type="text" name="zoek" class="form-control"
                   placeholder="Zoeken..." value="<?= htmlspecialchars($zoek) ?>">
        </div>
    </form>
 
    <!-- Tabel met lespakketten -->
    <div class="bg-white rounded border">
        <table class="table table-hover table-bestdriver mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Omschrijving</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <!-- Pakketten weergeven -->
            <?php if (empty($pakketten)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Geen pakketten gevonden.</td></tr>
            <?php else: ?>
                <?php foreach ($pakketten as $p): ?>
                <tr>
                    <td><?= $p['Lespakket_id'] ?></td>
                    <td><?= htmlspecialchars($p['Naam']) ?></td>
                    <td><?= htmlspecialchars($p['Omschrijving']) ?></td>
                    <td class="text-end">
                        <a href="?verwijder=<?= $p['Lespakket_id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Pakket verwijderen?')">
                            Verwijderen
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="p-2 text-muted small border-top"><?= count($pakketten) ?> resultaten</div>
    </div>
</main>
</div>
</div>

 <!-- Lespakket toevoegen -->
<div class="modal fade" id="pakketModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST">
        <input type="hidden" name="actie" value="toevoegen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lespakket toevoegen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Code</label>
                    <select name="naam" class="form-select" required>
                        <option value="">Kies...</option>
                        <option value="Auto">Auto</option>
                        <option value="Motor">Motor</option>
                        <option value="Groot">Groot</option>
                        <option value="BE">BE</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Omschrijving</label>
                    <textarea name="omschrijving" class="form-control" rows="2"></textarea>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <label class="form-label">Aantal lessen</label>
                        <input type="number" name="aantal" class="form-control" min="1" required>
                    </div>
                    <div class="col">
                        <label class="form-label">Prijs (€)</label>
                        <input type="number" name="prijs" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>
        </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>