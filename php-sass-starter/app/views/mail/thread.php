<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Konu</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../nav-bar.php'; ?>
<div class="register-container">
  <a class="btn-primary" href="/index.php?action=mail_inbox">Geri</a>
  <div class="register-form" style="margin-top:12px;">
    <?php foreach(($data['messages'] ?? []) as $m): ?>
      <div class="bubble <?= $m['direction']==='out' ? 'me' : 'you' ?>" style="padding:8px; margin:6px 0; border-radius:8px; background: <?= $m['direction']==='out' ? '#d1f5d3' : '#e9eef5' ?>;">
        <?= $m['html_body'] ? $m['html_body'] : nl2br(htmlspecialchars($m['text_body'] ?? '')) ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>

