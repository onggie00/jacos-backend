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
          $headers[strtolower($name)] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];
        $jenjang = $this->post('jenjang');
        $id_guru = $this->post('id_guru');
        $device_id = $this->post('device_id');

        if($jenjang=='sd'){
          $data = $this->mymodel->update('guru_sd',array('device_id'=>$device_id),'id_guru',$id_guru);
        }elseif($jenjang=='smp'){
          $data = $this->mymodel->update('guru_smp',array('device_id'=>$device_id),'id_guru',$id_guru);
        }elseif($jenjang=='sma'){
          $data = $this->mymodel->update('guru_sma',array('device_id'=>$device_id),'id_guru',$id_guru);
        }elseif($jenjang=='ft'){
          $data = $this->mymodel->update('guru_ft',array('device_id'=>$device_id),'id_guru',$id_guru);
        }

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
