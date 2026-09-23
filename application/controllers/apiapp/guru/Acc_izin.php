<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Acc_izin extends REST_Controller {
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

        $id=$this->post('id');
        // $id_siswa_aktif = $get_by_id->id_siswa_aktif;
        // $tanggal_mulai = $this->post('tanggal_mulai');
        // $tanggal_selesai = $this->post('tanggal_selesai');
        $id_guru = $this->post('id_guru');
        $konfirmasi = $this->post('konfirmasi');
        $jenjang = $this->post("jenjang");


        $get_by_id = $this->mymodel->withquery("select * from izin_siswa_".$jenjang." where id = '$id'","row");
        $get_siswa = $this->mymodel->withquery("select nama_lengkap, device_id_siswa, device_id_ortu, id_siswa_".$jenjang." from siswa_".$jenjang."_aktif where id_siswa_".$jenjang."_aktif = '".$get_by_id->id_siswa_aktif."' " ,"row");
        $nama_kolom = "id_siswa_".$jenjang;
        //cek setting notifikasi Izin / Sakit ortu
        $cek_setting = $this->mymodel->withquery("select * from setting_notifikasi_ortu where id_siswa_aktif = '".$get_by_id->id_siswa_aktif."' and jenjang = '".$jenjang."'","row");
        $get_data_siswa = $this->mymodel->withquery("select notelp_ibu, notelp_ayah, nama_lengkap from siswa_".$jenjang." where id_siswa_".$jenjang." =".$get_siswa->$nama_kolom,"row");
            if (!empty($get_data_siswa->notelp_ibu)) {
                $notelp = $get_data_siswa->notelp_ibu;
            }
            else if(!empty($get_data_siswa->notelp_ayah)){
                $notelp = $get_data_siswa->notelp_ayah;
            }
            $notelp = $this->cek_notelp($notelp);
        if($konfirmasi == "terima"){
            $update=$this->mymodel->update("izin_siswa_".$jenjang,array("status" => "diterima", "id_approver" => $id_guru, "update_at" => date("Y-m-d H:i:s")),"id", $id);
            if($update){
                $resp = array(
                    'id' => $id, 
                    'status' => "diterima"
                );
                $msg = array('status' => 200, 'message'=>'Berhasil melakukan acc izin!', 'data' => $resp);


                $current_date = strtotime($get_by_id->tanggal_mulai);
                $end_date = strtotime($get_by_id->tanggal_selesai);

                $date_array = array();

                while ($current_date <= $end_date) {
                    $day_name = strftime('%A', $current_date);

                    switch($day_name){
                        case 'Sunday':
                            $hari_ini = "Minggu";
                        break;

                        case 'Monday':			
                            $hari_ini = "Senin";
                        break;

                        case 'Tuesday':
                            $hari_ini = "Selasa";
                        break;

                        case 'Wednesday':
                            $hari_ini = "Rabu";
                        break;

                        case 'Thursday':
                            $hari_ini = "Kamis";
                        break;

                        case 'Friday':
                            $hari_ini = "Jumat";
                        break;

                        case 'Saturday':
                            $hari_ini = "Sabtu";
                        break;
                        
                        default:
                            $hari_ini = "Tidak di ketahui";		
                        break;
                    }

                    $date_to_save = date('Y-m-d', $current_date);
                    $data2 = array(
                        //"jenjang" => $jenjang,
                        "id_siswa_aktif" => $get_by_id->id_siswa_aktif,
                        "wifi_ssid" => null,
                        "wifi_ip" => null,
                        "hari_absen" => $hari_ini, //$this->post('hari_absen'),
                        "waktu_absen" => null,
                        "tanggal_absen" => $date_to_save,
                        "status_absen" => ucfirst(strtolower($get_by_id->jenis_izin)),
                        "id_izin" => $id,
                        "updated_at" => date("Y-m-d H:i:s")
                    );
                    if (!empty($data2)) {
                        //cek apakah sudah terdaftar presensi nya
                        $cek_presensi = $this->mymodel->withquery("select id from presensi_".$jenjang." where id_siswa_aktif = '".$get_by_id->id_siswa_aktif."' and tanggal_absen = '".$date_to_save."'", "row");
                        if (empty($cek_presensi)) {
                            $insert2=$this->mymodel->insertid("presensi_".$jenjang, $data2);
                        }
                        else{
                            $insert2 = $this->mymodel->update("presensi_".$jenjang, $data2, "id", $cek_presensi->id);
                        }
                        //echo $this->db->last_query();
                        if (!empty($cek_setting) && $cek_setting->notifikasi_wa_presensi == 1) {
                            $this->send_wa2($notelp,$get_siswa->nama_lengkap, $data2['status_absen'], array("tanggal_mulai" => $get_by_id->tanggal_mulai, "tanggal_selesai" => $get_by_id->tanggal_selesai));
                        }
                        $date_array[] = $data2;
                        if($insert2){
                            $msg2 = array('status' => 200, 'message'=>'Berhasil melakukan absen otomatis!', 'data_absensi' =>  $date_array);
                        }else{
                            $msg2 = array('status' => 401, 'message'=>'Gagal melakukan absen otomatis!', 'data_absensi' =>  $date_array);
                        }
                    }
                    // $date_array[] = $data2; //$date_to_save;
                    $current_date = strtotime('+1 day', $current_date);
                }
                if (!empty($get_siswa->device_id_ortu)) {
                    if (!empty($cek_setting) && $cek_setting->notifikasi_izin == 1) {
                        $this->send_notif("Pengajuan izin telah diterima", "Pengajuan izin siswa ".strtoupper($get_siswa->nama_lengkap)." diterima", $value->device_id_ortu, array("id_izin" => $id, "jenjang" => $jenjang) );
                    }
                    else if(empty($cek_setting)){
                        $this->send_notif("Pengajuan izin telah diterima", "Pengajuan izin siswa ".strtoupper($get_siswa->nama_lengkap)." diterima", $value->device_id_ortu, array("id_izin" => $id, "jenjang" => $jenjang) );
                    }
                }

            }else{
                $msg = array('status' => 401, 'message'=>'Gagal melakukan acc izin!', 'data' => $insert);
            }
        }else if($konfirmasi == "tolak"){
            $insert=$this->mymodel->update("izin_siswa_".$jenjang, array("status" => "ditolak", "id_approver" => $id_guru, "update_at" => date("Y-m-d H:i:s")),"id", $id);
            if($insert){
                $resp = array(
                    'id' => $id, 
                    'status' => "ditolak"
                );
                $msg = array('status' => 200, 'message'=>'Berhasil melakukan tolak izin!', 'data' => $resp);

                $current_date = strtotime($get_by_id->tanggal_mulai);
                $end_date = strtotime($get_by_id->tanggal_selesai);

                $date_array = array();

                while ($current_date <= $end_date) {
                    $day_name = strftime('%A', $current_date);

                    switch($day_name){
                        case 'Sunday':
                            $hari_ini = "Minggu";
                        break;

                        case 'Monday':			
                            $hari_ini = "Senin";
                        break;

                        case 'Tuesday':
                            $hari_ini = "Selasa";
                        break;

                        case 'Wednesday':
                            $hari_ini = "Rabu";
                        break;

                        case 'Thursday':
                            $hari_ini = "Kamis";
                        break;

                        case 'Friday':
                            $hari_ini = "Jumat";
                        break;

                        case 'Saturday':
                            $hari_ini = "Sabtu";
                        break;
                        
                        default:
                            $hari_ini = "Tidak di ketahui";		
                        break;
                    }

                    $date_to_save = date('Y-m-d', $current_date);
                    // $date_array[] = $data2; //$date_to_save;
                    $current_date = strtotime('+1 day', $current_date);
                }
                if (!empty($get_siswa->device_id_ortu)) {
                    if (!empty($cek_setting) && $cek_setting->notifikasi_izin == 1) {
                        $this->send_notif("Pengajuan izin telah ditolak", "Pengajuan izin siswa ".strtoupper($get_siswa->nama_lengkap)." ditolak", $value->device_id_ortu, array("id_izin" => $id, "jenjang" => $jenjang) );
                    }
                    else if(empty($cek_setting)){
                        $this->send_notif("Pengajuan izin telah ditolak", "Pengajuan izin siswa ".strtoupper($get_siswa->nama_lengkap)." ditolak", $value->device_id_ortu, array("id_izin" => $id, "jenjang" => $jenjang) );
                    }
                }

            }else{
                $msg = array('status' => 401, 'message'=>'Gagal melakukan tolak izin!', 'data' => $insert);
            }
        }

        // $get_by_id = $this->mymodel->withquery("select * from izin_siswa where id = '$id'","row");

        // $current_date = strtotime($get_by_id->tanggal_mulai);
        // $end_date = strtotime($get_by_id->tanggal_selesai);

        // $this->response($current_date);
        // $this->response($msg);
        $this->response(array('status' => 200, 'data'=>$msg, 'data_absensi' => $msg2));
    }

    public function send_wa($notelp, $nama_siswa, $status_absen, $tanggal){
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
    if ($status_absen == "Izin") {
        $message = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d", strtotime($tanggal['tanggal_mulai'])))." ".strtoupper($status_absen)." tidak masuk sekolah, Terimakasih";
    }
    else if($status_absen == "Sakit"){
        $message = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d", strtotime($tanggal['tanggal_mulai'])))." Izin ".strtoupper($status_absen)." tidak masuk sekolah, Terimakasih";
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

  public function send_wa2($notelp, $nama_siswa, $status_absen, $tanggal){    
    $url_send = "https://apiks.ristekmuslim.com/client/v1/message/send-text";
    if ($status_absen == "Izin") {
        $message = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d", strtotime($tanggal['tanggal_mulai'])))." ".strtoupper($status_absen)." tidak masuk sekolah, Terimakasih";
    }
    else if($status_absen == "Sakit"){
        $message = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d", strtotime($tanggal['tanggal_mulai'])))." Izin ".strtoupper($status_absen)." tidak masuk sekolah, Terimakasih";
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
        }
        return $hp;
    }

    public function send_notif($title,$desc,$fcm_id,$data){
        //$firebaseService = new FirebaseService();
        $id_izin = $data['id_izin'];
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
                    'id_izin' => $id_izin,
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
