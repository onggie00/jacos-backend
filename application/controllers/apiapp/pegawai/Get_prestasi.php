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
          $headers[$name] = $value;
      }
      $id = $this->post('id');
      $id_pegawai = $this->post('id_pegawai');

        $prestasi_pegawai="prestasi_pegawai";

      if($id){
        $data = $this->mymodel->withquery("select p.* from $prestasi_pegawai p where id_pegawai=$id_pegawai and id_prestasi=$id","result");
        if($data){
          if (!empty($data->file_prestasi)) {
            $data->file_prestasi = base_url("uploads/".$prestasi_pegawai."/").$data->file_prestasi;
          }
          if (!empty($data->foto_prestasi)) {
            $data->foto_prestasi = base_url("uploads/".$prestasi_pegawai."/").$data->foto_prestasi;
          }
        }

      }else{
        $data = $this->mymodel->withquery("select p.* from $prestasi_pegawai p where id_pegawai=$id_pegawai","result");
        if($data){
          foreach ($data as $key => $value) {
            if (!empty($value->foto_prestasi)) {
              $value->foto_prestasi = base_url("uploads/".$prestasi_pegawai."/").$value->foto_prestasi;
            }
            if (!empty($value->file_prestasi)) {
              $value->file_prestasi = base_url("uploads/".$prestasi_pegawai."/").$value->file_prestasi;
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
