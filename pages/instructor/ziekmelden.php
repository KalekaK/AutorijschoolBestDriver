<?php
/*
Naam: Ryan Sitaldien
Versie: 1.0
Datum: 08-04-2026
Beschrijving: Instructeur kan ziekmeldingen indienen.
*/
$pageTitle = 'Ziekmelden';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Ziekmelding.php';
require_once __DIR__ . '/../../includes/auth.php';

Auth::requireRol(2);

$ziekmeldingModel = new Ziekmelding();
$melding = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = $ziekmeldingModel->toevoegen(
        Auth::getGebruikerId(),
        $_POST['van'],
        $_POST['tot'],
        trim($_POST['toelichting'])
    );
    $melding = $ok ? 'success' : 'danger';
}

$ziekmeldingen = $ziekmeldingModel->getByGebruiker(Auth::getGebruikerId());
include __DIR__ . '/../../includes/header.php';
?>
<div class="container main-content">
    <h5 class="mb-4">Ziekmelden</h5>

    <?php if ($melding === 'success'): ?>
        <div class="alert alert-success">Ziekmelding succesvol ingediend!</div>
    <?php elseif ($melding === 'danger'): ?>
        <div class="alert alert-danger">Er ging iets mis. Probeer opnieuw.</div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="bg-white rounded border p-4">
                <h6 class="fw-semibold mb-3">Nieuwe ziekmelding</h6>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Van</label>
                        <input type="date" name="van" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tot</label>
                        <input type="date" name="tot" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Toelichting</label>
                        <textarea name="toelichting" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Indienen</button>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <h6 class="fw-semibold mb-3">Mijn ziekmeldingen</h6>
            <div class="bg-white rounded border">
                <table class="table table-hover table-bestdriver mb-0">
                    <thead>
                        <tr><th>Van</th><th>Tot</th><th>Toelichting</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($ziekmeldingen)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-4">Geen ziekmeldingen.</td></tr>
                    <?php else: ?>
                        <?php foreach ($ziekmeldingen as $z): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($z['Van'])) ?></td>
                            <td><?= date('d-m-Y', strtotime($z['Tot'])) ?></td>
                            <td><?= htmlspecialchars($z['Toelichting']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
