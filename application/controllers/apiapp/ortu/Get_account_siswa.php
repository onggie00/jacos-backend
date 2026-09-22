<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_account_siswa extends REST_Controller {
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

        // $email_ms_office_ortu = $this->post('email_ms_office_ortu');
        $notelp_ortu = $this->post('notelp_ortu');

        if (!empty($notelp_ortu)) {
            // $where_and_or="notelp_ibu=$notelp_ortu or notelp_ayah=$notelp_ortu";

            // $check_if_email_exist = $this->mymodel->withquery("select * from switch_account where email_ms_office_ortu like '%" . $email_ms_office_ortu . "%' $where_and_or","result");
            $check_if_notelp_exist = $this->mymodel->withquery("select * from switch_account where notelp_ibu like '%" . $notelp_ortu . "%' or notelp_ayah like '%" . $notelp_ortu . "%'","result");

            if (!empty($check_if_notelp_exist)) {
                $msg = array('status' => 200, 'message'=>'Berhasil mendapatkan semua data siswa dari ortu!', 'data' => $check_if_notelp_exist);
            }else{
                $msg = array('status' => 401, 'message'=>'Gagal mendapatkan data!', 'data' => $notelp_ortu);
            }
        }
        $this->response($msg);
    }
}