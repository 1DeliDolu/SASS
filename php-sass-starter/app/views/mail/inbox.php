<!doctype html>
<html lang="tr">
  <head>
    <meta charset="utf-8">
    <title>Mail</title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
    <?php include __DIR__ . '/../nav-bar.php'; ?>
    <div class="mail-app" style="display:grid; grid-template-columns: 240px 380px 1fr; gap:16px; align-items:start; margin-top:16px;">
      <aside class="card" style="padding:16px; background:#fff; border-radius:12px; box-shadow: 0 8px 24px rgba(44,62,80,.08);">
        <a class="btn-primary" href="/index.php?action=mail_compose" style="display:block; text-align:center;">Yeni Mesaj</a>
        <div style="margin-top:12px;">
          <select id="mailBoxSelect" style="width:100%; padding:8px; border-radius:8px; border:1px solid #e2e8f0; font-size:1rem;">
            <option value="mail_inbox" <?= ($data['active'] ?? '') === 'inbox' ? 'selected' : '' ?>>Gelen Kutusu</option>
            <option value="mail_sent" <?= ($data['active'] ?? '') === 'sent' ? 'selected' : '' ?>>Gönderilmiş</option>
            <option value="mail_scheduled" <?= ($data['active'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Zamanlı</option>
          </select>
        </div>
      </aside>

      <section class="card" style="background:#fff; border-radius:12px; box-shadow: 0 8px 24px rgba(44,62,80,.08); overflow:hidden;">
        <div
          class="mail-list" style="max-height:70vh; overflow:auto;">
          <?php if (($data['active'] ?? '') === 'scheduled'): ?>
            <div style="padding:10px 12px; border-bottom:1px solid #f0f2f6; display:flex; align-items:center; gap:8px;">
              <label style="font-weight:600; color:#334155;">Tarih:</label>
              <input
              type="date" value="<?= htmlspecialchars($data['selectedDate'] ?? '') ?>" onchange="location='<?= '/index.php?action=mail_scheduled&date=' ?>'+this.value">
              <?php if (!empty($data['selectedDate'])): ?>
                <a class="btn-primary" href="/index.php?action=mail_scheduled" style="padding:6px 10px; font-size:0.95rem;">Temizle</a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <?php $messages = $data['messages'] ?? []; ?>
          <?php if (empty($messages)): ?><p style="padding:16px"> Hiç mesaj yok.</p>
          <?php else:
            foreach ($messages as $m): ?>
              <article class="item" onclick="location='/index.php?action=mail_<?= htmlspecialchars($data['active']) ?>&id=<?= (int) $m['thread_id'] ?>&msg=<?= (int) $m['id'] ?>'" style="cursor:pointer; padding:12px 14px; border-bottom:1px solid #f0f2f6;">
                <div style="font-weight:700; color:#1f2937;"><?= htmlspecialchars($m['from_name'] ?: $m['from_email'] ?: 'Siz') ?></div>
                <div style="color:#334155; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($m['subject'] ?: '(No subject)') ?></div>
                <div style="font-size:12px;color:#64748b; margin-top:4px;">
                  <time><?= htmlspecialchars(($m['received_at'] ?? $m['sent_at'] ?? $m['created_at'])) ?></time>
                </div>
              </article>
            <?php endforeach; endif; ?>
        </div>
      </section>

      <section class="card" style="background:#fff; border-radius:12px; box-shadow: 0 8px 24px rgba(44,62,80,.08); min-height:60vh;">
        <div class="reader" style="padding:16px;">
          <?php if (!empty($data['thread'])): ?>
            <?php
            $threadId = (int) ($data['thread']['id'] ?? 0);
            $msgs = $data['threadMessages'] ?? [];
            $meEmail = $_SESSION['user']['mail'] ?? '';
            $lastAny = null;
            foreach ($msgs as $mm) {
              $lastAny = $mm;
            }
            // Prefer: last message whose sender is NOT me
            $replyTo = '';
            for ($i = count($msgs) - 1; $i >= 0; $i--) {
              $m = $msgs[$i];
              $from = $m['from_email'] ?? '';
              if ($from !== '' && strcasecmp($from, (string) $meEmail) !== 0) {
                $replyTo = $from;
                break;
              }
            }
            // Fallback: pick first recipient from last message that is not me
            if ($replyTo === '' && $lastAny) {
              $candidates = [];
              foreach (['to_json', 'cc_json', 'bcc_json'] as $k) {
                $arr = json_decode($lastAny[$k] ?? '[]', true);
                if (is_array($arr)) {
                  $candidates = array_merge($candidates, $arr);
                }
              }
              foreach ($candidates as $cand) {
                if ($cand !== '' && strcasecmp($cand, (string) $meEmail) !== 0) {
                  $replyTo = $cand;
                  break;
                }
              }
            }
            $subject = 'Re: ' . ($data['thread']['subject'] ?? '');
            $fwdSubject = 'Fwd: ' . ($data['thread']['subject'] ?? '');
            ?>
              <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;"> <h2 style="margin:0; color:#111827; font-size:1.3rem;"><?= htmlspecialchars($data['thread']['subject'] ?? '') ?></h2>
              <div style="display:flex; gap:8px;">
                <a class="btn-primary" href="/index.php?action=mail_compose&mode=reply&focus=1&subject=<?= rawurlencode($subject) ?>&to=<?= rawurlencode($replyTo) ?>&thread_id=<?= $threadId ?>" style="padding:8px 12px; font-size:0.95rem;">Cevapla</a>
                <a class="btn-primary" href="/index.php?action=mail_compose&mode=forward&focus=1&subject=<?= rawurlencode($fwdSubject) ?>&forward_tid=<?= $threadId ?>" style="padding:8px 12px; font-size:0.95rem;">İlet</a>
              </div>
            </div>
            <?php
            $selId = (int) ($data['selectedMsgId'] ?? 0);
            $sel = null;
            $lastAny = null;
            foreach (($data['threadMessages'] ?? []) as $tm) {
              $lastAny = $tm;
              if ($selId && (int) $tm['id'] === $selId) {
                $sel = $tm;
                break;
              }
            }
            if (!$sel) {
              $sel = $lastAny;
            }
            ?>
            <?php if (!empty($sel)): ?>
              <?php
              $fromLine = trim(($sel['from_name'] ?? '') . ' <' . ($sel['from_email'] ?? '') . '>');
              $tos = json_decode($sel['to_json'] ?? '[]', true) ?: [];
              $ccs = json_decode($sel['cc_json'] ?? '[]', true) ?: [];
              $date = htmlspecialchars($sel['sent_at'] ?? $sel['received_at'] ?? $sel['created_at'] ?? '');
              ?>
                <div style="padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; margin:10px 0; background:#fafafa;"> <div style="font-size:14px; color:#111827;">
                  <strong>Kimden:</strong>
                  <?= htmlspecialchars($fromLine) ?>
                </div>
                <?php if (!empty($tos)): ?>
                  <div style="font-size:13px; color:#374151; margin-top:4px;">
                    <strong>Kime:</strong>
                    <?= htmlspecialchars(implode(', ', $tos)) ?>
                  </div>
                <?php endif; ?>
                <?php if (!empty($ccs)): ?>
                  <div style="font-size:13px; color:#374151; margin-top:4px;">
                    <strong>CC:</strong>
                    <?= htmlspecialchars(implode(', ', $ccs)) ?>
                  </div>
                <?php endif; ?>
                <div style="font-size:12px; color:#6b7280; margin-top:6px;">Tarih:
                  <?= $date ?>
                </div>
              </div>
            <?php endif; ?>
            <div>
              <?php foreach (($data['threadMessages'] ?? []) as $m): ?>
                <div
                  class="bubble <?= $m['direction'] === 'out' ? 'me' : 'you' ?>" style="padding:10px 12px; margin:8px 0; border-radius:10px; max-width:80%; background: <?= $m['direction'] === 'out' ? '#d1f5d3' : '#e9eef5' ?>;">
                  <?= $m['html_body'] ? $m['html_body'] : nl2br(htmlspecialchars($m['text_body'] ?? '')) ?>
                  <div
                    style="font-size:11px;color:#64748b; margin-top:4px;"><?= htmlspecialchars($m['sent_at'] ?? $m['received_at'] ?? $m['created_at'] ?? '') ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div style="color:#64748b;">Bir konu seçin.</div>
          <?php endif; ?>
        </div>
      </section>
    </div>
    <script>
      document.getElementById('mailBoxSelect').addEventListener('change', function () {
location = '/index.php?action=' + this.value;
});
    </script>
  </body>
</html>

