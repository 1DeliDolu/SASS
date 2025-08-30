<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$isAuth = !empty($_SESSION['user']);
$role = $_SESSION['user']['role'] ?? 'calisan';
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary" style="background:#253445;">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand logo" href="/index.php" style="color:#fff; font-weight:800; font-size:2rem;">SASS MVC</a>
        <div class="d-flex flex-grow-1 justify-content-end align-items-center">
            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul
                    class="navbar-nav mb-2 mb-lg-0 flex-row flex-lg-row">
                    <?php if ($isAuth): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?action=projects">Projekte</a>
                        </li>
                        <?php if ($role === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/index.php?action=admin">Admin-Panel</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?action=profile">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?action=logout">Abmelden</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?action=login">Anmelden</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?action=register">Registrieren</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <form class="d-flex ms-lg-3 mt-2 mt-lg-0" role="search" action="/index.php" method="get">
                    <input type="hidden" name="action" value="search">
                    <input class="form-control me-2" type="search" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Suchen..." aria-label="Search"/>
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>
</nav>
<!-- Bootstrap JS (opsiyonel, responsive menü için gerekli) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

