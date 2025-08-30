<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Yeni Mesaj</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../nav-bar.php'; ?>
<div class="register-container">
  <a class="btn-primary" href="/index.php?action=mail_inbox">Geri</a>
  <section class="register-form" style="margin-top:12px;">
    <form method="post" action="/index.php?action=mail_send" enctype="multipart/form-data" onsubmit="sendForm(event)">
      <div class="form-group">
        <label>Alıcı(lar) (virgülle):</label>
        <div class="chip-input">
          <input id="toInput" name="to" autocomplete="off" placeholder="ör. user@example.com, ...">
          <div id="toDropdown" class="dropdown-modern">
            <?php foreach (($data['users'] ?? []) as $u): if (empty($u['mail'])) continue; ?>
              <div class="to-item" data-email="<?= htmlspecialchars($u['mail']) ?>">
                <input type="checkbox" class="to-check"> 
                <span><?= htmlspecialchars(($u['adi'] ?? '') . ' ' . ($u['soyadi'] ?? '')) ?> &lt;<?= htmlspecialchars($u['mail']) ?>&gt;</span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <small>Alıcı alanına tıklayınca kullanıcı listesi açılır. Çoklu seçim otomatik virgülle ayrılır.</small>
      </div>

      <div class="form-group">
        <label>CC:</label>
        <div class="chip-input">
          <input id="ccInput" name="cc" autocomplete="off" placeholder="virgülle çoklu ekle...">
          <div id="ccDropdown" class="dropdown-modern">
            <?php foreach (($data['users'] ?? []) as $u): if (empty($u['mail'])) continue; ?>
              <div class="to-item" data-email="<?= htmlspecialchars($u['mail']) ?>">
                <input type="checkbox" class="to-check"> 
                <span><?= htmlspecialchars(($u['adi'] ?? '') . ' ' . ($u['soyadi'] ?? '')) ?> &lt;<?= htmlspecialchars($u['mail']) ?>&gt;</span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>BCC:</label>
        <div class="chip-input">
          <input id="bccInput" name="bcc" autocomplete="off" placeholder="virgülle çoklu ekle...">
          <div id="bccDropdown" class="dropdown-modern">
            <?php foreach (($data['users'] ?? []) as $u): if (empty($u['mail'])) continue; ?>
              <div class="to-item" data-email="<?= htmlspecialchars($u['mail']) ?>">
                <input type="checkbox" class="to-check"> 
                <span><?= htmlspecialchars(($u['adi'] ?? '') . ' ' . ($u['soyadi'] ?? '')) ?> &lt;<?= htmlspecialchars($u['mail']) ?>&gt;</span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="form-group"><label>Konu: <input name="subject" style="width:100%"></label></div>
      <div class="form-group"><label>Zamanlı gönder: <input type="datetime-local" name="scheduled_at"></label></div>
      <div class="form-group"><label>Ekler: <input type="file" name="attachments[]" multiple></label></div>
      <div class="form-group"><label>İçerik (HTML): <textarea name="html_body" rows="8" style="width:100%"></textarea></label></div>
      <button type="submit" class="btn-primary">Gönder</button>
    </form>
  </section>
</div>
<script>
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
</script>
</body>
</html>
