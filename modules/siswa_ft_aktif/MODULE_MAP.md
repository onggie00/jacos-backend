# MODULE MAP: siswa_ft_aktif

Modul admin untuk kelola **data siswa aktif France Track (FT)** — hasil generate dari `status_daftar_ulang_ft` bulk action. Fitur lengkap: multi-filter builder, sort, import Excel, generate NIS, sinkronisasi Microsoft 365.

## Struktur File

```
siswa_ft_aktif/
├── controllers/
│   └── backend/
│       └── Siswa_ft_aktif.php                 # Controller utama
├── models/
│   └── Model_siswa_ft_aktif.php               # Model dengan multi-filter support
├── views/
│   └── backend/standart/administrator/siswa_ft_aktif/
│       ├── siswa_ft_aktif_list.php            # Halaman list + filter builder + modals
│       ├── siswa_ft_aktif_add.php             # Form add siswa
│       ├── siswa_ft_aktif_add_raport.php      # Form tambah raport
│       ├── siswa_ft_aktif_update.php          # Form edit + sinkronisasi SPP
│       └── siswa_ft_aktif_view.php            # Detail view
└── language/
    └── english/web_lang.php
```

## Fungsi Utama per File

| File | Fungsi |
|------|--------|
| `Siswa_ft_aktif.php` | Class `Siswa_ft_aktif extends Admin`. Methods: `index` (multi-filter+sort), `add/add_save`, `edit/edit_save`, `delete`, `view`, `get_detail` (AJAX JSON), `export` (custom XLS), `export_pdf`, `single_pdf`, `add_raport/add_raport_save`, `upload_file_raport_file`, `delete_file_raport_file`, `generate_nis`, `import_siswa`, `import_raport_siswa`, `update_password`, `reset_password`, `refresh_token` (Microsoft Graph API). |
| `Model_siswa_ft_aktif.php` | Field search: `nama_lengkap, nis, id_kelas, id_siswa_ft, id_tahun_ajaran, kewarganegaraan, nik, golongan_darah, telp, ...`. Field search join: `kelas_ft.label, tahun_ajaran.label`. Methods: `count_all`, `get` (sort+filter+join), `join_avaiable` (LEFT JOIN kelas_ft, siswa_ft, tahun_ajaran), `_build_multi_where` (operator: contains/equals/starts_with/ends_with/gt/lt), `export_siswa`. |
| `siswa_ft_aktif_list.php` | Filter builder (add row field+operator+value), sort dropdown, bulk action (delete), modal Generate NIS, Import Siswa, Import Raport, Update Password, Detail Siswa (AJAX). |

## Pola Multi-Filter (referensi untuk refactor `status_daftar_ulang_ft`)

### Controller
- `_parse_filters()`: baca GET `ff[]`, `fo[]`, `fv[]` → array `[['field','operator','value'], ...]`
- `index()`: jika ada multi-filter → panggil `model->get(null, null, $limit, $offset, [], $sort, $sort_type, $multi_filters)`

### Model
- `_build_multi_where($filters)`: switch operator → SQL condition, gabung dengan `AND`
- `field_map`: whitelist alias field → DB column (untuk amankan dari SQL injection via GET)

### View
- JavaScript `addFilterRow()`, `updateFilterNumbers()`, sinkronisasi dropdown field ke operator & value
- Hidden inputs `ff[]`, `fo[]`, `fv[]` di-submit via form GET

## Skema Tabel Inti

**`siswa_ft_aktif`** (PK `id_siswa_ft_aktif`):
- `id_siswa_ft` (FK), `nama_lengkap`, `nis`, `nik`, `telp`, `kewarganegaraan`, `golongan_darah`
- `id_kelas` (FK → kelas_ft), `id_tahun_ajaran` (FK → tahun_ajaran)
- `pendidikan_ayah/ibu`, `penghasilan_ayah/ibu`, `tgl_lahir_ayah/ibu`
- `spp_custom`, `spp_type` (FULL/HALF/FREE), `acc_ujian` (Boleh/Tidak Boleh)
- `nomor_peserta_ujian`, `file_raport`
