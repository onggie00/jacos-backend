<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Translator_test — PENGUJIAN Fase 3 (CLI ONLY).
 * Menolak akses non-CLI. Tidak tersambung ke endpoint manapun.
 *
 * Pemakaian: php index.php tools/translator_test [deepl_real]
 *   arg deepl_real = lakukan panggilan DeepL nyata (teks pendek, < 3000 char)
 */
class Translator_test extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index($deepl_real = '')
    {
        if (!$this->input->is_cli_request()) {
            show_404();
            return;
        }
        $this->load->library('Translator');
        $this->load->helper('translator_safe_db');

        $pass = 0; $fail = 0;
        $t = function($nama, $ok, $detail = '') use (&$pass, &$fail) {
            echo ($ok ? 'PASS' : 'FAIL') . " | $nama" . ($detail !== '' ? " | $detail" : ''), PHP_EOL;
            if ($ok) { $pass++; } else { $fail++; }
        };

        $doReal = ($deepl_real === 'deepl_real');

        // reset breaker
        $this->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        $this->cache->save($this->config->item('translator_breaker_cache_key'), array('fails' => 0, 'open_until' => 0), 600);

        // =====================================================
        echo "== 1. bahasa id: tanpa query & tanpa jaringan\n";
        // =====================================================
        $data_id = array('status' => 1, 'data' => array(array('judul' => 'Kegiatan Sekolah')));
        $sql_count_before = (int) $this->db->query('SHOW GLOBAL STATUS LIKE "Com_select"')->row()->Value;
        $out = $this->translator->translate_response($data_id, 'siswa/agenda/POST', 'id');
        $sql_count_after = (int) $this->db->query('SHOW GLOBAL STATUS LIKE "Com_select"')->row()->Value;
        $st = $this->translator->get_last_stats();
        $t('id: data tetap', ($out === $data_id || $out['data'][0]['judul'] === 'Kegiatan Sekolah'));
        $t('id: tanpa query SELECT', ($sql_count_after === $sql_count_before), "delta=" . ($sql_count_after - $sql_count_before));
        $t('id: stats id_atau_unknown=1', ($st['id_atau_unknown'] === 1));
        $t('id: tanpa jaringan (deepl_requests=0)', ($st['deepl_requests'] === 0));

        // =====================================================
        echo "== 2. kamus message + label komposit\n";
        // =====================================================
        $msg = $this->translator->translate_message('Data tidak ditemukan', 'en-GB');
        $t('kamus message', ($msg === 'No data found'), $msg);
        $msg2 = $this->translator->translate_message('Pesan yang tidak ada di kamus', 'en-GB');
        $t('kamus message fallback asli', ($msg2 === 'Pesan yang tidak ada di kamus'), $msg2);

        $labels = array(
            array('text' => 'Hadir', 'tipe' => 'label', 'bersama' => true),
            array('text' => 'senin,selasa', 'tipe' => 'label', 'bersama' => true),
            array('text' => 'senin, selasa', 'tipe' => 'label', 'bersama' => true),
        );
        $out_labels = $this->translator->translate_strings($labels, 'en-GB');
        $t('label hadir', (isset($out_labels[0]) && $out_labels[0] === 'Present'), isset($out_labels[0]) ? $out_labels[0] : '');
        $t('label komposit', (isset($out_labels[1]) && $out_labels[1] === 'Monday,Tuesday'), isset($out_labels[1]) ? $out_labels[1] : '');
        $t('label komposit spasi', (isset($out_labels[2]) && $out_labels[2] === 'Monday, Tuesday'), isset($out_labels[2]) ? $out_labels[2] : '');

        // =====================================================
        echo "== 3. override (tabel tidak ada = skip; jika ada: dipakai)\n";
        // =====================================================
        $ov = translator_safe_query("SHOW TABLES LIKE 'translation_override'", array(), 'result', false);
        $ada_override = is_array($ov) && count($ov) > 0;
        $teks_ov = 'Istilah khusus sekolah';
        $out_ov = $this->translator->translate_strings(array(array('text' => $teks_ov, 'tipe' => 'teks', 'bersama' => true)), 'en-GB');
        if (!$ada_override) {
            $t('override: tabel tidak ada -> service tetap jalan (tidak fatal)', true, '(tanpa tabel; DDL diusulkan di laporan)');
            $t('override: hasil berupa string', (is_string($out_ov[0]) && $out_ov[0] !== ''), substr((string)$out_ov[0], 0, 40));
        } else {
            $h = translator_hash(translator_normalize($teks_ov), 'teks');
            translator_safe_query("INSERT IGNORE INTO translation_override (hash_sumber, kode_bahasa, teks_asal, teks_hasil, aktif) VALUES (?, 'en-GB', ?, 'Custom school term', 1)", array($h, $teks_ov), 'write');
            $out_ov = $this->translator->translate_strings(array(array('text' => $teks_ov, 'tipe' => 'teks', 'bersama' => true)), 'en-GB');
            $t('override: dipakai prioritas', ($out_ov[0] === 'Custom school term'), $out_ov[0]);
            translator_safe_query("DELETE FROM translation_override WHERE hash_sumber = ?", array($h), 'write');
        }

        // =====================================================
        echo "== 4. cache hit pemanggilan kedua + hit_count tidak naik < 1 hari\n";
        // =====================================================
        // teks pendek non-sensitif utk cache; via DeepL real (pendek) atau fallback asli
        $teks_cache = 'Fasilitas laboratorium komputer sekolah';
        $args = array(array('text' => $teks_cache, 'tipe' => 'teks', 'bersama' => true));
        $this->translator->translate_strings($args, 'en-GB'); // pertama: isi cache (jika DeepL ok)
        $st1 = $this->translator->get_last_stats();
        $h = translator_hash(translator_normalize($teks_cache), 'teks');
        $row = translator_safe_query("SELECT hit_count, teks_hasil FROM translation_cache WHERE hash_sumber = ? AND kode_bahasa = 'en-GB'", array($h), 'row', false);
        $t('cache: row tersimpan (bersama=true walau >500?', is_object($row), is_object($row) ? 'hit_count=' . $row->hit_count : 'teks panjang?' . strlen($teks_cache));

        // kedua: harus cache hit
        $this->translator->translate_strings($args, 'en-GB');
        $st2 = $this->translator->get_last_stats();
        $t('cache: hit kedua dari_cache', ($st2['dari_cache'] === 1), 'dari_cache=' . $st2['dari_cache'] . ' deepl=' . $st2['dari_deepl']);
        $row2 = translator_safe_query("SELECT hit_count FROM translation_cache WHERE hash_sumber = ? AND kode_bahasa = 'en-GB'", array($h), 'row', false);
        $t('cache: hit_count tidak naik (<1 hari)', (is_object($row2) && is_object($row) && $row2->hit_count === $row->hit_count), 'hit=' . (is_object($row2) ? $row2->hit_count : '-'));

        // =====================================================
        echo "== 5. batch: N string unik = SATU request DeepL (per stats deepl_requests)\n";
        // =====================================================
        $items = array();
        foreach (array('Fasilitas perpustakaan', 'Fasilitas laboratorium', 'Fasilitas lapangan olahraga') as $x) {
            $items[] = array('text' => $x, 'tipe' => 'teks', 'bersama' => true);
        }
        $this->translator->translate_strings($items, 'en-GB');
        $st3 = $this->translator->get_last_stats();
        $t('batch: 3 teks unik <= 1 request', ($st3['deepl_requests'] <= 1), 'deepl_requests=' . $st3['deepl_requests'] . (isset($st1['deepl_requests']) ? ' (sebelumnya ' . $st1['deepl_requests'] . ')' : ''));

        // =====================================================
        echo "== 6. filter pengaman\n";
        // =====================================================
        $filtered = array(
            array('text' => 'Hubungi guru@labschoolcibubur.sch.id untuk info', 'tipe' => 'teks', 'bersama' => false),
            array('text' => 'Nomor telepon 0812-3456-7890 tersedia', 'tipe' => 'teks', 'bersama' => false),
            array('text' => 'NIS siswa 3098765001 valid', 'tipe' => 'teks', 'bersama' => false),
            array('text' => 'https://jacos.test:8080/ informasi', 'tipe' => 'teks', 'bersama' => false),
        );
        $this->translator->translate_strings($filtered, 'en-GB');
        $st4 = $this->translator->get_last_stats();
        $t('filter: 4 string terblokir', ($st4['dilewati_filter'] === 4), 'dilewati_filter=' . $st4['dilewati_filter']);
        $t('filter: tanpa jaringan', ($st4['deepl_requests'] === 0 && $st4['deepl_chars'] === 0));
        // bersama=true: digit tidak diblokir
        $b = array(array('text' => 'Deskripsi kegiatan tahun 20262027 semester 1', 'tipe' => 'teks', 'bersama' => true));
        $this->translator->translate_strings($b, 'en-GB');
        $st4b = $this->translator->get_last_stats();
        $t('filter: bersama=true digit tidak diblokir', ($st4b['dilewati_filter'] === 0), 'dilewati_filter=' . $st4b['dilewati_filter']);

        // =====================================================
        echo "== 7. teks panjang non-bersama tidak di-cache; bersama di-cache\n";
        // =====================================================
        $panjang = str_repeat('Deskripsi kegiatan pembelajaran siswa panjang. ', 20); // ~940 char
        if (!$doReal) {
            $t('long text: lewati (butuh DeepL real)', true, 'set flag deepl_real utk uji');
        } else {
            $args_long = array(array('text' => $panjang, 'tipe' => 'teks', 'bersama' => false));
            $res_long = $this->translator->translate_strings($args_long, 'en-GB');
            $h_long = translator_hash(translator_normalize($panjang), 'teks');
            $row_long = translator_safe_query("SELECT id_translation_cache FROM translation_cache WHERE hash_sumber = ?", array($h_long), 'row', false);
            $t('long non-bersama: tidak di-cache', (!is_object($row_long)), '');
            $args_long2 = array(array('text' => $panjang . ' versi bersama', 'tipe' => 'teks', 'bersama' => true));
            $this->translator->translate_strings($args_long2, 'en-GB');
            $h_long2 = translator_hash(translator_normalize($panjang . ' versi bersama'), 'teks');
            $row_long2 = translator_safe_query("SELECT id_translation_cache FROM translation_cache WHERE hash_sumber = ?", array($h_long2), 'row', false);
            $t('long bersama: di-cache', (is_object($row_long2)), '');
        }

        // =====================================================
        echo "== 8. html: tag utuh\n";
        // =====================================================
        $html = '<p>Deskripsi <b>kegiatan</b> sekolah</p>';
        if (!$doReal) {
            $t('html: lewati (butuh DeepL real)', true, '');
        } else {
            $res_html = $this->translator->translate_strings(array(array('text' => $html, 'tipe' => 'html', 'bersama' => true)), 'en-GB');
            $tags_asli = preg_match_all('/<\/?[a-z]+>/', $html);
            $tags_hasil = is_string($res_html[0]) ? preg_match_all('/<\/?[a-z]+>/', $res_html[0]) : 0;
            $t('html: jumlah tag sama', ($tags_hasil === $tags_asli), "asli=$tags_asli hasil=$tags_hasil");
            $t('html: tag p & b utuh', (is_string($res_html[0]) && strpos($res_html[0], '<p>') !== false && strpos($res_html[0], '<b>') !== false), substr((string)$res_html[0], 0, 80));
        }

        // =====================================================
        echo "== 9. kegagalan: URL salah + breaker\n";
        // =====================================================
        $url_pro_asli = translator_cfg('translator_deepl_url_pro');
        $url_free_asli = translator_cfg('translator_deepl_url_free');
        $this->config->set_item('translator_deepl_url_pro', 'https://invalid.deepl.test/v2/translate');
        $this->config->set_item('translator_deepl_url_free', 'https://invalid.deepl.test/v2/translate');
        $this->config->set_item('translator_breaker_fail_threshold', 2);
        $this->cache->save($this->config->item('translator_breaker_cache_key'), array('fails' => 0, 'open_until' => 0), 600);

        $res_fail = $this->translator->translate_strings(array(array('text' => 'Pengumuman ujian', 'tipe' => 'teks', 'bersama' => true)), 'en-GB');
        $t('gagal URL: teks asli, tanpa exception', ($res_fail[0] === 'Pengumuman ujian'), (string)$res_fail[0]);

        $res_fail2 = $this->translator->translate_strings(array(array('text' => 'Pengumuman ujian dua', 'tipe' => 'teks', 'bersama' => true)), 'en-GB');
        $t('breaker: terbuka setelah N gagal', (translator_breaker_is_open()), '');
        $st5 = $this->translator->get_last_stats();
        $t('gagal: stats asli_gagal>0', ($st5['asli_gagal'] >= 1), 'asli_gagal=' . $st5['asli_gagal']);
        // setelah breaker open: langsung fallback tanpa menunggu timeout
        $t0 = microtime(true);
        $this->translator->translate_strings(array(array('text' => 'Pengumuman ujian tiga', 'tipe' => 'teks', 'bersama' => true)), 'en-GB');
        $dt = microtime(true) - $t0;
        $t('breaker open: cepat tanpa timeout', ($dt < 0.5), sprintf('%.3fs', $dt));
        // pulihkan config + breaker (set_item eksplisit; config->load tidak me-reload)
        $this->config->set_item('translator_deepl_url_pro', $url_pro_asli);
        $this->config->set_item('translator_deepl_url_free', $url_free_asli);
        $this->cache->save($this->config->item('translator_breaker_cache_key'), array('fails' => 0, 'open_until' => 0), 600);

        // =====================================================
        echo "== 10. struktur response (key, tipe, urutan) via wildcard\n";
        // =====================================================
        $struk = array(
            'status' => 1,
            'message' => 'Berhasil ambil data',
            'data' => array(
                (object) array('judul' => 'Kegiatan Pramuka', 'nomor_va' => '9880019927110001', 'nested' => array('nama_kategori' => 'Kesiswaan')),
                (object) array('judul' => 'Pengumuman Libur', 'nomor_va' => null, 'nested' => array('nama_kategori' => 'Akademik')),
            ),
        );
        $before = var_export($struk, true);
        $this->translator->translate_response($struk, 'siswa/agenda/POST', 'en-GB');
        $same_keys = (
            isset($struk['data'][0]->judul, $struk['data'][0]->nomor_va)
            && property_exists($struk['data'][1], 'nested')
            && $struk['status'] === 1
        );
        $t('struktur: key/tipe utuh', ($same_keys && $struk['data'][0]->nomor_va === '9880019927110001'), 'nomor_va tak tersentuh');
        $after_order = array('status', 'message', 'data');
        $t('struktur: urutan key sama', (array_keys($struk) === $after_order));
        $t('struktur: stdClass tetap stdClass', (is_object($struk['data'][0])));

        // endpoint excluded & tak terdaftar: tidak berubah
        $ex = array('data' => array('instruksi' => 'WHO-5 instruksi'));
        $ex_before = $ex;
        $this->translator->translate_response($ex, 'root/mhcu/instrument_get', 'en-GB');
        $t('excluded: tidak berubah', ($ex == $ex_before));
        $nx = array('data' => array('judul' => 'Kegiatan Apa Saja'));
        $nx_before = $nx;
        $this->translator->translate_response($nx, 'siswa/tidak_ada/POST', 'en-GB');
        $t('unknown endpoint: tidak berubah', ($nx == $nx_before));

        // =====================================================
        echo "== 11. DeepL real (opsional, teks pendek)\n";
        // =====================================================
        if ($doReal) {
            $key = translator_env('DEEPL_API_KEY');
            $t('deepl: key ada di .env', ($key !== ''), 'suffix: ' . (substr($key, -3) === ':fx' ? 'free (:fx)' : 'pro'));
            $real = array(
                array('text' => 'Pengumuman libur sekolah', 'tipe' => 'teks', 'bersama' => true),
                array('text' => 'Fasilitas perpustakaan', 'tipe' => 'teks', 'bersama' => true),
            );
            $res_real = $this->translator->translate_strings($real, 'en-GB');
            $st6 = $this->translator->get_last_stats();
            $t('deepl real: ada hasil', (isset($res_real[0]) && is_string($res_real[0]) && $res_real[0] !== ''), substr((string)$res_real[0], 0, 60));
            $t('deepl real: 1 request (dedup+batch)', ($st6['deepl_requests'] === 1), 'requests=' . $st6['deepl_requests'] . ' chars=' . $st6['deepl_chars']);
            echo "   karakter terkirim ke DeepL: " . $st6['deepl_chars'] . PHP_EOL;
        } else {
            $t('deepl real: DILEWATI (jalankan dgn arg deepl_real)', true, '');
        }

        echo PHP_EOL . "TOTAL: $pass PASS, $fail FAIL" . PHP_EOL;
        exit($fail > 0 ? 1 : 0);
    }
}
