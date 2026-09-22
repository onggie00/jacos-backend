# Prompt Templates — Labschool Pi Sessions

## ──────────────────────────────────────────
## 1. MEMULAI SESI BARU (project fresh / awal hari)
## ──────────────────────────────────────────

```
Baca AGENTS.md.
Kemudian jalankan: git status && git log --oneline -5
Laporan singkat (3-5 baris):
- Branch aktif
- File terakhir diubah
- Ada task yang kelihatan belum selesai?
Setelah itu tunggu instruksi.
```

---

## 2. MELANJUTKAN TASK KEMARIN
## ──────────────────────────────────────────

```
Baca AGENTS.md.
Jalankan: git log --oneline -10 && git diff HEAD~1 --stat
Identifikasi: task apa yang sedang dikerjakan terakhir kali?
File mana yang diubah? Apakah ada yang terlihat belum selesai?
Ringkas dalam 5 baris, lalu tunggu instruksi.
```

---

## 3. MEMBUAT FITUR BARU (CRUD)
## ──────────────────────────────────────────

```
Baca AGENTS.md dan skill ci3-php.
Buat modul CRUD untuk [nama entitas] dengan:
- Tabel DB: [nama_tabel] (kolom: id, nama, ...)
- Controller: [NamaController] di application/controllers/
- Model: [NamaModel] di application/models/
- View: application/views/[nama_modul]/index.php
- AJAX untuk simpan, edit, hapus (tanpa reload halaman)
- DataTables untuk tampilan tabel
- Validasi: [sebutkan field wajib]

Mulai dari Model, lalu Controller, lalu View.
Tampilkan satu file sekaligus dan minta konfirmasi sebelum lanjut.
```

---

## 4. DEBUG QUERY SQL / DUPLIKASI DATA
## ──────────────────────────────────────────

```
Baca skill mysql-query.
Masalah: [describe masalah — contoh: query JOIN menghasilkan duplikasi baris]
Tabel yang terlibat: [tabel_a], [tabel_b]
Relasi: [one-to-many / many-to-many]
Query saat ini:
[paste query di sini]

Analisa penyebab dan berikan solusi dengan penjelasan singkat.
```

---

## 5. DEBUG AJAX / JAVASCRIPT
## ──────────────────────────────────────────

```
Baca skill ajax-js.
Masalah: [describe — contoh: response AJAX tidak terbaca, parseerror, dll]
Endpoint: [url controller]
Method: POST/GET
Data yang dikirim: [describe]
Response saat ini: [paste jika ada]
Error di console: [paste]

Cari penyebab dan perbaiki. Cek juga: apakah ada output PHP sebelum json_encode?
```

---

## 6. OPTIMASI QUERY LAMBAT
## ──────────────────────────────────────────

```
Baca skill mysql-query.
Query berikut lambat (>2 detik):
[paste query]

Jalankan EXPLAIN dan analisa:
1. Kolom mana yang perlu INDEX?
2. Apakah ada subquery yang bisa dijadikan JOIN?
3. Apakah LIMIT sudah ada?
Berikan versi query yang dioptimasi.
```

---

## 7. BUAT LAPORAN / EXPORT
## ──────────────────────────────────────────

```
Baca AGENTS.md dan skill mysql-query.
Buat fitur export [Excel/PDF] untuk data [nama modul].
Data yang diexport: [kolom-kolom]
Filter: [bulan, tahun, status, dll]
Library yang boleh dipakai: [PHPExcel / TCPDF / dll — sesuai yang sudah ada di project]

Cek dulu apakah library sudah ada di application/libraries/ sebelum membuat kode.
```

---

## 8. REVIEW KODE SEBELUM COMMIT
## ──────────────────────────────────────────

```
Baca AGENTS.md.
Review file berikut sebelum di-commit:
[paste nama file atau: jalankan git diff]

Cek:
- Ada raw query dengan input user yang tidak di-escape?
- Ada echo/var_dump yang lupa dihapus?
- Response AJAX sudah selalu json_encode + exit?
- Validasi input sudah ada di Controller?
Laporkan temuan, jangan langsung perbaiki kecuali diminta.
```

---

## TIPS PENGGUNAAN

- Selalu mulai sesi dengan prompt #1 atau #2 untuk orientasi cepat
- Gunakan `/caveman` di Pi setelah orientasi untuk hemat token output
- Ganti `[placeholder]` dengan detail spesifik sebelum kirim ke Pi
- Untuk task kompleks: minta Pi buat plan dulu, konfirmasi, baru eksekusi
- Jika Pi salah arah: ketik "stop, baca ulang AGENTS.md bagian [X]"
