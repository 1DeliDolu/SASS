<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title'] ?? 'Bilgileri Güncelle') ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="register-container">
            <h1>Bilgilerini Güncelle</h1>
            <?php if (!empty($data['message'])): ?>
                <p style="color: green; font-weight: 700;"><?= htmlspecialchars($data['message']) ?></p>
            <?php endif; ?>
            <?php if (!empty($data['error'])): ?>
                <p style="color: #c0392b; font-weight: 700;"><?= htmlspecialchars($data['error']) ?></p>
            <?php endif; ?>
            <form class="register-form" method="post" action="/index.php?action=update">
                <div class="form-group">
                    <label for="adi">Adı</label>
                    <input type="text" id="adi" name="adi" required value="<?= htmlspecialchars($data['user']['adi'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="soyadi">Soyadı</label>
                    <input type="text" id="soyadi" name="soyadi" required value="<?= htmlspecialchars($data['user']['soyadi'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="mail">Email</label>
                    <input type="email" id="mail" name="mail" required value="<?= htmlspecialchars($data['user']['mail'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="sifre">Yeni Şifre (boş bırakılırsa değişmez)</label>
                    <input type="password" id="sifre" name="sifre" placeholder="Yeni şifre">
                </div>
                <button class="btn-primary" type="submit">Kaydet</button>
                <a href="/index.php?action=profile" class="nav-link" style="margin-left:0.8rem;">İptal</a>
            </form>
        </div>
    </body>
</html>
