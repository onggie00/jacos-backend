-- Migration: perbaiki flag is_reversed instrument CBI (id_instrument = 4)
-- Jalankan manual di MySQL/phpMyAdmin
--
-- Sumber: Berkas_sample/agent-prompt/CBI_per_aspek.docx
--   "NB: Pengecualian (Item Terbalik): Butir Nomor 12 bersifat favorable/reversed.
--    Nilainya harus dibalik: Selalu = 0, Sering = 25, Kadang-kadang = 50,
--    Jarang = 75, Tidak Pernah = 100."
--
-- Koreksi dari migration lama (item10): verifikasi data riil sesi 1
-- (cbi_skor_before.tsv: Work=42.86, Overall=32.34) hanya cocok kalau item 12
-- yang di-reverse. Item 10 (energi cukup utk keluarga) TIDAK reversed.
--
-- Setelah migration ini, sesi lama wajib di-recalculate:
--   /administrator/mhcu_sesi/recalculate_periode?id_periode=<id>

UPDATE mhcu_instrument_item SET is_reversed = 0
WHERE id_instrument = 4 AND no_urut_item = 10;

UPDATE mhcu_instrument_item SET is_reversed = 1
WHERE id_instrument = 4 AND no_urut_item = 12;
