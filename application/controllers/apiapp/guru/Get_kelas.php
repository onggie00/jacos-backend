<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_kelas extends REST_Controller {
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

        $jenjang = $this->get('jenjang');
        $id_tingkatan = $this->get("id_tingkatan");
        $list_data = array();

        if (!empty($id_tingkatan)) {
            if ($jenjang == "sma") {
                $jenjang_ft = "ft";
                $data_ft = $this->mymodel->withquery("select id_kelas_ft, id_tingkatan, nama_kelas, label from kelas_".$jenjang_ft." where id_tingkatan = '".$id_tingkatan."' order by id_kelas_".$jenjang_ft." ASC","result");
            }
            $data = $this->mymodel->withquery("select id_kelas_".$jenjang.", id_tingkatan, nama_kelas, label from kelas_".$jenjang." where id_tingkatan = '".$id_tingkatan."' order by id_kelas_".$jenjang." ASC","result");
        }
        else{
            if ($jenjang == "sma") {
                $jenjang_ft = "ft";
                $data_ft = $this->mymodel->withquery("select id_kelas_ft, id_tingkatan, nama_kelas, label from kelas_".$jenjang_ft." order by id_kelas_".$jenjang_ft." ASC","result");
            }
            $data = $this->mymodel->withquery("select id_kelas_".$jenjang.", id_tingkatan, nama_kelas, label from kelas_".$jenjang." order by id_kelas_".$jenjang." ASC","result");
        }
        foreach ($data as $key => $value) {
            $nama_kolom = "id_kelas_".$jenjang;
            $value->jenjang = $jenjang;
            $value->id_kelas = $value->$nama_kolom;
            $value->label = $value->label." (".strtoupper($jenjang).")";
            $list_data[] = $data[$key];
        }
        if (!empty($data_ft)) {
            foreach ($data_ft as $key => $value) {
                $nama_kolom = "id_kelas_".$jenjang_ft;
                $value->jenjang = $jenjang_ft;
                $value->id_kelas = $value->$nama_kolom;
                $value->label = $value->label." (".strtoupper($jenjang_ft).")";
                $list_data[] = $data_ft[$key];
            }
        }

        if (!empty($data)) {
            $msg = array('status' => 200, 'message'=>'Data Kelas ditemukan' ,'data'=>$list_data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Kelas tidak ditemukan' ,'data'=>$list_data);
        }

        $this->response($msg);
    }
}
