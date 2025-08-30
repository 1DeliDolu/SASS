<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title'] ?? 'Passwort zurücksetzen') ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="register-container forgot-container">
            <h1>Passwort zurücksetzen</h1>

            <?php if (!empty($data['status'])): ?>
                <div class="auth-status success">✅ <?= htmlspecialchars($data['status']) ?></div>
            <?php endif; ?>
            <?php if (!empty($data['error'])): ?>
                <div class="auth-status error">⚠️ <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <p class="forgot-help">Gib deine E‑Mail ein. Wir senden dir einen Link zum Zurücksetzen deines Passworts.</p>

            <form class="register-form" method="post" action="/index.php?action=forgot_password">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($data['csrf'] ?? '') ?>">
                <div class="form-group">
                    <label for="mail">E‑Mail</label>
                    <input type="email" id="mail" name="mail" required placeholder="name@example.com" value="<?= isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : '' ?>">
                </div>
                <?php if (!empty($data['recaptcha_site_key'])): ?>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($data['recaptcha_site_key']) ?>"></div>
                </div>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <?php endif; ?>
                <button class="btn-primary" type="submit">Link senden</button>
            </form>

            <p class="login-link">
                <a href="/index.php?action=login">Zurück zum Login</a>
            </p>
        </div>
    </body>
</html>
