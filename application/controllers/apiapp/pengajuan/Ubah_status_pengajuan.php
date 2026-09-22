<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Ubah_status_pengajuan extends REST_Controller {
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
        if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $id_submission = $this->post('id_submission');
        $nama_lengkap = $this->post('approver_name');
        $status_submission = $this->post('status_submission'); //0 menunggu persetujuan, 1 disetujui, 2 ditolak
        $update = $this->mymodel->update('presensi_office_submission', array('submission_status'=>$status_submission,'updated_by'=>$nama_lengkap, 'updated_at'=>date('Y-m-d H:i:s')), 'id_submission', $id_submission);
        if (!empty($update)) {
            if ($status_submission == 1) {
                //cek presensi_office
                $get_submission = $this->mymodel->withquery("select s.*, p.nama_tabel, p.head_role, t.presensi_code from presensi_office_submission s 
                left join presensi_setting_role p on p.nama_role = s.role 
                join presensi_submission_type t on t.code = s.submission_code 
                where s.id_submission = '".$id_submission."'","row");
                $list_day = $this->get_working_days($get_submission->date_start,$get_submission->date_end);
                foreach ($list_day as $key => $value) {
                    $cek_presensi = $this->mymodel->withquery("select * from presensi_office where npp = ? and presensi_date = ?","row", array($get_submission->npp, $value));
                    $data_insert = array(
                        'npp'=>$get_submission->npp,
                        'presensi_date'=>$value, 
                        'status_presensi'=>$get_submission->presensi_code,
                        'status_presensi_selesai'=>$get_submission->presensi_code,
                        'npp'=>$get_submission->npp,
                        'nama_lengkap'=>$get_submission->nama_lengkap,
                        'presensi_hari'=>formatHari($value),
                        'role' => $get_submission->role,
                        'presensi_device'=>'Labscib Apps',
                        'keterangan'=>$get_submission->notes,
                        'updated_by' => $nama_lengkap
                    );
                    if (empty($cek_presensi)) {
                        $data_insert['created_at'] = date('Y-m-d H:i:s');
                        //insert
                        $insert = $this->mymodel->insert('presensi_office',$data_insert);
                    }
                    else if (!empty($cek_presensi)) {
                        //update
                        $data_insert['updated_at'] = date('Y-m-d H:i:s');
                        $update = $this->mymodel->update('presensi_office', $data_insert, 'id_presensi', $cek_presensi->id_presensi);
                    }

                }
            }
            $msg = array('status' => 200, 'message'=>'Berhasil mengubah status pengajuan');
        }
        else{
            $msg = array('status' => 200, 'message'=>'Gagal mengubah status pengajuan ');
        }
        $this->response($msg);
        // $this->response($tanggal_absen);
    }

    function get_working_days($start, $end)
    {
        $result = [];

        $startDate = new DateTime($start);
        $endDate   = new DateTime($end);

        // include end date
        $endDate->modify('+1 day');

        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);

        foreach ($period as $date) {
            $dayOfWeek = $date->format('N'); // 1 (Senin) - 7 (Minggu)

            if ($dayOfWeek < 6) { // exclude Sabtu(6) & Minggu(7)
                $result[] = $date->format('Y-m-d');
            }
        }

        return $result;
    }
}
