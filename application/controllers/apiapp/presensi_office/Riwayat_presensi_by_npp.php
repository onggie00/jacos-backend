<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Riwayat_presensi_by_npp extends REST_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->library('Presensi_office_rules');
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
        if (isset($headers['x-api-token'])) {
            $token = $headers['x-api-token'];
        }

        if (empty($token)) {
            $this->response(array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array()), 200);
            return;
        }

        //$npp = $this->get("npp");
        $start_date = (!empty($this->get("start_date"))) ? $this->get("start_date") : date("Y-m-d");
        $end_date = (!empty($this->get("end_date"))) ? $this->get("end_date") : date("Y-m-d");
        $presensi_code = (!empty($this->get("presensi_code"))) ? $this->get("presensi_code") : "";
        $npp = (!empty($this->get("npp"))) ? $this->get("npp") : "";

        $data = new stdClass();
        //cek sort dari staff, guru, pimpinan
        //pimpinan
        $cek_pimpinan = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sd') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pimpinan_sd u where u.token = '".$token."'","row");
        $unit_sekolah = "sd";
        if(empty($cek_pimpinan)) {
            $cek_pimpinan = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_smp') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pimpinan_smp u where u.token = '".$token."'","row");
            $unit_sekolah = "smp";
        }
        if (empty($cek_pimpinan)) {
            $cek_pimpinan = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sma') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pimpinan_sma u where u.token = '".$token."'","row");
            $unit_sekolah = "sma";
        }
        if (!empty($cek_pimpinan)) {
            $npp_list = array();
            if (empty($npp)){
                // mode semua: riwayat seluruh pegawai + guru satu unit (mengikuti pattern List_guru_pegawai)
                $get_data = $cek_pimpinan;
                $rows = $this->mymodel->withquery("select npp from pegawai where presensi_role like '%".strtoupper($unit_sekolah)."%'","result");
                if (!empty($rows)) { foreach ($rows as $r) { $npp_list[] = $r->npp; } }
                $rows = $this->mymodel->withquery("select npp from guru_".$unit_sekolah,"result");
                if (!empty($rows)) { foreach ($rows as $r) { $npp_list[] = $r->npp; } }
                $rows = $this->mymodel->withquery("select npp from guru_ft","result");
                if (!empty($rows)) { foreach ($rows as $r) { $npp_list[] = $r->npp; } }
            } else {
                //pegawai
                $get_data = $this->mymodel->withquery("select u.id_pegawai as id_user, concat('pegawai') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from pegawai u where npp = '".$npp."'","row");
                //guru
                if (empty($get_data)) {
                    $get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_ft') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from guru_ft u where npp = '".$npp."'","row");
                }
                if (empty($get_data)) {
                    $get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_sma') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from guru_sma u where npp = '".$npp."'","row");
                }
                if (empty($get_data)){
                    $get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_smp') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from guru_smp u where npp = '".$npp."'","row");
                }
                if (empty($get_data)){
                    $get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_sd') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap from guru_sd u where npp = '".$npp."'","row");
                }
            }

            {
                if (!empty($npp)){
                    $where = "where p.npp = '".$get_data->npp."' ";
                } else {
                    $where = "where p.npp in ('".implode("','", $npp_list)."') ";
                }
                if(!empty($this->get("start_date")) && !empty($this->get("end_date"))){
                    $where .= "and presensi_date >= '$start_date' and presensi_date <= '$end_date'";
                }
                else if(!empty($this->get("start_date"))){
                    $where .= "and presensi_date >= '$start_date'";
                }
                if(!empty($this->get("presensi_code"))){
                    $where .= "and status_presensi = '$presensi_code'";
                }
                $riwayat = $this->mymodel->withquery("select p.id_presensi, p.npp, p.nama_lengkap, p.presensi_date, p.check_in, p.status_presensi, ps.nama_status, p.check_out, p.status_presensi_selesai, p.presensi_device, p.file_report, p.report_status, p.file_report_end, p.report_status_end from presensi_office p 
                left join presensi_setting_status ps on p.status_presensi = ps.code 
                $where order by presensi_date DESC, check_in DESC, check_out DESC","result");
                $data->is_presensi_today = 0;
                $data->is_check_in_complete = 0;
                $data->is_check_out_complete = 0;
                $data->can_report_check_in = 1;
                $data->can_report_check_out = 1;
                $list_status = $this->mymodel->withquery("select * from presensi_setting_status", "result");

                // Buat lookup array agar tidak perlu nested loop
                $status_map = [];
                foreach ($list_status as $s) {
                    $status_map[$s->code] = $s->nama_status;
                }

                foreach ($riwayat as $key => $value) {

                    // ── STATUS CHECK-IN ──────────────────────────────────────────
                    $nama_checkin = $status_map[$value->status_presensi] ?? $value->status_presensi;

                    if (!empty($value->file_report)) {
                        $value->file_report = base_url($value->file_report);
                    }
                    if ($this->presensi_office_rules->isPendingCheckin($value)) {
                        $value->status_presensi      = "Pengajuan Masuk";
                        $value->status_presensi_text = "Pengajuan Masuk";
                    } else {
                        $value->status_presensi      = $nama_checkin;
                        $value->status_presensi_text = $nama_checkin;
                    }

                    // ── STATUS CHECK-OUT ─────────────────────────────────────────
                    $nama_checkout = $status_map[$value->status_presensi_selesai] ?? $value->status_presensi_selesai;

                    if (!empty($value->file_report_end)) {
                        $value->file_report_end = base_url($value->file_report_end);
                    }
                    if ($this->presensi_office_rules->isPendingCheckout($value)) {
                        $value->status_presensi_selesai      = "Pengajuan Pulang";
                        $value->status_presensi_selesai_text = "Pengajuan Pulang";
                    } else {
                        $value->status_presensi_selesai      = $nama_checkout;
                        $value->status_presensi_selesai_text = $nama_checkout;
                    }

                    // ── CEK PRESENSI HARI INI ────────────────────────────────────
                    // Cek berdasarkan tanggal hari ini, bukan break di iterasi pertama
                    if ($value->presensi_date == date('Y-m-d')) {
                        if (!empty($value->check_in)) {
                            $data->is_check_in_complete = 1;
                            $data->can_report_check_in = 0;
                        }
                        if (!empty($value->check_out)) {
                            $data->is_check_out_complete = 1;
                            $data->can_report_check_out = 0;
                        }
                        if (!empty($value->check_in) && !empty($value->check_out)) {
                            $data->is_presensi_today = 1;
                        }
                    }
                }
                $data->id_user = $get_data->id_user;
                $data->role = $get_data->role;
                $data->npp = $get_data->npp;
                $data->emp_code = $get_data->emp_code;
                $data->presensi_role = $get_data->presensi_role;
                $data->nama_lengkap = $get_data->nama_lengkap;
                $data->riwayat = (!empty($riwayat)) ? $riwayat : array();
                $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $data);
                $status = 200;
                $this->response($msg, $status);
            }
        }
        else{
            $msg = array('status' => 0, 'message' => 'Unauthorized', 'data' => array());
            $status = 200;
            $this->response($msg, $status);
        }
        
        if(!empty($get_data)){
            
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

}
