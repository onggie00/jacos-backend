# MODULE_MAP — `modules/program_anggaran_sma/`

Modul pengajuan & pelaporan anggaran khusus jenjang SMA. Data disimpan di tabel terpisah `program_anggaran_sma`, yang mereferensi `program_anggaran` (tabel master) untuk sumber nominal OKR.

> **Tipe:** Backend admin (HMVC module, namespace `administrator/program_anggaran_sma`)
> **Auth:** Aauth permission per aksi (`program_anggaran_sma_list/add/update/delete/view/export`)
> **Tabel DB:** `program_anggaran_sma`
> **Kunci Asing Logis:** `nomor_program` → `program_anggaran.nomor_program` (LEFT JOIN di model)

---

## Struktur File

| Path | Fungsi singkat |
|---|---|
| `controllers/backend/Program_anggaran_sma.php` | Controller utama. CRUD + workflow pengajuan/laporan + upload 5 jenis file + email notifikasi + cetak kwitansi + perhitungan sisa OKR. |
| `models/Model_program_anggaran_sma.php` | Model CiCOOL. Extend `MY_Model`. CRUD standar + `count_filtered`/`get_filtered` + multi-filter + join ke `program_anggaran` dan tabel status. |
| `language/english/web_lang.php` | Label bahasa Inggris (cclang). |
| `views/backend/standart/administrator/program_anggaran_sma/program_anggaran_sma_list.php` | Halaman list + filter builder + tabel status + modal detail + aksi approval/laporan/cetak kwitansi. |
| `views/backend/standart/administrator/program_anggaran_sma/program_anggaran_sma_add.php` | Form tambah pengajuan. Pilih program dari master `program_anggaran`, hitung sisa OKR, upload proposal. |
| `views/backend/standart/administrator/program_anggaran_sma/program_anggaran_sma_update.php` | Form edit + pelaporan. Upload laporan kegiatan/keuangan, user_keuangan bisa input tanggal pencairan. |
| `views/backend/standart/administrator/program_anggaran_sma/program_anggaran_sma_view.php` | Halaman detail read-only. |

---

## Controller — Method Map

| Method | Route | Hak akses | Deskripsi |
|---|---|---|---|
| `index($offset)` | `administrator/program_anggaran_sma/index` | `program_anggaran_sma_list` | List + pagination + search + multi-filter. Hitung `total_proposal` (status pengajuan/laporan masih berjalan). Set flag `user_keuangan` jika user punya group_id=10. |
| `add()` | `administrator/program_anggaran_sma/add` | `program_anggaran_sma_add` | Render form tambah. |
| `add_save()` | POST | `program_anggaran_sma_add` | Validasi, cek sisa OKR dari `program_anggaran`, tolak jika `nominal_pengajuan > sisa OKR`. Upload file proposal. Status otomatis 1 (berkas belum lengkap) atau 2 (lengkap, kirim email ke anggaran). |
| `edit($id)` | `administrator/program_anggaran_sma/edit/{id}` | `program_anggaran_sma_update` | Render form edit. |
| `edit_save($id)` | POST | `program_anggaran_sma_update` | Update pengajuan/laporan. Cek sisa OKR. Jika user_keuangan set `tanggal_pencairan` → status_pengajuan=6 + cetak kwitansi. Kirim email notifikasi. |
| `delete($id)` | GET `?id[]=…` | `program_anggaran_sma_delete` | Bulk/single delete + hapus file fisik. |
| `view($id)` | `administrator/program_anggaran_sma/view/{id}` | `program_anggaran_sma_view` | Render detail dengan join. |
| `get_program($id)` | AJAX | — | Ambil data `program_anggaran` by `nomor_program` dan hitung sisa OKR untuk dipakai frontend. |
| `get_detail($id)` | AJAX | `program_anggaran_sma_view` | Return JSON detail lengkap untuk modal. |
| `export()` | `administrator/program_anggaran_sma/export` | `program_anggaran_sma_export` | Export XLS. |
| `export_pdf()` | `administrator/program_anggaran_sma/export_pdf` | `program_anggaran_sma_export` | Export PDF. |
| `single_pdf($id)` | `administrator/program_anggaran_sma/single_pdf/{id}` | `program_anggaran_sma_export` | PDF 1 baris. |
| `cetak_kwitansi_program($id, $jenis)` | internal | — | Generate kwitansi PDF saat pencairan. |
| `send_email_file(...)` | internal | — | Kirim email notifikasi ke `anggaran@labschoolcibubur.sch.id`. |
| `upload_file_* / delete_file_* / get_file_*` | AJAX | add/update/delete | Handler upload/delete/get untuk 5 jenis file (proposal pengajuan, proposal keuangan, laporan kegiatan, laporan keuangan, kwitansi). |

---

## Model — Method Map

| Method | Return | Digunakan oleh |
|---|---|---|
| `count_all($q, $field)` | int | Pagination total (legacy). |
| `get($q, $field, $limit, $offset, $select_field)` | array | List data (legacy). |
| `count_filtered($filters, $search)` | int | `index()` pagination total. |
| `get_filtered($filters, $search, $limit, $offset, $select_field)` | array | `index()` list data. |
| `_build_filter_where($filters, $search)` | void | Terapkan multi-filter `ff/fo/fv[]` dan search keyword. |
| `_resolve_column($field)` | string | Mapping field ke kolom SQL (status join ke tabel status). |
| `join_avaiable()` | `$this` | LEFT JOIN `program_anggaran`, `program_anggaran_status_pengajuan`, `program_anggaran_status_laporan`. Select field gabungan. |
| `filter_avaiable()` | `$this` | Hook role-based. Saat ini kosong. |
| `store/change/find/remove` | — | Warisan MY_Model. |

---

## Workflow Status

### Status Pengajuan (`status_pengajuan`)
- `1` — Berkas Belum Lengkap (merah)
- `2` — Berkas Lengkap / Menunggu Approval (kuning)
- `3` — Sedang Diproses (kuning)
- `4` — Disetujui (hijau)
- `5` — Ditolak (merah)
- `6` — Dana Sudah Dicairkan (biru)

### Status Laporan (`status_laporan`)
Mapping warna sama seperti status_pengajuan.

---

## Integrasi & Catatan Penting

1. **Referensi OKR:** Saat add/update, controller membaca `program_anggaran.nominal_okr` berdasarkan `nomor_program` yang dipilih, lalu menghitung total `nominal_pengajuan` existing di `program_anggaran_sma` dengan `nomor_program` & `jenis_kegiatan` yang sama. Pengajuan ditolak jika melebihi sisa OKR.
2. **Email Notifikasi:** Setiap penyimpanan lengkap mengirim email ke `anggaran@labschoolcibubur.sch.id` dengan lampiran file.
3. **Role Keuangan:** User dengan `aauth_user_to_group.group_id = 10` mendapat flag `user_keuangan`. Hanya role ini yang bisa input `tanggal_pencairan` dan otomatis set status_pengajuan=6.
4. **Multi-Filter:** View list mendukung filter builder `ff[]/fo[]/fv[]` dengan operator `contains/equals/starts_with/ends_with/gt/lt`.
5. **File Upload:** 5 jenis file disimpan di `uploads/program_anggaran_sma/`. Saat delete record, file fisik ikut dihapus.
6. **Catatan Keamanan:** Terdapat banyak query SQL mentah dengan konkatenasi string (`withquery`, `getbywhere`) — perlu waspada SQL injection jika input tidak di-escape.

---

## Perbedaan dengan `modules/program_anggaran`

| Aspek | `program_anggaran` | `program_anggaran_sma` |
|---|---|---|
| Tabel | `program_anggaran` | `program_anggaran_sma` |
| Fungsi | Master program & alokasi OKR | Pengajuan pencairan dana OKR |
| Jenjang | Multi-jenjang (SD/SMP/SMA/FT) | Khusus SMA |
| Workflow | Aktif/non-aktif | Status pengajuan + status laporan |
| File upload | Tidak ada | 5 jenis file |
| Email | Tidak | Ya, ke bagian anggaran |
| Join | Single-table | Join ke `program_anggaran` & status tables |

---

*Dibuat otomatis oleh agent pada 2026-07-17 saat pertama kali membaca modul ini.*
