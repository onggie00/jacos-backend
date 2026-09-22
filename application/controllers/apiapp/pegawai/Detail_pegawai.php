<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Detail_pegawai extends REST_Controller {
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
        $id = $this->post('id');
        
        $data = $this->mymodel->withquery("select p.*,po.* from pegawai p left join posisi_pegawai po on po.id_posisi = p.id_posisi where id_pegawai = $id","row");
        if($data->foto_profil){
          $data->foto_profil=base_url('uploads/pegawai/').$data->foto_profil;
        }
        if($data->slip_gaji){
          $data->slip_gaji=base_url('uploads/pegawai/').$data->slip_gaji;
        }
        if($data->id_mapel==0){
          $data->id_mapel="";
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
