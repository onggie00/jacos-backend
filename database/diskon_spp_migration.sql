-- ============================================================
-- Modul: transaksi_spp_diskon — diskon bayar SPP >= 12 bulan (1 TA)
-- 2026-10-21 · Laragon lokal jacos_db (migrasi produksi terpisah)
-- Semua statement idempotent-safe utk di-run ulang.
-- ============================================================

-- 1) Kolom potongan pada transaksi_spp (omzet tagihan = setelah diskon)
ALTER TABLE transaksi_spp
  ADD COLUMN total_diskon BIGINT NOT NULL DEFAULT 0 COMMENT 'nominal potongan diskon yang melekat pada baris tagihan ini (0 = tanpa diskon)';

-- 2) Log pemberian diskon (1 baris transaksi_spp maksimal 1 diskon aktif)
CREATE TABLE IF NOT EXISTS transaksi_spp_diskon (
  id_diskon INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_transaksi INT NOT NULL COMMENT 'FK -> transaksi_spp.id_transaksi',
  id_setting INT UNSIGNED DEFAULT NULL COMMENT 'FK -> pengaturan_diskon_spp.id_setting (snapshot saat claim)',
  id_siswa_aktif INT NOT NULL,
  jenjang VARCHAR(25) NOT NULL,
  id_tingkatan INT DEFAULT NULL COMMENT 'tingkatan siswa saat diskon diberikan',
  id_tahun_ajaran INT NOT NULL,
  jenis_diskon VARCHAR(100) NOT NULL DEFAULT 'spp_bayar_1_tahun',
  persen_diskon DECIMAL(5,2) NOT NULL,
  nominal_diskon BIGINT NOT NULL COMMENT 'nominal potongan rupiah (setelah pembulatan)',
  nominal_awal BIGINT NOT NULL COMMENT 'SUM total_biaya baris gabungan sebelum diskon',
  jumlah_bulan INT NOT NULL COMMENT 'jumlah baris/bulan tagihan dalam satu pembayaran',
  sumber VARCHAR(20) NOT NULL DEFAULT 'apps' COMMENT 'apps | admin',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id_diskon),
  UNIQUE KEY uq_transaksi (id_transaksi),
  KEY idx_siswa (jenjang, id_siswa_aktif),
  KEY idx_setting (id_setting)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='log diskon SPP bayar 1 tahun ajaran';

-- 3) Pengaturan diskon per jenjang / per tingkatan (id_tingkatan NULL = semua tingkatan)
CREATE TABLE IF NOT EXISTS pengaturan_diskon_spp (
  id_setting INT UNSIGNED NOT NULL AUTO_INCREMENT,
  jenjang VARCHAR(25) NOT NULL,
  id_tingkatan INT DEFAULT NULL,
  min_pembayaran INT NOT NULL DEFAULT 12 COMMENT 'syarat minimal baris/bulan tagihan gabungan',
  persen_diskon DECIMAL(5,2) NOT NULL DEFAULT 3.00,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id_setting),
  KEY idx_jenjang_tingkatan (jenjang, id_tingkatan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='setting diskon SPP per jenjang/tingkatan';

-- Dummy default 3% per jenjang (skip bila sudah pernah di-seed)
INSERT INTO pengaturan_diskon_spp (jenjang, id_tingkatan, min_pembayaran, persen_diskon, aktif)
SELECT t.j, NULL, 12, 3.00, 1 FROM (
  SELECT 'sd' AS j UNION SELECT 'smp' UNION SELECT 'sma'
  UNION SELECT 'ft' UNION SELECT 'kb' UNION SELECT 'tk'
) t
WHERE NOT EXISTS (
  SELECT 1 FROM pengaturan_diskon_spp d
  WHERE d.jenjang = t.j AND d.id_tingkatan IS NULL
);

-- 4) Setting BNI baru (value kosong = diisi saat deploy/prod)
--    - SPP: bni_client_id_spp_{kb|tk|sd} + bni_secret_key_spp_{kb|tk|sd} (PATTERN = bni_client_id_spp / bni_secret_key_spp utk smp·sma)
--    - Pendaftaran: bni_client_id_{kb|tk|sd (bisa satukan bila rekening digabung; SD terpisah credential)
INSERT INTO pengaturan_akun (name_setting, value)
SELECT n, '' FROM (
  SELECT 'bni_client_id_spp_kb' AS n UNION SELECT 'bni_client_id_spp_tk' UNION SELECT 'bni_client_id_spp_sd'
  UNION SELECT 'bni_secret_key_spp_kb' UNION SELECT 'bni_secret_key_spp_tk' UNION SELECT 'bni_secret_key_spp_sd'
  UNION SELECT 'bni_client_id_kb' UNION SELECT 'bni_client_id_tk' UNION SELECT 'bni_client_id_sd'
) x
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun p WHERE p.name_setting = x.n);
