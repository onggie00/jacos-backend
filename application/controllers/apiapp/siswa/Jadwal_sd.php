<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Jadwal_sd extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
  }
  public function index_post()
  {
    $status = "";
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    if (isset($headers['x-token']))
      $token =  $headers['x-token'];
      $day = $this->post('day');
    if($day){
      $data = $this->mymodel->withquery("
      select 
        j.*,
        m.nama as nama_pelajaran,
        t.label as tahun_ajaran 
      from jadwal_sd j 
        join mata_pelajaran m on m.id_mata_pelajaran = j.id_mata_pelajaran 
        join tahun_ajaran t on t.id_tahun_ajaran = j.id_tahun_ajaran 
      where 
        j.hari='" . $day . "'", "result");
    }else{
      $data = $this->mymodel->withquery("select j.*,m.nama as nama_pelajaran,t.label as tahun_ajaran from jadwal_sd j join mata_pelajaran m on m.id_mata_pelajaran = j.id_mata_pelajaran join tahun_ajaran t on t.id_tahun_ajaran = j.id_tahun_ajaran", "result");
    }

    if (!empty($data)) {
      $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $data);
      $status = "200";
    } else {
      $msg = array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array());
      $status = "200";
    }

    $this->response($msg, $status);
  }
}
