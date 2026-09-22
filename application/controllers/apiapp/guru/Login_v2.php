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

            //Priorias cek email dari Ortu FT, SMA, SMP, SD
            //FT
            $cek_email_ortu = $this->mymodel->withquery("select id_siswa_ft as id_siswa, nama_lengkap,nama_ayah,nama_ibu, email, 	email_ms_office_ortu as email_ms_office, token_ortu, token_expired_ortu from siswa_ft where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null","row");
            if (empty($cek_email_ortu)) {
            //SMA
            $cek_email_ortu = $this->mymodel->withquery("select id_siswa_sma as id_siswa, nama_lengkap,nama_ayah,nama_ibu, email, 	email_ms_office_ortu as email_ms_office, token_ortu, token_expired_ortu from siswa_sma where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null","row");
            if (empty($cek_email_ortu)) {
                //SMP
                $cek_email_ortu = $this->mymodel->withquery("select id_siswa_smp as id_siswa, nama_lengkap,nama_ayah,nama_ibu, email, 	email_ms_office_ortu as email_ms_office, token_ortu, token_expired_ortu from siswa_smp where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null","row");
                if (empty($cek_email_ortu)) {
                //SD
                $cek_email_ortu = $this->mymodel->withquery("select id_siswa_sd as id_siswa, nama_lengkap,nama_ayah,nama_ibu, email, 	email_ms_office_ortu as email_ms_office, token_ortu, token_expired_ortu from siswa_sd where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null","row");
                if (empty($cek_email_ortu)) {
                    $msg = array('status' => 0, 'message'=>'Akun tidak ditemukan' ,array());
                    $status="200";
                }
                else{
                    $data_ortu = $cek_email_ortu;
                    $get_siswa_aktif = $this->mymodel->getbywhere("siswa_sd_aktif", "id_siswa_sd", $cek_email_ortu->id_siswa, "row");
                    //insert id siswa
                    $data_ortu->id_siswa_aktif=$get_siswa_aktif->id_siswa_sd_aktif;
                    $data_ortu->is_active=$get_siswa_aktif->is_active;
                    if (date("Y-m-d H:i:s", strtotime($data_ortu->token_expired_ortu)) < date("Y-m-d H:i:s") || empty($data_ortu->token_expired_ortu)) {
                    $token_new = md5($data_ortu->email." ".date("YmdHis"));
                    $this->mymodel->update("siswa_sd",array("token_ortu" => $token_new, "token_expired_ortu" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office_ortu", $email );
                    $data_ortu->token = $token_new;
                    }
                    $data_ortu->jenjang = "sd";
                }
                }
                else{
                $data_ortu = $cek_email_ortu;
                $get_siswa_aktif = $this->mymodel->getbywhere("siswa_smp_aktif", "id_siswa_smp", $cek_email_ortu->id_siswa, "row");
                //insert id siswa
                $data_ortu->id_siswa_aktif=$get_siswa_aktif->id_siswa_smp_aktif;
                $data_ortu->is_active=$get_siswa_aktif->is_active;
                    if (date("Y-m-d H:i:s", strtotime($data_ortu->token_expired_ortu)) < date("Y-m-d H:i:s") || empty($data_ortu->token_expired_ortu)) {
                    $token_new = md5($data_ortu->email." ".date("YmdHis"));
                    $this->mymodel->update("siswa_smp",array("token_ortu" => $token_new, "token_expired_ortu" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office_ortu", $email );
                    $data_ortu->token = $token_new;
                    }
                    $data_ortu->jenjang = "smp";
                }
            }
            else{
                $data_ortu = $cek_email_ortu;
                $get_siswa_aktif = $this->mymodel->getbywhere("siswa_sma_aktif", "id_siswa_sma", $cek_email_ortu->id_siswa, "row");
                //insert id siswa  
                $data_ortu->id_siswa_aktif=$get_siswa_aktif->id_siswa_sma_aktif;
                $data_ortu->is_active=$get_siswa_aktif->is_active;
                if (date("Y-m-d H:i:s", strtotime($data_ortu->token_expired_ortu)) < date("Y-m-d H:i:s") || empty($data_ortu->token_expired_ortu)) {
                $token_new = md5($data_ortu->email." ".date("YmdHis"));
                $this->mymodel->update("siswa_sma",array("token_ortu" => $token_new, "token_expired_ortu" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office_ortu", $email );
                $data_ortu->token = $token_new;
                }
                $data_ortu->jenjang = "sma";
            }
            }
            else{
            $data_ortu = $cek_email_ortu;
            $get_siswa_aktif = $this->mymodel->getbywhere("siswa_ft_aktif", "id_siswa_ft", $cek_email_ortu->id_siswa, "row");
            //insert id siswa    
            $data_ortu->id_siswa_aktif=$get_siswa_aktif->id_siswa_ft_aktif;
            $data_ortu->is_active=$get_siswa_aktif->is_active;
            if (date("Y-m-d H:i:s", strtotime($data_ortu->token_expired_ortu)) < date("Y-m-d H:i:s") || empty($data_ortu->token_expired_ortu)) {
                $token_new = md5($data_ortu->email." ".date("YmdHis"));
                $this->mymodel->update("siswa_ft",array("token_ortu" => $token_new, "token_expired_ortu" => date("Y-m-d H:i:s",strtotime("+3 days"))), "email_ms_office_ortu", $email );
                $data_ortu->token = $token_new;
            }
            $data_ortu->jenjang = "ft";
            }

        if (!empty($data_guru)) {
          $data_guru->role="teacher";
          $data_login[] = $data_guru;
          if(!empty($data)){
            $data->role = "director";
            $data_login[] = $data;
          }
          if(!empty($data_ortu)){
            $data_ortu->role="parent";
            $data_login[] = $data_ortu;
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
