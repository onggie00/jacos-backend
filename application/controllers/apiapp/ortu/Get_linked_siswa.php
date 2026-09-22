<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_linked_siswa extends REST_Controller {
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
        $data = array();
        //FT
        $cek_email_ft = $this->mymodel->withquery("select id_siswa_ft as id_siswa, nama_lengkap, email,	email_ms_office_ortu, email_ms_office as email_ms_siswa from siswa_ft where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null order by nama_lengkap ASC","result");
        if (!empty($cek_email_ft)) {
          foreach ($cek_email_ft as $key => $value) {
            $get_siswa_aktif = $this->mymodel->withquery("select id_siswa_ft_aktif, is_active from siswa_ft_aktif where id_siswa_ft = '".$value->id_siswa."'", "row");
            $value->id_siswa_aktif=$get_siswa_aktif->id_siswa_ft_aktif;
            $value->is_active=$get_siswa_aktif->is_active;
            $value->jenjang = "ft";
            $value->role = "parent";

            $data[] = $value;
          }
        }
        //SMA
        $cek_email_sma = $this->mymodel->withquery("select id_siswa_sma as id_siswa, nama_lengkap, email, email_ms_office_ortu, email_ms_office as email_ms_siswa from siswa_sma where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null order by nama_lengkap ASC","result");
        if (!empty($cek_email_sma)) {
          foreach ($cek_email_sma as $key => $value) {
            $get_siswa_aktif = $this->mymodel->withquery("select id_siswa_sma_aktif, is_active from siswa_sma_aktif where id_siswa_sma = '".$value->id_siswa."'", "row");
            $value->id_siswa_aktif=$get_siswa_aktif->id_siswa_sma_aktif;
            $value->is_active=$get_siswa_aktif->is_active;
            $value->jenjang = "sma";
            $value->role = "parent";

            $data[] = $value;
          }
        }
        //SMP
        $cek_email_smp = $this->mymodel->withquery("select id_siswa_smp as id_siswa, nama_lengkap, email, email_ms_office_ortu, email_ms_office as email_ms_siswa from siswa_smp where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null order by nama_lengkap ASC","result");
        if (!empty($cek_email_smp)) {
          foreach ($cek_email_smp as $key => $value) {
            $get_siswa_aktif = $this->mymodel->withquery("select id_siswa_smp_aktif, is_active from siswa_smp_aktif where id_siswa_smp = '".$value->id_siswa."'", "row");
            $value->id_siswa_aktif=$get_siswa_aktif->id_siswa_smp_aktif;
            $value->is_active=$get_siswa_aktif->is_active;
            $value->jenjang = "smp";
            $value->role = "parent";

            $data[] = $value;
          }
        }
        //SD
        $cek_email_sd = $this->mymodel->withquery("select id_siswa_sd as id_siswa, nama_lengkap, email, email_ms_office_ortu, email_ms_office as email_ms_siswa from siswa_sd where email_ms_office_ortu = '".$email."' and deleted_at_ortu is null order by nama_lengkap ASC","result");
        if (!empty($cek_email_sd)) {
          foreach ($cek_email_sd as $key => $value) {
            $get_siswa_aktif = $this->mymodel->withquery("select id_siswa_sd_aktif, is_active from siswa_sd_aktif where id_siswa_sd = '".$value->id_siswa."'", "row");
            $value->id_siswa_aktif=$get_siswa_aktif->id_siswa_sd_aktif;
            $value->is_active=$get_siswa_aktif->is_active;
            $value->jenjang = "sd";
            $value->role = "parent";

            $data[] = $value;
          }
        }

        if (!empty($data)) {
          $msg = array('status' => 1, 'message'=>'Berhasil mengambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
