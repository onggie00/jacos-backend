<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Translator/Translator_filter.php';
require_once __DIR__ . '/Translator/Translator_kamus.php';
require_once __DIR__ . '/Translator/Translator_cache.php';
require_once __DIR__ . '/Translator/Translator_whitelist.php';
require_once __DIR__ . '/Translator/Translator_deepl.php';
require_once __DIR__ . '/Translator/Translator_google.php';

/**
 * Translator — service utama switch bahasa (Fase 3; belum disambung ke endpoint).
 *
 * Urutan per string: normalisasi -> filter pengaman -> kamus -> override ->
 * cache -> DeepL (batch, dedup) -> Google (fallback opsional) -> teks asli.
 *
 * Kegagalan APA PUN: kembalikan teks asli, tidak melempar error, tidak
 * menampilkan halaman error CI3. Log ringkas (hash + jumlah karakter,
 * tanpa isi teks user, tanpa API key).
 *
 * PHP 7.4; MySQL 5.7; tanpa composer/SDK.
 */
class Translator {

    /** @var Translator_filter */
    public $filter;
    /** @var Translator_kamus */
    public $kamus;
    /** @var Translator_cache */
    public $cache;
    /** @var Translator_whitelist */
    public $whitelist;
    /** @var Translator_deepl */
    public $deepl;
    /** @var Translator_google */
    public $google;

    private $stats = array(
        'total_string'    => 0,
        'id_atau_unknown' => 0,
        'dilewati_filter' => 0,
        'dari_kamus'      => 0,
        'dari_override'   => 0,
        'dari_cache'      => 0,
        'dari_deepl'      => 0,
        'dari_google'     => 0,
        'asli_gagal'      => 0,
        'deepl_requests'  => 0,
        'deepl_chars'     => 0,
        'waktu_ms'        => 0,
    );

    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->config('translator');
        $CI->load->helper('translator_safe_db');
        $this->filter    = new Translator_filter();
        $this->kamus     = new Translator_kamus();
        $this->cache     = new Translator_cache();
        $this->whitelist = new Translator_whitelist();
        $this->deepl     = new Translator_deepl();
        $this->google    = new Translator_google();
    }

        private function reset_stats()
    {
        foreach ($this->stats as $k => $v) {
            $this->stats[$k] = 0;
        }
    }

    public function get_last_stats()
    {
        return $this->stats;
    }

    private function add($k, $n = 1)
    {
        $this->stats[$k] += $n;
    }

    private function perlu_translate($kode)
    {
        $kode = strtolower(trim((string) $kode));
        return ($kode !== '' && $kode !== 'id');
    }

    private function nama_bahasa($kode)
    {
        $CI =& get_instance();
        $CI->load->config('bahasa');
        $list = $CI->config->item('bahasa');
        return (is_array($list) && isset($list[$kode]['nama'])) ? $list[$kode]['nama'] : $kode;
    }

    private function target_lang($kode)
    {
        $CI =& get_instance();
        $CI->load->config('bahasa');
        $list = $CI->config->item('bahasa');
        return (is_array($list) && isset($list[$kode]['deepl'])) ? $list[$kode]['deepl'] : strtoupper($kode);
    }

    /* =====================================================
     *  translate_response — endpoint JSON dengan whitelist
     * ===================================================== */

    public function translate_response(&$data, $endpoint_key, $kode_bahasa)
    {
        $this->reset_stats();
        $t0 = microtime(true);
        $GLOBALS['_translator_t0'] = $t0;

        if (!$this->perlu_translate($kode_bahasa)) {
            $this->add('id_atau_unknown');
            $this->stats['waktu_ms'] = (int) ((microtime(true) - $t0) * 1000);
            return $data; // tanpa query, tanpa jaringan
        }

        $items = $this->whitelist->kumpulkan($data, $endpoint_key);
        if (empty($items)) {
            $this->stats['waktu_ms'] = (int) ((microtime(true) - $t0) * 1000);
            return $data; // endpoint tidak di whitelist / kosong — as-is persis
        }

        $payload = array();
        foreach ($items as $it) {
            $this->add('total_string');
            $payload[] = array(
                'text'    => $it['text'],
                'tipe'    => $it['tipe'],
                'bersama' => $it['bersama'],
                'refs'    => null,
            );
        }

        $hasil = $this->proses_items($payload, $kode_bahasa);

        // tulis balik berdasarkan path konkret (bukan referensi PHP)
        $this->whitelist->terapkan($data, $items, $hasil);

        $this->stats['waktu_ms'] = (int) ((microtime(true) - $t0) * 1000);
        return $data;
    }

    /* =====================================================
     *  translate_strings — daftar teks (push notification nanti)
     * ===================================================== */

    public function translate_strings(array $items, $kode_bahasa)
    {
        $this->reset_stats();
        $t0 = microtime(true);
        $GLOBALS['_translator_t0'] = $t0;

        if (!$this->perlu_translate($kode_bahasa)) {
            $this->add('id_atau_unknown', count($items));
            $this->stats['waktu_ms'] = (int) ((microtime(true) - $t0) * 1000);
            return $items;
        }

        $payload = array();
        foreach ($items as $it) {
            $this->add('total_string');
            $payload[] = array(
                'text'    => isset($it['text']) ? $it['text'] : '',
                'tipe'    => isset($it['tipe']) ? $it['tipe'] : 'teks',
                'bersama' => !empty($it['bersama']),
                'refs'    => null,
            );
        }
        $hasil = $this->proses_items($payload, $kode_bahasa);
        $this->stats['waktu_ms'] = (int) ((microtime(true) - $t0) * 1000);
        return $hasil; // urutan sama dgn input
    }

    /* =====================================================
     *  translate_message — field "message": HANYA kamus
     * ===================================================== */

    public function translate_message($teks, $kode_bahasa)
    {
        if (!$this->perlu_translate($kode_bahasa)) {
            return $teks;
        }
        try {
            $norm = translator_normalize($teks);
            $val = $this->kamus->get(mb_strtolower($norm, 'UTF-8'), $kode_bahasa);
            if ($val !== null && $val !== '') {
                $this->add('dari_kamus');
                return $val;
            }
        } catch (Throwable $e) {
            log_message('error', 'Translator: translate_message gagal: ' . substr($e->getMessage(), 0, 100));
        } catch (Exception $e) {
            log_message('error', 'Translator: translate_message gagal: ' . substr($e->getMessage(), 0, 100));
        }
        return $teks;
    }

    /* =====================================================
     *  Pipeline inti
     *  $payload[i] = array(text, tipe, bersama, refs|null)
     *  return list hasil per index (urut sama)
     * ===================================================== */

    private function proses_items(array $payload, $kode_bahasa)
    {
        $n = count($payload);
        $hasil = array();   // teks final per index (mulai = teks asli)
        $norms  = array();  // versi ternormalisasi utk hash/lookup
        $hashes = array();
        $sisa = array();    // index yang masih butuh override/cache/external

        // 1) normalisasi + filter + kamus
        foreach ($payload as $i => $p) {
            $teks_asli = (string) $p['text'];
            $hasil[$i] = $teks_asli;
            $norms[$i] = translator_normalize($teks_asli);
            $hashes[$i] = translator_hash($norms[$i], $p['tipe']);

            if ($norms[$i] === '' || $this->filter->murni_angka_tanggal($norms[$i])) {
                $this->add('dilewati_filter');
                continue;
            }
            if (!$this->filter->boleh_kirim($norms[$i], $p['tipe'], $p['bersama'])) {
                $this->add('dilewati_filter');
                continue;
            }

            if ($p['tipe'] === 'label') {
                $selesai = $this->coba_label($norms[$i], $kode_bahasa, $hasil, $i);
                if ($selesai) {
                    $this->add('dari_kamus');
                    continue;
                }
                // token tidak dikenal: jatuh ke jalur biasa dgn teks ternormalisasi utuh
                $sisa[] = $i;
                continue;
            }

            if ($p['tipe'] !== 'html') {
                $val = $this->kamus->get(mb_strtolower($norms[$i], 'UTF-8'), $kode_bahasa);
                if ($val !== null && $val !== '') {
                    $hasil[$i] = $val;
                    $this->add('dari_kamus');
                    continue;
                }
            }
            $sisa[] = $i;
        }

        // 2) override (jika tabel ada) + cache — batch per hash
        if (!empty($sisa)) {
            $semua_hash = array();
            foreach ($sisa as $i) {
                $semua_hash[$hashes[$i]] = true;
            }
            $semua_hash = array_keys($semua_hash);

            $override = $this->cache->ambil_override_banyak($kode_bahasa, $semua_hash);
            if (is_array($override)) {
                foreach ($sisa as $i) {
                    $h = $hashes[$i];
                    if (isset($override[$h]) && $override[$h] !== '') {
                        $hasil[$i] = $override[$h];
                        $this->add('dari_override');
                    }
                }
            }
        }

        $perlu_cache = array();
        foreach ($sisa as $i) {
            if ($hasil[$i] === (string) $payload[$i]['text']) {
                // belum diganti (kamus/override tidak kena)
                $perlu_cache[$hashes[$i]][] = $i;
            }
        }

        if (!empty($perlu_cache)) {
            $cache_rows = $this->cache->ambil_banyak($kode_bahasa, array_keys($perlu_cache));
            if ($cache_rows === false) {
                // translation_cache tidak bisa dibaca: JANGAN DeepL/Google, teks asli
                $this->add('asli_gagal', count($perlu_cache, COUNT_RECURSIVE) - count($perlu_cache));
                $out = array();
                foreach ($payload as $p) { $out[] = $p['text']; }
                return $out;
            }
            $hit_hash = array();
            foreach ($perlu_cache as $h => $idxs) {
                if (isset($cache_rows[$h]) && $cache_rows[$h] !== '') {
                    foreach ($idxs as $i) {
                        $hasil[$i] = $cache_rows[$h];
                    }
                    $this->add('dari_cache', count($idxs));
                    $hit_hash[] = $h;
                }
            }
            if (!empty($hit_hash)) {
                $this->cache->update_hit_banyak($kode_bahasa, $hit_hash);
            }
        }

        // 3) sisanya → DeepL (batch, dedup) → Google fallback → asli
        $belum = array(); // hash => list idx
        foreach ($perlu_cache as $h => $idxs) {
            if (!isset($cache_rows) || !isset($cache_rows[$h]) || $cache_rows[$h] === '') {
                $belum[$h] = $idxs;
            }
        }
        if (!empty($belum)) {
            $this->eksternal_batch($belum, $norms, $hasil, $payload, $kode_bahasa);
        }

        return $hasil;
    }

    /** Label komposit: split koma, translate token via kamus; TRUE jika semua ketemu. */
    private function coba_label($norm, $kode_bahasa, &$hasil, $i)
    {
        $parts = preg_split('/(,+)/', $norm, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if (!is_array($parts)) {
            return false;
        }
        $ok_semua = true;
        $out = array();
        foreach ($parts as $seg) {
            if ($seg !== '' && $seg[0] === ',') {
                $out[] = $seg; // pemisah dipertahankan persis
                continue;
            }
            $tok = trim($seg);
            if ($tok === '') {
                $out[] = $seg;
                continue;
            }
            $val = $this->kamus->get(mb_strtolower($tok, 'UTF-8'), $kode_bahasa);
            if ($val === null || $val === '') {
                return false; // token tidak dikenal → jalur biasa
            }
            $lead  = (strlen($seg) !== strlen(ltrim($seg))) ? ' ' : '';
            $trail = (strlen($seg) !== strlen(rtrim($seg))) ? ' ' : '';
            $out[] = $lead . $val . $trail;
        }
        $hasil[$i] = implode('', $out);
        return true;
    }

    /**
     * DeepL batch per tipe (html terpisah, tag_handling=html), dedup per hash,
     * retry/breaker di klien; fallback Google; gagal = teks asli.
     * $belum: hash => list index payload.
     */
    private function eksternal_batch(array $belum, array $norms, array &$hasil, array $payload, $kode_bahasa)
    {
        if (translator_cache_down()) {
            $this->add('asli_gagal', count($belum, COUNT_RECURSIVE) - count($belum));
            return; // cache tidak tersedia: tanpa DeepL/Google
        }

        // perlindungan kuota: string BARU melebihi batas -> kembalikan asli
        $max_uncached = (int) translator_cfg('translator_max_uncached_per_request', 150);
        $total_baru = 0;
        foreach ($belum as $idxs) {
            $total_baru += count($idxs);
        }
        if ($max_uncached > 0 && $total_baru > $max_uncached) {
            log_message('error', 'Translator: ' . $total_baru . ' string baru melebihi batas ' . $max_uncached . ' — dikembalikan asli');
            $this->add('asli_gagal', $total_baru);
            return;
        }

        if (translator_breaker_is_open()) {
            $this->google_fallback($belum, $norms, $hasil, $payload, $kode_bahasa);
            return;
        }

        $target = $this->target_lang($kode_bahasa);
        $bahasa_nama = $this->nama_bahasa($kode_bahasa);
        $short = (int) translator_cfg('translator_cache_short_chars', 500);

        $grup = array('teks' => array(), 'html' => array());
        foreach ($belum as $h => $idxs) {
            $tipe = ($payload[$idxs[0]]['tipe'] === 'html') ? 'html' : 'teks';
            $grup[$tipe][$h] = $idxs;
        }

        foreach ($grup as $tipe => $hash_map) {
            if (empty($hash_map)) {
                continue;
            }
            // text ternormalisasi (BUKAN hash); hash unik => teks unik per tipe
            $text_of_hash = array();
            $idxs_of_text = array();
            foreach ($hash_map as $h => $idxs) {
                $txt = $norms[$idxs[0]];
                $text_of_hash[$h] = $txt;
                $idxs_of_text[$txt] = $idxs;
            }
            $texts = array_keys($idxs_of_text);
            $chars = 0;
            foreach ($texts as $t) {
                $chars += strlen($t);
            }
            $this->add('deepl_chars', $chars);

            $budget = (float) translator_cfg('translator_time_budget', 3.0);
            $res = $this->deepl->translate_batch($texts, $target, ($tipe === 'html'), $budget);

            if (!is_array($res) || count($res) !== count($texts)) {
                $this->add('asli_gagal', count($texts));
                $this->google_fallback($hash_map, $norms, $hasil, $payload, $kode_bahasa);
                continue;
            }
            $this->add('deepl_requests');

            $simpan = array();
            $hash_by_text = array();
            foreach ($text_of_hash as $h => $txt) {
                $hash_by_text[$txt] = $h;
            }
            foreach ($texts as $idx => $txt) {
                $h = $hash_by_text[$txt];
                $teks_hasil = isset($res[$idx]) ? $res[$idx] : '';
                if ($teks_hasil === '') {
                    $this->add('asli_gagal', count($idxs_of_text[$txt]));
                    continue;
                }
                $this->add('dari_deepl', count($idxs_of_text[$txt]));
                foreach ($idxs_of_text[$txt] as $i) {
                    $hasil[$i] = $teks_hasil;
                }
                // cache: pendek selalu; bersama=true selalu; panjang non-bersama tidak
                $bersama_any = false;
                foreach ($idxs_of_text[$txt] as $i) {
                    if ($payload[$i]['bersama']) {
                        $bersama_any = true;
                        break;
                    }
                }
                if ($bersama_any || strlen($txt) <= $short) {
                    $simpan[] = array(
                        'hash'       => $h,
                        'teks_asal'  => $txt,
                        'teks_hasil' => $teks_hasil,
                        'bahasa'     => $bahasa_nama,
                        'kode'       => $kode_bahasa,
                    );
                }
            }
            if (!empty($simpan)) {
                $this->cache->simpan_banyak($simpan);
            }
        }
    }

    /** Google fallback per tipe teks; html → asli; gagal → asli. */
    private function google_fallback(array $belum, array $norms, array &$hasil, array $payload, $kode_bahasa)
    {
        if (translator_breaker_is_open()) {
            $this->add('asli_gagal', count($belum, COUNT_RECURSIVE) - count($belum));
            return;
        }
        if (translator_env('GOOGLE_TRANSLATE_API_KEY') === '') {
            $this->add('asli_gagal', count($belum, COUNT_RECURSIVE) - count($belum));
            return;
        }
        // pisah html (google dgn format=text saja di sini — html kembalikan asli)
        $teks_map = array();
        foreach ($belum as $h => $idxs) {
            if ($payload[$idxs[0]]['tipe'] === 'html') {
                $this->add('asli_gagal', count($idxs));
                continue;
            }
            $teks_map[$h] = $idxs;
        }
        if (empty($teks_map)) {
            return;
        }
        $texts = array();
        foreach ($teks_map as $h => $idxs) {
            $texts[] = $norms[$h];
        }
        $budget = (float) translator_cfg('translator_time_budget', 3.0);
        $res = $this->google->translate_batch($texts, $kode_bahasa, $budget);
        if (!is_array($res)) {
            $this->add('asli_gagal', count($teks_map, COUNT_RECURSIVE) - count($teks_map));
            return;
        }
        $idx = 0;
        foreach ($teks_map as $h => $idxs) {
            if (isset($res[$idx]) && $res[$idx] !== '') {
                $this->add('dari_google', count($idxs));
                foreach ($idxs as $i) {
                    $hasil[$i] = $res[$idx];
                }
            } else {
                $this->add('asli_gagal', count($idxs));
            }
            $idx++;
        }
    }
}
