<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cron_jadwal_ujian extends MY_Controller {
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

        $today = date("Y-m-d", strtotime("+1 day"));
        $get_siswa_sd = $this->mymodel->withquery("select d.*, k.kelas, k.id_tingkatan, k.id_ruang_kelas, s.nis, s.nama_lengkap, s.device_id_siswa from ujian_ruang_detail d left join ujian_ruang_kelas k on d.id_ruang_kelas = k.id_ruang_kelas left join siswa_sd_aktif s on d.nomor_peserta_ujian = s.nomor_peserta_ujian where d.nomor_peserta_ujian != '' order by d.urutan_kursi ASC","result");
        $get_siswa_smp = $this->mymodel->withquery("select d.*, k.kelas, k.id_tingkatan, k.id_ruang_kelas, s.nis, s.nama_lengkap, s.device_id_siswa from ujian_ruang_detail d left join ujian_ruang_kelas k on d.id_ruang_kelas = k.id_ruang_kelas left join siswa_smp_aktif s on d.nomor_peserta_ujian = s.nomor_peserta_ujian where d.nomor_peserta_ujian != '' order by d.urutan_kursi ASC","result");
        $get_siswa_sma = $this->mymodel->withquery("select d.*, k.kelas, k.id_tingkatan, k.id_ruang_kelas, s.nis, s.nama_lengkap, s.device_id_siswa from ujian_ruang_detail d left join ujian_ruang_kelas k on d.id_ruang_kelas = k.id_ruang_kelas left join siswa_sma_aktif s on d.nomor_peserta_ujian = s.nomor_peserta_ujian where d.nomor_peserta_ujian != '' order by d.urutan_kursi ASC","result");
        $get_siswa_ft = $this->mymodel->withquery("select d.*, k.kelas, k.id_tingkatan, k.id_ruang_kelas, s.nis, s.nama_lengkap, s.device_id_siswa from ujian_ruang_detail d left join ujian_ruang_kelas k on d.id_ruang_kelas = k.id_ruang_kelas left join siswa_ft_aktif s on d.nomor_peserta_ujian = s.nomor_peserta_ujian where d.nomor_peserta_ujian != '' order by d.urutan_kursi ASC","result");

        if (!empty($get_siswa_sd)) {
            $get_ujian_sd = $this->mymodel->withquery("select j.id_jadwal, j.id_jenis_ujian, j.tanggal, j.hari, j.jam_mulai, j.jam_selesai, j.id_tahun_ajaran, m.nama_mapel, t.label as tahun_ajaran from jadwal_ujian_sd j join ujian_mapel_sd m on j.id_mapel = m.id_mapel join tahun_ajaran t on j.id_tahun_ajaran = t.id_tahun_ajaran where j.tanggal = '".$today."' and j.id_tingkatan = '".$get_siswa_sd[0]->id_tingkatan."' order by tanggal ASC, jam_mulai ASC","result");
            $arr_ujian = array();
            $list_ujian = "";
            foreach ($get_ujian_sd as $key => $value) {
                $value->tanggal = formatTanggal($value->tanggal);
                $value->jam_mulai = date("H:i", strtotime($value->jam_mulai));
                $value->jam_selesai = date("H:i", strtotime($value->jam_selesai));
                $value->tahun_ajaran = str_replace("/", "-", $value->tahun_ajaran);
            }
            $arr_ujian = array_values($arr_ujian);
            foreach ($get_siswa_sd as $key => $value) {
                $judul_ujian = $this->mymodel->withquery("select ju.nama_ujian from jenis_ujian ju join jadwal_ujian_sd j on ju.id_jenis_ujian = j.id_jenis_ujian join ujian_ruang_kelas k on j.id_tingkatan = k.id_tingkatan where k.id_tingkatan = '".$value->id_tingkatan."'","row");
                if (!empty($judul_ujian)) {
                    $value->judul_ujian = $judul_ujian->nama_ujian;
                    if ($list_ujian == "") {
                        $list_ujian .= $value->nama_ujian;
                    }
                    else{
                        $list_ujian .= ", ".$value->nama_ujian;
                    }
                }
                else{
                    $value->judul_ujian = "";
                    $list_ujian .= "";
                }
                $this->send_notif("Jangan lupa besok ada ujian", "Halo ".$value->nama_lengkap.", besok ada ujian ".$list_ujian.". Jangan lupa belajar ya", $value->device_id_siswa, array() );
            }
        }

        if (!empty($get_siswa_smp)) {
            $get_ujian_smp = $this->mymodel->withquery("select j.id_jadwal, j.id_jenis_ujian, j.tanggal, j.hari, j.jam_mulai, j.jam_selesai, j.id_tahun_ajaran, m.nama_mapel, t.label as tahun_ajaran from jadwal_ujian_smp j join ujian_mapel_smp m on j.id_mapel = m.id_mapel join tahun_ajaran t on j.id_tahun_ajaran = t.id_tahun_ajaran where j.tanggal = '".$today."' and j.id_tingkatan = '".$get_siswa_smp[0]->id_tingkatan."' order by tanggal ASC, jam_mulai ASC","result");
            $arr_ujian = array();
            $list_ujian = "";
            foreach ($get_ujian_smp as $key => $value) {
                $value->tanggal = formatTanggal($value->tanggal);
                $value->jam_mulai = date("H:i", strtotime($value->jam_mulai));
                $value->jam_selesai = date("H:i", strtotime($value->jam_selesai));
                $value->tahun_ajaran = str_replace("/", "-", $value->tahun_ajaran);
            }
            $arr_ujian = array_values($arr_ujian);
            foreach ($get_siswa_smp as $key => $value) {
                $judul_ujian = $this->mymodel->withquery("select ju.nama_ujian from jenis_ujian ju join jadwal_ujian_smp j on ju.id_jenis_ujian = j.id_jenis_ujian join ujian_ruang_kelas k on j.id_tingkatan = k.id_tingkatan where k.id_tingkatan = '".$value->id_tingkatan."'","row");
                if (!empty($judul_ujian)) {
                    $value->judul_ujian = $judul_ujian->nama_ujian;
                    if ($list_ujian == "") {
                        $list_ujian .= $value->nama_ujian;
                    }
                    else{
                        $list_ujian .= ", ".$value->nama_ujian;
                    }
                }
                else{
                    $value->judul_ujian = "";
                    $list_ujian .= "";
                }
                $this->send_notif("Jangan lupa besok ada ujian", "Halo ".$value->nama_lengkap.", besok ada ujian ".$list_ujian.". Jangan lupa belajar ya", $value->device_id_siswa, array() );
            }
        }

        if (!empty($get_siswa_sma)) {
            $get_ujian_sma = $this->mymodel->withquery("select j.id_jadwal, j.id_jenis_ujian, j.tanggal, j.hari, j.jam_mulai, j.jam_selesai, j.id_tahun_ajaran, m.nama_mapel, t.label as tahun_ajaran from jadwal_ujian_sma j join ujian_mapel_sma m on j.id_mapel = m.id_mapel join tahun_ajaran t on j.id_tahun_ajaran = t.id_tahun_ajaran where j.tanggal = '".$today."' and j.id_tingkatan = '".$get_siswa_sma[0]->id_tingkatan."' order by tanggal ASC, jam_mulai ASC","result");
            $arr_ujian = array();
            $list_ujian = "";
            foreach ($get_ujian_sma as $key => $value) {
                $value->tanggal = formatTanggal($value->tanggal);
                $value->jam_mulai = date("H:i", strtotime($value->jam_mulai));
                $value->jam_selesai = date("H:i", strtotime($value->jam_selesai));
                $value->tahun_ajaran = str_replace("/", "-", $value->tahun_ajaran);
            }
            $arr_ujian = array_values($arr_ujian);
            foreach ($get_siswa_sma as $key => $value) {
                $judul_ujian = $this->mymodel->withquery("select ju.nama_ujian from jenis_ujian ju join jadwal_ujian_sma j on ju.id_jenis_ujian = j.id_jenis_ujian join ujian_ruang_kelas k on j.id_tingkatan = k.id_tingkatan where k.id_tingkatan = '".$value->id_tingkatan."'","row");
                if (!empty($judul_ujian)) {
                    $value->judul_ujian = $judul_ujian->nama_ujian;
                    if ($list_ujian == "") {
                        $list_ujian .= $value->nama_ujian;
                    }
                    else{
                        $list_ujian .= ", ".$value->nama_ujian;
                    }
                }
                else{
                    $value->judul_ujian = "";
                    $list_ujian .= "";
                }
                $this->send_notif("Jangan lupa besok ada ujian", "Halo ".$value->nama_lengkap.", besok ada ujian ".$list_ujian.". Jangan lupa belajar ya", $value->device_id_siswa, array() );
            }
        }

        if (!empty($get_siswa_ft)) {
            $get_ujian_ft = $this->mymodel->withquery("select j.id_jadwal, j.id_jenis_ujian, j.tanggal, j.hari, j.jam_mulai, j.jam_selesai, j.id_tahun_ajaran, m.nama_mapel, t.label as tahun_ajaran from jadwal_ujian_ft j join ujian_mapel_ft m on j.id_mapel = m.id_mapel join tahun_ajaran t on j.id_tahun_ajaran = t.id_tahun_ajaran where j.tanggal = '".$today."' and j.id_tingkatan = '".$get_siswa_ft[0]->id_tingkatan."' order by tanggal ASC, jam_mulai ASC","result");
            $arr_ujian = array();
            $list_ujian = "";
            foreach ($get_ujian_ft as $key => $value) {
                $value->tanggal = formatTanggal($value->tanggal);
                $value->jam_mulai = date("H:i", strtotime($value->jam_mulai));
                $value->jam_selesai = date("H:i", strtotime($value->jam_selesai));
                $value->tahun_ajaran = str_replace("/", "-", $value->tahun_ajaran);
            }
            $arr_ujian = array_values($arr_ujian);
            foreach ($get_siswa_ft as $key => $value) {
                $judul_ujian = $this->mymodel->withquery("select ju.nama_ujian from jenis_ujian ju join jadwal_ujian_ft j on ju.id_jenis_ujian = j.id_jenis_ujian join ujian_ruang_kelas k on j.id_tingkatan = k.id_tingkatan where k.id_tingkatan = '".$value->id_tingkatan."'","row");
                if (!empty($judul_ujian)) {
                    $value->judul_ujian = $judul_ujian->nama_ujian;
                    if ($list_ujian == "") {
                        $list_ujian .= $value->nama_ujian;
                    }
                    else{
                        $list_ujian .= ", ".$value->nama_ujian;
                    }
                }
                else{
                    $value->judul_ujian = "";
                    $list_ujian .= "";
                }
                $this->send_notif("Jangan lupa besok ada ujian", "Halo ".$value->nama_lengkap.", besok ada ujian ".$list_ujian.". Jangan lupa belajar ya", $value->device_id_siswa, array() );
            }
        }

        $msg = array('status' => 1, 'message' => 'Berhasil menjalankan CRON', 'jadwal_ujian_sd' => $get_ujian_sd, 'jadwal_ujian_smp' => $get_ujian_smp, 'jadwal_ujian_sma' => $get_ujian_sma, 'jadwal_ujian_ft' => $get_ujian_ft);
        $status = "200";
        //print_r($msg);
    }

    public function send_notif_legacy($title,$desc,$id_fcm,$data)
    {
        $Msg = array(
            'body' => $desc,
            'title' => $title
        );
        $fcmFields = array(
            'to' => $id_fcm,
            'notification' => $Msg,
             'data'=>$data
        );
        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        $cek_respon = explode(',',$result);
        $berhasil = substr($cek_respon[1],strpos($cek_respon[1],':')+1);
        //echo $result."\n\n";
    }

    public function send_notif($title,$desc,$fcm_id,$data){
        //$firebaseService = new FirebaseService();
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
                'data' => array()
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
        $message = "Ananda ".strtoupper($nama_siswa)." ".formatTanggal(date("Y-m-d"))." ".date("H:i")." WIB ".strtoupper($status_absen)." tidak hadir di sekolah tanpa keterangan, Terimakasih";
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
}
