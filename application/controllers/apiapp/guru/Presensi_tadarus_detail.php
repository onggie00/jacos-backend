<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * --------------------------------------------------------------------------
 * Presensi Tadarus — Detail
 * --------------------------------------------------------------------------
 * GET /apiapp/guru/presensi_tadarus_detail?id_presensi_tadarus=123
 * Detail satu data presensi tadarus berdasarkan ID.
 */
class Presensi_tadarus_detail extends REST_Controller
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
        $id = (int) $this->get('id_presensi_tadarus');

        if (empty($id)) {
            $this->response(array('status' => 0, 'message' => 'Parameter id_presensi_tadarus wajib diisi'), 400);
            return;
        }

        $sql = "SELECT
                    p.id_presensi_tadarus,
                    p.id_siswa_aktif,
                    p.nama_lengkap,
                    p.kelas,
                    p.jenjang,
                    p.status_hadir,
                    p.kehadiran,
                    p.kelengkapan,
                    p.adab,
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
                    END) AS nis
                FROM presensi_tadarus p
                WHERE p.id_presensi_tadarus = " . $id . "
                  AND p.deleted_at IS NULL";

        $row = $this->mymodel->withquery($sql, 'row');

        if (empty($row)) {
            $this->response(array('status' => 0, 'message' => 'Data tidak ditemukan'), 404);
            return;
        }

        // Security: verify guru has access to this record's jenjang
        if (!validate_guru_jenjang($guru_jenjang, $row->jenjang)) {
            $this->response(array('status' => 0, 'message' => 'Anda tidak memiliki akses untuk data ini'), 403);
            return;
        }

        $predikat = get_predikat_tadarus($row->total_nilai);
        $row->predikat  = $predikat['predikat'];
        $row->deskripsi = $predikat['deskripsi'];

        $this->response(array(
            'status'  => 1,
            'message' => 'Detail presensi tadarus',
            'data'    => $row,
        ));
    }
}
