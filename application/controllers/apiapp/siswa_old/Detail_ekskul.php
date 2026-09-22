<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Detail_ekskul extends REST_Controller {
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
        $id_siswa = $this->post("id_siswa");
        $id_ekskul = $this->post("id_ekskul");
        if (!empty($jenjang)) {
            /*if ($jenjang == "ft") {
                $jenjang = "sma";
            }*/
            $data = $this->mymodel->withquery("select e.* from ekskul e where e.jenjang = '".$jenjang."' and id_ekskul = '".$id_ekskul."' order by e.nama ASC", "result");
        }
        else{
            $data = $this->mymodel->withquery("select e.* from ekskul e order by e.nama ASC", "result");
        }

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $cek_member = $this->mymodel->withquery("select * from ekskul_member_".$jenjang." where id_siswa = '".$id_siswa."' and id_ekskul = '".$id_ekskul."' ","row");
                if (!empty($value->foto)) {
                    $value->foto = base_url("uploads/ekskul/").$value->foto;
                }
                if (!empty($cek_member->file_pembayaran)) {
                    $value->file_pembayaran = base_url("uploads/ekskul_member_".$jenjang."/").$cek_member->file_pembayaran;
                }
                else{
                    $value->file_pembayaran = "";
                }
                if (!empty($cek_member->id_siswa)) {
                    $value->id_siswa = $cek_member->id_siswa;
                }
                else{
                    $value->id_siswa = "";
                }
                if (!empty($cek_member->status_member)) {
                    $value->status_member = $cek_member->status_member;
                    $value->status_member_text = "Aktif";
                }
                else{
                    $value->status_member = "";
                    $value->status_member_text = "Tidak Aktif";
                }
            }
            $msg = array('status' => 200, 'message'=>'Data Ekstrakurikuler ditemukan' ,'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Ekstrakurikuler tidak ditemukan' ,'data'=>$data);
        }

        $this->response($msg);
    }
}
