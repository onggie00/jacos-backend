<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_all_kpi_guru extends REST_Controller
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
      $headers[strtolower($name)] = $value;
    }
    if (isset($headers['x-token']))
      $token =  $headers['x-token'];
    $result=[];
    // dd($id);
    $join_sd="join jenis_kpi j on j.id_jenis_kpi = k.id_jenis_kpi join guru_sd g on g.id_guru = k.id_guru";
    $join_smp="join jenis_kpi j on j.id_jenis_kpi = k.id_jenis_kpi join guru_smp g on g.id_guru = k.id_guru";
    $join_sma="join jenis_kpi j on j.id_jenis_kpi = k.id_jenis_kpi join guru_sma g on g.id_guru = k.id_guru";
    $join_ft="join jenis_kpi j on j.id_jenis_kpi = k.id_jenis_kpi join guru_ft g on g.id_guru = k.id_guru";

    $data_sd = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_sd k $join_sd ", "result");
      foreach ($data_sd as $key => $value) {
        if (!empty($value->file_piagam)) {
          $value->file_piagam = base_url("uploads/kpi_sd/") . $value->file_piagam;
        }
        $value->jenjang="sd";
        $result[]=$value;
      }
      
      $data_smp = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_smp k $join_smp ", "result");
      foreach ($data_smp as $key => $value) {
        if (!empty($value->file_piagam)) {
          $value->file_piagam = base_url("uploads/kpi_smp/") . $value->file_piagam;
        }
        $value->jenjang="smp";
        $result[]=$value;
      }
      $data_sma = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_sma k $join_sma ", "result");
      foreach ($data_sma as $key => $value) {
        if (!empty($value->file_piagam)) {
          $value->file_piagam = base_url("uploads/kpi_sma/") . $value->file_piagam;
        }
        $value->jenjang="sma";
        $result[]=$value;
      }
      $data_ft = $this->mymodel->withquery("select k.*,j.*,g.nama_lengkap from kpi_ft k $join_ft ", "result");
      foreach ($data_ft as $key => $value) {
        if (!empty($value->file_piagam)) {
          $value->file_piagam = base_url("uploads/kpi_ft/") . $value->file_piagam;
        }
        $value->jenjang="ft";
        $result[]=$value;
      }
      
    if (!empty($result)) {
      $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $result);
      $status = "200";
    } else {
      $msg = array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array());
      $status = "200";
    }

    $this->response($msg, $status);
  }
}
