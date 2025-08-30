<?php
class Captcha
{
    public static function recaptchaEnabled(): bool
    {
        $en = getenv('RECAPTCHA_ENABLED');
        $site = getenv('RECAPTCHA_SITE_KEY');
        $sec = getenv('RECAPTCHA_SECRET');
        return ($en && strtolower((string)$en) !== 'false' && $en !== '0') && $site && $sec;
    }

    public static function siteKey(): ?string
    {
        $k = getenv('RECAPTCHA_SITE_KEY');
        return $k ? (string)$k : null;
    }

    public static function verify(?string $response, ?string $remoteIp = null): bool
    {
        if (!self::recaptchaEnabled()) {
            return true; // disabled → always pass
        }
        $secret = (string)getenv('RECAPTCHA_SECRET');
        $resp = (string)($response ?? '');
        if ($resp === '') return false;
        $fields = http_build_query(['secret' => $secret, 'response' => $resp, 'remoteip' => $remoteIp]);
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        // Try cURL
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $fields,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
            ]);
            $out = curl_exec($ch);
            curl_close($ch);
        } else {
            $opts = ['http' => ['method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n", 'content' => $fields, 'timeout' => 5]];
            $context = stream_context_create($opts);
            $out = @file_get_contents($url, false, $context);
        }
        if (!$out) return false;
        $data = json_decode($out, true);
        return is_array($data) && !empty($data['success']);
    }
}

