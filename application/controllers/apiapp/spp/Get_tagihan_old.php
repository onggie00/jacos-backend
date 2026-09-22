<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_tagihan extends REST_Controller {
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

        $jenjang=strtoupper($this->post('jenjang'));
        $id_siswa=$this->post('id_siswa_aktif');
        $id_tahun_ajaran=$this->post('id_tahun_ajaran');
        $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran", "id_tahun_ajaran", $id_tahun_ajaran, "row");
        $code_tahun_ajaran=$tahun_ajaran->code;

        $data = $this->mymodel->withquery("select * from transaksi_spp where no_transaksi like '%LISPP-".$jenjang."-".$code_tahun_ajaran."-".$id_siswa."%'","result");

        if (!empty($data)) {
          foreach($data as $key => $item){
            if($item->file_slip){
              $item->file_slip = base_url("uploads/slip_pembayaran/").$item->file_slip;
            }
            if($item->file_kwitansi){
              $item->file_kwitansi = base_url("uploads/kwitansi/").$item->file_kwitansi;
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
