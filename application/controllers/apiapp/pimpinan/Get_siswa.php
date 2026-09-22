<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * --------------------------------------------------------------------------
 * Get Siswa (Pimpinan) — untuk Presensi Tadarus
 * --------------------------------------------------------------------------
 * GET /apiapp/pimpinan/get_siswa
 * List siswa aktif berdasarkan jenjang + kelas/tingkatan.
 * Pimpinan bisa akses semua jenjang.
 */
class Get_siswa extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('pimpinan_token');
    }

    private function _authenticate()
    {
        $headers = array();
        foreach (getallheaders() as $name => $value) {
            $headers[strtolower($name)] = $value;
        }
        $token = '';
        if (isset($headers['x-token'])) {
            $token = $headers['x-token'];
        } elseif (!empty($_SERVER['HTTP_X_TOKEN'])) {
            $token = $_SERVER['HTTP_X_TOKEN'];
        }
        return validate_pimpinan_token($token);
    }

    public function index_get()
    {
        $auth = $this->_authenticate();
        if (!$auth['valid']) {
            $this->response(array('status' => 0, 'message' => $auth['message']), 401);
            return;
        }

        $jenjang      = strtolower($this->get('jenjang'));
        $id_kelas     = $this->get('id_kelas');
        $id_tingkatan = $this->get('id_tingkatan');

        if (empty($jenjang)) {
            $this->response(array('status' => 0, 'message' => 'Parameter jenjang wajib diisi'), 400);
            return;
        }

        $siswa_tbl = get_siswa_table_name($jenjang);
        $siswa_id  = get_siswa_id_column($jenjang);
        $kelas_tbl = get_kelas_table_name($jenjang);
        $kelas_id  = get_kelas_id_column($jenjang);

        if (!$siswa_tbl || !$kelas_tbl) {
            $this->response(array('status' => 0, 'message' => 'Jenjang tidak valid'));
            return;
        }

        $where_extra = '';
        if (!empty($id_kelas)) {
            $where_extra = " AND s.id_kelas = " . (int) $id_kelas;
        } elseif (!empty($id_tingkatan)) {
            $where_extra = " AND s.id_kelas IN (SELECT {$kelas_id} FROM {$kelas_tbl} WHERE id_tingkatan = " . (int) $id_tingkatan . ")";
        }

        $sql = "SELECT
                    s.{$siswa_id} AS id_siswa_aktif,
                    s.nama_lengkap,
                    s.nis,
                    s.id_kelas,
                    k.label AS nama_kelas
                FROM {$siswa_tbl} s
                LEFT JOIN {$kelas_tbl} k ON k.{$kelas_id} = s.id_kelas
                WHERE s.deleted_at IS NULL AND s.is_active = 1
                {$where_extra}
                ORDER BY s.nama_lengkap ASC";

        $data = $this->mymodel->withquery($sql, 'result');

        $this->response(array(
            'status'  => 1,
            'message' => 'List siswa aktif',
            'data'    => $data,
        ));
    }
}
