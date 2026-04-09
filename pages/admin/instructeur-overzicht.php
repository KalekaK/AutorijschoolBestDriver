<?php
/*
Naam: Dominik Bulla
Versie: 1.0
Datum: 08-04-2026 / 09-04-2026
Beschrijving: Admin instructeurs overzicht met zoeken en instructeur blokkeren/activeren.
*/

$pageTitle = 'Instructeurs';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Gebruiker.php';
require_once __DIR__ . '/../../includes/auth.php';
// Alleen toegankelijk voor admins (rol 1)
Auth::requireRol(1);

// Model aanmaken en data ophalen
$model = new Gebruiker();

// Verwerk toggle actief indien formulier verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['actie'] ?? '') === 'toggle') {
    $id = (int)($_POST['id'] ?? 0);
    $actief = (int)($_POST['actief'] ?? 0);
    if ($id > 0) {
        $g = $model->getById($id);
        if ($g && (int)($g['Rol'] ?? 0) === 2) {
            $model->setActief($id, $actief);
        }
    }
    header('Location: instructeur-overzicht.php?melding=opgeslagen');
    exit;
}

// Zoekterm verwerken en instructeurs ophalen
$zoek = trim($_GET['zoek'] ?? '');
$instructeurs = $model->getAlleInstructeursAdmin($zoek);
include __DIR__ . '/../../includes/header.php';
?>

<!-- Sidebar -->
<div class="container-fluid"><div class="row">
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
<main class="col main-content">
  <?php if (($_GET['melding'] ?? '') === 'opgeslagen'): ?>
    <div class="alert alert-success">Opgeslagen.</div>
  <?php endif; ?>

  <!-- Instructeurs toevoegen button -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Instructeurs</h5>
    <a href="instructeur-toevoegen.php" class="btn btn-primary btn-sm">Instructeur toevoegen</a>
  </div>

  <!-- Zoekformulier -->
  <form class="mb-3" method="GET">
    <div class="input-group" style="max-width:300px">
      <input type="text" name="zoek" class="form-control" placeholder="Naam zoeken..." value="<?= htmlspecialchars($zoek) ?>">
    </div>
  </form>

  <!-- Instructeurs tabel -->
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
          <th>Geboortedatum</th>
          <th>Datum ingevoerd</th>
          <th>Actief</th>
          <th></th>
        </tr>
      </thead>
      <tbody>

      <!-- Instructeurs weergeven -->
      <?php if (empty($instructeurs)): ?>
        <tr><td colspan="10" class="text-center text-muted py-4">Geen instructeurs gevonden.</td></tr>
      <?php else: foreach ($instructeurs as $i): ?>
        <!-- Alleen instructeurs tonen (rol 2) -->
        <?php if ((int)($i['Rol'] ?? 0) !== 2) continue; ?>
        <tr>
            <!-- Lijst met alle gegevens van de instructeur. -->
          <td><?= (int)$i['Gebruiker_id'] ?></td>
          <td><?= htmlspecialchars(trim(($i['Voornaam'] ?? '').' '.($i['Tussenvoegsel'] ?? '').' '.($i['Achternaam'] ?? ''))) ?></td>
          <td><?= htmlspecialchars((string)($i['Gebruikersnaam'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string)($i['Adres'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string)($i['Email'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string)($i['Telefoon'] ?? '')) ?></td>
          <td>
            <?php
              $geb = (string)($i['Geboortedatum'] ?? '');
              echo $geb !== '' ? htmlspecialchars(date('d-m-Y', strtotime($geb))) : '';
            ?>
          </td>
          <td>
            <?php
              $reg = (string)($i['RegistratieDatum'] ?? '');
              echo $reg !== '' ? htmlspecialchars(date('d-m-Y', strtotime($reg))) : '';
            ?>
          </td>
          <!-- Actief status met badge -->
          <td><?= (int)($i['Actief'] ?? 0) === 1 ? '<span class="badge bg-success">Ja</span>' : '<span class="badge bg-secondary">Nee</span>' ?></td>
          <td class="text-end">
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">•••</button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="instructeur-bewerken.php?id=<?= (int)$i['Gebruiker_id'] ?>">Bewerken</a></li>
                <li>
                  <form method="POST" class="m-0">
                    <input type="hidden" name="actie" value="toggle">
                    <input type="hidden" name="id" value="<?= (int)$i['Gebruiker_id'] ?>">
                    <?php if ((int)($i['Actief'] ?? 0) === 1): ?>
                      <input type="hidden" name="actief" value="0">
                      <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Instructeur blokkeren?')">Blokkeren</button>
                    <?php else: ?>
                      <input type="hidden" name="actief" value="1">
                      <button type="submit" class="dropdown-item" onclick="return confirm('Instructeur activeren?')">Activeren</button>
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
    <div class="p-2 text-muted small border-top"><?= count($instructeurs) ?> resultaten</div>
  </div>
</main>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
