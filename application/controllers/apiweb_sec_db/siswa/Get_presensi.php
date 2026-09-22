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
            $daterange = "tanggal_absen >= '$start_date' and tanggal_absen <= '$end_date'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange order by tanggal_absen DESC ","result");
        }elseif(!empty($start_date)){
            $daterange = "tanggal_absen >= '$start_date'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange order by tanggal_absen DESC ","result");
        }elseif(!empty($end_date)){
            $daterange = "tanggal_absen <= '$end_date'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange order by tanggal_absen DESC ","result");
        }else{
            $daterange = "tanggal_absen >= '".date("Y-m-d")."' and tanggal_absen <= '".date("Y-m-d")."'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange order by tanggal_absen DESC","result");
        }

        if (!empty($data)) {
            $izin_tabel = "izin_siswa_".$jenjang;
            foreach ($data as $key => $value) {
                if (!empty($value->id_izin)) {
                    $value->detail_izin = $this->second_db->getbywhere($izin_tabel, "id", $value->id_izin, "row");
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
