<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * --------------------------------------------------------------------------
 * Presensi Pramuka — Detail
 * --------------------------------------------------------------------------
 * GET /apiapp/guru/presensi_pramuka_detail?id_presensi_pramuka=123
 */
class Presensi_pramuka_detail extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('guru_token');
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
        return validate_guru_token($token, FALSE);
    }

    public function index_get()
    {
        $auth = $this->_authenticate();
        if (!$auth['valid']) {
            $this->response(array('status' => 0, 'message' => $auth['message']), 401);
            return;
        }

        $guru_jenjang = $auth['jenjang'];
        $id = (int) $this->get('id_presensi_pramuka');

        if (empty($id)) {
            $this->response(array('status' => 0, 'message' => 'Parameter id_presensi_pramuka wajib diisi'), 400);
            return;
        }

        $sql = "SELECT
                    p.id_presensi_pramuka,
                    p.id_siswa_aktif,
                    p.nama_lengkap,
                    p.kelas,
                    p.jenjang,
                    p.status_hadir,
                    p.kehadiran,
                    p.status_kelengkapan,
                    p.kelengkapan,
                    p.status_keaktifan,
                    p.keaktifan,
                    p.total_nilai,
                    p.updated_by,
                    p.tanggal,
                    p.hari,
                    (CASE LOWER(p.jenjang)
                        WHEN 'sd'  THEN (SELECT nis FROM siswa_sd_aktif  WHERE id_siswa_sd_aktif  = p.id_siswa_aktif LIMIT 1)
                        WHEN 'smp' THEN (SELECT nis FROM siswa_smp_aktif WHERE id_siswa_smp_aktif = p.id_siswa_aktif LIMIT 1)
                        WHEN 'sma' THEN (SELECT nis FROM siswa_sma_aktif WHERE id_siswa_sma_aktif = p.id_siswa_aktif LIMIT 1)
                        WHEN 'ft'  THEN (SELECT nis FROM siswa_ft_aktif  WHERE id_siswa_ft_aktif  = p.id_siswa_aktif LIMIT 1)
                        ELSE ''
                    END) AS nis
                FROM presensi_pramuka p
                WHERE p.id_presensi_pramuka = " . $id . "
                AND p.deleted_at IS NULL
                LIMIT 1";

        $data = $this->mymodel->withquery($sql, 'row');

        if (empty($data)) {
            $this->response(array('status' => 0, 'message' => 'Data presensi pramuka tidak ditemukan'), 404);
            return;
        }

        if (!validate_guru_jenjang($guru_jenjang, $data->jenjang)) {
            $this->response(array('status' => 0, 'message' => 'Anda tidak memiliki akses untuk data jenjang ' . $data->jenjang), 403);
            return;
        }

        $predikat = get_predikat_pramuka($data->total_nilai);
        $data->predikat  = $predikat['predikat'];
        $data->deskripsi = $predikat['deskripsi'];

        $this->response(array(
            'status'  => 1,
            'message' => 'Detail presensi pramuka berhasil diambil',
            'data'    => $data,
        ));
    }
}
