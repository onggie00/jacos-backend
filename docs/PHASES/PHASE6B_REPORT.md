# Laporan Fase 6B — Verifikasi Dokumen, Keputusan Tahap, Jadwal Seleksi, Nilai Evaluasi, Reset Sandi Kandidat

Backend: `D:/myprojects/jacos`, branch `feature/career-admin-6a`, commit `7133163a`.
Skema baru: `database/migrations/005_career_admin_actions.sql` (repo frontend career-jacos, belum di-commit bersama prompt — file ada di working tree repo FE; termasuk 005_rollback.sql).

## Ruang lingkup

Sesuai `docs/PHASES/PHASE6B_PROMPT.md` (repo FE):
1. Verifikasi dokumen per persyaratan (D17) — endpoint `verifikasi/<id_application_requirement>` POST; keputusan `verified / revision_requested / rejected`; pesan kandidat wajib untuk revisi/tolak; review_note internal; reviewer & waktu tercatat (`reviewed_by`, `reviewed_at`).
2. Keputusan tahap (D16) — `keputusan` POST per tahap (1..5): `passed / failed`; `koreksi` untuk mengoreksi keputusan (alasan wajib); `terima` untuk status akhir `accepted` hanya setelah 5 tahap passed; tanpa UI skor. Aturan: tahap berikutnya hanya boleh diproses bila tahap sebelumnya `passed` (D16.2), lamaran hanya bisa diproses bila status `submitted/in_process` (D16.1), tahap yang sudah diputuskan terkunci kecuali via koreksi (D16.4), koreksi dapat membatalkan status akhir dan mengembalikan lamaran ke status konsisten (D16.5).
3. Jadwal seleksi per tahap — `jadwal` POST: `simpan / publikasi / batal / hapus`; simpan hanya sebelum publikasi; publikasi mengubah status tahap ke `scheduled` + kirim notifikasi kandidat `stage_scheduled` (D18); batal publikasi → kembali pending; hapus hanya sebelum publikasi.
4. Nilai & catatan internal (D13) — panel `nilai`; skor 0..100 desimal di `career_evaluation_scores` dengan `rubric_note` (B14 satu sumber kebenaran; tidak duplikasi ke `career_application_stages.score/note_internal`).
5. Reset sandi kandidat — modul `career_kandidat`: daftar kandidat + pencarian; tindakan reset password (POST+CSRF); perm `career_candidate_reset_password`.
6. Notifikasi in-app kandidat (D18) melalui helper `career_notify` (type: `requirement_revision`, `requirement_rejected`, `stage_passed`, `stage_failed`, `stage_scheduled`, `application_accepted`).

## Keputusan implementasi

- **Sumber kebenaran nilai**: `career_evaluation_scores` (satu baris per `id_application_stage`). `career_application_stages.score/note_internal` dibiarkan kosong — menghindari duplikasi data (prompt butir 4).
- **Keputusan tahap**: validasi berantai dilakukan di controller + model (helper `career_stage_keputusan_valid`, `career_stage_keputusan_valid_keputusan`); lamaran dikunci `SELECT ... FOR UPDATE` sebelum mutasi; autokoreksi status lamaran lewat helper `career_lamaran_status_autokoreksi`(`submitted->in_process` setelah tahap pertama diputuskan, `not_accepted` bila ada tahap failed, `in_process` bila tahap dikoreksi).
- **Jadwal**: tabel `career_selection_schedules` 1-1 per tahap; publikasi dijalankan lewat `jadwal_publikasi` (is_published + status tahap; notif kandidat) — tidak duplikasi jadwal ke kolom hari/tahun baru (menunggu keputusan Lead bila butuh).
- **Verifikasi dokumen**: sesuai D17 — verifikasi per persyaratan; status `verified / revision_requested / rejected`; notifikasi ke kandidat dengan pesan wajib; audit `verifikasi_persyaratan`.
- **Reset password**: kandidat cari di modul career_kandidat; password baru dicatat hanya sebagai hash; kegagalan transfer (id tidak ditemukan) → 404; tidak ada pengiriman email peripheral.
- **Audit**: hanya setelah transaksi commit; tidak menulis isi pesan kandidat/rubrik penuh ke audit (hanya panjang/berita hash) — reduksi PII.

## Bukti uji (all green di PHP 7.4.33 Laragon; BASE http://jacos.test:8080)

```
tests/api/smoke6b.php : PASS=95 FAIL=0   (fitur 6B)
tests/api/smoke6a.php : PASS=69 FAIL=0   (regresi 6A.1)
tests/api/smoke.php   : PASS=29 FAIL=0   (regresi smoke standalone)
tests/api/smoke45.php : PASS=65 FAIL=0   (regresi smoke feature-45)
tests/api/smoke5c1.php: PASS=9  FAIL=0   (regresi unduh dokumen + list Lamaran)
```

Detail cakupan smoke6b (95 asersi): hal & izin; versi dokumen; CSRF tolak; keputusan tahap sahih/tidak sahih (D16.1–D16.5); verifikasi dokumen terverifikasi/revisi/tolak (+ pesan wajib); jadwal simpan/publikasi/batal/hapus + tahap `scheduled`/`pending`; nilai (0..100 & `rubric_note`) via `career_evaluation_scores`; notifikasi `stage_scheduled`; audit `verifikasi_persyaratan/keputusan_tahap/koreksi_tahap/simpan_nilai_tahap`; halaman admin dirender tanpa fatal/notice; snapshot profil; tidak ada password_hash bocor ke halaman daftar kandidat; semua respons JSON `Content-Type: application/json`.

Manual FE (input normal): lamaran draft masih 'draft' → dilarang (verifikasi hanya `submitted/in_process`) — dilaporkan 409; data uji smoke dibersihkan dari DB (delete cascade terverifikasi 0 baris tersisa setelah cleanup).

## Risiko & catatan keputusan kecil

- High: **D13 skor internal** tak pernah keluar dari panel admin (apicareer Lamaran.php sudah memfilter kolom `score/note_internal/review_note/rubric_note` pada respons kandidat — sudah ada dari 4.5).
- Medium: keputusan dari UI menerima hanya tipe input aktif; `select` masih ditolak eksplisit (menunggu migrasi tipe skema).
- Low: cari kandidat memakai 'email=like — pencarian email draf belum casefold penuh.

## Kebutuhan API/FE untuk Fase 6C (pengembangan wizard frontend) — tidak dibuat fase ini

1. `revision_requested` upload baru: butuh jalur `Lamaran_dokumen/upload` yang MENERIMA versi baru walau lamaran terkunci (bila persyaratan merevisi; status kembali `pending_review`). FE perlu tombol "Unggah versi revisi" di kartu upload (status `revision_requested`), menampilkan pesan admin (notif) alasan revisi.
2. Kandidat dashboard/riwayat perlu menampilkan status per persyaratan (`not_uploaded/pending_review/verified/revision_requested/rejected`) + isi pesan notifikasi dari notifikasi list (tidak dari body yang sama).
3. `reset password kandidat` dari panel admin (career_kandidat) — UI sudah disediakan di panel admin; tidak butuh API kandidat baru.

## Pertanyaan terbuka (untuk Lead)

1. Konfirmasi publikasi backend: D14 menahan push ke `main` backend sampai Fase 7 — branch `feature/career-admin-6a` commit `7133163a` menunggu merge dari Lead.
2. Perlu kolom/trigger baru untuk jalur revisi dokumen otomatis (Fase 6C item 1)? Bila ya → perlu migrasi 006.
3. Keep `app_status_dari_req` helper — tidak lagi dipakai controller (kini `kunci_lamaran` langsung) — mau di-HAPUS atau dipakai di 6C?

## File yang berubah/gotak commit `7133163a`

- `modules/career_core/helpers/career_core_helper.php` (+108): career_notify, career_stages_rekap, career_stage_keputusan_valid, career_lamaran_status_autokoreksi.
- `modules/career_kandidat/*` (3 file baru): controller/admin reset password/kandidat.
- `modules/career_pelamar/controllers/backend/Career_pelamar.php` (+398 area verifikasi/keputusan/koreksi/terima/jadwal/nilai).
- `modules/career_pelamar/models/Model_career_pelamar.php` (+388 area model mutasi verifikasi/keputusan/jadwal/nilai).
- `tests/api/smoke6b.php` (568 baris — 95 asersi).

File milik kolega ~299 tetap tidak tersentuh (git status terverifikasi sisa 299 file unmodified-miliki-orang-lain).

## Batasan

- Font UI admin FE belum: panel nilai terpisah & alur jadwal di FE (butir alur Fase 6C, menunggu prompt 6C dari Lead).
- Kalau: keputusan kandidat (status akhir 'accepted/rejected') hanya via panel admin; tidak ada fitur email/submisi otomatis (D2).
- `select` requirement tetap tidak didukung (pesan "belum didukung" — sama dengan 6A).

Berhenti di gerbang Lead. Menunggu tinjauan/merge/review Lead.
