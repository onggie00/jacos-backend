<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Login extends REST_Controller {
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
          $headers[strtolower($name)] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $email = $this->post("email");
        $data_login = array();
        //Priorias cek email Pimpinan dari FT, SMA, SMP, SD
        //FT
        $cek_email = $this->mymodel->withquery("select * from pimpinan_sma where email_ms_office = '".$email."'","row");
        if (empty($cek_email)) {
          //SMA
          $cek_email = $this->mymodel->withquery("select * from pimpinan_smp where email_ms_office = '".$email."'","row");
          if (empty($cek_email)) {
            //SMP
            $cek_email = $this->mymodel->withquery("select * from pimpinan_sd where email_ms_office = '".$email."'","row");
            if (empty($cek_email)) {
              $msg = array('status' => 0, 'message'=>'Akun tidak ditemukan' ,array());
              $status="200";
            }
            else{
              $data = $cek_email;
              $data->jenjang="sd";
              if (date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s")) {
                $token_new = md5($data->email." ".date("YmdHis"));
                $this->mymodel->update("pimpinan_sd",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
                $data->token = $token_new;
                $data->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
                
              }
            }
          }
          else{
            $data = $cek_email;
            $data->jenjang="smp";
            if (date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s")) {
              $token_new = md5($data->email." ".date("YmdHis"));
              $this->mymodel->update("pimpinan_smp",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
              $data->token = $token_new;
              $data->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
              
            }
          }
        }
        else{
          $data = $cek_email;
          $data->jenjang="sma";
          if (date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s")) {
            $token_new = md5($data->email." ".date("YmdHis"));
            $this->mymodel->update("pimpinan_sma",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
            $data->token = $token_new;
            $data->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
            
          }
        }
        
        //Priorias cek email Guru dari FT, SMA, SMP, SD
        //FT
        $cek_email_guru = $this->mymodel->withquery("select * from guru_ft where email_ms_office = '$email' and deleted_at is null","row");
        if (empty($cek_email_guru)) {
          //SMA
          $cek_email_guru = $this->mymodel->withquery("select * from guru_sma where email_ms_office = '$email' and deleted_at is null","row");
          if (empty($cek_email_guru)) {
            //SMP
            $cek_email_guru = $this->mymodel->withquery("select * from guru_smp where email_ms_office = '$email' and deleted_at is null","row");
            if (empty($cek_email_guru)) {
              //SD
              $cek_email_guru = $this->mymodel->withquery("select * from guru_sd where email_ms_office = '$email' and deleted_at is null","row");
              if (empty($cek_email_guru)) {
                $msg = array('status' => 0, 'message'=>'Akun tidak ditemukan' ,array());
                $status="200";
              }
              else{
                $data_guru = $cek_email_guru;
                $data_guru->jenjang="sd";
                if (date("Y-m-d H:i:s", strtotime($data_guru->token_expired)) < date("Y-m-d H:i:s") || empty($data_guru->token) || $data_guru->token_expired == "0000-00-00 00:00:00") {
                  $token_new = md5($data_guru->email." ".date("YmdHis"));
                  $this->mymodel->update("guru_sd",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
                  $data_guru->token = $token_new;
                  $data_guru->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
                  
                }
              }
            }
            else{
              $data_guru = $cek_email_guru;
              $data_guru->jenjang="smp";
                if (date("Y-m-d H:i:s", strtotime($data_guru->token_expired)) < date("Y-m-d H:i:s") || empty($data_guru->token) || $data_guru->token_expired == "0000-00-00 00:00:00") {
                  $token_new = md5($data_guru->email." ".date("YmdHis"));
                  $this->mymodel->update("guru_smp",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
                  $data_guru->token = $token_new;
                  $data_guru->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
                
                }
            }
          }
          else{
            $data_guru = $cek_email_guru;
            $data_guru->jenjang="sma";
            if (date("Y-m-d H:i:s", strtotime($data_guru->token_expired)) < date("Y-m-d H:i:s") || empty($data_guru->token) || $data_guru->token_expired == "0000-00-00 00:00:00") {
              $token_new = md5($data_guru->email." ".date("YmdHis"));
              $this->mymodel->update("guru_sma",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
              $data_guru->token = $token_new;
              $data_guru->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
              
            }
          }
        }
        else{
          $data_guru = $cek_email_guru;
          $data_guru->jenjang="ft";
          if (date("Y-m-d H:i:s", strtotime($data_guru->token_expired)) < date("Y-m-d H:i:s") || empty($data_guru->token) || $data_guru->token_expired == "0000-00-00 00:00:00") {
            $token_new = md5($data_guru->email." ".date("YmdHis"));
            $this->mymodel->update("guru_ft",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
            $data_guru->token = $token_new;
            $data_guru->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
            
          }
        }


        if (!empty($data)) {
          $data->role="director";
          $data_login[] = $data;
          if(!empty($data_guru)){
            $data_guru->role="teacher";
            $data_login[] = $data_guru;
          }
          $msg = array('status' => 1, 'message'=>'Berhasil melakukan login' ,'data'=>$data_login);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>$data_login);
          $status="200";
        }

        $this->response($msg,$status);
    }
}
