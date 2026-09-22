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
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $email = $this->post("email_office");

        //Priorias cek email dari FT, SMA, SMP, SD
        //FT
        $cek_email = $this->mymodel->withquery("select id_siswa_ft as id_siswa, nama_lengkap,nama_ayah,nama_ibu, email, 	email_ms_office_ortu as email_ms_office, token_ortu, token_expired_ortu from siswa_ft where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null","row");
        if (empty($cek_email)) {
          //SMA
          $cek_email = $this->mymodel->withquery("select id_siswa_sma as id_siswa, nama_lengkap,nama_ayah,nama_ibu, email, 	email_ms_office_ortu as email_ms_office, token_ortu, token_expired_ortu from siswa_sma where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null","row");
          if (empty($cek_email)) {
            //SMP
            $cek_email = $this->mymodel->withquery("select id_siswa_smp as id_siswa, nama_lengkap,nama_ayah,nama_ibu, email, 	email_ms_office_ortu as email_ms_office, token_ortu, token_expired_ortu from siswa_smp where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null","row");
            if (empty($cek_email)) {
              //SD
              $cek_email = $this->mymodel->withquery("select id_siswa_sd as id_siswa, nama_lengkap,nama_ayah,nama_ibu, email, 	email_ms_office_ortu as email_ms_office, token_ortu, token_expired_ortu from siswa_sd where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null","row");
              if (empty($cek_email)) {
                $msg = array('status' => 0, 'message'=>'Akun tidak ditemukan' ,array());
                $status="200";
              }
              else{
                $data = $cek_email;
                $get_siswa_aktif = $this->mymodel->getbywhere("siswa_sd_aktif", "id_siswa_sd", $cek_email->id_siswa, "row");
                //insert id siswa
                $data->id_siswa_aktif=$get_siswa_aktif->id_siswa_sd_aktif;
                $data->is_active=$get_siswa_aktif->is_active;
                if (date("Y-m-d H:i:s", strtotime($data->token_expired_ortu)) < date("Y-m-d H:i:s") || empty($data->token_expired_ortu)) {
                  $token_new = md5($data->email." ".date("YmdHis"));
                  $this->mymodel->update("siswa_sd",array("token_ortu" => $token_new, "token_expired_ortu" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office_ortu", $email );
                  $data->token = $token_new;
                }
                $data->jenjang = "sd";
              }
            }
            else{
              $data = $cek_email;
              $get_siswa_aktif = $this->mymodel->getbywhere("siswa_smp_aktif", "id_siswa_smp", $cek_email->id_siswa, "row");
              //insert id siswa
              $data->id_siswa_aktif=$get_siswa_aktif->id_siswa_smp_aktif;
              $data->is_active=$get_siswa_aktif->is_active;
                if (date("Y-m-d H:i:s", strtotime($data->token_expired_ortu)) < date("Y-m-d H:i:s") || empty($data->token_expired_ortu)) {
                  $token_new = md5($data->email." ".date("YmdHis"));
                  $this->mymodel->update("siswa_smp",array("token_ortu" => $token_new, "token_expired_ortu" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office_ortu", $email );
                  $data->token = $token_new;
                }
                $data->jenjang = "smp";
            }
          }
          else{
            $data = $cek_email;
            $get_siswa_aktif = $this->mymodel->getbywhere("siswa_sma_aktif", "id_siswa_sma", $cek_email->id_siswa, "row");
            //insert id siswa  
            $data->id_siswa_aktif=$get_siswa_aktif->id_siswa_sma_aktif;
            $data->is_active=$get_siswa_aktif->is_active;
            if (date("Y-m-d H:i:s", strtotime($data->token_expired_ortu)) < date("Y-m-d H:i:s") || empty($data->token_expired_ortu)) {
              $token_new = md5($data->email." ".date("YmdHis"));
              $this->mymodel->update("siswa_sma",array("token_ortu" => $token_new, "token_expired_ortu" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office_ortu", $email );
              $data->token = $token_new;
            }
            $data->jenjang = "sma";
          }
        }
        else{
          $data = $cek_email;
          $get_siswa_aktif = $this->mymodel->getbywhere("siswa_ft_aktif", "id_siswa_ft", $cek_email->id_siswa, "row");
          //insert id siswa    
          $data->id_siswa_aktif=$get_siswa_aktif->id_siswa_ft_aktif;
          $data->is_active=$get_siswa_aktif->is_active;
          if (date("Y-m-d H:i:s", strtotime($data->token_expired_ortu)) < date("Y-m-d H:i:s") || empty($data->token_expired_ortu)) {
            $token_new = md5($data->email." ".date("YmdHis"));
            $this->mymodel->update("siswa_ft",array("token_ortu" => $token_new, "token_expired_ortu" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office_ortu", $email );
            $data->token = $token_new;
          }
          $data->jenjang = "ft";
        }

        if (!empty($data)) {
          $data->role="parent";
          $msg = array('status' => 1, 'message'=>'Berhasil melakukan login' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
