<?php $messages = $data['messages'] ?? []; ?>
<?php if (empty($messages)): ?>
  <p>Hiç mesaj yok.</p>
<?php else: ?>
  <?php foreach ($messages as $m): ?>
    <article class="item" onclick="location='/index.php?action=mail_thread&id=<?= (int)$m['thread_id'] ?>'" style="cursor:pointer; padding:8px; border-bottom:1px solid #eee;">
      <div><strong><?= htmlspecialchars($m['from_name'] ?: $m['from_email'] ?: 'Siz') ?></strong></div>
      <div><?= htmlspecialchars($m['subject'] ?: '(No subject)') ?></div>
      <div style="font-size:12px;color:#777;">
        <time><?= htmlspecialchars(($m['received_at'] ?? $m['sent_at'] ?? $m['created_at'])) ?></time>
      </div>
    </article>
  <?php endforeach; ?>
<?php endif; ?>

