# Catatan Perbaikan — Kwitansi Pendaftaran + Pendaftaran JI/BNI + Modul Transaksi

Tanggal: 2026-10-09
Commits: `afcf052`, `2b922b1`, `e851c04`, `e86075d`, `a01f324`, `d51711c`
SQL produksi: `2026XXXX-jacos-invoice-ji-bni.sql` (sudah diimport ke produksi)

## 1. Kwitansi pendaftaran (desain JACOS)
- `application/views/template_kwitansi.php` di-rewrite: A5 landscape (tag `<page format>`),
  header logo `JACOS-VERTIKAL.png` + kontak biru #1071B0 + garis dua warna (#1071B0/#F0D020),
  8 field (Receipt No/Date/Parent/Student/Amount/Amount Words/Level/Payment Method),
  checkbox glyph DejaVu Sans (☐ U+2610 / ☑ U+2611), kotak Payment Description,
  blok Authorized by. Backup lama: `template_kwitansi_old.php`.
- Fix Level: pemetaan jenjang case-insensitive (webhook/admin kirim "SD" uppercase,
  template lama banding lowercase) → SD=Primary, KB=P1/P2 (id_tingkatan 1/2), TK=K1/K2.
- Payment Method: default **Cash** bila kosong/tidak dikenal; opsi: Cash, VA BNI,
  Transfer BNI/BCA, QRIS BNI/BCA.
- Logo: `uploads/image_assets/JACOS-VERTIKAL.png` (60px, rasio 1080x1035) —
  wajib ada di server, kwitansi patah logonya kalau tidak.

## 2. Webhook pelunasan (payment_method_id)
- `Payment_notification.php` + `Payment_notification_ft.php` (branch LI/JI pendaftaran +
  uang pangkal): isi `transaksi.payment_method_id` = id `web_payment_method.nama='VA BNI'`
  (lookup by nama, bukan hardcode) **sebelum** `cetak_kwitansi()`, hanya jika masih kosong
  → push dobel aman, nilai manual tidak tertimpa.
- `payment_method_id` pada 4 handler + 2 `Cek_data` + `Payment_notification2/_spp`:
  kondisi prefix `JI` ditambah di semua parser (`"LI" || "JI"`).
- Row transaksi `LI-` lama tetap terproses (kondisi LI dipertahankan).

## 3. Pendaftaran KB/TK/SD (apiweb/psb) — JI + VA BNI
- no_transaksi: `JI-KB/TK/SD-Ymd-id_siswa` (JI = Jacos Invoice; lama LI = Labschool Invoice).
- VA BNI ala Pendaftaran_ft: key umum (`bni_client_id`/`bni_secret_key`/`bni_prefix`),
  kode_tahun 4 digit dari tahun_ajaran_psb, digit jalur KB=1/TK=2/SD=3, no urut 3 digit,
  retry duplikat s/d 100x, `trans_begin/commit/rollback`, siswa diinsert `PENDING`.
- Config bank kosong → TANPA generate VA: transaksi tetap dibuat, `va_number = NULL`,
  respons sukses (user diarahkan transfer manual di website PSB).
- Cabang BRI + flag `bank_psb_sd` + `create_va_bri()` dihapus.
- Prefix `LI-` → `JI-` di semua pembuat aktif lain (42 literal, 22 file):
  admin modules siswa_sd/kb/tk/smp/sma/ft + briva, apiapp/siswa, apiweb/psb + apiweb/siswa,
  termasuk varian mutasi/PPSBB. `Dashboard_psb.php` ditambah kondisi `"JI-"`.
  TIDAK disentuh: `apiweb_sec_db/*`, backup `OLD_*/NEW_*/*_OLD/.save`.

## 4. Modul admin transaksi
- Form update: dropdown Metode Pembayaran → simpan `payment_method_id` (kosong = NULL).
- List: kolom Metode Bayar, sorting status_transaksi/nama_lengkap/nama_bank
  (whitelist anti-injection di Model), list dipangkas 17→8 kolom, sisanya pindah ke
  modal detail; join `siswa_kb/tk` + `web_payment_method` ditambahkan.
- Tombol cetak kwitansi (status lunas) via method baru `cetak_kwitansi_action()`
  (reuse cetak_kwitansi curl ke apiapp/siswa/export_pdf_kwitansi).
- Peta unit no_peserta disamakan webhook: SD 11→**12**, ditambah KB=51/TK=52.

## 5. Database (lokal sudah dijalankan; produksi via SQL file)
- `CREATE TABLE web_payment_method` (id PK AI, nama, is_show) + seed idempotent:
  Cash, Transfer BNI/BCA, QRIS BNI/BCA, VA BNI.
- `ALTER transaksi ADD payment_method_id INT NULL AFTER nama_bank`.
- Backfill lokal: BNI → VA BNI = 5.134 rows; BRI 1.129 rows dibiarkan NULL
  (kwitansi tampil Cash; tanpa label VA BRI sesuai keputusan user).
- Backfill `web_periode_daftar`: periode SD aktif 2026-10-01..2027-07-31 `gelombang=1`
  (fix gelombang null pendaftaran; id 9 lokal — produksi sesuaikan periode/buat manual).
- Data uji lokal (5 siswa uji + transaksi JI-) dibiarkan, tidak dihapus.

## 6. Verifikasi yang sudah dilakukan (lokal)
- Render kwitansi: SD uppercase → ☑ Primary + ☑ Cash; TK → ☑ K1; VA BNI → ☑; 1 halaman A5.
- Runtime Pendaftaran_sd: config kosong → tagihan tanpa VA sukses; config terisi + API gagal
  → rollback bersih (0 row tertinggal) + error_log_bni; pengaturan_akun dikembalikan.
- php -l PASS semua file; lint PHP 5.2 kode baru.

## 7. Daftar upload ke server
42 file PHP + `uploads/image_assets/JACOS-VERTIKAL.png` — daftar lengkap lihat riwayat
chat sesi ini atau `git show --name-only afcf052 2b922b1 e851c04 e86075d a01f324 d51711c`.
Batch rebranding Labschool→JACOS (~99 file, uncommitted) BUKAN bagian upload ini.
