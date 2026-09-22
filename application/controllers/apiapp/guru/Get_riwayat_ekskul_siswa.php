<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_riwayat_ekskul_siswa extends REST_Controller {
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
        $id_siswa_aktif = $this->post('id_siswa');
        $id_ekskul = $this->post('id_ekskul');
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $tahun_ajaran = $this->mymodel->withquery("
            SELECT label 
            FROM tahun_ajaran 
            WHERE tanggal_mulai <= '".date('Y-m-d')."' 
            AND tanggal_selesai >= '".date('Y-m-d')."' 
            ORDER BY id_tahun_ajaran DESC 
            LIMIT 1
        ", "row")->label;

        $semester = (date('m') >= 7) ? "1" : "2";

        $nama_tabel = "ekskul_presensi_siswa_".$jenjang;
        $jenjang_ft = null;
        $list_data = array();
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

        if (!empty($id_siswa_aktif)) {
            if ($where == "") {
                $where .= "where p.id_siswa_aktif = '".$id_siswa_aktif."'";
            }
            else{
                $where .= " and p.id_siswa_aktif = '".$id_siswa_aktif."'";
            }
        }

        $data = $this->mymodel->withquery("select DISTINCT e.id_ekskul, e.nama as nama_ekskul, e.hari, p.tanggal_absen, e.jam from ekskul_presensi_siswa_".$jenjang." p join ekskul e on e.id_ekskul = p.id_ekskul join ekskul_manajemen_".$jenjang." m on e.id_ekskul = m.id_ekskul ".$where." and e.tahun_ajaran_aktif = '".$tahun_ajaran."' and e.semester_aktif = '".$semester."' group by p.tanggal_absen, p.id_ekskul ","result");
        if ($jenjang == "sma") {
            $jenjang_ft = "ft";
            $data_ft = $this->mymodel->withquery("select DISTINCT e.id_ekskul, e.nama as nama_ekskul, e.hari, p.tanggal_absen, e.jam from ekskul_presensi_siswa_".$jenjang_ft." p join ekskul e on e.id_ekskul = p.id_ekskul join ekskul_manajemen_".$jenjang_ft." m on e.id_ekskul = m.id_ekskul ".$where." and e.tahun_ajaran_aktif = '".$tahun_ajaran."' and e.semester_aktif = '".$semester."' group by p.tanggal_absen, p.id_ekskul ","result");
        }

        //$data = $this->mymodel->withquery("select p.*, s.nama_lengkap, k.label as nama_kelas, k.id_tingkatan, s.id_kelas from ".$nama_tabel." p join siswa_".$jenjang."_aktif s on p.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." ".$where." and e.tahun_ajaran_aktif = '".$tahun_ajaran."' and e.semester_aktif = '".$semester."' order by p.tanggal_absen ASC","result");
        //echo $this->db->last_query();

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $value->hari = ucfirst($value->hari);
                $value->jam = date("H:i", strtotime($value->jam))." WIB";
                //get total member
                $value->total_peserta_ekskul = $this->mymodel->withquery("select count(id_member) as total from ekskul_member_".$jenjang." where tahun_ajaran = '".$tahun_ajaran."' and semester = '".$semester."' and id_ekskul = '".$value->id_ekskul."' ","row")->total;
                //total hadir
                $value->total_hadir = $this->mymodel->withquery("select count(p.id) as total from ekskul_presensi_siswa_".$jenjang." p join ekskul_member_".$jenjang." m on m.id_siswa = p.id_siswa_aktif where m.tahun_ajaran = '".$tahun_ajaran."' and m.semester = '".$semester."' and m.id_ekskul = '".$value->id_ekskul."' and p.status_absen = 'Hadir' and p.tanggal_absen = '".$value->tanggal_absen."' ","row")->total;
                //detail peserta hadir
                $value->detail_hadir = $this->mymodel->withquery("select distinct p.id_siswa_aktif, s.nama_lengkap, k.label as nama_kelas, p.waktu_absen, p.status_absen from ekskul_presensi_siswa_".$jenjang." p 
                join siswa_".$jenjang."_aktif s on p.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif 
                join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." 
                join ekskul_member_".$jenjang." m on m.tahun_ajaran = '".$tahun_ajaran."' and m.semester = '".$semester."' 
                where tahun_ajaran = '".$tahun_ajaran."' and m.semester = '".$semester."' and p.id_ekskul = '".$value->id_ekskul."' and p.tanggal_absen = '".$value->tanggal_absen."' order by s.nama_lengkap ASC","result");
                $value->tanggal_absen = formatTanggal($value->tanggal_absen);
                //echo $this->db->last_query();
                $list_data[] = $value;
            }
        }
        if (!empty($data_ft)) {
            foreach ($data_ft as $key => $value) {
                $value->hari = ucfirst($value->hari);
                $value->jam = date("H:i", strtotime($value->jam))." WIB";
                //get total member
                $value->total_peserta_ekskul = $this->mymodel->withquery("select count(id_member) as total from ekskul_member_".$jenjang." where tahun_ajaran = '".$tahun_ajaran."' and semester = '".$semester."' and id_ekskul = '".$value->id_ekskul."' ","row")->total;
                //total hadir
                $value->total_hadir = $this->mymodel->withquery("select count(p.id) as total from ekskul_presensi_siswa_".$jenjang." p join ekskul_member_".$jenjang." m on m.id_siswa = p.id_siswa_aktif where m.tahun_ajaran = '".$tahun_ajaran."' and m.semester = '".$semester."' and m.id_ekskul = '".$value->id_ekskul."' and p.status_absen = 'Hadir' and p.tanggal_absen = '".$value->tanggal_absen."' ","row")->total;
                //detail peserta hadir
                $value->detail_hadir = $this->mymodel->withquery("select distinct p.id_siswa_aktif, s.nama_lengkap, k.label as nama_kelas, p.waktu_absen, p.status_absen from ekskul_presensi_siswa_".$jenjang." p 
                join siswa_".$jenjang."_aktif s on p.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif 
                join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." 
                join ekskul_member_".$jenjang." m on m.tahun_ajaran = '".$tahun_ajaran."' and m.semester = '".$semester."' 
                where tahun_ajaran = '".$tahun_ajaran."' and m.semester = '".$semester."' and p.id_ekskul = '".$value->id_ekskul."' and  p.tanggal_absen = '".$value->tanggal_absen."' order by s.nama_lengkap ASC","result");
                $value->tanggal_absen = formatTanggal($value->tanggal_absen);
                //echo $this->db->last_query();
                $list_data[] = $value;
            }
        }

        if (!empty($list_data)) {
            $msg = array('status' => 200, 'message'=>'Data Presensi Ekstrakurikuler ditemukan' ,'data'=>$list_data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Data Presensi Ekstrakurikuler tidak ditemukan' ,'data'=>null);
        }

        $this->response($msg);
    }
}
