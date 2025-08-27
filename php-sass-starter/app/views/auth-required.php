<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title'] ?? 'Erişim Gerekli') ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="register-container">
            <h1>Erişim Gerekli</h1>
            <p class="login-link" style="margin-top:0.25rem;">
                <?= htmlspecialchars($data['reason'] ?? 'Bu içeriği görmek için lütfen giriş yapın veya kayıt olun.') ?>
            </p>
            <div style="display:flex; gap:0.75rem; margin-top:1rem;">
                <?php $next = isset($_SERVER['REQUEST_URI']) ? urlencode($_SERVER['REQUEST_URI']) : ''; ?>
                <a class="btn-primary" href="/index.php?action=login<?= $next ? '&amp;next=' . $next : '' ?>" style="text-decoration:none; display:inline-block;">Giriş Yap</a>
                <a class="nav-link" href="/index.php?action=register<?= $next ? '&amp;next=' . $next : '' ?>">Kayıt Ol</a>
            </div>
        </div>
    </body>
</html>
