<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * translator_apply_helper.php — SATU titik sambung translator ke response API.
 * Dipanggil dari REST_Controller::response() untuk data array pada HTTP 2xx.
 *
 * Urutan keluar-cepat (setiap langkah gagal = kembalikan $data asli, tanpa
 * query tambahan):
 *  1) translator_enabled FALSE -> asli
 *  2) endpoint_key tidak ada di translator_enabled_endpoints -> asli
 *  3) endpoint excluded / tidak ada di whitelist aktif -> asli
 *  4) identitas dari X-Token (helper per role; BUKAN payload) -> gagal = asli
 *  5) kode bahasa Setting_bahasa_model::get_kode_bahasa; 'id' -> asli
 *  6) translate_response (whitelist) + translate_message (kamus saja)
 *  7) struktur identik
 *
 * Endpoint lain TIDAK dikenai biaya apa pun (tanpa query token/bahasa).
 * Semua kegagalan: data asli, tanpa exception ke luar.
 */

require_once APPPATH . 'helpers/translator_safe_db_helper.php';

if (!function_exists('translator_coverage_log')) {

    /**
     * Telemetri cakupan (opsional; config translator_coverage_log, default FALSE).
     * Satu baris per request ke endpoint yang DIAKTIFKAN, ke file
     * application/logs/translator-coverage-YYYY-MM-DD.log:
     *   endpoint_key|token_ada(0/1)|token_dikenal(0/1)|x_lang_ada(0/1)|bahasa|diterjemahkan(0/1)|sumber(token/x-lang/none)
     * TIDAK memuat token, email, IP, atau isi apa pun. Gagal menulis = diam
     * (tidak memengaruhi response). Sengaja TIDAK memakai log_message karena
     * log_threshold=1 membuang level info.
     */
    function translator_coverage_log($endpoint_key, $token_ada, $token_kenal, $x_lang_ada, $bahasa, $diterjemahkan, $sumber)
    {
        if (!translator_cfg('translator_coverage_log', false)) {
            return;
        }
        $line = $endpoint_key . '|' . (int) (bool) $token_ada . '|' . (int) (bool) $token_kenal . '|'
            . (int) (bool) $x_lang_ada . '|' . ($bahasa !== '' ? $bahasa : '-') . '|'
            . (int) (bool) $diterjemahkan . '|' . $sumber . "\n";
        @file_put_contents(APPPATH . 'logs/translator-coverage-' . date('Y-m-d') . '.log', $line, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('translator_apply_to_response')) {

    function translator_apply_to_response($data, $http_code)
    {
        $CI =& get_instance();
        $CI->load->config('translator'); // pastikan item translator_* termuat

        // 1) kill switch
        if (!translator_cfg('translator_enabled', false)) {
            return $data;
        }
        if (!is_array($data)) {
            return $data;
        }

        // 2) endpoint pilot
        $enabled = translator_cfg('translator_enabled_endpoints', array());
        if (empty($enabled) || !is_array($enabled)) {
            return $data;
        }

        // endpoint_key dari URI: directory (role), class, HTTP method
        $directory = trim((string) $CI->router->directory, '/');
        $controller = strtolower($CI->router->class);
        if ($directory !== 'apiapp' && strpos($directory, 'apiapp/') !== 0) {
            return $data; // bukan apiapp
        }
        $role = ($directory === 'apiapp') ? 'root' : substr($directory, strlen('apiapp/'));
        $http = strtoupper($CI->input->method(false)); // GET|POST
        $endpoint_key = $role . '/' . $controller . '/' . $http;

        if (!in_array($endpoint_key, $enabled, true)) {
            log_message('debug', 'TR hook: key=' . $endpoint_key . ' tidak di daftar pilot');
            return $data;
        }

        // 3) whitelist aktif & bukan excluded (double check murah)
        $CI->load->library('Translator');
        if ($CI->translator->whitelist->endpoint_aktif($endpoint_key) === null) {
            return $data;
        }

        // 4) identitas dari token (helper yang sudah ada; tidak pernah payload)
        $token = '';
        if (!empty($_SERVER['HTTP_X_TOKEN'])) {
            $token = $_SERVER['HTTP_X_TOKEN'];
        }
        if ($token === '' && function_exists('getallheaders')) {
            foreach (getallheaders() as $hk => $hv) {
                if (strtolower($hk) === 'x-token') {
                    $token = trim($hv);
                    break;
                }
            }
        }
        $valid = false;
        $email = '';
        if ($token !== '') {
            if ($role === 'guru') {
                if (!function_exists('validate_guru_token')) {
                    require APPPATH . 'helpers/guru_token_helper.php';
                }
                $auth = validate_guru_token($token, translator_cfg('translator_language_check_expiry', false));
                $valid = !empty($auth['valid']);
                if ($valid) { $email = $auth['data']->email_ms_office; }
            } elseif ($role === 'pimpinan') {
                if (!function_exists('validate_pimpinan_token')) {
                    require APPPATH . 'helpers/pimpinan_token_helper.php';
                }
                $auth = validate_pimpinan_token($token, translator_cfg('translator_language_check_expiry', false));
                $valid = !empty($auth['valid']);
                if ($valid) { $email = $auth['data']->email_ms_office; }
            } elseif (in_array($role, array('siswa', 'ortu', 'pegawai'), true)) {
                if (!function_exists('validate_akun_bahasa_token')) {
                    require APPPATH . 'helpers/bahasa_token_helper.php';
                }
                $auth = validate_akun_bahasa_token($token, $role, translator_cfg('translator_language_check_expiry', false));
                $valid = !empty($auth['valid']);
                if ($valid) { $email = $auth['email']; }
            }
        }
        if (!$valid || $email === '') {
            // token tidak ada / tidak valid -> bahasa cadangan via header X-Lang
            // HANYA utk path bersama=TRUE, bila translator_allow_lang_header aktif.
            $kode_hdr = '';
            $x_lang_ada = !empty($_SERVER['HTTP_X_LANG']);
            if (!$x_lang_ada && function_exists('getallheaders')) {
                foreach (getallheaders() as $hk => $hv) {
                    if (strtolower($hk) === 'x-lang') { $x_lang_ada = true; break; }
                }
            }
            $CI->load->config('bahasa'); // diperlukan utk validasi kode X-Lang
            if (translator_cfg('translator_allow_lang_header', false)) {
                $hdr = '';
                if (!empty($_SERVER['HTTP_X_LANG'])) $hdr = trim($_SERVER['HTTP_X_LANG']);
                if ($hdr === '' && $x_lang_ada && function_exists('getallheaders')) {
                    foreach (getallheaders() as $hk => $hv) {
                        if (strtolower($hk) === 'x-lang') { $hdr = trim($hv); break; }
                    }
                }
                $bahasa_cfg = $CI->config->item('bahasa');
                if ($hdr !== '' && $hdr !== 'id' && is_array($bahasa_cfg) && isset($bahasa_cfg[$hdr])) {
                    $kode_hdr = $hdr;
                }
            }
            // spesifikasi endpoint harus punya path bersama=true; kalau tidak, asli
            $spec = $CI->translator->whitelist->endpoint_aktif($endpoint_key);
            $ada_bersama = false;
            if (is_array($spec)) {
                foreach ($spec as $meta) {
                    if (!empty($meta['bersama'])) { $ada_bersama = true; break; }
                }
            }
            if ($kode_hdr === '' || !$ada_bersama) {
                translator_coverage_log($endpoint_key, $token !== '', $valid, $x_lang_ada, '', false, 'none');
                return $data; // response asli (Indonesia)
            }
            $data = $CI->translator->translate_response($data, $endpoint_key, $kode_hdr, true);
            translator_coverage_log($endpoint_key, $token !== '', $valid, $x_lang_ada, $kode_hdr, true, 'x-lang');
            return $data;
        }
        // 5) kode bahasa (memo per request di model)
        $CI->load->model('Setting_bahasa_model');
        $kode = $CI->Setting_bahasa_model->get_kode_bahasa($email, $role);
        if ($kode === 'id' || $kode === '' || $kode === null) {
            translator_coverage_log($endpoint_key, $token !== '', $valid, false, 'id', false, 'token');
            return $data;
        }

        // 6) translate (whitelist) + message (kamus saja)
        $data = $CI->translator->translate_response($data, $endpoint_key, $kode);
        if (isset($data['message']) && is_string($data['message'])) {
            $data['message'] = $CI->translator->translate_message($data['message'], $kode);
        }
        translator_coverage_log($endpoint_key, $token !== '', $valid, false, $kode, true, 'token');

        // F) debug headers (angka saja)
        if (translator_cfg('translator_debug_headers', false)) {
            $st = $CI->translator->get_last_stats();
            $CI->output->set_header('X-Translate-Lang: ' . $kode);
            $CI->output->set_header('X-Translate-Stats: s=' . (int) $st['total_string']
                . ';kamus=' . (int) $st['dari_kamus']
                . ';override=' . (int) $st['dari_override']
                . ';cache=' . (int) $st['dari_cache']
                . ';deepl=' . (int) $st['dari_deepl']
                . ';google=' . (int) $st['dari_google']
                . ';filter=' . (int) $st['dilewati_filter']
                . ';deepl_req=' . (int) $st['deepl_requests']
                . ';deepl_chars=' . (int) $st['deepl_chars']
                . ';ms=' . (int) $st['waktu_ms']);
        }

        return $data;
    }
}

