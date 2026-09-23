<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Send_wa extends MY_Controller {
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

        $today = date("Y-m-d");

        //get pengaturan_akun -> wa_multichat
        $get_pengaturan_akun = $this->mymodel->getall("pengaturan_akun");
        foreach ($get_pengaturan_akun as $key => $item) {
            if ($item->name_setting == 'wa_multichat_url') {
                $wa_multichat_url = $item->value;
            }
            if ($item->name_setting == 'wa_multichat_params') {
                $wa_multichat_params = $item->value;
            }
            if ($item->name_setting == 'wa_multichat_token') {
                $wa_multichat_token = $item->value;
            }
            if ($item->name_setting == 'wa_multichat_instance_id') {
                $wa_multichat_instance_id = $item->value;
            }
            if ($item->name_setting == 'wa_multichat_token') {
                $wa_multichat_token = $item->value;
            }
        }
        
        $notelp = $this->input->get('notelp');
        $pesan = "Test Whatsapp ".date("Y-m-d H:i:s");

        //curl kirim wa
        /* $params = "";
        $wa_multichat_params = str_replace("TOKEN_HERE", $wa_multichat_token, $wa_multichat_params);
        $wa_multichat_params = str_replace("JID_HERE", $notelp, $wa_multichat_params);
        $wa_multichat_params = str_replace("INSTANCE_HERE", $wa_multichat_instance_id, $wa_multichat_params);
        $wa_multichat_params = str_replace("MSG_HERE", $pesan, $wa_multichat_params);
        $url = $wa_multichat_url . '?' . $wa_multichat_params;
        echo $url;
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo json_decode($response);
        } */

        //curl kirim wa

        /* $nama_siswa = $value->nama_lengkap;
        if (!empty($value->notelp_ibu)) {
            $notelp = $value->notelp_ibu;
        }
        else if(!empty($value->notelp_ayah)){
            $notelp = $value->notelp_ayah;
        }
        $notelp = $this->cek_notelp($notelp); */
        $type = $this->input->get('type');
        if($type == "omnichat"){
            $pesan = $pesan." by ".$type;
            $this->send_wa_omni($notelp,  $pesan);
        }
        else if($type == "wapanel"){
            $this->send_wa2($notelp,"Dummy Test", "Tidak Hadir");
        }

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

    public function send_wa($notelp, $nama_siswa, $status_absen){
    $url = "https://apiks.ristekmuslim.com/public/v1/client/login";
    $postfield = [
        'uid' => '6281584655084',
        'pass' => 'DyWzIL'
      ];
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    //curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
    curl_setopt($curlHandle, CURLOPT_POST, 1);
    curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($postfield));
    $results = json_decode(curl_exec($curlHandle), true);
    //curl_exec($curlHandle);
    curl_close($curlHandle);
    //print_r($results);
    $access_token = $results["data"]["token"];
    $authorization = "Authorization: Bearer ".$access_token;
    $request_header[] = "Authorization: Bearer ".$access_token;
    $request_header[] = "Content-Type: application/json";
    //print_r($results);
    
    $url_send = "https://apiks.ristekmuslim.com/client/v1/message/send-text";
    if ($status_absen == "Tidak Hadir") {
        $message = "Selamat pagi Bapak/Ibu, ".strtoupper($nama_siswa)." hari ini (".formatTanggal(date("Y-m-d")).") belum melakukan presensi digital sampai dengan pukul 08.00. Kami sangat berharap apabila Bapak/Ibu mengingatkan Ananda melakukan presensi digital sebagai bukti kehadiran di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
    }
    else{
        $message = "Selamat pagi Bapak/Ibu, ".strtoupper($nama_siswa)." hari ini (".formatTanggal(date("Y-m-d")).") belum melakukan presensi digital sampai dengan pukul 08.00. Kami sangat berharap apabila Bapak/Ibu mengingatkan Ananda melakukan presensi digital sebagai bukti kehadiran di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_send);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT,30);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "instanceID" => "65e53769788fe711d3342a69",
        "phone" => $notelp,
        "message" => $message
      ])
    );
    //$results_send = json_decode(curl_exec($ch), true);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) {
      //echo "cURL Error #:" . $err;
    } else {
      //echo $response;
    }
  }

    public function send_wa2($notelp, $nama_siswa, $status_absen){
        if ($status_absen == "Tidak Hadir") {
            $message = "Selamat pagi Bapak/Ibu, ".strtoupper($nama_siswa)." hari ini (".formatTanggal(date("Y-m-d")).") belum melakukan presensi digital sampai dengan pukul 08.00. Kami sangat berharap apabila Bapak/Ibu mengingatkan Ananda melakukan presensi digital sebagai bukti kehadiran di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
        }
        else{
            $message = "Selamat pagi Bapak/Ibu, ".strtoupper($nama_siswa)." hari ini (".formatTanggal(date("Y-m-d")).") belum melakukan presensi digital sampai dengan pukul 08.00. Kami sangat berharap apabila Bapak/Ibu mengingatkan Ananda melakukan presensi digital sebagai bukti kehadiran di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
        }
        $no_hp = $notelp; // No.HP yang dikirim (No.HP Penerima)
        $pesan = $message; // Pesan yang dikirim
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://app.wapanels.com/api/create-message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
        'appkey' => akunSetting('wapanel_appkey'),
        'authkey' =>  akunSetting('wapanel_authkey'),
        'to' => $no_hp,
        'message' => $pesan,
        'sandbox' => 'false'
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        } else {
        echo $response;
        }
    }

    public function send_wa_omni($notelp, $pesan){
        $url = "https://app.omnichat.id/api-app/whatsapp/send-message";
        $no_hp = $notelp; // No.HP yang dikirim (No.HP Penerima)
        $pesan = $pesan; // Pesan yang dikirim
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            "device_key" => akunSetting('omnichat_devicekey'),
            "api_key" =>  akunSetting('omnichat_apikey'),
            "phone" => $no_hp,
            "method"=> "text",
            "text"=> $pesan,
            "is_group"=> false //true jika ingin mengirim ke group & false jika ingin mengirim ke individual
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        } else {
        echo $response;
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
else if(substr(trim($nohp), 0, 1)=="8"){
            $hp    ="62".substr(trim($nohp), 1);
        }
 		else if(substr(trim($nohp), 0, 1)=="8"){
            $hp    ="62".substr(trim($nohp), 0);
            }

    }
    return $hp;
  }
}
