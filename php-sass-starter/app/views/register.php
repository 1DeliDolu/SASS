<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title>Kayıt Ol</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <div class="register-container">
            <h1>Kayıt Ol</h1>
            <form class="register-form" method="post" action="/index.php?action=register">
                <div class="form-group">
                    <label for="adi">Adı</label>
                    <input type="text" id="adi" name="adi" required>
                </div>
                <div class="form-group">
                    <label for="soyadi">Soyadı</label>
                    <input type="text" id="soyadi" name="soyadi" required>
                </div>
                <div class="form-group">
                    <label for="mail">Email</label>
                    <input type="email" id="mail" name="mail" required>
                </div>
                <div class="form-group">
                    <label for="sifre">Şifre</label>
                    <input type="password" id="sifre" name="sifre" required>
                </div>
                <button class="btn-primary" type="submit">Kayıt Ol</button>
            </form>
            <p class="login-link">
                <a href="/index.php?action=login">Zaten hesabınız var mı? Giriş yapın</a>
            </p>
        </div>
    </body>
</html>

