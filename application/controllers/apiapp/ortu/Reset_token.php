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
        $user_role = $this->post('user_role');
        $new_token = '';

        if($user_role=='sd'){
          $data = $this->mymodel->update('siswa_sd',array('token_ortu'=>$new_token),'token_ortu',$token);
        }elseif($user_role=='smp'){
          $data = $this->mymodel->update('siswa_smp',array('token_ortu'=>$new_token),'token_ortu',$token);
        }elseif($user_role=='sma'){
          $data = $this->mymodel->update('siswa_sma',array('token_ortu'=>$new_token),'token_ortu',$token);
        }elseif($user_role=='ft'){
          $data = $this->mymodel->update('siswa_ft',array('token_ortu'=>$new_token),'token_ortu',$token);
        }

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
