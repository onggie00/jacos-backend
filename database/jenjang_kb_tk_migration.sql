-- ============================================================================
-- Jacos — Jenjang KB & TK: MIGRASI LENGKAP (import sekali di target server)
-- Isi: tabel siswa/kelas/tingkatan/aktif/DU/spp KB-TK, seed tingkatan & biaya,
--      perms Aauth, menu, flag migrasi SD BNI, seed konten PSB website.
-- Catatan collation: file memakai utf8mb4_general_ci (MySQL 5.7-safe). Jika
--      server target MySQL 8 dengan tabel lama utf8mb4_0900_ai_ci, jalankan
--      konversi berikut SETELAH import agar join tidak mismatch:
--        ALTER TABLE <tabel_baru> CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
--      (daftar tabel: siswa_kb, siswa_tk, kelas_kb, kelas_tk, tingkatan_kb,
--       tingkatan_tk, siswa_kb_aktif, siswa_tk_aktif, spp_kb, spp_tk,
--       status_daftar_ulang_kb, status_daftar_ulang_tk)
-- Urutan section: 1 tabel tingkatan, 2 kelas, 3 siswa, 4 biaya, 5 perms,
--   6 status_daftar_ulang, 7 menu, 8 flag bank, 9 siswa_aktif, 9b kolom aktif,
--   10 spp + perms/menu, 10b setting_sync_ujian, 11 konten PSB website,
--   12 geser menu, 13 menu/perms DU, 14 nonaktif menu SMP/SMA/FT.
-- Seeder data dummy: lihat database/seed_siswa_kb_tk.sql
-- ============================================================================

-- ============================================================
-- Jacos — Jenjang KB (Kelompok Bermain) & TK
-- Fase 2: tabel + seed baru (TANPA ALTER tabel existing)
-- Kompatibel MySQL 5.7+ (tanpa CTE/functional index).
-- Collation utf8mb4_general_ci (aman utk 5.7; join antar tabel
-- baru-intern & ke kolom INT, tidak menyentuh string kolom tabel lama).
-- Idempotent: CREATE TABLE IF NOT EXISTS + INSERT ... WHERE NOT EXISTS.
-- ============================================================

-- 1. Tabel tingkatan master -----------------------------------
CREATE TABLE IF NOT EXISTS `tingkatan_kb` (
  `id_tingkatan_kb` int unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `usia_min` int DEFAULT NULL COMMENT 'usia minimal tahun pendaftaran',
  `usia_max` int DEFAULT NULL COMMENT 'usia maksimal tahun pendaftaran',
  `biaya_spp` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tingkatan_kb`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tingkatan_tk` (
  `id_tingkatan_tk` int unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `usia_min` int DEFAULT NULL COMMENT 'usia minimal tahun pendaftaran',
  `usia_max` int DEFAULT NULL COMMENT 'usia maksimal tahun pendaftaran',
  `biaya_spp` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tingkatan_tk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Seed tingkatan (idempotent)
INSERT INTO `tingkatan_kb` (`label`,`usia_min`,`usia_max`)
SELECT 'KB Kecil',2,3 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `tingkatan_kb` WHERE `label`='KB Kecil');
INSERT INTO `tingkatan_kb` (`label`,`usia_min`,`usia_max`)
SELECT 'KB Besar',3,4 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `tingkatan_kb` WHERE `label`='KB Besar');
INSERT INTO `tingkatan_kb` (`label`,`usia_min`,`usia_max`)
SELECT 'KB Umum',2,4 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `tingkatan_kb` WHERE `label`='KB Umum');
INSERT INTO `tingkatan_tk` (`label`,`usia_min`,`usia_max`)
SELECT 'TK A',4,5 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `tingkatan_tk` WHERE `label`='TK A');
INSERT INTO `tingkatan_tk` (`label`,`usia_min`,`usia_max`)
SELECT 'TK B',5,6 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `tingkatan_tk` WHERE `label`='TK B');

-- 2. Tabel kelas ----------------------------------------------
CREATE TABLE IF NOT EXISTS `kelas_kb` (
  `id_kelas_kb` int unsigned NOT NULL AUTO_INCREMENT,
  `id_tingkatan` int NOT NULL DEFAULT 0,
  `nama_kelas` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `id_tahun_ajaran` int DEFAULT NULL,
  PRIMARY KEY (`id_kelas_kb`),
  KEY `idx_kelas_kb_tingkatan` (`id_tingkatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `kelas_tk` (
  `id_kelas_tk` int unsigned NOT NULL AUTO_INCREMENT,
  `id_tingkatan` int NOT NULL DEFAULT 0,
  `nama_kelas` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `id_tahun_ajaran` int DEFAULT NULL,
  PRIMARY KEY (`id_kelas_tk`),
  KEY `idx_kelas_tk_tingkatan` (`id_tingkatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Tabel siswa (clone siswa_sd MINUS ppsbb/va_number_bri PLUS id_tingkatan) ----
CREATE TABLE IF NOT EXISTS `siswa_kb` (
  `id_siswa_kb` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_ms_office` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `no_peserta` varchar(100) NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `nisn` varchar(30) NOT NULL,
  `npsn` varchar(10) DEFAULT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `jenis_kelamin` varchar(20) NOT NULL,
  `agama` varchar(50) NOT NULL,
  `email_ms_office_ortu` varchar(255) NOT NULL,
  `nama_ibu` varchar(255) NOT NULL,
  `pekerjaan_ibu` varchar(255) NOT NULL,
  `notelp_ibu` varchar(14) NOT NULL,
  `nama_ayah` varchar(255) NOT NULL,
  `pekerjaan_ayah` varchar(255) NOT NULL,
  `notelp_ayah` varchar(14) NOT NULL,
  `alamat` text NOT NULL,
  `kelurahan` int DEFAULT NULL,
  `kecamatan` int DEFAULT NULL,
  `kota` int DEFAULT NULL,
  `provinsi` int DEFAULT NULL,
  `kode_pos` varchar(8) NOT NULL,
  `sekolah_asal` text NOT NULL,
  `provinsi_sekolah` varchar(20) DEFAULT NULL,
  `kota_sekolah` varchar(20) DEFAULT NULL,
  `kecamatan_sekolah` varchar(20) DEFAULT NULL,
  `kelurahan_sekolah` varchar(20) DEFAULT NULL,
  `foto_peserta` varchar(255) NOT NULL,
  `akte_lahir` varchar(255) NOT NULL,
  `kartu_keluarga` varchar(255) NOT NULL,
  `sumber_informasi` varchar(255) NOT NULL,
  `alasan_tertarik` varchar(255) DEFAULT NULL,
  `id_tingkatan` int NOT NULL DEFAULT 0,
  `is_mutasi` tinyint(1) NOT NULL DEFAULT '2',
  `kelas_mutasi` int DEFAULT NULL,
  `status_lulus` tinyint(1) NOT NULL DEFAULT '1',
  `cadangan_no` tinyint DEFAULT NULL,
  `no_transaksi` varchar(100) NOT NULL,
  `va_number` varchar(100) NOT NULL,
  `token_expired` datetime DEFAULT NULL,
  `is_show` smallint NOT NULL DEFAULT '1',
  `token_expired_ortu` datetime DEFAULT NULL,
  `token_ortu` varchar(255) DEFAULT NULL,
  `tahun_ajaran` varchar(30) DEFAULT NULL,
  `gelombang` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_at_ortu` datetime DEFAULT NULL,
  PRIMARY KEY (`id_siswa_kb`),
  KEY `idx_siswa_kb_tingkatan` (`id_tingkatan`),
  KEY `idx_siswa_kb_transaksi` (`no_transaksi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `siswa_tk` (
  `id_siswa_tk` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_ms_office` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `no_peserta` varchar(100) NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `nisn` varchar(30) NOT NULL,
  `npsn` varchar(10) DEFAULT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `jenis_kelamin` varchar(20) NOT NULL,
  `agama` varchar(50) NOT NULL,
  `email_ms_office_ortu` varchar(255) NOT NULL,
  `nama_ibu` varchar(255) NOT NULL,
  `pekerjaan_ibu` varchar(255) NOT NULL,
  `notelp_ibu` varchar(14) NOT NULL,
  `nama_ayah` varchar(255) NOT NULL,
  `pekerjaan_ayah` varchar(255) NOT NULL,
  `notelp_ayah` varchar(14) NOT NULL,
  `alamat` text NOT NULL,
  `kelurahan` int DEFAULT NULL,
  `kecamatan` int DEFAULT NULL,
  `kota` int DEFAULT NULL,
  `provinsi` int DEFAULT NULL,
  `kode_pos` varchar(8) NOT NULL,
  `sekolah_asal` text NOT NULL,
  `provinsi_sekolah` varchar(20) DEFAULT NULL,
  `kota_sekolah` varchar(20) DEFAULT NULL,
  `kecamatan_sekolah` varchar(20) DEFAULT NULL,
  `kelurahan_sekolah` varchar(20) DEFAULT NULL,
  `foto_peserta` varchar(255) NOT NULL,
  `akte_lahir` varchar(255) NOT NULL,
  `kartu_keluarga` varchar(255) NOT NULL,
  `sumber_informasi` varchar(255) NOT NULL,
  `alasan_tertarik` varchar(255) DEFAULT NULL,
  `id_tingkatan` int NOT NULL DEFAULT 0,
  `is_mutasi` tinyint(1) NOT NULL DEFAULT '2',
  `kelas_mutasi` int DEFAULT NULL,
  `status_lulus` tinyint(1) NOT NULL DEFAULT '1',
  `cadangan_no` tinyint DEFAULT NULL,
  `no_transaksi` varchar(100) NOT NULL,
  `va_number` varchar(100) NOT NULL,
  `token_expired` datetime DEFAULT NULL,
  `is_show` smallint NOT NULL DEFAULT '1',
  `token_expired_ortu` datetime DEFAULT NULL,
  `token_ortu` varchar(255) DEFAULT NULL,
  `tahun_ajaran` varchar(30) DEFAULT NULL,
  `gelombang` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_at_ortu` datetime DEFAULT NULL,
  PRIMARY KEY (`id_siswa_tk`),
  KEY `idx_siswa_tk_tingkatan` (`id_tingkatan`),
  KEY `idx_siswa_tk_transaksi` (`no_transaksi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Seed biaya ------------------------------------------------
INSERT INTO `biaya_pendaftaran` (`jenjang`,`nominal_pendaftaran`,`nominal_daftar_ulang`)
SELECT 'KB',1000000,0 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `biaya_pendaftaran` WHERE `jenjang`='KB');
INSERT INTO `biaya_pendaftaran` (`jenjang`,`nominal_pendaftaran`,`nominal_daftar_ulang`)
SELECT 'TK',2100000,0 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `biaya_pendaftaran` WHERE `jenjang`='TK');
INSERT INTO `biaya_spp` (`nominal`,`jenjang`)
SELECT 1600000,'kb' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `biaya_spp` WHERE `jenjang`='kb');
INSERT INTO `biaya_spp` (`nominal`,`jenjang`)
SELECT 1700000,'tk' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `biaya_spp` WHERE `jenjang`='tk');

-- 5. Aauth permissions (pola modul CiCOOL per jenjang) ----------
-- perm: siswa_kb/kelas_kb/tingkatan_kb (dan _tk) list/add/update/delete/view/export
INSERT INTO `aauth_perms` (`name`)
SELECT t.p FROM (
  SELECT 'siswa_kb_list' p UNION SELECT 'siswa_kb_add' UNION SELECT 'siswa_kb_update'
  UNION SELECT 'siswa_kb_delete' UNION SELECT 'siswa_kb_view' UNION SELECT 'siswa_kb_export'
  UNION SELECT 'kelas_kb_list' UNION SELECT 'kelas_kb_add' UNION SELECT 'kelas_kb_update'
  UNION SELECT 'kelas_kb_delete' UNION SELECT 'kelas_kb_view' UNION SELECT 'kelas_kb_export'
  UNION SELECT 'tingkatan_kb_list' UNION SELECT 'tingkatan_kb_add' UNION SELECT 'tingkatan_kb_update'
  UNION SELECT 'tingkatan_kb_delete' UNION SELECT 'tingkatan_kb_view' UNION SELECT 'tingkatan_kb_export'
  UNION SELECT 'siswa_tk_list' UNION SELECT 'siswa_tk_add' UNION SELECT 'siswa_tk_update'
  UNION SELECT 'siswa_tk_delete' UNION SELECT 'siswa_tk_view' UNION SELECT 'siswa_tk_export'
  UNION SELECT 'kelas_tk_list' UNION SELECT 'kelas_tk_add' UNION SELECT 'kelas_tk_update'
  UNION SELECT 'kelas_tk_delete' UNION SELECT 'kelas_tk_view' UNION SELECT 'kelas_tk_export'
  UNION SELECT 'tingkatan_tk_list' UNION SELECT 'tingkatan_tk_add' UNION SELECT 'tingkatan_tk_update'
  UNION SELECT 'tingkatan_tk_delete' UNION SELECT 'tingkatan_tk_view' UNION SELECT 'tingkatan_tk_export'
) t
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` a WHERE a.`name` = t.p);

-- map ke grup Admin PSB (25) + Keuangan (10) — Admin (1) bypass Aauth
INSERT INTO `aauth_perm_to_group` (`perm_id`,`group_id`)
SELECT a.id, g.gid
FROM `aauth_perms` a
JOIN (SELECT 25 gid UNION SELECT 10) g
WHERE a.`name` LIKE 'siswa\_kb\_%' OR a.`name` LIKE 'kelas\_kb\_%' OR a.`name` LIKE 'tingkatan\_kb\_%'
   OR a.`name` LIKE 'siswa\_tk\_%' OR a.`name` LIKE 'kelas\_tk\_%' OR a.`name` LIKE 'tingkatan\_tk\_%'
AND NOT EXISTS (
  SELECT 1 FROM `aauth_perm_to_group` x
  WHERE x.`perm_id`=a.id AND x.`group_id`=g.gid
);

-- ============================================================
-- ROLLBACK (reversibel — data KB/TK ikut hilang):
-- DROP TABLE IF EXISTS siswa_kb, siswa_tk, kelas_kb, kelas_tk, tingkatan_kb, tingkatan_tk;
-- DELETE FROM biaya_pendaftaran WHERE jenjang IN ('KB','TK');
-- DELETE FROM biaya_spp WHERE jenjang IN ('kb','tk');
-- DELETE FROM aauth_perm_to_group WHERE perm_id IN (SELECT id FROM aauth_perms
--   WHERE name LIKE 'siswa\_kb\_%' OR name LIKE 'kelas\_kb\_%' OR name LIKE 'tingkatan\_kb\_%'
--      OR name LIKE 'siswa\_tk\_%' OR name LIKE 'kelas\_tk\_%' OR name LIKE 'tingkatan\_tk\_%');
-- DELETE FROM aauth_perms WHERE name LIKE 'siswa\_kb\_%' OR name LIKE 'kelas\_kb\_%'
--   OR name LIKE 'tingkatan\_kb\_%' OR name LIKE 'siswa\_tk\_%' OR name LIKE 'kelas\_tk\_%'
--   OR name LIKE 'tingkatan\_tk\_%';
-- Menu rows (dibuat terpisah, lihat bagian menu di laporan) — DELETE per link LIKE '%_kb' / '%_tk'
-- ============================================================

-- 6. Tabel pendukung daftar ulang (tanpa modul — dipakai alur edit siswa KB/TK) ----
CREATE TABLE IF NOT EXISTS `status_daftar_ulang_kb` (
  `id_daftar_ulang` int unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa_kb` int NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `slip_pembayaran` varchar(255) DEFAULT NULL,
  `kwitansi` varchar(255) DEFAULT NULL,
  `kartu_sementara` varchar(255) DEFAULT NULL,
  `tanggal_lulus` datetime DEFAULT NULL,
  `tgl_daftar_ulang` date DEFAULT NULL,
  `va_bri` varchar(30) DEFAULT NULL,
  `tgl_aktivasi` datetime DEFAULT NULL,
  `tgl_bayar` datetime DEFAULT NULL,
  `custom_payment` int DEFAULT NULL,
  PRIMARY KEY (`id_daftar_ulang`),
  KEY `idx_du_kb_siswa` (`id_siswa_kb`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `status_daftar_ulang_tk` (
  `id_daftar_ulang` int unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa_tk` int NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `slip_pembayaran` varchar(255) DEFAULT NULL,
  `kwitansi` varchar(255) DEFAULT NULL,
  `kartu_sementara` varchar(255) DEFAULT NULL,
  `tanggal_lulus` datetime DEFAULT NULL,
  `tgl_daftar_ulang` date DEFAULT NULL,
  `va_bri` varchar(30) DEFAULT NULL,
  `tgl_aktivasi` datetime DEFAULT NULL,
  `tgl_bayar` datetime DEFAULT NULL,
  `custom_payment` int DEFAULT NULL,
  PRIMARY KEY (`id_daftar_ulang`),
  KEY `idx_du_tk_siswa` (`id_siswa_tk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Menu (admin panel) — parent 23 = "Siswa", parent 220 = "Grafik PSB" ------------
INSERT INTO `menu` (`label`,`type`,`link`,`sort`,`parent`,`menu_type_id`,`active`)
SELECT t.* FROM (
  SELECT 'Siswa KB' label,'menu' type,'administrator/parent_kb' link,600 sort,23 parent,1 menu_type_id,1 active UNION ALL
  SELECT 'Siswa TK','menu','administrator/parent_tk',601,23,1,1 UNION ALL
  SELECT 'KB','menu','administrator/dashboard_psb/grafik_kb',68,220,1,1 UNION ALL
  SELECT 'TK','menu','administrator/dashboard_psb/grafik_tk',69,220,1,1
) t WHERE NOT EXISTS (SELECT 1 FROM `menu` m WHERE m.`link` = t.`link`);
-- anak menu (parent id dinamis — sesuaikan @kb_parent/@tk_parent dengan id parent yg baru dibuat):
-- Daftar Siswa administrator/siswa_kb, Tingkatan administrator/tingkatan_kb, Kelas administrator/kelas_kb → parent "Siswa KB"
-- Daftar Siswa administrator/siswa_tk, Tingkatan administrator/tingkatan_tk, Kelas administrator/kelas_tk → parent "Siswa TK"

-- ROLLBACK menu:
-- DELETE FROM menu WHERE link IN ('administrator/parent_kb','administrator/parent_tk');
-- DELETE FROM menu WHERE link IN ('administrator/siswa_kb','administrator/tingkatan_kb','administrator/kelas_kb','administrator/siswa_tk','administrator/tingkatan_tk','administrator/kelas_tk');
-- DELETE FROM menu WHERE link IN ('administrator/dashboard_psb/grafik_kb','administrator/dashboard_psb/grafik_tk');

-- 8. FASE 3 — flag rollback bank per alur SD (default 'BRI' = perilaku lama) --------
INSERT INTO `pengaturan_akun` (`name_setting`,`value`)
SELECT 'bank_psb_sd','BRI' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `pengaturan_akun` WHERE `name_setting`='bank_psb_sd');
INSERT INTO `pengaturan_akun` (`name_setting`,`value`)
SELECT 'bank_mutasi_sd','BRI' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `pengaturan_akun` WHERE `name_setting`='bank_mutasi_sd');
INSERT INTO `pengaturan_akun` (`name_setting`,`value`)
SELECT 'bank_daftar_ulang_sd','BRI' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `pengaturan_akun` WHERE `name_setting`='bank_daftar_ulang_sd');
-- ROLLBACK per alur: UPDATE pengaturan_akun SET value='BRI' WHERE name_setting='bank_psb_sd';

-- 9. Fase 2b — siswa aktif KB/TK (clone siswa_sd_aktif, tanpa FK) -------------------
CREATE TABLE IF NOT EXISTS `siswa_kb_aktif` (
  `id_siswa_kb_aktif` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) NOT NULL,
  `nis` varchar(255) DEFAULT NULL,
  `id_tahun_ajaran` int DEFAULT NULL,
  `id_kelas` int DEFAULT NULL,
  `id_siswa_kb` int DEFAULT '0',
  `nomor_peserta_ujian` varchar(30) DEFAULT NULL,
  `acc_ujian` tinyint(1) NOT NULL,
  `kewarganegaraan` varchar(255) DEFAULT NULL,
  `nik` varchar(100) DEFAULT NULL,
  `golongan_darah` varchar(5) DEFAULT NULL,
  `telp` varchar(20) DEFAULT NULL,
  `pendidikan_ayah` varchar(255) DEFAULT NULL,
  `pendidikan_ibu` varchar(255) DEFAULT NULL,
  `penghasilan_ayah` int DEFAULT NULL,
  `penghasilan_ibu` int DEFAULT NULL,
  `tgl_lahir_ayah` date DEFAULT NULL,
  `tgl_lahir_ibu` date DEFAULT NULL,
  `is_active` smallint NOT NULL DEFAULT '1',
  `foto_profil` varchar(255) DEFAULT NULL,
  `file_raport` text,
  `device_id_siswa` text,
  `device_id_ortu` text,
  `spp_custom` int NOT NULL DEFAULT '0',
  `spp_type` varchar(10) DEFAULT 'FULL' COMMENT 'FULL,HALF,FREE',
  `deleted_at` datetime DEFAULT NULL,
  `deleted_at_ortu` datetime DEFAULT NULL,
  PRIMARY KEY (`id_siswa_kb_aktif`),
  KEY `idx_siswa_kb_aktif_nis` (`nis`),
  KEY `idx_siswa_kb_aktif_siswa` (`id_siswa_kb`),
  KEY `idx_siswa_kb_aktif_kelas` (`id_kelas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `siswa_tk_aktif` (
  `id_siswa_tk_aktif` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) NOT NULL,
  `nis` varchar(255) DEFAULT NULL,
  `id_tahun_ajaran` int DEFAULT NULL,
  `id_kelas` int DEFAULT NULL,
  `id_siswa_tk` int DEFAULT '0',
  `nomor_peserta_ujian` varchar(30) DEFAULT NULL,
  `acc_ujian` tinyint(1) NOT NULL,
  `kewarganegaraan` varchar(255) DEFAULT NULL,
  `nik` varchar(100) DEFAULT NULL,
  `golongan_darah` varchar(5) DEFAULT NULL,
  `telp` varchar(20) DEFAULT NULL,
  `pendidikan_ayah` varchar(255) DEFAULT NULL,
  `pendidikan_ibu` varchar(255) DEFAULT NULL,
  `penghasilan_ayah` int DEFAULT NULL,
  `penghasilan_ibu` int DEFAULT NULL,
  `tgl_lahir_ayah` date DEFAULT NULL,
  `tgl_lahir_ibu` date DEFAULT NULL,
  `is_active` smallint NOT NULL DEFAULT '1',
  `foto_profil` varchar(255) DEFAULT NULL,
  `file_raport` text,
  `device_id_siswa` text,
  `device_id_ortu` text,
  `spp_custom` int NOT NULL DEFAULT '0',
  `spp_type` varchar(10) DEFAULT 'FULL' COMMENT 'FULL,HALF,FREE',
  `deleted_at` datetime DEFAULT NULL,
  `deleted_at_ortu` datetime DEFAULT NULL,
  PRIMARY KEY (`id_siswa_tk_aktif`),
  KEY `idx_siswa_tk_aktif_nis` (`nis`),
  KEY `idx_siswa_tk_aktif_siswa` (`id_siswa_tk`),
  KEY `idx_siswa_tk_aktif_kelas` (`id_kelas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- perms
INSERT INTO `aauth_perms` (`name`)
SELECT t.p FROM (
  SELECT 'siswa_kb_aktif_list' p UNION SELECT 'siswa_kb_aktif_add' UNION SELECT 'siswa_kb_aktif_update'
  UNION SELECT 'siswa_kb_aktif_delete' UNION SELECT 'siswa_kb_aktif_view' UNION SELECT 'siswa_kb_aktif_export'
  UNION SELECT 'siswa_tk_aktif_list' UNION SELECT 'siswa_tk_aktif_add' UNION SELECT 'siswa_tk_aktif_update'
  UNION SELECT 'siswa_tk_aktif_delete' UNION SELECT 'siswa_tk_aktif_view' UNION SELECT 'siswa_tk_aktif_export'
) t
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` a WHERE a.`name` = t.p);
INSERT INTO `aauth_perm_to_group` (`perm_id`,`group_id`)
SELECT a.id, g.gid
FROM `aauth_perms` a
JOIN (SELECT 25 gid UNION SELECT 10) g
WHERE a.`name` LIKE 'siswa\_kb_aktif\_%' OR a.`name` LIKE 'siswa\_tk_aktif\_%'
AND NOT EXISTS (
  SELECT 1 FROM `aauth_perm_to_group` x WHERE x.`perm_id`=a.id AND x.`group_id`=g.gid
);

-- menu anak "Siswa Aktif" (parent = menu "Daftar Siswa" KB/TK? ikut pola SD: anak dari parent jenjang)
INSERT INTO `menu` (`label`,`type`,`link`,`sort`,`parent`,`menu_type_id`,`active`)
SELECT 'Siswa Aktif','menu','administrator/siswa_kb_aktif',999,@kb_parent,1,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `link`='administrator/siswa_kb_aktif');
INSERT INTO `menu` (`label`,`type`,`link`,`sort`,`parent`,`menu_type_id`,`active`)
SELECT 'Siswa Aktif','menu','administrator/siswa_tk_aktif',999,@tk_parent,1,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `link`='administrator/siswa_tk_aktif');
-- ROLLBACK: DROP TABLE siswa_kb_aktif, siswa_tk_aktif;
-- DELETE perms/menunya (LIKE 'siswa_kb_aktif_%' / link administrator/siswa_kb_aktif dst)

-- 9b. Fase 2b lanjutan — kolom tingkatan & tgl_lahir utk import massal historis ----
ALTER TABLE `siswa_kb_aktif`
  ADD COLUMN `id_tingkatan` int NOT NULL DEFAULT '0' COMMENT '0 = tidak ditentukan (lookup label tingkatan_kb saat import)',
  ADD COLUMN `tgl_lahir` date DEFAULT NULL;
ALTER TABLE `siswa_tk_aktif`
  ADD COLUMN `id_tingkatan` int NOT NULL DEFAULT '0',
  ADD COLUMN `tgl_lahir` date DEFAULT NULL;

-- 10. Fase B — SPP KB/TK (clone spp_sd; transaksi_spp shared, tidak diubah) --------
CREATE TABLE IF NOT EXISTS `spp_kb` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_siswa_aktif` int NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `nominal` int NOT NULL,
  `juli` varchar(50) DEFAULT NULL,
  `agustus` varchar(50) DEFAULT NULL,
  `september` varchar(50) DEFAULT NULL,
  `oktober` varchar(50) DEFAULT NULL,
  `november` varchar(50) DEFAULT NULL,
  `desember` varchar(50) DEFAULT NULL,
  `januari` varchar(50) DEFAULT NULL,
  `februari` varchar(50) DEFAULT NULL,
  `maret` varchar(50) DEFAULT NULL,
  `april` varchar(50) DEFAULT NULL,
  `mei` varchar(50) DEFAULT NULL,
  `juni` varchar(50) DEFAULT NULL,
  `id_tahun_ajaran` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_spp_kb_siswa` (`id_siswa_aktif`),
  KEY `idx_spp_kb_ta` (`id_tahun_ajaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `spp_tk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_siswa_aktif` int NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `nominal` int NOT NULL,
  `juli` varchar(50) DEFAULT NULL,
  `agustus` varchar(50) DEFAULT NULL,
  `september` varchar(50) DEFAULT NULL,
  `oktober` varchar(50) DEFAULT NULL,
  `november` varchar(50) DEFAULT NULL,
  `desember` varchar(50) DEFAULT NULL,
  `januari` varchar(50) DEFAULT NULL,
  `februari` varchar(50) DEFAULT NULL,
  `maret` varchar(50) DEFAULT NULL,
  `april` varchar(50) DEFAULT NULL,
  `mei` varchar(50) DEFAULT NULL,
  `juni` varchar(50) DEFAULT NULL,
  `id_tahun_ajaran` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_spp_tk_siswa` (`id_siswa_aktif`),
  KEY `idx_spp_tk_ta` (`id_tahun_ajaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- perms
INSERT INTO `aauth_perms` (`name`)
SELECT t.p FROM (
  SELECT 'spp_kb_list' p UNION SELECT 'spp_kb_add' UNION SELECT 'spp_kb_update'
  UNION SELECT 'spp_kb_delete' UNION SELECT 'spp_kb_view' UNION SELECT 'spp_kb_export'
  UNION SELECT 'spp_tk_list' UNION SELECT 'spp_tk_add' UNION SELECT 'spp_tk_update'
  UNION SELECT 'spp_tk_delete' UNION SELECT 'spp_tk_view' UNION SELECT 'spp_tk_export'
) t
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` a WHERE a.`name` = t.p);
INSERT INTO `aauth_perm_to_group` (`perm_id`,`group_id`)
SELECT a.id, g.gid
FROM `aauth_perms` a
JOIN (SELECT 25 gid UNION SELECT 10) g
WHERE a.`name` LIKE 'spp\_kb\_%' OR a.`name` LIKE 'spp\_tk\_%'
AND NOT EXISTS (SELECT 1 FROM `aauth_perm_to_group` x WHERE x.`perm_id`=a.id AND x.`group_id`=g.gid);

-- menu di grup SPP keuangan (parent 392)
INSERT INTO `menu` (`label`,`type`,`link`,`sort`,`parent`,`menu_type_id`,`active`)
SELECT 'SPP KB','menu','administrator/spp_kb',104,392,1,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `link`='administrator/spp_kb');
INSERT INTO `menu` (`label`,`type`,`link`,`sort`,`parent`,`menu_type_id`,`active`)
SELECT 'SPP TK','menu','administrator/spp_tk',105,392,1,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `link`='administrator/spp_tk');
-- 10b. Fase B — baris setting_sync_ujian utk KB/TK (pola jenjang lain) --------------
INSERT INTO `setting_sync_ujian` (`jenjang`,`tanggal_terakhir_bayar`)
SELECT 'kb','2026-05-01' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `setting_sync_ujian` WHERE `jenjang`='kb');
INSERT INTO `setting_sync_ujian` (`jenjang`,`tanggal_terakhir_bayar`)
SELECT 'tk','2026-05-01' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `setting_sync_ujian` WHERE `jenjang`='tk');

-- 11. Seed konten PSB website utk KB/TK (Cek_data/Cek_pengumuman/Cek_email/Cek_tanggal_lahir)
INSERT INTO `pengumuman_ucapan` (`jenjang`,`ucapan_lulus`,`ucapan_tidak_lulus`,`ucapan_cadangan`,`ucapan_belum_tersedia`)
SELECT 'KB','Selamat, anak Bapak/Ibu dinyatakan LULUS.','Mohon maaf, belum lulus.','Anda dinyatakan CADANGAN.','Pengumuman belum tersedia.' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `pengumuman_ucapan` WHERE `jenjang`='KB');
INSERT INTO `pengumuman_ucapan` (`jenjang`,`ucapan_lulus`,`ucapan_tidak_lulus`,`ucapan_cadangan`,`ucapan_belum_tersedia`)
SELECT 'TK','Selamat, anak Bapak/Ibu dinyatakan LULUS.','Mohon maaf, belum lulus.','Anda dinyatakan CADANGAN.','Pengumuman belum tersedia.' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `pengumuman_ucapan` WHERE `jenjang`='TK');
INSERT INTO `judul_kartu_sementara` (`judul`,`jenjang`)
SELECT 'Kartu Siswa Sementara KB','kb' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `judul_kartu_sementara` WHERE `jenjang`='kb');
INSERT INTO `judul_kartu_sementara` (`judul`,`jenjang`)
SELECT 'Kartu Siswa Sementara TK','tk' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `judul_kartu_sementara` WHERE `jenjang`='tk');
INSERT INTO `pengaturan_tanggal_lahir` (`jenjang`,`date`)
SELECT 'kb','2020-12-01' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `pengaturan_tanggal_lahir` WHERE `jenjang`='kb');
INSERT INTO `pengaturan_tanggal_lahir` (`jenjang`,`date`)
SELECT 'tk','2020-12-01' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `pengaturan_tanggal_lahir` WHERE `jenjang`='tk');

-- 12. Geser menu Siswa KB/TK ke atas Siswa SD ---------------------------------------
UPDATE `menu` SET `sort`=194 WHERE `link`='administrator/parent_kb';
UPDATE `menu` SET `sort`=195 WHERE `link`='administrator/parent_tk';

-- 13. Modul Status Daftar Ulang KB/TK: menu + perms ---------------------------------
INSERT INTO `aauth_perms` (`name`)
SELECT t.p FROM (
  SELECT 'status_daftar_ulang_kb_list' p UNION SELECT 'status_daftar_ulang_kb_add' UNION SELECT 'status_daftar_ulang_kb_update'
  UNION SELECT 'status_daftar_ulang_kb_delete' UNION SELECT 'status_daftar_ulang_kb_view' UNION SELECT 'status_daftar_ulang_kb_export'
  UNION SELECT 'status_daftar_ulang_tk_list' UNION SELECT 'status_daftar_ulang_tk_add' UNION SELECT 'status_daftar_ulang_tk_update'
  UNION SELECT 'status_daftar_ulang_tk_delete' UNION SELECT 'status_daftar_ulang_tk_view' UNION SELECT 'status_daftar_ulang_tk_export'
) t
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` a WHERE a.`name` = t.p);
INSERT INTO `aauth_perm_to_group` (`perm_id`,`group_id`)
SELECT a.id, g.gid
FROM `aauth_perms` a
JOIN (SELECT 25 gid UNION SELECT 10) g
WHERE a.`name` LIKE 'status\_daftar\_ulang\_kb\_%' OR a.`name` LIKE 'status\_daftar\_ulang\_tk\_%'
AND NOT EXISTS (SELECT 1 FROM `aauth_perm_to_group` x WHERE x.`perm_id`=a.id AND x.`group_id`=g.gid);
INSERT INTO `menu` (`label`,`type`,`link`,`sort`,`parent`,`menu_type_id`,`active`)
SELECT 'Status Daftar Ulang','menu','administrator/status_daftar_ulang_kb',608,(SELECT id FROM `menu` WHERE `link`='administrator/parent_kb'),1,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `link`='administrator/status_daftar_ulang_kb');
INSERT INTO `menu` (`label`,`type`,`link`,`sort`,`parent`,`menu_type_id`,`active`)
SELECT 'Status Daftar Ulang','menu','administrator/status_daftar_ulang_tk',609,(SELECT id FROM `menu` WHERE `link`='administrator/parent_tk'),1,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `link`='administrator/status_daftar_ulang_tk');

-- 14. Nonaktifkan menu khusus SMP/SMA/FT (modul tetap; aktifkan kembali via active=1)
-- id list di-generate dari query jenjang-spesifik (smp/sma/ft saja, SD & KB/TK tetap aktif)
UPDATE menu SET active=0 WHERE id IN (28,29,30,31,32,39,40,43,44,45,46,72,74,75,76,77,78,79,82,83,84,94,95,96,97,98,100,101,102,107,110,111,112,119,120,121,123,124,126,127,129,130,136,137,138,145,146,147,149,150,151,155,157,158,169,170,173,174,177,178,187,188,190,191,192,204,205,206,212,213,222,223,224,229,230,231,239,240,241,243,244,245,247,248,249,252,253,254,257,258,259,260,261,262,265,266,267,269,270,271,280,281,282,284,285,286,288,289,290,292,293,294,296,297,298,313,314,315,319,320,321,322,323,324,332,333,334,336,337,338,342,343,344,357,358,359,366,367,368,384,396,397,442);
-- submenu PSB KB/TK di menu Data PSB (sejajar PSB SD)
INSERT INTO `menu` (`label`,`type`,`link`,`sort`,`parent`,`menu_type_id`,`active`)
SELECT 'PSB KB','menu','administrator/dashboard_psb/grafik_kb',5,383,1,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `link`='administrator/dashboard_psb/grafik_kb' AND `parent`=383);
INSERT INTO `menu` (`label`,`type`,`link`,`sort`,`parent`,`menu_type_id`,`active`)
SELECT 'PSB TK','menu','administrator/dashboard_psb/grafik_tk',6,383,1,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `link`='administrator/dashboard_psb/grafik_tk' AND `parent`=383);
