# Schema.md — Peta Modul & Database

> Dokumen ini menjawab **modul/tabel apa saja yang ada** dan **konvensi penamaannya**.
> **Ini BUKAN sumber kebenaran skema** — untuk struktur tabel/kolom real-time, selalu pakai
> skill `jacos-db-explorer`. Dokumen ini hanya peta orientasi supaya tidak perlu grep
> seluruh `modules/` dari nol.

## 1. Konvensi Penamaan Modul per Jenjang

Banyak modul punya 4 varian jenjang dengan suffix yang sama:

| Suffix | Jenjang |
|---|---|
| `_ft` | France Track (SMA, jalur khusus — BUKAN PAUD/TK; lihat catatan bawah) |
| `_sd` | SD |
| `_smp` | SMP |
| `_sma` | SMA/SMK |
| `_kb` | KB — Kelompok Bermain/PAUD (sejak 2026, lihat §6) |
| `_tk` | TK — Taman Kanak-kanak (sejak 2026, lihat §6) |

⚠️ **Koreksi lama**: `_ft` sering (keliru) disebut "Fase Tumbuh (PAUD/TK)". Faktanya FT =
**France Track** (kelas 10–12, jalur prestasi; siswa_ft = pendaftar PSB France Track).

## 6. Jenjang KB & TK (baru)

- Tabel: `siswa_kb/tk` (clone siswa_sd − ppsbb/va_number_bri + `id_tingkatan`, default 0),
  `kelas_kb/tk` (clone kelas_sd, tanpa tingkatan Mutasi/Alumni),
  `tingkatan_kb/tk` (label, `usia_min`, `usia_max`, biaya_spp — master untuk validasi usia),
  `siswa_kb/tk_aktif` (clone siswa_sd_aktif + `id_tingkatan` & `tgl_lahir`; relasi logis
  `id_siswa_kb/tk`, kelas → kelas_kb/tk, tanpa FK — dibuat staff via import Excel/form manual
  modul aktif, TANPA trigger otomatis dari webhook/pembayaran; ada **import massal CREATE**
  2-langkah dgn preview utk migrasi data historis: `siswa_kb/tk_aktif/import_create`,
  template via `template_import_create`, id_siswa_kb/tk = 0, validasi NIS duplikat/kelas/required),
  `status_daftar_ulang_kb/tk` (backend only, tanpa modul). Seed biaya:
  pendaftaran KB 1.000.000 / TK 2.100.000; SPP kb 1.600.000 / tk 1.700.000.
- Bank: **BNI eCollection** (BniEnc, kredensial `bni_client_id` utama). Kode VA:
  KB=`11`, TK=`12`; unit no_peserta webhook: KB=`51`, TK=`52`
  (lihat `Payment_notification.php`). VA di-generate via
  `administrator/siswa_{kb,tk}/generate_va_bni?id_siswa=X` atau otomatis saat alur
  pendaftaran publik KB/TK dibuat.
- Dashboard PSB: `dashboard_psb/grafik_kb`, `grafik_tk`.
- Modul `status_daftar_ulang_kb/tk` & `spp_kb/tk`: BELUM ADA (out of scope saat ini).
- **Utang teknis KB/TK** (diputuskan skip — siswa usia 2–6): `siswa_kb/tk_aktif_raport`,
  endpoint siswa-aktif app di **DUA kanal** — `apiapp/siswa/` dan `apiweb/siswa/`
  (85 file, mirror; Mobile App tidak dipakai KB/TK) —, endpoint cetak di
  `apiweb_sec_db/`, `spp_kb/tk` di `keuangan_dashboard_spp` (lihat atas).
- **PSB KB/TK publik**: endpoint website di `apiweb/psb/Pendaftaran_kb.php` &
  `Pendaftaran_tk.php` (VA BNI kode 11/12, validasi usia via helper `psb_cek_usia()`
  di app_helper). Mobile App tidak punya alur pendaftaran (redirect ke website).
- **Migrasi VA SD BRI→BNI berlaku di KEDUA kanal** (`apiapp/siswa/` dan
  `apiweb/psb/` — pendaftaran & mutasi; flag `bank_psb_sd`/`bank_mutasi_sd` sama).

Contoh: `siswa_ft`, `siswa_sd`, `siswa_smp`, `siswa_sma` — struktur controller/model/view mirip,
beda hanya scope jenjang.

## 2. Peta Modul per Kategori (200+ modul HMVC di `modules/`)

**Akademik:**
`siswa_{ft,sd,smp,sma}` (+ active/raport variant), `guru_{ft,sd,smp,sma}` (+ piket/duty),
`kelas_{ft,sd,smp,sma}`, `mata_pelajaran_{ft,sd,smp,sma}`, `jadwal_mapel_{ft,sd,smp,sma}`,
`jadwal_ujian_{ft,sd,smp,sma}`, `ujian_mapel`, `ujian_ruang`, `nilai_raport_{ft,sma,smp}`

**Keuangan:**
`spp_{ft,sd,smp,sma,kb,tk}`, `transaksi`, `transaksi_spp`, `transaksi_lain_*`, `trans_bri`,
`trans_bri_open`, `payment_response_bri`, `program_anggaran` (+ `_sd`/`_sma`/`_smp`)

**SPP KB/TK** (VA BNI, `bni_client_id_spp`, kode VA **KB=80, TK=81**): generate manual
bulk via modul spp_kb/tk (tombol Generate SPP) atau API `Generate_all_tagihan` +
`Create_tagihan`; push payment via `Payment_notification_spp` (map `Spp_payment_detail`).
**Utang teknis**: `keuangan_dashboard_spp` (whitelist 'SD','SMP','SMA','FT' di controller
aging_detail + model branch UNION + mapping kelas) belum mencakup KB/TK.

**PSB (Penerimaan Siswa Baru):**
`biaya_pendaftaran`, `ketentuan_pendaftaran_*`, `status_daftar_ulang_{ft,sd,smp,sma}` (+
`_kb`/`_tk` backend-only), `informasi_psb`, `web_informasi_psb`, `dashboard_psb`
(grafik sd/smp/sma/ft/ppsbbft/kb/tk)

**Presensi:**
`presensi_{ft,sd,smp,sma}` (lihat catatan presensi WiFi SMA di area terpisah),
`presensi_office`, `presensi_office_submission`,
`presensi_setting_role`, `presensi_setting_shift`, `presensi_setting_status`

**Ekstrakurikuler:**
`ekskul_manajemen_{ft,sd,smp,sma}`, `ekskul_member_{ft,sd,smp,sma}`,
`ekskul_presensi_pelatih`, `ekskul_presensi_siswa_{ft,sd,smp,sma}`

**KPI & Kinerja:**
`kpi_{ft,sd,smp,sma}`, `kpi_pegawai`, `kpi_pimpinan_{sd,sma,smp}`,
`laporan_kinerja_guru_{ft,sd,smp,sma}`, `laporan_kinerja_staff`

**Kesehatan (MHCU):**
`mhcu_sesi` (master referensi desain, lihat `Design.md`), `mhcu_peringatan` (dashboard krisis)

**Administrasi:**
`user`, `group`, `permission`, `access`, `setting`, `menu`, `menu_type`,
`list_notifikasi`, `mailbox_*`, `alumni_{ft,sd,smp,sma}`

**Web & App:**
`web`, `page`, `blog`, `galeri_foto`, `apps_assets`, `apps_banner`, `apps_icon`,
`apps_splash_screen`, `apps_version`, `kontak_labschool`, `tipe_kontak`

## 3. Model & Query Pattern

Semua modul pakai model utama `Mymodel` (auto-loaded) untuk operasi umum:

```php
$this->mymodel->getall('table_name');
$this->mymodel->getbywhere('table_name', 'column', 'value', 'row');
$this->mymodel->insertid('table_name', $data);
$this->mymodel->update('table_name', $data, 'where_column', $where_value);
$this->mymodel->delete('table_name', 'where_column', $where_value);
```

Untuk raw SQL, pakai method `withquery` (parameterized) — jangan concat string SQL manual.

## 4. Database

- Nama database: `jacos_db` (via Laragon MySQL, lihat `Architecture.md` §2)
- Sebelum bikin modul baru atau ubah skema → wajib pakai skill `jacos-db-explorer`
  untuk cek tabel/relasi/konvensi kolom yang sudah ada
- Eksplorasi read-only via Laragon lokal (`127.0.0.1:3306`, `jacos_db`) —
  detail di `pi-skills/jacos-db-explorer/SKILL.md`

## 5. Menambah Modul Baru

1. Inspeksi tabel terkait via `jacos-db-explorer`.
2. Buat folder `modules/[module_name]/` dengan subfolder `controllers/`, `models/`, `views/`.
3. Daftarkan di `application/config/routes.php` kalau perlu custom route.
4. Tambahkan ke menu via `modules/menu/`.
5. Ikuti konvensi suffix jenjang (§1) kalau modul memang butuh varian per jenjang.
