
<?php
/*
Naam: Ryan Sitaldien
Versie: 1.0
Datum: 08-04-2026
Beschrijving: Admin wagenpark overzicht met auto toevoegen/bewerken.
*/

$pageTitle = 'Wagenpark';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auto.php';
require_once __DIR__ . '/../../includes/auth.php';

Auth::requireRol(1);

$model = new Auto();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$actie = $_POST['actie'] ?? '';

	if ($actie === 'toevoegen') {
		$ok = $model->toevoegen($_POST);
		if ($ok) {
			header('Location: wagenpark.php?melding=opgeslagen');
			exit;
		}
		$errors[] = 'Auto toevoegen is mislukt. Controleer de velden.';
	}

	if ($actie === 'bewerken') {
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			$ok = $model->bijwerken($id, $_POST);
			if ($ok) {
				header('Location: wagenpark.php?melding=opgeslagen');
				exit;
			}
		}
		$errors[] = 'Auto bewerken is mislukt. Controleer de velden.';
	}

	if ($actie === 'verwijderen') {
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			$model->verwijderen($id);
		}
		header('Location: wagenpark.php?melding=opgeslagen');
		exit;
	}
}

$zoek = trim($_GET['zoek'] ?? '');
$autos = $model->getAll($zoek);
$soorten = $model->getSoorten();
$melding = ($_GET['melding'] ?? '') === 'opgeslagen' ? 'opgeslagen' : '';

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
		<li><a href="<?= BASE_URL ?>/pages/admin/wagenpark.php" class="nav-link active">Wagenpark</a></li>
		<li><a href="<?= BASE_URL ?>/pages/admin/ziekmelding.php" class="nav-link">Ziekmeldingen</a></li>
	</ul>
</nav>

<main class="col main-content">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h5 class="mb-0">Wagenpark</h5>
		<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#autoModal">
			Auto toevoegen
		</button>
	</div>

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

	<form class="mb-3" method="GET">
		<div class="input-group" style="max-width:300px">
			<input type="text" name="zoek" class="form-control" placeholder="Zoeken..." value="<?= htmlspecialchars($zoek) ?>">
		</div>
	</form>

	<div class="bg-white rounded border">
		<table class="table table-hover table-bestdriver mb-0">
			<thead>
				<tr>
					<th>ID</th>
					<th>Kenteken</th>
					<th>Merk</th>
					<th>Model</th>
					<th>Soort</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if (empty($autos)): ?>
				<tr><td colspan="6" class="text-center text-muted py-4">Geen auto’s gevonden.</td></tr>
			<?php else: foreach ($autos as $a): ?>
				<tr>
					<td><?= (int)$a['Auto_id'] ?></td>
					<td><?= htmlspecialchars($a['Kenteken']) ?></td>
					<td><?= htmlspecialchars($a['Merk']) ?></td>
					<td><?= htmlspecialchars($a['Model']) ?></td>
					<td><?= htmlspecialchars($a['soort'] ?? '') ?></td>
					<td class="text-end">
						<button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bewerkenModal<?= (int)$a['Auto_id'] ?>">
							Bewerken
						</button>
						<form method="POST" class="d-inline">
							<input type="hidden" name="actie" value="verwijderen">
							<input type="hidden" name="id" value="<?= (int)$a['Auto_id'] ?>">
							<button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Auto verwijderen?')">Verwijderen</button>
						</form>
					</td>
				</tr>

				<div class="modal fade" id="bewerkenModal<?= (int)$a['Auto_id'] ?>" tabindex="-1">
					<div class="modal-dialog">
						<form method="POST" class="modal-content" id="autoForm<?= (int)$a['Auto_id'] ?>">
							<input type="hidden" name="actie" value="bewerken">
							<input type="hidden" name="id" value="<?= (int)$a['Auto_id'] ?>">
							<div class="modal-header">
								<h5 class="modal-title">Auto bewerken</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
							</div>
							<div class="modal-body">
								<div class="mb-3">
									<label class="form-label">Kenteken *</label>
									<input type="text" name="kenteken" class="form-control" required maxlength="20" value="<?= htmlspecialchars($a['Kenteken']) ?>">
								</div>
								<div class="mb-3">
									<label class="form-label">Merk *</label>
									<input type="text" name="merk" class="form-control" required maxlength="255" value="<?= htmlspecialchars($a['Merk']) ?>">
								</div>
								<div class="mb-3">
									<label class="form-label">Model *</label>
									<input type="text" name="model" class="form-control" required maxlength="255" value="<?= htmlspecialchars($a['Model']) ?>">
								</div>
								<div class="mb-3">
									<label class="form-label">Soort *</label>
									<select name="soort_id" class="form-select" required>
										<option value="">Kies...</option>
										<?php foreach ($soorten as $s): ?>
											<option value="<?= (int)$s['Soort_id'] ?>" <?= (int)$a['SoortSoort_id'] === (int)$s['Soort_id'] ? 'selected' : '' ?>>
												<?= htmlspecialchars($s['Type']) ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
								<button type="submit" class="btn btn-primary">Opslaan</button>
							</div>
						</form>
					</div>
				</div>

			<?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="p-2 text-muted small border-top"><?= count($autos) ?> resultaten</div>
	</div>
</main>
</div>
</div>

<div class="modal fade" id="autoModal" tabindex="-1">
	<div class="modal-dialog">
		<form method="POST" class="modal-content" id="autoForm">
			<input type="hidden" name="actie" value="toevoegen">
			<div class="modal-header">
				<h5 class="modal-title">Auto toevoegen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label class="form-label">Kenteken *</label>
					<input type="text" name="kenteken" class="form-control" required maxlength="20">
				</div>
				<div class="mb-3">
					<label class="form-label">Merk *</label>
					<input type="text" name="merk" class="form-control" required maxlength="255">
				</div>
				<div class="mb-3">
					<label class="form-label">Model *</label>
					<input type="text" name="model" class="form-control" required maxlength="255">
				</div>
				<div class="mb-3">
					<label class="form-label">Soort *</label>
					<select name="soort_id" class="form-select" required>
						<option value="">Kies...</option>
						<?php foreach ($soorten as $s): ?>
							<option value="<?= (int)$s['Soort_id'] ?>"><?= htmlspecialchars($s['Type']) ?></option>
						<?php endforeach; ?>
					</select>
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
	var form = document.getElementById('autoForm');
	if(!form) return;

	form.addEventListener('submit', function(e){
		var kenteken = form.querySelector('input[name="kenteken"]').value.trim();
		if(kenteken.length < 2){
			alert('Vul een kenteken in.');
			e.preventDefault();
		}
	});
})();
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

