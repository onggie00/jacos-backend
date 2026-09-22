<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
define( 'PRIVATE_FIREBASE_KEY', FCPATH . 'labscib-app-c0ca345e64d9.json');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_riwayat_presensi extends MY_Controller {
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
        $token = $headers['x-token'];

        $host = "http://116.197.135.110";
        $port = "8090";
        $jenjang = "sd";
        // $host = "http://116.197.135.106";
        // $port = "8091";
        $data = new stdClass();
        //request token
        //cek hari libur
        $cek_hari_libur = (date("N") >= 6) ? 1 : 0;
    if($cek_hari_libur == 0){
        if (empty($token)) {
            $username = (!empty($this->input->post('username'))) ? $this->input->post('username') : "admin";
            $password = (!empty($this->input->post('password'))) ? $this->input->post('password') : "labschool123";
            $token = $this->request_token($host, $port, $username, $password);
            if (!empty($token)) {
                $token = $token->token;
            }
        }

        if (!empty($token)) {
            $msg = array('status' => 1, 'message'=>'Success Request Token' ,'token'=>$token, 'data'=>$data);
            $status="200";
            //Get riwayat presensi
            $start_time = (!empty($this->input->get('start_time'))) ? $this->input->get('start_time') : date('Y-m-d');
            $end_time = (!empty($this->input->get('end_time'))) ? $this->input->get('end_time') : date('Y-m-d', strtotime($start_time . ' +1 day'));
            $emp_code = (!empty($this->input->get('emp_code'))) ? $this->input->get('emp_code') : "";
            $page_size = (!empty($this->input->get('page_size'))) ? $this->input->get('page_size') : 1000;
            $now = new DateTime();
            $jam_sekolah_mulai = new DateTime('06:00');
            $jam_sekolah_akhir = new DateTime('16:00');
            if (!empty($start_time) && !empty($end_time) && !empty($page_size) && $now >= $jam_sekolah_mulai && $now <= $jam_sekolah_akhir) {
                //cek keterangan terlambat
                $waktu_batas_absen = $this->mymodel->withquery("select batas_jam from jam_presensi where jenjang = '".$jenjang."'","row")->batas_jam;
                $data_get = array(
                    'emp_code' => $emp_code,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'page_size' => $page_size,
                    'url' => $host . ":" . $port . "/iclock/api/transactions/"."?emp_code=" . $emp_code . "&page_size=" . $page_size . "&start_time=" . $start_time . "&end_time=" . $end_time
                );
                $get_presensi_mesin = $this->get_transaction_by_time($host, $port, $token, $emp_code, $start_time, $end_time, $page_size);
                //$riwayat_presensi = $this->get_transaction_report($host, $port, $token, $start_time, $end_time, $page_size);
                $riwayat_presensi_mesin = $get_presensi_mesin->data;
                $tahun_ajaran = $this->mymodel->withquery("select id_tahun_ajaran from tahun_ajaran where tanggal_mulai <= '".date('Y-m-d')."' and tanggal_selesai >= '".date('Y-m-d')."'","row")->id_tahun_ajaran;
                $get_all_siswa = $this->mymodel->withquery("select sa.id_siswa_".$jenjang."_aktif as id_siswa_aktif, sa.nis, s.notelp_ibu, s.notelp_ayah, sa.nama_lengkap, sa.device_id_siswa, sa.device_id_ortu from siswa_".$jenjang."_aktif sa 
                join siswa_".$jenjang." s on sa.id_siswa_".$jenjang." = s.id_siswa_".$jenjang." 
                where id_siswa_".$jenjang."_aktif and id_tahun_ajaran = '".$tahun_ajaran."' order by id_siswa_".$jenjang."_aktif asc","result");
                $get_presensi_server = $this->mymodel->withquery("select p.*, sa.nis, sa.id_siswa_".$jenjang."_aktif as id_siswa_aktif, s.notelp_ibu, s.notelp_ayah, s.nama_lengkap from presensi_".$jenjang." p 
                join siswa_".$jenjang."_aktif sa on p.id_siswa_aktif = sa.id_siswa_".$jenjang."_aktif 
                join siswa_".$jenjang." s on sa.id_siswa_".$jenjang." = s.id_siswa_".$jenjang."
                where p.tanggal_absen between '".$start_time."' and '".$end_time."' order by p.tanggal_absen asc, p.waktu_absen asc","result");
                $riwayat_presensi_server = $get_presensi_server;
                
                $data_presensi_siswa = array();
                if(!empty($riwayat_presensi_mesin)){

                    foreach($riwayat_presensi_mesin as $key => $value){
                        //cek apakah data sudah ada di database
                        $tanggal = date('Y-m-d', strtotime($value->punch_time));
                        $hari = formatHari($value->punch_time);
                        $waktu = date('H:i:s', strtotime($value->punch_time));
                        // cek apakah emp_code = nis di all siswa
                        foreach($get_all_siswa as $key1 => $value1){
                            //jika nis = emp_code, maka cek sudah presensi / belum (jika belum maka insert)
                            if($value1->nis == $value->emp_code){
                                $found = 0;
                                $id_presensi_server = null;
                                foreach($riwayat_presensi_server as $key2 => $value2){
                                    //jika sudah ditemukan berarti sudah presensi hari itu = tidak perlu insert & kirim wa cukup update(updated_at)
                                    if($value1->nis == $value2->nis){
                                        $found =  1;
                                        $id_presensi_server = $value2->id;
                                        break;
                                    }
                                }
                                //jika belum ditemukan = insert
                                if($found == 0){
                                    if (strtotime($waktu) > strtotime($waktu_batas_absen) ){
                                        $data = array(
                                            "id_siswa_aktif" => $value1->id_siswa_aktif,
                                            "wifi_ssid" => "Face Recognition ".$value->terminal_alias,
                                            "wifi_ip" => "",
                                            "hari_absen" => $hari,
                                            "waktu_absen" => $waktu,
                                            "tanggal_absen" => $tanggal,
                                            "status_absen" => 'Terlambat',
                                            "nis" => $value1->nis,
                                            "alasan_terlambat" => "",
                                        );
                                    }else{
                                        $data = array(
                                            "id_siswa_aktif" => $value1->id_siswa_aktif,
                                            "wifi_ssid" => "Face Recognition ".$value->terminal_alias,
                                            "wifi_ip" => "",
                                            "hari_absen" => $hari,
                                            "nis" => $value1->nis,
                                            "waktu_absen" => $waktu,
                                            "tanggal_absen" => $tanggal,
                                            "status_absen" => 'Hadir',
                                        );
                                    }
                                    $id_presensi = $this->mymodel->insertid("presensi_".$jenjang, $data);
                                    $riwayat_presensi_server = $this->mymodel->withquery("select p.*, sa.nis, sa.id_siswa_".$jenjang."_aktif as id_siswa_aktif, s.notelp_ibu, s.notelp_ayah, s.nama_lengkap from presensi_".$jenjang." p 
                                    join siswa_".$jenjang."_aktif sa on p.id_siswa_aktif = sa.id_siswa_".$jenjang."_aktif 
                                    join siswa_".$jenjang." s on sa.id_siswa_".$jenjang." = s.id_siswa_".$jenjang."
                                    where p.tanggal_absen between '".$start_time."' and '".$end_time."' order by p.tanggal_absen asc, p.waktu_absen asc","result");
                                    //echo $id_presensi." ".$value1->id_siswa_aktif." ".$value1->nama_lengkap." : "." ".$value->emp_code." ".date('Y-m-d H:i:s', strtotime($value->punch_time))."<br>";
                                    $data['id_presensi'] = $id_presensi;
                                    $data_presensi_siswa[] = $data;
                                    //jika berhasil insert maka kirim wa ke orang tuanya
                                    if (!empty($value1->notelp_ibu) && $value1->notelp_ibu != '-') {
                                        $notelp = $value1->notelp_ibu;
                                    }
                                    else if(!empty($value1->notelp_ayah) && $value1->notelp_ibu != '-'){
                                        $notelp = $value1->notelp_ayah;
                                    }
                                    if (!empty($notelp)) {
                                        $notelp = $this->cek_notelp($notelp);
                                    }
                                    $nama_siswa = $value1->nama_lengkap;
                                    if(!empty($notelp)){
                                        // $this->send_wa2($notelp,$nama_siswa, $data, "");
                                        if ($data['status_absen'] == "Hadir") {
                                            $pesan = "Selamat pagi Bapak/Ibu, *".strtoupper($nama_siswa)."* hari ini tanggal *".formatTanggal($data['tanggal_absen'])."* telah melakukan presensi *".strtoupper($data['status_absen'])."* pada pukul *".date("H:i", strtotime($data['waktu_absen']))."* WIB. Terima kasih telah mendukung pembiasaan hadir tepat waktu di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
                                        }
                                        else if($data['status_absen'] == "Terlambat"){
                                            $pesan = "Selamat pagi Bapak/Ibu, *".strtoupper($nama_siswa)."* hari ini tanggal *".formatTanggal($data['tanggal_absen'])."* telah melakukan presensi *HADIR* pada pukul *".date("H:i", strtotime($data['waktu_absen']))."* WIB, sehingga dikategorikan *TERLAMBAT HADIR*.   Kami sangat berterima kasih apabila Bapak/Ibu lebih mendorong Ananda membiasakan hadir tepat waktu di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
                                        }
                                        else{
                                            $pesan = "Selamat pagi Bapak/Ibu, *".strtoupper($nama_siswa)."* hari ini tanggal *".formatTanggal($data['tanggal_absen'])."* telah melakukan presensi *".strtoupper($data['status_absen'])."* pada pukul *".date("H:i", strtotime($data['waktu_absen']))."* WIB, sehingga dikategorikan TERLAMBAT HADIR.   Kami sangat berterima kasih apabila Bapak/Ibu lebih mendorong Ananda membiasakan hadir tepat waktu di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
                                        }
                                        $this->send_wa_omni($notelp,  $pesan);
                                    }

                                    //kirim notifikasi labscib apps Siswa & ortu
                                    $device_id = $value1->device_id_ortu;
                                    // echo $device_id." ".$value1->id_siswa_aktif." ".$value1->nama_lengkap." : "." ".$value->emp_code." ".date('Y-m-d H:i:s', strtotime($value->punch_time))."<br/><br/>";
                                    if (!empty($device_id)) {
                                        if ($data['status_absen'] == "Hadir") {
                                            $pesan = strtoupper($nama_siswa)."telah melakukan presensi pada pukul ".date("H:i", strtotime($data['waktu_absen']))." tanggal ".formatTanggal($data['tanggal_absen']);
                                        }
                                        else if($data['status_absen'] == "Terlambat"){
                                            $pesan = strtoupper($nama_siswa)." tanggal ".formatTanggal($data['tanggal_absen'])." telah melakukan presensi pada pukul ".date("H:i", strtotime($data['waktu_absen'])).", tercatat sebagai TERLAMBAT HADIR";
                                        }
                                        else{
                                            $pesan = strtoupper($nama_siswa)." tanggal ".formatTanggal($data['tanggal_absen'])." telah melakukan presensi ".strtoupper($data['status_absen'])." pada pukul ".date("H:i", strtotime($data['waktu_absen']))." (".strtoupper($data['wifi_ssid']).")";
                                        }
                                        $notif = $this->send_notif("PRESENSI MASUK ".strtoupper($nama_siswa), $pesan, $device_id, array("nis" => $value1->nis, "id_presensi" => $id_presensi, "id_siswa_aktif" => $data['id_siswa_aktif'], "jenjang" => $jenjang) );
                                        // $this->mymodel->update("presensi_".$jenjang, array("notif_response" => $notif), "id", $id_presensi);
                                        // echo $this->db->last_query();
                                    }
                                }
                                /* else if($found == 1){
                                    if (strtotime($waktu) > strtotime($waktu_batas_absen) ){
                                        $data = array(
                                            "id_siswa_aktif" => $value1->id_siswa_aktif,
                                            "wifi_ssid" => "Face Recognition ".$value->terminal_alias,
                                            "wifi_ip" => "",
                                            "hari_absen" => $hari,
                                            "waktu_absen" => $waktu,
                                            "tanggal_absen" => $tanggal,
                                            "status_absen" => 'Terlambat',
                                            "alasan_terlambat" => "",
                                        );
                                    }else{
                                        $data = array(
                                            "id_siswa_aktif" => $value1->id_siswa_aktif,
                                            "wifi_ssid" => "Face Recognition ".$value->terminal_alias,
                                            "wifi_ip" => "",
                                            "hari_absen" => $hari,
                                            "waktu_absen" => $waktu,
                                            "tanggal_absen" => $tanggal,
                                            "status_absen" => 'Hadir',
                                        );
                                    }
                                    // $id_presensi = $this->mymodel->update("presensi_".$jenjang, $data, "id", $id_presensi_server);
                                    //echo $id_presensi." ".$value1->id_siswa_aktif." ".$value1->nama_lengkap." : "." ".$value->emp_code." ".date('Y-m-d H:i:s', strtotime($value->punch_time))."<br>";
                                    $data['id_presensi'] = $id_presensi;
                                    $data_presensi_siswa[] = $data;
                                } */
                                else{
                                    continue;
                                }
                            }
                        }

                    }
                    $msg = array('status' => 1, 'message'=>'Sync Data Berhasil' ,'token'=>$token, 'data'=>$data_presensi_siswa, 'res' => $riwayat_presensi_mesin);
                    $status="200";
                }
                else{
                    $msg = array('status' => 0, 'message'=>'Data Empty' ,'token'=>$token, 'data'=>$data);
                    $status="200";
                }

                //$data->request = $data_get;
            }
        }
        else{
            $msg = array('status' => 0, 'message'=>'Request Token Failed' ,'token'=>$token, 'data'=>$data);
            $status="200";
        }
    }
    else{
        // echo "Hari Libur, Mesin dinonaktifkan";
        $msg = array('status' => 0, 'message'=>'Hari Libur, Mesin dinonaktifkan' ,'token'=>"", 'data'=>array());
        $status="200";
    }

        $this->response($msg,$status);
    }

    private function request_token($host, $port, $username, $password){ //Works
      $data = new stdClass();
      $data->username = (!empty($username)) ? $username : "admin";
      $data->password = (!empty($password)) ? $password : "labschool123";

      $url = $host . ":" . $port . "/jwt-api-token-auth/";
      $data = json_encode($data);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json')
      );
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result);
    }

    private function get_transaction_by_time($host, $port, $token, $emp_code, $start_time, $end_time, $page_size){ /*Unstable, cannot send date and time*/
        $url = $host . ":" . $port . "/iclock/api/transactions/"."?emp_code=" . $emp_code . "&page_size=" . $page_size . "&start_time=" . $start_time . "&end_time=" . $end_time;
        $url = str_replace(" ", "%20", $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: JWT ' . $token,
            'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        if (curl_errno($ch)) { 
            print curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result);
    }

    private function get_transaction_report($host, $port, $token, $start_time, $end_time, $page_size){ /*Unstable, cannot send date and time*/
        $url = $host . ":" . $port . "/att/api/transactionReport/"."?page_size=" . $page_size . "&start_time=" . $start_time . "&end_time=" . $end_time;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: JWT ' . $token,
            'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        if (curl_errno($ch)) { 
            print curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result);
    }

    public function send_wa2($notelp, $nama_siswa, $data_get, $alasan_terlambat = null){
      if ($data_get['status_absen'] == "Hadir") {
          $message = "Selamat pagi Bapak/Ibu, *".strtoupper($nama_siswa)."* hari ini tanggal *".formatTanggal($data_get['tanggal_absen'])."* telah melakukan presensi *".strtoupper($data_get['status_absen'])."* pada pukul *".date("H:i", strtotime($data_get['waktu_absen']))."* WIB. Terima kasih telah mendukung pembiasaan hadir tepat waktu di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
      }
      else if($data_get['status_absen'] == "Terlambat"){
          $message = "Selamat pagi Bapak/Ibu, *".strtoupper($nama_siswa)."* hari ini tanggal *".formatTanggal($data_get['tanggal_absen'])."* telah melakukan presensi *HADIR* pada pukul *".date("H:i", strtotime($data_get['waktu_absen']))."* WIB, sehingga dikategorikan *TERLAMBAT HADIR*.   Kami sangat berterima kasih apabila Bapak/Ibu lebih mendorong Ananda membiasakan hadir tepat waktu di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
      }
      else{
          $message = "Selamat pagi Bapak/Ibu, *".strtoupper($nama_siswa)."* hari ini tanggal *".formatTanggal($data_get['tanggal_absen'])."* telah melakukan presensi *".strtoupper($data_get['status_absen'])."* pada pukul *".date("H:i", strtotime($data_get['waktu_absen']))."* WIB, sehingga dikategorikan TERLAMBAT HADIR.   Kami sangat berterima kasih apabila Bapak/Ibu lebih mendorong Ananda membiasakan hadir tepat waktu di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
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
        // echo "cURL Error #:" . $err;
        } else {
        // echo $response;
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
            'body' => $desc,
            'id_presensi' => $data['id_presensi'],
            'jenjang' => $data['jenjang']

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
        $this->mymodel->update("presensi_".$data['jenjang'], array("notif_response" => json_encode($result)), "id", $data['id_presensi']);
        // echo $this->db->last_query();
        // echo json_encode($result);
        /*print_r($result);
        echo "<br/><br/>";*/
    }

}
