<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * --------------------------------------------------------------------------
 * Presensi PJOK — Simpan Batch
 * --------------------------------------------------------------------------
 * POST /apiapp/guru/presensi_pjok
 * Simpan presensi + nilai PJOK untuk multi siswa (1 kelas, 1 tanggal).
 */
class Presensi_pjok extends REST_Controller
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

    public function index_post()
    {
        $auth = $this->_authenticate();
        if (!$auth['valid']) {
            $this->response(array('status' => 0, 'message' => $auth['message']), 401);
            return;
        }

        $guru         = $auth['data'];
        $guru_jenjang = $auth['jenjang'];

        $tanggal    = $this->post('tanggal');
        $jenjang    = strtolower($this->post('jenjang'));
        $kelas      = $this->post('kelas');
        $siswa_data = $this->post('siswa');

        if (empty($tanggal) || empty($jenjang) || empty($kelas) || empty($siswa_data)) {
            $this->response(array('status' => 0, 'message' => 'Parameter tanggal, jenjang, kelas, dan siswa wajib diisi'));
            return;
        }

        if (!validate_guru_jenjang($guru_jenjang, $jenjang)) {
            $this->response(array('status' => 0, 'message' => 'Anda tidak memiliki akses untuk jenjang ' . strtoupper($jenjang)), 403);
            return;
        }

        if (!is_array($siswa_data)) {
            $this->response(array('status' => 0, 'message' => 'Parameter siswa harus berupa array'));
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

        $hari = get_nama_hari_indo($tanggal);
        $updated_by = $guru->nama_lengkap . ' (' . strtoupper($guru_jenjang) . ')';
        $results = array();

        foreach ($siswa_data as $s) {
            $id_siswa_aktif = isset($s['id_siswa_aktif']) ? (int) $s['id_siswa_aktif'] : 0;
            $status_hadir   = isset($s['status_hadir']) ? $s['status_hadir'] : 'Hadir';

            if (empty($id_siswa_aktif)) {
                $results[] = array('id_siswa_aktif' => 0, 'status' => 'gagal', 'message' => 'id_siswa_aktif wajib diisi');
                continue;
            }

            $siswa = $this->mymodel->withquery(
                "SELECT s.{$siswa_id} AS id_siswa_aktif, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas
                 FROM {$siswa_tbl} s
                 LEFT JOIN {$kelas_tbl} k ON k.{$kelas_id} = s.id_kelas
                 WHERE s.{$siswa_id} = " . $id_siswa_aktif . " AND s.deleted_at IS NULL",
                'row'
            );

            if (empty($siswa)) {
                $results[] = array('id_siswa_aktif' => $id_siswa_aktif, 'status' => 'gagal', 'message' => 'Siswa tidak ditemukan');
                continue;
            }

            $nama_lengkap = $siswa->nama_lengkap;
            $kelas_siswa  = !empty($siswa->nama_kelas) ? $siswa->nama_kelas : $kelas;

            $status_hadir = ucfirst(strtolower($status_hadir));
            $non_hadir = in_array($status_hadir, array('Sakit', 'Izin', 'Alfa', 'Tidak hadir'));

            // Rubrik default per status
            if ($status_hadir == 'Terlambat') {
                $default_kehadiran = 50;
                $default_keaktifan = 75;
            } elseif ($status_hadir == 'Alfa' || $status_hadir == 'Tidak hadir') {
                $default_kehadiran = 0;
                $default_keaktifan = 0;
            } elseif ($non_hadir) {
                // Sakit / Izin
                $default_kehadiran = 50;
                $default_keaktifan = 80;
            } else {
                // Hadir
                $default_kehadiran = 80;
                $default_keaktifan = 80;
            }

            $kehadiran = isset($s['kehadiran']) && is_numeric($s['kehadiran'])
                ? max(0, min(100, (int) $s['kehadiran']))
                : $default_kehadiran;

            $keaktifan = isset($s['keaktifan']) && is_numeric($s['keaktifan'])
                ? max(0, min(100, (int) $s['keaktifan']))
                : $default_keaktifan;

            $status_keaktifan = ($non_hadir && !isset($s['keaktifan']))
                ? '-'
                : ((!empty($s['status_keaktifan'])) ? $s['status_keaktifan'] : 'Aktif');

            $total_nilai = hitung_total_nilai_pjok($kehadiran, $keaktifan);
            $predikat    = get_predikat_pjok($total_nilai);

            $existing = $this->mymodel->withquery(
                "SELECT id_presensi_pjok FROM presensi_pjok
                 WHERE id_siswa_aktif = " . $id_siswa_aktif . "
                 AND tanggal = '" . $this->db->escape_str($tanggal) . "'
                 AND deleted_at IS NULL",
                'row'
            );

            $save_data = array(
                'hari'              => $hari,
                'tanggal'           => $tanggal,
                'jenjang'           => strtoupper($jenjang),
                'id_siswa_aktif'    => $id_siswa_aktif,
                'nama_lengkap'      => $nama_lengkap,
                'kelas'             => $kelas_siswa,
                'status_hadir'      => $status_hadir,
                'kehadiran'         => $kehadiran,
                'status_keaktifan'  => !empty($status_keaktifan) ? $status_keaktifan : '-',
                'keaktifan'         => $keaktifan,
                'total_nilai'       => $total_nilai,
                'updated_by'        => $updated_by,
                'updated_at'        => date('Y-m-d H:i:s'),
            );

            if (!empty($existing)) {
                $this->mymodel->update('presensi_pjok', $save_data, 'id_presensi_pjok', $existing->id_presensi_pjok);
                $id_record = $existing->id_presensi_pjok;
                $action = 'update';
            } else {
                $save_data['created_at'] = date('Y-m-d H:i:s');
                $id_record = $this->mymodel->insertid('presensi_pjok', $save_data);
                $action = 'insert';
            }

            $results[] = array(
                'id_presensi_pjok' => $id_record,
                'id_siswa_aktif'   => $id_siswa_aktif,
                'nama_lengkap'     => $nama_lengkap,
                'status'           => 'berhasil',
                'action'           => $action,
                'total_nilai'      => $total_nilai,
                'predikat'         => $predikat['predikat'],
                'deskripsi'        => $predikat['deskripsi'],
            );
        }

        $this->response(array(
            'status'  => 1,
            'message' => 'Presensi PJOK berhasil disimpan',
            'data'    => $results
        ));
    }
}
