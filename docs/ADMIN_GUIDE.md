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
3. **Jadwal seleksi** (perm `career_pelamar_schedule`): per tahap — isi tanggal `Y-m-d H:i`, kanal (maks 200, tanpa emoji), catatan (maks 500, tanpa emoji) → **Simpan** (belum rilis). Aksi jadwal pertama pada lamaran `submitted` otomatis mengubah lamaran menjadi `in_process` (D16.3). Setelah siap → **Publikasikan**: tahap jadi `scheduled`, kolom `scheduled_at` di baris tahap terisi dari jadwal terpublikasi, kandidat dapat notifikasi `stage_scheduled`. Batal publikasi → tahap kembali `pending` + `scheduled_at` kosong; HANYA tahap berstatus `scheduled` yang berubah — tahap `passed`/`failed` tidak disentuh, dan lamaran `accepted`/`not_accepted` ditolak (409). Hapus jadwal hanya sebelum publikasi dan sebelum tahap diputuskan. Tahap dapat dijadwalkan hanya bila seluruh tahap sebelumnya `passed` (D16.2).
4. **Keputusan tahap** (perm `career_pelamar_decide`): per tahap **Lulus (passed) / Gagal (failed)**. Aturan: hanya satu tahap berjalan (tahap N hanya bisa diproses bila tahap 1..N-1 passed); tahap yang sudah diputuskan terkunci — ubah lewat **Koreksi** (alasan wajib). Koreksi tahap pertama ke gagal/tahap apapun membatalkan keputusan tahap lain + status.
5. **Status akhir** (perm `career_pelamar_decide`): tombol **Terima** hanya aktif setelah 5 tahap lulus → lamaran `accepted` (notifikasi `application_accepted`). Terima ulang pada status `accepted` ditolak (409) — untuk membuka kembali gunakan **Koreksi**.
6. **Nilai tahap** (lihat butuh `career_pelamar_score_view`, ubah butuh `career_pelamar_score_edit`): panel nilai per tahap TAMPIL untuk admin score_view (nilai yang sudah tersimpan dibaca dari `career_evaluation_scores`), form perubahan hanya tampil untuk score_edit — skor 0..100 + rubrik/catatan internal; tidak pernah tampil ke kandidat (D13). Perubahan tercatat di audit tanpa isi rubrik/skor angka (panjang saja).
7. **Koreksi (batal status akhir)**: mengoreksi tahap pada lamaran `accepted` mengembalikan lamaran ke status konsisten (tahap yang dikoreksi menjadi keputusan baru, tahap lain reset pending).

## Kandidat Karier

- Menu anak **Kandidat Karier** (perm `career_candidate_reset_password` untuk aksi reset; perm `career_candidate_list` untuk daftar): daftar kandidat + pencarian nama/email + urut kolom (id/nama/email/telepon/lamaran/login) + infobox jumlah per status.
- **Reset sandi**: tombol reset → sandi baru DIGENERASI SERVER (14 karakter acak tanpa karakter ambigu) → POST+CSRF; tersimpan sebagai hash bcrypt, `token_version` naik (sesi/login lama gugur); sandi tampil sekali di modal (tombol salin) dan TIDAK dikirim email (D2 — proses lupa password ditangani admin). Respons JSON reset memakai `Cache-Control: no-store` + `Pragma: no-cache`.

## Catatan keamanan & batasan

- Tipe persyaratan `select` belum didukung (opsi pilihan belum ada di skema) — tolak eksplisit.
- Token CSRF per-sesi (belum dirotasi di login); keputusan rotasi menunggu keputusan framework.
- Pranala balik setelah aksi memakai helper framework `redirect_back` (HTTP_REFERER) — belum diubah (butir tunda #20).
- Tanggal audit filter belum tervalidasi penuh; penyeragaman `object_type` audit lama menunggu keputusan (butir #22 — lihat `PHASE6A_REPORT.md`).

## Menjadikan pelamar diterima sebagai guru (GURU.1)

- Muncul di detail pelamar hanya bila status lamaran **Diterima (accepted)** dan admin punya perm `career_pelamar_to_guru`.
- Alur dua langkah: tombol **Jadikan Guru** → halaman pratinjau `administrator/career_guru/konversi/{id_application}` (jenjang tujuan SD/KB/TK, emp_code, posisi, mapel untuk SD) → **Simpan** (POST + CSRF).
- Data disalin (bukan dipindah): profil, riwayat pendidikan terbaru, kontak. Berkas lamaran TIDAK disalin (tetap di storage karier, privat).
- Penjagaan: satu lamaran hanya boleh dikonversi sekali (UNIQUE); satu kandidat hanya satu baris link; duplikat guru lintas jenjang (emp_code/email/nik) ditolak; nama + tanggal lahir sama butuh konfirmasi pemeriksaan admin; semua audit tanpa data pribadi sensitif.
- Setelah sukses: lencana **Sudah jadi guru {jenjang} #id** di detail pelamar; data guru baru ditemukan di menu Guru sesuai jenjang (Guru SD/KB/TK).

## Modul Guru KB/TK

- Menu **Guru → Guru KB / Guru TK → Data Guru** (modul `guru_kb`/`guru_tk`, mirror modul Guru SD tanpa mata pelajaran & tanpa slip gaji).
- Employee Code (`emp_code`) wajib unik lintas semua jenjang guru (SD/SMP/SMA/FT/KB/TK) — dicek server.
- Upload foto profil: jpg/jpeg/png/webp maks 2 MB; nama file digenerate server.
- Hapus data guru = soft delete (tanda `deleted_at`); list tidak lagi menampilkan baris yang dihapus.
