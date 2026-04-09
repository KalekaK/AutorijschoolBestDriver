<?php
/*
Naam: Adrian
Versie: 1.0
Datum: 09-04-2026
Beschrijving: Admin pagina om een nieuwe klant toe te voegen.
*/

$pageTitle = 'Klant toevoegen';

// benodigde bestanden laden
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Gebruiker.php';
require_once __DIR__ . '/../../includes/auth.php';

// alleen admins mogen deze pagina zien
Auth::requireRol(1);

$model = new Gebruiker();
$pdo = Database::getInstance();

// ophaallocaties ophalen voor de dropdown
$ophaallocaties = $pdo->query("SELECT * FROM ophaallocatie ORDER BY Plaats, Adres")->fetchAll();

$errors = [];

// standaardwaarden zodat velden niet leeg zijn na een fout
$values = [
    'voornaam' => '',
    'tussenvoegsel' => '',
    'achternaam' => '',
    'gebruikersnaam' => '',
    'adres' => '',
    'ophaaladres' => '',
    'email' => '',
    'telefoon' => '',
    'registratiedatum' => date('Y-m-d'),
    'actief' => 1,
    'geslaagd' => 0,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // formulierdata ophalen en opschonen
    $values['voornaam'] = trim($_POST['voornaam'] ?? '');
    $values['tussenvoegsel'] = trim($_POST['tussenvoegsel'] ?? '');
    $values['achternaam'] = trim($_POST['achternaam'] ?? '');
    $values['gebruikersnaam'] = trim($_POST['gebruikersnaam'] ?? '');
    $values['adres'] = trim($_POST['adres'] ?? '');
    $values['ophaaladres'] = trim($_POST['ophaaladres'] ?? '');
    $values['email'] = trim($_POST['email'] ?? '');
    $values['telefoon'] = trim($_POST['telefoon'] ?? '');
    $values['registratiedatum'] = trim($_POST['registratiedatum'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';

    // checkboxes staan niet in $_POST als ze niet aangevinkt zijn
    $values['actief'] = isset($_POST['actief']) ? 1 : 0;
    $values['geslaagd'] = isset($_POST['geslaagd']) ? 1 : 0;

    // controleer of alle verplichte velden ingevuld zijn
    if (
        $values['voornaam'] === '' || $values['achternaam'] === '' || $values['gebruikersnaam'] === '' ||
        $values['adres'] === '' || $values['ophaaladres'] === '' || $values['email'] === '' ||
        $values['telefoon'] === '' || $values['registratiedatum'] === '' || $wachtwoord === ''
    ) {
        $errors[] = 'Vul alle verplichte velden in.';
    }

    // voornaam valideren op lengte
    if ($values['voornaam'] !== '' && (strlen($values['voornaam']) < 2 || strlen($values['voornaam']) > 30)) {
        $errors[] = 'Voornaam moet tussen 2 en 30 tekens zijn.';
    }

    if ($values['achternaam'] !== '' && (strlen($values['achternaam']) < 2 || strlen($values['achternaam']) > 30)) {
        $errors[] = 'Achternaam moet tussen 2 en 30 tekens zijn.';
    }

    if ($values['tussenvoegsel'] !== '' && strlen($values['tussenvoegsel']) > 15) {
        $errors[] = 'Tussenvoegsel mag maximaal 15 tekens zijn.';
    }

    // gebruikersnaam mag alleen letters, cijfers en underscores bevatten
    if ($values['gebruikersnaam'] !== '' && (!preg_match('/^[a-zA-Z0-9_]+$/', $values['gebruikersnaam']) || strlen($values['gebruikersnaam']) < 4 || strlen($values['gebruikersnaam']) > 20)) {
        $errors[] = 'Gebruikersnaam moet 4 t/m 20 tekens zijn en mag alleen letters, cijfers en _ bevatten.';
    }

    if ($wachtwoord !== '' && (strlen($wachtwoord) < 6 || strlen($wachtwoord) > 50)) {
        $errors[] = 'Wachtwoord moet 6 t/m 50 tekens zijn.';
    }

    if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vul een geldig e-mailadres in.';
    }

    if ($values['telefoon'] !== '' && (strlen($values['telefoon']) < 6 || strlen($values['telefoon']) > 20)) {
        $errors[] = 'Telefoonnummer moet tussen 6 en 20 tekens zijn.';
    }

    if ($values['registratiedatum'] !== '' && strtotime($values['registratiedatum']) === false) {
        $errors[] = 'Registratiedatum is ongeldig.';
    }

    // controleer of de gebruikersnaam al bestaat in de database
    if (!$errors && $model->bestaatGebruikersnaam($values['gebruikersnaam'])) {
        $errors[] = 'Deze gebruikersnaam bestaat al. Kies een andere.';
    }

    // alles ok, klant opslaan
    if (!$errors) {
        $ok = $model->toevoegen([
            'gebruikersnaam' => $values['gebruikersnaam'],
            'wachtwoord' => $wachtwoord,
            'voornaam' => $values['voornaam'],
            'tussenvoegsel' => $values['tussenvoegsel'],
            'achternaam' => $values['achternaam'],
            'adres' => $values['adres'],
            'ophaaladres' => $values['ophaaladres'],
            'email' => $values['email'],
            'telefoon' => $values['telefoon'],
            'registratiedatum' => $values['registratiedatum'],
            'rol' => 3, // 3 = klant
            'actief' => $values['actief'],
            'geslaagd' => $values['geslaagd'],
        ]);

        if ($ok) {
            // doorsturen naar overzicht na succesvol opslaan
            header('Location: klant-overzicht.php?melding=opgeslagen');
            exit;
        }

        $errors[] = 'Opslaan is mislukt. Probeer opnieuw.';
    }
}

include __DIR__ . '/../../includes/header.php';
?>
<div class="container-fluid">
<div class="row">
<nav class="col-auto sidebar pt-3">
    <ul class="nav flex-column gap-1">
        <li><a href="<?= BASE_URL ?>/pages/dashboard.php" class="nav-link">Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/les-overzicht.php" class="nav-link">Lesoverzicht</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/lespakket.php" class="nav-link">Lespakket</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/klant-overzicht.php" class="nav-link active">Klanten</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/instructeur-overzicht.php" class="nav-link">Instructeurs</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/wagenpark.php" class="nav-link">Wagenpark</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/ziekmelding.php" class="nav-link">Ziekmeldingen</a></li>
    </ul>
</nav>

<main class="col main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Klant toevoegen</h5>
        <a href="klant-overzicht.php" class="btn btn-outline-secondary btn-sm">Terug</a>
    </div>

    <!-- foutmeldingen tonen -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Controleer het formulier</div>
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded border p-4" style="max-width: 650px;">
        <form method="POST" id="klantForm">
            <div class="row g-3">
                <!-- naam -->
                <div class="col-md-4">
                    <label class="form-label">Voornaam *</label>
                    <input type="text" name="voornaam" class="form-control" required minlength="2" maxlength="30"
                           value="<?= htmlspecialchars($values['voornaam']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tussenvoegsel</label>
                    <input type="text" name="tussenvoegsel" class="form-control" maxlength="15"
                           value="<?= htmlspecialchars($values['tussenvoegsel']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Achternaam *</label>
                    <input type="text" name="achternaam" class="form-control" required minlength="2" maxlength="30"
                           value="<?= htmlspecialchars($values['achternaam']) ?>">
                </div>

                <!-- inloggegevens -->
                <div class="col-md-6">
                    <label class="form-label">Gebruikersnaam *</label>
                    <input type="text" name="gebruikersnaam" class="form-control" required minlength="4" maxlength="20"
                           value="<?= htmlspecialchars($values['gebruikersnaam']) ?>">
                    <div class="form-text">Alleen letters, cijfers en _</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Wachtwoord *</label>
                    <input type="password" name="wachtwoord" class="form-control" required minlength="6" maxlength="50">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Adres *</label>
                    <input type="text" name="adres" class="form-control" required maxlength="255"
                           value="<?= htmlspecialchars($values['adres']) ?>">
                </div>

                <!-- ophaaladres komt uit de database -->
                <div class="col-md-6">
                    <label class="form-label">Ophaaladres *</label>
                    <select name="ophaaladres" class="form-select" required>
                        <option value="">Kies...</option>
                        <?php foreach ($ophaallocaties as $o): ?>
                            <?php $adres = (string)($o['Adres'] ?? ''); ?>
                            <option value="<?= htmlspecialchars($adres) ?>" <?= $values['ophaaladres'] === $adres ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($o['Plaats'] ?? '') . ' - ' . ($o['Adres'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Registratiedatum *</label>
                    <input type="date" name="registratiedatum" class="form-control" required
                           value="<?= htmlspecialchars($values['registratiedatum']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">E-mailadres *</label>
                    <input type="email" name="email" class="form-control" required maxlength="255"
                           value="<?= htmlspecialchars($values['email']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telefoonnummer *</label>
                    <input type="text" name="telefoon" class="form-control" required maxlength="20"
                           value="<?= htmlspecialchars($values['telefoon']) ?>">
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="actief" id="actief" <?= $values['actief'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="actief">Actief</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="geslaagd" id="geslaagd" <?= $values['geslaagd'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="geslaagd">Geslaagd</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>
        </form>
    </div>
</main>
</div>
</div>

<!-- extra validatie aan de voorkant -->
<script>
(function(){
    var form = document.getElementById('klantForm');
    if(!form) return;

    form.addEventListener('submit', function(e){
        var gebruikersnaam = form.querySelector('input[name="gebruikersnaam"]').value.trim();
        var wachtwoord = form.querySelector('input[name="wachtwoord"]').value;
        var email = form.querySelector('input[name="email"]').value.trim();
        var telefoon = form.querySelector('input[name="telefoon"]').value.trim();

        // gebruikersnaam mag alleen letters, cijfers en underscore bevatten
        var re = /^[a-zA-Z0-9_]{4,20}$/;
        if(!re.test(gebruikersnaam)){
            alert('Gebruikersnaam mag alleen letters, cijfers en _ bevatten (4 t/m 20 tekens).');
            e.preventDefault();
            return;
        }

        if(wachtwoord.length < 6 || wachtwoord.length > 50){
            alert('Wachtwoord moet 6 t/m 50 tekens zijn.');
            e.preventDefault();
            return;
        }

        // simpele email check
        if(email !== '' && email.indexOf('@') === -1){
            alert('Vul een geldig e-mailadres in.');
            e.preventDefault();
            return;
        }

        if(telefoon.length < 6 || telefoon.length > 20){
            alert('Telefoonnummer moet tussen 6 en 20 tekens zijn.');
            e.preventDefault();
        }
    });
})();
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>