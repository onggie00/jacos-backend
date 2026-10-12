<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * translator_sources_helper.php — sumber data per gelombang rollout &
 * ekstraksi string (dipakai Translator_estimate & tool warm-up).
 *
 * Query per endpoint mengikuti whitelist (path tipe teks/html, bersama=true).
 * Gelombang:
 *   1a = path bersama=TRUE tipe TEKS
 *   1b = path bersama=TRUE tipe HTML
 *   2  = label & master data (teks, kardinalitas kecil)
 *   3  = sisanya non-push (dinamis; dilaporkan terpisah)
 */

if (!function_exists('translator_wave_sources')) {

    /**
     * return array(endpoint_key => array(array('sql'=>, 'tipe'=>)))
     * @param string $wave 1a|1b|2|3
     */
    function translator_wave_sources($wave)
    {
        $teks = 'teks'; $html = 'html';
        $w = array();

        // ---- Gel 1a: teks bersama (konten admin) ----
        if ($wave === '1a') {
            $w['siswa/fasilitas/POST'] = array(
                array('sql' => "SELECT nama_fasilitas AS v FROM fasilitas", 'tipe' => $teks));
            $w['siswa/agenda/POST'] = array(
                array('sql' => "SELECT judul AS v FROM agenda", 'tipe' => $teks),
                array('sql' => "SELECT nama_kategori AS v FROM kategori_agenda", 'tipe' => $teks));
            $w['root/sejarah_sekolah/GET'] = array(
                array('sql' => "SELECT judul AS v FROM sejarah_sekolah", 'tipe' => $teks));
            $w['root/informasi_psb/GET'] = array(
                array('sql' => "SELECT judul AS v FROM web_informasi_psb", 'tipe' => $teks));
            $w['root/ekstrakurikuler/POST'] = array(
                array('sql' => "SELECT nama AS v FROM ekskul", 'tipe' => $teks),
                array('sql' => "SELECT judul AS v FROM ekskul", 'tipe' => $teks));
            $w['root/kalender_akademik/POST'] = array(
                array('sql' => "SELECT label AS v FROM kalender_akademik", 'tipe' => $teks),
                array('sql' => "SELECT nama_tipe AS v FROM tipe_kalender", 'tipe' => $teks));
        }

        // ---- Gel 1b: html bersama ----
        if ($wave === '1b') {
            $w['siswa/fasilitas/POST'] = array(
                array('sql' => "SELECT deskripsi AS v FROM fasilitas", 'tipe' => $html));
            $w['siswa/agenda/POST'] = array(
                array('sql' => "SELECT deskripsi AS v FROM agenda", 'tipe' => $html));
            $w['root/visi_misi/GET'] = array(
                array('sql' => "SELECT isi AS v FROM visi_misi", 'tipe' => $html));
            $w['root/sejarah_sekolah/GET'] = array(
                array('sql' => "SELECT konten AS v FROM sejarah_sekolah", 'tipe' => $html));
            $w['root/informasi_psb/GET'] = array(
                array('sql' => "SELECT deskripsi AS v FROM web_informasi_psb", 'tipe' => $html));
            $w['root/ekstrakurikuler/POST'] = array(
                array('sql' => "SELECT konten AS v FROM ekskul", 'tipe' => $html));
        }

        // ---- Gel 2: label & master data ----
        if ($wave === '2') {
            $w['siswa/get_presensi/POST'] = array(
                array('sql' => "SELECT DISTINCT status_absen AS v FROM presensi_sd WHERE status_absen <> ''", 'tipe' => $teks),
                array('sql' => "SELECT DISTINCT hari_absen AS v FROM presensi_sd", 'tipe' => $teks));
            $w['siswa/list_ibadah_tipe/GET'] = array(
                array('sql' => "SELECT ibadah AS v FROM ibadah_setting", 'tipe' => $teks),
                array('sql' => "SELECT keterangan AS v FROM ibadah_setting", 'tipe' => $teks));
            $w['siswa/kategori_agenda/GET'] = array(
                array('sql' => "SELECT nama_kategori AS v FROM kategori_agenda", 'tipe' => $teks));
            $w['root/jenis_prestasi/GET'] = array(
                array('sql' => "SELECT jenis_prestasi AS v FROM jenis_prestasi", 'tipe' => $teks));
            $w['root/jenis_lomba/GET'] = array(
                array('sql' => "SELECT jenis_lomba AS v FROM jenis_lomba", 'tipe' => $teks));
            $w['root/keterangan_prestasi/GET'] = array(
                array('sql' => "SELECT keterangan_prestasi AS v FROM keterangan_prestasi", 'tipe' => $teks));
            $w['root/pekerjaan_ortu/GET'] = array(
                array('sql' => "SELECT nama_pekerjaan AS v FROM pekerjaan_ortu", 'tipe' => $teks));
            $w['root/mata_pelajaran/GET'] = array(
                array('sql' => "SELECT nama AS v FROM mata_pelajaran", 'tipe' => $teks));
            $w['root/alasan_tertarik/GET'] = array(
                array('sql' => "SELECT alasan AS v FROM alasan_tertarik", 'tipe' => $teks));
            $w['guru/get_jenis_kpi/GET'] = array(
                array('sql' => "SELECT nama_jenis AS v FROM jenis_kpi", 'tipe' => $teks));
            $w['guru/get_kategori_catatan/GET'] = array(
                array('sql' => "SELECT nama_kategori AS v FROM presensi_kategori_catatan", 'tipe' => $teks));
        }

        // ---- Gel 3: sisanya non-push (dinamis; estimasi kasar) ----
        if ($wave === '3') {
            $w['pimpinan/get_anggaran/POST'] = array(
                array('sql' => "SELECT nama_program AS v FROM program_anggaran_sd", 'tipe' => $teks),
                array('sql' => "SELECT tema_kegiatan AS v FROM program_anggaran_sd", 'tipe' => $teks),
                array('sql' => "SELECT nama_program AS v FROM program_anggaran_smp", 'tipe' => $teks),
                array('sql' => "SELECT tema_kegiatan AS v FROM program_anggaran_smp", 'tipe' => $teks),
                array('sql' => "SELECT nama_program AS v FROM program_anggaran_sma", 'tipe' => $teks),
                array('sql' => "SELECT tema_kegiatan AS v FROM program_anggaran_sma", 'tipe' => $teks));
            $w['root/list_notifikasi/POST'] = array(
                array('sql' => "SELECT title AS v FROM list_notifikasi", 'tipe' => $teks),
                array('sql' => "SELECT content AS v FROM list_notifikasi", 'tipe' => $teks));
        }

        return $w;
    }
}

if (!function_exists('translator_endpoint_sources')) {

    /**
     * Query sumber utk SATU endpoint (dicari di semua gelombang, hasil digabung).
     * return array(array('sql'=>, 'tipe'=>)) — kosong bila endpoint tidak dipetakan.
     */
    function translator_endpoint_sources($endpoint_key)
    {
        $out = array();
        foreach (array('1a', '1b', '2', '3') as $wave) {
            $w = translator_wave_sources($wave);
            if (isset($w[$endpoint_key]) && is_array($w[$endpoint_key])) {
                foreach ($w[$endpoint_key] as $q) $out[] = $q;
            }
        }
        return $out;
    }
}

if (!function_exists('translator_cli_args')) {

    /**
     * Parser argumen CLI gaya segmen URI (pasangan kunci/nilai). Flag nilai
     * greedy (mis. 'endpoint', 'out-dir' — nilainya mengandung '/') menyerap
     * semua segmen berikutnya sampai flag yang dikenal lagi. Flag boolean
     * ('execute', dst.) bernilai true tanpa nilai.
     *
     * Contoh: tools/translator_warmup/index/endpoint/root/visi_misi/GET/lang/en-GB/execute
     *   -> array('endpoint' => 'root/visi_misi/GET', 'lang' => 'en-GB', 'execute' => true)
     */
    function translator_cli_args(array $segs, array $value_flags, array $greedy = array(), array $bool_flags = array('execute'))
    {
        $args = array();
        $n = count($segs);
        for ($i = 0; $i < $n; $i++) {
            $k = $segs[$i];
            if (in_array($k, $bool_flags, true)) {
                $args[$k] = true;
                continue;
            }
            if (!in_array($k, $value_flags, true)) {
                continue; // segmen liar (sisa endpoint sebelumnya dsb.) diabaikan
            }
            $val = array();
            $j = $i + 1;
            if (in_array($k, $greedy, true)) {
                for (; $j < $n; $j++) {
                    if (in_array($segs[$j], $value_flags, true) || in_array($segs[$j], $bool_flags, true)) break;
                    $val[] = $segs[$j];
                }
            } elseif ($j < $n) {
                $val[] = $segs[$j];
                $j++;
            }
            $args[$k] = implode('/', $val);
            $i = $j - 1;
        }
        return $args;
    }
}

if (!function_exists('translator_fetch_strings')) {

    /**
     * Ekstraksi string unik ternormalisasi dari query sumber.
     * return array('strings' => array(teks => true), 'rows' => int, 'err' => bool)
     */
    function translator_fetch_strings(array $sources)
    {
        $CI =& get_instance();
        $CI->load->helper('translator_safe_db');
        $out = array('strings' => array(), 'rows' => 0, 'err' => false);
        foreach ($sources as $s) {
            $rows = translator_safe_query($s['sql'], array(), 'result', false);
            if (!is_array($rows)) { $out['err'] = true; continue; }
            foreach ($rows as $r) {
                $out['rows']++;
                foreach ((array) $r as $v) {
                    if (!is_string($v) || $v === '') continue;
                    $norm = translator_normalize($v);
                    if ($norm === '') continue;
                    if ($s['tipe'] === 'html') {
                        $out['strings'][$norm] = 'html';
                    } elseif (!isset($out['strings'][$norm])) {
                        $out['strings'][$norm] = 'teks';
                    }
                }
            }
        }
        return $out;
    }
}
