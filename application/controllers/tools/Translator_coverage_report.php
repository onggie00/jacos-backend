<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Translator_coverage_report — ringkas log telemetri cakupan translator.
 * CLI ONLY (HTTP 404). HANYA membaca; tidak memanggil DeepL/Google.
 *
 * Sumber: application/logs/translator-coverage-YYYY-MM-DD.log (aktif bila
 * config translator_coverage_log = TRUE). Format baris:
 *   endpoint_key|token_ada(0/1)|token_dikenal(0/1)|x_lang_ada(0/1)|bahasa|diterjemahkan(0/1)|sumber(token/x-lang/none)
 * Tanpa token/email/IP — hanya flag & kode bahasa.
 *
 * Pemakaian:
 *   php tool/run_cli.php tools/translator_coverage_report            (7 hari terakhir)
 *   php tool/run_cli.php tools/translator_coverage_report/index/days/30
 */
class Translator_coverage_report extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->input->is_cli_request()) { show_404(); return; }
        $this->load->helper('translator_sources');
        $this->load->config('translator');
    }

    public function index()
    {
        $args = translator_cli_args(array_slice($this->uri->segment_array(), 3), array('days'));
        $days = (isset($args['days']) && (int) $args['days'] > 0) ? (int) $args['days'] : 7;

        $stat = array(); // endpoint => array(total, token, xlang, kenal, terj)
        $baris_valid = 0; $baris_skip = 0;
        for ($i = 0; $i < $days; $i++) {
            $file = APPPATH . 'logs/translator-coverage-' . date('Y-m-d', strtotime("-{$i} day")) . '.log';
            if (!is_file($file)) continue;
            $fh = @fopen($file, 'r');
            if (!$fh) continue;
            while (($line = fgets($fh)) !== false) {
                $line = trim($line);
                if ($line === '') continue;
                $p = explode('|', $line);
                if (count($p) !== 7) { $baris_skip++; continue; }
                $ep = $p[0];
                if (!isset($stat[$ep])) $stat[$ep] = array('total' => 0, 'token' => 0, 'xlang' => 0, 'kenal' => 0, 'terj' => 0);
                $stat[$ep]['total']++;
                if ($p[1] === '1') $stat[$ep]['token']++;
                if ($p[2] === '1') $stat[$ep]['kenal']++;
                if ($p[3] === '1') $stat[$ep]['xlang']++;
                if ($p[5] === '1') $stat[$ep]['terj']++;
                $baris_valid++;
            }
            fclose($fh);
        }

        echo "=== LAPORAN CAKUPAN TRANSLATOR ({$days} hari terakhir) ===\n";
        echo "baris log valid: {$baris_valid}" . ($baris_skip > 0 ? " | dilewati (format): {$baris_skip}" : '') . "\n";
        if (empty($stat)) {
            echo "tidak ada data (log belum aktif / belum ada trafik). Aktifkan\n";
            echo "config translator_coverage_log = TRUE untuk mulai merekam.\n";
            return;
        }
        echo sprintf("%-40s %6s %8s %8s %8s %8s\n", 'endpoint', 'total', '%token', '%x-lang', '%kenal', '%terjemah');
        ksort($stat);
        foreach ($stat as $ep => $s) {
            echo sprintf("%-40s %6d %7.1f%% %7.1f%% %7.1f%% %7.1f%%\n",
                $ep, $s['total'],
                $s['total'] ? $s['token'] * 100.0 / $s['total'] : 0,
                $s['total'] ? $s['xlang'] * 100.0 / $s['total'] : 0,
                $s['total'] ? $s['kenal'] * 100.0 / $s['total'] : 0,
                $s['total'] ? $s['terj'] * 100.0 / $s['total'] : 0);
        }
        echo "\nketerangan: %token = request dgn X-Token; %kenal = token dikenali;\n";
        echo "%x-lang = request dgn X-Lang; %terjemah = response diterjemahkan.\n";
    }
}
