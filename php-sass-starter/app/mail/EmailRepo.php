<?php
class EmailRepo
{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/EmailDB.php';
        $this->pdo = EmailDB::pdo();
    }

    public function listInbox(): array
    {
        $t = EmailDB::t('messages');
        return ['messages' => $this->q("SELECT * FROM `{$t}` WHERE direction='in' AND status='received' ORDER BY COALESCE(received_at,created_at) DESC LIMIT 100")];
    }

    public function listSent(): array
    {
        $t = EmailDB::t('messages');
        return ['messages' => $this->q("SELECT * FROM `{$t}` WHERE direction='out' AND status='sent' ORDER BY COALESCE(sent_at,created_at) DESC LIMIT 100")];
    }

    public function listScheduled(): array
    {
        $t = EmailDB::t('messages');
        return ['messages' => $this->q("SELECT * FROM `{$t}` WHERE status='scheduled' ORDER BY scheduled_at ASC LIMIT 100")];
    }

    public function getThread(int $threadId): array
    {
        $tt = EmailDB::t('threads');
        $tm = EmailDB::t('messages');
        $t = $this->q1("SELECT * FROM `{$tt}` WHERE id=?", [$threadId]);
        $msgs = $this->q("SELECT * FROM `{$tm}` WHERE thread_id=? ORDER BY created_at ASC", [$threadId]);
        return ['thread' => $t, 'messages' => $msgs];
    }

    public function send(array $post, array $files): array
    {
        $to = array_filter(array_map('trim', explode(',', $post['to'] ?? '')));
        $cc = array_filter(array_map('trim', explode(',', $post['cc'] ?? '')));
        $bcc = array_filter(array_map('trim', explode(',', $post['bcc'] ?? '')));
        $subject = $post['subject'] ?? '(No subject)';
        $html = $post['html_body'] ?? null; $text = $post['text_body'] ?? null;
        $scheduled_at = !empty($post['scheduled_at']) ? date('Y-m-d H:i:s', strtotime($post['scheduled_at'])) : null;

        $threadId = $this->ensureThread($subject);
        $tm = EmailDB::t('messages');
        $st = $this->pdo->prepare("INSERT INTO `{$tm}`(thread_id,direction,status,to_json,cc_json,bcc_json,subject,html_body,text_body,scheduled_at,created_at,updated_at) VALUES(?, 'out', ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $st->execute([$threadId, $scheduled_at ? 'scheduled' : 'queued', json_encode($to), json_encode($cc), json_encode($bcc), $subject, $html, $text, $scheduled_at]);
        $msgId = (int)$this->pdo->lastInsertId();

        if (!empty($files['attachments']['name'][0])) {
            @mkdir(__DIR__ . '/../../storage', 0777, true);
            for ($i=0; $i<count($files['attachments']['name']); $i++) {
                if ($files['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmp = $files['attachments']['tmp_name'][$i];
                    $name = $files['attachments']['name'][$i];
                    $mime = $files['attachments']['type'][$i];
                    $dest = 'storage/' . uniqid('', true) . '_' . $name;
                    @rename($tmp, __DIR__ . '/../../' . $dest);
                    $ta = EmailDB::t('attachments');
                    $this->pdo->prepare("INSERT INTO `{$ta}`(message_id,file_name,mime,size,storage_path) VALUES(?,?,?,?,?)")
                        ->execute([$msgId, $name, $mime, @filesize(__DIR__ . '/../../' . $dest) ?: 0, $dest]);
                }
            }
        }

        if (!$scheduled_at) {
            $this->dispatchSend($msgId);
        }
        return ['ok' => true, 'id' => $msgId];
    }

    public function dispatchSend(int $messageId): void
    {
        require_once __DIR__ . '/../lib/Mailer.php';
        $tm = EmailDB::t('messages');
        $msg = $this->q1("SELECT * FROM `{$tm}` WHERE id=?", [$messageId]);
        if (!$msg) return;
        $ta = EmailDB::t('attachments');
        $atts = $this->q("SELECT * FROM `{$ta}` WHERE message_id=?", [$messageId]);
        try {
            $to = json_decode($msg['to_json'] ?? '[]', true);
            $cc = json_decode($msg['cc_json'] ?? '[]', true);
            $bcc = json_decode($msg['bcc_json'] ?? '[]', true);
            $subject = $msg['subject'] ?: '(No subject)';
            $html = ($msg['html_body'] ?: '') . $this->attachmentsHtml($atts);
            $text = $msg['text_body'] ?: strip_tags($html);
            $files = array_map(function($a){
                $path = __DIR__ . '/../../' . $a['storage_path'];
                return ['tmp_name' => $path, 'name' => $a['file_name']];
            }, $atts);
            $ok = Mailer::sendMessage([
                'to' => $to,
                'cc' => $cc,
                'bcc' => $bcc,
                'subject' => $subject,
                'html_body' => $html,
                'text_body' => $text,
            ], $files);
            if ($ok) {
                $this->pdo->prepare("UPDATE `{$tm}` SET status='sent', sent_at=NOW(), updated_at=NOW() WHERE id=?")->execute([$messageId]);
            } else {
                $this->pdo->prepare("UPDATE `{$tm}` SET status='failed', fail_reason=?, updated_at=NOW() WHERE id=?")->execute(['send failed', $messageId]);
            }
        } catch (Throwable $e) {
            $this->pdo->prepare("UPDATE `{$tm}` SET status='failed', fail_reason=?, updated_at=NOW() WHERE id=?")->execute([$e->getMessage(), $messageId]);
        }
    }

    public function runScheduler(): void
    {
        $tm = EmailDB::t('messages');
        $rows = $this->q("SELECT id FROM `{$tm}` WHERE status='scheduled' AND scheduled_at IS NOT NULL AND scheduled_at <= NOW() ORDER BY scheduled_at ASC LIMIT 100");
        foreach ($rows as $r) {
            $this->dispatchSend((int)$r['id']);
        }
    }

    public function ensureThread(string $subject): int
    {
        $tt = EmailDB::t('threads');
        $t = $this->q1("SELECT id FROM `{$tt}` WHERE subject=? LIMIT 1", [$subject]);
        if ($t) return (int)$t['id'];
        $this->pdo->prepare("INSERT INTO `{$tt}`(subject,last_message_at,created_at,updated_at) VALUES(?,NOW(),NOW(),NOW())")->execute([$subject]);
        return (int)$this->pdo->lastInsertId();
    }

    private function attachmentsHtml(array $atts): string
    {
        if (empty($atts)) return '';
        $out = '<hr><p>Ekler:</p><ul>';
        foreach ($atts as $a) {
            $out .= '<li>' . htmlspecialchars($a['file_name']) . ' (' . htmlspecialchars($a['mime']) . ')</li>';
        }
        return $out . '</ul>';
    }

    private function q(string $sql, array $p = []): array { $st = $this->pdo->prepare($sql); $st->execute($p); return $st->fetchAll(); }
    private function q1(string $sql, array $p = []) { $st = $this->pdo->prepare($sql); $st->execute($p); return $st->fetch(); }
}
