-- ============================================================================
-- GURU.1 — 007_career_guru_link.sql  (idempoten; MySQL 8)
-- Tautan lamaran diterima (accepted) → baris data guru (guru_sd/guru_kb/guru_tk).
-- Konversi adalah SALIN (bukan pindah): career_*/dokumen tetap utuh.
-- Catatan pra-run: tabel ini dibuat jika belum ada; perm career_pelamar_to_guru
--   diberikan ke grup pemegang career_pelamar_decide (tidak hardcode id grup).
-- Rollback: database/migrations/007_rollback.sql (HANYA tabel/perm; data guru
--   hasil konversi TIDAK dihapus oleh rollback).
-- ============================================================================

CREATE TABLE IF NOT EXISTS `career_guru_link` (
  `id_link` int unsigned NOT NULL AUTO_INCREMENT,
  `id_application` int unsigned NOT NULL,
  `id_candidate` int unsigned NOT NULL,
  `jenjang` enum('SD','KB','TK') NOT NULL,
  `id_guru` int NOT NULL,
  `converted_by` int DEFAULT NULL,
  `converted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `snapshot_json` json DEFAULT NULL,
  PRIMARY KEY (`id_link`),
  UNIQUE KEY `uq_link_application` (`id_application`),
  KEY `idx_link_jenjang_id_guru` (`jenjang`,`id_guru`),
  KEY `idx_link_candidate` (`id_candidate`),
  CONSTRAINT `fk_link_application` FOREIGN KEY (`id_application`) REFERENCES `career_applications` (`id_application`) ON DELETE RESTRICT,
  CONSTRAINT `fk_link_candidate` FOREIGN KEY (`id_candidate`) REFERENCES `career_candidates` (`id_candidate`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Perm konversi (menu tetap pelamar; tanpa perm view kedua)
INSERT INTO aauth_perms (name, definition)
SELECT 'career_pelamar_to_guru', 'Konversi pelamar accepted menjadi guru'
WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'career_pelamar_to_guru');

-- Grant ke grup pemegang career_pelamar_decide
INSERT IGNORE INTO aauth_perm_to_group (perm_id, group_id)
SELECT p2.id, pm.group_id
FROM aauth_perms p2
JOIN aauth_perm_to_group pm
  ON pm.perm_id = (SELECT id FROM aauth_perms WHERE name = 'career_pelamar_decide')
WHERE p2.name = 'career_pelamar_to_guru';
