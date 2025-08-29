<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Anmelden</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="register-container">
            <h1>Anmelden</h1>
            <?php $next = isset($_GET['next']) ? $_GET['next'] : ($_SESSION['redirect_after_login'] ?? ''); ?>
            <form class="register-form" method="post" action="/index.php?action=login<?= !empty($next) ? '&amp;next=' . urlencode($next) : '' ?>">
                <div class="form-group">
                    <label for="mail">Email</label>
                    <input type="email" id="mail" name="mail" required value="<?= isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="sifre">Passwort</label>
                    <input type="password" id="sifre" name="sifre" required>
                </div>
                <button class="btn-primary" type="submit">Anmelden</button>
            </form>
            <p class="login-link">
                <a href="/index.php?action=register">Noch kein Konto? Registrieren</a>
            </p>
        </div>
    </body>
</html>

