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
        $data = array();
        $data_login = array();
        //Priorias cek email Guru dari FT, SMA, SMP, SD
        //FT
        $cek_email = $this->mymodel->withquery("select * from guru_ft where email_ms_office = '$email' and deleted_at is null","row");
        if (empty($cek_email)) {
          //SMA
          $cek_email = $this->mymodel->withquery("select * from guru_sma where email_ms_office = '$email' and deleted_at is null","row");
          if (empty($cek_email)) {
            //SMP
            $cek_email = $this->mymodel->withquery("select * from guru_smp where email_ms_office = '$email' and deleted_at is null","row");
            if (empty($cek_email)) {
              //SD
              $cek_email = $this->mymodel->withquery("select * from guru_sd where email_ms_office = '$email' and deleted_at is null","row");
              if (empty($cek_email)) {
                $msg = array('status' => 0, 'message'=>'Akun tidak ditemukan' ,array());
                $status="200";
              }
              else{
                $data = $cek_email;
                $data->jenjang="sd";
                if (date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s") || empty($data->token) || $data->token_expired == "0000-00-00 00:00:00") {
                  $token_new = md5($data->email." ".date("YmdHis"));
                  $this->mymodel->update("guru_sd",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
                  $data->token = $token_new;
                  $data->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
                  
                }
              }
            }
            else{
              $data = $cek_email;
              $data->jenjang="smp";
                if (date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s") || empty($data->token) || $data->token_expired == "0000-00-00 00:00:00") {
                  $token_new = md5($data->email." ".date("YmdHis"));
                  $this->mymodel->update("guru_smp",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
                  $data->token = $token_new;
                  $data->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
                
                }
            }
          }
          else{
            $data = $cek_email;
            $data->jenjang="sma";
            if (date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s") || empty($data->token) || $data->token_expired == "0000-00-00 00:00:00") {
              $token_new = md5($data->email." ".date("YmdHis"));
              $this->mymodel->update("guru_sma",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
              $data->token = $token_new;
              $data->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
              
            }
          }
        }
        else{
          $data = $cek_email;
          $data->jenjang="ft";
          if (date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s") || empty($data->token) || $data->token_expired == "0000-00-00 00:00:00") {
            $token_new = md5($data->email." ".date("YmdHis"));
            $this->mymodel->update("guru_ft",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
            $data->token = $token_new;
            $data->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
            
          }
        }

        //Priorias cek email Pimpinan dari FT, SMA, SMP, SD
        //FT
        $cek_email_pimpinan = $this->mymodel->withquery("select * from pimpinan_sma where email_ms_office = '".$email."'","row");
        if (empty($cek_email_pimpinan)) {
          //SMA
          $cek_email_pimpinan = $this->mymodel->withquery("select * from pimpinan_smp where email_ms_office = '".$email."'","row");
          if (empty($cek_email_pimpinan)) {
            //SMP
            $cek_email_pimpinan = $this->mymodel->withquery("select * from pimpinan_sd where email_ms_office = '".$email."'","row");
            if (empty($cek_email_pimpinan)) {
              $msg = array('status' => 0, 'message'=>'Akun tidak ditemukan' ,array());
              $status="200";
            }
            else{
              $data_pimpinan = $cek_email_pimpinan;
              $data_pimpinan->jenjang="sd";
              if (date("Y-m-d H:i:s", strtotime($data_pimpinan->token_expired)) < date("Y-m-d H:i:s")) {
                $token_new = md5($data_pimpinan->email." ".date("YmdHis"));
                $this->mymodel->update("pimpinan_sd",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
                $data_pimpinan->token = $token_new;
                $data_pimpinan->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
                
              }
            }
          }
          else{
            $data_pimpinan = $cek_email_pimpinan;
            $data_pimpinan->jenjang="smp";
            if (date("Y-m-d H:i:s", strtotime($data_pimpinan->token_expired)) < date("Y-m-d H:i:s")) {
              $token_new = md5($data_pimpinan->email." ".date("YmdHis"));
              $this->mymodel->update("pimpinan_smp",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
              $data_pimpinan->token = $token_new;
              $data_pimpinan->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
              
            }
          }
        }
        else{
          $data_pimpinan = $cek_email_pimpinan;
          $data_pimpinan->jenjang="sma";
          if (date("Y-m-d H:i:s", strtotime($data_pimpinan->token_expired)) < date("Y-m-d H:i:s")) {
            $token_new = md5($data_pimpinan->email." ".date("YmdHis"));
            $this->mymodel->update("pimpinan_sma",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
            $data_pimpinan->token = $token_new;
            $data_pimpinan->token_expired=date("Y-m-d H:i:s",strtotime("+3 days"));
            
          }
        }

        if (!empty($data)) {
          $data->role="teacher";
          $data_login[] = $data;
          if(!empty($data_pimpinan)){
            $data_pimpinan->role = "director";
            $data_login[] = $data_pimpinan;
          }
          $msg = array('status' => 1, 'message'=>'Berhasil melakukan login' ,'data'=>$data_login);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
