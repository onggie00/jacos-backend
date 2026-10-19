-- =====================================================================
-- 6B.3 — Skrip pembersihan terarah: baris audit LAMA aksi 'simpan_nilai_tahap'
-- yang detail_json-nya masih memuat angka skor ('score'/'skor') dan/atau
-- rubrik_note. Versi baru detail_json = {stage_order, nilai:'diubah',
-- rubric_len}. TIDAK dijalankan otomatis — admin DB menjalankan manual
-- (lingkungan dev/lokal saja; WAJIB review dulu + backup tabel).
-- Kolom nyata career_audit_logs (001_career_schema.sql:297-311):
--   id_audit_log, actor_type, actor_id, action, object_type, object_id,
--   detail_json (JSON), ip_address, created_at, updated_at
--   → TIDAK ADA kolom 'note' (perbaikan atas 6B.2 H/M2).
-- Skrip: transaksi; nilai lama DIHAPUS (JSON_REMOVE), stage_order & aksi
-- lain dipertahankan JSON_SET nilai saja.
-- =====================================================================

-- 1) Tinjau dulu (read-only — jumlah & contoh baris yang akan disentuh):
SELECT id_audit_log, created_at, actor_id, object_type, object_id, detail_json
FROM career_audit_logs
WHERE action = 'simpan_nilai_tahap'
  AND detail_json IS NOT NULL
  AND JSON_VALID(detail_json)
  AND (JSON_EXTRACT(detail_json, '$.score') IS NOT NULL
       OR JSON_EXTRACT(detail_json, '$.skor') IS NOT NULL
       OR JSON_EXTRACT(detail_json, '$.rubric_note') IS NOT NULL);

-- 2) Bersihkan DI DALAM transaksi (COMMIT hanya bila jumlah baris cukup kecil —
--    review hasil SELECT di atas dulu; ubah/COMMIT manual bila ragu):
START TRANSACTION;

UPDATE career_audit_logs
SET detail_json = JSON_SET(
        JSON_REMOVE(detail_json, '$.score', '$.skor', '$.rubric_note'),
        '$.nilai', 'diubah(rubrik-lama-dibersihkan-6B.3)')
WHERE action = 'simpan_nilai_tahap'
  AND detail_json IS NOT NULL
  AND JSON_VALID(detail_json)
  AND (JSON_EXTRACT(detail_json, '$.score') IS NOT NULL
       OR JSON_EXTRACT(detail_json, '$.skor') IS NOT NULL
       OR JSON_EXTRACT(detail_json, '$.rubric_note') IS NOT NULL);

-- periksa hasil di sesi yang sama SEBELUM COMMIT:
--   SELECT id_audit_log, detail_json FROM career_audit_logs
--    WHERE action = 'simpan_nilai_tahap' AND updated_at >= NOW() - INTERVAL 1 MINUTE;
COMMIT;

-- 3) Verifikasi (harus 0):
SELECT COUNT(*) AS sisa_bar_ber_skor
FROM career_audit_logs
WHERE action = 'simpan_nilai_tahap'
  AND detail_json IS NOT NULL
  AND JSON_VALID(detail_json)
  AND (JSON_EXTRACT(detail_json, '$.score') IS NOT NULL
       OR JSON_EXTRACT(detail_json, '$.skor') IS NOT NULL
       OR JSON_EXTRACT(detail_json, '$.rubric_note') IS NOT NULL);
