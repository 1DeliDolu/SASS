<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title']) ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="main-layout">
            <section class="sidebar">
                <h2>Projeler</h2>
                <ul class="project-list">
                    <?php if (!empty($data['docsDirs'])): ?>
                        <?php $firstSeg = function($p){ $p = trim($p); if ($p==='') return ''; $parts = preg_split('#[\\/]#',$p); return $parts[0] ?? ''; }; ?>
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
                        <li>README klasörü bulunamadı.</li>
                    <?php endif; ?>
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

