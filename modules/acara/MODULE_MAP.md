# MODULE MAP: acara

## Overview
Modul master data acara/kegiatan sekolah. Mengelola informasi acara, QR code check-in/check-out, template sertifikat, dan integrasi ke modul presensi & evaluasi.

## Database
- **Tabel utama:** `acara`
- **Primary key:** `id_acara`
- **Kolom utama:** peserta_acara, nama_acara, unique_code, unique_code_finish, narasumber, keterangan, qr_code, qr_code_finish, waktu_mulai, waktu_selesai, lokasi, is_certificated, no_certificate, file_certificate, file_certificate_back

## File Structure

### Controllers
| File | Fungsi |
|------|--------|
| `controllers/backend/Acara.php` | Controller utama: list, add, edit, delete, view, upload file sertifikat, export Excel/PDF, single PDF. |
| `controllers/backend/Acara_old.php` | File lama/legacy (backup versi sebelumnya). |

### Models
| File | Fungsi |
|------|--------|
| `models/Model_acara.php` | CRUD acara + search/pagination. Memiliki method cross-module: `get_role_breakdown()`, `get_status_realtime()`, `get_activity_feed()`, `get_acara_detail()` yang membaca tabel `acara_presensi`, `acara_presensi_evaluasi`, dan `acara_evaluasi_form`. |

### Views
| File | Fungsi |
|------|--------|
| `views/backend/standart/administrator/acara/acara_list.php` | Halaman daftar acara dengan modal DataTables detail peserta, tombol ke Form Evaluasi & Hasil Evaluasi. |
| `views/backend/standart/administrator/acara/acara_add.php` | Form tambah acara (multi-select peserta, QR in/out, upload sertifikat). |
| `views/backend/standart/administrator/acara/acara_update.php` | Form edit acara. |
| `views/backend/standart/administrator/acara/acara_view.php` | Halaman detail acara. |
| `views/backend/standart/acara_list_old.php` | View lama/legacy. |

### Language
| File | Fungsi |
|------|--------|
| `language/english/web_lang.php` | Label bahasa untuk modul acara. |

## Key Features
1. CRUD acara dengan permission (`acara_list`, `acara_add`, `acara_update`, `acara_delete`, `acara_export`).
2. Generate QR Code check-in (`unique_code`) dan check-out (`unique_code_finish`) otomatis.
3. Upload template sertifikat depan & belakang (`file_certificate`, `file_certificate_back`).
4. Export Excel & PDF.
5. Modal detail peserta via AJAX ke `administrator/acara_presensi/detail_peserta`.
6. Tombol navigasi ke modul terkait: `acara_evaluasi_form`, `acara_presensi_evaluasi`.

## Related Modules
- `acara_presensi` — data peserta & presensi per acara.
- `acara_evaluasi_form` — form pertanyaan evaluasi per acara.
- `acara_presensi_evaluasi` — jawaban evaluasi peserta.
- `acara_presensi_realtime_tracker` — monitoring real-time evaluasi.

## Routes (URL)
- `administrator/acara` — list
- `administrator/acara/add` — tambah
- `administrator/acara/edit/{id}` — edit
- `administrator/acara/view/{id}` — detail
- `administrator/acara/delete/{id}` — hapus
- `administrator/acara/export` — export Excel
- `administrator/acara/export_pdf` — export PDF

## Notes
- QR code disimpan di `uploads/acara/`.
- File lama (`Acara_old.php`, `acara_list_old.php`) sebaiknya di-review apakah masih diperlukan.
