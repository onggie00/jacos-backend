<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Presensi_siswa extends REST_Controller {
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
        $id_siswa_aktif = $this->post('id_siswa_aktif');
        $waktu_absen = $this->post('waktu_absen');
        $hari_absen = $this->post('hari_absen');
        $tanggal_absen = $this->post('tanggal_absen'); //2023-11-01
        $list_siswa = explode(",", $id_siswa_aktif);
        $list_jenjang = explode(",", $jenjang);
        $status_hadir = $this->post("status_hadir");
        $data_presensi_siswa = array();
        for ($i=0; $i < count($list_siswa); $i++) {
            $where="where id_siswa_aktif = '$list_siswa[$i]'";
            $where_tanggalabsen="and tanggal_absen = '$tanggal_absen'";
            $get_siswa_aktif = $this->mymodel->withquery("select id_siswa_".$jenjang."_aktif as id_siswa_aktif, nama_lengkap, device_id_siswa, device_id_ortu, id_siswa_".$jenjang." from siswa_".$jenjang."_aktif where id_siswa_".$jenjang."_aktif = ".$list_siswa[$i],"row");
            $get_siswa = null;
            $nama_kolom = "id_siswa_".$jenjang;
            $notelp = "";
            $nama_siswa = "";
            if (!empty($get_siswa_aktif)) {
                $get_siswa = $this->mymodel->withquery("select notelp_ibu, notelp_ayah, nama_lengkap from siswa_".$jenjang." where id_siswa_".$jenjang." =".$get_siswa_aktif->$nama_kolom,"row");
                if (!empty($get_siswa->notelp_ibu)) {
                    $notelp = $get_siswa->notelp_ibu;
                }
                else if(!empty($get_siswa->notelp_ayah)){
                    $notelp = $get_siswa->notelp_ayah;
                }
                $notelp = $this->cek_notelp($notelp);
                $nama_siswa = $get_siswa_aktif->nama_lengkap;
            }

            $data = array(
                //"jenjang" => $jenjang,
                "id_siswa_aktif" => $list_siswa[$i],
                "wifi_ssid" => "Presensi oleh Pimpinan",//$cek_ssid->id_pengaturan,
                "wifi_ip" => "",
                "hari_absen" => $hari_absen,
                "waktu_absen" => $waktu_absen,
                "tanggal_absen" => $tanggal_absen,
                "status_absen" => ucfirst($status_hadir),
            );
            if (!empty($data)) {
                //cek sudah absen pada hari itu / belum
                $cek_presensi = $this->mymodel->withquery("select * from presensi_".$jenjang." where tanggal_absen = '".$tanggal_absen."' and hari_absen = '".$hari_absen."' and id_siswa_aktif = '".$list_siswa[$i]."' ","row");
                if (empty($cek_presensi)) {
                    $insert=$this->mymodel->insertid("presensi_".$jenjang,$data);
                    if($insert){
                        $data_presensi_siswa['id'] = $insert;
                        $msg = array('status' => 200, 'message'=>'Berhasil melakukan presensi!', 'data' => $data_presensi_siswa);
                        //cek setting notifikasi presensi ortu
                        $cek_setting = $this->mymodel->withquery("select * from setting_notifikasi_ortu where id_siswa_aktif = '".$get_siswa_aktif->id_siswa_aktif."' and jenjang = '".$jenjang."'","row");
                        //$this->send_notif("Presensi Siswa ".strtoupper($nama_siswa), "Siswa ananda ".$nama_siswa." telah melakukan presensi kehadiran di sekolah pada ".formatTanggal(date("Y-m-d"))." Pukul ".date("H:i")." WIB", $get_siswa_aktif->device_id_siswa, array("id_siswa_aktif" => $list_siswa[$i], "id_presensi" => $insert, "jenjang" => $jenjang) );
                        if (!empty($get_siswa_aktif->device_id_ortu)) {
                            if ($data['status_absen'] == "Hadir") {
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." di sekolah, Terimakasih";
                            }
                            else if($data['status_absen'] == "Terlambat"){
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." datang ke sekolah, Terimakasih";
                            }
                            else if($data['status_absen'] == "Alfa"){
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." , Terimakasih";
                            }
                            else if($data['status_absen'] == "Izin"){
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." tidak masuk sekolah, Terimakasih";
                            }
                            else if($data['status_absen'] == "Sakit"){
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen']).", Terimakasih";
                            }
                            if (!empty($cek_setting) && $cek_setting->notifikasi_presensi == 1) {
                                $this->send_notif("Presensi Siswa ".strtoupper($nama_siswa), $pesan, $get_siswa_aktif->device_id_ortu, array("id_siswa_aktif" => $list_siswa[$i], "id_presensi" => $insert, "jenjang" => $jenjang) );
                            }
                        }
                        if (!empty($cek_setting) && $cek_setting->notifikasi_wa_presensi == 1) {
                            $this->send_wa2($notelp,$nama_siswa, $data['status_absen']);
                        }
                    }else{
                        $msg = array('status' => 401, 'message'=>'Gagal melakukan presensi!', 'data' => $data_presensi_siswa);
                    }
                    array_push($data_presensi_siswa, $data);
                }
                else{
                    //update / edit data presensi siswa
                    $update=$this->mymodel->update("presensi_".$jenjang, $data, "id", $cek_presensi->id);
                    if($update){
                        $data_presensi_siswa['id'] = $cek_presensi->id;
                        $msg = array('status' => 200, 'message'=>'Berhasil mengubah presensi!', 'data' => $data_presensi_siswa);
                        //cek setting notifikasi presensi ortu
                        $cek_setting = $this->mymodel->withquery("select * from setting_notifikasi_ortu where id_siswa_aktif = '".$get_siswa_aktif->id_siswa_aktif."' and jenjang = '".$jenjang."'","row");
                        //$this->send_notif("Presensi Siswa ".strtoupper($nama_siswa), "Siswa ananda ".$nama_siswa." telah melakukan presensi kehadiran di sekolah pada ".formatTanggal(date("Y-m-d"))." Pukul ".date("H:i")." WIB", $get_siswa_aktif->device_id_siswa, array("id_siswa_aktif" => $list_siswa[$i], "id_presensi" => $insert, "jenjang" => $jenjang) );
                        if (!empty($get_siswa_aktif->device_id_ortu)) {
                            if ($data['status_absen'] == "Hadir") {
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." di sekolah, Terimakasih";
                            }
                            else if($data['status_absen'] == "Terlambat"){
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." datang ke sekolah, Terimakasih";
                            }
                            else if($data['status_absen'] == "Alfa"){
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." , Terimakasih";
                            }
                            else if($data['status_absen'] == "Izin"){
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen'])." tidak masuk sekolah, Terimakasih";
                            }
                            else if($data['status_absen'] == "Sakit"){
                                $pesan = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($data['status_absen']).", Terimakasih";
                            }
                            if (!empty($cek_setting) && $cek_setting->notifikasi_presensi == 1) {
                                $this->send_notif("Presensi Siswa ".strtoupper($nama_siswa), $pesan, $get_siswa_aktif->device_id_ortu, array("id_siswa_aktif" => $list_siswa[$i], "id_presensi" => $insert, "jenjang" => $jenjang) );
                            }
                        }
                        if (!empty($cek_setting) && $cek_setting->notifikasi_wa_presensi == 1) {
                            $this->send_wa2($notelp,$nama_siswa, $data['status_absen']);
                        }
                    }else{
                        $msg = array('status' => 401, 'message'=>'Gagal melakukan presensi!', 'data' => $data_presensi_siswa);
                    }
                    array_push($data_presensi_siswa, $data);
                }
            }
        }


        /*$check_if_presented = $this->mymodel->withquery("select * from presensi_".$jenjang." $where $where_tanggalabsen order by id desc","result");

        if ($check_if_presented){
            $msg = array('status' => 401, 'message'=>'Anda sudah melakukan absen untuk hari ini!');
        }else{
            $waktu_batas_absen = $this->mymodel->withquery("select batas_jam from jam_presensi where jenjang = '".$jenjang."'","row")->batas_jam;
            $cek_ssid = $this->mymodel->withquery("select * from pengaturan_whitelist_ssid where nama_ssid = '".$this->post('wifi_ssid')."' or id_pengaturan = '".$this->post('wifi_ssid')."'","row");
            if ($waktu_absen > $waktu_batas_absen){
                $data = array(
                    //"jenjang" => $jenjang,
                    "id_siswa_aktif" => $id_siswa_aktif,
                    "wifi_ssid" => $this->post('wifi_ssid'),//$cek_ssid->id_pengaturan,
                    "wifi_ip" => $this->post('wifi_ip'),
                    "hari_absen" => $this->post('hari_absen'),
                    "waktu_absen" => $waktu_absen,
                    "tanggal_absen" => $tanggal_absen,
                    "status_absen" => 'Terlambat',
                );
            }else{
                $data = array(
                    //"jenjang" => $jenjang,
                    "id_siswa_aktif" => $id_siswa_aktif,
                    "wifi_ssid" => $this->post('wifi_ssid'),//$cek_ssid->id_pengaturan,
                    "wifi_ip" => $this->post('wifi_ip'),
                    "hari_absen" => $this->post('hari_absen'),
                    "waktu_absen" => $waktu_absen,
                    "tanggal_absen" => $tanggal_absen,
                    "status_absen" => 'Hadir',
                );
            }
        }*/
        if (!empty($data_presensi_siswa)) {
            $msg = array('status' => 200, 'message'=>'Berhasil melakukan presensi!', 'data' => $data_presensi_siswa);
        }
        else{
            $msg = array('status' => 200, 'message'=>'Gagal melakukan presensi! (Presensi sudah tercatat)', 'data' => $data_presensi_siswa);
        }
        $this->response($msg);
        // $this->response($tanggal_absen);
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
    if ($status_absen == "Hadir") {
        $message = "Yth. Bapak/Ibu ananda ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah di lakukan presensi ".strtoupper($status_absen)." oleh guru pada pukul ".date("H:i")." WIB. Terimakasih";
    }
    else if($status_absen == "Terlambat"){
        $message = "Yth. Bapak/Ibu ananda ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah di lakukan presensi ".strtoupper($status_absen)." HADIR oleh guru pada pukul ".date("H:i")." WIB. Terimakasih";
    }
    else if($status_absen == "Tidak Hadir"){
        $message = "Yth. Bapak/Ibu ananda ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah di lakukan presensi ".strtoupper($status_absen)." oleh guru pada pukul ".date("H:i")." WIB. Terimakasih";
    }
    else{
        $message = "Yth. Bapak/Ibu ananda ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah di lakukan presensi ".strtoupper($status_absen)." oleh guru pada pukul ".date("H:i")." WIB. Terimakasih";
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
    if ($status_absen == "Hadir") {
        $message = "Yth. Bapak/Ibu ananda ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah di lakukan presensi ".strtoupper($status_absen)." oleh guru pada pukul ".date("H:i")." WIB. Terimakasih";
    }
    else if($status_absen == "Terlambat"){
        $message = "Yth. Bapak/Ibu ananda ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah di lakukan presensi ".strtoupper($status_absen)." HADIR oleh guru pada pukul ".date("H:i")." WIB. Terimakasih";
    }
    else if($status_absen == "Tidak Hadir"){
        $message = "Yth. Bapak/Ibu ananda ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah di lakukan presensi ".strtoupper($status_absen)." oleh guru pada pukul ".date("H:i")." WIB. Terimakasih";
    }
    else{
        $message = "Yth. Bapak/Ibu ananda ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah di lakukan presensi ".strtoupper($status_absen)." oleh guru pada pukul ".date("H:i")." WIB. Terimakasih";
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
