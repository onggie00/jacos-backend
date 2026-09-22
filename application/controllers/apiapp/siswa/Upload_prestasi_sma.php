<?php
header("Access-Control-Allow-Origin: *"); header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); header('Access-Control-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Upload_prestasi_sma extends REST_Controller {
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
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

      $id_siswa = $this->post('id_siswa');
      $data = array(
        "id_siswa_sma" => $id_siswa,
        "jenis_prestasi" => $this->post('jenis_prestasi'),
        "nama_prestasi" => $this->post('nama_prestasi'),
        "keterangan_prestasi" => $this->post('keterangan_prestasi'),
        "tahun_prestasi" => $this->post('tahun_prestasi'),
        "jenis_jenjang" => $this->post('jenis_jenjang'),
        "jenis_lomba" => $this->post('jenis_lomba'),
      );

      if (!empty($this->post('keterangan_lainnya')) ) {
        $data['keterangan_prestasi_lainnya'] = $this->post('keterangan_lainnya');
      }
      
      //File Sertifikat
      if (!empty($_FILES['sertifikat']['name'])) {
        $uploaddir = './uploads/siswa_sma/prestasi/';
        $img = explode('.', $_FILES['sertifikat']['name']);
        $extension = end($img);
        $file_name =  md5(date('y-m-d h:i:s').$_FILES['sertifikat']['name']).".".$extension;
        $uploadfile = $uploaddir.$file_name;
        $status = 0;
        if (move_uploaded_file($_FILES['sertifikat']['tmp_name'], $uploadfile)) {
          $data['sertifikat'] = $file_name;
          $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
        }
      }

      if (!empty($data)) {
        $ppsbb_siswa = $this->mymodel->insertid("ppsbb_siswa_sma",$data);
        $msg = array('status' => 1, 'message'=>'Berhasil tambah data', 'data' => $data);
      }
      else{
        $msg = array('status' => 0, 'message'=>'Data tidak valid', 'data' => array());
      }
      
      $this->response($msg);
    }
}
