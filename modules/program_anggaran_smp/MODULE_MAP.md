# MODULE MAP: program_anggaran_smp

## Overview
Modul pengajuan & pelaporan program anggaran untuk jenjang SMP. Mengelola proposal pengajuan, laporan kegiatan, status pengajuan/pencairan, serta notifikasi email ke bagian anggaran.

## Database
- **Tabel utama:** `program_anggaran_smp`
- **Primary key:** `id`
- **Kolom utama:** nomor_program, tahun_ajaran, nama_program, tanggal_program, jenis_kegiatan, sub_jenis_kegiatan, nominal_okr, nominal_pengajuan, status_pengajuan, tanggal_pencairan, catatan_pengajuan, status_laporan, catatan_laporan, file_proposal_pengajuan, file_proposal_keuangan, file_laporan_kegiatan, file_laporan_keuangan

## File Structure

### Controllers
| File | Fungsi |
|------|--------|
| `controllers/backend/Program_anggaran_smp.php` | Controller utama: list, add, edit, delete, view, AJAX `get_program`, AJAX `get_detail`, export Excel/PDF, single PDF, email notification, file upload handlers. |

### Models
| File | Fungsi |
|------|--------|
| `models/Model_program_anggaran_smp.php` | CRUD, search, pagination, multi-filter (ff/fo/fv), join ke `program_anggaran`, `program_anggaran_status_pengajuan`, `program_anggaran_status_laporan`. |

### Views
| File | Fungsi |
|------|--------|
| `views/backend/standart/administrator/program_anggaran_smp/program_anggaran_smp_list.php` | Halaman daftar dengan filter multi-kolom & modal detail. |
| `views/backend/standart/administrator/program_anggaran_smp/program_anggaran_smp_add.php` | Form tambah pengajuan. |
| `views/backend/standart/administrator/program_anggaran_smp/program_anggaran_smp_update.php` | Form edit pengajuan/laporan. |
| `views/backend/standart/administrator/program_anggaran_smp/program_anggaran_smp_view.php` | Halaman detail. |

### Language
| File | Fungsi |
|------|--------|
| `language/english/web_lang.php` | Label bahasa modul. |

## Key Features
1. CRUD pengajuan program anggaran SMP dengan permission.
2. Multi-filter advanced (field, operator, value via GET `ff[]`, `fo[]`, `fv[]`).
3. Validasi sisa OKR otomatis saat add/update.
4. Upload file proposal pengajuan, proposal keuangan, laporan kegiatan, laporan keuangan.
5. Status pengajuan & laporan dengan warna/badge.
6. Tombol detail modal (`get_detail`) dengan info OKR, status, dan file.
7. Export Excel & PDF.
8. Kirim email notifikasi ke `anggaran@labschoolcibubur.sch.id` saat submit proposal/laporan.
9. Cetak kwitansi saat pencairan oleh user keuangan.

## Related Modules
- `program_anggaran` — master nomor program & nominal OKR.
- `program_anggaran_status_pengajuan` — master status pengajuan.
- `program_anggaran_status_laporan` — master status laporan.
- `program_anggaran_sd`, `program_anggaran_sma` — modul serupa untuk jenjang lain.

## Routes (URL)
- `administrator/program_anggaran_smp`
- `administrator/program_anggaran_smp/add`
- `administrator/program_anggaran_smp/edit/{id}`
- `administrator/program_anggaran_smp/view/{id}`
- `administrator/program_anggaran_smp/get_program/{id}`
- `administrator/program_anggaran_smp/get_detail/{id}`
- `administrator/program_anggaran_smp/export`
- `administrator/program_anggaran_smp/export_pdf`

## Notes
- Folder upload: `uploads/program_anggaran_smp/`.
- File `program_anggaran_sma.php` di folder SD kemungkinan file sisa/salah tempat.
