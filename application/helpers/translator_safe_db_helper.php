<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper untuk akses DB service translator — pola sama dengan
 * Setting_bahasa_model::_safe_query (db_debug dimatikan sementara, error
 * dicatat, fallback dikembalikan, pulih di finally). Dibuat sebagai helper
 * agar Setting_bahasa_model tidak disentuh sama sekali.
 *
 * Semua fungsi MURNI read/INSERT IGNORE/UPDATE-hunt terbatas ke
 * translation_cache (dan translation_override bila ada). Tanpa DDL.
 */

if (!function_exists('translator_safe_query')) {
    /**
     * @param string $sql  SQL dengan placeholder "?"
     * @param array  $binds parameter
     * @param string $mode  'row' | 'result' | 'write'
     * @param mixed  $fallback nilai saat gagal
     * @return mixed
     */
    function translator_safe_query($sql, $binds = array(), $mode = 'row', $fallback = null)
    {
        $CI =& get_instance();
        $prev = isset($CI->db->db_debug) ? $CI->db->db_debug : false;

        try {
            $CI->db->db_debug = false;
            $query = $CI->db->query($sql, $binds);

            if ($query === false || ($query === true && $mode !== 'write')) {
                $err = $CI->db->error();
                $msg = isset($err['message']) ? trim($err['message']) : '';
                // ringkas: jangan catat isi teks user
                log_message('error', 'Translator: query gagal (' . substr($msg, 0, 120) . ')');
                return $fallback;
            }

            if ($mode === 'write') {
                return true;
            }
            return ($mode === 'row') ? $query->row() : $query->result();
        } catch (Throwable $e) {
            log_message('error', 'Translator: exception ' . get_class($e) . ': ' . substr($e->getMessage(), 0, 120));
            return $fallback;
        } catch (Exception $e) {
            log_message('error', 'Translator: exception: ' . substr($e->getMessage(), 0, 120));
            return $fallback;
        } finally {
            $CI->db->db_debug = $prev;
        }
    }
}

if (!function_exists('translator_load_env')) {
    /**
     * Parser .env mini (proyek tidak punya loader .env; file berisi
     * baris KEY=VALUE tanpa kutip). Hasil di-memo static.
     * TIDAK PERNAH menulis nilai ke log.
     *
     * @param string $path
     * @return array key => value (tanpa menampilkan nilai di log)
     */
    function translator_load_env($path)
    {
        static $memo = null;
        if ($memo !== null) {
            return $memo;
        }
        $memo = array();
        if (!is_file($path)) {
            return $memo;
        }
        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($lines)) {
            return $memo;
        }
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                continue;
            }
            list($k, $v) = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v);
            // buang kutip pembungkus bila ada
            $len = strlen($v);
            if ($len >= 2 && (($v[0] === '"' && $v[$len - 1] === '"') || ($v[0] === "'" && $v[$len - 1] === "'"))) {
                $v = substr($v, 1, -1);
            }
            if ($k !== '') {
                $memo[$k] = $v;
            }
        }
        return $memo;
    }
}

if (!function_exists('translator_env')) {
    /** Ambil satu variabel .env (atau getenv() sebagai cadangan). */
    function translator_env($key, $default = '')
    {
        $env = translator_load_env(APPPATH . '../.env');
        if (isset($env[$key]) && $env[$key] !== '') {
            return $env[$key];
        }
        $v = getenv($key);
        return ($v !== false && $v !== '') ? $v : $default;
    }
}

if (!function_exists('translator_normalize')) {
    /**
     * Normalisasi sebelum hash/lookup: trim, 
 -> 
, spasi ganda dirapikan,
     * NFC bila ekstensi intl tersedia.
     */
    function translator_normalize($teks)
    {
        $t = (string) $teks;
        $t = str_replace("
", "
", $t);
        $t = preg_replace('/ {2,}/', ' ', $t);
        $t = trim($t);
        if (function_exists('normalizer_is_normalized') && !normalizer_is_normalized($t, normalizer::FORM_C)) {
            $t = normalizer_normalize($t, normalizer::FORM_C);
        }
        return $t;
    }
}

if (!function_exists('translator_hash')) {
    /** md5(prefix_tipe + teks ternormalisasi) — prefix html beda agar tidak bentrok. */
    function translator_hash($teks_ternormalisasi, $tipe)
    {
        $prefix = ($tipe === 'html') ? 'html:' : 'teks:';
        return md5($prefix . $teks_ternormalisasi);
    }
}

if (!function_exists('translator_breaker_is_open')) {
    /** TRUE bila breaker terbuka (jangan panggil penyedia eksternal). */
    function translator_breaker_is_open()
    {
        $CI =& get_instance();
        $CI->load->config('translator');
        $key = $CI->config->item('translator_breaker_cache_key');
        $CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        $st = $CI->cache->get($key);
        if (!is_array($st)) {
            return false;
        }
        if (!empty($st['open_until']) && $st['open_until'] > time()) {
            return true;
        }
        return false;
    }
}

if (!function_exists('translator_breaker_register_fail')) {
    /** Catat satu kegagalan; buka breaker bila threshold tercapai. */
    function translator_breaker_register_fail($http_code)
    {
        $CI =& get_instance();
        $CI->load->config('translator');
        $key = $CI->config->item('translator_breaker_cache_key');
        $thr = (int) $CI->config->item('translator_breaker_fail_threshold');
        if ($thr <= 0) {
            $thr = 3;
        }
        $secs = (int) $CI->config->item('translator_breaker_open_seconds');
        if ($secs <= 0) {
            $secs = 60;
        }
        $CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        $st = $CI->cache->get($key);
        $fails = (is_array($st) && isset($st['fails'])) ? (int) $st['fails'] : 0;
        $fails++;
        if ($fails >= $thr) {
            $CI->cache->save($key, array('fails' => 0, 'open_until' => time() + $secs), $secs + 60);
            log_message('error', 'Translator: breaker OPEN ' . $secs . 's setelah ' . $fails . ' kegagalan (http terakhir ' . $http_code . ')');
        } else {
            $CI->cache->save($key, array('fails' => $fails, 'open_until' => 0), 600);
        }
    }
}

if (!function_exists('translator_breaker_open')) {
    /** Buka breaker manual (kuota habis / key salah). */
    function translator_breaker_open($seconds)
    {
        $CI =& get_instance();
        $CI->load->config('translator');
        $key = $CI->config->item('translator_breaker_cache_key');
        $CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        $CI->cache->save($key, array('fails' => 0, 'open_until' => time() + (int) $seconds), (int) $seconds + 60);
    }
}

if (!function_exists('translator_cfg')) {
    /** Baca item config translator_* (file config/translator.php memuat item top-level). */
    function translator_cfg($key, $default = null)
    {
        $CI =& get_instance();
        $v = $CI->config->item($key);
        return ($v === null) ? $default : $v;
    }
}

if (!function_exists('translator_cache_down')) {
    /**
     * Flag per-request: translation_cache tidak bisa dibaca/tulis.
     * translator_cache_down(TRUE) untuk menandai; log SEKALI (tanpa isi teks).
     */
    function translator_cache_down($set = null)
    {
        static $down = false;
        if ($set === true) {
            if (!$down) {
                $down = true;
                log_message('error', 'Translator: translation_cache tidak tersedia — skip DeepL/Google, kembalikan teks asli');
            }
        }
        return $down;
    }
}

if (!function_exists('translator_neg_cache_has')) {
    /** TRUE bila hash string sedang di negative cache (DeepL baru saja gagal utk teks ini). */
    function translator_neg_cache_has($hash)
    {
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        return (bool) $CI->cache->get('translator_neg_' . $hash);
    }
}

if (!function_exists('translator_neg_cache_set')) {
    /** Tandai hash gagal DeepL selama N menit (tanpa menyimpan teks). */
    function translator_neg_cache_set($hash, $minutes)
    {
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        $CI->cache->save('translator_neg_' . $hash, 1, max(1, (int) $minutes) * 60);
    }
}
