# Panduan Admin — Modul Karier (CiCOOL)

Dokumen untuk admin JACOS yang memakai menu **Karier** di panel administrator (CiCOOL). Merujuk SQL `database/migrations/004_career_admin_menu.sql` (di repo frontend career-jacos).

## Akses

- Menu **Karier** muncul setelah SQL 004 dijalankan (idempoten; jalankan sekali per lingkungan).
- Perm per fungsi: `menu_karier`, `career_template_view/_add/_update/_delete`, `career_lowongan_*` (view/add/update/delete), `career_pelamar_view/_add/_update/_delete`, `career_audit_view`. Diberikan ke grup **Admin**.
- Semua aksi mutasi POST + token CSRF; token hilang/salah = HTTP 403 JSON `{"success":false,...}`.
- Aksi GET tidak pernah mengubah data — jangan pernah mencoba hapus via URL langsung.

## Formulir Mutasi (Master Persyaratan → Unggah Dokumen)

1. Buka **Karier → Formulir Mutasi**. Tombol **Tambah**: nama (≤200), deskripsi (≤10000), tipe `document`, format (pdf/jpg/jpeg/png/doc/docx), ukuran maks 1..5120 KB. Isi yg aktif = template yang bisa dipilih saat buat lowongan.
2. **Ubah**: mengubah nama/deskripsi/formats/kb bebas. Tipe & input_type tidak bisa diubah bila template dipakai lowongan; nama/deskripsi/formats/kb tetap bisa.
3. **Hapus**: hanya template yang TIDAK PERNAH dipakai lowongan; yang terpakai → nonaktifkan saja (tombol Aktif/Nonaktif). Tombol hapus memakai konfirmasi peramban → POST AJAX.

## Lowongan Kerja

1. **Tambah**: judul (≤200), deskripsi (≤10000), tanggal buka/tutup (`YYYY-MM-DD`; buka ≤ tutup). Setelah tersimpan, tambah **persyaratan** dari Master Persyaratan (pilih template aktif) atau ad-hoc (text/number; `select` belum didukung — pesan "belum didukung"). Ubah urutan pakai tombol ↑/↓.
2. Tambah persyaratan berlabel **Wajib** pada lowongan yang sudah punya lamaran akan meminta konfirmasi eksplisit — konfirmasi hanya bila Anda sadar kandidat yang berlamar harus melengkapi persyaratan tambahan.
3. Persyaratan pada lowongan yang sudah punya lamaran terkunci sebagian: `input_type`/`requirement_type` tidak bisa diubah (nama/formats/kb masih bisa). Persyaratan yang sudah diisi kandidat tidak bisa dihapus.
4. **Terbitkan/Tutup**: tombol terpisah (POST). Terbit butuh ≥1 persyaratan + tanggal sah. Rentang: `draft` (belum terbit / belum mulai), `Terbuka` (berlangsung), `Tutup` (lewat tanggal tutup). Lowongan berlamaran tidak bisa dihapus.
5. Tombol **Hapus** di daftar = konfirmasi → POST AJAX → JSON; gagal D12 pesan jelas.

## Pelamar Karier

1. **Daftar**: filter lowongan/status/tahap + pencarian nama/email (aman tanda kutip); pagination 20/halaman, draft tak tenggelam (urut terbaru).
2. **Detail**: status lamaran (label), snapshot profil+item SAAT SUBMIT (bukan profil terkini), daftar persyaratan + versi dokumen TERBARU per persyaratan, 5 tahap + jadwal terbit, timeline notifikasi, 30 catatan audit terakhir. Skor psikologi/catatan internal TIDAK ditampilkan (data sensitif).
3. **Unduh dokumen**: tombol pranala per dokumen (hanya dokumen milik lamaran yang sedang dibuka). File keluar `Content-Type` sesuai mime sah, `attachment`, aman nosniff. Setiap unduh admin tercatat di audit (`unduh_dokumen_admin`).

## Audit

- Daftar read-only + filter jenis aksi/objek/aktor/tanggal (pakai tanggal `YYYY-MM-DD` yang sah; filter kosong = semua).
- Entri memuat aktor (id + tipe), objek, detail JSON (termasuk `before`/`after` utk edit). Kolom `detail_json` aman tetap valid (substitusi UTF-8).

## Catatan keamanan & batasan

- Tipe persyaratan `select` belum didukung (opsi pilihan belum ada di skema) — tolak eksplisit.
- Token CSRF per-sesi (belum dirotasi di login); keputusan rotasi menunggu keputusan framework.
- Pranala balik setelah aksi memakai helper framework `redirect_back` (HTTP_REFERER) — belum diubah (butir tunda #20).
- Tanggal audit filter belum tervalidasi penuh; penyeragaman `object_type` audit lama menunggu keputusan (butir #22 — lihat `PHASE6A_REPORT.md`).
