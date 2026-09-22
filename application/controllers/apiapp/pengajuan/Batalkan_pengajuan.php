<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Batalkan_pengajuan extends REST_Controller {
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

        $id_submission = $this->post('id_submission');
        
        $get_submission = $this->mymodel->withquery("select * from presensi_office_submission where id_submission = '".$id_submission."'","row");
        //delete image
        if(!empty($get_submission->file_submission)){
            unlink($get_submission->file_submission);
        }
        $delete = $this->mymodel->delete('presensi_office_submission','id_submission', $id_submission);

        if ($delete) {
            $msg = array('status' => 1, 'message'=>'Berhasil batalkan pengajuan' ,"data" => $get_submission);
            $status="200";
        }
        else{
            $msg = array('status' => 0, 'message'=>'Gagal batalkan pengajuan' , "data" => null);
            $status="200";
        }
        $this->response($msg,$status);
    }
}
