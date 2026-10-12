-- ============================================================
-- switch_bahasa / 90_rollback.sql — PEMBATALAN.
--
-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
-- !!  HATI-HATI: file ini MENGHAPUS tabel, index, izin & menu.  !!
-- !!  Jalankan HANYA jika fitur switch bahasa harus dibatalkan !!
-- !!  total di server ini. BACA URUTAN di bawah SEBELUM run.    !!
-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
--
-- URUTAN AMAN:
--   1) Putuskan dulu: translator_enabled = FALSE di config (kill switch) —
--      fitur mati segera TANPA rollback DB. Rollback DB hanya jika objek
--      mau dibersihkan total.
--   2) Semua blok bersifat kondisional: jalankan HANYA bila objek itu
--      DIBUAT oleh deploy switch_bahasa (bukan dibuat proses lain).
--   3) Jangan sentuh tabel akun selain index yang dibuat fase 1.
-- ============================================================

-- ---- R1. Matikan fitur di config (BUKAN SQL — sunting file):
--      application/config/translator.php:
--        $config['translator_enabled'] = FALSE;
--      (atau kosongkan translator_enabled_endpoints) — TANPA deploy ulang.
-- SETELAH ITU baru jalankan blok di bawah bila perlu bersih-bersih DB.

-- ---- R2. Hapus izin & menu modul (urutan: mapping dulu, lalu perm & menu)
DELETE FROM aauth_perm_to_group
WHERE perm_id IN (SELECT id FROM aauth_perms WHERE name IN (
  'setting_bahasa_admin_view','setting_bahasa_admin_add',
  'setting_bahasa_admin_update','setting_bahasa_admin_delete','menu_pengaturan_bahasa'));

DELETE FROM aauth_perms WHERE name IN (
  'setting_bahasa_admin_view','setting_bahasa_admin_add',
  'setting_bahasa_admin_update','setting_bahasa_admin_delete','menu_pengaturan_bahasa');

DELETE FROM menu WHERE link = 'administrator/setting_bahasa';

-- ---- R3. Drop tabel BARU (urutan aman: usage -> override -> cache).
--      PERINGATAN: translation_cache bisa memuat hasil terjemahan yang masih
--      dipakai (akan dihitung ulang saat DeepL dipanggil ulang).
--      setting_bahasa = pilihan bahasa USER — di-drop HANYA jika tabel ini
--      dibuat oleh deploy switch_bahasa dan Anda siap menghapus preferensi
--      bahasa user (fitur mati: user kembali ke default Indonesia).
DROP TABLE IF EXISTS `translation_usage_bulanan`;
DROP TABLE IF EXISTS `translation_override`;
-- Jalankan dua baris berikut HANYA jika setting_bahasa/translation_cache
-- dibuat oleh deploy ini (bukan sudah ada sebelumnya):
-- DROP TABLE IF EXISTS `translation_cache`;
-- DROP TABLE IF EXISTS `setting_bahasa`;

-- ---- R4. Hapus index email (index BARU fase 1) — tabel akun TIDAK diubah
--      selain index. Jalankan per tabel yang di preflight (00, bagian 0.4)
--      terbukti baru dibuat oleh 02_index_email_akun.sql.
-- Perhatian 5.7: DROP INDEX tidak punya IF NOT EXISTS; error "check that
-- column/key exists" berarti index memang tidak ada — abaikan.
-- ALTER TABLE `siswa_sd`      DROP INDEX `idx_email_ms_office`, DROP INDEX `idx_email_ms_office_ortu`;
-- ALTER TABLE `siswa_smp`     DROP INDEX `idx_email_ms_office`, DROP INDEX `idx_email_ms_office_ortu`;
-- ALTER TABLE `siswa_sma`     DROP INDEX `idx_email_ms_office`, DROP INDEX `idx_email_ms_office_ortu`;
-- ALTER TABLE `siswa_ft`      DROP INDEX `idx_email_ms_office`, DROP INDEX `idx_email_ms_office_ortu`;
-- ALTER TABLE `siswa_kb`      DROP INDEX `idx_email_ms_office`, DROP INDEX `idx_email_ms_office_ortu`;
-- ALTER TABLE `siswa_tk`      DROP INDEX `idx_email_ms_office`, DROP INDEX `idx_email_ms_office_ortu`;
-- ALTER TABLE `switch_account` DROP INDEX `idx_email_ms_office`, DROP INDEX `idx_email_ms_office_ortu`;
-- ALTER TABLE `guru_sd`       DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `guru_smp`      DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `guru_sma`      DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `guru_ft`       DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `pegawai`       DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `pimpinan`      DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `pimpinan_sd`   DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `pimpinan_smp`  DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `pimpinan_sma`  DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `pramubhakti`   DROP INDEX `idx_email_ms_office`;
-- ALTER TABLE `security`      DROP INDEX `idx_email_ms_office`;

-- ---- R5. Hapus kolom audit (jalankan hanya jika mau bersih total):
-- ALTER TABLE `translation_override`
--   DROP COLUMN `created_by`, DROP COLUMN `updated_by`;

-- ---- verifikasi akhir (hasil diharapkan: 0 baris di 0.7/0.8; tabel fitur
--      yang dibuat oleh deploy hilang; index email hilang)
SELECT id, name FROM aauth_perms WHERE name IN (
  'setting_bahasa_admin_view','setting_bahasa_admin_add',
  'setting_bahasa_admin_update','setting_bahasa_admin_delete','menu_pengaturan_bahasa');
SELECT id, label, link FROM menu WHERE link = 'administrator/setting_bahasa';
SELECT TABLE_NAME FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('translation_cache','translation_override','translation_usage_bulanan');
