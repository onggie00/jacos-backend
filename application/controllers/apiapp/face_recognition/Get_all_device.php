<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_all_device extends MY_Controller {
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

        $host = "http://116.197.135.106";
        $port = "8091";
        // $host = "http://116.197.135.110";
        // $port = "8090";
        $data = new stdClass();
        //request token
        if (empty($token)) {
          $username = (!empty($this->input->get('username'))) ? $this->input->get('username') : "admin";
          $password = (!empty($this->input->get('password'))) ? $this->input->get('password') : "labschool123";
          $token = $this->request_token($host, $port, $username, $password);
          if (!empty($token)) {
            $token = $token->token;
          }
        }

        if (!empty($token)) {
            $msg = array('status' => 1, 'message'=>'Success Request Token' ,'token'=>$token, 'data'=>$data);
            $status="200";
            //Get all device list
            $list_device = $this->get_all_device($host, $port, $token);
            if(!empty($list_device)){
                $data->all_device = $list_device;
                $msg = array('status' => 1, 'message'=>'Success Get All Device' ,'token'=>$token, 'data'=>$data);
                $status="200";
            }
            else{
                $msg = array('status' => 0, 'message'=>'Data Not Found' ,'token'=>$token, 'data'=>$data);
                $status="200";
            }
        }
        else{
            $msg = array('status' => 0, 'message'=>'Request Token Failed' ,'token'=>$token, 'data'=>$data);
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

    private function get_all_device($host, $port, $token){ //Works
      $url = $host . ":" . $port . "/iclock/api/terminals/";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Authorization: JWT ' . $token)
      );
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result);
    }

    public function send_wa2($notelp, $nama_siswa, $status_absen, $alasan_terlambat = null){
      if ($status_absen == "Hadir") {
          $message = "Selamat pagi Bapak/Ibu, ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah melakukan presensi digital pada pukul ".date("H:i")." WIB. Terima kasih telah mendukung pembiasaan hadir tepat waktu di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab";
      }
      else if($status_absen == "Terlambat"){
          $message = "Selamat pagi Bapak/Ibu, ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah melakukan presensi digital pada pukul ".date("H:i")." WIB, sehingga dikategorikan TERLAMBAT HADIR. Penyebab keterlambatan : ".$alasan_terlambat.".   Kami sangat berterima kasih apabila Bapak/Ibu lebih mendorong Ananda membiasakan hadir tepat waktu di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
      }
      else{
          $message = "Selamat pagi Bapak/Ibu, ".strtoupper($nama_siswa)." hari ini tanggal ".formatTanggal(date("Y-m-d"))." telah melakukan presensi digital pada pukul ".date("H:i")." WIB, sehingga dikategorikan TERLAMBAT HADIR. Penyebab keterlambatan : ".$alasan_terlambat.".   Kami sangat berterima kasih apabila Bapak/Ibu lebih mendorong Ananda membiasakan hadir tepat waktu di sekolah. Semoga Ananda tumbuh menjadi pribadi disiplin dan bertanggung jawab.";
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

}
