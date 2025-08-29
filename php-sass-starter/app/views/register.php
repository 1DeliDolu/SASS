<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Registrieren</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="register-container">
            <h1>Registrieren</h1>
            <?php if (!empty($data['error'])): ?>
                <p
                    style="color:#c0392b; font-weight:700;"><?= htmlspecialchars($data['error']) ?>
                </p>
            <?php endif; ?>
            <?php if (!empty($data['message'])): ?>
                <p
                    style="color:green; font-weight:700;"><?= htmlspecialchars($data['message']) ?>
                </p>
            <?php endif; ?>
            <?php $next = isset($_GET['next']) ? $_GET['next'] : ($_SESSION['redirect_after_login'] ?? ''); ?>
            <form class="register-form" method="post" action="/index.php?action=register<?= !empty($next) ? '&amp;next=' . urlencode($next) : '' ?>">
                <div class="form-group">
                    <label for="adi">Vorname</label>
                    <input type="text" id="adi" name="adi" required value="<?= isset($_POST['adi']) ? htmlspecialchars($_POST['adi']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="soyadi">Nachname</label>
                    <input type="text" id="soyadi" name="soyadi" required value="<?= isset($_POST['soyadi']) ? htmlspecialchars($_POST['soyadi']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="mail">Email</label>
                    <input type="email" id="mail" name="mail" required value="<?= isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="sifre">Passwort</label>
                    <input type="password" id="sifre" name="sifre" required>
                </div>
                <button class="btn-primary" type="submit">Registrieren</button>
            </form>
            <p class="login-link">
                <a href="/index.php?action=login">Schon ein Konto? Anmelden</a>
            </p>
        </div>
    </body>
</html>

