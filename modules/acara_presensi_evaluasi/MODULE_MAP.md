# Modules: acara_presensi_evaluasi — Map

> v2.4 FINAL per 2026-07-13. Page-level chart section dihapus (v2.3), diganti dengan tombol "Grafik" per peserta → modal Bar/Line chart (Y=nilai jawaban numeric, X=no_urut pertanyaan). Lihat HANDOFF.md untuk histori + commits.

## controllers/backend/Acara_presensi_evaluasi.php
- `index($offset)` — list summary 1 row per (npp, peserta) + pagination. Filter: `id_acara` + `q` (search NPP)
- `add()` / `add_save()` — form tambah baru → store ke DB
- `edit($id)` / `edit_save($id)` — view & update data (single evaluasi row)
- `view($id)` — detail satu record (JOIN acara_evaluasi_form)
- `delete($id)` — hapus multi, redirect back
- `export()` — export Excel via model
- `export_pdf()` — export PDF via model
- `single_pdf($id)` — single PDF per record
- `get_jawaban_by_peserta($npp, $peserta)` — v2 AJAX endpoint, return `{info, qa[]}` untuk modal Q&A
- `get_chart_by_peserta($npp, $peserta, $id_acara)` — **v2.4** AJAX endpoint untuk modal grafik, return `{info: {npp, nama, peserta, acara_nama}, chart: [{no_urut, pertanyaan, jawaban}]}`

## models/Model_acara_presensi_evaluasi.php (extends MY_Model)
- Primary key: `id_presensi_evaluasi`
- Table: `acara_presensi_evaluasi`
- Search fields: `id_acara`, `npp`, `id_evaluasi_form`, `jawaban`
- JOIN tersedia:
  - `acara` (LEFT) on `id_acara`
  - `acara_evaluasi_form` (LEFT) on `id_evaluasi_form`
  - `guru_ft` (LEFT) on `npp`
  - `acara_presensi` (LEFT) on composite `id_acara + npp`
- Fields dari JOIN: `acara_nama_acara`, `acara_evaluasi_form_pertanyaan`, `guru_nama_lengkap`, `acara_presensi_peserta`
- Methods:
  - `get_jawaban_by_peserta($npp, $peserta)` — v2 fetch Q&A
  - `count_distinct_peserta($id_acara, $search_npp)` — v2.2 COUNT pagination
  - `get_distinct_peserta($id_acara, $limit, $offset, $search_npp)` — v2.2 list summary
  - `get_numeric_chart_data($id_acara)` — **v2.3 DEPRECATED di v2.4** (page-level chart dihapus)
  - `get_completion_chart_data($id_acara)` — **v2.3 DEPRECATED di v2.4** (page-level chart dihapus)
  - `get_chart_data_by_peserta($npp, $peserta, $id_acara)` — **v2.4** JOIN `acara_evaluasi_form` untuk fetch `no_urut` + `pertanyaan`, filter numeric jawaban via REGEXP, ORDER BY `no_urut` ASC. Return `{id_evaluasi_form, no_urut, pertanyaan, jawaban_num}`
  - `get()` + `count_all()` — v1 retained, tidak dipakai controller index

## views/backend/standart/administrator/acara_presensi_evaluasi/

### acara_presensi_evaluasi_list.php
- Tabel list summary: 1 row per (npp, peserta)
- Kolom: checkbox + NPP + Peserta + Jumlah Q&A + **Aksi (Grafik + Lihat Q&A)**
- Tombol **Grafik** (btn-info, icon `fa-line-chart`) → buka modal grafik per peserta
- Tombol **Lihat Q&A** (btn-warning, icon `fa-comments`) → buka modal Q&A per peserta
- Filter section: search `q` (by NPP) + dropdown `id_acara`
- **Page-level chart section DIHAPUS di v2.4** (tidak ada lagi chart di atas tabel)
- Modal `qAModal`: info NPP + Peserta + tabel Q&A (search filter dalam modal) — unchanged
- **Modal `grafikModal` (v2.4)**: info section (NPP + Peserta) + toggle Bar/Line + canvas `#grafikChart` + empty state
- JS:
  - `lihatQa(npp, nama, peserta)` → AJAX `get_jawaban_by_peserta` → modal Q&A
  - `lihatGrafik(npp, nama, peserta)` → AJAX `get_chart_by_peserta` → modal grafik (Bar default)
  - `renderGrafik(data, type)` → render Bar atau Line chart, Y=nilai jawaban, X=no_urut
  - Toggle Bar/Line handlers

### acara_presensi_evaluasi_add.php — form tambah baru
### acara_presensi_evaluasi_update.php — form update
### acara_presensi_evaluasi_view.php — detail view

## language/english/web_lang.php — translations

## Tech debt / known limitations
- DB read-only user `labschool_ro` ada di config tapi MySQL tolak login — tidak bisa verify schema/sample data via skill DB.
- Bulk action delete: checkbox value = `sample_id` (id_presensi_evaluasi pertama untuk npp tsb), bukan hapus semua evaluasi npp tsb.
- Edge case: npp dengan `acara_presensi.peserta` value beda di acara berbeda = multiple rows untuk npp yg sama (di luar acara filter).
- Model methods `get_numeric_chart_data` + `get_completion_chart_data` deprecated tapi masih ada di model (dead code). Bisa dihapus di iterasi berikut jika tidak akan dipakai ulang.
- Chart filter id_acara: modal grafik kirim `$('#id_acara').val()` dari dropdown filter. Kalau user belum pilih acara, null = semua acara.
