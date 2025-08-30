<?php
class EmailDB
{
    public static ?PDO $pdo = null;
    private static array $tables = [
        'threads' => 'threads',
        'messages' => 'messages',
        'attachments' => 'attachments',
    ];

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) return self::$pdo;
        $dsn = getenv('MAIL_DB_DSN') ?: (getenv('DB_DSN') ?: (function(){
            $host = getenv('DB_HOST') ?: 'localhost';
            $db   = (getenv('DB_NAME') ?: 'sass') . '_mail';
            $charset = getenv('DB_CHARSET') ?: 'utf8mb4';
            return "mysql:host={$host};dbname={$db};charset={$charset}";
        })());
        $user = getenv('MAIL_DB_USER');
        if ($user === false || $user === '') $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('MAIL_DB_PASS');
        if ($pass === false) $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
        $opts = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            self::$pdo = new PDO($dsn, $user, $pass, $opts);
        } catch (PDOException $e) {
            // Fallback to main DB if _mail database missing
            $fallbackDsn = getenv('DB_DSN');
            if (!$fallbackDsn) {
                $host = getenv('DB_HOST') ?: 'localhost';
                $db   = getenv('DB_NAME') ?: 'sass';
                $charset = getenv('DB_CHARSET') ?: 'utf8mb4';
                $fallbackDsn = "mysql:host={$host};dbname={$db};charset={$charset}";
            }
            self::$pdo = new PDO($fallbackDsn, getenv('DB_USER') ?: 'root', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '', $opts);
        }
        self::ensureSchema(self::$pdo);
        return self::$pdo;
    }

    private static function ensureSchema(PDO $pdo): void
    {
        // Detect collision with existing `messages` from internal chat (no 'direction' column)
        $usePrefix = false;
        try {
            $exists = $pdo->query("SHOW TABLES LIKE 'messages'")->fetchColumn();
            if ($exists) {
                $cols = [];
                foreach ($pdo->query('SHOW COLUMNS FROM `messages`') as $row) {
                    $cols[strtolower((string)$row['Field'])] = true;
                }
                if (!isset($cols['direction'])) {
                    $usePrefix = true; // collision, use prefixed tables
                }
            }
        } catch (PDOException $e) {
            // ignore
        }
        if ($usePrefix) {
            self::$tables = [ 'threads' => 'mail_threads', 'messages' => 'mail_messages', 'attachments' => 'mail_attachments' ];
        } else {
            self::$tables = [ 'threads' => 'threads', 'messages' => 'messages', 'attachments' => 'attachments' ];
        }

        // FEHLER.md birebir tablo isimleri veya prefiksli versiyonları
        $tThreads = self::$tables['threads'];
        $tMessages = self::$tables['messages'];
        $tAttachments = self::$tables['attachments'];

        $stmts = [
            "CREATE TABLE IF NOT EXISTS `{$tThreads}` (\n" .
            "  id BIGINT AUTO_INCREMENT PRIMARY KEY,\n" .
            "  subject VARCHAR(998),\n" .
            "  last_message_at DATETIME,\n" .
            "  is_archived TINYINT(1) DEFAULT 0,\n" .
            "  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n" .
            "  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n" .
            "  INDEX(last_message_at)\n" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `{$tMessages}` (\n" .
            "  id BIGINT AUTO_INCREMENT PRIMARY KEY,\n" .
            "  thread_id BIGINT,\n" .
            "  direction ENUM('in','out') NOT NULL,\n" .
            "  status ENUM('received','queued','sent','failed','draft','scheduled') NOT NULL,\n" .
            "  from_email VARCHAR(254), from_name VARCHAR(190),\n" .
            "  to_json JSON, cc_json JSON, bcc_json JSON,\n" .
            "  subject VARCHAR(998),\n" .
            "  text_body MEDIUMTEXT, html_body MEDIUMTEXT,\n" .
            "  message_id VARCHAR(255), in_reply_to VARCHAR(255),\n" .
            "  headers_json JSON,\n" .
            "  sent_at DATETIME NULL, received_at DATETIME NULL,\n" .
            "  scheduled_at DATETIME NULL, fail_reason TEXT NULL,\n" .
            "  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n" .
            "  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n" .
            "  INDEX(thread_id), INDEX(status), INDEX(scheduled_at),\n" .
            "  FULLTEXT(subject, text_body, html_body)\n" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `{$tAttachments}` (\n" .
            "  id BIGINT AUTO_INCREMENT PRIMARY KEY,\n" .
            "  message_id BIGINT,\n" .
            "  file_name VARCHAR(255),\n" .
            "  mime VARCHAR(100),\n" .
            "  size INT,\n" .
            "  storage_path VARCHAR(255),\n" .
            "  cid VARCHAR(255) NULL\n" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        ];
        foreach ($stmts as $sql) {
            try { $pdo->exec($sql); } catch (PDOException $e) { /* ignore */ }
        }
    }

    public static function t(string $key): string
    {
        return self::$tables[$key] ?? $key;
    }
}
