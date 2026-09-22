<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Riwayat_presensi_ibadah extends REST_Controller {
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
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];
        $id_siswa_aktif = $this->post("id_siswa_aktif");
        $jenjang = $this->post("jenjang");
        $tanggal_start = $this->post("tanggal_start");
        $tanggal_end = $this->post("tanggal_end");
        $id_ibadah = $this->post("id_ibadah");
        $where = "";

        if (!empty($tanggal_start)) {
          $where .= " and tanggal >= '".date("Y-m-d", strtotime($tanggal_start))."'";
        }
        if (!empty($tanggal_end)) {
          $where .= " and tanggal <= '".date("Y-m-d", strtotime($tanggal_end))."'";
        }
        if (!empty($id_ibadah)) {
          $where .= " and id_ibadah = '".$id_ibadah."'";
        }

        $cek_presensi = $this->mymodel->withquery("select i.id, i.id_siswa_aktif, s.nama_lengkap, k.label as nama_kelas, i.jenjang, i.id_ibadah, p.ibadah, i.tanggal, i.waktu, i.jumlah_rakaat, i.keterangan from ibadah i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif and s.deleted_at is null join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." join ibadah_setting p on i.id_ibadah = p.id where i.deleted_at is null and i.id_siswa_aktif = '".$id_siswa_aktif."' ".$where." ","result");

        foreach ($cek_presensi as $key => $value) {
          $value->waktu = date("H:i", strtotime($value->waktu));
        }

        if (!empty($cek_presensi)) {
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$cek_presensi);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
