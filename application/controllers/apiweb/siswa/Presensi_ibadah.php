<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Presensi_ibadah extends REST_Controller {
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
        $tanggal = $this->post("tanggal");
        $waktu = $this->post("waktu");
        $id_ibadah = $this->post("id_ibadah");
        $rakaat = $this->post("rakaat");
        $keterangan = $this->post("keterangan");

        $data_insert = array(
          "id_siswa_aktif" => $id_siswa_aktif,
          "jenjang" => $jenjang,
          "tanggal" => $tanggal,
          "waktu" => $waktu,
          "id_ibadah" => $id_ibadah,
          "jumlah_rakaat" => $rakaat,
          "keterangan" => $keterangan,
        );
        $cek_presensi = $this->mymodel->withquery("select i.id, i.id_siswa_aktif, s.nama_lengkap, k.label as nama_kelas, i.jenjang, i.id_ibadah, p.ibadah, i.tanggal, i.waktu, i.jumlah_rakaat, i.keterangan from ibadah i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif and s.deleted_at is null join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." join ibadah_setting p on i.id_ibadah = p.id where i.tanggal = '".$tanggal."' and i.deleted_at is null and i.id_siswa_aktif = '".$id_siswa_aktif."'","row");

        if (!empty($cek_presensi)) {
          $data_insert["id"] = $cek_presensi->id;
          $data_insert['tanggal'] = $cek_presensi->tanggal;
          $data_insert["waktu"] = $cek_presensi->waktu;
          $data_insert["keterangan"] = $cek_presensi->keterangan;
          $data_insert["jumlah_rakaat"] = $cek_presensi->jumlah_rakaat;
          $msg = array('status' => 1, 'message'=>'Sudah melakukan Presensi ibadah terkait' ,'data'=>$data_insert);
          $status="200";
        }
        else if(empty($cek_presensi)){
          $insert = $this->mymodel->insertid("ibadah", $data_insert);
          $data_insert["id"] = $insert;
          $msg = array('status' => 1, 'message'=>'Berhasil melakukan presensi ibadah' ,'data'=>$data_insert);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data Input tidak valid' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
