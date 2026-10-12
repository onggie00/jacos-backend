-- ============================================================
-- switch_bahasa / 03_kolom_audit.sql — kolom jejak admin di
-- translation_override. Jalankan HANYA jika preflight (00, bagian 0.3)
-- menunjukkan kolom belum ada (MySQL 5.7 tidak punya ADD COLUMN IF NOT EXISTS).
-- ============================================================

ALTER TABLE `translation_override`
  ADD COLUMN `created_by` INT UNSIGNED DEFAULT NULL,
  ADD COLUMN `updated_by` INT UNSIGNED DEFAULT NULL;

-- ---- verifikasi (hasil diharapkan: 2 baris, IS_NULLABLE=YES)
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'translation_override'
  AND COLUMN_NAME IN ('created_by','updated_by');
