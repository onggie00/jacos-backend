<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Mark_important extends REST_Controller {
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
        $token =  $headers['x-token'];
        if (empty($token)){
            $token =  $headers['x-api-token'];
        }

        $id_recipient = $this->post('id_detail');
        $data = $this->mymodel->withquery("select m.id_mail, r.id as id_detail, m.mailbox_title, m.mailbox_description, m.is_confidential, m.mail_number, r.npp, r.nama_lengkap, r.role, r.nama_tabel, 
        CASE WHEN r.is_important = 1 THEN 'Y' ELSE 'N' END AS is_important,
        CASE WHEN r.is_read = 1 THEN 'Sudah dibaca' ELSE 'Belum dibaca' END AS is_read, r.created_at from mailbox_recipient r 
        left join mailbox_mail m on r.id_mail = m.id_mail 
        where r.id = '".$id_recipient."' ","row");
        $data_update = array(
            'is_important' => ($data->is_important == 'Y') ? 0 : 1,
            'updated_at' => date('Y-m-d H:i:s')
        );

        if (isset($data_update)) {
            $data = $this->mymodel->update("mailbox_recipient", $data_update, "id", $id_recipient);
            $data_update['id_detail'] = $id_recipient;
            $msg = array('status' => 1, 'message'=>'Berhasil ubah data' ,'data'=>$data_update);
            $status="200";
        }
        else{
            $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
            $status="200";
        }

        $this->response($msg,$status);
    }
}
