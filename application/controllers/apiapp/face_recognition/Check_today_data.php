<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Check_today_data extends MY_Controller {
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

        $setting = $this->get_setting();
        $host = $setting->host;
        $port = $setting->port;
        // $host = "http://116.197.135.110";
        // $port = "8090";
        $data = array();//new stdClass();
        //get token di database, jika updated at != hari ini maka request token baru kemudian update database
        $token = (!empty($token)) ? $token : $this->get_token();
        //request token
        if (empty($token)) {
            $username = (!empty($this->input->get('username'))) ? $this->input->get('username') : $setting->username;
            $password = (!empty($this->input->get('password'))) ? $this->input->get('password') : $setting->password;
            $token = $this->request_token($host, $port, $username, $password);
            if (!empty($token)) {
                $token = $token->token;
            }
            //update token
            if (!empty($token)) {
                $this->mymodel->update("presensi_setting",array('value' => $token, 'updated_at' => date('Y-m-d H:i:s')),array('name_setting' => 'token_mesin'));
            }
        }

        if (!empty($token)) {
            $msg = array('status' => 1, 'message'=>'Success Request Token' ,'token'=>$token, 'data'=>$data);
            $status="200";
            //Get riwayat presensi
            $start_time = (!empty($this->input->get('start_time'))) ? $this->input->get('start_time') : date('Y-m-d')." 00:00:00.000";
            $end_time = (!empty($this->input->get('end_time'))) ? $this->input->get('end_time') : date('Y-m-d')." 23:59:59.000";
            $hari = (!empty($this->input->get('start_time'))) ? formatHari($this->input->get('start_time')) : formatHari(date('Y-m-d'));
            $emp_code = (!empty($this->input->get('emp_code'))) ? $this->input->get('emp_code') : "";
            $page_size = (!empty($this->input->get('page_size'))) ? $this->input->get('page_size') : 1000;
            
            // get all device
            $list_mesin = $this->get_all_device();
            if(!empty($list_mesin)){
                foreach($list_mesin as $key => $value){
                    $terminal_sn = $value->sn_mesin;
                    $terminal_alias = $value->nama_mesin;
                    $data_get = array(
                        'emp_code' => $emp_code,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'page_size' => $page_size,
                        'terminal_sn' => $terminal_sn,
                        'terminal_alias' => $terminal_alias,
                        'url' => $host . ":" . $port . "/iclock/api/transactions/"."?emp_code=" . $emp_code . "&page_size=" . $page_size . "&start_time=" . $start_time . "&end_time=" . $end_time."&terminal_sn=" . $terminal_sn."&terminal_alias=" . $terminal_alias
                    );
                    $riwayat_presensi = $this->get_transaction_by_time($host, $port, $token, $emp_code, $start_time, $end_time, $page_size, $terminal_sn, $terminal_alias);
                    if(!empty($riwayat_presensi)){
                        $riwayat_presensi = $riwayat_presensi->data;
                        // $data[] = $riwayat_presensi;
                        foreach($riwayat_presensi as $key_riwayat => $value_riwayat){
                            $data_response = array(
                                'emp_code' => $value_riwayat->emp_code,
                                'first_name' => $value_riwayat->first_name,
                                'npp' => (empty($value_riwayat->last_name)) ? $value_riwayat->emp_code : $value_riwayat->last_name,
                                'hari' => formatHari($value_riwayat->punch_time),
                                'punch_time' => $value_riwayat->punch_time,
                                'punch_display_status' => $value_riwayat->punch_state_display,
                                'presensi_role' => $value_riwayat->department,
                                'presensi_sn' => $value_riwayat->terminal_sn,
                                'presensi_device' => $value_riwayat->terminal_alias,
                            );
                            //cek apakah data sudah ada di array
                            if(empty($data) && !empty($data_response['npp']) ){
                                $data[] = $data_response;
                            }
                            else if(!in_array($data_response, $data) && !empty($data_response['npp']) ){
                                $data[] = $data_response;
                            }
                        }
                        if (!empty($data)) {
                            usort($data, function ($a, $b) {
                                return strtotime($a['punch_time']) - strtotime($b['punch_time']);
                            });
                        }
                        $msg = array('status' => 1, 'message'=>'Success' ,'token'=>$token, 'data'=>$data);
                        $status="200";
                    }
                    else{
                        continue;
                    }
                    // $data->request = $data_get;
                }

                //cek insert ke database
                if(!empty($data)){
                    $msg = array('status' => 1, 'message'=>'Success' ,'token'=>$token, 'data'=>$data);
                    $status="200";
                }
                else{
                    $msg = array('status' => 0, 'message'=>'Data not found (Empty)' ,'token'=>$token, 'data'=>$data);
                    $status="200";
                }
            }
            else{
                $msg = array('status' => 0, 'message'=>'Device not found' ,'token'=>$token, 'data'=>$data);
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

    public function get_transaction_by_time($host, $port, $token, $emp_code, $start_time, $end_time, $page_size, $terminal_sn, $terminal_alias){ /*Unstable, cannot send date and time*/
        $url = $host . ":" . $port . "/iclock/api/transactions/"."?emp_code=" . $emp_code . "&page_size=" . $page_size . "&start_time=" . $start_time . "&end_time=" . $end_time."&terminal_sn=" . $terminal_sn."&terminal_alias=" . $terminal_alias;
        $url = str_replace(" ", "%20", $url);
        //$url = "http://116.197.135.106:8091/iclock/api/transactions/?emp_code=68224235&page_size=100&start_time=2025-11-25%2000:00:00.000&end_time=2025-11-25%2023:59:59.999";
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

    private function get_all_device(){
        $data = $this->mymodel->withquery("select * from presensi_device","result");

        return $data;
    }

    private function get_token(){
        $data = $this->mymodel->withquery("select * from presensi_setting where name_setting = 'token_mesin'","row")->value;
        if(!empty($data) && date("Y-m-d", strtotime($data)) != date("Y-m-d") ){
            $data = null;
        }

        return $data;
    }

    private function get_setting(){
        $get_setting = $this->mymodel->withquery("select * from presensi_setting","result");

        $data = new stdClass();
        if (!empty($get_setting)) {
            foreach ($get_setting as $key => $value) {
                $data->{$value->name_setting} = $value->value;
            }
        }
        
        return $data;
    }

    private function cek_pegawai($id_pegawai){
        $data = $this->mymodel->withquery("select * from presensi_pegawai where id_pegawai = '$id_pegawai'","row");
        
        return $data;
    }

}