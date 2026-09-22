<?php
header("Access-Control-Allow-Origin: *"); header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); header('Access-Control-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Update_kpi_guru extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }

    function index_post() {
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[strtolower($name)] = $value;
      }
      if(isset($headers['x-token']))$token =  $headers['x-token'];
      $id=$this->post('id');
      $jenjang=$this->post('jenjang');
     
      $data = array(
        "is_approve" => $this->post('is_approve'),
      );
      // dd($data);


      if($jenjang=='sd'){
        $update_kpi = $this->mymodel->update("kpi_sd", $data, "id_kpi", $id);
      }elseif($jenjang=='smp'){
        $update_kpi = $this->mymodel->update("kpi_smp", $data, "id_kpi", $id);
      }elseif($jenjang=='sma'){
        $update_kpi = $this->mymodel->update("kpi_sma", $data, "id_kpi", $id);
      }elseif($jenjang=='ft'){
        $update_kpi = $this->mymodel->update("kpi_ft", $data, "id_kpi", $id);
      }

      if ($update_kpi == -1) {
        $msg = array('status' => 0, 'message' => 'Gagal update data guru ', 'data' => array());
      } else {
        $msg = array('status' => 1, 'message' => 'Berhasil update data', 'data' => array());
      }
      $this->response($msg);
    }
}
