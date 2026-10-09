-- Produksi: admin module Ketentuan PSB KB & TK (perms + menu)
-- Jalankan SETELAH file module diupload.

-- aauth_perms (idempotent via cek nama)
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_kb_list','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_kb_list');
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_kb_view','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_kb_view');
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_kb_add','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_kb_add');
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_kb_update','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_kb_update');
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_kb_delete','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_kb_delete');
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_tk_list','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_tk_list');
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_tk_view','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_tk_view');
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_tk_add','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_tk_add');
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_tk_update','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_tk_update');
INSERT INTO aauth_perms (name, definition)
SELECT 'ketentuan_pendaftaran_psb_tk_delete','' WHERE NOT EXISTS (SELECT 1 FROM aauth_perms WHERE name='ketentuan_pendaftaran_psb_tk_delete');

-- menu (parent 41 = "Ketentuan Pendaftaran")
INSERT INTO menu (label, type, link, sort, parent, menu_type_id, active)
SELECT 'PSB KB','menu','administrator/ketentuan_pendaftaran_psb_kb',
  (SELECT COALESCE(MAX(sort),31)+1 FROM menu WHERE parent=41),41,1,1
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE link='administrator/ketentuan_pendaftaran_psb_kb');

INSERT INTO menu (label, type, link, sort, parent, menu_type_id, active)
SELECT 'PSB TK','menu','administrator/ketentuan_pendaftaran_psb_tk',
  (SELECT COALESCE(MAX(sort),31)+1 FROM menu WHERE parent=41),41,1,1
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE link='administrator/ketentuan_pendaftaran_psb_tk');

-- perbaiki judul/sub_judul konten ketentuan KB & TK (masih salinan SD)
UPDATE ketentuan_pendaftaran_psb_kb SET judul='Ketentuan PSB KB Jakarta Cosmopolite Islamic School', sub_judul='KB Jakarta Cosmopolite Islamic School Tahun Ajaran 2027/2028' WHERE judul LIKE '%PSB SD%';
UPDATE ketentuan_pendaftaran_psb_tk SET judul='Ketentuan PSB TK Jakarta Cosmopolite Islamic School', sub_judul='TK Jakarta Cosmopolite Islamic School Tahun Ajaran 2027/2028' WHERE judul LIKE '%PSB SD%';
