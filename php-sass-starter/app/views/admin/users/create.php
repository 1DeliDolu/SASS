<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8">
    <title>Benutzer hinzufügen</title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
    <?php if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); } ?>
    <?php include __DIR__ . '/../../nav-bar.php'; ?>
    <div class="main-layout">
      <section class="main-content">
        <h1>Benutzer hinzufügen</h1>
        <?php if (!empty($_SESSION['admin_user_create_error'])): ?>
          <p style="color:#c0392b; font-weight:700;">
            <?= htmlspecialchars($_SESSION['admin_user_create_error']) ?>
          </p>
          <?php unset($_SESSION['admin_user_create_error']); ?>
        <?php endif; ?>
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
          <a class="nav-link" href="/index.php?action=admin">Zurück</a>
        </form>
      </section>
    </div>
  </body>
  </html>

