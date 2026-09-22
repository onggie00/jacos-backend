<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Cek_email extends REST_Controller {
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

      if(isset($headers['user_role']))
        $role =  $headers['user_role'];

        $email = $this->get('email');
        $nama_lengkap = $this->get('nama_lengkap');
        $tipe_siswa = $this->get('tipe_siswa');
        $data = null;
        //cek data urut ft, sma, smp, sd
        if ($tipe_siswa == "ft" && !empty($email) && !empty($nama_lengkap)) {
          $data = $this->mymodel->withquery("select s.id_siswa_ft as id_siswa, s.email, st.status_lulus from siswa_ft s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%".$this->db->escape_like_str($email)."%' and nama_lengkap like '%".$this->db->escape_like_str($nama_lengkap)."%' and is_show = 1","row");
          if (!empty($data)) {
            $data->tipe_siswa = "ft";
          }
        }

        if ($tipe_siswa == "sma" && !empty($email) && !empty($nama_lengkap)) {
          $data = $this->mymodel->withquery("select s.id_siswa_sma as id_siswa, s.email, st.status_lulus from siswa_sma s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%".$this->db->escape_like_str($email)."%' and nama_lengkap like '%".$this->db->escape_like_str($nama_lengkap)."%' and is_show = 1","row");
          if (!empty($data)) {
            $data->tipe_siswa = "sma";
          }
        }

        if ($tipe_siswa == "smp" && !empty($email) && !empty($nama_lengkap)) {
          $data = $this->mymodel->withquery("select s.id_siswa_smp as id_siswa, s.email, st.status_lulus from siswa_smp s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%".$this->db->escape_like_str($email)."%' and nama_lengkap like '%".$this->db->escape_like_str($nama_lengkap)."%' and is_show = 1","row");
          if (!empty($data)) {
            $data->tipe_siswa = "smp";
          }
        }

        if ($tipe_siswa == "sd" && !empty($email) && !empty($nama_lengkap)) {
          $data = $this->mymodel->withquery("select s.id_siswa_sd as id_siswa, s.email, st.status_lulus from siswa_sd s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%".$this->db->escape_like_str($email)."%' and nama_lengkap like '%".$this->db->escape_like_str($nama_lengkap)."%' and is_show = 1","row");
          if (!empty($data)) {
            $data->tipe_siswa = "sd";
          }
        }

        if (!empty($data) && !empty($tipe_siswa)) {
          $msg = array('status' => 0, 'message'=>'Email sudah terdaftar' ,'data'=>$data);
          $status="200";
        }
        else if(empty($email) || empty($nama_lengkap)) {
          $msg = array('status' => 0, 'message'=>'Email / Nama Lengkap Kosong' ,'data'=>$data);
          $status="200";
        }
        else if(empty($data) && !empty($tipe_siswa)){
          $msg = array('status' => 1, 'message'=>'Email masih tersedia' ,'data'=>array());
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Tipe siswa tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}