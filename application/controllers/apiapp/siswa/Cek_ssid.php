<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cek_ssid extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];
        $ssid = $this->post("nama_ssid");

        $data = $this->mymodel->withquery("select * from pengaturan_whitelist_ssid where nama_ssid = '".$ssid."'","result");

        if (!empty($data)) {
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          //$msg = array('status' => 1, 'message'=>'SSID tidak ditemukan, namun berhasil melakukan pengecekan' ,'data'=>array("nama_ssid" =>"Labschool Cibubur WIFI", "id_pengaturan" =>"1") );
          $msg = array('status' => 0, 'message'=>'SSID tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
