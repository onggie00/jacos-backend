-- Migration: tambah kolom tanggal_publish di mhcu_periode
-- Jalankan manual di MySQL/phpMyAdmin

ALTER TABLE mhcu_periode ADD tanggal_publish DATE NULL AFTER tanggal_selesai;
