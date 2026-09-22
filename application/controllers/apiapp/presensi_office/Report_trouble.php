<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Report_trouble extends REST_Controller {
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
        // Fallback ke $_SERVER untuk PHP-FPM / nginx (getallheaders() kadang kosong)
        if (empty($headers) && isset($_SERVER['HTTP_X_TOKEN'])) {
            $headers['x-token'] = $_SERVER['HTTP_X_TOKEN'];
        }
        if (isset($headers['x-token'])) {
            $token = $headers['x-token'];
        }

        if (empty($token)) {
            $this->response(array('status' => 0, 'message' => 'Token tidak valid', 'data' => array()), 200);
            return;
        }

        $presensi_date = (!empty($this->post('presensi_date'))) ? $this->post('presensi_date') : date("Y-m-d");
        $check_in = $this->post('waktu_laporan');
        $keterangan = $this->post('keterangan');
        if (!preg_match('/^[0-9]{2}:[0-9]{2}(:[0-9]{2})?$/', $check_in) || strtotime($check_in) === false) {
            $this->response(array('status' => 0, 'message' => 'Format waktu laporan tidak valid', 'data' => array()), 200);
            return;
        }
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $presensi_date) || strtotime($presensi_date) === false) {
            $this->response(array('status' => 0, 'message' => 'Format tanggal presensi tidak valid', 'data' => array()), 200);
            return;
        }

        //pegawai
        $get_user = $this->mymodel->withquery("select u.id_pegawai as id_user, concat('pegawai') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pegawai u where u.token = '".$token."'","row");
        //pimpinan
        if (empty($get_user)) {
            $get_user = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sd') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pimpinan_sd u where u.token = '".$token."'","row");
        }
        if(empty($get_user)){
            $get_user = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_smp') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pimpinan_smp u where u.token = '".$token."'","row");
        }
        if (empty($get_user)) {
            $get_user = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sma') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pimpinan_sma u where u.token = '".$token."'","row");
        }
        //guru
        if (empty($get_user)) {
            $get_user = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_ft') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from guru_ft u where u.token = '".$token."'","row");
        }
        if (empty($get_user)) {
            $get_user = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_sma') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from guru_sma u where u.token = '".$token."'","row");
        }
        if (empty($get_user)){
            $get_user = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_smp') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from guru_smp u where u.token = '".$token."'","row");
        }
        if (empty($get_user)){
            $get_user = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_sd') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from guru_sd u where u.token = '".$token."'","row");
        }
        if (empty($get_user)) {
            $this->response(array('status' => 0, 'message' => 'Token tidak valid', 'data' => array()), 200);
            return;
        }

        // ambil start_checkout shift milik role user (dipakai memilah laporan masuk / pulang)
        $start_checkout = "12:00:00"; // ponytail: fallback jika shift tidak ditemukan, samakan dengan mesin fingerprint
        if (!empty($get_user)) {
            $get_shift = $this->mymodel->withquery("select s.start_checkin, s.limit_checkin, s.start_checkout, s.limit_checkout, s.hari from presensi_setting_role r join presensi_setting_shift s on s.id_role = r.id where r.nama_role = '".$get_user->presensi_role."'","result");
            if (!empty($get_shift)) {
                $hari_laporan = strtolower(formatHari($presensi_date));
                foreach ($get_shift as $gs) {
                    if (strpos(','.strtolower($gs->hari).',', ','.$hari_laporan.',') !== false) {
                        $start_checkout = $gs->start_checkout;
                        break;
                    }
                }
            }
        }
        // cek apakah data hari itu sudah ada
        $cek_data = $this->mymodel->withquery("select * from presensi_office where npp = '".$get_user->npp."' and presensi_date = '".$presensi_date."'","row");

        $data = new stdClass();
        //cek sort dari staff, guru, pimpinan
        
        if(!empty($get_user) && empty($cek_data)){
            $data_insert = array(
                "npp" => $get_user->npp,
                "nama_lengkap" => $get_user->nama_lengkap,
                "presensi_date" => $presensi_date,
                "presensi_hari" => formatHari($presensi_date),
                "role" => $get_user->presensi_role,
                "presensi_device" => "Labscib Apps",
                "status_presensi" => "TROUBLE",
                "keterangan" => (!empty($keterangan)) ? $keterangan : "Presensi via Apps, Mesin Trouble.",
                "file_report" => null,
                "report_status" => 0,
                "created_at" => date('Y-m-d H:i:s'),
            );
            //cek apakah check in atau check out (berdasarkan jendela waktu shift)
            $attendance_type = "";
            if (strtotime($check_in) < strtotime($start_checkout)) {
                if (empty($cek_data->check_in)) {
                    $attendance_type = "check_in";
                    $data_insert['check_in'] = $check_in;
                    $data_insert['status_presensi'] = "TROUBLE";
                    $data_insert['report_status'] = 0;
                }
                else if (empty($cek_data->check_out)) {
                    // check_in sudah terisi + laporan < start_checkout → pulang lebih awal (approve() akan menilai E)
                    $attendance_type = "check_out";
                    $data_insert['check_out'] = $check_in;
                    $data_insert['status_presensi_selesai'] = "TROUBLE";
                    $data_insert['report_status_end'] = 0;
                }
            }
            else if (empty($cek_data->check_out)) {
                $attendance_type = "check_out";
                $data_insert['check_out'] = $check_in;
                $data_insert['status_presensi_selesai'] = "TROUBLE";
                $data_insert['report_status_end'] = 0;
            }

            if (empty($attendance_type)) {
                $msg = array('status' => 0, 'message' => 'Laporan tidak dapat diproses: waktu laporan di luar jendela presensi yang tersedia atau sudah terisi', 'data' => array());
                $status = 200;
                $this->response($msg, $status);
                return;
            }

            //Foto Laporan
            if (!is_dir(FCPATH . '/uploads/presensi_office/'.$get_user->npp."/")) {
                mkdir(FCPATH . '/uploads/presensi_office/'.$get_user->npp."/", 0777);
            }
            if (!empty($_FILES['file_report']['name'])) {
                if (!is_dir(FCPATH . '/uploads/presensi_office/'.str_replace(" ", "_", $get_user->npp).'/')) {
                    mkdir(FCPATH . '/uploads/presensi_office/'.str_replace(" ", "_", $get_user->npp).'/', 0777);
                }
                $uploaddir = './uploads/presensi_office/'.str_replace(" ", "_", $get_user->npp).'/';
                $img = explode('.', $_FILES['file_report']['name']);
                $extension = end($img);
                $file_name =  date('y-m-d h.i').".".$extension;
                $uploadfile = $uploaddir.$file_name;
                if (!move_uploaded_file($_FILES['file_report']['tmp_name'], $uploadfile)) {
                    $msg = array('status' => 0, 'message' => 'Gagal mengupload foto laporan', 'data' => array());
                    $status = 200;
                    $this->response($msg, $status);
                    return;
                }
                if ($attendance_type == "check_in") {
                    $data_insert['file_report'] = $get_user->npp."/".$file_name;
                }
                else {
                    $data_insert['file_report_end'] = $get_user->npp."/".$file_name;
                }
            }

            $insert = $this->mymodel->insertid('presensi_office', $data_insert);
            $data = $this->mymodel->withquery("select * from presensi_office where npp = '".$get_user->npp."' and presensi_date = '".$presensi_date."'","row");
            $msg = array('status' => 1, 'message' => 'Berhasil menambah data', 'data' => $data);
            $status = 200;
            $this->response($msg, $status);
        }
        else if(!empty($get_user) && !empty($cek_data) && (!empty($cek_data->check_in) || !empty($cek_data->check_out)) ){
            $data_insert = array(
                "npp" => $get_user->npp,
                "nama_lengkap" => $get_user->nama_lengkap,
                "presensi_date" => $presensi_date,
                "presensi_hari" => formatHari($presensi_date),
                "role" => $get_user->presensi_role,
                "presensi_device" => "Labscib Apps",
                "keterangan" => (!empty($keterangan)) ? $keterangan : "Presensi via Apps, Mesin Trouble.",
                "updated_at" => date('Y-m-d H:i:s'),
            );
            //cek apakah check in atau check out (berdasarkan jendela waktu shift)
            $attendance_type = "";
            if (strtotime($check_in) < strtotime($start_checkout)) {
                if (empty($cek_data->check_in)) {
                    $attendance_type = "check_in";
                    $data_insert['check_in'] = $check_in;
                    $data_insert['status_presensi'] = "TROUBLE";
                    $data_insert['report_status'] = 0;
                }
                else if (empty($cek_data->check_out)) {
                    // check_in sudah terisi + laporan < start_checkout → pulang lebih awal (approve() akan menilai E)
                    $attendance_type = "check_out";
                    $data_insert['check_out'] = $check_in;
                    $data_insert['status_presensi_selesai'] = "TROUBLE";
                    $data_insert['report_status_end'] = 0;
                }
            }
            else if (empty($cek_data->check_out)) {
                $attendance_type = "check_out";
                $data_insert['check_out'] = $check_in;
                $data_insert['status_presensi_selesai'] = "TROUBLE";
                $data_insert['report_status_end'] = 0;
            }

            if (empty($attendance_type)) {
                $msg = array('status' => 0, 'message' => 'Laporan tidak dapat diproses: waktu laporan di luar jendela presensi yang tersedia atau sudah terisi', 'data' => array());
                $status = 200;
                $this->response($msg, $status);
                return;
            }

            //Foto Laporan
            if (!is_dir(FCPATH . '/uploads/presensi_office/'.$get_user->npp."/")) {
                mkdir(FCPATH . '/uploads/presensi_office/'.$get_user->npp."/", 0777);
            }
            if (!empty($_FILES['file_report']['name'])) {
                if (!is_dir(FCPATH . '/uploads/presensi_office/'.str_replace(" ", "_", $get_user->npp).'/')) {
                    mkdir(FCPATH . '/uploads/presensi_office/'.str_replace(" ", "_", $get_user->npp).'/', 0777);
                }
                $uploaddir = './uploads/presensi_office/'.str_replace(" ", "_", $get_user->npp).'/';
                $img = explode('.', $_FILES['file_report']['name']);
                $extension = end($img);
                $file_name =  date('y-m-d h.i').".".$extension;
                $uploadfile = $uploaddir.$file_name;
                if (!move_uploaded_file($_FILES['file_report']['tmp_name'], $uploadfile)) {
                    $msg = array('status' => 0, 'message' => 'Gagal mengupload foto laporan', 'data' => array());
                    $status = 200;
                    $this->response($msg, $status);
                    return;
                }
                if ($attendance_type == "check_in") {
                    $data_insert['file_report'] = $get_user->npp."/".$file_name;
                }
                else {
                    $data_insert['file_report_end'] = $get_user->npp."/".$file_name;
                }
            }

            $insert = $this->mymodel->update('presensi_office', $data_insert, "id_presensi", $cek_data->id_presensi);
            $data = $this->mymodel->withquery("select * from presensi_office where npp = '".$get_user->npp."' and presensi_date = '".$presensi_date."'","row");
            $msg = array('status' => 1, 'message' => 'Berhasil mengubah data', 'data' => $data);
            $status = 200;
            $this->response($msg, $status);
        }
        else{
            $msg = array('status' => 0, 'message' => 'Sudah pernah melakukan absen hari ini', 'data' => array());
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

}
