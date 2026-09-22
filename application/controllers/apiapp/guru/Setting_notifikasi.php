<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Setting_notifikasi extends REST_Controller {
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

        $jenjang = $this->post("jenjang");
        $id_akun = $this->post("id_guru");
        $data = $this->mymodel->withquery("select * from setting_notifikasi_guru where id_guru = '".$id_akun."' and jenjang = '".$jenjang."'", "result");

        if (!empty($data)) {
            $msg = array('status' => 200, 'message'=>'Data Setting Notifikasi ditemukan' ,'data'=>$data);
        }
        else{
            $data_insert = array(
                "jenjang" => $jenjang,
                "id_guru" => $id_akun,
                "notifikasi_izin" => 1,
            );
            $id_data = $this->mymodel->insertid("setting_notifikasi_guru", $data_insert);
            $data_insert["id"] = $id_data;
            $data = $data_insert;
            $msg = array('status' => 200, 'message'=>'Data Setting Notifikasi ditemukan' ,'data'=>$data);
        }

        $this->response($msg);
    }
}
