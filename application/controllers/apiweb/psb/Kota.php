<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Kota extends REST_Controller {
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

        $keyword = $this->get("keyword");
        $id_provinsi = $this->get("id_provinsi");

        if (!empty($keyword)) {
          $data = $this->mymodel->withquery("select id, name as kota from regencies where name like '%".$this->db->escape_like_str($keyword)."%' order by name ASC","result");
        }
        else if(!empty($id_provinsi)){
          $data = $this->mymodel->withquery("select id, name as kota from regencies where province_id = ".$this->db->escape($id_provinsi)." order by name ASC","result");
        }
        else{
          $data = $this->mymodel->withquery("select id, name as kota from regencies order by name ASC","result");
        }

        if (!empty($data)) {
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
