<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * --------------------------------------------------------------------------
 * Presensi Pramuka (Pimpinan) — Riwayat
 * --------------------------------------------------------------------------
 * GET /apiapp/pimpinan/presensi_pramuka_riwayat
 */
class Presensi_pramuka_riwayat extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('pimpinan_token');
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
        return validate_pimpinan_token($token, FALSE);
    }

    public function index_get()
    {
        $auth = $this->_authenticate();
        if (!$auth['valid']) {
            $this->response(array('status' => 0, 'message' => $auth['message']), 401);
            return;
        }

        $start_date = $this->get('start_date');
        $end_date   = $this->get('end_date');
        $jenjang    = strtolower($this->get('jenjang'));
        $tingkatan  = $this->get('tingkatan');
        $kelas      = $this->get('kelas');
        $limit      = $this->get('limit') ? (int) $this->get('limit') : 20;
        $offset     = $this->get('offset') ? (int) $this->get('offset') : 0;

        $where = "WHERE p.deleted_at IS NULL";

        if (!empty($jenjang)) {
            $where .= " AND p.jenjang = '" . strtoupper($jenjang) . "'";
        }
        if (!empty($start_date)) {
            $where .= " AND p.tanggal >= '" . $this->db->escape_str($start_date) . "'";
        }
        if (!empty($end_date)) {
            $where .= " AND p.tanggal <= '" . $this->db->escape_str($end_date) . "'";
        }
        if (!empty($kelas) && !empty($jenjang)) {
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
        if (!empty($tingkatan) && !empty($jenjang)) {
            $tingkatan_tbl = get_tingkatan_table_name($jenjang);
            $tingkatan_id_col = get_tingkatan_id_column($jenjang);
            if ($tingkatan_tbl && $tingkatan_id_col) {
                $tingkatan_row = $this->mymodel->withquery(
                    "SELECT label FROM {$tingkatan_tbl} WHERE {$tingkatan_id_col} = " . (int) $tingkatan . " LIMIT 1",
                    'row'
                );
                if (!empty($tingkatan_row)) {
                    $where .= " AND p.kelas LIKE '" . $this->db->escape_str($tingkatan_row->label) . "%'";
                }
            }
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
                {$where}
                ORDER BY p.tanggal DESC, p.id_presensi_pramuka DESC
                LIMIT {$limit} OFFSET {$offset}";

        $rows = $this->mymodel->withquery($sql, 'result');

        $data = array();
        foreach ($rows as $row) {
            $predikat = get_predikat_pramuka($row->total_nilai);
            $row->predikat  = $predikat['predikat'];
            $row->deskripsi = $predikat['deskripsi'];
            $data[] = $row;
        }

        $count_sql = "SELECT COUNT(*) AS cnt FROM presensi_pramuka p {$where}";
        $total_row = $this->mymodel->withquery($count_sql, 'row');
        $total     = !empty($total_row) ? (int) $total_row->cnt : 0;

        $this->response(array(
            'status'  => 1,
            'message' => 'Riwayat presensi pramuka berhasil diambil',
            'data'    => $data,
            'meta'    => array(
                'total'  => $total,
                'limit'  => $limit,
                'offset' => $offset,
            )
        ));
    }
}
