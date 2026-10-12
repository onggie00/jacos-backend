-- ============================================================
-- switch_bahasa / 05_izin_menu.sql — izin & menu modul admin
-- "Pengaturan Bahasa" utk PRODUKSI. Idempoten (aman dijalankan 2x),
-- TANPA ID hardcode: parent menu dicari dari label/link, group Admin
-- dicari dari nama. MySQL 5.7 kompatibel.
-- Catatan: tabel `menu` & `aauth_*` = data modul panel CiCOOL
-- (DML data, bukan DDL).
-- ============================================================

-- ---- 5.1 Permissions modul (5 buah) — insert hanya jika belum ada
INSERT INTO aauth_perms (name, definition)
SELECT 'setting_bahasa_admin_view', 'Switch bahasa: lihat modul'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'setting_bahasa_admin_view');

INSERT INTO aauth_perms (name, definition)
SELECT 'setting_bahasa_admin_add', 'Switch bahasa: tambah override'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'setting_bahasa_admin_add');

INSERT INTO aauth_perms (name, definition)
SELECT 'setting_bahasa_admin_update', 'Switch bahasa: ubah/nonaktif override'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'setting_bahasa_admin_update');

INSERT INTO aauth_perms (name, definition)
SELECT 'setting_bahasa_admin_delete', 'Switch bahasa: hapus override'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'setting_bahasa_admin_delete');

INSERT INTO aauth_perms (name, definition)
SELECT 'setting_bahasa_admin_warmup', 'Switch bahasa: halaman Warm-up (isi cache admin)'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'setting_bahasa_admin_warmup');

INSERT INTO aauth_perms (name, definition)
SELECT 'menu_pengaturan_bahasa', 'Menu Pengaturan Bahasa'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'menu_pengaturan_bahasa');

-- ---- 5.2 Berikan semua perm ke group Admin (dicari via nama, bukan id)
INSERT INTO aauth_perm_to_group (perm_id, group_id)
SELECT p.id, g.id
FROM aauth_perms p
JOIN aauth_groups g ON g.name = 'Admin' AND g.definition = 'Superadmin Group'
WHERE p.name IN (
  'setting_bahasa_admin_view','setting_bahasa_admin_add',
  'setting_bahasa_admin_update','setting_bahasa_admin_delete',
  'setting_bahasa_admin_warmup','menu_pengaturan_bahasa')
AND NOT EXISTS (
  SELECT 1 FROM aauth_perm_to_group x
  WHERE x.perm_id = p.id AND x.group_id = g.id);

-- ---- 5.3 Menu "Pengaturan Bahasa" (parent = menu "Pengaturan" utama)
-- parent dicari dari menu utama label 'Pengaturan' (link administrator/sumber_informasi).
INSERT INTO menu (label, type, link, sort, parent, icon, menu_type_id, active)
SELECT 'Pengaturan Bahasa', 'menu', 'administrator/setting_bahasa', 99, par.pid, NULL, 1, 1
FROM (SELECT id AS pid FROM menu WHERE label = 'Pengaturan' LIMIT 1) AS par
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE link = 'administrator/setting_bahasa');

-- ---- 5.4 Berikan perm menu ke group Admin (visibilitas sidebar)
INSERT INTO aauth_perm_to_group (perm_id, group_id)
SELECT p.id, g.id
FROM aauth_perms p
JOIN aauth_groups g ON g.name = 'Admin' AND g.definition = 'Superadmin Group'
WHERE p.name = 'menu_pengaturan_bahasa'
AND NOT EXISTS (SELECT 1 FROM aauth_perm_to_group x WHERE x.perm_id = p.id AND x.group_id = g.id);

-- ---- verifikasi (hasil diharapkan: 6 perm, 7 assignment group Admin, 1 menu)
SELECT name FROM aauth_perms WHERE name IN (
  'setting_bahasa_admin_view','setting_bahasa_admin_add',
  'setting_bahasa_admin_update','setting_bahasa_admin_delete',
  'setting_bahasa_admin_warmup','menu_pengaturan_bahasa');

SELECT p.name AS perm, gp.group_id
FROM aauth_perms p
JOIN aauth_perm_to_group gp ON gp.perm_id = p.id
WHERE p.name IN (
  'setting_bahasa_admin_view','setting_bahasa_admin_add',
  'setting_bahasa_admin_update','setting_bahasa_admin_delete','menu_pengaturan_bahasa')
ORDER BY p.name;

SELECT id, label, link, parent FROM menu WHERE link = 'administrator/setting_bahasa';

-- ---- (opsional) berikan juga ke group lain, ulangi pola 5.2 dgn nama group:
-- INSERT INTO aauth_perm_to_group (perm_id, group_id)
-- SELECT p.id, g.id FROM aauth_perms p
-- JOIN aauth_groups g ON g.name = 'ADMIN SMA'
-- WHERE p.name IN ('setting_bahasa_admin_view','setting_bahasa_admin_add',
--                  'setting_bahasa_admin_update','setting_bahasa_admin_delete')
-- AND NOT EXISTS (SELECT 1 FROM aauth_perm_to_group x
--                 WHERE x.perm_id = p.id AND x.group_id = g.id);
