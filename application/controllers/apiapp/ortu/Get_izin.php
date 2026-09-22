<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_izin extends REST_Controller {
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

        $jenjang = $this->post('jenjang');
        $id_izin = $this->post('id_izin');
        $id_siswa_aktif = $this->post('id_siswa_aktif');
        $filter_status = $this->post('filter_status');

        if($filter_status){
            $data = $this->mymodel->withquery("select i.*, s.nama_lengkap, s.nis, k.label, s.id_kelas from izin_siswa_".$jenjang." i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." where i.id_siswa_aktif = '$id_siswa_aktif' and i.status = '$filter_status' order by i.created_at DESC","result");
        }
        else if(!empty($id_izin)){
            $data = $this->mymodel->withquery("select i.*, s.nama_lengkap, s.nis, k.label, s.id_kelas from izin_siswa_".$jenjang." i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." where i.id_siswa_aktif = '$id_siswa_aktif' and i.id = '$id_izin' order by i.created_at DESC","result");
        }
        else{
            $data = $this->mymodel->withquery("select i.*, s.nama_lengkap, s.nis, k.label, s.id_kelas from izin_siswa_".$jenjang." i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." where i.id_siswa_aktif = '$id_siswa_aktif' order by i.created_at DESC ","result");
        }

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (!empty($value->file_izin)) {
                    $value->file_izin = base_url("uploads/izin_siswa_".$jenjang."/").$value->file_izin;
                }
            }
            $msg = array('status' => 200, 'message'=>'Data Izin ditemukan' ,'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Izin tidak ditemukan' ,'data'=>$data);
        }

        $this->response($msg,$status);
    }
}
