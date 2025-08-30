<!doctype html>
<html lang="tr">
  <head>
    <meta charset="utf-8">
    <title>Yeni Mesaj</title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
    <?php include __DIR__ . '/../nav-bar.php'; ?>
    <div class="mail-app" style="display:grid; grid-template-columns: 240px 1fr; gap:16px; align-items:start; margin-top:16px;">
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

      <section class="register-form card" style="margin-top:0; background:#fff; border-radius:12px; box-shadow: 0 8px 24px rgba(44,62,80,.08); padding:16px;">
        <a class="btn-primary" href="/index.php?action=mail_inbox" style="padding:8px 12px; font-size:0.95rem; width:max-content;">Geri</a>

        <?php
        $prefillTo = isset($_GET['to']) ? trim((string) $_GET['to']) : '';
        $mode = isset($_GET['mode']) ? trim((string) $_GET['mode']) : '';
        $forwardTid = isset($_GET['forward_tid']) ? (int) $_GET['forward_tid'] : 0;
        $threadId = isset($_GET['thread_id']) ? (int) $_GET['thread_id'] : 0;
        $focus = isset($_GET['focus']) && $_GET['focus'] == '1';
        $quotedHtml = '';
        if ($mode === 'reply' || $mode === 'forward') {
          require_once __DIR__ . '/../../mail/EmailRepo.php';
          // we don't strictly need user filter for quoting; just fetch thread messages
          if ($forwardTid > 0) {
            $repo = new EmailRepo();
            $th = $repo->getThread($forwardTid, $_SESSION['user']['mail'] ?? '');
            $msgs = $th['messages'] ?? [];
            // quote last incoming if exists, else last any
            $lastIn = null;
            $lastAny = null;
            foreach ($msgs as $mm) {
              $lastAny = $mm;
              if (($mm['direction'] ?? '') === 'in') {
                $lastIn = $mm;
              }
            }
            $q = $lastIn ?: $lastAny;
            if ($q) {
              $body = $q['html_body'] ?: nl2br(htmlspecialchars($q['text_body'] ?? ''));
              $date = htmlspecialchars($q['sent_at'] ?? $q['received_at'] ?? $q['created_at'] ?? '');
              $from = htmlspecialchars($q['from_name'] ?? $q['from_email'] ?? '');
              $quotedHtml = '<div style="border-left:3px solid #e2e8f0;padding-left:8px;margin-top:8px">'
                . '<div style="font-size:12px;color:#64748b">' . $from . ' — ' . $date . '</div>'
                . '<div>' . $body . '</div>'
                . '</div>';
            }
          }
        }
        ?>
        <form method="post" action="/index.php?action=mail_send" enctype="multipart/form-data" onsubmit="sendForm(event)" style="margin-top:12px;">
          <div class="form-group">
            <label style="font-weight:600; margin-bottom:4px; display:block;">Alıcı(lar) (virgülle):</label>
            <div class="chip-input" style="position:relative;">
              <input id="toInput" name="to" autocomplete="off" placeholder="ör. user@example.com, ..." value="<?= htmlspecialchars($prefillTo) ?>" style="width:100%; box-sizing:border-box; padding:8px; border-radius:6px; border:1px solid #e2e8f0; font-size:1rem;">
              <div
                id="toDropdown" class="dropdown-modern" style="display:none; position:absolute; left:0; top:110%; min-width:100%; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 4px 16px rgba(44,62,80,.10); z-index:10; padding:4px 0; text-align:left;">
                <?php foreach (($data['users'] ?? []) as $u):
                  if (empty($u['mail']))
                    continue; ?>
                  <div class="to-item" data-email="<?= htmlspecialchars($u['mail']) ?>" style="display:inline; padding:0; margin-right:18px; background:none; border:none; box-shadow:none;">
                    <input type="checkbox" class="to-check" style="margin:0 4px 0 0;vertical-align:middle;">
                    <span
                      style="font-size:1rem; color:#222; vertical-align:middle; padding:0; margin:0; background:none; border:none;">
                      <?= htmlspecialchars(($u['adi'] ?? '') . ' ' . ($u['soyadi'] ?? '')) ?>
                      &lt;<?= htmlspecialchars($u['mail']) ?>&gt;</span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <small style="display:block; margin-top:4px; color:#64748b; text-align:left;">Alıcı alanına tıklayınca kullanıcı listesi açılır. Çoklu seçim otomatik virgülle ayrılır.</small>
          </div>

          <div class="form-group">
            <label>CC:</label>
            <div class="chip-input">
              <input id="ccInput" name="cc" autocomplete="off" placeholder="virgülle çoklu ekle...">
              <div
                id="ccDropdown" class="dropdown-modern" style="display:none; position:absolute; left:0; top:110%; min-width:100%; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 4px 16px rgba(44,62,80,.10); z-index:10; padding:4px 0; text-align:left;">
                <?php foreach (($data['users'] ?? []) as $u):
                  if (empty($u['mail']))
                    continue; ?>
                  <div class="to-item" data-email="<?= htmlspecialchars($u['mail']) ?>" style="display:inline; padding:0; margin-right:18px; background:none; border:none; box-shadow:none;">
                    <input type="checkbox" class="to-check" style="margin:0 4px 0 0;vertical-align:middle;">
                    <span
                      style="font-size:1rem; color:#222; vertical-align:middle; padding:0; margin:0; background:none; border:none;">
                      <?= htmlspecialchars(($u['adi'] ?? '') . ' ' . ($u['soyadi'] ?? '')) ?>
                      &lt;<?= htmlspecialchars($u['mail']) ?>&gt;</span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>BCC:</label>
            <div class="chip-input">
              <input id="bccInput" name="bcc" autocomplete="off" placeholder="virgülle çoklu ekle...">
              <div
                id="bccDropdown" class="dropdown-modern" style="display:none; position:absolute; left:0; top:110%; min-width:100%; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 4px 16px rgba(44,62,80,.10); z-index:10; padding:4px 0; text-align:left;">
                <?php foreach (($data['users'] ?? []) as $u):
                  if (empty($u['mail']))
                    continue; ?>
                  <div class="to-item" data-email="<?= htmlspecialchars($u['mail']) ?>" style="display:inline; padding:0; margin-right:18px; background:none; border:none; box-shadow:none;">
                    <input type="checkbox" class="to-check" style="margin:0 4px 0 0;vertical-align:middle;">
                    <span
                      style="font-size:1rem; color:#222; vertical-align:middle; padding:0; margin:0; background:none; border:none;">
                      <?= htmlspecialchars(($u['adi'] ?? '') . ' ' . ($u['soyadi'] ?? '')) ?>
                      &lt;<?= htmlspecialchars($u['mail']) ?>&gt;</span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Konu:
              <input name="subject" style="width:100%" value="<?= htmlspecialchars($_GET['subject'] ?? '') ?>"></label>
          </div>
          <div class="form-group">
            <label>Zamanlı gönder:
              <input type="datetime-local" name="scheduled_at"></label>
          </div>
          <div class="form-group">
            <label>Ekler:
              <input type="file" name="attachments[]" multiple></label>
          </div>
          <div class="form-group">
            <label>İçerik (HTML):
              <textarea id="htmlBody" name="html_body" rows="8" style="width:100%"><?php if ($mode === 'forward' && $quotedHtml) {
                echo "\n\n" . $quotedHtml;
              } ?></textarea>
            </label>
          </div>
          <button type="submit" class="btn-primary">Gönder</button>
        </form>
      </section>
    </div>
    <script>
      document.getElementById('mailBoxSelect').addEventListener('change', function(){
        location = '/index.php?action=' + this.value;
      });
            async function sendForm(e){
                    e.preventDefault();
                    const fd = new FormData(e.target);
                    const res = await fetch('/index.php?action=mail_send', {method:'POST', body: fd});
                    try{ const j = await res.json(); alert(j.ok?'Gönderildi/kuyruğa alındı':'Hata'); }catch(e){ alert('Hata'); }
                    location='/index.php?action=mail_inbox';
                  }
                  
                  // Reusable multi-select dropdown logic
                  function setupMultiSelect(inputId, dropdownId){
                    const input = document.getElementById(inputId);
                    const dropdown = document.getElementById(dropdownId);
                    function show(){ dropdown.style.display='block'; }
                    function hide(){ dropdown.style.display='none'; }
                    function parse(v){ return v.split(',').map(s=>s.trim()).filter(Boolean); }
                    function unique(arr){ return Array.from(new Set(arr)); }
                    function setVals(arr){ input.value = unique(arr).join(', ') + (arr.length? ', ': ''); }
                    function getVals(){ return parse(input.value); }
                  
                    document.addEventListener('click', (e)=>{ if (!dropdown.contains(e.target) && e.target !== input) hide(); });
                    dropdown.addEventListener('click', (e)=>{
                      const item = e.target.closest('.to-item'); if (!item) return;
                      const email = item.getAttribute('data-email');
                      const check = item.querySelector('.to-check');
                      check.checked = !check.checked;
                      const list = getVals();
                      const idx = list.indexOf(email);
                      if (check.checked) { if (idx === -1) list.push(email); }
                      else { if (idx !== -1) list.splice(idx,1); }
                      setVals(list); input.focus();
                    });
                    input.addEventListener('focus', show);
                    input.addEventListener('input', ()=>{
                      const tokens = input.value.split(',');
                      const last = tokens[tokens.length-1].trim().toLowerCase();
                      dropdown.querySelectorAll('.to-item').forEach(it=>{
                        const text = it.textContent.toLowerCase();
                        it.style.display = last==='' || text.includes(last) ? 'flex' : 'none';
                      });
                    });
                  }
                  
                  setupMultiSelect('toInput','toDropdown');
                  setupMultiSelect('ccInput','ccDropdown');
                  setupMultiSelect('bccInput','bccDropdown');
                  // Pre-check recipients based on initial value
                  function precheck(dropdownId, emails){
                    const set = new Set(emails.map(e=>e.toLowerCase()));
                    document.querySelectorAll('#'+dropdownId+' .to-item').forEach(it=>{
                      const email = it.getAttribute('data-email');
                      if (email && set.has(email.toLowerCase())) { it.querySelector('.to-check').checked = true; }
                    });
                  }
                  precheck('toDropdown', (document.getElementById('toInput').value||'').split(',').map(s=>s.trim()).filter(Boolean));
                  
                  // Auto focus content if requested
                  <?php if (!empty($focus)): ?>
                          document.getElementById('htmlBody').focus();
                  <?php endif; ?>
                  </script>
                  </body>
                  </html>

