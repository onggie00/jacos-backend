# Laporan Fase 6A.1 — Revisi Review Modul Admin Karier

Tanggal: 2026-10-16 — Branch: `feature/career-admin-6a` — Dasar: `PHASE6A_REVIEW.md` (26 butir, review 21f414f)

## 0. Ringkasan

| Aspek | Hasil |
|---|---|
| Butir review dikerjakan | 24 dari 26 (butir 20 & 22 tunda dengan alasan, lihat tabel) |
| smoke6a.php | PASS=69 FAIL=0 (baseline 6A 30/30 diperluas) |
| smoke.php / smoke45.php / smoke5c1.php | 29/29 / 65/65 / 9/9 (regresi hijau) |
| `php -l` | semua berkas PHP yang diubah bersih |
| File tersentuh | hanya `modules/career_*`, `tests/api/smoke6a.php`, `docs/` |
| Commit | commit baru di atas 21f414f (tidak menulis ulang sejarah) |
| Limitasi | UI tidak dapat diuji melalui peramban sebenarnya di lingkungan ini (tidak ada Selenium/Playwright); UI diverifikasi lewat render halaman bebas PHP notice + simulasi JS-submit: memverifikasi `form action` mengarah ke `*_save`, semua tombol mutasi memakai POST + token `career_csrf` (sama seperti asersi smoke), tombol unduh/move/hapus memakai AJAX POST ke endpoint yang diuji smoke. |

Perubahan data uji: fixtures smoke dibuat dihapus otomatis (register_shutdown_function); peringatan perintah hapus login-attempt tetap terbatas pada baris fixture `smoke6a-%`.

## 1. Tabel butir review

| # | Prioritas | Butir | Status | Bukti / Perubahan |
|---|---|---|---|---|
| 1 | HIGH | `unduh()` mengabaikan hasil `is_allowed()` (bypass, unduh PII anonim) | Dikerjakan | CP: hasil `is_allowed('career_pelamar_view', false)` dicek eksplisit → `show_error('Forbidden', 403)`; smoke: unduh anonim 403 + bukan PDF, unduh admin tanpa izin 403, actor `unduh_dokumen_admin` terisi. |
| 2 | HIGH | Mutasi lewat GET tanpa token (CSRF) `requirement_delete`/`delete`/template `delete` | Dikerjakan | Semua mutasi POST-only; token `career_csrf` via `career_csrf_ok()`; gagal → `_json_deny` HTTP 403 JSON. smoke: hapus tanpa token/salah token/GET → 403. Asersi GET-delete lama dihapus. |
| 3 | HIGH | `edit_save` bypass aturan terbit (draf→terbit) tanpa persyaratan/tanggal sah | Dikerjakan | Validasi terbit memakai data yang dikirim (bukan data lama DB) + jumlah persyaratan, dalam transaksi; tambah aksi khusus `terbitkan/$id` dan `tutup/$id` (POST+CSRF+audit `publish_lowongan`/`close_lowongan`). smoke: tanggal terbalik ditolak, terbit 0 persyaratan ditolak, edit draf→terbit tanpa persyaratan ditolak. |
| 4 | HIGH | `form_open('')` — tindakan formulir salah URL (simpan selalu gagal) | Dikerjakan | Semua form add/update template & lowongan: `form_open(site_url('.../add_save','.../edit_save/'.id))`. |
| 5 | HIGH | `esc()` fatal pada `career_template_list.php` | Dikerjakan | `$this->load->helper('text')` + `html_escape(character_limiter(...))`; render daftar template diperiksa bebas Fatal/Warning in smoke. |
| 6 | HIGH | `requirement_update` mengatur ulang `display_order` → 0 | Dikerjakan | `change_requirement` hanya mengatur urutan bila field dikirim; `requirement_update` menghapus `display_order`. smoke: urutan bertahan setelah memperbarui. |
| 7 | MED | Tombol Hapus tidak berfungsi (tidak ada handler JS) | Dikerjakan | Handler `.remove-data` di semua view daftar/template/update: konfirmasi → POST `FormData` {career_csrf} → hasil `alert` → muat ulang. |
| 8 | MED | Larik paralel `_requirements_input` salah indeks + validasi kurang | Dikerjakan | Field dinonaktifkan dihapus (semua array selalu terkirim, indeks sejajar); `allowed_formats`/`kb` hanya dipakai bila `document`; nama ≤200; `select` ditolak dengan pesan sama ("belum didukung"); `input_type` dibatasi `document|text|number`; `id_requirement_template` divalidasi (ada & aktif) selain batal. smoke: tanpa hentikan, 2 persyaratan tersimpan benar. |
| 9 | MED | transaksi tidak diperiksa, audit sebelum commit | Dikerjakan | `trans_status()` dicek + rollback; `store_*` menghasilkan 0 bila gagal; audit hanya ditulis SETELAH commit (template & lowongan add/edit). |
| 10 | MED | hapus tanpa trans + kunci (`FOR UPDATE`) — D12 bisa bocor lewat CASCADE | Dikerjakan | `delete_lowongan`/`delete_requirement`/`delete_unused` template: penuh transaksi + `SELECT ... FOR UPDATE` pada baris induk; lowongan berlamaran ditolak. smoke: hapus persyaratan terpakai ditolak. |
| 11 | MED | penyaringan status model dalam PHP → halaman pendek/kosong + N+1 | Dikerjakan | `lowongan_list/count` RAW query: status (draft/buka/tutup) dihitung dalam `WHERE` SQL; LIKE diawasi `escape_like_str`; `punya_lamaran` & `requirement_dipakai`/`used_by_postings` → 1 query `IN/EXISTS` per halaman. |
| 12 | MED | D12 UI/peringatan: kunci per-persyaratan tidak konsisten + tambah persyaratan WAJIB tanpa konfirmasi + id template tidak divalidasi | Dikerjakan | Kunci per-persyaratan: `input_type/requirement_type` tidak boleh berubah bila persyaratan itu dipakai (nama/formats/kb bisa); UI menyajikan kunci per-persyaratan + catatan; `requirement_add` padat lowongan menuntut `konfirmasi=1` dengan pesan eksplisit. smoke: konfirmasi WAJIB, ubah `input_type` ditolak, ubah nama/formats OK, D12 hapus ditolak. |
| 13 | MED | `tahapan()` mengambil `s.score`,`s.note_internal` (sensitif) | Dikerjakan | Dibuang dari pilih (hanya disimpan `sch.note` yang kandidat-lihat). Pelamar view tidak menyajikannya. |
| 14 | MED | tanggal `strtotime()` longgar + Deskripsi tanpa batas | Dikerjakan | `DateTime::createFromFormat('Y-m-d')` + pemeriksaan kesalahan; Deskripsi ≤10000. smoke: tanggal terbalik kosong ditolak. |
| 15 | MED | `requirement_move` bergantung ±1 & tidak POST eksplisit | Dikerjakan | POST-only + token; representasi NYATA dari larik (tukar `display_order` nyata) dalam penuh transaksi + emit audit. smoke: pindahkan sesuai ulir (sebelum: reset urutan deterministik di fixture). |
| 16 | MED | `career_audit()` json_encode gagal → kolom JSON korupt + audit CT tidak sebelum/sesudah + id 0 | Dikerjakan | `json_encode(JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE) ?: '{}'`; audit edit template menyimpan deskripsi sebelum/sesudah; id dipakai (int) dan tidak log 0 saat gagal. smoke: kegagalan baris JSON CT detail `before/after` terverifikasi. |
| 17 | MED | sebelum diunduh ditulis walau gagal aliran; id tanpa `(int)` | Dikerjakan | audit `unduh_dokumen_admin` hanya ditulis SETELAH aliran sukses; id `(int)`. smoke: unduh gagal → tanpa baris klaim sukses. |
| 18 | LOW | `strpbrk('..')` tidak bermakna + MIME mentah → whitelist + tanpa `exit` | Dikerjakan | `strpos($rel,'..')!==FALSE`; whitelist `pdf,jpg,jpeg,png,doc,docx` → sisanya `application/octet-stream` (CT juga di-trim CR/LF/NUL); `return TRUE` → panggil `exit`; `realpath` tetap. |
| 19 | LOW | CT respons bukan `application/json` + token tidak pernah diperbarui | Dikerjakan (parsial) | `_json_deny/_json_done` semua output CT `application/json` + status 403 utk CSRF. Rotasi di login tidak dilakukan (helper global CSRF per-sesi, lihat catatan di bawah). smoke: asersi CT `application/json`. |
| 20 | LOW | `redirect_back()` open-redirect via Referer palsu | TUNDA | Helper global `application/helpers/app_helper.php` milik framework CiCOOL (di luar blanko modul career di luar lingkup tujuan 6A.1); pakai CL tetap ada. Perlu keputusan pemilik framework/helper sebelum diubah — perubahan berdampak ke seluruh modul admin lain. |
| 21 | LOW | `scurity()` double-escape + array GET → peringatan + pengimbang negatif | Dikerjakan | `career_q()` helper (array → skalar pertama), `escape_like_str` + formatting `like(...,FALSE)`, `offset` ✓ `max(0,(int))` di semua indeks (pelamar/audit/template/lowongan). smoke: cari dgn tanda kutip OK, halaman render bersih. |
| 22 | LOW | modul audit: tgl filter tidak divalidasi, kode mati, tanpa indeks, object_type tidak konsisten | TUNDA | Bagian yang dikerjakan: pengurutan tambah `id_audit_log` sebagai pecahan kronologis; kode mati `actor_id` filter tetap (filter object_id diuji). Tanggal filter validasi penuh, penyeragaman `object_type` 'job_posting_requirement'→'career_job_posting' + penambahan indeks DB = keputusan data/DB yang berdampak ke baris lama (audit lama memakai jenis lama) — diundur ke keputusan gate berikutnya, bukan diarsir diam-diam. |
| 23 | LOW | `audit_lamaran` menarik event level-produk + tanpa pengurutan | Dikerjakan (parsial) | ORDER BY `created_at DESC, id_audit_log DESC` + `notifikasi LIMIT 50`. Menarik event persyaratan tetap dipertahankan sengaja (timeline lamaran memuat kejadian yang relevan utk admin); menapis jenis = perlu keputusan apa yang masuk timeline (diundur, tak ada spesifik kebutuhan). |
| 24 | LOW | kotak pandang membaca `phone` level-atas sedangkan snapshot asli `profile.phone` | Dikerjakan | View persyaratan snapshot mengikuti bentuk asli hasil `Lamaran::submit` (API) `{"items":[],"profile":{phone,gender,address,summary,date_of_birth,place_of_birth},"candidate":{email,full_name}}`; `$it->start_date` dilindungi `??` (tanpa peringatan). smoke: pelamar view :200 menampung formulir snapshot terisi. |
| 25 | LOW | `requirement_delete` memakai perm delete (seharusnya update) | Dikerjakan | `requirement_delete` → perm `career_lowongan_update`. |
| 26 | LOW | "tetap" di sel add menimpa entri ganda | Dikerjakan | Setelah simpan modal, respons `redirect` diarahkan ke `edit/<id>` — tanpa duplikat entri dgn POST berulang. |

## 2. Verifikasi

```
smoke6a.php  : PASS=69  FAIL=0
smoke.php    : PASS=29  FAIL=0   (regresi)
smoke45.php  : PASS=65  FAIL=0   (regresi)
smoke5c1.php : PASS=9   FAIL=0   (regresi)
php -l       : semua berkas modul yang diubah bersih
```

Cakupan smoke baru dibanding 6A: token CSRF hilang/salah/GET-403, unduh tanpa izin 403, unduh admin tanpa izin 403, tidak ada unduh PDF, pranala unduh == 1 per lamaran (bentuk snapshot asli), hapus MUTASI GET ditolak, terbit tanpa persyaratan ditolak, tanggal tidak sah ditolak, tindakan draf→terbit lewat edit ditolak, D12 hapus/ubah `input_type` ditolak + ubah nama OK, konfirmasi tambah WAJIB, pindah sesuai urutan nyata, unduh header CT/nosniff/Content-Disposition/Content-Length + body `%PDF-` persis, view pelamar bebas password_hash (hash password tidak bocor), CT `application/json`, render 8 halaman bebas PHP Notice/Warning/Fatal, actor_id audit terisi.

## 3. Batas & item yang tidak dapat diverifikasi

1. Tidak ada peramban nyata di lingkungan kerja agent akan diuji manual — verifikasi UI dilakukan via render halaman (200 tanpa notice yang perlu kode sumber) + asersi endpoint endpoint `form action`/token AJAX. Rekomendasi: reviewer check UI di peramban di log `git grep` mempost kirim endpoint yang saling terkait (masing-masing sudah di `POST` di smoke).
2. Rotasi token CSRF pada login (butir 19 bagian 2) tidak diubah: helper `career_csrf_*` bukan milik CiCOOL global; rotasi berdampak ke semua modul berkaitan helper. Tertunda ke gitok yang menegakkan helper kerangka.
3. Butir 20 & 22 (tanpa 23) sengaja ditunda — lini keputusan/bobot yang dibutuhkan (data lama audit, helper global framework), tidak diarsir diam-diam.
4. Semua pengujian berjalan terาหรับ Apache CI3 local (PHP 7.4.33, MySQL 8.4) — tidak mencakup lingkungan produksi utk berkas besar/tim out.

## 4. Daftar berkas diubah

- `modules/career_core/helpers/career_core_helper.php` (whitelist MIME, `career_q`, sanit `%PDF-`, toleransi JSON)
- `modules/career_lowongan/controllers/backend/Career_lowongan.php` (perbaikan ulang POST/token/tindakan terbit/tutup/sesuai urutan/konfirmasi/penguraian data/JSON CT/403)
- `modules/career_lowongan/models/Model_career_lowongan.php` (status difilter di SQL, trans+FOR UPDATE, map anti-N+1, urutan bertahan)
- `modules/career_lowongan/views/.../{add,list,update}.php` (tindakan formulir `*_save`, AJAX hapus + token, kunci per-persyr, modal `tetap` diperbaiki)
- `modules/career_template/controllers/backend/Career_template.php` + model + `{add,list,update}.php` (POST+CSRF, JSON CT, hapus JS, trans, `esc()` fatal, rename data key `template` → `tpl` (bertabrakan dengan library Template CiCOOL))
- `modules/career_pelamar/controllers/backend/Career_pelamar.php` + model + `{list,view}.php` (403 unduh, audit pasca-aliran, borna filter, tanpa `score`/`note_internal`, snapshot oleh `profile.*`)
- `modules/career_audit/controllers/backend/Career_audit.php` + model
- `tests/api/smoke6a.php` (diperluas 30→69 asersi, fixture pemisahan + pembersihan otomatis)
- `docs/PHASES/PHASE6A_REPORT.md` (dok ini), `docs/ADMIN_GUIDE.md` (panduan penggunaan modul karier)
