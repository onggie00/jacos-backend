<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Update_tagihan_expired extends REST_Controller
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

    $id_transaksi = explode(',', $this->post('id_transaksi'));

    foreach ($id_transaksi as $key => $item) {
      //cek status_transaksi
      $cek = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = '" . $item . "'", "row");
      if ($cek->status_transaksi != "2" && date("Y-m-d H:i:s") > date("Y-m-d H:i:s", strtotime($cek->expired_date))) {
        $update = $this->mymodel->update("transaksi_spp", array("status_transaksi" => "0", "kode_tagihan" => "", "file_slip" => ""), "id_transaksi", $item);
        if ($update != 0) {
          $msg = array('status' => 0, 'message' => 'update data gagal', 'data' => array());
          $status = "200";
        }
      }
      else{
        $msg = array('status' => 1, 'message' => 'Data sudah lunas', 'data' => array());
        $status = "200";
      }
    }

    $msg = array('status' => 1, 'message' => 'Berhasil update data', 'data' => array());
    $status = "200";

    $this->response($msg, $status);
  }
}
