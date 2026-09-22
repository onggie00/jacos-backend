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
        $id_pegawai = $this->post('id_pegawai');
        $device_id = $this->post('device_id');

        $data = $this->mymodel->update('pegawai',array('device_id'=>$device_id),'id_pegawai',$id_pegawai);

        if ($data== -1) {
          $msg = array('status' => 0, 'message'=>'Gagal update device id' ,'data'=>array());
          $status="200";
        }
        else{
          $msg = array('status' => 1, 'message'=>'Berhasil update device id' ,'data'=>$device_id);
          $status="200";
        }

        $this->response($msg,$status);
    }
}
