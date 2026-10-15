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

## Pelamar Karier — alur 6B (verifikasi → jadwal → keputusan)

1. **Verifikasi dokumen/jawaban** (perm `career_pelamar_verify`): di halaman detail pelamar, tiap persyaratan berstatus `pending_review` punya tombol keputusan: **Terverifikasi / Perlu revisi / Ditolak**. Revisi & tolak wajib menulis pesan untuk kandidat (maks 500 karakter) — pesan ini satu-satunya jalur alasan ke kandidat (dikirim sebagai notifikasi in-app `requirement_revision`/`requirement_rejected`). Catatan internal (`review_note`) tidak pernah dikirim kandidat.
2. **Verifikasi hanya bisa** lamaran status `submitted`/`in_process`; persyaratan belum diunggah tidak bisa diverifikasi; persyaratan `document` butuh >=1 dokumen; persyaratan text/number butuh jawaban.
3. **Jadwal seleksi** (perm `career_pelamar_schedule`): per tahap — isi tanggal `Y-m-d H:i`, kanal (maks 200), catatan (maks 500) → **Simpan** (belum rilis). Setelah siap → **Publikasikan**: tahap jadi `scheduled` + kandidat dapat notifikasi `stage_scheduled`. Batal publikasi → kembali `pending` (jadwal boleh diedit ulang); hapus jadwal hanya sebelum publikasi. Tahap dapat dijadwalkan hanya bila seluruh tahap sebelumnya `passed`.
4. **Keputusan tahap** (perm `career_pelamar_decide`): per tahap **Lulus (passed) / Gagal (failed)**. Aturan: hanya satu tahap berjalan (tahap N hanya bisa diproses bila tahap 1..N-1 passed); tahap yang sudah diputuskan terkunci — ubah lewat **Koreksi** (alasan wajib). Koreksi tahap pertama ke gagal/tahap apapun membatalkan keputusan tahap lain + status.
5. **Status akhir** (perm `career_pelamar_decide`): tombol **Terima** hanya aktif setelah 5 tahap lulus → lamaran `accepted` (notifikasi `application_accepted`). Terima ulang pada status accepted → idempoten.
6. **Nilai tahap** (perm `career_pelamar_score_view/_edit`): panel nilai per tahap — skor 0..100 + rubrik/catatan internal; tersimpan di `career_evaluation_scores`; tidak pernah tampil ke kandidat (D13). Perubahan tercatat di audit tanpa isi rubrik (panjang saja).
7. **Koreksi (batal status akhir)**: mengoreksi tahap pada lamaran `accepted` mengembalikan lamaran ke status konsisten (tahap yang dikoreksi menjadi keputusan baru, tahap lain reset pending).

## Kandidat Karier

- Menu anak **Kandidat Karier** (perm `career_candidate_reset_password` untuk aksi reset): daftar kandidat + pencarian nama/email.
- **Reset sandi**: tombol reset → isi sandi baru (min 8 karakter) → POST+CSRF; tersimpan sebagai hash bcrypt. Tidak ada email dikirim (D2 — proses lupa password ditangani admin).

## Catatan keamanan & batasan

- Tipe persyaratan `select` belum didukung (opsi pilihan belum ada di skema) — tolak eksplisit.
- Token CSRF per-sesi (belum dirotasi di login); keputusan rotasi menunggu keputusan framework.
- Pranala balik setelah aksi memakai helper framework `redirect_back` (HTTP_REFERER) — belum diubah (butir tunda #20).
- Tanggal audit filter belum tervalidasi penuh; penyeragaman `object_type` audit lama menunggu keputusan (butir #22 — lihat `PHASE6A_REPORT.md`).
