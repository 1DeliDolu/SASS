<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title']) ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="main-layout">
            <section class="sidebar">
                <h2>SASS</h2>
                <ul class="project-list">
                    <?php if (!empty($data['docsDirs'])): ?>
                        <?php $firstSeg = function ($p) {
                            $p = trim($p);
                            if ($p === '')
                                return '';
                            $parts = preg_split('#[\\/]#', $p);
                            return $parts[0] ?? '';
                        };
                        ?>
                        <?php $currentTop = $firstSeg($data['path'] ?? ''); ?>
                        <?php foreach ($data['docsDirs'] as $dir): ?>
                            <?php $isActive = $currentTop !== '' && $currentTop === $dir['path']; ?>
                                <li class="<?= $isActive ? 'active' : '' ?>">
                                    <a href="/index.php?action=docs&amp;path=<?= urlencode($dir['path']) ?>">
                                        <?= htmlspecialchars($dir['name']) ?>
                                    </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>README-Verzeichnis nicht gefunden.</li>
                    <?php endif; ?>
                </ul>
            </section>
            <section class="main-content">
                <h1>SASS MVC Start</h1>
                <p>Willkommen!</p>
                <p>Diese Seite ist ein modernes SASS + PHP MVC Starterprojekt.</p>
            </section>
        </div>
    </body>
</html>
