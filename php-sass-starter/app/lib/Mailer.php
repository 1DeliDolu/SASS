<?php
class Mailer
{
    private static function enabled(): bool
    {
        $v = getenv('MAIL_ENABLED');
        if ($v === false) return false;
        $v = strtolower((string)$v);
        return in_array($v, ['1','true','yes','on'], true);
    }

    private static function from(): array
    {
        $from = getenv('SMTP_FROM') ?: 'no-reply@example.com';
        $name = getenv('SMTP_FROM_NAME') ?: 'SASS MVC';
        return [$from, $name];
    }

    public static function send(string $to, string $subject, string $htmlBody, ?string $textBody = null, array $headers = []): bool
    {
        // Always log to file for traceability
        self::log($to, $subject, $htmlBody, $textBody);

        if (!self::enabled()) {
            return true; // consider as success in dev
        }

        // Basic mail() implementation as fallback (no external libs)
        [$from, $name] = self::from();
        $boundary = uniqid('np');
        $h = [];
        $h[] = 'MIME-Version: 1.0';
        $h[] = 'From: ' . sprintf('"%s" <%s>', self::encodeHeader($name), $from);
        $h[] = 'Content-Type: multipart/alternative; boundary=' . $boundary;
        foreach ($headers as $k => $v) {
            $h[] = $k . ': ' . $v;
        }
        $body  = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $body .= ($textBody ?: strip_tags($htmlBody)) . "\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $body .= $htmlBody . "\r\n";
        $body .= "--$boundary--";

        // Suppress warnings from mail() in dev
        $ok = @mail($to, self::encodeHeader($subject), $body, implode("\r\n", $h));
        return (bool)$ok;
    }

    public static function sendMany(array $recipients, string $subject, string $htmlBody, ?string $textBody = null): void
    {
        foreach ($recipients as $to) {
            if (!is_string($to) || trim($to) === '') continue;
            self::send($to, $subject, $htmlBody, $textBody);
        }
    }

    private static function log(string $to, string $subject, string $html, ?string $text): void
    {
        $dir = __DIR__ . '/../../logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $line = "[" . date('Y-m-d H:i:s') . "] TO=$to | SUBJ=" . str_replace(["\r","\n"], ' ', $subject) . "\n";
        $line .= "HTML:\n" . $html . "\n";
        if ($text !== null) {
            $line .= "TEXT:\n" . $text . "\n";
        }
        @file_put_contents($dir . '/mail.log', $line . "\n---\n", FILE_APPEND);
    }

    private static function encodeHeader(string $v): string
    {
        return '=?UTF-8?B?' . base64_encode($v) . '?=';
    }
}

