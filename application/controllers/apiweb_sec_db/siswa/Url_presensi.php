<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Url_presensi extends REST_Controller {
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
      $jenjang=$this->post('jenjang');
      $id_tahun_ajaran=$this->post('id_tahun_ajaran');
        $data = $this->second_db->withquery("select p.*,t.* from pengaturan_url_presensi_siswa p join tahun_ajaran t on t.id_tahun_ajaran = p.id_tahun_ajaran where p.jenjang = '$jenjang' and p.id_tahun_ajaran = $id_tahun_ajaran","result");

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
