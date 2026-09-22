<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Update_setting_notifikasi extends REST_Controller {
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

        $jenjang = $this->post("jenjang");
        $id_akun = $this->post("id_siswa_aktif");
        $data_update = array(
                "notifikasi_presensi" => $this->post('notifikasi_presensi'),
                "notifikasi_wa_presensi" => $this->post('notifikasi_wa_presensi'),
                "notifikasi_presensi_ekskul" => $this->post('notifikasi_presensi_ekskul'),
                "notifikasi_izin" => $this->post('notifikasi_izin')
            );
        $get_data_setting = $this->mymodel->getbywhere("setting_notifikasi_ortu","id_siswa_aktif = '".$id_akun."' and jenjang=",$jenjang,"row");
        if (!empty($data_update) && !empty($get_data_setting)) {
            $this->mymodel->update("setting_notifikasi_ortu", $data_update, "id_siswa_aktif = '".$id_akun."' and jenjang=", $jenjang);
            $msg = array('status' => 200, 'message'=>'Data Setting Notifikasi diubah' ,'data'=>$data_update);
        }
        else if(!empty($data_update)){
            $data_update['id_siswa_aktif'] = $id_akun;
            $data_update['jenjang'] = $jenjang;
            $this->mymodel->insert("setting_notifikasi_ortu", $data_update);
            $msg = array('status' => 200, 'message'=>'Data Setting Notifikasi diubah' ,'data'=>$data_update);
        }
        else{
            $msg = array('status' => 200, 'message'=>'Data Setting Notifikasi gagal diubah' ,'data'=>array());
        }

        $this->response($msg);
    }
}
