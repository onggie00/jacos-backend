# MODULE MAP: status_daftar_ulang_sd

Modul admin untuk kelola **status daftar ulang siswa jenjang SD** — mirip FT tapi dengan integrasi payment **BRI** (bukan BNI). Fitur: aktivasi VA BRI, penerbitan slip/kwitansi/kartu sementara, generate siswa aktif, **update expired VA** (fitur unik SD).

## Struktur File

```
status_daftar_ulang_sd/
├── controllers/
│   ├── Status_daftar_ulang_sd.php            # Root level (tidak dipanggil via routing)
│   ├── Status_daftar_ulang_sd_old.php        # Backup root level
│   └── backend/
│       └── Status_daftar_ulang_sd.php        # Controller utama
├── models/
│   └── Model_status_daftar_ulang_sd.php      # Model utama (CRUD + export XLS)
├── views/
│   └── backend/standart/administrator/status_daftar_ulang_sd/
│       ├── status_daftar_ulang_sd_list.php   # Halaman list + 2 modal (Siswa Aktif + Update Expired)
│       ├── status_daftar_ulang_sd_update.php # Form edit + handler BRI VA
│       └── status_daftar_ulang_sd_view.php   # Detail view
└── language/
    └── english/web_lang.php
```

## Fungsi Utama per File

| File | Fungsi |
|------|--------|
| `controllers/backend/Status_daftar_ulang_sd.php` | Class `Status_daftar_ulang_sd extends Admin`. Methods: `index` (list+filter), `edit/edit_save` (update status + integrasi BRI VA), `delete`, `view`, `update_status` & `update_status_new` & `update_status2` (bulk aktivasi VA BRI), `siswa_aktif` (bulk generate siswa_sd_aktif), `update_expired_va` (bulk update expired date VA BRI — **fitur unik SD**), `export`, `export_pdf`, `single_pdf`, `create_va_bri`, `update_va_bri`, `update_va_bri_status`, `delete_va_bri`, `create_billing` (BNI legacy), `cetak_*`, `send_email_file`. |
| `Model_status_daftar_ulang_sd.php` | Field search: `id_siswa_sd, status, slip_pembayaran, kwitansi, kartu_sementara`. Methods: `count_all`, `get`, `join_avaiable` (LEFT JOIN siswa_sd + LEFT JOIN transaksi dengan `group_by va_bri` + select `expired_datetime`), `filter_avaiable`, `join_avaiable_export`, `export_siswa`. |
| `status_daftar_ulang_sd_list.php` | Tabel list, filter single-field, bulk action (Aktivasi VA / Siswa Aktif / **Update Expired**), 2 modal, pagination. |

## Perbedaan Kunci vs Modul FT

| Aspek | FT | SD |
|-------|----|----|
| Payment gateway | BNI (BniEnc) | BRI (BriApi) |
| Field VA | `va_number` | `va_bri` |
| Library | `BniEnc::encrypt/decrypt` | `BriApi::create/update/delete/updateBayar` |
| Bulk action ke-3 | (tidak ada) | **Update Expired VA** |
| Modal | 1 (`#modal_add_new`) | 2 (`#modal_add_new` + `#modal_update_expired`) |
| Skema `status` | smallint(6) | int(11) |
| Join transaksi | tidak | ya (untuk `expired_datetime`) |

## Skema Tabel Inti (referensi)

**`status_daftar_ulang_sd`** (PK `id_daftar_ulang`):
- `id_siswa_sd` (FK → siswa_sd)
- `status` (int: 0=Menunggu Aktivasi, 1=VA Aktif, 2=Lunas)
- `va_bri`, `slip_pembayaran`, `kwitansi`, `kartu_sementara`
- `tanggal_lulus` (datetime), `tgl_daftar_ulang` (date), `tgl_aktivasi` (datetime), `tgl_bayar` (datetime)
- `custom_payment` (int)

**`siswa_sd`** (di-join, kolom untuk filter):
- `nama_lengkap`, `email`, `no_peserta`, `nisn`
- `tahun_ajaran` (varchar, mis. "2026/2027")
- `gelombang` (tinyint: 1, 2)
- `va_number` (BNI, tidak dipakai di status_daftar_ulang_sd), `va_number_bri` (BRI, sumber untuk join)

## Statistik Data (read-only cek 2026-07-16)

- Total: 550 rows
- Status: 0=3, 1=82, 2=465
- Tahun ajaran: 2026/2027
- Gelombang: 1 (175 siswa), 2 (25 siswa)

## Catatan Penting

- Modul ini **business-critical**: setiap perubahan `status` ke 1 memicu pembuatan VA BRI + email; status 2 cetak kwitansi + kartu siswa.
- Bulk action `Siswa Aktif` insert ke `siswa_sd_aktif` (transaksi).
- Ada file `_old.php` di controller (root level + backup) — jangan disentuh saat refactor UI.
- Field `va_bri` panjang (13 digit) — pertimbangkan truncate atau `text-overflow` saat styling tabel.
