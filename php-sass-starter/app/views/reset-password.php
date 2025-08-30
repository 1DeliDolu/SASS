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
            <h1>Neues Passwort</h1>

            <?php if (!empty($data['status'])): ?>
                <div class="auth-status success">✅ <?= htmlspecialchars($data['status']) ?></div>
            <?php endif; ?>
            <?php if (!empty($data['error'])): ?>
                <div class="auth-status error">⚠️ <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <?php if (!empty($data['valid'])): ?>
            <form class="register-form" method="post" action="/index.php?action=reset_password&amp;token=<?= urlencode((string)($data['token'] ?? '')) ?>">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($data['csrf'] ?? '') ?>">
                <div class="form-group">
                    <label for="sifre1">Neues Passwort</label>
                    <input type="password" id="sifre1" name="sifre1" required>
                </div>
                <div class="form-group">
                    <label for="sifre2">Passwort bestätigen</label>
                    <input type="password" id="sifre2" name="sifre2" required>
                </div>
                <button class="btn-primary" type="submit">Speichern</button>
            </form>
            <?php else: ?>
                <p class="forgot-help">Der Link ist ungültig oder abgelaufen. Du kannst einen neuen Link anfordern.</p>
                <p class="login-link"><a href="/index.php?action=forgot_password">Neuen Link senden</a></p>
            <?php endif; ?>

            <p class="login-link">
                <a href="/index.php?action=login">Zurück zum Login</a>
            </p>
        </div>
    </body>
    </html>
