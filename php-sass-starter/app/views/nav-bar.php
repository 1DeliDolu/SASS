<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$isAuth = !empty($_SESSION['user']);
?>
<nav class="navbar">
    <div class="navbar-left">
        <a href="/index.php" class="logo" style="text-decoration:none;color:#fff;">SASS MVC</a>
    </div>
    <div class="navbar-right">
        <form class="search-form" action="/index.php" method="get" role="search">
            <input type="hidden" name="action" value="search">
            <div class="search">
                <span class="search-icon" aria-hidden="true">🔍</span>
                <input
                    class="search-input"
                    type="search"
                    name="q"
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                    placeholder="Ara..."
                    aria-label="Site içinde ara"
                >
            </div>
        </form>
        <?php if ($isAuth): ?>
            <a href="/index.php?action=profile" class="nav-link">Profil</a>
            <a href="/index.php?action=logout" class="nav-link">Çıkış</a>
        <?php else: ?>
            <a href="/index.php?action=login" class="nav-link">Giriş Yap</a>
            <a href="/index.php?action=register" class="nav-link">Kayıt Ol</a>
        <?php endif; ?>
    </div>
    </nav>
<script src="/js/search.js"></script>
