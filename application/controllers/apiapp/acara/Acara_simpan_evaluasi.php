<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Acara_simpan_evaluasi extends REST_Controller {
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
        
        $where = "";
        $id_acara = $this->post("id_acara");
        $id_evaluasi_form = $this->post("id_evaluasi_form");
        $npp = $this->post("npp");
        $jawaban = $this->post("jawaban");

        $data = array(
            "id_acara" => $id_acara,
            "npp" => $npp,
        );
        $inserted_data = array();
        if (!empty($data)) {
            $this->db->trans_start();
            for($i=0;$i<count($id_evaluasi_form);$i++){
                $data["id_evaluasi_form"] = $id_evaluasi_form[$i];
                $data["jawaban"] = $jawaban[$i];
                $this->mymodel->insert("acara_presensi_evaluasi",$data);
                $inserted_data[] = $data;
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $id_evaluasi = false;
            } else {
                $id_evaluasi = true;
            }
            // $id_evaluasi = $this->mymodel->insertid("acara_presensi_evaluasi",$data);
            if($id_evaluasi){
                $data['id_evaluasi'] = $id_evaluasi;
                $msg = array('status' => 1, 'message'=>'Berhasil simpan data' ,'data'=>$inserted_data);
                $status="200";
            }
            else{
                $msg = array('status' => 0, 'message'=>'Gagal simpan data' ,'data'=>array());
                $status="200";
            }
        }
        else{
            $msg = array('status' => 0, 'message'=>'Data tidak valid' ,'data'=>array());
            $status="200";
        }

        $this->response($msg,$status);
    }
}
