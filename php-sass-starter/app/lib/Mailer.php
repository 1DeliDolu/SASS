<?php
class Mailer
{
    public static function enabled(): bool
    {
        $v = getenv('MAIL_ENABLED');
        return $v !== false && strtolower((string)$v) !== 'false' && $v !== '0' && $v !== '';
    }

    // Backward compatible simple send (single recipient)
    public static function send(string $to, string $subject, ?string $htmlBody, ?string $textBody = null): bool
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            self::log("Invalid recipient email: {$to}");
            return false;
        }
        return self::sendMessage([
            'to' => [$to],
            'cc' => [],
            'bcc' => [],
            'subject' => $subject,
            'html_body' => $htmlBody,
            'text_body' => $textBody,
        ]);
    }

    // Advanced send compatible with FEHLER.md MessageRepo usage
    // $msg: ['to'=>[], 'cc'=>[], 'bcc'=>[], 'subject'=>, 'html_body'=>, 'text_body'=>]
    // $attachments: [['tmp_name'=>, 'name'=>], ...]
    public static function sendMessage(array $msg, array $attachments = []): bool
    {
        $toList = array_values(array_filter($msg['to'] ?? [], fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
        $ccList = array_values(array_filter($msg['cc'] ?? [], fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
        $bccList = array_values(array_filter($msg['bcc'] ?? [], fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
        if (empty($toList)) {
            self::log('No valid recipients');
            return false;
        }
        $from = getenv('SMTP_FROM') ?: 'no-reply@example.com';
        $fromName = getenv('SMTP_FROM_NAME') ?: 'App Mailer';
        $host = getenv('SMTP_HOST') ?: 'localhost';
        $port = (int)(getenv('SMTP_PORT') ?: 25);
        $user = getenv('SMTP_USER') ?: '';
        $pass = getenv('SMTP_PASS') !== false ? getenv('SMTP_PASS') : '';
        $secure = strtolower((string)(getenv('SMTP_SECURE') ?: ''));

        // Use PHPMailer if available
        if (class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
            if (!self::enabled()) {
                self::log('[DRY-RUN][PHPMailer] To=' . implode(',', $toList) . '; Subject=' . ($msg['subject'] ?? ''));
                return true;
            }
            try {
                $m = new PHPMailer\PHPMailer\PHPMailer(true);
                $m->isSMTP();
                $m->Host = $host; $m->Port = $port;
                if ($secure) { $m->SMTPSecure = $secure; }
                if ($user !== '') { $m->SMTPAuth = true; $m->Username = $user; $m->Password = $pass; }
                $fromOverride = $msg['from_email'] ?? null; $fromNameOverride = $msg['from_name'] ?? null;
                if ($fromOverride && filter_var($fromOverride, FILTER_VALIDATE_EMAIL)) {
                    $m->setFrom($fromOverride, $fromNameOverride ?: $fromName);
                } else {
                    $m->setFrom($from, $fromName);
                }
                foreach ($toList as $addr) { $m->addAddress($addr); }
                foreach ($ccList as $addr) { $m->addCC($addr); }
                foreach ($bccList as $addr) { $m->addBCC($addr); }
                $m->Subject = $msg['subject'] ?? '(No subject)';
                $htmlBody = (string)($msg['html_body'] ?? '');
                $textBody = (string)($msg['text_body'] ?? '');
                if ($htmlBody !== '') { $m->isHTML(true); $m->Body = $htmlBody; $m->AltBody = $textBody ?: strip_tags($htmlBody); }
                else { $m->Body = $textBody; }
                foreach ($attachments as $f) {
                    if (!empty($f['tmp_name'])) {
                        $m->addAttachment($f['tmp_name'], $f['name'] ?? basename($f['tmp_name']));
                    }
                }
                $m->send();
                return true;
            } catch (Throwable $e) {
                self::log('PHPMailer error: ' . $e->getMessage());
                return false;
            }
        }

        // Fallback to raw SMTP for first recipient only (no CC/BCC/attachments)
        $subject = $msg['subject'] ?? '';
        $htmlBody = $msg['html_body'] ?? null;
        $textBody = $msg['text_body'] ?? null;
        $to = $toList[0];

        // Build message
        $boundary = 'bnd_' . bin2hex(random_bytes(8));
        $headers = [];
        $headers[] = 'From: ' . self::formatAddress($from, $fromName);
        $headers[] = 'To: ' . self::formatAddress($to, null);
        $headers[] = 'Subject: ' . self::encodeHeader($subject ?: '(No subject)');
        $headers[] = 'MIME-Version: 1.0';

        $hasHtml = $htmlBody !== null && $htmlBody !== '';
        $hasText = $textBody !== null && $textBody !== '';
        if ($hasHtml && !$hasText) { $textBody = strip_tags((string)$htmlBody); $hasText = true; }
        $body = '';
        if ($hasHtml && $hasText) {
            $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
            $body = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n" . self::normalizeEols((string)$textBody) . "\r\n"
                  . "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n" . self::normalizeEols((string)$htmlBody) . "\r\n"
                  . "--{$boundary}--\r\n";
        } elseif ($hasHtml) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $body = self::normalizeEols((string)$htmlBody) . "\r\n";
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
            $body = self::normalizeEols((string)($textBody ?? '')) . "\r\n";
        }

        if (!self::enabled()) { self::log("[DRY-RUN] To={$to}; Subject={$subject}\n{$body}"); return true; }
        $ok = self::smtpSend($host, $port, $secure, $user, $pass, $from, $to, implode("\r\n", $headers) . "\r\n\r\n" . $body);
        if (!$ok) { self::log('SMTP send failed for ' . $to . ' subject=' . $subject); }
        return $ok;
    }

    private static function smtpSend(string $host, int $port, string $secure, string $user, string $pass, string $from, string $to, string $data): bool
    {
        $remote = ($secure === 'ssl') ? "ssl://{$host}:{$port}" : "tcp://{$host}:{$port}";
        $fp = @stream_socket_client($remote, $errno, $errstr, 10, STREAM_CLIENT_CONNECT);
        if (!$fp) {
            self::log("SMTP connect error: {$errno} {$errstr}");
            return false;
        }
        $read = function () use ($fp) {
            $resp = '';
            while (!feof($fp)) {
                $line = fgets($fp, 515);
                if ($line === false) break;
                $resp .= $line;
                if (strlen($line) < 4 || $line[3] !== '-') break; // last line
                if (!isset($line[3])) break;
            }
            return $resp;
        };
        $cmd = function ($c) use ($fp, $read) {
            fwrite($fp, $c . "\r\n");
            return $read();
        };
        $resp = $read();
        if (strpos($resp, '220') !== 0) { fclose($fp); return false; }
        $resp = $cmd('EHLO localhost');
        if ($secure === 'tls' && stripos($resp, 'STARTTLS') !== false) {
            $resp = $cmd('STARTTLS');
            if (strpos($resp, '220') !== 0) { fclose($fp); return false; }
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) { fclose($fp); return false; }
            $resp = $cmd('EHLO localhost');
        }
        if ($user !== '') {
            $resp = $cmd('AUTH LOGIN');
            if (strpos($resp, '334') !== 0) { fclose($fp); return false; }
            $resp = $cmd(base64_encode($user));
            if (strpos($resp, '334') !== 0) { fclose($fp); return false; }
            $resp = $cmd(base64_encode($pass));
            if (strpos($resp, '235') !== 0) { fclose($fp); return false; }
        }
        $resp = $cmd('MAIL FROM:<' . $from . '>');
        if (strpos($resp, '250') !== 0) { fclose($fp); return false; }
        $resp = $cmd('RCPT TO:<' . $to . '>');
        if (strpos($resp, '250') !== 0 && strpos($resp, '251') !== 0) { fclose($fp); return false; }
        $resp = $cmd('DATA');
        if (strpos($resp, '354') !== 0) { fclose($fp); return false; }
        // Dot-stuff and send
        $payload = preg_replace('/\r?\n\./', "\r\n..", $data);
        fwrite($fp, $payload . "\r\n.\r\n");
        $resp = $read();
        $cmd('QUIT');
        fclose($fp);
        return strpos($resp, '250') === 0;
    }

    private static function formatAddress(string $email, ?string $name): string
    {
        if ($name === null || $name === '') return $email;
        $name = trim($name);
        $name = preg_replace('/[\r\n]+/', ' ', $name);
        if (preg_match('/[\x80-\xFF]/', $name)) {
            $name = '=?UTF-8?B?' . base64_encode($name) . '?=';
        }
        return sprintf('%s <%s>', $name, $email);
    }

    private static function encodeHeader(string $text): string
    {
        if (!preg_match('/[\x80-\xFF]/', $text)) return $text;
        return '=?UTF-8?B?' . base64_encode($text) . '?=';
    }

    private static function normalizeEols(string $s): string
    {
        $s = str_replace(["\r\n", "\r"], "\n", $s);
        return str_replace("\n", "\r\n", $s);
    }

    private static function log(string $msg): void
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n";
        $logDir = __DIR__ . '/../../logs';
        @mkdir($logDir, 0777, true);
        @file_put_contents($logDir . '/mail.log', $line, FILE_APPEND);
    }
}
