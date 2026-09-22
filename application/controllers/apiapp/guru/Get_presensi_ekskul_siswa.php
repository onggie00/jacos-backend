<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_presensi_ekskul_siswa extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
        $headers = getallheaders();

        $jenjang         = $this->post('jenjang');
        $id_siswa_aktif  = $this->post('id_siswa_aktif');
        $id_ekskul       = $this->post('id_ekskul');
        $tanggal_mulai   = $this->post('tanggal_mulai');
        $tanggal_selesai = $this->post('tanggal_selesai');

        $tahun_ajaran = $this->mymodel->withquery("
            SELECT label 
            FROM tahun_ajaran 
            WHERE tanggal_mulai <= '".date('Y-m-d')."' 
            AND tanggal_selesai >= '".date('Y-m-d')."' 
            ORDER BY id_tahun_ajaran DESC 
            LIMIT 1
        ", "row")->label;

        $semester = (date('m') >= 7) ? "1" : "2";

        // ==========================
        // Dynamic condition
        // ==========================
        $where = [
            "tahun_ajaran = '".$tahun_ajaran."'",
            "semester = '".$semester."'"
        ];

        if ($jenjang)        $where[] = "jenjang = '$jenjang'";
        if ($id_siswa_aktif) $where[] = "id_siswa_aktif = '$id_siswa_aktif'";
        if ($id_ekskul)      $where[] = "id_ekskul = '$id_ekskul'";

        if ($tanggal_mulai && $tanggal_selesai) {
            $where[] = "tanggal_absen BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'";
        } elseif ($tanggal_mulai) {
            $where[] = "tanggal_absen >= '$tanggal_mulai'";
        } elseif ($tanggal_selesai) {
            $where[] = "tanggal_absen <= '$tanggal_selesai'";
        }

        $sql = "SELECT * FROM presensi_ekskul";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $data = $this->mymodel->withquery($sql, "result");

        // ==========================
        // Response
        // ==========================
        $msg = !empty($data) 
            ? ['status' => 200, 'message'=>'Data Absensi ditemukan', 'data'=>$data]
            : ['status' => 401, 'message'=>'Data Absensi tidak ditemukan', 'data'=>$data];

        $this->response($msg);
    }
}
