-- ============================================================================
-- 006_guru_kb_tk.sql — Fase GURU.1 (modul Guru KB & Guru TK)
-- Jalankan: mysql --no-defaults -uroot jacos_db < 006_guru_kb_tk.sql
-- Idempoten: aman dijalankan 2x. Rollback: 006_rollback.sql.
-- Catatan skema (beda dari guru_sd):
--   - satuan_pendidikan default 'KB' / 'TK'
--   - kode_mapel NOT NULL DEFAULT '' (mapel tidak dipakai untuk KB/TK)
--   - mapel_nomor NOT NULL DEFAULT 0
--   - id_mapel NULL boleh (kolom tetap ada demi kesejajaran skema, tidak
--     dipakai UI KB/TK)
--   - foto_profil / login_token NOT NULL DEFAULT '' (guru_sd TANPA default;
--     di sini diberi default agar INSERT tanpa kolom tidak gagal)
--   - UNIQUE uq_emp_code (tabel baru kosong saat dibuat)
-- ============================================================================

-- ---------------------------------------------------------------------------
-- 1. Tabel guru_kb & guru_tk (mirror guru_sd)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `guru_kb` (
  `id_guru` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) NOT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nuptk` varchar(255) DEFAULT NULL,
  `npp` varchar(255) DEFAULT NULL,
  `npwp` varchar(255) DEFAULT NULL,
  `alamat` text,
  `agama` varchar(255) DEFAULT NULL,
  `jenis_kelamin` varchar(255) DEFAULT NULL,
  `status_menikah` varchar(255) DEFAULT NULL,
  `jumlah_anak` int DEFAULT NULL,
  `no_telp` varchar(25) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id_posisi` int DEFAULT NULL,
  `satuan_pendidikan` varchar(255) DEFAULT 'KB',
  `unit` varchar(255) DEFAULT NULL,
  `id_mapel` int DEFAULT NULL,
  `status_kepegawaian` varchar(255) DEFAULT NULL,
  `informasi_kepala_pimpinan` varchar(255) DEFAULT NULL,
  `emp_code` varchar(255) DEFAULT NULL,
  `keterangan_jabatan` varchar(50) DEFAULT NULL,
  `jam_ajar` varchar(50) DEFAULT NULL,
  `kode_mapel` varchar(20) NOT NULL DEFAULT '',
  `mapel_nomor` int NOT NULL DEFAULT 0,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL,
  `universitas` varchar(200) DEFAULT NULL,
  `jurusan` varchar(150) DEFAULT NULL,
  `tahun_lulus` int DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `token_expired` datetime DEFAULT NULL,
  `email_ms_office` varchar(50) DEFAULT NULL,
  `foto_profil` text NOT NULL,
  `no_kk` varchar(50) DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `slip_gaji` text,
  `presensi_role` varchar(50) DEFAULT 'GURUKB',
  `device_id` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `login_token` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_guru`),
  UNIQUE KEY `uq_emp_code` (`emp_code`),
  KEY `idx_npp` (`npp`),
  KEY `idx_email_ms_office` (`email_ms_office`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `guru_tk` (
  `id_guru` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) NOT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nuptk` varchar(255) DEFAULT NULL,
  `npp` varchar(255) DEFAULT NULL,
  `npwp` varchar(255) DEFAULT NULL,
  `alamat` text,
  `agama` varchar(255) DEFAULT NULL,
  `jenis_kelamin` varchar(255) DEFAULT NULL,
  `status_menikah` varchar(255) DEFAULT NULL,
  `jumlah_anak` int DEFAULT NULL,
  `no_telp` varchar(25) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id_posisi` int DEFAULT NULL,
  `satuan_pendidikan` varchar(255) DEFAULT 'TK',
  `unit` varchar(255) DEFAULT NULL,
  `id_mapel` int DEFAULT NULL,
  `status_kepegawaian` varchar(255) DEFAULT NULL,
  `informasi_kepala_pimpinan` varchar(255) DEFAULT NULL,
  `emp_code` varchar(255) DEFAULT NULL,
  `keterangan_jabatan` varchar(50) DEFAULT NULL,
  `jam_ajar` varchar(50) DEFAULT NULL,
  `kode_mapel` varchar(20) NOT NULL DEFAULT '',
  `mapel_nomor` int NOT NULL DEFAULT 0,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL,
  `universitas` varchar(200) DEFAULT NULL,
  `jurusan` varchar(150) DEFAULT NULL,
  `tahun_lulus` int DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `token_expired` datetime DEFAULT NULL,
  `email_ms_office` varchar(50) DEFAULT NULL,
  `foto_profil` text NOT NULL,
  `no_kk` varchar(50) DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `slip_gaji` text,
  `presensi_role` varchar(50) DEFAULT 'GURUTK',
  `device_id` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `login_token` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_guru`),
  UNIQUE KEY `uq_emp_code` (`emp_code`),
  KEY `idx_npp` (`npp`),
  KEY `idx_email_ms_office` (`email_ms_office`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ---------------------------------------------------------------------------
-- 2. Posisi Guru (dedupe by nama_posisi)
-- ---------------------------------------------------------------------------
INSERT INTO `posisi_guru` (`nama_posisi`)
SELECT 'Guru KB' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `posisi_guru` WHERE `nama_posisi` = 'Guru KB');

INSERT INTO `posisi_guru` (`nama_posisi`)
SELECT 'Guru TK' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `posisi_guru` WHERE `nama_posisi` = 'Guru TK');

-- ---------------------------------------------------------------------------
-- 3. Permissions modul (guru_kb_* / guru_tk_*) — dedupe by name
-- ---------------------------------------------------------------------------
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_kb_list',   'Melihat daftar guru KB' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_kb_list');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_kb_add',    'Menambah guru KB' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_kb_add');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_kb_update', 'Mengubah guru KB' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_kb_update');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_kb_delete', 'Menghapus guru KB' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_kb_delete');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_kb_view',   'Melihat detail guru KB' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_kb_view');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_kb_export', 'Export data guru KB' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_kb_export');

INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_tk_list',   'Melihat daftar guru TK' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_tk_list');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_tk_add',    'Menambah guru TK' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_tk_add');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_tk_update', 'Mengubah guru TK' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_tk_update');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_tk_delete', 'Menghapus guru TK' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_tk_delete');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_tk_view',   'Melihat detail guru TK' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_tk_view');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'guru_tk_export', 'Export data guru TK' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'guru_tk_export');

-- ---------------------------------------------------------------------------
-- 4. Permissions menu (label sidebar: menu_<label snake lowercase>)
--    Pemegang menu_guru otomatis lihat anak Guru KB/Guru TK; parent 'Guru'
--    tetap tergantung menu_guru (sudah ada).
-- ---------------------------------------------------------------------------
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'menu_guru_kb', 'Menu Guru KB' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'menu_guru_kb');
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'menu_guru_tk', 'Menu Guru TK' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'menu_guru_tk');

-- ---------------------------------------------------------------------------
-- 5. Menu sidebar: 'Guru KB' dan 'Guru TK' di bawah menu 'Guru' (parent sesuai
--    baris menu label 'Guru' — diverifikasi A-1), lalu anak 'Data Guru'.
--    Dedupe pakai link. Sort dipilih di jalur kosong setelah Guru SD (125) dan
--    sebelum Data KPI (144): Guru KB 141 (+ anak 142), Guru TK 145 (+ anak 146).
-- ---------------------------------------------------------------------------
INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`)
SELECT 'Guru KB', 'menu', 'default', 'administrator/guru_kb', 141,
       (SELECT id FROM (SELECT id FROM `menu` WHERE label = 'Guru' AND parent = 390 LIMIT 1) x),
       '', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE link = 'administrator/guru_kb') y);

INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`)
SELECT 'Guru TK', 'menu', 'default', 'administrator/guru_tk', 145,
       (SELECT id FROM (SELECT id FROM `menu` WHERE label = 'Guru' AND parent = 390 LIMIT 1) x),
       '', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE link = 'administrator/guru_tk') y);

-- Anak: label 'Data Guru' dengan link menu masing-masing (pola 117/255).
INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`)
SELECT 'Data Guru', 'menu', 'default', 'administrator/guru_kb', 142,
       (SELECT id FROM (SELECT id FROM `menu` WHERE label = 'Guru KB' AND link = 'administrator/guru_kb' LIMIT 1) x),
       '', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE link = 'administrator/guru_kb' AND label = 'Data Guru') y);

INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`)
SELECT 'Data Guru', 'menu', 'default', 'administrator/guru_tk', 146,
       (SELECT id FROM (SELECT id FROM `menu` WHERE label = 'Guru TK' AND link = 'administrator/guru_tk' LIMIT 1) x),
       '', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE link = 'administrator/guru_tk' AND label = 'Data Guru') y);

-- ---------------------------------------------------------------------------
-- 6. Grant: grup pemegang guru_sd_list mendapat semua perm guru_kb_*/guru_tk_*
--    (tanpa hardcode nama grup; grup Admin id 1 otomatis punya semua via
--    is_admin()); grup pemegang menu_guru mendapat menu_guru_kb/menu_guru_tk.
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `aauth_perm_to_group` (`perm_id`, `group_id`)
SELECT pk.id, pg.group_id
FROM `aauth_perms` pk
JOIN `aauth_perms` src ON src.name = 'guru_sd_list'
JOIN `aauth_perm_to_group` pg ON pg.perm_id = src.id
WHERE pk.name IN (
  'guru_kb_list','guru_kb_add','guru_kb_update','guru_kb_delete','guru_kb_view','guru_kb_export',
  'guru_tk_list','guru_tk_add','guru_tk_update','guru_tk_delete','guru_tk_view','guru_tk_export'
);

INSERT IGNORE INTO `aauth_perm_to_group` (`perm_id`, `group_id`)
SELECT pk.id, pg.group_id
FROM `aauth_perms` pk
JOIN `aauth_perms` src ON src.name = 'menu_guru'
JOIN `aauth_perm_to_group` pg ON pg.perm_id = src.id
WHERE pk.name IN ('menu_guru_kb', 'menu_guru_tk');

-- grante menu_data_guru sudah ada (dipakai anak 'Data Guru' bersama Guru SD).
