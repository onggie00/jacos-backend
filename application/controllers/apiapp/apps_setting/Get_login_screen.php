<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_login_screen extends REST_Controller {
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
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];
        $data = array();

        //login page
        $data = $this->mymodel->withquery("select id_login_page, jenjang, role, title, sub_title, description, img_file, is_active, is_default from apps_login_page where is_active = '1'","result");
        if (!empty($data)) {
            foreach($data as $key => $value){
                if (!empty($value->img_file)){
                    $value->img_file = base_url('uploads/apps_login_page/'.$value->img_file);
                }
            }
            $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
            $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
