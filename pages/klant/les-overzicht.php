
<?php
/*
Naam: Krishna Sardarsing
Versie: 1.3
Datum: 09-04-2026
Beschrijving: Klant overzicht van eigen lessen.
*/

$pageTitle = 'Mijn lessen';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Les.php';
require_once __DIR__ . '/../../includes/auth.php';

Auth::requireRol(3);

// lessen ophalen van de ingelogde klant
$model = new Les();
$lessen = $model->getByKlant(Auth::getGebruikerId());

include __DIR__ . '/../../includes/header.php';
?>
<div class="container main-content">
	<h5 class="mb-4">Mijn lessen</h5>

	<div class="bg-white rounded border">
		<table class="table table-hover table-bestdriver mb-0">
			<thead>
				<tr>
					<th>Datum</th>
					<th>Tijd</th>
					<th>Instructeur</th>
					<th>Ophaaladres</th>
					<th>Lespakket</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>

<!-- als er geen lessen zijn, tonen we een melding. anders tonen we de lessen in een tabel -->
			<?php if (empty($lessen)): ?>
				<tr><td colspan="6" class="text-center text-muted py-4">Geen lessen gevonden.</td></tr>
			<?php else: foreach ($lessen as $l): ?>
				<tr>
					<td><?= date('d-m-Y', strtotime($l['Lestijd'])) ?></td>
					<td><?= date('H:i', strtotime($l['Lestijd'])) ?></td>
					<td><?= htmlspecialchars($l['instructeur_naam'] ?? '') ?></td>
					<td><?= htmlspecialchars($l['ophaallocatie'] ?? '') ?></td>
					<td><?= htmlspecialchars($l['lespakket_naam'] ?? '') ?></td>
					<td>
						<?php if ((int)$l['Geannuleerd'] === 1): ?>
							<span class="badge bg-secondary">Geannuleerd</span>
						<?php else: ?>
							<span class="badge bg-success">Gepland</span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="p-2 text-muted small border-top"><?= count($lessen) ?> resultaten</div>
	</div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
