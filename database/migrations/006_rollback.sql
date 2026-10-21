-- ============================================================================
-- 006_rollback.sql — Fase GURU.1 (pembalikan 006_guru_kb_tk.sql)
-- AMAN: tabel guru_kb/guru_tk hanya di-drop bila KOSONG; baris menu dibersihkan
-- hanya bila tanpa data; posisi hanya dihapus bila tidak dipakai.
-- Jalankan: mysql --no-defaults -uroot jacos_db < 006_rollback.sql
-- ============================================================================

-- 1. Menu anak (Data Guru) lalu parent — hanya jika tabel guru terkait kosong.
--    Perbaiki dulu: mesin rollback menolak bila data masih ada.
SET @ada_kb = (SELECT COUNT(*) FROM `guru_kb`);
SET @ada_tk = (SELECT COUNT(*) FROM `guru_tk`);
SELECT CASE WHEN @ada_kb > 0 OR @ada_tk > 0
       THEN 'HALT: guru_kb/guru_tk masih berisi data — bersihkan data dulu sebelum rollback.'
       ELSE 'OK: aman dilanjutkan' END AS status_rollback;

-- 2. Hapus menu anak lalu menu parent (dedupe by link).
DELETE FROM `menu`
WHERE label = 'Data Guru'
  AND parent IN (SELECT id FROM (SELECT id FROM (SELECT id FROM `menu` WHERE link IN ('administrator/guru_kb','administrator/guru_tk')) t) q);

DELETE FROM `menu` WHERE link = 'administrator/guru_kb' AND label = 'Guru KB';
DELETE FROM `menu` WHERE link = 'administrator/guru_tk' AND label = 'Guru TK';

-- 3. Hapus permissions (semua tanpa aauth_perm_to_group otomatis ikut hilang
--    karena menyimpan perm_id — baris relasi juga dibersihkan eksplisit).
DELETE FROM `aauth_perm_to_group`
WHERE perm_id IN (SELECT id FROM `aauth_perms` WHERE name IN (
  'guru_kb_list','guru_kb_add','guru_kb_update','guru_kb_delete','guru_kb_view','guru_kb_export',
  'guru_tk_list','guru_tk_add','guru_tk_update','guru_tk_delete','guru_tk_view','guru_tk_export',
  'menu_guru_kb','menu_guru_tk'));

DELETE FROM `aauth_perms` WHERE name IN (
  'guru_kb_list','guru_kb_add','guru_kb_update','guru_kb_delete','guru_kb_view','guru_kb_export',
  'guru_tk_list','guru_tk_add','guru_tk_update','guru_tk_delete','guru_tk_view','guru_tk_export',
  'menu_guru_kb','menu_guru_tk');

-- 4. Hapus posisi Guru KB/Guru TK hanya bila tak dipakai baris guru.
DELETE p FROM `posisi_guru` p
LEFT JOIN `guru_sd` s  ON s.id_posisi = p.id_posisi
LEFT JOIN `guru_smp` s2 ON s2.id_posisi = p.id_posisi
LEFT JOIN `guru_sma` s3 ON s3.id_posisi = p.id_posisi
LEFT JOIN `guru_ft`  s4 ON s4.id_posisi = p.id_posisi
LEFT JOIN `guru_kb`  s5 ON s5.id_posisi = p.id_posisi
LEFT JOIN `guru_tk`  s6 ON s6.id_posisi = p.id_posisi
WHERE p.nama_posisi IN ('Guru KB','Guru TK')
  AND s.id_guru IS NULL AND s2.id_guru IS NULL AND s3.id_guru IS NULL
  AND s4.id_guru IS NULL AND s5.id_guru IS NULL AND s6.id_guru IS NULL;

-- 5. Drop tabel hanya bila kosong.
SET @drop_kb = IF((SELECT COUNT(*) FROM `guru_kb`) = 0,
  'DROP TABLE IF EXISTS `guru_kb`', 'SELECT 1');
SET @drop_tk = IF((SELECT COUNT(*) FROM `guru_tk`) = 0,
  'DROP TABLE IF EXISTS `guru_tk`', 'SELECT 1');
PREPARE st_kb FROM @drop_kb; EXECUTE st_kb; DEALLOCATE PREPARE st_kb;
PREPARE st_tk FROM @drop_tk; EXECUTE st_tk; DEALLOCATE PREPARE st_tk;

-- Verifikasi manual setelah rollback:
--   SELECT COUNT(*) FROM menu WHERE link LIKE 'administrator/guru_k%';   → 0
--   SELECT COUNT(*) FROM aauth_perms WHERE name LIKE 'guru_kb_%' OR name LIKE 'guru_tk_%' OR name IN ('menu_guru_kb','menu_guru_tk'); → 0
