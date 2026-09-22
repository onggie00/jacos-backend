<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_riwayat_ekskul_pelatih extends REST_Controller {
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
        $id_pelatih = $this->post('id_pelatih');
        $id_ekskul = $this->post('id_ekskul');
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');

        $nama_tabel = "ekskul_presensi_siswa_".$jenjang;
        $where = "";


        if (!empty($start_date) && !empty($end_date)) {
            if ($where == "") {
                $where .= "where p.tanggal_absen >= '".$start_date."' and p.tanggal_absen <= '".$end_date."'";
            }
            else{
                $where .= " and p.tanggal_absen >= '".$start_date."' and p.tanggal_absen <= '".$end_date."'";
            }
        }
        else if(!empty($start_date) && empty($end_date)){
            if ($where == "") {
                $where .= "where p.tanggal_absen >= '".$start_date."'";
            }
            else{
                $where .= " and p.tanggal_absen >= '".$start_date."'";
            }
        }
        else if (empty($start_date) && !empty($end_date)) {
            if ($where == "") {
                $where .= "where p.tanggal_absen <= '".$end_date."'";
            }
            else{
                $where .= " and p.tanggal_absen <= '".$end_date."'";
            }
        }
        else{
            if ($where == "") {
                $where .= "where p.tanggal_absen >= '".date("Y-m-d")."' and p.tanggal_absen <= '".date("Y-m-d")."'";
            }
            else{
                $where .= " and p.tanggal_absen >= '".date("Y-m-d")."' and p.tanggal_absen <= '".date("Y-m-d")."'";
            }
        }

        if (!empty($id_pelatih)) {
            if ($where == "") {
                $where .= "where p.id_pelatih = '".$id_pelatih."'";
            }
            else{
                $where .= " and p.id_pelatih = '".$id_pelatih."'";
            }
        }

        $data = $this->mymodel->withquery("select e.id_ekskul, e.nama as nama_ekskul, e.hari, p.tanggal_absen, e.jam from ekskul_presensi_pelatih_".$jenjang." p join ekskul e on e.id_ekskul = p.id_ekskul join ekskul_manajemen_".$jenjang." m on p.id_ekskul = m.id_ekskul ".$where." and m.id_guru_pembina = '".$id_guru."' and m.keterangan ='pembina' group by p.tanggal_absen, p.id_ekskul ","result");

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $value->hari = ucfirst($value->hari);
                $value->jam = date("H:i", strtotime($value->jam))." WIB";
                //get total member
                $value->total_pelatih_ekskul = $this->mymodel->withquery("select count(id_manajemen) as total from ekskul_manajemen_".$jenjang." where id_ekskul = '".$value->id_ekskul."' and keterangan != 'pembina' ","row")->total;
                //total hadir
                $value->total_hadir = $this->mymodel->withquery("select count(id) as total from ekskul_presensi_pelatih_".$jenjang." where id_ekskul = '".$value->id_ekskul."' and status_absen = 'Hadir' and tanggal_absen = '".$value->tanggal_absen."' ","row")->total;
                //detail pelatih hadir
                $value->detail_hadir = $this->mymodel->withquery("select p.id_pelatih, m.nama, p.waktu_absen, p.status_absen from ekskul_presensi_pelatih_".$jenjang." p join ekskul_manajemen_".$jenjang." m on p.id_pelatih = m.id_manajemen where p.id_ekskul = '".$value->id_ekskul."' and m.keterangan != 'pembina' and  p.tanggal_absen = '".$value->tanggal_absen."' order by m.nama ASC","result");
                $value->tanggal_absen = formatTanggal($value->tanggal_absen);
            }
            $msg = array('status' => 200, 'message'=>'Data Presensi Ekstrakurikuler ditemukan' ,'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Presensi Ekstrakurikuler tidak ditemukan' ,'data'=>null);
        }

        $this->response($msg);
    }
}
