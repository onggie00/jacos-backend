<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_kpi extends REST_Controller
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
    $id = $this->post('id');
    $id_pegawai = $this->post('id_pegawai');
    $order_by=" order by k.id_kpi desc";

    // dd($id);
    $join="join jenis_kpi_pegawai j on j.id_jenis_kpi = k.id_jenis_kpi join pegawai p on p.id_pegawai = k.id_pegawai";

    if ($id) {
      $data = $this->mymodel->withquery("select k.*,j.*,p.nama_lengkap from kpi_pegawai k $join where id_kpi= $id and k.id_pegawai= $id_pegawai", "row");
      if($data){
        $data->file_piagam =  base_url("uploads/kpi_pegawai/") . $data->file_piagam;
      }
    } else {
      $data = $this->mymodel->withquery("select k.*,j.*,p.nama_lengkap from kpi_pegawai k $join where k.id_pegawai=" . $id_pegawai .$order_by, "result");
        foreach ($data as $key => $value) {
          if (!empty($value->file_piagam)) {
            $value->file_piagam = base_url("uploads/kpi_pegawai/") . $value->file_piagam;
          }
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
