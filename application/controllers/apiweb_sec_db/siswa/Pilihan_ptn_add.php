<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Pilihan_ptn_add extends REST_Controller {
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
        $where="";
        
        // dd($where);
        $jenjang = $this->post("jenjang");
        $id_siswa = $this->post("id_siswa");
        $id_pt = $this->post("id_pt");
        $id_pt = explode(",", $id_pt);
        $id_jurusan = $this->post("id_jurusan");
        $id_jurusan = explode(",", $id_jurusan);
        
        $data_pilihan = array(
            "jenjang" => $jenjang,
            "id_siswa_aktif" => $id_siswa,
        );
        $data_save = array();
        for ($i=0; $i < count($id_pt); $i++) { 
          $data_pilihan["id_pt"] = $id_pt[$i];
          $data_pilihan["id_jurusan"] = $id_jurusan[$i];
          //cek pilihan apakah sdh ada dan maksimal 2 pilihan
          $cek_pilihan = $this->second_db->withquery("select id from pt_pilihan_ptn where id_siswa_aktif = '".$id_siswa."' and jenjang = '".$jenjang."'", "result");
          //echo $this->db->last_query();

          if (count($cek_pilihan) < 2) {
            $save_data = $this->second_db->insertid("pt_pilihan_ptn", $data_pilihan);
            //$data_pilihan["id_pilihan"] = $save_data;
            array_push($data_save, $data_pilihan);
          }
          else{
            $msg = array('status' => 0, 'message'=>'Gagal simpan data (Pilihan maksimal 2)' ,'data'=>array());
            $status="200";
            break;
          }
        }

        if (!empty($data_save)) {
            $msg = array('status' => 1, 'message'=>'Berhasil simpan data' ,'data'=>$data_save);
            $status="200";
        }

        $this->response($msg,$status);
    }
}
