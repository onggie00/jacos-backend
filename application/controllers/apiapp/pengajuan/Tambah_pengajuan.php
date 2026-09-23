<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Tambah_pengajuan extends REST_Controller {
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

        $npp = $this->post('npp');
        $nama_lengkap = $this->post('nama_lengkap');
        $submission_code = $this->post('submission_code');
        $notes = $this->post('notes');
        $submission_status = 0;
        $date_start = $this->post('date_start');
        $date_end = $this->post('date_end');
        $role = $this->post('role');
        
        if ($role == "director"){
            $jenjang = $this->post('jenjang');
            $get_user = $this->mymodel->withquery("select u.*, p.head_role from pimpinan_".strtolower($jenjang)." u 
            join presensi_setting_role p on p.nama_role = u.presensi_role
            where u.npp = '".$npp."'","row");
        }
        else if ($role == "teacher"){
            $jenjang = $this->post('jenjang');
            $get_user = $this->mymodel->withquery("select u.*, p.head_role from guru_".strtolower($jenjang)." u 
            join presensi_setting_role p on p.nama_role = u.presensi_role 
            where u.npp = '".$npp."'","row");
        }
        else if ($role == "employee"){
            $jenjang = $this->post('jenjang');
            $get_user = $this->mymodel->withquery("select u.*, p.head_role from pegawai u 
            join presensi_setting_role p on p.nama_role = u.presensi_role 
            where u.npp = '".$npp."'","row");
        }

        //get rule
        $rule = $this->mymodel->withquery("select * from presensi_submission_type where code = ?","row", array($submission_code));

        if (empty($rule)) {
            $msg = array('status' => 0, 'message'=>'Kode pengajuan tidak ditemukan' ,array());
            $status="200";
            $this->response($msg,$status);
            die();
        }
        
        if($rule->file_required == 1 && $_FILES['file_submission']['name'] == ""){
            $msg = array('status' => 0, 'message'=>'File tidak boleh kosong' ,array());
            $status="200";
            $this->response($msg,$status);
            die();
        }

        $data_insert = array(
            'npp' => $npp,
            'nama_lengkap' => $nama_lengkap,
            'submission_code' => $submission_code,
            'notes' => $notes,
            'submission_status' => $submission_status,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'role' => $get_user->presensi_role,
            'head_role' => $get_user->head_role,
            'created_at' => date('Y-m-d H:i:s'),
        );

        //File Submission
        if (!empty($_FILES['file_submission']['name'])) {
            $uploaddir = "./uploads/submission/".$npp."/";
            if (!is_dir($uploaddir)) {
                mkdir($uploaddir, 0777, true);
            }
                $img = explode('.', $_FILES['file_submission']['name']);
                $extension = end($img);
                $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_submission']['name']).".".$extension;
                $uploadfile = $uploaddir.$file_name;
                $status = 0;
            if (move_uploaded_file($_FILES['file_submission']['tmp_name'], $uploadfile)) {
                $data_insert['file_submission'] = $uploadfile;
                $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
            }
        }

        $this->db->trans_begin();
        $insert = $this->mymodel->insertid('presensi_office_submission', $data_insert);
        //insert into presensi_office
        $list_day = $this->get_working_days($date_start,$date_end);
        foreach ($list_day as $key => $value) {
            $cek_presensi = $this->mymodel->withquery("select * from presensi_office where npp = '".$npp."' and presensi_date = '".$value."'","row");
            if (empty($cek_presensi)) {
                $data_insert_presensi = array(
                    'npp' => $npp,
                    'nama_lengkap' => $get_user->nama_lengkap,
                    'presensi_date' => $value,
                    'role' => $get_user->presensi_role,
                    'id_izin' => $insert,
                    'presensi_device' => "Labscib Apps",
                    'presensi_hari' => formatHari(date('Y-m-d', strtotime($value))),
                    'status_presensi' => "PENGAJUAN ".$rule->presensi_code,
                    'status_presensi_selesai' => "PENGAJUAN ".$rule->presensi_code,
                );
                $this->mymodel->insert('presensi_office', $data_insert_presensi);
            }
        }
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
        }
        else
        {
            $this->db->trans_commit();
        }
        if (!empty($insert)) {
            $data_insert['id'] = $insert;
            $msg = array('status' => 200, 'message'=>'Berhasil menambahkan pengajuan', 'data' => $data_insert);
        }
        else{
            $msg = array('status' => 200, 'message'=>'Gagal menambahkan pengajuan (Pengajuan sudah tercatat)', 'data' => $data_insert);
        }
        $this->response($msg);
        // $this->response($tanggal_absen);
    }

    public function send_notif($title,$desc,$fcm_id,$data){
        //$firebaseService = new FirebaseService();
        $id_siswa_aktif = $data['id_siswa_aktif'];
        $id_presensi = $data['id_presensi'];
        $jenjang = $data['jenjang'];
        $token = $this->getAccessToken();
        $data = [
            'token' => $fcm_id,
            'title' => $title,
            'body' => $desc,
            'id_presensi' => $data['id_presensi'],
            'jenjang' => $data['jenjang'],
            'nis' => $data['nis']
        ];
        $data_notification = array(
            'message' => array(
                'token' => $fcm_id,
                'notification' => array(
                    'title' => $data['title'],
                    'body' => $data['body']
                ),
                'data' => array(
                    'id_siswa_aktif' => $id_siswa_aktif,
                    'id_presensi' => $id_presensi,
                    'jenjang' => $jenjang
                )
                //'data' => array('route' => 'detailAnnouncement?idAnnouncement='.$data['id_agenda'])
            )
        );
        $send_notif = $this->firebase_send_notif($data);
        //$result = $firebaseService->sendMessage($fcm_id, $data['title'] ?? '', $data['body'] ?? '', array('route' => 'detailAnnouncement?idAnnouncement='.$data['id_agenda']));
        //print_r($token);
    }

    function getAccessToken(){

        //require "google-api-php-client/vendor/autoload.php";
        $client= new \Google_Client();
        //$client= new Google\Client();
        $client->setAuthConfig(PRIVATE_FIREBASE_KEY);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        //$client->fetchAccessTokenWithAssertion();
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        //result array(access_token, expires_in, token_type, created)
        $result=$token['access_token'];
                
        return $result;
    
    }
    function firebase_send_notif($data){
        $headers = [
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Content-Type: application/json'
        ];
    
        $fields = [
            'message' => [
                'token' => $data['token'],
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body']
                ]
            ]
        ];
    
        $fields = json_encode($fields);
        $url = 'https://fcm.googleapis.com/v1/projects/labscib-app/messages:send';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
        $result = curl_exec($ch);
        curl_close($ch);
        $this->mymodel->update("presensi_".$data['jenjang'], array("nis" => $data['nis'], "notif_response" => json_encode($result)), "id", $data['id_presensi']);
        /*print_r($result);
        echo "<br/><br/>";*/
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
