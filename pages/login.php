<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Gebruiker.php';
require_once __DIR__ . '/../includes/auth.php';

// Als je al ben ingelogd direct naar dashboard
if (Auth::isLoggedIn()) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$fout = '';
$gebruikersnaam = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $wachtwoord     = $_POST['wachtwoord'] ?? '';

    // Basiscontrole als alle velden zijn ingevuld
    if ($gebruikersnaam === '' || $wachtwoord === '') {
        $fout = 'Vul alle velden in.';
    }

    // Gebruikersnaam validatie
    if (!$fout && !preg_match('/^[a-zA-Z0-9_]+$/', $gebruikersnaam)) {
        $fout = 'Gebruikersnaam mag alleen letters, cijfers en _ bevatten.';
    }

    if (!$fout && (strlen($gebruikersnaam) < 4 || strlen($gebruikersnaam) > 20)) {
        $fout = 'Gebruikersnaam moet tussen 4 en 20 tekens zijn.';
    }

    // Wachtwoord validatie
    if (!$fout && (strlen($wachtwoord) < 6 || strlen($wachtwoord) > 50)) {
        $fout = 'Wachtwoord moet tussen 6 en 50 tekens zijn.';
    }

    // Inloggen met checks
    if (!$fout) {
        $gebruikerModel = new Gebruiker();
        $gebruiker      = $gebruikerModel->getByGebruikersnaam($gebruikersnaam);

        if ($gebruiker && password_verify($wachtwoord, $gebruiker['Wachtwoord'])) {
            Auth::login($gebruiker);
            header('Location: ' . BASE_URL . '/pages/dashboard.php');
            exit;
        } else {
            $fout = 'Onjuiste gebruikersnaam of wachtwoord.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Best Driver – Inloggen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/pages/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="login-logo">Best Driver</div>
            <p class="text-muted mt-1">Autorijschool – Inlogportaal</p>
        </div>

        <?php if ($fout): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($fout) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Gebruikersnaam</label>
                <input
                    type="text"
                    name="gebruikersnaam"
                    class="form-control"
                    placeholder="Gebruikersnaam"
                    required
                    autofocus
                    value="<?= htmlspecialchars($gebruikersnaam) ?>"
                >
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Wachtwoord</label>
                <input
                    type="password"
                    name="wachtwoord"
                    class="form-control"
                    placeholder="Wachtwoord"
                    required
                >
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-2 fw-semibold">
                    Inloggen
                </button>
                <button type="reset" class="btn btn-outline-secondary py-2 fw-semibold">
                    Reset
                </button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>