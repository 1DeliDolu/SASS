<?php
use PhpImap\Mailbox;

class ImapSync
{
    public function syncInbox(?string $onlyToEmail = null, int $limit = 50): int
    {
        $host = getenv('IMAP_HOST');
        $port = (int)(getenv('IMAP_PORT') ?: 993);
        $enc  = getenv('IMAP_ENCRYPTION') ?: 'ssl';
        $user = getenv('IMAP_USER');
        $pass = getenv('IMAP_PASS');
        if (!$host || !$user || $pass === false) {
            return 0; // not configured
        }
        $mailboxPath = sprintf('{%s:%d/%s}INBOX', $host, $port, $enc ?: 'novalidate-cert');
        $imported = 0;
        try {
            if (class_exists('PhpImap\\Mailbox')) {
                $mb = new Mailbox($mailboxPath, $user, $pass, __DIR__ . '/../../storage/imap', 'UTF-8');
                $mids = $mb->searchMailbox('UNSEEN');
                if (!is_array($mids)) $mids = [];
                $mids = array_slice($mids, 0, max(1, $limit));
                require_once __DIR__ . '/EmailRepo.php';
                $repo = new EmailRepo();
                foreach ($mids as $mid) {
                    $mail = $mb->getMail($mid);
                    $toEmail = $onlyToEmail ?: ($mail->to ? array_key_first($mail->to) : ($mail->toString ?? $user));
                    // Basic filtering by recipient if requested
                    if ($onlyToEmail && stripos((string)$toEmail, $onlyToEmail) === false) {
                        continue;
                    }
                    $fromEmail = $mail->fromAddress ?: 'unknown@example.com';
                    $fromName  = $mail->fromName ?: '';
                    $subject   = $mail->subject ?: '(No subject)';
                    $html      = $mail->textHtml ?: null;
                    $text      = $mail->textPlain ?: null;
                    $repo->logInbound((string)$toEmail, $subject, $html, $text, $fromEmail, $fromName);
                    $imported++;
                }
            } elseif (function_exists('imap_open')) {
                $inbox = @imap_open($mailboxPath, $user, $pass);
                if (!$inbox) return 0;
                $emails = imap_search($inbox, 'UNSEEN') ?: [];
                $emails = array_slice($emails, 0, max(1, $limit));
                require_once __DIR__ . '/EmailRepo.php';
                $repo = new EmailRepo();
                foreach ($emails as $num) {
                    $overview = imap_fetch_overview($inbox, $num, 0)[0] ?? null;
                    $structure = imap_fetchstructure($inbox, $num);
                    $body = imap_fetchbody($inbox, $num, 1.2);
                    if ($body === '') $body = imap_fetchbody($inbox, $num, 1);
                    $html = null; $text = null;
                    if ($structure && isset($structure->subtype)) {
                        $sub = strtolower((string)$structure->subtype);
                        if ($sub === 'html') $html = imap_qprint($body);
                        else $text = imap_qprint($body);
                    } else {
                        $text = imap_qprint($body);
                    }
                    $from = $overview->from ?? '';
                    $subject = $overview->subject ?? '(No subject)';
                    // crude parsing for from email
                    if (preg_match('/<([^>]+)>/', $from, $m)) { $fromEmail = $m[1]; $fromName = trim(str_replace($m[0], '', $from)); }
                    else { $fromEmail = $from; $fromName = ''; }
                    $toEmail = $onlyToEmail ?: ($user);
                    if ($onlyToEmail && stripos((string)$toEmail, $onlyToEmail) === false) continue;
                    $repo->logInbound((string)$toEmail, $subject, $html, $text, (string)$fromEmail, (string)$fromName);
                    $imported++;
                }
                imap_close($inbox);
            }
        } catch (Throwable $e) {
            // swallow errors in sync
        }
        return $imported;
    }
}

