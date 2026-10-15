# Laporan Fase 4 — API apicareer

Status: siap review Lead. Branch `feature/career-api`, commit `92f1a34`.
Semua file PHP lulus `php -l`. Perubahan file milik orang lain tidak
di-stage sama sekali; working tree mereka dibiarkan apa adanya.

## Bagian A — Migrasi ke jacos_db lokal

Backup sesuai prompt (dijalankan dulu, sebelum migrasi apa pun):

```
mysqldump --no-defaults -uroot --single-transaction --routines --triggers \
  jacos_db > D:/tmp/jacos_db_backup_20261015_210201.sql
```

Ukuran file: 302.725.571 bytes. Catatan teknis: argumen pertama wajib
`--no-defaults` karena `my.ini` Laragon berisi baris `=quick` yang membuat
mysqldump gagal dengan pesan `unknown variable 'quick'`.

`001_career_schema.sql` dan `002_career_seed_stages.sql` dijalankan ke
jacos_db lokal (MySQL 8.4.3). Terverifikasi: 17 tabel `career_*` + 5 baris
tahap berurutan (Seleksi Berkas online, Psikotes offline, Tes Psikolog
offline, Microteaching offline, Interview Pimpinan offline).

Server produksi jacos_admin tidak disentuh sama sekali.

## Bagian B/C — File baru

| File | Isi |
|---|---|
| `application/libraries/Career_base.php` | Base abstract semua controller apicareer. Tugasnya: CORS allowlist (hapus wildcard lalu set origin yang diizinkan dari config; origin asal diset dari header `Origin`, default deny bila tidak cocok), verifikasi Bearer token JWT HS256 (cek `sub` dan `ver` numerik, kandidat ada dan aktif, `token_version` cocok), pengaturan $methods per controller sehingga endpoint kandidat tidak dimintai X-Api-Key, wrapper respons seragam `_ok`/`_err`, helper rate limit login/register per-IP/email (baca-kunci career_login_attempts), penghitung fail + locked_until, audit helper (career_audit_logs), helper notification (career_notifications), `_own_application` (404 anti IDOR, 409 bila bukan draft), `_posting_is_open`. |
| `application/controllers/apicareer/Auth.php` | register (validasi email 190/max, password ≥8, full_name ≤150, password_hash bcrypt, transaksi kandidat+profil, rate limit per-IP, JWT berlaku 8 jam), login (bila salah password: counter fail naik, lockout sesuai config; login_kandidat sukses: counter fail di-reset, last_login_at di-set), logout (token_version+1 → token mati), me (profil tanpa created/updated_at). |
| `application/controllers/apicareer/Profil.php` | GET profil + PUT profil dengan whitelist field (phone, place_of_birth, address, summary, gender male/female, date_of_birth format YYYY-MM-DD divalidasi via date_parse). |
| `application/controllers/apicareer/Profil_item.php` | CRUD item profil generik (pendidikan, pekerjaan, pelatihan, prestasi, referensi, portofolio) tabel career_candidate_items; anti IDOR: cek kepemilikan sebelum update/delete → 404. |
| `application/controllers/apicareer/Lowongan.php` | GET list lowongan (is_published=1 dan hari ini di antara opening/closing) + GET detail (riwayat requirements: nama, required/optional, input_type, allowed_formats, max_file_size_kb, display_order). 404 bila lowongan tutup/tidak ada. |
| `application/controllers/apicareer/Lamaran.php` | POST draft (posting harus terbuka; satu lamaran per lowongan; transaksi: insert lamaran + auto-generate baris career_application_requirements dari requirements lowongan), GET list lowongan, GET detail (requirements + dokumen + status tahap — hanya kolom aman: stage_order/name/mode/status/scheduled_at/decided_at, ditambah jadwal ter-publish; kolom internal (score, note_internal, review_note) tidak pernah dikembalikan), POST submit (syarat: status masih draft, posting masih terbuka, semua requirement berinput file yang required sudah punya ≥1 dokumen; transaksi: snapshot profil ke career_application_profile_snapshots, 5 baris career_application_stages status pending sesuai urutan 1–5, update career_applications → submitted + submitted_at + current_stage_id ke tahap 1, lalu notify + audit). |
| `application/controllers/apicareer/Lamaran_dokumen.php` | POST upload (id_application_requirement wajib milik kandidat sendiri, lamaran wajib masih draft; cek ukuran → max_file_size_kb requirement atau fallback konfigurasi 5120 KB; cek ekstensi terhadap allowed_formats; cek MIME asli file via finfo — bila tidak cocok 422; checksum sha256 untuk file yang sama mengembalikan 200 idempotent, tidak buat versi baru; version = max+1; nama file acak app{id}_req{id}_v{v}_{hex}.ext di application/storage/career/; kembalikan 201 id dokumen). GET detail (unduh file milik kandidat sendiri, header Content-Disposition attachment, 404 anti IDOR, audit unduh_dokumen). |
| `application/controllers/apicareer/Notifikasi.php` | GET list dan PUT read (all endpoint cek kepemilikan 404 anti IDOR). |
| `application/config/career.php` | Allowlist CORS: `careers.jacos.id`, `localhost:3000`; exp token 8 jam; max login attempts 5; login lockout 900 detik; register rate limit 5/jam; path `application/storage/career/`; max upload default 5120 KB. |
| `application/config/credentials.example.php` | placeholder `CAREER_JWT_KEY` beserta instruksi membuatnya; tidak ada nilai secret di file contoh. |
| `application/storage/career/.htaccess` | Deny all, sehingga dokumen hanya bisa diakses lewat endpoint GET detail (cek kandidat kepemilikan), tidak diakses URL web langsung. |
| `docs/API_CONTRACT.md` | Kontrak API lengkap: daftar endpoint, method, body, kode status, aturan otorisasi (JWT Bearer). |
| `tests/api/smoke.sh` | Wrapper bash: `bash tests/api/smoke.sh [BASE_URL]`. |
| `tests/api/smoke.php` | 29 tindakan smoke end-to-end memakai curl extension PHP (lihat Bagian E). |

## Bagian D — CORS (dicek langsung via curl)

- GET `apicareer/Auth/me` dengan header Origin `http://localhost:3000`
  → respons memuat `Access-Control-Allow-Origin: http://localhost:3000`
  dan `Vary: Origin` (bukan `*`).
- Permintaan dengan origin `http://evil.example` → header
  `Access-Control-Allow-Origin` tidak dikirim sama sekali (allowlist).
- `Access-Control-Allow-Methods` hanya metode yang diizinkan
  (GET/POST/PUT/DELETE/OPTIONS), bukan `*`.
- Catatan: OPTIONS preflight masih dijawab REST_Controller sendiri
  (`Access-Control-Allow-Origin: *`) sebelum controller apicareer
  sempat berjalan; karena `_check_cors()` di wrapper milik REST lib
  versi 3.0 dipanggil di constructor dan langsung exit untuk OPTIONS.
  Fase 4 melarang menyentuh `rest.php`/`REST_Controller.php`, sehingga
  deviasi ini dibiarkan direcord — rekomendasi fase hardening: atasi di
  .htaccess/router, jangan lewat rest.php.

## Bagian E — Smoke test

Semua file `apicareer/` lulus `php -l`. Smoke end-to-end di Jalur
`http://jacos.test:8080/apicareer` hasil: 29/29 PASS (detail per skenario di
`tests/api/smoke.php`; berkas data di application/storage/career/
dibersihkan otomatis di akhir run register_shutdown_function).

```
PASS  login tanpa body -> 400 (HTTP 400)
PASS  register -> 201 (HTTP 201)
PASS  register dup -> 409 (HTTP 409)
PASS  register password pendek -> 422 (HTTP 422)
PASS  login salah -> 401 (HTTP 401)
PASS  me -> 200 (HTTP 200)
PASS  me tanpa token -> 401 (HTTP 401)
PASS  profil gender invalid -> 422 (HTTP 422)
PASS  item type invalid -> 422 (HTTP 422)
PASS  item education -> 201 (HTTP 201)
PASS  item milik orang lain -> 404 (HTTP 404)
PASS  lowongan detail -> 200 (HTTP 200)
PASS  lowongan detail tidak ada -> 404 (HTTP 404)
PASS  lamaran draft -> 201 (HTTP 201)
PASS  lamaran dup -> 409 (HTTP 409)
PASS  upload ext/MIME salah -> 422 (HTTP 422)
PASS  upload pdf -> 201 (HTTP 201)
PASS  upload requirement bukan milik -> 404 (HTTP 404)
PASS  submit -> 200 (HTTP 200)
PASS  submit lagi -> 409 (HTTP 409)
PASS  submit bukan milik -> 404 (HTTP 404)
PASS  detail stage terisi -> 200 (HTTP 200)
PASS  5 stage pending
PASS  unduh dokumen -> 200 (HTTP 200)
PASS  unduh dokumen bukan milik -> 404 (HTTP 404)
PASS  notifikasi >=1 setelah submit
PASS  notifikasi read -> 200 (HTTP 200)
PASS  logout -> 200 (HTTP 200)
PASS  me dengan token lama -> 401 (HTTP 401)
PASS=29 FAIL=0
```

Logout menghasilkan token_version + 1 → me dengan token lama langsung 401
(skenario token invalidasi real).

## Bagian F — Catatan keputusan desain

1. **Keyless API di apicareer (`$methods` map)** — REST lib repo ini
   mewajibkan X-Api-Key bila `rest_enable_keys=TRUE` di `rest.php`.
   rest.php adalah config yang DIPAKAI BERSAMA modul apiweb/apiapp dan
   Phase 4 melarang mengubahnya. Solusi: property `$methods` di Career_base dipetakan ke
   endpoint career dengan `key = FALSE` (REST lib mengizinkan per-endpoint
   method override key, dieksekusi lewat `_remap`); auth kandidat memakai JWT
   Bearer. Kalau Lead lebih ingin pakai X-Api-Key, perlu diskusi penuh
   (satu key yang dipakai bersama oleh semua FE publik tidak disarankan;
   kebocoran key memengaruhi seluruh apicareer).
2. **APPATH typo** di Career_base baris 40 ditemukan saat smoke pertama
   (baris `file_exists(APPATH…)`) — sudah ditulis ulang menjadi
   APPPATH. (php -l memang tidak menangkap konstanta undefined; hanya runtime.)
3. **Anti-duplikat checksum** di Lamaran_dokumen.php implementasi sendiri
   (bukan UNIQUE skema). Perlu konfirmasi Lead: jadikan UNIQUE di database
   atau tetap validasi manual di API (pilihan saat ini). Risiko collisi
   rendah (entropy tinggi).
4. **Anti-duplicate jadwal tahap career_selection_schedules** tidak
   di-enforce di API (admin di Fase 6) — jika admin membuat dua jadwal ter-publish untuk satu tahap, API
   mengambil yang terbaru (ORDER BY id DESC). Aturan final di Fase 6.
5. **Storage file lokal** (bukan S3) — keputusan soal storage non-lokal
   ditangguhkan ke Fase 7 bila nanti deploy pakai penyimpanan
   terpisah dari app server. File fisik tetap di belakang .htaccess
   `Require all denied`; akses hanya lewat endpoint unduh dengan cek
   kepemilikan.

## Bagian G — SQL data dummy lowongan (untuk smoke, lokal saja)

```sql
INSERT INTO career_job_postings (title, description, opening_date, closing_date, is_published, created_by)
VALUES ('[DUMMY TEST] Guru Kelas SD (uji API)', 'Data uji API — bukan lowongan resmi.', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1, 1);
INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
VALUES
((SELECT id_job_posting FROM career_job_postings WHERE title='[DUMMY TEST] Guru Kelas SD (uji API)'), 'Ijazah SD/Sederajat', 'required', 'document', 'PDF', 2048, 1);
```

Data dummy ini masih ada di jacos_db lokal (dipakai ulang oleh smoke).
TIDAK dipindah ke server produksi. Pembersihan menunggu keputusan Lead
(lihat pertanyaan 3).

## Pertanyaan / keputusan yang masih terbuka

1. Non-file requirement (penyimpanan jawaban text/number/select skema
   belum ada): diperlukan keputusan skema (kolom tambahan? tabel baru?)
   sebelum fase admin dijalankan. Smoke Fase ini tidak mencakup submit
   lowongan dengan requirement non-file.
2. OPTIONS preflight masih mengembalikan `Access-Control-Allow-Origin: *`
   (bagian D) — diberi tahu ke Lead sebagai masuk daftar perlu tindak
   lanjut di fase hardening.
3. Data uji DUMMY + file fisik di storage — dibersihkan atau dibiarkan
   untuk smoke ulang?
4. Kolom `actor_id` di `career_audit_logs` (tanpa FK) — tetap sesuai
   keputusan desain Fase 3; tidak ada perubahan struktur.
5. URL API untuk FE (Fase 5) — saat ini semua request via
   `http://jacos.test:8080/apicareer`; `.env.example` Fase 1.5 sudah
   memakai `NEXT_PUBLIC_API_BASE_URL` yang sama. Perlu konfirmasi URL
   produksi saat go-live.

## Keterbatasan

- Belum ada uji paralel/concurrency (2 kandidat submit bersamaan,
  upload versi bersamaan, dst).
- Belum ada uji HTTPS/CDN/prod (hanya jacos.test:8080 http).
- Upload hanya dianggap PDF/JPG/JPEG/PNG (whitelist dokumentasi file
  profile: ijazah/transkrip/portofolio/karya ilmiah dsb). Format
  tambahan (mis. DOCX) masih terbuka, tergantung kebutuhan admin
  (judul pekerjaan). Dirender default secure — hati-hati bila mengubah
  daftar ekstensi yang diizinkan.
- Pentest (bypass JWT, replay token, privilege escalation) ada di luar
  scope Fase 4 — direkomendasikan khusus di Fase 7.
