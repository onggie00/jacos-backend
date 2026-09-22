<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_presensi_ekskul extends REST_Controller {
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
        $id_siswa_aktif = $this->post('id_siswa');
        $id_ekskul = $this->post('id_ekskul');
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');

        $nama_tabel = "ekskul_presensi_siswa_".$jenjang;
        $where = "";


        if (!empty($start_date) && !empty($end_date)) {
            if ($where == "") {
                $where .= "where p.tanggal_absen >= '".$start_date."' and p.tanggal_absen <= '".$end_date."'";
            }
            else{
                $where .= " and p.tanggal_absen >= '".$start_date."' and p.tanggal_absen <= '".$end_date."'";
            }
        }
        else if(!empty($start_date) && empty($end_date)){
            if ($where == "") {
                $where .= "where p.tanggal_absen >= '".$start_date."'";
            }
            else{
                $where .= " and p.tanggal_absen >= '".$start_date."'";
            }
        }
        else if (empty($start_date) && !empty($end_date)) {
            if ($where == "") {
                $where .= "where p.tanggal_absen <= '".$end_date."'";
            }
            else{
                $where .= " and p.tanggal_absen <= '".$end_date."'";
            }
        }
        else{
            if ($where == "") {
                $where .= "where p.tanggal_absen >= '".date("Y-m-d")."' and p.tanggal_absen <= '".date("Y-m-d")."'";
            }
            else{
                $where .= " and p.tanggal_absen >= '".date("Y-m-d")."' and p.tanggal_absen <= '".date("Y-m-d")."'";
            }
        }

        if (!empty($id_siswa_aktif)) {
            if ($where == "") {
                $where .= "where p.id_siswa_aktif = '".$id_siswa_aktif."'";
            }
            else{
                $where .= " and p.id_siswa_aktif = '".$id_siswa_aktif."'";
            }
        }

        $data = $this->mymodel->withquery("select p.*, s.nama_lengkap, k.label as nama_kelas, k.id_tingkatan, s.id_kelas from ".$nama_tabel." p join siswa_".$jenjang."_aktif s on p.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." ".$where." order by p.tanggal_absen ASC","result");

        if (!empty($data)) {
            $msg = array('status' => 200, 'message'=>'Data Presensi Ekstrakurikuler ditemukan' ,'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Presensi Ekstrakurikuler tidak ditemukan' ,'data'=>null);
        }

        $this->response($msg);
    }
}
