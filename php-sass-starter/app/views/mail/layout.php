<?php $active = $_GET['action'] ?? 'mail_inbox'; ?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Mail Kutusu</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../nav-bar.php'; ?>
<div class="register-container" style="display:flex; gap:12px; align-items:flex-start;">
  <aside class="register-form" style="min-width:220px;">
    <a class="btn-primary" href="/index.php?action=mail_compose">Yeni Mesaj</a>
    <div style="margin-top:12px; display:flex; flex-direction:column; gap:6px;">
      <a href="/index.php?action=mail_inbox" class="btn-primary" style="opacity:<?= $active==='mail_inbox'?'1':'0.8' ?>">Gelen Kutusu</a>
      <a href="/index.php?action=mail_sent" class="btn-primary" style="opacity:<?= $active==='mail_sent'?'1':'0.8' ?>">Gönderilmiş</a>
      <a href="/index.php?action=mail_scheduled" class="btn-primary" style="opacity:<?= $active==='mail_scheduled'?'1':'0.8' ?>">Zamanlı</a>
    </div>
  </aside>
  <section class="register-form" style="flex:1;">
    <?php include __DIR__ . '/list.php'; ?>
  </section>
</div>
</body>
</html>

