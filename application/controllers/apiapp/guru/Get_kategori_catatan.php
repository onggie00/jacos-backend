<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API: Get_kategori_catatan
 *
 * Tujuan  : Mengambil daftar master kategori catatan pelanggaran (aktif saja).
 *           Dipakai FE/mobile untuk dropdown pilihan kategori saat input catatan.
 * Akses   : Semua user terotentikasi (guru/operator).
 * Tabel   : presensi_kategori_catatan
 *
 * Input GET:
 *   - (tidak ada) — return semua kategori aktif
 *
 * Header:
 *   - x-token : token login guru_<jenjang> (tidak divalidasi ketat, cukup ada)
 *
 * Response: array kategori aktif, urut ASC id_kategori_catatan.
 *           Kolom: id_kategori_catatan, nama_kategori, skor, is_custom
 */
class Get_kategori_catatan extends REST_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $data = $this->mymodel->withquery(
            "SELECT id_kategori_catatan, nama_kategori, skor, is_custom
             FROM presensi_kategori_catatan
             WHERE status = 'aktif'
             ORDER BY id_kategori_catatan ASC",
            "result"
        );

        if (!empty($data)) {
            $msg = array('status' => 200, 'message' => 'Data kategori ditemukan', 'data' => $data);
        } else {
            $msg = array('status' => 401, 'message' => 'Data kategori tidak ditemukan', 'data' => array());
        }

        $this->response($msg);
    }
}
