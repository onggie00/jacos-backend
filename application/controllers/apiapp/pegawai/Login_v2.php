<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Login_v2 extends REST_Controller {
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

        $email = $this->post("email");

        $data = $this->mymodel->withquery("select * from pegawai where email_ms_office = '".$email."'","result");

        if (!empty($data[0])) {
            $token_new = md5($data[0]->email." ".date("YmdHis"));
            if (date("Y-m-d H:i:s", strtotime($data[0]->token_expired)) < date("Y-m-d H:i:s") || empty($data[0]->token) || $data[0]->token_expired == "0000-00-00 00:00:00") {
              $this->mymodel->update("pegawai",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
              $data[0]->token = $token_new;
              $data[0]->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
            }
          $data[0]->role="employee";
          $msg = array('status' => 1, 'message'=>'Berhasil melakukan login' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>$data);
          $status="200";
        }

        $this->response($msg,$status);
    }
}
