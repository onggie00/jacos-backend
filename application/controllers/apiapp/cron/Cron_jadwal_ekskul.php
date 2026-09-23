<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cron_jadwal_ekskul extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $today = formatHari(date("Y-m-d", strtotime("+1 days")));
        //$get_siswa_sd = $this->mymodel->withquery("select sa.id_siswa_sd_aktif, sa.nama_lengkap, sa.device_id_siswa, sa.device_id_ortu from siswa_sd_aktif sa natural left join presensi_sd p on sa.id_siswa_sd_aktif = p.id_siswa_aktif where p.tanggal_absen = '".$today."' and p.id_siswa_aktif is null", "result");
        $get_ekskul_today = $this->mymodel->withquery("select e.id_ekskul, e.nama, e.jam, e.jenjang from ekskul e where e.hari like '%".strtolower($today)."%'","result");

        if (!empty($get_ekskul_today)) {
            foreach ($get_ekskul_today as $key => $value) {
                //get list siswa member ekskul
                $list_siswa = $this->mymodel->withquery("select s.id_siswa_".$value->jenjang."_aktif as id_siswa_aktif , s.nama_lengkap, s.device_id_siswa, s.device_id_ortu from ekskul_member_".$value->jenjang." e join siswa_".$value->jenjang."_aktif s on e.id_siswa = s.id_siswa_".$value->jenjang."_aktif where e.id_ekskul = '".$value->id_ekskul."' and jenjang  = 'sd' and status_member = '1'","result");
                //echo $this->db->last_query();
                if (!empty($list_siswa)) {
                    foreach ($list_siswa as $key_siswa => $value_siswa) {
                        if (!empty($value_siswa->device_id_siswa)) {
                            $this->send_notif("Reminder Ekstrakurikuler ".ucfirst($value->nama), "Halo ".$value_siswa->nama_lengkap.", jangan lupa besok ".ucfirst($today)." ada ekstrakurikuler ".ucfirst($value->nama)." jam ".$value->jam." WIB. Jangan terlambat ya.", $value_siswa->device_id_siswa, array("id_siswa_aktif" => $value_siswa->id_siswa_aktif, "id_ekskul" => $value->id_ekskul, "jenjang" => $value->jenjang) );
                        }
                        if(!empty($value_siswa->device_id_ortu)){
                            $this->send_notif("Reminder Ekstrakurikuler ".ucfirst($value->nama), "Halo ".$value_siswa->nama_lengkap.", jangan lupa besok ".ucfirst($today)." ada ekstrakurikuler ".ucfirst($value->nama)." jam ".$value->jam." WIB. Jangan terlambat ya.", $value_siswa->device_id_ortu, array("id_siswa_aktif" => $value_siswa->id_siswa_aktif, "id_ekskul" => $value->id_ekskul, "jenjang" => $value->jenjang) );
                        }
                    }
                }
            }
        }

    }


    public function send_notif($title,$desc,$fcm_id,$data){
        //$firebaseService = new FirebaseService();
        $token = $this->getAccessToken();
        $id_siswa_aktif = $data['id_siswa_aktif'];
        $id_ekskul = $data['id_ekskul'];
        $jenjang = $data['jenjang'];
        $data = [
            'token' => $fcm_id,
            'title' => $title,
            'body' => $desc
        ];
        $data_notification = array(
            'message' => array(
                'token' => $fcm_id,
                'notification' => array(
                    'title' => $data['title'],
                    'body' => $data['body']
                ),
                'data' => array(
                    "id_siswa_aktif" => $id_siswa_aktif,
                    "id_ekskul" => $id_ekskul,
                    "jenjang" => $jenjang
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
        $url = 'https://fcm.googleapis.com/v1/projects/' . FIREBASE_PROJECT_ID . '/messages:send';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
        $result = curl_exec($ch);
        curl_close($ch);
        /*print_r($result);
        echo "<br/><br/>";*/
    }

}
