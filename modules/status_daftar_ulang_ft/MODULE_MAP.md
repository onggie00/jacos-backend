# MODULE MAP: status_daftar_ulang_ft

Modul admin untuk kelola **status daftar ulang siswa jalur France Track (FT)** — mencakup aktivasi VA BNI, penerbitan slip/kwitansi/kartu sementara, dan generate siswa aktif ke tabel `siswa_ft_aktif`.

## Struktur File

```
status_daftar_ulang_ft/
├── controllers/
│   └── backend/
│       ├── Status_daftar_ulang_ft.php        # Controller utama (CRUD + bulk action)
│       └── Status_daftar_ulang_ft_old.php     # Backup lama (jangan dihapus dulu)
├── models/
│   └── Model_status_daftar_ulang_ft.php       # Model utama (CRUD + export XLS)
├── views/
│   └── backend/standart/administrator/status_daftar_ulang_ft/
│       ├── status_daftar_ulang_ft_list.php    # Halaman list + filter + bulk action + modal siswa_aktif
│       ├── status_daftar_ulang_ft_add.php     # Form add (jarang dipakai, ada generate otomatis)
│       ├── status_daftar_ulang_ft_update.php  # Form edit + handler aktivasi VA BNI
│       └── status_daftar_ulang_ft_view.php    # Detail view
└── language/
    └── english/web_lang.php                   # Label bahasa modul
```

## Fungsi Utama per File

| File | Fungsi |
|------|--------|
| `Status_daftar_ulang_ft.php` | Class `Status_daftar_ulang_ft extends Admin`. Endpoint: `index` (list+filter), `add/add_save`, `edit/edit_save` (update status + integrasi BNI), `delete`, `view`, `update_status` (bulk aktivasi VA), `siswa_aktif` (bulk generate siswa_aktif), `export`, `export_pdf`, `single_pdf`, `create_billing`, `cancel_billing`, `cetak_*`, `send_email_file*`. |
| `Model_status_daftar_ulang_ft.php` | Field search: `id_siswa_ft, status, slip_pembayaran, kwitansi, kartu_sementara, tanggal_lulus, tgl_daftar_ulang`. Methods: `count_all`, `get`, `join_avaiable` (LEFT JOIN `siswa_ft`), `filter_avaiable`, `join_avaiable_export`, `export_siswa` (XLS). |
| `status_daftar_ulang_ft_list.php` | Tabel list, filter single-field (`q`+`f`), bulk action (Aktivasi VA / Siswa Aktif), modal siswa_aktif (pilih kelas+tahun ajaran+SPP custom), pagination. |

## Skema Tabel Inti (referensi)

**`status_daftar_ulang_ft`** (PK `id_daftar_ulang`):
- `id_siswa_ft` (FK → siswa_ft)
- `status` (smallint: 0=Menunggu Aktivasi, 1=VA Aktif, 2=Lunas)
- `va_number`, `slip_pembayaran`, `kwitansi`, `kartu_sementara`
- `tanggal_lulus` (datetime), `tgl_daftar_ulang` (date), `tgl_aktivasi` (datetime), `tgl_bayar` (datetime)
- `custom_payment` (int)

**`siswa_ft`** (di-join, kolom yang relevan untuk filter):
- `nama_lengkap`, `email`, `no_peserta`, `nisn`
- `tahun_ajaran` (varchar, mis. "2026/2027")
- `gelombang` (tinyint: NULL/0/1/2/3)

## Catatan Penting

- Modul ini **business-critical**: setiap perubahan `status` ke 1 memicu pembuatan VA BNI + email; status 2 cetak kwitansi + kartu siswa.
- Bulk action `Siswa Aktif` melakukan INSERT ke tabel `siswa_ft_aktif` dalam transaction (rollback jika ada yg gagal).
- Ada file `_old.php` di controller — backup, jangan disentuh saat refactor UI.
