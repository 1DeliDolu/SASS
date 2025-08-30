<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($data['title'] ?? 'Mesajlar') ?></title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div class="register-container">
            <h1>Mesajlar</h1>
            <p style="margin:0 0 8px;color:#555;">E‑posta adresiniz: <strong><?= htmlspecialchars($data['user']['mail'] ?? '') ?></strong></p>

            <div class="register-form">
                <div class="form-group">
                    <label>Konuşma Seç</label>
                    <select onchange="if(this.value){window.location='<?= '/index.php?action=messages&with=' ?>'+this.value;}">
                        <option value="">Bir kullanıcı seçin…</option>
                        <?php foreach (($data['users'] ?? []) as $u): ?>
                            <option value="<?= (int)$u['id'] ?>" <?= (isset($data['withUser']['id']) && $data['withUser']['id'] == $u['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($u['adi'] ?? '') . ' ' . ($u['soyadi'] ?? '') . ' (' . ($u['mail'] ?? '') . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (!empty($data['withUser'])): ?>
                    <div id="conv" style="border:1px solid #ddd;padding:12px;border-radius:6px;max-height:300px;overflow:auto;margin-bottom:12px;background:#fafafa;">
                        <?php
                            $partialData = ['user' => $data['user'], 'conversation' => $data['conversation']];
                            $data_backup = $data;
                            $data = $partialData;
                            include __DIR__ . '/partials/conversation.php';
                            $data = $data_backup;
                        ?>
                    </div>

                    <form method="post" action="/index.php?action=send_message&with=<?= (int)$data['withUser']['id'] ?>">
                        <input type="hidden" name="to_id" value="<?= (int)$data['withUser']['id'] ?>">
                        <div class="form-group">
                            <label>Mesaj</label>
                            <textarea name="message" rows="3" required placeholder="Mesajınızı yazın..."></textarea>
                        </div>
                        <button type="submit" class="btn-primary">Gönder</button>
                    </form>
                <?php else: ?>
                    <p>Mesajlaşmak için listeden bir kullanıcı seçin.</p>
                <?php endif; ?>
            </div>

            <div style="margin-top:12px; display:flex; gap:8px; align-items:center;">
                <?php if (!empty($data['withUser'])): ?>
                    <?php if (!empty($data['isArchived'])): ?>
                        <form method="post" action="/index.php?action=messages_unarchive&with=<?= (int)$data['withUser']['id'] ?>">
                            <input type="hidden" name="with_id" value="<?= (int)$data['withUser']['id'] ?>">
                            <button type="submit" class="btn-primary">Arşivden Çıkar</button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="/index.php?action=messages_archive">
                            <input type="hidden" name="with_id" value="<?= (int)$data['withUser']['id'] ?>">
                            <button type="submit" class="btn-primary">Konuşmayı Arşivle</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (empty($data['archived'])): ?>
                    <a class="btn-primary" href="/index.php?action=messages&archived=1">Arşivleri Göster</a>
                <?php else: ?>
                    <a class="btn-primary" href="/index.php?action=messages">Aktifleri Göster</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($data['withUser'])): ?>
        <script>
            (function(){
                const conv = document.getElementById('conv');
                let firstLoad = true;
                function scrollToBottom(force){
                    const nearBottom = conv.scrollHeight - conv.scrollTop - conv.clientHeight < 60;
                    if (force || nearBottom) {
                        conv.scrollTop = conv.scrollHeight;
                    }
                }
                scrollToBottom(true);
                async function tick(){
                    try {
                        const res = await fetch('/index.php?action=messages_partial&with=<?= (int)$data['withUser']['id'] ?>', {cache: 'no-store'});
                        if (!res.ok) return;
                        const html = await res.text();
                        const prevBottom = conv.scrollHeight - conv.scrollTop - conv.clientHeight;
                        conv.innerHTML = html;
                        if (firstLoad || prevBottom < 60) scrollToBottom(true);
                        firstLoad = false;
                    } catch (e) { /* ignore */ }
                }
                setInterval(tick, 5000);
            })();
        </script>
        <?php endif; ?>
    </body>
 </html>
