<?php


/*
Naam: Adrian.
Versie: 1.0
Datum: 08-04-2026
Beschrijving: Algemene header met basis HTML, Bootstrap en navigatie.
*/

$titel = $pageTitle ?? 'Best Driver';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($titel) ?> – Best Driver</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?= BASE_URL ?>/pages/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<div class="container-fluid">
		<a class="navbar-brand fw-semibold" href="<?= BASE_URL ?>/pages/dashboard.php">
			Best Driver
		</a>

		<div class="ms-auto d-flex align-items-center">
			<?php if (class_exists('Auth') && Auth::isLoggedIn()): ?>
				<span class="text-white small me-3">
					<?= htmlspecialchars(Auth::getNaam()) ?>
				</span>
				<a class="btn btn-sm btn-outline-light" href="<?= BASE_URL ?>/pages/logout.php">
					Logout
				</a>
			<?php endif; ?>
		</div>
	</div>
</nav>
