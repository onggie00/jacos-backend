<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Profile extends REST_Controller {
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

      if (!empty($token) && !empty($role)) {
        //get user
        if ($role == "sd") {
          $user = $this->second_db->withquery("select id_siswa_sd, nama_lengkap,foto_peserta from siswa_sd where token = '".$token."'","row");
          $user->foto_peserta=base_url('uploads/siswa_sd/').$user->foto_peserta;
        }

      }
      else{
        $msg = array('status' => 0, 'message'=>'Token anda kosong');
        $status="200";
      }

        $data = $user;

        if (!empty($data)) {
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}