<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_anggaran extends REST_Controller
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
      $headers[strtolower($name)] = $value;
    }
    if (isset($headers['x-token']))
      $token =  $headers['x-token'];

    $id = $this->post('id');
    $jenjang = $this->post('jenjang');
    // dd($id);

    if ($id) {
      if ($jenjang == 'sd') {
        $data = $this->mymodel->withquery("select k.* from program_anggaran_sd k where id= $id", "row");
      } elseif ($jenjang == 'smp') {
        $data = $this->mymodel->withquery("select k.* from program_anggaran_smp k where id= $id", "row");
      } else {
        $data = $this->mymodel->withquery("select k.* from program_anggaran_sma k where id= $id", "row");
      }
    } else {
      if ($jenjang == 'sd') {
        $data = $this->mymodel->withquery("select k.* from program_anggaran_sd k", "result");
      } elseif ($jenjang == 'smp') {
        $data = $this->mymodel->withquery("select k.* from program_anggaran_smp k", "result");
      } else {
        $data = $this->mymodel->withquery("select k.* from program_anggaran_sma k", "result");
      }
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
