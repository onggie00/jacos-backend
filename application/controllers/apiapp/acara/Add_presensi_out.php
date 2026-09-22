<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Add_presensi_out extends REST_Controller {
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
        $unique_code_finish = $this->post('unique_code');
        $peserta = $this->post('peserta');
        
        $data = array(
            "waktu_presensi_selesai" => $waktu_presensi,
            "peserta" => $peserta
        );

        if (!empty($data)) {
            //cek acara
            $get_acara = $this->mymodel->withquery("select id_acara, unique_code, unique_code_finish, peserta_acara from acara where id_acara = ".$this->db->escape($id_acara),"row");
            if (!empty($get_acara)) {
                //if($get_acara->unique_code_finish == $unique_code_finish){
                    if (strpos($get_acara->peserta_acara, $role) !== false) {
                        //cek sudah presensi atau belum
                        $cek_presensi = $this->mymodel->withquery("select id_presensi, id_acara, npp, role from acara_presensi where id_acara = ".$this->db->escape($id_acara)." and npp = ".$this->db->escape($npp)." and role = ".$this->db->escape($role),"row");
                        if (!empty($cek_presensi)) {
                            $update = $this->mymodel->update("acara_presensi", $data, "id_acara = ".$this->db->escape($id_acara)." and npp = ".$this->db->escape($npp)." and role = ",$role);
                            if (!empty($update)) {
                                $data['id_acara'] = $id_acara;
                                $data['nama_acara'] = $get_acara->nama_acara;
                                $data['npp'] = $npp;
                                $data['role'] = $role;
                                $msg = array('status' => 1, 'message'=>'Berhasil simpan data' ,'data'=>$data);
                                $status="200";
                            }
                            else{
                                $msg = array('status' => 0, 'message'=>'Gagal simpan data' ,'data'=>$data);
                            }
                        }
                        else{
                            $msg = array('status' => 0, 'message'=>'Anda Belum melakukan Presensi sebelumnya' ,'data'=>$data);
                        }
                    }
                    else{
                        $msg = array('status' => 0, 'message'=>'Anda tidak diperbolehkan akses, Acara Khusus untuk '.ucfirst($get_acara->peserta_acara),'data'=>array());
                        $status="200";
                    }
                /* }
                else{
                    $msg = array('status' => 0, 'message'=>'Unique Code Salah' ,'data'=>array());
                    $status="200";
                } */
            }
            else{
                $msg = array('status' => 0, 'message'=>'Acara tidak ditemukan' ,'data'=>array());
                $status="200";
            }
        }
        else{
            $msg = array('status' => 0, 'message'=>'Input data tidak valid' ,'data'=>array());
            $status="200";
        }

        $this->response($msg,$status);
    }
}
