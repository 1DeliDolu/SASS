<!DOCTYPE html>
<html lang="de"></html>
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($data['title'] ?? 'Projekte (Verwaltung)') ?></title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/../../nav-bar.php'; ?>
  <div class="main-layout">
    <section class="main-content">
      <h1>Projekte (Verwaltung)</h1>
      <p>
        <a class="btn-primary" style="text-decoration:none; display:inline-block;" href="/index.php?action=admin_project_create">+ Projekt hinzufügen</a>
      </p>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Kunde</th>
            <th>Status</th>
            <th>Aktion</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($data['projects'])): ?>
            <?php foreach ($data['projects'] as $p): ?>
                <tr><td>
                  <?= (int) $p['id'] ?>
                </td>
                <td>
                  <?= htmlspecialchars($p['ad'] ?? '') ?>
                </td>
                <td>
                  <?= htmlspecialchars($p['musteri_mail'] ?? '') ?>
                </td>
                <td>
                  <?php require_once __DIR__ . '/../../../lib/I18n.php'; $lang = I18n::lang(); $d=(string)($p['durum'] ?? ''); echo htmlspecialchars(I18n::statusLabel($d,$lang)); ?>
                </td>
                <td>
                  <a class="btn-action btn-edit" href="/index.php?action=admin_project_edit&amp;id=<?= (int) $p['id'] ?>">Edit</a>
                  <form method="post" action="/index.php?action=admin_project_delete" style="display:inline-block; margin-left:.1rem;" onsubmit="return confirm('sind sie sicher, dass sie lÃ¶schen mÃ¶chten?');">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button type="submit" class="btn-action btn-delete">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5">Kein Eintrag gefunden.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</body></html>

