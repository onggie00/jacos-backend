<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
define( 'API_ACCESS_KEY', 'AAAAX38-FW8:APA91bGoy4cJtX9jf4kfphdyh-1EZ3VFU8GlbVzXmka4-x-c2q6-oAvoltIKeSzoW4Pz8hbUL_MT0EW6NTUctWryTgsAlAmakleTaC-QzwLocy8OaVbswc_RuCC-tUaqPKta3TiYdoJ-' );
define( 'PRIVATE_FIREBASE_KEY', FCPATH . 'labscib-app-c0ca345e64d9.json');
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Izin extends REST_Controller {
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

        $jenjang=$this->post('jenjang');
        $id_kelas = $this->post('id_kelas');
        
        $data = array(
        //"jenjang" => $this->post('jenjang'),
        "id_siswa_aktif" => $this->post('id_siswa_aktif'),
        "tanggal_mulai" => $this->post('tanggal_mulai'),
        "tanggal_selesai" => $this->post('tanggal_selesai'),
        "keterangan" => $this->post('keterangan'),
        "jenis_izin" => $this->post('jenis_izin'),
        "status" => "pending"
        );

        //cek ijin 1 hari hanya boleh 1x pengajuan ijin
        $cek_ijin = $this->mymodel->withquery("select id from izin_siswa_".$jenjang." where id_siswa_aktif = '".$this->post('id_siswa_aktif')."' and tanggal_mulai='".date("Y-m-d", strtotime($this->post("tanggal_mulai")))."' ","row");
        if (empty($cek_ijin) || $cek_ijin->id == 0) {
            if (!empty($_FILES['file_izin']['name'])) {
                if($jenjang=='sd'){
                    $uploaddir = './uploads/izin_siswa_sd/';
                }elseif($jenjang=='smp'){
                    $uploaddir = './uploads/izin_siswa_smp/';
                }elseif($jenjang=='sma'){
                    $uploaddir = './uploads/izin_siswa_sma/';
                }else{
                    $uploaddir = './uploads/izin_siswa_ft/';
                }
                $img = explode('.', $_FILES['file_izin']['name']);
                $extension = end($img);
                $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_izin']['name']).".".$extension;
                $uploadfile = $uploaddir.$file_name;
                $status = 0;
                if (move_uploaded_file($_FILES['file_izin']['tmp_name'], $uploadfile)) {
                    $data['file_izin'] = $file_name;
                    $msg = array('success'=>1,'message'=>'Upload File Berhasil');
                }
            }

            if (!empty($data)) {
                if($this->post('tanggal_mulai') > $this->post('tanggal_selesai')){
                    $msg = array('status' => 0, 'message'=>'Data tanggal tidak valid', 'data' => array());
                }
                $insert=$this->mymodel->insertid("izin_siswa_".$jenjang,$data);
                if(!empty($insert)) {
                    $get_siswa = $this->mymodel->withquery("select sa.nama_lengkap, sa.id_kelas, s.notelp_ibu, s.notelp_ayah from siswa_".$jenjang."_aktif sa join siswa_".$jenjang." s on sa.id_siswa_".$jenjang." = s.id_siswa_".$jenjang." where id_siswa_".$jenjang."_aktif = '".$data['id_siswa_aktif']."' " ,"row");

                    //get guru piket sesuai jenjang
                    $get_guru = null;
                    if($jenjang=='sd'){
                        $get_guru = $this->mymodel->withquery("select p.*, g.device_id, g.no_telp, g.nama_lengkap, k.label as nama_kelas from guru_sd_piket p join guru_sd g on p.id_guru = g.id_guru join kelas_sd k on p.id_kelas = k.id_kelas_sd where p.id_kelas = '".$id_kelas."'","result");
                    }elseif($jenjang=='smp'){
                        $get_guru = $this->mymodel->withquery("select p.*, g.device_id, g.no_telp, g.nama_lengkap, k.label as nama_kelas from guru_smp_piket p join guru_smp g on p.id_guru = g.id_guru join kelas_smp k on p.id_kelas = k.id_kelas_smp where p.id_kelas = '".$id_kelas."'","result");
                    }elseif($jenjang=='sma'){
                        $get_guru = $this->mymodel->withquery("select p.*, g.device_id, g.no_telp, g.nama_lengkap, k.label as nama_kelas from guru_sma_piket p join guru_sma g on p.id_guru = g.id_guru join kelas_sma k on p.id_kelas = k.id_kelas_sma where p.id_kelas = '".$id_kelas."'","result");
                    }elseif($jenjang=='ft'){
                        $get_guru = $this->mymodel->withquery("select p.*, g.device_id, g.no_telp, g.nama_lengkap, k.label as nama_kelas from guru_ft_piket p left join guru_ft g on p.id_guru = g.id_guru join kelas_ft k on p.id_kelas = k.id_kelas_ft where p.id_kelas = '".$id_kelas."'","result");
                        if (empty($get_guru)) {
                            $get_guru = $this->mymodel->withquery("select p.*, g.device_id, g.no_telp, g.nama_lengkap, k.label as nama_kelas from guru_sma_piket p left join guru_ft g2 on p.id_guru = g2.id_guru left join guru_sma g on p.id_guru = g.id_guru join kelas_ft k on p.id_kelas = k.id_kelas_ft where p.id_kelas = '".$id_kelas."'","result");
                        }
                    }

                    if (!empty($get_guru)) {
                        $array_guru = array();
                        foreach ($get_guru as $key => $value) {
                            //array_push($array_guru, array("id_guru" => $value->id_guru, "nama_lengkap" => $value->nama_lengkap) );
                            //cek setting notifikasi ekskul ortu
                            $cek_setting = $this->mymodel->withquery("select * from setting_notifikasi_guru where id_guru = '".$value->id_guru."' and jenjang = '".$jenjang."'","row");
                            if (!empty($cek_setting) && $cek_setting->notifikasi_izin == 1) {
                                $this->send_notif("Pengajuan izin baru", "Pengajuan izin siswa ".strtoupper($get_siswa->nama_lengkap)." diterima", $value->device_id, array("id_izin" => $insert, "jenjang" => $jenjang) );
                            }
                            else if (empty($cek_setting) ){
                                $this->send_notif("Pengajuan izin baru", "Pengajuan izin siswa ".strtoupper($get_siswa->nama_lengkap)." diterima", $value->device_id, array("id_izin" => $insert, "jenjang" => $jenjang) );
                            }

                            if (!empty($value->no_telp)) {
                                $notelp = $value->no_telp;
                            }
                            $notelp = $this->cek_notelp($notelp);
                            $this->send_wa($notelp, "Pengajuan izin baru diterima, Siswa ".strtoupper($get_siswa->nama_lengkap)." ".$value->nama_kelas." telah mengajukan izin pada tanggal ".date("d-m-Y", strtotime($this->post('tanggal_mulai'))).". Silahkan melakukan approval di menu Profil > Pengajuan izin di aplikasi atau klik link berikut : https://admin.labschoolcibubur.sch.id/notif_wa_izin_guru?id=".$insert."&jenjang=".$jenjang);
                        }
                        //print_r($get_guru);
                        //shuffle($array_guru);
                        //$this->send_notif("Pengajuan izin baru", "Pengajuan izin siswa ".strtoupper($get_siswa->nama_lengkap)." diterima", $array_guru[0]->device_id, array("id_izin" => $insert, "jenjang" => $jenjang) );
                    }
                    $msg = array('status' => 1, 'message'=>'Berhasil tambah data', 'id'=>$insert,'data' => $data);
                }else{
                    $msg = array('status' => 0, 'message'=>'Gagal tambah data', 'data' => array());
                }
            }else{
                $msg = array('status' => 0, 'message'=>'Data tidak valid', 'data' => array());
            }
        }
        else{
                $msg = array('status' => 0, 'message'=>'Gagal mengajukan izin (Siswa telah mengajukan izin pada tanggal tersebut)', 'data' => array());
        }

        $this->response($msg);
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
        /* print_r($result);
        echo "<br/><br/>"; */
    }

    public function send_wa($notelp, $message){
    
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

}
