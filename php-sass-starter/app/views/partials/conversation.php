<?php
// expects $data['user'], $data['conversation']
?>
<?php if (empty($data['conversation'])): ?>
    <p>Bu kullanıcıyla henüz mesaj yok.</p>
<?php else: ?>
    <?php foreach ($data['conversation'] as $m): ?>
        <?php $mine = ((int)$m['sender_id'] === (int)$data['user']['id']); ?>
        <div style="margin:6px 0; text-align: <?= $mine ? 'right' : 'left' ?>;">
            <div style="display:inline-block; padding:8px 10px; border-radius:8px; background: <?= $mine ? '#d1f5d3' : '#e9eef5' ?>; max-width:80%; position: relative;">
                <div style="font-size:12px;color:#555; margin-bottom:4px;">
                    <?php if (!$mine): ?>
                        <span><?= htmlspecialchars(($m['s_adi'] ?? '') . ' ' . ($m['s_soyadi'] ?? '')) ?></span>
                    <?php else: ?>
                        <span>Ben</span>
                    <?php endif; ?>
                    <span style="color:#999;"> • <?= htmlspecialchars($m['created_at'] ?? '') ?></span>
                </div>
                <div><?= nl2br(htmlspecialchars($m['content'] ?? '')) ?></div>
                <?php if ($mine): ?>
                    <div style="font-size:11px;color:#777;margin-top:4px; text-align:right;">
                        <?= !empty($m['read_at']) ? ('Okundu • ' . htmlspecialchars($m['read_at'])) : 'Gönderildi' ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

