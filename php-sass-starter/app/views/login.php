<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title>Giriş Yap</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <div class="register-container">
            <h1>Giriş Yap</h1>
            <form class="register-form" method="post" action="/index.php?action=login">
                <div class="form-group">
                    <label for="mail">Email</label>
                    <input type="email" id="mail" name="mail" required>
                </div>
                <div class="form-group">
                    <label for="sifre">Şifre</label>
                    <input type="password" id="sifre" name="sifre" required>
                </div>
                <button class="btn-primary" type="submit">Giriş Yap</button>
            </form>
            <p class="login-link">
                <a href="/index.php?action=register">Hesabınız yok mu? Kayıt olun</a>
            </p>
        </div>
    </body>
</html>

