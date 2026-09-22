<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_ekskul_guru extends REST_Controller {
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

        $jenjang = $this->post('jenjang');
        $id_guru = $this->post("id_guru");
        $tahun_ajaran = $this->mymodel->withquery("
            SELECT label 
            FROM tahun_ajaran 
            WHERE tanggal_mulai <= '".date('Y-m-d')."' 
            AND tanggal_selesai >= '".date('Y-m-d')."' 
            ORDER BY id_tahun_ajaran DESC 
            LIMIT 1
        ", "row")->label;

        $semester = (date('m') >= 7) ? "1" : "2";
        
        $list_data = array();
        $data = $this->mymodel->withquery("select m.*, e.nama as nama_ekskul, g.nama_lengkap, g.email_ms_office from ekskul e left 
        join ekskul_manajemen_".$jenjang." m on e.id_ekskul = m.id_ekskul 
        join guru_".$jenjang." g on m.id_guru_pembina = g.id_guru 
        where m.id_guru_pembina = '".$id_guru."' and e.tahun_ajaran_aktif = '".$tahun_ajaran."' and e.semester_aktif = '".$semester."' ","result");
        if ($jenjang == "sma") {
            $jenjang_ft = "ft";
            $data_ft = $this->mymodel->withquery("select m.*, e.nama as nama_ekskul, g.nama_lengkap, g.email_ms_office from ekskul e left join ekskul_manajemen_".$jenjang_ft." m on e.id_ekskul = m.id_ekskul join guru_".$jenjang_ft." g on m.id_guru_pembina = g.id_guru where m.id_guru_pembina = '".$id_guru."' and e.tahun_ajaran_aktif = '".$tahun_ajaran."' and e.semester_aktif = '".$semester."' ","result");
        }

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $value->list_pelatih = $this->mymodel->withquery("select nama, keterangan, email_pelatih, notelp_pelatih from ekskul_manajemen_".$jenjang." where keterangan != 'pembina' and id_ekskul = '".$value->id_ekskul."' ","result");
                $list_data[] = $value;
            }
        }
        if (!empty($data_ft)) {
            $jenjang_ft = "ft";
            foreach ($data_ft as $key => $value) {
                $value->list_pelatih = $this->mymodel->withquery("select nama, keterangan, email_pelatih, notelp_pelatih from ekskul_manajemen_".$jenjang_ft." where keterangan != 'pembina' and id_ekskul = '".$value->id_ekskul."' ","result");
                $list_data[] = $value;
            }
        }
        if (!empty($list_data)) {
            $msg = array('status' => 200, 'message'=>'Data Ekstrakurikuler ditemukan' ,'data'=>$list_data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Ekstrakurikuler tidak ditemukan' ,'data'=>$list_data);
        }

        $this->response($msg);
    }
}
