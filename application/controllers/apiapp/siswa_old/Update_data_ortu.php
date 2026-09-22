<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Update_data_ortu extends REST_Controller
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
      "nama_ayah" => $this->post('nama_ayah'),
      "nama_ibu" => $this->post('nama_ibu'),
      "notelp_ibu" => $this->post('notelp_ibu'),
      "notelp_ayah" => $this->post('notelp_ayah'),

    );

    if (!empty($data)) {
      $update_siswa = $this->mymodel->update($siswa, $data, "id_siswa_sd", $id);
    }
    
    $data_aktif = array(
      "pendidikan_ayah" => $this->post('pendidikan_ayah'),
      "pendidikan_ibu" => $this->post('pendidikan_ibu'),
      "penghasilan_ayah" => $this->post('penghasilan_ayah'),
      "penghasilan_ibu" => $this->post('penghasilan_ibu'),
      "tgl_lahir_ayah" => $this->post('tgl_lahir_ayah'),
      "tgl_lahir_ibu" => $this->post('tgl_lahir_ibu'),
    );

    if (!empty($data_aktif)) {
      $update_siswa_aktif = $this->mymodel->update($siswa_aktif, $data_aktif, "id_".$siswa."_aktif", $id_aktif);
    }
    // dd($update_siswa_aktif);
    if ($update_siswa == -1 && $update_siswa_aktif == -1) {
      $msg = array('status' => 0, 'message' => 'Gagal update data siswa ', 'data' => array());
    } else {
      $msg = array('status' => 1, 'message' => 'Berhasil update data', 'data' => array());
    }
    $this->response($msg);
  }
}
