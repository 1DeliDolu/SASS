-- MySQL schema for php-sass-starter
-- Creates DB, users, projects, and project_assignments tables

-- Create database if needed
CREATE DATABASE IF NOT EXISTS `sass`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `sass`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `adi` VARCHAR(100) NOT NULL,
  `soyadi` VARCHAR(100) NOT NULL,
  `mail` VARCHAR(180) NOT NULL,
  `sifre` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','calisan','musteri') DEFAULT 'calisan',
  `son_giris_tarihi` DATETIME NULL,
  `olusturma_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_mail` (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects table
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ad` VARCHAR(255) NOT NULL,
  `aciklama` TEXT NULL,
  `musteri_id` INT NULL,
  `durum` ENUM('yeni','devam','tamam','iptal') NOT NULL DEFAULT 'yeni',
  `olusturma_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_projects_musteri` (`musteri_id`),
  CONSTRAINT `fk_projects_musteri` FOREIGN KEY (`musteri_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Project assignments (many-to-many projects <-> users as calisan)
CREATE TABLE IF NOT EXISTS `project_assignments` (
  `project_id` INT NOT NULL,
  `calisan_id` INT NOT NULL,
  `rol_projede` VARCHAR(120) NULL,
  `atama_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_id`, `calisan_id`),
  KEY `idx_pa_calisan` (`calisan_id`),
  CONSTRAINT `fk_pa_project` FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pa_user` FOREIGN KEY (`calisan_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Project milestones
CREATE TABLE IF NOT EXISTS `project_milestones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `project_id` INT NOT NULL,
  `baslik` VARCHAR(200) NOT NULL,
  `due_date` DATETIME NOT NULL,
  `completed_at` DATETIME NULL,
  `olusturma_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pm_project` (`project_id`),
  KEY `idx_pm_due` (`due_date`),
  CONSTRAINT `fk_pm_project` FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: seed an admin quickly (set your own password hash)
-- INSERT INTO users (adi, soyadi, mail, sifre, role, type)
-- VALUES ('Admin', 'User', 'admin@example.com', '$2y$10$REPLACE_WITH_PASSWORD_HASH', 'admin', 'admin');

-- Or register via the app, then elevate:
-- UPDATE users SET role='admin', type='admin' WHERE mail='your@email.tld';
