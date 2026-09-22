-- Migration: Tambah kolom spp_type ke tabel siswa aktif
-- Jalankan manual di database

-- Untuk SMP
ALTER TABLE siswa_smp_aktif 
ADD COLUMN spp_type ENUM('FULL','HALF','FREE') DEFAULT 'FULL' AFTER spp_custom;

-- Untuk SD
ALTER TABLE siswa_sd_aktif 
ADD COLUMN spp_type ENUM('FULL','HALF','FREE') DEFAULT 'FULL' AFTER spp_custom;

-- Untuk SMA
ALTER TABLE siswa_sma_aktif 
ADD COLUMN spp_type ENUM('FULL','HALF','FREE') DEFAULT 'FULL' AFTER spp_custom;

-- Untuk FT (France Track)
ALTER TABLE siswa_ft_aktif 
ADD COLUMN spp_type ENUM('FULL','HALF','FREE') DEFAULT 'FULL' AFTER spp_custom;
