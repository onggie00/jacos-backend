# MODULE MAP: acara_presensi

## Overview
Modul data presensi peserta per acara. Mencatat NPP, nama peserta, role, waktu presensi masuk & selesai, serta tautan sertifikat.

## Database
- **Tabel utama:** `acara_presensi`
- **Primary key:** `id_presensi`
- **Kolom utama:** id_acara, npp, peserta, role, role_lainnya, waktu_presensi, waktu_presensi_selesai, custom_sertifikat

## File Structure

### Controllers
| File | Fungsi |
|------|--------|
| `controllers/backend/Acara_presensi.php` | Controller utama: list, add, edit, delete, view, export Excel/PDF, single PDF, endpoint AJAX `detail_peserta`. |

### Models
| File | Fungsi |
|------|--------|
| `models/Model_acara_presensi.php` | CRUD acara_presensi + search/pagination dengan LEFT JOIN ke tabel `acara`. |

### Views
| File | Fungsi |
|------|--------|
| `views/backend/standart/administrator/acara_presensi/acara_presensi_list.php` | Halaman daftar presensi peserta per acara. |
| `views/backend/standart/administrator/acara_presensi/acara_presensi_add.php` | Form tambah peserta presensi. |
| `views/backend/standart/administrator/acara_presensi/acara_presensi_update.php` | Form edit peserta presensi. |
| `views/backend/standart/administrator/acara_presensi/acara_presensi_view.php` | Halaman detail peserta presensi. |

### Language
| File | Fungsi |
|------|--------|
| `language/english/web_lang.php` | Label bahasa untuk modul acara_presensi. |

## Key Features
1. CRUD peserta presensi dengan permission (`acara_presensi_list`, `acara_presensi_add`, `acara_presensi_update`, `acara_presensi_delete`, `acara_presensi_export`).
2. Filter/list presensi berdasarkan `id_acara`.
3. Link ke detail acara via popup view.
4. Tombol "Lihat sertifikat" mengarah ke `apiapp/acara/lihat_sertifikat`.
5. Export Excel & PDF.
6. Endpoint AJAX `detail_peserta` untuk mengisi modal DataTables di halaman acara list.

## Related Modules
- `acara` — master data acara; acara_presensi bergantung pada `id_acara`.
- `acara_evaluasi_form` — form evaluasi per acara.
- `acara_presensi_evaluasi` — jawaban evaluasi peserta.
- `acara_presensi_realtime_tracker` — monitoring real-time evaluasi.

## Routes (URL)
- `administrator/acara_presensi` — list
- `administrator/acara_presensi/add` — tambah
- `administrator/acara_presensi/edit/{id}` — edit
- `administrator/acara_presensi/view/{id}` — detail
- `administrator/acara_presensi/delete/{id}` — hapus
- `administrator/acara_presensi/export` — export Excel
- `administrator/acara_presensi/export_pdf` — export PDF
- `administrator/acara_presensi/detail_peserta` — AJAX detail peserta per id_acara

## Notes
- Export Excel saat ini masih mengandung query copy-paste dari modul siswa (`siswa_sd_aktif`, `kelas_sd`, `tahun_ajaran`) dan variabel `$case_spp` yang tidak didefinisikan — perlu diperbaiki jika fitur export acara_presensi benar-benar digunakan.
- Sertifikat bisa custom per peserta (`custom_sertifikat`) atau menggunakan template dari modul acara.
