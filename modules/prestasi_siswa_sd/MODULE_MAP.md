# Modules: prestasi_siswa_sd — Map

> Per 2026-07-14. Modul CRUD untuk tabel `prestasi_siswa_sd` (data prestasi siswa jenjang SD). REDESIGN UI (match acara) + MULTI-FILTER SELESAI — lihat TASKS.md untuk histori commit.

## Schema tabel `prestasi_siswa_sd`
- PK: `id_prestasi`
- Field inti: `nama_prestasi`, `tgl_raih` (date), `juara`, `konten`, `judul`
- FK: `id_siswa` (→ siswa_sd_aktif), `jenis_prestasi_id` (→ jenis_prestasi), `id_prestasi_bidang` (→ prestasi_siswa_bidang)
- File: `file_prestasi` (sertifikat), `foto_prestasi` (dokumentasi)
- Status: `is_approved` (0=pending, 1=disetujui, 2=ditolak — note: nilai 2 di-set lewat bulk action `ditolak` tapi kolom kemungkinan besar 0/1)
- Boolean: `kurasi_pusprenas` (YA/TIDAK), `link_pusprenas`
- Audit: `tanggal_posting`

## controllers/backend/Prestasi_siswa_sd.php
- `index($offset)` — list + pagination. Pakai filter array (q, f, id_siswa, jenis_prestasi_id, id_prestasi_bidang, is_approved, tgl_raih_from, tgl_raih_to, kurasi_pusprenas). Pagination dengan `reuse_query_string` agar semua filter terbawa. Load dropdown reference data via `_load_dropdown()`.
- `_build_where()` (private) — bangun array filter dari query string GET (validasi tipe data + format tanggal).
- `_load_dropdown($table, $id_col, $label_col)` (private) — ambil data dropdown via `mymodel->getallsort()`, return associative array id=>label (skip row kosong).
- `add()` / `add_save()` — form tambah baru + validasi form + upload file (rename dari tmp folder)
- `edit($id)` / `edit_save($id)` — view & update dengan validasi + upload file replace
- `delete($id)` / `_remove($id)` — hapus single + bulk (multi ID via GET id[]), auto hapus file fisik
- `view($id)` — detail satu record (dengan JOIN via `join_avaiable`)
- `update_status($id)` — bulk update `is_approved` (dipakai oleh bulk action Disetujui/Ditolak) dengan DB transaction. TIDAK ada `is_allowed()` check (anyone can hit).
- `export()` — export Excel XLS. Refactor: ambil data via `model_prestasi_siswa_sd->get($filters, 0, 0, array(), true)` (exclude_mutasi=TRUE sesuai behavior export lama). `field_map` menerjemahkan `nama_lengkap`→`siswa_sd_aktif_nama_lengkap`, `kelas`→`kelas_sd_label`.
- `export_pdf()` — export PDF seluruh data (model method `pdf()`)
- `single_pdf($id)` — PDF per-record. Method tetap ada (backward compat) tapi TIDAK dipanggil dari UI (konsisten dengan acara).
- Upload helpers: `upload_file_prestasi_file()`, `delete_file_prestasi_file()`, `get_file_prestasi_file()` (parallel untuk `foto_prestasi`)

## models/Model_prestasi_siswa_sd.php (extends MY_Model)
- Primary key: `id_prestasi`
- Table: `prestasi_siswa_sd`
- `field_search`: `array('nama_prestasi', 'tgl_raih', 'juara', 'file_prestasi', 'foto_prestasi', 'id_siswa', 'jenis_prestasi_id', 'konten', 'tanggal_posting', 'is_approved')` (legacy, kept for backward compat)
- JOIN LEFT: `siswa_sd_aktif` via `siswa_sd_aktif.id_siswa_sd_aktif = prestasi_siswa_sd.id_siswa` (PK siswa_sd_aktif = `id_siswa_sd_aktif`, bukan `id_siswa`!), `kelas_sd` via `kelas_sd.id_kelas_sd = siswa_sd_aktif.id_kelas`, `jenis_prestasi` via `jenis_prestasi.id_jenis_prestasi = prestasi_siswa_sd.jenis_prestasi_id`
- SELECT: `prestasi_siswa_sd.*, siswa_sd_aktif.nama_lengkap as siswa_sd_aktif_nama_lengkap, jenis_prestasi.jenis_prestasi, kelas_sd.label as kelas_sd_label`
- Methods:
  - `count_all($filters = array())` — COUNT(*) dengan `apply_filters()`
  - `get($filters = array(), $limit = 0, $offset = 0, $select_field = array(), $exclude_mutasi = false)` — ambil data + `apply_filters()` + optional exclude `kelas_sd.label NOT LIKE '%mutasi%'` (dipakai export)
  - `apply_filters($filters = array())` (private) — bangun WHERE clause: keyword search (q+f), FK filters, status approval, date range, kurasi pusprenas. AND semantics.
  - `join_avaiable()` — JOIN + base SELECT (dipanggil get/count_all/view)
  - `filter_avaiable()` — no-op (kept for backward compat)

## views/backend/standart/administrator/prestasi_siswa_sd/

### prestasi_siswa_sd_list.php (REDESIGNED 2026-07-14)
- Container: `box box-warning` flat (match acara) — tanpa nested widget-user-2
- Box-header: judul `fa fa-trophy` + `<span class="label bg-yellow">N Data</span>` + tombol Tambah & Export XLS di `pull-right`
- CSS `.btn-action` (view/edit/delete) — disalin dari acara_list.php
- Search row: input `q` + tombol Cari + reset, dropdown field selector (5 opsi: nama_prestasi, nama_lengkap, kelas, juara, konten), "Hasil pencarian" indicator
- Filter Tambahan Box (collapsible AdminLTE `box collapsed-box`):
  - Default collapsed; expanded otomatis jika ada filter aktif
  - 6 filter control: Siswa (chosen), Jenis Prestasi (chosen), Bidang Prestasi (chosen), Status approval, Tgl Raih range (datepicker dd/mm/yyyy), Kurasi Pusprenas
  - Tombol Terapkan Filter + Reset Semua
- Data Table: kolom Checkbox, Nama Prestasi (+ judul sub-text), Tgl Raih, Siswa (+ status label), Kelas, Jenis, Juara, Foto, Aksi
- Action buttons per row: Detail (biru), Edit (oranye), Hapus (merah) — TIDAK ada PDF per-row (konsisten dengan acara)
- Bulk action: Disetujui, Ditolak, Delete (tanpa permission check sesuai method update_status existing)
- Pagination: `paging_simple_numbers` dengan `reuse_query_string` (semua filter terbawa)
- Keyboard shortcuts: Ctrl+a (add), Ctrl+f (search focus), Ctrl+x (reset)
- JS: Bootstrap datepicker init + format conversion dd/mm/yyyy ↔ Y-m-d di submit; swal untuk delete & bulk confirmation; iCheck untuk checkbox

### prestasi_siswa_sd_add.php — form tambah dengan validasi + upload (TIDAK diubah)
### prestasi_siswa_sd_update.php — form update dengan replace file (TIDAK diubah)
### prestasi_siswa_sd_view.php — detail view dengan JOIN (TIDAK diubah)

## language/english/web_lang.php — 17 string label filter ditambah (filter_tambahan, semua, disetujui, belum_disetujui, ditolak, ya, tidak, tgl_raih_dari, tgl_raih_hingga, terapkan_filter, reset_semua, field_*)
## language/indonesian/web_lang.php — tidak ada (Indonesian strings disimpan di english file sesuai konvensi project)

## Multi-filter (implemented 2026-07-14)

Filter aktif (bisa dikombinasikan, AND semantics):

1. **Search keyword** (`q`) + **field selector** (`f`) — 5 value: `nama_prestasi`, `nama_lengkap`, `kelas`, `juara`, `konten`
2. **FK Siswa** (`id_siswa`) — dropdown `siswa_sd_aktif` (label: `nama_lengkap`)
3. **Jenis Prestasi** (`jenis_prestasi_id`) — dropdown `jenis_prestasi`
4. **Bidang Prestasi** (`id_prestasi_bidang`) — dropdown `prestasi_siswa_bidang`
5. **Status approval** (`is_approved`) — select: Semua / Disetujui (1) / Belum Disetujui (0) / Ditolak (2)
6. **Tanggal raih range** (`tgl_raih_from` + `tgl_raih_to`) — datepicker dd/mm/yyyy, controller validasi format Y-m-d
7. **Kurasi Pusprenas** (`kurasi_pusprenas`) — select: Semua / YA / TIDAK

URL params: `?q=&f=&id_siswa=&jenis_prestasi_id=&id_prestasi_bidang=&is_approved=&tgl_raih_from=&tgl_raih_to=&kurasi_pusprenas=`

Backend: `controller->_build_where()` → array filter → `model->get($filters)` + `model->count_all($filters)` via private `apply_filters()`. Pagination `reuse_query_string=true`. Export XLS pakai filter yang sama (dengan `exclude_mutasi=true` sesuai behavior export lama).

## Catatan redesign (commit 2026-07-14)
- View list di-redesign match `acara_list.php` (box-warning flat + `.btn-action` CSS, tanpa widget-user-2)
- 6 filter tambahan di panel collapsible "Filter Tambahan" (default collapsed, expanded otomatis saat ada filter aktif)
- Keyword + field selector dipertahankan dengan 5 value (sebelumnya hanya 2)
- Tombol PDF per-row dihapus dari UI (konsisten dengan acara) — method `single_pdf()` tetap ada (backward compat)
- Tombol Export XLS dipindah ke box-header `pull-right`, pakai filter yang sama dengan index
- Pagination `reuse_query_string=true` agar semua filter terbawa
- Model direfactor: `get()` & `count_all()` terima array filter (sebelumnya hanya `($q, $field)`), private `apply_filters()` DRY WHERE clause builder
- Controller tambah `_build_where()` (validasi tipe + format tanggal) + `_load_dropdown()` (dropdown reference data)
- Bahasa: 17 string label filter ditambah di `language/english/web_lang.php` (project menyimpan Indonesian values di file english)
