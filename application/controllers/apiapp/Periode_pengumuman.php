<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Periode_pengumuman extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
  }
  public function index_get()
  {
    $status = "";
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    if (isset($headers['x-token']))
      $token =  $headers['x-token'];

    $data = $this->mymodel->withquery("select * from web_periode_pengumuman", "result");

    if (!empty($data)) {
      foreach ($data as $key => $value) {
        $value->periode_mulai_teks = formatTanggal($value->periode_pengumuman_mulai);
        $value->periode_selesai_teks = formatTanggal($value->periode_pengumuman_selesai);
        $value->periode_mulai_teks_unformated = $value->periode_pengumuman_mulai;
        $value->periode_selesai_teks_unformated  = $value->periode_pengumuman_selesai;
        if (date("Y-m-d H:i:s", strtotime($value->periode_pengumuman_mulai)) <= date("Y-m-d H:i:s") && date("Y-m-d H:i:s", strtotime($value->periode_pengumuman_selesai)) >= date("Y-m-d H:i:s")) {
          $value->is_show = 1;
        } else {
          $value->is_show = 2;
        }
      }
      $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $data);
      $status = "200";
    } else {
      $msg = array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array());
      $status = "200";
    }

    $this->response($msg, $status);
  }
}
