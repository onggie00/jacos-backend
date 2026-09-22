<?php
header("Access-Control-Allow-Origin: *"); header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); header('Access-Control-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Tambah_kpi extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }

    function index_post() {
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[strtolower($name)] = $value;
      }
      if(isset($headers['x-token']))$token =  $headers['x-token'];
      $jenjang=$this->post('jenjang');
     
      $data = array(
        "nama_kpi" => $this->post('nama_kpi'),
        "id_guru" => $this->post('id_guru'),
        "id_jenis_kpi" => $this->post('id_jenis_kpi'),
        "judul_kpi" => $this->post('judul_kpi'),
        "tanggal" => $this->post('tanggal'),
        "keterangan" => $this->post('keterangan'),
        "is_approve" => 0
      );
      // dd($data);
      //File Sertifikat
      if (!empty($_FILES['file_piagam']['name'])) {
        if($jenjang=='sd'){
          $uploaddir = './uploads/kpi_sd/';
        }elseif($jenjang=='smp'){
          $uploaddir = './uploads/kpi_smp/';
        }elseif($jenjang=='sma'){
          $uploaddir = './uploads/kpi_sma/';
        }else{
          $uploaddir = './uploads/kpi_ft/';
        }
        $img = explode('.', $_FILES['file_piagam']['name']);
        $extension = end($img);
        $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_piagam']['name']).".".$extension;
        $uploadfile = $uploaddir.$file_name;
        $status = 0;
        if (move_uploaded_file($_FILES['file_piagam']['tmp_name'], $uploadfile)) {
          $data['file_piagam'] = $file_name;
          $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
        }
      }

      if (!empty($data)) {
        if($jenjang=='sd'){
          $insert=$this->mymodel->insertid("kpi_sd",$data);
        }elseif($jenjang=='smp'){
          $insert=$this->mymodel->insertid("kpi_smp",$data);
        }elseif($jenjang=='sma'){
          $insert=$this->mymodel->insertid("kpi_sma",$data);
        }else{
          $insert=$this->mymodel->insertid("kpi_ft",$data);
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
