-- ============================================================
-- switch_bahasa / 00_preflight.sql — HANYA SELECT/SHOW.
-- Jalankan di database PRODUKSI (phpMyAdmin, jalankan per blok) SEBELUM
-- menjalankan blok DDL. Semua hasil dicatat sebagai bukti & dasar keputusan.
-- Target: MySQL 5.7 (tanpa fitur MySQL 8).
-- ============================================================

-- ---- 0.1 Versi MySQL (harus 5.7.x; jika 8.x, catat — collation 0900 default)
SELECT VERSION() AS mysql_version;
-- hasil diharapkan: 5.7.x

-- ---- 0.2 Ada tidaknya tabel fitur + struktur + collation
SELECT TABLE_NAME, ENGINE, TABLE_COLLATION, TABLE_ROWS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('setting_bahasa','translation_cache','translation_override','translation_usage_bulanan');
-- hasil diharapkan: keempat tabel ADA (jika ada yang hilang -> jalankan blok DDL yang sesuai);
-- collation: setting_bahasa bisa general_ci; 3 tabel lain sebaiknya utf8mb4_unicode_ci

SHOW CREATE TABLE setting_bahasa;
SHOW CREATE TABLE translation_cache;
SHOW CREATE TABLE translation_override;
SHOW CREATE TABLE translation_usage_bulanan;
-- hasil diharapkan: setting_bahasa = UNIQUE (email_ms_office, role) uq_email_role;
-- translation_cache = UNIQUE (hash_sumber, kode_bahasa) uq_hash_kode + KEY idx_last_used;
-- translation_override = UNIQUE (hash_sumber, kode_bahasa) uq_hash_kode;
-- translation_usage_bulanan = UNIQUE (bulan, penyedia)

-- ---- 0.3 Kolom created_by & updated_by di translation_override (jalankan 03 jika belum ada)
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'translation_override'
  AND COLUMN_NAME IN ('created_by','updated_by');
-- hasil diharapkan: 2 baris (kalau 0 baris -> jalankan 03_kolom_audit.sql)

-- ---- 0.4 Index email di semua tabel akun (list dari fase1_index_email.sql)
--      19 tabel: hasil diharapkan >= 1 baris per tabel (kolom email_ms_office
--      sebagai kolom pertama index); tabel yang TIDAK muncul = belum berindex
SELECT TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX, COLUMN_NAME
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND ((TABLE_NAME IN ('siswa_sd','siswa_smp','siswa_sma','siswa_ft','siswa_kb','siswa_tk','switch_account')
        AND COLUMN_NAME IN ('email_ms_office','email_ms_office_ortu'))
    OR (TABLE_NAME IN ('guru_sd','guru_smp','guru_sma','guru_ft','pegawai','pimpinan','pimpinan_sd','pimpinan_sma','pimpinan_smp','pramubhakti','security')
        AND COLUMN_NAME = 'email_ms_office'))
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

-- ---- 0.5 Row format tabel akun (COMPACT/REDUNDANT = risiko panjang key index email;
--      DYNAMIC = aman). Kolom email varchar(255) utf8mb4 = 1020 byte > 767 byte limit
--      COMPACT lama.
SELECT TABLE_NAME, ENGINE, ROW_FORMAT, CREATE_OPTIONS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('siswa_sd','siswa_smp','siswa_sma','siswa_ft','siswa_kb','siswa_tk','switch_account',
                     'guru_sd','guru_smp','guru_sma','guru_ft','pegawai','pimpinan','pimpinan_sd','pimpinan_sma','pimpinan_smp','pramubhakti','security');
-- hasil diharapkan: ROW_FORMAT=DYNAMIC (atau innodb_file_format/large_prefix aktif di 5.7)
-- bila COMPACT/REDUNDANT: index email di tabel tsb bisa gagal (1673/1071) — jalankan
-- ALTER TABLE <t> ROW_FORMAT=DYNAMIC dulu (lihat komentar di 02_index_email_akun.sql)

-- ---- 0.6 Collation kolom email di tiap tabel akun (untuk parameterkan DDL 02)
SELECT TABLE_NAME, COLUMN_NAME, CHARACTER_SET_NAME, COLLATION_NAME, COLUMN_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME IN ('email_ms_office','email_ms_office_ortu')
ORDER BY TABLE_NAME, COLUMN_NAME;
-- catat: bila kolom email latin1/utf8mb3 -> sesuaikan komentar di 02

-- ---- 0.7 Izin & menu modul Pengaturan Bahasa (jalankan 05 jika kosong)
SELECT id, name FROM aauth_perms WHERE name IN (
  'setting_bahasa_admin_view','setting_bahasa_admin_add',
  'setting_bahasa_admin_update','setting_bahasa_admin_delete','menu_pengaturan_bahasa');
SELECT id, label, link, parent FROM menu WHERE link = 'administrator/setting_bahasa';
-- hasil diharapkan: 5 perm + 1 menu (parent = menu "Pengaturan")

-- ---- 0.8 Izin sudah diberikan ke group Admin?
SELECT p.name, gp.group_id
FROM aauth_perms p
LEFT JOIN aauth_perm_to_group gp ON gp.perm_id = p.id
WHERE p.name IN ('setting_bahasa_admin_view','setting_bahasa_admin_add',
                 'setting_bahasa_admin_update','setting_bahasa_admin_delete','menu_pengaturan_bahasa');
-- hasil diharapkan: setiap perm punya baris utk group "Admin" (cari id group dulu: SELECT id, name FROM aauth_groups)

-- ---- 0.9 Jumlah baris setting_bahasa & translation_cache
SELECT 'setting_bahasa' AS tabel, COUNT(*) AS jumlah FROM setting_bahasa
UNION ALL
SELECT 'translation_cache', COUNT(*) FROM translation_cache;

-- ---- 0.10 Daftar kemungkinan data uji (tinjau manual sebelum dipakai produksi)
SELECT 'setting_bahasa-uji' AS sumber, email_ms_office AS teks, role, '' AS kode
FROM setting_bahasa
WHERE email_ms_office LIKE '%@jacos.test%' OR email_ms_office LIKE '%@example.com%'
   OR email_ms_office LIKE '%race.test%'
UNION ALL
SELECT 'cache-cek-hash', LEFT(teks_asal,60), kode_bahasa, ''
FROM translation_cache
WHERE teks_hasil REGEXP '^[a-f0-9]{32}$'
LIMIT 50;
-- hasil diharapkan: 0 baris. Baris yang muncul = data uji -> bersihkan manual bila perlu.
