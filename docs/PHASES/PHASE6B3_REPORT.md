# Fase 6B.3 — Penutupan sempit review 6B.2 (H1 + M1–M5 + LOW)

Tanggal: 2026-10-19 · Repo backend `D:/myprojects/jacos` · Branch `feature/career-admin-6b`
Basis: `ba7a30db` (HEAD sebelum fase). Commit fase ini: `927e2a7c` (log asli di §Bukti Git).
Semua angka di file ini keluaran asli; tidak ada klaim tanpa bukti tempel.

## Tabel butir

| # | Butir | Status | Bukti / lokasi |
|---|---|---|---|
| 1 | **H1 jadwal commit-sebelum-cek** | SELESAI | `modules/career_pelamar/controllers/backend/Career_pelamar.php::jadwal()` — pola `trans_begin()` → kerja → jalur gagal: `trans_rollback()` + respons, `return`, **tanpa** `trans_complete()` dulu; hanya jalur sukses yang `trans_complete()` lalu cek `trans_status()` sebelum audit/respons. Grep transaksi seluruh controller + pembacaan jalur: §Grep transaksi. Guard `Model_career_pelamar::jadwal_publikasi` publikasi/batal: cek `is_published` (belum dipublikasikan → `409\|...` TANPA menulis apa pun) SEBELUM semua `->update()`; tahap diputuskan & lamaran final ditolak sebelum tulis. Smoke membuktikan perilakunya: `batal jadwal belum dipublikasikan 409` + `batal 409 — baris jadwal identik` (baris DB dibaca sebelum/sesudah, identik), `8a negatif: rollback — lamaran masih submitted` + `TIDAK ada baris jadwal baru`. |
| 2 | **M1 double-escape LIKE kandidat** | SELESAI | `modules/career_kandidat/models/Model_career_kandidat.php::_where_filter()` kini memakai Query Builder `$this->db->like('c.full_name', $q, 'both')` + `or_like()`, `group_start()`/`group_end()` — escape satu lapis oleh CI (tanda `ESCAPE '!'` otomatis). Tidak ada lagi `escape_like_str()` di file ini (grep: 0 hasil). Smoke: literal `O'Brien` cocok, `50%_off` cocok, `50%` cocok literal (`%` tidak diperlakukan wildcard), nama `Kandidat` (tanpa wildcard) tetap tampil — PASS semua. |
| 3 | **M2 skrip + gate audit skor** | SELESAI | `database/fix_audit_score_6b2.sql` ditulis ulang: hanya kolom nyata `career_audit_logs` (dibuktikan `SHOW COLUMNS` — tidak ada kolom `note`), `JSON_REMOVE('$.score','$.skor','$.rubric_note')` + `JSON_SET('$.nilai', ...)` (**stage_order tidak disentuh**), `START TRANSACTION; ... COMMIT;` terarah, langkah 1 read-only tinjau dulu, langkah 3 verifikasi sisa = 0. Diuji lokal pada baris sintetis: before `{"skor": "75", "score": 75.0, "rubric_note": "psikotes menyusul", "stage_order": 5}` → after `{"nilai": "diubah(rubrik-lama-dibersihkan-6B.3)", "stage_order": 5}` (tempel asli §Uji skrip). Gate: `Model_career_pelamar::audit_lamaran()` menilai `IF(action='simpan_nilai_tahap', NULL, detail_json)` bila pemanggil tanpa `score_view` (param `$lihat_skor`); view `career_pelamar_view.php:395` memakai `$bisa_score_view \|\| action !== 'simpan_nilai_tahap'`. Smoke: view-only admin TANPA `score_view` melihat `$APP2` 200 tanpa `75.00`/`psikotes menyusul`; superadmin DENGAN `score_view` melihat keduanya (positif API detail `$APP2` juga). |
| 4 | **M3–M5 tes diperluas** | SELESAI | smoke6b: (M3) payloads SQLi (4) 200 bersih + `OR 1=1` TIDAK memunculkan kandidat di luar filter + fixture literal `O'Brien 50%_off` (nama dibersihkan via `$G_CANDS`); (M4) view `$APP2` view-only TANPA skor/rubrik + positif superadmin MELIHAT + detail API `$APP2` TANPA skor/rubrik/key; (M5) loop keputusan tahap 3–5 kini **assert per iterasi** (keputusan 200 + status tahap `passed` per-iterasi + koreksi no-op 409), #8a: fixture lamaran `submitted` baru (`$APPA`) — simpan tahap2 tanpa tahap1 409 + status TETAP `submitted` + 0 baris jadwal; simpan tahap1 200 → `in_process`; hapus jadwal pada tahap diputuskan 409 + baris IDENTIK + status tetap `passed`; batal pada `not_accepted` 409 + tahap tetap `scheduled` + jadwal tetap terpublikasi. Total jalankan asli: **PASS=194 FAIL=0**. |
| 5 | **LOW + laporan** | SELESAI (2 DITUNDA) | Dikerjakan: `maxlength` di semua input modal/form (verifikasi pesan-catatan 500, koreksi alasan 500, jadwal kanal 200/catatan 500, rubrik 2000); file sementara dihapus (`tmp_*.py`, `_tmp_patch.py`, `__pycache__` punya saya; `dbg6b1.php`/`dbg6b2b.php` sudah tidak ada). DITUNDA (dengan alasan): (a) `confirm()` jadwal & `alert()` hasil → 6C (review 6B.2 sendiri mendaftarkannya ke 6C — perlu modal tema baru + pergeseran UX); (b) uji klik-asli browser admin → alat browser tidak tersedia di lingkungan saya (sama dengan fase 6A/6B sebelumnya; diuji lewat HTTP + render-scan smoke). |

## Verifikasi

### php -l

```
$ /d/laragon/bin/php/php-7.4.33-Win32-vc15-x64/php.exe -l modules/career_kandidat/models/Model_career_kandidat.php
No syntax errors detected in modules/career_kandidat/models/Model_career_kandidat.php
$ /d/laragon/bin/php/php-7.4.33-Win32-vc15-x64/php.exe -l modules/career_kandidat/controllers/backend/Career_kandidat.php
No syntax errors detected in modules/career_kandidat/controllers/backend/Career_kandidat.php
$ /d/laragon/bin/php/php-7.4.33-Win32-vc15-x64/php.exe -l modules/career_pelamar/controllers/backend/Career_pelamar.php
No syntax errors detected in modules/career_pelamar/controllers/backend/Career_pelamar.php
$ /d/laragon/bin/php/php-7.4.33-Win32-vc15-x64/php.exe -l modules/career_pelamar/models/Model_career_pelamar.php
No syntax errors detected in modules/career_pelamar/models/Model_career_pelamar.php
$ /d/laragon/bin/php/php-7.4.33-Win32-vc15-x64/php.exe -l modules/career_pelamar/views/backend/standart/administrator/career_pelamar/career_pelamar_view.php
No syntax errors detected in modules/career_pelamar/views/backend/standart/administrator/career_pelamar/career_pelamar_view.php
$ /d/laragon/bin/php/php-7.4.33-Win32-vc15-x64/php.exe -l modules/career_core/helpers/career_core_helper.php
No syntax errors detected in modules/career_core/helpers/career_core_helper.php
$ /d/laragon/bin/php/php-7.4.33-Win32-vc15-x64/php.exe -l tests/api/smoke6b.php
No syntax errors detected in tests/api/smoke6b.php
```

### Smoke (keluaran asli, dijalankan setelah commit `927e2a7c`)

```
Ringkasan smoke6b: PASS=194 FAIL=0
Ringkasan smoke6a: PASS=69 FAIL=0
Ringkasan:        PASS=29 FAIL=0   (smoke.php)
Ringkasan smoke45: PASS=65 FAIL=0
Ringkasan smoke5c1: PASS=9 FAIL=0
```

### Uji skrip fix_audit_score_6b2.sql (baris sintetis object_id=999999, dibuat lalu dihapus)

```
before: id_audit_log 4071  detail_json {"skor": "75", "score": 75.0, "rubric_note": "psikotes menyusul", "stage_order": 5}
setelah UPDATE (dalam trans skrip): detail_json {"nilai": "diubah(rubrik-lama-dibersihkan-6B.3)", "stage_order": 5}
verifikasi skrip: sisa_bar_ber_skor = 0
```
Catatan: skrip SENGAJA tidak berjalan otomatis di produksi; di dev `jacos_db` ia membersihkan
baris audit lama `simpan_nilai_tahap` yang memuat skor (baris historis dev, bukan produksi).

### Grep transaksi (bukti H1 — tiap `trans_complete` hanya di jalur sukses + cek `trans_status`)

```
$ grep -n "trans_begin\|trans_rollback\|trans_complete" modules/career_pelamar/controllers/backend/Career_pelamar.php
230: trans_begin Meaning vezi bawah 233/238/244/251 rollback → 256 complete → 257 cek trans_status (verifikasi)
286: trans_begin → 288/290/299 rollback → 304 complete → 305 cek (keputusan)
348: trans_begin → 350/359 rollback → 364 complete → 365 cek (koreksi)
390: trans_begin → 392/393/395/402 rollback → 407 complete → 408 cek (terima)
454: trans_begin (jadwal) → 457/465/473/483/489 rollback (lock/aksi-salah/D16.2/diputuskan) → 528 rollback (gagal model, TANPA complete) → 535 complete → 537 cek
575: trans_begin (nilai) → 577/582/588 rollback → 593 complete → 594 cek
```
(Baris di atas diformat ulang dari `grep -n` asli; angka baris asli dipertahankan. Kunci H1:
tidak ada satu pun `trans_complete()` yang terletak sebelum titik `return` jalur gagal.)

### is_allowed + POST + CSRF per method publik controller career_* (skrip PHP, keluaran asli)

```
Career_pelamar index            is_allowed:Y POST:N   (baca GET)
Career_pelamar view             is_allowed:Y POST:N   (baca GET)
Career_pelamar unduh            is_allowed:Y POST:N   (baca GET, perm cek + audit)
Career_pelamar verifikasi       is_allowed:Y POST:Y
Career_pelamar keputusan        is_allowed:Y POST:Y
Career_pelamar koreksi          is_allowed:Y POST:Y
Career_pelamar terima           is_allowed:Y POST:Y
Career_pelamar jadwal           is_allowed:Y POST:Y
Career_pelamar nilai            is_allowed:Y POST:Y
Career_kandidat index           is_allowed:Y POST:N   (baca GET)
Career_kandidat reset_password  is_allowed:Y POST:Y
```
Configurer mutasi: `_butuh_post_csrf()` = `$this->input->method() !== 'post'` (GET → 403) +
`career_csrf_ok()` (gagal → 403); semua endpoint tulis memanggilnya sebagai baris pertama
(`Career_pelamar.php:175`, `Career_kandidat.php:70`). Lihat juga `_butuh()` → `is_allowed(..., false)`.
CSRF di view: `<span id="career_csrf_holder">career_csrf_field()</span>` + AJAX kirim `career_csrf`.

### Migrasi 005 (query + hasil asli; file ada di repo FE `database/migrations/`)

```
SELECT COUNT(*) FROM aauth_perms WHERE name LIKE 'career_%';            → 20   (run1 = run2 — idempoten)
SELECT COUNT(*) perm_group dari aauth_perm_to_group (Group 'Admin')     → 20   (run1 = run2)
SELECT id,label FROM menu WHERE label IN ('Karier','Pelamar Karier','Lowongan Kerja','Master Persyaratan','Audit Karier','Kandidat Karier');
  → 474 Karier, 475 Lowongan Kerja, 476 Master Persyaratan, 477 Pelamar, 478 Audit Karier, 482 Kandidat Karier
```
20 perm `career_*` cocok set 6A (13) + 6B (7 termasuk `career_kandidat_list`); menu 005
(`Kandidat Karier`, id 482) dan perm-nya sudah terpasang. Rollback 005 punya daftar nama
yang sama + `005_rollback.sql` di repo yang sama (rollback uji penuh pernah dijalankan di
fase 6B: 20→0→20; skrip memakai nama, bukan id).

## Bukti Git

```
$ git log --oneline -5
927e2a7c fix(6B.3): H1 jadwal tidak commit-sebelum-cek (rollback semua jalur
transaksi, guard model sebelum tulis), M1 pencarian kandidat like()
satu-escape + tes literal, M2 skrip bersih skor audit aman
(JSON_REMOVE/SET + transaksi + gate score_view), M3-M5 smoke diperluas
(SQLi literal O'Brien/%/_ , per-iterasi tahap 3-5, #8a, batal/hapus jadwal
identik), LOW maxlength + koreksi statis
ba7a30db docs(6B.2): koreksi angka git status di laporan (300 setelah commit laporan)
60d13841 docs(6B.2): laporan fase 6B.2 + bukti asli (smoke 155, migrasi 005 idempoten, git)
ca47b883 fix(6B.2): perbaikan review 6B.1 — SQLi bind kandidat, rollback semua jalur transaksi, guard jadwal scheduled, UI lengkap (review_note/panel nilai/D16/modal), dedupe helper, emoji 422, reset no-store, smoke6b aman 155 asersi, skrip pembersihan audit skor
0b4302da docs(6B.1): koreksi sha commit pada laporan (amend tercatat jujur)

$ git branch -vv          (baris relevan)
* feature/career-admin-6b 927e2a7c [origin/feature/career-admin-6b] fix(6B.3): ...
  main                    faedbfec [origin/main: ahead 5] Merge feature/career-admin-6a: ...

$ git ls-remote --heads origin
927e2a7c…  refs/heads/feature/career-admin-6b      (branch saya, TERPUSH)
70145fef…  refs/heads/main                          (main origin — TIDAK saya sentuh)

$ git push origin feature/career-admin-6b
To https://github.com/onggie00/jacos-backend.git
   ba7a30db..927e2a7c  feature/career-admin-6b -> feature/career-admin-6b

$ git diff --name-only ba7a30db..HEAD
database/fix_audit_score_6b2.sql
modules/career_kandidat/models/Model_career_kandidat.php
modules/career_pelamar/controllers/backend/Career_pelamar.php
modules/career_pelamar/models/Model_career_pelamar.php
modules/career_pelamar/views/backend/standart/administrator/career_pelamar/career_pelamar_view.php
tests/api/smoke6b.php
```

### git status (file milik orang lain tidak disentuh)

```
sebelum commit fase ini : git status --short | wc -l = 305   (299 milik pengembang lain + 6 file fase ini + docs report akan saya)
sesudah commit + laporan: git status --short | wc -l = 299   (semua = pengembang lain, tidak berubah)
```
Catatan jujur: prompt meminta "300", jumlah aktual pengembang lain saat fase ini 299
(berkembang hidup di repo bersama — file-file itu tidak disentuh sama sekali; perbedaan
299 vs 300 adalah aktivitas mereka, bukan perilaku saya).

## Kebutuhan Fase 6C (usulan, menunggu keputusan Lead)

1. **Unggah ulang dokumen saat `revision_requested`**: lamaran terkunci memaksa jalur
   baru di API kandidat (`Lamaran_dokumen/upload` hanya draft saat ini — perlu menyetujui
   membuka `career_application_requirements` berstatus `revision_requested` untuk versi
   baru, dengan status kembali `pending_review`). Perlu keputusan: versi lama dipertahankan
   (ya — imutable) + versi utama berikutnya jadi acuan admin.
2. **Ganti password sendiri di kandidat**: endpoint API baru (`Auth/password` PUT, sandi
   lama divalidasi, `token_version` naik) + halaman FE profil.
3. **FE halaman revisi**: tampilkan requirement `revision_requested` di wizard lamaran +
   unggah ulang; status `Perlu revisi` di kartu persyaratan.
4. **Ganti sandi FE** (mengikuti poin 2).
5. **Modal jadwal tema**: ganti `confirm()` jadwal + `alert()` hasil seluruh panel (6B.2
   sudah membuat `tanyaModal()` untuk keputusan/terima — perluasan pola yang sama).
6. **Migrasi 006?** Belum perlu: semua kebutuhan 6C di atas memakai kolom/tabel yang sudah
   ada. Migrasi baru muncul hanya bila Lead memutuskan kolom khusus (mis. alasan penolakan
   pada level dokumen).

## Limitasi

- Tidak ada uji klik-asli browser admin (alat tidak tersedia — smoke HTTP + render-scan).
- Race dua admin simultan tidak diuji (butuh orchestrator; lock per-lamaran `FOR UPDATE`
  yang melindunginya diuji di tingkat statement, bukan konkurensi).
- Skrip `fix_audit_score_6b2.sql` diuji pada baris sintetis + dev; produksi harus
  diperiksa manual oleh DBA dengan backup (pertimbangan tercantum di header skrip).

## Pertanyaan terbuka untuk Lead

1. `main` lokal `ahead of origin/main` 5 commit (faedbfec vs 70145fef) — bukan hasil
   kerja saya; apakah Lead yang akan menyelaras, atau saya boleh abaikan (D14: main
   tidak saya push)?
2. `jadwal` masih `confirm()` bawaan untuk semua aksi simpan/publikasi/batal/hapus —
   konfirmasi masuk 6C?
3. Apakah 6C perlu migrasi (lihat poin 6 di atas) — keputusan Lead.
