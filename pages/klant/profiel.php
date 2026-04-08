
<?php
/*
Naam: Krishna Sardarsing
Versie: 1.2
Datum: 08-04-2026
Beschrijving: Klant profiel bekijken en wachtwoord aanpassen.
*/

$pageTitle = 'Mijn profiel';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Gebruiker.php';
require_once __DIR__ . '/../../includes/auth.php';

Auth::requireRol(3);

$model = new Gebruiker();
$gebruiker = $model->getById(Auth::getGebruikerId());

$errors = [];
$melding = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$huidig = $_POST['huidig_wachtwoord'] ?? '';
	$nieuw = $_POST['nieuw_wachtwoord'] ?? '';
	$nieuw2 = $_POST['nieuw_wachtwoord2'] ?? '';

	if ($huidig === '' || $nieuw === '' || $nieuw2 === '') {
		$errors[] = 'Vul alle velden in.';
	}

	if ($nieuw !== '' && (strlen($nieuw) < 6 || strlen($nieuw) > 50)) {
		$errors[] = 'Nieuw wachtwoord moet 6 t/m 50 tekens zijn.';
	}

	if ($nieuw !== $nieuw2) {
		$errors[] = 'Nieuwe wachtwoorden komen niet overeen.';
	}

	if (!$errors) {
		if (!$gebruiker || !password_verify($huidig, $gebruiker['Wachtwoord'])) {
			$errors[] = 'Huidig wachtwoord is niet correct.';
		}
	}

	if (!$errors) {
		$ok = $model->wachtwoordBijwerken(Auth::getGebruikerId(), $nieuw);
		if ($ok) {
			$melding = 'opgeslagen';
		} else {
			$errors[] = 'Wachtwoord aanpassen is mislukt.';
		}
	}
}

include __DIR__ . '/../../includes/header.php';
?>
<div class="container main-content">
	<h5 class="mb-4">Mijn profiel</h5>

	<?php if ($melding === 'opgeslagen'): ?>
		<div class="alert alert-success">Wachtwoord aangepast.</div>
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

	<div class="row g-4">
		<div class="col-md-6">
			<div class="bg-white rounded border p-4">
				<h6 class="fw-semibold mb-3">Persoonsgegevens</h6>
				<?php if (!$gebruiker): ?>
					<div class="text-muted">Geen gegevens gevonden.</div>
				<?php else: ?>
					<div class="mb-2"><strong>Naam:</strong> <?= htmlspecialchars(trim($gebruiker['Voornaam'] . ' ' . $gebruiker['Tussenvoegsel'] . ' ' . $gebruiker['Achternaam'])) ?></div>
					<div class="mb-2"><strong>Gebruikersnaam:</strong> <?= htmlspecialchars($gebruiker['Gebruikersnaam']) ?></div>
					<div class="mb-2"><strong>Actief:</strong> <?= (int)$gebruiker['Actief'] === 1 ? 'Ja' : 'Nee' ?></div>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-md-6">
			<div class="bg-white rounded border p-4">
				<h6 class="fw-semibold mb-3">Wachtwoord aanpassen</h6>
				<form method="POST" id="wachtwoordForm">
					<div class="mb-3">
						<label class="form-label">Huidig wachtwoord *</label>
						<input type="password" name="huidig_wachtwoord" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Nieuw wachtwoord *</label>
						<input type="password" name="nieuw_wachtwoord" class="form-control" required minlength="6" maxlength="50">
					</div>
					<div class="mb-3">
						<label class="form-label">Herhaal nieuw wachtwoord *</label>
						<input type="password" name="nieuw_wachtwoord2" class="form-control" required minlength="6" maxlength="50">
					</div>

					<div class="d-flex gap-2">
						<button type="submit" class="btn btn-primary">Opslaan</button>
						<button type="reset" class="btn btn-outline-secondary">Reset</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
(function(){
	var form = document.getElementById('wachtwoordForm');
	if(!form) return;

	form.addEventListener('submit', function(e){
		var nieuw = form.querySelector('input[name="nieuw_wachtwoord"]').value;
		var nieuw2 = form.querySelector('input[name="nieuw_wachtwoord2"]').value;
		if(nieuw !== nieuw2){
			alert('Nieuwe wachtwoorden komen niet overeen.');
			e.preventDefault();
		}
	});
})();
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

