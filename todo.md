# TODO - Jadwal Pelajaran SMP Labschool

## Status: Implementasi Flow Generate Selesai ✅

---

## Alur Kerja Awal (5 Langkah)

```
1. Generate Draft → isi jam_target manual per guru per kelas (46 guru × 21 kelas = 966 input)
2. Set Final → ubah status draft ke final (extra click)
3. Preview Fase 1 → lihat urutan constraint (opsional)
4. Generate Fase 2 → generate jadwal ke slot waktu (extra click)
5. Lihat Hasil → buka jadwal_pelajaran_smp
```

---

## Optimasi yang Diimplementasikan

### Flow Baru (3 Langkah) ✅

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. IMPORT EXCEL                                                 │
│    /administrator/mapel_alokasi_smp/import_excel                │
│    Upload file → alokasi otomatis terisi (status FINAL)         │
│                                                                 │
│ 2. QUICK GENERATE                                               │
│    /administrator/mapel_alokasi_smp/quick_generate_preview      │
│    Preview data → klik generate → Fase 1 + Fase 2 sekaligus    │
│                                                                 │
│ 3. LIHAT HASIL                                                  │
│    Redirect ke /administrator/jadwal_pelajaran_smp              │
└─────────────────────────────────────────────────────────────────┘
```

### Perbandingan

| Aspek | Sebelum | Sesudah |
|-------|---------|---------|
| Jumlah langkah | 5 | 3 |
| Input manual | 966 field | 0 (import Excel) |
| Extra click | Set Final + Preview | Tidak ada |
| Waktu | ~15-30 menit | ~2-5 menit |

---

## Module yang Dibuat

### 1. mapel_alokasi_smp
**Fungsi:** Manage alokasi jam per mapel per kelas

| File | Keterangan |
|------|------------|
| `controllers/backend/Mapel_alokasi_smp.php` | Controller utama |
| `models/Model_mapel_alokasi_smp.php` | Model dengan join |
| `views/.../mapel_alokasi_smp_list.php` | List view |
| `views/.../mapel_alokasi_smp_generate.php` | Generate draft (manual) |
| `views/.../mapel_alokasi_smp_import.php` | **BARU** - Import Excel |
| `views/.../mapel_alokasi_smp_quick_generate.php` | **BARU** - Quick Generate |
| `views/.../mapel_alokasi_smp_fase1.php` | Preview Fase 1 |
| `views/.../mapel_alokasi_smp_fase2.php` | Hasil Fase 2 |
| `views/.../mapel_alokasi_smp_view.php` | Detail view |

**Methods:**
- `index()` - List alokasi
- `generate_draft()` - Form generate manual
- `save_draft()` - Simpan draft (AJAX)
- `set_final()` - Set status FINAL
- `set_draft()` - Set status DRAFT
- `fase1()` - Preview Fase 1
- `fase2()` - Eksekusi Fase 2
- `import_excel()` - **BARU** - Form upload Excel
- `import_excel_process()` - **BARU** - Proses import
- `quick_generate_preview()` - **BARU** - Preview Quick Generate
- `quick_generate_execute()` - **BARU** - Eksekusi Quick Generate
- `reset_jadwal()` - Reset semua jadwal

### 2. mapel_ketetapan_smp
**Fungsi:** Manage ketetapan hari/jam per mapel

| File | Keterangan |
|------|------------|
| `controllers/backend/Mapel_ketetapan_smp.php` | Controller CRUD |
| `models/Model_mapel_ketetapan_smp.php` | Model |
| `views/.../ketetapan_list.php` | List view |
| `views/.../ketetapan_add.php` | Form tambah |
| `views/.../ketetapan_edit.php` | Form edit |

### 3. jadwal_pelajaran_smp
**Fungsi:** View hasil generate jadwal

| File | Keterangan |
|------|------------|
| `controllers/backend/Jadwal_pelajaran_smp.php` | Controller |
| `models/Model_jadwal_pelajaran_smp.php` | Model dengan join lengkap |
| `views/.../jadwal_pelajaran_smp_list.php` | List view |
| `views/.../jadwal_pelajaran_smp_matrix.php` | Matrix view (hari × jam) |
| `views/.../jadwal_pelajaran_smp_ringkasan.php` | Ringkasan guru per hari |
| `views/.../jadwal_pelajaran_smp_view.php` | Detail view |

**Methods:**
- `index()` - List jadwal
- `matrix($id_kelas)` - Matrix per kelas
- `ringkasan_guru()` - Ringkasan guru per hari
- `export()` - Export semua (3 sheet Excel)
- `export_kelas($id)` - Export per kelas

---

## Database

### Tabel yang Dibuat

#### mapel_alokasi_smp
```sql
- id_alokasi_smp (PK)
- id_guru (FK → guru_smp)
- id_kelas_smp (FK → kelas_smp)
- id_mapel (FK → mata_pelajaran_smp)
- jam_target (target jam per minggu)
- jam_terpenuhi (progress)
- total_jam (hard cap)
- status (draft/final)
```

#### mapel_ketetapan_smp
```sql
- id_ketetapan (PK)
- kode_mapel (varchar)
- tipe_ketetapan (enum: hari/jam)
- nilai (comma separated: 1,2,3,4)
- keterangan
```

### Data Ketetapan

| Kode Mapel | Tipe | Nilai | Keterangan |
|------------|------|-------|------------|
| IPA1 | hari | 3,5,6 | Selasa, Kamis, Jumat |
| PP2 | hari | 3,5,6 | Selasa, Kamis, Jumat |
| ING2 | hari | 3 | Selasa |
| NAT | hari | 4,6 | Rabu, Jumat |
| PJOK1/2/3 | hari | 1,2,3,4 | Tidak ada Jumat |
| PJOK1/2/3 | jam | 7,8,9,10 | Jam ke 7-10 |
| PAIBP1/2/3 | hari | 1,2,3,4 | Tidak ada Jumat |

### Data yang Diupdate

#### guru_smp
- `kode_mapel` diisi dari Excel (46 guru)

#### mata_pelajaran_smp
- `kode_mapel` distandarisasi (hilangkan spasi)
- 4 kode baru ditambahkan: BIN6, ING4, BK4, IPS5

---

## Menu & Permissions

| Menu | URL | Parent |
|------|-----|--------|
| Alokasi Mapel | `/administrator/mapel_alokasi_smp` | SMP |
| Ketetapan Mapel | `/administrator/mapel_ketetapan_smp` | SMP |
| Jadwal Pelajaran | `/administrator/jadwal_pelajaran_smp` | SMP |

---

## Bug Fix yang Dilakukan

### SPP Module - id_siswa_aktif Conflict

**Masalah:** `id_siswa_aktif` tidak unik antar jenjang (SD/SMP/SMA bisa sama)

**Fix:** Tambah filter `no_transaksi LIKE '%sd%'` (atau '%SMP%', '%sma%', '%ft%')

| Module | File | Filter |
|--------|------|--------|
| spp_sd | `Spp_sd.php` | `no_transaksi LIKE '%sd%'` |
| spp_smp | `Spp_smp.php` | `no_transaksi LIKE '%SMP%' OR '%smp%'` |
| spp_sma | `Spp_sma.php` | `no_transaksi LIKE '%sma%' OR '%SMA%'` |
| spp_ft | `Spp_ft.php` | `no_transaksi LIKE '%ft%' OR '%FT%'` |

---

## File Referensi

| File | Lokasi | Keterangan |
|------|--------|------------|
| Jadwal Pelajaran V21 | `berkas_sample/jadwal pelajaran V21.xlsm` | Excel referensi utama |
| PANDUAN | `berkas_sample/PANDUAN & Catatan Jadwal Pelajaran.html` | Dokumentasi |

---

## Checklist Selesai

### Tahap A: Audit Data ✅
- [x] Cek guru.kode_mapel
- [x] Cek mata_pelajaran_smp.kode_mapel
- [x] Cek pelajaran_jam
- [x] Cek pelajaran_setting_waktu
- [x] Mapping guru Excel → database

### Tahap B: UI Generate Draft ✅
- [x] Generate draft (manual input)
- [x] Set Final / Set Draft
- [x] Collapse per guru
- [x] Modal panduan
- [x] Error handling dengan highlight

### Tahap C: Fase 1 (Alokasi) ✅
- [x] Preview alokasi FINAL
- [x] Sort by constraint (guru sisa jam paling sedikit duluan)
- [x] Expand slot (jam_target → N baris)
- [x] Tampilkan ketetapan

### Tahap D: Fase 2 (Penempatan Waktu) ✅
- [x] Generate jadwal ke slot waktu
- [x] Constraint: tidak bentrok kelas
- [x] Constraint: tidak bentrok guru (lintas kelas)
- [x] Constraint: max 3 jam/hari per (kelas, mapel)
- [x] Constraint: ketetapan hari/jam
- [x] Reset jadwal

### Tahap E: Export & Import ✅
- [x] Export Excel (3 sheet: matrix, list, ringkasan guru)
- [x] Export per kelas
- [x] Import Excel alokasi dari Lembar2
- [x] Quick Generate (preview + execute)

### Fitur Tambahan ✅
- [x] Ringkasan guru per hari
- [x] Matrix view per kelas
- [x] Ketetapan mapel (CRUD)
- [x] Tombol Import Excel di list view
- [x] Tombol Quick Generate di list view

---

## Catatan Teknis

### Mapping Kolom Excel (Lembar2)

| Kolom | Isi | Mapping DB |
|-------|-----|------------|
| A | NO | - |
| B | NAMA GURU | guru_smp.nama_lengkap (match sebelum koma) |
| H | KODE (BIN1-7A) | Extract kode_mapel (sebelum "-") |
| J-Q | Jam kelas 7A-7H | mapel_alokasi_smp.jam_target |
| S-Y | Jam kelas 8A-8G | mapel_alokasi_smp.jam_target |
| AA-AG | Jam kelas 9A-9G | mapel_alokasi_smp.jam_target |

### Constraint Generate

1. Slot harus kategori MENGAJAR
2. Kelas tidak bentrok di slot sama
3. Guru tidak bentrok di slot sama (lintas kelas)
4. Max 3 jam per hari untuk (kelas, mapel)
5. Ketetapan hari dari mapel_ketetapan_smp
6. Ketetapan jam dari mapel_ketetapan_smp

---

*Last updated: Juli 2026*
*Project: LabSchool/LabScib Management System*
*Framework: CodeIgniter 3 (HMVC) with CiCOOL CMS*
