<?php
class ThrottleModel
{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../lib/Database.php';
        $this->pdo = Database::pdo();
    }

    public function hit(string $action, string $key, ?string $ip): void
    {
        $st = $this->pdo->prepare('INSERT INTO throttle (`action`,`key`,`ip`,`created_at`) VALUES (?,?,?,NOW())');
        $st->execute([$action, $key, $ip]);
    }

    public function tooMany(string $action, string $key, int $max, int $seconds): bool
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) AS c FROM throttle WHERE `action`=? AND `key`=? AND created_at > (NOW() - INTERVAL ? SECOND)');
        $st->execute([$action, $key, $seconds]);
        $row = $st->fetch();
        return ((int)($row['c'] ?? 0)) >= $max;
    }

    public function tooManyByIp(string $action, ?string $ip, int $max, int $seconds): bool
    {
        if (!$ip) return false;
        $st = $this->pdo->prepare('SELECT COUNT(*) AS c FROM throttle WHERE `action`=? AND `ip`=? AND created_at > (NOW() - INTERVAL ? SECOND)');
        $st->execute([$action, $ip, $seconds]);
        $row = $st->fetch();
        return ((int)($row['c'] ?? 0)) >= $max;
    }
}

