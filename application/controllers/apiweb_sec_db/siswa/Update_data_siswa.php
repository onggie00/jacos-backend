<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Update_data_siswa extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
  }

  function index_post()
  {
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    if (isset($headers['x-token'])) $token =  $headers['x-token'];
    $id = $this->post('id_siswa');
    $id_aktif = $this->post('id_siswa_aktif');
    $jenjang = $this->post('jenjang');

    if($jenjang=='sd'){
      $siswa="siswa_sd";
      $siswa_aktif="siswa_sd_aktif";
    }elseif($jenjang=='smp'){
      $siswa="siswa_smp";
      $siswa_aktif="siswa_smp_aktif";
    }elseif($jenjang=='sma'){
      $siswa="siswa_sma";
      $siswa_aktif="siswa_sma_aktif";
    }else{
      $siswa="siswa_ft";
      $siswa_aktif="siswa_ft_aktif";
    }
    $data = array(
      "nama_lengkap" => $this->post('nama_lengkap'),
      "nisn" => $this->post('nisn'),
      "agama" => $this->post('agama'),
      "jenis_kelamin" => $this->post('jenis_kelamin'),
      "tempat_lahir" => $this->post('tempat_lahir'),
      "tgl_lahir" => $this->post('tgl_lahir'),
      "alamat" => $this->post('alamat'),
      "email_ms_office" => $this->post('email_ms_office'),
    );

    if (!empty($data)) {
      $update_siswa = $this->second_db->update2($siswa, $data, "id_".$siswa, $id);
    }

    $data_aktif = array(
      "nama_lengkap" => $this->post('nama_lengkap'),
      "kewarganegaraan" => $this->post('kewarganegaraan'),
      "nik" => $this->post('nik'),
      "golongan_darah" => $this->post('golongan_darah'),
      "telp" => $this->post('telp'),
    );

    //Foto profil
    if (!empty($_FILES['foto_profil']['name'])) {
      $uploaddir = './uploads/'.$siswa.'/';
      $img = explode('.', $_FILES['foto_profil']['name']);
      $extension = end($img);
      $file_name =  md5(date('y-m-d h:i:s').$_FILES['foto_profil']['name']).".".$extension;
      $uploadfile = $uploaddir.$file_name;
      $status = 0;
      if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $uploadfile)) {
        $data_aktif['foto_profil'] = $file_name;
        $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
      }
    }

    if (!empty($data_aktif)) {
      $update_siswa_aktif = $this->second_db->update2($siswa_aktif, $data_aktif, "id_".$siswa."_aktif", $id_aktif);
    }

    if ($update_siswa == -1 && $update_siswa_aktif == -1) {
      $msg = array('status' => 0, 'message' => 'Gagal update data siswa ', 'data' => array());
    } else {
      $msg = array('status' => 1, 'message' => 'Berhasil update data', 'data' => array());
    }
    $this->response($msg);
  }
}
