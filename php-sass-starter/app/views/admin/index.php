<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title'] ?? 'Admin-Panel') ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
      .table { width:100%; border-collapse:collapse; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 10px rgba(44,62,80,.08); }
      .table th, .table td { border:1px solid rgba(44,62,80,.12); padding:.7rem .9rem; text-align:left; }
      .badge { display:inline-block; padding:.15rem .45rem; border-radius:6px; font-weight:700; font-size:.85rem; }
      .badge.admin { background:#1e293b; color:#fff; }
      .badge.calisan { background:#0ea5e9; color:#fff; }
      .badge.musteri { background:#22c55e; color:#fff; }
      .hint { margin:.6rem 0 1rem; opacity:.8; }
      .panel { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 1px 6px rgba(44,62,80,.08); margin-bottom:1rem; }
      code { background: rgba(44, 62, 80, 0.08); padding: 0.1rem 0.35rem; border-radius: 6px; }
      pre { background:#0f172a; color:#e2e8f0; padding:1rem; border-radius:10px; overflow:auto; }
    </style>
  </head>
  <body>
    <?php include __DIR__ . '/../nav-bar.php'; ?>
    <div class="main-layout">
      <section class="main-content">
        <h1>Admin-Panel</h1>
        <?php if (!empty($_SESSION['admin_user_create_error'])): ?>
          <p style="color:#c0392b; font-weight:700;"><?= htmlspecialchars($_SESSION['admin_user_create_error']) ?></p>
          <?php unset($_SESSION['admin_user_create_error']); ?>
        <?php endif; ?>

        <div class="panel">
          <h2>Benutzer hinzufügen</h2>
          <form method="post" action="/index.php?action=admin_user_create" class="register-form" style="max-width:520px;">
            <div class="form-group">
              <label for="adi">Vorname</label>
              <input type="text" id="adi" name="adi" required>
            </div>
            <div class="form-group">
              <label for="soyadi">Nachname</label>
              <input type="text" id="soyadi" name="soyadi" required>
            </div>
            <div class="form-group">
              <label for="mail">E-Mail</label>
              <input type="email" id="mail" name="mail" required>
            </div>
            <div class="form-group">
              <label for="sifre">Passwort</label>
              <input type="password" id="sifre" name="sifre" required>
            </div>
            <div class="form-group">
              <label for="role">Rolle</label>
              <select id="role" name="role" required>
                <option value="calisan">Mitarbeiter</option>
                <option value="musteri">Kunde</option>
                <option value="admin">Administrator</option>
              </select>
            </div>
            <button class="btn-primary" type="submit">Hinzufügen</button>
          </form>
        </div>
        <div class="panel">
          <div class="hint">
            Falls die Spalte 'role' fehlt, fügen Sie sie mit folgendem Befehl hinzu und laden Sie die Seite neu.
          </div>
          <pre><code>ALTER TABLE users ADD COLUMN role ENUM('admin','calisan','musteri') NOT NULL DEFAULT 'calisan' AFTER sifre;</code></pre>
          <div class="hint">Beispieltabelle für Projekte und Zuweisungen:</div>
          <pre><code>CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ad VARCHAR(200) NOT NULL,
  aciklama TEXT NULL,
  musteri_id INT NULL,
  durum ENUM('yeni','devam','tamam','iptal') NOT NULL DEFAULT 'yeni',
  olusturma_tarihi DATETIME NOT NULL,
  guncelleme_tarihi DATETIME NOT NULL,
  CONSTRAINT fk_projects_musteri FOREIGN KEY (musteri_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE project_assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  calisan_id INT NOT NULL,
  rol_projede VARCHAR(50) NULL,
  atama_tarihi DATETIME NOT NULL DEFAULT NOW(),
  CONSTRAINT fk_pa_project FOREIGN KEY (project_id) REFERENCES projects(id),
  CONSTRAINT fk_pa_user FOREIGN KEY (calisan_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</code></pre>
        </div>

        <p>
          <a class="btn-primary" style="text-decoration:none; display:inline-block;" href="/index.php?action=admin_projects">Projekte (Verwaltung)</a>
        </p>

        <h2>Benutzer</h2>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>E-Mail</th>
              <th>Rolle</th>
              <th>Letzter Login</th>
              <th>Aktion</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($data['users'])): ?>
              <?php foreach ($data['users'] as $u): ?>
                <?php $role = $u['role'] ?? 'calisan'; ?>
                <tr>
                  <td><?= (int)$u['id'] ?></td>
                  <td><?= htmlspecialchars(($u['adi'] ?? '') . ' ' . ($u['soyadi'] ?? '')) ?></td>
                  <td><?= htmlspecialchars($u['mail'] ?? '') ?></td>
                  <td>
                    <span class="badge <?= htmlspecialchars($role) ?>"><?= htmlspecialchars($role) ?></span>
                  </td>
                  <td><?= htmlspecialchars($u['son_giris_tarihi'] ?? '-') ?></td>
                  <td>
                    <form method="post" action="/index.php?action=admin_update_role" style="display:flex; gap:.4rem; align-items:center;">
                      <input type="hidden" name="id" value="<?= (int)$u['id'] ?>" />
                        <select name="role">
                        <option value="admin" <?= $role==='admin'?'selected':'' ?>>Administrator</option>
                        <option value="calisan" <?= $role==='calisan'?'selected':'' ?>>Mitarbeiter</option>
                        <option value="musteri" <?= $role==='musteri'?'selected':'' ?>>Kunde</option>
                        </select>
                      <button type="submit">Aktualisieren</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6">Kein Benutzer gefunden.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </div>
  </body>
</html>
