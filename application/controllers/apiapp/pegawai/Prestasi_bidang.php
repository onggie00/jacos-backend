<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

class Prestasi_bidang extends REST_Controller {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * GET list of id_prestasi_bidang + nama_bidang
     * Dipakai oleh mobile app untuk populate dropdown "Bidang Prestasi"
     * saat tambah/edit data prestasi pegawai.
     * Mirror apiapp/siswa/Prestasi_bidang.php — tabel dipisah per role.
     */
    function index_get()
    {
        $data = $this->mymodel->withquery(
            "SELECT id_prestasi_bidang, nama_bidang
             FROM prestasi_pegawai_bidang
             ORDER BY id_prestasi_bidang ASC",
            "result"
        );

        if (!empty($data)) {
            $msg = array(
                'status'  => 1,
                'message' => 'Berhasil ambil data',
                'data'    => $data
            );
        } else {
            $msg = array(
                'status'  => 0,
                'message' => 'Data tidak ditemukan',
                'data'    => array()
            );
        }

        $this->response($msg, 200);
    }

}
