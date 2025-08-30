<?php
class Csrf
{
    private static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = [];
        }
    }

    public static function token(string $key): string
    {
        self::ensureSession();
        if (!isset($_SESSION['csrf'][$key])) {
            $_SESSION['csrf'][$key] = bin2hex(random_bytes(16));
        }
        return $_SESSION['csrf'][$key];
    }

    public static function validate(string $key, ?string $token): bool
    {
        self::ensureSession();
        if (!isset($_SESSION['csrf'][$key]) || !is_string($token)) {
            return false;
        }
        $ok = hash_equals($_SESSION['csrf'][$key], $token);
        // rotate token on successful validation to mitigate replay
        if ($ok) {
            unset($_SESSION['csrf'][$key]);
        }
        return $ok;
    }
}

