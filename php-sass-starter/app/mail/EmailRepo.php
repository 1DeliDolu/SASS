<?php
class EmailRepo
{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/EmailDB.php';
        $this->pdo = EmailDB::pdo();
    }

    public function listInbox(string $userEmail): array
    {
        $t = EmailDB::t('messages');
        // Gelen: alıcı listesinde benim mailim olacak ve gönderen ben olmayacağım.
        $sql = "SELECT * FROM `{$t}` WHERE ("
             . " JSON_CONTAINS(COALESCE(to_json,'[]'), JSON_QUOTE(?))"
             . " OR JSON_CONTAINS(COALESCE(cc_json,'[]'), JSON_QUOTE(?))"
             . " OR JSON_CONTAINS(COALESCE(bcc_json,'[]'), JSON_QUOTE(?))"
             . ") AND from_email IS NOT NULL AND from_email <> ?"
             . " ORDER BY COALESCE(received_at,created_at) DESC LIMIT 200";
        $m = [$userEmail, $userEmail, $userEmail, $userEmail];
        return ['messages' => $this->q($sql, $m)];
    }

    public function listSent(string $userEmail): array
    {
        $t = EmailDB::t('messages');
        // Gönderilmiş: sadece gönderen benim olduğum tüm çıkışlar (durum kısıtı olmadan göster)
        $sql = "SELECT * FROM `{$t}` WHERE from_email = ? ORDER BY COALESCE(sent_at,created_at) DESC LIMIT 200";
        return ['messages' => $this->q($sql, [$userEmail])];
    }

    public function listScheduled(string $userEmail, ?string $dateYmd = null): array
    {
        $t = EmailDB::t('messages');
        // Zamanlı: sadece benim oluşturduğum planlı çıkışlar (from_email = ben)
        if ($dateYmd && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateYmd)) {
            $sql = "SELECT * FROM `{$t}` WHERE status='scheduled' AND from_email=? AND DATE(scheduled_at)=? ORDER BY scheduled_at ASC LIMIT 200";
            return ['messages' => $this->q($sql, [$userEmail, $dateYmd])];
        }
        $sql = "SELECT * FROM `{$t}` WHERE status='scheduled' AND from_email=? ORDER BY scheduled_at ASC LIMIT 200";
        return ['messages' => $this->q($sql, [$userEmail])];
    }

    public function getThread(int $threadId, string $userEmail): array
    {
        $tt = EmailDB::t('threads');
        $tm = EmailDB::t('messages');
        // Authorize: ensure user participates in this thread
        $auth = $this->q1(
            "SELECT 1 FROM `{$tm}` WHERE thread_id=? AND (from_email=? OR JSON_CONTAINS(COALESCE(to_json,'[]'), JSON_QUOTE(?)) OR JSON_CONTAINS(COALESCE(cc_json,'[]'), JSON_QUOTE(?)) OR JSON_CONTAINS(COALESCE(bcc_json,'[]'), JSON_QUOTE(?))) LIMIT 1",
            [$threadId, $userEmail, $userEmail, $userEmail, $userEmail]
        );
        if (!$auth) {
            return ['thread' => null, 'messages' => []];
        }
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
        // resolve sender from session if available
        $fromEmail = getenv('SMTP_FROM') ?: 'no-reply@example.com';
        $fromName  = getenv('SMTP_FROM_NAME') ?: 'App Mailer';
        if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
        if (!empty($_SESSION['user']['mail'])) {
            $fromEmail = $_SESSION['user']['mail'];
            $fromName = trim(($_SESSION['user']['adi'] ?? '') . ' ' . ($_SESSION['user']['soyadi'] ?? '')) ?: $fromName;
        }

        $threadId = $this->ensureThread($subject);
        $tm = EmailDB::t('messages');
        $st = $this->pdo->prepare("INSERT INTO `{$tm}`(thread_id,direction,status,from_email,from_name,to_json,cc_json,bcc_json,subject,html_body,text_body,scheduled_at,created_at,updated_at) VALUES(?, 'out', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $st->execute([$threadId, $scheduled_at ? 'scheduled' : 'queued', $fromEmail, $fromName, json_encode($to), json_encode($cc), json_encode($bcc), $subject, $html, $text, $scheduled_at]);
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
                'from_email' => $msg['from_email'] ?? null,
                'from_name' => $msg['from_name'] ?? null,
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
