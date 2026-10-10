-- Produksi: modul Kuesioner PSB (web_psb_pertanyaan + web_psb_item) — perms + menu
-- Jalankan SETELAH file module diupload.
-- Idempotent: aman dijalankan berulang.

-- aauth_perms (idempotent via cek nama) — web_psb_pertanyaan
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_pertanyaan_list','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_pertanyaan_list');
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_pertanyaan_view','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_pertanyaan_view');
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_pertanyaan_add','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_pertanyaan_add');
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_pertanyaan_update','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_pertanyaan_update');
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_pertanyaan_delete','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_pertanyaan_delete');

-- aauth_perms — web_psb_item
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_item_list','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_item_list');
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_item_view','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_item_view');
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_item_add','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_item_add');
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_item_update','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_item_update');
INSERT INTO aauth_perms (name, definition)
SELECT 'web_psb_item_delete','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='web_psb_item_delete');

-- menu: submenu "Kuesioner PSB" di Pengaturan PSB (parent 389)
-- (jawaban/item diakses dari detail pertanyaan, tanpa menu terpisah)
INSERT INTO menu (label, type, link, sort, parent, menu_type_id, active)
SELECT 'Kuesioner PSB','menu','administrator/web_psb_pertanyaan',
  (SELECT COALESCE(MAX(sort),0)+1 FROM menu WHERE parent=389),389,1,1
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE link='administrator/web_psb_pertanyaan');
