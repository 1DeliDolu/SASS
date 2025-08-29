<!DOCTYPE html>
<html lang="de"></html>
  <head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title'] ?? 'Projekt bearbeiten') ?></title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
    <?php include __DIR__ . '/../../nav-bar.php'; ?>
    <div class="main-layout">
      <section class="main-content">
        <h1>Projekt bearbeiten</h1>
        <?php if (!empty($data['error'])): ?>
          <p style="color:#c0392b; font-weight:700;"><?= htmlspecialchars($data['error']) ?></p>
        <?php endif; ?>
        <form id="edit" method="post" action="/index.php?action=admin_project_edit&amp;id=<?= (int)$data['project']['id'] ?>" class="register-form">
          <div class="form-group">
            <label for="ad">Name</label>
            <input type="text" id="ad" name="ad" required value="<?= htmlspecialchars($_POST['ad'] ?? $data['project']['ad']) ?>">
          </div>
          <div class="form-group">
            <label for="aciklama">Beschreibung</label>
            <input type="text" id="aciklama" name="aciklama" value="<?= htmlspecialchars($_POST['aciklama'] ?? ($data['project']['aciklama'] ?? '')) ?>">
          </div>
          <div class="form-group">
            <label for="musteri_id">Kunde</label>
            <select id="musteri_id" name="musteri_id">
              <option value="">â€” wÃ¤hlen â€”</option>
              <?php $sel = (int)($_POST['musteri_id'] ?? ($data['project']['musteri_id'] ?? 0)); ?>
              <?php foreach ($data['musteriler'] as $m): ?>
                <option value="<?= (int)$m['id'] ?>" <?= $sel===(int)$m['id']?'selected':'' ?>><?= htmlspecialchars(($m['adi'] ?? '') . ' ' . ($m['soyadi'] ?? '') . ' â€” ' . ($m['mail'] ?? '')) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="durum">Status</label>
            <?php $dur = $_POST['durum'] ?? ($data['project']['durum'] ?? 'neu'); ?>
            <select id="durum" name="durum">
              <option value="yeni" <?= $dur==='yeni'?'selected':'' ?>>neu</option>
              <option value="devam" <?= $dur==='devam'?'selected':'' ?>>lÃ¤uft</option>
              <option value="tamam" <?= $dur==='tamam'?'selected':'' ?>>abgeschlossen</option>
              <option value="iptal" <?= $dur==='iptal'?'selected':'' ?>>abgebrochen</option>
            </select>
          </div>
          <button class="btn-primary" type="submit">Speichern</button>
          <a class="nav-link" href="/index.php?action=admin_projects">ZurÃ¼ck</a>
        </form>

        <hr style="margin:1.5rem 0; opacity:.4;">

        <h2>Zuweisungsverwaltung</h2>
        <form method="post" action="/index.php?action=admin_assignment_add" class="register-form" style="max-width:520px;">
          <input type="hidden" name="project_id" value="<?= (int)$data['project']['id'] ?>">
          <div class="form-group">
            <label for="calisan_id">Mitarbeiter</label>
            <select id="calisan_id" name="calisan_id" required>
              <option value="">â€” wÃ¤hlen â€”</option>
              <?php foreach ($data['calisanlar'] as $c): ?>
                <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars(($c['adi'] ?? '') . ' ' . ($c['soyadi'] ?? '') . ' â€” ' . ($c['mail'] ?? '')) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="rol_projede">Rolle (optional)</label>
            <input type="text" id="rol_projede" name="rol_projede" placeholder="verantwortlich / Mitglied usw.">
          </div>
          <button class="btn-primary" type="submit">HinzufÃ¼gen</button>
        </form>

        <h3>Zugewiesene</h3>
        <ul class="project-list">
          <?php if (!empty($data['assignments'])): ?>
            <?php foreach ($data['assignments'] as $a): ?>
              <li style="display:flex; align-items:center; gap:.5rem;">
                <span><?= htmlspecialchars(($a['adi'] ?? '') . ' ' . ($a['soyadi'] ?? '')) ?> (<?= htmlspecialchars($a['mail'] ?? '') ?>) <?= !empty($a['rol_projede']) ? 'â€” ' . htmlspecialchars($a['rol_projede']) : '' ?></span>
                <form method="post" action="/index.php?action=admin_assignment_remove" onsubmit="return confirm('Zuweisung entfernen?');"></form>
                  <input type="hidden" name="project_id" value="<?= (int)$data['project']['id'] ?>">
                  <input type="hidden" name="calisan_id" value="<?= (int)$a['calisan_id'] ?>">
                  <button type="submit">Entfernen</button>
                </form>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>Keine Zuweisung gefunden.</li>
          <?php endif; ?>
        </ul>

        <hr style="margin:1.5rem 0; opacity:.4;">

        <h2 id="milestones">Meilensteine</h2>
        <?php
          if (!isset($data['milestones'])) {
            require_once __DIR__ . '/../../../models/ProjectModel.php';
            $pmTmp = new ProjectModel();
            $data['milestones'] = $pmTmp->getMilestones($data['project']['id']);
          }
        ?>
        <form method="post" action="/index.php?action=admin_milestone_add" class="register-form" style="max-width:520px;">
          <input type="hidden" name="project_id" value="<?= (int)$data['project']['id'] ?>">
          <div class="form-group">
            <label for="baslik">Titel</label>
            <input type="text" id="baslik" name="baslik" required placeholder="z.B.: Design-Abgabe">
          </div>
          <div class="form-group">
            <label for="due_date">FÃ¤lligkeitsdatum</label>
            <input type="datetime-local" id="due_date" name="due_date" required>
          </div>
          <button class="btn-primary" type="submit">HinzufÃ¼gen</button>
        </form>

        <h3>Bevorstehend / Ausstehend</h3>
        <ul class="project-list">
          <?php if (!empty($data['milestones'])): ?>
            <?php foreach ($data['milestones'] as $m): ?>
              <li style="display:flex; align-items:center; gap:.5rem;">
                <span><?= htmlspecialchars($m['baslik'] ?? '') ?> â€” <?= htmlspecialchars($m['due_date'] ?? '') ?>
                  <?php if (!empty($m['completed_at'])): ?>
                    <em>(abgeschlossen: <?= htmlspecialchars($m['completed_at']) ?>)</em>
                  <?php endif; ?>
                </span>
                <?php if (empty($m['completed_at'])): ?>
                  <form method="post" action="/index.php?action=admin_milestone_done" onsubmit="return confirm('Als abgeschlossen markieren?');"></form>
                    <input type="hidden" name="project_id" value="<?= (int)$data['project']['id'] ?>">
                    <input type="hidden" name="milestone_id" value="<?= (int)$m['id'] ?>">
                    <button type="submit">Abgeschlossen</button>
                  </form>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>Keine EintrÃ¤ge.</li>
          <?php endif; ?>
        </ul>
      </section>
    </div>
  </body>
</html>

