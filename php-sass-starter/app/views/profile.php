<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title'] ?? 'Mein Profil') ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="register-container">
            <h1>Merhaba, <?= htmlspecialchars(($data['user']['adi'] ?? '') . ' ' . ($data['user']['soyadi'] ?? '')) ?></h1>
            <?php if (!empty($_SESSION['flash'])): ?>
                <div style="margin:10px 0; padding:10px; background:#e1f5fe; border:1px solid #81d4fa; border-radius:6px; color:#01579b;">
                    <?= htmlspecialchars($_SESSION['flash']) ?>
                </div>
                <?php $_SESSION['flash'] = null; unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <div class="register-form" style="display:flex; gap:12px;">
                <a href="/index.php?action=update" class="btn-primary" style="display:inline-block;text-decoration:none;text-align:center; padding:10px 16px;">Bilgileri Güncelle</a>
                <a href="/index.php?action=mail_inbox" class="btn-primary" style="display:inline-block;text-decoration:none;text-align:center; padding:10px 16px;">Mail</a>
            </div>
        </div>
    </body>
</html>
