<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API: Get_jam_catatan
 *
 * Tujuan  : Mengambil daftar master jam catatan presensi siswa.
 *           Dipakai FE/mobile untuk dropdown pilihan jam saat input catatan.
 * Tabel   : presensi_jam_catatan
 *
 * Input GET: (tidak ada) — return semua data
 *
 * Data:
 *   - jam 1–12  (is_lainnya = 0)
 *   - lainnya   (is_lainnya = 1)
 */
class Get_jam_catatan extends REST_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $data = $this->mymodel->withquery(
            "SELECT id_jam_catatan, jam, is_lainnya
             FROM presensi_jam_catatan
             ORDER BY is_lainnya ASC, id_jam_catatan ASC",
            "result"
        );

        if (!empty($data)) {
            $msg = array('status' => 200, 'message' => 'Data jam catatan ditemukan', 'data' => $data);
        } else {
            $msg = array('status' => 401, 'message' => 'Data jam catatan tidak ditemukan', 'data' => array());
        }

        $this->response($msg);
    }
}
