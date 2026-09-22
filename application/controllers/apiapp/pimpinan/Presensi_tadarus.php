<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * --------------------------------------------------------------------------
 * Presensi Tadarus (Pimpinan) — Simpan Batch
 * --------------------------------------------------------------------------
 * POST /apiapp/pimpinan/presensi_tadarus
 * Simpan presensi + nilai tadarus untuk multi siswa (1 kelas, 1 tanggal).
 */
class Presensi_tadarus extends REST_Controller
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

    public function index_post()
    {
        $auth = $this->_authenticate();
        if (!$auth['valid']) {
            $this->response(array('status' => 0, 'message' => $auth['message']), 401);
            return;
        }

        $pimpinan = $auth['data'];

        $tanggal    = $this->post('tanggal');
        $jenjang    = strtolower($this->post('jenjang'));
        $kelas      = $this->post('kelas');
        $siswa_data = $this->post('siswa');

        if (empty($tanggal) || empty($jenjang) || empty($kelas) || empty($siswa_data)) {
            $this->response(array('status' => 0, 'message' => 'Parameter tanggal, jenjang, kelas, dan siswa wajib diisi'));
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
        $updated_by = $pimpinan->nama_lengkap . ' (Pimpinan ' . strtoupper($auth['jenjang']) . ')';
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
            $default_skor = ($status_hadir === 'Hadir') ? 4 : 1;

            $kehadiran   = (!empty($s['kehadiran']))   ? max(1, min(4, (int) $s['kehadiran']))   : $default_skor;
            $kelengkapan = (!empty($s['kelengkapan'])) ? max(1, min(4, (int) $s['kelengkapan'])) : $default_skor;
            $adab        = (!empty($s['adab']))        ? max(1, min(4, (int) $s['adab']))        : $default_skor;
            $keaktifan   = (!empty($s['keaktifan']))   ? max(1, min(4, (int) $s['keaktifan']))   : $default_skor;

            $total_nilai = hitung_total_nilai_tadarus($kehadiran, $kelengkapan, $adab, $keaktifan);
            $predikat    = get_predikat_tadarus($total_nilai);

            $existing = $this->mymodel->withquery(
                "SELECT id_presensi_tadarus FROM presensi_tadarus
                 WHERE id_siswa_aktif = " . $id_siswa_aktif . "
                 AND tanggal = '" . $this->db->escape_str($tanggal) . "'
                 AND deleted_at IS NULL",
                'row'
            );

            $save_data = array(
                'hari'           => $hari,
                'tanggal'        => $tanggal,
                'jenjang'        => strtoupper($jenjang),
                'id_siswa_aktif' => $id_siswa_aktif,
                'nama_lengkap'   => $nama_lengkap,
                'kelas'          => $kelas_siswa,
                'status_hadir'   => $status_hadir,
                'kehadiran'      => $kehadiran,
                'kelengkapan'    => $kelengkapan,
                'adab'           => $adab,
                'keaktifan'      => $keaktifan,
                'total_nilai'    => $total_nilai,
                'updated_by'     => $updated_by,
                'updated_at'     => date('Y-m-d H:i:s'),
            );

            if (!empty($existing)) {
                $this->mymodel->update('presensi_tadarus', $save_data, 'id_presensi_tadarus', $existing->id_presensi_tadarus);
                $id_record = $existing->id_presensi_tadarus;
                $action = 'update';
            } else {
                $save_data['created_at'] = date('Y-m-d H:i:s');
                $id_record = $this->mymodel->insertid('presensi_tadarus', $save_data);
                $action = 'insert';
            }

            $results[] = array(
                'id_presensi_tadarus' => $id_record,
                'id_siswa_aktif'      => $id_siswa_aktif,
                'nama_lengkap'        => $nama_lengkap,
                'status'              => 'berhasil',
                'action'              => $action,
                'total_nilai'         => $total_nilai,
                'predikat'            => $predikat['predikat'],
                'deskripsi'           => $predikat['deskripsi'],
            );
        }

        $this->response(array(
            'status'  => 1,
            'message' => 'Presensi tadarus berhasil disimpan',
            'data'    => $results
        ));
    }
}
