# MODULE MAP: status_daftar_ulang_smp

Modul admin untuk kelola **status daftar ulang siswa jenjang SMP** — mirip FT secara struktural (BNI, va_number) dengan tambahan logika **PPSBB** (Peserta Program Santri Berprestasi Baitul Qur'an). Saat ini tabel masih kosong (0 rows), tapi pola UI perlu disiapkan untuk konsistensi.

## Struktur File

```
status_daftar_ulang_smp/
├── controllers/
│   └── backend/
│       └── Status_daftar_ulang_smp.php        # Controller utama
├── models/
│   └── Model_status_daftar_ulang_smp.php      # Model utama (CRUD + export XLS)
├── views/
│   └── backend/standart/administrator/status_daftar_ulang_smp/
│       ├── status_daftar_ulang_smp_list.php   # Halaman list + modal siswa_aktif
│       ├── status_daftar_ulang_smp_update.php # Form edit + handler BNI VA
│       └── status_daftar_ulang_smp_view.php   # Detail view
└── language/
    └── english/web_lang.php
```

**Catatan struktur:** Lebih ramping dari SD — tidak ada `_old.php` backup, tidak ada root-level controller, tidak ada `add` view.

## Fungsi Utama per File

| File | Fungsi |
|------|--------|
| `controllers/backend/Status_daftar_ulang_smp.php` | Class `Status_daftar_ulang_smp extends Admin`. Methods: `index` (list+filter), `edit/edit_save`, `delete`, `view`, `update_status` (bulk aktivasi VA, max 5 data), `siswa_aktif` (bulk generate siswa_smp_aktif), `export`, `export_pdf`, `single_pdf`, `create_billing`, `cancel_billing`, `cetak_*`, `send_email_file`. |
| `Model_status_daftar_ulang_smp.php` | Field search: `id_siswa_smp, status, slip_pembayaran, kwitansi, kartu_sementara, tanggal_lulus, tgl_daftar_ulang`. Methods: `count_all`, `get`, `join_avaiable` (LEFT JOIN siswa_smp), `filter_avaiable`, `join_avaiable_export`, `export_siswa`. |
| `status_daftar_ulang_smp_list.php` | Tabel list, filter single-field, bulk action (Aktivasi VA / Siswa Aktif), 1 modal, pagination. |

## Perbedaan Kunci vs FT dan SD

| Aspek | FT | SD | SMP |
|-------|----|----|-----|
| Payment gateway | BNI | BRI | **BNI** (sama dgn FT) |
| Field VA | `va_number` | `va_bri` | **`va_number`** (sama dgn FT) |
| Bulk limit | 10 | 20 | **5** (paling ketat) |
| Logika khusus | — | `is_mutasi` | **`ppsbb`** (PSB vs PPSBB SMP) |
| Tipe pendaftaran | "PSB FT" | "PSB SD" | **"PSB SMP" / "PPSBB SMP"** |
| No transaksi prefix | LDUI-FT- | LDUI-SD- | **LDUI-PSBSMP- / LDUI-PPSBBSMP-** |
| Skema `status` | smallint(6) | int(11) | **smallint(6)** (sama dgn FT) |
| Bulk action ke-3 | — | Update Expired | **(tidak ada)** |
| Modal | 1 | 2 | **1** |

## Skema Tabel Inti (referensi)

**`status_daftar_ulang_smp`** (PK `id_daftar_ulang`) — **IDENTIK dengan FT:**
- `id_siswa_smp` (FK → siswa_smp)
- `status` (smallint: 0=Menunggu Aktivasi, 1=VA Aktif, 2=Lunas)
- `va_number`, `slip_pembayaran`, `kwitansi`, `kartu_sementara`
- `tanggal_lulus` (datetime), `tgl_daftar_ulang` (date), `tgl_aktivasi` (datetime), `tgl_bayar` (datetime)
- `custom_payment` (int)

**`siswa_smp`** (di-join, kolom untuk filter):
- `nama_lengkap`, `email`, `no_peserta`, `nisn`, `va_number`
- `tahun_ajaran` (varchar, mis. "2026/2027")
- `gelombang` (tinyint: NULL/0/1/2/3)
- `ppsbb` (tinyint, tambahan: 1=PPSBB, 2=PSB reguler)

## Statistik Data (read-only cek 2026-07-16)

- `status_daftar_ulang_smp`: **0 rows** (belum ada data) ⚠️
- `siswa_smp`: 2080 baris (`id_siswa_smp` terakhir = 2080)
- Siswa SMP aktif terdistribusi per tahun ajaran & gelombang (lihat query manual)

## Catatan Penting

- **Modul ini business-critical** ketika sudah ada datanya: setiap perubahan `status` ke 1 memicu pembuatan VA BNI + email; status 2 cetak kwitansi + kartu siswa.
- Bulk action `Siswa Aktif` insert ke `siswa_smp_aktif` (transaksi).
- **BUG ditemukan**: di method `update_status`, line `$this->mymodel->update('status_daftar_ulang_sd', ...)` — typo, harusnya `status_daftar_ulang_smp`. **Tidak akan disentuh** (di luar scope refactor UI).
- **Tidak ada `_old.php`** backup atau root-level controller — lebih bersih dari FT/SD.
- Bulk limit 5 data lebih ketat dari FT (10) dan SD (20).
- Karena tabel kosong, testing visual sulit — perlu insert sample data dulu untuk verifikasi (di luar scope).
