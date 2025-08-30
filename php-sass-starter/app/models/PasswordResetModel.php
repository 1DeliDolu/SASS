<?php
class PasswordResetModel
{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../lib/Database.php';
        $this->pdo = Database::pdo();
    }

    public function create(int $userId, string $email, int $ttlSeconds = 900): array
    {
        $token = bin2hex(random_bytes(32)); // 64 hex chars
        $hash = hash('sha256', $token);
        $ttl = max(60, (int)$ttlSeconds);
        // Use DB time for expiry to avoid PHP/DB timezone mismatch
        $sql = 'INSERT INTO password_resets (user_id, email, token_hash, expires_at, created_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND), NOW())';
        $st = $this->pdo->prepare($sql);
        $st->execute([$userId, $email, $hash, $ttl]);
        $id = (int)$this->pdo->lastInsertId();
        // Optionally fetch computed expires_at from DB
        $row = $this->pdo->query('SELECT expires_at FROM password_resets WHERE id=' . (int)$id)->fetch();
        return ['id' => $id, 'token' => $token, 'expires_at' => ($row['expires_at'] ?? null)];
    }

    public function findValidByToken(string $token): ?array
    {
        $hash = hash('sha256', $token);
        $sql = 'SELECT * FROM password_resets WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW() LIMIT 1';
        $st = $this->pdo->prepare($sql);
        $st->execute([$hash]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function markUsed(int $id): bool
    {
        $st = $this->pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?');
        return $st->execute([$id]);
    }

    public function invalidateAllForUser(int $userId): void
    {
        $this->pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL')->execute([$userId]);
    }
}
