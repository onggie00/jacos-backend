<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_tingkatan extends REST_Controller {
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
            $headers[strtolower($name)] = $value;
            
        }

        $list_data = array();
        $jenjang = $this->get('jenjang');
        $data = $this->mymodel->withquery("select * from tingkatan_".$jenjang." order by id_tingkatan_".$jenjang." ASC","result");

        foreach ($data as $key => $value) {
            $value->jenjang = $jenjang;
            $value->label = $value->label." (".strtoupper($jenjang).")";
            $list_data[] = $data[$key];
        }
        /*if ($jenjang == "sma") {
            $jenjang_ft = "ft";
            $data_ft = $this->mymodel->withquery("select * from tingkatan_".$jenjang_ft." order by id_tingkatan_".$jenjang_ft." ASC","result");
            foreach ($data_ft as $key => $value) {
                $value->jenjang = $jenjang_ft;
                $value->label = $value->label." (".strtoupper($jenjang_ft).")";
                $list_data[] = $data_ft[$key];
            }
        }*/
        if (!empty($data)) {
            $msg = array('status' => 200, 'message'=>'Data Tingkatan ditemukan' ,'data'=>$list_data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Tingkatan tidak ditemukan' ,'data'=>$list_data);
        }

        $this->response($msg);
    }
}
