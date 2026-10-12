-- ============================================================
-- switch_bahasa / 04_verifikasi.sql — verifikasi agregat SETELAH blok
-- 01–03 dijalankan. Hasil diharapkan tertulis di tiap blok.
-- ============================================================

-- ---- 4.1 Keempat tabel ada + collation
SELECT TABLE_NAME, ENGINE, TABLE_COLLATION, TABLE_ROWS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('setting_bahasa','translation_cache','translation_override','translation_usage_bulanan')
ORDER BY TABLE_NAME;

-- ---- 4.2 Unique key & index utama
SELECT TABLE_NAME, INDEX_NAME, GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS kolom
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('setting_bahasa','translation_cache','translation_override')
  AND INDEX_NAME IN ('uq_email_role','uq_hash_kode','idx_last_used')
GROUP BY TABLE_NAME, INDEX_NAME;
-- hasil diharapkan: 4 baris (uq_email_role, uq_hash_kode x2, idx_last_used)

-- ---- 4.3 Index email 19 tabel akun
SELECT COUNT(DISTINCT TABLE_NAME) AS jumlah_tabel_berindex
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME IN ('email_ms_office','email_ms_office_ortu');
-- hasil diharapkan: >= 19

-- ---- 4.4 Kolom audit translation_override
SELECT COLUMN_NAME FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'translation_override'
  AND COLUMN_NAME IN ('created_by','updated_by');
-- hasil diharapkan: 2 baris
