<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Translator_kalibrasi — kalibrasi billed_characters DeepL. CLI ONLY.
 *
 * Alur:
 *  1) GET /v2/usage (sebelum)
 *  2) POST /v2/translate 1 teks pendek diketahui panjangnya (show_billed_characters=1)
 *  3) GET /v2/usage (sesudah)
 * Laporkan: billed_characters dari respons, hitungan lokal, dan selisih
 * GET /v2/usage sebelum-sesudah. Tanpa menulis database.
 *
 * Pemakaian: php tool/run_cli.php tools/translator_kalibrasi
 */
class Translator_kalibrasi extends MX_Controller {

    public function __construct() { parent::__construct(); }

    public function index()
    {
        if (!$this->input->is_cli_request()) { show_404(); return; }

        $this->config->load('translator');
        $this->load->helper('translator_safe_db');

        $key = translator_env('DEEPL_API_KEY');
        if ($key === '') { echo "DEEPL_API_KEY kosong di .env\n"; exit(1); }
        $base = (substr($key, -3) === ':fx')
            ? 'https://api-free.deepl.com'
            : 'https://api.deepl.com';

        // ---- 1) usage sebelum
        $before = $this->usage($key, $base);
        if ($before === null) { echo "Gagal GET usage sebelum\n"; exit(1); }
        echo "usage SEBELUM : character_count={$before['count']} character_limit={$before['limit']}\n";

        // ---- 2) translate 1 teks pendek (panjang diketahui)
        $teks = 'Kalibrasi kuota DeepL.'; // 23 karakter
        echo "teks: \"{$teks}\" (strlen=" . strlen($teks) . ", mb_strlen=" . mb_strlen($teks) . ")\n";

        $post = http_build_query(array(
            'source_lang' => 'ID',
            'target_lang' => 'EN-GB',
            'preserve_formatting' => '1',
            'show_billed_characters' => '1',
            'text' => $teks,
        ));
        $ch = curl_init($base . '/v2/translate');
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_HTTPHEADER => array(
                'Authorization: DeepL-Auth-Key ' . $key,
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0 || $code !== 200 || $body === false) {
            echo "Gagal translate: errno={$errno} http={$code}\n"; exit(1);
        }
        $json = json_decode($body, true);
        $tr = isset($json['translations'][0]) ? $json['translations'][0] : array();
        $billed = isset($tr['billed_characters']) ? (int) $tr['billed_characters'] : null;
        $hitung = strlen($teks);
        echo "hasil terjemahan: " . (isset($tr['text']) ? $tr['text'] : '-') . "\n";
        if ($billed === null) {
            echo "billed_characters: TIDAK ADA di respons (fallback ke hitungan lokal)\n";
        } else {
            echo "billed_characters: {$billed} (hitungan lokal: {$hitung}, selisih=" . ($billed - $hitung) . ")\n";
        }

        // ---- 3) usage sesudah
        $after = $this->usage($key, $base);
        if ($after === null) { echo "Gagal GET usage sesudah\n"; exit(1); }
        echo "usage SESUDAH : character_count={$after['count']}\n";
        echo "selisih usage : " . ($after['count'] - $before['count']) . "\n";
    }

    private function usage($key, $base)
    {
        $ch = curl_init($base . '/v2/usage');
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
        if ($errno !== 0 || $code !== 200 || $body === false) return null;
        $j = json_decode($body, true);
        if (!isset($j['character_count'])) return null;
        return array('count' => (int) $j['character_count'], 'limit' => (int) $j['character_limit']);
    }
}
