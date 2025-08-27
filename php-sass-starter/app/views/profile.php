<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title'] ?? 'Profilim') ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="register-container">
            <h1>Merhaba, <?= htmlspecialchars(($data['user']['adi'] ?? '') . ' ' . ($data['user']['soyadi'] ?? '')) ?></h1>
            <div class="register-form">
                <div class="form-group">
                    <label>Adı</label>
                    <input type="text" value="<?= htmlspecialchars($data['user']['adi'] ?? '') ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Soyadı</label>
                    <input type="text" value="<?= htmlspecialchars($data['user']['soyadi'] ?? '') ?>" disabled>
                </div>
                <div class="form-group">
                    <label>E‑posta</label>
                    <input type="text" value="<?= htmlspecialchars($data['user']['mail'] ?? '') ?>" disabled>
                </div>
                <div class="form-group">
                    <label>En son giriş</label>
                    <input type="text" value="<?= htmlspecialchars($data['user']['son_giris_tarihi'] ?? 'Bilgi yok') ?>" disabled>
                </div>
                <div>
                    <a href="/index.php?action=update" class="btn-primary" style="display:inline-block;text-decoration:none;text-align:center;">Bilgileri Güncelle</a>
                </div>
            </div>
        </div>
    </body>
</html>
