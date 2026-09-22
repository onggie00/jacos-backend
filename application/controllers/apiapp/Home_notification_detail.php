<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Home_notification_detail extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_get()
    {
        $status = "";
        $token = "";
        $headers=array();
        foreach (getallheaders() as $name => $value) {
                $headers[$name] = $value;
        }
        $token =  $headers['x-token'];
        if (empty($token)){
            $token =  $headers['x-api-token'];
        }
        
        $npp = $this->input->get('npp');
        $tipe_user = $this->input->get('role'); // nama_tabel
        
        $data_notification = array();
        $total_mailbox = 0;
        $total_acara = 0;

        //section mailbox
        $get_mail = $this->mymodel->withquery("SELECT 
            m.id_mail,
            r.id as id_detail,
            m.mailbox_title,
            r.npp,
            r.nama_lengkap,
            r.nama_tabel,
            r.is_read,
            CONCAT (m.mail_show_date,' ',m.mail_show_time) as created_at
        FROM mailbox_recipient r
        JOIN mailbox_mail m 
            ON r.id_mail = m.id_mail and r.npp = '".$npp."'
        WHERE r.npp = '".$npp."' and CONCAT(m.mail_show_date, ' ', m.mail_show_time) <= NOW() and r.nama_tabel = '".$tipe_user."' 
        group by m.id_mail order by r.created_at DESC limit 0,20 ","result");
        // echo $this->db->last_query();
        if(!empty($get_mail)){
            foreach ($get_mail as $key => $value) {
                $data_add = array(
                    "notification_type" => "mailbox",
                    "id_detail" => $value->id_detail,
                    "title" => $value->mailbox_title,
                    "nama_lengkap" => $value->nama_lengkap,
                    "red_mark_notification" => (empty($value->is_read)) ? 0 : 1,
                    "created_at" => $value->created_at,
                );
                $data_notification[] = $data_add;
            }
        }

        //section presensi
        /* $data_add = array(
            "notification_type" => "acara",
            "red_mark_notification" => (empty($total_acara)) ? 0 : 1,
        );
        $data_notification[] = $data_add; */

        if (!empty($data_notification)) {
            $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data_notification);
            $status="200";
        }
        else{
            $msg = array('status' => 0, 'message'=>'Data kosong / Role tidak sesuai' ,'data'=>array());
            $status="200";
        }

        $this->response($msg,$status);
    }
}
