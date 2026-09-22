<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_presensi_siswa extends REST_Controller {
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
        $id_kelas = $this->post('id_kelas');
        $id_tingkatan = $this->post('id_tingkatan');
        $nama_tabel = "presensi_".$jenjang;
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
        else {
            if ($where == "") {
                $where .= "where p.tanggal_absen >= '".date("Y-m-d")."' and p.tanggal_absen <= '".date("Y-m-d")."'";
            }
            else{
                $where .= " and p.tanggal_absen >= '".date("Y-m-d")."' and p.tanggal_absen <= '".date("Y-m-d")."'";
            }
        }

        if (!empty($id_kelas)) {
            if ($where == "") {
                $where .= "where s.id_kelas = '".$id_kelas."'";
            }
            else{
                $where .= " and s.id_kelas = '".$id_kelas."'";
            }
        }

        if (!empty($id_tingkatan)) {
            if ($where == "") {
                $where .= "where k.id_tingkatan = '".$id_tingkatan."'";
            }
            else{
                $where .= " and k.id_tingkatan = '".$id_tingkatan."'";
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

        $data = $this->second_db->withquery("select s.id_siswa_".$jenjang."_aktif, s.nama_lengkap, k.label as nama_kelas, k.id_tingkatan, s.id_kelas, p.wifi_ssid, p.wifi_ip, p.hari_absen, p.tanggal_absen, p.waktu_absen, p.status_absen, p.id_izin from siswa_".$jenjang."_aktif s join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." left join presensi_".$jenjang." p on s.id_siswa_".$jenjang."_aktif = p.id_siswa_aktif where s.id_kelas = '".$id_kelas."' order by s.nama_lengkap ASC ","result");

        /*if($start_date && $end_date && $id_siswa_aktif){
            $daterange = "tanggal_absen >= '$start_date' and tanggal_absen <= '$end_date'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange ","result");
        }elseif($start_date && $end_date){
            $daterange = "tanggal_absen >= '$start_date' and tanggal_absen <= '$end_date'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where $daterange ","result");
        }elseif($start_date && $id_siswa_aktif){
            $daterange = "tanggal_absen >= '$start_date'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange ","result");
        }elseif($end_date && $id_siswa_aktif){
            $daterange = "tanggal_absen <= '$end_date'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' and $daterange ","result");
        }elseif($start_date){
            $daterange = "tanggal_absen >= '$start_date'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where $daterange ","result");
        }elseif($end_date){
            $daterange = "tanggal_absen <= '$end_date'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where $daterange ","result");
        }elseif($id_siswa_aktif){
            $daterange = "tanggal_absen <= '$id_siswa_aktif'";
            $data = $this->second_db->withquery("select * from ".$nama_tabel." where id_siswa_aktif = '$id_siswa_aktif' ","result");
        }else{
            $data = $this->second_db->withquery("select * from ".$nama_tabel." ","result");
        }*/

        if (!empty($data)) {
            $data_presensi = $this->second_db->withquery("select p.*, k.label as nama_kelas, k.id_tingkatan, s.id_kelas, s.nama_lengkap from ".$nama_tabel." p join siswa_".$jenjang."_aktif s on p.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." ".$where." order by p.tanggal_absen ASC","result");
            foreach ($data as $key => $value) {
                $izin_tabel = "izin_siswa_".$jenjang;
                foreach ($data_presensi as $key => $value) {
                    if (!empty($value->id_izin)) {
                        $value->detail_izin = $this->second_db->getbywhere($izin_tabel, "id", $value->id_izin, "row");
                    }
                    else{
                        $value->detail_izin = new stdClass();
                    }
                }
            }
            $msg = array('status' => 200, 'message'=>'Data Absensi ditemukan' ,'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Absensi tidak ditemukan' ,'data'=>$data);
        }

        $this->response($msg);
    }
}
