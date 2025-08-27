<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title'] ?? 'Dokümanlar') ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="main-layout">
            <section class="sidebar">
                <h2>Projeler</h2>
                <ul class="project-list">
                    <?php if (!empty($data['docsDirs'])): ?>
                        <?php foreach ($data['docsDirs'] as $dir): ?>
                            <li>
                                <a href="/index.php?action=docs&amp;path=<?= urlencode($dir['path']) ?>">
                                    <?= htmlspecialchars($dir['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </section>
            <section class="main-content">
                <?php if (!empty($data['breadcrumbs'])): ?>
                    <nav class="breadcrumbs">
                        <?php foreach ($data['breadcrumbs'] as $i => $bc): ?>
                            <?php if ($i > 0): ?> <span class="sep">/</span> <?php endif; ?>
                            <?php if ($i === count($data['breadcrumbs']) - 1): ?>
                                <span class="crumb current"><?= htmlspecialchars($bc['name']) ?></span>
                            <?php else: ?>
                                <a class="crumb" href="/index.php?action=docs&amp;path=<?= urlencode($bc['path']) ?>"><?= htmlspecialchars($bc['name']) ?></a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
                <?php if (!empty($data['listing'])): ?>
                    <h1><?= htmlspecialchars($data['path'] ?: 'README') ?></h1>
                    <ul class="project-list">
                        <?php foreach ($data['listing'] as $it): ?>
                            <?php $isActive = isset($data['path']) && $data['path'] === $it['path']; ?>
                            <li class="<?= $isActive ? 'active' : '' ?>">
                                <?php if ($it['type'] === 'dir'): ?>
                                    <a href="/index.php?action=docs&amp;path=<?= urlencode($it['path']) ?>">📁 <?= htmlspecialchars($it['name']) ?></a>
                                <?php else: ?>
                                    <a href="/index.php?action=docs&amp;path=<?= urlencode($it['path']) ?>">📄 <?= htmlspecialchars($it['name']) ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif (!empty($data['html'])): ?>
                    <article class="doc-article">
                        <?= $data['html'] ?>
                    </article>
                <?php else: ?>
                    <p>İçerik bulunamadı.</p>
                <?php endif; ?>
            </section>
        </div>
    </body>
    </html>
