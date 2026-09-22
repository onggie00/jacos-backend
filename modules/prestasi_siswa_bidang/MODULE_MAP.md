# Modules: prestasi_siswa_bidang — Map

> Per 2026-07-13. Modul CRUD sederhana untuk tabel `prestasi_siswa_bidang` (fields: nama_bidang, created_at). REDESIGN selesai — tampilan + search/filter sudah serupa/senada dengan modul `acara`. Lihat HANDOFF.md untuk histori + commits.

## controllers/backend/Prestasi_siswa_bidang.php
- `index($offset)` — list + pagination. Filter `q` (search) + `f` (field selector). Pagination dengan `reuse_query_string = TRUE`
- `add()` / `add_save()` — form tambah baru
- `edit($id)` / `edit_save($id)` — view & update
- `delete($id)` — hapus multi
- `view($id)` — detail satu record
- `export()` — export Excel via model
- `export_pdf()` — export PDF via model
- `single_pdf($id)` — single PDF per record (tidak ditampilkan di list view per row, hanya via menu export global)

## models/Model_prestasi_siswa_bidang.php (extends MY_Model)
- Primary key: `id_prestasi_bidang`
- Table: `prestasi_siswa_bidang`
- Search fields: `nama_bidang`, `created_at`
- JOIN: none (tabel sederhana)
- Methods: standard MY_Model CRUD (get, count_all, store, change, remove, find, join_avaiable, filter_avaiable)

## views/backend/standart/administrator/prestasi_siswa_bidang/

### prestasi_siswa_bidang_list.php (v2 redesign)
- Container: `box box-warning` (clean, modern — sama dengan modul acara)
- Box header: `with-border` + `box-title` dengan icon + count label + `box-tools pull-right` untuk tombol Tambah
- Tombol "Tambah Data" di box-tools pull-right dengan id `btn_add_new` + shortcut Ctrl+a
- CSS `.btn-action` untuk styling tombol action (border + hover effect): view/edit/delete
- Filter section: search input + reset button inline (muncul hanya jika ada q), field selector (Semua Kolom / Nama Bidang / Created At), "Hasil pencarian" indicator di kanan
- Pagination: dibungkus dengan div `dataTables_paginate paging_simple_numbers`, `reuse_query_string = TRUE` agar q & f tidak hilang saat pindah halaman
- Action buttons per row: pakai class `btn-action btn-action-view`/`edit`/`delete` (konsisten dengan acara)
- Keyboard shortcuts: Ctrl+a (add), Ctrl+f (search focus), Ctrl+x (reset)
- Bottom JS: remove-data confirmation, bulk action, check_all (konsisten dengan acara)

### prestasi_siswa_bidang_add.php — form tambah baru
### prestasi_siswa_bidang_update.php — form update
### prestasi_siswa_bidang_view.php — detail view

## language/english/web_lang.php — translations
