<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Graph\Generated\Users\Item\ChangePassword\ChangePasswordPostRequestBody;

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Ubah_password extends REST_Controller {
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

        $where = "";
        $date = date('Y-m-d H:i:s');

        $jenjang = strtolower($this->post('jenjang'));
        $data = array();//$this->second_db->withquery("select * from siswa_".$jenjang."_aktif sa where sa.id_siswa_".$jenjang."_aktif = '".$this->post('id_siswa_aktif')."' ","row");
        
        $access_token = $this->post('access_token');
        if (!empty($access_token)) {
            $data_post = array(
                "currentPassword" => $this->post('currentPassword'),
                "newPassword" => $this->post('newPassword'),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token, 'Content-type: application/json'));
            curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me/changePassword");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_post));
            $result = json_decode(curl_exec($ch), 1);

            if($result == null){
                $data_post['email'] = $this->post('email');
                if (!empty($value->no_telp)) {
                    $notelp = $value->no_telp;
                }
                $notelp = $this->cek_notelp($notelp);
                $this->send_wa($notelp, "Ubah Password Email Anda Berhasil, Email : ".$data_post['email']." Password Baru : ".$data_post['newPassword']);
                $msg = array('status' => 1, 'message'=>'Berhasil request perubahan password' ,'data'=>$data_post, 'result' => $result);
            }
            else{
                $msg = array('status' => 0, 'message'=>'Gagal request perubahan password' ,'data'=>array(), 'result' => $result);
            }
            $status="200";
        }
        else{
            $msg = array('status' => 0, 'message'=>'Akses Token tidak boleh kosong, silahkan login dahulu untuk mendapatkan akses token' ,'data'=>array(), 'result' => array());
            $status="200";
        }

        $this->response($msg,$status);
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
            'appkey' => 'c845b7df-9b39-4036-ac40-d2318675e290',
            'authkey' => '2awgKS7tXUahAc9vpAXkfyLhTrb9juKsIKxUuOs3qPei9dqvY6',
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
}
