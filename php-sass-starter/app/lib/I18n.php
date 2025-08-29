<?php
class I18n
{
    public static function lang(): string
    {
        $env = getenv('APP_LANG');
        if (is_string($env) && $env !== '') {
            $env = strtolower($env);
            if (str_starts_with($env, 'de')) return 'de';
            if (str_starts_with($env, 'tr')) return 'tr';
        }
        $al = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $al = strtolower((string)$al);
        if (strpos($al, 'de') !== false) return 'de';
        if (strpos($al, 'tr') !== false) return 'tr';
        return 'tr';
    }

    public static function statusLabel(string $value, ?string $lang = null): string
    {
        $lang = $lang ?: self::lang();
        $map = [
            'yeni'  => ['tr' => 'Yeni',   'de' => 'Neu'],
            'devam' => ['tr' => 'Devam',  'de' => 'Läuft'],
            'tamam' => ['tr' => 'Tamam',  'de' => 'Fertig'],
            'iptal' => ['tr' => 'İptal',  'de' => 'Abgebrochen'],
        ];
        $key = strtolower($value);
        if (isset($map[$key])) {
            return $map[$key][$lang] ?? $map[$key]['tr'];
        }
        return $value;
    }
}

