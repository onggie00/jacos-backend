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
      $headers[strtolower($name)] = $value;
    }
    if (isset($headers['x-token']))
      $token =  $headers['x-token'];
    $id = $this->post('id');
    $id_guru = $this->post('id_guru');
    $jenjang = $this->post('jenjang');
    // dd($id);
    $join="join jenis_kpi j on j.id_jenis_kpi = k.id_jenis_kpi join guru_$jenjang g on g.id_guru = k.id_guru";
    $order_by=" order by k.id_kpi desc";

    if ($id) {
      if ($jenjang == 'sd') {
        $data = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_sd k $join where id_kpi= $id and k.id_guru= $id_guru", "row");
        if($data){
          $data->file_piagam =  base_url("uploads/kpi_sd/") . $data->file_piagam;
        }
      } elseif ($jenjang == 'smp') {
        $data = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_smp k $join where id_kpi= $id and k.id_guru= $id_guru", "row");
        if($data){
          $data->file_piagam =  base_url("uploads/kpi_smp/") . $data->file_piagam;
        }
      } elseif ($jenjang == 'sma') {
        $data = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_sma k $join where id_kpi= $id and k.id_guru= $id_guru", "row");
        if($data){
          $data->file_piagam =  base_url("uploads/kpi_sma/") . $data->file_piagam;
        }
      } else {
        $data = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_ft k $join where id_kpi= $id and k.id_guru= $id_guru", "row");
        if($data){
          $data->file_piagam =  base_url("uploads/kpi_ft/") . $data->file_piagam;
        }
      }
    } else {
      if ($jenjang == 'sd') {
        $data = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_sd k $join where k.id_guru=" . $id_guru . $order_by, "result");
        foreach ($data as $key => $value) {
          if (!empty($value->file_piagam)) {
            $value->file_piagam = base_url("uploads/kpi_sd/") . $value->file_piagam;
          }
        }
      } elseif ($jenjang == 'smp') {
        $data = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_smp k $join where k.id_guru=" . $id_guru . $order_by, "result");
        foreach ($data as $key => $value) {
          if (!empty($value->file_piagam)) {
            $value->file_piagam = base_url("uploads/kpi_smp/") . $value->file_piagam;
          }
        }
      } elseif ($jenjang == 'sma') {
        $data = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_sma k $join where k.id_guru=" . $id_guru . $order_by, "result");
        foreach ($data as $key => $value) {
          if (!empty($value->file_piagam)) {
            $value->file_piagam = base_url("uploads/kpi_sma/") . $value->file_piagam;
          }
        }
      } else {
        $data = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_ft k $join where k.id_guru=" . $id_guru . $order_by, "result");
        foreach ($data as $key => $value) {
          if (!empty($value->file_piagam)) {
            $value->file_piagam = base_url("uploads/kpi_ft/") . $value->file_piagam;
          }
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
