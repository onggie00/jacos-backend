<?php
header("Access-Control-Allow-Origin: *"); header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); header('Access-Control-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Tambah_prestasi extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }

    function index_post() {
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))$token =  $headers['x-token'];
      $jenjang=$this->post('jenjang');
     
      $data = array(
        "nama_prestasi" => $this->post('nama_prestasi'),
        "id_siswa" => $this->post('id_siswa'),
        "konten" => $this->post('konten'),
        "tgl_raih" => $this->post('tgl_raih'),
        "judul" => (!empty($this->post('judul'))) ? $this->post('judul') : "",
        "jenis_prestasi_id" => $this->post('jenis_prestasi_id'),
        "konten" => $this->post('konten'),
        "juara" => $this->post('juara'),
        "tanggal_posting" =>date("Y-m-d H:i:s"),
        "is_approved" => 0
      );
      // dd($data);
      //File Sertifikat
      if (!empty($_FILES['file_prestasi']['name'])) {
        if($jenjang=='sd'){
          $uploaddir = './uploads/prestasi_siswa_sd/';
        }elseif($jenjang=='smp'){
          $uploaddir = './uploads/prestasi_siswa_smp/';
        }elseif($jenjang=='sma'){
          $uploaddir = './uploads/prestasi_siswa_sma/';
        }else{
          $uploaddir = './uploads/prestasi_siswa_ft/';
        }
        $img = explode('.', $_FILES['file_prestasi']['name']);
        $extension = end($img);
        $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_prestasi']['name']).".".$extension;
        $uploadfile = $uploaddir.$file_name;
        $status = 0;
        if (move_uploaded_file($_FILES['file_prestasi']['tmp_name'], $uploadfile)) {
          $data['file_prestasi'] = $file_name;
          $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
        }
      }

      if (!empty($_FILES['foto_prestasi']['name'])) {
        if($jenjang=='sd'){
          $uploaddir = './uploads/prestasi_siswa_sd/';
        }elseif($jenjang=='smp'){
          $uploaddir = './uploads/prestasi_siswa_smp/';
        }elseif($jenjang=='sma'){
          $uploaddir = './uploads/prestasi_siswa_sma/';
        }else{
          $uploaddir = './uploads/prestasi_siswa_ft/';
        }
        $img = explode('.', $_FILES['foto_prestasi']['name']);
        $extension = end($img);
        $file_name =  md5(date('y-m-d h:i:s').$_FILES['foto_prestasi']['name']).".".$extension;
        $uploadfile = $uploaddir.$file_name;
        $status = 0;
        if (move_uploaded_file($_FILES['foto_prestasi']['tmp_name'], $uploadfile)) {
          $data['foto_prestasi'] = $file_name;
          $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
        }
      }

      if (!empty($data)) {
        if($jenjang=='sd'){
          $insert=$this->mymodel->insertid("prestasi_siswa_sd",$data);
        }elseif($jenjang=='smp'){
          $insert=$this->mymodel->insertid("prestasi_siswa_smp",$data);
        }elseif($jenjang=='sma'){
          $insert=$this->mymodel->insertid("prestasi_siswa_sma",$data);
        }else{
          $insert=$this->mymodel->insertid("prestasi_siswa_ft",$data);
        }
        if($insert){
          $msg = array('status' => 1, 'message'=>'Berhasil tambah data', 'data' => $data);
        }else{
          $msg = array('status' => 0, 'message'=>'Gagal tambah data', 'data' => array());
        }
      }
      else{
        $msg = array('status' => 0, 'message'=>'Data tidak valid', 'data' => array());
      }
      
      $this->response($msg);
    }
}
