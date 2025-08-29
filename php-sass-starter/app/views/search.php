<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title'] ?? 'Suche') ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="main-layout">
            <section class="main-content">
                <h1>Suche</h1>
                <?php if (!empty($data['q'])): ?>
                    <p>
                        <strong>Gesucht:</strong>
                        "<?= htmlspecialchars($data['q']) ?>"</p>
                    <?php if (!empty($data['results'])): ?>
                        <ul
                            class="search-results">
                            <?php foreach ($data['results'] as $res): ?>
                                <li class="search-result-item">
                                    <div class="search-result-file">📄
                                        <a href="/index.php?action=docs&amp;path=<?= urlencode($res['file']) ?>"><?= htmlspecialchars($res['display'] ?? basename($res['file'])) ?></a>
                                        <span style="opacity:.6; font-size:.92em;">(<?= htmlspecialchars($res['file']) ?>)</span>
                                        <span style="opacity:.6;">—
                                            <?= (int) $res['count'] ?>
                                            Treffer</span>
                                    </div>
                                    <ul
                                        class="search-snippets">
                                        <?php foreach ($res['matches'] as $m): ?>
                                            <li>
                                                <code>L<?= (int) $m['line'] ?>:</code>
                                                <?= preg_replace('/(' . preg_quote($data['q'], '/') . ')/i', '<mark class="hl">$1</mark>', htmlspecialchars($m['text'])) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Keine Treffer gefunden.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Suchen Sie etwas…</p>
                <?php endif; ?>
            </section>
        </div>
    </body>
</html>

