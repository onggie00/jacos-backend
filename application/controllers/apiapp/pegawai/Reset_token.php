<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Reset_token extends REST_Controller {
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
        $new_token = '';

        $data = $this->mymodel->update('pegawai',array('token'=>$new_token),'token',$token);

        if ($data==0) {
          $msg = array('status' => 1, 'message'=>'Berhasil update token' ,'data'=>$new_token);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Gagal update token' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
