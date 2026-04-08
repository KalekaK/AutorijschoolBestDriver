
<?php
/*
Naam: Krishna Sardarsing
Versie: 1.2
Datum: 08-04-2026
Beschrijving: Admin overzicht van alle lessen + les inplannen.
*/

$pageTitle = 'Lesoverzicht';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Gebruiker.php';
require_once __DIR__ . '/../../classes/Les.php';
require_once __DIR__ . '/../../classes/Lespakket.php';
require_once __DIR__ . '/../../classes/Auto.php';
require_once __DIR__ . '/../../includes/auth.php';

Auth::requireRol(1);

$lesModel = new Les();
$gebruikerModel = new Gebruiker();
$lespakketModel = new Lespakket();
$autoModel = new Auto();
$pdo = Database::getInstance();

$melding = '';
$errors = [];

$lespakketTypeId = (int)($_GET['lespakket_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['actie'] ?? '') === 'toevoegen') {
	$lestijd = trim($_POST['lestijd'] ?? '');
	$ophaallocatieId = (int)($_POST['ophaallocatie_id'] ?? 0);
	$instructeurId = (int)($_POST['instructeur_id'] ?? 0);
	$klantId = (int)($_POST['klant_id'] ?? 0);
	$lespakketId = (int)($_POST['lespakket_id'] ?? 0);
	$autoId = (int)($_POST['auto_id'] ?? 0);
	$doel = trim($_POST['doel'] ?? '');

	if ($lestijd === '' || $ophaallocatieId < 1 || $instructeurId < 1 || $klantId < 1 || $lespakketId < 1 || $autoId < 1) {
		$errors[] = 'Vul alle verplichte velden in.';
	}

	if (!$errors) {
		$ok = $lesModel->toevoegen([
			'lestijd' => $lestijd,
			'ophaallocatie_id' => $ophaallocatieId,
			'instructeur_id' => $instructeurId,
			'klant_id' => $klantId,
			'lespakket_id' => $lespakketId,
			'auto_id' => $autoId,
			'doel' => $doel,
		]);
		if ($ok) {
			header('Location: les-overzicht.php?melding=opgeslagen');
			exit;
		}
		$errors[] = 'Les opslaan is mislukt.';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['actie'] ?? '') === 'annuleren') {
	$lesId = (int)($_POST['les_id'] ?? 0);
	$reden = trim($_POST['reden'] ?? '');
	if ($lesId > 0 && $reden !== '') {
		$lesModel->annuleren($lesId, $reden);
		header('Location: les-overzicht.php?melding=opgeslagen');
		exit;
	}
}

if (($_GET['melding'] ?? '') === 'opgeslagen') {
	$melding = 'opgeslagen';
}

$geselecteerdLespakket = false;
$lessen = [];
$lespakkettenOverzicht = [];

if ($lespakketTypeId > 0) {
	$geselecteerdLespakket = $lespakketModel->getById($lespakketTypeId);
	if ($geselecteerdLespakket) {
		$lessen = $lesModel->getByLespakketType($lespakketTypeId);
	}
} else {
	$lespakkettenOverzicht = $lespakketModel->getAllMetInschrijvingen('');
}

$instructeurs = $gebruikerModel->getAlleInstructeurs();
$klanten = $gebruikerModel->getAlleKlanten('');
$lespakketten = $lespakketModel->getAll('');
$autos = $autoModel->getAll('');

$ophaallocaties = $pdo->query("SELECT * FROM ophaallocatie ORDER BY Plaats, Adres")->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>
<div class="container-fluid">
<div class="row">
<nav class="col-auto sidebar pt-3">
	<ul class="nav flex-column gap-1">
		<li><a href="<?= BASE_URL ?>/pages/dashboard.php" class="nav-link">Dashboard</a></li>
		<li><a href="<?= BASE_URL ?>/pages/admin/les-overzicht.php" class="nav-link active">Lesoverzicht</a></li>
		<li><a href="<?= BASE_URL ?>/pages/admin/lespakket.php" class="nav-link">Lespakket</a></li>
		<li><a href="<?= BASE_URL ?>/pages/admin/klant-overzicht.php" class="nav-link">Klanten</a></li>
		<li><a href="<?= BASE_URL ?>/pages/admin/instructeur-overzicht.php" class="nav-link">Instructeurs</a></li>
		<li><a href="<?= BASE_URL ?>/pages/admin/wagenpark.php" class="nav-link">Wagenpark</a></li>
		<li><a href="<?= BASE_URL ?>/pages/admin/ziekmelding.php" class="nav-link">Ziekmeldingen</a></li>
	</ul>
</nav>

<main class="col main-content">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<div>
			<h5 class="mb-0">Lesoverzicht</h5>
			<?php if ($geselecteerdLespakket): ?>
				<div class="text-muted small">
					<?= htmlspecialchars($geselecteerdLespakket['Naam'] ?? '') ?>
				</div>
			<?php endif; ?>
		</div>
		<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#lesModal">
			Les inplannen
		</button>
	</div>

	<?php if ($lespakketTypeId > 0): ?>
		<div class="mb-3">
			<a href="<?= BASE_URL ?>/pages/admin/les-overzicht.php" class="btn btn-outline-secondary btn-sm">
				Terug naar overzicht
			</a>
		</div>
	<?php endif; ?>

	<?php if ($melding === 'opgeslagen'): ?>
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

	<div class="bg-white rounded border">
		<?php if ($lespakketTypeId < 1): ?>
			<table class="table table-hover table-bestdriver mb-0">
				<thead>
					<tr>
						<th>Les</th>
						<th>Omschrijving</th>
						<th>Ingeschreven klanten</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php if (empty($lespakkettenOverzicht)): ?>
					<tr><td colspan="4" class="text-center text-muted py-4">Geen lespakketten gevonden.</td></tr>
				<?php else: ?>
					<?php foreach ($lespakkettenOverzicht as $p): ?>
					<tr>
						<td class="fw-semibold"><?= htmlspecialchars($p['Naam'] ?? '') ?></td>
						<td class="text-muted"><?= htmlspecialchars($p['Omschrijving'] ?? '') ?></td>
						<td><?= (int)($p['aantal_ingeschreven'] ?? 0) ?></td>
						<td class="text-end">
							<a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/pages/admin/les-overzicht.php?lespakket_id=<?= (int)$p['Lespakket_id'] ?>">
								Bekijken
							</a>
						</td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
			<div class="p-2 text-muted small border-top"><?= count($lespakkettenOverzicht) ?> resultaten</div>
		<?php else: ?>
			<?php if (!$geselecteerdLespakket): ?>
				<div class="p-3">
					<div class="alert alert-warning mb-0">Lespakket niet gevonden.</div>
				</div>
			<?php else: ?>
				<table class="table table-hover table-bestdriver mb-0">
					<thead>
						<tr>
							<th>ID</th>
							<th>Datum</th>
							<th>Tijd</th>
							<th>Instructeur</th>
							<th>Klant</th>
							<th>Ophaal plek</th>
							<th>Auto</th>
							<th>Doel</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php if (empty($lessen)): ?>
						<tr><td colspan="9" class="text-center text-muted py-4">Geen lessen gevonden.</td></tr>
					<?php else: ?>
						<?php foreach ($lessen as $l): ?>
						<tr>
							<td><?= (int)$l['Les_id'] ?></td>
							<td><?= date('d-m-Y', strtotime($l['Lestijd'])) ?></td>
							<td><?= date('H:i', strtotime($l['Lestijd'])) ?></td>
							<td><?= htmlspecialchars($l['instructeur_naam'] ?? '') ?></td>
							<td><?= htmlspecialchars($l['klant_naam'] ?? '') ?></td>
							<td><?= htmlspecialchars($l['ophaallocatie'] ?? '') ?></td>
							<td><?= htmlspecialchars((string)($l['auto'] ?? '')) ?></td>
							<td><?= htmlspecialchars((string)($l['Doel'] ?? '')) ?></td>
							<td class="text-end">
								<?php if ((int)$l['Geannuleerd'] !== 1): ?>
									<button
										class="btn btn-sm btn-outline-danger"
										data-bs-toggle="modal"
										data-bs-target="#annuleerModal"
										data-annuleer-les-id="<?= (int)$l['Les_id'] ?>"
									>
										Annuleren
									</button>
								<?php else: ?>
									<span class="text-muted small">Geannuleerd</span>
								<?php endif; ?>
							</td>
						</tr>

						<?php endforeach; ?>
					<?php endif; ?>
					</tbody>
				</table>
				<div class="p-2 text-muted small border-top"><?= count($lessen) ?> resultaten</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</main>
</div>
</div>

<div class="modal fade" id="annuleerModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<form method="POST" class="modal-content" id="annuleerForm">
			<input type="hidden" name="actie" value="annuleren">
			<input type="hidden" name="les_id" id="annuleer_les_id" value="">
			<div class="modal-header">
				<h5 class="modal-title">Les annuleren</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
			</div>
			<div class="modal-body">
				<label class="form-label">Reden *</label>
				<textarea name="reden" id="annuleer_reden" class="form-control" rows="3" required></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="submit" class="btn btn-danger">Bevestigen</button>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="lesModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<form method="POST" class="modal-content" id="lesForm">
			<input type="hidden" name="actie" value="toevoegen">
			<div class="modal-header">
				<h5 class="modal-title">Les inplannen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label">Datum en tijd *</label>
						<input type="datetime-local" name="lestijd" class="form-control" required>
					</div>
					<div class="col-md-6">
						<label class="form-label">Ophaallocatie *</label>
						<select name="ophaallocatie_id" class="form-select" required>
							<option value="">Kies...</option>
							<?php foreach ($ophaallocaties as $o): ?>
								<option value="<?= (int)$o['Ophaallocatie_id'] ?>">
									<?= htmlspecialchars($o['Plaats'] . ' - ' . $o['Adres']) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="col-md-6">
						<label class="form-label">Instructeur *</label>
						<select name="instructeur_id" class="form-select" required>
							<option value="">Kies...</option>
							<?php foreach ($instructeurs as $i): ?>
								<option value="<?= (int)$i['Gebruiker_id'] ?>">
									<?= htmlspecialchars(trim($i['Voornaam'] . ' ' . $i['Achternaam'])) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label">Klant *</label>
						<select name="klant_id" class="form-select" required>
							<option value="">Kies...</option>
							<?php foreach ($klanten as $k): ?>
								<option value="<?= (int)$k['Gebruiker_id'] ?>">
									<?= htmlspecialchars(trim($k['Voornaam'] . ' ' . $k['Achternaam'])) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="col-md-6">
						<label class="form-label">Lespakket *</label>
						<select name="lespakket_id" class="form-select" required>
							<option value="">Kies...</option>
							<?php foreach ($lespakketten as $p): ?>
								<option value="<?= (int)$p['Lespakket_id'] ?>">
									<?= htmlspecialchars($p['Naam']) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label">Auto *</label>
						<select name="auto_id" class="form-select" required>
							<option value="">Kies...</option>
							<?php foreach ($autos as $a): ?>
								<option value="<?= (int)$a['Auto_id'] ?>">
									<?= htmlspecialchars($a['Kenteken'] . ' - ' . $a['Merk'] . ' ' . $a['Model']) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="col-12">
						<label class="form-label">Doel</label>
						<textarea name="doel" class="form-control" rows="2"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>

<script>
(function(){
	var form = document.getElementById('lesForm');
	if(!form) return;

	form.addEventListener('submit', function(e){
		var klant = form.querySelector('select[name="klant_id"]').value;
		var instructeur = form.querySelector('select[name="instructeur_id"]').value;
		var auto = form.querySelector('select[name="auto_id"]').value;
		var lespakket = form.querySelector('select[name="lespakket_id"]').value;
		var ophaal = form.querySelector('select[name="ophaallocatie_id"]').value;
		var lestijd = form.querySelector('input[name="lestijd"]').value;

		if(lestijd === '' || klant === '' || instructeur === '' || auto === '' || lespakket === '' || ophaal === ''){
			alert('Vul alle verplichte velden in.');
			e.preventDefault();
		}
	});
})();

(function(){
	var modal = document.getElementById('annuleerModal');
	var lesIdInput = document.getElementById('annuleer_les_id');
	var redenInput = document.getElementById('annuleer_reden');
	if(!modal || !lesIdInput) return;

	document.addEventListener('click', function(e){
		var btn = e.target.closest('[data-annuleer-les-id]');
		if(!btn) return;
		lesIdInput.value = btn.getAttribute('data-annuleer-les-id') || '';
		if(redenInput) {
			redenInput.value = '';
			setTimeout(function(){ redenInput.focus(); }, 150);
		}
	});
})();
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

