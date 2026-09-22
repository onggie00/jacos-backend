<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Periode_pendaftaran extends REST_Controller {
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

        $data = $this->second_db->withquery("select p.*, l.link_daftar_lain from web_periode_daftar p join web_link_daftar_lain l on p.id_web_periode_daftar = l.id","result");

        if (!empty($data)) {
          foreach ($data as $key => $value) {
            $value->periode_mulai_teks = formatTanggal($value->periode_pendaftaran_mulai);
            $value->periode_selesai_teks = formatTanggal($value->periode_pendaftaran_selesai);
            $value->periode_mulai_teks_unformated = $value->periode_pendaftaran_mulai;
            $value->periode_selesai_teks_unformated = $value->periode_pendaftaran_selesai;
            if (date("Y-m-d", strtotime($value->periode_pendaftaran_mulai)) <= date("Y-m-d") && date("Y-m-d", strtotime($value->periode_pendaftaran_selesai)) >= date("Y-m-d")) {
              $value->is_show = 1;
            }
            else if(!empty($id_siswa_aktif)){
              $value->is_show = 1;
            }
            else{
              $value->is_show = 2; 
            }
            if (!empty($value->link_daftar_lain)) {
              $value->is_redirected = 1;
            }
            else{
              $value->is_redirected = 2;
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
