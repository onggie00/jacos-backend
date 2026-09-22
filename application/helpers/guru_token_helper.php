<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper untuk validasi token guru
 * Digunakan di API guru (application/controllers/apiapp/guru/ dan application/controllers/guru/)
 */

if (!function_exists('validate_guru_token')) {
    /**
     * Validasi X-Token header, cari di 4 tabel guru (ft→sma→smp→sd).
     * Return array('valid'=>bool, 'data'=>object|NULL, 'jenjang'=>string, 'message'=>string)
     * 
     * @param string $token Nilai X-Token dari header
     * @return array
     */
    function validate_guru_token($token, $check_expiry = TRUE)
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

        // Prioritas cek: FT → SMA → SMP → SD (konsisten Login.php)
        $tables = array('ft', 'sma', 'smp', 'sd');
        foreach ($tables as $j) {
            $tbl = 'guru_' . $j;
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

if (!function_exists('validate_guru_jenjang')) {
    /**
     * Cek apakah guru boleh akses jenjang tertentu.
     * Aturan: guru_sma boleh akses FT juga.
     * 
     * @param string $guru_jenjang Jenjang asal guru (dari validate_guru_token)
     * @param string $target_jenjang Jenjang yang ingin diakses
     * @return bool
     */
    function validate_guru_jenjang($guru_jenjang, $target_jenjang)
    {
        $guru_jenjang   = strtolower($guru_jenjang);
        $target_jenjang = strtolower($target_jenjang);

        // Guru SMA boleh akses SMA dan FT
        if ($guru_jenjang == 'sma' && in_array($target_jenjang, array('sma', 'ft'))) {
            return TRUE;
        }

        // Guru lain hanya boleh akses jenjang sendiri
        return ($guru_jenjang === $target_jenjang);
    }
}

if (!function_exists('get_siswa_table_name')) {
    /**
     * Return nama tabel siswa_aktif berdasarkan jenjang.
     * 
     * @param string $jenjang
     * @return string|false
     */
    function get_siswa_table_name($jenjang)
    {
        $map = array(
            'sd'  => 'siswa_sd_aktif',
            'smp' => 'siswa_smp_aktif',
            'sma' => 'siswa_sma_aktif',
            'ft'  => 'siswa_ft_aktif',
        );
        $j = strtolower($jenjang);
        return isset($map[$j]) ? $map[$j] : FALSE;
    }
}

if (!function_exists('get_siswa_id_column')) {
    /**
     * Return nama kolom PK siswa_aktif berdasarkan jenjang.
     * 
     * @param string $jenjang
     * @return string|false
     */
    function get_siswa_id_column($jenjang)
    {
        $map = array(
            'sd'  => 'id_siswa_sd_aktif',
            'smp' => 'id_siswa_smp_aktif',
            'sma' => 'id_siswa_sma_aktif',
            'ft'  => 'id_siswa_ft_aktif',
        );
        $j = strtolower($jenjang);
        return isset($map[$j]) ? $map[$j] : FALSE;
    }
}

if (!function_exists('get_kelas_table_name')) {
    /**
     * Return nama tabel kelas berdasarkan jenjang.
     * 
     * @param string $jenjang
     * @return string|false
     */
    function get_kelas_table_name($jenjang)
    {
        $map = array(
            'sd'  => 'kelas_sd',
            'smp' => 'kelas_smp',
            'sma' => 'kelas_sma',
            'ft'  => 'kelas_ft',
        );
        $j = strtolower($jenjang);
        return isset($map[$j]) ? $map[$j] : FALSE;
    }
}

if (!function_exists('get_tingkatan_table_name')) {
    /**
     * Return nama tabel tingkatan berdasarkan jenjang.
     * 
     * @param string $jenjang
     * @return string|false
     */
    function get_tingkatan_table_name($jenjang)
    {
        $map = array(
            'sd'  => 'tingkatan_sd',
            'smp' => 'tingkatan_smp',
            'sma' => 'tingkatan_sma',
            'ft'  => 'tingkatan_ft',
        );
        $j = strtolower($jenjang);
        return isset($map[$j]) ? $map[$j] : FALSE;
    }
}

if (!function_exists('get_kelas_id_column')) {
    /**
     * Return nama kolom PK kelas berdasarkan jenjang.
     * 
     * @param string $jenjang
     * @return string|false
     */
    function get_kelas_id_column($jenjang)
    {
        $map = array(
            'sd'  => 'id_kelas_sd',
            'smp' => 'id_kelas_smp',
            'sma' => 'id_kelas_sma',
            'ft'  => 'id_kelas_ft',
        );
        $j = strtolower($jenjang);
        return isset($map[$j]) ? $map[$j] : FALSE;
    }
}

if (!function_exists('get_tingkatan_id_column')) {
    /**
     * Return nama kolom PK tingkatan berdasarkan jenjang.
     * 
     * @param string $jenjang
     * @return string|false
     */
    function get_tingkatan_id_column($jenjang)
    {
        $map = array(
            'sd'  => 'id_tingkatan_sd',
            'smp' => 'id_tingkatan_smp',
            'sma' => 'id_tingkatan_sma',
            'ft'  => 'id_tingkatan_ft',
        );
        $j = strtolower($jenjang);
        return isset($map[$j]) ? $map[$j] : FALSE;
    }
}

if (!function_exists('hitung_total_nilai_tadarus')) {
    /**
     * Hitung total_nilai berdasarkan 4 aspek.
     * Formula: (kehadiran/4*100)*0.4 + (kelengkapan/4*100)*0.3 + (adab/4*100)*0.2 + (keaktifan/4*100)*0.1
     * 
     * @param int $kehadiran 1-4
     * @param int $kelengkapan 1-4
     * @param int $adab 1-4
     * @param int $keaktifan 1-4
     * @return string total_nilai dengan 2 desimal
     */
    function hitung_total_nilai_tadarus($kehadiran, $kelengkapan, $adab, $keaktifan)
    {
        $kehadiran   = (int) $kehadiran;
        $kelengkapan = (int) $kelengkapan;
        $adab        = (int) $adab;
        $keaktifan   = (int) $keaktifan;

        $total = ($kehadiran / 4 * 100) * 0.4
               + ($kelengkapan / 4 * 100) * 0.3
               + ($adab / 4 * 100) * 0.2
               + ($keaktifan / 4 * 100) * 0.1;

        return number_format($total, 2, '.', '');
    }
}

if (!function_exists('get_predikat_tadarus')) {
    /**
     * Return array(predikat, deskripsi) dari total_nilai.
     * 
     * @param float $total_nilai
     * @return array('predikat'=>string, 'deskripsi'=>string)
     */
    function get_predikat_tadarus($total_nilai)
    {
        $total = (float) $total_nilai;

        if ($total >= 90) {
            return array('predikat' => 'A', 'deskripsi' => 'Sangat Baik');
        } elseif ($total >= 80) {
            return array('predikat' => 'B', 'deskripsi' => 'Baik');
        } elseif ($total >= 70) {
            return array('predikat' => 'C', 'deskripsi' => 'Cukup');
        } elseif ($total >= 60) {
            return array('predikat' => 'D', 'deskripsi' => 'Perlu Bimbingan');
        } else {
            return array('predikat' => 'E', 'deskripsi' => 'Perlu Pembinaan Intensif');
        }
    }
}

if (!function_exists('get_nama_hari_indo')) {
    /**
     * Return nama hari dalam bahasa Inggris dari tanggal.
     * 
     * @param string $tanggal YYYY-MM-DD
     * @return string nama hari (Senin, Selasa, dst)
     */
    function get_nama_hari_indo($tanggal)
    {
        $hari_en = date('l', strtotime($tanggal));
        $map = array(
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        );
        return isset($map[$hari_en]) ? $map[$hari_en] : $hari_en;
    }
}

if (!function_exists('hitung_total_nilai_pjok')) {
    /**
     * Hitung total_nilai PJOK berdasarkan 2 aspek (0-100).
     * Formula: (kehadiran + keaktifan) / 2
     *
     * @param int $kehadiran 0-100
     * @param int $keaktifan 0-100
     * @return string total_nilai dengan 2 desimal
     */
    function hitung_total_nilai_pjok($kehadiran, $keaktifan)
    {
        $kehadiran = (int) $kehadiran;
        $keaktifan = (int) $keaktifan;

        $total = ($kehadiran + $keaktifan) / 2;

        return number_format($total, 2, '.', '');
    }
}

if (!function_exists('get_predikat_pjok')) {
    /**
     * Return array(predikat, deskripsi) dari total_nilai PJOK.
     *
     * @param float $total_nilai
     * @return array('predikat'=>string, 'deskripsi'=>string)
     */
    function get_predikat_pjok($total_nilai)
    {
        $total = (float) $total_nilai;

        if ($total >= 90) {
            return array('predikat' => 'A', 'deskripsi' => 'Sangat Baik');
        } elseif ($total >= 80) {
            return array('predikat' => 'B', 'deskripsi' => 'Baik');
        } elseif ($total >= 70) {
            return array('predikat' => 'C', 'deskripsi' => 'Cukup');
        } elseif ($total >= 60) {
            return array('predikat' => 'D', 'deskripsi' => 'Perlu Bimbingan');
        } else {
            return array('predikat' => 'E', 'deskripsi' => 'Perlu Pembinaan Intensif');
        }
    }
}

if (!function_exists('hitung_total_nilai_pramuka')) {
    /**
     * Hitung total_nilai Presensi Pramuka berdasarkan 3 aspek (0-100).
     * Formula: (kehadiran + kelengkapan + keaktifan) / 3
     *
     * @param int $kehadiran 0-100
     * @param int $kelengkapan 0-100
     * @param int $keaktifan 0-100
     * @return string total_nilai dengan 2 desimal
     */
    function hitung_total_nilai_pramuka($kehadiran, $kelengkapan, $keaktifan)
    {
        $kehadiran   = (int) $kehadiran;
        $kelengkapan = (int) $kelengkapan;
        $keaktifan   = (int) $keaktifan;

        $total = ($kehadiran + $kelengkapan + $keaktifan) / 3;

        return number_format($total, 2, '.', '');
    }
}

if (!function_exists('get_predikat_pramuka')) {
    /**
     * Return array(predikat, deskripsi) dari total_nilai Pramuka.
     *
     * @param float $total_nilai
     * @return array('predikat'=>string, 'deskripsi'=>string)
     */
    function get_predikat_pramuka($total_nilai)
    {
        $total = (float) $total_nilai;

        if ($total >= 90) {
            return array('predikat' => 'A', 'deskripsi' => 'Sangat Baik');
        } elseif ($total >= 80) {
            return array('predikat' => 'B', 'deskripsi' => 'Baik');
        } elseif ($total >= 70) {
            return array('predikat' => 'C', 'deskripsi' => 'Cukup');
        } elseif ($total >= 60) {
            return array('predikat' => 'D', 'deskripsi' => 'Perlu Bimbingan');
        } else {
            return array('predikat' => 'E', 'deskripsi' => 'Perlu Pembinaan Intensif');
        }
    }
}

