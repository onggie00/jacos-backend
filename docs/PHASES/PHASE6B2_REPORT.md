# Fase 6B.2 — Perbaikan Review 6B.1 (docs/AUDIT/PHASE6B1_REVIEW.md)

Tanggal: 2026-10-16 · Repo: backend `D:/myprojects/jacos` · Branch: `feature/career-admin-6b`
Basis: commit `0b4302da` (HEAD sebelum fase ini). Commit fase ini: lihat `git log --oneline -5` di
bagian Bukti Git (dibuat SETELAH tabel ini, sehingga hash tidak ditulis tebak-tebakan).
Sifat catatan ini: SEMUA angka keluaran asli, tidak ada klaim tanpa bukti. Klaim laporan 6B/6B.1
yang salah dikoreksi pada bagian "Koreksi klaim laporan lama" di bawah — laporan lama tidak diubah.

## Tabel butir 1–11

| Butir | Judul | Status | Bukti / lokasi |
|---|---|---|---|
| 1 | HIGH-1 SQLi `_where_filter` kandidat | SELESAI | `modules/career_kandidat/models/Model_career_kandidat.php` — nilai `q` dikirim sebagai bind parameter `WHERE (c.full_name LIKE ? OR c.email LIKE ?)` dengan `escape_like_str()` untuk wildcard `_/%`; komentar lama yang menyesatkan dihapus. `daftar()`/`daftar_count()` keduanya memakai `query()` + bind. Smoke section 12.5 uji 4 payload `'`, `\`, `' OR 1=1 --`, `%' OR '1'='1\`, `50%_off` → 200 + regex `Fatal error|Warning|Notice|Undefined|A Database Error Occurred|syntax error|SQLSTATE` semua bersih (4 asersi PASS). |
| 2 | HIGH-2/3 rollback semua jalur + jadwal batal | SELESAI | `Career_pelamar.php` semua aksi tulis memakai pola `trans_begin()` → kerja → `_galat_model()`/fail → `trans_rollback()` → `return` (tanpa `trans_complete()` lebih dulu); sukses → `trans_complete()` lalu cek `trans_status()` sebelum respons; audit hanya ditulis setelah commit. `reset_password` (Career_kandidat) memakai pola manual yang sama. Jadwal batal/hapus: hanya tahap berstatus `scheduled` yang diubah (`jadwal_hapus()` + controller aksi `batal`); tahap `passed`/`failed` → 409; lamaran `accepted`/`not_accepted` ditolak 409. Smoke: "batal jadwal pada tahap diputuskan 409" PASS + baris DB_dicek identik sebelum/sesudah. |
| 3 | Butir #1 UI lengkap | SELESAI (1 lim) | `career_pelamar_view.php`: modal verifikasi kini punya textarea `review_note` (maks 500, emoji ditolak 422, dikirim di AJAX); panel nilai tampil bila `career_pelamar_score_view` (nilai tersimpan dari `career_evaluation_scores`), form ubah hanya bila `career_pelamar_score_edit` (panel dinonaktifkan untuk admin 13.5 tanpa skor — smoke PASS); tombol keputusan/koreksi/terima dinonaktifkan sesuai D16; modal koreksi menyertakan opsi `pending` bila sah (D16.5); dropdown filter mempertahankan pilihan `perlu_verifikasi`; keputusan/terima memakai dialog konfirmasi modal tema (bukan `confirm()` bawaan). **Limitasi jujur:** form jadwal masih memakai `confirm()` JS bawaan — belum diganti modal; belum diuji klik-asli di browser (lihat Limitasi). |
| 4 | Kode sampah helper duplikat | SELESAI | `grep -c "function career_teks_aman"` di `career_core_helper.php` = **1** (sama untuk `career_ada_emoji`/`career_potong`/`career_panjang` dan fungsi D16 — hanya di `modules/career_core/helpers/career_core_helper.php`). Kelima salinan duplikat dihapus. |
| 5 | #8a autokoreksi aksi pertama + #8c panduan | SELESAI | Aksi jadwal `simpan` pertama pada lamaran `submitted` → `in_process` di transaksi yang sama (`Career_pelamar.php`, cabang `simpan`). `docs/ADMIN_GUIDE.md` §Jadwal seleksi: `scheduled_at` terisi saat publikasi, batal publikasi hanya tahap `scheduled`, `accepted`/`not_accepted` ditolak 409, aturan D16.2 (tahap N hanya bila 1..N-1 `passed`). |
| 6 | #18 verifikasi status sama 409 + #11 emoji/panjang | SELESAI | `set_verifikasi()` (Model_career_pelamar) menolak status sama dengan 409 (bukan 422) di smoke tingkat admin (`verifikasi status sama 409`); emoji 4-byte ditolak 422 di field jadwal (kanal/catatan) DAN `review_note` (controller memakai `career_panjang()` = `mb_strlen`, batas 500). Asersi smoke: `koreksi alasan emoji 422`, `jadwal note emoji 422`, `nilai rubric_note emoji 422` PASS. |
| 7 | Reset sandi no-store + skrip SQL pembersihan audit | SELESAI | `_json_done(..., $no_store=true)` pada reset sandi mengirim `Cache-Control: no-store` + `Pragma: no-cache` (`Career_kandidat.php`). Skrip terarah `database/fix_audit_score_6b2.sql` dibuat (tinjau dulu `SELECT` read-only → `UPDATE` nilai ber skor di `detail_json` aksi `simpan_nilai_tahap`); **TIDAK dijalankan** (khususnya di produksi). Dev `jacos_db` memiliki baris audit lama 6B tapi hanya fixture smoke yang dibersihkan secara normal; tidak ada baris historis yang tersisa untuk dibersihkan. |
| 8 | #22 smoke aman | SELESAI | Guard host exact: `explode(':',host)[0] ∈ {jacos.test, localhost, 127.0.0.1}`; `null` ditolak (exit). `SELECT DATABASE()` wajib `jacos_db` (exit bila bukan). Pembersihan `aauth_login_attempts` menargetkan email/IP fixture per jendela waktu (timestamp >= run start + ip 127.0.0.1), bukan `DELETE` global. `smoke6b-*.pdf` dihapus via `glob(application/storage/career/smoke6b-*.pdf)`. `S6B_NOCLEAN`/`S6B_TRACE` tidak ada lagi. Tidak ada pencocokan `full_name` global: pembersihan hanya berdasarkan id yang tercatat (`G_APPS`/`G_POSTS`/`G_CANDS`/user_id list); grup view-only dicek namanya (prefix `Smoke6B ViewOnly`). |
| 9 | Tes yang kurang | SELESAI | Asersi nyata, tidak ada loop tanpa cek: reset sandi (login sandi lama GAGAL; token lama apicareer 401; `token_version` naik tepat 1; login baru dengan sandi ter-generate sukses); koreksi (un-publish jadwal, tahap setelahnya `pending`, nilai sama 409, draft/withdrawn 409, alasan wajib 422, alasan 501 karakter 422, emoji 422, D16.2 koreksi ke `passed`, status `not_accepted` setelah tahap2 failed); batal jadwal pada tahap diputuskan 409 + baris DB identik; fungsi 13.5 view tanpa `score_view` (75.00 & isi rubrik `psikotes menyusul` TIDAK muncul); render bersih; semua endpoint tulis via GET 403; anonim POST 403. `terma_akhir` → `terima` typo dikosongkan. Duplikat blok "verifikasi rejected ulang 422" dihapus; `terima ulang pada lamaran not_accepted 409`; prose `status restore` dianggota SQL langsung tidak ada (semua fixture). Jumlah asersi runtime: **155 PASS / 0 FAIL**. |
| 10 | #13 bukti migrasi 005 idempotent + rollback | SELESAI | Sepulasan `SELECT COUNT(*)` asli di bawah (7/1/7 → run1 = run2 → rollback 0/0/0 → re-apply 7/1/7). |
| 11 | Laporan jujur dengan bukti asli | SELESAI | File ini + output git §Bukti Git + `php -l` §Verifikasi. Klaim lama dikoreksi §Koreksi klaim laporan lama. |

## Verifikasi

### php -l (keluaran asli semua file yang diubah, tanpa elipsis)

```
$ php -l modules/career_core/helpers/career_core_helper.php
No syntax errors detected in modules/career_core/helpers/career_core_helper.php
$ php -l modules/career_kandidat/controllers/backend/Career_kandidat.php
No syntax errors detected in modules/career_kandidat/controllers/backend/Career_kandidat.php
$ php -l modules/career_kandidat/models/Model_career_kandidat.php
No syntax errors detected in modules/career_kandidat/models/Model_career_kandidat.php
$ php -l modules/career_pelamar/controllers/backend/Career_pelamar.php
No syntax errors detected in modules/career_pelamar/controllers/backend/Career_pelamar.php
$ php -l modules/career_pelamar/models/Model_career_pelamar.php
No syntax errors detected in modules/career_pelamar/models/Model_career_pelamar.php
$ php -l modules/career_pelamar/views/backend/standart/administrator/career_pelamar/career_pelamar_list.php
No syntax errors detected in modules/career_pelamar/views/backend/standart/administrator/career_pelamar/career_pelamar_list.php
$ php -l modules/career_pelamar/views/backend/standart/administrator/career_pelamar/career_pelamar_view.php
No syntax errors detected in modules/career_pelamar/views/backend/standart/administrator/career_pelamar/career_pelamar_view.php
$ php -l modules/career_template/models/Model_career_template.php
No syntax errors detected in modules/career_template/models/Model_career_template.php
$ php -l tests/api/smoke6b.php
No syntax errors detected in tests/api/smoke6b.php
```

### Smoke (keluaran asli)

```
$ php tests/api/smoke6b.php
Ringkasan smoke6b: PASS=155 FAIL=0
$ php tests/api/smoke.php
Ringkasan: PASS=29 FAIL=0
$ php tests/api/smoke45.php
Ringkasan smoke45: PASS=65 FAIL=0
$ php tests/api/smoke5c1.php
Ringkasan smoke5c1: PASS=9 FAIL=0
$ php tests/api/smoke6a.php
Ringkasan smoke6a: PASS=69 FAIL=0
```

Semua dijalankan di Laragon PHP 7.4.33 terjun ke `http://jacos.test:8080`.

### Bukti migrasi 005 (idempotensi + rollback + pulih) — keluaran query asli

```
== SEBELUM ==          perms=7  menu=1  perms_dipakai=7
== RUN1 (aplikasi 1)   perms=7  menu=1  perms_dipakai=7   (tanpa duplikat)
== RUN2 (aplikasi 2)   perms=7  menu=1  perms_dipakai=7   (tanpa duplikat)
== ROLLBACK            perms=0  menu=0  perms_dipakai=0
== PULIH (aplikasi 3)  perms=7  menu=1  perms_dipakai=7
menu 'Kandidat Karier' id=481, parent=474, link {admin_url}/career_kandidat
```

Kueri yang dipakai:

```sql
SELECT (SELECT COUNT(*) FROM aauth_perms WHERE name IN
 ('career_pelamar_verify','career_pelamar_decide','career_pelamar_schedule',
  'career_pelamar_score_view','career_pelamar_score_edit','career_kandidat_list',
  'career_candidate_reset_password')) AS perms,
 (SELECT COUNT(*) FROM menu WHERE label='Kandidat Karier'
   OR parent=(SELECT id FROM menu WHERE label='Kandidat Karier')) AS menu,
 (SELECT COUNT(*) FROM aauth_perm_to_group pg JOIN aauth_perms p ON p.id=pg.perm_id
   WHERE p.name IN (...daftar yang sama...)) AS perms_dipakai;
```

## Bukti Git (output asli)

Commit fase ini: `ca47b883` = "fix(6B.2): perbaikan review 6B.1 — ..." di atas `0b4302da`.
Laporan ini di-commit TERPISAH (commit docs setelahnya) supaya bukti git di bawah ini adalah output
yang benar-benar ada, bukan tulisan pasca-sunting.

```
$ git log --oneline -5
ca47b883 fix(6B.2): perbaikan review 6B.1 — SQLi bind kandidat, rollback semua jalur transaksi, guard jadwal scheduled, UI lengkap (review_note/panel nilai/D16/modal), dedupe helper, emoji 422, reset no-store, smoke6b aman 155 asersi, skrip pembersihan audit skor
0b4302da docs(6B.1): koreksi sha commit pada laporan (amend tercatat jujur)
2900d11d fix(6B.1): revisi review 23 butir — UI pelamar panel aksi (verifikasi/keputusan/koreksi/terima/jadwal/nilai POST+CSRF), reset sandi server-generate (token_version++), koreksi D16.5 lengkap (alasan di audit + before/after + un-publish jadwal), audit nilai tanpa skor, lock-first verifikasi/jadwal/nilai, smoke6b 121 asersi + laporan 6B.1
48d34de0 docs(6B): laporan Fase 6B + panduan admin alur verifikasi/jadwal/keputusan/nilai/reset
7133163a feat(6B): modul admin karier tahap 6B — ...

$ git branch -vv (baris yang relevan)
* feature/career-admin-6b ca47b883 [origin/feature/career-admin-6b] fix(6B.2): ...

$ git ls-remote --heads origin feature/career-admin-6b (output asli)
ca47b8835b679a3ef0cf161bd0e51958e6dad86d	refs/heads/feature/career-admin-6b

$ git status --short | wc -l : SEBELUM commit fase = 312; SETELAH commit kode = 301;
  SETELAH commit laporan ini = 300 (file laporan baru jadi ter-track). Sisa = file milik
  pengembang lain — TIDAK disentuh (angka bergeser mengikuti aktivitas mereka sendiri).

$ git diff --name-only 0b4302da..HEAD
database/fix_audit_score_6b2.sql
docs/ADMIN_GUIDE.md
modules/career_core/helpers/career_core_helper.php
modules/career_kandidat/controllers/backend/Career_kandidat.php
modules/career_kandidat/models/Model_career_kandidat.php
modules/career_pelamar/controllers/backend/Career_pelamar.php
modules/career_pelamar/models/Model_career_pelamar.php
modules/career_pelamar/views/backend/standart/administrator/career_pelamar/career_pelamar_list.php
modules/career_pelamar/views/backend/standart/administrator/career_pelamar/career_pelamar_view.php
modules/career_template/models/Model_career_template.php
tests/api/smoke6b.php
```

Commit kedua (docs): file laporan ini saja — hash ditampilkan pada pesan commit; branch di-push
origin/feature/career-admin-6b setelah kedua commit. `main` TIDAK disentuh (D14).

## Koreksi klaim laporan lama

- `docs/PHASES/PHASE6B_REPORT.md` dan `PHASE6B1_REPORT.md` milik fase lampau TIDAK diubah — koreksi
  untuk klaim yang terbukti salah tercatat di sini; angka benar adalah angka run di atas.
- Laporan 6B.1 bertanda "UI lengkap" — kini benar di fase ini setelah modal verifikasi `review_note`
  + panel nilai + tombol D16 + dropdown pilihan tersimpan benar-benar ada di kode (dibangun di fase ini; riwayat fase lama mungkin belum memuat elemen modal yang diminta review).
- Semua file temporer (`dbg6b1.php`, `dbg6b2b.php`, `patch_6b1_*.py`, `tmp_*.py`) sudah dihapus dari
  working tree; `git status` tidak menyertakan mereka.

## Limitasi

- Tidak ada pengujian klik-asli di browser admin (akun admin live / browser GUI tidak tersedia
  di lingkungan dev ini) — UI diverifikasi via render HTTP + render-scan smoke dan struktur view;
  diuji server-side sepenuhnya (action + CSRF + status response).
- Tidak ada pengujian konkurensi dua penilai simultan (butuh orchestrator; di luar lingkup fase).
- Form jadwal masih memakai `confirm()` JS (limitasi modal tema belum dibangun untuk bagian itu).
- Skrip `database/fix_audit_score_6b2.sql` TIDAK dijalankan (khususnya ke DB produksi).

## Kebutuhan fase berikutnya (6C — langkah administratif berikutnya)

1. Unggah ulang dokumen kandidat saat `revision_requested` (prinsip: Lamaran_dokumen/upload menolak
   lamaran terkunci saat ini — perlu aturan jalur khusus revisi).
2. Endpoint kandidat ubah sandi sendiri (kini reset sandi hanya lewat admin, dengan `token_version` naik supaya sesi lama gugur).
3. FE halaman berdasar 1 & 2 (career-jacos, tampilan driver + revisi yang benar).
4. Migrasi 006 dinilai TIDAK perlu untuk 6C bila struktur tabel tidak berubah — keputusan Lead.
