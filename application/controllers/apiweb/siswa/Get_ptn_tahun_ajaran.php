<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_ptn_tahun_ajaran extends REST_Controller {
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

        //print_r($headers['x-token']);
        $data = $this->mymodel->withquery("select tahun_ajaran from pt_to_siswa where tahun_ajaran != '' and tahun_ajaran IS NOT NULL group by tahun_ajaran order by tahun_ajaran DESC","result");

        if (!empty($data)) {
            
            $msg = array('status' => 200, 'message'=>'Berhasil ambil data' ,'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Belum ada data' ,'data'=>array());
        }

        $this->response($msg);
    }
}
