<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title'] ?? 'Projektdetails') ?></title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
    <?php include __DIR__ . '/../nav-bar.php'; ?>
    <div class="main-layout">
      <section class="main-content">
        <h1 style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
          <span>Projekt: <?= htmlspecialchars($data['project']['ad'] ?? '') ?></span>
          <?php if (!empty($_SESSION['user']) && (($_SESSION['user']['role'] ?? 'calisan') === 'admin')): ?>
            <a class="btn-action btn-edit" href="/index.php?action=admin_project_edit&amp;id=<?= (int)($data['project']['id'] ?? 0) ?>#edit">Edit</a>
          <?php endif; ?>
        </h1>
        <p>
          <strong>Kunde:</strong>
          <?= htmlspecialchars($data['project']['musteri_mail'] ?? '-') ?>
        </p>
        <p>
          <strong>Status:</strong>
          <?php require_once __DIR__ . '/../../lib/I18n.php'; $lang = I18n::lang(); $d = (string)($data['project']['durum'] ?? '-'); echo htmlspecialchars(I18n::statusLabel($d,$lang)); ?>
        </p>
        <p>
          <strong>Beschreibung:</strong><br><?= nl2br(htmlspecialchars($data['project']['aciklama'] ?? '')) ?></p>

        <h2>Zugewiesene Mitarbeiter</h2>
        <ul class="project-list">
          <?php if (!empty($data['assignments'])): ?>
            <?php foreach ($data['assignments'] as $a): ?>
                <li>
                <?= htmlspecialchars(($a['adi'] ?? '') . ' ' . ($a['soyadi'] ?? '')) ?>(<?= htmlspecialchars($a['mail'] ?? '') ?>
                )
                <?php if (!empty($a['rol_projede'])): ?>
                —
                  <?= htmlspecialchars($a['rol_projede']) ?>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>Keine Zuweisung gefunden.</li>
          <?php endif; ?>
        </ul>

        <hr
        style="margin:1.5rem 0; opacity:.4;">

        <?php
        if (!isset($data['milestones']) || !is_array($data['milestones'])) {
          require_once __DIR__ . '/../../models/ProjectModel.php';
          $pmTmp = new ProjectModel();
          $data['milestones'] = $pmTmp->getMilestones($data['project']['id']);
          $data['next_milestone'] = $pmTmp->getNextMilestone($data['project']['id']);
        }
        ?>

        <h2>Meilensteine</h2>
        <?php if (!empty($data['next_milestone'])): ?>
          <?php
          $ms = $data['next_milestone'];
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
          ?>
          <p>
            <strong>Nächster Termin:</strong>
            <span
              class="milestone-badge <?= $cls ?>" title="<?= htmlspecialchars($ms['baslik'] ?? '') ?>"><?= htmlspecialchars(($ms['baslik'] ?? 'Milestone') . ' - ' . ($ms['due_date'] ?? '')) ?>
            </span>
          </p>
        <?php endif; ?>

        <ul class="project-list">
          <?php if (!empty($data['milestones'])): ?>
            <?php foreach ($data['milestones'] as $m): ?>
                <li>
                <?= htmlspecialchars($m['baslik'] ?? '') ?>
                —
                <?= htmlspecialchars($m['due_date'] ?? '') ?>
                <?php if (!empty($m['completed_at'])): ?><em>(abgeschlossen:
                    <?= htmlspecialchars($m['completed_at']) ?>)
                </em>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>Kein Eintrag.</li>
          <?php endif; ?>
        </ul>
      </section>
    </div>
  </body>
</html>
