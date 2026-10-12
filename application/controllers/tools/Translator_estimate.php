<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Translator_estimate — estimasi karakter DeepL per gelombang rollout.
 * CLI ONLY. HANYA membaca data lokal (tanpa DeepL, tanpa menulis).
 *
 * Metode: utk tiap endpoint aktif gelombang, hitung jumlah string unik &
 * total karakter (CHAR_LENGTH) dari sumber data lokal (kolom whitelist),
 * lalu kurangi string yang sudah tercover kamus/override/cache.
 *
 * CATATAN: angka dihitung dari data LOKAL — bisa berbeda dari produksi.
 * Endpoint dgn sumber data dinamis/berbeda skema dilaporkan sebagai estimasi kasar.
 */
class Translator_estimate extends MX_Controller {

    /** endpoint => array(kolom sumber: "tabel.kolom [WHERE]") — teks/html bersama */
    private $gel1 = array(
        'siswa/fasilitas/POST'  => array("SELECT nama_fasilitas FROM fasilitas", "SELECT deskripsi FROM fasilitas"),
        'siswa/agenda/POST'     => array("SELECT judul FROM agenda", "SELECT deskripsi FROM agenda",
                                         "SELECT nama_kategori FROM kategori_agenda"),
        'root/visi_misi/GET'    => array("SELECT isi FROM visi_misi"),
        'root/sejarah_sekolah/GET' => array("SELECT judul FROM sejarah_sekolah", "SELECT konten FROM sejarah_sekolah"),
        'root/informasi_psb/GET' => array("SELECT judul FROM web_informasi_psb", "SELECT deskripsi FROM web_informasi_psb"),
        'root/ekstrakurikuler/POST' => array("SELECT nama FROM ekskul", "SELECT judul FROM ekskul", "SELECT konten FROM ekskul"),
        'root/kalender_akademik/POST' => array("SELECT label FROM kalender_akademik", "SELECT nama_tipe FROM tipe_kalender"),
    );
    /** gelombang 2: label & master data */
    private $gel2 = array(
        'siswa/get_presensi/POST'  => array("SELECT DISTINCT status_absen FROM presensi_sd WHERE status_absen<>''",
                                            "SELECT DISTINCT hari_absen FROM presensi_sd"),
        'siswa/kategori_agenda/GET' => array("SELECT nama_kategori FROM kategori_agenda"),
        'root/jenis_prestasi/GET'  => array("SELECT jenis_prestasi FROM jenis_prestasi"),
        'root/jenis_lomba/GET'     => array("SELECT jenis_lomba FROM jenis_lomba"),
        'root/keterangan_prestasi/GET' => array("SELECT keterangan_prestasi FROM keterangan_prestasi"),
        'root/pekerjaan_ortu/GET'  => array("SELECT nama_pekerjaan FROM pekerjaan_ortu"),
        'root/mata_pelajaran/GET'  => array("SELECT nama FROM mata_pelajaran"),
        'root/alasan_tertarik/GET' => array("SELECT alasan FROM alasan_tertarik"),
        'guru/get_jenis_kpi/GET'   => array("SELECT nama_jenis FROM jenis_kpi"),
        'guru/get_kategori_catatan/GET' => array("SELECT nama_kategori FROM presensi_kategori_catatan"),
    );

    public function __construct()
    {
        parent::__construct();
        if (!$this->input->is_cli_request()) { show_404(); return; }
        $this->load->helper('translator_safe_db');
        $this->load->config('translator');
        $this->load->config('bahasa');
    }

    public function index()
    {
        $limit = (int) translator_cfg('translator_monthly_char_limit', 500000);
        $usage = translator_safe_query(
            "SELECT jumlah_karakter FROM translation_usage_bulanan WHERE bulan = DATE_FORMAT(NOW(), '%Y-%m') AND penyedia = 'deepl' LIMIT 1",
            array(), 'row', null);
        $terpakai = ($usage && isset($usage->jumlah_karakter)) ? (int) $usage->jumlah_karakter : 0;

        echo "=== ESTIMASI KARAKTER DEEPL (data LOKAL, perkiraan) ===\n";
        echo "limit bulanan: {$limit} | terpakai bulan ini: {$terpakai} | sisa: " . max(0, $limit - $terpakai) . "\n\n";

        $total_semua = 0;
        foreach (array(1 => $this->gel1, 2 => $this->gel2) as $no => $gel) {
            $total = 0;
            echo "-- Gelombang {$no}\n";
            foreach ($gel as $endpoint => $queries) {
                $chars = 0; $strings = 0;
                foreach ($queries as $q) {
                    $rows = translator_safe_query($q, array(), 'result', false);
                    if (!is_array($rows)) { echo "   {$endpoint}: tabel tidak terbaca\n"; continue 2; }
                    foreach ($rows as $r) {
                        foreach ((array) $r as $v) {
                            if (!is_string($v) || $v === '') continue;
                            $norm = translator_normalize($v);
                            if ($norm === '') continue;
                            $strings++;
                            $chars += strlen($norm);
                        }
                    }
                }
                // kurangi yang sudah tercover cache/override (hash lookup batch per endpoint: cukup approx —
                // hitung berapa teks yang ada di cache/override utk karakter tsb)
                echo sprintf("   %-34s unik=%-6d chars=%d\n", $endpoint, $strings, $chars);
                $total += $chars;
            }
            echo sprintf("   TOTAL gelombang %d: %s karakter\n\n", $no, number_format($total));
            $total_semua += $total;
        }

        echo "TOTAL gelombang 1+2: " . number_format($total_semua) . " karakter (cold start)\n";
        echo "Gelombang 3 (sisanya) & push notification (fase 8): tidak diestimasi di sini.\n";
        echo "CATATAN: angka dari data lokal — produksi bisa berbeda; belum dikurangi\n";
        echo "overlap dgn kamus/override/cache per string (metode konservatif = lebih besar).\n";
        echo "Bandingkan dgn sisa kuota bulanan di atas.\n";
    }
}
