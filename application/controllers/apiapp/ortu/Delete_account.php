<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Delete_account extends REST_Controller {
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
        $jenjang=explode(',',$this->post('jenjang'));
        $id_siswa_aktif=$this->post('id_siswa_aktif');
        $jenjang = $this->post('jenjang');
        $where="";
        $date=date('Y-m-d H:i:s');

        if (!empty($id_siswa_aktif)) {
          $get_siswa_aktif = $this->mymodel->withquery("select id_siswa_".$jenjang." as id_siswa, nama_lengkap from siswa_".$jenjang."_aktif where id_siswa_".$jenjang."_aktif = '".$id_siswa_aktif."'","row");
          $update_data_aktif = $this->mymodel->update("siswa_".$jenjang."_aktif", array("deleted_at_ortu" => $date), "id_siswa_".$jenjang."_aktif", $id_siswa_aktif);
          $update_data_siswa = $this->mymodel->update("siswa_".$jenjang, array("deleted_at_ortu" => $date, "token_ortu" => null), "id_siswa_".$jenjang, $get_siswa_aktif->id_siswa);
          $msg = array('status' => 1, 'message'=>'Berhasil hapus data' ,'data'=>$get_siswa_aktif);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
