<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Translator_export_test — bukti paket ekspor (canary visi_misi):
 * 1. hash dari string whitelist endpoint DIHITUNG ULANG, harus sama dgn baris
 *    di schema uji (hasil impor cache.sql).
 * 2. translate_response root/visi_misi dgn X-Lang: en-GB pada schema uji
 *    harus memakai cache IMPOR (deepl_chars = 0, hasil = teks_hasil cache).
 * CLI ONLY (HTTP 404). Schema uji dibuat terpisah & dibuang setelah tes.
 *
 * Pemakaian:
 *   php tool/run_cli.php tools/translator_export_test/index/schema/jacos_export_test
 */
class Translator_export_test extends MX_Controller {

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
        $args = translator_cli_args(array_slice($this->uri->segment_array(), 3), array('schema'), array('schema'));
        $schema = isset($args['schema']) && $args['schema'] !== '' ? $args['schema'] : 'jacos_export_test';

        // pengaman: hanya schema uji berawalan jacos_ uji
        if (strpos($schema, 'jacos_export_test') !== 0) {
            exit("schema harus berawalan jacos_export_test (schema uji terpisah, dibuang setelah tes)\n");
        }

        // 1) hash dihitung ulang dari string whitelist bersama=true — di database SUMBER,
        //    sebelum pindah koneksi (tabel sumber tidak ada di schema uji)
        $queries = translator_endpoint_sources('root/visi_misi/GET');
        $f = translator_fetch_strings($queries);
        $hashes = array();
        foreach ($f['strings'] as $txt => $tipe) $hashes[] = translator_hash($txt, $tipe);
        sort($hashes);

        // pindah koneksi default ke schema uji (koneksi baru menimpa get_instance()->db,
        // dipakai translator_safe_query & semua query Translator)
        $cfg = array(
            'hostname' => $this->db->hostname,
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => $schema,
            'dbdriver' => 'mysqli',
            'db_debug' => TRUE,
        );
        get_instance()->db = $this->load->database($cfg, TRUE);
        echo "schema uji: " . get_instance()->db->database . " (@@DATABASE()=" . translator_safe_query('SELECT DATABASE() AS d', array(), 'row', null)->d . ")\n";

        $rows = translator_safe_query('SELECT hash_sumber, teks_asal, teks_hasil FROM translation_cache WHERE kode_bahasa = ?', array('en-GB'), 'result', false);
        $db_hashes = array();
        foreach ((array) $rows as $r) $db_hashes[] = $r->hash_sumber;
        sort($db_hashes);

        if ($hashes === $db_hashes && !empty($hashes)) {
            echo "PASS 1: " . count($hashes) . " hash whitelist == hash impor (cocok persis)\n";
        } else {
            echo "FAIL 1: hash whitelist vs impor BEDA\n";
            echo "  whitelist: " . implode(', ', $hashes) . "\n";
            echo "  impor    : " . implode(', ', $db_hashes) . "\n";
            return;
        }

        // 2) panggil pipeline root/visi_misi dgn X-Lang en-GB (translate_response, only_bersama)
        //    breaker dipaksa TERTUTUP supaya bukti "deepl_chars=0" murni dari cache impor
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        $CI->cache->save(translator_cfg('translator_breaker_cache_key', 'translator_breaker_v1'),
            array('fails' => 0, 'open_until' => 0), 600);
        $this->config->set_item('translator_enabled', TRUE);

        $data = array('data' => array());
        foreach ((array) $rows as $r) $data['data'][] = array('isi' => $r->teks_asal);
        $asli_cache = array();
        foreach ((array) $rows as $r) $asli_cache[$r->teks_asal] = $r->teks_hasil;

        $this->translator->translate_response($data, 'root/visi_misi/GET', 'en-GB', true);
        $st = $this->translator->get_last_stats();

        echo "stats: total_string=" . $st['total_string'] . " dari_cache=" . $st['dari_cache']
            . " dari_deepl=" . $st['dari_deepl'] . " deepl_chars=" . $st['deepl_chars']
            . " deepl_billed=" . $st['deepl_billed'] . " gagal=" . $st['asli_gagal'] . "\n";

        $cocok = 0;
        $hasil_cache = array();
        foreach ((array) $rows as $r) $hasil_cache[$r->teks_hasil] = true;
        foreach ($data['data'] as $row) {
            if (isset($hasil_cache[$row['isi']])) $cocok++;
        }
        if ((int) $st['deepl_chars'] === 0 && (int) $st['dari_deepl'] === 0 && $cocok === count($asli_cache) && $cocok > 0) {
            echo "PASS 2: deepl_chars=0, semua " . $cocok . " string dilayani cache impor (hasil = teks_hasil persis)\n";
        } else {
            echo "FAIL 2: cocok={$cocok}/" . count($asli_cache) . " deepl_chars=" . $st['deepl_chars'] . "\n";
        }
    }
}
