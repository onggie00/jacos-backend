<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_presensi extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
        $status = "";
        $token = "";
        $headers=array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
            
        }

        $jenjang = $this->post('jenjang');
        $id_siswa_aktif = $this->post('id_siswa_aktif');
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $nama_tabel = "presensi_".$jenjang;

        if(!empty($start_date) && !empty($end_date) ){
            $daterange = "p.tanggal_absen >= '$start_date' and p.tanggal_absen <= '$end_date'";
            $daterange_sub = "tanggal_absen >= '$start_date' and tanggal_absen <= '$end_date'";
        }elseif(!empty($start_date)){
            $daterange = "p.tanggal_absen >= '$start_date'";
            $daterange_sub = "tanggal_absen >= '$start_date'";
        }elseif(!empty($end_date)){
            $daterange = "p.tanggal_absen <= '$end_date'";
            $daterange_sub = "tanggal_absen <= '$end_date'";
        }else{
            $daterange = "p.tanggal_absen >= '".date("Y-m-d")."' and p.tanggal_absen <= '".date("Y-m-d")."'";
            $daterange_sub = "tanggal_absen >= '".date("Y-m-d")."' and tanggal_absen <= '".date("Y-m-d")."'";
        }

        $data = $this->mymodel->withquery("
            SELECT p.* FROM ".$nama_tabel." p
            INNER JOIN (
                SELECT id_siswa_aktif, tanggal_absen, hari_absen, MIN(waktu_absen) AS min_waktu
                FROM ".$nama_tabel."
                WHERE id_siswa_aktif = '$id_siswa_aktif' AND $daterange_sub
                GROUP BY id_siswa_aktif, tanggal_absen, hari_absen
            ) pmin ON p.id_siswa_aktif = pmin.id_siswa_aktif
                AND p.tanggal_absen = pmin.tanggal_absen
                AND p.hari_absen = pmin.hari_absen
                AND p.waktu_absen = pmin.min_waktu
            WHERE p.id_siswa_aktif = '$id_siswa_aktif' AND $daterange
            ORDER BY p.tanggal_absen DESC
        ", "result");

        // Deduplicate: ambil 1 record per (id_siswa_aktif, tanggal_absen, hari_absen) dengan waktu_absen terkecil
        if (!empty($data)) {
            $seen = array();
            $filtered = array();
            foreach ($data as $row) {
                $key = $row->id_siswa_aktif . '_' . $row->tanggal_absen . '_' . $row->hari_absen;
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $filtered[] = $row;
                }
            }
            $data = $filtered;
        }

        if (!empty($data)) {
            $izin_tabel = "izin_siswa_".$jenjang;
            foreach ($data as $key => $value) {
                if (!empty($value->id_izin)) {
                    $value->detail_izin = $this->mymodel->getbywhere($izin_tabel, "id", $value->id_izin, "row");
                }
                else{
                    $value->detail_izin = new stdClass();
                }
            }
            $msg = array('status' => 200, 'message'=>'Data Absensi ditemukan' ,'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Absensi tidak ditemukan' ,'data'=>$data);
        }

        $this->response($msg,$status);
    }
}
