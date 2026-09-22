<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper untuk validasi token pimpinan
 * Digunakan di API pimpinan (application/controllers/apiapp/pimpinan/)
 */

if (!function_exists('validate_pimpinan_token')) {
    /**
     * Validasi X-Token header, cari di 4 tabel pimpinan (ft→sma→smp→sd).
     * Return array('valid'=>bool, 'data'=>object|NULL, 'jenjang'=>string, 'message'=>string)
     * 
     * @param string $token Nilai X-Token dari header
     * @return array
     */
    function validate_pimpinan_token($token, $check_expiry = TRUE)
    {
        $CI =& get_instance();
        $result = array(
            'valid'   => FALSE,
            'data'    => NULL,
            'jenjang' => '',
            'message' => ''
        );

        if (empty($token)) {
            $result['message'] = 'Token tidak ditemukan';
            return $result;
        }

        // Prioritas cek: SMA → SMP → SD (pimpinan tidak ada FT)
        $tables = array('sma', 'smp', 'sd');
        foreach ($tables as $j) {
            $tbl = 'pimpinan_' . $j;
            $data = $CI->mymodel->withquery(
                "SELECT * FROM {$tbl} WHERE token = '" . $CI->db->escape_str($token) . "' AND deleted_at IS NULL",
                'row'
            );
            if (!empty($data)) {
                // Cek expired (skip jika $check_expiry = FALSE)
                if ($check_expiry) {
                    if (!empty($data->token_expired) && $data->token_expired != '0000-00-00 00:00:00') {
                        if (date('Y-m-d H:i:s', strtotime($data->token_expired)) < date('Y-m-d H:i:s')) {
                            $result['message'] = 'Token expired';
                            return $result;
                        }
                    }
                }
                $result['valid']   = TRUE;
                $result['data']    = $data;
                $result['jenjang'] = $j;
                $result['message'] = 'Valid';
                return $result;
            }
        }

        $result['message'] = 'Token tidak valid';
        return $result;
    }
}
