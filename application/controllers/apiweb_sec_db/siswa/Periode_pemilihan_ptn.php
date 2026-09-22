<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Periode_pemilihan_ptn extends REST_Controller {
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
        $where="";
        
        // dd($where);
        $today = date("Y-m-d");
        $data = $this->second_db->withquery("select * from pt_periode_pemilihan where id = '1'","result");
        if (!empty($data)) {
          foreach ($data as $key => $value) {
            if (date("m", strtotime($value->start_date)) == date("m", strtotime($value->end_date)) ) {
              $value->periode_text = date("d", strtotime($value->start_date))." - ".date("d", strtotime($value->end_date)). " " . formatBulan($value->start_date)." ".date("Y", strtotime($value->start_date));
            }
            else{
              $value->periode_text = formatTanggal($value->start_date)." - ".formatTanggal($value->end_date);
            }
            if ($today >= $value->start_date && $today <= $value->end_date) {
              $value->can_select = 1;
            }
            else{
              $value->can_select = 0;
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
