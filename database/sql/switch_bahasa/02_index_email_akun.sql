-- ============================================================
-- switch_bahasa / 02_index_email_akun.sql — index email di 19 tabel akun
-- (idempoten: jalankan hanya baris yang di preflight (00, bagian 0.4) belum
--  muncul. MySQL 5.7 tidak punya ADD INDEX IF NOT EXISTS.)
-- Jika preflight bagian 0.5 menunjukkan ROW_FORMAT COMPACT/REDUNDANT dan
-- error 1673/1071 saat ADD INDEX, jalankan dulu ALTER ROW_FORMAT=DYNAMIC
-- utk tabel tsb (contoh di komentar paling bawah).
-- ============================================================

-- siswa (2 index per tabel: email_ms_office & email_ms_office_ortu)
ALTER TABLE `siswa_sd`     ADD INDEX `idx_email_ms_office` (`email_ms_office`), ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`);
ALTER TABLE `siswa_smp`    ADD INDEX `idx_email_ms_office` (`email_ms_office`), ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`);
ALTER TABLE `siswa_sma`    ADD INDEX `idx_email_ms_office` (`email_ms_office`), ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`);
ALTER TABLE `siswa_ft`     ADD INDEX `idx_email_ms_office` (`email_ms_office`), ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`);

ALTER TABLE `siswa_kb`     ADD INDEX `idx_email_ms_office` (`email_ms_office`), ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`);
ALTER TABLE `siswa_tk`     ADD INDEX `idx_email_ms_office` (`email_ms_office`), ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`);
ALTER TABLE `switch_account` ADD INDEX `idx_email_ms_office` (`email_ms_office`), ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`);

-- guru, pegawai, pimpinan, pramubhakti, security (email_ms_office saja)
ALTER TABLE `guru_sd`      ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `guru_smp`     ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `guru_sma`     ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `guru_ft`      ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `pegawai`      ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `pimpinan`     ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `pimpinan_sd`  ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `pimpinan_smp` ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `pimpinan_sma` ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `pramubhakti`  ADD INDEX `idx_email_ms_office` (`email_ms_office`);
ALTER TABLE `security`     ADD INDEX `idx_email_ms_office` (`email_ms_office`);

-- ---- verifikasi (hasil diharapkan: 19 tabel, masing-masing >= 1 index email)
SELECT TABLE_NAME, INDEX_NAME, COLUMN_NAME, SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME IN ('email_ms_office','email_ms_office_ortu')
  AND TABLE_NAME IN ('siswa_sd','siswa_smp','siswa_sma','siswa_ft','siswa_kb','siswa_tk','switch_account',
                     'guru_sd','guru_smp','guru_sma','guru_ft','pegawai','pimpinan','pimpinan_sd','pimpinan_sma','pimpinan_smp','pramubhakti','security')
ORDER BY TABLE_NAME, INDEX_NAME;

-- ---- bila ROW_FORMAT perlu disesuaikan (jalankan per tabel yang error 1673/1071):
-- ALTER TABLE `siswa_sd` ROW_FORMAT=DYNAMIC;
-- ALTER TABLE `guru_sd`  ROW_FORMAT=DYNAMIC;
-- (ulangi utk tabel lain sesuai hasil preflight 0.5)
