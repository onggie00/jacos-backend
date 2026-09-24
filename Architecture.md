# Architecture.md — Jacos (Jakarta Cosmopolite Islamic School)

> Dokumen ini menjawab **BAGAIMANA** sistem dibangun secara teknis.
> Untuk konteks produk → `PRD.md`. Untuk aturan kerja agent → `Rules.md`.
> Untuk daftar modul & tabel → `Schema.md`. Untuk UI → `Design.md`.

## 1. Base Framework

CiCOOL — CodeIgniter 3 CRUD generator, versi base `v3.1.0`. Fitur bawaan CiCOOL yang dipakai:
Wizard Setup, CRUD Generator, Auto-generate Web API, Full Auth (Aauth), Form Builder,
HTML Page Builder, AdminLTE theme.

## 2. Environment — Laragon

Project ini jalan di **Laragon** (local dev). Jangan asumsikan setup Docker lama.

| Item | Value |
|---|---|
| Web server | Apache via Laragon |
| PHP | **7.4.33** |
| Database | MySQL via Laragon, `127.0.0.1:3306`, database `jacos_db` |
| Kredensial | Lihat `application/config/database.php` (gitignored) |

**Command referensi:**

```bash
# Cek PHP version
php -v

# Query (eksplorasi skema — lihat skill jacos-db-explorer)
mysql -u root jacos_db -e "<SQL>"
```

## 3. Tech Stack

### Backend
- **Framework:** CodeIgniter 3 (kompatibel PHP 5.2.4+)
- **Package manager:** Composer
- **Database:** MySQL via CI Query Builder — hanya `jacos_db`
- **Auth:** Aauth library (CodeIgniter-Aauth)
- **Library kunci:** `kreait/firebase-php` v6, `google/apiclient` 2.14, `simplesoftwareio/simple-qrcode`, `setasign/fpdf`, `filp/whoops`, REST_Controller custom, Template custom, PHPMailer, MoodleRest custom
- **Payment:** BRI API, BNI Virtual Account

### Frontend
- AdminLTE (admin template) + Bootstrap 3
- jQuery 2.2.3, SweetAlert, Toastr.js, FancyBox, Chosen, DateTimePicker
- Font Awesome 4.5, Ionicons
- **Server-side rendered — tidak ada React/Vue/Angular**

### API
- Mobile API: `application/controllers/apiapp/` (60+ controller)
- Web API: `application/controllers/apiweb/`
- Secondary DB API: `application/controllers/apiweb_sec_db/`
- Auth: JWT + session-based

## 4. Struktur Folder

```
jacos/
├── application/
│   ├── controllers/{apiapp, apiweb, apiweb_sec_db, [module]}/
│   ├── models/{Mymodel.php, Second_db.php}
│   ├── views/{backend, core_template, [views]}/
│   ├── config/{database.php, routes.php, autoload.php}
│   ├── helpers/, libraries/, logs/
├── modules/                 # HMVC — 200+ modul (lihat Schema.md)
├── pi-skills/                # Agent skills
├── asset/                   # CSS/JS termasuk labs-ui (lihat Design.md)
├── system/, vendor/, uploads/
├── Berkas_sample/            # Dokumen kerja & output test (lihat Rules.md)
│   └── agent-prompt/         # Arsip prompt untuk perubahan skala besar
├── TASKS.md                  # Checklist kerja aktif (root only, gitignored)
├── HANDOFF.md                 # Checkpoint sesi (opsional, gitignored)
└── AGENT.md
```

## 5. Konfigurasi Kunci

| File | Fungsi |
|---|---|
| `application/config/database.php` | Koneksi MySQL |
| `application/config/config.php` | Konfigurasi utama CI |
| `application/config/routes.php` | Routing URL |
| `application/config/autoload.php` | Auto-load resource |
| `application/config/constants.php` | `BASE_URL`, dll |
| `application/config/jwt.php` | JWT settings |
| `application/config/rest.php` | REST API settings |
| `application/config/aauth.php` | Auth settings |
| `application/config/site.php` | Setting spesifik site (gitignored) |

Env var penting: `BASE_URL` (auto-detect), `BASE_ASSET` = `BASE_URL . 'asset/'`,
`ADMIN_NAMESPACE_URL` = `'administrator'`, `VERSION` = `'3.1.0'`,
`EXTENSION_PATH` = `FCPATH . 'cc-content/extensions/'`.

## 6. Integrasi Eksternal (level teknis)

| Integrasi | Library/Cara |
|---|---|
| Firebase Cloud Messaging | `Fcm` library |
| BRI Virtual Account + SNAP BI push notif | `BriApi` library, private key `key_bri_sd_private.pem` — untuk SD dikontrol flag rollback per alur di `pengaturan_akun`: `bank_psb_sd`, `bank_mutasi_sd`, `bank_daftar_ulang_sd` (default `BRI`; set `BNI` utk migrasi per alur, lihat catatan di bawah) |
| BNI Virtual Account (eCollection VA Credit) | library `BniEnc`, webhook `apiapp/Payment_notification.php`; kredensial di `pengaturan_akun` (`bni_client_id`, `bni_prefix`, dst — pola `_ft`/`_spp` per fungsi). Dipakai: PSB SD (opsional via flag), daftar ulang SD (flag `bank_daftar_ulang_sd`), KB (`kode VA 11`), TK (`12`), FT, SPP |
| Moodle LMS | `MoodleRest` library |
| Google API | `google/apiclient`, service account `labscib-app-c0ca345e64d9.json` |

**Catatan migrasi VA SD BRI→BNI**: siswa SD yang sudah punya VA BRI aktif tetap valid
sampai lunas — tidak ada migrasi paksa. Rollback per alur cukup set ulang flag di
`pengaturan_akun`. Sebelum flag `bank_psb_sd` di-switch ke `BNI` di production, wajib
testing staging dengan dummy data (alur pendaftaran publik = production-facing).

## 7. Keamanan

- **Jangan commit**: `database.php`, `site.php`, `key_bri_sd_private.pem`, `labscib-app-c0ca345e64d9.json`
- Sanitasi input pakai CI security helper, validasi CSRF di form, HTTPS wajib di production
- Detail file apa saja yang di-gitignore → lihat `.gitignore` di root

## 8. Error Handling & Profiling

- Development: Whoops (pretty error page), profiler aktif (kecuali endpoint API)
- Production: error disembunyikan, log ke `application/logs/`
