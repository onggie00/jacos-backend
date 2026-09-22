# MODULE_MAP — `modules/program_anggaran/`

Modul CRUD generator CiCOOL standar untuk tabel `program_anggaran`. Mengelola program anggaran per tahun ajaran & jenjang (FT/SD/SMP/SMA).

> **Tipe:** Backend admin (HMVC module, namespace `administrator/program_anggaran`)
> **Auth:** Aauth permission per aksi (`program_anggaran_list/add/update/delete/view/export`)
> **Tabel DB:** `program_anggaran`
> **Kolom:** `id, nomor_program, nama_program, tahun_ajaran, jenis_kegiatan, nominal_okr, jenjang, is_active`

---

## Struktur File

| Path | Fungsi singkat |
|---|---|
| `controllers/backend/Program_anggaran.php` | Controller utama (class `Program_anggaran extends Admin`). Handler CRUD + aksi tambahan (active/inactive, import Excel, export XLS/PDF, single PDF). |
| `models/Model_program_anggaran.php` | Model CiCOOL. Extend `MY_Model`. CRUD standar + `count_all`, `get`, `join_avaiable`, `filter_avaiable`. Tidak ada relasi join (single-table). |
| `language/english/web_lang.php` | Label bahasa Inggris (cclang). |
| `views/backend/standart/administrator/program_anggaran/program_anggaran_list.php` | Halaman list + filter + tombol Add/Export/Import. Tabel 8 kolom (id, nomor, nama, thn ajaran, jenis, nominal, jenjang, is_active) + Action. Shortcut keyboard Ctrl+a/f/x/b. |
| `views/backend/standart/administrator/program_anggaran/program_anggaran_add.php` | Form tambah. Validasi client + server. Tombol Save (stay/redirect). |
| `views/backend/standart/administrator/program_anggaran/program_anggaran_update.php` | Form edit (struktur sama dengan add, dengan field terisi). |
| `views/backend/standart/administrator/program_anggaran/program_anggaran_view.php` | Halaman detail read-only. |

---

## Controller — Method Map

| Method | Route | Hak akses | Deskripsi |
|---|---|---|---|
| `index($offset)` | `administrator/program_anggaran/index` | `program_anggaran_list` | List + pagination + search by `q`+`f`. Render `program_anggaran_list`. |
| `add()` | `administrator/program_anggaran/add` | `program_anggaran_add` | Render form add. |
| `add_save()` | `administrator/program_anggaran/add_save` (POST) | `program_anggaran_add` | Validasi 7 field, insert via `model->store()`. Respons JSON, opsi save_type=stay/redirect. |
| `edit($id)` | `administrator/program_anggaran/edit/{id}` | `program_anggaran_update` | Render form edit dengan data existing. |
| `edit_save($id)` | POST ke `edit_save/{id}` | `program_anggaran_update` | Validasi + update via `model->change()`. Respons JSON. |
| `delete($id)` | `administrator/program_anggaran/delete` (GET `?id[]=…`) | `program_anggaran_delete` | Bulk atau single delete. Private helper `_remove($id)`. Redirect back dengan flash message. |
| `view($id)` | `administrator/program_anggaran/view/{id}` | `program_anggaran_view` | Render detail dengan join + filter. |
| `export()` | `administrator/program_anggaran/export` | `program_anggaran_export` | Export XLS semua data via `model->export()`. |
| `export_pdf()` | `administrator/program_anggaran/export_pdf` | `program_anggaran_export` | Export PDF semua data via `model->pdf()`. |
| `single_pdf($id)` | `administrator/program_anggaran/single_pdf/{id}` | `program_anggaran_export` | Render 1 baris jadi PDF via library `HtmlPdf`. |
| `not_active()` | GET `?id[]=…` | (tidak ada `is_allowed`) | Bulk set `is_active=0` via `mymodel->update()`. |
| `active()` | GET `?id[]=…` | (tidak ada `is_allowed`) | Bulk set `is_active=1`. |
| `import()` | POST (upload file) | (tidak ada `is_allowed`) | Import dari Excel via library `excel` + `PHPExcel_IOFactory`. Kolom dibaca urut: nomor, nama, tahun_ajaran, jenis_kegiatan, nominal_okr, jenjang. Rollback jika ada cell kosong. Upsert by `nomor_program`. |

---

## Model — Method Map

| Method | Return | Digunakan oleh |
|---|---|---|
| `count_all($q, $field)` | int | `index()` (pagination total) |
| `get($q, $field, $limit, $offset, $select_field)` | array of rows | `index()` (list data) |
| `join_avaiable()` | `$this` (chainable) | Dipanggil di `count_all`, `get`, `view`. Saat ini tidak ada join, hanya select `*`. |
| `filter_avaiable()` | `$this` (chainable) | Hook untuk filter role-based. Saat ini kosong (selalu return $this). |
| `store($data)` (warisan MY_Model) | insert_id | `add_save()` |
| `change($id, $data)` (warisan MY_Model) | bool | `edit_save()` |
| `find($id)` (warisan MY_Model) | row | `edit()`, `view()`, `_remove()` |
| `remove($id)` (warisan MY_Model) | bool | `_remove()` |
| `export()` (warisan) | file download | `export()` |
| `pdf()` (warisan) | file download | `export_pdf()` |

**Pencarian:** `q` di-LIKE ke semua kolom `field_search` (7 kolom), atau ke 1 kolom tertentu jika `f` diberikan.

---

## Catatan Penting (Bug / Quirks)

1. **`active()` & `not_active()` tanpa `is_allowed`** — method ini tidak diproteksi permission. Siapapun yang tahu URL bisa panggil. (lihat baris akhir controller.)
2. **`import()` tanpa `is_allowed`** — sama, import Excel bisa dipanggil siapapun. Upload juga tidak ada validasi role.
3. **`import()` raw SQL di where clause** — `$cek_nomor = $this->mymodel->withquery("... where nomor_program = '".$data_pendaftaran["nomor_program"]."'","row");` — rentan SQL injection via cell Excel (tidak di-escape).
4. **`single_pdf()` variabel tidak konsisten** — `$this->pdf` di-set sebagai property, tapi method ini juga menulis `$this->pdf->pdf->...`. Pola tidak lazim.
5. **`import()` tidak set `is_active`** — kolom `is_active` selalu NULL untuk data hasil import (tidak ada default).
6. **`single_pdf()` query tidak terpakai** — `$result = $this->db->get($table);` hasilnya dibuang, hanya `$data = $this->model_program_anggaran->find($id);` yang dipakai untuk fields.

---

## Riwayat Perubahan (git)

```
132dd9b7  Sistem prestasi siswa update kurasi pusprenas, Chart_presensi apps. ... (2025-10 area)
83606712  ujian pendaftaran, callback ft, new format kartu peserta, export_pdf_siswa, ...
380e9530  Update 10 September 2025, PSB APIWEB fix, Penilaian kinerja update, ...
5c267b1e  Labschool Cibubur 2025 Started
```

(Dibuat otomatis oleh agent pada 2026-07-16 saat pertama kali membaca modul ini.)
