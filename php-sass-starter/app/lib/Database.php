<?php
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $db   = getenv('DB_NAME') ?: 'sass';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';
        $opts = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $dsn = getenv('DB_DSN');
        if (!$dsn) {
            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        }
        try {
            self::$pdo = new PDO($dsn, $user, $pass, $opts);
            // Ensure schema exists (idempotent)
            self::ensureSchema(self::$pdo);
        } catch (PDOException $e) {
            $msg = 'Veritabanı bağlantı hatası: ' . $e->getMessage();
            // Fail fast with a clear message for dev; in prod you might log instead.
            http_response_code(500);
            die($msg);
        }
        return self::$pdo;
    }

    private static function ensureSchema(PDO $pdo): void
    {
        $stmts = [
            // users
            "CREATE TABLE IF NOT EXISTS `users` (\n" .
            "  `id` INT NOT NULL AUTO_INCREMENT,\n" .
            "  `adi` VARCHAR(100) NOT NULL,\n" .
            "  `soyadi` VARCHAR(100) NOT NULL,\n" .
            "  `mail` VARCHAR(180) NOT NULL,\n" .
            "  `sifre` VARCHAR(255) NOT NULL,\n" .
            "  `role` ENUM('admin','calisan','musteri') DEFAULT 'calisan',\n" .
            "  `son_giris_tarihi` DATETIME NULL,\n" .
            "  `olusturma_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
            "  `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n" .
            "  PRIMARY KEY (`id`),\n" .
            "  UNIQUE KEY `uniq_users_mail` (`mail`)\n" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // projects
            "CREATE TABLE IF NOT EXISTS `projects` (\n" .
            "  `id` INT NOT NULL AUTO_INCREMENT,\n" .
            "  `ad` VARCHAR(255) NOT NULL,\n" .
            "  `aciklama` TEXT NULL,\n" .
            "  `musteri_id` INT NULL,\n" .
            "  `durum` ENUM('yeni','devam','tamam','iptal') NOT NULL DEFAULT 'yeni',\n" .
            "  `olusturma_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
            "  `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n" .
            "  PRIMARY KEY (`id`),\n" .
            "  KEY `idx_projects_musteri` (`musteri_id`),\n" .
            "  CONSTRAINT `fk_projects_musteri` FOREIGN KEY (`musteri_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE\n" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // project_assignments
            "CREATE TABLE IF NOT EXISTS `project_assignments` (\n" .
            "  `project_id` INT NOT NULL,\n" .
            "  `calisan_id` INT NOT NULL,\n" .
            "  `rol_projede` VARCHAR(120) NULL,\n" .
            "  `atama_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
            "  PRIMARY KEY (`project_id`, `calisan_id`),\n" .
            "  KEY `idx_pa_calisan` (`calisan_id`),\n" .
            "  CONSTRAINT `fk_pa_project` FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,\n" .
            "  CONSTRAINT `fk_pa_user` FOREIGN KEY (`calisan_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE\n" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // project_milestones
            "CREATE TABLE IF NOT EXISTS `project_milestones` (\n" .
            "  `id` INT NOT NULL AUTO_INCREMENT,\n" .
            "  `project_id` INT NOT NULL,\n" .
            "  `baslik` VARCHAR(200) NOT NULL,\n" .
            "  `due_date` DATETIME NOT NULL,\n" .
            "  `completed_at` DATETIME NULL,\n" .
            "  `olusturma_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
            "  `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n" .
            "  PRIMARY KEY (`id`),\n" .
            "  KEY `idx_pm_project` (`project_id`),\n" .
            "  KEY `idx_pm_due` (`due_date`),\n" .
            "  CONSTRAINT `fk_pm_project` FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE ON UPDATE CASCADE\n" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        ];

        foreach ($stmts as $sql) {
            try {
                $pdo->exec($sql);
            } catch (PDOException $e) {
                // In production you would log this; keep going to avoid hard failure.
            }
        }

        // Ensure required columns exist on legacy installs
        self::ensureUsersColumns($pdo);
        self::ensureProjectsColumns($pdo);
        self::ensureAssignmentsColumns($pdo);
        self::ensureMilestonesColumns($pdo);
    }

    private static function ensureUsersColumns(PDO $pdo): void
    {
        try {
            $cols = [];
            foreach ($pdo->query('SHOW COLUMNS FROM `users`') as $row) {
                $cols[strtolower((string)$row['Field'])] = true;
            }
            if (!isset($cols['role'])) {
                $pdo->exec("ALTER TABLE `users` ADD COLUMN `role` ENUM('admin','calisan','musteri') DEFAULT 'calisan'");
            }
            // Drop legacy `type` column if present
            if (isset($cols['type'])) {
                try {
                    $pdo->exec("ALTER TABLE `users` DROP COLUMN `type`");
                } catch (PDOException $e) {
                    // ignore if cannot drop (permissions), app will continue using `role`
                }
            }
            if (!isset($cols['son_giris_tarihi'])) {
                $pdo->exec("ALTER TABLE `users` ADD COLUMN `son_giris_tarihi` DATETIME NULL");
            }
            if (!isset($cols['olusturma_tarihi'])) {
                $pdo->exec("ALTER TABLE `users` ADD COLUMN `olusturma_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
            }
            if (!isset($cols['guncelleme_tarihi'])) {
                // Some MySQL versions require full clause without ON UPDATE for initial add
                try {
                    $pdo->exec("ALTER TABLE `users` ADD COLUMN `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
                } catch (PDOException $e) {
                    $pdo->exec("ALTER TABLE `users` ADD COLUMN `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
                }
            }
        } catch (PDOException $e) {
            // ignore; table may not exist yet
        }
    }

    private static function ensureProjectsColumns(PDO $pdo): void
    {
        try {
            $cols = [];
            foreach ($pdo->query('SHOW COLUMNS FROM `projects`') as $row) {
                $cols[strtolower((string)$row['Field'])] = true;
            }
            if (!isset($cols['ad'])) {
                $pdo->exec("ALTER TABLE `projects` ADD COLUMN `ad` VARCHAR(255) NOT NULL");
            }
            if (!isset($cols['aciklama'])) {
                $pdo->exec("ALTER TABLE `projects` ADD COLUMN `aciklama` TEXT NULL");
            }
            if (!isset($cols['musteri_id'])) {
                $pdo->exec("ALTER TABLE `projects` ADD COLUMN `musteri_id` INT NULL");
            }
            if (!isset($cols['durum'])) {
                $pdo->exec("ALTER TABLE `projects` ADD COLUMN `durum` ENUM('yeni','devam','tamam','iptal') NOT NULL DEFAULT 'yeni'");
            }
            if (!isset($cols['olusturma_tarihi'])) {
                $pdo->exec("ALTER TABLE `projects` ADD COLUMN `olusturma_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
            }
            if (!isset($cols['guncelleme_tarihi'])) {
                try {
                    $pdo->exec("ALTER TABLE `projects` ADD COLUMN `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
                } catch (PDOException $e) {
                    $pdo->exec("ALTER TABLE `projects` ADD COLUMN `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
                }
            }
        } catch (PDOException $e) {
            // ignore if table is missing; it will be created by ensureSchema DDL
        }
    }

    private static function ensureAssignmentsColumns(PDO $pdo): void
    {
        try {
            $cols = [];
            foreach ($pdo->query('SHOW COLUMNS FROM `project_assignments`') as $row) {
                $cols[strtolower((string)$row['Field'])] = true;
            }
            if (!isset($cols['project_id'])) {
                $pdo->exec("ALTER TABLE `project_assignments` ADD COLUMN `project_id` INT NOT NULL");
            }
            if (!isset($cols['calisan_id'])) {
                $pdo->exec("ALTER TABLE `project_assignments` ADD COLUMN `calisan_id` INT NOT NULL");
            }
            if (!isset($cols['rol_projede'])) {
                $pdo->exec("ALTER TABLE `project_assignments` ADD COLUMN `rol_projede` VARCHAR(120) NULL");
            }
            if (!isset($cols['atama_tarihi'])) {
                $pdo->exec("ALTER TABLE `project_assignments` ADD COLUMN `atama_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
            }
        } catch (PDOException $e) {
            // ignore
        }
    }

    private static function ensureMilestonesColumns(PDO $pdo): void
    {
        try {
            $cols = [];
            foreach ($pdo->query('SHOW COLUMNS FROM `project_milestones`') as $row) {
                $cols[strtolower((string)$row['Field'])] = true;
            }
            if (!isset($cols['baslik'])) {
                $pdo->exec("ALTER TABLE `project_milestones` ADD COLUMN `baslik` VARCHAR(200) NOT NULL");
            }
            if (!isset($cols['due_date'])) {
                $pdo->exec("ALTER TABLE `project_milestones` ADD COLUMN `due_date` DATETIME NOT NULL");
            }
            if (!isset($cols['completed_at'])) {
                $pdo->exec("ALTER TABLE `project_milestones` ADD COLUMN `completed_at` DATETIME NULL");
            }
            if (!isset($cols['olusturma_tarihi'])) {
                $pdo->exec("ALTER TABLE `project_milestones` ADD COLUMN `olusturma_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
            }
            if (!isset($cols['guncelleme_tarihi'])) {
                try {
                    $pdo->exec("ALTER TABLE `project_milestones` ADD COLUMN `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
                } catch (PDOException $e) {
                    $pdo->exec("ALTER TABLE `project_milestones` ADD COLUMN `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
                }
            }
        } catch (PDOException $e) {
            // ignore
        }
    }
}
