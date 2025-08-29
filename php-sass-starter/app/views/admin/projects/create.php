<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title'] ?? 'Projekt Hinzufügen') ?></title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
    <?php include __DIR__ . '/../../nav-bar.php'; ?>
    <div class="main-layout">
      <section class="main-content">
        <h1>Projekt Hinzufügen</h1>
        <?php if (!empty($data['error'])): ?>
          <p style="color:#c0392b; font-weight:700;"><?= htmlspecialchars($data['error']) ?></p>
        <?php endif; ?>
        <form method="post" action="/index.php?action=admin_project_create" class="register-form">
          <div class="form-group">
            <label for="ad">Name</label>
            <input type="text" id="ad" name="ad" required value="<?= isset($_POST['ad']) ? htmlspecialchars($_POST['ad']) : '' ?>">
          </div>
          <div class="form-group">
            <label for="aciklama">Beschreibung</label>
            <input type="text" id="aciklama" name="aciklama" value="<?= isset($_POST['aciklama']) ? htmlspecialchars($_POST['aciklama']) : '' ?>">
          </div>
          <div class="form-group">
            <label for="musteri_id">Kunde</label>
            <select id="musteri_id" name="musteri_id">
              <option value="">— wählen —</option>
              <?php foreach ($data['musteriler'] as $m): ?>
                <option value="<?= (int) $m['id'] ?>" <?= (isset($_POST['musteri_id']) && (int) $_POST['musteri_id'] === (int) $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars(($m['adi'] ?? '') . ' ' . ($m['soyadi'] ?? '') . ' — ' . ($m['mail'] ?? '')) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="durum">Status</label>
            <select
              id="durum" name="durum">
              <?php $dur = $_POST['durum'] ?? 'yeni'; ?>
              <option value="yeni" <?= $dur === 'yeni' ? 'selected' : '' ?>>neu</option>
              <option value="devam" <?= $dur === 'devam' ? 'selected' : '' ?>>in Bearbeitung</option>
              <option value="tamam" <?= $dur === 'tamam' ? 'selected' : '' ?>>abgeschlossen</option>
              <option value="iptal" <?= $dur === 'iptal' ? 'selected' : '' ?>>abgebrochen</option>
            </select>
          </div>
          <button class="btn-primary" type="submit">Speichern</button>
          <a class="nav-link" href="/index.php?action=admin_projects">Abbrechen</a>
        </form>
      </section>
    </div>
  </body>
</html>

