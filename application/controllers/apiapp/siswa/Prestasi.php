<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Prestasi extends REST_Controller {
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
      $id=$this->post('id');
      $id_siswa=$this->post('id_siswa_aktif');
      $jenjang=$this->post('jenjang');

      if($id){
        if($jenjang=='sd'){
          $data = $this->mymodel->withquery("select p.*, j.jenis_prestasi, j.id_jenis_prestasi from prestasi_siswa_sd p left join jenis_prestasi j on p.jenis_prestasi_id = j.id_jenis_prestasi where p.id_prestasi =$id","result");
          $path='prestasi_siswa_sd';
        }elseif($jenjang=='smp'){
          $data = $this->mymodel->withquery("select p.*, j.jenis_prestasi, j.id_jenis_prestasi from prestasi_siswa_smp p left join jenis_prestasi j on p.jenis_prestasi_id = j.id_jenis_prestasi where p.id_prestasi =$id","result");
          $path='prestasi_siswa_smp';
        }elseif($jenjang=='sma'){
          $data = $this->mymodel->withquery("select p.*, j.jenis_prestasi, j.id_jenis_prestasi from prestasi_siswa_sma p left join jenis_prestasi j on p.jenis_prestasi_id = j.id_jenis_prestasi where p.id_prestasi =$id","result");
          $path='prestasi_siswa_sma';
        }else{
          $data = $this->mymodel->withquery("select p.*, j.jenis_prestasi, j.id_jenis_prestasi from prestasi_siswa_ft p left join jenis_prestasi j on p.jenis_prestasi_id = j.id_jenis_prestasi where p.id_prestasi =$id","result");
          $path='prestasi_siswa_ft';
        }
      }else{
        if($jenjang=='sd'){
          $data = $this->mymodel->withquery("select p.* from prestasi_siswa_sd p where p.id_siswa =$id_siswa order by id_prestasi DESC","result");
          $path='prestasi_siswa_sd';
        }elseif($jenjang=='smp'){
          $data = $this->mymodel->withquery("select p.* from prestasi_siswa_smp p where p.id_siswa =$id_siswa order by id_prestasi DESC","result");
          $path='prestasi_siswa_smp';
        }elseif($jenjang=='sma'){
          $data = $this->mymodel->withquery("select p.* from prestasi_siswa_sma p where p.id_siswa =$id_siswa order by id_prestasi DESC","result");
          $path='prestasi_siswa_sma';
        }else{
          $data = $this->mymodel->withquery("select p.* from prestasi_siswa_ft p where p.id_siswa =$id_siswa order by id_prestasi DESC","result");
          $path='prestasi_siswa_ft';
        }
      }
      

        if (!empty($data)) {
          foreach ($data as $key => $value) {
            if (!empty($value->foto_prestasi)) {
              $value->foto_prestasi = base_url("uploads/$path/").$value->foto_prestasi;
            }
            if (!empty($value->file_prestasi)) {
              $value->file_prestasi = base_url("uploads/$path/").$value->file_prestasi;
            }
          }
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
