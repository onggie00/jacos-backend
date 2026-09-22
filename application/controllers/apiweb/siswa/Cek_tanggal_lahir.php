<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Cek_tanggal_lahir extends REST_Controller {
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

        $tanggal_lahir = $this->get('tanggal_lahir');
        $tipe_siswa = $this->get('tipe_siswa');
        $data = null;
        //cek data urut ft, sma, smp, sd
        if (!empty($tanggal_lahir)) {
            $data = $this->mymodel->getbywhere("pengaturan_tanggal_lahir","jenjang",$tipe_siswa,"row");
        }

        if ($data->date < date("Y-m-d",strtotime($tanggal_lahir))) {
          $msg = array('status' => 0, 'message'=>'Umur belum memenuhi syarat minimal pendaftaran' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 1, 'message'=>'Usia sudah sesuai' ,'data'=>$data);
          $status="200";
        }

        $this->response($msg,$status);
    }
}