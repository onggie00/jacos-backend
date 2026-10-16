-- =====================================================================
-- 6B.2 — Skrip pembersihan terarah: hasil audit LAMA (sebelum perbaikan
-- #4 6B.1) yang memuat angka skor di detail_json ('simpan_nilai_tahap').
-- Karakter data: career_audit_logs.detail_json padaaksi simpan_nilai_tahap
-- versi lama memuat 'score'/'skor' numerik; versi baru sudah
-- {stage_order,nilai:'diubah',rubric_len}. TIDAK dijalankan ke produksi —
-- jalankan manual oleh admin DB setelah meninjau baris yang dicakup.
-- Scope: baris yang HANYA cocok pola skor numeric pada aksi
-- simpan_nilai_tahap; tidak menyentuh audit saja.
-- =====================================================================

-- 1) Tinjau dulu (read-only):
SELECT ca.id_audit_log, ca.created_at, ca.actor_id, ca.detail_json
FROM career_audit_logs ca
WHERE ca.action = 'simpan_nilai_tahap'
  AND ca.detail_json REGEXP '"(score|skor)"[[:space:]]*:';

-- 2) Histori yang memuat skor diganti dengan detail minimal tanpa angka:
UPDATE career_audit_logs ca
JOIN (
    SELECT id_audit_log
    FROM career_audit_logs
    WHERE action = 'simpan_nilai_tahap'
      AND detail_json REGEXP '"(score|skor)"[[:space:]]*:'
) tgt ON tgt.id_audit_log = ca.id_audit_log
SET ca.detail_json = '{"stage_order":9,"nilai":"diubah","rubric_len":-1}',
    ca.note = 'detail_json dari-versi-lama-skor-dihapus (6B.2)';
-- catatan: rubric_len=-1 menandakan nilai lama tak terukur dari baris ini;
-- sesuaikan bila ingin membuang nilai itu.

-- 3) Atau, bila sepenuhnya ingin batal: tulis 'detail_json'->'removed'.
-- UPDATE career_audit_logs ... SET detail_json = '{"removed":true}' ...;

-- Verifikasi (harus 0):
SELECT COUNT(*) FROM career_audit_logs
WHERE action = 'simpan_nilai_tahap' AND detail_json REGEXP '"(score|skor)"[[:space:]]*:';
