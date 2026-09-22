<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_perguruan_tinggi extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_get()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
        $where="";
        
        // dd($where);
        $today = date("Y-m-d");
        $provinsi = $this->get("id_provinsi");
        if (empty($provinsi)) {
          $data = $this->second_db->withquery("select pt.id, pt.nama_pt, pt.inisial, p.name as provinsi, r.name as kota, pt.logo_ptn, pt.deskripsi from pt_perguruan_tinggi pt join provinces p on pt.provinsi = p.id left join regencies r on pt.kota = r.id order by pt.nama_pt ASC","result");
        }
        else{
          $data = $this->second_db->withquery("select pt.id, pt.nama_pt, pt.inisial, p.name as provinsi, r.name as kota, pt.logo_ptn, pt.deskripsi from pt_perguruan_tinggi pt join provinces p on pt.provinsi = p.id left join regencies r on pt.kota = r.id where provinsi = '".$provinsi."' order by pt.nama_pt ASC","result");
        }
        if (!empty($data)) {
          foreach ($data as $key => $value) {
            if (!empty($value->logo_ptn)) {
              $base_url = "https://admin.labschoolcibubur.sch.id/";
              $value->logo_ptn = $base_url."uploads/pt_perguruan_tinggi/".$value->logo_ptn;
            }
          }
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
