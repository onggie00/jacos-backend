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
        $tanggal_mulai = $this->post('start_date');
        $tanggal_selesai = $this->post('end_date');
        $nama_tabel = "presensi_".$jenjang;

        if($tanggal_mulai && $tanggal_selesai){
            $daterange = "tanggal_absen >= '$tanggal_mulai' and tanggal_absen <= '$tanggal_selesai'";
            $data = $this->mymodel->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange order by tanggal_absen DESC","result");
        }elseif($tanggal_mulai){
            $daterange = "tanggal_absen >= '$tanggal_mulai'";
            $data = $this->mymodel->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange order by tanggal_absen DESC","result");
        }elseif($tanggal_selesai){
            $daterange = "tanggal_absen <= '$tanggal_selesai'";
            $data = $this->mymodel->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange order by tanggal_absen DESC","result");
        }else{
            $data = $this->mymodel->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' order by tanggal_absen DESC","result");
        }

        if (!empty($data)) {
            $izin_tabel = "izin_siswa_".$jenjang;
            //Grouping: jika ada >1 record per (id_siswa_aktif, tanggal_absen), ambil waktu_absen paling awal
            $grouped = array();
            foreach ($data as $row) {
                $key = $row->id_siswa_aktif.'_'.$row->tanggal_absen;
                if (!isset($grouped[$key]) || strtotime($row->waktu_absen) < strtotime($grouped[$key]->waktu_absen)) {
                    $grouped[$key] = $row;
                }
            }
            $data = array_values($grouped);
            //Re-sort: tanggal_absen DESC, waktu_absen ASC (konsisten dengan output awal)
            usort($data, function($a, $b) {
                if ($a->tanggal_absen == $b->tanggal_absen) {
                    return strtotime($a->waktu_absen) - strtotime($b->waktu_absen);
                }
                return strtotime($b->tanggal_absen) - strtotime($a->tanggal_absen);
            });
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
