<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Mailbox extends REST_Controller {
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
        $page = (empty($this->input->get('page'))) ? 0 : $this->input->get('page');
        $limit = (empty($this->input->get('limit'))) ? 20 : $this->input->get('limit');
        $search = (!empty($this->input->get('search'))) ? " and r.nama_lengkap like '%".$this->input->get('search')."%' or r.npp like '%".$this->input->get('search')."%' or m.mailbox_title like '%".$this->input->get('search')."%' or m.mailbox_description like '%".$this->input->get('search')."%' or m.mail_number like '%".$this->input->get('search')."%'" : '';
        $category = (!empty($this->input->get('category'))) ? " and m.mailbox_category = '".$this->input->get('category')."' " : '';
        $category = $this->input->get('category');

        if($this->input->get('category') != null){
            if($this->input->get('category') == "6") {
                $category = " and (r.is_important = 1) ";
            }
            else {
                $category = " and m.mailbox_category = '".$this->input->get('category')."' ";
            }
        }
        
        $data = $this->mymodel->withquery("SELECT 
            m.id_mail,
            r.id as id_detail,
            m.mailbox_title,
            m.mailbox_description,
            r.npp,
            r.nama_lengkap,
            r.role,
            r.nama_tabel,
            m.mail_show_date,
            m.mail_show_time,
            CASE WHEN r.is_important = 1 THEN 'Y' ELSE 'N' END AS is_important,
            CASE WHEN r.is_read = 1 THEN 'Sudah dibaca' ELSE 'Belum dibaca' END AS is_read,
            r.created_at
        FROM mailbox_recipient r
        JOIN mailbox_mail m 
            ON r.id_mail = m.id_mail and r.npp = '".$npp."'
        WHERE r.npp = '".$npp."' and CONCAT(m.mail_show_date, ' ', m.mail_show_time) <= NOW() ".$search." ".$category." and r.nama_tabel = '".$tipe_user."' 
        group by m.id_mail order by r.created_at DESC limit ".$page.",".$limit." ","result");
        // echo $this->db->last_query();
        if (!empty($data)) {
            $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
            $status="200";
        }
        else{
            $msg = array('status' => 0, 'message'=>'Data kosong / Role tidak sesuai' ,'data'=>array());
            $status="200";
        }

        $this->response($msg,$status);
    }
}
