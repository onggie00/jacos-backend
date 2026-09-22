<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class List_guru_pegawai extends REST_Controller {
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
        // Fallback ke $_SERVER untuk PHP-FPM / nginx (getallheaders() kadang kosong)
        if (empty($headers) && isset($_SERVER['HTTP_X_TOKEN'])) {
            $headers['x-token'] = $_SERVER['HTTP_X_TOKEN'];
        }
        if (isset($headers['x-token'])) {
            $token = $headers['x-token'];
        }

        if (empty($token)) {
            $this->response(array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array()), 200);
            return;
        }

        //cek apakah pimpinan
        $unit = null;
        //pimpinan
        if (empty($get_user)) {
            $get_user = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sd') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pimpinan_sd u where u.token = '".$token."'","row");
            $unit = "sd";
        }
        if (empty($get_user)) {
            $get_user = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_smp') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pimpinan_smp u where u.token = '".$token."'","row");
            $unit = "smp";
        }
        if (empty($get_user)) {
            $get_user = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sma') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pimpinan_sma u where u.token = '".$token."'","row");
            $unit = "sma";
        }

        // Ambil data pegawai & guru berdasarkan unit
        if ($unit == "sd") {
            $get_user_pegawai = $this->mymodel->withquery("SELECT u.id_pegawai as id_user, u.presensi_role, u.npp, u.emp_code, u.nama_lengkap 
                FROM pegawai u 
                WHERE u.presensi_role LIKE '%SD%'
                ORDER BY u.nama_lengkap ASC", "result");

            $get_user_sd = $this->mymodel->withquery("SELECT u.id_guru as id_user, u.presensi_role, u.npp, u.emp_code, u.nama_lengkap 
                FROM guru_sd u 
                ORDER BY u.nama_lengkap ASC", "result");

            $list_user = $this->mergeUsers($get_user_pegawai, $get_user_sd);
        }
        else if ($unit == "smp") {
            $get_user_pegawai = $this->mymodel->withquery("SELECT u.id_pegawai as id_user, u.presensi_role, u.npp, u.emp_code, u.nama_lengkap 
                FROM pegawai u 
                WHERE u.presensi_role LIKE '%SMP%'
                ORDER BY u.nama_lengkap ASC", "result");

            $get_user_smp = $this->mymodel->withquery("SELECT u.id_guru as id_user, u.presensi_role, u.npp, u.emp_code, u.nama_lengkap 
                FROM guru_smp u 
                ORDER BY u.nama_lengkap ASC", "result");

            $list_user = $this->mergeUsers($get_user_pegawai, $get_user_smp);
        }
        else if ($unit == "sma") {
            $get_user_pegawai = $this->mymodel->withquery("SELECT u.id_pegawai as id_user, u.presensi_role, u.npp, u.emp_code, u.nama_lengkap 
                FROM pegawai u 
                WHERE u.presensi_role LIKE '%SMA%'
                ORDER BY u.nama_lengkap ASC", "result");

            $get_user_sma = $this->mymodel->withquery("SELECT u.id_guru as id_user, u.presensi_role, u.npp, u.emp_code, u.nama_lengkap 
                FROM guru_sma u 
                ORDER BY u.nama_lengkap ASC", "result");

            $list_user = $this->mergeUsers($get_user_pegawai, $get_user_sma);
        }

        // Sort gabungan by nama_lengkap
        if (!empty($list_user)) {
            usort($list_user, function($a, $b) {
                return strcmp($a->nama_lengkap, $b->nama_lengkap);
            });
        }
        // cek apakah data hari itu sudah ada
        // $cek_data = $this->mymodel->withquery("select * from presensi_office where npp = '".$get_user->npp."' and presensi_date = '".$presensi_date."'","row");
        

        
        //cek sort dari staff, guru, pimpinan
        
        if(!empty($get_user) && isset($list_user) ){
            $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $list_user);
            $status = 200;
            $this->response($msg, $status);
        }
        else if(empty($get_user)){
            $msg = array('status' => 0, 'message' => 'Role tidak diperbolehkan mengakses API ini', 'data' => array());
            $status = 200;
            $this->response($msg, $status);
        }
        else{
            $msg = array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array());
            $status = 200;
            $this->response($msg, $status);
        }

    }

    public function cek_notelp($nohp){
        if(!preg_match("/[^+0-9]/",trim($nohp))){
            // cek apakah no hp karakter ke 1 dan 2 adalah angka 62
            if(substr(trim($nohp), 0, 2)=="62"){
                $hp    =trim($nohp);
            }
                // cek apakah no hp karakter ke 1 adalah angka 0
            else if(substr(trim($nohp), 0, 1)=="0"){
                $hp    ="62".substr(trim($nohp), 1);
            }
        }
        return $hp;
    }

    // Fungsi helper untuk merge & hindari error jika query return null
    function mergeUsers(...$arrays) {
        $result = [];
        foreach ($arrays as $arr) {
            if (!empty($arr)) {
                $result = array_merge($result, $arr);
            }
        }
        return $result;
    }

}
