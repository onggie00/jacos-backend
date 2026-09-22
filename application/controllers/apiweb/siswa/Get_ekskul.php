<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_ekskul extends REST_Controller {
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
        $semester = $this->post("semester");
        $tahun_ajaran = $this->post("tahun_ajaran");
        if (!empty($jenjang)) {
            if ($jenjang == "ft") {
                //$jenjang = "sma";
            }
            $data = $this->mymodel->withquery("select e.* from ekskul e where e.jenjang = '".$jenjang."' order by e.nama ASC", "result");
        }
        else{
            $data = $this->mymodel->withquery("select e.* from ekskul e order by e.nama ASC", "result");
        }

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $cek_member = $this->mymodel->withquery("select * from ekskul_member_".$jenjang." where id_siswa = '".$id_siswa."' and id_ekskul = '".$value->id_ekskul."' and semester = '".$semester."' and tahun_ajaran = '".$tahun_ajaran."' ","row");
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
                }
                else{
                    $value->status_member = "";
                }
                if (!empty($cek_member->semester)){
                    $value->semester = $cek_member->semester;
                }
                else{
                    $value->semester = "";
                }
                if (!empty($cek_member->tahun_ajaran)){
                    $value->tahun_ajaran = $cek_member->tahun_ajaran;
                }
                else{
                    $value->tahun_ajaran = "";
                }
                if (!empty($cek_member->payment_date)){
                    $value->payment_date = $cek_member->payment_date;
                }
                else{
                    $value->payment_date = "";
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
