<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * --------------------------------------------------------------------------
 * Presensi Tadarus — Riwayat
 * --------------------------------------------------------------------------
 * GET /apiapp/guru/presensi_tadarus_riwayat
 * Riwayat presensi tadarus per siswa (1 record = 1 siswa).
 */
class Presensi_tadarus_riwayat extends REST_Controller
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

        $guru         = $auth['data'];
        $guru_jenjang = $auth['jenjang'];

        $start_date = $this->get('start_date');
        $end_date   = $this->get('end_date');
        $jenjang    = strtolower($this->get('jenjang'));
        $tingkatan  = $this->get('tingkatan');
        $kelas      = $this->get('kelas');
        $limit      = $this->get('limit') ? (int) $this->get('limit') : 20;
        $offset     = $this->get('offset') ? (int) $this->get('offset') : 0;

        if (empty($jenjang)) {
            $jenjang = $guru_jenjang;
        }

        if (!validate_guru_jenjang($guru_jenjang, $jenjang)) {
            $this->response(array('status' => 0, 'message' => 'Anda tidak memiliki akses untuk jenjang ' . strtoupper($jenjang)), 403);
            return;
        }

        $where = "WHERE p.deleted_at IS NULL AND p.jenjang = '" . strtoupper($jenjang) . "'";

        if (!empty($start_date)) {
            $where .= " AND p.tanggal >= '" . $this->db->escape_str($start_date) . "'";
        }
        if (!empty($end_date)) {
            $where .= " AND p.tanggal <= '" . $this->db->escape_str($end_date) . " 23:59:59'";
        }
        if (!empty($kelas)) {
            $kelas_tbl = get_kelas_table_name($jenjang);
            $kelas_id_col = get_kelas_id_column($jenjang);
            if ($kelas_tbl && $kelas_id_col) {
                $kelas_row = $this->mymodel->withquery(
                    "SELECT label FROM {$kelas_tbl} WHERE {$kelas_id_col} = " . (int) $kelas . " LIMIT 1",
                    'row'
                );
                if (!empty($kelas_row)) {
                    $where .= " AND p.kelas = '" . $this->db->escape_str($kelas_row->label) . "'";
                }
            }
        }
        if (!empty($tingkatan)) {
            $kelas_tbl = get_kelas_table_name($jenjang);
            $where .= " AND p.kelas IN (SELECT label FROM {$kelas_tbl} WHERE id_tingkatan = " . (int) $tingkatan . ")";
        }

        $sql = "SELECT
                    p.id_presensi_tadarus,
                    p.tanggal,
                    p.hari,
                    p.jenjang,
                    p.id_siswa_aktif,
                    p.nama_lengkap,
                    p.kelas,
                    p.status_hadir,
                    p.kehadiran,
                    p.kelengkapan,
                    p.adab,
                    p.keaktifan,
                    p.total_nilai,
                    p.updated_by,
                    (CASE LOWER(p.jenjang)
                        WHEN 'sd'  THEN (SELECT nis FROM siswa_sd_aktif  WHERE id_siswa_sd_aktif  = p.id_siswa_aktif LIMIT 1)
                        WHEN 'smp' THEN (SELECT nis FROM siswa_smp_aktif WHERE id_siswa_smp_aktif = p.id_siswa_aktif LIMIT 1)
                        WHEN 'sma' THEN (SELECT nis FROM siswa_sma_aktif WHERE id_siswa_sma_aktif = p.id_siswa_aktif LIMIT 1)
                        WHEN 'ft'  THEN (SELECT nis FROM siswa_ft_aktif  WHERE id_siswa_ft_aktif  = p.id_siswa_aktif LIMIT 1)
                    END) AS nis
                FROM presensi_tadarus p
                {$where}
                ORDER BY p.tanggal DESC, p.nama_lengkap ASC
                LIMIT {$offset}, {$limit}";

        $data = $this->mymodel->withquery($sql, 'result');

        if (!empty($data)) {
            foreach ($data as &$row) {
                $predikat = get_predikat_tadarus($row->total_nilai);
                $row->predikat  = $predikat['predikat'];
                $row->deskripsi = $predikat['deskripsi'];
            }
        }

        $count_sql = "SELECT COUNT(*) AS cnt FROM presensi_tadarus p {$where}";
        $count_row = $this->mymodel->withquery($count_sql, 'row');
        $total = !empty($count_row) ? (int) $count_row->cnt : 0;

        $this->response(array(
            'status'  => 1,
            'message' => 'Riwayat presensi tadarus',
            'data'    => $data,
            'total'   => $total,
            'limit'   => $limit,
            'offset'  => $offset,
        ));
    }
}
