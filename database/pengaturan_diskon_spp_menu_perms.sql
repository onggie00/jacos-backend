-- ============================================================================
-- pengaturan_diskon_spp_menu_perms.sql
-- Menu + permission modul admin Pengaturan Diskon SPP (idempotent).
-- Jalankan setelah diskon_spp_migration.sql.
-- Groups diberi akses: 10 (Keuangan), 25 (Admin PSB) — pola sama dgn modul
-- kuota_diskon_psb.
-- ============================================================================

-- 1. Permissions -------------------------------------------------------------
INSERT INTO aauth_perms (`name`, `definition`)
SELECT 'pengaturan_diskon_spp_list', '' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'pengaturan_diskon_spp_list');
INSERT INTO aauth_perms (`name`, `definition`)
SELECT 'pengaturan_diskon_spp_add', '' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'pengaturan_diskon_spp_add');
INSERT INTO aauth_perms (`name`, `definition`)
SELECT 'pengaturan_diskon_spp_update', '' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'pengaturan_diskon_spp_update');
INSERT INTO aauth_perms (`name`, `definition`)
SELECT 'pengaturan_diskon_spp_delete', '' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'pengaturan_diskon_spp_delete');
INSERT INTO aauth_perms (`name`, `definition`)
SELECT 'pengaturan_diskon_spp_view', '' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'pengaturan_diskon_spp_view');
INSERT INTO aauth_perms (`name`, `definition`)
SELECT 'menu_pengaturan_diskon_spp', '' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name = 'menu_pengaturan_diskon_spp');

-- 2. Group access (10 = Keuangan, 25 = Admin PSB) -----------------------------
INSERT INTO aauth_perm_to_group (`perm_id`, `group_id`)
SELECT p.id, g.id
FROM aauth_perms p
JOIN (SELECT 10 AS id UNION SELECT 25 AS id) g
WHERE (p.name LIKE 'pengaturan_diskon_spp_%' OR p.name = 'menu_pengaturan_diskon_spp')
  AND NOT EXISTS (
    SELECT 1 FROM aauth_perm_to_group pg
    WHERE pg.perm_id = p.id AND pg.group_id = g.id
  );

-- 3. Menu: anak dari "Pengaturan" akademik/keuangan (parent 389 sama dgn Kuota
--    Diskon PSB), sort 52 sesudah Kuota Diskon PSB ----------------------------
INSERT INTO menu (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`)
SELECT 'Pengaturan Diskon SPP', 'menu', 'default', 'administrator/pengaturan_diskon_spp', 52, 389, '', 1, 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE link = 'administrator/pengaturan_diskon_spp');
