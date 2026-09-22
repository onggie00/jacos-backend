<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Update_device extends REST_Controller {
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
        $user_role = $this->post('user_role');
        $id_siswa_aktif = $this->post('id_siswa_aktif');
        $device_id = $this->post('device_id');

        if($user_role=='sd'){
          $data = $this->second_db->update('siswa_sd_aktif',array('device_id_siswa'=>$device_id),'id_siswa_sd_aktif',$id_siswa_aktif);
        }elseif($user_role=='smp'){
          $data = $this->second_db->update('siswa_smp_aktif',array('device_id_siswa'=>$device_id),'id_siswa_smp_aktif',$id_siswa_aktif);
        }elseif($user_role=='sma'){
          $data = $this->second_db->update('siswa_sma_aktif',array('device_id_siswa'=>$device_id),'id_siswa_sma_aktif',$id_siswa_aktif);
        }elseif($user_role=='ft'){
          $data = $this->second_db->update('siswa_ft_aktif',array('device_id_siswa'=>$device_id),'id_siswa_ft_aktif',$id_siswa_aktif);
        }

        if ($data== -1) {
          $msg = array('status' => 0, 'message'=>'Gagal update token' ,'data'=>array());
          $status="200";
        }
        else{
          $msg = array('status' => 1, 'message'=>'Berhasil update token' ,'data'=>$device_id);
          $status="200";
        }

        $this->response($msg,$status);
    }
}
