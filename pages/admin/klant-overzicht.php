<?php
/*
Naam: Krishna Sardarsing
Versie: 1.3
Datum: 08-04-2026
Beschrijving: Admin klantenoverzicht met zoeken en klant blokkeren/activeren.
*/

$pageTitle = 'Klanten';
require_once __DIR__.'/../../config/config.php';
require_once __DIR__.'/../../classes/Database.php';
require_once __DIR__.'/../../classes/Gebruiker.php';
require_once __DIR__.'/../../includes/auth.php';
Auth::requireRol(1);

$model = new Gebruiker();
// hier controleren we of een POST request is gedaan om een klant te blokkeren of activeren, en voeren we dat uit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['actie'] ?? '') === 'toggle') {
    $id = (int)($_POST['id'] ?? 0);
    $actief = (int)($_POST['actief'] ?? 0);
    if ($id > 0) {
        $model->setActief($id, $actief);
    }
    header('Location: klant-overzicht.php?melding=opgeslagen');
    exit;
}

// zoekterm ophalen en klantenlijst opvragen
$zoek    = trim($_GET['zoek']??'');
$klanten = $model->getAlleKlanten($zoek);
include __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid"><div class="row">
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

<!-- main content van de pagina, hierin komt het overzicht van klanten en de zoekfunctie -->
<main class="col main-content">
  <?php if (($_GET['melding'] ?? '') === 'opgeslagen'): ?>
    <div class="alert alert-success">Opgeslagen.</div>
  <?php endif; ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Klanten</h5>
    <a href="klant-toevoegen.php" class="btn btn-primary btn-sm">Klant toevoegen</a>
  </div>
  <form class="mb-3" method="GET">
    <div class="input-group" style="max-width:300px">
      <input type="text" name="zoek" class="form-control" placeholder="Naam zoeken..." value="<?= htmlspecialchars($zoek) ?>">
    </div>
  </form>

  <div class="bg-white rounded border">
    <table class="table table-hover table-bestdriver mb-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Naam</th>
          <th>Gebruikersnaam</th>
          <th>Adres</th>
          <th>E-mail</th>
          <th>Telefoon</th>
          <th>Registratie datum</th>
          <th>Actief</th>
          <th>Geslaagd</th>
          <th></th>
        </tr>
      </thead>
      <tbody>

      <!-- als er geen klanten zijn, tonen we een melding. anders tonen we de klanten in een tabel -->
      <?php if(empty($klanten)): ?>
        <tr><td colspan="10" class="text-center text-muted py-4">Geen klanten gevonden.</td></tr>
      <?php else: foreach($klanten as $k): ?>
        <tr>
          <td><?= $k['Gebruiker_id'] ?></td>
          <td><?= htmlspecialchars(trim($k['Voornaam'].' '.$k['Tussenvoegsel'].' '.$k['Achternaam'])) ?></td>
          <td><?= htmlspecialchars($k['Gebruikersnaam']) ?></td>
          <td><?= htmlspecialchars((string)($k['Adres'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string)($k['Email'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string)($k['Telefoon'] ?? '')) ?></td>
          <td>
            <?php
              $reg = (string)($k['RegistratieDatum'] ?? '');
              echo $reg !== '' ? htmlspecialchars(date('d-m-Y', strtotime($reg))) : '';
            ?>
          </td>
          
          <!-- we tonen een badge groen als de klant actief is, anders grijs. en een badge groen als de klant geslaagd is, anders geel -->
          <td><?= $k['Actief'] ? '<span class="badge bg-success">Ja</span>' : '<span class="badge bg-secondary">Nee</span>' ?></td>
          <td><?= $k['Geslaagd'] ? '<span class="badge bg-success">Ja</span>' : '<span class="badge bg-warning text-dark">Nee</span>' ?></td>
          <td class="text-end">
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">•••</button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="klant-bewerken.php?id=<?= $k['Gebruiker_id'] ?>">Bewerken</a></li>
                <li>
                  <form method="POST" class="m-0">
                    <input type="hidden" name="actie" value="toggle">
                    <input type="hidden" name="id" value="<?= $k['Gebruiker_id'] ?>">
                    <?php if ((int)$k['Actief'] === 1): ?>
                      <input type="hidden" name="actief" value="0">
                      <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Klant blokkeren?')">
                        Blokkeren
                      </button>
                    <?php else: ?>
                      <input type="hidden" name="actief" value="1">
                      <button type="submit" class="dropdown-item" onclick="return confirm('Klant activeren?')">
                        Activeren
                      </button>
                    <?php endif; ?>
                  </form>
                </li>
              </ul>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
    <div class="p-2 text-muted small border-top"><?= count($klanten) ?> resultaten</div>
  </div>
</main></div></div>
<?php include __DIR__.'/../../includes/footer.php'; ?>