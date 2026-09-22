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
    public function index_get()
    {
        $status = "";
        $token = "";
        $headers=array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }

        $jenjang = $this->get("jenjang");
        $id_siswa = $this->get("id_siswa");

        //cek periode tahun ajaran aktif
        $get_tahun_ajaran = $this->mymodel->withquery("select * from tahun_ajaran where tanggal_mulai <= '".date("Y-m-d")."' and tanggal_selesai >= '".date("Y-m-d")."'","row")->label;

        if (!empty($jenjang)) {
            if ($jenjang == "ft") {
                //$jenjang = "sma";
                $data = $this->mymodel->withquery("select e.* from ekskul e where e.jenjang = 'sma' and tahun_ajaran_aktif != '' and tahun_ajaran_aktif = '".$get_tahun_ajaran."' order by e.nama ASC", "result");
            }
            else{
                $data = $this->mymodel->withquery("select e.* from ekskul e where e.jenjang = '".$jenjang."' and tahun_ajaran_aktif != '' and tahun_ajaran_aktif = '".$get_tahun_ajaran."' order by e.nama ASC", "result");
            }
        }
        else{
            $data = $this->mymodel->withquery("select e.* from ekskul e where tahun_ajaran_aktif != '' and tahun_ajaran_aktif = '".$get_tahun_ajaran."' order by e.nama ASC", "result");
        }
        $semester = (!empty($this->post("semester"))) ? $this->post("semester") : $data[0]->semester_aktif; ;
        $tahun_ajaran = (!empty($this->post("tahun_ajaran"))) ? $this->post("tahun_ajaran") : $data[0]->tahun_ajaran_aktif;

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $cek_member = $this->mymodel->withquery("select * from ekskul_member_".$jenjang." where id_siswa = '".$id_siswa."' and id_ekskul = '".$value->id_ekskul."' and semester = '".$semester."' and tahun_ajaran = '".$tahun_ajaran."' order by id_member DESC","row");
                $detail_member = new stdClass();
                if (!empty($value->foto)) {
                    $value->foto = base_url("uploads/ekskul/").$value->foto;
                }
                if (!empty($cek_member->status_member)) {
                    $detail_member->status_member = $cek_member->status_member;
                    $detail_member->status_member_text = "Aktif";
                }
                else{
                    $detail_member->status_member = "";
                    $detail_member->status_member_text = "Tidak Aktif";
                }
                if (!empty($cek_member->file_pembayaran)) {
                    $detail_member->file_pembayaran = base_url("uploads/ekskul_member_".$jenjang."/").$cek_member->file_pembayaran;
                }
                else{
                    $detail_member->file_pembayaran = "";
                }
                if (!empty($cek_member->id_siswa)) {
                    $detail_member->id_siswa = $cek_member->id_siswa;
                }
                else{
                    $detail_member->id_siswa = "";
                }
                if (!empty($cek_member->semester)){
                    $detail_member->semester = $cek_member->semester;
                }
                else{
                    $detail_member->semester = "";
                }
                if (!empty($cek_member->tahun_ajaran)){
                    $detail_member->tahun_ajaran = $cek_member->tahun_ajaran;
                }
                else{
                    $detail_member->tahun_ajaran = "";
                }
                if (!empty($cek_member->payment_date)){
                    $detail_member->payment_date = $cek_member->payment_date;
                }
                else{
                    $detail_member->payment_date = "";
                }
                $value->detail_member = $detail_member;
            }
            $msg = array('status' => 200, 'message'=>'Data Ekstrakurikuler ditemukan' , 'periode' => array("tahun_ajaran_aktif" => $get_tahun_ajaran, "semester_aktif" => $data[0]->semester_aktif), 'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Ekstrakurikuler tidak ditemukan' , 'periode' => array(), 'data'=>$data);
        }

        $this->response($msg);
    }
}
