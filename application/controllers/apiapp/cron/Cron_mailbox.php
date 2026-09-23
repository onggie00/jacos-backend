<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cron_mailbox extends MY_Controller {
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

        $get_mailbox = $this->mymodel->withquery("select r.id as id_detail, r.id_mail, r.npp, r.nama_lengkap, r.nama_tabel, m.mailbox_title, m.mail_show_date, m.mail_show_time, r.is_read from mailbox_recipient r 
        left join mailbox_mail m on r.id_mail = m.id_mail where m.deleted_at is null and r.is_read is null group by r.id_mail order by m.mail_show_date desc, m.mail_show_time desc ","result");

        if (!empty($get_mailbox)){
            foreach ($get_mailbox as $key => $value) {
                $get_user = $this->mymodel->withquery("select device_id from ".$value->nama_tabel." where npp = '".$value->npp."' ","row");
                if (!empty($get_user->device_id) && $value->is_read === null && is_null($value->is_read) ) {
                    $this->send_notif("New Mailbox has Arrived!", $value->mailbox_title, $get_user->device_id, array("npp" => $value->npp, "id_mail" => $value->id_mail, "id_detail" => $value->id_detail, "tipe_notification" => "mailbox") );
                    $this->mymodel->update("mailbox_recipient",array("is_read" => "0", "updated_at" => date('Y-m-d H:i:s')),array("id" => $value->id_detail));
                    echo "Berhasil kirim : ".$value->id_detail." - ".$value->npp." - ".$value->mailbox_title."<br/>";
                }
            }
        }
    }

    public function send_notif($title,$desc,$fcm_id,$data){
        //$firebaseService = new FirebaseService();
        $id_detail = $data['id_detail'];
        $id_mail = $data['id_mail'];
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
                    'id_detail' => $id_detail,
                    'id_mail' => $id_mail,
                    'notification_type' => $data['tipe_notification']
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

?>