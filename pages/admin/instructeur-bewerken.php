<?php
/*
Naam: Dominik Bulla
Versie: 1.0
Datum: 08-04-2026 / 09-04-2026
Beschrijving: Admin pagina om een instructeur te bewerken.
*/

$pageTitle = 'Instructeur bewerken';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Gebruiker.php';
require_once __DIR__ . '/../../includes/auth.php';

// Alleen rol 1 (admin) mag hier
Auth::requireRol(1);

// Maak model aan
$model = new Gebruiker();

// Haal instructeur op basis van ID uit query string
$id = (int)($_GET['id'] ?? 0);
$instructeur = $id > 0 ? $model->getById($id) : false;

// Controleer of instructeur bestaat en rol 2 (instructeur) heeft
if (!$instructeur || (int)($instructeur['Rol'] ?? 0) !== 2) {
    include __DIR__ . '/../../includes/header.php';
    ?>
    <div class="container main-content">
        <div class="alert alert-danger">Instructeur niet gevonden.</div>
        <a href="instructeur-overzicht.php" class="btn btn-outline-secondary btn-sm">Terug</a>
    </div>
    <?php
    include __DIR__ . '/../../includes/footer.php';
    exit;
}

// Initialiseer variabelen voor formulierwaarden en fouten
$errors = [];
$success = false;

// Vul formulierwaarden in met bestaande data of lege waarden
$values = [
    'voornaam' => $instructeur['Voornaam'] ?? '',
    'tussenvoegsel' => $instructeur['Tussenvoegsel'] ?? '',
    'achternaam' => $instructeur['Achternaam'] ?? '',
    'gebruikersnaam' => $instructeur['Gebruikersnaam'] ?? '',
    'adres' => $instructeur['Adres'] ?? '',
    'email' => $instructeur['Email'] ?? '',
    'telefoon' => $instructeur['Telefoon'] ?? '',
    'geboortedatum' => $instructeur['Geboortedatum'] ?? '',
    'registratiedatum' => $instructeur['RegistratieDatum'] ?? '',
    'actief' => (int)($instructeur['Actief'] ?? 0),
];

// Verwerk formulier indien verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['voornaam'] = trim($_POST['voornaam'] ?? '');
    $values['tussenvoegsel'] = trim($_POST['tussenvoegsel'] ?? '');
    $values['achternaam'] = trim($_POST['achternaam'] ?? '');
    $values['gebruikersnaam'] = trim($_POST['gebruikersnaam'] ?? '');
    $values['adres'] = trim($_POST['adres'] ?? '');
    $values['email'] = trim($_POST['email'] ?? '');
    $values['telefoon'] = trim($_POST['telefoon'] ?? '');
    $values['geboortedatum'] = trim($_POST['geboortedatum'] ?? '');
    $values['registratiedatum'] = trim($_POST['registratiedatum'] ?? '');
    $values['actief'] = isset($_POST['actief']) ? 1 : 0;

    $nieuwWachtwoord = $_POST['wachtwoord'] ?? '';

    // Validatie
    if (
        $values['voornaam'] === '' || $values['achternaam'] === '' || $values['gebruikersnaam'] === '' ||
        $values['adres'] === '' || $values['email'] === '' || $values['telefoon'] === '' ||
        $values['geboortedatum'] === '' || $values['registratiedatum'] === ''
    ) {
        $errors[] = 'Vul alle verplichte velden in.';
    }

    if ($values['voornaam'] !== '' && (strlen($values['voornaam']) < 2 || strlen($values['voornaam']) > 30)) {
        $errors[] = 'Voornaam moet tussen 2 en 30 tekens zijn.';
    }

    if ($values['achternaam'] !== '' && (strlen($values['achternaam']) < 2 || strlen($values['achternaam']) > 30)) {
        $errors[] = 'Achternaam moet tussen 2 en 30 tekens zijn.';
    }

    if ($values['tussenvoegsel'] !== '' && strlen($values['tussenvoegsel']) > 15) {
        $errors[] = 'Tussenvoegsel mag maximaal 15 tekens zijn.';
    }

    if ($values['gebruikersnaam'] !== '' && (!preg_match('/^[a-zA-Z0-9_]+$/', $values['gebruikersnaam']) || strlen($values['gebruikersnaam']) < 4 || strlen($values['gebruikersnaam']) > 20)) {
        $errors[] = 'Gebruikersnaam moet 4 t/m 20 tekens zijn en mag alleen letters, cijfers en _ bevatten.';
    }

    if ($nieuwWachtwoord !== '' && (strlen($nieuwWachtwoord) < 6 || strlen($nieuwWachtwoord) > 50)) {
        $errors[] = 'Nieuw wachtwoord moet 6 t/m 50 tekens zijn.';
    }

    if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vul een geldig e-mailadres in.';
    }

    if ($values['telefoon'] !== '' && (strlen($values['telefoon']) < 6 || strlen($values['telefoon']) > 20)) {
        $errors[] = 'Telefoonnummer moet tussen 6 en 20 tekens zijn.';
    }

    if ($values['geboortedatum'] !== '' && strtotime($values['geboortedatum']) === false) {
        $errors[] = 'Geboortedatum is ongeldig.';
    }

    if ($values['registratiedatum'] !== '' && strtotime($values['registratiedatum']) === false) {
        $errors[] = 'Datum ingevoerd is ongeldig.';
    }

    if (!$errors && $model->bestaatGebruikersnaam($values['gebruikersnaam'], $id)) {
        $errors[] = 'Deze gebruikersnaam bestaat al. Kies een andere.';
    }

    // Als er geen fouten zijn, update de instructeur
    if (!$errors) {
        $ok = $model->bijwerken($id, [
            'voornaam' => $values['voornaam'],
            'tussenvoegsel' => $values['tussenvoegsel'],
            'achternaam' => $values['achternaam'],
            'gebruikersnaam' => $values['gebruikersnaam'],
            'actief' => $values['actief'],
            'geslaagd' => (int)($instructeur['Geslaagd'] ?? 0),
            'adres' => $values['adres'],
            'ophaaladres' => (string)($instructeur['Ophaaladres'] ?? ''),
            'email' => $values['email'],
            'telefoon' => $values['telefoon'],
            'registratiedatum' => $values['registratiedatum'],
            'geboortedatum' => $values['geboortedatum'],
        ]);

        if ($ok && $nieuwWachtwoord !== '') {
            $model->wachtwoordBijwerken($id, $nieuwWachtwoord);
        }
        // Herlaad instructeur data na update en als update onsuccessful was, toon foutmelding
        if ($ok) {
            $success = true;
            $instructeur = $model->getById($id);
        } else {
            $errors[] = 'Opslaan is mislukt. Probeer opnieuw.';
        }
    }
}

include __DIR__ . '/../../includes/header.php';
?>
<!-- Sidebar -->
<div class="container-fluid">
<div class="row">
<nav class="col-auto sidebar pt-3">
    <ul class="nav flex-column gap-1">
        <li><a href="<?= BASE_URL ?>/pages/dashboard.php" class="nav-link">Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/les-overzicht.php" class="nav-link">Lesoverzicht</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/lespakket.php" class="nav-link">Lespakket</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/klant-overzicht.php" class="nav-link">Klanten</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/instructeur-overzicht.php" class="nav-link active">Instructeurs</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/wagenpark.php" class="nav-link">Wagenpark</a></li>
        <li><a href="<?= BASE_URL ?>/pages/admin/ziekmelding.php" class="nav-link">Ziekmeldingen</a></li>
    </ul>
</nav>

<!-- Main content -->
<main class="col main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Instructeur bewerken</h5>
        <a href="instructeur-overzicht.php" class="btn btn-outline-secondary btn-sm">Terug</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">Opgeslagen.</div>
    <?php endif; ?>

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

    <!-- Formulier -->
    <div class="bg-white rounded border p-4" style="max-width: 650px;">
        <form method="POST">
            <div class="row g-3">
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

                <div class="col-md-6">
                    <label class="form-label">Gebruikersnaam *</label>
                    <input type="text" name="gebruikersnaam" class="form-control" required minlength="4" maxlength="20"
                           value="<?= htmlspecialchars($values['gebruikersnaam']) ?>">
                    <div class="form-text">Alleen letters, cijfers en _</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nieuw wachtwoord (optioneel)</label>
                    <input type="password" name="wachtwoord" class="form-control" minlength="6" maxlength="50">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Adres *</label>
                    <input type="text" name="adres" class="form-control" required maxlength="255"
                           value="<?= htmlspecialchars($values['adres']) ?>">
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

                <div class="col-md-6">
                    <label class="form-label">Geboortedatum *</label>
                    <input type="date" name="geboortedatum" class="form-control" required
                           value="<?= htmlspecialchars($values['geboortedatum']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Datum ingevoerd *</label>
                    <input type="date" name="registratiedatum" class="form-control" required
                           value="<?= htmlspecialchars($values['registratiedatum']) ?>">
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="actief" id="actief" <?= $values['actief'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="actief">Actief</label>
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
<?php include __DIR__ . '/../../includes/footer.php'; ?>
