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
            $headers[strtolower($name)] = $value;
            
        }

        $jenjang = $this->post('jenjang');
        $id_izin = $this->post('id_izin');
        $id_kelas = $this->post('id_kelas');
        // $id_siswa_aktif = $this->post('id_siswa_aktif');
        $filter_status = $this->post('filter_status');
        $id_guru = $this->post('id_guru');
        $list_array = array();

        $where = "";

        if (!empty($id_izin)) {
            if ($where == "") {
                $where .= "where i.id = '".$id_izin."'";
            }
            else{
                $where .= " and i.id = '".$id_izin."'";
            }
        }
        if (!empty($filter_status)) {
            if ($where == "") {
                $where .= "where i.status = '".$filter_status."'";
            }
            else{
                $where .= " and i.status = '".$filter_status."'";
            }
        }
        if (!empty($id_kelas)) {
            if ($where == "") {
                $where .= "where s.id_kelas = '".$id_kelas."'";
            }
            else{
                $where .= " and s.id_kelas = '".$id_kelas."'";
            }
        }

        if (!empty($id_guru)) {
            if ($where == "") {
                $where .= "where g.id_guru = '".$id_guru."'";
            }
            else{
                $where .= " and g.id_guru = '".$id_guru."'";
            }
        }

        //cek guru piket
        $cek_piket = $this->mymodel->withquery("select * from guru_".$jenjang."_piket where id_guru = '".$id_guru."'","row");
        $jenjang_ft = "ft";
        if (!empty($cek_piket)) {
            $data = $this->mymodel->withquery("select i.*, s.nama_lengkap, s.nis, k.label, s.id_kelas from izin_siswa_".$jenjang." i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." join guru_".$jenjang."_piket g on s.id_kelas = g.id_kelas ".$where." order by id DESC ","result");
            if ($jenjang == "sma") {
                $cek_piket_ft = $this->mymodel->withquery("select * from guru_ft_piket where id_guru = '".$id_guru."' and jenjang='sma'","row");
                if (!empty($cek_piket_ft) ) {
                    $data_ft = $this->mymodel->withquery("select i.*, s.nama_lengkap, s.nis, k.label, s.id_kelas from izin_siswa_".$jenjang_ft." i join siswa_".$jenjang_ft."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang_ft."_aktif join kelas_".$jenjang_ft." k on s.id_kelas = k.id_kelas_".$jenjang_ft." join guru_".$jenjang_ft."_piket g on s.id_kelas = g.id_kelas ".$where." order by id DESC ","result");
                }
                else if(!empty($cek_piket)){
                    $data_ft = $this->mymodel->withquery("select i.*, s.nama_lengkap, s.nis, k.label, s.id_kelas from izin_siswa_".$jenjang_ft." i join siswa_".$jenjang_ft."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang_ft."_aktif join kelas_".$jenjang_ft." k on s.id_kelas = k.id_kelas_".$jenjang_ft." join guru_".$jenjang."_piket g on s.id_kelas = g.id_kelas ".$where." order by id DESC ","result");
                }
            }
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    if (!empty($value->file_izin)) {
                        $value->file_izin = base_url("uploads/izin_siswa_".$jenjang."/").$value->file_izin;
                    }
                    $value->jenjang = $jenjang;
                    array_push($list_array, $data[$key]);
                }
            }
            if (!empty($data_ft)) {
                foreach ($data_ft as $key => $value) {
                    if (!empty($value->file_izin)) {
                        $value->file_izin = base_url("uploads/izin_siswa_".$jenjang_ft."/").$value->file_izin;
                    }
                    $value->jenjang = $jenjang_ft;
                    array_push($list_array, $data_ft[$key]);
                } 
            }
            if (!empty($list_array)) {
                $msg = array('status' => 200, 'message'=>'Data Izin ditemukan' ,'data'=>$list_array);
            }
            else{
                $msg = array('status' => 401, 'message'=>'Data Izin tidak ditemukan' ,'data'=>null);
            }
        }
        else if(empty($cek_piket) && ($jenjang == "sma" || $jenjang == "ft" )){
            if ($jenjang == "sma") {
                $jenjang2 = "ft";
                $cek_piket2 = $this->mymodel->withquery("select * from guru_".$jenjang2."_piket where id_guru = '".$id_guru."'","row");
            }
            else if ($jenjang == "ft") {
                $jenjang2 = "sma";
                $cek_piket2 = $this->mymodel->withquery("select * from guru_".$jenjang2."_piket where id_guru = '".$id_guru."'","row");
            }
            //echo $this->db->last_query();
            if (!empty($cek_piket) && !empty($cek_piket2)) {
                $data = $this->mymodel->withquery("select i.*, s.nama_lengkap, s.nis, k.label, s.id_kelas from izin_siswa_".$jenjang." i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." join guru_".$jenjang."_piket g on s.id_kelas = g.id_kelas ".$where." order by id DESC ","result");
                if ($jenjang == "sma") {
                    $data_ft = $this->mymodel->withquery("select i.*, s.nama_lengkap, s.nis, k.label, s.id_kelas from izin_siswa_".$jenjang_ft." i join siswa_".$jenjang_ft."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang_ft."_aktif join kelas_".$jenjang_ft." k on s.id_kelas = k.id_kelas_".$jenjang_ft." join guru_".$jenjang_ft."_piket g on s.id_kelas = g.id_kelas ".$where." order by id DESC ","result");
                }
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        if (!empty($value->file_izin)) {
                            $value->file_izin = base_url("uploads/izin_siswa_".$jenjang."/").$value->file_izin;
                        }
                        $value->jenjang = $jenjang;
                        array_push($list_array, $data[$key]);
                    }
                }
                if (!empty($data_ft)) {
                    foreach ($data_ft as $key => $value) {
                        if (!empty($value->file_izin)) {
                            $value->file_izin = base_url("uploads/izin_siswa_".$jenjang_ft."/").$value->file_izin;
                        }
                        $value->jenjang = $jenjang_ft;
                        array_push($list_array, $data_ft[$key]);
                    } 
                }
                if (!empty($list_array)) {
                    $msg = array('status' => 200, 'message'=>'Data Izin ditemukan' ,'data'=>$list_array);
                }
                else{
                    $msg = array('status' => 401, 'message'=>'Data Izin tidak ditemukan' ,'data'=>null);
                }
            }
            else{
                $msg = array('status' => 401, 'message'=>'Bukan Guru Piket / Data Guru Piket tidak ditemukan' ,'data'=>null);
            }
        }
        else{
            $msg = array('status' => 401, 'message'=>'Bukan Guru Piket' ,'data'=>null);
        }

        $this->response($msg,$status);
    }
}
