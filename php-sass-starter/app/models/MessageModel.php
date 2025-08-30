<?php
class MessageModel
{
    private $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../lib/Database.php';
        $this->pdo = Database::pdo();
    }

    public function sendMessage(int $fromId, int $toId, string $content): bool
    {
        $content = trim($content);
        if ($content === '' || $fromId <= 0 || $toId <= 0 || $fromId === $toId) {
            return false;
        }
        $sql = 'INSERT INTO messages (sender_id, receiver_id, content, created_at) VALUES (?, ?, ?, NOW())';
        $st = $this->pdo->prepare($sql);
        return $st->execute([$fromId, $toId, $content]);
    }

    public function getConversation(int $userId, int $withId, int $limit = 50): array
    {
        $sql = 'SELECT m.*, s.adi as s_adi, s.soyadi as s_soyadi, r.adi as r_adi, r.soyadi as r_soyadi '
             . 'FROM messages m '
             . 'JOIN users s ON s.id = m.sender_id '
             . 'JOIN users r ON r.id = m.receiver_id '
             . 'WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) '
             . 'ORDER BY m.created_at ASC '
             . 'LIMIT ' . intval($limit);
        $st = $this->pdo->prepare($sql);
        $st->execute([$userId, $withId, $withId, $userId]);
        return $st->fetchAll();
    }

    public function getPartners(int $userId): array
    {
        // Users who have messaged with the current user and are not archived by this user
        $sql = 'SELECT DISTINCT u.* FROM users u '
             . 'JOIN messages m ON (m.sender_id = u.id AND m.receiver_id = ?) OR (m.receiver_id = u.id AND m.sender_id = ?) '
             . 'LEFT JOIN message_archives a ON a.user_id = ? AND a.with_user_id = u.id '
             . 'WHERE u.id <> ? AND a.user_id IS NULL '
             . 'ORDER BY u.adi, u.soyadi';
        $st = $this->pdo->prepare($sql);
        $st->execute([$userId, $userId, $userId, $userId]);
        return $st->fetchAll();
    }

    public function getArchivedPartners(int $userId): array
    {
        $sql = 'SELECT u.* FROM users u '
             . 'JOIN message_archives a ON a.with_user_id = u.id AND a.user_id = ? '
             . 'ORDER BY u.adi, u.soyadi';
        $st = $this->pdo->prepare($sql);
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public function isArchived(int $userId, int $withId): bool
    {
        $st = $this->pdo->prepare('SELECT 1 FROM message_archives WHERE user_id=? AND with_user_id=?');
        $st->execute([$userId, $withId]);
        return (bool)$st->fetchColumn();
    }

    public function archiveConversation(int $userId, int $withId): bool
    {
        $st = $this->pdo->prepare('REPLACE INTO message_archives (user_id, with_user_id, archived_at) VALUES (?, ?, NOW())');
        return $st->execute([$userId, $withId]);
    }

    public function unarchiveConversation(int $userId, int $withId): bool
    {
        $st = $this->pdo->prepare('DELETE FROM message_archives WHERE user_id=? AND with_user_id=?');
        return $st->execute([$userId, $withId]);
    }

    public function markAsRead(int $receiverId, int $senderId): void
    {
        // Mark all messages sent by sender to receiver as read
        $st = $this->pdo->prepare('UPDATE messages SET read_at = COALESCE(read_at, NOW()) WHERE sender_id = ? AND receiver_id = ? AND read_at IS NULL');
        try { $st->execute([$senderId, $receiverId]); } catch (PDOException $e) { /* ignore */ }
    }
}
