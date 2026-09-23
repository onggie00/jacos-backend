<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cron_presensi_siswa extends MY_Controller {
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
        
        $tahun_ajaran_aktif = $this->mymodel->withquery("select id_tahun_ajaran from tahun_ajaran where tanggal_mulai <= '".$today."' and tanggal_selesai >= '".$today."'","row")->id_tahun_ajaran;

        $where_cron_setting = "";
        //cek cron setting
        $where_cs_smp = "and cs.nama_cron = 'cron_presensi_siswa' and cs.is_active = '1' and (cs.id_siswa_aktif = sa.id_siswa_smp_aktif)";
        $where_cs_sma = "and cs.nama_cron = 'cron_presensi_siswa' and cs.is_active = '1' and (cs.id_siswa_aktif = sa.id_siswa_sma_aktif)";
        $where_cs_ft = "and cs.nama_cron = 'cron_presensi_siswa' and cs.is_active = '1' and (cs.id_siswa_aktif = sa.id_siswa_ft_aktif)";

        //$get_siswa_sd = $this->mymodel->withquery("select sa.id_siswa_sd_aktif as id_siswa_aktif, sa.nama_lengkap, sa.device_id_siswa, sa.device_id_ortu, s.notelp_ibu, s.notelp_ayah from siswa_sd_aktif sa join siswa_sd s on sa.id_siswa_sd = s.id_siswa_sd and s.deleted_at is null join kelas_sd k on sa.id_kelas = k.id_kelas_sd left join cron_setting cs on sa.id_siswa_sd_aktif = cs.id_siswa_aktif where sa.id_tahun_ajaran = '".$tahun_ajaran_aktif."' and (k.label not like '%alumni%' or k.label not like '%mutasi%') and sa.id_siswa_sd_aktif not in (select id_siswa_aktif from presensi_sd where tanggal_absen = '".$today."') order by nama_lengkap ASC", "result");
        $get_siswa_smp = $this->mymodel->withquery("select sa.id_siswa_smp_aktif as id_siswa_aktif, sa.nama_lengkap, sa.device_id_siswa, sa.device_id_ortu, s.notelp_ibu, s.notelp_ayah from siswa_smp_aktif sa join siswa_smp s on sa.id_siswa_smp = s.id_siswa_smp and s.deleted_at is null join kelas_smp k on sa.id_kelas = k.id_kelas_smp left join cron_setting cs on sa.id_siswa_smp_aktif = cs.id_siswa_aktif where sa.id_tahun_ajaran = '".$tahun_ajaran_aktif."' $where_cs_smp and (k.label not like '%alumni%' and k.label not like '%mutasi%') and sa.id_siswa_smp_aktif not in (select id_siswa_aktif from presensi_smp where tanggal_absen = '".$today."') order by nama_lengkap ASC", "result");
        $get_siswa_sma = $this->mymodel->withquery("select sa.id_siswa_sma_aktif as id_siswa_aktif, sa.nama_lengkap, sa.device_id_siswa, sa.device_id_ortu, s.notelp_ibu, s.notelp_ayah from siswa_sma_aktif sa join siswa_sma s on sa.id_siswa_sma = s.id_siswa_sma and s.deleted_at is null join kelas_sma k on sa.id_kelas = k.id_kelas_sma left join cron_setting cs on sa.id_siswa_sma_aktif = cs.id_siswa_aktif where sa.id_tahun_ajaran = '".$tahun_ajaran_aktif."' $where_cs_sma and (k.label not like '%alumni%' and k.label not like '%mutasi%' and k.label not like '%XII%') and sa.id_siswa_sma_aktif not in (select id_siswa_aktif from presensi_sma where tanggal_absen = '".$today."') order by nama_lengkap ASC", "result");
        $get_siswa_ft = $this->mymodel->withquery("select sa.id_siswa_ft_aktif as id_siswa_aktif, sa.nama_lengkap, sa.device_id_siswa, sa.device_id_ortu, s.notelp_ibu, s.notelp_ayah from siswa_ft_aktif sa join siswa_ft s on sa.id_siswa_ft = s.id_siswa_ft and s.deleted_at is null join kelas_ft k on sa.id_kelas = k.id_kelas_ft left join cron_setting cs on sa.id_siswa_ft_aktif = cs.id_siswa_aktif where sa.id_tahun_ajaran = '".$tahun_ajaran_aktif."' $where_cs_ft and (k.label not like '%alumni%' and k.label not like '%mutasi%' and k.label not like '%XII%') and sa.id_siswa_ft_aktif not in (select id_siswa_aktif from presensi_ft where tanggal_absen = '".$today."') order by nama_lengkap ASC", "result");

        /*select sa.id_siswa_smp_aktif as id_siswa_aktif, sa.nama_lengkap, sa.device_id_siswa, sa.device_id_ortu, s.notelp_ibu, s.notelp_ayah from siswa_smp_aktif sa join siswa_smp s on sa.id_siswa_smp = s.id_siswa_smp and s.deleted_at is null join kelas_smp k on sa.id_kelas = k.id_kelas_smp left join cron_setting cs on sa.id_siswa_smp_aktif = cs.id_siswa_aktif where sa.id_tahun_ajaran = '6' and cs.nama_cron = 'cron_presensi_siswa' and cs.is_active = '1' and (cs.id_siswa_aktif = sa.id_siswa_smp_aktif) and (k.label not like '%alumni%' and k.label not like '%mutasi%') and sa.id_siswa_smp_aktif not in (select id_siswa_aktif from presensi_smp where tanggal_absen = '2025-04-21') order by nama_lengkap ASC */

        //cek weekend
        $weekday = date('N');

        //cek kalender akademik
        $cek_akademik = $this->mymodel->withquery("select k.*,ta.label as label_tahun_ajaran,t.nama_tipe, t.warna_hexcode from kalender_akademik k join tahun_ajaran ta on ta.id_tahun_ajaran = k.id_tahun_ajaran join tipe_kalender t on t.id_tipe = k.id_tipe where t.nama_tipe = 'Libur' and k.date = '".date("Y-m-d")."'","row");

        $data_alpha = array(
            "wifi_ssid" => null,
            "wifi_ip" => null,
            "hari_absen" => formatHari($today),
            "waktu_absen" => null,
            "tanggal_absen" => $today,
            "status_absen" => 'Tidak Hadir',
        );


        /*if (!empty($get_siswa_sd) && $weekday < 6 && empty($cek_akademik)) {
            //echo "<br/><br/>sd<br/>";
            foreach ($get_siswa_sd as $key => $value) {
                //insert ke presensi alpha
                $data_alpha['id_siswa_aktif'] = $value->id_siswa_sd_aktif;
                //$insert = $this->mymodel->insertid("presensi_sd", $data_alpha);
                $notelp = "";
                $nama_siswa = $value->nama_lengkap;
                if (!empty($value->notelp_ibu)) {
                    $notelp = $value->notelp_ibu;
                }
                else if(!empty($value->notelp_ayah)){
                    $notelp = $value->notelp_ayah;
                }
                $notelp = $this->cek_notelp($notelp);
                $this->send_wa2($notelp,$nama_siswa, "Tidak Hadir");

                if (!empty($value->device_id_ortu)) {
                    $this->send_notif("Presensi Siswa ".strtoupper($nama_siswa), "Siswa ".$nama_siswa." tidak melakukan presensi kehadiran di sekolah pada ".formatTanggal(date("Y-m-d"))." ", $get_siswa_aktif->device_id_ortu, array("id_siswa_aktif" => $id_siswa_aktif, "id_presensi" => null, "jenjang" => "sd") );
                }
            }
        }*/

        if (!empty($get_siswa_smp && $weekday < 6 && empty($cek_akademik))) {
            //echo "<br/><br/>smp<br/>";
            foreach ($get_siswa_smp as $key => $value) {
                //insert ke presensi alpha
                $data_alpha['id_siswa_aktif'] = $value->id_siswa_smp_aktif;
                //$insert = $this->mymodel->insertid("presensi_smp", $data_alpha);
                $notelp = "";
                $nama_siswa = $value->nama_lengkap;
                if (!empty($value->notelp_ibu)) {
                    $notelp = $value->notelp_ibu;
                }
                else if(!empty($value->notelp_ayah)){
                    $notelp = $value->notelp_ayah;
                }
                $notelp = $this->cek_notelp($notelp);
                $this->send_wa2($notelp,$nama_siswa, "Tidak Hadir");

                if (!empty($value->device_id_ortu)) {
                    $this->send_notif("Presensi Siswa ".strtoupper($nama_siswa), "Siswa ".$nama_siswa." tidak melakukan presensi kehadiran di sekolah pada ".formatTanggal(date("Y-m-d"))." ", $get_siswa_aktif->device_id_ortu, array("id_siswa_aktif" => $id_siswa_aktif, "id_presensi" => null, "jenjang" => "smp") );
                }
            }
        }

        if (!empty($get_siswa_sma && $weekday < 6 && empty($cek_akademik))) {
            //echo "<br/><br/>sma<br/>";
            foreach ($get_siswa_sma as $key => $value) {
                //insert ke presensi alpha
                $data_alpha['id_siswa_aktif'] = $value->id_siswa_sma_aktif;
                //$insert = $this->mymodel->insertid("presensi_sma", $data_alpha);
                $notelp = "";
                $nama_siswa = $value->nama_lengkap;
                if (!empty($value->notelp_ibu)) {
                    $notelp = $value->notelp_ibu;
                }
                else if(!empty($value->notelp_ayah)){
                    $notelp = $value->notelp_ayah;
                }
                $notelp = $this->cek_notelp($notelp);
                $this->send_wa2($notelp,$nama_siswa, "Tidak Hadir");

                if (!empty($value->device_id_ortu)) {
                    $this->send_notif("Presensi Siswa ".strtoupper($nama_siswa), "Siswa ".$nama_siswa." tidak melakukan presensi kehadiran di sekolah pada ".formatTanggal(date("Y-m-d"))." ", $get_siswa_aktif->device_id_ortu, array("id_siswa_aktif" => $id_siswa_aktif, "id_presensi" => null, "jenjang" => "sma") );
                }
            }
        }

        if (!empty($get_siswa_ft) && $weekday < 6 && empty($cek_akademik)) {
            //echo "<br/><br/>ft<br/>";
            foreach ($get_siswa_ft as $key => $value) {
                //insert ke presensi alpha
                $data_alpha['id_siswa_aktif'] = $value->id_siswa_ft_aktif;
                //$insert = $this->mymodel->insertid("presensi_ft", $data_alpha);
                $notelp = "";
                $nama_siswa = $value->nama_lengkap;
                if (!empty($value->notelp_ibu)) {
                    $notelp = $value->notelp_ibu;
                }
                else if(!empty($value->notelp_ayah)){
                    $notelp = $value->notelp_ayah;
                }
                $notelp = $this->cek_notelp($notelp);
                $this->send_wa2($notelp,$nama_siswa, "Tidak Hadir");

                if (!empty($value->device_id_ortu)) {
                    $this->send_notif("Presensi Siswa ".strtoupper($nama_siswa), "Siswa ".$nama_siswa." tidak melakukan presensi kehadiran di sekolah pada ".formatTanggal(date("Y-m-d"))." ", $get_siswa_aktif->device_id_ortu, array("id_siswa_aktif" => $id_siswa_aktif, "id_presensi" => null, "jenjang" => "ft") );
                }
            }
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
 		else if(substr(trim($nohp), 0, 1)=="8"){
            $hp    ="62".substr(trim($nohp), 0);
            }

    }
    return $hp;
  }
}
