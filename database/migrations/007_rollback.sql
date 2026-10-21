-- ============================================================================
-- GURU.1 — 007_rollback.sql (idempoten)
-- Menghapus perm & tabel career_guru_link.
-- AMAN: hanya jalan bila tabel kosong/berisi baris link — cek dulu sebelum
-- eksekusi di server:
--   SELECT COUNT(*) FROM career_guru_link;
-- Baris link yang masih dipakai tidak boleh dihapus manual di sini; gunakan
-- database/rollback_guru_konversi.sql (BERPARAMETER) yang memeriksa guru belum
-- dipakai presensi (presensi_office/pegawai_slip) sebelum menghapus.
-- ============================================================================

DELETE pm
FROM aauth_perm_to_group pm
JOIN aauth_perms p ON p.id = pm.perm_id
WHERE p.name = 'career_pelamar_to_guru';

DELETE FROM aauth_perms WHERE name = 'career_pelamar_to_guru';

DROP TABLE IF EXISTS `career_guru_link`;
