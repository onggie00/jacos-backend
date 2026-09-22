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
          $headers[strtolower($name)] = $value;
      }
      if(isset($headers['x-token']))$token =  $headers['x-token'];

      $jenjang=$this->post('jenjang');

      // Validasi required fields (mirror apiapp/siswa/Tambah_prestasi.php,
      // id_siswa diganti id_pimpinan).
      $required = array(
         'nama_prestasi'      => 'Nama prestasi',
         'id_pimpinan'        => 'ID pimpinan',
         'tgl_raih'           => 'Tanggal raih',
         'jenis_prestasi_id'  => 'ID jenis prestasi',
         'id_prestasi_bidang' => 'ID bidang prestasi',
      );
      foreach ($required as $field => $label) {
         $val = $this->post($field);
         if ($val === null || $val === '' || (is_string($val) && trim($val) === '')) {
            $msg = array(
               'status'  => 0,
               'message' => $label . ' wajib diisi',
               'data'    => array()
            );
            $this->response($msg, 400);
            return;
         }
      }

      $data = array(
        "nama_prestasi" => $this->post('nama_prestasi'),
        "id_pimpinan" => $this->post('id_pimpinan'),
        "tgl_raih" => $this->post('tgl_raih'),
        "judul" => (!empty($this->post('judul'))) ? $this->post('judul') : "",
        "jenis_prestasi_id" => $this->post('jenis_prestasi_id'),
        "konten" => $this->post('konten'),
        "juara" => $this->post('juara'),
        "id_prestasi_bidang" => $this->post('id_prestasi_bidang'),
        "kurasi_pusprenas" => $this->post('kurasi_pusprenas'),
        "link_pusprenas"  => $this->post('link_pusprenas'),
        "tanggal_posting" =>date("Y-m-d H:i:s"),
        "is_approved" => 0
      );

      // Whitelist ekstensi + size cap untuk upload.
      // Whitelist pimpinan mengikuti guru (termasuk heic/webp/gif untuk
      // kompatibilitas mobile app).
      $upload_allowed_ext = array('jpg','jpeg','png','gif','heic','webp');
      $upload_max_bytes   = 10 * 1024 * 1024; // 10MB

      // Pastikan folder upload ada.
      if (!is_dir(FCPATH . '/uploads/prestasi_pimpinan_sd/')) {
        mkdir(FCPATH . '/uploads/prestasi_pimpinan_sd/');
      }
      if (!is_dir(FCPATH . '/uploads/prestasi_pimpinan_smp/')) {
        mkdir(FCPATH . '/uploads/prestasi_pimpinan_smp/');
      }
      if (!is_dir(FCPATH . '/uploads/prestasi_pimpinan_sma/')) {
        mkdir(FCPATH . '/uploads/prestasi_pimpinan_sma/');
      }
      if (!is_dir(FCPATH . '/uploads/prestasi_pimpinan_ft/')) {
        mkdir(FCPATH . '/uploads/prestasi_pimpinan_ft/');
      }

      //File Sertifikat
      if (!empty($_FILES['file_prestasi']['name'])) {
        $img = explode('.', $_FILES['file_prestasi']['name']);
        $extension = strtolower(end($img));
        if (!in_array($extension, $upload_allowed_ext)) {
           $msg = array(
              'status'  => 0,
              'message' => 'Ekstensi file_prestasi tidak diizinkan: ' . $extension,
              'data'    => array()
           );
           $this->response($msg, 400);
           return;
        }
        if (isset($_FILES['file_prestasi']['size']) && (int)$_FILES['file_prestasi']['size'] > $upload_max_bytes) {
           $msg = array(
              'status'  => 0,
              'message' => 'Ukuran file_prestasi melebihi 10MB',
              'data'    => array()
           );
           $this->response($msg, 400);
           return;
        }
        if($jenjang=='sd'){
          $uploaddir = './uploads/prestasi_pimpinan_sd/';
        }elseif($jenjang=='smp'){
          $uploaddir = './uploads/prestasi_pimpinan_smp/';
        }elseif($jenjang=='sma'){
          $uploaddir = './uploads/prestasi_pimpinan_sma/';
        }else{
          $uploaddir = './uploads/prestasi_pimpinan_ft/';
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
        $img = explode('.', $_FILES['foto_prestasi']['name']);
        $extension = strtolower(end($img));
        if (!in_array($extension, $upload_allowed_ext)) {
           $msg = array(
              'status'  => 0,
              'message' => 'Ekstensi foto_prestasi tidak diizinkan: ' . $extension,
              'data'    => array()
           );
           $this->response($msg, 400);
           return;
        }
        if (isset($_FILES['foto_prestasi']['size']) && (int)$_FILES['foto_prestasi']['size'] > $upload_max_bytes) {
           $msg = array(
              'status'  => 0,
              'message' => 'Ukuran foto_prestasi melebihi 10MB',
              'data'    => array()
           );
           $this->response($msg, 400);
           return;
        }
        if($jenjang=='sd'){
          $uploaddir = './uploads/prestasi_pimpinan_sd/';
        }elseif($jenjang=='smp'){
          $uploaddir = './uploads/prestasi_pimpinan_smp/';
        }elseif($jenjang=='sma'){
          $uploaddir = './uploads/prestasi_pimpinan_sma/';
        }else{
          $uploaddir = './uploads/prestasi_pimpinan_ft/';
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
          $insert=$this->mymodel->insertid("prestasi_pimpinan_sd",$data);
        }elseif($jenjang=='smp'){
          $insert=$this->mymodel->insertid("prestasi_pimpinan_smp",$data);
        }elseif($jenjang=='sma'){
          $insert=$this->mymodel->insertid("prestasi_pimpinan_sma",$data);
        }else{
          $insert=$this->mymodel->insertid("prestasi_pimpinan_ft",$data);
        }
        if($insert){
          $data['id_prestasi'] = $insert;
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
