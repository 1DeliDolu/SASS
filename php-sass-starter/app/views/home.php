<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title']) ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <nav class="navbar">
            <div class="navbar-left">
                <span class="logo">SASS MVC</span>
            </div>
            <div class="navbar-right">
                <a href="/index.php?action=login" class="nav-link">Giriş Yap</a>
                <a  style="margin-right:30px" href="/index.php?action=register" class="nav-link">Kayıt Ol</a>
            </div>
        </nav>
        <div class="main-layout">
            <section class="sidebar">
                <h2>Projeler</h2>
                <ul class="project-list">
                    <li>
                        <a href="#">SASS Başlangıç</a>
                    </li>
                    <li>
                        <a href="#">SASS Değişkenler</a>
                    </li>
                    <li>
                        <a href="#">SASS Fonksiyonlar</a>
                    </li>
                    <li>
                        <a href="#">SASS At-Rules</a>
                    </li>
                    <li>
                        <a href="#">SASS Modüller</a>
                    </li>
                </ul>
            </section>
            <section class="main-content">
                <h1><?= htmlspecialchars($data['title']) ?></h1>
                <p><?= htmlspecialchars($data['message']) ?></p>
                <p>Bu sayfa modern bir SASS + PHP MVC başlangıç projesidir.</p>
            </section>
        </div>
    </body>
</html>

