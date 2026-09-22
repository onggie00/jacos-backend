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
          $headers[strtolower($name)] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $email = $this->post("email");
        $data_login = array();
        //Cek email Pimpinan: prioritas sma, smp, sd
        $data = null;

        //Jika belum ketemu, cek pimpinan_sma
        if (empty($data)) {
          $cek_email = $this->mymodel->withquery("select * from pimpinan_sma where email_ms_office = '".$email."' order by id_pimpinan desc limit 1","row");
          if (!empty($cek_email)) {
            $data = $cek_email;
            $data->jenjang = "sma";
            if (empty($data->token_expired) || $data->token_expired == "0000-00-00 00:00:00" || date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s")) {
              $token_new = md5($email." ".date("YmdHis"));
              $this->mymodel->update("pimpinan_sma",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
              $data->token = $token_new;
              $data->token_expired = date("Y-m-d H:i:s",strtotime("+3 days"));
            }
          }
        }

        //Jika belum ketemu, cek pimpinan_smp
        if (empty($data)) {
          $cek_email = $this->mymodel->withquery("select * from pimpinan_smp where email_ms_office = '".$email."' order by id_pimpinan desc limit 1","row");
          if (!empty($cek_email)) {
            $data = $cek_email;
            $data->jenjang = "smp";
            if (empty($data->token_expired) || $data->token_expired == "0000-00-00 00:00:00" || date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s")) {
              $token_new = md5($email." ".date("YmdHis"));
              $this->mymodel->update("pimpinan_smp",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
              $data->token = $token_new;
              $data->token_expired = date("Y-m-d H:i:s",strtotime("+3 days"));
            }
          }
        }

        //Jika belum ketemu, cek pimpinan_sd
        if (empty($data)) {
          $cek_email = $this->mymodel->withquery("select * from pimpinan_sd where email_ms_office = '".$email."' order by id_pimpinan desc limit 1","row");
          if (!empty($cek_email)) {
            $data = $cek_email;
            $data->jenjang = "sd";
            if (empty($data->token_expired) || $data->token_expired == "0000-00-00 00:00:00" || date("Y-m-d H:i:s", strtotime($data->token_expired)) < date("Y-m-d H:i:s")) {
              $token_new = md5($email." ".date("YmdHis"));
              $this->mymodel->update("pimpinan_sd",array("token" => $token_new, "token_expired" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office", $email );
              $data->token = $token_new;
              $data->token_expired = date("Y-m-d H:i:s",strtotime("+3 days"));
            }
          }
        }
        
        //Jika pimpinan ditemukan, langsung return (tidak lanjut cek guru/ortu)
        if (!empty($data)) {
          $data->role="director";
          $data_login[] = $data;
          $msg = array('status' => 1, 'message'=>'Berhasil melakukan login' ,'data'=>$data_login);
          $status="200";
          $this->response($msg,$status);
          return;
        }

        //Jika tidak ditemukan di tabel pimpinan manapun
        $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
        $status="200";
        $this->response($msg,$status);
        return;
    }
}
