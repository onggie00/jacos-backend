<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Translator_estimate — estimasi karakter DeepL per gelombang rollout.
 * CLI ONLY. HANYA membaca data lokal (tanpa DeepL, tanpa menulis).
 *
 * Gelombang (definisi di helpers/translator_sources_helper.php):
 *   1a = konten admin bersama tipe TEKS
 *   1b = konten admin bersama tipe HTML
 *   2  = label & master data
 *   3  = sisanya non-push (dinamis; estimasi kasar)
 *
 * CATATAN: angka dari data LOKAL — bisa berbeda dari produksi; metode
 * konservatif (belum dikurangi overlap kamus/override/cache per string).
 */
class Translator_estimate extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->input->is_cli_request()) { show_404(); return; }
        $this->load->helper('translator_safe_db');
        $this->load->helper('translator_sources');
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
        foreach (array('1a', '1b', '2', '3') as $wave) {
            $sources = translator_wave_sources($wave);
            $total = 0; $total_strings = 0;
            echo "-- Gelombang {$wave}\n";
            foreach ($sources as $endpoint => $queries) {
                $f = translator_fetch_strings($queries);
                if ($f['err']) { echo "   {$endpoint}: tabel tidak terbaca\n"; continue; }
                $chars = 0;
                foreach ($f['strings'] as $txt => $tipe) $chars += strlen($txt);
                echo sprintf("   %-34s unik=%-6d chars=%d\n", $endpoint, count($f['strings']), $chars);
                $total += $chars; $total_strings += count($f['strings']);
            }
            echo sprintf("   TOTAL gelombang %s: %s karakter / %s string unik\n\n", $wave, number_format($total), number_format($total_strings));
            $total_semua += $total;
        }

        echo "TOTAL gelombang 1a+1b+2+3: " . number_format($total_semua) . " karakter (cold start)\n";
        echo "Push notification (FCM/pesan) = Fase 8, tidak diestimasi.\n";
        echo "CATATAN: data lokal bisa berbeda dari produksi; angka konservatif\n";
        echo "(belum dikurangi overlap dgn kamus/override/cache per string).\n";
    }
}
