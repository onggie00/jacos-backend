<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Add_presensi_in extends REST_Controller {
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
        $where = "";
        $npp = $this->post('npp');
        $role = $this->post('role');
        $id_acara = $this->post('id_acara');
        $waktu_presensi = $this->post('waktu_presensi');
        $unique_code = $this->post('unique_code');
        $peserta = $this->post('peserta');
        
        $data = array(
            "npp" => $npp,
            "role" => $role,
            "id_acara" => $id_acara,
            "waktu_presensi" => $waktu_presensi,
            "peserta" => $peserta
        );

        if (!empty($data) && !empty($unique_code)) {
            //cek acara
            $get_acara = $this->mymodel->withquery("select id_acara, unique_code, peserta_acara from acara where id_acara = ".$this->db->escape($id_acara),"row");
            if (!empty($get_acara)) {
                if($get_acara->unique_code == $unique_code){
                    if (strpos($get_acara->peserta_acara, $role) !== false) {
                        //cek sudah presensi atau belum
                        $cek_presensi = $this->mymodel->withquery("select id_presensi, id_acara, npp, role from acara_presensi where id_acara = ".$this->db->escape($id_acara)." and npp = ".$this->db->escape($npp)." and role = ".$this->db->escape($role),"row");
                        if (!empty($cek_presensi)) {
                            $msg = array('status' => 0, 'message'=>'Anda Sudah melakukan Presensi sebelumnya' ,'data'=>$cek_presensi);
                            $status="200";
                        }
                        else{
                            $insert = $this->mymodel->insertid("acara_presensi", $data);
                            $data['id_presensi'] = $insert;
                            $msg = array('status' => 1, 'message'=>'Berhasil melakukan Presensi' ,'data'=>$data);
                        }
                    }
                    else{
                        $msg = array('status' => 0, 'message'=>'Anda tidak diperbolehkan akses, Acara Khusus untuk '.ucfirst($get_acara->peserta_acara),'data'=>array());
                        $status="200";
                    }
                }
                else{
                    $msg = array('status' => 0, 'message'=>'Unique Code Salah' ,'data'=>array());
                    $status="200";
                }
            }
            else{
                $msg = array('status' => 0, 'message'=>'Acara tidak ditemukan' ,'data'=>array());
                $status="200";
            }
        }
        else{
            $msg = array('status' => 0, 'message'=>'Input data tidak valid, Unique Code diperlukan' ,'data'=>array());
            $status="200";
        }

        $this->response($msg,$status);
    }
}
