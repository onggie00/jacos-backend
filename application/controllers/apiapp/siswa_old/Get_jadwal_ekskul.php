<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_jadwal_ekskul extends REST_Controller {
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
        //cek hari ekskul
        $today = strtolower(formatHari(date("Y-m-d")));
        $data = $this->mymodel->withquery("select e.nama as nama_ekskul, e.hari, e.jam, m.* from ekskul_member_".$jenjang." m join ekskul e on m.id_ekskul = e.id_ekskul where m.id_siswa = '".$id_siswa."' order by m.status_member ASC, m.joined_date ASC", "result");

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (!empty($value->file_pembayaran)) {
                    $value->file_pembayaran = base_url("uploads/ekskul_member_".$jenjang."/").$value->file_pembayaran;
                }
                if ($value->status_member == 0) {
                    $value->status_member_text = "Menunggu Persetujuan Pembina";
                }
                else if($value->status_member == 1){
                    $value->status_member_text = "Aktif";
                }
                $value->joined_date = formatTanggal($value->joined_date). " (".date("H:i", strtotime($value->joined_date))." WIB)";
                $value->jadwal = ucfirst($value->hari);
                $value->waktu = date("H:i", strtotime($value->jam))." WIB";
                if (date("H:i:s", strtotime($value->jam)) > date("H:i:s")) {
                    $value->status_ekskul = "Belum Berlangsung";
                }
                else if(date("H:i:s", strtotime($value->jam)) < date("H:i:s") ){
                    $value->status_ekskul = "Sudah Berlangsung";
                }
                else{
                    $value->status_ekskul = "Selesai";
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
