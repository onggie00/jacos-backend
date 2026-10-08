<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper untuk validasi token apiapp siswa / ortu / pegawai + hook default
 * bahasa saat login. Pattern mengikuti guru_token_helper.php dan
 * pimpinan_token_helper.php (return array('valid','data','jenjang','message')).
 *
 * Digunakan di:
 * - application/controllers/apiapp/siswa/Setting_bahasa.php
 * - application/controllers/apiapp/ortu/Setting_bahasa.php
 * - application/controllers/apiapp/pegawai/Setting_bahasa.php
 * - application/controllers/apiapp/(siswa|ortu|pegawai)/Login*.php (hook default bahasa)
 *
 * Guru & pimpinan sudah punya helper sendiri:
 * guru_token_helper.php, pimpinan_token_helper.php.
 *
 * Urutan prioritas tabel mengikuti login masing-masing role:
 * - siswa : siswa_ft -> siswa_sma -> siswa_smp -> siswa_sd (token, deleted_at)
 * - ortu  : siswa_ft -> siswa_sma -> siswa_smp -> siswa_sd (token_ortu, deleted_at_ortu)
 * - pegawai : pegawai (token, deleted_at)
 * Siswa TK/KB tidak lewat login API (abaikan).
 */

if (!function_exists('akun_bahasa_tables')) {
    /**
     * Kumpulan tabel per role untuk pencarian token, urutan mengikuti login.
     * Kembalikan array: array(nama_tabel, kolom_token, kolom_token_expired, kolom_delete, kolom_email)
     */
    function akun_bahasa_tables($role)
    {
        $jenjang = array('ft', 'sma', 'smp', 'sd');
        $out = array();
        if ($role == 'siswa') {
            foreach ($jenjang as $j) {
                $out[] = array('siswa_' . $j, 'token', 'token_expired', 'deleted_at', 'email_ms_office');
            }
        } elseif ($role == 'ortu') {
            foreach ($jenjang as $j) {
                $out[] = array('siswa_' . $j, 'token_ortu', 'token_expired_ortu', 'deleted_at_ortu', 'email_ms_office_ortu');
            }
        } elseif ($role == 'pegawai') {
            $out[] = array('pegawai', 'token', 'token_expired', 'deleted_at', 'email_ms_office');
        }
        return $out;
    }
}

if (!function_exists('validate_akun_bahasa_token')) {
    /**
     * Validasi X-Token untuk role siswa|ortu|pegawai.
     * Return array('valid'=>bool, 'data'=>object|NULL, 'jenjang'=>string, 'email'=>string, 'role'=>string, 'message'=>string)
     *
     * Untuk role ortu, 'email' adalah email_ms_office_ortu (email akun ortu yang login).
     * Untuk pegawai jenjang = ''. Untuk siswa jenjang = ft|sma|smp|sd.
     *
     * @param string $token nilai X-Token dari header
     * @param string $role  siswa|ortu|pegawai
     * @param bool $check_expiry
     * @return array
     */
    function validate_akun_bahasa_token($token, $role, $check_expiry = TRUE)
    {
        $CI =& get_instance();
        $result = array(
            'valid'   => FALSE,
            'data'    => NULL,
            'jenjang' => '',
            'email'   => '',
            'role'    => $role,
            'message' => ''
        );

        if (empty($token)) {
            $result['message'] = 'Token tidak ditemukan';
            return $result;
        }

        foreach (akun_bahasa_tables($role) as $t) {
            list($tbl, $col_token, $col_expired, $col_deleted, $col_email) = $t;

            $sql = "SELECT * FROM {$tbl} WHERE {$col_token} = ? AND {$col_deleted} IS NULL LIMIT 1";


            $data = $CI

->mymodel

->withquery

($sql, 'row', array

($token

)

);
            if (!empty($data)) {
                if ($check_expiry) {
                    if (empty($data->{$col_expired})
                        || $data->{$col_expired} == '0000-00-00 00:00:00'
                        || date('Y-m-d H:i:s', strtotime($data->{$col_expired})) < date('Y-m-d H:i:s')) {
                        $result['message'] = 'Token expired';
                        return $result;
                    }
                }
                if ($role == 'ortu') {
                    // jenjang siswa tergantung tabel asal
                    $jenjang = substr($tbl, 6);
                } else {
                    $jenjang = '';
                }
                $result['valid']   = TRUE;
                $result['data']    = $data;
                $result['jenjang'] = $jenjang;
                $result['email']   = $data->{$col_email};
                $result['message'] = 'Valid';
                return $result;
            }
        }

        $result['message'] = 'Token tidak valid';
        return $result;
    }
}

if (!function_exists('bahasa_default_after_login')) {
    /**
     * Hook default bahasa (best effort) di titik login sukses.
     * Kegagalan TIDAK boleh menggagalkan login — semua exception ditelan
     * dan hanya di-log.
     *
     * @param array $accounts daftar akun: array(array('email'=>..., 'role'=>..., 'jenjang'=>...), ...)
     */
    function bahasa_default_after_login($accounts = array())
    {
        try {
            $CI =& get_instance();
            $CI->load->model('Setting_bahasa_model');
            foreach ($accounts as $acc) {
                if (empty($acc['email']) || empty($acc['role'])) {
                    continue;
                }
                $jenjang = isset($acc['jenjang']) ? $acc['jenjang'] : null;
                $CI->Setting_bahasa_model->get_or_create_default($acc['email'], $acc['role'], $jenjang);
            }
        } catch (Exception $e) {
            log_message('error', 'bahasa_default_after_login gagal: ' . $e->getMessage());
        }
    }
}
