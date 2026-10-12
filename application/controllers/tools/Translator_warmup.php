<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Translator_warmup — pra-isi translation_cache utk konten bersama.
 * CLI ONLY (HTTP 404).
 *
 * Pemakaian:
 *   php tool/run_cli.php tools/translator_warmup --wave=1a --lang=en-GB            (DRY-RUN)
 *   php tool/run_cli.php tools/translator_warmup --wave=2 --lang=en-GB --execute --max-chars=1500
 *
 * --execute menerjemahkan string bersama=true gelombang itu lewat pipeline
 * Translator yang SAMA (normalisasi, hash prefix tipe, batch DeepL, INSERT
 * IGNORE cache) dgn timeout lebih panjang (translator_warmup_timeout).
 * Dry-run default: hanya menghitung string & karakter yang BELUM ada di
 * kamus/override/cache.
 */
class Translator_warmup extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->input->is_cli_request()) { show_404(); return; }
        $this->load->helper('translator_safe_db');
        $this->load->helper('translator_sources');
        $this->load->config('translator');
        $this->load->config('bahasa');
        $this->load->library('Translator');
    }

    public function index()
    {
        // argumen via segmen URI key/value: tools/translator_warmup/wave/2/lang/en-GB/execute/max-chars/1500
        // (CI URI filter menolak '=' dan '--')
        $args = array();
        $segs = $this->uri->segment_array();
        // buang segmen awal (tools, translator_warmup, index)
        $segs = array_slice($segs, 3);
        for ($i = 0; $i < count($segs); $i += 2) {
            $args[$segs[$i]] = isset($segs[$i + 1]) ? $segs[$i + 1] : true;
        }
        $wave = isset($args['wave']) ? $args['wave'] : '1a';
        $lang = isset($args['lang']) ? $args['lang'] : 'en-GB';
        $execute = !empty($args['execute']);
        $maxChars = isset($args['max-chars']) ? (int) $args['max-chars'] : 0;

        if (!in_array($wave, array('1a', '1b', '2', '3'), true)) {
            exit("wave tidak valid: {$wave} (1a|1b|2|3)\n");
        }
        $bahasa = $this->config->item('bahasa');
        if (!isset($bahasa[$lang]) || $lang === 'id') {
            exit("lang tidak valid: {$lang}\n");
        }

        echo "warm-up wave={$wave} lang={$lang} mode=" . ($execute ? 'EXECUTE' : 'DRY-RUN') . "\n";

        // 1) kumpulkan string sumber (tipe dipertahankan)
        $sources = translator_wave_sources($wave);
        $semua = array(); // teks => tipe
        $per_endpoint = array();
        foreach ($sources as $endpoint => $queries) {
            $f = translator_fetch_strings($queries);
            $n = 0; $c = 0;
            foreach ($f['strings'] as $txt => $tipe) {
                if (!isset($semua[$txt])) $semua[$txt] = $tipe;
                $n++; $c += strlen($txt);
            }
            $per_endpoint[$endpoint] = array('unik' => $n, 'chars' => $c, 'err' => $f['err']);
        }
        echo "sumber: " . count($semua) . " string unik\n";

        // 2) kurangi yang sudah tercover kamus/override/cache
        $kamus = $this->translator->kamus->load($lang);
        $kamus_keys = is_array($kamus) ? array_keys($kamus) : array();
        $hashes = array();
        foreach ($semua as $txt => $tipe) $hashes[] = translator_hash($txt, $tipe);

        $ov = $this->translator->cache->ambil_override_banyak($lang, $hashes);
        $ov = is_array($ov) ? $ov : array();
        $ca = $this->translator->cache->ambil_banyak($lang, $hashes);
        $ca = is_array($ca) ? $ca : array();

        $baru = array(); // teks => tipe (belum tercover)
        $covered = 0; $covered_chars = 0;
        foreach ($semua as $txt => $tipe) {
            $kunci = mb_strtolower($txt, 'UTF-8');
            $h = translator_hash($txt, $tipe);
            $kena_kamus = ($tipe !== 'html' && in_array($kunci, $kamus_keys, true));
            if ($kena_kamus || isset($ov[$h]) || isset($ca[$h])) {
                $covered++; $covered_chars += strlen($txt);
                continue;
            }
            $baru[$txt] = $tipe;
        }
        $chars_baru = 0;
        foreach ($baru as $txt => $tipe) $chars_baru += strlen($txt);

        echo "sudah tercover (kamus/override/cache): {$covered} string / {$covered_chars} char\n";
        echo "BELUM tercover: " . count($baru) . " string / {$chars_baru} char\n";
        foreach ($per_endpoint as $ep => $info) {
            echo sprintf("   %-34s unik=%-5d chars=%-7d %s\n", $ep, $info['unik'], $info['chars'], $info['err'] ? '[tabel tidak terbaca]' : '');
        }

        if (!$execute) {
            echo "\nDRY-RUN selesai. Jalankan dgn --execute --max-chars=N utk menerjemahkan.\n";
            return;
        }

        // 3) batasi sesuai --max-chars
        if ($maxChars > 0 && $chars_baru > $maxChars) {
            echo "anggaran {$maxChars} < kebutuhan {$chars_baru} — ambil subset (urut teks)\n";
            $ambil = array(); $ambil_chars = 0;
            $keys = array_keys($baru); sort($keys);
            foreach ($keys as $txt) {
                $len = strlen($txt);
                if ($ambil_chars + $len > $maxChars) continue;
                $ambil[$txt] = $baru[$txt];
                $ambil_chars += $len;
            }
            $baru = $ambil;
            echo "dieksekusi: " . count($baru) . " string / {$ambil_chars} char\n";
        }
        if (empty($baru)) {
            echo "tidak ada string baru utk diterjemahkan. SELESAI.\n";
            return;
        }

        // 4) jalankan lewat pipeline Translator yang SAMA (timeout warm-up)
        $this->config->set_item('translator_deepl_total_timeout',
            (int) translator_cfg('translator_warmup_timeout', 30));
        $this->config->set_item('translator_time_budget',
            (float) translator_cfg('translator_warmup_timeout', 30));

        // breaker harus tertutup utk warm-up (hindari menelan semua string sbg gagal)
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        $this->cache->save(translator_cfg('translator_breaker_cache_key', 'translator_breaker_v1'),
            array('fails' => 0, 'open_until' => 0), 600);

        $items = array();
        foreach ($baru as $txt => $tipe) {
            $items[] = array('text' => $txt, 'tipe' => $tipe, 'bersama' => true);
        }
        $hasil = $this->translator->translate_strings($items, $lang);
        $st = $this->translator->get_last_stats();

        echo "\n== ringkasan eksekusi ==\n";
        echo "string input     : " . count($items) . "\n";
        echo "dari DeepL       : " . (int) $st['dari_deepl'] . "\n";
        echo "karakter dikirim : " . (int) $st['deepl_chars'] . "\n";
        echo "karakter billed  : " . (int) $st['deepl_billed'] . "\n";
        echo "gagal (asli)     : " . (int) $st['asli_gagal'] . "\n";
        $limit = (int) translator_cfg('translator_monthly_char_limit', 500000);
        $u = translator_safe_query(
            "SELECT jumlah_karakter FROM translation_usage_bulanan WHERE bulan = DATE_FORMAT(NOW(), '%Y-%m') AND penyedia = 'deepl' LIMIT 1",
            array(), 'row', null);
        $pakai = ($u && isset($u->jumlah_karakter)) ? (int) $u->jumlah_karakter : 0;
        echo "kuota: terpakai {$pakai} / {$limit} | sisa " . max(0, $limit - $pakai) . "\n";
    }
}
