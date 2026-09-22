<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Detail_pimpinan extends REST_Controller {
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
        $id = $this->post('id');
        $jenjang = $this->post('jenjang');
        $join="left join mata_pelajaran_$jenjang m on m.id_mapel = p.id_mapel left join posisi_pimpinan po on po.id_posisi = p.id_posisi";

        if($jenjang=='sd'){
          $data = $this->mymodel->withquery("select p.*,po.* from pimpinan_sd p $join where id_pimpinan = ".$id."","row");
          if($data->foto_profil){
            $data->foto_profil=base_url('uploads/pimpinan_sd/').$data->foto_profil;
          }
          if($data->slip_gaji){
            $data->slip_gaji=base_url('uploads/pimpinan_sd/').$data->slip_gaji;
          }
          if($data->id_mapel==0){
            $data->id_mapel="";
          }
        }else if($jenjang=='smp'){
          $data = $this->mymodel->withquery("select * from pimpinan_smp p $join where id_pimpinan = ".$id."","row");
          if($data->foto_profil){
            $data->foto_profil=base_url('uploads/pimpinan_smp/').$data->foto_profil;
          }
          if($data->slip_gaji){
            $data->slip_gaji=base_url('uploads/pimpinan_smp/').$data->slip_gaji;
          }
          if($data->id_mapel==0){
            $data->id_mapel="";
          }
        }else if($jenjang=='sma'){
          $data = $this->mymodel->withquery("select * from pimpinan_sma p $join where id_pimpinan = ".$id."","row");
          if($data->foto_profil){
            $data->foto_profil=base_url('uploads/pimpinan_sma/').$data->foto_profil;
          }
          if($data->slip_gaji){
            $data->slip_gaji=base_url('uploads/pimpinan_sma/').$data->slip_gaji;
          }
          if($data->id_mapel==0){
            $data->id_mapel="";
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
