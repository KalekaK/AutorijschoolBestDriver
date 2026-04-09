<?php
/*
naam: ryan sitaldien
versie: 1.0
datum: 08-04-2026
beschrijving: admin pagina om een nieuwe instructeur toe te voegen.
*/

$pageTitle = 'Instructeur toevoegen';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Gebruiker.php';
require_once __DIR__ . '/../../includes/auth.php';

// alleen admins mogen hier komen
Auth::requireRol(1);

$model = new Gebruiker();

$errors = [];
// standaardwaarden voor formulier, handig bij validatiefouten
$values = [
    'voornaam'         => '',
    'tussenvoegsel'    => '',
    'achternaam'       => '',
    'gebruikersnaam'   => '',
    'adres'            => '',
    'email'            => '',
    'telefoon'         => '',
    'geboortedatum'    => '',
    'registratiedatum' => date('Y-m-d'),
    'actief'           => 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // formulierwaarden ophalen en trimmen
    $values['voornaam']         = trim($_POST['voornaam'] ?? '');
    $values['tussenvoegsel']    = trim($_POST['tussenvoegsel'] ?? '');
    $values['achternaam']       = trim($_POST['achternaam'] ?? '');
    $values['gebruikersnaam']   = trim($_POST['gebruikersnaam'] ?? '');
    $values['adres']            = trim($_POST['adres'] ?? '');
    $values['email']            = trim($_POST['email'] ?? '');
    $values['telefoon']         = trim($_POST['telefoon'] ?? '');
    $values['geboortedatum']    = trim($_POST['geboortedatum'] ?? '');
    $values['registratiedatum'] = trim($_POST['registratiedatum'] ?? '');
    $wachtwoord                 = $_POST['wachtwoord'] ?? '';

    // checkbox actief is alleen aanwezig als hij aangevinkt is
    $values['actief'] = isset($_POST['actief']) ? 1 : 0;

    // verplichte velden controleren
    if (
        $values['voornaam'] === '' || $values['achternaam'] === '' || $values['gebruikersnaam'] === '' ||
        $values['adres'] === '' || $values['email'] === '' || $values['telefoon'] === '' ||
        $values['geboortedatum'] === '' || $values['registratiedatum'] === '' || $wachtwoord === ''
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

    // gebruikersnaam mag alleen letters, cijfers en underscore bevatten
    if (
        $values['gebruikersnaam'] !== '' &&
        (!preg_match('/^[a-zA-Z0-9_]+$/', $values['gebruikersnaam']) ||
            strlen($values['gebruikersnaam']) < 4 ||
            strlen($values['gebruikersnaam']) > 20)
    ) {
        $errors[] = 'Gebruikersnaam moet 4 t/m 20 tekens zijn en mag alleen letters, cijfers en _ bevatten.';
    }

    // basic check op wachtwoordlengte
    if ($wachtwoord !== '' && (strlen($wachtwoord) < 6 || strlen($wachtwoord) > 50)) {
        $errors[] = 'Wachtwoord moet 6 t/m 50 tekens zijn.';
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

    // gebruikersnaam moet uniek zijn
    if (!$errors && $model->bestaatGebruikersnaam($values['gebruikersnaam'])) {
        $errors[] = 'Deze gebruikersnaam bestaat al. Kies een andere.';
    }

    // als alles goed is, instructeur opslaan
    if (!$errors) {
        $ok = $model->toevoegen([
            'gebruikersnaam'   => $values['gebruikersnaam'],
            'wachtwoord'       => $wachtwoord,
            'voornaam'         => $values['voornaam'],
            'tussenvoegsel'    => $values['tussenvoegsel'],
            'achternaam'       => $values['achternaam'],
            'rol'              => 2,
            'actief'           => $values['actief'],
            'geslaagd'         => 0,
            'adres'            => $values['adres'],
            'ophaaladres'      => '',
            'email'            => $values['email'],
            'telefoon'         => $values['telefoon'],
            'registratiedatum' => $values['registratiedatum'],
            'geboortedatum'    => $values['geboortedatum'],
        ]);

        if ($ok) {
            header('Location: instructeur-overzicht.php?melding=opgeslagen');
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
        ><a href="<?= BASE_URL ?>/pages/dashboard.php" class="nav-link">Dashboard</a></li>
        ><a href="<?= BASE_URL ?>/pages/admin/les-overzicht.php" class="nav-link">Lesoverzicht</a></li>
        ><a href="<?= BASE_URL ?>/pages/admin/lespakket.php" class="nav-link">Lespakket</a></li>
        ><a href="<?= BASE_URL ?>/pages/admin/klant-overzicht.php" class="nav-link">Klanten</a></li>
        ><a href="<?= BASE_URL ?>/pages/admin/instructeur-overzicht.php" class="nav-link active">Instructeurs</a></li>
        ><a href="<?= BASE_URL ?>/pages/admin/wagenpark.php" class="nav-link">Wagenpark</a></li>
        ><a href="<?= BASE_URL ?>/pages/admin/ziekmelding.php" class="nav-link">Ziekmeldingen</a></li>
    </ul>
</nav>

<main class="col main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Instructeur toevoegen</h5>
        <a href="instructeur-overzicht.php" class="btn btn-outline-secondary btn-sm">Terug</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Controleer het formulier</div>
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    ><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded border p-4" style="max-width: 650px;">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    abel class="form-label">Voornaam *</label>
                    <input type="text" name="voornaam" class="form-control" required minlength="2" maxlength="30"
                           value="<?= htmlspecialchars($values['voornaam']) ?>">
                </div>
                <div class="col-md-4">
                    abel class="form-label">Tussenvoegsel</label>
                    <input type="text" name="tussenvoegsel" class="form-control" maxlength="15"
                           value="<?= htmlspecialchars($values['tussenvoegsel']) ?>">
                </div>
                <div class="col-md-4">
                    abel class="form-label">Achternaam *</label>
                    <input type="text" name="achternaam" class="form-control" required minlength="2" maxlength="30"
                           value="<?= htmlspecialchars($values['achternaam']) ?>">
                </div>

                <div class="col-md-6">
                    abel class="form-label">Gebruikersnaam *</label>
                    <input type="text" name="gebruikersnaam" class="form-control" required minlength="4" maxlength="20"
                           value="<?= htmlspecialchars($values['gebruikersnaam']) ?>">
                    <div class="form-text">Alleen letters, cijfers en _</div>
                </div>
                <div class="col-md-6">
                    abel class="form-label">Wachtwoord *</label>
                    <input type="password" name="wachtwoord" class="form-control" required minlength="6" maxlength="50">
                </div>

                <div class="col-md-12">
                    abel class="form-label">Adres *</label>
                    <input type="text" name="adres" class="form-control" required maxlength="255"
                           value="<?= htmlspecialchars($values['adres']) ?>">
                </div>

                <div class="col-md-6">
                    abel class="form-label">E-mailadres *</label>
                    <input type="email" name="email" class="form-control" required maxlength="255"
                           value="<?= htmlspecialchars($values['email']) ?>">
                </div>

                <div class="col-md-6">
                    abel class="form-label">Telefoonnummer *</label>
                    <input type="text" name="telefoon" class="form-control" required maxlength="20"
                           value="<?= htmlspecialchars($values['telefoon']) ?>">
                </div>

                <div class="col-md-6">
                    abel class="form-label">Geboortedatum *</label>
                    <input type="date" name="geboortedatum" class="form-control" required
                           value="<?= htmlspecialchars($values['geboortedatum']) ?>">
                </div>

                <div class="col-md-6">
                    abel class="form-label">Datum ingevoerd *</label>
                    <input type="date" name="registratiedatum" class="form-control" required
                           value="<?= htmlspecialchars($values['registratiedatum']) ?>">
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="actief" id="actief" <?= $values['actief'] ? 'checked' : '' ?>>
                        abel class="form-check-label" for="actief">Actief</label>
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