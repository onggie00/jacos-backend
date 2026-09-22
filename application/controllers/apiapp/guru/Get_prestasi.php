<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_prestasi extends REST_Controller {
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
      $id = $this->post('id');
      $id_guru = $this->post('id_guru');
      $jenjang = $this->post('jenjang');

      if($jenjang=="sd"){
        $prestasi_guru="prestasi_guru_sd";
      }elseif($jenjang=="smp"){
        $prestasi_guru="prestasi_guru_smp";
      }elseif($jenjang=="sma"){
        $prestasi_guru="prestasi_guru_sma";
      }else{
        $prestasi_guru="prestasi_guru_ft";
      }

      if($id){
        $data = $this->mymodel->withquery("select p.* from $prestasi_guru p where id_guru=$id_guru and id_prestasi=$id","result");
        if($data){
          if (!empty($data->file_prestasi)) {
            $data->file_prestasi = base_url("uploads/".$prestasi_guru."/").$data->file_prestasi;
          }
          if (!empty($data->foto_prestasi)) {
            $data->foto_prestasi = base_url("uploads/".$prestasi_guru."/").$data->foto_prestasi;
          }
        }

      }else{
        $data = $this->mymodel->withquery("select p.* from $prestasi_guru p where id_guru=$id_guru","result");
        if($data){
          foreach ($data as $key => $value) {
            if (!empty($value->foto_prestasi)) {
              $value->foto_prestasi = base_url("uploads/".$prestasi_guru."/").$value->foto_prestasi;
            }
            if (!empty($value->file_prestasi)) {
              $value->file_prestasi = base_url("uploads/".$prestasi_guru."/").$value->file_prestasi;
            }
          }
        }
      }

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
