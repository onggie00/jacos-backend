<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
define( 'PRIVATE_FIREBASE_KEY', FCPATH . 'labscib-app-c0ca345e64d9.json');
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cron_spp_tagihan extends MY_Controller {
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
        //jatuh tempo tgl 10, pengingat tgl 1,5 yang tgl 11 ini 
        $tahun_ajaran_aktif = $this->mymodel->withquery("select id_tahun_ajaran from tahun_ajaran where tanggal_mulai <= '".date("Y-m-d")."' and tanggal_selesai >= '".date("Y-m-d")."'","row")->id_tahun_ajaran;
        $tanggal = date("d");
        $bulan = strtolower(formatBulan(date("Y-m-d")));

        if ($tanggal == 1 || $tanggal == 10 || $tanggal == 20) {
            $spp_sd = $this->mymodel->withquery("select sp.*, sa.nama_lengkap, sa.device_id_ortu, s.notelp_ibu, s.notelp_ayah from spp_sd sp 
            join siswa_sd_aktif sa on sp.id_siswa_aktif = sa.id_siswa_sd_aktif join siswa_sd s on sa.id_siswa_sd = s.id_siswa_sd join kelas_sd k on sa.id_kelas = k.id_kelas_sd and k.label not like '%keluar%' and k.label not like '%lulus%' 
            where sp.id_tahun_ajaran = '".$tahun_ajaran_aktif."' and sp.".$bulan." IS NULL order by nama ASC","result");
            $spp_smp = $this->mymodel->withquery("select sp.*, sa.nama_lengkap, sa.device_id_ortu, s.notelp_ibu, s.notelp_ayah from spp_smp sp 
            join siswa_smp_aktif sa on sp.id_siswa_aktif = sa.id_siswa_smp_aktif join siswa_smp s on sa.id_siswa_smp = s.id_siswa_smp join kelas_smp k on sa.id_kelas = k.id_kelas_smp and k.label not like '%keluar%' and k.label not like '%lulus%' 
            where sp.id_tahun_ajaran = '".$tahun_ajaran_aktif."' and sp.".$bulan." IS NULL order by nama ASC","result");
            $spp_sma = $this->mymodel->withquery("select sp.*, sa.nama_lengkap, sa.device_id_ortu, s.notelp_ibu, s.notelp_ayah from spp_sma sp 
            join siswa_sma_aktif sa on sp.id_siswa_aktif = sa.id_siswa_sma_aktif join siswa_sma s on sa.id_siswa_sma = s.id_siswa_sma join kelas_sma k on sa.id_kelas = k.id_kelas_sma and k.label not like '%keluar%' and k.label not like '%lulus%' 
            where sp.id_tahun_ajaran = '".$tahun_ajaran_aktif."' and sp.".$bulan." IS NULL order by nama ASC","result");
            $spp_ft = $this->mymodel->withquery("select sp.*, sa.nama_lengkap, sa.device_id_ortu, s.notelp_ibu, s.notelp_ayah from spp_ft sp 
            join siswa_ft_aktif sa on sp.id_siswa_aktif = sa.id_siswa_ft_aktif join siswa_ft s on sa.id_siswa_ft = s.id_siswa_ft join kelas_ft k on sa.id_kelas = k.id_kelas_ft and k.label not like '%keluar%' and k.label not like '%lulus%' 
            where sp.id_tahun_ajaran = '".$tahun_ajaran_aktif."' and sp.".$bulan." IS NULL order by nama ASC","result");
            
            if (!empty($spp_sd)) {
                foreach ($spp_sd as $key => $value) {
                    $notelp = "";
                    $nama_siswa = $value->nama_lengkap;
                    if (!empty($value->notelp_ibu)) {
                        $notelp = $value->notelp_ibu;
                    }
                    else if(!empty($value->notelp_ayah)){
                        $notelp = $value->notelp_ayah;
                    }
                    $notelp = $this->cek_notelp($notelp);
                    if ($tanggal == 1 || $tanggal == 10 || $tanggal == 3) {
                        // $this->send_wa2($notelp, "Pembayaran SPP siswa atas nama ".$nama_siswa." jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485");
                        $pesan = "Pembayaran SPP siswa atas nama ".$nama_siswa." jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485";
                        $this->send_wa_omni($notelp,  $pesan);
                    }
                    else if($tanggal == 20){
                        // $this->send_wa2($notelp, "Pembayaran SPP siswa atas nama ".$nama_siswa." telah jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485");
                        $pesan = "Pembayaran SPP siswa atas nama ".$nama_siswa." telah jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485";
                        $this->send_wa_omni($notelp,  $pesan);
                    }
                }
            }

            if (!empty($spp_smp)) {
                foreach ($spp_smp as $key => $value) {
                    $notelp = "";
                    $nama_siswa = $value->nama_lengkap;
                    if (!empty($value->notelp_ibu)) {
                        $notelp = $value->notelp_ibu;
                    }
                    else if(!empty($value->notelp_ayah)){
                        $notelp = $value->notelp_ayah;
                    }
                    $notelp = $this->cek_notelp($notelp);
                    if ($tanggal == 1 || $tanggal == 10) {
                        // $this->send_wa2($notelp, "Pembayaran SPP siswa atas nama ".$nama_siswa." jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485");
                        $pesan = "Pembayaran SPP siswa atas nama ".$nama_siswa." jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485";
                        $this->send_wa_omni($notelp,  $pesan);
                    }
                    else if($tanggal == 20){
                        // $this->send_wa2($notelp, "Pembayaran SPP siswa atas nama ".$nama_siswa." telah jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485");
                        $pesan = "Pembayaran SPP siswa atas nama ".$nama_siswa." telah jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485";
                        $this->send_wa_omni($notelp,  $pesan);
                    }
                }
            }

            if (!empty($spp_sma)) {
                foreach ($spp_sma as $key => $value) {
                    $notelp = "";
                    $nama_siswa = $value->nama_lengkap;
                    if (!empty($value->notelp_ibu)) {
                        $notelp = $value->notelp_ibu;
                    }
                    else if(!empty($value->notelp_ayah)){
                        $notelp = $value->notelp_ayah;
                    }
                    $notelp = $this->cek_notelp($notelp);
                    if ($tanggal == 1 || $tanggal == 10) {
                        // $this->send_wa2($notelp, "Pembayaran SPP siswa atas nama ".$nama_siswa." jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485");
                        $pesan = "Pembayaran SPP siswa atas nama ".$nama_siswa." jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485";
                        $this->send_wa_omni($notelp,  $pesan);
                    }
                    else if($tanggal == 20){
                        // $this->send_wa2($notelp, "Pembayaran SPP siswa atas nama ".$nama_siswa." telah jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485");
                        $pesan = "Pembayaran SPP siswa atas nama ".$nama_siswa." telah jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485";
                        $this->send_wa_omni($notelp,  $pesan);
                    }
                }
            }

            if (!empty($spp_ft)) {
                foreach ($spp_ft as $key => $value) {
                    $notelp = "";
                    $nama_siswa = $value->nama_lengkap;
                    if (!empty($value->notelp_ibu)) {
                        $notelp = $value->notelp_ibu;
                    }
                    else if(!empty($value->notelp_ayah)){
                        $notelp = $value->notelp_ayah;
                    }
                    $notelp = $this->cek_notelp($notelp);
                    if ($tanggal == 1 || $tanggal == 10) {
                        // $this->send_wa2($notelp, "Pembayaran SPP siswa atas nama ".$nama_siswa." jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485");
                        $pesan = "Pembayaran SPP siswa atas nama ".$nama_siswa." jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485";
                        $this->send_wa_omni($notelp,  $pesan);
                    }
                    else if($tanggal == 20){
                        // $this->send_wa2($notelp, "Pembayaran SPP siswa atas nama ".$nama_siswa." telah jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485");
                        $pesan = "Pembayaran SPP siswa atas nama ".$nama_siswa." telah jatuh tempo pada tanggal 10 ".ucfirst($bulan)." ".date("Y").", untuk melakukan pembayaran SPP melalui Labscib App. Terima kasih (ini adalah pesan otomatis, mohon untuk tidak membalas pesan ini). Jika ada kendala dapat menghubungi no whatsapp helpdesk : SD : Eka - 08119326082 , SMP : Richi - 081584655084 , SMA : Gerry - 085693174485";
                        $this->send_wa_omni($notelp,  $pesan);
                    }
                }
            }
        }

        echo "Cron Selesai ";
    }

    public function send_wa($notelp, $keterangan){
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
        if (!empty($keterangan)) {
            $message = $keterangan;
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

    public function send_wa2($notelp, $keterangan){
        if (!empty($keterangan)) {
            $message = $keterangan;
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
          /* echo "<br/>".json_encode(array(
            'appkey' => akunSetting('wapanel_appkey'),
            'authkey' =>  akunSetting('wapanel_authkey'),
            'to' => $no_hp,
            'message' => $pesan,
            'sandbox' => 'false'
            ))."<br/>".$response; */
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
                $hp    ="62".substr(trim($nohp), 0);
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
