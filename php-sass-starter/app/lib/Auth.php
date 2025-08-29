<?php
class Auth
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function user()
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function requireLogin(): void
    {
        self::start();
        if (!isset($_SESSION['user'])) {
            header('Location: /index.php?action=login&next=' . urlencode($_SERVER['REQUEST_URI'] ?? '/'));
            exit;
        }
    }

    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();
        $role = $_SESSION['user']['role'] ?? 'calisan';
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            echo 'Bu sayfaya erişim yetkiniz yok.';
            exit;
        }
    }
}
