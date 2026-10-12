-- ============================================================
-- switch_bahasa / 01_tabel.sql — DDL tabel fitur (idempoten).
-- Jalankan HANYA jika 00_preflight menunjukkan tabel belum ada.
-- MySQL 5.7; collation parameter: sesuai tabel akun produksi — ganti
-- utf8mb4_unicode_ci bila hasil preflight menunjukkan tabel akun memakai
-- collation lain (mis. utf8mb4_general_ci).
-- ============================================================

-- ---- 1.1 setting_bahasa (1 baris per user per role)
CREATE TABLE IF NOT EXISTS `setting_bahasa` (
  `id_setting_bahasa` INT NOT NULL AUTO_INCREMENT,
  `kode_bahasa` VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bahasa` VARCHAR(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenjang` VARCHAR(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_ms_office` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id_setting_bahasa`),
  UNIQUE KEY `uq_email_role` (`email_ms_office`,`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- verifikasi: SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='setting_bahasa';  -- = 1

-- ---- 1.2 translation_cache (hasil DeepL; hash = md5(prefix_tipe + teks ternormalisasi))
CREATE TABLE IF NOT EXISTS `translation_cache` (
  `id_translation_cache` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_sumber` CHAR(32) NOT NULL,
  `bahasa` VARCHAR(50) NOT NULL,
  `kode_bahasa` VARCHAR(20) NOT NULL,
  `teks_asal` MEDIUMTEXT NOT NULL,
  `teks_hasil` MEDIUMTEXT NOT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `last_used_at` DATETIME DEFAULT NULL,
  `hit_count` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_translation_cache`),
  UNIQUE KEY `uq_hash_kode` (`hash_sumber`,`kode_bahasa`),
  KEY `idx_last_used` (`last_used_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- verifikasi: SHOW CREATE TABLE translation_cache;  -- uq_hash_kode + idx_last_used ada

-- ---- 1.3 translation_override (koreksi admin; prioritas di atas cache & DeepL)
CREATE TABLE IF NOT EXISTS `translation_override` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_sumber` CHAR(32) NOT NULL,
  `kode_bahasa` VARCHAR(10) NOT NULL,
  `teks_asal` VARCHAR(500) NOT NULL,
  `teks_hasil` TEXT NOT NULL,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `catatan` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_hash_kode` (`hash_sumber`,`kode_bahasa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- verifikasi: SHOW CREATE TABLE translation_override;  -- uq_hash_kode ada

-- ---- 1.4 translation_usage_bulanan (kuota DeepL per bulan)
CREATE TABLE IF NOT EXISTS `translation_usage_bulanan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bulan` CHAR(7) NOT NULL,
  `penyedia` VARCHAR(10) NOT NULL,
  `jumlah_request` INT UNSIGNED NOT NULL DEFAULT 0,
  `jumlah_karakter` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `jumlah_string` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bulan_penyedia` (`bulan`,`penyedia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- verifikasi: SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('translation_cache','translation_override','translation_usage_bulanan');  -- = 3
