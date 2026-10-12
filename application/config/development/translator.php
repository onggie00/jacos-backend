<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * config/development/translator.php — override HANYA key yang berbeda
 * dari application/config/translator.php (CI3 merge: main dulu, lalu env).
 * Produksi tidak memuat file ini -> memakai nilai dasar (translator_enabled FALSE).
 */

$config['translator_enabled'] = TRUE; // PILOT lokal (development only)

$config['translator_enabled_endpoints'] = array(
    'siswa/fasilitas/POST',
    'siswa/get_presensi/POST',
    'guru/get_jadwal_ekskul_guru/POST',
    'root/jenis_prestasi/GET',   // tes X-Lang (fase 7b/7c)
    'root/list_notifikasi/POST', // excluded — verifikasi tidak tersentuh
);

// Fase 7b: bahasa cadangan via header X-Lang utk endpoint root/publik (path bersama=TRUE)
$config['translator_allow_lang_header'] = TRUE;
