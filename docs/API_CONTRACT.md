# API Contract — apicareer (JACOS Careers, Fase 4)

Base URL lokal: `http://jacos.test:8080/apicareer`
Otentikasi: **JWT Bearer** — header `Authorization: Bearer <token>`. Tidak memakai
API key (`keys` table) — lihat catatan di `application/libraries/Career_base.php`.
Token berlaku 8 jam (`career_jwt_expire = 28800`); logout menaikkan
`token_version` sehingga token lama langsung invalid.

Semua respons seragam: `{"status": bool, "message": string, "data": mixed}`.

| Status | Arti |
|---|---|
| 400 | Input hilang/tidak terbaca |
| 401 | Belum login / token invalid / email-password salah |
| 404 | Objek tidak ditemukan (termasuk objek milik kandidat lain — anti IDOR) |
| 409 | Konflik state (duplikat, sudah terkunci, buka-tutup) |
| 422 | Validasi isi gagal |
| 429 | Rate-limit / akun terkunci sementara |

## CORS
- Allowlist: `https://careers.jacos.id`, `http://localhost:3000` —
  permintaan dari origin lain TIDAK mendapat header `Access-Control-Allow-Origin`.
- **Catatan deviasi**: preflight `OPTIONS` masih dijawab REST_Controller
  (`Access-Control-Allow-Origin: *`) SEBELUM constructor apicareer berjalan,
  karena `_check_cors()` berjalan di constructor REST lib dan exit pada OPTIONS.
  Permintaan sesungguhnya (GET/POST/PUT/DELETE) sudah mengikuti allowlist
  (diperiksa via curl). Akses data tidak bocor — hanya header preflight.
  Rekomendasi fase berikutnya: override preflight di level router/htaccess,
  bukan dengan menyentuh rest.php.

## Endpoints

### POST /Auth/register — publik
Body JSON: `{email, password, full_name}` (password min 8 email ≤190 nama ≤150).
201 → `{id_candidate, full_name, token, token_type:"Bearer"}`.
409 email sudah terdaftar; 422 validasi; 429 rate-limit per-IP
(`career_register_max_attempts` per `career_register_rate_window`).
Tidak ada email verifikasi (keputusan proyek — lihat PROJECT_REQUIREMENTS.md).

### POST /Auth/login — publik
Body JSON: `{email, password}`. 200 → `{id_candidate, full_name, token}`.
401 kredensial salah; 429 bila `failed_login_count >= career_login_max_attempts`
atau `locked_until` belum lewat (lock mengabaikan password benar).

### POST /Auth/logout — auth
Menaikkan `token_version` → token tersebut (dan semua versi lama) invalid. 200.

### GET /Auth/me — auth
Kandidat + profil tanpa `created_at/updated_at`.

### GET /Profil/index — auth
### PUT /Profil/index — auth
Body opsional: `{phone(30), place_of_birth(120), date_of_birth YYYY-MM-DD,
gender male|female, address(500), summary(5000)}`. Field di luar daftar diabaikan.

### GET /Profil_item/index[?type=education|...] — auth
type ∈ education/experience/training/achievement/reference/portfolio.

### POST /Profil_item/index — auth
Body `{item_type, title(1..200), institution, start_date, end_date,
description(5000), extra_json, display_order}` → 201 `{id_candidate_item}`.

### PUT /Profil_item/detail/:id / DELETE /Profil_item/detail/:id — auth
404 bila item bukan milik kandidat.

### GET /Lowongan/index — publik
Hanya `is_published=1` DAN hari ini dalam rentang opening_date..closing_date.

### GET /Lowongan/detail/:id — publik
Detail + daftar `requirements` (requirement_name/requirement_type
required|optional/input_type/allowed_formats/max_file_size_kb/display_order).
404 bila bukan lowongan terbuka.

### POST /Lamaran/index — auth
Body `{id_job_posting}` → 201 draft. Requirements lowongan otomatis dibuat
(status `not_uploaded`). 409 bila sudah pernah melamar (1 lamaran per
lowongan) atau lowongan tidak terbuka.

### GET /Lamaran/index — auth
Daftar lamaran milik sendiri.

### GET /Lamaran/detail/:id — auth
Detail + `requirements` + `stages`. Kandidat HANYA melihat data aman per tahap
(stage_order/name/mode/status/scheduled_at/decided_at + jadwal terpublish
channel/note dari `career_selection_schedules`). Kolom internal
(score, note_internal, review_note) TIDAK PERNAH dikembalikan.

### POST /Lamaran/submit — auth
Body `{id_application}`. Syarat: status draft, lowongan masih terbuka, semua
requirement bertipe `required` dgn `input_type document` punya ≥1 dokumen →
422 bila belum. transaksi: snapshot profil disimpan di `career_application_profile_snapshots`,
5 baris stage dibuat di `career_application_stages` (semua status `pending`), status
`submitted` + `submitted_at` + `current_stage_id`=stage 1.
Lamaran TERKUNCI setelah submit; ubahan profil berikutnya tidak mengubah
snapshot. CATATAN (pertanyaan terbuka): requirement non-file (text/number/
select) belum punya tempat penyimpanan jawaban di skema — tidak divalidasi
di submit Fase ini (lihat PHASE4_REPORT.md).

### POST /Lamaran_dokumen/upload — auth (multipart/form-data)
Field: `id_application_requirement` + file `document`. Syarat: lamaran masih
draft. Validasi: ukuran vs `max_file_size_kb` requirement (fallback
`career_upload_max_kb`), ekstensi vs `allowed_formats`, **MIME asli (finfo)
harus cocok** dengan ekstensi diizinkan. File disimpan ke
`application/storage/career/` (deny dari web), nama acak
`app{id}_req{id}_v{v}_{hex}.<ext>`. Checksum sha256 file identik →
respons 200 "File identik sudah diunggah" TANPA versi baru (idempotent).
201 → `{id_application_document, version, original_name, download_url}`.
Status requirement → `pending_review`. Audit log dicatat.

### GET /Lamaran_dokumen/detail/:id — auth
Unduh binary file milik kandidat bersangkutan (404 bila milik orang lain).

### GET /Notifikasi/index — auth
### PUT /Notifikasi/read/:id — auth (tandai sudah dibaca)

## Audit
Setiap aksi penting (register/login/logout/login_gagal/upload/unduh/submit)
dicatat di `career_audit_logs` + percobaan login/login gagal di
`career_login_attempts`. Kegagalan login berturut-turut menaikkan
`failed_login_count`; mencapai batas → `locked_until` = sekarang + lockout.
