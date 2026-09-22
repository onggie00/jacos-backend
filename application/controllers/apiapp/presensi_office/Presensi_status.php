<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Presensi_status extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_get()
    {
        $status = "";
        $token = "";
        $headers=array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }
        // Fallback ke $_SERVER untuk PHP-FPM / nginx (getallheaders() kadang kosong)
        if (empty($headers) && isset($_SERVER['HTTP_X_TOKEN'])) {
            $headers['x-token'] = $_SERVER['HTTP_X_TOKEN'];
        }
        if (isset($headers['x-token'])) {
            $token = $headers['x-token'];
        }

        if (empty($token)) {
            $this->response(array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array()), 200);
            return;
        }

        $data = $this->mymodel->withquery("select id_status, nama_status, code as presensi_code from presensi_setting_status","result");
        
        if(!empty($data)){
            $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $data);
            $status = 200;
            $this->response($msg, $status);
        }
        else{
            $msg = array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array());
            $status = 200;
            $this->response($msg, $status);
        }

    }

    public function cek_notelp($nohp){
        if(!preg_match("/[^+0-9]/",trim($nohp))){
            // cek apakah no hp karakter ke 1 dan 2 adalah angka 62
            if(substr(trim($nohp), 0, 2)=="62"){
                $hp    =trim($nohp);
            }
                // cek apakah no hp karakter ke 1 adalah angka 0
            else if(substr(trim($nohp), 0, 1)=="0"){
                $hp    ="62".substr(trim($nohp), 1);
            }
        }
        return $hp;
    }

}
