-- fase1_index_email.sql
-- Index untuk lookup switch bahasa berdasarkan email (setting_bahasa).
-- Target kolom: email_ms_office / email_ms_office_ortu
-- Sumber deteksi: information_schema.COLUMNS (jacos_db, MySQL 8.4.3) + grep kode.
-- Hanya tabel yang kolom email-nya BELUM menjadi kolom pertama index manapun.
-- setting_bahasa tidak disertakan: kolom email_ms_office sudah kolom pertama
-- unique index `uq_email_role`.
--
-- Semua ALTER di bawah: MySQL 8.4 dengan InnoDB row format DYNAMIC,
-- ADD INDEX otomatis INPLACE + LOCK=NONE. Prefix eksplisit dipasang agar
-- gagal cepat jika dijalankan di lingkungan yang tidak mendukung.
--
-- Estimasi panjang key:
--   varchar(255) utf8mb4_0900_ai_ci = 255*4 = 1020 byte  < 3072 byte limit  -> aman
--   varchar(50)  utf8mb4_0900_ai_ci =  50*4 =  200 byte  < 3072 byte limit  -> aman
-- Semua index NON-UNIQUE (email boleh kosong / berulang).

-- ============ siswa (email_ms_office + email_ms_office_ortu) ============

ALTER TABLE `siswa_sd`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `siswa_smp`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `siswa_sma`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `siswa_ft`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `siswa_kb`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `siswa_tk`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `switch_account`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ADD INDEX `idx_email_ms_office_ortu` (`email_ms_office_ortu`),
  ALGORITHM=INPLACE, LOCK=NONE;

-- ============ guru ============

ALTER TABLE `guru_sd`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `guru_smp`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `guru_sma`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `guru_ft`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

-- ============ pimpinan ============

ALTER TABLE `pimpinan`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `pimpinan_sd`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `pimpinan_smp`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `pimpinan_sma`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

-- ============ lain-lain ============

ALTER TABLE `pegawai`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `pramubhakti`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;

ALTER TABLE `security`
  ADD INDEX `idx_email_ms_office` (`email_ms_office`),
  ALGORITHM=INPLACE, LOCK=NONE;
