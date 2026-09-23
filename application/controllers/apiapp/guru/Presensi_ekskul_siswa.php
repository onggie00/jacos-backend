<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Presensi_ekskul_siswa extends REST_Controller {
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
        if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $jenjang=$this->post('jenjang');
        $list_jenjang = explode(",", $jenjang);
        $list_siswa = explode(",",$this->post('id_siswa'));
        $id_siswa_aktif = $this->post('id_siswa');
        $id_ekskul = $this->post('id_ekskul');
        $waktu_absen = $this->post('waktu_absen');
        $tanggal_absen = $this->post('tanggal_absen'); //2023-11-01
        $today = strtolower(formatHari(date("Y-m-d", strtotime($this->post('tanggal_absen')))));

        $get_ekskul = $this->mymodel->withquery("select nama as nama_ekskul from ekskul where id_ekskul = '".$id_ekskul."'","row");
        for ($i=0; $i < count($list_siswa); $i++) {
            $where="where id_siswa_aktif = '$list_siswa[$i]' and jenjang = '$list_jenjang[$i]'";
            $where_tanggalabsen="and tanggal_absen = '$tanggal_absen'";
            $where_ekskul="and id_ekskul = '$id_ekskul'";

            $check_if_presented = $this->mymodel->withquery("select * from ekskul_presensi_siswa_".$list_jenjang[$i]." $where $where_tanggalabsen $where_ekskul order by id desc","row");

            if ($check_if_presented){
                $data = array(
                    "jenjang" => $list_jenjang[$i],
                    "id_siswa_aktif" => $list_siswa[$i],
                    "id_ekskul" => $id_ekskul,
                    "hari_absen" => strtolower($today),
                    "waktu_absen" => $waktu_absen,
                    "tanggal_absen" => $tanggal_absen,
                    "status_absen" => $this->post('status_absen'),
                );
                $this->mymodel->update("ekskul_presensi_siswa_".$list_jenjang[$i], $data, "id", $check_if_presented->id);
                $msg = array('status' => 401, 'message'=>'Berhasil Ubah Presensi Ekskul Siswa.');
                //$msg = array('status' => 401, 'message'=>'Siswa sudah melakukan absen ekskul untuk hari ini!');
            }else{
                $data = array(
                    "jenjang" => $list_jenjang[$i],
                    "id_siswa_aktif" => $list_siswa[$i],
                    "id_ekskul" => $id_ekskul,
                    "hari_absen" => strtolower($today),
                    "waktu_absen" => $waktu_absen,
                    "tanggal_absen" => $tanggal_absen,
                    "status_absen" => $this->post('status_absen'),
                );
                if (!empty($data)) {
                    $insert=$this->mymodel->insertid("ekskul_presensi_siswa_".$list_jenjang[$i], $data);
                    if($insert){
                    $nama_kolom = "id_siswa_".$list_jenjang[$i];
                    $get_siswa_aktif = $this->mymodel->withquery("select id_siswa_".$list_jenjang[$i]."_aktif as id_siswa_aktif, nama_lengkap, id_siswa_".$list_jenjang[$i].", device_id_siswa, device_id_ortu from siswa_".$list_jenjang[$i]."_aktif where id_siswa_".$list_jenjang[$i]."_aktif = ".$list_siswa[$i],"row");
                    $get_siswa = $this->mymodel->withquery("select notelp_ibu, notelp_ayah, nama_lengkap from siswa_".$list_jenjang[$i]." where id_siswa_".$list_jenjang[$i]." ='".$get_siswa_aktif->$nama_kolom."'","row");
                    //cek setting notifikasi ekskul ortu
                    $cek_setting = $this->mymodel->withquery("select * from setting_notifikasi_ortu where id_siswa_aktif = '".$get_siswa_aktif->id_siswa_aktif."' and jenjang = '".$list_jenjang[$i]."'","row");
                    if (!empty($get_siswa->notelp_ibu)) {
                        $notelp = $get_siswa->notelp_ibu;
                    }
                    else if(!empty($get_siswa->notelp_ayah)){
                        $notelp = $get_siswa->notelp_ayah;
                    }
                    $notelp = $this->cek_notelp($notelp);
                    $nama_siswa = $get_siswa_aktif->nama_lengkap;
                        if ($data['status_absen'] == "Hadir") {
                            $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." mengikuti Ekstrakurikuler, Terimakasih";
                        }
                        else if($data['status_absen'] == "Tidak hadir"){
                            $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." mengikuti Ekstrakurikuler, Terimakasih";
                        }
                        if (!empty($cek_setting) && $cek_setting->notifikasi_presensi_ekskul == 1) {
                            $this->send_notif("Presensi Ekstrakurikuler ".ucfirst($get_ekskul->nama_ekskul)." Siswa ".strtoupper($nama_siswa), $pesan, $get_siswa_aktif->device_id_ortu, array("id_siswa_aktif" => $id_siswa_aktif, "id_presensi" => $insert, "jenjang" => $list_jenjang[$i]) );
                        }
                        if (!empty($cek_setting) && $cek_setting->notifikasi_wa_presensi == 1) {
                            $this->send_wa2($notelp,$nama_siswa, $data['status_absen'], $get_ekskul->nama_ekskul);
                        }
                        $msg = array('status' => 200, 'message'=>'Berhasil melakukan absen!', 'data' => $data);
                    }else{
                        $msg = array('status' => 401, 'message'=>'Gagal melakukan absen!', 'data' => $data);
                    }
                }
            }
        }
        $this->response($msg);
    }

    public function send_wa($notelp, $nama_siswa, $status_absen, $nama_ekskul){
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
    if ($status_absen == "Hadir") {
        $message = "Siswa ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($status_absen)." di Ekstrakurikuler ".ucfirst($nama_ekskul).", Terimakasih";
    }
    else if($status_absen == "Tidak Hadir"){
        $message = "Siswa ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($status_absen)." di Ekstrakurikuler ".ucfirst($nama_ekskul).", Terimakasih";
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

  public function send_wa2($notelp, $nama_siswa, $status_absen, $nama_ekskul){
    if ($status_absen == "Hadir") {
        $message = "Siswa ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($status_absen)." di Ekstrakurikuler ".ucfirst($nama_ekskul).", Terimakasih";
    }
    else if($status_absen == "Tidak Hadir"){
        $message = "Siswa ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($status_absen)." di Ekstrakurikuler ".ucfirst($nama_ekskul).", Terimakasih";
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
      //echo $response;
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
    }
    return $hp;
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
        $url = 'https://fcm.googleapis.com/v1/projects/labscib-app/messages:send';
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
