<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cek_waktu_terlambat extends REST_Controller {
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
        $jenjang = $this->post("jenjang");
        $waktu_absen = $this->post("waktu_absen");

        $waktu_batas_absen = $this->second_db->withquery("select batas_jam from jam_presensi where jenjang = '".$jenjang."'","row")->batas_jam;

        if (strtotime($waktu_absen) > strtotime($waktu_batas_absen)) {
          $msg = array('status' => 1, 'message'=>'Terlambat' ,'data'=>$waktu_batas_absen);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Belum Terlambat' ,'data'=>$waktu_batas_absen);
          $status="200";
        }

        $this->response($msg,$status);
    }
}
