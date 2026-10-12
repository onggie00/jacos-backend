<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Translator_warmup — pra-isi translation_cache utk konten bersama.
 * CLI ONLY (HTTP 404).
 *
 * Pemakaian:
 *   php tool/run_cli.php tools/translator_warmup wave/1a lang/en-GB            (DRY-RUN)
 *   php tool/run_cli.php tools/translator_warmup wave/2 lang/en-GB execute max-chars/1500
 *   php tool/run_cli.php tools/translator_warmup endpoint/root/visi_misi/GET lang/en-GB execute max-chars/8000
 *
 * --endpoint (segmen: endpoint/<kunci whitelist>) hanya memproses string
 * path whitelist aktif bersama=TRUE milik endpoint itu; kombinasi dgn
 * lang & max-chars tetap berlaku. Kunci tidak ada di whitelist aktif atau
 * tanpa path bersama=true -> ditolak. Execute eksplisit; default DRY-RUN.
 * Dry-run menampilkan jumlah string, panjang karakter, tipe & total — tanpa
 * isi teks.
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
        // nilai greedy 'endpoint' menyerap segmen berikutnya (kunci mengandung '/')
        $args = translator_cli_args(
            array_slice($this->uri->segment_array(), 3),
            array('wave', 'lang', 'endpoint', 'max-chars'),
            array('endpoint')
        );
        $wave = isset($args['wave']) ? $args['wave'] : '1a';
        $lang = isset($args['lang']) ? $args['lang'] : 'en-GB';
        $execute = !empty($args['execute']);
        $maxChars = isset($args['max-chars']) ? (int) $args['max-chars'] : 0;
        $endpoint = (isset($args['endpoint']) && $args['endpoint'] !== '') ? $args['endpoint'] : null;

        if ($endpoint === null && !in_array($wave, array('1a', '1b', '2', '3'), true)) {
            exit("wave tidak valid: {$wave} (1a|1b|2|3)\n");
        }
        $bahasa = $this->config->item('bahasa');
        if (!isset($bahasa[$lang]) || $lang === 'id') {
            exit("lang tidak valid: {$lang}\n");
        }

        // 0) validasi endpoint (hanya bila dipakai)
        if ($endpoint !== null) {
            $spec = $this->translator->whitelist->endpoint_aktif($endpoint);
            if ($spec === null) {
                exit("endpoint tidak ada di whitelist aktif (atau seluruhnya excluded): {$endpoint}\n");
            }
            $ada_bersama = false;
            foreach ($spec as $meta) {
                if (is_array($meta) && !empty($meta['bersama'])) { $ada_bersama = true; break; }
            }
            if (!$ada_bersama) {
                exit("endpoint tidak punya path whitelist bersama=true: {$endpoint}\n");
            }
        }

        echo "warm-up " . ($endpoint !== null ? "endpoint={$endpoint}" : "wave={$wave}") . " lang={$lang} mode=" . ($execute ? 'EXECUTE' : 'DRY-RUN') . "\n";

        // 1) kumpulkan string sumber (tipe dipertahankan)
        if ($endpoint !== null) {
            $queries = translator_endpoint_sources($endpoint);
            if (empty($queries)) {
                echo "endpoint ini tidak dipetakan di helper sumber (translator_sources_helper) — tidak ada string.\n";
                return;
            }
            $sources = array($endpoint => $queries);
        } else {
            $sources = translator_wave_sources($wave);
        }
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

        // 1b) rincian per tipe (teks/html) utk dry-run endpoint
        if ($endpoint !== null) {
            $tipe_stat = array();
            foreach ($semua as $txt => $tipe) {
                if (!isset($tipe_stat[$tipe])) $tipe_stat[$tipe] = array('n' => 0, 'c' => 0);
                $tipe_stat[$tipe]['n']++;
                $tipe_stat[$tipe]['c'] += strlen($txt);
            }
            ksort($tipe_stat);
            foreach ($tipe_stat as $t => $s) {
                echo sprintf("   tipe %-5s unik=%-5d chars=%d\n", $t, $s['n'], $s['c']);
            }
        }

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
            if ($info['unik'] === 0 && $info['err']) {
                echo "   {$ep}: tabel sumber TIDAK TERBACA / kosong di database ini\n";
                continue;
            }
            echo sprintf("   %-34s unik=%-5d chars=%-7d %s\n", $ep, $info['unik'], $info['chars'], $info['err'] ? '[sebagian tabel tidak terbaca]' : '');
        }
        if (empty($semua)) {
            echo "TIDAK ADA string ditemukan (tabel sumber kosong di database lokal / semua tak terbaca). Tidak ada yang diproses.\n";
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

        // 3b) cek kuota memakai angka RIIL DeepL (GET /v2/usage — tidak memakai kuota)
        $pakai_kuota = 0; $limit_kuota = (int) translator_cfg('translator_monthly_char_limit', 500000);
        $sumber_kuota = 'lokal (fallback)';
        $key = translator_env('DEEPL_API_KEY');
        if ($key !== '') {
            $url_usage = (substr($key, -3) === ':fx')
                ? 'https://api-free.deepl.com/v2/usage'
                : 'https://api.deepl.com/v2/usage';
            $ch = curl_init($url_usage);
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_TIMEOUT => 3,
                CURLOPT_HTTPHEADER => array('Authorization: DeepL-Auth-Key ' . $key),
            ));
            $body = curl_exec($ch);
            $errno = curl_errno($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $json = ($errno === 0 && $code === 200 && $body !== false) ? json_decode($body, true) : null;
            if (is_array($json) && isset($json['character_count'], $json['character_limit'])) {
                $pakai_kuota = (int) $json['character_count'];
                $limit_kuota = (int) $json['character_limit'];
                $sumber_kuota = 'deepl (riil)';
            } else {
                $u = translator_safe_query(
                    "SELECT jumlah_karakter FROM translation_usage_bulanan WHERE bulan = DATE_FORMAT(NOW(), '%Y-%m') AND penyedia = 'deepl' LIMIT 1",
                    array(), 'row', null);
                $pakai_kuota = ($u && isset($u->jumlah_karakter)) ? (int) $u->jumlah_karakter : 0;
                log_message('error', 'AUDIT warmup: kuota fallback ke DB lokal (panggilan /v2/usage gagal, HTTP ' . $code . ')');
            }
        } else {
            $u = translator_safe_query(
                "SELECT jumlah_karakter FROM translation_usage_bulanan WHERE bulan = DATE_FORMAT(NOW(), '%Y-%m') AND penyedia = 'deepl' LIMIT 1",
                array(), 'row', null);
            $pakai_kuota = ($u && isset($u->jumlah_karakter)) ? (int) $u->jumlah_karakter : 0;
            log_message('error', 'AUDIT warmup: kuota fallback ke DB lokal (DEEPL_API_KEY kosong)');
        }
        $batas_90 = (int) floor($limit_kuota * 0.9);
        echo "kuota [{$sumber_kuota}]: terpakai {$pakai_kuota} / {$limit_kuota} | batas 90% = {$batas_90} | rencana {$chars_baru} char\n";
        if (($pakai_kuota + $chars_baru) >= $batas_90) {
            echo "STOP: rencana akan melewati 90% limit riil DeepL. Tidak ada eksekusi.\n";
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
