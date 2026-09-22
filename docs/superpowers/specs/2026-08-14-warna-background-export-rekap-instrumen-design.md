# Warna Background per Skor Export Excel Rekap Instrumen

## Goal
Memberi warna background pastel pada sel skor instrumen dan kategori profil keseluruhan di export Excel rekap instrumen.

## Scope
- `export_rekap_instrumen()` mengambil warna skor dari `mhcu_band_kategori` melalui JOIN berbasis kode instrumen, dimensi, dan rentang skor.
- Warna disimpan sebagai field terpisah untuk WHO-5, PHQ-9, GAD-7, CBI, dan Psikososial.
- Kategori profil mengambil `mhcu_profil_kategori.warna`.
- `Mhcu_excel::generate_rekap_instrumen_export()` menerapkan fill pastel pada lima sel skor dan sel Kategori.
- Instrumen 6 tidak diberi warna.

## Data Flow
1. Query skor bulk JOIN `mhcu_instrument` dan `mhcu_band_kategori`.
2. Controller memetakan `warna` ke field `{skor}_warna`.
3. Query hasil individu JOIN `mhcu_profil_kategori`, membawa label dan `kategori_warna`.
4. Library PHPExcel menulis data, lalu menerapkan fill warna setelah alternating-row fill.

## Palette
Nama warna database dipetakan ke pastel:
- `hijau`: `C6EFCE`
- `kuning`: `FFEB9C`
- `orange`: `FCE4D6`
- `merah`: `FFC7CE`

Warna tidak dikenali atau kosong tidak mengubah fill cell.

## Compatibility
Project memakai PHPExcel lama (`PHPExcel.php`, `PHPExcel_IOFactory.php`). Implementasi memakai API `PHPExcel_Style_Fill::FILL_SOLID` dan PHP 5.2-compatible syntax pada kode baru.

## Verification
- PHP lint controller dan library memakai `C:/xampp/php/php.exe -l`.
- Review diff memastikan query, field map, dan fill hanya menyentuh scope fitur.
- Export live dengan seed Budi/Sari/Nasrudin diverifikasi bila environment DB/session tersedia.
