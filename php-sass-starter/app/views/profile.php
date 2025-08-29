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
            <h1>Hallo,
                <?= htmlspecialchars(($data['user']['adi'] ?? '') . ' ' . ($data['user']['soyadi'] ?? '')) ?>
            </h1>
            <div class="register-form">
                <div class="form-group">
                    <label>Vorname</label>
                    <input type="text" value="<?= htmlspecialchars($data['user']['adi'] ?? '') ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Nachname</label>
                    <input type="text" value="<?= htmlspecialchars($data['user']['soyadi'] ?? '') ?>" disabled>
                </div>
                <div class="form-group">
                    <label>E‑Mail</label>
                    <input type="text" value="<?= htmlspecialchars($data['user']['mail'] ?? '') ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Letzter Login</label>
                    <input type="text" value="<?= htmlspecialchars($data['user']['son_giris_tarihi'] ?? 'Keine Information') ?>" disabled>
                </div>
                <div>
                    <a href="/index.php?action=update" class="btn-primary" style="display:inline-block;text-decoration:none;text-align:center;">Informationen aktualisieren</a>
                </div>
            </div>
        </div>
    </body>
</html>

