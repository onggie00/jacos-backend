<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Translator_cache_export — paket ekspor translation_cache + override
 * utk SATU endpoint, diimpor manual ke PRODUKSI via phpMyAdmin.
 * CLI ONLY (HTTP 404). Tidak memanggil DeepL; hanya membaca cache lokal.
 *
 * Pemakaian:
 *   php tool/run_cli.php tools/translator_cache_export/index/endpoint/root/visi_misi/GET/lang/en-GB
 *   opsi: out-dir/Berkas_sample/export  (nilai greedy, default Berkas_sample/export)
 *
 * Hanya baris yang hash-nya DIHITUNG ULANG dari string path whitelist aktif
 * bersama=true milik endpoint itu (bukan seluruh cache). Hanya konten
 * bersama=true yang diekspor (tanpa data pribadi).
 *
 * Hasil 3 berkas di --out-dir:
 *   (a) <endpoint>_<lang>_cache.sql    INSERT IGNORE, batch 100 baris, idempoten
 *   (b) <endpoint>_<lang>_override.sql INSERT ... ON DUPLICATE KEY UPDATE, idempoten
 *   (c) <endpoint>_<lang>_review.csv   UTF-8 + BOM utk ditinjau sebelum impor
 *
 * Kompatibel MySQL 5.7 (aman diimpor 2x — tidak menggandakan baris).
 */
class Translator_cache_export extends MX_Controller {

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
        $args = translator_cli_args(
            array_slice($this->uri->segment_array(), 3),
            array('endpoint', 'lang', 'out-dir'),
            array('endpoint', 'out-dir')
        );
        $endpoint = isset($args['endpoint']) ? $args['endpoint'] : '';
        $lang = isset($args['lang']) ? $args['lang'] : '';
        $outDir = isset($args['out-dir']) && $args['out-dir'] !== '' ? $args['out-dir'] : 'Berkas_sample/export';

        if ($endpoint === '' || $lang === '') {
            exit("pemakaian: tools/translator_cache_export/index/endpoint/<kunci>/lang/<kode> [out-dir/<path>]\n");
        }
        $bahasa = $this->config->item('bahasa');
        if (!isset($bahasa[$lang]) || $lang === 'id') {
            exit("lang tidak valid: {$lang}\n");
        }

        // endpoint harus di whitelist aktif & punya path bersama=true
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

        // 1) string sumber (dihitung ulang, bukan dibaca dari cache)
        $queries = translator_endpoint_sources($endpoint);
        if (empty($queries)) {
            exit("endpoint ini tidak dipetakan di helper sumber (translator_sources_helper): {$endpoint}\n");
        }
        $f = translator_fetch_strings($queries);
        if (empty($f['strings'])) {
            echo "TIDAK ADA string ditemukan utk endpoint {$endpoint}"
                . ($f['err'] ? ' (tabel sumber kosong / tidak terbaca di database ini)' : '')
                . " — tidak ada yang diekspor.\n";
            return;
        }
        $hashes = array(); // hash => array('teks'=>, 'tipe'=>)
        foreach ($f['strings'] as $txt => $tipe) {
            $h = translator_hash($txt, $tipe);
            $hashes[$h] = array('teks' => $txt, 'tipe' => $tipe);
        }

        // 2) ambil baris cache & override aktif yang hash-nya cocok SAJA
        $cacheRows = $this->_fetch_rows(
            translator_cfg('translator_cache_table', 'translation_cache'),
            'hash_sumber, teks_asal, teks_hasil, kode_bahasa',
            $lang, array_keys($hashes));
        $ovRows = $this->_fetch_rows(
            translator_cfg('translator_override_table', 'translation_override'),
            'hash_sumber, teks_asal, teks_hasil, kode_bahasa, catatan',
            $lang, array_keys($hashes), 'aktif = 1');

        $nama = str_replace('/', '_', $endpoint) . '_' . $lang;
        if (!is_dir($outDir) && !mkdir($outDir, 0775, true)) {
            exit("gagal membuat folder output: {$outDir}\n");
        }

        // 3a) cache.sql
        $sqlCache = array();
        $sqlCache[] = '-- ============================================================';
        $sqlCache[] = '-- Ekspor translation_cache (Translator_cache_export)';
        $sqlCache[] = '-- endpoint  : ' . $endpoint;
        $sqlCache[] = '-- bahasa    : ' . $lang;
        $sqlCache[] = '-- jumlah    : ' . count($cacheRows) . ' baris (hash cocok string whitelist bersama=true)';
        $sqlCache[] = '-- waktu     : ' . date('Y-m-d H:i:s');
        $sqlCache[] = '-- Idempoten (INSERT IGNORE). Impor via phpMyAdmin. MySQL 5.7.';
        $sqlCache[] = '-- ============================================================';
        $sqlCache[] = 'SET NAMES utf8mb4;';
        $batas = 100; $i = 0;
        $batch = array();
        foreach ($cacheRows as $r) {
            $batch[] = '(' . $this->_esc($r->hash_sumber)
                . ', ' . $this->_esc($lang)
                . ', ' . $this->_esc($r->kode_bahasa)
                . ', ' . $this->_esc($r->teks_asal)
                . ', ' . $this->_esc($r->teks_hasil)
                . ', NOW(), NULL, 0)';
            $i++;
            if ($i % $batas === 0) {
                $sqlCache[] = $this->_insert_cache($batch);
                $batch = array();
            }
        }
        if (!empty($batch)) $sqlCache[] = $this->_insert_cache($batch);
        $sqlCache[] = '';
        $sqlCache[] = '-- verifikasi (harus = jumlah baris di header):';
        $sqlCache[] = 'SELECT COUNT(*) AS jumlah_cache FROM `translation_cache` WHERE `kode_bahasa` = ' . $this->_esc($lang)
            . ' AND `hash_sumber` IN (' . $this->_hash_list(array_keys($hashes)) . ');';
        $fileCache = rtrim($outDir, '/\\') . '/' . $nama . '_cache.sql';
        $this->_write($fileCache, implode("\n", $sqlCache) . "\n");

        // 3b) override.sql
        $sqlOv = array();
        $sqlOv[] = '-- ============================================================';
        $sqlOv[] = '-- Ekspor translation_override AKTIF (Translator_cache_export)';
        $sqlOv[] = '-- endpoint  : ' . $endpoint;
        $sqlOv[] = '-- bahasa    : ' . $lang;
        $sqlOv[] = '-- jumlah    : ' . count($ovRows) . ' baris (hash cocok string whitelist bersama=true)';
        $sqlOv[] = '-- waktu     : ' . date('Y-m-d H:i:s');
        $sqlOv[] = '-- Idempoten (ON DUPLICATE KEY UPDATE). created_by/updated_by NULL.';
        $sqlOv[] = '-- ============================================================';
        $sqlOv[] = 'SET NAMES utf8mb4;';
        foreach ($ovRows as $r) {
            $sqlOv[] = 'INSERT INTO `translation_override` (`hash_sumber`, `kode_bahasa`, `teks_asal`, `teks_hasil`, `aktif`, `catatan`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES ('
                . $this->_esc($r->hash_sumber)
                . ', ' . $this->_esc($r->kode_bahasa)
                . ', ' . $this->_esc($r->teks_asal)
                . ', ' . $this->_esc($r->teks_hasil)
                . ', 1, ' . (isset($r->catatan) && $r->catatan !== null ? $this->_esc($r->catatan) : 'NULL')
                . ', NOW(), NULL, NULL, NULL)'
                . ' ON DUPLICATE KEY UPDATE `teks_hasil` = VALUES(`teks_hasil`), `aktif` = 1,'
                . ' `catatan` = VALUES(`catatan`), `updated_at` = NOW(), `updated_by` = NULL;';
        }
        $sqlOv[] = '';
        $sqlOv[] = '-- verifikasi (harus = jumlah baris di header):';
        $sqlOv[] = 'SELECT COUNT(*) AS jumlah_override FROM `translation_override` WHERE `kode_bahasa` = ' . $this->_esc($lang)
            . ' AND `hash_sumber` IN (' . $this->_hash_list(array_keys($hashes)) . ');';
        $fileOv = rtrim($outDir, '/\\') . '/' . $nama . '_override.sql';
        $this->_write($fileOv, implode("\n", $sqlOv) . "\n");

        // 3c) review.csv (UTF-8 + BOM)
        $csv = "\xEF\xBB\xBF" . "tipe,kode_bahasa,teks_asal,teks_hasil,sumber,panjang,catatan\n";
        $nCsv = 0;
        foreach ($cacheRows as $r) {
            $tipe = isset($hashes[$r->hash_sumber]) ? $hashes[$r->hash_sumber]['tipe'] : '';
            $csv .= $this->_csv_line(array($tipe, $r->kode_bahasa, $r->teks_asal, $r->teks_hasil, 'cache',
                (string) mb_strlen($r->teks_asal, 'UTF-8'), ''));
            $nCsv++;
        }
        foreach ($ovRows as $r) {
            $tipe = isset($hashes[$r->hash_sumber]) ? $hashes[$r->hash_sumber]['tipe'] : '';
            $csv .= $this->_csv_line(array($tipe, $r->kode_bahasa, $r->teks_asal, $r->teks_hasil, 'override',
                (string) mb_strlen($r->teks_asal, 'UTF-8'), isset($r->catatan) ? $r->catatan : ''));
            $nCsv++;
        }
        $fileCsv = rtrim($outDir, '/\\') . '/' . $nama . '_review.csv';
        $this->_write($fileCsv, $csv);

        echo "=== ekspor selesai: {$endpoint} lang={$lang} ===\n";
        echo "string whitelist : " . count($hashes) . " hash (bersama=true, hash dihitung ulang)\n";
        echo "cache.sql        : " . count($cacheRows) . " baris -> {$fileCache}\n";
        echo "override.sql     : " . count($ovRows) . " baris -> {$fileOv}\n";
        echo "review.csv       : {$nCsv} baris -> {$fileCsv}\n";
        echo "urutan impor phpMyAdmin: cache.sql dulu, lalu override.sql.\n";
    }

    /* ===================== helper ===================== */

    private function _fetch_rows($tabel, $kolom, $lang, array $hashes, $where_tambahan = '')
    {
        if (empty($hashes)) return array();
        $sql = 'SELECT ' . $kolom . ' FROM `' . $tabel . '` WHERE `kode_bahasa` = ? AND `hash_sumber` IN ('
            . implode(',', array_fill(0, count($hashes), '?')) . ')'
            . ($where_tambahan !== '' ? ' AND ' . $where_tambahan : '');
        $rows = translator_safe_query($sql, array_merge(array($lang), $hashes), 'result', false);
        return is_array($rows) ? $rows : array();
    }

    private function _esc($teks)
    {
        return "'" . $this->db->escape_str($teks) . "'";
    }

    private function _insert_cache(array $batch)
    {
        return 'INSERT IGNORE INTO `translation_cache` (`hash_sumber`, `bahasa`, `kode_bahasa`, `teks_asal`, `teks_hasil`, `created_at`, `last_used_at`, `hit_count`) VALUES '
            . implode(', ', $batch) . ';';
    }

    private function _hash_list(array $hashes)
    {
        $out = array();
        foreach ($hashes as $h) $out[] = "'" . $this->db->escape_str($h) . "'";
        return implode(', ', $out);
    }

    private function _csv_line(array $fields)
    {
        $out = array();
        foreach ($fields as $v) {
            $v = (string) $v;
            $v = str_replace("\r\n", "\n", $v);
            if (strpos($v, '"') !== false || strpos($v, ',') !== false || strpos($v, "\n") !== false) {
                $v = '"' . str_replace('"', '""', $v) . '"';
            }
            $out[] = $v;
        }
        return implode(',', $out) . "\n";
    }

    private function _write($file, $isi)
    {
        if (file_put_contents($file, $isi) === false) {
            exit("gagal menulis berkas: {$file}\n");
        }
    }
}
