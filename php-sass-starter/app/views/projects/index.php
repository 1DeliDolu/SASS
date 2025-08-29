<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title'] ?? 'Projekte') ?></title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
    <?php include __DIR__ . '/../nav-bar.php'; ?>
    <div class="main-layout">
      <section class="main-content">
        <h1>Projekte</h1>
        <div class="table-wrap">
        <?php require_once __DIR__ . '/../../lib/I18n.php'; $lang = I18n::lang(); ?>
        <table class="table table-sticky table-compact">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Kunde</th>
                <th>Status</th>
                <th>Termin</th>
                <th>Aktion</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data['projects'])): ?>
                <?php foreach ($data['projects'] as $p): ?>
                    <tr><td>
                      <?= (int) $p['id'] ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($p['ad'] ?? '') ?>
                    </td>
                    <td>
                      <?php $mail = (string)($p['musteri_mail'] ?? ''); $short = (strlen($mail) > 28) ? (substr($mail,0,14) . '…' . substr($mail, -10)) : $mail; ?>
                      <span class="ellipsis" title="<?= htmlspecialchars($mail) ?>"><?= htmlspecialchars($short) ?></span>
                    </td>
                    <td>
                      <?php $d = (string)($p['durum'] ?? ''); echo htmlspecialchars(I18n::statusLabel($d, $lang)); ?>
                    </td>
                    <td>
                      <?php if (!empty($p['next_milestone'])): ?>
                        <?php
                        $ms = $p['next_milestone'];
                        $due = isset($ms['due_date']) ? strtotime($ms['due_date']) : null;
                        $now = time();
                        $diffDays = $due ? floor(($due - $now) / 86400) : null;
                        $cls = 'green';
                        if ($diffDays !== null) {
                          if ($diffDays < 0) {
                            $cls = 'red';
                          } elseif ($diffDays <= 3) {
                            $cls = 'orange';
                          } elseif ($diffDays <= 7) {
                            $cls = 'yellow';
                          } else {
                            $cls = 'green';
                          }
                        }
                        ?><span
                          class="milestone-badge <?= $cls ?>" title="<?= htmlspecialchars($ms['baslik'] ?? '') ?>"> <?= htmlspecialchars(($ms['baslik'] ?? 'Milestone') . ' - ' . ($ms['due_date'] ?? '')) ?>
                        </span>
                      <?php else: ?>
                        <span class="muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a class="btn-primary nav-link" style="padding:0.3em 1em; border-radius:6px; font-weight:600;" href="/index.php?action=project_show&amp;id=<?= (int) $p['id'] ?>">Anzeigen</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6">Kein Eintrag gefunden.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </body>
</html>
