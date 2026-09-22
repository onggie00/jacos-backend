<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_jadwal_pelatih extends REST_Controller {
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
        $id_guru = $this->post("id_guru");

        $today = strtolower(formatHari(date("Y-m-d")));
        $data = $this->mymodel->withquery("select e.id_ekskul, e.nama as nama_ekskul, e.hari, e.jam from ekskul e join ekskul_manajemen_".$jenjang." m on e.id_ekskul = m.id_ekskul where m.id_guru_pembina = '".$id_guru."' and keterangan ='pembina' and e.hari like '%".$today."%'","result");
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $data_detail = $this->mymodel->withquery("select m.id_manajemen as id_pelatih,  m.jenjang_pembina, m.nama as nama_pelatih, m.keterangan, m.email_pelatih, m.notelp_pelatih from ekskul_manajemen_".$jenjang." m where m.id_ekskul = '".$value->id_ekskul."' and m.keterangan != 'pembina'","result");
                $value->jadwal = ucfirst($today).", ".formatTanggal(date("Y-m-d"));
                $value->waktu = date("H:i", strtotime($value->jam))." WIB";
                if (!empty($data_detail)) {
                    foreach ($data_detail as $key_detail => $value_detail) {
                        //cek presensi
                        $cek = $this->mymodel->withquery("select id from ekskul_presensi_pelatih_".$jenjang." where id_pelatih = '".$value_detail->id_pelatih."' and tanggal_absen = '".date("Y-m-d")."' and id_ekskul = '".$value->id_ekskul."'","row");
                        if (empty($cek)) {
                            $value_detail->is_absen = false;
                        }
                        else{
                            $value_detail->is_absen = true;
                        }
                    }
                }
                else{
                    $data_detail = array();
                }
                $value->list_pelatih = $data_detail;
            }
            $msg = array('status' => 200, 'message'=>'Data Ekstrakurikuler ditemukan' ,'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Ekstrakurikuler tidak ditemukan' ,'data'=>$data);
        }

        $this->response($msg);
    }
}
