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

// --- contoh GEL 1 (konten bersama) — tempel saat tiba waktunya: ---
// $config['translator_enabled_endpoints'] = array(
//     'siswa/fasilitas/POST',
//     'siswa/agenda/POST',
//     'root/visi_misi/GET',
//     'root/sejarah_sekolah/GET',
//     'root/informasi_psb/GET',
//     'root/ekstrakurikuler/POST',
//     'root/kalender_akademik/POST',
// );

// --- contoh GEL 2 (label & master) — tambahkan setelah gel 1 stabil: ---
// 'siswa/get_presensi/POST', 'siswa/kategori_agenda/GET', 'root/jenis_prestasi/GET',
// 'root/jenis_lomba/GET', 'root/keterangan_prestasi/GET', 'root/pekerjaan_ortu/GET',
// 'root/mata_pelajaran/GET', 'root/alasan_tertarik/GET', 'guru/get_jenis_kpi/GET',
// 'guru/get_kategori_catatan/GET'
