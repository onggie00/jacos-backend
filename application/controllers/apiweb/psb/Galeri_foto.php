<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Galeri_foto extends REST_Controller {
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
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];
        $data = $this->mymodel->withquery("select * from galeri_foto","result");
          foreach($data as $key => $value){
            if (!empty($value->foto)) {
              $value->foto = /* base_url() */"https://admin.labschoolcibubur.sch.id/"."uploads/galeri_foto/".$value->foto;
            }
          }

        if (!empty($data)) {
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
