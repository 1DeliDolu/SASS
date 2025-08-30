<?php
class ImapSync
{
    public static function run(): void
    {
        // Requires php-imap/php-imap via Composer; fallback to no-op if not available
        if (!class_exists('PhpImap\\Mailbox')) {
            // Log and return
            @file_put_contents(__DIR__ . '/../../logs/mail.log', '['.date('Y-m-d H:i:s').'] IMAP library not installed; skipping sync' . "\n", FILE_APPEND);
            return;
        }
        $host = getenv('IMAP_HOST');
        $port = getenv('IMAP_PORT') ?: 993;
        $enc  = getenv('IMAP_ENCRYPTION') ?: 'ssl';
        $user = getenv('IMAP_USER') ?: '';
        $pass = getenv('IMAP_PASS') ?: '';
        if (!$host || !$user || !$pass) return;
        $dsn = '{'.$host.':'.$port.'/imap/'.$enc.'}INBOX';
        $storage = __DIR__ . '/../../storage/imap';
        @mkdir($storage, 0777, true);
        $mailbox = new PhpImap\Mailbox($dsn, $user, $pass, $storage, 'UTF-8');
        $ids = $mailbox->searchMailbox('UNSEEN SINCE "'.date('d-M-Y', strtotime('-7 days')).'"');
        if (!$ids) return;
        require_once __DIR__ . '/EmailRepo.php';
        $repo = new EmailRepo();
        $tm = EmailDB::t('messages');
        $tt = EmailDB::t('threads');
        $ta = EmailDB::t('attachments');
        foreach ($ids as $id) {
            $m = $mailbox->getMail($id, false);
            $subject = $m->subject ?: '(No subject)';
            $threadId = $repo->ensureThread($subject);
            $pdo = EmailDB::pdo();
            $pdo->prepare("INSERT INTO `{$tm}` (thread_id,direction,status,from_email,from_name,to_json,subject,text_body,html_body,received_at,message_id,created_at,updated_at) VALUES(?, 'in','received', ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())")
                ->execute([$threadId, $m->fromAddress, $m->fromName, json_encode($m->to), $subject, $m->textPlain, $m->textHtml, $m->date, $m->messageId]);
            $msgId = (int)$pdo->lastInsertId();
            foreach ($m->getAttachments() as $att) {
                $path = 'storage/' . uniqid('', true) . '_' . $att->name;
                @file_put_contents(__DIR__ . '/../../' . $path, $att->getContents());
                $pdo->prepare("INSERT INTO `{$ta}`(message_id,file_name,mime,size,storage_path,cid) VALUES(?,?,?,?,?,?)")
                    ->execute([$msgId, $att->name, $att->mimeType, $att->size, $path, $att->contentId]);
            }
            $pdo->prepare("UPDATE `{$tt}` SET last_message_at=NOW(), updated_at=NOW() WHERE id=?")->execute([$threadId]);
        }
    }
}
