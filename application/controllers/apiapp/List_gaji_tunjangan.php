<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class List_gaji_tunjangan extends REST_Controller {
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
        $npp = $this->post('npp');
        $npp_no_dot = str_replace(".", "", $npp);
        $bulan = (!empty($this->post('bulan'))) ? $this->post('bulan') : date('m');
        $tahun = (!empty($this->post('tahun'))) ? $this->post('tahun') : date('Y');

        $data = $this->mymodel->withquery("select id_slip, npp, periode_mulai, periode_selesai, total_diterima_gaji, total_diterima_tunjangan from pegawai_slip 
        where (npp = '".$npp."' or npp = '".$npp_no_dot."') and MONTH(periode_selesai) = '".$bulan."' and YEAR(periode_selesai) = '".$tahun."'","row");

        //get thr / gaji14
        $get_slip_custom = $this->mymodel->withquery("select * from pegawai_slip_custom where (npp = '".$npp."' or npp = '".$npp_no_dot."') and MONTH(periode_selesai) = '".$bulan."' and YEAR(periode_selesai) = '".$tahun."'","result");
        
        if (!empty($data)) {
            $data->periode_mulai_text = formatTanggal($data->periode_mulai);
            $data->periode_selesai_text = formatTanggal($data->periode_selesai);
            $data->slip_gaji = (!empty($data->total_diterima_gaji)) ? base_url("apiapp/export_slip_gaji_tunjangan?tipe=gaji&id=").$data->id_slip : null;
            $data->slip_tunjangan = (!empty($data->total_diterima_tunjangan)) ? base_url("apiapp/export_slip_gaji_tunjangan?tipe=tunjangan&id=").$data->id_slip : null;

            if(!empty($get_slip_custom)){
                foreach ($get_slip_custom as $key => $value) {
                    $data->slip_thr = base_url("apiapp/export_slip_gaji_tunjangan?tipe=thr&id=").$value->id_slip;
                    // $data->slip_tunjangan = base_url("apiapp/export_slip_gaji_tunjangan?tipe=tunjangan&id=").$value->id_slip;
                }

            }
            $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
            $status="200";
        }
        else if(!empty($get_slip_custom)){
            $data = new stdClass();
            $data->periode_mulai_text = formatTanggal($get_slip_custom[0]->periode_mulai);
            $data->periode_selesai_text = formatTanggal($get_slip_custom[0]->periode_selesai);
            $data->slip_thr = base_url("apiapp/export_slip_gaji_tunjangan?tipe=thr&id=").$get_slip_custom[0]->id_slip;
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
