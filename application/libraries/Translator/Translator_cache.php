<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Akses translation_cache (hanya INSERT IGNORE + SELECT + UPDATE hit_count)
 * dan translation_override (SELECT, tabel opsional). Pola DB aman via
 * helper translator_safe_query (db_debug sementara OFF).
 */
class Translator_cache {

    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->config('translator');
    }

    /** @return array hash => teks_hasil */
    public function ambil_banyak($kode_bahasa, array $hashes)
    {
        if (empty($hashes)) {
            return array();
        }
        $hashes = array_values(array_unique($hashes));
        $rows = translator_safe_query(
            "SELECT hash_sumber, teks_hasil FROM " . translator_cfg('translator_cache_table', 'translation_cache') .
            " WHERE kode_bahasa = ? AND hash_sumber IN (" .
            implode(',', array_fill(0, count($hashes), '?')) . ")",
            array_merge(array($kode_bahasa), $hashes),
            'result',
            false
        );
        if ($rows === false || !is_array($rows)) {
            translator_cache_down(true); // DB error -> caller skip DeepL/Google
            return false;
        }
        $out = array();
        foreach ($rows as $r) {
            $out[$r->hash_sumber] = $r->teks_hasil;
        }
        return $out;
    }

    /**
     * INSERT IGNORE batch. $pairs: list array('hash' =>, 'teks_asal' =>,
     * 'teks_hasil' =>, 'bahasa' =>, 'kode' =>). Pemanggil memfilter panjang.
     */
    public function simpan_banyak($pairs)
    {
        if (empty($pairs)) {
            return false;
        }
        $ok = true;
        foreach ($pairs as $p) {
            $res = translator_safe_query(
                "INSERT IGNORE INTO " . translator_cfg('translator_cache_table', 'translation_cache') .
                " (hash_sumber, bahasa, kode_bahasa, teks_asal, teks_hasil, created_at, last_used_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                array($p['hash'], $p['bahasa'], $p['kode'], $p['teks_asal'], $p['teks_hasil']),
                'write',
                false
            );
            if ($res !== true) {
                $ok = false;
                translator_cache_down(true);
            }
        }
        return $ok;
    }

    /** Satu query batch: hit_count+1 hanya jika last_used_at NULL / > refresh_hours. */
    public function update_hit_banyak($kode_bahasa, array $hashes)
    {
        if (empty($hashes)) {
            return false;
        }
        $hashes = array_values(array_unique($hashes));
        return translator_safe_query(
            "UPDATE " . translator_cfg('translator_cache_table', 'translation_cache') .
            " SET hit_count = hit_count + 1, last_used_at = NOW() " .
            "WHERE kode_bahasa = ? AND hash_sumber IN (" . implode(',', array_fill(0, count($hashes), '?')) . ") " .
            "AND (last_used_at IS NULL OR last_used_at < NOW() - INTERVAL " .
            (int) translator_cfg('translator_cache_hit_refresh_hours', 24) . " HOUR)",
            array_merge(array($kode_bahasa), $hashes),
            'write',
            false
        );
    }

    /** Override: hash => teks_hasil. false = tabel tidak ada / error. */
    public function ambil_override_banyak($kode_bahasa, array $hashes)
    {
        if (empty($hashes)) {
            return array();
        }
        $hashes = array_values(array_unique($hashes));
        $rows = translator_safe_query(
            "SELECT hash_sumber, teks_hasil FROM " . translator_cfg('translator_override_table', 'translation_override') .
            " WHERE kode_bahasa = ? AND aktif = 1 AND hash_sumber IN (" .
            implode(',', array_fill(0, count($hashes), '?')) . ")",
            array_merge(array($kode_bahasa), $hashes),
            'result',
            false
        );
        if ($rows === false || !is_array($rows)) {
            return false; // tabel belum ada / error — caller lanjut tanpa override
        }
        $out = array();
        foreach ($rows as $r) {
            $out[$r->hash_sumber] = $r->teks_hasil;
        }
        return $out;
    }
}
