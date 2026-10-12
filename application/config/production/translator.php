<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * config/production/translator.php — TEMPLATE GO-LIVE (fase 7).
 * CI3 memuat file ini SETELAH config dasar (hanya key yang berbeda).
 *
 * SAAT INI: mati total (aman). Aktifkan per gelombang dengan mengisi
 * translator_enabled_endpoints (kunci = "<role>/<controller>/<HTTP>",
 * daftar kunci siap-tempel per gelombang di
 * Berkas_sample/fase7_checklist_produksi.md).
 *
 * KILL SWITCH: set translator_enabled = FALSE (atau kosongkan endpoints)
 * -> fitur mati SEGERA tanpa deploy ulang. Response kembali ke Indonesia.
 */

$config['translator_enabled'] = FALSE;

$config['translator_enabled_endpoints'] = array();

// --- GEL 1a (konten bersama TEKS) — tempel saat tiba waktunya: ---
// $config['translator_enabled'] = TRUE;
// $config['translator_enabled_endpoints'] = array(
//     'siswa/fasilitas/POST','siswa/agenda/POST','root/sejarah_sekolah/GET',
//     'root/informasi_psb/GET','root/ekstrakurikuler/POST','root/kalender_akademik/POST',
// );

// --- GEL 1b (konten bersama HTML) — setelah 1a stabil: ---
// 'root/visi_misi/GET' + (endpoint gel 1a tetap)

// --- GEL 2 (label & master) — setelah 1b: ---
// 'siswa/get_presensi/POST','siswa/list_ibadah_tipe/GET','siswa/kategori_agenda/GET',
// 'root/jenis_prestasi/GET','root/jenis_lomba/GET','root/keterangan_prestasi/GET',
// 'root/pekerjaan_ortu/GET','root/mata_pelajaran/GET','root/alasan_tertarik/GET',
// 'guru/get_jenis_kpi/GET','guru/get_kategori_catatan/GET',
// 'siswa/get_presensi_ekskul/POST','guru/get_ekskul_guru/POST','guru/get_jadwal_ekskul_guru/POST',
// 'guru/get_riwayat_ekskul_siswa/POST','guru/get_riwayat_ekskul_pelatih/POST',
// 'guru/get_presensi_siswa/POST','guru/get_presensi_ekskul_siswa/POST',
// 'pimpinan/get_jenis_kpi/GET','pimpinan/get_all_kpi_guru/GET','pimpinan/get_presensi_siswa/POST',
// 'guru|pimpinan/presensi_{pjok,pramuka,tadarus}_{detail,riwayat}/GET'

// --- GEL 3 (sisanya non-push): get_anggaran, list_notifikasi, home_notification_detail

// --- GEL 1a juga mengaktifkan (keputusan fase 7c):
// $config['translator_allow_lang_header'] = TRUE;   // X-Lang utk endpoint publik

// --- defer (keputusan fase 7c): TRUE di minggu-minggu awal go-live.
// Konten html/baru admin tampil Indonesia sampai warm-up berikutnya dijalankan.
// Evaluasi setelah 2-3 minggu; otomatisasi (cron) = Fase 9.
// $config['translator_defer_long_to_warmup'] = TRUE;
